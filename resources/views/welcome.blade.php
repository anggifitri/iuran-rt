@extends('layouts.app')

@section('title', 'NexaNest - Smart Living Starts Here')

@section('content')
<style>
    .hero-section {
        padding: 6rem 0 4rem;
        position: relative;
        overflow: hidden;
    }
    .hero-title {
        font-family: 'Montserrat', sans-serif;
        font-size: 3.5rem;
        font-weight: 800;
        line-height: 1.2;
        background: linear-gradient(135deg, #ffffff 0%, #cbd5e1 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 1.5rem;
    }
    html[data-theme="light"] .hero-title {
        background: linear-gradient(135deg, #0f172a 0%, #334155 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .hero-subtitle {
        font-size: 1.15rem;
        color: var(--text-muted);
        margin-bottom: 2.5rem;
        font-weight: 400;
        line-height: 1.7;
    }
    .feature-card {
        background: rgba(15, 23, 42, 0.4);
        border: 1px solid rgba(148, 163, 184, 0.1);
        border-radius: 1.5rem;
        padding: 2rem;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        backdrop-filter: blur(12px);
        text-decoration: none;
        display: flex;
        flex-direction: column;
        color: var(--text-main);
    }
    html[data-theme="light"] .feature-card {
        background: rgba(255, 255, 255, 0.7);
        border: 1px solid rgba(99, 102, 241, 0.15);
    }
    .feature-card:hover {
        transform: translateY(-8px);
        background: rgba(30, 41, 59, 0.8);
        border-color: rgba(56, 189, 248, 0.4);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
    }
    html[data-theme="light"] .feature-card:hover {
        background: rgba(255, 255, 255, 0.95);
        border-color: rgba(99, 102, 241, 0.4);
        box-shadow: 0 20px 40px rgba(99, 102, 241, 0.15);
    }
    .feature-icon-wrapper {
        width: 60px;
        height: 60px;
        border-radius: 1.25rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        margin-bottom: 1.5rem;
        background: rgba(56, 189, 248, 0.1);
        color: var(--primary);
        transition: all 0.3s;
    }
    .feature-card:hover .feature-icon-wrapper {
        transform: scale(1.1) rotate(5deg);
    }
    .feature-title {
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        font-size: 1.25rem;
        margin-bottom: 0.75rem;
        color: var(--text-main);
    }
    .feature-desc {
        color: var(--text-muted);
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 1.5rem;
        flex-grow: 1;
    }
    .feature-link-text {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--primary);
        display: flex;
        align-items: center;
        opacity: 0.7;
        transition: opacity 0.3s;
    }
    .feature-card:hover .feature-link-text {
        opacity: 1;
    }
    
    /* Background Decoration */
    .blob {
        position: absolute;
        filter: blur(80px);
        z-index: -1;
        opacity: 0.4;
    }
    .blob-1 {
        top: 0;
        left: -10%;
        width: 400px;
        height: 400px;
        background: rgba(56, 189, 248, 0.2);
        border-radius: 50%;
    }
    .blob-2 {
        bottom: 20%;
        right: -10%;
        width: 500px;
        height: 500px;
        background: rgba(244, 114, 182, 0.15);
        border-radius: 50%;
    }
    html[data-theme="light"] .blob-1 { background: rgba(99, 102, 241, 0.15); filter: blur(60px); }
    html[data-theme="light"] .blob-2 { background: rgba(236, 72, 153, 0.15); filter: blur(60px); }
</style>

<div class="blob blob-1"></div>
<div class="blob blob-2"></div>

<div class="container hero-section text-center">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-inline-block mb-4">
                <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary px-3 py-2 fw-medium border border-primary-subtle" style="font-size: 0.85rem; letter-spacing: 0.5px;">
                    <i class="fas fa-star me-1 text-warning"></i> Solusi Pengelolaan Lingkungan Terpadu
                </span>
            </div>
            <h1 class="hero-title">Smart Living Starts Here</h1>
            <p class="hero-subtitle px-md-4">
                Tinggalkan cara lama. NexaNest hadir sebagai platform digital terintegrasi untuk mendigitalisasi pengelolaan kas, penerbitan surat dengan Tanda Tangan Elektronik (TTE), jadwal Posyandu, hingga memajukan UMKM di lingkungan RW Anda. Semua dalam satu sistem cerdas.
            </p>
            <div class="d-flex justify-content-center gap-3 mt-2">
                <a href="{{ route('register') }}" class="btn btn-primary px-5 py-3 rounded-pill fw-bold shadow-sm" style="transition: all 0.3s; font-size: 1.1rem;">
                    Daftar Sekarang <i class="fas fa-arrow-right ms-2"></i>
                </a>
                <a href="{{ route('login') }}" class="btn btn-outline-secondary px-5 py-3 rounded-pill fw-bold" style="transition: all 0.3s; font-size: 1.1rem; border: 1px solid rgba(148, 163, 184, 0.4); color: var(--text-main);">
                    Login
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container pb-5 mb-5 position-relative">
    <div class="text-center mb-5 pb-2">
        <h3 class="fw-bold mb-2" style="font-family: 'Montserrat', sans-serif; color: var(--text-main);">Jelajahi Fitur Unggulan</h3>
        <p style="color: var(--text-muted); font-size: 1.05rem;">Solusi digital lengkap untuk mempermudah urusan warga dan pengurus RT/RW</p>
    </div>
    
    <div class="row g-4">
        <!-- Feature 1 -->
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('pembayaran.index') }}" class="feature-card">
                <div class="feature-icon-wrapper">
                    <i class="fas fa-wallet"></i>
                </div>
                <h4 class="feature-title">Keuangan Transparan</h4>
                <p class="feature-desc">Kelola iuran dan kas warga dengan sistem pencatatan cerdas. Pantau riwayat pemasukan dan pengeluaran secara real-time dan terorganisir.</p>
                <div class="feature-link-text">Lihat Fitur <i class="fas fa-chevron-right ms-2" style="font-size: 0.8rem;"></i></div>
            </a>
        </div>
        
        <!-- Feature 2 -->
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('surat.index') }}" class="feature-card">
                <div class="feature-icon-wrapper" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                    <i class="fas fa-file-signature"></i>
                </div>
                <h4 class="feature-title">Surat & TTE Otomatis</h4>
                <p class="feature-desc">Cetak surat pengantar dan keterangan dalam hitungan detik. Dilengkapi verifikasi Tanda Tangan Elektronik (TTE) resmi dari pengurus RT/RW.</p>
                <div class="feature-link-text" style="color: #10b981;">Lihat Fitur <i class="fas fa-chevron-right ms-2" style="font-size: 0.8rem;"></i></div>
            </a>
        </div>

        <!-- Feature 3 -->
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('posyandu.index') }}" class="feature-card">
                <div class="feature-icon-wrapper" style="background: rgba(244, 63, 94, 0.1); color: #f43f5e;">
                    <i class="fas fa-baby"></i>
                </div>
                <h4 class="feature-title">Sistem Posyandu</h4>
                <p class="feature-desc">Pemantauan gizi, berat badan, dan tumbuh kembang balita serta kesehatan ibu hamil yang tercatat rapi, historikal, dan dapat dipantau orang tua.</p>
                <div class="feature-link-text" style="color: #f43f5e;">Lihat Fitur <i class="fas fa-chevron-right ms-2" style="font-size: 0.8rem;"></i></div>
            </a>
        </div>

        <!-- Feature 4 -->
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('umkm.index') }}" class="feature-card">
                <div class="feature-icon-wrapper" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                    <i class="fas fa-store"></i>
                </div>
                <h4 class="feature-title">Pusat UMKM Warga</h4>
                <p class="feature-desc">Dukung ekonomi lingkungan dengan direktori bisnis warga. Etalase produk dan jasa lokal yang mudah diakses seluruh warga di RW Anda.</p>
                <div class="feature-link-text" style="color: #8b5cf6;">Lihat Fitur <i class="fas fa-chevron-right ms-2" style="font-size: 0.8rem;"></i></div>
            </a>
        </div>

        <!-- Feature 5 -->
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('pengaduan.index') }}" class="feature-card">
                <div class="feature-icon-wrapper" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <h4 class="feature-title">Pengaduan Interaktif</h4>
                <p class="feature-desc">Sampaikan keluhan, aspirasi, atau laporan kejadian langsung ke pengurus secara cepat, transparan, dan pantau langsung progres penyelesaiannya.</p>
                <div class="feature-link-text" style="color: #f59e0b;">Lihat Fitur <i class="fas fa-chevron-right ms-2" style="font-size: 0.8rem;"></i></div>
            </a>
        </div>

        <!-- Feature 6 -->
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('warga.index') }}" class="feature-card">
                <div class="feature-icon-wrapper" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                    <i class="fas fa-users"></i>
                </div>
                <h4 class="feature-title">Data Kependudukan</h4>
                <p class="feature-desc">Manajemen arsip data keluarga yang rapi dan terpusat. Memudahkan pengurus mendata dan memetakan demografi warga di lingkungan RW.</p>
                <div class="feature-link-text" style="color: #3b82f6;">Lihat Fitur <i class="fas fa-chevron-right ms-2" style="font-size: 0.8rem;"></i></div>
            </a>
        </div>
    </div>
</div>
@endsection
