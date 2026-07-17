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
    }
}
