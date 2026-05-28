<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Tapestry — Masuk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #0e2430; }
        .card { width: 920px; border-radius: 24px; overflow: hidden; box-shadow: 0 30px 60px rgba(2,6,23,0.6); }
        .left-panel { background: linear-gradient(180deg,#050d1a,#0b1424); }
        .right-panel { background: linear-gradient(180deg,#1e3a5f,#0f172a); }
        .brand-title { font-family: 'Playfair Display', serif; color: #D1A151; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-8">
    <div class="card flex">
        <div class="left-panel w-1/2 p-16 flex flex-col items-center justify-center text-center relative">

            <div class="mb-6 text-[#D1A151] filter drop-shadow-[0_10px_25px_rgba(209,161,81,0.4)]">
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

            <h2 class="brand-title text-5xl font-bold tracking-wide italic">Kas RT</h2>
            <p class="mt-4 text-slate-400 text-xs tracking-widest uppercase">Solusi Pintar Pengelolaan Iuran</p>
        </div>

        <div class="right-panel w-1/2 p-12 flex items-center">
            <div class="w-full max-w-md mx-auto">

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
                            <a href="#" class="text-amber-400 text-sm hover:underline">Lupa Password?</a>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3.5 mt-2 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-bold rounded-xl shadow-lg shadow-orange-950/30 transition-all transform active:scale-[0.98] uppercase tracking-wider text-sm">
                        MASUK SEKARANG
                    </button>

                    <div class="text-center mt-4 text-slate-400 text-sm">
                        Belum punya akun? <a href="{{ route('register') }}" class="text-amber-400 font-bold hover:underline">Daftar Warga</a>
                    </div>
                </form>

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
