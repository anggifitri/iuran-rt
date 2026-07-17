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
});

// 2. Route Login & Register (Penting agar image_a8278c.png tidak error)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// 3. Route yang butuh Login
Route::middleware(['auth'])->group(function () {
    // Dashboard (image_a8a673.png)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Resource (Otomatis bikin warga.index, pembayaran.index dll - image_b29c3a.png)
    Route::resource('warga', WargaController::class);
    Route::resource('pembayaran', PembayaranController::class);

    // Cetak Laporan (image_b29c3a.png baris 23)
    Route::get('/laporan/cetak', [PembayaranController::class, 'cetakLaporan'])->name('laporan.cetak');

    //Route Laporan Bulanan
    Route::get('/laporan', [PembayaranController::class, 'indexLaporan'])->name('laporan.index');
    Route::get('/laporan/cetak-bulanan', [PembayaranController::class, 'cetakLaporanBulanan'])->name('laporan.cetak_bulanan');
    // Route Laporan Iuran
    Route::get('/laporan/iuran', [PembayaranController::class, 'laporanIuran'])->name('laporan.iuran');
    Route::get('/laporan/cetak-iuran', [PembayaranController::class, 'cetakLaporanIuran'])->name('laporan.cetak_iuran');

    // Profil Admin RT
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Layanan Mandiri: Penerbitan Surat, Pengaduan, Posyandu, UMKM
    Route::resource('surat', App\Http\Controllers\SuratPengajuanController::class);
    Route::post('/surat/{surat}/approve-rt', [App\Http\Controllers\SuratPengajuanController::class, 'approveRt'])->name('surat.approve_rt');
    Route::post('/surat/{surat}/approve-rw', [App\Http\Controllers\SuratPengajuanController::class, 'approveRw'])->name('surat.approve_rw');
    Route::get('/surat/{surat}/pdf', [App\Http\Controllers\SuratPengajuanController::class, 'downloadPdf'])->name('surat.pdf');
    Route::resource('pengaduan', App\Http\Controllers\PengaduanController::class)->only(['index','create','store']);
    Route::resource('posyandu', App\Http\Controllers\PosyanduController::class)->only(['index','create','store']);
    Route::resource('umkm', App\Http\Controllers\UmkmController::class)->only(['index','show','create','store']);
    Route::get('/umkm/categories', [App\Http\Controllers\UmkmController::class, 'categories'])->name('umkm.categories');
});
