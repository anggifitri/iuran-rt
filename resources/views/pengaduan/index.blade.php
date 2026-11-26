@extends('layouts.app')

@section('title', 'Manajemen Aset & Pengaduan — NexaNest')

@section('content')
@php
    $user = Auth::user();
    $isWarga = $user->isWarga();
    $isAdminRw = $user->isAdminRw();
    $isAdminRt = $user->isAdminRt();
@endphp

<!-- ==================== HEADER HALAMAN ==================== -->
<div class="nx-page-header mb-4">
    <div>
        <span class="badge rounded-pill mb-2 px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
            <i class="{{ $isWarga ? 'fas fa-comments' : 'fas fa-boxes-stacked' }} me-1"></i> {{ $isWarga ? 'Layanan Aspirasi & Keluhan Lingkungan' : 'Pusat Pengelolaan Fasilitas & Aspirasi Warga' }}
        </span>
        <h1 class="nx-page-title">{{ $isWarga ? 'Layanan Pengaduan & Keluhan Warga' : 'Manajemen Aset & Pengaduan' }}</h1>
        <p class="nx-page-subtitle">{{ $isWarga ? 'Sampaikan laporan pengaduan, keluhan fasilitas umum, atau aspirasi lingkungan Anda di RW 018 secara cepat, transparan, dan terpantau.' : 'Satu pusat terpadu pengelolaan inventaris fisik lingkungan, aspirasi pengaduan warga, pemantauan inspeksi, dan integrasi QR Code Google Maps.' }}</p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('pengaduan.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i> Buat Laporan Baru
        </a>
        <a href="{{ route('pengaduan.index', ['tab' => 'dokumentasi', 'scan' => 1]) }}" class="btn btn-outline-primary">
            <i class="fas fa-qrcode me-1"></i> Scan QR Aset
        </a>
        @if(!$isWarga)
            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalTambahAset">
                <i class="fas fa-plus me-1"></i> Tambah Aset
            </button>
        @endif
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert" style="background: var(--nx-mint-soft); border-color: var(--nx-sage); color: var(--nx-forest);">
        <i class="fas fa-check-circle me-2 fs-5"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
        <i class="fas fa-exclamation-circle me-2 fs-5"></i>
        <div>{{ session('error') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- ==================== TAB UTAMA FITUR (4 BAGIAN UTAMA) ==================== -->
<div class="card p-2 mb-4 border-0 shadow-sm" style="background: var(--nx-bg); border-radius: 14px;">
    <ul class="nav nav-pills nav-fill gap-1" id="asetPengaduanTab" role="tablist">
        @if(!$isWarga)
        <li class="nav-item" role="presentation">
            <a href="{{ route('pengaduan.index', ['tab' => 'ringkasan']) }}"
               class="nav-link py-2.5 px-3 fw-bold d-flex align-items-center justify-content-center gap-2 {{ $activeTab === 'ringkasan' ? 'active' : '' }}"
               style="border-radius: 10px; font-size: 0.9rem;">
                <i class="fas fa-chart-pie"></i>
                <span>1. Ringkasan &amp; Statistik</span>
            </a>
        </li>
        @endif
        @if(!$isWarga)
        <li class="nav-item" role="presentation">
            <a href="{{ route('pengaduan.index', ['tab' => 'aset']) }}"
               class="nav-link py-2.5 px-3 fw-bold d-flex align-items-center justify-content-center gap-2 {{ $activeTab === 'aset' ? 'active' : '' }}"
               style="border-radius: 10px; font-size: 0.9rem;">
                <i class="fas fa-boxes-stacked"></i>
                <span>2. Database Aset ({{ $asets->count() ?: \App\Models\Aset::count() }})</span>
            </a>
        </li>
        @endif
        <li class="nav-item" role="presentation">
            <a href="{{ route('pengaduan.index', ['tab' => 'pengaduan']) }}"
               class="nav-link py-2.5 px-3 fw-bold d-flex align-items-center justify-content-center gap-2 {{ $activeTab === 'pengaduan' ? 'active' : '' }}"
               style="border-radius: 10px; font-size: 0.9rem;">
                <i class="fas fa-clipboard-list"></i>
                <span>{{ $isWarga ? '1. Pengaduan Saya' : '3. Pengaduan Warga' }}</span>
                @if(isset($stat['pengaduanMenunggu']) && $stat['pengaduanMenunggu'] > 0 && !$isWarga)
                    <span class="badge rounded-pill bg-warning text-dark px-2 py-0.5" style="font-size: 0.72rem;">{{ $stat['pengaduanMenunggu'] }} Baru</span>
                @endif
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a href="{{ route('pengaduan.index', ['tab' => 'dokumentasi']) }}"
               class="nav-link py-2.5 px-3 fw-bold d-flex align-items-center justify-content-center gap-2 {{ $activeTab === 'dokumentasi' ? 'active' : '' }}"
               style="border-radius: 10px; font-size: 0.9rem;">
                <i class="fas fa-qrcode"></i>
                <span>{{ $isWarga ? '2. Dokumentasi & Scan QR' : '4. Dokumentasi & Scan QR' }}</span>
            </a>
        </li>
    </ul>
</div>

<!-- ========================================================================= -->
<!-- 1. TAB RINGKASAN: STATISTIK MENYELURUH & GRAFIK DARI DATABASE             -->
<!-- ========================================================================= -->
@if($activeTab === 'ringkasan' && !$isWarga)
    <!-- FILTER BAR RINGKASAN -->
    <div class="card p-3 mb-4 border-0 shadow-sm">
        <form method="GET" action="{{ route('pengaduan.index') }}" class="row g-2 align-items-center">
            <input type="hidden" name="tab" value="ringkasan">
            <div class="col-md-4 col-sm-6">
                <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-map-marker-alt me-1"></i>Filter Wilayah RT</label>
                @if($isAdminRt)
                    <input type="text" class="form-control form-control-sm bg-light" value="RT {{ $user->rt_number }} (Wilayah Anda)" readonly>
                    <input type="hidden" name="rt_number" value="{{ $user->rt_number }}">
                @else
                    <select name="rt_number" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all" {{ ($rtFilter === 'all' || !$rtFilter) ? 'selected' : '' }}>Semua Wilayah RT (006 - 010)</option>
                        @foreach($rtList as $rt)
                            <option value="{{ $rt }}" {{ $rtFilter === $rt ? 'selected' : '' }}>RT {{ $rt }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
            <div class="col-md-4 col-sm-6">
                <label class="form-label small fw-bold text-muted mb-1"><i class="far fa-calendar-alt me-1"></i>Periode Waktu</label>
                <select name="periode" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="year_2026" selected>Tahun 2026 (Januari - Desember)</option>
                    <option value="6m" {{ request('periode') === '6m' ? 'selected' : '' }}>6 Bulan Terakhir</option>
                    <option value="30d" {{ request('periode') === '30d' ? 'selected' : '' }}>30 Hari Terakhir</option>
                </select>
            </div>
            <div class="col-md-4 text-md-end mt-sm-auto pt-2">
                <span class="badge rounded-pill px-3 py-1.5" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-size: 0.82rem;">
                    <i class="fas fa-database me-1"></i> Data Aktual Lingkungan RW 018
                </span>
            </div>
        </form>
    </div>

    <!-- 8 KARTU STATISTIK KONDISI ASET & PENGADUAN (STANDAR NEXANEST) -->
    <div class="row g-3 mb-4">
        <!-- 1. Total Aset -->
        <div class="col-xl-3 col-md-6">
            <x-stat-card 
                title="Total Aset Terdaftar" 
                value="{{ $stat['totalAset'] }}" 
                sub="Inventaris sarana & fasum" 
                icon="fas fa-boxes-stacked" 
                theme="forest" 
            />
        </div>

        <!-- 2. Aset Kondisi Baik -->
        <div class="col-xl-3 col-md-6">
            <x-stat-card 
                title="Aset Kondisi Baik" 
                value="{{ $stat['asetBaik'] }}" 
                sub="{{ $stat['totalAset'] > 0 ? round(($stat['asetBaik']/$stat['totalAset'])*100) : 0 }}% sarana beroperasi normal" 
                icon="fas fa-circle-check" 
                theme="success" 
                valColor="#059669" 
            />
        </div>

        <!-- 3. Aset Bermasalah -->
        <div class="col-xl-3 col-md-6">
            <x-stat-card 
                title="Aset Bermasalah" 
                value="{{ $stat['asetBermasalah'] }}" 
                sub="Perlu servis / dalam perbaikan" 
                icon="fas fa-triangle-exclamation" 
                theme="warning" 
                valColor="#D97706" 
            />
        </div>

        <!-- 4. Total Pengaduan -->
        <div class="col-xl-3 col-md-6">
            <x-stat-card 
                title="Total Pengaduan" 
                value="{{ $stat['totalPengaduan'] }}" 
                sub="Aspirasi warga tercatat" 
                icon="fas fa-clipboard-list" 
                theme="primary" 
                valColor="#1D4ED8" 
            />
        </div>

        <!-- 5. Pengaduan Menunggu -->
        <div class="col-xl-3 col-md-6">
            <x-stat-card 
                title="Menunggu Verifikasi" 
                value="{{ $stat['pengaduanMenunggu'] }}" 
                sub="Butuh respon pengurus" 
                icon="fas fa-clock" 
                theme="warning" 
                valColor="#D97706" 
            />
        </div>

        <!-- 6. Pengaduan Sedang Diproses -->
        <div class="col-xl-3 col-md-6">
            <x-stat-card 
                title="Sedang Diproses" 
                value="{{ $stat['pengaduanProses'] }}" 
                sub="Ditugaskan ke teknisi / RT" 
                icon="fas fa-screwdriver-wrench" 
                theme="indigo" 
                valColor="#4338CA" 
            />
        </div>

        <!-- 7. Pengaduan Selesai -->
        <div class="col-xl-3 col-md-6">
            <x-stat-card 
                title="Laporan Selesai" 
                value="{{ $stat['pengaduanSelesai'] }}" 
                sub="Tuntas ditangani teknis" 
                icon="fas fa-check-double" 
                theme="forest" 
                valColor="var(--nx-forest)" 
            />
        </div>

        <!-- 8. Pengaduan Terlambat Ditangani -->
        <div class="col-xl-3 col-md-6">
            <x-stat-card 
                title="Terlambat / Overdue" 
                value="{{ $stat['pengaduanTerlambat'] }}" 
                sub="{{ $stat['pengaduanTerlambat'] > 0 ? 'Melewati target SLA 3 hari' : 'Semua tertangani tepat waktu' }}" 
                icon="fas fa-hourglass-end" 
                theme="danger" 
                valColor="var(--nx-danger)" 
                style="{{ $stat['pengaduanTerlambat'] > 0 ? 'background: #FFF5F5 !important;' : '' }}"
            />
        </div>
    </div>

    <!-- 3 GRAFIK KEUANGAN DAN OPERASIONAL PENGADUAN -->
    <div class="row g-3 mb-4">
        <!-- Grafik 1: Tren Pengaduan Bulanan -->
        <div class="col-lg-8">
            <div class="card p-4 h-100 border-0 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-0" style="font-size: 1.05rem;">
                            <i class="fas fa-chart-line text-primary me-2"></i>Tren Pengaduan Warga Bulanan (Tahun 2026)
                        </h5>
                        <small class="text-muted">Fluktuasi jumlah keluhan masuk, pengerjaan, dan penyelesaian fasilitas per bulan</small>
                    </div>
                    <div class="d-flex gap-3 small fw-semibold">
                        <span class="d-flex align-items-center gap-1"><span style="width:10px;height:10px;border-radius:2px;background:#3B82F6;display:inline-block;"></span> Masuk</span>
                        <span class="d-flex align-items-center gap-1"><span style="width:10px;height:10px;border-radius:2px;background:#10B981;display:inline-block;"></span> Selesai</span>
                    </div>
                </div>
                <div style="height: 270px; position: relative;">
                    <canvas id="chartTrendPengaduan"></canvas>
                </div>
            </div>
        </div>

        <!-- Grafik 2 & 3: Distribusi Kategori & Kondisi Aset -->
        <div class="col-lg-4">
            <div class="row g-3">
                <div class="col-12">
                    <div class="card p-3.5 border-0 shadow-sm">
                        <h6 class="fw-bold mb-1" style="font-size: 0.92rem;">
                            <i class="fas fa-pie-chart text-success me-2"></i>Distribusi Kategori Keluhan
                        </h6>
                        <small class="text-muted d-block mb-2">Persentase masalah berdasarkan sarana</small>
                        <div style="height: 160px; position: relative;">
                            <canvas id="chartCategoryPengaduan"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card p-3.5 border-0 shadow-sm">
                        <h6 class="fw-bold mb-1" style="font-size: 0.92rem;">
                            <i class="fas fa-boxes-stacked text-warning me-2"></i>Ringkasan Kondisi Aset
                        </h6>
                        <small class="text-muted d-block mb-2">Kesiapan operasional seluruh inventaris</small>
                        <div style="height: 160px; position: relative;">
                            <canvas id="chartKondisiAsetCanvas"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- ========================================================================= -->
<!-- 2. TAB DATABASE ASET: GROUPING RT, SEARCH, FILTER, QR & MAPS LINK         -->
<!-- ========================================================================= -->
@if($activeTab === 'aset' && !$isWarga)
    <!-- TOOLBAR DATABASE ASET -->
    <div class="card p-3.5 mb-4 border-0 shadow-sm">
        <form method="GET" action="{{ route('pengaduan.index') }}" class="row g-2.5 align-items-end">
            <input type="hidden" name="tab" value="aset">
            
            <div class="col-lg-4 col-md-5">
                <label class="form-label small fw-bold text-muted mb-1">Pencarian Aset</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="q_aset" value="{{ request('q_aset') }}" class="form-control border-start-0" placeholder="Cari nama, ID barcode (misal TL-018), atau lokasi...">
                </div>
            </div>

            <div class="col-lg-2 col-md-3 col-6">
                <label class="form-label small fw-bold text-muted mb-1">Wilayah RT</label>
                @if($isAdminRt)
                    <input type="text" class="form-control form-control-sm bg-light" value="RT {{ $user->rt_number }}" readonly>
                    <input type="hidden" name="rt_number" value="{{ $user->rt_number }}">
                @else
                    <select name="rt_number" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all">Semua RT (006 - 010)</option>
                        @foreach($rtList as $rt)
                            <option value="{{ $rt }}" {{ $rtFilter === $rt ? 'selected' : '' }}>RT {{ $rt }}</option>
                        @endforeach
                    </select>
                @endif
            </div>

            <div class="col-lg-3 col-md-4 col-6">
                <label class="form-label small fw-bold text-muted mb-1">Jenis / Kategori</label>
                <select name="kategori_aset" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="all">Semua Kategori</option>
                    @foreach([
                        'Tiang Listrik & Lampu Jalan',
                        'CCTV & Keamanan',
                        'Hydrant & Utilitas Air',
                        'Saluran Air & Drainase',
                        'Fasilitas Umum',
                        'Infrastruktur Lingkungan',
                        'Sanitasi & Bak Sampah'
                    ] as $kat)
                        <option value="{{ $kat }}" {{ request('kategori_aset') === $kat ? 'selected' : '' }}>{{ $kat }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-2 col-md-4 col-6">
                <label class="form-label small fw-bold text-muted mb-1">Kondisi Fisik</label>
                <select name="kondisi_aset" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="all">Semua Kondisi</option>
                    <option value="baik" {{ request('kondisi_aset') === 'baik' ? 'selected' : '' }}>Kondisi Baik</option>
                    <option value="rusak_ringan" {{ request('kondisi_aset') === 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                    <option value="rusak_berat" {{ request('kondisi_aset') === 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                    <option value="dalam_perbaikan" {{ request('kondisi_aset') === 'dalam_perbaikan' ? 'selected' : '' }}>Dalam Perbaikan</option>
                </select>
            </div>

            <div class="col-lg-1 col-md-2 col-6 text-end">
                <button type="submit" class="btn btn-sm btn-primary w-100">
                    <i class="fas fa-filter"></i>
                </button>
            </div>
        </form>

        <!-- PILL REKAP RT -->
        @if(!$isAdminRt)
            <div class="d-flex align-items-center gap-2 mt-3 pt-2 border-top flex-wrap small">
                <span class="text-muted fw-semibold">Distribusi RT:</span>
                @foreach($rtList as $r)
                    <a href="{{ route('pengaduan.index', ['tab' => 'aset', 'rt_number' => $r]) }}"
                       class="badge rounded-pill text-decoration-none px-2.5 py-1 {{ $rtFilter === $r ? 'bg-primary text-white' : 'bg-light text-dark border' }}">
                        RT {{ $r }}: {{ $asetCountsByRt[$r] ?? 0 }} Aset
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    <!-- TABEL DATABASE ASET (STANDAR NEXANEST) -->
    <div class="card nx-table-card overflow-hidden shadow-xs border-0 mb-4">
        <div class="card-header bg-white p-3.5 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-0 text-dark" style="font-size: 0.96rem;">
                    <i class="fas fa-list-check text-primary me-2"></i>Daftar Inventaris Sarana Lingkungan
                </h5>
                <small class="text-muted" style="font-size: 0.75rem;">Total {{ $asets->count() }} aset terdata di wilayah {{ $rtFilter && $rtFilter !== 'all' ? 'RT ' . $rtFilter : 'RW 018' }}</small>
            </div>
            @if(!$isWarga)
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahAset">
                    <i class="fas fa-plus me-1"></i> Tambah Aset Baru
                </button>
            @endif
        </div>

        <div class="table-responsive nx-table-responsive">
            <table class="table table-hover align-middle nx-table mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" style="width: 25%;">Identitas &amp; ID Barcode</th>
                        <th style="width: 18%;">Kategori &amp; Wilayah</th>
                        <th style="width: 22%;">Lokasi &amp; Koordinat</th>
                        <th style="width: 14%;">Kondisi Terkini</th>
                        <th style="width: 11%;">Status Servis</th>
                        <th class="pe-4 text-end" style="width: 10%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($asets as $aset)
                        @php
                            $badge = $aset->kondisi_badge;
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                         style="width: 42px; height: 42px; background: var(--nx-mint-soft); color: var(--nx-forest); font-size: 1.1rem;">
                                        @if(str_contains($aset->nomor_aset, 'TL'))
                                            <i class="fas fa-bolt"></i>
                                        @elseif(str_contains($aset->nomor_aset, 'CCTV'))
                                            <i class="fas fa-video"></i>
                                        @elseif(str_contains($aset->nomor_aset, 'HYD'))
                                            <i class="fas fa-fire-extinguisher"></i>
                                        @elseif(str_contains($aset->nomor_aset, 'BSK'))
                                            <i class="fas fa-trash-can"></i>
                                        @elseif(str_contains($aset->nomor_aset, 'DRA'))
                                            <i class="fas fa-water"></i>
                                        @else
                                            <i class="fas fa-box-archive"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <a href="{{ route('aset.show', $aset->id) }}" class="fw-bold text-decoration-none d-block" style="color: var(--nx-text); font-size: 0.9rem;">
                                            {{ $aset->nama }}
                                        </a>
                                        <span class="badge rounded-pill px-2 py-0.5 font-monospace" style="background: var(--nx-bg); color: var(--nx-forest); border: 1px solid var(--nx-border); font-size: 0.72rem;">
                                            <i class="fas fa-qrcode me-1"></i>{{ $aset->nomor_aset }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <strong class="d-block small" style="color: var(--nx-text);">{{ $aset->kategori }}</strong>
                                <span class="badge rounded-pill px-2 py-0.5" style="background: var(--nx-powder); color: #1F517F; font-size: 0.72rem;">
                                    RT {{ $aset->rt_number }} / RW {{ $aset->rw_number }}
                                </span>
                            </td>
                            <td>
                                <span class="d-block text-truncate small" style="max-width: 250px;" title="{{ $aset->lokasi }}">
                                    <i class="fas fa-map-pin text-danger me-1"></i>{{ $aset->lokasi }}
                                </span>
                                @if($aset->latitude && $aset->longitude)
                                    <a href="{{ $aset->google_maps_url }}" target="_blank" class="small text-decoration-none text-primary" style="font-size: 0.75rem;">
                                        <i class="fas fa-map-location-dot me-1"></i>{{ $aset->latitude }}, {{ $aset->longitude }}
                                    </a>
                                @else
                                    <small class="text-muted" style="font-size: 0.75rem;">Titik GPS belum diset</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge rounded-pill px-2.5 py-1" style="background: {{ $badge['bg'] }}; color: {{ $badge['text'] }}; font-size: 0.75rem;">
                                    {{ $badge['label'] }}
                                </span>
                                @if($aset->pengaduans_count > 0)
                                    <small class="d-block text-danger fw-semibold mt-0.5" style="font-size: 0.7rem;">
                                        <i class="fas fa-bell me-1"></i>{{ $aset->pengaduans_count }} Laporan Masuk
                                    </small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-0.5 small" style="font-size: 0.72rem;">
                                    {{ ucfirst(str_replace('_', ' ', $aset->status_pemeliharaan)) }}
                                </span>
                                <small class="d-block text-muted" style="font-size: 0.68rem;">
                                    Cek: {{ $aset->terakhir_diperiksa ? $aset->terakhir_diperiksa->translatedFormat('d M Y') : '-' }}
                                </small>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('aset.show', $aset->id) }}" class="btn btn-sm btn-outline-primary px-2.5 py-1" title="Detail Aset & Riwayat">
                                        <i class="fas fa-eye me-1"></i> Detail
                                    </a>
                                    @if($aset->google_maps_url)
                                        <a href="{{ $aset->google_maps_url }}" target="_blank" class="btn btn-sm btn-outline-success px-2 py-1" title="Buka di Google Maps">
                                            <i class="fas fa-map-location-dot"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="mx-auto mb-2 rounded-circle d-flex align-items-center justify-content-center"
                                     style="width: 50px; height: 50px; background: var(--nx-bg); color: var(--nx-muted);">
                                    <i class="fas fa-box-open fa-lg"></i>
                                </div>
                                <h6 class="fw-bold mb-1">Tidak Ditemukan Aset Lingkungan</h6>
                                <p class="text-muted small mb-0">Coba ubah kata kunci pencarian atau sesuaikan filter wilayah RT.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif

<!-- ========================================================================= -->
<!-- 3. TAB PENGELOLAAN PENGADUAN WARGA (CARD LIST, MULTI-RT & REAL CITIZEN)  -->
<!-- ========================================================================= -->
@if($activeTab === 'pengaduan')
    <!-- FILTER BAR PENGADUAN -->
    <div class="card p-3.5 mb-4 border-0 shadow-sm">
        <form method="GET" action="{{ route('pengaduan.index') }}" class="row g-2.5 align-items-end">
            <input type="hidden" name="tab" value="pengaduan">

            <div class="col-lg-3 col-md-4">
                <label class="form-label small fw-bold text-muted mb-1">Cari Laporan</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="q_pengaduan" value="{{ request('q_pengaduan') }}" class="form-control border-start-0" placeholder="Nomor, judul, atau nama pelapor...">
                </div>
            </div>

            @if(!$isWarga)
                <div class="col-lg-2 col-md-3 col-6">
                    <label class="form-label small fw-bold text-muted mb-1">Wilayah RT</label>
                    @if($isAdminRt)
                        <input type="text" class="form-control form-control-sm bg-light" value="RT {{ $user->rt_number }}" readonly>
                        <input type="hidden" name="rt_number" value="{{ $user->rt_number }}">
                    @else
                        <select name="rt_number" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="all">Semua RT (006 - 010)</option>
                            @foreach($rtList as $rt)
                                <option value="{{ $rt }}" {{ $rtFilter === $rt ? 'selected' : '' }}>RT {{ $rt }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
            @endif

            <div class="col-lg-2 col-md-3 col-6">
                <label class="form-label small fw-bold text-muted mb-1">Kategori</label>
                <select name="kategori_pengaduan" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="all">Semua Kategori</option>
                    @foreach($categoriesList as $c)
                        <option value="{{ $c }}" {{ request('kategori_pengaduan') === $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-2 col-md-3 col-6">
                <label class="form-label small fw-bold text-muted mb-1">Status Penanganan</label>
                <select name="status_pengaduan" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="all">Semua Status</option>
                    <option value="menunggu" {{ request('status_pengaduan') === 'menunggu' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="diverifikasi" {{ request('status_pengaduan') === 'diverifikasi' ? 'selected' : '' }}>Diverifikasi</option>
                    <option value="ditugaskan" {{ request('status_pengaduan') === 'ditugaskan' ? 'selected' : '' }}>Ditugaskan</option>
                    <option value="proses" {{ request('status_pengaduan') === 'proses' ? 'selected' : '' }}>Sedang Diproses</option>
                    <option value="selesai" {{ request('status_pengaduan') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="ditolak" {{ request('status_pengaduan') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <div class="col-lg-2 col-md-3 col-6">
                <label class="form-label small fw-bold text-muted mb-1">Tingkat Prioritas</label>
                <select name="priority" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="all">Semua Prioritas</option>
                    <option value="darurat" {{ request('priority') === 'darurat' ? 'selected' : '' }}>Darurat / Emergency</option>
                    <option value="tinggi" {{ request('priority') === 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                    <option value="sedang" {{ request('priority') === 'sedang' ? 'selected' : '' }}>Sedang</option>
                    <option value="rendah" {{ request('priority') === 'rendah' ? 'selected' : '' }}>Rendah</option>
                </select>
            </div>

            <div class="col-lg-1 col-md-2 text-end">
                <button type="submit" class="btn btn-sm btn-primary w-100">
                    <i class="fas fa-search me-1"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- DAFTAR KARTU PENGADUAN WARGA (DIPERBAIKI & DIRAPIKAN) -->
    @if($pengaduans->isEmpty())
        <div class="card p-5 text-center border-0 shadow-sm">
            <div class="mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center"
                 style="width: 64px; height: 64px; background: var(--nx-mint-soft); color: var(--nx-forest);">
                <i class="fas fa-clipboard-check fa-2x"></i>
            </div>
            <h5 class="fw-bold mb-1">Tidak Ditemukan Laporan Pengaduan</h5>
            <p class="text-muted small mb-0">Semua laporan telah terselesaikan atau tidak ada data yang cocok dengan filter saat ini.</p>
        </div>
    @else
        <div class="d-flex flex-column gap-3 mb-4">
            @foreach($pengaduans as $p)
                @php
                    $sBadge = $p->status_badge;
                    $pBadge = $p->priority_badge;
                    $overdue = $p->is_overdue;
                @endphp
                <div class="card p-4 border-0 shadow-sm card-hover" style="border-left: 5px solid {{ $overdue ? 'var(--nx-danger)' : ($p->status === 'selesai' ? 'var(--nx-forest)' : '#3B82F6') }} !important;">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2 pb-2 border-bottom">
                        <div>
                            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                <span class="badge rounded-pill font-monospace px-2.5 py-1" style="background: var(--nx-bg); color: var(--nx-forest); border: 1px solid var(--nx-border); font-size: 0.78rem;">
                                    <i class="fas fa-hashtag me-1"></i>{{ $p->nomor_pengaduan ?: 'PGD-' . $p->id }}
                                </span>
                                <span class="badge rounded-pill px-2.5 py-1" style="background: {{ $pBadge['bg'] }}; color: {{ $pBadge['color'] }}; font-size: 0.75rem; font-weight: 700;">
                                    <i class="fas fa-flag me-1"></i>Prioritas: {{ $pBadge['label'] }}
                                </span>
                                @if($overdue)
                                    <span class="badge rounded-pill px-2.5 py-1 bg-danger text-white animate-pulse" style="font-size: 0.75rem;">
                                        <i class="fas fa-triangle-exclamation me-1"></i> Terlambat / Overdue
                                    </span>
                                @endif
                            </div>
                            <h5 class="fw-bold mb-1" style="color: var(--nx-text); font-size: 1.1rem;">
                                <a href="{{ route('pengaduan.show', $p->id) }}" class="text-decoration-none" style="color: inherit;">
                                    {{ $p->title }}
                                </a>
                            </h5>
                            <small class="text-muted">
                                <i class="fas fa-user-circle text-success me-1"></i>Pelapor: <strong>{{ $p->user->name ?? 'Warga' }}</strong>
                                <span class="badge rounded-pill px-2 py-0.5 ms-1" style="background: var(--nx-powder); color: #1F517F; font-size: 0.7rem;">RT {{ $p->rt_number }}</span>
                                • <i class="far fa-calendar-alt ms-2 me-1"></i>{{ $p->created_at ? $p->created_at->translatedFormat('d F Y · H:i') : '-' }} WIB
                            </small>
                        </div>

                        <!-- Status Badge & Tindak Lanjut Button -->
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: {{ $sBadge['bg'] }}; color: {{ $sBadge['color'] }}; font-size: 0.82rem;">
                                <i class="fas {{ $sBadge['icon'] }} me-1"></i> {{ strtoupper($p->status_label) }}
                            </span>
                            <a href="{{ route('pengaduan.show', $p->id) }}" class="btn btn-sm btn-primary px-3 py-1.5">
                                Detail &amp; Tindak Lanjut <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Uraian Masalah -->
                    <p class="text-muted mb-3" style="font-size: 0.92rem; line-height: 1.6;">
                        {{ $p->content }}
                    </p>

                    <!-- Info Aset & Lokasi Terkait -->
                    <div class="p-3 rounded-3 d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: var(--nx-bg); border: 1px solid var(--nx-border);">
                        <div class="d-flex align-items-center gap-2 flex-wrap small">
                            <span class="badge rounded-pill px-2.5 py-1" style="background: #FFFFFF; color: var(--nx-text); border: 1px solid var(--nx-border);">
                                <i class="fas fa-tag text-primary me-1"></i>{{ $p->category }}
                            </span>
                            @if($p->aset)
                                <a href="{{ route('aset.show', $p->aset->id) }}" class="badge rounded-pill text-decoration-none px-2.5 py-1" style="background: var(--nx-mint); color: var(--nx-forest);">
                                    <i class="fas fa-qrcode me-1"></i>Aset: {{ $p->aset->nomor_aset }} - {{ $p->aset->nama }}
                                </a>
                            @elseif($p->barcode_code)
                                <span class="badge rounded-pill px-2.5 py-1" style="background: var(--nx-mint-soft); color: var(--nx-forest);">
                                    <i class="fas fa-barcode me-1"></i>QR: {{ $p->barcode_code }}
                                </span>
                            @endif
                            <span class="text-muted">
                                <i class="fas fa-location-dot text-danger me-1"></i><strong>Lokasi:</strong> {{ $p->location_details }}
                            </span>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            @if($p->petugas_nama)
                                <span class="small text-muted">
                                    <i class="fas fa-user-gear text-primary me-1"></i>Petugas: <strong>{{ $p->petugas_nama }}</strong>
                                </span>
                            @endif
                            @if($p->google_maps_url)
                                <a href="{{ $p->google_maps_url }}" target="_blank" class="btn btn-xs btn-outline-success py-1 px-2.5" style="font-size: 0.75rem;">
                                    <i class="fas fa-map-location-dot me-1"></i> Google Maps
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Feedback Warga (Jika ada tanggapan pengurus) -->
                    @if($p->feedback_warga)
                        <div class="mt-2.5 p-2.5 rounded-3 d-flex align-items-start gap-2 small" style="background: var(--nx-mint-soft); color: var(--nx-forest);">
                            <i class="fas fa-comment-check mt-1"></i>
                            <div>
                                <strong>Tanggapan Pengurus:</strong> {{ $p->feedback_warga }}
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-3">
                {{ $pengaduans->links() }}
            </div>
        </div>
    @endif
@endif

<!-- ========================================================================= -->
<!-- 4. TAB DOKUMENTASI & IDENTIFIKASI ASET: DUA OPSI UTAMA (FOTO & SCAN QR)  -->
<!-- ========================================================================= -->
@if($activeTab === 'dokumentasi')
    <div class="row g-4 mb-4">
        <!-- OPSI A: AMBIL / UNGGAH FOTO DOKUMENTASI LAPANGAN -->
        <div class="col-lg-6">
            <div class="card p-4 h-100 border-0 shadow-sm">
                <div class="d-flex align-items-center gap-2.5 mb-3 pb-2 border-bottom">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width: 44px; height: 44px; background: var(--nx-mint); color: var(--nx-forest);">
                        <i class="fas fa-camera fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0" style="font-size: 1.05rem;">Opsi A: Ambil atau Unggah Foto</h5>
                        <small class="text-muted">Dokumentasikan foto kondisi awal, proses perbaikan, atau hasil akhir</small>
                    </div>
                </div>

                <form method="POST" action="{{ route('dokumentasi.store') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Pilihan Aset -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Hubungkan dengan Aset Terkait</label>
                        <select name="aset_id" id="dokAsetId" class="form-select form-select-sm">
                            <option value="">-- Tanpa Aset Khusus (Fasilitas Umum Bebas) --</option>
                            @foreach($allAsetsForDropdown as $as)
                                <option value="{{ $as->id }}">[{{ $as->nomor_aset }}] {{ $as->nama }} (RT {{ $as->rt_number }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Pilihan Pengaduan -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Hubungkan dengan Pengaduan Terkait (Opsional)</label>
                        <select name="pengaduan_id" id="dokPengaduanId" class="form-select form-select-sm">
                            <option value="">-- Tanpa Pengaduan Khusus (Pemeriksaan Mandiri) --</option>
                            @foreach($allPengaduansForDropdown as $pg)
                                <option value="{{ $pg->id }}">[{{ $pg->nomor_pengaduan ?: 'PGD-'.$pg->id }}] {{ $pg->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Kategori Dokumentasi -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Tipe Dokumentasi <span class="text-danger">*</span></label>
                        <select name="tipe_dokumentasi" class="form-select form-select-sm" required>
                            <option value="kondisi_awal">1. Kondisi Awal / Kerusakan Teridentifikasi</option>
                            <option value="kerusakan">2. Kerusakan Aset Spesifik</option>
                            <option value="proses_pemeriksaan">3. Proses Inspeksi / Pengerjaan Petugas</option>
                            <option value="hasil_perbaikan">4. Hasil Akhir Perbaikan / Selesai</option>
                        </select>
                    </div>

                    <!-- File / Kamera HP Input -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Ambil Foto Kamera / Pilih File <span class="text-danger">*</span></label>
                        <input type="file" name="foto" id="inputDokFoto" class="form-control form-control-sm" accept="image/*" capture="environment" required>
                        <small class="text-muted" style="font-size: 0.75rem;">Mendukung kamera HP langsung atau unggah galeri (JPEG, PNG, maks. 6MB).</small>
                    </div>

                    <!-- Preview Foto -->
                    <div id="dokFotoPreviewContainer" class="mb-3 text-center" style="display: none;">
                        <img id="dokFotoPreview" src="" alt="Preview" class="img-fluid rounded-3 border" style="max-height: 180px;">
                    </div>

                    <!-- Keterangan Foto -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Keterangan / Temuan Lapangan</label>
                        <textarea name="keterangan" class="form-control form-control-sm" rows="2.5" placeholder="Contoh: Penggantian trafo ballast PJU dan kabel sambungan telah selesai dites..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                        <i class="fas fa-upload me-1"></i> Simpan &amp; Hubungkan Dokumentasi
                    </button>
                </form>
            </div>
        </div>

        <!-- OPSI B: SCAN QR CODE ASET & DETEKSI GOOGLE MAPS -->
        <div class="col-lg-6">
            <div class="card p-4 h-100 border-0 shadow-sm" style="background: linear-gradient(180deg, #FFFFFF 0%, var(--nx-bg) 100%);">
                <div class="d-flex align-items-center gap-2.5 mb-3 pb-2 border-bottom">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width: 44px; height: 44px; background: #DBEAFE; color: #1D4ED8;">
                        <i class="fas fa-qrcode fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0" style="font-size: 1.05rem;">Opsi B: Scan QR Code Aset &amp; Maps</h5>
                        <small class="text-muted">Pindai kode QR fisik pada tiang, hydrant, atau CCTV via kamera HP</small>
                    </div>
                </div>

                <!-- SCANNER CAMERA VIEWFINDER & CONTROLS -->
                <div class="p-3 rounded-4 mb-3" style="background: #1B3B2B; color: #FFFFFF;">
                    <div id="qrReader" style="width: 100%; border-radius: 12px; overflow: hidden;"></div>

                    <div id="qrScannerControls" class="d-flex justify-content-between align-items-center mt-2.5">
                        <button type="button" id="btnStartScanner" class="btn btn-sm btn-success px-3">
                            <i class="fas fa-camera me-1"></i> Buka Kamera Scan QR
                        </button>
                        <button type="button" id="btnStopScanner" class="btn btn-sm btn-outline-light px-3" style="display: none;">
                            <i class="fas fa-stop me-1"></i> Hentikan Kamera
                        </button>
                        <span class="small text-muted" id="scanStatusText">Kamera siap digunakan</span>
                    </div>
                </div>

                <!-- PENCARIAN ID ASET SECARA MANUAL JIKA KAMERA TIDAK TERSEDIA -->
                <div class="p-3 rounded-3 mb-3" style="background: #FFFFFF; border: 1px solid var(--nx-border);">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="fas fa-keyboard me-1"></i>Pencarian ID Aset Manual (Jika Kamera Kendala):
                    </label>
                    <div class="input-group input-group-sm">
                        <input type="text" id="manualAsetCode" class="form-control font-monospace" placeholder="Contoh: TL-018-006-012, CCTV-018-008-005">
                        <button type="button" id="btnManualFindAset" class="btn btn-primary px-3">
                            <i class="fas fa-search me-1"></i> Cocokkan ID
                        </button>
                    </div>
                    
                    <!-- Quick Simulation Pills -->
                    <div class="d-flex align-items-center gap-1.5 mt-2 flex-wrap" style="font-size: 0.72rem;">
                        <span class="text-muted">Uji Kode:</span>
                        <button type="button" class="badge rounded-pill border bg-light text-dark btn-quick-code" data-code="TL-018-006-012">TL-018-006-012</button>
                        <button type="button" class="badge rounded-pill border bg-light text-dark btn-quick-code" data-code="HYD-018-007-003">HYD-018-007-003</button>
                        <button type="button" class="badge rounded-pill border bg-light text-dark btn-quick-code" data-code="CCTV-018-008-005">CCTV-018-008-005</button>
                        <button type="button" class="badge rounded-pill border bg-light text-dark btn-quick-code" data-code="BSK-018-009-001">BSK-018-009-001</button>
                        <button type="button" class="badge rounded-pill border bg-light text-dark btn-quick-code" data-code="PRT-018-010-002">PRT-018-010-002</button>
                    </div>
                </div>

                <!-- HASIL IDENTIFIKASI SCAN QR (DYNAMIC DISPLAY) -->
                <div id="scanResultCard" class="p-3.5 rounded-4" style="display: none; background: #FFFFFF; border: 2px solid var(--nx-forest); box-shadow: var(--nx-shadow);">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="badge rounded-pill px-2.5 py-1 mb-1 font-monospace" style="background: var(--nx-mint); color: var(--nx-forest); font-weight: 700;">
                                <i class="fas fa-qrcode me-1"></i><span id="resNomorAset"></span>
                            </span>
                            <h5 class="fw-bold mb-0" id="resNamaAset" style="color: var(--nx-text);"></h5>
                            <small class="text-muted" id="resKategoriAset"></small>
                        </div>
                        <span id="resKondisiBadge" class="badge rounded-pill px-2.5 py-1"></span>
                    </div>

                    <div class="p-2.5 rounded-3 mb-3 small" style="background: var(--nx-bg); border: 1px solid var(--nx-border);">
                        <div class="mb-1">
                            <i class="fas fa-map-pin text-danger me-1"></i><strong>Alamat Lokasi:</strong> <span id="resLokasiAset"></span>
                        </div>
                        <div class="mb-1">
                            <i class="fas fa-location-crosshairs text-primary me-1"></i><strong>Koordinat GPS:</strong> <span id="resCoordsAset" class="font-monospace"></span>
                        </div>
                        <div>
                            <i class="far fa-clock text-muted me-1"></i><strong>Terakhir Diperiksa:</strong> <span id="resTerakhirAset"></span>
                        </div>
                    </div>

                    <!-- TOMBOL GOOGLE MAPS DAN AKSI PENANGANAN -->
                    <div class="d-flex flex-column gap-2">
                        <a href="#" id="resGoogleMapsBtn" target="_blank" class="btn btn-success fw-bold py-2">
                            <i class="fas fa-map-location-dot me-1"></i> Buka Titik Lokasi di Google Maps
                        </a>
                        <div class="d-flex gap-2">
                            <a href="#" id="resDetailAsetBtn" class="btn btn-sm btn-outline-primary flex-fill">
                                <i class="fas fa-eye me-1"></i> Detail Aset &amp; Riwayat
                            </a>
                            <a href="#" id="resLaporPengaduanBtn" class="btn btn-sm btn-outline-warning text-dark flex-fill">
                                <i class="fas fa-triangle-exclamation me-1"></i> Lapor Kerusakan
                            </a>
                        </div>
                    </div>
                </div>

                <!-- PESAN NOT FOUND SCAN -->
                <div id="scanErrorCard" class="alert alert-danger p-3 rounded-3" style="display: none;">
                    <i class="fas fa-times-circle me-1"></i> <span id="scanErrorMsg">QR Code aset tidak ditemukan di database.</span>
                </div>
            </div>
        </div>
    </div>

    <!-- GALERI DOKUMENTASI TERBARU -->
    <div class="card p-4 border-0 shadow-sm">
        <h5 class="fw-bold mb-1" style="font-size: 1.05rem;">
            <i class="fas fa-images text-primary me-2"></i>Galeri Dokumentasi Foto &amp; Pemeriksaan Fisik Terkini
        </h5>
        <p class="text-muted small mb-3">Foto kondisi awal, bukti kerusakan, proses perbaikan teknis, dan verifikasi lapangan</p>

        @if($dokumentasis->isEmpty())
            <div class="text-center py-4 text-muted small">Belum ada foto dokumentasi lapangan diunggah.</div>
        @else
            <div class="row g-3">
                @foreach($dokumentasis as $dok)
                    <div class="col-md-3 col-sm-6">
                        <div class="card h-100 border p-2 rounded-3">
                            <div class="position-relative overflow-hidden rounded-2 mb-2" style="height: 140px; background: #E2E8F0;">
                                @if(file_exists(public_path('storage/' . $dok->foto)))
                                    <img src="{{ asset('storage/' . $dok->foto) }}" alt="Foto" class="w-100 h-100 object-fit-cover">
                                @else
                                    <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-muted" style="background: var(--nx-bg);">
                                        <i class="fas fa-camera fa-2x mb-1 text-secondary opacity-50"></i>
                                        <span style="font-size: 0.7rem;">Foto Tersimpan</span>
                                    </div>
                                @endif
                                <span class="badge position-absolute top-0 start-0 m-1.5 px-2 py-0.5" style="background: rgba(0,0,0,0.65); font-size: 0.65rem;">
                                    {{ ucfirst(str_replace('_', ' ', $dok->tipe_dokumentasi)) }}
                                </span>
                            </div>
                            <strong class="d-block text-truncate small" style="color: var(--nx-text);">
                                {{ $dok->aset ? $dok->aset->nama : ($dok->pengaduan ? $dok->pengaduan->title : 'Dokumentasi Bebas') }}
                            </strong>
                            <p class="text-muted small mb-1 text-truncate" style="font-size: 0.72rem;">{{ $dok->keterangan ?: 'Dokumentasi foto sarana.' }}</p>
                            <small class="text-muted mt-auto" style="font-size: 0.68rem;">
                                Oleh: {{ $dok->user->name ?? 'Petugas' }} • {{ $dok->created_at->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endif

<!-- ==================== MODAL TAMBAH ASET BARU (ADMIN RW / RT) ==================== -->
@if(!$isWarga)
<div class="modal fade" id="modalTambahAset" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form method="POST" action="{{ route('aset.store') }}" class="modal-content border-0 shadow">
            @csrf
            <div class="modal-header border-bottom py-3">
                <h6 class="modal-title fw-bold">
                    <i class="fas fa-plus-circle text-primary me-2"></i>Registrasi Inventaris Aset Lingkungan Baru
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Nomor ID Aset Unik (Format QR) <span class="text-danger">*</span></label>
                        <input type="text" name="nomor_aset" class="form-control form-control-sm font-monospace" placeholder="Contoh: TL-018-006-015, CCTV-018-007-002" required>
                        <small class="text-muted" style="font-size: 0.7rem;">Gunakan format standar: [JENIS]-018-[RT]-[NOMOR]</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Nama Aset Sarana <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control form-control-sm" placeholder="Contoh: Tiang Lampu PJU Kenanga 02" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Kategori / Jenis Aset <span class="text-danger">*</span></label>
                        <select name="kategori" class="form-select form-select-sm" required>
                            <option value="Tiang Listrik & Lampu Jalan">Tiang Listrik &amp; Lampu Jalan</option>
                            <option value="CCTV & Keamanan">CCTV &amp; Keamanan</option>
                            <option value="Hydrant & Utilitas Air">Hydrant &amp; Utilitas Air</option>
                            <option value="Saluran Air & Drainase">Saluran Air &amp; Drainase</option>
                            <option value="Fasilitas Umum">Fasilitas Umum</option>
                            <option value="Infrastruktur Lingkungan">Infrastruktur Lingkungan</option>
                            <option value="Sanitasi & Bak Sampah">Sanitasi &amp; Bak Sampah</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Wilayah RT <span class="text-danger">*</span></label>
                        @if($isAdminRt)
                            <input type="text" class="form-control form-control-sm bg-light" value="RT {{ $user->rt_number }}" readonly>
                            <input type="hidden" name="rt_number" value="{{ $user->rt_number }}">
                        @else
                            <select name="rt_number" class="form-select form-select-sm" required>
                                @foreach($rtList as $rt)
                                    <option value="{{ $rt }}">RT {{ $rt }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-bold">Alamat / Patokan Lokasi Fisik <span class="text-danger">*</span></label>
                        <input type="text" name="lokasi" class="form-control form-control-sm" placeholder="Contoh: Jl. Kenanga Depan Blok B No. 04" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Titik Koordinat Latitude (GPS)</label>
                        <input type="number" step="any" name="latitude" class="form-control form-control-sm" placeholder="-6.914744">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Titik Koordinat Longitude (GPS)</label>
                        <input type="number" step="any" name="longitude" class="form-control form-control-sm" placeholder="107.609810">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Kondisi Awal <span class="text-danger">*</span></label>
                        <select name="kondisi" class="form-select form-select-sm" required>
                            <option value="baik">Kondisi Baik (Normal)</option>
                            <option value="rusak_ringan">Rusak Ringan</option>
                            <option value="rusak_berat">Rusak Berat</option>
                            <option value="dalam_perbaikan">Dalam Perbaikan</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Status Pemeliharaan</label>
                        <select name="status_pemeliharaan" class="form-select form-select-sm">
                            <option value="normal">Normal</option>
                            <option value="jadwal_servis">Jadwal Servis Berkala</option>
                            <option value="perlu_tindakan">Perlu Tindakan Lapangan</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-bold">Spesifikasi Teknis / Keterangan</label>
                        <textarea name="spesifikasi" class="form-control form-control-sm" rows="2" placeholder="Tuliskan spesifikasi daya, kapasitas, merk, atau petunjuk operasional..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top py-2.5">
                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-sm btn-primary px-3">
                    <i class="fas fa-save me-1"></i> Daftarkan Aset
                </button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- HTML5 QR Code Library for Real Camera Scanner -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Inisialisasi Grafik Ringkasan jika berada di Tab Ringkasan
    @if($activeTab === 'ringkasan' && !$isWarga)
        // Grafik 1: Tren Pengaduan Bulanan
        const trendCtx = document.getElementById('chartTrendPengaduan')?.getContext('2d');
        if (trendCtx) {
            new Chart(trendCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartTrend['labels']) !!},
                    datasets: [
                        {
                            label: 'Laporan Masuk',
                            data: {!! json_encode($chartTrend['total']) !!},
                            backgroundColor: '#3B82F6',
                            borderRadius: 5,
                            maxBarThickness: 24,
                        },
                        {
                            label: 'Laporan Selesai',
                            data: {!! json_encode($chartTrend['selesai']) !!},
                            backgroundColor: '#10B981',
                            borderRadius: 5,
                            maxBarThickness: 24,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false } },
                        y: { beginAtZero: true, ticks: { stepSize: 1 } }
                    }
                }
            });
        }

        // Grafik 2: Kategori Pengaduan
        const catCtx = document.getElementById('chartCategoryPengaduan')?.getContext('2d');
        if (catCtx) {
            new Chart(catCtx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($chartCategory['labels']) !!},
                    datasets: [{
                        data: {!! json_encode($chartCategory['data']) !!},
                        backgroundColor: ['#285943', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6', '#10B981', '#6B7280'],
                        borderWidth: 2,
                        borderColor: '#FFFFFF'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } }
                }
            });
        }

        // Grafik 3: Kondisi Aset
        const asetCtx = document.getElementById('chartKondisiAsetCanvas')?.getContext('2d');
        if (asetCtx) {
            new Chart(asetCtx, {
                type: 'pie',
                data: {
                    labels: {!! json_encode($chartKondisiAset['labels']) !!},
                    datasets: [{
                        data: {!! json_encode($chartKondisiAset['data']) !!},
                        backgroundColor: ['#10B981', '#F59E0B', '#EF4444', '#0284C7'],
                        borderWidth: 2,
                        borderColor: '#FFFFFF'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } }
                }
            });
        }
    @endif

    // 2. Preview Foto Dokumentasi Input
    const inputDokFoto = document.getElementById('inputDokFoto');
    if (inputDokFoto) {
        inputDokFoto.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('dokFotoPreview').src = e.target.result;
                    document.getElementById('dokFotoPreviewContainer').style.display = 'block';
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    // 3. Logika Scan QR Code Aset & Pencarian Manual
    const allAsetsData = {!! json_encode($allAsetsJson) !!};

    function displayAsetResult(aset) {
        document.getElementById('scanErrorCard').style.display = 'none';
        document.getElementById('resNomorAset').textContent = aset.nomor_aset;
        document.getElementById('resNamaAset').textContent = aset.nama;
        document.getElementById('resKategoriAset').textContent = aset.kategori + ' · RT ' + aset.rt_number + ' RW ' + aset.rw_number;
        document.getElementById('resLokasiAset').textContent = aset.lokasi;
        document.getElementById('resCoordsAset').textContent = (aset.latitude && aset.longitude) ? (aset.latitude + ', ' + aset.longitude) : 'Belum diatur';
        document.getElementById('resTerakhirAset').textContent = aset.terakhir_diperiksa || '-';

        // Badge Kondisi
        const badgeSpan = document.getElementById('resKondisiBadge');
        if (aset.kondisi === 'baik') {
            badgeSpan.className = 'badge rounded-pill bg-success px-2.5 py-1';
            badgeSpan.textContent = 'Kondisi Baik';
        } else if (aset.kondisi === 'rusak_ringan') {
            badgeSpan.className = 'badge rounded-pill bg-warning text-dark px-2.5 py-1';
            badgeSpan.textContent = 'Rusak Ringan';
        } else if (aset.kondisi === 'rusak_berat') {
            badgeSpan.className = 'badge rounded-pill bg-danger px-2.5 py-1';
            badgeSpan.textContent = 'Rusak Berat';
        } else {
            badgeSpan.className = 'badge rounded-pill bg-info text-white px-2.5 py-1';
            badgeSpan.textContent = 'Dalam Perbaikan';
        }

        // Google Maps Link
        const gmapsBtn = document.getElementById('resGoogleMapsBtn');
        if (aset.latitude && aset.longitude) {
            gmapsBtn.href = `https://www.google.com/maps/search/?api=1&query=${aset.latitude},${aset.longitude}`;
            gmapsBtn.style.display = 'inline-block';
        } else if (aset.lokasi) {
            gmapsBtn.href = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(aset.lokasi + ', RW 018')}`;
            gmapsBtn.style.display = 'inline-block';
        } else {
            gmapsBtn.style.display = 'none';
        }

        // Direct Action Buttons
        document.getElementById('resDetailAsetBtn').href = `/aset/${aset.id}`;
        document.getElementById('resLaporPengaduanBtn').href = `/pengaduan/create?aset_id=${aset.id}&aset_code=${aset.nomor_aset}`;

        // Auto select on Option A dropdown if open
        const dokAsetSelect = document.getElementById('dokAsetId');
        if (dokAsetSelect) {
            dokAsetSelect.value = aset.id;
        }

        document.getElementById('scanResultCard').style.display = 'block';

        // Suara beep konfirmasi
        try {
            const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.type = 'sine';
            osc.frequency.value = 880;
            gain.gain.setValueAtTime(0.06, audioCtx.currentTime);
            osc.start();
            setTimeout(() => osc.stop(), 150);
        } catch(e) {}
    }

    function handleFindCode(code) {
        code = code.trim().toUpperCase();
        if (!code) return;

        // Cek di array lokal terlebih dahulu
        if (allAsetsData && allAsetsData[code]) {
            displayAsetResult(allAsetsData[code]);
            return;
        }

        // AJAX Fallback ke Backend API
        fetch(`/aset/find-by-code/${encodeURIComponent(code)}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.aset) {
                    displayAsetResult(data.aset);
                } else {
                    document.getElementById('scanResultCard').style.display = 'none';
                    document.getElementById('scanErrorMsg').textContent = data.message || `Aset dengan ID '${code}' tidak ditemukan di database.`;
                    document.getElementById('scanErrorCard').style.display = 'block';
                }
            })
            .catch(err => {
                document.getElementById('scanResultCard').style.display = 'none';
                document.getElementById('scanErrorMsg').textContent = `Aset dengan ID '${code}' tidak ditemukan di database lingkungan RW 018.`;
                document.getElementById('scanErrorCard').style.display = 'block';
            });
    }

    // Tombol Cari Manual
    const btnManual = document.getElementById('btnManualFindAset');
    if (btnManual) {
        btnManual.addEventListener('click', function() {
            const val = document.getElementById('manualAsetCode').value;
            handleFindCode(val);
        });
    }

    // Quick Simulation Pills
    document.querySelectorAll('.btn-quick-code').forEach(btn => {
        btn.addEventListener('click', function() {
            const code = this.getAttribute('data-code');
            document.getElementById('manualAsetCode').value = code;
            handleFindCode(code);
        });
    });

    // Inisialisasi Real Camera QR Scanner (Html5Qrcode)
    let html5QrCode = null;
    const btnStart = document.getElementById('btnStartScanner');
    const btnStop = document.getElementById('btnStopScanner');
    const scanStatusText = document.getElementById('scanStatusText');

    if (btnStart && typeof Html5Qrcode !== 'undefined') {
        btnStart.addEventListener('click', function() {
            html5QrCode = new Html5Qrcode("qrReader");
            const config = { fps: 10, qrbox: { width: 220, height: 220 } };

            scanStatusText.textContent = "Mengakses kamera...";
            html5QrCode.start(
                { facingMode: "environment" },
                config,
                (decodedText, decodedResult) => {
                    scanStatusText.textContent = "QR Terdeteksi: " + decodedText;
                    document.getElementById('manualAsetCode').value = decodedText;
                    handleFindCode(decodedText);
                    // Hentikan scanner setelah berhasil deteksi
                    html5QrCode.stop().then(() => {
                        btnStart.style.display = 'inline-block';
                        btnStop.style.display = 'none';
                    });
                },
                (errorMessage) => {
                    // Scanning in progress
                }
            ).then(() => {
                btnStart.style.display = 'none';
                btnStop.style.display = 'inline-block';
                scanStatusText.textContent = "Arahkan kamera ke QR Code Aset";
            }).catch(err => {
                scanStatusText.textContent = "Kamera tidak dapat diakses. Gunakan input manual ID.";
            });
        });

        if (btnStop) {
            btnStop.addEventListener('click', function() {
                if (html5QrCode) {
                    html5QrCode.stop().then(() => {
                        btnStart.style.display = 'inline-block';
                        btnStop.style.display = 'none';
                        scanStatusText.textContent = "Kamera dihentikan";
                    });
                }
            });
        }
    }
});
</script>
@endpush
