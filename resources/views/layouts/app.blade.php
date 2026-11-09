<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NexaNest — Smart & Green Community')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* ==========================================================================
           NEXANEST GLOBAL DESIGN SYSTEM — MODERN GREEN COMMUNITY
           Master Visual Reference Tokens & Reusable Components
           ========================================================================== */
        :root {
            /* Primary Brand & Pastel Palette */
            --nx-forest: #285943;
            --nx-forest-dark: #1E4633;
            --nx-forest-soft: #3A755B;
            --nx-sage: #A8C9B5;
            --nx-pastel-blue: #A9C9E8;
            --nx-mint: #DCEBDD;
            --nx-mint-soft: #EEF6EF;
            --nx-powder: #DCEAF5;
            --nx-powder-soft: #EBF4FB;
            --nx-bg: #F7F9F7;
            --nx-white: #FFFFFF;
            --nx-text: #263449;
            --nx-muted: #718096;
            --nx-border: #E5EBE6;
            --nx-soft-red: #FCE8E9;
            --nx-danger: #E05353;
            --nx-danger-soft: #FCE8E9;
            --nx-warning: #D97706;
            --nx-warning-soft: #FEF3DF;
            --nx-info: #2B5F8E;
            --nx-shadow: 0 8px 26px rgba(40, 89, 67, 0.05);
            --nx-shadow-hover: 0 14px 32px rgba(40, 89, 67, 0.10);
            --nx-radius-lg: 20px;
            --nx-radius-md: 14px;
            --nx-radius-sm: 10px;

            /* Legacy Alias Compatibility so any existing inline styles look clean */
            --bg-main: #F7F9F7;
            --bg-card: #FFFFFF;
            --bg-body: #F7F9F7;
            --text-main: #263449;
            --text-muted: #718096;
            --accent: #285943;
            --accent-pink: #E05353;
            --border-color: #E5EBE6;
            --primary: #285943;
            --primary-hover: #1E4633;
        }

        body {
            background: var(--nx-bg);
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            color: var(--nx-text);
            overflow-x: hidden;
            margin: 0;
        }

        .theme-toggle-btn {
            display: none !important;
        }

        /* ==================== APP SHELL (SIDEBAR + TOPBAR + MAIN) ==================== */
        .nx-shell {
            display: flex;
            min-height: 100vh;
            background: var(--nx-bg);
            position: relative;
        }

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

        .nx-emergency-btn {
            width: 100%;
            background: var(--nx-danger-soft);
            color: var(--nx-danger);
            border: 1px solid rgba(224, 83, 83, 0.22);
            border-radius: 14px;
            padding: 11px 14px;
            font-weight: 700;
            font-size: 0.86rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.2s ease;
            cursor: pointer;
            margin-bottom: 10px;
        }

        .nx-emergency-btn:hover {
            background: #fbd5d5;
            transform: translateY(-1px);
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

        /* ==================== TOPBAR & MAIN CONTAINER ==================== */
        .nx-main {
            flex: 1;
            min-width: 0;
            padding: 20px 28px 40px;
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
            font-weight: 800;
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
            display: inline-flex;
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
            background: var(--nx-mint-soft);
        }

        .nx-profile-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--nx-mint);
        }

        /* ==================== REUSABLE GLOBAL UI COMPONENTS ==================== */
        .nx-page-header {
            background: linear-gradient(130deg, #DCEBDD 0%, #E8F3EE 52%, #DCEAF5 100%);
            border: 1px solid var(--nx-border);
            border-radius: 22px;
            padding: 24px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            box-shadow: var(--nx-shadow);
            margin-bottom: 20px;
        }

        .nx-page-title {
            font-size: 1.45rem;
            font-weight: 800;
            color: var(--nx-forest);
            letter-spacing: -0.02em;
            margin: 0 0 4px;
        }

        .nx-page-subtitle {
            font-size: 0.86rem;
            color: #4D6257;
            margin: 0;
        }

        .nx-card,
        .card,
        .card-hover,
        .stat-card {
            background: var(--nx-white) !important;
            border: 1px solid var(--nx-border) !important;
            border-radius: 20px !important;
            box-shadow: var(--nx-shadow) !important;
            color: var(--nx-text) !important;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-hover:hover,
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--nx-shadow-hover) !important;
        }

        .card-header {
            background: var(--nx-white) !important;
            border-bottom: 1px solid var(--nx-border) !important;
            color: var(--nx-text) !important;
            border-radius: 20px 20px 0 0 !important;
        }

        /* Buttons */
        .btn-primary,
        .nx-btn-primary {
            background-color: var(--nx-forest) !important;
            border-color: var(--nx-forest) !important;
            color: var(--nx-white) !important;
            font-weight: 700 !important;
            border-radius: 12px !important;
            padding: 9px 18px;
            box-shadow: 0 6px 16px rgba(40, 89, 67, 0.18);
            transition: all 0.2s ease;
        }

        .btn-primary:hover,
        .nx-btn-primary:hover {
            background-color: var(--nx-forest-dark) !important;
            border-color: var(--nx-forest-dark) !important;
            transform: translateY(-1px);
        }

        .btn-success {
            background-color: #2E7D5B !important;
            border-color: #2E7D5B !important;
            color: #FFFFFF !important;
            font-weight: 600;
            border-radius: 12px;
        }

        .btn-outline-primary,
        .btn-outline-light,
        .btn-outline-secondary,
        .nx-btn-outline {
            background-color: var(--nx-white) !important;
            border: 1.5px solid var(--nx-border) !important;
            color: var(--nx-forest) !important;
            font-weight: 600 !important;
            border-radius: 12px !important;
            transition: all 0.2s ease;
        }

        .btn-outline-primary:hover,
        .btn-outline-light:hover,
        .btn-outline-secondary:hover,
        .nx-btn-outline:hover {
            background-color: var(--nx-mint-soft) !important;
            border-color: var(--nx-sage) !important;
            color: var(--nx-forest-dark) !important;
        }

        .btn-outline-danger {
            background-color: var(--nx-soft-red) !important;
            border: 1px solid rgba(224, 83, 83, 0.25) !important;
            color: var(--nx-danger) !important;
            font-weight: 600;
            border-radius: 10px;
        }

        .btn-outline-danger:hover {
            background-color: var(--nx-danger) !important;
            color: #fff !important;
        }

        /* Forms & Inputs */
        .form-control,
        .form-select,
        .input-group-text {
            background-color: var(--nx-bg) !important;
            color: var(--nx-text) !important;
            border: 1.5px solid var(--nx-border) !important;
            border-radius: 12px !important;
            font-size: 0.9rem;
            padding: 10px 14px;
            transition: all 0.2s ease;
        }

        .form-control:focus,
        .form-select:focus {
            background-color: var(--nx-white) !important;
            border-color: var(--nx-forest) !important;
            box-shadow: 0 0 0 3px rgba(40, 89, 67, 0.12) !important;
            color: var(--nx-text) !important;
        }

        .form-control[readonly],
        .form-control:disabled,
        .form-select:disabled {
            background-color: #EDF2EE !important;
            color: var(--nx-muted) !important;
            cursor: not-allowed;
        }

        .form-label {
            color: var(--nx-text) !important;
            font-weight: 700;
            font-size: 0.83rem;
            margin-bottom: 6px;
        }

        /* Tables */
        .table {
            color: var(--nx-text) !important;
            vertical-align: middle;
            margin-bottom: 0;
        }

        .table thead th {
            background-color: var(--nx-mint-soft) !important;
            color: var(--nx-forest) !important;
            font-weight: 700;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            border-bottom: 1px solid var(--nx-border) !important;
            padding: 13px 16px;
        }

        .table tbody td {
            background-color: transparent !important;
            color: var(--nx-text) !important;
            border-bottom: 1px solid var(--nx-border) !important;
            padding: 13px 16px;
            font-size: 0.88rem;
        }

        .table-hover tbody tr:hover td {
            background-color: #F4F9F5 !important;
        }

        /* Modals */
        .modal-content {
            background-color: var(--nx-white) !important;
            color: var(--nx-text) !important;
            border: 1px solid var(--nx-border) !important;
            border-radius: 22px !important;
            box-shadow: 0 24px 60px rgba(38, 52, 73, 0.16) !important;
        }

        .modal-header,
        .modal-footer {
            border-color: var(--nx-border) !important;
        }

        .btn-close,
        .btn-close-white {
            filter: none !important;
        }

        /* Alerts */
        .alert-success {
            background-color: var(--nx-mint-soft) !important;
            color: var(--nx-forest) !important;
            border: 1px solid var(--nx-sage) !important;
            border-radius: 14px !important;
            font-weight: 600;
        }

        .alert-danger {
            background-color: var(--nx-soft-red) !important;
            color: #9E2A2F !important;
            border: 1px solid #F5C2C5 !important;
            border-radius: 14px !important;
            font-weight: 600;
        }

        /* Text Overrides for Legacy Dark Classes */
        .text-white,
        .text-light {
            color: var(--nx-text) !important;
        }

        .btn .text-white,
        .badge.text-white,
        .bg-primary .text-white,
        .bg-success .text-white,
        .bg-danger .text-white,
        .nx-btn-primary .text-white {
            color: #FFFFFF !important;
        }

        .text-muted,
        .text-secondary {
            color: var(--nx-muted) !important;
        }

        /* Mobile Drawer */
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
            .nx-page-header {
                padding: 18px 20px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    @php
        $useInternalShell = Auth::check()
            && !request()->routeIs('dashboard', 'login', 'register', 'password.*')
            && !request()->is('/');
    @endphp

    @if($useInternalShell)
        @php
            $authUser = Auth::user();
            $isWargaAuth = $authUser->isWarga();
            $isAdminRWAuth = $authUser->isAdminRw();
            $isAdminRTAuth = $authUser->isAdminRt();
            $firstNameAuth = explode(' ', trim($authUser->name))[0] ?? $authUser->name;
            $roleLabelAuth = $isAdminRWAuth
                ? 'Admin RW 018'
                : ($isAdminRTAuth
                    ? 'Admin RT ' . ($authUser->rt_number ?? '-')
                    : ($authUser->isBendahara() ? 'Bendahara' : 'Warga RT ' . ($authUser->rt_number ?? '-')));

            $suratPendingCount = 0;
            $pengaduanBaruCount = 0;
            try {
                if (class_exists(\App\Models\SuratPengajuan::class)) {
                    $suratQ = \App\Models\SuratPengajuan::query();
                    if ($isAdminRTAuth) {
                        $suratQ->where('rt_number', $authUser->rt_number);
                    } elseif ($isWargaAuth) {
                        $suratQ->where('user_id', $authUser->id);
                    }
                    $suratPendingCount = $suratQ->whereIn('status', ['pending_rt', 'pending_rw'])->count();
                }
                if (class_exists(\App\Models\Pengaduan::class)) {
                    $pengaduanQ = \App\Models\Pengaduan::query();
                    if ($isAdminRTAuth) {
                        $pengaduanQ->where('rt_number', $authUser->rt_number);
                    } elseif ($isWargaAuth) {
                        $pengaduanQ->where('user_id', $authUser->id);
                    }
                    $pengaduanBaruCount = $pengaduanQ->whereIn('status', ['pending', 'menunggu'])->count();
                }
            } catch (\Throwable $e) {
                $suratPendingCount = 0;
                $pengaduanBaruCount = 0;
            }
            $shellNotifCount = $suratPendingCount + $pengaduanBaruCount;
        @endphp

        <div class="nx-shell" id="nxShell">
            <div class="nx-mobile-backdrop" id="nxBackdrop"></div>

            <!-- ==================== A. SIDEBAR NAVIGATION ==================== -->
            <aside class="nx-sidebar" aria-label="Sidebar Navigasi NexaNest">
                <div class="nx-brand">
                    <a href="{{ route('dashboard') }}" class="nx-brand-link">
                        <span class="nx-brand-logo">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none">
                                <path d="M3 10.5L12 3L21 10.5V20C21 20.5523 20.5523 21 20 21H4C3.44772 21 3 20.5523 3 20V10.5Z" stroke="#285943" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M9 21V14C9 12.8954 9.89543 12 11 12H13C14.1046 12 15 12.8954 15 14V21" stroke="#285943" stroke-width="2"/>
                                <path d="M13.5 8.5C15.5 7 18 8 18 10.5C16 11.5 13.5 10.5 13.5 8.5Z" fill="#285943"/>
                            </svg>
                        </span>
                        <div class="nx-brand-text">
                            <h1 class="nx-brand-title">NexaNest</h1>
                            <p class="nx-brand-sub">Smart &amp; Green Community</p>
                        </div>
                    </a>
                    <button type="button" class="nx-collapse-btn d-none d-lg-inline-flex" id="nxCollapseBtn" title="Perkecil / Perlebar Sidebar">
                        <i class="fas fa-angles-left"></i>
                    </button>
                </div>

                <!-- KELOMPOK 1: MENU UTAMA -->
                <div class="nx-menu-group-label">Menu Utama</div>
                <ul class="nx-nav-list">
                    <li class="nx-nav-item">
                        <a href="{{ route('dashboard') }}" class="nx-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="fas fa-border-all"></i>
                            <span class="nx-nav-text">Dashboard</span>
                        </a>
                    </li>
                    @if(!$isWargaAuth)
                    <li class="nx-nav-item">
                        <a href="{{ route('warga.index') }}" class="nx-nav-link {{ request()->routeIs('warga.*') ? 'active' : '' }}">
                            <i class="fas fa-users"></i>
                            <span class="nx-nav-text">Data Warga</span>
                        </a>
                    </li>
                    <li class="nx-nav-item">
                        <a href="{{ route('pembayaran.index') }}" class="nx-nav-link {{ request()->routeIs('pembayaran.*') ? 'active' : '' }}">
                            <i class="fas fa-wallet"></i>
                            <span class="nx-nav-text">Transaksi Kas</span>
                        </a>
                    </li>
                    <li class="nx-nav-item">
                        <a href="{{ route('laporan.index') }}" class="nx-nav-link {{ request()->routeIs('laporan.index') ? 'active' : '' }}">
                            <i class="fas fa-chart-column"></i>
                            <span class="nx-nav-text">Laporan Bulanan</span>
                        </a>
                    </li>
                    @endif
                    <li class="nx-nav-item">
                        <a href="{{ route('laporan.iuran') }}" class="nx-nav-link {{ request()->routeIs('laporan.iuran*') ? 'active' : '' }}">
                            <i class="fas fa-file-invoice-dollar"></i>
                            <span class="nx-nav-text">{{ $isWargaAuth ? 'Riwayat Iuran Saya' : 'Laporan Iuran' }}</span>
                        </a>
                    </li>
                </ul>

                <!-- KELOMPOK 2: LAYANAN WARGA -->
                <div class="nx-menu-group-label">Layanan Warga</div>
                <ul class="nx-nav-list">
                    <li class="nx-nav-item">
                        <a href="{{ route('surat.index') }}" class="nx-nav-link {{ request()->routeIs('surat.*') ? 'active' : '' }}">
                            <i class="fas fa-file-signature"></i>
                            <span class="nx-nav-text">Penerbitan Surat</span>
                            @if($suratPendingCount > 0)
                                <span class="nx-nav-badge">{{ $suratPendingCount }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nx-nav-item">
                        <a href="{{ route('pengaduan.index') }}" class="nx-nav-link {{ (request()->routeIs('pengaduan.*') && !request()->has('scan')) ? 'active' : '' }}">
                            <i class="fas fa-comment-dots"></i>
                            <span class="nx-nav-text">Pengaduan Warga</span>
                            @if($pengaduanBaruCount > 0)
                                <span class="nx-nav-badge">{{ $pengaduanBaruCount }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nx-nav-item">
                        <a href="{{ route('posyandu.index') }}" class="nx-nav-link {{ request()->routeIs('posyandu.*') ? 'active' : '' }}">
                            <i class="fas fa-heart"></i>
                            <span class="nx-nav-text">Info Posyandu</span>
                        </a>
                    </li>
                    <li class="nx-nav-item">
                        <a href="{{ route('umkm.index') }}" class="nx-nav-link {{ request()->routeIs('umkm.*') ? 'active' : '' }}">
                            <i class="fas fa-store"></i>
                            <span class="nx-nav-text">Direktori UMKM</span>
                        </a>
                    </li>
                </ul>

                <!-- KELOMPOK 3: LINGKUNGAN & AKUN -->
                <div class="nx-menu-group-label">Lingkungan &amp; Profil</div>
                <ul class="nx-nav-list">
                    <li class="nx-nav-item">
                        <a href="{{ route('pengaduan.create', ['scan' => 1]) }}" class="nx-nav-link {{ request()->fullUrlIs('*scan=1*') ? 'active' : '' }}">
                            <i class="fas fa-leaf"></i>
                            <span class="nx-nav-text">Laporan Lingkungan (QR)</span>
                        </a>
                    </li>
                    <li class="nx-nav-item">
                        <a href="#modalEdukasiLingkunganGlobal" data-bs-toggle="modal" class="nx-nav-link">
                            <i class="fas fa-seedling"></i>
                            <span class="nx-nav-text">Edukasi &amp; Bank Sampah</span>
                        </a>
                    </li>
                    <li class="nx-nav-item">
                        <a href="{{ route('profile.edit') }}" class="nx-nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                            <i class="fas fa-user-check"></i>
                            <span class="nx-nav-text">Profil Saya</span>
                        </a>
                    </li>
                </ul>

                <!-- FOOTER SIDEBAR -->
                <div class="nx-sidebar-footer">
                    <button type="button" class="nx-emergency-btn" data-bs-toggle="modal" data-bs-target="#modalDaruratGlobal">
                        <i class="fas fa-bell"></i>
                        <span class="nx-emergency-text">Tombol Darurat</span>
                    </button>

                    <a href="{{ route('logout') }}" class="nx-logout-btn"
                       onclick="event.preventDefault(); document.getElementById('global-logout-form').submit();">
                        <i class="fas fa-arrow-right-from-bracket"></i>
                        <span class="nx-nav-text">Keluar</span>
                    </a>
                    <form id="global-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </aside>

            <!-- ==================== B & C. MAIN CONTENT AREA ==================== -->
            <main class="nx-main">
                <!-- TOP NAVIGATION BAR -->
                <header class="nx-topbar">
                    <div class="d-flex align-items-center gap-2 flex-grow-1">
                        <button type="button" class="nx-icon-btn d-lg-none" id="nxMobileMenuBtn" aria-label="Buka Menu">
                            <i class="fas fa-bars"></i>
                        </button>

                        <div class="nx-search-wrap">
                            <i class="fas fa-search nx-search-icon"></i>
                            <input type="text" id="nxQuickSearch" class="nx-search-input"
                                   placeholder="Cari fitur atau halaman... (Ctrl + K)" autocomplete="off">
                            <span class="nx-search-kbd d-none d-sm-inline-block">Ctrl + K</span>

                            <div class="nx-search-results" id="nxSearchResults">
                                <a href="{{ route('dashboard') }}" class="nx-search-item" data-keywords="dashboard beranda ringkasan">
                                    <i class="fas fa-border-all text-success"></i> Dashboard Utama
                                </a>
                                <a href="{{ route('surat.index') }}" class="nx-search-item" data-keywords="surat pengantar domisili sktm tte">
                                    <i class="fas fa-file-signature text-success"></i> Penerbitan &amp; Pengajuan Surat
                                </a>
                                <a href="{{ route('pengaduan.index') }}" class="nx-search-item" data-keywords="pengaduan laporan keluhan kerusakan qr lingkungan">
                                    <i class="fas fa-comment-dots text-primary"></i> Pengaduan Warga &amp; Lingkungan
                                </a>
                                <a href="{{ route('laporan.iuran') }}" class="nx-search-item" data-keywords="iuran tagihan bayar kas lunas">
                                    <i class="fas fa-file-invoice-dollar text-success"></i> {{ $isWargaAuth ? 'Riwayat Iuran Saya' : 'Laporan Iuran Warga' }}
                                </a>
                                <a href="{{ route('posyandu.index') }}" class="nx-search-item" data-keywords="posyandu kesehatan balita ibu hamil jadwal">
                                    <i class="fas fa-heart text-danger"></i> Informasi &amp; Jadwal Posyandu
                                </a>
                                <a href="{{ route('umkm.index') }}" class="nx-search-item" data-keywords="umkm usaha toko kuliner belanja">
                                    <i class="fas fa-store text-warning"></i> Direktori UMKM Warga
                                </a>
                                @if(!$isWargaAuth)
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
                    </div>

                    <div class="nx-topbar-actions">
                        <div class="dropdown">
                            <button class="nx-icon-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Notifikasi">
                                <i class="far fa-bell"></i>
                                @if($shellNotifCount > 0)
                                    <span class="nx-notif-badge">{{ $shellNotifCount }}</span>
                                @endif
                            </button>
                            <div class="dropdown-menu dropdown-menu-end p-3 border-0 shadow-lg" style="width: 310px; border-radius: 18px; background: #fff;">
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                    <span class="fw-bold" style="color: var(--nx-text); font-size: 0.9rem;">Notifikasi Layanan</span>
                                    <span class="badge rounded-pill" style="background: var(--nx-mint); color: var(--nx-forest);">{{ $shellNotifCount }} Aktif</span>
                                </div>
                                <div class="d-flex flex-column gap-2">
                                    @if($suratPendingCount > 0)
                                        <a href="{{ route('surat.index', ['filter' => 'pending']) }}" class="text-decoration-none p-2 rounded-3" style="background: var(--nx-mint-soft); color: var(--nx-text); font-size: 0.82rem;">
                                            <i class="fas fa-file-signature text-success me-2"></i>
                                            <strong>{{ $suratPendingCount }} Surat</strong> sedang diproses / menunggu persetujuan.
                                        </a>
                                    @endif
                                    @if($pengaduanBaruCount > 0)
                                        <a href="{{ route('pengaduan.index') }}" class="text-decoration-none p-2 rounded-3" style="background: var(--nx-powder-soft); color: var(--nx-text); font-size: 0.82rem;">
                                            <i class="fas fa-exclamation-circle text-primary me-2"></i>
                                            <strong>{{ $pengaduanBaruCount }} Laporan Warga</strong> menunggu tindak lanjut.
                                        </a>
                                    @endif
                                    @if($shellNotifCount === 0)
                                        <div class="text-center py-3 text-muted small">
                                            <i class="fas fa-check-circle text-success mb-1 d-block fa-lg"></i>
                                            Semua layanan &amp; administrasi sudah tertangani.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="dropdown">
                            <a href="#" class="nx-profile-btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="{{ $authUser->profile_photo_url }}" alt="{{ $authUser->name }}" class="nx-profile-avatar">
                                <div class="d-none d-sm-block text-start me-1">
                                    <div class="fw-bold" style="font-size: 0.85rem; line-height: 1.15; color: var(--nx-text);">
                                        Hello, {{ $firstNameAuth }}!
                                    </div>
                                    <small style="font-size: 0.72rem; color: var(--nx-muted); font-weight: 600;">
                                        {{ $roleLabelAuth }}
                                    </small>
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2" style="border-radius: 16px; background: #fff; min-width: 210px;">
                                <li class="px-3 py-2 border-bottom mb-1">
                                    <div class="fw-bold small" style="color: var(--nx-text);">{{ $authUser->name }}</div>
                                    <div class="text-muted" style="font-size: 0.73rem;">{{ $authUser->email }}</div>
                                </li>
                                <li>
                                    <a class="dropdown-item rounded-3 py-2 small fw-semibold" href="{{ route('dashboard') }}" style="color: var(--nx-text);">
                                        <i class="fas fa-border-all me-2 text-success"></i>Dashboard Utama
                                    </a>
                                </li>
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

                <!-- FLASH MESSAGES -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- PAGE CONTENT -->
                <div class="nx-content-body">
                    @yield('content')
                </div>
            </main>
        </div>

        <!-- MODAL DARURAT GLOBAL -->
        <div class="modal fade" id="modalDaruratGlobal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
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
                        <div class="d-flex flex-column gap-2">
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

        <!-- MODAL EDUKASI LINGKUNGAN GLOBAL -->
        <div class="modal fade" id="modalEdukasiLingkunganGlobal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold" style="color: var(--nx-forest);">
                            <i class="fas fa-seedling me-2"></i>Smart &amp; Green Community RW 018
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="p-3 rounded-4 mb-3" style="background: var(--nx-mint-soft); border: 1px solid var(--nx-sage);">
                            <h6 class="fw-bold mb-1" style="color: var(--nx-forest);"><i class="fas fa-recycle me-1"></i>Program Lingkungan &amp; Bank Sampah</h6>
                            <p class="small text-muted mb-0">
                                Mari bersama menjaga kebersihan blok rumah, memilah sampah dari sumbernya, dan melaporkan titik fasilitas lingkungan yang membutuhkan perawatan.
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
                        <a href="{{ route('pengaduan.create', ['scan' => 1]) }}" class="btn btn-primary btn-sm px-3">
                            Laporkan Masalah Kebersihan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="nexa-dashboard-root">
            @yield('content')
        </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: @json(session('success')),
                timer: 2800,
                showConfirmButton: false,
                background: '#FFFFFF',
                color: '#263449',
                iconColor: '#285943'
            });
        </script>
    @endif

    @if($useInternalShell)
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const shell = document.getElementById('nxShell');
            const collapseBtn = document.getElementById('nxCollapseBtn');
            const mobileMenuBtn = document.getElementById('nxMobileMenuBtn');
            const backdrop = document.getElementById('nxBackdrop');

            if (shell && localStorage.getItem('nx_sidebar_collapsed') === '1') {
                shell.classList.add('sidebar-collapsed');
            }

            if (collapseBtn && shell) {
                collapseBtn.addEventListener('click', function () {
                    shell.classList.toggle('sidebar-collapsed');
                    localStorage.setItem('nx_sidebar_collapsed', shell.classList.contains('sidebar-collapsed') ? '1' : '0');
                });
            }

            if (mobileMenuBtn && shell) {
                mobileMenuBtn.addEventListener('click', function () {
                    shell.classList.toggle('mobile-open');
                });
            }

            if (backdrop && shell) {
                backdrop.addEventListener('click', function () {
                    shell.classList.remove('mobile-open');
                });
            }

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
        });
        </script>
    @endif

    @stack('scripts')
</body>
</html>
