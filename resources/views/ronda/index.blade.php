@extends('layouts.app')

@section('title', 'Pusat Ronda & Keamanan RW 018 — NexaNest')

@section('content')
<div class="nx-page-header">
    <div>
        <span class="badge rounded-pill mb-2 px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
            <i class="fas fa-shield-halved me-1"></i> Keamanan Lingkungan, Siskamling &amp; CCTV Terpadu · RW 018
        </span>
        <h1 class="nx-page-title">Pusat Ronda &amp; Keamanan RW 018</h1>
        <p class="nx-page-subtitle">
            Pusat pengelolaan jadwal ronda, pemantauan keamanan, laporan kejadian, dan CCTV seluruh wilayah RW 018.
        </p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <button type="button" class="btn btn-outline-danger fw-bold" data-bs-toggle="modal" data-bs-target="#modalLaporKeamanan" style="border-radius: 12px;">
            <i class="fas fa-triangle-exclamation me-1"></i> Lapor Kejadian
        </button>
        @if(!$isWarga)
            <button type="button" class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahJadwal" style="border-radius: 12px; background: var(--nx-forest); border-color: var(--nx-forest);">
                <i class="fas fa-plus me-1"></i> Buat Jadwal Ronda
            </button>
            @if($isAdminRw)
                <button type="button" class="btn btn-outline-success fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahCctv" style="border-radius: 12px;">
                    <i class="fas fa-video me-1"></i> Tambah Titik CCTV
                </button>
            @endif
        @endif
    </div>
</div>

<!-- ========================================================================= -->
<!-- 4 KARTU STATISTIK UTAMA (DATABASE DERIVED) -->
<!-- ========================================================================= -->
<div class="row g-3 mb-4">
    <!-- 1. Jadwal Ronda Mendatang -->
    <div class="col-lg-3 col-md-6 col-6">
        <x-stat-card 
            title="Jadwal Ronda Mendatang" 
            value="{{ number_format($totalJadwalMendatang) }}" 
            sub="Shift ronda terjadwal" 
            icon="fas fa-calendar-check" 
            theme="forest" 
        />
    </div>

    <!-- 2. Jadwal Ronda Terlaksana -->
    <div class="col-lg-3 col-md-6 col-6">
        <x-stat-card 
            title="Jadwal Terlaksana" 
            value="{{ number_format($totalJadwalTerlaksana) }}" 
            sub="Patroli siskamling selesai" 
            icon="fas fa-person-military-pointing" 
            theme="primary" 
            valColor="#1F567A" 
        />
    </div>

    <!-- 3. Kejadian Belum Selesai -->
    <div class="col-lg-3 col-md-6 col-6">
        <x-stat-card 
            title="Kejadian Belum Selesai" 
            value="{{ number_format($totalKejadianBelumSelesai) }}" 
            sub="Perlu penanganan/tindak lanjut" 
            icon="fas fa-triangle-exclamation" 
            theme="danger" 
            valColor="var(--nx-danger)" 
        />
    </div>

    <!-- 4. Kasus Selesai -->
    <div class="col-lg-3 col-md-6 col-6">
        <x-stat-card 
            title="Kasus Selesai" 
            value="{{ number_format($totalKejadianSelesai) }}" 
            sub="Penanganan tuntas aman" 
            icon="fas fa-circle-check" 
            theme="success" 
            valColor="#2E7D32" 
        />
    </div>
</div>

<!-- ========================================================================= -->
<!-- RINGKASAN AKTIVITAS & PERINGATAN GANGGUAN -->
<!-- ========================================================================= -->
@if($kameraGangguanList->count() > 0)
    <div class="alert alert-warning border-0 shadow-xs mb-4 d-flex align-items-center justify-content-between py-2.5 px-3.5" style="border-radius: 12px; background: #FFF9E6; border-left: 4px solid #E69500 !important;">
        <div class="d-flex align-items-center gap-2.5">
            <i class="fas fa-video-slash text-warning fa-lg"></i>
            <div>
                <strong class="text-dark small" style="font-size: 0.84rem;">Peringatan Kamera Offline/Gangguan:</strong>
                <span class="small text-muted ms-1" style="font-size: 0.8rem;">
                    Terdapat <strong>{{ $kameraGangguanList->count() }} titik kamera</strong> memerlukan atensi teknis ({{ $kameraGangguanList->pluck('nama_kamera')->take(2)->join(', ') }}).
                </span>
            </div>
        </div>
        <a href="{{ route('ronda.index', ['tab' => 'cctv', 'status' => 'gangguan']) }}" class="btn btn-sm btn-outline-warning text-dark fw-bold px-3 py-1" style="font-size: 0.75rem; border-radius: 8px;">
            Periksa CCTV <i class="fas fa-arrow-right ms-1"></i>
        </a>
    </div>
@endif

<!-- ========================================================================= -->
<!-- NAV TABS 4 BAGIAN UTAMA -->
<!-- ========================================================================= -->
<ul class="nav nav-pills mb-4 gap-2 border-bottom pb-2" id="rondaTabNav">
    <li class="nav-item">
        <a class="nav-link px-3.5 py-2 fw-bold {{ $activeTab === 'jadwal' ? 'active shadow-xs' : 'text-secondary' }}" 
           href="{{ route('ronda.index', ['tab' => 'jadwal', 'rt_number' => $selectedRt]) }}"
           style="border-radius: 10px; font-size: 0.88rem; {{ $activeTab === 'jadwal' ? 'background: var(--nx-forest); color: #fff;' : 'background: #fff;' }}">
            <i class="fas fa-calendar-days me-1.5"></i> 1. Jadwal Ronda
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link px-3.5 py-2 fw-bold {{ $activeTab === 'laporan' ? 'active shadow-xs' : 'text-secondary' }}" 
           href="{{ route('ronda.index', ['tab' => 'laporan', 'rt_number' => $selectedRt]) }}"
           style="border-radius: 10px; font-size: 0.88rem; {{ $activeTab === 'laporan' ? 'background: var(--nx-forest); color: #fff;' : 'background: #fff;' }}">
            <i class="fas fa-file-shield me-1.5"></i> 2. Laporan Kejadian
            @if($totalKejadianBelumSelesai > 0)
                <span class="badge rounded-pill bg-danger ms-1.5 px-2 py-0.5" style="font-size: 0.65rem;">{{ $totalKejadianBelumSelesai }}</span>
            @endif
        </a>
    </li>
    @if(!$isWarga)
    <li class="nav-item">
        <a class="nav-link px-3.5 py-2 fw-bold {{ $activeTab === 'cctv' ? 'active shadow-xs' : 'text-secondary' }}" 
           href="{{ route('ronda.index', ['tab' => 'cctv', 'rt_number' => $selectedRt]) }}"
           style="border-radius: 10px; font-size: 0.88rem; {{ $activeTab === 'cctv' ? 'background: var(--nx-forest); color: #fff;' : 'background: #fff;' }}">
            <i class="fas fa-video me-1.5"></i> 3. CCTV &amp; Dokumentasi
            @if($totalCctvGangguan > 0)
                <span class="badge rounded-pill bg-warning text-dark ms-1.5 px-2 py-0.5" style="font-size: 0.65rem;">{{ $totalCctvGangguan }} Gangguan</span>
            @endif
        </a>
    </li>
    @endif
    <li class="nav-item">
        <a class="nav-link px-3.5 py-2 fw-bold {{ $activeTab === 'evaluasi' ? 'active shadow-xs' : 'text-secondary' }}" 
           href="{{ route('ronda.index', ['tab' => 'evaluasi', 'rt_number' => $selectedRt]) }}"
           style="border-radius: 10px; font-size: 0.88rem; {{ $activeTab === 'evaluasi' ? 'background: var(--nx-forest); color: #fff;' : 'background: #fff;' }}">
            <i class="fas fa-chart-line me-1.5"></i> 4. Riwayat &amp; Evaluasi Keamanan
        </a>
    </li>
</ul>

<!-- ========================================================================= -->
<!-- TAB 1: JADWAL RONDA -->
<!-- ========================================================================= -->
@if($activeTab === 'jadwal')
    <!-- FILTER BAR JADWAL -->
    <div class="card p-3 mb-4 shadow-xs border-0" style="border-radius: 12px; background: #fff;">
        <form method="GET" action="{{ route('ronda.index') }}" class="row g-2 align-items-center">
            <input type="hidden" name="tab" value="jadwal">

            <!-- Search -->
            <div class="col-md-3 col-12">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama petugas, pos, catatan..." value="{{ request('search') }}">
                </div>
            </div>

            <!-- RT Filter (Admin RW can select, Admin RT locked) -->
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
                    <option value="terjadwal" {{ request('status') === 'terjadwal' ? 'selected' : '' }}>Terjadwal</option>
                    <option value="berlangsung" {{ request('status') === 'berlangsung' ? 'selected' : '' }}>Sedang Berlangsung</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai Terlaksana</option>
                    <option value="tidak_terlaksana" {{ request('status') === 'tidak_terlaksana' ? 'selected' : '' }}>Tidak Terlaksana</option>
                </select>
            </div>

            <!-- Bulan & Tahun Filter -->
            <div class="col-md-2 col-6">
                <select name="bulan" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Seluruh Bulan --</option>
                    @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $k => $nama)
                        <option value="{{ $k }}" {{ $selectedBulan == $k ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-1 col-6">
                <select name="tahun" class="form-select form-select-sm" onchange="this.form.submit()">
                    @foreach(['2025', '2026', '2027'] as $y)
                        <option value="{{ $y }}" {{ $selectedTahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2 col-12 text-end d-flex gap-1 justify-content-end">
                <button type="submit" class="btn btn-sm btn-primary fw-bold px-3">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                <a href="{{ route('ronda.index', ['tab' => 'jadwal']) }}" class="btn btn-sm btn-light border text-muted px-2" title="Reset Filter">
                    <i class="fas fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- TABEL DAFTAR JADWAL RONDA (STANDAR NEXANEST) -->
    <div class="card nx-table-card overflow-hidden shadow-xs border-0 mb-4">
        <div class="card-header bg-white p-3.5 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-0 text-dark" style="font-size: 0.96rem;">
                    <i class="fas fa-list-check text-success me-2"></i>Daftar Jadwal Ronda Siskamling Lingkungan
                </h5>
                <small class="text-muted" style="font-size: 0.74rem;">Pengaturan giliran shift keamanan lingkungan warga RW 018</small>
            </div>
            <span class="badge rounded-pill px-3 py-1.5" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-weight: 700; font-size: 0.74rem;">
                Total: {{ $jadwals->total() }} Jadwal Terdata
            </span>
        </div>
        <div class="table-responsive nx-table-responsive">
            <table class="table table-hover align-middle nx-table mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" style="width: 14%;">Tanggal &amp; Hari</th>
                        <th style="width: 8%;">Wilayah</th>
                        <th style="width: 16%;">Shift &amp; Pos Jaga</th>
                        <th style="width: 25%;">Daftar Petugas Ronda</th>
                        <th style="width: 13%;">Status</th>
                        <th style="width: 14%;">Catatan Patroli</th>
                        <th class="pe-4 text-center" style="width: 10%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jadwals as $j)
                        <tr>
                            <td class="ps-3">
                                <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($j->tanggal)->translatedFormat('d M Y') }}</div>
                                <small class="text-muted" style="font-size: 0.7rem;">{{ \Carbon\Carbon::parse($j->tanggal)->translatedFormat('l') }}</small>
                            </td>
                            <td>
                                <span class="badge rounded-pill bg-light text-dark border px-2 py-0.5 fw-bold" style="font-size: 0.72rem;">
                                    RT {{ $j->rt_number }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark" style="font-size: 0.8rem;">{{ $j->shift }}</div>
                                <small class="text-muted d-block text-truncate" style="max-width: 180px;">
                                    <i class="fas fa-map-pin text-danger me-1"></i>{{ $j->lokasi_pos }}
                                </small>
                            </td>
                            <td>
                                <div class="text-dark small" style="line-height: 1.35; max-height: 48px; overflow: hidden;" title="{{ $j->petugas }}">
                                    {{ $j->petugas }}
                                </div>
                                @if($j->koordinator)
                                    <small class="text-muted d-block mt-0.5" style="font-size: 0.7rem;">
                                        <i class="fas fa-user-tie text-success me-1"></i>Koord: <strong>{{ $j->koordinator }}</strong>
                                        @if($j->kontak_koordinator)
                                            · {{ $j->kontak_koordinator }}
                                        @endif
                                    </small>
                                @endif
                            </td>
                            <td>
                                {!! $j->status_badge !!}
                            </td>
                            <td>
                                <small class="text-muted d-block text-truncate" style="max-width: 160px;" title="{{ $j->catatan ?: ($j->laporan_patroli ?: '-') }}">
                                    {{ $j->laporan_patroli ?: ($j->catatan ?: '-') }}
                                </small>
                            </td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light border py-1 px-2 text-muted" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="font-size: 0.82rem;">
                                        <li>
                                            <a class="dropdown-item btn-detail-jadwal" href="javascript:void(0)" data-id="{{ $j->id }}">
                                                <i class="fas fa-eye text-primary me-2"></i> Lihat Detail
                                            </a>
                                        </li>
                                        @if(!$isWarga && ($isAdminRw || ($isAdminRt && Auth::user()->rt_number === $j->rt_number)))
                                            <li>
                                                <a class="dropdown-item btn-status-jadwal" href="javascript:void(0)" data-id="{{ $j->id }}" data-status="{{ $j->status }}" data-laporan="{{ $j->laporan_patroli }}">
                                                    <i class="fas fa-check-to-slot text-warning me-2"></i> Update Pelaksanaan
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item btn-edit-jadwal" href="javascript:void(0)" data-id="{{ $j->id }}">
                                                    <i class="fas fa-pen-to-square text-secondary me-2"></i> Edit Jadwal
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('ronda.destroy_jadwal', $j->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ronda ini?')" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fas fa-trash-can me-2"></i> Hapus Jadwal
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
                            <td colspan="7" class="text-center py-4 text-muted">
                                <div class="py-3">
                                    <i class="fas fa-calendar-xmark fa-2x text-muted mb-2 opacity-50"></i>
                                    <div>Tidak ditemukan jadwal ronda untuk filter yang dipilih.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($jadwals->hasPages())
            <div class="card-footer bg-white p-3 border-top d-flex justify-content-between align-items-center">
                <span class="small text-muted">Menampilkan {{ $jadwals->firstItem() }} - {{ $jadwals->lastItem() }} dari {{ $jadwals->total() }}</span>
                {{ $jadwals->links() }}
            </div>
        @endif
    </div>
@endif

<!-- ========================================================================= -->
<!-- TAB 2: LAPORAN KEJADIAN -->
<!-- ========================================================================= -->
@if($activeTab === 'laporan')
    <!-- FILTER BAR LAPORAN -->
    <div class="card p-3 mb-4 shadow-xs border-0" style="border-radius: 12px; background: #fff;">
        <form method="GET" action="{{ route('ronda.index') }}" class="row g-2 align-items-center">
            <input type="hidden" name="tab" value="laporan">

            <!-- Search -->
            <div class="col-md-3 col-12">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nomor, judul, lokasi..." value="{{ request('search') }}">
                </div>
            </div>

            <!-- RT Filter -->
            @if($isAdminRw)
                <div class="col-md-2 col-6">
                    <select name="rt_number" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Seluruh RT / RW --</option>
                        @foreach($allRts as $rtOpt)
                            <option value="{{ $rtOpt }}" {{ $selectedRt == $rtOpt ? 'selected' : '' }}>RT {{ $rtOpt }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <!-- Kategori Filter -->
            <div class="col-md-2 col-6">
                <select name="kategori" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Semua Kategori --</option>
                    @foreach($kategoriList as $kat)
                        <option value="{{ $kat }}" {{ request('kategori') === $kat ? 'selected' : '' }}>{{ $kat }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div class="col-md-2 col-6">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Semua Alur Status --</option>
                    <option value="baru" {{ request('status') === 'baru' ? 'selected' : '' }}>1. Baru Dilaporkan</option>
                    <option value="menunggu" {{ request('status') === 'menunggu' ? 'selected' : '' }}>2. Menunggu Penanganan</option>
                    <option value="diproses" {{ request('status') === 'diproses' ? 'selected' : '' }}>3. Sedang Ditangani</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>4. Selesai</option>
                </select>
            </div>

            <div class="col-md-3 col-12 text-end d-flex gap-1 justify-content-end">
                <button type="submit" class="btn btn-sm btn-primary fw-bold px-3">
                    <i class="fas fa-filter me-1"></i> Terapkan
                </button>
                <a href="{{ route('ronda.index', ['tab' => 'laporan']) }}" class="btn btn-sm btn-light border text-muted px-2" title="Reset Filter">
                    <i class="fas fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- DAFTAR LAPORAN KEJADIAN (STANDAR NEXANEST) -->
    <div class="card nx-table-card overflow-hidden shadow-xs border-0 mb-4">
        <div class="card-header bg-white p-3.5 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-0 text-dark" style="font-size: 0.96rem;">
                    <i class="fas fa-shield-virus text-danger me-2"></i>Pusat Data Laporan Insiden &amp; Gangguan Keamanan
                </h5>
                <small class="text-muted" style="font-size: 0.74rem;">Catatan gangguan kamtibmas, kehilangan, dan insiden darurat lingkungan</small>
            </div>
            <span class="badge rounded-pill px-3 py-1.5" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-weight: 700; font-size: 0.74rem;">
                Total: {{ $laporanKeamanans->total() }} Laporan Masuk
            </span>
        </div>
        <div class="table-responsive nx-table-responsive">
            <table class="table table-hover align-middle nx-table mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" style="width: 14%;">No. Laporan &amp; Waktu</th>
                        <th style="width: 25%;">Judul Kejadian &amp; Kategori</th>
                        <th style="width: 15%;">Lokasi &amp; CCTV</th>
                        <th style="width: 15%;">Pelapor</th>
                        <th style="width: 13%;">Status</th>
                        <th style="width: 12%;">Petugas</th>
                        <th class="pe-4 text-center" style="width: 6%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporanKeamanans as $lap)
                        <tr>
                            <td class="ps-3">
                                <div class="fw-bold text-dark font-monospace" style="font-size: 0.8rem;">{{ $lap->nomor_laporan ?: 'LAP-00' . $lap->id }}</div>
                                <small class="text-muted" style="font-size: 0.7rem;">
                                    {{ \Carbon\Carbon::parse($lap->waktu_kejadian)->translatedFormat('d M Y - H:i') }} WIB
                                </small>
                            </td>
                            <td>
                                <div class="fw-bold text-dark mb-1" style="font-size: 0.86rem; line-height: 1.3;">{{ $lap->judul }}</div>
                                <span class="badge bg-light text-dark border px-2 py-0.5 fw-semibold" style="font-size: 0.7rem;">
                                    <i class="fas fa-tag text-muted me-1"></i>{{ $lap->kategori }}
                                </span>
                            </td>
                            <td>
                                <div class="small text-dark fw-semibold">
                                    <i class="fas fa-map-pin text-danger me-1"></i>{{ in_array($lap->rt_number, ['RW', '000']) ? 'RW 018' : 'RT ' . $lap->rt_number }}
                                </div>
                                <small class="text-muted d-block text-truncate" style="max-width: 160px;" title="{{ $lap->lokasi }}">
                                    {{ $lap->lokasi }}
                                </small>
                                @if($lap->cctv)
                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-0.5 mt-1" style="font-size: 0.65rem;" title="{{ $lap->cctv->nama_kamera }}">
                                        <i class="fas fa-video me-1"></i>{{ $lap->cctv->kode_kamera }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark small">{{ $lap->nama_pelapor ?: ($lap->user ? $lap->user->name : 'Warga') }}</div>
                                @if($lap->kontak_pelapor)
                                    <small class="text-muted d-block" style="font-size: 0.7rem;">
                                        <i class="fas fa-phone text-muted me-1"></i>{{ $lap->kontak_pelapor }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                {!! $lap->status_badge !!}
                            </td>
                            <td>
                                <small class="text-muted d-block text-truncate" style="max-width: 130px;">
                                    {{ $lap->petugas_penanganan ?: 'Belum ditugaskan' }}
                                </small>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2.5 btn-detail-keamanan" data-id="{{ $lap->id }}" title="Lihat Detail & Tindak Lanjut" style="border-radius: 8px;">
                                    <i class="fas fa-arrow-up-right-from-square"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <div class="py-3">
                                    <i class="fas fa-shield-check fa-2x text-muted mb-2 opacity-50"></i>
                                    <div>Tidak ditemukan laporan kejadian untuk filter yang dipilih. Lingkungan terpantau aman!</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($laporanKeamanans->hasPages())
            <div class="card-footer bg-white p-3 border-top d-flex justify-content-between align-items-center">
                <span class="small text-muted">Menampilkan {{ $laporanKeamanans->firstItem() }} - {{ $laporanKeamanans->lastItem() }} dari {{ $laporanKeamanans->total() }}</span>
                {{ $laporanKeamanans->links() }}
            </div>
        @endif
    </div>
@endif

<!-- ========================================================================= -->
<!-- TAB 3: CCTV & DOKUMENTASI (ADMIN RW & RT ACCESS) -->
<!-- ========================================================================= -->
@if($activeTab === 'cctv' && !$isWarga)
    <!-- RINGKASAN CCTV (4 KARTU) -->
    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-6">
            <div class="card p-3 shadow-xs border h-100" style="border-radius: 14px; background: #fff; border-color: var(--nx-border) !important;">
                <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.68rem;">Total Titik Kamera</span>
                <h4 class="fw-bold mb-0 text-dark">{{ $totalCctv }} Kamera</h4>
                <small class="text-muted" style="font-size: 0.72rem;">Terpasang di area publik RW</small>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="card p-3 shadow-xs border h-100" style="border-radius: 14px; background: #F0F9F3; border-color: rgba(46, 125, 50, 0.2) !important;">
                <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.68rem;">Kamera Online</span>
                <h4 class="fw-bold mb-0 text-success">{{ $totalCctvOnline }} Kamera</h4>
                <small class="text-muted" style="font-size: 0.72rem;">Feed aktif terhubung</small>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="card p-3 shadow-xs border h-100" style="border-radius: 14px; background: #F8FAF8; border-color: rgba(100, 116, 139, 0.2) !important;">
                <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.68rem;">Kamera Offline</span>
                <h4 class="fw-bold mb-0 text-secondary">{{ $totalCctvOffline }} Kamera</h4>
                <small class="text-muted" style="font-size: 0.72rem;">Daya / jaringan terputus</small>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="card p-3 shadow-xs border h-100" style="border-radius: 14px; background: #FFF5F5; border-color: rgba(201, 59, 43, 0.2) !important;">
                <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.68rem;">Mengalami Gangguan</span>
                <h4 class="fw-bold mb-0 text-danger">{{ $totalCctvGangguan }} Kamera</h4>
                <small class="text-muted" style="font-size: 0.72rem;">Perlu perbaikan teknisi</small>
            </div>
        </div>
    </div>

    <!-- FILTER CCTV -->
    <div class="card p-3 mb-4 shadow-xs border-0" style="border-radius: 12px; background: #fff;">
        <form method="GET" action="{{ route('ronda.index') }}" class="row g-2 align-items-center">
            <input type="hidden" name="tab" value="cctv">
            <div class="col-md-4 col-12">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama kamera, lokasi, kode..." value="{{ request('search') }}">
                </div>
            </div>
            @if($isAdminRw)
                <div class="col-md-3 col-6">
                    <select name="rt_number" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Seluruh Wilayah RW 018 --</option>
                        @foreach($allRts as $rtOpt)
                            <option value="{{ $rtOpt }}" {{ $selectedRt == $rtOpt ? 'selected' : '' }}>RT {{ $rtOpt }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="col-md-3 col-6">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Semua Status Kamera --</option>
                    <option value="online" {{ request('status') === 'online' ? 'selected' : '' }}>Online</option>
                    <option value="offline" {{ request('status') === 'offline' ? 'selected' : '' }}>Offline</option>
                    <option value="gangguan" {{ request('status') === 'gangguan' ? 'selected' : '' }}>Mengalami Gangguan</option>
                </select>
            </div>
            <div class="col-md-2 col-12 text-end d-flex justify-content-end gap-1">
                <button type="submit" class="btn btn-sm btn-primary fw-bold px-3">Filter</button>
                <a href="{{ route('ronda.index', ['tab' => 'cctv']) }}" class="btn btn-sm btn-light border text-muted px-2" title="Reset"><i class="fas fa-rotate-left"></i></a>
            </div>
        </form>
    </div>

    <!-- GRID CARD CCTV (STANDAR NEXANEST) -->
    <div class="row g-3 mb-4">
        @forelse($cctvs as $cam)
            <div class="col-lg-4 col-md-6">
                <div class="card nx-grid-card shadow-xs">
                    <div class="nx-grid-card-body">
                        <!-- Header Card -->
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-light text-dark border px-2.5 py-1 font-monospace fw-bold" style="font-size: 0.72rem;">
                                {{ $cam->kode_kamera }}
                            </span>
                            {!! $cam->status_badge !!}
                        </div>

                        <h6 class="fw-bold text-dark mb-1" style="font-size: 0.94rem; line-height: 1.35;">
                            {{ $cam->nama_kamera }}
                        </h6>

                        <div class="small text-muted mb-2" style="font-size: 0.76rem;">
                            <i class="fas fa-map-pin text-danger me-1"></i>{{ $cam->lokasi }} · <strong>{{ in_array($cam->rt_number, ['RW', '000']) ? 'RW 018' : 'RT ' . $cam->rt_number }}</strong>
                        </div>

                        <p class="text-dark mb-3 small" style="font-size: 0.78rem; line-height: 1.4; min-height: 38px; color: #475569;">
                            <i class="fas fa-bullseye text-muted me-1"></i><strong>Area Pantau:</strong> {{ $cam->area_pantau }}
                        </p>

                        <!-- Box Specs & Integrasi -->
                        <div class="p-2.5 rounded-3 bg-light mb-3" style="font-size: 0.74rem; line-height: 1.6;">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Tipe Kamera:</span>
                                <span class="fw-bold text-dark text-truncate" style="max-width: 170px;">{{ $cam->tipe_kamera ?: 'IP Camera Dome' }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">IP Address:</span>
                                <span class="font-monospace text-dark">{{ $cam->ip_address ?: '-' }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Arsip Rekaman:</span>
                                <span class="fw-bold text-primary">{{ $cam->recordings_count }} Arsip Tersedia</span>
                            </div>
                            @if($cam->status === 'gangguan' && $cam->catatan_gangguan)
                                <div class="mt-1 pt-1 border-top text-danger" style="font-size: 0.7rem; line-height: 1.3;">
                                    <i class="fas fa-triangle-exclamation me-1"></i><strong>Kendala:</strong> {{ $cam->catatan_gangguan }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Footer Actions (Aligned Baseline) -->
                    <div class="nx-grid-card-footer">
                        <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2.5 fw-bold btn-view-cctv" data-id="{{ $cam->id }}" style="font-size: 0.76rem; border-radius: 8px;">
                            <i class="fas fa-tv me-1"></i> Feed &amp; Rekaman
                        </button>
                        <div class="d-flex gap-1.5 align-items-center">
                            @if($cam->maps_url)
                                <a href="{{ $cam->maps_url }}" target="_blank" class="nx-action-btn" title="Buka Lokasi di Google Maps">
                                    <i class="fas fa-location-arrow text-danger"></i>
                                </a>
                            @endif
                            <button type="button" class="nx-action-btn btn-status-cctv" data-id="{{ $cam->id }}" data-status="{{ $cam->status }}" data-catatan="{{ $cam->catatan_gangguan }}" title="Ubah Status Kamera">
                                <i class="fas fa-wrench text-secondary"></i>
                            </button>
                            @if($isAdminRw)
                                <button type="button" class="nx-action-btn btn-edit-cctv" data-id="{{ $cam->id }}" title="Edit Info Kamera">
                                    <i class="fas fa-pen-to-square text-secondary"></i>
                                </button>
                                <form action="{{ route('ronda.destroy_cctv', $cam->id) }}" method="POST" onsubmit="return confirm('Hapus kamera CCTV ini?')" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="nx-action-btn text-danger" title="Hapus Kamera">
                                        <i class="fas fa-trash-can"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="fas fa-video-slash fa-3x text-muted mb-2 opacity-50"></i>
                <h6 class="text-muted">Belum ada titik kamera CCTV terdaftar untuk filter ini.</h6>
            </div>
        @endforelse
    </div>
@endif

<!-- ========================================================================= -->
<!-- TAB 4: RIWAYAT & EVALUASI KEAMANAN -->
<!-- ========================================================================= -->
@if($activeTab === 'evaluasi')
    <!-- FILTER PERIODE EVALUASI -->
    <div class="card p-3 mb-4 shadow-xs border-0" style="border-radius: 12px; background: #fff;">
        <form method="GET" action="{{ route('ronda.index') }}" class="row g-2 align-items-center">
            <input type="hidden" name="tab" value="evaluasi">
            <div class="col-md-3 col-6">
                <label class="form-label small fw-bold mb-1">Tahun Evaluasi</label>
                <select name="tahun" class="form-select form-select-sm" onchange="this.form.submit()">
                    @foreach(['2025', '2026', '2027'] as $y)
                        <option value="{{ $y }}" {{ $selectedTahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 col-6">
                <label class="form-label small fw-bold mb-1">Bulan Evaluasi</label>
                <select name="bulan" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Seluruh Bulan (Tahunan) --</option>
                    @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $k => $nama)
                        <option value="{{ $k }}" {{ $selectedBulan == $k ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            @if($isAdminRw)
                <div class="col-md-3 col-6">
                    <label class="form-label small fw-bold mb-1">Wilayah RT</label>
                    <select name="rt_number" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Seluruh Wilayah RW 018 --</option>
                        @foreach($allRts as $rtOpt)
                            <option value="{{ $rtOpt }}" {{ $selectedRt == $rtOpt ? 'selected' : '' }}>RT {{ $rtOpt }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="col-md-3 col-6 text-end pt-3">
                <a href="{{ route('ronda.index', ['tab' => 'evaluasi']) }}" class="btn btn-sm btn-light border text-muted px-3">
                    <i class="fas fa-rotate-left me-1"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <!-- GRAFIK TREN KEJADIAN & KEPATUHAN RONDA -->
    <div class="row g-3 mb-4">
        <!-- Grafik 1: Tren Bulanan Kejadian -->
        <div class="col-lg-8">
            <div class="card p-4 shadow-xs border-0 h-100" style="border-radius: 14px; background: #fff;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="fas fa-chart-line text-success me-2"></i>Tren Insiden Keamanan &amp; Penyelesaian Masalah Tahun {{ $selectedTahun }}
                        </h6>
                        <small class="text-muted" style="font-size: 0.74rem;">Berdasarkan waktu kejadian resmi di database</small>
                    </div>
                </div>
                <div style="height: 250px;">
                    <canvas id="chartTrenKejadian"></canvas>
                </div>
            </div>
        </div>

        <!-- Grafik 2: Komposisi Kejadian berdasarkan Kategori -->
        <div class="col-lg-4">
            <div class="card p-4 shadow-xs border-0 h-100" style="border-radius: 14px; background: #fff;">
                <h6 class="fw-bold mb-1 text-dark">
                    <i class="fas fa-chart-pie text-primary me-2"></i>Distribusi Kategori Kejadian
                </h6>
                <small class="text-muted d-block mb-3" style="font-size: 0.74rem;">Proporsi tipe gangguan lingkungan</small>
                <div style="height: 220px;">
                    <canvas id="chartKategoriDoughnut"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- REKAPITULASI KEAMANAN PER RT (TABEL INDIKATOR) -->
    <div class="card overflow-hidden shadow-xs border-0 mb-4" style="border-radius: 14px;">
        <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="fas fa-table text-success me-2"></i>Matriks Evaluasi Kinerja Keamanan &amp; Siskamling Antar-RT
            </h6>
            <small class="text-muted" style="font-size: 0.74rem;">Evaluasi ketaatan siskamling dan respon insiden</small>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.84rem;">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Wilayah</th>
                        <th class="text-center">Jadwal Terdaftar</th>
                        <th class="text-center">Ronda Terlaksana</th>
                        <th class="text-center">Tidak Terlaksana</th>
                        <th class="text-center">Tingkat Kepatuhan</th>
                        <th class="text-center">Insiden Dilaporkan</th>
                        <th class="text-center">Selesai Ditangani</th>
                        <th class="text-center">Titik CCTV</th>
                        <th class="text-center">Status Wilayah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekapKeamananRt as $rtCode => $rk)
                        <tr>
                            <td class="ps-3 fw-bold text-dark">
                                <span class="badge bg-light text-dark border px-2.5 py-1">RT {{ $rtCode }}</span>
                            </td>
                            <td class="text-center">{{ number_format($rk['total_jadwal']) }}</td>
                            <td class="text-center text-success fw-bold">{{ number_format($rk['terlaksana']) }}</td>
                            <td class="text-center text-danger">{{ number_format($rk['tidak_terlaksana']) }}</td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 6px; max-width: 80px;">
                                        <div class="progress-bar {{ $rk['kepatuhan_persen'] >= 85 ? 'bg-success' : ($rk['kepatuhan_persen'] >= 70 ? 'bg-warning' : 'bg-danger') }}" 
                                             role="progressbar" style="width: {{ $rk['kepatuhan_persen'] }}%;"></div>
                                    </div>
                                    <span class="small fw-bold">{{ $rk['kepatuhan_persen'] }}%</span>
                                </div>
                            </td>
                            <td class="text-center fw-bold">{{ number_format($rk['total_insiden']) }}</td>
                            <td class="text-center text-success fw-bold">{{ number_format($rk['insiden_selesai']) }}</td>
                            <td class="text-center">{{ number_format($rk['cctv_count']) }}</td>
                            <td class="text-center">
                                <span class="badge bg-{{ $rk['kondisi_badge'] }}-subtle text-{{ $rk['kondisi_badge'] }} border border-{{ $rk['kondisi_badge'] }}-subtle px-2 py-0.5 fw-bold">
                                    {{ $rk['kondisi'] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- RIWAYAT AUDIT & AKTIVITAS KEAMANAN -->
    <div class="card p-3 shadow-xs border-0" style="border-radius: 14px;">
        <h6 class="fw-bold mb-3 text-dark">
            <i class="fas fa-clock-rotate-left text-muted me-2"></i>Log Aktivitas Pengelolaan Keamanan &amp; Siskamling Terbaru
        </h6>
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.78rem;">
                <thead class="table-light">
                    <tr>
                        <th class="ps-2">Waktu</th>
                        <th>Pengguna</th>
                        <th>Modul</th>
                        <th>Aksi</th>
                        <th>Deskripsi Aktivitas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentSecurityLogs as $log)
                        <tr>
                            <td class="ps-2 text-muted">{{ $log->created_at->translatedFormat('d M Y, H:i') }}</td>
                            <td class="fw-bold text-dark">{{ $log->user_name }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $log->module }}</span></td>
                            <td><span class="badge bg-secondary-subtle text-secondary">{{ strtoupper($log->event) }}</span></td>
                            <td class="text-muted">{{ $log->description }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-3 text-muted">Belum ada riwayat aktivitas keamanan tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif

<!-- ========================================================================= -->
<!-- MODAL INTERAKTIF SISTEM RONDA & KEAMANAN -->
<!-- ========================================================================= -->

<!-- 1. MODAL TAMBAH JADWAL RONDA -->
<div class="modal fade" id="modalTambahJadwal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form action="{{ route('ronda.store_jadwal') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="font-size: 1.05rem; color: var(--nx-forest);">
                        <i class="fas fa-calendar-plus me-2"></i>Buat Jadwal Ronda Siskamling
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Wilayah RT <span class="text-danger">*</span></label>
                            @if($isAdminRt)
                                <input type="text" class="form-control" value="RT {{ Auth::user()->rt_number }}" readonly>
                                <input type="hidden" name="rt_number" value="{{ Auth::user()->rt_number }}">
                            @else
                                <select name="rt_number" class="form-select" required>
                                    @foreach($allRts as $rtOpt)
                                        <option value="{{ $rtOpt }}">RT {{ $rtOpt }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Tanggal Ronda <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Shift Waktu Jaga <span class="text-danger">*</span></label>
                            <select name="shift" class="form-select" required>
                                <option value="Malam (22:00 - 04:00 WIB)">Malam (22:00 - 04:00 WIB)</option>
                                <option value="Shift 1 (20:00 - 00:00 WIB)">Shift 1 (20:00 - 00:00 WIB)</option>
                                <option value="Shift 2 (00:00 - 04:00 WIB)">Shift 2 (00:00 - 04:00 WIB)</option>
                                <option value="Pagi/Subuh (04:00 - 06:00 WIB)">Pagi/Subuh (04:00 - 06:00 WIB)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Lokasi Pos / Titik Kumpul <span class="text-danger">*</span></label>
                            <input type="text" name="lokasi_pos" class="form-control" placeholder="Contoh: Pos Ronda RT 006 / Depan Balai" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Koordinator Piket &amp; Kontak</label>
                            <div class="input-group">
                                <input type="text" name="koordinator" class="form-control" placeholder="Nama Koordinator">
                                <input type="text" name="kontak_koordinator" class="form-control" placeholder="No HP/WA">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Daftar Nama Petugas Ronda <span class="text-danger">*</span></label>
                        <textarea name="petugas" class="form-control" rows="3" placeholder="Masukkan nama-nama warga/petugas yang bertugas ronda malam ini (pisahkan dengan koma)..." required></textarea>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold">Catatan Arahan / Perhatian Khusus (Opsional)</label>
                        <input type="text" name="catatan" class="form-control" placeholder="Contoh: Fokus patroli gerbang samping dan portal lorong utara...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">Simpan Jadwal Ronda</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 2. MODAL LAPOR KEJADIAN KEAMANAN (WARGA & ADMIN) -->
<div class="modal fade" id="modalLaporKeamanan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form action="{{ route('ronda.store_laporan_keamanan') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="font-size: 1.05rem; color: #c93b2b;">
                        <i class="fas fa-triangle-exclamation me-2"></i>Form Laporan Kejadian Keamanan Lingkungan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Judul Laporan Kejadian <span class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control" placeholder="Contoh: Lampu penerangan jalan mati / Upaya pembobolan pagar..." required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Kategori Kejadian <span class="text-danger">*</span></label>
                            <select name="kategori" class="form-select" required>
                                @foreach($kategoriList as $kat)
                                    <option value="{{ $kat }}">{{ $kat }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Wilayah RT <span class="text-danger">*</span></label>
                            <select name="rt_number" class="form-select" required>
                                <option value="RW">Pusat RW / Gerbang Umum</option>
                                @foreach($allRts as $rtOpt)
                                    <option value="{{ $rtOpt }}" {{ Auth::user()->rt_number == $rtOpt ? 'selected' : '' }}>RT {{ $rtOpt }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Waktu Kejadian <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="waktu_kejadian" class="form-control" value="{{ date('Y-m-d\TH:i') }}" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-bold">Lokasi Rinci Kejadian <span class="text-danger">*</span></label>
                            <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Jl. Melati Blok B No. 12 Depan Pos Ronda" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Pelapor</label>
                            <input type="text" name="nama_pelapor" class="form-control" value="{{ Auth::user()->name }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nomor Kontak / WhatsApp</label>
                            <input type="text" name="kontak_pelapor" class="form-control" placeholder="Contoh: 0812-xxxx-xxxx">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Titik CCTV Terdekat / Terkait (Opsional)</label>
                        <select name="cctv_id" class="form-select">
                            <option value="">-- Tidak Terkait / Belum Tahu --</option>
                            @foreach($allCctvs as $cCam)
                                <option value="{{ $cCam->id }}">{{ $cCam->kode_kamera }} - {{ $cCam->nama_kamera }} ({{ in_array($cCam->rt_number, ['RW', '000']) ? 'RW 018' : 'RT ' . $cCam->rt_number }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Kronologi &amp; Rincian Kejadian <span class="text-danger">*</span></label>
                        <textarea name="kronologi" class="form-control" rows="3" placeholder="Ceritakan urutan kejadian secara jelas dan objektif..." required></textarea>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold">Foto Dokumentasi / Bukti Pendukung (Opsional)</label>
                        <input type="file" name="dokumentasi" class="form-control" accept="image/*">
                        <small class="text-muted" style="font-size: 0.72rem;">Format: JPG, PNG, WEBP (Maksimal 4 MB).</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm px-4 fw-bold">Kirim Laporan Kejadian</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 3. MODAL DETAIL & TINDAK LANJUT LAPORAN KEAMANAN -->
<div class="modal fade" id="modalDetailKeamanan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <span class="badge bg-light text-dark border px-2 py-0.5 font-monospace fw-bold mb-1" id="detailLapNo">-</span>
                    <h5 class="modal-title fw-bold text-dark" id="detailLapJudul" style="font-size: 1.05rem;">-</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 bg-light h-100 small" style="line-height: 1.8;">
                            <div><strong>Waktu Kejadian:</strong> <span id="detailLapWaktu">-</span></div>
                            <div><strong>Wilayah:</strong> <span id="detailLapRt" class="badge bg-light text-dark border">-</span></div>
                            <div><strong>Lokasi:</strong> <span id="detailLapLokasi">-</span></div>
                            <div><strong>Pelapor:</strong> <span id="detailLapPelapor">-</span> (<span id="detailLapKontak">-</span>)</div>
                            <div><strong>Status Saat Ini:</strong> <span id="detailLapStatusBadge">-</span></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 bg-light h-100 small" style="line-height: 1.8;">
                            <div><strong>Petugas Penanggung Jawab:</strong> <span id="detailLapPetugas" class="fw-bold text-primary">-</span></div>
                            <div><strong>Kamera CCTV Terkait:</strong> <span id="detailLapCctv" class="fw-bold">-</span></div>
                            <div><strong>Waktu Selesai:</strong> <span id="detailLapSelesaiAt">-</span></div>
                            <div class="mt-1"><strong>Catatan Tindak Lanjut:</strong></div>
                            <p class="text-muted mb-0 small" id="detailLapTindakLanjut" style="font-size: 0.78rem;">-</p>
                        </div>
                    </div>
                </div>

                <!-- Kronologi -->
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark">Kronologi Kejadian:</label>
                    <div class="p-3 rounded-2 border bg-white small" id="detailLapKronologi" style="line-height: 1.5;"></div>
                </div>

                <!-- Foto Bukti -->
                <div class="row g-3 mb-3" id="boxDetailFoto" style="display: none;">
                    <div class="col-md-6" id="boxFotoKejadian">
                        <label class="form-label small fw-bold text-muted">Dokumentasi Awal:</label>
                        <div>
                            <img src="" id="imgDetailKejadian" class="img-fluid rounded border shadow-xs" style="max-height: 180px; object-fit: cover;" alt="Bukti Kejadian">
                        </div>
                    </div>
                    <div class="col-md-6" id="boxFotoPenanganan">
                        <label class="form-label small fw-bold text-muted">Dokumentasi Penanganan/Selesai:</label>
                        <div>
                            <img src="" id="imgDetailPenanganan" class="img-fluid rounded border shadow-xs" style="max-height: 180px; object-fit: cover;" alt="Bukti Penanganan">
                        </div>
                    </div>
                </div>

                <!-- Timeline Riwayat Status -->
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark"><i class="fas fa-timeline text-muted me-1"></i>Riwayat Perubahan Status &amp; Catatan Penanganan:</label>
                    <div class="p-3 rounded-2 border bg-light" id="boxTimelineHistories" style="max-height: 160px; overflow-y: auto;">
                        <ul class="list-unstyled mb-0" id="listHistories" style="font-size: 0.78rem;"></ul>
                    </div>
                </div>

                @if(!$isWarga)
                    <!-- FORM UPDATE STATUS & PENUGASAN (PENGURUS) -->
                    <div id="containerFormTindakLanjut" class="p-3 rounded-3 border-top bg-white mt-3">
                        <h6 class="fw-bold text-dark mb-2" style="font-size: 0.88rem;">
                            <i class="fas fa-user-shield text-success me-1"></i>Tindak Lanjut &amp; Perubahan Status Petugas
                        </h6>
                        <form id="formTindakLanjut" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-2 mb-2">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Ubah Status Alur</label>
                                    <select name="status" id="tlStatusSelect" class="form-select form-select-sm" required>
                                        <option value="baru">1. Baru Dilaporkan</option>
                                        <option value="menunggu">2. Menunggu Penanganan</option>
                                        <option value="diproses">3. Sedang Ditangani</option>
                                        <option value="selesai">4. Selesai Ditangani</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Petugas Penanggung Jawab</label>
                                    <input type="text" name="petugas_penanganan" id="tlPetugasInput" class="form-control form-control-sm" placeholder="Nama Petugas">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Tautkan Kamera CCTV</label>
                                    <select name="cctv_id" id="tlCctvSelect" class="form-select form-select-sm">
                                        <option value="">-- Tanpa Tautan CCTV --</option>
                                        @foreach($allCctvs as $cCam)
                                            <option value="{{ $cCam->id }}">{{ $cCam->kode_kamera }} ({{ in_array($cCam->rt_number, ['RW', '000']) ? 'RW 018' : 'RT ' . $cCam->rt_number }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row g-2 mb-2">
                                <div class="col-md-8">
                                    <label class="form-label small fw-bold">Catatan Tindak Lanjut</label>
                                    <input type="text" name="tindak_lanjut" id="tlCatatanInput" class="form-control form-control-sm" placeholder="Langkah konkrit yang telah / sedang diambil...">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Foto Hasil Penanganan</label>
                                    <input type="file" name="dokumentasi_penanganan" class="form-control form-control-sm" accept="image/*">
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-sm btn-primary fw-bold px-3">
                                    <i class="fas fa-check me-1"></i> Simpan Tindak Lanjut
                                </button>
                            </div>
                        </form>
                    </div>
                    <div id="alertTindakLanjutRestricted" class="alert alert-warning d-flex align-items-center mt-3 mb-0" style="display: none; border-radius: 10px;">
                        <i class="fas fa-shield-halved me-2 fs-5 text-warning"></i>
                        <div style="font-size: 0.82rem;">
                            <strong>Kewenangan Terbatas:</strong> Laporan kejadian tingkat RW atau wilayah RT lain hanya dapat ditindaklanjuti dan diselesaikan oleh <strong>Admin RW</strong>.
                        </div>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- 4. MODAL DETAIL CCTV, LIVE FEED & REKAMAN NVR -->
<div class="modal fade" id="modalDetailCctv" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <span class="badge bg-light text-dark border px-2 py-0.5 font-monospace fw-bold mb-1" id="cctvModalKode">-</span>
                    <h5 class="modal-title fw-bold text-dark" id="cctvModalNama" style="font-size: 1.05rem;">-</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <!-- LIVE FEED PLAYER SIMULATION CONTAINER -->
                <div class="p-3 rounded-3 text-white mb-3 position-relative overflow-hidden" 
                     style="background: #111827; min-height: 230px; display: flex; flex-direction: column; justify-content: space-between; border: 2px solid #374151;">
                    <!-- Overlay Top -->
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-danger px-2 py-0.5" id="cctvFeedStatusBadge">
                            <i class="fas fa-circle me-1 fa-fade"></i> LIVE STREAM
                        </span>
                        <div class="font-monospace small opacity-75" id="cctvFeedTimestamp">
                            {{ date('Y-m-d H:i:s') }} WIB
                        </div>
                    </div>

                    <!-- Watermark / Status Message -->
                    <div class="text-center py-4 my-auto">
                        <div id="cctvOnlineView">
                            <i class="fas fa-video fa-2x text-success mb-2 opacity-75"></i>
                            <div class="fw-bold font-monospace" style="font-size: 0.92rem; color: #10B981;">STREAM TERHUBUNG KE NVR POSKO RW 018</div>
                            <small class="text-muted d-block font-monospace" id="cctvStreamIpInfo">RTSP protocol active · 25 FPS · H.265</small>
                            <small class="text-secondary d-block mt-1" style="font-size: 0.72rem;">Struktur integrasi siap dikembangkan ke IP camera / video gateway server.</small>
                        </div>
                        <div id="cctvOfflineView" style="display: none;">
                            <i class="fas fa-video-slash fa-2x text-danger mb-2 opacity-75"></i>
                            <div class="fw-bold font-monospace text-danger" style="font-size: 0.92rem;">PERANGKAT OFFLINE / DALAM PERBAIKAN</div>
                            <small class="text-muted d-block" id="cctvOfflineMessage">Sinyal RTSP tidak terdeteksi dari unit kamera.</small>
                        </div>
                    </div>

                    <!-- Overlay Bottom -->
                    <div class="d-flex justify-content-between align-items-center small font-monospace opacity-75 pt-2 border-top border-secondary">
                        <span id="cctvFeedAreaInfo">-</span>
                        <span id="cctvFeedResolution">1080p Full HD</span>
                    </div>
                </div>

                <!-- INFO TEKNIS & MAPS -->
                <div class="row g-2 mb-3 small">
                    <div class="col-md-4">
                        <div class="p-2.5 rounded-2 bg-light">
                            <span class="text-muted d-block">Wilayah RT:</span>
                            <strong class="text-dark" id="cctvModalRt">-</strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2.5 rounded-2 bg-light">
                            <span class="text-muted d-block">IP Address:</span>
                            <strong class="font-monospace text-dark" id="cctvModalIp">-</strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2.5 rounded-2 bg-light">
                            <span class="text-muted d-block">Kapasitas NVR:</span>
                            <strong class="text-dark" id="cctvModalNvr">-</strong>
                        </div>
                    </div>
                </div>

                <!-- DAFTAR REKAMAN TERSEDIA -->
                <div>
                    <h6 class="fw-bold text-dark mb-2" style="font-size: 0.88rem;">
                        <i class="fas fa-film text-danger me-1"></i>Arsip Rekaman CCTV Tersimpan (NVR)
                    </h6>
                    <div class="table-responsive border rounded-2" style="max-height: 180px; overflow-y: auto;">
                        <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.78rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-2">Judul Rekaman</th>
                                    <th>Waktu Mulai - Selesai</th>
                                    <th>Ukuran</th>
                                    <th>Status Arsip</th>
                                    <th class="text-center">Kunci Bukti</th>
                                </tr>
                            </thead>
                            <tbody id="cctvRecordingsList"></tbody>
                        </table>
                    </div>
                    <small class="text-muted d-block mt-1.5" style="font-size: 0.7rem;">
                        <i class="fas fa-lock me-1 text-warning"></i>Rekaman yang dikunci (Evidence Lock) dipertahankan permanen dan dikecualikan dari pembersihan retensi berkala.
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <a href="" id="btnModalMapsCctv" target="_blank" class="btn btn-outline-danger btn-sm fw-bold">
                    <i class="fas fa-map-location-dot me-1"></i> Buka Titik Maps
                </a>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- 5. MODAL TAMBAH CCTV (ADMIN RW ONLY) -->
@if($isAdminRw)
<div class="modal fade" id="modalTambahCctv" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form action="{{ route('ronda.store_cctv') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="font-size: 1.05rem; color: var(--nx-forest);">
                        <i class="fas fa-video me-2"></i>Tambah Titik Kamera CCTV Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Kode Kamera <span class="text-danger">*</span></label>
                            <input type="text" name="kode_kamera" class="form-control" placeholder="Contoh: CCTV-018-006-002" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-bold">Nama Kamera <span class="text-danger">*</span></label>
                            <input type="text" name="nama_kamera" class="form-control" placeholder="Contoh: CCTV Simpang Pos Kamling RT 006" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Wilayah RT <span class="text-danger">*</span></label>
                            <select name="rt_number" class="form-select" required>
                                <option value="RW">Pusat RW / Gerbang</option>
                                @foreach($allRts as $rtOpt)
                                    <option value="{{ $rtOpt }}">RT {{ $rtOpt }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Lokasi Pemasangan <span class="text-danger">*</span></label>
                            <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Tiang Lampu Depan Pos Ronda RT 006" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Area Pantau Kamera <span class="text-danger">*</span></label>
                            <input type="text" name="area_pantau" class="form-control" placeholder="Contoh: Akses simpang jalan dan halaman pos kamling" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Status Awal</label>
                            <select name="status" class="form-select" required>
                                <option value="online">Online</option>
                                <option value="offline">Offline</option>
                                <option value="gangguan">Mengalami Gangguan</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Tipe Kamera</label>
                            <input type="text" name="tipe_kamera" class="form-control" value="Hikvision Bullet 2MP Weatherproof">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">IP Address</label>
                            <input type="text" name="ip_address" class="form-control" placeholder="192.168.10.xxx">
                        </div>
                    </div>

                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Kapasitas NVR / Retensi</label>
                            <input type="text" name="kapasitas_penyimpanan" class="form-control" value="2 TB NVR (Retensi 30 Hari)">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">URL Google Maps</label>
                            <input type="text" name="maps_url" class="form-control" placeholder="https://maps.google.com/?q=...">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">Simpan Kamera CCTV</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 6. MODAL EDIT CCTV (ADMIN RW ONLY) -->
<div class="modal fade" id="modalEditCctv" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form id="formEditCctv" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="font-size: 1.05rem; color: var(--nx-forest);">
                        <i class="fas fa-pen-to-square me-2"></i>Edit Informasi Kamera CCTV
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Nama Kamera <span class="text-danger">*</span></label>
                            <input type="text" name="nama_kamera" id="editCctvNama" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Wilayah RT <span class="text-danger">*</span></label>
                            <select name="rt_number" id="editCctvRt" class="form-select" required>
                                <option value="RW">Pusat RW / Gerbang</option>
                                @foreach($allRts as $rtOpt)
                                    <option value="{{ $rtOpt }}">RT {{ $rtOpt }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Lokasi Pemasangan <span class="text-danger">*</span></label>
                            <input type="text" name="lokasi" id="editCctvLokasi" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Area Pantau <span class="text-danger">*</span></label>
                            <input type="text" name="area_pantau" id="editCctvArea" class="form-control" required>
                        </div>
                    </div>
                    <div class="row g-3 mb-2">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">IP Address</label>
                            <input type="text" name="ip_address" id="editCctvIp" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Tipe Kamera</label>
                            <input type="text" name="tipe_kamera" id="editCctvTipe" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">URL Google Maps</label>
                            <input type="text" name="maps_url" id="editCctvMaps" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- 7. MODAL UBAH STATUS CCTV -->
<div class="modal fade" id="modalStatusCctv" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formStatusCctv" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="font-size: 1rem; color: var(--nx-forest);">
                        <i class="fas fa-wrench me-2"></i>Update Status Operasional CCTV
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Status Koneksi Kamera <span class="text-danger">*</span></label>
                        <select name="status" id="cctvStatusSelect" class="form-select" required>
                            <option value="online">Online (Terhubung Normal)</option>
                            <option value="offline">Offline (Daya Dimatikan / Pemeliharaan)</option>
                            <option value="gangguan">Mengalami Gangguan (Kabel Kendor, Lensa Buram, dll)</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Catatan Kendala / Rincian Gangguan</label>
                        <textarea name="catatan_gangguan" id="cctvCatatanGangguan" class="form-control" rows="3" placeholder="Jelaskan kendala teknis atau perbaikan yang sedang berjalan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">Simpan Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 8. MODAL UBAH STATUS PELAKSANAAN JADWAL RONDA -->
<div class="modal fade" id="modalUpdateStatusJadwal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formStatusJadwal" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="font-size: 1rem; color: var(--nx-forest);">
                        <i class="fas fa-check-to-slot me-2"></i>Update Pelaksanaan Jadwal Ronda
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Status Pelaksanaan <span class="text-danger">*</span></label>
                        <select name="status" id="modalJadwalStatusVal" class="form-select" required>
                            <option value="terjadwal">Terjadwal</option>
                            <option value="berlangsung">Sedang Berlangsung</option>
                            <option value="selesai">Selesai Terlaksana</option>
                            <option value="tidak_terlaksana">Tidak Terlaksana</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Laporan Ringkas Patroli / Hasil Ronda</label>
                        <textarea name="laporan_patroli" id="modalJadwalLaporanVal" class="form-control" rows="3" placeholder="Contoh: Patroli keliling 3 putaran selesai, lingkungan aman, pintu portal terkunci..."></textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Dokumentasi Patroli (Opsional)</label>
                        <input type="file" name="dokumentasi_patroli" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">Simpan Pelaksanaan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 9. MODAL DETAIL JADWAL RONDA -->
<div class="modal fade" id="modalDetailJadwal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" style="font-size: 1rem; color: var(--nx-forest);">
                    <i class="fas fa-calendar-day me-2"></i>Rincian Jadwal Ronda Siskamling
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="p-3 rounded-3 bg-light mb-3 small" style="line-height: 1.8;">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Tanggal:</span>
                        <strong class="text-dark" id="detJadwalTgl">-</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Wilayah:</span>
                        <span class="badge bg-light text-dark border" id="detJadwalRt">-</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Shift Waktu:</span>
                        <strong class="text-dark" id="detJadwalShift">-</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Pos Ronda:</span>
                        <span class="text-dark" id="detJadwalPos">-</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Status:</span>
                        <span id="detJadwalStatusBadge">-</span>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark">Daftar Petugas Ronda:</label>
                    <div class="p-2.5 rounded-2 border bg-white small" id="detJadwalPetugas" style="line-height: 1.5;">-</div>
                </div>

                <div class="mb-2">
                    <label class="form-label small fw-bold text-dark">Laporan / Catatan Pelaksanaan:</label>
                    <div class="p-2.5 rounded-2 border bg-white small text-muted" id="detJadwalCatatan" style="line-height: 1.5;">-</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- CHART.JS SCRIPTS & INTERACTION LOGIC -->
<!-- ========================================================================= -->
@if($activeTab === 'evaluasi')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Chart Tren Kejadian Bulanan
    const ctxTren = document.getElementById('chartTrenKejadian');
    if (ctxTren) {
        new Chart(ctxTren.getContext('2d'), {
            type: 'bar',
            data: {
                labels: @json($chartMonths),
                datasets: [
                    {
                        label: 'Insiden Dilaporkan',
                        data: @json($chartKejadianBulanan),
                        backgroundColor: '#c93b2b',
                        borderRadius: 6,
                    },
                    {
                        label: 'Kasus Selesai',
                        data: @json($chartKejadianSelesaiBulanan),
                        backgroundColor: '#285943',
                        borderRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }

    // 2. Chart Komposisi Kategori Kejadian (Doughnut)
    const ctxDoughnut = document.getElementById('chartKategoriDoughnut');
    if (ctxDoughnut) {
        const rawKat = @json($kategoriDistribution);
        const labels = Object.keys(rawKat);
        const dataVals = Object.values(rawKat);
        const bgColors = ['#285943', '#1F567A', '#c93b2b', '#E69500', '#8b5cf6', '#06b6d4', '#64748b'];

        new Chart(ctxDoughnut.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: labels.length > 0 ? labels : ['Belum Ada Insiden'],
                datasets: [{
                    data: dataVals.length > 0 ? dataVals : [1],
                    backgroundColor: dataVals.length > 0 ? bgColors.slice(0, labels.length) : ['#e2e8f0'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } }
                }
            }
        });
    }
});
</script>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Detail Jadwal Ronda Modal
    document.querySelectorAll('.btn-detail-jadwal').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            fetch(`/ronda/jadwal/${id}`)
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        const d = res.data;
                        document.getElementById('detJadwalTgl').textContent = d.tanggal_formatted;
                        document.getElementById('detJadwalRt').textContent = 'RT ' + d.rt_number;
                        document.getElementById('detJadwalShift').textContent = d.shift;
                        document.getElementById('detJadwalPos').textContent = d.lokasi_pos;
                        document.getElementById('detJadwalStatusBadge').innerHTML = d.status_badge;
                        document.getElementById('detJadwalPetugas').textContent = d.petugas;
                        document.getElementById('detJadwalCatatan').textContent = d.laporan_patroli || d.catatan || 'Tidak ada catatan pelaksanaan khusus.';

                        new bootstrap.Modal(document.getElementById('modalDetailJadwal')).show();
                    }
                });
        });
    });

    // 2. Status Jadwal Ronda Modal
    document.querySelectorAll('.btn-status-jadwal').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const status = this.getAttribute('data-status');
            const laporan = this.getAttribute('data-laporan') || '';

            const form = document.getElementById('formStatusJadwal');
            form.action = `/ronda/jadwal/${id}/status`;
            document.getElementById('modalJadwalStatusVal').value = status;
            document.getElementById('modalJadwalLaporanVal').value = laporan;

            new bootstrap.Modal(document.getElementById('modalUpdateStatusJadwal')).show();
        });
    });

    // 3. Detail & Tindak Lanjut Laporan Keamanan
    document.querySelectorAll('.btn-detail-keamanan').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            fetch(`/ronda/keamanan/${id}`)
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        const d = res.data;
                        document.getElementById('detailLapNo').textContent = d.nomor_laporan;
                        document.getElementById('detailLapJudul').textContent = d.judul;
                        document.getElementById('detailLapWaktu').textContent = d.waktu_kejadian_formatted;
                        document.getElementById('detailLapRt').textContent = (d.rt_number === 'RW' || d.rt_number === '000') ? 'RW 018 (Tingkat RW)' : 'RT ' + d.rt_number;
                        document.getElementById('detailLapLokasi').textContent = d.lokasi;
                        document.getElementById('detailLapPelapor').textContent = d.nama_pelapor;
                        document.getElementById('detailLapKontak').textContent = d.kontak_pelapor;
                        document.getElementById('detailLapStatusBadge').innerHTML = d.status_badge;
                        document.getElementById('detailLapPetugas').textContent = d.petugas_penanganan;
                        document.getElementById('detailLapCctv').textContent = d.cctv ? `${d.cctv.kode_kamera} - ${d.cctv.nama_kamera}` : 'Tidak ditautkan CCTV';
                        document.getElementById('detailLapSelesaiAt').textContent = d.selesai_at || '-';
                        document.getElementById('detailLapTindakLanjut').textContent = d.tindak_lanjut;
                        document.getElementById('detailLapKronologi').textContent = d.kronologi;

                        // Foto box
                        const boxFoto = document.getElementById('boxDetailFoto');
                        let hasFoto = false;
                        if (d.dokumentasi_url) {
                            document.getElementById('imgDetailKejadian').src = d.dokumentasi_url;
                            document.getElementById('boxFotoKejadian').style.display = 'block';
                            hasFoto = true;
                        } else {
                            document.getElementById('boxFotoKejadian').style.display = 'none';
                        }
                        if (d.dokumentasi_penanganan_url) {
                            document.getElementById('imgDetailPenanganan').src = d.dokumentasi_penanganan_url;
                            document.getElementById('boxFotoPenanganan').style.display = 'block';
                            hasFoto = true;
                        } else {
                            document.getElementById('boxFotoPenanganan').style.display = 'none';
                        }
                        boxFoto.style.display = hasFoto ? 'flex' : 'none';

                        // Timeline histories
                        const listHist = document.getElementById('listHistories');
                        listHist.innerHTML = '';
                        if (d.histories && d.histories.length > 0) {
                            d.histories.forEach(h => {
                                const li = document.createElement('li');
                                li.className = 'mb-1 pb-1 border-bottom';
                                li.innerHTML = `<span class="badge bg-secondary-subtle text-secondary me-1.5 uppercase">${h.status_baru}</span> <strong>${h.petugas}</strong>: ${h.keterangan} <span class="text-muted float-end">${h.time_formatted}</span>`;
                                listHist.appendChild(li);
                            });
                        } else {
                            listHist.innerHTML = '<li class="text-muted">Belum ada riwayat tercatat.</li>';
                        }

                        // Form action & authorization check
                        const containerFormTl = document.getElementById('containerFormTindakLanjut');
                        const alertRestricted = document.getElementById('alertTindakLanjutRestricted');
                        const formTl = document.getElementById('formTindakLanjut');

                        if (containerFormTl) {
                            if (d.can_edit) {
                                containerFormTl.style.display = 'block';
                                if (alertRestricted) alertRestricted.style.display = 'none';
                                if (formTl) {
                                    formTl.action = `/ronda/keamanan/${d.id}/update`;
                                    document.getElementById('tlStatusSelect').value = d.status;
                                    document.getElementById('tlPetugasInput').value = d.petugas_penanganan !== 'Belum ditugaskan' ? d.petugas_penanganan : '';
                                    if (d.cctv) {
                                        document.getElementById('tlCctvSelect').value = d.cctv.id;
                                    } else {
                                        document.getElementById('tlCctvSelect').value = '';
                                    }
                                }
                            } else {
                                containerFormTl.style.display = 'none';
                                if (alertRestricted) alertRestricted.style.display = 'flex';
                            }
                        }

                        new bootstrap.Modal(document.getElementById('modalDetailKeamanan')).show();
                    }
                });
        });
    });

    // 4. Detail CCTV & Live Feed Modal
    document.querySelectorAll('.btn-view-cctv').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            fetch(`/ronda/cctv/${id}`)
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        const d = res.data;
                        document.getElementById('cctvModalKode').textContent = d.kode_kamera;
                        document.getElementById('cctvModalNama').textContent = d.nama_kamera;
                        document.getElementById('cctvModalRt').textContent = (d.rt_number === 'RW' || d.rt_number === '000') ? 'RW 018 (Tingkat RW)' : 'RT ' + d.rt_number;
                        document.getElementById('cctvModalIp').textContent = d.ip_address;
                        document.getElementById('cctvModalNvr').textContent = d.kapasitas_penyimpanan;
                        document.getElementById('cctvFeedAreaInfo').textContent = d.lokasi;
                        document.getElementById('btnModalMapsCctv').href = d.maps_url;

                        if (d.status === 'online') {
                            document.getElementById('cctvOnlineView').style.display = 'block';
                            document.getElementById('cctvOfflineView').style.display = 'none';
                            document.getElementById('cctvFeedStatusBadge').className = 'badge bg-danger px-2 py-0.5';
                            document.getElementById('cctvFeedStatusBadge').innerHTML = '<i class="fas fa-circle me-1 fa-fade"></i> LIVE STREAM';
                            document.getElementById('cctvStreamIpInfo').textContent = `RTSP: ${d.ip_address}:554/live/ch1 · 25 FPS · H.265`;
                        } else {
                            document.getElementById('cctvOnlineView').style.display = 'none';
                            document.getElementById('cctvOfflineView').style.display = 'block';
                            document.getElementById('cctvFeedStatusBadge').className = 'badge bg-secondary px-2 py-0.5';
                            document.getElementById('cctvFeedStatusBadge').innerHTML = '<i class="fas fa-power-off me-1"></i> OFFLINE';
                            document.getElementById('cctvOfflineMessage').textContent = d.catatan_gangguan || 'Kamera sedang tidak terhubung ke jaringan NVR.';
                        }

                        // Recordings list
                        const recBody = document.getElementById('cctvRecordingsList');
                        recBody.innerHTML = '';
                        if (d.recordings && d.recordings.length > 0) {
                            d.recordings.forEach(r => {
                                const tr = document.createElement('tr');
                                tr.innerHTML = `
                                    <td class="ps-2 fw-semibold text-dark">${r.judul_rekaman}</td>
                                    <td>${r.waktu_mulai_formatted} - ${r.waktu_selesai_formatted}</td>
                                    <td>${r.ukuran_file}</td>
                                    <td>${r.is_locked ? '<span class="badge bg-warning-subtle text-dark border border-warning"><i class="fas fa-lock me-1"></i>Bukti Dikunci</span>' : '<span class="badge bg-light text-muted border">Arsip Reguler</span>'}</td>
                                    <td class="text-center">
                                        <form action="/ronda/cctv/recording/${r.id}/lock" method="POST" class="d-inline">
                                            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : ''}">
                                            <button type="submit" class="btn btn-sm ${r.is_locked ? 'btn-warning text-dark' : 'btn-outline-secondary'} py-0 px-2" title="${r.is_locked ? 'Buka Kunci' : 'Kunci Bukti'}">
                                                <i class="fas ${r.is_locked ? 'fa-lock' : 'fa-lock-open'}"></i>
                                            </button>
                                        </form>
                                    </td>
                                `;
                                recBody.appendChild(tr);
                            });
                        } else {
                            recBody.innerHTML = '<tr><td colspan="5" class="text-center py-3 text-muted">Belum ada file rekaman NVR terarsip untuk kamera ini.</td></tr>';
                        }

                        new bootstrap.Modal(document.getElementById('modalDetailCctv')).show();
                    }
                });
        });
    });

    // 5. Ubah Status CCTV Modal
    document.querySelectorAll('.btn-status-cctv').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const status = this.getAttribute('data-status');
            const catatan = this.getAttribute('data-catatan') || '';

            const form = document.getElementById('formStatusCctv');
            form.action = `/ronda/cctv/${id}/status`;
            document.getElementById('cctvStatusSelect').value = status;
            document.getElementById('cctvCatatanGangguan').value = catatan;

            new bootstrap.Modal(document.getElementById('modalStatusCctv')).show();
        });
    });

    // 6. Edit CCTV Modal (Admin RW)
    document.querySelectorAll('.btn-edit-cctv').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            fetch(`/ronda/cctv/${id}`)
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        const d = res.data;
                        const form = document.getElementById('formEditCctv');
                        form.action = `/ronda/cctv/${d.id}`;
                        document.getElementById('editCctvNama').value = d.nama_kamera;
                        document.getElementById('editCctvRt').value = d.rt_number;
                        document.getElementById('editCctvLokasi').value = d.lokasi;
                        document.getElementById('editCctvArea').value = d.area_pantau;
                        document.getElementById('editCctvIp').value = d.ip_address;
                        document.getElementById('editCctvTipe').value = d.tipe_kamera;
                        document.getElementById('editCctvMaps').value = d.maps_url;

                        new bootstrap.Modal(document.getElementById('modalEditCctv')).show();
                    }
                });
        });
    });
});
</script>
@endsection
