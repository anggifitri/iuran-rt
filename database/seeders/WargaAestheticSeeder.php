<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Warga;
use Carbon\Carbon;

class WargaAestheticSeeder extends Seeder
{
    public function run()
    {
        $namaCowok = ['Keenan', 'Aksara', 'Bumi', 'Langit', 'Mahesa', 'Dirgantara', 'Devan', 'Kenzo', 'Arkan', 'Zein', 'Kalandra', 'Gibran', 'Reno', 'Bintang', 'Gala', 'Dipta', 'Bagas', 'Raka', 'Jefri', 'Vino', 'Reza', 'Angga', 'Iqbaal', 'Dimas', 'Arya'];
        $namaCewek = ['Senja', 'Aurora', 'Kiera', 'Valerie', 'Aletta', 'Kanaya', 'Nadhira', 'Freya', 'Ziva', 'Lyodra', 'Tiara', 'Mahalini', 'Keisya', 'Raisa', 'Isyana', 'Anya', 'Chelsea', 'Tara', 'Maudy', 'Pevita', 'Zara', 'Sisca', 'Ariel', 'Cinta', 'Lestari'];
        $namaBelakang = ['Adhitama', 'Mahardika', 'Baskoro', 'Wijaya', 'Tanjung', 'Pangestu', 'Siregar', 'Wibowo', 'Nugroho', 'Lestari', 'Kirana', 'Salsabila', 'Anandita', 'Pradipta', 'Kusuma', 'Syahreza', 'Nicholas'];

        $blok = ['Blok A', 'Blok B', 'Blok C', 'Blok D', 'Blok E', 'Blok F'];
        $rtList = ['006', '007', '008', '009', '010'];

        $wargaCount = 0;
        $targetWarga = 200;

        while ($wargaCount < $targetWarga) {
            // 1. BUAT KEPALA KELUARGA (Suami Ganteng)
            $rt = $rtList[array_rand($rtList)];
            $blokRumah = $blok[array_rand($blok)] . ' No. ' . rand(1, 99);
            
            $kkLast = $namaBelakang[array_rand($namaBelakang)];
            $kkName = $namaCowok[array_rand($namaCowok)] . ' ' . $kkLast;
            $kkTglLahir = Carbon::now()->subYears(rand(25, 45))->subMonths(rand(1, 12))->format('Y-m-d');
            $kkNik = '327501' . rand(1000000000, 9999999999);
            $noKk = '327501' . rand(1000000000, 9999999999);

            $kk = Warga::create([
                'nama' => $kkName,
                'blok_rumah' => $blokRumah,
                'nomor_hp' => '08' . rand(1111111111, 9999999999),
                'gender' => 'L',
                'no_kk' => $noKk,
                'nik' => $kkNik,
                // Pravatar API: Bakal ngasih muka unik berdasarkan NIK
                'profile_photo' => 'https://i.pravatar.cc/300?u=' . $kkNik,
                'tanggal_lahir' => $kkTglLahir,
                'rt_number' => $rt,
                'rw_number' => '018',
                'is_kk' => true,
                'kk_id' => null,
                'alamat' => 'KOTA BANDUNG, Provinsi JAWA BARAT'
            ]);
            $wargaCount++;
            if ($wargaCount >= $targetWarga) break;

            // 2. BUAT ISTRI (Cewek Cantik)
            $istriName = $namaCewek[array_rand($namaCewek)] . ' ' . $namaBelakang[array_rand($namaBelakang)];
            $istriTglLahir = Carbon::parse($kkTglLahir)->addYears(rand(-4, 3))->format('Y-m-d');
            $istriNik = '327501' . rand(1000000000, 9999999999);

            Warga::create([
                'nama' => $istriName,
                'blok_rumah' => $blokRumah,
                'nomor_hp' => '08' . rand(1111111111, 9999999999),
                'gender' => 'P',
                'no_kk' => $noKk,
                'nik' => $istriNik,
                'profile_photo' => 'https://i.pravatar.cc/300?u=' . $istriNik,
                'tanggal_lahir' => $istriTglLahir,
                'rt_number' => $rt,
                'rw_number' => '018',
                'is_kk' => false,
                'kk_id' => $kk->id,
                'alamat' => 'KOTA BANDUNG, Provinsi JAWA BARAT'
            ]);
            $wargaCount++;
            if ($wargaCount >= $targetWarga) break;

            // 3. BUAT ANAK (1 sampai 3 Anak per keluarga, campuran balita & remaja)
            $jmlAnak = rand(1, 3);
            for ($i = 0; $i < $jmlAnak; $i++) {
                if ($wargaCount >= $targetWarga) break;

                $isCowok = rand(0, 1) == 1;
                $anakName = ($isCowok ? $namaCowok[array_rand($namaCowok)] : $namaCewek[array_rand($namaCewek)]) . ' ' . $kkLast;
                
                // Setengah anak jadi balita (buat Posyandu), setengahnya remaja
                if (rand(0, 1) == 1) {
                    $anakTglLahir = Carbon::now()->subMonths(rand(1, 59))->format('Y-m-d'); // Balita (0-4 tahun)
                } else {
                    $anakTglLahir = Carbon::now()->subYears(rand(5, 17))->format('Y-m-d'); // Remaja
                }

                $anakNik = '327501' . rand(1000000000, 9999999999);

                Warga::create([
                    'nama' => $anakName,
                    'blok_rumah' => $blokRumah,
                    'nomor_hp' => null,
                    'gender' => $isCowok ? 'L' : 'P',
                    'no_kk' => $noKk,
                    'nik' => $anakNik,
                    'profile_photo' => 'https://i.pravatar.cc/300?u=' . $anakNik,
                    'tanggal_lahir' => $anakTglLahir,
                    'rt_number' => $rt,
                    'rw_number' => '018',
                    'is_kk' => false,
                    'kk_id' => $kk->id,
                    'alamat' => 'KOTA BANDUNG, Provinsi JAWA BARAT'
                ]);
                $wargaCount++;
            }
        }
    }
}