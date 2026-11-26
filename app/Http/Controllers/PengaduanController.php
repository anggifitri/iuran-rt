<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Models\AsetPemeriksaan;
use App\Models\AsetDokumentasi;
use App\Models\Pengaduan;
use App\Models\PengaduanHistory;
use App\Models\User;
use App\Models\FinanceNotification;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PengaduanController extends Controller
{
    /**
     * Halaman Utama: Manajemen Aset & Pengaduan (4 Tab Terpadu)
     * 1. Ringkasan
     * 2. Database Aset
     * 3. Pengaduan
     * 4. Dokumentasi & Identifikasi Aset
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isWarga = $user->isWarga();
        $isAdminRw = $user->isAdminRw();
        $isAdminRt = $user->isAdminRt();

        // Tab aktif (default: ringkasan jika admin, pengaduan jika warga tanpa query khusus)
        $defaultTab = $isWarga ? 'pengaduan' : 'ringkasan';
        $activeTab = $request->get('tab', $defaultTab);

        if ($isWarga && $activeTab === 'aset') {
            abort(403, 'Akses ditolak. Modul inventaris aset lingkungan hanya untuk pengurus.');
        }

        // Filter RT (Admin RT terkunci ke RT-nya sendiri)
        $rtFilter = $request->get('rt_number');
        if ($isAdminRt) {
            $rtFilter = $user->rt_number;
        }

        $rtList = ['006', '007', '008', '009', '010'];
        $categoriesList = [
            'Tiang Listrik (Kelistrikan)',
            'CCTV (Keamanan)',
            'Hydrant Damkar (Utilitas Air)',
            'Saluran Air (Drainase)',
            'Bak Sampah (Sanitasi)',
            'Fasilitas Umum',
            'Infrastruktur (Jalan)',
            'Ketertiban Lingkungan',
        ];

        // ========================================================
        // TAB 1: RINGKASAN (Statistik, Grafik, & Indikator Lengkap)
        // ========================================================
        $stat = [
            'totalAset' => 0,
            'asetBaik' => 0,
            'asetBermasalah' => 0,
            'totalPengaduan' => 0,
            'pengaduanMenunggu' => 0,
            'pengaduanProses' => 0,
            'pengaduanSelesai' => 0,
            'pengaduanTerlambat' => 0,
        ];

        $chartTrend = [
            'labels' => [],
            'total' => [],
            'selesai' => [],
            'proses' => [],
        ];
        $chartCategory = ['labels' => [], 'data' => []];
        $chartKondisiAset = ['labels' => [], 'data' => []];

        // Base Query Aset
        $asetBaseQ = Aset::query();
        if ($rtFilter && $rtFilter !== 'all') {
            $asetBaseQ->where('rt_number', $rtFilter);
        }

        // Base Query Pengaduan
        $pengaduanBaseQ = Pengaduan::query();
        if ($isWarga) {
            $pengaduanBaseQ->where('user_id', $user->id);
        } elseif ($rtFilter && $rtFilter !== 'all') {
            $pengaduanBaseQ->where('rt_number', $rtFilter);
        }

        if ($activeTab === 'ringkasan' || !$isWarga) {
            // Hitung statistik aset
            $stat['totalAset'] = (clone $asetBaseQ)->count();
            $stat['asetBaik'] = (clone $asetBaseQ)->where('kondisi', 'baik')->count();
            $stat['asetBermasalah'] = (clone $asetBaseQ)->whereIn('kondisi', ['rusak_ringan', 'rusak_berat', 'dalam_perbaikan'])->count();

            // Hitung statistik pengaduan
            $allPengaduansInScope = (clone $pengaduanBaseQ)->get();
            $stat['totalPengaduan'] = $allPengaduansInScope->count();
            $stat['pengaduanMenunggu'] = $allPengaduansInScope->whereIn('status', ['menunggu', 'pending', 'diverifikasi'])->count();
            $stat['pengaduanProses'] = $allPengaduansInScope->whereIn('status', ['ditugaskan', 'proses', 'sedang_diproses'])->count();
            $stat['pengaduanSelesai'] = $allPengaduansInScope->where('status', 'selesai')->count();
            
            // Hitung terlambat
            $stat['pengaduanTerlambat'] = $allPengaduansInScope->filter(function($item) {
                return $item->is_overdue;
            })->count();

            // 1. Grafik Tren Pengaduan Bulanan Tahun 2026 (Jan - Des)
            for ($m = 1; $m <= 12; $m++) {
                $monthDate = Carbon::create(2026, $m, 1);
                $chartTrend['labels'][] = $monthDate->translatedFormat('M');

                $monthItems = $allPengaduansInScope->filter(function($p) use ($m) {
                    return $p->created_at && (int)$p->created_at->format('m') === $m && (int)$p->created_at->format('Y') === 2026;
                });

                $chartTrend['total'][] = $monthItems->count();
                $chartTrend['selesai'][] = $monthItems->where('status', 'selesai')->count();
                $chartTrend['proses'][] = $monthItems->whereIn('status', ['ditugaskan', 'proses', 'sedang_diproses'])->count();
            }

            // 2. Grafik Distribusi Pengaduan Berdasarkan Kategori
            $catGroups = $allPengaduansInScope->groupBy(function($item) {
                return $item->category ?: 'Lainnya';
            });
            foreach ($catGroups as $catName => $group) {
                $chartCategory['labels'][] = $catName;
                $chartCategory['data'][] = $group->count();
            }

            // 3. Ringkasan Kondisi Aset
            $kondisiBaik = (clone $asetBaseQ)->where('kondisi', 'baik')->count();
            $kondisiRingan = (clone $asetBaseQ)->where('kondisi', 'rusak_ringan')->count();
            $kondisiBerat = (clone $asetBaseQ)->where('kondisi', 'rusak_berat')->count();
            $kondisiPerbaikan = (clone $asetBaseQ)->where('kondisi', 'dalam_perbaikan')->count();

            $chartKondisiAset = [
                'labels' => ['Kondisi Baik', 'Rusak Ringan', 'Rusak Berat', 'Dalam Perbaikan'],
                'data' => [$kondisiBaik, $kondisiRingan, $kondisiBerat, $kondisiPerbaikan],
            ];
        }

        // ========================================================
        // TAB 2: DATABASE ASET (Pencarian, Filter RT, Kondisi, Jenis)
        // ========================================================
        $asets = collect();
        $asetCountsByRt = [];
        if ($activeTab === 'aset' || $activeTab === 'ringkasan') {
            $asetQuery = (clone $asetBaseQ)->withCount(['pengaduans', 'pemeriksaans']);

            // Filter Kata Kunci
            if ($request->filled('q_aset')) {
                $q = $request->get('q_aset');
                $asetQuery->where(function($builder) use ($q) {
                    $builder->where('nama', 'like', "%{$q}%")
                            ->orWhere('nomor_aset', 'like', "%{$q}%")
                            ->orWhere('lokasi', 'like', "%{$q}%");
                });
            }

            // Filter Jenis / Kategori
            if ($request->filled('kategori_aset') && $request->get('kategori_aset') !== 'all') {
                $asetQuery->where('kategori', $request->get('kategori_aset'));
            }

            // Filter Kondisi
            if ($request->filled('kondisi_aset') && $request->get('kondisi_aset') !== 'all') {
                $asetQuery->where('kondisi', $request->get('kondisi_aset'));
            }

            $asets = $asetQuery->orderBy('rt_number')->orderBy('nomor_aset')->get();

            // Hitung aset per RT
            foreach ($rtList as $r) {
                $asetCountsByRt[$r] = Aset::where('rt_number', $r)->count();
            }
        }

        // ========================================================
        // TAB 3: PENGELOLAAN PENGADUAN WARGA (Card Layout & Filter)
        // ========================================================
        $pengaduans = collect();
        if ($activeTab === 'pengaduan' || $activeTab === 'ringkasan') {
            $pengaduanQuery = (clone $pengaduanBaseQ)->with(['user', 'aset', 'verifier', 'petugas']);

            // Filter Pencarian
            if ($request->filled('q_pengaduan')) {
                $q = $request->get('q_pengaduan');
                $pengaduanQuery->where(function($b) use ($q) {
                    $b->where('title', 'like', "%{$q}%")
                      ->orWhere('nomor_pengaduan', 'like', "%{$q}%")
                      ->orWhere('location_details', 'like', "%{$q}%")
                      ->orWhere('barcode_code', 'like', "%{$q}%")
                      ->orWhereHas('user', function($uq) use ($q) {
                          $uq->where('name', 'like', "%{$q}%");
                      });
                });
            }

            // Filter Kategori
            if ($request->filled('kategori_pengaduan') && $request->get('kategori_pengaduan') !== 'all') {
                $pengaduanQuery->where('category', $request->get('kategori_pengaduan'));
            }

            // Filter Status
            if ($request->filled('status_pengaduan') && $request->get('status_pengaduan') !== 'all') {
                $st = $request->get('status_pengaduan');
                if ($st === 'menunggu') {
                    $pengaduanQuery->whereIn('status', ['menunggu', 'pending']);
                } elseif ($st === 'proses') {
                    $pengaduanQuery->whereIn('status', ['proses', 'sedang_diproses', 'ditugaskan']);
                } else {
                    $pengaduanQuery->where('status', $st);
                }
            }

            // Filter Prioritas
            if ($request->filled('priority') && $request->get('priority') !== 'all') {
                $pengaduanQuery->where('priority', $request->get('priority'));
            }

            $pengaduans = $pengaduanQuery->latest('created_at')->paginate(10)->withQueryString();
        }

        // ========================================================
        // TAB 4: DOKUMENTASI & IDENTIFIKASI ASET (Foto & QR Scanner)
        // ========================================================
        $dokumentasis = collect();
        $allAsetsForDropdown = collect();
        $allPengaduansForDropdown = collect();

        if ($activeTab === 'dokumentasi' || $activeTab === 'ringkasan') {
            $dokQ = AsetDokumentasi::with(['aset', 'pengaduan', 'user'])->latest();
            if ($rtFilter && $rtFilter !== 'all') {
                $dokQ->whereHas('aset', function($q) use ($rtFilter) {
                    $q->where('rt_number', $rtFilter);
                });
            }
            $dokumentasis = $dokQ->take(20)->get();

            $allAsetsForDropdown = Aset::orderBy('rt_number')->orderBy('nama')->get();
            $allPengaduansForDropdown = (clone $pengaduanBaseQ)->whereIn('status', ['menunggu', 'diverifikasi', 'ditugaskan', 'proses'])->latest()->get();
        }

        // Daftar Aset JSON untuk Scanner QR & Google Maps
        $allAsetsJson = Aset::select('id', 'nomor_aset', 'nama', 'kategori', 'rt_number', 'rw_number', 'lokasi', 'latitude', 'longitude', 'kondisi', 'status_pemeliharaan')
            ->get()
            ->keyBy('nomor_aset');

        return view('pengaduan.index', compact(
            'user',
            'isWarga',
            'isAdminRw',
            'isAdminRt',
            'activeTab',
            'rtFilter',
            'rtList',
            'categoriesList',
            'stat',
            'chartTrend',
            'chartCategory',
            'chartKondisiAset',
            'asets',
            'asetCountsByRt',
            'pengaduans',
            'dokumentasis',
            'allAsetsForDropdown',
            'allPengaduansForDropdown',
            'allAsetsJson'
        ));
    }

    /**
     * Detail Aset Lingkungan (Identitas, QR Code, Google Maps, Riwayat, Dokumen)
     */
    public function showAset($id)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            abort(403, 'Akses ditolak. Detail aset lingkungan hanya untuk pengurus.');
        }

        $aset = Aset::with([
            'pengaduans.user',
            'pemeriksaans.user',
            'dokumentasis.user'
        ])->findOrFail($id);

        // RBAC check for Admin RT
        if ($user->isAdminRt() && $aset->rt_number !== $user->rt_number && $aset->rt_number !== '000') {
            return redirect()->route('pengaduan.index', ['tab' => 'aset'])
                ->with('error', 'Anda hanya memiliki akses untuk aset di lingkungan RT ' . $user->rt_number . '.');
        }

        return view('pengaduan.aset_detail', compact('user', 'aset'));
    }

    /**
     * Detail Pengaduan Warga (Alur Penanganan, Status, Catatan Internal vs Warga)
     */
    public function show($id)
    {
        $user = Auth::user();
        $pengaduan = Pengaduan::with([
            'user',
            'aset',
            'verifier',
            'petugas',
            'histories.user',
            'pemeriksaans.user',
            'dokumentasis.user'
        ])->findOrFail($id);

        // RBAC check: Warga hanya boleh melihat pengaduannya sendiri
        if ($user->isWarga() && $pengaduan->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat rincian laporan warga lain.');
        }

        // Admin RT hanya boleh melihat laporan di wilayah RT-nya
        if ($user->isAdminRt() && $pengaduan->rt_number !== $user->rt_number && $pengaduan->rt_number !== '000') {
            return redirect()->route('pengaduan.index')
                ->with('error', 'Laporan ini berada di luar kewenangan wilayah RT ' . $user->rt_number . '.');
        }

        // Ambil daftar petugas/teknisi atau admin RT untuk form penugasan
        $petugasList = User::whereIn('role', ['admin', 'admin_rt', 'admin_rw'])
            ->orderBy('name')
            ->get();

        return view('pengaduan.show', compact('user', 'pengaduan', 'petugasList'));
    }

    /**
     * Form Buat Pengaduan Baru (Support prefilled aset via QR)
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $prefilledAset = null;
        if ($request->filled('aset_code')) {
            $prefilledAset = Aset::where('nomor_aset', $request->get('aset_code'))->first();
        } elseif ($request->filled('aset_id')) {
            $prefilledAset = Aset::find($request->get('aset_id'));
        }

        $allAsets = Aset::orderBy('rt_number')->orderBy('nama')->get();

        return view('pengaduan.create', compact('user', 'prefilledAset', 'allAsets'));
    }

    /**
     * Simpan Pengaduan Baru
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:3000',
            'category' => 'required|string|max:100',
            'priority' => 'nullable|string|in:rendah,sedang,tinggi,darurat',
            'barcode_code' => 'nullable|string|max:100',
            'aset_id' => 'nullable|exists:asets,id',
            'rt_number' => 'nullable|string|max:10',
            'location_details' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'foto_lampiran' => 'nullable|image|max:5120', // max 5MB
        ]);

        // Jika ada barcode tetapi aset_id belum diisi, coba cocokkan
        $aset = null;
        if (!empty($validated['aset_id'])) {
            $aset = Aset::find($validated['aset_id']);
        } elseif (!empty($validated['barcode_code'])) {
            $aset = Aset::where('nomor_aset', $validated['barcode_code'])->first();
        }

        // Koordinat & RT otomatis sinkron dengan aset jika belum diisi
        $lat = $validated['latitude'] ?? ($aset ? $aset->latitude : null);
        $lng = $validated['longitude'] ?? ($aset ? $aset->longitude : null);
        $rt = $validated['rt_number'] ?? ($aset ? $aset->rt_number : ($user->rt_number ?? '006'));

        // Handle upload foto lampiran jika ada
        $fotoPath = null;
        if ($request->hasFile('foto_lampiran')) {
            $fotoPath = $request->file('foto_lampiran')->store('pengaduan_lampiran', 'public');
        }

        // Generate Nomor Pengaduan Unik
        $dateCode = Carbon::now()->format('Ym');
        $lastReport = Pengaduan::where('nomor_pengaduan', 'like', "PGD-{$dateCode}-%")->orderByDesc('id')->first();
        $nextNum = 1;
        if ($lastReport && preg_match('/PGD-\d{6}-(\d{4})/', $lastReport->nomor_pengaduan, $matches)) {
            $nextNum = (int)$matches[1] + 1;
        }
        $nomorPengaduan = sprintf('PGD-%s-%04d', $dateCode, $nextNum);

        $pengaduan = Pengaduan::create([
            'nomor_pengaduan' => $nomorPengaduan,
            'user_id' => $user->id,
            'title' => $validated['title'],
            'content' => $validated['content'],
            'status' => 'menunggu',
            'priority' => $validated['priority'] ?? 'sedang',
            'barcode_code' => $aset ? $aset->nomor_aset : ($validated['barcode_code'] ?? null),
            'aset_id' => $aset ? $aset->id : null,
            'category' => $validated['category'],
            'rt_number' => $rt,
            'location_details' => $validated['location_details'],
            'latitude' => $lat,
            'longitude' => $lng,
            'foto_lampiran' => $fotoPath,
            'deadline_at' => Carbon::now()->addDays(3), // Standar SLA 3 hari
        ]);

        // Simpan Log Awal Pengaduan
        PengaduanHistory::create([
            'pengaduan_id' => $pengaduan->id,
            'user_id' => $user->id,
            'status_dari' => null,
            'status_ke' => 'menunggu',
            'catatan' => 'Laporan diajukan oleh ' . $user->name . ' (' . ($user->role === 'warga' ? 'Warga RT ' . $user->rt_number : 'Pengurus') . ').',
            'is_internal' => false,
        ]);

        // Dokumentasikan foto jika ada dan terhubung aset
        if ($fotoPath && $aset) {
            AsetDokumentasi::create([
                'aset_id' => $aset->id,
                'pengaduan_id' => $pengaduan->id,
                'user_id' => $user->id,
                'foto' => $fotoPath,
                'tipe_dokumentasi' => 'kondisi_awal',
                'keterangan' => 'Foto pengaduan dari warga: ' . $validated['title'],
                'latitude' => $lat,
                'longitude' => $lng,
            ]);
        }

        // Audit log sistem
        AuditLogger::log(
            event: 'create',
            module: 'Manajemen Aset & Pengaduan',
            recordId: $pengaduan->id,
            description: "Pengaduan baru #{$nomorPengaduan} '{$pengaduan->title}' diajukan oleh {$user->name} untuk wilayah RT {$rt}.",
            rtNumber: $rt,
            status: 'success',
            actionType: 'CREATE'
        );

        return redirect()->route('pengaduan.show', $pengaduan->id)
            ->with('success', "Laporan Anda dengan nomor {$nomorPengaduan} berhasil dikirim dan siap diverifikasi oleh pengurus.");
    }

    /**
     * Update Status Pengaduan, Penugasan Petugas, Catatan Internal & Feedback Warga
     */
    public function updateStatus(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            abort(403, 'Warga tidak berwenang memperbarui status tindak lanjut pengaduan.');
        }

        $pengaduan = Pengaduan::findOrFail($id);

        if ($user->isAdminRt()) {
            if ($pengaduan->rt_number !== $user->rt_number) {
                abort(403, 'Akses ditolak: Anda hanya dapat menangani pengaduan di wilayah RT ' . $user->rt_number . '.');
            }
        }

        $validated = $request->validate([
            'status' => 'required|string|in:menunggu,diverifikasi,ditugaskan,proses,selesai,ditolak',
            'priority' => 'nullable|string|in:rendah,sedang,tinggi,darurat',
            'petugas_nama' => 'nullable|string|max:255',
            'internal_notes' => 'nullable|string|max:2000',
            'feedback_warga' => 'nullable|string|max:2000',
            'kondisi_aset_setelah' => 'nullable|string|in:baik,rusak_ringan,rusak_berat,dalam_perbaikan',
        ]);

        $oldStatus = $pengaduan->status;
        $newStatus = $validated['status'];

        $updateData = [
            'status' => $newStatus,
            'internal_notes' => $validated['internal_notes'] ?? $pengaduan->internal_notes,
            'feedback_warga' => $validated['feedback_warga'] ?? $pengaduan->feedback_warga,
        ];

        if (!empty($validated['priority'])) {
            $updateData['priority'] = $validated['priority'];
        }

        if (!empty($validated['petugas_nama'])) {
            $updateData['petugas_nama'] = $validated['petugas_nama'];
        }

        // Tandai verifikasi jika status berubah ke diverifikasi/ditugaskan
        if (in_array($newStatus, ['diverifikasi', 'ditugaskan', 'proses']) && !$pengaduan->verified_at) {
            $updateData['verified_at'] = now();
            $updateData['verified_by'] = $user->id;
        }

        // Tandai selesai jika status selesai
        if ($newStatus === 'selesai' && !$pengaduan->completed_at) {
            $updateData['completed_at'] = now();
        }

        $pengaduan->update($updateData);

        // Catat riwayat perubahan status
        $logCatatan = $validated['feedback_warga'] ?? ($validated['internal_notes'] ?? "Status diperbarui menjadi " . ucfirst($newStatus));
        PengaduanHistory::create([
            'pengaduan_id' => $pengaduan->id,
            'user_id' => $user->id,
            'status_dari' => $oldStatus,
            'status_ke' => $newStatus,
            'catatan' => $logCatatan,
            'is_internal' => false,
        ]);

        // Jika terhubung aset dan ada pembaruan kondisi aset
        if ($pengaduan->aset && !empty($validated['kondisi_aset_setelah'])) {
            $pengaduan->aset->update([
                'kondisi' => $validated['kondisi_aset_setelah'],
                'terakhir_diperiksa' => now(),
                'status_pemeliharaan' => $newStatus === 'selesai' ? 'normal' : 'sedang_dikerjakan',
            ]);

            // Buat entri pemeriksaan jika selesai
            if ($newStatus === 'selesai') {
                AsetPemeriksaan::create([
                    'aset_id' => $pengaduan->aset->id,
                    'user_id' => $user->id,
                    'pengaduan_id' => $pengaduan->id,
                    'jenis_kegiatan' => 'perbaikan_kerusakan',
                    'tanggal' => now(),
                    'kondisi_sebelum' => 'rusak_ringan',
                    'kondisi_setelah' => $validated['kondisi_aset_setelah'],
                    'catatan' => 'Penyelesaian laporan ' . $pengaduan->nomor_pengaduan . ': ' . ($validated['feedback_warga'] ?? 'Perbaikan aset selesai.'),
                    'petugas_nama' => $pengaduan->petugas_nama ?? $user->name,
                ]);
            }
        }

        AuditLogger::log(
            event: 'update',
            module: 'Manajemen Aset & Pengaduan',
            recordId: $pengaduan->id,
            description: "Status pengaduan #{$pengaduan->nomor_pengaduan} diperbarui dari '{$oldStatus}' menjadi '{$newStatus}' oleh {$user->name}.",
            oldValues: ['status' => $oldStatus],
            newValues: ['status' => $newStatus, 'petugas' => $pengaduan->petugas_nama],
            rtNumber: $pengaduan->rt_number,
            status: 'success',
            actionType: 'UPDATE'
        );

        return redirect()->route('pengaduan.show', $pengaduan->id)
            ->with('success', "Status laporan berhasil diperbarui menjadi: " . $pengaduan->status_label);
    }

    /**
     * API Pencarian Aset via QR Code atau Input Manual
     */
    public function findAsetByCode($code)
    {
        $code = trim($code);
        $aset = Aset::where('nomor_aset', $code)
            ->orWhere('nomor_aset', strtoupper($code))
            ->first();

        if (!$aset) {
            return response()->json([
                'success' => false,
                'message' => "Aset dengan ID '{$code}' tidak ditemukan di database lingkungan RW 018.",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'aset' => [
                'id' => $aset->id,
                'nomor_aset' => $aset->nomor_aset,
                'nama' => $aset->nama,
                'kategori' => $aset->kategori,
                'rt_number' => $aset->rt_number,
                'rw_number' => $aset->rw_number,
                'lokasi' => $aset->lokasi,
                'latitude' => $aset->latitude,
                'longitude' => $aset->longitude,
                'kondisi' => $aset->kondisi,
                'kondisi_label' => $aset->kondisi_badge['label'],
                'status_pemeliharaan' => $aset->status_pemeliharaan,
                'google_maps_url' => $aset->google_maps_url,
                'terakhir_diperiksa' => $aset->terakhir_diperiksa ? $aset->terakhir_diperiksa->translatedFormat('d M Y H:i') : '-',
                'detail_url' => route('aset.show', $aset->id),
                'report_url' => route('pengaduan.create', ['aset_id' => $aset->id, 'aset_code' => $aset->nomor_aset]),
            ]
        ]);
    }

    /**
     * Simpan Aset Baru (Admin RW / RT)
     */
    public function storeAset(Request $request)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            abort(403, 'Hanya pengurus yang berwenang mendaftarkan aset baru.');
        }

        $validated = $request->validate([
            'nomor_aset' => 'required|string|max:50|unique:asets,nomor_aset',
            'nama' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'rt_number' => 'required|string|max:10',
            'lokasi' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'kondisi' => 'required|string|in:baik,rusak_ringan,rusak_berat,dalam_perbaikan',
            'status_pemeliharaan' => 'nullable|string|in:normal,jadwal_servis,perlu_tindakan,sedang_dikerjakan',
            'tanggal_perolehan' => 'nullable|date',
            'spesifikasi' => 'nullable|string|max:1000',
        ]);

        $validated['rw_number'] = '018';
        if ($user->isAdminRt()) {
            $validated['rt_number'] = $user->rt_number;
        }
        $validated['status_pemeliharaan'] = $validated['status_pemeliharaan'] ?? 'normal';
        $validated['terakhir_diperiksa'] = now();

        $aset = Aset::create($validated);

        AuditLogger::log(
            event: 'create',
            module: 'Manajemen Aset & Pengaduan',
            recordId: $aset->id,
            description: "Aset baru {$aset->nomor_aset} '{$aset->nama}' didaftarkan oleh {$user->name} untuk wilayah RT {$aset->rt_number}.",
            rtNumber: $aset->rt_number,
            status: 'success',
            actionType: 'CREATE'
        );

        return redirect()->route('aset.show', $aset->id)
            ->with('success', "Aset baru {$aset->nomor_aset} - {$aset->nama} berhasil didaftarkan ke sistem.");
    }

    /**
     * Update Aset (Admin RW / RT)
     */
    public function updateAset(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            abort(403);
        }

        $aset = Aset::findOrFail($id);
        if ($user->isAdminRt()) {
            if ($aset->rt_number !== $user->rt_number) {
                abort(403, 'Akses ditolak: Anda hanya dapat mengubah aset di wilayah RT ' . $user->rt_number . '.');
            }
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'rt_number' => 'required|string|max:10',
            'lokasi' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'kondisi' => 'required|string|in:baik,rusak_ringan,rusak_berat,dalam_perbaikan',
            'status_pemeliharaan' => 'required|string|in:normal,jadwal_servis,perlu_tindakan,sedang_dikerjakan',
            'spesifikasi' => 'nullable|string|max:1000',
        ]);

        if ($user->isAdminRt()) {
            $validated['rt_number'] = $user->rt_number;
        }

        $oldValues = $aset->only(['nama', 'kategori', 'kondisi', 'status_pemeliharaan', 'lokasi']);
        $aset->update($validated);

        AuditLogger::log(
            event: 'update',
            module: 'Manajemen Aset & Pengaduan',
            recordId: $aset->id,
            description: "Informasi aset {$aset->nomor_aset} '{$aset->nama}' diperbarui oleh {$user->name}.",
            oldValues: $oldValues,
            newValues: $aset->only(['nama', 'kategori', 'kondisi', 'status_pemeliharaan', 'lokasi']),
            rtNumber: $aset->rt_number,
            status: 'success',
            actionType: 'UPDATE'
        );

        return redirect()->route('aset.show', $aset->id)
            ->with('success', "Data aset {$aset->nomor_aset} berhasil diperbarui.");
    }

    /**
     * Catat Riwayat Pemeriksaan & Perawatan Aset
     */
    public function storeAsetPemeriksaan(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            abort(403, 'Warga tidak memiliki hak mencatat inspeksi teknis aset.');
        }

        $aset = Aset::findOrFail($id);
        if ($user->isAdminRt()) {
            if ($aset->rt_number !== $user->rt_number) {
                abort(403, 'Akses ditolak: Anda hanya dapat mencatat inspeksi aset di wilayah RT ' . $user->rt_number . '.');
            }
        }

        $validated = $request->validate([
            'jenis_kegiatan' => 'required|string|in:pemeriksaan_rutin,inspeksi_pengaduan,perawatan_berkala,perbaikan_kerusakan',
            'tanggal' => 'required|date',
            'kondisi_sebelum' => 'nullable|string',
            'kondisi_setelah' => 'required|string|in:baik,rusak_ringan,rusak_berat,dalam_perbaikan',
            'catatan' => 'required|string|max:2000',
            'biaya' => 'nullable|numeric|min:0',
            'petugas_nama' => 'nullable|string|max:255',
            'foto_sebelum' => 'nullable|image|max:5120',
            'foto_setelah' => 'nullable|image|max:5120',
        ]);

        $fotoSebelumPath = null;
        if ($request->hasFile('foto_sebelum')) {
            $fotoSebelumPath = $request->file('foto_sebelum')->store('aset_dokumentasi', 'public');
        }

        $fotoSetelahPath = null;
        if ($request->hasFile('foto_setelah')) {
            $fotoSetelahPath = $request->file('foto_setelah')->store('aset_dokumentasi', 'public');
        }

        AsetPemeriksaan::create([
            'aset_id' => $aset->id,
            'user_id' => $user->id,
            'jenis_kegiatan' => $validated['jenis_kegiatan'],
            'tanggal' => $validated['tanggal'],
            'kondisi_sebelum' => $validated['kondisi_sebelum'] ?? $aset->kondisi,
            'kondisi_setelah' => $validated['kondisi_setelah'],
            'catatan' => $validated['catatan'],
            'biaya' => $validated['biaya'] ?? 0,
            'petugas_nama' => $validated['petugas_nama'] ?? $user->name,
            'foto_sebelum' => $fotoSebelumPath,
            'foto_setelah' => $fotoSetelahPath,
        ]);

        // Perbarui kondisi terkini aset
        $aset->update([
            'kondisi' => $validated['kondisi_setelah'],
            'terakhir_diperiksa' => now(),
            'status_pemeliharaan' => $validated['kondisi_setelah'] === 'baik' ? 'normal' : 'sedang_dikerjakan',
        ]);

        return redirect()->route('aset.show', $aset->id)
            ->with('success', "Pemeriksaan/Perawatan aset berhasil dicatat.");
    }

    /**
     * Upload Dokumentasi Foto Aset & Pengaduan
     */
    public function storeDokumentasi(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'aset_id' => 'nullable|exists:asets,id',
            'pengaduan_id' => 'nullable|exists:pengaduans,id',
            'foto' => 'required|image|max:6144', // max 6MB
            'tipe_dokumentasi' => 'required|string|in:kondisi_awal,kerusakan,proses_pemeriksaan,hasil_perbaikan',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        $fotoPath = $request->file('foto')->store('aset_dokumentasi', 'public');

        $lat = null;
        $lng = null;
        if (!empty($validated['aset_id'])) {
            $aset = Aset::find($validated['aset_id']);
            $lat = $aset?->latitude;
            $lng = $aset?->longitude;
        }

        AsetDokumentasi::create([
            'aset_id' => $validated['aset_id'] ?? null,
            'pengaduan_id' => $validated['pengaduan_id'] ?? null,
            'user_id' => $user->id,
            'foto' => $fotoPath,
            'tipe_dokumentasi' => $validated['tipe_dokumentasi'],
            'keterangan' => $validated['keterangan'] ?? 'Dokumentasi foto lapangan.',
            'latitude' => $lat,
            'longitude' => $lng,
        ]);

        return redirect()->route('pengaduan.index', ['tab' => 'dokumentasi'])
            ->with('success', "Foto dokumentasi lapangan berhasil diunggah dan terhubung ke sistem.");
    }
}
