@extends('layouts.app')

@section('title', 'Dashboard — NexaNest Smart & Green Community')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Kalam:wght@700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@php
    $isSimulated = $user->isSuperAdmin() && $user->isRoleSwitched();
    $simCtx = $isSimulated ? $user->getRoleSwitchContext() : null;
    $simulatedTargetName = $simCtx['target_user_name'] ?? null;
    $dashboardDisplayName = $simulatedTargetName ?: $user->name;
    $firstName = explode(' ', trim($dashboardDisplayName))[0] ?? $dashboardDisplayName;
    $dashboardProfilePhotoUrl = $user->profile_photo_url;

    $roleLabel = $isAdminRW
        ? 'Admin RW 018'
        : ($isAdminRT
            ? 'Admin RT ' . ($user->rt_number ?? '-')
            : ($user->isBendahara() ? 'Bendahara' : 'Warga RT ' . ($user->rt_number ?? '-')));

    $notifCount = ($newFeatureCounts['surat_pending'] ?? 0) + ($newFeatureCounts['pengaduan_baru'] ?? 0);
    if ($isWarga && isset($wargaIuranLunas) && !$wargaIuranLunas) {
        $notifCount += 1;
    }
@endphp

<style>
    :root {
        --nx-forest: #285943;
        --nx-forest-soft: #3a755b;
        --nx-sage: #A8C9B5;
        --nx-pastel-blue: #A9C9E8;
        --nx-mint: #DCEBDD;
        --nx-mint-soft: #EAF4EB;
        --nx-powder: #DCEAF5;
        --nx-powder-soft: #EBF4FB;
        --nx-bg: #F7F9F7;
        --nx-white: #FFFFFF;
        --nx-text: #263449;
        --nx-muted: #718096;
        --nx-border: #E5EFE7;
        --nx-danger: #E05353;
        --nx-danger-soft: #FDECEC;
        --nx-warning-soft: #FEF3DF;
        --nx-shadow: 0 8px 26px rgba(40, 89, 67, 0.05);
        --nx-shadow-hover: 0 14px 32px rgba(40, 89, 67, 0.10);
    }

    body {
        background: var(--nx-bg) !important;
        color: var(--nx-text) !important;
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

    /* ============ APP SHELL LAYOUT ============ */
    .nx-shell {
        display: flex;
        min-height: 100vh;
        background: var(--nx-bg);
        position: relative;
    }

    /* ============ A. SIDEBAR NAVIGATION ============ */
    .nx-sidebar {
        width: 268px;
        background: var(--nx-white);
        border-right: 1px solid var(--nx-border);
        padding: 24px 18px 20px;
        display: flex;
        flex-direction: column;
        position: sticky;
        top: 0;
        height: 100vh;
        overflow-y: auto;
        transition: width 0.28s cubic-bezier(0.4, 0, 0.2, 1), transform 0.28s ease;
        z-index: 1040;
        flex-shrink: 0;
        box-shadow: 4px 0 24px rgba(40, 89, 67, 0.03);
    }

    .nx-sidebar::-webkit-scrollbar {
        width: 4px;
    }
    .nx-sidebar::-webkit-scrollbar-thumb {
        background: var(--nx-mint);
        border-radius: 10px;
    }

    .nx-shell.sidebar-collapsed .nx-sidebar {
        width: 84px;
        padding-left: 12px;
        padding-right: 12px;
    }

    .nx-shell.sidebar-collapsed .nx-brand-text,
    .nx-shell.sidebar-collapsed .nx-menu-group-label,
    .nx-shell.sidebar-collapsed .nx-nav-text,
    .nx-shell.sidebar-collapsed .nx-nav-badge,
    .nx-shell.sidebar-collapsed .nx-emergency-text {
        display: none !important;
    }

    .nx-shell.sidebar-collapsed .nx-nav-link {
        justify-content: center;
        padding: 12px;
    }

    .nx-shell.sidebar-collapsed .nx-nav-link i {
        margin-right: 0 !important;
        font-size: 1.1rem;
    }

    .nx-shell.sidebar-collapsed .nx-collapse-btn i {
        transform: rotate(180deg);
    }

    .nx-brand {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 4px 6px 20px;
        border-bottom: 1px solid var(--nx-border);
        margin-bottom: 16px;
    }

    .nx-brand-link {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        color: var(--nx-forest);
    }

    .nx-brand-logo {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        background: linear-gradient(135deg, var(--nx-mint) 0%, #c5e0c8 100%);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(40, 89, 67, 0.12);
    }

    .nx-brand-title {
        font-weight: 800;
        font-size: 1.15rem;
        color: var(--nx-forest);
        letter-spacing: -0.02em;
        line-height: 1.15;
        margin: 0;
    }

    .nx-brand-sub {
        font-size: 0.72rem;
        font-weight: 600;
        color: var(--nx-muted);
        margin: 0;
    }

    .nx-collapse-btn {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        border: 1px solid var(--nx-border);
        background: var(--nx-bg);
        color: var(--nx-forest);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 0.75rem;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .nx-collapse-btn:hover {
        background: var(--nx-mint);
    }

    .nx-menu-group-label {
        font-size: 0.68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #8FA396;
        padding: 10px 12px 6px;
        margin-top: 4px;
    }

    .nx-nav-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .nx-nav-item {
        margin-bottom: 4px;
    }

    .nx-nav-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 14px;
        border-radius: 12px;
        color: #5A6E62;
        font-size: 0.88rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
        position: relative;
        border: none;
        background: transparent;
        width: 100%;
        text-align: left;
    }

    .nx-nav-link i {
        width: 20px;
        text-align: center;
        font-size: 0.98rem;
        color: #6B8273;
        transition: color 0.2s ease;
    }

    .nx-nav-link:hover {
        background: var(--nx-mint-soft);
        color: var(--nx-forest);
    }

    .nx-nav-link:hover i {
        color: var(--nx-forest);
    }

    .nx-nav-link.active {
        background: var(--nx-mint);
        color: var(--nx-forest);
        font-weight: 700;
    }

    .nx-nav-link.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 8px;
        bottom: 8px;
        width: 4px;
        border-radius: 0 4px 4px 0;
        background: var(--nx-forest);
    }

    .nx-nav-link.active i {
        color: var(--nx-forest);
    }

    .nx-nav-badge {
        margin-left: auto;
        background: var(--nx-mint);
        color: var(--nx-forest);
        font-size: 0.68rem;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 20px;
    }

    .nx-sidebar-footer {
        margin-top: auto;
        padding-top: 16px;
        position: relative;
    }

    /* ============ TOPBAR EMERGENCY BUTTON & THEME TOGGLE ============ */
    .nx-topbar-emergency-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 15px;
        border-radius: 999px;
        background: #FFF1F2;
        color: #E11D48;
        border: 1px solid #FECDD3;
        font-size: 0.82rem;
        font-weight: 700;
        transition: all 0.2s ease;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(225, 29, 72, 0.12);
        text-decoration: none;
        white-space: nowrap;
    }
    .nx-topbar-emergency-btn:hover {
        background: #E11D48;
        color: #FFFFFF;
        border-color: #E11D48;
        box-shadow: 0 4px 14px rgba(225, 29, 72, 0.28);
        transform: translateY(-1px);
    }
    .nx-topbar-emergency-btn i {
        font-size: 0.95rem;
    }

    .nx-theme-toggle-btn {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        background: var(--nx-bg);
        border: 1px solid var(--nx-border);
        color: var(--nx-forest);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.2s ease;
        padding: 0;
        flex-shrink: 0;
    }
    .nx-theme-toggle-btn:hover {
        background: var(--nx-mint-soft);
        color: var(--nx-forest-dark);
        transform: translateY(-1px);
    }
    .dark-icon-sun {
        display: none !important;
    }
    .dark-icon-moon {
        display: inline-block !important;
    }

    .nx-logout-btn {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 14px;
        border-radius: 12px;
        color: var(--nx-danger);
        font-size: 0.86rem;
        font-weight: 600;
        text-decoration: none;
        background: transparent;
        border: none;
        width: 100%;
        transition: background 0.2s ease;
    }

    .nx-logout-btn:hover {
        background: var(--nx-danger-soft);
    }

    /* ============ B & C. MAIN AREA & TOPBAR ============ */
    .nx-main {
        flex: 1;
        min-width: 0;
        padding: 20px 28px 36px;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .nx-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .nx-search-wrap {
        position: relative;
        max-width: 430px;
        width: 100%;
    }

    .nx-search-input {
        width: 100%;
        background: var(--nx-white) !important;
        border: 1px solid var(--nx-border) !important;
        border-radius: 999px;
        padding: 10px 80px 10px 42px;
        font-size: 0.86rem;
        color: var(--nx-text) !important;
        box-shadow: var(--nx-shadow);
        transition: all 0.2s ease;
    }

    .nx-search-input:focus {
        outline: none;
        border-color: var(--nx-sage) !important;
        box-shadow: 0 0 0 3px rgba(168, 201, 181, 0.25);
    }

    .nx-search-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--nx-muted);
        font-size: 0.88rem;
    }

    .nx-search-kbd {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        background: var(--nx-bg);
        border: 1px solid var(--nx-border);
        border-radius: 6px;
        padding: 2px 7px;
        font-size: 0.7rem;
        color: var(--nx-muted);
        font-weight: 600;
    }

    .nx-search-results {
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        right: 0;
        background: var(--nx-white);
        border-radius: 16px;
        border: 1px solid var(--nx-border);
        box-shadow: var(--nx-shadow-hover);
        padding: 8px;
        z-index: 1050;
        display: none;
    }

    .nx-search-results.show {
        display: block;
    }

    .nx-search-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 12px;
        border-radius: 10px;
        text-decoration: none;
        color: var(--nx-text);
        font-size: 0.84rem;
        font-weight: 600;
    }

    .nx-search-item:hover {
        background: var(--nx-mint-soft);
        color: var(--nx-forest);
    }

    .nx-topbar-actions {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .nx-icon-btn {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: var(--nx-white);
        border: 1px solid var(--nx-border);
        color: var(--nx-text);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        position: relative;
        box-shadow: var(--nx-shadow);
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .nx-icon-btn:hover {
        background: var(--nx-mint-soft);
        color: var(--nx-forest);
    }

    .nx-notif-badge {
        position: absolute;
        top: -2px;
        right: -2px;
        background: var(--nx-danger);
        color: #fff;
        font-size: 0.65rem;
        font-weight: 700;
        min-width: 18px;
        height: 18px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 4px;
        border: 2px solid var(--nx-white);
    }

    .nx-profile-btn {
        display: flex;
        align-items: center;
        gap: 10px;
        background: var(--nx-white);
        border: 1px solid var(--nx-border);
        border-radius: 999px;
        padding: 5px 14px 5px 6px;
        text-decoration: none;
        color: var(--nx-text);
        box-shadow: var(--nx-shadow);
        transition: all 0.2s ease;
    }

    .nx-profile-btn:hover {
        border-color: var(--nx-sage);
        color: var(--nx-forest);
    }

    .nx-profile-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--nx-mint);
    }

    /* ============ 6. WELCOME BANNER / HERO SECTION ============ */
    .nx-hero {
        background-color: #E8F3EE;
        background-image: linear-gradient(90deg, rgba(232, 246, 240, 0.96) 0%, rgba(232, 246, 240, 0.75) 45%, rgba(232, 246, 240, 0.35) 75%, rgba(232, 246, 240, 0.15) 100%), url("{{ asset('images/dashboard-banner.jpg') }}");
        background-size: cover;
        background-position: right center;
        background-repeat: no-repeat;
        border-radius: 22px;
        padding: 26px 30px;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(168, 201, 181, 0.35);
        box-shadow: var(--nx-shadow);
        min-height: 172px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    html.dark .nx-hero {
        background-color: #0F172A !important;
        background-image: linear-gradient(90deg, rgba(15, 23, 42, 0.90) 0%, rgba(23, 37, 84, 0.75) 45%, rgba(30, 58, 138, 0.50) 75%, rgba(30, 58, 138, 0.35) 100%), url("{{ asset('images/dashboard-banner.jpg') }}") !important;
        border-color: rgba(30, 58, 138, 0.6) !important;
    }

    /* ============ DARK MODE: DASHBOARD WARGA IURAN & BANK SAMPAH ============ */
    html.dark .nx-card-tunggakan-warga.has-tunggakan {
        background: rgba(244, 63, 94, 0.14) !important;
        border: 1px solid rgba(244, 63, 94, 0.35) !important;
    }
    html.dark .nx-card-tunggakan-warga.has-tunggakan .nx-tunggakan-label {
        color: #FDA4AF !important;
    }
    html.dark .nx-card-tunggakan-warga.has-tunggakan .nx-tunggakan-nominal {
        color: #FB7185 !important;
    }
    html.dark .nx-card-tunggakan-warga.has-tunggakan .nx-tunggakan-subtext {
        color: #CBD5E1 !important;
    }

    html.dark .nx-badge-status-danger,
    html.dark .table .badge[style*="var(--nx-soft-red)"],
    html.dark .table .badge[style*="#FEE2E2"] {
        background: rgba(244, 63, 94, 0.16) !important;
        border: 1px solid rgba(244, 63, 94, 0.38) !important;
        color: #FDA4AF !important;
    }
    html.dark .nx-badge-status-danger *,
    html.dark .table .badge[style*="var(--nx-soft-red)"] *,
    html.dark .table .badge[style*="#FEE2E2"] * {
        color: #FDA4AF !important;
    }

    html.dark .nx-bar-bank-sampah-warga {
        background: linear-gradient(90deg, rgba(14, 165, 233, 0.14) 0%, rgba(16, 185, 129, 0.14) 100%) !important;
        border: 1px solid rgba(56, 189, 248, 0.28) !important;
    }
    html.dark .nx-bar-bank-sampah-warga .nx-bar-sampah-title {
        color: #6EE7B7 !important;
    }
    html.dark .nx-bar-bank-sampah-warga .nx-bar-sampah-desc {
        color: #94A3B8 !important;
    }
    html.dark .nx-bar-bank-sampah-warga .nx-bar-sampah-desc strong {
        color: #F1F5F9 !important;
    }
    html.dark .nx-bar-bank-sampah-warga .nx-bar-sampah-icon {
        background: rgba(16, 185, 129, 0.25) !important;
        color: #34D399 !important;
        border: 1px solid rgba(52, 211, 153, 0.3) !important;
    }
    html.dark .nx-btn-bank-sampah-warga {
        background: rgba(16, 185, 129, 0.18) !important;
        border: 1px solid rgba(52, 211, 153, 0.40) !important;
        color: #6EE7B7 !important;
    }
    html.dark .nx-btn-bank-sampah-warga * {
        color: #6EE7B7 !important;
    }
    html.dark .nx-btn-bank-sampah-warga:hover {
        background: #10B981 !important;
        border-color: #10B981 !important;
        color: #FFFFFF !important;
    }
    html.dark .nx-btn-bank-sampah-warga:hover * {
        color: #FFFFFF !important;
    }

    .nx-hero-content {
        position: relative;
        z-index: 3;
        max-width: 540px;
    }

    .nx-hero-title {
        font-size: 1.65rem;
        font-weight: 800;
        color: var(--nx-text);
        margin-bottom: 6px;
        letter-spacing: -0.02em;
    }

    .nx-hero-subtitle {
        font-size: 0.92rem;
        color: #4A5D52;
        font-weight: 500;
        margin-bottom: 14px;
    }

    .nx-date-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #6F9E7B;
        color: #FFFFFF;
        padding: 7px 16px;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(40, 89, 67, 0.18);
    }

    .nx-hero-motto {
        position: absolute;
        top: 20px;
        right: 32px;
        z-index: 3;
        transform: rotate(-4deg);
        text-align: center;
        pointer-events: none;
    }

    .nx-hero-motto .nx-kalam-line {
        font-family: 'Kalam', cursive !important;
        font-size: 1.35rem;
        font-weight: 700;
        color: #1B4332;
        line-height: 1.15;
        letter-spacing: -0.01em;
        text-shadow: 0 2px 10px rgba(255, 255, 255, 0.95);
    }

    .nx-hero-motto .nx-kalam-curve {
        display: block;
        margin: 2px auto 0;
        filter: drop-shadow(0 1px 2px rgba(255, 255, 255, 0.9));
    }

    /* ============ 7. STATISTIK UTAMA (4 PASTEL CARDS) ============ */
    .nx-stat-card {
        border-radius: 18px;
        padding: 18px 20px;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.8);
        box-shadow: var(--nx-shadow);
        transition: transform 0.24s ease, box-shadow 0.24s ease;
        height: 100%;
        display: flex;
        align-items: flex-start;
        gap: 14px;
        text-decoration: none;
    }

    .nx-stat-card::before,
    .nx-stat-card::after {
        display: none !important;
        content: none !important;
    }

    .nx-stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--nx-shadow-hover);
    }

    .nx-stat-green {
        background: linear-gradient(135deg, #E3F1E6 0%, #D5E9D9 100%);
        color: var(--nx-text);
    }

    .nx-stat-blue {
        background: linear-gradient(135deg, #E2EEF9 0%, #D4E6F6 100%);
        color: var(--nx-text);
    }

    .nx-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .nx-stat-green .nx-stat-icon {
        background: rgba(40, 89, 67, 0.14);
        color: var(--nx-forest);
    }

    .nx-stat-blue .nx-stat-icon {
        background: rgba(43, 95, 142, 0.14);
        color: #2B5F8E;
    }

    .nx-stat-info {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
    }

    .nx-stat-value {
        font-size: 1.35rem;
        font-weight: 800;
        color: var(--nx-text);
        line-height: 1.2;
        margin-bottom: 2px;
        word-break: break-word;
    }

    .nx-stat-label {
        font-size: 0.78rem;
        font-weight: 600;
        color: #54667A;
        margin: 0;
        line-height: 1.3;
        white-space: normal !important;
        overflow: visible !important;
        text-overflow: clip !important;
        word-break: normal !important;
    }

    .nx-stat-pill {
        display: inline-flex;
        align-items: center;
        align-self: flex-start;
        margin-top: 8px;
        background: rgba(255, 255, 255, 0.85);
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 700;
        color: var(--nx-forest);
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
        white-space: nowrap;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* ============ WHITE SURFACE CARDS ============ */
    .nx-card {
        background: var(--nx-white);
        border: 1px solid var(--nx-border);
        border-radius: 20px;
        padding: 22px 24px;
        box-shadow: var(--nx-shadow);
        height: 100%;
    }

    .nx-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 18px;
    }

    .nx-card-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.02rem;
        font-weight: 800;
        color: var(--nx-text);
        margin: 0;
    }

    .nx-card-title-icon {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        background: var(--nx-mint);
        color: var(--nx-forest);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.88rem;
    }

    .nx-link-more {
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--nx-forest);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: gap 0.2s ease;
    }

    .nx-link-more:hover {
        gap: 9px;
        color: var(--nx-forest-soft);
    }

    /* ============ 9. PENGUMUMAN LIST ============ */
    .nx-announce-item {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 13px 14px;
        border-radius: 15px;
        background: var(--nx-bg);
        border: 1px solid var(--nx-border);
        margin-bottom: 11px;
        transition: all 0.2s ease;
        text-decoration: none;
        color: var(--nx-text);
    }

    .nx-announce-item:last-child {
        margin-bottom: 0;
    }

    .nx-announce-item:hover {
        background: var(--nx-mint-soft);
        transform: translateX(3px);
    }

    .nx-announce-icon {
        width: 42px;
        height: 42px;
        border-radius: 13px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.05rem;
    }

    .nx-badge-kegiatan {
        background: var(--nx-mint);
        color: var(--nx-forest);
    }

    .nx-badge-info {
        background: var(--nx-powder);
        color: #2B5F8E;
    }

    .nx-badge-kesehatan {
        background: #FDE8E8;
        color: #B84343;
    }

    .nx-badge-umkm {
        background: var(--nx-warning-soft);
        color: #9C6523;
    }

    .nx-category-pill {
        font-size: 0.68rem;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 999px;
        white-space: nowrap;
    }

    /* ============ 10. KEGIATAN MENDATANG ============ */
    .nx-event-box {
        background: linear-gradient(135deg, #F2F8F3 0%, #E6F2E8 100%);
        border: 1px solid #D2E6D6;
        border-radius: 16px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 16px;
        position: relative;
        overflow: hidden;
    }

    .nx-date-badge {
        width: 68px;
        height: 74px;
        border-radius: 14px;
        background: var(--nx-mint);
        border: 1px solid #C2DEC7;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: var(--nx-forest);
    }

    .nx-date-badge .day {
        font-size: 1.45rem;
        font-weight: 800;
        line-height: 1.05;
    }

    .nx-date-badge .month {
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: capitalize;
    }

    /* ============ 11. STATUS LAYANAN SMALL CARDS ============ */
    .nx-service-card {
        border-radius: 16px;
        padding: 16px 14px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
        text-decoration: none;
        color: var(--nx-text);
        border: 1px solid transparent;
        transition: all 0.22s ease;
    }

    .nx-service-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--nx-shadow);
        color: var(--nx-forest);
    }

    .nx-service-card.mint {
        background: var(--nx-mint-soft);
        border-color: #D2E8D5;
    }

    .nx-service-card.blue {
        background: var(--nx-powder-soft);
        border-color: #D2E5F4;
    }

    .nx-service-icon {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
        font-size: 1rem;
    }

    .nx-service-card.mint .nx-service-icon {
        background: var(--nx-mint);
        color: var(--nx-forest);
    }

    .nx-service-card.blue .nx-service-icon {
        background: var(--nx-powder);
        color: #2B5F8E;
    }

    /* ============ MOBILE DRAWER ============ */
    .nx-mobile-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(38, 52, 73, 0.35);
        backdrop-filter: blur(2px);
        z-index: 1035;
    }

    @media (max-width: 991.98px) {
        .nx-sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            transform: translateX(-100%);
            width: 270px !important;
        }
        .nx-shell.mobile-open .nx-sidebar {
            transform: translateX(0);
        }
        .nx-shell.mobile-open .nx-mobile-backdrop {
            display: block;
        }
        .nx-main {
            padding: 16px;
        }
        .nx-hero {
            flex-direction: column;
            align-items: flex-start;
            gap: 14px;
            padding: 20px;
        }
        .nx-hero-art {
            opacity: 0.25;
            width: 100%;
        }
        .nx-hero-motto {
            text-align: left;
        }
    }

    /* ============ MOBILE-SPECIFIC (< 640px) OPTIMIZATIONS ============ */
    @media (max-width: 639.98px) {
        .nx-main {
            padding: 12px !important;
        }
        .nx-topbar {
            padding: 8px 12px !important;
            margin-bottom: 16px !important;
            border-radius: 14px !important;
        }
        .nx-topbar-actions {
            gap: 8px !important;
        }
        .nx-icon-btn {
            width: 36px !important;
            height: 36px !important;
            font-size: 0.9rem !important;
        }
        .nx-theme-toggle-btn {
            width: 36px !important;
            height: 36px !important;
            border-radius: 10px !important;
            font-size: 0.85rem !important;
        }
        .nx-topbar-emergency-btn {
            padding: 6px 10px !important;
            font-size: 0.78rem !important;
            border-radius: 10px !important;
        }
        .nx-profile-avatar {
            width: 32px !important;
            height: 32px !important;
        }
        .nx-profile-btn {
            padding: 3px !important;
            border-radius: 50% !important;
        }

        /* Banner Hero Compact on Mobile */
        .nx-hero {
            padding: 16px !important;
            border-radius: 18px !important;
            min-height: auto !important;
            gap: 10px !important;
            margin-bottom: 16px !important;
        }
        .nx-hero-title {
            font-size: 1.18rem !important;
            margin-bottom: 4px !important;
        }
        .nx-hero-subtitle {
            font-size: 0.78rem !important;
            line-height: 1.4 !important;
            margin-bottom: 10px !important;
        }
        .nx-date-pill {
            padding: 4px 10px !important;
            font-size: 0.70rem !important;
        }
        .nx-hero .badge {
            font-size: 0.68rem !important;
            padding: 4px 8px !important;
        }

        /* Stat Cards: Compact 2-column Grid */
        .nx-stat-grid {
            margin-bottom: 16px !important;
        }
        .nx-stat-card {
            padding: 12px 10px !important;
            gap: 8px !important;
            border-radius: 14px !important;
            flex-direction: column !important;
            align-items: flex-start !important;
        }
        .nx-stat-icon {
            width: 32px !important;
            height: 32px !important;
            border-radius: 10px !important;
            font-size: 0.95rem !important;
            margin-top: 0 !important;
        }
        .nx-stat-value {
            font-size: 1.10rem !important;
            line-height: 1.15 !important;
            margin-bottom: 2px !important;
            word-break: break-all !important;
        }
        .nx-stat-label {
            font-size: 0.68rem !important;
            line-height: 1.25 !important;
        }
        .nx-stat-pill {
            margin-top: 6px !important;
            padding: 2px 7px !important;
            font-size: 0.64rem !important;
        }

        /* Cards & Tables on Mobile */
        .nx-card {
            padding: 14px !important;
            border-radius: 16px !important;
        }
        .nx-card-title {
            font-size: 0.90rem !important;
        }
        .nx-card-title-icon {
            width: 28px !important;
            height: 28px !important;
            font-size: 0.85rem !important;
        }
        .nx-link-more {
            font-size: 0.72rem !important;
        }
        .nx-rt-summary-table th,
        .nx-rt-summary-table td {
            font-size: 0.75rem !important;
            padding: 8px 6px !important;
        }
        .nx-rt-summary-card {
            padding: 12px !important;
            border-radius: 14px !important;
        }
        .nx-rt-summary-card p {
            font-size: 0.74rem !important;
            line-height: 1.4 !important;
            margin-bottom: 8px !important;
        }
    }
</style>

<div class="nx-shell" id="nxShell">
    <div class="nx-mobile-backdrop" id="nxBackdrop"></div>

    <!-- ==================== A. SIDEBAR NAVIGATION ==================== -->
    <aside class="nx-sidebar" aria-label="Sidebar Navigasi NexaNest">
        <div class="nx-brand">
            <a href="{{ route('dashboard') }}" class="nx-brand-link">
                <x-nexanest-logo size="md" />
            </a>
            <button type="button" class="nx-collapse-btn d-none d-lg-inline-flex" id="nxCollapseBtn" title="Perkecil / Perlebar Sidebar">
                <i class="fas fa-angles-left"></i>
            </button>
        </div>

        @if($user && $user->isSuperAdmin())
        <div class="p-2 mb-3 rounded-3 text-white" style="background: linear-gradient(135deg, #1E4633 0%, #285943 60%, #785310 100%); border: 1.5px solid #D4AF37;">
            <div class="d-flex align-items-center justify-content-between mb-1">
                <span class="badge" style="background: #FFE082; color: #452600; font-size: 0.68rem; font-weight: 800;">
                    <i class="fas fa-crown"></i> SUPER ADMIN
                </span>
                <a href="{{ route('super_admin.dashboard') }}" class="text-white-50 text-decoration-none small" style="font-size: 0.70rem;">
                    HQ &rarr;
                </a>
            </div>
            <button type="button" class="btn btn-sm w-100 py-1 px-2 text-dark fw-bold d-flex align-items-center justify-content-between shadow-sm" style="background: linear-gradient(135deg, #FFE082 0%, #FFB300 100%); font-size: 0.74rem; border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#modalGlobalRoleSwitcher">
                <span><i class="fas fa-arrow-right-arrow-left me-1"></i> Role Switcher</span>
                <i class="fas fa-chevron-right" style="font-size: 0.65rem;"></i>
            </button>
        </div>
        @endif

        <!-- KELOMPOK 1: DASHBOARD -->
        <div class="nx-menu-group-label">Dashboard</div>
        <ul class="nx-nav-list">
            <li class="nx-nav-item">
                <a href="{{ route('dashboard') }}" class="nx-nav-link active">
                    <i class="fas fa-border-all"></i>
                    <span class="nx-nav-text">Dashboard</span>
                </a>
            </li>
            @if($isAdminRW)
            <li class="nx-nav-item">
                <a href="{{ route('monitoring.index') }}" class="nx-nav-link">
                    <i class="fas fa-tower-observation"></i>
                    <span class="nx-nav-text">Monitoring Wilayah</span>
                </a>
            </li>
            @endif
        </ul>

        <!-- KELOMPOK 2: ADMINISTRASI WARGA -->
        @if(!$isWarga)
        <div class="nx-menu-group-label">Administrasi Warga</div>
        <ul class="nx-nav-list">
            @if($isAdminRW)
            <li class="nx-nav-item">
                <a href="{{ route('warga.index') }}" class="nx-nav-link">
                    <i class="fas fa-users-gear"></i>
                    <span class="nx-nav-text">Database Warga</span>
                </a>
            </li>
            @else
            <li class="nx-nav-item">
                <a href="{{ route('warga.index') }}" class="nx-nav-link">
                    <i class="fas fa-users"></i>
                    <span class="nx-nav-text">Data Warga</span>
                </a>
            </li>
            <li class="nx-nav-item">
                <a href="{{ route('verifikasi.index') }}" class="nx-nav-link">
                    <i class="fas fa-user-check"></i>
                    <span class="nx-nav-text">Verifikasi Warga</span>
                </a>
            </li>
            @endif
            <li class="nx-nav-item">
                <a href="{{ route('mutasi.index') }}" class="nx-nav-link">
                    <i class="fas fa-arrows-split-up-and-left"></i>
                    <span class="nx-nav-text">Mutasi Warga</span>
                </a>
            </li>
        </ul>
        @endif

        <!-- KELOMPOK 3: KEUANGAN DAN PELAYANAN -->
        <div class="nx-menu-group-label">Keuangan &amp; Pelayanan</div>
        <ul class="nx-nav-list">
            @if(!$isWarga)
            <li class="nx-nav-item">
                <a href="{{ route('pembayaran.index') }}" class="nx-nav-link">
                    <i class="fas fa-wallet"></i>
                    <span class="nx-nav-text">Transaksi Kas</span>
                </a>
            </li>
            <li class="nx-nav-item">
                <a href="{{ route('laporan.index') }}" class="nx-nav-link">
                    <i class="fas fa-chart-column"></i>
                    <span class="nx-nav-text">Laporan Bulanan</span>
                </a>
            </li>
            @endif
            <li class="nx-nav-item">
                <a href="{{ route('laporan.iuran') }}" class="nx-nav-link">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span class="nx-nav-text">{{ $isWarga ? 'Iuran Warga Saya' : 'Iuran Warga' }}</span>
                </a>
            </li>
            @if(!$isWarga)
            <li class="nx-nav-item">
                <a href="{{ route('laporan_rt.index') }}" class="nx-nav-link">
                    <i class="fas fa-clipboard-check"></i>
                    <span class="nx-nav-text">{{ $isAdminRW ? 'Pemeriksaan Laporan RT' : 'Laporan Resmi RT' }}</span>
                </a>
            </li>
            @endif
            <li class="nx-nav-item">
                <a href="{{ route('surat.index') }}" class="nx-nav-link">
                    <i class="fas fa-file-signature"></i>
                    <span class="nx-nav-text">Penerbitan Surat</span>
                    @if(($newFeatureCounts['surat_pending'] ?? 0) > 0)
                        <span class="nx-nav-badge">{{ $newFeatureCounts['surat_pending'] }}</span>
                    @endif
                </a>
            </li>
            <li class="nx-nav-item">
                <a href="{{ route('pengaduan.index') }}" class="nx-nav-link">
                    <i class="{{ $isWarga ? 'fas fa-comment-dots' : 'fas fa-boxes-stacked' }}"></i>
                    <span class="nx-nav-text">{{ $isWarga ? 'Pengaduan Warga' : 'Manajemen Aset & Pengaduan' }}</span>
                    @if(($newFeatureCounts['pengaduan_baru'] ?? 0) > 0)
                        <span class="nx-nav-badge">{{ $newFeatureCounts['pengaduan_baru'] }}</span>
                    @endif
                </a>
            </li>
        </ul>

        <!-- KELOMPOK 4: LINGKUNGAN DAN KEAMANAN -->
        <div class="nx-menu-group-label">Lingkungan &amp; Keamanan</div>
        <ul class="nx-nav-list">
            @if(!$isWarga)
            <li class="nx-nav-item">
                <a href="{{ route('lingkungan.index') }}" class="nx-nav-link">
                    <i class="fas fa-leaf"></i>
                    <span class="nx-nav-text">Laporan Lingkungan</span>
                </a>
            </li>
            @endif
            <li class="nx-nav-item">
                <a href="{{ route('bank_sampah.index') }}" class="nx-nav-link">
                    <i class="fas fa-recycle"></i>
                    <span class="nx-nav-text">Edukasi &amp; Bank Sampah</span>
                </a>
            </li>
            <li class="nx-nav-item">
                <a href="{{ route('ronda.index') }}" class="nx-nav-link">
                    <i class="fas fa-shield-halved"></i>
                    <span class="nx-nav-text">Ronda &amp; Keamanan</span>
                </a>
            </li>
        </ul>

        <!-- KELOMPOK 5: FASILITAS DAN KEGIATAN -->
        <div class="nx-menu-group-label">Fasilitas &amp; Kegiatan</div>
        <ul class="nx-nav-list">
            <li class="nx-nav-item">
                <a href="{{ route('fasilitas.index') }}" class="nx-nav-link">
                    <i class="fas fa-building-circle-check"></i>
                    <span class="nx-nav-text">Peminjaman Fasilitas</span>
                </a>
            </li>
            <li class="nx-nav-item">
                <a href="{{ route('agenda.index') }}" class="nx-nav-link">
                    <i class="fas fa-handshake"></i>
                    <span class="nx-nav-text">Agenda &amp; Rapat</span>
                </a>
            </li>
            <li class="nx-nav-item">
                <a href="{{ route('kalender.index') }}" class="nx-nav-link">
                    <i class="fas fa-calendar-check"></i>
                    <span class="nx-nav-text">Kalender Kegiatan</span>
                </a>
            </li>
            <li class="nx-nav-item">
                <a href="{{ route('umkm.index') }}" class="nx-nav-link">
                    <i class="fas fa-store"></i>
                    <span class="nx-nav-text">Direktori UMKM</span>
                </a>
            </li>
            <li class="nx-nav-item">
                <a href="{{ route('posyandu.index') }}" class="nx-nav-link">
                    <i class="fas fa-heart"></i>
                    <span class="nx-nav-text">Info Posyandu</span>
                </a>
            </li>
        </ul>

        <!-- KELOMPOK 6: INFORMASI -->
        <div class="nx-menu-group-label">Informasi</div>
        <ul class="nx-nav-list">
            <li class="nx-nav-item">
                <a href="{{ route('pengumuman.index') }}" class="nx-nav-link">
                    <i class="fas fa-bullhorn"></i>
                    <span class="nx-nav-text">Pusat Pengumuman</span>
                </a>
            </li>
        </ul>

        <!-- KELOMPOK 7: PENGELOLAAN SISTEM -->
        <div class="nx-menu-group-label">Pengelolaan Sistem</div>
        <ul class="nx-nav-list">
            @if(!$isWarga)
            <li class="nx-nav-item">
                <a href="{{ route('audit.index') }}" class="nx-nav-link">
                    <i class="fas fa-clock-rotate-left"></i>
                    <span class="nx-nav-text">{{ $isAdminRW ? 'Audit & Aktivitas' : 'Audit & Aktivitas RT' }}</span>
                </a>
            </li>
            @endif
            <li class="nx-nav-item">
                <a href="{{ route('profile.edit') }}" class="nx-nav-link">
                    <i class="fas fa-user-gear"></i>
                    <span class="nx-nav-text">Profil Saya</span>
                </a>
            </li>
        </ul>

        <!-- FOOTER SIDEBAR: KELUAR -->
        <div class="nx-sidebar-footer">
            <a href="{{ route('logout') }}" class="nx-logout-btn"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-arrow-right-from-bracket"></i>
                <span class="nx-nav-text">Keluar</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </aside>

    <!-- ==================== B & C. MAIN CONTENT AREA ==================== -->
    <main class="nx-main">

        <!-- B. TOP NAVIGATION BAR -->
        <header class="nx-topbar">
            <div class="d-flex align-items-center gap-1.5 gap-sm-2 flex-grow-1 overflow-hidden">
                <button type="button" class="nx-icon-btn d-lg-none" id="nxMobileMenuBtn" aria-label="Buka Menu">
                    <i class="fas fa-bars"></i>
                </button>

                <!-- Search Wrap for Desktop / Tablet -->
                <div class="nx-search-wrap d-none d-sm-block">
                    <i class="fas fa-search nx-search-icon"></i>
                    <input type="text" id="nxQuickSearch" class="nx-search-input"
                           placeholder="Cari fitur atau informasi... (Ctrl + K)" autocomplete="off">
                    <span class="nx-search-kbd d-none d-md-inline-block">Ctrl + K</span>

                    <!-- Quick Search Dropdown -->
                    <div class="nx-search-results" id="nxSearchResults">
                        <a href="{{ route('surat.index') }}" class="nx-search-item" data-keywords="surat pengantar domisili sktm tte">
                            <i class="fas fa-file-signature text-success"></i> Penerbitan &amp; Pengajuan Surat
                        </a>
                        <a href="{{ route('pengaduan.index') }}" class="nx-search-item" data-keywords="pengaduan laporan keluhan kerusakan qr lingkungan">
                            <i class="fas fa-comment-dots text-primary"></i> Pengaduan Warga &amp; Lingkungan
                        </a>
                        <a href="{{ route('laporan.iuran') }}" class="nx-search-item" data-keywords="iuran tagihan bayar kas lunas">
                            <i class="fas fa-file-invoice-dollar text-success"></i> {{ $isWarga ? 'Riwayat Iuran Saya' : 'Laporan Iuran Warga' }}
                        </a>
                        <a href="{{ route('posyandu.index') }}" class="nx-search-item" data-keywords="posyandu kesehatan balita ibu hamil jadwal">
                            <i class="fas fa-heart text-danger"></i> Informasi &amp; Jadwal Posyandu
                        </a>
                        <a href="{{ route('umkm.index') }}" class="nx-search-item" data-keywords="umkm usaha toko kuliner belanja">
                            <i class="fas fa-store text-warning"></i> Direktori UMKM Warga
                        </a>
                        @if(!$isWarga)
                        <a href="{{ route('warga.index') }}" class="nx-search-item" data-keywords="warga penduduk kk nik e-ktp rfid">
                            <i class="fas fa-users text-success"></i> Data Warga &amp; Rekam E-KTP
                        </a>
                        <a href="{{ route('pembayaran.index') }}" class="nx-search-item" data-keywords="transaksi kas pemasukan pengeluaran">
                            <i class="fas fa-wallet text-primary"></i> Transaksi Kas RT/RW
                        </a>
                        <a href="{{ route('laporan.index') }}" class="nx-search-item" data-keywords="laporan bulanan keuangan cetak">
                            <i class="fas fa-chart-column text-success"></i> Laporan Keuangan Bulanan
                        </a>
                        @endif
                        <a href="{{ route('profile.edit') }}" class="nx-search-item" data-keywords="profil akun password foto">
                            <i class="fas fa-user-cog text-secondary"></i> Pengaturan Profil Saya
                        </a>
                    </div>
                </div>

                <!-- Mobile Quick Search Trigger Icon Button -->
                <button type="button" class="nx-icon-btn d-sm-none" id="nxMobileSearchToggleBtn" aria-label="Buka Pencarian" data-bs-toggle="collapse" data-bs-target="#nxMobileSearchCollapse">
                    <i class="fas fa-search"></i>
                </button>
            </div>

            <div class="nx-topbar-actions">
                <!-- 1. Tombol Darurat -->
                <button type="button" class="nx-topbar-emergency-btn" data-bs-toggle="modal" data-bs-target="#modalDarurat" title="Tombol Darurat RW 018">
                    <i class="fas fa-triangle-exclamation"></i>
                    <span class="d-none d-sm-inline ms-1">Darurat</span>
                </button>

                <!-- 2. Toggle Mode Dark / Light -->
                <button type="button" class="nx-theme-toggle-btn" id="nxThemeToggleBtnDash" title="Beralih Tema (Dark/Light)">
                    <i class="far fa-moon dark-icon-moon"></i>
                    <i class="far fa-sun dark-icon-sun"></i>
                </button>

                <!-- 3. Notification Bell Dropdown -->
                <div class="dropdown">
                    <button class="nx-icon-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Notifikasi">
                        <i class="far fa-bell"></i>
                        @if($notifCount > 0)
                            <span class="nx-notif-badge">{{ $notifCount }}</span>
                        @endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-end p-3 border-0 shadow-lg" style="width: 310px; border-radius: 18px; background: #fff;">
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <span class="fw-bold" style="color: var(--nx-text); font-size: 0.9rem;">Notifikasi Lingkungan</span>
                            <span class="badge rounded-pill" style="background: var(--nx-mint); color: var(--nx-forest);">{{ $notifCount }} Baru</span>
                        </div>
                        <div class="d-flex flex-column gap-2">
                            @if(($newFeatureCounts['surat_pending'] ?? 0) > 0)
                                <a href="{{ route('surat.index', ['filter' => 'pending']) }}" class="text-decoration-none p-2 rounded-3" style="background: var(--nx-mint-soft); color: var(--nx-text); font-size: 0.82rem;">
                                    <i class="fas fa-file-signature text-success me-2"></i>
                                    <strong>{{ $newFeatureCounts['surat_pending'] }} Surat</strong> sedang diproses / menunggu persetujuan.
                                </a>
                            @endif
                            @if(($newFeatureCounts['pengaduan_baru'] ?? 0) > 0)
                                <a href="{{ route('pengaduan.index') }}" class="text-decoration-none p-2 rounded-3" style="background: var(--nx-powder-soft); color: var(--nx-text); font-size: 0.82rem;">
                                    <i class="fas fa-exclamation-circle text-primary me-2"></i>
                                    <strong>{{ $newFeatureCounts['pengaduan_baru'] }} Laporan Warga</strong> berstatus menunggu tindak lanjut.
                                </a>
                            @endif
                            @if($isWarga && !$wargaIuranLunas)
                                <a href="{{ route('laporan.iuran') }}" class="text-decoration-none p-2 rounded-3" style="background: var(--nx-warning-soft); color: var(--nx-text); font-size: 0.82rem;">
                                    <i class="fas fa-wallet text-warning me-2"></i>
                                    Iuran bulan {{ \Carbon\Carbon::now()->translatedFormat('F Y') }} belum tercatat lunas.
                                </a>
                            @endif
                            @if($notifCount === 0)
                                <div class="text-center py-3 text-muted small">
                                    <i class="fas fa-check-circle text-success mb-1 d-block fa-lg"></i>
                                    Semua layanan &amp; administrasi sudah tertangani.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- User Profile Dropdown -->
                <div class="dropdown">
                    <a href="#" class="nx-profile-btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="{{ $dashboardProfilePhotoUrl }}" alt="{{ $dashboardDisplayName }}" class="nx-profile-avatar">
                        <div class="d-none d-sm-block text-start me-1">
                            <div class="fw-bold" style="font-size: 0.85rem; line-height: 1.15; color: var(--nx-text);">
                                Hello, {{ $firstName }}!
                            </div>
                            <small style="font-size: 0.72rem; color: var(--nx-muted); font-weight: 600;">
                                {{ $roleLabel }}
                            </small>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2" style="border-radius: 16px; background: #fff; min-width: 210px;">
                        <li class="px-3 py-2 border-bottom mb-1">
                            <div class="fw-bold small" style="color: var(--nx-text);">{{ $dashboardDisplayName }}</div>
                            @if($isSimulated)
                                <div class="badge bg-warning-subtle text-dark border border-warning-subtle mt-1" style="font-size: 0.68rem; color: #0F172A !important;">
                                    👑 Sesi Super Admin: {{ $user->name }}
                                </div>
                            @else
                                <div class="text-muted" style="font-size: 0.73rem;">{{ $user->email }}</div>
                            @endif
                        </li>
                        @if($user && $user->isSuperAdmin())
                        <li>
                            <a class="dropdown-item rounded-3 py-2 small fw-bold text-dark" href="{{ route('super_admin.dashboard') }}" style="background: #FEF3C7;">
                                <i class="fas fa-crown me-2 text-warning"></i>Platform Control Center
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item rounded-3 py-2 small fw-semibold" href="#" data-bs-toggle="modal" data-bs-target="#modalGlobalRoleSwitcher">
                                <i class="fas fa-arrow-right-arrow-left me-2 text-success"></i>Buka Role Switcher
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        @endif
                        <li>
                            <a class="dropdown-item rounded-3 py-2 small fw-semibold" href="{{ route('profile.edit') }}" style="color: var(--nx-text);">
                                <i class="fas fa-user-pen me-2 text-success"></i>Pengaturan Profil
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item rounded-3 py-2 small fw-semibold" href="{{ route('password.request') }}" style="color: var(--nx-text);">
                                <i class="fas fa-key me-2 text-primary"></i>Ganti Password
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item rounded-3 py-2 small fw-semibold text-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i>Keluar
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Mobile Search Dropdown Bar (Collapsible on Mobile) -->
        <div class="collapse mb-3 d-sm-none" id="nxMobileSearchCollapse">
            <div class="nx-card p-2.5">
                <div class="position-relative">
                    <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                    <input type="text" id="nxMobileQuickSearch" class="form-control ps-5 rounded-pill border" placeholder="Cari layanan, surat, iuran..." style="font-size: 0.85rem;">
                </div>
            </div>
        </div>

        <!-- 6. WELCOME BANNER / HERO SECTION -->
        <section class="nx-hero">
            <div class="nx-hero-content">
                <h2 class="nx-hero-title">👋 Selamat Datang, {{ $dashboardDisplayName }}!</h2>
                <p class="nx-hero-subtitle">
                    @if($isAdminRW)
                        Bersama Membangun Lingkungan RW 018 yang Transparan, Asri, dan Berkelanjutan 💚
                    @elseif($isAdminRT)
                        Bersama Membangun Lingkungan RT {{ $user->rt_number }} yang Tertib, Rukun, dan Hijau 💚
                    @else
                        Semoga harimu menyenangkan di lingkungan kita • Bersama Membangun Lingkungan yang Lebih Baik 💚
                    @endif
                </p>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="nx-date-pill">
                        <i class="far fa-calendar-alt"></i>
                        {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}
                    </span>
                    @if($isAdminRW)
                        <span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.85); color: var(--nx-forest); font-weight: 700;">
                            <i class="fas fa-shield-halved me-1"></i>{{ $totalAdminRW }} Admin RW • {{ $totalAdminRT }} Admin RT Aktif
                        </span>
                    @elseif($isAdminRT)
                        <span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.85); color: var(--nx-forest); font-weight: 700;">
                            <i class="fas fa-house-user me-1"></i>Wilayah RT {{ $user->rt_number }} / RW 018
                        </span>
                    @elseif($wargaRecord)
                        <span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.85); color: var(--nx-forest); font-weight: 700;">
                            <i class="fas fa-location-dot me-1"></i>{{ preg_match('/^blok\s+/i', trim($wargaRecord->blok_rumah ?? '')) ? trim($wargaRecord->blok_rumah) : 'Blok ' . trim($wargaRecord->blok_rumah ?? '-') }} • RT {{ $wargaRecord->rt_number }}
                        </span>
                        @if($user->isRoleSwitched())
                            <span class="badge rounded-pill px-3 py-2" style="background: rgba(251, 191, 36, 0.2); color: #B45309; border: 1px solid rgba(245, 158, 11, 0.4); font-weight: 700;">
                                <i class="fas fa-eye me-1"></i>Simulasi Warga: {{ $wargaRecord->nama }} (RT {{ $wargaRecord->rt_number }})
                            </span>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Floating Handwritten Motto Callout -->
            <div class="nx-hero-motto d-none d-md-block">
                <div class="nx-kalam-line">Bersama Jaga</div>
                <div class="nx-kalam-line">Lingkungan Kita</div>
                <svg class="nx-kalam-curve" width="120" height="12" viewBox="0 0 120 12" fill="none">
                    <path d="M4 9C38 3 82 3 116 8" stroke="#1B4332" stroke-width="2.4" stroke-linecap="round"/>
                </svg>
            </div>
        </section>

        <!-- 7. EMPAT KARTU STATISTIK UTAMA -->
        <section aria-label="Statistik Utama">
            <div class="row g-2 g-sm-3 nx-stat-grid">
                <!-- Kartu 1: Total Warga / Anggota Keluarga -->
                <div class="col-6 col-xl-3">
                    <a href="{{ $isWarga ? route('profile.edit') : route('warga.index') }}" class="nx-stat-card nx-stat-green">
                        <div class="nx-stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="nx-stat-info">
                            <div class="nx-stat-value">{{ number_format($totalJiwa, 0, ',', '.') }}</div>
                            <p class="nx-stat-label">
                                @if($isAdminRW)
                                    Total Warga RW 018
                                @elseif($isAdminRT)
                                    Warga RT {{ $user->rt_number }}
                                @else
                                    Anggota Keluarga
                                @endif
                            </p>
                            <span class="nx-stat-pill">
                                {{ $totalKK }} KK
                            </span>
                        </div>
                    </a>
                </div>

                <!-- Kartu 2: Saldo Kas RT/RW atau Total Iuran Warga -->
                <div class="col-6 col-xl-3">
                    <a href="{{ $isWarga ? route('laporan.iuran') : route('pembayaran.index') }}" class="nx-stat-card nx-stat-blue">
                        <div class="nx-stat-icon">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div class="nx-stat-info">
                            <div class="nx-stat-value">Rp {{ number_format($saldo, 0, ',', '.') }}</div>
                            <p class="nx-stat-label">
                                @if($isAdminRW)
                                    Saldo Kas RW
                                @elseif($isAdminRT)
                                    Saldo Kas RT {{ $user->rt_number }}
                                @else
                                    Total Iuran
                                @endif
                            </p>
                            @if($isWarga)
                                <span class="nx-stat-pill" style="color: {{ $wargaIuranLunas ? '#285943' : '#B84343' }};">
                                    {{ $wargaIuranLunas ? 'Iuran ' . \Carbon\Carbon::now()->locale('id')->translatedFormat('M') . ': Lunas ✓' : 'Iuran ' . \Carbon\Carbon::now()->locale('id')->translatedFormat('M') . ': Belum Lunas' }}
                                </span>
                            @else
                                <span class="nx-stat-pill" style="color: #2B5F8E;">
                                    RT {{ $isAdminRT ? $user->rt_number : '006-010' }}
                                </span>
                            @endif
                        </div>
                    </a>
                </div>

                <!-- Kartu 3: Bank Sampah (Data Riil Terpadu) -->
                <div class="col-6 col-xl-3">
                    <a href="{{ route('bank_sampah.index') }}" class="nx-stat-card nx-stat-green">
                        <div class="nx-stat-icon">
                            <i class="fas fa-recycle"></i>
                        </div>
                        <div class="nx-stat-info">
                            @if($isWarga)
                                <div class="nx-stat-value">Rp {{ number_format($bankSampahSaldoWarga ?? 0, 0, ',', '.') }}</div>
                                <p class="nx-stat-label">Tabungan Sampah</p>
                            @elseif($isAdminRT)
                                <div class="nx-stat-value">{{ number_format($bankSampahKgRt ?? 0, 1, ',', '.') }} kg</div>
                                <p class="nx-stat-label">Sampah RT {{ $user->rt_number }}</p>
                            @else
                                <div class="nx-stat-value">{{ number_format($bankSampahKgRw ?? 0, 1, ',', '.') }} kg</div>
                                <p class="nx-stat-label">Sampah RW 018</p>
                            @endif
                            @if($isWarga)
                                <span class="nx-stat-pill" style="color: #285943;">
                                    <i class="fas fa-weight-hanging me-1"></i>{{ number_format($bankSampahKgWarga ?? 0, 1, ',', '.') }} kg
                                </span>
                            @elseif($isAdminRT)
                                <span class="nx-stat-pill" style="color: #285943;">
                                    Rp {{ number_format($bankSampahRupiahRt ?? 0, 0, ',', '.') }}
                                </span>
                            @else
                                <span class="nx-stat-pill" style="color: #285943;">
                                    <i class="fas fa-leaf me-1"></i>Ekonomi Hijau
                                </span>
                            @endif
                        </div>
                    </a>
                </div>

                <!-- Kartu 4: Laporan Baru (Pengaduan Warga) -->
                <div class="col-6 col-xl-3">
                    <a href="{{ route('pengaduan.index') }}" class="nx-stat-card nx-stat-blue">
                        <div class="nx-stat-icon">
                            <i class="fas fa-triangle-exclamation"></i>
                        </div>
                        <div class="nx-stat-info">
                            <div class="nx-stat-value">{{ $newFeatureCounts['pengaduan_baru'] ?? 0 }}</div>
                            <p class="nx-stat-label">
                                {{ $isWarga ? 'Laporan Diproses' : 'Laporan Baru' }}
                            </p>
                            <span class="nx-stat-pill" style="color: #2B5F8E;">
                                Rincian <i class="fas fa-arrow-right ms-1"></i>
                            </span>
                        </div>
                    </a>
                </div>
            </div>
        </section>

        @if($isWarga)
        <!-- SECTION KHUSUS WARGA: RINGKASAN TAGIHAN IURAN KELUARGA (POINT 5.E) -->
        <section class="mb-4">
            <div class="nx-card p-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="rounded-3 d-inline-flex align-items-center justify-content-center"
                              style="width: 36px; height: 36px; background: var(--nx-mint); color: var(--nx-forest);">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </span>
                        <div>
                            <h4 class="fw-bold mb-0" style="font-size: 1.05rem;">Ringkasan Tagihan Iuran Keluarga</h4>
                            <small class="text-muted">Kewajiban iuran bulanan kebersihan &amp; keamanan lingkungan RT {{ $user->rt_number }}</small>
                        </div>
                    </div>
                    <a href="{{ route('laporan.iuran') }}" class="btn btn-sm btn-primary">
                        Lihat Semua Tagihan <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>

                <!-- 4 KARTU STATUS TAGIHAN -->
                <div class="row g-2.5 mb-3">
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded-3 h-100" style="background: var(--nx-bg); border: 1px solid var(--nx-border);">
                            <span class="text-muted small fw-bold text-uppercase d-block" style="font-size: 0.68rem;">Tagihan Bulan Ini</span>
                            <h5 class="fw-bold my-1" style="color: var(--nx-forest);">Rp {{ number_format($wargaTagihanAktif ?? 100000, 0, ',', '.') }}</h5>
                            <span class="badge rounded-pill {{ ($wargaStatusTagihan ?? '') === 'Lunas' ? 'bg-success' : 'bg-warning text-dark' }}" style="font-size: 0.7rem;">
                                {{ $wargaStatusTagihan ?? 'Belum Bayar' }}
                            </span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded-3 h-100 nx-card-tunggakan-warga {{ ($wargaTotalTunggakan ?? 0) > 0 ? 'has-tunggakan' : 'no-tunggakan' }}" style="background: {{ ($wargaTotalTunggakan ?? 0) > 0 ? 'var(--nx-soft-red)' : 'var(--nx-mint-soft)' }}; border: 1px solid {{ ($wargaTotalTunggakan ?? 0) > 0 ? '#F8B4B7' : 'var(--nx-sage)' }};">
                            <span class="text-muted small fw-bold text-uppercase d-block nx-tunggakan-label" style="font-size: 0.68rem;">Total Tunggakan</span>
                            <h5 class="fw-bold my-1 nx-tunggakan-nominal" style="color: {{ ($wargaTotalTunggakan ?? 0) > 0 ? 'var(--nx-danger)' : 'var(--nx-forest)' }};">
                                Rp {{ number_format($wargaTotalTunggakan ?? 0, 0, ',', '.') }}
                            </h5>
                            <small class="text-muted nx-tunggakan-subtext" style="font-size: 0.7rem;">
                                {{ ($wargaTotalTunggakan ?? 0) > 0 ? ($wargaCountBelumBayar ?? 0) . ' periode tertunda' : 'Semua periode lunas ✓' }}
                            </small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded-3 h-100" style="background: var(--nx-bg); border: 1px solid var(--nx-border);">
                            <span class="text-muted small fw-bold text-uppercase d-block" style="font-size: 0.68rem;">Status Verifikasi</span>
                            <h5 class="fw-bold my-1" style="color: #2B5F8E;">{{ $wargaCountMenungguKonfirmasi ?? 0 }} Pengajuan</h5>
                            <small class="text-muted" style="font-size: 0.7rem;">Menunggu diperiksa pengurus</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded-3 h-100" style="background: var(--nx-bg); border: 1px solid var(--nx-border);">
                            <span class="text-muted small fw-bold text-uppercase d-block" style="font-size: 0.68rem;">Jatuh Tempo</span>
                            <h5 class="fw-bold my-1" style="color: var(--nx-text);">Tgl 10 / Bulan</h5>
                            <small class="text-muted" style="font-size: 0.7rem;">
                                @if(($wargaCountTerlambat ?? 0) > 0)
                                    <span class="text-danger fw-bold"><i class="fas fa-exclamation-circle me-1"></i>{{ $wargaCountTerlambat }} Tagihan Lewat Tempo</span>
                                @else
                                    <span class="text-success"><i class="fas fa-check-circle me-1"></i>Tepat Waktu</span>
                                @endif
                            </small>
                        </div>
                    </div>
                </div>

                <!-- TABEL 4 PERIODE TERBARU -->
                @if(isset($wargaDaftarTagihan) && $wargaDaftarTagihan->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead>
                                <tr class="bg-light">
                                    <th class="ps-3" style="width: 30%;">Periode Iuran</th>
                                    <th style="width: 25%;">Nominal</th>
                                    <th style="width: 25%;">Status</th>
                                    <th class="pe-3 text-end" style="width: 20%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($wargaDaftarTagihan->take(4) as $wdt)
                                    <tr>
                                        <td class="ps-3">
                                            <strong style="color: var(--nx-text);">{{ $wdt->periode }}</strong>
                                            <small class="text-muted d-block">Tempo: {{ $wdt->due_date }}</small>
                                        </td>
                                        <td>
                                            <span class="fw-bold">Rp {{ number_format($wdt->nominal, 0, ',', '.') }}</span>
                                        </td>
                                        <td>
                                            @if($wdt->status === 'Lunas')
                                                <span class="badge rounded-pill px-2.5 py-1" style="background: var(--nx-mint); color: var(--nx-forest);">
                                                    <i class="fas fa-check-circle me-1"></i>Lunas
                                                </span>
                                            @elseif($wdt->status === 'Menunggu Konfirmasi')
                                                <span class="badge rounded-pill px-2.5 py-1 bg-warning text-dark">
                                                    <i class="fas fa-clock me-1"></i>Menunggu
                                                </span>
                                            @else
                                                <span class="badge rounded-pill px-2.5 py-1 nx-badge-status-danger" style="background: var(--nx-soft-red); color: var(--nx-danger);">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>{{ $wdt->status }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="pe-3 text-end">
                                            @if($wdt->status === 'Lunas' && $wdt->pembayaran_id)
                                                <a href="{{ route('iuran.kuitansi', $wdt->pembayaran_id) }}" target="_blank" class="btn btn-xs btn-outline-success py-1 px-2.5">
                                                    <i class="fas fa-receipt me-1"></i>Kuitansi
                                                </a>
                                            @else
                                                <a href="{{ route('laporan.iuran') }}" class="btn btn-xs btn-primary py-1 px-2.5">
                                                    <i class="fas fa-upload me-1"></i>Bayar
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <!-- KARTU KELUARGA: TABUNGAN BANK SAMPAH & EKONOMI SIRKULAR -->
                <div class="p-3 rounded-3 mt-3 d-flex flex-wrap align-items-center justify-content-between gap-3 nx-bar-bank-sampah-warga"
                     style="background: linear-gradient(135deg, var(--nx-mint-soft) 0%, #E8F5E9 100%); border: 1px solid var(--nx-sage);">
                    <div class="d-flex align-items-center gap-3">
                        <span class="rounded-3 d-inline-flex align-items-center justify-content-center nx-bar-sampah-icon"
                              style="width: 42px; height: 42px; background: var(--nx-forest); color: #fff; font-size: 1.1rem;">
                            <i class="fas fa-recycle"></i>
                        </span>
                        <div>
                            <h6 class="fw-bold mb-0 nx-bar-sampah-title" style="color: var(--nx-forest);">Tabungan Bank Sampah Keluarga: Rp {{ number_format($bankSampahSaldoWarga ?? 0, 0, ',', '.') }}</h6>
                            <small class="text-muted nx-bar-sampah-desc">
                                Total sampah terpilah yang telah disetorkan: <strong>{{ number_format($bankSampahKgWarga ?? 0, 1, ',', '.') }} kg</strong>. Saldo tabungan dapat ditarik tunai atau dialokasikan untuk iuran warga.
                            </small>
                        </div>
                    </div>
                    <a href="{{ route('bank_sampah.index') }}" class="btn btn-sm btn-outline-success nx-btn-bank-sampah-warga d-inline-flex align-items-center gap-1.5" style="border-radius: 8px; font-weight: 600; font-size: 0.8rem;">
                        <span>Buka Buku Tabungan Sampah</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </section>
        @endif

        <!-- 8 & 9. GRAFIK KEUANGAN + PENGUMUMAN TERBARU -->
        <section>
            <div class="row g-4">
                <!-- 8. GRAFIK KEUANGAN -->
                <div class="col-12 col-xl-7">
                    <div class="nx-card">
                        <div class="nx-card-header flex-wrap">
                            <h3 class="nx-card-title">
                                <span class="nx-card-title-icon"><i class="fas fa-chart-line"></i></span>
                                {{ $isWarga ? 'Grafik Riwayat Iuran Saya' : 'Grafik Keuangan' }}
                            </h3>

                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <div class="d-flex align-items-center gap-2 small fw-semibold" style="color: var(--nx-muted);">
                                    <span style="width:10px;height:10px;border-radius:50%;background:#3D8B63;display:inline-block;"></span> Pemasukan
                                    <span class="ms-2" style="width:10px;height:10px;border-radius:50%;background:#4A89DC;display:inline-block;"></span> Pengeluaran
                                </div>

                                <select id="nxChartPeriod" class="form-select form-select-sm fw-semibold"
                                        style="width: auto; border-radius: 999px; border-color: var(--nx-border); background-color: var(--nx-bg); color: var(--nx-text); font-size: 0.78rem; padding-right: 28px;">
                                    <option value="year" selected>Tahun 2026 (Jan - Des)</option>
                                    <option value="6months">6 Bulan Terakhir</option>
                                    <option value="week">7 Hari Terakhir</option>
                                </select>
                            </div>
                        </div>

                        <!-- Ringkasan Total Pemasukan & Pengeluaran -->
                        <div class="row g-2 mb-3">
                            <div class="col-6 col-sm-3">
                                <div class="p-2 px-2.5 rounded-3" style="background: var(--nx-mint-soft); border: 1px solid var(--nx-border);">
                                    <small class="d-block text-muted" style="font-size: 0.68rem;">Total Pemasukan</small>
                                    <span class="fw-bold" style="color: var(--nx-forest); font-size: 0.88rem;">
                                        +Rp {{ number_format($pemasukan, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-6 col-sm-3">
                                <div class="p-2 px-2.5 rounded-3" style="background: var(--nx-powder-soft); border: 1px solid #D5E5F2;">
                                    <small class="d-block text-muted" style="font-size: 0.68rem;">Total Pengeluaran</small>
                                    <span class="fw-bold nx-stat-expense-val" style="color: #2B5F8E; font-size: 0.88rem;">
                                        -Rp {{ number_format($pengeluaran, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-6 col-sm-3">
                                <div class="p-2 px-2.5 rounded-3" style="background: var(--nx-bg); border: 1px solid var(--nx-border);">
                                    <small class="d-block text-muted" style="font-size: 0.68rem;">Bulan Berjalan</small>
                                    <span class="fw-bold" style="color: var(--nx-text); font-size: 0.82rem;">
                                        <span class="text-success">+{{ number_format(($pemasukanBulanIni ?? 0)/1000000, 1) }}Jt</span> / <span class="text-danger">-{{ number_format(($pengeluaranBulanIni ?? 0)/1000000, 1) }}Jt</span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-6 col-sm-3">
                                <div class="p-2 px-2.5 rounded-3" style="background: var(--nx-bg); border: 1px solid var(--nx-border);">
                                    <small class="d-block text-muted" style="font-size: 0.68rem;">
                                        {{ $isWarga ? 'Status Bulan Ini' : 'Kepatuhan KK' }}
                                    </small>
                                    <span class="fw-bold" style="color: var(--nx-text); font-size: 0.88rem;">
                                        @if($isWarga)
                                            {{ ($wargaStatusTagihan ?? '') === 'Lunas' ? 'Lunas ✓' : 'Belum Bayar' }}
                                        @else
                                            {{ $paidThisMonth }}/{{ $totalKKForReminder }} KK Lunas
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div style="height: 245px; position: relative;">
                            <canvas id="kasChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- 9. PENGUMUMAN TERBARU -->
                <div class="col-12 col-xl-5">
                    <div class="nx-card">
                        <div class="nx-card-header">
                            <h3 class="nx-card-title">
                                <span class="nx-card-title-icon"><i class="fas fa-bullhorn"></i></span>
                                Pengumuman Terbaru
                            </h3>
                            <a href="{{ route('pengumuman.index') }}" class="nx-link-more">
                                Lihat Semua <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>

                        <!-- Item 1: Kegiatan Posyandu / Lingkungan -->
                        <a href="{{ route('posyandu.index') }}" class="nx-announce-item">
                            <div class="nx-announce-icon nx-badge-kegiatan">
                                <i class="fas fa-leaf"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <h6 class="fw-bold mb-1" style="font-size: 0.9rem;">
                                        {{ $latestPosyandu ? $latestPosyandu->nama : 'Posyandu & Kesehatan Lingkungan' }}
                                    </h6>
                                    <span class="nx-category-pill nx-badge-kegiatan">Kegiatan</span>
                                </div>
                                <p class="mb-0 text-muted" style="font-size: 0.78rem;">
                                    @if($latestPosyandu)
                                        {{ \Carbon\Carbon::parse($latestPosyandu->tanggal)->translatedFormat('l, d F Y') }} • {{ $latestPosyandu->lokasi ?? 'Balai Warga RW 018' }}
                                    @else
                                        Pantau jadwal kegiatan kesehatan &amp; kerja bakti warga secara berkala.
                                    @endif
                                </p>
                            </div>
                        </a>

                        <!-- Item 2: Informasi Pembayaran Iuran Bulanan (Data Riil) -->
                        <a href="{{ route('laporan.iuran') }}" class="nx-announce-item">
                            <div class="nx-announce-icon nx-badge-info">
                                <i class="fas fa-circle-info"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <h6 class="fw-bold mb-1" style="font-size: 0.9rem;">
                                        Informasi Iuran Bulan {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}
                                    </h6>
                                    <span class="nx-category-pill nx-badge-info">Informasi</span>
                                </div>
                                <p class="mb-0 text-muted" style="font-size: 0.78rem;">
                                    @if($isWarga)
                                        @if($wargaIuranLunas)
                                            Terima kasih! Iuran Anda bulan ini sudah tercatat Lunas.
                                        @else
                                            Mohon segera melakukan pembayaran iuran bulanan kepada pengurus RT {{ $user->rt_number }}.
                                        @endif
                                    @else
                                        Tercatat <strong>{{ $paidThisMonth }} KK sudah bayar</strong> dari {{ $totalKKForReminder }} KK ({{ $unpaidKK }} KK belum bayar).
                                    @endif
                                </p>
                            </div>
                        </a>

                        <!-- Item 3: UMKM Pilihan / Pengeluaran Kas Terbaru -->
                        @if(!$isWarga && $latestExpenseAmount > 0)
                            <a href="{{ route('pembayaran.index') }}" class="nx-announce-item">
                                <div class="nx-announce-icon nx-badge-kesehatan">
                                    <i class="fas fa-receipt"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                        <h6 class="fw-bold mb-1" style="font-size: 0.9rem;">
                                            Transparansi Pengeluaran Kas
                                        </h6>
                                        <span class="nx-category-pill nx-badge-kesehatan">Administrasi</span>
                                    </div>
                                    <p class="mb-0 text-muted" style="font-size: 0.78rem;">
                                        Pengeluaran terakhir sebesar <strong>Rp {{ number_format($latestExpenseAmount, 0, ',', '.') }}</strong> pada {{ $latestExpenseDate }}.
                                    </p>
                                </div>
                            </a>
                        @else
                            <a href="{{ route('umkm.index') }}" class="nx-announce-item">
                                <div class="nx-announce-icon nx-badge-umkm">
                                    <i class="fas fa-store"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                        <h6 class="fw-bold mb-1" style="font-size: 0.9rem;">
                                            {{ $featuredUmkm ? $featuredUmkm->nama : 'Dukung UMKM Warga RW 018' }}
                                        </h6>
                                        <span class="nx-category-pill nx-badge-umkm">Ekonomi</span>
                                    </div>
                                    <p class="mb-0 text-muted" style="font-size: 0.78rem;">
                                        @if($featuredUmkm)
                                            Kategori {{ $featuredUmkm->kategori }} • Milik {{ $featuredUmkm->pemilik ?? 'Warga RT ' . $featuredUmkm->rt_number }}
                                        @else
                                            Temukan produk kuliner &amp; jasa terbaik dari warga sekitar kita.
                                        @endif
                                    </p>
                                </div>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <!-- 10 & 11. KEGIATAN MENDATANG + STATUS LAYANAN -->
        <section id="sectionKegiatan">
            <div class="row g-4">
                <!-- 10. KEGIATAN MENDATANG -->
                <div class="col-12 col-xl-5">
                    <div class="nx-card">
                        <div class="nx-card-header">
                            <h3 class="nx-card-title">
                                <span class="nx-card-title-icon"><i class="far fa-calendar-check"></i></span>
                                Kegiatan Mendatang
                            </h3>
                            <a href="{{ route('kalender.index') }}" class="nx-link-more">
                                Lihat Semua <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>

                        @if($latestPosyandu)
                            @php
                                $eventDate = \Carbon\Carbon::parse($latestPosyandu->tanggal);
                            @endphp
                            <div class="nx-event-box mb-3">
                                <div class="nx-date-badge">
                                    <span class="day">{{ $eventDate->format('d') }}</span>
                                    <span class="month">{{ $eventDate->translatedFormat('M') }}</span>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="badge rounded-pill mb-1" style="background: var(--nx-forest); color: #fff; font-size: 0.65rem;">
                                        Terjadwal
                                    </span>
                                    <h5 class="fw-bold mb-1" style="font-size: 1rem; color: var(--nx-text);">
                                        {{ $latestPosyandu->nama }}
                                    </h5>
                                    <div class="text-muted" style="font-size: 0.78rem;">
                                        <div><i class="far fa-clock me-1 text-success"></i>08.00 WIB - Selesai</div>
                                        <div><i class="fas fa-location-dot me-1 text-success"></i>{{ $latestPosyandu->lokasi ?? 'Lingkungan RW 018' }}</div>
                                    </div>
                                </div>
                                <a href="{{ route('posyandu.index') }}" class="btn btn-sm fw-bold px-3 py-2"
                                   style="background: var(--nx-forest); color: #fff; border-radius: 10px; font-size: 0.76rem;">
                                    Detail
                                </a>
                            </div>

                            @if($upcomingPosyandus->count() > 1)
                                <div class="d-flex flex-column gap-2">
                                    @foreach($upcomingPosyandus->skip(1) as $item)
                                        <div class="d-flex align-items-center justify-content-between p-2 px-3 rounded-3" style="background: var(--nx-bg); border: 1px solid var(--nx-border);">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fas fa-calendar-day text-success"></i>
                                                <div>
                                                    <div class="fw-bold" style="font-size: 0.82rem;">{{ $item->nama }}</div>
                                                    <small class="text-muted" style="font-size: 0.72rem;">{{ $item->lokasi ?? 'RW 018' }}</small>
                                                </div>
                                            </div>
                                            <span class="badge rounded-pill" style="background: var(--nx-mint); color: var(--nx-forest); font-size: 0.72rem;">
                                                {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <div class="text-center py-4 px-3 rounded-4" style="background: var(--nx-bg); border: 1px dashed var(--nx-sage);">
                                <i class="far fa-calendar-plus fa-2x mb-2" style="color: var(--nx-forest);"></i>
                                <h6 class="fw-bold mb-1" style="font-size: 0.9rem;">Belum Ada Jadwal Kegiatan</h6>
                                <p class="text-muted small mb-2">Kegiatan Posyandu atau warga yang ditambahkan akan tampil di sini.</p>
                                <a href="{{ route('posyandu.create') }}" class="btn btn-sm fw-semibold" style="background: var(--nx-mint); color: var(--nx-forest); border-radius: 10px;">
                                    + Tambah Jadwal
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- 11. STATUS LAYANAN -->
                <div class="col-12 col-xl-7">
                    <div class="nx-card">
                        <div class="nx-card-header">
                            <h3 class="nx-card-title">
                                <span class="nx-card-title-icon"><i class="fas fa-layer-group"></i></span>
                                Status Layanan {{ $isWarga ? 'Saya' : ($isAdminRT ? 'RT ' . $user->rt_number : 'RW 018') }}
                            </h3>
                            <a href="{{ route('surat.index') }}" class="nx-link-more">
                                Lihat Detail <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>

                        <div class="row g-3">
                            <!-- Surat -->
                            <div class="col-6 col-md-3">
                                <a href="{{ route('surat.index') }}" class="nx-service-card mint">
                                    <div class="nx-service-icon">
                                        <i class="fas fa-file-signature"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold" style="font-size: 0.92rem;">Surat</div>
                                        <div class="text-muted fw-semibold" style="font-size: 0.78rem;">
                                            @if(($newFeatureCounts['surat_pending'] ?? 0) > 0)
                                                {{ $newFeatureCounts['surat_pending'] }} Diproses
                                            @else
                                                {{ $newFeatureCounts['surat'] }} Tercatat
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <!-- Pengaduan -->
                            <div class="col-6 col-md-3">
                                <a href="{{ route('pengaduan.index') }}" class="nx-service-card mint">
                                    <div class="nx-service-icon">
                                        <i class="fas fa-comment-dots"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold" style="font-size: 0.92rem;">Pengaduan</div>
                                        <div class="text-muted fw-semibold" style="font-size: 0.78rem;">
                                            {{ $newFeatureCounts['pengaduan_baru'] ?? 0 }} Diproses
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <!-- Posyandu -->
                            <div class="col-6 col-md-3">
                                <a href="{{ route('posyandu.index') }}" class="nx-service-card mint">
                                    <div class="nx-service-icon">
                                        <i class="fas fa-heart"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold" style="font-size: 0.92rem;">Posyandu</div>
                                        <div class="text-muted fw-semibold" style="font-size: 0.78rem;">
                                            {{ ($newFeatureCounts['posyandu'] ?? 0) > 0 ? $newFeatureCounts['posyandu'] . ' Jadwal Aktif' : 'Belum Ada' }}
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <!-- UMKM -->
                            <div class="col-6 col-md-3">
                                <a href="{{ route('umkm.index') }}" class="nx-service-card blue">
                                    <div class="nx-service-icon">
                                        <i class="fas fa-store"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold" style="font-size: 0.92rem;">UMKM</div>
                                        <div class="text-muted fw-semibold" style="font-size: 0.78rem;">
                                            {{ $newFeatureCounts['umkm'] ?? 0 }} Mitra
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 12. PANEL KHUSUS BERDASARKAN ROLE (RW / RT / WARGA) -->
        <section>
            @if($isAdminRW)
                <!-- PANEL KHUSUS ADMIN RW: RINGKASAN 5 RT & DEMOGRAFI -->
                <div class="row g-4">
                    <div class="col-12 col-xl-8">
                        <div class="nx-card">
                            <div class="nx-card-header d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-2">
                                <h3 class="nx-card-title mb-0">
                                    <span class="nx-card-title-icon"><i class="fas fa-building-user"></i></span>
                                    <span>Rekapitulasi Wilayah RT 006 – RT 010 (Admin RW)</span>
                                </h3>
                                <a href="{{ route('laporan.iuran') }}" class="nx-link-more text-nowrap">
                                    <span>Filter per RT</span> <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                            <div class="table-responsive">
                                <table class="table align-middle mb-0 nx-rt-summary-table" style="font-size: 0.85rem;">
                                    <thead style="background: var(--nx-mint-soft);">
                                        <tr>
                                            <th class="py-2 ps-3 rounded-start">Wilayah RT</th>
                                            <th class="py-2">Kepala Keluarga</th>
                                            <th class="py-2">Total Warga</th>
                                            <th class="py-2 pe-3 text-end rounded-end">Saldo Kas RT</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($rtSummaries as $rtItem)
                                            <tr>
                                                <td class="ps-3 fw-bold" style="color: var(--nx-forest);">RT {{ $rtItem->rt_number }}</td>
                                                <td>{{ $rtItem->kk }} KK</td>
                                                <td>{{ $rtItem->warga }} Jiwa</td>
                                                <td class="pe-3 text-end fw-bold">Rp {{ number_format($rtItem->saldo, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-xl-4">
                        <div class="nx-card">
                            <div class="nx-card-header">
                                <h3 class="nx-card-title">
                                    <span class="nx-card-title-icon"><i class="fas fa-venus-mars"></i></span>
                                    Demografi Gender Warga
                                </h3>
                            </div>
                            <div class="d-flex align-items-center justify-content-around py-2">
                                <div style="width: 125px; height: 125px;">
                                    <canvas id="genderChart"></canvas>
                                </div>
                                <div>
                                    <div class="mb-2">
                                        <span class="d-inline-block rounded-circle me-1" style="width:10px;height:10px;background:#3D8B63;"></span>
                                        <small class="text-muted">Laki-laki</small>
                                        <div class="fw-bold fs-5">{{ $maleCount }} Jiwa</div>
                                    </div>
                                    <div>
                                        <span class="d-inline-block rounded-circle me-1" style="width:10px;height:10px;background:#6AA6D8;"></span>
                                        <small class="text-muted">Perempuan</small>
                                        <div class="fw-bold fs-5">{{ $femaleCount }} Jiwa</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($isAdminRT)
                <!-- RINGKASAN OPERASIONAL ADMIN RT (SURAT PENDING & STATUS LAPORAN RT BULAN BERJALAN) -->
                <div class="row g-3 g-md-4 mb-4">
                    <div class="col-12 col-md-6">
                        <div class="nx-card p-3 p-sm-3.5 h-100 nx-rt-summary-card" style="background: linear-gradient(135deg, #F0F6FC 0%, #E8F1F8 100%); border: 1px solid rgba(43, 95, 142, 0.18);">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="d-flex align-items-center gap-2 fw-bold" style="color: var(--nx-text); font-size: 0.95rem;">
                                    <span class="rounded-3 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(43, 95, 142, 0.15); color: #2B5F8E;">
                                        <i class="fas fa-file-signature"></i>
                                    </span>
                                    Antrean Surat Menunggu Persetujuan RT
                                </span>
                                <span class="badge {{ ($suratPendingRtCount ?? 0) > 0 ? 'bg-danger' : 'bg-success' }} px-2.5 py-1.5" style="border-radius: 8px;">
                                    {{ $suratPendingRtCount ?? 0 }} Berkas
                                </span>
                            </div>
                            <p class="text-muted small mb-3">
                                @if(($suratPendingRtCount ?? 0) > 0)
                                    Terdapat {{ $suratPendingRtCount }} pengajuan surat dari warga RT {{ $user->rt_number }} yang memerlukan verifikasi dan persetujuan pengantar Anda.
                                @else
                                    Tidak ada antrean surat pending. Seluruh permohonan surat warga RT {{ $user->rt_number }} telah selesai ditindaklanjuti.
                                @endif
                            </p>
                            <a href="{{ route('surat.index', ['status' => 'pending_rt']) }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1.5" style="border-radius: 8px; font-size: 0.78rem;">
                                <span>Buka Antrean Surat RT</span>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="nx-card p-3 p-sm-3.5 h-100 nx-rt-summary-card" style="background: linear-gradient(135deg, #EDF7F1 0%, #E2EFE7 100%); border: 1px solid rgba(40, 89, 67, 0.18);">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="d-flex align-items-center gap-2 fw-bold" style="color: var(--nx-text); font-size: 0.95rem;">
                                    <span class="rounded-3 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(40, 89, 67, 0.15); color: var(--nx-forest);">
                                        <i class="fas fa-clipboard-check"></i>
                                    </span>
                                    Status Laporan Resmi RT ({{ $periodeLaporanRtBerjalan ?? \Carbon\Carbon::now()->translatedFormat('F Y') }})
                                </span>
                                @php
                                    $stBadge = match($statusLaporanRtBerjalan ?? 'belum_dibuat') {
                                        'diterima' => 'bg-success',
                                        'menunggu' => 'bg-info text-dark',
                                        'draft' => 'bg-warning text-dark',
                                        'perlu_revisi' => 'bg-danger',
                                        default => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $stBadge }} px-2.5 py-1.5" style="border-radius: 8px;">
                                    {{ $statusLaporanRtLabel ?? 'Belum Dibuat' }}
                                </span>
                            </div>
                            <p class="text-muted small mb-3">
                                @if(($statusLaporanRtBerjalan ?? 'belum_dibuat') === 'belum_dibuat')
                                    Laporan bulanan RT {{ $user->rt_number }} untuk periode {{ $periodeLaporanRtBerjalan ?? \Carbon\Carbon::now()->translatedFormat('F Y') }} belum dibuat. Segera susun laporan untuk diteruskan ke RW.
                                @elseif($statusLaporanRtBerjalan === 'menunggu')
                                    Laporan RT periode {{ $periodeLaporanRtBerjalan }} telah dikirim dan sedang menunggu verifikasi oleh Pengurus RW 018.
                                @elseif($statusLaporanRtBerjalan === 'diterima')
                                    Laporan RT periode {{ $periodeLaporanRtBerjalan }} telah disetujui dan diterima resmi oleh Pengurus RW 018.
                                @elseif($statusLaporanRtBerjalan === 'perlu_revisi')
                                    Laporan RT periode {{ $periodeLaporanRtBerjalan }} memerlukan perbaikan sesuai catatan dari Pengurus RW 018.
                                @else
                                    Laporan RT periode {{ $periodeLaporanRtBerjalan }} tersimpan sebagai draft dan belum dikirimkan ke RW.
                                @endif
                            </p>
                            <a href="{{ route('laporan_rt.index') }}" class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1.5" style="border-radius: 8px; font-size: 0.78rem;">
                                <span>Kelola Laporan Resmi RT</span>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- PANEL KHUSUS ADMIN RT: AKSI CEPAT PENGURUS RT & DEMOGRAFI RT -->
                <div class="row g-4">
                    <div class="col-12 col-xl-8">
                        <div class="nx-card">
                            <div class="nx-card-header">
                                <h3 class="nx-card-title">
                                    <span class="nx-card-title-icon"><i class="fas fa-bolt"></i></span>
                                    Panel Tugas Pengurus RT {{ $user->rt_number }}
                                </h3>
                            </div>
                            <div class="row g-3">
                                <div class="col-sm-6 col-md-3">
                                    <a href="{{ route('warga.index') }}" class="nx-service-card mint text-center align-items-center">
                                        <div class="nx-service-icon mx-auto"><i class="fas fa-id-card"></i></div>
                                        <div class="fw-bold small">Data &amp; Rekam E-KTP</div>
                                    </a>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <a href="{{ route('pembayaran.create') }}" class="nx-service-card blue text-center align-items-center">
                                        <div class="nx-service-icon mx-auto"><i class="fas fa-plus-circle"></i></div>
                                        <div class="fw-bold small">Catat Kas RT {{ $user->rt_number }}</div>
                                    </a>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <a href="{{ route('surat.index', ['filter' => 'pending']) }}" class="nx-service-card mint text-center align-items-center">
                                        <div class="nx-service-icon mx-auto"><i class="fas fa-check-double"></i></div>
                                        <div class="fw-bold small">Verifikasi Surat RT</div>
                                    </a>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <a href="{{ route('laporan.cetak_bulanan') }}" class="nx-service-card blue text-center align-items-center">
                                        <div class="nx-service-icon mx-auto"><i class="fas fa-print"></i></div>
                                        <div class="fw-bold small">Cetak Laporan RT</div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-xl-4">
                        <div class="nx-card">
                            <div class="nx-card-header">
                                <h3 class="nx-card-title">
                                    <span class="nx-card-title-icon"><i class="fas fa-venus-mars"></i></span>
                                    Gender Warga RT {{ $user->rt_number }}
                                </h3>
                            </div>
                            <div class="d-flex align-items-center justify-content-around py-1">
                                <div style="width: 110px; height: 110px;">
                                    <canvas id="genderChart"></canvas>
                                </div>
                                <div>
                                    <div class="mb-2">
                                        <small class="text-muted">Laki-laki:</small>
                                        <strong class="d-block">{{ $maleCount }} Jiwa</strong>
                                    </div>
                                    <div>
                                        <small class="text-muted">Perempuan:</small>
                                        <strong class="d-block">{{ $femaleCount }} Jiwa</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- PANEL KHUSUS WARGA: AKSES CEPAT MANDIRI -->
                <div class="nx-card">
                    <div class="nx-card-header">
                        <h3 class="nx-card-title">
                            <span class="nx-card-title-icon"><i class="fas fa-compass"></i></span>
                            Akses Cepat Layanan Mandiri Warga
                        </h3>
                    </div>
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <a href="{{ route('surat.create') }}" class="nx-service-card mint">
                                <div class="nx-service-icon"><i class="fas fa-paper-plane"></i></div>
                                <div class="fw-bold small">Ajukan Surat Pengantar</div>
                                <small class="text-muted" style="font-size: 0.72rem;">Domisili, SKTM, SKU, dll.</small>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="{{ route('pengaduan.create', ['scan' => 1]) }}" class="nx-service-card blue">
                                <div class="nx-service-icon"><i class="fas fa-qrcode"></i></div>
                                <div class="fw-bold small">Lapor Fasilitas via QR</div>
                                <small class="text-muted" style="font-size: 0.72rem;">Tiang listrik, hydrant, CCTV</small>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="{{ route('laporan.iuran') }}" class="nx-service-card mint">
                                <div class="nx-service-icon"><i class="fas fa-receipt"></i></div>
                                <div class="fw-bold small">Cek Kartu Iuran Saya</div>
                                <small class="text-muted" style="font-size: 0.72rem;">
                                    {{ $wargaLastPayment ? 'Terakhir: ' . \Carbon\Carbon::parse($wargaLastPayment->tanggal)->translatedFormat('d M Y') : 'Lihat riwayat pembayaran' }}
                                </small>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="{{ route('umkm.create') }}" class="nx-service-card blue">
                                <div class="nx-service-icon"><i class="fas fa-shop"></i></div>
                                <div class="fw-bold small">Daftarkan Usaha UMKM</div>
                                <small class="text-muted" style="font-size: 0.72rem;">Promosi gratis ke warga RW 018</small>
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </section>

    </main>
</div>

<!-- MODAL TOMBOL DARURAT -->
<div class="modal fade" id="modalDarurat" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; background: #fff; color: var(--nx-text);">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-danger">
                    <i class="fas fa-triangle-exclamation me-2"></i>Layanan &amp; Kontak Darurat RW 018
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small mb-3">
                    Gunakan saluran berikut untuk keadaan darurat keamanan, kebakaran, medis, atau kerusakan fasilitas vital di lingkungan RW 018:
                </p>
                <div class="d-flex flex-column gap-2 mb-3">
                    <div class="p-3 rounded-3 d-flex justify-content-between align-items-center" style="background: var(--nx-danger-soft);">
                        <div>
                            <div class="fw-bold text-danger">Pos Keamanan / Siskamling RW 018</div>
                            <small class="text-muted">Siaga 24 Jam • Gang Utama &amp; Portal</small>
                        </div>
                        <a href="{{ route('pengaduan.create', ['scan' => 1]) }}" class="btn btn-sm btn-danger rounded-pill px-3">Lapor Cepat</a>
                    </div>
                    <div class="p-3 rounded-3 d-flex justify-content-between align-items-center" style="background: var(--nx-mint-soft);">
                        <div>
                            <div class="fw-bold" style="color: var(--nx-forest);">Ambulans &amp; Layanan Medis Darurat</div>
                            <small class="text-muted">Call Center Nasional 119 / Puskesmas</small>
                        </div>
                        <a href="tel:119" class="btn btn-sm btn-success rounded-pill px-3">Hubungi 119</a>
                    </div>
                    <div class="p-3 rounded-3 d-flex justify-content-between align-items-center" style="background: var(--nx-powder-soft);">
                        <div>
                            <div class="fw-bold" style="color: #2B5F8E;">Pemadam Kebakaran &amp; Penyelamatan</div>
                            <small class="text-muted">Call Center Damkar 113</small>
                        </div>
                        <a href="tel:113" class="btn btn-sm btn-primary rounded-pill px-3">Hubungi 113</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL EDUKASI LINGKUNGAN & BANK SAMPAH -->
<div class="modal fade" id="modalEdukasiLingkungan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; background: #fff; color: var(--nx-text);">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" style="color: var(--nx-forest);">
                    <i class="fas fa-seedling me-2"></i>Smart &amp; Green Community RW 018
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-4">
                <div class="p-3 rounded-4 mb-3" style="background: var(--nx-mint-soft); border: 1px solid var(--nx-sage);">
                    <h6 class="fw-bold mb-1" style="color: var(--nx-forest);"><i class="fas fa-recycle me-1"></i>Status Modul Bank Sampah</h6>
                    <p class="small text-muted mb-0">
                        Saat ini belum ada transaksi penimbangan Bank Sampah yang tercatat di database. Sistem menampilkan status jujur tanpa angka tiruan.
                    </p>
                </div>
                <h6 class="fw-bold small text-uppercase text-muted mb-2">Panduan Pilah Sampah Rumah Tangga</h6>
                <ul class="small text-muted mb-0 ps-3">
                    <li class="mb-1"><strong>Organik (Hijau):</strong> Sisa makanan &amp; dedaunan untuk kompos taman RT.</li>
                    <li class="mb-1"><strong>Anorganik Daur Ulang (Biru):</strong> Botol plastik, kardus, dan kaleng bernilai ekonomis.</li>
                    <li><strong>Residu &amp; B3 (Merah):</strong> Baterai bekas, lampu, dan kemasan medis.</li>
                </ul>
            </div>
            <div class="modal-footer border-top">
                <a href="{{ route('pengaduan.create', ['scan' => 1]) }}" class="btn btn-sm fw-bold px-3" style="background: var(--nx-forest); color: #fff; border-radius: 10px;">
                    Laporkan Masalah Kebersihan
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Sidebar Collapse (Desktop) & Drawer (Mobile)
    const shell = document.getElementById('nxShell');
    const collapseBtn = document.getElementById('nxCollapseBtn');
    const mobileMenuBtn = document.getElementById('nxMobileMenuBtn');
    const backdrop = document.getElementById('nxBackdrop');

    if (localStorage.getItem('nx_sidebar_collapsed') === '1') {
        shell.classList.add('sidebar-collapsed');
    }

    if (collapseBtn) {
        collapseBtn.addEventListener('click', function () {
            shell.classList.toggle('sidebar-collapsed');
            localStorage.setItem('nx_sidebar_collapsed', shell.classList.contains('sidebar-collapsed') ? '1' : '0');
        });
    }

    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', function () {
            shell.classList.toggle('mobile-open');
        });
    }

    if (backdrop) {
        backdrop.addEventListener('click', function () {
            shell.classList.remove('mobile-open');
        });
    }

    // 2. Quick Search (Ctrl + K)
    const searchInput = document.getElementById('nxQuickSearch');
    const searchResults = document.getElementById('nxSearchResults');
    const searchItems = searchResults ? searchResults.querySelectorAll('.nx-search-item') : [];

    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            searchInput?.focus();
        }
    });

    if (searchInput && searchResults) {
        searchInput.addEventListener('focus', () => searchResults.classList.add('show'));
        searchInput.addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            searchResults.classList.add('show');
            searchItems.forEach(item => {
                const text = (item.textContent + ' ' + (item.dataset.keywords || '')).toLowerCase();
                item.style.display = text.includes(q) ? 'flex' : 'none';
            });
        });

        document.addEventListener('click', function (e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.classList.remove('show');
            }
        });
    }

    // 3. Grafik Keuangan (Chart.js dengan Filter Periode Data Asli Sinkron Transaksi Kas)
    const datasetsByPeriod = {
        'year': {
            labels: {!! json_encode($chartDataYear->pluck('label')) !!},
            fullLabels: {!! json_encode($chartDataYear->pluck('label_full')) !!},
            masuk: {!! json_encode($chartDataYear->pluck('masuk')) !!},
            keluar: {!! json_encode($chartDataYear->pluck('keluar')) !!},
            net: {!! json_encode($chartDataYear->pluck('net')) !!}
        },
        '6months': {
            labels: {!! json_encode($chartData6m->pluck('label')) !!},
            fullLabels: {!! json_encode($chartData6m->pluck('label_full')) !!},
            masuk: {!! json_encode($chartData6m->pluck('masuk')) !!},
            keluar: {!! json_encode($chartData6m->pluck('keluar')) !!},
            net: {!! json_encode($chartData6m->pluck('net')) !!}
        },
        'week': {
            labels: {!! json_encode($chartDataWeek->pluck('label')) !!},
            fullLabels: {!! json_encode($chartDataWeek->pluck('label')) !!},
            masuk: {!! json_encode($chartDataWeek->pluck('masuk')) !!},
            keluar: {!! json_encode($chartDataWeek->pluck('keluar')) !!},
            net: {!! json_encode($chartDataWeek->pluck('net')) !!}
        }
    };

    let currentPeriodKey = 'year';

    const ctx = document.getElementById('kasChart')?.getContext('2d');
    if (ctx) {
        const greenGrad = ctx.createLinearGradient(0, 0, 0, 230);
        greenGrad.addColorStop(0, 'rgba(61, 139, 99, 0.24)');
        greenGrad.addColorStop(1, 'rgba(220, 235, 221, 0.02)');

        const blueGrad = ctx.createLinearGradient(0, 0, 0, 230);
        blueGrad.addColorStop(0, 'rgba(74, 137, 220, 0.20)');
        blueGrad.addColorStop(1, 'rgba(220, 234, 245, 0.02)');

        const kasChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: datasetsByPeriod['year'].labels,
                datasets: [
                    {
                        label: 'Pemasukan',
                        data: datasetsByPeriod['year'].masuk,
                        borderColor: '#3D8B63',
                        backgroundColor: greenGrad,
                        borderWidth: 2.5,
                        pointBackgroundColor: '#FFFFFF',
                        pointBorderColor: '#3D8B63',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        tension: 0.38,
                        fill: true
                    },
                    {
                        label: 'Pengeluaran',
                        data: datasetsByPeriod['year'].keluar,
                        borderColor: '#4A89DC',
                        backgroundColor: blueGrad,
                        borderWidth: 2.5,
                        pointBackgroundColor: '#FFFFFF',
                        pointBorderColor: '#4A89DC',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        tension: 0.38,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#263449',
                        titleColor: '#FFFFFF',
                        bodyColor: '#F7F9F7',
                        padding: 12,
                        cornerRadius: 12,
                        callbacks: {
                            title: function (items) {
                                if (!items || !items.length) return '';
                                const idx = items[0].dataIndex;
                                const activeDataset = datasetsByPeriod[currentPeriodKey] || datasetsByPeriod['year'];
                                return activeDataset.fullLabels[idx] || items[0].label;
                            },
                            label: function (context) {
                                const val = context.parsed.y || 0;
                                return context.dataset.label + ': ' + new Intl.NumberFormat('id-ID', {
                                    style: 'currency',
                                    currency: 'IDR',
                                    maximumFractionDigits: 0
                                }).format(val);
                            },
                            afterBody: function (items) {
                                if (!items || !items.length) return '';
                                const idx = items[0].dataIndex;
                                const activeDataset = datasetsByPeriod[currentPeriodKey] || datasetsByPeriod['year'];
                                const net = (activeDataset.net && activeDataset.net[idx] !== undefined) ? activeDataset.net[idx] : 0;
                                const netFormatted = (net >= 0 ? '+Rp ' : '-Rp ') + new Intl.NumberFormat('id-ID').format(Math.abs(net));
                                const status = net >= 0 ? 'Surplus' : 'Defisit';
                                return '\nArus Bersih (Net): ' + netFormatted + ' (' + status + ')';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#EDF3EE', drawBorder: false },
                        ticks: {
                            color: '#718096',
                            font: { family: 'Plus Jakarta Sans', size: 11 },
                            callback: function (value) {
                                if (value >= 1000000) {
                                    return (value / 1000000).toLocaleString('id-ID') + ' jt';
                                }
                                if (value >= 1000) {
                                    return (value / 1000).toLocaleString('id-ID') + ' rb';
                                }
                                return value;
                            }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: '#718096',
                            font: { family: 'Plus Jakarta Sans', size: 11 }
                        }
                    }
                }
            }
        });

        const periodSelect = document.getElementById('nxChartPeriod');
        if (periodSelect) {
            periodSelect.addEventListener('change', function () {
                currentPeriodKey = this.value;
                const chosen = datasetsByPeriod[currentPeriodKey] || datasetsByPeriod['year'];
                kasChart.data.labels = chosen.labels;
                kasChart.data.datasets[0].data = chosen.masuk;
                kasChart.data.datasets[1].data = chosen.keluar;
                kasChart.update();
            });
        }
    }

    // 4. Demografi Gender Chart (Admin RW & RT)
    const gCtx = document.getElementById('genderChart')?.getContext('2d');
    if (gCtx) {
        new Chart(gCtx, {
            type: 'doughnut',
            data: {
                labels: ['Laki-laki', 'Perempuan'],
                datasets: [{
                    data: [{{ (int) $maleCount }}, {{ (int) $femaleCount }}],
                    backgroundColor: ['#3D8B63', '#6AA6D8'],
                    borderWidth: 2,
                    borderColor: '#FFFFFF'
                }]
            },
            options: {
                cutout: '64%',
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }
});
</script>
@endsection
