@extends('layouts.app')

@section('title', 'Transaksi Kas — NexaNest')

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
            <i class="fas fa-wallet me-1"></i> Keuangan Lingkungan {{ $isAdminRt ? 'RT ' . $user->rt_number : 'RW 018' }}
        </span>
        <h1 class="nx-page-title">Transaksi Kas</h1>
        <p class="nx-page-subtitle">Kelola dan pantau seluruh pemasukan serta pengeluaran keuangan lingkungan {{ $isAdminRt ? 'RT ' . $user->rt_number : 'RW' }}.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('laporan.iuran', ['scan' => 1]) }}" class="btn btn-outline-success">
            <i class="fas fa-id-card me-1"></i> Scan E-KTP Warga
        </a>
        <a href="{{ route('laporan.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-chart-pie me-1"></i> Lihat Laporan Bulanan
        </a>
        <a href="{{ route('pembayaran.export', request()->query()) }}" class="btn btn-outline-secondary">
            <i class="fas fa-file-excel me-1"></i> Ekspor Laporan
        </a>
        <a href="{{ route('pembayaran.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i> Catat Transaksi Baru
        </a>
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

<!-- 1. RINGKASAN KEUANGAN AKTUAL (5 METRIC CARDS - STANDAR NEXANEST) -->
<div class="row g-3 mb-4">
    <!-- Saldo Kas Saat Ini -->
    <div class="col-xl-4 col-md-6">
        <div class="card p-3.5 h-100" style="background: linear-gradient(135deg, #285943 0%, #1e4533 100%); color: #ffffff; border: none; box-shadow: 0 4px 14px rgba(40,89,67,0.22); border-radius: 16px !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px; opacity: 0.85;">Saldo Kas Saat Ini</span>
                    <h2 class="fw-bold my-1 text-white" style="font-size: 1.75rem; letter-spacing: -0.5px;">
                        Rp {{ number_format($saldoKasSaatIni, 0, ',', '.') }}
                    </h2>
                    <div class="d-flex align-items-center gap-1.5" style="font-size: 0.76rem; opacity: 0.9;">
                        <i class="fas fa-shield-alt text-warning"></i>
                        <span>Kas Aktif &amp; Riil Terverifikasi</span>
                    </div>
                </div>
                <div class="rounded-3 d-flex align-items-center justify-content-center"
                     style="width: 48px; height: 48px; background: rgba(255,255,255,0.18); color: #ffffff;">
                    <i class="fas fa-vault fa-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Pemasukan -->
    <div class="col-xl-2 col-md-6 col-sm-6">
        <x-stat-card 
            title="Total Pemasukan" 
            value="+Rp {{ number_format($totalPemasukan, 0, ',', '.') }}" 
            sub="Kumulatif seluruh kas masuk" 
            icon="fas fa-arrow-down" 
            theme="forest" 
            valColor="var(--nx-forest)" 
        />
    </div>

    <!-- Total Pengeluaran -->
    <div class="col-xl-2 col-md-6 col-sm-6">
        <x-stat-card 
            title="Total Pengeluaran" 
            value="-Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}" 
            sub="Kumulatif operasional keluar" 
            icon="fas fa-arrow-up" 
            theme="danger" 
            valColor="var(--nx-danger)" 
        />
    </div>

    <!-- Pemasukan Bulan Ini -->
    <div class="col-xl-2 col-md-6 col-sm-6">
        <x-stat-card 
            title="Masuk Bulan Ini" 
            value="Rp {{ number_format($pemasukanBulanIni, 0, ',', '.') }}" 
            sub="{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}" 
            icon="fas fa-calendar-check" 
            theme="success" 
            valColor="#059669" 
        />
    </div>

    <!-- Pengeluaran Bulan Ini -->
    <div class="col-xl-2 col-md-6 col-sm-6">
        <x-stat-card 
            title="Keluar Bulan Ini" 
            value="Rp {{ number_format($pengeluaranBulanIni, 0, ',', '.') }}" 
            sub="{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}" 
            icon="fas fa-calendar-xmark" 
            theme="danger" 
            valColor="var(--nx-danger)" 
        />
    </div>
</div>
</div>

<!-- 2. GRAFIK ARUS KAS & BREAKDOWN KATEGORI -->
<div class="row g-4 mb-4">
    <!-- GRAFIK ARUS KAS -->
    <div class="col-lg-8">
        <div class="card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div class="d-flex align-items-center gap-2.5">
                    <span class="rounded-3 d-inline-flex align-items-center justify-content-center"
                          style="width: 38px; height: 38px; background: var(--nx-mint); color: var(--nx-forest);">
                        <i class="fas fa-chart-line"></i>
                    </span>
                    <div>
                        <h5 class="fw-bold mb-0" style="font-size: 1.05rem;">Visualisasi Arus Kas Transaksi</h5>
                        <small class="text-muted">Perbandingan realisasi arus uang masuk dan keluar aktual dari database</small>
                    </div>
                </div>

                <!-- FILTER PERIODE GRAFIK -->
                <div class="d-flex align-items-center gap-2">
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="{{ request()->fullUrlWithQuery(['periode' => '7d']) }}" class="btn {{ $periodeFilter === '7d' ? 'btn-primary' : 'btn-outline-secondary' }}">7 Hari</a>
                        <a href="{{ request()->fullUrlWithQuery(['periode' => '6m']) }}" class="btn {{ $periodeFilter === '6m' ? 'btn-primary' : 'btn-outline-secondary' }}">6 Bulan</a>
                        <a href="{{ request()->fullUrlWithQuery(['periode' => '12m']) }}" class="btn {{ $periodeFilter === '12m' ? 'btn-primary' : 'btn-outline-secondary' }}">12 Bulan</a>
                        <a href="{{ request()->fullUrlWithQuery(['periode' => 'ytd']) }}" class="btn {{ $periodeFilter === 'ytd' ? 'btn-primary' : 'btn-outline-secondary' }}">Tahun 2026</a>
                    </div>
                </div>
            </div>

            <!-- Legend Info -->
            <div class="d-flex align-items-center gap-4 mb-3 small fw-semibold">
                <span class="d-flex align-items-center gap-1.5">
                    <span style="display:inline-block; width:12px; height:12px; border-radius:3px; background:#285943;"></span>
                    Pemasukan Kas (Masuk)
                </span>
                <span class="d-flex align-items-center gap-1.5">
                    <span style="display:inline-block; width:12px; height:12px; border-radius:3px; background:#5B9BD5;"></span>
                    Pengeluaran Operasional (Keluar)
                </span>
            </div>

            <!-- Canvas Chart -->
            <div style="height: 270px; position: relative;">
                <canvas id="kasArusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- BREAKDOWN KATEGORI KEUANGAN -->
    <div class="col-lg-4">
        <div class="card p-4 h-100">
            <h5 class="fw-bold mb-1" style="font-size: 1rem;">
                <i class="fas fa-layer-group text-primary me-2"></i>Komposisi Kategori
            </h5>
            <p class="text-muted small mb-3">Distribusi transaksi berdasarkan alokasi pos keuangan</p>

            <!-- Nav Tabs Kategori -->
            <ul class="nav nav-pills nav-fill mb-3" id="kategoriTab" role="tablist" style="background: var(--nx-bg); border-radius: 8px; padding: 3px;">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active py-1.5 px-2 small fw-bold" id="masuk-tab" data-bs-toggle="tab" data-bs-target="#tab-masuk" type="button" role="tab" style="border-radius: 6px;">
                        Pemasukan
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-1.5 px-2 small fw-bold" id="keluar-tab" data-bs-toggle="tab" data-bs-target="#tab-keluar" type="button" role="tab" style="border-radius: 6px;">
                        Pengeluaran
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="kategoriTabContent" style="max-height: 250px; overflow-y: auto;">
                <!-- Tab Masuk -->
                <div class="tab-pane fade show active" id="tab-masuk" role="tabpanel">
                    @forelse($kategoriMasuk as $km)
                        @php
                            $pct = $totalPemasukan > 0 ? round(($km->total / $totalPemasukan) * 100, 1) : 0;
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center small mb-1">
                                <span class="fw-semibold text-truncate" style="max-width: 60%;" title="{{ $km->kategori }}">{{ $km->kategori }}</span>
                                <span class="fw-bold" style="color: var(--nx-forest);">Rp {{ number_format($km->total, 0, ',', '.') }}</span>
                            </div>
                            <div class="progress" style="height: 6px; background: var(--nx-bg);">
                                <div class="progress-bar" role="progressbar" style="width: {{ $pct }}%; background-color: var(--nx-forest);" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between text-muted" style="font-size: 0.7rem; margin-top: 2px;">
                                <span>{{ $km->count }} transaksi</span>
                                <span>{{ $pct }}%</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted small">Belum ada rincian kategori pemasukan.</div>
                    @endforelse
                </div>

                <!-- Tab Keluar -->
                <div class="tab-pane fade" id="tab-keluar" role="tabpanel">
                    @forelse($kategoriKeluar as $kk)
                        @php
                            $pct = $totalPengeluaran > 0 ? round(($kk->total / $totalPengeluaran) * 100, 1) : 0;
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center small mb-1">
                                <span class="fw-semibold text-truncate" style="max-width: 60%;" title="{{ $kk->kategori }}">{{ $kk->kategori }}</span>
                                <span class="fw-bold" style="color: var(--nx-danger);">Rp {{ number_format($kk->total, 0, ',', '.') }}</span>
                            </div>
                            <div class="progress" style="height: 6px; background: var(--nx-bg);">
                                <div class="progress-bar" role="progressbar" style="width: {{ $pct }}%; background-color: #5B9BD5;" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between text-muted" style="font-size: 0.7rem; margin-top: 2px;">
                                <span>{{ $kk->count }} transaksi</span>
                                <span>{{ $pct }}%</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted small">Belum ada rincian kategori pengeluaran.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 3. FILTER & SEARCH PANEL (STANDAR NEXANEST) -->
<div class="card nx-filter-card mb-4">
    <form method="GET" action="{{ route('pembayaran.index') }}" class="row g-2.5 align-items-end">
        <input type="hidden" name="periode" value="{{ $periodeFilter }}">

        <div class="col-xl-3 col-lg-3 col-md-6 col-12">
            <label class="form-label small fw-bold text-muted mb-1">Cari Transaksi</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0" placeholder="No. TRX, Nama, Keterangan...">
            </div>
        </div>

        <div class="col-xl-2 col-lg-2 col-md-3 col-6">
            <label class="form-label small fw-bold text-muted mb-1">Tipe</label>
            <select name="tipe" class="form-select">
                <option value="">-- Semua Tipe --</option>
                <option value="masuk" {{ request('tipe') === 'masuk' ? 'selected' : '' }}>Pemasukan (Masuk)</option>
                <option value="keluar" {{ request('tipe') === 'keluar' ? 'selected' : '' }}>Pengeluaran (Keluar)</option>
            </select>
        </div>

        <div class="col-xl-2 col-lg-2 col-md-3 col-6">
            <label class="form-label small fw-bold text-muted mb-1">Kategori</label>
            <select name="kategori" class="form-select">
                <option value="">-- Semua Kategori --</option>
                <optgroup label="Pemasukan">
                    @foreach($kategoriMasukList as $kmItem)
                        <option value="{{ $kmItem }}" {{ request('kategori') === $kmItem ? 'selected' : '' }}>{{ $kmItem }}</option>
                    @endforeach
                </optgroup>
                <optgroup label="Pengeluaran">
                    @foreach($kategoriKeluarList as $kkItem)
                        <option value="{{ $kkItem }}" {{ request('kategori') === $kkItem ? 'selected' : '' }}>{{ $kkItem }}</option>
                    @endforeach
                </optgroup>
            </select>
        </div>

        @if($isAdminRw)
            <div class="col-xl-2 col-lg-2 col-md-4 col-6">
                <label class="form-label small fw-bold text-muted mb-1">Wilayah RT</label>
                <select name="rt_number" class="form-select">
                    <option value="all">Semua Wilayah</option>
                    <option value="000" {{ request('rt_number') === '000' ? 'selected' : '' }}>RW 018 (Kas Induk)</option>
                    @foreach($rtList as $rt)
                        <option value="{{ $rt }}" {{ request('rt_number') === $rt ? 'selected' : '' }}>RT {{ $rt }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="col-xl-1 col-lg-1 col-md-4 col-6">
            <label class="form-label small fw-bold text-muted mb-1">Status</label>
            <select name="status" class="form-select px-2">
                <option value="">Semua</option>
                <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="dibatalkan" {{ request('status') === 'dibatalkan' ? 'selected' : '' }}>Batal</option>
            </select>
        </div>

        <div class="col-xl-2 col-lg-2 col-md-4 col-12 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1">
                <i class="fas fa-filter me-1.5"></i> Filter
            </button>
            <a href="{{ route('pembayaran.index') }}" class="btn btn-outline-secondary" title="Reset Filter" style="width: 40px; padding: 0;">
                <i class="fas fa-rotate-left"></i>
            </a>
        </div>
    </form>
</div>

<!-- 4. TABEL RIWAYAT TRANSAKSI KAS (STANDAR TABEL NEXANEST) -->
<div class="card nx-table-card overflow-hidden shadow-xs border-0 mb-4">
    <div class="card-header p-3.5 bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <h5 class="fw-bold mb-0 text-dark" style="font-size: 0.96rem;">
                <i class="fas fa-receipt text-primary me-2"></i>Buku Jurnal Transaksi Kas
            </h5>
            <span class="badge rounded-pill px-2.5 py-1" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-size: 0.74rem; font-weight: 700;">
                {{ $pembayarans->total() }} Transaksi Tercatat
            </span>
        </div>
        <small class="text-muted" style="font-size: 0.75rem;">Klik baris atau menu aksi untuk melihat detail kuitansi &amp; bukti transaksi.</small>
    </div>

    <div class="table-responsive nx-table-responsive">
        <table class="table table-hover align-middle nx-table mb-0">
            <thead>
                <tr>
                    <th class="ps-4" style="width: 14%;">Nomor Transaksi</th>
                    <th style="width: 11%;">Tanggal</th>
                    <th style="width: 25%;">Keterangan &amp; Sumber/Tujuan</th>
                    <th style="width: 8%;">Wilayah</th>
                    <th style="width: 14%;">Kategori</th>
                    <th style="width: 14%;">Nominal</th>
                    <th style="width: 8%;">Status</th>
                    <th class="pe-4 text-center" style="width: 6%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pembayarans as $p)
                    <tr class="{{ $p->status === 'dibatalkan' ? 'opacity-50 bg-light' : '' }}">
                        <td class="ps-4">
                            <span class="fw-bold font-monospace" style="font-size: 0.84rem; color: var(--nx-text);">
                                {{ $p->nomor_transaksi_formatted }}
                            </span>
                            @if($p->pembayaran_id)
                                <span class="badge rounded-pill px-2 py-0.5 mt-0.5 d-block text-truncate" style="background: #E8F4EC; color: #1E6B37; font-size: 0.68rem; max-width: 100px;">
                                    <i class="fas fa-link me-1"></i>Iuran KK
                                </span>
                            @endif
                        </td>
                        <td class="small fw-semibold text-muted">
                            {{ $p->tanggal ? $p->tanggal->translatedFormat('d M Y') : '-' }}
                        </td>
                        <td>
                            <strong class="d-block" style="color: var(--nx-text); font-size: 0.88rem;">
                                @if($p->warga)
                                    {{ $p->warga->nama }}
                                @else
                                    {{ $p->sumber_tujuan ?: 'Kas Operasional Lingkungan' }}
                                @endif
                            </strong>
                            <small class="text-muted d-block text-truncate" style="max-width: 280px;" title="{{ $p->keterangan }}">
                                {{ $p->keterangan ?: '-' }}
                            </small>
                        </td>
                        <td>
                            @if($p->rt_number === '000')
                                <span class="badge rounded-pill px-2 py-1" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-size: 0.72rem;">RW 018</span>
                            @else
                                <span class="badge rounded-pill px-2 py-1" style="background: var(--nx-powder); color: #1F517F; font-size: 0.72rem;">RT {{ $p->rt_number }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge rounded-pill px-2.5 py-1" style="background: var(--nx-bg); color: var(--nx-text); border: 1px solid var(--nx-border); font-size: 0.74rem;">
                                {{ $p->kategori }}
                            </span>
                        </td>
                        <td>
                            @if($p->tipe === 'masuk')
                                <strong style="color: var(--nx-forest); font-size: 0.92rem;">
                                    + Rp {{ number_format($p->jumlah, 0, ',', '.') }}
                                </strong>
                            @else
                                <strong style="color: var(--nx-danger); font-size: 0.92rem;">
                                    - Rp {{ number_format($p->jumlah, 0, ',', '.') }}
                                </strong>
                            @endif
                            <small class="text-muted d-block" style="font-size: 0.7rem;">{{ $p->metode_pembayaran ?: 'Tunai' }}</small>
                        </td>
                        <td>
                            @if($p->status === 'selesai' || !$p->status)
                                <x-status-badge status="selesai" label="Selesai" />
                            @elseif($p->status === 'dibatalkan')
                                <x-status-badge status="dibatalkan" label="Batal" />
                            @else
                                <x-status-badge status="menunggu" :label="ucfirst($p->status)" />
                            @endif
                        </td>
                        <td class="pe-4 text-center">
                            <div class="dropdown">
                                <button class="nx-action-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Menu Aksi">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius: 12px; font-size: 0.82rem;">
                                    <li>
                                        <button class="dropdown-item small d-flex align-items-center gap-2" type="button" onclick="showTransactionDetail({{ $p->id }})">
                                            <i class="fas fa-eye text-primary"></i> Detail Transaksi
                                        </button>
                                    </li>
                                    @if($p->tipe === 'masuk')
                                        <li>
                                            <a class="dropdown-item small d-flex align-items-center gap-2" href="{{ route('iuran.kuitansi', $p->id) }}" target="_blank">
                                                <i class="fas fa-receipt text-success"></i> Cetak Kuitansi
                                            </a>
                                        </li>
                                    @endif
                                    @if($p->status !== 'dibatalkan')
                                        <li>
                                            <a class="dropdown-item small d-flex align-items-center gap-2" href="{{ route('pembayaran.edit', $p->id) }}">
                                                <i class="fas fa-edit text-warning"></i> Edit Transaksi
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li>
                                            <button class="dropdown-item small text-danger d-flex align-items-center gap-2" type="button" onclick="confirmCancelTransaction({{ $p->id }}, '{{ $p->nomor_transaksi_formatted }}')">
                                                <i class="fas fa-ban"></i> Batalkan Transaksi
                                            </button>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="p-0 border-0">
                            <x-empty-state
                                icon="fas fa-receipt"
                                title="Tidak Ada Transaksi Ditemukan"
                                message="Tidak ada data transaksi kas yang sesuai dengan kriteria filter saat ini. Klik tombol di bawah untuk mencatat transaksi kas baru."
                                actionUrl="{{ route('pembayaran.create') }}"
                                actionLabel="Catat Transaksi Baru"
                                actionIcon="fas fa-plus-circle"
                            />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- PAGINATION -->
    <div class="card-footer bg-white p-3.5 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
        <small class="text-muted">
            Menampilkan {{ $pembayarans->firstItem() ?? 0 }} - {{ $pembayarans->lastItem() ?? 0 }} dari {{ $pembayarans->total() }} transaksi
        </small>
        <div>
            {{ $pembayarans->links() }}
        </div>
    </div>
</div>

<!-- MODAL DETAIL TRANSAKSI -->
<div class="modal fade" id="modalDetailTx" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <h6 class="modal-title fw-bold" id="detailModalTitle">
                    <i class="fas fa-receipt text-primary me-2"></i>Rincian Transaksi Kas
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="detailModalBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Memuat...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top py-2.5">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL BATALKAN TRANSAKSI -->
<div class="modal fade" id="modalCancelTx" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" id="formCancelTx" class="modal-content border-0 shadow">
            @csrf
            <div class="modal-header border-bottom py-3">
                <h6 class="modal-title fw-bold text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Pembatalan Transaksi
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="mb-2">Anda akan membatalkan transaksi <strong id="cancelTxNomor"></strong>.</p>
                <p class="text-muted small mb-3">Transaksi yang dibatalkan tidak akan dihitung ke dalam saldo kas aktif, namun riwayat audit pencatatannya tetap tersimpan demi akuntabilitas.</p>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Alasan Pembatalan <span class="text-danger">*</span></label>
                    <textarea name="alasan" class="form-control form-control-sm" rows="3" required placeholder="Tuliskan alasan pembatalan transaksi ini..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-top py-2.5">
                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-sm btn-danger px-3">
                    <i class="fas fa-ban me-1"></i> Ya, Batalkan Transaksi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Inisialisasi Grafik Arus Kas Multi-Periode
    const ctx = document.getElementById('kasArusChart')?.getContext('2d');
    if (ctx) {
        const labels = {!! json_encode($chartData->pluck('label')) !!};
        const fullLabels = {!! json_encode($chartData->pluck('label_full')) !!};
        const masukData = {!! json_encode($chartData->pluck('masuk')) !!};
        const keluarData = {!! json_encode($chartData->pluck('keluar')) !!};
        const netData = {!! json_encode($chartData->pluck('net')) !!};

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Pemasukan Kas (Masuk)',
                        data: masukData,
                        backgroundColor: '#285943',
                        borderRadius: 6,
                        maxBarThickness: 28,
                    },
                    {
                        label: 'Pengeluaran Kas (Keluar)',
                        data: keluarData,
                        backgroundColor: '#5B9BD5',
                        borderRadius: 6,
                        maxBarThickness: 28,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#263449',
                        titleColor: '#FFFFFF',
                        bodyColor: '#F7F9F7',
                        padding: 12,
                        cornerRadius: 10,
                        callbacks: {
                            title: function(items) {
                                if (!items || !items.length) return '';
                                const idx = items[0].dataIndex;
                                return fullLabels[idx] || items[0].label;
                            },
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) label += ': ';
                                if (context.parsed.y !== null) {
                                    label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                                }
                                return label;
                            },
                            afterBody: function(items) {
                                if (!items || !items.length) return '';
                                const idx = items[0].dataIndex;
                                const net = netData[idx] !== undefined ? netData[idx] : 0;
                                const netFormatted = (net >= 0 ? '+Rp ' : '-Rp ') + new Intl.NumberFormat('id-ID').format(Math.abs(net));
                                const status = net >= 0 ? 'Surplus' : 'Defisit';
                                return '\nArus Kas Bersih (Net): ' + netFormatted + ' (' + status + ')';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748B', font: { family: 'Plus Jakarta Sans', size: 11 } }
                    },
                    y: {
                        grid: { color: '#EDF3EE' },
                        ticks: {
                            color: '#64748B',
                            font: { family: 'Plus Jakarta Sans', size: 11 },
                            callback: function(value) {
                                if (value >= 1000000) return 'Rp ' + (value/1000000).toFixed(0) + ' Jt';
                                if (value >= 1000) return 'Rp ' + (value/1000).toFixed(0) + ' Rb';
                                return value;
                            }
                        }
                    }
                }
            }
        });
    }
});

// 2. Fungsi AJAX Buka Detail Transaksi
function showTransactionDetail(id) {
    const modalEl = document.getElementById('modalDetailTx');
    const modalBody = document.getElementById('detailModalBody');
    const modal = new bootstrap.Modal(modalEl);
    
    modalBody.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Memuat...</span>
            </div>
        </div>`;
    modal.show();

    fetch(`/pembayaran/${id}`)
        .then(response => {
            if (!response.ok) throw new Error('Gagal mengambil data.');
            return response.json();
        })
        .then(data => {
            const isMasuk = data.tipe === 'masuk';
            modalBody.innerHTML = `
                <div class="text-center pb-3 border-bottom mb-3">
                    <span class="badge rounded-pill px-3 py-1 mb-2 ${isMasuk ? 'bg-success text-white' : 'bg-danger text-white'}">
                        <i class="fas ${isMasuk ? 'fa-arrow-down' : 'fa-arrow-up'} me-1"></i>
                        ${isMasuk ? 'Pemasukan Kas' : 'Pengeluaran Kas'}
                    </span>
                    <h3 class="fw-bold mb-0 ${isMasuk ? 'text-success' : 'text-danger'}">${data.jumlah_formatted}</h3>
                    <div class="font-monospace text-muted small mt-1">${data.nomor_transaksi}</div>
                </div>

                <div class="row g-2 small">
                    <div class="col-5 text-muted">Tanggal:</div>
                    <div class="col-7 fw-semibold text-end">${data.tanggal}</div>

                    <div class="col-5 text-muted">Kategori:</div>
                    <div class="col-7 fw-semibold text-end"><span class="badge bg-light text-dark border">${data.kategori}</span></div>

                    <div class="col-5 text-muted">Wilayah:</div>
                    <div class="col-7 fw-semibold text-end">${data.rt_number}</div>

                    <div class="col-5 text-muted">Pihak Terkait / Warga:</div>
                    <div class="col-7 fw-semibold text-end">${data.warga_nama !== '-' ? data.warga_nama : data.sumber_tujuan}</div>

                    <div class="col-5 text-muted">Metode Pembayaran:</div>
                    <div class="col-7 fw-semibold text-end">${data.metode_pembayaran}</div>

                    <div class="col-5 text-muted">Nomor Referensi:</div>
                    <div class="col-7 fw-semibold text-end">${data.nomor_referensi}</div>

                    <div class="col-5 text-muted">Status:</div>
                    <div class="col-7 fw-semibold text-end">
                        <span class="badge ${data.status === 'selesai' ? 'bg-success' : 'bg-secondary'}">${data.status}</span>
                    </div>

                    <div class="col-5 text-muted">Dicatat Oleh:</div>
                    <div class="col-7 fw-semibold text-end">${data.created_by_name}</div>
                </div>

                <div class="mt-3 pt-3 border-top">
                    <label class="form-label text-muted small mb-1 fw-bold">Keterangan Catatan:</label>
                    <p class="small bg-light p-2.5 rounded-2 mb-0">${data.keterangan}</p>
                </div>

                ${data.bukti_url ? `
                <div class="mt-3 pt-3 border-top">
                    <label class="form-label text-muted small mb-1 fw-bold">Bukti / Nota Pembayaran:</label>
                    <div class="text-center mt-1">
                        <a href="${data.bukti_url}" target="_blank">
                            <img src="${data.bukti_url}" alt="Bukti Transaksi" class="img-fluid rounded border shadow-sm" style="max-height: 200px;">
                        </a>
                        <small class="d-block text-muted mt-1"><i class="fas fa-external-link-alt me-1"></i> Klik untuk memperbesar</small>
                    </div>
                </div>
                ` : ''}
            `;
        })
        .catch(err => {
            modalBody.innerHTML = `<div class="alert alert-danger mb-0 small">Gagal memuat data transaksi. Silakan coba lagi.</div>`;
        });
}

// 3. Konfirmasi Batalkan Transaksi
function confirmCancelTransaction(id, nomor) {
    document.getElementById('cancelTxNomor').textContent = nomor;
    document.getElementById('formCancelTx').action = `/pembayaran/${id}/cancel`;
    new bootstrap.Modal(document.getElementById('modalCancelTx')).show();
}
</script>
@endpush
