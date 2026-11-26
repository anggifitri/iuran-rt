@extends('layouts.app')

@section('title', 'Verifikasi Alamat Email — NexaNest Smart & Green Community')

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
        background: var(--nx-mint);
        color: var(--nx-forest-dark);
        font-size: 0.74rem;
        font-weight: 700;
        margin-bottom: 12px;
        width: fit-content;
    }

    .nx-email-icon-box {
        width: 64px;
        height: 64px;
        border-radius: 20px;
        background: rgba(40, 89, 67, 0.1);
        color: var(--nx-forest);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        margin-bottom: 18px;
    }

    .nx-form-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--nx-text);
        margin-bottom: 8px;
    }

    .nx-form-subtitle {
        font-size: 0.88rem;
        color: var(--nx-muted);
        line-height: 1.6;
        margin-bottom: 20px;
    }

    .nx-email-highlight {
        font-weight: 700;
        color: var(--nx-text);
        background: var(--nx-bg);
        padding: 3px 8px;
        border-radius: 8px;
        border: 1px solid var(--nx-border);
        display: inline-block;
        word-break: break-all;
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
    }

    .nx-submit-btn:hover {
        background: var(--nx-forest-dark);
        transform: translateY(-1px);
    }

    .nx-logout-btn {
        width: 100%;
        padding: 11px 20px;
        border-radius: 14px;
        border: 1.5px solid var(--nx-border);
        background: transparent;
        color: var(--nx-muted);
        font-size: 0.86rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-top: 10px;
    }

    .nx-logout-btn:hover {
        background: var(--nx-bg);
        color: var(--nx-text);
        border-color: var(--nx-muted);
    }

    .nx-auth-footer {
        margin-top: 24px;
        padding-top: 16px;
        border-top: 1px solid var(--nx-border);
        text-align: center;
        font-size: 0.82rem;
        color: var(--nx-muted);
        line-height: 1.5;
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

    html.dark .nx-form-badge {
        background: rgba(40, 89, 67, 0.4) !important;
        color: #A8C9B5 !important;
    }

    html.dark .nx-email-icon-box {
        background: rgba(56, 189, 248, 0.15) !important;
        color: #38BDF8 !important;
    }

    html.dark .nx-email-highlight {
        background: #0B1324 !important;
        border-color: #243454 !important;
        color: #38BDF8 !important;
    }

    html.dark .nx-submit-btn {
        background: #285943 !important;
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.4) !important;
    }

    html.dark .nx-submit-btn:hover {
        background: #1E4633 !important;
    }

    html.dark .nx-logout-btn {
        border-color: #243454 !important;
        color: #94A3B8 !important;
    }

    html.dark .nx-logout-btn:hover {
        background: #17223B !important;
        color: #FFFFFF !important;
        border-color: #38BDF8 !important;
    }

    html.dark .nx-auth-footer {
        border-top-color: #243454 !important;
        color: #64748B !important;
    }

    html.dark .nx-alert-status {
        background: rgba(40, 89, 67, 0.25) !important;
        border-color: rgba(168, 201, 181, 0.35) !important;
        color: #A8C9B5 !important;
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
                    <button type="button" class="nx-theme-toggle-btn nx-auth-theme-btn" id="nxVerifyThemeToggle" title="Beralih Tema (Dark/Light)">
                        <i class="far fa-moon dark-icon-moon"></i>
                        <i class="far fa-sun dark-icon-sun"></i>
                    </button>
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
                <h2 class="nx-visual-title">Verifikasi Identitas Digital</h2>
                <p class="nx-visual-desc">
                    Verifikasi alamat email memastikan bahwa laporan tagihan iuran bulanan, penerbitan surat pengantar RT/RW, dan pengumuman resmi lingkungan RW 018 sampai kepada Anda secara aman dan akurat.
                </p>
            </div>
        </div>

        <!-- KOLOM KANAN (KOTAK PEMBERITAHUAN & RESEND) -->
        <div class="nx-auth-form-panel">
            <div class="nx-email-icon-box">
                <i class="fas fa-envelope-circle-check"></i>
            </div>

            <span class="nx-form-badge">
                <i class="fas fa-shield-check"></i> Langkah Terakhir Pendaftaran
            </span>

            <h2 class="nx-form-title">Cek Kotak Masuk Email Anda</h2>

            <p class="nx-form-subtitle">
                Kami telah mengirimkan tautan verifikasi ke alamat email:
                <br>
                <span class="nx-email-highlight mt-1">{{ Auth::user()->email ?? 'email@gmail.com' }}</span>
                <br><br>
                Silakan buka kotak masuk atau folder <em>spam</em> email Anda, lalu klik tombol <strong>"Verifikasi Alamat Email"</strong> untuk mengaktifkan akun warga Anda.
            </p>

            @if (session('status') == 'verification-link-sent' || session('success'))
                <div class="nx-alert-status" role="alert">
                    <i class="fas fa-circle-check me-2"></i>
                    {{ session('success') ?? 'Tautan verifikasi email baru telah berhasil dikirimkan ke alamat email Anda!' }}
                </div>
            @endif

            <!-- Form Kirim Ulang Email Verifikasi -->
            <form method="POST" action="{{ route('verification.send') }}" id="nxResendForm">
                @csrf
                <button type="submit" class="nx-submit-btn" id="nxResendBtn">
                    <i class="fas fa-rotate-right" id="nxResendIcon"></i>
                    <span id="nxResendText">Kirim Ulang Email Verifikasi</span>
                </button>
            </form>

            <!-- Form Logout / Ganti Akun -->
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit" class="nx-logout-btn">
                    <i class="fas fa-arrow-right-from-bracket"></i>
                    <span>Keluar dari Akun (Ganti Email)</span>
                </button>
            </form>

            <div class="nx-auth-footer">
                Belum menerima email setelah beberapa menit? Pastikan Anda memeriksa folder <strong>Spam</strong> atau <strong>Promosi</strong> di akun Gmail Anda.
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const resendForm = document.getElementById('nxResendForm');
    const resendBtn = document.getElementById('nxResendBtn');
    const resendText = document.getElementById('nxResendText');
    const resendIcon = document.getElementById('nxResendIcon');

    if (resendForm && resendBtn) {
        resendForm.addEventListener('submit', function () {
            resendBtn.disabled = true;
            resendText.textContent = 'Mengirim Ulang...';
            resendIcon.className = 'fas fa-circle-notch fa-spin';
        });
    }
});
</script>
@endpush
