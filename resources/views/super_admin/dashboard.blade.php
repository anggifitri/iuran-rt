@extends('layouts.super_admin')

@section('title', 'Platform Control Center — NexaNest Super Admin')
@section('page_title', 'Platform Control Center')

@section('content')
<!-- HERO WELCOME BANNER (CHAMPAGNE GOLD + FOREST GREEN GRADIENT) -->
<div class="sa-card p-3 p-sm-4 border-0 position-relative overflow-hidden shadow-sm mb-4 sa-hero-banner" style="background: linear-gradient(135deg, #132E21 0%, #1E4633 55%, #594312 95%); border-radius: 22px; color: #FFFFFF;">
    <div class="position-absolute end-0 top-50 translate-middle-y me-4 opacity-25 d-none d-lg-block pointer-events-none">
        <i class="fas fa-crown text-warning" style="font-size: 11rem;"></i>
    </div>

    <div class="position-relative" style="z-index: 2; max-width: 820px;">
        <div class="d-flex flex-wrap align-items-center gap-1.5 gap-sm-2 mb-2.5 mb-sm-3 sa-hero-badges">
            <span class="d-inline-flex align-items-center gap-1.5 px-2.5 py-1 rounded-pill sa-badge-hq" style="background: rgba(255, 215, 0, 0.22); border: 1.5px solid #FFD54F; font-size: 0.72rem; font-weight: 800; color: #FFE082;">
                <i class="fas fa-crown text-warning"></i> PLATFORM OWNER HQ
            </span>
            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size: 0.70rem;">
                <i class="fas fa-circle-check me-1"></i> Operasional: RW 018
            </span>
            <span class="badge sa-badge-multi-rw" style="background: rgba(255, 255, 255, 0.15); font-size: 0.70rem; color: #E2E8F0;">
                <i class="fas fa-cubes me-1"></i> Multi-RW Ready
            </span>
            <span class="badge sa-badge-modules" style="background: rgba(255, 215, 0, 0.18); border: 1px solid rgba(255, 215, 0, 0.4); font-size: 0.70rem; color: #FFE082;">
                <i class="fas fa-cube me-1 text-warning"></i> {{ $activeModules }}/{{ $totalModules }} Modul Fitur Aktif
            </span>
        </div>

        <h2 class="fw-bold mb-2 text-white sa-hero-title" style="font-size: 1.65rem;">
            NexaNest Platform Control Center — Wilayah RW 018
        </h2>
        <p class="text-white-50 mb-3 mb-sm-4 sa-hero-desc" style="font-size: 0.88rem; line-height: 1.55;">
            Super Admin memiliki kewenangan tertinggi atas seluruh tata kelola ekosistem NexaNest RW 018 (RT 006–010). Fondasi arsitektur telah dipersiapkan mendukung ekspansi multi-RW di masa depan dengan pemisahan tenant dan perlindungan data yang ketat.
        </p>

        <div class="d-flex flex-wrap align-items-center gap-2 gap-sm-3 sa-hero-actions">
            <button type="button" class="btn btn-warning px-3 py-2 fw-bold text-dark d-inline-flex align-items-center gap-1.5 shadow" style="background: linear-gradient(135deg, #FFE082 0%, #FFB300 100%); border: 1px solid #FFD54F; border-radius: 12px; font-size: 0.82rem;" data-bs-toggle="modal" data-bs-target="#modalGlobalRoleSwitcher">
                <i class="fas fa-crown"></i>
                <span>Buka Role Switcher</span>
            </button>

            <!-- Quick Switch Directly to Admin RW 018 -->
            <form action="{{ route('super_admin.role_switch.switch') }}" method="POST" class="d-inline m-0">
                @csrf
                <input type="hidden" name="target_role" value="admin_rw">
                <input type="hidden" name="target_rw" value="018">
                <input type="hidden" name="target_rt" value="000">
                <input type="hidden" name="mode" value="preview">
                <button type="submit" class="btn btn-outline-light px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1.5" style="border-radius: 12px; font-size: 0.82rem;" title="Simulasi langsung sebagai Admin RW 018">
                    <i class="fas fa-building-columns"></i>
                    <span>Simulasi RW 018</span>
                </button>
            </form>

            <a href="{{ route('super_admin.modules.index') }}" class="btn btn-outline-light px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1.5" style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.28); border-radius: 12px; font-size: 0.82rem; color: #FFFFFF;" title="Konfigurasi Modul Fitur RW 018">
                <i class="fas fa-sliders"></i>
                <span>Konfigurasi 22 Modul &rarr;</span>
            </a>
        </div>
    </div>
</div>

<!-- 4 OPERATIONAL KPI CARDS (RW 018) -->
<div class="row g-2 g-sm-3 mb-4">
    <!-- KPI 1: Wilayah Operasional -->
    <div class="col-6 col-xl-3">
        <div class="sa-kpi-card h-100">
            <div class="d-flex align-items-center justify-content-between">
                <span class="sa-kpi-label">Wilayah Operasional</span>
                <div class="sa-kpi-icon" style="background: #E8F5E9; color: #2E7D32;">
                    <i class="fas fa-location-dot"></i>
                </div>
            </div>
            <div>
                <h3 class="sa-kpi-val fs-4">RW 018</h3>
                <small class="text-success fw-bold d-flex align-items-center gap-1 mt-1 sa-kpi-sub" style="font-size: 0.76rem;">
                    <i class="fas fa-house-chimney"></i> 5 RT (006 - 010)
                </small>
            </div>
        </div>
    </div>

    <!-- KPI 2: Total Pengguna Terdaftar -->
    <div class="col-6 col-xl-3">
        <div class="sa-kpi-card h-100">
            <div class="d-flex align-items-center justify-content-between">
                <span class="sa-kpi-label">Akun Pengguna</span>
                <div class="sa-kpi-icon" style="background: #E3F2FD; color: #1565C0;">
                    <i class="fas fa-users-gear"></i>
                </div>
            </div>
            <div>
                <h3 class="sa-kpi-val">{{ $totalUsers }} <span class="fs-6 fw-semibold text-muted">Akun</span></h3>
                <small class="text-primary fw-bold d-flex align-items-center gap-1 mt-1 sa-kpi-sub" style="font-size: 0.76rem;">
                    <i class="fas fa-user-shield"></i> {{ $totalAdminRw }} RW &bull; {{ $totalAdminRt }} RT
                </small>
            </div>
        </div>
    </div>

    <!-- KPI 3: Populasi Warga -->
    <div class="col-6 col-xl-3">
        <div class="sa-kpi-card h-100">
            <div class="d-flex align-items-center justify-content-between">
                <span class="sa-kpi-label">Populasi Warga</span>
                <div class="sa-kpi-icon" style="background: #FFF3E0; color: #E65100;">
                    <i class="fas fa-people-roof"></i>
                </div>
            </div>
            <div>
                <h3 class="sa-kpi-val">{{ $totalWargaData }} <span class="fs-6 fw-semibold text-muted">Jiwa</span></h3>
                <small class="text-secondary fw-semibold d-flex align-items-center gap-1 mt-1 sa-kpi-sub" style="font-size: 0.76rem;">
                    <i class="fas fa-folder-tree"></i> {{ $totalKk }} KK
                </small>
            </div>
        </div>
    </div>

    <!-- KPI 4: Kas Terkelola -->
    <div class="col-6 col-xl-3">
        <div class="sa-kpi-card h-100">
            <div class="d-flex align-items-center justify-content-between">
                <span class="sa-kpi-label">Kas Terkelola</span>
                <div class="sa-kpi-icon" style="background: #EDE7F6; color: #512DA8;">
                    <i class="fas fa-sack-dollar"></i>
                </div>
            </div>
            <div>
                <h3 class="sa-kpi-val fs-5 text-break">Rp {{ number_format($totalKasGlobal, 0, ',', '.') }}</h3>
                <small class="text-success fw-bold d-flex align-items-center gap-1 mt-1 sa-kpi-sub" style="font-size: 0.76rem;">
                    <i class="fas fa-scale-balanced me-1"></i> Saldo gabungan RT
                </small>
            </div>
        </div>
    </div>
</div>

<!-- SECTION 1: PROFIL WILAYAH RW 018 & STATUS RT -->
<div class="row g-4 mb-4">
    <!-- Profil RW 018 -->
    <div class="col-lg-5">
        <div class="sa-card h-100">
            <div class="sa-card-header">
                <div>
                    <h4 class="sa-card-title">
                        <i class="fas fa-landmark text-success"></i>
                        <span>Profil Wilayah RW 018</span>
                    </h4>
                    <p class="text-muted m-0 small">Pusat tata kelola lingkungan operasional saat ini</p>
                </div>
                <a href="{{ route('super_admin.wilayah.index') }}" class="btn btn-sm btn-outline-success fw-semibold" style="border-radius: 8px;">
                    Kelola Wilayah &rarr;
                </a>
            </div>

            <div class="d-flex flex-column gap-3">
                <div class="p-3 rounded-3 border" style="background: #FAFCFA;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <strong class="text-dark">{{ $rw018->nama_wilayah }}</strong>
                        <span class="badge bg-success-subtle text-success border border-success-subtle">Aktif Beroperasi</span>
                    </div>
                    <div class="small text-muted mb-2">
                        <i class="fas fa-location-dot me-1 text-danger"></i>
                        {{ $rw018->alamat_sekretariat ?: 'Balai Warga RW 018, Jl. Boulevard Utama No. 01' }}
                    </div>
                    <div class="small text-muted">
                        {{ $rw018->kelurahan }}, {{ $rw018->kecamatan }}, {{ $rw018->kota }}, {{ $rw018->provinsi }} ({{ $rw018->kode_pos }})
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col-6">
                        <div class="p-2.5 rounded-3 border bg-light">
                            <span class="text-muted d-block small" style="font-size: 0.72rem;">Ketua RW:</span>
                            <strong class="text-dark small">{{ $rw018->ketua_rw_nama ?: 'Dirga Pradipta' }}</strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2.5 rounded-3 border bg-light">
                            <span class="text-muted d-block small" style="font-size: 0.72rem;">Kontak Sekretariat:</span>
                            <strong class="text-dark small font-monospace">{{ $rw018->ketua_rw_phone ?: '081234567890' }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Multi-RW Ready Architecture Note -->
                <div class="p-3 rounded-3 border d-flex align-items-start gap-2.5" style="background: #FFFBEB; border-color: #FDE68A !important;">
                    <i class="fas fa-layer-group text-warning fs-5 mt-0.5"></i>
                    <div>
                        <strong class="text-dark small d-block mb-0.5">Arsitektur Multi-RW Ready:</strong>
                        <p class="m-0 text-muted" style="font-size: 0.74rem; line-height: 1.4;">
                            Data administratif diisolasi per tenant. Penambahan RW baru di masa depan tidak memerlukan perombakan kode maupun migrasi ulang database.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sebaran 5 RT di Wilayah Operasional RW 018 -->
    <div class="col-lg-7">
        <div class="sa-card h-100">
            <div class="sa-card-header">
                <div>
                    <h4 class="sa-card-title">
                        <i class="fas fa-network-wired text-primary"></i>
                        <span>Pemantauan Rukun Tetangga (RT 006 - RT 010)</span>
                    </h4>
                    <p class="text-muted m-0 small">Penugasan Admin RT dan jumlah warga terdata di wilayah operasional RW 018</p>
                </div>
                <a href="{{ route('super_admin.accounts.index', ['role' => 'admin_rt']) }}" class="btn btn-sm btn-outline-primary fw-semibold" style="border-radius: 8px;">
                    Kelola Admin RT
                </a>
            </div>

            <div class="table-responsive">
                <table class="sa-table">
                    <thead>
                        <tr>
                            <th>Rukun Tetangga</th>
                            <th>Admin RT Bertugas</th>
                            <th>Populasi Warga</th>
                            <th class="text-end">Aksi Simulasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rtList as $rt)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge text-white fw-bold px-2 py-1" style="background: #285943; font-size: 0.75rem;">
                                            RT {{ $rt['rt_number'] }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <strong class="text-dark small d-block">{{ $rt['admin_name'] }}</strong>
                                    <small class="text-muted">{{ $rt['admin_email'] ?: 'Belum ada akun admin' }}</small>
                                </td>
                                <td>
                                    <span class="small fw-bold text-dark">{{ $rt['warga_count'] }} Jiwa</span>
                                    <small class="text-muted d-block">({{ $rt['kk_count'] }} KK)</small>
                                </td>
                                <td class="text-end">
                                    <form action="{{ route('super_admin.role_switch.switch') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="target_role" value="admin_rt">
                                        <input type="hidden" name="target_rw" value="018">
                                        <input type="hidden" name="target_rt" value="{{ $rt['rt_number'] }}">
                                        <input type="hidden" name="mode" value="preview">
                                        <button type="submit" class="btn btn-sm btn-light border py-1 px-2 d-inline-flex align-items-center gap-1" style="font-size: 0.74rem; border-radius: 8px;" title="Simulasikan sebagai Admin RT {{ $rt['rt_number'] }} (Preview)">
                                            <i class="fas fa-eye text-info"></i>
                                            <span class="d-none d-sm-inline">Preview</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold" style="background: rgba(40, 89, 67, 0.05); border-top: 2px solid var(--nx-border);">
                            <td class="small">Total: 5 RT</td>
                            <td class="small text-muted">RT 006 s/d RT 010</td>
                            <td class="small text-success">{{ $totalWargaData }} Jiwa ({{ $totalKk }} KK)</td>
                            <td class="text-end small text-muted">5 RT Aktif</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- SECTION 2: AUDIT LOGS RW 018 -->
<div class="sa-card">
    <div class="sa-card-header">
        <div>
            <h4 class="sa-card-title">
                <i class="fas fa-shield-halved text-danger"></i>
                <span>Audit &amp; Log Keamanan Terkini RW 018</span>
            </h4>
            <p class="text-muted m-0 small">Aktivitas administratif &amp; keamanan append-only lingkungan RW 018</p>
        </div>
        <a href="{{ route('super_admin.audit.index') }}" class="btn btn-sm btn-outline-secondary fw-semibold" style="border-radius: 8px;">
            Semua Log &rarr;
        </a>
    </div>

    <div class="table-responsive">
        <table class="sa-table">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Aktor Pengguna</th>
                    <th>Peran</th>
                    <th>Modul</th>
                    <th>Aksi &amp; Keterangan</th>
                    <th>IP Address</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentAuditLogs as $log)
                    <tr>
                        <td class="text-nowrap small text-muted">
                            {{ $log->created_at ? $log->created_at->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td>
                            <strong class="text-dark small">{{ $log->user_name ?: 'Sistem' }}</strong>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border" style="font-size: 0.70rem;">
                                {{ strtoupper($log->role ?: 'GUEST') }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.70rem;">
                                {{ $log->module ?: 'Platform' }}
                            </span>
                        </td>
                        <td>
                            <div class="small fw-semibold text-dark">{{ $log->event }}</div>
                            <small class="text-muted text-truncate d-inline-block" style="max-width: 340px;">
                                {{ $log->description ?: '-' }}
                            </small>
                        </td>
                        <td class="small text-muted font-monospace">
                            {{ $log->ip_address ?: '127.0.0.1' }}
                        </td>
                        <td>
                            <span class="badge {{ $log->status === 'success' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} px-2 py-0.5" style="font-size: 0.70rem;">
                                {{ strtoupper($log->status ?: 'OK') }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Belum ada riwayat aktivitas yang tercatat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
