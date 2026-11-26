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
        padding: 56px 0 86px;
        min-height: 520px;
        background-color: #F1F7F3;
        background-image: linear-gradient(90deg, rgba(240, 249, 255, 0.95) 0%, rgba(240, 249, 255, 0.82) 42%, rgba(240, 249, 255, 0.25) 72%, rgba(240, 249, 255, 0) 100%), url("{{ asset('images/hero-community.jpg') }}");
        background-size: cover;
        background-position: right bottom;
        background-repeat: no-repeat;
        overflow: hidden;
        display: flex;
        align-items: center;
    }

    .nx-kalam-floating {
        position: absolute;
        top: 36px;
        right: 48px;
        z-index: 5;
        transform: rotate(-4deg);
        text-align: center;
        pointer-events: none;
    }

    .nx-kalam-line {
        font-family: 'Kalam', cursive !important;
        font-size: 1.7rem;
        font-weight: 700;
        color: #1B4332;
        line-height: 1.15;
        letter-spacing: -0.01em;
        text-shadow: 0 2px 14px rgba(255, 255, 255, 0.95);
    }

    .nx-kalam-curve {
        display: block;
        margin: 3px auto 0;
        filter: drop-shadow(0 1px 2px rgba(255, 255, 255, 0.9));
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

    /* ==========================================================================
       LANDING PAGE (COVER) DARK MODE OVERRIDES (DARK NAVY -> DEEP INDIGO)
       ========================================================================== */
    html.dark body {
        background: #0A0F1E !important;
        color: #F1F5F9 !important;
    }

    html.dark .nx-landing {
        background: #0A0F1E !important;
        color: #F1F5F9 !important;
    }

    /* 1. Global NexaNest Brand Logo Dark Mode */
    html.dark .nexanest-brand-logo .nx-brand-title {
        color: #F8FAFC !important;
    }

    html.dark .nexanest-brand-logo .nx-brand-sub {
        color: #94A3B8 !important;
    }

    html.dark .nexanest-brand-logo .nx-logo-house {
        stroke: #38BDF8 !important;
    }

    html.dark .nexanest-brand-logo .nx-logo-leaf-1 {
        fill: #10B981 !important;
        stroke: #059669 !important;
    }

    html.dark .nexanest-brand-logo .nx-logo-leaf-2 {
        fill: #34D399 !important;
        stroke: #10B981 !important;
    }

    html.dark .nexanest-brand-logo .nx-logo-leaf-vein {
        stroke: #D1FAE5 !important;
    }

    /* 2. Top Navbar Landing Page */
    html.dark .nx-navbar-wrap {
        background: rgba(13, 20, 38, 0.92) !important;
        backdrop-filter: blur(12px) !important;
        -webkit-backdrop-filter: blur(12px) !important;
        border-bottom: 1px solid #1E293B !important;
    }

    html.dark .nx-nav-pill-bar {
        background: #0D1426 !important;
        border: 1px solid #1E2D4A !important;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.45) !important;
    }

    html.dark .nx-nav-item-link {
        color: #CBD5E1 !important;
    }

    html.dark .nx-nav-item-link:hover {
        background: rgba(56, 189, 248, 0.12) !important;
        color: #38BDF8 !important;
    }

    html.dark .nx-nav-item-link.active {
        background: #172554 !important;
        color: #38BDF8 !important;
    }

    html.dark .nx-brand-tagline {
        color: #94A3B8 !important;
    }

    html.dark .nx-search-box input {
        background: #0F172A !important;
        border: 1px solid #273654 !important;
        color: #F1F5F9 !important;
    }

    html.dark .nx-search-box input:focus {
        border-color: #38BDF8 !important;
        box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.2) !important;
    }

    html.dark .nx-search-box i {
        color: #64748B !important;
    }

    /* 3. Hero Section */
    html.dark .nx-hero-section {
        background-color: #0A0F1E !important;
        background-image: linear-gradient(90deg, rgba(10, 15, 30, 0.96) 0%, rgba(10, 15, 30, 0.88) 42%, rgba(10, 15, 30, 0.45) 72%, rgba(10, 15, 30, 0.2) 100%), url("{{ asset('images/hero-community.jpg') }}") !important;
    }

    html.dark .nx-welcome-badge {
        background: rgba(16, 185, 129, 0.16) !important;
        color: #34D399 !important;
        border: 1px solid rgba(52, 211, 153, 0.3) !important;
    }

    html.dark .nx-hero-heading .line-green {
        color: #F8FAFC !important;
    }

    html.dark .nx-hero-heading .line-blue {
        color: #38BDF8 !important;
    }

    html.dark .nx-hero-desc {
        color: #94A3B8 !important;
    }

    html.dark .nx-btn-outline {
        background: rgba(19, 27, 46, 0.75) !important;
        border: 1.5px solid #38BDF8 !important;
        color: #38BDF8 !important;
    }

    html.dark .nx-btn-outline:hover {
        background: rgba(56, 189, 248, 0.2) !important;
        color: #FFFFFF !important;
        border-color: #38BDF8 !important;
    }

    html.dark .nx-kalam-line {
        color: #34D399 !important;
        text-shadow: 0 2px 14px rgba(0, 0, 0, 0.85) !important;
    }

    html.dark .nx-kalam-curve path {
        stroke: #34D399 !important;
    }

    /* 4. Lekukan Wave Bawah Hero */
    html.dark .nx-wave-divider svg path {
        fill: #0A0F1E !important;
    }

    /* 5. Section "Jelajahi Fitur Unggulan" */
    html.dark .nx-features-section {
        background: #0A0F1E !important;
        background-color: #0A0F1E !important;
    }

    html.dark .nx-section-title {
        color: #F1F5F9 !important;
    }

    html.dark .nx-section-subtitle {
        color: #94A3B8 !important;
    }

    html.dark .nx-section-title-line {
        background: #1E3A5F !important;
    }

    html.dark .nx-section-header i.fa-seedling {
        color: #34D399 !important;
    }

    /* 6. 6 Feature Cards Grid */
    html.dark .nx-feature-card {
        background: #131B2E !important;
        border: 1px solid #1E2D4A !important;
        color: #F1F5F9 !important;
        box-shadow: 0 10px 28px rgba(0, 0, 0, 0.35) !important;
    }

    html.dark .nx-feature-card:hover {
        border-color: #3B82F6 !important;
        box-shadow: 0 16px 34px rgba(59, 130, 246, 0.18) !important;
        transform: translateY(-5px);
        color: #F1F5F9 !important;
    }

    html.dark .nx-feature-title {
        color: #F1F5F9 !important;
    }

    html.dark .nx-feature-desc {
        color: #94A3B8 !important;
    }

    /* Kotak Ikon Translucent Gelap Sesuai Warna Masing-Masing */
    html.dark .nx-accent-green .nx-feature-icon {
        background: rgba(16, 185, 129, 0.16) !important;
        color: #34D399 !important;
    }

    html.dark .nx-accent-blue .nx-feature-icon {
        background: rgba(56, 189, 248, 0.16) !important;
        color: #38BDF8 !important;
    }

    html.dark .nx-accent-red .nx-feature-icon {
        background: rgba(239, 68, 68, 0.16) !important;
        color: #F87171 !important;
    }

    /* Tombol Panah Bulat #1E293B dengan Ikon #38BDF8 */
    html.dark .nx-feature-arrow,
    html.dark .nx-accent-green .nx-feature-arrow,
    html.dark .nx-accent-blue .nx-feature-arrow,
    html.dark .nx-accent-red .nx-feature-arrow {
        background: #1E293B !important;
        color: #38BDF8 !important;
    }

    html.dark .nx-feature-card:hover .nx-feature-arrow,
    html.dark .nx-accent-green:hover .nx-feature-arrow,
    html.dark .nx-accent-blue:hover .nx-feature-arrow,
    html.dark .nx-accent-red:hover .nx-feature-arrow {
        background: #2563EB !important;
        color: #FFFFFF !important;
    }

    /* 7. Footer Pill */
    html.dark .nx-footer-pill {
        background: #131B2E !important;
        border: 1px solid #1E2D4A !important;
        color: #CBD5E1 !important;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.35) !important;
    }

    html.dark .nx-footer-pill i {
        color: #34D399 !important;
    }

    /* 8. Modals & Dropdown on Landing Page */
    html.dark .modal-content {
        background: #131B2E !important;
        color: #F1F5F9 !important;
        border: 1px solid #1E2D4A !important;
    }

    html.dark .modal-header,
    html.dark .modal-footer {
        border-color: #1E2D4A !important;
    }

    html.dark .modal-title {
        color: #F1F5F9 !important;
    }

    html.dark .modal-body,
    html.dark .modal-body p {
        color: #CBD5E1 !important;
    }

    html.dark .modal-body .text-muted {
        color: #94A3B8 !important;
    }

    html.dark .modal-content .btn-close {
        filter: invert(1) grayscale(100%) brightness(200%);
    }

    html.dark .dropdown-menu {
        background-color: #131B2E !important;
        border: 1px solid #1E2D4A !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.45) !important;
    }

    html.dark .dropdown-item {
        color: #E2E8F0 !important;
    }

    html.dark .dropdown-item:hover {
        background-color: #1A2744 !important;
        color: #38BDF8 !important;
    }

    html.dark .nx-corner-leaf-left,
    html.dark .nx-corner-leaf-right {
        opacity: 0.35 !important;
        filter: brightness(0.65) contrast(1.2);
    }
</style>

<div class="nx-landing">

    <!-- ==================== 3. STICKY HORIZONTAL NAVBAR ==================== -->
    <header class="nx-navbar-wrap">
        <div class="container">
            <div class="nx-navbar-inner">
                <!-- Left: Logo + Brand NexaNest + Tagline (Vector Resmi Tanpa Kotak Hijau) -->
                <x-nexanest-logo size="md" />

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

                    <button type="button" class="nx-theme-toggle-btn me-2" id="nxThemeToggleBtnLanding" title="Beralih Tema (Dark/Light)">
                        <i class="far fa-moon dark-icon-moon"></i>
                        <i class="far fa-sun dark-icon-sun"></i>
                    </button>

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
    <section class="nx-hero-section" id="beranda">
        <!-- Floating Handwritten Motto Callout -->
        <div class="nx-kalam-floating d-none d-md-block">
            <div class="nx-kalam-line">Bersama Jaga</div>
            <div class="nx-kalam-line">Lingkungan Kita</div>
            <svg class="nx-kalam-curve" width="145" height="14" viewBox="0 0 145 14" fill="none">
                <path d="M4 10C45 3 98 3 141 9" stroke="#1B4332" stroke-width="2.6" stroke-linecap="round"/>
            </svg>
        </div>
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

                <!-- Right Column: Visual Spacer allowing hero-community illustration to shine -->
                <div class="col-lg-6 d-none d-lg-block position-relative" style="min-height: 380px;">
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
