<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Warga;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PembayaranController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Mengambil data dari tabel transactions (Model Pembayaran)
        // Kita tampilkan semua transaksi, baik masuk maupun keluar
        $pembayarans = Pembayaran::with('warga')
            ->orderBy('tanggal', 'desc')
            ->paginate(15);

        return view('pembayaran.index', compact('pembayarans'));
    }

    public function create()
    {
        // Ambil data warga untuk dropdown di form iuran
        $warga = Warga::all();
        return view('pembayaran.create', compact('warga'));
    }

    public function store(Request $request)
    {
        // Validasi sesuai kolom di tabel transactions
        $request->validate([
            'warga_id' => 'nullable|exists:wargas,id',
            'tipe' => 'required|in:masuk,keluar',
            'jumlah' => 'required|numeric',
            'kategori' => 'required|string',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        // Simpan ke tabel transactions
        Pembayaran::create([
            'warga_id' => $request->warga_id, // Bisa kosong jika pengeluaran umum
            'tipe' => $request->tipe,
            'jumlah' => $request->jumlah,
            'kategori' => $request->kategori,
            'keterangan' => $request->keterangan,
            'tanggal' => $request->tanggal,
        ]);

        return redirect()->route('dashboard')->with('success', 'Transaksi kas berhasil dicatat!');
    }

    public function cetakLaporan()
    {
        $pembayarans = Pembayaran::with('warga')->orderBy('tanggal', 'asc')->get();
        return view('pembayaran.cetak', compact('pembayarans'));
    }

    public function destroy($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        $pembayaran->delete();

        return redirect()->route('dashboard')->with('success', 'Transaksi berhasil dihapus!');
    }

    public function indexLaporan(Request $request)
    {
        // Mengambil filter bulan dan tahun, default-nya adalah bulan & tahun sekarang
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));

        // 1. HITUNG SALDO AWAL (Semua transaksi MASUK dikurangi KELUAR SEBELUM bulan terpilih)
        $saldoAwalMasuk = Pembayaran::where('tipe', 'masuk')
            ->where('tanggal', '<', "$tahun-$bulan-01")
            ->sum('jumlah');

        $saldoAwalKeluar = Pembayaran::where('tipe', 'keluar')
            ->where('tanggal', '<', "$tahun-$bulan-01")
            ->sum('jumlah');

        $saldoAwal = $saldoAwalMasuk - $saldoAwalKeluar;

        // 2. AMBIL TRANSAKSI KHUSUS BULAN INI
        $pembayarans = Pembayaran::with('warga')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'asc')
            ->get();

        // 3. HITUNG TOTAL MASUK & KELUAR DI BULAN INI
        $totalMasuk = $pembayarans->where('tipe', 'masuk')->sum('jumlah');
        $totalKeluar = $pembayarans->where('tipe', 'keluar')->sum('jumlah');

        // 4. HITUNG SALDO AKHIR
        $saldoAkhir = $saldoAwal + $totalMasuk - $totalKeluar;

        // Membuat daftar pilihan tahun (tahun ini mundur sampai 5 tahun ke belakang)
        $daftarTahun = range(date('Y'), date('Y') - 5);

        return view('laporan.index', compact(
            'pembayarans', 'bulan', 'tahun', 'saldoAwal',
            'totalMasuk', 'totalKeluar', 'saldoAkhir', 'daftarTahun'
        ));
    }

    public function cetakLaporanBulanan(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));

        // Logika perhitungan sama persis dengan indexLaporan
        $saldoAwalMasuk = Pembayaran::where('tipe', 'masuk')
            ->where('tanggal', '<', "$tahun-$bulan-01")
            ->sum('jumlah');
        $saldoAwalKeluar = Pembayaran::where('tipe', 'keluar')
            ->where('tanggal', '<', "$tahun-$bulan-01")
            ->sum('jumlah');
        $saldoAwal = $saldoAwalMasuk - $saldoAwalKeluar;

        $pembayarans = Pembayaran::with('warga')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'asc')
            ->get();

        $totalMasuk = $pembayarans->where('tipe', 'masuk')->sum('jumlah');
        $totalKeluar = $pembayarans->where('tipe', 'keluar')->sum('jumlah');
        $saldoAkhir = $saldoAwal + $totalMasuk - $totalKeluar;

        return view('laporan.cetak_bulanan', compact(
            'pembayarans', 'bulan', 'tahun', 'saldoAwal',
            'totalMasuk', 'totalKeluar', 'saldoAkhir'
        ));
    }

    public function laporanIuran(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));
        $wargaFilter = $request->get('warga_id');

        $wargaList = Warga::orderBy('nama')->get();
        $wargasQuery = Warga::orderBy('nama');
        if ($wargaFilter) {
            $wargasQuery->where('id', $wargaFilter);
        }
        $wargas = $wargasQuery->get();

        $rows = [];
        $selectedDate = Carbon::createFromDate($tahun, $bulan, 1);

        foreach ($wargas as $warga) {
            $paidThisMonth = Pembayaran::where('warga_id', $warga->id)
                ->where('tipe', 'masuk')
                ->where('kategori', 'like', '%Iuran%')
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->orderBy('tanggal', 'desc')
                ->get();

            $status = $paidThisMonth->count() ? 'Lunas' : 'Belum';
            $tanggalBayar = $paidThisMonth->first()->tanggal ?? null;

            $history = [];
            for ($i = 1; $i <= 3; $i++) {
                $dt = $selectedDate->copy()->subMonths($i);
                $has = Pembayaran::where('warga_id', $warga->id)
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

            $totalPayments = Pembayaran::where('warga_id', $warga->id)
                ->where('tipe', 'masuk')
                ->where('kategori', 'like', '%Iuran%')
                ->count();

            if ($totalPayments == 0) {
                $keterangan = 'Tidak Pernah Bayar';
            } elseif (collect($history)->every(fn($h) => $h['paid'])) {
                $keterangan = 'Rajin';
            } else {
                $keterangan = 'Kadang-kadang';
            }

            $rows[] = [
                'warga' => $warga,
                'status' => $status,
                'tanggal_bayar' => $tanggalBayar,
                'history' => $history,
                'keterangan' => $keterangan,
            ];
        }

        $daftarTahun = range(date('Y'), date('Y') - 5);

        return view('laporan.iuran', compact('rows', 'wargas', 'wargaList', 'bulan', 'tahun', 'daftarTahun', 'wargaFilter'));
    }

    public function cetakLaporanIuran(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));
        $wargaFilter = $request->get('warga_id');

        $wargasQuery = Warga::orderBy('nama');
        if ($wargaFilter) {
            $wargasQuery->where('id', $wargaFilter);
        }
        $wargas = $wargasQuery->get();

        $rows = [];
        $selectedDate = Carbon::createFromDate($tahun, $bulan, 1);

        foreach ($wargas as $warga) {
            $paidThisMonth = Pembayaran::where('warga_id', $warga->id)
                ->where('tipe', 'masuk')
                ->where('kategori', 'like', '%Iuran%')
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->orderBy('tanggal', 'desc')
                ->get();

            $status = $paidThisMonth->count() ? 'Lunas' : 'Belum';
            $tanggalBayar = $paidThisMonth->first()->tanggal ?? null;

            $history = [];
            for ($i = 1; $i <= 3; $i++) {
                $dt = $selectedDate->copy()->subMonths($i);
                $has = Pembayaran::where('warga_id', $warga->id)
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

            $totalPayments = Pembayaran::where('warga_id', $warga->id)
                ->where('tipe', 'masuk')
                ->where('kategori', 'like', '%Iuran%')
                ->count();

            if ($totalPayments == 0) {
                $keterangan = 'Tidak Pernah Bayar';
            } elseif (collect($history)->every(fn($h) => $h['paid'])) {
                $keterangan = 'Rajin';
            } else {
                $keterangan = 'Kadang-kadang';
            }

            $rows[] = [
                'warga' => $warga,
                'status' => $status,
                'tanggal_bayar' => $tanggalBayar,
                'history' => $history,
                'keterangan' => $keterangan,
            ];
        }

        return view('laporan.cetak_iuran', compact('rows', 'bulan', 'tahun', 'wargaFilter'));
    }
}
