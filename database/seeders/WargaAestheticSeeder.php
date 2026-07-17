<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Warga;
use App\Models\PosyanduAnak;
use App\Models\PosyanduBumil;
use App\Models\Posyandu;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WargaAestheticSeeder extends Seeder
{
    public function run()
    {
        // Matikan sementara foreign key check & kosongkan tabel
        DB::statement('PRAGMA foreign_keys = OFF;');
        Warga::truncate();
        PosyanduAnak::truncate();
        PosyanduBumil::truncate();
        Posyandu::truncate();
        DB::statement('PRAGMA foreign_keys = ON;');

        // Counter indeks foto portrait (randomuser.me/api/portraits)
        $malePhotoIndex = 0;
        $femalePhotoIndex = 0;
        $childBoyPhotoIndex = 50;
        $childGirlPhotoIndex = 50;

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

        // Distribusi 100 KK dan 200 total warga:
        // - 40 KK Single (1 warga) = 40 warga
        // - 30 KK Pasangan (2 warga: Suami + Istri) = 60 warga
        // - 20 KK 1 Anak (3 warga: Suami + Istri + 1 Anak) = 60 warga
        // - 10 KK 2 Anak (4 warga: Suami + Istri + 2 Anak) = 40 warga
        // Total KK = 40 + 30 + 20 + 10 = 100 KK
        // Total Warga = 40 + 60 + 60 + 40 = 200 Warga

        $families = [];
        for ($i = 0; $i < 40; $i++) $families[] = 'single';
        for ($i = 0; $i < 30; $i++) $families[] = 'couple';
        for ($i = 0; $i < 20; $i++) $families[] = 'one_child';
        for ($i = 0; $i < 10; $i++) $families[] = 'two_children';

        shuffle($families);

        $balitaList = [];
        $bumilCandidates = [];

        foreach ($families as $type) {
            $rt = $rtList[array_rand($rtList)];
            $blokRumah = $blok[array_rand($blok)] . ' No. ' . rand(1, 99);
            $kkLast = $namaBelakang[array_rand($namaBelakang)];
            
            // 1. Generate Kepala Keluarga (90% Laki-laki, 10% Perempuan)
            $isMaleKk = rand(1, 10) > 1;
            $kkFirstName = $isMaleKk ? $namaCowok[array_rand($namaCowok)] : $namaCewek[array_rand($namaCewek)];
            $kkName = $kkFirstName . ' ' . $kkLast;
            $kkTglLahir = Carbon::now()->subYears(rand(28, 55))->subMonths(rand(1, 12))->format('Y-m-d');
            $kkNik = '327501' . rand(1000000000, 9999999999);
            $noKk = '327501' . rand(1000000000, 9999999999);

            $kk = Warga::create([
                'nama' => $kkName,
                'blok_rumah' => $blokRumah,
                'nomor_hp' => '08' . rand(1111111111, 9999999999),
                'gender' => $isMaleKk ? 'L' : 'P',
                'no_kk' => $noKk,
                'nik' => $kkNik,
                'profile_photo' => $isMaleKk
                    ? 'https://randomuser.me/api/portraits/men/' . (($malePhotoIndex++) % 100) . '.jpg'
                    : 'https://randomuser.me/api/portraits/women/' . (($femalePhotoIndex++) % 100) . '.jpg',
                'tanggal_lahir' => $kkTglLahir,
                'rt_number' => $rt,
                'rw_number' => '018',
                'is_kk' => true,
                'kk_id' => null,
                'alamat' => 'Jl. Merdeka Indah No. ' . rand(1, 120) . ', RT ' . $rt . ' RW 018, KOTA BANDUNG, JAWA BARAT'
            ]);

            // 2. Generate Pasangan (Istri)
            if ($type !== 'single') {
                $wifeName = $namaCewek[array_rand($namaCewek)] . ' ' . $namaBelakang[array_rand($namaBelakang)];
                $wifeTglLahir = Carbon::parse($kkTglLahir)->addYears(rand(-5, 4))->format('Y-m-d');
                $wifeNik = '327501' . rand(1000000000, 9999999999);
                
                $wife = Warga::create([
                    'nama' => $wifeName,
                    'blok_rumah' => $blokRumah,
                    'nomor_hp' => '08' . rand(1111111111, 9999999999),
                    'gender' => 'P',
                    'no_kk' => $noKk,
                    'nik' => $wifeNik,
                    'profile_photo' => 'https://randomuser.me/api/portraits/women/' . (($femalePhotoIndex++) % 100) . '.jpg',
                    'tanggal_lahir' => $wifeTglLahir,
                    'rt_number' => $rt,
                    'rw_number' => '018',
                    'is_kk' => false,
                    'kk_id' => $kk->id,
                    'alamat' => $kk->alamat
                ]);

                // Calon Ibu Hamil (Umur produktif 19-40 tahun)
                $age = Carbon::parse($wifeTglLahir)->age;
                if ($age >= 19 && $age <= 40) {
                    $bumilCandidates[] = $wife;
                }
            }

            // 3. Generate Anak (1 atau 2 anak)
            $numChildren = 0;
            if ($type === 'one_child') $numChildren = 1;
            if ($type === 'two_children') $numChildren = 2;

            for ($c = 0; $c < $numChildren; $c++) {
                $isBoy = rand(0, 1) == 1;
                $childFirstName = $isBoy ? $namaCowok[array_rand($namaCowok)] : $namaCewek[array_rand($namaCewek)];
                $childName = $childFirstName . ' ' . $kkLast;
                
                // 80% anak diposisikan berumur balita (0-4 tahun) agar terdaftar di sasaran Posyandu
                $isBalita = rand(1, 10) <= 8;
                if ($isBalita) {
                    $childTglLahir = Carbon::now()->subMonths(rand(1, 59))->format('Y-m-d');
                } else {
                    $childTglLahir = Carbon::now()->subYears(rand(6, 17))->format('Y-m-d');
                }

                $childNik = '327501' . rand(1000000000, 9999999999);
                
                $child = Warga::create([
                    'nama' => $childName,
                    'blok_rumah' => $blokRumah,
                    'nomor_hp' => null,
                    'gender' => $isBoy ? 'L' : 'P',
                    'no_kk' => $noKk,
                    'nik' => $childNik,
                    'profile_photo' => $isBoy
                        ? 'https://randomuser.me/api/portraits/men/' . (min(($childBoyPhotoIndex++), 99)) . '.jpg'
                        : 'https://randomuser.me/api/portraits/women/' . (min(($childGirlPhotoIndex++), 99)) . '.jpg',
                    'tanggal_lahir' => $childTglLahir,
                    'rt_number' => $rt,
                    'rw_number' => '018',
                    'is_kk' => false,
                    'kk_id' => $kk->id,
                    'alamat' => $kk->alamat
                ]);

                if ($isBalita) {
                    $balitaList[] = $child;
                }
            }
        }

        // --- SEED POSYANDU REKAM ANAK (Growth Records untuk 15 Balita) ---
        shuffle($balitaList);
        $selectedBalitas = array_slice($balitaList, 0, 15);

        foreach ($selectedBalitas as $idx => $anak) {
            $umurBulan = Carbon::parse($anak->tanggal_lahir)->diffInMonths(Carbon::now());
            if ($umurBulan < 1) $umurBulan = 1;

            // Berat dan tinggi ideal yang disesuaikan umur + variasi
            $berat = round(($umurBulan * 0.35) + 3.2 + (rand(-12, 12)/10), 2);
            $tinggi = round(($umurBulan * 1.5) + 49 + rand(-4, 4), 2);

            // Berikan status tumbuh kembang beserta solusi jika bermasalah
            if ($idx === 1 || $idx === 5) {
                $status = 'Kurang Gizi';
                $solusi = 'Berat badan anak berada di bawah batas normal KMS (Pita Kuning/Merah). Berikan Pemberian Makanan Tambahan (PMT) kaya protein hewani (telur, ikan, daging), tingkatkan frekuensi makan menjadi 3x makanan utama dan 2x selingan padat gizi, serta segera konsultasi ke Puskesmas untuk mendapatkan vitamin gizi tambahan.';
            } elseif ($idx === 3 || $idx === 9) {
                $status = 'Stunting';
                $solusi = 'Tinggi badan anak tidak sesuai dengan usianya (pendek/stunted). Diperlukan intervensi gizi spesifik: berikan asupan protein hewani setiap hari, konsultasikan dengan dokter anak di Puskesmas untuk pemberian suplemen Zinc/Zat Besi, perbaiki sanitasi lingkungan rumah, dan pastikan pola asuh makan anak dijadwal teratur.';
            } elseif ($idx === 7) {
                $status = 'Kelebihan Berat Badan';
                $solusi = 'Berat badan anak melebihi kurva pertumbuhan atas. Kurangi porsi makanan instan, batasi susu formula/minuman manis dengan gula tambahan, dorong anak untuk bermain fisik aktif (merangkak, berjalan, berlari), dan konsultasikan pola menu diet sehat dengan ahli gizi Puskesmas.';
            } else {
                $status = 'Normal';
                $solusi = 'Tumbuh kembang anak berjalan sangat baik dan berada di pita hijau KMS. Lanjutkan pemberian ASI/Susu, makanan dengan gizi seimbang (karbohidrat, protein, lemak, vitamin), dan rutin timbang berat/tinggi badan di Posyandu Melati setiap bulan untuk memantau grafik perkembangannya.';
            }

            // Checklist imunisasi yang sudah diambil berdasarkan umur anak
            $checked = [];
            if ($umurBulan >= 1) $checked[] = 'BCG';
            if ($umurBulan >= 2) $checked[] = 'Polio 1';
            if ($umurBulan >= 3) {
                $checked[] = 'Polio 2';
                $checked[] = 'DPT-HB-Hib 1';
            }
            if ($umurBulan >= 4) {
                $checked[] = 'Polio 3';
                $checked[] = 'DPT-HB-Hib 2';
            }
            if ($umurBulan >= 6) $checked[] = 'DPT-HB-Hib 3';
            if ($umurBulan >= 9) $checked[] = 'Campak-Rubela';

            PosyanduAnak::create([
                'nama_anak' => $anak->nama,
                'umur_bulan' => $umurBulan,
                'berat_badan' => $berat,
                'tinggi_badan' => $tinggi,
                'status_tumbuh' => $status,
                'solusi' => $solusi,
                'imunisasi_checked' => $checked,
            ]);
        }

        // --- SEED POSYANDU BUMIL (Rekam Medis 8 Ibu Hamil) ---
        shuffle($bumilCandidates);
        $selectedBumils = array_slice($bumilCandidates, 0, 8);

        foreach ($selectedBumils as $idx => $ibu) {
            $usiaKehamilan = rand(6, 36);
            $beratIbu = 50 + rand(5, 20);
            $lila = 21.0 + (rand(0, 60) / 10); // < 23.5 cm menunjukkan KEK (Kekurangan Energi Kronis)

            if ($lila < 23.5) {
                $status = 'Kekurangan Energi Kronis (KEK)';
                $solusi = 'Lingkar lengan atas (LILA) kurang dari 23.5 cm menunjukkan risiko KEK. Ibu hamil wajib mengonsumsi PMT pemulihan ibu hamil (biskuit/susu), menambah asupan kalori protein harian (tambah nasi, telur, tahu/tempe), rutin minum Tablet Tambah Darah (TTD), serta meminimalkan aktivitas fisik yang melelahkan agar berat badan janin tumbuh normal.';
            } elseif ($idx === 2) {
                $status = 'Hipertensi Gestasional';
                $solusi = 'Tekanan darah ibu hamil tinggi (di atas 140/90 mmHg). Batasi ketat konsumsi garam dan makanan asin/instan, perbanyak istirahat dengan posisi tidur miring kiri untuk melancarkan aliran darah ke janin, kelola stres dengan baik, dan lakukan kontrol mingguan ke bidan/dokter kandungan untuk mencegah risiko preeklamsia.';
            } elseif ($idx === 4) {
                $status = 'Anemia Ringan';
                $solusi = 'Kadar hemoglobin ibu rendah. Disarankan untuk meminum Tablet Tambah Darah (TTD) 2 kali sehari (minum menggunakan air jeruk/sumber Vitamin C untuk memaksimalkan penyerapan zat besi), perbanyak makan sayur hijau (bayam, daun katuk), hati ayam, dan daging merah, serta hindari minum teh/kopi setelah makan.';
            } else {
                $status = 'Sehat';
                $solusi = 'Kondisi kesehatan ibu dan perkembangan janin terpantau sangat baik dan normal. Lanjutkan konsumsi makanan bergizi seimbang (karbohidrat, protein, buah, sayur), minum 1 Tablet Tambah Darah (TTD) setiap hari, minum air putih 2.5 - 3 liter per hari, dan ikuti kelas senam hamil secara rutin.';
            }

            $tekananDarah = $status === 'Hipertensi Gestasional' ? '142/92' : (rand(0, 1) == 0 ? '120/80' : '115/75');

            PosyanduBumil::create([
                'nama_ibu' => $ibu->nama,
                'usia_kehamilan_minggu' => $usiaKehamilan,
                'berat_badan' => $beratIbu,
                'tekanan_darah' => $tekananDarah,
                'lila' => $lila,
                'status_kesehatan' => $status,
                'solusi' => $solusi,
            ]);
        }

        // --- SEED JADWAL PEMERIKSAAN POSYANDU & IMUNISASI ---
        Posyandu::create([
            'nama' => 'Posyandu Balita Melati - Pemeriksaan Tumbuh Kembang',
            'tanggal' => Carbon::now()->addDays(3)->format('Y-m-d'),
            'lokasi' => 'Balai RW 018',
            'keterangan' => 'Kegiatan rutin bulanan penimbangan berat badan, pengukuran tinggi badan, lingkar kepala balita (0-5 tahun), pemberian Vitamin A (kapsul merah & biru), pemberian PMT bubur bergizi, serta konsultasi tumbuh kembang anak.'
        ]);

        Posyandu::create([
            'nama' => 'Pemeriksaan Kesehatan Ibu Hamil & Kelas Senam Hamil',
            'tanggal' => Carbon::now()->addDays(10)->format('Y-m-d'),
            'lokasi' => 'Posyandu Melati Mandiri',
            'keterangan' => 'Pemeriksaan tensi darah, penimbangan berat badan, pengukuran LILA, deteksi detak jantung janin (DJJ), konsultasi gizi kehamilan, pembagian Tablet Tambah Darah (TTD) gratis, dilanjutkan kelas senam hamil sehat.'
        ]);

        Posyandu::create([
            'nama' => 'Imunisasi Polio Massal (PIN) & Booster Campak',
            'tanggal' => Carbon::now()->addDays(17)->format('Y-m-d'),
            'lokasi' => 'Halaman Masjid Al-Ikhlas RT 007',
            'keterangan' => 'Pemberian imunisasi tetes polio (OPV) massal gratis untuk seluruh anak umur 0-59 bulan guna pencegahan kelumpuhan, serta penyuntikan vaksin booster Campak-Rubela (MR) bagi anak berumur 9-18 bulan.'
        ]);

        Posyandu::create([
            'nama' => 'Posyandu Lansia, Deteksi PTM & Senam Bugar',
            'tanggal' => Carbon::now()->addDays(24)->format('Y-m-d'),
            'lokasi' => 'Balai RW 018',
            'keterangan' => 'Pemeriksaan kesehatan lansia meliputi cek tekanan darah, kadar asam urat, gula darah sewaktu, dan kolesterol gratis. Dilanjutkan dengan senam bugar lansia bersama bidan wilayah.'
        ]);
    }
}