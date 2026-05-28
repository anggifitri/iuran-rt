<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password | Digital Tapestry</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #0e2430; }
        .card { width: 920px; border-radius: 24px; overflow: hidden; box-shadow: 0 30px 60px rgba(2,6,23,0.6); }
        .left-panel { background: linear-gradient(180deg,#050d1a,#0b1424); }
        .right-panel { background: linear-gradient(180deg,#1e3a5f,#0f172a); }
        .brand-title { color: #D1A151; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-8">
    <div class="card flex">
        <div class="left-panel w-1/2 p-16 flex flex-col items-center justify-center text-center">
            <div class="mb-6 text-[#D1A151] filter drop-shadow-[0_10px_20px_rgba(209,161,81,0.3)]">
                <svg xmlns="http://www.w3.org/2000/svg" width="160" height="160" viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M50 10 L15 45 L22 45 L50 17 L78 45 L85 45 Z" fill="currentColor" opacity="0.15"/>
                    <path d="M50 10 L15 45 H25 L50 20 L75 45 H85 Z" fill="currentColor"/>
                    <path d="M25 45 V85 H75 V45 Z" stroke="currentColor" stroke-width="2"/>
                    <path d="M43 85 V65 H57 V85 Z" fill="currentColor" opacity="0.2" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M32 52 L42 62 M32 62 L42 52" stroke="currentColor" stroke-width="1" opacity="0.7"/>
                    <path d="M32 70 L42 80 M32 80 L42 70" stroke="currentColor" stroke-width="1" opacity="0.7"/>
                    <path d="M58 52 L68 62 M58 62 L68 52" stroke="currentColor" stroke-width="1" opacity="0.7"/>
                    <path d="M58 70 L68 80 M58 80 L68 70" stroke="currentColor" stroke-width="1" opacity="0.7"/>
                    <path d="M46 25 L50 21 L54 25 L50 29 Z" fill="currentColor"/>
                    <path d="M40 32 L50 42 L60 32" stroke="currentColor" stroke-width="1.2"/>
                    <path d="M45 32 L50 37 L55 32" stroke="currentColor" stroke-width="1"/>
                </svg>
            </div>
            <h2 class="brand-title text-5xl font-bold tracking-wide">Lupa Password</h2>
            <p class="mt-3 text-slate-400 text-sm tracking-wider uppercase">Masukkan email dan password baru tanpa kirim link</p>
        </div>
        <div class="right-panel w-1/2 p-12 flex items-center">
            <div class="w-full max-w-md mx-auto">
                @if (session('status'))
                    <div class="mb-6 rounded-xl bg-emerald-600/10 border border-emerald-500/20 p-4 text-emerald-200">
                        {{ session('status') }}
                    </div>
                @endif
                <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-slate-200 mb-2">Email Terdaftar</label>
                        <input type="email" name="email" required autofocus
                            class="w-full rounded-xl py-3 px-5 bg-slate-900/60 text-white placeholder-slate-500 border border-slate-700/70 focus:outline-none focus:border-amber-500 transition-all"
                            placeholder="admin@rt.com">
                        @error('email')
                            <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-200 mb-2">Password Baru</label>
                        <input type="password" name="password" required
                            class="w-full rounded-xl py-3 px-5 bg-slate-900/60 text-white placeholder-slate-500 border border-slate-700/70 focus:outline-none focus:border-amber-500 transition-all"
                            placeholder="Masukkan password baru">
                        @error('password')
                            <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-200 mb-2">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" required
                            class="w-full rounded-xl py-3 px-5 bg-slate-900/60 text-white placeholder-slate-500 border border-slate-700/70 focus:outline-none focus:border-amber-500 transition-all"
                            placeholder="Ulangi password baru">
                    </div>
                    <button type="submit" class="w-full py-3 bg-gradient-to-r from-amber-500 to-orange-500 text-white font-bold rounded-xl shadow-lg hover:from-amber-600 hover:to-orange-600 transition-all">Simpan Password Baru</button>
                </form>
                <div class="text-center mt-6 text-slate-300 text-sm">
                    Ingat password? <a href="{{ route('login') }}" class="text-amber-400 font-semibold hover:underline">Kembali ke Login</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
