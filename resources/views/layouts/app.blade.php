<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NexaNest — Smart & Green Community')</title>

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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=Kalam:wght@700&display=swap" rel="stylesheet">

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

        /* Utility Shims for Decimal Spacing */
        .p-2\.5 { padding: 0.75rem !important; }
        .p-3\.5 { padding: 1.15rem !important; }
        .p-4\.5 { padding: 1.5rem !important; }
        .py-2\.5 { padding-top: 0.65rem !important; padding-bottom: 0.65rem !important; }
        .px-2\.5 { padding-left: 0.75rem !important; padding-right: 0.75rem !important; }
        .px-3\.5 { padding-left: 1.15rem !important; padding-right: 1.15rem !important; }
        .pt-2\.5 { padding-top: 0.65rem !important; }
        .gap-1\.5 { gap: 0.5rem !important; }
        .gap-2\.5 { gap: 0.75rem !important; }

        .nx-card,
        .card,
        .card-hover,
        .stat-card {
            background: var(--nx-white) !important;
            border: 1px solid var(--nx-border) !important;
            border-radius: 18px !important;
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
            border-radius: 18px 18px 0 0 !important;
        }

        /* Standard Stat Card Component (NexaNest Modern) */
        .nx-stat-card {
            background: var(--nx-white) !important;
            border: 1px solid var(--nx-border) !important;
            border-radius: 16px !important;
            padding: 16px 18px !important;
            box-shadow: var(--nx-shadow) !important;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            min-height: 98px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .nx-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--nx-shadow-hover) !important;
        }

        /* Full surface clean cards - no vertical bar */
        .nx-stat-card::before {
            display: none !important;
            content: none !important;
        }

        .nx-stat-card--forest { --stat-accent: var(--nx-forest); }
        .nx-stat-card--success { --stat-accent: #10B981; }
        .nx-stat-card--warning { --stat-accent: #F59E0B; }
        .nx-stat-card--danger { --stat-accent: #EF4444; }
        .nx-stat-card--primary { --stat-accent: #3B82F6; }
        .nx-stat-card--indigo { --stat-accent: #6366F1; }
        .nx-stat-card--cyan { --stat-accent: #06B6D4; }
        .nx-stat-card--amber { --stat-accent: #D97706; }
        .nx-stat-card--muted { --stat-accent: #94A3B8; }

        .nx-stat-content {
            flex: 1;
            min-width: 0;
            padding-left: 0;
        }

        .nx-stat-label {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--nx-muted);
            display: block;
            margin-bottom: 3px;
            white-space: nowrap;
            overflow: hidden;
            text-truncate: ellipsis;
        }

        .nx-stat-value {
            font-size: 1.45rem;
            font-weight: 800;
            line-height: 1.2;
            color: var(--nx-text);
            margin: 0;
            letter-spacing: -0.02em;
        }

        .nx-stat-sub {
            font-size: 0.72rem;
            color: var(--nx-muted);
            margin-top: 3px;
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .nx-stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.15rem;
        }

        .nx-stat-icon--forest { background: var(--nx-mint-soft); color: var(--nx-forest); }
        .nx-stat-icon--success { background: #D1FAE5; color: #059669; }
        .nx-stat-icon--warning { background: #FEF3C7; color: #D97706; }
        .nx-stat-icon--danger { background: var(--nx-soft-red); color: var(--nx-danger); }
        .nx-stat-icon--primary { background: #DBEAFE; color: #1D4ED8; }
        .nx-stat-icon--indigo { background: #E0E7FF; color: #4338CA; }

        /* Compact KPI Stat Card (for 6-column grids such as Laporan Bulanan) */
        .nx-stat-card-kpi {
            padding: 12px 14px !important;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: stretch !important;
            gap: 4px !important;
            min-height: 94px !important;
        }

        .nx-stat-card-kpi .nx-stat-content {
            padding-left: 2px !important;
            width: 100%;
        }

        .nx-stat-card-kpi .nx-stat-label {
            font-size: 0.68rem !important;
            padding-right: 34px !important;
            line-height: 1.2;
        }

        .nx-stat-card-kpi .nx-stat-icon {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 32px !important;
            height: 32px !important;
            font-size: 0.88rem !important;
            border-radius: 8px !important;
        }

        .nx-stat-card-kpi .nx-stat-value {
            font-size: 1.05rem !important;
            letter-spacing: -0.015em !important;
            word-break: break-word;
            margin-top: 2px;
            line-height: 1.25;
        }

        .nx-stat-card-kpi .nx-stat-sub {
            font-size: 0.67rem !important;
            margin-top: 1px;
        }

        /* Standard Filter Card & Bar Component */
        .nx-filter-card {
            background: var(--nx-white) !important;
            border: 1px solid var(--nx-border) !important;
            border-radius: 16px !important;
            padding: 16px 20px !important;
            box-shadow: var(--nx-shadow) !important;
            margin-bottom: 22px;
        }

        .nx-filter-bar {
            display: flex;
            align-items: flex-end;
            gap: 12px;
            flex-wrap: wrap;
        }

        .nx-filter-card .form-control,
        .nx-filter-card .form-select,
        .nx-filter-card .input-group-text {
            height: 40px !important;
            font-size: 0.85rem !important;
            border-radius: 10px !important;
        }

        .nx-filter-card .btn {
            height: 40px !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px !important;
            font-size: 0.85rem !important;
            font-weight: 700 !important;
        }

        /* Standard Grid Card Component (For Facilities, Cameras, Categories, etc.) */
        .nx-grid-card {
            background: var(--nx-white) !important;
            border: 1px solid var(--nx-border) !important;
            border-radius: 16px !important;
            box-shadow: var(--nx-shadow) !important;
            padding: 18px 20px !important;
            display: flex !important;
            flex-direction: column !important;
            height: 100% !important;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .nx-grid-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--nx-shadow-hover) !important;
        }

        .nx-grid-card-body {
            flex: 1 1 auto;
            display: flex;
            flex-direction: column;
        }

        .nx-grid-card-footer {
            margin-top: auto;
            padding-top: 14px;
            border-top: 1px solid var(--nx-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
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

        /* ==================== STANDAR TABEL NEXANEST ==================== */
        .nx-table-card {
            background: var(--nx-white) !important;
            border: 1px solid var(--nx-border) !important;
            border-radius: 16px !important;
            box-shadow: var(--nx-shadow) !important;
            overflow: hidden;
        }

        .nx-table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table,
        .nx-table {
            color: var(--nx-text) !important;
            vertical-align: middle;
            margin-bottom: 0;
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table thead th,
        .nx-table thead th {
            background-color: var(--nx-mint-soft) !important;
            color: var(--nx-forest) !important;
            font-weight: 700;
            font-size: 0.76rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1.5px solid var(--nx-sage) !important;
            border-top: none !important;
            padding: 14px 18px;
            white-space: nowrap;
        }

        .table tbody td,
        .nx-table tbody td {
            background-color: transparent !important;
            color: var(--nx-text) !important;
            border-bottom: 1px solid var(--nx-border) !important;
            padding: 13px 18px;
            font-size: 0.86rem;
            line-height: 1.45;
        }

        .table-hover tbody tr:hover td,
        .nx-table tbody tr:hover td {
            background-color: #F4F9F5 !important;
        }

        /* Status Badges Standard */
        .nx-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 0.74rem;
            font-weight: 700;
            letter-spacing: 0.01em;
            line-height: 1.25;
            white-space: nowrap;
            border: 1px solid transparent;
        }

        .nx-badge-success {
            background-color: #E8F5E9 !important;
            color: #1E6B37 !important;
            border-color: #C8E6C9 !important;
        }

        .nx-badge-warning {
            background-color: #FEF3DF !important;
            color: #B45309 !important;
            border-color: #FDE68A !important;
        }

        .nx-badge-danger {
            background-color: #FCE8E9 !important;
            color: #B91C1C !important;
            border-color: #FECACA !important;
        }

        .nx-badge-info {
            background-color: #EBF4FB !important;
            color: #1E40AF !important;
            border-color: #BFDBFE !important;
        }

        .nx-badge-secondary {
            background-color: #F1F5F9 !important;
            color: #475569 !important;
            border-color: #E2E8F0 !important;
        }

        /* Empty State Standard */
        .nx-empty-state {
            padding: 48px 24px;
            text-align: center;
        }

        .nx-empty-icon {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            background: var(--nx-mint-soft);
            color: var(--nx-forest);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            margin-bottom: 16px;
            border: 2px solid var(--nx-sage);
        }

        /* Action Menu & Dropdown Standard */
        .nx-action-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid var(--nx-border);
            background: var(--nx-white);
            color: var(--nx-text);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.8rem;
        }

        .nx-action-btn:hover {
            background: var(--nx-mint-soft);
            color: var(--nx-forest);
            border-color: var(--nx-sage);
        }

        /* ==================== FULLCALENDAR NEXANEST MODERN THEME ==================== */
        .fc {
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif !important;
            color: var(--nx-text) !important;
        }

        .fc .fc-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px !important;
        }

        .fc .fc-toolbar-title {
            font-size: 1.18rem !important;
            font-weight: 800 !important;
            color: var(--nx-forest) !important;
            letter-spacing: -0.02em;
        }

        .fc .fc-button-primary {
            background: var(--nx-white) !important;
            color: var(--nx-forest) !important;
            border: 1.5px solid var(--nx-border) !important;
            font-weight: 700 !important;
            font-size: 0.82rem !important;
            border-radius: 10px !important;
            padding: 6px 14px !important;
            box-shadow: 0 2px 6px rgba(40, 89, 67, 0.04) !important;
            transition: all 0.2s ease !important;
            text-transform: capitalize;
        }

        .fc .fc-button-primary:hover,
        .fc .fc-button-primary:focus {
            background: var(--nx-mint-soft) !important;
            color: var(--nx-forest-dark) !important;
            border-color: var(--nx-sage) !important;
            box-shadow: 0 4px 10px rgba(40, 89, 67, 0.08) !important;
        }

        .fc .fc-button-primary:not(:disabled).fc-button-active,
        .fc .fc-button-primary:not(:disabled):active {
            background: var(--nx-forest) !important;
            color: #FFFFFF !important;
            border-color: var(--nx-forest) !important;
            box-shadow: 0 4px 12px rgba(40, 89, 67, 0.2) !important;
        }

        .fc .fc-button-group {
            border-radius: 10px !important;
            overflow: hidden;
        }

        /* Calendar Grid & Headers */
        .fc-theme-standard th {
            background: var(--nx-mint-soft) !important;
            border: 1px solid var(--nx-border) !important;
            padding: 10px 4px !important;
        }

        .fc .fc-col-header-cell-cushion {
            color: var(--nx-forest) !important;
            font-weight: 700 !important;
            font-size: 0.8rem !important;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            text-decoration: none !important;
            display: inline-block;
            padding: 4px 6px;
        }

        .fc-theme-standard td,
        .fc-theme-standard .fc-scrollgrid {
            border-color: var(--nx-border) !important;
        }

        /* Day Numbers - NO BLUE UNDERLINE */
        .fc .fc-daygrid-day-number {
            color: var(--nx-text) !important;
            font-weight: 700 !important;
            font-size: 0.84rem !important;
            text-decoration: none !important;
            padding: 6px 10px !important;
            display: inline-block;
            border-radius: 8px;
            transition: background 0.15s ease;
        }

        .fc .fc-daygrid-day-number:hover {
            color: var(--nx-forest) !important;
            text-decoration: none !important;
            background: var(--nx-mint-soft);
        }

        /* Today Highlight */
        .fc .fc-day-today {
            background: #F2F8F4 !important;
        }

        .fc .fc-day-today .fc-daygrid-day-number {
            background: var(--nx-forest) !important;
            color: #FFFFFF !important;
            border-radius: 50% !important;
            width: 28px !important;
            height: 28px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 4px 8px;
            padding: 0 !important;
        }

        /* Other Month Days */
        .fc .fc-day-other {
            background: #FAFCFA !important;
            opacity: 0.65;
        }

        .fc .fc-day-other .fc-daygrid-day-number {
            color: var(--nx-muted) !important;
        }

        /* Event Cards in Calendar */
        .fc-daygrid-event {
            border-radius: 8px !important;
            padding: 3px 6px !important;
            font-size: 0.74rem !important;
            font-weight: 600 !important;
            margin: 2px 3px !important;
            box-shadow: 0 2px 4px rgba(40, 89, 67, 0.08) !important;
            transition: transform 0.15s ease, box-shadow 0.15s ease !important;
            border: 1px solid transparent !important;
            cursor: pointer;
        }

        .fc-daygrid-event:hover {
            transform: translateY(-1px) scale(1.02);
            box-shadow: 0 4px 10px rgba(40, 89, 67, 0.15) !important;
        }

        .fc-daygrid-more-link {
            font-size: 0.72rem !important;
            font-weight: 700 !important;
            color: var(--nx-forest) !important;
            background: var(--nx-mint-soft) !important;
            padding: 2px 6px !important;
            border-radius: 6px !important;
            text-decoration: none !important;
            margin: 2px 4px !important;
            display: inline-block;
        }

        .fc-daygrid-more-link:hover {
            text-decoration: none !important;
            background: var(--nx-mint) !important;
        }

        .fc-popover {
            border-radius: 14px !important;
            border: 1px solid var(--nx-border) !important;
            box-shadow: var(--nx-shadow-hover) !important;
            background: var(--nx-white) !important;
            overflow: hidden;
            z-index: 1055 !important;
        }

        .fc-popover-header {
            background: var(--nx-mint-soft) !important;
            color: var(--nx-forest) !important;
            font-weight: 700 !important;
            padding: 8px 12px !important;
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

        /* Mobile Layout Ala GoPay (<= 768px) */
        @media (max-width: 768px) {
            body {
                overflow-x: hidden;
                box-sizing: border-box;
            }
            .nx-shell {
                overflow-x: hidden;
                max-width: 100vw;
            }
            .nx-main {
                padding: 12px !important;
                padding-bottom: 84px !important;
                overflow-x: hidden;
                max-width: 100vw;
            }
            .nx-topbar {
                padding: 10px 12px;
                gap: 8px;
            }
            .nx-page-title {
                font-size: clamp(0.85rem, 3.8vw, 1.05rem) !important;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 100%;
            }

            /* Bottom Navigation Bar ala GoPay */
            .nx-bottom-nav {
                display: flex !important;
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                height: 62px;
                background: rgba(255, 255, 255, 0.96);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                border-top: 1px solid rgba(0, 0, 0, 0.08);
                z-index: 1045;
                justify-content: space-around;
                align-items: center;
                padding: 4px 6px calc(env(safe-area-inset-bottom, 0px) + 4px);
                box-shadow: 0 -4px 18px rgba(0, 0, 0, 0.04);
            }
            html.dark .nx-bottom-nav {
                background: rgba(15, 23, 42, 0.96);
                border-top: 1px solid rgba(255, 255, 255, 0.08);
                box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.35);
            }
            .nx-bottom-nav-item {
                flex: 1;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-decoration: none;
                color: #64748b;
                padding: 4px 2px;
                border-radius: 8px;
                transition: all 0.15s ease;
                background: transparent;
                border: 0;
                cursor: pointer;
            }
            html.dark .nx-bottom-nav-item {
                color: #94a3b8;
            }
            .nx-bottom-nav-item i {
                font-size: 19px;
                margin-bottom: 2px;
                line-height: 1;
            }
            .nx-bottom-nav-item span {
                font-size: 10px;
                font-weight: 600;
                letter-spacing: -0.01em;
                line-height: 1.1;
            }
            .nx-bottom-nav-item.active {
                color: #1e4633;
                font-weight: 700;
            }
            html.dark .nx-bottom-nav-item.active {
                color: #38bdf8;
            }
            .nx-bottom-nav-item:active {
                transform: scale(0.93);
            }

            /* Quick Action Grid ala GoPay di Mobile Hero */
            .nx-hero-actions {
                display: grid !important;
                grid-template-columns: repeat(3, 1fr) !important;
                gap: 8px !important;
                width: 100% !important;
            }
            .nx-hero-actions button,
            .nx-hero-actions a,
            .nx-hero-actions form {
                width: 100% !important;
                margin: 0 !important;
            }
            .nx-hero-actions button,
            .nx-hero-actions a {
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                justify-content: center !important;
                text-align: center !important;
                padding: 10px 4px !important;
                gap: 5px !important;
                min-height: 64px;
            }
            .nx-hero-actions button i,
            .nx-hero-actions a i {
                font-size: 1.1rem !important;
            }
            .nx-hero-actions button span,
            .nx-hero-actions a span {
                font-size: 0.68rem !important;
                line-height: 1.15 !important;
                font-weight: 600;
            }
        }
        @media (min-width: 769px) {
            .nx-bottom-nav {
                display: none !important;
            }
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
    </style>
    @stack('styles')
    <link rel="stylesheet" href="{{ asset('css/nexanest-dark-mode.css') }}?v={{ filemtime(public_path('css/nexanest-dark-mode.css')) }}">
</head>
<body>
    @include('components.role-switch-banner')
    @php
        $useInternalShell = Auth::check()
            && !request()->routeIs('dashboard', 'login', 'register', 'password.*')
            && !request()->is('/');
    @endphp

    @if($useInternalShell)
        @php
            $authUser = Auth::user();
            $isAdminRWAuth = $authUser->isAdminRw();
            $isAdminRTAuth = $authUser->isAdminRt();
            $isWargaAuth = $authUser->isWarga();

            // Deteksi konteks simulasi Role Switcher Super Admin
            $isSimulatedAuth = $authUser->isSuperAdmin() && $authUser->isRoleSwitched();
            $simCtxAuth = $isSimulatedAuth ? $authUser->getRoleSwitchContext() : null;
            $simTargetName = $simCtxAuth['target_user_name'] ?? null;

            $authDisplayName = $simTargetName ?: $authUser->name;
            $firstNameAuth = explode(' ', trim($authDisplayName))[0] ?? $authDisplayName;
            $authProfilePhotoUrl = $authUser->profile_photo_url;

            if ($isSimulatedAuth) {
                $simRole = $simCtxAuth['target_role'] ?? 'warga';
                $simRt = $simCtxAuth['target_rt'] ?? null;
                $roleLabelAuth = match($simRole) {
                    'admin_rw' => 'Admin RW 018 (Simulasi)',
                    'admin_rt' => 'Admin RT ' . ($simRt ?? '-') . ' (Simulasi)',
                    'warga' => 'Warga RT ' . ($simRt ?? '-') . ' (Simulasi)',
                    default => strtoupper($simRole) . ' (Simulasi)'
                };
            } else {
                $roleLabelAuth = $isAdminRWAuth
                    ? 'Admin RW 018'
                    : ($isAdminRTAuth
                        ? 'Admin RT ' . ($authUser->rt_number ?? '-')
                        : ($authUser->isBendahara() ? 'Bendahara' : 'Warga RT ' . ($authUser->rt_number ?? '-')));
            }

            $suratPendingCount = 0;
            $pengaduanBaruCount = 0;
            $financeNotifCount = 0;
            $financeNotifs = collect();
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
                if (class_exists(\App\Models\FinanceNotification::class)) {
                    $fnQ = \App\Models\FinanceNotification::where('user_id', $authUser->id)->where('is_read', false);
                    $financeNotifCount = (clone $fnQ)->count();
                    $financeNotifs = $fnQ->latest()->limit(5)->get();
                }
            } catch (\Throwable $e) {
                $suratPendingCount = 0;
                $pengaduanBaruCount = 0;
                $financeNotifCount = 0;
                $financeNotifs = collect();
            }
            $shellNotifCount = $suratPendingCount + $pengaduanBaruCount + $financeNotifCount;
        @endphp

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

                @if($authUser && $authUser->isSuperAdmin())
                <div class="p-2 mb-3 rounded-3 text-white" style="background: linear-gradient(135deg, #1E4633 0%, #285943 60%, #785310 100%); border: 1.5px solid #D4AF37;">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="badge" style="background: #FFE082; color: #452600; font-size: 0.68rem; font-weight: 800;">
                            <i class="fas fa-crown"></i> SUPER ADMIN
                        </span>
                        <a href="{{ route('super_admin.dashboard') }}" class="text-white-50 text-decoration-none small" style="font-size: 0.70rem;">
                            Control Center &rarr;
                        </a>
                    </div>
                    <button type="button" class="btn btn-sm btn-gold btn-role-switcher-gold w-100 py-1 px-2 fw-bold d-flex align-items-center justify-content-between shadow-sm" style="background: linear-gradient(135deg, #FFE082 0%, #FFB300 100%) !important; font-size: 0.74rem; border-radius: 8px; color: #0F172A !important;" data-bs-toggle="modal" data-bs-target="#modalGlobalRoleSwitcher">
                        <span style="color: #0F172A !important;"><i class="fas fa-arrow-right-arrow-left me-1" style="color: #0F172A !important;"></i> Role Switcher</span>
                        <i class="fas fa-chevron-right" style="font-size: 0.65rem; color: #0F172A !important;"></i>
                    </button>
                </div>
                @endif

                <!-- KELOMPOK 1: DASHBOARD -->
                <div class="nx-menu-group-label">Dashboard</div>
                <ul class="nx-nav-list">
                    <li class="nx-nav-item">
                        <a href="{{ route('dashboard') }}" class="nx-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="fas fa-border-all"></i>
                            <span class="nx-nav-text">Dashboard</span>
                        </a>
                    </li>
                    @if($isAdminRWAuth)
                    <li class="nx-nav-item">
                        <a href="{{ route('monitoring.index') }}" class="nx-nav-link {{ request()->routeIs('monitoring.*') ? 'active' : '' }}">
                            <i class="fas fa-tower-observation"></i>
                            <span class="nx-nav-text">Monitoring Wilayah</span>
                        </a>
                    </li>
                    @endif
                </ul>

                <!-- KELOMPOK 2: ADMINISTRASI WARGA -->
                @if(!$isWargaAuth)
                <div class="nx-menu-group-label">Administrasi Warga</div>
                <ul class="nx-nav-list">
                    @if($isAdminRWAuth)
                    <li class="nx-nav-item">
                        <a href="{{ route('warga.index') }}" class="nx-nav-link {{ (request()->routeIs('warga.*') || request()->routeIs('verifikasi.*')) ? 'active' : '' }}">
                            <i class="fas fa-users-gear"></i>
                            <span class="nx-nav-text">Database Warga</span>
                        </a>
                    </li>
                    @else
                    <li class="nx-nav-item">
                        <a href="{{ route('warga.index') }}" class="nx-nav-link {{ request()->routeIs('warga.*') ? 'active' : '' }}">
                            <i class="fas fa-users"></i>
                            <span class="nx-nav-text">Data Warga</span>
                        </a>
                    </li>
                    <li class="nx-nav-item">
                        <a href="{{ route('verifikasi.index') }}" class="nx-nav-link {{ request()->routeIs('verifikasi.*') ? 'active' : '' }}">
                            <i class="fas fa-user-check"></i>
                            <span class="nx-nav-text">Verifikasi Warga</span>
                        </a>
                    </li>
                    @endif
                    <li class="nx-nav-item">
                        <a href="{{ route('mutasi.index') }}" class="nx-nav-link {{ request()->routeIs('mutasi.*') ? 'active' : '' }}">
                            <i class="fas fa-arrows-split-up-and-left"></i>
                            <span class="nx-nav-text">Mutasi Warga</span>
                        </a>
                    </li>
                </ul>
                @endif

                <!-- KELOMPOK 3: KEUANGAN DAN PELAYANAN -->
                <div class="nx-menu-group-label">Keuangan &amp; Pelayanan</div>
                <ul class="nx-nav-list">
                    @if(!$isWargaAuth)
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
                        <a href="{{ route('laporan.iuran') }}" class="nx-nav-link {{ request()->routeIs('laporan.iuran*') || request()->routeIs('iuran.*') ? 'active' : '' }}">
                            <i class="fas fa-file-invoice-dollar"></i>
                            <span class="nx-nav-text">{{ $isWargaAuth ? 'Iuran Warga Saya' : 'Iuran Warga' }}</span>
                        </a>
                    </li>
                    @if(!$isWargaAuth)
                    <li class="nx-nav-item">
                        <a href="{{ route('laporan_rt.index') }}" class="nx-nav-link {{ request()->routeIs('laporan_rt.*') ? 'active' : '' }}">
                            <i class="fas fa-clipboard-check"></i>
                            <span class="nx-nav-text">{{ $isAdminRWAuth ? 'Pemeriksaan Laporan RT' : 'Laporan Resmi RT' }}</span>
                        </a>
                    </li>
                    @endif
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
                        <a href="{{ route('pengaduan.index') }}" class="nx-nav-link {{ request()->routeIs('pengaduan.*') || request()->routeIs('aset.*') ? 'active' : '' }}">
                            <i class="{{ $isWargaAuth ? 'fas fa-comments' : 'fas fa-boxes-stacked' }}"></i>
                            <span class="nx-nav-text">{{ $isWargaAuth ? 'Pengaduan Warga' : 'Manajemen Aset & Pengaduan' }}</span>
                            @if($pengaduanBaruCount > 0)
                                <span class="nx-nav-badge">{{ $pengaduanBaruCount }}</span>
                            @endif
                        </a>
                    </li>
                </ul>

                <!-- KELOMPOK 4: LINGKUNGAN DAN KEAMANAN -->
                <div class="nx-menu-group-label">Lingkungan &amp; Keamanan</div>
                <ul class="nx-nav-list">
                    @if(!$isWargaAuth)
                    <li class="nx-nav-item">
                        <a href="{{ route('lingkungan.index') }}" class="nx-nav-link {{ request()->routeIs('lingkungan.*') ? 'active' : '' }}">
                            <i class="fas fa-leaf"></i>
                            <span class="nx-nav-text">Laporan Lingkungan</span>
                        </a>
                    </li>
                    @endif
                    <li class="nx-nav-item">
                        <a href="{{ route('bank_sampah.index') }}" class="nx-nav-link {{ request()->routeIs('bank_sampah.*') ? 'active' : '' }}">
                            <i class="fas fa-recycle"></i>
                            <span class="nx-nav-text">Edukasi &amp; Bank Sampah</span>
                        </a>
                    </li>
                    <li class="nx-nav-item">
                        <a href="{{ route('ronda.index') }}" class="nx-nav-link {{ request()->routeIs('ronda.*') ? 'active' : '' }}">
                            <i class="fas fa-shield-halved"></i>
                            <span class="nx-nav-text">Ronda &amp; Keamanan</span>
                        </a>
                    </li>
                    <li class="nx-nav-item">
                        <a href="{{ route('smart_gate.index') }}" class="nx-nav-link {{ request()->routeIs('smart_gate.*') ? 'active' : '' }}">
                            <i class="fas fa-torii-gate"></i>
                            <span class="nx-nav-text">Smart Gate RW 018</span>
                        </a>
                    </li>
                </ul>

                <!-- KELOMPOK 5: FASILITAS DAN KEGIATAN -->
                <div class="nx-menu-group-label">Fasilitas &amp; Kegiatan</div>
                <ul class="nx-nav-list">
                    <li class="nx-nav-item">
                        <a href="{{ route('fasilitas.index') }}" class="nx-nav-link {{ request()->routeIs('fasilitas.*') ? 'active' : '' }}">
                            <i class="fas fa-building-circle-check"></i>
                            <span class="nx-nav-text">Peminjaman Fasilitas</span>
                        </a>
                    </li>
                    <li class="nx-nav-item">
                        <a href="{{ route('agenda.index') }}" class="nx-nav-link {{ request()->routeIs('agenda.*') ? 'active' : '' }}">
                            <i class="fas fa-handshake"></i>
                            <span class="nx-nav-text">Agenda &amp; Rapat</span>
                        </a>
                    </li>
                    <li class="nx-nav-item">
                        <a href="{{ route('kalender.index') }}" class="nx-nav-link {{ request()->routeIs('kalender.*') ? 'active' : '' }}">
                            <i class="fas fa-calendar-check"></i>
                            <span class="nx-nav-text">Kalender Kegiatan</span>
                        </a>
                    </li>
                    <li class="nx-nav-item">
                        <a href="{{ route('umkm.index') }}" class="nx-nav-link {{ request()->routeIs('umkm.*') ? 'active' : '' }}">
                            <i class="fas fa-store"></i>
                            <span class="nx-nav-text">Direktori UMKM</span>
                        </a>
                    </li>
                    <li class="nx-nav-item">
                        <a href="{{ route('posyandu.index') }}" class="nx-nav-link {{ request()->routeIs('posyandu.*') ? 'active' : '' }}">
                            <i class="fas fa-heart"></i>
                            <span class="nx-nav-text">Info Posyandu</span>
                        </a>
                    </li>
                </ul>

                <!-- KELOMPOK 6: INFORMASI -->
                <div class="nx-menu-group-label">Informasi</div>
                <ul class="nx-nav-list">
                    <li class="nx-nav-item">
                        <a href="{{ route('pengumuman.index') }}" class="nx-nav-link {{ request()->routeIs('pengumuman.*') ? 'active' : '' }}">
                            <i class="fas fa-bullhorn"></i>
                            <span class="nx-nav-text">Pusat Pengumuman</span>
                        </a>
                    </li>
                </ul>

                <!-- KELOMPOK 7: PENGELOLAAN SISTEM -->
                <div class="nx-menu-group-label">{{ $isWargaAuth ? 'Akun & Profil' : 'Pengelolaan Sistem' }}</div>
                <ul class="nx-nav-list">
                    @if(!$isWargaAuth)
                    <li class="nx-nav-item">
                        <a href="{{ route('audit.index') }}" class="nx-nav-link {{ request()->routeIs('audit.*') ? 'active' : '' }}">
                            <i class="fas fa-clock-rotate-left"></i>
                            <span class="nx-nav-text">
                                @if($isAdminRWAuth)
                                    Audit &amp; Aktivitas
                                @elseif($isAdminRTAuth)
                                    Audit &amp; Aktivitas RT
                                @endif
                            </span>
                        </a>
                    </li>
                    @if($isAdminRWAuth || (auth()->check() && auth()->user()->isSuperAdmin()))
                    <li class="nx-nav-item">
                        <a href="{{ route('iot.index') }}" class="nx-nav-link {{ request()->routeIs('iot.*') ? 'active' : '' }}">
                            <i class="fas fa-microchip"></i>
                            <span class="nx-nav-text">Pengaturan API &amp; IoT</span>
                        </a>
                    </li>
                    @endif
                    @endif
                    <li class="nx-nav-item">
                        <a href="{{ route('profile.edit') }}" class="nx-nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                            <i class="fas fa-user-gear"></i>
                            <span class="nx-nav-text">Profil Saya</span>
                        </a>
                    </li>
                </ul>

                <!-- FOOTER SIDEBAR -->
                <div class="nx-sidebar-footer">
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
                                <a href="{{ route('pengaduan.index') }}" class="nx-search-item" data-keywords="manajemen aset pengaduan laporan keluhan kerusakan qr lingkungan pemeriksaan perawatan">
                                    <i class="fas fa-boxes-stacked text-primary"></i> Manajemen Aset &amp; Pengaduan
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
                        <!-- 1. Tombol Darurat -->
                        <button type="button" class="nx-topbar-emergency-btn" data-bs-toggle="modal" data-bs-target="#modalDaruratGlobal" title="Tombol Darurat RW 018">
                            <i class="fas fa-triangle-exclamation"></i>
                            <span class="d-none d-sm-inline ms-1">Darurat</span>
                        </button>

                        <!-- 2. Toggle Mode Dark / Light -->
                        <button type="button" class="nx-theme-toggle-btn" id="nxThemeToggleBtn" title="Beralih Tema (Dark/Light)">
                            <i class="far fa-moon dark-icon-moon"></i>
                            <i class="far fa-sun dark-icon-sun"></i>
                        </button>

                        <!-- 3. Notifikasi -->
                        <div class="dropdown">
                            <button class="nx-icon-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Notifikasi">
                                <i class="far fa-bell"></i>
                                @if($shellNotifCount > 0)
                                    <span class="nx-notif-badge">{{ $shellNotifCount }}</span>
                                @endif
                            </button>
                            <div class="dropdown-menu dropdown-menu-end p-3 border-0 shadow-lg" style="width: 340px; border-radius: 18px; background: #fff;">
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                    <span class="fw-bold" style="color: var(--nx-text); font-size: 0.9rem;">Notifikasi Layanan &amp; Keuangan</span>
                                    <span class="badge rounded-pill" style="background: var(--nx-mint); color: var(--nx-forest);">{{ $shellNotifCount }} Aktif</span>
                                </div>
                                <div class="d-flex flex-column gap-2" style="max-height: 380px; overflow-y: auto;">
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
                                    @if(isset($financeNotifs) && $financeNotifs->count() > 0)
                                        @foreach($financeNotifs as $fn)
                                            <a href="{{ $fn->link_url ?? route('notifikasi.index') }}" class="text-decoration-none p-2 rounded-3 d-flex align-items-start gap-2" style="background: {{ $fn->type === 'pembayaran_ditolak' ? '#FEE2E2' : ($fn->type === 'pembayaran_diverifikasi' ? 'var(--nx-mint-soft)' : '#FEF3C7') }}; color: var(--nx-text); font-size: 0.80rem;">
                                                <i class="fas {{ $fn->type === 'pembayaran_diverifikasi' ? 'fa-check-circle text-success' : ($fn->type === 'pembayaran_ditolak' ? 'fa-times-circle text-danger' : 'fa-receipt text-warning') }} mt-1"></i>
                                                <div class="flex-grow-1">
                                                    <strong class="d-block text-truncate" style="max-width: 260px;">{{ $fn->title }}</strong>
                                                    <span class="text-muted d-block text-truncate" style="max-width: 260px; font-size: 0.75rem;">{{ $fn->message }}</span>
                                                </div>
                                            </a>
                                        @endforeach
                                    @endif
                                    @if($shellNotifCount === 0)
                                        <div class="text-center py-3 text-muted small">
                                            <i class="fas fa-check-circle text-success mb-1 d-block fa-lg"></i>
                                            Semua layanan &amp; administrasi sudah tertangani.
                                        </div>
                                    @endif
                                </div>
                                <div class="mt-2 pt-2 border-top d-flex justify-content-between align-items-center">
                                    <a href="{{ route('notifikasi.index') }}" class="small fw-bold text-decoration-none" style="color: var(--nx-forest); font-size: 0.78rem;">
                                        Lihat Riwayat Notifikasi &rarr;
                                    </a>
                                    @if($shellNotifCount > 0)
                                        <form action="{{ route('notifikasi.read_all') }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-link p-0 text-muted small text-decoration-none" style="font-size: 0.75rem;">Tandai Dibaca</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="dropdown">
                            <a href="#" class="nx-profile-btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="{{ $authProfilePhotoUrl }}" 
                                     alt="{{ $authDisplayName }}" 
                                     class="nx-profile-avatar"
                                     onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ urlencode($authDisplayName) }}&background=285943&color=fff';">
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
                                    <div class="fw-bold small" style="color: var(--nx-text);">{{ $authDisplayName }}</div>
                                    @if($isSimulatedAuth)
                                        <div class="badge bg-warning-subtle text-dark border border-warning-subtle mt-1" style="font-size: 0.68rem; color: #0F172A !important;">
                                            👑 Sesi Super Admin: {{ $authUser->name }}
                                        </div>
                                    @else
                                        <div class="text-muted" style="font-size: 0.73rem;">{{ $authUser->email }}</div>
                                    @endif
                                </li>
                                @if($authUser && $authUser->isSuperAdmin())
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

                <!-- MODERN FLOATING TOAST NOTIFICATIONS (ALL ROLES) -->
                <x-nexa-toast />

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

    @if(Auth::check() && Auth::user()->isSuperAdmin())
        @include('components.role-switch-modal')
    @endif

    <!-- NexaNest Global Theme Controller & Chart.js Theme Sync -->
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

        // Synchronize Chart.js defaults and all active instances
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

        // Initialize Chart.js defaults on page load if dark mode is already active
        document.addEventListener('DOMContentLoaded', function() {
            var isDark = document.documentElement.classList.contains('dark');
            if (typeof Chart !== 'undefined' && isDark) {
                try {
                    Chart.defaults.color = '#94A3B8';
                    Chart.defaults.borderColor = 'rgba(148, 163, 184, 0.12)';
                } catch(err) {}
            }

            var toggleBtns = document.querySelectorAll('#nxThemeToggleBtn, #nxThemeToggleBtnDash, .nx-theme-toggle-btn');
            toggleBtns.forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.toggleNexaTheme();
                });
            });
        });
    </script>

    @stack('scripts')

    @auth
    @php
        $currUser = Auth::user();
        $isUserWarga = $currUser->isWarga();
        $isUserAdminRw = $currUser->isAdminRw();
        $isUserAdminRt = $currUser->isAdminRt();
    @endphp
    <!-- Mobile Bottom Navigation Bar ala GoPay (<= 768px) -->
    <nav class="nx-bottom-nav" aria-label="Navigasi Bawah Mobile">
        <a href="{{ route('dashboard') }}" class="nx-bottom-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Beranda</span>
        </a>

        @if($isUserWarga)
            <a href="{{ route('laporan.iuran') }}" class="nx-bottom-nav-item {{ request()->routeIs('laporan.iuran*') ? 'active' : '' }}">
                <i class="fas fa-receipt"></i>
                <span>Iuran</span>
            </a>
            <a href="{{ route('smart-gate.index') }}" class="nx-bottom-nav-item {{ request()->routeIs('smart-gate*') ? 'active' : '' }}">
                <i class="fas fa-torii-gate"></i>
                <span>Smart Gate</span>
            </a>
            <a href="{{ route('surat.index') }}" class="nx-bottom-nav-item {{ request()->routeIs('surat*') || request()->routeIs('pengaduan*') ? 'active' : '' }}">
                <i class="fas fa-envelope-open-text"></i>
                <span>Layanan</span>
            </a>
        @elseif($isUserAdminRw)
            <a href="{{ route('warga.index') }}" class="nx-bottom-nav-item {{ request()->routeIs('warga*') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>Warga/RT</span>
            </a>
            <a href="{{ route('pembayaran.index') }}" class="nx-bottom-nav-item {{ request()->routeIs('pembayaran*') || request()->routeIs('kas*') ? 'active' : '' }}">
                <i class="fas fa-wallet"></i>
                <span>Keuangan</span>
            </a>
            <a href="{{ route('smart-gate.index') }}" class="nx-bottom-nav-item {{ request()->routeIs('smart-gate*') ? 'active' : '' }}">
                <i class="fas fa-torii-gate"></i>
                <span>Smart Gate</span>
            </a>
        @else
            {{-- Admin RT / Bendahara RT --}}
            <a href="{{ route('warga.index') }}" class="nx-bottom-nav-item {{ request()->routeIs('warga*') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>Warga</span>
            </a>
            <a href="{{ route('pembayaran.index') }}" class="nx-bottom-nav-item {{ request()->routeIs('pembayaran*') || request()->routeIs('laporan.iuran*') ? 'active' : '' }}">
                <i class="fas fa-receipt"></i>
                <span>Iuran</span>
            </a>
            <a href="{{ route('surat.index') }}" class="nx-bottom-nav-item {{ request()->routeIs('surat*') || request()->routeIs('laporan_rt*') ? 'active' : '' }}">
                <i class="fas fa-clipboard-check"></i>
                <span>Surat/Laporan</span>
            </a>
        @endif

        <button type="button" class="nx-bottom-nav-item" onclick="document.getElementById('nxShell')?.classList.toggle('mobile-open')">
            <i class="fas fa-bars"></i>
            <span>Menu</span>
        </button>
    </nav>
    @endauth

    <!-- Floating Telegram Button Pengaduan (Warga, Admin RT, Admin RW) -->
    @include('components.floating-telegram')
</body>
</html>
