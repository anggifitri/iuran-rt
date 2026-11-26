@extends('layouts.app')

@section('title', 'Edukasi & Bank Sampah — NexaNest')

@section('content')
<div class="nx-page-header">
    <div>
        <span class="badge rounded-pill mb-2 px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
            <i class="fas fa-recycle me-1"></i> Ekonomi Sirkular &amp; Bank Sampah Digital · RW 018
        </span>
        <h1 class="nx-page-title">
            @if($isWarga)
                Tabungan Bank Sampah Saya
            @else
                Pusat Pengelolaan Bank Sampah &amp; Edukasi
            @endif
        </h1>
        <p class="nx-page-subtitle">
            @if($isWarga)
                Pantau saldo tabungan hasil pemilahan sampah Anda, riwayat setoran, daftar harga acuan resmi RW, dan panduan edukasi lingkungan.
            @elseif($isAdminRt)
                Pusat pencatatan setoran daur ulang warga RT {{ Auth::user()->rt_number }}, monitoring tabungan, dan edukasi pemilahan sampah tingkat RT.
            @else
                Pusat pemantauan volume daur ulang seluruh wilayah RW 018, penetapan harga acuan resmi, pengelolaan tabungan warga, dan edukasi lingkungan terpadu.
            @endif
        </p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        @if(!$isWarga)
            <button type="button" class="btn btn-outline-secondary fw-bold" data-bs-toggle="modal" data-bs-target="#modalTarikSaldo" style="border-radius: 12px;">
                <i class="fas fa-hand-holding-dollar me-1"></i> Pencairan Tabungan Warga
            </button>
            <button type="button" class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#modalCatatSetoran" style="border-radius: 12px; background: var(--nx-forest); border-color: var(--nx-forest);">
                <i class="fas fa-plus me-1"></i> Catat Setoran Sampah
            </button>
        @else
            <a href="{{ route('bank_sampah.index', ['tab' => 'edukasi']) }}" class="btn btn-outline-success fw-bold" style="border-radius: 12px;">
                <i class="fas fa-book-open me-1"></i> Baca Panduan &amp; Harga Acuan
            </a>
        @endif
    </div>
</div>

<!-- NAV TABS 4 BAGIAN UTAMA -->
<ul class="nav nav-pills mb-4 gap-2 border-bottom pb-2" id="bankSampahTabNav">
    <li class="nav-item">
        <a class="nav-link px-3.5 py-2 fw-bold {{ $activeTab === 'ringkasan' ? 'active shadow-xs' : 'text-secondary' }}" 
           href="{{ route('bank_sampah.index', ['tab' => 'ringkasan', 'rt_number' => $selectedRt, 'tahun' => $selectedTahun]) }}"
           style="border-radius: 10px; font-size: 0.88rem; {{ $activeTab === 'ringkasan' ? 'background: var(--nx-forest); color: #fff;' : 'background: #fff;' }}">
            <i class="fas fa-chart-pie me-1.5"></i> 1. Ringkasan
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link px-3.5 py-2 fw-bold {{ $activeTab === 'setoran' ? 'active shadow-xs' : 'text-secondary' }}" 
           href="{{ route('bank_sampah.index', ['tab' => 'setoran', 'rt_number' => $selectedRt, 'tahun' => $selectedTahun]) }}"
           style="border-radius: 10px; font-size: 0.88rem; {{ $activeTab === 'setoran' ? 'background: var(--nx-forest); color: #fff;' : 'background: #fff;' }}">
            <i class="fas fa-scale-balanced me-1.5"></i> {{ $isWarga ? '2. Riwayat Setoran Saya' : '2. Setoran Sampah' }}
            @if($menungguKonfirmasiCount > 0 && !$isWarga)
                <span class="badge rounded-pill bg-warning text-dark ms-1.5" style="font-size: 0.7rem;">{{ $menungguKonfirmasiCount }} Antrean</span>
            @endif
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link px-3.5 py-2 fw-bold {{ $activeTab === 'edukasi' ? 'active shadow-xs' : 'text-secondary' }}" 
           href="{{ route('bank_sampah.index', ['tab' => 'edukasi', 'rt_number' => $selectedRt, 'tahun' => $selectedTahun]) }}"
           style="border-radius: 10px; font-size: 0.88rem; {{ $activeTab === 'edukasi' ? 'background: var(--nx-forest); color: #fff;' : 'background: #fff;' }}">
            <i class="fas fa-graduation-cap me-1.5"></i> 3. Edukasi &amp; Harga Sampah
        </a>
    </li>
    @if(!$isWarga)
    <li class="nav-item">
        <a class="nav-link px-3.5 py-2 fw-bold {{ $activeTab === 'laporan' ? 'active shadow-xs' : 'text-secondary' }}" 
           href="{{ route('bank_sampah.index', ['tab' => 'laporan', 'rt_number' => $selectedRt, 'tahun' => $selectedTahun]) }}"
           style="border-radius: 10px; font-size: 0.88rem; {{ $activeTab === 'laporan' ? 'background: var(--nx-forest); color: #fff;' : 'background: #fff;' }}">
            <i class="fas fa-file-waveform me-1.5"></i> 4. Laporan &amp; Rekapitulasi
        </a>
    </li>
    @endif
</ul>

<!-- ========================================================================= -->
<!-- TAB 1: RINGKASAN (DASHBOARD UTAMA) -->
<!-- ========================================================================= -->
@if($activeTab === 'ringkasan')
    <!-- ROLE WARGA: KARTU TABUNGAN PRIBADI -->
    @if($isWarga)
        <div class="row g-3 mb-4">
            <div class="col-lg-4 col-md-6">
                <div class="card p-3.5 h-100 border-0 shadow-xs text-white" style="background: linear-gradient(135deg, #285943 0%, #1e4533 100%); border-radius: 14px;">
                    <span class="text-white-50 small fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem;">Saldo Tabungan Sampah Anda</span>
                    <h2 class="fw-bold mb-1 text-white">Rp {{ number_format($wargaTabunganData['saldo_aktif'], 0, ',', '.') }}</h2>
                    <small class="text-white-50" style="font-size: 0.72rem;">Dapat dicairkan tunai atau dialokasikan untuk iuran bulanan.</small>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-6">
                <div class="card p-3 h-100 border-0 shadow-xs" style="background: var(--nx-mint-soft); border-radius: 14px;">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.68rem;">Total Sampah Disetor</span>
                    <h3 class="fw-bold mb-0" style="color: var(--nx-forest);">{{ number_format($wargaTabunganData['total_kg'], 1) }} <span class="fs-6 text-muted fw-normal">Kg</span></h3>
                    <small class="text-muted" style="font-size: 0.72rem;">Limbah terpilah dari rumah</small>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-6">
                <div class="card p-3 h-100 border-0 shadow-xs" style="background: var(--nx-powder-soft); border-radius: 14px;">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.68rem;">Total Nilai Terkumpul</span>
                    <h3 class="fw-bold mb-0" style="color: #1F567A;">Rp {{ number_format($wargaTabunganData['total_nilai'], 0, ',', '.') }}</h3>
                    <small class="text-muted" style="font-size: 0.72rem;">Akumulasi hasil daur ulang</small>
                </div>
            </div>
            <div class="col-lg-2 col-md-6 col-12">
                <div class="card p-3 h-100 border-0 shadow-xs" style="background: #FFFDF0; border-radius: 14px;">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.68rem;">Transaksi</span>
                    <h3 class="fw-bold mb-0 text-dark">{{ $wargaTabunganData['transaksi_count'] }} <span class="fs-6 text-muted fw-normal">Kali</span></h3>
                    <small class="text-muted" style="font-size: 0.72rem;">Penyetoran aktif</small>
                </div>
            </div>
        </div>
    @else
        <!-- ROLE ADMIN RW & RT: KARTU METRIK KESELURUHAN -->
        <div class="row g-3 mb-4">
            <div class="col-xl-2 col-md-4 col-6">
                <div class="card p-3 h-100 border-0 shadow-xs" style="background: #F0F9F3; border-left: 3px solid #285943 !important;">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.68rem;">Total Terkumpul</span>
                    <h3 class="fw-bold mb-0" style="color: var(--nx-forest);">{{ number_format($totalBeratKg, 1) }} <span class="fs-6 text-muted fw-normal">Kg</span></h3>
                    <small class="text-muted" style="font-size: 0.72rem;">Limbah terpilah valid</small>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="card p-3 h-100 border-0 shadow-xs" style="background: #EBF4FA; border-left: 3px solid #1F567A !important;">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.68rem;">Nilai Ekonomi</span>
                    <h3 class="fw-bold mb-0" style="color: #1F567A;">Rp {{ number_format($totalNilaiRupiah, 0, ',', '.') }}</h3>
                    <small class="text-muted" style="font-size: 0.72rem;">Tabungan sah warga</small>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="card p-3 h-100 border-0 shadow-xs" style="background: #FAF8EA; border-left: 3px solid #8C6A05 !important;">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.68rem;">Total Transaksi</span>
                    <h3 class="fw-bold mb-0" style="color: #8C6A05;">{{ number_format($totalTransaksi) }} <span class="fs-6 text-muted fw-normal">Kali</span></h3>
                    <small class="text-muted" style="font-size: 0.72rem;">Setoran terverifikasi</small>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="card p-3 h-100 border-0 shadow-xs" style="background: #F8FAF8; border-left: 3px solid #3E4657 !important;">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.68rem;">Warga Aktif</span>
                    <h3 class="fw-bold mb-0 text-dark">{{ $wargaAktifCount }} <span class="fs-6 text-muted fw-normal">Warga</span></h3>
                    <small class="text-muted" style="font-size: 0.72rem;">Nasabah bank sampah</small>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="card p-3 h-100 border-0 shadow-xs" style="background: #FDF9F3; border-left: 3px solid #A8C9B5 !important;">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.68rem;">RT Berpartisipasi</span>
                    <h3 class="fw-bold mb-0" style="color: var(--nx-forest);">{{ $rtAktifCount }} <span class="fs-6 text-muted fw-normal">/ 5 RT</span></h3>
                    <small class="text-muted" style="font-size: 0.72rem;">Partisipasi wilayah</small>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="card p-3 h-100 border-0 shadow-xs" style="background: #FFFDF0; border-left: 3px solid #E2A03F !important;">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.68rem;">Menunggu Verifikasi</span>
                    <h3 class="fw-bold mb-0 text-warning">{{ $menungguKonfirmasiCount }}</h3>
                    <small class="text-muted" style="font-size: 0.72rem;">Perlu dicek timbang</small>
                </div>
            </div>
        </div>
    @endif

    <!-- GRAFIK SECTION -->
    <div class="row g-3 mb-4">
        <!-- GRAFIK 1: PERKEMBANGAN BERAT DAN NILAI EKONOMI BULANAN -->
        <div class="col-lg-7">
            <div class="card p-3.5 h-100 shadow-xs">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.92rem;">
                            <i class="fas fa-chart-line text-success me-2"></i>Perkembangan Pengumpulan Sampah (Jan - Des {{ $selectedTahun }})
                        </h6>
                        <small class="text-muted" style="font-size: 0.72rem;">Volume timbangan (Kg) dan konversi nilai tabungan (Rp)</small>
                    </div>
                    <form method="GET" action="{{ route('bank_sampah.index') }}" class="d-flex align-items-center gap-1.5">
                        <input type="hidden" name="tab" value="ringkasan">
                        @if(!empty($selectedRt)) <input type="hidden" name="rt_number" value="{{ $selectedRt }}"> @endif
                        <select name="tahun" class="form-select form-select-sm" onchange="this.form.submit()">
                            @foreach(['2025', '2026', '2027'] as $y)
                                <option value="{{ $y }}" {{ $selectedTahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <div style="height: 250px;">
                    <canvas id="chartBulananSampah"></canvas>
                </div>
            </div>
        </div>

        <!-- GRAFIK 2: KOMPOSISI KATEGORI SAMPAH (DOUGHNUT) -->
        <div class="col-lg-5">
            <div class="card p-3.5 h-100 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.92rem;">
                        <i class="fas fa-chart-pie text-success me-2"></i>Komposisi Kategori Sampah (Kg)
                    </h6>
                    <span class="badge bg-light text-muted border px-2 py-0.5" style="font-size: 0.7rem;">Limbah Terpilah</span>
                </div>
                <div style="height: 210px;">
                    <canvas id="chartKategoriDoughnut"></canvas>
                </div>
                <div class="pt-2 border-top mt-2 row g-1 text-center small" style="font-size: 0.7rem;">
                    @foreach($kategoriBreakdown as $code => $kb)
                        <div class="col-4 mb-1">
                            <span class="text-muted d-block text-truncate">{{ $kb['nama'] }}</span>
                            <strong class="text-dark">{{ $kb['berat'] }} Kg</strong>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        @if(!$isWarga && $isAdminRw)
        <!-- GRAFIK 3: PERBANDINGAN JUMLAH SETORAN ANTAR-RT (KHUSUS RW) -->
        <div class="col-12">
            <div class="card p-3.5 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.92rem;">
                        <i class="fas fa-scale-balanced text-primary me-2"></i>Perbandingan Akumulasi Setoran Antar-RT (RT 006 s/d RT 010)
                    </h6>
                    <small class="text-muted" style="font-size: 0.72rem;">Komparasi volume sampah terpilah per wilayah RT</small>
                </div>
                <div style="height: 220px;">
                    <canvas id="chartPerbandinganRt"></canvas>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- RECENT SETORAN SAMPAH TABLE PREVIEW -->
    <div class="card overflow-hidden shadow-xs mb-4">
        <div class="card-header p-3 bg-white d-flex justify-content-between align-items-center border-bottom">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="fas fa-clock-rotate-left text-success me-2"></i>{{ $isWarga ? 'Aktivitas Setoran Terakhir Saya' : 'Aktivitas Setoran Terbaru' }}
            </h6>
            <a href="{{ route('bank_sampah.index', ['tab' => 'setoran']) }}" class="btn btn-sm btn-link text-decoration-none p-0 small fw-bold" style="color: var(--nx-forest); font-size: 0.78rem;">
                Lihat Seluruh Riwayat Setoran <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.82rem;">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 15%;">No Transaksi</th>
                        <th style="width: 13%;">Tanggal</th>
                        <th style="width: 22%;">Nama Warga &amp; RT</th>
                        <th style="width: 16%;">Kategori</th>
                        <th style="width: 12%;">Berat</th>
                        <th style="width: 12%;">Nilai Rupiah</th>
                        <th class="pe-3 text-end" style="width: 10%;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentSetorans as $rs)
                        <tr>
                            <td class="ps-3">
                                <strong class="text-dark">{{ $rs->nomor_transaksi ?: 'BS-' . $rs->id }}</strong>
                            </td>
                            <td>{{ $rs->tanggal_setor ? $rs->tanggal_setor->translatedFormat('d M Y') : '-' }}</td>
                            <td>
                                <strong class="text-dark d-block">{{ $rs->nama_penyetor }}</strong>
                                <small class="text-muted" style="font-size: 0.7rem;">RT {{ $rs->rt_number }} · Petugas: {{ $rs->petugas ?: '-' }}</small>
                            </td>
                            <td>
                                <span class="badge rounded-pill px-2.5 py-0.5" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-weight: 700; font-size: 0.7rem;">
                                    {{ ucfirst($rs->kategori) }}
                                </span>
                            </td>
                            <td><strong>{{ $rs->berat_kg }} {{ $rs->satuan }}</strong></td>
                            <td><strong class="text-success">Rp {{ number_format($rs->nilai_rupiah, 0, ',', '.') }}</strong></td>
                            <td class="pe-3 text-end">{!! $rs->status_badge !!}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted small">
                                Belum ada aktivitas setoran sampah yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif

<!-- ========================================================================= -->
<!-- TAB 2: SETORAN SAMPAH & TABUNGAN WARGA -->
<!-- ========================================================================= -->
@if($activeTab === 'setoran')
    <!-- FILTER BAR -->
    <div class="card p-3 mb-3 shadow-xs">
        <form method="GET" action="{{ route('bank_sampah.index') }}" class="row g-2 align-items-center">
            <input type="hidden" name="tab" value="setoran">
            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="{{ $isWarga ? 'Cari No Transaksi / Kategori...' : 'Cari nama warga / No Tx...' }}" value="{{ request('search') }}">
                </div>
            </div>
            @if(!$isWarga && $isAdminRw)
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
                <select name="kategori" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Semua Kategori --</option>
                    @foreach($kategoris as $kOpt)
                        <option value="{{ $kOpt->kode }}" {{ request('kategori') == $kOpt->kode ? 'selected' : '' }}>{{ $kOpt->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 col-6">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Semua Status --</option>
                    <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                    <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <div class="col-md-3 col-6 text-end">
                <button type="submit" class="btn btn-sm btn-primary px-3 fw-bold">
                    <i class="fas fa-filter me-1"></i> Terapkan
                </button>
                <a href="{{ route('bank_sampah.index', ['tab' => 'setoran']) }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>

    <!-- TABEL TRANSAKSI SETORAN (STANDAR NEXANEST) -->
    <div class="card nx-table-card overflow-hidden shadow-xs border-0 mb-4">
        <div class="card-header p-3.5 bg-white d-flex justify-content-between align-items-center border-bottom flex-wrap gap-2">
            <div>
                <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.95rem;">
                    <i class="fas fa-list-check text-success me-2"></i>Daftar Transaksi Setoran Sampah
                </h6>
                <small class="text-muted" style="font-size: 0.74rem;">Catatan penimbangan limbah terpilah warga lingkungan RW 018</small>
            </div>
            <span class="badge rounded-pill px-3 py-1.5" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-weight: 700; font-size: 0.74rem;">
                Total: {{ $setorans->total() }} Transaksi
            </span>
        </div>

        <div class="table-responsive nx-table-responsive">
            <table class="table table-hover align-middle nx-table mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" style="width: 14%;">No Transaksi</th>
                        <th style="width: 12%;">Tanggal</th>
                        <th style="width: 20%;">Penyetor &amp; RT</th>
                        <th style="width: 15%;">Kategori</th>
                        <th style="width: 12%;">Berat/Vol</th>
                        <th style="width: 13%;">Harga Satuan</th>
                        <th style="width: 14%;">Total Nilai</th>
                        <th style="width: 12%;">Status</th>
                        <th class="pe-4 text-end" style="width: 8%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($setorans as $s)
                        <tr>
                            <td class="ps-3">
                                <strong class="text-dark">{{ $s->nomor_transaksi ?: 'BS-' . $s->id }}</strong>
                            </td>
                            <td>{{ $s->tanggal_setor ? $s->tanggal_setor->translatedFormat('d M Y') : '-' }}</td>
                            <td>
                                <strong class="text-dark d-block">{{ $s->nama_penyetor }}</strong>
                                <small class="text-muted" style="font-size: 0.7rem;">RT {{ $s->rt_number }} · Petugas: {{ $s->petugas ?: '-' }}</small>
                            </td>
                            <td>
                                <span class="badge rounded-pill px-2.5 py-0.5" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-weight: 700; font-size: 0.72rem;">
                                    {{ ucfirst($s->kategori) }}
                                </span>
                            </td>
                            <td><strong>{{ $s->berat_kg }} {{ $s->satuan }}</strong></td>
                            <td><small class="text-muted">Rp {{ number_format($s->harga_per_satuan, 0, ',', '.') }}</small></td>
                            <td><strong class="text-success">Rp {{ number_format($s->nilai_rupiah, 0, ',', '.') }}</strong></td>
                            <td>
                                {!! $s->status_badge !!}
                                @if($s->status === 'ditolak' && $s->alasan_penolakan)
                                    <small class="text-danger d-block text-truncate" style="max-width: 140px; font-size: 0.68rem;" title="{{ $s->alasan_penolakan }}">
                                        {{ $s->alasan_penolakan }}
                                    </small>
                                @endif
                            </td>
                            <td class="pe-3 text-end">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-secondary btn-detail-setoran" data-id="{{ $s->id }}" title="Lihat Detail Transaksi">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if(!$isWarga && $s->status === 'menunggu')
                                        <button type="button" class="btn btn-outline-success btn-verifikasi-modal" data-id="{{ $s->id }}" data-notx="{{ $s->nomor_transaksi }}" data-nama="{{ $s->nama_penyetor }}" data-nilai="Rp {{ number_format($s->nilai_rupiah, 0, ',', '.') }}" title="Verifikasi Setoran">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted small">
                                <i class="fas fa-scale-balanced fa-3x mb-2 text-success opacity-50"></i>
                                <p class="mb-0">Tidak ada transaksi setoran sampah yang sesuai dengan filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($setorans->hasPages())
            <div class="card-footer bg-white p-3 border-top">
                {{ $setorans->links() }}
            </div>
        @endif
    </div>

    <!-- SECTION BUKU TABUNGAN NASABAH WARGA (KHUSUS ADMIN RW & RT) -->
    @if(!$isWarga)
        <div class="card nx-table-card overflow-hidden shadow-xs border-0">
            <div class="card-header bg-white p-3.5 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.95rem;">
                        <i class="fas fa-wallet text-success me-2"></i>Buku Rekening Tabungan Bank Sampah Warga
                    </h6>
                    <small class="text-muted" style="font-size: 0.74rem;">Pantau akumulasi timbangan, nilai tabungan, dan saldo penarikan warga.</small>
                </div>
                <button type="button" class="btn btn-sm btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#modalTarikSaldo" style="border-radius: 8px;">
                    <i class="fas fa-hand-holding-dollar me-1"></i> Proses Penarikan Saldo
                </button>
            </div>

            <div class="table-responsive nx-table-responsive">
                <table class="table table-hover align-middle nx-table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Nama Warga</th>
                            <th>Wilayah</th>
                            <th>Total Sampah Disetor</th>
                            <th>Total Tabungan Masuk</th>
                            <th>Total Telah Ditarik</th>
                            <th>Saldo Aktif Saat Ini</th>
                            <th class="pe-4 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tabunganWargaList as $tw)
                            <tr>
                                <td class="ps-3">
                                    <strong class="text-dark">{{ $tw['warga']->nama }}</strong>
                                    <small class="text-muted d-block" style="font-size: 0.7rem;">NIK: {{ $tw['warga']->nik ?: '-' }}</small>
                                </td>
                                <td><span class="badge bg-light text-dark border">RT {{ $tw['warga']->rt_number }}</span></td>
                                <td><strong>{{ number_format($tw['total_kg'], 1) }} Kg</strong></td>
                                <td><span class="text-muted">Rp {{ number_format($tw['total_setoran'], 0, ',', '.') }}</span></td>
                                <td><span class="text-danger">- Rp {{ number_format($tw['total_penarikan'], 0, ',', '.') }}</span></td>
                                <td>
                                    <h6 class="fw-bold mb-0 text-success" style="font-size: 0.9rem;">
                                        Rp {{ number_format($tw['saldo'], 0, ',', '.') }}
                                    </h6>
                                </td>
                                <td class="pe-3 text-end">
                                    <a href="{{ route('bank_sampah.detail_tabungan', $tw['warga']->id) }}" class="btn btn-sm btn-outline-secondary py-1" style="font-size: 0.72rem;">
                                        <i class="fas fa-book me-1"></i> Buku Kas
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted small">Belum ada data warga terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(isset($tabunganWargas) && $tabunganWargas->hasPages())
                <div class="card-footer bg-white p-2.5 border-top">
                    {{ $tabunganWargas->links() }}
                </div>
            @endif
        </div>
    @endif
@endif

<!-- ========================================================================= -->
<!-- TAB 3: EDUKASI & HARGA SAMPAH -->
<!-- ========================================================================= -->
@if($activeTab === 'edukasi')
    <!-- SEKTOR 1: STANDAR KATEGORI & HARGA DAUR ULANG RESMI RW -->
    <div class="card p-4 mb-4 shadow-xs" style="background: linear-gradient(135deg, #F5FBF6 0%, #FFFFFF 100%); border-radius: 14px;">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
            <div class="d-flex align-items-center gap-3">
                <span class="rounded-circle d-flex align-items-center justify-content-center shadow-xs" style="width: 44px; height: 44px; background: var(--nx-forest); color: #fff;">
                    <i class="fas fa-tags fa-lg"></i>
                </span>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Standar Klasifikasi &amp; Harga Acuan Bank Sampah RW 018</h5>
                    <p class="text-muted small mb-0" style="font-size: 0.8rem;">
                        Taksiran nilai tukar tabungan daur ulang per kilogram/liter berdasarkan standar harga pasar limbah terpilah.
                    </p>
                </div>
            </div>
            @if($isAdminRw)
                <button type="button" class="btn btn-sm btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahKategori" style="border-radius: 8px;">
                    <i class="fas fa-plus me-1"></i> Tambah Kategori Sampah
                </button>
            @endif
        </div>

        <!-- CARDS KATEGORI SAMPAH & HARGA (STANDAR NEXANEST) -->
        <div class="row g-3">
            @foreach($kategoris as $kat)
                <div class="col-lg-4 col-md-6">
                    <div class="card nx-grid-card shadow-xs">
                        <div class="nx-grid-card-body">
                            <div class="d-flex align-items-center justify-content-between mb-2.5">
                                <span class="badge rounded-pill px-2.5 py-1" style="background: rgba(40,89,67,0.1); color: {{ $kat->accent_color ?: '#285943' }}; font-weight: 700; font-size: 0.74rem;">
                                    <i class="fas {{ $kat->icon ?: 'fa-recycle' }} me-1"></i> {{ $kat->nama_kategori }}
                                </span>
                                @if(!$kat->is_active)
                                    <span class="badge bg-secondary rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">Nonaktif</span>
                                @else
                                    <span class="small text-muted fw-bold" style="font-size: 0.72rem;">Satuan: {{ $kat->satuan }}</span>
                                @endif
                            </div>

                            <p class="text-dark mb-2.5" style="font-size: 0.8rem; line-height: 1.4; min-height: 40px; color: #475569;">
                                {{ $kat->deskripsi }}
                            </p>

                            <!-- BOX HARGA ACUAN -->
                            <div class="p-2.5 rounded-3 mb-2.5" style="background: rgba(40,89,67,0.05); border: 1px dashed rgba(40,89,67,0.22);">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem;">Rentang Pasar:</span>
                                    <span class="badge rounded-pill px-2 py-0.5" style="background: {{ $kat->accent_color ?: '#285943' }}; color: #fff; font-size: 0.65rem;">
                                        Patokan RW
                                    </span>
                                </div>
                                <div class="fw-bold d-flex align-items-baseline gap-1" style="color: {{ $kat->accent_color ?: '#285943' }}; font-size: 1.05rem;">
                                    {{ $kat->range_label }}
                                </div>
                                <div class="text-muted d-flex justify-content-between align-items-center pt-1 border-top mt-1" style="font-size: 0.72rem;">
                                    <span>Harga Acuan Beli:</span>
                                    <strong class="text-dark">Rp {{ number_format($kat->harga_acuan, 0, ',', '.') }} / {{ $kat->satuan }}</strong>
                                </div>
                            </div>

                            <small class="text-muted d-block mb-2" style="font-size: 0.72rem; line-height: 1.35;">
                                <i class="fas fa-lightbulb text-warning me-1"></i><strong>Tips:</strong> {{ $kat->tips_pemilahan }}
                            </small>
                        </div>

                        @if($isAdminRw)
                            <div class="nx-grid-card-footer">
                                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2.5 btn-edit-kategori" 
                                        data-id="{{ $kat->id }}" data-nama="{{ $kat->nama_kategori }}" data-deskripsi="{{ $kat->deskripsi }}" 
                                        data-satuan="{{ $kat->satuan }}" data-min="{{ $kat->harga_min }}" data-max="{{ $kat->harga_max }}" 
                                        data-acuan="{{ $kat->harga_acuan }}" data-tips="{{ $kat->tips_pemilahan }}" style="font-size: 0.75rem; border-radius: 8px;">
                                    <i class="fas fa-pen-to-square me-1"></i> Edit Harga / Info
                                </button>
                                <form action="{{ route('bank_sampah.toggle_kategori', $kat->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $kat->is_active ? 'btn-outline-danger' : 'btn-outline-success' }} py-1 px-2.5" style="font-size: 0.75rem; border-radius: 8px;">
                                        {{ $kat->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- SEKTOR 2: ARTIKEL EDUKASI LINGKUNGAN -->
    <div class="card p-4 shadow-xs" style="border-radius: 14px;">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
            <div>
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="fas fa-book-open-reader text-success me-2"></i>Panduan &amp; Edukasi Pemilahan Sampah Rumah Tangga
                </h5>
                <small class="text-muted" style="font-size: 0.78rem;">Informasi praktis pengelolaan sampah harian, teknik kompos, dan penerapan zero waste di lingkungan RW 018.</small>
            </div>
            @if($isAdminRw)
                <button type="button" class="btn btn-sm btn-outline-success fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahEdukasi" style="border-radius: 8px;">
                    <i class="fas fa-plus me-1"></i> Tulis Artikel Baru
                </button>
            @endif
        </div>

        <div class="row g-3">
            @foreach($edukasiArticles as $art)
                <div class="col-lg-4 col-md-6">
                    <div class="p-3.5 rounded-3 bg-white border h-100 d-flex flex-column justify-content-between shadow-xs">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-light text-dark border px-2.5 py-1 fw-bold" style="font-size: 0.72rem;">
                                    <i class="fas {{ $art->icon ?: 'fa-newspaper' }} text-success me-1"></i> {{ $art->kategori_materi }}
                                </span>
                                <small class="text-muted" style="font-size: 0.68rem;">{{ $art->published_at ? $art->published_at->translatedFormat('d M Y') : '-' }}</small>
                            </div>
                            <h6 class="fw-bold text-dark mb-2" style="font-size: 0.9rem; line-height: 1.35;">
                                {{ $art->judul }}
                            </h6>
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4;">
                                {{ $art->deskripsi_singkat }}
                            </p>
                        </div>

                        <div class="pt-2.5 border-top d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-3 btn-baca-edukasi" 
                                    data-judul="{{ $art->judul }}" data-kategori="{{ $art->kategori_materi }}" 
                                    data-isi="{{ $art->isi_materi }}" style="font-size: 0.74rem; font-weight: 600;">
                                Baca Selengkapnya <i class="fas fa-arrow-right ms-1"></i>
                            </button>
                            @if($isAdminRw)
                                <form action="{{ route('bank_sampah.destroy_edukasi', $art->id) }}" method="POST" onsubmit="return confirm('Hapus materi edukasi ini?')" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link btn-sm text-danger p-0" title="Hapus Artikel">
                                        <i class="fas fa-trash-can"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

<!-- ========================================================================= -->
<!-- TAB 4: LAPORAN & REKAPITULASI -->
<!-- ========================================================================= -->
@if($activeTab === 'laporan' && !$isWarga)
    <!-- FILTER BAR LAPORAN -->
    <div class="card p-3 mb-4 shadow-xs">
        <form method="GET" action="{{ route('bank_sampah.index') }}" class="row g-2 align-items-center">
            <input type="hidden" name="tab" value="laporan">
            <div class="col-md-3 col-6">
                <label class="form-label small fw-bold mb-1">Pilih Tahun</label>
                <select name="tahun" class="form-select form-select-sm" onchange="this.form.submit()">
                    @foreach(['2025', '2026', '2027'] as $y)
                        <option value="{{ $y }}" {{ $selectedTahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 col-6">
                <label class="form-label small fw-bold mb-1">Pilih Bulan</label>
                <select name="bulan" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Seluruh Bulan --</option>
                    @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $k => $nama)
                        <option value="{{ $k }}" {{ $selectedBulan == $k ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            @if(!$isWarga && $isAdminRw)
                <div class="col-md-3 col-6">
                    <label class="form-label small fw-bold mb-1">Wilayah RT</label>
                    <select name="rt_number" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Seluruh RW 018 --</option>
                        @foreach($allRts as $rtOpt)
                            <option value="{{ $rtOpt }}" {{ request('rt_number') == $rtOpt ? 'selected' : '' }}>RT {{ $rtOpt }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="col-md-3 col-6 text-end d-flex align-items-end justify-content-end gap-2 pt-3">
                <a href="{{ route('bank_sampah.export_csv', request()->all()) }}" class="btn btn-sm btn-outline-success fw-bold">
                    <i class="fas fa-file-csv me-1"></i> Ekspor CSV
                </a>
                <a href="{{ route('bank_sampah.cetak_laporan', request()->all()) }}" target="_blank" class="btn btn-sm btn-outline-secondary fw-bold">
                    <i class="fas fa-print me-1"></i> Cetak Dokumen
                </a>
            </div>
        </form>
    </div>

    @if(!$isWarga && $isAdminRw)
        <!-- TABEL REKAPITULASI HASIL PER RT -->
        <div class="card overflow-hidden shadow-xs mb-4">
            <div class="card-header p-3 bg-white border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fas fa-table text-success me-2"></i>Rekapitulasi Capaian Bank Sampah Per RT
                </h6>
                <small class="text-muted" style="font-size: 0.74rem;">Hasil timbangan tervalidasi</small>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.83rem;">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Wilayah</th>
                            <th>Total Berat (Kg)</th>
                            <th>Total Nilai Tabungan</th>
                            <th>Jumlah Transaksi</th>
                            <th>Warga Berpartisipasi</th>
                            <th class="pe-3">Kategori Terbanyak</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rekapLaporanRt as $r)
                            <tr>
                                <td class="ps-3"><strong class="text-dark">RT {{ $r['rt_number'] }}</strong></td>
                                <td><strong class="text-dark">{{ $r['total_berat'] }} Kg</strong></td>
                                <td><strong class="text-success">Rp {{ number_format($r['total_nilai'], 0, ',', '.') }}</strong></td>
                                <td>{{ $r['total_transaksi'] }} Transaksi</td>
                                <td>{{ $r['warga_count'] }} Warga</td>
                                <td class="pe-3"><span class="badge bg-light text-dark border">{{ $r['top_kategori'] }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endif

<!-- ========================================================================= -->
<!-- MODALS -->
<!-- ========================================================================= -->

<!-- 1. MODAL CATAT SETORAN SAMPAH -->
<div class="modal fade" id="modalCatatSetoran" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('bank_sampah.store_setoran') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="font-size: 1rem; color: var(--nx-forest);">
                        <i class="fas fa-scale-balanced me-2"></i>Catat Setoran Bank Sampah
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Dropdown Warga dari Database Warga -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Pilih Warga Penyetor (Dari Database Warga)</label>
                        <select name="warga_id" id="modalWargaSelect" class="form-select">
                            <option value="">-- Pilih dari Daftar Warga atau Ketik Manual --</option>
                            @foreach($wargaDropdown as $wd)
                                <option value="{{ $wd->id }}" data-nama="{{ $wd->nama }}" data-rt="{{ $wd->rt_number }}">
                                    {{ $wd->nama }} (RT {{ $wd->rt_number }}{{ $wd->blok_rumah ? ' - ' . $wd->blok_rumah : '' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label small fw-bold">Nama Penyetor <span class="text-danger">*</span></label>
                            <input type="text" name="nama_penyetor" id="modalNamaPenyetor" class="form-control" placeholder="Contoh: Ibu Rina" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-bold">Wilayah RT <span class="text-danger">*</span></label>
                            @if(Auth::user()->isAdminRt())
                                <input type="text" class="form-control bg-light" value="RT {{ Auth::user()->rt_number }}" readonly>
                                <input type="hidden" name="rt_number" id="modalRtNumber" value="{{ Auth::user()->rt_number }}">
                            @else
                                <select name="rt_number" id="modalRtNumber" class="form-select" required>
                                    @foreach($allRts as $rtOpt)
                                        <option value="{{ $rtOpt }}">RT {{ $rtOpt }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Kategori Sampah <span class="text-danger">*</span></label>
                            <select name="kategori" id="modalKategoriSelect" class="form-select" required>
                                @foreach($activeKategoris as $ak)
                                    <option value="{{ $ak->kode }}" data-harga="{{ $ak->harga_acuan }}" data-satuan="{{ $ak->satuan }}">
                                        {{ $ak->nama_kategori }} (Rp {{ number_format($ak->harga_acuan, 0, ',', '.') }}/{{ $ak->satuan }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Berat / Volume (<span id="modalSatuanLabel">Kg</span>) <span class="text-danger">*</span></label>
                            <input type="number" step="0.1" name="berat_kg" id="modalBeratInput" class="form-control" placeholder="Contoh: 12.5" required>
                        </div>
                    </div>

                    <!-- Otomatisasi Nilai Rupiah -->
                    <div class="p-2.5 rounded-2 bg-light border mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem;">Total Nilai Otomatis:</span>
                            <span class="badge bg-secondary-subtle text-secondary" id="modalHargaAcuanBadge">Rp 3.500 / Kg</span>
                        </div>
                        <h4 class="fw-bold mb-0 text-success" id="modalTotalNilaiDisplay">Rp 0</h4>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tanggal Setor</label>
                            <input type="date" name="tanggal_setor" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Foto Dokumentasi (Opsional)</label>
                            <input type="file" name="foto_dokumentasi" class="form-control" accept="image/*">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold">Catatan Tambahan (Opsional)</label>
                        <input type="text" name="catatan" class="form-control" placeholder="Contoh: Penyetoran rutin mingguan...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">Simpan Setoran</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 2. MODAL PENARIKAN SALDO TABUNGAN -->
<div class="modal fade" id="modalTarikSaldo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('bank_sampah.store_penarikan') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="font-size: 1rem; color: var(--nx-forest);">
                        <i class="fas fa-hand-holding-dollar me-2"></i>Pencairan Saldo Tabungan Warga
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Pilih Warga</label>
                        <select name="warga_id" id="modalTarikWargaSelect" class="form-select" required>
                            <option value="">-- Pilih Warga Nasabah --</option>
                            @foreach($wargaDropdown as $wd)
                                <option value="{{ $wd->id }}">{{ $wd->nama }} (RT {{ $wd->rt_number }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Jumlah Penarikan (Rp)</label>
                            <input type="number" name="jumlah_penarikan" class="form-control" placeholder="Contoh: 50000" min="1000" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Metode Pencairan</label>
                            <select name="metode_penarikan" class="form-select" required>
                                <option value="tunai">Uang Tunai</option>
                                <option value="transfer">Transfer Bank / E-Wallet</option>
                                <option value="potong_iuran">Alokasi Pembayaran Iuran</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Tanggal Pencairan</label>
                        <input type="date" name="tanggal_penarikan" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold">Catatan Penarikan</label>
                        <input type="text" name="catatan" class="form-control" placeholder="Contoh: Pencairan tabungan akhir bulan...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">Proses Penarikan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 3. MODAL VERIFIKASI SETORAN -->
<div class="modal fade" id="modalVerifikasiSetoran" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formVerifikasiSetoran" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="font-size: 1rem; color: var(--nx-forest);">
                        <i class="fas fa-check-double me-2"></i>Verifikasi Setoran Sampah
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="p-2.5 rounded-2 bg-light mb-3 small">
                        <div><strong>No Transaksi:</strong> <span id="verifNoTx">-</span></div>
                        <div><strong>Warga:</strong> <span id="verifNama">-</span></div>
                        <div><strong>Nilai Setoran:</strong> <span id="verifNilai" class="text-success fw-bold">-</span></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Keputusan Verifikasi</label>
                        <select name="keputusan" id="verifKeputusanSelect" class="form-select" required>
                            <option value="diterima">Terima Setoran (Saldo Bertambah)</option>
                            <option value="ditolak">Tolak Setoran</option>
                        </select>
                    </div>

                    <div class="mb-2" id="boxAlasanTolak" style="display: none;">
                        <label class="form-label small fw-bold text-danger">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="alasan_penolakan" class="form-control" rows="2" placeholder="Jelaskan alasan penolakan..."></textarea>
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

<!-- 4. MODAL BACA ARTIKEL EDUKASI LENGKAP -->
<div class="modal fade" id="modalBacaEdukasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <span class="badge bg-light text-dark border px-2 py-0.5 fw-bold mb-1" id="bacaKategoriBadge">-</span>
                    <h5 class="modal-title fw-bold" id="bacaJudulModal" style="font-size: 1.05rem; color: var(--nx-forest);">-</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="bacaIsiContent" style="white-space: pre-line; line-height: 1.6; font-size: 0.88rem; color: #263449;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@if($isAdminRw)
<!-- 5. MODAL TAMBAH KATEGORI SAMPAH (ADMIN RW) -->
<div class="modal fade" id="modalTambahKategori" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('bank_sampah.store_kategori') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="font-size: 1rem; color: var(--nx-forest);">
                        <i class="fas fa-plus-circle me-2"></i>Tambah Kategori Sampah Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Kode Unik <span class="text-danger">*</span></label>
                            <input type="text" name="kode" class="form-control" placeholder="Contoh: kaleng" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" name="nama_kategori" class="form-control" placeholder="Contoh: Kaleng Minuman" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Deskripsi Singkat</label>
                        <input type="text" name="deskripsi" class="form-control" placeholder="Contoh: Kaleng aluminium bekas minuman ringan...">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Satuan</label>
                            <input type="text" name="satuan" class="form-control" value="Kg" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Harga Min (Rp)</label>
                            <input type="number" name="harga_min" class="form-control" value="2000" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Harga Max (Rp)</label>
                            <input type="number" name="harga_max" class="form-control" value="5000" min="0" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Harga Acuan RW (Rp/Satuan) <span class="text-danger">*</span></label>
                        <input type="number" name="harga_acuan" class="form-control" value="3500" min="0" required>
                        <small class="text-muted" style="font-size: 0.72rem;">Harga yang digunakan otomatis sebagai pengali saat warga menyetorkan sampah jenis ini.</small>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold">Tips Pemilahan</label>
                        <textarea name="tips_pemilahan" class="form-control" rows="2" placeholder="Contoh: Bersihkan sisa minuman dan pipihkan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 6. MODAL EDIT KATEGORI & HARGA ACUAN (ADMIN RW) -->
<div class="modal fade" id="modalEditKategori" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formEditKategori" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="font-size: 1rem; color: var(--nx-forest);">
                        <i class="fas fa-pen-to-square me-2"></i>Edit Informasi &amp; Harga Acuan Kategori
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" name="nama_kategori" id="editKatNama" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Satuan <span class="text-danger">*</span></label>
                            <input type="text" name="satuan" id="editKatSatuan" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Deskripsi</label>
                        <input type="text" name="deskripsi" id="editKatDeskripsi" class="form-control">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Rentang Pasar Bawah (Rp)</label>
                            <input type="number" name="harga_min" id="editKatMin" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Rentang Pasar Atas (Rp)</label>
                            <input type="number" name="harga_max" id="editKatMax" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-success">Harga Acuan RW Saat Ini (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="harga_acuan" id="editKatAcuan" class="form-control fw-bold border-success" required>
                        <small class="text-muted" style="font-size: 0.72rem;">Perubahan harga akan otomatis dicatat dalam riwayat perubahan harga dan tidak mengubah nominal transaksi masa lalu.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Tips Pemilahan Warga</label>
                        <textarea name="tips_pemilahan" id="editKatTips" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold">Catatan Alasan Perubahan (Opsional)</label>
                        <input type="text" name="catatan_perubahan" class="form-control" placeholder="Contoh: Penyesuaian kenaikan harga pasar daur ulang...">
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

<!-- 7. MODAL TAMBAH ARTIKEL EDUKASI (ADMIN RW) -->
<div class="modal fade" id="modalTambahEdukasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form action="{{ route('bank_sampah.store_edukasi') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="font-size: 1rem; color: var(--nx-forest);">
                        <i class="fas fa-feather-pointed me-2"></i>Tulis Materi Edukasi Lingkungan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Judul Artikel <span class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control" placeholder="Contoh: 5 Cara Memilah Sampah Dapur untuk Kompos" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Kategori Materi <span class="text-danger">*</span></label>
                            <select name="kategori_materi" class="form-select" required>
                                <option value="Pemilahan Sampah">Pemilahan Sampah</option>
                                <option value="Kompos Organik">Kompos Organik</option>
                                <option value="Limbah B3 & Elektronik">Limbah B3 & Elektronik</option>
                                <option value="Zero Waste">Zero Waste</option>
                                <option value="Minyak Jelantah">Minyak Jelantah</option>
                                <option value="Ekonomi Sirkular">Ekonomi Sirkular</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Deskripsi Ringkas (Maks. 500 Karakter) <span class="text-danger">*</span></label>
                        <textarea name="deskripsi_singkat" class="form-control" rows="2" placeholder="Ringkasan isi panduan yang menarik untuk warga..." required></textarea>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold">Isi Panduan Lengkap <span class="text-danger">*</span></label>
                        <textarea name="isi_materi" class="form-control" rows="8" placeholder="Tuliskan materi edukasi, tips terperinci, dan langkah-langkah praktis untuk warga..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">Terbitkan Artikel</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- CHART SCRIPTS & INTERACTION -->
@if($activeTab === 'ringkasan')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const months = @json($chartMonths);

    // 1. Chart Bulanan Berat & Nilai
    const ctxBulanan = document.getElementById('chartBulananSampah');
    if (ctxBulanan) {
        new Chart(ctxBulanan.getContext('2d'), {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Berat Sampah (Kg)',
                        data: @json($chartBeratBulanan),
                        borderColor: '#285943',
                        backgroundColor: 'rgba(40, 89, 67, 0.1)',
                        fill: true,
                        tension: 0.3,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Nilai Tabungan (Rp)',
                        data: @json($chartNilaiBulanan),
                        borderColor: '#1F567A',
                        borderDash: [5, 5],
                        fill: false,
                        tension: 0.3,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: { display: true, text: 'Kg' }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: { drawOnChartArea: false },
                        title: { display: true, text: 'Rupiah' }
                    }
                }
            }
        });
    }

    // 2. Chart Komposisi Kategori Doughnut
    const ctxDoughnut = document.getElementById('chartKategoriDoughnut');
    if (ctxDoughnut) {
        const kb = @json($kategoriBreakdown);
        const labels = Object.values(kb).map(x => x.nama);
        const dataVals = Object.values(kb).map(x => x.berat);
        const bgColors = Object.values(kb).map(x => x.color);

        new Chart(ctxDoughnut.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: dataVals,
                    backgroundColor: bgColors,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right' }
                }
            }
        });
    }

    // 3. Chart Perbandingan Antar-RT
    const ctxRt = document.getElementById('chartPerbandinganRt');
    if (ctxRt) {
        new Chart(ctxRt.getContext('2d'), {
            type: 'bar',
            data: {
                labels: @json($chartPerbandinganRt['labels']),
                datasets: [
                    {
                        label: 'Total Sampah (Kg)',
                        data: @json($chartPerbandinganRt['berat_kg']),
                        backgroundColor: '#285943',
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }
});
</script>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Auto-fill citizen info when selected from dropdown
    const wargaSelect = document.getElementById('modalWargaSelect');
    const namaInput = document.getElementById('modalNamaPenyetor');
    const rtSelect = document.getElementById('modalRtNumber');

    if (wargaSelect) {
        wargaSelect.addEventListener('change', function() {
            const opt = wargaSelect.options[wargaSelect.selectedIndex];
            if (opt.value) {
                if (namaInput) namaInput.value = opt.getAttribute('data-nama');
                if (rtSelect) rtSelect.value = opt.getAttribute('data-rt');
            }
        });
    }

    // 2. Auto-calculation of deposit total value
    const katSelect = document.getElementById('modalKategoriSelect');
    const beratInput = document.getElementById('modalBeratInput');
    const satuanLabel = document.getElementById('modalSatuanLabel');
    const displayTotal = document.getElementById('modalTotalNilaiDisplay');
    const badgeAcuan = document.getElementById('modalHargaAcuanBadge');

    function calculateTotal() {
        if (!katSelect) return;
        const opt = katSelect.options[katSelect.selectedIndex];
        const harga = parseFloat(opt.getAttribute('data-harga') || 0);
        const satuan = opt.getAttribute('data-satuan') || 'Kg';
        if (satuanLabel) satuanLabel.textContent = satuan;
        if (badgeAcuan) badgeAcuan.textContent = 'Rp ' + harga.toLocaleString('id-ID') + ' / ' + satuan;

        const berat = parseFloat(beratInput ? beratInput.value : 0) || 0;
        const total = Math.round(berat * harga);
        if (displayTotal) displayTotal.textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    if (katSelect) katSelect.addEventListener('change', calculateTotal);
    if (beratInput) beratInput.addEventListener('input', calculateTotal);
    calculateTotal();

    // 3. Verifikasi Modal triggers
    document.querySelectorAll('.btn-verifikasi-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const notx = this.getAttribute('data-notx');
            const nama = this.getAttribute('data-nama');
            const nilai = this.getAttribute('data-nilai');

            document.getElementById('verifNoTx').textContent = notx;
            document.getElementById('verifNama').textContent = nama;
            document.getElementById('verifNilai').textContent = nilai;

            const form = document.getElementById('formVerifikasiSetoran');
            form.action = `/bank-sampah/setoran/${id}/verifikasi`;

            const modal = new bootstrap.Modal(document.getElementById('modalVerifikasiSetoran'));
            modal.show();
        });
    });

    const keputusanSelect = document.getElementById('verifKeputusanSelect');
    const boxAlasan = document.getElementById('boxAlasanTolak');
    if (keputusanSelect && boxAlasan) {
        keputusanSelect.addEventListener('change', function() {
            boxAlasan.style.display = this.value === 'ditolak' ? 'block' : 'none';
        });
    }

    // 4. Baca Edukasi Modal
    document.querySelectorAll('.btn-baca-edukasi').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('bacaJudulModal').textContent = this.getAttribute('data-judul');
            document.getElementById('bacaKategoriBadge').textContent = this.getAttribute('data-kategori');
            document.getElementById('bacaIsiContent').textContent = this.getAttribute('data-isi');
            new bootstrap.Modal(document.getElementById('modalBacaEdukasi')).show();
        });
    });

    // 5. Edit Kategori Modal (Admin RW)
    document.querySelectorAll('.btn-edit-kategori').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');
            const deskripsi = this.getAttribute('data-deskripsi') || '';
            const satuan = this.getAttribute('data-satuan');
            const min = this.getAttribute('data-min');
            const max = this.getAttribute('data-max');
            const acuan = this.getAttribute('data-acuan');
            const tips = this.getAttribute('data-tips') || '';

            const form = document.getElementById('formEditKategori');
            if (form) {
                form.action = `/bank-sampah/kategori/${id}`;
                document.getElementById('editKatNama').value = nama;
                document.getElementById('editKatSatuan').value = satuan;
                document.getElementById('editKatDeskripsi').value = deskripsi;
                document.getElementById('editKatMin').value = min;
                document.getElementById('editKatMax').value = max;
                document.getElementById('editKatAcuan').value = acuan;
                document.getElementById('editKatTips').value = tips;

                new bootstrap.Modal(document.getElementById('modalEditKategori')).show();
            }
        });
    });
});
</script>
@endsection
