@extends('layouts.app')

@section('title', 'Pusat Laporan & Evaluasi Lingkungan — NexaNest')

@section('content')
<div class="nx-page-header">
    <div>
        <span class="badge rounded-pill mb-2 px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
            <i class="fas fa-leaf me-1"></i> Smart &amp; Sustainable Neighborhood · RW 018
        </span>
        <h1 class="nx-page-title">Pusat Laporan &amp; Evaluasi Lingkungan</h1>
        <p class="nx-page-subtitle">
            Pusat pelaporan berkala, pemantauan kebersihan, saluran drainase, penghijauan, dan evaluasi kondisi lingkungan seluruh RT di wilayah RW 018.
        </p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        @if($isAdminRt)
            @if($currentRtReport)
                @if(in_array($currentRtReport->status, ['draft', 'perlu_revisi']))
                    <a href="{{ route('lingkungan.edit', $currentRtReport->id) }}" class="btn btn-warning fw-bold text-dark" style="border-radius: 12px;">
                        <i class="fas fa-pen-to-square me-1"></i> {{ $currentRtReport->status === 'perlu_revisi' ? 'Perbaiki Laporan (Revisi)' : 'Lanjutkan Draft Laporan' }}
                    </a>
                @else
                    <a href="{{ route('lingkungan.show', $currentRtReport->id) }}" class="btn btn-outline-success fw-bold" style="border-radius: 12px;">
                        <i class="fas fa-file-lines me-1"></i> Lihat Laporan Bulan Ini ({{ $currentRtReport->status_label }})
                    </a>
                @endif
            @else
                <a href="{{ route('lingkungan.create', ['rt_number' => Auth::user()->rt_number, 'periode_bulan' => $selectedBulan, 'periode_tahun' => $selectedTahun]) }}" class="btn btn-primary fw-bold" style="border-radius: 12px;">
                    <i class="fas fa-plus me-1"></i> Buat Laporan Lingkungan RT {{ Auth::user()->rt_number }}
                </a>
            @endif
        @elseif($isAdminRw)
            <button type="button" class="btn btn-outline-secondary fw-bold" data-bs-toggle="modal" data-bs-target="#modalPengaturanPeriode" style="border-radius: 12px;">
                <i class="fas fa-clock-rotate-left me-1"></i> Atur Batas Waktu Periode
            </button>
            <a href="{{ route('lingkungan.create', ['periode_bulan' => $selectedBulan, 'periode_tahun' => $selectedTahun]) }}" class="btn btn-primary fw-bold" style="border-radius: 12px;">
                <i class="fas fa-plus me-1"></i> Input Laporan RT Baru
            </a>
        @endif
    </div>
</div>

<!-- PERIOD & DEADLINE BANNER -->
<div class="card p-3 mb-4 shadow-xs" style="background: linear-gradient(135deg, #F4F9F5 0%, #FFFFFF 100%); border-left: 4px solid var(--nx-forest); border-radius: 12px;">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <span class="rounded-circle d-flex align-items-center justify-content-center shadow-xs" style="width: 44px; height: 44px; background: var(--nx-mint); color: var(--nx-forest);">
                <i class="fas fa-calendar-check fa-lg"></i>
            </span>
            <div>
                <div class="d-flex align-items-center gap-2">
                    <h5 class="fw-bold mb-0 text-dark">
                        Periode Pelaporan: {{ \Carbon\Carbon::createFromDate($selectedTahun, $selectedBulan, 1)->translatedFormat('F Y') }}
                    </h5>
                    @if($isPastDeadline)
                        <span class="badge bg-danger rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                            <i class="fas fa-circle-exclamation me-1"></i> Lewat Batas Waktu
                        </span>
                    @else
                        <span class="badge bg-success rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                            <i class="fas fa-clock me-1"></i> Periode Terbuka
                        </span>
                    @endif
                </div>
                <small class="text-muted d-block mt-0.5" style="font-size: 0.78rem;">
                    Batas Pengiriman: <strong>{{ \Carbon\Carbon::parse($periodeSetting->batas_waktu)->translatedFormat('l, d F Y') }}</strong> (Pukul 23:59 WIB)
                    @if($periodeSetting->catatan_instruksi_rw)
                        · <span class="text-dark fw-semibold"><i class="fas fa-bullhorn text-warning me-1"></i>Instruksi RW:</span> "{{ $periodeSetting->catatan_instruksi_rw }}"
                    @endif
                </small>
            </div>
        </div>

        <!-- FORM FILTER PERIODE CEPAT -->
        <form method="GET" action="{{ route('lingkungan.index') }}" class="d-flex align-items-center gap-2">
            <input type="hidden" name="tab" value="{{ $activeTab }}">
            @if(!empty($selectedRt) && $isAdminRw)
                <input type="hidden" name="rt_number" value="{{ $selectedRt }}">
            @endif
            <div class="input-group input-group-sm">
                <select name="periode_bulan" class="form-select" onchange="this.form.submit()">
                    @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $k => $nama)
                        <option value="{{ $k }}" {{ $selectedBulan == $k ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
                <select name="periode_tahun" class="form-select" onchange="this.form.submit()">
                    @foreach(['2025','2026','2027'] as $y)
                        <option value="{{ $y }}" {{ $selectedTahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

<!-- NAV TABS 4 BAGIAN UTAMA -->
<ul class="nav nav-pills mb-4 gap-2 border-bottom pb-2" id="lingkunganTabNav" role="tablist">
    <li class="nav-item">
        <a class="nav-link px-3.5 py-2 fw-bold {{ $activeTab === 'ringkasan' ? 'active shadow-xs' : 'text-secondary' }}" 
           href="{{ route('lingkungan.index', ['tab' => 'ringkasan', 'periode_bulan' => $selectedBulan, 'periode_tahun' => $selectedTahun, 'rt_number' => $selectedRt]) }}"
           style="border-radius: 10px; font-size: 0.88rem; {{ $activeTab === 'ringkasan' ? 'background: var(--nx-forest); color: #fff;' : 'background: #fff;' }}">
            <i class="fas fa-chart-line me-1.5"></i> 1. Ringkasan Lingkungan
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link px-3.5 py-2 fw-bold {{ $activeTab === 'laporan_rt' ? 'active shadow-xs' : 'text-secondary' }}" 
           href="{{ route('lingkungan.index', ['tab' => 'laporan_rt', 'periode_bulan' => $selectedBulan, 'periode_tahun' => $selectedTahun, 'rt_number' => $selectedRt]) }}"
           style="border-radius: 10px; font-size: 0.88rem; {{ $activeTab === 'laporan_rt' ? 'background: var(--nx-forest); color: #fff;' : 'background: #fff;' }}">
            <i class="fas fa-sitemap me-1.5"></i> 2. Laporan Per RT
            <span class="badge rounded-pill bg-light text-dark ms-1.5" style="font-size: 0.72rem;">{{ $laporans->total() }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link px-3.5 py-2 fw-bold {{ $activeTab === 'evaluasi' ? 'active shadow-xs' : 'text-secondary' }}" 
           href="{{ route('lingkungan.index', ['tab' => 'evaluasi', 'periode_bulan' => $selectedBulan, 'periode_tahun' => $selectedTahun, 'rt_number' => $selectedRt]) }}"
           style="border-radius: 10px; font-size: 0.88rem; {{ $activeTab === 'evaluasi' ? 'background: var(--nx-forest); color: #fff;' : 'background: #fff;' }}">
            <i class="fas fa-clipboard-check me-1.5"></i> 3. Evaluasi &amp; Tindak Lanjut
            @if(count($pendingEvaluasiList) > 0)
                <span class="badge rounded-pill bg-warning text-dark ms-1.5" style="font-size: 0.72rem;">{{ count($pendingEvaluasiList) }} Antrean</span>
            @endif
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link px-3.5 py-2 fw-bold {{ $activeTab === 'riwayat' ? 'active shadow-xs' : 'text-secondary' }}" 
           href="{{ route('lingkungan.index', ['tab' => 'riwayat', 'periode_bulan' => $selectedBulan, 'periode_tahun' => $selectedTahun, 'rt_number' => $selectedRt]) }}"
           style="border-radius: 10px; font-size: 0.88rem; {{ $activeTab === 'riwayat' ? 'background: var(--nx-forest); color: #fff;' : 'background: #fff;' }}">
            <i class="fas fa-clock-rotate-left me-1.5"></i> 4. Riwayat Laporan
        </a>
    </li>
</ul>

<!-- ========================================================================= -->
<!-- TAB 1: RINGKASAN LINGKUNGAN -->
<!-- ========================================================================= -->
@if($activeTab === 'ringkasan')
    <!-- SUMMARY STATISTIC CARDS -->
    <div class="row g-3 mb-4">
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card p-3 h-100 border shadow-xs" style="background: #F8FAF8; border-color: rgba(40, 89, 67, 0.2) !important; border-radius: 14px;">
                <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.68rem;">Total Wilayah RT</span>
                <h3 class="fw-bold mb-0 text-dark">{{ $totalRt }} <span class="fs-6 text-muted fw-normal">RT</span></h3>
                <small class="text-muted" style="font-size: 0.72rem;">Wilayah aktif RW 018</small>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card p-3 h-100 border shadow-xs" style="background: #F0F9F3; border-color: rgba(40, 89, 67, 0.2) !important; border-radius: 14px;">
                <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.68rem;">Sudah Mengirim</span>
                <h3 class="fw-bold mb-0" style="color: var(--nx-forest);">{{ $rtSubmittedCount }} <span class="fs-6 text-muted fw-normal">/ {{ $totalRt }}</span></h3>
                <small class="text-success fw-semibold" style="font-size: 0.72rem;">{{ round(($rtSubmittedCount / $totalRt) * 100) }}% partisipasi</small>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card p-3 h-100 border shadow-xs" style="background: #FBFBFB; border-color: rgba(113, 128, 150, 0.2) !important; border-radius: 14px;">
                <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.68rem;">Belum Mengirim</span>
                <h3 class="fw-bold mb-0 text-secondary">{{ $rtBelumKirimCount }} <span class="fs-6 text-muted fw-normal">RT</span></h3>
                <small class="text-muted" style="font-size: 0.72rem;">Menunggu submission</small>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card p-3 h-100 border shadow-xs" style="background: #FFFDF0; border-color: rgba(226, 160, 63, 0.25) !important; border-radius: 14px;">
                <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.68rem;">Menunggu Evaluasi</span>
                <h3 class="fw-bold mb-0 text-warning">{{ $rtMenungguCount }} <span class="fs-6 text-muted fw-normal">Laporan</span></h3>
                <small class="text-muted" style="font-size: 0.72rem;">Perlu ditinjau RW</small>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card p-3 h-100 border shadow-xs" style="background: #EBF8F2; border-color: rgba(30, 126, 52, 0.2) !important; border-radius: 14px;">
                <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.68rem;">Laporan Diterima</span>
                <h3 class="fw-bold mb-0 text-success">{{ $rtDiterimaCount }} <span class="fs-6 text-muted fw-normal">Laporan</span></h3>
                <small class="text-success" style="font-size: 0.72rem;">Disetujui resmi RW</small>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card p-3 h-100 border shadow-xs" style="background: #FFF5F5; border-color: rgba(220, 53, 69, 0.2) !important; border-radius: 14px;">
                <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.68rem;">Perlu Revisi</span>
                <h3 class="fw-bold mb-0 text-danger">{{ $rtRevisiCount }} <span class="fs-6 text-muted fw-normal">Laporan</span></h3>
                <small class="text-danger" style="font-size: 0.72rem;">Proses tindak lanjut</small>
            </div>
        </div>
    </div>

    <!-- STATUS MONITORING 5 RT BLOK CEPAT -->
    <div class="card p-3.5 mb-4 shadow-xs">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fas fa-table-cells-large text-success me-2"></i>Status Kepatuhan Laporan 5 RT Periode {{ \Carbon\Carbon::createFromDate($selectedTahun, $selectedBulan, 1)->translatedFormat('F Y') }}
                </h6>
                <small class="text-muted" style="font-size: 0.75rem;">Pantau secara real-time status pengiriman dan kelengkapan evaluasi masing-masing pengurus RT.</small>
            </div>
            <a href="{{ route('lingkungan.index', ['tab' => 'laporan_rt', 'periode_bulan' => $selectedBulan, 'periode_tahun' => $selectedTahun]) }}" class="btn btn-sm btn-outline-success fw-bold px-3" style="border-radius: 8px;">
                Lihat Daftar Laporan RT <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-3">
            @foreach($rtMatrix as $rtNum => $m)
                <div class="col-lg col-md-4 col-sm-6">
                    <div class="p-3 rounded-3 border h-100 d-flex flex-column justify-content-between bg-white shadow-xs" style="border-top: 3px solid {{ $m['report'] && $m['report']->status === 'diterima' ? '#285943' : ($m['report'] && $m['report']->status === 'perlu_revisi' ? '#E05353' : ($m['report'] ? '#A8C9B5' : '#CBD5E1')) }} !important;">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-1.5">
                                <h6 class="fw-bold mb-0 text-dark">RT {{ $rtNum }}</h6>
                                <span class="badge {{ $m['badge_class'] }} rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                                    {{ $m['status_label'] }}
                                </span>
                            </div>
                            <small class="text-muted d-block mb-2" style="font-size: 0.72rem;">
                                Admin: <strong>{{ $m['admin_user'] ? $m['admin_user']->name : 'Ketua RT ' . $rtNum }}</strong>
                            </small>

                            @if($m['report'])
                                <div class="p-2 rounded-2 bg-light small mb-2" style="font-size: 0.72rem; line-height: 1.35;">
                                    <div class="d-flex justify-content-between text-muted mb-0.5">
                                        <span>Kebersihan:</span>
                                        <strong class="text-dark">{{ $m['report']->kondisi_kebersihan }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between text-muted">
                                        <span>Drainase:</span>
                                        <strong class="text-dark">{{ $m['report']->kondisi_drainase }}</strong>
                                    </div>
                                </div>
                            @else
                                <div class="p-2 rounded-2 bg-light small mb-2 text-center text-muted" style="font-size: 0.72rem;">
                                    <em>Belum ada submission laporan.</em>
                                </div>
                            @endif
                        </div>

                        <div class="pt-2 border-top text-end">
                            @if($m['report'])
                                <a href="{{ route('lingkungan.show', $m['report']->id) }}" class="btn btn-sm btn-outline-secondary w-100 py-1" style="font-size: 0.72rem; font-weight: 600;">
                                    <i class="fas fa-eye me-1"></i> Buka Laporan
                                </a>
                            @else
                                @if($isAdminRt && Auth::user()->rt_number === $rtNum)
                                    <a href="{{ route('lingkungan.create', ['rt_number' => $rtNum, 'periode_bulan' => $selectedBulan, 'periode_tahun' => $selectedTahun]) }}" class="btn btn-sm btn-primary w-100 py-1" style="font-size: 0.72rem; font-weight: 700;">
                                        <i class="fas fa-plus me-1"></i> Buat Laporan
                                    </a>
                                @else
                                    <span class="text-muted small d-block py-1" style="font-size: 0.7rem;">Menunggu input RT</span>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- CHARTS SECTION: VISUALISASI PERKEMBANGAN LINGKUNGAN BULANAN -->
    <div class="row g-3 mb-4">
        <!-- GRAFIK 1: KONDISI KEBERSIHAN PER BULAN -->
        <div class="col-lg-6">
            <div class="card p-3.5 h-100 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.92rem;">
                        <i class="fas fa-broom text-success me-2"></i>Perkembangan Kondisi Kebersihan Lingkungan (Jan - Des {{ $selectedTahun }})
                    </h6>
                    <span class="badge bg-light text-muted border px-2 py-1" style="font-size: 0.7rem;">5 RT RW 018</span>
                </div>
                <div style="height: 250px;">
                    <canvas id="chartKebersihan"></canvas>
                </div>
                <div class="pt-3 border-top mt-2 d-flex justify-content-around text-center small" style="font-size: 0.74rem;">
                    <div><span class="d-inline-block rounded-circle me-1" style="width: 8px; height: 8px; background: #285943;"></span> Sangat Bersih &amp; Tertata</div>
                    <div><span class="d-inline-block rounded-circle me-1" style="width: 8px; height: 8px; background: #A8C9B5;"></span> Cukup Bersih &amp; Terawat</div>
                    <div><span class="d-inline-block rounded-circle me-1" style="width: 8px; height: 8px; background: #E05353;"></span> Perlu Kerja Bakti</div>
                </div>
            </div>
        </div>

        <!-- GRAFIK 2: KONDISI DRAINASE PER BULAN -->
        <div class="col-lg-6">
            <div class="card p-3.5 h-100 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.92rem;">
                        <i class="fas fa-water text-primary me-2"></i>Kondisi Kelancaran Saluran Drainase (Jan - Des {{ $selectedTahun }})
                    </h6>
                    <span class="badge bg-light text-muted border px-2 py-1" style="font-size: 0.7rem;">Bebas Genangan</span>
                </div>
                <div style="height: 250px;">
                    <canvas id="chartDrainase"></canvas>
                </div>
                <div class="pt-3 border-top mt-2 d-flex justify-content-around text-center small" style="font-size: 0.74rem;">
                    <div><span class="d-inline-block rounded-circle me-1" style="width: 8px; height: 8px; background: #1F567A;"></span> Lancar Tanpa Hambatan</div>
                    <div><span class="d-inline-block rounded-circle me-1" style="width: 8px; height: 8px; background: #E2A03F;"></span> Sedimen Cukup Tebal</div>
                    <div><span class="d-inline-block rounded-circle me-1" style="width: 8px; height: 8px; background: #DC3545;"></span> Rawan Genangan</div>
                </div>
            </div>
        </div>

        <!-- GRAFIK 3: PERBANDINGAN KONDISI ANTAR-RT -->
        <div class="col-lg-7">
            <div class="card p-3.5 h-100 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.92rem;">
                        <i class="fas fa-scale-balanced text-warning me-2"></i>Perbandingan Skor Kualitas Lingkungan Antar-RT (Tahun {{ $selectedTahun }})
                    </h6>
                    <small class="text-muted" style="font-size: 0.72rem;">Indeks kebersihan &amp; drainase (Skala 0-100)</small>
                </div>
                <div style="height: 240px;">
                    <canvas id="chartPerbandinganRt"></canvas>
                </div>
            </div>
        </div>

        <!-- GRAFIK 4: TREN VOLUME & STATUS LAPORAN -->
        <div class="col-lg-5">
            <div class="card p-3.5 h-100 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.92rem;">
                        <i class="fas fa-chart-column text-success me-2"></i>Tren Pengiriman Laporan Bulanan
                    </h6>
                    <small class="text-muted" style="font-size: 0.72rem;">Jan - Des {{ $selectedTahun }}</small>
                </div>
                <div style="height: 240px;">
                    <canvas id="chartLaporanBulanan"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- INTEGRASI LINTAS FITUR: PENGADUAN LINGKUNGAN, BANK SAMPAH, DAN AGENDA (STANDAR NEXANEST) -->
    <div class="row g-3">
        <!-- INTEGRASI 1: PENGADUAN LINGKUNGAN AKTIF -->
        <div class="col-lg-5">
            <div class="card p-4 h-100 shadow-xs d-flex flex-column">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background: #FCE8E9; color: var(--nx-danger);">
                            <i class="fas fa-triangle-exclamation"></i>
                        </span>
                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.92rem;">Pengaduan Lingkungan Aktif</h6>
                    </div>
                    <a href="{{ route('pengaduan.index') }}" class="btn btn-sm btn-link text-decoration-none p-0 small fw-bold" style="color: var(--nx-forest); font-size: 0.76rem;">
                        Lihat Semua <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="list-group list-group-flush flex-grow-1">
                    @forelse($pengaduanLingkungan as $aduan)
                        <div class="list-group-item px-0 py-2 border-bottom">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge bg-light text-dark border fw-bold" style="font-size: 0.68rem;">RT {{ $aduan->rt_number }}</span>
                                <span class="badge {{ $aduan->status === 'pending' ? 'bg-warning text-dark' : 'bg-primary' }} rounded-pill" style="font-size: 0.65rem;">
                                    {{ ucfirst($aduan->status) }}
                                </span>
                            </div>
                            <strong class="text-dark d-block small" style="font-size: 0.82rem;">{{ $aduan->title }}</strong>
                            <small class="text-muted d-block text-truncate" style="font-size: 0.72rem;">{{ $aduan->content }}</small>
                        </div>
                    @empty
                        <div class="py-4 text-center text-muted small my-auto">
                            <i class="fas fa-circle-check text-success fa-2x mb-2 d-block"></i>
                            Tidak ada pengaduan kebersihan atau kerusakan lingkungan yang aktif.
                        </div>
                    @endforelse
                </div>
                <div class="pt-3 border-top mt-auto text-end">
                    <a href="{{ route('pengaduan.index') }}" class="btn btn-sm btn-outline-secondary w-100" style="font-size: 0.78rem; font-weight: 600;">
                        <i class="fas fa-list me-1"></i> Buka Manajemen Pengaduan
                    </a>
                </div>
            </div>
        </div>

        <!-- INTEGRASI 2: REKAP BANK SAMPAH & AGENDA KERJA BAKTI -->
        <div class="col-lg-7">
            <div class="row g-3 h-100">
                <div class="col-sm-6">
                    <div class="card p-4 h-100 shadow-xs d-flex flex-column" style="background: linear-gradient(135deg, #F5FBF6 0%, #FFFFFF 100%);">
                        <div class="d-flex align-items-center gap-2 mb-2.5">
                            <span class="rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background: var(--nx-mint); color: var(--nx-forest);">
                                <i class="fas fa-recycle"></i>
                            </span>
                            <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.9rem;">Pilah Sampah &amp; Bank Sampah</h6>
                        </div>
                        <p class="text-muted small mb-2" style="font-size: 0.76rem;">
                            Akumulasi setoran limbah daur ulang yang tercatat pada Bank Sampah RW 018 bulan ini:
                        </p>
                        <h3 class="fw-bold mb-1" style="color: var(--nx-forest);">
                            {{ number_format($bankSampahMonthKg, 1) }} <span class="fs-6 text-muted fw-normal">Kg</span>
                        </h3>
                        <small class="text-muted d-block mb-3" style="font-size: 0.72rem;">Total sampah plastik, kardus, logam, dan minyak jelantah terpilah.</small>
                        <a href="{{ route('bank_sampah.index') }}" class="btn btn-sm btn-outline-success mt-auto fw-bold" style="border-radius: 8px; font-size: 0.78rem;">
                            Buka Bank Sampah RW <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="card p-4 h-100 shadow-xs d-flex flex-column" style="background: linear-gradient(135deg, #F8FAFD 0%, #FFFFFF 100%);">
                        <div class="d-flex align-items-center gap-2 mb-2.5">
                            <span class="rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background: var(--nx-powder); color: #1e4b6d;">
                                <i class="fas fa-hand-holding-hand"></i>
                            </span>
                            <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.9rem;">Agenda Kerja Bakti</h6>
                        </div>
                        <p class="text-muted small mb-2" style="font-size: 0.76rem;">
                            Kegiatan gotong royong dan pembersihan lingkungan yang terdaftar di kalender RW:
                        </p>
                        <div class="small mb-3 flex-grow-1">
                            @forelse($agendaLingkungan as $ag)
                                <div class="mb-2 pb-1.5 border-bottom">
                                    <strong class="text-dark d-block text-truncate" style="font-size: 0.8rem;">{{ $ag->judul }}</strong>
                                    <small class="text-muted" style="font-size: 0.7rem;"><i class="fas fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($ag->tanggal_waktu)->translatedFormat('d M Y') }} · {{ $ag->lokasi }}</small>
                                </div>
                            @empty
                                <small class="text-muted d-block py-2">Belum ada agenda kerja bakti terdaftar bulan ini.</small>
                            @endforelse
                        </div>
                        <a href="{{ route('kalender.index') }}" class="btn btn-sm btn-outline-primary mt-auto fw-bold" style="border-radius: 8px; font-size: 0.78rem;">
                            Buka Kalender Kegiatan <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
                        <p class="text-muted small mb-2" style="font-size: 0.75rem;">
                            Kegiatan gotong royong dan pembersihan lingkungan yang terdaftar di kalender RW:
                        </p>
                        <div class="small">
                            @forelse($agendaLingkungan as $ag)
                                <div class="mb-2 pb-1.5 border-bottom">
                                    <strong class="text-dark d-block text-truncate" style="font-size: 0.8rem;">{{ $ag->judul }}</strong>
                                    <small class="text-muted" style="font-size: 0.7rem;"><i class="fas fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($ag->tanggal_waktu)->translatedFormat('d M Y') }} · {{ $ag->lokasi }}</small>
                                </div>
                            @empty
                                <small class="text-muted d-block py-2">Belum ada agenda kerja bakti terdaftar bulan ini.</small>
                            @endforelse
                        </div>
                        <a href="{{ route('kalender.index') }}" class="btn btn-sm btn-outline-primary mt-auto fw-bold" style="border-radius: 8px; font-size: 0.75rem;">
                            Buka Kalender Kegiatan <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- ========================================================================= -->
<!-- TAB 2: LAPORAN PER RT -->
<!-- ========================================================================= -->
@if($activeTab === 'laporan_rt')
    <div class="card p-3 mb-3 shadow-xs">
        <form method="GET" action="{{ route('lingkungan.index') }}" class="row g-2 align-items-center">
            <input type="hidden" name="tab" value="laporan_rt">
            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari kegiatan / RT / kendala..." value="{{ request('search') }}">
                </div>
            </div>
            @if($isAdminRw)
                <div class="col-md-2 col-6">
                    <select name="rt_number" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Semua RT --</option>
                        @foreach($allRts as $rtOpt)
                            <option value="{{ $rtOpt }}" {{ request('rt_number') == $rtOpt ? 'selected' : '' }}>RT {{ $rtOpt }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="col-md-2 col-6">
                <select name="periode_bulan" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Semua Bulan --</option>
                    @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $k => $nama)
                        <option value="{{ $k }}" {{ request('periode_bulan') == $k ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 col-6">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Semua Status --</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft Disimpan</option>
                    <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu Pemeriksaan</option>
                    <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima RW</option>
                    <option value="perlu_revisi" {{ request('status') == 'perlu_revisi' ? 'selected' : '' }}>Perlu Revisi</option>
                </select>
            </div>
            <div class="col-md-3 col-6 text-end">
                <button type="submit" class="btn btn-sm btn-primary px-3 fw-bold">
                    <i class="fas fa-filter me-1"></i> Terapkan Filter
                </button>
                <a href="{{ route('lingkungan.index', ['tab' => 'laporan_rt']) }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>

    <div class="card nx-table-card overflow-hidden shadow-xs border-0 mb-4">
        <div class="card-header p-3.5 bg-white d-flex justify-content-between align-items-center border-bottom flex-wrap gap-2">
            <div>
                <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.95rem;">
                    <i class="fas fa-list-check text-success me-2"></i>Daftar Laporan Evaluasi Lingkungan Bulanan Per RT
                </h6>
                <small class="text-muted" style="font-size: 0.74rem;">Rekapitulasi berkas laporan resmi kebersihan dan drainase dari pengurus RT</small>
            </div>
            <span class="badge rounded-pill px-3 py-1.5" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-weight: 700; font-size: 0.74rem;">
                Total: {{ $laporans->total() }} Laporan
            </span>
        </div>

        <div class="table-responsive nx-table-responsive">
            <table class="table table-hover align-middle nx-table mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" style="width: 13%;">Periode</th>
                        <th style="width: 14%;">Wilayah &amp; Admin</th>
                        <th style="width: 18%;">Kondisi Kebersihan</th>
                        <th style="width: 18%;">Kondisi Drainase</th>
                        <th style="width: 22%;">Kegiatan &amp; Penghijauan</th>
                        <th style="width: 15%;">Status Laporan</th>
                        <th class="pe-4 text-end" style="width: 10%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporans as $lap)
                        <tr>
                            <td class="ps-3">
                                <strong class="d-block text-dark">{{ $lap->nama_bulan }} {{ $lap->periode_tahun }}</strong>
                                <small class="text-muted" style="font-size: 0.72rem;">
                                    {{ $lap->tanggal_pengiriman ? $lap->tanggal_pengiriman->format('d M Y, H:i') : 'Belum Dikirim' }}
                                </small>
                            </td>
                            <td>
                                <strong class="d-block text-dark">RT {{ $lap->rt_number }}</strong>
                                <small class="text-muted" style="font-size: 0.72rem;">{{ $lap->user ? $lap->user->name : '-' }}</small>
                            </td>
                            <td>
                                <span class="badge rounded-pill px-2.5 py-1 mb-1 d-inline-block" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-weight: 700; font-size: 0.72rem;">
                                    <i class="fas fa-broom me-1"></i>{{ $lap->kondisi_kebersihan }}
                                </span>
                                @if($lap->area_terjaga_kebersihan)
                                    <small class="text-muted d-block text-truncate" style="max-width: 200px; font-size: 0.7rem;">{{ $lap->area_terjaga_kebersihan }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge rounded-pill px-2.5 py-1 mb-1 d-inline-block" style="background: var(--nx-powder-soft); color: #1e4b6d; font-weight: 700; font-size: 0.72rem;">
                                    <i class="fas fa-water me-1"></i>{{ $lap->kondisi_drainase }}
                                </span>
                                @if($lap->titik_rawan_genangan)
                                    <small class="text-muted d-block text-truncate" style="max-width: 200px; font-size: 0.7rem;">{{ $lap->titik_rawan_genangan }}</small>
                                @endif
                            </td>
                            <td>
                                @if($lap->kegiatan_kerja_bakti)
                                    <div class="text-dark fw-semibold text-truncate" style="max-width: 240px; font-size: 0.78rem;">
                                        <i class="fas fa-people-roof text-success me-1"></i>{{ $lap->kegiatan_kerja_bakti }}
                                    </div>
                                @endif
                                @if($lap->kendala_lingkungan)
                                    <small class="text-danger d-block text-truncate" style="max-width: 240px; font-size: 0.7rem;">
                                        <i class="fas fa-triangle-exclamation me-1"></i>{{ $lap->kendala_lingkungan }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $lap->status === 'diterima' ? 'bg-success' : ($lap->status === 'perlu_revisi' ? 'bg-danger' : ($lap->status === 'draft' ? 'bg-warning text-dark' : 'bg-info text-dark')) }} rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                                    {{ $lap->status_label }}
                                </span>
                                @if($lap->versi > 1)
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-1.5" style="font-size: 0.65rem;">v{{ $lap->versi }}</span>
                                @endif
                                @if($lap->is_terlambat)
                                    <small class="text-danger d-block mt-0.5 fw-bold" style="font-size: 0.68rem;">Terlambat</small>
                                @endif
                            </td>
                            <td class="pe-3 text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('lingkungan.show', $lap->id) }}" class="btn btn-outline-secondary" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($lap->canBeEditedBy(Auth::user()))
                                        <a href="{{ route('lingkungan.edit', $lap->id) }}" class="btn btn-outline-warning text-dark" title="Edit / Revisi">
                                            <i class="fas fa-pen-to-square"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-leaf fa-3x mb-2 text-success opacity-50"></i>
                                <p class="mb-0">Tidak ada laporan lingkungan yang sesuai dengan filter pencarian.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($laporans->hasPages())
            <div class="card-footer bg-white p-3 border-top">
                {{ $laporans->links() }}
            </div>
        @endif
    </div>
@endif

<!-- ========================================================================= -->
<!-- TAB 3: EVALUASI & TINDAK LANJUT -->
<!-- ========================================================================= -->
@if($activeTab === 'evaluasi')
    <div class="row g-4">
        <!-- ANTREAN EVALUASI RW -->
        <div class="col-lg-7">
            <div class="card p-3.5 mb-4 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="fas fa-inbox text-warning me-2"></i>Antrean Laporan Menunggu Pemeriksaan RW
                        </h6>
                        <small class="text-muted" style="font-size: 0.74rem;">Laporan yang telah dikirimkan oleh Admin RT dan menunggu verifikasi evaluasi Ketua RW.</small>
                    </div>
                    <span class="badge bg-warning text-dark px-2.5 py-1 rounded-pill">{{ count($pendingEvaluasiList) }} Antrean</span>
                </div>

                @forelse($pendingEvaluasiList as $item)
                    <div class="p-3 mb-3 rounded-3 border bg-white shadow-xs">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <span class="badge rounded-pill bg-light text-dark border px-2 py-0.5 fw-bold" style="font-size: 0.72rem;">RT {{ $item->rt_number }}</span>
                                <strong class="ms-1 text-dark">Periode {{ $item->nama_bulan }} {{ $item->periode_tahun }}</strong>
                            </div>
                            <span class="badge bg-info text-dark rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">Menunggu Evaluasi</span>
                        </div>

                        <p class="small text-muted mb-2" style="font-size: 0.78rem;">
                            Pengirim: <strong>{{ $item->user ? $item->user->name : '-' }}</strong> · Dikirim: {{ $item->tanggal_pengiriman ? $item->tanggal_pengiriman->translatedFormat('d M Y, H:i') : '-' }}
                        </p>

                        <div class="row g-2 mb-3 small" style="font-size: 0.75rem;">
                            <div class="col-6">
                                <span class="text-muted d-block">Kebersihan:</span>
                                <strong class="text-dark">{{ $item->kondisi_kebersihan }}</strong>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block">Drainase:</span>
                                <strong class="text-dark">{{ $item->kondisi_drainase }}</strong>
                            </div>
                        </div>

                        @if($isAdminRw)
                            <!-- FORM CEPAT EVALUASI RW -->
                            <form action="{{ route('lingkungan.evaluasi', $item->id) }}" method="POST" class="p-2.5 rounded-2 border bg-light">
                                @csrf
                                <label class="form-label small fw-bold mb-1">Catatan Evaluasi RW (Transparan untuk Admin RT &amp; RW):</label>
                                <textarea name="catatan_evaluasi_rw" rows="2" class="form-control form-control-sm mb-2" placeholder="Tuliskan catatan apresiasi atau poin instruksi perbaikan untuk RT..." required></textarea>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="{{ route('lingkungan.show', $item->id) }}" class="btn btn-sm btn-outline-secondary py-1" style="font-size: 0.74rem;">
                                        <i class="fas fa-eye me-1"></i> Tinjau Detail
                                    </a>
                                    <div class="d-flex gap-2">
                                        <button type="submit" name="keputusan" value="perlu_revisi" class="btn btn-sm btn-outline-danger fw-bold py-1" style="font-size: 0.74rem;">
                                            <i class="fas fa-rotate-left me-1"></i> Minta Revisi RT
                                        </button>
                                        <button type="submit" name="keputusan" value="diterima" class="btn btn-sm btn-success fw-bold py-1 px-3" style="font-size: 0.74rem;">
                                            <i class="fas fa-check-circle me-1"></i> Terima Laporan
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <a href="{{ route('lingkungan.show', $item->id) }}" class="btn btn-sm btn-outline-secondary w-100 py-1" style="font-size: 0.75rem;">
                                <i class="fas fa-eye me-1"></i> Lihat Rincian Laporan
                            </a>
                        @endif
                    </div>
                @empty
                    <div class="py-5 text-center text-muted small">
                        <i class="fas fa-clipboard-check text-success fa-3x mb-2 d-block opacity-50"></i>
                        Semua laporan lingkungan telah dievaluasi. Tidak ada antrean menunggu pemeriksaan.
                    </div>
                @endforelse
            </div>

            <!-- LAPORAN DALAM PROSES REVISI RT -->
            @if(count($revisiList) > 0)
                <div class="card p-3.5 shadow-xs border-danger">
                    <div class="d-flex align-items-center justify-content-between mb-2.5">
                        <h6 class="fw-bold mb-0 text-danger">
                            <i class="fas fa-triangle-exclamation me-2"></i>Laporan Sedang Dalam Proses Perbaikan RT ({{ count($revisiList) }})
                        </h6>
                    </div>
                    @foreach($revisiList as $rev)
                        <div class="p-2.5 mb-2 rounded-2 border bg-white small">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong class="text-dark">RT {{ $rev->rt_number }} · Periode {{ $rev->nama_bulan }} {{ $rev->periode_tahun }}</strong>
                                <span class="badge bg-danger rounded-pill">Perlu Revisi</span>
                            </div>
                            <p class="text-muted mb-1" style="font-size: 0.75rem;">
                                <strong class="text-danger"><i class="fas fa-comment-dots me-1"></i>Catatan Evaluasi RW:</strong> "{{ $rev->catatan_evaluasi_rw ?: $rev->catatan_rw }}"
                            </p>
                            @if($isAdminRt && Auth::user()->rt_number === $rev->rt_number)
                                <a href="{{ route('lingkungan.edit', $rev->id) }}" class="btn btn-sm btn-warning text-dark fw-bold py-0.5 px-2 mt-1" style="font-size: 0.72rem;">
                                    <i class="fas fa-pen-to-square me-1"></i> Kirim Revisi Sekarang
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- RIWAYAT AKTIVITAS & EVALUASI TERKINI -->
        <div class="col-lg-5">
            <div class="card p-3.5 shadow-xs h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-clock-rotate-left text-success me-2"></i>Catatan &amp; Riwayat Tindak Lanjut
                    </h6>
                    <small class="text-muted" style="font-size: 0.72rem;">Log audit transparan</small>
                </div>

                <div class="timeline-activity">
                    @forelse($recentHistories as $h)
                        <div class="d-flex gap-2.5 mb-3 pb-2.5 border-bottom">
                            <span class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-0.5" 
                                  style="width: 28px; height: 28px; background: {{ $h->aksi === 'diterima' ? '#EBF8F2' : ($h->aksi === 'revisi_diminta' ? '#FFF5F5' : '#F0F4F8') }}; color: {{ $h->aksi === 'diterima' ? '#285943' : ($h->aksi === 'revisi_diminta' ? '#DC3545' : '#1e4b6d') }}; font-size: 0.75rem;">
                                <i class="fas {{ $h->aksi === 'diterima' ? 'fa-check' : ($h->aksi === 'revisi_diminta' ? 'fa-rotate-left' : 'fa-paper-plane') }}"></i>
                            </span>
                            <div class="flex-grow-1 small" style="font-size: 0.78rem;">
                                <div class="d-flex justify-content-between align-items-center mb-0.5">
                                    <strong class="text-dark">{{ $h->user ? $h->user->name : 'Sistem' }}</strong>
                                    <small class="text-muted" style="font-size: 0.7rem;">{{ $h->created_at->diffForHumans() }}</small>
                                </div>
                                <div class="text-muted mb-1" style="font-size: 0.73rem;">
                                    Laporan RT {{ $h->laporan ? $h->laporan->rt_number : '-' }} · Status: 
                                    <span class="fw-bold text-dark">{{ ucfirst(str_replace('_', ' ', $h->status_sesudah)) }}</span>
                                </div>
                                @if($h->catatan)
                                    <p class="p-2 rounded-2 bg-light text-dark mb-0 font-italic" style="font-size: 0.72rem; line-height: 1.35;">
                                        "{{ $h->catatan }}"
                                    </p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted small py-4">Belum ada riwayat aktivitas evaluasi tercatat.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endif

<!-- ========================================================================= -->
<!-- TAB 4: RIWAYAT LAPORAN -->
<!-- ========================================================================= -->
@if($activeTab === 'riwayat')
    <div class="card nx-table-card overflow-hidden shadow-xs border-0 mb-4">
        <div class="card-header p-3.5 bg-white d-flex justify-content-between align-items-center border-bottom flex-wrap gap-2">
            <div>
                <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.95rem;">
                    <i class="fas fa-box-archive text-success me-2"></i>Arsip Riwayat Laporan Lingkungan Antarperiode
                </h6>
                <small class="text-muted" style="font-size: 0.74rem;">Seluruh laporan resmi yang telah diterima disimpan permanen sebagai dokumentasi historis lingkungan RW 018.</small>
            </div>
            @if($isAdminRw)
                <form method="GET" action="{{ route('lingkungan.index') }}" class="d-flex gap-2">
                    <input type="hidden" name="tab" value="riwayat">
                    <select name="rt_number" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Semua RT --</option>
                        @foreach($allRts as $rtOpt)
                            <option value="{{ $rtOpt }}" {{ request('rt_number') == $rtOpt ? 'selected' : '' }}>RT {{ $rtOpt }}</option>
                        @endforeach
                    </select>
                </form>
            @endif
        </div>

        <div class="table-responsive nx-table-responsive">
            <table class="table table-hover align-middle nx-table mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" style="width: 14%;">Periode Laporan</th>
                        <th style="width: 12%;">Wilayah</th>
                        <th style="width: 18%;">Hasil Kebersihan</th>
                        <th style="width: 18%;">Hasil Drainase</th>
                        <th style="width: 24%;">Catatan Evaluasi RW</th>
                        <th class="pe-4 text-end" style="width: 14%;">Dokumentasi &amp; Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayatLaporans as $r)
                        <tr>
                            <td class="ps-3">
                                <strong class="d-block text-dark">{{ $r->nama_bulan }} {{ $r->periode_tahun }}</strong>
                                <small class="text-muted" style="font-size: 0.72rem;">Diterima: {{ $r->tanggal_pemeriksaan ? $r->tanggal_pemeriksaan->format('d M Y') : '-' }}</small>
                            </td>
                            <td>
                                <strong class="d-block text-dark">RT {{ $r->rt_number }}</strong>
                                <small class="text-muted" style="font-size: 0.72rem;">Oleh {{ $r->user ? $r->user->name : '-' }}</small>
                            </td>
                            <td>
                                <span class="badge rounded-pill px-2.5 py-0.5" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-weight: 700; font-size: 0.72rem;">
                                    {{ $r->kondisi_kebersihan }}
                                </span>
                            </td>
                            <td>
                                <span class="badge rounded-pill px-2.5 py-0.5" style="background: var(--nx-powder-soft); color: #1e4b6d; font-weight: 700; font-size: 0.72rem;">
                                    {{ $r->kondisi_drainase }}
                                </span>
                            </td>
                            <td>
                                <p class="text-muted small mb-0 text-truncate" style="max-width: 280px; font-size: 0.74rem;">
                                    {{ $r->catatan_evaluasi_rw ?: $r->catatan_rw ?: 'Disetujui tanpa catatan khusus.' }}
                                </p>
                            </td>
                            <td class="pe-3 text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('lingkungan.show', $r->id) }}" class="btn btn-outline-secondary" title="Buka Detail">
                                        <i class="fas fa-eye me-1"></i> Detail
                                    </a>
                                    <a href="{{ route('lingkungan.cetak', $r->id) }}" target="_blank" class="btn btn-outline-secondary" title="Cetak Berita Acara">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <p class="mb-0">Belum ada arsip riwayat laporan tersimpan untuk filter yang dipilih.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($riwayatLaporans->hasPages())
            <div class="card-footer bg-white p-3 border-top">
                {{ $riwayatLaporans->links() }}
            </div>
        @endif
    </div>
@endif

<!-- MODAL PENGATURAN BATAS WAKTU PERIODE (ADMIN RW) -->
@if($isAdminRw)
<div class="modal fade" id="modalPengaturanPeriode" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('lingkungan.update_periode') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="font-size: 1rem; color: var(--nx-forest);">
                        <i class="fas fa-clock-rotate-left me-2"></i>Pengaturan Batas Waktu &amp; Instruksi Periode Laporan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Bulan Laporan</label>
                            <select name="periode_bulan" class="form-select" required>
                                @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $k => $nama)
                                    <option value="{{ $k }}" {{ $selectedBulan == $k ? 'selected' : '' }}>{{ $nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Tahun</label>
                            <input type="text" name="periode_tahun" class="form-control" value="{{ $selectedTahun }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Tanggal Batas Waktu (Deadline Submission RT)</label>
                        <input type="date" name="batas_waktu" class="form-control" value="{{ $periodeSetting->batas_waktu->format('Y-m-d') }}" required>
                        <small class="text-muted" style="font-size: 0.72rem;">Setelah tanggal ini, laporan yang belum dikirim akan ditandai status "Terlambat".</small>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Instruksi / Catatan Khusus RW untuk Seluruh RT</label>
                        <textarea name="catatan_instruksi_rw" rows="3" class="form-control" placeholder="Contoh: Fokus utama bulan ini adalah pengerukan endapan selokan menjelang puncak musim hujan...">{{ $periodeSetting->catatan_instruksi_rw }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-3 fw-bold">Simpan Pengaturan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- CHART JS SCRIPTS -->
@if($activeTab === 'ringkasan')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const months = @json($chartKebersihanData['labels']);

    // 1. Chart Kebersihan Bulanan
    const ctxKebersihan = document.getElementById('chartKebersihan');
    if (ctxKebersihan) {
        new Chart(ctxKebersihan.getContext('2d'), {
            type: 'bar',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Sangat Bersih',
                        data: @json($chartKebersihanData['sangat_bersih']),
                        backgroundColor: '#285943',
                        borderRadius: 4
                    },
                    {
                        label: 'Cukup Bersih',
                        data: @json($chartKebersihanData['cukup_bersih']),
                        backgroundColor: '#A8C9B5',
                        borderRadius: 4
                    },
                    {
                        label: 'Perlu Kerja Bakti',
                        data: @json($chartKebersihanData['perlu_kerja_bakti']),
                        backgroundColor: '#E05353',
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { stacked: true },
                    y: { stacked: true, beginAtZero: true, max: 5 }
                },
                plugins: { legend: { display: false } }
            }
        });
    }

    // 2. Chart Drainase Bulanan
    const ctxDrainase = document.getElementById('chartDrainase');
    if (ctxDrainase) {
        new Chart(ctxDrainase.getContext('2d'), {
            type: 'bar',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Lancar',
                        data: @json($chartDrainaseData['lancar']),
                        backgroundColor: '#1F567A',
                        borderRadius: 4
                    },
                    {
                        label: 'Sedimen',
                        data: @json($chartDrainaseData['sedimen']),
                        backgroundColor: '#E2A03F',
                        borderRadius: 4
                    },
                    {
                        label: 'Tersumbat',
                        data: @json($chartDrainaseData['tersumbat']),
                        backgroundColor: '#DC3545',
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { stacked: true },
                    y: { stacked: true, beginAtZero: true, max: 5 }
                },
                plugins: { legend: { display: false } }
            }
        });
    }

    // 3. Chart Perbandingan Antar-RT
    const ctxPerbandingan = document.getElementById('chartPerbandinganRt');
    if (ctxPerbandingan) {
        new Chart(ctxPerbandingan.getContext('2d'), {
            type: 'bar',
            data: {
                labels: @json($chartPerbandinganRt['labels']),
                datasets: [
                    {
                        label: 'Indeks Kebersihan',
                        data: @json($chartPerbandinganRt['kebersihan_score']),
                        backgroundColor: '#285943',
                        borderRadius: 6
                    },
                    {
                        label: 'Indeks Kelancaran Drainase',
                        data: @json($chartPerbandinganRt['drainase_score']),
                        backgroundColor: '#A9C9E8',
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, max: 100 }
                },
                plugins: {
                    legend: { position: 'top' }
                }
            }
        });
    }

    // 4. Chart Laporan Bulanan
    const ctxLaporan = document.getElementById('chartLaporanBulanan');
    if (ctxLaporan) {
        new Chart(ctxLaporan.getContext('2d'), {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Laporan Diterima',
                        data: @json($chartLaporanBulanan['diterima']),
                        borderColor: '#285943',
                        backgroundColor: 'rgba(40, 89, 67, 0.1)',
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Total Terkirim',
                        data: @json($chartLaporanBulanan['total']),
                        borderColor: '#A8C9B5',
                        borderDash: [5, 5],
                        fill: false,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, max: 6 }
                },
                plugins: {
                    legend: { position: 'top' }
                }
            }
        });
    }
});
</script>
@endif
@endsection
