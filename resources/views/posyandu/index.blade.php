@extends('layouts.app')

@section('title', 'Posyandu Digital — NexaNest')

@section('content')
@php
    $user = Auth::user();
@endphp

<!-- ==================== HEADER HALAMAN ==================== -->
<div class="nx-page-header mb-4">
    <div>
        <span class="badge rounded-pill mb-2 px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
            <i class="fas fa-heart-pulse me-1"></i> Layanan Kesehatan Lingkungan RW 018
        </span>
        <h1 class="nx-page-title">Informasi &amp; Pemantauan Posyandu Digital</h1>
        <p class="nx-page-subtitle">Satu sistem terpadu pemantauan tumbuh kembang balita dengan KMS digital, skrining kesehatan ibu hamil (ANC), imunisasi rutin, dan jadwal kegiatan posyandu.</p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        @if($isAdminRw)
            <a href="{{ route('posyandu.petugas.index') }}" class="btn btn-outline-dark" style="border-radius: 10px; font-weight: 600;">
                <i class="fas fa-users-gear me-1"></i> Manajemen Petugas ({{ $totalPetugasAktif }} Aktif)
            </a>
        @endif
        @if($isOfficer && !$isWarga)
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahJadwal">
                <i class="fas fa-calendar-plus me-1"></i> Tambah Jadwal Kegiatan
            </button>
            <div class="dropdown">
                <button class="btn btn-outline-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user-plus me-1"></i> Daftarkan Warga Baru
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="border-radius: 12px;">
                    <li>
                        <button class="dropdown-item py-2" type="button" data-bs-toggle="modal" data-bs-target="#modalTambahBalita">
                            <i class="fas fa-baby text-success me-2"></i>Daftarkan Balita Baru
                        </button>
                    </li>
                    <li>
                        <button class="dropdown-item py-2" type="button" data-bs-toggle="modal" data-bs-target="#modalTambahBumil">
                            <i class="fas fa-person-pregnant text-primary me-2"></i>Daftarkan Ibu Hamil Baru
                        </button>
                    </li>
                </ul>
            </div>
        @else
            <button type="button" class="btn btn-primary" onclick="handleRekamMedisKeluarga()">
                <i class="fas fa-users-viewfinder me-1"></i> Rekam Medis Keluarga Saya
            </button>
        @endif
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert" style="background: var(--nx-mint-soft); border-color: var(--nx-sage); color: var(--nx-forest); border-radius: 14px;">
        <i class="fas fa-check-circle me-2 fs-5"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert" style="border-radius: 14px;">
        <i class="fas fa-exclamation-circle me-2 fs-5"></i>
        <div>{{ session('error') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($isAdminRt && !$isOfficer)
    <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4 p-3" style="background: linear-gradient(135deg, #EBF8FF 0%, #DCEAF5 100%); border-left: 5px solid #3182CE !important; border-radius: 14px;">
        <i class="fas fa-shield-halved text-primary fs-4 me-3"></i>
        <div class="small">
            <strong class="text-primary d-block">Mode Akses Administratif Wilayah RT {{ $user->rt_number }}:</strong>
            <span class="text-secondary">Sebagai Pengurus RT, Anda memiliki hak pemantauan jadwal dan statistik agregat kesehatan balita &amp; bumil. Rekam medis klinis individual (KMS &amp; ANC) dirahasiakan dan dikhususkan untuk Petugas Posyandu / Tenaga Kesehatan terverifikasi atau orang tua warga yang bersangkutan.</span>
        </div>
    </div>
@endif

@if(!$isWarga)
<!-- ==================== 6 KARTU STATISTIK POSYANDU ==================== -->
<div class="row g-3 mb-4">
    <!-- 1. Total Balita -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <x-stat-card 
            title="Balita Terdaftar" 
            value="{{ $totalBalita }}" 
            sub="Usia 0 - 5 tahun RW 018" 
            icon="fas fa-baby" 
            theme="forest" 
        />
    </div>

    <!-- 2. Ibu Hamil Terdaftar -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <x-stat-card 
            title="Ibu Hamil Terdaftar" 
            value="{{ $totalBumil }}" 
            sub="Pemantauan ANC aktif" 
            icon="fas fa-person-pregnant" 
            theme="primary" 
            valColor="#2563EB"
        />
    </div>

    <!-- 3. Pemeriksaan Bulan Ini -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <x-stat-card 
            title="Pemeriksaan Bulan Ini" 
            value="{{ $pemeriksaanBulanIni }}" 
            sub="Periode {{ now()->translatedFormat('F Y') }}" 
            icon="fas fa-heart-pulse" 
            theme="success" 
            valColor="#059669"
        />
    </div>

    <!-- 4. Imunisasi Diberikan -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <x-stat-card 
            title="Imunisasi Diberikan" 
            value="{{ $totalImunisasiDiberikan }}" 
            sub="Dosis vaksin tercatat" 
            icon="fas fa-syringe" 
            theme="indigo" 
            valColor="#4F46E5"
        />
    </div>

    <!-- 5. Jadwal Posyandu Mendatang -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <x-stat-card 
            title="Jadwal Mendatang" 
            value="{{ $jadwalMendatang }}" 
            sub="Kegiatan terencana" 
            icon="far fa-calendar-check" 
            theme="forest" 
        />
    </div>

    <!-- 6. Perlu Tindak Lanjut -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <x-stat-card 
            title="Butuh Tindak Lanjut" 
            value="{{ $perluTindakLanjutCount }}" 
            sub="Risiko gizi & kesehatan" 
            icon="fas fa-triangle-exclamation" 
            theme="warning" 
            valColor="#D97706"
        />
    </div>
</div>
@endif

<!-- ==================== BANNER PERINGATAN TINDAK LANJUT (JIKA ADA) ==================== -->
@if($perluTindakLanjutCount > 0 && $isOfficer && !$isWarga)
<div class="card p-4 mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #FFFBEB 0%, #FEF3C7 100%); border-left: 5px solid #F59E0B !important; border-radius: 16px;">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
        <div class="d-flex align-items-center gap-2">
            <span class="rounded-circle d-inline-flex align-items-center justify-content-center text-warning" style="width: 32px; height: 32px; background: rgba(245, 158, 11, 0.2);">
                <i class="fas fa-bell"></i>
            </span>
            <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.95rem;">
                Perhatian Khusus Petugas Posyandu &amp; Bidan Wilayah ({{ $perluTindakLanjutCount }} Warga Butuh Tindak Lanjut)
            </h6>
        </div>
        <span class="badge rounded-pill bg-warning text-dark px-3 py-1 font-monospace" style="font-size: 0.72rem;">
            Intervensi Gizi &amp; Kesehatan
        </span>
    </div>
    <p class="small text-muted mb-3">Terdapat balita dan ibu hamil dengan catatan antropometri di luar batas normal (indikasi gizi kurang, stunting, atau risiko KEK). Segera jadwalkan pemberian PMT dan kunjungan rumah.</p>

    <div class="row g-2">
        @foreach($balitaTindakLanjut->take(3) as $bt)
            <div class="col-md-4">
                <div class="p-2.5 rounded-3 bg-white border d-flex justify-content-between align-items-center">
                    <div>
                        <strong class="d-block text-dark small">{{ $bt->nama_anak }} ({{ $bt->umur_bulan }} Bln • RT {{ $bt->rt_number }})</strong>
                        <span class="badge rounded-pill bg-danger-subtle text-danger px-2 py-0.5" style="font-size: 0.7rem;">{{ $bt->status_tumbuh }}</span>
                    </div>
                    <a href="{{ route('posyandu.balita.show', $bt->id) }}" class="btn btn-sm btn-outline-primary py-1 px-2.5" style="font-size: 0.75rem;">
                        Buka KMS &rarr;
                    </a>
                </div>
            </div>
        @endforeach
        @foreach($bumilTindakLanjut->take(3) as $bmt)
            <div class="col-md-4">
                <div class="p-2.5 rounded-3 bg-white border d-flex justify-content-between align-items-center">
                    <div>
                        <strong class="d-block text-dark small">{{ $bmt->nama_ibu }} (Hamil {{ $bmt->usia_kehamilan_minggu }} Mgg • RT {{ $bmt->rt_number }})</strong>
                        <span class="badge rounded-pill bg-warning-subtle text-dark px-2 py-0.5" style="font-size: 0.7rem;">{{ $bmt->status_kesehatan }}</span>
                    </div>
                    <a href="{{ route('posyandu.bumil.show', $bmt->id) }}" class="btn btn-sm btn-outline-primary py-1 px-2.5" style="font-size: 0.75rem;">
                        Buka ANC &rarr;
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- ==================== TAB UTAMA NAVIGASI FITUR ==================== -->
<div class="card p-2 mb-4 border-0 shadow-sm" style="background: var(--nx-bg); border-radius: 14px;">
    <ul class="nav nav-pills nav-fill gap-1" id="mainPosyanduTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link py-2.5 px-3 fw-bold d-flex align-items-center justify-content-center gap-2 active"
                    id="tab-btn-jadwal" data-bs-toggle="pill" data-bs-target="#pane-jadwal" type="button" role="tab"
                    style="border-radius: 10px; font-size: 0.88rem;">
                <i class="far fa-calendar-check"></i>
                <span>1. Kalender &amp; Jadwal Kegiatan</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link py-2.5 px-3 fw-bold d-flex align-items-center justify-content-center gap-2"
                    id="tab-btn-balita" data-bs-toggle="pill" data-bs-target="#pane-balita" type="button" role="tab"
                    style="border-radius: 10px; font-size: 0.88rem;">
                <i class="fas fa-baby"></i>
                <span>2. Balita &amp; KMS Digital ({{ $isWarga ? $anak->count() : $totalBalita }})</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link py-2.5 px-3 fw-bold d-flex align-items-center justify-content-center gap-2"
                    id="tab-btn-bumil" data-bs-toggle="pill" data-bs-target="#pane-bumil" type="button" role="tab"
                    style="border-radius: 10px; font-size: 0.88rem;">
                <i class="fas fa-person-pregnant"></i>
                <span>3. Kesehatan Ibu Hamil ({{ $isWarga ? $bumils->count() : $totalBumil }})</span>
            </button>
        </li>
        @if(!$isOfficer && !$isWarga)
        <li class="nav-item" role="presentation">
            <button class="nav-link py-2.5 px-3 fw-bold d-flex align-items-center justify-content-center gap-2"
                    id="tab-btn-keluarga" data-bs-toggle="pill" data-bs-target="#pane-keluarga" type="button" role="tab"
                    style="border-radius: 10px; font-size: 0.88rem;">
                <i class="fas fa-house-chimney-medical text-success"></i>
                <span>Data Keluarga Saya</span>
            </button>
        </li>
        @endif
        <li class="nav-item" role="presentation">
            <button class="nav-link py-2.5 px-3 fw-bold d-flex align-items-center justify-content-center gap-2"
                    id="tab-btn-edukasi" data-bs-toggle="pill" data-bs-target="#pane-edukasi" type="button" role="tab"
                    style="border-radius: 10px; font-size: 0.88rem;">
                <i class="fas fa-book-medical"></i>
                <span>4. Panduan &amp; Edukasi Gizi</span>
            </button>
        </li>
    </ul>
</div>

<div class="tab-content" id="mainPosyanduTabContent">
    <!-- ========================================================================= -->
    <!-- TAB 1: KALENDER & JADWAL KEGIATAN POSYANDU                                -->
    <!-- ========================================================================= -->
    <div class="tab-pane fade show active" id="pane-jadwal" role="tabpanel">
        <div class="card p-3 mb-4 border-0 shadow-sm">
            <form method="GET" action="{{ route('posyandu.index') }}" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search_jadwal" class="form-control border-start-0" placeholder="Cari nama posyandu, lokasi, materi kegiatan..." value="{{ request('search_jadwal') }}">
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <select name="status_jadwal" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="semua" {{ request('status_jadwal') === 'semua' ? 'selected' : '' }}>Semua Status Kegiatan</option>
                        <option value="terjadwal" {{ request('status_jadwal') === 'terjadwal' ? 'selected' : '' }}>Terjadwal (Mendatang)</option>
                        <option value="selesai" {{ request('status_jadwal') === 'selesai' ? 'selected' : '' }}>Selesai Terlaksana</option>
                        <option value="dibatalkan" {{ request('status_jadwal') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div class="col-md-3 text-md-end">
                    <button type="submit" class="btn btn-primary btn-sm px-3">
                        <i class="fas fa-filter me-1"></i> Terapkan Filter
                    </button>
                    <a href="{{ route('posyandu.index') }}" class="btn btn-outline-secondary btn-sm ms-1" title="Reset">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                </div>
            </form>
        </div>

        @if($jadwal->isEmpty())
            <x-empty-state 
                title="Belum Ada Jadwal Posyandu" 
                subtitle="Tidak ada jadwal kegiatan yang cocok dengan kriteria pencarian Anda." 
                icon="far fa-calendar-xmark" 
            />
        @else
            <div class="row g-3 mb-4">
                @foreach($jadwal as $ev)
                    @php
                        $badge = $ev->status_badge;
                        $tgl = \Carbon\Carbon::parse($ev->tanggal);
                        $isUpcoming = $tgl->isFuture() || $tgl->isToday();
                    @endphp
                    <div class="col-lg-4 col-md-6">
                        <div class="card p-3.5 h-100 d-flex flex-column justify-content-between border-0 shadow-sm"
                             style="border-radius: 16px; background: {{ $isUpcoming ? 'linear-gradient(135deg, #FFFFFF 0%, #F4F9F5 100%)' : '#FFFFFF' }}; border: 1px solid var(--nx-border) !important;">
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge rounded-pill px-2.5 py-1" style="background: {{ $badge['bg'] }}; color: {{ $badge['color'] }}; font-size: 0.72rem; font-weight: 700;">
                                        <i class="fas {{ $badge['icon'] }} me-1"></i>{{ $badge['label'] }}
                                    </span>
                                    <span class="badge rounded-pill px-2.5 py-1" style="background: var(--nx-bg); color: var(--nx-forest); border: 1px solid var(--nx-border); font-size: 0.72rem;">
                                        <i class="fas fa-tag me-1"></i>{{ $ev->kategori ?? 'Balita' }}
                                    </span>
                                </div>

                                <h6 class="fw-bold mb-1" style="color: var(--nx-text); font-size: 0.98rem;">{{ $ev->nama }}</h6>
                                
                                <div class="small text-muted mb-2 d-flex flex-column gap-1">
                                    <span><i class="far fa-calendar text-success me-1"></i><strong>{{ $tgl->translatedFormat('l, d F Y') }}</strong></span>
                                    <span><i class="far fa-clock text-success me-1"></i>{{ $ev->jam_mulai ?? '08:30' }} - {{ $ev->jam_selesai ?? '11:30' }} WIB</span>
                                    <span><i class="fas fa-location-dot text-success me-1"></i>{{ $ev->lokasi ?? 'Balai Warga Utama RW 018' }}</span>
                                    @if($ev->penanggung_jawab)
                                        <span><i class="fas fa-user-doctor text-success me-1"></i>{{ $ev->penanggung_jawab }}</span>
                                    @endif
                                </div>

                                <p class="text-muted small mb-3" style="line-height: 1.45; font-size: 0.82rem;">
                                    {{ Str::limit($ev->keterangan, 140) }}
                                </p>
                            </div>

                            @if($isOfficer)
                            <div class="pt-2 border-top d-flex justify-content-between align-items-center">
                                <small class="text-muted" style="font-size: 0.72rem;">ID #{{ $ev->id }}</small>
                                <div class="d-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2"
                                            data-bs-toggle="modal" data-bs-target="#modalEditJadwal{{ $ev->id }}"
                                            style="font-size: 0.75rem;" title="Edit Jadwal">
                                        <i class="fas fa-pencil"></i> Edit
                                    </button>
                                    <form action="{{ route('posyandu.jadwal.destroy', $ev->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal posyandu ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" style="font-size: 0.75rem;" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Modal Edit Jadwal -->
                            <div class="modal fade" id="modalEditJadwal{{ $ev->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow" style="border-radius: 18px;">
                                        <div class="modal-header border-bottom-0 pb-0">
                                            <h5 class="fw-bold modal-title" style="color: var(--nx-forest);">
                                                <i class="fas fa-pencil me-1"></i> Perbarui Jadwal Posyandu
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('posyandu.jadwal.update', $ev->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Nama Kegiatan</label>
                                                    <input type="text" name="nama" class="form-control" value="{{ $ev->nama }}" required>
                                                </div>
                                                <div class="row g-2 mb-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-bold">Tanggal</label>
                                                        <input type="date" name="tanggal" class="form-control" value="{{ $ev->tanggal->format('Y-m-d') }}" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-bold">Status</label>
                                                        <select name="status" class="form-select" required>
                                                            <option value="terjadwal" {{ $ev->status === 'terjadwal' ? 'selected' : '' }}>Terjadwal</option>
                                                            <option value="selesai" {{ $ev->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                                            <option value="dibatalkan" {{ $ev->status === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row g-2 mb-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-bold">Kategori</label>
                                                        <select name="kategori" class="form-select">
                                                            <option value="Balita" {{ $ev->kategori === 'Balita' ? 'selected' : '' }}>Balita</option>
                                                            <option value="Ibu Hamil" {{ $ev->kategori === 'Ibu Hamil' ? 'selected' : '' }}>Ibu Hamil</option>
                                                            <option value="Lansia" {{ $ev->kategori === 'Lansia' ? 'selected' : '' }}>Lansia</option>
                                                            <option value="Terpadu" {{ $ev->kategori === 'Terpadu' ? 'selected' : '' }}>Terpadu</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label small fw-bold">Mulai</label>
                                                        <input type="text" name="jam_mulai" class="form-control" value="{{ $ev->jam_mulai ?? '08:30' }}">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label small fw-bold">Selesai</label>
                                                        <input type="text" name="jam_selesai" class="form-control" value="{{ $ev->jam_selesai ?? '11:30' }}">
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Lokasi</label>
                                                    <input type="text" name="lokasi" class="form-control" value="{{ $ev->lokasi }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Penanggung Jawab / Bidan</label>
                                                    <input type="text" name="penanggung_jawab" class="form-control" value="{{ $ev->penanggung_jawab }}">
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label small fw-bold">Keterangan Kegiatan</label>
                                                    <textarea name="keterangan" rows="3" class="form-control">{{ $ev->keterangan }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-top-0">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 2: BALITA & KMS DIGITAL                                               -->
    <!-- ========================================================================= -->
    <div class="tab-pane fade" id="pane-balita" role="tabpanel">
        @if(!$isWarga)
        <div class="card p-3 mb-4 border-0 shadow-sm">
            <form method="GET" action="{{ route('posyandu.index') }}" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search_anak" class="form-control border-start-0" placeholder="Cari nama balita, nama ibu, nama ayah..." value="{{ request('search_anak') }}">
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <select name="rt_anak" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="semua" {{ request('rt_anak') === 'semua' ? 'selected' : '' }}>Semua Wilayah RT (006 - 010)</option>
                        @foreach($rtList as $rt)
                            <option value="{{ $rt }}" {{ request('rt_anak') === $rt ? 'selected' : '' }}>Wilayah RT {{ $rt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-sm-6">
                    <select name="status_anak" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="semua" {{ request('status_anak') === 'semua' ? 'selected' : '' }}>Semua Status</option>
                        <option value="normal" {{ request('status_anak') === 'normal' ? 'selected' : '' }}>Normal / Baik</option>
                        <option value="tindak_lanjut" {{ request('status_anak') === 'tindak_lanjut' ? 'selected' : '' }}>Perlu Tindak Lanjut</option>
                    </select>
                </div>
                <div class="col-md-2 text-md-end">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
        @endif

        @if(!$isOfficer && $myBalitas->count() > 0 && !$isWarga)
            <div class="p-3 mb-4 rounded-4" style="background: var(--nx-mint-soft); border: 1px solid var(--nx-sage);">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="fas fa-circle-check text-success"></i>
                    <strong class="text-dark small">Balita Keluarga Anda yang Terdaftar di Posyandu Melati RW 018:</strong>
                </div>
                <div class="row g-2">
                    @foreach($myBalitas as $mb)
                        <div class="col-md-6">
                            <div class="p-2.5 rounded-3 bg-white border d-flex justify-content-between align-items-center">
                                <div>
                                    <strong class="d-block text-dark">{{ $mb->nama_anak }}</strong>
                                    <small class="text-muted">{{ $mb->usia_teks }} • {{ $mb->jenis_kelamin_teks }} • RT {{ $mb->rt_number }}</small>
                                </div>
                                <a href="{{ route('posyandu.balita.show', $mb->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-chart-line me-1"></i> Buka KMS Digital
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if($anak->isEmpty())
            <x-empty-state 
                title="{{ $isWarga ? 'Belum Ada Balita Terdaftar di KK Anda' : 'Data Balita Belum Ditemukan' }}" 
                subtitle="{{ $isWarga ? 'Fitur Rekam Medis Keluarga Saya tidak tersedia karena belum ada anggota keluarga (ibu hamil atau bayi/balita) yang terdaftar di Posyandu pada Kartu Keluarga Anda. Silakan hubungi kader Posyandu setempat jika ingin melakukan pendaftaran.' : 'Tidak ada data balita yang cocok dengan filter pencarian Anda.' }}" 
                icon="fas fa-baby" 
            />
        @else
            <div class="row g-3">
                @foreach($anak as $item)
                    @php
                        $badge = $item->status_badge;
                        $genderIcon = strtoupper($item->jenis_kelamin ?? 'L') === 'P' ? 'fa-venus text-danger' : 'fa-mars text-primary';
                    @endphp
                    <div class="col-xl-4 col-md-6">
                        <div class="card p-3.5 h-100 d-flex flex-column justify-content-between border-0 shadow-sm"
                             style="border-radius: 16px; border: 1px solid var(--nx-border) !important;">
                            <div>
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <span class="badge rounded-pill px-2.5 py-1 mb-1" style="background: var(--nx-bg); color: var(--nx-forest); border: 1px solid var(--nx-border); font-size: 0.72rem;">
                                            RT {{ $item->rt_number ?? '007' }} • <i class="fas {{ $genderIcon }}"></i> {{ $item->jenis_kelamin_teks }}
                                        </span>
                                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 1rem;">
                                            <a href="{{ route('posyandu.balita.show', $item->id) }}" class="text-decoration-none text-dark hover-forest">
                                                {{ $item->nama_anak }}
                                            </a>
                                        </h6>
                                    </div>
                                    <span class="badge rounded-pill px-2.5 py-1 text-truncate" style="background: {{ $badge['bg'] }}; color: {{ $badge['color'] }}; font-size: 0.72rem; max-width: 140px;" title="{{ $badge['label'] }}">
                                        <i class="fas {{ $badge['icon'] }} me-1"></i>{{ $badge['label'] }}
                                    </span>
                                </div>

                                <div class="p-2.5 rounded-3 mb-2" style="background: var(--nx-bg); font-size: 0.82rem;">
                                    <div class="row g-2 text-center">
                                        <div class="col-4">
                                            <span class="d-block text-muted" style="font-size: 0.7rem;">Usia</span>
                                            <strong class="text-dark">{{ $item->usia_teks }}</strong>
                                        </div>
                                        <div class="col-4">
                                            <span class="d-block text-muted" style="font-size: 0.7rem;">Berat</span>
                                            <strong style="color: var(--nx-forest);">{{ $item->berat_badan }} kg</strong>
                                        </div>
                                        <div class="col-4">
                                            <span class="d-block text-muted" style="font-size: 0.7rem;">Tinggi</span>
                                            <strong class="text-primary">{{ $item->tinggi_badan }} cm</strong>
                                        </div>
                                    </div>
                                </div>

                                <div class="small text-muted mb-2" style="font-size: 0.78rem;">
                                    <div><i class="fas fa-user-friends me-1 text-success"></i>Ibu: <strong>{{ $item->nama_ibu ?? 'Ibu Warga' }}</strong> • Ayah: {{ $item->nama_ayah ?? 'Bpk. Warga' }}</div>
                                    <div><i class="fas fa-location-dot me-1 text-success"></i>{{ Str::limit($item->alamat ?? 'RW 018', 40) }}</div>
                                </div>

                                @if($item->perlu_tindak_lanjut)
                                    <div class="p-2 rounded-2 mb-2" style="background: #FFFBEB; border: 1px dashed #F59E0B; font-size: 0.75rem; color: #B45309;">
                                        <i class="fas fa-triangle-exclamation me-1"></i><strong>Perhatian:</strong> {{ $item->alasan_tindak_lanjut ?? 'Perlu pendampingan berkala' }}
                                    </div>
                                @endif
                            </div>

                            <div class="pt-2 border-top d-flex justify-content-between align-items-center">
                                <small class="text-muted" style="font-size: 0.72rem;">
                                    {{ $item->pemeriksaans->count() }} Pemeriksaan
                                </small>
                                @if($isOfficer || (isset($familyWargaIds) && in_array($item->warga_id, $familyWargaIds)))
                                    <a href="{{ route('posyandu.balita.show', $item->id) }}" class="btn btn-sm btn-primary px-3 py-1" style="font-size: 0.78rem;">
                                        <i class="fas fa-chart-line me-1"></i> Buka KMS Digital
                                    </a>
                                @else
                                    <span class="badge bg-light text-muted border px-2.5 py-1" style="font-size: 0.72rem;" title="Data rekam medis individual hanya dapat diakses oleh Petugas Posyandu / Nakes atau orang tua bersangkutan.">
                                        <i class="fas fa-lock me-1"></i> Rekam Medis Privat
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 3: KESEHATAN IBU HAMIL (ANC)                                          -->
    <!-- ========================================================================= -->
    <div class="tab-pane fade" id="pane-bumil" role="tabpanel">
        @if(!$isWarga)
        <div class="card p-3 mb-4 border-0 shadow-sm">
            <form method="GET" action="{{ route('posyandu.index') }}" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search_bumil" class="form-control border-start-0" placeholder="Cari nama ibu, nama suami..." value="{{ request('search_bumil') }}">
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <select name="rt_bumil" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="semua" {{ request('rt_bumil') === 'semua' ? 'selected' : '' }}>Semua Wilayah RT (006 - 010)</option>
                        @foreach($rtList as $rt)
                            <option value="{{ $rt }}" {{ request('rt_bumil') === $rt ? 'selected' : '' }}>Wilayah RT {{ $rt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-sm-6">
                    <select name="status_bumil" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="semua" {{ request('status_bumil') === 'semua' ? 'selected' : '' }}>Semua Status</option>
                        <option value="normal" {{ request('status_bumil') === 'normal' ? 'selected' : '' }}>Normal / Baik</option>
                        <option value="tindak_lanjut" {{ request('status_bumil') === 'tindak_lanjut' ? 'selected' : '' }}>Perlu Tindak Lanjut</option>
                    </select>
                </div>
                <div class="col-md-2 text-md-end">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
        @endif

        @if(!$isOfficer && $myBumils->count() > 0 && !$isWarga)
            <div class="p-3 mb-4 rounded-4" style="background: var(--nx-powder); border: 1px solid #B0D4F1;">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="fas fa-circle-check text-primary"></i>
                    <strong class="text-dark small">Data Kehamilan Keluarga Anda di Posyandu Melati RW 018:</strong>
                </div>
                <div class="row g-2">
                    @foreach($myBumils as $mbu)
                        <div class="col-md-6">
                            <div class="p-2.5 rounded-3 bg-white border d-flex justify-content-between align-items-center">
                                <div>
                                    <strong class="d-block text-dark">{{ $mbu->nama_ibu }}</strong>
                                    <small class="text-muted">{{ $mbu->trimester }} • Usia {{ $mbu->usia_kehamilan_minggu }} Minggu • RT {{ $mbu->rt_number }}</small>
                                </div>
                                <a href="{{ route('posyandu.bumil.show', $mbu->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-stethoscope me-1"></i> Buka Timeline ANC
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if($bumils->isEmpty())
            <x-empty-state 
                title="{{ $isWarga ? 'Belum Ada Data Ibu Hamil Terdaftar di KK Anda' : 'Data Ibu Hamil Belum Ditemukan' }}" 
                subtitle="{{ $isWarga ? 'Fitur Rekam Medis Keluarga Saya tidak tersedia karena belum ada anggota keluarga (ibu hamil atau bayi/balita) yang terdaftar di Posyandu pada Kartu Keluarga Anda. Silakan hubungi kader Posyandu setempat jika ingin melakukan pendaftaran.' : 'Tidak ada data ibu hamil yang cocok dengan filter pencarian Anda.' }}" 
                icon="fas fa-person-pregnant" 
            />
        @else
            <div class="row g-3">
                @foreach($bumils as $item)
                    @php
                        $badge = $item->status_badge;
                    @endphp
                    <div class="col-xl-4 col-md-6">
                        <div class="card p-3.5 h-100 d-flex flex-column justify-content-between border-0 shadow-sm"
                             style="border-radius: 16px; border: 1px solid var(--nx-border) !important;">
                            <div>
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <span class="badge rounded-pill px-2.5 py-1 mb-1" style="background: var(--nx-powder); color: #1F517F; border: 1px solid #BCE0FD; font-size: 0.72rem;">
                                            RT {{ $item->rt_number ?? '008' }} • {{ $item->trimester }}
                                        </span>
                                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 1rem;">
                                            <a href="{{ route('posyandu.bumil.show', $item->id) }}" class="text-decoration-none text-dark hover-forest">
                                                {{ $item->nama_ibu }}
                                            </a>
                                        </h6>
                                    </div>
                                    <span class="badge rounded-pill px-2.5 py-1 text-truncate" style="background: {{ $badge['bg'] }}; color: {{ $badge['color'] }}; font-size: 0.72rem; max-width: 140px;" title="{{ $badge['label'] }}">
                                        <i class="fas {{ $badge['icon'] }} me-1"></i>{{ $badge['label'] }}
                                    </span>
                                </div>

                                <div class="p-2.5 rounded-3 mb-2" style="background: var(--nx-bg); font-size: 0.82rem;">
                                    <div class="row g-2 text-center">
                                        <div class="col-4">
                                            <span class="d-block text-muted" style="font-size: 0.7rem;">Usia Kandungan</span>
                                            <strong class="text-dark">{{ $item->usia_kehamilan_minggu }} Minggu</strong>
                                        </div>
                                        <div class="col-4">
                                            <span class="d-block text-muted" style="font-size: 0.7rem;">Tensi Darah</span>
                                            <strong class="text-primary">{{ $item->tekanan_darah }}</strong>
                                        </div>
                                        <div class="col-4">
                                            <span class="d-block text-muted" style="font-size: 0.7rem;">LiLA</span>
                                            <strong style="color: {{ $item->lila < 23.5 ? 'var(--nx-danger)' : 'var(--nx-forest)' }};">{{ $item->lila }} cm</strong>
                                        </div>
                                    </div>
                                </div>

                                <div class="small text-muted mb-2" style="font-size: 0.78rem;">
                                    <div><i class="fas fa-heart text-danger me-1"></i>Suami: <strong>{{ $item->nama_suami ?? 'Bpk. Pendamping' }}</strong></div>
                                    <div><i class="far fa-calendar-check text-primary me-1"></i>HPL: <strong>{{ $item->hpl ? $item->hpl->format('d M Y') : '-' }}</strong> ({{ $item->hpl_countdown }})</div>
                                    <div><i class="fas fa-location-dot text-success me-1"></i>{{ Str::limit($item->alamat ?? 'RW 018', 40) }}</div>
                                </div>

                                @if($item->perlu_tindak_lanjut)
                                    <div class="p-2 rounded-2 mb-2" style="background: #FEE2E2; border: 1px dashed #EF4444; font-size: 0.75rem; color: #B91C1C;">
                                        <i class="fas fa-triangle-exclamation me-1"></i><strong>Perhatian:</strong> {{ $item->alasan_tindak_lanjut ?? 'Perlu pemantauan intensif bidan' }}
                                    </div>
                                @endif
                            </div>

                            <div class="pt-2 border-top d-flex justify-content-between align-items-center">
                                <small class="text-muted" style="font-size: 0.72rem;">
                                    {{ $item->pemeriksaans->count() }} Pemeriksaan ANC
                                </small>
                                @if($isOfficer || (isset($familyWargaIds) && in_array($item->warga_id, $familyWargaIds)))
                                    <a href="{{ route('posyandu.bumil.show', $item->id) }}" class="btn btn-sm btn-primary px-3 py-1" style="font-size: 0.78rem;">
                                        <i class="fas fa-stethoscope me-1"></i> Buka Timeline ANC
                                    </a>
                                @else
                                    <span class="badge bg-light text-muted border px-2.5 py-1" style="font-size: 0.72rem;" title="Data rekam medis ANC individual hanya dapat diakses oleh Petugas Posyandu / Bidan atau ibu hamil bersangkutan.">
                                        <i class="fas fa-lock me-1"></i> Rekam Medis Privat
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 4: DATA KESEHATAN KELUARGA SAYA (KHUSUS WARGA)                        -->
    <!-- ========================================================================= -->
    @if(!$isOfficer && !$isWarga)
    <div class="tab-pane fade" id="pane-keluarga" role="tabpanel">
        <div class="card p-4 mb-4 border-0 shadow-sm" style="background: #FFFFFF; border-radius: 18px;">
            <div class="d-flex align-items-center gap-3 mb-3">
                <span class="rounded-circle d-inline-flex align-items-center justify-content-center text-white"
                      style="width: 44px; height: 44px; background: var(--nx-forest);">
                    <i class="fas fa-shield-halved fa-lg"></i>
                </span>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Ruang Privasi Kesehatan Keluarga Anda</h5>
                    <small class="text-muted">Akses eksklusif data pertumbuhan anak dan rekam medis kehamilan berdasarkan kartu keluarga resmi</small>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-6">
                    <h6 class="fw-bold mb-3" style="color: var(--nx-forest);"><i class="fas fa-baby me-2"></i>Balita Anda ({{ $myBalitas->count() }})</h6>
                    @if($myBalitas->isEmpty())
                        <div class="p-3 text-center text-muted small rounded-3 border bg-light">
                            Tidak ada balita yang tercatat atas Kartu Keluarga akun Anda. Jika anak Anda belum terdaftar di Posyandu, silakan hubungi kader RT/RW.
                        </div>
                    @else
                        @foreach($myBalitas as $mb)
                            <div class="p-3 rounded-3 border mb-2 d-flex justify-content-between align-items-center bg-white shadow-sm">
                                <div>
                                    <strong class="d-block text-dark">{{ $mb->nama_anak }}</strong>
                                    <small class="text-muted">{{ $mb->usia_teks }} • {{ $mb->jenis_kelamin_teks }} • BB {{ $mb->berat_badan }} kg / TB {{ $mb->tinggi_badan }} cm</small>
                                </div>
                                <a href="{{ route('posyandu.balita.show', $mb->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-chart-line me-1"></i> Buka KMS
                                </a>
                            </div>
                        @endforeach
                    @endif
                </div>

                <div class="col-lg-6">
                    <h6 class="fw-bold mb-3" style="color: var(--nx-forest);"><i class="fas fa-person-pregnant me-2"></i>Kehamilan / Ibu Hamil ({{ $myBumils->count() }})</h6>
                    @if($myBumils->isEmpty())
                        <div class="p-3 text-center text-muted small rounded-3 border bg-light">
                            Tidak ada data kehamilan aktif yang tercatat atas Kartu Keluarga Anda saat ini.
                        </div>
                    @else
                        @foreach($myBumils as $mbu)
                            <div class="p-3 rounded-3 border mb-2 d-flex justify-content-between align-items-center bg-white shadow-sm">
                                <div>
                                    <strong class="d-block text-dark">{{ $mbu->nama_ibu }}</strong>
                                    <small class="text-muted">{{ $mbu->trimester }} • Usia {{ $mbu->usia_kehamilan_minggu }} Minggu • HPL: {{ $mbu->hpl ? $mbu->hpl->format('d/m/Y') : '-' }}</small>
                                </div>
                                <a href="{{ route('posyandu.bumil.show', $mbu->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-stethoscope me-1"></i> Buka ANC
                                </a>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- ========================================================================= -->
    <!-- TAB 5: PANDUAN & EDUKASI GIZI LINGKUNGAN                                   -->
    <!-- ========================================================================= -->
    <div class="tab-pane fade" id="pane-edukasi" role="tabpanel">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card p-4 h-100 border-0 shadow-sm" style="border-radius: 16px;">
                    <h6 class="fw-bold mb-3" style="color: var(--nx-forest);">
                        <i class="fas fa-chart-area me-2"></i>Standar Kurva Pertumbuhan WHO &amp; Buku KMS
                    </h6>
                    <div class="d-flex flex-column gap-3 small text-muted" style="line-height: 1.6;">
                        <div class="p-3 rounded-3" style="background: var(--nx-bg); border-left: 4px solid var(--nx-forest);">
                            <strong class="d-block text-dark mb-1">1. Pita Hijau Tua (Median Standar Normal)</strong>
                            Pertumbuhan ideal sesuai rujukan WHO. Berat badan dan tinggi badan anak bertambah proporsional setiap bulan.
                        </div>
                        <div class="p-3 rounded-3" style="background: var(--nx-bg); border-left: 4px solid #10B981;">
                            <strong class="d-block text-dark mb-1">2. Pita Hijau Muda (-2 SD s/d +1 SD)</strong>
                            Kondisi gizi baik. Anak memiliki daya tahan tubuh optimal dan pertumbuhan fisik yang sehat.
                        </div>
                        <div class="p-3 rounded-3" style="background: var(--nx-bg); border-left: 4px solid #F59E0B;">
                            <strong class="d-block text-dark mb-1">3. Pita Kuning (-3 SD s/d -2 SD)</strong>
                            Kondisi waspada / berat badan kurang (underweight). Memerlukan tambahan asupan kalori dan protein hewani (PMT).
                        </div>
                        <div class="p-3 rounded-3" style="background: var(--nx-bg); border-left: 4px solid #EF4444;">
                            <strong class="d-block text-dark mb-1">4. Di Bawah Garis Merah / -3 SD (Gizi Buruk / Sangat Pendek)</strong>
                            Kondisi darurat gizi yang membutuhkan rujukan segera ke Puskesmas atau Dokter Spesialis Anak untuk intervensi spesifik.
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card p-4 h-100 border-0 shadow-sm" style="border-radius: 16px;">
                    <h6 class="fw-bold mb-3" style="color: var(--nx-forest);">
                        <i class="fas fa-syringe me-2"></i>Jadwal Imunisasi Dasar Lengkap Kemenkes RI
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless small mb-0">
                            <thead class="text-muted border-bottom">
                                <tr>
                                    <th>Usia Anak</th>
                                    <th>Vaksinasi Wajib</th>
                                    <th>Manfaat Proteksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="fw-bold text-dark">0 Bulan</td>
                                    <td><span class="badge bg-secondary-subtle text-dark">HB0</span></td>
                                    <td>Pencegahan Hepatitis B sejak dini</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-dark">1 Bulan</td>
                                    <td><span class="badge bg-secondary-subtle text-dark">BCG, Polio 1</span></td>
                                    <td>Proteksi Tuberkulosis (TBC) &amp; Polio</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-dark">2 Bulan</td>
                                    <td><span class="badge bg-secondary-subtle text-dark">DPT-HB-Hib 1, Polio 2, PCV 1, Rotavirus 1</span></td>
                                    <td>Difteri, Tetanus, Batuk Rejan, Pneumonia &amp; Diare</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-dark">3 Bulan</td>
                                    <td><span class="badge bg-secondary-subtle text-dark">DPT-HB-Hib 2, Polio 3, PCV 2, Rotavirus 2</span></td>
                                    <td>Dosis lanjutan penguat imun anak</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-dark">4 Bulan</td>
                                    <td><span class="badge bg-secondary-subtle text-dark">DPT-HB-Hib 3, Polio 4, IPV, Rotavirus 3</span></td>
                                    <td>Proteksi lengkap terhadap kelumpuhan polio</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-dark">9 Bulan</td>
                                    <td><span class="badge bg-danger-subtle text-danger">Campak-Rubela (MR 1)</span></td>
                                    <td>Pencegahan campak dan cacar rubella</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-dark">18 Bulan</td>
                                    <td><span class="badge bg-success-subtle text-success">DPT-HB-Hib Booster &amp; MR 2</span></td>
                                    <td>Imunitas jangka panjang usia balita</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==================== MODAL TAMBAH JADWAL POSYANDU ==================== -->
@if($isOfficer)
<div class="modal fade" id="modalTambahJadwal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 18px;">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="fw-bold modal-title" style="color: var(--nx-forest);">
                    <i class="far fa-calendar-plus me-1"></i> Jadwalkan Kegiatan Posyandu Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('posyandu.jadwal.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Kegiatan</label>
                        <input type="text" name="nama" class="form-control" placeholder="Contoh: Posyandu Balita Melati - Penimbangan &amp; Vitamin A" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tanggal Kegiatan</label>
                            <input type="date" name="tanggal" class="form-control" value="{{ now()->addDays(7)->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Kategori Sasaran</label>
                            <select name="kategori" class="form-select">
                                <option value="Balita">Balita (Tumbuh Kembang &amp; Imunisasi)</option>
                                <option value="Ibu Hamil">Ibu Hamil (Skrining ANC &amp; Senam Hamil)</option>
                                <option value="Terpadu" selected>Terpadu (Balita, Ibu Hamil &amp; Lansia)</option>
                                <option value="Lansia">Lansia (Pemeriksaan PTM)</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Jam Mulai</label>
                            <input type="text" name="jam_mulai" class="form-control" value="08:30" placeholder="08:30">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Jam Selesai</label>
                            <input type="text" name="jam_selesai" class="form-control" value="11:30" placeholder="11:30">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Lokasi Pelaksanaan</label>
                        <input type="text" name="lokasi" class="form-control" value="Balai Warga Utama RW 018" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Bidan / Petugas Penanggung Jawab</label>
                        <input type="text" name="penanggung_jawab" class="form-control" value="Bidan Siti Aminah, Amd.Keb &amp; Kader Posyandu">
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold">Keterangan &amp; Materi Kegiatan</label>
                        <textarea name="keterangan" rows="3" class="form-control" placeholder="Rincian layanan penimbangan, vaksinasi, PMT, atau penyuluhan kesehatan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan &amp; Publikasikan Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==================== MODAL TAMBAH BALITA BARU ==================== -->
<div class="modal fade" id="modalTambahBalita" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 18px;">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="fw-bold modal-title" style="color: var(--nx-forest);">
                    <i class="fas fa-baby me-1"></i> Pendaftaran Balita Baru ke Posyandu
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('posyandu.balita.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label small fw-bold">Nama Lengkap Balita</label>
                            <input type="text" name="nama_anak" class="form-control" placeholder="Contoh: Danendra Pradipta" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-bold">NIK Anak (Jika Ada)</label>
                            <input type="text" name="nik" class="form-control" placeholder="16 digit NIK anak">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select" required>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Golongan Darah</label>
                            <select name="golongan_darah" class="form-select">
                                <option value="">Belum Diketahui</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="AB">AB</option>
                                <option value="O">O</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Ibu Kandung</label>
                            <input type="text" name="nama_ibu" class="form-control" placeholder="Nama ibu" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Ayah</label>
                            <input type="text" name="nama_ayah" class="form-control" placeholder="Nama ayah">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Wilayah RT</label>
                            <select name="rt_number" class="form-select" required>
                                @foreach($rtList as $rt)
                                    <option value="{{ $rt }}">RT {{ $rt }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Alamat Rumah</label>
                            <input type="text" name="alamat" class="form-control" placeholder="Jl. Melati Blok B No. 12 RT 007">
                        </div>
                    </div>
                    <hr class="my-3">
                    <h6 class="fw-bold mb-2 small text-muted"><i class="fas fa-scale-balanced me-1"></i>Pengukuran Pertama Hari Ini</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Berat Badan (kg)</label>
                            <input type="number" step="0.01" name="berat_badan" class="form-control" placeholder="Misal: 8.5" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Tinggi/Panjang Badan (cm)</label>
                            <input type="number" step="0.1" name="tinggi_badan" class="form-control" placeholder="Misal: 72.0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Lingkar Kepala (cm)</label>
                            <input type="number" step="0.1" name="lingkar_kepala" class="form-control" placeholder="Misal: 43.5">
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold">Catatan Khusus / Riwayat Alergi</label>
                        <textarea name="catatan_khusus" rows="2" class="form-control" placeholder="Catatan alergi susu sapi, riwayat lahir prematur, dll..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Daftarkan Balita &amp; Buat KMS</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==================== MODAL TAMBAH IBU HAMIL BARU ==================== -->
<div class="modal fade" id="modalTambahBumil" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 18px;">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="fw-bold modal-title" style="color: var(--nx-forest);">
                    <i class="fas fa-person-pregnant me-1"></i> Pendaftaran Ibu Hamil Baru ke Posyandu
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('posyandu.bumil.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label small fw-bold">Nama Lengkap Ibu Hamil</label>
                            <input type="text" name="nama_ibu" class="form-control" placeholder="Contoh: Ny. Lestari Pradipta" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-bold">NIK Ibu Hamil</label>
                            <input type="text" name="nik" class="form-control" placeholder="16 digit NIK KTP">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Suami</label>
                            <input type="text" name="nama_suami" class="form-control" placeholder="Nama suami pendamping" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nomor Telepon / WhatsApp</label>
                            <input type="text" name="no_telepon" class="form-control" placeholder="081234567890">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Wilayah RT</label>
                            <select name="rt_number" class="form-select" required>
                                @foreach($rtList as $rt)
                                    <option value="{{ $rt }}">RT {{ $rt }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Alamat Rumah</label>
                            <input type="text" name="alamat" class="form-control" placeholder="Jl. Anggrek Blok C No. 5 RT 008">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Usia Kandungan Saat Ini (Minggu)</label>
                            <input type="number" name="usia_kehamilan_minggu" class="form-control" placeholder="Misal: 14" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Kehamilan Ke- (Gravida)</label>
                            <input type="number" name="hamil_ke" class="form-control" value="1" min="1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Golongan Darah</label>
                            <select name="golongan_darah" class="form-select">
                                <option value="">Belum Diketahui</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="AB">AB</option>
                                <option value="O">O</option>
                            </select>
                        </div>
                    </div>
                    <hr class="my-3">
                    <h6 class="fw-bold mb-2 small text-muted"><i class="fas fa-stethoscope me-1"></i>Pemeriksaan Fisik Pertama Hari Ini</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Berat Badan (kg)</label>
                            <input type="number" step="0.1" name="berat_badan" class="form-control" placeholder="Misal: 58.5" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Tekanan Darah (Tensi)</label>
                            <input type="text" name="tekanan_darah" class="form-control" placeholder="Misal: 110/75" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Lingkar Lengan LiLA (cm)</label>
                            <input type="number" step="0.1" name="lila" class="form-control" placeholder="Misal: 25.5" required>
                            <small class="text-muted" style="font-size: 0.7rem;">Batas normal: &ge; 23.5 cm</small>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold">Catatan Awal / Keluhan Ibu</label>
                        <textarea name="solusi" rows="2" class="form-control" placeholder="Keluhan mual, nafsu makan, saran konsumsi tablet tambah darah..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Daftarkan Ibu Hamil &amp; Buat Timeline ANC</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@if($isWarga)
<!-- Modal Info Rekam Medis Keluarga Belum Tersedia -->
<div class="modal fade" id="modalNoFamilyPosyandu" tabindex="-1" aria-labelledby="modalNoFamilyPosyanduLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-body p-4 text-center">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                     style="width: 64px; height: 64px; background: #FEF3C7; color: #D97706;">
                    <i class="fas fa-house-chimney-medical fa-2x"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2" id="modalNoFamilyPosyanduLabel">Rekam Medis Keluarga Belum Tersedia</h5>
                <p class="text-muted small mb-4" style="line-height: 1.6;">
                    Fitur <strong>Rekam Medis Keluarga Saya</strong> tidak tersedia karena belum ada anggota keluarga (ibu hamil atau bayi/balita) yang terdaftar di Posyandu pada Kartu Keluarga Anda. Silakan hubungi kader Posyandu setempat jika ingin melakukan pendaftaran.
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal" style="border-radius: 10px;">
                        Tutup
                    </button>
                    <button type="button" class="btn btn-primary px-4 py-2" data-bs-dismiss="modal" onclick="const btn = document.getElementById('tab-btn-edukasi'); if (btn) new bootstrap.Tab(btn).show();" style="border-radius: 10px;">
                        <i class="fas fa-book-medical me-1"></i> Baca Panduan &amp; Edukasi
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
function handleRekamMedisKeluarga() {
    @if($isWarga)
        @if($anak->count() > 0)
            const btn = document.getElementById('tab-btn-balita');
            if (btn) {
                const trigger = new bootstrap.Tab(btn);
                trigger.show();
                if (window.showNexaToast) {
                    window.showNexaToast('success', 'Membuka pemantauan tumbuh kembang balita dan KMS Digital keluarga Anda.', 'KMS Keluarga');
                }
            }
        @elseif($bumils->count() > 0)
            const btn = document.getElementById('tab-btn-bumil');
            if (btn) {
                const trigger = new bootstrap.Tab(btn);
                trigger.show();
                if (window.showNexaToast) {
                    window.showNexaToast('success', 'Membuka rekam medis pemeriksaan kesehatan kehamilan keluarga Anda.', 'ANC Keluarga');
                }
            }
        @else
            if (window.showNexaToast) {
                window.showNexaToast('warning', 'Belum ada anggota keluarga yang terdaftar pada layanan Posyandu di Kartu Keluarga Anda.', 'Rekam Medis Belum Tersedia');
            }
            const modalEl = document.getElementById('modalNoFamilyPosyandu');
            if (modalEl) {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        @endif
    @else
        const btn = document.getElementById('tab-btn-balita');
        if (btn) {
            const trigger = new bootstrap.Tab(btn);
            trigger.show();
        }
    @endif
}

function switchMainTab(tab) {
    if (tab === 'keluarga') {
        handleRekamMedisKeluarga();
    } else {
        const btn = document.getElementById('tab-btn-' + tab);
        if (btn) {
            const trigger = new bootstrap.Tab(btn);
            trigger.show();
        }
    }
}
</script>
@endpush
