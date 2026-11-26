<?php

namespace Database\Seeders;

use App\Models\FasilitasUmum;
use App\Models\PemeriksaanFasilitas;
use App\Models\PeminjamanFasilitas;
use App\Models\PeminjamanHistory;
use App\Models\User;
use App\Models\Warga;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FasilitasUmumSeeder extends Seeder
{
    public function run(): void
    {
        $adminRw = User::where('email', 'adminrw@rt.com')->first() ?: User::first();
        $adminRt6 = User::where('email', 'adminrt6@rt.com')->first();
        $adminRt7 = User::where('email', 'adminrt7@rt.com')->first();
        $wargaUsers = User::where('role', 'warga')->take(10)->get();

        // =========================================================================
        // 1. SEED / UPDATE FASILITAS UMUM (5 Fasilitas Strategis RW 018)
        // =========================================================================
        $fasilitasList = [
            [
                'nama' => 'Balai Warga Utama RW 018',
                'kategori' => 'Gedung / Aula',
                'deskripsi' => 'Gedung aula serbaguna ber-AC dengan panggung utama untuk rapat akbar RW, resepsi pernikahan warga, penyuluhan kesehatan, dan senam bersama.',
                'lokasi' => 'Pusat Fasilitas RW 018 (Depan Lapangan Utama)',
                'kapasitas' => 150,
                'status_operasional' => 'tersedia',
                'kondisi_fisik' => 'Baik',
                'daftar_perlengkapan' => 'Sound system 2000W, 4 wireless mic, proyektor & screen 120 inch, 4 unit AC 2PK, 120 kursi futura, 10 meja lipat banquet, podium sambutan.',
                'jam_operasional' => '07:00 - 22:00 WIB',
                'aturan_penggunaan' => "1. Menjaga kebersihan dan ketertiban aula selama acara.\n2. Dilarang merokok dan menyalakan kembang api di dalam aula.\n3. Acara musik/pengeras suara maksimal sampai pukul 22:00 WIB.\n4. Sampah sisa acara wajib dimasukkan ke kantong sampah dan diletakkan di TPS luar.",
                'biaya_sewa' => 150000,
                'uang_jaminan' => 100000,
                'penanggung_jawab' => 'Seksi Sarpras & Pembangunan RW 018',
                'kontak_pj' => '0812-8888-0181',
                'foto' => null,
            ],
            [
                'nama' => 'Lapangan Olahraga Serbaguna',
                'kategori' => 'Lapangan Olahraga',
                'deskripsi' => 'Lapangan outdoor berlapis flexi-pave untuk futsal, bulutangkis, bola voli, basket 3on3, dan tempat latihan pencak silat pemuda lingkungan.',
                'lokasi' => 'Jl. Mawar Samping Taman Blok A',
                'kapasitas' => 60,
                'status_operasional' => 'tersedia',
                'kondisi_fisik' => 'Baik',
                'daftar_perlengkapan' => 'Tiang & jaring bulutangkis portabel, tiang & net voli, gawang futsal mini (2 unit), ring basket, lampu sorot LED lapangan 4 titik.',
                'jam_operasional' => '06:00 - 21:00 WIB',
                'aturan_penggunaan' => "1. Wajib mengenakan sepatu olahraga berpasangan sol karet.\n2. Matikan lampu sorot lapangan setelah selesai bermain jika bermain malam.\n3. Dilarang merusak jaring/net dan fasilitas tiang.",
                'biaya_sewa' => 0,
                'uang_jaminan' => 0,
                'penanggung_jawab' => 'Karang Taruna Unit RW 018',
                'kontak_pj' => '0813-7777-0182',
                'foto' => null,
            ],
            [
                'nama' => 'Set Tenda Terop & Kursi Lipat RW (80 Unit)',
                'kategori' => 'Inventaris Acara',
                'deskripsi' => 'Paket tenda kanopi terop outdoor ukuran 4x6 meter (2 unit) beserta 80 unit kursi lipat stainless steel untuk keperluan tasyakuran atau hajatan warga di rumah.',
                'lokasi' => 'Gudang Inventaris Balai RW 018',
                'kapasitas' => 80,
                'status_operasional' => 'tersedia',
                'kondisi_fisik' => 'Baik',
                'daftar_perlengkapan' => '2 set rangka besi terop 4x6m, 2 terpal pelindung anti-air, 80 kursi lipat Chitose, 4 meja prasmanan lipat.',
                'jam_operasional' => 'Peminjaman harian (24 jam)',
                'aturan_penggunaan' => "1. Wajib memeriksa kelengkapan saat serah terima di gudang RW.\n2. Warga bertanggung jawab atas kebersihan kain terpal dan keutuhan kursi.\n3. Pengembalian maksimal 1x24 jam setelah acara selesai.",
                'biaya_sewa' => 50000,
                'uang_jaminan' => 50000,
                'penanggung_jawab' => 'Pengurus Inventaris RW 018',
                'kontak_pj' => '0819-6666-0183',
                'foto' => null,
            ],
            [
                'nama' => 'Balai Posyandu & Ruang Pertemuan RT 007',
                'kategori' => 'Gedung / Aula',
                'deskripsi' => 'Ruang pertemuan semi-indoor berpendingin kipas angin gantung untuk kegiatan posyandu balita/lansia, senam ibu-ibu PKK, dan arisan warga.',
                'lokasi' => 'Jl. Melati Blok B Dekat Taman RT 007',
                'kapasitas' => 45,
                'status_operasional' => 'tersedia',
                'kondisi_fisik' => 'Baik',
                'daftar_perlengkapan' => 'Timbangan bayi & anak posyandu, alat ukur tinggi badan, 40 kursi plastik napolly, 2 meja pendaftaran, whiteboard besar, dispenser air minum.',
                'jam_operasional' => '08:00 - 18:00 WIB',
                'aturan_penggunaan' => "1. Prioritas utama untuk pelayanan kesehatan balita dan lansia jadwal rutin posyandu.\n2. Menjaga kebersihan dan membuang popok bayi di tong sampah khusus di luar ruangan.",
                'biaya_sewa' => 0,
                'uang_jaminan' => 0,
                'penanggung_jawab' => 'Pengurus Posyandu & RT 007',
                'kontak_pj' => '0821-5555-0184',
                'foto' => null,
            ],
            [
                'nama' => 'Gazebo Komunitas & Taman Terbuka Hijau',
                'kategori' => 'Area Terbuka',
                'deskripsi' => 'Taman hijau asri dilengkapi gazebo kayu jati seluas 5x5 meter untuk diskusi santai pemuda, bimbingan belajar anak warga, dan rapat pengurus lingkungan.',
                'lokasi' => 'Taman Asri RW 018 Blok C',
                'kapasitas' => 25,
                'status_operasional' => 'tersedia',
                'kondisi_fisik' => 'Baik',
                'daftar_perlengkapan' => 'Gazebo kayu beratap sirap, instalasi colokan listrik 4 lubang, penerangan lampu taman warm-white, karpet gulung lantai.',
                'jam_operasional' => '07:00 - 21:00 WIB',
                'aturan_penggunaan' => "1. Menjaga kelestarian tanaman dan tidak menginjak rumput taman hias.\n2. Wajib membawa kembali kantong sampah pribadi.\n3. Tidak menggunakan sound volume tinggi yang mengganggu ketenangan rumah sekitar.",
                'biaya_sewa' => 0,
                'uang_jaminan' => 0,
                'penanggung_jawab' => 'Seksi Kebersihan & Lingkungan RW 018',
                'kontak_pj' => '0857-4444-0185',
                'foto' => null,
            ],
        ];

        $fasilitasModels = [];
        foreach ($fasilitasList as $fd) {
            $fasilitasModels[$fd['nama']] = FasilitasUmum::updateOrCreate(
                ['nama' => $fd['nama']],
                $fd
            );
        }

        // =========================================================================
        // 2. SEED REALISTIC PEMINJAMAN RECORDS
        // =========================================================================
        $fBalai = $fasilitasModels['Balai Warga Utama RW 018'];
        $fLap = $fasilitasModels['Lapangan Olahraga Serbaguna'];
        $fTenda = $fasilitasModels['Set Tenda Terop & Kursi Lipat RW (80 Unit)'];
        $fPosyandu = $fasilitasModels['Balai Posyandu & Ruang Pertemuan RT 007'];

        $sampleBookings = [
            // 1. Selesai (Past Month)
            [
                'nomor_pengajuan' => 'PINJAM-202610-0001',
                'fasilitas_id' => $fBalai->id,
                'nama_peminjam' => 'Adrian Pradana',
                'nik_peminjam' => '3175081205900001',
                'rt_number' => '006',
                'nomor_kontak' => '0812-3456-7890',
                'tujuan' => 'Rapat Pleno Panitia Perayaan Sumpah Pemuda RW 018',
                'jumlah_peserta' => 60,
                'waktu_mulai' => Carbon::parse('2026-10-25 19:00:00'),
                'waktu_selesai' => Carbon::parse('2026-10-25 22:00:00'),
                'status' => 'selesai',
                'catatan_pengelola' => 'Disetujui. Digunakan untuk kegiatan kepemudaan RW.',
                'biaya_sewa' => 0,
                'uang_jaminan' => 0,
                'status_pembayaran' => 'gratis',
                'disetujui_oleh' => $adminRw->id,
                'inspection' => [
                    'petugas_nama' => 'Dirga Pradipta (Admin RW)',
                    'kondisi_sebelum' => 'Baik',
                    'catatan_sebelum' => 'Aula bersih, AC dingin, sound system berfungsi normal.',
                    'perlengkapan_keluar' => 'Sound system, 2 mic wireless, proyektor, 60 kursi.',
                    'kondisi_sesudah' => 'Baik',
                    'catatan_sesudah' => 'Aula ditinggalkan dalam keadaan bersih, lampu dan AC dimatikan tepat waktu.',
                    'perlengkapan_kembali' => 'Lengkap 100%, 2 mic dan remote AC dikembalikan utuh.',
                    'ada_kerusakan' => false,
                ],
            ],
            // 2. Selesai (Last Week)
            [
                'nomor_pengajuan' => 'PINJAM-202611-0002',
                'fasilitas_id' => $fLap->id,
                'nama_peminjam' => 'Bastian Sucipto',
                'nik_peminjam' => '3175081205900007',
                'rt_number' => '009',
                'nomor_kontak' => '0813-8899-7711',
                'tujuan' => 'Turnamen Futsal Persahabatan Antar-RT Seri 1',
                'jumlah_peserta' => 45,
                'waktu_mulai' => Carbon::now()->subDays(6)->setTime(16, 0),
                'waktu_selesai' => Carbon::now()->subDays(6)->setTime(18, 30),
                'status' => 'selesai',
                'catatan_pengelola' => 'Disetujui. Jaga sportivitas dan buang sampah botol minuman di tong.',
                'biaya_sewa' => 0,
                'uang_jaminan' => 0,
                'status_pembayaran' => 'gratis',
                'disetujui_oleh' => $adminRw->id,
                'inspection' => [
                    'petugas_nama' => 'Rian Sucipto (Admin RT 009)',
                    'kondisi_sebelum' => 'Baik',
                    'catatan_sebelum' => 'Lapangan bersih, tiang gawang terpasang kokoh.',
                    'perlengkapan_keluar' => 'Gawang futsal mini 2 unit, net cadangan.',
                    'kondisi_sesudah' => 'Baik',
                    'catatan_sesudah' => 'Kegiatan turnamen berjalan lancar tanpa insiden.',
                    'perlengkapan_kembali' => 'Gawang dan net kembali lengkap.',
                    'ada_kerusakan' => false,
                ],
            ],
            // 3. Sedang Digunakan (Hari Ini)
            [
                'nomor_pengajuan' => 'PINJAM-202611-0003',
                'fasilitas_id' => $fPosyandu->id,
                'nama_peminjam' => 'Aisha Gunawan',
                'nik_peminjam' => '3175081205900003',
                'rt_number' => '007',
                'nomor_kontak' => '0812-9988-7766',
                'tujuan' => 'Layanan Rutin Posyandu Balita & Imunisasi Bulanan RT 007',
                'jumlah_peserta' => 35,
                'waktu_mulai' => Carbon::today()->setTime(8, 30),
                'waktu_selesai' => Carbon::today()->setTime(12, 0),
                'status' => 'digunakan',
                'catatan_pengelola' => 'Disetujui. Tim medis puskesmas kelurahan hadir mendampingi.',
                'biaya_sewa' => 0,
                'uang_jaminan' => 0,
                'status_pembayaran' => 'gratis',
                'disetujui_oleh' => $adminRt7 ? $adminRt7->id : $adminRw->id,
                'inspection' => [
                    'petugas_nama' => 'Gavin Gunawan (Admin RT 007)',
                    'kondisi_sebelum' => 'Baik',
                    'catatan_sebelum' => 'Ruangan posyandu disinfeksi sebelum pelayanan dimulai.',
                    'perlengkapan_keluar' => 'Timbangan bayi, pengukur tinggi, 35 kursi.',
                    'kondisi_sesudah' => null,
                    'catatan_sesudah' => null,
                    'perlengkapan_kembali' => null,
                    'ada_kerusakan' => false,
                ],
            ],
            // 4. Disetujui (Upcoming 2 Days Ahead)
            [
                'nomor_pengajuan' => 'PINJAM-202611-0004',
                'fasilitas_id' => $fBalai->id,
                'nama_peminjam' => 'Angga Wibowo',
                'nik_peminjam' => '3175081205900004',
                'rt_number' => '008',
                'nomor_kontak' => '0878-1122-3344',
                'tujuan' => 'Syukuran Aqiqah & Pengajian Keluarga Besar Warga RT 008',
                'jumlah_peserta' => 90,
                'waktu_mulai' => Carbon::now()->addDays(2)->setTime(13, 0),
                'waktu_selesai' => Carbon::now()->addDays(2)->setTime(17, 0),
                'status' => 'disetujui',
                'catatan_pengelola' => 'Disetujui. Biaya sewa dan uang jaminan telah diverifikasi lunas oleh pengurus kas RW.',
                'biaya_sewa' => 150000,
                'uang_jaminan' => 100000,
                'status_pembayaran' => 'lunas',
                'disetujui_oleh' => $adminRw->id,
            ],
            // 5. Menunggu Persetujuan (Upcoming 5 Days Ahead)
            [
                'nomor_pengajuan' => 'PINJAM-202611-0005',
                'fasilitas_id' => $fTenda->id,
                'nama_peminjam' => 'Bagas Kusumah',
                'nik_peminjam' => '3175081205900010',
                'rt_number' => '010',
                'nomor_kontak' => '0856-7788-9900',
                'tujuan' => 'Peminjaman Tenda Terop & Kursi untuk Khitanan Anak',
                'jumlah_peserta' => 60,
                'waktu_mulai' => Carbon::now()->addDays(5)->setTime(8, 0),
                'waktu_selesai' => Carbon::now()->addDays(6)->setTime(12, 0),
                'status' => 'menunggu',
                'catatan_pengelola' => null,
                'biaya_sewa' => 50000,
                'uang_jaminan' => 50000,
                'status_pembayaran' => 'menunggu_pembayaran',
                'disetujui_oleh' => null,
            ],
            // 6. Ditolak (Bentrok / Kapasitas)
            [
                'nomor_pengajuan' => 'PINJAM-202611-0006',
                'fasilitas_id' => $fBalai->id,
                'nama_peminjam' => 'Aldebaran Siregar',
                'nik_peminjam' => '3175081205900005',
                'rt_number' => '006',
                'nomor_kontak' => '0812-4455-6677',
                'tujuan' => 'Latihan Band Pemuda Malam Hari',
                'jumlah_peserta' => 15,
                'waktu_mulai' => Carbon::now()->addDays(2)->setTime(14, 0),
                'waktu_selesai' => Carbon::now()->addDays(2)->setTime(16, 0),
                'status' => 'ditolak',
                'catatan_pengelola' => 'Mohon maaf jadwal bentrok dengan acara Aqiqah warga yang sudah disetujui lebih dulu.',
                'alasan_penolakan' => 'Jadwal bertabrakan dengan agenda pengajian aqiqah yang telah disetujui pada tanggal dan jam yang sama.',
                'biaya_sewa' => 0,
                'uang_jaminan' => 0,
                'status_pembayaran' => 'gratis',
                'disetujui_oleh' => $adminRw->id,
            ],
        ];

        foreach ($sampleBookings as $b) {
            $inspectionData = $b['inspection'] ?? null;
            unset($b['inspection']);

            $creatorUser = User::where('name', $b['nama_peminjam'])->first() ?: $adminRw;

            $booking = PeminjamanFasilitas::updateOrCreate(
                ['nomor_pengajuan' => $b['nomor_pengajuan']],
                array_merge($b, ['user_id' => $creatorUser->id])
            );

            // Record initial history
            PeminjamanHistory::firstOrCreate(
                [
                    'peminjaman_id' => $booking->id,
                    'status_baru' => 'menunggu',
                ],
                [
                    'user_id' => $creatorUser->id,
                    'status_sebelumnya' => null,
                    'keterangan' => 'Pengajuan peminjaman fasilitas diajukan oleh ' . $b['nama_peminjam'],
                    'petugas' => $b['nama_peminjam'],
                ]
            );

            // Record subsequent status history if not pending
            if ($booking->status !== 'menunggu') {
                PeminjamanHistory::firstOrCreate(
                    [
                        'peminjaman_id' => $booking->id,
                        'status_baru' => $booking->status,
                    ],
                    [
                        'user_id' => $adminRw->id,
                        'status_sebelumnya' => 'menunggu',
                        'keterangan' => $booking->catatan_pengelola ?: 'Status diperbarui menjadi ' . ucfirst($booking->status),
                        'petugas' => $adminRw->name,
                    ]
                );
            }

            // Create inspection record if defined
            if ($inspectionData) {
                PemeriksaanFasilitas::updateOrCreate(
                    ['peminjaman_id' => $booking->id],
                    array_merge($inspectionData, [
                        'fasilitas_id' => $booking->fasilitas_id,
                        'tanggal_cek_awal' => $booking->waktu_mulai->copy()->subHours(1),
                        'tanggal_cek_akhir' => $booking->status === 'selesai' ? $booking->waktu_selesai->copy()->addMinutes(30) : null,
                    ])
                );
            }
        }
    }
}
