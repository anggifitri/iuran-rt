<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Cctv;
use App\Models\CctvRecording;
use App\Models\FinanceNotification;
use App\Models\JadwalRonda;
use App\Models\LaporanKeamanan;
use App\Models\LaporanKeamananHistory;
use App\Models\User;
use App\Models\Warga;
use App\Services\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RondaKeamananController extends Controller
{
    /**
     * Display the main Ronda & Keamanan center with 4 core tabs.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdminRw = $user->isAdminRw();
        $isAdminRt = $user->isAdminRt();
        $isWarga = $user->isWarga();

        // 1. Tab & Filter Parameters
        $activeTab = $request->query('tab', 'jadwal');
        if (!in_array($activeTab, ['jadwal', 'laporan', 'cctv', 'evaluasi'])) {
            $activeTab = 'jadwal';
        }

        $allRts = ['006', '007', '008', '009', '010'];
        $rtList = $allRts;

        // RT Filtering: Admin RT is locked to their own RT
        $selectedRt = $request->query('rt_number');
        if ($isAdminRt) {
            $selectedRt = $user->rt_number;
        } elseif ($isWarga) {
            $selectedRt = $user->rt_number ?: '006';
        }

        $search = $request->query('search');
        $selectedStatus = $request->query('status');
        $selectedKategori = $request->query('kategori');
        $selectedTahun = $request->query('tahun', date('Y'));
        $selectedBulan = $request->query('bulan');

        // =========================================================================
        // 2. CORE STATISTICAL CARDS & SUMMARY (RBAC Scoped)
        // =========================================================================
        $baseJadwalQuery = JadwalRonda::query();
        $baseKejadianQuery = LaporanKeamanan::query();
        $baseCctvQuery = Cctv::query();

        if ($selectedRt && $selectedRt !== 'all') {
            $baseJadwalQuery->where('rt_number', $selectedRt);
            $baseKejadianQuery->where(function ($q) use ($selectedRt) {
                $q->where('rt_number', $selectedRt)->orWhere('rt_number', 'RW');
            });
            $baseCctvQuery->where(function ($q) use ($selectedRt) {
                $q->where('rt_number', $selectedRt)->orWhere('rt_number', 'RW');
            });
        }

        // 4 Main Stats Cards
        $totalJadwalMendatang = (clone $baseJadwalQuery)->whereDate('tanggal', '>=', today())->count();
        $totalJadwalTerlaksana = (clone $baseJadwalQuery)->where('status', 'selesai')->count();
        $totalKejadianBelumSelesai = (clone $baseKejadianQuery)->whereIn('status', ['baru', 'menunggu', 'diproses'])->count();
        $totalKejadianSelesai = (clone $baseKejadianQuery)->where('status', 'selesai')->count();

        // CCTV Summary Cards
        $totalCctv = (clone $baseCctvQuery)->count();
        $totalCctvOnline = (clone $baseCctvQuery)->where('status', 'online')->count();
        $totalCctvOffline = (clone $baseCctvQuery)->where('status', 'offline')->count();
        $totalCctvGangguan = (clone $baseCctvQuery)->where('status', 'gangguan')->count();

        // Activity Highlights
        $jadwalTerdekat = (clone $baseJadwalQuery)
            ->whereDate('tanggal', '>=', today())
            ->orderBy('tanggal', 'asc')
            ->limit(3)
            ->get();

        $laporanTerbaru = (clone $baseKejadianQuery)
            ->with(['user', 'cctv'])
            ->orderBy('waktu_kejadian', 'desc')
            ->limit(3)
            ->get();

        if ($isAdminRt) {
            $kameraGangguanList = Cctv::where('rt_number', $user->rt_number)->whereIn('status', ['gangguan', 'offline'])->get();
        } else {
            $kameraGangguanList = Cctv::whereIn('status', ['gangguan', 'offline'])->get();
        }

        $recentSecurityLogs = AuditLog::whereIn('module', ['Jadwal Ronda', 'Keamanan & Ketertiban', 'CCTV'])
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        // =========================================================================
        // 3. TAB 1: JADWAL RONDA DATA
        // =========================================================================
        $jadwalQuery = (clone $baseJadwalQuery)->orderBy('tanggal', 'desc');

        if (!empty($selectedStatus)) {
            $jadwalQuery->where('status', $selectedStatus);
        }
        if (!empty($selectedBulan)) {
            $jadwalQuery->whereMonth('tanggal', $selectedBulan);
        }
        if (!empty($selectedTahun)) {
            $jadwalQuery->whereYear('tanggal', $selectedTahun);
        }
        if (!empty($search)) {
            $jadwalQuery->where(function ($q) use ($search) {
                $q->where('petugas', 'like', "%{$search}%")
                  ->orWhere('koordinator', 'like', "%{$search}%")
                  ->orWhere('lokasi_pos', 'like', "%{$search}%")
                  ->orWhere('catatan', 'like', "%{$search}%");
            });
        }

        $jadwals = $jadwalQuery->paginate(15, ['*'], 'page_jadwal')->withQueryString();

        // =========================================================================
        // 4. TAB 2: LAPORAN KEJADIAN DATA
        // =========================================================================
        $laporanQuery = (clone $baseKejadianQuery)
            ->with(['user', 'cctv', 'histories'])
            ->orderBy('waktu_kejadian', 'desc');

        if (!empty($selectedStatus)) {
            $laporanQuery->where('status', $selectedStatus);
        }
        if (!empty($selectedKategori)) {
            $laporanQuery->where('kategori', $selectedKategori);
        }
        if (!empty($selectedBulan)) {
            $laporanQuery->whereMonth('waktu_kejadian', $selectedBulan);
        }
        if (!empty($selectedTahun)) {
            $laporanQuery->whereYear('waktu_kejadian', $selectedTahun);
        }
        if (!empty($search)) {
            $laporanQuery->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('nomor_laporan', 'like', "%{$search}%")
                  ->orWhere('nama_pelapor', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%")
                  ->orWhere('kronologi', 'like', "%{$search}%");
            });
        }

        $laporanKeamanans = $laporanQuery->paginate(12, ['*'], 'page_laporan')->withQueryString();

        $kategoriList = [
            'Gangguan keamanan',
            'Kehilangan barang',
            'Aktivitas mencurigakan',
            'Kerusakan fasilitas umum',
            'Gangguan penerangan jalan',
            'Kerusakan CCTV',
            'Kejadian lainnya',
        ];

        $allCctvs = Cctv::orderBy('kode_kamera')->get(['id', 'kode_kamera', 'nama_kamera', 'rt_number', 'status']);

        // =========================================================================
        // 5. TAB 3: CCTV & DOKUMENTASI DATA
        // =========================================================================
        $cctvListQuery = Cctv::withCount('recordings')->orderBy('kode_kamera', 'asc');
        if ($selectedRt && $selectedRt !== 'all') {
            $cctvListQuery->where(function ($q) use ($selectedRt) {
                $q->where('rt_number', $selectedRt)->orWhere('rt_number', 'RW');
            });
        }
        if (!empty($selectedStatus)) {
            $cctvListQuery->where('status', $selectedStatus);
        }
        if (!empty($search)) {
            $cctvListQuery->where(function ($q) use ($search) {
                $q->where('nama_kamera', 'like', "%{$search}%")
                  ->orWhere('kode_kamera', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%")
                  ->orWhere('area_pantau', 'like', "%{$search}%");
            });
        }

        $cctvs = $cctvListQuery->get();

        // =========================================================================
        // 6. TAB 4: RIWAYAT & EVALUASI KEAMANAN (CHARTS & REKAP PER RT)
        // =========================================================================
        $chartMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $chartKejadianBulanan = array_fill(0, 12, 0);
        $chartKejadianSelesaiBulanan = array_fill(0, 12, 0);

        $evalKejadianRecords = LaporanKeamanan::whereYear('waktu_kejadian', $selectedTahun)
            ->when($selectedRt && $selectedRt !== 'all', function ($q) use ($selectedRt) {
                $q->where(function ($sub) use ($selectedRt) {
                    $sub->where('rt_number', $selectedRt)->orWhere('rt_number', 'RW');
                });
            })
            ->get();

        foreach ($evalKejadianRecords as $rec) {
            $mIdx = (int)Carbon::parse($rec->waktu_kejadian)->format('m') - 1;
            if ($mIdx >= 0 && $mIdx < 12) {
                $chartKejadianBulanan[$mIdx]++;
                if ($rec->status === 'selesai') {
                    $chartKejadianSelesaiBulanan[$mIdx]++;
                }
            }
        }

        // Kategori Breakdown for Doughnut Chart
        $kategoriDistribution = [];
        foreach ($kategoriList as $kat) {
            $count = (clone $baseKejadianQuery)->where('kategori', $kat)->count();
            if ($count > 0) {
                $kategoriDistribution[$kat] = $count;
            }
        }

        // Rekap Status Keamanan Per RT (5 RTs in RW 018)
        $rekapKeamananRt = [];
        $chartRondaPerRt = [
            'labels' => [],
            'terlaksana' => [],
            'tidak_terlaksana' => [],
        ];

        foreach ($allRts as $rt) {
            $qJadwal = JadwalRonda::where('rt_number', $rt);
            if (!empty($selectedTahun)) {
                $qJadwal->whereYear('tanggal', $selectedTahun);
            }
            if (!empty($selectedBulan)) {
                $qJadwal->whereMonth('tanggal', $selectedBulan);
            }

            $totJadwal = (clone $qJadwal)->count();
            $terlaksana = (clone $qJadwal)->where('status', 'selesai')->count();
            $tidakTerlaksana = (clone $qJadwal)->where('status', 'tidak_terlaksana')->count();
            $kepatuhan = $totJadwal > 0 ? round(($terlaksana / $totJadwal) * 100, 1) : 100;

            $qInsiden = LaporanKeamanan::where('rt_number', $rt);
            if (!empty($selectedTahun)) {
                $qInsiden->whereYear('waktu_kejadian', $selectedTahun);
            }
            $totInsiden = (clone $qInsiden)->count();
            $insidenSelesai = (clone $qInsiden)->where('status', 'selesai')->count();
            $insidenAktif = (clone $qInsiden)->whereIn('status', ['baru', 'menunggu', 'diproses'])->count();

            $cctvCount = Cctv::where('rt_number', $rt)->count();

            // Status Indikator Lingkungan
            $kondisi = 'Aman & Terkendali';
            $kondisiBadge = 'success';
            if ($insidenAktif > 2 || $kepatuhan < 70) {
                $kondisi = 'Perlu Perhatian';
                $kondisiBadge = 'danger';
            } elseif ($insidenAktif > 0 || $kepatuhan < 85) {
                $kondisi = 'Waspada Rutin';
                $kondisiBadge = 'warning';
            }

            $rekapKeamananRt[$rt] = [
                'rt_number' => $rt,
                'total_jadwal' => $totJadwal,
                'terlaksana' => $terlaksana,
                'tidak_terlaksana' => $tidakTerlaksana,
                'kepatuhan_persen' => $kepatuhan,
                'total_insiden' => $totInsiden,
                'insiden_selesai' => $insidenSelesai,
                'insiden_aktif' => $insidenAktif,
                'cctv_count' => $cctvCount,
                'kondisi' => $kondisi,
                'kondisi_badge' => $kondisiBadge,
            ];

            $chartRondaPerRt['labels'][] = 'RT ' . $rt;
            $chartRondaPerRt['terlaksana'][] = $terlaksana;
            $chartRondaPerRt['tidak_terlaksana'][] = $tidakTerlaksana;
        }

        return view('ronda.index', compact(
            'isAdminRw',
            'isAdminRt',
            'isWarga',
            'activeTab',
            'allRts',
            'rtList',
            'selectedRt',
            'search',
            'selectedStatus',
            'selectedKategori',
            'selectedTahun',
            'selectedBulan',
            'totalJadwalMendatang',
            'totalJadwalTerlaksana',
            'totalKejadianBelumSelesai',
            'totalKejadianSelesai',
            'totalCctv',
            'totalCctvOnline',
            'totalCctvOffline',
            'totalCctvGangguan',
            'jadwalTerdekat',
            'laporanTerbaru',
            'kameraGangguanList',
            'recentSecurityLogs',
            'jadwals',
            'laporanKeamanans',
            'kategoriList',
            'allCctvs',
            'cctvs',
            'chartMonths',
            'chartKejadianBulanan',
            'chartKejadianSelesaiBulanan',
            'kategoriDistribution',
            'rekapKeamananRt',
            'chartRondaPerRt'
        ));
    }

    /**
     * Store new patrol schedule.
     */
    public function storeJadwal(Request $request)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            return redirect()->route('ronda.index')->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'rt_number' => 'required|string',
            'tanggal' => 'required|date',
            'shift' => 'required|string',
            'lokasi_pos' => 'required|string',
            'petugas' => 'required|string',
            'koordinator' => 'nullable|string',
            'kontak_koordinator' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        if ($user->isAdminRt() && $request->rt_number !== $user->rt_number) {
            return redirect()->route('ronda.index')->with('error', 'Hanya bisa menambah jadwal untuk RT Anda sendiri.');
        }

        $jadwal = JadwalRonda::create([
            'rt_number' => $request->rt_number,
            'tanggal' => $request->tanggal,
            'shift' => $request->shift,
            'lokasi_pos' => $request->lokasi_pos,
            'petugas' => $request->petugas,
            'koordinator' => $request->koordinator ?: 'Petugas Piket RT ' . $request->rt_number,
            'kontak_koordinator' => $request->kontak_koordinator,
            'status' => 'terjadwal',
            'catatan' => $request->catatan,
        ]);

        AuditLogger::log(
            'create',
            'Jadwal Ronda',
            $jadwal->id,
            "Penambahan jadwal ronda RT {$request->rt_number} tanggal {$request->tanggal} ({$request->shift}) oleh {$user->name}.",
            null,
            $jadwal->toArray(),
            $request->rt_number
        );

        return redirect()->route('ronda.index', ['tab' => 'jadwal', 'rt_number' => $request->rt_number])
            ->with('success', 'Jadwal ronda berhasil ditambahkan!');
    }

    /**
     * Show schedule details (JSON for modal).
     */
    public function showJadwal($id)
    {
        $jadwal = JadwalRonda::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $jadwal->id,
                'rt_number' => $jadwal->rt_number,
                'tanggal' => Carbon::parse($jadwal->tanggal)->format('Y-m-d'),
                'tanggal_formatted' => Carbon::parse($jadwal->tanggal)->translatedFormat('l, d F Y'),
                'shift' => $jadwal->shift,
                'lokasi_pos' => $jadwal->lokasi_pos,
                'petugas' => $jadwal->petugas,
                'koordinator' => $jadwal->koordinator,
                'kontak_koordinator' => $jadwal->kontak_koordinator,
                'status' => $jadwal->status,
                'status_label' => $jadwal->status_label,
                'status_badge' => $jadwal->status_badge,
                'catatan' => $jadwal->catatan,
                'laporan_patroli' => $jadwal->laporan_patroli,
            ]
        ]);
    }

    /**
     * Update existing patrol schedule.
     */
    public function updateJadwal(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            return redirect()->route('ronda.index')->with('error', 'Akses ditolak.');
        }

        $jadwal = JadwalRonda::findOrFail($id);

        if ($user->isAdminRt() && $jadwal->rt_number !== $user->rt_number) {
            return redirect()->route('ronda.index')->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'rt_number' => 'required|string',
            'tanggal' => 'required|date',
            'shift' => 'required|string',
            'lokasi_pos' => 'required|string',
            'petugas' => 'required|string',
            'koordinator' => 'nullable|string',
            'kontak_koordinator' => 'nullable|string',
            'status' => 'required|in:terjadwal,berlangsung,selesai,tidak_terlaksana',
            'catatan' => 'nullable|string',
        ]);

        $oldData = $jadwal->toArray();

        $jadwal->update([
            'rt_number' => $request->rt_number,
            'tanggal' => $request->tanggal,
            'shift' => $request->shift,
            'lokasi_pos' => $request->lokasi_pos,
            'petugas' => $request->petugas,
            'koordinator' => $request->koordinator,
            'kontak_koordinator' => $request->kontak_koordinator,
            'status' => $request->status,
            'catatan' => $request->catatan,
        ]);

        AuditLogger::log(
            'update',
            'Jadwal Ronda',
            $jadwal->id,
            "Pembaruan data jadwal ronda #{$jadwal->id} RT {$jadwal->rt_number} oleh {$user->name}.",
            $oldData,
            $jadwal->toArray(),
            $jadwal->rt_number
        );

        return redirect()->route('ronda.index', ['tab' => 'jadwal'])
            ->with('success', 'Jadwal ronda berhasil diperbarui!');
    }

    /**
     * Update execution status of patrol schedule.
     */
    public function updateStatusJadwal(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            return redirect()->route('ronda.index')->with('error', 'Akses ditolak.');
        }

        $jadwal = JadwalRonda::findOrFail($id);
        if ($user->isAdminRt() && $jadwal->rt_number !== $user->rt_number) {
            return redirect()->route('ronda.index')->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'status' => 'required|in:terjadwal,berlangsung,selesai,tidak_terlaksana',
            'laporan_patroli' => 'nullable|string',
            'dokumentasi_patroli' => 'nullable|image|max:3072',
        ]);

        $docPath = $jadwal->dokumentasi_patroli;
        if ($request->hasFile('dokumentasi_patroli')) {
            $docPath = $request->file('dokumentasi_patroli')->store('ronda_dokumentasi', 'public');
        }

        $oldStatus = $jadwal->status;
        $jadwal->update([
            'status' => $request->status,
            'laporan_patroli' => $request->laporan_patroli ?: $jadwal->laporan_patroli,
            'dokumentasi_patroli' => $docPath,
        ]);

        AuditLogger::log(
            'update',
            'Jadwal Ronda',
            $jadwal->id,
            "Perubahan status jadwal ronda #{$jadwal->id} menjadi " . strtoupper($request->status) . " oleh {$user->name}.",
            ['status' => $oldStatus],
            ['status' => $jadwal->status, 'laporan_patroli' => $jadwal->laporan_patroli],
            $jadwal->rt_number
        );

        return redirect()->route('ronda.index', ['tab' => 'jadwal'])
            ->with('success', "Status pelaksanaan jadwal ronda berhasil diperbarui menjadi {$jadwal->status_label}!");
    }

    /**
     * Delete schedule with confirmation.
     */
    public function destroyJadwal($id)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            return redirect()->route('ronda.index')->with('error', 'Akses ditolak.');
        }

        $jadwal = JadwalRonda::findOrFail($id);
        if ($user->isAdminRt() && $jadwal->rt_number !== $user->rt_number) {
            return redirect()->route('ronda.index')->with('error', 'Akses ditolak.');
        }

        $info = "Jadwal ronda #{$jadwal->id} RT {$jadwal->rt_number} tanggal {$jadwal->tanggal}";
        $jadwal->delete();

        AuditLogger::log(
            'delete',
            'Jadwal Ronda',
            $id,
            "Penghapusan {$info} oleh {$user->name}.",
            null,
            null,
            $jadwal->rt_number
        );

        return redirect()->route('ronda.index', ['tab' => 'jadwal'])
            ->with('success', 'Jadwal ronda berhasil dihapus.');
    }

    /**
     * Store new incident report.
     */
    public function storeLaporanKeamanan(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'rt_number' => 'required|string',
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string',
            'waktu_kejadian' => 'required|date',
            'lokasi' => 'required|string',
            'kronologi' => 'required|string',
            'nama_pelapor' => 'nullable|string|max:100',
            'kontak_pelapor' => 'nullable|string|max:50',
            'cctv_id' => 'nullable|exists:cctvs,id',
            'dokumentasi' => 'nullable|image|max:4096',
        ]);

        $dokumenPath = null;
        if ($request->hasFile('dokumentasi')) {
            $dokumenPath = $request->file('dokumentasi')->store('laporan_keamanan', 'public');
        }

        // Generate unique report code: LAP-YYYYMM-XXXX
        $blnCode = date('Ym');
        $lastReport = LaporanKeamanan::where('nomor_laporan', 'like', "LAP-{$blnCode}-%")->orderBy('id', 'desc')->first();
        $nextSeq = 1;
        if ($lastReport && preg_match('/LAP-\d{6}-(\d+)/', $lastReport->nomor_laporan, $m)) {
            $nextSeq = (int)$m[1] + 1;
        }
        $nomorLaporan = 'LAP-' . $blnCode . '-' . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);

        $namaPelapor = $request->nama_pelapor ?: $user->name;
        $kontakPelapor = $request->kontak_pelapor ?: ($user->warga ? $user->warga->telepon : null);

        $laporan = LaporanKeamanan::create([
            'nomor_laporan' => $nomorLaporan,
            'user_id' => $user->id,
            'nama_pelapor' => $namaPelapor,
            'kontak_pelapor' => $kontakPelapor,
            'rt_number' => $request->rt_number,
            'judul' => $request->judul,
            'kategori' => $request->kategori,
            'waktu_kejadian' => $request->waktu_kejadian,
            'lokasi' => $request->lokasi,
            'cctv_id' => $request->cctv_id,
            'kronologi' => $request->kronologi,
            'dokumentasi' => $dokumenPath,
            'status' => 'baru',
        ]);

        // Record initial timeline step
        LaporanKeamananHistory::create([
            'laporan_keamanan_id' => $laporan->id,
            'user_id' => $user->id,
            'status_sebelumnya' => null,
            'status_baru' => 'baru',
            'keterangan' => "Laporan insiden keamanan diajukan oleh {$namaPelapor}.",
            'petugas' => $user->name,
        ]);

        // Dispatch notifications to RT and RW admins
        $adminUsers = User::where(function ($q) use ($request) {
            $q->where('email', 'like', 'adminrw%')
              ->orWhere('email', 'admin@rt.com')
              ->orWhere(function ($sub) use ($request) {
                  $sub->where('role', 'admin')->where('rt_number', $request->rt_number);
              });
        })->get();

        foreach ($adminUsers as $adm) {
            FinanceNotification::send(
                $adm->id,
                'laporan_keamanan_baru',
                'Laporan Keamanan Baru: ' . $laporan->nomor_laporan,
                "Insiden '{$laporan->judul}' di RT {$laporan->rt_number} baru saja dilaporkan oleh {$namaPelapor}.",
                ['link_url' => route('ronda.index', ['tab' => 'laporan', 'search' => $laporan->nomor_laporan])]
            );
        }

        AuditLogger::log(
            'create',
            'Keamanan & Ketertiban',
            $laporan->id,
            "Laporan kejadian keamanan {$nomorLaporan}: '{$request->judul}' di RT {$request->rt_number} dilaporkan oleh {$namaPelapor}.",
            null,
            $laporan->toArray(),
            $request->rt_number
        );

        return redirect()->route('ronda.index', ['tab' => 'laporan'])
            ->with('success', "Laporan kejadian keamanan {$nomorLaporan} berhasil dibuat dan masuk ke posko keamanan!");
    }

    /**
     * Show incident details (JSON for modal / timeline).
     */
    public function showLaporanKeamanan($id)
    {
        $user = Auth::user();
        $laporan = LaporanKeamanan::with(['user', 'cctv', 'histories.user', 'recordings'])->findOrFail($id);

        $canEdit = false;
        if ($user) {
            if ($user->isAdminRw()) {
                $canEdit = true;
            } elseif ($user->isAdminRt()) {
                $canEdit = ($laporan->rt_number === $user->rt_number);
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $laporan->id,
                'can_edit' => $canEdit,
                'nomor_laporan' => $laporan->nomor_laporan,
                'judul' => $laporan->judul,
                'kategori' => $laporan->kategori,
                'rt_number' => $laporan->rt_number,
                'waktu_kejadian_formatted' => Carbon::parse($laporan->waktu_kejadian)->translatedFormat('l, d F Y - H:i') . ' WIB',
                'lokasi' => $laporan->lokasi,
                'nama_pelapor' => $laporan->nama_pelapor ?: ($laporan->user ? $laporan->user->name : '-'),
                'kontak_pelapor' => $laporan->kontak_pelapor ?: '-',
                'kronologi' => $laporan->kronologi,
                'dokumentasi_url' => $laporan->dokumentasi ? asset('storage/' . $laporan->dokumentasi) : null,
                'dokumentasi_penanganan_url' => $laporan->dokumentasi_penanganan ? asset('storage/' . $laporan->dokumentasi_penanganan) : null,
                'status' => $laporan->status,
                'status_label' => $laporan->status_label,
                'status_badge' => $laporan->status_badge,
                'petugas_penanganan' => $laporan->petugas_penanganan ?: 'Belum ditugaskan',
                'tindak_lanjut' => $laporan->tindak_lanjut ?: 'Belum ada catatan tindak lanjut.',
                'selesai_at' => $laporan->selesai_at ? Carbon::parse($laporan->selesai_at)->translatedFormat('d M Y H:i') : null,
                'cctv' => $laporan->cctv ? [
                    'id' => $laporan->cctv->id,
                    'kode_kamera' => $laporan->cctv->kode_kamera,
                    'nama_kamera' => $laporan->cctv->nama_kamera,
                    'lokasi' => $laporan->cctv->lokasi,
                    'status' => $laporan->cctv->status,
                ] : null,
                'histories' => $laporan->histories->map(function ($h) {
                    return [
                        'id' => $h->id,
                        'status_baru' => $h->status_baru,
                        'keterangan' => $h->keterangan,
                        'petugas' => $h->petugas ?: ($h->user ? $h->user->name : '-'),
                        'time_formatted' => $h->created_at->translatedFormat('d M Y, H:i') . ' WIB',
                    ];
                }),
                'recordings_count' => $laporan->recordings->count(),
            ]
        ]);
    }

    /**
     * Update incident status, assigned officer, and follow-up notes.
     */
    public function updateLaporanKeamanan(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            return redirect()->route('ronda.index')->with('error', 'Akses ditolak.');
        }

        $laporan = LaporanKeamanan::findOrFail($id);

        if ($user->isAdminRt() && $laporan->rt_number !== $user->rt_number) {
            abort(403, 'Akses ditolak: Anda hanya dapat menangani laporan kejadian di wilayah RT ' . $user->rt_number . '. Laporan tingkat RW atau RT lain hanya dapat ditindaklanjuti oleh pengurus yang berwenang.');
        }

        $request->validate([
            'status' => 'required|in:baru,menunggu,diproses,selesai',
            'tindak_lanjut' => 'nullable|string',
            'petugas_penanganan' => 'nullable|string',
            'cctv_id' => 'nullable|exists:cctvs,id',
            'dokumentasi_penanganan' => 'nullable|image|max:4096',
        ]);

        $docPath = $laporan->dokumentasi_penanganan;
        if ($request->hasFile('dokumentasi_penanganan')) {
            $docPath = $request->file('dokumentasi_penanganan')->store('laporan_keamanan', 'public');
        }

        $oldStatus = $laporan->status;
        $petugas = $request->petugas_penanganan ?: $user->name;

        $laporan->status = $request->status;
        $laporan->tindak_lanjut = $request->tindak_lanjut ?: $laporan->tindak_lanjut;
        $laporan->petugas_penanganan = $petugas;
        $laporan->dokumentasi_penanganan = $docPath;
        if ($request->filled('cctv_id')) {
            $laporan->cctv_id = $request->cctv_id;
        }
        if ($request->status === 'selesai' && !$laporan->selesai_at) {
            $laporan->selesai_at = now();
        }
        $laporan->save();

        // Record history step if status changed or notes added
        LaporanKeamananHistory::create([
            'laporan_keamanan_id' => $laporan->id,
            'user_id' => $user->id,
            'status_sebelumnya' => $oldStatus,
            'status_baru' => $request->status,
            'keterangan' => $request->tindak_lanjut ?: "Status diperbarui menjadi {$laporan->status_label}.",
            'petugas' => $petugas,
        ]);

        // Send notification to the citizen reporter if registered user
        if ($laporan->user_id && $laporan->user_id !== $user->id) {
            FinanceNotification::send(
                $laporan->user_id,
                'laporan_keamanan_update',
                "Status Laporan Keamanan: {$laporan->nomor_laporan}",
                "Laporan Anda '{$laporan->judul}' kini berstatus: {$laporan->status_label}. Petugas: {$petugas}.",
                ['link_url' => route('ronda.index', ['tab' => 'laporan', 'search' => $laporan->nomor_laporan])]
            );
        }

        AuditLogger::log(
            'update',
            'Keamanan & Ketertiban',
            $laporan->id,
            "Status kejadian keamanan {$laporan->nomor_laporan} diperbarui menjadi " . strtoupper($request->status) . " oleh {$user->name}. Tindak lanjut: " . ($request->tindak_lanjut ?: '-'),
            ['status' => $oldStatus],
            ['status' => $laporan->status, 'tindak_lanjut' => $laporan->tindak_lanjut],
            $laporan->rt_number
        );

        return redirect()->route('ronda.index', ['tab' => 'laporan'])
            ->with('success', "Laporan {$laporan->nomor_laporan} berhasil diperbarui menjadi status {$laporan->status_label}!");
    }

    /**
     * Link incident report to a CCTV camera.
     */
    public function linkCctv(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            return redirect()->route('ronda.index')->with('error', 'Akses ditolak.');
        }

        $laporan = LaporanKeamanan::findOrFail($id);

        if ($user->isAdminRt() && $laporan->rt_number !== $user->rt_number) {
            abort(403, 'Akses ditolak: Anda hanya dapat menautkan CCTV pada laporan kejadian di wilayah RT ' . $user->rt_number . '.');
        }

        $request->validate([
            'cctv_id' => 'required|exists:cctvs,id',
        ]);

        $cctv = Cctv::findOrFail($request->cctv_id);
        $laporan->update(['cctv_id' => $cctv->id]);

        AuditLogger::log(
            'update',
            'Keamanan & Ketertiban',
            $laporan->id,
            "Laporan kejadian {$laporan->nomor_laporan} ditautkan ke kamera CCTV {$cctv->kode_kamera} ({$cctv->nama_kamera}) oleh {$user->name}.",
            null,
            ['cctv_id' => $cctv->id],
            $laporan->rt_number
        );

        return redirect()->route('ronda.index', ['tab' => 'laporan'])
            ->with('success', "Laporan {$laporan->nomor_laporan} berhasil ditautkan ke kamera {$cctv->kode_kamera}!");
    }

    /**
     * Store new CCTV camera (Admin RW only).
     */
    public function storeCctv(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdminRw()) {
            return redirect()->route('ronda.index')->with('error', 'Hanya Admin RW yang berwenang mengelola titik CCTV.');
        }

        $request->validate([
            'kode_kamera' => 'required|string|max:50|unique:cctvs,kode_kamera',
            'nama_kamera' => 'required|string|max:255',
            'rt_number' => 'required|string',
            'lokasi' => 'required|string|max:255',
            'area_pantau' => 'required|string',
            'status' => 'required|in:online,offline,gangguan',
            'catatan_gangguan' => 'nullable|string',
            'tanggal_pemasangan' => 'nullable|date',
            'ip_address' => 'nullable|string|max:50',
            'stream_url' => 'nullable|string|max:255',
            'tipe_kamera' => 'nullable|string|max:100',
            'kapasitas_penyimpanan' => 'nullable|string|max:100',
            'maps_url' => 'nullable|string|max:255',
        ]);

        $cctv = Cctv::create([
            'kode_kamera' => $request->kode_kamera,
            'nama_kamera' => $request->nama_kamera,
            'rt_number' => $request->rt_number,
            'lokasi' => $request->lokasi,
            'area_pantau' => $request->area_pantau,
            'status' => $request->status,
            'catatan_gangguan' => $request->catatan_gangguan,
            'tanggal_pemasangan' => $request->tanggal_pemasangan ?: date('Y-m-d'),
            'ip_address' => $request->ip_address,
            'stream_url' => $request->stream_url,
            'tipe_kamera' => $request->tipe_kamera ?: 'Hikvision Bullet 2MP Weatherproof',
            'kapasitas_penyimpanan' => $request->kapasitas_penyimpanan ?: '2 TB NVR (Retensi 30 Hari)',
            'maps_url' => $request->maps_url,
            'last_online_at' => $request->status === 'online' ? now() : null,
        ]);

        AuditLogger::log(
            'create',
            'CCTV',
            $cctv->id,
            "Penambahan titik kamera {$cctv->kode_kamera} '{$cctv->nama_kamera}' di RT {$cctv->rt_number} oleh {$user->name}.",
            null,
            $cctv->toArray(),
            $cctv->rt_number
        );

        return redirect()->route('ronda.index', ['tab' => 'cctv'])
            ->with('success', "Titik kamera {$cctv->kode_kamera} berhasil ditambahkan!");
    }

    /**
     * Show CCTV details & recordings (JSON for modal with audit log).
     */
    public function showCctv($id)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            return response()->json(['success' => false, 'message' => 'Akses CCTV dibatasi untuk pengurus keamanan.'], 403);
        }

        $cctv = Cctv::with(['recordings'])->findOrFail($id);

        // Security Audit Log: Log whenever an admin accesses camera details/recordings
        AuditLogger::log(
            'view',
            'CCTV',
            $cctv->id,
            "Pengurus {$user->name} ({$user->role}) mengakses detail & feed kamera {$cctv->kode_kamera} ({$cctv->nama_kamera}).",
            null,
            null,
            $cctv->rt_number
        );

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $cctv->id,
                'kode_kamera' => $cctv->kode_kamera,
                'nama_kamera' => $cctv->nama_kamera,
                'rt_number' => $cctv->rt_number,
                'lokasi' => $cctv->lokasi,
                'area_pantau' => $cctv->area_pantau,
                'status' => $cctv->status,
                'status_label' => $cctv->status_label,
                'status_badge' => $cctv->status_badge,
                'catatan_gangguan' => $cctv->catatan_gangguan,
                'tanggal_pemasangan' => $cctv->tanggal_pemasangan ? $cctv->tanggal_pemasangan->translatedFormat('d F Y') : '-',
                'ip_address' => $cctv->ip_address ?: '192.168.10.x',
                'stream_url' => $cctv->stream_url,
                'tipe_kamera' => $cctv->tipe_kamera ?: 'Outdoor IP Camera IR',
                'kapasitas_penyimpanan' => $cctv->kapasitas_penyimpanan ?: '2 TB NVR (30 Hari)',
                'maps_url' => $cctv->maps_url ?: "https://maps.google.com/?q=-6.2630,106.8930",
                'last_online_formatted' => $cctv->last_online_at ? $cctv->last_online_at->diffForHumans() : 'Tidak diketahui',
                'recordings' => $cctv->recordings->map(function ($r) {
                    return [
                        'id' => $r->id,
                        'judul_rekaman' => $r->judul_rekaman,
                        'waktu_mulai_formatted' => $r->waktu_mulai->translatedFormat('d M Y, H:i'),
                        'waktu_selesai_formatted' => $r->waktu_selesai->format('H:i') . ' WIB',
                        'ukuran_file' => $r->ukuran_file,
                        'is_locked' => (bool)$r->is_locked,
                        'catatan' => $r->catatan ?: '-',
                    ];
                }),
            ]
        ]);
    }

    /**
     * Update CCTV information (Admin RW only).
     */
    public function updateCctv(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isAdminRw()) {
            return redirect()->route('ronda.index')->with('error', 'Hanya Admin RW yang berwenang mengubah titik CCTV.');
        }

        $cctv = Cctv::findOrFail($id);

        $request->validate([
            'nama_kamera' => 'required|string|max:255',
            'rt_number' => 'required|string',
            'lokasi' => 'required|string|max:255',
            'area_pantau' => 'required|string',
            'ip_address' => 'nullable|string|max:50',
            'stream_url' => 'nullable|string|max:255',
            'tipe_kamera' => 'nullable|string|max:100',
            'kapasitas_penyimpanan' => 'nullable|string|max:100',
            'maps_url' => 'nullable|string|max:255',
        ]);

        $oldData = $cctv->toArray();

        $cctv->update([
            'nama_kamera' => $request->nama_kamera,
            'rt_number' => $request->rt_number,
            'lokasi' => $request->lokasi,
            'area_pantau' => $request->area_pantau,
            'ip_address' => $request->ip_address,
            'stream_url' => $request->stream_url,
            'tipe_kamera' => $request->tipe_kamera,
            'kapasitas_penyimpanan' => $request->kapasitas_penyimpanan,
            'maps_url' => $request->maps_url,
        ]);

        AuditLogger::log(
            'update',
            'CCTV',
            $cctv->id,
            "Pembaruan informasi kamera {$cctv->kode_kamera} oleh {$user->name}.",
            $oldData,
            $cctv->toArray(),
            $cctv->rt_number
        );

        return redirect()->route('ronda.index', ['tab' => 'cctv'])
            ->with('success', "Informasi kamera {$cctv->kode_kamera} berhasil diperbarui!");
    }

    /**
     * Update CCTV camera status (online, offline, gangguan).
     */
    public function updateStatusCctv(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            return redirect()->route('ronda.index')->with('error', 'Akses ditolak.');
        }

        $cctv = Cctv::findOrFail($id);

        if ($user->isAdminRt() && $cctv->rt_number !== $user->rt_number) {
            abort(403, 'Akses ditolak: Anda hanya dapat memperbarui status kamera CCTV di wilayah RT Anda.');
        }

        $request->validate([
            'status' => 'required|in:online,offline,gangguan',
            'catatan_gangguan' => 'nullable|string',
        ]);

        $oldStatus = $cctv->status;
        $cctv->update([
            'status' => $request->status,
            'catatan_gangguan' => $request->status !== 'online' ? $request->catatan_gangguan : null,
            'last_online_at' => $request->status === 'online' ? now() : $cctv->last_online_at,
        ]);

        // If status changed to gangguan, notify Admin RW
        if ($request->status === 'gangguan') {
            $rwAdmin = User::where('email', 'adminrw@rt.com')->first() ?: User::first();
            if ($rwAdmin && $rwAdmin->id !== $user->id) {
                FinanceNotification::send(
                    $rwAdmin->id,
                    'cctv_gangguan',
                    "Peringatan Gangguan CCTV: {$cctv->kode_kamera}",
                    "Kamera '{$cctv->nama_kamera}' di RT {$cctv->rt_number} mengalami gangguan: " . ($request->catatan_gangguan ?: 'Koneksi terputus.'),
                    ['link_url' => route('ronda.index', ['tab' => 'cctv'])]
                );
            }
        }

        AuditLogger::log(
            'update',
            'CCTV',
            $cctv->id,
            "Status kamera {$cctv->kode_kamera} diubah dari {$oldStatus} menjadi " . strtoupper($request->status) . " oleh {$user->name}. Catatan: " . ($request->catatan_gangguan ?: '-'),
            ['status' => $oldStatus],
            ['status' => $cctv->status, 'catatan_gangguan' => $cctv->catatan_gangguan],
            $cctv->rt_number
        );

        return redirect()->route('ronda.index', ['tab' => 'cctv'])
            ->with('success', "Status kamera {$cctv->kode_kamera} berhasil diperbarui menjadi {$cctv->status_label}!");
    }

    /**
     * Delete CCTV camera (Admin RW only).
     */
    public function destroyCctv($id)
    {
        $user = Auth::user();
        if (!$user->isAdminRw()) {
            return redirect()->route('ronda.index')->with('error', 'Hanya Admin RW yang berwenang menghapus titik CCTV.');
        }

        $cctv = Cctv::findOrFail($id);
        $kode = $cctv->kode_kamera;
        $cctv->delete();

        AuditLogger::log(
            'delete',
            'CCTV',
            $id,
            "Penghapusan titik kamera {$kode} oleh {$user->name}.",
            null,
            null,
            $cctv->rt_number
        );

        return redirect()->route('ronda.index', ['tab' => 'cctv'])
            ->with('success', "Titik kamera {$kode} berhasil dihapus dari sistem.");
    }

    /**
     * Lock recording as official incident evidence.
     */
    public function lockRecording(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            return redirect()->route('ronda.index')->with('error', 'Akses ditolak.');
        }

        $rec = CctvRecording::findOrFail($id);
        $rec->is_locked = !$rec->is_locked;
        $rec->save();

        $action = $rec->is_locked ? 'dikunci sebagai barang bukti (anti-purge)' : 'dibuka dari kunci bukti';

        AuditLogger::log(
            'update',
            'CCTV',
            $rec->cctv_id,
            "Rekaman #{$rec->id} '{$rec->judul_rekaman}' {$action} oleh {$user->name}.",
            ['is_locked' => !$rec->is_locked],
            ['is_locked' => $rec->is_locked],
            $rec->cctv ? $rec->cctv->rt_number : null
        );

        return redirect()->back()->with('success', "Rekaman CCTV berhasil {$action}!");
    }
}
