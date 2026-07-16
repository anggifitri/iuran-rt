<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SultanSeeder extends Seeder
{
    public function run()
    {
        DB::statement('PRAGMA foreign_keys = OFF;');
        DB::table('wargas')->truncate();
        DB::table('pembayarans')->truncate();
        DB::statement('PRAGMA foreign_keys = ON;');

        $namaCowok = ['Keenan', 'Aksara', 'Bumi', 'Langit', 'Mahesa', 'Dirgantara', 'Devan', 'Kenzo', 'Arkan', 'Zein', 'Kalandra', 'Gibran', 'Reno', 'Bintang', 'Gala', 'Dipta'];
        $namaCewek = ['Senja', 'Aurora', 'Kiera', 'Valerie', 'Aletta', 'Kanaya', 'Nadhira', 'Freya', 'Ziva', 'Lyodra', 'Tiara', 'Mahalini', 'Keisya', 'Raisa', 'Isyana', 'Anya'];
        $namaBelakang = ['Adhitama', 'Mahardika', 'Baskoro', 'Wijaya', 'Tanjung', 'Pangestu', 'Siregar', 'Wibowo', 'Nugroho', 'Lestari', 'Kirana', 'Salsabila'];
        $blok = ['Blok A', 'Blok B', 'Blok C', 'Blok D', 'Blok E'];
        $rtList = ['006', '007', '008', '009', '010'];

        $wargaIds = [];
        $wargaCount = 0;
        $targetWarga = 200;

      while ($wargaCount < $targetWarga) {
            $rt = $rtList[array_rand($rtList)];
            $blokRumah = $blok[array_rand($blok)] . ' No. ' . rand(1, 99);
            $kkLast = $namaBelakang[array_rand($namaBelakang)];

            $kkNik = '327501' . rand(1000000000, 9999999999);
            $kkId = DB::table('wargas')->insertGetId([
                'nama' => $namaCowok[array_rand($namaCowok)] . ' ' . $kkLast,
                'blok_rumah' => $blokRumah,
                'nomor_hp' => '08' . rand(1111111111, 9999999999),
                'gender' => 'L',
                'no_kk' => '327501' . rand(1000000000, 9999999999),
                'nik' => $kkNik,
                'profile_photo' => 'https://i.pravatar.cc/300?u=' . $kkNik,
                'tanggal_lahir' => Carbon::now()->subYears(rand(25, 45))->format('Y-m-d'),
                'rt_number' => $rt,
                'rw_number' => '018',
                'is_kk' => 1,
                'kk_id' => null,
                'alamat' => 'KOTA BANDUNG, Provinsi JAWA BARAT',
                'created_at' => now(), 'updated_at' => now(),
            ]);
            $wargaIds[] = $kkId;
            $wargaCount++;
            if ($wargaCount >= $targetWarga) break;

            // ISTRI
            $istriNik = '327501' . rand(1000000000, 9999999999);
            DB::table('wargas')->insert([
                'nama' => $namaCewek[array_rand($namaCewek)] . ' ' . $kkLast,
                'blok_rumah' => $blokRumah,
                'gender' => 'P',
                'no_kk' => '327501' . rand(1000000000, 9999999999),
                'nik' => $istriNik,
                'profile_photo' => 'https://i.pravatar.cc/300?u=' . $istriNik,
                'tanggal_lahir' => Carbon::now()->subYears(rand(22, 40))->format('Y-m-d'),
                'rt_number' => $rt,
                'rw_number' => '018',
                'is_kk' => 0,
                'kk_id' => $kkId,
                'alamat' => 'KOTA BANDUNG, Provinsi JAWA BARAT',
                'created_at' => now(), 'updated_at' => now(),
            ]);
            $wargaCount++;
            if ($wargaCount >= $targetWarga) break;

            for ($i = 0; $i < rand(1, 2); $i++) {
                if ($wargaCount >= $targetWarga) break;
                $isCowok = rand(0, 1);
                $anakNik = '327501' . rand(1000000000, 9999999999);
                DB::table('wargas')->insert([
                    'nama' => ($isCowok ? $namaCowok[array_rand($namaCowok)] : $namaCewek[array_rand($namaCewek)]) . ' ' . $kkLast,
                    'blok_rumah' => $blokRumah,
                    'gender' => $isCowok ? 'L' : 'P',
                    'nik' => $anakNik,
                    'profile_photo' => 'https://i.pravatar.cc/300?u=' . $anakNik,
                    'tanggal_lahir' => Carbon::now()->subYears(rand(1, 15))->format('Y-m-d'),
                    'rt_number' => $rt,
                    'rw_number' => '018',
                    'is_kk' => 0,
                    'kk_id' => $kkId,
                    'alamat' => 'KOTA BANDUNG, Provinsi JAWA BARAT',
                    'created_at' => now(), 'updated_at' => now(),
                ]);
                $wargaCount++;
            }
        }

        $bulanList = [2, 3, 4, 5, 6, 7];
        $tahun = 2026;

        foreach ($bulanList as $bulan) {
            $jumlahPenyetor = rand(50, 60);
            for ($i = 0; $i < $jumlahPenyetor; $i++) {
                DB::table('pembayarans')->insert([
                    'warga_id'   => $wargaIds[array_rand($wargaIds)],
                    'jenis'      => 'Iuran Kas & Keamanan',
                    'tipe'       => 'pemasukan',
                    'jumlah'     => rand(3, 8) * 100000, // Rp 300.000 - Rp 800.000 per warga (Sultan!)
                    'keterangan' => 'Pembayaran lunas bulan ' . $bulan,
                    'tanggal'    => Carbon::create($tahun, $bulan, rand(1, 15))->format('Y-m-d'),
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }

            $pengeluarans = [
                ['Gaji Keamanan & Sampah RW 018', rand(4, 6) * 1000000], // 4-6 Juta
                ['Perawatan Taman & Gapura Estetik', rand(1, 3) * 1000000], // 1-3 Juta
                ['Konsumsi Rapat Warga & ATK', rand(5, 15) * 100000], // 500rb - 1.5 Juta
            ];

            foreach ($pengeluarans as $peng) {
                DB::table('pembayarans')->insert([
                    'warga_id'   => $wargaIds[0], // Diwakilkan KK pertama (Pak RT)
                    'jenis'      => 'Pengeluaran Operasional',
                    'tipe'       => 'pengeluaran',
                    'jumlah'     => $peng[1],
                    'keterangan' => $peng[0],
                    'tanggal'    => Carbon::create($tahun, $bulan, rand(16, 28))->format('Y-m-d'),
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        }
    }
}
