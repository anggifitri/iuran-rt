@extends('layouts.app')

@section('title', 'Masuk ke Akun — NexaNest Smart & Green Community')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    :root {
        --nx-forest: #285943;
        --nx-forest-dark: #1E4633;
        --nx-sage: #A8C9B5;
        --nx-pastel-blue: #A9C9E8;
        --nx-mint: #DCEBDD;
        --nx-mint-soft: #EEF6EF;
        --nx-powder: #DCEAF5;
        --nx-bg: #F7F9F7;
        --nx-white: #FFFFFF;
        --nx-text: #263449;
        --nx-muted: #718096;
        --nx-border: #E5EBE6;
        --nx-soft-red: #FCE8E9;
        --nx-danger: #D94E4E;
    }

    body {
        font-family: 'Plus Jakarta Sans', 'Inter', sans-serif !important;
        background: radial-gradient(circle at 10% 15%, #E6F2E7 0%, #F7F9F7 48%, #E7F1FA 100%) !important;
        color: var(--nx-text) !important;
        min-height: 100vh;
        margin: 0;
    }

    .theme-toggle-btn,
    nav.navbar {
        display: none !important;
    }

    .nx-auth-page {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 28px 16px;
        position: relative;
        overflow: hidden;
    }

    .nx-auth-shell {
        width: 100%;
        max-width: 1060px;
        background: var(--nx-white);
        border-radius: 28px;
        border: 1px solid var(--nx-border);
        box-shadow: 0 22px 60px rgba(40, 89, 67, 0.09);
        display: grid;
        grid-template-columns: 1.08fr 1fr;
        overflow: hidden;
        position: relative;
        z-index: 2;
    }

    /* ============ LEFT VISUAL BRANDING PANEL ============ */
    .nx-auth-visual {
        background: linear-gradient(150deg, #DCEBDD 0%, #E7F2EE 52%, #DCEAF5 100%);
        padding: 42px 40px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
        border-right: 1px solid var(--nx-border);
    }

    .nx-auth-visual::before {
        content: '';
        position: absolute;
        width: 300px;
        height: 300px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.35);
        top: -90px;
        right: -90px;
        pointer-events: none;
    }

    html.dark .nx-auth-visual::before {
        background: rgba(255, 255, 255, 0.03) !important;
    }

    .nx-auth-theme-btn {
        width: 34px;
        height: 34px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.85);
        border: 1px solid rgba(40, 89, 67, 0.15);
        color: var(--nx-forest);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.85rem;
    }

    .nx-auth-theme-btn:hover {
        background: var(--nx-white);
        transform: translateY(-1px);
    }

    html.dark .nx-auth-theme-btn {
        background: #17223B !important;
        border-color: #243454 !important;
        color: #FDE047 !important;
    }

    html.dark .nx-auth-theme-btn:hover {
        background: #1E2D4A !important;
        border-color: #38BDF8 !important;
    }

    .nx-illustration-box {
        margin: 18px 0;
        position: relative;
        z-index: 2;
    }

    .nx-img-frame {
        position: relative;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 12px 30px rgba(40, 89, 67, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.8);
        max-height: 220px;
    }

    .nx-community-img {
        width: 100%;
        height: 210px;
        object-fit: cover;
        transition: transform 0.35s ease;
    }

    .nx-img-day {
        display: block;
    }

    .nx-img-night {
        display: none;
    }

    .nx-img-frame:hover .nx-community-img {
        transform: scale(1.03);
    }

    .nx-img-overlay {
        display: none !important;
    }

    html.dark .nx-img-frame {
        border: 1px solid rgba(148, 163, 184, 0.2) !important;
        box-shadow: 0 14px 36px rgba(0, 0, 0, 0.35) !important;
    }

    html.dark .nx-img-day {
        display: none !important;
    }

    html.dark .nx-img-night {
        display: block !important;
    }

    .nx-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.85);
        border: 1px solid rgba(168, 201, 181, 0.4);
        color: #1E3A2F;
        font-size: 0.76rem;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(40, 89, 67, 0.05);
    }

    .nx-chip i {
        color: var(--nx-forest);
    }

    html.dark .nx-chip {
        background: rgba(15, 23, 42, 0.45) !important;
        backdrop-filter: blur(8px) !important;
        -webkit-backdrop-filter: blur(8px) !important;
        border: 1px solid rgba(148, 163, 184, 0.25) !important;
        color: #E2E8F0 !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.35) !important;
    }

    html.dark .nx-chip i {
        color: #38BDF8 !important;
    }

    /* Dark Mode Shell & Form Overrides */
    html.dark body:has(.nx-auth-page),
    html.dark .nx-auth-page {
        background: #0A0F1E !important;
    }

    html.dark .nx-auth-shell {
        background: #0D1426 !important;
        border-color: #1E293B !important;
        box-shadow: 0 22px 60px rgba(0, 0, 0, 0.55) !important;
    }

    html.dark .nx-auth-visual {
        background: linear-gradient(150deg, #0A0F1E 0%, #10192E 52%, #131E38 100%) !important;
        border-right-color: #1E293B !important;
        border-bottom-color: #1E293B !important;
    }

    html.dark .nx-back-home {
        background: rgba(19, 27, 46, 0.85) !important;
        border-color: #1E293B !important;
        color: #CBD5E1 !important;
    }

    html.dark .nx-back-home:hover {
        background: #1E2D4A !important;
        color: #38BDF8 !important;
    }

    html.dark .nx-visual-pill {
        background: rgba(16, 185, 129, 0.16) !important;
        color: #34D399 !important;
    }

    html.dark .nx-visual-title {
        color: #F8FAFC !important;
    }

    html.dark .nx-visual-desc {
        color: #94A3B8 !important;
    }

    html.dark .nx-auth-form-panel {
        background: #0D1426 !important;
    }

    html.dark .nx-form-badge {
        background: rgba(56, 189, 248, 0.16) !important;
        color: #38BDF8 !important;
    }

    html.dark .nx-form-title {
        color: #F8FAFC !important;
    }

    html.dark .nx-form-subtitle {
        color: #94A3B8 !important;
    }

    html.dark .nx-label {
        color: #E2E8F0 !important;
    }

    html.dark .nx-input {
        background: #131B2E !important;
        border-color: #1E2D4A !important;
        color: #F1F5F9 !important;
    }

    html.dark .nx-input:focus {
        background: #17223B !important;
        border-color: #3B82F6 !important;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2) !important;
    }

    html.dark .nx-input-wrap:focus-within .nx-input-icon {
        color: #38BDF8 !important;
    }

    html.dark .nx-eye-btn {
        color: #94A3B8 !important;
    }

    html.dark .nx-eye-btn:hover {
        background: #1E2D4A !important;
        color: #38BDF8 !important;
    }

    html.dark .nx-remember {
        color: #94A3B8 !important;
    }

    html.dark .nx-remember input {
        accent-color: #2563EB !important;
    }

    html.dark .nx-forgot-link {
        color: #38BDF8 !important;
    }

    html.dark .nx-forgot-link:hover {
        color: #60A5FA !important;
    }

    html.dark .nx-submit-btn {
        background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%) !important;
        border: 1px solid rgba(59, 130, 246, 0.4) !important;
        color: #FFFFFF !important;
        box-shadow: 0 10px 24px rgba(37, 99, 235, 0.35) !important;
    }

    html.dark .nx-submit-btn:hover:not(:disabled) {
        background: linear-gradient(135deg, #1D4ED8 0%, #1E40AF 100%) !important;
        box-shadow: 0 14px 28px rgba(37, 99, 235, 0.45) !important;
        color: #FFFFFF !important;
    }

    html.dark .nx-submit-btn *,
    html.dark .nx-submit-btn span,
    html.dark .nx-submit-btn i {
        color: #FFFFFF !important;
    }

    html.dark .nx-auth-footer {
        border-top-color: #1E293B !important;
        color: #94A3B8 !important;
    }

    html.dark .nx-auth-footer a {
        color: #38BDF8 !important;
    }

    .nx-auth-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        position: relative;
        z-index: 2;
    }

    .nx-brand-badge {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
    }

    .nx-brand-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        background: var(--nx-white);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 20px rgba(40, 89, 67, 0.12);
    }

    .nx-brand-name {
        font-weight: 800;
        font-size: 1.25rem;
        color: var(--nx-forest);
        letter-spacing: -0.02em;
        line-height: 1.15;
        margin: 0;
    }

    .nx-brand-tagline {
        font-size: 0.75rem;
        font-weight: 600;
        color: #4A6B5B;
        margin: 0;
    }

    .nx-back-home {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.78);
        border: 1px solid rgba(40, 89, 67, 0.14);
        color: var(--nx-forest);
        font-size: 0.78rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .nx-back-home:hover {
        background: var(--nx-white);
        color: var(--nx-forest-dark);
        transform: translateY(-1px);
    }

    .nx-illustration-box {
        margin: 22px 0;
        background: rgba(255, 255, 255, 0.62);
        border: 1px solid rgba(255, 255, 255, 0.9);
        border-radius: 22px;
        padding: 18px 16px 10px;
        box-shadow: 0 12px 30px rgba(40, 89, 67, 0.06);
        position: relative;
        z-index: 2;
    }

    .nx-visual-copy {
        position: relative;
        z-index: 2;
    }

    .nx-visual-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 999px;
        background: rgba(40, 89, 67, 0.1);
        color: var(--nx-forest);
        font-size: 0.74rem;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .nx-visual-title {
        font-size: 1.65rem;
        font-weight: 800;
        color: var(--nx-forest);
        letter-spacing: -0.025em;
        margin-bottom: 8px;
        line-height: 1.25;
    }

    .nx-visual-desc {
        font-size: 0.92rem;
        color: #465A50;
        line-height: 1.6;
        margin-bottom: 16px;
    }

    .nx-visual-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .nx-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.8);
        color: var(--nx-text);
        font-size: 0.76rem;
        font-weight: 600;
    }

    .nx-chip i {
        color: var(--nx-forest);
    }

    /* ============ RIGHT FORM PANEL ============ */
    .nx-auth-form-panel {
        padding: 46px 44px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background: var(--nx-white);
    }

    .nx-form-head {
        margin-bottom: 26px;
    }

    .nx-form-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 999px;
        background: var(--nx-mint-soft);
        color: var(--nx-forest);
        font-size: 0.74rem;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .nx-form-title {
        font-size: 1.6rem;
        font-weight: 800;
        color: var(--nx-text);
        letter-spacing: -0.02em;
        margin-bottom: 6px;
    }

    .nx-form-subtitle {
        font-size: 0.88rem;
        color: var(--nx-muted);
        margin: 0;
        line-height: 1.5;
    }

    .nx-alert-error {
        background: var(--nx-soft-red);
        border: 1px solid #F5C2C5;
        color: #9E2A2F;
        border-radius: 14px;
        padding: 12px 16px;
        font-size: 0.84rem;
        font-weight: 600;
        margin-bottom: 20px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }

    .nx-alert-status {
        background: var(--nx-mint-soft);
        border: 1px solid var(--nx-sage);
        color: var(--nx-forest);
        border-radius: 14px;
        padding: 12px 16px;
        font-size: 0.84rem;
        font-weight: 600;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .nx-field-group {
        margin-bottom: 18px;
    }

    .nx-label {
        display: block;
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--nx-text);
        margin-bottom: 7px;
    }

    .nx-input-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }

    .nx-input-icon {
        position: absolute;
        left: 15px;
        color: #8899A6;
        font-size: 0.92rem;
        pointer-events: none;
        transition: color 0.2s ease;
    }

    .nx-input {
        width: 100%;
        padding: 12px 42px 12px 42px;
        background: var(--nx-bg) !important;
        border: 1.5px solid var(--nx-border) !important;
        border-radius: 14px !important;
        font-size: 0.9rem;
        color: var(--nx-text) !important;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .nx-input::placeholder {
        color: #9AA8B6;
        font-weight: 400;
    }

    .nx-input:focus {
        outline: none;
        background: var(--nx-white) !important;
        border-color: var(--nx-forest) !important;
        box-shadow: 0 0 0 4px rgba(40, 89, 67, 0.1) !important;
    }

    .nx-input-wrap:focus-within .nx-input-icon {
        color: var(--nx-forest);
    }

    .nx-eye-btn {
        position: absolute;
        right: 12px;
        background: transparent;
        border: none;
        color: var(--nx-muted);
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .nx-eye-btn:hover {
        background: var(--nx-mint-soft);
        color: var(--nx-forest);
    }

    .nx-row-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 22px;
        font-size: 0.83rem;
    }

    .nx-remember {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--nx-muted);
        font-weight: 600;
        cursor: pointer;
        user-select: none;
    }

    .nx-remember input {
        width: 16px;
        height: 16px;
        accent-color: var(--nx-forest);
        border-radius: 4px;
        cursor: pointer;
    }

    .nx-forgot-link {
        color: var(--nx-forest);
        font-weight: 700;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .nx-forgot-link:hover {
        color: var(--nx-forest-dark);
        text-decoration: underline;
    }

    .nx-submit-btn {
        width: 100%;
        padding: 13px 20px;
        border-radius: 14px;
        border: none;
        background: var(--nx-forest);
        color: var(--nx-white);
        font-size: 0.94rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        cursor: pointer;
        box-shadow: 0 10px 24px rgba(40, 89, 67, 0.22);
        transition: all 0.25s ease;
    }

    .nx-submit-btn:hover:not(:disabled) {
        background: var(--nx-forest-dark);
        transform: translateY(-1px);
        box-shadow: 0 14px 28px rgba(40, 89, 67, 0.28);
    }

    .nx-submit-btn:disabled {
        opacity: 0.75;
        cursor: wait;
    }

    .nx-auth-footer {
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid var(--nx-border);
        text-align: center;
        font-size: 0.86rem;
        color: var(--nx-muted);
    }

    .nx-auth-footer a {
        color: var(--nx-forest);
        font-weight: 700;
        text-decoration: none;
    }

    .nx-auth-footer a:hover {
        text-decoration: underline;
    }

    @media (max-width: 991.98px) {
        .nx-auth-shell {
            grid-template-columns: 1fr;
            max-width: 520px;
        }
        .nx-auth-visual {
            padding: 28px 26px;
            border-right: none;
            border-bottom: 1px solid var(--nx-border);
        }
        .nx-illustration-box {
            display: none;
        }
        .nx-auth-form-panel {
            padding: 32px 26px;
        }
    }
</style>
@endpush

@section('content')
<div class="nx-auth-page">
    <div class="nx-auth-shell">

        <!-- KOLOM KIRI: VISUAL BRANDING PANEL -->
        <div class="nx-auth-visual">
            <div class="nx-auth-top">
                <a href="{{ url('/') }}" class="text-decoration-none">
                    <x-nexanest-logo size="md" />
                </a>

                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="nx-theme-toggle-btn nx-auth-theme-btn" id="nxAuthThemeToggle" title="Beralih Tema (Dark/Light)">
                        <i class="far fa-moon dark-icon-moon"></i>
                        <i class="far fa-sun dark-icon-sun"></i>
                    </button>

                    <a href="{{ url('/') }}" class="nx-back-home">
                        <i class="fas fa-arrow-left"></i> Beranda
                    </a>
                </div>
            </div>

            <!-- Ilustrasi Komunitas NexaNest Asli (Day & Night Mode Otomatis) -->
            <div class="nx-illustration-box" aria-hidden="true">
                <div class="nx-img-frame">
                    <img src="{{ asset('images/login-community-day.jpg') }}" alt="NexaNest Community Day" class="nx-community-img nx-img-day">
                    <img src="{{ asset('images/login-community-night.jpg') }}" alt="NexaNest Community Night" class="nx-community-img nx-img-night">
                </div>
            </div>

            <div class="nx-visual-copy">
                <span class="nx-visual-pill">
                    <i class="fas fa-seedling"></i> Smart & Sustainable Neighborhood
                </span>
                <h2 class="nx-visual-title">Selamat Datang Kembali!</h2>
                <p class="nx-visual-desc">
                    Bersama NexaNest, wujudkan lingkungan yang lebih terorganisir, nyaman, dan berkelanjutan.
                </p>
                <div class="nx-visual-chips">
                    <span class="nx-chip"><i class="fas fa-shield-halved"></i> Akses Aman RT/RW</span>
                    <span class="nx-chip"><i class="fas fa-wallet"></i> Kas Transparan</span>
                    <span class="nx-chip"><i class="fas fa-file-signature"></i> E-Surat & TTE</span>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: FORM LOGIN -->
        <div class="nx-auth-form-panel">
            <div class="nx-form-head">
                <span class="nx-form-badge">
                    <i class="fas fa-leaf"></i> Portal Warga &amp; Pengurus
                </span>
                <h2 class="nx-form-title">Masuk ke Akun</h2>
                <p class="nx-form-subtitle">
                    Masukkan email terdaftar dan kata sandi Anda untuk mengakses layanan digital NexaNest.
                </p>
            </div>

            @if (session('status'))
                <div class="nx-alert-status" role="alert">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="nx-alert-error" role="alert" style="margin-bottom: 16px;">
                    <i class="fas fa-triangle-exclamation mt-0.5"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            @if ($errors->any())
                <div class="nx-alert-error" role="alert">
                    <i class="fas fa-circle-exclamation mt-1"></i>
                    <div>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="nxLoginForm">
                @csrf

                <div class="nx-field-group">
                    <label for="email" class="nx-label">Alamat Email</label>
                    <div class="nx-input-wrap">
                        <i class="far fa-envelope nx-input-icon"></i>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="nx-input"
                            placeholder="nama@email.com"
                            required
                            autofocus
                            autocomplete="username"
                        >
                    </div>
                </div>

                <div class="nx-field-group">
                    <label for="password" class="nx-label">Kata Sandi</label>
                    <div class="nx-input-wrap">
                        <i class="fas fa-lock nx-input-icon"></i>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            class="nx-input"
                            placeholder="Masukkan kata sandi Anda"
                            required
                            autocomplete="current-password"
                        >
                        <button type="button" class="nx-eye-btn" id="togglePasswordBtn" aria-label="Tampilkan atau sembunyikan kata sandi">
                            <i class="far fa-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="nx-row-meta">
                    <label class="nx-remember" for="remember">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span>Ingat Saya</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="nx-forgot-link">
                            Lupa Password?
                        </a>
                    @endif
                </div>

                <button type="submit" class="nx-submit-btn" id="nxLoginBtn">
                    <span id="nxLoginBtnText">Masuk</span>
                    <i class="fas fa-arrow-right" id="nxLoginBtnIcon"></i>
                </button>
            </form>

            @if (Route::has('register'))
                <div class="nx-auth-footer">
                    Belum punya akun warga?
                    <a href="{{ route('register') }}">Daftar sekarang</a>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const pwdInput = document.getElementById('password');
    const toggleBtn = document.getElementById('togglePasswordBtn');
    const toggleIcon = document.getElementById('togglePasswordIcon');

    if (toggleBtn && pwdInput) {
        toggleBtn.addEventListener('click', function () {
            const isHidden = pwdInput.getAttribute('type') === 'password';
            pwdInput.setAttribute('type', isHidden ? 'text' : 'password');
            toggleIcon.className = isHidden ? 'far fa-eye-slash' : 'far fa-eye';
        });
    }

    const loginForm = document.getElementById('nxLoginForm');
    const loginBtn = document.getElementById('nxLoginBtn');
    const loginBtnText = document.getElementById('nxLoginBtnText');
    const loginBtnIcon = document.getElementById('nxLoginBtnIcon');

    if (loginForm && loginBtn) {
        loginForm.addEventListener('submit', function () {
            loginBtn.disabled = true;
            loginBtnText.textContent = 'Memproses Masuk...';
            loginBtnIcon.className = 'fas fa-circle-notch fa-spin';
        });
    }
});
</script>
@endpush
