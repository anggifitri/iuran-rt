<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang — Kas RT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <style>
        :root{--accent:#D1A151;--bg-1:#031221;--bg-2:#072433}
        body { font-family: 'Plus Jakarta Sans', sans-serif; color: #e6eef6; }

        /* animated gradient background (navy, darker) */
        .bg-animated{background: linear-gradient(180deg,#031022 0%, #041d34 40%, #042e55 100%);background-size:300% 300%;animation: moveGrad 15s ease infinite;}
        @keyframes moveGrad{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}

        body{position:relative;}
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

        .glass { background: linear-gradient(180deg, rgba(255,255,255,0.01), rgba(255,255,255,0.005)); border: 1px solid rgba(255,255,255,0.02); backdrop-filter: blur(10px); }

        /* float / entrance animations */
        .float-anim{animation: floaty 6s ease-in-out infinite}
        @keyframes floaty{0%{transform:translateY(0)}50%{transform:translateY(-8px)}100%{transform:translateY(0)}}
        .fade-up{opacity:0;transform:translateY(12px);animation:fadeUp .9s cubic-bezier(.2,.8,.2,1) forwards}
        .delay-1{animation-delay:.12s}.delay-2{animation-delay:.22s}.delay-3{animation-delay:.36s}.delay-4{animation-delay:.48s}
        @keyframes fadeUp{to{opacity:1;transform:none}}

        .hero-title{font-family:'Playfair Display', serif}
        .feature-title{font-family:'Playfair Display', serif;letter-spacing:0.02em;text-transform:none;line-height:1.05;}

        /* CTA */
        .cta-primary{background:linear-gradient(90deg,var(--accent),#fb923c);box-shadow:0 10px 30px rgba(209,161,81,0.12);}
        .cta-primary:hover{transform:translateY(-4px) scale(1.02)}
        .cta-ghost{border-color:rgba(148,163,184,0.14)}

        .feature-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:0.85rem;margin-top:1.5rem;}
        .feature-card{background:linear-gradient(180deg, rgba(255,255,255,0.08), rgba(255,255,255,0.03));border:1px solid rgba(255,255,255,0.12);backdrop-filter:blur(18px);border-radius:1.8rem;padding:1.25rem 1.2rem 1.2rem;text-align:left;box-shadow:0 16px 40px rgba(0,0,0,0.1);transition:transform .25s ease,background .25s ease;display:flex;flex-direction:column;justify-content:space-between;min-height:150px;}
        .feature-card:hover{background:linear-gradient(180deg, rgba(255,255,255,0.12), rgba(255,255,255,0.04));transform:translateY(-2px);}
        .feature-item{display:flex;align-items:flex-start;gap:0.85rem;}
        .feature-icon{width:2.6rem;height:2.6rem;min-width:2.6rem;display:grid;place-items:center;border-radius:9999px;background:rgba(209,161,81,0.18);color:#D1A151;box-shadow:0 10px 20px rgba(209,161,81,0.08);}
        .feature-card h4{font-size:1rem;font-weight:700;color:#f8fafc;margin-bottom:0.35rem;line-height:1.35;}
        .feature-card p{margin:0;color:#cbd5e1;font-size:0.92rem;line-height:1.65;}
        @media (max-width:768px){.feature-grid{grid-template-columns:1fr;}.feature-card{min-height:auto;}}        /* decorative shapes (muted) */
        .shape-spark{position:absolute;right:10%;top:12%;width:120px;height:120px;border-radius:999px;background:radial-gradient(circle at 30% 30%, rgba(209,161,81,0.06), transparent 40%);filter:blur(18px);opacity:.45}

        /* responsive tweaks */
        @media (max-width:768px){.max-w-6xl{padding:0 1rem}.hero-title{font-size:2.2rem}}
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6 bg-animated">
    <div class="stars"></div>
    <div class="max-w-6xl w-full rounded-3xl overflow-hidden shadow-2xl grid md:grid-cols-2 grid-cols-1" style="background: linear-gradient(180deg, #041026 0%, #052f4a 35%, #042b44 100%); border: 1px solid rgba(255,255,255,0.03);">
        <div class="relative p-12 md:p-16 flex flex-col justify-center" style="background: transparent;">
            <div class="absolute -left-16 -top-16 opacity-10">
                <svg width="300" height="300" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="60" cy="60" r="60" fill="#0b2633" />
                    <rect x="90" y="90" width="90" height="90" fill="#092735" />
                </svg>
            </div>

            <div class="shape-spark" aria-hidden="true"></div>

            <div class="mb-6 mx-auto text-amber-400 filter drop-shadow-[0_10px_25px_rgba(209,161,81,0.4)] float-anim fade-up delay-1" style="width:160px;height:160px;">
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

            <h1 class="hero-title text-5xl md:text-6xl font-extrabold text-white tracking-tight mb-4 fade-up delay-2">Kas RT — Solusi Iuran<br class="hidden md:block">Warga & Pengurus</h1>
            <p class="text-slate-300 max-w-xl text-lg mb-8 fade-up delay-3">Kelola iuran, pembayaran, dan laporan RT Anda dengan aman dan efisien. Antarmuka modern, ringkas, dan mudah dipahami untuk pengurus dan warga.</p>

            <div class="flex flex-wrap gap-4 items-center fade-up delay-4">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-3 py-3 px-6 cta-primary rounded-full text-white font-semibold shadow-lg transform transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7"/></svg>
                    Masuk
                </a>

                <a href="{{ route('register') }}" class="inline-flex items-center gap-3 py-3 px-6 border border-slate-600 text-slate-200 rounded-full hover:bg-slate-800 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM6 21v-2a4 4 0 014-4h4a4 4 0 014 4v2"/></svg>
                    Daftar Warga
                </a>

                <a href="{{ route('password.request') }}" class="ml-2 text-sm text-slate-300 underline">Lupa password?</a>
            </div>

            <div class="mt-8 text-slate-400 text-sm">Dipersonalisasi untuk RT Anda — aman dan mudah digunakan.</div>
        </div>

        <div class="p-10 flex items-center justify-center glass relative" style="background: rgba(5,18,35,0.55); border: 1px solid rgba(255,255,255,0.03);">
            <div class="absolute left-6 top-6 w-36 h-36 rounded-full bg-gradient-to-br from-amber-600/8 to-transparent blur-3xl opacity-35"></div>
            <div class="relative max-w-md text-center">
                <h3 class="feature-title text-2xl md:text-3xl font-semibold text-white mb-2 fade-up delay-2">Fitur Unggulan</h3>
                <p class="text-slate-400 mb-6 fade-up delay-2">Satu ruang kerja RT yang rapi untuk mencatat iuran, mengelola warga, dan mencetak laporan tanpa ribet.</p>
                <div class="feature-grid fade-up delay-3">
                    <div class="feature-card">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                            <div>
                                <h4>Kelola Iuran & Pembayaran</h4>
                                <p>Catat, pantau, dan rekap transaksi warga dengan jelas dan cepat.</p>
                            </div>
                        </div>
                    </div>
                    <div class="feature-card">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 8v4l3 3" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="9" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                            <div>
                                <h4>Cetak Laporan Cepat</h4>
                                <p>Unduh laporan kas dan iuran langsung tanpa menunggu.</p>
                            </div>
                        </div>
                    </div>
                    <div class="feature-card">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0z"/><path d="M6 21v-2a4 4 0 014-4h4a4 4 0 014 4v2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                            <div>
                                <h4>Kelola Data Warga</h4>
                                <p>Simpan data anggota RT dan profil penting dengan aman.</p>
                            </div>
                        </div>
                    </div>
                    <div class="feature-card">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 11c1.656 0 3-1.344 3-3s-1.344-3-3-3-3 1.344-3 3 1.344 3 3 3z"/><path d="M5.859 17.841A5.99 5.99 0 0112 15c2.692 0 4.987 1.737 5.859 4.207"/><path d="M12 22c-4.418 0-8-3.582-8-8 0-.73.1-1.438.284-2.115" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                            <div>
                                <h4>Reset Password Mudah</h4>
                                <p>Ubah password langsung tanpa proses email panjang.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-8 text-sm text-slate-400 fade-up delay-4">Jika Anda admin, klik <span class="text-amber-400 font-semibold">Masuk</span> untuk mulai mengelola.</div>
            </div>

            <div class="absolute right-8 bottom-8 opacity-30">
                <svg width="120" height="120" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="8" y="8" width="84" height="84" rx="14" stroke="#08323b" stroke-width="2" fill="#05242d"/></svg>
            </div>
        </div>
    </div>
</body>
</html>
