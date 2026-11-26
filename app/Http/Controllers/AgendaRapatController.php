<?php

namespace App\Http\Controllers;

use App\Models\AgendaKehadiran;
use App\Models\AgendaNotulenHistory;
use App\Models\AgendaRapat;
use App\Models\FinanceNotification;
use App\Models\User;
use App\Services\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AgendaRapatController extends Controller
{
    /**
     * Display a listing of meeting agendas.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isRw = $user->isRw();
        $isAdminRT = $user->isAdminRT();
        $isWarga = $user->isWarga();

        $query = AgendaRapat::with(['creator', 'kehadirans'])->orderBy('tanggal_waktu', 'desc');

        // RBAC filtering
        if ($isWarga) {
            // Warga only sees RW-level meetings (or open to warga) AND meetings of their own RT (rt_number == $user->rt_number or null). Meetings of OTHER RTs must NOT appear!
            $query->where(function ($q) use ($user) {
                $q->where(function ($sub) {
                    $sub->where('jenis_rapat', 'rw')
                        ->orWhere('jenis_rapat', 'musyawarah_warga')
                        ->orWhere('target_peserta', 'like', '%warga%')
                        ->orWhere('target_peserta', 'like', '%terbuka%');
                })->where(function ($rtSub) use ($user) {
                    $rtSub->whereNull('rt_number')
                          ->orWhere('rt_number', '')
                          ->orWhere('rt_number', $user->rt_number);
                });
            });
        } elseif ($isAdminRT) {
            // Admin RT sees RW meetings involving RTs, plus their own RT meetings
            $query->where(function ($q) use ($user) {
                $q->where('jenis_rapat', 'rw')
                  ->orWhere('target_peserta', 'like', '%RT%')
                  ->orWhere('target_peserta', 'like', '%warga%')
                  ->orWhere('target_peserta', 'like', '%terbuka%')
                  ->orWhere('rt_number', $user->rt_number)
                  ->orWhere('created_by', $user->id);
            });
        }
        // Admin RW sees all records across all RTs

        // 1. Filter Search
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('judul', 'like', "%{$s}%")
                  ->orWhere('lokasi', 'like', "%{$s}%")
                  ->orWhere('penyelenggara', 'like', "%{$s}%")
                  ->orWhere('agenda_pembahasan', 'like', "%{$s}%");
            });
        }

        // 2. Filter Jenis Rapat
        if ($request->filled('jenis_rapat')) {
            $query->where('jenis_rapat', $request->jenis_rapat);
        }

        // 3. Filter Wilayah RT
        if ($request->filled('rt_number')) {
            if ($request->rt_number === 'rw') {
                $query->whereNull('rt_number');
            } else {
                $query->where('rt_number', $request->rt_number);
            }
        }

        // 4. Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 5. Filter Periode
        if ($request->filled('periode')) {
            $now = Carbon::now();
            if ($request->periode === 'bulan_ini') {
                $query->whereYear('tanggal_waktu', $now->year)->whereMonth('tanggal_waktu', $now->month);
            } elseif ($request->periode === 'bulan_depan') {
                $next = $now->copy()->addMonth();
                $query->whereYear('tanggal_waktu', $next->year)->whereMonth('tanggal_waktu', $next->month);
            } elseif ($request->periode === 'riwayat') {
                $query->where('tanggal_waktu', '<', $now);
            }
        }

        $agendas = $query->paginate(9)->withQueryString();

        // Database-driven stat cards (computed based on user visibility scope)
        $statQuery = AgendaRapat::query();
        if ($isWarga) {
            $statQuery->where(function ($q) {
                $q->where('jenis_rapat', 'musyawarah_warga')
                  ->orWhere('target_peserta', 'like', '%warga%')
                  ->orWhere('target_peserta', 'like', '%terbuka%');
            });
        } elseif ($isAdminRT) {
            $statQuery->where(function ($q) use ($user) {
                $q->where('jenis_rapat', 'rw')
                  ->orWhere('target_peserta', 'like', '%RT%')
                  ->orWhere('rt_number', $user->rt_number)
                  ->orWhere('created_by', $user->id);
            });
        }

        $totalMendatang = (clone $statQuery)->where('status', 'akan_datang')->count();
        $totalSelesai = (clone $statQuery)->where('status', 'selesai')->count();
        $totalAgenda = (clone $statQuery)->count();

        $rtList = ['006', '007', '008', '009', '010'];

        return view('agenda.index', compact(
            'agendas',
            'totalMendatang',
            'totalSelesai',
            'totalAgenda',
            'rtList'
        ));
    }

    /**
     * Store a newly created agenda rapat.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            return redirect()->route('agenda.index')->with('error', 'Akses ditolak: Warga tidak memiliki izin membuat agenda.');
        }

        $allowedJenis = $user->isAdminRT() ? 'koordinasi_rt,musyawarah_warga' : 'rw,koordinasi_rt,musyawarah_warga';

        $request->validate([
            'judul' => 'required|string|max:255',
            'jenis_rapat' => 'required|in:' . $allowedJenis,
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required',
            'lokasi' => 'required|string|max:255',
            'penyelenggara' => 'required|string|max:255',
            'target_peserta' => 'required|string|max:255',
            'rt_number' => 'nullable|string|max:10',
            'agenda_pembahasan' => 'required|string',
            'deskripsi' => 'nullable|string',
            'lampiran_dokumen' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:5120',
        ]);

        // Combine date and time
        $startDateTime = Carbon::parse($request->tanggal . ' ' . $request->waktu_mulai);
        $waktuSelesai = Carbon::parse($request->waktu_selesai)->format('H:i:s');
        $endDateTime = Carbon::parse($request->tanggal . ' ' . $request->waktu_selesai);

        // Validation: end time must be after start time
        if ($endDateTime->lessThanOrEqualTo($startDateTime)) {
            return redirect()->back()->withInput()->with('error', 'Waktu selesai harus lebih akhir dari waktu mulai pertemuan.');
        }

        // Check for potential collision / overlapping meetings in the same location
        $collisionQuery = AgendaRapat::whereDate('tanggal_waktu', $startDateTime->toDateString())
            ->where('status', '!=', 'dibatalkan')
            ->where(function ($q) use ($request) {
                $q->where('lokasi', 'like', '%' . trim($request->lokasi) . '%')
                  ->orWhere('lokasi', $request->lokasi);
            });

        $collision = $collisionQuery->first();
        $warningMessage = null;
        if ($collision) {
            $warningMessage = "Catatan: Terdapat agenda lain di lokasi yang mirip pada tanggal yang sama: '{$collision->judul}'. Harap pastikan ketersediaan ruangan.";
        }

        // File upload
        $lampiranPath = null;
        if ($request->hasFile('lampiran_dokumen')) {
            $lampiranPath = $request->file('lampiran_dokumen')->store('dokumen_agenda', 'public');
        }

        $agenda = AgendaRapat::create([
            'judul' => $request->judul,
            'jenis_rapat' => $request->jenis_rapat,
            'deskripsi' => $request->deskripsi,
            'tanggal_waktu' => $startDateTime,
            'waktu_selesai' => $waktuSelesai,
            'lokasi' => $request->lokasi,
            'penyelenggara' => $request->penyelenggara,
            'target_peserta' => $request->target_peserta,
            'rt_number' => $user->isAdminRT() ? $user->rt_number : ($request->rt_number ?: null),
            'agenda_pembahasan' => $request->agenda_pembahasan,
            'lampiran_dokumen' => $lampiranPath,
            'status' => 'akan_datang',
            'is_published_notulen' => false,
            'created_by' => $user->id,
        ]);

        // Auto-populate initial roster into agenda_kehadirans
        $this->generateDefaultAttendance($agenda);

        // Send notifications to relevant users
        $this->notifyParticipants($agenda, 'create');

        // Audit Log
        AuditLogger::log(
            'create',
            'Agenda & Rapat',
            $agenda->id,
            "Agenda rapat baru '{$request->judul}' ({$agenda->jenis_label}) dijadwalkan pada {$agenda->waktu_formatted} di {$request->lokasi} oleh {$user->name}.",
            null,
            $agenda->toArray(),
            $agenda->rt_number
        );

        $flashSuccess = 'Agenda rapat berhasil dipublikasikan langsung ke kalender kegiatan!';
        if ($warningMessage) {
            return redirect()->route('agenda.show', $agenda->id)->with('success', $flashSuccess)->with('warning', $warningMessage);
        }

        return redirect()->route('agenda.show', $agenda->id)->with('success', $flashSuccess);
    }

    /**
     * Display the specified agenda detail, attendance, and minutes.
     */
    public function show($id)
    {
        $user = Auth::user();
        $agenda = AgendaRapat::with(['creator', 'kehadirans.user', 'notulenHistories.user'])->findOrFail($id);

        // Warga access control: cannot see internal RT meetings of other RTs or non-public meetings
        if ($user->isWarga()) {
            $isCitizenMeeting = ($agenda->jenis_rapat === 'musyawarah_warga')
                || ($agenda->jenis_rapat === 'rw')
                || str_contains(strtolower($agenda->target_peserta ?? ''), 'warga')
                || str_contains(strtolower($agenda->target_peserta ?? ''), 'terbuka');

            $isOwnRtOrRw = empty($agenda->rt_number) || $agenda->rt_number === $user->rt_number;

            if (!$isCitizenMeeting || !$isOwnRtOrRw) {
                return redirect()->route('agenda.index')->with('error', 'Akses dibatasi: Pertemuan ini bukan untuk wilayah RT Anda atau merupakan koordinasi internal pengurus.');
            }
        }

        $kehadiranSummary = $agenda->kehadiran_summary;
        $allUsers = User::orderBy('name', 'asc')->get();

        return view('agenda.show', compact('agenda', 'kehadiranSummary', 'allUsers'));
    }

    /**
     * Update agenda information.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            return redirect()->route('agenda.index')->with('error', 'Akses ditolak.');
        }

        $agenda = AgendaRapat::findOrFail($id);

        // RBAC: RT can only edit their own agenda
        if ($user->isAdminRT() && $agenda->created_by !== $user->id && $agenda->rt_number !== $user->rt_number) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses mengubah agenda RT lain atau agenda RW.');
        }

        $allowedJenis = $user->isAdminRT() ? 'koordinasi_rt,musyawarah_warga' : 'rw,koordinasi_rt,musyawarah_warga';

        $request->validate([
            'judul' => 'required|string|max:255',
            'jenis_rapat' => 'required|in:' . $allowedJenis,
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required',
            'lokasi' => 'required|string|max:255',
            'penyelenggara' => 'required|string|max:255',
            'target_peserta' => 'required|string|max:255',
            'rt_number' => 'nullable|string|max:10',
            'agenda_pembahasan' => 'required|string',
            'deskripsi' => 'nullable|string',
            'lampiran_dokumen' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:5120',
        ]);

        $startDateTime = Carbon::parse($request->tanggal . ' ' . $request->waktu_mulai);
        $waktuSelesai = Carbon::parse($request->waktu_selesai)->format('H:i:s');
        $endDateTime = Carbon::parse($request->tanggal . ' ' . $request->waktu_selesai);

        if ($endDateTime->lessThanOrEqualTo($startDateTime)) {
            return redirect()->back()->withInput()->with('error', 'Waktu selesai harus lebih akhir dari waktu mulai.');
        }

        $oldValues = $agenda->toArray();

        // Check if date or location changed
        $dateChanged = $agenda->tanggal_waktu->format('Y-m-d H:i') !== $startDateTime->format('Y-m-d H:i');
        $locationChanged = $agenda->lokasi !== $request->lokasi;

        $agenda->judul = $request->judul;
        $agenda->jenis_rapat = $request->jenis_rapat;
        $agenda->tanggal_waktu = $startDateTime;
        $agenda->waktu_selesai = $waktuSelesai;
        $agenda->lokasi = $request->lokasi;
        $agenda->penyelenggara = $request->penyelenggara;
        $agenda->target_peserta = $request->target_peserta;
        $agenda->rt_number = $user->isAdminRT() ? $user->rt_number : ($request->rt_number ?: null);
        $agenda->agenda_pembahasan = $request->agenda_pembahasan;
        $agenda->deskripsi = $request->deskripsi;

        if ($request->hasFile('lampiran_dokumen')) {
            if ($agenda->lampiran_dokumen) {
                Storage::disk('public')->delete($agenda->lampiran_dokumen);
            }
            $agenda->lampiran_dokumen = $request->file('lampiran_dokumen')->store('dokumen_agenda', 'public');
        }

        $agenda->save();

        if ($dateChanged || $locationChanged) {
            $this->notifyParticipants($agenda, 'update');
        }

        AuditLogger::log(
            'update',
            'Agenda & Rapat',
            $agenda->id,
            "Agenda rapat '{$agenda->judul}' diperbarui oleh {$user->name}.",
            $oldValues,
            $agenda->toArray(),
            $agenda->rt_number
        );

        return redirect()->route('agenda.show', $agenda->id)->with('success', 'Perubahan informasi agenda rapat berhasil disimpan!');
    }

    /**
     * Update meeting status (akan_datang, berlangsung, selesai, dibatalkan).
     */
    public function updateStatus(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            return redirect()->route('agenda.index')->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'status' => 'required|in:akan_datang,berlangsung,selesai,dibatalkan',
            'keterangan_batal' => 'nullable|string|max:255',
        ]);

        $agenda = AgendaRapat::findOrFail($id);
        $oldStatus = $agenda->status;
        $agenda->status = $request->status;
        $agenda->save();

        if ($request->status === 'dibatalkan') {
            $this->notifyParticipants($agenda, 'cancel', $request->keterangan_batal);
        }

        AuditLogger::log(
            'status_change',
            'Agenda & Rapat',
            $agenda->id,
            "Status agenda '{$agenda->judul}' diubah dari " . strtoupper($oldStatus) . " menjadi " . strtoupper($request->status) . " oleh {$user->name}." . ($request->keterangan_batal ? " Alasan: {$request->keterangan_batal}" : ""),
            ['status' => $oldStatus],
            ['status' => $agenda->status],
            $agenda->rt_number
        );

        return redirect()->route('agenda.show', $agenda->id)->with('success', 'Status agenda berhasil diperbarui menjadi: ' . strtoupper(str_replace('_', ' ', $agenda->status)));
    }

    /**
     * Add attendee manually to the attendance roster.
     */
    public function storeKehadiran(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            return redirect()->route('agenda.index')->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'nama_peserta' => 'required|string|max:150',
            'jabatan' => 'nullable|string|max:100',
            'rt_number' => 'nullable|string|max:10',
            'status' => 'required|in:hadir,tidak_hadir,izin',
            'keterangan' => 'nullable|string|max:255',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $agenda = AgendaRapat::findOrFail($id);

        AgendaKehadiran::create([
            'agenda_id' => $agenda->id,
            'user_id' => $request->user_id ?: null,
            'nama_peserta' => $request->nama_peserta,
            'jabatan' => $request->jabatan,
            'rt_number' => $request->rt_number,
            'status' => $request->status,
            'keterangan' => $request->keterangan,
            'waktu_kehadiran' => $request->status === 'hadir' ? now() : null,
        ]);

        AuditLogger::log(
            'create',
            'Agenda & Rapat',
            $agenda->id,
            "Peserta '{$request->nama_peserta}' ditambahkan ke daftar hadir agenda '{$agenda->judul}' oleh {$user->name}.",
            null,
            $request->all(),
            $agenda->rt_number
        );

        return redirect()->route('agenda.show', $agenda->id . '#tab-kehadiran')->with('success', "Peserta '{$request->nama_peserta}' berhasil dicatat dalam daftar hadir.");
    }

    /**
     * Fast toggle attendance status (hadir, izin, tidak_hadir).
     */
    public function toggleKehadiran(Request $request, $id, $kehadiranId)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            return redirect()->route('agenda.index')->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'status' => 'required|in:hadir,tidak_hadir,izin',
        ]);

        $kehadiran = AgendaKehadiran::where('agenda_id', $id)->findOrFail($kehadiranId);
        $kehadiran->status = $request->status;
        $kehadiran->waktu_kehadiran = $request->status === 'hadir' ? now() : null;
        $kehadiran->save();

        return redirect()->route('agenda.show', $id . '#tab-kehadiran')->with('success', "Status kehadiran {$kehadiran->nama_peserta} diperbarui menjadi " . strtoupper($request->status));
    }

    /**
     * Remove attendee from attendance roster.
     */
    public function destroyKehadiran($id, $kehadiranId)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            return redirect()->route('agenda.index')->with('error', 'Akses ditolak.');
        }

        $kehadiran = AgendaKehadiran::where('agenda_id', $id)->findOrFail($kehadiranId);
        $nama = $kehadiran->nama_peserta;
        $kehadiran->delete();

        return redirect()->route('agenda.show', $id . '#tab-kehadiran')->with('success', "Peserta '{$nama}' telah dihapus dari daftar hadir.");
    }

    /**
     * Save draft or publish meeting minutes (notulensi & hasil rapat).
     */
    public function updateNotulen(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            return redirect()->route('agenda.index')->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'action_type' => 'required|in:draft,publish',
            'hasil_keputusan' => 'nullable|string',
            'notulen' => 'nullable|string',
            'tindak_lanjut' => 'nullable|string',
            'pic_tindak_lanjut' => 'nullable|string|max:150',
            'tenggat_tindak_lanjut' => 'nullable|date',
            'lampiran_notulen' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:5120',
            'status_rapat' => 'nullable|in:akan_datang,berlangsung,selesai,dibatalkan',
        ]);

        $agenda = AgendaRapat::findOrFail($id);

        $agenda->hasil_keputusan = $request->hasil_keputusan;
        $agenda->notulen = $request->notulen;
        $agenda->tindak_lanjut = $request->tindak_lanjut;
        $agenda->pic_tindak_lanjut = $request->pic_tindak_lanjut;
        $agenda->tenggat_tindak_lanjut = $request->tenggat_tindak_lanjut;

        if ($request->filled('status_rapat')) {
            $agenda->status = $request->status_rapat;
        }

        if ($request->hasFile('lampiran_notulen')) {
            if ($agenda->lampiran_notulen) {
                Storage::disk('public')->delete($agenda->lampiran_notulen);
            }
            $agenda->lampiran_notulen = $request->file('lampiran_notulen')->store('notulen_agenda', 'public');
        }

        $isPublish = $request->action_type === 'publish';
        if ($isPublish) {
            $agenda->is_published_notulen = true;
            $agenda->published_at = now();
            if ($agenda->status !== 'dibatalkan') {
                $agenda->status = 'selesai';
            }
        } else {
            // Draft mode
            $agenda->is_published_notulen = false;
        }

        $agenda->save();

        // Create change history log
        AgendaNotulenHistory::create([
            'agenda_id' => $agenda->id,
            'user_id' => $user->id,
            'action_type' => $request->action_type,
            'ringkasan' => $agenda->notulen ? substr(strip_tags($agenda->notulen), 0, 200) : null,
            'hasil_keputusan' => $agenda->hasil_keputusan,
            'catatan_perubahan' => $isPublish ? "Notulensi & Keputusan resmi dipublikasikan oleh {$user->name}" : "Draft notulensi disimpan oleh {$user->name}",
        ]);

        // If published, notify participants & citizens
        if ($isPublish) {
            $this->notifyParticipants($agenda, 'publish_notulen');
        }

        AuditLogger::log(
            $isPublish ? 'publish' : 'update',
            'Agenda & Rapat',
            $agenda->id,
            $isPublish
                ? "Notulensi & hasil keputusan rapat '{$agenda->judul}' dipublikasikan resmi oleh {$user->name}."
                : "Draft notulensi rapat '{$agenda->judul}' diperbarui oleh {$user->name}.",
            null,
            ['is_published_notulen' => $agenda->is_published_notulen, 'status' => $agenda->status],
            $agenda->rt_number
        );

        $msg = $isPublish
            ? 'Notulensi & hasil rapat berhasil dipublikasikan resmi kepada seluruh peserta!'
            : 'Draft notulensi rapat berhasil disimpan.';

        return redirect()->route('agenda.show', $agenda->id . '#tab-notulensi')->with('success', $msg);
    }

    /**
     * Delete an agenda.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            return redirect()->route('agenda.index')->with('error', 'Akses ditolak.');
        }

        $agenda = AgendaRapat::findOrFail($id);

        if ($user->isAdminRT() && $agenda->created_by !== $user->id && $agenda->rt_number !== $user->rt_number) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses menghapus agenda ini.');
        }

        $judul = $agenda->judul;
        $rtNumber = $agenda->rt_number;

        if ($agenda->lampiran_dokumen) {
            Storage::disk('public')->delete($agenda->lampiran_dokumen);
        }
        if ($agenda->lampiran_notulen) {
            Storage::disk('public')->delete($agenda->lampiran_notulen);
        }

        $agenda->delete();

        AuditLogger::log(
            'delete',
            'Agenda & Rapat',
            $id,
            "Agenda rapat '{$judul}' dihapus oleh {$user->name}.",
            ['judul' => $judul],
            null,
            $rtNumber
        );

        return redirect()->route('agenda.index')->with('success', "Agenda '{$judul}' berhasil dihapus dari sistem.");
    }

    /**
     * API endpoint to check collision in real time
     */
    public function checkCollision(Request $request)
    {
        $tanggal = $request->get('tanggal');
        $lokasi = trim($request->get('lokasi', ''));
        $agendaId = $request->get('agenda_id');

        if (!$tanggal || !$lokasi) {
            return response()->json(['collision' => false]);
        }

        $query = AgendaRapat::whereDate('tanggal_waktu', $tanggal)
            ->where('status', '!=', 'dibatalkan')
            ->where('lokasi', 'like', "%{$lokasi}%");

        if ($agendaId) {
            $query->where('id', '!=', $agendaId);
        }

        $collision = $query->first();

        if ($collision) {
            return response()->json([
                'collision' => true,
                'message' => "Perhatian: Terdapat agenda lain di lokasi yang sama ('{$collision->judul}', {$collision->waktu_formatted}). Harap pastikan ruangan tidak bentrok.",
            ]);
        }

        return response()->json(['collision' => false]);
    }

    /**
     * Helper to auto-populate default roster into agenda_kehadirans.
     */
    protected function generateDefaultAttendance(AgendaRapat $agenda): void
    {
        $adminRw = User::where('role', 'admin')->where('email', 'adminrw@rt.com')->first();
        $adminRTs = User::where('role', 'admin')->where('email', 'like', 'adminrt%')->get();
        $bendahara = User::where('role', 'bendahara')->first();

        // Always invite Ketua RW
        if ($adminRw) {
            AgendaKehadiran::create([
                'agenda_id' => $agenda->id,
                'user_id' => $adminRw->id,
                'nama_peserta' => $adminRw->name,
                'jabatan' => 'Ketua RW 018',
                'rt_number' => '000',
                'status' => 'hadir',
            ]);
        }

        // If target includes RTs or RW coordination, add all RT heads
        if ($agenda->jenis_rapat === 'rw' || str_contains(strtolower($agenda->target_peserta), 'rt')) {
            foreach ($adminRTs as $rt) {
                AgendaKehadiran::create([
                    'agenda_id' => $agenda->id,
                    'user_id' => $rt->id,
                    'nama_peserta' => $rt->name,
                    'jabatan' => 'Ketua RT ' . $rt->rt_number,
                    'rt_number' => $rt->rt_number,
                    'status' => 'hadir',
                ]);
            }
            if ($bendahara) {
                AgendaKehadiran::create([
                    'agenda_id' => $agenda->id,
                    'user_id' => $bendahara->id,
                    'nama_peserta' => $bendahara->name,
                    'jabatan' => 'Bendahara RW',
                    'rt_number' => '001',
                    'status' => 'hadir',
                ]);
            }
        } elseif ($agenda->rt_number) {
            // If specific RT meeting, add that RT's admin
            $specificRt = $adminRTs->firstWhere('rt_number', $agenda->rt_number);
            if ($specificRt && $specificRt->id !== ($adminRw ? $adminRw->id : null)) {
                AgendaKehadiran::create([
                    'agenda_id' => $agenda->id,
                    'user_id' => $specificRt->id,
                    'nama_peserta' => $specificRt->name,
                    'jabatan' => 'Ketua RT ' . $agenda->rt_number,
                    'rt_number' => $agenda->rt_number,
                    'status' => 'hadir',
                ]);
            }
        }
    }

    /**
     * Helper to dispatch system notifications.
     */
    protected function notifyParticipants(AgendaRapat $agenda, string $eventType, ?string $extraNote = null): void
    {
        try {
            $recipients = collect();

            if ($agenda->jenis_rapat === 'musyawarah_warga') {
                // If musyawarah warga, notify citizens and all admins
                $recipients = User::take(30)->get();
            } else {
                // Coordinations: notify admins and attendance list users
                $recipients = User::whereIn('role', ['admin', 'bendahara'])->get();
            }

            $judulNotif = match ($eventType) {
                'create' => "Undangan Agenda Rapat: {$agenda->judul}",
                'update' => "Perubahan Jadwal Rapat: {$agenda->judul}",
                'cancel' => "Pemberitahuan: Rapat Dibatalkan - {$agenda->judul}",
                'publish_notulen' => "Notulensi Diterbitkan: {$agenda->judul}",
                default => "Pemberitahuan Agenda: {$agenda->judul}",
            };

            $pesanNotif = match ($eventType) {
                'create' => "Agenda rapat '{$agenda->judul}' telah dijadwalkan pada {$agenda->waktu_formatted} di {$agenda->lokasi}.",
                'update' => "Terdapat penyesuaian jadwal/lokasi untuk agenda rapat '{$agenda->judul}'. Waktu baru: {$agenda->waktu_formatted} di {$agenda->lokasi}.",
                'cancel' => "Agenda rapat '{$agenda->judul}' telah dibatalkan." . ($extraNote ? " Alasan: {$extraNote}" : ""),
                'publish_notulen' => "Notulensi dan keputusan musyawarah untuk '{$agenda->judul}' telah resmi dipublikasikan.",
                default => "Pembaruan informasi pada agenda '{$agenda->judul}'.",
            };

            foreach ($recipients as $recipient) {
                FinanceNotification::create([
                    'user_id' => $recipient->id,
                    'tipe' => 'agenda',
                    'judul' => $judulNotif,
                    'pesan' => $pesanNotif,
                    'link_url' => route('agenda.show', $agenda->id),
                    'is_read' => false,
                ]);
            }
        } catch (\Exception $e) {
            // Ignore notification delivery failures so main flow is uninterrupted
        }
    }
}
