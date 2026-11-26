<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Aset;
use App\Models\AsetPemeriksaan;
use App\Models\AsetDokumentasi;
use App\Models\Pengaduan;
use App\Models\PengaduanHistory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AsetDanPengaduanSeeder extends Seeder
{
    public function run()
    {
        // 1. Data Pengguna untuk Relasi
        $adminRw = User::where('role', 'admin')->where('rt_number', '000')->first() ?? User::first();
        $adminRt6 = User::where('rt_number', '006')->where('role', 'admin')->first() ?? $adminRw;
        $adminRt7 = User::where('rt_number', '007')->where('role', 'admin')->first() ?? $adminRw;
        $adminRt8 = User::where('rt_number', '008')->where('role', 'admin')->first() ?? $adminRw;
        $adminRt9 = User::where('rt_number', '009')->where('role', 'admin')->first() ?? $adminRw;
        $adminRt10 = User::where('rt_number', '010')->where('role', 'admin')->first() ?? $adminRw;

        // Ambil warga riil dari berbagai RT
        $wargasByRt = [
            '006' => User::where('role', 'warga')->where('rt_number', '006')->get(),
            '007' => User::where('role', 'warga')->where('rt_number', '007')->get(),
            '008' => User::where('role', 'warga')->where('rt_number', '008')->get(),
            '009' => User::where('role', 'warga')->where('rt_number', '009')->get(),
            '010' => User::where('role', 'warga')->where('rt_number', '010')->get(),
        ];

        // Fallback jika ada RT yang warganya kosong
        $allWargas = User::where('role', 'warga')->get();

        // 2. Data Master Aset Lingkungan RW 018
        $daftarAset = [
            // RT 006
            [
                'nomor_aset' => 'TL-018-006-012',
                'nama' => 'Tiang Lampu PJU Sakura 12',
                'kategori' => 'Tiang Listrik & Lampu Jalan',
                'rt_number' => '006',
                'rw_number' => '018',
                'lokasi' => 'Jl. Sakura Depan Blok A No. 12, RT 006',
                'latitude' => -6.914744,
                'longitude' => 107.609810,
                'kondisi' => 'dalam_perbaikan',
                'status_pemeliharaan' => 'sedang_dikerjakan',
                'tanggal_perolehan' => '2024-03-15',
                'terakhir_diperiksa' => '2026-11-10 14:30:00',
                'spesifikasi' => 'Tiang Galvanis 7 meter, Lampu LED Philips 90W Solar-Hybrid dengan photo-sensor otomatis.',
            ],
            [
                'nomor_aset' => 'DRA-018-006-001',
                'nama' => 'Saluran Drainase Utama Blok A',
                'kategori' => 'Saluran Air & Drainase',
                'rt_number' => '006',
                'rw_number' => '018',
                'lokasi' => 'Pertemuan Gorong-Gorong Jl. Sakura Ujung, RT 006',
                'latitude' => -6.914610,
                'longitude' => 107.609650,
                'kondisi' => 'baik',
                'status_pemeliharaan' => 'normal',
                'tanggal_perolehan' => '2023-08-20',
                'terakhir_diperiksa' => '2026-10-15 09:00:00',
                'spesifikasi' => 'U-Ditch Beton 60x60 cm dengan grating besi penutup jalan utama RT 006.',
            ],
            [
                'nomor_aset' => 'CCTV-018-006-001',
                'nama' => 'CCTV Pemantau Gang Sakura',
                'kategori' => 'CCTV & Keamanan',
                'rt_number' => '006',
                'rw_number' => '018',
                'lokasi' => 'Pos Keamanan Gerbang Sakura RT 006',
                'latitude' => -6.914800,
                'longitude' => 107.609900,
                'kondisi' => 'baik',
                'status_pemeliharaan' => 'normal',
                'tanggal_perolehan' => '2025-01-10',
                'terakhir_diperiksa' => '2026-11-01 10:15:00',
                'spesifikasi' => 'Hikvision 4MP IP Camera Outdoor Night Vision dengan koneksi fiber optic ke posko RW.',
            ],

            // RT 007
            [
                'nomor_aset' => 'HYD-018-007-003',
                'nama' => 'Pilar Hydrant Damkar Pos Cemara',
                'kategori' => 'Hydrant & Utilitas Air',
                'rt_number' => '007',
                'rw_number' => '018',
                'lokasi' => 'Samping Pos Ronda Gang Cemara, RT 007',
                'latitude' => -6.915230,
                'longitude' => 107.610245,
                'kondisi' => 'rusak_ringan',
                'status_pemeliharaan' => 'perlu_tindakan',
                'tanggal_perolehan' => '2023-05-12',
                'terakhir_diperiksa' => '2026-11-12 11:00:00',
                'spesifikasi' => 'Pilar Hydrant Type Two-Way 4x2.5 inch Tekanan 8 Bar, terhubung jaringan PDAM induk.',
            ],
            [
                'nomor_aset' => 'TL-018-007-005',
                'nama' => 'Tiang Lampu PJU Gang Cemara 5',
                'kategori' => 'Tiang Listrik & Lampu Jalan',
                'rt_number' => '007',
                'rw_number' => '018',
                'lokasi' => 'Jl. Cemara Blok C No. 08, RT 007',
                'latitude' => -6.915350,
                'longitude' => 107.610400,
                'kondisi' => 'baik',
                'status_pemeliharaan' => 'normal',
                'tanggal_perolehan' => '2024-04-10',
                'terakhir_diperiksa' => '2026-10-28 16:00:00',
                'spesifikasi' => 'Tiang PJU Pipa Oktagonal 6m, Lampu LED 60W hemat energi.',
            ],
            [
                'nomor_aset' => 'FAS-018-007-001',
                'nama' => 'Pos Ronda & Gardu Kamling Cemara',
                'kategori' => 'Fasilitas Umum',
                'rt_number' => '007',
                'rw_number' => '018',
                'lokasi' => 'Pojok Pertigaan Cemara - Melati RT 007',
                'latitude' => -6.915200,
                'longitude' => 107.610190,
                'kondisi' => 'baik',
                'status_pemeliharaan' => 'normal',
                'tanggal_perolehan' => '2022-11-05',
                'terakhir_diperiksa' => '2026-11-05 20:00:00',
                'spesifikasi' => 'Bangunan pos ronda permanen 3x3m, dilengkapi kentongan alarm, senter darurat, dan P3K.',
            ],

            // RT 008
            [
                'nomor_aset' => 'CCTV-018-008-005',
                'nama' => 'Tiang CCTV Balai Pertemuan RW',
                'kategori' => 'CCTV & Keamanan',
                'rt_number' => '008',
                'rw_number' => '018',
                'lokasi' => 'Pertigaan Balai Pertemuan Utama, RT 008',
                'latitude' => -6.914390,
                'longitude' => 107.611102,
                'kondisi' => 'baik',
                'status_pemeliharaan' => 'normal',
                'tanggal_perolehan' => '2024-09-01',
                'terakhir_diperiksa' => '2026-11-11 13:45:00',
                'spesifikasi' => 'PTZ Camera 360-degree rotation 4K Resolution, optical zoom 25x, koneksi NVR RW.',
            ],
            [
                'nomor_aset' => 'FAS-018-008-002',
                'nama' => 'Balai Pertemuan Serbaguna RW 018',
                'kategori' => 'Fasilitas Umum',
                'rt_number' => '008',
                'rw_number' => '018',
                'lokasi' => 'Komp. Fasum Pusat RT 008 RW 018',
                'latitude' => -6.914420,
                'longitude' => 107.611050,
                'kondisi' => 'baik',
                'status_pemeliharaan' => 'normal',
                'tanggal_perolehan' => '2021-06-15',
                'terakhir_diperiksa' => '2026-11-02 10:00:00',
                'spesifikasi' => 'Gedung pertemuan aula 15x10 meter, kapasitas 120 kursi, panggung permanen dan audio sound system.',
            ],
            [
                'nomor_aset' => 'TL-018-008-003',
                'nama' => 'PJU Tiang Ganda Lapangan RT 008',
                'kategori' => 'Tiang Listrik & Lampu Jalan',
                'rt_number' => '008',
                'rw_number' => '018',
                'lokasi' => 'Pinggir Lapangan Voli Blok D RT 008',
                'latitude' => -6.914500,
                'longitude' => 107.611250,
                'kondisi' => 'rusak_ringan',
                'status_pemeliharaan' => 'jadwal_servis',
                'tanggal_perolehan' => '2023-10-18',
                'terakhir_diperiksa' => '2026-11-09 17:00:00',
                'spesifikasi' => 'Tiang ganda lampu sorot 150W LED untuk penerangan kegiatan olahraga warga.',
            ],

            // RT 009
            [
                'nomor_aset' => 'BSK-018-009-001',
                'nama' => 'Bak Sampah Komunal Fiber 01',
                'kategori' => 'Sanitasi & Bak Sampah',
                'rt_number' => '009',
                'rw_number' => '018',
                'lokasi' => 'Depan Taman Bermain Anak Flamboyan, RT 009',
                'latitude' => -6.913880,
                'longitude' => 107.610540,
                'kondisi' => 'rusak_berat',
                'status_pemeliharaan' => 'perlu_tindakan',
                'tanggal_perolehan' => '2023-01-25',
                'terakhir_diperiksa' => '2026-11-13 08:30:00',
                'spesifikasi' => 'Bak sampah pilah 3 kompartemen (Organik, Anorganik, B3) kapasitas 660 Liter roda heavy-duty.',
            ],
            [
                'nomor_aset' => 'INF-018-009-004',
                'nama' => 'Speed Bump Karet & Marka Taman RT 009',
                'kategori' => 'Infrastruktur Lingkungan',
                'rt_number' => '009',
                'rw_number' => '018',
                'lokasi' => 'Jl. Flamboyan Depan PAUD Kasih Ibu RT 009',
                'latitude' => -6.913950,
                'longitude' => 107.610620,
                'kondisi' => 'baik',
                'status_pemeliharaan' => 'normal',
                'tanggal_perolehan' => '2024-07-14',
                'terakhir_diperiksa' => '2026-10-20 11:00:00',
                'spesifikasi' => 'Polisi tidur karet reflektif panjang 6 meter dengan baut dyna bolt pengaman anak-anak.',
            ],
            [
                'nomor_aset' => 'TL-018-009-007',
                'nama' => 'Tiang Lampu PJU Flamboyan 7',
                'kategori' => 'Tiang Listrik & Lampu Jalan',
                'rt_number' => '009',
                'rw_number' => '018',
                'lokasi' => 'Jl. Flamboyan Blok E No. 19 RT 009',
                'latitude' => -6.913800,
                'longitude' => 107.610420,
                'kondisi' => 'baik',
                'status_pemeliharaan' => 'normal',
                'tanggal_perolehan' => '2025-02-11',
                'terakhir_diperiksa' => '2026-11-04 15:20:00',
                'spesifikasi' => 'Tiang pipa PJU 6m LED 60W hemat energi solar cell otomatis.',
            ],

            // RT 010
            [
                'nomor_aset' => 'PRT-018-010-002',
                'nama' => 'Portal Gerbang Otomatis RFID Kenanga',
                'kategori' => 'Infrastruktur Lingkungan',
                'rt_number' => '010',
                'rw_number' => '018',
                'lokasi' => 'Pintu Gerbang Utama Jl. Kenanga RT 010',
                'latitude' => -6.916120,
                'longitude' => 107.612003,
                'kondisi' => 'dalam_perbaikan',
                'status_pemeliharaan' => 'sedang_dikerjakan',
                'tanggal_perolehan' => '2024-11-20',
                'terakhir_diperiksa' => '2026-11-13 16:00:00',
                'spesifikasi' => 'Boom Barrier Otomatis MX-50 dengan RFID scanner UHF 5 meter jarak deteksi, sensor loop induksi.',
            ],
            [
                'nomor_aset' => 'CCTV-018-010-008',
                'nama' => 'CCTV ANPR Gerbang Masuk Kenanga',
                'kategori' => 'CCTV & Keamanan',
                'rt_number' => '010',
                'rw_number' => '018',
                'lokasi' => 'Pilar Portal Gerbang Kenanga RT 010',
                'latitude' => -6.916135,
                'longitude' => 107.612015,
                'kondisi' => 'baik',
                'status_pemeliharaan' => 'normal',
                'tanggal_perolehan' => '2024-11-20',
                'terakhir_diperiksa' => '2026-11-08 09:30:00',
                'spesifikasi' => 'Kamera Automatic Number Plate Recognition (ANPR) pencatat plat nomor kendaraan warga dan tamu.',
            ],
            [
                'nomor_aset' => 'DRA-018-010-003',
                'nama' => 'Grating & Bak Kontrol Drainase Kenanga',
                'kategori' => 'Saluran Air & Drainase',
                'rt_number' => '010',
                'rw_number' => '018',
                'lokasi' => 'Depan Balai Warga RT 010 Jl. Kenanga Selatan',
                'latitude' => -6.916250,
                'longitude' => 107.612150,
                'kondisi' => 'baik',
                'status_pemeliharaan' => 'normal',
                'tanggal_perolehan' => '2023-09-10',
                'terakhir_diperiksa' => '2026-10-30 14:00:00',
                'spesifikasi' => 'Tutup cast iron heavy-duty penahan beban truk sampah & mobil damkar.',
            ],
        ];

        $asetMap = [];
        foreach ($daftarAset as $data) {
            $aset = Aset::updateOrCreate(
                ['nomor_aset' => $data['nomor_aset']],
                $data
            );
            $asetMap[$data['nomor_aset']] = $aset;
        }

        // 3. SEED DIVERSIFIED PENGADUAN WARGA (Berbagai RT, Warga Riil, Status, Prioritas)
        // Bersihkan data pengaduan lama yang dominan satu warga
        DB::table('pengaduan_histories')->delete();
        DB::table('aset_pemeriksaans')->delete();
        DB::table('aset_dokumentasis')->delete();
        DB::table('pengaduans')->delete();

        $daftarPengaduan = [
            // RT 006
            [
                'rt' => '006',
                'title' => 'Korsleting & Lampu Jalan Padam di Tiang TL-018-006-012',
                'content' => 'Lampu penerangan jalan umum pada tiang listrik nomor TL-018-006-012 mati total sejak semalam. Sempat terdengar suara letupan kecil di bagian atas kotak sekring tiang.',
                'aset_code' => 'TL-018-006-012',
                'category' => 'Tiang Listrik (Kelistrikan)',
                'priority' => 'tinggi',
                'status' => 'proses',
                'created_at' => Carbon::now()->subDays(2),
                'location' => 'Jl. Sakura Depan Blok A No. 12, RT 006',
                'lat' => -6.914744,
                'lng' => 107.609810,
                'petugas' => 'Budi Santoso (Teknisi Listrik RW)',
                'internal_notes' => 'Telah dikoordinasikan dengan petugas PLN sub-rayon untuk penggantian fitting ballast dan sekring.',
                'feedback_warga' => 'Laporan telah diverifikasi oleh RW. Teknisi Budi sedang melakukan penggantian modul bohlam LED dan sekring.',
                'verified' => true,
                'deadline_days' => 3,
            ],
            [
                'rt' => '006',
                'title' => 'Endapan Lumpur & Sampah di Saluran Drainase Blok A',
                'content' => 'Saluran drainase U-ditch mulai mendangkal karena tertimbun endapan lumpur setelah hujan lebat kemarin. Dikhawatirkan air meluap ke badan jalan saat hujan deras.',
                'aset_code' => 'DRA-018-006-001',
                'category' => 'Saluran Air (Drainase)',
                'priority' => 'sedang',
                'status' => 'selesai',
                'created_at' => Carbon::now()->subDays(12),
                'location' => 'Pertemuan Gorong-Gorong Jl. Sakura Ujung, RT 006',
                'lat' => -6.914610,
                'lng' => 107.609650,
                'petugas' => 'Regu Kerja Bakti RT 006 & Petugas Kebersihan',
                'internal_notes' => 'Pembersihan got selesai dilakukan secara gotong royong warga RT 006 pada Minggu pagi.',
                'feedback_warga' => 'Pengerukan lumpur dan pengangkatan sampah selesai dilakukan. Aliran air kembali lancar.',
                'verified' => true,
                'completed' => true,
            ],
            [
                'rt' => '006',
                'title' => 'Dahan Pohon Menutupi Sudut Pandang Kamera CCTV Sakura',
                'content' => 'Ranting pohon mangga di dekat tiang pos satpam tumbuh lebat menutupi lensa kamera CCTV gerbang RT 006. Mohon dibantu perapihan dahan.',
                'aset_code' => 'CCTV-018-006-001',
                'category' => 'CCTV (Keamanan)',
                'priority' => 'rendah',
                'status' => 'menunggu',
                'created_at' => Carbon::now()->subHours(6),
                'location' => 'Pos Keamanan Gerbang Sakura RT 006',
                'lat' => -6.914800,
                'lng' => 107.609900,
                'petugas' => null,
                'internal_notes' => 'Menunggu persetujuan pemilik pohon untuk pemangkasan dahan.',
                'feedback_warga' => 'Laporan telah diterima oleh sistem dan sedang masuk antrean verifikasi pengurus.',
                'verified' => false,
            ],

            // RT 007
            [
                'rt' => '007',
                'title' => 'Kebocoran Air Bersih di Pilar Hydrant HYD-018-007-003',
                'content' => 'Ada rembesan air bersih yang cukup deras keluar dari katup bawah pilar hydrant nomor HYD-018-007-003. Air menggenangi jalan masuk gang Cemara.',
                'aset_code' => 'HYD-018-007-003',
                'category' => 'Hydrant Damkar (Utilitas Air)',
                'priority' => 'darurat',
                'status' => 'ditugaskan',
                'created_at' => Carbon::now()->subDays(1),
                'location' => 'Samping Pos Ronda Gang Cemara, RT 007',
                'lat' => -6.915230,
                'lng' => 107.610245,
                'petugas' => 'Agus Priyanto (Petugas Utilitas RT 007)',
                'internal_notes' => 'Valve seal pilar hydrant pecah, perlu penggantian karet gasket 4 inch.',
                'feedback_warga' => 'Laporan diprioritaskan DARURAT. Petugas Agus telah ditugaskan untuk menutup kran isolasi dan mengganti gasket.',
                'verified' => true,
                'deadline_days' => 1,
            ],
            [
                'rt' => '007',
                'title' => 'Lampu PJU Gang Cemara 5 Berkedip-kedip',
                'content' => 'Lampu jalan di tiang TL-018-007-005 sering berkedip redup sejak 3 malam terakhir, membuat lorong gelap saat malam.',
                'aset_code' => 'TL-018-007-005',
                'category' => 'Tiang Listrik (Kelistrikan)',
                'priority' => 'sedang',
                'status' => 'selesai',
                'created_at' => Carbon::now()->subDays(18),
                'location' => 'Jl. Cemara Blok C No. 08, RT 007',
                'lat' => -6.915350,
                'lng' => 107.610400,
                'petugas' => 'Hendra Setiawan (Seksi Sarpras RT 007)',
                'internal_notes' => 'Driver power supply LED diganti baru. Kondisi lampu menyala stabil.',
                'feedback_warga' => 'Lampu PJU telah diperbaiki dan menyala normal kembali.',
                'verified' => true,
                'completed' => true,
            ],
            [
                'rt' => '007',
                'title' => 'Cat Tembok Pos Ronda Cemara Kusam dan Ada Coretan Liar',
                'content' => 'Dinding gardu pos kamling RT 007 terlihat kusam dan terdapat coretan spidol liar oleh anak-anak luar wilayah.',
                'aset_code' => 'FAS-018-007-001',
                'category' => 'Fasilitas Umum',
                'priority' => 'rendah',
                'status' => 'diverifikasi',
                'created_at' => Carbon::now()->subDays(4),
                'location' => 'Pojok Pertigaan Cemara - Melati RT 007',
                'lat' => -6.915200,
                'lng' => 107.610190,
                'petugas' => 'Seksi Lingkungan RT 007',
                'internal_notes' => 'Akan diagendakan pengecatan ulang saat kerja bakti bulanan RT.',
                'feedback_warga' => 'Laporan telah diverifikasi oleh Admin RT 007 dan dimasukkan ke jadwal kerja bakti terdekat.',
                'verified' => true,
            ],

            // RT 008
            [
                'rt' => '008',
                'title' => 'Kamera CCTV Keamanan Mati di Tiang CCTV-018-008-005',
                'content' => 'Tampilan feed CCTV keamanan lingkungan pada tiang nomor CCTV-018-008-005 terputus dan tidak mengirimkan gambar sejak tadi siang. Mohon dicek koneksi kabel LAN atau adaptornya.',
                'aset_code' => 'CCTV-018-008-005',
                'category' => 'CCTV (Keamanan)',
                'priority' => 'tinggi',
                'status' => 'selesai',
                'created_at' => Carbon::now()->subDays(7),
                'location' => 'Pertigaan Balai Pertemuan Utama, RT 008',
                'lat' => -6.914390,
                'lng' => 107.611102,
                'petugas' => 'Dani Kurnia (Teknisi IT / Jaringan RW)',
                'internal_notes' => 'Port switch PoE di kotak outdoor longgar terkena angin kencang. Sudah dikencangkan dan tahan cuaca.',
                'feedback_warga' => 'Koneksi kamera CCTV telah dipulihkan. Video feed kembali aktif di monitor posko RW.',
                'verified' => true,
                'completed' => true,
            ],
            [
                'rt' => '008',
                'title' => 'Kran Air Tempat Wudhu / Cuci Tangan Balai Warga Bocor',
                'content' => 'Kran wastafel samping pintu masuk aula balai warga dol dan air menetes terus menerus sehingga tandon cepat habis.',
                'aset_code' => 'FAS-018-008-002',
                'category' => 'Fasilitas Umum',
                'priority' => 'sedang',
                'status' => 'proses',
                'created_at' => Carbon::now()->subDays(3),
                'location' => 'Komp. Fasum Pusat RT 008 RW 018',
                'lat' => -6.914420,
                'lng' => 107.611050,
                'petugas' => 'Rizky Pramudya (Pengurus RT 008)',
                'internal_notes' => 'Kran stainless telah dibeli dari kas fasum, proses pemasangan hari ini.',
                'feedback_warga' => 'Penggantian kran wastafel sedang dalam pengerjaan oleh petugas fasum.',
                'verified' => true,
                'deadline_days' => 2,
            ],
            [
                'rt' => '008',
                'title' => 'Lampu Sorot Lapangan Voli Mati Sebelah',
                'content' => 'Satu unit lampu sorot di tiang ganda lapangan olahraga mati, sehingga lapangan agak remang untuk latihan malam anak muda.',
                'aset_code' => 'TL-018-008-003',
                'category' => 'Tiang Listrik (Kelistrikan)',
                'priority' => 'sedang',
                'status' => 'ditugaskan',
                'created_at' => Carbon::now()->subDays(5),
                'location' => 'Pinggir Lapangan Voli Blok D RT 008',
                'lat' => -6.914500,
                'lng' => 107.611250,
                'petugas' => 'Budi Santoso (Teknisi Listrik RW)',
                'internal_notes' => 'Perlu tangga lipat tinggi untuk menaikkan teknisi.',
                'feedback_warga' => 'Penanganan telah ditugaskan ke Teknisi Listrik RW dan dijadwalkan besok sore.',
                'verified' => true,
                'deadline_days' => 3, // overdue
            ],

            // RT 009
            [
                'rt' => '009',
                'title' => 'Tutup Bak Sampah Komunal BSK-018-009-001 Pecah',
                'content' => 'Engsel pintu penutup bak sampah komunal fiber nomor BSK-018-009-001 patah akibat benturan. Sampah menjadi berserakan karena tidak bisa tertutup rapat.',
                'aset_code' => 'BSK-018-009-001',
                'category' => 'Bak Sampah (Sanitasi)',
                'priority' => 'tinggi',
                'status' => 'proses',
                'created_at' => Carbon::now()->subDays(3),
                'location' => 'Depan Taman Bermain Anak Flamboyan, RT 009',
                'lat' => -6.913880,
                'lng' => 107.610540,
                'petugas' => 'Rian Sucipto (Ketua RT 009) & Tim Pengangkut Sampah',
                'internal_notes' => 'Telah dipesan bak pengganti dari distributor fiberglass. Sementara ditutup terpal rapi.',
                'feedback_warga' => 'Sedang dilakukan pengadaan bak sampah baru. Area sementara ditutup terpal agar tidak mencemari lingkungan.',
                'verified' => true,
                'deadline_days' => 2,
            ],
            [
                'rt' => '009',
                'title' => 'Baut Pengikat Polisi Tidur Karet Depan PAUD Kendor',
                'content' => 'Baut dyna bolt pada speed bump karet depan PAUD terangkat 2 cm, berbahaya bagi pengendara motor dan anak-anak yang menyeberang.',
                'aset_code' => 'INF-018-009-004',
                'category' => 'Infrastruktur (Jalan)',
                'priority' => 'tinggi',
                'status' => 'selesai',
                'created_at' => Carbon::now()->subDays(15),
                'location' => 'Jl. Flamboyan Depan PAUD Kasih Ibu RT 009',
                'lat' => -6.913950,
                'lng' => 107.610620,
                'petugas' => 'Seksi Keamanan & Ketertiban RT 009',
                'internal_notes' => 'Baut dikencangkan ulang dan dicor semen resin pengikat agar tidak mudah kendor kembali.',
                'feedback_warga' => 'Baut polisi tidur sudah diperbaiki dan rata dengan aspal jalan.',
                'verified' => true,
                'completed' => true,
            ],

            // RT 010
            [
                'rt' => '010',
                'title' => 'Portal Gerbang Otomatis PRT-018-010-002 Macet',
                'content' => 'Motor penggerak portal otomatis pada gerbang masuk PRT-018-010-002 tidak merespon saat kartu RFID warga ditempelkan. Terpaksa gerbang dibuka secara manual.',
                'aset_code' => 'PRT-018-010-002',
                'category' => 'Portal Gate (Keamanan)',
                'priority' => 'darurat',
                'status' => 'proses',
                'created_at' => Carbon::now()->subDays(1),
                'location' => 'Pintu Gerbang Utama Jl. Kenanga RT 010',
                'lat' => -6.916120,
                'lng' => 107.612003,
                'petugas' => 'Bagas Kusumah (Admin RT 010) & Vendor Barrier Gate',
                'internal_notes' => 'Gearbox motor terganjal kerikil dan limit switch aus. Teknisi vendor sedang membongkar unit.',
                'feedback_warga' => 'Teknisi vendor portal gate sedang berada di lokasi untuk penggantian limit switch.',
                'verified' => true,
                'deadline_days' => 1,
            ],
            [
                'rt' => '010',
                'title' => 'Permintaan Rekaman CCTV Gerbang Kenanga Terkait Paket Hilang',
                'content' => 'Mohon bantuan rekaman kamera CCTV gerbang masuk pada hari Selasa jam 14.00-15.00 WIB untuk verifikasi kurir ekspedisi yang mengantar paket ke rumah No. 25.',
                'aset_code' => 'CCTV-018-010-008',
                'category' => 'CCTV (Keamanan)',
                'priority' => 'sedang',
                'status' => 'selesai',
                'created_at' => Carbon::now()->subDays(6),
                'location' => 'Pilar Portal Gerbang Kenanga RT 010',
                'lat' => -6.916135,
                'lng' => 107.612015,
                'petugas' => 'Petugas Jaga Pos Ronda RT 010',
                'internal_notes' => 'Rekaman video kurir telah diperlihatkan kepada warga bersangkutan. Paket ternyata dititipkan ke tetangga sebelah.',
                'feedback_warga' => 'Pengecekan CCTV telah selesai dilakukan bersama petugas keamanan.',
                'verified' => true,
                'completed' => true,
            ],
            [
                'rt' => '010',
                'title' => 'Laporan Penebangan Pohon Pribadi Tanpa Izin Lingkungan',
                'content' => 'Warga menanyakan tentang pohon di pekarangan rumah tetangga yang ditebang dan rantingnya menutupi trotoar jalan selama 2 hari belum dibersihkan.',
                'aset_code' => null,
                'category' => 'Ketertiban Lingkungan',
                'priority' => 'rendah',
                'status' => 'ditolak',
                'created_at' => Carbon::now()->subDays(8),
                'location' => 'Jl. Kenanga Timur No. 14 RT 010',
                'lat' => -6.916200,
                'lng' => 107.612100,
                'petugas' => 'Pengurus RT 010',
                'internal_notes' => 'Pemilik rumah sudah meminta izin dan sedang menyewa pikap untuk mengangkut sisa ranting.',
                'feedback_warga' => 'Laporan ditutup. Pemilik rumah telah menyelesaikan pengangkutan sisa ranting secara mandiri.',
                'verified' => true,
            ],
            [
                'rt' => '010',
                'title' => 'Bau Tidak Sedap dari Saluran Air Samping Balai RT 010',
                'content' => 'Saluran air depan balai tercium bau menyengat air limbah cucian yang mengendap karena kemiringan got kurang curam.',
                'aset_code' => 'DRA-018-010-003',
                'category' => 'Saluran Air (Drainase)',
                'priority' => 'sedang',
                'status' => 'menunggu',
                'created_at' => Carbon::now()->subDays(4), // Overdue karena > 3 hari belum diverifikasi
                'location' => 'Depan Balai Warga RT 010 Jl. Kenanga Selatan',
                'lat' => -6.916250,
                'lng' => 107.612150,
                'petugas' => null,
                'internal_notes' => 'Perlu pengecekan apakah ada sumbatan sampah plastik di bawah jembatan plat beton.',
                'feedback_warga' => 'Laporan telah dicatat dan menunggu giliran inspeksi.',
                'verified' => false,
            ],
        ];

        $counter = 1;
        foreach ($daftarPengaduan as $dp) {
            $rt = $dp['rt'];
            $wargaList = $wargasByRt[$rt]->isNotEmpty() ? $wargasByRt[$rt] : $allWargas;
            // Pilih pelapor secara acak dari warga RT terkait
            $pelapor = $wargaList->random();
            $aset = isset($dp['aset_code']) && isset($asetMap[$dp['aset_code']]) ? $asetMap[$dp['aset_code']] : null;

            $nomorPengaduan = sprintf('PGD-202611-%04d', $counter++);

            $pengaduan = Pengaduan::create([
                'nomor_pengaduan' => $nomorPengaduan,
                'user_id' => $pelapor->id,
                'title' => $dp['title'],
                'content' => $dp['content'],
                'status' => $dp['status'],
                'priority' => $dp['priority'],
                'barcode_code' => $dp['aset_code'] ?? null,
                'aset_id' => $aset ? $aset->id : null,
                'category' => $dp['category'],
                'rt_number' => $rt,
                'location_details' => $dp['location'],
                'latitude' => $dp['lat'],
                'longitude' => $dp['lng'],
                'foto_lampiran' => null,
                'petugas_nama' => $dp['petugas'] ?? null,
                'internal_notes' => $dp['internal_notes'] ?? null,
                'feedback_warga' => $dp['feedback_warga'] ?? null,
                'verified_at' => !empty($dp['verified']) ? $dp['created_at']->copy()->addHours(2) : null,
                'verified_by' => !empty($dp['verified']) ? $adminRw->id : null,
                'deadline_at' => isset($dp['deadline_days']) ? $dp['created_at']->copy()->addDays($dp['deadline_days']) : null,
                'completed_at' => !empty($dp['completed']) ? $dp['created_at']->copy()->addDays(2) : null,
                'created_at' => $dp['created_at'],
                'updated_at' => $dp['created_at'],
            ]);

            // Tambahkan Riwayat Pengaduan
            PengaduanHistory::create([
                'pengaduan_id' => $pengaduan->id,
                'user_id' => $pelapor->id,
                'status_dari' => null,
                'status_ke' => 'menunggu',
                'catatan' => 'Laporan pengaduan diajukan oleh warga pelapor: ' . $pelapor->name,
                'is_internal' => false,
                'created_at' => $dp['created_at'],
            ]);

            if (!empty($dp['verified'])) {
                PengaduanHistory::create([
                    'pengaduan_id' => $pengaduan->id,
                    'user_id' => $adminRw->id,
                    'status_dari' => 'menunggu',
                    'status_ke' => 'diverifikasi',
                    'catatan' => 'Laporan diverifikasi oleh Admin RW. Prioritas ditetapkan: ' . strtoupper($dp['priority']),
                    'is_internal' => false,
                    'created_at' => $dp['created_at']->copy()->addHours(2),
                ]);
            }

            if (!empty($dp['petugas'])) {
                PengaduanHistory::create([
                    'pengaduan_id' => $pengaduan->id,
                    'user_id' => $adminRw->id,
                    'status_dari' => 'diverifikasi',
                    'status_ke' => 'ditugaskan',
                    'catatan' => 'Penanganan ditugaskan kepada: ' . $dp['petugas'],
                    'is_internal' => false,
                    'created_at' => $dp['created_at']->copy()->addHours(3),
                ]);
            }

            if (in_array($dp['status'], ['proses', 'selesai'])) {
                PengaduanHistory::create([
                    'pengaduan_id' => $pengaduan->id,
                    'user_id' => $adminRw->id,
                    'status_dari' => 'ditugaskan',
                    'status_ke' => 'proses',
                    'catatan' => 'Petugas mulai melakukan investigasi lapangan dan pengerjaan perbaikan.',
                    'is_internal' => false,
                    'created_at' => $dp['created_at']->copy()->addHours(5),
                ]);
            }

            if ($dp['status'] === 'selesai') {
                PengaduanHistory::create([
                    'pengaduan_id' => $pengaduan->id,
                    'user_id' => $adminRw->id,
                    'status_dari' => 'proses',
                    'status_ke' => 'selesai',
                    'catatan' => 'Perbaikan dan penanganan keluhan dinyatakan selesai dengan hasil baik.',
                    'is_internal' => false,
                    'created_at' => $dp['created_at']->copy()->addDays(2),
                ]);
            } elseif ($dp['status'] === 'ditolak') {
                PengaduanHistory::create([
                    'pengaduan_id' => $pengaduan->id,
                    'user_id' => $adminRw->id,
                    'status_dari' => 'menunggu',
                    'status_ke' => 'ditolak',
                    'catatan' => 'Laporan ditolak / ditutup: Bukan wewenang fasilitas publik RW.',
                    'is_internal' => false,
                    'created_at' => $dp['created_at']->copy()->addHours(4),
                ]);
            }

            // Tambahkan Riwayat Pemeriksaan Aset jika terhubung aset
            if ($aset) {
                AsetPemeriksaan::create([
                    'aset_id' => $aset->id,
                    'user_id' => $adminRw->id,
                    'pengaduan_id' => $pengaduan->id,
                    'jenis_kegiatan' => $dp['status'] === 'selesai' ? 'perbaikan_kerusakan' : 'inspeksi_pengaduan',
                    'tanggal' => $dp['created_at']->copy()->addHours(4),
                    'kondisi_sebelum' => $aset->kondisi,
                    'kondisi_setelah' => $dp['status'] === 'selesai' ? 'baik' : $aset->kondisi,
                    'catatan' => 'Tindak lanjut laporan ' . $pengaduan->nomor_pengaduan . ': ' . ($dp['internal_notes'] ?? 'Inspeksi fisik aset.'),
                    'biaya' => $dp['status'] === 'selesai' ? 150000 : 0,
                    'petugas_nama' => $dp['petugas'] ?? 'Tim Satgas RW 018',
                    'created_at' => $dp['created_at']->copy()->addHours(4),
                ]);

                // Tambahkan Dokumentasi Aset
                AsetDokumentasi::create([
                    'aset_id' => $aset->id,
                    'pengaduan_id' => $pengaduan->id,
                    'user_id' => $pelapor->id,
                    'foto' => 'sample_aset_' . $aset->nomor_aset . '.jpg',
                    'tipe_dokumentasi' => 'kondisi_awal',
                    'keterangan' => 'Foto bukti kondisi awal dilaporkan oleh warga.',
                    'latitude' => $aset->latitude,
                    'longitude' => $aset->longitude,
                    'created_at' => $dp['created_at'],
                ]);
            }
        }

        echo "Seeding Aset dan Pengaduan selesai: " . count($daftarAset) . " aset dan " . count($daftarPengaduan) . " pengaduan.\n";
    }
}
