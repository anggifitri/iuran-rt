<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pembayaran;
use App\Models\PembayaranIuran;
use App\Models\Iuran;
use App\Models\Warga;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CompleteFinanceSeeder extends Seeder
{
    public function run()
    {
        $this->command->info("Memulai Seeding Kelengkapan Transaksi Kas & Iuran Warga 2026...");

        $masterIuran = Iuran::firstOrCreate(
            ['name' => 'Iuran Kebersihan & Keamanan Bulanan'],
            [
                'amount' => 100000,
                'periode' => 'bulanan',
                'type' => 'wajib',
                'due_date' => '2026-11-10',
                'target_wilayah' => 'all',
                'target_pembayar' => 'kk',
                'is_active' => true,
                'description' => 'Iuran wajib kebersihan lingkungan dan pos keamanan 24 jam seluruh KK.'
            ]
        );

        $adminRw = User::where('rt_number', '000')->first() ?? User::where('role', 'admin')->first();
        $adminRwId = $adminRw ? $adminRw->id : 1;

        $kepalaKeluargas = Warga::where('is_kk', true)->where('status_warga', '!=', 'pindah')->get();
        if ($kepalaKeluargas->isEmpty()) {
            $this->command->warn("Tidak ada data warga KK ditemukan.");
            return;
        }

        $rtList = ['006', '007', '008', '009', '010'];
        $year = 2026;

        // Loop bulan 1 sampai 12
        for ($month = 1; $month <= 12; $month++) {
            $monthStr = sprintf('%02d', $month);
            $startDate = Carbon::create($year, $month, 1);
            $daysInMonth = $startDate->daysInMonth;

            // 1. PEMBAYARAN IURAN WARGA (KK)
            // Setiap RT memiliki tingkat kepatuhan 75% - 90%
            foreach ($rtList as $rtNum) {
                $kkInRt = $kepalaKeluargas->where('rt_number', $rtNum);
                if ($kkInRt->isEmpty()) continue;

                // Tentukan siapa yang bayar di bulan ini (75% - 90% acak tetap berbasis hash)
                foreach ($kkInRt as $kk) {
                    // Gunakan deterministic hash agar idempotent
                    $hashVal = crc32("{$kk->id}-{$year}-{$month}");
                    $shouldPay = ($hashVal % 100) < 82; // 82% bayar tepat waktu

                    if (!$shouldPay && $month >= 11) {
                        // Untuk bulan 11 & 12, sebagian dibiarkan belum bayar atau pending untuk testing antrean
                        if (($hashVal % 10) === 0 && $month === 11) {
                            // 10% buat status pending untuk pengujian verifikasi Admin
                            $existsPending = PembayaranIuran::where('warga_id', $kk->id)->where('bulan', $month)->where('tahun', $year)->first();
                            if (!$existsPending) {
                                PembayaranIuran::create([
                                    'warga_id' => $kk->id,
                                    'user_id' => User::where('nik', $kk->nik)->value('id') ?? $adminRwId,
                                    'iuran_id' => $masterIuran->id,
                                    'rt_number' => $rtNum,
                                    'bulan' => $month,
                                    'tahun' => $year,
                                    'nominal_tagihan' => 100000,
                                    'amount_paid' => 100000,
                                    'payment_method' => 'Transfer Bank',
                                    'payment_date' => $startDate->copy()->addDays(5),
                                    'status' => 'pending',
                                    'notes' => 'Pengajuan pembayaran via transfer m-Banking',
                                ]);
                            }
                        }
                        continue;
                    }

                    // Cek apakah transaksi sudah ada untuk warga ini di bulan & tahun ini
                    $existsTx = Pembayaran::where('warga_id', $kk->id)
                        ->where('status', '!=', 'dibatalkan')
                        ->where('tipe', 'masuk')
                        ->where('kategori', 'like', '%Iuran%')
                        ->whereMonth('tanggal', $month)
                        ->whereYear('tanggal', $year)
                        ->first();

                    if (!$existsTx) {
                        $payDay = min($daysInMonth, 1 + ($hashVal % 20)); // Bayar antara tanggal 1 - 20
                        $tanggalBayar = Carbon::create($year, $month, $payDay)->format('Y-m-d');
                        $metode = ($hashVal % 3 === 0) ? 'Transfer Bank' : (($hashVal % 3 === 1) ? 'QRIS' : 'Tunai');

                        $nextId = (Pembayaran::max('id') ?? 0) + 1;
                        $nomorTx = sprintf('TRX-%s-%05d', $year, $nextId);

                        // Buat record pembayaran iuran
                        $pIuran = PembayaranIuran::firstOrCreate(
                            [
                                'warga_id' => $kk->id,
                                'bulan' => $month,
                                'tahun' => $year,
                            ],
                            [
                                'user_id' => User::where('nik', $kk->nik)->value('id') ?? $adminRwId,
                                'iuran_id' => $masterIuran->id,
                                'rt_number' => $rtNum,
                                'nominal_tagihan' => 100000,
                                'amount_paid' => 100000,
                                'payment_method' => $metode,
                                'payment_date' => $tanggalBayar,
                                'status' => 'confirmed',
                                'confirmed_by' => $adminRwId,
                                'notes' => "Iuran Lingkungan Periode {$monthStr}/{$year}",
                            ]
                        );

                        // Buat jurnal kas masuk
                        $tx = Pembayaran::create([
                            'nomor_transaksi' => $nomorTx,
                            'warga_id' => $kk->id,
                            'rt_number' => $rtNum,
                            'tipe' => 'masuk',
                            'jumlah' => 100000,
                            'kategori' => 'Iuran Bulanan',
                            'keterangan' => "Iuran Lingkungan Periode {$monthStr}/{$year} a.n {$kk->nama}",
                            'sumber_tujuan' => "Warga RT {$rtNum} (Blok {$kk->blok_rumah})",
                            'metode_pembayaran' => $metode,
                            'nomor_referensi' => "IURAN-{$pIuran->id}",
                            'status' => 'selesai',
                            'pembayaran_id' => $pIuran->id,
                            'created_by' => $adminRwId,
                            'tanggal' => $tanggalBayar,
                        ]);

                        $pIuran->transaction_id = $tx->id;
                        $pIuran->save();
                    }
                }
            }

            // 2. PEMASUKAN NON-IURAN (Donasi Warga, Bank Sampah, Kas Lainnya)
            $nonIuranCount = Pembayaran::whereMonth('tanggal', $month)
                ->whereYear('tanggal', $year)
                ->where('tipe', 'masuk')
                ->where('kategori', '!=', 'Iuran Bulanan')
                ->count();

            if ($nonIuranCount < 2) {
                // Tambah Donasi Warga
                $donor = $kepalaKeluargas->random();
                $nextId = (Pembayaran::max('id') ?? 0) + 1;
                Pembayaran::create([
                    'nomor_transaksi' => sprintf('TRX-%s-%05d', $year, $nextId),
                    'warga_id' => $donor->id,
                    'rt_number' => $donor->rt_number,
                    'tipe' => 'masuk',
                    'jumlah' => rand(3, 10) * 100000, // 300rb - 1jt
                    'kategori' => 'Donasi Warga',
                    'keterangan' => "Donasi sukarela sarana fasilitas lingkungan dari {$donor->nama}",
                    'sumber_tujuan' => "Donatur {$donor->nama}",
                    'metode_pembayaran' => 'Transfer Bank',
                    'status' => 'selesai',
                    'created_by' => $adminRwId,
                    'tanggal' => Carbon::create($year, $month, rand(5, 20))->format('Y-m-d'),
                ]);

                // Tambah Hasil Penjualan Bank Sampah
                $nextId = (Pembayaran::max('id') ?? 0) + 1;
                Pembayaran::create([
                    'nomor_transaksi' => sprintf('TRX-%s-%05d', $year, $nextId),
                    'warga_id' => null,
                    'rt_number' => '000', // Kas RW
                    'tipe' => 'masuk',
                    'jumlah' => rand(8, 20) * 100000, // 800rb - 2jt
                    'kategori' => 'Pendapatan Bank Sampah',
                    'keterangan' => "Setoran hasil timbang & daur ulang unit Bank Sampah RW 018 bulan {$monthStr}/{$year}",
                    'sumber_tujuan' => "Koperasi Bank Sampah RW 018",
                    'metode_pembayaran' => 'Tunai',
                    'status' => 'selesai',
                    'created_by' => $adminRwId,
                    'tanggal' => Carbon::create($year, $month, rand(21, min(28, $daysInMonth)))->format('Y-m-d'),
                ]);
            }

            // 3. PENGELUARAN KAS OPERASIONAL (Per RT & Kas RW)
            $keluarCount = Pembayaran::whereMonth('tanggal', $month)
                ->whereYear('tanggal', $year)
                ->where('tipe', 'keluar')
                ->count();

            if ($keluarCount < 6) {
                // Pengeluaran Rutin Kas Induk RW (RT 000)
                $pengeluaranRw = [
                    ['Honor Petugas Keamanan Utama RW', 2500000, 'Keamanan & Ronda', rand(25, min(28, $daysInMonth))],
                    ['Biaya Listrik Lampu Jalan & Gerbang RW', rand(6, 10) * 100000, 'Listrik & Air Fasum', rand(10, 15)],
                    ['Pemeliharaan Sarana Fasum & Taman RW', rand(5, 15) * 100000, 'Perbaikan & Maintenance', rand(12, 22)],
                ];

                // Tambahan acara khusus
                if ($month === 8) {
                    $pengeluaranRw[] = ['Kepanitiaan & Lomba HUT Kemerdekaan RI RW 018', 4500000, 'Kegiatan Masyarakat', 16];
                } elseif ($month === 5 || $month === 4) {
                    $pengeluaranRw[] = ['Bantuan Sosial & Konsumsi Halal Bihalal Warga', 2500000, 'Kegiatan Masyarakat', 18];
                } else {
                    $pengeluaranRw[] = ['Konsumsi & Perlengkapan Rapat Pengurus RW', rand(3, 6) * 100000, 'Operasional & ATK', rand(18, 24)];
                }

                foreach ($pengeluaranRw as $exp) {
                    $nextId = (Pembayaran::max('id') ?? 0) + 1;
                    Pembayaran::create([
                        'nomor_transaksi' => sprintf('TRX-%s-%05d', $year, $nextId),
                        'warga_id' => null,
                        'rt_number' => '000',
                        'tipe' => 'keluar',
                        'jumlah' => $exp[1],
                        'kategori' => $exp[2],
                        'keterangan' => $exp[0] . " periode {$monthStr}/{$year}",
                        'sumber_tujuan' => "Vendor / Pihak Terkait",
                        'metode_pembayaran' => 'Tunai',
                        'status' => 'selesai',
                        'created_by' => $adminRwId,
                        'tanggal' => Carbon::create($year, $month, min($daysInMonth, $exp[3]))->format('Y-m-d'),
                    ]);
                }

                // Pengeluaran Masing-Masing RT (RT 006 s/d 010)
                foreach ($rtList as $rt) {
                    // Petugas kebersihan RT
                    $nextId = (Pembayaran::max('id') ?? 0) + 1;
                    Pembayaran::create([
                        'nomor_transaksi' => sprintf('TRX-%s-%05d', $year, $nextId),
                        'warga_id' => null,
                        'rt_number' => $rt,
                        'tipe' => 'keluar',
                        'jumlah' => rand(12, 18) * 100000, // 1.2jt - 1.8jt
                        'kategori' => 'Kebersihan & Sampah',
                        'keterangan' => "Honor petugas angkut sampah lingkungan RT {$rt} bulan {$monthStr}/{$year}",
                        'sumber_tujuan' => "Petugas Kebersihan RT {$rt}",
                        'metode_pembayaran' => 'Tunai',
                        'status' => 'selesai',
                        'created_by' => $adminRwId,
                        'tanggal' => Carbon::create($year, $month, rand(23, min(28, $daysInMonth)))->format('Y-m-d'),
                    ]);

                    // Keamanan & Ronda RT
                    $nextId = (Pembayaran::max('id') ?? 0) + 1;
                    Pembayaran::create([
                        'nomor_transaksi' => sprintf('TRX-%s-%05d', $year, $nextId),
                        'warga_id' => null,
                        'rt_number' => $rt,
                        'tipe' => 'keluar',
                        'jumlah' => rand(8, 14) * 100000,
                        'kategori' => 'Keamanan & Ronda',
                        'keterangan' => "Honor jaga pos kamling & konsumsi ronda malam RT {$rt}",
                        'sumber_tujuan' => "Petugas Keamanan RT {$rt}",
                        'metode_pembayaran' => 'Tunai',
                        'status' => 'selesai',
                        'created_by' => $adminRwId,
                        'tanggal' => Carbon::create($year, $month, rand(24, min(28, $daysInMonth)))->format('Y-m-d'),
                    ]);

                    // Listrik / Fasum RT
                    $nextId = (Pembayaran::max('id') ?? 0) + 1;
                    Pembayaran::create([
                        'nomor_transaksi' => sprintf('TRX-%s-%05d', $year, $nextId),
                        'warga_id' => null,
                        'rt_number' => $rt,
                        'tipe' => 'keluar',
                        'jumlah' => rand(25, 50) * 10000, // 250k - 500k
                        'kategori' => 'Listrik & Air Fasum',
                        'keterangan' => "Pembayaran token listrik pos ronda & lampu jalan RT {$rt}",
                        'sumber_tujuan' => "PLN / Poskamling RT {$rt}",
                        'metode_pembayaran' => 'Transfer Bank',
                        'status' => 'selesai',
                        'created_by' => $adminRwId,
                        'tanggal' => Carbon::create($year, $month, rand(5, 12))->format('Y-m-d'),
                    ]);
                }
            }
        }

        // 4. BUAT CONTOH NOTIFIKASI TAGIHAN AKTIF UNTUK SEMUA USER WARGA
        $wargaUsers = User::where('role', 'warga')->get();
        foreach ($wargaUsers as $wu) {
            $wRecord = Warga::where('nik', $wu->nik)->orWhere('no_kk', $wu->no_kk)->first();
            $wId = $wRecord ? $wRecord->id : null;

            // Cek apakah sudah ada notif tagihan november
            $hasNotif = \App\Models\FinanceNotification::where('user_id', $wu->id)
                ->where('periode', '11/2026')
                ->exists();

            if (!$hasNotif) {
                \App\Models\FinanceNotification::create([
                    'user_id' => $wu->id,
                    'warga_id' => $wId,
                    'tipe' => 'tagihan_baru',
                    'judul' => 'Tagihan Iuran Kebersihan & Keamanan November 2026',
                    'pesan' => 'Tagihan Iuran Lingkungan periode November 2026 telah diterbitkan sebesar Rp100.000. Jatuh tempo pada tanggal 10 November 2026. Silakan selesaikan pembayaran.',
                    'nominal' => 100000,
                    'periode' => '11/2026',
                    'due_date' => '2026-11-10',
                    'link_url' => route('laporan.iuran'),
                    'is_read' => false,
                ]);

                \App\Models\FinanceNotification::create([
                    'user_id' => $wu->id,
                    'warga_id' => $wId,
                    'tipe' => 'pembayaran_diterima',
                    'judul' => 'Pembayaran Iuran Oktober 2026 Terverifikasi',
                    'pesan' => 'Pembayaran iuran keluarga Anda untuk periode Oktober 2026 sebesar Rp100.000 telah diverifikasi dan disahkan oleh pengurus. Terima kasih.',
                    'nominal' => 100000,
                    'periode' => '10/2026',
                    'due_date' => '2026-10-10',
                    'link_url' => route('laporan.iuran'),
                    'is_read' => true,
                    'read_at' => Carbon::now()->subDays(10),
                ]);
            }
        }

        $this->command->info("Seeder Transaksi Kas & Iuran Warga 2026 Berhasil Dijalankan!");
    }
}
