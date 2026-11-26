@extends('layouts.super_admin')

@section('title', 'Manajemen Akun & Akses Pengurus — NexaNest Super Admin')
@section('page_title', 'Manajemen Akun & Akses Pengurus')

@section('content')
<!-- TOP HEADER & CONTEXT -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h2 class="fw-bold m-0 fs-5" style="color: var(--nx-text);">Manajemen Akun Pengurus &amp; Hak Akses Platform</h2>
        <p class="m-0 small" style="color: var(--nx-muted);">Pusat pengelolaan akun Administrator RW &amp; RT, penugasan wilayah, dan matriks kewenangan platform NexaNest</p>
    </div>

    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-3 py-2 fw-bold d-inline-flex align-items-center gap-1.5" style="font-size: 0.78rem;">
            <i class="fas fa-user-shield text-warning"></i>
            <span>Pengurus-Only Scope</span>
        </span>
        <button type="button" class="btn btn-sm btn-success fw-semibold d-inline-flex align-items-center gap-2 px-3 py-2 shadow-sm" style="border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#modalTambahPengurus">
            <i class="fas fa-user-plus"></i>
            <span>Tambah Akun Pengurus</span>
        </button>
    </div>
</div>

<!-- SECTION A: RINGKASAN AKUN (KPI STATISTIK AKTUAL) -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="sa-kpi-card p-3 rounded-3 border h-100" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="small fw-semibold" style="color: var(--nx-muted);">Total Akun Pengurus</span>
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: rgba(30, 70, 51, 0.12); color: #1E4633;">
                    <i class="fas fa-users-gear"></i>
                </div>
            </div>
            <h3 class="fs-4 fw-bold m-0" style="color: var(--nx-text);">{{ $totalPengurus }} <span class="fs-6 fw-normal" style="color: var(--nx-muted);">Akun</span></h3>
            <div class="small mt-1 d-flex align-items-center gap-2" style="color: var(--nx-muted); font-size: 0.76rem;">
                <span>Admin RW: <strong>{{ $totalAdminRw }}</strong></span>
                <span>&bull;</span>
                <span>Admin RT: <strong>{{ $totalAdminRt }}</strong></span>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="sa-kpi-card p-3 rounded-3 border h-100" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="small fw-semibold" style="color: var(--nx-muted);">Status Pengurus</span>
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: rgba(13, 148, 136, 0.12); color: #0D9488;">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
            <h3 class="fs-4 fw-bold m-0 text-success">{{ $pengurusAktif }} <span class="fs-6 fw-normal" style="color: var(--nx-muted);">Aktif</span></h3>
            <div class="small mt-1 d-flex align-items-center gap-1 text-danger" style="font-size: 0.76rem;">
                <i class="fas fa-circle-exclamation" style="font-size: 0.70rem;"></i>
                <span>{{ $pengurusNonaktif }} Akun Nonaktif / Ditangguhkan</span>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="sa-kpi-card p-3 rounded-3 border h-100" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="small fw-semibold" style="color: var(--nx-muted);">Warga Terdaftar (Agregat)</span>
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: rgba(59, 130, 246, 0.12); color: #2563EB;">
                    <i class="fas fa-id-card"></i>
                </div>
            </div>
            <h3 class="fs-4 fw-bold m-0" style="color: var(--nx-text);">{{ number_format($totalWarga) }} <span class="fs-6 fw-normal" style="color: var(--nx-muted);">Jiwa</span></h3>
            <div class="small mt-1 d-flex align-items-center gap-1 text-primary" style="font-size: 0.76rem;">
                <i class="fas fa-mobile-screen"></i>
                <span>{{ $wargaAktif }} Akun Aplikasi Aktif</span>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="sa-kpi-card p-3 rounded-3 border h-100" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="small fw-semibold" style="color: var(--nx-muted);">Verifikasi Warga (Agregat)</span>
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: rgba(217, 119, 6, 0.12); color: #D97706;">
                    <i class="fas fa-shield-halved"></i>
                </div>
            </div>
            <h3 class="fs-4 fw-bold m-0 text-success">{{ number_format($totalWarga - $wargaBelumVerifikasi) }} <span class="fs-6 fw-normal" style="color: var(--nx-muted);">Valid</span></h3>
            <div class="small mt-1 d-flex align-items-center gap-1 text-warning" style="font-size: 0.76rem;">
                <i class="fas fa-clock"></i>
                <span>{{ $wargaBelumVerifikasi }} Warga Perlu Verifikasi RT</span>
            </div>
        </div>
    </div>
</div>

<!-- AGGREGATE DISTRIBUTION & PRINCIPLE NOTICE -->
<div class="p-3 rounded-3 border mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
    <div class="d-flex align-items-center gap-2">
        <i class="fas fa-circle-info text-primary fs-5"></i>
        <div class="small" style="color: var(--nx-text);">
            <strong>Prinsip Tata Kelola Platform:</strong>
            Super Admin hanya mengelola kredensial akun pengurus (Admin RW &amp; Admin RT). Administrasi, registrasi, dan verifikasi akun Warga dikelola secara mandiri oleh Admin RW dan Admin RT.
        </div>
    </div>
    <div class="d-flex align-items-center gap-1 flex-wrap">
        <span class="small text-muted me-1 font-monospace" style="font-size: 0.74rem;">Distribusi Warga RW 018:</span>
        @foreach($wargaDistribution as $rtNum => $count)
            <span class="badge font-monospace border" style="background: var(--nx-bg); border-color: var(--nx-border) !important; color: var(--nx-text); font-size: 0.74rem;">
                RT {{ $rtNum }}: <strong>{{ $count }}</strong>
            </span>
        @endforeach
    </div>
</div>

<!-- SECTION B: DAFTAR AKUN PENGURUS (TABEL UTAMA) -->
<div class="sa-card mb-4">
    <div class="sa-card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h4 class="sa-card-title">
                <i class="fas fa-user-shield text-success"></i>
                <span>Daftar Akun Pengurus Aktif (Admin RW &amp; Admin RT)</span>
            </h4>
            <p class="m-0 small" style="color: var(--nx-muted);">Menampilkan seluruh administrator wilayah yang memiliki hak kelola operasional pada platform NexaNest</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light text-dark border px-2.5 py-1.5" style="border-color: var(--nx-border) !important;">
                Total: <strong>{{ $pengurusList->total() }} Pengurus</strong>
            </span>
        </div>
    </div>

    <!-- FILTER & SEARCH BAR -->
    <div class="p-3 border-bottom" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
        <form method="GET" action="{{ route('super_admin.accounts.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text border-end-0" style="background: var(--nx-card-bg); border-color: var(--nx-border); color: var(--nx-muted);"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" style="background: var(--nx-card-bg); border-color: var(--nx-border); color: var(--nx-text);" placeholder="Cari nama pengurus, email, telepon..." value="{{ $search }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="role" class="form-select" style="background: var(--nx-card-bg); border-color: var(--nx-border); color: var(--nx-text);">
                    <option value="">Semua Peran Pengurus</option>
                    <option value="admin_rw" {{ $role === 'admin_rw' ? 'selected' : '' }}>Admin RW 018</option>
                    <option value="admin_rt" {{ $role === 'admin_rt' ? 'selected' : '' }}>Admin RT (006 - 010)</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="rt" class="form-select" style="background: var(--nx-card-bg); border-color: var(--nx-border); color: var(--nx-text);">
                    <option value="">Semua Penugasan RT</option>
                    <option value="000" {{ $rt === '000' ? 'selected' : '' }}>Tingkat RW (RW 018)</option>
                    @foreach($availableRts as $art)
                        <option value="{{ $art }}" {{ $rt === $art ? 'selected' : '' }}>RT {{ $art }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select" style="background: var(--nx-card-bg); border-color: var(--nx-border); color: var(--nx-text);">
                    <option value="">Semua Status</option>
                    <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="suspended" {{ $status === 'suspended' ? 'selected' : '' }}>Ditangguhkan</option>
                </select>
            </div>
            <div class="col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-outline-success fw-semibold w-100" style="border-radius: 8px;" title="Terapkan Filter">
                    <i class="fas fa-filter"></i>
                </button>
                @if($search || $role || $rt !== null && $rt !== '' || $status)
                    <a href="{{ route('super_admin.accounts.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;" title="Reset Filter">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- TABLE LISTING -->
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="color: var(--nx-text);">
            <thead style="background: var(--nx-bg); border-bottom: 2px solid var(--nx-border);">
                <tr>
                    <th class="ps-3 py-3 text-uppercase font-monospace small" style="font-size: 0.72rem; color: var(--nx-muted);">Pengurus</th>
                    <th class="py-3 text-uppercase font-monospace small" style="font-size: 0.72rem; color: var(--nx-muted);">Peran / Jabatan</th>
                    <th class="py-3 text-uppercase font-monospace small" style="font-size: 0.72rem; color: var(--nx-muted);">Wilayah Penugasan</th>
                    <th class="py-3 text-uppercase font-monospace small" style="font-size: 0.72rem; color: var(--nx-muted);">Kontak Telepon</th>
                    <th class="py-3 text-center text-uppercase font-monospace small" style="font-size: 0.72rem; color: var(--nx-muted);">Status Akun</th>
                    <th class="py-3 text-uppercase font-monospace small" style="font-size: 0.72rem; color: var(--nx-muted);">Terakhir Aktif</th>
                    <th class="pe-3 py-3 text-end text-uppercase font-monospace small" style="font-size: 0.72rem; color: var(--nx-muted);">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengurusList as $u)
                @php
                    $isRwAdmin = $u->isAdminRw();
                    $isRtAdmin = $u->isAdminRt();
                    $badgeRole = $isRwAdmin 
                        ? 'bg-warning-subtle text-dark border border-warning-subtle' 
                        : 'bg-primary-subtle text-primary border border-primary-subtle';
                @endphp
                <tr style="border-bottom: 1px solid var(--nx-border);">
                    <td class="ps-3 py-3">
                        <div class="d-flex align-items-center gap-2.5">
                            <img src="{{ $u->profile_photo_url }}" class="rounded-circle border" width="38" height="38" style="object-fit: cover; border-color: var(--nx-border) !important;" alt="avatar">
                            <div>
                                <strong class="d-block" style="color: var(--nx-text); font-size: 0.88rem;">{{ $u->name }}</strong>
                                <small class="font-monospace" style="color: var(--nx-muted); font-size: 0.74rem;">{{ $u->email }}</small>
                            </div>
                        </div>
                    </td>
                    <td class="py-3">
                        <span class="badge {{ $badgeRole }} px-2.5 py-1 fw-bold" style="font-size: 0.74rem;">
                            {{ $u->role_label }}
                        </span>
                    </td>
                    <td class="py-3">
                        <div class="d-flex align-items-center gap-1.5 flex-wrap">
                            <span class="badge font-monospace bg-light text-dark border px-2 py-0.5" style="font-size: 0.74rem; border-color: var(--nx-border) !important;">
                                RW {{ $u->rw_number ?: '018' }}
                            </span>
                            @if($isRwAdmin || $u->rt_number === '000')
                                <span class="badge font-monospace bg-warning-subtle text-dark border border-warning-subtle px-2 py-0.5" style="font-size: 0.74rem;">
                                    Tingkat RW (Seluruh RW 018)
                                </span>
                            @else
                                <span class="badge font-monospace bg-success-subtle text-success border border-success-subtle px-2 py-0.5" style="font-size: 0.74rem;">
                                    RT {{ $u->rt_number }}
                                </span>
                            @endif
                        </div>
                    </td>
                    <td class="py-3 font-monospace small" style="color: var(--nx-muted);">
                        {{ $u->phone ?: '-' }}
                    </td>
                    <td class="py-3 text-center">
                        @if($u->status === 'active')
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1">
                                <i class="fas fa-circle-check me-1"></i>Aktif
                            </span>
                        @else
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1">
                                <i class="fas fa-ban me-1"></i>Ditangguhkan
                            </span>
                        @endif
                    </td>
                    <td class="py-3 small font-monospace" style="color: var(--nx-muted); font-size: 0.76rem;">
                        {{ $u->last_active_at ? $u->last_active_at->diffForHumans() : ($u->created_at ? $u->created_at->format('d/m/Y') : '-') }}
                    </td>
                    <td class="pe-3 py-3 text-end">
                        <div class="d-inline-flex align-items-center gap-1">
                            <!-- Detail Modal Button -->
                            <button type="button" class="btn btn-sm btn-outline-secondary px-2 py-1" style="border-radius: 6px;" title="Lihat Detail Akun" data-bs-toggle="modal" data-bs-target="#modalDetailPengurus{{ $u->id }}">
                                <i class="fas fa-eye"></i>
                            </button>

                            <!-- Edit Modal Button -->
                            <button type="button" class="btn btn-sm btn-outline-primary px-2 py-1" style="border-radius: 6px;" title="Edit Data Pengurus" data-bs-toggle="modal" data-bs-target="#modalEditPengurus{{ $u->id }}">
                                <i class="fas fa-pen"></i>
                            </button>

                            <!-- Reassign Modal Button -->
                            <button type="button" class="btn btn-sm btn-outline-warning text-dark px-2 py-1" style="border-radius: 6px;" title="Alihkan Penugasan RT/RW" data-bs-toggle="modal" data-bs-target="#modalReassignPengurus{{ $u->id }}">
                                <i class="fas fa-people-arrows"></i>
                            </button>

                            <!-- Reset Password Button -->
                            <button type="button" class="btn btn-sm btn-outline-info text-dark px-2 py-1" style="border-radius: 6px;" title="Reset Kata Sandi" data-bs-toggle="modal" data-bs-target="#modalResetPassword{{ $u->id }}">
                                <i class="fas fa-key"></i>
                            </button>

                            <!-- Toggle Status Button -->
                            <button type="button" class="btn btn-sm {{ $u->status === 'active' ? 'btn-outline-danger' : 'btn-outline-success' }} px-2 py-1" style="border-radius: 6px;" title="{{ $u->status === 'active' ? 'Tangguhkan Akun (Non-destructive)' : 'Aktifkan Akun' }}" data-bs-toggle="modal" data-bs-target="#modalToggleStatus{{ $u->id }}">
                                <i class="fas {{ $u->status === 'active' ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4" style="color: var(--nx-muted);">
                        <i class="fas fa-user-slash fa-2x mb-2 d-block"></i>
                        Tidak ada akun pengurus ditemukan sesuai filter yang dipilih.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pengurusList->hasPages())
    <div class="p-3 border-top d-flex justify-content-end" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
        {{ $pengurusList->links() }}
    </div>
    @endif
</div>

<!-- SECTION D: PERAN & PENUGASAN (STRUKTUR KEPENGURUSAN RW 018) -->
<div class="row g-4 mb-4">
    <div class="col-lg-5">
        <div class="sa-card h-100">
            <div class="sa-card-header d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="sa-card-title fs-6">
                        <i class="fas fa-sitemap text-success"></i>
                        <span>Struktur Pengurus RW 018</span>
                    </h4>
                    <p class="m-0 small" style="color: var(--nx-muted);">Hirarki kepengurusan wilayah operasional aktif</p>
                </div>
                <span class="badge bg-success-subtle text-success border border-success-subtle">Aktif</span>
            </div>

            <!-- RW 018 NODE -->
            <div class="p-3 rounded-3 border mb-3" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small fw-bold text-success text-uppercase font-monospace" style="font-size: 0.74rem;">Pimpinan Wilayah (RW)</span>
                    <span class="badge bg-warning-subtle text-dark border border-warning-subtle font-monospace">RW 018</span>
                </div>
                <div class="d-flex align-items-center gap-2.5">
                    <img src="{{ $adminRwUser ? $adminRwUser->profile_photo_url : asset('images/avatar-default.png') }}" class="rounded-circle border" width="40" height="40" alt="avatar">
                    <div>
                        <strong class="d-block" style="color: var(--nx-text);">{{ $adminRwUser ? $adminRwUser->name : 'Belum Ditugaskan' }}</strong>
                        <small style="color: var(--nx-muted); font-size: 0.74rem;">{{ $adminRwUser ? $adminRwUser->email : '-' }}</small>
                    </div>
                </div>
            </div>

            <div class="small fw-bold mb-2 font-monospace text-uppercase" style="font-size: 0.72rem; color: var(--nx-muted);">
                <i class="fas fa-network-wired text-primary me-1"></i> Penanggung Jawab Rukun Tetangga (RT 006 s/d RT 010):
            </div>

            <div class="d-flex flex-column gap-2 sa-struktur-rt-list" style="max-height: 420px; overflow-y: auto;">
                @foreach($rts as $rtItem)
                <div class="p-2 rounded-3 border d-flex align-items-center justify-content-between" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace fw-bold px-2 py-0.5">
                            RT {{ $rtItem->rt_number }}
                        </span>
                        <div>
                            <strong class="d-block small" style="color: var(--nx-text);">
                                {{ $rtItem->adminUser ? $rtItem->adminUser->name : ($rtItem->nama_ketua_rt ?: 'Belum Ada Admin') }}
                            </strong>
                            <small style="color: var(--nx-muted); font-size: 0.70rem;">
                                Populasi: {{ $wargaDistribution[$rtItem->rt_number] ?? 0 }} Jiwa
                            </small>
                        </div>
                    </div>
                    <span class="badge {{ $rtItem->adminUser ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }}" style="font-size: 0.70rem;">
                        {{ $rtItem->adminUser ? 'Terisi' : 'Kosong' }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- SECTION E: HAK AKSES (MATRIKS RBAC PLATFORM) -->
    <div class="col-lg-7">
        <div class="sa-card h-100">
            <div class="sa-card-header d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="sa-card-title fs-6">
                        <i class="fas fa-shield-halved text-primary"></i>
                        <span>Matriks Kewenangan &amp; Pembatasan Akses (RBAC)</span>
                    </h4>
                    <p class="m-0 small" style="color: var(--nx-muted);">Pemisahan hak akses data berdasarkan peran dan wilayah penugasan</p>
                </div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Role Scoped</span>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle small mb-0" style="color: var(--nx-text); border-color: var(--nx-border);">
                    <thead style="background: var(--nx-bg);">
                        <tr class="text-center font-monospace" style="font-size: 0.72rem; color: var(--nx-muted);">
                            <th class="text-start py-2">Role / Peran</th>
                            <th class="py-2">Cakupan Data</th>
                            <th class="py-2">Kelola Pengurus</th>
                            <th class="py-2">Kelola Warga</th>
                            <th class="py-2">Kas &amp; Keuangan</th>
                            <th class="py-2">Pelayanan Surat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong class="text-warning">Super Admin</strong></td>
                            <td>Platform Global</td>
                            <td class="text-center text-success"><i class="fas fa-circle-check"></i> Penuh</td>
                            <td class="text-center text-muted"><i class="fas fa-ban text-danger"></i> Agregat</td>
                            <td class="text-center text-muted"><i class="fas fa-eye text-primary"></i> Audit Saja</td>
                            <td class="text-center text-muted"><i class="fas fa-ban text-danger"></i> Tidak</td>
                        </tr>
                        <tr>
                            <td><strong class="text-success">Admin RW</strong></td>
                            <td>Tingkat RW 018</td>
                            <td class="text-center text-muted"><i class="fas fa-ban text-danger"></i> Tidak</td>
                            <td class="text-center text-success"><i class="fas fa-circle-check"></i> Verifikasi RW</td>
                            <td class="text-center text-success"><i class="fas fa-circle-check"></i> Kas RW</td>
                            <td class="text-center text-success"><i class="fas fa-circle-check"></i> Pengantar RW</td>
                        </tr>
                        <tr>
                            <td><strong class="text-primary">Admin RT</strong></td>
                            <td>RT Terdaftar</td>
                            <td class="text-center text-muted"><i class="fas fa-ban text-danger"></i> Tidak</td>
                            <td class="text-center text-success"><i class="fas fa-circle-check"></i> Verifikasi RT</td>
                            <td class="text-center text-success"><i class="fas fa-circle-check"></i> Kas RT</td>
                            <td class="text-center text-success"><i class="fas fa-circle-check"></i> Pengantar RT</td>
                        </tr>
                        <tr>
                            <td><strong class="text-secondary">Warga</strong></td>
                            <td>Akun Mandiri</td>
                            <td class="text-center text-muted"><i class="fas fa-ban text-danger"></i> Tidak</td>
                            <td class="text-center text-muted"><i class="fas fa-ban text-danger"></i> Tidak</td>
                            <td class="text-center text-primary"><i class="fas fa-credit-card"></i> Bayar Iuran</td>
                            <td class="text-center text-primary"><i class="fas fa-file-arrow-up"></i> Ajukan Surat</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-2.5 rounded-3 border mt-3 small" style="background: var(--nx-bg); border-color: var(--nx-border) !important; color: var(--nx-muted); font-size: 0.76rem;">
                <i class="fas fa-lock text-success me-1"></i>
                <strong>Jaminan Perlindungan IDOR:</strong> Backend memvalidasi otorisasi kepemilikan data pada setiap endpoint query sehingga pengurus tidak dapat mengakses atau memanipulasi data di luar wilayah penugasannya.
            </div>
        </div>
    </div>
</div>

<!-- SECTION F: RIWAYAT PERUBAHAN AKSES (AUDIT TRAIL) -->
<div class="sa-card mb-4">
    <div class="sa-card-header d-flex align-items-center justify-content-between">
        <div>
            <h4 class="sa-card-title fs-6">
                <i class="fas fa-clock-rotate-left text-primary"></i>
                <span>Riwayat Perubahan Akun &amp; Akses Pengurus (Audit Trail)</span>
            </h4>
            <p class="m-0 small" style="color: var(--nx-muted);">Catatan audit otomatis untuk setiap pembuatan akun, mutasi penugasan, pembaruan status, dan reset sandi pengurus</p>
        </div>
        <a href="{{ route('super_admin.audit.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 8px;">
            <i class="fas fa-external-link-alt me-1"></i>Semua Audit
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="color: var(--nx-text);">
            <thead style="background: var(--nx-bg); border-bottom: 2px solid var(--nx-border);">
                <tr>
                    <th class="ps-3 py-2.5 text-uppercase font-monospace small" style="font-size: 0.72rem; color: var(--nx-muted);">Waktu</th>
                    <th class="py-2.5 text-uppercase font-monospace small" style="font-size: 0.72rem; color: var(--nx-muted);">Aktor</th>
                    <th class="py-2.5 text-uppercase font-monospace small" style="font-size: 0.72rem; color: var(--nx-muted);">Jenis Tindakan</th>
                    <th class="py-2.5 text-uppercase font-monospace small" style="font-size: 0.72rem; color: var(--nx-muted);">Keterangan Aktivitas</th>
                    <th class="pe-3 py-2.5 text-end text-uppercase font-monospace small" style="font-size: 0.72rem; color: var(--nx-muted);">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($auditLogs as $log)
                <tr style="border-bottom: 1px solid var(--nx-border);">
                    <td class="ps-3 py-2.5 small font-monospace" style="color: var(--nx-muted); font-size: 0.76rem;">
                        {{ $log->created_at ? $log->created_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') . ' WIB' : '-' }}
                    </td>
                    <td class="py-2.5 small fw-semibold" style="color: var(--nx-text);">
                        {{ $log->user_name ?: 'Super Admin' }}
                    </td>
                    <td class="py-2.5">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace" style="font-size: 0.70rem;">
                            {{ $log->action_type ?: $log->action }}
                        </span>
                    </td>
                    <td class="py-2.5 small" style="color: var(--nx-text);">
                        {{ $log->description }}
                    </td>
                    <td class="pe-3 py-2.5 text-end">
                        <span class="badge bg-success-subtle text-success">
                            <i class="fas fa-check-circle me-1"></i>Tercatat
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 small" style="color: var(--nx-muted);">
                        <i class="fas fa-history fa-2x mb-2 d-block opacity-50"></i>
                        Belum ada riwayat perubahan akun atau hak akses pengurus.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL TAMBAH AKUN PENGURUS BARU -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalTambahPengurus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; background: var(--nx-card-bg); color: var(--nx-text);">
            <div class="modal-header px-4 py-3" style="background: linear-gradient(135deg, #1E4633 0%, #285943 100%);">
                <h5 class="modal-title fs-6 fw-bold text-white">
                    <i class="fas fa-user-plus me-1 text-warning"></i> Tambah Akun Pengurus Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('super_admin.accounts.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 text-start">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Lengkap Pengurus <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Nama lengkap pengurus" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Alamat Email Resmi <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="nama@email.com" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Peran Otorisasi <span class="text-danger">*</span></label>
                            <select name="role" id="tambahRoleSelect" class="form-select" onchange="toggleTambahRtSelect(this.value)" required>
                                <option value="admin_rt">Admin RT</option>
                                <option value="admin_rw">Admin RW 018</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="tambahRtContainer">
                            <label class="form-label small fw-bold">Penugasan RT <span class="text-danger">*</span></label>
                            <select name="rt_number" class="form-select">
                                @foreach($availableRts as $art)
                                    <option value="{{ $art }}">RT {{ $art }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nomor Kontak / WhatsApp</label>
                        <input type="text" name="phone" class="form-control" placeholder="08xxxxxxxxxx">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Kata Sandi Awal <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter" minlength="8" required>
                        <small class="text-muted" style="font-size: 0.74rem;">Pengurus dapat memperbarui kata sandi mandiri setelah aktivasi.</small>
                    </div>
                </div>
                <div class="modal-footer px-4 py-3 border-top" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold">Buat Akun Pengurus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODALS PER PENGURUS (DETAIL, EDIT, REASSIGN, RESET PASSWORD, STATUS) -->
<!-- ========================================================================= -->
@foreach($pengurusList as $u)
<!-- MODAL DETAIL PENGURUS -->
<div class="modal fade" id="modalDetailPengurus{{ $u->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; background: var(--nx-card-bg); color: var(--nx-text);">
            <div class="modal-header px-4 py-3 border-bottom" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                <h5 class="modal-title fs-6 fw-bold" style="color: var(--nx-text);">
                    <i class="fas fa-id-badge text-success me-1"></i> Profil Administrator: {{ $u->name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-start">
                <div class="d-flex align-items-center gap-3 p-3 rounded-3 border mb-3" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                    <img src="{{ $u->profile_photo_url }}" class="rounded-circle border" width="54" height="54" style="object-fit: cover;" alt="avatar">
                    <div>
                        <strong class="d-block fs-6" style="color: var(--nx-text);">{{ $u->name }}</strong>
                        <span class="badge {{ $u->isAdminRw() ? 'bg-warning-subtle text-dark' : 'bg-primary-subtle text-primary' }} mb-1">
                            {{ $u->role_label }}
                        </span>
                        <div class="small font-monospace" style="color: var(--nx-muted);">ID: #{{ $u->id }} &bull; RW {{ $u->rw_number ?: '018' }} &bull; {{ ($u->isAdminRw() || $u->rt_number === '000') ? 'Tingkat RW (Seluruh RW 018)' : 'RT ' . $u->rt_number }}</div>
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="p-2.5 rounded-3 border" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                            <span class="small d-block" style="color: var(--nx-muted);">Email Akun:</span>
                            <strong class="small text-truncate d-block" style="color: var(--nx-text);">{{ $u->email }}</strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2.5 rounded-3 border" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                            <span class="small d-block" style="color: var(--nx-muted);">Kontak Telepon:</span>
                            <strong class="small font-monospace" style="color: var(--nx-text);">{{ $u->phone ?: '-' }}</strong>
                        </div>
                    </div>
                </div>

                <div class="p-3 rounded-3 border mb-3" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                    <span class="small fw-bold d-block text-success mb-1">
                        <i class="fas fa-shield-halved me-1"></i> Cakupan Modul &amp; Batas Akses:
                    </span>
                    @if($u->isAdminRw())
                        <div class="small" style="color: var(--nx-muted);">
                            Memiliki wewenang koordinasi seluruh ekosistem komunitas RW 018 (22 modul aktif), pengawasan kas seluruh RT, verifikasi kependudukan tingkat RW, dan administrasi surat pengantar RW.
                        </div>
                    @else
                        <div class="small" style="color: var(--nx-muted);">
                            Memiliki wewenang tata kelola wilayah <strong>RT {{ $u->rt_number }}</strong>: pembukuan kas RT {{ $u->rt_number }}, verifikasi berkas warga RT {{ $u->rt_number }}, pengantar surat RT, dan patroli ronda lingkungan RT.
                        </div>
                    @endif
                </div>

                <div class="d-flex justify-content-between align-items-center small font-monospace" style="color: var(--nx-muted);">
                    <span>Dibuat: {{ $u->created_at ? $u->created_at->format('d M Y') : '-' }}</span>
                    <span>Status: <strong class="{{ $u->status === 'active' ? 'text-success' : 'text-danger' }}">{{ ucfirst($u->status) }}</strong></span>
                </div>
            </div>
            <div class="modal-footer px-4 py-3 border-top" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL EDIT PENGURUS -->
<div class="modal fade" id="modalEditPengurus{{ $u->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; background: var(--nx-card-bg); color: var(--nx-text);">
            <div class="modal-header px-4 py-3" style="background: linear-gradient(135deg, #1E4633 0%, #285943 100%);">
                <h5 class="modal-title fs-6 fw-bold text-white">
                    <i class="fas fa-user-pen me-1 text-warning"></i> Perbarui Data Pengurus: {{ $u->name }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('super_admin.accounts.update', $u->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4 text-start">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" value="{{ $u->name }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Alamat Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $u->email }}" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Peran Otorisasi</label>
                            <select name="role" class="form-select" required>
                                <option value="admin_rt" {{ $u->isAdminRt() ? 'selected' : '' }}>Admin RT</option>
                                <option value="admin_rw" {{ $u->isAdminRw() ? 'selected' : '' }}>Admin RW 018</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Penugasan RT</label>
                            <select name="rt_number" class="form-select">
                                <option value="000" {{ $u->rt_number === '000' ? 'selected' : '' }}>Tingkat RW (RW 018)</option>
                                @foreach($availableRts as $art)
                                    <option value="{{ $art }}" {{ $u->rt_number === $art ? 'selected' : '' }}>RT {{ $art }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nomor Telepon</label>
                            <input type="text" name="phone" class="form-control" value="{{ $u->phone }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Status Akun</label>
                            <select name="status" class="form-select" required>
                                <option value="active" {{ $u->status === 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="suspended" {{ $u->status === 'suspended' ? 'selected' : '' }}>Ditangguhkan</option>
                                <option value="inactive" {{ $u->status === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-4 py-3 border-top" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL REASSIGN PENGURUS -->
<div class="modal fade" id="modalReassignPengurus{{ $u->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; background: var(--nx-card-bg); color: var(--nx-text);">
            <div class="modal-header px-4 py-3" style="background: linear-gradient(135deg, #1E4633 0%, #285943 100%);">
                <h5 class="modal-title fs-6 fw-bold text-white">
                    <i class="fas fa-people-arrows me-1 text-warning"></i> Alihkan Penugasan: {{ $u->name }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('super_admin.accounts.reassign') }}" method="POST">
                @csrf
                <input type="hidden" name="user_id" value="{{ $u->id }}">
                <div class="modal-body p-4 text-start">
                    <p class="small text-muted mb-3">
                        Pindahkan penugasan wilayah operasional pengurus. Seluruh riwayat transaksi dan catatan administrasi terdahulu tetap terjaga utuh (Non-destructive).
                    </p>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Target Peran Wilayah <span class="text-danger">*</span></label>
                        <select name="target_role" class="form-select" required>
                            <option value="admin_rt" {{ $u->isAdminRt() ? 'selected' : '' }}>Admin RT (Wilayah Khusus)</option>
                            <option value="admin_rw" {{ $u->isAdminRw() ? 'selected' : '' }}>Admin RW 018 (Komunitas Penuh)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Target Penugasan RT</label>
                        <select name="target_rt" class="form-select">
                            @foreach($availableRts as $art)
                                <option value="{{ $art }}" {{ $u->rt_number === $art ? 'selected' : '' }}>RT {{ $art }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer px-4 py-3 border-top" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold">Terapkan Penugasan Baru</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL RESET PASSWORD PENGURUS -->
<div class="modal fade" id="modalResetPassword{{ $u->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; background: var(--nx-card-bg); color: var(--nx-text);">
            <div class="modal-header px-4 py-3 bg-info text-white" style="background: linear-gradient(135deg, #0284C7 0%, #0369A1 100%);">
                <h5 class="modal-title fs-6 fw-bold text-white">
                    <i class="fas fa-key me-1 text-warning"></i> Reset Kata Sandi Pengurus: {{ $u->name }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('super_admin.accounts.reset_password', $u->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4 text-start">
                    <p class="small text-muted mb-3">
                        Tetapkan kata sandi baru yang aman untuk akun pengurus <strong>{{ $u->name }}</strong> ({{ $u->email }}). Tindakan ini akan dicatat dalam audit trail platform.
                    </p>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Kata Sandi Baru <span class="text-danger">*</span></label>
                        <input type="password" name="new_password" class="form-control" placeholder="Minimal 8 karakter" minlength="8" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Konfirmasi Kata Sandi Baru <span class="text-danger">*</span></label>
                        <input type="password" name="new_password_confirmation" class="form-control" placeholder="Ulangi kata sandi baru" minlength="8" required>
                    </div>
                </div>
                <div class="modal-footer px-4 py-3 border-top" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info text-white px-4 fw-bold">Simpan Kata Sandi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL TOGGLE STATUS PENGURUS -->
<div class="modal fade" id="modalToggleStatus{{ $u->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; background: var(--nx-card-bg); color: var(--nx-text);">
            <div class="modal-header px-4 py-3 {{ $u->status === 'active' ? 'bg-danger text-white' : 'bg-success text-white' }}">
                <h5 class="modal-title fs-6 fw-bold text-white">
                    <i class="fas {{ $u->status === 'active' ? 'fa-triangle-exclamation' : 'fa-circle-check' }} me-1"></i>
                    Konfirmasi {{ $u->status === 'active' ? 'Penangguhan' : 'Aktivasi' }} Akun
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('super_admin.accounts.toggle_status', $u->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4 text-start">
                    <p class="mb-3" style="color: var(--nx-text);">
                        Apakah Anda yakin ingin <strong>{{ $u->status === 'active' ? 'menangguhkan (nonaktifkan)' : 'mengaktifkan kembali' }}</strong> akun pengurus <strong>{{ $u->name }}</strong> ({{ $u->email }})?
                    </p>

                    <div class="p-3 rounded-3 border mb-3" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                        <span class="d-block fw-bold small text-success mb-1">
                            <i class="fas fa-shield-halved me-1"></i> Jaminan Integritas Data (Non-Destructive):
                        </span>
                        <ul class="list-unstyled small m-0 d-flex flex-column gap-1" style="color: var(--nx-muted); font-size: 0.78rem;">
                            <li><i class="fas fa-check text-success me-1"></i> Riwayat kerja, persetujuan surat, dan pencatatan kas terdahulu tetap tersimpan utuh.</li>
                            <li><i class="fas fa-check text-success me-1"></i> Akun dapat diaktifkan kembali sewaktu-waktu oleh Super Admin.</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer px-4 py-3 border-top" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn {{ $u->status === 'active' ? 'btn-danger' : 'btn-success' }} px-4 fw-bold">
                        Ya, {{ $u->status === 'active' ? 'Tangguhkan Akun' : 'Aktifkan Akun' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<script>
function toggleTambahRtSelect(role) {
    const container = document.getElementById('tambahRtContainer');
    if (role === 'admin_rt') {
        container.style.display = 'block';
    } else {
        container.style.display = 'none';
    }
}
</script>

@endsection
