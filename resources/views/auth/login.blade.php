<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kas RT — Masuk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <style>
        :root{--accent:#D1A151;--bg-1:#031022;--bg-2:#042e55}
        body { font-family: 'Plus Jakarta Sans', sans-serif; color: #e6eef6; position: relative; }
        .bg-animated{background: linear-gradient(180deg,#031022 0%, #041d34 40%, #042e55 100%);background-size:300% 300%;animation: moveGrad 15s ease infinite;}
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
        .left-panel, .right-panel { padding: 3rem 2.5rem; display:flex; align-items:center; justify-content:center; min-height:520px; }
        .left-panel { background: linear-gradient(180deg, #020b13 0%, #071522 100%); }
        .left-panel .brand-title { color: #c69d4d; }
        .left-panel p { color: #9fb4c3; }
        .right-panel { background: linear-gradient(180deg, rgba(5,12,25,0.92) 0%, rgba(10,24,46,0.98) 100%); border-left: 1px solid rgba(255,255,255,0.08); position: relative; }
        .right-panel::before { content: ''; position: absolute; inset: 0; background: radial-gradient(circle at top right, rgba(209,161,81,0.04), transparent 25%); pointer-events: none; }
        .panel-inner { width: 100%; max-width: 520px; position: relative; z-index: 1; }
        .form-card { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08); backdrop-filter: blur(20px); border-radius: 28px; padding: 2rem; box-shadow: 0 24px 60px rgba(0,0,0,0.18); }
        .form-card label { color: #f8fafc; }
        .form-card input { width: 100%; border-radius: 1rem; padding: 0.85rem 1rem; background: rgba(243,246,255,0.95); color: #0f172a; border: 1px solid rgba(209,161,81,0.3); }
        .form-card input::placeholder { color: rgba(15,23,42,0.45); }
        .form-card input:focus { outline: none; box-shadow: 0 0 0 4px rgba(209,161,81,0.16); border-color: rgba(209,161,81,0.55); }
        .form-card button { width: 100%; border-radius: 1rem; padding: 0.95rem 1rem; }
        .form-card .helper { color: #cbd5e1; }
        .brand-title { color: #D1A151; font-family: 'Playfair Display', serif; letter-spacing: -0.035em; text-shadow: 0 4px 20px rgba(0,0,0,0.25); }
        @media (max-width:1024px) {.card{grid-template-columns:1fr;} .left-panel,.right-panel{padding:2rem;} .form-card{max-width:100%;}}
        @media (max-width:640px){body{padding:1rem;} .left-panel{padding:1.5rem;} .right-panel{padding:1.5rem;} .brand-title{text-align:center;} }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6 bg-animated">
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

            <h2 class="brand-title text-5xl font-bold tracking-wide italic -mt-1">Kas RT</h2>
            <p class="mt-1 text-slate-400 text-xs tracking-widest uppercase">Solusi Pintar Pengelolaan Iuran</p>
        </div>
        </div>

        <div class="right-panel p-12 flex items-center">
            <div class="panel-inner">
                <div class="form-card">
                    <form action="{{ route('login') }}" method="POST" class="space-y-5">
                        @csrf

                        <div>
                            <label class="text-slate-400 text-xs font-semibold mb-2 block uppercase tracking-wider">Alamat Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                                class="w-full rounded-xl py-3 px-5 bg-slate-900/60 text-white placeholder-slate-600 border border-slate-700/60 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all"
                                placeholder="admin@rt.com">
                            @error('email')
                                <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="text-slate-400 text-xs font-semibold mb-2 block uppercase tracking-wider">Password</label>
                            <input type="password" name="password" required
                                class="w-full rounded-xl py-3 px-5 bg-slate-900/60 text-white placeholder-slate-600 border border-slate-700/60 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all"
                                placeholder="••••••••">
                            @error('password')
                                <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between pt-1">
                            <div class="flex items-center">
                                <input id="remember" type="checkbox" name="remember" class="w-4 h-4 mr-3 accent-amber-500 rounded">
                                <label for="remember" class="text-slate-300 font-semibold text-sm select-none">Remember Me</label>
                            </div>
                            <div>
                                <a href="{{ route('password.request') }}" class="text-amber-400 text-sm hover:underline">Lupa Password?</a>
                            </div>
                        </div>

                        <button type="submit" class="w-full py-3.5 mt-2 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-bold rounded-xl shadow-lg shadow-orange-950/30 transition-all transform active:scale-[0.98] uppercase tracking-wider text-sm">
                            MASUK SEKARANG
                        </button>
                    </form>

                    <div class="text-center mt-4 text-slate-400 text-sm">
                        Belum punya akun? <a href="{{ route('register') }}" class="text-amber-400 font-bold hover:underline">Daftar Warga</a>
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
</body>
</html>
