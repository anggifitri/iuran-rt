<?php

namespace Tests\Feature;

use App\Models\AgendaRapat;
use App\Models\Aset;
use App\Models\BankSampah;
use App\Models\Cctv;
use App\Models\FasilitasUmum;
use App\Models\MutasiWarga;
use App\Models\PeminjamanFasilitas;
use App\Models\Pengaduan;
use App\Models\PosyanduAnak;
use App\Models\PosyanduBumil;
use App\Models\SuratPengajuan;
use App\Models\Umkm;
use App\Models\User;
use App\Models\Warga;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAuthorizationSyncTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminRw;
    protected User $adminRt006;
    protected User $adminRt007;
    protected User $userWargaA;
    protected User $userWargaB;
    protected Warga $wargaA;
    protected Warga $wargaB;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Setup Admin RW
        $this->adminRw = User::create([
            'name' => 'Dirga Pradipta',
            'email' => 'adminrw@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'admin_rw',
            'rt_number' => '000',
        ]);

        // 2. Setup Admin RT 006
        $this->adminRt006 = User::create([
            'name' => 'Fathir Hartono',
            'email' => 'adminrt6@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'admin_rt',
            'rt_number' => '006',
        ]);

        // 3. Setup Admin RT 007
        $this->adminRt007 = User::create([
            'name' => 'Gavin Gunawan',
            'email' => 'adminrt7@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'admin_rt',
            'rt_number' => '007',
        ]);

        // 4. Setup Warga A (RT 006)
        $this->wargaA = Warga::create([
            'nama' => 'Warga A Subagyo',
            'blok_rumah' => 'A1/01',
            'rt_number' => '006',
            'rw_number' => '018',
            'nik' => '3275010000000001',
            'no_kk' => '3275010000000011',
            'status_warga' => 'aktif',
            'is_kk' => true,
        ]);

        $this->userWargaA = User::create([
            'name' => 'Warga A Subagyo',
            'email' => 'wargaa@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'warga',
            'rt_number' => '006',
            'nik' => '3275010000000001',
            'no_kk' => '3275010000000011',
        ]);
        $this->wargaA->update(['user_id' => $this->userWargaA->id]);

        // 5. Setup Warga B (RT 007)
        $this->wargaB = Warga::create([
            'nama' => 'Warga B Santoso',
            'blok_rumah' => 'B2/05',
            'rt_number' => '007',
            'rw_number' => '018',
            'nik' => '3275010000000002',
            'no_kk' => '3275010000000022',
            'status_warga' => 'aktif',
            'is_kk' => true,
        ]);

        $this->userWargaB = User::create([
            'name' => 'Warga B Santoso',
            'email' => 'wargab@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'warga',
            'rt_number' => '007',
            'nik' => '3275010000000002',
            'no_kk' => '3275010000000022',
        ]);
        $this->wargaB->update(['user_id' => $this->userWargaB->id]);
    }

    /**
     * Skenario 1: Penerbitan Surat (IDOR & Cross-RT)
     */
    public function test_penerbitan_surat_idor_and_cross_rt(): void
    {
        // Surat selesai milik Warga A (RT 006)
        $suratSelesaiA = SuratPengajuan::create([
            'user_id' => $this->userWargaA->id,
            'rt_number' => '006',
            'jenis_surat' => 'Surat Pengantar KTP',
            'keperluan' => 'Perekaman E-KTP',
            'status' => 'selesai',
        ]);

        // Surat selesai milik Warga B (RT 007)
        $suratSelesaiB = SuratPengajuan::create([
            'user_id' => $this->userWargaB->id,
            'rt_number' => '007',
            'jenis_surat' => 'Surat Keterangan Domisili',
            'keperluan' => 'Pembukaan Rekening Bank',
            'status' => 'selesai',
        ]);

        // 1.1 Warga A mengunduh surat miliknya sendiri -> SUCCESS (HTTP 200)
        $this->actingAs($this->userWargaA)
            ->get(route('surat.pdf', $suratSelesaiA->id))
            ->assertStatus(200);

        // 1.2 Warga A mencoba mengunduh surat milik Warga B -> FORBIDDEN (HTTP 403)
        $this->actingAs($this->userWargaA)
            ->get(route('surat.pdf', $suratSelesaiB->id))
            ->assertStatus(403);

        // Surat pending RT milik Warga A (RT 006) & Warga B (RT 007)
        $suratPendingA = SuratPengajuan::create([
            'user_id' => $this->userWargaA->id,
            'rt_number' => '006',
            'jenis_surat' => 'Surat Pengantar SKCK',
            'keperluan' => 'Melamar Pekerjaan',
            'status' => 'pending_rt',
        ]);

        $suratPendingB = SuratPengajuan::create([
            'user_id' => $this->userWargaB->id,
            'rt_number' => '007',
            'jenis_surat' => 'Surat Pengantar SKCK',
            'keperluan' => 'Pemberkasan CPNS',
            'status' => 'pending_rt',
        ]);

        // 1.3 Admin RT 006 menyetujui surat warga RT 006 -> SUCCESS (Redirect & status updated)
        $this->actingAs($this->adminRt006)
            ->post(route('surat.approve_rt', $suratPendingA->id))
            ->assertRedirect();

        $this->assertSame('pending_rw', $suratPendingA->fresh()->status);

        // 1.4 Admin RT 006 mencoba menyetujui surat warga RT 007 -> FORBIDDEN (HTTP 403)
        $this->actingAs($this->adminRt006)
            ->post(route('surat.approve_rt', $suratPendingB->id))
            ->assertStatus(403);

        // 1.5 Admin RT 006 mencoba membuatkan surat manual untuk warga RT 007 -> FORBIDDEN (HTTP 403)
        $this->actingAs($this->adminRt006)
            ->post(route('surat.store'), [
                'target_user_id' => $this->userWargaB->id,
                'jenis_surat' => 'Surat Pengantar KTP',
                'keperluan' => 'Pembuatan KTP Baru',
            ])
            ->assertStatus(403);
    }

    /**
     * Skenario 2: Isolasi Lintas RT (Pengaduan, Aset, UMKM, Mutasi, CCTV & Agenda)
     */
    public function test_isolasi_lintas_rt_pengaduan_aset_umkm_mutasi_cctv_agenda(): void
    {
        // 2.1 Pengaduan RT 007 & RW 000
        $pengaduan007 = Pengaduan::create([
            'nomor_pengaduan' => 'ADU-007-0001',
            'user_id' => $this->userWargaB->id,
            'title' => 'Lampu PJU Padam di RT 007',
            'content' => 'Lampu jalan mati sejak kemarin malam',
            'category' => 'Tiang Listrik (Kelistrikan)',
            'status' => 'menunggu',
            'rt_number' => '007',
        ]);

        $pengaduanRw = Pengaduan::create([
            'nomor_pengaduan' => 'ADU-000-0001',
            'user_id' => $this->userWargaA->id,
            'title' => 'Portal Gerbang RW Macet',
            'content' => 'Portal otomatis tidak menutup sempurna',
            'category' => 'Fasilitas Umum',
            'status' => 'menunggu',
            'rt_number' => '000',
        ]);

        // Admin RT 006 update status pengaduan RT 007 -> HTTP 403
        $this->actingAs($this->adminRt006)
            ->put(route('pengaduan.update_status', $pengaduan007->id), [
                'status' => 'proses',
            ])
            ->assertStatus(403);

        // Admin RT 006 update status pengaduan RW (000) -> HTTP 403
        $this->actingAs($this->adminRt006)
            ->put(route('pengaduan.update_status', $pengaduanRw->id), [
                'status' => 'proses',
            ])
            ->assertStatus(403);

        // 2.2 Aset RT 007 & Aset RW (000)
        $aset007 = Aset::create([
            'nomor_aset' => 'AST-018-007-001',
            'nama' => 'Hydrant Damkar Gang 7',
            'kategori' => 'Hydrant & Utilitas Air',
            'rt_number' => '007',
            'rw_number' => '018',
            'lokasi' => 'Jl. Mawar RT 007',
            'kondisi' => 'baik',
            'status_pemeliharaan' => 'normal',
        ]);

        $asetRw = Aset::create([
            'nomor_aset' => 'AST-018-000-001',
            'nama' => 'Pos Pantau Gerbang Barat RW',
            'kategori' => 'Fasilitas Umum',
            'rt_number' => '000',
            'rw_number' => '018',
            'lokasi' => 'Gerbang Barat RW 018',
            'kondisi' => 'baik',
            'status_pemeliharaan' => 'normal',
        ]);

        // Admin RT 006 update aset RT 007 -> HTTP 403
        $this->actingAs($this->adminRt006)
            ->put(route('aset.update', $aset007->id), [
                'nama' => 'Hydrant Gang 7 Diubah',
                'kategori' => 'Hydrant & Utilitas Air',
                'rt_number' => '007',
                'lokasi' => 'Jl. Mawar RT 007',
                'kondisi' => 'rusak_ringan',
                'status_pemeliharaan' => 'perlu_tindakan',
            ])
            ->assertStatus(403);

        // Admin RT 006 update aset RW (000) -> HTTP 403
        $this->actingAs($this->adminRt006)
            ->put(route('aset.update', $asetRw->id), [
                'nama' => 'Pos Pantau Diubah',
                'kategori' => 'Fasilitas Umum',
                'rt_number' => '000',
                'lokasi' => 'Gerbang Barat RW 018',
                'kondisi' => 'rusak_ringan',
                'status_pemeliharaan' => 'perlu_tindakan',
            ])
            ->assertStatus(403);

        // 2.3 UMKM Milik Warga RT 007
        $umkm007 = Umkm::create([
            'user_id' => $this->userWargaB->id,
            'nama' => 'Warung Kopi Bu B',
            'pemilik' => 'Warga B Santoso',
            'kategori' => 'Makanan & Minuman',
            'telepon' => '081299887766',
            'jam_operasional' => '07:00 - 21:00 WIB',
            'alamat' => 'Jl. Melati RT 007',
            'deskripsi' => 'Kopi dan aneka gorengan hangat',
            'rt_number' => '007',
            'status_verifikasi' => 'menunggu',
        ]);

        // Admin RT 006 verifikasi UMKM RT 007 -> HTTP 403
        $this->actingAs($this->adminRt006)
            ->post(route('umkm.toggle_verifikasi', $umkm007->id), [
                'status_verifikasi' => 'terverifikasi',
            ])
            ->assertStatus(403);

        // Admin RT 006 edit UMKM RT 007 -> HTTP 403
        $this->actingAs($this->adminRt006)
            ->put(route('umkm.update', $umkm007->id), [
                'nama' => 'Warung Kopi Diubah Oleh RT 006',
                'pemilik' => 'Warga B Santoso',
                'kategori' => 'Makanan & Minuman',
                'telepon' => '081299887766',
                'jam_operasional' => '07:00 - 21:00 WIB',
                'alamat' => 'Jl. Melati RT 007',
                'deskripsi' => 'Deskripsi usaha diubah',
                'rt_number' => '007',
            ])
            ->assertStatus(403);

        // 2.4 Mutasi Warga yang tidak melibatkan RT 006
        $mutasiCross = MutasiWarga::create([
            'warga_id' => $this->wargaB->id,
            'nama_warga' => 'Warga B Santoso',
            'nik' => '3275010000000002',
            'tipe' => 'keluar',
            'rt_asal' => '007',
            'rt_tujuan' => '008',
            'tanggal_mutasi' => '2026-11-20',
            'alasan' => 'Pindah rumah ke lingkungan RT 008',
            'created_by' => $this->userWargaB->id,
        ]);

        // Admin RT 006 melihat detail mutasi RT 007 -> RT 008 -> HTTP 403
        $this->actingAs($this->adminRt006)
            ->get(route('mutasi.show', $mutasiCross->id))
            ->assertStatus(403);

        // 2.5 CCTV Kamera Milik RT 007
        $cctv007 = Cctv::create([
            'kode_kamera' => 'CCTV-018-007-001',
            'nama_kamera' => 'CCTV Gang Melati RT 007',
            'rt_number' => '007',
            'lokasi' => 'Jl. Melati Samping Pos Ronda RT 007',
            'area_pantau' => 'Akses gang pemukiman warga RT 007',
            'status' => 'online',
        ]);

        // Admin RT 006 mengubah status kamera CCTV RT 007 -> HTTP 403
        $this->actingAs($this->adminRt006)
            ->post(route('ronda.update_status_cctv', $cctv007->id), [
                'status' => 'gangguan',
                'catatan_gangguan' => 'Kamera offline tersambar petir',
            ])
            ->assertStatus(403);

        // 2.6 Agenda Rapat dengan jenis rapat tingkat RW
        $agendaResponse = $this->actingAs($this->adminRt006)
            ->post(route('agenda.store'), [
                'judul' => 'Rapat Pleno RW 018 Ilegal',
                'jenis_rapat' => 'rw',
                'tanggal' => '2026-12-05',
                'waktu_mulai' => '09:00',
                'waktu_selesai' => '11:00',
                'lokasi' => 'Balai Warga RW 018',
                'penyelenggara' => 'Admin RT 006',
                'target_peserta' => 'Seluruh Ketua RT',
                'agenda_pembahasan' => 'Pembahasan keuangan RW',
            ]);

        // Harus ditolak dengan validasi error pada 'jenis_rapat'
        $agendaResponse->assertSessionHasErrors('jenis_rapat');
    }

    /**
     * Skenario 3: Privasi Data Medis Posyandu & Peminjaman Fasilitas
     */
    public function test_privasi_data_medis_posyandu_dan_peminjaman_fasilitas(): void
    {
        // 3.1 Data Medis Posyandu Anak
        $anakA = PosyanduAnak::create([
            'warga_id' => $this->wargaA->id,
            'nama_anak' => 'Ananda Balita Keluarga A',
            'nik' => '3275019900000001',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '2024-05-10',
            'umur_bulan' => 18,
            'berat_badan' => 11.5,
            'tinggi_badan' => 84.0,
            'status_tumbuh' => 'Normal',
            'nama_ibu' => 'Ibu Kandung Warga A',
            'nama_ayah' => 'Warga A Subagyo',
            'rt_number' => '006',
            'alamat' => 'Blok A1/01',
        ]);

        $anakB = PosyanduAnak::create([
            'warga_id' => $this->wargaB->id,
            'nama_anak' => 'Ananda Balita Keluarga B',
            'nik' => '3275019900000002',
            'jenis_kelamin' => 'P',
            'tanggal_lahir' => '2024-07-15',
            'umur_bulan' => 16,
            'berat_badan' => 10.2,
            'tinggi_badan' => 81.0,
            'status_tumbuh' => 'Normal',
            'nama_ibu' => 'Ibu Kandung Warga B',
            'nama_ayah' => 'Warga B Santoso',
            'rt_number' => '007',
            'alamat' => 'Blok B2/05',
        ]);

        // Login sebagai Warga A
        $posyanduResp = $this->actingAs($this->userWargaA)->get(route('posyandu.index'));
        $posyanduResp->assertStatus(200);

        // Hanya data balita dari KK Warga A yang tampil
        $posyanduResp->assertSee('Ananda Balita Keluarga A');
        $posyanduResp->assertDontSee('Ananda Balita Keluarga B');

        // 3.2 Peminjaman Fasilitas Detail JSON
        $fasilitas = FasilitasUmum::create([
            'nama' => 'Lapangan Olahraga RW 018',
            'kategori' => 'Sarana Olahraga',
            'lokasi' => 'Komplek Fasilitas Umum RW 018',
            'kapasitas' => 50,
            'status_operasional' => 'tersedia',
            'kondisi_fisik' => 'Baik',
            'jam_operasional' => '08:00 - 22:00 WIB',
        ]);

        $bookingA = PeminjamanFasilitas::create([
            'nomor_pengajuan' => 'PINJAM-202611-0001',
            'fasilitas_id' => $fasilitas->id,
            'user_id' => $this->userWargaA->id,
            'nik_peminjam' => $this->userWargaA->nik,
            'nama_peminjam' => $this->userWargaA->name,
            'rt_number' => '006',
            'nomor_kontak' => '081200000001',
            'tujuan' => 'Turnamen Bulutangkis RT 006',
            'jumlah_peserta' => 12,
            'waktu_mulai' => now()->addDays(2)->setTime(16, 0),
            'waktu_selesai' => now()->addDays(2)->setTime(18, 0),
            'status' => 'disetujui',
        ]);

        $bookingB = PeminjamanFasilitas::create([
            'nomor_pengajuan' => 'PINJAM-202611-0002',
            'fasilitas_id' => $fasilitas->id,
            'user_id' => $this->userWargaB->id,
            'nik_peminjam' => $this->userWargaB->nik,
            'nama_peminjam' => $this->userWargaB->name,
            'rt_number' => '007',
            'nomor_kontak' => '081200000002',
            'tujuan' => 'Acara Syukuran Keluarga B',
            'jumlah_peserta' => 20,
            'waktu_mulai' => now()->addDays(3)->setTime(10, 0),
            'waktu_selesai' => now()->addDays(3)->setTime(12, 0),
            'status' => 'disetujui',
        ]);

        // Warga A mengakses detail booking Warga B -> HTTP 403
        $this->actingAs($this->userWargaA)
            ->getJson(route('fasilitas.show_booking', $bookingB->id))
            ->assertStatus(403);

        // Warga A mengakses detail booking miliknya sendiri -> SUCCESS (HTTP 200)
        $bookingResp = $this->actingAs($this->userWargaA)
            ->getJson(route('fasilitas.show_booking', $bookingA->id));

        $bookingResp->assertStatus(200);
        $bookingResp->assertJsonPath('data.id', $bookingA->id);
        $bookingResp->assertJsonPath('data.nomor_pengajuan', 'PINJAM-202611-0001');
    }

    /**
     * Skenario 4: Navigasi Sidebar & Sinkronisasi Dashboard
     */
    public function test_navigasi_sidebar_dan_sinkronisasi_dashboard(): void
    {
        // 4.1 Admin RT mencoba mengakses /monitoring secara langsung -> HTTP 403
        $this->actingAs($this->adminRt006)
            ->get(route('monitoring.index'))
            ->assertStatus(403);

        // 4.2 Dashboard & Sidebar Admin RT dimuat tanpa error (HTTP 200)
        $dashRt = $this->actingAs($this->adminRt006)->get(route('dashboard'));
        $dashRt->assertStatus(200);

        // Sidebar Admin RT tidak memuat tautan /monitoring
        $dashRt->assertDontSee(route('monitoring.index'));

        // Memuat tautan pengumuman & kalender yang benar
        $dashRt->assertSee(route('pengumuman.index'));
        $dashRt->assertSee(route('kalender.index'));

        // 4.3 Dashboard & Sidebar Warga dimuat tanpa error (HTTP 200)
        $dashWarga = $this->actingAs($this->userWargaA)->get(route('dashboard'));
        $dashWarga->assertStatus(200);

        // Sidebar Warga tidak memuat tautan /lingkungan
        $dashWarga->assertDontSee(route('lingkungan.index'));

        // Memuat tautan pengumuman & kalender
        $dashWarga->assertSee(route('pengumuman.index'));
        $dashWarga->assertSee(route('kalender.index'));
    }

    /**
     * Skenario 5: Uji Regresi Baseline Admin RW (Zero Regression)
     */
    public function test_uji_regresi_baseline_admin_rw_zero_regression(): void
    {
        // 5.1 Admin RW berhasil mengakses Monitoring Wilayah (HTTP 200)
        $this->actingAs($this->adminRw)
            ->get(route('monitoring.index'))
            ->assertStatus(200);

        // Setup surat selesai di RT 006 dan RT 007
        $surat006 = SuratPengajuan::create([
            'user_id' => $this->userWargaA->id,
            'rt_number' => '006',
            'jenis_surat' => 'Surat Pengantar KTP',
            'keperluan' => 'Perekaman KTP',
            'status' => 'selesai',
        ]);

        $surat007 = SuratPengajuan::create([
            'user_id' => $this->userWargaB->id,
            'rt_number' => '007',
            'jenis_surat' => 'Surat Domisili Usaha',
            'keperluan' => 'Izin Usaha',
            'status' => 'selesai',
        ]);

        // 5.2 Admin RW berhasil mengunduh PDF surat warga dari RT mana pun (HTTP 200)
        $this->actingAs($this->adminRw)
            ->get(route('surat.pdf', $surat006->id))
            ->assertStatus(200);

        $this->actingAs($this->adminRw)
            ->get(route('surat.pdf', $surat007->id))
            ->assertStatus(200);

        // 5.3 Admin RW berhasil mengelola aset tingkat RW (000)
        $asetRw = Aset::create([
            'nomor_aset' => 'AST-018-000-0099',
            'nama' => 'Papan Informasi Utama RW 018',
            'kategori' => 'Infrastruktur Lingkungan',
            'rt_number' => '000',
            'rw_number' => '018',
            'lokasi' => 'Gerbang Utama Barat',
            'kondisi' => 'baik',
            'status_pemeliharaan' => 'normal',
        ]);

        $updateRw = $this->actingAs($this->adminRw)
            ->put(route('aset.update', $asetRw->id), [
                'nama' => 'Papan Informasi Utama RW 018 Digital',
                'kategori' => 'Infrastruktur Lingkungan',
                'rt_number' => '000',
                'lokasi' => 'Gerbang Utama Barat Samping Posko',
                'kondisi' => 'baik',
                'status_pemeliharaan' => 'normal',
            ]);

        $updateRw->assertRedirect();
        $this->assertSame('Papan Informasi Utama RW 018 Digital', $asetRw->fresh()->nama);

        // 5.4 Admin RW berhasil melihat detail mutasi lintas RT tanpa hambatan
        $mutasiCross = MutasiWarga::create([
            'warga_id' => $this->wargaB->id,
            'nama_warga' => 'Warga B Santoso',
            'nik' => '3275010000000002',
            'tipe' => 'masuk',
            'rt_asal' => '009',
            'rt_tujuan' => '010',
            'tanggal_mutasi' => '2026-11-21',
            'alasan' => 'Pindah rumah antar-RT',
            'created_by' => $this->userWargaB->id,
        ]);

        $this->actingAs($this->adminRw)
            ->get(route('mutasi.show', $mutasiCross->id))
            ->assertStatus(200);
    }

    /**
     * Skenario 6: Privasi Data & UX Khusus Role Warga (Posyandu, Bank Sampah, Agenda, Fasilitas, Pengaduan).
     */
    public function test_privasi_dan_isolasi_warga_posyandu_bank_sampah_agenda_dan_fasilitas(): void
    {
        // 6.1 Bank Sampah: Warga dilarang ekspor CSV dan cetak laporan (HTTP 403), Admin RW boleh (HTTP 200)
        $this->actingAs($this->userWargaA)
            ->get(route('bank_sampah.export_csv'))
            ->assertStatus(403);

        $this->actingAs($this->userWargaA)
            ->get(route('bank_sampah.cetak_laporan'))
            ->assertStatus(403);

        $this->actingAs($this->adminRw)
            ->get(route('bank_sampah.export_csv'))
            ->assertStatus(200);

        $this->actingAs($this->adminRw)
            ->get(route('bank_sampah.cetak_laporan'))
            ->assertStatus(200);

        // 6.2 Posyandu: Warga A tidak melihat data balita/bumil milik Warga B di index Posyandu
        $balitaB = PosyanduAnak::create([
            'warga_id' => $this->wargaB->id,
            'nama_anak' => 'Bayi Budi',
            'nik' => '3275010000000099',
            'tempat_lahir' => 'Bekasi',
            'tanggal_lahir' => now()->subYears(2)->toDateString(),
            'jenis_kelamin' => 'L',
            'nama_ibu' => 'Ibu Budi',
            'rt_number' => '007',
            'rw_number' => '018',
            'status' => 'aktif',
            'umur_bulan' => 24,
            'berat_badan' => 12.5,
            'tinggi_badan' => 88.0,
        ]);

        $respPosyanduA = $this->actingAs($this->userWargaA)
            ->get(route('posyandu.index'));
        $respPosyanduA->assertStatus(200);
        $anakList = $respPosyanduA->viewData('anak');
        $this->assertFalse($anakList->contains('id', $balitaB->id));

        // 6.3 Agenda Rapat: Agenda RT 007 tidak boleh diakses atau dilihat oleh Warga A (RT 006)
        $agendaRt7 = AgendaRapat::create([
            'judul' => 'Rapat Internal RT 007',
            'jenis_rapat' => 'koordinasi_rt',
            'rt_number' => '007',
            'rw_number' => '018',
            'tanggal_waktu' => now()->addDays(3),
            'lokasi' => 'Balai RT 007',
            'penyelenggara' => 'Pengurus RT 007',
            'status' => 'akan_datang',
            'created_by' => $this->adminRt007->id,
            'target_peserta' => 'Pengurus RT 007',
        ]);

        // Show blocked
        $respShowAgenda = $this->actingAs($this->userWargaA)
            ->get(route('agenda.show', $agendaRt7->id));
        $respShowAgenda->assertRedirect(route('agenda.index'));

        // Index filter query excludes RT 007 internal meeting
        $respAgendaIndex = $this->actingAs($this->userWargaA)
            ->get(route('agenda.index'));
        $respAgendaIndex->assertStatus(200);
        $agendas = $respAgendaIndex->viewData('agendas');
        $this->assertFalse($agendas->contains('id', $agendaRt7->id));

        // 6.4 Fasilitas Umum: Tab laporan dialihkan otomatis ke katalog untuk Warga
        $respFasilitas = $this->actingAs($this->userWargaA)
            ->get(route('fasilitas.index', ['tab' => 'laporan']));
        $respFasilitas->assertStatus(200);
        $this->assertSame('katalog', $respFasilitas->viewData('activeTab'));

        // 6.5 Pengaduan: Tab aset diblokir HTTP 403 untuk Warga
        $this->actingAs($this->userWargaA)
            ->get(route('pengaduan.index', ['tab' => 'aset']))
            ->assertStatus(403);
    }

    /**
     * Skenario 7: Validasi Wajib Domain @gmail.com & Pencarian Email di Database Warga
     */
    public function test_validasi_domain_gmail_dan_pencarian_email_database_warga(): void
    {
        // 7.1 Registrasi Akun: domain non-gmail ditolak
        $respRegFail = $this->post(route('register.post'), [
            'name' => 'Warga Baru Uji',
            'email' => 'warga.baru@yahoo.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'blok_rumah' => 'C3/10',
            'tanggal_lahir' => '1995-05-10',
            'rt_number' => '006',
            'rw_number' => '018',
            'is_kk' => '1',
            'address' => 'Jl. Anggrek RT 006',
        ]);
        $respRegFail->assertSessionHasErrors(['email']);

        // 7.2 Update Akun Profil: domain non-gmail ditolak
        $respProfileFail = $this->actingAs($this->userWargaA)
            ->put(route('profile.update_account'), [
                'email' => 'warga.update@hotmail.com',
                'phone' => '081299998888',
                'current_password' => 'password123',
            ]);
        $respProfileFail->assertSessionHasErrors(['email']);

        // 7.3 Update Akun Profil: domain @gmail.com berhasil
        $respProfileSuccess = $this->actingAs($this->userWargaA)
            ->put(route('profile.update_account'), [
                'email' => 'warga.update.resmi@gmail.com',
                'phone' => '081299998888',
                'current_password' => 'password123',
            ]);
        $respProfileSuccess->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', [
            'id' => $this->userWargaA->id,
            'email' => 'warga.update.resmi@gmail.com',
        ]);

        // 7.4 Pencarian Warga Berdasarkan Email di RT Detail (Admin RT 006)
        $respSearch = $this->actingAs($this->adminRt006)
            ->get(route('warga.rt', ['rt' => '006', 'search' => 'warga.update.resmi@gmail.com']));
        $respSearch->assertStatus(200);
        $wargasView = $respSearch->viewData('wargas');
        $this->assertTrue($wargasView->contains('id', $this->wargaA->id));

        // 7.5 Halaman Detail Warga (Admin RW) menampilkan email akun
        $respShow = $this->actingAs($this->adminRw)
            ->get(route('warga.show', $this->wargaA->id));
        $respShow->assertStatus(200);
        $respShow->assertSee('warga.update.resmi@gmail.com');
    }
}
