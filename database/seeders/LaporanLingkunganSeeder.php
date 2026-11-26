<?php

namespace Database\Seeders;

use App\Models\AgendaRapat;
use App\Models\LaporanLingkungan;
use App\Models\LaporanLingkunganHistory;
use App\Models\LaporanLingkunganPeriode;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LaporanLingkunganSeeder extends Seeder
{
    public function run(): void
    {
        $adminRw = User::where('role', 'admin')->where('rt_number', '000')->first() ?: User::first();
        $adminRts = [
            '006' => User::where('role', 'admin')->where('rt_number', '006')->first() ?: $adminRw,
            '007' => User::where('role', 'admin')->where('rt_number', '007')->first() ?: $adminRw,
            '008' => User::where('role', 'admin')->where('rt_number', '008')->first() ?: $adminRw,
            '009' => User::where('role', 'admin')->where('rt_number', '009')->first() ?: $adminRw,
            '010' => User::where('role', 'admin')->where('rt_number', '010')->first() ?: $adminRw,
        ];

        // 1. Seed Periode Bulanan 2026
        for ($m = 1; $m <= 12; $m++) {
            $bulanStr = str_pad($m, 2, '0', STR_PAD_LEFT);
            $startDate = Carbon::create(2026, $m, 1);
            $deadline = Carbon::create(2026, $m, 10);

            LaporanLingkunganPeriode::updateOrCreate(
                ['periode_bulan' => $bulanStr, 'periode_tahun' => '2026'],
                [
                    'tanggal_mulai' => $startDate->toDateString(),
                    'batas_waktu' => $deadline->toDateString(),
                    'status' => $m <= 11 ? 'aktif' : 'aktif',
                    'catatan_instruksi_rw' => "Fokus pemantauan periode ini: kebersihan saluran drainase menjelang musim hujan dan optimalisasi bank sampah tingkat RT.",
                    'created_by' => $adminRw ? $adminRw->id : null,
                ]
            );
        }

        // Variasi data realistis kebersihan dan drainase
        $kebersihanOptions = [
            'Sangat Bersih & Tertata',
            'Cukup Bersih & Terawat',
            'Kurang Bersih / Perlu Penanganan',
        ];
        $drainaseOptions = [
            'Lancar Tanpa Hambatan',
            'Sedimen Cukup Tebal',
            'Tersumbat / Berpotensi Genangan',
        ];
        $aliranOptions = [
            'Lancar Mengalir',
            'Lambat / Genangan Sedang',
            'Tersumbat Total',
        ];
        $penghijauanOptions = [
            'Subur & Asri Rindang',
            'Cukup Terawat',
            'Kering / Butuh Peremajaan',
        ];

        // 2. Seed Laporan Historis Januari - Oktober 2026 untuk 5 RT
        for ($m = 1; $m <= 10; $m++) {
            $bulanStr = str_pad($m, 2, '0', STR_PAD_LEFT);

            foreach (['006', '007', '008', '009', '010'] as $idx => $rt) {
                $userRt = $adminRts[$rt];
                $submitDate = Carbon::create(2026, $m, rand(4, 9), rand(8, 17), rand(10, 50));
                $reviewDate = (clone $submitDate)->addDays(rand(1, 3))->addHours(rand(1, 4));

                $kebersihan = ($m % 3 == 0 && $rt == '008') ? $kebersihanOptions[1] : $kebersihanOptions[0];
                $drainase = ($m >= 9 && in_array($rt, ['008', '010'])) ? $drainaseOptions[1] : $drainaseOptions[0];
                $aliran = $drainase == $drainaseOptions[0] ? $aliranOptions[0] : $aliranOptions[1];
                $penghijauan = $penghijauanOptions[rand(0, 1)];

                $lap = LaporanLingkungan::updateOrCreate(
                    [
                        'rt_number' => $rt,
                        'periode_bulan' => $bulanStr,
                        'periode_tahun' => '2026',
                    ],
                    [
                        'user_id' => $userRt->id,
                        'tanggal_pengiriman' => $submitDate,
                        'kondisi_kebersihan' => $kebersihan,
                        'area_terjaga_kebersihan' => "Jalur utama Jl. Kenanga & Jl. Anggrek RT {$rt} terpelihara bersih dan bebas tumpukan sampah liar.",
                        'area_perlu_perhatian_kebersihan' => ($kebersihan != $kebersihanOptions[0]) ? "Sudut gang perbatasan RT butuh penertiban pembuangan sampah dedaunan." : "Tidak ada area kritis.",
                        'masalah_sampah' => "Pengangkutan sampah terjadwal rutin 3x seminggu oleh petugas DLH bersama tim kebersihan RW.",
                        'keterangan_kebersihan' => "Warga aktif menyapu halaman dan meletakkan tempat sampah tertutup di depan rumah.",

                        'kondisi_drainase' => $drainase,
                        'kondisi_aliran_air' => $aliran,
                        'titik_rawan_genangan' => ($drainase != $drainaseOptions[0]) ? "Saluran di depan Blok C RT {$rt} rawan genangan 10-15 cm saat hujan deras lebih dari 1 jam." : "Tidak ada genangan signifikan.",
                        'saluran_butuh_pembersihan' => "Pengerukan lumpur rutin berkala pada gorong-gorong cabang timur.",
                        'keterangan_drainase' => "Kemiringan saluran telah memadai mengarah ke drainase induk RW.",

                        'program_penghijauan' => "Perawatan tanaman pot warga dan penghijauan sepanjang taman koridor RT.",
                        'kondisi_penghijauan' => $penghijauan,
                        'ketersediaan_ruang_terbuka' => "Taman bermain mini RT {$rt} terawat baik dan menjadi sarana interaksi sosial warga.",
                        'kegiatan_perawatan_tanaman' => "Penyiraman rutin pagi sore dan pemupukan kompos hasil olahan Bank Sampah.",
                        'kendala_penghijauan' => "Beberapa pohon pelindung mulai rimbun menyentuh kabel listrik PLN.",

                        'kegiatan_kerja_bakti' => "Kerja bakti gotong royong warga RT {$rt} pembersihan lingkungan dan perapian dahan.",
                        'program_kebersihan' => "Gerakan pilah sampah organik dan anorganik dari rumah tangga.",
                        'program_lingkungan_lainnya' => "Monitoring jentik nyamuk (Jumantik) mandiri per dasawisma.",
                        'tanggal_pelaksanaan_kegiatan' => Carbon::create(2026, $m, rand(12, 25))->toDateString(),
                        'jumlah_peserta_kegiatan' => rand(25, 60),

                        'kendala_lingkungan' => "Kendala minor pengangkutan sampah saat hari libur nasional.",
                        'lokasi_perhatian_khusus' => "Perbatasan tanah kosong blok timur dekat pagar perimeter.",
                        'rencana_tindak_lanjut' => "Koordinasi pemasangan plang larangan buang sampah sembarangan dan kerja bakti lanjutan.",
                        'bantuan_rw_dibutuhkan' => "Dukungan armada gerobak sampah tambahan saat kerja bakti akbar.",

                        'status' => 'diterima',
                        'catatan_rw' => "Laporan sangat lengkap dan informatif. Kondisi kebersihan dan kerja bakti RT {$rt} berjalan sangat baik. Terima kasih atas partisipasi aktif warga.",
                        'pemeriksa_id' => $adminRw ? $adminRw->id : null,
                        'tanggal_pemeriksaan' => $reviewDate,
                        'catatan_evaluasi_rw' => "Disetujui tanpa catatan perbaikan. Pertahankan koordinasi kerja bakti rutin.",
                        'versi' => 1,
                        'is_terlambat' => false,
                    ]
                );

                // History
                LaporanLingkunganHistory::create([
                    'laporan_id' => $lap->id,
                    'user_id' => $userRt->id,
                    'aksi' => 'dikirim_ke_rw',
                    'status_sebelum' => 'draft',
                    'status_sesudah' => 'menunggu_pemeriksaan',
                    'catatan' => "Laporan bulanan lingkungan RT {$rt} periode {$bulanStr}/2026 diserahkan kepada RW.",
                    'versi' => 1,
                    'created_at' => $submitDate,
                ]);

                LaporanLingkunganHistory::create([
                    'laporan_id' => $lap->id,
                    'user_id' => $adminRw ? $adminRw->id : null,
                    'aksi' => 'diterima',
                    'status_sebelum' => 'menunggu_pemeriksaan',
                    'status_sesudah' => 'diterima',
                    'catatan' => "Laporan telah diperiksa dan disetujui resmi oleh Ketua RW 018.",
                    'versi' => 1,
                    'created_at' => $reviewDate,
                ]);
            }
        }

        // 3. Seed Periode Berjalan (November 2026) dengan Alur Realistis
        // RT 006: Diterima
        $submit006 = Carbon::create(2026, 11, 5, 9, 30);
        $review006 = Carbon::create(2026, 11, 6, 14, 15);
        $lap006 = LaporanLingkungan::updateOrCreate(
            ['rt_number' => '006', 'periode_bulan' => '11', 'periode_tahun' => '2026'],
            [
                'user_id' => $adminRts['006']->id,
                'tanggal_pengiriman' => $submit006,
                'kondisi_kebersihan' => 'Sangat Bersih & Tertata',
                'area_terjaga_kebersihan' => 'Area perumahan blok A1 sampai A15 sangat bersih dan rapi.',
                'area_perlu_perhatian_kebersihan' => 'Pos ronda perlu pengecatan ulang dan penataan tong sampah.',
                'masalah_sampah' => 'Pengangkutan lancar, warga aktif mendaur ulang ke Bank Sampah RW.',
                'keterangan_kebersihan' => 'Hasil evaluasi mingguan kebersihan mencapai predikat A.',
                'kondisi_drainase' => 'Lancar Tanpa Hambatan',
                'kondisi_aliran_air' => 'Lancar Mengalir',
                'titik_rawan_genangan' => 'Nihil genangan setelah pembersihan sedimen minggu lalu.',
                'saluran_butuh_pembersihan' => 'Hanya saluran tersier kecil yang perlu pembersihan daun kering berkala.',
                'keterangan_drainase' => 'Debit air pembuangan berjalan normal ke saluran utama.',
                'program_penghijauan' => 'Penanaman bibit pohon tabebuya kuning di sepanjang jalan protokol RT.',
                'kondisi_penghijauan' => 'Subur & Asri Rindang',
                'ketersediaan_ruang_terbuka' => 'Ruang Terbuka Hijau (RTH) RT 006 terawat optimal.',
                'kegiatan_perawatan_tanaman' => 'Pemotongan rumput berkala dan pemupukan organik.',
                'kendala_penghijauan' => 'Kebutuhan selang air panjang untuk taman sudut.',
                'kegiatan_kerja_bakti' => 'Kerja bakti pembersihan selokan serentak pada hari Minggu 8 November.',
                'program_kebersihan' => 'Operasi Semut Pagi dan Gerakan Bebas Plastik Sekali Pakai.',
                'program_lingkungan_lainnya' => 'Sosialisasi pengolahan kompos rumahan dengan komposter biopori.',
                'tanggal_pelaksanaan_kegiatan' => '2026-11-08',
                'jumlah_peserta_kegiatan' => 48,
                'kendala_lingkungan' => 'Dahan pohon rindang mendekati jaringan tiang listrik utama.',
                'lokasi_perhatian_khusus' => 'Dekat gardu trafo blok timur RT 006.',
                'rencana_tindak_lanjut' => 'Pemangkasan mandiri bersama tim keamanan lingkungan.',
                'bantuan_rw_dibutuhkan' => 'Bantuan koordinasi dengan dinas pertamanan atau PLN untuk dahan tinggi.',
                'status' => 'diterima',
                'catatan_rw' => 'Laporan sangat memuaskan, kerja bakti terlaksana dengan partisipasi 48 warga. Bantuan koordinasi dahan pohon telah diteruskan ke pihak terkait.',
                'pemeriksa_id' => $adminRw ? $adminRw->id : null,
                'tanggal_pemeriksaan' => $review006,
                'catatan_evaluasi_rw' => 'Disetujui. Sangat mengapresiasi keaktifan warga RT 006.',
                'versi' => 1,
                'is_terlambat' => false,
            ]
        );
        LaporanLingkunganHistory::create([
            'laporan_id' => $lap006->id,
            'user_id' => $adminRts['006']->id,
            'aksi' => 'dikirim_ke_rw',
            'status_sebelum' => 'draft',
            'status_sesudah' => 'menunggu_pemeriksaan',
            'catatan' => 'Laporan lingkungan November 2026 dikirimkan tepat waktu sebelum batas deadline.',
            'versi' => 1,
            'created_at' => $submit006,
        ]);
        LaporanLingkunganHistory::create([
            'laporan_id' => $lap006->id,
            'user_id' => $adminRw ? $adminRw->id : null,
            'aksi' => 'diterima',
            'status_sebelum' => 'menunggu_pemeriksaan',
            'status_sesudah' => 'diterima',
            'catatan' => 'Laporan diperiksa dan diterima langsung tanpa revisi.',
            'versi' => 1,
            'created_at' => $review006,
        ]);

        // RT 007: Menunggu Pemeriksaan
        $submit007 = Carbon::create(2026, 11, 9, 11, 20);
        $lap007 = LaporanLingkungan::updateOrCreate(
            ['rt_number' => '007', 'periode_bulan' => '11', 'periode_tahun' => '2026'],
            [
                'user_id' => $adminRts['007']->id,
                'tanggal_pengiriman' => $submit007,
                'kondisi_kebersihan' => 'Sangat Bersih & Tertata',
                'area_terjaga_kebersihan' => 'Area pemukiman gang Melati dan Flamboyan bersih tertib.',
                'area_perlu_perhatian_kebersihan' => 'Area parkir komunal ujung gang terkadang berdebu.',
                'masalah_sampah' => 'Tong sampah terpilah telah tersedia di 12 titik RT 007.',
                'keterangan_kebersihan' => 'Petugas kebersihan RT bekerja setiap pagi pukul 06.00-08.00.',
                'kondisi_drainase' => 'Lancar Tanpa Hambatan',
                'kondisi_aliran_air' => 'Lancar Mengalir',
                'titik_rawan_genangan' => 'Nihil, aliran air lancar ke saluran sekunder.',
                'saluran_butuh_pembersihan' => 'Pembersihan daun gugur secara mingguan.',
                'keterangan_drainase' => 'Penutup selokan beton kokoh dan aman dilewati warga.',
                'program_penghijauan' => 'Program "Lorong Hijau" dengan pot gantung dan tanaman hias vertikal.',
                'kondisi_penghijauan' => 'Subur & Asri Rindang',
                'ketersediaan_ruang_terbuka' => 'Taman sudut RT asri dan terawat.',
                'kegiatan_perawatan_tanaman' => 'Penyiraman otomatis berbasis timer di taman bersama.',
                'kendala_penghijauan' => 'Nihil kendala teknis.',
                'kegiatan_kerja_bakti' => 'Pembersihan saluran air dan pengecatan trotoar jalan RT.',
                'program_kebersihan' => 'Pilah sampah plastik untuk disetor ke bank sampah.',
                'program_lingkungan_lainnya' => 'Sosialisasi pembuatan ekoenzim dari sisa kulit buah dapur.',
                'tanggal_pelaksanaan_kegiatan' => '2026-11-07',
                'jumlah_peserta_kegiatan' => 36,
                'kendala_lingkungan' => 'Perlu penambahan lampu penerangan jalan di lorong belakang.',
                'lokasi_perhatian_khusus' => 'Gang Melati IV ujung utara.',
                'rencana_tindak_lanjut' => 'Mengajukan pengadaan 2 unit lampu solar cell ke RW.',
                'bantuan_rw_dibutuhkan' => 'Bantuan koordinasi pengadaan lampu penerangan lorong.',
                'status' => 'menunggu_pemeriksaan',
                'versi' => 1,
                'is_terlambat' => false,
            ]
        );
        LaporanLingkunganHistory::create([
            'laporan_id' => $lap007->id,
            'user_id' => $adminRts['007']->id,
            'aksi' => 'dikirim_ke_rw',
            'status_sebelum' => 'draft',
            'status_sesudah' => 'menunggu_pemeriksaan',
            'catatan' => 'Laporan bulanan diserahkan untuk proses verifikasi dan evaluasi Ketua RW.',
            'versi' => 1,
            'created_at' => $submit007,
        ]);

        // RT 008: Perlu Revisi
        $submit008 = Carbon::create(2026, 11, 8, 16, 45);
        $review008 = Carbon::create(2026, 11, 10, 10, 0);
        $lap008 = LaporanLingkungan::updateOrCreate(
            ['rt_number' => '008', 'periode_bulan' => '11', 'periode_tahun' => '2026'],
            [
                'user_id' => $adminRts['008']->id,
                'tanggal_pengiriman' => $submit008,
                'kondisi_kebersihan' => 'Cukup Bersih & Terawat',
                'area_terjaga_kebersihan' => 'Jalan utama RT 008 bersih dan tertata.',
                'area_perlu_perhatian_kebersihan' => 'Saluran batas RT 008 dan RT 009 masih terdapat endapan lumpur pekat.',
                'masalah_sampah' => 'Sebagian warga masih membuang sampah ranting di pinggir selokan.',
                'keterangan_kebersihan' => 'Perlu kerja bakti gabungan lintas RT.',
                'kondisi_drainase' => 'Sedimen Cukup Tebal',
                'kondisi_aliran_air' => 'Lambat / Genangan Sedang',
                'titik_rawan_genangan' => 'Titik pertemuan saluran gang 3 dan gang 4 rawan meluap saat hujan lebat.',
                'saluran_butuh_pembersihan' => 'Pengerukan lumpur sedimen sepanjang 35 meter.',
                'keterangan_drainase' => 'Gorong-gorong membutuhkan pembongkaran sedimentasi.',
                'program_penghijauan' => 'Penanaman bibit tanaman obat keluarga (TOGA).',
                'kondisi_penghijauan' => 'Cukup Terawat',
                'ketersediaan_ruang_terbuka' => 'Ruang terbuka hijau terbatas.',
                'kegiatan_perawatan_tanaman' => 'Penyiraman berkala oleh pengurus RT.',
                'kendala_penghijauan' => 'Lahan sempit di tepi jalan.',
                'kegiatan_kerja_bakti' => 'Kerja bakti pembersihan sampah dan pemangkasan tanaman.',
                'program_kebersihan' => 'Pemberian kantong sampah pilah.',
                'program_lingkungan_lainnya' => 'Edukasi door-to-door pemilahan sampah anorganik.',
                'tanggal_pelaksanaan_kegiatan' => '2026-11-06',
                'jumlah_peserta_kegiatan' => 22,
                'kendala_lingkungan' => 'Sedimen lumpur tebal memerlukan cangkul dan karung pasir ekstra.',
                'lokasi_perhatian_khusus' => 'Saluran gang 3 RT 008.',
                'rencana_tindak_lanjut' => 'Mengagendakan pengerukan total pada hari Minggu pekan depan.',
                'bantuan_rw_dibutuhkan' => 'Pinjaman alat berat pengeruk manual atau tim kebersihan RW.',
                'status' => 'perlu_revisi',
                'catatan_rw' => 'Mohon lengkapi estimasi volume lumpur yang perlu dikeruk dan lampirkan foto dokumentasi kondisi terkini gorong-gorong gang 3 sebelum kami koordinasikan bantuan alat pengeruk RW.',
                'pemeriksa_id' => $adminRw ? $adminRw->id : null,
                'tanggal_pemeriksaan' => $review008,
                'catatan_evaluasi_rw' => 'Perlu revisi: lampirkan rincian kebutuhan karung pasir dan foto titik gorong-gorong.',
                'versi' => 1,
                'is_terlambat' => false,
            ]
        );
        LaporanLingkunganHistory::create([
            'laporan_id' => $lap008->id,
            'user_id' => $adminRts['008']->id,
            'aksi' => 'dikirim_ke_rw',
            'status_sebelum' => 'draft',
            'status_sesudah' => 'menunggu_pemeriksaan',
            'catatan' => 'Laporan pertama kali diserahkan ke RW.',
            'versi' => 1,
            'created_at' => $submit008,
        ]);
        LaporanLingkunganHistory::create([
            'laporan_id' => $lap008->id,
            'user_id' => $adminRw ? $adminRw->id : null,
            'aksi' => 'revisi_diminta',
            'status_sebelum' => 'menunggu_pemeriksaan',
            'status_sesudah' => 'perlu_revisi',
            'catatan' => 'Ketua RW meminta penambahan foto dokumentasi sedimen gorong-gorong dan rincian kebutuhan alat.',
            'versi' => 1,
            'created_at' => $review008,
        ]);

        // RT 009: Draft Disimpan
        $lap009 = LaporanLingkungan::updateOrCreate(
            ['rt_number' => '009', 'periode_bulan' => '11', 'periode_tahun' => '2026'],
            [
                'user_id' => $adminRts['009']->id,
                'tanggal_pengiriman' => null,
                'kondisi_kebersihan' => 'Sangat Bersih & Tertata',
                'area_terjaga_kebersihan' => 'Blok D dan taman kanak-kanak bersih teratur.',
                'area_perlu_perhatian_kebersihan' => 'Sedang dalam pengecekan lapangan.',
                'masalah_sampah' => 'Pengangkutan tepat waktu setiap hari Selasa dan Jumat.',
                'keterangan_kebersihan' => 'Draft sedang dilengkapi oleh Sekretaris RT 009.',
                'kondisi_drainase' => 'Lancar Tanpa Hambatan',
                'kondisi_aliran_air' => 'Lancar Mengalir',
                'titik_rawan_genangan' => 'Nihil genangan air.',
                'saluran_butuh_pembersihan' => 'Pembersihan ringan di mulut gorong-gorong.',
                'keterangan_drainase' => 'Saluran drainase dalam kondisi baik.',
                'program_penghijauan' => 'Penanaman pohon peneduh di tepi lapangan serbaguna.',
                'kondisi_penghijauan' => 'Subur & Asri Rindang',
                'ketersediaan_ruang_terbuka' => 'Lapangan warga RT 009 difungsikan maksimal.',
                'kegiatan_perawatan_tanaman' => 'Pemangkasan rumput lapangan.',
                'kendala_penghijauan' => 'Nihil.',
                'kegiatan_kerja_bakti' => 'Rencana kerja bakti tanggal 20 November 2026.',
                'program_kebersihan' => 'Jadwal rutin pembersihan tong sampah komunal.',
                'program_lingkungan_lainnya' => 'Partisipasi bank sampah mandiri.',
                'tanggal_pelaksanaan_kegiatan' => '2026-11-20',
                'jumlah_peserta_kegiatan' => 30,
                'kendala_lingkungan' => 'Masih dalam proses kompilasi catatan warga.',
                'lokasi_perhatian_khusus' => 'Belum ada.',
                'rencana_tindak_lanjut' => 'Finalisasi laporan sebelum dikirim ke RW.',
                'bantuan_rw_dibutuhkan' => 'Belum dibutuhkan.',
                'status' => 'draft',
                'catatan_rw' => null,
                'versi' => 1,
                'is_terlambat' => false,
            ]
        );
        LaporanLingkunganHistory::create([
            'laporan_id' => $lap009->id,
            'user_id' => $adminRts['009']->id,
            'aksi' => 'draft_disimpan',
            'status_sebelum' => null,
            'status_sesudah' => 'draft',
            'catatan' => 'Draft awal laporan bulanan lingkungan RT 009 disimpan sementara.',
            'versi' => 1,
            'created_at' => Carbon::create(2026, 11, 10, 15, 0),
        ]);

        // RT 010: Belum Mengirim (lewat deadline 10 November -> status terlambat mengirim)
        // Dibiarkan tidak ada record LaporanLingkungan untuk November 2026 sehingga sistem mendeteksi "Belum Mengirim / Terlambat"
    }
}
