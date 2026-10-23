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

        // Warga should not access this raw kas table directly, but if they do, show only theirs
        if ($user->role === 'warga') {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak: Halaman Transaksi Kas hanya untuk pengurus.');
        }

        $query = Pembayaran::with('warga')->orderBy('tanggal', 'desc');

        if ($user->isAdminRt()) {
            $query->where('rt_number', $user->rt_number);
        }

        $pembayarans = $query->paginate(15);

        // Langkah A: Buat susunan template 6 bulan terakhir dengan nilai default 0
        $templateBulan = collect();
        for ($i = 5; $i >= 0; $i--) {
            $targetDate = now()->subMonths($i);
            $templateBulan->put($targetDate->format('Y-m'), (object)[
                'year_month'  => $targetDate->format('Y-m'),
                'bulan_tahun' => $targetDate->translatedFormat('F Y'),
                'masuk'       => 0,
                'keluar'      => 0
            ]);
        }

        // Langkah B: Ambil data riil dari database
        $chartQuery = Pembayaran::where('tanggal', '>=', now()->subMonths(5)->startOfMonth())
            ->orderBy('tanggal', 'asc');
            
        if ($user->isAdminRt()) {
            $chartQuery->where('rt_number', $user->rt_number);
        }

        $pembayaransChart = $chartQuery->get()->groupBy(function($item) {
            return Carbon::parse($item->tanggal)->format('Y-m');
        });

        // Langkah C: Gabungkan data database ke template
        $chartData = $templateBulan->map(function($default, $key) use ($pembayaransChart) {
            if ($pembayaransChart->has($key)) {
                $group = $pembayaransChart[$key];
                return (object)[
                    'year_month'  => $key,
                    'bulan_tahun' => Carbon::parse($group->first()->tanggal)->translatedFormat('F Y'),
                    'masuk'       => $group->where('tipe', 'masuk')->sum('jumlah'),
                    'keluar'      => $group->where('tipe', 'keluar')->sum('jumlah'),
                ];
            }
            return $default;
        })->values();

        return view('pembayaran.index', compact('pembayarans', 'chartData'));
    }

    public function create()
    {
        if (Auth::user()->role === 'warga') {
            return redirect()->route('dashboard');
        }
        
        $wargaQuery = Warga::query();
        if (Auth::user()->isAdminRt()) {
            $wargaQuery->where('rt_number', Auth::user()->rt_number);
        }
        $warga = $wargaQuery->get();
        return view('pembayaran.create', compact('warga'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'warga_id' => 'nullable|exists:wargas,id',
            'tipe' => 'required|in:masuk,keluar',
            'jumlah' => 'required|numeric',
            'kategori' => 'required|string',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        $rt_number = Auth::user()->isAdminRt() ? Auth::user()->rt_number : '000';
        if ($request->warga_id) {
            $warga = Warga::find($request->warga_id);
            if ($warga) {
                $rt_number = $warga->rt_number;
            }
        }

        Pembayaran::create([
            'warga_id' => $request->warga_id,
            'rt_number' => $rt_number,
            'tipe' => $request->tipe,
            'jumlah' => $request->jumlah,
            'kategori' => $request->kategori,
            'keterangan' => $request->keterangan,
            'tanggal' => $request->tanggal,
        ]);

        return redirect()->route('pembayaran.index')->with('success', 'Transaksi kas berhasil dicatat!');
    }

    public function cetakLaporan()
    {
        if (Auth::user()->role === 'warga') return redirect()->route('dashboard');

        $query = Pembayaran::with('warga')->orderBy('tanggal', 'asc');
        if (Auth::user()->isAdminRt()) {
            $query->where('rt_number', Auth::user()->rt_number);
        }
        $pembayarans = $query->get();
        return view('pembayaran.cetak', compact('pembayarans'));
    }

    public function destroy($id)
    {
        if (Auth::user()->role === 'warga') return redirect()->route('dashboard');
        
        $pembayaran = Pembayaran::findOrFail($id);
        
        if (Auth::user()->isAdminRt() && $pembayaran->rt_number !== Auth::user()->rt_number) {
            return redirect()->route('pembayaran.index')->with('error', 'Tidak berhak menghapus transaksi RT lain.');
        }

        $pembayaran->delete();
        return redirect()->route('pembayaran.index')->with('success', 'Transaksi berhasil dihapus!');
    }

    public function indexLaporan(Request $request)
    {
        if (Auth::user()->role === 'warga') return redirect()->route('dashboard');

        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));

        $queryAwalMasuk = Pembayaran::where('tipe', 'masuk')->where('tanggal', '<', "$tahun-$bulan-01");
        $queryAwalKeluar = Pembayaran::where('tipe', 'keluar')->where('tanggal', '<', "$tahun-$bulan-01");
        $queryBulanIni = Pembayaran::with('warga')->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);

        if (Auth::user()->isAdminRt()) {
            $rt = Auth::user()->rt_number;
            $queryAwalMasuk->where('rt_number', $rt);
            $queryAwalKeluar->where('rt_number', $rt);
            $queryBulanIni->where('rt_number', $rt);
        }

        $saldoAwalMasuk = $queryAwalMasuk->sum('jumlah');
        $saldoAwalKeluar = $queryAwalKeluar->sum('jumlah');
        $saldoAwal = $saldoAwalMasuk - $saldoAwalKeluar;

        $pembayarans = $queryBulanIni->orderBy('tanggal', 'asc')->get();

        $totalMasuk = $pembayarans->where('tipe', 'masuk')->sum('jumlah');
        $totalKeluar = $pembayarans->where('tipe', 'keluar')->sum('jumlah');
        $saldoAkhir = $saldoAwal + $totalMasuk - $totalKeluar;

        $daftarTahun = range(date('Y'), date('Y') - 5);

        return view('laporan.index', compact(
            'pembayarans', 'bulan', 'tahun', 'saldoAwal',
            'totalMasuk', 'totalKeluar', 'saldoAkhir', 'daftarTahun'
        ));
    }

    public function cetakLaporanBulanan(Request $request)
    {
        if (Auth::user()->role === 'warga') return redirect()->route('dashboard');

        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));

        $queryAwalMasuk = Pembayaran::where('tipe', 'masuk')->where('tanggal', '<', "$tahun-$bulan-01");
        $queryAwalKeluar = Pembayaran::where('tipe', 'keluar')->where('tanggal', '<', "$tahun-$bulan-01");
        $queryBulanIni = Pembayaran::with('warga')->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);

        if (Auth::user()->isAdminRt()) {
            $rt = Auth::user()->rt_number;
            $queryAwalMasuk->where('rt_number', $rt);
            $queryAwalKeluar->where('rt_number', $rt);
            $queryBulanIni->where('rt_number', $rt);
        }

        $saldoAwalMasuk = $queryAwalMasuk->sum('jumlah');
        $saldoAwalKeluar = $queryAwalKeluar->sum('jumlah');
        $saldoAwal = $saldoAwalMasuk - $saldoAwalKeluar;

        $pembayarans = $queryBulanIni->orderBy('tanggal', 'asc')->get();

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
        
        $user = Auth::user();
        if ($user->role === 'warga') {
            // Warga only sees their own
            $wargaRecord = Warga::where('nik', $user->nik)->orWhere('no_kk', $user->no_kk)->first();
            $wargaId = $wargaRecord ? $wargaRecord->id : -1;

            $paidThisMonth = Pembayaran::where('warga_id', $wargaId)
                ->where('tipe', 'masuk')
                ->where('kategori', 'like', '%Iuran%')
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->orderBy('tanggal', 'desc')
                ->get();

            $rows = [];
            if ($wargaRecord) {
                $status = $paidThisMonth->isEmpty() ? 'Belum Bayar' : 'Lunas';
                $badgeClass = $status === 'Lunas' ? 'bg-success' : 'bg-danger';

                $rows[] = (object)[
                    'warga' => $wargaRecord,
                    'status' => $status,
                    'badge_class' => $badgeClass,
                    'pembayarans' => $paidThisMonth,
                ];
            }

            return view('laporan.iuran', compact('rows', 'bulan', 'tahun'));
        }

        $rtFilter = $request->get('rt_number');
        if ($user->isAdminRt()) {
            $rtFilter = $user->rt_number;
        }

        $rtList = ['006', '007', '008', '009', '010'];

        $wargasQuery = Warga::where('is_kk', true)->orderBy('nama');
        if ($rtFilter) {
            $wargasQuery->where('rt_number', $rtFilter);
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

            if ($status === 'Lunas') {
                $keterangan = 'Rajin';
            } elseif ($totalPayments == 0) {
                $keterangan = 'Tidak Pernah Bayar';
            } else {
                $keterangan = 'Kurang Bayar';
            }

            $rows[] = [
                'warga' => $warga,
                'status' => $status,
                'tanggal_bayar' => $tanggalBayar,
                'history' => $history,
                'keterangan' => $keterangan,
            ];
        }

        // Hitung total ketaatan bayar
        $countRajin = 0;
        $countKurang = 0;
        $countTidakPernah = 0;
        foreach ($rows as $row) {
            if ($row['keterangan'] === 'Rajin') {
                $countRajin++;
            } elseif ($row['keterangan'] === 'Kurang Bayar') {
                $countKurang++;
            } else {
                $countTidakPernah++;
            }
        }

        $daftarTahun = range(date('Y'), date('Y') - 5);

        return view('laporan.iuran', compact(
            'rows', 'wargas', 'rtList', 'bulan', 'tahun', 'daftarTahun', 'rtFilter',
            'countRajin', 'countKurang', 'countTidakPernah'
        ));
    }

    public function cetakLaporanIuran(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));
        
        $user = Auth::user();
        if ($user->role === 'warga') return redirect()->route('dashboard');
        
        $rtFilter = $request->get('rt_number');

        $wargasQuery = Warga::where('is_kk', true)->orderBy('nama');
        if ($rtFilter) {
            $wargasQuery->where('rt_number', $rtFilter);
        }
        if ($user->isAdminRt()) {
            $wargasQuery->where('rt_number', $user->rt_number);
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

            if ($status === 'Lunas') {
                $keterangan = 'Rajin';
            } elseif ($totalPayments == 0) {
                $keterangan = 'Tidak Pernah Bayar';
            } else {
                $keterangan = 'Kurang Bayar';
            }

            $rows[] = [
                'warga' => $warga,
                'status' => $status,
                'tanggal_bayar' => $tanggalBayar,
                'history' => $history,
                'keterangan' => $keterangan,
            ];
        }

        return view('laporan.cetak_iuran', compact('rows', 'bulan', 'tahun', 'rtFilter'));
    }
}
