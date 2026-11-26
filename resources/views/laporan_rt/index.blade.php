@extends('layouts.app')

@section('title', 'Laporan Bulanan RT & Pemeriksaan RW — NexaNest')

@section('content')
<div class="nx-page-header">
    <div>
        <span class="badge rounded-pill mb-2 px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
            <i class="fas fa-file-invoice me-1"></i> Tata Kelola &amp; Akuntabilitas
        </span>
        <h1 class="nx-page-title">Sistem Pelaporan RT &amp; Pemeriksaan RW</h1>
        <p class="nx-page-subtitle">
            Alur pelaporan resmi berkala antara Admin RT dan Admin RW berbasis snapshot data otomatis, evaluasi, kendala, dan riwayat revisi.
        </p>
    </div>
    <div class="d-flex gap-2">
        @if(Auth::user()->isAdminRt() || Auth::user()->isAdminRw())
        <a href="{{ route('laporan_rt.create') }}" class="btn btn-primary" style="border-radius: 12px; font-weight: 700;">
            <i class="fas fa-plus me-1"></i> Buat Laporan Periode Ini
        </a>
        @endif
    </div>
</div>

<!-- METRIC CARDS (STANDAR NEXANEST) -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <x-stat-card 
            title="Laporan Diterima" 
            value="{{ number_format($countDiterima) }}" 
            sub="Disahkan oleh Admin RW" 
            icon="fas fa-check-double" 
            theme="success" 
            valColor="#059669" 
        />
    </div>
    <div class="col-md-3 col-6">
        <x-stat-card 
            title="Menunggu Review" 
            value="{{ number_format($countMenunggu) }}" 
            sub="Perlu ditinjau Admin RW" 
            icon="fas fa-hourglass-half" 
            theme="warning" 
            valColor="#D97706" 
        />
    </div>
    <div class="col-md-3 col-6">
        <x-stat-card 
            title="Perlu Revisi" 
            value="{{ number_format($countRevisi) }}" 
            sub="Dikembalikan dengan catatan" 
            icon="fas fa-triangle-exclamation" 
            theme="danger" 
            valColor="var(--nx-danger)" 
        />
    </div>
    <div class="col-md-3 col-6">
        <x-stat-card 
            title="Draft Laporan" 
            value="{{ number_format($countDraft) }}" 
            sub="Belum diserahkan RT" 
            icon="fas fa-pencil" 
            theme="primary" 
            valColor="#1F567A" 
        />
    </div>
</div>

<!-- FILTER -->
<div class="card p-4 mb-4">
    <form method="GET" action="{{ route('laporan_rt.index') }}" class="row g-3 align-items-end">
        @if(Auth::user()->isAdminRw())
        <div class="col-md-3">
            <label class="form-label small fw-bold">Wilayah RT</label>
            <select name="rt_number" class="form-select">
                <option value="">-- Semua RT --</option>
                @foreach(['006', '007', '008', '009', '010'] as $rt)
                    <option value="{{ $rt }}" {{ request('rt_number') == $rt ? 'selected' : '' }}>RT {{ $rt }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div class="col-md-3">
            <label class="form-label small fw-bold">Status Laporan</label>
            <select name="status" class="form-select">
                <option value="">-- Semua Status --</option>
                <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu Review</option>
                <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima RW</option>
                <option value="perlu_revisi" {{ request('status') == 'perlu_revisi' ? 'selected' : '' }}>Perlu Revisi</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold">Tahun</label>
            <select name="tahun" class="form-select">
                <option value="">-- Semua Tahun --</option>
                @foreach(range(date('Y'), date('Y') - 4) as $y)
                    <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1" style="border-radius: 12px; font-weight: 700;">
                <i class="fas fa-filter me-1"></i> Filter
            </button>
            <a href="{{ route('laporan_rt.index') }}" class="btn btn-outline-secondary" style="border-radius: 12px;">
                <i class="fas fa-undo"></i>
            </a>
        </div>
    </form>
</div>

<!-- TABLE LAPORAN RT (STANDAR TABEL NEXANEST) -->
<div class="card nx-table-card overflow-hidden shadow-xs border-0 mb-4">
    <div class="card-header bg-white p-3.5 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="fw-bold mb-0 text-dark" style="font-size: 0.96rem;">
                <i class="fas fa-clipboard-list text-success me-2"></i>Daftar Laporan Resmi Bulanan RT
            </h5>
            <small class="text-muted" style="font-size: 0.75rem;">Dokumen pelaporan berkala dari para Admin RT kepada Admin RW 018</small>
        </div>
        <span class="badge rounded-pill px-3 py-1.5" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-weight: 700; font-size: 0.74rem;">
            Total: {{ $laporans->total() }} Berkas
        </span>
    </div>
    <div class="table-responsive nx-table-responsive">
        <table class="table table-hover align-middle nx-table mb-0">
            <thead>
                <tr>
                    <th class="ps-4" style="width: 14%;">Periode</th>
                    <th style="width: 14%;">Wilayah</th>
                    <th style="width: 18%;">Status</th>
                    <th style="width: 20%;">Pengirim &amp; Tanggal</th>
                    <th style="width: 20%;">Pemeriksa RW</th>
                    <th class="pe-4 text-end" style="width: 14%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($laporans as $lap)
                    <tr>
                        <td class="ps-4">
                            <strong class="d-block text-dark">{{ \Carbon\Carbon::createFromDate($lap->tahun, $lap->bulan, 1)->translatedFormat('F Y') }}</strong>
                            <small class="text-muted" style="font-size: 0.72rem;">Versi Dokumen: #{{ $lap->version }}</small>
                        </td>
                        <td>
                            <strong class="d-block text-dark">RT {{ $lap->rt_number }}</strong>
                            <small class="text-muted" style="font-size: 0.72rem;">RW 018</small>
                        </td>
                        <td>
                            @if($lap->status == 'diterima')
                                <x-status-badge status="disetujui" label="Diterima RW" icon="fas fa-check-double" />
                            @elseif($lap->status == 'menunggu')
                                <x-status-badge status="menunggu" label="Menunggu Review" icon="fas fa-hourglass-half" />
                            @elseif($lap->status == 'perlu_revisi')
                                <x-status-badge status="ditolak" label="Perlu Revisi" icon="fas fa-triangle-exclamation" />
                            @else
                                <x-status-badge status="draft" label="Draft" icon="fas fa-pencil" />
                            @endif
                        </td>
                        <td>
                            <strong class="d-block text-dark small">{{ $lap->pengirim ? $lap->pengirim->name : 'Admin RT' }}</strong>
                            <small class="text-muted" style="font-size: 0.72rem;">{{ $lap->created_at->format('d/m/Y H:i') }} WIB</small>
                        </td>
                        <td>
                            @if($lap->pemeriksa)
                                <strong class="d-block text-dark small">{{ $lap->pemeriksa->name }}</strong>
                                <small class="text-muted" style="font-size: 0.72rem;">{{ $lap->waktu_pemeriksaan ? $lap->waktu_pemeriksaan->format('d/m/Y H:i') . ' WIB' : '-' }}</small>
                            @else
                                <span class="text-muted small">Belum diperiksa</span>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            <a href="{{ route('laporan_rt.show', $lap->id) }}" class="btn btn-sm btn-outline-primary px-3 py-1" style="border-radius: 8px; font-weight: 600; font-size: 0.78rem;">
                                <i class="fas fa-file-magnifying-glass me-1"></i> Periksa
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-0 border-0">
                            <x-empty-state
                                icon="fas fa-file-invoice"
                                title="Belum Ada Laporan Bulanan RT"
                                message="Belum ada laporan bulanan resmi RT yang diserahkan untuk kriteria filter ini."
                            />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($laporans->hasPages())
        <div class="card-footer bg-white p-3.5 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="small text-muted">Menampilkan {{ $laporans->firstItem() }} - {{ $laporans->lastItem() }} dari {{ $laporans->total() }} berkas</span>
            {{ $laporans->links() }}
        </div>
    @endif
</div>
@endsection
