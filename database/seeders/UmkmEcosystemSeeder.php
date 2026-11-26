<?php

namespace Database\Seeders;

use App\Models\Umkm;
use App\Models\UmkmFavorit;
use App\Models\UmkmProduk;
use App\Models\UmkmUlasan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UmkmEcosystemSeeder extends Seeder
{
    public function run(): void
    {
        // Clean up previous ecosystem data
        UmkmFavorit::query()->delete();
        UmkmUlasan::query()->delete();
        UmkmProduk::query()->delete();

        $adminRw = User::where('email', 'adminrw@rt.com')->first() ?? User::find(2);
        $adminRt7 = User::where('email', 'adminrt7@rt.com')->first() ?? User::find(4);
        $adminRt8 = User::where('email', 'adminrt8@rt.com')->first() ?? User::find(5);

        $wargaUsers = User::where('role', 'warga')->take(15)->get();
        if ($wargaUsers->isEmpty()) {
            $wargaUsers = User::take(5)->get();
        }

        // Mapping UMKM to Owner User & Verification
        $umkms = Umkm::all();
        foreach ($umkms as $idx => $u) {
            // Assign owner user
            $owner = match ($u->id) {
                1 => $wargaUsers->get(0) ?? $adminRw,
                2 => $wargaUsers->get(1) ?? $adminRw,
                3 => $wargaUsers->get(2) ?? $adminRw,
                4 => $wargaUsers->get(3) ?? $adminRw,
                5 => $wargaUsers->get(4) ?? $adminRw,
                7 => $adminRt7,
                8 => $adminRt8,
                default => $wargaUsers->get(($idx % $wargaUsers->count())) ?? $adminRw,
            };

            $statusVerifikasi = ($u->id === 5 || $u->id === 10) ? 'menunggu' : 'terverifikasi';
            $verifiedAt = $statusVerifikasi === 'terverifikasi' ? Carbon::now()->subMonths(3) : null;
            $verifiedBy = $statusVerifikasi === 'terverifikasi' ? ($adminRw ? $adminRw->id : 2) : null;

            $u->update([
                'user_id' => $owner ? $owner->id : null,
                'status_verifikasi' => $statusVerifikasi,
                'verified_at' => $verifiedAt,
                'verified_by' => $verifiedBy,
                'jam_operasional' => '08:00 - 21:00 WIB',
            ]);
        }

        // Sample Products per Category
        $sampleProducts = [
            1 => [ // Aurora Atelier (Kerajinan)
                ['nama' => 'Tas Anyaman Rotan Estetik', 'deskripsi' => 'Tas jinjing anyaman tangan dari serat rotan alami, kuat dan ramah lingkungan.', 'harga' => 125000, 'foto' => 'https://images.unsplash.com/photo-1590736704728-f4730bb30770?auto=format&fit=crop&w=500&q=80'],
                ['nama' => 'Lilin Aromaterapi Scented Soy', 'deskripsi' => 'Lilin wangi terapi dari minyak kedelai alami dengan aroma lavender menenangkan.', 'harga' => 65000, 'foto' => 'https://images.unsplash.com/photo-1603006905003-be475563bc59?auto=format&fit=crop&w=500&q=80'],
                ['nama' => 'Gelang Anyaman Kulit Handmade', 'deskripsi' => 'Aksesoris gelang kulit asli kombinasi manik kayu khas pengrajin warga.', 'harga' => 45000, 'foto' => 'https://images.unsplash.com/photo-1611591437281-460bfbe1220a?auto=format&fit=crop&w=500&q=80'],
            ],
            2 => [ // Velvet Vibes (Jasa)
                ['nama' => 'Jasa Jahit & Permak Busana Custom', 'deskripsi' => 'Jahit gaun, kebaya, kemeja batik, dan permak celana jeans rapi dan cepat.', 'harga' => 85000, 'foto' => 'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?auto=format&fit=crop&w=500&q=80'],
                ['nama' => 'Konsultasi Desain & Pola Busana', 'deskripsi' => 'Pembuatan pola pakaian custom sesuai ukuran badan personal.', 'harga' => 120000, 'foto' => 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=500&q=80'],
            ],
            3 => [ // Serein Sips (Makanan & Minuman)
                ['nama' => 'Es Kopi Susu Gula Aren 250ml', 'deskripsi' => 'Espresso arabika blend dengan susu segar dan sirup aren murni organik.', 'harga' => 18000, 'foto' => 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?auto=format&fit=crop&w=500&q=80'],
                ['nama' => 'Roti Bakar Cokelat Keju Gandum', 'deskripsi' => 'Roti gandum bakar empuk bertabur meses cokelat belgia dan keju cheddar tebal.', 'harga' => 22000, 'foto' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=500&q=80'],
                ['nama' => 'Cold Brew Tea Hibiscus Lemon', 'deskripsi' => 'Teh seduh dingin bunga rosella segar dengan sensasi asam manis menyegarkan.', 'harga' => 16000, 'foto' => 'https://images.unsplash.com/photo-1576092768241-dec231879fc3?auto=format&fit=crop&w=500&q=80'],
            ],
            7 => [ // Warung Nasi Timbel Teh Lilis (Kuliner)
                ['nama' => 'Paket Nasi Timbel Komplit Ayam Goreng', 'deskripsi' => 'Nasi timbel daun pisang, ayam goreng lengkuas, tahu tempe, lalap sambal terasi teruji lezat.', 'harga' => 28000, 'foto' => 'https://images.unsplash.com/photo-1565557623262-b51c2513a641?auto=format&fit=crop&w=500&q=80'],
                ['nama' => 'Sayur Asem Khas Sunda Porsi Besar', 'deskripsi' => 'Sayur asem segar asam gurih isi jagung manis, labu siam, dan melinjo.', 'harga' => 15000, 'foto' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=500&q=80'],
                ['nama' => 'Pepes Ikan Mas Duri Lunak', 'deskripsi' => 'Ikan mas bumbu kuning kemangi wangi rempah, duri empuk lumer di mulut.', 'harga' => 25000, 'foto' => 'https://images.unsplash.com/photo-1612927601601-6638404737ce?auto=format&fit=crop&w=500&q=80'],
            ],
            8 => [ // Bakso Aci Garut Neng Santi (Kuliner)
                ['nama' => 'Bakso Aci Kuah Pedas Jeletot', 'deskripsi' => 'Boci kenyal isi jando sapi, cuankie lidah, siomay mini, pilus cikur, dan jeruk limau segar.', 'harga' => 20000, 'foto' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=500&q=80'],
                ['nama' => 'Seblak Prasmanan Aneka Topping', 'deskripsi' => 'Seblak kuah kencur gurih dengan pilihan kerupuk mawar, sosis, bakso, dan ceker empuk.', 'harga' => 18000, 'foto' => 'https://images.unsplash.com/photo-1585032226651-759b368d7246?auto=format&fit=crop&w=500&q=80'],
            ],
            9 => [ // Sate Maranggi Pak Haji (Kuliner)
                ['nama' => 'Sate Maranggi Daging Sapi (10 Tusuk)', 'deskripsi' => 'Daging sapi empuk marinasi ketumbar manis disajikan dengan sambal tomat pedas.', 'harga' => 38000, 'foto' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&w=500&q=80'],
                ['nama' => 'Sop Iga Sapi Kuah Bening', 'deskripsi' => 'Iga sapi lembut kuah kaldu rempah gurih hangat.', 'harga' => 45000, 'foto' => 'https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=500&q=80'],
            ],
            10 => [ // Es Cendol Mang Asep
                ['nama' => 'Es Cendol Durian Medan Spesial', 'deskripsi' => 'Cendol beras pandan wangi, santan gurih, gula aren cair, dan 1 butir daging durian montong.', 'harga' => 22000, 'foto' => 'https://images.unsplash.com/photo-1551024709-8f23befc6f87?auto=format&fit=crop&w=500&q=80'],
                ['nama' => 'Es Cendol Nangka Santan Murni', 'deskripsi' => 'Cendol lembut wangi suji dengan potongan nangka manis legit.', 'harga' => 15000, 'foto' => 'https://images.unsplash.com/photo-1563805042-7684c019e1cb?auto=format&fit=crop&w=500&q=80'],
            ]
        ];

        // Seed products for all UMKMs
        foreach ($umkms as $u) {
            $prods = $sampleProducts[$u->id] ?? [
                ['nama' => 'Paket Produk Unggulan ' . $u->nama, 'deskripsi' => 'Produk andalan buatan warga dengan kualitas terjamin.', 'harga' => 35000, 'foto' => $u->cover_image],
                ['nama' => 'Layanan Khusus ' . $u->kategori, 'deskripsi' => 'Pelayanan ramah langsung dari pengrajin warga.', 'harga' => 25000, 'foto' => $u->cover_image],
            ];

            foreach ($prods as $p) {
                UmkmProduk::create([
                    'umkm_id' => $u->id,
                    'nama_produk' => $p['nama'],
                    'deskripsi' => $p['deskripsi'],
                    'harga' => $p['harga'],
                    'foto' => $p['foto'],
                    'is_tersedia' => true,
                ]);
            }
        }

        // Sample Reviews
        $reviewsPool = [
            ['rating' => 5, 'komentar' => 'Sangat memuaskan! Rasanya konsisten enak dan pengirimannya cepat ke rumah di RT 007. Penjualnya ramah banget.', 'balasan' => 'Terima kasih banyak atas dukungannya Bu! Ditunggu pesanan berikutnya ya.'],
            ['rating' => 5, 'komentar' => 'Kualitas produknya rapi dan estetik, cocok banget buat souvenir acara keluarga kemarin. Sukses terus untuk usahanya.', 'balasan' => 'Alhamdulillah, senang bisa membantu acara ibu. Terima kasih ulasan bintang 5-nya!'],
            ['rating' => 4, 'komentar' => 'Pelayanan cepat dan harga sangat bersahabat untuk sesama warga. Rasa mantap, porsi cukup banyak.', 'balasan' => 'Terima kasih Pak atas ulasannya. Kami terus jaga kualitas untuk warga.'],
            ['rating' => 5, 'komentar' => 'Langganan setia tiap akhir pekan. Senang ada UMKM berkualitas tinggi di lingkungan RW 018 sendiri.', 'balasan' => null],
            ['rating' => 4, 'komentar' => 'Recommended banget. Komunikasi via WhatsApp lancar dan langsung diantar ke depan teras rumah.', 'balasan' => 'Sama-sama Pak, kami siap melayani pesanan kapan saja.'],
        ];

        foreach ($umkms as $u) {
            $buyerCount = rand(2, 4);
            for ($i = 0; $i < $buyerCount; $i++) {
                $reviewer = $wargaUsers->get(($u->id + $i) % $wargaUsers->count()) ?? $wargaUsers->first();
                $revData = $reviewsPool[($u->id + $i) % count($reviewsPool)];

                UmkmUlasan::create([
                    'umkm_id' => $u->id,
                    'user_id' => $reviewer->id,
                    'nama_pembeli' => $reviewer->name,
                    'rating' => $revData['rating'],
                    'komentar' => $revData['komentar'],
                    'is_verified_purchase' => true,
                    'balasan_penjual' => $revData['balasan'],
                    'balasan_at' => $revData['balasan'] ? Carbon::now()->subDays(2) : null,
                    'is_reported' => false,
                    'is_hidden' => false,
                    'created_at' => Carbon::now()->subDays(rand(3, 30)),
                ]);
            }

            // Recalculate rating
            $u->refreshRating();
        }

        // Add 1 reported review on UMKM 7 for testing moderation by Admin RW
        $sampleReviewer = $wargaUsers->last();
        if ($sampleReviewer) {
            UmkmUlasan::create([
                'umkm_id' => 7,
                'user_id' => $sampleReviewer->id,
                'nama_pembeli' => $sampleReviewer->name,
                'rating' => 1,
                'komentar' => 'Pesanannya agak telat 20 menit saat jam makan siang kemarin.',
                'is_verified_purchase' => true,
                'balasan_penjual' => 'Mohon maaf atas keterlambatannya Pak, kemarin pas jam makan siang kurir kami kehujanan di jalan.',
                'balasan_at' => Carbon::now()->subHours(12),
                'is_reported' => true,
                'report_reason' => 'Perbedaan persepsi waktu pengantaran saat hujan lebat, mohon dimoderasi.',
                'is_hidden' => false,
                'created_at' => Carbon::now()->subDays(1),
            ]);
            $umkms->find(7)?->refreshRating();
        }

        // Seed some favorites
        foreach ($wargaUsers->take(4) as $w) {
            UmkmFavorit::create([
                'umkm_id' => 3, // Serein Sips
                'user_id' => $w->id,
            ]);
            UmkmFavorit::create([
                'umkm_id' => 7, // Nasi Timbel
                'user_id' => $w->id,
            ]);
        }
    }
}
