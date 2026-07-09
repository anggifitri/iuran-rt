<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kas RT — Masuk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <style>
        :root{--accent:#D1A151;--bg-1:#031022;--bg-2:#042e55}

        /* Light Mode */
        html[data-theme="light"] {
            --accent:#6366f1;
            --accent-light:#818cf8;
            --bg-primary:#ffffff;
            --bg-secondary:#f8fafc;
            --text-primary:#1e293b;
            --text-secondary:#64748b;
            --border-color:#e2e8f0;
            --gradient-from:#ec4899;
            --gradient-to:#d946ef;
        }

        /* Dark Mode */
        html[data-theme="dark"] {
            --accent:#D1A151;
            --accent-light:#f59e0b;
            --bg-primary:#031022;
            --bg-secondary:#042e55;
            --text-primary:#e6eef6;
            --text-secondary:#cbd5e1;
            --border-color:rgba(255,255,255,0.03);
            --gradient-from:#D1A151;
            --gradient-to:#fb923c;
        }

        body { font-family: 'Plus Jakarta Sans', sans-serif; color: #e6eef6; position: relative; transition: background-color 0.3s, color 0.3s; }

        html[data-theme="dark"] .bg-animated{background: linear-gradient(180deg,#031022 0%, #041d34 40%, #042e55 100%);background-size:300% 300%;animation: moveGrad 15s ease infinite;}
        html[data-theme="light"] .bg-animated{background: linear-gradient(180deg, #ffffff 0%, #f0f9ff 40%, #fce7f3 100%);background-size:300% 300%;animation: moveGrad 15s ease infinite;}

        .bg-animated{background-size:300% 300%;animation: moveGrad 15s ease infinite;}
        @keyframes moveGrad{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
        .stars{position:absolute;inset:0;pointer-events:none;background:
            radial-gradient(circle at 12% 18%, rgba(255,255,255,0.9) 1px, transparent 0),
            radial-gradient(circle at 28% 14%, rgba(255,255,255,0.75) 1px, transparent 0),
            radial-gradient(circle at 46% 22%, rgba(255,255,255,0.85) 1px, transparent 0),
            radial-gradient(circle at 63% 12%, rgba(255,255,255,0.8) 1px, transparent 0),
            radial-gradient(circle at 84% 20%, rgba(255,255,255,0.7) 1px, transparent 0),
            radial-gradient(circle at 15% 50%, rgba(255,255,255,0.75) 1px, transparent 0),
            radial-gradient(circle at 36% 58%, rgba(255,255,255,0.9) 1px, transparent 0),
            radial-gradient(circle at 52% 44%, rgba(255,255,255,0.8) 1px, transparent 0),
            radial-gradient(circle at 71% 62%, rgba(255,255,255,0.7) 1px, transparent 0),
            radial-gradient(circle at 90% 55%, rgba(255,255,255,0.85) 1px, transparent 0);
        background-size: 180px 180px, 160px 160px, 220px 220px, 140px 140px, 240px 240px, 200px 200px, 88px 88px, 130px 130px, 170px 170px, 95px 95px;
        opacity:.9;
        }

        html[data-theme="light"] .stars {
            background: none !important;
            opacity: 0;
        }

        .stars::after{content:'';position:absolute;inset:0;background:
            radial-gradient(circle at 25% 82%, rgba(255,255,255,0.35) 2px, transparent 0),
            radial-gradient(circle at 48% 72%, rgba(255,255,255,0.4) 1.5px, transparent 0),
            radial-gradient(circle at 68% 92%, rgba(255,255,255,0.38) 1px, transparent 0),
            radial-gradient(circle at 12% 62%, rgba(255,255,255,0.3) 1px, transparent 0),
            radial-gradient(circle at 82% 35%, rgba(255,255,255,0.35) 1px, transparent 0),
            radial-gradient(circle at 37% 30%, rgba(255,255,255,0.3) 1px, transparent 0);
        background-size: 120px 120px, 90px 90px, 140px 140px, 80px 80px, 100px 100px, 130px 130px;
        opacity:.7;animation: twinkle 10s ease-in-out infinite alternate;
        }
        @keyframes twinkle{from{opacity:.55}to{opacity:.8}}

        .card { width: min(980px, calc(100% - 2rem)); max-width: 1080px; border-radius: 36px; overflow: hidden; box-shadow: 0 40px 100px rgba(0,0,0,0.35); border: 1px solid rgba(255,255,255,0.03); background: linear-gradient(180deg, #041026 0%, #052f4a 35%, #042b44 100%); display:grid; grid-template-columns:1fr 1fr; }

        html[data-theme="light"] .card {
            background: linear-gradient(180deg, #ffffff 0%, #f9f5ff 35%, #faf5ff 100%);
            border-color: rgba(99,102,241,0.1);
            box-shadow: 0 40px 100px rgba(99,102,241,0.1);
        }

        .left-panel, .right-panel { padding: 3rem 2.5rem; display:flex; align-items:center; justify-content:center; min-height:520px; }

        html[data-theme="dark"] .left-panel { background: linear-gradient(180deg, #020b13 0%, #071522 100%); }
        html[data-theme="light"] .left-panel { background: linear-gradient(180deg, #f8f6ff 0%, #faf5ff 100%); }

        html[data-theme="dark"] .left-panel .brand-title { color: #c69d4d; }
        html[data-theme="light"] .left-panel .brand-title { color: #6366f1; }

        html[data-theme="dark"] .left-panel p { color: #9fb4c3; }
        html[data-theme="light"] .left-panel p { color: #7c3aed; }

        html[data-theme="dark"] .right-panel { background: linear-gradient(180deg, rgba(5,12,25,0.92) 0%, rgba(10,24,46,0.98) 100%); border-left: 1px solid rgba(255,255,255,0.08); position: relative; }
        html[data-theme="light"] .right-panel { background: linear-gradient(180deg, rgba(248,245,255,0.95) 0%, rgba(243,244,255,0.98) 100%); border-left: 1px solid rgba(99,102,241,0.1); position: relative; }

        html[data-theme="dark"] .right-panel::before { content: ''; position: absolute; inset: 0; background: radial-gradient(circle at top right, rgba(209,161,81,0.04), transparent 25%); pointer-events: none; }
        html[data-theme="light"] .right-panel::before { content: ''; position: absolute; inset: 0; background: radial-gradient(circle at top right, rgba(99,102,241,0.05), transparent 25%); pointer-events: none; }

        .panel-inner { width: 100%; max-width: 520px; position: relative; z-index: 1; }

        html[data-theme="dark"] .form-card { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08); backdrop-filter: blur(20px); border-radius: 28px; padding: 2rem; box-shadow: 0 24px 60px rgba(0,0,0,0.18); }
        html[data-theme="light"] .form-card { background: rgba(255,255,255,0.8); border: 1px solid rgba(99,102,241,0.2); backdrop-filter: blur(20px); border-radius: 28px; padding: 2rem; box-shadow: 0 24px 60px rgba(99,102,241,0.08); }

        html[data-theme="dark"] .form-card label { color: #f8fafc; }
        html[data-theme="light"] .form-card label { color: #1e293b; }

        html[data-theme="dark"] .form-card input { width: 100%; border-radius: 1rem; padding: 0.85rem 1rem; background: rgba(243,246,255,0.95); color: #0f172a; border: 1px solid rgba(209,161,81,0.3); }
        html[data-theme="light"] .form-card input { width: 100%; border-radius: 1rem; padding: 0.85rem 1rem; background: rgba(248,245,255,0.95); color: #1e293b; border: 1px solid rgba(99,102,241,0.2); }

        html[data-theme="dark"] .form-card input::placeholder { color: rgba(15,23,42,0.45); }
        html[data-theme="light"] .form-card input::placeholder { color: rgba(30,41,59,0.45); }

        html[data-theme="dark"] .form-card input:focus { outline: none; box-shadow: 0 0 0 4px rgba(209,161,81,0.16); border-color: rgba(209,161,81,0.55); }
        html[data-theme="light"] .form-card input:focus { outline: none; box-shadow: 0 0 0 4px rgba(99,102,241,0.16); border-color: rgba(99,102,241,0.55); }

        .form-card button { width: 100%; border-radius: 1rem; padding: 0.95rem 1rem; }

        html[data-theme="dark"] .form-card .helper { color: #cbd5e1; }
        html[data-theme="light"] .form-card .helper { color: #64748b; }

        .brand-title { font-family: 'Playfair Display', serif; letter-spacing: -0.035em; text-shadow: 0 4px 20px rgba(0,0,0,0.25); }

        /* Theme Toggle Button */
        .theme-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            background: linear-gradient(135deg, var(--accent), var(--accent-light));
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            color: white;
        }

        html[data-theme="light"] .theme-toggle {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            box-shadow: 0 8px 24px rgba(99,102,241,0.3);
        }

        .theme-toggle:hover {
            transform: scale(1.1);
            box-shadow: 0 12px 32px rgba(0,0,0,0.3);
        }

        .theme-toggle svg {
            width: 24px;
            height: 24px;
            transition: transform 0.3s ease;
        }

        html[data-theme="light"] .theme-toggle:hover {
            box-shadow: 0 12px 32px rgba(99,102,241,0.4);
        }

        @media (max-width:1024px) {.card{grid-template-columns:1fr;} .left-panel,.right-panel{padding:2rem;} .form-card{max-width:100%;}}
        @media (max-width:640px){body{padding:1rem;} .left-panel{padding:1.5rem;} .right-panel{padding:1.5rem;} .brand-title{text-align:center;} .theme-toggle{width:45px;height:45px;top:15px;right:15px;}}
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6 bg-animated">
    <button id="themeToggle" class="theme-toggle" title="Toggle Dark/Light Mode" aria-label="Toggle theme">
        <svg id="sunIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1m-16 0H1m15.364 1.636l.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        <svg id="moonIcon" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
        </svg>
    </button>
    <div class="stars"></div>
    <div class="card">
        <div class="left-panel">
            <div class="panel-inner text-center">
                <div class="mb-0 mx-auto text-amber-400 filter drop-shadow-[0_10px_25px_rgba(209,161,81,0.4)]" style="width:160px;height:160px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="160" height="160" viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="1.5">
                    <line x1="5" y1="80" x2="95" y2="80" stroke="currentColor" stroke-width="0.5" opacity="0.2"/>
                    <line x1="25" y1="20" x2="25" y2="90" stroke="currentColor" stroke-width="0.5" opacity="0.2"/>
                    <line x1="75" y1="20" x2="75" y2="90" stroke="currentColor" stroke-width="0.5" opacity="0.2"/>

                    <line x1="10" y1="48" x2="52" y2="14" stroke="currentColor" stroke-width="1.8"/>
                    <line x1="48" y1="14" x2="90" y2="48" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M22 45 L50 20 L78 45" stroke="currentColor" stroke-width="1.2" opacity="0.7"/>

                    <path d="M25 45 V80 H75 V45" stroke="currentColor" stroke-width="2"/>

                    <path d="M63 26 V35" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M63 26 H69 V40" stroke="currentColor" stroke-width="1.5"/>

                    <rect x="44" y="58" width="14" height="22" stroke="currentColor" stroke-width="1.5" fill="#0b1424" fill-opacity="0.5"/>
                    <rect x="32" y="52" width="6" height="10" stroke="currentColor" stroke-width="1.5"/>
                    <rect x="62" y="52" width="6" height="10" stroke="currentColor" stroke-width="1.5"/>

                    <line x1="20" y1="45" x2="80" y2="45" stroke="currentColor" stroke-width="0.8" opacity="0.5"/>
                    <line x1="15" y1="80" x2="85" y2="80" stroke="currentColor" stroke-width="2"/>
                </svg>
            </div>

            <h2 class="brand-title text-5xl font-bold tracking-wide italic -mt-1" style="color: var(--accent);">Kas RT</h2>
            <p class="mt-1 text-slate-400 text-xs tracking-widest uppercase" style="color: var(--text-secondary);">Solusi Pintar Pengelolaan Iuran</p>
        </div>
        </div>

        <div class="right-panel p-12 flex items-center">
            <div class="panel-inner">
                <div class="form-card">
                    <form action="{{ route('login') }}" method="POST" class="space-y-5">
                        @csrf

                        <div>
                            <label class="text-slate-400 text-xs font-semibold mb-2 block uppercase tracking-wider" style="color: var(--text-secondary);">Alamat Email</label>
                            <div class="relative flex items-center">
                                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                                    class="w-full rounded-xl py-3 pl-4 pr-5 border transition-all focus:outline-none"
                                    style="background-color: rgba(243,246,255,0.95); color: var(--text-primary); border-color: var(--border-color);"
                                    placeholder="Masukkan Email">
                            </div>
                            @error('email')
                                <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="text-slate-400 text-xs font-semibold mb-2 block uppercase tracking-wider" style="color: var(--text-secondary);">Password</label>
                            <div class="relative flex items-center">
                                <span class="absolute left-4 flex items-center pointer-events-none" style="color: #94a3b8;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                    </svg>
                                </span>
                                <input type="password" id="loginPassword" name="password" required
                                    class="w-full rounded-xl py-3 pl-12 pr-4 border transition-all focus:outline-none"
                                    style="background-color: rgba(243,246,255,0.95); color: var(--text-primary); border-color: var(--border-color);"
                                    placeholder="*******">
                                <button type="button" onclick="toggleLoginPassword()" class="absolute flex items-center focus:outline-none" style="color: #94a3b8; right:-12px; top:50%; transform:translateY(-50%);" tabindex="-1">
                                    <svg id="loginEyeIcon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                                        <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                                        <line x1="1" y1="1" x2="23" y2="23"/>
                                    </svg>
                                    <svg id="loginEyeOffIcon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between pt-1">
                            <div class="flex items-center">
                                <input id="remember" type="checkbox" name="remember" class="w-4 h-4 mr-3 accent-amber-500 rounded" style="accent-color: var(--accent);">
                                <label for="remember" class="text-slate-300 font-semibold text-sm select-none" style="color: var(--text-primary);">Remember Me</label>
                            </div>
                            <div>
                                <a href="{{ route('password.request') }}" class="text-amber-400 text-sm hover:underline" style="color: var(--accent);">Lupa Password?</a>
                            </div>
                        </div>

                        <button type="submit" class="w-full py-3.5 mt-2 text-white font-bold rounded-xl shadow-lg transition-all transform active:scale-[0.98] uppercase tracking-wider text-sm" style="background: linear-gradient(90deg, var(--gradient-from), var(--gradient-to)); box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                            MASUK SEKARANG
                        </button>
                    </form>

                    <div class="text-center mt-4 text-slate-400 text-sm" style="color: var(--text-secondary);">
                        Belum punya akun? <a href="{{ route('register') }}" class="text-amber-400 font-bold hover:underline" style="color: var(--accent);">Daftar Warga</a>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="mt-6 bg-red-950/40 border border-red-900/60 rounded-xl p-4 text-red-400 text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <span>{{ $error }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Toggle Login Password Visibility (only via eye icon)
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
            setTimeout(() => { window.loginEyeClicked = false; }, 350);
        }

        // Ensure clicking or focusing the password field does NOT reveal it — only the eye button does
        (function() {
            const input = document.getElementById('loginPassword');
            if (!input) return;
            input.addEventListener('focus', function() {
                if (!window.loginEyeClicked) {
                    this.type = 'password';
                    const eyeIcon = document.getElementById('loginEyeIcon');
                    const eyeOffIcon = document.getElementById('loginEyeOffIcon');
                    if (eyeIcon) eyeIcon.style.display = 'block';
                    if (eyeOffIcon) eyeOffIcon.style.display = 'none';
                }
            });
        })();

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
        document.getElementById('themeToggle').addEventListener('click', toggleTheme);

        // Initialize on page load
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initTheme);
        } else {
            initTheme();
        }
    </script>
</body>
</html>
