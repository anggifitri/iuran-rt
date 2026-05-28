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
        User::create([
            'name' => 'Admin RT',
            'email' => 'admin@rt.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'rt_number' => '001',
            'phone' => '081234567890',
            'address' => 'Jl. Admin No. 1',
            'no_kk' => $generateIdentityNumber(),
            'nik' => $generateIdentityNumber(),
        ]);

        // Bendahara
        User::create([
            'name' => 'Bendahara RT',
            'email' => 'bendahara@rt.com',
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
            User::create([
                'name' => "Warga $i",
                'email' => "warga$i@rt.com",
                'password' => Hash::make('password123'),
                'role' => 'warga',
                'rt_number' => str_pad($i, 3, '0', STR_PAD_LEFT),
                'phone' => "08123456789$i",
                'address' => "Jl. Contoh No. $i",
                'no_kk' => $generateIdentityNumber(),
                'nik' => $generateIdentityNumber(),
            ]);
        }

        // --- DATA WARGA KAS RT (DIPERTAHANKAN) ---
        $dataWarga = [
            ['nama' => 'Abel', 'blok_rumah' => 'A1', 'nomor_hp' => '08111'],
            ['nama' => 'Araa', 'blok_rumah' => 'A2', 'nomor_hp' => '08112'],
            ['nama' => 'Adara', 'blok_rumah' => 'B1', 'nomor_hp' => '08113'],
            ['nama' => 'Sera', 'blok_rumah' => 'B2', 'nomor_hp' => '08114'],
            ['nama' => 'Fez', 'blok_rumah' => 'C1', 'nomor_hp' => '08115'],
            ['nama' => 'Faren', 'blok_rumah' => 'C2', 'nomor_hp' => '08116'],
            ['nama' => 'Albara', 'blok_rumah' => 'D1', 'nomor_hp' => '08117'],
            ['nama' => 'Aditama', 'blok_rumah' => 'D2', 'nomor_hp' => '08118'],
        ];

        foreach ($dataWarga as $w) {
            Warga::create(array_merge($w, [
                'no_kk' => $generateIdentityNumber(),
                'nik' => $generateIdentityNumber(),
                'gender' => $faker->randomElement(['L', 'P']),
            ]));
        }

        // Isi No.KK dan NIK untuk semua warga lama yang belum lengkap
        // Update any existing wargas that still miss identity fields using DB to avoid model/IDE issues
        $rows = DB::select("select id, no_kk, nik, gender from wargas where no_kk is null or nik is null or gender is null");
        foreach ($rows as $row) {
            DB::table('wargas')->where('id', $row->id)->update([
                'no_kk' => $row->no_kk ?: $generateIdentityNumber(),
                'nik' => $row->nik ?: $generateIdentityNumber(),
                'gender' => $row->gender ?: $faker->randomElement(['L', 'P']),
            ]);
        }

        $faker = Faker::create('id_ID');
        $semuaWargaIds = Warga::query()->pluck('id')->toArray(); // Ambil ID 8 warga awal

        // Tambah 92 warga baru biar total jadi 100
        for ($i = 1; $i <= 92; $i++) {
            $wargaBaru = Warga::create([
                'nama' => $faker->name,
                'blok_rumah' => 'Blok ' . $faker->randomElement(['A', 'B', 'C', 'D']) . ' No. ' . rand(10, 99),
                'nomor_hp' => '08' . rand(1000000000, 9999999999),
                'no_kk' => $generateIdentityNumber(),
                'nik' => $generateIdentityNumber(),
                'gender' => $faker->randomElement(['L', 'P']),
            ]);
            $semuaWargaIds[] = $wargaBaru->id; // Kumpulin ID nya
        }

        // TRANSAKSI OTOMATIS UNTUK 100 WARGA (JAN - MEI 2026)
        foreach ($semuaWargaIds as $wargaId) {
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
            'warga_id' => 1, // ID 1 ini otomatis mengikat ke warga pertama yaitu 'Abel'
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
    }
}
