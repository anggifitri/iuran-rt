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
@endsection

@push('scripts')
<script>
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
