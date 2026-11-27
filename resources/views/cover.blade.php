@extends('layouts.app')

@section('title', 'NexaNest — Smart & Green Community')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Kalam:wght@700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

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
        --nx-natural-white: #F8FAF9;
        --nx-white: #FFFFFF;
        --nx-dark-text: #1E293B;
        --nx-secondary-text: #64748B;
        --nx-border: #E2E8F0;
        --nx-navy-card: #0F172A;
    }

    body {
        background: var(--nx-natural-white) !important;
        color: var(--nx-dark-text) !important;
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        overflow-x: hidden !important;
        margin: 0;
        padding: 0;
    }

    .nexa-dashboard-root {
        padding: 0 !important;
        margin: 0 !important;
        max-width: 100%;
    }

    .nx-landing {
        min-height: 100vh;
        position: relative;
        overflow-x: hidden !important;
        background: linear-gradient(180deg, #EBF3F8 0%, #F4F8F5 35%, #F8FAF9 100%);
    }

    /* 1. NAVBAR */
    .nx-navbar-wrap {
        position: sticky;
        top: 0;
        z-index: 1030;
        padding: 14px 0;
        background: rgba(248, 250, 249, 0.88);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        border-bottom: 1px solid rgba(226, 232, 240, 0.8);
        transition: all 0.25s ease;
    }

    .nx-navbar-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .nx-brand {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
    }

    .nx-brand-icon {
        width: 44px;
        height: 44px;
        border-radius: 13px;
        background: linear-gradient(135deg, #DCEBDD 0%, #B8D8C2 100%);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 14px rgba(40, 89, 67, 0.12);
        flex-shrink: 0;
    }

    .nx-brand-name {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--nx-forest);
        letter-spacing: -0.02em;
        line-height: 1.1;
        margin: 0;
    }

    .nx-brand-tagline {
        font-size: 0.72rem;
        font-weight: 600;
        color: #547263;
        margin: 0;
    }

    .nx-nav-pill-bar {
        background: rgba(255, 255, 255, 0.94);
        border: 1px solid rgba(168, 201, 181, 0.45);
        border-radius: 999px;
        padding: 4px 6px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        box-shadow: 0 4px 18px rgba(38, 52, 73, 0.04);
    }

    .nx-nav-item-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 16px;
        border-radius: 999px;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--nx-dark-text);
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .nx-nav-item-link:hover, .nx-nav-item-link.active {
        background: var(--nx-mint-soft);
        color: var(--nx-forest);
    }

    .nx-nav-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .nx-btn-login {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 8px 18px;
        border-radius: 999px;
        background: var(--nx-forest);
        color: #FFFFFF !important;
        font-size: 0.85rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(40, 89, 67, 0.22);
    }

    .nx-btn-login:hover {
        background: var(--nx-forest-hover);
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(40, 89, 67, 0.3);
    }

    /* 2. HERO SECTION */
    .nx-hero-section {
        position: relative;
        padding: 48px 0 76px;
        background-color: #F1F7F3;
        background-image: linear-gradient(90deg, rgba(240, 249, 255, 0.96) 0%, rgba(240, 249, 255, 0.85) 44%, rgba(240, 249, 255, 0.3) 72%, rgba(240, 249, 255, 0) 100%), url("{{ asset('images/hero-community.jpg') }}");
        background-size: cover;
        background-position: right bottom;
        background-repeat: no-repeat;
    }

    .nx-welcome-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 14px;
        border-radius: 999px;
        background: #E8F5E9;
        color: #1B5E20;
        font-size: 0.78rem;
        font-weight: 700;
        margin-bottom: 16px;
        border: 1px solid #C8E6C9;
    }

    .nx-hero-heading {
        font-size: clamp(2.2rem, 4.4vw, 3.6rem);
        font-weight: 800;
        line-height: 1.1;
        letter-spacing: -0.03em;
        margin-bottom: 16px;
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
        font-size: 1rem;
        line-height: 1.65;
        color: #475569;
        max-width: 500px;
        margin-bottom: 24px;
    }

    .nx-hero-cta {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .nx-btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        border-radius: 999px;
        background: var(--nx-forest);
        color: #FFFFFF !important;
        font-size: 0.92rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
        box-shadow: 0 4px 14px rgba(40, 89, 67, 0.25);
    }

    .nx-btn-primary:hover {
        background: var(--nx-forest-hover);
        transform: translateY(-2px);
    }

    .nx-btn-outline {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 22px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.9);
        color: var(--nx-forest) !important;
        font-size: 0.92rem;
        font-weight: 700;
        text-decoration: none;
        border: 1.5px solid var(--nx-forest);
        transition: all 0.2s ease;
    }

    .nx-btn-outline:hover {
        background: var(--nx-mint-soft);
        transform: translateY(-2px);
    }

    /* 3. FLOATING HIGHLIGHT BAR (TANPA JUMLAH POPULASI) */
    .nx-highlight-section {
        margin-top: -36px;
        position: relative;
        z-index: 10;
        margin-bottom: 48px;
    }

    .nx-highlight-card {
        background: #FFFFFF;
        border-radius: 18px;
        border: 1px solid var(--nx-border);
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 14px;
        height: 100%;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .nx-highlight-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.08);
    }

    .nx-highlight-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: var(--nx-mint-soft);
        color: var(--nx-forest);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .nx-highlight-title {
        font-size: 0.92rem;
        font-weight: 800;
        color: var(--nx-dark-text);
        margin: 0;
        line-height: 1.2;
    }

    .nx-highlight-sub {
        font-size: 0.74rem;
        color: var(--nx-secondary-text);
        margin: 2px 0 0;
        line-height: 1.3;
    }

    /* 4. FITUR UNGGULAN (GRID 3x2) */
    .nx-section-header {
        text-align: center;
        margin-bottom: 36px;
    }

    .nx-section-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 14px;
        border-radius: 999px;
        background: var(--nx-mint-soft);
        color: var(--nx-forest);
        font-size: 0.75rem;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .nx-section-title {
        font-size: clamp(1.6rem, 3.2vw, 2.2rem);
        font-weight: 800;
        color: var(--nx-dark-text);
        letter-spacing: -0.02em;
        margin-bottom: 8px;
    }

    .nx-section-subtitle {
        font-size: 0.92rem;
        color: var(--nx-secondary-text);
        max-width: 580px;
        margin: 0 auto;
    }

    .nx-card-feature {
        background: #FFFFFF;
        border-radius: 20px;
        border: 1px solid var(--nx-border);
        padding: 22px;
        height: 100%;
        display: flex;
        flex-direction: column;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        text-decoration: none;
        color: inherit;
    }

    .nx-card-feature:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.07);
        color: inherit;
    }

    .nx-feat-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        margin-bottom: 16px;
        flex-shrink: 0;
    }

    .nx-feat-icon-box.green { background: #ECFDF5; color: #059669; }
    .nx-feat-icon-box.blue { background: #EFF6FF; color: #2563EB; }
    .nx-feat-icon-box.amber { background: #FFFBEB; color: #D97706; }
    .nx-feat-icon-box.purple { background: #FAF5FF; color: #7C3AED; }
    .nx-feat-icon-box.rose { background: #FFF1F2; color: #E11D48; }
    .nx-feat-icon-box.cyan { background: #ECFEFF; color: #0891B2; }

    .nx-card-feature h4 {
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--nx-dark-text);
        margin-bottom: 8px;
    }

    .nx-card-feature p {
        font-size: 0.82rem;
        line-height: 1.55;
        color: var(--nx-secondary-text);
        margin: 0;
        flex-grow: 1;
    }

    .nx-feat-arrow {
        margin-top: 14px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.78rem;
        font-weight: 700;
        color: var(--nx-forest);
    }

    /* 5. SPOTLIGHT: KEAMANAN CERDAS (DARK NAVY) */
    .nx-spotlight-card {
        background: linear-gradient(135deg, #0B132B 0%, #172554 100%);
        border-radius: 28px;
        padding: 40px;
        color: #FFFFFF;
        box-shadow: 0 20px 40px rgba(11, 19, 43, 0.25);
        border: 1px solid rgba(255, 255, 255, 0.08);
        position: relative;
        overflow: hidden;
    }

    .nx-spotlight-item {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        margin-bottom: 20px;
    }

    .nx-spotlight-item:last-child {
        margin-bottom: 0;
    }

    .nx-spotlight-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: rgba(56, 189, 248, 0.15);
        color: #38BDF8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.05rem;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .nx-spotlight-title {
        font-size: 0.95rem;
        font-weight: 800;
        color: #F8FAFC;
        margin: 0;
    }

    .nx-spotlight-desc {
        font-size: 0.78rem;
        color: #94A3B8;
        margin: 2px 0 0;
        line-height: 1.45;
    }

    /* Preview Gerbang Kanan */
    .nx-gate-preview-box {
        background: rgba(15, 23, 42, 0.75);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 20px;
        padding: 24px;
        backdrop-filter: blur(12px);
    }

    .nx-gate-anim-frame {
        background: #090E1A;
        border-radius: 14px;
        height: 140px;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border: 1px dashed rgba(255, 255, 255, 0.15);
        margin: 14px 0;
    }

    /* 6. LANGKAH PENDAFTARAN */
    .nx-step-card {
        background: #FFFFFF;
        border: 1px solid var(--nx-border);
        border-radius: 20px;
        padding: 24px 20px;
        text-align: center;
        position: relative;
        height: 100%;
    }

    .nx-step-badge {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: var(--nx-mint-soft);
        color: var(--nx-forest);
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        margin-bottom: 12px;
    }

    /* 7. FAQ ACCORDION */
    .nx-faq-item {
        background: #FFFFFF;
        border: 1px solid var(--nx-border);
        border-radius: 16px;
        margin-bottom: 12px;
        overflow: hidden;
    }

    .nx-faq-btn {
        width: 100%;
        text-align: left;
        background: transparent;
        border: none;
        padding: 16px 20px;
        font-weight: 700;
        font-size: 0.92rem;
        color: var(--nx-dark-text);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .nx-faq-btn i {
        transition: transform 0.2s ease;
        color: var(--nx-forest);
    }

    .nx-faq-btn:not(.collapsed) i {
        transform: rotate(180deg);
    }

    .nx-faq-body {
        padding: 0 20px 16px;
        font-size: 0.84rem;
        line-height: 1.6;
        color: var(--nx-secondary-text);
    }

    /* 8. FOOTER */
    .nx-footer {
        background: #F1F5F9;
        border-top: 1px solid var(--nx-border);
        padding: 48px 0 24px;
        margin-top: 64px;
    }

    .nx-footer-col h5 {
        font-size: 0.88rem;
        font-weight: 800;
        color: var(--nx-dark-text);
        margin-bottom: 14px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .nx-footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .nx-footer-links li {
        margin-bottom: 8px;
    }

    .nx-footer-links a {
        color: var(--nx-secondary-text);
        text-decoration: none;
        font-size: 0.82rem;
        transition: color 0.15s ease;
    }

    .nx-footer-links a:hover {
        color: var(--nx-forest);
    }

    /* ==========================================================================
       RESPONSIVE MOBILE BREAKPOINTS (@media max-width: 768px)
       ========================================================================== */
    @media (max-width: 768px) {
        /* Container Breathing Room: 18px-20px padding */
        .container, .container-fluid {
            padding-left: 18px !important;
            padding-right: 18px !important;
        }

        .nx-navbar-wrap {
            padding: 10px 0;
        }

        .nx-nav-pill-bar, .nx-search-box {
            display: none !important;
        }

        /* Hero Mobile Proporsional */
        .nx-hero-section {
            padding: 24px 0 44px;
            background-position: center bottom;
            text-align: center;
        }

        .nx-hero-heading {
            font-size: 1.85rem !important;
            line-height: 1.15;
            margin-bottom: 12px;
        }

        .nx-hero-desc {
            font-size: 0.85rem !important;
            line-height: 1.5;
            margin-left: auto;
            margin-right: auto;
            margin-bottom: 18px;
        }

        .nx-hero-cta {
            justify-content: center;
            gap: 8px;
        }

        .nx-btn-primary, .nx-btn-outline {
            padding: 9px 16px;
            font-size: 0.82rem;
            width: 100%;
            justify-content: center;
        }

        /* Floating Highlight Bar: Grid 2x2 */
        .nx-highlight-section {
            margin-top: -24px;
            margin-bottom: 32px;
        }

        .nx-highlight-card {
            padding: 12px 10px;
            gap: 10px;
            border-radius: 14px;
        }

        .nx-highlight-icon {
            width: 36px;
            height: 36px;
            font-size: 1rem;
            border-radius: 10px;
        }

        .nx-highlight-title {
            font-size: 0.80rem;
        }

        .nx-highlight-sub {
            font-size: 0.68rem;
        }

        /* Fitur Unggulan Mobile */
        .nx-card-feature {
            padding: 16px;
            border-radius: 16px;
        }

        .nx-feat-icon-box {
            width: 40px;
            height: 40px;
            font-size: 1.1rem;
            margin-bottom: 12px;
        }

        .nx-card-feature h4 {
            font-size: 0.95rem;
            margin-bottom: 6px;
        }

        .nx-card-feature p {
            font-size: 0.78rem;
            line-height: 1.45;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Spotlight Dark Navy Mobile */
        .nx-spotlight-card {
            padding: 22px 18px;
            border-radius: 20px;
            margin: 0 4px;
        }

        .nx-gate-preview-box {
            margin-top: 24px;
            padding: 16px;
        }

        .nx-gate-anim-frame {
            height: 110px;
        }

        .nx-spotlight-title {
            font-size: 0.88rem;
        }

        .nx-spotlight-desc {
            font-size: 0.74rem;
        }

        /* Steps & FAQ Mobile */
        .nx-step-card {
            padding: 18px 14px;
            margin-bottom: 10px;
        }

        .nx-faq-btn {
            padding: 14px 16px;
            font-size: 0.86rem;
        }

        .nx-faq-body {
            padding: 0 16px 14px;
            font-size: 0.78rem;
        }

        /* Footer Mobile */
        .nx-footer {
            padding: 32px 0 20px;
            margin-top: 40px;
        }
    }

    /* ==========================================================================
       DARK MODE HARMONIZATION
       ========================================================================== */
    html.dark body, html.dark .nx-landing {
        background: #0A0F1E !important;
        color: #F1F5F9 !important;
    }

    html.dark .nx-navbar-wrap {
        background: rgba(10, 15, 30, 0.92);
        border-bottom-color: rgba(30, 41, 59, 0.8);
    }

    html.dark .nx-brand-name { color: #38BDF8 !important; }
    html.dark .nx-brand-tagline { color: #94A3B8 !important; }

    html.dark .nx-nav-pill-bar {
        background: #131B2E;
        border-color: #1E2D4A;
    }

    html.dark .nx-nav-item-link {
        color: #94A3B8;
    }

    html.dark .nx-nav-item-link:hover, html.dark .nx-nav-item-link.active {
        background: #172554;
        color: #38BDF8;
    }

    html.dark .nx-hero-section {
        background-color: #0A0F1E !important;
        background-image: linear-gradient(90deg, rgba(10, 15, 30, 0.96) 0%, rgba(10, 15, 30, 0.88) 44%, rgba(10, 15, 30, 0.4) 72%, rgba(10, 15, 30, 0.2) 100%), url("{{ asset('images/hero-community.jpg') }}") !important;
    }

    html.dark .nx-hero-heading .line-green { color: #F8FAFC !important; }
    html.dark .nx-hero-heading .line-blue { color: #38BDF8 !important; }
    html.dark .nx-hero-desc { color: #94A3B8 !important; }

    html.dark .nx-btn-outline {
        background: #131B2E !important;
        border-color: #38BDF8 !important;
        color: #38BDF8 !important;
    }

    html.dark .nx-highlight-card, 
    html.dark .nx-card-feature,
    html.dark .nx-step-card,
    html.dark .nx-faq-item {
        background: #131B2E !important;
        border-color: #1E2D4A !important;
        color: #F1F5F9 !important;
    }

    html.dark .nx-highlight-title,
    html.dark .nx-section-title,
    html.dark .nx-card-feature h4,
    html.dark .nx-faq-btn,
    html.dark .nx-footer-col h5 {
        color: #F1F5F9 !important;
    }

    html.dark .nx-highlight-sub,
    html.dark .nx-section-subtitle,
    html.dark .nx-card-feature p,
    html.dark .nx-faq-body,
    html.dark .nx-footer-links a {
        color: #94A3B8 !important;
    }

    html.dark .nx-footer {
        background: #080D1A !important;
        border-top-color: #1E293B !important;
    }
</style>

<div class="nx-landing">
    <!-- 1. NAVBAR STICKY -->
    <header class="nx-navbar-wrap">
        <div class="container">
            <div class="nx-navbar-inner">
                <!-- Brand -->
                <a href="{{ route('home') }}" class="nx-brand">
                    <span class="nx-brand-icon">
                        <i class="fas fa-leaf" style="color: var(--nx-forest); font-size: 1.25rem;"></i>
                    </span>
                    <div>
                        <h1 class="nx-brand-name">NexaNest</h1>
                        <p class="nx-brand-tagline">Smart &amp; Green Community RW 018</p>
                    </div>
                </a>

                <!-- Desktop Pill Navigation -->
                <nav class="nx-nav-pill-bar d-none d-lg-inline-flex" aria-label="Navigasi Utama">
                    <a href="#beranda" class="nx-nav-item-link active"><i class="fas fa-home"></i> Beranda</a>
                    <a href="#fiturUnggulan" class="nx-nav-item-link"><i class="fas fa-layer-group"></i> Fitur</a>
                    <a href="#keamanan" class="nx-nav-item-link"><i class="fas fa-shield-halved"></i> Keamanan IoT</a>
                    <a href="#faq" class="nx-nav-item-link"><i class="fas fa-circle-question"></i> FAQ</a>
                </nav>

                <!-- Actions: Theme Toggle + Login / Dashboard -->
                <div class="nx-nav-actions">
                    <button type="button" class="nx-theme-toggle-btn" id="nxThemeToggleBtnLanding" title="Beralih Tema (Dark/Light)">
                        <i class="far fa-moon dark-icon-moon"></i>
                        <i class="far fa-sun dark-icon-sun"></i>
                    </button>

                    @auth
                        <a href="{{ route('dashboard') }}" class="nx-btn-login">
                            <i class="fas fa-gauge-high"></i> <span>Dashboard</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="nx-btn-login">
                            <i class="fas fa-arrow-right-to-bracket"></i> <span>Login</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- 2. HERO SECTION -->
    <section class="nx-hero-section" id="beranda">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-6">
                    <div class="nx-welcome-badge">
                        <i class="fas fa-leaf"></i> Selamat Datang di Ekosistem Digital RW 018
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
                        Wujudkan lingkungan yang lebih baik bersama warga melalui layanan digital yang mudah, transparan, terintegrasi IoT, dan aman terpercaya.
                    </p>

                    <div class="nx-hero-cta">
                        @auth
                            <a href="{{ route('dashboard') }}" class="nx-btn-primary">
                                Masuk ke Dashboard <i class="fas fa-arrow-right"></i>
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="nx-btn-primary">
                                Daftar Sekarang <i class="fas fa-arrow-right"></i>
                            </a>
                        @endauth

                        <a href="#fiturUnggulan" class="nx-btn-outline">
                            <i class="fas fa-circle-play"></i> Pelajari Lebih Lanjut
                        </a>
                    </div>
                </div>

                <div class="col-lg-6 d-none d-lg-block">
                    <!-- Spasi visual untuk ilustrasi jalan hijau hero -->
                </div>
            </div>
        </div>
    </section>

    <!-- 3. FLOATING HIGHLIGHT BAR (4 POIN RINGKAS TANPA POPULASI) -->
    <section class="nx-highlight-section">
        <div class="container">
            <div class="row g-2 g-md-3">
                <div class="col-6 col-md-3">
                    <div class="nx-highlight-card">
                        <div class="nx-highlight-icon">
                            <i class="fas fa-location-dot"></i>
                        </div>
                        <div>
                            <h3 class="nx-highlight-title">RW 018 Aktif</h3>
                            <p class="nx-highlight-sub">Wilayah Operasional</p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="nx-highlight-card">
                        <div class="nx-highlight-icon" style="background: #EFF6FF; color: #2563EB;">
                            <i class="fas fa-building-user"></i>
                        </div>
                        <div>
                            <h3 class="nx-highlight-title">5 RT Terintegrasi</h3>
                            <p class="nx-highlight-sub">RT 006 – RT 010</p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="nx-highlight-card">
                        <div class="nx-highlight-icon" style="background: #FFFBEB; color: #D97706;">
                            <i class="fas fa-torii-gate"></i>
                        </div>
                        <div>
                            <h3 class="nx-highlight-title">24/7 Smart Gate</h3>
                            <p class="nx-highlight-sub">Sistem RFID &amp; GPS</p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="nx-highlight-card">
                        <div class="nx-highlight-icon" style="background: #FAF5FF; color: #7C3AED;">
                            <i class="fas fa-shield-halved"></i>
                        </div>
                        <div>
                            <h3 class="nx-highlight-title">Layanan Terpadu</h3>
                            <p class="nx-highlight-sub">Administrasi &amp; Keamanan</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. SECTION JELAJAHI FITUR UNGGULAN (6 KARTU KURASI INTI) -->
    <section class="py-4 py-md-5" id="fiturUnggulan">
        <div class="container">
            <div class="nx-section-header">
                <span class="nx-section-pill"><i class="fas fa-sparkles"></i> Ekosistem Lengkap</span>
                <h3 class="nx-section-title">Jelajahi Fitur Unggulan</h3>
                <p class="nx-section-subtitle">
                    Inovasi tata kelola lingkungan terpadu untuk memberikan kenyamanan dan transparansi maksimal bagi seluruh warga.
                </p>
            </div>

            <div class="row g-3 g-md-4">
                <!-- 1. Keuangan & Iuran -->
                <div class="col-12 col-md-6 col-lg-4">
                    <a href="{{ auth()->check() ? (Route::has('laporan.iuran') ? route('laporan.iuran') : url('/laporan/iuran')) : route('login') }}" class="nx-card-feature">
                        <div class="nx-feat-icon-box green">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <h4>Keuangan &amp; Iuran</h4>
                        <p>Kelola iuran, kas, dan laporan keuangan warga secara transparan dan akuntabel.</p>
                        <span class="nx-feat-arrow">Buka Modul <i class="fas fa-arrow-right"></i></span>
                    </a>
                </div>

                <!-- 2. Surat Pengantar Online -->
                <div class="col-12 col-md-6 col-lg-4">
                    <a href="{{ auth()->check() ? (Route::has('surat.index') ? route('surat.index') : url('/surat')) : route('login') }}" class="nx-card-feature">
                        <div class="nx-feat-icon-box blue">
                            <i class="fas fa-file-signature"></i>
                        </div>
                        <h4>Surat Pengantar Online</h4>
                        <p>Pengajuan surat pengantar RT/RW secara digital tanpa perlu antre dengan verifikasi cepat.</p>
                        <span class="nx-feat-arrow">Ajukan Surat <i class="fas fa-arrow-right"></i></span>
                    </a>
                </div>

                <!-- 3. Lapor QR & Pengaduan -->
                <div class="col-12 col-md-6 col-lg-4">
                    <a href="{{ auth()->check() ? (Route::has('pengaduan.create') ? route('pengaduan.create', ['scan' => 1]) : url('/pengaduan/create')) : route('login') }}" class="nx-card-feature">
                        <div class="nx-feat-icon-box rose">
                            <i class="fas fa-qrcode"></i>
                        </div>
                        <h4>Lapor QR &amp; Pengaduan</h4>
                        <p>Laporkan masalah fasilitas atau lampu jalan via scan QR code &amp; pengaduan cepat.</p>
                        <span class="nx-feat-arrow">Lapor Cepat <i class="fas fa-arrow-right"></i></span>
                    </a>
                </div>

                <!-- 4. Peminjaman Fasilitas -->
                <div class="col-12 col-md-6 col-lg-4">
                    <a href="{{ auth()->check() ? (Route::has('fasilitas.index') ? route('fasilitas.index') : url('/fasilitas')) : route('login') }}" class="nx-card-feature">
                        <div class="nx-feat-icon-box cyan">
                            <i class="fas fa-building"></i>
                        </div>
                        <h4>Peminjaman Fasilitas</h4>
                        <p>Pesan dan pinjam fasilitas umum warga (lapangan, balai warga, tenda) dengan mudah.</p>
                        <span class="nx-feat-arrow">Cek Jadwal <i class="fas fa-arrow-right"></i></span>
                    </a>
                </div>

                <!-- 5. Bank Sampah & Posyandu -->
                <div class="col-12 col-md-6 col-lg-4">
                    <a href="{{ auth()->check() ? (Route::has('bank_sampah.index') ? route('bank_sampah.index') : url('/bank-sampah')) : route('login') }}" class="nx-card-feature">
                        <div class="nx-feat-icon-box amber">
                            <i class="fas fa-recycle"></i>
                        </div>
                        <h4>Bank Sampah &amp; Posyandu</h4>
                        <p>Program lingkungan berkelanjutan serta pantauan jadwal kesehatan dan imunisasi keluarga.</p>
                        <span class="nx-feat-arrow">Lihat Program <i class="fas fa-arrow-right"></i></span>
                    </a>
                </div>

                <!-- 6. UMKM & Agenda Warga -->
                <div class="col-12 col-md-6 col-lg-4">
                    <a href="{{ auth()->check() ? (Route::has('umkm.index') ? route('umkm.index') : url('/umkm')) : route('login') }}" class="nx-card-feature">
                        <div class="nx-feat-icon-box purple">
                            <i class="fas fa-store"></i>
                        </div>
                        <h4>UMKM &amp; Agenda Warga</h4>
                        <p>Dukung usaha warga sekitar dan pantau jadwal agenda kegiatan gotong royong RT/RW.</p>
                        <span class="nx-feat-arrow">Jelajahi UMKM <i class="fas fa-arrow-right"></i></span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. SECTION SPOTLIGHT: KEAMANAN CERDAS & PROTEKSI DATA (DARK NAVY) -->
    <section class="py-4 py-md-5" id="keamanan">
        <div class="container">
            <div class="nx-spotlight-card">
                <div class="row align-items-center g-4">
                    <!-- Kolom Kiri: 4 Poin Keamanan -->
                    <div class="col-lg-7">
                        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-3" style="background: rgba(56, 189, 248, 0.15); color: #38BDF8; font-size: 0.76rem; font-weight: 700;">
                            <i class="fas fa-shield-halved"></i> IoT &amp; Cybersecurity Architecture
                        </div>
                        <h3 class="fw-bold mb-3" style="font-size: clamp(1.4rem, 2.8vw, 1.9rem); color: #F8FAFC;">
                            Keamanan Cerdas &amp; Proteksi Data Warga
                        </h3>
                        <p class="text-secondary small mb-4" style="color: #94A3B8 !important; max-width: 520px;">
                            Infrastruktur otomatisasi gerbang terpadu mikrokontroler ESP32 + RC522 yang dirancang kokoh dengan standar keamanan data tingkat lanjut.
                        </p>

                        <!-- 4 Poin Ikon Bersih -->
                        <div class="nx-spotlight-item">
                            <div class="nx-spotlight-icon"><i class="fas fa-id-card"></i></div>
                            <div>
                                <h5 class="nx-spotlight-title">Smart Gate IoT &amp; e-KTP RFID</h5>
                                <p class="nx-spotlight-desc">Akses gerbang otomatis terhubung mikrokontroler ESP32 &amp; pembaca RFID RC522 terenkripsi.</p>
                            </div>
                        </div>

                        <div class="nx-spotlight-item">
                            <div class="nx-spotlight-icon"><i class="fas fa-location-crosshairs"></i></div>
                            <div>
                                <h5 class="nx-spotlight-title">Buka Portal Darurat GPS 20m</h5>
                                <p class="nx-spotlight-desc">Verifikasi geofencing lokasi real-time radius 5-20 meter khusus warga terdaftar saat kartu tertinggal.</p>
                            </div>
                        </div>

                        <div class="nx-spotlight-item">
                            <div class="nx-spotlight-icon"><i class="fas fa-users-gear"></i></div>
                            <div>
                                <h5 class="nx-spotlight-title">Validasi NIK &amp; Akses Multi-Role</h5>
                                <p class="nx-spotlight-desc">Pemisahan hak akses ketat antara Super Admin, Pengurus RW, Ketua RT, dan Warga.</p>
                            </div>
                        </div>

                        <div class="nx-spotlight-item">
                            <div class="nx-spotlight-icon"><i class="fas fa-lock"></i></div>
                            <div>
                                <h5 class="nx-spotlight-title">Enkripsi &amp; Log Audit</h5>
                                <p class="nx-spotlight-desc">Pencatatan aktivitas tap kartu dan audit trail menyeluruh tanpa menyimpan data sensitif terbuka.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom Kanan: Preview Minimalis Gerbang Utama -->
                    <div class="col-lg-5">
                        <div class="nx-gate-preview-box">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">
                                        <i class="fas fa-signal me-1"></i> ONLINE
                                    </span>
                                    <strong class="text-white small">Gerbang Utama RW 018</strong>
                                </div>
                                <small class="text-muted font-monospace" style="font-size: 0.70rem;">ESP32-GATE-01</small>
                            </div>

                            <!-- Ilustrasi Palang Gerbang -->
                            <div class="nx-gate-anim-frame">
                                <div class="text-center">
                                    <i class="fas fa-torii-gate fa-2x mb-2" style="color: #38BDF8;"></i>
                                    <div class="small fw-bold text-white font-monospace">STATUS: STANDBY</div>
                                    <div class="text-muted" style="font-size: 0.70rem;">Tertutup • Menunggu Tap e-KTP</div>
                                </div>
                            </div>

                            <div class="p-2.5 rounded-3 border d-flex align-items-center justify-content-between" style="background: rgba(255, 255, 255, 0.05); border-color: rgba(255, 255, 255, 0.1) !important;">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-shield-check text-info"></i>
                                    <small class="text-light" style="font-size: 0.75rem;">Geofencing Geolocation</small>
                                </div>
                                <span class="badge bg-primary-subtle text-primary font-monospace" style="font-size: 0.70rem;">Aktif 20m</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. SECTION: MULAI DALAM 3 LANGKAH MUDAH -->
    <section class="py-4 py-md-5">
        <div class="container">
            <div class="nx-section-header">
                <span class="nx-section-pill"><i class="fas fa-route"></i> Alur Praktis</span>
                <h3 class="nx-section-title">Mulai dalam 3 Langkah Mudah</h3>
                <p class="nx-section-subtitle">Akses seluruh fasilitas lingkungan dan gerbang otomatis hanya dalam hitungan menit.</p>
            </div>

            <div class="row g-3 g-md-4">
                <div class="col-12 col-md-4">
                    <div class="nx-step-card">
                        <div class="nx-step-badge">1</div>
                        <h4 class="fw-bold mb-2" style="font-size: 1.05rem;">Daftar &amp; Verifikasi NIK</h4>
                        <p class="text-secondary small mb-0">
                            Registrasi akun warga sesuai nomor RT (006–010) dan data 16 digit NIK resmi untuk verifikasi pengurus.
                        </p>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="nx-step-card">
                        <div class="nx-step-badge">2</div>
                        <h4 class="fw-bold mb-2" style="font-size: 1.05rem;">Rekam e-KTP / Aktivasi</h4>
                        <p class="text-secondary small mb-0">
                            Tautkan chip RFID e-KTP Anda melalui perangkat perekam pengurus RT/RW untuk aktivasi akses kartu.
                        </p>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="nx-step-card">
                        <div class="nx-step-badge">3</div>
                        <h4 class="fw-bold mb-2" style="font-size: 1.05rem;">Akses Layanan &amp; Gerbang</h4>
                        <p class="text-secondary small mb-0">
                            Nikmati kemudahan tap e-KTP di gerbang otomatis, bayar iuran transparan, dan buat surat secara digital.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 7. SECTION FAQ ACCORDION -->
    <section class="py-4 py-md-5" id="faq">
        <div class="container" style="max-width: 820px;">
            <div class="nx-section-header">
                <span class="nx-section-pill"><i class="fas fa-circle-question"></i> Pusat Informasi</span>
                <h3 class="nx-section-title">Pertanyaan Umum (FAQ)</h3>
                <p class="nx-section-subtitle">Jawaban atas pertanyaan seputar penggunaan platform dan Smart Gate NexaNest.</p>
            </div>

            <div class="accordion" id="accordionFaq">
                <!-- FAQ 1 -->
                <div class="nx-faq-item">
                    <button class="nx-faq-btn" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1" aria-expanded="true">
                        <span>Bagaimana cara merekam e-KTP untuk Smart Gate?</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div id="faqCollapse1" class="collapse show" data-bs-parent="#accordionFaq">
                        <div class="nx-faq-body">
                            Warga yang telah memiliki akun terverifikasi cukup menemui pengurus RT atau Admin RW. Pengurus akan membuka modal Rekam e-KTP pada sistem, lalu Anda cukup menempelkan kartu e-KTP ke sensor RFID RC522 sekali saja untuk ditautkan secara permanen.
                        </div>
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="nx-faq-item">
                    <button class="nx-faq-btn collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2" aria-expanded="false">
                        <span>Apakah data NIK dan data pribadi warga aman?</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div id="faqCollapse2" class="collapse" data-bs-parent="#accordionFaq">
                        <div class="nx-faq-body">
                            Sangat aman. Sistem NexaNest membatasi akses data melalui kontrol peran berlapis (RBAC). Nomor identitas dan data sensitif dilindungi, hanya digunakan untuk validasi internal kependudukan warga RW 018, dan tidak pernah dibagikan kepada pihak ketiga.
                        </div>
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="nx-faq-item">
                    <button class="nx-faq-btn collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3" aria-expanded="false">
                        <span>Bagaimana jika kartu e-KTP tertinggal saat di depan gerbang?</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div id="faqCollapse3" class="collapse" data-bs-parent="#accordionFaq">
                        <div class="nx-faq-body">
                            Anda tetap dapat membuka gerbang menggunakan menu <strong>Smart Gate</strong> di smartphone Anda. Tekan tombol <em>Buka Gerbang Darurat via GPS</em> saat berada dalam radius izin gerbang (maksimal 20 meter). Sistem akan memverifikasi koordinat GPS perangkat Anda dan membuka palang secara instan.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 8. FOOTER MINIMALIS & KONTAK TERSENSOR (PRIVASI) -->
    <footer class="nx-footer">
        <div class="container">
            <div class="row g-4 mb-4">
                <div class="col-12 col-md-4">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="nx-brand-icon" style="width: 36px; height: 36px; border-radius: 10px;">
                            <i class="fas fa-leaf" style="color: var(--nx-forest); font-size: 1rem;"></i>
                        </span>
                        <strong class="fs-5 fw-bold" style="color: var(--nx-forest);">NexaNest</strong>
                    </div>
                    <p class="text-secondary small mb-2" style="line-height: 1.5;">
                        Platform Smart &amp; Green Community terpadu untuk lingkungan rukun, asri, transparan, dan berdaya saing digital.
                    </p>
                    <small class="text-muted d-block" style="font-size: 0.72rem;">
                        Wilayah Operasional RW 018 (RT 006, RT 007, RT 008, RT 009, RT 010)
                    </small>
                </div>

                <div class="col-6 col-md-2 nx-footer-col">
                    <h5>Layanan</h5>
                    <ul class="nx-footer-links">
                        <li><a href="{{ route('login') }}">Iuran &amp; Kas RW</a></li>
                        <li><a href="{{ route('login') }}">Surat Pengantar</a></li>
                        <li><a href="{{ route('login') }}">Lapor Fasilitas QR</a></li>
                        <li><a href="{{ route('login') }}">Peminjaman Aset</a></li>
                    </ul>
                </div>

                <div class="col-6 col-md-3 nx-footer-col">
                    <h5>Sistem &amp; IoT</h5>
                    <ul class="nx-footer-links">
                        <li><a href="#keamanan">Smart Gate RFID</a></li>
                        <li><a href="#keamanan">Geofencing GPS</a></li>
                        <li><a href="{{ route('login') }}">Pusat Kendali IoT</a></li>
                        <li><a href="{{ route('login') }}">Audit Log Sistem</a></li>
                    </ul>
                </div>

                <!-- Kontak Tersensor Demi Privasi -->
                <div class="col-12 col-md-3 nx-footer-col">
                    <h5>Contact / Support</h5>
                    <div class="p-3 rounded-3 border" style="background: rgba(255,255,255,0.7); border-color: var(--nx-border) !important;">
                        <div class="d-flex align-items-center gap-2 mb-1.5">
                            <i class="fab fa-telegram text-primary"></i>
                            <span class="small fw-bold">Telegram Pengurus:</span>
                        </div>
                        <div class="font-monospace text-muted small mb-2">
                            t.me/n••••••
                        </div>
                        <a href="{{ route('login') }}" class="btn btn-xs btn-outline-primary d-inline-flex align-items-center gap-1 w-100 justify-content-center py-1.5 mb-1.5" style="border-radius: 8px; font-size: 0.75rem;">
                            <i class="fas fa-lock"></i>
                            <span>Login untuk Hubungi Admin</span>
                        </a>
                        <small class="text-muted d-block" style="font-size: 0.68rem; line-height: 1.3;">
                            *Tautan langsung layanan pengaduan otomatis aktif di pojok kanan bawah setelah masuk.
                        </small>
                    </div>
                </div>
            </div>

            <div class="pt-3 border-top text-center text-secondary small" style="border-color: var(--nx-border) !important; font-size: 0.75rem;">
                &copy; {{ date('Y') }} NexaNest Community. Dikembangkan untuk kemaslahatan warga RW 018.
            </div>
        </div>
    </footer>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Smooth scroll internal anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if (targetId && targetId !== '#') {
                const targetEl = document.querySelector(targetId);
                if (targetEl) {
                    e.preventDefault();
                    targetEl.scrollIntoView({ behavior: 'smooth' });
                }
            }
        });
    });
});
</script>
@endpush
@endsection
