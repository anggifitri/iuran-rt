@extends('layouts.app')

@section('title', 'Database Warga — NexaNest Admin RW')

@section('content')
<style>
    /* Styling khusus Database Warga NexaNest */
    .dbw-header {
        background: linear-gradient(135deg, #FFFFFF 0%, #F3F8F5 100%);
        border: 1px solid var(--nx-border);
        border-radius: var(--nx-radius-lg, 20px);
        padding: 24px 28px;
        margin-bottom: 24px;
        box-shadow: var(--nx-shadow);
    }
    .dbw-stat-card {
        background: #FFFFFF;
        border: 1px solid var(--nx-border);
        border-radius: var(--nx-radius-md, 14px);
        padding: 20px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        height: 100%;
        box-shadow: var(--nx-shadow);
    }
    .dbw-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--nx-shadow-hover);
    }
    .dbw-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    .dbw-rt-card {
        background: #FFFFFF;
        border: 1px solid var(--nx-border);
        border-radius: var(--nx-radius-md, 16px);
        padding: 22px;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none !important;
        color: inherit !important;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
        position: relative;
        overflow: hidden;
        box-shadow: var(--nx-shadow);
    }
    .dbw-rt-card:hover {
        transform: translateY(-4px);
        border-color: var(--nx-forest);
        box-shadow: 0 14px 30px rgba(40, 89, 67, 0.12);
    }
    .dbw-rt-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--nx-sage);
        transition: background 0.25s;
    }
    .dbw-rt-card:hover::before {
        background: var(--nx-forest);
    }
    .dbw-search-wrap {
        position: relative;
    }
    .dbw-search-results {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 1050;
        background: #FFFFFF;
        border: 1px solid var(--nx-border);
        border-radius: 12px;
        margin-top: 6px;
        box-shadow: 0 12px 36px rgba(40, 89, 67, 0.15);
        max-height: 380px;
        overflow-y: auto;
        display: none;
    }
    .dbw-search-item {
        padding: 12px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid var(--nx-border);
        text-decoration: none;
        color: var(--nx-text);
        transition: background 0.15s;
    }
    .dbw-search-item:last-child {
        border-bottom: none;
    }
    .dbw-search-item:hover {
        background: var(--nx-mint-soft);
    }
    .dbw-tab-btn {
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 8px 16px;
        border: 1px solid transparent;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .dbw-tab-btn.active {
        background: var(--nx-forest);
        color: #FFFFFF !important;
    }
    .dbw-tab-btn:not(.active) {
        background: #FFFFFF;
        color: var(--nx-text);
        border-color: var(--nx-border);
    }
    .dbw-tab-btn:not(.active):hover {
        background: var(--nx-mint-soft);
        color: var(--nx-forest);
    }

    /* Dark Mode Support */
    html.dark .dbw-tab-btn:not(.active) {
        background: #17223B !important;
        border-color: #273654 !important;
        color: #CBD5E1 !important;
    }
    html.dark .dbw-tab-btn:not(.active):hover {
        background: #1E2E4F !important;
        border-color: #38BDF8 !important;
        color: #FFFFFF !important;
    }
</style>

<!-- 1. HEADER HALAMAN (Point 2.A) -->
<div class="dbw-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge rounded-pill px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700; font-size: 0.75rem;">
                <i class="fas fa-id-card-clip me-1"></i> Pemerintahan RW 018
            </span>
            <span class="badge rounded-pill px-3 py-1" style="background: var(--nx-mint); color: var(--nx-forest); font-weight: 700; font-size: 0.75rem;">
                <i class="fas fa-circle-check me-1"></i> Data Terintegrasi
            </span>
        </div>
        <h1 class="h3 fw-bold mb-1" style="color: var(--nx-forest); font-family: 'Plus Jakarta Sans', sans-serif;">
            Database Warga
        </h1>
        <p class="text-muted mb-0" style="font-size: 0.92rem;">
            Pusat pengelolaan data kependudukan seluruh warga di wilayah RW.
        </p>
    </div>

    <!-- Tombol Aksi Relevan -->
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2"
                data-bs-toggle="modal" data-bs-target="#modalEksporWarga"
                style="border-radius: 12px; font-weight: 600; padding: 10px 18px; border-color: var(--nx-border); background: #FFF;">
            <i class="fas fa-file-export text-muted"></i>
            <span>Ekspor Data</span>
        </button>

        <a href="{{ route('warga.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2"
           style="background: var(--nx-forest); border-color: var(--nx-forest); border-radius: 12px; font-weight: 600; padding: 10px 20px;">
            <i class="fas fa-user-plus"></i>
            <span>Tambah Warga</span>
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" style="background: #E8F5E9; color: #2E7D32; border-radius: 12px;" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('info'))
    <div class="alert alert-info alert-dismissible fade show mb-4 border-0 shadow-sm" style="background: var(--nx-powder-soft); color: var(--nx-info); border-radius: 12px;" role="alert">
        <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" style="border-radius: 12px;" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- 8. PENCARIAN GLOBAL DATABASE WARGA (Point 8) -->
<div class="card p-3 mb-4 shadow-sm" style="border-radius: 16px; border: 1px solid var(--nx-border);">
    <div class="dbw-search-wrap">
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0 ps-3" style="border-color: var(--nx-border); border-radius: 12px 0 0 12px;">
                <i class="fas fa-magnifying-glass text-muted"></i>
            </span>
            <input type="text" id="globalWargaSearchInput" class="form-control border-start-0 py-2"
                   placeholder="Pencarian Global: Masukkan Nama, Email, NIK, No. KK, atau Alamat dari seluruh wilayah RW..."
                   style="border-color: var(--nx-border); font-size: 0.92rem;"
                   autocomplete="off">
            <span class="input-group-text bg-white border-start-0 pe-3" id="globalSearchSpinner" style="border-color: var(--nx-border); border-radius: 0 12px 12px 0; display: none;">
                <div class="spinner-border spinner-border-sm text-success" role="status"></div>
            </span>
        </div>
        <!-- Live search results container -->
        <div id="globalSearchResults" class="dbw-search-results"></div>
    </div>
    <div class="mt-2 d-flex align-items-center justify-content-between px-1">
        <small class="text-muted" style="font-size: 0.78rem;">
            <i class="fas fa-circle-info me-1 text-primary"></i> Pencarian global mencari warga lintas seluruh RT 006 s/d RT 010.
        </small>
        <span class="badge" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-size: 0.75rem;">
            Wilayah RW 018
        </span>
    </div>
</div>

<!-- 2. RINGKASAN STATISTIK KEPENDUDUKAN (Point 2.B) -->
<div class="row g-3 mb-4">
    <!-- Card Total Warga -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="dbw-stat-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                    Total Warga
                </span>
                <h2 class="h3 fw-bold mb-1" style="color: var(--nx-forest);">{{ number_format($totalWarga) }}</h2>
                <small class="text-muted" style="font-size: 0.75rem;">Jiwa terdaftar di RW 018</small>
            </div>
            <div class="dbw-stat-icon" style="background: var(--nx-mint); color: var(--nx-forest);">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>

    <!-- Card Total Kepala Keluarga -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="dbw-stat-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                    Kepala Keluarga (KK)
                </span>
                <h2 class="h3 fw-bold mb-1" style="color: #2b567a;">{{ number_format($totalKk) }}</h2>
                <small class="text-muted" style="font-size: 0.75rem;">Kartu Keluarga aktif</small>
            </div>
            <div class="dbw-stat-icon" style="background: var(--nx-powder); color: #2b567a;">
                <i class="fas fa-house-chimney-user"></i>
            </div>
        </div>
    </div>

    <!-- Card Terverifikasi -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="dbw-stat-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                    Terverifikasi
                </span>
                <h2 class="h3 fw-bold mb-1" style="color: #1b7a43;">{{ number_format($totalTerverifikasi) }}</h2>
                <small class="text-muted" style="font-size: 0.75rem;">
                    {{ $totalWarga > 0 ? round(($totalTerverifikasi / $totalWarga) * 100, 1) : 0 }}% keabsahan berkas
                </small>
            </div>
            <div class="dbw-stat-icon" style="background: #E8F5E9; color: #2E7D32;">
                <i class="fas fa-shield-check"></i>
            </div>
        </div>
    </div>

    <!-- Card Menunggu Verifikasi -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="dbw-stat-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                    Menunggu Verifikasi
                </span>
                <h2 class="h3 fw-bold mb-1" style="color: var(--nx-warning);">{{ number_format($totalMenunggu) }}</h2>
                <small class="text-muted" style="font-size: 0.75rem;">Perlu pemeriksaan RW</small>
            </div>
            <div class="dbw-stat-icon" style="background: var(--nx-warning-soft); color: var(--nx-warning);">
                <i class="fas fa-hourglass-half"></i>
            </div>
        </div>
    </div>
</div>

<!-- GRAFIK & STATISTIK DEMOGRAFI (Point 2.B) -->
<div class="row g-3 mb-4">
    <!-- Demografi Gender -->
    <div class="col-lg-6">
        <div class="card p-4 h-100 shadow-sm" style="border-radius: 16px; border: 1px solid var(--nx-border);">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-bold mb-0" style="color: var(--nx-forest);">
                    <i class="fas fa-venus-mars me-2"></i>Komposisi Jenis Kelamin
                </h6>
                <span class="badge" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-weight: 600;">
                    Total: {{ number_format($totalLakiLaki + $totalPerempuan) }} Jiwa
                </span>
            </div>

            @php
                $totGender = ($totalLakiLaki + $totalPerempuan) ?: 1;
                $pctL = round(($totalLakiLaki / $totGender) * 100, 1);
                $pctP = round(($totalPerempuan / $totGender) * 100, 1);
            @endphp

            <div class="mb-3">
                <div class="progress" style="height: 14px; border-radius: 8px; background: #EEF3EF;">
                    <div class="progress-bar" role="progressbar" style="width: {{ $pctL }}%; background: #2b567a;" title="Laki-laki: {{ $pctL }}%"></div>
                    <div class="progress-bar" role="progressbar" style="width: {{ $pctP }}%; background: #c2527a;" title="Perempuan: {{ $pctP }}%"></div>
                </div>
            </div>

            <div class="row g-2 text-center pt-2">
                <div class="col-6">
                    <div class="p-2 rounded-3" style="background: var(--nx-powder-soft); border: 1px solid var(--nx-pastel-blue);">
                        <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
                            <span class="rounded-circle d-inline-block" style="width: 10px; height: 10px; background: #2b567a;"></span>
                            <span class="small fw-bold text-muted">Laki-laki</span>
                        </div>
                        <h4 class="fw-bold mb-0" style="color: #2b567a;">{{ number_format($totalLakiLaki) }}</h4>
                        <small class="text-muted" style="font-size: 0.75rem;">{{ $pctL }}% dari warga</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-2 rounded-3" style="background: #FDF2F4; border: 1px solid #F8CCD5;">
                        <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
                            <span class="rounded-circle d-inline-block" style="width: 10px; height: 10px; background: #c2527a;"></span>
                            <span class="small fw-bold text-muted">Perempuan</span>
                        </div>
                        <h4 class="fw-bold mb-0" style="color: #c2527a;">{{ number_format($totalPerempuan) }}</h4>
                        <small class="text-muted" style="font-size: 0.75rem;">{{ $pctP }}% dari warga</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Demografi Kelompok Usia -->
    <div class="col-lg-6">
        <div class="card p-4 h-100 shadow-sm" style="border-radius: 16px; border: 1px solid var(--nx-border);">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-bold mb-0" style="color: var(--nx-forest);">
                    <i class="fas fa-chart-pie me-2"></i>Komposisi Kelompok Usia
                </h6>
                <span class="badge" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-weight: 600;">
                    3 Kategori Usia
                </span>
            </div>

            @php
                $totUsia = ($totalAnak + $totalDewasa + $totalLansia) ?: 1;
                $pctAnak = round(($totalAnak / $totUsia) * 100, 1);
                $pctDewasa = round(($totalDewasa / $totUsia) * 100, 1);
                $pctLansia = round(($totalLansia / $totUsia) * 100, 1);
            @endphp

            <div class="mb-3">
                <div class="progress" style="height: 14px; border-radius: 8px; background: #EEF3EF;">
                    <div class="progress-bar" role="progressbar" style="width: {{ $pctAnak }}%; background: #10B981;" title="Anak-anak: {{ $pctAnak }}%"></div>
                    <div class="progress-bar" role="progressbar" style="width: {{ $pctDewasa }}%; background: var(--nx-forest);" title="Dewasa/Produktif: {{ $pctDewasa }}%"></div>
                    <div class="progress-bar" role="progressbar" style="width: {{ $pctLansia }}%; background: #F59E0B;" title="Lansia: {{ $pctLansia }}%"></div>
                </div>
            </div>

            <div class="row g-2 text-center pt-2">
                <div class="col-4">
                    <div class="p-2 rounded-3" style="background: #ECFDF5; border: 1px solid #A7F3D0;">
                        <span class="small fw-bold text-muted d-block" style="font-size: 0.72rem;">Anak (0-17)</span>
                        <h5 class="fw-bold mb-0 text-success">{{ number_format($totalAnak) }}</h5>
                        <small class="text-muted" style="font-size: 0.7rem;">{{ $pctAnak }}%</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-2 rounded-3" style="background: var(--nx-mint-soft); border: 1px solid var(--nx-sage);">
                        <span class="small fw-bold text-muted d-block" style="font-size: 0.72rem;">Dewasa (18-59)</span>
                        <h5 class="fw-bold mb-0" style="color: var(--nx-forest);">{{ number_format($totalDewasa) }}</h5>
                        <small class="text-muted" style="font-size: 0.7rem;">{{ $pctDewasa }}%</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-2 rounded-3" style="background: var(--nx-warning-soft); border: 1px solid #FCD34D;">
                        <span class="small fw-bold text-muted d-block" style="font-size: 0.72rem;">Lansia (60+)</span>
                        <h5 class="fw-bold mb-0" style="color: #B45309;">{{ number_format($totalLansia) }}</h5>
                        <small class="text-muted" style="font-size: 0.7rem;">{{ $pctLansia }}%</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 3. BAGIAN VERIFIKASI WARGA (Point 3) -->
<div class="card p-4 mb-5 shadow-sm" id="verifikasi-section" style="border-radius: 20px; border: 1px solid var(--nx-border); background: #FFFFFF;">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 pb-3 mb-3 border-bottom">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge rounded-pill px-3 py-1" style="background: var(--nx-warning-soft); color: var(--nx-warning); font-weight: 700; font-size: 0.72rem;">
                    <i class="fas fa-clipboard-check me-1"></i> Antrean Verifikasi RW
                </span>
                <span class="text-muted small" style="font-size: 0.8rem;">Persetujuan Administrasi</span>
            </div>
            <h4 class="fw-bold mb-0" style="color: var(--nx-forest);">
                Pemeriksaan &amp; Pengesahan Warga
            </h4>
            <p class="text-muted small mb-0 mt-1">
                Pusat validasi pengajuan pendaftaran warga baru, pembaruan data identitas, KK, dan kependudukan RW 018.
            </p>
        </div>

        <!-- Metric Badges Ringkas -->
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="badge px-3 py-2" style="background: var(--nx-warning-soft); color: var(--nx-warning); font-size: 0.8rem; border-radius: 10px;">
                <i class="fas fa-hourglass-half me-1"></i> Menunggu: <strong>{{ $totalMenunggu }}</strong>
            </span>
            <span class="badge px-3 py-2" style="background: var(--nx-powder-soft); color: #1e4d72; font-size: 0.8rem; border-radius: 10px;">
                <i class="fas fa-wrench me-1"></i> Perbaikan: <strong>{{ $totalPerbaikan }}</strong>
            </span>
            <span class="badge px-3 py-2" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-size: 0.8rem; border-radius: 10px;">
                <i class="fas fa-check-circle me-1"></i> Disetujui: <strong>{{ $totalTerverifikasi }}</strong>
            </span>
            <span class="badge px-3 py-2" style="background: var(--nx-danger-soft); color: var(--nx-danger); font-size: 0.8rem; border-radius: 10px;">
                <i class="fas fa-ban me-1"></i> Ditolak: <strong>{{ $totalDitolak }}</strong>
            </span>
        </div>
    </div>

    <!-- Filter & Tabs Verifikasi -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('warga.index', array_merge(request()->except(['verif_page', 'status']), ['status' => 'menunggu'])) }}#verifikasi-section"
               class="dbw-tab-btn {{ $verifStatus === 'menunggu' ? 'active' : '' }}">
                <i class="fas fa-clock"></i>
                <span>Menunggu Pemeriksaan</span>
                @if($totalMenunggu > 0)
                    <span class="badge bg-danger rounded-pill ms-1">{{ $totalMenunggu }}</span>
                @endif
            </a>
            <a href="{{ route('warga.index', array_merge(request()->except(['verif_page', 'status']), ['status' => 'perbaikan'])) }}#verifikasi-section"
               class="dbw-tab-btn {{ $verifStatus === 'perbaikan' ? 'active' : '' }}">
                <i class="fas fa-wrench"></i>
                <span>Perlu Perbaikan</span>
                @if($totalPerbaikan > 0)
                    <span class="badge bg-secondary rounded-pill ms-1">{{ $totalPerbaikan }}</span>
                @endif
            </a>
            <a href="{{ route('warga.index', array_merge(request()->except(['verif_page', 'status']), ['status' => 'terverifikasi'])) }}#verifikasi-section"
               class="dbw-tab-btn {{ $verifStatus === 'terverifikasi' ? 'active' : '' }}">
                <i class="fas fa-circle-check"></i>
                <span>Disetujui</span>
            </a>
            <a href="{{ route('warga.index', array_merge(request()->except(['verif_page', 'status']), ['status' => 'ditolak'])) }}#verifikasi-section"
               class="dbw-tab-btn {{ $verifStatus === 'ditolak' ? 'active' : '' }}">
                <i class="fas fa-times-circle"></i>
                <span>Ditolak</span>
            </a>
            <a href="{{ route('warga.index', array_merge(request()->except(['verif_page', 'status']), ['status' => 'semua'])) }}#verifikasi-section"
               class="dbw-tab-btn {{ $verifStatus === 'semua' ? 'active' : '' }}">
                <i class="fas fa-list"></i>
                <span>Semua Status</span>
            </a>
        </div>

        <!-- Search dalam verifikasi -->
        <form method="GET" action="{{ route('warga.index') }}#verifikasi-section" class="d-flex align-items-center gap-2">
            <input type="hidden" name="status" value="{{ $verifStatus }}">
            <div class="input-group input-group-sm" style="width: 240px;">
                <input type="text" name="search_verif" class="form-control" value="{{ request('search_verif') }}" placeholder="Cari nama / NIK..." style="border-radius: 8px 0 0 8px; border-color: var(--nx-border);">
                <button type="submit" class="btn btn-outline-secondary" style="border-radius: 0 8px 8px 0; border-color: var(--nx-border);">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            @if(request('search_verif'))
                <a href="{{ route('warga.index', ['status' => $verifStatus]) }}#verifikasi-section" class="btn btn-sm btn-link text-muted p-0" title="Reset">
                    <i class="fas fa-times-circle"></i>
                </a>
            @endif
        </form>
    </div>

    <!-- Tabel Antrean Verifikasi (STANDAR TABEL NEXANEST) -->
    <div class="table-responsive nx-table-responsive rounded-3" style="border: 1px solid var(--nx-border);">
        <table class="table table-hover align-middle nx-table mb-0">
            <thead>
                <tr>
                    <th class="ps-4">Nama Warga</th>
                    <th>Jenis Pengajuan</th>
                    <th>RT Terkait</th>
                    <th>Tanggal Masuk</th>
                    <th>Pengaju / Pelapor</th>
                    <th>Status</th>
                    <th class="pe-4 text-end">Aksi Pemeriksaan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($antreanVerifikasi as $item)
                    @php
                        $maskedNik = $item->nik ? (strlen($item->nik) >= 10 ? substr($item->nik, 0, 6) . '******' . substr($item->nik, -4) : $item->nik) : '-';
                    @endphp
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $item->profile_photo_url }}" alt="{{ $item->nama }}"
                                     class="rounded-circle" style="width: 38px; height: 38px; object-fit: cover; border: 2px solid var(--nx-mint);">
                                <div>
                                    <div class="fw-bold" style="color: var(--nx-text);">{{ $item->nama }}</div>
                                    <div class="text-xs text-slate-500" style="font-size: 0.74rem;">
                                        @if($item->user && $item->user->email)
                                            <span class="text-slate-500 font-monospace"><i class="fas fa-envelope text-muted me-1"></i>{{ $item->user->email }}</span>
                                        @else
                                            <span class="text-slate-400 fst-italic">Belum punya akun</span>
                                        @endif
                                    </div>
                                    <small class="text-muted font-monospace">NIK: {{ $maskedNik }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge" style="background: var(--nx-powder-soft); color: #1e4d72; font-weight: 600; border: 1px solid var(--nx-pastel-blue);">
                                <i class="fas fa-file-lines me-1"></i>{{ $item->jenis_pengajuan ?? 'Pendaftaran Warga Baru' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-weight: 700;">
                                RT {{ $item->rt_number }}
                            </span>
                        </td>
                        <td>
                            <span class="text-muted" style="font-size: 0.82rem;">
                                {{ $item->updated_at ? $item->updated_at->translatedFormat('d M Y, H:i') : '-' }}
                            </span>
                        </td>
                        <td>
                            <span class="text-dark fw-semibold" style="font-size: 0.82rem;">
                                {{ $item->pengaju_nama ?? 'Admin RT ' . $item->rt_number }}
                            </span>
                        </td>
                        <td>
                            @if($item->status_verifikasi === 'terverifikasi')
                                <x-status-badge status="terverifikasi" label="Disetujui" />
                            @elseif($item->status_verifikasi === 'perbaikan')
                                <x-status-badge status="perlu_perbaikan" label="Perlu Perbaikan" />
                            @elseif($item->status_verifikasi === 'ditolak')
                                <x-status-badge status="ditolak" label="Ditolak" />
                            @else
                                <x-status-badge status="menunggu" label="Menunggu Review" />
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            <button type="button" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1.5 px-3 py-1"
                                    style="border-radius: 8px; font-weight: 600; font-size: 0.78rem;"
                                    onclick="openVerifModal({{ json_encode($item) }})">
                                <i class="fas fa-eye"></i>
                                <span>Periksa</span>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-0 border-0">
                            <x-empty-state
                                icon="fas fa-clipboard-check"
                                title="Tidak Ada Pengajuan dalam Kategori Ini"
                                message="Semua berkas pada status ini telah diproses atau belum ada antrean verifikasi baru dari Admin RT."
                            />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($antreanVerifikasi->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-3 pt-2">
            <small class="text-muted">
                Menampilkan {{ $antreanVerifikasi->firstItem() }} - {{ $antreanVerifikasi->lastItem() }} dari {{ $antreanVerifikasi->total() }} pengajuan
            </small>
            <div>
                {{ $antreanVerifikasi->links() }}
            </div>
        </div>
    @endif
</div>

<!-- 4. DAFTAR DATABASE WARGA BERDASARKAN RT (Point 4) -->
<div class="mb-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge rounded-pill px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700; font-size: 0.72rem;">
                    <i class="fas fa-sitemap me-1"></i> Struktur Wilayah
                </span>
                <span class="text-muted small">Kelompok Rukun Tetangga (RT)</span>
            </div>
            <h4 class="fw-bold mb-0" style="color: var(--nx-forest);">
                Database Warga Berdasarkan RT
            </h4>
            <p class="text-muted small mb-0 mt-1">
                Pilih RT untuk menelusuri daftar warga, kartu keluarga, dan profil kependudukan di wilayah terkait.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="badge px-3 py-2" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-size: 0.82rem; border-radius: 10px;">
                <i class="fas fa-map-location-dot me-1"></i> {{ count($daftarRt) }} Wilayah RT Aktif
            </span>
        </div>
    </div>

    <!-- Grid Card Per RT (Point 4.A) -->
    <div class="row g-4">
        @foreach($daftarRt as $rt)
            <div class="col-12 col-md-6 col-xl-4">
                <a href="{{ route('warga.rt', $rt['rt_number']) }}" class="dbw-rt-card">
                    <!-- Top section card RT -->
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge px-3 py-2 fs-6 fw-bold" style="background: var(--nx-forest); color: #FFFFFF; border-radius: 10px;">
                                    RT {{ $rt['rt_number'] }}
                                </span>
                                <span class="badge" style="background: var(--nx-mint); color: var(--nx-forest); font-size: 0.75rem;">
                                    RW 018
                                </span>
                            </div>
                            <span class="badge bg-light text-muted border" style="font-size: 0.72rem;">
                                <i class="fas fa-arrow-right text-success"></i> Buka Data
                            </span>
                        </div>

                        <!-- Nama Ketua RT -->
                        <div class="mb-3 pb-2 border-bottom">
                            <small class="text-muted d-block" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                Ketua RT
                            </small>
                            <div class="fw-bold text-dark d-flex align-items-center gap-2 mt-1">
                                <i class="fas fa-user-tie text-muted"></i>
                                <span>{{ $rt['ketua_rt'] }}</span>
                            </div>
                            @if(!empty($rt['kontak_rt']) && $rt['kontak_rt'] !== '-')
                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                                    <i class="fas fa-phone text-muted me-1"></i> {{ $rt['kontak_rt'] }}
                                </small>
                            @endif
                        </div>

                        <!-- Statistik Inti RT -->
                        <div class="row g-2 text-center mb-3">
                            <div class="col-6">
                                <div class="p-2 rounded-3" style="background: var(--nx-mint-soft); border: 1px solid var(--nx-border);">
                                    <span class="text-muted d-block small" style="font-size: 0.72rem;">Total Warga</span>
                                    <h5 class="fw-bold mb-0" style="color: var(--nx-forest);">{{ number_format($rt['total_warga']) }}</h5>
                                    <small class="text-muted" style="font-size: 0.68rem;">Jiwa</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 rounded-3" style="background: var(--nx-powder-soft); border: 1px solid var(--nx-border);">
                                    <span class="text-muted d-block small" style="font-size: 0.72rem;">Kepala Keluarga</span>
                                    <h5 class="fw-bold mb-0" style="color: #2b567a;">{{ number_format($rt['total_kk']) }}</h5>
                                    <small class="text-muted" style="font-size: 0.68rem;">KK</small>
                                </div>
                            </div>
                        </div>

                        <!-- Status Verifikasi Ringkas -->
                        <div class="p-2 px-3 rounded-3 mb-2" style="background: #F8FBF9; border: 1px solid var(--nx-border);">
                            <div class="d-flex justify-content-between align-items-center small mb-1">
                                <span class="text-muted" style="font-size: 0.75rem;">Verifikasi Data:</span>
                                <span class="fw-bold" style="font-size: 0.75rem; color: var(--nx-forest);">
                                    {{ $rt['total_terverifikasi'] }} Sah
                                    @if($rt['total_menunggu'] > 0)
                                        <span class="text-warning">({{ $rt['total_menunggu'] }} proses)</span>
                                    @endif
                                </span>
                            </div>
                            @php
                                $totV = $rt['total_warga'] ?: 1;
                                $pctV = round(($rt['total_terverifikasi'] / $totV) * 100);
                            @endphp
                            <div class="progress" style="height: 6px; border-radius: 4px; background: #E5EBE6;">
                                <div class="progress-bar" style="width: {{ $pctV }}%; background: var(--nx-forest);"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Indicator -->
                    <div class="pt-2 text-end">
                        <span class="small fw-bold text-success d-inline-flex align-items-center gap-1" style="font-size: 0.8rem;">
                            Lihat Warga RT {{ $rt['rt_number'] }} <i class="fas fa-chevron-right ms-1"></i>
                        </span>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL DETAIL & KEPUTUSAN VERIFIKASI WARGA (Point 3.D) -->
<!-- ============================================================== -->
<div class="modal fade" id="modalVerifDetail" tabindex="-1" aria-labelledby="modalVerifDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 18px; border: none; box-shadow: 0 20px 48px rgba(0,0,0,0.18);">
            <div class="modal-header" style="background: linear-gradient(135deg, var(--nx-forest) 0%, var(--nx-forest-dark) 100%); color: #FFF; border-radius: 18px 18px 0 0;">
                <div>
                    <h5 class="modal-title fw-bold" id="modalVerifDetailLabel">
                        <i class="fas fa-user-check me-2"></i>Pemeriksaan Pengajuan Kependudukan
                    </h5>
                    <small class="text-white-50" id="modalVerifSub">Persetujuan &amp; Validasi Data Tingkat RW</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4 mb-4">
                    <!-- Foto & Ringkasan -->
                    <div class="col-md-4 text-center">
                        <img id="mVerifPhoto" src="" alt="Foto Warga" class="rounded-circle mb-3 shadow-sm"
                             style="width: 110px; height: 110px; object-fit: cover; border: 4px solid var(--nx-mint);">
                        <h5 class="fw-bold mb-1 text-dark" id="mVerifNama">-</h5>
                        <span class="badge px-3 py-1 mb-2" id="mVerifBadgeRt" style="background: var(--nx-mint-soft); color: var(--nx-forest);">
                            RT -
                        </span>
                        <div class="small text-muted" id="mVerifStatusHubungan">-</div>
                    </div>

                    <!-- Informasi Warga -->
                    <div class="col-md-8">
                        <div class="p-3 rounded-3" style="background: var(--nx-mint-soft); border: 1px solid var(--nx-sage);">
                            <h6 class="fw-bold mb-2" style="color: var(--nx-forest);">
                                <i class="fas fa-id-card me-1"></i> Identitas Warga
                            </h6>
                            <div class="row g-2 small">
                                <div class="col-6">
                                    <span class="text-muted d-block">NIK</span>
                                    <span class="fw-bold font-monospace" id="mVerifNik">-</span>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted d-block">Nomor KK</span>
                                    <span class="fw-bold font-monospace" id="mVerifNoKk">-</span>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted d-block">Jenis Kelamin / Usia</span>
                                    <span class="fw-semibold" id="mVerifGenderUsia">-</span>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted d-block">Kontak / HP</span>
                                    <span class="fw-semibold" id="mVerifHp">-</span>
                                </div>
                                <div class="col-12">
                                    <span class="text-muted d-block">Alamat Domisili</span>
                                    <span class="fw-semibold" id="mVerifAlamat">-</span>
                                </div>
                            </div>
                        </div>

                        <!-- Detail Pengajuan -->
                        <div class="mt-3 p-3 rounded-3" style="background: #FDF9F0; border: 1px solid #F8E3B6;">
                            <h6 class="fw-bold mb-2" style="color: #92400E;">
                                <i class="fas fa-file-signature me-1"></i> Detail Permohonan
                            </h6>
                            <div class="row g-2 small">
                                <div class="col-6">
                                    <span class="text-muted d-block">Jenis Pengajuan</span>
                                    <span class="fw-bold" id="mVerifJenis">-</span>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted d-block">Diajukan Oleh</span>
                                    <span class="fw-bold" id="mVerifPengaju">-</span>
                                </div>
                                <div class="col-12">
                                    <span class="text-muted d-block">Alasan Pengajuan</span>
                                    <div class="p-2 rounded bg-white border" id="mVerifAlasan" style="font-size: 0.82rem;">-</div>
                                </div>
                                <div id="mVerifDataSebelumnyaWrap" class="col-12" style="display: none;">
                                    <span class="text-muted d-block fw-bold text-danger">Data Sebelum Perubahan</span>
                                    <pre class="p-2 rounded bg-white border small mb-0" id="mVerifDataSebelumnya" style="font-size: 0.75rem; max-height: 100px; overflow-y: auto;"></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Keputusan Admin RW -->
                <form id="formVerifRw" method="POST" action="">
                    @csrf
                    <input type="hidden" name="status_verifikasi" id="inputStatusVerif" value="terverifikasi">

                    <div class="mb-3">
                        <label class="form-label small fw-bold">
                            Catatan Keputusan Admin RW <span class="text-muted">(Wajib diisi jika Minta Perbaikan atau Tolak)</span>
                        </label>
                        <textarea name="catatan_verifikasi" id="mVerifCatatanInput" class="form-control" rows="2"
                                  placeholder="Tuliskan catatan verifikasi, rekomendasi revisi dokumen, atau alasan keputusan..."></textarea>
                    </div>

                    <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                        <a href="#" id="mVerifLinkDetailProfil" class="btn btn-sm btn-outline-secondary" target="_blank">
                            <i class="fas fa-arrow-up-right-from-square me-1"></i> Buka Profil Lengkap
                        </a>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-outline-danger" onclick="submitVerifDecision('ditolak')">
                                <i class="fas fa-times-circle me-1"></i> Tolak
                            </button>
                            <button type="button" class="btn btn-outline-warning" style="color: #92400E; border-color: #F59E0B;" onclick="submitVerifDecision('perbaikan')">
                                <i class="fas fa-wrench me-1"></i> Minta Perbaikan
                            </button>
                            <button type="button" class="btn btn-success" style="background: var(--nx-forest); border-color: var(--nx-forest);" onclick="submitVerifDecision('terverifikasi')">
                                <i class="fas fa-check-circle me-1"></i> Setujui Pengajuan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL EKSPOR DATA WARGA (Point 2.A) -->
<!-- ============================================================== -->
<div class="modal fade" id="modalEksporWarga" tabindex="-1" aria-labelledby="modalEksporLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none;">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" id="modalEksporLabel" style="color: var(--nx-forest);">
                    <i class="fas fa-file-export me-2"></i>Ekspor Database Kependudukan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="GET" action="{{ route('warga.export') }}">
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        Pilih lingkup data yang ingin Anda unduh dalam format CSV (Kompatibel dengan Microsoft Excel dan Google Sheets).
                    </p>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Wilayah RT</label>
                        <select name="rt" class="form-select">
                            <option value="">Semua RT (Seluruh Wilayah RW 018)</option>
                            @foreach($daftarRt as $rt)
                                <option value="{{ $rt['rt_number'] }}">RT {{ $rt['rt_number'] }} ({{ $rt['total_warga'] }} Warga)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="p-3 rounded-3" style="background: var(--nx-mint-soft); border: 1px solid var(--nx-sage);">
                        <small class="text-muted d-block">
                            <i class="fas fa-shield-halved me-1 text-success"></i> Dokumen ekspor dilindungi standar privasi kependudukan NexaNest.
                        </small>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" style="background: var(--nx-forest); border-color: var(--nx-forest);">
                        <i class="fas fa-download me-1"></i> Mulai Unduh CSV
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // 1. LIVE GLOBAL SEARCH JAVASCRIPT (Point 8)
    const searchInput = document.getElementById('globalWargaSearchInput');
    const searchResults = document.getElementById('globalSearchResults');
    const searchSpinner = document.getElementById('globalSearchSpinner');
    let debounceTimer = null;

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            clearTimeout(debounceTimer);

            if (query.length < 2) {
                searchResults.style.display = 'none';
                searchResults.innerHTML = '';
                return;
            }

            searchSpinner.style.display = 'flex';

            debounceTimer = setTimeout(() => {
                fetch(`{{ route('warga.search') }}?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        searchSpinner.style.display = 'none';
                        if (!data || data.length === 0) {
                            searchResults.innerHTML = `
                                <div class="p-3 text-center text-muted small">
                                    <i class="fas fa-user-slash me-1"></i> Warga tidak ditemukan untuk pencarian "<strong>${query}</strong>"
                                </div>
                            `;
                            searchResults.style.display = 'block';
                            return;
                        }

                        let html = '';
                        data.forEach(item => {
                            html += `
                                <a href="${item.detail_url}" class="dbw-search-item">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="${item.profile_photo_url}" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;">
                                        <div>
                                            <div class="fw-bold small text-dark">${item.nama}</div>
                                            <div class="text-muted" style="font-size: 0.74rem;">
                                                ${item.email ? `<span class="text-slate-600 font-monospace fw-semibold"><i class="fas fa-envelope text-muted me-1"></i>${item.email}</span> &bull; ` : '<span class="text-muted fst-italic">Belum punya akun</span> &bull; '}
                                                NIK: <span class="font-monospace">${item.nik_masked}</span> &bull; Blok: ${item.blok_rumah || '-'}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge" style="background: var(--nx-forest); color: #fff; font-size: 0.72rem;">RT ${item.rt_number}</span>
                                        <div class="small text-muted mt-1" style="font-size: 0.7rem;">${item.status_verifikasi}</div>
                                    </div>
                                </a>
                            `;
                        });
                        searchResults.innerHTML = html;
                        searchResults.style.display = 'block';
                    })
                    .catch(err => {
                        searchSpinner.style.display = 'none';
                        console.error('Search error:', err);
                    });
            }, 300);
        });

        // Hide results on outside click
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.style.display = 'none';
            }
        });
    }

    // 2. MODAL VERIFIKASI WARGA JAVASCRIPT
    function openVerifModal(item) {
        document.getElementById('mVerifPhoto').src = item.profile_photo_url || 'https://ui-avatars.com/api/?name=Warga&background=285943&color=ffffff';
        document.getElementById('mVerifNama').textContent = item.nama;
        document.getElementById('mVerifBadgeRt').textContent = 'RT ' + item.rt_number + ' / RW ' + (item.rw_number || '018');
        document.getElementById('mVerifStatusHubungan').textContent = item.status_hubungan || 'Warga';

        const maskedNik = item.nik ? (item.nik.length >= 10 ? item.nik.substring(0, 6) + '******' + item.nik.substring(item.nik.length - 4) : item.nik) : '-';
        document.getElementById('mVerifNik').textContent = maskedNik;
        document.getElementById('mVerifNoKk').textContent = item.no_kk || '-';
        document.getElementById('mVerifGenderUsia').textContent = (item.gender === 'L' ? 'Laki-laki' : 'Perempuan') + ' &bull; ' + (item.umur_formatted || '-');
        document.getElementById('mVerifHp').textContent = item.nomor_hp || '-';
        document.getElementById('mVerifAlamat').textContent = item.alamat || (item.blok_rumah ? 'Blok ' + item.blok_rumah : '-');

        document.getElementById('mVerifJenis').textContent = item.jenis_pengajuan || 'Pendaftaran Warga Baru';
        document.getElementById('mVerifPengaju').textContent = item.pengaju_nama || ('Admin RT ' + item.rt_number);
        document.getElementById('mVerifAlasan').textContent = item.alasan_pengajuan || 'Pengajuan validasi data kependudukan.';

        const wrapSebelumnya = document.getElementById('mVerifDataSebelumnyaWrap');
        if (item.data_sebelumnya) {
            wrapSebelumnya.style.display = 'block';
            document.getElementById('mVerifDataSebelumnya').textContent = JSON.stringify(item.data_sebelumnya, null, 2);
        } else {
            wrapSebelumnya.style.display = 'none';
        }

        document.getElementById('mVerifCatatanInput').value = item.catatan_verifikasi || '';

        // Route URL for form submission
        const form = document.getElementById('formVerifRw');
        form.action = `{{ url('/warga') }}/${item.id}/verifikasi-rw`;

        // Link detail profil
        document.getElementById('mVerifLinkDetailProfil').href = `{{ url('/warga') }}/${item.id}`;

        const modal = new bootstrap.Modal(document.getElementById('modalVerifDetail'));
        modal.show();
    }

    function submitVerifDecision(status) {
        const catatan = document.getElementById('mVerifCatatanInput').value.trim();
        if ((status === 'perbaikan' || status === 'ditolak') && catatan === '') {
            alert('Catatan verifikasi wajib diisi untuk tindakan Perlu Perbaikan atau Penolakan.');
            document.getElementById('mVerifCatatanInput').focus();
            return;
        }

        const confirmText = status === 'terverifikasi'
            ? 'Apakah Anda yakin ingin MENYETUJUI pengajuan data warga ini?'
            : (status === 'perbaikan'
                ? 'Kembalikan pengajuan ke warga/RT untuk DIPERBAIKI?'
                : 'Apakah Anda yakin ingin MENOLAK pengajuan ini?');

        if (confirm(confirmText)) {
            document.getElementById('inputStatusVerif').value = status;
            document.getElementById('formVerifRw').submit();
        }
    }
</script>
@endpush
@endsection
