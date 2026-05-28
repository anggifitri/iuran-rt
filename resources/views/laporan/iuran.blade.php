@extends('layouts.app')

@section('title', 'Laporan Iuran')

@section('content')
<div class="container-fluid">
    <div class="card mb-4 p-3" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 15px;">
        <form method="GET" action="{{ route('laporan.iuran') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label text-white-50 small">Bulan</label>
                <select name="bulan" class="form-select bg-dark text-white border-secondary">
                    @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $k => $n)
                        <option value="{{ $k }}" {{ (isset($bulan) && $bulan == $k) ? 'selected' : (date('m') == $k && !isset($bulan) ? 'selected' : '') }}>{{ $n }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label text-white-50 small">Tahun</label>
                <select name="tahun" class="form-select bg-dark text-white border-secondary">
                    @foreach($daftarTahun as $t)
                        <option value="{{ $t }}" {{ (isset($tahun) && $tahun == $t) ? 'selected' : (date('Y') == $t && !isset($tahun) ? 'selected' : '') }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label text-white-50 small">Warga (opsional)</label>
                <select name="warga_id" class="form-select bg-dark text-white border-secondary">
                    <option value="">Semua Warga</option>
                    @foreach($wargaList as $w)
                        <option value="{{ $w->id }}" {{ request('warga_id') == $w->id ? 'selected' : '' }}>{{ $w->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-primary">Tampilkan</button>
            </div>
        </form>
    </div>

    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('laporan.cetak_iuran', ['bulan' => $bulan, 'tahun' => $tahun, 'warga_id' => request('warga_id')]) }}" target="_blank" class="btn btn-success">
            <i class="fas fa-file-pdf me-1"></i> Cetak PDF
        </a>
    </div>

    <div class="card" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 15px;">
        <div class="card-header bg-transparent border-bottom border-secondary p-3">
            <h5 class="mb-0 text-white"><i class="fas fa-list me-2"></i>Daftar Ketaatan Bayar Iuran</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead>
                        <tr class="text-white-50 small" style="background: rgba(255,255,255,0.02);">
                            <th class="p-3">Nama Warga</th>
                            <th class="p-3">Status Iuran</th>
                            <th class="p-3">Tanggal Bayar</th>
                            <th class="p-3">Riwayat 3 Bulan Terakhir</th>
                            <th class="p-3">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $row)
                            <tr>
                                <td class="p-3">{{ $row['warga']->nama }}</td>
                                <td class="p-3">
                                    @if($row['status'] == 'Lunas')
                                        <span class="badge bg-success">Lunas</span>
                                    @else
                                        <span class="badge bg-danger">Belum Bayar</span>
                                    @endif
                                </td>
                                <td class="p-3">{{ $row['tanggal_bayar'] ? \Carbon\Carbon::parse($row['tanggal_bayar'])->format('d-m-Y') : '-' }}</td>
                                <td class="p-3">
                                    @foreach($row['history'] as $h)
                                        @if($h['paid'])
                                            <span class="badge bg-success me-1" title="{{ $h['month'] }}">●</span>
                                        @else
                                            <span class="badge bg-secondary text-dark me-1" title="{{ $h['month'] }}">○</span>
                                        @endif
                                    @endforeach
                                </td>
                                <td class="p-3">
                                    @if($row['keterangan'] == 'Rajin')
                                        <span class="badge bg-success">Rajin</span>
                                    @elseif($row['keterangan'] == 'Tidak Pernah Bayar')
                                        <span class="badge bg-danger">Tidak Pernah Bayar</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Kadang-kadang</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="p-3" colspan="5">Belum ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
