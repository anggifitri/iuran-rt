@extends('layouts.app')

@section('title', 'Mutasi Kependudukan Warga — NexaNest')

@section('content')
<div class="nx-page-header">
    <div>
        <span class="badge rounded-pill mb-2 px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
            <i class="fas fa-arrows-split-up-and-left me-1"></i> Administrasi Kependudukan
        </span>
        <h1 class="nx-page-title">Mutasi Warga (Pindah &amp; Datang)</h1>
        <p class="nx-page-subtitle">
            Pencatatan perpindahan warga masuk dan keluar lingkungan RW 018 dengan arsip riwayat kependudukan yang tetap tersimpan aman.
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('mutasi.create') }}" class="btn btn-primary" style="border-radius: 12px; font-weight: 700;">
            <i class="fas fa-plus me-1"></i> Catat Mutasi Warga
        </a>
    </div>
</div>

<!-- METRIC CARDS (STANDAR NEXANEST) -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <x-stat-card 
            title="Warga Pindah Masuk" 
            value="{{ number_format($totalMasuk) }} Warga" 
            sub="Menjadi warga baru" 
            icon="fas fa-arrow-right-to-bracket" 
            theme="forest" 
        />
    </div>
    <div class="col-md-4">
        <x-stat-card 
            title="Warga Pindah Keluar" 
            value="{{ number_format($totalKeluar) }} Warga" 
            sub="Status non-aktif domisili" 
            icon="fas fa-arrow-right-from-bracket" 
            theme="danger" 
            valColor="var(--nx-danger)" 
        />
    </div>
    <div class="col-md-4">
        <x-stat-card 
            title="Total Riwayat Mutasi" 
            value="{{ number_format($totalMutasi) }} Peristiwa" 
            sub="Arsip mutasi tercatat" 
            icon="fas fa-file-invoice" 
            theme="primary" 
            valColor="#1F567A" 
        />
    </div>
</div>

<!-- FILTER PANEL (STANDAR NEXANEST) -->
<div class="card nx-filter-card mb-4">
    <form method="GET" action="{{ route('mutasi.index') }}" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label small fw-bold">Pencarian Nama / NIK / Alasan</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" value="{{ request('search') }}" placeholder="Cari mutasi...">
            </div>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold">Jenis Mutasi</label>
            <select name="tipe" class="form-select">
                <option value="">-- Semua Jenis --</option>
                <option value="masuk" {{ request('tipe') == 'masuk' ? 'selected' : '' }}>Pindah Masuk (Warga Baru)</option>
                <option value="keluar" {{ request('tipe') == 'keluar' ? 'selected' : '' }}>Pindah Keluar</option>
            </select>
        </div>
        @if(Auth::user()->isAdminRw())
        <div class="col-md-2">
            <label class="form-label small fw-bold">Wilayah RT</label>
            <select name="rt_number" class="form-select">
                <option value="">-- Semua RT --</option>
                @foreach(['006', '007', '008', '009', '010'] as $rt)
                    <option value="{{ $rt }}" {{ request('rt_number') == $rt ? 'selected' : '' }}>RT {{ $rt }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1" style="border-radius: 12px; font-weight: 700;">
                <i class="fas fa-filter me-1"></i> Terapkan
            </button>
            <a href="{{ route('mutasi.index') }}" class="btn btn-outline-secondary" title="Reset" style="border-radius: 12px;">
                <i class="fas fa-undo"></i>
            </a>
        </div>
    </form>
</div>

<!-- TABLE MUTASI (STANDAR TABEL NEXANEST) -->
<div class="card nx-table-card overflow-hidden shadow-xs border-0 mb-4">
    <div class="card-header bg-white p-3.5 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="fw-bold mb-0 text-dark" style="font-size: 0.96rem;">
                <i class="fas fa-clock-rotate-left text-success me-2"></i>Daftar Riwayat Mutasi Warga
            </h5>
            <small class="text-muted" style="font-size: 0.75rem;">Arsip pergerakan kependudukan pindah masuk dan pindah keluar lingkungan RW 018</small>
        </div>
        <span class="badge rounded-pill px-3 py-1.5" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-weight: 700; font-size: 0.74rem;">
            {{ $mutasis->total() }} Data Tercatat
        </span>
    </div>
    <div class="table-responsive nx-table-responsive">
        <table class="table table-hover align-middle nx-table mb-0">
            <thead>
                <tr>
                    <th class="ps-4" style="width: 15%;">Tanggal</th>
                    <th style="width: 25%;">Nama Warga</th>
                    <th style="width: 15%;">Jenis Mutasi</th>
                    <th style="width: 27%;">Keterangan Asal &amp; Tujuan</th>
                    <th class="pe-4 text-end" style="width: 18%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mutasis as $m)
                    <tr>
                        <td class="ps-4">
                            <strong class="d-block text-dark">{{ \Carbon\Carbon::parse($m->tanggal_mutasi)->format('d M Y') }}</strong>
                            <small class="text-muted" style="font-size: 0.72rem;">Dicatat: {{ $m->created_at->format('H:i') }} WIB</small>
                        </td>
                        <td>
                            <strong class="d-block text-dark">{{ $m->nama_warga }}</strong>
                            <small class="text-muted" style="font-size: 0.75rem;">NIK: {{ $m->nik ?: '-' }}</small>
                        </td>
                        <td>
                            @if($m->tipe == 'masuk')
                                <x-status-badge status="aktif" label="Pindah Masuk" icon="fas fa-arrow-right-to-bracket" />
                            @else
                                <x-status-badge status="ditolak" label="Pindah Keluar" icon="fas fa-arrow-right-from-bracket" />
                            @endif
                        </td>
                        <td>
                            @if($m->tipe == 'masuk')
                                <div><small class="text-muted">Asal:</small> <span class="fw-semibold text-dark">{{ $m->alamat_asal ?: 'Luar Wilayah' }}</span></div>
                                <div><small class="text-muted">Tujuan:</small> <span class="fw-bold text-success">RT {{ $m->rt_tujuan }} (RW 018)</span></div>
                            @else
                                <div><small class="text-muted">Asal:</small> <span class="fw-bold text-dark">RT {{ $m->rt_asal }}</span></div>
                                <div><small class="text-muted">Tujuan:</small> <span class="fw-semibold text-muted">{{ $m->alamat_tujuan ?: 'Luar Wilayah' }}</span></div>
                            @endif
                            <small class="d-block text-muted text-truncate mt-1" style="max-width: 240px; font-size: 0.72rem;" title="{{ $m->alasan }}">
                                Alasan: {{ $m->alasan }}
                            </small>
                        </td>
                        <td class="pe-4 text-end">
                            <a href="{{ route('mutasi.show', $m->id) }}" class="btn btn-sm btn-outline-secondary px-2.5 py-1" style="border-radius: 8px; font-size: 0.78rem;">
                                <i class="fas fa-file-lines me-1 text-primary"></i> Detail Berkas
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-0 border-0">
                            <x-empty-state
                                icon="fas fa-people-arrows"
                                title="Belum Ada Catatan Mutasi"
                                message="Tidak ditemukan riwayat mutasi kependudukan untuk filter ini. Klik tombol di bawah untuk mencatat peristiwa mutasi warga."
                                actionUrl="{{ route('mutasi.create') }}"
                                actionLabel="Catat Mutasi Warga"
                                actionIcon="fas fa-plus"
                            />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($mutasis->hasPages())
        <div class="card-footer bg-white p-3.5 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="small text-muted">Menampilkan {{ $mutasis->firstItem() }} - {{ $mutasis->lastItem() }} dari {{ $mutasis->total() }} data</span>
            {{ $mutasis->links() }}
        </div>
    @endif
</div>
@endsection
