@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- HEADER & ACTION -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark" style="color: var(--text-main) !important;">Laporan Kepatuhan Iuran Warga</h2>
            <p class="text-muted mb-0 font-sans">Pantau tingkat kepatuhan dan histori pembayaran iuran bulanan dari seluruh warga RW 018.</p>
        </div>
    </div>

    <!-- FILTER PANEL -->
    <div class="card border-0 shadow-sm rounded-4 mb-4" style="background-color: var(--bg-card); border: 1px solid var(--border-color) !important;">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('laporan.iuran') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-bold text-secondary small">Bulan</label>
                    <select name="bulan" class="form-select" style="border-radius: 10px;">
                        @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $k => $n)
                            <option value="{{ $k }}" {{ $bulan == $k ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold text-secondary small">Tahun</label>
                    <select name="tahun" class="form-select" style="border-radius: 10px;">
                        @foreach($daftarTahun as $t)
                            <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold text-secondary small">Pilih Wilayah RT (Opsional)</label>
                    <select name="rt_number" class="form-select" style="border-radius: 10px;">
                        <option value="">-- Semua RT (006 - 010) --</option>
                        @foreach($rtList as $rt)
                            <option value="{{ $rt }}" {{ $rtFilter == $rt ? 'selected' : '' }}>RT {{ $rt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 py-2.5" style="border-radius: 10px; font-weight: 600;">Tampilkan</button>
                    <a href="{{ route('laporan.cetak_iuran', ['bulan' => $bulan, 'tahun' => $tahun, 'rt_number' => $rtFilter]) }}" target="_blank" class="btn btn-success py-2.5 px-3" style="border-radius: 10px;" title="Cetak PDF">
                        <i class="fas fa-file-pdf"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- METRICS CARDS (KAISAR KEPATUHAN WARGA) -->
    <div class="row g-3 mb-4">
        <!-- Rajin Bayar -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 d-flex flex-row align-items-center justify-content-between" style="background-color: var(--bg-card); border: 1px solid var(--border-color) !important;">
                <div>
                    <span class="text-muted small fw-semibold uppercase d-block mb-1" style="font-size: 0.75rem;">Warga Rajin Bayar</span>
                    <h2 class="fw-bold text-success mb-0">{{ $countRajin }} <span class="fs-6 text-muted fw-normal">Warga</span></h2>
                    <small class="text-success-emphasis" style="font-size: 0.75rem;">Membayar iuran rutin 3 bulan terakhir</small>
                </div>
                <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="fas fa-check-double fa-lg"></i>
                </div>
            </div>
        </div>
        <!-- Kurang Bayar -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 d-flex flex-row align-items-center justify-content-between" style="background-color: var(--bg-card); border: 1px solid var(--border-color) !important;">
                <div>
                    <span class="text-muted small fw-semibold uppercase d-block mb-1" style="font-size: 0.75rem;">Warga Kurang Bayar</span>
                    <h2 class="fw-bold text-warning mb-0">{{ $countKurang }} <span class="fs-6 text-muted fw-normal">Warga</span></h2>
                    <small class="text-warning-emphasis" style="font-size: 0.75rem;">Menunggak atau telat beberapa iuran</small>
                </div>
                <div class="rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="fas fa-exclamation-triangle fa-lg"></i>
                </div>
            </div>
        </div>
        <!-- Belum Pernah Bayar -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 d-flex flex-row align-items-center justify-content-between" style="background-color: var(--bg-card); border: 1px solid var(--border-color) !important;">
                <div>
                    <span class="text-muted small fw-semibold uppercase d-block mb-1" style="font-size: 0.75rem;">Belum Pernah Bayar</span>
                    <h2 class="fw-bold text-danger mb-0">{{ $countTidakPernah }} <span class="fs-6 text-muted fw-normal">Warga</span></h2>
                    <small class="text-danger-emphasis" style="font-size: 0.75rem;">Belum ada riwayat pembayaran sama sekali</small>
                </div>
                <div class="rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="fas fa-user-times fa-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- PREVIEW TABLE -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background-color: var(--bg-card); border: 1px solid var(--border-color) !important;">
        <div class="card-header bg-transparent border-bottom p-3" style="border-color: var(--border-color) !important;">
            <h5 class="fw-bold text-dark mb-0" style="color: var(--text-main) !important;">
                <i class="fas fa-chart-line text-primary me-2"></i>Status Ketaatan Iuran Warga
            </h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="color: var(--text-main);">
                <thead style="background-color: rgba(59,130,246,0.03); color: var(--text-main); border-bottom: 2px solid var(--border-color);">
                    <tr>
                        <th class="ps-4" style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; opacity: 0.8; width: 30%;">Nama Warga</th>
                        <th style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; opacity: 0.8; width: 15%;">Status Bulan Ini</th>
                        <th style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; opacity: 0.8; width: 15%;">Tanggal Bayar</th>
                        <th style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; opacity: 0.8; width: 20%;" class="text-center">Tren 3 Bulan Terakhir</th>
                        <th class="pe-4 text-end" style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; opacity: 0.8; width: 20%;">Kategori Kepatuhan</th>
                    </tr>
                </thead>
                <tbody style="border-top: none;">
                    @forelse($rows as $row)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center text-secondary fw-semibold" style="width: 35px; height: 35px; font-size: 0.85rem;">
                                        {{ strtoupper(substr($row['warga']->nama, 0, 2)) }}
                                    </div>
                                    <div>
                                        <strong class="text-dark d-block" style="color: var(--text-main) !important; font-size: 0.95rem;">
                                            {{ $row['warga']->nama }}
                                        </strong>
                                        <small class="text-muted" style="font-size: 0.75rem;">Blok {{ $row['warga']->blok_rumah }} · RT {{ $row['warga']->rt_number }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($row['status'] == 'Lunas')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.75rem;">Lunas</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size: 0.75rem;">Belum Bayar</span>
                                @endif
                            </td>
                            <td style="font-size: 0.9rem;">
                                {{ $row['tanggal_bayar'] ? \Carbon\Carbon::parse($row['tanggal_bayar'])->translatedFormat('d M Y') : '-' }}
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    @foreach($row['history'] as $h)
                                        @if($h['paid'])
                                            <span class="badge bg-success" style="width: 10px; height: 10px; border-radius: 50%; padding: 0;" title="{{ $h['month'] }}: Lunas"></span>
                                        @else
                                            <span class="badge bg-secondary" style="width: 10px; height: 10px; border-radius: 50%; padding: 0; background-color: #cbd5e1 !important;" title="{{ $h['month'] }}: Belum Bayar"></span>
                                        @endif
                                    @endforeach
                                </div>
                            </td>
                            <td class="pe-4 text-end">
                                @if($row['keterangan'] == 'Rajin')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 rounded-pill" style="font-size: 0.75rem; font-weight: 600;">
                                        <i class="fas fa-heart text-success me-1"></i>Rajin Bayar
                                    </span>
                                @elseif($row['keterangan'] == 'Tidak Pernah Bayar')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1.5 rounded-pill" style="font-size: 0.75rem; font-weight: 600;">
                                        <i class="fas fa-times-circle text-danger me-1"></i>Tidak Pernah Bayar
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1.5 rounded-pill" style="font-size: 0.75rem; font-weight: 600;">
                                        <i class="fas fa-exclamation-circle text-warning me-1"></i>Kurang Bayar
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3 text-secondary" style="opacity: 0.5;"></i>
                                <h5 class="fw-semibold mb-1">Belum Ada Data Warga</h5>
                                <p class="small mb-0">Data tingkat ketaatan pembayaran iuran warga akan terdaftar di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
