<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Warga;
use App\Models\Pengumuman;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // Pastikan import Carbon

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Pengumuman 
        try {
            $pengumuman = Pengumuman::where(function($query) {
                    $query->where('published_at', '<=', now())->orWhereNull('published_at');
                })
                ->orderBy('is_pinned', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();
        } catch (\Exception $e) {
            $pengumuman = collect();
        }

        // 2. Hitung Kas 
        try {
            $pemasukan = Pembayaran::where('tipe', 'masuk')->sum('jumlah');
            $pengeluaran = Pembayaran::where('tipe', 'keluar')->sum('jumlah');
        } catch (\Exception $e) {
            $pemasukan = 0;
            $pengeluaran = 0;
        }

        $saldo = $pemasukan - $pengeluaran;

        // 3. Total Warga (Tetap sesuai kodingan lu)
        try {
            $totalWarga = Warga::count();
        } catch (\Exception $e) {
            $totalWarga = User::where('role', 'user')->count();
        }

        // 4. Data untuk Grafik 
        try {
            // Langkah A: Buat susunan template 6 bulan terakhir dengan nilai default 0
            $templateBulan = collect();
            for ($i = 5; $i >= 0; $i--) {
                $targetDate = now()->subMonths($i);
                $templateBulan->put($targetDate->format('Y-m'), (object)[
                    'year_month'  => $targetDate->format('Y-m'),
                    'bulan_tahun' => $targetDate->format('F Y'), // Format Bahasa Inggris: "July 2026"
                    'masuk'       => 0,
                    'keluar'      => 0
                ]);
            }

            // Langkah B: Ambil data riil dari database menggunakan query SQL
            $rawChartData = Pembayaran::select(
                    DB::raw('DATE_FORMAT(tanggal, "%Y-%m") as year_month'),
                    DB::raw('DATE_FORMAT(tanggal, "%M %Y") as bulan_tahun'),
                    DB::raw("SUM(CASE WHEN tipe = 'masuk' THEN jumlah ELSE 0 END) as masuk"),
                    DB::raw("SUM(CASE WHEN tipe = 'keluar' THEN jumlah ELSE 0 END) as keluar")
                )
                ->where('tanggal', '>=', now()->subMonths(5)->startOfMonth())
                ->groupBy('year_month', 'bulan_tahun')
                ->get()
                ->keyBy('year_month');

            // Langkah C: Gabungkan (Merge) data riil database ke dalam template agar bulan kosong terisi 0
            $chartData = $templateBulan->map(function($default, $key) use ($rawChartData) {
                if ($rawChartData->has($key)) {
                    return (object)[
                        'year_month'  => $key,
                        'bulan_tahun' => $rawChartData[$key]->bulan_tahun,
                        'masuk'       => (float) $rawChartData[$key]->masuk,
                        'keluar'      => (float) $rawChartData[$key]->keluar,
                    ];
                }
                return $default;
            })->values();

        } catch (\Exception $e) {
            // FALLBACK: Logika pencadangan jika raw SQL di atas gagal (menggunakan Collection Laravel)
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

                $pembayarans = Pembayaran::where('tanggal', '>=', now()->subMonths(5)->startOfMonth())
                    ->orderBy('tanggal', 'asc')
                    ->get()
                    ->groupBy(function($item) {
                        return Carbon::parse($item->tanggal)->format('Y-m');
                    });

                $chartData = $templateBulan->map(function($default, $key) use ($pembayarans) {
                    if ($pembayarans->has($key)) {
                        $group = $pembayarans[$key];
                        return (object)[
                            'year_month'  => $key,
                            'bulan_tahun' => Carbon::parse($group->first()->tanggal)->format('F Y'),
                            'masuk'       => $group->where('tipe', 'masuk')->sum('jumlah'),
                            'keluar'      => $group->where('tipe', 'keluar')->sum('jumlah'),
                        ];
                    }
                    return $default;
                })->values();

            } catch (\Exception $ex) {
                $chartData = collect();
            }
        }

        // 5. Hitung gender (L / P)
        try {
            $maleCount = Warga::where('gender', 'L')->count();
            $femaleCount = Warga::where('gender', 'P')->count();
        } catch (\Exception $e) {
            $maleCount = 0;
            $femaleCount = 0;
        }

        // 6. Ambil pengeluaran terbaru untuk notifikasi tampilan
        try {
            $latestExpense = Pembayaran::where('tipe', 'keluar')
                ->orderBy('tanggal', 'desc')
                ->first();
            $latestExpenseAmount = $latestExpense ? $latestExpense->jumlah : 0;
            $latestExpenseDate = $latestExpense ? \Carbon\Carbon::parse($latestExpense->tanggal)->format('d M Y') : null;
        } catch (\Exception $e) {
            $latestExpenseAmount = 0;
            $latestExpenseDate = null;
        }

        // 7. Admin summary untuk fitur baru
        $newFeatureCounts = [];
        if ($user->isAdmin()) {
            $newFeatureCounts = [
                'surat' => \App\Models\Surat::count(),
                'pengaduan' => \App\Models\Pengaduan::count(),
                'posyandu' => \App\Models\Posyandu::count(),
                'umkm' => \App\Models\Umkm::count(),
            ];
        }

        return view('dashboard', compact(
            'user', 'pengumuman', 'pemasukan',
            'pengeluaran', 'saldo', 'totalWarga', 'chartData',
            'maleCount', 'femaleCount', 'latestExpenseAmount', 'latestExpenseDate',
            'newFeatureCounts'
        ));
    }
}
