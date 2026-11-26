<?php

namespace App\Http\Controllers;

use App\Models\AgendaRapat;
use App\Models\FinanceNotification;
use App\Models\Pengumuman;
use App\Models\PengumumanRead;
use App\Models\PengumumanRsvp;
use App\Models\User;
use App\Models\Warga;
use App\Services\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PengumumanController extends Controller
{
    /**
     * Dashboard & Listing Pusat Pengumuman
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdminRw = $user && $user->isAdminRw();
        $isAdminRt = $user && $user->isAdminRt();
        $isWarga = $user && $user->isWarga();

        $tab = $request->get('tab', 'aktif');
        $validTabs = ['aktif', 'penting', 'draft', 'terjadwal', 'arsip'];
        if (!in_array($tab, $validTabs)) {
            $tab = 'aktif';
        }

        // Warga is restricted to 'aktif' and 'penting'
        if ($isWarga && !in_array($tab, ['aktif', 'penting'])) {
            $tab = 'aktif';
        }

        $query = Pengumuman::with(['creator', 'reads', 'rsvps']);

        // Apply Tab Filters
        switch ($tab) {
            case 'penting':
                $query->active()->daruratPenting();
                break;

            case 'draft':
                $query->where('status', 'draft')->where('is_archived', false);
                break;

            case 'terjadwal':
                $query->where('status', 'published')
                      ->where('published_at', '>', now())
                      ->where('is_archived', false);
                break;

            case 'arsip':
                $query->where(function ($q) {
                    $q->where('is_archived', true)
                      ->orWhere(function ($sq) {
                          $sq->whereNotNull('tanggal_berakhir')
                             ->where('tanggal_berakhir', '<', now()->toDateString());
                      });
                });
                break;

            case 'aktif':
            default:
                $query->active();
                break;
        }

        // Scoping by User Role
        $query->forUser($user);

        // Kategori Filter
        if ($request->filled('kategori') && $request->kategori !== 'semua') {
            $kat = $request->kategori;
            if ($kat === 'darurat') {
                $query->whereIn('kategori', ['darurat', 'informasi_darurat']);
            } else {
                $query->where('kategori', $kat);
            }
        }

        // Prioritas Filter
        if ($request->filled('prioritas') && $request->prioritas !== 'semua') {
            $query->where('prioritas', $request->prioritas);
        }

        // Target RT Filter
        if ($request->filled('target_rt') && $request->target_rt !== 'semua') {
            $query->where('target_rt', $request->target_rt);
        }

        // Search Filter
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('content', 'like', "%{$s}%")
                  ->orWhere('kategori', 'like', "%{$s}%")
                  ->orWhereHas('creator', function ($cq) use ($s) {
                      $cq->where('name', 'like', "%{$s}%");
                  });
            });
        }

        // Ordering: Pinned first on active/penting, then newest
        $pengumumans = $query->orderByDesc('is_pinned')
                             ->orderByDesc('published_at')
                             ->orderByDesc('created_at')
                             ->paginate(9)
                             ->withQueryString();

        // Featured Pinned / Emergency Announcements (For Section C)
        $pinnedHighlights = collect();
        if (in_array($tab, ['aktif', 'penting'])) {
            $pinnedHighlights = Pengumuman::with('creator')
                ->active()
                ->forUser($user)
                ->where(function ($q) {
                    $q->where('is_pinned', true)
                      ->orWhere('prioritas', 'darurat');
                })
                ->orderByDesc('prioritas')
                ->orderByDesc('created_at')
                ->take(3)
                ->get();
        }

        // ==========================================
        // DYNAMIC STATS (SCOPED BY USER ROLE)
        // ==========================================
        $baseStatsQuery = Pengumuman::forUser($user);

        $totalAktif = (clone $baseStatsQuery)->active()->count();
        $totalPenting = (clone $baseStatsQuery)->active()->daruratPenting()->count();
        $totalDipublikasikan = (clone $baseStatsQuery)->where('status', 'published')->count();
        $totalDraft = $isWarga ? 0 : (clone $baseStatsQuery)->where('status', 'draft')->where('is_archived', false)->count();
        $totalTerjadwal = $isWarga ? 0 : (clone $baseStatsQuery)->where('status', 'published')->where('published_at', '>', now())->where('is_archived', false)->count();
        $totalArsip = $isWarga ? 0 : (clone $baseStatsQuery)->where('is_archived', true)->count();

        // Unread Announcements for current user
        $unreadPengumumanCount = 0;
        if ($user) {
            $readIds = PengumumanRead::where('user_id', $user->id)->pluck('pengumuman_id')->toArray();
            $unreadPengumumanCount = (clone $baseStatsQuery)->active()->whereNotIn('id', $readIds)->count();
        }

        // Unread in-app notification count
        $unreadNotificationCount = FinanceNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->where(function ($q) {
                $q->where('tipe', 'like', '%pengumuman%')
                  ->orWhere('judul', 'like', '%Pengumuman%');
            })
            ->count();

        $rtList = ['006', '007', '008', '009', '010'];

        return view('pengumuman.index', compact(
            'pengumumans',
            'tab',
            'pinnedHighlights',
            'totalAktif',
            'totalPenting',
            'totalDipublikasikan',
            'totalDraft',
            'totalTerjadwal',
            'totalArsip',
            'unreadPengumumanCount',
            'unreadNotificationCount',
            'isAdminRw',
            'isAdminRt',
            'isWarga',
            'rtList'
        ));
    }

    /**
     * Show form to create new announcement (Admin RW & Admin RT only)
     */
    public function create()
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            return redirect()->route('pengumuman.index')
                ->with('error', 'Hanya Pengurus RW atau RT yang memiliki wewenang untuk menerbitkan pengumuman resmi.');
        }

        $rtList = ['006', '007', '008', '009', '010'];
        return view('pengumuman.create', compact('rtList'));
    }

    /**
     * Store newly created announcement
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            return redirect()->route('pengumuman.index')->with('error', 'Akses ditolak.');
        }

        $validated = $request->validate([
            'title'                  => 'required|string|max:255',
            'content'                => 'required|string',
            'kategori'               => 'required|string|in:umum,kegiatan,keuangan,keamanan,kesehatan,sosial,darurat,informasi_darurat',
            'prioritas'              => 'required|string|in:darurat,penting,informasi,kegiatan',
            'target_rt'              => 'required|string',
            'lampiran_file'          => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
            'is_pinned'              => 'nullable|boolean',
            'requires_rsvp'          => 'nullable|boolean',
            'rsvp_type'              => 'nullable|string|in:hadir_tidak,partisipasi,konfirmasi_baca',
            'action_type'            => 'required|string|in:publish_now,schedule,draft',
            'published_at'           => 'nullable|date',
            'tanggal_berakhir'       => 'nullable|date|after_or_equal:today',
            'is_kegiatan'            => 'nullable|boolean',
            'kegiatan_tanggal'       => 'nullable|date',
            'kegiatan_waktu_mulai'   => 'nullable|string|max:10',
            'kegiatan_waktu_selesai' => 'nullable|string|max:10',
            'kegiatan_lokasi'        => 'nullable|string|max:255',
        ]);

        // Security enforcement on target_rt:
        // Admin RT is strictly locked to their own RT number
        $targetRt = $validated['target_rt'];
        if ($user->isAdminRt()) {
            $targetRt = $user->rt_number;
        }

        // Handle attachment upload
        $filePath = null;
        if ($request->hasFile('lampiran_file')) {
            $filePath = $request->file('lampiran_file')->store('pengumuman_files', 'public');
        }

        // Determine status and publication time
        $status = 'published';
        $publishedAt = now();

        if ($validated['action_type'] === 'draft') {
            $status = 'draft';
            $publishedAt = null;
        } elseif ($validated['action_type'] === 'schedule') {
            $status = 'published';
            $publishedAt = !empty($validated['published_at']) ? Carbon::parse($validated['published_at']) : now()->addDay();
        }

        // Handle Calendar integration (AgendaRapat)
        $agendaId = null;
        $isKegiatan = $request->has('is_kegiatan') && !empty($validated['kegiatan_tanggal']);
        if ($isKegiatan) {
            $startDateTime = Carbon::parse($validated['kegiatan_tanggal'] . ' ' . ($validated['kegiatan_waktu_mulai'] ?: '08:00:00'));
            $endDateTime = !empty($validated['kegiatan_waktu_selesai'])
                ? Carbon::parse($validated['kegiatan_tanggal'] . ' ' . $validated['kegiatan_waktu_selesai'])
                : null;

            $agenda = AgendaRapat::create([
                'judul'             => $validated['title'],
                'deskripsi'         => Str::limit(strip_tags($validated['content']), 300),
                'tanggal_waktu'     => $startDateTime,
                'waktu_selesai'     => $endDateTime,
                'lokasi'            => $validated['kegiatan_lokasi'] ?: 'Lingkungan RW 018',
                'rt_number'         => $targetRt === 'all' ? null : $targetRt,
                'jenis_rapat'       => 'sosialisasi_warga',
                'penyelenggara'     => $user->name . ' (' . ($user->isAdminRw() ? 'Pengurus RW 018' : 'Pengurus RT ' . $user->rt_number) . ')',
                'target_peserta'    => $targetRt === 'all' ? 'Seluruh Warga RW 018' : 'Warga RT ' . $targetRt,
                'status'            => 'terjadwal',
                'created_by'        => $user->id,
            ]);

            $agendaId = $agenda->id;
        }

        $pengumuman = Pengumuman::create([
            'title'                  => $validated['title'],
            'content'                => $validated['content'],
            'kategori'               => $validated['kategori'],
            'prioritas'              => $validated['prioritas'],
            'target_rt'              => $targetRt,
            'is_pinned'              => $request->has('is_pinned'),
            'requires_rsvp'          => $request->has('requires_rsvp'),
            'rsvp_type'              => $validated['rsvp_type'] ?? 'konfirmasi_baca',
            'lampiran_file'          => $filePath,
            'status'                 => $status,
            'published_at'           => $publishedAt,
            'tanggal_berakhir'       => $validated['tanggal_berakhir'] ?? null,
            'created_by'             => $user->id,
            'is_kegiatan'            => $isKegiatan,
            'kegiatan_tanggal'       => $validated['kegiatan_tanggal'] ?? null,
            'kegiatan_waktu_mulai'   => $validated['kegiatan_waktu_mulai'] ?? null,
            'kegiatan_waktu_selesai' => $validated['kegiatan_waktu_selesai'] ?? null,
            'kegiatan_lokasi'        => $validated['kegiatan_lokasi'] ?? null,
            'agenda_rapat_id'        => $agendaId,
        ]);

        // Auto mark read for the creator
        PengumumanRead::firstOrCreate([
            'pengumuman_id' => $pengumuman->id,
            'user_id'       => $user->id,
        ], [
            'read_at'       => now(),
        ]);

        // Trigger Notifications if published immediately
        if ($status === 'published' && $publishedAt <= now()) {
            $this->dispatchPengumumanNotifications($pengumuman, $user);
        }

        AuditLogger::log(
            'create',
            'Pusat Pengumuman',
            $pengumuman->id,
            "Pengumuman '{$pengumuman->title}' [{$pengumuman->prioritas_label}] diterbitkan oleh {$user->name} untuk target: {$pengumuman->target_teks}."
        );

        $msg = match($validated['action_type']) {
            'draft'    => 'Draft pengumuman berhasil disimpan.',
            'schedule' => "Pengumuman berhasil dijadwalkan untuk dipublikasikan pada " . $publishedAt->translatedFormat('d F Y, H:i') . ' WIB.',
            default    => 'Pengumuman resmi berhasil dipublikasikan kepada seluruh target penerima!',
        };

        return redirect()->route('pengumuman.index')->with('success', $msg);
    }

    /**
     * Show detailed announcement (automatically marks as read)
     */
    public function show($id)
    {
        $user = Auth::user();
        $pengumuman = Pengumuman::with(['creator', 'reads.user', 'rsvps.user.warga', 'agendaRapat'])->findOrFail($id);

        $isAdminRw = $user && $user->isAdminRw();
        $isAdminRt = $user && $user->isAdminRt();
        $isWarga = $user && $user->isWarga();

        // RBAC Access Control
        if ($isWarga) {
            // Cannot view draft or other RT's private announcements
            if ($pengumuman->status === 'draft' || $pengumuman->is_archived) {
                abort(403, 'Pengumuman ini sedang tidak aktif atau diarsipkan.');
            }
            if ($pengumuman->target_rt !== 'all' && $pengumuman->target_rt !== $user->rt_number) {
                abort(403, 'Anda tidak memiliki hak akses untuk melihat pengumuman wilayah RT lain.');
            }
        } elseif ($isAdminRt) {
            if ($pengumuman->target_rt !== 'all' && $pengumuman->target_rt !== $user->rt_number && $pengumuman->created_by !== $user->id) {
                abort(403, 'Akses terbatas untuk pengumuman wilayah RT Anda.');
            }
        }

        // Automatic Read Tracking: mark as read if user belongs to target audience
        $hasRead = false;
        if ($user) {
            $readRecord = PengumumanRead::firstOrCreate([
                'pengumuman_id' => $pengumuman->id,
                'user_id'       => $user->id,
            ], [
                'read_at'       => now(),
            ]);
            $hasRead = true;
        }

        // Officer Read Tracking & RSVP Stats
        $statsKeterbacaan = null;
        $statsRsvp = null;
        if ($isAdminRw || $isAdminRt || ($user && $user->id === $pengumuman->created_by)) {
            $statsKeterbacaan = $pengumuman->stats_keterbacaan;
            $statsRsvp = $pengumuman->stats_rsvp;
        }

        // Current User's RSVP response
        $myRsvp = null;
        if ($user) {
            $myRsvp = PengumumanRsvp::where('pengumuman_id', $pengumuman->id)
                ->where('user_id', $user->id)
                ->first();
        }

        return view('pengumuman.show', compact(
            'pengumuman',
            'statsKeterbacaan',
            'statsRsvp',
            'myRsvp',
            'isAdminRw',
            'isAdminRt',
            'isWarga'
        ));
    }

    /**
     * Show edit form for announcement
     */
    public function edit($id)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            return redirect()->route('pengumuman.index')->with('error', 'Akses ditolak.');
        }

        $pengumuman = Pengumuman::findOrFail($id);

        // Authorization check: Admin RT can only edit their own announcements
        if ($user->isAdminRt() && $pengumuman->created_by !== $user->id && $pengumuman->target_rt !== $user->rt_number) {
            return redirect()->route('pengumuman.index')->with('error', 'Anda tidak memiliki hak untuk mengedit pengumuman ini.');
        }

        $rtList = ['006', '007', '008', '009', '010'];
        return view('pengumuman.edit', compact('pengumuman', 'rtList'));
    }

    /**
     * Update existing announcement
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            return redirect()->route('pengumuman.index')->with('error', 'Akses ditolak.');
        }

        $pengumuman = Pengumuman::findOrFail($id);

        if ($user->isAdminRt() && $pengumuman->created_by !== $user->id && $pengumuman->target_rt !== $user->rt_number) {
            return redirect()->route('pengumuman.index')->with('error', 'Akses ditolak.');
        }

        $validated = $request->validate([
            'title'                  => 'required|string|max:255',
            'content'                => 'required|string',
            'kategori'               => 'required|string|in:umum,kegiatan,keuangan,keamanan,kesehatan,sosial,darurat,informasi_darurat',
            'prioritas'              => 'required|string|in:darurat,penting,informasi,kegiatan',
            'target_rt'              => 'required|string',
            'lampiran_file'          => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
            'is_pinned'              => 'nullable|boolean',
            'requires_rsvp'          => 'nullable|boolean',
            'rsvp_type'              => 'nullable|string|in:hadir_tidak,partisipasi,konfirmasi_baca',
            'action_type'            => 'required|string|in:publish_now,schedule,draft',
            'published_at'           => 'nullable|date',
            'tanggal_berakhir'       => 'nullable|date',
            'is_kegiatan'            => 'nullable|boolean',
            'kegiatan_tanggal'       => 'nullable|date',
            'kegiatan_waktu_mulai'   => 'nullable|string|max:10',
            'kegiatan_waktu_selesai' => 'nullable|string|max:10',
            'kegiatan_lokasi'        => 'nullable|string|max:255',
        ]);

        $targetRt = $validated['target_rt'];
        if ($user->isAdminRt()) {
            $targetRt = $user->rt_number;
        }

        $filePath = $pengumuman->lampiran_file;
        if ($request->hasFile('lampiran_file')) {
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            $filePath = $request->file('lampiran_file')->store('pengumuman_files', 'public');
        }

        $wasDraft = ($pengumuman->status === 'draft');
        $status = 'published';
        $publishedAt = $pengumuman->published_at ?: now();

        if ($validated['action_type'] === 'draft') {
            $status = 'draft';
        } elseif ($validated['action_type'] === 'schedule') {
            $status = 'published';
            $publishedAt = !empty($validated['published_at']) ? Carbon::parse($validated['published_at']) : now()->addDay();
        } elseif ($validated['action_type'] === 'publish_now') {
            $status = 'published';
            $publishedAt = now();
        }

        // Sync or Create AgendaRapat
        $isKegiatan = $request->has('is_kegiatan') && !empty($validated['kegiatan_tanggal']);
        $agendaId = $pengumuman->agenda_rapat_id;

        if ($isKegiatan) {
            $startDateTime = Carbon::parse($validated['kegiatan_tanggal'] . ' ' . ($validated['kegiatan_waktu_mulai'] ?: '08:00:00'));
            $endDateTime = !empty($validated['kegiatan_waktu_selesai'])
                ? Carbon::parse($validated['kegiatan_tanggal'] . ' ' . $validated['kegiatan_waktu_selesai'])
                : null;

            if ($agendaId && ($existingAgenda = AgendaRapat::find($agendaId))) {
                $existingAgenda->update([
                    'judul'          => $validated['title'],
                    'deskripsi'      => Str::limit(strip_tags($validated['content']), 300),
                    'tanggal_waktu'  => $startDateTime,
                    'waktu_selesai'  => $endDateTime,
                    'lokasi'         => $validated['kegiatan_lokasi'] ?: 'Lingkungan RW 018',
                    'rt_number'      => $targetRt === 'all' ? null : $targetRt,
                    'target_peserta' => $targetRt === 'all' ? 'Seluruh Warga RW 018' : 'Warga RT ' . $targetRt,
                ]);
            } else {
                $newAgenda = AgendaRapat::create([
                    'judul'          => $validated['title'],
                    'deskripsi'      => Str::limit(strip_tags($validated['content']), 300),
                    'tanggal_waktu'  => $startDateTime,
                    'waktu_selesai'  => $endDateTime,
                    'lokasi'         => $validated['kegiatan_lokasi'] ?: 'Lingkungan RW 018',
                    'rt_number'      => $targetRt === 'all' ? null : $targetRt,
                    'jenis_rapat'    => 'sosialisasi_warga',
                    'penyelenggara'  => $user->name,
                    'target_peserta' => $targetRt === 'all' ? 'Seluruh Warga RW 018' : 'Warga RT ' . $targetRt,
                    'status'         => 'terjadwal',
                    'created_by'     => $user->id,
                ]);
                $agendaId = $newAgenda->id;
            }
        } elseif ($agendaId) {
            // Remove linked agenda if un-checked
            AgendaRapat::where('id', $agendaId)->delete();
            $agendaId = null;
        }

        $pengumuman->update([
            'title'                  => $validated['title'],
            'content'                => $validated['content'],
            'kategori'               => $validated['kategori'],
            'prioritas'              => $validated['prioritas'],
            'target_rt'              => $targetRt,
            'is_pinned'              => $request->has('is_pinned'),
            'requires_rsvp'          => $request->has('requires_rsvp'),
            'rsvp_type'              => $validated['rsvp_type'] ?? 'konfirmasi_baca',
            'lampiran_file'          => $filePath,
            'status'                 => $status,
            'published_at'           => $publishedAt,
            'tanggal_berakhir'       => $validated['tanggal_berakhir'] ?? null,
            'is_kegiatan'            => $isKegiatan,
            'kegiatan_tanggal'       => $validated['kegiatan_tanggal'] ?? null,
            'kegiatan_waktu_mulai'   => $validated['kegiatan_waktu_mulai'] ?? null,
            'kegiatan_waktu_selesai' => $validated['kegiatan_waktu_selesai'] ?? null,
            'kegiatan_lokasi'        => $validated['kegiatan_lokasi'] ?? null,
            'agenda_rapat_id'        => $agendaId,
        ]);

        // If changed from draft to published, send notifications
        if ($wasDraft && $status === 'published' && $publishedAt <= now()) {
            $this->dispatchPengumumanNotifications($pengumuman, $user);
        }

        AuditLogger::log(
            'update',
            'Pusat Pengumuman',
            $pengumuman->id,
            "Pengumuman '{$pengumuman->title}' diperbarui oleh {$user->name}."
        );

        return redirect()->route('pengumuman.show', $pengumuman->id)
            ->with('success', 'Perubahan pengumuman berhasil disimpan!');
    }

    /**
     * Delete announcement
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            return redirect()->route('pengumuman.index')->with('error', 'Akses ditolak.');
        }

        $pengumuman = Pengumuman::findOrFail($id);

        if ($user->isAdminRt() && $pengumuman->created_by !== $user->id && $pengumuman->target_rt !== $user->rt_number) {
            return redirect()->route('pengumuman.index')->with('error', 'Akses ditolak.');
        }

        $title = $pengumuman->title;

        // Delete linked agenda if any
        if ($pengumuman->agenda_rapat_id) {
            AgendaRapat::where('id', $pengumuman->agenda_rapat_id)->delete();
        }

        // Delete attachment
        if ($pengumuman->lampiran_file && Storage::disk('public')->exists($pengumuman->lampiran_file)) {
            Storage::disk('public')->delete($pengumuman->lampiran_file);
        }

        $pengumuman->delete();

        AuditLogger::log(
            'delete',
            'Pusat Pengumuman',
            $id,
            "Pengumuman '{$title}' dihapus dari sistem oleh {$user->name}."
        );

        return redirect()->route('pengumuman.index')->with('success', "Pengumuman '{$title}' berhasil dihapus.");
    }

    /**
     * Archive or Unarchive announcement
     */
    public function archive($id)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            return redirect()->route('pengumuman.index')->with('error', 'Akses ditolak.');
        }

        $pengumuman = Pengumuman::findOrFail($id);
        $newArchiveState = !$pengumuman->is_archived;

        $pengumuman->update([
            'is_archived' => $newArchiveState,
            'archived_at' => $newArchiveState ? now() : null,
        ]);

        $actionWord = $newArchiveState ? 'diarsipkan' : 'dipulihkan dari arsip';

        AuditLogger::log(
            'archive',
            'Pusat Pengumuman',
            $pengumuman->id,
            "Pengumuman '{$pengumuman->title}' {$actionWord} oleh {$user->name}."
        );

        return redirect()->back()->with('success', "Pengumuman berhasil {$actionWord}.");
    }

    /**
     * Publish draft/scheduled announcement immediately
     */
    public function publishNow($id)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            return redirect()->route('pengumuman.index')->with('error', 'Akses ditolak.');
        }

        $pengumuman = Pengumuman::findOrFail($id);

        $pengumuman->update([
            'status'       => 'published',
            'published_at' => now(),
            'is_archived'  => false,
        ]);

        $this->dispatchPengumumanNotifications($pengumuman, $user);

        AuditLogger::log(
            'publish',
            'Pusat Pengumuman',
            $pengumuman->id,
            "Pengumuman '{$pengumuman->title}' dipublikasikan sekarang oleh {$user->name}."
        );

        return redirect()->back()->with('success', 'Pengumuman telah resmi dipublikasikan seketika.');
    }

    /**
     * Citizen RSVP / Confirmation response
     */
    public function rsvp(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $pengumuman = Pengumuman::findOrFail($id);

        if (!$pengumuman->requires_rsvp) {
            return redirect()->back()->with('error', 'Pengumuman ini tidak membutuhkan konfirmasi kehadiran.');
        }

        $validated = $request->validate([
            'response' => 'required|string|in:hadir,tidak_hadir,ikut,tidak_ikut,konfirmasi',
            'catatan'  => 'nullable|string|max:500',
        ]);

        $warga = Warga::where('user_id', $user->id)->first();

        $rsvp = PengumumanRsvp::updateOrCreate(
            [
                'pengumuman_id' => $pengumuman->id,
                'user_id'       => $user->id,
            ],
            [
                'warga_id'     => $warga ? $warga->id : null,
                'response'     => $validated['response'],
                'catatan'      => $validated['catatan'] ?? null,
                'responded_at' => now(),
            ]
        );

        return redirect()->back()->with('success', "Konfirmasi respons Anda ('{$rsvp->response_label}') berhasil tercatat.");
    }

    /**
     * Mark single announcement as read explicitly
     */
    public function markRead($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false], 401);
        }

        PengumumanRead::firstOrCreate([
            'pengumuman_id' => $id,
            'user_id'       => $user->id,
        ], [
            'read_at'       => now(),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Mark all relevant announcements as read for current user
     */
    public function markAllRead()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $relevantPengumumans = Pengumuman::active()->forUser($user)->get();
        foreach ($relevantPengumumans as $p) {
            PengumumanRead::firstOrCreate([
                'pengumuman_id' => $p->id,
                'user_id'       => $user->id,
            ], [
                'read_at'       => now(),
            ]);
        }

        // Also mark corresponding in-app notifications as read
        FinanceNotification::where('user_id', $user->id)
            ->where(function ($q) {
                $q->where('tipe', 'like', '%pengumuman%')
                  ->orWhere('judul', 'like', '%Pengumuman%');
            })
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return redirect()->back()->with('success', 'Seluruh pengumuman aktif telah ditandai sudah dibaca.');
    }

    /**
     * Helper: Dispatch In-App Notifications to Target Recipients
     */
    protected function dispatchPengumumanNotifications(Pengumuman $pengumuman, User $sender): void
    {
        $recipientQuery = User::where('id', '!=', $sender->id);

        if ($pengumuman->target_rt && $pengumuman->target_rt !== 'all') {
            $recipientQuery->where('rt_number', $pengumuman->target_rt);
        }

        $recipients = $recipientQuery->get();

        $prefix = match($pengumuman->prioritas) {
            'darurat' => '🚨 [DARURAT]',
            'penting' => '⚠️ [PENTING]',
            default   => '📢 [PENGUMUMAN]',
        };

        $judulNotif = "{$prefix} {$pengumuman->title}";
        $pesanNotif = Str::limit(strip_tags($pengumuman->content), 140);

        foreach ($recipients as $recipient) {
            FinanceNotification::create([
                'user_id'  => $recipient->id,
                'warga_id' => null,
                'tipe'     => 'pengumuman_' . $pengumuman->prioritas,
                'judul'    => $judulNotif,
                'pesan'    => $pesanNotif,
                'nominal'  => null,
                'periode'  => null,
                'due_date' => $pengumuman->tanggal_berakhir,
                'link_url' => route('pengumuman.show', $pengumuman->id),
                'is_read'  => false,
                'data'     => [
                    'pengumuman_id' => $pengumuman->id,
                    'kategori'      => $pengumuman->kategori,
                    'prioritas'     => $pengumuman->prioritas,
                    'sender_name'   => $sender->name,
                ],
            ]);
        }
    }
}
