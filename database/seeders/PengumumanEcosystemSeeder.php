<?php

namespace Database\Seeders;

use App\Models\AgendaRapat;
use App\Models\FinanceNotification;
use App\Models\Pengumuman;
use App\Models\PengumumanRead;
use App\Models\PengumumanRsvp;
use App\Models\User;
use App\Models\Warga;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PengumumanEcosystemSeeder extends Seeder
{
    public function run(): void
    {
        $adminRw = User::where('role', 'admin')->where('rt_number', '000')->first() ?? User::where('role', 'admin')->first();
        $adminRt7 = User::where('role', 'admin')->where('rt_number', '007')->first() ?? $adminRw;
        $wargaUsers = User::where('role', 'warga')->get();

        if (!$adminRw) return;

        // 1. Darurat: Tanggul Saluran & Kewaspadaan Cuaca Ekstrem
        $p1 = Pengumuman::updateOrCreate(
            ['title' => 'PEMBERITAHUAN MENDESAK: Kewaspadaan Cuaca Ekstrem & Patroli Saluran Air RW 018'],
            [
                'content' => "Berdasarkan peringatan dini BMKG dan arahan BPBD mengenai potensi hujan intensitas tinggi disertai angin kencang di wilayah Bandung Raya selama 3 hari ke depan, Pengurus RW 018 mengimbau seluruh warga untuk meningkatkan kewaspadaan:\n\n1. Pastikan saluran air dan talang di sekitar hunian masing-masing bebas dari sumbatan sampah atau dedaunan kering.\n2. Hindari memarkir kendaraan roda 4 maupun roda 2 di bawah pohon rindang atau tiang reklame yang berpotensi rawan roboh.\n3. Tim Keamanan & Satgas Siaga Bencana RW 018 telah disiagakan 24 jam untuk memantau ketinggian debit saluran pembuangan utama.\n4. Apabila terjadi genangan, pohon tumbang, atau kabel listrik terputus, segera hubungi Pos Komando RW 018 via Tombol Darurat aplikasi NexaNest atau kontak darurat di nomor 0812-3456-7890.",
                'kategori' => 'darurat',
                'prioritas' => 'darurat',
                'target_rt' => 'all',
                'created_by' => $adminRw->id,
                'is_pinned' => true,
                'requires_rsvp' => false,
                'rsvp_type' => 'konfirmasi_baca',
                'status' => 'published',
                'published_at' => Carbon::now()->subHours(2),
                'tanggal_berakhir' => Carbon::now()->addDays(5)->toDateString(),
                'is_kegiatan' => false,
            ]
        );

        // 2. Kegiatan: Kerja Bakti Bersih Lingkungan & Normalisasi Drainase
        $agenda1 = AgendaRapat::updateOrCreate(
            ['judul' => 'Kerja Bakti Akbar Bersih Lingkungan & Normalisasi Drainase RW 018'],
            [
                'deskripsi' => 'Kerja bakti serentak seluruh warga RW 018 untuk pembersihan saluran air, selokan, perantingan pohon rapuh, dan pengangkutan sampah anorganik.',
                'tanggal_waktu' => Carbon::now()->addDays(4)->setTime(7, 0),
                'waktu_selesai' => Carbon::now()->addDays(4)->setTime(11, 30),
                'lokasi' => 'Balai Warga & Saluran Air RT 006 - RT 010',
                'rt_number' => null,
                'jenis_rapat' => 'sosialisasi_warga',
                'penyelenggara' => 'Pengurus RW 018 & Seluruh Ketua RT',
                'target_peserta' => 'Seluruh Kepala Keluarga RW 018',
                'status' => 'terjadwal',
                'created_by' => $adminRw->id,
            ]
        );

        $p2 = Pengumuman::updateOrCreate(
            ['title' => 'Kerja Bakti Akbar Bersih Lingkungan & Normalisasi Drainase Serentak RW 018'],
            [
                'content' => "Menindaklanjuti program Smart & Green Community serta persiapan menghadapi musim hujan lebat, Pengurus RW 018 bersama pengurus RT 006 s.d. RT 010 mengajak seluruh warga untuk hadir dalam kegiatan Kerja Bakti Akbar.\n\nTitik Fokus Pembersihan:\n- Pengerukan sedimen dan lumpur saluran drainase utama.\n- Pemangkasan dahan pohon yang menghalangi penerangan jalan umum.\n- Pemilahan sampah anorganik daur ulang untuk langsung disetorkan ke Bank Sampah RW.\n\nPerlengkapan yang disarankan dibawa: cangkul kecil, sarung tangan karet, dan sapu lidi. Pengurus menyediakan kantong sampah tebal, konsumsi pagi, dan armada pengangkut sampah.\n\nMohon konfirmasi keikutsertaan Anda melalui formulir RSVP di bawah ini demi kelancaran logistik dan konsumsi warga.",
                'kategori' => 'kegiatan',
                'prioritas' => 'kegiatan',
                'target_rt' => 'all',
                'created_by' => $adminRw->id,
                'is_pinned' => true,
                'requires_rsvp' => true,
                'rsvp_type' => 'partisipasi',
                'status' => 'published',
                'published_at' => Carbon::now()->subDay(),
                'tanggal_berakhir' => Carbon::now()->addDays(4)->toDateString(),
                'is_kegiatan' => true,
                'kegiatan_tanggal' => Carbon::now()->addDays(4)->toDateString(),
                'kegiatan_waktu_mulai' => '07:00',
                'kegiatan_waktu_selesai' => '11:30',
                'kegiatan_lokasi' => 'Balai Warga RW 018 & Saluran Air RT 006 - 010',
                'agenda_rapat_id' => $agenda1->id,
            ]
        );

        // 3. Keuangan: Laporan Transparansi Kas & Penyesuaian Iuran
        $p3 = Pengumuman::updateOrCreate(
            ['title' => 'Publikasi Transparansi Laporan Kas Lingkungan & Hasil Audit Keuangan Periode Berjalan'],
            [
                'content' => "Salam sejahtera bagi seluruh warga RW 018.\n\nSebagai wujud komitmen keterbukaan tata kelola keuangan komunitas, Bendahara RW 018 telah merilis ringkasan realisasi penerimaan iuran warga, alokasi biaya operasional kebersihan sampah, dan biaya perawatan sistem CCTV lingkungan.\n\nWarga dapat memeriksa kuitansi elektronik dan rincian transaksi kas masuk-keluar secara transparan melalui menu Keuangan di portal NexaNest. Apabila terdapat catatan atau masukan atas alokasi anggaran, warga dipersilakan mengisi form konfirmasi atau menyampaikan aspirasi pada menu Pengaduan.",
                'kategori' => 'keuangan',
                'prioritas' => 'penting',
                'target_rt' => 'all',
                'created_by' => $adminRw->id,
                'is_pinned' => false,
                'requires_rsvp' => true,
                'rsvp_type' => 'konfirmasi_baca',
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(2),
                'tanggal_berakhir' => Carbon::now()->addDays(15)->toDateString(),
                'is_kegiatan' => false,
            ]
        );

        // 4. Kesehatan: Jadwal Posyandu & Imunisasi Balita
        $p4 = Pengumuman::updateOrCreate(
            ['title' => 'Jadwal Pemeriksaan Rutin Posyandu Melati: Penimbangan Balita & Pemeriksaan Ibu Hamil'],
            [
                'content' => "Diberitahukan kepada para orang tua balita dan ibu hamil di lingkungan RW 018, kegiatan Posyandu Bulanan akan dilaksanakan dengan agenda:\n\n1. Pengukuran Antropometri (Berat Badan, Tinggi Badan, Lingkar Kepala, Lingkar Lengan) dengan alat standar Kemenkes.\n2. Pemberian Vitamin A dosis biru & merah serta obat cacing berkala.\n3. Pemeriksaan Antenatal Care (ANC) terpadu oleh Bidan Desa bagi ibu hamil.\n4. Konsultasi gizi balita untuk pencegahan risiko stunting.\n\nDiharapkan para orang tua membawa Buku KIA / Kartu Menuju Sehat (KMS) fisik atau dapat langsung memantau data KMS Digital melalui fitur Info Posyandu NexaNest.",
                'kategori' => 'kesehatan',
                'prioritas' => 'kegiatan',
                'target_rt' => 'all',
                'created_by' => $adminRw->id,
                'is_pinned' => false,
                'requires_rsvp' => true,
                'rsvp_type' => 'hadir_tidak',
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(3),
                'tanggal_berakhir' => Carbon::now()->addDays(6)->toDateString(),
                'is_kegiatan' => true,
                'kegiatan_tanggal' => Carbon::now()->addDays(6)->toDateString(),
                'kegiatan_waktu_mulai' => '08:30',
                'kegiatan_waktu_selesai' => '11:30',
                'kegiatan_lokasi' => 'Balai Posyandu & Ruang Pertemuan RT 007',
            ]
        );

        // 5. Keamanan RT 007: Pembaruan Jadwal Ronda & Portal Otomatis
        $p5 = Pengumuman::updateOrCreate(
            ['title' => 'Pemberitahuan Khusus Warga RT 007: Penerapan Jam Portal Malam & Jadwal Siskamling'],
            [
                'content' => "Disampaikan kepada seluruh kepala keluarga di lingkungan RT 007:\n\n1. Portal utama gang masuk RT 007 akan mulai ditutup setiap malam pukul 23:00 WIB dan dibuka kembali pukul 05:00 WIB demi keamanan lingkungan.\n2. Tamu dari luar lingkungan yang berkunjung melebihi pukul 22:00 WIB wajib melapor kepada petugas jaga di Pos Ronda RT 007.\n3. Jadwal giliran ronda malam warga untuk shift minggu ini dapat dilihat pada menu Ronda & Keamanan.\n\nKerja sama dan kepatuhan warga sangat kami harapkan untuk menjaga kondusivitas bersama.",
                'kategori' => 'keamanan',
                'prioritas' => 'informasi',
                'target_rt' => '007',
                'created_by' => $adminRt7->id,
                'is_pinned' => false,
                'requires_rsvp' => true,
                'rsvp_type' => 'konfirmasi_baca',
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(4),
                'tanggal_berakhir' => Carbon::now()->addDays(20)->toDateString(),
                'is_kegiatan' => false,
            ]
        );

        // 6. Sosial: Penyaluran Bantuan & Donasi Warga
        $p6 = Pengumuman::updateOrCreate(
            ['title' => 'Gerakan Solidaritas Warga RW 018: Donasi Peduli Korban Banjir Wilayah Tetangga'],
            [
                'content' => "Menyikapi musibah banjir yang melanda saudara-saudara kita di bantaran sungai wilayah kelurahan tetangga, Seksi Sosial RW 018 membuka posko penerimaan bantuan warga:\n\nBantuan yang sangat dibutuhkan:\n- Sembako (Beras, mie instan, minyak goreng, biskuit balita).\n- Perlengkapan kebersihan (Sapu, karbol, ember, sabun).\n- Selimut, popok bayi, dan pakaian layak pakai bersih.\n\nPengumpulan bantuan difokuskan di lobi Balai Warga RW 018 hingga hari Jumat pukul 17:00 WIB.",
                'kategori' => 'sosial',
                'prioritas' => 'informasi',
                'target_rt' => 'all',
                'created_by' => $adminRw->id,
                'is_pinned' => false,
                'requires_rsvp' => false,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(5),
                'tanggal_berakhir' => Carbon::now()->addDays(7)->toDateString(),
                'is_kegiatan' => false,
            ]
        );

        // 7. Draft Announcement
        Pengumuman::updateOrCreate(
            ['title' => 'DRAFT: Rencana Sosialisasi Pemasangan Panel Surya Mandiri & Penerangan Hemat Energi'],
            [
                'content' => "Draf konsep sosialisasi efisiensi energi lingkungan dan rencana kerja sama dengan dinas terkait mengenai pengadaan panel surya komunal untuk fasilitas umum RW 018.",
                'kategori' => 'umum',
                'prioritas' => 'informasi',
                'target_rt' => 'all',
                'created_by' => $adminRw->id,
                'is_pinned' => false,
                'status' => 'draft',
                'published_at' => null,
            ]
        );

        // 8. Terjadwal (Scheduled Announcement)
        Pengumuman::updateOrCreate(
            ['title' => 'TERJADWAL: Rapat Koordinasi Akhir Tahun Evaluasi Kinerja Pengurus RT & RW 018'],
            [
                'content' => "Pemberitahuan terjadwal mengenai agenda rapat evaluasi program kerja tahun 2026 dan penyusunan kalender rencana kegiatan tahun 2027.",
                'kategori' => 'kegiatan',
                'prioritas' => 'penting',
                'target_rt' => 'all',
                'created_by' => $adminRw->id,
                'is_pinned' => false,
                'status' => 'published',
                'published_at' => Carbon::now()->addDays(3),
                'tanggal_berakhir' => Carbon::now()->addDays(14)->toDateString(),
            ]
        );

        // 9. Archived Announcement
        Pengumuman::updateOrCreate(
            ['title' => 'ARSIP: Pemberitahuan Penutupan Akses Jalan Simpang Melati Terkait Pekerjaan Saluran'],
            [
                'content' => "Pemberitahuan penutupan jalan sementara selama pekerjaan perbaikan gorong-gorong pipa utama di simpang Jl. Melati. Pekerjaan telah selesai dikerjakan dengan aman.",
                'kategori' => 'umum',
                'prioritas' => 'informasi',
                'target_rt' => 'all',
                'created_by' => $adminRw->id,
                'is_pinned' => false,
                'status' => 'published',
                'is_archived' => true,
                'archived_at' => Carbon::now()->subDays(10),
                'published_at' => Carbon::now()->subDays(25),
                'tanggal_berakhir' => Carbon::now()->subDays(10)->toDateString(),
            ]
        );

        // Seed Read Tracking Records
        foreach ($wargaUsers->take(8) as $idx => $wu) {
            PengumumanRead::updateOrCreate(
                ['pengumuman_id' => $p1->id, 'user_id' => $wu->id],
                ['read_at' => Carbon::now()->subHours(rand(1, 30))]
            );
            PengumumanRead::updateOrCreate(
                ['pengumuman_id' => $p2->id, 'user_id' => $wu->id],
                ['read_at' => Carbon::now()->subHours(rand(2, 40))]
            );
            if ($idx % 2 === 0) {
                PengumumanRead::updateOrCreate(
                    ['pengumuman_id' => $p3->id, 'user_id' => $wu->id],
                    ['read_at' => Carbon::now()->subHours(rand(5, 50))]
                );
            }
        }

        // Seed Sample RSVPs
        foreach ($wargaUsers->take(6) as $idx => $wu) {
            $w = Warga::where('user_id', $wu->id)->first();
            $resp = ($idx % 3 === 0) ? 'tidak_ikut' : 'ikut';
            PengumumanRsvp::updateOrCreate(
                ['pengumuman_id' => $p2->id, 'user_id' => $wu->id],
                [
                    'warga_id' => $w ? $w->id : null,
                    'response' => $resp,
                    'catatan' => $resp === 'ikut' ? 'Siap hadir membawa cangkul & sapu lidi dari rumah.' : 'Mohon izin ada keperluan tugas dinas luar kota.',
                    'responded_at' => Carbon::now()->subHours(rand(3, 20)),
                ]
            );

            PengumumanRsvp::updateOrCreate(
                ['pengumuman_id' => $p3->id, 'user_id' => $wu->id],
                [
                    'warga_id' => $w ? $w->id : null,
                    'response' => 'konfirmasi',
                    'catatan' => 'Laporan kas sudah dibaca dan dipahami, terima kasih pengurus.',
                    'responded_at' => Carbon::now()->subHours(rand(2, 15)),
                ]
            );
        }

        // Seed Notifications for Citizens
        foreach ($wargaUsers as $wu) {
            FinanceNotification::firstOrCreate(
                ['user_id' => $wu->id, 'link_url' => route('pengumuman.show', $p1->id)],
                [
                    'tipe' => 'pengumuman_darurat',
                    'judul' => '🚨 [DARURAT] ' . $p1->title,
                    'pesan' => 'Peringatan dini cuaca ekstrem & instruksi kesiapsiagaan seluruh warga RW 018.',
                    'is_read' => false,
                    'due_date' => $p1->tanggal_berakhir,
                    'data' => ['pengumuman_id' => $p1->id],
                ]
            );

            FinanceNotification::firstOrCreate(
                ['user_id' => $wu->id, 'link_url' => route('pengumuman.show', $p2->id)],
                [
                    'tipe' => 'pengumuman_kegiatan',
                    'judul' => '📢 [KEGIATAN] ' . $p2->title,
                    'pesan' => 'Undangan Kerja Bakti Akbar RW 018 hari Minggu. Mohon konfirmasi kehadiran Anda.',
                    'is_read' => false,
                    'due_date' => $p2->tanggal_berakhir,
                    'data' => ['pengumuman_id' => $p2->id],
                ]
            );
        }
    }
}
