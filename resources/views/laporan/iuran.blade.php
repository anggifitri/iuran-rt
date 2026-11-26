@extends('layouts.app')

@section('title', 'Iuran Warga — NexaNest')

@section('content')
@php
    $user = Auth::user();
    $isAdminRw = $user->isAdminRw();
    $isAdminRt = $user->isAdminRt();
@endphp

<!-- HEADER HALAMAN -->
<div class="nx-page-header">
    <div>
        <span class="badge rounded-pill mb-2 px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
            <i class="fas fa-file-invoice-dollar me-1"></i> Manajemen Iuran {{ $isAdminRt ? 'RT ' . $user->rt_number : 'RW 018' }}
        </span>
        <h1 class="nx-page-title">Iuran Warga</h1>
        <p class="nx-page-subtitle">Pusat pengelolaan jenis iuran, penetapan tagihan, konfirmasi pembayaran, tunggakan, dan rekapitulasi kepatuhan warga.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <button type="button" class="btn btn-outline-success" id="btnOpenScanEktp" data-bs-toggle="modal" data-bs-target="#modalScanEktpIuran">
            <i class="fas fa-id-card me-1"></i> Scan E-KTP Warga
        </button>
        <a href="{{ route('laporan.cetak_iuran', ['bulan' => $bulan, 'tahun' => $tahun, 'rt_number' => $rtFilter]) }}" target="_blank" class="btn btn-outline-secondary">
            <i class="fas fa-file-pdf me-1"></i> Cetak Rekap PDF
        </a>
        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalQuickPay">
            <i class="fas fa-hand-holding-dollar me-1"></i> Catat Pembayaran Tunai
        </button>
        @if($isAdminRw)
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahIuran">
                <i class="fas fa-plus-circle me-1"></i> Tambah Jenis Iuran
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

<!-- 1. SUMMARY CARDS IURAN (METRIK UTAMA - STANDAR NEXANEST) -->
<div class="row g-3 mb-4">
    <!-- Total Tagihan -->
    <div class="col-xl-3 col-md-6 col-sm-6">
        <x-stat-card 
            title="Target Tagihan Bulan Ini" 
            value="Rp {{ number_format($totalTagihanNominal, 0, ',', '.') }}" 
            sub="Periode {{ sprintf('%02d/%d', $bulan, $tahun) }}" 
            icon="fas fa-file-invoice-dollar" 
            theme="primary" 
            valColor="#1F517F" 
        />
    </div>

    <!-- Terkumpul -->
    <div class="col-xl-3 col-md-6 col-sm-6">
        <x-stat-card 
            title="Iuran Terkumpul" 
            value="Rp {{ number_format($totalPembayaranDiterima, 0, ',', '.') }}" 
            sub="{{ $countLunas }} Kepala Keluarga Lunas" 
            icon="fas fa-circle-check" 
            theme="forest" 
            valColor="var(--nx-forest)" 
        />
    </div>

    <!-- Tunggakan -->
    <div class="col-xl-3 col-md-6 col-sm-6">
        <x-stat-card 
            title="Total Tunggakan" 
            value="Rp {{ number_format($totalTunggakanNominal, 0, ',', '.') }}" 
            sub="{{ $countBelumLunas }} KK Belum Menyelesaikan" 
            icon="fas fa-hourglass-half" 
            theme="danger" 
            valColor="var(--nx-danger)" 
        />
    </div>

    <!-- Tingkat Kepatuhan -->
    <div class="col-xl-3 col-md-6 col-sm-6">
        <div class="nx-stat-card nx-stat-card--forest h-100">
            <div class="nx-stat-content w-100">
                <span class="nx-stat-label">Kepatuhan Warga</span>
                <div class="d-flex align-items-baseline gap-2 my-1">
                    <h3 class="nx-stat-value" style="color: var(--nx-forest) !important;">{{ $persenLunas }}%</h3>
                    <small class="text-muted fw-bold" style="font-size: 0.72rem;">Target 100%</small>
                </div>
                <div class="progress mt-2" style="height: 6px; background: var(--nx-bg); border-radius: 999px;">
                    <div class="progress-bar" role="progressbar" style="width: {{ $persenLunas }}%; background-color: var(--nx-forest); border-radius: 999px;" aria-valuenow="{{ $persenLunas }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
            <div class="nx-stat-icon nx-stat-icon--forest ms-2">
                <i class="fas fa-chart-pie"></i>
            </div>
        </div>
    </div>
</div>

<!-- 2. ANTREAN KONFIRMASI PEMBAYARAN WARGA (JIKA ADA PENDING) -->
@if($antreanKonfirmasi->count() > 0)
    <div class="card mb-4 border-warning shadow-sm" style="border-width: 2px;">
        <div class="card-header bg-warning bg-opacity-10 p-3.5 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-warning text-dark px-2.5 py-1 fw-bold">
                    <i class="fas fa-bell me-1"></i> {{ $antreanKonfirmasi->count() }} Menunggu Konfirmasi
                </span>
                <h5 class="fw-bold mb-0" style="font-size: 0.95rem;">Antrean Pembayaran Iuran Warga</h5>
            </div>
            <small class="text-muted">Pembayaran yang dikonfirmasi otomatis dicatat ke Transaksi Kas tanpa duplikasi.</small>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Tgl Pengajuan</th>
                        <th>Nama Warga</th>
                        <th>Wilayah</th>
                        <th>Jenis Iuran &amp; Periode</th>
                        <th>Nominal</th>
                        <th>Metode &amp; Bukti</th>
                        <th class="pe-4 text-end">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($antreanKonfirmasi as $ak)
                        <tr>
                            <td class="ps-4 small text-muted">
                                {{ $ak->payment_date ? $ak->payment_date->translatedFormat('d M Y') : $ak->created_at->translatedFormat('d M Y') }}
                            </td>
                            <td>
                                <strong class="d-block" style="color: var(--nx-text);">{{ $ak->warga->nama ?? '-' }}</strong>
                                <small class="text-muted">Blok {{ $ak->warga->blok_rumah ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="badge rounded-pill px-2 py-0.5" style="background: var(--nx-powder); color: #1F517F;">RT {{ $ak->rt_number }}</span>
                            </td>
                            <td>
                                <span class="fw-semibold small d-block">{{ $ak->iuran->name ?? 'Iuran Lingkungan' }}</span>
                                <small class="text-muted">Periode: {{ sprintf('%02d/%d', $ak->bulan, $ak->tahun) }}</small>
                            </td>
                            <td>
                                <strong class="text-success">Rp {{ number_format($ak->amount_paid, 0, ',', '.') }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border me-1">{{ $ak->payment_method ?: 'Transfer' }}</span>
                                @if($ak->proof_image)
                                    <a href="{{ asset('storage/' . $ak->proof_image) }}" target="_blank" class="btn btn-xs btn-outline-primary py-0.5 px-2" style="font-size: 0.72rem;">
                                        <i class="fas fa-image me-1"></i>Bukti
                                    </a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-flex justify-content-end gap-1.5">
                                    <form action="{{ route('iuran.confirm', $ak->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Sahkan pembayaran iuran ini? Transaksi kas akan otomatis terbuat.')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success px-2.5 py-1" title="Konfirmasi & Sahkan">
                                            <i class="fas fa-check me-1"></i> Konfirmasi
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-outline-danger px-2.5 py-1" onclick="openRejectModal({{ $ak->id }}, '{{ $ak->warga->nama ?? 'Warga' }}')">
                                        <i class="fas fa-times me-1"></i> Tolak
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

<!-- 3. MASTER JENIS IURAN (ACCORDION) -->
<div class="card mb-4">
    <div class="card-header bg-white p-3.5 d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#collapseJenisIuran" style="cursor: pointer;">
        <div class="d-flex align-items-center gap-2">
            <span class="rounded-3 d-inline-flex align-items-center justify-content-center"
                  style="width: 34px; height: 34px; background: var(--nx-mint); color: var(--nx-forest);">
                <i class="fas fa-tags"></i>
            </span>
            <div>
                <h5 class="fw-bold mb-0" style="font-size: 0.95rem;">Master Jenis Iuran Aktif</h5>
                <small class="text-muted">Daftar tarif dan regulasi iuran resmi yang berlaku di lingkungan RW 018</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge rounded-pill px-2.5 py-1" style="background: var(--nx-bg); color: var(--nx-text); border: 1px solid var(--nx-border);">
                {{ $masterIurans->count() }} Jenis Terdaftar
            </span>
            <i class="fas fa-chevron-down text-muted"></i>
        </div>
    </div>
    <div class="collapse show" id="collapseJenisIuran">
        <div class="p-3.5 border-top">
            <div class="row g-3">
                @forelse($masterIurans as $mi)
                    <div class="col-md-4 col-sm-6">
                        <div class="p-3 rounded-3 h-100" style="background: var(--nx-bg); border: 1px solid var(--nx-border);">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="fw-bold mb-0 text-truncate" title="{{ $mi->name }}">{{ $mi->name }}</h6>
                                <span class="badge rounded-pill px-2 py-0.5 {{ $mi->type === 'wajib' ? 'bg-primary' : 'bg-secondary' }}" style="font-size: 0.68rem;">
                                    {{ ucfirst($mi->type) }}
                                </span>
                            </div>
                            <h5 class="fw-bold mb-1" style="color: var(--nx-forest);">
                                Rp {{ number_format($mi->amount, 0, ',', '.') }}
                                <small class="text-muted fw-normal" style="font-size: 0.75rem;">/ {{ $mi->periode ?: 'bulan' }}</small>
                            </h5>
                            <p class="text-muted small mb-2 text-truncate" style="font-size: 0.75rem;" title="{{ $mi->description }}">
                                {{ $mi->description ?: 'Iuran operasional berkala lingkungan.' }}
                            </p>
                            <div class="d-flex justify-content-between align-items-center pt-2 border-top text-muted" style="font-size: 0.72rem;">
                                <span><i class="far fa-calendar-alt me-1"></i> Tempo: tgl {{ $mi->due_date ? date('d', strtotime($mi->due_date)) : '10' }}</span>
                                <span class="badge {{ $mi->is_active ? 'bg-success' : 'bg-secondary' }}" style="font-size: 0.65rem;">
                                    {{ $mi->is_active ? 'Aktif' : 'Non-Aktif' }}
                                </span>
                            </div>
                            @if($isAdminRw)
                                <div class="d-flex justify-content-end align-items-center gap-1.5 mt-2 pt-1 border-top">
                                    <button type="button" class="btn btn-xs btn-outline-primary py-0.5 px-2" style="font-size: 0.72rem;"
                                            onclick='openEditIuranModal(@json($mi))'>
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </button>
                                    <form action="{{ route('iuran.jenis.toggle', $mi->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-xs {{ $mi->is_active ? 'btn-outline-warning' : 'btn-outline-success' }} py-0.5 px-2" style="font-size: 0.72rem;">
                                            <i class="fas {{ $mi->is_active ? 'fa-pause' : 'fa-play' }} me-1"></i>{{ $mi->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-3 text-muted small">Belum ada jenis iuran terdaftar.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- 4. FILTER BULAN, TAHUN, DAN WILAYAH RT -->
<div class="card p-3.5 mb-4">
    <form method="GET" action="{{ route('laporan.iuran') }}" class="row g-2.5 align-items-end">
        <div class="col-lg-3 col-md-4">
            <label class="form-label small fw-bold text-muted mb-1">Periode Bulan</label>
            <select name="bulan" class="form-select form-select-sm">
                @foreach(['1'=>'Januari','2'=>'Februari','3'=>'Maret','4'=>'April','5'=>'Mei','6'=>'Juni','7'=>'Juli','8'=>'Agustus','9'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $k => $n)
                    <option value="{{ $k }}" {{ (int)$bulan === (int)$k ? 'selected' : '' }}>{{ $n }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-lg-2 col-md-3">
            <label class="form-label small fw-bold text-muted mb-1">Tahun</label>
            <select name="tahun" class="form-select form-select-sm">
                @foreach($daftarTahun as $t)
                    <option value="{{ $t }}" {{ (int)$tahun === (int)$t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>

        @if($isAdminRw)
            <div class="col-lg-3 col-md-5">
                <label class="form-label small fw-bold text-muted mb-1">Wilayah RT</label>
                <select name="rt_number" class="form-select form-select-sm">
                    <option value="all">Semua Wilayah RT (006 - 010)</option>
                    @foreach($rtList as $rt)
                        <option value="{{ $rt }}" {{ $rtFilter === $rt ? 'selected' : '' }}>RT {{ $rt }}</option>
                    @endforeach
                </select>
            </div>
        @else
            <div class="col-lg-3 col-md-5">
                <label class="form-label small fw-bold text-muted mb-1">Wilayah RT</label>
                <input type="text" class="form-control form-control-sm bg-light" value="RT {{ $user->rt_number }}" readonly>
            </div>
        @endif

        <div class="col-lg-2 col-md-4">
            <button type="submit" class="btn btn-sm btn-primary w-100">
                <i class="fas fa-filter me-1"></i> Terapkan Filter
            </button>
        </div>
    </form>
</div>

<!-- 5. TABEL REKAP KEPATUHAN KELUARGA (KK) (STANDAR TABEL NEXANEST) -->
<div class="card nx-table-card overflow-hidden shadow-xs border-0 mb-4">
    <div class="card-header p-3.5 bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="fw-bold mb-0 text-dark" style="font-size: 0.96rem;">
                <i class="fas fa-users-viewfinder text-primary me-2"></i>Status Pembayaran Iuran Kepala Keluarga
            </h5>
            <small class="text-muted" style="font-size: 0.75rem;">Rekap kewajiban iuran seluruh KK wilayah {{ $isAdminRt ? 'RT ' . $user->rt_number : ($rtFilter && $rtFilter !== 'all' ? 'RT ' . $rtFilter : 'RW 018') }} periode {{ sprintf('%02d/%d', $bulan, $tahun) }}</small>
        </div>
        <span class="badge rounded-pill px-3 py-1.5" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-weight: 700; font-size: 0.74rem;">
            {{ count($rows) }} Kepala Keluarga Terdaftar
        </span>
    </div>

    <div class="table-responsive nx-table-responsive">
        <table class="table table-hover align-middle nx-table mb-0">
            <thead>
                <tr>
                    <th class="ps-4" style="width: 25%;">Kepala Keluarga</th>
                    <th style="width: 10%;">Wilayah</th>
                    <th style="width: 15%;">Tagihan</th>
                    <th style="width: 14%;">Status</th>
                    <th style="width: 14%;">Tanggal Bayar</th>
                    <th style="width: 10%;" class="text-center">Histori 3 Bln</th>
                    <th class="pe-4 text-end" style="width: 12%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                    @php
                        $w = $row['warga'];
                        $isLunas = $row['status'] === 'Lunas';
                        $isPending = $row['status'] === 'Menunggu Konfirmasi';
                    @endphp
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                     style="width: 36px; height: 36px; font-size: 0.8rem; background: var(--nx-mint); color: var(--nx-forest);">
                                    {{ strtoupper(substr($w->nama, 0, 2)) }}
                                </div>
                                <div>
                                    <strong class="d-block" style="color: var(--nx-text); font-size: 0.88rem;">{{ $w->nama }}</strong>
                                    <small class="text-muted">Blok {{ $w->blok_rumah ?? '-' }} · No. {{ $w->nomor_rumah ?? '-' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge rounded-pill px-2 py-0.5" style="background: var(--nx-powder); color: #1F517F; font-size: 0.72rem;">RT {{ $w->rt_number }}</span>
                        </td>
                        <td>
                            <strong style="font-size: 0.88rem; color: var(--nx-text);">Rp {{ number_format($row['nominal_tagihan'], 0, ',', '.') }}</strong>
                            <small class="text-muted d-block" style="font-size: 0.7rem;">Iuran Terpadu</small>
                        </td>
                        <td>
                            @if($isLunas)
                                <x-status-badge status="lunas" label="Lunas" />
                            @elseif($isPending)
                                <x-status-badge status="menunggu" label="Menunggu" />
                            @else
                                <x-status-badge status="tunggakan" label="Belum Bayar" />
                            @endif
                        </td>
                        <td class="small fw-semibold text-muted">
                            @if($row['tanggal_bayar'])
                                {{ \Carbon\Carbon::parse($row['tanggal_bayar'])->translatedFormat('d M Y') }}
                                <small class="d-block text-muted" style="font-size: 0.7rem;">via {{ $row['metode_bayar'] }}</small>
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1.5" title="Indikator pembayaran 3 bulan sebelumnya">
                                @foreach($row['history'] as $h)
                                    @if($h['paid'])
                                        <span class="d-inline-block rounded-circle" style="width: 10px; height: 10px; background: var(--nx-forest);" title="{{ $h['month'] }}: Lunas"></span>
                                    @else
                                        <span class="d-inline-block rounded-circle" style="width: 10px; height: 10px; background: #CBD5E1;" title="{{ $h['month'] }}: Belum Bayar"></span>
                                    @endif
                                @endforeach
                            </div>
                        </td>
                        <td class="pe-4 text-end">
                            @if($isLunas && $row['pembayaran_id'])
                                <a href="{{ route('iuran.kuitansi', $row['pembayaran_id']) }}" target="_blank" class="btn btn-sm btn-outline-success px-2.5 py-1" style="border-radius: 8px; font-size: 0.78rem;" title="Lihat Kuitansi Digital">
                                    <i class="fas fa-receipt me-1"></i> Kuitansi
                                </a>
                            @elseif(!$isLunas)
                                <button type="button" class="btn btn-sm btn-primary px-2.5 py-1" style="border-radius: 8px; font-size: 0.78rem;" onclick="openDirectPayModal({{ $w->id }}, '{{ $w->nama }}', '{{ $w->rt_number }}', {{ $row['nominal_tagihan'] }})">
                                    <i class="fas fa-hand-holding-dollar me-1"></i> Bayar
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-0 border-0">
                            <x-empty-state
                                icon="fas fa-users-slash"
                                title="Tidak Ada Data Warga"
                                message="Belum ada kepala keluarga terdaftar di wilayah ini untuk periode yang dipilih."
                            />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL QUICK PAY (CATAT PEMBAYARAN TUNAI WARGA) -->
<div class="modal fade" id="modalQuickPay" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('iuran.bayar') }}" class="modal-content border-0 shadow">
            @csrf
            <div class="modal-header border-bottom py-3">
                <h6 class="modal-title fw-bold">
                    <i class="fas fa-hand-holding-dollar text-primary me-2"></i>Catat Pembayaran Iuran Warga
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Pilih Kepala Keluarga <span class="text-danger">*</span></label>
                    <select name="warga_id" id="quickPayWargaId" class="form-select form-select-sm" required>
                        <option value="">-- Pilih Warga --</option>
                        @foreach($rows as $r)
                            <option value="{{ $r['warga']->id }}" data-rt="{{ $r['warga']->rt_number }}" data-nominal="{{ $r['nominal_tagihan'] }}">
                                {{ $r['warga']->nama }} (RT {{ $r['warga']->rt_number }} · Blok {{ $r['warga']->blok_rumah ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Bulan Periode <span class="text-danger">*</span></label>
                        <select name="bulan" class="form-select form-select-sm" required>
                            @foreach(['1'=>'Januari','2'=>'Februari','3'=>'Maret','4'=>'April','5'=>'Mei','6'=>'Juni','7'=>'Juli','8'=>'Agustus','9'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $k => $n)
                                <option value="{{ $k }}" {{ (int)$bulan === (int)$k ? 'selected' : '' }}>{{ $n }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Tahun <span class="text-danger">*</span></label>
                        <input type="number" name="tahun" class="form-control form-control-sm" value="{{ $tahun }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Jumlah Diterima (Rp) <span class="text-danger">*</span></label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="amount_paid" id="quickPayAmount" class="form-control" value="100000" required min="1000">
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Metode <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select form-select-sm" required>
                            <option value="Tunai">Tunai / Cash</option>
                            <option value="Transfer Bank">Transfer Bank</option>
                            <option value="QRIS">QRIS Lingkungan</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Tanggal Diterima <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>

                <div class="mb-2">
                    <label class="form-label small fw-bold">Catatan / Keterangan</label>
                    <input type="text" name="notes" class="form-control form-control-sm" placeholder="Contoh: Diterima langsung oleh pengurus">
                </div>
            </div>
            <div class="modal-footer border-top py-2.5">
                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-sm btn-primary px-3">
                    <i class="fas fa-check-circle me-1"></i> Simpan &amp; Sahkan Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL TOLAK PEMBAYARAN -->
<div class="modal fade" id="modalRejectPayment" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" id="formRejectPayment" class="modal-content border-0 shadow">
            @csrf
            <div class="modal-header border-bottom py-3">
                <h6 class="modal-title fw-bold text-danger">
                    <i class="fas fa-times-circle me-2"></i>Tolak Pengajuan Pembayaran
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="mb-2">Tolak pengajuan pembayaran dari <strong id="rejectWargaNama"></strong>.</p>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Alasan Penolakan <span class="text-danger">*</span></label>
                    <textarea name="alasan" class="form-control form-control-sm" rows="3" required placeholder="Contoh: Bukti transfer tidak terbaca / nominal tidak sesuai"></textarea>
                </div>
            </div>
            <div class="modal-footer border-top py-2.5">
                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-sm btn-danger px-3">
                    <i class="fas fa-ban me-1"></i> Tolak Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>

@if($isAdminRw)
<!-- MODAL TAMBAH JENIS IURAN (KHUSUS RW) -->
<div class="modal fade" id="modalTambahIuran" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('iuran.jenis.store') }}" class="modal-content border-0 shadow">
            @csrf
            <div class="modal-header border-bottom py-3">
                <h6 class="modal-title fw-bold">
                    <i class="fas fa-plus-circle text-primary me-2"></i>Tambah Jenis Iuran Baru
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Nama Jenis Iuran <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control form-control-sm" required placeholder="Contoh: Iuran Keamanan Malam">
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Nominal (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control form-control-sm" required placeholder="50000" min="1000">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Periode Penagihan <span class="text-danger">*</span></label>
                        <select name="periode" class="form-select form-select-sm" required>
                            <option value="bulanan">Bulanan</option>
                            <option value="mingguan">Mingguan</option>
                            <option value="tahunan">Tahunan</option>
                            <option value="sekali_bayar">Sekali Bayar</option>
                        </select>
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Sifat Iuran <span class="text-danger">*</span></label>
                        <select name="type" class="form-select form-select-sm" required>
                            <option value="wajib">Wajib Seluruh KK</option>
                            <option value="sukarela">Sukarela / Donasi</option>
                            <option value="kebersihan">Kebersihan</option>
                            <option value="keamanan">Keamanan</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Batas Bayar / Tempo <span class="text-danger">*</span></label>
                        <input type="date" name="due_date" class="form-control form-control-sm" value="{{ date('Y-m-10') }}" required>
                    </div>
                </div>

                <div class="mb-2">
                    <label class="form-label small fw-bold">Deskripsi / Peruntukan Dana</label>
                    <textarea name="description" class="form-control form-control-sm" rows="2" placeholder="Jelaskan tujuan dan pemanfaatan iuran ini..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-top py-2.5">
                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-sm btn-primary px-3">
                    <i class="fas fa-save me-1"></i> Simpan Jenis Iuran
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT JENIS IURAN (KHUSUS RW) -->
<div class="modal fade" id="modalEditIuran" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" id="formEditIuran" class="modal-content border-0 shadow">
            @csrf
            @method('PUT')
            <div class="modal-header border-bottom py-3">
                <h6 class="modal-title fw-bold">
                    <i class="fas fa-edit text-primary me-2"></i>Edit Jenis Iuran
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Nama Jenis Iuran <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="editIuranName" class="form-control form-control-sm" required>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Nominal (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" id="editIuranAmount" class="form-control form-control-sm" required min="1000">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Periode Penagihan <span class="text-danger">*</span></label>
                        <select name="periode" id="editIuranPeriode" class="form-select form-select-sm" required>
                            <option value="bulanan">Bulanan</option>
                            <option value="mingguan">Mingguan</option>
                            <option value="tahunan">Tahunan</option>
                            <option value="sekali_bayar">Sekali Bayar</option>
                        </select>
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Sifat Iuran <span class="text-danger">*</span></label>
                        <select name="type" id="editIuranType" class="form-select form-select-sm" required>
                            <option value="wajib">Wajib Seluruh KK</option>
                            <option value="sukarela">Sukarela / Donasi</option>
                            <option value="kebersihan">Kebersihan</option>
                            <option value="keamanan">Keamanan</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Batas Bayar / Tempo <span class="text-danger">*</span></label>
                        <input type="date" name="due_date" id="editIuranDueDate" class="form-control form-control-sm">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Status Aktif</label>
                    <select name="is_active" id="editIuranIsActive" class="form-select form-select-sm">
                        <option value="1">Aktif</option>
                        <option value="0">Non-Aktif</option>
                    </select>
                </div>

                <div class="mb-2">
                    <label class="form-label small fw-bold">Deskripsi / Peruntukan Dana</label>
                    <textarea name="description" id="editIuranDesc" class="form-control form-control-sm" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer border-top py-2.5">
                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-sm btn-primary px-3">
                    <i class="fas fa-save me-1"></i> Perbarui Iuran
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<!-- MODAL IDENTIFIKASI CEPAT SETORAN VIA E-KTP (TAHAP 3 MULTI-FUNGSI RFID) -->
<div class="modal fade" id="modalScanEktpIuran" tabindex="-1" aria-labelledby="modalScanEktpIuranLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4" style="background: var(--nx-card-bg); color: var(--nx-text);">
            <!-- Modal Header -->
            <div class="modal-header border-bottom py-3 px-4" style="border-color: var(--nx-border) !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width: 44px; height: 44px; background: rgba(40, 89, 67, 0.12); color: var(--nx-forest);">
                        <i class="fas fa-id-card-clip fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="modalScanEktpIuranLabel" style="color: var(--nx-text);">
                            Identifikasi Cepat Setoran via E-KTP
                        </h5>
                        <small class="text-muted">NodeMCU ESP32 • Reader RFID RC522 (13.56 MHz)</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-4">
                <!-- Status Mode Badge (Isolasi Smart Gate) -->
                <div class="d-flex align-items-center justify-content-between p-2.5 px-3 rounded-3 mb-3"
                     style="background: rgba(40, 89, 67, 0.08); border: 1px solid rgba(40, 89, 67, 0.2); color: var(--nx-forest);">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-shield-halved"></i>
                        <span class="small fw-semibold">Mode Pembayaran Iuran Aktif — Tap kartu tidak akan membuka gerbang</span>
                    </div>
                    <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-2.5 py-1" style="font-size: 0.72rem;">
                        <i class="fas fa-circle-dot fa-fade me-1"></i> Sensor Siap
                    </span>
                </div>

                <!-- STATE 1: SCANNER BOX (WAITING FOR TAP) -->
                <div id="iuranScanWaitingBox" class="text-center p-4 rounded-4 border position-relative overflow-hidden mb-3"
                     style="border: 2px dashed var(--nx-sage) !important; background: var(--nx-bg); transition: all 0.3s ease;">
                    <div class="mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center"
                         style="width: 72px; height: 72px; background: rgba(40, 89, 67, 0.12); color: var(--nx-forest);">
                        <i class="fas fa-wifi fa-2x fa-beat-fade"></i>
                    </div>
                    <h6 class="fw-bold mb-1" style="color: var(--nx-text);">Menunggu Tempelan E-KTP Warga...</h6>
                    <p class="text-muted small mb-0">
                        Dekatkan fisik E-KTP warga pada sensor <strong>RFID RC522 (NodeMCU ESP32)</strong> untuk identifikasi profil dan tagihan instan.
                    </p>
                </div>

                <!-- STATE 2: RESIDENT CARD PROFILE (REGISTERED) -->
                <div id="iuranScanResultCard" class="d-none">
                    <div class="card border rounded-4 overflow-hidden mb-3" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
                        <div class="p-3.5 border-bottom d-flex align-items-center justify-content-between"
                             style="background: rgba(40, 89, 67, 0.06); border-color: var(--nx-border) !important;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold fs-5"
                                     id="iuranResAvatar"
                                     style="width: 48px; height: 48px; background: var(--nx-forest);">
                                    W
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <h5 class="fw-bold mb-0" id="iuranResNama" style="color: var(--nx-text);">-</h5>
                                        <span class="badge rounded-pill" id="iuranResRoleBadge" style="background: var(--nx-forest); color: #fff; font-size: 0.68rem;">Kepala Keluarga</span>
                                    </div>
                                    <small class="text-muted" id="iuranResMeta">-</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-light text-dark border font-monospace px-2.5 py-1.5" id="iuranResUidBadge">
                                    <i class="fas fa-id-card me-1 text-primary"></i>UID: -
                                </span>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="p-3 rounded-3 h-100" style="background: var(--nx-bg); border: 1px solid var(--nx-border);">
                                        <span class="small text-muted d-block mb-1 fw-semibold">Status Iuran Periode Ini</span>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge px-3 py-1.5" id="iuranResStatusBadge" style="font-size: 0.85rem;">-</span>
                                            <span class="small fw-semibold text-muted" id="iuranResPeriode">-</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="p-3 rounded-3 h-100" style="background: var(--nx-bg); border: 1px solid var(--nx-border);">
                                        <span class="small text-muted d-block mb-1 fw-semibold">Riwayat Pembayaran Terakhir</span>
                                        <div class="fw-bold" id="iuranResLastPayment" style="color: var(--nx-text);">-</div>
                                        <small class="text-muted" id="iuranResLastAmount">-</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STATE 3: UNREGISTERED CARD ALERT -->
                <div id="iuranScanUnregisteredCard" class="d-none">
                    <div class="alert alert-warning rounded-4 p-4 border-warning-subtle mb-0 d-flex align-items-start gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-warning"
                             style="width: 44px; height: 44px; background: rgba(245, 158, 11, 0.15); flex-shrink: 0;">
                            <i class="fas fa-exclamation-triangle fa-lg"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="fw-bold text-dark mb-1">Kartu E-KTP Belum Terdaftar</h6>
                            <p class="small text-muted mb-3">
                                Kartu dengan UID <strong id="unregisteredUid" class="font-monospace text-dark">-</strong> berhasil dibaca oleh reader RFID, namun belum terhubung ke data warga manapun di RT/RW. Silakan rekam E-KTP warga terlebih dahulu.
                            </p>
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('warga.index') }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-user-plus me-1"></i> Buka Data Warga
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnIuranScanAgainUnregistered">
                                    <i class="fas fa-rotate me-1"></i> Scan Kartu Lain
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer border-top py-2.5 px-4" style="border-color: var(--nx-border) !important;">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnIuranSimulateTap" title="Simulasikan Tap E-KTP untuk pengujian tanpa perangkat fisik">
                            <i class="fas fa-microchip me-1"></i> Simulasi Tap E-KTP
                        </button>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-sm btn-outline-primary d-none" id="btnIuranScanAgain">
                            <i class="fas fa-rotate me-1"></i> Scan Ulang
                        </button>
                        <button type="button" class="btn btn-sm btn-success d-none" id="btnIuranProceedPay">
                            <i class="fas fa-hand-holding-dollar me-1"></i> Catat Setoran untuk Warga Ini
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let iuranPollInterval = null;
let iuranKeepAliveInterval = null;
let iuranScanOpenedAt = 0;
let currentScannedWarga = null;

const modalScanIuranEl = document.getElementById('modalScanEktpIuran');
const bsModalScanIuran = modalScanIuranEl ? new bootstrap.Modal(modalScanIuranEl) : null;
const waitingBox = document.getElementById('iuranScanWaitingBox');
const resultCard = document.getElementById('iuranScanResultCard');
const unregisteredCard = document.getElementById('iuranScanUnregisteredCard');
const btnProceedPay = document.getElementById('btnIuranProceedPay');
const btnScanAgain = document.getElementById('btnIuranScanAgain');
const btnScanAgainUnreg = document.getElementById('btnIuranScanAgainUnregistered');
const btnSimulateIuran = document.getElementById('btnIuranSimulateTap');

function setIuranScanWaiting() {
    waitingBox.classList.remove('d-none');
    resultCard.classList.add('d-none');
    unregisteredCard.classList.add('d-none');
    btnProceedPay.classList.add('d-none');
    btnScanAgain.classList.add('d-none');
    currentScannedWarga = null;
}

function activateIuranScanMode(mode) {
    fetch(`{{ route('iot.scan_mode') }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ mode: mode })
    }).catch(() => {});
}

function startIuranScanEngine() {
    stopIuranScanEngine();
    setIuranScanWaiting();
    iuranScanOpenedAt = Date.now() - 3000;

    // 1. Kunci mode scan ke 'iuran'
    activateIuranScanMode('iuran');
    iuranKeepAliveInterval = setInterval(function () {
        activateIuranScanMode('iuran');
    }, 45000);

    // 2. Polling ringan setiap 1.5 detik ke /iot/latest-scan?context=iuran
    iuranPollInterval = setInterval(function () {
        fetch(`{{ route('iot.latest_scan') }}?context=iuran&since=${iuranScanOpenedAt}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(res => {
            if (res.status === 'detected' && res.data && res.data.uid) {
                iuranScanOpenedAt = res.data.timestamp_ms;
                handleIuranScanDetected(res.data);
            }
        })
        .catch(() => {});
    }, 1500);
}

function stopIuranScanEngine() {
    if (iuranPollInterval) {
        clearInterval(iuranPollInterval);
        iuranPollInterval = null;
    }
    if (iuranKeepAliveInterval) {
        clearInterval(iuranKeepAliveInterval);
        iuranKeepAliveInterval = null;
    }
    // Reset kembali ke smart_gate
    activateIuranScanMode('smart_gate');
}

function handleIuranScanDetected(data) {
    // Hentikan polling sementara setelah kartu terbaca
    if (iuranPollInterval) {
        clearInterval(iuranPollInterval);
        iuranPollInterval = null;
    }

    if (data.registered && data.warga) {
        currentScannedWarga = data.warga;

        // Inisial Avatar
        const initials = data.warga.nama ? data.warga.nama.split(' ').map(n => n[0]).slice(0, 2).join('').toUpperCase() : 'W';
        document.getElementById('iuranResAvatar').textContent = initials;
        document.getElementById('iuranResNama').textContent = data.warga.nama;
        document.getElementById('iuranResRoleBadge').textContent = data.warga.hubungan_keluarga || 'Warga';
        document.getElementById('iuranResMeta').textContent = `RT ${data.warga.rt_number} / RW ${data.warga.rw_number || '018'} · Blok ${data.warga.blok_rumah || '-'}`;
        document.getElementById('iuranResUidBadge').innerHTML = `<i class="fas fa-id-card me-1 text-primary"></i>UID: ${data.uid}`;

        // Ringkasan Status Iuran
        const summary = data.iuran_summary || {};
        const statusBadge = document.getElementById('iuranResStatusBadge');
        statusBadge.className = `badge px-3 py-1.5 ${summary.status_badge || 'bg-secondary-subtle text-secondary'}`;
        statusBadge.textContent = summary.status_bulan_ini || 'Belum Bayar';

        document.getElementById('iuranResPeriode').textContent = `Periode ${summary.periode_bulan || ''}`;
        document.getElementById('iuranResLastPayment').textContent = summary.terakhir_bayar || 'Belum ada riwayat';
        document.getElementById('iuranResLastAmount').textContent = summary.terakhir_nominal ? `Nominal: ${summary.terakhir_nominal}` : '-';

        waitingBox.classList.add('d-none');
        unregisteredCard.classList.add('d-none');
        resultCard.classList.remove('d-none');
        btnProceedPay.classList.remove('d-none');
        btnScanAgain.classList.remove('d-none');
    } else {
        document.getElementById('unregisteredUid').textContent = data.uid || '-';
        waitingBox.classList.add('d-none');
        resultCard.classList.add('d-none');
        unregisteredCard.classList.remove('d-none');
        btnProceedPay.classList.add('d-none');
        btnScanAgain.classList.remove('d-none');
    }
}

if (modalScanIuranEl) {
    modalScanIuranEl.addEventListener('show.bs.modal', function () {
        startIuranScanEngine();
    });

    modalScanIuranEl.addEventListener('hidden.bs.modal', function () {
        stopIuranScanEngine();
    });
}

if (btnScanAgain) {
    btnScanAgain.addEventListener('click', function () {
        startIuranScanEngine();
    });
}

if (btnScanAgainUnreg) {
    btnScanAgainUnreg.addEventListener('click', function () {
        startIuranScanEngine();
    });
}

if (btnProceedPay) {
    btnProceedPay.addEventListener('click', function () {
        if (!currentScannedWarga) return;

        // Tutup modal scan
        bsModalScanIuran.hide();

        // Buka modal pembayaran Quick Pay
        const targetWargaId = currentScannedWarga.kk_id || currentScannedWarga.id;
        const sel = document.getElementById('quickPayWargaId');

        let found = false;
        if (sel) {
            for (let i = 0; i < sel.options.length; i++) {
                if (sel.options[i].value == targetWargaId || sel.options[i].value == currentScannedWarga.id) {
                    sel.selectedIndex = i;
                    found = true;
                    break;
                }
            }
            if (!found) {
                const opt = document.createElement('option');
                opt.value = currentScannedWarga.id;
                opt.textContent = `${currentScannedWarga.nama} (RT ${currentScannedWarga.rt_number} · Blok ${currentScannedWarga.blok_rumah || '-'})`;
                opt.selected = true;
                sel.appendChild(opt);
            }
        }

        const amountInput = document.getElementById('quickPayAmount');
        if (amountInput) {
            amountInput.value = 100000;
        }

        const quickPayModalEl = document.getElementById('modalQuickPay');
        if (quickPayModalEl) {
            setTimeout(() => {
                new bootstrap.Modal(quickPayModalEl).show();
            }, 300);
        }
    });
}

if (btnSimulateIuran) {
    btnSimulateIuran.addEventListener('click', function () {
        @php
            $sampleUid = null;
            foreach ($rows as $r) {
                if (!empty($r['warga']->rfid_uid)) {
                    $sampleUid = $r['warga']->rfid_uid;
                    break;
                }
            }
        @endphp
        const sampleUid = '{{ $sampleUid ?? "" }}';
        const randomHex = Array.from({ length: 7 }, () =>
            Math.floor(Math.random() * 256).toString(16).padStart(2, '0').toUpperCase()
        ).join('');
        const uidToSend = sampleUid ? sampleUid : randomHex;

        fetch(`{{ route('rfid.receive') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                uid: uidToSend,
                context: 'iuran',
                method: 'simulation'
            })
        })
        .then(r => r.json())
        .then(() => {
            // Polling akan otomatis menangkap dalam 1.5 detik
        })
        .catch(() => {});
    });
}

// Buka otomatis jika ada query parameter ?scan=1
document.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('scan') === '1' && bsModalScanIuran) {
        bsModalScanIuran.show();
    }
});
function openDirectPayModal(wargaId, nama, rt, nominal) {
    const sel = document.getElementById('quickPayWargaId');
    sel.value = wargaId;
    document.getElementById('quickPayAmount').value = nominal;
    new bootstrap.Modal(document.getElementById('modalQuickPay')).show();
}

function openRejectModal(id, nama) {
    document.getElementById('rejectWargaNama').textContent = nama;
    document.getElementById('formRejectPayment').action = `/iuran/reject/${id}`;
    new bootstrap.Modal(document.getElementById('modalRejectPayment')).show();
}

function openEditIuranModal(iuran) {
    const form = document.getElementById('formEditIuran');
    form.action = '/iuran/jenis/' + iuran.id;
    document.getElementById('editIuranName').value = iuran.name || '';
    document.getElementById('editIuranAmount').value = parseInt(iuran.amount) || 0;
    document.getElementById('editIuranPeriode').value = iuran.periode || 'bulanan';
    document.getElementById('editIuranType').value = iuran.type || 'wajib';
    if (iuran.due_date) {
        document.getElementById('editIuranDueDate').value = (typeof iuran.due_date === 'string' ? iuran.due_date.substring(0, 10) : '');
    }
    document.getElementById('editIuranIsActive').value = iuran.is_active ? '1' : '0';
    document.getElementById('editIuranDesc').value = iuran.description || '';
    new bootstrap.Modal(document.getElementById('modalEditIuran')).show();
}
</script>
@endpush
