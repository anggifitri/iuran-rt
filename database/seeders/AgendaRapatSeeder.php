<?php

namespace Database\Seeders;

use App\Models\AgendaKehadiran;
use App\Models\AgendaNotulenHistory;
use App\Models\AgendaRapat;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AgendaRapatSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing agenda data cleanly
        AgendaNotulenHistory::query()->delete();
        AgendaKehadiran::query()->delete();
        AgendaRapat::query()->delete();

        $adminRw = User::where('email', 'adminrw@rt.com')->first() ?? User::first();
        $adminRt6 = User::where('email', 'adminrt6@rt.com')->first();
        $adminRt7 = User::where('email', 'adminrt7@rt.com')->first();
        $adminRt8 = User::where('email', 'adminrt8@rt.com')->first();
        $adminRt9 = User::where('email', 'adminrt9@rt.com')->first();
        $adminRt10 = User::where('email', 'adminrt10@rt.com')->first();
        $bendahara = User::where('email', 'bendahara@rt.com')->first();
        $warga1 = User::where('email', 'warga1@rt.com')->first();
        $warga2 = User::where('email', 'warga2@rt.com')->first();
        $warga3 = User::where('email', 'warga3@rt.com')->first();
        $warga4 = User::where('email', 'warga4@rt.com')->first();

        // 1. Agenda Mendatang: Rapat Koordinasi RW & RT
        $ag1 = AgendaRapat::create([
            'judul' => 'Rapat Koordinasi Penataan Lingkungan & Kesiapan Musim Hujan RW 018',
            'jenis_rapat' => 'rw',
            'deskripsi' => 'Koordinasi rutin evaluasi kebersihan saluran drainase, pengecekan lampu jalan umum (PJU), dan jadwal kerja bakti serentak tingkat RW 018 menyambut puncak musim penghujan.',
            'tanggal_waktu' => Carbon::now()->addDays(3)->setTime(19, 30),
            'waktu_selesai' => '21:30:00',
            'lokasi' => 'Balai Warga Utama RW 018 (Depan Lapangan)',
            'penyelenggara' => 'Pengurus RW 018',
            'target_peserta' => 'Seluruh Ketua RT 006 - 010 & Pengurus RW',
            'rt_number' => null,
            'agenda_pembahasan' => "1. Evaluasi laporan kebersihan saluran got & parit dari masing-masing RT\n2. Jadwal kerja bakti massal serentak RW 018\n3. Pengadaan sarana fogging dan pengurasan jentik nyamuk\n4. Penyampaian laporan keuangan kas operasional RW",
            'notulen' => null,
            'hasil_keputusan' => null,
            'status' => 'akan_datang',
            'is_published_notulen' => false,
            'created_by' => $adminRw ? $adminRw->id : 1,
        ]);

        // Kehadiran untuk Agenda 1 (Daftar Undangan)
        $peserta1 = [
            ['user' => $adminRw, 'nama' => 'Dirga Pradipta', 'jabatan' => 'Ketua RW 018', 'rt' => '000', 'status' => 'hadir'],
            ['user' => $bendahara, 'nama' => 'Gibran Nugraha', 'jabatan' => 'Bendahara RW', 'rt' => '001', 'status' => 'hadir'],
            ['user' => $adminRt6, 'nama' => 'Fathir Hartono', 'jabatan' => 'Ketua RT 006', 'rt' => '006', 'status' => 'hadir'],
            ['user' => $adminRt7, 'nama' => 'Gavin Gunawan', 'jabatan' => 'Ketua RT 007', 'rt' => '007', 'status' => 'hadir'],
            ['user' => $adminRt8, 'nama' => 'Rizky Pramudya', 'jabatan' => 'Ketua RT 008', 'rt' => '008', 'status' => 'hadir'],
            ['user' => $adminRt9, 'nama' => 'Rian Sucipto', 'jabatan' => 'Ketua RT 009', 'rt' => '009', 'status' => 'hadir'],
            ['user' => $adminRt10, 'nama' => 'Bagas Kusumah', 'jabatan' => 'Ketua RT 010', 'rt' => '010', 'status' => 'hadir'],
        ];
        foreach ($peserta1 as $p) {
            AgendaKehadiran::create([
                'agenda_id' => $ag1->id,
                'user_id' => $p['user'] ? $p['user']->id : null,
                'nama_peserta' => $p['nama'],
                'jabatan' => $p['jabatan'],
                'rt_number' => $p['rt'],
                'status' => $p['status'],
                'waktu_kehadiran' => null,
            ]);
        }

        // 2. Agenda Selesai & Notulensi Dipublikasikan: Musyawarah Terbuka Warga
        $ag2 = AgendaRapat::create([
            'judul' => 'Musyawarah Terbuka Warga: Program Pengelolaan Sampah Mandiri & Bank Sampah',
            'jenis_rapat' => 'musyawarah_warga',
            'deskripsi' => 'Musyawarah akbar warga mengenai mekanisme operasional Bank Sampah Berkah RW 018, penentuan standar acuan harga tukar, serta pembagian tempat sampah terpilah.',
            'tanggal_waktu' => Carbon::now()->subDays(6)->setTime(9, 0),
            'waktu_selesai' => '11:45:00',
            'lokasi' => 'Gedung Serbaguna Balai Warga RW 018',
            'penyelenggara' => 'Pengurus RW 018 & Satgas Lingkungan',
            'target_peserta' => 'Warga RW 018 (Terbuka)',
            'rt_number' => null,
            'agenda_pembahasan' => "1. Pembukaan oleh Ketua RW 018\n2. Paparan sistem Bank Sampah digital NexaNest oleh Tim Pengembang\n3. Penetapan jadwal timbang sampah rutin dwimingguan\n4. Tanya jawab dan aspirasi perwakilan warga per RT",
            'hasil_keputusan' => "1. Disepakati bahwa seluruh warga RW 018 diwajibkan memilah sampah kering bernilai jual (kardus, plastik, logam, botol kaca) dari rumah.\n2. Penimbangan Bank Sampah Berkah RW 018 dilaksanakan secara terpusat pada hari Minggu ke-2 dan ke-4 setiap bulan pukul 08.00 - 11.00 WIB di Balai RW.\n3. Saldo tabungan sampah otomatis masuk ke akun NexaNest warga dan dapat digunakan langsung untuk memotong pembayaran Iuran Warga bulanan.",
            'notulen' => "Musyawarah dibuka pada pukul 09.05 WIB dengan sambutan dari Bapak Ketua RW. Dilanjutkan pemaparan oleh koordinator bank sampah. Beberapa warga dari RT 007 dan RT 009 mengajukan usulan agar minyak jelantah bekas pakai rumah tangga juga dapat ditampung. Usulan disetujui dengan penetapan standar harga acuan Rp 6.500/liter. Sebanyak 10 perwakilan warga hadir menyepakati berita acara kesepakatan musyawarah.",
            'tindak_lanjut' => 'Pengadaan jerigen penampung jelantah dan timbangan digital duduk kapasitas 150 kg di Balai RW.',
            'pic_tindak_lanjut' => 'Gavin Gunawan (Koordinator Bank Sampah)',
            'tenggat_tindak_lanjut' => Carbon::now()->addDays(10),
            'status' => 'selesai',
            'is_published_notulen' => true,
            'published_at' => Carbon::now()->subDays(5)->setTime(14, 0),
            'created_by' => $adminRw ? $adminRw->id : 1,
        ]);

        $peserta2 = [
            ['user' => $adminRw, 'nama' => 'Dirga Pradipta', 'jabatan' => 'Ketua RW 018', 'rt' => '000', 'status' => 'hadir', 'ket' => 'Pimpinan Musyawarah'],
            ['user' => $adminRt6, 'nama' => 'Fathir Hartono', 'jabatan' => 'Ketua RT 006', 'rt' => '006', 'status' => 'hadir', 'ket' => 'Hadir tepat waktu'],
            ['user' => $adminRt7, 'nama' => 'Gavin Gunawan', 'jabatan' => 'Ketua RT 007', 'rt' => '007', 'status' => 'hadir', 'ket' => 'Hadir sebagai narasumber'],
            ['user' => $adminRt8, 'nama' => 'Rizky Pramudya', 'jabatan' => 'Ketua RT 008', 'rt' => '008', 'status' => 'hadir', 'ket' => 'Hadir tepat waktu'],
            ['user' => $adminRt9, 'nama' => 'Rian Sucipto', 'jabatan' => 'Ketua RT 009', 'rt' => '009', 'status' => 'izin', 'ket' => 'Ada dinas kantor luar kota'],
            ['user' => $adminRt10, 'nama' => 'Bagas Kusumah', 'jabatan' => 'Ketua RT 010', 'rt' => '010', 'status' => 'hadir', 'ket' => 'Hadir tepat waktu'],
            ['user' => $warga1, 'nama' => 'Adrian Pradana', 'jabatan' => 'Perwakilan Warga RT 010', 'rt' => '010', 'status' => 'hadir', 'ket' => 'Hadir'],
            ['user' => $warga2, 'nama' => 'Adrian Sucipto', 'jabatan' => 'Perwakilan Warga RT 007', 'rt' => '007', 'status' => 'hadir', 'ket' => 'Hadir'],
            ['user' => $warga3, 'nama' => 'Adrian Tanjung', 'jabatan' => 'Perwakilan Warga RT 009', 'rt' => '009', 'status' => 'hadir', 'ket' => 'Hadir'],
            ['user' => $warga4, 'nama' => 'Aisha Gunawan', 'jabatan' => 'Koordinator Karang Taruna', 'rt' => '006', 'status' => 'hadir', 'ket' => 'Hadir'],
        ];
        foreach ($peserta2 as $p) {
            AgendaKehadiran::create([
                'agenda_id' => $ag2->id,
                'user_id' => $p['user'] ? $p['user']->id : null,
                'nama_peserta' => $p['nama'],
                'jabatan' => $p['jabatan'],
                'rt_number' => $p['rt'],
                'status' => $p['status'],
                'keterangan' => $p['ket'],
                'waktu_kehadiran' => Carbon::now()->subDays(6)->setTime(9, 5),
            ]);
        }

        AgendaNotulenHistory::create([
            'agenda_id' => $ag2->id,
            'user_id' => $adminRw ? $adminRw->id : 1,
            'action_type' => 'publish',
            'ringkasan' => 'Publikasi resmi hasil musyawarah warga terkait bank sampah dan penetapan jadwal penimbangan rutin.',
            'hasil_keputusan' => $ag2->hasil_keputusan,
            'catatan_perubahan' => 'Notulensi disahkan dan dipublikasikan kepada seluruh warga RW 018.',
            'created_at' => Carbon::now()->subDays(5)->setTime(14, 0),
        ]);

        // 3. Agenda RT 007 Selesai (Draft Notulensi belum dipublikasikan)
        $ag3 = AgendaRapat::create([
            'judul' => 'Rapat Evaluasi Program Kerja & Kamtibmas Lingkungan RT 007',
            'jenis_rapat' => 'koordinasi_rt',
            'deskripsi' => 'Pertemuan internal pengurus RT 007 membahas peningkatan patroli siskamling malam, pengecekan CCTV simpang utama, dan persiapan perayaan HUT lingkungan.',
            'tanggal_waktu' => Carbon::now()->subDays(2)->setTime(20, 0),
            'waktu_selesai' => '22:00:00',
            'lokasi' => 'Pos Ronda RT 007, Jl. Melati Blok B',
            'penyelenggara' => 'Pengurus RT 007',
            'target_peserta' => 'Pengurus & Petugas Ronda RT 007',
            'rt_number' => '007',
            'agenda_pembahasan' => "1. Evaluasi rekap absensi ronda malam bulan berjalan\n2. Perbaikan kabel LAN CCTV outdoor RT 007\n3. Pengelolaan iuran kebersihan RT 007",
            'hasil_keputusan' => "Penyegaran jadwal shift ronda tiap hari Jumat malam ditambah 2 personil warga muda. Teknisi CCTV akan dipanggil pada hari Sabtu untuk perbaikan konektor.",
            'notulen' => "Diskusi berlangsung hangat dengan kehadiran 6 personil pengurus. Masalah utama adalah beberapa titik lampu sorot yang mati di dekat pos kamling.",
            'tindak_lanjut' => 'Pembelian 2 unit lampu sorot LED 50W untuk area pos kamling.',
            'pic_tindak_lanjut' => 'Gavin Gunawan (Ketua RT 007)',
            'tenggat_tindak_lanjut' => Carbon::now()->addDays(5),
            'status' => 'selesai',
            'is_published_notulen' => false, // Masih status DRAFT
            'created_by' => $adminRt7 ? $adminRt7->id : 4,
        ]);

        $peserta3 = [
            ['user' => $adminRt7, 'nama' => 'Gavin Gunawan', 'jabatan' => 'Ketua RT 007', 'rt' => '007', 'status' => 'hadir'],
            ['user' => $warga2, 'nama' => 'Adrian Sucipto', 'jabatan' => 'Koordinator Keamanan RT 007', 'rt' => '007', 'status' => 'hadir'],
            ['user' => null, 'nama' => 'Pak Joko Susilo', 'jabatan' => 'Warga RT 007 Blok B2', 'rt' => '007', 'status' => 'hadir'],
            ['user' => null, 'nama' => 'Bambang Sudarmo', 'jabatan' => 'Warga RT 007 Blok B5', 'rt' => '007', 'status' => 'tidak_hadir'],
        ];
        foreach ($peserta3 as $p) {
            AgendaKehadiran::create([
                'agenda_id' => $ag3->id,
                'user_id' => $p['user'] ? $p['user']->id : null,
                'nama_peserta' => $p['nama'],
                'jabatan' => $p['jabatan'],
                'rt_number' => $p['rt'],
                'status' => $p['status'],
                'waktu_kehadiran' => Carbon::now()->subDays(2)->setTime(20, 10),
            ]);
        }

        // 4. Agenda Mendatang: Sosialisasi Jadwal Posyandu Balita & Lansia
        AgendaRapat::create([
            'judul' => 'Sosialisasi Program Posyandu Balita & Skrining Lansia Sehat RW 018',
            'jenis_rapat' => 'musyawarah_warga',
            'deskripsi' => 'Sosialisasi bersama bidan puskesmas pembantu terkait jadwal imunisasi balita polio & campak serta pemeriksaan gula darah/kolesterol gratis untuk lansia.',
            'tanggal_waktu' => Carbon::now()->addDays(7)->setTime(8, 30),
            'waktu_selesai' => '11:00:00',
            'lokasi' => 'Balai Posyandu & Ruang Pertemuan RT 007',
            'penyelenggara' => 'Kader Posyandu Bougenville RW 018',
            'target_peserta' => 'Warga RW 018 (Terbuka)',
            'rt_number' => '007',
            'agenda_pembahasan' => "1. Penjelasan target vaksinasi balita tahun 2026\n2. Jadwal pemeriksaan antropometri (berat & tinggi badan balita)\n3. Pemberian Makanan Tambahan (PMT) bergizi",
            'status' => 'akan_datang',
            'is_published_notulen' => false,
            'created_by' => $adminRt7 ? $adminRt7->id : 4,
        ]);

        // 5. Agenda Dibatalkan: Pertemuan Rencana Portal Otomatis RT 010
        AgendaRapat::create([
            'judul' => 'Musyawarah Warga Pengadaan Portal Otomatis Jalan Masuk RT 010',
            'jenis_rapat' => 'koordinasi_rt',
            'deskripsi' => 'Rapat pembahasan rencana instalasi portal barrier gate otomatis di gerbang depan RT 010.',
            'tanggal_waktu' => Carbon::now()->subDays(10)->setTime(19, 30),
            'waktu_selesai' => '21:00:00',
            'lokasi' => 'Pos Satpam RT 010',
            'penyelenggara' => 'Pengurus RT 010',
            'target_peserta' => 'Warga RT 010',
            'rt_number' => '010',
            'agenda_pembahasan' => '1. Estimasi RAB pengadaan mesin portal otomatis',
            'status' => 'dibatalkan',
            'is_published_notulen' => false,
            'created_by' => $adminRt10 ? $adminRt10->id : 7,
        ]);
    }
}
