@extends('layouts.app')

@section('title', 'Peminjaman Fasilitas Umum — NexaNest')

@section('content')
<div class="nx-page-header">
    <div>
        <span class="badge rounded-pill mb-2 px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
            <i class="fas fa-building-circle-check me-1"></i> Sarana &amp; Prasarana Bersama · RW 018
        </span>
        <h1 class="nx-page-title">Peminjaman Fasilitas Umum RW 018</h1>
        <p class="nx-page-subtitle">
            Pusat reservasi sarana bersama (Balai Pertemuan, Lapangan Olahraga, Inventaris Acara) dengan pengecekan bentrok jadwal otomatis dan pemantauan kondisi fasilitas.
        </p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <button type="button" class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#modalBookingFasilitas" style="border-radius: 12px; background: var(--nx-forest); border-color: var(--nx-forest);">
            <i class="fas fa-calendar-plus me-1"></i> Ajukan Peminjaman
        </button>
        @if($isAdminRw)
            <button type="button" class="btn btn-outline-success fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahFasilitas" style="border-radius: 12px;">
                <i class="fas fa-plus me-1"></i> Tambah Fasilitas Baru
            </button>
        @endif
    </div>
</div>

<!-- 6 KARTU STATISTIK RINGKASAN (STANDAR NEXANEST) -->
<div class="row g-3 mb-4">
    <!-- 1. Total Fasilitas -->
    <div class="col-lg-2 col-md-4 col-6">
        <x-stat-card 
            title="Total Fasilitas" 
            value="{{ $totalFasilitas }}" 
            sub="Sarana terdaftar" 
            icon="fas fa-building" 
            theme="forest" 
        />
    </div>
    <!-- 2. Fasilitas Tersedia -->
    <div class="col-lg-2 col-md-4 col-6">
        <x-stat-card 
            title="Fasilitas Siap" 
            value="{{ $totalTersedia }}" 
            sub="Operasional normal" 
            icon="fas fa-circle-check" 
            theme="success" 
            valColor="#059669" 
        />
    </div>
    <!-- 3. Pengajuan Menunggu -->
    <div class="col-lg-2 col-md-4 col-6">
        <x-stat-card 
            title="Menunggu Review" 
            value="{{ $totalPengajuanMenunggu }}" 
            sub="Perlu ditinjau" 
            icon="fas fa-clock" 
            theme="warning" 
            valColor="#D97706" 
        />
    </div>
    <!-- 4. Sedang Digunakan -->
    <div class="col-lg-2 col-md-4 col-6">
        <x-stat-card 
            title="Sedang Digunakan" 
            value="{{ $totalSedangDigunakan }}" 
            sub="Aktivitas aktif" 
            icon="fas fa-calendar-check" 
            theme="primary" 
            valColor="#1F567A" 
        />
    </div>
    <!-- 5. Peminjaman Selesai -->
    <div class="col-lg-2 col-md-4 col-6">
        <x-stat-card 
            title="Riwayat Selesai" 
            value="{{ $totalPeminjamanSelesai }}" 
            sub="Tuntas diperiksa" 
            icon="fas fa-check-double" 
            theme="forest" 
            valColor="var(--nx-forest)" 
        />
    </div>
    <!-- 6. Dalam Pemeliharaan -->
    <div class="col-lg-2 col-md-4 col-6">
        <x-stat-card 
            title="Pemeliharaan" 
            value="{{ $totalDalamPemeliharaan }}" 
            sub="Perbaikan / servis" 
            icon="fas fa-wrench" 
            theme="danger" 
            valColor="var(--nx-danger)" 
        />
    </div>
</div>

<!-- ========================================================================= -->
<!-- NAV TABS 4 BAGIAN UTAMA -->
<!-- ========================================================================= -->
<ul class="nav nav-pills mb-4 gap-2 border-bottom pb-2" id="fasilitasTabNav">
    <li class="nav-item">
        <a class="nav-link px-3.5 py-2 fw-bold {{ $activeTab === 'katalog' ? 'active shadow-xs' : 'text-secondary' }}" 
           href="{{ route('fasilitas.index', ['tab' => 'katalog']) }}"
           style="border-radius: 10px; font-size: 0.88rem; {{ $activeTab === 'katalog' ? 'background: var(--nx-forest); color: #fff;' : 'background: #fff;' }}">
            <i class="fas fa-layer-group me-1.5"></i> 1. Katalog Fasilitas
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link px-3.5 py-2 fw-bold {{ $activeTab === 'kalender' ? 'active shadow-xs' : 'text-secondary' }}" 
           href="{{ route('fasilitas.index', ['tab' => 'kalender', 'fasilitas_id' => $selectedFasilitasId]) }}"
           style="border-radius: 10px; font-size: 0.88rem; {{ $activeTab === 'kalender' ? 'background: var(--nx-forest); color: #fff;' : 'background: #fff;' }}">
            <i class="fas fa-calendar-days me-1.5"></i> 2. Kalender Ketersediaan
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link px-3.5 py-2 fw-bold {{ $activeTab === 'riwayat' ? 'active shadow-xs' : 'text-secondary' }}" 
           href="{{ route('fasilitas.index', ['tab' => 'riwayat', 'rt_number' => $selectedRt]) }}"
           style="border-radius: 10px; font-size: 0.88rem; {{ $activeTab === 'riwayat' ? 'background: var(--nx-forest); color: #fff;' : 'background: #fff;' }}">
            <i class="fas fa-clipboard-list me-1.5"></i> 3. Pengajuan &amp; Riwayat Peminjaman
            @if($totalPengajuanMenunggu > 0)
                <span class="badge rounded-pill bg-warning text-dark ms-1.5 px-2 py-0.5" style="font-size: 0.65rem;">{{ $totalPengajuanMenunggu }} Menunggu</span>
            @endif
        </a>
    </li>
    @if(!$isWarga)
    <li class="nav-item">
        <a class="nav-link px-3.5 py-2 fw-bold {{ $activeTab === 'laporan' ? 'active shadow-xs' : 'text-secondary' }}" 
           href="{{ route('fasilitas.index', ['tab' => 'laporan']) }}"
           style="border-radius: 10px; font-size: 0.88rem; {{ $activeTab === 'laporan' ? 'background: var(--nx-forest); color: #fff;' : 'background: #fff;' }}">
            <i class="fas fa-chart-pie me-1.5"></i> 4. Laporan &amp; Statistik Penggunaan
        </a>
    </li>
    @endif
</ul>

<!-- ========================================================================= -->
<!-- TAB 1: KATALOG FASILITAS UMUM -->
<!-- ========================================================================= -->
@if($activeTab === 'katalog')
    <!-- FILTER BAR KATALOG -->
    <div class="card p-3 mb-4 shadow-xs border-0" style="border-radius: 12px; background: #fff;">
        <form method="GET" action="{{ route('fasilitas.index') }}" class="row g-2 align-items-center">
            <input type="hidden" name="tab" value="katalog">
            <div class="col-md-4 col-12">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama fasilitas, lokasi..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3 col-6">
                <select name="kategori" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Semua Kategori Fasilitas --</option>
                    @foreach($kategoriList as $kOpt)
                        <option value="{{ $kOpt }}" {{ $selectedKategori === $kOpt ? 'selected' : '' }}>{{ $kOpt }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 col-6">
                <select name="kondisi" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Kondisi Fisik --</option>
                    <option value="Baik" {{ $selectedKondisi === 'Baik' ? 'selected' : '' }}>Kondisi Baik</option>
                    <option value="Perlu Perawatan" {{ $selectedKondisi === 'Perlu Perawatan' ? 'selected' : '' }}>Perlu Perawatan</option>
                    <option value="Tidak Tersedia" {{ $selectedKondisi === 'Tidak Tersedia' ? 'selected' : '' }}>Tidak Tersedia</option>
                </select>
            </div>
            <div class="col-md-3 col-12 text-end d-flex justify-content-end gap-1">
                <button type="submit" class="btn btn-sm btn-primary fw-bold px-3">Filter</button>
                <a href="{{ route('fasilitas.index', ['tab' => 'katalog']) }}" class="btn btn-sm btn-light border text-muted px-2" title="Reset"><i class="fas fa-rotate-left"></i></a>
            </div>
        </form>
    </div>

    <!-- GRID KATALOG FASILITAS (STANDAR NEXANEST) -->
    <div class="row g-4 mb-4">
        @forelse($fasilitas as $f)
            <div class="col-lg-4 col-md-6">
                <div class="card nx-grid-card shadow-xs">
                    <div class="nx-grid-card-body">
                        <!-- Header Badges -->
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge rounded-pill px-2.5 py-1" style="background: rgba(40,89,67,0.1); color: var(--nx-forest); font-weight: 700; font-size: 0.72rem;">
                                {{ $f->kategori }}
                            </span>
                            {!! $f->status_operasional_badge !!}
                        </div>

                        <!-- Nama & Lokasi -->
                        <h5 class="fw-bold text-dark mb-1" style="font-size: 1.02rem; line-height: 1.35;">{{ $f->nama }}</h5>
                        <p class="small text-muted mb-2" style="font-size: 0.78rem;">
                            <i class="fas fa-map-pin text-danger me-1"></i>{{ $f->lokasi }}
                        </p>

                        <!-- Deskripsi Singkat -->
                        <p class="text-dark small mb-3" style="font-size: 0.8rem; line-height: 1.4; min-height: 40px; color: #475569;">
                            {{ Str::limit($f->deskripsi, 110) }}
                        </p>

                        <!-- Box Spesifikasi Ringkas -->
                        <div class="p-2.5 rounded-3 bg-light mb-3" style="font-size: 0.75rem; line-height: 1.7;">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted"><i class="fas fa-users me-1"></i>Kapasitas:</span>
                                <strong class="text-dark">{{ $f->kapasitas > 0 ? $f->kapasitas . ' Orang' : 'Fleksibel' }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted"><i class="fas fa-clock me-1"></i>Jam Buka:</span>
                                <span class="text-dark">{{ $f->jam_operasional ?: '08:00 - 22:00 WIB' }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted"><i class="fas fa-tag me-1"></i>Biaya Peminjaman:</span>
                                <strong class="{{ $f->biaya_sewa > 0 ? 'text-primary' : 'text-success' }}">
                                    {{ $f->biaya_sewa > 0 ? 'Rp ' . number_format($f->biaya_sewa, 0, ',', '.') : 'Gratis Warga RW' }}
                                </strong>
                            </div>
                            <div class="d-flex justify-content-between pt-1 border-top mt-1">
                                <span class="text-muted">Kondisi Fisik:</span>
                                {!! $f->kondisi_fisik_badge !!}
                            </div>
                        </div>
                    </div>

                    <!-- Footer Actions (Aligned Baseline) -->
                    <div class="nx-grid-card-footer">
                        <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2.5 btn-detail-fasilitas" data-id="{{ $f->id }}" style="font-size: 0.76rem; border-radius: 8px;">
                            <i class="fas fa-eye me-1"></i> Detail &amp; Aturan
                        </button>
                        <div class="d-flex gap-1.5 align-items-center">
                            @if($f->status_operasional === 'tersedia')
                                <button type="button" class="btn btn-sm btn-primary py-1 px-3 fw-bold btn-book-this-fasilitas" 
                                        data-id="{{ $f->id }}" data-nama="{{ $f->nama }}" style="font-size: 0.76rem; border-radius: 8px; background: var(--nx-forest); border-color: var(--nx-forest);">
                                    <i class="fas fa-calendar-check me-1"></i> Ajukan
                                </button>
                            @else
                                <button type="button" class="btn btn-sm btn-secondary py-1 px-2 text-white" disabled style="font-size: 0.75rem; border-radius: 8px;">
                                    Tidak Tersedia
                                </button>
                            @endif

                            @if($isAdminRw)
                                <div class="dropdown">
                                    <button class="nx-action-btn" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-vertical text-secondary"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="font-size: 0.8rem;">
                                        <li>
                                            <a class="dropdown-item btn-edit-fasilitas" href="javascript:void(0)" data-id="{{ $f->id }}">
                                                <i class="fas fa-pen-to-square text-secondary me-2"></i> Edit Data Fasilitas
                                            </a>
                                        </li>
                                        <li>
                                            <form action="{{ route('fasilitas.toggle_status', $f->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="dropdown-item {{ $f->status_operasional === 'pemeliharaan' ? 'text-success' : 'text-warning' }}">
                                                    <i class="fas fa-wrench me-2"></i> {{ $f->status_operasional === 'pemeliharaan' ? 'Tandai Siap Pakai' : 'Set Dalam Pemeliharaan' }}
                                                </button>
                                            </form>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('fasilitas.destroy_fasilitas', $f->id) }}" method="POST" onsubmit="return confirm('Hapus fasilitas ini secara permanen?')" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fas fa-trash-can me-2"></i> Hapus Fasilitas
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <x-empty-state
                    icon="fas fa-house-circle-xmark"
                    title="Tidak Ditemukan Fasilitas"
                    message="Tidak ada fasilitas umum yang sesuai dengan kata kunci atau filter kondisi yang dipilih. Silakan atur ulang pencarian."
                />
            </div>
        @endforelse
    </div>
@endif

<!-- ========================================================================= -->
<!-- TAB 2: KALENDER KETERSEDIAAN FASILITAS -->
<!-- ========================================================================= -->
@if($activeTab === 'kalender')
    <!-- FILTER BAR KALENDER TERPADU (FASILITAS, RT, STATUS) -->
    <div class="card p-3.5 mb-4 shadow-xs border-0" style="border-radius: 14px; background: #fff;">
        <form method="GET" action="{{ route('fasilitas.index') }}" class="row g-2.5 align-items-end">
            <input type="hidden" name="tab" value="kalender">

            <!-- Filter Fasilitas -->
            <div class="col-lg-4 col-md-6 col-12">
                <label class="form-label small fw-bold text-muted mb-1">
                    <i class="fas fa-building me-1 text-success"></i>Pilih Fasilitas:
                </label>
                <select name="kalender_fasilitas_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Seluruh Fasilitas RW 018 --</option>
                    @foreach($fasilitas as $fOpt)
                        <option value="{{ $fOpt->id }}" {{ $calendarFasilitasId == $fOpt->id ? 'selected' : '' }}>
                            {{ $fOpt->nama }} ({{ $fOpt->kategori }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter RT -->
            @if($isAdminRw)
                <div class="col-lg-3 col-md-3 col-6">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="fas fa-map-pin me-1 text-primary"></i>Wilayah RT:
                    </label>
                    <select name="kalender_rt" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Seluruh RT --</option>
                        @foreach($allRts as $rtOpt)
                            <option value="{{ $rtOpt }}" {{ $calendarRt == $rtOpt ? 'selected' : '' }}>RT {{ $rtOpt }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <!-- Filter Status -->
            <div class="col-lg-3 col-md-3 col-6">
                <label class="form-label small fw-bold text-muted mb-1">
                    <i class="fas fa-filter me-1 text-warning"></i>Status Peminjaman:
                </label>
                <select name="kalender_status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Semua Status Aktif --</option>
                    <option value="disetujui" {{ $calendarStatus === 'disetujui' ? 'selected' : '' }}>Disetujui (Terkonfirmasi)</option>
                    <option value="digunakan" {{ $calendarStatus === 'digunakan' ? 'selected' : '' }}>Sedang Digunakan</option>
                    <option value="menunggu" {{ $calendarStatus === 'menunggu' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                    <option value="selesai" {{ $calendarStatus === 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>

            <!-- Aksi & Reset -->
            <div class="col-lg-2 col-md-12 col-12 d-flex gap-1.5 justify-content-end">
                <button type="submit" class="btn btn-sm btn-primary fw-bold w-100" style="border-radius: 10px;">
                    <i class="fas fa-check me-1"></i> Terapkan
                </button>
                <a href="{{ route('fasilitas.index', ['tab' => 'kalender']) }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter" style="border-radius: 10px;">
                    <i class="fas fa-rotate-left"></i>
                </a>
            </div>
        </form>

        <!-- Keterangan Status / Legend -->
        <div class="d-flex flex-wrap align-items-center gap-3 mt-3 pt-2.5 border-top" style="font-size: 0.76rem;">
            <span class="text-muted fw-bold text-uppercase" style="letter-spacing: 0.04em;">Indikator Jadwal:</span>
            <span class="d-inline-flex align-items-center gap-1.5">
                <span class="rounded-circle" style="width: 10px; height: 10px; background: #1F567A;"></span>
                <span class="fw-semibold text-dark">Disetujui (Terkonfirmasi)</span>
            </span>
            <span class="d-inline-flex align-items-center gap-1.5">
                <span class="rounded-circle" style="width: 10px; height: 10px; background: #285943;"></span>
                <span class="fw-semibold text-dark">Sedang Digunakan</span>
            </span>
            <span class="d-inline-flex align-items-center gap-1.5">
                <span class="rounded-circle" style="width: 10px; height: 10px; background: #D97706;"></span>
                <span class="fw-semibold text-dark">Menunggu Persetujuan (Tentatif)</span>
            </span>
            <span class="d-inline-flex align-items-center gap-1.5">
                <span class="rounded-circle" style="width: 10px; height: 10px; background: #5A6E62;"></span>
                <span class="fw-semibold text-dark">Selesai</span>
            </span>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- KALENDER BULANAN INTERAKTIF -->
        <div class="col-lg-8">
            <div class="card p-4 shadow-xs border-0 h-100" style="border-radius: 16px; background: #fff;">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 1rem;">
                            <i class="fas fa-calendar-alt text-success me-2"></i>Kalender Jadwal Reservasi Fasilitas
                        </h6>
                        <small class="text-muted" style="font-size: 0.76rem;">Gunakan navigasi Bulan / Minggu / Daftar. Klik agenda untuk rincian peminjaman.</small>
                    </div>
                    <span class="badge rounded-pill px-3 py-1.5" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-weight: 700; font-size: 0.74rem;">
                        {{ count($calendarEventsJson) }} Jadwal Terjadwal
                    </span>
                </div>
                <div id="facilityCalendarWidget" style="min-height: 540px;"></div>
            </div>
        </div>

        <!-- JADWAL 14 HARI KE DEPAN (PANEL AGENDA TERDEKAT) -->
        <div class="col-lg-4">
            <div class="card p-3.5 shadow-xs border-0 h-100 d-flex flex-column" style="border-radius: 16px; background: #fff;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.95rem;">
                        <i class="fas fa-clock-rotate-left text-primary me-2"></i>Agenda Mendatang Terdekat
                    </h6>
                    <span class="badge rounded-pill" style="background: var(--nx-powder-soft); color: #1E40AF; font-size: 0.7rem;">
                        {{ $upcomingSchedules->count() }} Terdekat
                    </span>
                </div>
                <small class="text-muted d-block mb-3" style="font-size: 0.74rem;">Jadwal pemakaian sarana yang akan datang</small>

                <div class="d-flex flex-column gap-2.5 flex-grow-1" style="max-height: 520px; overflow-y: auto; padding-right: 4px;">
                    @forelse($upcomingSchedules as $us)
                        <div class="card p-3 border shadow-none bg-white schedule-card cursor-pointer"
                             style="border-radius: 12px; transition: all 0.2s ease; border-left: 3px solid {{ $us->status === 'disetujui' ? '#1F567A' : ($us->status === 'digunakan' ? 'var(--nx-forest)' : '#D97706') }} !important;"
                             onclick="openDetailBookingById({{ $us->id }})"
                             title="Klik untuk melihat rincian peminjaman">
                            <div class="d-flex justify-content-between align-items-center mb-1.5">
                                <span class="badge bg-light text-dark border px-2 py-0.5 font-monospace fw-bold" style="font-size: 0.68rem;">
                                    RT {{ $us->rt_number }}
                                </span>
                                {!! $us->status_badge !!}
                            </div>
                            <div class="fw-bold text-dark mb-1" style="font-size: 0.86rem; line-height: 1.25;">
                                {{ $us->fasilitas ? $us->fasilitas->nama : '-' }}
                            </div>
                            <div class="text-muted mb-1" style="font-size: 0.74rem;">
                                <i class="fas fa-calendar-day me-1 text-success"></i>
                                <strong>{{ $us->waktu_mulai->translatedFormat('d M Y') }}</strong> · {{ $us->waktu_mulai->format('H:i') }} - {{ $us->waktu_selesai->format('H:i') }} WIB
                            </div>
                            <div class="text-dark small text-truncate mb-1.5" style="max-width: 250px;" title="{{ $us->tujuan }}">
                                <i class="fas fa-bullseye text-muted me-1"></i>{{ $us->tujuan }}
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-1.5 border-top" style="font-size: 0.72rem;">
                                <span class="text-muted">
                                    <i class="fas fa-user me-1"></i>{{ $us->nama_peminjam }}
                                </span>
                                <span class="text-muted">
                                    <i class="fas fa-users me-1"></i>{{ $us->jumlah_peserta }} org
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted small my-auto">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 48px; height: 48px; background: var(--nx-mint-soft); color: var(--nx-forest);">
                                <i class="fas fa-calendar-check fa-lg"></i>
                            </div>
                            <div class="fw-bold text-dark mb-1">Tidak Ada Jadwal Mendatang</div>
                            <div class="text-muted">Seluruh sarana dan fasilitas siap dipesan oleh warga.</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endif

<!-- ========================================================================= -->
<!-- TAB 3: DAFTAR & RIWAYAT PENGAJUAN PEMINJAMAN -->
<!-- ========================================================================= -->
@if($activeTab === 'riwayat')
    <!-- FILTER BAR RIWAYAT -->
    <div class="card p-3 mb-4 shadow-xs border-0" style="border-radius: 12px; background: #fff;">
        <form method="GET" action="{{ route('fasilitas.index') }}" class="row g-2 align-items-center">
            <input type="hidden" name="tab" value="riwayat">

            <!-- Search -->
            <div class="col-md-3 col-12">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nomor, peminjam, tujuan..." value="{{ request('search') }}">
                </div>
            </div>

            <!-- Fasilitas Filter -->
            <div class="col-md-3 col-6">
                <select name="fasilitas_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Semua Fasilitas --</option>
                    @foreach($fasilitas as $fOpt)
                        <option value="{{ $fOpt->id }}" {{ $selectedFasilitasId == $fOpt->id ? 'selected' : '' }}>{{ $fOpt->nama }}</option>
                    @endforeach
                </select>
            </div>

            <!-- RT Filter (Admin RW) -->
            @if($isAdminRw)
                <div class="col-md-2 col-6">
                    <select name="rt_number" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Seluruh RT (RW 018) --</option>
                        @foreach($allRts as $rtOpt)
                            <option value="{{ $rtOpt }}" {{ $selectedRt == $rtOpt ? 'selected' : '' }}>RT {{ $rtOpt }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <!-- Status Filter -->
            <div class="col-md-2 col-6">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Semua Status --</option>
                    <option value="menunggu" {{ request('status') === 'menunggu' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                    <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="digunakan" {{ request('status') === 'digunakan' ? 'selected' : '' }}>Sedang Digunakan</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    <option value="dibatalkan" {{ request('status') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            <div class="col-md-2 col-12 text-end d-flex gap-1 justify-content-end">
                <button type="submit" class="btn btn-sm btn-primary fw-bold px-3">Filter</button>
                <a href="{{ route('fasilitas.index', ['tab' => 'riwayat']) }}" class="btn btn-sm btn-light border text-muted px-2" title="Reset"><i class="fas fa-rotate-left"></i></a>
            </div>
        </form>
    </div>

    <!-- TABEL DAFTAR PENGAJUAN (STANDAR TABEL NEXANEST) -->
    <div class="card nx-table-card overflow-hidden shadow-xs border-0 mb-4">
        <div class="card-header bg-white p-3.5 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-0 text-dark" style="font-size: 0.96rem;">
                    <i class="fas fa-file-contract text-success me-2"></i>Daftar Pengajuan &amp; Riwayat Peminjaman Fasilitas
                </h5>
                <small class="text-muted" style="font-size: 0.75rem;">Seluruh permohonan reservasi sarana bersama lingkungan RW 018</small>
            </div>
            <span class="badge rounded-pill px-3 py-1.5" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-weight: 700; font-size: 0.74rem;">
                {{ $peminjamans->total() }} Data Tercatat
            </span>
        </div>
        <div class="table-responsive nx-table-responsive">
            <table class="table table-hover align-middle nx-table mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" style="width: 14%;">No. Pengajuan</th>
                        <th style="width: 16%;">Nama Peminjam &amp; RT</th>
                        <th style="width: 20%;">Fasilitas Dipinjam</th>
                        <th style="width: 18%;">Waktu Penggunaan</th>
                        <th style="width: 17%;">Tujuan Kegiatan</th>
                        <th style="width: 12%;">Status</th>
                        <th class="pe-4 text-center" style="width: 7%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($peminjamans as $p)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark font-monospace" style="font-size: 0.82rem;">
                                    {{ $p->nomor_pengajuan ?: 'PINJAM-00' . $p->id }}
                                </div>
                                <small class="text-muted" style="font-size: 0.7rem;">
                                    Dibuat: {{ $p->created_at->translatedFormat('d M Y') }}
                                </small>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $p->nama_peminjam }}</div>
                                <small class="text-muted">
                                    <span class="badge bg-light text-dark border px-1.5 py-0">RT {{ $p->rt_number }}</span>
                                    @if($p->nomor_kontak)
                                        · {{ $p->nomor_kontak }}
                                    @endif
                                </small>
                            </td>
                            <td>
                                <div class="fw-bold text-dark" style="font-size: 0.84rem;">{{ $p->fasilitas ? $p->fasilitas->nama : '-' }}</div>
                                <small class="text-muted d-block" style="font-size: 0.72rem;">
                                    {{ $p->fasilitas ? $p->fasilitas->kategori : '-' }}
                                </small>
                            </td>
                            <td>
                                <div class="text-dark fw-semibold" style="font-size: 0.82rem;">
                                    {{ $p->waktu_mulai->translatedFormat('d M Y') }}
                                </div>
                                <small class="text-muted" style="font-size: 0.72rem;">
                                    <i class="fas fa-clock me-1 text-primary"></i>{{ $p->waktu_mulai->format('H:i') }} - {{ $p->waktu_selesai->format('H:i') }} WIB
                                </small>
                            </td>
                            <td>
                                <div class="text-dark small text-truncate" style="max-width: 180px;" title="{{ $p->tujuan }}">
                                    {{ $p->tujuan }}
                                </div>
                                <small class="text-muted" style="font-size: 0.7rem;">
                                    <i class="fas fa-users me-1"></i>{{ $p->jumlah_peserta }} Peserta
                                </small>
                            </td>
                            <td>
                                {!! $p->status_badge !!}
                                @if($p->pemeriksaan && $p->pemeriksaan->ada_kerusakan)
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-1.5 py-0 mt-0.5 d-inline-block" style="font-size: 0.65rem;">
                                        Ada Kerusakan
                                    </span>
                                @endif
                            </td>
                            <td class="pe-4 text-center">
                                <div class="dropdown">
                                    <button class="nx-action-btn" type="button" data-bs-toggle="dropdown" title="Menu Aksi">
                                        <i class="fas fa-ellipsis-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="font-size: 0.82rem; border-radius: 12px;">
                                        <li>
                                            <a class="dropdown-item btn-detail-booking" href="javascript:void(0)" data-id="{{ $p->id }}">
                                                <i class="fas fa-eye text-primary me-2"></i> Lihat Detail Peminjaman
                                            </a>
                                        </li>
                                        @if(!$isWarga)
                                            <li>
                                                <a class="dropdown-item btn-review-booking" href="javascript:void(0)" data-id="{{ $p->id }}" data-status="{{ $p->status }}" data-catatan="{{ $p->catatan_pengelola }}">
                                                    <i class="fas fa-clipboard-check text-success me-2"></i> Review / Update Status
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item btn-inspeksi-booking" href="javascript:void(0)" data-id="{{ $p->id }}">
                                                    <i class="fas fa-list-check text-warning me-2"></i> Pemeriksaan Fisik &amp; Alat
                                                </a>
                                            </li>
                                        @endif
                                        @if($isWarga && $p->status === 'menunggu')
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('fasilitas.batal', $p->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan permohonan peminjaman ini?')" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fas fa-ban me-2"></i> Batalkan Pengajuan
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-0 border-0">
                                <x-empty-state
                                    icon="fas fa-calendar-xmark"
                                    title="Belum Ada Riwayat Peminjaman"
                                    message="Tidak ditemukan data peminjaman untuk kriteria pencarian dan filter yang dipilih. Silakan atur ulang filter atau buat pengajuan baru."
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($peminjamans->hasPages())
            <div class="card-footer bg-white p-3.5 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span class="small text-muted">Menampilkan {{ $peminjamans->firstItem() }} - {{ $peminjamans->lastItem() }} dari {{ $peminjamans->total() }} data</span>
                {{ $peminjamans->links() }}
            </div>
        @endif
    </div>
@endif

<!-- ========================================================================= -->
<!-- TAB 4: LAPORAN & STATISTIK PENGGUNAAN -->
<!-- ========================================================================= -->
@if($activeTab === 'laporan' && !$isWarga)
    <div class="card p-3 mb-4 shadow-xs border-0" style="border-radius: 12px; background: #fff;">
        <form method="GET" action="{{ route('fasilitas.index') }}" class="row g-2 align-items-center">
            <input type="hidden" name="tab" value="laporan">
            <div class="col-md-3 col-6">
                <label class="form-label small fw-bold mb-1">Tahun Evaluasi:</label>
                <select name="tahun" class="form-select form-select-sm" onchange="this.form.submit()">
                    @foreach(['2025', '2026', '2027'] as $y)
                        <option value="{{ $y }}" {{ $selectedTahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-9 col-6 text-end pt-3">
                <a href="{{ route('fasilitas.index', ['tab' => 'laporan']) }}" class="btn btn-sm btn-light border text-muted px-3">
                    <i class="fas fa-rotate-left me-1"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <div class="row g-3 mb-4">
        <!-- Grafik 1: Tren Peminjaman Bulanan -->
        <div class="col-lg-8">
            <div class="card p-4 shadow-xs border-0 h-100" style="border-radius: 14px; background: #fff;">
                <h6 class="fw-bold mb-1 text-dark">
                    <i class="fas fa-chart-line text-success me-2"></i>Tren Frekuensi Peminjaman Fasilitas Tahun {{ $selectedTahun }}
                </h6>
                <small class="text-muted d-block mb-3" style="font-size: 0.74rem;">Total pengajuan masuk vs agenda disetujui/selesai</small>
                <div style="height: 250px;">
                    <canvas id="chartTrenPeminjaman"></canvas>
                </div>
            </div>
        </div>

        <!-- Grafik 2: Fasilitas Terpopuler -->
        <div class="col-lg-4">
            <div class="card p-4 shadow-xs border-0 h-100" style="border-radius: 14px; background: #fff;">
                <h6 class="fw-bold mb-1 text-dark">
                    <i class="fas fa-ranking-star text-primary me-2"></i>Fasilitas Paling Sering Digunakan
                </h6>
                <small class="text-muted d-block mb-3" style="font-size: 0.74rem;">Berdasarkan total peminjaman sukses</small>
                <div style="height: 220px;">
                    <canvas id="chartTopFasilitas"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- REKAP PENGGUNAAN PER RT & RIWAYAT KERUSAKAN -->
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card p-4 shadow-xs border-0 h-100" style="border-radius: 14px; background: #fff;">
                <h6 class="fw-bold mb-1 text-dark">
                    <i class="fas fa-users-viewfinder text-info me-2"></i>Sebaran Peminjaman Berdasarkan Asal RT Warga
                </h6>
                <small class="text-muted d-block mb-3" style="font-size: 0.74rem;">Aktivitas pemanfaatan sarana bersama tingkat RT</small>
                <div style="height: 220px;">
                    <canvas id="chartBookingsByRt"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card p-3.5 shadow-xs border-0 h-100" style="border-radius: 14px; background: #fff;">
                <h6 class="fw-bold mb-1 text-dark">
                    <i class="fas fa-wrench text-danger me-2"></i>Riwayat Catatan Kerusakan &amp; Pemeliharaan
                </h6>
                <small class="text-muted d-block mb-3" style="font-size: 0.74rem;">Temuan dari hasil inspeksi fisik pengembalian fasilitas</small>

                <div class="table-responsive nx-table-responsive" style="max-height: 220px; overflow-y: auto;">
                    <table class="table table-sm table-hover align-middle nx-table mb-0" style="font-size: 0.8rem;">
                        <thead>
                            <tr>
                                <th class="ps-3">Fasilitas</th>
                                <th>Tanggal</th>
                                <th>Catatan Kerusakan</th>
                                <th class="pe-3">Tindak Lanjut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($damageReports as $dr)
                                <tr>
                                    <td class="ps-3 fw-bold text-dark">{{ $dr->fasilitas ? $dr->fasilitas->nama : '-' }}</td>
                                    <td>{{ $dr->tanggal_cek_akhir ? $dr->tanggal_cek_akhir->translatedFormat('d M Y') : '-' }}</td>
                                    <td class="text-danger fw-semibold">{{ $dr->catatan_sesudah ?: 'Ada kerusakan alat' }}</td>
                                    <td class="pe-3"><small class="text-muted">{{ $dr->tindak_lanjut_kerusakan ?: 'Ditangani teknisi RW' }}</small></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted small">
                                        <i class="fas fa-shield-check text-success fa-lg mb-1 d-block"></i>
                                        Belum ada riwayat kerusakan fasilitas tercatat. Seluruh aset terawat dengan baik!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- ========================================================================= -->
<!-- MODAL INTERAKTIF PEMINJAMAN FASILITAS -->
<!-- ========================================================================= -->

<!-- 1. MODAL AJUKAN PEMINJAMAN (BOOKING) -->
<div class="modal fade" id="modalBookingFasilitas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form action="{{ route('fasilitas.store_booking') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="font-size: 1.05rem; color: var(--nx-forest);">
                        <i class="fas fa-calendar-plus me-2"></i>Formulir Pengajuan Peminjaman Fasilitas
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Warning / Info Box -->
                    <div class="alert alert-light border shadow-xs p-2.5 rounded-3 mb-3 small d-flex align-items-center gap-2">
                        <i class="fas fa-shield-halved text-success fa-lg"></i>
                        <div>
                            Sistem secara otomatis mengecek jadwal bentrok. Pastikan jam penggunaan tidak bertabrakan dengan jadwal yang sudah disetujui.
                        </div>
                    </div>

                    <!-- Fasilitas Select -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Pilih Sarana / Fasilitas <span class="text-danger">*</span></label>
                        <select name="fasilitas_id" id="bookingFasilitasSelect" class="form-select" required>
                            <option value="">-- Pilih Fasilitas Umum --</option>
                            @foreach($fasilitas as $fOpt)
                                <option value="{{ $fOpt->id }}" 
                                        data-kapasitas="{{ $fOpt->kapasitas }}" 
                                        data-biaya="{{ $fOpt->biaya_sewa }}" 
                                        data-jaminan="{{ $fOpt->uang_jaminan }}"
                                        data-operasional="{{ $fOpt->jam_operasional }}">
                                    {{ $fOpt->nama }} ({{ $fOpt->kategori }}) - Kapasitas: {{ $fOpt->kapasitas }} org
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted d-block mt-1" id="bookingInfoFasilitasText" style="font-size: 0.72rem;"></small>
                    </div>

                    <!-- Data Peminjam (Auto-filled) -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Peminjam <span class="text-danger">*</span></label>
                            <input type="text" name="nama_peminjam" class="form-control" value="{{ Auth::user()->name }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Asal Wilayah RT <span class="text-danger">*</span></label>
                            <select name="rt_number" class="form-select" required>
                                @foreach($allRts as $rtOpt)
                                    <option value="{{ $rtOpt }}" {{ (Auth::user()->rt_number == $rtOpt) ? 'selected' : '' }}>RT {{ $rtOpt }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Nomor Kontak / WA <span class="text-danger">*</span></label>
                            <input type="text" name="nomor_kontak" class="form-control" value="{{ Auth::user()->warga ? Auth::user()->warga->telepon : '' }}" placeholder="0812-xxxx" required>
                        </div>
                    </div>

                    <!-- Waktu & Durasi -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Waktu Mulai Penggunaan <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="waktu_mulai" class="form-control" value="{{ date('Y-m-d\TH:i', strtotime('+1 day 09:00')) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Waktu Selesai Penggunaan <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="waktu_selesai" class="form-control" value="{{ date('Y-m-d\TH:i', strtotime('+1 day 12:00')) }}" required>
                        </div>
                    </div>

                    <!-- Tujuan & Jumlah Peserta -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Tujuan / Agenda Peminjaman <span class="text-danger">*</span></label>
                            <input type="text" name="tujuan" class="form-control" placeholder="Contoh: Rapat Warga RT 006 / Senam Bersama / Tasyakuran..." required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Perkiraan Jumlah Peserta <span class="text-danger">*</span></label>
                            <input type="number" name="jumlah_peserta" class="form-control" value="30" min="1" required>
                        </div>
                    </div>

                    <!-- Dokumen Pendukung & Catatan -->
                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Dokumen Pendukung / Surat Izin (Opsional)</label>
                            <input type="file" name="dokumen_pendukung" class="form-control" accept=".pdf,image/*">
                            <small class="text-muted" style="font-size: 0.72rem;">Format: PDF atau Gambar (Maks. 4 MB).</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Catatan Tambahan untuk Pengelola</label>
                            <input type="text" name="catatan" class="form-control" placeholder="Contoh: Membutuhkan proyektor dan 40 kursi tambahan...">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">Kirim Permohonan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 2. MODAL DETAIL FASILITAS & ATURAN -->
<div class="modal fade" id="modalDetailFasilitas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <span class="badge bg-light text-dark border px-2 py-0.5 fw-bold mb-1" id="detFasilitasKategori">-</span>
                    <h5 class="modal-title fw-bold text-dark" id="detFasilitasNama" style="font-size: 1.1rem;">-</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 bg-light small" style="line-height: 1.8;">
                            <div><strong>Lokasi:</strong> <span id="detFasilitasLokasi">-</span></div>
                            <div><strong>Kapasitas Maksimal:</strong> <strong class="text-dark" id="detFasilitasKapasitas">-</strong> Orang</div>
                            <div><strong>Jam Operasional:</strong> <span id="detFasilitasJam">-</span></div>
                            <div><strong>Penanggung Jawab:</strong> <span id="detFasilitasPj">-</span> (<span id="detFasilitasKontak">-</span>)</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 bg-light small" style="line-height: 1.8;">
                            <div><strong>Status Operasional:</strong> <span id="detFasilitasStatusBadge">-</span></div>
                            <div><strong>Kondisi Fisik:</strong> <span id="detFasilitasKondisiBadge">-</span></div>
                            <div><strong>Biaya Peminjaman:</strong> <strong class="text-primary" id="detFasilitasBiaya">-</strong></div>
                            <div><strong>Uang Jaminan:</strong> <span id="detFasilitasJaminan">-</span></div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark">Deskripsi Fasilitas:</label>
                    <p class="small text-muted mb-0 p-2.5 rounded-2 border bg-white" id="detFasilitasDeskripsi" style="line-height: 1.5;">-</p>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark">Daftar Perlengkapan &amp; Inventaris:</label>
                    <div class="small p-2.5 rounded-2 border bg-white" id="detFasilitasPerlengkapan" style="line-height: 1.5;">-</div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark"><i class="fas fa-gavel text-warning me-1"></i>Aturan Penggunaan:</label>
                    <div class="small p-2.5 rounded-2 border bg-white text-muted" id="detFasilitasAturan" style="white-space: pre-line; line-height: 1.6;">-</div>
                </div>

                <div>
                    <label class="form-label small fw-bold text-dark"><i class="fas fa-calendar-check text-success me-1"></i>Agenda Peminjaman Terkonfirmasi Mendatang:</label>
                    <div class="table-responsive border rounded-2" style="max-height: 150px; overflow-y: auto;">
                        <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.78rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-2">Waktu</th>
                                    <th>Peminjam</th>
                                    <th>Tujuan</th>
                                </tr>
                            </thead>
                            <tbody id="detFasilitasUpcomingList"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- 3. MODAL DETAIL PENGAJUAN PEMINJAMAN -->
<div class="modal fade" id="modalDetailBooking" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <span class="badge bg-light text-dark border px-2 py-0.5 font-monospace fw-bold mb-1" id="detBookNo">-</span>
                    <h5 class="modal-title fw-bold text-dark" id="detBookTujuan" style="font-size: 1.05rem;">-</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 bg-light h-100 small" style="line-height: 1.8;">
                            <div><strong>Fasilitas:</strong> <span class="fw-bold text-dark" id="detBookFasilitas">-</span></div>
                            <div><strong>Peminjam:</strong> <span id="detBookNama">-</span> (<span id="detBookKontak">-</span>)</div>
                            <div><strong>Asal RT:</strong> <span class="badge bg-light text-dark border" id="detBookRt">-</span></div>
                            <div><strong>Jumlah Peserta:</strong> <span id="detBookPeserta">-</span> Orang</div>
                            <div><strong>Status Permohonan:</strong> <span id="detBookStatusBadge">-</span></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 bg-light h-100 small" style="line-height: 1.8;">
                            <div><strong>Mulai:</strong> <span id="detBookMulai">-</span></div>
                            <div><strong>Selesai:</strong> <span id="detBookSelesai">-</span></div>
                            <div><strong>Biaya Sewa / Jaminan:</strong> <span id="detBookBiaya">-</span> / <span id="detBookJaminan">-</span></div>
                            <div><strong>Status Pembayaran:</strong> <span class="badge bg-light text-dark border text-uppercase" id="detBookBayar">-</span></div>
                            <div><strong>Disetujui Oleh:</strong> <span id="detBookApprover">-</span></div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark">Catatan Pengelola / Alasan:</label>
                    <div class="p-2.5 rounded-2 border bg-white small text-muted" id="detBookCatatan" style="line-height: 1.5;">-</div>
                </div>

                <!-- Hasil Inspeksi Fisik (Jika ada) -->
                <div class="mb-3" id="detBookBoxInspeksi" style="display: none;">
                    <label class="form-label small fw-bold text-dark"><i class="fas fa-list-check text-success me-1"></i>Hasil Pemeriksaan Kondisi Fisik:</label>
                    <div class="p-3 rounded-2 border bg-light small" style="line-height: 1.6;">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <strong>Sebelum Digunakan:</strong> <span id="detInspeksiSebelum" class="badge bg-success-subtle text-success">-</span>
                                <div class="text-muted small mt-1" id="detInspeksiCatatanSebelum">-</div>
                            </div>
                            <div class="col-md-6">
                                <strong>Setelah Digunakan:</strong> <span id="detInspeksiSesudah" class="badge bg-info-subtle text-info">-</span>
                                <div class="text-muted small mt-1" id="detInspeksiCatatanSesudah">-</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Timeline Riwayat Status -->
                <div>
                    <label class="form-label small fw-bold text-dark"><i class="fas fa-timeline text-muted me-1"></i>Riwayat Status &amp; Aktivitas:</label>
                    <div class="p-3 rounded-2 border bg-light" style="max-height: 150px; overflow-y: auto;">
                        <ul class="list-unstyled mb-0" id="detBookHistoriesList" style="font-size: 0.78rem;"></ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- 4. MODAL REVIEW / TINJAU PERMOHONAN (ADMIN) -->
<div class="modal fade" id="modalReviewBooking" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formReviewBooking" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="font-size: 1rem; color: var(--nx-forest);">
                        <i class="fas fa-clipboard-check me-2"></i>Review &amp; Keputusan Peminjaman
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Pilih Keputusan Status <span class="text-danger">*</span></label>
                        <select name="status" id="reviewStatusSelect" class="form-select" required>
                            <option value="disetujui">Setujui Peminjaman (Jadwal Terkunci)</option>
                            <option value="ditolak">Tolak Permohonan</option>
                            <option value="digunakan">Tandai Sedang Digunakan</option>
                            <option value="selesai">Tandai Selesai Tuntas</option>
                        </select>
                    </div>

                    <div class="mb-3" id="boxAlasanTolakBooking" style="display: none;">
                        <label class="form-label small fw-bold text-danger">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="alasan_penolakan" class="form-control" rows="2" placeholder="Jelaskan alasan penolakan (misal: jadwal bentrok, kapasitas berlebih)..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Catatan Pengelola untuk Warga</label>
                        <textarea name="catatan_pengelola" id="reviewCatatanInput" class="form-control" rows="2" placeholder="Contoh: Kunci dapat diambil di pos RW H-1 acara..."></textarea>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold">Status Pembayaran / Jaminan</label>
                        <select name="status_pembayaran" class="form-select">
                            <option value="gratis">Gratis (Fasilitas Tanpa Biaya)</option>
                            <option value="lunas">Lunas (Biaya / Deposit Diterima)</option>
                            <option value="menunggu_pembayaran">Menunggu Pembayaran / Deposit</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">Simpan Keputusan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 5. MODAL PEMERIKSAAN KONDISI FISIK (SEBELUM & SESUDAH) -->
<div class="modal fade" id="modalPemeriksaanFasilitas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form id="formPemeriksaanFasilitas" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="font-size: 1rem; color: var(--nx-forest);">
                        <i class="fas fa-list-check me-2"></i>Pemeriksaan Kondisi Fisik Fasilitas &amp; Serah Terima
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tahap Pemeriksaan <span class="text-danger">*</span></label>
                            <select name="tahap" class="form-select" required>
                                <option value="sebelum">1. Sebelum Digunakan (Serah Terima Awal)</option>
                                <option value="sesudah">2. Sesudah Digunakan (Pemeriksaan Pengembalian)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Petugas Pemeriksa <span class="text-danger">*</span></label>
                            <input type="text" name="petugas_nama" class="form-control" value="{{ Auth::user()->name }}" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Kondisi Fasilitas <span class="text-danger">*</span></label>
                            <select name="kondisi" class="form-select" required>
                                <option value="Baik">Kondisi Baik &amp; Bersih</option>
                                <option value="Cukup">Kondisi Cukup Baik</option>
                                <option value="Kotor Perlu Pembersihan">Kotor (Perlu Pembersihan)</option>
                                <option value="Ada Kerusakan">Ada Kerusakan</option>
                                <option value="Ada Kehilangan Perlengkapan">Ada Kehilangan Perlengkapan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Foto Dokumentasi Pemeriksaan</label>
                            <input type="file" name="foto" class="form-control" accept="image/*">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Daftar Perlengkapan yang Diserahterimakan / Dikembalikan</label>
                        <input type="text" name="perlengkapan" class="form-control" placeholder="Contoh: 2 mic wireless, remote AC, kunci aula 2 buah...">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Catatan Pemeriksaan</label>
                        <textarea name="catatan" class="form-control" rows="2" placeholder="Tuliskan catatan kondisi fisik atau kebersihan fasilitas..."></textarea>
                    </div>

                    <div class="p-3 rounded-2 bg-light border">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="ada_kerusakan" value="1" id="checkAdaKerusakan">
                            <label class="form-check-label small fw-bold text-danger" for="checkAdaKerusakan">
                                Ada Kerusakan / Kehilangan Barang (Tandai jika ditemukan masalah)
                            </label>
                        </div>
                        <div id="boxTindakLanjutKerusakan" style="display: none;">
                            <label class="form-label small fw-bold text-danger">Catatan Kerusakan &amp; Usulan Tindak Lanjut:</label>
                            <textarea name="tindak_lanjut_kerusakan" class="form-control form-control-sm" rows="2" placeholder="Contoh: Gagang pintu toilet patah, biaya perbaikan dipotong dari uang jaminan..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">Simpan Hasil Pemeriksaan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 6. MODAL TAMBAH FASILITAS BARU (ADMIN RW) -->
@if($isAdminRw)
<div class="modal fade" id="modalTambahFasilitas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form action="{{ route('fasilitas.store_fasilitas') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="font-size: 1.05rem; color: var(--nx-forest);">
                        <i class="fas fa-plus-circle me-2"></i>Tambah Sarana / Fasilitas Umum Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label small fw-bold">Nama Fasilitas <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" placeholder="Contoh: Balai Pertemuan RW 018" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-bold">Kategori Fasilitas <span class="text-danger">*</span></label>
                            <select name="kategori" class="form-select" required>
                                <option value="Gedung / Aula">Gedung / Aula</option>
                                <option value="Lapangan Olahraga">Lapangan Olahraga</option>
                                <option value="Inventaris Acara">Inventaris Acara</option>
                                <option value="Area Terbuka">Area Terbuka</option>
                                <option value="Peralatan Kebersihan">Peralatan Kebersihan</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Lokasi Fasilitas <span class="text-danger">*</span></label>
                            <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Jl. Mawar Blok A Depan Lapangan" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Kapasitas (Orang) <span class="text-danger">*</span></label>
                            <input type="number" name="kapasitas" class="form-control" value="50" min="0" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Jam Buka <span class="text-danger">*</span></label>
                            <input type="text" name="jam_operasional" class="form-control" value="08:00 - 22:00 WIB" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Deskripsi Fasilitas &amp; Fungsi <span class="text-danger">*</span></label>
                        <textarea name="deskripsi" class="form-control" rows="2" placeholder="Jelaskan fungsi utama sarana ini..." required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Daftar Inventaris / Perlengkapan</label>
                        <input type="text" name="daftar_perlengkapan" class="form-control" placeholder="Contoh: Sound system, 2 mic, proyektor, 100 kursi...">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Aturan Penggunaan</label>
                        <textarea name="aturan_penggunaan" class="form-control" rows="2" placeholder="Aturan kebersihan, batas suara malam, larangan merokok..."></textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Biaya Sewa Warga (Rp)</label>
                            <input type="number" name="biaya_sewa" class="form-control" value="0" min="0">
                            <small class="text-muted" style="font-size: 0.72rem;">Isi 0 jika gratis untuk warga RW 018.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Uang Jaminan / Deposit (Rp)</label>
                            <input type="number" name="uang_jaminan" class="form-control" value="0" min="0">
                        </div>
                    </div>

                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Kondisi Fisik</label>
                            <select name="kondisi_fisik" class="form-select">
                                <option value="Baik">Kondisi Baik</option>
                                <option value="Perlu Perawatan">Perlu Perawatan</option>
                                <option value="Tidak Tersedia">Tidak Tersedia</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Status Operasional</label>
                            <select name="status_operasional" class="form-select">
                                <option value="tersedia">Tersedia (Siap Dipinjam)</option>
                                <option value="pemeliharaan">Dalam Pemeliharaan</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">Simpan Fasilitas</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- ========================================================================= -->
<!-- CHART.JS & KALENDER SCRIPTS -->
<!-- ========================================================================= -->
@if($activeTab === 'laporan' && !$isWarga)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Chart Tren Peminjaman Bulanan
    const ctxTren = document.getElementById('chartTrenPeminjaman');
    if (ctxTren) {
        new Chart(ctxTren.getContext('2d'), {
            type: 'line',
            data: {
                labels: @json($chartMonths),
                datasets: [
                    {
                        label: 'Total Pengajuan',
                        data: @json($chartBookingsMonthly),
                        borderColor: '#E69500',
                        backgroundColor: 'rgba(230, 149, 0, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Peminjaman Disetujui/Sukses',
                        data: @json($chartBookingsApprovedMonthly),
                        borderColor: '#285943',
                        backgroundColor: 'rgba(40, 89, 67, 0.1)',
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    }

    // 2. Chart Top Fasilitas (Doughnut)
    const ctxTop = document.getElementById('chartTopFasilitas');
    if (ctxTop) {
        const topData = @json($chartTopFacilities);
        new Chart(ctxTop.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: topData.labels.length > 0 ? topData.labels : ['Belum ada'],
                datasets: [{
                    data: topData.counts.length > 0 ? topData.counts : [1],
                    backgroundColor: ['#285943', '#1F567A', '#E69500', '#8b5cf6', '#06b6d4'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } }
                }
            }
        });
    }

    // 3. Chart Bookings by RT (Bar)
    const ctxRt = document.getElementById('chartBookingsByRt');
    if (ctxRt) {
        const rtData = @json($chartBookingsByRt);
        new Chart(ctxRt.getContext('2d'), {
            type: 'bar',
            data: {
                labels: rtData.labels,
                datasets: [{
                    label: 'Peminjaman Sukses',
                    data: rtData.counts,
                    backgroundColor: '#285943',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    }
});
</script>
@endif

<!-- KALENDER KETERSEDIAAN WIDGET SCRIPT -->
@if($activeTab === 'kalender')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('facilityCalendarWidget');
    if (calendarEl) {
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'id',
            height: 'auto',
            aspectRatio: 1.45,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listMonth'
            },
            buttonText: {
                today: 'Hari Ini',
                month: 'Bulan',
                week: 'Minggu',
                list: 'Daftar'
            },
            dayMaxEvents: 2,
            moreLinkClick: 'popover',
            moreLinkText: function(num) {
                return '+' + num + ' agenda lainnya';
            },
            events: @json($calendarEventsJson),
            eventDidMount: function(info) {
                const ep = info.event.extendedProps;
                if (ep && ep.is_pending) {
                    info.el.style.border = '1.5px dashed #D97706';
                    info.el.style.backgroundColor = '#FEF3DF';
                    info.el.style.color = '#B45309';
                    const titleEl = info.el.querySelector('.fc-event-title');
                    if (titleEl) {
                        titleEl.style.color = '#B45309';
                        titleEl.style.fontWeight = '700';
                    }
                }
            },
            eventClick: function(info) {
                const id = info.event.id;
                if (id && typeof window.openDetailBookingById === 'function') {
                    window.openDetailBookingById(id);
                }
            }
        });
        calendar.render();
    }
});
</script>
@endif

<script>
window.openDetailBookingById = function(id) {
    if (!id) return;
    fetch(`/fasilitas/booking/${id}`)
        .then(r => r.json())
        .then(res => {
            if (res.success && res.data) {
                openDetailBookingModal(res.data);
            }
        })
        .catch(err => console.error('Gagal memuat detail peminjaman:', err));
};

document.addEventListener('DOMContentLoaded', function() {
    // 1. Pre-select fasilitas when clicking "Ajukan" on card
    document.querySelectorAll('.btn-book-this-fasilitas').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const select = document.getElementById('bookingFasilitasSelect');
            if (select) {
                select.value = id;
                select.dispatchEvent(new Event('change'));
            }
            new bootstrap.Modal(document.getElementById('modalBookingFasilitas')).show();
        });
    });

    // 2. Auto-display capacity & fee info when facility selected
    const bSelect = document.getElementById('bookingFasilitasSelect');
    const infoText = document.getElementById('bookingInfoFasilitasText');
    if (bSelect && infoText) {
        bSelect.addEventListener('change', function() {
            const opt = bSelect.options[bSelect.selectedIndex];
            if (opt.value) {
                const kap = opt.getAttribute('data-kapasitas');
                const biaya = parseFloat(opt.getAttribute('data-biaya') || 0);
                const jaminan = parseFloat(opt.getAttribute('data-jaminan') || 0);
                const jam = opt.getAttribute('data-operasional');
                infoText.innerHTML = `<i class="fas fa-info-circle text-primary me-1"></i> Kapasitas Maks: <strong>${kap} orang</strong> | Biaya: <strong>${biaya > 0 ? 'Rp ' + biaya.toLocaleString('id-ID') : 'Gratis'}</strong> | Jam Operasional: ${jam}`;
            } else {
                infoText.innerHTML = '';
            }
        });
    }

    // 3. Detail Fasilitas Modal
    document.querySelectorAll('.btn-detail-fasilitas').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            fetch(`/fasilitas/${id}/detail`)
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        const d = res.data;
                        document.getElementById('detFasilitasNama').textContent = d.nama;
                        document.getElementById('detFasilitasKategori').textContent = d.kategori;
                        document.getElementById('detFasilitasLokasi').textContent = d.lokasi;
                        document.getElementById('detFasilitasKapasitas').textContent = d.kapasitas;
                        document.getElementById('detFasilitasJam').textContent = d.jam_operasional;
                        document.getElementById('detFasilitasPj').textContent = d.penanggung_jawab;
                        document.getElementById('detFasilitasKontak').textContent = d.kontak_pj;
                        document.getElementById('detFasilitasStatusBadge').innerHTML = d.status_badge;
                        document.getElementById('detFasilitasKondisiBadge').innerHTML = d.kondisi_badge;
                        document.getElementById('detFasilitasBiaya').textContent = d.biaya_sewa_formatted;
                        document.getElementById('detFasilitasJaminan').textContent = d.uang_jaminan_formatted;
                        document.getElementById('detFasilitasDeskripsi').textContent = d.deskripsi;
                        document.getElementById('detFasilitasPerlengkapan').textContent = d.daftar_perlengkapan;
                        document.getElementById('detFasilitasAturan').textContent = d.aturan_penggunaan;

                        const upBody = document.getElementById('detFasilitasUpcomingList');
                        upBody.innerHTML = '';
                        if (d.upcoming_bookings && d.upcoming_bookings.length > 0) {
                            d.upcoming_bookings.forEach(b => {
                                const tr = document.createElement('tr');
                                tr.innerHTML = `
                                    <td class="ps-2 fw-semibold">${b.waktu_mulai} - ${b.waktu_selesai}</td>
                                    <td>${b.nama_peminjam} (RT ${b.rt})</td>
                                    <td class="text-truncate" style="max-width: 140px;">${b.tujuan}</td>
                                `;
                                upBody.appendChild(tr);
                            });
                        } else {
                            upBody.innerHTML = '<tr><td colspan="3" class="text-center py-2 text-muted">Belum ada agenda peminjaman terkonfirmasi mendatang.</td></tr>';
                        }

                        new bootstrap.Modal(document.getElementById('modalDetailFasilitas')).show();
                    }
                });
        });
    });

    // 4. Detail Booking Modal
    window.openDetailBookingModal = function(d) {
        document.getElementById('detBookNo').textContent = d.nomor_pengajuan;
        document.getElementById('detBookTujuan').textContent = d.tujuan;
        document.getElementById('detBookFasilitas').textContent = d.fasilitas_nama;
        document.getElementById('detBookNama').textContent = d.nama_peminjam;
        document.getElementById('detBookKontak').textContent = d.nomor_kontak;
        document.getElementById('detBookRt').textContent = 'RT ' + d.rt_number;
        document.getElementById('detBookPeserta').textContent = d.jumlah_peserta;
        document.getElementById('detBookStatusBadge').innerHTML = d.status_badge;
        document.getElementById('detBookMulai').textContent = d.waktu_mulai_formatted;
        document.getElementById('detBookSelesai').textContent = d.waktu_selesai_formatted;
        document.getElementById('detBookBiaya').textContent = d.biaya_sewa_formatted;
        document.getElementById('detBookJaminan').textContent = d.uang_jaminan_formatted;
        document.getElementById('detBookBayar').textContent = d.status_pembayaran;
        document.getElementById('detBookApprover').textContent = d.approver_nama;
        document.getElementById('detBookCatatan').textContent = d.alasan_penolakan !== '-' ? `Alasan: ${d.alasan_penolakan}` : d.catatan_pengelola;

        // Inspeksi Fisik
        const boxInsp = document.getElementById('detBookBoxInspeksi');
        if (d.pemeriksaan) {
            boxInsp.style.display = 'block';
            document.getElementById('detInspeksiSebelum').textContent = d.pemeriksaan.kondisi_sebelum;
            document.getElementById('detInspeksiCatatanSebelum').textContent = d.pemeriksaan.catatan_sebelum;
            document.getElementById('detInspeksiSesudah').textContent = d.pemeriksaan.kondisi_sesudah;
            document.getElementById('detInspeksiCatatanSesudah').textContent = d.pemeriksaan.catatan_sesudah;
        } else {
            boxInsp.style.display = 'none';
        }

        // Timeline histories
        const hList = document.getElementById('detBookHistoriesList');
        hList.innerHTML = '';
        if (d.histories && d.histories.length > 0) {
            d.histories.forEach(h => {
                const li = document.createElement('li');
                li.className = 'mb-1 pb-1 border-bottom';
                li.innerHTML = `<span class="badge bg-secondary-subtle text-secondary me-1.5 uppercase">${h.status_baru}</span> <strong>${h.petugas}</strong>: ${h.keterangan} <span class="text-muted float-end">${h.time_formatted}</span>`;
                hList.appendChild(li);
            });
        }

        new bootstrap.Modal(document.getElementById('modalDetailBooking')).show();
    };

    document.querySelectorAll('.btn-detail-booking').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            fetch(`/fasilitas/booking/${id}`)
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        openDetailBookingModal(res.data);
                    }
                });
        });
    });

    // 5. Review Booking Modal
    document.querySelectorAll('.btn-review-booking').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const status = this.getAttribute('data-status');
            const catatan = this.getAttribute('data-catatan') || '';

            const form = document.getElementById('formReviewBooking');
            form.action = `/fasilitas/booking/${id}/review`;
            document.getElementById('reviewStatusSelect').value = status;
            document.getElementById('reviewCatatanInput').value = catatan;

            const boxTolak = document.getElementById('boxAlasanTolakBooking');
            boxTolak.style.display = status === 'ditolak' ? 'block' : 'none';

            new bootstrap.Modal(document.getElementById('modalReviewBooking')).show();
        });
    });

    const revSelect = document.getElementById('reviewStatusSelect');
    const boxTolak = document.getElementById('boxAlasanTolakBooking');
    if (revSelect && boxTolak) {
        revSelect.addEventListener('change', function() {
            boxTolak.style.display = this.value === 'ditolak' ? 'block' : 'none';
        });
    }

    // 6. Inspeksi Fisik Modal
    document.querySelectorAll('.btn-inspeksi-booking').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const form = document.getElementById('formPemeriksaanFasilitas');
            form.action = `/fasilitas/pemeriksaan/${id}`;

            new bootstrap.Modal(document.getElementById('modalPemeriksaanFasilitas')).show();
        });
    });

    const checkRusak = document.getElementById('checkAdaKerusakan');
    const boxRusak = document.getElementById('boxTindakLanjutKerusakan');
    if (checkRusak && boxRusak) {
        checkRusak.addEventListener('change', function() {
            boxRusak.style.display = this.checked ? 'block' : 'none';
        });
    }
});
</script>
@endsection
