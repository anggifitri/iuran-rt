@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- HEADER & ACTION -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark" style="color: var(--text-main) !important;">Laporan Keuangan Bulanan</h2>
            <p class="text-muted mb-0 font-sans">Analisis total pemasukan, pengeluaran, dan saldo kas bulanan RW 018 secara akurat.</p>
        </div>
    </div>

    <!-- FILTER PANEL -->
    <div class="card border-0 shadow-sm rounded-4 mb-4" style="background-color: var(--bg-card); border: 1px solid var(--border-color) !important;">
        <div class="card-body p-4">
            <form action="{{ route('laporan.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-bold text-secondary small">Bulan Laporan</label>
                    <select name="bulan" class="form-select" style="border-radius: 10px;">
                        @foreach([
                            '01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April',
                            '05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus',
                            '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'
                        ] as $key => $namaBulan)
                            <option value="{{ $key }}" {{ $bulan == $key ? 'selected' : '' }}>{{ $namaBulan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold text-secondary small">Tahun Laporan</label>
                    <select name="tahun" class="form-select" style="border-radius: 10px;">
                        @foreach($daftarTahun as $t)
                            <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 py-2.5" style="border-radius: 10px; font-weight: 600;">
                        <i class="fas fa-filter me-1"></i>Filter Laporan
                    </button>
                    <a href="{{ route('laporan.cetak_bulanan', ['bulan' => $bulan, 'tahun' => $tahun]) }}" target="_blank" class="btn btn-success w-100 py-2.5" style="border-radius: 10px; font-weight: 600;">
                        <i class="fas fa-file-pdf me-1"></i>Cetak PDF
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- METRICS CARDS -->
    <div class="row g-3 mb-4">
        <!-- Saldo Awal -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center text-md-start h-100" style="background-color: var(--bg-card); border: 1px solid var(--border-color) !important;">
                <span class="text-muted small fw-semibold uppercase d-block mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Saldo Awal Bulan</span>
                <h3 class="fw-bold text-info mb-0">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</h3>
            </div>
        </div>
        <!-- Pemasukan -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center text-md-start h-100" style="background-color: var(--bg-card); border: 1px solid var(--border-color) !important;">
                <span class="text-muted small fw-semibold uppercase d-block mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Pemasukan</span>
                <h3 class="fw-bold text-success mb-0">+Rp {{ number_format($totalMasuk, 0, ',', '.') }}</h3>
            </div>
        </div>
        <!-- Pengeluaran -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center text-md-start h-100" style="background-color: var(--bg-card); border: 1px solid var(--border-color) !important;">
                <span class="text-muted small fw-semibold uppercase d-block mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Pengeluaran</span>
                <h3 class="fw-bold text-danger mb-0">-Rp {{ number_format($totalKeluar, 0, ',', '.') }}</h3>
            </div>
        </div>
        <!-- Saldo Akhir -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center text-md-start h-100" style="background-color: var(--bg-card); border: 1px solid var(--border-color) !important;">
                <span class="text-muted small fw-semibold uppercase d-block mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Saldo Akhir Kas</span>
                <h3 class="fw-bold text-primary mb-0">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>

    <!-- PREVIEW TABLE -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background-color: var(--bg-card); border: 1px solid var(--border-color) !important;">
        <div class="card-header bg-transparent border-bottom p-3" style="border-color: var(--border-color) !important;">
            <h5 class="fw-bold text-dark mb-0" style="color: var(--text-main) !important;"><i class="fas fa-list-alt text-primary me-2"></i>Rincian Arus Kas ({{ $bulan }}/{{ $tahun }})</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="color: var(--text-main);">
                <thead style="background-color: rgba(59,130,246,0.03); color: var(--text-main); border-bottom: 2px solid var(--border-color);">
                    <tr>
                        <th class="ps-4" style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; opacity: 0.8;">Tanggal</th>
                        <th style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; opacity: 0.8;">Keterangan / Warga</th>
                        <th style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; opacity: 0.8;">Kategori</th>
                        <th style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; opacity: 0.8; class: text-center;">Tipe</th>
                        <th class="pe-4 text-end" style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; opacity: 0.8;">Jumlah</th>
                    </tr>
                </thead>
                <tbody style="border-top: none;">
                    @forelse($pembayarans as $p)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td class="ps-4" style="font-size: 0.9rem;">{{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d M Y') }}</td>
                            <td>
                                <strong class="text-dark d-block" style="color: var(--text-main) !important; font-size: 0.95rem;">
                                    {{ $p->warga ? $p->warga->nama : 'Umum / Non-Warga' }}
                                </strong>
                                @if($p->keterangan)<small class="text-muted d-block" style="font-size: 0.8rem;">{{ $p->keterangan }}</small>@endif
                            </td>
                            <td><span class="badge bg-secondary-subtle text-secondary border" style="font-size: 0.75rem;">{{ $p->kategori }}</span></td>
                            <td>
                                @if($p->tipe == 'masuk')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.75rem;">Masuk</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size: 0.75rem;">Keluar</span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <strong class="{{ $p->tipe == 'masuk' ? 'text-success' : 'text-danger' }}" style="font-size: 0.95rem;">
                                    {{ $p->tipe == 'masuk' ? '+' : '-' }} Rp {{ number_format($p->jumlah, 0, ',', '.') }}
                                </strong>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3 text-secondary" style="opacity: 0.5;"></i>
                                <h5 class="fw-semibold mb-1">Tidak Ada Transaksi</h5>
                                <p class="small mb-0">Belum ada pencatatan kas masuk atau keluar pada bulan dan tahun ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
