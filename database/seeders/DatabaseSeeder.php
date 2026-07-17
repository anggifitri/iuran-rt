<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Warga;
use App\Models\Pembayaran;
use App\Models\Pengumuman;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $generateIdentityNumber = function () use ($faker) {
            return $faker->numerify('################');
        };

        // --- DATA WARGA NexaNest ---
        $this->call(WargaAestheticSeeder::class);

        // --- DATA USER (DIPERTAHANKAN) ---
        $wargaAdmins = Warga::where('is_kk', true)->inRandomOrder()->take(10)->get();
        $adminIdx = 0;

        // Admin
        $wargaAdmin = $wargaAdmins[$adminIdx++];
        User::updateOrCreate([
            'email' => 'admin@rt.com'
        ], [
            'name' => $wargaAdmin->nama,
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'rt_number' => '001',
            'phone' => $wargaAdmin->nomor_hp ?? '081234567890',
            'address' => $wargaAdmin->alamat,
            'no_kk' => $wargaAdmin->no_kk,
            'nik' => $wargaAdmin->nik,
            'profile_photo' => $wargaAdmin->profile_photo,
        ]);

        // Admin RW 018
        $wargaRw = $wargaAdmins[$adminIdx++];
        User::updateOrCreate([
            'email' => 'adminrw@rt.com'
        ], [
            'name' => $wargaRw->nama,
            'password' => Hash::make('mahenn09'),
            'role' => 'admin',
            'rt_number' => '000',
            'phone' => $wargaRw->nomor_hp ?? '081299990000',
            'address' => $wargaRw->alamat,
            'no_kk' => $wargaRw->no_kk,
            'nik' => $wargaRw->nik,
            'profile_photo' => $wargaRw->profile_photo,
        ]);

        // Admin RT 006 s/d 010
        for ($rt = 6; $rt <= 10; $rt++) {
            $rtStr = str_pad($rt, 3, '0', STR_PAD_LEFT);
            $wargaRt = Warga::where('is_kk', true)->where('rt_number', $rtStr)->whereNotIn('id', $wargaAdmins->pluck('id'))->first();
            
            if ($wargaRt) {
                User::updateOrCreate([
                    'email' => "adminrt{$rt}@rt.com"
                ], [
                    'name' => $wargaRt->nama,
                    'password' => Hash::make('password123'),
                    'role' => 'admin',
                    'rt_number' => $rtStr,
                    'phone' => $wargaRt->nomor_hp ?? "08129999000{$rt}",
                    'address' => $wargaRt->alamat,
                    'no_kk' => $wargaRt->no_kk,
                    'nik' => $wargaRt->nik,
                    'profile_photo' => $wargaRt->profile_photo,
                ]);
            }
        }

        // Bendahara
        $wargaBendahara = $wargaAdmins[$adminIdx++];
        User::updateOrCreate([
            'email' => 'bendahara@rt.com'
        ], [
            'name' => $wargaBendahara->nama,
            'password' => Hash::make('password123'),
            'role' => 'bendahara',
            'rt_number' => '001',
            'phone' => $wargaBendahara->nomor_hp ?? '081234567891',
            'address' => $wargaBendahara->alamat,
            'no_kk' => $wargaBendahara->no_kk,
            'nik' => $wargaBendahara->nik,
            'profile_photo' => $wargaBendahara->profile_photo,
        ]);

        // Warga sample (User Login) linked to real seeded Wargas
        $wargaList = Warga::where('is_kk', true)->orderBy('nama', 'asc')->limit(30)->get();
        foreach ($wargaList as $index => $w) {
            $i = $index + 1;
            User::firstOrCreate([
                'email' => "warga$i@rt.com"
            ], [
                'name' => $w->nama,
                'password' => Hash::make('password123'),
                'role' => 'warga',
                'rt_number' => $w->rt_number,
                'phone' => $w->nomor_hp ?? "08123456789$i",
                'address' => $w->alamat,
                'no_kk' => $w->no_kk,
                'nik' => $w->nik,
                'profile_photo' => $w->profile_photo,
            ]);
        }

        $kkIds = Warga::where('is_kk', true)->pluck('id')->toArray();

        // TRANSAKSI OTOMATIS UNTUK SEMUA WARGA (JAN - MEI 2026) DIPERTAHANKAN DENGAN DISTRIBUSI REALISTIS
        foreach ($kkIds as $index => $wargaId) {
            // Bagi 100 KK menjadi:
            // - Index 0 - 74 (75 KK): Rajin (bayar bulan 1 s/d 5)
            // - Index 75 - 89 (15 KK): Kurang Bayar (hanya bayar bulan 1 s/d 3)
            // - Index 90 - 99 (10 KK): Tidak Pernah Bayar (tidak di-seed pembayaran)
            if ($index < 75) {
                $maxBulan = 5;
            } elseif ($index < 90) {
                $maxBulan = 3;
            } else {
                continue; // 10 KK tidak pernah bayar sama sekali
            }

            for ($bulan = 1; $bulan <= $maxBulan; $bulan++) {
                // 1. Pemasukan Wajib Bulanan (100k)
                Pembayaran::create([
                    'warga_id' => $wargaId,
                    'tipe' => 'masuk',
                    'jumlah' => 100000,
                    'kategori' => 'Iuran Bulanan',
                    'tanggal' => Carbon::create(2026, $bulan, rand(1, 28))->format('Y-m-d'),
                    'keterangan' => 'Iuran bulanan otomatis warga'
                ]);

                // 2. Pemasukan Sukarela / Donasi (Peluang 30% warga nyumbang tiap bulan)
                if (rand(1, 10) > 7) {
                    $kategoriAcak = rand(0, 1) == 0 ? 'Iuran Sukarela' : 'Donasi Warga';
                    $nominalAcak = [50000, 100000, 200000, 500000][rand(0, 3)];

                    Pembayaran::create([
                        'warga_id' => $wargaId,
                        'tipe' => 'masuk',
                        'jumlah' => $nominalAcak,
                        'kategori' => $kategoriAcak,
                        'tanggal' => Carbon::create(2026, $bulan, rand(1, 28))->format('Y-m-d'),
                        'keterangan' => $kategoriAcak . ' swadaya'
                    ]);
                }
            }
        }

        Pembayaran::create([
            'warga_id' => $kkIds[0], // ID dynamic from first KK
            'tipe' => 'masuk',
            'jumlah' => 500000,
            'kategori' => 'Iuran Bulanan',
            'tanggal' => now()->subMonths(1)->format('Y-m-d'),
            'keterangan' => 'Iuran warga blok A'
        ]);

        // --- DATA PENGELUARAN RUTIN BULANAN & EVENT (JANUARI - JULI 2026) ---
        for ($bulan = 1; $bulan <= 7; $bulan++) {
            // 1. Kebersihan Bulanan (Petugas Sampah)
            Pembayaran::create([
                'warga_id' => null,
                'tipe' => 'keluar',
                'jumlah' => 1500000,
                'kategori' => 'Kebersihan',
                'tanggal' => Carbon::create(2026, $bulan, 25)->format('Y-m-d'),
                'keterangan' => 'Honor petugas kebersihan & angkutan sampah bulanan'
            ]);

            // 2. Keamanan Bulanan (Security)
            Pembayaran::create([
                'warga_id' => null,
                'tipe' => 'keluar',
                'jumlah' => 2000000,
                'kategori' => 'Keamanan',
                'tanggal' => Carbon::create(2026, $bulan, 28)->format('Y-m-d'),
                'keterangan' => 'Honor petugas keamanan (security) & siskamling'
            ]);

            // 3. Listrik & Air PJU
            Pembayaran::create([
                'warga_id' => null,
                'tipe' => 'keluar',
                'jumlah' => 350000,
                'kategori' => 'Listrik & Air',
                'tanggal' => Carbon::create(2026, $bulan, 10)->format('Y-m-d'),
                'keterangan' => 'Pembayaran PJU (Penerangan Jalan Umum) & air bersih pos ronda'
            ]);
        }

        // SEED EXPENSES EVENTS (PENGELUARAN KEGIATAN KHUSUS)
        // Januari 2026: Fogging Demam Berdarah
        Pembayaran::create([
            'warga_id' => null,
            'tipe' => 'keluar',
            'jumlah' => 850000,
            'kategori' => 'Perbaikan/Maintenance',
            'tanggal' => '2026-01-15',
            'keterangan' => 'Kegiatan fogging DBD massal & pembelian obat abate'
        ]);

        // Maret 2026: Buka Bersama & Santunan Ramadhan
        Pembayaran::create([
            'warga_id' => null,
            'tipe' => 'keluar',
            'jumlah' => 2500000,
            'kategori' => 'Lain-lain',
            'tanggal' => '2026-03-20',
            'keterangan' => 'Kegiatan buka bersama warga & santunan anak yatim'
        ]);

        // Mei 2026: Halal Bihalal Syawal
        Pembayaran::create([
            'warga_id' => null,
            'tipe' => 'keluar',
            'jumlah' => 3000000,
            'kategori' => 'Lain-lain',
            'tanggal' => '2026-05-10',
            'keterangan' => 'Penyelenggaraan Halal Bihalal silaturahmi Idul Fitri warga RW 018'
        ]);

        // Juni 2026: Perbaikan Saluran Air Jebol
        Pembayaran::create([
            'warga_id' => null,
            'tipe' => 'keluar',
            'jumlah' => 1200000,
            'kategori' => 'Perbaikan/Maintenance',
            'tanggal' => '2026-06-18',
            'keterangan' => 'Renovasi beton gorong-gorong selokan jalan yang jebol di RT 008'
        ]);

        // Juli 2026: Persiapan HUT RI
        Pembayaran::create([
            'warga_id' => null,
            'tipe' => 'keluar',
            'jumlah' => 950000,
            'kategori' => 'Lain-lain',
            'tanggal' => '2026-07-05',
            'keterangan' => 'Pembelian umbul-umbul merah putih, bendera, dan persiapan gapura HUT RI'
        ]);

        Pengumuman::create([
            'title' => 'Selamat Datang di Aplikasi Iuran RT',
            'content' => 'Aplikasi ini digunakan untuk memudahkan pengelolaan iuran warga. Silahkan lakukan pembayaran tepat waktu.',
            'created_by' => 1,
            'is_pinned' => true,
            'published_at' => now()
        ]);

        $this->call(UmkmSeeder::class);
        $this->call(SuratPengajuanSeeder::class);

        // Seed 5 Modern Barcode Complaints
        $wargaUser = User::where('role', 'warga')->first();
        $userId = $wargaUser ? $wargaUser->id : 1;

        \App\Models\Pengaduan::create([
            'user_id' => $userId,
            'title' => 'Korsleting & Lampu Jalan Padam di Tiang TL-018-006-012',
            'content' => 'Lampu penerangan jalan umum pada tiang listrik nomor TL-018-006-012 mati total sejak semalam. Sempat terdengar suara letupan kecil di bagian atas kotak sekring tiang.',
            'status' => 'pending',
            'barcode_code' => 'TL-018-006-012',
            'category' => 'Tiang Listrik (Kelistrikan)',
            'rt_number' => '006',
            'location_details' => 'Tiang Listrik TL-018-006-012, Jl. Sakura Depan Blok A No. 12, RT 006',
            'latitude' => -6.914744,
            'longitude' => 107.609810,
        ]);

        \App\Models\Pengaduan::create([
            'user_id' => $userId,
            'title' => 'Kebocoran Air Bersih di Pilar Hydrant HYD-018-007-003',
            'content' => 'Ada rembesan air bersih yang cukup deras keluar dari katup bawah pilar hydrant nomor HYD-018-007-003. Air menggenangi jalan masuk gang Cemara.',
            'status' => 'proses',
            'barcode_code' => 'HYD-018-007-003',
            'category' => 'Hydrant Damkar (Utilitas Air)',
            'rt_number' => '007',
            'location_details' => 'Hydrant Pemadam HYD-018-007-003, Samping Pos Ronda Cemara, RT 007',
            'latitude' => -6.915230,
            'longitude' => 107.610245,
        ]);

        \App\Models\Pengaduan::create([
            'user_id' => $userId,
            'title' => 'Kamera CCTV Keamanan Mati di Tiang CCTV-018-008-005',
            'content' => 'Tampilan feed CCTV keamanan lingkungan pada tiang nomor CCTV-018-008-005 terputus dan tidak mengirimkan gambar sejak tadi siang. Mohon dicek koneksi kabel LAN atau powernya.',
            'status' => 'selesai',
            'barcode_code' => 'CCTV-018-008-005',
            'category' => 'Tiang WiFi/CCTV (Keamanan)',
            'rt_number' => '008',
            'location_details' => 'Tiang CCTV CCTV-018-008-005, Pertigaan Balai Pertemuan, RT 008',
            'latitude' => -6.914390,
            'longitude' => 107.611102,
        ]);

        \App\Models\Pengaduan::create([
            'user_id' => $userId,
            'title' => 'Tutup Bak Sampah Komunal BSK-018-009-001 Pecah',
            'content' => 'Engsel pintu penutup bak sampah komunal fiber nomor BSK-018-009-001 patah akibat benturan. Sampah menjadi berserakan karena tidak bisa tertutup rapat.',
            'status' => 'pending',
            'barcode_code' => 'BSK-018-009-001',
            'category' => 'Bak Sampah (Sanitasi)',
            'rt_number' => '009',
            'location_details' => 'Bak Sampah Fiber BSK-018-009-001, Depan Taman Bermain Anak, RT 009',
            'latitude' => -6.913880,
            'longitude' => 107.610540,
        ]);

        \App\Models\Pengaduan::create([
            'user_id' => $userId,
            'title' => 'Portal Gerbang Otomatis PRT-018-010-002 Macet',
            'content' => 'Motor penggerak portal otomatis pada gerbang masuk PRT-018-010-002 tidak merespon saat kartu RFID warga ditempelkan. Terpaksa gerbang dibuka secara manual.',
            'status' => 'proses',
            'barcode_code' => 'PRT-018-010-002',
            'category' => 'Portal Gate (Akses RW)',
            'rt_number' => '010',
            'location_details' => 'Pintu Gerbang Utama PRT-018-010-002, Jl. Kenanga Masuk RT 010',
            'latitude' => -6.916120,
            'longitude' => 107.612003,
        ]);
    }
}
