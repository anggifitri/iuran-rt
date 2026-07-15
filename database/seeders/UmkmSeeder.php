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
                'alamat' => 'Jl. Bunga Sakura No. 12',
                'deskripsi' => 'Kerajinan tangan premium dengan sentuhan estetika dan bahan lokal pilihan.',
            ],
            [
                'nama' => 'Velvet Vibes',
                'pemilik' => 'Adara Sky',
                'kategori' => 'Jasa',
                'telepon' => '081234560002',
                'alamat' => 'Jl. Cemara Indah No. 7',
                'deskripsi' => 'Layanan personal styling dan konsultasi brand untuk usaha kreatif.',
            ],
            [
                'nama' => 'Serein Sips',
                'pemilik' => 'Mika Rain',
                'kategori' => 'Makanan & Minuman',
                'telepon' => '081234560003',
                'alamat' => 'Jl. Pelangi No. 9',
                'deskripsi' => 'Kafe kecil dengan menu minuman segar, pastry artisan, dan vibes humid.',
            ],
            [
                'nama' => 'Nimble Nook',
                'pemilik' => 'Celia Moon',
                'kategori' => 'Jasa/Service',
                'telepon' => '081234560004',
                'alamat' => 'Jl. Melati No. 19',
                'deskripsi' => 'Layanan kreatif dan perbaikan cepat untuk kebutuhan rumah dan bisnis kecil.',
            ],
            [
                'nama' => 'Luna Lumi',
                'pemilik' => 'Faye Oria',
                'kategori' => 'Kuliner',
                'telepon' => '081234560005',
                'alamat' => 'Jl. Purnama No. 3',
                'deskripsi' => 'Dapur rumahan dengan sajian kuliner estetis dan cita rasa kekinian.',
            ],
            [
                'nama' => 'Pax Peddler',
                'pemilik' => 'Noah Sage',
                'kategori' => 'Perdagangan',
                'telepon' => '081234560006',
                'alamat' => 'Jl. Harmoni No. 5',
                'deskripsi' => 'Toko konsep untuk produk lifestyle, kerajinan, dan kebutuhan sehari-hari.',
            ],
        ];

        foreach ($umkms as $umkm) {
            Umkm::updateOrCreate([
                'nama' => $umkm['nama'],
                'pemilik' => $umkm['pemilik'],
            ], $umkm);
        }
    }
}
