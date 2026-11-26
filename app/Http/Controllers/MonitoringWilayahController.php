<?php

namespace App\Http\Controllers;

use App\Models\BankSampah;
use App\Models\LaporanLingkungan;
use App\Models\LaporanRt;
use App\Models\Pembayaran;
use App\Models\Pengaduan;
use App\Models\SuratPengajuan;
use App\Models\User;
use App\Models\Warga;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonitoringWilayahController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdminRw()) {
            abort(403, 'Akses ditolak: Monitoring Wilayah hanya dapat diakses oleh Admin RW.');
        }

        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));
        $rtList = ['006', '007', '008', '009', '010'];

        $rtData = [];
        $totalWargaRW = 0;
        $totalKkRW = 0;
        $totalPengaduanAktifRW = 0;
        $totalSampahTerkumpulRW = 0;

        foreach ($rtList as $rt) {
            $ketuaRt = User::where('rt_number', $rt)
                ->where(function ($q) {
                    $q->where('role', 'admin')->orWhere('role', 'admin_rt');
                })->first();

            $wargaCount = Warga::where('rt_number', $rt)->where('status_warga', '!=', 'pindah')->count();
            $kkCount = Warga::where('rt_number', $rt)->where('is_kk', true)->where('status_warga', '!=', 'pindah')->count();
            $maleCount = Warga::where('rt_number', $rt)->where('gender', 'L')->where('status_warga', '!=', 'pindah')->count();
            $femaleCount = Warga::where('rt_number', $rt)->where('gender', 'P')->where('status_warga', '!=', 'pindah')->count();

            // Kas RT
            $kasMasuk = Pembayaran::where('rt_number', $rt)->where('tipe', 'masuk')->sum('jumlah');
            $kasKeluar = Pembayaran::where('rt_number', $rt)->where('tipe', 'keluar')->sum('jumlah');
            $saldoKas = $kasMasuk - $kasKeluar;

            // Iuran Bulan Ini
            $paidIuranCount = Pembayaran::where('rt_number', $rt)
                ->where('tipe', 'masuk')
                ->where('kategori', 'like', '%Iuran%')
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->count();
            $iuranCompliance = $kkCount > 0 ? round(($paidIuranCount / $kkCount) * 100) : 0;

            // Pengaduan
            $pengaduanAktif = Pengaduan::where('rt_number', $rt)->whereIn('status', ['pending', 'diproses', 'menunggu'])->count();
            $pengaduanSelesai = Pengaduan::where('rt_number', $rt)->where('status', 'selesai')->count();

            // Sampah & Lingkungan
            $sampahKg = BankSampah::where('rt_number', $rt)->sum('berat_kg');

            // Laporan RT Status
            $laporan = LaporanRt::where('rt_number', $rt)
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->first();

            $statusLaporan = $laporan ? $laporan->status : 'belum_dikirim';

            $totalWargaRW += $wargaCount;
            $totalKkRW += $kkCount;
            $totalPengaduanAktifRW += $pengaduanAktif;
            $totalSampahTerkumpulRW += $sampahKg;

            $rtData[$rt] = [
                'rt_number' => $rt,
                'ketua_nama' => $ketuaRt ? $ketuaRt->name : 'Belum Ditunjuk',
                'ketua_phone' => $ketuaRt ? $ketuaRt->phone : '-',
                'warga_count' => $wargaCount,
                'kk_count' => $kkCount,
                'male_count' => $maleCount,
                'female_count' => $femaleCount,
                'saldo_kas' => $saldoKas,
                'iuran_compliance' => $iuranCompliance,
                'pengaduan_aktif' => $pengaduanAktif,
                'pengaduan_selesai' => $pengaduanSelesai,
                'sampah_kg' => $sampahKg,
                'status_laporan' => $statusLaporan,
                'laporan_id' => $laporan ? $laporan->id : null,
            ];
        }

        // Summary Laporan counts
        $laporanQuery = LaporanRt::where('bulan', $bulan)->where('tahun', $tahun);
        $countLaporanDiterima = (clone $laporanQuery)->where('status', 'diterima')->count();
        $countLaporanMenunggu = (clone $laporanQuery)->where('status', 'menunggu')->count();
        $countLaporanRevisi = (clone $laporanQuery)->where('status', 'perlu_revisi')->count();
        $countLaporanBelumKirim = 5 - ($countLaporanDiterima + $countLaporanMenunggu + $countLaporanRevisi);

        // Reminders list
        $reminders = [];
        foreach ($rtData as $rt => $data) {
            if ($data['status_laporan'] === 'belum_dikirim') {
                $reminders[] = [
                    'type' => 'warning',
                    'icon' => 'fa-file-circle-exclamation',
                    'text' => "RT {$rt} belum menyerahkan Laporan Bulanan periode " . Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F Y'),
                ];
            } elseif ($data['status_laporan'] === 'perlu_revisi') {
                $reminders[] = [
                    'type' => 'danger',
                    'icon' => 'fa-file-circle-xmark',
                    'text' => "Laporan RT {$rt} memerlukan revisi dan menunggu tindak lanjut Admin RT",
                ];
            }
            if ($data['pengaduan_aktif'] > 2) {
                $reminders[] = [
                    'type' => 'info',
                    'icon' => 'fa-comment-dots',
                    'text' => "RT {$rt} memiliki {$data['pengaduan_aktif']} pengaduan warga yang masih dalam proses penanganan",
                ];
            }
        }

        return view('monitoring.index', compact(
            'rtData',
            'rtList',
            'bulan',
            'tahun',
            'totalWargaRW',
            'totalKkRW',
            'totalPengaduanAktifRW',
            'totalSampahTerkumpulRW',
            'countLaporanDiterima',
            'countLaporanMenunggu',
            'countLaporanRevisi',
            'countLaporanBelumKirim',
            'reminders'
        ));
    }

    public function detailRt($rt_number, Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdminRw() && (!$user->isAdminRt() || $user->rt_number !== $rt_number)) {
            abort(403, 'Akses ditolak: Anda tidak memiliki hak akses untuk memonitor wilayah RT ' . $rt_number . '.');
        }

        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));

        $ketuaRt = User::where('rt_number', $rt_number)
            ->where(function ($q) {
                $q->where('role', 'admin')->orWhere('role', 'admin_rt');
            })->first();

        // 1. Administrasi Warga
        $wargas = Warga::where('rt_number', $rt_number)->where('status_warga', '!=', 'pindah')->get();
        $totalWarga = $wargas->count();
        $totalKk = $wargas->where('is_kk', true)->count();
        $maleCount = $wargas->where('gender', 'L')->count();
        $femaleCount = $wargas->where('gender', 'P')->count();
        $verifMenunggu = $wargas->where('status_verifikasi', 'menunggu')->count();
        $verifSah = $wargas->where('status_verifikasi', 'terverifikasi')->count();

        // 2. Keuangan
        $kasMasuk = Pembayaran::where('rt_number', $rt_number)->where('tipe', 'masuk')->sum('jumlah');
        $kasKeluar = Pembayaran::where('rt_number', $rt_number)->where('tipe', 'keluar')->sum('jumlah');
        $saldoKas = $kasMasuk - $kasKeluar;
        $recentTransactions = Pembayaran::where('rt_number', $rt_number)->orderBy('tanggal', 'desc')->limit(6)->get();

        // 3. Iuran Warga
        $iuranPaid = Pembayaran::where('rt_number', $rt_number)
            ->where('tipe', 'masuk')
            ->where('kategori', 'like', '%Iuran%')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->count();

        // 4. Pelayanan Surat
        $suratPengajuanCount = SuratPengajuan::where('rt_number', $rt_number)->count();
        $suratSelesaiCount = SuratPengajuan::where('rt_number', $rt_number)->where('status', 'selesai')->count();
        $suratPendingCount = SuratPengajuan::where('rt_number', $rt_number)->whereIn('status', ['pending_rt', 'pending_rw'])->count();

        // 5. Pengaduan
        $pengaduanBaru = Pengaduan::where('rt_number', $rt_number)->where('status', 'pending')->count();
        $pengaduanProses = Pengaduan::where('rt_number', $rt_number)->where('status', 'diproses')->count();
        $pengaduanSelesai = Pengaduan::where('rt_number', $rt_number)->where('status', 'selesai')->count();

        // 6. Bank Sampah & Lingkungan
        $totalSampahKg = BankSampah::where('rt_number', $rt_number)->sum('berat_kg');
        $recentLaporanLingkungan = LaporanLingkungan::where('rt_number', $rt_number)->orderBy('created_at', 'desc')->first();

        // 7. Laporan Bulanan RT
        $laporanList = LaporanRt::where('rt_number', $rt_number)->orderByDesc('tahun')->orderByDesc('bulan')->limit(12)->get();

        return view('monitoring.rt_detail', compact(
            'rt_number',
            'ketuaRt',
            'totalWarga',
            'totalKk',
            'maleCount',
            'femaleCount',
            'verifMenunggu',
            'verifSah',
            'saldoKas',
            'kasMasuk',
            'kasKeluar',
            'recentTransactions',
            'iuranPaid',
            'suratPengajuanCount',
            'suratSelesaiCount',
            'suratPendingCount',
            'pengaduanBaru',
            'pengaduanProses',
            'pengaduanSelesai',
            'totalSampahKg',
            'recentLaporanLingkungan',
            'laporanList',
            'bulan',
            'tahun'
        ));
    }
}
