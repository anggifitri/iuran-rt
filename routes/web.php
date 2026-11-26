<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WargaController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;

// 1. Halaman utama: tampilkan cover sebelum login
Route::get('/', function () {
    return view('cover');
})->name('home');

// 2. Route Login & Register (Penting agar image_a8278c.png tidak error)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

// Route Verifikasi Email Registrasi (Laravel MustVerifyEmail)
Route::middleware(['auth'])->group(function () {
    Route::get('/email/verify', [AuthController::class, 'showVerificationNotice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->middleware(['signed'])->name('verification.verify');
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail'])->middleware(['throttle:6,1'])->name('verification.send');
});

// Endpoint IoT NodeMCU ESP32 + RFID RC522 (bebas CSRF & session untuk mikrokontroler)
Route::match(['get', 'post'], '/api/rfid/scan', [WargaController::class, 'receiveRfidScan'])->name('rfid.receive');

// Endpoint API Session Management & Token Expiration
Route::prefix('api/auth')->name('api.auth.')->group(function () {
    Route::post('/token/refresh', [\App\Http\Controllers\Api\SessionTokenController::class, 'refreshToken'])->name('token.refresh');
    Route::get('/session/status', [\App\Http\Controllers\Api\SessionTokenController::class, 'sessionStatus'])->name('session.status');
    Route::post('/logout', [\App\Http\Controllers\Api\SessionTokenController::class, 'logout'])->name('logout');
});

// 3. Route yang butuh Login
Route::middleware(['auth'])->group(function () {
    // Dashboard (image_a8a673.png)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Resource (Otomatis bikin warga.index, pembayaran.index dll - image_b29c3a.png)
    Route::get('/warga/rfid/latest', [WargaController::class, 'getLatestRfidScan'])->name('warga.rfid.latest');
    Route::post('/warga/{warga}/rfid', [WargaController::class, 'storeRfidKtp'])->name('warga.rfid.store');
    Route::get('/warga/export', [WargaController::class, 'export'])->name('warga.export');
    Route::get('/warga/search-global', [WargaController::class, 'globalSearch'])->name('warga.search');
    Route::get('/warga/rt/{rt}', [WargaController::class, 'rtDetail'])->name('warga.rt');
    Route::post('/warga/{id}/verifikasi-rw', [WargaController::class, 'verifyRw'])->name('warga.verify_rw');
    Route::resource('warga', WargaController::class);
    // ==========================================
    // SISTEM KEUANGAN TERPADU (KAS, IURAN, LAPORAN)
    // ==========================================
    Route::get('/pembayaran/export', [PembayaranController::class, 'export'])->name('pembayaran.export');
    Route::post('/pembayaran/{id}/cancel', [PembayaranController::class, 'cancel'])->name('pembayaran.cancel');
    Route::resource('pembayaran', PembayaranController::class);

    // Laporan Keuangan Bulanan
    Route::get('/laporan', [PembayaranController::class, 'indexLaporan'])->name('laporan.index');
    Route::get('/laporan/cetak-bulanan', [PembayaranController::class, 'cetakLaporanBulanan'])->name('laporan.cetak_bulanan');
    Route::get('/laporan/export-bulanan', [PembayaranController::class, 'exportLaporanBulanan'])->name('laporan.export_bulanan');
    Route::get('/laporan/cetak', [PembayaranController::class, 'cetakLaporan'])->name('laporan.cetak');

    // Iuran Warga (Tagihan, Pembayaran, Jenis Iuran, Konfirmasi)
    Route::get('/laporan/iuran', [PembayaranController::class, 'laporanIuran'])->name('laporan.iuran');
    Route::get('/laporan/cetak-iuran', [PembayaranController::class, 'cetakLaporanIuran'])->name('laporan.cetak_iuran');
    Route::post('/iuran/jenis', [PembayaranController::class, 'storeJenisIuran'])->name('iuran.jenis.store');
    Route::post('/iuran/store-jenis', [PembayaranController::class, 'storeJenisIuran'])->name('iuran.store_jenis');
    Route::put('/iuran/jenis/{id}', [PembayaranController::class, 'updateJenisIuran'])->name('iuran.jenis.update');
    Route::post('/iuran/jenis/{id}/toggle', [PembayaranController::class, 'toggleJenisIuran'])->name('iuran.jenis.toggle');
    Route::delete('/iuran/jenis/{id}', [PembayaranController::class, 'destroyJenisIuran'])->name('iuran.jenis.destroy');
    Route::post('/iuran/bayar', [PembayaranController::class, 'bayarIuran'])->name('iuran.bayar');
    Route::post('/iuran/confirm/{id}', [PembayaranController::class, 'confirmPembayaranIuran'])->name('iuran.confirm');
    Route::post('/iuran/reject/{id}', [PembayaranController::class, 'rejectPembayaranIuran'])->name('iuran.reject');
    Route::get('/iuran/kuitansi/{id}', [PembayaranController::class, 'kuitansiIuran'])->name('iuran.kuitansi');

    // Notifikasi Tagihan & Layanan
    Route::get('/notifikasi', [\App\Http\Controllers\NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/{id}/read', [\App\Http\Controllers\NotifikasiController::class, 'markAsRead'])->name('notifikasi.read');
    Route::post('/notifikasi/read-all', [\App\Http\Controllers\NotifikasiController::class, 'markAllAsRead'])->name('notifikasi.read_all');

    // Profil Admin RT / RW / Warga (Pusat Pengelolaan Akun & Identitas)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::match(['post', 'put'], '/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/personal', [ProfileController::class, 'updatePersonal'])->name('profile.update_personal');
    Route::put('/profile/account', [ProfileController::class, 'updateAccount'])->name('profile.update_account');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update_password');
    Route::put('/profile/notifications', [ProfileController::class, 'updateNotifications'])->name('profile.update_notifications');
    Route::put('/profile/preferences', [ProfileController::class, 'updatePreferences'])->name('profile.update_preferences');
    Route::post('/profile/verify-phone', [ProfileController::class, 'verifyPhone'])->name('profile.verify_phone');
    Route::post('/profile/logout-other-devices', [ProfileController::class, 'logoutOtherDevices'])->name('profile.logout_other_devices');
    Route::post('/profile/request-deactivation', [ProfileController::class, 'requestDeactivation'])->name('profile.request_deactivation');

    // Layanan Mandiri: Penerbitan Surat, Pengaduan, Posyandu, UMKM
    Route::resource('surat', App\Http\Controllers\SuratPengajuanController::class);
    Route::post('/surat/{surat}/approve-rt', [App\Http\Controllers\SuratPengajuanController::class, 'approveRt'])->name('surat.approve_rt');
    Route::post('/surat/{surat}/approve-rw', [App\Http\Controllers\SuratPengajuanController::class, 'approveRw'])->name('surat.approve_rw');
    Route::get('/surat/{surat}/pdf', [App\Http\Controllers\SuratPengajuanController::class, 'downloadPdf'])->name('surat.pdf');

    // Manajemen Aset & Pengaduan (Pusat Pengelolaan Terpadu)
    Route::get('/pengaduan', [App\Http\Controllers\PengaduanController::class, 'index'])->name('pengaduan.index');
    Route::get('/pengaduan/create', [App\Http\Controllers\PengaduanController::class, 'create'])->name('pengaduan.create');
    Route::post('/pengaduan', [App\Http\Controllers\PengaduanController::class, 'store'])->name('pengaduan.store');
    Route::get('/pengaduan/{id}', [App\Http\Controllers\PengaduanController::class, 'show'])->name('pengaduan.show');
    Route::put('/pengaduan/{id}/status', [App\Http\Controllers\PengaduanController::class, 'updateStatus'])->name('pengaduan.update_status');

    // Sub-Fitur Aset Lingkungan & QR Code
    Route::get('/aset/{id}', [App\Http\Controllers\PengaduanController::class, 'showAset'])->name('aset.show');
    Route::post('/aset', [App\Http\Controllers\PengaduanController::class, 'storeAset'])->name('aset.store');
    Route::put('/aset/{id}', [App\Http\Controllers\PengaduanController::class, 'updateAset'])->name('aset.update');
    Route::get('/aset/find-by-code/{code}', [App\Http\Controllers\PengaduanController::class, 'findAsetByCode'])->name('aset.find_by_code');
    Route::post('/aset/{id}/pemeriksaan', [App\Http\Controllers\PengaduanController::class, 'storeAsetPemeriksaan'])->name('aset.pemeriksaan.store');
    Route::post('/dokumentasi/upload', [App\Http\Controllers\PengaduanController::class, 'storeDokumentasi'])->name('dokumentasi.store');

    // Ekosistem Posyandu & KMS Digital RW 018
    Route::get('/posyandu', [App\Http\Controllers\PosyanduController::class, 'index'])->name('posyandu.index');
    Route::get('/posyandu/create', [App\Http\Controllers\PosyanduController::class, 'create'])->name('posyandu.create');
    Route::post('/posyandu', [App\Http\Controllers\PosyanduController::class, 'store'])->name('posyandu.store');
    Route::post('/posyandu/jadwal', [App\Http\Controllers\PosyanduController::class, 'storeJadwal'])->name('posyandu.jadwal.store');
    Route::put('/posyandu/jadwal/{posyandu}', [App\Http\Controllers\PosyanduController::class, 'updateJadwal'])->name('posyandu.jadwal.update');
    Route::delete('/posyandu/jadwal/{posyandu}', [App\Http\Controllers\PosyanduController::class, 'destroyJadwal'])->name('posyandu.jadwal.destroy');

    // Balita: Profil Individual, KMS Digital, Pemeriksaan & Imunisasi
    Route::get('/posyandu/balita/{anak}', [App\Http\Controllers\PosyanduController::class, 'showBalita'])->name('posyandu.balita.show');
    Route::post('/posyandu/balita', [App\Http\Controllers\PosyanduController::class, 'storeBalita'])->name('posyandu.balita.store');
    Route::put('/posyandu/balita/{anak}', [App\Http\Controllers\PosyanduController::class, 'updateBalita'])->name('posyandu.balita.update');
    Route::post('/posyandu/balita/{anak}/pemeriksaan', [App\Http\Controllers\PosyanduController::class, 'storePemeriksaanBalita'])->name('posyandu.balita.pemeriksaan.store');
    Route::delete('/posyandu/balita/{anak}/pemeriksaan/{pemeriksaan}', [App\Http\Controllers\PosyanduController::class, 'destroyPemeriksaanBalita'])->name('posyandu.balita.pemeriksaan.destroy');
    Route::post('/posyandu/balita/{anak}/imunisasi', [App\Http\Controllers\PosyanduController::class, 'storeImunisasiBalita'])->name('posyandu.balita.imunisasi.store');
    Route::put('/posyandu/balita/{anak}/imunisasi/{imunisasi}', [App\Http\Controllers\PosyanduController::class, 'updateImunisasiBalita'])->name('posyandu.balita.imunisasi.update');

    // Ibu Hamil: Profil Individual & Timeline Pemeriksaan ANC
    Route::get('/posyandu/bumil/{bumil}', [App\Http\Controllers\PosyanduController::class, 'showBumil'])->name('posyandu.bumil.show');
    Route::post('/posyandu/bumil', [App\Http\Controllers\PosyanduController::class, 'storeBumil'])->name('posyandu.bumil.store');
    Route::put('/posyandu/bumil/{bumil}', [App\Http\Controllers\PosyanduController::class, 'updateBumil'])->name('posyandu.bumil.update');
    Route::post('/posyandu/bumil/{bumil}/pemeriksaan', [App\Http\Controllers\PosyanduController::class, 'storePemeriksaanBumil'])->name('posyandu.bumil.pemeriksaan.store');
    Route::delete('/posyandu/bumil/{bumil}/pemeriksaan/{pemeriksaan}', [App\Http\Controllers\PosyanduController::class, 'destroyPemeriksaanBumil'])->name('posyandu.bumil.pemeriksaan.destroy');

    // Validasi & Verifikasi Medis Tenaga Kesehatan (Nakes)
    Route::post('/posyandu/balita/{anak}/pemeriksaan/{pemeriksaan}/verify-nakes', [App\Http\Controllers\PosyanduController::class, 'verifyNakesBalita'])->name('posyandu.balita.pemeriksaan.verify_nakes');
    Route::post('/posyandu/bumil/{bumil}/pemeriksaan/{pemeriksaan}/verify-nakes', [App\Http\Controllers\PosyanduController::class, 'verifyNakesBumil'])->name('posyandu.bumil.pemeriksaan.verify_nakes');

    // Manajemen Penugasan Petugas Posyandu (Admin RW Only)
    Route::get('/posyandu/petugas', [App\Http\Controllers\PosyanduController::class, 'petugasIndex'])->name('posyandu.petugas.index');
    Route::post('/posyandu/petugas', [App\Http\Controllers\PosyanduController::class, 'storePetugas'])->name('posyandu.petugas.store');
    Route::put('/posyandu/petugas/{petugas}', [App\Http\Controllers\PosyanduController::class, 'updatePetugas'])->name('posyandu.petugas.update');
    Route::post('/posyandu/petugas/{petugas}/verify', [App\Http\Controllers\PosyanduController::class, 'verifyPetugas'])->name('posyandu.petugas.verify');
    Route::post('/posyandu/petugas/{petugas}/revoke', [App\Http\Controllers\PosyanduController::class, 'revokePetugas'])->name('posyandu.petugas.revoke');
    Route::delete('/posyandu/petugas/{petugas}', [App\Http\Controllers\PosyanduController::class, 'destroyPetugas'])->name('posyandu.petugas.destroy');
    Route::get('/umkm/categories', [App\Http\Controllers\UmkmController::class, 'categories'])->name('umkm.categories');
    Route::resource('umkm', App\Http\Controllers\UmkmController::class);
    Route::post('/umkm/{umkm}/toggle-verifikasi', [App\Http\Controllers\UmkmController::class, 'toggleVerifikasi'])->name('umkm.toggle_verifikasi');
    Route::post('/umkm/{umkm}/toggle-favorit', [App\Http\Controllers\UmkmController::class, 'toggleFavorit'])->name('umkm.toggle_favorit');
    Route::post('/umkm/{umkm}/produk', [App\Http\Controllers\UmkmController::class, 'storeProduk'])->name('umkm.produk.store');
    Route::put('/umkm/{umkm}/produk/{produk}', [App\Http\Controllers\UmkmController::class, 'updateProduk'])->name('umkm.produk.update');
    Route::delete('/umkm/{umkm}/produk/{produk}', [App\Http\Controllers\UmkmController::class, 'destroyProduk'])->name('umkm.produk.destroy');
    Route::post('/umkm/{umkm}/ulasan', [App\Http\Controllers\UmkmController::class, 'storeUlasan'])->name('umkm.ulasan.store');
    Route::put('/umkm/{umkm}/ulasan/{ulasan}', [App\Http\Controllers\UmkmController::class, 'updateUlasan'])->name('umkm.ulasan.update');
    Route::post('/umkm/{umkm}/ulasan/{ulasan}/reply', [App\Http\Controllers\UmkmController::class, 'replyUlasan'])->name('umkm.ulasan.reply');
    Route::post('/umkm/{umkm}/ulasan/{ulasan}/report', [App\Http\Controllers\UmkmController::class, 'reportUlasan'])->name('umkm.ulasan.report');
    Route::post('/umkm/{umkm}/ulasan/{ulasan}/moderate', [App\Http\Controllers\UmkmController::class, 'moderateUlasan'])->name('umkm.ulasan.moderate');

    // ==========================================
    // MODUL LENGKAP ADMIN RW & PEMERINTAHAN RT/RW
    // ==========================================

    // 1. Monitoring Wilayah
    Route::get('/monitoring', [App\Http\Controllers\MonitoringWilayahController::class, 'index'])->name('monitoring.index');
    Route::get('/monitoring/rt/{rt}', [App\Http\Controllers\MonitoringWilayahController::class, 'detailRt'])->name('monitoring.rt_detail');

    // 2. Audit dan Riwayat Aktivitas
    Route::get('/audit', [App\Http\Controllers\AuditLogController::class, 'index'])->name('audit.index');
    Route::get('/audit/export/pdf', [App\Http\Controllers\AuditLogController::class, 'exportPdf'])->name('audit.export_pdf');
    Route::get('/audit/export/excel', [App\Http\Controllers\AuditLogController::class, 'exportExcel'])->name('audit.export_excel');
    Route::get('/audit/{id}/diff', [App\Http\Controllers\AuditLogController::class, 'diffDetail'])->name('audit.diff');

    // 3. Verifikasi Warga
    Route::get('/verifikasi', [App\Http\Controllers\VerifikasiWargaController::class, 'index'])->name('verifikasi.index');
    Route::get('/verifikasi/{id}', [App\Http\Controllers\VerifikasiWargaController::class, 'show'])->name('verifikasi.show');
    Route::post('/verifikasi/{id}/status', [App\Http\Controllers\VerifikasiWargaController::class, 'updateStatus'])->name('verifikasi.update_status');

    // 4. Mutasi Warga
    Route::resource('mutasi', App\Http\Controllers\MutasiWargaController::class)->only(['index', 'create', 'store', 'show']);

    // 5. Laporan RT & Pemeriksaan RW
    Route::get('/laporan-rt', [App\Http\Controllers\LaporanRtController::class, 'index'])->name('laporan_rt.index');
    Route::get('/laporan-rt/create', [App\Http\Controllers\LaporanRtController::class, 'create'])->name('laporan_rt.create');
    Route::post('/laporan-rt', [App\Http\Controllers\LaporanRtController::class, 'store'])->name('laporan_rt.store');
    Route::get('/laporan-rt/{id}', [App\Http\Controllers\LaporanRtController::class, 'show'])->name('laporan_rt.show');
    Route::put('/laporan-rt/{id}', [App\Http\Controllers\LaporanRtController::class, 'update'])->name('laporan_rt.update');
    Route::post('/laporan-rt/{id}/review', [App\Http\Controllers\LaporanRtController::class, 'review'])->name('laporan_rt.review');

    // 6. Ronda dan Keamanan
    Route::get('/ronda', [App\Http\Controllers\RondaKeamananController::class, 'index'])->name('ronda.index');
    Route::post('/ronda/jadwal', [App\Http\Controllers\RondaKeamananController::class, 'storeJadwal'])->name('ronda.store_jadwal');
    Route::get('/ronda/jadwal/{id}', [App\Http\Controllers\RondaKeamananController::class, 'showJadwal'])->name('ronda.show_jadwal');
    Route::put('/ronda/jadwal/{id}', [App\Http\Controllers\RondaKeamananController::class, 'updateJadwal'])->name('ronda.update_jadwal');
    Route::post('/ronda/jadwal/{id}/status', [App\Http\Controllers\RondaKeamananController::class, 'updateStatusJadwal'])->name('ronda.update_status_jadwal');
    Route::delete('/ronda/jadwal/{id}', [App\Http\Controllers\RondaKeamananController::class, 'destroyJadwal'])->name('ronda.destroy_jadwal');
    Route::post('/ronda/keamanan', [App\Http\Controllers\RondaKeamananController::class, 'storeLaporanKeamanan'])->name('ronda.store_laporan_keamanan');
    Route::get('/ronda/keamanan/{id}', [App\Http\Controllers\RondaKeamananController::class, 'showLaporanKeamanan'])->name('ronda.show_laporan_keamanan');
    Route::post('/ronda/keamanan/{id}/update', [App\Http\Controllers\RondaKeamananController::class, 'updateLaporanKeamanan'])->name('ronda.update_laporan_keamanan');
    Route::post('/ronda/keamanan/{id}/link-cctv', [App\Http\Controllers\RondaKeamananController::class, 'linkCctv'])->name('ronda.link_cctv');
    Route::post('/ronda/cctv', [App\Http\Controllers\RondaKeamananController::class, 'storeCctv'])->name('ronda.store_cctv');
    Route::get('/ronda/cctv/{id}', [App\Http\Controllers\RondaKeamananController::class, 'showCctv'])->name('ronda.show_cctv');
    Route::put('/ronda/cctv/{id}', [App\Http\Controllers\RondaKeamananController::class, 'updateCctv'])->name('ronda.update_cctv');
    Route::post('/ronda/cctv/{id}/status', [App\Http\Controllers\RondaKeamananController::class, 'updateStatusCctv'])->name('ronda.update_status_cctv');
    Route::delete('/ronda/cctv/{id}', [App\Http\Controllers\RondaKeamananController::class, 'destroyCctv'])->name('ronda.destroy_cctv');
    Route::post('/ronda/cctv/recording/{id}/lock', [App\Http\Controllers\RondaKeamananController::class, 'lockRecording'])->name('ronda.lock_recording');

    // 7. Peminjaman Fasilitas Umum
    Route::get('/fasilitas', [App\Http\Controllers\FasilitasUmumController::class, 'index'])->name('fasilitas.index');
    Route::post('/fasilitas/store', [App\Http\Controllers\FasilitasUmumController::class, 'storeFasilitas'])->name('fasilitas.store_fasilitas');
    Route::get('/fasilitas/{id}/detail', [App\Http\Controllers\FasilitasUmumController::class, 'showFasilitas'])->name('fasilitas.show_fasilitas');
    Route::put('/fasilitas/{id}', [App\Http\Controllers\FasilitasUmumController::class, 'updateFasilitas'])->name('fasilitas.update_fasilitas');
    Route::post('/fasilitas/{id}/toggle-status', [App\Http\Controllers\FasilitasUmumController::class, 'toggleStatusFasilitas'])->name('fasilitas.toggle_status');
    Route::delete('/fasilitas/{id}', [App\Http\Controllers\FasilitasUmumController::class, 'destroyFasilitas'])->name('fasilitas.destroy_fasilitas');
    Route::post('/fasilitas/booking', [App\Http\Controllers\FasilitasUmumController::class, 'storeBooking'])->name('fasilitas.store_booking');
    Route::get('/fasilitas/booking/{id}', [App\Http\Controllers\FasilitasUmumController::class, 'showBooking'])->name('fasilitas.show_booking');
    Route::post('/fasilitas/booking/{id}/review', [App\Http\Controllers\FasilitasUmumController::class, 'reviewBooking'])->name('fasilitas.review');
    Route::post('/fasilitas/booking/{id}/batal', [App\Http\Controllers\FasilitasUmumController::class, 'batalBooking'])->name('fasilitas.batal');
    Route::post('/fasilitas/pemeriksaan/{id}', [App\Http\Controllers\FasilitasUmumController::class, 'simpanPemeriksaan'])->name('fasilitas.simpan_pemeriksaan');

    // 8. Agenda & Rapat RW
    Route::resource('agenda', App\Http\Controllers\AgendaRapatController::class);
    Route::post('/agenda/{id}/status', [App\Http\Controllers\AgendaRapatController::class, 'updateStatus'])->name('agenda.update_status');
    Route::post('/agenda/{id}/kehadiran', [App\Http\Controllers\AgendaRapatController::class, 'storeKehadiran'])->name('agenda.store_kehadiran');
    Route::post('/agenda/{id}/kehadiran/{kehadiranId}/toggle', [App\Http\Controllers\AgendaRapatController::class, 'toggleKehadiran'])->name('agenda.toggle_kehadiran');
    Route::delete('/agenda/{id}/kehadiran/{kehadiranId}', [App\Http\Controllers\AgendaRapatController::class, 'destroyKehadiran'])->name('agenda.destroy_kehadiran');
    Route::post('/agenda/{id}/notulen', [App\Http\Controllers\AgendaRapatController::class, 'updateNotulen'])->name('agenda.update_notulen');
    Route::get('/agenda-check-collision', [App\Http\Controllers\AgendaRapatController::class, 'checkCollision'])->name('agenda.check_collision');

    // 9. Pusat Informasi & Pengumuman
    Route::post('/pengumuman/mark-all-read', [App\Http\Controllers\PengumumanController::class, 'markAllRead'])->name('pengumuman.mark_all_read');
    Route::post('/pengumuman/{id}/archive', [App\Http\Controllers\PengumumanController::class, 'archive'])->name('pengumuman.archive');
    Route::post('/pengumuman/{id}/publish-now', [App\Http\Controllers\PengumumanController::class, 'publishNow'])->name('pengumuman.publish_now');
    Route::post('/pengumuman/{id}/rsvp', [App\Http\Controllers\PengumumanController::class, 'rsvp'])->name('pengumuman.rsvp');
    Route::post('/pengumuman/{id}/read', [App\Http\Controllers\PengumumanController::class, 'markRead'])->name('pengumuman.read');
    Route::resource('pengumuman', App\Http\Controllers\PengumumanController::class);

    // 10. Laporan Lingkungan
    Route::get('/lingkungan', [App\Http\Controllers\LaporanLingkunganController::class, 'index'])->name('lingkungan.index');
    Route::get('/lingkungan/create', [App\Http\Controllers\LaporanLingkunganController::class, 'create'])->name('lingkungan.create');
    Route::post('/lingkungan', [App\Http\Controllers\LaporanLingkunganController::class, 'store'])->name('lingkungan.store');
    Route::post('/lingkungan/periode/setting', [App\Http\Controllers\LaporanLingkunganController::class, 'updatePeriodeSetting'])->name('lingkungan.update_periode');
    Route::get('/lingkungan/{id}', [App\Http\Controllers\LaporanLingkunganController::class, 'show'])->name('lingkungan.show');
    Route::get('/lingkungan/{id}/edit', [App\Http\Controllers\LaporanLingkunganController::class, 'edit'])->name('lingkungan.edit');
    Route::put('/lingkungan/{id}', [App\Http\Controllers\LaporanLingkunganController::class, 'update'])->name('lingkungan.update');
    Route::post('/lingkungan/{id}/evaluasi', [App\Http\Controllers\LaporanLingkunganController::class, 'evaluasi'])->name('lingkungan.evaluasi');
    Route::get('/lingkungan/{id}/cetak', [App\Http\Controllers\LaporanLingkunganController::class, 'cetak'])->name('lingkungan.cetak');

    // 11. Edukasi & Bank Sampah
    Route::get('/bank-sampah', [App\Http\Controllers\BankSampahController::class, 'index'])->name('bank_sampah.index');
    Route::post('/bank-sampah/setoran', [App\Http\Controllers\BankSampahController::class, 'storeSetoran'])->name('bank_sampah.store_setoran');
    Route::get('/bank-sampah/setoran/{id}', [App\Http\Controllers\BankSampahController::class, 'showSetoran'])->name('bank_sampah.show_setoran');
    Route::post('/bank-sampah/setoran/{id}/verifikasi', [App\Http\Controllers\BankSampahController::class, 'verifikasiSetoran'])->name('bank_sampah.verifikasi_setoran');
    Route::post('/bank-sampah/kategori', [App\Http\Controllers\BankSampahController::class, 'storeKategori'])->name('bank_sampah.store_kategori');
    Route::put('/bank-sampah/kategori/{id}', [App\Http\Controllers\BankSampahController::class, 'updateKategori'])->name('bank_sampah.update_kategori');
    Route::post('/bank-sampah/kategori/{id}/toggle', [App\Http\Controllers\BankSampahController::class, 'toggleKategori'])->name('bank_sampah.toggle_kategori');
    Route::post('/bank-sampah/penarikan', [App\Http\Controllers\BankSampahController::class, 'storePenarikan'])->name('bank_sampah.store_penarikan');
    Route::get('/bank-sampah/warga/{id}/tabungan', [App\Http\Controllers\BankSampahController::class, 'detailTabunganWarga'])->name('bank_sampah.detail_tabungan');
    Route::post('/bank-sampah/edukasi', [App\Http\Controllers\BankSampahController::class, 'storeEdukasi'])->name('bank_sampah.store_edukasi');
    Route::put('/bank-sampah/edukasi/{id}', [App\Http\Controllers\BankSampahController::class, 'updateEdukasi'])->name('bank_sampah.update_edukasi');
    Route::delete('/bank-sampah/edukasi/{id}', [App\Http\Controllers\BankSampahController::class, 'destroyEdukasi'])->name('bank_sampah.destroy_edukasi');
    Route::get('/bank-sampah/laporan/export-csv', [App\Http\Controllers\BankSampahController::class, 'exportCsv'])->name('bank_sampah.export_csv');
    Route::get('/bank-sampah/laporan/cetak', [App\Http\Controllers\BankSampahController::class, 'cetakLaporan'])->name('bank_sampah.cetak_laporan');

    // 12. Kalender Kegiatan Terpadu
    Route::get('/kalender', [App\Http\Controllers\KalenderKegiatanController::class, 'index'])->name('kalender.index');
});

// =========================================================================
// SUPER ADMIN — NEXANEST PLATFORM CONTROL CENTER
// =========================================================================
Route::middleware(['auth', 'super_admin'])->prefix('super-admin')->name('super_admin.')->group(function () {
    // 1. Platform Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\SuperAdmin\SuperAdminDashboardController::class, 'index'])->name('dashboard');

    // 2. Role Switcher Engine (Prioritas Utama)
    Route::post('/role-switch', [\App\Http\Controllers\SuperAdmin\RoleSwitcherController::class, 'switch'])->name('role_switch.switch');
    Route::get('/role-switch/warga-by-rt/{rt}', [\App\Http\Controllers\SuperAdmin\RoleSwitcherController::class, 'getWargaByRt'])->name('role_switch.warga_by_rt');
    Route::post('/role-switch/toggle-mode', [\App\Http\Controllers\SuperAdmin\RoleSwitcherController::class, 'toggleMode'])->name('role_switch.toggle_mode');
    Route::post('/role-switch/exit', [\App\Http\Controllers\SuperAdmin\RoleSwitcherController::class, 'exit'])->name('role_switch.exit');

    // 3. Manajemen Wilayah (Multi-RW & RT RW 018)
    Route::get('/wilayah', [\App\Http\Controllers\SuperAdmin\WilayahRwController::class, 'index'])->name('wilayah.index');
    Route::post('/wilayah', [\App\Http\Controllers\SuperAdmin\WilayahRwController::class, 'store'])->name('wilayah.store');
    Route::put('/wilayah/{id}', [\App\Http\Controllers\SuperAdmin\WilayahRwController::class, 'update'])->name('wilayah.update');
    Route::post('/wilayah/{id}/toggle-status', [\App\Http\Controllers\SuperAdmin\WilayahRwController::class, 'toggleStatus'])->name('wilayah.toggle_status');
    Route::delete('/wilayah/{id}', [\App\Http\Controllers\SuperAdmin\WilayahRwController::class, 'destroy'])->name('wilayah.destroy');
    Route::post('/wilayah/rt', [\App\Http\Controllers\SuperAdmin\WilayahRwController::class, 'storeRt'])->name('wilayah.rt.store');
    Route::put('/wilayah/rt/{id}', [\App\Http\Controllers\SuperAdmin\WilayahRwController::class, 'updateRt'])->name('wilayah.rt.update');
    Route::post('/wilayah/rt/{id}/toggle-status', [\App\Http\Controllers\SuperAdmin\WilayahRwController::class, 'toggleRtStatus'])->name('wilayah.rt.toggle_status');
    Route::post('/wilayah/assign-admin', [\App\Http\Controllers\SuperAdmin\WilayahRwController::class, 'assignAdmin'])->name('wilayah.assign_admin');

    // 4. Pengajuan RW Baru
    Route::get('/pengajuan', [\App\Http\Controllers\SuperAdmin\WilayahRwController::class, 'pengajuanIndex'])->name('pengajuan.index');
    Route::post('/pengajuan/{id}/approve', [\App\Http\Controllers\SuperAdmin\WilayahRwController::class, 'approvePengajuan'])->name('pengajuan.approve');
    Route::post('/pengajuan/{id}/revision', [\App\Http\Controllers\SuperAdmin\WilayahRwController::class, 'requestRevision'])->name('pengajuan.revision');
    Route::post('/pengajuan/{id}/reject', [\App\Http\Controllers\SuperAdmin\WilayahRwController::class, 'rejectPengajuan'])->name('pengajuan.reject');

    // 5. Manajemen Akun Pengurus & Akses Platform
    Route::get('/accounts', [\App\Http\Controllers\SuperAdmin\AccountPlatformController::class, 'index'])->name('accounts.index');
    Route::post('/accounts', [\App\Http\Controllers\SuperAdmin\AccountPlatformController::class, 'store'])->name('accounts.store');
    Route::get('/accounts/{id}', [\App\Http\Controllers\SuperAdmin\AccountPlatformController::class, 'show'])->name('accounts.show');
    Route::put('/accounts/{id}', [\App\Http\Controllers\SuperAdmin\AccountPlatformController::class, 'update'])->name('accounts.update');
    Route::post('/accounts/{id}/toggle-status', [\App\Http\Controllers\SuperAdmin\AccountPlatformController::class, 'toggleStatus'])->name('accounts.toggle_status');
    Route::post('/accounts/{id}/reset-password', [\App\Http\Controllers\SuperAdmin\AccountPlatformController::class, 'resetPassword'])->name('accounts.reset_password');
    Route::post('/accounts/{id}/revoke-session', [\App\Http\Controllers\SuperAdmin\AccountPlatformController::class, 'revokeSession'])->name('accounts.revoke_session');
    Route::post('/accounts/reassign', [\App\Http\Controllers\SuperAdmin\AccountPlatformController::class, 'reassign'])->name('accounts.reassign');
    Route::post('/accounts/unlock-sensitive', [\App\Http\Controllers\SuperAdmin\AccountPlatformController::class, 'unlockSensitive'])->name('accounts.unlock_sensitive');
    Route::get('/accounts/sensitive-logs', [\App\Http\Controllers\SuperAdmin\AccountPlatformController::class, 'sensitiveLogs'])->name('sensitive_logs');

    // 6. Audit & Keamanan Platform
    Route::get('/audit', [\App\Http\Controllers\SuperAdmin\AuditPlatformController::class, 'index'])->name('audit.index');
    Route::get('/audit/export-csv', [\App\Http\Controllers\SuperAdmin\AuditPlatformController::class, 'exportCsv'])->name('audit.export_csv');

    // 7. Monitoring Sistem
    Route::get('/monitoring', [\App\Http\Controllers\SuperAdmin\SystemMonitoringController::class, 'index'])->name('monitoring');
    Route::get('/monitoring/api/metrics', [\App\Http\Controllers\SuperAdmin\SystemMonitoringController::class, 'apiMetrics'])->name('monitoring.metrics');
    Route::post('/monitoring/integrity-check', [\App\Http\Controllers\SuperAdmin\SystemMonitoringController::class, 'integrityCheck'])->name('monitoring.integrity_check');

    // 8. Manajemen Modul Platform
    Route::get('/modules', [\App\Http\Controllers\SuperAdmin\ModuleConfigController::class, 'index'])->name('modules.index');
    Route::post('/modules/{id}/toggle-global', [\App\Http\Controllers\SuperAdmin\ModuleConfigController::class, 'toggleGlobal'])->name('modules.toggle_global');
    Route::post('/modules/{id}/update-rw', [\App\Http\Controllers\SuperAdmin\ModuleConfigController::class, 'updateRwAccess'])->name('modules.update_rw');

    // 9. Broadcast & Notifikasi Platform
    Route::get('/notifications', [\App\Http\Controllers\SuperAdmin\NotificationPlatformController::class, 'index'])->name('notifications.index');
    Route::post('/notifications', [\App\Http\Controllers\SuperAdmin\NotificationPlatformController::class, 'store'])->name('notifications.store');
    Route::delete('/notifications/{id}', [\App\Http\Controllers\SuperAdmin\NotificationPlatformController::class, 'destroy'])->name('notifications.destroy');

    // 10. Backup & Pemulihan
    Route::get('/backup', [\App\Http\Controllers\SuperAdmin\BackupPlatformController::class, 'index'])->name('backup.index');
    Route::post('/backup/create', [\App\Http\Controllers\SuperAdmin\BackupPlatformController::class, 'createBackup'])->name('backup.create');
    Route::get('/backup/{id}/download', [\App\Http\Controllers\SuperAdmin\BackupPlatformController::class, 'download'])->name('backup.download');

    // 11. Pengaturan Global Platform & Control Center (5 Tab Terpadu)
    Route::get('/settings', [\App\Http\Controllers\SuperAdmin\SettingsPlatformController::class, 'index'])->name('settings.index');
    Route::post('/settings', [\App\Http\Controllers\SuperAdmin\SettingsPlatformController::class, 'updateGeneral'])->name('settings.update');
    Route::post('/settings/general', [\App\Http\Controllers\SuperAdmin\SettingsPlatformController::class, 'updateGeneral'])->name('settings.general.update');
    Route::post('/settings/maintenance/toggle', [\App\Http\Controllers\SuperAdmin\SettingsPlatformController::class, 'toggleMaintenance'])->name('settings.maintenance.toggle');
    Route::post('/settings/modules/{id}/toggle-global', [\App\Http\Controllers\SuperAdmin\SettingsPlatformController::class, 'toggleModuleGlobal'])->name('settings.modules.toggle_global');
    Route::post('/settings/modules/{id}/toggle-rw', [\App\Http\Controllers\SuperAdmin\SettingsPlatformController::class, 'toggleModuleRw'])->name('settings.modules.toggle_rw');
    Route::post('/settings/notifications', [\App\Http\Controllers\SuperAdmin\SettingsPlatformController::class, 'storeNotification'])->name('settings.notifications.store');
    Route::put('/settings/notifications/{id}', [\App\Http\Controllers\SuperAdmin\SettingsPlatformController::class, 'updateNotification'])->name('settings.notifications.update');
    Route::delete('/settings/notifications/{id}', [\App\Http\Controllers\SuperAdmin\SettingsPlatformController::class, 'destroyNotification'])->name('settings.notifications.destroy');
    Route::post('/settings/backup/create', [\App\Http\Controllers\SuperAdmin\SettingsPlatformController::class, 'createBackup'])->name('settings.backup.create');
    Route::post('/settings/backup/schedule', [\App\Http\Controllers\SuperAdmin\SettingsPlatformController::class, 'updateBackupSchedule'])->name('settings.backup.schedule');
    Route::post('/settings/backup/{id}/restore', [\App\Http\Controllers\SuperAdmin\SettingsPlatformController::class, 'restoreBackup'])->name('settings.backup.restore');
    Route::get('/settings/backup/{id}/download', [\App\Http\Controllers\SuperAdmin\SettingsPlatformController::class, 'downloadBackup'])->name('settings.backup.download');
    Route::post('/settings/security', [\App\Http\Controllers\SuperAdmin\SettingsPlatformController::class, 'updateSecurity'])->name('settings.security.update');

    // 12. Profil Super Admin (Pusat Pengelolaan Akun, Keamanan & Sesi HQ)
    Route::get('/profile', [\App\Http\Controllers\SuperAdmin\SuperAdminProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/personal', [\App\Http\Controllers\SuperAdmin\SuperAdminProfileController::class, 'updatePersonal'])->name('profile.update_personal');
    Route::post('/profile/account', [\App\Http\Controllers\SuperAdmin\SuperAdminProfileController::class, 'updateAccount'])->name('profile.update_account');
    Route::post('/profile/password', [\App\Http\Controllers\SuperAdmin\SuperAdminProfileController::class, 'updatePassword'])->name('profile.update_password');
    Route::post('/profile/session/{id}/revoke', [\App\Http\Controllers\SuperAdmin\SuperAdminProfileController::class, 'revokeSession'])->name('profile.session.revoke');
    Route::post('/profile/session/revoke-others', [\App\Http\Controllers\SuperAdmin\SuperAdminProfileController::class, 'revokeOtherSessions'])->name('profile.session.revoke_others');
    Route::post('/profile/notifications', [\App\Http\Controllers\SuperAdmin\SuperAdminProfileController::class, 'updateNotifications'])->name('profile.notifications.update');
    Route::post('/profile/preferences', [\App\Http\Controllers\SuperAdmin\SuperAdminProfileController::class, 'updatePreferences'])->name('profile.preferences.update');
});
