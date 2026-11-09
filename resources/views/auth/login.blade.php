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
        background: rgba(255, 255, 255, 0.45);
        top: -90px;
        right: -90px;
        pointer-events: none;
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
                <a href="{{ url('/') }}" class="nx-brand-badge">
                    <span class="nx-brand-icon">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none">
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

                <a href="{{ url('/') }}" class="nx-back-home">
                    <i class="fas fa-arrow-left"></i> Beranda
                </a>
            </div>

            <!-- Flat Vector Illustration: Sustainable Green Neighborhood -->
            <div class="nx-illustration-box" aria-hidden="true">
                <svg viewBox="0 0 460 230" width="100%" height="auto" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="460" height="230" rx="16" fill="#EBF5EE"/>
                    <!-- Sun & Clouds -->
                    <circle cx="385" cy="48" r="22" fill="#FBE5A2"/>
                    <path d="M70 52C70 44 77 38 85 38C91 38 96 41 98 46C100 45 103 44 106 44C112 44 117 49 117 55C117 56 117 57 116 58H71C70 56 70 54 70 52Z" fill="#FFFFFF" fill-opacity="0.85"/>
                    <path d="M270 38C270 32 275 27 282 27C287 27 291 30 293 34C295 33 297 32 299 32C304 32 308 36 308 41H271C270 40 270 39 270 38Z" fill="#FFFFFF" fill-opacity="0.8"/>

                    <!-- Rolling Pastel Hills -->
                    <path d="M0 168C95 140 190 148 290 165C355 176 415 160 460 150V230H0V168Z" fill="#C4DFC8"/>
                    <path d="M0 186C120 168 250 172 460 192V230H0V186Z" fill="#9EC6A8"/>

                    <!-- River / Eco Stream -->
                    <path d="M180 182C215 194 250 205 295 230H225C205 212 182 198 158 184L180 182Z" fill="#A9C9E8"/>

                    <!-- Eco House 1 (Left) with Solar Roof -->
                    <rect x="64" y="114" width="82" height="58" rx="4" fill="#FFFFFF"/>
                    <polygon points="56,116 105,78 154,116" fill="#285943"/>
                    <!-- Solar Panel on Roof -->
                    <polygon points="88,92 122,92 132,108 94,108" fill="#6AA6D8" stroke="#FFFFFF" stroke-width="1.5"/>
                    <rect x="94" y="138" width="22" height="34" rx="3" fill="#A8C9B5"/>
                    <rect x="74" y="126" width="14" height="14" rx="2" fill="#DCEAF5"/>
                    <rect x="122" y="126" width="14" height="14" rx="2" fill="#DCEAF5"/>

                    <!-- Eco House 2 (Center-Right) -->
                    <rect x="234" y="102" width="104" height="68" rx="5" fill="#F7F9F7"/>
                    <polygon points="224,104 286,62 348,104" fill="#3B7A5E"/>
                    <polygon points="268,78 308,78 318,96 274,96" fill="#A9C9E8" stroke="#FFFFFF" stroke-width="1.5"/>
                    <rect x="275" y="132" width="24" height="38" rx="3" fill="#285943"/>
                    <rect x="248" y="118" width="18" height="18" rx="3" fill="#A9C9E8"/>
                    <rect x="308" y="118" width="18" height="18" rx="3" fill="#A9C9E8"/>

                    <!-- Trees & Greenery -->
                    <rect x="184" y="138" width="6" height="32" rx="3" fill="#6D5446"/>
                    <circle cx="187" cy="126" r="20" fill="#4C8C6A"/>
                    <circle cx="177" cy="134" r="14" fill="#68A883"/>

                    <rect x="382" y="126" width="7" height="38" rx="3" fill="#6D5446"/>
                    <circle cx="385" cy="112" r="23" fill="#285943"/>
                    <circle cx="397" cy="122" r="16" fill="#5B9E7B"/>

                    <circle cx="40" cy="144" r="16" fill="#5B9E7B"/>
                    <rect x="38" y="154" width="5" height="20" fill="#6D5446"/>
                </svg>
            </div>

            <div class="nx-visual-copy">
                <span class="nx-visual-pill">
                    <i class="fas fa-seedling"></i> Smart &amp; Sustainable Neighborhood
                </span>
                <h2 class="nx-visual-title">Selamat Datang Kembali!</h2>
                <p class="nx-visual-desc">
                    Bersama NexaNest, wujudkan lingkungan yang lebih terorganisir, nyaman, dan berkelanjutan.
                </p>
                <div class="nx-visual-chips">
                    <span class="nx-chip"><i class="fas fa-shield-halved"></i> Akses Aman RT/RW</span>
                    <span class="nx-chip"><i class="fas fa-wallet"></i> Kas Transparan</span>
                    <span class="nx-chip"><i class="fas fa-file-signature"></i> E-Surat &amp; TTE</span>
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
