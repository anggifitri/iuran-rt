@if(Auth::check() && Auth::user()->isSuperAdmin() && Auth::user()->isRoleSwitched())
@php
    $ctx = Auth::user()->getRoleSwitchContext();
    $targetRole = $ctx['target_role'] ?? 'admin_rw';
    $targetRw = '018'; // Single-RW Operational
    $targetRt = $ctx['target_rt'] ?? '006';
    $mode = $ctx['mode'] ?? 'preview';
    $isReadOnly = ($mode === 'preview');
    $targetUserName = $ctx['target_user_name'] ?? null;

    $roleTitle = match($targetRole) {
        'admin_rw' => "Admin RW 018",
        'admin_rt' => "Admin RT {$targetRt} · RW 018",
        'warga' => "Warga RT {$targetRt} · RW 018",
        default => strtoupper($targetRole)
    };
@endphp
<style>
    .nx-role-switcher-banner {
        z-index: 1060;
        background: linear-gradient(90deg, #132E21 0%, #1E4633 35%, #5C4010 85%, #8C6514 100%);
        border-bottom: 2px solid #D4AF37;
    }
    .nx-role-switcher-banner .badge-gold,
    .nx-role-switcher-banner .btn-gold,
    .nx-role-switcher-banner .btn-exit-switcher {
        background: linear-gradient(135deg, #FFE082 0%, #FFB300 100%) !important;
        border: 1px solid #FFD54F !important;
        color: #0F172A !important;
    }
    .nx-role-switcher-banner .badge-gold *,
    .nx-role-switcher-banner .btn-gold *,
    .nx-role-switcher-banner .btn-exit-switcher * {
        color: #0F172A !important;
    }
    .nx-role-switcher-banner .badge-preview {
        background: #FDE047 !important;
        border: 1px solid #FACC15 !important;
        color: #0F172A !important;
    }
    .nx-role-switcher-banner .badge-preview * {
        color: #0F172A !important;
    }
    .nx-role-switcher-banner .badge-simulasi {
        background: #FEF08A !important;
        border: 1px solid #FACC15 !important;
        color: #0F172A !important;
    }
    .nx-role-switcher-banner .badge-simulasi * {
        color: #0F172A !important;
    }
    .nx-role-switcher-banner .badge-operational {
        background: #059669 !important;
        border: 1px solid #047857 !important;
        color: #FFFFFF !important;
    }
    .nx-role-switcher-banner .badge-context {
        background: rgba(15, 23, 42, 0.75) !important;
        border: 1px solid rgba(255, 255, 255, 0.25) !important;
        color: #FFFFFF !important;
    }
</style>

<div class="nx-role-switcher-banner py-1.5 px-3 text-white position-sticky top-0 shadow">
    <div class="container-fluid d-flex flex-wrap flex-lg-nowrap align-items-center justify-content-between gap-2 px-1">
        
        <!-- SISI KIRI: INDIKATOR SWITCHER + KONTEKS + BADGE SIMULASI + MODE + TOOLTIP INFO -->
        <div class="d-flex align-items-center flex-nowrap gap-2 overflow-hidden">
            <!-- Crown Badge (Super Admin Switcher) -->
            <span class="badge badge-gold d-inline-flex align-items-center gap-1.5 px-2.5 py-1 fw-bold shadow-sm flex-shrink-0" style="font-size: 0.76rem; border-radius: 8px;">
                <i class="fas fa-crown"></i>
                <span>Super Admin Switcher</span>
            </span>

            <!-- Konteks Aktif Pill -->
            <div class="d-flex align-items-center gap-1.5 flex-nowrap flex-shrink-0">
                <span class="text-white-50 small d-none d-md-inline" style="font-size: 0.74rem;">Konteks:</span>
                <span class="badge badge-context fw-bold px-2 py-1 rounded shadow-sm flex-shrink-0" style="font-size: 0.78rem;">
                    {{ $roleTitle }}
                </span>

                @if(!empty($targetUserName))
                    <span class="badge badge-simulasi px-2 py-1 fw-bold text-truncate flex-shrink-0" style="font-size: 0.74rem; max-width: 200px;" title="Akun Sasaran: {{ $targetUserName }} ({{ $roleTitle }})">
                        <i class="fas fa-user-check me-1"></i>{{ $targetUserName }}
                    </span>
                @endif
            </div>

            <!-- Mode Badge (PREVIEW vs OPERATIONAL) -->
            @if($isReadOnly)
                <span class="badge badge-preview px-2.5 py-1 fw-bold shadow-sm flex-shrink-0" style="font-size: 0.74rem;" title="Mode Preview aktif: Seluruh mutasi data diproteksi backend">
                    <i class="fas fa-eye me-1"></i> PREVIEW MODE
                </span>
            @else
                <span class="badge badge-operational px-2.5 py-1 fw-bold shadow-sm flex-shrink-0" style="font-size: 0.74rem;" title="Mode Operasional aktif: Dapat melakukan tindakan sesuai peran">
                    <i class="fas fa-bolt me-1"></i> OPERATIONAL MODE
                </span>
            @endif

            <!-- Info Tooltip Ringkas (Aktor audit tetap Super Admin) -->
            <span class="badge d-none d-xl-inline-flex align-items-center gap-1 py-1 px-2 text-white-50 border border-white-50 flex-shrink-0" style="background: rgba(0,0,0,0.25); font-size: 0.70rem; cursor: help;" title="Aktor audit tetap Super Admin · Role asli di database tidak berubah">
                <i class="fas fa-shield-alt text-warning"></i>
                <span>Audit: Super Admin</span>
            </span>
        </div>

        <!-- SISI KANAN: 3 TOMBOL RINGKAS SEJAJAR (DESKTOP) -->
        <div class="d-flex align-items-center gap-1.5 flex-nowrap flex-shrink-0 ms-auto">
            <!-- 1. Tombol Toggle Mode -->
            <form action="{{ route('super_admin.role_switch.toggle_mode') }}" method="POST" class="d-inline m-0">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-light d-inline-flex align-items-center gap-1 py-1 px-2.5" style="font-size: 0.74rem; border-radius: 8px;" title="Ganti antara Mode Preview & Operasional">
                    <i class="fas {{ $isReadOnly ? 'fa-toggle-off text-white-50' : 'fa-toggle-on text-warning' }}"></i>
                    <span class="d-none d-sm-inline">{{ $isReadOnly ? 'Ke Mode Operasional' : 'Ke Mode Preview' }}</span>
                    <span class="d-sm-none">{{ $isReadOnly ? 'Operasional' : 'Preview' }}</span>
                </button>
            </form>

            <!-- 2. Tombol Ganti Peran (Modal) -->
            <button type="button" class="btn btn-sm btn-light d-inline-flex align-items-center gap-1 py-1 px-2.5 fw-semibold text-dark shadow-sm" style="font-size: 0.74rem; border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#modalGlobalRoleSwitcher" title="Buka modal pemilihan peran lain">
                <i class="fas fa-arrow-right-arrow-left text-success"></i>
                <span>Ganti Peran</span>
            </button>

            <!-- 3. Tombol Kembali ke Super Admin (Kuning Tajam Kontras) -->
            <form action="{{ route('super_admin.role_switch.exit') }}" method="POST" class="d-inline m-0">
                @csrf
                <button type="submit" class="btn btn-sm btn-gold btn-exit-switcher d-inline-flex align-items-center gap-1 py-1 px-3 fw-bold shadow-sm" style="font-size: 0.76rem; border-radius: 8px; color: #0F172A !important;" title="Keluar dari simulasi dan kembali ke Dashboard Super Admin">
                    <i class="fas fa-crown" style="color: #0F172A !important;"></i>
                    <span class="d-none d-sm-inline" style="color: #0F172A !important;">Kembali ke Super Admin</span>
                    <span class="d-sm-none" style="color: #0F172A !important;">Super Admin</span>
                </button>
            </form>
        </div>

    </div>
</div>
@endif
