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
                    <h5 class="fw-bold mb-2" style="color: var(--primary); font-family: serif; font-style: italic; padding: 0; line-height: 1;">NexaNest</h5>

                    <p class="mb-0" style="color: var(--primary); font-size: 1.0rem; line-height: 1.4; font-family: 'Times New Roman', Times, serif;">Platform Digital RW 018 untuk Kas, Surat, Posyandu & UMKM</p>
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
                        <a class="nav-link rounded d-flex align-items-center" data-bs-toggle="collapse" href="#layananMenu" role="button" aria-expanded="false">
                            <i class="fas fa-th-large me-3"></i> Layanan
                            <i class="fas fa-chevron-down ms-auto"></i>
                        </a>
                        <div class="collapse" id="layananMenu">
                            <ul class="nav flex-column ms-3">
                                <li class="nav-item"><a class="nav-link rounded" href="{{ route('surat.index') }}"><i class="fas fa-file-alt me-2"></i> Penerbitan Surat</a></li>
                                <li class="nav-item"><a class="nav-link rounded" href="{{ route('pengaduan.index') }}"><i class="fas fa-phone me-2"></i> Pengaduan Warga</a></li>
                                <li class="nav-item"><a class="nav-link rounded" href="{{ route('posyandu.index') }}"><i class="fas fa-heart me-2"></i> Info Posyandu</a></li>
                                <li class="nav-item"><a class="nav-link rounded" href="{{ route('umkm.index') }}"><i class="fas fa-store me-2"></i> Direktori UMKM</a></li>
                            </ul>
                        </div>
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

        @if($user->isAdmin())
            <div class="row g-3 mb-4">
                <div class="col-12">
                    <div class="card p-4" style="border-radius: 20px;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="fw-bold mb-1" style="color: var(--text-main);">Ringkasan Admin</h5>
                                <p class="mb-0 text-muted">Data riil dari seluruh fitur NexaNest RW 018.</p>
                            </div>
                            <div class="d-flex gap-2">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1" style="font-size: 0.7rem;"><i class="fas fa-user-shield me-1"></i>{{ $totalAdminRW }} Admin RW</span>
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1" style="font-size: 0.7rem;"><i class="fas fa-user-tie me-1"></i>{{ $totalAdminRT }} Admin RT</span>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-6 col-md-3">
                                <div class="card p-3 stat-card" style="border-radius: 18px; background: var(--bg-card); border: 1px solid var(--border-color);">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="mb-1 text-muted" style="font-size: 0.75rem;">Pengajuan Surat</p>
                                            <h4 class="fw-bold mb-0">{{ $newFeatureCounts['surat'] }}</h4>
                                        </div>
                                        <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width:38px;height:38px;"><i class="fas fa-file-signature"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="card p-3 stat-card" style="border-radius: 18px; background: var(--bg-card); border: 1px solid var(--border-color);">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="mb-1 text-muted" style="font-size: 0.75rem;">Pengaduan Warga</p>
                                            <h4 class="fw-bold mb-0">{{ $newFeatureCounts['pengaduan'] }}</h4>
                                        </div>
                                        <div class="rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center" style="width:38px;height:38px;"><i class="fas fa-exclamation-circle"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="card p-3 stat-card" style="border-radius: 18px; background: var(--bg-card); border: 1px solid var(--border-color);">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="mb-1 text-muted" style="font-size: 0.75rem;">Jadwal Posyandu</p>
                                            <h4 class="fw-bold mb-0">{{ $newFeatureCounts['posyandu'] }}</h4>
                                        </div>
                                        <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width:38px;height:38px;"><i class="fas fa-heartbeat"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="card p-3 stat-card" style="border-radius: 18px; background: var(--bg-card); border: 1px solid var(--border-color);">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="mb-1 text-muted" style="font-size: 0.75rem;">UMKM Terdaftar</p>
                                            <h4 class="fw-bold mb-0">{{ $newFeatureCounts['umkm'] }}</h4>
                                        </div>
                                        <div class="rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center" style="width:38px;height:38px;"><i class="fas fa-store"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

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
                    <h5 class="fw-bold mb-0" style="color: #f8fafc;">{{ $totalJiwa }} Jiwa</h5>
                    <small class="text-white-50" style="font-size: 0.72rem;">{{ $totalKK }} Kepala Keluarga</small>
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
                        <span class="small">NexaNest Aktif</span>
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
                            <h6 class="fw-bold mb-1 text-white">Info & Pengumuman Terbaru</h6>
                            <p class="text-white-50 mb-0">Informasi penting dan update terkini untuk warga RW 018.</p>
                        </div>
                        <div style="width:44px; height:44px; border-radius:14px; background: rgba(255,255,255,0.08); display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-bell text-white"></i>
                        </div>
                    </div>
                    <div class="row g-3">
                        {{-- Card 1: Pengingat Iuran --}}
                        <div class="col-md-6">
                            <div style="background: linear-gradient(135deg, rgba(239,68,68,0.12) 0%, rgba(239,68,68,0.04) 100%); border: 1px solid rgba(239,68,68,0.2); border-radius: 18px; padding: 16px;">
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <div style="width:36px; height:36px; border-radius:12px; background: rgba(239,68,68,0.2); display:flex; align-items:center; justify-content:center;">
                                        <i class="fas fa-money-bill-wave" style="color: #f87171;"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-white" style="font-size: 0.85rem;">Pengingat Iuran Bulanan</h6>
                                        <small class="text-white-50">{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</small>
                                    </div>
                                </div>
                                <p class="text-white-50 small mb-1">Sudah bayar: <span class="text-success fw-bold">{{ $paidThisMonth }} KK</span> dari {{ $totalKKForReminder }} KK</p>
                                <p class="mb-0"><span class="badge bg-danger rounded-pill px-2">{{ $unpaidKK }} KK belum bayar</span></p>
                            </div>
                        </div>
                        {{-- Card 2: Info Posyandu --}}
                        <div class="col-md-6">
                            <div style="background: linear-gradient(135deg, rgba(16,185,129,0.12) 0%, rgba(16,185,129,0.04) 100%); border: 1px solid rgba(16,185,129,0.2); border-radius: 18px; padding: 16px;">
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <div style="width:36px; height:36px; border-radius:12px; background: rgba(16,185,129,0.2); display:flex; align-items:center; justify-content:center;">
                                        <i class="fas fa-heartbeat" style="color: #34d399;"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-white" style="font-size: 0.85rem;">Info Posyandu</h6>
                                        <small class="text-white-50">Jadwal terkini</small>
                                    </div>
                                </div>
                                @if($latestPosyandu)
                                    <p class="text-white-50 small mb-1">{{ $latestPosyandu->judul ?? $latestPosyandu->kegiatan ?? 'Posyandu Rutin' }}</p>
                                    <p class="mb-0"><span class="badge bg-success rounded-pill px-2">{{ \Carbon\Carbon::parse($latestPosyandu->tanggal)->translatedFormat('d F Y') }}</span></p>
                                @else
                                    <p class="text-white-50 small mb-0">Belum ada jadwal posyandu tersedia.</p>
                                @endif
                            </div>
                        </div>
                        {{-- Card 3: Pengeluaran Terbaru --}}
                        <div class="col-md-6">
                            <div style="background: linear-gradient(135deg, rgba(251,146,60,0.12) 0%, rgba(251,146,60,0.04) 100%); border: 1px solid rgba(251,146,60,0.2); border-radius: 18px; padding: 16px;">
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <div style="width:36px; height:36px; border-radius:12px; background: rgba(251,146,60,0.2); display:flex; align-items:center; justify-content:center;">
                                        <i class="fas fa-receipt" style="color: #fb923c;"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-white" style="font-size: 0.85rem;">Pengeluaran Terbaru</h6>
                                        <small class="text-white-50">{{ $latestExpenseDate ?? 'Belum ada' }}</small>
                                    </div>
                                </div>
                                <p class="text-white-50 small mb-0">Kas keluar sebesar <span class="fw-bold" style="color: #fb923c;">Rp {{ number_format($latestExpenseAmount, 0, ',', '.') }}</span></p>
                            </div>
                        </div>
                        {{-- Card 4: Rekomendasi UMKM --}}
                        <div class="col-md-6">
                            <div style="background: linear-gradient(135deg, rgba(99,102,241,0.12) 0%, rgba(99,102,241,0.04) 100%); border: 1px solid rgba(99,102,241,0.2); border-radius: 18px; padding: 16px;">
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <div style="width:36px; height:36px; border-radius:12px; background: rgba(99,102,241,0.2); display:flex; align-items:center; justify-content:center;">
                                        <i class="fas fa-store" style="color: #818cf8;"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-white" style="font-size: 0.85rem;">UMKM Pilihan Warga</h6>
                                        <small class="text-white-50">Dukung usaha lokal!</small>
                                    </div>
                                </div>
                                @if($featuredUmkm)
                                    <p class="text-white-50 small mb-1">{{ $featuredUmkm->nama }} — {{ $featuredUmkm->kategori }}</p>
                                    <p class="mb-0"><span class="badge rounded-pill px-2" style="background: rgba(99,102,241,0.3); color: #c7d2fe;">{{ $featuredUmkm->pemilik }}</span></p>
                                @else
                                    <p class="text-white-50 small mb-0">Belum ada UMKM terdaftar.</p>
                                @endif
                            </div>
                        </div>
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
