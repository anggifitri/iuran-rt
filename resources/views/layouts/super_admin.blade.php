<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NexaNest — Platform Control Center')</title>

    <!-- Anti-FOUC inline script: prevent white flash on page load in dark mode -->
    <script>
        (function() {
            try {
                var theme = localStorage.getItem('nexanest_theme');
                if (!theme) {
                    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                        theme = 'dark';
                    } else {
                        theme = 'light';
                    }
                }
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark');
                    document.documentElement.setAttribute('data-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    document.documentElement.setAttribute('data-theme', 'light');
                }
            } catch (e) {}
        })();
    </script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            /* Forest Green Signature */
            --nx-forest: #285943;
            --nx-forest-dark: #1E4633;
            --nx-forest-deep: #143023;
            --nx-sage: #A8C9B5;
            --nx-mint: #DCEBDD;
            --nx-mint-soft: #EEF6EF;
            
            /* Super Admin Champagne Gold Theme */
            --nx-gold: #D4AF37;
            --nx-gold-light: #FDE047;
            --nx-gold-soft: #FEF3C7;
            --nx-gold-deep: #B45309;
            --nx-gold-border: #FCD34D;
            --nx-gold-gradient: linear-gradient(135deg, #FFFBEB 0%, #FEF3C7 50%, #FDE68A 100%);
            
            --nx-powder: #DCEAF5;
            --nx-bg: #F4F7F5;
            --nx-white: #FFFFFF;
            --nx-text: #263449;
            --nx-muted: #64748B;
            --nx-border: #E2E8F0;
            --nx-card-border: #E5EBE6;
            --nx-danger: #EF4444;
            --nx-warning: #F59E0B;
            --nx-success: #10B981;
            --nx-shadow: 0 10px 30px rgba(30, 70, 51, 0.06);
            --nx-shadow-hover: 0 16px 36px rgba(30, 70, 51, 0.12);
        }

        html.dark {
            --nx-bg: #0A0F1E;
            --nx-white: #131B2E;
            --nx-text: #F1F5F9;
            --nx-muted: #64748B;
            --nx-border: #1E2D4A;
            --nx-card-border: #1E2D4A;
            --nx-forest: #38BDF8;
            --nx-forest-dark: #0284C7;
            --nx-forest-deep: #0C4A6E;
            --nx-mint: #172554;
            --nx-mint-soft: rgba(56, 189, 248, 0.12);
            --nx-gold-soft: rgba(245, 158, 11, 0.16);
            --nx-gold-gradient: linear-gradient(135deg, #0F172A 0%, #172554 55%, #1E3A8A 100%);
        }

        body {
            background: var(--nx-bg);
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            color: var(--nx-text);
            margin: 0;
            overflow-x: hidden;
        }

        .sa-shell {
            display: flex;
            min-height: 100vh;
        }

        /* SUPER ADMIN SIDEBAR */
        .sa-sidebar {
            width: 280px;
            background: #FFFFFF;
            border-right: 1px solid var(--nx-border);
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            flex-shrink: 0;
            z-index: 1040;
            padding: 20px 16px;
            box-shadow: 4px 0 24px rgba(40, 89, 67, 0.04);
        }

        .sa-sidebar::-webkit-scrollbar {
            width: 4px;
        }
        .sa-sidebar::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 10px;
        }

        .sa-brand {
            padding: 6px 8px 18px;
            border-bottom: 1px solid var(--nx-border);
            margin-bottom: 16px;
        }

        .sa-brand-link {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .sa-crown-badge {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: linear-gradient(135deg, #1E4633 0%, #285943 60%, #856118 100%);
            border: 2px solid var(--nx-gold);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #FFD700;
            font-size: 1.3rem;
            box-shadow: 0 4px 14px rgba(212, 175, 55, 0.35);
            flex-shrink: 0;
        }

        .sa-brand-title {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--nx-forest);
            letter-spacing: -0.02em;
            line-height: 1.15;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .sa-brand-tag {
            font-size: 0.65rem;
            font-weight: 800;
            background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%);
            color: #92400E;
            border: 1px solid #FCD34D;
            padding: 2px 6px;
            border-radius: 6px;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .sa-brand-sub {
            font-size: 0.72rem;
            color: var(--nx-muted);
            margin: 2px 0 0;
            font-weight: 600;
        }

        /* ROLE SWITCHER PROMINENT CARD IN SIDEBAR */
        .sa-switcher-card {
            background: linear-gradient(135deg, #1A3E2D 0%, #285943 100%);
            border: 1.5px solid #D4AF37;
            border-radius: 16px;
            padding: 14px;
            color: #FFFFFF;
            margin-bottom: 20px;
            box-shadow: 0 6px 18px rgba(30, 70, 51, 0.15);
        }

        .sa-switcher-card .btn-switch-trigger {
            background: linear-gradient(135deg, #FFE082 0%, #FFB300 100%);
            border: 1px solid #FFD54F;
            color: #452600;
            font-weight: 700;
            font-size: 0.78rem;
            border-radius: 10px;
            width: 100%;
            padding: 8px 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .sa-switcher-card .btn-switch-trigger:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(255, 179, 0, 0.35);
        }

        /* NAVIGATION MENU */
        .sa-menu-label {
            font-size: 0.68rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #94A3B8;
            padding: 8px 12px 4px;
            margin-top: 6px;
        }

        .sa-nav-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sa-nav-item {
            margin-bottom: 3px;
        }

        .sa-nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 9px 12px;
            border-radius: 12px;
            color: #475569;
            font-size: 0.86rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .sa-nav-link i {
            width: 22px;
            text-align: center;
            font-size: 0.95rem;
            color: #64748B;
            transition: color 0.2s ease;
        }

        .sa-nav-link:hover {
            background: var(--nx-mint-soft);
            color: var(--nx-forest);
        }
        .sa-nav-link:hover i {
            color: var(--nx-forest);
        }

        .sa-nav-link.active {
            background: #EAF4EE;
            color: var(--nx-forest);
            font-weight: 700;
            position: relative;
        }
        .sa-nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 6px;
            bottom: 6px;
            width: 4px;
            background: var(--nx-forest);
            border-radius: 0 4px 4px 0;
        }
        .sa-nav-link.active i {
            color: var(--nx-forest);
        }

        .sa-nav-badge {
            margin-left: auto;
            font-size: 0.68rem;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 12px;
        }

        /* MAIN CONTENT AREA */
        .sa-main {
            flex: 1;
            min-width: 0;
            padding: 22px 30px 50px;
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        /* TOPBAR */
        .sa-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            background: #FFFFFF;
            padding: 12px 22px;
            border-radius: 18px;
            border: 1px solid var(--nx-border);
            box-shadow: var(--nx-shadow);
        }

        .sa-topbar-title {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sa-topbar-title h1 {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--nx-forest);
            margin: 0;
        }

        .sa-topbar-title .badge-crown {
            background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%);
            color: #92400E;
            border: 1px solid #FCD34D;
            font-size: 0.72rem;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .sa-system-pill {
            background: #ECFDF5;
            color: #065F46;
            border: 1px solid #A7F3D0;
            border-radius: 999px;
            padding: 5px 14px;
            font-size: 0.78rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .sa-system-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #10B981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.25);
            animation: pulse-dot 2s infinite;
        }

        @keyframes pulse-dot {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.5); }
            70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        /* CARDS & UI COMPONENTS */
        .sa-card {
            background: #FFFFFF;
            border: 1px solid var(--nx-card-border);
            border-radius: 20px;
            padding: 24px;
            box-shadow: var(--nx-shadow);
            transition: all 0.2s ease;
        }

        .sa-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 14px;
            border-bottom: 1px solid #F1F5F9;
        }

        .sa-card-title {
            font-size: 1.02rem;
            font-weight: 800;
            color: var(--nx-text);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sa-kpi-card {
            border-radius: 18px;
            padding: 20px;
            border: 1px solid var(--nx-border);
            background: #FFFFFF;
            box-shadow: var(--nx-shadow);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            gap: 12px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .sa-kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--nx-shadow-hover);
        }

        .sa-kpi-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .sa-kpi-val {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--nx-text);
            line-height: 1;
            margin: 0;
        }

        .sa-kpi-label {
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--nx-muted);
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin: 0;
        }

        /* TABLES */
        .sa-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .sa-table th {
            background: #F8FAFC;
            font-size: 0.74rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748B;
            padding: 12px 16px;
            border-bottom: 1px solid #E2E8F0;
        }

        .sa-table td {
            padding: 14px 16px;
            font-size: 0.85rem;
            vertical-align: middle;
            border-bottom: 1px solid #F1F5F9;
            color: var(--nx-text);
        }

        .sa-table tr:hover td {
            background: #F8FAF9;
        }

        /* THEME TOGGLE BUTTON */
        .nx-theme-toggle-btn {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            background: var(--nx-bg);
            border: 1px solid var(--nx-border);
            color: var(--nx-text);
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

        html.dark .dark-icon-sun {
            display: inline-block !important;
        }

        html.dark .dark-icon-moon {
            display: none !important;
        }

        @media (max-width: 991.98px) {
            .sa-sidebar {
                position: fixed;
                left: 0;
                top: 0;
                bottom: 0;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .sa-shell.mobile-open .sa-sidebar {
                transform: translateX(0);
            }
            .sa-main {
                padding: 16px;
            }
        }

        @media (max-width: 639.98px) {
            .sa-topbar {
                padding: 10px 14px;
                gap: 8px;
                border-radius: 14px;
            }
            .sa-topbar-title h1 {
                font-size: 0.95rem !important;
                max-width: 140px !important;
            }
            .sa-kpi-card {
                padding: 12px;
                gap: 8px;
                border-radius: 14px;
            }
            .sa-kpi-icon {
                width: 32px;
                height: 32px;
                border-radius: 10px;
                font-size: 0.95rem;
            }
            .sa-kpi-label {
                font-size: 0.68rem;
                letter-spacing: 0.02em;
            }
            .sa-kpi-val {
                font-size: 1.15rem !important;
                line-height: 1.15;
            }
            .sa-kpi-sub {
                font-size: 0.68rem !important;
                line-height: 1.25;
            }
            .sa-hero-banner {
                padding: 16px !important;
                border-radius: 18px !important;
            }
            .sa-hero-title {
                font-size: 1.15rem !important;
            }
            .sa-hero-desc {
                font-size: 0.78rem !important;
                line-height: 1.45 !important;
            }
            .sa-hero-badges span {
                font-size: 0.65rem !important;
                padding: 3px 8px !important;
            }
            .sa-hero-actions button,
            .sa-hero-actions a {
                padding: 6px 12px !important;
                font-size: 0.75rem !important;
                border-radius: 10px !important;
            }
            .sa-table th {
                padding: 8px 10px;
                font-size: 0.68rem;
            }
            .sa-table td {
                padding: 10px;
                font-size: 0.78rem;
            }
        }
    </style>
    @stack('styles')
    <link rel="stylesheet" href="{{ asset('css/nexanest-dark-mode.css') }}?v={{ filemtime(public_path('css/nexanest-dark-mode.css')) }}">
</head>
<body>
    @include('components.role-switch-banner')

    <div class="sa-shell" id="saShell">
        <!-- SIDEBAR -->
        <aside class="sa-sidebar">
            <div class="sa-brand">
                <a href="{{ route('super_admin.dashboard') }}" class="sa-brand-link">
                    <span class="sa-crown-badge">
                        <i class="fas fa-crown"></i>
                    </span>
                    <div>
                        <div class="sa-brand-title">
                            NexaNest <span class="sa-brand-tag">HQ</span>
                        </div>
                        <p class="sa-brand-sub">Platform Control Center</p>
                    </div>
                </a>
            </div>

            <!-- ==================== 1. PLATFORM OVERVIEW ==================== -->
            <div class="sa-menu-label">Platform Overview</div>
            <ul class="sa-nav-list">
                <li class="sa-nav-item">
                    <a href="{{ route('super_admin.dashboard') }}" class="sa-nav-link {{ request()->routeIs('super_admin.dashboard', 'super_admin.audit.*') ? 'active' : '' }}">
                        <i class="fas fa-gauge-high"></i>
                        <span>Dashboard Platform</span>
                    </a>
                </li>
                <li class="sa-nav-item">
                    <a href="{{ route('super_admin.monitoring') }}" class="sa-nav-link {{ request()->routeIs('super_admin.monitoring*') ? 'active' : '' }}">
                        <i class="fas fa-server"></i>
                        <span>Monitoring Sistem</span>
                    </a>
                </li>
            </ul>

            <!-- ==================== 2. TATA KELOLA RW 018 ==================== -->
            <div class="sa-menu-label">Tata Kelola RW 018</div>
            <ul class="sa-nav-list">
                <li class="sa-nav-item">
                    <a href="{{ route('super_admin.wilayah.index') }}" class="sa-nav-link {{ request()->routeIs('super_admin.wilayah.*', 'super_admin.pengajuan.*') ? 'active' : '' }}">
                        <i class="fas fa-landmark"></i>
                        <span>Manajemen Wilayah</span>
                        <span class="sa-nav-badge bg-success-subtle text-success">RW 018</span>
                    </a>
                </li>
                <li class="sa-nav-item">
                    <a href="{{ route('super_admin.accounts.index') }}" class="sa-nav-link {{ request()->routeIs('super_admin.accounts.*', 'super_admin.sensitive_logs') ? 'active' : '' }}">
                        <i class="fas fa-users-gear"></i>
                        <span>Manajemen Akun &amp; Akses</span>
                    </a>
                </li>
                <li class="sa-nav-item">
                    <a href="{{ route('smart_gate.index') }}" class="sa-nav-link {{ request()->routeIs('smart_gate.*') ? 'active' : '' }}">
                        <i class="fas fa-torii-gate"></i>
                        <span>Smart Gate RW 018</span>
                        <span class="sa-nav-badge bg-info-subtle text-info">Live</span>
                    </a>
                </li>
            </ul>

            <!-- ==================== 3. SISTEM & PROFIL ==================== -->
            <div class="sa-menu-label">Sistem &amp; Profil</div>
            <ul class="sa-nav-list">
                <li class="sa-nav-item">
                    <a href="{{ route('super_admin.settings.index') }}" class="sa-nav-link {{ request()->routeIs('super_admin.settings.*', 'super_admin.modules.*', 'super_admin.notifications.*', 'super_admin.backup.*') ? 'active' : '' }}">
                        <i class="fas fa-sliders"></i>
                        <span>Pengaturan Platform</span>
                    </a>
                </li>
                <li class="sa-nav-item">
                    <a href="{{ route('iot.index') }}" class="sa-nav-link {{ request()->routeIs('iot.*') ? 'active' : '' }}">
                        <i class="fas fa-microchip"></i>
                        <span>Pengaturan API &amp; IoT</span>
                        <span class="sa-nav-badge bg-primary-subtle text-primary">Smart Gate</span>
                    </a>
                </li>
                <li class="sa-nav-item">
                    <a href="{{ route('super_admin.profile.index') }}" class="sa-nav-link {{ request()->routeIs('super_admin.profile.*') ? 'active' : '' }}">
                        <i class="fas fa-id-badge"></i>
                        <span>Profil Super Admin</span>
                    </a>
                </li>
            </ul>

            <!-- FOOTER PROFILE & LOGOUT -->
            <div class="mt-auto pt-3 border-top">
                <div class="d-flex align-items-center justify-content-between p-2 rounded-3" style="background: #F8FAFC;">
                    <a href="{{ route('super_admin.profile.index') }}" class="d-flex align-items-center gap-2 overflow-hidden text-decoration-none" title="Buka Profil Super Admin">
                        <img src="{{ Auth::user()->profile_photo_url }}" alt="Super Admin" class="rounded-circle" style="width: 34px; height: 34px; border: 2px solid var(--nx-gold); object-fit: cover;">
                        <div class="overflow-hidden">
                            <strong class="d-block text-truncate small" style="color: var(--nx-text);">{{ Auth::user()->display_name }}</strong>
                            <span class="d-block text-truncate text-muted" style="font-size: 0.70rem;">{{ Auth::user()->email }}</span>
                        </div>
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-link text-danger p-1" title="Keluar">
                            <i class="fas fa-power-off"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- MAIN CONTAINER -->
        <main class="sa-main">
            <!-- TOPBAR -->
            <header class="sa-topbar">
                <div class="sa-topbar-title">
                    <button type="button" class="btn btn-sm btn-light d-lg-none" onclick="document.getElementById('saShell').classList.toggle('mobile-open')">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div>
                        <h1 class="text-truncate sa-page-title" style="max-width: 220px;">@yield('page_title', 'Platform Control Center')</h1>
                    </div>
                    <span class="badge-crown d-none d-sm-inline-flex">
                        <i class="fas fa-crown text-warning"></i> Super Admin
                    </span>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <div class="sa-system-pill d-none d-md-inline-flex">
                        <span class="sa-system-dot"></span>
                        <span>Multi-Tenant Engine Online</span>
                    </div>

                    <!-- Role Switcher Trigger Button -->
                    <button type="button" class="btn btn-sm d-inline-flex align-items-center gap-2 px-3 py-2 fw-bold text-dark shadow-sm" style="background: linear-gradient(135deg, #FFE082 0%, #FFB300 100%); border: 1px solid #FFD54F; border-radius: 10px; font-size: 0.82rem;" data-bs-toggle="modal" data-bs-target="#modalGlobalRoleSwitcher">
                        <i class="fas fa-crown"></i>
                        <span class="d-none d-sm-inline">Role Switcher</span>
                    </button>

                    <!-- Theme Toggle Mode Dark / Light -->
                    <button type="button" class="nx-theme-toggle-btn" id="nxThemeToggleBtn" title="Beralih Tema (Dark/Light)">
                        <i class="far fa-moon dark-icon-moon"></i>
                        <i class="far fa-sun dark-icon-sun"></i>
                    </button>

                    <!-- Operational Dashboard Shortcut -->
                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1 px-3 py-2 fw-semibold" style="border-radius: 10px; font-size: 0.82rem;" title="Lihat Dashboard Lingkungan RW 018">
                        <i class="fas fa-compass"></i>
                        <span class="d-none d-md-inline">Lihat Lingkungan</span>
                    </a>
                </div>
            </header>

            <!-- MODERN FLOATING TOAST NOTIFICATIONS (SUPER ADMIN) -->
            <x-nexa-toast />

            @yield('content')
        </main>
    </div>

    <!-- ROLE SWITCHER MODAL -->
    @include('components.role-switch-modal')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.toggleNexaTheme = function() {
            var isDark = document.documentElement.classList.contains('dark');
            var newTheme = isDark ? 'light' : 'dark';
            if (newTheme === 'dark') {
                document.documentElement.classList.add('dark');
                document.documentElement.setAttribute('data-theme', 'dark');
            } else {
                document.documentElement.classList.remove('dark');
                document.documentElement.setAttribute('data-theme', 'light');
            }
            try {
                localStorage.setItem('nexanest_theme', newTheme);
            } catch(e) {}
            window.dispatchEvent(new CustomEvent('nexanest-theme-changed', { detail: { theme: newTheme } }));
        };

        window.addEventListener('nexanest-theme-changed', function(e) {
            var isDark = e.detail.theme === 'dark';
            if (typeof Chart !== 'undefined') {
                try {
                    Chart.defaults.color = isDark ? '#94A3B8' : '#718096';
                    Chart.defaults.borderColor = isDark ? 'rgba(148, 163, 184, 0.12)' : 'rgba(226, 232, 240, 0.8)';
                    if (Chart.instances) {
                        Object.values(Chart.instances).forEach(function(chart) {
                            if (chart && chart.options) {
                                if (chart.options.scales) {
                                    Object.values(chart.options.scales).forEach(function(scale) {
                                        if (scale.ticks) scale.ticks.color = isDark ? '#94A3B8' : '#718096';
                                        if (scale.grid) scale.grid.color = isDark ? 'rgba(148, 163, 184, 0.12)' : 'rgba(226, 232, 240, 0.8)';
                                    });
                                }
                                if (chart.options.plugins && chart.options.plugins.legend && chart.options.plugins.legend.labels) {
                                    chart.options.plugins.legend.labels.color = isDark ? '#F1F5F9' : '#263449';
                                }
                                chart.update();
                            }
                        });
                    }
                } catch(err) {}
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            var isDark = document.documentElement.classList.contains('dark');
            if (typeof Chart !== 'undefined' && isDark) {
                try {
                    Chart.defaults.color = '#94A3B8';
                    Chart.defaults.borderColor = 'rgba(148, 163, 184, 0.12)';
                } catch(err) {}
            }

            var toggleBtns = document.querySelectorAll('#nxThemeToggleBtn, .nx-theme-toggle-btn');
            toggleBtns.forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.toggleNexaTheme();
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
