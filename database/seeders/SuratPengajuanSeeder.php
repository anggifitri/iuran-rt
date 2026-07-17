<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SuratPengajuan;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SuratPengajuanSeeder extends Seeder
{
    public function run(): void
    {
        $wargaUsers = User::where('role', 'warga')->get();

        if ($wargaUsers->isEmpty()) {
            return;
        }

        $jenisSurats = [
            'Surat Keterangan Domisili',
            'Surat Keterangan Usaha (SKU)',
            'Surat Keterangan Tidak Mampu (SKTM)',
            'Surat Pengantar Kelakuan Baik (SKCK)',
            'Surat Pengantar Pernikahan'
        ];

        $keperluans = [
            'Surat Keterangan Domisili' => [
                'Syarat pembukaan rekening tabungan bank syariah',
                'Kelengkapan berkas mutasi alamat kependudukan',
                'Persyaratan melamar pekerjaan sebagai staf admin',
                'Persyaratan pendaftaran domisili usaha rumahan'
            ],
            'Surat Keterangan Usaha (SKU)' => [
                'Syarat pengajuan Kredit Usaha Rakyat (KUR) Bank Mandiri',
                'Persyaratan izin kemitraan waralaba makanan tradisional',
                'Kelengkapan pengajuan bantuan modal UMKM dari Dinas Koperasi',
                'Syarat pembukaan merchant QRIS usaha aurora'
            ],
            'Surat Keterangan Tidak Mampu (SKTM)' => [
                'Pengajuan beasiswa Kartu Indonesia Pintar (KIP) anak sekolah',
                'Keringanan biaya pengobatan rawat inap rumah sakit daerah',
                'Pengajuan subsidi listrik golongan R1-450 VA',
                'Syarat pembebasan biaya pendaftaran universitas negeri'
            ],
            'Surat Pengantar Kelakuan Baik (SKCK)' => [
                'Syarat pendaftaran seleksi CPNS Kementerian Perhubungan',
                'Melamar pekerjaan di anak perusahaan BUMN',
                'Persyaratan memperpanjang kontrak kerja karyawan',
                'Kelengkapan pengajuan visa kunjungan ke luar negeri'
            ],
            'Surat Pengantar Pernikahan' => [
                'Pendaftaran berkas pernikahan di KUA Kecamatan Bandung Raya',
                'Syarat pengurusan administrasi numpang nikah di KUA daerah asal',
                'Syarat validasi nikah untuk pendaftaran tunjangan keluarga'
            ]
        ];

        // We will seed exactly 20 records
        // 7 pending_rt, 7 pending_rw, 6 selesai
        for ($i = 1; $i <= 20; $i++) {
            // Assign sequentially to ensure 20 completely unique citizens
            $user = $wargaUsers[$i - 1];
            $jenis = $jenisSurats[array_rand($jenisSurats)];
            
            // Pick a random purpose matching the certificate type
            $keperluanList = $keperluans[$jenis];
            $keperluan = $keperluanList[array_rand($keperluanList)];

            // Determine status
            if ($i <= 7) {
                $status = 'pending_rt';
                $tteHash = null;
                $pdfPath = null;
            } elseif ($i <= 14) {
                $status = 'pending_rw';
                $tteHash = null;
                $pdfPath = null;
            } else {
                $status = 'selesai';
                $tteHash = hash('sha256', $i . '|' . $jenis . '|' . now()->subDays(rand(1, 10))->toDateTimeString());
                $pdfPath = route('surat.pdf', $i); // Dynamic route
            }

            // Generate realistic created date
            $createdAt = Carbon::now()->subDays(rand(1, 15))->subHours(rand(1, 23));

            SuratPengajuan::create([
                'user_id' => $user->id,
                'rt_number' => $user->rt_number ?? '006',
                'jenis_surat' => $jenis,
                'keperluan' => $keperluan,
                'status' => $status,
                'tte_hash' => $tteHash,
                'pdf_path' => $pdfPath,
                'created_at' => $createdAt,
                'updated_at' => $status === 'selesai' ? $createdAt->addDays(rand(1, 2)) : $createdAt
            ]);
        }
    }
}
