<?php

namespace App\Http\Controllers;

use App\Models\AgendaRapat;
use App\Models\BankSampah;
use App\Models\LaporanLingkungan;
use App\Models\LaporanLingkunganHistory;
use App\Models\LaporanLingkunganPeriode;
use App\Models\Pengaduan;
use App\Models\User;
use App\Services\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LaporanLingkunganController extends Controller
{
    /**
     * Display the main environment reporting page with 4 tabs.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->isWarga()) {
            abort(403, 'Akses ditolak. Fitur Laporan Lingkungan hanya untuk Pengurus RT dan RW.');
        }

        $isAdminRw = $user->isAdminRw();
        $isAdminRt = $user->isAdminRt();

        // 1. Current or selected period & filters
        $activeTab = $request->query('tab', 'ringkasan');
        $selectedBulan = $request->query('periode_bulan', date('m'));
        $selectedTahun = $request->query('periode_tahun', date('Y'));
        $selectedBulan = str_pad($selectedBulan, 2, '0', STR_PAD_LEFT);

        $selectedRt = $isAdminRt ? $user->rt_number : $request->query('rt_number');
        $selectedStatus = $request->query('status');
        $search = $request->query('search');

        $allRts = ['006', '007', '008', '009', '010'];
        $rtList = $isAdminRt ? [$user->rt_number] : $allRts;

        // 2. Periode & Deadline Setting
        $periodeSetting = LaporanLingkunganPeriode::where('periode_bulan', $selectedBulan)
            ->where('periode_tahun', $selectedTahun)
            ->first();

        if (!$periodeSetting) {
            $defaultStartDate = Carbon::create((int)$selectedTahun, (int)$selectedBulan, 1)->toDateString();
            $defaultDeadline = Carbon::create((int)$selectedTahun, (int)$selectedBulan, 10)->toDateString();
            $periodeSetting = LaporanLingkunganPeriode::create([
                'periode_bulan' => $selectedBulan,
                'periode_tahun' => $selectedTahun,
                'tanggal_mulai' => $defaultStartDate,
                'batas_waktu' => $defaultDeadline,
                'status' => 'aktif',
                'catatan_instruksi_rw' => 'Mohon lakukan pemantauan berkala dan utamakan kebersihan saluran drainase serta optimalisasi pilah sampah warga.',
                'created_by' => $user->id,
            ]);
        }

        $now = Carbon::now();
        $batasWaktuDate = Carbon::parse($periodeSetting->batas_waktu)->endOfDay();
        $isPastDeadline = $now->gt($batasWaktuDate);

        // 3. Matrix Status Pengiriman 5 RT pada Periode Terpilih
        $rtMatrix = [];
        $rtSubmittedCount = 0;
        $rtMenungguCount = 0;
        $rtDiterimaCount = 0;
        $rtRevisiCount = 0;
        $rtTerlambatCount = 0;

        foreach ($allRts as $rtNum) {
            $report = LaporanLingkungan::with(['user', 'pemeriksa'])
                ->where('rt_number', $rtNum)
                ->where('periode_bulan', $selectedBulan)
                ->where('periode_tahun', $selectedTahun)
                ->first();

            $adminRtUser = User::where('role', 'admin')->where('rt_number', $rtNum)->first();

            $displayStatus = 'belum_mengirim';
            $statusLabel = 'Belum Mengirim';
            $badgeClass = 'bg-secondary';

            if ($report) {
                if ($report->status === 'draft') {
                    $displayStatus = $isPastDeadline ? 'draft_terlambat' : 'draft';
                    $statusLabel = $isPastDeadline ? 'Draft (Lewat Batas)' : 'Draft Disimpan';
                    $badgeClass = 'bg-warning text-dark';
                } elseif (in_array($report->status, ['menunggu_pemeriksaan', 'menunggu', 'tercatat'])) {
                    $displayStatus = 'menunggu_pemeriksaan';
                    $statusLabel = 'Menunggu Evaluasi RW';
                    $badgeClass = 'bg-info text-dark';
                    $rtSubmittedCount++;
                    $rtMenungguCount++;
                } elseif ($report->status === 'diterima') {
                    $displayStatus = 'diterima';
                    $statusLabel = 'Diterima RW';
                    $badgeClass = 'bg-success';
                    $rtSubmittedCount++;
                    $rtDiterimaCount++;
                } elseif ($report->status === 'perlu_revisi') {
                    $displayStatus = 'perlu_revisi';
                    $statusLabel = 'Perlu Revisi RT';
                    $badgeClass = 'bg-danger';
                    $rtSubmittedCount++;
                    $rtRevisiCount++;
                }
            } else {
                if ($isPastDeadline) {
                    $displayStatus = 'terlambat';
                    $statusLabel = 'Terlambat Mengirim';
                    $badgeClass = 'bg-danger text-white';
                    $rtTerlambatCount++;
                } else {
                    $displayStatus = 'belum_mengirim';
                    $statusLabel = 'Belum Mengirim';
                    $badgeClass = 'bg-light text-muted border';
                }
            }

            $rtMatrix[$rtNum] = [
                'rt_number' => $rtNum,
                'admin_user' => $adminRtUser,
                'report' => $report,
                'display_status' => $displayStatus,
                'status_label' => $statusLabel,
                'badge_class' => $badgeClass,
                'is_past_deadline' => $isPastDeadline,
            ];
        }

        $totalRt = count($allRts);
        $rtBelumKirimCount = $totalRt - $rtSubmittedCount;

        // 4. Query Laporan Keseluruhan (untuk Tab 2: Laporan Per RT)
        $laporanQuery = LaporanLingkungan::with(['user', 'pemeriksa'])
            ->orderBy('periode_tahun', 'desc')
            ->orderBy('periode_bulan', 'desc')
            ->orderBy('rt_number', 'asc');

        if ($isAdminRt) {
            $laporanQuery->where('rt_number', $user->rt_number);
        } elseif (!empty($selectedRt)) {
            $laporanQuery->where('rt_number', $selectedRt);
        }

        if ($request->filled('periode_bulan')) {
            $laporanQuery->where('periode_bulan', $selectedBulan);
        }
        if ($request->filled('periode_tahun')) {
            $laporanQuery->where('periode_tahun', $selectedTahun);
        }

        if (!empty($selectedStatus)) {
            if ($selectedStatus === 'menunggu') {
                $laporanQuery->whereIn('status', ['menunggu_pemeriksaan', 'menunggu', 'tercatat']);
            } else {
                $laporanQuery->where('status', $selectedStatus);
            }
        }

        if (!empty($search)) {
            $laporanQuery->where(function ($q) use ($search) {
                $q->where('kegiatan_kerja_bakti', 'like', "%{$search}%")
                  ->orWhere('program_penghijauan', 'like', "%{$search}%")
                  ->orWhere('kendala_lingkungan', 'like', "%{$search}%")
                  ->orWhere('area_terjaga_kebersihan', 'like', "%{$search}%")
                  ->orWhere('rt_number', 'like', "%{$search}%");
            });
        }

        $laporans = $laporanQuery->paginate(12)->withQueryString();

        // 5. Query Khusus Evaluasi RW & Tindak Lanjut (Tab 3)
        $evaluasiPendingQuery = LaporanLingkungan::with(['user', 'histories.user'])
            ->whereIn('status', ['menunggu_pemeriksaan', 'menunggu', 'tercatat'])
            ->orderBy('updated_at', 'desc');

        $evaluasiRevisiQuery = LaporanLingkungan::with(['user', 'histories.user'])
            ->where('status', 'perlu_revisi')
            ->orderBy('updated_at', 'desc');

        if ($isAdminRt) {
            $evaluasiPendingQuery->where('rt_number', $user->rt_number);
            $evaluasiRevisiQuery->where('rt_number', $user->rt_number);
        }

        $pendingEvaluasiList = $evaluasiPendingQuery->get();
        $revisiList = $evaluasiRevisiQuery->get();

        // Recent evaluation logs
        $recentHistoriesQuery = LaporanLingkunganHistory::with(['laporan', 'user'])
            ->orderBy('created_at', 'desc');
        if ($isAdminRt) {
            $recentHistoriesQuery->whereHas('laporan', function ($q) use ($user) {
                $q->where('rt_number', $user->rt_number);
            });
        }
        $recentHistories = $recentHistoriesQuery->limit(15)->get();

        // 6. Query Riwayat & Arsip (Tab 4)
        $riwayatQuery = LaporanLingkungan::with(['user', 'pemeriksa'])
            ->whereIn('status', ['diterima', 'perlu_revisi'])
            ->orderBy('periode_tahun', 'desc')
            ->orderBy('periode_bulan', 'desc');

        if ($isAdminRt) {
            $riwayatQuery->where('rt_number', $user->rt_number);
        } elseif (!empty($selectedRt)) {
            $riwayatQuery->where('rt_number', $selectedRt);
        }

        $riwayatLaporans = $riwayatQuery->paginate(15, ['*'], 'riwayat_page')->withQueryString();

        // 7. Grafik & Visualisasi Statistik Lingkungan Aktual (Jan - Des 2026)
        $chartKebersihanData = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            'sangat_bersih' => array_fill(0, 12, 0),
            'cukup_bersih' => array_fill(0, 12, 0),
            'perlu_kerja_bakti' => array_fill(0, 12, 0),
        ];

        $chartDrainaseData = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            'lancar' => array_fill(0, 12, 0),
            'sedimen' => array_fill(0, 12, 0),
            'tersumbat' => array_fill(0, 12, 0),
        ];

        $chartLaporanBulanan = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            'diterima' => array_fill(0, 12, 0),
            'proses' => array_fill(0, 12, 0),
            'total' => array_fill(0, 12, 0),
        ];

        $chartPerbandinganRt = [
            'labels' => ['RT 006', 'RT 007', 'RT 008', 'RT 009', 'RT 010'],
            'kebersihan_score' => [0, 0, 0, 0, 0],
            'drainase_score' => [0, 0, 0, 0, 0],
            'total_laporan' => [0, 0, 0, 0, 0],
        ];

        // Populate charts from DB records
        $allYearReports = LaporanLingkungan::where('periode_tahun', $selectedTahun)->get();

        foreach ($allYearReports as $rep) {
            $mIdx = (int)$rep->periode_bulan - 1;
            if ($mIdx >= 0 && $mIdx < 12) {
                // Kebersihan
                if (str_contains($rep->kondisi_kebersihan, 'Sangat Bersih')) {
                    $chartKebersihanData['sangat_bersih'][$mIdx]++;
                } elseif (str_contains($rep->kondisi_kebersihan, 'Cukup Bersih')) {
                    $chartKebersihanData['cukup_bersih'][$mIdx]++;
                } else {
                    $chartKebersihanData['perlu_kerja_bakti'][$mIdx]++;
                }

                // Drainase
                if (str_contains($rep->kondisi_drainase, 'Lancar')) {
                    $chartDrainaseData['lancar'][$mIdx]++;
                } elseif (str_contains($rep->kondisi_drainase, 'Sedimen')) {
                    $chartDrainaseData['sedimen'][$mIdx]++;
                } else {
                    $chartDrainaseData['tersumbat'][$mIdx]++;
                }

                // Status bulanan
                $chartLaporanBulanan['total'][$mIdx]++;
                if ($rep->status === 'diterima') {
                    $chartLaporanBulanan['diterima'][$mIdx]++;
                } else {
                    $chartLaporanBulanan['proses'][$mIdx]++;
                }
            }

            // Perbandingan RT
            $rtIdx = array_search($rep->rt_number, $allRts);
            if ($rtIdx !== false) {
                $chartPerbandinganRt['total_laporan'][$rtIdx]++;
                $chartPerbandinganRt['kebersihan_score'][$rtIdx] += str_contains($rep->kondisi_kebersihan, 'Sangat') ? 95 : 75;
                $chartPerbandinganRt['drainase_score'][$rtIdx] += str_contains($rep->kondisi_drainase, 'Lancar') ? 90 : 70;
            }
        }

        // Calculate average score per RT
        for ($i = 0; $i < 5; $i++) {
            $cnt = $chartPerbandinganRt['total_laporan'][$i];
            if ($cnt > 0) {
                $chartPerbandinganRt['kebersihan_score'][$i] = round($chartPerbandinganRt['kebersihan_score'][$i] / $cnt);
                $chartPerbandinganRt['drainase_score'][$i] = round($chartPerbandinganRt['drainase_score'][$i] / $cnt);
            }
        }

        // 8. Integrasi Cepat (Pengaduan, Bank Sampah, Agenda)
        $pengaduanLingkungan = Pengaduan::where(function ($q) {
            $q->where('category', 'like', '%Lingkungan%')
              ->orWhere('category', 'like', '%Kebersihan%')
              ->orWhere('category', 'like', '%Sampah%')
              ->orWhere('category', 'like', '%Drainase%');
        })
        ->whereIn('status', ['pending', 'diproses'])
        ->when($isAdminRt, function ($q) use ($user) {
            $q->where('rt_number', $user->rt_number);
        })
        ->limit(6)
        ->get();

        $bankSampahMonthKg = BankSampah::whereMonth('tanggal_setor', $selectedBulan)
            ->whereYear('tanggal_setor', $selectedTahun)
            ->when($isAdminRt, function ($q) use ($user) {
                $q->where('rt_number', $user->rt_number);
            })
            ->sum('berat_kg');

        $agendaLingkungan = AgendaRapat::whereMonth('tanggal_waktu', $selectedBulan)
            ->whereYear('tanggal_waktu', $selectedTahun)
            ->where(function ($q) {
                $q->where('judul', 'like', '%Kerja Bakti%')
                  ->orWhere('judul', 'like', '%Lingkungan%')
                  ->orWhere('judul', 'like', '%Bersih%')
                  ->orWhere('deskripsi', 'like', '%lingkungan%');
            })
            ->limit(4)
            ->get();

        // 9. Laporan RT User Aktif untuk Bulan Berjalan (Dashboard Admin RT)
        $currentRtReport = null;
        if ($isAdminRt) {
            $currentRtReport = LaporanLingkungan::where('rt_number', $user->rt_number)
                ->where('periode_bulan', $selectedBulan)
                ->where('periode_tahun', $selectedTahun)
                ->first();
        }

        return view('lingkungan.index', compact(
            'isAdminRw',
            'isAdminRt',
            'activeTab',
            'selectedBulan',
            'selectedTahun',
            'selectedRt',
            'selectedStatus',
            'search',
            'allRts',
            'rtList',
            'periodeSetting',
            'isPastDeadline',
            'rtMatrix',
            'totalRt',
            'rtSubmittedCount',
            'rtBelumKirimCount',
            'rtMenungguCount',
            'rtDiterimaCount',
            'rtRevisiCount',
            'rtTerlambatCount',
            'laporans',
            'pendingEvaluasiList',
            'revisiList',
            'recentHistories',
            'riwayatLaporans',
            'chartKebersihanData',
            'chartDrainaseData',
            'chartLaporanBulanan',
            'chartPerbandinganRt',
            'pengaduanLingkungan',
            'bankSampahMonthKg',
            'agendaLingkungan',
            'currentRtReport'
        ));
    }

    /**
     * Show form to create new monthly environment report.
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk pengurus lingkungan.');
        }

        $rtNumber = $user->isAdminRt() ? $user->rt_number : $request->query('rt_number', '006');
        $periodeBulan = str_pad($request->query('periode_bulan', date('m')), 2, '0', STR_PAD_LEFT);
        $periodeTahun = $request->query('periode_tahun', date('Y'));

        // Check if report already exists for this RT & period
        $existing = LaporanLingkungan::where('rt_number', $rtNumber)
            ->where('periode_bulan', $periodeBulan)
            ->where('periode_tahun', $periodeTahun)
            ->first();

        if ($existing) {
            if (in_array($existing->status, ['draft', 'perlu_revisi'])) {
                return redirect()->route('lingkungan.edit', $existing->id)
                    ->with('info', "Laporan RT {$rtNumber} periode {$periodeBulan}/{$periodeTahun} sudah ada ({$existing->status_label}). Silakan lanjutkan pengisian.");
            }
            return redirect()->route('lingkungan.show', $existing->id)
                ->with('info', "Laporan RT {$rtNumber} periode {$periodeBulan}/{$periodeTahun} telah dikirim ({$existing->status_label}).");
        }

        // Integration references
        $bankSampahKg = BankSampah::where('rt_number', $rtNumber)
            ->whereMonth('tanggal_setor', $periodeBulan)
            ->whereYear('tanggal_setor', $periodeTahun)
            ->sum('berat_kg');

        $pengaduanTerkait = Pengaduan::where('rt_number', $rtNumber)
            ->where(function ($q) {
                $q->where('category', 'like', '%Lingkungan%')
                  ->orWhere('category', 'like', '%Sampah%')
                  ->orWhere('category', 'like', '%Drainase%');
            })
            ->whereIn('status', ['pending', 'diproses'])
            ->get();

        $agendaTerkait = AgendaRapat::whereMonth('tanggal_waktu', $periodeBulan)
            ->whereYear('tanggal_waktu', $periodeTahun)
            ->get();

        $allRts = ['006', '007', '008', '009', '010'];

        return view('lingkungan.create', compact(
            'rtNumber',
            'periodeBulan',
            'periodeTahun',
            'allRts',
            'bankSampahKg',
            'pengaduanTerkait',
            'agendaTerkait'
        ));
    }

    /**
     * Store new report (as Draft or Submitted to RW).
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk pengurus lingkungan.');
        }

        $rtNumber = $user->isAdminRt() ? $user->rt_number : $request->rt_number;

        $request->validate([
            'rt_number' => 'required|string',
            'periode_bulan' => 'required|string',
            'periode_tahun' => 'required|string',
            'kondisi_kebersihan' => 'required|string',
            'kondisi_drainase' => 'required|string',
            'foto_dokumentasi' => 'nullable|image|max:5120',
            'foto_sebelum' => 'nullable|image|max:5120',
            'foto_sesudah' => 'nullable|image|max:5120',
            'action_type' => 'required|in:draft,submit',
        ]);

        $periodeBulan = str_pad($request->periode_bulan, 2, '0', STR_PAD_LEFT);
        $periodeTahun = $request->periode_tahun;

        // Prevent duplicate report
        $existing = LaporanLingkungan::where('rt_number', $rtNumber)
            ->where('periode_bulan', $periodeBulan)
            ->where('periode_tahun', $periodeTahun)
            ->first();

        if ($existing && !in_array($existing->status, ['draft', 'perlu_revisi'])) {
            return redirect()->route('lingkungan.show', $existing->id)
                ->with('warning', 'Laporan untuk RT dan periode ini sudah dikirimkan sebelumnya.');
        }

        // File uploads
        $fotoDokumentasi = $existing ? $existing->foto_dokumentasi : null;
        $fotoSebelum = $existing ? $existing->foto_sebelum : null;
        $fotoSesudah = $existing ? $existing->foto_sesudah : null;

        if ($request->hasFile('foto_dokumentasi')) {
            $fotoDokumentasi = $request->file('foto_dokumentasi')->store('lingkungan_photos', 'public');
        }
        if ($request->hasFile('foto_sebelum')) {
            $fotoSebelum = $request->file('foto_sebelum')->store('lingkungan_photos', 'public');
        }
        if ($request->hasFile('foto_sesudah')) {
            $fotoSesudah = $request->file('foto_sesudah')->store('lingkungan_photos', 'public');
        }

        // Check deadline
        $periode = LaporanLingkunganPeriode::where('periode_bulan', $periodeBulan)
            ->where('periode_tahun', $periodeTahun)
            ->first();
        $isTerlambat = false;
        if ($periode) {
            $deadline = Carbon::parse($periode->batas_waktu)->endOfDay();
            if (Carbon::now()->gt($deadline)) {
                $isTerlambat = true;
            }
        }

        $isSubmit = $request->action_type === 'submit';
        $status = $isSubmit ? 'menunggu_pemeriksaan' : 'draft';

        $data = [
            'rt_number' => $rtNumber,
            'user_id' => $user->id,
            'periode_bulan' => $periodeBulan,
            'periode_tahun' => $periodeTahun,
            'tanggal_pengiriman' => $isSubmit ? Carbon::now() : null,

            // Kebersihan
            'kondisi_kebersihan' => $request->kondisi_kebersihan,
            'area_terjaga_kebersihan' => $request->area_terjaga_kebersihan,
            'area_perlu_perhatian_kebersihan' => $request->area_perlu_perhatian_kebersihan,
            'masalah_sampah' => $request->masalah_sampah,
            'keterangan_kebersihan' => $request->keterangan_kebersihan,

            // Drainase
            'kondisi_drainase' => $request->kondisi_drainase,
            'kondisi_aliran_air' => $request->kondisi_aliran_air,
            'titik_rawan_genangan' => $request->titik_rawan_genangan,
            'saluran_butuh_pembersihan' => $request->saluran_butuh_pembersihan,
            'keterangan_drainase' => $request->keterangan_drainase,

            // Penghijauan
            'program_penghijauan' => $request->program_penghijauan,
            'kondisi_penghijauan' => $request->kondisi_penghijauan,
            'ketersediaan_ruang_terbuka' => $request->ketersediaan_ruang_terbuka,
            'kegiatan_perawatan_tanaman' => $request->kegiatan_perawatan_tanaman,
            'kendala_penghijauan' => $request->kendala_penghijauan,

            // Program & Kegiatan
            'kegiatan_kerja_bakti' => $request->kegiatan_kerja_bakti,
            'program_kebersihan' => $request->program_kebersihan,
            'program_lingkungan_lainnya' => $request->program_lingkungan_lainnya,
            'tanggal_pelaksanaan_kegiatan' => $request->tanggal_pelaksanaan_kegiatan,
            'jumlah_peserta_kegiatan' => $request->jumlah_peserta_kegiatan ?: 0,
            'agenda_id' => $request->agenda_id,

            // Kendala & Rencana
            'kendala_lingkungan' => $request->kendala_lingkungan,
            'lokasi_perhatian_khusus' => $request->lokasi_perhatian_khusus,
            'rencana_tindak_lanjut' => $request->rencana_tindak_lanjut,
            'bantuan_rw_dibutuhkan' => $request->bantuan_rw_dibutuhkan,

            // Foto
            'foto_dokumentasi' => $fotoDokumentasi,
            'foto_sebelum' => $fotoSebelum,
            'foto_sesudah' => $fotoSesudah,
            'keterangan_foto' => $request->keterangan_foto,

            'status' => $status,
            'is_terlambat' => $isTerlambat,
            'versi' => $existing ? $existing->versi : 1,
        ];

        if ($existing) {
            $existing->update($data);
            $laporan = $existing;
            $aksi = $isSubmit ? 'dikirim_ke_rw' : 'update_draft';
        } else {
            $laporan = LaporanLingkungan::create($data);
            $aksi = $isSubmit ? 'dikirim_ke_rw' : 'draft_disimpan';
        }

        // Record history
        LaporanLingkunganHistory::create([
            'laporan_id' => $laporan->id,
            'user_id' => $user->id,
            'aksi' => $aksi,
            'status_sebelum' => $existing ? $existing->status : null,
            'status_sesudah' => $status,
            'catatan' => $isSubmit
                ? "Laporan lingkungan bulanan diserahkan ke Admin RW untuk proses pemeriksaan dan evaluasi."
                : "Draft laporan lingkungan disimpan sementara oleh Admin RT {$rtNumber}.",
            'versi' => $laporan->versi,
        ]);

        AuditLogger::log(
            'create',
            'Laporan Lingkungan',
            $laporan->id,
            "Laporan lingkungan RT {$rtNumber} periode {$periodeBulan}/{$periodeTahun} (" . strtoupper($status) . ") disimpan oleh {$user->name}.",
            null,
            $laporan->toArray(),
            $rtNumber
        );

        $msg = $isSubmit
            ? "Laporan lingkungan RT {$rtNumber} berhasil diserahkan ke Admin RW!"
            : "Draft laporan lingkungan RT {$rtNumber} berhasil disimpan.";

        return redirect()->route('lingkungan.show', $laporan->id)->with('success', $msg);
    }

    /**
     * Show detailed view of a monthly report.
     */
    public function show($id)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk pengurus lingkungan.');
        }

        $laporan = LaporanLingkungan::with(['user', 'pemeriksa', 'agenda', 'histories.user'])->findOrFail($id);

        if ($user->isAdminRt() && $user->rt_number !== $laporan->rt_number) {
            return redirect()->route('lingkungan.index')->with('error', 'Anda tidak memiliki hak akses melihat laporan RT lain.');
        }

        // Integrated complaints
        $pengaduanTerkait = Pengaduan::where('rt_number', $laporan->rt_number)
            ->where(function ($q) {
                $q->where('category', 'like', '%Lingkungan%')
                  ->orWhere('category', 'like', '%Sampah%')
                  ->orWhere('category', 'like', '%Kebersihan%')
                  ->orWhere('category', 'like', '%Drainase%');
            })
            ->limit(8)
            ->get();

        // Integrated bank sampah
        $bankSampahSummary = BankSampah::where('rt_number', $laporan->rt_number)
            ->whereMonth('tanggal_setor', $laporan->periode_bulan)
            ->whereYear('tanggal_setor', $laporan->periode_tahun)
            ->selectRaw('count(*) as total_transaksi, sum(berat_kg) as total_kg, sum(nilai_rupiah) as total_rp')
            ->first();

        // Linked agenda
        $agendaTerkait = $laporan->agenda;

        return view('lingkungan.show', compact('laporan', 'pengaduanTerkait', 'bankSampahSummary', 'agendaTerkait'));
    }

    /**
     * Edit draft or revision of report.
     */
    public function edit($id)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk pengurus lingkungan.');
        }

        $laporan = LaporanLingkungan::with(['histories.user'])->findOrFail($id);

        if ($user->isAdminRt() && $user->rt_number !== $laporan->rt_number) {
            return redirect()->route('lingkungan.index')->with('error', 'Anda tidak memiliki hak akses mengedit laporan RT lain.');
        }

        if (!$laporan->canBeEditedBy($user)) {
            return redirect()->route('lingkungan.show', $laporan->id)
                ->with('warning', 'Laporan yang berstatus ' . $laporan->status_label . ' sedang dikunci. Hanya laporan berstatus Draft atau Perlu Revisi yang dapat diedit.');
        }

        $allRts = ['006', '007', '008', '009', '010'];

        $bankSampahKg = BankSampah::where('rt_number', $laporan->rt_number)
            ->whereMonth('tanggal_setor', $laporan->periode_bulan)
            ->whereYear('tanggal_setor', $laporan->periode_tahun)
            ->sum('berat_kg');

        $pengaduanTerkait = Pengaduan::where('rt_number', $laporan->rt_number)
            ->whereIn('status', ['pending', 'diproses'])
            ->get();

        $agendaTerkait = AgendaRapat::whereMonth('tanggal_waktu', $laporan->periode_bulan)
            ->whereYear('tanggal_waktu', $laporan->periode_tahun)
            ->get();

        return view('lingkungan.edit', compact('laporan', 'allRts', 'bankSampahKg', 'pengaduanTerkait', 'agendaTerkait'));
    }

    /**
     * Update draft or resubmit revision of report.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk pengurus lingkungan.');
        }

        $laporan = LaporanLingkungan::findOrFail($id);

        if ($user->isAdminRt() && $user->rt_number !== $laporan->rt_number) {
            return redirect()->route('lingkungan.index')->with('error', 'Akses ditolak.');
        }

        if (!$laporan->canBeEditedBy($user)) {
            return redirect()->route('lingkungan.show', $laporan->id)
                ->with('error', 'Laporan ini tidak dapat diedit karena sedang dalam status ' . $laporan->status_label);
        }

        $request->validate([
            'kondisi_kebersihan' => 'required|string',
            'kondisi_drainase' => 'required|string',
            'action_type' => 'required|in:draft,submit',
            'foto_dokumentasi' => 'nullable|image|max:5120',
            'foto_sebelum' => 'nullable|image|max:5120',
            'foto_sesudah' => 'nullable|image|max:5120',
        ]);

        $fotoDokumentasi = $laporan->foto_dokumentasi;
        $fotoSebelum = $laporan->foto_sebelum;
        $fotoSesudah = $laporan->foto_sesudah;

        if ($request->hasFile('foto_dokumentasi')) {
            $fotoDokumentasi = $request->file('foto_dokumentasi')->store('lingkungan_photos', 'public');
        }
        if ($request->hasFile('foto_sebelum')) {
            $fotoSebelum = $request->file('foto_sebelum')->store('lingkungan_photos', 'public');
        }
        if ($request->hasFile('foto_sesudah')) {
            $fotoSesudah = $request->file('foto_sesudah')->store('lingkungan_photos', 'public');
        }

        $isSubmit = $request->action_type === 'submit';
        $prevStatus = $laporan->status;
        $newStatus = $isSubmit ? 'menunggu_pemeriksaan' : 'draft';
        $newVersi = $laporan->versi;

        if ($prevStatus === 'perlu_revisi' && $isSubmit) {
            $newVersi += 1;
        }

        $laporan->update([
            'kondisi_kebersihan' => $request->kondisi_kebersihan,
            'area_terjaga_kebersihan' => $request->area_terjaga_kebersihan,
            'area_perlu_perhatian_kebersihan' => $request->area_perlu_perhatian_kebersihan,
            'masalah_sampah' => $request->masalah_sampah,
            'keterangan_kebersihan' => $request->keterangan_kebersihan,

            'kondisi_drainase' => $request->kondisi_drainase,
            'kondisi_aliran_air' => $request->kondisi_aliran_air,
            'titik_rawan_genangan' => $request->titik_rawan_genangan,
            'saluran_butuh_pembersihan' => $request->saluran_butuh_pembersihan,
            'keterangan_drainase' => $request->keterangan_drainase,

            'program_penghijauan' => $request->program_penghijauan,
            'kondisi_penghijauan' => $request->kondisi_penghijauan,
            'ketersediaan_ruang_terbuka' => $request->ketersediaan_ruang_terbuka,
            'kegiatan_perawatan_tanaman' => $request->kegiatan_perawatan_tanaman,
            'kendala_penghijauan' => $request->kendala_penghijauan,

            'kegiatan_kerja_bakti' => $request->kegiatan_kerja_bakti,
            'program_kebersihan' => $request->program_kebersihan,
            'program_lingkungan_lainnya' => $request->program_lingkungan_lainnya,
            'tanggal_pelaksanaan_kegiatan' => $request->tanggal_pelaksanaan_kegiatan,
            'jumlah_peserta_kegiatan' => $request->jumlah_peserta_kegiatan ?: 0,
            'agenda_id' => $request->agenda_id,

            'kendala_lingkungan' => $request->kendala_lingkungan,
            'lokasi_perhatian_khusus' => $request->lokasi_perhatian_khusus,
            'rencana_tindak_lanjut' => $request->rencana_tindak_lanjut,
            'bantuan_rw_dibutuhkan' => $request->bantuan_rw_dibutuhkan,

            'foto_dokumentasi' => $fotoDokumentasi,
            'foto_sebelum' => $fotoSebelum,
            'foto_sesudah' => $fotoSesudah,
            'keterangan_foto' => $request->keterangan_foto,

            'status' => $newStatus,
            'catatan_revisi_rt' => $request->catatan_revisi_rt ?: $laporan->catatan_revisi_rt,
            'tanggal_pengiriman' => $isSubmit ? Carbon::now() : $laporan->tanggal_pengiriman,
            'versi' => $newVersi,
        ]);

        $historyNote = $isSubmit
            ? ($prevStatus === 'perlu_revisi'
                ? "Laporan hasil revisi (v{$newVersi}) telah dikirimkan kembali oleh Admin RT. Catatan perbaikan: " . ($request->catatan_revisi_rt ?: 'Revisi telah disesuaikan.')
                : "Laporan diserahkan kepada Admin RW untuk diperiksa.")
            : "Pembaruan isi draft laporan lingkungan oleh Admin RT.";

        LaporanLingkunganHistory::create([
            'laporan_id' => $laporan->id,
            'user_id' => $user->id,
            'aksi' => $isSubmit ? ($prevStatus === 'perlu_revisi' ? 'revisi_dikirim' : 'dikirim_ke_rw') : 'update_draft',
            'status_sebelum' => $prevStatus,
            'status_sesudah' => $newStatus,
            'catatan' => $historyNote,
            'versi' => $newVersi,
        ]);

        AuditLogger::log(
            'update',
            'Laporan Lingkungan',
            $laporan->id,
            "Laporan lingkungan RT {$laporan->rt_number} periode {$laporan->periode_bulan}/{$laporan->periode_tahun} diupdate menjadi {$newStatus} oleh {$user->name}.",
            null,
            $laporan->toArray(),
            $laporan->rt_number
        );

        return redirect()->route('lingkungan.show', $laporan->id)
            ->with('success', $isSubmit ? 'Laporan berhasil diperbarui dan diserahkan kembali ke Admin RW!' : 'Perubahan draft berhasil disimpan.');
    }

    /**
     * Admin RW Action: Evaluate report (Accept or Request Revision).
     */
    public function evaluasi(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isAdminRw()) {
            return redirect()->route('lingkungan.index')->with('error', 'Hanya Admin RW yang berwenang melakukan evaluasi laporan lingkungan.');
        }

        $request->validate([
            'keputusan' => 'required|in:diterima,perlu_revisi',
            'catatan_evaluasi_rw' => 'required|string|max:1000',
        ]);

        $laporan = LaporanLingkungan::findOrFail($id);
        $prevStatus = $laporan->status;
        $newStatus = $request->keputusan;

        $laporan->update([
            'status' => $newStatus,
            'catatan_rw' => $request->catatan_evaluasi_rw,
            'catatan_evaluasi_rw' => $request->catatan_evaluasi_rw,
            'pemeriksa_id' => $user->id,
            'tanggal_pemeriksaan' => Carbon::now(),
        ]);

        LaporanLingkunganHistory::create([
            'laporan_id' => $laporan->id,
            'user_id' => $user->id,
            'aksi' => $newStatus === 'diterima' ? 'diterima' : 'revisi_diminta',
            'status_sebelum' => $prevStatus,
            'status_sesudah' => $newStatus,
            'catatan' => $request->catatan_evaluasi_rw,
            'versi' => $laporan->versi,
        ]);

        AuditLogger::log(
            'update',
            'Laporan Lingkungan',
            $laporan->id,
            "Evaluasi Laporan Lingkungan RT {$laporan->rt_number} ({$newStatus}) oleh Ketua RW {$user->name}. Catatan: {$request->catatan_evaluasi_rw}",
            null,
            $laporan->toArray(),
            $laporan->rt_number
        );

        $statusMsg = $newStatus === 'diterima'
            ? "Laporan lingkungan RT {$laporan->rt_number} berhasil diterima dan disetujui resmi."
            : "Permintaan revisi berhasil dikirimkan ke Admin RT {$laporan->rt_number}.";

        return redirect()->route('lingkungan.show', $laporan->id)->with('success', $statusMsg);
    }

    /**
     * Admin RW Action: Update period deadline and instructions.
     */
    public function updatePeriodeSetting(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdminRw()) {
            return redirect()->route('lingkungan.index')->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'periode_bulan' => 'required|string',
            'periode_tahun' => 'required|string',
            'batas_waktu' => 'required|date',
            'catatan_instruksi_rw' => 'nullable|string|max:1000',
        ]);

        $bulan = str_pad($request->periode_bulan, 2, '0', STR_PAD_LEFT);
        $tahun = $request->periode_tahun;

        $periode = LaporanLingkunganPeriode::updateOrCreate(
            ['periode_bulan' => $bulan, 'periode_tahun' => $tahun],
            [
                'tanggal_mulai' => Carbon::create((int)$tahun, (int)$bulan, 1)->toDateString(),
                'batas_waktu' => $request->batas_waktu,
                'status' => 'aktif',
                'catatan_instruksi_rw' => $request->catatan_instruksi_rw,
                'created_by' => $user->id,
            ]
        );

        return redirect()->route('lingkungan.index', [
            'tab' => 'ringkasan',
            'periode_bulan' => $bulan,
            'periode_tahun' => $tahun,
        ])->with('success', "Pengaturan batas waktu pengiriman laporan periode {$bulan}/{$tahun} berhasil diperbarui.");
    }

    /**
     * Print view for official monthly report.
     */
    public function cetak($id)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk pengurus lingkungan.');
        }

        $laporan = LaporanLingkungan::with(['user', 'pemeriksa', 'histories.user'])->findOrFail($id);

        if ($user->isAdminRt() && $user->rt_number !== $laporan->rt_number) {
            return redirect()->route('lingkungan.index')->with('error', 'Akses ditolak.');
        }

        return view('lingkungan.cetak', compact('laporan'));
    }
}
