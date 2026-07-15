<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kas RT')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>

        :root {
            --bg-body: #0F172A;
            --bg-card: #1E293B;
            --primary: #818CF8;
            --primary-hover: #6366F1;
            --text-main: #F8FAFC;
            --text-muted: #94A3B8;
            --border-color: #334155;
        }

        /* Light Mode */
        html[data-theme="light"] {
            --bg-body: #ffffff;
            --bg-card: #f8fafc;
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
        }

        body {
            background-color: var(--bg-body);
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            color: var(--text-main);
            transition: background-color 0.3s, color 0.3s;
        }

        .navbar {
            background: var(--bg-card) !important;
            border-bottom: 1px solid var(--border-color);
            padding: 15px 0;
            transition: background-color 0.3s, border-color 0.3s;
        }

        .navbar-brand {
            color: var(--text-main) !important;
            font-size: 1.25rem;
        }

        .navbar-toggler {
            border-color: var(--border-color);
        }
        .navbar-toggler-icon {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        html[data-theme="light"] .navbar-toggler-icon {
            filter: none;
        }

        .dropdown-menu {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
            border-radius: 12px;
            transition: background-color 0.3s, border-color 0.3s;
        }

        .dropdown-item {
            color: var(--text-main);
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: #334155;
            color: var(--primary);
        }

        html[data-theme="light"] .dropdown-item:hover {
            background-color: #e2e8f0;
        }

        .dropdown-divider {
            border-top-color: var(--border-color);
        }

        .text-danger {
            color: #F87171 !important;
        }

        html[data-theme="light"] .text-danger {
            color: #ef4444 !important;
        }

        .card-hover, .sidebar, .welcome-section, .stat-card {
            background: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        html[data-theme="light"] .card-hover, html[data-theme="light"] .sidebar, html[data-theme="light"] .welcome-section, html[data-theme="light"] .stat-card {
            box-shadow: 0 4px 6px -1px rgba(99,102,241,0.1);
        }

        .sidebar {
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.98), rgba(30, 41, 59, 0.95));
            padding: 24px;
            border-radius: 24px;
            border: 1px solid rgba(148, 163, 184, 0.15);
            min-height: calc(100vh - 110px);
            position: sticky;
            top: 20px;
        }

        html[data-theme="light"] .sidebar {
            background: linear-gradient(180deg, rgba(248, 250, 252, 0.95), rgba(241, 245, 249, 0.98));
            border: 1px solid rgba(99, 102, 241, 0.1);
        }

        .sidebar .brand {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 28px;
        }

        .sidebar .brand-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(129, 140, 248, 0.18);
            color: var(--primary);
            font-size: 1.1rem;
        }

        html[data-theme="light"] .sidebar .brand-icon {
            background: rgba(99, 102, 241, 0.12);
        }

        .sidebar .menu-title {
            color: var(--text-muted);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.09em;
            margin-bottom: 12px;
        }

        .sidebar .nav-link {
            color: var(--text-muted);
            transition: all 0.2s ease;
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 6px;
            font-weight: 500;
            display: flex;
            align-items: center;
        }

        .sidebar .nav-link i {
            width: 24px;
            text-align: center;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: rgba(129, 140, 248, 0.14);
            color: var(--primary);
        }

        html[data-theme="light"] .sidebar .nav-link:hover,
        html[data-theme="light"] .sidebar .nav-link.active {
            background-color: rgba(99, 102, 241, 0.1);
        }

        .sidebar .nav-link.active {
            border-left: 3px solid var(--primary);
            color: var(--primary);
        }

        .sidebar .nav-link.active i,
        .sidebar .nav-link:hover i {
            color: var(--primary);
        }

        .stat-card { padding: 24px; }
        .stat-card:hover, .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.3);
            border-color: #475569;
        }

        html[data-theme="light"] .stat-card:hover, html[data-theme="light"] .card-hover:hover {
            box-shadow: 0 10px 15px -3px rgba(99,102,241,0.2);
            border-color: var(--primary);
        }

        .alert-success {
            background-color: rgba(6, 95, 70, 0.4);
            color: #34D399;
            border: 1px solid #065F46;
            border-radius: 12px;
        }

        html[data-theme="light"] .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            color: #059669;
            border: 1px solid #d1fae5;
        }

        .alert-danger {
            background-color: rgba(153, 27, 27, 0.4);
            color: #F87171;
            border: 1px solid #991B1B;
            border-radius: 12px;
        }

        html[data-theme="light"] .alert-danger {
            background-color: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            border: 1px solid #fee2e2;
        }

        .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        html[data-theme="light"] .btn-close {
            filter: none;
        }

        /* Theme Toggle Button */
        .theme-toggle-btn {
            position: fixed;
            top: 12px;
            right: 12px;
            z-index: 1100;
            background: linear-gradient(135deg, var(--primary), #7c3aed);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(99,102,241,0.3);
        }

        html[data-theme="dark"] .theme-toggle-btn {
            background: linear-gradient(135deg, #818CF8, #a78bfa);
        }

        .theme-toggle-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 16px rgba(99,102,241,0.4);
        }

        .theme-toggle-btn svg {
            width: 20px;
            height: 20px;
            transition: transform 0.3s ease;
        }
    </style>
    @stack('styles')
</head>
<body>
    @auth
    <nav class="navbar navbar-expand-lg mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">
                <i class="fas fa-hand-holding-usd me-2" style="color: var(--primary);"></i>
                Kas RT
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <button id="themeToggle" class="theme-toggle-btn" title="Toggle Dark/Light Mode" aria-label="Toggle theme">
                    <svg id="sunIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1m-16 0H1m15.364 1.636l.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <svg id="moonIcon" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
                    </svg>
                </button>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" style="font-weight: 500; color: var(--text-main);">
                            <img src="{{ Auth::user()->profile_photo_url }}" alt="Avatar" class="rounded-circle me-2" width="36" height="36">
                            <div class="text-start d-none d-lg-block">
                                <div>{{ Auth::user()->name }}</div>
                                <small class="text-muted">RT {{ Auth::user()->rt_number ?? '-' }} • {{ ucfirst(Auth::user()->role) }}</small>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-user me-2" style="color: var(--text-muted);"></i>Profil Saya
                                </a>
                            </li>
                            <li>
        <a class="dropdown-item" href="{{ route('dashboard') }}">
            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
        </a>
    </li>
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('password.request') }}">
                                    <i class="fas fa-lock me-2" style="color: var(--text-muted);"></i>Ganti Password
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 text-danger">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    @endauth

    <div class="container-fluid px-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false,
                background: '#1E293B',
                color: '#F8FAFC',
                customClass: {
                    popup: 'rounded-4 border border-secondary'
                }
            });
        </script>
    @endif

    <script>
        // Initialize theme from localStorage or system preference
        function initTheme() {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
            updateThemeIcons(savedTheme);
        }

        function updateThemeIcons(theme) {
            const sunIcon = document.getElementById('sunIcon');
            const moonIcon = document.getElementById('moonIcon');

            if (theme === 'light') {
                sunIcon.style.display = 'block';
                moonIcon.style.display = 'none';
            } else {
                sunIcon.style.display = 'none';
                moonIcon.style.display = 'block';
            }
        }

        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateThemeIcons(newTheme);
        }

        // Setup event listeners
        const themeToggle = document.getElementById('themeToggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', toggleTheme);
        }

        // Initialize on page load
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initTheme);
        } else {
            initTheme();
        }
    </script>

    @stack('scripts')
</body>
</html>
