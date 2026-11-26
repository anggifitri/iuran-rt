<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\PosyanduPetugas;
use Carbon\Carbon;

class PosyanduPetugasSeeder extends Seeder
{
    public function run(): void
    {
        $adminRw = User::where('role', 'admin_rw')->orWhere('email', 'adminrw@rt.com')->first();
        $adminRwId = $adminRw ? $adminRw->id : null;

        // 1. Tenaga Kesehatan: Aisha Gunawan (Bidan Desa Wilayah RW 018)
        $nakesUser = User::where('email', 'warga4@rt.com')->first() ?? User::where('name', 'like', '%Aisha%')->first();
        if ($nakesUser) {
            PosyanduPetugas::updateOrCreate(
                ['user_id' => $nakesUser->id, 'tipe_petugas' => 'tenaga_kesehatan'],
                [
                    'jabatan'             => 'Bidan Desa Pembina RW 018',
                    'nomor_sk_str'        => 'STR-BIDAN-3275-2026-0442',
                    'instansi'            => 'Puskesmas Kelurahan & Poskesdes',
                    'posyandu_nama'       => 'Semua Posyandu RW 018',
                    'wilayah_rt'          => 'semua',
                    'tanggal_mulai'       => Carbon::now()->subMonths(6)->format('Y-m-d'),
                    'tanggal_selesai'     => Carbon::now()->addMonths(18)->format('Y-m-d'),
                    'status'              => 'aktif',
                    'status_verifikasi'   => 'terverifikasi',
                    'kualifikasi_dokumen' => 'Surat Izin Praktik Bidan (SIPB) No. 446/SIPB/2026 dan Sertifikat Pelatihan Tata Laksana Antropometri Kemenkes.',
                    'catatan_penugasan'   => 'Bertanggung jawab melakukan verifikasi klinis hasil penimbangan balita, skrining ANC risiko tinggi, dan rujukan medis ke Puskesmas.',
                    'catatan_verifikasi'  => 'Berkas SIPB dan ijazah profesi kebidanan telah divalidasi keasliannya oleh Pengurus RW 018.',
                    'assigned_by'         => $adminRwId,
                ]
            );
        }

        // 2. Kader Posyandu Balita: Adrian Pradana
        $kaderUser1 = User::where('email', 'warga1@rt.com')->first() ?? User::where('name', 'like', '%Adrian%')->first();
        if ($kaderUser1) {
            PosyanduPetugas::updateOrCreate(
                ['user_id' => $kaderUser1->id, 'tipe_petugas' => 'kader'],
                [
                    'jabatan'             => 'Kader Utama Pemantauan Balita & KMS',
                    'nomor_sk_str'        => 'SK-RW018/KDR-POSY/2026-001',
                    'instansi'            => 'Posyandu Melati Mandiri RW 018',
                    'posyandu_nama'       => 'Posyandu Melati Mandiri',
                    'wilayah_rt'          => 'semua',
                    'tanggal_mulai'       => Carbon::now()->subMonths(8)->format('Y-m-d'),
                    'tanggal_selesai'     => Carbon::now()->addMonths(16)->format('Y-m-d'),
                    'status'              => 'aktif',
                    'status_verifikasi'   => 'terverifikasi',
                    'kualifikasi_dokumen' => 'Sertifikat Pelatihan Kader Posyandu Terintegrasi Dinas Kesehatan Kota.',
                    'catatan_penugasan'   => 'Melakukan penimbangan berat badan, pengukuran tinggi badan balita, dan pencatatan imunisasi rutin di buku register.',
                    'catatan_verifikasi'  => 'Telah lulus orientasi 25 kompetensi kader posyandu.',
                    'assigned_by'         => $adminRwId,
                ]
            );
        }

        // 3. Kader Posyandu Ibu Hamil: Adrian Sucipto (RT 007)
        $kaderUser2 = User::where('email', 'warga2@rt.com')->first();
        if ($kaderUser2) {
            PosyanduPetugas::updateOrCreate(
                ['user_id' => $kaderUser2->id, 'tipe_petugas' => 'kader'],
                [
                    'jabatan'             => 'Kader Pendamping Ibu Hamil & Balita RT 007',
                    'nomor_sk_str'        => 'SK-RW018/KDR-POSY/2026-002',
                    'instansi'            => 'Posyandu Bougenville RT 007',
                    'posyandu_nama'       => 'Posyandu Bougenville',
                    'wilayah_rt'          => '007',
                    'tanggal_mulai'       => Carbon::now()->subMonths(4)->format('Y-m-d'),
                    'tanggal_selesai'     => Carbon::now()->addMonths(8)->format('Y-m-d'),
                    'status'              => 'aktif',
                    'status_verifikasi'   => 'terverifikasi',
                    'kualifikasi_dokumen' => 'Surat Rekomendasi Ketua RT 007 dan Sertifikat Pembinaan Ibu Hamil Sehat.',
                    'catatan_penugasan'   => 'Pendampingan ibu hamil risiko KEK di wilayah RT 007 dan distribusi PMT biskuit.',
                    'catatan_verifikasi'  => 'Penugasan aktif wilayah RT 007.',
                    'assigned_by'         => $adminRwId,
                ]
            );
        }

        // 4. Riwayat Penugasan yang Pernah Dicabut / Kedaluwarsa (for history testing)
        $revokedUser = User::where('email', 'warga3@rt.com')->first();
        if ($revokedUser) {
            PosyanduPetugas::updateOrCreate(
                ['user_id' => $revokedUser->id, 'tipe_petugas' => 'kader'],
                [
                    'jabatan'             => 'Mantan Kader Posyandu RT 009',
                    'nomor_sk_str'        => 'SK-RW018/KDR-POSY/2025-019',
                    'instansi'            => 'Posyandu RT 009',
                    'posyandu_nama'       => 'Posyandu Cempaka RT 009',
                    'wilayah_rt'          => '009',
                    'tanggal_mulai'       => Carbon::now()->subYears(1)->format('Y-m-d'),
                    'tanggal_selesai'     => Carbon::now()->subMonths(2)->format('Y-m-d'),
                    'status'              => 'dicabut',
                    'status_verifikasi'   => 'terverifikasi',
                    'catatan_penugasan'   => 'Penugasan periode 2025 telah berakhir.',
                    'revoked_at'          => Carbon::now()->subMonths(2),
                    'revoked_by'          => $adminRwId,
                    'revocation_reason'   => 'Masa tugas berakhir dan pindah domisili tugas.',
                    'assigned_by'         => $adminRwId,
                ]
            );
        }
    }
}
