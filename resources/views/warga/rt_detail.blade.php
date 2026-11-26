@extends('layouts.app')

@section('title', 'Data Warga RT ' . $rtPad . ' — NexaNest')

@section('content')
<style>
    .rt-breadcrumb {
        font-size: 0.88rem;
        font-weight: 600;
        margin-bottom: 16px;
    }
    .rt-breadcrumb a {
        color: var(--nx-forest);
        text-decoration: none;
    }
    .rt-breadcrumb a:hover {
        text-decoration: underline;
    }
    .rt-summary-card {
        background: linear-gradient(135deg, #FFFFFF 0%, #F4F8F5 100%);
        border: 1px solid var(--nx-border);
        border-radius: var(--nx-radius-lg, 20px);
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: var(--nx-shadow);
    }
    .rt-filter-card {
        background: #FFFFFF;
        border: 1px solid var(--nx-border);
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 24px;
        box-shadow: var(--nx-shadow);
    }

    /* Dark Mode Support */
    html.dark .rt-summary-card {
        background: linear-gradient(135deg, #0F172A 0%, #172554 55%, #1E3A8A 100%) !important;
        border-color: rgba(30, 58, 138, 0.6) !important;
        color: #F1F5F9 !important;
    }
    html.dark .rt-filter-card {
        background: #131B2E !important;
        border-color: #1E2D4A !important;
        color: #F1F5F9 !important;
    }
</style>

<!-- BREADCRUMB (Point 5) -->
<nav class="rt-breadcrumb d-flex align-items-center gap-2" aria-label="breadcrumb">
    <a href="{{ route('warga.index') }}">
        <i class="fas fa-database me-1"></i> Database Warga
    </a>
    <i class="fas fa-chevron-right text-muted small" style="font-size: 0.75rem;"></i>
    <span class="text-dark">RT {{ $rtPad }}</span>
</nav>

<!-- 5.A. RINGKASAN RT -->
<div class="rt-summary-card">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 pb-3 mb-3 border-bottom">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge px-3 py-1 fs-6 fw-bold" style="background: var(--nx-forest); color: #FFFFFF; border-radius: 8px;">
                    RT {{ $rtStats['rt_number'] }}
                </span>
                <span class="badge" style="background: var(--nx-mint); color: var(--nx-forest); font-weight: 700;">
                    RW 018
                </span>
                <span class="badge" style="background: #E8F5E9; color: #2E7D32; font-weight: 600;">
                    <i class="fas fa-circle-check me-1"></i> Wilayah Aktif
                </span>
            </div>
            <h2 class="h4 fw-bold mb-1" style="color: var(--nx-forest); font-family: 'Plus Jakarta Sans', sans-serif;">
                Wilayah Rukun Tetangga (RT) {{ $rtStats['rt_number'] }}
            </h2>
            <div class="text-muted small d-flex align-items-center gap-3 mt-1">
                <span>
                    <i class="fas fa-user-tie text-muted me-1"></i> <strong>Ketua RT:</strong> {{ $rtStats['ketua_rt'] }}
                </span>
                @if($rtStats['kontak_rt'] !== '-')
                    <span>
                        <i class="fas fa-phone text-muted me-1"></i> {{ $rtStats['kontak_rt'] }}
                    </span>
                @endif
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('warga.export', ['rt' => $rtPad]) }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2"
               style="border-radius: 12px; font-weight: 600; padding: 9px 18px; border-color: var(--nx-border); background: #FFF;">
                <i class="fas fa-file-export text-muted"></i>
                <span>Ekspor RT {{ $rtPad }}</span>
            </a>
            <a href="{{ route('warga.create') }}?rt={{ $rtPad }}" class="btn btn-primary d-inline-flex align-items-center gap-2"
               style="background: var(--nx-forest); border-color: var(--nx-forest); border-radius: 12px; font-weight: 600; padding: 9px 18px;">
                <i class="fas fa-user-plus"></i>
                <span>Tambah Warga</span>
            </a>
        </div>
    </div>

    <!-- Statistik Ringkas RT -->
    <div class="row g-3 text-center">
        <div class="col-6 col-md-3">
            <div class="p-3 rounded-3" style="background: #FFFFFF; border: 1px solid var(--nx-border);">
                <span class="text-muted small d-block mb-1">Total Warga</span>
                <h3 class="fw-bold mb-0" style="color: var(--nx-forest);">{{ number_format($rtStats['total_warga']) }}</h3>
                <small class="text-muted" style="font-size: 0.72rem;">Jiwa terdaftar</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 rounded-3" style="background: #FFFFFF; border: 1px solid var(--nx-border);">
                <span class="text-muted small d-block mb-1">Kepala Keluarga (KK)</span>
                <h3 class="fw-bold mb-0" style="color: #2b567a;">{{ number_format($rtStats['total_kk']) }}</h3>
                <small class="text-muted" style="font-size: 0.72rem;">Kepala Keluarga</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 rounded-3" style="background: #FFFFFF; border: 1px solid var(--nx-border);">
                <span class="text-muted small d-block mb-1">Terverifikasi</span>
                <h3 class="fw-bold mb-0" style="color: #1b7a43;">{{ number_format($rtStats['total_terverifikasi']) }}</h3>
                <small class="text-muted" style="font-size: 0.72rem;">Berkas tervalidasi</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 rounded-3" style="background: #FFFFFF; border: 1px solid var(--nx-border);">
                <span class="text-muted small d-block mb-1">Belum Terverifikasi</span>
                <h3 class="fw-bold mb-0" style="color: var(--nx-warning);">{{ number_format($rtStats['total_belum_verifikasi']) }}</h3>
                <small class="text-muted" style="font-size: 0.72rem;">Menunggu / Perbaikan</small>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" style="background: #E8F5E9; color: #2E7D32; border-radius: 12px;" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- 5.C. PENCARIAN DAN FILTER RT -->
<div class="rt-filter-card">
    <form method="GET" action="{{ route('warga.rt', $rtPad) }}" class="row g-3 align-items-end">
        <div class="col-12 col-md-4">
            <label class="form-label small fw-bold">Pencarian di RT {{ $rtPad }}</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0"
                       value="{{ request('search') }}" placeholder="Cari Nama, Email, NIK, No. KK, atau Blok...">
            </div>
        </div>

        <div class="col-6 col-md-2">
            <label class="form-label small fw-bold">Status Verifikasi</label>
            <select name="status_verifikasi" class="form-select">
                <option value="">Semua Status</option>
                <option value="terverifikasi" {{ request('status_verifikasi') == 'terverifikasi' ? 'selected' : '' }}>Terverifikasi</option>
                <option value="menunggu" {{ request('status_verifikasi') == 'menunggu' ? 'selected' : '' }}>Menunggu Review</option>
                <option value="perbaikan" {{ request('status_verifikasi') == 'perbaikan' ? 'selected' : '' }}>Perlu Perbaikan</option>
                <option value="ditolak" {{ request('status_verifikasi') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>

        <div class="col-6 col-md-2">
            <label class="form-label small fw-bold">Status Kependudukan</label>
            <select name="status_warga" class="form-select">
                <option value="">Semua Status</option>
                <option value="aktif" {{ request('status_warga') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="pindah" {{ request('status_warga') == 'pindah' ? 'selected' : '' }}>Pindah</option>
                <option value="meninggal" {{ request('status_warga') == 'meninggal' ? 'selected' : '' }}>Meninggal</option>
            </select>
        </div>

        <div class="col-6 col-md-2">
            <label class="form-label small fw-bold">Jenis Kelamin</label>
            <select name="gender" class="form-select">
                <option value="">Semua Gender</option>
                <option value="L" {{ request('gender') == 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                <option value="P" {{ request('gender') == 'P' ? 'selected' : '' }}>Perempuan (P)</option>
            </select>
        </div>

        <div class="col-6 col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100 fw-bold" style="background: var(--nx-forest); border-color: var(--nx-forest); border-radius: 10px;">
                <i class="fas fa-filter me-1"></i> Filter
            </button>
            @if(request()->hasAny(['search', 'status_verifikasi', 'status_warga', 'gender', 'sort']))
                <a href="{{ route('warga.rt', $rtPad) }}" class="btn btn-outline-secondary" style="border-radius: 10px;" title="Reset Filter">
                    <i class="fas fa-rotate-left"></i>
                </a>
            @endif
        </div>
    </form>
</div>

<!-- 5.B. TABEL DATA WARGA -->
<div class="card overflow-hidden shadow-sm mb-4" style="border-radius: 16px; border: 1px solid var(--nx-border);">
    <div class="p-3 border-bottom bg-white d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0" style="color: var(--nx-forest);">
            <i class="fas fa-users me-2"></i>Daftar Warga RT {{ $rtPad }}
        </h6>
        <span class="text-muted small">
            Menampilkan {{ $wargas->total() }} warga terdaftar
        </span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
            <thead style="background: var(--nx-mint-soft); color: var(--nx-forest); font-weight: 700;">
                <tr>
                    <th class="ps-3 py-3" style="width: 50px;">No</th>
                    <th>Nama Warga</th>
                    <th>NIK</th>
                    <th>Nomor KK</th>
                    <th class="text-center">Gender</th>
                    <th>Usia</th>
                    <th>Hubungan Keluarga</th>
                    <th>Status Domisili</th>
                    <th>Status Verifikasi</th>
                    <th class="pe-3 text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($wargas as $index => $warga)
                    @php
                        $maskedNik = $warga->nik ? (strlen($warga->nik) >= 10 ? substr($warga->nik, 0, 6) . '******' . substr($warga->nik, -4) : $warga->nik) : '-';
                        $maskedKk = $warga->no_kk ? (strlen($warga->no_kk) >= 10 ? substr($warga->no_kk, 0, 6) . '******' . substr($warga->no_kk, -4) : $warga->no_kk) : '-';
                    @endphp
                    <tr>
                        <td class="ps-3 text-muted fw-bold">{{ $wargas->firstItem() + $index }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ $warga->profile_photo_url }}" alt="{{ $warga->nama }}"
                                     class="rounded-circle shadow-sm" style="width: 38px; height: 38px; object-fit: cover; border: 2px solid var(--nx-mint);">
                                <div>
                                    <a href="{{ route('warga.show', $warga->id) }}" class="fw-bold text-dark text-decoration-none hover-forest">
                                        {{ $warga->nama }}
                                    </a>
                                    <div class="text-xs text-slate-500" style="font-size: 0.74rem;">
                                        @if($warga->user && $warga->user->email)
                                            <span class="text-slate-500 font-monospace"><i class="fas fa-envelope text-muted me-1"></i>{{ $warga->user->email }}</span>
                                        @else
                                            <span class="text-slate-400 fst-italic">Belum punya akun</span>
                                        @endif
                                    </div>
                                    <div class="text-muted" style="font-size: 0.74rem;">
                                        Blok: {{ $warga->blok_rumah ?: '-' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="font-monospace text-dark" style="font-size: 0.82rem;" title="Disamarkan untuk privasi">
                                {{ $maskedNik }}
                            </span>
                        </td>
                        <td>
                            <span class="font-monospace text-muted" style="font-size: 0.82rem;">
                                {{ $maskedKk }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($warga->gender === 'L')
                                <span class="badge" style="background: var(--nx-powder); color: #2b567a; font-weight: 700;">L</span>
                            @elseif($warga->gender === 'P')
                                <span class="badge" style="background: #FDF2F4; color: #c2527a; font-weight: 700;">P</span>
                            @else
                                <span class="badge bg-light text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-dark fw-semibold" style="font-size: 0.82rem;">
                                {{ $warga->umur_formatted }}
                            </span>
                        </td>
                        <td>
                            @if($warga->is_kk)
                                <span class="badge" style="background: var(--nx-mint); color: var(--nx-forest); font-weight: 700;">
                                    <i class="fas fa-crown me-1"></i> Kepala Keluarga
                                </span>
                            @else
                                <span class="badge bg-light text-muted border" style="font-weight: 600;">
                                    {{ $warga->status_hubungan }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($warga->status_warga === 'pindah')
                                <span class="badge bg-secondary">Pindah</span>
                            @elseif($warga->status_warga === 'meninggal')
                                <span class="badge bg-dark">Meninggal</span>
                            @else
                                <span class="badge" style="background: #E8F5E9; color: #2E7D32;">Aktif</span>
                            @endif
                        </td>
                        <td>
                            @if($warga->status_verifikasi === 'terverifikasi')
                                <span class="badge" style="background: #E8F5E9; color: #2E7D32; font-weight: 700;">
                                    <i class="fas fa-check-circle me-1"></i> Terverifikasi
                                </span>
                            @elseif($warga->status_verifikasi === 'perbaikan')
                                <span class="badge" style="background: var(--nx-powder-soft); color: #1e4d72; font-weight: 700;">
                                    <i class="fas fa-wrench me-1"></i> Perbaikan
                                </span>
                            @elseif($warga->status_verifikasi === 'ditolak')
                                <span class="badge" style="background: var(--nx-danger-soft); color: var(--nx-danger); font-weight: 700;">
                                    <i class="fas fa-times-circle me-1"></i> Ditolak
                                </span>
                            @else
                                <span class="badge" style="background: var(--nx-warning-soft); color: var(--nx-warning); font-weight: 700;">
                                    <i class="fas fa-hourglass-half me-1"></i> Menunggu
                                </span>
                            @endif
                        </td>
                        <td class="pe-3 text-end">
                            <a href="{{ route('warga.show', $warga->id) }}" class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1"
                               style="border-color: var(--nx-forest); color: var(--nx-forest); border-radius: 8px; font-weight: 600; font-size: 0.8rem;">
                                <i class="fas fa-id-card"></i>
                                <span>Lihat Profil</span>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            <i class="fas fa-users-slash fa-2x mb-2 d-block text-muted opacity-50"></i>
                            <div class="fw-bold">Tidak ada data warga ditemukan di RT {{ $rtPad }}</div>
                            <small>Ubah kriteria pencarian atau tambahkan warga baru ke RT ini.</small>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($wargas->hasPages())
        <div class="d-flex justify-content-between align-items-center p-3 border-top bg-light">
            <small class="text-muted">
                Menampilkan {{ $wargas->firstItem() }} - {{ $wargas->lastItem() }} dari {{ $wargas->total() }} warga di RT {{ $rtPad }}
            </small>
            <div>
                {{ $wargas->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
