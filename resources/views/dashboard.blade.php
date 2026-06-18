@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    /* Dashboard Light Mode Support */
    html[data-theme="light"] .stat-card {
        background: linear-gradient(135deg, #f0f4ff 0%, #faf5ff 100%) !important;
        border-color: rgba(99, 102, 241, 0.2) !important;
    }

    html[data-theme="light"] .stat-card p {
        color: #64748b !important;
    }

    html[data-theme="light"] .card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%) !important;
        border-color: rgba(99, 102, 241, 0.1) !important;
    }

    html[data-theme="light"] .text-white {
        color: #1e293b !important;
    }

    html[data-theme="light"] .text-white-50 {
        color: #94a3b8 !important;
    }

    html[data-theme="light"] .fw-bold {
        color: #1e293b !important;
    }

    html[data-theme="light"] .badge {
        background: linear-gradient(135deg, #6366f1, #8b5cf6) !important;
    }

    html[data-theme="light"] .rounded {
        background: rgba(99, 102, 241, 0.05) !important;
    }

    html[data-theme="light"] [style*="rgba(255,255,255,0.03)"] {
        background: rgba(99, 102, 241, 0.03) !important;
    }

    html[data-theme="light"] [style*="rgba(255,255,255,0.08)"] {
        background: rgba(99, 102, 241, 0.08) !important;
    }

    html[data-theme="light"] [style*="rgba(255,255,255,0.14)"] {
        background: rgba(99, 102, 241, 0.1) !important;
    }

    html[data-theme="light"] [style*="rgba(148,163,184,0.12)"] {
        border-color: rgba(99, 102, 241, 0.15) !important;
    }

    html[data-theme="light"] [style*="rgba(148,163,184,0.14)"] {
        border-color: rgba(99, 102, 241, 0.15) !important;
    }

    html[data-theme="light"] [style*="rgba(148,163,184,0.18)"] {
        background: rgba(99, 102, 241, 0.12) !important;
    }

    html[data-theme="light"] h4, 
    html[data-theme="light"] h5, 
    html[data-theme="light"] h6 {
        color: #1e293b !important;
    }

    html[data-theme="light"] p {
        color: #64748b !important;
    }

    .sidebar .brand-icon i {
        color: var(--primary) !important;
    }

    html[data-theme="light"] .sidebar .brand-icon i {
        color: #6366f1 !important;
    }
</style>

<div class="row">
    <div class="col-xl-3 col-lg-4">
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-icon">
                    <i class="fas fa-house-chimney" style="color: var(--primary);"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-2" style="color: var(--primary); font-family: serif; font-style: italic; padding: 0; line-height: 1;">Kas RT</h5>

                    <p class="mb-0" style="color: var(--primary); font-size: 1.0rem; line-height: 1.4; font-family: 'Times New Roman', Times, serif;">Solusi Pintar Pengelolaan Iuran Anda</p>
                </div>
            </div>

            <div class="menu-section">
                <p class="menu-title">Navigasi</p>
                <ul class="nav flex-column">
                    <li class="nav-item mb-2">
                        <a class="nav-link active rounded" href="{{ route('dashboard') }}">
                            <i class="fas fa-tachometer-alt me-3"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link rounded" href="{{ route('warga.index') }}">
                            <i class="fas fa-users me-3"></i> Data Warga
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link rounded" href="{{ route('pembayaran.index') }}">
                            <i class="fas fa-money-bill-wave me-3"></i> Transaksi Kas
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link rounded" href="{{ route('laporan.index') }}">
                            <i class="fas fa-file-pdf me-3"></i> Laporan Bulanan
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link rounded" href="{{ route('laporan.iuran') }}">
                            <i class="fas fa-file-alt me-3"></i> Laporan Iuran
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link rounded d-flex align-items-center text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt me-3"></i> Keluar
                        </a>
                    </li>
                </ul>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </aside>
    </div>

    <div class="col-xl-9 col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0" style="color: var(--text-main);">Selamat datang, {{ $user->name }} 👋</h4>
                <p style="color: var(--text-muted); font-size: 0.9rem;">Kelola keuangan RT {{ $user->rt_number ?? '-' }} dengan transparan</p>
            </div>
            <div class="p-2 px-3 rounded shadow-sm" style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-main);">
                <i class="far fa-calendar-alt me-2" style="color: var(--primary);"></i>
                {{ date('l, d M Y') }}
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card p-3" style="background: linear-gradient(135deg, #111827 0%, #1f2937 100%); border-radius: 20px; border: 1px solid rgba(148, 163, 184, 0.12); box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12);">
                    <p class="mb-2 text-white-50" style="font-size: 0.8rem;">Total Pemasukan</p>
                    <h5 class="fw-bold mb-0" style="color: #22c55e;">Rp {{ number_format($pemasukan, 0, ',', '.') }}</h5>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card p-3" style="background: linear-gradient(135deg, #111827 0%, #1f2937 100%); border-radius: 20px; border: 1px solid rgba(248, 113, 113, 0.16); box-shadow: 0 18px 40px rgba(190, 18, 60, 0.08);">
                    <p class="mb-2 text-white-50" style="font-size: 0.8rem;">Total Pengeluaran</p>
                    <h5 class="fw-bold mb-0" style="color: #fb7185;">Rp {{ number_format($pengeluaran, 0, ',', '.') }}</h5>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card p-3" style="background: linear-gradient(135deg, #111827 0%, #1f2937 100%); border-radius: 20px; border: 1px solid rgba(14, 165, 233, 0.14); box-shadow: 0 18px 40px rgba(14, 165, 233, 0.08);">
                    <p class="mb-2 text-white-50" style="font-size: 0.8rem;">Saldo Kas</p>
                    <h5 class="fw-bold mb-0" style="color: #38bdf8;">Rp {{ number_format($saldo, 0, ',', '.') }}</h5>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card p-3" style="background: linear-gradient(135deg, #111827 0%, #1f2937 100%); border-radius: 20px; border: 1px solid rgba(148, 163, 184, 0.12); box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12);">
                    <p class="mb-2 text-white-50" style="font-size: 0.8rem;">Total Warga</p>
                    <h5 class="fw-bold mb-0" style="color: #f8fafc;">{{ $totalWarga }} Orang</h5>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-5">
                <div class="card h-100" style="background: linear-gradient(135deg, #2563eb 0%, #0ea5e9 100%); border: none; border-radius: 25px; padding: 30px; color: white; position: relative; overflow: hidden; min-height: 220px;">
                    <p class="mb-2 text-white-50" style="letter-spacing: .05em; text-transform: uppercase; font-size: .78rem;">Saldo Akhir</p>
                    <h1 class="fw-bold mb-4">Rp {{ number_format($saldo, 0, ',', '.') }}</h1>
                    <div class="d-flex align-items-center gap-2 px-3 py-2 rounded" style="background: rgba(255,255,255,0.14); width: fit-content;">
                        <i class="fas fa-wallet" style="font-size: 1rem;"></i>
                        <span class="small">Kas RT Aktif</span>
                    </div>
                    <div style="position:absolute; right:-20px; top:-20px; width:120px; height:120px; background: rgba(255,255,255,0.12); border-radius: 50%;"></div>
                </div>
            </div>
            <div class="col-md-7">
                <div class="card h-100 p-3" style="background: linear-gradient(180deg, rgba(15,23,42,0.96) 0%, rgba(30,41,59,0.96) 100%); border: 1px solid rgba(148, 163, 184, 0.12); border-radius: 25px; min-height: 220px;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0 text-white">Grafik Arus Kas</h6>
                        <span class="badge text-white" style="background: linear-gradient(135deg, #38bdf8, #0ea5e9);">6 Bulan</span>
                    </div>
                    <div style="height: 220px;">
                        <canvas id="kasChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card p-3" style="background: linear-gradient(135deg, rgba(15,23,42,0.96) 0%, rgba(31,41,55,0.96) 100%); border: 1px solid rgba(148, 163, 184, 0.14); border-radius: 20px;">
                    <h6 class="fw-bold mb-3 text-white" style="font-size: 0.9rem;">Komposisi Gender Warga</h6>
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:110px; height:110px; flex-shrink: 0;">
                            <canvas id="genderChart"></canvas>
                        </div>
                        <div>
                            <p class="mb-0 small text-white-50">Perempuan</p>
                            <h6 class="mb-2 text-white fw-bold">{{ $femaleCount }}</h6>
                            <p class="mb-0 small text-white-50">Laki-laki</p>
                            <h6 class="mb-0 text-white fw-bold">{{ $maleCount }}</h6>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="card h-100 p-4" style="background: linear-gradient(135deg, rgba(15,23,42,0.96) 0%, rgba(31,41,55,0.96) 100%); border: 1px solid rgba(148, 163, 184, 0.14); border-radius: 25px;">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h6 class="fw-bold mb-1 text-white">Pengumuman Terbaru</h6>
                            <p class="text-white-50 mb-0">Gunakan pemberitahuan ini untuk ringkasan pengeluaran dan pengingat iuran bulan ini.</p>
                        </div>
                        <div style="width:44px; height:44px; border-radius:14px; background: rgba(255,255,255,0.08); display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-bullhorn text-white"></i>
                        </div>
                    </div>
                    <div class="d-grid gap-3">
                        <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(148,163,184,0.12); border-radius: 22px; padding: 18px;">
                            <div class="d-flex align-items-start gap-3 mb-2">
                                <div style="width:32px; height:32px; border-radius:12px; background: rgba(148,163,184,0.18); display:flex; align-items:center; justify-content:center; color:#ffffff; font-weight:700;">1</div>
                                <div>
                                    <h6 class="mb-1 text-white">Pengeluaran Terbaru</h6>
                                    <p class="small text-white-50 mb-0">{{ $latestExpenseDate ?? 'Tanggal tidak tersedia' }}</p>
                                </div>
                            </div>
                            <p class="text-white-50 small mb-0">Rp {{ number_format($latestExpenseAmount, 0, ',', '.') }} - Pastikan semua warga ingat untuk membayar iuran bulan ini.</p>
                        </div>
                        @forelse($pengumuman as $index => $p)
                            <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(148,163,184,0.12); border-radius: 22px; padding: 18px;">
                                <div class="d-flex align-items-start gap-3 mb-2">
                                    <div style="width:32px; height:32px; border-radius:12px; background: rgba(148,163,184,0.18); display:flex; align-items:center; justify-content:center; color:#ffffff; font-weight:700;">{{ $index + 2 }}</div>
                                    <div>
                                        <h6 class="mb-1 text-white">{{ $p->title }}</h6>
                                        <p class="small text-white-50 mb-0">{{ $p->created_at->format('d M Y') }}</p>
                                    </div>
                                </div>
                                <p class="text-white-50 small mb-0">{{ Str::limit($p->content, 120) }}</p>
                            </div>
                        @empty
                            <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(148,163,184,0.12); border-radius: 22px; padding: 18px;">
                                <p class="text-white-50 mb-0">Belum ada pengumuman untuk ditampilkan.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('kasChart').getContext('2d');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartData->pluck('bulan_tahun')) !!},
                datasets: [{
                    label: 'Pemasukan (+)',
                    data: {!! json_encode($chartData->pluck('masuk')) !!},
                    borderColor: '#34D399',
                    backgroundColor: 'rgba(52, 211, 153, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Pengeluaran (-)',
                    data: {!! json_encode($chartData->pluck('keluar')) !!},
                    borderColor: '#F87171',
                    backgroundColor: 'rgba(248, 113, 113, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        labels: { color: '#F8FAFC', font: { family: 'Inter' } }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) { label += ': '; }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#94A3B8',
                            callback: function(value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        },
                        grid: { color: '#334155' }
                    },
                    x: {
                        grid: { color: 'transparent' },
                        ticks: { color: '#94A3B8' }
                    }
                }
            }
        });

        const gCtx = document.getElementById('genderChart')?.getContext('2d');
        if (gCtx) {
            const female = {!! json_encode($femaleCount ?? 0) !!};
            const male = {!! json_encode($maleCount ?? 0) !!};
            const total = (female + male) || 1;
            new Chart(gCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Perempuan', 'Laki-laki'],
                    datasets: [{
                        data: [female, male],
                        backgroundColor: ['#FB7185', '#60A5FA'],
                        hoverBackgroundColor: ['#f43f5e', '#3b82f6']
                    }]
                },
                options: {
                    cutout: '55%',
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        }
    });
</script>
@endsection
