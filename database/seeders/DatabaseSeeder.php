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

        // --- DATA USER (DIPERTAHANKAN) ---

        // Admin
        User::firstOrCreate([
            'email' => 'admin@rt.com'
        ], [
            'name' => 'Admin RT',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'rt_number' => '001',
            'phone' => '081234567890',
            'address' => 'Jl. Admin No. 1',
            'no_kk' => $generateIdentityNumber(),
            'nik' => $generateIdentityNumber(),
        ]);

        // Bendahara
        User::firstOrCreate([
            'email' => 'bendahara@rt.com'
        ], [
            'name' => 'Bendahara RT',
            'password' => Hash::make('password123'),
            'role' => 'bendahara',
            'rt_number' => '001',
            'phone' => '081234567891',
            'address' => 'Jl. Bendahara No. 2',
            'no_kk' => $generateIdentityNumber(),
            'nik' => $generateIdentityNumber(),
        ]);

        // Warga sample (User Login)
        for ($i = 1; $i <= 5; $i++) {
            User::firstOrCreate([
                'email' => "warga$i@rt.com"
            ], [
                'name' => "Warga $i",
                'password' => Hash::make('password123'),
                'role' => 'warga',
                'rt_number' => str_pad($i, 3, '0', STR_PAD_LEFT),
                'phone' => "08123456789$i",
                'address' => "Jl. Contoh No. $i",
                'no_kk' => $generateIdentityNumber(),
                'nik' => $generateIdentityNumber(),
            ]);
        }

        // --- DATA WARGA NexaNest ---
        $this->call(WargaAestheticSeeder::class);

        $kkIds = Warga::where('is_kk', true)->pluck('id')->toArray();

        // TRANSAKSI OTOMATIS UNTUK SEMUA WARGA (JAN - MEI 2026) DIPERTAHANKAN
        foreach ($kkIds as $wargaId) {
            for ($bulan = 1; $bulan <= 5; $bulan++) {
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

        Pembayaran::create([
            'warga_id' => null,
            'tipe' => 'keluar',
            'jumlah' => 200000,
            'kategori' => 'Kebersihan',
            'tanggal' => now()->subDays(5)->format('Y-m-d'),
            'keterangan' => 'Bayar tukang sampah'
        ]);

        Pengumuman::create([
            'title' => 'Selamat Datang di Aplikasi Iuran RT',
            'content' => 'Aplikasi ini digunakan untuk memudahkan pengelolaan iuran warga. Silahkan lakukan pembayaran tepat waktu.',
            'created_by' => 1,
            'is_pinned' => true,
            'published_at' => now()
        ]);

        $this->call(UmkmSeeder::class);

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
