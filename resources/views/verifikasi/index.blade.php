@extends('layouts.app')

@section('title', 'Verifikasi Data Warga — NexaNest')

@section('content')
<div class="nx-page-header">
    <div>
        <span class="badge rounded-pill mb-2 px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
            <i class="fas fa-user-check me-1"></i> Administrasi Kependudukan
        </span>
        <h1 class="nx-page-title">Verifikasi Data Warga</h1>
        <p class="nx-page-subtitle">
            Pusat validasi berkas identitas, keabsahan NIK &amp; KK warga baru atau pembaruan data sebelum disahkan secara resmi di lingkungan RW 018.
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('warga.create') }}" class="btn btn-primary" style="border-radius: 12px; font-weight: 700;">
            <i class="fas fa-plus me-1"></i> Tambah Warga Baru
        </a>
    </div>
</div>

<!-- METRIC CARDS -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card p-3 h-100 d-flex flex-row align-items-center justify-content-between" style="background: var(--nx-warning-soft) !important; border-color: #F6D89E !important;">
            <div>
                <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem;">Menunggu Review</span>
                <h3 class="fw-bold mb-0" style="color: var(--nx-warning);">{{ number_format($totalMenunggu) }}</h3>
                <small class="text-muted" style="font-size: 0.72rem;">Perlu ditindaklanjuti</small>
            </div>
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: #FCE8BF; color: var(--nx-warning);">
                <i class="fas fa-hourglass-half fa-lg"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card p-3 h-100 d-flex flex-row align-items-center justify-content-between" style="background: var(--nx-mint-soft) !important; border-color: var(--nx-sage) !important;">
            <div>
                <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem;">Terverifikasi Sah</span>
                <h3 class="fw-bold mb-0" style="color: var(--nx-forest);">{{ number_format($totalTerverifikasi) }}</h3>
                <small class="text-muted" style="font-size: 0.72rem;">Data resmi aktif</small>
            </div>
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: var(--nx-mint); color: var(--nx-forest);">
                <i class="fas fa-circle-check fa-lg"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card p-3 h-100 d-flex flex-row align-items-center justify-content-between" style="background: var(--nx-powder-soft) !important; border-color: var(--nx-pastel-blue) !important;">
            <div>
                <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem;">Perlu Perbaikan</span>
                <h3 class="fw-bold mb-0" style="color: #2b567a;">{{ number_format($totalPerbaikan) }}</h3>
                <small class="text-muted" style="font-size: 0.72rem;">Dikembalikan ke warga</small>
            </div>
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: var(--nx-powder); color: #2b567a;">
                <i class="fas fa-wrench fa-lg"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card p-3 h-100 d-flex flex-row align-items-center justify-content-between" style="background: var(--nx-danger-soft) !important; border-color: #F5C2C5 !important;">
            <div>
                <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem;">Pengajuan Ditolak</span>
                <h3 class="fw-bold mb-0" style="color: var(--nx-danger);">{{ number_format($totalDitolak) }}</h3>
                <small class="text-muted" style="font-size: 0.72rem;">Berkas tidak valid</small>
            </div>
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: #F9D2D5; color: var(--nx-danger);">
                <i class="fas fa-times-circle fa-lg"></i>
            </div>
        </div>
    </div>
</div>

<!-- FILTER PANEL -->
<div class="card p-4 mb-4">
    <form method="GET" action="{{ route('verifikasi.index') }}" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label small fw-bold">Pencarian Nama / NIK / No. KK</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" value="{{ request('search') }}" placeholder="Cari warga...">
            </div>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold">Status Verifikasi</label>
            <select name="status" class="form-select">
                <option value="">-- Semua Status --</option>
                <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu Review</option>
                <option value="terverifikasi" {{ request('status') == 'terverifikasi' ? 'selected' : '' }}>Terverifikasi</option>
                <option value="perbaikan" {{ request('status') == 'perbaikan' ? 'selected' : '' }}>Perlu Perbaikan</option>
                <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
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
            <a href="{{ route('verifikasi.index') }}" class="btn btn-outline-secondary" title="Reset" style="border-radius: 12px;">
                <i class="fas fa-undo"></i>
            </a>
        </div>
    </form>
</div>

<!-- TABLE VERIFIKASI (STANDAR TABEL NEXANEST) -->
<div class="card nx-table-card overflow-hidden shadow-xs border-0 mb-4">
    <div class="card-header bg-white p-3.5 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="fw-bold mb-0 text-dark" style="font-size: 0.96rem;">
                <i class="fas fa-users-viewfinder text-success me-2"></i>Daftar Pengajuan &amp; Status Verifikasi Warga
            </h5>
            <small class="text-muted" style="font-size: 0.75rem;">Antrean verifikasi berkas identitas kependudukan tingkat RT</small>
        </div>
        <span class="badge rounded-pill px-3 py-1.5" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-weight: 700; font-size: 0.74rem;">
            Total: {{ $wargas->total() }} Warga
        </span>
    </div>
    <div class="table-responsive nx-table-responsive">
        <table class="table table-hover align-middle nx-table mb-0">
            <thead>
                <tr>
                    <th class="ps-4" style="width: 28%;">Identitas Warga</th>
                    <th style="width: 20%;">Nomor Kependudukan</th>
                    <th style="width: 15%;">Wilayah / Blok</th>
                    <th style="width: 17%;">Status Verifikasi</th>
                    <th class="pe-4 text-end" style="width: 20%;">Aksi Petugas</th>
                </tr>
            </thead>
            <tbody>
                @forelse($wargas as $w)
                    @php
                        // Mask NIK for privacy protection (keep first 6 and last 4)
                        $maskedNik = $w->nik ? (strlen($w->nik) >= 12 ? substr($w->nik, 0, 6) . '******' . substr($w->nik, -4) : $w->nik) : '-';
                    @endphp
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ $w->profile_photo_url }}" alt="{{ $w->nama }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover; border: 2px solid var(--nx-mint);">
                                <div>
                                    <strong class="d-block text-dark">{{ $w->nama }}</strong>
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        {{ $w->gender == 'L' ? 'Laki-laki' : 'Perempuan' }} · {{ $w->status_hubungan }}
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.7rem;">NIK (Terproteksi):</small>
                                <code class="fw-bold text-dark">{{ $maskedNik }}</code>
                            </div>
                            <small class="text-muted" style="font-size: 0.72rem;">No. KK: {{ $w->no_kk ?: '-' }}</small>
                        </td>
                        <td>
                            <strong class="d-block" style="color: var(--nx-forest);">RT {{ $w->rt_number }} / RW {{ $w->rw_number ?? '018' }}</strong>
                            <small class="text-muted">Blok {{ $w->blok_rumah ?: '-' }}</small>
                        </td>
                        <td>
                            @if($w->status_verifikasi == 'terverifikasi')
                                <x-status-badge status="terverifikasi" label="Terverifikasi" />
                            @elseif($w->status_verifikasi == 'perbaikan')
                                <x-status-badge status="perlu_perbaikan" label="Perlu Perbaikan" />
                            @elseif($w->status_verifikasi == 'ditolak')
                                <x-status-badge status="ditolak" label="Ditolak" />
                            @else
                                <x-status-badge status="menunggu" label="Menunggu Review" />
                            @endif
                            @if($w->catatan_verifikasi)
                                <small class="d-block text-muted text-truncate mt-1" style="max-width: 170px; font-size: 0.72rem;" title="{{ $w->catatan_verifikasi }}">
                                    <em>"{{ $w->catatan_verifikasi }}"</em>
                                </small>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            <div class="d-inline-flex gap-1.5">
                                <a href="{{ route('verifikasi.show', $w->id) }}" class="btn btn-sm btn-outline-secondary px-2.5 py-1" style="border-radius: 8px; font-weight: 600; font-size: 0.78rem;" title="Periksa Detail Lengkap">
                                    <i class="fas fa-eye me-1 text-primary"></i> Detail
                                </a>

                                <!-- Button Modal Quick Action -->
                                <button type="button" class="btn btn-sm btn-primary px-2.5 py-1" style="border-radius: 8px; font-size: 0.78rem;" data-bs-toggle="modal" data-bs-target="#modalVerif{{ $w->id }}">
                                    <i class="fas fa-stamp me-1"></i> Verifikasi
                                </button>
                            </div>

                            <!-- QUICK VERIFY MODAL -->
                            <div class="modal fade" id="modalVerif{{ $w->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered text-start">
                                    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                                        <form action="{{ route('verifikasi.update_status', $w->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header border-bottom py-3">
                                                <h5 class="modal-title fw-bold" style="font-size: 1rem; color: var(--nx-forest);">
                                                    <i class="fas fa-shield-halved me-2"></i>Verifikasi: {{ $w->nama }}
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Pilih Keputusan Verifikasi</label>
                                                    <select name="status_verifikasi" class="form-select" required>
                                                        <option value="terverifikasi" {{ $w->status_verifikasi == 'terverifikasi' ? 'selected' : '' }}>✅ Sahkan &amp; Terverifikasi</option>
                                                        <option value="perbaikan" {{ $w->status_verifikasi == 'perbaikan' ? 'selected' : '' }}>⚠️ Minta Perbaikan Berkas</option>
                                                        <option value="ditolak" {{ $w->status_verifikasi == 'ditolak' ? 'selected' : '' }}>❌ Tolak Pengajuan</option>
                                                        <option value="menunggu" {{ $w->status_verifikasi == 'menunggu' ? 'selected' : '' }}>⏳ Kembalikan ke Menunggu</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Catatan Petugas (Opsional / Alasan)</label>
                                                    <textarea name="catatan_verifikasi" rows="3" class="form-control" placeholder="Tuliskan catatan pemeriksaan atau instruksi perbaikan data...">{{ $w->catatan_verifikasi }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-top py-2.5">
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary btn-sm px-3">Simpan Keputusan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-0 border-0">
                            <x-empty-state
                                icon="fas fa-id-card-clip"
                                title="Tidak Ada Data Pengajuan Verifikasi"
                                message="Semua berkas warga di wilayah ini telah terverifikasi dengan baik."
                            />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($wargas->hasPages())
        <div class="card-footer bg-white p-3.5 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="small text-muted">Menampilkan {{ $wargas->firstItem() }} - {{ $wargas->lastItem() }} dari {{ $wargas->total() }} warga</span>
            {{ $wargas->links() }}
        </div>
    @endif
</div>
@endsection
