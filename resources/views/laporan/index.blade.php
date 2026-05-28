@extends('layouts.app')

@section('title', 'Laporan Keuangan Bulanan')

@section('content')
<div class="container-fluid">
    <div class="card mb-4" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 15px;">
        <div class="card-body p-4">
            <form action="{{ route('laporan.index') }}" method="GET" class="row align-items-end">
                <div class="col-md-4 mb-3 mb-md-0">
                    <label class="form-label text-white-50 small">Pilih Bulan</label>
                    <select name="bulan" class="form-select bg-dark text-white border-secondary">
                        @foreach([
                            '01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April',
                            '05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus',
                            '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'
                        ] as $key => $namaBulan)
                            <option value="{{ $key }}" {{ $bulan == $key ? 'selected' : '' }}>{{ $namaBulan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <label class="form-label text-white-50 small">Pilih Tahun</label>
                    <select name="tahun" class="form-select bg-dark text-white border-secondary">
                        @foreach($daftarTahun as $t)
                            <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i>Filter Data
                    </button>
                    <a href="{{ route('laporan.cetak_bulanan', ['bulan' => $bulan, 'tahun' => $tahun]) }}" target="_blank" class="btn btn-success w-100">
                        <i class="fas fa-file-pdf me-2"></i>Cetak PDF
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-dark text-white border-secondary p-3">
                <small class="text-white-50">Saldo Awal Bulan</small>
                <h4 class="mb-0 text-info">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-dark text-white border-secondary p-3">
                <small class="text-white-50">Total Pemasukan</small>
                <h4 class="mb-0 text-success">+ Rp {{ number_format($totalMasuk, 0, ',', '.') }}</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-dark text-white border-secondary p-3">
                <small class="text-white-50">Total Pengeluaran</small>
                <h4 class="mb-0 text-danger">- Rp {{ number_format($totalKeluar, 0, ',', '.') }}</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-dark text-white border-secondary p-3">
                <small class="text-white-50">Saldo Akhir Kas</small>
                <h4 class="mb-0 text-primary">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>

    <div class="card" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 15px;">
        <div class="card-header bg-transparent border-bottom border-secondary p-3">
            <h5 class="mb-0 text-white"><i class="fas fa-table me-2"></i>Preview Transaksi</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead>
                        <tr class="text-white-50 small" style="background: rgba(255,255,255,0.02);">
                            <th class="p-3">Tanggal</th>
                            <th class="p-3">Keterangan / Warga</th>
                            <th class="p-3">Kategori</th>
                            <th class="p-3 text-center">Tipe</th>
                            <th class="p-3 text-end">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pembayarans as $p)
                        <tr class="border-bottom border-secondary">
                            <td class="p-3 text-white-50">{{ date('d M Y', strtotime($p->tanggal)) }}</td>
                            <td class="p-3">
                                <strong class="text-white">{{ $p->warga ? $p->warga->nama : 'Umum / Non-Warga' }}</strong>
                                @if($p->keterangan)<br><small class="text-muted italic">{{ $p->keterangan }}</small>@endif
                            </td>
                            <td class="p-3"><span class="badge bg-secondary p-2">{{ $p->kategori }}</span></td>
                            <td class="p-3 text-center">
                                <span class="badge {{ $p->tipe == 'masuk' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $p->tipe == 'masuk' ? 'Masuk' : 'Keluar' }}
                                </span>
                            </td>
                            <td class="p-3 text-end {{ $p->tipe == 'masuk' ? 'text-success' : 'text-danger' }}fw-bold">
                                {{ $p->tipe == 'masuk' ? '+' : '-' }} Rp {{ number_format($p->jumlah, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center p-4 text-white-50">Tidak ada transaksi di bulan ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
