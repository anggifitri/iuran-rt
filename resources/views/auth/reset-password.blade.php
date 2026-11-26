@extends('layouts.app')

@section('title', 'Atur Ulang Kata Sandi — NexaNest Smart & Green Community')

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
    }

    .nx-auth-shell {
        width: 100%;
        max-width: 1040px;
        background: var(--nx-white);
        border-radius: 28px;
        border: 1px solid var(--nx-border);
        box-shadow: 0 22px 60px rgba(40, 89, 67, 0.09);
        display: grid;
        grid-template-columns: 1.05fr 1fr;
        overflow: hidden;
    }

    .nx-auth-visual {
        background: linear-gradient(150deg, #DCEAF5 0%, #E7F2EE 52%, #DCEBDD 100%);
        padding: 42px 38px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        border-right: 1px solid var(--nx-border);
        position: relative;
    }

    .nx-auth-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .nx-back-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.8);
        border: 1px solid rgba(40, 89, 67, 0.15);
        color: var(--nx-forest);
        font-size: 0.78rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .nx-back-btn:hover {
        background: var(--nx-white);
        color: var(--nx-forest-dark);
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
        height: 200px;
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

    .nx-visual-title {
        font-size: 1.55rem;
        font-weight: 800;
        color: var(--nx-forest);
        margin-bottom: 8px;
    }

    .nx-visual-desc {
        font-size: 0.9rem;
        color: #465A50;
        line-height: 1.6;
        margin: 0;
    }

    .nx-auth-form-panel {
        padding: 42px 42px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .nx-form-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 999px;
        background: var(--nx-powder);
        color: #2B5F8E;
        font-size: 0.74rem;
        font-weight: 700;
        margin-bottom: 10px;
        width: fit-content;
    }

    .nx-form-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--nx-text);
        margin-bottom: 6px;
    }

    .nx-form-subtitle {
        font-size: 0.86rem;
        color: var(--nx-muted);
        margin-bottom: 22px;
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
        margin-bottom: 18px;
    }

    .nx-alert-status {
        background: var(--nx-mint-soft);
        border: 1px solid var(--nx-sage);
        color: var(--nx-forest);
        border-radius: 14px;
        padding: 12px 16px;
        font-size: 0.84rem;
        font-weight: 600;
        margin-bottom: 18px;
    }

    .nx-field-group {
        margin-bottom: 16px;
    }

    .nx-label {
        display: block;
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--nx-text);
        margin-bottom: 6px;
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
        font-size: 0.9rem;
        pointer-events: none;
    }

    .nx-input {
        width: 100%;
        padding: 11px 42px 11px 42px;
        background: var(--nx-bg) !important;
        border: 1.5px solid var(--nx-border) !important;
        border-radius: 14px !important;
        font-size: 0.88rem;
        color: var(--nx-text) !important;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .nx-input:focus {
        outline: none;
        background: var(--nx-white) !important;
        border-color: var(--nx-forest) !important;
        box-shadow: 0 0 0 4px rgba(40, 89, 67, 0.1) !important;
    }

    .nx-eye-btn {
        position: absolute;
        right: 10px;
        background: transparent;
        border: none;
        color: var(--nx-muted);
        width: 32px;
        height: 32px;
        border-radius: 8px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: color 0.2s ease;
    }

    .nx-eye-btn:hover {
        color: var(--nx-forest);
    }

    .nx-submit-btn {
        width: 100%;
        padding: 13px 20px;
        border-radius: 14px;
        border: none;
        background: var(--nx-forest);
        color: var(--nx-white);
        font-size: 0.92rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        cursor: pointer;
        box-shadow: 0 10px 24px rgba(40, 89, 67, 0.2);
        transition: all 0.2s ease;
        margin-top: 6px;
    }

    .nx-submit-btn:hover {
        background: var(--nx-forest-dark);
        transform: translateY(-1px);
    }

    .nx-auth-footer {
        margin-top: 20px;
        padding-top: 16px;
        border-top: 1px solid var(--nx-border);
        text-align: center;
        font-size: 0.85rem;
        color: var(--nx-muted);
    }

    .nx-auth-footer a {
        color: var(--nx-forest);
        font-weight: 700;
        text-decoration: none;
    }

    /* DARK MODE */
    html.dark body {
        background: radial-gradient(circle at 10% 15%, #0B1120 0%, #0F172A 48%, #090D16 100%) !important;
        color: #E2E8F0 !important;
    }

    html.dark .nx-auth-shell {
        background: #131E35 !important;
        border-color: #243454 !important;
        box-shadow: 0 24px 60px rgba(0, 0, 0, 0.45) !important;
    }

    html.dark .nx-auth-visual {
        background: linear-gradient(150deg, #17243E 0%, #131D33 52%, #111A2E 100%) !important;
        border-right-color: #243454 !important;
    }

    html.dark .nx-visual-title {
        color: #F1F5F9 !important;
    }

    html.dark .nx-visual-desc {
        color: #94A3B8 !important;
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
        background: #0B1324 !important;
        border-color: #243454 !important;
        color: #F8FAFC !important;
    }

    html.dark .nx-input:focus {
        background: #111C33 !important;
        border-color: #38BDF8 !important;
        box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.15) !important;
    }

    html.dark .nx-back-btn {
        background: #17223B !important;
        border-color: #243454 !important;
        color: #94A3B8 !important;
    }

    html.dark .nx-back-btn:hover {
        background: #1E2D4A !important;
        color: #FFFFFF !important;
    }

    html.dark .nx-submit-btn {
        background: #285943 !important;
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.4) !important;
    }

    html.dark .nx-submit-btn:hover {
        background: #1E4633 !important;
    }

    html.dark .nx-form-badge {
        background: rgba(56, 189, 248, 0.15) !important;
        color: #38BDF8 !important;
    }

    html.dark .nx-auth-footer {
        border-top-color: #243454 !important;
        color: #94A3B8 !important;
    }

    html.dark .nx-auth-footer a {
        color: #38BDF8 !important;
    }

    html.dark .nx-eye-btn {
        color: #94A3B8 !important;
    }

    html.dark .nx-eye-btn:hover {
        color: #38BDF8 !important;
    }

    html.dark .nx-alert-status {
        background: rgba(40, 89, 67, 0.25) !important;
        border-color: rgba(168, 201, 181, 0.35) !important;
        color: #A8C9B5 !important;
    }

    html.dark .nx-alert-error {
        background: rgba(220, 38, 38, 0.2) !important;
        border-color: rgba(248, 113, 113, 0.3) !important;
        color: #FCA5A5 !important;
    }

    @media (max-width: 991.98px) {
        .nx-auth-shell {
            grid-template-columns: 1fr;
            max-width: 520px;
        }
        .nx-illustration-box {
            display: none;
        }
        .nx-auth-visual,
        .nx-auth-form-panel {
            padding: 28px 24px;
        }
    }
</style>
@endpush

@section('content')
<div class="nx-auth-page">
    <div class="nx-auth-shell">

        <!-- KOLOM KIRI (VISUAL BRANDING) -->
        <div class="nx-auth-visual">
            <div class="nx-auth-top">
                <a href="{{ url('/') }}" class="text-decoration-none">
                    <x-nexanest-logo size="md" />
                </a>

                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="nx-theme-toggle-btn nx-auth-theme-btn" id="nxResetThemeToggle" title="Beralih Tema (Dark/Light)">
                        <i class="far fa-moon dark-icon-moon"></i>
                        <i class="far fa-sun dark-icon-sun"></i>
                    </button>
                    <a href="{{ route('login') }}" class="nx-back-btn">
                        <i class="fas fa-arrow-left"></i> Masuk
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

            <div>
                <h2 class="nx-visual-title">Keamanan Kata Sandi</h2>
                <p class="nx-visual-desc">
                    Gunakan kombinasi minimal 8 karakter dengan paduan huruf besar, huruf kecil, dan angka untuk melindungi akses akun keluarga Anda di NexaNest.
                </p>
            </div>
        </div>

        <!-- KOLOM KANAN (FORM INPUT KATA SANDI BARU) -->
        <div class="nx-auth-form-panel">
            <span class="nx-form-badge">
                <i class="fas fa-shield-halved"></i> Kredensial Baru
            </span>
            <h2 class="nx-form-title">Atur Ulang Kata Sandi</h2>
            <p class="nx-form-subtitle">
                Silakan masukkan kata sandi baru yang aman untuk akun NexaNest Anda.
            </p>

            @if ($errors->any())
                <div class="nx-alert-error" role="alert">
                    @foreach ($errors->all() as $error)
                        <div><i class="fas fa-circle-exclamation me-2"></i> {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" id="nxResetPasswordForm">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="nx-field-group">
                    <label for="email" class="nx-label">Alamat Email Terdaftar</label>
                    <div class="nx-input-wrap">
                        <i class="far fa-envelope nx-input-icon"></i>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email', $email) }}"
                            class="nx-input"
                            placeholder="nama@gmail.com"
                            required
                            readonly
                        >
                    </div>
                </div>

                <div class="nx-field-group">
                    <label for="password" class="nx-label">Kata Sandi Baru</label>
                    <div class="nx-input-wrap">
                        <i class="fas fa-lock nx-input-icon"></i>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            class="nx-input"
                            placeholder="Minimal 8 karakter"
                            required
                            autofocus
                        >
                        <button type="button" class="nx-eye-btn" data-toggle-target="password" aria-label="Tampilkan password">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="nx-field-group">
                    <label for="password_confirmation" class="nx-label">Ulangi Kata Sandi Baru</label>
                    <div class="nx-input-wrap">
                        <i class="fas fa-shield-halved nx-input-icon"></i>
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            class="nx-input"
                            placeholder="Ulangi kata sandi baru"
                            required
                        >
                        <button type="button" class="nx-eye-btn" data-toggle-target="password_confirmation" aria-label="Tampilkan konfirmasi password">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="nx-submit-btn" id="nxSubmitResetBtn">
                    <i class="fas fa-check-circle" id="nxResetIcon"></i>
                    <span id="nxResetText">Simpan Kata Sandi Baru</span>
                </button>
            </form>

            <div class="nx-auth-footer">
                Ingat kata sandi lama Anda?
                <a href="{{ route('login') }}">Masuk ke Akun</a>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-toggle-target]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const targetId = this.getAttribute('data-toggle-target');
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');
            if (!input) return;
            const isHidden = input.getAttribute('type') === 'password';
            input.setAttribute('type', isHidden ? 'text' : 'password');
            if (icon) {
                icon.className = isHidden ? 'far fa-eye-slash' : 'far fa-eye';
            }
        });
    });

    const resetForm = document.getElementById('nxResetPasswordForm');
    const submitBtn = document.getElementById('nxSubmitResetBtn');
    const btnText = document.getElementById('nxResetText');
    const btnIcon = document.getElementById('nxResetIcon');

    if (resetForm && submitBtn) {
        resetForm.addEventListener('submit', function () {
            submitBtn.disabled = true;
            btnText.textContent = 'Menyimpan Kata Sandi...';
            btnIcon.className = 'fas fa-circle-notch fa-spin';
        });
    }
});
</script>
@endpush
