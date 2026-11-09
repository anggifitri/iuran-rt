@extends('layouts.app')

@section('title', 'NexaNest — Smart & Green Community')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    :root {
        --nx-forest: #285943;
        --nx-forest-hover: #1e4633;
        --nx-sage: #A8C9B5;
        --nx-pastel-blue: #A9C9E8;
        --nx-sky-blue: #709BC4;
        --nx-mint: #DCEBDD;
        --nx-mint-soft: #EAF4EB;
        --nx-powder: #DCEAF5;
        --nx-powder-soft: #EBF4FB;
        --nx-natural-white: #F7F9F7;
        --nx-white: #FFFFFF;
        --nx-dark-text: #263449;
        --nx-secondary-text: #718096;
        --nx-soft-red: #FDE8E8;
        --nx-red-accent: #E26D6D;
        --nx-border: #E5EFE7;
    }

    body {
        background: var(--nx-natural-white) !important;
        color: var(--nx-dark-text) !important;
        font-family: 'Plus Jakarta Sans', 'Inter', sans-serif !important;
        overflow-x: hidden;
    }

    .theme-toggle-btn {
        display: none !important;
    }

    .nexa-dashboard-root {
        padding: 0 !important;
        margin: 0 !important;
        max-width: 100%;
    }

    /* ============ LANDING WRAPPER & BACKGROUND ============ */
    .nx-landing {
        min-height: 100vh;
        position: relative;
        overflow: hidden;
        background: linear-gradient(180deg, #E8F2FA 0%, #F1F7F3 46%, #F7F9F7 100%);
    }

    /* ============ 3. STICKY HORIZONTAL NAVBAR ============ */
    .nx-navbar-wrap {
        position: sticky;
        top: 0;
        z-index: 1030;
        padding: 18px 0 12px;
        background: rgba(247, 249, 247, 0.72);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-bottom: 1px solid rgba(168, 201, 181, 0.25);
        transition: all 0.25s ease;
    }

    .nx-navbar-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    /* Left: Brand */
    .nx-brand {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
    }

    .nx-brand-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        background: linear-gradient(135deg, #DCEBDD 0%, #B8D8C2 100%);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 6px 16px rgba(40, 89, 67, 0.12);
    }

    .nx-brand-name {
        font-size: 1.32rem;
        font-weight: 800;
        color: var(--nx-forest);
        letter-spacing: -0.02em;
        line-height: 1.1;
        margin: 0;
    }

    .nx-brand-tagline {
        font-size: 0.74rem;
        font-weight: 600;
        color: #547263;
        margin: 0;
    }

    /* Center: Pill Navigation */
    .nx-nav-pill-bar {
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid rgba(168, 201, 181, 0.45);
        border-radius: 999px;
        padding: 5px 8px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        box-shadow: 0 8px 24px rgba(38, 52, 73, 0.05);
    }

    .nx-nav-item-link {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 8px 18px;
        border-radius: 999px;
        font-size: 0.88rem;
        font-weight: 600;
        color: var(--nx-dark-text);
        text-decoration: none;
        transition: all 0.2s ease;
        border: none;
        background: transparent;
    }

    .nx-nav-item-link:hover {
        background: var(--nx-mint-soft);
        color: var(--nx-forest);
    }

    .nx-nav-item-link.active {
        background: var(--nx-mint);
        color: var(--nx-forest);
        font-weight: 700;
    }

    /* Right: Search + Login Button */
    .nx-nav-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .nx-search-box {
        position: relative;
        width: 225px;
    }

    .nx-search-box input {
        width: 100%;
        background: rgba(255, 255, 255, 0.94);
        border: 1px solid rgba(168, 201, 181, 0.48);
        border-radius: 999px;
        padding: 9px 16px 9px 38px;
        font-size: 0.85rem;
        color: var(--nx-dark-text);
        box-shadow: 0 6px 18px rgba(38, 52, 73, 0.04);
        transition: all 0.2s ease;
    }

    .nx-search-box input:focus {
        outline: none;
        border-color: var(--nx-forest);
        box-shadow: 0 0 0 3px rgba(40, 89, 67, 0.12);
    }

    .nx-search-box i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--nx-secondary-text);
        font-size: 0.85rem;
    }

    .nx-btn-login {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--nx-forest);
        color: var(--nx-white) !important;
        font-size: 0.88rem;
        font-weight: 700;
        padding: 10px 24px;
        border-radius: 999px;
        text-decoration: none;
        box-shadow: 0 8px 20px rgba(40, 89, 67, 0.22);
        transition: all 0.22s ease;
    }

    .nx-btn-login:hover {
        background: var(--nx-forest-hover);
        transform: translateY(-1px);
        box-shadow: 0 10px 24px rgba(40, 89, 67, 0.28);
    }

    /* ============ 4. HERO SECTION ============ */
    .nx-hero-section {
        position: relative;
        padding: 34px 0 68px;
        overflow: hidden;
    }

    .nx-welcome-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--nx-mint);
        color: var(--nx-forest);
        font-size: 0.84rem;
        font-weight: 700;
        padding: 8px 18px;
        border-radius: 999px;
        margin-bottom: 18px;
        border: 1px solid rgba(40, 89, 67, 0.12);
    }

    .nx-hero-heading {
        font-size: clamp(2.5rem, 4.6vw, 3.85rem);
        font-weight: 800;
        line-height: 1.08;
        letter-spacing: -0.03em;
        margin-bottom: 18px;
    }

    .nx-hero-heading .line-green {
        color: var(--nx-forest);
        display: block;
    }

    .nx-hero-heading .line-blue {
        color: var(--nx-sky-blue);
        display: inline-block;
        position: relative;
    }

    .nx-hero-desc {
        font-size: 1.02rem;
        line-height: 1.68;
        color: #46586B;
        max-width: 500px;
        margin-bottom: 28px;
        font-weight: 500;
    }

    .nx-hero-cta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 14px;
    }

    .nx-btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: var(--nx-forest);
        color: var(--nx-white) !important;
        padding: 13px 28px;
        border-radius: 999px;
        font-size: 0.94rem;
        font-weight: 700;
        text-decoration: none;
        box-shadow: 0 10px 24px rgba(40, 89, 67, 0.24);
        transition: all 0.24s ease;
    }

    .nx-btn-primary:hover {
        background: var(--nx-forest-hover);
        transform: translateY(-2px);
    }

    .nx-btn-outline {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: rgba(255, 255, 255, 0.85);
        color: var(--nx-forest) !important;
        border: 1.5px solid var(--nx-forest);
        padding: 12px 26px;
        border-radius: 999px;
        font-size: 0.94rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.24s ease;
    }

    .nx-btn-outline:hover {
        background: var(--nx-mint);
        transform: translateY(-2px);
    }

    /* Right Hero Landscape Illustration */
    .nx-hero-illustration-wrap {
        position: relative;
        min-height: 360px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .nx-motto-floating {
        position: absolute;
        top: 6px;
        right: 18px;
        z-index: 4;
        transform: rotate(-4deg);
        text-align: center;
    }

    .nx-motto-floating span {
        display: block;
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--nx-forest);
        line-height: 1.2;
        text-shadow: 0 2px 10px rgba(255, 255, 255, 0.9);
    }

    /* Gentle Wave Divider into Feature Section */
    .nx-wave-divider {
        position: absolute;
        bottom: -1px;
        left: 0;
        width: 100%;
        line-height: 0;
        pointer-events: none;
    }

    /* ============ 5. FEATURE SECTION — JELAJAHI FITUR UNGGULAN ============ */
    .nx-features-section {
        background: var(--nx-natural-white);
        position: relative;
        padding: 20px 0 64px;
        z-index: 2;
    }

    .nx-section-header {
        text-align: center;
        margin-bottom: 38px;
    }

    .nx-section-title-wrap {
        display: inline-flex;
        align-items: center;
        gap: 18px;
        margin-bottom: 8px;
    }

    .nx-section-title-line {
        width: 70px;
        height: 1.5px;
        background: var(--nx-sage);
        display: inline-block;
    }

    .nx-section-title {
        font-size: 1.85rem;
        font-weight: 800;
        color: var(--nx-forest);
        margin: 0;
        letter-spacing: -0.02em;
    }

    .nx-section-subtitle {
        font-size: 0.96rem;
        color: var(--nx-secondary-text);
        font-weight: 500;
        margin: 0;
    }

    /* Six Feature Cards (Horizontal Layout Matching Reference) */
    .nx-feature-card {
        background: var(--nx-white);
        border: 1px solid var(--nx-border);
        border-radius: 22px;
        padding: 22px 22px;
        display: flex;
        align-items: center;
        gap: 18px;
        text-decoration: none;
        color: var(--nx-dark-text);
        box-shadow: 0 10px 28px rgba(40, 89, 67, 0.05);
        transition: all 0.26s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        position: relative;
    }

    .nx-feature-card:hover {
        transform: translateY(-5px);
        border-color: var(--nx-sage);
        box-shadow: 0 16px 34px rgba(40, 89, 67, 0.10);
        color: var(--nx-dark-text);
    }

    .nx-feature-icon {
        width: 68px;
        height: 68px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.65rem;
        flex-shrink: 0;
        transition: transform 0.25s ease;
    }

    .nx-feature-card:hover .nx-feature-icon {
        transform: scale(1.06);
    }

    .nx-accent-green .nx-feature-icon {
        background: var(--nx-mint);
        color: var(--nx-forest);
    }

    .nx-accent-blue .nx-feature-icon {
        background: var(--nx-powder);
        color: #3E7CB8;
    }

    .nx-accent-red .nx-feature-icon {
        background: var(--nx-soft-red);
        color: var(--nx-red-accent);
    }

    .nx-feature-body {
        flex: 1;
        min-width: 0;
    }

    .nx-feature-title {
        font-size: 1.08rem;
        font-weight: 800;
        color: var(--nx-dark-text);
        margin-bottom: 5px;
    }

    .nx-feature-desc {
        font-size: 0.83rem;
        line-height: 1.5;
        color: var(--nx-secondary-text);
        margin: 0;
        font-weight: 500;
    }

    .nx-feature-arrow {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.82rem;
        flex-shrink: 0;
        transition: transform 0.22s ease;
    }

    .nx-accent-green .nx-feature-arrow {
        background: var(--nx-mint);
        color: var(--nx-forest);
    }

    .nx-accent-blue .nx-feature-arrow {
        background: var(--nx-powder);
        color: #2B5F8E;
    }

    .nx-accent-red .nx-feature-arrow {
        background: #EEF2F6;
        color: var(--nx-dark-text);
    }

    .nx-feature-card:hover .nx-feature-arrow {
        transform: translateX(3px);
    }

    /* ============ FOOTER PILL BANNER ============ */
    .nx-footer-pill-wrap {
        text-align: center;
        margin-top: 44px;
    }

    .nx-footer-pill {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: var(--nx-mint);
        border: 1px solid var(--nx-sage);
        color: var(--nx-forest);
        padding: 11px 28px;
        border-radius: 999px;
        font-size: 0.86rem;
        font-weight: 600;
        font-style: italic;
        box-shadow: 0 6px 20px rgba(40, 89, 67, 0.06);
    }

    /* Corner Leaf Ornaments */
    .nx-corner-leaf-left {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 120px;
        pointer-events: none;
        z-index: 1;
        opacity: 0.85;
    }

    .nx-corner-leaf-right {
        position: fixed;
        bottom: 0;
        right: 0;
        width: 120px;
        pointer-events: none;
        z-index: 1;
        opacity: 0.85;
    }

    @media (max-width: 991.98px) {
        .nx-nav-pill-bar {
            display: none;
        }
        .nx-search-box {
            width: 165px;
        }
        .nx-hero-section {
            padding: 24px 0 48px;
            text-align: center;
        }
        .nx-hero-desc {
            margin-left: auto;
            margin-right: auto;
        }
        .nx-hero-cta {
            justify-content: center;
        }
    }
</style>

<div class="nx-landing">

    <!-- ==================== 3. STICKY HORIZONTAL NAVBAR ==================== -->
    <header class="nx-navbar-wrap">
        <div class="container">
            <div class="nx-navbar-inner">
                <!-- Left: Logo + Brand NexaNest + Tagline -->
                <a href="{{ url('/') }}" class="nx-brand">
                    <span class="nx-brand-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                            <path d="M3 10.5L12 3L21 10.5V20C21 20.5523 20.5523 21 20 21H4C3.44772 21 3 20.5523 3 20V10.5Z" stroke="#285943" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M9 21V14C9 12.8954 9.89543 12 11 12H13C14.1046 12 15 12.8954 15 14V21" stroke="#285943" stroke-width="2"/>
                            <path d="M13.5 8.5C15.5 7 18 8 18 10.5C16 11.5 13.5 10.5 13.5 8.5Z" fill="#285943"/>
                        </svg>
                    </span>
                    <div>
                        <h1 class="nx-brand-name">NexaNest</h1>
                        <p class="nx-brand-tagline">Smart &amp; Green Community</p>
                    </div>
                </a>

                <!-- Center: Pill Navigation (Beranda, Fitur Dropdown, Tentang Kami, Bantuan Dropdown) -->
                <nav class="nx-nav-pill-bar" aria-label="Navigasi Utama">
                    <a href="{{ url('/') }}" class="nx-nav-item-link active">
                        <i class="fas fa-home"></i> Beranda
                    </a>

                    <div class="dropdown">
                        <button class="nx-nav-item-link dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-border-all text-success"></i> Fitur
                        </button>
                        <ul class="dropdown-menu border-0 shadow-lg p-2" style="border-radius: 16px; min-width: 220px;">
                            <li><a class="dropdown-item rounded-3 py-2 small fw-semibold" href="#fiturUnggulan"><i class="fas fa-wallet text-success me-2"></i>Keuangan Transparan</a></li>
                            <li><a class="dropdown-item rounded-3 py-2 small fw-semibold" href="{{ route('surat.index') }}"><i class="fas fa-file-signature text-success me-2"></i>Penerbitan Surat &amp; TTE</a></li>
                            <li><a class="dropdown-item rounded-3 py-2 small fw-semibold" href="{{ route('posyandu.index') }}"><i class="fas fa-heart-pulse text-danger me-2"></i>Posyandu Keluarga</a></li>
                            <li><a class="dropdown-item rounded-3 py-2 small fw-semibold" href="{{ route('umkm.index') }}"><i class="fas fa-store text-primary me-2"></i>Direktori UMKM Warga</a></li>
                            <li><a class="dropdown-item rounded-3 py-2 small fw-semibold" href="{{ route('pengaduan.index') }}"><i class="fas fa-qrcode text-success me-2"></i>Pengaduan &amp; QR Lingkungan</a></li>
                        </ul>
                    </div>

                    <a href="#tentangNexaNest" data-bs-toggle="modal" class="nx-nav-item-link">
                        Tentang Kami
                    </a>

                    <div class="dropdown">
                        <button class="nx-nav-item-link dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="far fa-life-ring text-success"></i> Bantuan
                        </button>
                        <ul class="dropdown-menu border-0 shadow-lg p-2" style="border-radius: 16px; min-width: 220px;">
                            <li><a class="dropdown-item rounded-3 py-2 small fw-semibold" href="#modalBantuan" data-bs-toggle="modal"><i class="fas fa-circle-question text-success me-2"></i>Cara Pendaftaran Warga</a></li>
                            <li><a class="dropdown-item rounded-3 py-2 small fw-semibold" href="#modalDaruratLanding" data-bs-toggle="modal"><i class="fas fa-bell text-danger me-2"></i>Kontak Darurat Lingkungan</a></li>
                            <li><a class="dropdown-item rounded-3 py-2 small fw-semibold" href="{{ route('password.request') }}"><i class="fas fa-key text-primary me-2"></i>Lupa Password Akun</a></li>
                        </ul>
                    </div>
                </nav>

                <!-- Right: Search Bar + Login / Dashboard Button -->
                <div class="nx-nav-actions">
                    <div class="nx-search-box d-none d-sm-block">
                        <i class="fas fa-search"></i>
                        <input type="text" id="landingSearchInput" placeholder="Cari fitur..." aria-label="Cari fitur">
                    </div>

                    @auth
                        <a href="{{ route('dashboard') }}" class="nx-btn-login">
                            <i class="fas fa-gauge-high"></i> Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="nx-btn-login">
                            <i class="fas fa-user"></i> Login
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- ==================== 4. HERO SECTION ==================== -->
    <section class="nx-hero-section">
        <div class="container">
            <div class="row align-items-center g-4">
                <!-- Left Column: Copywriting & CTA -->
                <div class="col-lg-6">
                    <div class="nx-welcome-badge">
                        <i class="fas fa-leaf"></i> Selamat Datang di NexaNest
                    </div>

                    <h2 class="nx-hero-heading">
                        <span class="line-green">Smart Living</span>
                        <span class="line-blue">
                            Starts Here
                            <svg width="34" height="30" viewBox="0 0 34 30" fill="none" style="margin-left: 6px; vertical-align: middle;">
                                <path d="M4 22C10 8 24 4 30 6C28 16 18 24 4 22Z" fill="#8BB89A"/>
                                <path d="M14 27C20 20 27 18 32 20C29 26 21 29 14 27Z" fill="#A8C9B5"/>
                            </svg>
                        </span>
                    </h2>

                    <p class="nx-hero-desc">
                        Wujudkan lingkungan yang lebih baik bersama warga melalui layanan digital yang mudah, transparan, dan terpercaya.
                    </p>

                    <div class="nx-hero-cta">
                        @auth
                            <a href="{{ route('dashboard') }}" class="nx-btn-primary">
                                Buka Dashboard <i class="fas fa-arrow-right"></i>
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="nx-btn-primary">
                                Daftar Sekarang <i class="fas fa-arrow-right"></i>
                            </a>
                        @endauth

                        <a href="#fiturUnggulan" class="nx-btn-outline">
                            <i class="fas fa-play-circle"></i> Pelajari Lebih Lanjut
                        </a>
                    </div>
                </div>

                <!-- Right Column: Sustainable Neighborhood Landscape Illustration -->
                <div class="col-lg-6">
                    <div class="nx-hero-illustration-wrap">
                        <div class="nx-motto-floating">
                            <span>Bersama Jaga</span>
                            <span>Lingkungan Kita</span>
                            <svg width="140" height="12" viewBox="0 0 140 12" fill="none">
                                <path d="M4 9C45 2 95 2 136 8" stroke="#285943" stroke-width="2.5" stroke-linecap="round"/>
                            </svg>
                        </div>

                        <!-- Integrated Vector Landscape SVG (Houses, Solar/Eco Roofs, Trees, Winding Road, Sun, Clouds) -->
                        <svg viewBox="0 0 680 400" width="100%" height="100%" aria-label="Ilustrasi Lingkungan Asri NexaNest">
                            <defs>
                                <linearGradient id="skyGlow" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#DCEAF5" stop-opacity="0.7"/>
                                    <stop offset="100%" stop-color="#F7F9F7" stop-opacity="0"/>
                                </linearGradient>
                                <linearGradient id="hillGrad1" x1="0" y1="0" x2="1" y2="1">
                                    <stop offset="0%" stop-color="#B9D8C2"/>
                                    <stop offset="100%" stop-color="#8AB599"/>
                                </linearGradient>
                                <linearGradient id="hillGrad2" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#9AC2A7"/>
                                    <stop offset="100%" stop-color="#6A9B7B"/>
                                </linearGradient>
                            </defs>

                            <!-- Soft Sun & Clouds -->
                            <circle cx="215" cy="74" r="26" fill="#FCE592" opacity="0.85"/>
                            <g fill="#FFFFFF" opacity="0.85">
                                <ellipse cx="130" cy="78" rx="45" ry="15"/>
                                <circle cx="112" cy="70" r="18"/>
                                <circle cx="144" cy="68" r="22"/>
                                <ellipse cx="430" cy="58" rx="38" ry="12"/>
                                <circle cx="415" cy="52" r="15"/>
                                <circle cx="442" cy="50" r="17"/>
                            </g>

                            <!-- Distant Eco City Skyline Silhouette -->
                            <g fill="#CFE2EE" opacity="0.65">
                                <rect x="140" y="150" width="28" height="90" rx="3"/>
                                <rect x="174" y="132" width="34" height="108" rx="3"/>
                                <rect x="214" y="162" width="24" height="78" rx="3"/>
                                <rect x="465" y="140" width="32" height="100" rx="3"/>
                                <rect x="504" y="156" width="26" height="84" rx="3"/>
                            </g>

                            <!-- Rolling Green Hills -->
                            <path d="M10 340 Q180 220 390 255 T670 235 L670 375 L10 375 Z" fill="url(#hillGrad1)"/>
                            <path d="M60 365 Q280 245 485 275 T675 265 L675 385 L60 385 Z" fill="url(#hillGrad2)"/>

                            <!-- Left Neighborhood House -->
                            <g transform="translate(230, 178)">
                                <rect x="10" y="38" width="76" height="58" rx="4" fill="#F4F1EA"/>
                                <polygon points="2,40 48,6 94,40" fill="#7D98A6"/>
                                <rect x="38" y="60" width="18" height="36" rx="2" fill="#6B8290"/>
                                <rect x="18" y="50" width="14" height="16" rx="2" fill="#A9C9E8"/>
                                <rect x="62" y="50" width="14" height="16" rx="2" fill="#A9C9E8"/>
                            </g>

                            <!-- Center Main Modern Sustainable Home -->
                            <g transform="translate(318, 146)">
                                <rect x="12" y="48" width="118" height="82" rx="5" fill="#FFFFFF"/>
                                <rect x="12" y="48" width="46" height="82" rx="4" fill="#EBF3F9"/>
                                <polygon points="4,52 71,4 138,52" fill="#5D7B93"/>
                                <!-- Solar Panel Accent on Roof -->
                                <polygon points="75,18 118,46 92,46 56,18" fill="#82A8C9" opacity="0.75"/>
                                <rect x="26" y="84" width="22" height="46" rx="3" fill="#527261"/>
                                <rect x="68" y="68" width="22" height="24" rx="3" fill="#A9C9E8"/>
                                <rect x="98" y="68" width="22" height="24" rx="3" fill="#A9C9E8"/>
                            </g>

                            <!-- Right Cozy Home -->
                            <g transform="translate(465, 174)">
                                <rect x="8" y="36" width="74" height="56" rx="4" fill="#F9F6F0"/>
                                <polygon points="0,38 45,6 90,38" fill="#8C6D58"/>
                                <rect x="36" y="58" width="16" height="34" rx="2" fill="#6E5240"/>
                                <rect x="16" y="48" width="14" height="14" rx="2" fill="#A9C9E8"/>
                                <rect x="58" y="48" width="14" height="14" rx="2" fill="#A9C9E8"/>
                            </g>

                            <!-- Lush Green Trees -->
                            <g transform="translate(155, 142)">
                                <rect x="34" y="72" width="10" height="48" rx="3" fill="#7C5A43"/>
                                <circle cx="39" cy="48" r="34" fill="#77AA85"/>
                                <circle cx="19" cy="62" r="24" fill="#8FBE9C"/>
                                <circle cx="57" cy="60" r="25" fill="#619470"/>
                            </g>
                            <g transform="translate(418, 124)">
                                <rect x="32" y="78" width="10" height="52" rx="3" fill="#70513C"/>
                                <circle cx="37" cy="50" r="36" fill="#6B9E7B"/>
                                <circle cx="18" cy="64" r="25" fill="#86B694"/>
                                <circle cx="56" cy="62" r="26" fill="#568967"/>
                            </g>

                            <!-- Winding Neighborhood Road -->
                            <path d="M355 276 Q395 315 340 385 L465 385 Q475 315 395 276 Z" fill="#E3E9EE"/>
                            <path d="M374 282 Q405 320 395 380" stroke="#FFFFFF" stroke-width="3" stroke-dasharray="8 8" fill="none"/>

                            <!-- Foreground Bushes & Flowers -->
                            <circle cx="255" cy="285" r="18" fill="#5E916D"/>
                            <circle cx="278" cy="292" r="14" fill="#79AC88"/>
                            <circle cx="515" cy="285" r="22" fill="#5E916D"/>
                            <circle cx="542" cy="292" r="16" fill="#79AC88"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Soft Wave Merging into Feature Section -->
        <div class="nx-wave-divider">
            <svg viewBox="0 0 1440 70" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" style="width: 100%; height: 54px;">
                <path d="M0 45C320 0 680 75 1080 35C1260 17 1370 25 1440 35V70H0V45Z" fill="#F7F9F7"/>
            </svg>
        </div>
    </section>

    <!-- ==================== 5. FEATURE SECTION — JELAJAHI FITUR UNGGULAN ==================== -->
    <section class="nx-features-section" id="fiturUnggulan">
        <div class="container">
            <div class="nx-section-header">
                <div class="mb-1">
                    <i class="fas fa-seedling" style="color: var(--nx-forest); font-size: 1.25rem;"></i>
                </div>
                <div class="nx-section-title-wrap">
                    <span class="nx-section-title-line d-none d-sm-inline-block"></span>
                    <h3 class="nx-section-title">Jelajahi Fitur Unggulan</h3>
                    <span class="nx-section-title-line d-none d-sm-inline-block"></span>
                </div>
                <p class="nx-section-subtitle">
                    Solusi digital lengkap untuk mempermudah urusan warga dan pengurus RT/RW.
                </p>
            </div>

            <!-- 6 Feature Cards Grid (3 Columns Desktop) -->
            <div class="row g-4" id="featureCardsGrid">
                <!-- CARD 1 — KEUANGAN TRANSPARAN (Pastel Green) -->
                <div class="col-md-6 col-lg-4 feature-card-col" data-title="keuangan transparan iuran kas warga laporan">
                    <a href="{{ auth()->check() ? route('laporan.iuran') : route('login') }}" class="nx-feature-card nx-accent-green">
                        <div class="nx-feature-icon">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <div class="nx-feature-body">
                            <h4 class="nx-feature-title">Keuangan Transparan</h4>
                            <p class="nx-feature-desc">
                                Kelola iuran dan kas warga dengan sistem pencatatan cerdas dan transparan.
                            </p>
                        </div>
                        <span class="nx-feature-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </span>
                    </a>
                </div>

                <!-- CARD 2 — BANK SAMPAH (Pastel Blue) -->
                <div class="col-md-6 col-lg-4 feature-card-col" data-title="bank sampah daur ulang lingkungan bersih">
                    <a href="#modalBankSampahLanding" data-bs-toggle="modal" class="nx-feature-card nx-accent-blue">
                        <div class="nx-feature-icon">
                            <i class="fas fa-recycle"></i>
                        </div>
                        <div class="nx-feature-body">
                            <h4 class="nx-feature-title">Bank Sampah</h4>
                            <p class="nx-feature-desc">
                                Setor sampah, dapatkan manfaat untuk lingkungan, dan dukung pengelolaan sampah berkelanjutan.
                            </p>
                        </div>
                        <span class="nx-feature-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </span>
                    </a>
                </div>

                <!-- CARD 3 — POSYANDU (Pastel Green) -->
                <div class="col-md-6 col-lg-4 feature-card-col" data-title="posyandu kesehatan balita ibu hamil keluarga">
                    <a href="{{ auth()->check() ? route('posyandu.index') : route('login') }}" class="nx-feature-card nx-accent-green">
                        <div class="nx-feature-icon">
                            <i class="fas fa-heart-pulse"></i>
                        </div>
                        <div class="nx-feature-body">
                            <h4 class="nx-feature-title">Posyandu</h4>
                            <p class="nx-feature-desc">
                                Pantau informasi dan kegiatan Posyandu untuk mendukung kesehatan keluarga.
                            </p>
                        </div>
                        <span class="nx-feature-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </span>
                    </a>
                </div>

                <!-- CARD 4 — DIREKTORI UMKM (Pastel Blue) -->
                <div class="col-md-6 col-lg-4 feature-card-col" data-title="direktori umkm toko usaha warga kuliner lokal">
                    <a href="{{ auth()->check() ? route('umkm.index') : route('login') }}" class="nx-feature-card nx-accent-blue">
                        <div class="nx-feature-icon">
                            <i class="fas fa-store"></i>
                        </div>
                        <div class="nx-feature-body">
                            <h4 class="nx-feature-title">Direktori UMKM</h4>
                            <p class="nx-feature-desc">
                                Dukung produk lokal dan temukan berbagai usaha milik warga di lingkungan sekitar.
                            </p>
                        </div>
                        <span class="nx-feature-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </span>
                    </a>
                </div>

                <!-- CARD 5 — KALENDER KEGIATAN (Pastel Green) -->
                <div class="col-md-6 col-lg-4 feature-card-col" data-title="kalender kegiatan jadwal kerja bakti rapat warga surat">
                    <a href="{{ auth()->check() ? route('posyandu.index') : route('login') }}" class="nx-feature-card nx-accent-green">
                        <div class="nx-feature-icon">
                            <i class="fas fa-calendar-days"></i>
                        </div>
                        <div class="nx-feature-body">
                            <h4 class="nx-feature-title">Kalender Kegiatan</h4>
                            <p class="nx-feature-desc">
                                Ikuti jadwal kegiatan warga, kerja bakti, rapat, dan berbagai kegiatan lingkungan.
                            </p>
                        </div>
                        <span class="nx-feature-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </span>
                    </a>
                </div>

                <!-- CARD 6 — TOMBOL DARURAT (Soft Red) -->
                <div class="col-md-6 col-lg-4 feature-card-col" data-title="tombol darurat keamanan kebakaran ambulans pengaduan">
                    <a href="#modalDaruratLanding" data-bs-toggle="modal" class="nx-feature-card nx-accent-red">
                        <div class="nx-feature-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div class="nx-feature-body">
                            <h4 class="nx-feature-title">Tombol Darurat</h4>
                            <p class="nx-feature-desc">
                                Laporkan keadaan darurat dan hubungi pihak terkait dengan lebih cepat.
                            </p>
                        </div>
                        <span class="nx-feature-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </span>
                    </a>
                </div>
            </div>

            <!-- Bottom Motto Pill Banner -->
            <div class="nx-footer-pill-wrap">
                <div class="nx-footer-pill">
                    <i class="fas fa-leaf"></i>
                    <span>NexaNest — Membangun lingkungan yang cerdas, peduli, dan berkelanjutan.</span>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- MODAL TENTANG NEXANEST -->
<div class="modal fade" id="tentangNexaNest" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 22px; background: #fff; color: var(--nx-dark-text);">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" style="color: var(--nx-forest);">
                    <i class="fas fa-leaf me-2"></i>Tentang NexaNest
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-4">
                <p class="small text-muted mb-3">
                    <strong>NexaNest (Smart &amp; Green Community)</strong> adalah platform digital terpadu untuk pengelolaan administrasi RT/RW, transparansi kas &amp; iuran warga, penerbitan surat ber-TTE (SHA-256), pemantauan Posyandu, direktori UMKM, serta pelaporan fasilitas lingkungan berbasis QR Code.
                </p>
                <div class="row g-2 text-center">
                    <div class="col-4">
                        <div class="p-2 rounded-3" style="background: var(--nx-mint-soft);">
                            <div class="fw-bold" style="color: var(--nx-forest);">RT 006–010</div>
                            <small class="text-muted" style="font-size: 0.72rem;">Cakupan RW 018</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 rounded-3" style="background: var(--nx-powder-soft);">
                            <div class="fw-bold" style="color: #2B5F8E;">TTE SHA-256</div>
                            <small class="text-muted" style="font-size: 0.72rem;">Surat Digital</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 rounded-3" style="background: var(--nx-mint-soft);">
                            <div class="fw-bold" style="color: var(--nx-forest);">100% Transparan</div>
                            <small class="text-muted" style="font-size: 0.72rem;">Kas per RT &amp; RW</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL BANK SAMPAH & LINGKUNGAN -->
<div class="modal fade" id="modalBankSampahLanding" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 22px; background: #fff; color: var(--nx-dark-text);">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" style="color: var(--nx-forest);">
                    <i class="fas fa-recycle me-2"></i>Program Bank Sampah &amp; Lingkungan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-4">
                <p class="small text-muted mb-3">
                    Dukung gerakan <strong>Smart &amp; Sustainable Neighborhood</strong> dengan memilah sampah organik dan anorganik dari rumah. Warga juga dapat melaporkan masalah kebersihan bak sampah komunal melalui fitur QR Code Aset Lingkungan.
                </p>
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ auth()->check() ? route('pengaduan.create', ['scan' => 1]) : route('login') }}" class="btn btn-sm fw-bold px-3 py-2" style="background: var(--nx-forest); color: #fff; border-radius: 999px;">
                        Buka Layanan Lingkungan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL TOMBOL DARURAT -->
<div class="modal fade" id="modalDaruratLanding" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 22px; background: #fff; color: var(--nx-dark-text);">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-danger">
                    <i class="fas fa-bell me-2"></i>Layanan &amp; Kontak Darurat Lingkungan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex flex-column gap-2">
                    <div class="p-3 rounded-3 d-flex justify-content-between align-items-center" style="background: var(--nx-soft-red);">
                        <div>
                            <div class="fw-bold text-danger">Pos Keamanan / Siskamling RW 018</div>
                            <small class="text-muted">Lapor cepat insiden keamanan atau fasilitas</small>
                        </div>
                        <a href="{{ auth()->check() ? route('pengaduan.create', ['scan' => 1]) : route('login') }}" class="btn btn-sm btn-danger rounded-pill px-3">Lapor</a>
                    </div>
                    <div class="p-3 rounded-3 d-flex justify-content-between align-items-center" style="background: var(--nx-mint-soft);">
                        <div>
                            <div class="fw-bold" style="color: var(--nx-forest);">Ambulans / Medis Darurat</div>
                            <small class="text-muted">Call Center 119</small>
                        </div>
                        <a href="tel:119" class="btn btn-sm btn-success rounded-pill px-3">119</a>
                    </div>
                    <div class="p-3 rounded-3 d-flex justify-content-between align-items-center" style="background: var(--nx-powder-soft);">
                        <div>
                            <div class="fw-bold" style="color: #2B5F8E;">Pemadam Kebakaran</div>
                            <small class="text-muted">Call Center 113</small>
                        </div>
                        <a href="tel:113" class="btn btn-sm btn-primary rounded-pill px-3">113</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL BANTUAN PENDAFTARAN -->
<div class="modal fade" id="modalBantuan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 22px; background: #fff; color: var(--nx-dark-text);">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" style="color: var(--nx-forest);">
                    <i class="far fa-circle-question me-2"></i>Panduan Singkat Warga Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-4 small text-muted">
                <ol class="ps-3 mb-3">
                    <li class="mb-2">Klik tombol <strong>Daftar Sekarang</strong> pada halaman utama.</li>
                    <li class="mb-2">Isi identitas lengkap, nomor RT (006–010), blok rumah, dan pilih status Kepala Keluarga / Anggota Keluarga.</li>
                    <li>Setelah terdaftar, Anda otomatis masuk ke Dashboard Warga untuk melihat riwayat iuran, mengajukan surat, dan mengakses layanan lingkungan.</li>
                </ol>
                <div class="text-end">
                    <a href="{{ route('register') }}" class="btn btn-sm fw-bold px-3 py-2" style="background: var(--nx-forest); color: #fff; border-radius: 999px;">
                        Daftar Warga Sekarang
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('landingSearchInput');
    const cols = document.querySelectorAll('.feature-card-col');

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            cols.forEach(col => {
                const hay = (col.dataset.title || '') + ' ' + col.textContent.toLowerCase();
                col.style.display = hay.includes(q) ? '' : 'none';
            });
        });

        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                document.getElementById('fiturUnggulan')?.scrollIntoView({ behavior: 'smooth' });
            }
        });
    }
});
</script>
@endsection
