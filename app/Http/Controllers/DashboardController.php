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
use App\Models\BankSampah;
use App\Models\BankSampahPenarikan;
use App\Models\LaporanRt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(?Request $request = null)
    {
        $request = $request ?: request();
        $user = Auth::user();

        // If Super Admin accesses main dashboard without role switcher, redirect to Platform Control Center
        if ($user->isSuperAdmin() && !$user->isRoleSwitched()) {
            return redirect()->route('super_admin.dashboard');
        }

        // Jika akun warga belum menyelesaikan verifikasi email, arahkan ke layar verifikasi email
        if ($user->role === 'warga' && !$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice')->with('warning', 'Alamat email Anda belum diverifikasi. Silakan periksa email Anda.');
        }

        $isWarga = $user->isWarga();
        $isAdminRW = $user->isAdminRw();
        $isAdminRT = $user->isAdminRt();

        // 1. Get Warga record if user is warga
        $wargaRecord = null;
        $wargaId = null;
        if ($isWarga) {
            $wargaRecord = $user->resolveWarga();
            if ($wargaRecord) {
                $wargaId = $wargaRecord->id;
            }
        }

        // 2. Hitung Kas (RBAC applied, sinkron 100% dengan Transaksi Kas)
        try {
            $pembayaranQuery = Pembayaran::query()->where('status', '!=', 'dibatalkan')->where('status', '!=', 'pending');

            if ($isWarga) {
                // Warga only sees their own / family payments
                $familyIds = $user->familyWargaIds();
                if (!empty($familyIds)) {
                    $pembayaranQuery->whereIn('warga_id', $familyIds);
                } elseif ($wargaId) {
                    $pembayaranQuery->where('warga_id', $wargaId);
                } else {
                    $pembayaranQuery->where('id', -1);
                }
            } elseif ($isAdminRT) {
                // Admin RT sees kas for their RT
                $pembayaranQuery->where('rt_number', $user->rt_number);
            }
            // Admin RW (or Bendahara) sees everything (RT 000 + RT 006-010)

            $pemasukan = (clone $pembayaranQuery)->where('tipe', 'masuk')->sum('jumlah');
            $pengeluaran = (clone $pembayaranQuery)->where('tipe', 'keluar')->sum('jumlah');
            $saldo = $pemasukan - $pengeluaran;

            $currentMonth = (int)date('m');
            $currentYear = (int)date('Y');
            $pemasukanBulanIni = (clone $pembayaranQuery)->where('tipe', 'masuk')->whereMonth('tanggal', $currentMonth)->whereYear('tanggal', $currentYear)->sum('jumlah');
            $pengeluaranBulanIni = (clone $pembayaranQuery)->where('tipe', 'keluar')->whereMonth('tanggal', $currentMonth)->whereYear('tanggal', $currentYear)->sum('jumlah');

            // Ringkasan Kategori Keuangan
            $kategoriMasuk = (clone $pembayaranQuery)->where('tipe', 'masuk')
                ->select('kategori', DB::raw('SUM(jumlah) as total'))
                ->groupBy('kategori')->orderByDesc('total')->take(5)->get();

            $kategoriKeluar = (clone $pembayaranQuery)->where('tipe', 'keluar')
                ->select('kategori', DB::raw('SUM(jumlah) as total'))
                ->groupBy('kategori')->orderByDesc('total')->take(5)->get();
        } catch (\Exception $e) {
            $pemasukan = 0;
            $pengeluaran = 0;
            $saldo = 0;
            $pemasukanBulanIni = 0;
            $pengeluaranBulanIni = 0;
            $kategoriMasuk = collect();
            $kategoriKeluar = collect();
        }

        // 3. Total Warga (RBAC applied)
        try {
            $wargaQuery = Warga::query();
            if ($isAdminRT) {
                $wargaQuery->where('rt_number', $user->rt_number);
            } elseif ($isWarga) {
                if ($wargaRecord && !empty(trim($wargaRecord->no_kk ?? ''))) {
                    $wargaQuery->where('no_kk', trim($wargaRecord->no_kk));
                } elseif ($wargaRecord) {
                    $wargaQuery->where('id', $wargaRecord->id);
                } else {
                    $wargaQuery->where('id', -1);
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

        // 4. Data untuk Grafik (RBAC applied, sinkron dengan Transaksi Kas)
        try {
            // 4A. Grafik 12 Bulan Tahun 2026 (Januari s/d Desember Lengkap)
            $chartYearQuery = Pembayaran::where('status', '!=', 'dibatalkan')->where('status', '!=', 'pending')->whereYear('tanggal', 2026);
            if ($isWarga) {
                $familyIds = $user->familyWargaIds();
                if (!empty($familyIds)) $chartYearQuery->whereIn('warga_id', $familyIds); elseif ($wargaId) $chartYearQuery->where('warga_id', $wargaId); else $chartYearQuery->where('id', -1);
            } elseif ($isAdminRT) {
                $chartYearQuery->where('rt_number', $user->rt_number);
            }
            $rawYearData = $chartYearQuery->get()->groupBy(function($item) {
                return Carbon::parse($item->tanggal)->format('Y-m');
            });

            $chartDataYear = collect();
            for ($m = 1; $m <= 12; $m++) {
                $monthDate = Carbon::create(2026, $m, 1);
                $key = $monthDate->format('Y-m');
                $group = $rawYearData->get($key);
                $mVal = $group ? (float)$group->where('tipe', 'masuk')->sum('jumlah') : 0;
                $kVal = $group ? (float)$group->where('tipe', 'keluar')->sum('jumlah') : 0;
                $chartDataYear->push((object)[
                    'year_month'  => $key,
                    'bulan_tahun' => $monthDate->translatedFormat('M Y'),
                    'label'       => $monthDate->translatedFormat('M'),
                    'label_full'  => $monthDate->translatedFormat('F Y'),
                    'masuk'       => $mVal,
                    'keluar'      => $kVal,
                    'net'         => $mVal - $kVal,
                ]);
            }

            // 4B. Grafik 6 Bulan Terakhir
            $chart6mQuery = Pembayaran::where('status', '!=', 'dibatalkan')->where('status', '!=', 'pending')
                ->where('tanggal', '>=', now()->subMonths(5)->startOfMonth()->toDateString());
            if ($isWarga) {
                $familyIds = $user->familyWargaIds();
                if (!empty($familyIds)) $chart6mQuery->whereIn('warga_id', $familyIds); elseif ($wargaId) $chart6mQuery->where('warga_id', $wargaId); else $chart6mQuery->where('id', -1);
            } elseif ($isAdminRT) {
                $chart6mQuery->where('rt_number', $user->rt_number);
            }
            $raw6mData = $chart6mQuery->get()->groupBy(function($item) {
                return Carbon::parse($item->tanggal)->format('Y-m');
            });

            $chartData6m = collect();
            for ($i = 5; $i >= 0; $i--) {
                $mDate = now()->subMonths($i);
                $key = $mDate->format('Y-m');
                $group = $raw6mData->get($key);
                $mVal = $group ? (float)$group->where('tipe', 'masuk')->sum('jumlah') : 0;
                $kVal = $group ? (float)$group->where('tipe', 'keluar')->sum('jumlah') : 0;
                $chartData6m->push((object)[
                    'year_month'  => $key,
                    'bulan_tahun' => $mDate->translatedFormat('M Y'),
                    'label'       => $mDate->translatedFormat('M Y'),
                    'label_full'  => $mDate->translatedFormat('F Y'),
                    'masuk'       => $mVal,
                    'keluar'      => $kVal,
                    'net'         => $mVal - $kVal,
                ]);
            }

            // 4C. Grafik 7 Hari Terakhir
            $chartWeekQuery = Pembayaran::where('status', '!=', 'dibatalkan')->where('status', '!=', 'pending')
                ->where('tanggal', '>=', now()->subDays(6)->toDateString());
            if ($isWarga) {
                $familyIds = $user->familyWargaIds();
                if (!empty($familyIds)) $chartWeekQuery->whereIn('warga_id', $familyIds); elseif ($wargaId) $chartWeekQuery->where('warga_id', $wargaId); else $chartWeekQuery->where('id', -1);
            } elseif ($isAdminRT) {
                $chartWeekQuery->where('rt_number', $user->rt_number);
            }
            $rawWeekData = $chartWeekQuery->get()->groupBy(function($item) {
                return Carbon::parse($item->tanggal)->format('Y-m-d');
            });

            $chartDataWeek = collect();
            for ($d = 6; $d >= 0; $d--) {
                $dayDate = now()->subDays($d);
                $key = $dayDate->format('Y-m-d');
                $group = $rawWeekData->get($key);
                $mVal = $group ? (float)$group->where('tipe', 'masuk')->sum('jumlah') : 0;
                $kVal = $group ? (float)$group->where('tipe', 'keluar')->sum('jumlah') : 0;
                $chartDataWeek->push((object)[
                    'label'      => $dayDate->translatedFormat('d M'),
                    'label_full' => $dayDate->translatedFormat('d F Y'),
                    'masuk'      => $mVal,
                    'keluar'     => $kVal,
                    'net'        => $mVal - $kVal,
                ]);
            }

            // Default chart data for initial view
            $chartData = $chartDataYear;
            $chartDataMonth = $chartData6m;
            $refMonthDate = now();
        } catch (\Exception $e) {
            $chartData = collect();
            $chartDataYear = collect();
            $chartData6m = collect();
            $chartDataWeek = collect();
            $chartDataMonth = collect();
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
                $effectiveUserIds = array_filter([$user->id, $wargaRecord?->user_id]);
                $familyIds = $user->familyWargaIds();
                $suratQuery->whereIn('user_id', $effectiveUserIds);
                $pengaduanQuery->where(function ($q) use ($effectiveUserIds, $familyIds, $wargaId) {
                    $q->whereIn('user_id', $effectiveUserIds);
                    if (!empty($familyIds)) {
                        $q->orWhereIn('warga_id', $familyIds);
                    } elseif ($wargaId) {
                        $q->orWhere('warga_id', $wargaId);
                    }
                });
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

        // Hitung Admin RT aktif hanya pada wilayah operasional aktif RW 018 (RT 006 - RT 010)
        $totalAdminRT = User::where(function ($q) {
                $q->where('role', 'admin_rt')->orWhere('role', 'admin');
            })
            ->where('rw_number', '018')
            ->whereIn('rt_number', ['006', '007', '008', '009', '010'])
            ->where(function ($q) {
                $q->whereNull('status')->orWhere('status', 'active');
            })
            ->distinct('rt_number')
            ->count('rt_number');

        if ($totalAdminRT === 0) {
            $totalAdminRT = 5;
        }

        $totalAdminRW = User::where(function ($q) {
                $q->where('role', 'admin_rw')->orWhere('role', 'admin');
            })
            ->where('rw_number', '018')
            ->where('rt_number', '000')
            ->where(function ($q) {
                $q->whereNull('status')->orWhere('status', 'active');
            })
            ->count();

        if ($totalAdminRW === 0) {
            $totalAdminRW = 1;
        }

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

        // Status Iuran & Ringkasan Tagihan khusus Warga yang login (Point 5.E)
        $wargaIuranLunas = false;
        $wargaLastPayment = null;
        $wargaDaftarTagihan = collect();
        $wargaTagihanAktif = 0;
        $wargaStatusTagihan = 'Belum Bayar';
        $wargaTotalTunggakan = 0;
        $wargaCountBelumBayar = 0;
        $wargaCountMendekatiTempo = 0;
        $wargaCountMenungguKonfirmasi = 0;
        $wargaCountTerlambat = 0;

        if ($isWarga && $wargaId) {
            $standardNominal = 100000;
            $currentDay = (int)date('d');

            for ($i = 0; $i < 6; $i++) {
                $periodDate = now()->subMonths($i);
                $pMonth = (int)$periodDate->format('m');
                $pYear = (int)$periodDate->format('Y');

                $isCur = ($i === 0);
                $dueDate = Carbon::create($pYear, $pMonth, 10);

                $familyIds = $user->familyWargaIds();
                $txPaid = Pembayaran::where(function ($q) use ($familyIds, $wargaId) {
                        if (!empty($familyIds)) {
                            $q->whereIn('warga_id', $familyIds);
                        } else {
                            $q->where('warga_id', $wargaId);
                        }
                    })
                    ->where('status', '!=', 'dibatalkan')
                    ->where('tipe', 'masuk')
                    ->where('kategori', 'like', '%Iuran%')
                    ->whereMonth('tanggal', $pMonth)
                    ->whereYear('tanggal', $pYear)
                    ->first();

                $itemPending = \App\Models\PembayaranIuran::where(function ($q) use ($familyIds, $wargaId) {
                        if (!empty($familyIds)) {
                            $q->whereIn('warga_id', $familyIds);
                        } else {
                            $q->where('warga_id', $wargaId);
                        }
                    })
                    ->where('bulan', $pMonth)
                    ->where('tahun', $pYear)
                    ->orderByDesc('id')
                    ->first();

                $st = 'Belum Bayar';
                if ($itemPending && $itemPending->status === 'pending') {
                    $st = 'Menunggu Konfirmasi';
                    $wargaCountMenungguKonfirmasi++;
                } elseif ($txPaid || ($itemPending && $itemPending->status === 'confirmed')) {
                    $st = 'Lunas';
                    if ($isCur) $wargaIuranLunas = true;
                } else {
                    $wargaCountBelumBayar++;
                    $wargaTotalTunggakan += $standardNominal;
                    if ($isCur) {
                        $wargaTagihanAktif += $standardNominal;
                        if ($currentDay >= 7 && $currentDay <= 10) {
                            $wargaCountMendekatiTempo++;
                        } elseif ($currentDay > 10) {
                            $wargaCountTerlambat++;
                            $st = 'Terlambat';
                        }
                    } else {
                        $wargaCountTerlambat++;
                        $st = 'Terlambat';
                    }
                }

                if ($isCur) {
                    $wargaStatusTagihan = $st;
                }

                $wargaDaftarTagihan->push((object)[
                    'periode' => $periodDate->translatedFormat('F Y'),
                    'bulan' => $pMonth,
                    'tahun' => $pYear,
                    'nominal' => $standardNominal,
                    'status' => $st,
                    'due_date' => $dueDate->translatedFormat('d M Y'),
                    'pembayaran_id' => $itemPending ? $itemPending->id : ($txPaid ? $txPaid->id : null),
                ]);
            }

            $wargaLastPayment = Pembayaran::where('warga_id', $wargaId)
                ->where('status', '!=', 'dibatalkan')
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

        // 8. Bank Sampah Metrics (Data Riil Terpadu)
        $bankSampahKgRw = 0;
        $bankSampahRupiahRw = 0;
        $bankSampahKgRt = 0;
        $bankSampahRupiahRt = 0;
        $bankSampahSaldoWarga = 0;
        $bankSampahKgWarga = 0;

        try {
            if (class_exists(BankSampah::class)) {
                if ($isAdminRW) {
                    $bankSampahKgRw = (float) BankSampah::where('status', 'diterima')->sum('berat_kg');
                    $bankSampahRupiahRw = (float) BankSampah::where('status', 'diterima')->sum('nilai_rupiah');
                } elseif ($isAdminRT) {
                    $bankSampahKgRt = (float) BankSampah::where('status', 'diterima')->where('rt_number', $user->rt_number)->sum('berat_kg');
                    $bankSampahRupiahRt = (float) BankSampah::where('status', 'diterima')->where('rt_number', $user->rt_number)->sum('nilai_rupiah');
                } elseif ($isWarga) {
                    $familyIds = $user->familyWargaIds();
                    $effectiveUserIds = array_filter([$user->id, $wargaRecord?->user_id]);

                    $totSetoran = (float) BankSampah::where('status', 'diterima')
                        ->where(function ($q) use ($familyIds, $wargaId, $effectiveUserIds) {
                            if (!empty($familyIds)) {
                                $q->whereIn('warga_id', $familyIds);
                            } elseif ($wargaId) {
                                $q->where('warga_id', $wargaId);
                            }
                            if (!empty($effectiveUserIds)) {
                                $q->orWhereIn('user_id', $effectiveUserIds);
                            }
                        })->sum('nilai_rupiah');

                    $totKg = (float) BankSampah::where('status', 'diterima')
                        ->where(function ($q) use ($familyIds, $wargaId, $effectiveUserIds) {
                            if (!empty($familyIds)) {
                                $q->whereIn('warga_id', $familyIds);
                            } elseif ($wargaId) {
                                $q->where('warga_id', $wargaId);
                            }
                            if (!empty($effectiveUserIds)) {
                                $q->orWhereIn('user_id', $effectiveUserIds);
                            }
                        })->sum('berat_kg');

                    $totPenarikan = 0;
                    if (class_exists(BankSampahPenarikan::class)) {
                        $totPenarikan = (float) BankSampahPenarikan::where('status', 'selesai')
                            ->where(function ($q) use ($familyIds, $wargaId, $effectiveUserIds) {
                                if (!empty($familyIds)) {
                                    $q->whereIn('warga_id', $familyIds);
                                } elseif ($wargaId) {
                                    $q->where('warga_id', $wargaId);
                                }
                                if (!empty($effectiveUserIds)) {
                                    $q->orWhereIn('user_id', $effectiveUserIds);
                                }
                            })->sum('jumlah_penarikan');
                    }

                    $bankSampahSaldoWarga = max(0, $totSetoran - $totPenarikan);
                    $bankSampahKgWarga = $totKg;
                }
            }
        } catch (\Throwable $e) {
            // keep zeros
        }

        // 9. Widget Operasional Khusus Admin RT (Antrean Surat & Status Laporan Resmi Bulan Berjalan)
        $suratPendingRtCount = 0;
        $statusLaporanRtBerjalan = 'belum_dibuat';
        $statusLaporanRtLabel = 'Belum Dibuat';
        $periodeLaporanRtBerjalan = Carbon::now()->translatedFormat('F Y');

        if ($isAdminRT) {
            try {
                $suratPendingRtCount = SuratPengajuan::where('rt_number', $user->rt_number)
                    ->where('status', 'pending_rt')
                    ->count();

                $cMonth = date('m');
                $cYear = date('Y');
                $laporanRtBulanBerjalan = LaporanRt::where('rt_number', $user->rt_number)
                    ->where(function ($q) use ($cMonth) {
                        $q->where('bulan', $cMonth)->orWhere('bulan', (int)$cMonth);
                    })
                    ->where('tahun', $cYear)
                    ->first();

                if ($laporanRtBulanBerjalan) {
                    $statusLaporanRtBerjalan = $laporanRtBulanBerjalan->status;
                    $statusLaporanRtLabel = match ($laporanRtBulanBerjalan->status) {
                        'draft' => 'Draft',
                        'menunggu' => 'Menunggu Verifikasi RW',
                        'diterima' => 'Diterima',
                        'perlu_revisi' => 'Perlu Revisi',
                        default => ucfirst($laporanRtBulanBerjalan->status),
                    };
                }
            } catch (\Throwable $e) {
                // keep defaults
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
            'isAdminRW',
            'pemasukanBulanIni',
            'pengeluaranBulanIni',
            'kategoriMasuk',
            'kategoriKeluar',
            'chartDataYear',
            'chartData6m',
            'wargaTagihanAktif',
            'wargaStatusTagihan',
            'wargaTotalTunggakan',
            'wargaCountBelumBayar',
            'wargaCountMendekatiTempo',
            'wargaCountMenungguKonfirmasi',
            'wargaCountTerlambat',
            'wargaDaftarTagihan',
            'bankSampahKgRw',
            'bankSampahRupiahRw',
            'bankSampahKgRt',
            'bankSampahRupiahRt',
            'bankSampahSaldoWarga',
            'bankSampahKgWarga',
            'suratPendingRtCount',
            'statusLaporanRtBerjalan',
            'statusLaporanRtLabel',
            'periodeLaporanRtBerjalan'
        ));
    }
}
