<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Warga;
use App\Models\Pengumuman;
use App\Models\User;
use App\Models\SuratPengajuan;
use App\Models\Pengaduan;
use App\Models\Posyandu;
use App\Models\Umkm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isWarga = $user->isWarga();
        $isAdminRW = $user->isAdminRw();
        $isAdminRT = $user->isAdminRt();

        // 1. Get Warga record if user is warga
        $wargaRecord = null;
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
                    $pembayaranQuery->where('id', -1);
                }
            } elseif ($isAdminRT) {
                // Admin RT sees kas for their RT
                $pembayaranQuery->where('rt_number', $user->rt_number);
            }
            // Admin RW (or Bendahara) sees everything, no filter needed

            $pemasukan = (clone $pembayaranQuery)->where('tipe', 'masuk')->sum('jumlah');
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
            } elseif ($isWarga) {
                if ($wargaRecord && $wargaRecord->no_kk) {
                    $wargaQuery->where('no_kk', $wargaRecord->no_kk);
                } elseif ($user->rt_number) {
                    $wargaQuery->where('rt_number', $user->rt_number);
                }
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

        // 4. Data untuk Grafik (RBAC applied): 6 Bulan, Bulan Ini (4 pekan), Minggu Ini (7 hari)
        $applyRbacToPembayaran = function ($query) use ($isWarga, $wargaId, $isAdminRT, $user) {
            if ($isWarga) {
                if ($wargaId) {
                    $query->where('warga_id', $wargaId);
                } else {
                    $query->where('id', -1);
                }
            } elseif ($isAdminRT) {
                $query->where('rt_number', $user->rt_number);
            }
            return $query;
        };

        // 4A. Grafik 6 Bulan Terakhir (Tahunan / 6 Bulan)
        try {
            $templateBulan = collect();
            for ($i = 5; $i >= 0; $i--) {
                $targetDate = now()->subMonths($i);
                $templateBulan->put($targetDate->format('Y-m'), (object)[
                    'year_month'  => $targetDate->format('Y-m'),
                    'bulan_tahun' => $targetDate->translatedFormat('M Y'),
                    'masuk'       => 0,
                    'keluar'      => 0
                ]);
            }

            $rawChartDataQuery = Pembayaran::select(
                DB::raw("strftime('%Y-%m', tanggal) as year_month"),
                DB::raw("SUM(CASE WHEN tipe = 'masuk' THEN jumlah ELSE 0 END) as masuk"),
                DB::raw("SUM(CASE WHEN tipe = 'keluar' THEN jumlah ELSE 0 END) as keluar")
            )->where('tanggal', '>=', now()->subMonths(5)->startOfMonth());

            $applyRbacToPembayaran($rawChartDataQuery);

            $rawChartData = $rawChartDataQuery->groupBy('year_month')
                ->get()
                ->keyBy('year_month');

            $chartData = $templateBulan->map(function ($default, $key) use ($rawChartData) {
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

        // 4B. Grafik Bulan Ini (1-7, 8-14, 15-21, 22-Akhir) & Minggu Ini (7 Hari Terakhir)
        try {
            // Gunakan bulan terbaru yang memiliki transaksi jika bulan berjalan kosong, agar data tetap riil
            $refDateQuery = Pembayaran::query();
            $applyRbacToPembayaran($refDateQuery);
            $latestTxDate = (clone $refDateQuery)->max('tanggal');
            $refMonthDate = $latestTxDate ? Carbon::parse($latestTxDate) : now();

            $monthTxQuery = Pembayaran::whereYear('tanggal', $refMonthDate->year)
                ->whereMonth('tanggal', $refMonthDate->month);
            $applyRbacToPembayaran($monthTxQuery);
            $monthTransactions = $monthTxQuery->get();

            $buckets = [
                '1-7'   => ['masuk' => 0, 'keluar' => 0],
                '8-14'  => ['masuk' => 0, 'keluar' => 0],
                '15-21' => ['masuk' => 0, 'keluar' => 0],
                '22-' . $refMonthDate->daysInMonth => ['masuk' => 0, 'keluar' => 0],
            ];
            $lastBucketKey = '22-' . $refMonthDate->daysInMonth;

            foreach ($monthTransactions as $tx) {
                $day = Carbon::parse($tx->tanggal)->day;
                $bKey = $day <= 7 ? '1-7' : ($day <= 14 ? '8-14' : ($day <= 21 ? '15-21' : $lastBucketKey));
                if ($tx->tipe === 'masuk') {
                    $buckets[$bKey]['masuk'] += (float) $tx->jumlah;
                } else {
                    $buckets[$bKey]['keluar'] += (float) $tx->jumlah;
                }
            }

            $chartDataMonth = collect($buckets)->map(function ($val, $label) {
                return (object)[
                    'label'  => 'Tgl ' . $label,
                    'masuk'  => $val['masuk'],
                    'keluar' => $val['keluar'],
                ];
            })->values();

            // Minggu Ini (7 hari terakhir dari refMonthDate)
            $weekEnd = $refMonthDate->copy()->endOfDay();
            $weekStart = $refMonthDate->copy()->subDays(6)->startOfDay();
            $weekTxQuery = Pembayaran::whereBetween('tanggal', [$weekStart->toDateString(), $weekEnd->toDateString()]);
            $applyRbacToPembayaran($weekTxQuery);
            $weekTransactions = $weekTxQuery->get()->groupBy(function ($item) {
                return Carbon::parse($item->tanggal)->format('Y-m-d');
            });

            $chartDataWeek = collect();
            for ($d = 6; $d >= 0; $d--) {
                $dayDate = $refMonthDate->copy()->subDays($d);
                $dKey = $dayDate->format('Y-m-d');
                $dayGroup = $weekTransactions->get($dKey, collect());
                $chartDataWeek->push((object)[
                    'label'  => $dayDate->translatedFormat('d M'),
                    'masuk'  => (float) $dayGroup->where('tipe', 'masuk')->sum('jumlah'),
                    'keluar' => (float) $dayGroup->where('tipe', 'keluar')->sum('jumlah'),
                ]);
            }
        } catch (\Exception $e) {
            $chartDataMonth = collect();
            $chartDataWeek = collect();
            $refMonthDate = now();
        }

        // 5. Ambil pengeluaran terbaru (RBAC)
        try {
            $latestExpenseQuery = Pembayaran::where('tipe', 'keluar')->orderBy('tanggal', 'desc');
            if ($isAdminRT) {
                $latestExpenseQuery->where('rt_number', $user->rt_number);
            }
            if ($isWarga) {
                $latestExpense = null;
            } else {
                $latestExpense = $latestExpenseQuery->first();
            }

            $latestExpenseAmount = $latestExpense ? $latestExpense->jumlah : 0;
            $latestExpenseDate = $latestExpense ? Carbon::parse($latestExpense->tanggal)->translatedFormat('d M Y') : null;
        } catch (\Exception $e) {
            $latestExpenseAmount = 0;
            $latestExpenseDate = null;
        }

        // 6. Layanan & Status Summary (RBAC applied for RW, RT, and Warga)
        $newFeatureCounts = [
            'surat'          => 0,
            'surat_pending'  => 0,
            'surat_selesai'  => 0,
            'pengaduan'      => 0,
            'pengaduan_baru' => 0,
            'posyandu'       => 0,
            'umkm'           => 0,
        ];
        try {
            $suratQuery = SuratPengajuan::query();
            $pengaduanQuery = Pengaduan::query();
            $umkmQuery = Umkm::query();

            if ($isAdminRT) {
                $suratQuery->where('rt_number', $user->rt_number);
                $pengaduanQuery->where(function ($q) use ($user) {
                    $q->where('rt_number', $user->rt_number)
                      ->orWhereHas('user', function ($uq) use ($user) {
                          $uq->where('rt_number', $user->rt_number);
                      });
                });
                $umkmQuery->where('rt_number', $user->rt_number);
            } elseif ($isWarga) {
                $suratQuery->where('user_id', $user->id);
                $pengaduanQuery->where('user_id', $user->id);
            }

            $newFeatureCounts['surat'] = (clone $suratQuery)->count();
            $newFeatureCounts['surat_pending'] = (clone $suratQuery)->whereIn('status', ['pending_rt', 'pending_rw'])->count();
            $newFeatureCounts['surat_selesai'] = (clone $suratQuery)->where('status', 'selesai')->count();

            $newFeatureCounts['pengaduan'] = (clone $pengaduanQuery)->count();
            $newFeatureCounts['pengaduan_baru'] = (clone $pengaduanQuery)->where('status', 'pending')->count();

            $newFeatureCounts['posyandu'] = Posyandu::count();
            $newFeatureCounts['umkm'] = $umkmQuery->count();
        } catch (\Exception $e) {
            // keep zeros
        }

        $totalAdminRT = User::where('role', 'admin')->where('rt_number', '!=', '000')->count();
        $totalAdminRW = User::where('role', 'admin')->where('rt_number', '000')->count();

        // 7. Dynamic Info Cards for Pengumuman Terbaru & Status Iuran
        $currentMonth = date('m');
        $currentYear = date('Y');

        $wargaQuery2 = Warga::query();
        if ($isAdminRT) {
            $wargaQuery2->where('rt_number', $user->rt_number);
        }
        $totalKKForReminder = $wargaQuery2->where('is_kk', true)->count();

        $paidQuery = Pembayaran::where('tipe', 'masuk')
            ->where('kategori', 'like', '%Iuran%')
            ->whereMonth('tanggal', $currentMonth)
            ->whereYear('tanggal', $currentYear);

        if ($isAdminRT) {
            $paidQuery->where('rt_number', $user->rt_number);
        }

        $paidThisMonth = $paidQuery->distinct('warga_id')->count('warga_id');
        $unpaidKK = max(0, $totalKKForReminder - $paidThisMonth);

        // Status Iuran khusus Warga yang login
        $wargaIuranLunas = false;
        $wargaLastPayment = null;
        if ($isWarga && $wargaId) {
            $wargaIuranLunas = Pembayaran::where('warga_id', $wargaId)
                ->where('tipe', 'masuk')
                ->where('kategori', 'like', '%Iuran%')
                ->whereMonth('tanggal', $currentMonth)
                ->whereYear('tanggal', $currentYear)
                ->exists();

            $wargaLastPayment = Pembayaran::where('warga_id', $wargaId)
                ->where('tipe', 'masuk')
                ->orderBy('tanggal', 'desc')
                ->first();
        }

        // Jadwal Posyandu / Kegiatan Mendatang (dari tabel posyandus & pengumumans)
        try {
            $latestPosyandu = Posyandu::orderBy('tanggal', 'desc')->first();
            $upcomingPosyandus = Posyandu::orderBy('tanggal', 'desc')->take(3)->get();
        } catch (\Exception $e) {
            $latestPosyandu = null;
            $upcomingPosyandus = collect();
        }

        try {
            $pengumumans = Pengumuman::orderBy('is_pinned', 'desc')->orderBy('created_at', 'desc')->take(3)->get();
        } catch (\Exception $e) {
            $pengumumans = collect();
        }

        try {
            $featuredUmkm = Umkm::inRandomOrder()->first();
        } catch (\Exception $e) {
            $featuredUmkm = null;
        }

        // Ringkasan per-RT khusus Admin RW
        $rtSummaries = collect();
        if ($isAdminRW) {
            try {
                foreach (['006', '007', '008', '009', '010'] as $rtNum) {
                    $masukRt = Pembayaran::where('rt_number', $rtNum)->where('tipe', 'masuk')->sum('jumlah');
                    $keluarRt = Pembayaran::where('rt_number', $rtNum)->where('tipe', 'keluar')->sum('jumlah');
                    $wargaRtCount = Warga::where('rt_number', $rtNum)->count();
                    $kkRtCount = Warga::where('rt_number', $rtNum)->where('is_kk', true)->count();
                    $rtSummaries->push((object)[
                        'rt_number' => $rtNum,
                        'warga'     => $wargaRtCount,
                        'kk'        => $kkRtCount,
                        'saldo'     => $masukRt - $keluarRt,
                    ]);
                }
            } catch (\Exception $e) {
                $rtSummaries = collect();
            }
        }

        return view('dashboard', compact(
            'user',
            'wargaRecord',
            'pemasukan',
            'pengeluaran',
            'saldo',
            'totalKK',
            'totalJiwa',
            'chartData',
            'chartDataMonth',
            'chartDataWeek',
            'refMonthDate',
            'maleCount',
            'femaleCount',
            'latestExpenseAmount',
            'latestExpenseDate',
            'newFeatureCounts',
            'totalAdminRT',
            'totalAdminRW',
            'unpaidKK',
            'totalKKForReminder',
            'paidThisMonth',
            'wargaIuranLunas',
            'wargaLastPayment',
            'latestPosyandu',
            'upcomingPosyandus',
            'pengumumans',
            'featuredUmkm',
            'rtSummaries',
            'isWarga',
            'isAdminRT',
            'isAdminRW'
        ));
    }
}
