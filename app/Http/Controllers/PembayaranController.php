<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\PembayaranIuran;
use App\Models\Iuran;
use App\Models\Warga;
use App\Models\User;
use App\Models\LaporanRt;
use App\Models\SuratPengajuan;
use App\Models\Pengaduan;
use App\Models\BankSampah;
use App\Models\JadwalRonda;
use App\Models\AgendaRapat;
use App\Models\MutasiWarga;
use App\Models\FinanceNotification;
use App\Services\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PembayaranController extends Controller
{
    /**
     * 1. TRANSAKSI KAS — ADMIN RW & ADMIN RT & WARGA (TRANSPARANSI)
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // JIKA WARGA: Tampilkan ringkasan transparansi kas lingkungan publik (Point 9.F)
        if ($user->role === 'warga') {
            return $this->wargaTransparansiKas($request);
        }

        $query = Pembayaran::with(['warga', 'creator'])->orderBy('tanggal', 'desc')->orderBy('id', 'desc');

        // RBAC: Admin RT hanya melihat transaksi RT-nya
        if ($user->isAdminRt()) {
            $query->where('rt_number', $user->rt_number);
        } elseif ($request->filled('rt_number') && $request->rt_number !== 'all') {
            $query->where('rt_number', $request->rt_number);
        }

        // Filter Pencarian Teks
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function($q) use ($s) {
                $q->where('nomor_transaksi', 'like', "%{$s}%")
                  ->orWhere('keterangan', 'like', "%{$s}%")
                  ->orWhere('sumber_tujuan', 'like', "%{$s}%")
                  ->orWhereHas('warga', function($wq) use ($s) {
                      $wq->where('nama', 'like', "%{$s}%")
                         ->orWhere('nik', 'like', "%{$s}%");
                  });
            });
        }

        // Filter Tipe (masuk / keluar)
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        // Filter Kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter Status Transaksi
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter Tanggal
        if ($request->filled('tgl_mulai')) {
            $query->where('tanggal', '>=', $request->tgl_mulai);
        }
        if ($request->filled('tgl_akhir')) {
            $query->where('tanggal', '<=', $request->tgl_akhir);
        }

        $pembayarans = $query->paginate(15)->withQueryString();

        // ==========================================
        // PERHITUNGAN RINGKASAN KEUANGAN AKTUAL (Point 3.B)
        // Saldo = Total Pemasukan Valid - Total Pengeluaran Valid
        // ==========================================
        $baseStatQuery = Pembayaran::query()->where('status', '!=', 'dibatalkan')->where('status', '!=', 'pending');
        if ($user->isAdminRt()) {
            $baseStatQuery->where('rt_number', $user->rt_number);
        } elseif ($request->filled('rt_number') && $request->rt_number !== 'all') {
            $baseStatQuery->where('rt_number', $request->rt_number);
        }

        $totalPemasukan = (clone $baseStatQuery)->where('tipe', 'masuk')->sum('jumlah');
        $totalPengeluaran = (clone $baseStatQuery)->where('tipe', 'keluar')->sum('jumlah');
        $saldoKasSaatIni = $totalPemasukan - $totalPengeluaran;

        // Pemasukan & Pengeluaran Bulan Berjalan
        $currMonth = date('m');
        $currYear = date('Y');
        $pemasukanBulanIni = (clone $baseStatQuery)->where('tipe', 'masuk')->whereMonth('tanggal', $currMonth)->whereYear('tanggal', $currYear)->sum('jumlah');
        $pengeluaranBulanIni = (clone $baseStatQuery)->where('tipe', 'keluar')->whereMonth('tanggal', $currMonth)->whereYear('tanggal', $currYear)->sum('jumlah');

        // ==========================================
        // GRAFIK ARUS KAS DENGAN FILTER PERIODE (Point 3.C)
        // ==========================================
        $periodeFilter = $request->get('periode', 'ytd'); // default: tahun 2026 berjalan (YTD) agar seluruh data riil tampil
        $chartData = $this->generateCashflowChartData($user, $periodeFilter, $request->get('rt_number'));

        // ==========================================
        // BREAKDOWN KATEGORI PEMASUKAN & PENGELUARAN (Point 3.D)
        // ==========================================
        $kategoriMasuk = (clone $baseStatQuery)->where('tipe', 'masuk')
            ->select('kategori', DB::raw('SUM(jumlah) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('kategori')->orderByDesc('total')->get();

        $kategoriKeluar = (clone $baseStatQuery)->where('tipe', 'keluar')
            ->select('kategori', DB::raw('SUM(jumlah) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('kategori')->orderByDesc('total')->get();

        // Master lists for filters & forms
        $rtList = ['006', '007', '008', '009', '010'];
        $wargaQuery = Warga::query();
        if ($user->isAdminRt()) {
            $wargaQuery->where('rt_number', $user->rt_number);
        }
        $wargas = $wargaQuery->orderBy('nama')->get();

        // Kategori bawaan
        $kategoriMasukList = ['Iuran Bulanan', 'Donasi Warga', 'Iuran Sukarela', 'Bantuan Sosial', 'Pendapatan Bank Sampah', 'Pemasukan Kas Lainnya'];
        $kategoriKeluarList = ['Keamanan & Ronda', 'Kebersihan & Sampah', 'Listrik & Air Fasum', 'Perbaikan & Maintenance', 'Kegiatan Masyarakat', 'Operasional & ATK', 'Pengeluaran Lainnya'];

        return view('pembayaran.index', compact(
            'pembayarans',
            'saldoKasSaatIni',
            'totalPemasukan',
            'totalPengeluaran',
            'pemasukanBulanIni',
            'pengeluaranBulanIni',
            'chartData',
            'periodeFilter',
            'kategoriMasuk',
            'kategoriKeluar',
            'kategoriMasukList',
            'kategoriKeluarList',
            'rtList',
            'wargas'
        ));
    }

    /**
     * Helper membuat data grafik arus kas multi-periode (7d, 6m, 12m, ytd)
     */
    private function generateCashflowChartData($user, $periode, $rtFilter = null)
    {
        $chartQuery = Pembayaran::query()->where('status', '!=', 'dibatalkan')->where('status', '!=', 'pending');
        if ($user->isAdminRt()) {
            $chartQuery->where('rt_number', $user->rt_number);
        } elseif ($rtFilter && $rtFilter !== 'all') {
            $chartQuery->where('rt_number', $rtFilter);
        }

        if ($periode === '7d') {
            // 7 Hari Terakhir
            $chartQuery->where('tanggal', '>=', now()->subDays(6)->toDateString());
            $raw = $chartQuery->get()->groupBy(function($item) {
                return Carbon::parse($item->tanggal)->format('Y-m-d');
            });

            $result = collect();
            for ($i = 6; $i >= 0; $i--) {
                $d = now()->subDays($i);
                $key = $d->format('Y-m-d');
                $group = $raw->get($key);
                $m = $group ? (float)$group->where('tipe', 'masuk')->sum('jumlah') : 0;
                $k = $group ? (float)$group->where('tipe', 'keluar')->sum('jumlah') : 0;
                $result->push((object)[
                    'label' => $d->translatedFormat('d M'),
                    'label_full' => $d->translatedFormat('d F Y'),
                    'masuk' => $m,
                    'keluar' => $k,
                    'net' => $m - $k,
                ]);
            }
            return $result;
        }

        if ($periode === '6m') {
            // 6 Bulan Terakhir
            $chartQuery->where('tanggal', '>=', now()->subMonths(5)->startOfMonth()->toDateString());
            $monthsCount = 6;
        } elseif ($periode === '12m') {
            // 12 Bulan Terakhir
            $chartQuery->where('tanggal', '>=', now()->subMonths(11)->startOfMonth()->toDateString());
            $monthsCount = 12;
        } else {
            // Default: Tahun 2026 Berjalan (12 Bulan Lengkap Jan - Des)
            $year = 2026;
            $chartQuery->whereYear('tanggal', $year);
            $raw = $chartQuery->get()->groupBy(function($item) {
                return Carbon::parse($item->tanggal)->format('Y-m');
            });

            $result = collect();
            for ($m = 1; $m <= 12; $m++) {
                $monthDate = Carbon::create($year, $m, 1);
                $key = $monthDate->format('Y-m');
                $group = $raw->get($key);
                $mVal = $group ? (float)$group->where('tipe', 'masuk')->sum('jumlah') : 0;
                $kVal = $group ? (float)$group->where('tipe', 'keluar')->sum('jumlah') : 0;
                $result->push((object)[
                    'label' => $monthDate->translatedFormat('M'),
                    'label_full' => $monthDate->translatedFormat('F Y'),
                    'masuk' => $mVal,
                    'keluar' => $kVal,
                    'net' => $mVal - $kVal,
                ]);
            }
            return $result;
        }

        $raw = $chartQuery->get()->groupBy(function($item) {
            return Carbon::parse($item->tanggal)->format('Y-m');
        });

        $result = collect();
        for ($i = $monthsCount - 1; $i >= 0; $i--) {
            $monthDate = now()->subMonths($i);
            $key = $monthDate->format('Y-m');
            $group = $raw->get($key);
            $mVal = $group ? (float)$group->where('tipe', 'masuk')->sum('jumlah') : 0;
            $kVal = $group ? (float)$group->where('tipe', 'keluar')->sum('jumlah') : 0;
            $result->push((object)[
                'label' => $monthDate->translatedFormat('M Y'),
                'label_full' => $monthDate->translatedFormat('F Y'),
                'masuk' => $mVal,
                'keluar' => $kVal,
                'net' => $mVal - $kVal,
            ]);
        }
        return $result;
    }

    /**
     * Transparansi Kas Lingkungan untuk Warga (Section 9.F)
     */
    private function wargaTransparansiKas(Request $request)
    {
        $user = Auth::user();
        $currMonth = date('m');
        $currYear = date('Y');

        // Ringkasan Agregat Publik RW & RT
        $rwMasuk = Pembayaran::where('status', '!=', 'dibatalkan')->where('tipe', 'masuk')->sum('jumlah');
        $rwKeluar = Pembayaran::where('status', '!=', 'dibatalkan')->where('tipe', 'keluar')->sum('jumlah');
        $rwSaldo = $rwMasuk - $rwKeluar;

        $rtMasuk = Pembayaran::where('status', '!=', 'dibatalkan')->where('rt_number', $user->rt_number)->where('tipe', 'masuk')->sum('jumlah');
        $rtKeluar = Pembayaran::where('status', '!=', 'dibatalkan')->where('rt_number', $user->rt_number)->where('tipe', 'keluar')->sum('jumlah');
        $rtSaldo = $rtMasuk - $rtKeluar;

        // Distribusi Pengeluaran Publik
        $pengeluaranKategori = Pembayaran::where('status', '!=', 'dibatalkan')
            ->where('tipe', 'keluar')
            ->select('kategori', DB::raw('SUM(jumlah) as total'))
            ->groupBy('kategori')->orderByDesc('total')->get();

        // Daftar transaksi publik (tanpa nama warga/NIK/rekening/dokumen pribadi)
        $transaksiPublik = Pembayaran::where('status', 'selesai')
            ->select('nomor_transaksi', 'tipe', 'jumlah', 'kategori', 'keterangan', 'sumber_tujuan', 'tanggal', 'rt_number')
            ->orderBy('tanggal', 'desc')
            ->paginate(15);

        return view('pembayaran.warga_transparansi', compact(
            'rwMasuk', 'rwKeluar', 'rwSaldo',
            'rtMasuk', 'rtKeluar', 'rtSaldo',
            'pengeluaranKategori', 'transaksiPublik'
        ));
    }

    /**
     * 2. CATAT TRANSAKSI KAS BARU (Point 4)
     */
    public function create()
    {
        $user = Auth::user();
        if ($user->role === 'warga') return redirect()->route('dashboard');

        $wargaQuery = Warga::query();
        if ($user->isAdminRt()) {
            $wargaQuery->where('rt_number', $user->rt_number);
        }
        $warga = $wargaQuery->orderBy('nama')->get();

        $kategoriMasuk = ['Iuran Bulanan', 'Donasi Warga', 'Iuran Sukarela', 'Bantuan Sosial', 'Pendapatan Bank Sampah', 'Pemasukan Kas Lainnya'];
        $kategoriKeluar = ['Keamanan & Ronda', 'Kebersihan & Sampah', 'Listrik & Air Fasum', 'Perbaikan & Maintenance', 'Kegiatan Masyarakat', 'Operasional & ATK', 'Pengeluaran Lainnya'];

        return view('pembayaran.create', compact('warga', 'kategoriMasuk', 'kategoriKeluar'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->role === 'warga') return redirect()->route('dashboard');

        $request->validate([
            'tipe' => 'required|in:masuk,keluar',
            'jumlah' => 'required|numeric|min:1',
            'kategori' => 'required|string|max:100',
            'tanggal' => 'required|date',
            'keterangan' => 'required|string|max:1000',
            'sumber_tujuan' => 'nullable|string|max:255',
            'metode_pembayaran' => 'nullable|string|max:50',
            'nomor_referensi' => 'nullable|string|max:100',
            'warga_id' => 'nullable|exists:wargas,id',
            'bukti_transaksi' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        $rt_number = '000';
        if ($user->isAdminRt()) {
            $rt_number = $user->rt_number;
        } elseif ($request->filled('rt_number')) {
            $rt_number = str_pad($request->rt_number, 3, '0', STR_PAD_LEFT);
        } elseif ($request->filled('warga_id')) {
            $w = Warga::find($request->warga_id);
            if ($w) $rt_number = $w->rt_number;
        }

        // Upload bukti transaksi jika ada
        $buktiPath = null;
        if ($request->hasFile('bukti_transaksi')) {
            $buktiPath = $request->file('bukti_transaksi')->store('bukti_transaksi', 'public');
        }

        // Generate nomor transaksi
        $nextId = (Pembayaran::max('id') ?? 0) + 1;
        $year = date('Y', strtotime($request->tanggal));
        $nomorTx = sprintf('TRX-%s-%05d', $year, $nextId);

        $tx = Pembayaran::create([
            'nomor_transaksi' => $nomorTx,
            'warga_id' => $request->warga_id,
            'rt_number' => $rt_number,
            'tipe' => $request->tipe,
            'jumlah' => $request->jumlah,
            'kategori' => $request->kategori,
            'keterangan' => $request->keterangan,
            'sumber_tujuan' => $request->sumber_tujuan ?: ($request->tipe === 'masuk' ? 'Pemasukan Lingkungan' : 'Pengeluaran Lingkungan'),
            'metode_pembayaran' => $request->metode_pembayaran ?: 'Tunai',
            'nomor_referensi' => $request->nomor_referensi,
            'bukti_transaksi' => $buktiPath,
            'status' => 'selesai',
            'created_by' => $user->id,
            'tanggal' => $request->tanggal,
        ]);

        // Audit Logging (Point 13)
        AuditLogger::log(
            'create',
            'Transaksi Kas',
            $tx->id,
            "Pencatatan kas {$tx->tipe} sebesar Rp " . number_format($tx->jumlah) . " ({$tx->kategori}) pada {$tx->nomor_transaksi} oleh {$user->name}.",
            null,
            $tx->toArray(),
            $rt_number
        );

        return redirect()->route('pembayaran.index')->with('success', "Transaksi {$tx->nomor_transaksi} berhasil dicatat!");
    }

    public function show($id)
    {
        $user = Auth::user();
        $tx = Pembayaran::with(['warga', 'creator', 'pembayaranIuran'])->findOrFail($id);

        if ($user->isAdminRt() && $tx->rt_number !== $user->rt_number && $tx->rt_number !== '000') {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        return response()->json([
            'id' => $tx->id,
            'nomor_transaksi' => $tx->nomor_transaksi_formatted,
            'tipe' => $tx->tipe,
            'jumlah' => $tx->jumlah,
            'jumlah_formatted' => 'Rp ' . number_format($tx->jumlah, 0, ',', '.'),
            'kategori' => $tx->kategori,
            'keterangan' => $tx->keterangan ?: '-',
            'sumber_tujuan' => $tx->sumber_tujuan ?: '-',
            'rt_number' => $tx->rt_number === '000' ? 'RW 018' : 'RT ' . $tx->rt_number,
            'metode_pembayaran' => $tx->metode_pembayaran ?: 'Tunai',
            'nomor_referensi' => $tx->nomor_referensi ?: '-',
            'status' => $tx->status ?: 'selesai',
            'tanggal' => $tx->tanggal ? $tx->tanggal->translatedFormat('d F Y') : '-',
            'warga_nama' => $tx->warga ? $tx->warga->nama : '-',
            'created_by_name' => $tx->creator ? $tx->creator->name : '-',
            'bukti_url' => $tx->bukti_transaksi ? asset('storage/' . $tx->bukti_transaksi) : null,
        ]);
    }

    public function edit($id)
    {
        $user = Auth::user();
        if ($user->role === 'warga') return redirect()->route('dashboard');

        $pembayaran = Pembayaran::findOrFail($id);
        if ($user->isAdminRt() && $pembayaran->rt_number !== $user->rt_number) {
            return redirect()->route('pembayaran.index')->with('error', 'Akses ditolak: Anda hanya dapat mengubah transaksi RT Anda.');
        }

        $wargaQuery = Warga::query();
        if ($user->isAdminRt()) {
            $wargaQuery->where('rt_number', $user->rt_number);
        }
        $warga = $wargaQuery->orderBy('nama')->get();

        $kategoriMasuk = ['Iuran Bulanan', 'Donasi Warga', 'Iuran Sukarela', 'Bantuan Sosial', 'Pendapatan Bank Sampah', 'Pemasukan Kas Lainnya'];
        $kategoriKeluar = ['Keamanan & Ronda', 'Kebersihan & Sampah', 'Listrik & Air Fasum', 'Perbaikan & Maintenance', 'Kegiatan Masyarakat', 'Operasional & ATK', 'Pengeluaran Lainnya'];

        return view('pembayaran.edit', compact('pembayaran', 'warga', 'kategoriMasuk', 'kategoriKeluar'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->role === 'warga') return redirect()->route('dashboard');

        $pembayaran = Pembayaran::findOrFail($id);
        if ($user->isAdminRt() && $pembayaran->rt_number !== $user->rt_number) {
            return redirect()->route('pembayaran.index')->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'tipe' => 'required|in:masuk,keluar',
            'jumlah' => 'required|numeric|min:1',
            'kategori' => 'required|string|max:100',
            'tanggal' => 'required|date',
            'keterangan' => 'required|string|max:1000',
            'sumber_tujuan' => 'nullable|string|max:255',
            'metode_pembayaran' => 'nullable|string|max:50',
            'nomor_referensi' => 'nullable|string|max:100',
            'warga_id' => 'nullable|exists:wargas,id',
            'bukti_transaksi' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        $oldValues = $pembayaran->toArray();

        $data = $request->only([
            'tipe', 'jumlah', 'kategori', 'keterangan', 'sumber_tujuan',
            'metode_pembayaran', 'nomor_referensi', 'tanggal', 'warga_id'
        ]);

        if ($request->hasFile('bukti_transaksi')) {
            if ($pembayaran->bukti_transaksi && Storage::disk('public')->exists($pembayaran->bukti_transaksi)) {
                Storage::disk('public')->delete($pembayaran->bukti_transaksi);
            }
            $data['bukti_transaksi'] = $request->file('bukti_transaksi')->store('bukti_transaksi', 'public');
        }

        $pembayaran->update($data);

        // Audit Logging
        AuditLogger::log(
            'update',
            'Transaksi Kas',
            $pembayaran->id,
            "Perubahan transaksi {$pembayaran->nomor_transaksi_formatted} sebesar Rp " . number_format($pembayaran->jumlah) . " oleh {$user->name}.",
            $oldValues,
            $pembayaran->toArray(),
            $pembayaran->rt_number
        );

        return redirect()->route('pembayaran.index')->with('success', "Transaksi {$pembayaran->nomor_transaksi_formatted} berhasil diperbarui.");
    }

    public function cancel(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->role === 'warga') return redirect()->route('dashboard');

        $pembayaran = Pembayaran::findOrFail($id);
        if ($user->isAdminRt() && $pembayaran->rt_number !== $user->rt_number) {
            return redirect()->route('pembayaran.index')->with('error', 'Akses ditolak.');
        }

        $pembayaran->status = 'dibatalkan';
        $pembayaran->keterangan = $pembayaran->keterangan . " (Dibatalkan oleh {$user->name}: " . ($request->alasan ?? 'Pembatalan transaksi') . ")";
        $pembayaran->save();

        AuditLogger::log(
            'cancel',
            'Transaksi Kas',
            $pembayaran->id,
            "Transaksi {$pembayaran->nomor_transaksi_formatted} dibatalkan oleh {$user->name}. Alasan: " . ($request->alasan ?? '-'),
            ['status' => 'selesai'],
            ['status' => 'dibatalkan'],
            $pembayaran->rt_number
        );

        return redirect()->route('pembayaran.index')->with('success', "Transaksi {$pembayaran->nomor_transaksi_formatted} berhasil dibatalkan.");
    }

    public function destroy($id)
    {
        $user = Auth::user();
        if ($user->role === 'warga') return redirect()->route('dashboard');

        $pembayaran = Pembayaran::findOrFail($id);
        if ($user->isAdminRt() && $pembayaran->rt_number !== $user->rt_number) {
            return redirect()->route('pembayaran.index')->with('error', 'Akses ditolak.');
        }

        // Arsipkan/batalkan secara aman tanpa merusak keutuhan riwayat
        $pembayaran->status = 'dibatalkan';
        $pembayaran->save();

        AuditLogger::log(
            'delete',
            'Transaksi Kas',
            $pembayaran->id,
            "Transaksi {$pembayaran->nomor_transaksi_formatted} diarsipkan/dihapus oleh {$user->name}.",
            null,
            ['status' => 'dibatalkan'],
            $pembayaran->rt_number
        );

        return redirect()->route('pembayaran.index')->with('success', "Transaksi {$pembayaran->nomor_transaksi_formatted} berhasil diarsipkan.");
    }

    /**
     * Ekspor Transaksi Kas ke format CSV (Excel Friendly UTF-8 BOM)
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        if ($user->role === 'warga') return redirect()->route('dashboard');

        $query = Pembayaran::with('warga')->orderBy('tanggal', 'desc');
        if ($user->isAdminRt()) {
            $query->where('rt_number', $user->rt_number);
        } elseif ($request->filled('rt_number') && $request->rt_number !== 'all') {
            $query->where('rt_number', $request->rt_number);
        }
        if ($request->filled('tipe')) $query->where('tipe', $request->tipe);
        if ($request->filled('kategori')) $query->where('kategori', $request->kategori);
        if ($request->filled('status')) $query->where('status', $request->status);

        $filename = 'transaksi_kas_' . ($user->isAdminRt() ? 'rt_' . $user->rt_number . '_' : 'rw018_') . date('Ymd_His') . '.csv';
        $txs = $query->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function() use ($txs) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel

            fputcsv($handle, [
                'No', 'Nomor Transaksi', 'Tanggal', 'Jenis', 'Kategori', 'Keterangan',
                'Sumber / Tujuan', 'RT Terkait', 'Nominal (Rp)', 'Status', 'Metode Pembayaran', 'Nomor Referensi'
            ]);

            $no = 1;
            foreach ($txs as $t) {
                fputcsv($handle, [
                    $no++,
                    $t->nomor_transaksi_formatted,
                    $t->tanggal ? $t->tanggal->format('Y-m-d') : '-',
                    strtoupper($t->tipe),
                    $t->kategori,
                    $t->keterangan ?: '-',
                    $t->sumber_tujuan ?: '-',
                    $t->rt_number === '000' ? 'RW 018' : 'RT ' . $t->rt_number,
                    $t->jumlah,
                    ucfirst($t->status ?: 'selesai'),
                    $t->metode_pembayaran ?: 'Tunai',
                    $t->nomor_referensi ?: '-',
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }

    public function cetakLaporan()
    {
        return redirect()->route('laporan.index');
    }

    /**
     * 3. IURAN WARGA (Point 5, 8.C, 9)
     * Mengelola tagihan iuran, rekap KK, master jenis iuran, dan antrean konfirmasi
     */
    public function laporanIuran(Request $request)
    {
        $user = Auth::user();
        $bulan = (int)$request->get('bulan', date('m'));
        $tahun = (int)$request->get('tahun', date('Y'));
        $rtFilter = $request->get('rt_number');

        if ($user->isAdminRt()) {
            $rtFilter = $user->rt_number;
        }

        $rtList = ['006', '007', '008', '009', '010'];
        $daftarTahun = range(date('Y'), date('Y') - 5);

        // ==========================================
        // KHUSUS WARGA: TAMPILAN DASHBOARD IURAN SAYA (Point 9)
        // ==========================================
        if ($user->role === 'warga') {
            return $this->wargaDashboardIuran($user, $bulan, $tahun);
        }

        // ==========================================
        // ADMIN RW & ADMIN RT: MONITORING & TAGIHAN IURAN
        // ==========================================
        $masterIurans = Iuran::orderBy('is_active', 'desc')->orderBy('name')->get();

        // 1. Antrean Pembayaran Menunggu Konfirmasi (Point 5.D & 5.F)
        $antreanQuery = PembayaranIuran::with(['warga', 'iuran', 'user'])->where('status', 'pending')->orderBy('created_at', 'desc');
        if ($user->isAdminRt()) {
            $antreanQuery->where('rt_number', $user->rt_number);
        } elseif ($rtFilter && $rtFilter !== 'all') {
            $antreanQuery->where('rt_number', $rtFilter);
        }
        $antreanKonfirmasi = $antreanQuery->get();

        // 2. Daftar Kepala Keluarga untuk Rekap Iuran
        $wargasQuery = Warga::where('is_kk', true)->where('status_warga', '!=', 'pindah')->orderBy('rt_number')->orderBy('nama');
        if ($rtFilter && $rtFilter !== 'all') {
            $wargasQuery->where('rt_number', $rtFilter);
        }
        $wargas = $wargasQuery->get();

        $rows = [];
        $totalTagihanNominal = 0;
        $totalPembayaranDiterima = 0;
        $totalTunggakanNominal = 0;
        $countLunas = 0;
        $countBelumLunas = 0;

        $standardNominal = 100000; // Iuran standar kebersihan & keamanan lingkungan

        foreach ($wargas as $warga) {
            $tagihan = $standardNominal;
            $totalTagihanNominal += $tagihan;

            // Cari bukti pembayaran di 'transactions' ATAU 'pembayarans' untuk bulan & tahun ini
            $txPayment = Pembayaran::where('warga_id', $warga->id)
                ->where('status', '!=', 'dibatalkan')
                ->where('tipe', 'masuk')
                ->where('kategori', 'like', '%Iuran%')
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->first();

            $pembayaranItem = PembayaranIuran::where('warga_id', $warga->id)
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->orderByDesc('id')
                ->first();

            $status = 'Belum Bayar';
            $nominalDibayar = 0;
            $tanggalBayar = null;
            $metodeBayar = '-';
            $pembayaranId = null;

            if ($pembayaranItem && $pembayaranItem->status === 'pending') {
                $status = 'Menunggu Konfirmasi';
                $nominalDibayar = $pembayaranItem->amount_paid;
                $tanggalBayar = $pembayaranItem->payment_date;
                $metodeBayar = $pembayaranItem->payment_method;
                $pembayaranId = $pembayaranItem->id;
            } elseif ($txPayment || ($pembayaranItem && $pembayaranItem->status === 'confirmed')) {
                $status = 'Lunas';
                $nominalDibayar = $txPayment ? $txPayment->jumlah : ($pembayaranItem ? $pembayaranItem->amount_paid : $standardNominal);
                $tanggalBayar = $txPayment ? $txPayment->tanggal : ($pembayaranItem ? $pembayaranItem->payment_date : null);
                $metodeBayar = $txPayment ? ($txPayment->metode_pembayaran ?: 'Tunai') : ($pembayaranItem ? $pembayaranItem->payment_method : 'Tunai');
                $pembayaranId = $pembayaranItem ? $pembayaranItem->id : ($txPayment ? $txPayment->id : null);
                $countLunas++;
                $totalPembayaranDiterima += $nominalDibayar;
            } else {
                $countBelumLunas++;
                $totalTunggakanNominal += $tagihan;
            }

            // Riwayat 3 bulan sebelumnya untuk indikator ketaatan
            $selectedDate = Carbon::createFromDate($tahun, $bulan, 1);
            $history = [];
            for ($i = 1; $i <= 3; $i++) {
                $dt = $selectedDate->copy()->subMonths($i);
                $has = Pembayaran::where('warga_id', $warga->id)
                    ->where('status', '!=', 'dibatalkan')
                    ->where('tipe', 'masuk')
                    ->where('kategori', 'like', '%Iuran%')
                    ->whereMonth('tanggal', $dt->month)
                    ->whereYear('tanggal', $dt->year)
                    ->exists();
                $history[] = [
                    'month' => $dt->format('M Y'),
                    'paid' => $has,
                ];
            }

            $rows[] = [
                'warga' => $warga,
                'jenis_iuran' => 'Iuran Lingkungan Terpadu',
                'periode' => sprintf('%02d/%d', $bulan, $tahun),
                'nominal_tagihan' => $tagihan,
                'nominal_dibayar' => $nominalDibayar,
                'tanggal_bayar' => $tanggalBayar,
                'metode_bayar' => $metodeBayar,
                'status' => $status,
                'history' => $history,
                'pembayaran_id' => $pembayaranId,
            ];
        }

        // Persentase Pembayaran
        $totalKKCount = count($wargas) ?: 1;
        $persenLunas = round(($countLunas / $totalKKCount) * 100, 1);

        return view('laporan.iuran', compact(
            'rows',
            'masterIurans',
            'antreanKonfirmasi',
            'bulan',
            'tahun',
            'daftarTahun',
            'rtFilter',
            'rtList',
            'totalTagihanNominal',
            'totalPembayaranDiterima',
            'totalTunggakanNominal',
            'countLunas',
            'countBelumLunas',
            'persenLunas'
        ));
    }

    /**
     * Dashboard & Tagihan Warga Mandiri (Point 9)
     */
    private function wargaDashboardIuran($user, $bulan, $tahun)
    {
        // Temukan data warga untuk akun yang login
        $warga = $user->resolveWarga();
        $wargaId = $warga ? $warga->id : 0;
        $standardNominal = 100000;

        // 1. Tagihan Aktif Bulan Terpilih
        $paidTx = Pembayaran::where('warga_id', $wargaId)
            ->where('status', '!=', 'dibatalkan')
            ->where('tipe', 'masuk')
            ->where('kategori', 'like', '%Iuran%')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->first();

        $paidPending = PembayaranIuran::where('warga_id', $wargaId)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->orderByDesc('id')
            ->first();

        $statusTagihan = 'Belum Bayar';
        if ($paidPending && $paidPending->status === 'pending') {
            $statusTagihan = 'Menunggu Konfirmasi';
        } elseif ($paidTx || ($paidPending && $paidPending->status === 'confirmed')) {
            $statusTagihan = 'Lunas';
        }

        // 2. Tagihan 6 Bulan Terakhir untuk rekap status
        $tagihanBulanan = collect();
        $totalTunggakan = 0;
        for ($i = 0; $i < 6; $i++) {
            $d = now()->subMonths($i);
            $m = (int)$d->format('m');
            $y = (int)$d->format('Y');

            $tx = Pembayaran::where('warga_id', $wargaId)
                ->where('status', '!=', 'dibatalkan')
                ->where('tipe', 'masuk')
                ->where('kategori', 'like', '%Iuran%')
                ->whereMonth('tanggal', $m)
                ->whereYear('tanggal', $y)
                ->first();

            $pPending = PembayaranIuran::where('warga_id', $wargaId)
                ->where('bulan', $m)
                ->where('tahun', $y)
                ->orderByDesc('id')
                ->first();

            $st = 'Belum Bayar';
            if ($pPending && $pPending->status === 'pending') {
                $st = 'Menunggu Konfirmasi';
            } elseif ($tx || ($pPending && $pPending->status === 'confirmed')) {
                $st = 'Lunas';
            } else {
                $totalTunggakan += $standardNominal;
            }

            $tagihanBulanan->push((object)[
                'periode' => $d->translatedFormat('F Y'),
                'bulan' => $m,
                'tahun' => $y,
                'nominal' => $standardNominal,
                'status' => $st,
                'tanggal_bayar' => $tx ? $tx->tanggal : ($pPending ? $pPending->payment_date : null),
                'pembayaran_id' => $pPending ? $pPending->id : ($tx ? $tx->id : null),
            ]);
        }

        // 3. Riwayat Pembayaran Saya
        $riwayatPembayaran = Pembayaran::where('warga_id', $wargaId)
            ->where('status', '!=', 'dibatalkan')
            ->where('tipe', 'masuk')
            ->orderBy('tanggal', 'desc')
            ->take(10)
            ->get();

        $masterIuran = Iuran::where('is_active', true)->first();

        return view('laporan.warga_iuran', compact(
            'warga',
            'bulan',
            'tahun',
            'statusTagihan',
            'standardNominal',
            'totalTunggakan',
            'tagihanBulanan',
            'riwayatPembayaran',
            'masterIuran'
        ));
    }

    /**
     * Tambah Jenis Iuran Baru oleh Admin RW (Point 5.B)
     */
    public function storeJenisIuran(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdminRw()) {
            return redirect()->back()->with('error', 'Hanya Admin RW yang berwenang membuat jenis iuran baru.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1000',
            'periode' => 'required|in:bulanan,mingguan,tahunan,sekali_bayar',
            'type' => 'required|in:wajib,sukarela,kebersihan,keamanan',
            'due_date' => 'required|date',
            'target_wilayah' => 'nullable|string',
            'description' => 'nullable|string|max:1000',
        ]);

        $iuran = Iuran::create([
            'name' => $request->name,
            'description' => $request->description,
            'amount' => $request->amount,
            'periode' => $request->periode,
            'due_date' => $request->due_date,
            'type' => $request->type,
            'target_wilayah' => $request->target_wilayah ?: 'all',
            'target_pembayar' => 'kk',
            'is_active' => true,
        ]);

        AuditLogger::log(
            'create',
            'Iuran Warga',
            $iuran->id,
            "Jenis iuran baru '{$iuran->name}' (Rp " . number_format($iuran->amount) . ") dibuat oleh {$user->name}."
        );

        // Kirim notifikasi tagihan baru kepada warga sasaran
        try {
            $targetWargas = Warga::where('is_kk', true)->where('status_warga', '!=', 'pindah');
            if ($iuran->target_wilayah && $iuran->target_wilayah !== 'all') {
                $targetWargas->where('rt_number', $iuran->target_wilayah);
            }
            $targetWargas = $targetWargas->get();

            $periodeLabel = Carbon::now()->translatedFormat('F Y');
            foreach ($targetWargas as $tw) {
                $u = User::where('nik', $tw->nik)->orWhere('no_kk', $tw->no_kk)->first();
                if ($u) {
                    FinanceNotification::send(
                        $u->id,
                        'tagihan_baru',
                        "Tagihan Iuran Baru: {$iuran->name}",
                        "Tagihan iuran '{$iuran->name}' periode {$periodeLabel} telah diterbitkan sebesar Rp " . number_format($iuran->amount, 0, ',', '.') . ". Batas jatuh tempo tanggal " . Carbon::parse($iuran->due_date)->translatedFormat('d F Y') . ". Silakan periksa detail pembayaran keluarga Anda.",
                        [
                            'warga_id' => $tw->id,
                            'nominal' => $iuran->amount,
                            'periode' => date('m/Y'),
                            'due_date' => $iuran->due_date,
                            'link_url' => route('laporan.iuran'),
                        ]
                    );
                }
            }
        } catch (\Throwable $e) {}

        return redirect()->back()->with('success', "Jenis iuran '{$iuran->name}' berhasil dibuat dan notifikasi telah dikirim ke warga!");
    }

    /**
     * Update Jenis Iuran oleh Admin RW
     */
    public function updateJenisIuran(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isAdminRw()) {
            return redirect()->back()->with('error', 'Hanya Admin RW yang berwenang mengubah jenis iuran.');
        }

        $iuran = Iuran::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1000',
            'periode' => 'nullable|string',
            'type' => 'nullable|string',
            'due_date' => 'nullable|date',
            'is_active' => 'nullable|boolean',
            'description' => 'nullable|string',
        ]);

        $data = $request->only(['name', 'amount', 'periode', 'type', 'due_date', 'description']);
        if ($request->has('is_active')) {
            $data['is_active'] = (bool) $request->input('is_active');
        }

        $iuran->update($data);

        AuditLogger::log(
            'update',
            'Iuran Warga',
            $iuran->id,
            "Jenis iuran '{$iuran->name}' diperbarui oleh {$user->name}."
        );

        return redirect()->back()->with('success', "Jenis iuran '{$iuran->name}' berhasil diperbarui.");
    }

    /**
     * Toggle status aktif Jenis Iuran
     */
    public function toggleJenisIuran($id)
    {
        $user = Auth::user();
        if (!$user->isAdminRw()) {
            return redirect()->back()->with('error', 'Hanya Admin RW yang berwenang.');
        }

        $iuran = Iuran::findOrFail($id);
        $iuran->is_active = !$iuran->is_active;
        $iuran->save();

        AuditLogger::log(
            'update',
            'Iuran Warga',
            $iuran->id,
            "Status jenis iuran '{$iuran->name}' diubah menjadi " . ($iuran->is_active ? 'Aktif' : 'Non-Aktif') . " oleh {$user->name}."
        );

        return redirect()->back()->with('success', "Status jenis iuran '{$iuran->name}' diubah menjadi " . ($iuran->is_active ? 'Aktif' : 'Non-Aktif') . ".");
    }

    /**
     * Hapus / Nonaktifkan Jenis Iuran
     */
    public function destroyJenisIuran($id)
    {
        $user = Auth::user();
        if (!$user->isAdminRw()) {
            return redirect()->back()->with('error', 'Hanya Admin RW yang berwenang.');
        }

        $iuran = Iuran::findOrFail($id);
        $iuran->is_active = false;
        $iuran->save();

        AuditLogger::log(
            'delete',
            'Iuran Warga',
            $iuran->id,
            "Jenis iuran '{$iuran->name}' dinonaktifkan oleh {$user->name}."
        );

        return redirect()->back()->with('success', "Jenis iuran '{$iuran->name}' berhasil dinonaktifkan.");
    }

    /**
     * Bayar Iuran / Unggah Bukti Pembayaran (Point 5.D & 9.D)
     * Dapat dipanggil oleh Warga (upload bukti) maupun Admin (catat langsung tunai/transfer)
     */
    public function bayarIuran(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'warga_id' => 'required|exists:wargas,id',
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|between:2020,2035',
            'amount_paid' => 'required|numeric|min:1000',
            'payment_method' => 'required|string|max:100',
            'proof_image' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'notes' => 'nullable|string|max:500',
        ]);

        $warga = Warga::findOrFail($request->warga_id);

        // RBAC: Jika Warga, pastikan membayar untuk dirinya/KK-nya sendiri
        if ($user->role === 'warga') {
            if ($user->nik && $warga->nik !== $user->nik && $warga->no_kk !== $user->no_kk) {
                return redirect()->back()->with('error', 'Akses ditolak: Anda hanya dapat membayar iuran keluarga Anda.');
            }
        } elseif ($user->isAdminRt() && $warga->rt_number !== $user->rt_number) {
            return redirect()->back()->with('error', 'Akses ditolak: Warga berada di luar RT Anda.');
        }

        $proofPath = null;
        if ($request->hasFile('proof_image')) {
            $proofPath = $request->file('proof_image')->store('bukti_iuran', 'public');
        }

        $iuran = Iuran::first();
        $iuranId = $iuran ? $iuran->id : 1;

        // Jika dicatat langsung oleh Admin (tunai/transfer langsung sah), langsung 'confirmed'
        // Jika diunggah oleh Warga mandiri, status 'pending' menunggu pemeriksaan admin
        $isDirectAdmin = !$user->isWarga();
        $status = $isDirectAdmin ? 'confirmed' : 'pending';

        DB::beginTransaction();
        try {
            $pembayaran = PembayaranIuran::create([
                'user_id' => $user->id,
                'warga_id' => $warga->id,
                'rt_number' => $warga->rt_number,
                'iuran_id' => $iuranId,
                'amount_paid' => $request->amount_paid,
                'nominal_tagihan' => $request->amount_paid,
                'payment_date' => now()->format('Y-m-d'),
                'bulan' => $request->bulan,
                'tahun' => $request->tahun,
                'status' => $status,
                'payment_method' => $request->payment_method,
                'proof_image' => $proofPath,
                'notes' => $request->notes ?: "Pembayaran Iuran {$request->bulan}/{$request->tahun} a.n {$warga->nama}",
                'confirmed_by' => $isDirectAdmin ? $user->id : null,
            ]);

            // Jika langsung dikonfirmasi oleh Admin, otomatis integrasi ke Transaksi Kas (Point 5.F)
            if ($isDirectAdmin) {
                $nextId = (Pembayaran::max('id') ?? 0) + 1;
                $nomorTx = sprintf('TRX-%s-%05d', $request->tahun, $nextId);

                $tx = Pembayaran::create([
                    'nomor_transaksi' => $nomorTx,
                    'warga_id' => $warga->id,
                    'rt_number' => $warga->rt_number,
                    'tipe' => 'masuk',
                    'jumlah' => $request->amount_paid,
                    'kategori' => $iuran ? $iuran->name : 'Iuran Bulanan',
                    'keterangan' => "Iuran Warga Periode {$request->bulan}/{$request->tahun} a.n {$warga->nama}",
                    'sumber_tujuan' => "Warga RT {$warga->rt_number}",
                    'metode_pembayaran' => $request->payment_method,
                    'nomor_referensi' => "IURAN-{$pembayaran->id}",
                    'bukti_transaksi' => $proofPath,
                    'status' => 'selesai',
                    'pembayaran_id' => $pembayaran->id,
                    'created_by' => $user->id,
                    'tanggal' => now()->format('Y-m-d'),
                ]);

                $pembayaran->transaction_id = $tx->id;
                $pembayaran->save();
            }

            DB::commit();

            AuditLogger::log(
                $isDirectAdmin ? 'confirm' : 'create',
                'Iuran Warga',
                $pembayaran->id,
                "Pembayaran iuran {$request->bulan}/{$request->tahun} sebesar Rp " . number_format($request->amount_paid) . " a.n '{$warga->nama}' disimpan (Status: {$status}) oleh {$user->name}.",
                null,
                $pembayaran->toArray(),
                $warga->rt_number
            );

            // Kirim notifikasi jika warga yang mengajukan
            if (!$isDirectAdmin) {
                try {
                    FinanceNotification::send(
                        $user->id,
                        'pembayaran_pending',
                        "Pembayaran Iuran Menunggu Verifikasi",
                        "Bukti pembayaran iuran periode {$request->bulan}/{$request->tahun} sebesar Rp " . number_format($request->amount_paid, 0, ',', '.') . " a.n {$warga->nama} telah dikirimkan dan menunggu verifikasi pengurus.",
                        [
                            'warga_id' => $warga->id,
                            'nominal' => $request->amount_paid,
                            'periode' => sprintf('%02d/%d', $request->bulan, $request->tahun),
                            'link_url' => route('laporan.iuran'),
                        ]
                    );
                } catch (\Throwable $e) {}
            }

            $msg = $isDirectAdmin
                ? "Pembayaran iuran a.n {$warga->nama} berhasil dicatat dan disahkan ke Kas RT {$warga->rt_number}!"
                : "Bukti pembayaran berhasil diunggah! Status pembayaran sedang menunggu konfirmasi admin.";

            return redirect()->back()->with('success', $msg);

        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Konfirmasi Pembayaran Iuran & Integrasi Transaksi Kas (Point 5.F)
     */
    public function confirmPembayaranIuran(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->role === 'warga' || $user->isWarga()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk pengurus lingkungan.');
        }

        $payment = PembayaranIuran::with(['warga', 'iuran'])->findOrFail($id);

        if ($user->isAdminRt() && $payment->rt_number !== $user->rt_number) {
            return redirect()->back()->with('error', 'Akses ditolak: Pembayaran berasal dari RT lain.');
        }

        DB::beginTransaction();
        try {
            $payment->status = 'confirmed';
            $payment->confirmed_by = $user->id;

            // CEK APAKAH SUDAH TERHUBUNG KE TRANSAKSI KAS AGAR TIDAK DUPLIKAT (Point 5.F)
            if (!$payment->transaction_id) {
                $nextId = (Pembayaran::max('id') ?? 0) + 1;
                $year = $payment->tahun ?: date('Y');
                $nomorTx = sprintf('TRX-%s-%05d', $year, $nextId);

                $tx = Pembayaran::create([
                    'nomor_transaksi' => $nomorTx,
                    'warga_id' => $payment->warga_id,
                    'rt_number' => $payment->rt_number,
                    'tipe' => 'masuk',
                    'jumlah' => $payment->amount_paid,
                    'kategori' => $payment->iuran ? $payment->iuran->name : 'Iuran Bulanan',
                    'keterangan' => "Iuran Warga Periode {$payment->bulan}/{$payment->tahun} a.n " . ($payment->warga->nama ?? 'Warga'),
                    'sumber_tujuan' => "Warga RT {$payment->rt_number}",
                    'metode_pembayaran' => $payment->payment_method ?: 'Transfer Bank',
                    'nomor_referensi' => "IURAN-{$payment->id}",
                    'bukti_transaksi' => $payment->proof_image,
                    'status' => 'selesai',
                    'pembayaran_id' => $payment->id,
                    'created_by' => $user->id,
                    'tanggal' => $payment->payment_date ? $payment->payment_date->format('Y-m-d') : now()->format('Y-m-d'),
                ]);

                $payment->transaction_id = $tx->id;
            }

            $payment->save();
            DB::commit();

            AuditLogger::log(
                'confirm',
                'Iuran Warga',
                $payment->id,
                "Pembayaran iuran {$payment->bulan}/{$payment->tahun} a.n '{$payment->warga->nama}' sebesar Rp " . number_format($payment->amount_paid) . " dikonfirmasi oleh {$user->name}.",
                ['status' => 'pending'],
                ['status' => 'confirmed', 'transaction_id' => $payment->transaction_id],
                $payment->rt_number
            );

            // Kirim notifikasi pembayaran diterima kepada warga
            if ($payment->user_id) {
                try {
                    FinanceNotification::send(
                        $payment->user_id,
                        'pembayaran_diterima',
                        "Pembayaran Iuran Disahkan & Lunas",
                        "Pembayaran iuran periode {$payment->bulan}/{$payment->tahun} sebesar Rp " . number_format($payment->amount_paid, 0, ',', '.') . " a.n " . ($payment->warga->nama ?? 'Warga') . " telah disahkan dan tercatat di kas lingkungan. Kuitansi resmi dapat diunduh.",
                        [
                            'warga_id' => $payment->warga_id,
                            'nominal' => $payment->amount_paid,
                            'periode' => sprintf('%02d/%d', $payment->bulan, $payment->tahun),
                            'link_url' => route('iuran.kuitansi', $payment->id),
                        ]
                    );
                } catch (\Throwable $e) {}
            }

            return redirect()->back()->with('success', "Pembayaran iuran a.n {$payment->warga->nama} berhasil disahkan dan tercatat di Kas!");

        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengonfirmasi pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Tolak Pembayaran Iuran
     */
    public function rejectPembayaranIuran(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->role === 'warga' || $user->isWarga()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk pengurus lingkungan.');
        }

        $request->validate([
            'alasan' => 'required|string|max:500',
        ]);

        $payment = PembayaranIuran::with('warga')->findOrFail($id);
        if ($user->isAdminRt() && $payment->rt_number !== $user->rt_number) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $payment->status = 'rejected';
        $payment->notes = $payment->notes . " (Ditolak oleh {$user->name}: {$request->alasan})";
        $payment->confirmed_by = $user->id;
        $payment->save();

        AuditLogger::log(
            'reject',
            'Iuran Warga',
            $payment->id,
            "Pembayaran iuran ID #{$payment->id} a.n '{$payment->warga->nama}' ditolak oleh {$user->name}. Alasan: {$request->alasan}",
            ['status' => 'pending'],
            ['status' => 'rejected', 'notes' => $payment->notes],
            $payment->rt_number
        );

        // Kirim notifikasi pembayaran ditolak kepada warga beserta alasannya
        if ($payment->user_id) {
            try {
                FinanceNotification::send(
                    $payment->user_id,
                    'pembayaran_ditolak',
                    "Pembayaran Iuran Perlu Perbaikan",
                    "Pengajuan pembayaran iuran periode {$payment->bulan}/{$payment->tahun} a.n " . ($payment->warga->nama ?? 'Warga') . " ditolak oleh pengurus. Alasan: {$request->alasan}. Silakan ajukan ulang dengan bukti transfer yang sesuai.",
                    [
                        'warga_id' => $payment->warga_id,
                        'nominal' => $payment->amount_paid,
                        'periode' => sprintf('%02d/%d', $payment->bulan, $payment->tahun),
                        'link_url' => route('laporan.iuran'),
                    ]
                );
            } catch (\Throwable $e) {}
        }

        return redirect()->back()->with('success', "Pembayaran iuran a.n {$payment->warga->nama} telah ditolak.");
    }

    /**
     * Kuitansi Digital Iuran
     */
    public function kuitansiIuran($id)
    {
        $payment = PembayaranIuran::with(['warga', 'iuran', 'verifikator'])->find($id);
        if (!$payment) {
            // Coba ambil dari transaksi kas
            $tx = Pembayaran::with(['warga', 'creator'])->findOrFail($id);
            return view('laporan.kuitansi_tx', compact('tx'));
        }

        return view('laporan.kuitansi', compact('payment'));
    }

    public function cetakLaporanIuran(Request $request)
    {
        $user = Auth::user();
        if ($user->role === 'warga' || $user->isWarga()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk pengurus lingkungan.');
        }
        $bulan = (int)$request->get('bulan', date('m'));
        $tahun = (int)$request->get('tahun', date('Y'));
        $rtFilter = $request->get('rt_number');
        if ($user->isAdminRt()) {
            $rtFilter = $user->rt_number;
        }

        $wargasQuery = Warga::where('is_kk', true)->where('status_warga', '!=', 'pindah')->orderBy('rt_number')->orderBy('nama');
        if ($rtFilter && $rtFilter !== 'all') {
            $wargasQuery->where('rt_number', $rtFilter);
        }
        $wargas = $wargasQuery->get();

        $rows = [];
        $totalTerkumpul = 0;
        $totalTunggakan = 0;

        foreach ($wargas as $warga) {
            $tx = Pembayaran::where('warga_id', $warga->id)
                ->where('status', '!=', 'dibatalkan')
                ->where('tipe', 'masuk')
                ->where('kategori', 'like', '%Iuran%')
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->first();

            $status = $tx ? 'Lunas' : 'Belum Bayar';
            $nominal = $tx ? $tx->jumlah : 100000;

            if ($status === 'Lunas') {
                $totalTerkumpul += $nominal;
            } else {
                $totalTunggakan += $nominal;
            }

            $rows[] = [
                'warga' => $warga,
                'status' => $status,
                'nominal' => $nominal,
                'tanggal_bayar' => $tx ? $tx->tanggal : null,
                'metode' => $tx ? ($tx->metode_pembayaran ?: 'Tunai') : '-',
            ];
        }

        return view('laporan.cetak_iuran', compact('rows', 'bulan', 'tahun', 'rtFilter', 'totalTerkumpul', 'totalTunggakan'));
    }

    /**
     * 4. LAPORAN BULANAN TERPADU (Point 6 & 8.D)
     * Rekap Keuangan + Rekap Laporan RT + Rekap Aktivitas Administrasi Wilayah
     */
    public function indexLaporan(Request $request)
    {
        $user = Auth::user();
        if ($user->role === 'warga' || $user->isWarga()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk pengurus lingkungan.');
        }

        $bulan = (int)$request->get('bulan', date('m'));
        $tahun = (int)$request->get('tahun', date('Y'));
        $rtFilter = $request->get('rt_number');

        if ($user->isAdminRt()) {
            $rtFilter = $user->rt_number;
        }

        // ==========================================
        // 1. REKAP KEUANGAN BULANAN AKTUAL (Point 6.B)
        // ==========================================
        $firstDayOfMonth = sprintf('%d-%02d-01', $tahun, $bulan);

        $querySaldoAwalMasuk = Pembayaran::where('status', '!=', 'dibatalkan')->where('tipe', 'masuk')->where('tanggal', '<', $firstDayOfMonth);
        $querySaldoAwalKeluar = Pembayaran::where('status', '!=', 'dibatalkan')->where('tipe', 'keluar')->where('tanggal', '<', $firstDayOfMonth);
        $queryBulanIni = Pembayaran::with(['warga', 'creator'])->where('status', '!=', 'dibatalkan')->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);

        if ($user->isAdminRt()) {
            $rt = $user->rt_number;
            $querySaldoAwalMasuk->where('rt_number', $rt);
            $querySaldoAwalKeluar->where('rt_number', $rt);
            $queryBulanIni->where('rt_number', $rt);
        } elseif ($rtFilter && $rtFilter !== 'all') {
            $querySaldoAwalMasuk->where('rt_number', $rtFilter);
            $querySaldoAwalKeluar->where('rt_number', $rtFilter);
            $queryBulanIni->where('rt_number', $rtFilter);
        }

        $saldoAwal = $querySaldoAwalMasuk->sum('jumlah') - $querySaldoAwalKeluar->sum('jumlah');

        $pembayarans = $queryBulanIni->orderBy('tanggal', 'asc')->get();
        $totalMasuk = $pembayarans->where('tipe', 'masuk')->sum('jumlah');
        $totalKeluar = $pembayarans->where('tipe', 'keluar')->sum('jumlah');
        $saldoAkhir = $saldoAwal + $totalMasuk - $totalKeluar;

        // Total Iuran Terkumpul & Tunggakan
        $totalIuranBulanIni = $pembayarans->where('tipe', 'masuk')->filter(function($i) {
            return str_contains(strtolower($i->kategori), 'iuran');
        })->sum('jumlah');

        $kkCount = Warga::where('is_kk', true);
        if ($user->isAdminRt()) {
            $kkCount->where('rt_number', $user->rt_number);
        } elseif ($rtFilter && $rtFilter !== 'all') {
            $kkCount->where('rt_number', $rtFilter);
        }
        $totalKkTarget = $kkCount->count();
        $tunggakanIuran = max(0, ($totalKkTarget * 100000) - $totalIuranBulanIni);

        // ==========================================
        // 2. REKAP STATUS LAPORAN RESMI RT (Point 6.A)
        // ==========================================
        $laporanRtQuery = LaporanRt::where('bulan', $bulan)->where('tahun', $tahun);
        $totalRt = 5;
        $laporanTerkirim = (clone $laporanRtQuery)->count();
        $laporanDiterima = (clone $laporanRtQuery)->where('status', 'diterima')->count();
        $laporanRevisi = (clone $laporanRtQuery)->where('status', 'perlu_revisi')->count();
        $laporanMenunggu = (clone $laporanRtQuery)->where('status', 'menunggu')->count();
        $laporanBelumKirim = max(0, $totalRt - $laporanTerkirim);

        // ==========================================
        // 3. REKAP KOMPARASI KEUANGAN ANTAR RT (Point 6.D)
        // ==========================================
        $rtList = ['006', '007', '008', '009', '010'];
        $rekapPerRt = [];

        foreach ($rtList as $rtNum) {
            $txMasukRt = Pembayaran::where('status', '!=', 'dibatalkan')->where('rt_number', $rtNum)->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->where('tipe', 'masuk')->sum('jumlah');
            $txKeluarRt = Pembayaran::where('status', '!=', 'dibatalkan')->where('rt_number', $rtNum)->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->where('tipe', 'keluar')->sum('jumlah');
            $saldoRt = $txMasukRt - $txKeluarRt;

            $kkRt = Warga::where('is_kk', true)->where('rt_number', $rtNum)->count();
            $lunasRt = Pembayaran::where('status', '!=', 'dibatalkan')
                ->where('rt_number', $rtNum)
                ->where('tipe', 'masuk')
                ->where('kategori', 'like', '%Iuran%')
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->distinct('warga_id')
                ->count('warga_id');

            $laporanRtItem = LaporanRt::where('rt_number', $rtNum)->where('bulan', $bulan)->where('tahun', $tahun)->first();

            $rekapPerRt[] = [
                'rt_number' => $rtNum,
                'pemasukan' => $txMasukRt,
                'pengeluaran' => $txKeluarRt,
                'saldo' => $saldoRt,
                'total_kk' => $kkRt,
                'kk_lunas' => $lunasRt,
                'persen_kepatuhan' => $kkRt > 0 ? round(($lunasRt / $kkRt) * 100, 1) : 0,
                'status_laporan' => $laporanRtItem ? $laporanRtItem->status : 'belum_kirim',
                'laporan_id' => $laporanRtItem ? $laporanRtItem->id : null,
            ];
        }

        // ==========================================
        // 4. REKAP AKTIVITAS WILAYAH TERPADU (Point 6.C)
        // ==========================================
        $aktivitasSurat = SuratPengajuan::whereMonth('created_at', $bulan)->whereYear('created_at', $tahun)->count();
        $aktivitasPengaduan = Pengaduan::whereMonth('created_at', $bulan)->whereYear('created_at', $tahun)->count();
        $aktivitasBankSampah = BankSampah::whereMonth('tanggal_transaksi', $bulan)->whereYear('tanggal_transaksi', $tahun)->count();
        $aktivitasRonda = JadwalRonda::whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->count();
        $aktivitasMutasi = MutasiWarga::whereMonth('tanggal_mutasi', $bulan)->whereYear('tanggal_mutasi', $tahun)->count();

        $daftarTahun = range(date('Y'), date('Y') - 5);

        return view('laporan.index', compact(
            'pembayarans', 'bulan', 'tahun', 'saldoAwal', 'totalMasuk', 'totalKeluar', 'saldoAkhir',
            'totalIuranBulanIni', 'tunggakanIuran', 'totalRt', 'laporanTerkirim', 'laporanDiterima',
            'laporanRevisi', 'laporanMenunggu', 'laporanBelumKirim', 'rekapPerRt', 'daftarTahun',
            'rtFilter', 'rtList', 'aktivitasSurat', 'aktivitasPengaduan', 'aktivitasBankSampah',
            'aktivitasRonda', 'aktivitasMutasi'
        ));
    }

    public function cetakLaporanBulanan(Request $request)
    {
        $user = Auth::user();
        if ($user->role === 'warga' || $user->isWarga()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk pengurus lingkungan.');
        }

        $bulan = (int)$request->get('bulan', date('m'));
        $tahun = (int)$request->get('tahun', date('Y'));
        $rtFilter = $request->get('rt_number');
        if ($user->isAdminRt()) {
            $rtFilter = $user->rt_number;
        }

        $firstDayOfMonth = sprintf('%d-%02d-01', $tahun, $bulan);

        $querySaldoAwalMasuk = Pembayaran::where('status', '!=', 'dibatalkan')->where('tipe', 'masuk')->where('tanggal', '<', $firstDayOfMonth);
        $querySaldoAwalKeluar = Pembayaran::where('status', '!=', 'dibatalkan')->where('tipe', 'keluar')->where('tanggal', '<', $firstDayOfMonth);
        $queryBulanIni = Pembayaran::with('warga')->where('status', '!=', 'dibatalkan')->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);

        if ($user->isAdminRt()) {
            $querySaldoAwalMasuk->where('rt_number', $user->rt_number);
            $querySaldoAwalKeluar->where('rt_number', $user->rt_number);
            $queryBulanIni->where('rt_number', $user->rt_number);
        } elseif ($rtFilter && $rtFilter !== 'all') {
            $querySaldoAwalMasuk->where('rt_number', $rtFilter);
            $querySaldoAwalKeluar->where('rt_number', $rtFilter);
            $queryBulanIni->where('rt_number', $rtFilter);
        }

        $saldoAwal = $querySaldoAwalMasuk->sum('jumlah') - $querySaldoAwalKeluar->sum('jumlah');
        $pembayarans = $queryBulanIni->orderBy('tanggal', 'asc')->get();

        $totalMasuk = $pembayarans->where('tipe', 'masuk')->sum('jumlah');
        $totalKeluar = $pembayarans->where('tipe', 'keluar')->sum('jumlah');
        $saldoAkhir = $saldoAwal + $totalMasuk - $totalKeluar;

        return view('laporan.cetak_bulanan', compact(
            'pembayarans', 'bulan', 'tahun', 'saldoAwal',
            'totalMasuk', 'totalKeluar', 'saldoAkhir', 'rtFilter'
        ));
    }

    public function exportLaporanBulanan(Request $request)
    {
        $user = Auth::user();
        if ($user->role === 'warga' || $user->isWarga()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk pengurus lingkungan.');
        }

        $bulan = (int)$request->get('bulan', date('m'));
        $tahun = (int)$request->get('tahun', date('Y'));
        $rtFilter = $request->get('rt_number');
        if ($user->isAdminRt()) {
            $rtFilter = $user->rt_number;
        }

        $filename = 'rekap_bulanan_' . sprintf('%02d_%d_', $bulan, $tahun) . ($rtFilter ? 'rt_' . $rtFilter : 'rw018') . '.csv';
        $query = Pembayaran::with('warga')->where('status', '!=', 'dibatalkan')->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
        if ($rtFilter && $rtFilter !== 'all') {
            $query->where('rt_number', $rtFilter);
        }
        $txs = $query->orderBy('tanggal', 'asc')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function() use ($txs, $bulan, $tahun) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['REKAP LAPORAN KEUANGAN BULANAN - PERIODE ' . sprintf('%02d/%d', $bulan, $tahun)]);
            fputcsv($handle, []);
            fputcsv($handle, ['No', 'Nomor Transaksi', 'Tanggal', 'Tipe', 'Kategori', 'Keterangan', 'RT', 'Nominal (Rp)']);

            $no = 1;
            foreach ($txs as $t) {
                fputcsv($handle, [
                    $no++,
                    $t->nomor_transaksi_formatted,
                    $t->tanggal ? $t->tanggal->format('Y-m-d') : '-',
                    strtoupper($t->tipe),
                    $t->kategori,
                    $t->keterangan,
                    $t->rt_number === '000' ? 'RW 018' : 'RT ' . $t->rt_number,
                    $t->jumlah,
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }
}
