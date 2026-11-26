<?php

namespace Database\Seeders;

use App\Models\BankSampah;
use App\Models\BankSampahEdukasi;
use App\Models\BankSampahHargaHistory;
use App\Models\BankSampahKategori;
use App\Models\BankSampahPenarikan;
use App\Models\User;
use App\Models\Warga;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BankSampahSeeder extends Seeder
{
    public function run(): void
    {
        $adminRw = User::where('role', 'admin')->where('rt_number', '000')->first() ?: User::first();

        // 1. Seed Kategori Sampah Standar RW 018
        $kategoris = [
            [
                'kode' => 'plastik',
                'nama_kategori' => 'Sampah Plastik',
                'deskripsi' => 'Botol PET bening, tutup, gelas PP air mineral, jerigen, dan kantong kresek bersih.',
                'satuan' => 'Kg',
                'harga_min' => 2500,
                'harga_max' => 4500,
                'harga_acuan' => 3500,
                'tips_pemilahan' => 'Bilas hingga bersih dari sisa cairan manis/minyak, keringkan, dan remas/pipihkan untuk menghemat ruang penyimpanan.',
                'icon' => 'fa-bottle-water',
                'accent_color' => '#285943',
                'is_active' => true,
            ],
            [
                'kode' => 'kertas',
                'nama_kategori' => 'Kertas & Dokumen',
                'deskripsi' => 'Koran bekas, majalah, kertas HVS arsip kantor, brosur, dan buku pelajaran usang.',
                'satuan' => 'Kg',
                'harga_min' => 1500,
                'harga_max' => 3200,
                'harga_acuan' => 2350,
                'tips_pemilahan' => 'Pastikan dalam kondisi kering tanpa noda minyak atau basah. Lepaskan klip staples tebal dan jilid spiral plastik.',
                'icon' => 'fa-newspaper',
                'accent_color' => '#1F567A',
                'is_active' => true,
            ],
            [
                'kode' => 'kardus',
                'nama_kategori' => 'Kardus & Karton',
                'deskripsi' => 'Kardus packing cokelat tebal bergelombang, karton box makanan, dan kotak dupleks.',
                'satuan' => 'Kg',
                'harga_min' => 1600,
                'harga_max' => 2800,
                'harga_acuan' => 2200,
                'tips_pemilahan' => 'Lipat mendatar atau gepengkan kardus, lepaskan sisa lakban plastik tebal, lalu ikat rapi menggunakan tali rafia.',
                'icon' => 'fa-box-open',
                'accent_color' => '#8C6A05',
                'is_active' => true,
            ],
            [
                'kode' => 'logam',
                'nama_kategori' => 'Logam & Kaleng',
                'deskripsi' => 'Kaleng minuman aluminium, kaleng susu kental, seng, besi rongsok, dan kabel tembaga.',
                'satuan' => 'Kg',
                'harga_min' => 4000,
                'harga_max' => 18000,
                'harga_acuan' => 8500,
                'tips_pemilahan' => 'Pisahkan kaleng aluminium (ringan, tidak menempel magnet) dari kaleng besi/seng karena nilai ekonominya berbeda jauh.',
                'icon' => 'fa-can-food',
                'accent_color' => '#3E4657',
                'is_active' => true,
            ],
            [
                'kode' => 'kaca',
                'nama_kategori' => 'Kaca & Botol Utuh',
                'deskripsi' => 'Botol kecap, sirup, saus, botol minuman kaca bening atau cokelat utuh tidak pecah.',
                'satuan' => 'Kg',
                'harga_min' => 800,
                'harga_max' => 2000,
                'harga_acuan' => 1400,
                'tips_pemilahan' => 'Utamakan botol dalam keadaan utuh tanpa retak/sumbing. Kemas dalam krat atau peti kayu agar aman bagi petugas timbang.',
                'icon' => 'fa-wine-bottle',
                'accent_color' => '#156666',
                'is_active' => true,
            ],
            [
                'kode' => 'minyak',
                'nama_kategori' => 'Minyak Jelantah & Organik',
                'deskripsi' => 'Minyak goreng bekas pakai rumah tangga (Used Cooking Oil / UCO) tersaring dan pupuk kompos matang.',
                'satuan' => 'Liter',
                'harga_min' => 5500,
                'harga_max' => 7500,
                'harga_acuan' => 6500,
                'tips_pemilahan' => 'Saring minyak jelantah dari remah tepung dan endapan gorengan, simpan di dalam jerigen atau botol plastik tebal tertutup.',
                'icon' => 'fa-seedling',
                'accent_color' => '#8A4E29',
                'is_active' => true,
            ],
            [
                'kode' => 'lainnya',
                'nama_kategori' => 'Kategori Lainnya',
                'deskripsi' => 'Barang elektronik kecil bekas, botol plastik tebal campuran, dan aneka limbah anorganik daur ulang.',
                'satuan' => 'Kg',
                'harga_min' => 1000,
                'harga_max' => 3000,
                'harga_acuan' => 2000,
                'tips_pemilahan' => 'Kelompokkan berdasarkan jenis material sejenis untuk mempermudah pengecekan oleh petugas bank sampah.',
                'icon' => 'fa-cubes',
                'accent_color' => '#718096',
                'is_active' => true,
            ],
        ];

        foreach ($kategoris as $k) {
            $kat = BankSampahKategori::updateOrCreate(
                ['kode' => $k['kode']],
                array_merge($k, ['created_by' => $adminRw ? $adminRw->id : null])
            );

            // Seed price history jika belum ada
            if ($kat->priceHistories()->count() === 0) {
                BankSampahHargaHistory::create([
                    'kategori_id' => $kat->id,
                    'harga_lama' => $k['harga_acuan'] - 200,
                    'harga_baru' => $k['harga_acuan'],
                    'user_id' => $adminRw ? $adminRw->id : null,
                    'catatan' => 'Penyesuaian berkala harga pasar daur ulang RW 018 per November 2026.',
                ]);
            }
        }

        // 2. Seed Materi Artikel Edukasi Lingkungan
        $edukasiArticles = [
            [
                'judul' => 'Panduan Memilah Sampah Organik dan Anorganik dari Dapur Rumah',
                'kategori_materi' => 'Pemilahan',
                'icon' => 'fa-recycle',
                'deskripsi_singkat' => 'Langkah fundamental memulai pemilahan sampah rumah tangga dengan sistem dua tempat sampah untuk memisahkan sisa makanan basah dan kemasan kering.',
                'isi_materi' => "Memilah sampah dari rumah tangga adalah pondasi utama terwujudnya lingkungan bersih dan ekonomi sirkular di RW 018.\n\n1. Sediakan Dua Wadah Terpisah: Tempat sampah hijau untuk sampah organik (sisa sayur, buah, sisa nasi, ampas teh/kopi) dan tempat sampah kuning untuk anorganik kering (plastik, kertas, kaleng).\n2. Jaga Sampah Anorganik Tetap Kering: Sampah kertas dan kardus yang basah terkena kuah sayur akan rusak dan turun nilai ekonominya drastis di bank sampah.\n3. Libatkan Seluruh Anggota Keluarga: Edukasikan anak-anak dan keluarga agar membiasakan diri membuang sampah pada wadah yang sesuai.",
            ],
            [
                'judul' => 'Mengenal Kode Daur Ulang Plastik (PET, HDPE, PVC, LDPE, PP, PS, OTHER)',
                'kategori_materi' => 'Daur Ulang',
                'icon' => 'fa-bottle-water',
                'deskripsi_singkat' => 'Memahami simbol segitiga panah bernomor 1 sampai 7 pada kemasan plastik dan mengetahui jenis mana yang paling dicari bank sampah.',
                'isi_materi' => "Di bagian bawah setiap botol atau kemasan plastik terdapat simbol segitiga bernomor:\n\n- Nomor 1 (PET / PETE): Botol air mineral bening, botol jus. Nilai paling tinggi di bank sampah dan paling mudah didaur ulang menjadi serat tekstil sintetis.\n- Nomor 2 (HDPE): Botol sampo tebal, botol pembersih lantai, jerigen. Kuat dan aman, sangat diterima bank sampah.\n- Nomor 3 (PVC): Pipa paralon, selang air. Tidak disarankan untuk kemasan makanan.\n- Nomor 4 (LDPE): Plastik kresek bening, bubble wrap, plastik pelindung.\n- Nomor 5 (PP): Gelas minuman plastik kekinian, mangkok microwave, kotak makan Tupperware. Bernilai jual tinggi di bank sampah.\n- Nomor 6 (PS): Styrofoam dan sendok sekali pakai. Sulit didaur ulang.\n- Nomor 7 (OTHER): Plastik campuran polikarbonat, botol galon keras.",
            ],
            [
                'judul' => 'Cara Praktis Mencuci & Menyimpan Sampah Sebelum Disetorkan',
                'kategori_materi' => 'Pemilahan',
                'icon' => 'fa-soap',
                'deskripsi_singkat' => 'Teknik mencuci wadah botol dan gelas plastik agar tidak menimbulkan bau tidak sedap dan tidak mengundang lalat atau semut di rumah.',
                'isi_materi' => "Agar sampah yang dikumpulkan tidak menimbulkan bau tak sedap sebelum jadwal penimbangan Bank Sampah:\n\n1. Bilas dengan Sisa Air: Cukup gunakan sedikit air bekas cucian piring untuk membilas sisa susu, kecap, atau minuman manis.\n2. Tiriskan dan Keringkan: Balikkan botol di tempat penirisan hingga kering sebelum dimasukkan ke kantong penyimpanan.\n3. Pipihkan Botol: Remas atau injak botol hingga gepeng untuk menghemat volume ruangan hingga 70%.\n4. Pisahkan Tutup Botol: Tutup botol (HDPE) memiliki jenis plastik berbeda dengan badan botol (PET), pisahkan di kantong kecil tersendiri.",
            ],
            [
                'judul' => 'Pengolahan Sampah Dapur Menjadi Pupuk Kompos dengan Biopori & Takakura',
                'kategori_materi' => 'Kompos',
                'icon' => 'fa-seedling',
                'deskripsi_singkat' => 'Mengolah sisa sayuran, kulit buah, dan daun taman menjadi pupuk kompos organik yang subur untuk tanaman hias dan pekarangan rumah.',
                'isi_materi' => "Hampir 50% sampah rumah tangga adalah sampah organik basah. Mengolahnya menjadi kompos mengurangi volume sampah ke TPA secara signifikan:\n\n1. Metode Lubang Biopori: Tanam pipa PVC berlubang di halaman rumah, masukkan sampah buah dan sayur secara berkala. Selain menghasilkan kompos cair dan padat, biopori mencegah genangan banjir.\n2. Metode Keranjang Takakura: Cocok untuk rumah dengan lahan terbatas. Menggunakan keranjang pakaian berventilasi yang diisi sekam dan bantal sabut kelapa untuk fermentasi sampah dapur aerobik tanpa bau.",
            ],
            [
                'judul' => 'Langkah Mudah Memulai Gaya Hidup Minim Sampah (Zero Waste Lifestyle)',
                'kategori_materi' => 'Zero Waste',
                'icon' => 'fa-leaf',
                'deskripsi_singkat' => 'Prinsip 5R (Refuse, Reduce, Reuse, Repurpose, Recycle) yang mudah diterapkan dalam aktivitas belanja dan rutinitas harian warga RW 018.',
                'isi_materi' => "Prinsip Zero Waste bukan berarti tidak memproduksi sampah sama sekali, melainkan upaya sadar mengurangi sampah dari hulu:\n\n- Refuse: Tolak kantong kresek sekali pakai saat belanja di minimarket atau pasar, selalu bawa tas kain sendiri.\n- Reduce: Kurangi membeli produk kemasan sachet kecil, beralihlah ke kemasan isi ulang (refill) yang lebih ekonomis.\n- Reuse: Gunakan tumbler minuman dan tempat makan sendiri saat beraktivitas di luar rumah.\n- Repurpose: Alih fungsikan toples kaca selai atau kaleng biskuit menjadi wadah bumbu atau pot tanaman hias.\n- Recycle: Setorkan seluruh kemasan yang tidak terpakai ke Bank Sampah RW 018 untuk didaur ulang.",
            ],
            [
                'judul' => 'Pemanfaatan Minyak Jelantah Menjadi Biodiesel & Sabun Pembersih',
                'kategori_materi' => 'Daur Ulang',
                'icon' => 'fa-oil-can',
                'deskripsi_singkat' => 'Jangan buang minyak jelantah ke saluran air! Minyak goreng bekas bernilai Rp 6.500/liter di Bank Sampah RW dan diolah menjadi bahan bakar terbarukan.',
                'isi_materi' => "Membuang minyak jelantah ke wastafel atau selokan menyebabkan penyumbatan pipa saluran air dan mencemari air tanah:\n\n1. Kumpulkan di Wadah Khusus: Dinginkan minyak bekas pakai, saring dengan saringan teh kain, dan kumpulkan di botol atau jerigen plastik.\n2. Bernilai Rupiah Tinggi: Di Bank Sampah RW 018, setiap liter minyak jelantah dihargai Rp 5.500 s/d Rp 7.500 per liter.\n3. Manfaat Hilir: Minyak jelantah yang dikumpulkan disalurkan ke fasilitas pemurnian resmi untuk diolah menjadi bahan bakar biodiesel ramah lingkungan dan lilin aromaterapi.",
            ],
            [
                'judul' => 'Tips Menjaga Kebersihan Lingkungan & Mencegah Sarang Nyamuk DBD',
                'kategori_materi' => 'Kebersihan',
                'icon' => 'fa-shield-halved',
                'deskripsi_singkat' => 'Gerakan 3M Plus bersama kader Jumantik RT untuk memastikan lingkungan bebas genangan jentik nyamuk dan bersih nyaman.',
                'isi_materi' => "Kebersihan lingkungan berdampak langsung pada kesehatan seluruh warga RW 018:\n\n1. Menguras Tempat Penampungan Air: Kuras bak mandi dan wadah penampung air kulkas minimal seminggu sekali.\n2. Menutup Rapat Tempat Penampungan: Pastikan toren dan ember air tertutup rapat agar nyamuk Aedes aegypti tidak dapat bertelur.\n3. Mengubur / Mendaur Ulang Barang Bekas: Jangan biarkan kaleng, botol, atau ban bekas menampung air hujan di pekarangan rumah.\n4. Plus: Tanam tanaman pengusir nyamuk seperti serai wangi, lavender, dan rosemary di teras rumah.",
            ],
        ];

        foreach ($edukasiArticles as $idx => $art) {
            BankSampahEdukasi::updateOrCreate(
                ['judul' => $art['judul']],
                array_merge($art, [
                    'status' => 'published',
                    'created_by' => $adminRw ? $adminRw->id : null,
                    'published_at' => Carbon::now()->subDays(rand(1, 30)),
                ])
            );
        }

        // 3. Backfill data transaksi lama yang sudah ada di database agar memiliki nomor transaksi unik dan harga satuan
        $existingSetorans = BankSampah::whereNull('nomor_transaksi')->orWhere('nomor_transaksi', '')->get();
        $counter = 1;
        foreach ($existingSetorans as $setoran) {
            $tgl = Carbon::parse($setoran->tanggal_setor);
            $kodeBulan = $tgl->format('Ym');
            $nomorTx = 'BS-' . $kodeBulan . '-' . str_pad($counter, 4, '0', STR_PAD_LEFT);

            // Tentukan harga per satuan berdasarkan kategori
            $katModel = BankSampahKategori::where('kode', $setoran->kategori)->first();
            $hargaAcuan = $katModel ? $katModel->harga_acuan : 3000;
            $satuan = $katModel ? $katModel->satuan : 'Kg';

            $nilai = $setoran->nilai_rupiah > 0 ? $setoran->nilai_rupiah : round($setoran->berat_kg * $hargaAcuan);
            $hargaPerSatuan = $setoran->berat_kg > 0 ? round($nilai / $setoran->berat_kg) : $hargaAcuan;

            // Link ke warga terdaftar jika ada nama yang cocok
            $wargaMatch = Warga::where('nama', 'like', '%' . $setoran->nama_penyetor . '%')->first();

            $setoran->update([
                'nomor_transaksi' => $nomorTx,
                'satuan' => $satuan,
                'harga_per_satuan' => $hargaPerSatuan,
                'nilai_rupiah' => $nilai,
                'status' => 'diterima',
                'warga_id' => $wargaMatch ? $wargaMatch->id : $setoran->warga_id,
                'verified_by' => $adminRw ? $adminRw->id : null,
                'verified_at' => $tgl->addHours(2),
            ]);

            $counter++;
        }

        // 4. Pastikan ada transaksi bulanan lengkap dari Jan 2026 s/d Nov 2026 untuk 5 RT
        // agar grafik perkembangan bulanan dan perbandingan antar-RT terisi realistis
        $wargas = Warga::where('status_warga', '!=', 'pindah')->take(20)->get();
        if ($wargas->isEmpty()) {
            $wargas = collect([
                (object)['id' => 1, 'nama' => 'Aisha Gunawan', 'rt_number' => '006'],
                (object)['id' => 2, 'nama' => 'Budi Santoso', 'rt_number' => '006'],
                (object)['id' => 3, 'nama' => 'Gavin Gunawan', 'rt_number' => '007'],
                (object)['id' => 4, 'nama' => 'Bagas Tanjung', 'rt_number' => '007'],
                (object)['id' => 5, 'nama' => 'Rizky Pramudya', 'rt_number' => '008'],
                (object)['id' => 6, 'nama' => 'Alya Kusuma', 'rt_number' => '008'],
                (object)['id' => 7, 'nama' => 'Adrian Tanjung', 'rt_number' => '009'],
                (object)['id' => 8, 'nama' => 'Arkan Lestari', 'rt_number' => '009'],
                (object)['id' => 9, 'nama' => 'Bagas Kusumah', 'rt_number' => '010'],
                (object)['id' => 10, 'nama' => 'Adrian Pradana', 'rt_number' => '010'],
            ]);
        }

        $allKategoris = BankSampahKategori::all();
        $txNumberCounter = BankSampah::count() + 1;

        // Cek apakah data transaksi bulanan Januari - Oktober sudah lengkap
        $janCount = BankSampah::whereMonth('tanggal_setor', 1)->whereYear('tanggal_setor', 2026)->count();
        if ($janCount === 0) {
            for ($bulan = 1; $bulan <= 10; $bulan++) {
                foreach (['006', '007', '008', '009', '010'] as $rt) {
                    $wargaRt = $wargas->where('rt_number', $rt)->first() ?: $wargas->first();
                    $katItem = $allKategoris->random();

                    $tglTx = Carbon::create(2026, $bulan, rand(5, 25), rand(9, 16), rand(10, 50));
                    $kodeBln = $tglTx->format('Ym');
                    $berat = round(rand(80, 350) / 10, 1);
                    $harga = $katItem->harga_acuan;
                    $totalNilai = round($berat * $harga);

                    BankSampah::create([
                        'nomor_transaksi' => 'BS-' . $kodeBln . '-' . str_pad($txNumberCounter++, 4, '0', STR_PAD_LEFT),
                        'rt_number' => $rt,
                        'warga_id' => $wargaRt ? $wargaRt->id : null,
                        'nama_penyetor' => $wargaRt ? $wargaRt->nama : 'Warga RT ' . $rt,
                        'tanggal_setor' => $tglTx->toDateString(),
                        'kategori' => $katItem->kode,
                        'berat_kg' => $berat,
                        'satuan' => $katItem->satuan,
                        'harga_per_satuan' => $harga,
                        'nilai_rupiah' => $totalNilai,
                        'status' => 'diterima',
                        'catatan' => 'Setoran rutin bulanan bank sampah RT ' . $rt,
                        'petugas' => 'Petugas Bank Sampah RW',
                        'verified_by' => $adminRw ? $adminRw->id : null,
                        'verified_at' => $tglTx->addHours(1),
                    ]);
                }
            }
        }

        // 5. Seed transaksi bulan berjalan (November 2026) dengan variasi status:
        // Ada transaksi Diterima, Menunggu Konfirmasi, dan Ditolak
        $novPending = BankSampah::where('status', 'menunggu')->count();
        if ($novPending === 0) {
            $wargaPending = $wargas->first();
            BankSampah::create([
                'nomor_transaksi' => 'BS-202611-' . str_pad($txNumberCounter++, 4, '0', STR_PAD_LEFT),
                'rt_number' => $wargaPending ? $wargaPending->rt_number : '006',
                'warga_id' => $wargaPending ? $wargaPending->id : null,
                'nama_penyetor' => $wargaPending ? $wargaPending->nama : 'Aisha Gunawan',
                'tanggal_setor' => Carbon::now()->subDays(1)->toDateString(),
                'kategori' => 'plastik',
                'berat_kg' => 12.0,
                'satuan' => 'Kg',
                'harga_per_satuan' => 3500,
                'nilai_rupiah' => 42000,
                'status' => 'menunggu',
                'catatan' => 'Penyetoran mandiri oleh warga, menunggu verifikasi timbangan petugas.',
                'petugas' => 'Admin RT ' . ($wargaPending ? $wargaPending->rt_number : '006'),
            ]);

            // Transaksi ditolak contoh
            BankSampah::create([
                'nomor_transaksi' => 'BS-202611-' . str_pad($txNumberCounter++, 4, '0', STR_PAD_LEFT),
                'rt_number' => '008',
                'warga_id' => $wargas->where('rt_number', '008')->first()?->id,
                'nama_penyetor' => $wargas->where('rt_number', '008')->first()?->nama ?: 'Alya Kusuma',
                'tanggal_setor' => Carbon::now()->subDays(3)->toDateString(),
                'kategori' => 'kertas',
                'berat_kg' => 8.0,
                'satuan' => 'Kg',
                'harga_per_satuan' => 2350,
                'nilai_rupiah' => 18800,
                'status' => 'ditolak',
                'alasan_penolakan' => 'Sampah kardus dan kertas basah kuyup terkena hujan lebat sehingga hancur dan tidak dapat diterima oleh pengepul.',
                'catatan' => 'Mohon dikeringkan terlebih dahulu sebelum disetor ulang.',
                'petugas' => 'Petugas Bank Sampah RW',
                'verified_by' => $adminRw ? $adminRw->id : null,
                'verified_at' => Carbon::now()->subDays(2),
            ]);
        }

        // 6. Seed sample penarikan saldo tabungan (Withdrawal)
        if (BankSampahPenarikan::count() === 0) {
            $wargaWithdraw = Warga::where('rt_number', '006')->first() ?: $wargas->first();
            if ($wargaWithdraw) {
                BankSampahPenarikan::create([
                    'nomor_penarikan' => 'WD-202611-0001',
                    'warga_id' => $wargaWithdraw->id,
                    'user_id' => null,
                    'rt_number' => $wargaWithdraw->rt_number,
                    'tanggal_penarikan' => Carbon::now()->subDays(2)->toDateString(),
                    'jumlah_penarikan' => 50000,
                    'metode_penarikan' => 'tunai',
                    'petugas' => 'Bendahara Bank Sampah RW',
                    'catatan' => 'Pencairan tabungan bank sampah tunai untuk kebutuhan rumah tangga.',
                    'status' => 'selesai',
                ]);
            }
        }
    }
}
