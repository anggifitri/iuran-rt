<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Warga;
use App\Models\Pengumuman;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isWarga = $user->role === 'warga';
        $isAdminRW = $user->role === 'admin' && $user->rt_number === '000';
        $isAdminRT = $user->role === 'admin' && $user->rt_number !== '000';

        // 1. Get Warga record if user is warga
        $wargaId = null;
        if ($isWarga) {
            $wargaRecord = Warga::where('nik', $user->nik)->orWhere('no_kk', $user->no_kk)->first();
            if ($wargaRecord) {
                $wargaId = $wargaRecord->id;
            }
        }

        // 2. Hitung Kas (RBAC applied)
        try {
            $pembayaranQuery = Pembayaran::query();
            
            if ($isWarga) {
                // Warga only sees their own total payments
                if ($wargaId) {
                    $pembayaranQuery->where('warga_id', $wargaId);
                } else {
                    $pembayaranQuery->where('id', -1); // Force empty if not found
                }
            } elseif ($isAdminRT) {
                // Admin RT sees kas for their RT
                $pembayaranQuery->where('rt_number', $user->rt_number);
            }
            // Admin RW (or Bendahara) sees everything, no filter needed

            $pemasukan = (clone $pembayaranQuery)->where('tipe', 'masuk')->sum('jumlah');
            
            // Warga has no 'keluar', but just in case
            $pengeluaran = (clone $pembayaranQuery)->where('tipe', 'keluar')->sum('jumlah');
        } catch (\Exception $e) {
            $pemasukan = 0;
            $pengeluaran = 0;
        }

        $saldo = $pemasukan - $pengeluaran;

        // 3. Total Warga (RBAC applied)
        try {
            $wargaQuery = Warga::query();
            if ($isAdminRT) {
                $wargaQuery->where('rt_number', $user->rt_number);
            }
            
            $totalKK = (clone $wargaQuery)->where('is_kk', true)->count();
            $totalJiwa = (clone $wargaQuery)->count();
            
            $maleCount = (clone $wargaQuery)->where('gender', 'L')->count();
            $femaleCount = (clone $wargaQuery)->where('gender', 'P')->count();
        } catch (\Exception $e) {
            $totalKK = 0;
            $totalJiwa = 0;
            $maleCount = 0;
            $femaleCount = 0;
        }

        // 4. Data untuk Grafik (RBAC applied)
        try {
            $templateBulan = collect();
            for ($i = 5; $i >= 0; $i--) {
                $targetDate = now()->subMonths($i);
                $templateBulan->put($targetDate->format('Y-m'), (object)[
                    'year_month'  => $targetDate->format('Y-m'),
                    'bulan_tahun' => $targetDate->format('F Y'),
                    'masuk'       => 0,
                    'keluar'      => 0
                ]);
            }

            $rawChartDataQuery = Pembayaran::select(
                    DB::raw("strftime('%Y-%m', tanggal) as year_month"),
                    DB::raw("SUM(CASE WHEN tipe = 'masuk' THEN jumlah ELSE 0 END) as masuk"),
                    DB::raw("SUM(CASE WHEN tipe = 'keluar' THEN jumlah ELSE 0 END) as keluar")
                )
                ->where('tanggal', '>=', now()->subMonths(5)->startOfMonth());
                
            if ($isWarga) {
                if ($wargaId) $rawChartDataQuery->where('warga_id', $wargaId);
                else $rawChartDataQuery->where('id', -1);
            } elseif ($isAdminRT) {
                $rawChartDataQuery->where('rt_number', $user->rt_number);
            }

            $rawChartData = $rawChartDataQuery->groupBy('year_month')
                ->get()
                ->keyBy('year_month');

            $chartData = $templateBulan->map(function($default, $key) use ($rawChartData) {
                if ($rawChartData->has($key)) {
                    return (object)[
                        'year_month'  => $key,
                        'bulan_tahun' => $default->bulan_tahun,
                        'masuk'       => (float) $rawChartData[$key]->masuk,
                        'keluar'      => (float) $rawChartData[$key]->keluar,
                    ];
                }
                return $default;
            })->values();

        } catch (\Exception $e) {
            $chartData = collect();
        }

        // 6. Ambil pengeluaran terbaru (RBAC)
        try {
            $latestExpenseQuery = Pembayaran::where('tipe', 'keluar')->orderBy('tanggal', 'desc');
            if ($isAdminRT) {
                $latestExpenseQuery->where('rt_number', $user->rt_number);
            }
            // Warga doesn't really see pengeluaran, but if they do, we'll just not show it
            if ($isWarga) {
                $latestExpense = null;
            } else {
                $latestExpense = $latestExpenseQuery->first();
            }
            
            $latestExpenseAmount = $latestExpense ? $latestExpense->jumlah : 0;
            $latestExpenseDate = $latestExpense ? \Carbon\Carbon::parse($latestExpense->tanggal)->format('d M Y') : null;
        } catch (\Exception $e) {
            $latestExpenseAmount = 0;
            $latestExpenseDate = null;
        }

        // 7. Admin summary 
        $newFeatureCounts = [
            'surat' => 0,
            'pengaduan' => 0,
            'posyandu' => 0,
            'umkm' => 0,
        ];
        try {
            // Adjust based on RT
            $suratQuery = \App\Models\SuratPengajuan::query();
            $pengaduanQuery = \App\Models\Pengaduan::query();
            
            if ($isAdminRT) {
                $suratQuery->whereHas('user', function($q) use ($user) {
                    $q->where('rt_number', $user->rt_number);
                });
                // Pengaduan might not have user relation loaded properly here, we will just use basic count for now or try
                // assuming user is related
            }
            
            $newFeatureCounts['surat'] = $suratQuery->count();
            $newFeatureCounts['pengaduan'] = \App\Models\Pengaduan::count();
            $newFeatureCounts['posyandu'] = \App\Models\Posyandu::count();
            $newFeatureCounts['umkm'] = \App\Models\Umkm::count();
        } catch (\Exception $e) {
            // keep zeros
        }

        $totalAdminRT = User::where('role', 'admin')->where('rt_number', '!=', '000')->count();
        $totalAdminRW = User::where('role', 'admin')->where('rt_number', '000')->count();

        // 8. Dynamic Info Cards for Pengumuman Terbaru
        $currentMonth = date('m');
        $currentYear = date('Y');

        // Iuran reminder: count KK who haven't paid this month
        $wargaQuery2 = Warga::query();
        if ($isAdminRT) {
            $wargaQuery2->where('rt_number', $user->rt_number);
        }
        
        $totalKKForReminder = $wargaQuery2->where('is_kk', true)->count();
        
        $paidQuery = Pembayaran::where('tipe', 'masuk')
            ->where('kategori', 'like', '%Iuran%')
            ->whereMonth('tanggal', $currentMonth)
            ->whereYear('tanggal', $currentYear);
            
        if ($isAdminRT) $paidQuery->where('rt_number', $user->rt_number);
        
        $paidThisMonth = $paidQuery->distinct('warga_id')->count('warga_id');
        $unpaidKK = $totalKKForReminder - $paidThisMonth;

        try {
            $latestPosyandu = \App\Models\Posyandu::orderBy('tanggal', 'desc')->first();
        } catch (\Exception $e) {
            $latestPosyandu = null;
        }

        try {
            $featuredUmkm = \App\Models\Umkm::inRandomOrder()->first();
        } catch (\Exception $e) {
            $featuredUmkm = null;
        }

        return view('dashboard', compact(
            'user', 'pemasukan',
            'pengeluaran', 'saldo', 'totalKK', 'totalJiwa', 'chartData',
            'maleCount', 'femaleCount', 'latestExpenseAmount', 'latestExpenseDate',
            'newFeatureCounts', 'totalAdminRT', 'totalAdminRW',
            'unpaidKK', 'totalKKForReminder', 'paidThisMonth', 'latestPosyandu', 'featuredUmkm', 'isWarga', 'isAdminRT', 'isAdminRW'
        ));
    }
}
