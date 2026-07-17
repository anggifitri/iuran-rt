<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NexaNest — Masuk Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --accent: #D1A151;
            --accent-light: #f59e0b;
            --bg-1: #020914;
            --bg-2: #052646;
        }

        /* Light Mode */
        html[data-theme="light"] {
            --accent: #6366f1;
            --accent-light: #4f46e5;
            --bg-primary: #ffffff;
            --bg-secondary: #f8fafc;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-color: rgba(99, 102, 241, 0.08);
            --gradient-from: #6366f1;
            --gradient-to: #a855f7;
            --glow-color: rgba(99, 102, 241, 0.15);
        }

        /* Dark Mode */
        html[data-theme="dark"] {
            --accent: #D1A151;
            --accent-light: #fbbf24;
            --bg-primary: #031022;
            --bg-secondary: #042e55;
            --text-primary: #e6eef6;
            --text-secondary: #94a3b8;
            --border-color: rgba(209, 161, 81, 0.12);
            --gradient-from: #D1A151;
            --gradient-to: #ea580c;
            --glow-color: rgba(209, 161, 81, 0.18);
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            color: var(--text-primary); 
            transition: background-color 0.4s, color 0.4s;
            overflow-x: hidden;
        }

        /* Animated Background */
        html[data-theme="dark"] .bg-animated {
            background: linear-gradient(135deg, #020914 0%, #03172e 45%, #052d56 100%);
            background-size: 400% 400%;
            animation: moveGrad 18s ease infinite;
        }
        html[data-theme="light"] .bg-animated {
            background: linear-gradient(135deg, #ffffff 0%, #eff6ff 45%, #f5f3ff 100%);
            background-size: 400% 400%;
            animation: moveGrad 18s ease infinite;
        }

        @keyframes moveGrad {
            0% { background-position: 0% 50% }
            50% { background-position: 100% 50% }
            100% { background-position: 0% 50% }
        }

        /* Twinkling Starfield */
        .stars {
            position: absolute;
            inset: 0;
            pointer-events: none;
            background:
                radial-gradient(circle at 15% 20%, rgba(255,255,255,0.7) 1px, transparent 0),
                radial-gradient(circle at 35% 15%, rgba(255,255,255,0.6) 1.5px, transparent 0),
                radial-gradient(circle at 55% 25%, rgba(255,255,255,0.8) 1px, transparent 0),
                radial-gradient(circle at 75% 12%, rgba(255,255,255,0.5) 1px, transparent 0),
                radial-gradient(circle at 90% 30%, rgba(255,255,255,0.75) 1.5px, transparent 0),
                radial-gradient(circle at 20% 60%, rgba(255,255,255,0.65) 1px, transparent 0),
                radial-gradient(circle at 50% 65%, rgba(255,255,255,0.8) 1px, transparent 0),
                radial-gradient(circle at 80% 55%, rgba(255,255,255,0.5) 2px, transparent 0);
            background-size: 250px 250px;
            opacity: 0.8;
            animation: twinkleStars 12s infinite alternate ease-in-out;
        }

        @keyframes twinkleStars {
            0% { opacity: 0.5; }
            100% { opacity: 0.9; }
        }

        html[data-theme="light"] .stars { display: none; }

        /* Floating Aurora Orbs */
        .aurora-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.25;
            z-index: 0;
            pointer-events: none;
        }
        .orb-1 {
            width: 400px;
            height: 400px;
            background: var(--accent);
            top: -100px;
            right: -100px;
            animation: floatOrb1 15s infinite alternate ease-in-out;
        }
        .orb-2 {
            width: 500px;
            height: 500px;
            background: #3b82f6;
            bottom: -150px;
            left: -150px;
            animation: floatOrb2 20s infinite alternate ease-in-out;
        }

        @keyframes floatOrb1 {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(-80px, 50px) scale(1.15); }
        }
        @keyframes floatOrb2 {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(100px, -60px) scale(0.9); }
        }

        /* Glassmorphism Card with Entrance Animation */
        .login-card {
            width: min(1000px, calc(100% - 2rem));
            border-radius: 36px;
            overflow: hidden;
            display: grid;
            grid-template-columns: 1.1fr 1fr;
            backdrop-filter: blur(20px);
            border: 1px solid var(--border-color);
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUp 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            box-shadow: 0 30px 70px rgba(0, 0, 0, 0.25);
            transition: box-shadow 0.5s ease, transform 0.5s ease;
        }

        .login-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 40px 90px rgba(0, 0, 0, 0.35);
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        html[data-theme="dark"] .login-card {
            background: linear-gradient(135deg, rgba(4, 15, 33, 0.75) 0%, rgba(5, 37, 65, 0.85) 100%);
        }
        html[data-theme="light"] .login-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.75) 0%, rgba(248, 250, 252, 0.85) 100%);
        }

        /* Split Panels */
        .left-panel {
            padding: 4rem 3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        html[data-theme="dark"] .left-panel {
            background: rgba(3, 10, 22, 0.4);
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }
        html[data-theme="light"] .left-panel {
            background: rgba(241, 245, 249, 0.4);
            border-right: 1px solid rgba(99, 102, 241, 0.06);
        }

        .right-panel {
            padding: 4rem 3rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Glassmorphism Form Wrapper */
        .form-container {
            width: 100%;
            max-width: 440px;
        }

        .input-group-modern {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .input-modern {
            width: 100%;
            border-radius: 14px;
            padding: 0.95rem 1rem 0.95rem 2.8rem;
            font-size: 0.95rem;
            font-weight: 500;
            border: 1.5px solid var(--border-color);
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.05);
        }

        html[data-theme="dark"] .input-modern {
            color: #f8fafc;
            background: rgba(255, 255, 255, 0.03);
        }
        html[data-theme="light"] .input-modern {
            color: #1e293b;
            background: rgba(255, 255, 255, 0.8);
            border-color: rgba(99, 102, 241, 0.15);
        }

        .input-modern:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 4px var(--glow-color);
            background: rgba(255, 255, 255, 0.08);
        }

        .input-icon-left {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 1.1rem;
            pointer-events: none;
            transition: color 0.3s ease;
        }
        .input-modern:focus ~ .input-icon-left {
            color: var(--accent);
        }

        /* Eye Icon Button Reset */
        .eye-toggle-btn {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            cursor: pointer;
            z-index: 10;
            border: none;
            background: transparent;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .eye-toggle-btn:hover {
            color: var(--accent);
        }

        /* 3D Floating House SVG Animation */
        .floating-art {
            animation: floatArt 6s ease-in-out infinite;
        }

        @keyframes floatArt {
            0% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(1deg); }
            100% { transform: translateY(0px) rotate(0deg); }
        }

        /* Golden/Blue Theme Toggle Button */
        .theme-toggle {
            position: fixed;
            top: 25px;
            right: 25px;
            z-index: 1000;
            border-radius: 50%;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 8px 30px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            color: white;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
        }
        html[data-theme="dark"] .theme-toggle {
            background: rgba(209, 161, 81, 0.15);
            color: #fbbf24;
        }
        html[data-theme="light"] .theme-toggle {
            background: rgba(99, 102, 241, 0.15);
            color: #6366f1;
        }
        .theme-toggle:hover {
            transform: scale(1.1) rotate(15deg);
        }

        /* Submit Button */
        .btn-submit {
            background: linear-gradient(135deg, var(--gradient-from), var(--gradient-to));
            color: white;
            font-weight: 700;
            border: none;
            letter-spacing: 0.5px;
            box-shadow: 0 10px 25px var(--glow-color);
            transition: all 0.3s ease;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 35px var(--glow-color);
            filter: brightness(1.05);
        }
        .btn-submit:active {
            transform: translateY(0);
        }

        @media (max-width: 992px) {
            .login-card { grid-template-columns: 1fr; }
            .left-panel { display: none; }
            .right-panel { padding: 3rem 1.5rem; }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 bg-animated relative">
    
    <!-- Theme Toggle -->
    <button id="themeToggle" class="theme-toggle" title="Ubah Tema" aria-label="Ubah Tema">
        <svg id="sunIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;" width="22" height="22">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1m-16 0H1m15.364 1.636l.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        <svg id="moonIcon" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" width="22" height="22">
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
        </svg>
    </button>

    <!-- Starfield & Aurora Orbs -->
    <div class="stars"></div>
    <div class="aurora-orb orb-1"></div>
    <div class="aurora-orb orb-2"></div>

    <!-- Login Card -->
    <div class="login-card">
        
        <!-- Left Panel: Graphic Branding -->
        <div class="left-panel">
            <div class="text-center w-full max-w-sm">
                <!-- 3D Glowing House SVG Illustration -->
                <div class="floating-art mx-auto mb-6" style="width: 200px; height: 200px; filter: drop-shadow(0 15px 30px var(--glow-color));">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" fill="none" stroke="currentColor" class="w-full h-full" style="color: var(--accent);">
                        <!-- Grid Lines behind -->
                        <circle cx="50" cy="50" r="45" stroke="currentColor" stroke-width="0.5" stroke-dasharray="2 2" opacity="0.25"/>
                        <circle cx="50" cy="50" r="35" stroke="currentColor" stroke-width="0.5" opacity="0.15"/>
                        
                        <!-- Ground line -->
                        <line x1="15" y1="75" x2="85" y2="75" stroke="currentColor" stroke-width="1" opacity="0.4"/>
                        
                        <!-- Isometric house block -->
                        <!-- Roof Front -->
                        <path d="M50 20 L20 48 L28 48 L50 29" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="1.8"/>
                        <!-- Roof Right -->
                        <path d="M50 20 L80 48 L72 48 L50 29" fill="currentColor" fill-opacity="0.1" stroke="currentColor" stroke-width="1.8"/>
                        
                        <!-- House Body Front -->
                        <path d="M28 48 V75 H50 V45 Z" fill="currentColor" fill-opacity="0.05" stroke="currentColor" stroke-width="1.8"/>
                        <!-- House Body Right -->
                        <path d="M72 48 V75 H50 V45 Z" fill="currentColor" fill-opacity="0.05" stroke="currentColor" stroke-width="1.8"/>
                        
                        <!-- Door -->
                        <rect x="44" y="56" width="12" height="19" rx="2" stroke="currentColor" stroke-width="1.5" fill="#000" fill-opacity="0.3"/>
                        <!-- Window Left -->
                        <rect x="33" y="52" width="6" height="8" rx="1" stroke="currentColor" stroke-width="1.2"/>
                        <!-- Window Right -->
                        <rect x="61" y="52" width="6" height="8" rx="1" stroke="currentColor" stroke-width="1.2"/>
                        
                        <!-- Chimney -->
                        <path d="M66 30 V42 H72 V30 Z" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                </div>

                <h1 class="brand-title text-5xl font-extrabold tracking-tight italic mb-2" style="color: var(--accent); text-shadow: 0 4px 15px var(--glow-color);">NexaNest</h1>
                <p class="text-sm font-semibold tracking-widest uppercase opacity-85" style="color: var(--text-secondary);">Portal Layanan Digital Warga</p>
                <div class="w-16 h-1 mx-auto mt-4 rounded" style="background: linear-gradient(90deg, var(--gradient-from), var(--gradient-to));"></div>
            </div>
        </div>

        <!-- Right Panel: Login Form -->
        <div class="right-panel">
            <div class="form-container">
                <div class="mb-8">
                    <h3 class="text-3xl font-extrabold mb-1" style="color: var(--text-main);">Selamat Datang</h3>
                    <p class="text-sm text-secondary" style="color: var(--text-secondary);">Silakan masuk untuk mengakses portal kas, surat, dan posyandu.</p>
                </div>

                <form action="{{ route('login') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Email Input -->
                    <div>
                        <label class="text-xs font-bold mb-2 block uppercase tracking-wider" style="color: var(--text-secondary);">Alamat Email</label>
                        <div class="input-group-modern">
                            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                                class="input-modern"
                                placeholder="nama@email.com">
                            <i class="fa-regular fa-envelope input-icon-left"></i>
                        </div>
                        @error('email')
                            <span class="text-red-400 text-xs mt-1 block"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password Input -->
                    <div>
                        <label class="text-xs font-bold mb-2 block uppercase tracking-wider" style="color: var(--text-secondary);">Password Akun</label>
                        <div class="input-group-modern">
                            <input type="password" id="loginPassword" name="password" required
                                class="input-modern"
                                placeholder="••••••••">
                            <i class="fa-solid fa-lock input-icon-left"></i>
                            
                            <!-- Toggle Eye -->
                            <button type="button" onclick="toggleLoginPassword()" class="eye-toggle-btn" tabindex="-1">
                                <i id="loginEyeIcon" class="fa-regular fa-eye"></i>
                                <i id="loginEyeOffIcon" class="fa-regular fa-eye-slash" style="display: none;"></i>
                            </button>
                        </div>
                        @error('password')
                            <span class="text-red-400 text-xs mt-1 block"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Remember & Forgot Password -->
                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center cursor-pointer select-none">
                            <input id="remember" type="checkbox" name="remember" checked class="w-4 h-4 mr-2.5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" style="accent-color: var(--accent);">
                            <span class="text-xs font-medium" style="color: var(--text-primary);">Ingat Saya</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="text-xs font-bold hover:underline" style="color: var(--accent);">Lupa Password?</a>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit" class="btn-submit w-full py-3.5 rounded-xl text-sm font-extrabold uppercase transition-all tracking-wider shadow-lg active:scale-[0.98]">
                            <i class="fas fa-sign-in-alt me-1"></i> Masuk Sekarang
                        </button>
                    </div>
                </form>

                <div class="text-center mt-6 text-sm" style="color: var(--text-secondary);">
                    Belum memiliki akun warga? <a href="{{ route('register') }}" class="font-extrabold hover:underline" style="color: var(--accent);">Daftar Baru</a>
                </div>

                @if ($errors->any())
                    <div class="mt-6 p-4 rounded-xl border bg-red-950/20 border-red-900/40 text-red-400 text-xs space-y-1">
                        @foreach ($errors->all() as $error)
                            <div class="flex items-center gap-2">
                                <i class="fas fa-exclamation-triangle flex-shrink-0"></i>
                                <span>{{ $error }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Toggle Password Visibility
        window.loginEyeClicked = false;
        function toggleLoginPassword() {
            const input = document.getElementById('loginPassword');
            const eyeIcon = document.getElementById('loginEyeIcon');
            const eyeOffIcon = document.getElementById('loginEyeOffIcon');
            window.loginEyeClicked = true;
            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.style.display = 'none';
                eyeOffIcon.style.display = 'block';
            } else {
                input.type = 'password';
                eyeIcon.style.display = 'block';
                eyeOffIcon.style.display = 'none';
            }
            setTimeout(() => { window.loginEyeClicked = false; }, 300);
        }

        // Initialize theme
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

        document.getElementById('themeToggle').addEventListener('click', toggleTheme);

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initTheme);
        } else {
            initTheme();
        }
    </script>
</body>
</html>
