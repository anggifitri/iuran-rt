<?php

namespace App\Http\Controllers;

use App\Models\BankSampah;
use App\Models\BankSampahEdukasi;
use App\Models\BankSampahHargaHistory;
use App\Models\BankSampahKategori;
use App\Models\BankSampahPenarikan;
use App\Models\FinanceNotification;
use App\Models\User;
use App\Models\Warga;
use App\Services\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class BankSampahController extends Controller
{
    /**
     * Main index method displaying the 4 internal tabs.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdminRw = $user->isAdminRw();
        $isAdminRt = $user->isAdminRt();
        $isWarga = $user->isWarga();

        $activeTab = $request->query('tab', 'ringkasan');
        if (!in_array($activeTab, ['ringkasan', 'setoran', 'edukasi', 'laporan'])) {
            $activeTab = 'ringkasan';
        }
        if ($isWarga && $activeTab === 'laporan') {
            $activeTab = 'ringkasan';
        }
        $allRts = ['006', '007', '008', '009', '010'];
        $rtList = $isAdminRt ? [$user->rt_number] : $allRts;

        // Current filter params
        $filterPeriod = $request->query('chart_period', '12bulan');
        $selectedTahun = $request->query('tahun', date('Y'));
        $selectedBulan = $request->query('bulan', date('m'));
        $selectedRt = $isAdminRt ? $user->rt_number : $request->query('rt_number');
        $selectedKategori = $request->query('kategori');
        $selectedStatus = $request->query('status');
        $search = $request->query('search');

        // Identify citizen profile for warga role
        $citizenProfile = null;
        if ($isWarga) {
            $citizenProfile = $user->resolveWarga();
        }

        // =========================================================================
        // 1. BASE METRICS (ONLY 'diterima' TRANSACTIONS AFFECT TOTALS & VALUE)
        // =========================================================================
        $baseAcceptedQuery = BankSampah::where('status', 'diterima');
        $baseAllQuery = BankSampah::query();

        if ($isAdminRt) {
            $baseAcceptedQuery->where('rt_number', $user->rt_number);
            $baseAllQuery->where('rt_number', $user->rt_number);
        } elseif ($isWarga) {
            $wargaId = $citizenProfile ? $citizenProfile->id : 0;
            $userId = $user->id;
            $baseAcceptedQuery->where(function ($q) use ($wargaId, $userId) {
                $q->where('warga_id', $wargaId)->orWhere('user_id', $userId);
            });
            $baseAllQuery->where(function ($q) use ($wargaId, $userId) {
                $q->where('warga_id', $wargaId)->orWhere('user_id', $userId);
            });
        } elseif (!empty($selectedRt)) {
            $baseAcceptedQuery->where('rt_number', $selectedRt);
            $baseAllQuery->where('rt_number', $selectedRt);
        }

        $totalBeratKg = (clone $baseAcceptedQuery)->sum('berat_kg');
        $totalNilaiRupiah = (clone $baseAcceptedQuery)->sum('nilai_rupiah');
        $totalTransaksi = (clone $baseAcceptedQuery)->count();
        $menungguKonfirmasiCount = (clone $baseAllQuery)->where('status', 'menunggu')->count();

        // Warga aktif & RT aktif (RW/RT scope)
        $wargaAktifCount = (clone $baseAcceptedQuery)->distinct('warga_id')->count('warga_id');
        if ($wargaAktifCount === 0) {
            $wargaAktifCount = (clone $baseAcceptedQuery)->distinct('nama_penyetor')->count('nama_penyetor');
        }
        $rtAktifCount = (clone $baseAcceptedQuery)->distinct('rt_number')->count('rt_number');

        // Personal citizen metrics (if role is Warga)
        $wargaTabunganData = [
            'total_kg' => 0,
            'total_nilai' => 0,
            'total_penarikan' => 0,
            'saldo_aktif' => 0,
            'transaksi_count' => 0,
            'last_setoran' => null,
        ];
        if ($isWarga && $citizenProfile) {
            $totalSetoranWarga = BankSampah::where('status', 'diterima')
                ->where(function ($q) use ($citizenProfile, $user) {
                    $q->where('warga_id', $citizenProfile->id)->orWhere('user_id', $user->id);
                })
                ->sum('nilai_rupiah');

            $totalPenarikanWarga = BankSampahPenarikan::where('status', 'selesai')
                ->where(function ($q) use ($citizenProfile, $user) {
                    $q->where('warga_id', $citizenProfile->id)->orWhere('user_id', $user->id);
                })
                ->sum('jumlah_penarikan');

            $wargaTabunganData = [
                'total_kg' => BankSampah::where('status', 'diterima')
                    ->where(function ($q) use ($citizenProfile, $user) {
                        $q->where('warga_id', $citizenProfile->id)->orWhere('user_id', $user->id);
                    })
                    ->sum('berat_kg'),
                'total_nilai' => $totalSetoranWarga,
                'total_penarikan' => $totalPenarikanWarga,
                'saldo_aktif' => max(0, $totalSetoranWarga - $totalPenarikanWarga),
                'transaksi_count' => BankSampah::where(function ($q) use ($citizenProfile, $user) {
                    $q->where('warga_id', $citizenProfile->id)->orWhere('user_id', $user->id);
                })->count(),
                'last_setoran' => BankSampah::where(function ($q) use ($citizenProfile, $user) {
                    $q->where('warga_id', $citizenProfile->id)->orWhere('user_id', $user->id);
                })->latest('tanggal_setor')->first(),
            ];
        }

        // =========================================================================
        // 2. KATEGORI & HARGA SAMPAH
        // =========================================================================
        $kategoris = BankSampahKategori::with(['priceHistories.user'])->orderBy('nama_kategori')->get();
        $activeKategoris = $kategoris->where('is_active', true);

        // =========================================================================
        // 3. SETORAN SAMPAH TABLE (TAB 2)
        // =========================================================================
        $setoranQuery = BankSampah::with(['warga', 'user', 'verifier'])->orderBy('tanggal_setor', 'desc')->orderBy('id', 'desc');

        if ($isAdminRt) {
            $setoranQuery->where('rt_number', $user->rt_number);
        } elseif ($isWarga) {
            $wargaId = $citizenProfile ? $citizenProfile->id : 0;
            $userId = $user->id;
            $setoranQuery->where(function ($q) use ($wargaId, $userId) {
                $q->where('warga_id', $wargaId)->orWhere('user_id', $userId);
            });
        } elseif (!empty($selectedRt)) {
            $setoranQuery->where('rt_number', $selectedRt);
        }

        if (!empty($selectedKategori)) {
            $setoranQuery->where('kategori', $selectedKategori);
        }
        if (!empty($selectedStatus)) {
            $setoranQuery->where('status', $selectedStatus);
        }
        if (!empty($search)) {
            $setoranQuery->where(function ($q) use ($search) {
                $q->where('nama_penyetor', 'like', "%{$search}%")
                  ->orWhere('nomor_transaksi', 'like', "%{$search}%")
                  ->orWhere('catatan', 'like', "%{$search}%");
            });
        }

        $setorans = $setoranQuery->paginate(15)->withQueryString();

        // Recent activity for dashboard
        $recentSetorans = (clone $setoranQuery)->limit(8)->get();

        // =========================================================================
        // 4. TABUNGAN WARGA LEDGER (TAB 2 SUB-SECTION)
        // =========================================================================
        $tabunganWargaList = [];
        $tabunganWargas = collect();
        if (!$isWarga) {
            $wargaLedgerQuery = Warga::query();
            if ($isAdminRt) {
                $wargaLedgerQuery->where('rt_number', $user->rt_number);
            } elseif (!empty($selectedRt)) {
                $wargaLedgerQuery->where('rt_number', $selectedRt);
            }

            // Ambil warga yang memiliki transaksi bank sampah atau terdaftar di RT
            $tabunganWargas = $wargaLedgerQuery->orderBy('nama')->paginate(12, ['*'], 'page_tabungan')->withQueryString();

            foreach ($tabunganWargas as $w) {
                $totSetoran = BankSampah::where('warga_id', $w->id)->where('status', 'diterima')->sum('nilai_rupiah');
                $totKg = BankSampah::where('warga_id', $w->id)->where('status', 'diterima')->sum('berat_kg');
                $totPenarikan = BankSampahPenarikan::where('warga_id', $w->id)->where('status', 'selesai')->sum('jumlah_penarikan');
                $txCount = BankSampah::where('warga_id', $w->id)->count();

                $tabunganWargaList[] = [
                    'warga' => $w,
                    'total_kg' => $totKg,
                    'total_setoran' => $totSetoran,
                    'total_penarikan' => $totPenarikan,
                    'saldo' => max(0, $totSetoran - $totPenarikan),
                    'transaksi_count' => $txCount,
                ];
            }
        }

        // =========================================================================
        // 5. MATERI EDUKASI LINGKUNGAN (TAB 3)
        // =========================================================================
        $edukasiQuery = BankSampahEdukasi::query();
        if ($isWarga || $isAdminRt) {
            $edukasiQuery->where('status', 'published');
        }
        $edukasiArticles = $edukasiQuery->orderBy('id', 'asc')->get();

        // =========================================================================
        // 6. CHARTS DATA (RINGKASAN & LAPORAN)
        // =========================================================================
        $chartMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $chartBeratBulanan = array_fill(0, 12, 0);
        $chartNilaiBulanan = array_fill(0, 12, 0);

        $chartAcceptedRecords = (clone $baseAcceptedQuery)
            ->whereYear('tanggal_setor', $selectedTahun)
            ->get();

        foreach ($chartAcceptedRecords as $rec) {
            $mIdx = (int)Carbon::parse($rec->tanggal_setor)->format('m') - 1;
            if ($mIdx >= 0 && $mIdx < 12) {
                $chartBeratBulanan[$mIdx] += (float)$rec->berat_kg;
                $chartNilaiBulanan[$mIdx] += (float)$rec->nilai_rupiah;
            }
        }

        // Perbandingan kategori sampah (doughnut chart)
        $kategoriBreakdown = [];
        foreach ($activeKategoris as $k) {
            $kategoriBreakdown[$k->kode] = [
                'nama' => $k->nama_kategori,
                'berat' => (clone $baseAcceptedQuery)->where('kategori', $k->kode)->sum('berat_kg'),
                'color' => $k->accent_color ?: '#285943',
            ];
        }

        // Perbandingan antar-RT (RT 006 s/d 010)
        $chartPerbandinganRt = [
            'labels' => ['RT 006', 'RT 007', 'RT 008', 'RT 009', 'RT 010'],
            'berat_kg' => [0, 0, 0, 0, 0],
            'nilai_rupiah' => [0, 0, 0, 0, 0],
            'transaksi' => [0, 0, 0, 0, 0],
        ];

        foreach ($allRts as $idx => $rt) {
            $rtAccepted = BankSampah::where('status', 'diterima')->where('rt_number', $rt);
            if ($filterPeriod === '1bulan') {
                $rtAccepted->whereMonth('tanggal_setor', $selectedBulan)->whereYear('tanggal_setor', $selectedTahun);
            } elseif ($filterPeriod === '12bulan') {
                $rtAccepted->whereYear('tanggal_setor', $selectedTahun);
            }
            $chartPerbandinganRt['berat_kg'][$idx] = round((clone $rtAccepted)->sum('berat_kg'), 1);
            $chartPerbandinganRt['nilai_rupiah'][$idx] = (clone $rtAccepted)->sum('nilai_rupiah');
            $chartPerbandinganRt['transaksi'][$idx] = (clone $rtAccepted)->count();
        }

        // =========================================================================
        // 7. REKAPITULASI LAPORAN PER RT (TAB 4)
        // =========================================================================
        $rekapLaporanRt = [];
        foreach ($allRts as $rt) {
            $q = BankSampah::where('rt_number', $rt)->where('status', 'diterima');
            if (!empty($selectedTahun)) {
                $q->whereYear('tanggal_setor', $selectedTahun);
            }
            if (!empty($selectedBulan)) {
                $q->whereMonth('tanggal_setor', $selectedBulan);
            }

            $wargaPartisipasi = (clone $q)->distinct('warga_id')->count('warga_id');
            if ($wargaPartisipasi === 0) {
                $wargaPartisipasi = (clone $q)->distinct('nama_penyetor')->count('nama_penyetor');
            }

            // Top category in this RT
            $topKat = (clone $q)->select('kategori', DB::raw('sum(berat_kg) as total_berat'))
                ->groupBy('kategori')
                ->orderByDesc('total_berat')
                ->first();

            $topKatLabel = $topKat ? ucfirst($topKat->kategori) . ' (' . round($topKat->total_berat, 1) . ' Kg)' : '-';

            $rekapLaporanRt[$rt] = [
                'rt_number' => $rt,
                'total_berat' => round((clone $q)->sum('berat_kg'), 1),
                'total_nilai' => (clone $q)->sum('nilai_rupiah'),
                'total_transaksi' => (clone $q)->count(),
                'warga_count' => $wargaPartisipasi,
                'top_kategori' => $topKatLabel,
            ];
        }

        // Database citizens for dropdown selection in form
        $wargaDropdown = Warga::where('status_warga', '!=', 'pindah')
            ->when($isAdminRt, function ($q) use ($user) {
                $q->where('rt_number', $user->rt_number);
            })
            ->orderBy('nama')
            ->get(['id', 'nama', 'rt_number', 'blok_rumah', 'nik']);

        return view('bank_sampah.index', compact(
            'isAdminRw',
            'isAdminRt',
            'isWarga',
            'activeTab',
            'filterPeriod',
            'selectedTahun',
            'selectedBulan',
            'selectedRt',
            'selectedKategori',
            'selectedStatus',
            'search',
            'allRts',
            'rtList',
            'citizenProfile',
            'totalBeratKg',
            'totalNilaiRupiah',
            'totalTransaksi',
            'menungguKonfirmasiCount',
            'wargaAktifCount',
            'rtAktifCount',
            'wargaTabunganData',
            'kategoris',
            'activeKategoris',
            'setorans',
            'recentSetorans',
            'tabunganWargaList',
            'tabunganWargas',
            'edukasiArticles',
            'chartMonths',
            'chartBeratBulanan',
            'chartNilaiBulanan',
            'kategoriBreakdown',
            'chartPerbandinganRt',
            'rekapLaporanRt',
            'wargaDropdown'
        ));
    }

    /**
     * Store new waste deposit with automatic calculation and unique transaction number.
     */
    public function storeSetoran(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'warga_id' => 'nullable|exists:wargas,id',
            'nama_penyetor' => 'required|string|max:255',
            'rt_number' => 'required|string',
            'tanggal_setor' => 'required|date',
            'kategori' => 'required|string',
            'berat_kg' => 'required|numeric|min:0.01',
            'catatan' => 'nullable|string|max:500',
            'foto_dokumentasi' => 'nullable|image|max:5120',
        ]);

        // RT isolation check
        $rtNumber = $user->isAdminRt() ? $user->rt_number : $request->rt_number;

        // Fetch category to get active benchmark price
        $kat = BankSampahKategori::where('kode', $request->kategori)->first();
        $hargaSatuan = $kat ? $kat->harga_acuan : 3000;
        $satuan = $kat ? $kat->satuan : 'Kg';

        // Auto-calculate total value
        $nilaiRupiah = round($request->berat_kg * $hargaSatuan);

        // Upload photo documentation if present
        $fotoPath = null;
        if ($request->hasFile('foto_dokumentasi')) {
            $fotoPath = $request->file('foto_dokumentasi')->store('bank_sampah_photos', 'public');
        }

        // Generate unique transaction number: BS-YYYYMM-XXXX
        $bulanCode = Carbon::parse($request->tanggal_setor)->format('Ym');
        $lastTx = BankSampah::where('nomor_transaksi', 'like', "BS-{$bulanCode}-%")->orderBy('id', 'desc')->first();
        $nextSeq = 1;
        if ($lastTx && preg_match('/BS-\d{6}-(\d+)/', $lastTx->nomor_transaksi, $matches)) {
            $nextSeq = (int)$matches[1] + 1;
        }
        $nomorTx = 'BS-' . $bulanCode . '-' . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);

        // Status determination: directly accepted if entered by admin/officer, pending if user self-registers
        $status = ($user->isAdmin() || $user->isBendahara()) ? 'diterima' : 'menunggu';

        $wargaId = $request->warga_id;
        if (!$wargaId) {
            $wMatch = Warga::where('nama', $request->nama_penyetor)->where('rt_number', $rtNumber)->first();
            if ($wMatch) {
                $wargaId = $wMatch->id;
            }
        }

        $setoran = BankSampah::create([
            'nomor_transaksi' => $nomorTx,
            'rt_number' => $rtNumber,
            'warga_id' => $wargaId,
            'user_id' => $user->isWarga() ? $user->id : null,
            'nama_penyetor' => $request->nama_penyetor,
            'tanggal_setor' => $request->tanggal_setor,
            'kategori' => $request->kategori,
            'berat_kg' => $request->berat_kg,
            'satuan' => $satuan,
            'harga_per_satuan' => $hargaSatuan,
            'nilai_rupiah' => $nilaiRupiah,
            'status' => $status,
            'foto_dokumentasi' => $fotoPath,
            'catatan' => $request->catatan,
            'petugas' => $user->name,
            'verified_by' => $status === 'diterima' ? $user->id : null,
            'verified_at' => $status === 'diterima' ? now() : null,
        ]);

        // Dispatch notification if citizen is registered
        if ($wargaId && $status === 'diterima') {
            $targetUser = User::where('name', $request->nama_penyetor)->first() ?: User::where('role', 'warga')->where('rt_number', $rtNumber)->first();
            if ($targetUser) {
                FinanceNotification::send(
                    $targetUser->id,
                    'bank_sampah',
                    'Setoran Bank Sampah Berhasil Diterima',
                    "Setoran sampah {$request->berat_kg} {$satuan} ({$request->kategori}) senilai Rp " . number_format($nilaiRupiah, 0, ',', '.') . " telah diterima. Saldo tabungan Anda bertambah.",
                    [
                        'warga_id' => $wargaId,
                        'nominal' => $nilaiRupiah,
                        'link_url' => route('bank_sampah.index', ['tab' => 'ringkasan']),
                    ]
                );
            }
        }

        AuditLogger::log(
            'create',
            'Bank Sampah',
            $setoran->id,
            "Pencatatan setoran sampah {$nomorTx} oleh {$request->nama_penyetor} di RT {$rtNumber}: {$request->berat_kg} {$satuan} senilai Rp " . number_format($nilaiRupiah, 0, ',', '.') . " ({$status}).",
            null,
            $setoran->toArray(),
            $rtNumber
        );

        return redirect()->route('bank_sampah.index', ['tab' => 'setoran'])
            ->with('success', "Setoran sampah {$nomorTx} berhasil dicatat! Status: " . ucfirst($status));
    }

    /**
     * Show single deposit detail (JSON or redirect).
     */
    public function showSetoran($id)
    {
        $user = Auth::user();
        $setoran = BankSampah::with(['warga', 'user', 'verifier', 'kategoriDetail'])->findOrFail($id);

        if ($user->isAdminRt() && $user->rt_number !== $setoran->rt_number) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        return response()->json([
            'id' => $setoran->id,
            'nomor_transaksi' => $setoran->nomor_transaksi,
            'nama_penyetor' => $setoran->nama_penyetor,
            'rt_number' => $setoran->rt_number,
            'tanggal_setor' => $setoran->tanggal_setor->translatedFormat('d F Y'),
            'kategori' => ucfirst($setoran->kategori),
            'berat_kg' => $setoran->berat_kg,
            'satuan' => $setoran->satuan,
            'harga_per_satuan' => number_format($setoran->harga_per_satuan, 0, ',', '.'),
            'nilai_rupiah' => number_format($setoran->nilai_rupiah, 0, ',', '.'),
            'status' => $setoran->status,
            'alasan_penolakan' => $setoran->alasan_penolakan,
            'foto_url' => $setoran->foto_dokumentasi ? asset('storage/' . $setoran->foto_dokumentasi) : null,
            'petugas' => $setoran->petugas,
            'verified_by' => $setoran->verifier ? $setoran->verifier->name : null,
            'verified_at' => $setoran->verified_at ? $setoran->verified_at->translatedFormat('d F Y, H:i') : null,
            'catatan' => $setoran->catatan,
        ]);
    }

    /**
     * Verify (Accept or Reject) a deposit transaction.
     */
    public function verifikasiSetoran(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            return redirect()->route('bank_sampah.index')->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'keputusan' => 'required|in:diterima,ditolak',
            'alasan_penolakan' => 'nullable|string|max:500',
        ]);

        $setoran = BankSampah::findOrFail($id);

        if ($user->isAdminRt() && $user->rt_number !== $setoran->rt_number) {
            return redirect()->route('bank_sampah.index')->with('error', 'Akses ditolak.');
        }

        $prevStatus = $setoran->status;
        $newStatus = $request->keputusan;

        $setoran->update([
            'status' => $newStatus,
            'alasan_penolakan' => $newStatus === 'ditolak' ? $request->alasan_penolakan : null,
            'verified_by' => $user->id,
            'verified_at' => now(),
        ]);

        // Send notification to citizen
        $targetUser = User::where('name', $setoran->nama_penyetor)->first() ?: User::where('role', 'warga')->where('rt_number', $setoran->rt_number)->first();
        if ($targetUser) {
            if ($newStatus === 'diterima') {
                FinanceNotification::send(
                    $targetUser->id,
                    'bank_sampah',
                    'Setoran Bank Sampah Diverifikasi Diterima',
                    "Setoran Anda nomor {$setoran->nomor_transaksi} sebesar Rp " . number_format($setoran->nilai_rupiah, 0, ',', '.') . " telah diverifikasi diterima oleh petugas.",
                    ['nominal' => $setoran->nilai_rupiah, 'link_url' => route('bank_sampah.index', ['tab' => 'ringkasan'])]
                );
            } else {
                FinanceNotification::send(
                    $targetUser->id,
                    'bank_sampah_ditolak',
                    'Setoran Bank Sampah Ditolak',
                    "Setoran Anda nomor {$setoran->nomor_transaksi} ditolak oleh petugas. Alasan: " . ($request->alasan_penolakan ?: 'Kondisi limbah tidak memenuhi standar kebersihan.'),
                    ['link_url' => route('bank_sampah.index', ['tab' => 'setoran'])]
                );
            }
        }

        AuditLogger::log(
            'update',
            'Bank Sampah',
            $setoran->id,
            "Verifikasi setoran {$setoran->nomor_transaksi} ({$newStatus}) oleh {$user->name}.",
            ['status' => $prevStatus],
            $setoran->toArray(),
            $setoran->rt_number
        );

        return redirect()->route('bank_sampah.index', ['tab' => 'setoran'])
            ->with('success', "Status setoran {$setoran->nomor_transaksi} berhasil diubah menjadi: " . ucfirst($newStatus));
    }

    /**
     * Admin RW: Store new waste category.
     */
    public function storeKategori(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdminRw()) {
            return redirect()->route('bank_sampah.index')->with('error', 'Hanya Admin RW yang berwenang menambah kategori sampah.');
        }

        $request->validate([
            'kode' => 'required|string|alpha_dash|unique:bank_sampah_kategoris,kode|max:50',
            'nama_kategori' => 'required|string|max:100',
            'deskripsi' => 'nullable|string|max:500',
            'satuan' => 'required|string|max:20',
            'harga_min' => 'required|numeric|min:0',
            'harga_max' => 'required|numeric|gte:harga_min',
            'harga_acuan' => 'required|numeric|gte:harga_min|lte:harga_max',
            'tips_pemilahan' => 'nullable|string|max:500',
            'accent_color' => 'nullable|string|max:20',
        ]);

        $kat = BankSampahKategori::create([
            'kode' => strtolower($request->kode),
            'nama_kategori' => $request->nama_kategori,
            'deskripsi' => $request->deskripsi,
            'satuan' => $request->satuan,
            'harga_min' => $request->harga_min,
            'harga_max' => $request->harga_max,
            'harga_acuan' => $request->harga_acuan,
            'tips_pemilahan' => $request->tips_pemilahan,
            'accent_color' => $request->accent_color ?: '#285943',
            'is_active' => true,
            'created_by' => $user->id,
        ]);

        BankSampahHargaHistory::create([
            'kategori_id' => $kat->id,
            'harga_lama' => 0,
            'harga_baru' => $request->harga_acuan,
            'user_id' => $user->id,
            'catatan' => 'Penetapan harga acuan awal untuk kategori baru.',
        ]);

        return redirect()->route('bank_sampah.index', ['tab' => 'edukasi'])
            ->with('success', "Kategori sampah '{$kat->nama_kategori}' berhasil ditambahkan!");
    }

    /**
     * Admin RW: Update category or benchmark price.
     */
    public function updateKategori(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isAdminRw()) {
            return redirect()->route('bank_sampah.index')->with('error', 'Akses ditolak.');
        }

        $kat = BankSampahKategori::findOrFail($id);

        $request->validate([
            'nama_kategori' => 'required|string|max:100',
            'deskripsi' => 'nullable|string|max:500',
            'satuan' => 'required|string|max:20',
            'harga_min' => 'required|numeric|min:0',
            'harga_max' => 'required|numeric|gte:harga_min',
            'harga_acuan' => 'required|numeric|gte:harga_min|lte:harga_max',
            'tips_pemilahan' => 'nullable|string|max:500',
            'catatan_perubahan' => 'nullable|string|max:255',
        ]);

        $hargaLama = $kat->harga_acuan;
        $hargaBaru = $request->harga_acuan;

        $kat->update([
            'nama_kategori' => $request->nama_kategori,
            'deskripsi' => $request->deskripsi,
            'satuan' => $request->satuan,
            'harga_min' => $request->harga_min,
            'harga_max' => $request->harga_max,
            'harga_acuan' => $hargaBaru,
            'tips_pemilahan' => $request->tips_pemilahan,
        ]);

        // Record price history if benchmark price changed
        if ($hargaLama != $hargaBaru) {
            BankSampahHargaHistory::create([
                'kategori_id' => $kat->id,
                'harga_lama' => $hargaLama,
                'harga_baru' => $hargaBaru,
                'user_id' => $user->id,
                'catatan' => $request->catatan_perubahan ?: "Penyesuaian harga acuan RW dari Rp " . number_format($hargaLama, 0, ',', '.') . " menjadi Rp " . number_format($hargaBaru, 0, ',', '.') . " / {$kat->satuan}",
            ]);
        }

        return redirect()->route('bank_sampah.index', ['tab' => 'edukasi'])
            ->with('success', "Informasi dan harga kategori '{$kat->nama_kategori}' berhasil diperbarui!");
    }

    /**
     * Admin RW: Toggle active/inactive state of category.
     */
    public function toggleKategori($id)
    {
        $user = Auth::user();
        if (!$user->isAdminRw()) {
            return redirect()->route('bank_sampah.index')->with('error', 'Akses ditolak.');
        }

        $kat = BankSampahKategori::findOrFail($id);
        $kat->update(['is_active' => !$kat->is_active]);

        $statusText = $kat->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('bank_sampah.index', ['tab' => 'edukasi'])
            ->with('success', "Kategori '{$kat->nama_kategori}' berhasil {$statusText}.");
    }

    /**
     * Citizen Waste Bank Savings Withdrawal (Penarikan Saldo).
     */
    public function storePenarikan(Request $request)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            return redirect()->route('bank_sampah.index')->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'warga_id' => 'required|exists:wargas,id',
            'tanggal_penarikan' => 'required|date',
            'jumlah_penarikan' => 'required|numeric|min:1000',
            'metode_penarikan' => 'required|in:tunai,transfer,potong_iuran',
            'catatan' => 'nullable|string|max:500',
        ]);

        $warga = Warga::findOrFail($request->warga_id);

        if ($user->isAdminRt() && $user->rt_number !== $warga->rt_number) {
            return redirect()->route('bank_sampah.index')->with('error', 'Akses ditolak. Tidak dapat memproses warga RT lain.');
        }

        // Calculate available balance
        $totalSetoran = BankSampah::where('warga_id', $warga->id)->where('status', 'diterima')->sum('nilai_rupiah');
        $totalPenarikan = BankSampahPenarikan::where('warga_id', $warga->id)->where('status', 'selesai')->sum('jumlah_penarikan');
        $saldoAktif = max(0, $totalSetoran - $totalPenarikan);

        if ($request->jumlah_penarikan > $saldoAktif) {
            return redirect()->back()->with('error', "Penarikan gagal! Saldo tabungan warga saat ini (Rp " . number_format($saldoAktif, 0, ',', '.') . ") tidak mencukupi penarikan sebesar Rp " . number_format($request->jumlah_penarikan, 0, ',', '.'));
        }

        // Generate unique withdrawal number: WD-YYYYMM-XXXX
        $blnCode = Carbon::parse($request->tanggal_penarikan)->format('Ym');
        $lastWd = BankSampahPenarikan::where('nomor_penarikan', 'like', "WD-{$blnCode}-%")->orderBy('id', 'desc')->first();
        $nextSeq = 1;
        if ($lastWd && preg_match('/WD-\d{6}-(\d+)/', $lastWd->nomor_penarikan, $m)) {
            $nextSeq = (int)$m[1] + 1;
        }
        $nomorWd = 'WD-' . $blnCode . '-' . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);

        $penarikan = BankSampahPenarikan::create([
            'nomor_penarikan' => $nomorWd,
            'warga_id' => $warga->id,
            'user_id' => null,
            'rt_number' => $warga->rt_number,
            'tanggal_penarikan' => $request->tanggal_penarikan,
            'jumlah_penarikan' => $request->jumlah_penarikan,
            'metode_penarikan' => $request->metode_penarikan,
            'petugas' => $user->name,
            'catatan' => $request->catatan,
            'status' => 'selesai',
        ]);

        // Dispatch notification to citizen
        $targetUser = User::where('name', $warga->nama)->first() ?: User::where('role', 'warga')->where('rt_number', $warga->rt_number)->first();
        if ($targetUser) {
            FinanceNotification::send(
                $targetUser->id,
                'penarikan_tabungan',
                'Penarikan Saldo Bank Sampah Berhasil',
                "Penarikan saldo tabungan Anda sebesar Rp " . number_format($request->jumlah_penarikan, 0, ',', '.') . " ({$request->metode_penarikan}) berhasil diproses oleh {$user->name}. Sisa saldo: Rp " . number_format($saldoAktif - $request->jumlah_penarikan, 0, ',', '.'),
                ['nominal' => $request->jumlah_penarikan, 'link_url' => route('bank_sampah.index', ['tab' => 'ringkasan'])]
            );
        }

        AuditLogger::log(
            'create',
            'Bank Sampah Penarikan',
            $penarikan->id,
            "Penarikan saldo tabungan {$nomorWd} oleh {$warga->nama} (RT {$warga->rt_number}) senilai Rp " . number_format($request->jumlah_penarikan, 0, ',', '.') . " melalui {$request->metode_penarikan}.",
            null,
            $penarikan->toArray(),
            $warga->rt_number
        );

        return redirect()->route('bank_sampah.index', ['tab' => 'setoran'])
            ->with('success', "Penarikan tabungan {$nomorWd} sebesar Rp " . number_format($request->jumlah_penarikan, 0, ',', '.') . " berhasil diproses!");
    }

    /**
     * View detailed citizen waste bank passbook (Ledger).
     */
    public function detailTabunganWarga($id)
    {
        $user = Auth::user();
        $warga = Warga::findOrFail($id);

        if ($user->isAdminRt() && $user->rt_number !== $warga->rt_number) {
            return redirect()->route('bank_sampah.index')->with('error', 'Akses ditolak.');
        }

        $setorans = BankSampah::where('warga_id', $warga->id)->orderBy('tanggal_setor', 'desc')->get();
        $penarikans = BankSampahPenarikan::where('warga_id', $warga->id)->orderBy('tanggal_penarikan', 'desc')->get();

        $totalSetoran = $setorans->where('status', 'diterima')->sum('nilai_rupiah');
        $totalKg = $setorans->where('status', 'diterima')->sum('berat_kg');
        $totalPenarikan = $penarikans->where('status', 'selesai')->sum('jumlah_penarikan');
        $saldo = max(0, $totalSetoran - $totalPenarikan);

        return view('bank_sampah.tabungan_detail', compact(
            'warga',
            'setorans',
            'penarikans',
            'totalSetoran',
            'totalKg',
            'totalPenarikan',
            'saldo'
        ));
    }

    /**
     * Admin RW: Store educational article.
     */
    public function storeEdukasi(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdminRw()) {
            return redirect()->route('bank_sampah.index')->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'judul' => 'required|string|max:255',
            'kategori_materi' => 'required|string|max:100',
            'deskripsi_singkat' => 'required|string|max:500',
            'isi_materi' => 'required|string',
            'icon' => 'nullable|string|max:50',
        ]);

        BankSampahEdukasi::create([
            'judul' => $request->judul,
            'kategori_materi' => $request->kategori_materi,
            'deskripsi_singkat' => $request->deskripsi_singkat,
            'isi_materi' => $request->isi_materi,
            'icon' => $request->icon ?: 'fa-book-open',
            'status' => 'published',
            'created_by' => $user->id,
            'published_at' => now(),
        ]);

        return redirect()->route('bank_sampah.index', ['tab' => 'edukasi'])
            ->with('success', "Materi edukasi lingkungan '{$request->judul}' berhasil diterbitkan!");
    }

    /**
     * Admin RW: Update educational article.
     */
    public function updateEdukasi(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isAdminRw()) {
            return redirect()->route('bank_sampah.index')->with('error', 'Akses ditolak.');
        }

        $article = BankSampahEdukasi::findOrFail($id);

        $request->validate([
            'judul' => 'required|string|max:255',
            'kategori_materi' => 'required|string|max:100',
            'deskripsi_singkat' => 'required|string|max:500',
            'isi_materi' => 'required|string',
            'status' => 'required|in:published,draft,archived',
        ]);

        $article->update([
            'judul' => $request->judul,
            'kategori_materi' => $request->kategori_materi,
            'deskripsi_singkat' => $request->deskripsi_singkat,
            'isi_materi' => $request->isi_materi,
            'status' => $request->status,
        ]);

        return redirect()->route('bank_sampah.index', ['tab' => 'edukasi'])
            ->with('success', "Materi edukasi '{$article->judul}' berhasil diperbarui!");
    }

    /**
     * Admin RW: Delete / archive educational article.
     */
    public function destroyEdukasi($id)
    {
        $user = Auth::user();
        if (!$user->isAdminRw()) {
            return redirect()->route('bank_sampah.index')->with('error', 'Akses ditolak.');
        }

        $article = BankSampahEdukasi::findOrFail($id);
        $article->delete();

        return redirect()->route('bank_sampah.index', ['tab' => 'edukasi'])
            ->with('success', "Materi edukasi berhasil dihapus.");
    }

    /**
     * Export deposits to CSV.
     */
    public function exportCsv(Request $request)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            abort(403, 'Akses ditolak. Fitur ekspor laporan bank sampah hanya untuk pengurus lingkungan.');
        }

        $query = BankSampah::query()->orderBy('tanggal_setor', 'desc');

        if ($user->isAdminRt()) {
            $query->where('rt_number', $user->rt_number);
        } elseif ($request->filled('rt_number')) {
            $query->where('rt_number', $request->rt_number);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_setor', $request->tahun);
        }
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_setor', $request->bulan);
        }
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $records = $query->get();

        $filename = 'Laporan_Bank_Sampah_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($records) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel
            fputs($file, "\xEF\xBB\xBF");

            // CSV Header
            fputcsv($file, [
                'No Transaksi',
                'Tanggal Setor',
                'Nama Penyetor',
                'Wilayah RT',
                'Kategori Sampah',
                'Berat/Volume',
                'Satuan',
                'Harga Satuan (Rp)',
                'Total Nilai (Rp)',
                'Status',
                'Petugas',
                'Catatan',
            ]);

            foreach ($records as $r) {
                fputcsv($file, [
                    $r->nomor_transaksi,
                    Carbon::parse($r->tanggal_setor)->format('Y-m-d'),
                    $r->nama_penyetor,
                    'RT ' . $r->rt_number,
                    ucfirst($r->kategori),
                    $r->berat_kg,
                    $r->satuan,
                    $r->harga_per_satuan,
                    $r->nilai_rupiah,
                    ucfirst($r->status),
                    $r->petugas,
                    $r->catatan,
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Printable Official Bank Sampah Report (HTML/PDF view).
     */
    public function cetakLaporan(Request $request)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            abort(403, 'Akses ditolak. Fitur cetak laporan bank sampah hanya untuk pengurus lingkungan.');
        }

        $query = BankSampah::where('status', 'diterima')->orderBy('tanggal_setor', 'desc');

        if ($user->isAdminRt()) {
            $query->where('rt_number', $user->rt_number);
        } elseif ($request->filled('rt_number')) {
            $query->where('rt_number', $request->rt_number);
        }

        $tahun = $request->query('tahun', date('Y'));
        $bulan = $request->query('bulan', date('m'));
        $rtFilter = $user->isAdminRt() ? $user->rt_number : $request->query('rt_number');

        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_setor', $tahun);
        }
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_setor', $bulan);
        }

        $records = $query->get();
        $totalKg = $records->sum('berat_kg');
        $totalRp = $records->sum('nilai_rupiah');
        $totalTx = $records->count();

        return view('bank_sampah.cetak_laporan', compact(
            'records',
            'totalKg',
            'totalRp',
            'totalTx',
            'tahun',
            'bulan',
            'rtFilter'
        ));
    }
}
