<?php

namespace Database\Seeders;

use App\Models\Cctv;
use App\Models\CctvRecording;
use App\Models\JadwalRonda;
use App\Models\LaporanKeamanan;
use App\Models\LaporanKeamananHistory;
use App\Models\User;
use App\Models\Warga;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RondaKeamananSeeder extends Seeder
{
    public function run(): void
    {
        $allRts = ['006', '007', '008', '009', '010'];
        $adminRw = User::where('role', 'admin_rw')->orWhere('email', 'adminrw@rt.com')->orWhere('email', 'adminrw@nexanest.com')->first() ?: User::first();
        $adminRwNama = $adminRw ? $adminRw->name : 'Dirga Pradipta';

        // =========================================================================
        // 1. SEED CCTV CAMERAS (10 Titik Strategis di RW 018)
        // =========================================================================
        $cctvData = [
            [
                'kode_kamera' => 'CCTV-018-RW-001',
                'nama_kamera' => 'CCTV Gerbang Utama Masuk RW 018',
                'rt_number' => 'RW',
                'lokasi' => 'Pos Gerbang Barat, Jl. Melayu Raya No. 1',
                'area_pantau' => 'Akses keluar-masuk utama kendaraan roda 2 dan roda 4, portal otomatis, dan pos sekuriti RW',
                'status' => 'online',
                'catatan_gangguan' => null,
                'tanggal_pemasangan' => '2025-06-15',
                'ip_address' => '192.168.10.101',
                'stream_url' => 'rtsp://192.168.10.101:554/live/ch1',
                'tipe_kamera' => 'Hikvision PTZ 4MP Speed Dome (360° Optical Zoom 25x)',
                'kapasitas_penyimpanan' => '4 TB NVR (Retensi 45 Hari)',
                'maps_url' => 'https://maps.google.com/?q=-6.2625,106.8920',
                'last_online_at' => now(),
            ],
            [
                'kode_kamera' => 'CCTV-018-RW-002',
                'nama_kamera' => 'CCTV Gerbang Timur RW 018',
                'rt_number' => 'RW',
                'lokasi' => 'Pintu Gerbang Timur, Jl. Melayu Permai',
                'area_pantau' => 'Akses keluar kendaraan arah jalan protokol dan pertigaan jalan raya',
                'status' => 'online',
                'catatan_gangguan' => null,
                'tanggal_pemasangan' => '2025-06-15',
                'ip_address' => '192.168.10.102',
                'stream_url' => 'rtsp://192.168.10.102:554/live/ch1',
                'tipe_kamera' => 'Dahua Bullet 4MP IR Night Vision 50m',
                'kapasitas_penyimpanan' => '2 TB NVR (Retensi 30 Hari)',
                'maps_url' => 'https://maps.google.com/?q=-6.2630,106.8935',
                'last_online_at' => now(),
            ],
            [
                'kode_kamera' => 'CCTV-018-006-001',
                'nama_kamera' => 'CCTV Pos Ronda & Simpang RT 006',
                'rt_number' => '006',
                'lokasi' => 'Pos Ronda RT 006, Jl. Mawar Blok A',
                'area_pantau' => 'Simpang pertigaan Blok A, pos kamling RT 006, dan area taman bermain anak',
                'status' => 'online',
                'catatan_gangguan' => null,
                'tanggal_pemasangan' => '2025-08-10',
                'ip_address' => '192.168.10.103',
                'stream_url' => 'rtsp://192.168.10.103:554/live/ch1',
                'tipe_kamera' => 'Hikvision ColorVu Fixed Bullet 2MP',
                'kapasitas_penyimpanan' => '2 TB NVR (Retensi 30 Hari)',
                'maps_url' => 'https://maps.google.com/?q=-6.2622,106.8915',
                'last_online_at' => now(),
            ],
            [
                'kode_kamera' => 'CCTV-018-007-001',
                'nama_kamera' => 'CCTV Simpang Utama RT 007',
                'rt_number' => '007',
                'lokasi' => 'Jl. Melati Blok B Depan No. 12',
                'area_pantau' => 'Perempatan jalan utama RT 007, area penerangan jalan dan deretan hunian warga',
                'status' => 'online',
                'catatan_gangguan' => null,
                'tanggal_pemasangan' => '2025-08-12',
                'ip_address' => '192.168.10.104',
                'stream_url' => 'rtsp://192.168.10.104:554/live/ch1',
                'tipe_kamera' => 'Hikvision Bullet 2MP Weatherproof IP67',
                'kapasitas_penyimpanan' => '2 TB NVR (Retensi 30 Hari)',
                'maps_url' => 'https://maps.google.com/?q=-6.2628,106.8928',
                'last_online_at' => now(),
            ],
            [
                'kode_kamera' => 'CCTV-018-008-001',
                'nama_kamera' => 'CCTV Balai Warga & Pos RT 008',
                'rt_number' => '008',
                'lokasi' => 'Balai Warga RT 008, Jl. Kenanga Blok C',
                'area_pantau' => 'Halaman parkir balai warga, pos ronda RT 008, dan gudang fasilitas lingkungan',
                'status' => 'gangguan',
                'catatan_gangguan' => 'Koneksi kabel LAN outdoor sedikit kendor tersenggol perantingan pohon saat hujan angin kemarin. Menunggu teknisi RT.',
                'tanggal_pemasangan' => '2025-09-01',
                'ip_address' => '192.168.10.105',
                'stream_url' => null,
                'tipe_kamera' => 'Dahua Eyeball Dome 2MP Smart Dual Light',
                'kapasitas_penyimpanan' => '2 TB NVR (Retensi 30 Hari)',
                'maps_url' => 'https://maps.google.com/?q=-6.2635,106.8918',
                'last_online_at' => now()->subHours(18),
            ],
            [
                'kode_kamera' => 'CCTV-018-009-001',
                'nama_kamera' => 'CCTV Taman Lingkungan & Lapangan RT 009',
                'rt_number' => '009',
                'lokasi' => 'Taman Olahraga RT 009, Jl. Cempaka Blok D',
                'area_pantau' => 'Area lapangan serbaguna, gazebo pemuda, dan koridor jalan blok D',
                'status' => 'online',
                'catatan_gangguan' => null,
                'tanggal_pemasangan' => '2025-09-05',
                'ip_address' => '192.168.10.106',
                'stream_url' => 'rtsp://192.168.10.106:554/live/ch1',
                'tipe_kamera' => 'Hikvision PTZ 2MP Outdoor Solar Standalone',
                'kapasitas_penyimpanan' => '2 TB NVR (Retensi 30 Hari)',
                'maps_url' => 'https://maps.google.com/?q=-6.2640,106.8925',
                'last_online_at' => now(),
            ],
            [
                'kode_kamera' => 'CCTV-018-010-001',
                'nama_kamera' => 'CCTV Portal Masuk RT 010',
                'rt_number' => '010',
                'lokasi' => 'Portal Gang Masuk RT 010, Jl. Anggrek Blok E',
                'area_pantau' => 'Pintu portal lorong masuk permukiman RT 010 dan area titik kumpul warga',
                'status' => 'offline',
                'catatan_gangguan' => 'Sedang dilakukan pemindahan tiang listrik PLN sementara di sekitar lokasi portal. Daya dimatikan.',
                'tanggal_pemasangan' => '2025-09-10',
                'ip_address' => '192.168.10.107',
                'stream_url' => null,
                'tipe_kamera' => 'Uniview Bullet 2MP Full Color',
                'kapasitas_penyimpanan' => '1 TB NVR (Retensi 20 Hari)',
                'maps_url' => 'https://maps.google.com/?q=-6.2645,106.8932',
                'last_online_at' => now()->subDays(2),
            ],
            [
                'kode_kamera' => 'CCTV-018-RW-003',
                'nama_kamera' => 'CCTV Depo Bank Sampah & TPS RW 018',
                'rt_number' => 'RW',
                'lokasi' => 'Depo Bank Sampah Terpadu RW 018 Samping Lapangan',
                'area_pantau' => 'Area timbangan bank sampah, tempat pemilahan residu, dan gudang peralatan kebersihan',
                'status' => 'online',
                'catatan_gangguan' => null,
                'tanggal_pemasangan' => '2025-10-01',
                'ip_address' => '192.168.10.108',
                'stream_url' => 'rtsp://192.168.10.108:554/live/ch1',
                'tipe_kamera' => 'Hikvision Dome 2MP Vandal-Proof IK10',
                'kapasitas_penyimpanan' => '2 TB NVR (Retensi 30 Hari)',
                'maps_url' => 'https://maps.google.com/?q=-6.2638,106.8940',
                'last_online_at' => now(),
            ],
        ];

        $cctvModels = [];
        foreach ($cctvData as $cd) {
            $cctvModels[$cd['kode_kamera']] = Cctv::updateOrCreate(
                ['kode_kamera' => $cd['kode_kamera']],
                $cd
            );
        }

        // =========================================================================
        // 2. SEED LAPORAN KEAMANAN (Insiden dengan nomor otomatis & alur status)
        // =========================================================================
        $incidents = [
            [
                'nomor_laporan' => 'LAP-202611-0001',
                'rt_number' => '006',
                'judul' => 'Lampu Penerangan Jalan Simpang Blok A Mati',
                'kategori' => 'Gangguan penerangan jalan',
                'waktu_kejadian' => Carbon::now()->subDays(1)->setTime(19, 30),
                'lokasi' => 'Tiang Lampu PJL No. 4, Depan Rumah Blok A1/10 RT 006',
                'cctv_id' => $cctvModels['CCTV-018-006-001']->id,
                'nama_pelapor' => 'Adrian Pradana',
                'kontak_pelapor' => '0812-3456-7890',
                'kronologi' => 'Saat warga mulai beraktivitas sore ke malam, lampu penerangan jalan utama di tiang No. 4 padam mendadak sehingga jalanan gelap dan rawan tindak kriminal.',
                'status' => 'diproses',
                'tindak_lanjut' => 'Petugas ronda RT 006 sudah memeriksa MCB tiang. Telah dikoordinasikan dengan teknisi PLN/PJU kelurahan untuk penggantian fitting bohlam LED 50W besok pagi.',
                'petugas_penanganan' => 'Fathir Hartono (Admin RT 006)',
                'selesai_at' => null,
            ],
            [
                'nomor_laporan' => 'LAP-202611-0002',
                'rt_number' => '007',
                'judul' => 'Percobaan Pencurian Spion Mobil di Garasi Luar',
                'kategori' => 'Kehilangan barang',
                'waktu_kejadian' => Carbon::now()->subDays(3)->setTime(2, 45),
                'lokasi' => 'Jl. Melati Blok B No. 15, RT 007',
                'cctv_id' => $cctvModels['CCTV-018-007-001']->id,
                'nama_pelapor' => 'Aisha Gunawan',
                'kontak_pelapor' => '0813-8899-7711',
                'kronologi' => 'Terdengar suara gesekan di mobil Avanza yang terparkir di carport depan. Warga terbangun dan menyalakan lampu, terduga 2 orang bermotor langsung melarikan diri ke arah gerbang barat.',
                'status' => 'selesai',
                'tindak_lanjut' => 'Rekaman CCTV simpang RT 007 dan Gerbang Barat diputar dan diamankan sebagai bukti. Pelaku terpantau nopol motor B 4821 XXX. Telah diserahkan ke Babinkamtibmas dan patroli ronda ditingkatkan.',
                'petugas_penanganan' => 'Gavin Gunawan (Admin RT 007)',
                'selesai_at' => Carbon::now()->subDays(2)->setTime(10, 0),
            ],
            [
                'nomor_laporan' => 'LAP-202611-0003',
                'rt_number' => '008',
                'judul' => 'Kabel CCTV Balai RT 008 Kendor Tersenggol Pohon',
                'kategori' => 'Kerusakan CCTV',
                'waktu_kejadian' => Carbon::now()->subHours(18)->setTime(16, 15),
                'lokasi' => 'Halaman Balai Warga RT 008',
                'cctv_id' => $cctvModels['CCTV-018-008-001']->id,
                'nama_pelapor' => 'Aryo Nicholas',
                'kontak_pelapor' => '0819-0987-1234',
                'kronologi' => 'Saat hujan lebat disertai angin sore kemarin, dahan pohon mangga dekat balai patah dan menyenggol kabel UTP CCTV yang mengarah ke pos kamling sehingga status kamera menjadi gangguan.',
                'status' => 'menunggu',
                'tindak_lanjut' => 'Dahan pohon telah dirapikan bersama warga pagi tadi. Menunggu teknisi IT RW untuk melakukan crimping ulang konektor RJ45.',
                'petugas_penanganan' => 'Rizky Pramudya (Admin RT 008)',
                'selesai_at' => null,
            ],
            [
                'nomor_laporan' => 'LAP-202611-0004',
                'rt_number' => 'RW',
                'judul' => 'Aktivitas Pemulung Masuk Tanpa Izin Dini Hari',
                'kategori' => 'Aktivitas mencurigakan',
                'waktu_kejadian' => Carbon::now()->subHours(6)->setTime(3, 10),
                'lokasi' => 'Gerbang Barat RW 018 Samping Pos',
                'cctv_id' => $cctvModels['CCTV-018-RW-001']->id,
                'nama_pelapor' => 'Security Pos RW (Bripka Joko)',
                'kontak_pelapor' => '0821-1122-3344',
                'kronologi' => 'Seorang pria tidak dikenal dengan gerobak mencoba membuka grendel portal barat yang terkunci gembok pada pukul 03.10 WIB.',
                'status' => 'baru',
                'tindak_lanjut' => 'Petugas sekuriti menghampiri dan menegur yang bersangkutan untuk tidak beroperasi di luar jam operasional lingkungan (maks pukul 18.00 WIB). Dicatat identitasnya.',
                'petugas_penanganan' => $adminRwNama . ' (Admin RW)',
                'selesai_at' => null,
            ],
            [
                'nomor_laporan' => 'LAP-202610-0012',
                'rt_number' => '009',
                'judul' => 'Pagar Pembatas Taman Lapangan RT 009 Roboh',
                'kategori' => 'Kerusakan fasilitas umum',
                'waktu_kejadian' => Carbon::parse('2026-10-24 15:40:00'),
                'lokasi' => 'Sisi Utara Lapangan RT 009, Jl. Cempaka',
                'cctv_id' => $cctvModels['CCTV-018-009-001']->id,
                'nama_pelapor' => 'Bastian Sucipto',
                'kontak_pelapor' => '0812-9988-7766',
                'kronologi' => 'Pagar kawat pembatas lapangan terdorong pohon tumbang saat kerja bakti warga.',
                'status' => 'selesai',
                'tindak_lanjut' => 'Telah dilakukan perbaikan dan pengelasan ulang rangka pagar menggunakan kas operasional RT 009 pada tanggal 26 Oktober 2026.',
                'petugas_penanganan' => 'Rian Sucipto (Admin RT 009)',
                'selesai_at' => Carbon::parse('2026-10-26 17:00:00'),
            ],
            [
                'nomor_laporan' => 'LAP-202610-0008',
                'rt_number' => '010',
                'judul' => 'Keributan Remaja Nongkrong Larut Malam',
                'kategori' => 'Gangguan keamanan',
                'waktu_kejadian' => Carbon::parse('2026-10-18 23:50:00'),
                'lokasi' => 'Dekat Portal Masuk RT 010',
                'cctv_id' => $cctvModels['CCTV-018-010-001']->id,
                'nama_pelapor' => 'Ghiserabiella',
                'kontak_pelapor' => '0878-1234-5678',
                'kronologi' => 'Sekelompok remaja luar lingkungan menyalakan knalpot brong dan membunyikan musik keras di ujung lorong jalan RT 010.',
                'status' => 'selesai',
                'tindak_lanjut' => 'Regu ronda RT 010 bersama sekuriti RW mendatangi lokasi dan membubarkan kelompok tersebut dengan persuasif dan aman.',
                'petugas_penanganan' => 'Bagas Kusumah (Admin RT 010)',
                'selesai_at' => Carbon::parse('2026-10-19 00:30:00'),
            ],
        ];

        foreach ($incidents as $inc) {
            $laporan = LaporanKeamanan::updateOrCreate(
                ['nomor_laporan' => $inc['nomor_laporan']],
                array_merge($inc, ['user_id' => $adminRw->id])
            );

            // Create initial history
            LaporanKeamananHistory::firstOrCreate(
                [
                    'laporan_keamanan_id' => $laporan->id,
                    'status_baru' => $laporan->status,
                ],
                [
                    'user_id' => $adminRw->id,
                    'status_sebelumnya' => null,
                    'keterangan' => 'Laporan pertama kali tercatat di sistem oleh ' . $inc['nama_pelapor'],
                    'petugas' => $inc['petugas_penanganan'] ?: 'Admin Sistem',
                ]
            );

            // If done, add completed history step
            if ($laporan->status === 'selesai') {
                LaporanKeamananHistory::firstOrCreate(
                    [
                        'laporan_keamanan_id' => $laporan->id,
                        'status_baru' => 'selesai',
                        'keterangan' => 'Penanganan insiden dinyatakan selesai tuntas: ' . $inc['tindak_lanjut'],
                    ],
                    [
                        'user_id' => $adminRw->id,
                        'status_sebelumnya' => 'diproses',
                        'petugas' => $inc['petugas_penanganan'],
                    ]
                );
            }
        }

        // =========================================================================
        // 3. SEED CCTV RECORDINGS (Terkait dan Tersimpan)
        // =========================================================================
        $sampleCctv1 = $cctvModels['CCTV-018-007-001'];
        $lapSpion = LaporanKeamanan::where('nomor_laporan', 'LAP-202611-0002')->first();
        if ($lapSpion && $sampleCctv1) {
            CctvRecording::firstOrCreate(
                ['judul_rekaman' => 'Rekaman Bukti Insiden Spion RT 007 (02:30 - 03:00)'],
                [
                    'cctv_id' => $sampleCctv1->id,
                    'laporan_keamanan_id' => $lapSpion->id,
                    'waktu_mulai' => Carbon::now()->subDays(3)->setTime(2, 30),
                    'waktu_selesai' => Carbon::now()->subDays(3)->setTime(3, 0),
                    'ukuran_file' => '850 MB',
                    'file_path' => null,
                    'is_locked' => true, // Evidence locked
                    'catatan' => 'Rekaman potongan video pelaku berboncengan melintas di pertigaan RT 007 pukul 02:44 WIB.',
                ]
            );
        }

        $sampleCctvGate = $cctvModels['CCTV-018-RW-001'];
        $lapPemulung = LaporanKeamanan::where('nomor_laporan', 'LAP-202611-0004')->first();
        if ($lapPemulung && $sampleCctvGate) {
            CctvRecording::firstOrCreate(
                ['judul_rekaman' => 'Rekaman Pantauan Gerbang Barat (03:00 - 03:30)'],
                [
                    'cctv_id' => $sampleCctvGate->id,
                    'laporan_keamanan_id' => $lapPemulung->id,
                    'waktu_mulai' => Carbon::now()->subHours(6)->setTime(3, 0),
                    'waktu_selesai' => Carbon::now()->subHours(6)->setTime(3, 30),
                    'ukuran_file' => '920 MB',
                    'file_path' => null,
                    'is_locked' => true,
                    'catatan' => 'Visual rekaman individu membawa gerobak di depan portal pukul 03:10 WIB.',
                ]
            );
        }

        // Additional regular recordings
        foreach ($cctvModels as $cm) {
            if ($cm->status === 'online') {
                CctvRecording::firstOrCreate(
                    [
                        'cctv_id' => $cm->id,
                        'judul_rekaman' => "Rekaman Arsip 24 Jam {$cm->kode_kamera} - Kemarin",
                    ],
                    [
                        'laporan_keamanan_id' => null,
                        'waktu_mulai' => Carbon::yesterday()->setTime(0, 0),
                        'waktu_selesai' => Carbon::yesterday()->setTime(23, 59),
                        'ukuran_file' => '14.2 GB',
                        'file_path' => null,
                        'is_locked' => false,
                        'catatan' => 'Arsip rekaman harian otomatis dari NVR unit.',
                    ]
                );
            }
        }

        // =========================================================================
        // 4. SEED JADWAL RONDA (Multi-RT & Multi-Status)
        // =========================================================================
        $petugasSamples = [
            '006' => [
                'Adrian Pradana (Koordinator), Budi Santoso, Bambang Irawan, Dimas Wahyu',
                'Fathir Hartono (Admin RT), Joko Susilo, Hendra Wijaya, Irwan Setiadi',
                'Kusuma Wardana, Lukman Hakim, Miftah Faridl, Naufal Arifin',
            ],
            '007' => [
                'Gavin Gunawan (Admin RT), Raditya Dika, Reyhan Pratama, Sigit Purnomo',
                'Aldebaran Siregar, Angga Wibowo, Bastian Baskoro, Bintang Gunawan',
                'Teguh Prakoso, Umar Bakri, Vicky Prasetyo, Wahyu Hidayat',
            ],
            '008' => [
                'Rizky Pramudya (Admin RT), Aryo Nicholas, Bagas Mahardika, Dwi Cahyo',
                'Eko Prasetyo, Fajar Ramadhan, Gilang Permana, Hadi Santoso',
                'Irfan Maulana, Junaedi, Kurniawan, Lutfi Hakim',
            ],
            '009' => [
                'Rian Sucipto (Admin RT), Aksara Setiawan, Bastian Sucipto, Candra Wijaya',
                'Danang Sutrisno, Erik Febrian, Fikri Haikal, Gunawan Wibisono',
                'Haris Munandar, Ilham Pratama, Jaka Tarub, Kuncoro',
            ],
            '010' => [
                'Bagas Kusumah (Admin RT), Aldebaran, Arjuna Pradipta, Bayu Pamungkas',
                'Cahya Kamandanu, Dicky Chandra, Evan Dimas, Farhan Alamsyah',
                'Gading Marten, Hilman Syah, Indra Bekti, Joko Anwar',
            ],
        ];

        // Past schedules (Last 14 days) -> mostly 'selesai', some 'tidak_terlaksana'
        for ($day = 14; $day >= 1; $day--) {
            $tgl = Carbon::today()->subDays($day);
            foreach ($allRts as $rt) {
                // Determine status: 90% selesai, 10% tidak_terlaksana
                $status = ($day % 7 === 0 && $rt === '010') ? 'tidak_terlaksana' : 'selesai';
                $petugasList = $petugasSamples[$rt][$day % 3];
                $koordinator = explode(',', $petugasList)[0];

                JadwalRonda::firstOrCreate(
                    [
                        'rt_number' => $rt,
                        'tanggal' => $tgl->format('Y-m-d'),
                        'shift' => 'Malam (22:00 - 04:00 WIB)',
                    ],
                    [
                        'lokasi_pos' => 'Pos Ronda Siskamling RT ' . $rt,
                        'petugas' => $petugasList,
                        'koordinator' => trim(str_replace('(Koordinator)', '', str_replace('(Admin RT)', '', $koordinator))),
                        'kontak_koordinator' => '081' . rand(20000000, 99999999),
                        'status' => $status,
                        'catatan' => $status === 'selesai' ? 'Situasi lingkungan aman, patroli keliling dilaksanakan 3 putaran (23:00, 01:30, 03:00 WIB).' : 'Hujan badai ekstrem dan pos ronda tergenang air, patroli dialihkan monitoring berkala lewat WA grup.',
                        'laporan_patroli' => $status === 'selesai' ? 'Patroli selesai. Gembok portal terkunci rapat, kendaraan warga aman.' : null,
                    ]
                );
            }
        }

        // Today's schedule -> 'berlangsung' or 'terjadwal'
        foreach ($allRts as $rt) {
            JadwalRonda::firstOrCreate(
                [
                    'rt_number' => $rt,
                    'tanggal' => Carbon::today()->format('Y-m-d'),
                    'shift' => 'Malam (22:00 - 04:00 WIB)',
                ],
                [
                    'lokasi_pos' => 'Pos Ronda Siskamling RT ' . $rt,
                    'petugas' => $petugasSamples[$rt][0],
                    'koordinator' => 'Petugas Piket RT ' . $rt,
                    'kontak_koordinator' => '0812-3344-55' . $rt,
                    'status' => 'berlangsung',
                    'catatan' => 'Jadwal ronda malam hari ini. Diharapkan hadir tepat waktu di pos ronda.',
                    'laporan_patroli' => null,
                ]
            );
        }

        // Upcoming schedules (Next 7 days) -> 'terjadwal'
        for ($day = 1; $day <= 7; $day++) {
            $tgl = Carbon::today()->addDays($day);
            foreach ($allRts as $rt) {
                JadwalRonda::firstOrCreate(
                    [
                        'rt_number' => $rt,
                        'tanggal' => $tgl->format('Y-m-d'),
                        'shift' => 'Malam (22:00 - 04:00 WIB)',
                    ],
                    [
                        'lokasi_pos' => 'Pos Ronda Siskamling RT ' . $rt,
                        'petugas' => $petugasSamples[$rt][$day % 3],
                        'koordinator' => 'Petugas Piket RT ' . $rt,
                        'kontak_koordinator' => '0813-5566-77' . $rt,
                        'status' => 'terjadwal',
                        'catatan' => 'Shift ronda rutin mingguan warga RT ' . $rt,
                        'laporan_patroli' => null,
                    ]
                );
            }
        }
    }
}
