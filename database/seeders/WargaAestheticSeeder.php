<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Warga;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WargaAestheticSeeder extends Seeder
{
    public function run()
    {
        // Matikan sementara foreign key check & kosongkan tabel
        DB::statement('PRAGMA foreign_keys = OFF;');
        Warga::truncate();
        DB::statement('PRAGMA foreign_keys = ON;');

        // Pool nama-nama aesthetic untuk pembuatan data warga
        $namaCowok = [
            'Keenan', 'Aksara', 'Bumi', 'Langit', 'Mahesa', 'Dirgantara', 'Devan', 'Kenzo', 
            'Arkan', 'Zein', 'Kalandra', 'Gibran', 'Reno', 'Bintang', 'Gala', 'Dipta', 
            'Bagas', 'Raka', 'Vino', 'Reza', 'Angga', 'Iqbaal', 'Dimas', 'Arya', 'Reynald', 
            'Elian', 'Bastian', 'Gavin', 'Narendra', 'Raditya', 'Fathir', 'Danendra', 
            'Aldebaran', 'Ravindra', 'Arjuna', 'Rendra', 'Farhan', 'Naufal', 'Hanif', 
            'Raffi', 'Devano', 'Adrian', 'Zaki', 'Haikal', 'Rian', 'Fatur', 'Aryo', 'Rizky'
        ];

        $namaCewek = [
            'Senja', 'Aurora', 'Kiera', 'Valerie', 'Aletta', 'Kanaya', 'Nadhira', 'Freya', 
            'Ziva', 'Lyodra', 'Tiara', 'Mahalini', 'Keisya', 'Raisa', 'Isyana', 'Anya', 
            'Chelsea', 'Tara', 'Maudy', 'Pevita', 'Zara', 'Sisca', 'Ariel', 'Cinta', 
            'Lestari', 'Clara', 'Nabila', 'Salma', 'Amara', 'Kiara', 'Sabrina', 'Talitha', 
            'Amanda', 'Zahra', 'Alya', 'Fiona', 'Nadia', 'Kania', 'Aisha', 'Syifa', 
            'Keyla', 'Adel', 'Clarissa', 'Gisella', 'Jessica', 'Nadine', 'Tasya', 'Mikha'
        ];

        $namaBelakang = [
            'Adhitama', 'Mahardika', 'Baskoro', 'Wijaya', 'Tanjung', 'Pangestu', 'Siregar', 
            'Wibowo', 'Nugroho', 'Lestari', 'Kirana', 'Salsabila', 'Anandita', 'Pradipta', 
            'Kusuma', 'Syahreza', 'Nicholas', 'Pradana', 'Kurniawan', 'Hidayat', 'Saputra', 
            'Setiawan', 'Pratama', 'Gunawan', 'Sudrajat', 'Kusumah', 'Subagja', 'Wahyudi', 
            'Nugraha', 'Hartono', 'Prakoso', 'Sucipto', 'Dewantara', 'Abimanyu'
        ];

        $blok = ['Blok A', 'Blok B', 'Blok C', 'Blok D', 'Blok E', 'Blok F'];
        $rtList = ['006', '007', '008', '009', '010'];

        for ($i = 0; $i < 100; $i++) {
            // 1. BUAT KEPALA KELUARGA
            $rt = $rtList[array_rand($rtList)];
            $blokRumah = $blok[array_rand($blok)] . ' No. ' . rand(1, 99);
            $kkLast = $namaBelakang[array_rand($namaBelakang)];
            
            // 90% Kepala Keluarga adalah Laki-laki, 10% Perempuan (misal single parent / janda)
            $isMaleKk = rand(1, 10) > 1;
            $kkFirstName = $isMaleKk ? $namaCowok[array_rand($namaCowok)] : $namaCewek[array_rand($namaCewek)];
            $kkName = $kkFirstName . ' ' . $kkLast;
            
            $kkTglLahir = Carbon::now()->subYears(rand(30, 60))->subMonths(rand(1, 12))->format('Y-m-d');
            $kkNik = '327501' . rand(1000000000, 9999999999);
            $noKk = '327501' . rand(1000000000, 9999999999);

            $kk = Warga::create([
                'nama' => $kkName,
                'blok_rumah' => $blokRumah,
                'nomor_hp' => '08' . rand(1111111111, 9999999999),
                'gender' => $isMaleKk ? 'L' : 'P',
                'no_kk' => $noKk,
                'nik' => $kkNik,
                // Pravatar API untuk foto profil estetik & unik
                'profile_photo' => 'https://i.pravatar.cc/300?u=' . $kkNik,
                'tanggal_lahir' => $kkTglLahir,
                'rt_number' => $rt,
                'rw_number' => '018',
                'is_kk' => true,
                'kk_id' => null,
                'alamat' => 'Jl. Merdeka Indah No. ' . rand(1, 120) . ', RT ' . $rt . ' RW 018, KOTA BANDUNG, JAWA BARAT'
            ]);

            // 2. BUAT 1 ANGGOTA KELUARGA (total 1 KK + 1 Anggota = 2 warga per KK. 100 KK * 2 = 200 warga total)
            if ($isMaleKk) {
                // Jika KK Laki-laki: 80% Istri, 20% Anak
                $isWife = rand(1, 10) > 2;
                if ($isWife) {
                    $membName = $namaCewek[array_rand($namaCewek)] . ' ' . $namaBelakang[array_rand($namaBelakang)];
                    $membTglLahir = Carbon::parse($kkTglLahir)->addYears(rand(-5, 5))->format('Y-m-d');
                    $membGender = 'P';
                } else {
                    // Anak
                    $isBoy = rand(0, 1) == 1;
                    $membName = ($isBoy ? $namaCowok[array_rand($namaCowok)] : $namaCewek[array_rand($namaCewek)]) . ' ' . $kkLast;
                    $membTglLahir = Carbon::now()->subYears(rand(2, 25))->format('Y-m-d');
                    $membGender = $isBoy ? 'L' : 'P';
                }
            } else {
                // Jika KK Perempuan: Harus Anak (Single parent)
                $isBoy = rand(0, 1) == 1;
                $membName = ($isBoy ? $namaCowok[array_rand($namaCowok)] : $namaCewek[array_rand($namaCewek)]) . ' ' . $kkLast;
                $membTglLahir = Carbon::now()->subYears(rand(2, 25))->format('Y-m-d');
                $membGender = $isBoy ? 'L' : 'P';
            }

            $membNik = '327501' . rand(1000000000, 9999999999);
            $age = Carbon::parse($membTglLahir)->age;
            $membPhone = $age >= 17 ? '08' . rand(1111111111, 9999999999) : null;

            Warga::create([
                'nama' => $membName,
                'blok_rumah' => $blokRumah,
                'nomor_hp' => $membPhone,
                'gender' => $membGender,
                'no_kk' => $noKk,
                'nik' => $membNik,
                'profile_photo' => 'https://i.pravatar.cc/300?u=' . $membNik,
                'tanggal_lahir' => $membTglLahir,
                'rt_number' => $rt,
                'rw_number' => '018',
                'is_kk' => false,
                'kk_id' => $kk->id,
                'alamat' => $kk->alamat
            ]);
        }
    }
}