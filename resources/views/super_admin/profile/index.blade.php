@extends('layouts.super_admin')

@section('title', 'Profil Super Admin — NexaNest Control Center')
@section('page_title', 'Profil Super Admin')

@section('content')
<!-- PROFILE HERO HEADER -->
<div class="sa-card p-4 rounded-3 border mb-4" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="position-relative">
                <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" 
                     class="rounded-circle shadow-sm" 
                     style="width: 76px; height: 76px; object-fit: cover; border: 3px solid var(--nx-gold);">
                <span class="position-absolute bottom-0 end-0 rounded-circle d-flex align-items-center justify-content-center" 
                      style="width: 24px; height: 24px; background: #D97706; color: #FFFFFF; font-size: 0.70rem; border: 2px solid var(--nx-card-bg);" 
                      title="Super Admin HQ">
                    <i class="fas fa-crown"></i>
                </span>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h3 class="fw-bold m-0 fs-5" style="color: var(--nx-text);">{{ $user->display_name }}</h3>
                    <span class="badge font-monospace px-2.5 py-1 sa-badge-superadmin-crown" style="background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%); color: #0F172A !important; border: 1px solid #FCD34D; font-size: 0.68rem; border-radius: 6px; font-weight: 700;">
                        <i class="fas fa-crown me-1" style="color: #0F172A !important;"></i> SUPER ADMIN
                    </span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace px-2.5 py-1" style="font-size: 0.68rem; border-radius: 6px;">
                        <i class="fas fa-circle-check me-1"></i> AKTIF &amp; TERVERIFIKASI
                    </span>
                </div>
                <div class="d-flex align-items-center gap-3 mt-1 flex-wrap" style="font-size: 0.8rem; color: var(--nx-muted);">
                    <span><i class="fas fa-envelope me-1"></i>{{ $user->email }}</span>
                    <span><i class="fas fa-phone me-1"></i>{{ $user->phone ?: 'Belum diatur' }}</span>
                    <span class="font-monospace"><i class="fas fa-fingerprint me-1"></i>#SA-{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
                @if($user->bio)
                    <p class="m-0 mt-2 small text-truncate" style="color: var(--nx-muted); max-width: 600px;">
                        {{ $user->bio }}
                    </p>
                @endif
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="badge font-monospace px-3 py-2 border" style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border) !important; font-size: 0.74rem; border-radius: 8px;">
                <i class="fas fa-clock me-1 text-primary"></i> Sesi Login: {{ $lastLoginFormatted }}
            </span>
        </div>
    </div>
</div>

<!-- 6 INTERNAL TABS NAVIGATION -->
<div class="mb-4">
    <ul class="nav nav-pills p-1.5 rounded-3 border d-flex flex-wrap gap-1" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
        <li class="nav-item flex-grow-1 flex-md-grow-0">
            <a class="nav-link text-center px-3 py-2 fw-bold small d-flex align-items-center justify-content-center gap-2 {{ $activeTab === 'personal' ? 'active' : '' }}" 
               href="{{ route('super_admin.profile.index', ['tab' => 'personal']) }}"
               style="{{ $activeTab === 'personal' ? 'background: var(--nx-forest); color: #FFFFFF; border-radius: 8px;' : 'color: var(--nx-muted); border-radius: 8px;' }}">
                <i class="fas fa-id-card"></i>
                <span>A. Informasi Pribadi</span>
            </a>
        </li>
        <li class="nav-item flex-grow-1 flex-md-grow-0">
            <a class="nav-link text-center px-3 py-2 fw-bold small d-flex align-items-center justify-content-center gap-2 {{ $activeTab === 'account' ? 'active' : '' }}" 
               href="{{ route('super_admin.profile.index', ['tab' => 'account']) }}"
               style="{{ $activeTab === 'account' ? 'background: var(--nx-forest); color: #FFFFFF; border-radius: 8px;' : 'color: var(--nx-muted); border-radius: 8px;' }}">
                <i class="fas fa-user-lock"></i>
                <span>B. Informasi Akun</span>
            </a>
        </li>
        <li class="nav-item flex-grow-1 flex-md-grow-0">
            <a class="nav-link text-center px-3 py-2 fw-bold small d-flex align-items-center justify-content-center gap-2 {{ $activeTab === 'security' ? 'active' : '' }}" 
               href="{{ route('super_admin.profile.index', ['tab' => 'security']) }}"
               style="{{ $activeTab === 'security' ? 'background: var(--nx-forest); color: #FFFFFF; border-radius: 8px;' : 'color: var(--nx-muted); border-radius: 8px;' }}">
                <i class="fas fa-shield-halved"></i>
                <span>C. Keamanan &amp; Sesi</span>
                <span class="badge rounded-pill {{ $activeTab === 'security' ? 'bg-white text-dark' : 'bg-light text-muted border' }}" style="font-size: 0.68rem;">{{ $sessions->count() }} Sesi</span>
            </a>
        </li>
        <li class="nav-item flex-grow-1 flex-md-grow-0">
            <a class="nav-link text-center px-3 py-2 fw-bold small d-flex align-items-center justify-content-center gap-2 {{ $activeTab === 'notifications' ? 'active' : '' }}" 
               href="{{ route('super_admin.profile.index', ['tab' => 'notifications']) }}"
               style="{{ $activeTab === 'notifications' ? 'background: var(--nx-forest); color: #FFFFFF; border-radius: 8px;' : 'color: var(--nx-muted); border-radius: 8px;' }}">
                <i class="fas fa-bell"></i>
                <span>D. Preferensi Notifikasi</span>
            </a>
        </li>
        <li class="nav-item flex-grow-1 flex-md-grow-0">
            <a class="nav-link text-center px-3 py-2 fw-bold small d-flex align-items-center justify-content-center gap-2 {{ $activeTab === 'activity' ? 'active' : '' }}" 
               href="{{ route('super_admin.profile.index', ['tab' => 'activity']) }}"
               style="{{ $activeTab === 'activity' ? 'background: var(--nx-forest); color: #FFFFFF; border-radius: 8px;' : 'color: var(--nx-muted); border-radius: 8px;' }}">
                <i class="fas fa-clock-rotate-left"></i>
                <span>E. Aktivitas Saya</span>
            </a>
        </li>
        <li class="nav-item flex-grow-1 flex-md-grow-0">
            <a class="nav-link text-center px-3 py-2 fw-bold small d-flex align-items-center justify-content-center gap-2 {{ $activeTab === 'preferences' ? 'active' : '' }}" 
               href="{{ route('super_admin.profile.index', ['tab' => 'preferences']) }}"
               style="{{ $activeTab === 'preferences' ? 'background: var(--nx-forest); color: #FFFFFF; border-radius: 8px;' : 'color: var(--nx-muted); border-radius: 8px;' }}">
                <i class="fas fa-palette"></i>
                <span>F. Preferensi Tampilan</span>
            </a>
        </li>
    </ul>
</div>

<!-- ========================================================================= -->
<!-- TAB A: INFORMASI PRIBADI -->
<!-- ========================================================================= -->
@if($activeTab === 'personal')
<div class="row g-4">
    <!-- FORM INFORMASI PRIBADI -->
    <div class="col-lg-8">
        <div class="sa-card p-4 rounded-3 border" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
            <div class="border-bottom pb-3 mb-4" style="border-color: var(--nx-border) !important;">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(40, 89, 67, 0.12); color: var(--nx-forest);">
                        <i class="fas fa-user-edit"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold m-0 fs-6" style="color: var(--nx-text);">Identitas Pribadi Super Admin</h5>
                        <small style="color: var(--nx-muted);">Kelola nama tampilan, nomor kontak, dan biodata profil</small>
                    </div>
                </div>
            </div>

            <form action="{{ route('super_admin.profile.update_personal') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold" style="color: var(--nx-text);">Nama Lengkap (Sesuai KTP)</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required
                               style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold" style="color: var(--nx-text);">Nama Tampilan (Panggilan / Alias)</label>
                        <input type="text" name="display_name" class="form-control" value="{{ old('display_name', $user->display_name) }}"
                               placeholder="Nama yang muncul di header &amp; log"
                               style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold" style="color: var(--nx-text);">Nomor Telepon / WhatsApp</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}"
                               placeholder="Contoh: 081234567890"
                               style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold" style="color: var(--nx-text);">Unggah Foto Profil Baru</label>
                        <input type="file" name="profile_photo" class="form-control sa-file-input" accept="image/png,image/jpeg,image/webp"
                               style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                        <small class="text-muted" style="font-size: 0.72rem;">Format: JPG, PNG, WebP. Maksimal: 3 MB.</small>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold" style="color: var(--nx-text);">Bio Singkat</label>
                        <textarea name="bio" rows="3" class="form-control" placeholder="Deskripsi singkat mengenai peran atau catatan operasional Anda..."
                                  style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">{{ old('bio', $user->bio) }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end pt-3 mt-4 border-top" style="border-color: var(--nx-border) !important;">
                    <button type="submit" class="btn btn-success px-4 py-2 fw-bold d-inline-flex align-items-center gap-2 shadow-sm" style="border-radius: 8px;">
                        <i class="fas fa-check-circle"></i>
                        <span>Simpan Informasi Pribadi</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- PREVIEW FOTO & IDENTITAS HQ -->
    <div class="col-lg-4">
        <div class="sa-card p-4 rounded-3 border text-center mb-4" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
            <div class="mb-3 position-relative d-inline-block">
                <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" 
                     class="rounded-circle shadow-sm" 
                     style="width: 110px; height: 110px; object-fit: cover; border: 3px solid var(--nx-gold);">
            </div>
            <h5 class="fw-bold m-0 fs-6" style="color: var(--nx-text);">{{ $user->display_name }}</h5>
            <span class="badge font-monospace px-2.5 py-1 mt-1 sa-badge-superadmin-crown" style="background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%); color: #0F172A !important; border: 1px solid #FCD34D; font-size: 0.68rem; border-radius: 6px; font-weight: 700;">
                <i class="fas fa-crown me-1" style="color: #0F172A !important;"></i> SUPER ADMIN · PLATFORM OWNER
            </span>
            <div class="mt-3 pt-3 border-top text-start d-flex flex-column gap-2" style="border-color: var(--nx-border) !important; font-size: 0.78rem;">
                <div class="d-flex align-items-center justify-content-between">
                    <span style="color: var(--nx-muted);">ID Akun:</span>
                    <strong class="font-monospace" style="color: var(--nx-text);">#SA-{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</strong>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <span style="color: var(--nx-muted);">Email Login:</span>
                    <strong class="font-monospace text-truncate" style="max-width: 170px; color: var(--nx-text);">{{ $user->email }}</strong>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <span style="color: var(--nx-muted);">Role Otoritas:</span>
                    <span class="badge bg-success-subtle text-success">HQ Root</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- ========================================================================= -->
<!-- TAB B: INFORMASI AKUN -->
<!-- ========================================================================= -->
@if($activeTab === 'account')
<div class="row g-4">
    <!-- DETAIL AKUN UTAMA -->
    <div class="col-lg-6">
        <div class="sa-card p-4 rounded-3 border h-100" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
            <div class="border-bottom pb-3 mb-4" style="border-color: var(--nx-border) !important;">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(14, 165, 233, 0.12); color: #0284C7;">
                        <i class="fas fa-id-badge"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold m-0 fs-6" style="color: var(--nx-text);">Detail Informasi Akun Platform</h5>
                        <small style="color: var(--nx-muted);">Parameter otentikasi dan status kewenangan akun</small>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column gap-3" style="font-size: 0.82rem;">
                <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                    <div>
                        <span class="d-block text-muted small">Nomor Identitas Akun (ID)</span>
                        <strong class="fs-6 font-monospace" style="color: var(--nx-text);">#SA-{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</strong>
                    </div>
                    <span class="badge font-monospace bg-primary-subtle text-primary border border-primary-subtle">HQ Master</span>
                </div>

                <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                    <div>
                        <span class="d-block text-muted small">Role / Hak Akses Utama</span>
                        <strong class="fs-6 text-warning" style="color: #D97706 !important;">Super Admin (Platform Owner)</strong>
                    </div>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Permanen</span>
                </div>

                <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                    <div>
                        <span class="d-block text-muted small">Status Akun &amp; Verifikasi</span>
                        <strong class="fs-6 text-success">Aktif &amp; Terverifikasi</strong>
                    </div>
                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                        <i class="fas fa-check me-1"></i> Verified
                    </span>
                </div>

                <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                    <div>
                        <span class="d-block text-muted small">Tanggal Pembuatan Akun</span>
                        <strong class="font-monospace" style="color: var(--nx-text);">{{ $user->created_at ? $user->created_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') : '-' }} WIB</strong>
                    </div>
                    <small class="text-muted">{{ $user->created_at ? $user->created_at->diffForHumans() : '' }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- FORM UBAH EMAIL (DENGAN PROTEKSI PASSWORD) -->
    <div class="col-lg-6">
        <div class="sa-card p-4 rounded-3 border h-100" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
            <div class="border-bottom pb-3 mb-4" style="border-color: var(--nx-border) !important;">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center sa-icon-email-login" style="width: 32px; height: 32px; background: rgba(239, 68, 68, 0.12); color: #EF4444;">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold m-0 fs-6" style="color: var(--nx-text);">Ubah Alamat Email Login</h5>
                        <small style="color: var(--nx-muted);">Perubahan email memerlukan verifikasi kata sandi aktif</small>
                    </div>
                </div>
            </div>

            <div class="p-3 rounded-3 border mb-3" style="background: rgba(14, 165, 233, 0.08); border-color: rgba(14, 165, 233, 0.25) !important;">
                <p class="m-0 small text-primary" style="line-height: 1.45;">
                    <i class="fas fa-circle-info me-1"></i> <strong>Prosedur Keamanan:</strong><br>
                    Email digunakan sebagai identitas autentikasi utama Super Admin HQ. Untuk mencegah pengambilalihan akun yang tidak sah, Anda wajib mengonfirmasi kata sandi saat ini.
                </p>
            </div>

            <form action="{{ route('super_admin.profile.update_account') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-bold" style="color: var(--nx-text);">Alamat Email Login Baru</label>
                    <input type="email" name="email" class="form-control font-monospace" value="{{ old('email', $user->email) }}" required
                           style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-danger">Konfirmasi Kata Sandi Saat Ini</label>
                    <input type="password" name="current_password" class="form-control" placeholder="Masukkan kata sandi login Anda..." required
                           style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                </div>

                <div class="d-flex justify-content-end pt-3 border-top" style="border-color: var(--nx-border) !important;">
                    <button type="submit" class="btn btn-primary px-4 py-2 fw-bold d-inline-flex align-items-center gap-2 shadow-sm" style="border-radius: 8px;">
                        <i class="fas fa-shield"></i>
                        <span>Konfirmasi &amp; Perbarui Email</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- ========================================================================= -->
<!-- TAB C: KEAMANAN AKUN & SESI LOGIN -->
<!-- ========================================================================= -->
@if($activeTab === 'security')
<div class="row g-4 mb-4">
    <!-- FORM UBAH KATA SANDI -->
    <div class="col-lg-6">
        <div class="sa-card p-4 rounded-3 border h-100" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
            <div class="border-bottom pb-3 mb-4" style="border-color: var(--nx-border) !important;">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(239, 68, 68, 0.12); color: #EF4444;">
                        <i class="fas fa-key"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold m-0 fs-6" style="color: var(--nx-text);">Perbarui Kata Sandi Akun</h5>
                        <small style="color: var(--nx-muted);">Gunakan kombinasi minimal 8 karakter dengan huruf dan angka</small>
                    </div>
                </div>
            </div>

            <form action="{{ route('super_admin.profile.update_password') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-bold" style="color: var(--nx-text);">Kata Sandi Lama</label>
                    <input type="password" name="current_password" class="form-control" required
                           style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold" style="color: var(--nx-text);">Kata Sandi Baru</label>
                    <input type="password" name="password" class="form-control" minlength="8" required
                           style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                    <small class="text-muted" style="font-size: 0.72rem;">Minimal 8 karakter.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold" style="color: var(--nx-text);">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" name="password_confirmation" class="form-control" minlength="8" required
                           style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                </div>

                <div class="d-flex justify-content-end pt-3 border-top" style="border-color: var(--nx-border) !important;">
                    <button type="submit" class="btn btn-danger px-4 py-2 fw-bold d-inline-flex align-items-center gap-2 shadow-sm" style="border-radius: 8px;">
                        <i class="fas fa-lock"></i>
                        <span>Perbarui Kata Sandi</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- RIWAYAT KEAMANAN TERKINI -->
    <div class="col-lg-6">
        <div class="sa-card p-4 rounded-3 border h-100" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
            <div class="border-bottom pb-3 mb-3 d-flex align-items-center justify-content-between" style="border-color: var(--nx-border) !important;">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center sa-icon-security-history" style="width: 32px; height: 32px; background: rgba(16, 185, 129, 0.12); color: #10B981;">
                        <i class="fas fa-shield-halved"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold m-0 fs-6" style="color: var(--nx-text);">Riwayat Keamanan Terkini</h5>
                        <small style="color: var(--nx-muted);">Aktivitas login, logout, dan pengubahan kredensial</small>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle m-0" style="color: var(--nx-text); font-size: 0.78rem;">
                    <thead style="background: var(--nx-bg); border-bottom: 1px solid var(--nx-border);">
                        <tr>
                            <th class="py-2 px-2.5 small fw-bold" style="color: var(--nx-muted);">WAKTU</th>
                            <th class="py-2 px-2.5 small fw-bold" style="color: var(--nx-muted);">EVENT</th>
                            <th class="py-2 px-2.5 small fw-bold" style="color: var(--nx-muted);">STATUS</th>
                            <th class="py-2 px-2.5 small fw-bold text-end" style="color: var(--nx-muted);">IP ADDRESS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($securityLogs as $sLog)
                            <tr style="border-bottom: 1px solid var(--nx-border);">
                                <td class="px-2.5 py-2 font-monospace" style="color: var(--nx-muted);">
                                    {{ $sLog->created_at ? $sLog->created_at->timezone('Asia/Jakarta')->translatedFormat('d/m H:i') . ' WIB' : '-' }}
                                </td>
                                <td class="px-2.5 py-2">
                                    @php
                                        $ev = strtolower($sLog->event ?? '');
                                        $secBadgeClass = 'sa-badge-event-slate';
                                        if (str_contains($ev, 'login')) $secBadgeClass = 'sa-badge-event-blue';
                                        elseif (str_contains($ev, 'logout') || str_contains($ev, 'expired')) $secBadgeClass = 'sa-badge-event-amber';
                                    @endphp
                                    <span class="badge {{ $secBadgeClass }} font-monospace">{{ strtoupper($sLog->event) }}</span>
                                </td>
                                <td class="px-2.5 py-2">
                                    <span class="badge {{ $sLog->status === 'success' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                        {{ strtoupper($sLog->status) }}
                                    </span>
                                </td>
                                <td class="px-2.5 py-2 text-end font-monospace" style="color: var(--nx-muted);">
                                    {{ $sLog->ip_address ?: '127.0.0.1' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-3 text-muted small">Belum ada riwayat keamanan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MANAJEMEN SESI LOGIN & PERANGKAT AKTIF -->
<div class="sa-card p-4 rounded-3 border mb-4" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
    <div class="border-bottom pb-3 mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3" style="border-color: var(--nx-border) !important;">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(59, 130, 246, 0.12); color: #3B82F6;">
                <i class="fas fa-desktop"></i>
            </div>
            <div>
                <h5 class="fw-bold m-0 fs-6" style="color: var(--nx-text);">Sesi Login &amp; Perangkat Aktif</h5>
                <small style="color: var(--nx-muted);">Kelola perangkat yang sedang terhubung ke akun Super Admin</small>
            </div>
        </div>

        <button type="button" class="btn btn-sm btn-outline-danger px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-2" style="border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#modalRevokeOtherSessions">
            <i class="fas fa-arrow-right-from-bracket"></i>
            <span>Logout Seluruh Perangkat Lain</span>
        </button>
    </div>

    <div class="row g-3">
        @forelse($sessions as $sess)
            <div class="col-md-6 col-xl-6">
                <div class="p-3 rounded-3 border h-100 d-flex flex-column justify-content-between" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                    <div>
                        <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: {{ $sess->is_current ? 'rgba(40, 89, 67, 0.15)' : 'rgba(100, 116, 139, 0.15)' }}; color: {{ $sess->is_current ? 'var(--nx-forest)' : '#64748B' }}; font-size: 1rem;">
                                    <i class="fas {{ stripos($sess->device_name, 'Windows') !== false || stripos($sess->device_name, 'macOS') !== false ? 'fa-laptop' : 'fa-mobile-screen' }}"></i>
                                </div>
                                <div>
                                    <strong class="d-block small" style="color: var(--nx-text);">{{ $sess->device_name }}</strong>
                                    <span class="badge font-monospace" style="font-size: 0.68rem; background: var(--nx-card-bg); color: var(--nx-muted); border: 1px solid var(--nx-border);">
                                        IP: {{ $sess->ip_address ?: '127.0.0.1' }}
                                    </span>
                                </div>
                            </div>

                            @if($sess->is_current)
                                <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace" style="font-size: 0.68rem;">
                                    <i class="fas fa-circle-dot me-1"></i> Perangkat Ini (Aktif)
                                </span>
                            @else
                                <form action="{{ route('super_admin.profile.session.revoke', $sess->id) }}" method="POST" class="m-0" onsubmit="return confirm('Apakah Anda yakin ingin memutuskan sesi perangkat ini?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-0.5" style="border-radius: 6px; font-size: 0.72rem;">
                                        <i class="fas fa-power-off me-1"></i> Putuskan
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <div class="pt-2 mt-2 border-top d-flex align-items-center justify-content-between" style="border-color: var(--nx-border) !important; font-size: 0.72rem; color: var(--nx-muted);">
                        <span>Aktivitas Terakhir: <strong>{{ $sess->last_activity->diffForHumans() }}</strong></span>
                        <span class="font-monospace">{{ $sess->last_activity ? $sess->last_activity->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') . ' WIB' : '-' }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-4 text-muted small">
                Tidak ada data sesi aktif ditemukan.
            </div>
        @endforelse
    </div>
</div>

<!-- MODAL REVOKE OTHER SESSIONS -->
<div class="modal fade" id="modalRevokeOtherSessions" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
            <div class="modal-header border-bottom" style="border-color: var(--nx-border) !important;">
                <h5 class="modal-title fw-bold fs-6 text-danger d-flex align-items-center gap-2">
                    <i class="fas fa-triangle-exclamation"></i>
                    <span>Konfirmasi Logout Seluruh Perangkat Lain</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('super_admin.profile.session.revoke_others') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="small text-muted mb-3" style="line-height: 1.45;">
                        Tindakan ini akan memutus seluruh sesi login Super Admin di perangkat lain (browser lain, laptop lain, atau smartphone). Sesi yang sedang Anda gunakan saat ini akan tetap aktif.
                    </p>
                    <div class="mb-3">
                        <label class="form-label small fw-bold" style="color: var(--nx-text);">Kata Sandi Super Admin</label>
                        <input type="password" name="confirm_password" class="form-control" placeholder="Masukkan kata sandi untuk konfirmasi..." required
                               style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                    </div>
                </div>
                <div class="modal-footer border-top" style="border-color: var(--nx-border) !important;">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batalkan</button>
                    <button type="submit" class="btn btn-danger btn-sm px-3 fw-bold">Putuskan Sesi Lain Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- ========================================================================= -->
<!-- TAB D: PREFERENSI NOTIFIKASI -->
<!-- ========================================================================= -->
@if($activeTab === 'notifications')
<div class="sa-card p-4 rounded-3 border" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
    <div class="border-bottom pb-3 mb-4" style="border-color: var(--nx-border) !important;">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(245, 158, 11, 0.12); color: #D97706;">
                <i class="fas fa-bell"></i>
            </div>
            <div>
                <h5 class="fw-bold m-0 fs-6" style="color: var(--nx-text);">Preferensi Notifikasi &amp; Saluran Siaran HQ</h5>
                <small style="color: var(--nx-muted);">Atur peringatan keamanan, insiden sistem, dan perubahan otoritas administrator</small>
            </div>
        </div>
    </div>

    <form action="{{ route('super_admin.profile.notifications.update') }}" method="POST">
        @csrf
        <div class="row g-4">
            <!-- SEKSI 1: KATEGORI NOTIFIKASI -->
            <div class="col-lg-8">
                <h6 class="fw-bold small mb-3 text-uppercase font-monospace" style="color: var(--nx-muted);">Kategori Notifikasi Platform</h6>
                
                <div class="d-flex flex-column gap-3">
                    <!-- 1. Login Activity -->
                    <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                        <div>
                            <strong class="d-block small" style="color: var(--nx-text);">Aktivitas Login Akun</strong>
                            <small class="text-muted">Pemberitahuan setiap kali sesi login baru dibuat pada akun Super Admin.</small>
                        </div>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" name="notif_login_activity" value="1" {{ !empty($notifPrefs['notif_login_activity']) ? 'checked' : '' }}>
                        </div>
                    </div>

                    <!-- 2. Account Change -->
                    <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                        <div>
                            <strong class="d-block small" style="color: var(--nx-text);">Perubahan Informasi Akun</strong>
                            <small class="text-muted">Pemberitahuan saat email, nomor telepon, atau profil kredensial diubah.</small>
                        </div>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" name="notif_account_change" value="1" {{ !empty($notifPrefs['notif_account_change']) ? 'checked' : '' }}>
                        </div>
                    </div>

                    <!-- 3. Admin Access Change -->
                    <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                        <div>
                            <strong class="d-block small" style="color: var(--nx-text);">Perubahan Akses Admin RW &amp; Admin RT</strong>
                            <small class="text-muted">Peringatan saat akun pengurus baru ditambahkan, dinonaktifkan, atau dimutasi wilayahnya.</small>
                        </div>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" name="notif_admin_access_change" value="1" {{ !empty($notifPrefs['notif_admin_access_change']) ? 'checked' : '' }}>
                        </div>
                    </div>

                    <!-- 4. Security Alert (Mandatory) -->
                    <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between border-danger-subtle" style="background: rgba(239, 68, 68, 0.05);">
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <strong class="small text-danger">Peringatan Keamanan Kritis</strong>
                                <span class="badge bg-danger text-white font-monospace" style="font-size: 0.64rem;">WAJIB AKTIF</span>
                            </div>
                            <small class="text-muted">Deteksi serangan brute force, percobaan akses 403 tidak sah, dan pembukaan data sensitif.</small>
                        </div>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" checked disabled style="cursor: not-allowed;">
                        </div>
                    </div>

                    <!-- 5. System Outage -->
                    <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                        <div>
                            <strong class="d-block small" style="color: var(--nx-text);">Gangguan &amp; Latensi Sistem</strong>
                            <small class="text-muted">Notifikasi saat response time database tinggi atau terjadi kendala server.</small>
                        </div>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" name="notif_system_outage" value="1" {{ !empty($notifPrefs['notif_system_outage']) ? 'checked' : '' }}>
                        </div>
                    </div>

                    <!-- 6. Backup Failure -->
                    <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                        <div>
                            <strong class="d-block small" style="color: var(--nx-text);">Kegagalan Pencadangan (Backup)</strong>
                            <small class="text-muted">Pemberitahuan darurat jika scheduled backup cron mengalami error.</small>
                        </div>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" name="notif_backup_failure" value="1" {{ !empty($notifPrefs['notif_backup_failure']) ? 'checked' : '' }}>
                        </div>
                    </div>

                    <!-- 7. Platform Maintenance -->
                    <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                        <div>
                            <strong class="d-block small" style="color: var(--nx-text);">Pemeliharaan Platform (Maintenance)</strong>
                            <small class="text-muted">Status aktivasi mode pemeliharaan global dan parsial RW 018.</small>
                        </div>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" name="notif_platform_maintenance" value="1" {{ !empty($notifPrefs['notif_platform_maintenance']) ? 'checked' : '' }}>
                        </div>
                    </div>

                    <!-- 8. Platform Update -->
                    <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                        <div>
                            <strong class="d-block small" style="color: var(--nx-text);">Pembaruan &amp; Rilis Versi Baru</strong>
                            <small class="text-muted">Pengumuman rilis fitur baru atau patch keamanan platform NexaNest.</small>
                        </div>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" name="notif_platform_update" value="1" {{ !empty($notifPrefs['notif_platform_update']) ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SEKSI 2: KANAL PENGIRIMAN -->
            <div class="col-lg-4">
                <h6 class="fw-bold small mb-3 text-uppercase font-monospace" style="color: var(--nx-muted);">Kanal Notifikasi</h6>

                <div class="p-3 rounded-3 border mb-3" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                    <div class="form-check form-switch m-0 mb-2">
                        <input class="form-check-input" type="checkbox" name="channel_in_app" value="1" id="switchInApp" {{ !empty($notifPrefs['channel_in_app']) ? 'checked' : '' }}>
                        <label class="form-check-label small fw-bold" for="switchInApp" style="color: var(--nx-text);">
                            <i class="fas fa-bell me-1 text-primary"></i> Notifikasi Dalam Aplikasi (In-App)
                        </label>
                    </div>
                    <small class="text-muted d-block" style="font-size: 0.72rem;">
                        Tampilkan banner mengambang dan lonceng notifikasi pada dashboard HQ.
                    </small>
                </div>

                <div class="p-3 rounded-3 border" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                    <div class="form-check form-switch m-0 mb-2">
                        <input class="form-check-input" type="checkbox" name="channel_email" value="1" id="switchEmail" {{ !empty($notifPrefs['channel_email']) ? 'checked' : '' }}>
                        <label class="form-check-label small fw-bold" for="switchEmail" style="color: var(--nx-text);">
                            <i class="fas fa-envelope me-1 text-success"></i> Notifikasi Email Resmi
                        </label>
                    </div>
                    <small class="text-muted d-block" style="font-size: 0.72rem;">
                        Kirimkan ringkasan peringatan kritis ke: <code style="font-size: 0.70rem;">{{ $user->email }}</code>
                    </small>
                </div>
            </div>

            <!-- SUBMIT BUTTON -->
            <div class="col-12 d-flex justify-content-end pt-3 border-top" style="border-color: var(--nx-border) !important;">
                <button type="submit" class="btn btn-success px-4 py-2 fw-bold d-inline-flex align-items-center gap-2 shadow-sm" style="border-radius: 8px;">
                    <i class="fas fa-check-circle"></i>
                    <span>Simpan Preferensi Notifikasi</span>
                </button>
            </div>
        </div>
    </form>
</div>
@endif

<!-- ========================================================================= -->
<!-- TAB E: AKTIVITAS SAYA -->
<!-- ========================================================================= -->
@if($activeTab === 'activity')
<div class="sa-card p-4 rounded-3 border mb-4" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
    <div class="border-bottom pb-3 mb-3 d-flex flex-wrap align-items-center justify-content-between gap-3" style="border-color: var(--nx-border) !important;">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(59, 130, 246, 0.12); color: #3B82F6;">
                <i class="fas fa-clock-rotate-left"></i>
            </div>
            <div>
                <h5 class="fw-bold m-0 fs-6" style="color: var(--nx-text);">Riwayat Aktivitas Super Admin (Audit Trail Pribadi)</h5>
                <small style="color: var(--nx-muted);">Catatan seluruh tindakan konfigurasi, otorisasi, dan manajemen yang Anda lakukan</small>
            </div>
        </div>

        <a href="{{ route('super_admin.audit.index') }}" class="btn btn-sm btn-link text-decoration-none p-0 fw-bold" style="color: var(--nx-forest); font-size: 0.78rem;">
            Lihat Semua Audit Platform &rarr;
        </a>
    </div>

    <!-- FILTER BAR AKTIVITAS -->
    <form action="{{ route('super_admin.profile.index') }}" method="GET" class="row g-2 mb-3">
        <input type="hidden" name="tab" value="activity">
        
        <div class="col-md-3">
            <select name="act_module" class="form-select form-select-sm" style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                <option value="semua">Semua Modul Terkait</option>
                @foreach($modulesList as $mod)
                    <option value="{{ $mod }}" {{ request('act_module') === $mod ? 'selected' : '' }}>{{ $mod }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <select name="act_type" class="form-select form-select-sm" style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                <option value="semua">Semua Jenis Aksi</option>
                @foreach($actionTypesList as $type)
                    <option value="{{ $type }}" {{ request('act_type') === $type ? 'selected' : '' }}>{{ $type }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <input type="date" name="act_date" class="form-control form-control-sm" value="{{ request('act_date') }}"
                   style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
        </div>

        <div class="col-md-3 d-flex align-items-center gap-1.5">
            <button type="submit" class="btn btn-sm btn-primary px-3 fw-bold flex-grow-1" style="border-radius: 6px;">
                <i class="fas fa-filter me-1"></i> Filter
            </button>
            @if(request()->anyFilled(['act_module', 'act_type', 'act_date']))
                <a href="{{ route('super_admin.profile.index', ['tab' => 'activity']) }}" class="btn btn-sm btn-outline-secondary px-2.5" style="border-radius: 6px;" title="Reset Filter">
                    <i class="fas fa-rotate-left"></i>
                </a>
            @endif
        </div>
    </form>

    <!-- TABEL RIWAYAT AKTIVITAS PRIBADI -->
    <div class="table-responsive">
        <table class="table align-middle m-0" style="color: var(--nx-text); font-size: 0.8rem;">
            <thead style="background: var(--nx-bg); border-bottom: 1px solid var(--nx-border);">
                <tr>
                    <th class="py-2.5 px-3 small fw-bold" style="color: var(--nx-muted);">ID LOG</th>
                    <th class="py-2.5 px-3 small fw-bold" style="color: var(--nx-muted);">WAKTU</th>
                    <th class="py-2.5 px-3 small fw-bold" style="color: var(--nx-muted);">MODUL</th>
                    <th class="py-2.5 px-3 small fw-bold" style="color: var(--nx-muted);">AKSI</th>
                    <th class="py-2.5 px-3 small fw-bold" style="color: var(--nx-muted);">DESKRIPSI TINDAKAN</th>
                    <th class="py-2.5 px-3 small fw-bold text-end" style="color: var(--nx-muted);">IP ADDRESS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($activities as $act)
                    <tr style="border-bottom: 1px solid var(--nx-border);">
                        <td class="px-3 py-2.5 font-monospace fw-bold text-primary" style="font-size: 0.74rem;">
                            {{ $act->formatted_id }}
                        </td>
                        <td class="px-3 py-2.5 font-monospace text-muted" style="font-size: 0.74rem;">
                            {{ $act->created_at ? $act->created_at->timezone('Asia/Jakarta')->translatedFormat('d/m/Y H:i:s') . ' WIB' : '-' }}
                        </td>
                        <td class="px-3 py-2.5">
                            <span class="badge bg-light text-dark border font-monospace sa-badge-module" style="font-size: 0.68rem;">{{ $act->module }}</span>
                        </td>
                        <td class="px-3 py-2.5">
                            @php
                                $actType = strtolower($act->action_type ?: $act->event ?: '');
                                $actBadgeClass = 'sa-badge-event-slate';
                                if (str_contains($actType, 'login') || str_contains($actType, 'create')) $actBadgeClass = 'sa-badge-event-blue';
                                elseif (str_contains($actType, 'logout') || str_contains($actType, 'expired') || str_contains($actType, 'delete')) $actBadgeClass = 'sa-badge-event-amber';
                            @endphp
                            <span class="badge {{ $actBadgeClass }} font-monospace" style="font-size: 0.68rem;">{{ $act->action_type ?: $act->event }}</span>
                        </td>
                        <td class="px-3 py-2.5 text-muted" style="font-size: 0.78rem;">
                            {{ $act->description }}
                        </td>
                        <td class="px-3 py-2.5 text-end font-monospace text-muted" style="font-size: 0.72rem;">
                            {{ $act->ip_address ?: '127.0.0.1' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted small">
                            <i class="fas fa-clock-rotate-left fa-2x mb-2 d-block opacity-25"></i>
                            <span>Belum ada catatan aktivitas yang sesuai dengan filter.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($activities->hasPages())
        <div class="p-3 border-top" style="border-color: var(--nx-border) !important;">
            {{ $activities->appends(['tab' => 'activity', 'act_module' => request('act_module'), 'act_type' => request('act_type'), 'act_date' => request('act_date')])->links() }}
        </div>
    @endif
</div>
@endif

<!-- ========================================================================= -->
<!-- TAB F: PREFERENSI TAMPILAN -->
<!-- ========================================================================= -->
@if($activeTab === 'preferences')
<div class="sa-card p-4 rounded-3 border" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
    <div class="border-bottom pb-3 mb-4" style="border-color: var(--nx-border) !important;">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(147, 51, 234, 0.12); color: #9333EA;">
                <i class="fas fa-palette"></i>
            </div>
            <div>
                <h5 class="fw-bold m-0 fs-6" style="color: var(--nx-text);">Preferensi Tampilan &amp; Tema (Day / Night Mode)</h5>
                <small style="color: var(--nx-muted);">Sesuaikan tema visual antarmuka platform sesuai kenyamanan kerja Anda</small>
            </div>
        </div>
    </div>

    <form action="{{ route('super_admin.profile.preferences.update') }}" method="POST">
        @csrf
        <div class="row g-4">
            <!-- PILIHAN TEMA VISUAL -->
            <div class="col-md-4">
                <div class="p-4 rounded-3 border h-100 text-center theme-option-card" 
                     style="background: #F8FAFC; border: 2px solid {{ $user->theme_preference === 'light' ? 'var(--nx-forest)' : 'var(--nx-border)' }} !important; cursor: pointer;"
                     onclick="selectThemePreference('light')">
                    <div class="mb-3 d-inline-block rounded-circle p-3" style="background: #FFFFFF; color: #D97706; box-shadow: 0 4px 12px rgba(0,0,0,0.06);">
                        <i class="fas fa-sun fa-2x"></i>
                    </div>
                    <h6 class="fw-bold m-0 text-dark">Day / Light Mode</h6>
                    <small class="text-muted d-block mt-1">Nuansa terang dengan aksen Forest Green &amp; latar putih bersih.</small>
                    <div class="form-check d-flex justify-content-center mt-3">
                        <input class="form-check-input" type="radio" name="theme_preference" id="themeLight" value="light" {{ $user->theme_preference === 'light' ? 'checked' : '' }}>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="p-4 rounded-3 border h-100 text-center theme-option-card" 
                     style="background: #0A0F1E; border: 2px solid {{ $user->theme_preference === 'dark' ? 'var(--nx-forest)' : '#1E2D4A' }} !important; cursor: pointer;"
                     onclick="selectThemePreference('dark')">
                    <div class="mb-3 d-inline-block rounded-circle p-3" style="background: #131B2E; color: #38BDF8; box-shadow: 0 4px 12px rgba(0,0,0,0.3);">
                        <i class="fas fa-moon fa-2x"></i>
                    </div>
                    <h6 class="fw-bold m-0 text-white">Night / Dark Mode</h6>
                    <small class="text-white-50 d-block mt-1">Nuansa gelap Dark Navy &amp; Deep Indigo, nyaman untuk pemantauan malam.</small>
                    <div class="form-check d-flex justify-content-center mt-3">
                        <input class="form-check-input" type="radio" name="theme_preference" id="themeDark" value="dark" {{ $user->theme_preference === 'dark' ? 'checked' : '' }}>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="p-4 rounded-3 border h-100 text-center theme-option-card" 
                     style="background: var(--nx-bg); border: 2px solid {{ $user->theme_preference === 'system' ? 'var(--nx-forest)' : 'var(--nx-border)' }} !important; cursor: pointer;"
                     onclick="selectThemePreference('system')">
                    <div class="mb-3 d-inline-block rounded-circle p-3" style="background: var(--nx-card-bg); color: #64748B;">
                        <i class="fas fa-circle-half-stroke fa-2x"></i>
                    </div>
                    <h6 class="fw-bold m-0" style="color: var(--nx-text);">Otomatis / Sistem</h6>
                    <small class="d-block mt-1" style="color: var(--nx-muted);">Menyesuaikan secara otomatis dengan tema sistem operasi perangkat.</small>
                    <div class="form-check d-flex justify-content-center mt-3">
                        <input class="form-check-input" type="radio" name="theme_preference" id="themeSystem" value="system" {{ $user->theme_preference === 'system' ? 'checked' : '' }}>
                    </div>
                </div>
            </div>

            <!-- SUBMIT BUTTON -->
            <div class="col-12 d-flex justify-content-end pt-3 border-top" style="border-color: var(--nx-border) !important;">
                <button type="submit" class="btn btn-success px-4 py-2 fw-bold d-inline-flex align-items-center gap-2 shadow-sm" style="border-radius: 8px;">
                    <i class="fas fa-check-circle"></i>
                    <span>Simpan Preferensi Tampilan</span>
                </button>
            </div>
        </div>
    </form>
</div>
@endif

@push('scripts')
<script>
function selectThemePreference(theme) {
    if (theme === 'light') {
        document.getElementById('themeLight').checked = true;
        if (document.documentElement.classList.contains('dark')) {
            window.toggleNexaTheme();
        }
    } else if (theme === 'dark') {
        document.getElementById('themeDark').checked = true;
        if (!document.documentElement.classList.contains('dark')) {
            window.toggleNexaTheme();
        }
    } else if (theme === 'system') {
        document.getElementById('themeSystem').checked = true;
    }
}
</script>
@endpush
@endsection
