@extends('layouts.app')

@section('title', 'Laporan Keuangan Bulanan — NexaNest')

@section('content')
<div class="nx-page-header">
    <div>
        <span class="badge rounded-pill mb-2 px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
            <i class="fas fa-chart-column me-1"></i> Transparansi Kas RT/RW
        </span>
        <h1 class="nx-page-title">Laporan Keuangan Bulanan</h1>
        <p class="nx-page-subtitle">Analisis saldo awal, realisasi pemasukan, pengeluaran, dan saldo akhir kas bulanan secara akurat.</p>
    </div>
    <div>
        <a href="{{ route('laporan.cetak_bulanan', ['bulan' => $bulan, 'tahun' => $tahun]) }}" target="_blank" class="btn btn-primary">
            <i class="fas fa-file-pdf me-1"></i> Cetak / Ekspor PDF
        </a>
    </div>
</div>

<!-- FILTER PANEL -->
<div class="card p-4 mb-4">
    <form action="{{ route('laporan.index') }}" method="GET" class="row g-3 align-items-end">
        <div class="col-md-5">
            <label class="form-label">Bulan Laporan</label>
            <select name="bulan" class="form-select">
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
            <label class="form-label">Tahun Laporan</label>
            <select name="tahun" class="form-select">
                @foreach($daftarTahun as $t)
                    <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100 py-2.5">
                <i class="fas fa-filter me-1"></i> Tampilkan Laporan
            </button>
        </div>
    </form>
</div>

<!-- METRICS SUMMARY CARDS -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card p-4 h-100" style="background: var(--nx-powder-soft) !important; border-color: var(--nx-pastel-blue) !important;">
            <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.72rem;">Saldo Awal Bulan</span>
            <h4 class="fw-bold mb-0" style="color: #2B5F8E;">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</h4>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-4 h-100" style="background: var(--nx-mint-soft) !important; border-color: var(--nx-sage) !important;">
            <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.72rem;">Total Pemasukan</span>
            <h4 class="fw-bold mb-0" style="color: var(--nx-forest);">+Rp {{ number_format($totalMasuk, 0, ',', '.') }}</h4>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-4 h-100" style="background: var(--nx-soft-red) !important; border-color: #F5C2C5 !important;">
            <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.72rem;">Total Pengeluaran</span>
            <h4 class="fw-bold mb-0" style="color: var(--nx-danger);">-Rp {{ number_format($totalKeluar, 0, ',', '.') }}</h4>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-4 h-100" style="background: var(--nx-mint) !important; border-color: var(--nx-sage) !important;">
            <span class="small fw-bold text-uppercase d-block mb-1" style="font-size: 0.72rem; color: var(--nx-forest);">Saldo Akhir Kas</span>
            <h4 class="fw-bold mb-0" style="color: var(--nx-forest);">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</h4>
        </div>
    </div>
</div>

<!-- RINCIAN TABLE -->
<div class="card overflow-hidden">
    <div class="card-header p-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0" style="font-size: 1rem;">
            <i class="fas fa-receipt text-success me-2"></i>Rincian Arus Kas Periode {{ $bulan }}/{{ $tahun }}
        </h5>
        <span class="badge rounded-pill px-3 py-1" style="background: var(--nx-mint-soft); color: var(--nx-forest);">
            {{ $pembayarans->count() }} Transaksi
        </span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4">Tanggal</th>
                    <th>Keterangan / Warga</th>
                    <th>Kategori</th>
                    <th>Tipe</th>
                    <th class="pe-4 text-end">Jumlah Nominal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pembayarans as $p)
                    <tr>
                        <td class="ps-4 small fw-semibold">{{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d M Y') }}</td>
                        <td>
                            <strong class="d-block" style="color: var(--nx-text);">
                                {{ $p->warga ? $p->warga->nama : 'Kas Umum / Non-Warga' }}
                            </strong>
                            @if($p->keterangan)<small class="text-muted d-block">{{ $p->keterangan }}</small>@endif
                        </td>
                        <td>
                            <span class="badge rounded-pill px-3 py-1" style="background: var(--nx-bg); color: var(--nx-text); border: 1px solid var(--nx-border);">
                                {{ $p->kategori }}
                            </span>
                        </td>
                        <td>
                            @if($p->tipe == 'masuk')
                                <span class="badge rounded-pill px-3 py-1" style="background: var(--nx-mint); color: var(--nx-forest);">Masuk</span>
                            @else
                                <span class="badge rounded-pill px-3 py-1" style="background: var(--nx-soft-red); color: var(--nx-danger);">Keluar</span>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            <strong style="color: {{ $p->tipe == 'masuk' ? 'var(--nx-forest)' : 'var(--nx-danger)' }};">
                                {{ $p->tipe == 'masuk' ? '+' : '-' }} Rp {{ number_format($p->jumlah, 0, ',', '.') }}
                            </strong>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 56px; height: 56px; background: var(--nx-mint-soft); color: var(--nx-forest);">
                                <i class="fas fa-folder-open fa-lg"></i>
                            </div>
                            <h6 class="fw-bold mb-1">Tidak Ada Transaksi pada Periode Ini</h6>
                            <p class="text-muted small mb-0">Belum ada pencatatan kas masuk atau keluar pada bulan dan tahun yang dipilih.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
