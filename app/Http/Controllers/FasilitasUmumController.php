<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\FasilitasUmum;
use App\Models\FinanceNotification;
use App\Models\PemeriksaanFasilitas;
use App\Models\PeminjamanFasilitas;
use App\Models\PeminjamanHistory;
use App\Models\User;
use App\Models\Warga;
use App\Services\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FasilitasUmumController extends Controller
{
    /**
     * Main index view with 4 integrated tabs.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdminRw = $user->isAdminRw();
        $isAdminRt = $user->isAdminRt();
        $isWarga = $user->isWarga();

        // 1. Tab parameter: katalog, kalender, riwayat, laporan
        $activeTab = $request->query('tab', 'katalog');
        if (!in_array($activeTab, ['katalog', 'kalender', 'riwayat', 'laporan'])) {
            $activeTab = 'katalog';
        }
        if ($isWarga && $activeTab === 'laporan') {
            $activeTab = 'katalog';
        }

        $allRts = ['006', '007', '008', '009', '010'];

        // Filters
        $search = $request->query('search');
        $selectedKategori = $request->query('kategori');
        $selectedFasilitasId = $request->query('fasilitas_id');
        $selectedStatus = $request->query('status');
        $selectedKondisi = $request->query('kondisi');
        $selectedRt = $request->query('rt_number');
        if ($isAdminRt) {
            $selectedRt = $user->rt_number;
        }

        $selectedTahun = $request->query('tahun', date('Y'));
        $selectedBulan = $request->query('bulan');

        // =========================================================================
        // 2. DASHBOARD SUMMARY STATS CARDS (ACTUAL DB DATA)
        // =========================================================================
        $totalFasilitas = FasilitasUmum::count();
        $totalTersedia = FasilitasUmum::where('status_operasional', 'tersedia')->count();
        $totalDalamPemeliharaan = FasilitasUmum::where('status_operasional', 'pemeliharaan')->count();

        $basePeminjamanQuery = PeminjamanFasilitas::query();
        if ($isWarga) {
            $basePeminjamanQuery->where('user_id', $user->id);
        } elseif ($isAdminRt) {
            $basePeminjamanQuery->where('rt_number', $user->rt_number);
        }

        $totalPengajuanMenunggu = (clone $basePeminjamanQuery)->where('status', 'menunggu')->count();
        $totalSedangDigunakan = (clone $basePeminjamanQuery)->where('status', 'digunakan')->count();
        $totalPeminjamanSelesai = (clone $basePeminjamanQuery)->where('status', 'selesai')->count();
        $totalPeminjamanDisetujui = (clone $basePeminjamanQuery)->where('status', 'disetujui')->count();

        // =========================================================================
        // 3. TAB 1: KATALOG FASILITAS DATA
        // =========================================================================
        $fasilitasQuery = FasilitasUmum::withCount('peminjamans')->orderBy('nama', 'asc');

        if (!empty($selectedKategori)) {
            $fasilitasQuery->where('kategori', $selectedKategori);
        }
        if (!empty($selectedKondisi)) {
            $fasilitasQuery->where('kondisi_fisik', $selectedKondisi);
        }
        if ($request->filled('status_operasional')) {
            $fasilitasQuery->where('status_operasional', $request->status_operasional);
        }
        if (!empty($search) && $activeTab === 'katalog') {
            $fasilitasQuery->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $fasilitas = $fasilitasQuery->get();
        $kategoriList = FasilitasUmum::distinct()->pluck('kategori')->filter()->values();

        // Database citizens for dropdown if Admin
        $wargaDropdown = collect();
        if (!$isWarga) {
            $wargaDropdown = Warga::where('status_warga', '!=', 'pindah')
                ->when($isAdminRt, fn($q) => $q->where('rt_number', $user->rt_number))
                ->orderBy('nama')
                ->get(['id', 'nama', 'rt_number', 'nik', 'telepon']);
        }

        // =========================================================================
        // 4. TAB 2: KALENDER KETERSEDIAAN DATA (INTEGRASI PENUH DATABASE & FILTER)
        // =========================================================================
        $calendarFasilitasId = $request->query('kalender_fasilitas_id', ($activeTab === 'kalender' ? $selectedFasilitasId : null));
        $calendarRt = $request->query('kalender_rt', ($activeTab === 'kalender' && $isAdminRt ? $user->rt_number : ($activeTab === 'kalender' ? $selectedRt : null)));
        $calendarStatus = $request->query('kalender_status', ($activeTab === 'kalender' ? $selectedStatus : null));

        $calendarQuery = PeminjamanFasilitas::with(['fasilitas', 'user'])
            ->whereNotIn('status', ['ditolak', 'dibatalkan']); // Ditolak/dibatalkan tidak dihitung aktif

        if (!empty($calendarFasilitasId)) {
            $calendarQuery->where('fasilitas_id', $calendarFasilitasId);
        }
        if (!empty($calendarRt)) {
            $calendarQuery->where('rt_number', $calendarRt);
        }
        if (!empty($calendarStatus)) {
            $calendarQuery->where('status', $calendarStatus);
        }

        $allCalendarEvents = $calendarQuery->orderBy('waktu_mulai', 'asc')->get();

        // Format events for modern calendar visualization
        $calendarEventsJson = $allCalendarEvents->map(function ($ev) {
            $bgColor = match ($ev->status) {
                'disetujui' => '#1F567A', // Biru solid terkonfirmasi
                'digunakan' => '#285943', // Forest green sedang dipakai
                'selesai'   => '#5A6E62', // Sage/dark muted selesai
                default     => '#D97706', // Amber/kuning menunggu review
            };

            $isPending = ($ev->status === 'menunggu');

            return [
                'id' => $ev->id,
                'title' => ($ev->fasilitas ? $ev->fasilitas->nama : 'Fasilitas') . ' · ' . $ev->nama_peminjam . ' (RT ' . $ev->rt_number . ')',
                'start' => $ev->waktu_mulai->format('Y-m-d\TH:i:s'),
                'end' => $ev->waktu_selesai->format('Y-m-d\TH:i:s'),
                'status' => $ev->status,
                'status_label' => $ev->status_label,
                'is_pending' => $isPending,
                'backgroundColor' => $bgColor,
                'borderColor' => $bgColor,
                'textColor' => '#ffffff',
                'tujuan' => $ev->tujuan,
                'fasilitas_nama' => $ev->fasilitas ? $ev->fasilitas->nama : '-',
                'fasilitas_kategori' => $ev->fasilitas ? $ev->fasilitas->kategori : '-',
                'peminjam' => $ev->nama_peminjam,
                'rt' => $ev->rt_number,
                'jam_mulai' => $ev->waktu_mulai->format('H:i'),
                'jam_selesai' => $ev->waktu_selesai->format('H:i'),
                'jumlah_peserta' => $ev->jumlah_peserta,
            ];
        });

        // Upcoming schedule list (next 14 days, filtered & database derived)
        $upcomingQuery = PeminjamanFasilitas::with(['fasilitas', 'user'])
            ->whereNotIn('status', ['ditolak', 'dibatalkan'])
            ->where('waktu_mulai', '>=', now()->subHours(6));

        if (!empty($calendarFasilitasId)) {
            $upcomingQuery->where('fasilitas_id', $calendarFasilitasId);
        }
        if (!empty($calendarRt)) {
            $upcomingQuery->where('rt_number', $calendarRt);
        }
        if (!empty($calendarStatus)) {
            $upcomingQuery->where('status', $calendarStatus);
        }

        $upcomingSchedules = $upcomingQuery->orderBy('waktu_mulai', 'asc')
            ->limit(15)
            ->get();

        // =========================================================================
        // 5. TAB 3: DAFTAR PENGAJUAN & RIWAYAT PEMINJAMAN
        // =========================================================================
        $riwayatQuery = PeminjamanFasilitas::with(['fasilitas', 'user', 'approver', 'pemeriksaan'])
            ->orderBy('waktu_mulai', 'desc');

        // RBAC Scoping
        if ($isWarga) {
            $riwayatQuery->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('nama_peminjam', $user->name);
            });
        } elseif ($isAdminRt) {
            $riwayatQuery->where('rt_number', $user->rt_number);
        } elseif (!empty($selectedRt)) {
            $riwayatQuery->where('rt_number', $selectedRt);
        }

        if (!empty($selectedStatus)) {
            $riwayatQuery->where('status', $selectedStatus);
        }
        if (!empty($selectedFasilitasId)) {
            $riwayatQuery->where('fasilitas_id', $selectedFasilitasId);
        }
        if ($request->filled('tanggal_dari')) {
            $riwayatQuery->whereDate('waktu_mulai', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $riwayatQuery->whereDate('waktu_mulai', '<=', $request->tanggal_sampai);
        }
        if (!empty($search) && $activeTab === 'riwayat') {
            $riwayatQuery->where(function ($q) use ($search) {
                $q->where('nomor_pengajuan', 'like', "%{$search}%")
                  ->orWhere('nama_peminjam', 'like', "%{$search}%")
                  ->orWhere('tujuan', 'like', "%{$search}%");
            });
        }

        $peminjamans = $riwayatQuery->paginate(15, ['*'], 'page_peminjaman')->withQueryString();

        // =========================================================================
        // 6. TAB 4: LAPORAN & STATISTIK PENGGUNAAN (CHART.JS)
        // =========================================================================
        $chartMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $chartBookingsMonthly = array_fill(0, 12, 0);
        $chartBookingsApprovedMonthly = array_fill(0, 12, 0);

        $evalRecords = PeminjamanFasilitas::whereYear('waktu_mulai', $selectedTahun)->get();
        foreach ($evalRecords as $rec) {
            $mIdx = (int)$rec->waktu_mulai->format('m') - 1;
            if ($mIdx >= 0 && $mIdx < 12) {
                $chartBookingsMonthly[$mIdx]++;
                if (in_array($rec->status, ['disetujui', 'selesai', 'digunakan'])) {
                    $chartBookingsApprovedMonthly[$mIdx]++;
                }
            }
        }

        // Top Facilities by Usage
        $topFacilities = FasilitasUmum::withCount(['peminjamans' => function ($q) {
            $q->whereIn('status', ['disetujui', 'selesai', 'digunakan']);
        }])->orderByDesc('peminjamans_count')->limit(5)->get();

        $chartTopFacilities = [
            'labels' => $topFacilities->pluck('nama')->toArray(),
            'counts' => $topFacilities->pluck('peminjamans_count')->toArray(),
        ];

        // Bookings by RT
        $chartBookingsByRt = [
            'labels' => [],
            'counts' => [],
        ];
        foreach ($allRts as $rtCode) {
            $chartBookingsByRt['labels'][] = 'RT ' . $rtCode;
            $chartBookingsByRt['counts'][] = PeminjamanFasilitas::where('rt_number', $rtCode)
                ->whereIn('status', ['disetujui', 'selesai', 'digunakan'])
                ->count();
        }

        // Damage / Maintenance History
        $damageReports = PemeriksaanFasilitas::with(['fasilitas', 'peminjaman'])
            ->where('ada_kerusakan', true)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('fasilitas.index', compact(
            'isAdminRw',
            'isAdminRt',
            'isWarga',
            'activeTab',
            'allRts',
            'selectedRt',
            'search',
            'selectedKategori',
            'selectedFasilitasId',
            'selectedStatus',
            'selectedKondisi',
            'selectedTahun',
            'selectedBulan',
            'totalFasilitas',
            'totalTersedia',
            'totalPengajuanMenunggu',
            'totalSedangDigunakan',
            'totalPeminjamanSelesai',
            'totalPeminjamanDisetujui',
            'totalDalamPemeliharaan',
            'fasilitas',
            'kategoriList',
            'wargaDropdown',
            'peminjamans',
            'calendarEventsJson',
            'upcomingSchedules',
            'chartMonths',
            'chartBookingsMonthly',
            'chartBookingsApprovedMonthly',
            'chartTopFacilities',
            'chartBookingsByRt',
            'damageReports',
            'calendarFasilitasId',
            'calendarRt',
            'calendarStatus'
        ));
    }

    /**
     * Submit a new facility booking request with automatic conflict detection.
     */
    public function storeBooking(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'fasilitas_id' => 'required|exists:fasilitas_umums,id',
            'nama_peminjam' => 'required|string|max:255',
            'rt_number' => 'required|string',
            'nomor_kontak' => 'required|string|max:50',
            'tujuan' => 'required|string',
            'jumlah_peserta' => 'required|integer|min:1',
            'waktu_mulai' => 'required|date|after_or_equal:today',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
            'dokumen_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'catatan' => 'nullable|string',
        ]);

        $fasilitas = FasilitasUmum::findOrFail($request->fasilitas_id);

        // Validation 1: Capacity warning/enforcement
        if ($fasilitas->kapasitas > 0 && $request->jumlah_peserta > ($fasilitas->kapasitas * 1.5)) {
            return redirect()->back()->withErrors([
                'jumlah_peserta' => "Jumlah peserta ({$request->jumlah_peserta}) melebihi daya tampung aman fasilitas ({$fasilitas->kapasitas} orang)."
            ])->withInput();
        }

        // Validation 2: Automatic Schedule Conflict Check
        $hasClash = PeminjamanFasilitas::where('fasilitas_id', $request->fasilitas_id)
            ->whereIn('status', ['disetujui', 'digunakan'])
            ->where(function ($q) use ($request) {
                $q->where('waktu_mulai', '<', $request->waktu_selesai)
                  ->where('waktu_selesai', '>', $request->waktu_mulai);
            })->exists();

        if ($hasClash) {
            return redirect()->back()->withErrors([
                'waktu_mulai' => "Jadwal bentrok! Fasilitas '{$fasilitas->nama}' sudah memiliki agenda yang telah disetujui pada jam tersebut. Silakan periksa Kalender Ketersediaan dan pilih jadwal lain."
            ])->withInput();
        }

        // Generate auto booking code: PINJAM-YYYYMM-XXXX
        $blnCode = date('Ym');
        $lastBooking = PeminjamanFasilitas::where('nomor_pengajuan', 'like', "PINJAM-{$blnCode}-%")->orderBy('id', 'desc')->first();
        $nextSeq = 1;
        if ($lastBooking && preg_match('/PINJAM-\d{6}-(\d+)/', $lastBooking->nomor_pengajuan, $m)) {
            $nextSeq = (int)$m[1] + 1;
        }
        $nomorPengajuan = 'PINJAM-' . $blnCode . '-' . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);

        $dokumenPath = null;
        if ($request->hasFile('dokumen_pendukung')) {
            $dokumenPath = $request->file('dokumen_pendukung')->store('peminjaman_dokumen', 'public');
        }

        $nikPeminjam = $user->warga ? $user->warga->nik : null;

        $peminjaman = PeminjamanFasilitas::create([
            'nomor_pengajuan' => $nomorPengajuan,
            'fasilitas_id' => $request->fasilitas_id,
            'user_id' => $user->id,
            'nik_peminjam' => $nikPeminjam,
            'nama_peminjam' => $request->nama_peminjam,
            'rt_number' => $request->rt_number,
            'nomor_kontak' => $request->nomor_kontak,
            'tujuan' => $request->tujuan,
            'jumlah_peserta' => $request->jumlah_peserta,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'status' => 'menunggu',
            'catatan_pengelola' => $request->catatan,
            'dokumen_pendukung' => $dokumenPath,
            'biaya_sewa' => $fasilitas->biaya_sewa ?: 0,
            'uang_jaminan' => $fasilitas->uang_jaminan ?: 0,
            'status_pembayaran' => ($fasilitas->biaya_sewa > 0 || $fasilitas->uang_jaminan > 0) ? 'menunggu_pembayaran' : 'gratis',
        ]);

        // Record initial history
        PeminjamanHistory::create([
            'peminjaman_id' => $peminjaman->id,
            'user_id' => $user->id,
            'status_sebelumnya' => null,
            'status_baru' => 'menunggu',
            'keterangan' => "Pengajuan peminjaman fasilitas {$nomorPengajuan} berhasil dikirim oleh {$request->nama_peminjam}.",
            'petugas' => $user->name,
        ]);

        // Dispatch notifications to Admin RW & Admin RT
        $admins = User::where(function ($q) use ($request) {
            $q->where('email', 'like', 'adminrw%')
              ->orWhere('email', 'admin@rt.com')
              ->orWhere(function ($sub) use ($request) {
                  $sub->where('role', 'admin')->where('rt_number', $request->rt_number);
              });
        })->get();

        foreach ($admins as $adm) {
            FinanceNotification::send(
                $adm->id,
                'peminjaman_fasilitas_baru',
                'Pengajuan Peminjaman Baru: ' . $nomorPengajuan,
                "Permohonan peminjaman '{$fasilitas->nama}' oleh {$request->nama_peminjam} (RT {$request->rt_number}) tanggal " . Carbon::parse($request->waktu_mulai)->translatedFormat('d M Y') . " menunggu persetujuan.",
                ['link_url' => route('fasilitas.index', ['tab' => 'riwayat', 'search' => $nomorPengajuan])]
            );
        }

        AuditLogger::log(
            'create',
            'Peminjaman Fasilitas',
            $peminjaman->id,
            "Pengajuan peminjaman {$nomorPengajuan} fasilitas '{$fasilitas->nama}' oleh {$request->nama_peminjam} (RT {$request->rt_number}) untuk tanggal {$request->waktu_mulai}.",
            null,
            $peminjaman->toArray(),
            $request->rt_number
        );

        return redirect()->route('fasilitas.index', ['tab' => 'riwayat'])
            ->with('success', "Permohonan peminjaman {$nomorPengajuan} berhasil diajukan dan sedang menunggu persetujuan pengelola!");
    }

    /**
     * Show booking details (JSON for modal).
     */
    public function showBooking($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $p = PeminjamanFasilitas::with(['fasilitas', 'user', 'approver', 'pemeriksaan', 'histories.user'])->findOrFail($id);

        $isOwner = $p->user_id === $user->id;
        $isRw = $user->isAdminRw();
        $isSameRt = $user->isAdminRt() && ($p->rt_number === $user->rt_number);

        if (!$isOwner && !$isRw && !$isSameRt) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak: Anda tidak memiliki izin untuk melihat detail peminjaman ini.'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $p->id,
                'nomor_pengajuan' => $p->nomor_pengajuan ?: 'PINJAM-00' . $p->id,
                'fasilitas_id' => $p->fasilitas_id,
                'fasilitas_nama' => $p->fasilitas ? $p->fasilitas->nama : '-',
                'fasilitas_kategori' => $p->fasilitas ? $p->fasilitas->kategori : '-',
                'fasilitas_lokasi' => $p->fasilitas ? $p->fasilitas->lokasi : '-',
                'nama_peminjam' => $p->nama_peminjam,
                'rt_number' => $p->rt_number,
                'nik_peminjam' => $p->nik_peminjam ?: '-',
                'nomor_kontak' => $p->nomor_kontak ?: '-',
                'tujuan' => $p->tujuan,
                'jumlah_peserta' => $p->jumlah_peserta,
                'waktu_mulai_formatted' => $p->waktu_mulai->translatedFormat('l, d F Y - H:i') . ' WIB',
                'waktu_selesai_formatted' => $p->waktu_selesai->translatedFormat('l, d F Y - H:i') . ' WIB',
                'status' => $p->status,
                'status_label' => $p->status_label,
                'status_badge' => $p->status_badge,
                'catatan_pengelola' => $p->catatan_pengelola ?: '-',
                'alasan_penolakan' => $p->alasan_penolakan ?: '-',
                'biaya_sewa' => $p->biaya_sewa,
                'biaya_sewa_formatted' => 'Rp ' . number_format($p->biaya_sewa, 0, ',', '.'),
                'uang_jaminan' => $p->uang_jaminan,
                'uang_jaminan_formatted' => 'Rp ' . number_format($p->uang_jaminan, 0, ',', '.'),
                'status_pembayaran' => $p->status_pembayaran,
                'dokumen_url' => $p->dokumen_pendukung ? asset('storage/' . $p->dokumen_pendukung) : null,
                'approver_nama' => $p->approver ? $p->approver->name : '-',
                'pemeriksaan' => $p->pemeriksaan ? [
                    'petugas_nama' => $p->pemeriksaan->petugas_nama,
                    'kondisi_sebelum' => $p->pemeriksaan->kondisi_sebelum,
                    'catatan_sebelum' => $p->pemeriksaan->catatan_sebelum ?: '-',
                    'perlengkapan_keluar' => $p->pemeriksaan->perlengkapan_keluar ?: '-',
                    'kondisi_sesudah' => $p->pemeriksaan->kondisi_sesudah ?: 'Belum diperiksa',
                    'catatan_sesudah' => $p->pemeriksaan->catatan_sesudah ?: '-',
                    'perlengkapan_kembali' => $p->pemeriksaan->perlengkapan_kembali ?: '-',
                    'ada_kerusakan' => (bool)$p->pemeriksaan->ada_kerusakan,
                    'tindak_lanjut_kerusakan' => $p->pemeriksaan->tindak_lanjut_kerusakan ?: '-',
                ] : null,
                'histories' => $p->histories->map(fn($h) => [
                    'status_baru' => $h->status_baru,
                    'keterangan' => $h->keterangan,
                    'petugas' => $h->petugas ?: ($h->user ? $h->user->name : '-'),
                    'time_formatted' => $h->created_at->translatedFormat('d M Y, H:i') . ' WIB',
                ]),
            ]
        ]);
    }

    /**
     * Admin review: approve, reject, start using, or complete booking.
     */
    public function reviewBooking(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            return redirect()->route('fasilitas.index')->with('error', 'Akses ditolak.');
        }

        $booking = PeminjamanFasilitas::with('fasilitas')->findOrFail($id);

        if ($user->isAdminRt() && $booking->rt_number !== $user->rt_number) {
            return redirect()->route('fasilitas.index')->with('error', 'Hanya dapat meninjau permohonan warga RT Anda sendiri.');
        }

        $request->validate([
            'status' => 'required|in:disetujui,ditolak,digunakan,menunggu_pengembalian,selesai,dibatalkan',
            'catatan_pengelola' => 'nullable|string',
            'alasan_penolakan' => 'nullable|string',
            'status_pembayaran' => 'nullable|in:gratis,menunggu_pembayaran,lunas',
        ]);

        // If approving, check schedule collision strictly
        if ($request->status === 'disetujui') {
            $hasClash = PeminjamanFasilitas::where('fasilitas_id', $booking->fasilitas_id)
                ->where('id', '!=', $booking->id)
                ->whereIn('status', ['disetujui', 'digunakan'])
                ->where(function ($q) use ($booking) {
                    $q->where('waktu_mulai', '<', $booking->waktu_selesai)
                      ->where('waktu_selesai', '>', $booking->waktu_mulai);
                })->exists();

            if ($hasClash) {
                return redirect()->back()->with('error', 'Gagal menyetujui: Terdapat jadwal lain yang sudah disetujui bersamaan dengan rentang waktu fasilitas ini!');
            }
        }

        $oldStatus = $booking->status;
        $booking->status = $request->status;
        $booking->catatan_pengelola = $request->catatan_pengelola ?: $booking->catatan_pengelola;
        if ($request->status === 'ditolak') {
            $booking->alasan_penolakan = $request->alasan_penolakan ?: 'Tidak memenuhi syarat / jadwal tidak memungkinkan.';
        }
        if ($request->filled('status_pembayaran')) {
            $booking->status_pembayaran = $request->status_pembayaran;
        }
        $booking->disetujui_oleh = $user->id;
        $booking->save();

        // Record history step
        PeminjamanHistory::create([
            'peminjaman_id' => $booking->id,
            'user_id' => $user->id,
            'status_sebelumnya' => $oldStatus,
            'status_baru' => $request->status,
            'keterangan' => $request->status === 'ditolak' ? "Ditolak: {$booking->alasan_penolakan}" : ($request->catatan_pengelola ?: "Status diubah menjadi {$booking->status_label}"),
            'petugas' => $user->name,
        ]);

        // Send notification to the citizen
        if ($booking->user_id && $booking->user_id !== $user->id) {
            $judulNotif = match ($request->status) {
                'disetujui' => 'Pengajuan Peminjaman Disetujui!',
                'ditolak' => 'Pengajuan Peminjaman Ditolak',
                'selesai' => 'Peminjaman Fasilitas Selesai',
                default => 'Status Peminjaman Fasilitas Diperbarui',
            };
            $pesanNotif = match ($request->status) {
                'disetujui' => "Pengajuan fasilitas '{$booking->fasilitas->nama}' untuk tanggal " . $booking->waktu_mulai->translatedFormat('d M Y') . " telah disetujui oleh pengelola.",
                'ditolak' => "Pengajuan fasilitas '{$booking->fasilitas->nama}' ditolak. Alasan: " . ($booking->alasan_penolakan ?: 'Jadwal bertabrakan.'),
                'selesai' => "Penggunaan fasilitas '{$booking->fasilitas->nama}' telah selesai dan pemeriksaan pengembalian tuntas. Terima kasih telah menjaga kebersihan sarana bersama.",
                default => "Permohonan peminjaman Anda kini berstatus: {$booking->status_label}.",
            };

            FinanceNotification::send(
                $booking->user_id,
                'peminjaman_update',
                $judulNotif,
                $pesanNotif,
                ['link_url' => route('fasilitas.index', ['tab' => 'riwayat', 'search' => $booking->nomor_pengajuan])]
            );
        }

        AuditLogger::log(
            $request->status === 'disetujui' ? 'approve' : ($request->status === 'ditolak' ? 'reject' : 'update'),
            'Peminjaman Fasilitas',
            $booking->id,
            "Permohonan peminjaman {$booking->nomor_pengajuan} ({$booking->fasilitas->nama}) diubah menjadi " . strtoupper($request->status) . " oleh {$user->name}.",
            ['status' => $oldStatus],
            ['status' => $booking->status, 'catatan' => $booking->catatan_pengelola],
            $booking->rt_number
        );

        return redirect()->route('fasilitas.index', ['tab' => 'riwayat'])
            ->with('success', "Status permohonan {$booking->nomor_pengajuan} berhasil diperbarui menjadi {$booking->status_label}!");
    }

    /**
     * Cancel booking request by citizen (only if status is still 'menunggu').
     */
    public function batalBooking(Request $request, $id)
    {
        $user = Auth::user();
        $booking = PeminjamanFasilitas::findOrFail($id);

        if ($user->isWarga() && $booking->user_id !== $user->id) {
            return redirect()->route('fasilitas.index')->with('error', 'Akses ditolak.');
        }

        if ($booking->status !== 'menunggu') {
            return redirect()->route('fasilitas.index')->with('error', 'Hanya permohonan yang masih menunggu persetujuan yang dapat dibatalkan.');
        }

        $booking->update([
            'status' => 'dibatalkan',
            'catatan_pengelola' => 'Dibatalkan oleh peminjam.',
        ]);

        PeminjamanHistory::create([
            'peminjaman_id' => $booking->id,
            'user_id' => $user->id,
            'status_sebelumnya' => 'menunggu',
            'status_baru' => 'dibatalkan',
            'keterangan' => 'Permohonan dibatalkan oleh peminjam.',
            'petugas' => $user->name,
        ]);

        AuditLogger::log(
            'update',
            'Peminjaman Fasilitas',
            $booking->id,
            "Permohonan {$booking->nomor_pengajuan} dibatalkan oleh peminjam {$user->name}.",
            ['status' => 'menunggu'],
            ['status' => 'dibatalkan'],
            $booking->rt_number
        );

        return redirect()->route('fasilitas.index', ['tab' => 'riwayat'])
            ->with('success', "Permohonan peminjaman {$booking->nomor_pengajuan} berhasil dibatalkan.");
    }

    /**
     * Save pre-use and post-use facility condition inspection.
     */
    public function simpanPemeriksaan(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            return redirect()->route('fasilitas.index')->with('error', 'Akses ditolak.');
        }

        $booking = PeminjamanFasilitas::with('fasilitas')->findOrFail($id);

        $request->validate([
            'tahap' => 'required|in:sebelum,sesudah',
            'petugas_nama' => 'required|string|max:100',
            'kondisi' => 'required|string',
            'catatan' => 'nullable|string',
            'perlengkapan' => 'nullable|string',
            'foto' => 'nullable|image|max:4096',
            'ada_kerusakan' => 'nullable|boolean',
            'tindak_lanjut_kerusakan' => 'nullable|string',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('fasilitas_inspeksi', 'public');
        }

        $pemeriksaan = PemeriksaanFasilitas::firstOrNew(['peminjaman_id' => $booking->id]);
        $pemeriksaan->fasilitas_id = $booking->fasilitas_id;
        $pemeriksaan->petugas_nama = $request->petugas_nama ?: $user->name;

        if ($request->tahap === 'sebelum') {
            $pemeriksaan->tanggal_cek_awal = now();
            $pemeriksaan->kondisi_sebelum = $request->kondisi;
            $pemeriksaan->catatan_sebelum = $request->catatan;
            $pemeriksaan->perlengkapan_keluar = $request->perlengkapan;
            if ($fotoPath) $pemeriksaan->foto_sebelum = $fotoPath;

            // Automatically move status to 'digunakan' if currently approved
            if ($booking->status === 'disetujui') {
                $booking->update(['status' => 'digunakan']);
            }
        } else {
            // Tahap sesudah penggunaan
            $pemeriksaan->tanggal_cek_akhir = now();
            $pemeriksaan->kondisi_sesudah = $request->kondisi;
            $pemeriksaan->catatan_sesudah = $request->catatan;
            $pemeriksaan->perlengkapan_kembali = $request->perlengkapan;
            $pemeriksaan->ada_kerusakan = $request->boolean('ada_kerusakan');
            $pemeriksaan->tindak_lanjut_kerusakan = $request->tindak_lanjut_kerusakan;
            if ($fotoPath) $pemeriksaan->foto_sesudah = $fotoPath;

            // Move booking to 'selesai'
            $booking->update(['status' => 'selesai']);

            // Update facility condition if damage reported
            if ($request->boolean('ada_kerusakan')) {
                $booking->fasilitas->update(['kondisi_fisik' => 'Perlu Perawatan']);
            }
        }
        $pemeriksaan->save();

        PeminjamanHistory::create([
            'peminjaman_id' => $booking->id,
            'user_id' => $user->id,
            'status_sebelumnya' => $booking->status,
            'status_baru' => $booking->status,
            'keterangan' => "Pemeriksaan kondisi fisik tahap {$request->tahap} dicatat oleh {$request->petugas_nama}. Kondisi: {$request->kondisi}.",
            'petugas' => $request->petugas_nama,
        ]);

        AuditLogger::log(
            'update',
            'Peminjaman Fasilitas',
            $booking->id,
            "Pemeriksaan fasilitas {$booking->fasilitas->nama} ({$booking->nomor_pengajuan}) tahap {$request->tahap} oleh {$request->petugas_nama}. Kondisi: {$request->kondisi}.",
            null,
            $pemeriksaan->toArray(),
            $booking->rt_number
        );

        return redirect()->route('fasilitas.index', ['tab' => 'riwayat'])
            ->with('success', "Data pemeriksaan fasilitas tahap {$request->tahap} berhasil disimpan!");
    }

    /**
     * Store a new facility (Admin RW only).
     */
    public function storeFasilitas(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdminRw()) {
            return redirect()->route('fasilitas.index')->with('error', 'Hanya Admin RW yang berwenang mengelola data fasilitas.');
        }

        $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'lokasi' => 'required|string|max:255',
            'kapasitas' => 'required|integer|min:0',
            'deskripsi' => 'required|string',
            'jam_operasional' => 'required|string|max:100',
            'daftar_perlengkapan' => 'nullable|string',
            'aturan_penggunaan' => 'nullable|string',
            'biaya_sewa' => 'nullable|numeric|min:0',
            'uang_jaminan' => 'nullable|numeric|min:0',
            'penanggung_jawab' => 'nullable|string|max:100',
            'kontak_pj' => 'nullable|string|max:50',
            'kondisi_fisik' => 'required|in:Baik,Perlu Perawatan,Tidak Tersedia',
            'status_operasional' => 'required|in:tersedia,digunakan,pemeliharaan',
            'foto' => 'nullable|image|max:4096',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('fasilitas_foto', 'public');
        }

        $fasilitas = FasilitasUmum::create([
            'nama' => $request->nama,
            'kategori' => $request->kategori,
            'lokasi' => $request->lokasi,
            'kapasitas' => $request->kapasitas,
            'deskripsi' => $request->deskripsi,
            'jam_operasional' => $request->jam_operasional,
            'daftar_perlengkapan' => $request->daftar_perlengkapan,
            'aturan_penggunaan' => $request->aturan_penggunaan,
            'biaya_sewa' => $request->biaya_sewa ?: 0,
            'uang_jaminan' => $request->uang_jaminan ?: 0,
            'penanggung_jawab' => $request->penanggung_jawab ?: 'Pengurus RW 018',
            'kontak_pj' => $request->kontak_pj,
            'kondisi_fisik' => $request->kondisi_fisik,
            'status_operasional' => $request->status_operasional,
            'foto' => $fotoPath,
        ]);

        AuditLogger::log(
            'create',
            'Fasilitas Umum',
            $fasilitas->id,
            "Penambahan fasilitas baru '{$fasilitas->nama}' ({$fasilitas->kategori}) oleh {$user->name}.",
            null,
            $fasilitas->toArray(),
            'RW'
        );

        return redirect()->route('fasilitas.index', ['tab' => 'katalog'])
            ->with('success', "Fasilitas umum '{$fasilitas->nama}' berhasil ditambahkan!");
    }

    /**
     * Show facility details (JSON for modal / catalog).
     */
    public function showFasilitas($id)
    {
        $user = Auth::user();
        $f = FasilitasUmum::with(['peminjamans' => function ($q) {
            $q->whereIn('status', ['disetujui', 'digunakan'])->where('waktu_mulai', '>=', now()->subHours(6))->orderBy('waktu_mulai', 'asc')->limit(5);
        }])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $f->id,
                'nama' => $f->nama,
                'kategori' => $f->kategori,
                'lokasi' => $f->lokasi,
                'kapasitas' => $f->kapasitas,
                'deskripsi' => $f->deskripsi,
                'jam_operasional' => $f->jam_operasional ?: '08:00 - 22:00 WIB',
                'daftar_perlengkapan' => $f->daftar_perlengkapan ?: 'Tidak ada daftar inventaris khusus.',
                'aturan_penggunaan' => $f->aturan_penggunaan ?: '1. Wajib menjaga kebersihan sarana bersama.\n2. Mengembalikan perlengkapan tepat waktu.',
                'biaya_sewa' => $f->biaya_sewa,
                'biaya_sewa_formatted' => $f->biaya_sewa > 0 ? 'Rp ' . number_format($f->biaya_sewa, 0, ',', '.') : 'Gratis untuk Warga RW 018',
                'uang_jaminan' => $f->uang_jaminan,
                'uang_jaminan_formatted' => $f->uang_jaminan > 0 ? 'Rp ' . number_format($f->uang_jaminan, 0, ',', '.') : 'Rp 0',
                'penanggung_jawab' => $f->penanggung_jawab ?: 'Pengurus RW 018',
                'kontak_pj' => $f->kontak_pj ?: '-',
                'kondisi_fisik' => $f->kondisi_fisik,
                'kondisi_badge' => $f->kondisi_fisik_badge,
                'status_operasional' => $f->status_operasional,
                'status_badge' => $f->status_operasional_badge,
                'foto_url' => $f->foto ? asset('storage/' . $f->foto) : null,
                'upcoming_bookings' => $f->peminjamans->map(function($b) use ($user) {
                    $canSeeDetails = $user && ($user->isAdminRw() || $user->isAdminRt() || $b->user_id === $user->id);
                    return [
                        'nama_peminjam' => $canSeeDetails ? $b->nama_peminjam : 'Warga RT ' . $b->rt_number,
                        'rt' => $b->rt_number,
                        'tujuan' => $canSeeDetails ? $b->tujuan : 'Kegiatan Warga',
                        'waktu_mulai' => $b->waktu_mulai->translatedFormat('d M Y - H:i'),
                        'waktu_selesai' => $b->waktu_selesai->format('H:i') . ' WIB',
                    ];
                }),
            ]
        ]);
    }

    /**
     * Update existing facility (Admin RW only).
     */
    public function updateFasilitas(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isAdminRw()) {
            return redirect()->route('fasilitas.index')->with('error', 'Hanya Admin RW yang berwenang mengubah fasilitas.');
        }

        $fasilitas = FasilitasUmum::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'lokasi' => 'required|string|max:255',
            'kapasitas' => 'required|integer|min:0',
            'deskripsi' => 'required|string',
            'jam_operasional' => 'required|string|max:100',
            'daftar_perlengkapan' => 'nullable|string',
            'aturan_penggunaan' => 'nullable|string',
            'biaya_sewa' => 'nullable|numeric|min:0',
            'uang_jaminan' => 'nullable|numeric|min:0',
            'penanggung_jawab' => 'nullable|string|max:100',
            'kontak_pj' => 'nullable|string|max:50',
            'kondisi_fisik' => 'required|in:Baik,Perlu Perawatan,Tidak Tersedia',
            'status_operasional' => 'required|in:tersedia,digunakan,pemeliharaan',
            'foto' => 'nullable|image|max:4096',
        ]);

        $fotoPath = $fasilitas->foto;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('fasilitas_foto', 'public');
        }

        $oldData = $fasilitas->toArray();

        $fasilitas->update([
            'nama' => $request->nama,
            'kategori' => $request->kategori,
            'lokasi' => $request->lokasi,
            'kapasitas' => $request->kapasitas,
            'deskripsi' => $request->deskripsi,
            'jam_operasional' => $request->jam_operasional,
            'daftar_perlengkapan' => $request->daftar_perlengkapan,
            'aturan_penggunaan' => $request->aturan_penggunaan,
            'biaya_sewa' => $request->biaya_sewa ?: 0,
            'uang_jaminan' => $request->uang_jaminan ?: 0,
            'penanggung_jawab' => $request->penanggung_jawab,
            'kontak_pj' => $request->kontak_pj,
            'kondisi_fisik' => $request->kondisi_fisik,
            'status_operasional' => $request->status_operasional,
            'foto' => $fotoPath,
        ]);

        AuditLogger::log(
            'update',
            'Fasilitas Umum',
            $fasilitas->id,
            "Pembaruan data fasilitas '{$fasilitas->nama}' oleh {$user->name}.",
            $oldData,
            $fasilitas->toArray(),
            'RW'
        );

        return redirect()->route('fasilitas.index', ['tab' => 'katalog'])
            ->with('success', "Data fasilitas '{$fasilitas->nama}' berhasil diperbarui!");
    }

    /**
     * Toggle maintenance status of facility.
     */
    public function toggleStatusFasilitas(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isAdminRw()) {
            return redirect()->route('fasilitas.index')->with('error', 'Akses ditolak.');
        }

        $fasilitas = FasilitasUmum::findOrFail($id);
        $newStatus = $fasilitas->status_operasional === 'pemeliharaan' ? 'tersedia' : 'pemeliharaan';
        $fasilitas->update(['status_operasional' => $newStatus]);

        AuditLogger::log(
            'update',
            'Fasilitas Umum',
            $fasilitas->id,
            "Status fasilitas '{$fasilitas->nama}' diubah menjadi {$newStatus} oleh {$user->name}.",
            ['status_operasional' => $fasilitas->status_operasional],
            ['status_operasional' => $newStatus],
            'RW'
        );

        return redirect()->route('fasilitas.index', ['tab' => 'katalog'])
            ->with('success', "Status fasilitas '{$fasilitas->nama}' diubah menjadi " . ucfirst($newStatus) . "!");
    }

    /**
     * Delete facility (Admin RW only).
     */
    public function destroyFasilitas($id)
    {
        $user = Auth::user();
        if (!$user->isAdminRw()) {
            return redirect()->route('fasilitas.index')->with('error', 'Akses ditolak.');
        }

        $fasilitas = FasilitasUmum::findOrFail($id);
        $nama = $fasilitas->nama;
        $fasilitas->delete();

        AuditLogger::log(
            'delete',
            'Fasilitas Umum',
            $id,
            "Penghapusan fasilitas '{$nama}' oleh {$user->name}.",
            null,
            null,
            'RW'
        );

        return redirect()->route('fasilitas.index', ['tab' => 'katalog'])
            ->with('success', "Fasilitas '{$nama}' berhasil dihapus dari katalog.");
    }
}
