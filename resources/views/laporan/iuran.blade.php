@extends('layouts.app')

@section('title', 'Laporan & Riwayat Iuran Warga — NexaNest')

@section('content')
@php
    $isWargaView = Auth::check() && Auth::user()->isWarga();
    $rtFilter = $rtFilter ?? null;
    $rtList = $rtList ?? ['006', '007', '008', '009', '010'];
    $daftarTahun = $daftarTahun ?? range(date('Y'), date('Y') - 5);
    $countRajin = $countRajin ?? 0;
    $countKurang = $countKurang ?? 0;
    $countTidakPernah = $countTidakPernah ?? 0;
@endphp

<div class="nx-page-header">
    <div>
        <span class="badge rounded-pill mb-2 px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
            <i class="fas fa-file-invoice-dollar me-1"></i> {{ $isWargaView ? 'Kartu Iuran Digital' : 'Monitoring Kepatuhan Iuran' }}
        </span>
        <h1 class="nx-page-title">{{ $isWargaView ? 'Riwayat & Status Iuran Warga' : 'Laporan Kepatuhan Iuran Warga' }}</h1>
        <p class="nx-page-subtitle">
            {{ $isWargaView ? 'Pantau status kelunasan dan histori pembayaran iuran bulanan lingkungan secara transparan.' : 'Pantau tingkat kepatuhan dan histori pembayaran iuran bulanan dari seluruh warga RW 018.' }}
        </p>
    </div>
    <div>
        <a href="{{ route('laporan.cetak_iuran', ['bulan' => $bulan, 'tahun' => $tahun, 'rt_number' => $rtFilter]) }}" target="_blank" class="btn btn-primary">
            <i class="fas fa-file-pdf me-1"></i> Cetak PDF
        </a>
    </div>
</div>

<!-- FILTER PANEL -->
<div class="card p-4 mb-4">
    <form method="GET" action="{{ route('laporan.iuran') }}" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label">Bulan</label>
            <select name="bulan" class="form-select">
                @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $k => $n)
                    <option value="{{ $k }}" {{ $bulan == $k ? 'selected' : '' }}>{{ $n }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Tahun</label>
            <select name="tahun" class="form-select">
                @foreach($daftarTahun as $t)
                    <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Pilih Wilayah RT (Opsional)</label>
            <select name="rt_number" class="form-select">
                <option value="">-- Semua Wilayah RT --</option>
                @foreach($rtList as $rt)
                    <option value="{{ $rt }}" {{ $rtFilter == $rt ? 'selected' : '' }}>RT {{ $rt }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100 py-2.5">
                <i class="fas fa-filter me-1"></i> Tampilkan
            </button>
        </div>
    </form>
</div>

<!-- METRICS CARDS -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card p-4 d-flex flex-row align-items-center justify-content-between h-100" style="background: var(--nx-mint-soft) !important; border-color: var(--nx-sage) !important;">
            <div>
                <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.72rem;">Warga Rajin Bayar</span>
                <h2 class="fw-bold mb-0" style="color: var(--nx-forest);">{{ $countRajin }} <span class="fs-6 text-muted fw-normal">Warga</span></h2>
                <small class="text-muted" style="font-size: 0.75rem;">Membayar iuran rutin 3 bulan terakhir</small>
            </div>
            <div class="rounded-circle d-flex align-items-center justify-content-center"
                 style="width: 50px; height: 50px; background: var(--nx-mint); color: var(--nx-forest);">
                <i class="fas fa-check-double fa-lg"></i>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-4 d-flex flex-row align-items-center justify-content-between h-100" style="background: var(--nx-warning-soft) !important; border-color: #F6D89E !important;">
            <div>
                <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.72rem;">Warga Kurang Bayar</span>
                <h2 class="fw-bold mb-0" style="color: var(--nx-warning);">{{ $countKurang }} <span class="fs-6 text-muted fw-normal">Warga</span></h2>
                <small class="text-muted" style="font-size: 0.75rem;">Terdapat bulan iuran yang belum tercatat</small>
            </div>
            <div class="rounded-circle d-flex align-items-center justify-content-center"
                 style="width: 50px; height: 50px; background: #FCE8BF; color: var(--nx-warning);">
                <i class="fas fa-clock fa-lg"></i>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-4 d-flex flex-row align-items-center justify-content-between h-100" style="background: var(--nx-soft-red) !important; border-color: #F5C2C5 !important;">
            <div>
                <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.72rem;">Belum Pernah Bayar</span>
                <h2 class="fw-bold mb-0" style="color: var(--nx-danger);">{{ $countTidakPernah }} <span class="fs-6 text-muted fw-normal">Warga</span></h2>
                <small class="text-muted" style="font-size: 0.75rem;">Belum ada riwayat pembayaran tercatat</small>
            </div>
            <div class="rounded-circle d-flex align-items-center justify-content-center"
                 style="width: 50px; height: 50px; background: #F9D2D5; color: var(--nx-danger);">
                <i class="fas fa-user-clock fa-lg"></i>
            </div>
        </div>
    </div>
</div>

<!-- TABEL STATUS KEPATUHAN -->
<div class="card overflow-hidden">
    <div class="card-header p-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0" style="font-size: 1rem;">
            <i class="fas fa-list-check text-success me-2"></i>Daftar Status Iuran Warga ({{ $bulan }}/{{ $tahun }})
        </h5>
        <span class="badge rounded-pill px-3 py-1" style="background: var(--nx-mint-soft); color: var(--nx-forest);">
            {{ count($rows) }} Data Warga
        </span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4" style="width: 32%;">Nama Warga</th>
                    <th style="width: 15%;">Status Bulan Ini</th>
                    <th style="width: 18%;">Tanggal Bayar</th>
                    <th style="width: 15%;" class="text-center">Tren 3 Bulan Terakhir</th>
                    <th class="pe-4 text-end" style="width: 20%;">Kategori Kepatuhan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                     style="width: 38px; height: 38px; font-size: 0.82rem; background: var(--nx-mint); color: var(--nx-forest);">
                                    {{ strtoupper(substr($row['warga']->nama, 0, 2)) }}
                                </div>
                                <div>
                                    <strong class="d-block" style="color: var(--nx-text);">{{ $row['warga']->nama }}</strong>
                                    <small class="text-muted">Blok {{ $row['warga']->blok_rumah }} · RT {{ $row['warga']->rt_number }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($row['status'] == 'Lunas')
                                <span class="badge rounded-pill px-3 py-1" style="background: var(--nx-mint); color: var(--nx-forest);">
                                    <i class="fas fa-check-circle me-1"></i>Lunas
                                </span>
                            @else
                                <span class="badge rounded-pill px-3 py-1" style="background: var(--nx-soft-red); color: var(--nx-danger);">
                                    <i class="fas fa-hourglass-half me-1"></i>Belum Bayar
                                </span>
                            @endif
                        </td>
                        <td class="small fw-semibold">
                            {{ $row['tanggal_bayar'] ? \Carbon\Carbon::parse($row['tanggal_bayar'])->translatedFormat('d M Y') : '-' }}
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1.5">
                                @foreach($row['history'] as $h)
                                    @if($h['paid'])
                                        <span class="d-inline-block rounded-circle"
                                              style="width: 12px; height: 12px; background: var(--nx-forest);"
                                              title="{{ $h['month'] }}: Lunas"></span>
                                    @else
                                        <span class="d-inline-block rounded-circle"
                                              style="width: 12px; height: 12px; background: #CBD5E1;"
                                              title="{{ $h['month'] }}: Belum Bayar"></span>
                                    @endif
                                @endforeach
                            </div>
                        </td>
                        <td class="pe-4 text-end">
                            @if($row['keterangan'] == 'Rajin')
                                <span class="badge rounded-pill px-3 py-1.5" style="background: var(--nx-mint-soft); color: var(--nx-forest); border: 1px solid var(--nx-sage);">
                                    <i class="fas fa-seedling me-1"></i>Rajin Bayar
                                </span>
                            @elseif($row['keterangan'] == 'Tidak Pernah Bayar')
                                <span class="badge rounded-pill px-3 py-1.5" style="background: var(--nx-soft-red); color: var(--nx-danger);">
                                    <i class="fas fa-times-circle me-1"></i>Tidak Pernah Bayar
                                </span>
                            @else
                                <span class="badge rounded-pill px-3 py-1.5" style="background: var(--nx-warning-soft); color: var(--nx-warning);">
                                    <i class="fas fa-exclamation-circle me-1"></i>Kurang Bayar
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <h6 class="fw-bold mb-1">Belum Ada Data Warga</h6>
                            <p class="text-muted small mb-0">Data tingkat ketaatan pembayaran iuran warga akan muncul di sini.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
