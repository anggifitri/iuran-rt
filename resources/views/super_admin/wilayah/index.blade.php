@extends('layouts.super_admin')

@section('title', 'Manajemen Wilayah RW 018 — NexaNest Super Admin')
@section('page_title', 'Manajemen Wilayah RW')

@section('content')
<!-- TOP HEADER & STATUS BAR -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h2 class="fw-bold m-0 fs-5" style="color: var(--nx-text);">Wilayah Operasional Utama: RW 018</h2>
        <p class="m-0 small" style="color: var(--nx-muted);">Pusat pengelolaan identitas RW 018, administrasi RT 006 s/d RT 010, penugasan pengurus, dan kesiapan arsitektur multi-tenant</p>
    </div>

    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fw-bold d-inline-flex align-items-center gap-2">
            <i class="fas fa-circle-check"></i>
            <span>Single-RW Operasional Aktif</span>
        </span>
        <button type="button" class="btn btn-sm btn-success fw-semibold d-inline-flex align-items-center gap-2 px-3 py-2 shadow-sm" style="border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#modalTambahRt">
            <i class="fas fa-plus-circle"></i>
            <span>Tambah RT Baru</span>
        </button>
    </div>
</div>

<!-- PANEL A: REGION OVERVIEW (KPI CARDS) -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="sa-kpi-card p-3 rounded-3 border h-100" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="small fw-semibold" style="color: var(--nx-muted);">Wilayah Terdaftar</span>
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: rgba(30, 70, 51, 0.12); color: #1E4633;">
                    <i class="fas fa-map-location-dot"></i>
                </div>
            </div>
            <h3 class="fs-4 fw-bold m-0" style="color: var(--nx-text);">RW 018</h3>
            <div class="small mt-1 text-truncate" style="color: var(--nx-muted);">{{ $rw->kelurahan }}, {{ $rw->kota }}</div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="sa-kpi-card p-3 rounded-3 border h-100" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="small fw-semibold" style="color: var(--nx-muted);">Rukun Tetangga (RT)</span>
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: rgba(13, 148, 136, 0.12); color: #0D9488;">
                    <i class="fas fa-sitemap"></i>
                </div>
            </div>
            <h3 class="fs-4 fw-bold m-0" style="color: var(--nx-text);">{{ $totalRtAll }} RT</h3>
            <div class="small mt-1 d-flex align-items-center gap-1 text-success">
                <i class="fas fa-circle-check" style="font-size: 0.72rem;"></i>
                <span>{{ $totalRt }} RT Berstatus Aktif</span>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="sa-kpi-card p-3 rounded-3 border h-100" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="small fw-semibold" style="color: var(--nx-muted);">Total Warga RW 018</span>
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: rgba(59, 130, 246, 0.12); color: #2563EB;">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <h3 class="fs-4 fw-bold m-0" style="color: var(--nx-text);">{{ number_format($totalWarga) }} <span class="fs-6 fw-normal" style="color: var(--nx-muted);">Jiwa</span></h3>
            <div class="small mt-1" style="color: var(--nx-muted);">Tersebar di RT 006 s/d RT 010</div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="sa-kpi-card p-3 rounded-3 border h-100" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="small fw-semibold" style="color: var(--nx-muted);">Admin RT Bertugas</span>
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: rgba(217, 119, 6, 0.12); color: #D97706;">
                    <i class="fas fa-user-shield"></i>
                </div>
            </div>
            <h3 class="fs-4 fw-bold m-0" style="color: var(--nx-text);">{{ $totalAdminRt }} <span class="fs-6 fw-normal" style="color: var(--nx-muted);">Petugas</span></h3>
            <div class="small mt-1 d-flex align-items-center gap-1 text-success">
                <i class="fas fa-shield-halved" style="font-size: 0.72rem;"></i>
                <span>Semua RT Memiliki Penanggung Jawab</span>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- PANEL B: IDENTITAS & PROFIL RW 018 -->
    <div class="col-lg-7">
        <div class="sa-card h-100">
            <div class="sa-card-header d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="sa-card-title">
                        <i class="fas fa-landmark text-success"></i>
                        <span>Identitas Wilayah RW 018</span>
                    </h4>
                    <p class="m-0 small" style="color: var(--nx-muted);">Profil legalitas, alamat resmi, dan kontak sekretariat komunitas</p>
                </div>

                <button type="button" class="btn btn-sm btn-outline-success fw-semibold d-inline-flex align-items-center gap-1" style="border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#modalEditRw18">
                    <i class="fas fa-pen"></i>
                    <span>Edit Profil RW</span>
                </button>
            </div>

            <div class="d-flex flex-column gap-3">
                <div class="p-3 rounded-3 border" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h5 class="fw-bold m-0 fs-6" style="color: var(--nx-text);">{{ $rw->nama_wilayah }}</h5>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1">
                            <i class="fas fa-check-circle me-1"></i>Status: {{ ucfirst($rw->status) }}
                        </span>
                    </div>
                    <div class="small mb-2" style="color: var(--nx-muted);">
                        <i class="fas fa-building text-success me-1"></i>
                        Sekretariat: {{ $rw->alamat_sekretariat ?: 'Balai Warga RW 018, Jl. Boulevard Utama No. 01' }}
                    </div>
                    <div class="small" style="color: var(--nx-muted);">
                        <i class="fas fa-location-dot text-danger me-1"></i>
                        {{ $rw->kelurahan }}, {{ $rw->kecamatan }}, {{ $rw->kota }}, {{ $rw->provinsi }} ({{ $rw->kode_pos ?? '16411' }})
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3 border h-100" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                            <span class="d-block small fw-medium" style="font-size: 0.74rem; color: var(--nx-muted);">Ketua RW Bertugas:</span>
                            <strong class="d-block mt-0.5" style="color: var(--nx-text);">{{ $rw->ketua_rw_nama ?? 'Dirga Pradipta' }}</strong>
                            <small class="d-block font-monospace mt-1" style="color: var(--nx-muted);">
                                <i class="fas fa-phone me-1 text-success"></i>{{ $rw->ketua_rw_phone ?? '081234567890' }}
                            </small>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3 border h-100" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                            <span class="d-block small fw-medium" style="font-size: 0.74rem; color: var(--nx-muted);">Email Resmi Komunitas:</span>
                            <strong class="d-block mt-0.5 text-truncate" style="color: var(--nx-text);">{{ $rw->email_resmi ?? 'sekretariat@rw018.nexanest.id' }}</strong>
                            <small class="d-block mt-1" style="color: var(--nx-muted);">
                                <i class="fas fa-envelope me-1 text-primary"></i>Kanal Surat & Notifikasi Resmi
                            </small>
                        </div>
                    </div>
                </div>

                @if(!empty($rw->catatan))
                    <div class="p-2.5 rounded-3 border small" style="background: var(--nx-bg); border-color: var(--nx-border) !important; color: var(--nx-muted);">
                        <i class="fas fa-circle-info text-primary me-1"></i> {{ str_replace('8 RT terhubung', "{$totalRt} RT terhubung (RT 006–010)", $rw->catatan) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- PANEL E: KONFIGURASI TENANT RW 018 & MULTI-RW READINESS -->
    <div class="col-lg-5">
        <div class="sa-card h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="sa-card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="sa-card-title fs-6">
                            <i class="fas fa-cubes text-warning"></i>
                            <span>Konfigurasi Tenant & Multi-RW</span>
                        </h4>
                        <p class="m-0" style="font-size: 0.74rem; color: var(--nx-muted);">Kesiapan arsitektur isolasi multi-tenant platform</p>
                    </div>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle" style="font-size: 0.70rem;">Multi-Tenant Ready</span>
                </div>

                <div class="p-3 rounded-3 border mb-3" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="small fw-semibold" style="color: var(--nx-muted);">Tenant Identifier:</span>
                        <span class="badge font-monospace bg-primary-subtle text-primary border border-primary-subtle">{{ $rw->tenant_id ?? 'TENANT-RW018-MEKARJAYA' }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="small fw-semibold" style="color: var(--nx-muted);">Status Lingkungan:</span>
                        @php
                            $currentEnv = config('app.env', 'production');
                            $envBadgeClass = match($currentEnv) {
                                'production' => 'bg-success-subtle text-success border border-success-subtle sa-badge-env-production',
                                'staging' => 'bg-warning-subtle text-warning border border-warning-subtle sa-badge-env-staging',
                                default => 'bg-info-subtle text-info border border-info-subtle sa-badge-env-local',
                            };
                            $envLabel = match($currentEnv) {
                                'production' => 'Production / Live',
                                'staging' => 'Staging / Uji Coba',
                                default => 'Local / Development',
                            };
                        @endphp
                        <span class="badge {{ $envBadgeClass }}">{{ $envLabel }}</span>
                    </div>
                </div>

                <div class="mb-3">
                    <span class="d-block small fw-bold mb-2" style="font-size: 0.78rem; color: var(--nx-text);">
                        <i class="fas fa-layer-group text-success me-1"></i> Cakupan 22 Modul Aktif RW 018:
                    </span>
                    <div class="d-flex flex-wrap gap-1.5" style="max-height: 140px; overflow-y: auto;">
                        @foreach($tenantModules as $mod)
                            <span class="badge border" style="background: var(--nx-bg); border-color: var(--nx-border) !important; color: var(--nx-text); font-size: 0.68rem; font-weight: normal; padding: 4px 7px;">
                                <i class="fas {{ $mod['icon'] }} text-success me-1"></i>{{ $mod['name'] }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <ul class="list-unstyled d-flex flex-column gap-2 small m-0" style="font-size: 0.78rem; color: var(--nx-muted);">
                    <li class="d-flex align-items-start gap-2">
                        <i class="fas fa-check-circle text-success mt-0.5"></i>
                        <span><strong>Tenant Scoping:</strong> Seluruh modul dan data terisolasi otomatis menggunakan query scope <code>rw_number = '018'</code>.</span>
                    </li>
                    <li class="d-flex align-items-start gap-2">
                        <i class="fas fa-check-circle text-success mt-0.5"></i>
                        <span><strong>Zero Cross-Tenant Leakage:</strong> Hak akses Admin RW & Admin RT dibatasi strictly pada wilayah penugasan.</span>
                    </li>
                </ul>
            </div>

            <div class="p-2.5 rounded-3 border mt-3 small d-flex align-items-center justify-content-between" style="background: var(--nx-bg); border-color: var(--nx-border) !important; color: var(--nx-muted);">
                <span>Registrasi RW Eksternal:</span>
                <span class="badge sa-badge-tenant-readonly bg-secondary-subtle text-secondary border border-secondary-subtle">Readonly (Fokus Tunggal RW 018)</span>
            </div>
        </div>
    </div>
</div>

<!-- PANEL C: MANAJEMEN RT DALAM RW 018 -->
<div class="sa-card mb-4">
    <div class="sa-card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div>
            <h4 class="sa-card-title">
                <i class="fas fa-network-wired text-success"></i>
                <span>Manajemen Rukun Tetangga (RT) dalam RW 018</span>
            </h4>
            <p class="m-0 small" style="color: var(--nx-muted);">Daftar wilayah RT yang aktif beroperasi di bawah naungan RW 018 Mekarjaya</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light text-dark border px-2.5 py-1.5" style="border-color: var(--nx-border) !important;">
                Total: <strong>{{ count($rts) }} RT</strong>
            </span>
            <button type="button" class="btn btn-sm btn-success fw-semibold d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modalTambahRt">
                <i class="fas fa-plus"></i>
                <span>Tambah RT</span>
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="color: var(--nx-text);">
            <thead style="background: var(--nx-bg); border-bottom: 2px solid var(--nx-border);">
                <tr>
                    <th class="ps-3 py-3 text-uppercase font-monospace small" style="font-size: 0.72rem; color: var(--nx-muted);">Nomor RT</th>
                    <th class="py-3 text-uppercase font-monospace small" style="font-size: 0.72rem; color: var(--nx-muted);">Ketua RT & Kontak</th>
                    <th class="py-3 text-uppercase font-monospace small" style="font-size: 0.72rem; color: var(--nx-muted);">Administrator RT Bertugas</th>
                    <th class="py-3 text-center text-uppercase font-monospace small" style="font-size: 0.72rem; color: var(--nx-muted);">Jumlah Warga</th>
                    <th class="py-3 text-center text-uppercase font-monospace small" style="font-size: 0.72rem; color: var(--nx-muted);">Status</th>
                    <th class="pe-3 py-3 text-end text-uppercase font-monospace small" style="font-size: 0.72rem; color: var(--nx-muted);">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rts as $rt)
                <tr style="border-bottom: 1px solid var(--nx-border);">
                    <td class="ps-3 py-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace px-2.5 py-1.5 fw-bold" style="font-size: 0.85rem;">
                                RT {{ $rt->rt_number }}
                            </span>
                            @if(!empty($rt->catatan))
                                <span class="badge bg-light text-muted border" style="font-size: 0.68rem;" title="{{ $rt->catatan }}">
                                    <i class="fas fa-info-circle"></i>
                                </span>
                            @endif
                        </div>
                    </td>
                    <td class="py-3">
                        <strong class="d-block" style="color: var(--nx-text);">{{ $rt->nama_ketua_rt ?: 'Belum ditentukan' }}</strong>
                        <small class="font-monospace" style="color: var(--nx-muted);">
                            <i class="fas fa-phone text-muted me-1" style="font-size: 0.70rem;"></i>{{ $rt->phone_ketua_rt ?: '-' }}
                        </small>
                    </td>
                    <td class="py-3">
                        @if($rt->adminUser)
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $rt->adminUser->profile_photo_url }}" class="rounded-circle" width="28" height="28" alt="avatar">
                                <div>
                                    <strong class="d-block small" style="color: var(--nx-text);">{{ $rt->adminUser->name }}</strong>
                                    <small style="color: var(--nx-muted); font-size: 0.72rem;">{{ $rt->adminUser->email }}</small>
                                </div>
                            </div>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                <i class="fas fa-user-xmark me-1"></i>Belum Ditugaskan
                            </span>
                        @endif
                    </td>
                    <td class="py-3 text-center">
                        <span class="badge px-3 py-1.5 fw-bold" style="background: rgba(59, 130, 246, 0.12); color: #2563EB; font-size: 0.82rem;">
                            {{ $rt->warga_count }} Jiwa
                        </span>
                    </td>
                    <td class="py-3 text-center">
                        @if($rt->status === 'active')
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1">
                                <i class="fas fa-check-circle me-1"></i>Aktif
                            </span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2.5 py-1">
                                <i class="fas fa-ban me-1"></i>Nonaktif
                            </span>
                        @endif
                    </td>
                    <td class="pe-3 py-3 text-end">
                        <div class="d-inline-flex align-items-center gap-1">
                            <button type="button" class="btn btn-sm btn-outline-secondary px-2 py-1" style="border-radius: 6px;" title="Lihat Detail RT" data-bs-toggle="modal" data-bs-target="#modalDetailRt{{ $rt->id }}">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary px-2 py-1" style="border-radius: 6px;" title="Edit Data RT" data-bs-toggle="modal" data-bs-target="#modalEditRt{{ $rt->id }}">
                                <i class="fas fa-pen"></i>
                            </button>
                            <button type="button" class="btn btn-sm {{ $rt->status === 'active' ? 'btn-outline-danger' : 'btn-outline-success' }} px-2 py-1" style="border-radius: 6px;" title="{{ $rt->status === 'active' ? 'Nonaktifkan RT (Non-destructive)' : 'Aktifkan RT' }}" data-bs-toggle="modal" data-bs-target="#modalToggleRt{{ $rt->id }}">
                                <i class="fas {{ $rt->status === 'active' ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4" style="color: var(--nx-muted);">
                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                        Tidak ada data RT ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- PANEL D: MANAJEMEN PENUGASAN ADMINISTRATOR WILAYAH -->
<div class="row g-4 mb-4">
    <!-- ADMINISTRATOR RW 018 -->
    <div class="col-lg-5">
        <div class="sa-card h-100">
            <div class="sa-card-header d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="sa-card-title fs-6">
                        <i class="fas fa-crown text-warning"></i>
                        <span>Administrator RW 018</span>
                    </h4>
                    <p class="m-0 small" style="color: var(--nx-muted);">Penanggung jawab utama operasional komunitas</p>
                </div>
                <button type="button" class="btn btn-sm btn-outline-warning fw-semibold d-inline-flex align-items-center gap-1" style="border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#modalAssignAdminRw">
                    <i class="fas fa-user-pen"></i>
                    <span>Alihkan Penugasan</span>
                </button>
            </div>

            <div class="p-3 rounded-3 border" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                @if($adminRw)
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="{{ $adminRw->profile_photo_url }}" class="rounded-circle border border-warning" width="48" height="48" alt="avatar">
                        <div>
                            <strong class="d-block fs-6" style="color: var(--nx-text);">{{ $adminRw->name }}</strong>
                            <small class="badge bg-warning-subtle text-dark border border-warning-subtle mt-0.5">Admin RW 018 · Pengurus Komunitas</small>
                        </div>
                    </div>
                    <div class="d-flex flex-column gap-1.5 small" style="color: var(--nx-muted);">
                        <div><i class="fas fa-envelope text-primary me-2"></i>{{ $adminRw->email }}</div>
                        <div><i class="fas fa-phone text-success me-2"></i>{{ $adminRw->phone ?: 'Belum ada nomor telepon' }}</div>
                        <div><i class="fas fa-circle-check text-success me-2"></i>Status Akun: <strong>Aktif</strong></div>
                    </div>
                @else
                    <div class="text-center py-3" style="color: var(--nx-muted);">
                        <i class="fas fa-user-slash fa-2x mb-2 d-block"></i>
                        Belum ada Administrator RW yang ditugaskan.
                    </div>
                @endif
            </div>

            <div class="p-2.5 rounded-3 border mt-3 small" style="background: var(--nx-bg); border-color: var(--nx-border) !important; color: var(--nx-muted); font-size: 0.78rem;">
                <i class="fas fa-shield-halved text-success me-1"></i>
                Pengalihan penugasan tidak akan menghapus riwayat audit atau transaksi yang dicatat sebelumnya.
            </div>
        </div>
    </div>

    <!-- MATRIKS PENUGASAN ADMINISTRATOR RT -->
    <div class="col-lg-7">
        <div class="sa-card h-100">
            <div class="sa-card-header d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="sa-card-title fs-6">
                        <i class="fas fa-users-gear text-success"></i>
                        <span>Matriks Penugasan Administrator RT</span>
                    </h4>
                    <p class="m-0 small" style="color: var(--nx-muted);">Penetapan akun penanggung jawab masing-masing RT</p>
                </div>
                <button type="button" class="btn btn-sm btn-outline-success fw-semibold d-inline-flex align-items-center gap-1" style="border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#modalAssignAdminRt">
                    <i class="fas fa-user-plus"></i>
                    <span>Tugaskan Admin RT</span>
                </button>
            </div>

            <div class="d-flex flex-column gap-2 sa-matriks-rt-list" style="max-height: 420px; overflow-y: auto;">
                @foreach($rts as $rt)
                <div class="p-2.5 rounded-3 border d-flex align-items-center justify-content-between gap-3" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace fw-bold px-2 py-1">
                            RT {{ $rt->rt_number }}
                        </span>
                        <div>
                            <strong class="d-block small" style="color: var(--nx-text);">
                                {{ $rt->adminUser ? $rt->adminUser->name : ($rt->nama_ketua_rt ?: 'Belum Ada Admin') }}
                            </strong>
                            <small style="color: var(--nx-muted); font-size: 0.72rem;">
                                {{ $rt->adminUser ? $rt->adminUser->email : 'Akun belum ditautkan' }}
                            </small>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <span class="badge {{ $rt->adminUser ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }}" style="font-size: 0.70rem;">
                            {{ $rt->adminUser ? 'Terhubung' : 'Pending' }}
                        </span>
                        <button type="button" class="btn btn-xs btn-outline-secondary px-2 py-1" style="border-radius: 6px; font-size: 0.72rem;" data-bs-toggle="modal" data-bs-target="#modalEditRt{{ $rt->id }}">
                            Ubah
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- PANEL F: RIWAYAT PERUBAHAN WILAYAH (AUDIT TRAIL) -->
<div class="sa-card mb-4">
    <div class="sa-card-header d-flex align-items-center justify-content-between">
        <div>
            <h4 class="sa-card-title fs-6">
                <i class="fas fa-clock-rotate-left text-primary"></i>
                <span>Riwayat Perubahan & Audit Wilayah RW 018</span>
            </h4>
            <p class="m-0 small" style="color: var(--nx-muted);">Log aktivitas perubahan profil, penambahan RT, penonaktifan, dan penugasan admin</p>
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
                    <th class="py-2.5 text-uppercase font-monospace small" style="font-size: 0.72rem; color: var(--nx-muted);">Aksi</th>
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
                        Belum ada riwayat perubahan profil wilayah, struktur RT, atau penugasan admin.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL EDIT PROFIL RW 018 -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalEditRw18" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; background: var(--nx-card-bg); color: var(--nx-text);">
            <div class="modal-header px-4 py-3" style="background: linear-gradient(135deg, #1E4633 0%, #285943 100%);">
                <h5 class="modal-title fs-6 fw-bold text-white">
                    <i class="fas fa-pen-to-square me-1 text-warning"></i> Perbarui Identitas Wilayah RW 018
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('super_admin.wilayah.update', $rw->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4 text-start">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Nomor RW</label>
                            <input type="text" class="form-control font-monospace" style="background: var(--nx-bg); color: var(--nx-muted);" value="018" disabled>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Nama Komunitas Wilayah</label>
                            <input type="text" name="nama_wilayah" class="form-control" value="{{ $rw->nama_wilayah }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Kelurahan</label>
                            <input type="text" name="kelurahan" class="form-control" value="{{ $rw->kelurahan }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Kecamatan</label>
                            <input type="text" name="kecamatan" class="form-control" value="{{ $rw->kecamatan }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Kota / Kabupaten</label>
                            <input type="text" name="kota" class="form-control" value="{{ $rw->kota }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Provinsi</label>
                            <input type="text" name="provinsi" class="form-control" value="{{ $rw->provinsi }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Kode Pos</label>
                            <input type="text" name="kode_pos" class="form-control" value="{{ $rw->kode_pos }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Nama Ketua RW</label>
                            <input type="text" name="ketua_rw_nama" class="form-control" value="{{ $rw->ketua_rw_nama }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">No. HP Ketua RW / Kontak</label>
                            <input type="text" name="ketua_rw_phone" class="form-control" value="{{ $rw->ketua_rw_phone }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Email Resmi Sekretariat</label>
                            <input type="email" name="email_resmi" class="form-control" value="{{ $rw->email_resmi }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Status Operasional</label>
                            <select name="status" class="form-select">
                                <option value="active" {{ $rw->status === 'active' ? 'selected' : '' }}>Aktif (Operasional Penuh)</option>
                                <option value="suspended" {{ $rw->status === 'suspended' ? 'selected' : '' }}>Ditangguhkan Sementara</option>
                                <option value="inactive" {{ $rw->status === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Alamat Balai Warga / Sekretariat</label>
                            <textarea name="alamat_sekretariat" class="form-control" rows="2">{{ $rw->alamat_sekretariat }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Catatan Lingkungan</label>
                            <textarea name="catatan" class="form-control" rows="2">{{ $rw->catatan }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-4 py-3 border-top" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold">Simpan Perubahan RW 018</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL TAMBAH RT BARU -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalTambahRt" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; background: var(--nx-card-bg); color: var(--nx-text);">
            <div class="modal-header px-4 py-3" style="background: linear-gradient(135deg, #1E4633 0%, #285943 100%);">
                <h5 class="modal-title fs-6 fw-bold text-white">
                    <i class="fas fa-plus-circle me-1 text-warning"></i> Tambah Rukun Tetangga (RT) Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('super_admin.wilayah.rt.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 text-start">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nomor RT (3 Digit) <span class="text-danger">*</span></label>
                        <input type="text" name="rt_number" class="form-control font-monospace" placeholder="Contoh: 011" pattern="^[0-9]{3}$" maxlength="3" required>
                        <small class="text-muted" style="font-size: 0.74rem;">Wajib format 3 digit numerik (contoh: 011, 012). Nomor RT harus unik di RW 018.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Ketua RT</label>
                        <input type="text" name="nama_ketua_rt" class="form-control" placeholder="Nama lengkap ketua RT">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">No. HP Ketua RT</label>
                        <input type="text" name="phone_ketua_rt" class="form-control" placeholder="08xxxxxxxxxx">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Tugaskan Akun Administrator RT</label>
                        <select name="admin_user_id" class="form-select">
                            <option value="">-- Pilih Akun Admin (Opsional) --</option>
                            @foreach($candidateUsers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                        <small class="text-muted" style="font-size: 0.74rem;">User terpilih akan diberikan role Admin RT pada wilayah ini.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Catatan Wilayah RT</label>
                        <textarea name="catatan" class="form-control" rows="2" placeholder="Contoh: Blok K-L, Pos Keamanan Utara"></textarea>
                    </div>
                </div>
                <div class="modal-footer px-4 py-3 border-top" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold">Daftarkan RT Baru</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODALS DETAIL, EDIT & TOGGLE STATUS PER RT -->
<!-- ========================================================================= -->
@foreach($rts as $rt)
<!-- MODAL DETAIL RT -->
<div class="modal fade" id="modalDetailRt{{ $rt->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; background: var(--nx-card-bg); color: var(--nx-text);">
            <div class="modal-header px-4 py-3 border-bottom" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                <h5 class="modal-title fs-6 fw-bold" style="color: var(--nx-text);">
                    <i class="fas fa-sitemap text-success me-1"></i> Rincian Wilayah RT {{ $rt->rt_number }} — RW 018
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-start">
                <div class="d-flex align-items-center justify-content-between mb-3 p-3 rounded-3 border" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                    <div>
                        <span class="small d-block" style="color: var(--nx-muted);">Wilayah Rukun Tetangga</span>
                        <h4 class="fw-bold m-0" style="color: var(--nx-text);">RT {{ $rt->rt_number }}</h4>
                    </div>
                    <span class="badge {{ $rt->status === 'active' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} px-3 py-1.5 fs-6">
                        Status: {{ ucfirst($rt->status) }}
                    </span>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="p-2.5 rounded-3 border" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                            <span class="small d-block" style="color: var(--nx-muted);">Populasi Terdaftar:</span>
                            <strong class="fs-6 text-primary">{{ $rt->warga_count }} Jiwa</strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2.5 rounded-3 border" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                            <span class="small d-block" style="color: var(--nx-muted);">Induk Wilayah:</span>
                            <strong class="fs-6" style="color: var(--nx-text);">RW 018 Mekarjaya</strong>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column gap-2 small">
                    <div class="p-2.5 rounded-3 border" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                        <span class="text-muted d-block" style="font-size: 0.74rem;">Ketua RT:</span>
                        <strong style="color: var(--nx-text);">{{ $rt->nama_ketua_rt ?: 'Belum diisi' }}</strong>
                        <span class="d-block font-monospace text-muted mt-0.5">{{ $rt->phone_ketua_rt ?: '-' }}</span>
                    </div>

                    <div class="p-2.5 rounded-3 border" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                        <span class="text-muted d-block" style="font-size: 0.74rem;">Akun Admin RT:</span>
                        @if($rt->adminUser)
                            <strong style="color: var(--nx-text);">{{ $rt->adminUser->name }}</strong>
                            <span class="d-block text-muted mt-0.5">{{ $rt->adminUser->email }}</span>
                        @else
                            <span class="text-muted">Belum ada akun user yang ditugaskan sebagai admin</span>
                        @endif
                    </div>

                    @if(!empty($rt->catatan))
                    <div class="p-2.5 rounded-3 border" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                        <span class="text-muted d-block" style="font-size: 0.74rem;">Catatan Lingkungan:</span>
                        <span style="color: var(--nx-text);">{{ $rt->catatan }}</span>
                    </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer px-4 py-3 border-top" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL EDIT RT -->
<div class="modal fade" id="modalEditRt{{ $rt->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; background: var(--nx-card-bg); color: var(--nx-text);">
            <div class="modal-header px-4 py-3" style="background: linear-gradient(135deg, #1E4633 0%, #285943 100%);">
                <h5 class="modal-title fs-6 fw-bold text-white">
                    <i class="fas fa-pen-to-square me-1 text-warning"></i> Edit Data RT {{ $rt->rt_number }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('super_admin.wilayah.rt.update', $rt->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4 text-start">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nomor RT</label>
                        <input type="text" class="form-control font-monospace" style="background: var(--nx-bg); color: var(--nx-muted);" value="{{ $rt->rt_number }}" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Ketua RT</label>
                        <input type="text" name="nama_ketua_rt" class="form-control" value="{{ $rt->nama_ketua_rt }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">No. HP Ketua RT</label>
                        <input type="text" name="phone_ketua_rt" class="form-control" value="{{ $rt->phone_ketua_rt }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Administrator RT Bertugas</label>
                        <select name="admin_user_id" class="form-select">
                            <option value="">-- Belum Ditugaskan --</option>
                            @foreach($candidateUsers as $user)
                                <option value="{{ $user->id }}" {{ $rt->admin_user_id == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Catatan Lingkungan</label>
                        <textarea name="catatan" class="form-control" rows="2">{{ $rt->catatan }}</textarea>
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

<!-- MODAL TOGGLE STATUS RT (NON-DESTRUCTIVE) -->
<div class="modal fade" id="modalToggleRt{{ $rt->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; background: var(--nx-card-bg); color: var(--nx-text);">
            <div class="modal-header px-4 py-3 {{ $rt->status === 'active' ? 'bg-danger text-white' : 'bg-success text-white' }}">
                <h5 class="modal-title fs-6 fw-bold text-white">
                    <i class="fas {{ $rt->status === 'active' ? 'fa-triangle-exclamation' : 'fa-circle-check' }} me-1"></i>
                    Konfirmasi {{ $rt->status === 'active' ? 'Penonaktifan' : 'Aktivasi' }} RT {{ $rt->rt_number }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('super_admin.wilayah.rt.toggle_status', $rt->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4 text-start">
                    <p class="mb-3" style="color: var(--nx-text);">
                        Apakah Anda yakin ingin <strong>{{ $rt->status === 'active' ? 'menonaktifkan' : 'mengaktifkan kembali' }} RT {{ $rt->rt_number }}</strong>?
                    </p>

                    <div class="p-3 rounded-3 border mb-3" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                        <span class="d-block fw-bold small text-success mb-1">
                            <i class="fas fa-shield-halved me-1"></i> Jaminan Keamanan Data (Non-Destructive):
                        </span>
                        <ul class="list-unstyled small m-0 d-flex flex-column gap-1" style="color: var(--nx-muted); font-size: 0.78rem;">
                            <li><i class="fas fa-check text-success me-1"></i> Data kependudukan (<strong>{{ $rt->warga_count }} jiwa</strong>) tetap tersimpan aman di database.</li>
                            <li><i class="fas fa-check text-success me-1"></i> Seluruh riwayat transaksi kas RT, iuran warga, dan pengaduan tidak akan terhapus.</li>
                            <li><i class="fas fa-check text-success me-1"></i> Wilayah dapat diaktifkan kembali kapan saja oleh Super Admin.</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer px-4 py-3 border-top" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn {{ $rt->status === 'active' ? 'btn-danger' : 'btn-success' }} px-4 fw-bold">
                        Ya, {{ $rt->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }} RT {{ $rt->rt_number }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- ========================================================================= -->
<!-- MODAL ASSIGN ADMINISTRATOR RW 018 -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalAssignAdminRw" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; background: var(--nx-card-bg); color: var(--nx-text);">
            <div class="modal-header px-4 py-3" style="background: linear-gradient(135deg, #1E4633 0%, #285943 100%);">
                <h5 class="modal-title fs-6 fw-bold text-white">
                    <i class="fas fa-user-shield me-1 text-warning"></i> Alihkan Penugasan Administrator RW 018
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('super_admin.wilayah.assign_admin') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="rw">
                <div class="modal-body p-4 text-start">
                    <p class="small text-muted mb-3">
                        Pilih akun pengguna yang akan ditunjuk sebagai Administrator RW 018. Penugasan ini akan memberikan kewenangan penuh manajemen komunitas RW 018 tanpa mengubah data administratif historis.
                    </p>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Pilih Pengguna Bertugas <span class="text-danger">*</span></label>
                        <select name="user_id" class="form-select" required>
                            @foreach($candidateAdmins as $candidate)
                                <option value="{{ $candidate->id }}" {{ ($adminRw && $adminRw->id == $candidate->id) ? 'selected' : '' }}>
                                    {{ $candidate->name }} ({{ $candidate->email }}) — {{ ucfirst(str_replace('_', ' ', $candidate->role)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="p-2.5 rounded-3 border small" style="background: var(--nx-bg); border-color: var(--nx-border) !important; color: var(--nx-muted); font-size: 0.78rem;">
                        <i class="fas fa-info-circle text-primary me-1"></i>
                        Aktivitas pergantian admin RW akan langsung dicatat pada Audit & Aktivitas Platform.
                    </div>
                </div>
                <div class="modal-footer px-4 py-3 border-top" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold">Simpan Penugasan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL ASSIGN ADMINISTRATOR RT -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalAssignAdminRt" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; background: var(--nx-card-bg); color: var(--nx-text);">
            <div class="modal-header px-4 py-3" style="background: linear-gradient(135deg, #1E4633 0%, #285943 100%);">
                <h5 class="modal-title fs-6 fw-bold text-white">
                    <i class="fas fa-user-plus me-1 text-warning"></i> Tugaskan Administrator RT
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('super_admin.wilayah.assign_admin') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="rt">
                <div class="modal-body p-4 text-start">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Pilih Wilayah RT <span class="text-danger">*</span></label>
                        <select name="rt_id" class="form-select" required>
                            @foreach($rts as $rt)
                                <option value="{{ $rt->id }}">RT {{ $rt->rt_number }} (Saat ini: {{ $rt->adminUser ? $rt->adminUser->name : 'Belum Ada' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Pilih Pengguna Sebagai Admin RT <span class="text-danger">*</span></label>
                        <select name="user_id" class="form-select" required>
                            @foreach($candidateUsers as $candidate)
                                <option value="{{ $candidate->id }}">
                                    {{ $candidate->name }} ({{ $candidate->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="p-2.5 rounded-3 border small" style="background: var(--nx-bg); border-color: var(--nx-border) !important; color: var(--nx-muted); font-size: 0.78rem;">
                        <i class="fas fa-shield-halved text-success me-1"></i>
                        Pengguna terpilih akan diberikan role <code>admin</code> dengan scope RT yang dipilih.
                    </div>
                </div>
                <div class="modal-footer px-4 py-3 border-top" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold">Tetapkan Admin RT</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
