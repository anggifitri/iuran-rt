<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Umkm;

class UmkmSeeder extends Seeder
{
    public function run(): void
    {
        $umkms = [
            [
                'nama' => 'Aurora Atelier',
                'pemilik' => 'Luna Melati',
                'kategori' => 'Kerajinan',
                'telepon' => '081234560001',
                'alamat' => 'Jl. Bunga Sakura No. 12, RT 006 RW 018',
                'deskripsi' => 'Kerajinan tangan premium dengan sentuhan estetika dan bahan lokal pilihan berkualitas tinggi.',
                'cover_image' => 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?auto=format&fit=crop&w=600&q=80',
                'rt_number' => '006',
            ],
            [
                'nama' => 'Velvet Vibes',
                'pemilik' => 'Adara Sky',
                'kategori' => 'Jasa',
                'telepon' => '081234560002',
                'alamat' => 'Jl. Cemara Indah No. 7, RT 007 RW 018',
                'deskripsi' => 'Layanan personal styling, desain busana custom, dan konsultasi trend brand lokal.',
                'cover_image' => 'https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?auto=format&fit=crop&w=600&q=80',
                'rt_number' => '007',
            ],
            [
                'nama' => 'Serein Sips',
                'pemilik' => 'Mika Rain',
                'kategori' => 'Makanan & Minuman',
                'telepon' => '081234560003',
                'alamat' => 'Jl. Pelangi No. 9, RT 008 RW 018',
                'deskripsi' => 'Kafe minimalis dengan menu racikan minuman dingin segar, kopi susu gula aren, dan roti bakar kaya rasa.',
                'cover_image' => 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?auto=format&fit=crop&w=600&q=80',
                'rt_number' => '008',
            ],
            [
                'nama' => 'Nimble Nook',
                'pemilik' => 'Celia Moon',
                'kategori' => 'Jasa',
                'telepon' => '081234560004',
                'alamat' => 'Jl. Melati No. 19, RT 009 RW 018',
                'deskripsi' => 'Jasa reparasi perkakas rumah tangga, perbaikan pipa air, kelistrikan, dan pertukangan kayu kilat.',
                'cover_image' => 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?auto=format&fit=crop&w=600&q=80',
                'rt_number' => '009',
            ],
            [
                'nama' => 'Luna Lumi',
                'pemilik' => 'Faye Oria',
                'kategori' => 'Kuliner',
                'telepon' => '081234560005',
                'alamat' => 'Jl. Purnama No. 3, RT 010 RW 018',
                'deskripsi' => 'Catering harian rumahan menyajikan menu sarapan sehat, bento box anak sekolah, dan tumpeng mini.',
                'cover_image' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=600&q=80',
                'rt_number' => '010',
            ],
            [
                'nama' => 'Pax Peddler',
                'pemilik' => 'Noah Sage',
                'kategori' => 'Perdagangan',
                'telepon' => '081234560006',
                'alamat' => 'Jl. Harmoni No. 5, RT 006 RW 018',
                'deskripsi' => 'Sembako lengkap dan aneka kebutuhan pokok rumah tangga terlengkap dengan harga bersaing.',
                'cover_image' => 'https://images.unsplash.com/photo-1472851294608-062f824d29cc?auto=format&fit=crop&w=600&q=80',
                'rt_number' => '006',
            ],
            [
                'nama' => 'Warung Nasi Timbel Teh Lilis',
                'pemilik' => 'Lilis Suryani',
                'kategori' => 'Kuliner',
                'telepon' => '081234560007',
                'alamat' => 'Jl. Melati Indah No. 2, RT 007 RW 018',
                'deskripsi' => 'Nasi timbel hangat komplit dengan ayam goreng serundeng khas Sunda, sambal terasi dadak super pedas, lalapan segar, dan tahu-tempe goreng crispy.',
                'cover_image' => 'https://images.unsplash.com/photo-1610192244261-3f33de3f55e4?auto=format&fit=crop&w=600&q=80',
                'rt_number' => '007',
            ],
            [
                'nama' => 'Bakso Aci Garut Neng Santi',
                'pemilik' => 'Santi Rahayu',
                'kategori' => 'Kuliner',
                'telepon' => '081234560008',
                'alamat' => 'Jl. Dahlia No. 4, RT 008 RW 018',
                'deskripsi' => 'Bakso aci kenyal isi daging sapi dengan kuah pedas gurih, jeruk limau segar, cuanki lidah renyah, dan taburan pilus cikur khas Priangan.',
                'cover_image' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&w=600&q=80',
                'rt_number' => '008',
            ],
            [
                'nama' => 'Sate Maranggi Purwakarta Pak Haji',
                'pemilik' => 'Haji Rohman',
                'kategori' => 'Kuliner',
                'telepon' => '081234560009',
                'alamat' => 'Jl. Cempaka No. 11, RT 009 RW 018',
                'deskripsi' => 'Sate daging sapi maranggi tanpa lemak yang empuk direndam bumbu ketumbar tradisional, disajikan dengan sambal tomat iris yang pedas segar.',
                'cover_image' => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=600&q=80',
                'rt_number' => '009',
            ],
            [
                'nama' => 'Es Cendol Durian Mang Asep',
                'pemilik' => 'Asep Sunandar',
                'kategori' => 'Makanan & Minuman',
                'telepon' => '081234560010',
                'alamat' => 'Jl. Kenanga No. 25, RT 010 RW 018',
                'deskripsi' => 'Es cendol beras hijau pandan asli dipadu kuah santan gurih, sirup gula aren kental, dan topping satu buah daging durian Medan manis legit.',
                'cover_image' => 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?auto=format&fit=crop&w=600&q=80',
                'rt_number' => '010',
            ],
        ];

        foreach ($umkms as $umkm) {
            Umkm::updateOrCreate([
                'nama' => $umkm['nama'],
            ], $umkm);
        }
    }
}
