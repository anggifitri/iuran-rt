@extends('layouts.app')

@section('title', 'Profil Saya — NexaNest')

@section('content')
<!-- ==================== HEADER HALAMAN ==================== -->
<div class="nx-page-header mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
            <span class="badge rounded-pill px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
                <i class="fas fa-user-shield me-1"></i> Identitas &amp; Pengelolaan Akun
            </span>
            <span class="badge rounded-pill px-2.5 py-1 bg-dark" style="font-size: 0.72rem;">
                {{ $user->role_label }}
            </span>
            @if($user->email_verified_at)
            <span class="badge rounded-pill px-2.5 py-1" style="background: #E8F5E9; color: #2e7d32; font-size: 0.72rem;">
                <i class="fas fa-check-circle me-1"></i> Akun Terverifikasi
            </span>
            @endif
        </div>
        <h1 class="nx-page-title mb-1">Pengaturan Profil Saya</h1>
        <p class="nx-page-subtitle mb-0">
            Pusat pengelolaan identitas kependudukan, kredensial akun, keamanan autentikasi, preferensi notifikasi, dan riwayat aktivitas pribadi Anda.
        </p>
    </div>

    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary fw-bold" style="border-radius: 12px;">
            <i class="fas fa-arrow-left me-1.5"></i> Dashboard
        </a>
    </div>
</div>

<!-- ==================== ALERTS ==================== -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert" style="background: var(--nx-mint-soft); border-color: var(--nx-sage); color: var(--nx-forest); border-radius: 14px;">
    <i class="fas fa-circle-check me-2 fs-5"></i>
    <div>{{ session('success') }}</div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(isset($errors) && $errors->any())
<div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 14px;">
    <div class="d-flex align-items-center mb-1">
        <i class="fas fa-circle-exclamation me-2 fs-5"></i>
        <strong class="small">Terdapat kendala pada formulir:</strong>
    </div>
    <ul class="mb-0 ps-3 small">
        @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- ==================== MAIN 2-COLUMN LAYOUT ==================== -->
<div class="row g-4">
    <!-- ==================== KOLOM KIRI (col-lg-4): PROFIL SUMMARY & SIDE NAV ==================== -->
    <div class="col-lg-4 col-12">
        <!-- 1. KARTU IDENTITAS PENGGUNA -->
        <div class="card p-4 text-center border-0 shadow-sm mb-4" style="border-radius: 18px; border: 1px solid var(--nx-border) !important;">
            <!-- Avatar Foto Profil -->
            <div class="position-relative d-inline-block mx-auto mb-3">
                <img id="avatarPreviewSidebar"
                     src="{{ $user->profile_photo_url }}"
                     alt="Avatar {{ $user->name }}"
                     class="rounded-circle shadow-sm"
                     width="115"
                     height="115"
                     style="object-fit: cover; border: 4px solid var(--nx-mint);">
                <span class="position-absolute bottom-0 end-0 rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm"
                      style="width: 34px; height: 34px; background: var(--nx-forest); color: #fff;">
                    <i class="fas fa-camera small"></i>
                </span>
            </div>

            <!-- Nama & Email -->
            <h5 class="fw-bold mb-1" style="color: var(--nx-dark); font-size: 1.15rem;">{{ $user->name }}</h5>
            <p class="text-muted small mb-3" style="font-size: 0.8rem;">{{ $user->email }}</p>

            <!-- Badges Role & Wilayah -->
            <div class="d-flex justify-content-center gap-1.5 flex-wrap mb-3">
                <span class="badge rounded-pill px-3 py-1.5" style="background: var(--nx-mint); color: var(--nx-forest); font-weight: 700; font-size: 0.72rem;">
                    <i class="fas fa-id-badge me-1"></i>
                    {{ $user->isAdminRw() ? 'Admin RW 018' : ($user->isAdminRt() ? 'Admin RT ' . ($user->rt_number ?: '006') : 'Warga Komunitas') }}
                </span>
                <span class="badge rounded-pill px-3 py-1.5" style="background: #E0F2FE; color: #0369A1; font-weight: 700; font-size: 0.72rem;">
                    <i class="fas fa-location-dot me-1"></i>
                    {{ $user->rt_number === '000' ? 'Seluruh RW 018' : 'Wilayah RT ' . ($user->rt_number ?: '006') }}
                </span>
            </div>

            <!-- Mini Statistik Aktivitas Akun -->
            <div class="row g-2 pt-3 border-top" style="border-color: var(--nx-border) !important;">
                <div class="col-4">
                    <div class="p-2 rounded-3 bg-light border">
                        <span class="d-block text-muted" style="font-size: 0.68rem;">Aktivitas</span>
                        <strong class="d-block" style="color: var(--nx-dark); font-size: 0.95rem;">{{ $totalActivities }}</strong>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-2 rounded-3 bg-light border">
                        <span class="d-block text-muted" style="font-size: 0.68rem;">Surat</span>
                        <strong class="d-block" style="color: var(--nx-forest); font-size: 0.95rem;">{{ $totalSurat }}</strong>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-2 rounded-3 bg-light border">
                        <span class="d-block text-muted" style="font-size: 0.68rem;">Iuran</span>
                        <strong class="d-block" style="color: #0284c7; font-size: 0.95rem;">{{ $totalPembayaran }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. MENU NAVIGASI PENGATURAN (TABS SIDEBAR) -->
        <div class="card border-0 shadow-sm p-2 mb-4" style="border-radius: 18px; border: 1px solid var(--nx-border) !important;">
            <div class="nav flex-column nav-pills gap-1">
                <!-- Tab 1: Personal -->
                <a href="{{ route('profile.edit', ['tab' => 'personal']) }}"
                   class="nav-link py-2.5 px-3 fw-bold d-flex align-items-center justify-content-between {{ $activeTab === 'personal' ? 'active' : 'text-secondary' }}"
                   style="{{ $activeTab === 'personal' ? 'background: var(--nx-forest); border-radius: 12px;' : 'border-radius: 12px;' }}">
                    <span><i class="fas fa-user-pen me-2.5" style="width: 18px;"></i> Informasi Pribadi</span>
                    <i class="fas fa-chevron-right small opacity-50"></i>
                </a>

                <!-- Tab 2: Account -->
                <a href="{{ route('profile.edit', ['tab' => 'account']) }}"
                   class="nav-link py-2.5 px-3 fw-bold d-flex align-items-center justify-content-between {{ $activeTab === 'account' ? 'active' : 'text-secondary' }}"
                   style="{{ $activeTab === 'account' ? 'background: var(--nx-forest); border-radius: 12px;' : 'border-radius: 12px;' }}">
                    <span><i class="fas fa-id-card me-2.5" style="width: 18px;"></i> Informasi Akun</span>
                    <i class="fas fa-chevron-right small opacity-50"></i>
                </a>

                <!-- Tab 3: Security -->
                <a href="{{ route('profile.edit', ['tab' => 'security']) }}"
                   class="nav-link py-2.5 px-3 fw-bold d-flex align-items-center justify-content-between {{ $activeTab === 'security' ? 'active' : 'text-secondary' }}"
                   style="{{ $activeTab === 'security' ? 'background: var(--nx-forest); border-radius: 12px;' : 'border-radius: 12px;' }}">
                    <span><i class="fas fa-shield-halved me-2.5" style="width: 18px;"></i> Keamanan &amp; Sesi</span>
                    <i class="fas fa-chevron-right small opacity-50"></i>
                </a>

                <!-- Tab 4: Notifications -->
                <a href="{{ route('profile.edit', ['tab' => 'notifications']) }}"
                   class="nav-link py-2.5 px-3 fw-bold d-flex align-items-center justify-content-between {{ $activeTab === 'notifications' ? 'active' : 'text-secondary' }}"
                   style="{{ $activeTab === 'notifications' ? 'background: var(--nx-forest); border-radius: 12px;' : 'border-radius: 12px;' }}">
                    <span><i class="fas fa-bell me-2.5" style="width: 18px;"></i> Preferensi Notifikasi</span>
                    <i class="fas fa-chevron-right small opacity-50"></i>
                </a>

                <!-- Tab 5: Activity -->
                <a href="{{ route('profile.edit', ['tab' => 'activity']) }}"
                   class="nav-link py-2.5 px-3 fw-bold d-flex align-items-center justify-content-between {{ $activeTab === 'activity' ? 'active' : 'text-secondary' }}"
                   style="{{ $activeTab === 'activity' ? 'background: var(--nx-forest); border-radius: 12px;' : 'border-radius: 12px;' }}">
                    <span><i class="fas fa-clock-rotate-left me-2.5" style="width: 18px;"></i> Aktivitas Saya</span>
                    <span class="badge {{ $activeTab === 'activity' ? 'bg-light text-dark' : 'bg-secondary bg-opacity-10 text-secondary' }}">{{ $totalActivities }}</span>
                </a>

                <!-- Tab 6: Preferences -->
                <a href="{{ route('profile.edit', ['tab' => 'preferences']) }}"
                   class="nav-link py-2.5 px-3 fw-bold d-flex align-items-center justify-content-between {{ $activeTab === 'preferences' ? 'active' : 'text-secondary' }}"
                   style="{{ $activeTab === 'preferences' ? 'background: var(--nx-forest); border-radius: 12px;' : 'border-radius: 12px;' }}">
                    <span><i class="fas fa-sliders me-2.5" style="width: 18px;"></i> Pengaturan &amp; Privasi</span>
                    <i class="fas fa-chevron-right small opacity-50"></i>
                </a>
            </div>
        </div>

        <!-- 3. KARTU KEBIJAKAN ADMINISTRASI ROLE -->
        <div class="card p-3.5 border-0 shadow-sm" style="border-radius: 16px; background: var(--nx-mint-soft); border: 1px solid var(--nx-sage) !important;">
            <div class="d-flex align-items-start gap-2.5">
                <i class="fas fa-circle-info text-success mt-0.5 fs-5"></i>
                <div>
                    <h6 class="fw-bold mb-1" style="color: var(--nx-forest); font-size: 0.85rem;">Integritas Data Wilayah RW 018</h6>
                    <p class="small text-muted mb-0" style="font-size: 0.76rem; line-height: 1.45;">
                        Nomor wilayah RT dan peran sistem (Role) dikunci terpusat oleh basis data sistem. Apabila terdapat perubahan domisili, silakan hubungi pengurus melalui menu Mutasi Warga.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== KOLOM KANAN (col-lg-8): KONTEN TAB FORM ==================== -->
    <div class="col-lg-8 col-12">
        <!-- ==================== TAB 1: INFORMASI PRIBADI ==================== -->
        @if($activeTab === 'personal')
        <div class="card p-4 border-0 shadow-sm" style="border-radius: 18px; border: 1px solid var(--nx-border) !important;">
            <div class="d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom" style="border-color: var(--nx-border) !important;">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: var(--nx-mint); color: var(--nx-forest);">
                        <i class="fas fa-user-pen"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0" style="color: var(--nx-dark); font-size: 1.05rem;">1. Informasi Pribadi &amp; Biodata Warga</h5>
                        <small class="text-muted" style="font-size: 0.78rem;">Kelola identitas diri, foto profil, dan data kependudukan resmi Anda.</small>
                    </div>
                </div>
            </div>

            <form action="{{ route('profile.update_personal') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Upload Foto Profil -->
                <div class="mb-4 p-3 rounded-4 bg-light border" style="border-color: var(--nx-border) !important;">
                    <label class="form-label small fw-bold" style="color: var(--nx-dark);">Foto Profil Baru</label>
                    <div class="d-flex align-items-center gap-3">
                        <img id="avatarPreviewForm" src="{{ $user->profile_photo_url }}" class="rounded-circle border" width="60" height="60" style="object-fit: cover;">
                        <div class="flex-grow-1">
                            <input type="file" name="profile_photo" id="inputPhotoForm" accept=".jpg,.jpeg,.png,.webp" class="form-control form-control-sm" style="border-radius: 10px;">
                            <span class="text-muted" style="font-size: 0.72rem;">Format JPG, PNG, atau WEBP. Ukuran maksimal 3 MB.</span>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <!-- Nama Lengkap -->
                    <div class="col-md-6 col-12">
                        <label class="form-label small fw-bold" style="color: var(--nx-dark);">
                            Nama Lengkap <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                               class="form-control" style="border-radius: 12px; border: 1px solid var(--nx-border); font-size: 0.88rem;">
                    </div>

                    <!-- Jenis Kelamin -->
                    <div class="col-md-6 col-12">
                        <label class="form-label small fw-bold" style="color: var(--nx-dark);">Jenis Kelamin</label>
                        <select name="gender" class="form-select" style="border-radius: 12px; border: 1px solid var(--nx-border); font-size: 0.88rem;">
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="L" {{ old('gender', $warga ? $warga->gender : '') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('gender', $warga ? $warga->gender : '') === 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <!-- NIK (Masked & Protected) -->
                    <div class="col-md-6 col-12">
                        <label class="form-label small fw-bold d-flex justify-content-between align-items-center" style="color: var(--nx-dark);">
                            <span>Nomor Induk Kependudukan (NIK)</span>
                            <span class="badge bg-light text-secondary rounded-pill border px-2 py-0.5" style="font-size: 0.68rem;">Tersamarkan</span>
                        </label>
                        <div class="input-group">
                            <input type="text" id="nikInput" value="{{ $user->masked_nik }}" readonly
                                   data-full="{{ $user->nik }}" data-masked="{{ $user->masked_nik }}"
                                   class="form-control bg-light" style="border-radius: 12px 0 0 12px; border: 1px solid var(--nx-border); font-size: 0.88rem; font-family: monospace;">
                            <button type="button" class="btn btn-outline-secondary" onclick="toggleMaskNik()" style="border-radius: 0 12px 12px 0;" title="Lihat/Sembunyikan NIK">
                                <i class="fas fa-eye" id="nikToggleIcon"></i>
                            </button>
                        </div>
                        <small class="text-muted" style="font-size: 0.72rem;">Perubahan nomor identitas NIK hanya dapat dilakukan melalui verifikasi pengurus RW.</small>
                    </div>

                    <!-- No KK -->
                    <div class="col-md-6 col-12">
                        <label class="form-label small fw-bold" style="color: var(--nx-dark);">Nomor Kartu Keluarga (KK)</label>
                        <input type="text" value="{{ $user->no_kk ?: ($warga ? $warga->no_kk : 'Belum Terdaftar') }}" readonly
                               class="form-control bg-light" style="border-radius: 12px; border: 1px solid var(--nx-border); font-size: 0.88rem; font-family: monospace;">
                    </div>

                    <!-- Tempat Lahir & Tanggal Lahir -->
                    <div class="col-md-6 col-12">
                        <label class="form-label small fw-bold" style="color: var(--nx-dark);">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $warga ? $warga->tempat_lahir : '') }}" placeholder="Contoh: Jakarta"
                               class="form-control" style="border-radius: 12px; border: 1px solid var(--nx-border); font-size: 0.88rem;">
                    </div>
                    <div class="col-md-6 col-12">
                        <label class="form-label small fw-bold" style="color: var(--nx-dark);">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $warga && $warga->tanggal_lahir ? (is_string($warga->tanggal_lahir) ? $warga->tanggal_lahir : $warga->tanggal_lahir->format('Y-m-d')) : '') }}"
                               class="form-control" style="border-radius: 12px; border: 1px solid var(--nx-border); font-size: 0.88rem;">
                    </div>

                    <!-- Agama & Status Perkawinan -->
                    <div class="col-md-6 col-12">
                        <label class="form-label small fw-bold" style="color: var(--nx-dark);">Agama</label>
                        <select name="agama" class="form-select" style="border-radius: 12px; border: 1px solid var(--nx-border); font-size: 0.88rem;">
                            <option value="">-- Pilih Agama --</option>
                            @foreach(['Islam', 'Kristen Protestan', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu'] as $ag)
                                <option value="{{ $ag }}" {{ old('agama', $warga ? $warga->agama : '') === $ag ? 'selected' : '' }}>{{ $ag }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 col-12">
                        <label class="form-label small fw-bold" style="color: var(--nx-dark);">Status Perkawinan</label>
                        <select name="status_perkawinan" class="form-select" style="border-radius: 12px; border: 1px solid var(--nx-border); font-size: 0.88rem;">
                            <option value="">-- Pilih Status --</option>
                            @foreach(['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'] as $sp)
                                <option value="{{ $sp }}" {{ old('status_perkawinan', $warga ? $warga->status_perkawinan : '') === $sp ? 'selected' : '' }}>{{ $sp }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Pekerjaan & Blok Rumah -->
                    <div class="col-md-6 col-12">
                        <label class="form-label small fw-bold" style="color: var(--nx-dark);">Pekerjaan</label>
                        <input type="text" name="pekerjaan" value="{{ old('pekerjaan', $warga ? $warga->pekerjaan : '') }}" placeholder="Contoh: Karyawan Swasta / Wiraswasta"
                               class="form-control" style="border-radius: 12px; border: 1px solid var(--nx-border); font-size: 0.88rem;">
                    </div>
                    <div class="col-md-6 col-12">
                        <label class="form-label small fw-bold" style="color: var(--nx-dark);">Blok &amp; Nomor Rumah</label>
                        <input type="text" name="blok_rumah" value="{{ old('blok_rumah', $warga ? $warga->blok_rumah : '') }}" placeholder="Contoh: Blok B No. 14"
                               class="form-control" style="border-radius: 12px; border: 1px solid var(--nx-border); font-size: 0.88rem;">
                    </div>

                    <!-- Alamat Lengkap -->
                    <div class="col-12">
                        <label class="form-label small fw-bold" style="color: var(--nx-dark);">
                            Alamat Domisili Lengkap <span class="text-danger">*</span>
                        </label>
                        <textarea name="address" rows="3" required class="form-control" style="border-radius: 12px; border: 1px solid var(--nx-border); font-size: 0.88rem;">{{ old('address', $user->address) }}</textarea>
                    </div>

                    <!-- Wilayah RT & RW (Terkunci) -->
                    <div class="col-md-6 col-12">
                        <label class="form-label small fw-bold text-muted">Wilayah RT (Terkunci)</label>
                        <input type="text" value="RT {{ $user->rt_number ?: '006' }}" readonly class="form-control bg-light" style="border-radius: 12px; font-size: 0.88rem;">
                    </div>
                    <div class="col-md-6 col-12">
                        <label class="form-label small fw-bold text-muted">Wilayah RW (Terkunci)</label>
                        <input type="text" value="RW 018" readonly class="form-control bg-light" style="border-radius: 12px; font-size: 0.88rem;">
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top" style="border-color: var(--nx-border) !important;">
                    <button type="submit" class="btn btn-primary fw-bold px-4 py-2.5" style="border-radius: 12px; background: var(--nx-forest); border-color: var(--nx-forest);">
                        <i class="fas fa-save me-1.5"></i> Simpan Perubahan Biodata
                    </button>
                </div>
            </form>
        </div>

        <!-- ==================== TAB 2: INFORMASI AKUN ==================== -->
        @elseif($activeTab === 'account')
        <div class="card p-4 border-0 shadow-sm" style="border-radius: 18px; border: 1px solid var(--nx-border) !important;">
            <div class="d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom" style="border-color: var(--nx-border) !important;">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: #e0f2fe; color: #0369a1;">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0" style="color: var(--nx-dark); font-size: 1.05rem;">2. Informasi Akun &amp; Kredensial Kontak</h5>
                        <small class="text-muted" style="font-size: 0.78rem;">Pembaruan email dan kontak WhatsApp dilindungi konfirmasi kata sandi.</small>
                    </div>
                </div>
            </div>

            <form action="{{ route('profile.update_account') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <!-- Email Akun -->
                    <div class="col-md-6 col-12">
                        <label class="form-label small fw-bold d-flex justify-content-between align-items-center" style="color: var(--nx-dark);">
                            <span>Alamat Email Utama (@gmail.com) <span class="text-danger">*</span></span>
                            @if($user->email_verified_at)
                                <span class="badge rounded-pill" style="background: #e8f5e9; color: #2e7d32; font-size: 0.68rem;"><i class="fas fa-circle-check me-0.5"></i> Terverifikasi</span>
                            @endif
                        </label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="form-control" style="border-radius: 12px; border: 1px solid var(--nx-border); font-size: 0.88rem;"
                               placeholder="contoh: nama@gmail.com">
                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;"><i class="fas fa-info-circle me-1"></i> Alamat email wajib menggunakan domain @gmail.com (contoh: nama@gmail.com).</small>
                    </div>

                    <!-- Nomor Telepon -->
                    <div class="col-md-6 col-12">
                        <label class="form-label small fw-bold d-flex justify-content-between align-items-center" style="color: var(--nx-dark);">
                            <span>Nomor Telepon / WhatsApp <span class="text-danger">*</span></span>
                            @if($user->phone_verified_at)
                                <span class="badge rounded-pill" style="background: #e8f5e9; color: #2e7d32; font-size: 0.68rem;"><i class="fas fa-circle-check me-0.5"></i> Terverifikasi</span>
                            @else
                                <span class="badge rounded-pill bg-warning text-dark" style="font-size: 0.68rem;">Belum Terverifikasi</span>
                            @endif
                        </label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required
                               class="form-control" style="border-radius: 12px; border: 1px solid var(--nx-border); font-size: 0.88rem;">
                    </div>

                    <!-- Peran Sistem (Role) -->
                    <div class="col-md-6 col-12">
                        <label class="form-label small fw-bold text-muted">Peran Akun Sistem (Terkunci)</label>
                        <input type="text" value="{{ $user->role_label }}" readonly class="form-control bg-light" style="border-radius: 12px; font-size: 0.88rem;">
                        <small class="text-muted" style="font-size: 0.72rem;">Peran pengguna ditentukan oleh otorisasi pengurus RW 018.</small>
                    </div>

                    <!-- Tanggal Bergabung -->
                    <div class="col-md-6 col-12">
                        <label class="form-label small fw-bold text-muted">Tanggal Pendaftaran Akun</label>
                        <input type="text" value="{{ $user->created_at->translatedFormat('d F Y, H:i') }} WIB" readonly class="form-control bg-light" style="border-radius: 12px; font-size: 0.88rem;">
                    </div>

                    <!-- Password Saat Ini (Verifikasi Keamanan) -->
                    <div class="col-12 mt-4 pt-3 border-top" style="border-color: var(--nx-border) !important;">
                        <div class="p-3 rounded-4" style="background: #f8fafc; border: 1px dashed var(--nx-border);">
                            <label class="form-label small fw-bold text-dark mb-1">
                                <i class="fas fa-lock text-warning me-1"></i> Masukkan Kata Sandi Saat Ini <span class="text-danger">*</span>
                            </label>
                            <input type="password" name="current_password" required placeholder="Ketikkan kata sandi untuk otorisasi perubahan..."
                                   class="form-control" style="border-radius: 10px; font-size: 0.88rem; max-width: 450px;">
                            <small class="text-muted d-block mt-1" style="font-size: 0.74rem;">Konfirmasi kata sandi diperlukan setiap kali memperbarui kontak utama.</small>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top" style="border-color: var(--nx-border) !important;">
                    @if(!$user->phone_verified_at)
                    <form action="{{ route('profile.verify_phone') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-success btn-sm fw-bold px-3" style="border-radius: 10px;">
                            <i class="fab fa-whatsapp me-1"></i> Verifikasi Nomor Telepon
                        </button>
                    </form>
                    @else
                    <div></div>
                    @endif

                    <button type="submit" class="btn btn-primary fw-bold px-4 py-2.5" style="border-radius: 12px; background: var(--nx-forest); border-color: var(--nx-forest);">
                        <i class="fas fa-save me-1.5"></i> Perbarui Kredensial Kontak
                    </button>
                </div>
            </form>
        </div>

        <!-- ==================== TAB 3: KEAMANAN & SESI ==================== -->
        @elseif($activeTab === 'security')
        <div class="space-y-4">
            <!-- Form Ganti Kata Sandi -->
            <div class="card p-4 border-0 shadow-sm mb-4" style="border-radius: 18px; border: 1px solid var(--nx-border) !important;">
                <div class="d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom" style="border-color: var(--nx-border) !important;">
                    <div class="d-flex align-items-center gap-2.5">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: #fee2e2; color: #dc2626;">
                            <i class="fas fa-key"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0" style="color: var(--nx-dark); font-size: 1.05rem;">Ubah Kata Sandi Akun</h5>
                            <small class="text-muted" style="font-size: 0.78rem;">Pastikan menggunakan kombinasi karakter kuat minimal 6 karakter.</small>
                        </div>
                    </div>
                </div>

                <form action="{{ route('profile.update_password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-4 col-12">
                            <label class="form-label small fw-bold">Kata Sandi Lama <span class="text-danger">*</span></label>
                            <input type="password" name="current_password" required placeholder="Kata sandi saat ini"
                                   class="form-control" style="border-radius: 12px; font-size: 0.88rem;">
                        </div>
                        <div class="col-md-4 col-12">
                            <label class="form-label small fw-bold">Kata Sandi Baru <span class="text-danger">*</span></label>
                            <input type="password" name="password" required placeholder="Minimal 6 karakter"
                                   class="form-control" style="border-radius: 12px; font-size: 0.88rem;">
                        </div>
                        <div class="col-md-4 col-12">
                            <label class="form-label small fw-bold">Ulangi Sandi Baru <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" required placeholder="Konfirmasi kata sandi"
                                   class="form-control" style="border-radius: 12px; font-size: 0.88rem;">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4 pt-3 border-top" style="border-color: var(--nx-border) !important;">
                        <button type="submit" class="btn btn-danger fw-bold px-4 py-2.5" style="border-radius: 12px;">
                            <i class="fas fa-shield-alt me-1.5"></i> Perbarui Kata Sandi
                        </button>
                    </div>
                </form>
            </div>

            <!-- Sesi Aktif & Putuskan Sesi Lain -->
            <div class="card p-4 border-0 shadow-sm mb-4" style="border-radius: 18px; border: 1px solid var(--nx-border) !important;">
                <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom" style="border-color: var(--nx-border) !important;">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.95rem;">
                            <i class="fas fa-laptop me-2" style="color: var(--nx-forest);"></i>Sesi &amp; Perangkat yang Sedang Aktif
                        </h6>
                        <small class="text-muted" style="font-size: 0.76rem;">Informasi sesi browser yang saat ini terhubung ke akun Anda.</small>
                    </div>
                    <span class="badge rounded-pill px-2.5 py-1" style="background: #e8f5e9; color: #2e7d32; font-size: 0.72rem; font-weight: 700;">
                        <i class="fas fa-circle text-success me-1"></i> Perangkat Ini
                    </span>
                </div>

                <div class="p-3 rounded-3 bg-light border mb-3" style="font-size: 0.82rem;">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-desktop text-secondary fs-4"></i>
                                <div>
                                    <strong class="d-block" style="color: var(--nx-dark);">{{ $currentDevice }}</strong>
                                    <span class="text-muted" style="font-size: 0.72rem;">Alamat IP: <code>{{ $currentIp }}</code></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 fw-bold">Sesi Berjalan Aktif</span>
                        </div>
                    </div>
                </div>

                <form action="{{ route('profile.logout_other_devices') }}" method="POST" class="d-flex align-items-center gap-2 flex-wrap">
                    @csrf
                    <input type="password" name="confirm_password" required placeholder="Ketik kata sandi untuk putuskan sesi lain"
                           class="form-control form-control-sm" style="border-radius: 10px; max-width: 320px;">
                    <button type="submit" class="btn btn-outline-danger btn-sm fw-bold px-3 py-1.5" style="border-radius: 10px;">
                        <i class="fas fa-right-from-bracket me-1"></i> Logout Sesi Perangkat Lain
                    </button>
                </form>
            </div>

            <!-- Riwayat Login Terakhir -->
            <div class="card p-4 border-0 shadow-sm" style="border-radius: 18px; border: 1px solid var(--nx-border) !important;">
                <h6 class="fw-bold mb-3 text-dark" style="font-size: 0.95rem;">
                    <i class="fas fa-clock-rotate-left me-2" style="color: #0369a1;"></i>Riwayat Sesi Autentikasi Terakhir
                </h6>
                <div class="table-responsive border rounded-3" style="border-color: var(--nx-border) !important;">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.82rem;">
                        <thead class="table-light">
                            <tr>
                                <th class="py-2.5 px-3">Waktu Kejadian</th>
                                <th class="py-2.5 px-3">Jenis Aktivitas</th>
                                <th class="py-2.5 px-3">Alamat IP</th>
                                <th class="py-2.5 px-3">Perangkat / Browser</th>
                                <th class="py-2.5 px-3 text-end">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($loginHistory as $lh)
                            <tr>
                                <td class="py-2 px-3 fw-bold">{{ $lh->created_at->format('d/m/Y H:i:s') }} WIB</td>
                                <td class="py-2 px-3">
                                    <span class="badge rounded-pill px-2.5 py-0.5" style="background: {{ $lh->event === 'login' ? '#ecfdf5' : ($lh->event === 'logout' ? '#f1f5f9' : '#fef2f2') }}; color: {{ $lh->event === 'login' ? '#047857' : ($lh->event === 'logout' ? '#475569' : '#dc2626') }}; font-weight: 700; font-size: 0.68rem;">
                                        {{ strtoupper($lh->event) }}
                                    </span>
                                </td>
                                <td class="py-2 px-3"><code>{{ $lh->ip_address ?: '-' }}</code></td>
                                <td class="py-2 px-3 text-muted">{{ $lh->device_label }}</td>
                                <td class="py-2 px-3 text-end">
                                    <span class="badge rounded-pill px-2 py-0.5 {{ $lh->status === 'failed' ? 'bg-danger' : 'bg-success' }}" style="font-size: 0.65rem;">
                                        {{ strtoupper($lh->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-3 text-muted">Belum ada riwayat login tercatat.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ==================== TAB 4: PREFERENSI NOTIFIKASI ==================== -->
        @elseif($activeTab === 'notifications')
        <div class="card p-4 border-0 shadow-sm" style="border-radius: 18px; border: 1px solid var(--nx-border) !important;">
            <div class="d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom" style="border-color: var(--nx-border) !important;">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: #fef3c7; color: #b45309;">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0" style="color: var(--nx-dark); font-size: 1.05rem;">4. Pengaturan &amp; Preferensi Notifikasi</h5>
                        <small class="text-muted" style="font-size: 0.78rem;">Pilih jenis pemberitahuan dan kanal komunikasi yang ingin Anda terima.</small>
                    </div>
                </div>
            </div>

            <form action="{{ route('profile.update_notifications') }}" method="POST">
                @csrf
                @method('PUT')

                <h6 class="fw-bold text-uppercase mb-3" style="font-size: 0.8rem; color: var(--nx-forest); letter-spacing: 0.05em;">
                    Kategori Notifikasi Lingkungan
                </h6>

                <div class="space-y-3 mb-4">
                    <!-- 1. Pengumuman Lingkungan -->
                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-light border mb-2">
                        <div>
                            <strong class="d-block text-dark small">Pengumuman &amp; Peringatan Darurat</strong>
                            <span class="text-muted" style="font-size: 0.74rem;">Warta resmi RW, surat edaran, dan peringatan darurat lingkungan.</span>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" role="switch" name="notif_pengumuman" value="1" {{ ($notifPrefs['pengumuman'] ?? true) ? 'checked' : '' }}>
                        </div>
                    </div>

                    <!-- 2. Informasi Iuran & Kas -->
                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-light border mb-2">
                        <div>
                            <strong class="d-block text-dark small">Informasi Iuran Warga &amp; Kas</strong>
                            <span class="text-muted" style="font-size: 0.74rem;">Pemberitahuan tagihan bulanan, status kuitansi, dan laporan kas terbuka.</span>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" role="switch" name="notif_iuran" value="1" {{ ($notifPrefs['iuran'] ?? true) ? 'checked' : '' }}>
                        </div>
                    </div>

                    <!-- 3. Jadwal Kegiatan -->
                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-light border mb-2">
                        <div>
                            <strong class="d-block text-dark small">Jadwal Kegiatan &amp; Musyawarah</strong>
                            <span class="text-muted" style="font-size: 0.74rem;">Undangan kerja bakti, rapat warga RT/RW, dan kalender kegiatan lingkungan.</span>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" role="switch" name="notif_kegiatan" value="1" {{ ($notifPrefs['kegiatan'] ?? true) ? 'checked' : '' }}>
                        </div>
                    </div>

                    <!-- 4. Posyandu -->
                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-light border mb-2">
                        <div>
                            <strong class="d-block text-dark small">Informasi Posyandu &amp; Kesehatan</strong>
                            <span class="text-muted" style="font-size: 0.74rem;">Jadwal penimbangan balita, imunisasi, dan pemantauan gizi ibu hamil.</span>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" role="switch" name="notif_posyandu" value="1" {{ ($notifPrefs['posyandu'] ?? true) ? 'checked' : '' }}>
                        </div>
                    </div>

                    <!-- 5. Status Surat -->
                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-light border mb-2">
                        <div>
                            <strong class="d-block text-dark small">Status Pengajuan Surat Pengantar</strong>
                            <span class="text-muted" style="font-size: 0.74rem;">Progres verifikasi permohonan surat pengantar dari RT dan RW.</span>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" role="switch" name="notif_surat" value="1" {{ ($notifPrefs['surat'] ?? true) ? 'checked' : '' }}>
                        </div>
                    </div>

                    <!-- 6. Pengaduan Warga -->
                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-light border mb-2">
                        <div>
                            <strong class="d-block text-dark small">Tindak Lanjut Pengaduan &amp; Fasilitas</strong>
                            <span class="text-muted" style="font-size: 0.74rem;">Status penanganan keluhan fasilitas umum dan permohonan peminjaman sarana.</span>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" role="switch" name="notif_pengaduan" value="1" {{ ($notifPrefs['pengaduan'] ?? true) ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold text-uppercase mb-3 mt-4 pt-3 border-top" style="font-size: 0.8rem; color: var(--nx-forest); letter-spacing: 0.05em; border-color: var(--nx-border) !important;">
                    Kanal Pengiriman Notifikasi
                </h6>

                <div class="row g-3 mb-4">
                    <div class="col-md-6 col-12">
                        <div class="p-3 rounded-3 bg-light border d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2.5">
                                <i class="fas fa-mobile-screen text-primary fs-4"></i>
                                <div>
                                    <strong class="d-block small text-dark">Dalam Aplikasi (In-App)</strong>
                                    <span class="text-muted" style="font-size: 0.72rem;">Loncat langsung ke dashboard NexaNest</span>
                                </div>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" role="switch" name="channel_in_app" value="1" {{ ($notifPrefs['in_app'] ?? true) ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        <div class="p-3 rounded-3 bg-light border d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2.5">
                                <i class="fas fa-envelope text-success fs-4"></i>
                                <div>
                                    <strong class="d-block small text-dark">Email Resmi</strong>
                                    <span class="text-muted" style="font-size: 0.72rem;">Kirim ke {{ $user->email }}</span>
                                </div>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" role="switch" name="channel_email" value="1" {{ ($notifPrefs['email'] ?? true) ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4 pt-3 border-top" style="border-color: var(--nx-border) !important;">
                    <button type="submit" class="btn btn-primary fw-bold px-4 py-2.5" style="border-radius: 12px; background: var(--nx-forest); border-color: var(--nx-forest);">
                        <i class="fas fa-save me-1.5"></i> Simpan Preferensi Notifikasi
                    </button>
                </div>
            </form>
        </div>

        <!-- ==================== TAB 5: AKTIVITAS SAYA ==================== -->
        @elseif($activeTab === 'activity')
        <div class="card p-4 border-0 shadow-sm" style="border-radius: 18px; border: 1px solid var(--nx-border) !important;">
            <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom flex-wrap gap-2" style="border-color: var(--nx-border) !important;">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: #e0f2fe; color: #0369a1;">
                        <i class="fas fa-clock-rotate-left"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0" style="color: var(--nx-dark); font-size: 1.05rem;">5. Riwayat Aktivitas Akun Saya</h5>
                        <small class="text-muted" style="font-size: 0.78rem;">Catatan kronologis seluruh tindakan, permohonan layanan, dan interaksi sistem Anda.</small>
                    </div>
                </div>
                <span class="badge rounded-pill px-3 py-1.5" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-weight: 700; font-size: 0.74rem;">
                    Total {{ $activities->total() }} Catatan
                </span>
            </div>

            <!-- Filter Aktivitas Akun -->
            <form action="{{ route('profile.edit') }}" method="GET" class="row g-2 mb-4 p-2 rounded-3 bg-light border align-items-center">
                <input type="hidden" name="tab" value="activity">
                <div class="col-md-5 col-12">
                    <select name="act_category" class="form-select form-select-sm" style="border-radius: 8px;">
                        <option value="semua">Semua Kategori Aktivitas</option>
                        <option value="login" {{ request('act_category') === 'login' ? 'selected' : '' }}>Sesi Login &amp; Logout</option>
                        <option value="surat" {{ request('act_category') === 'surat' ? 'selected' : '' }}>Permohonan Surat</option>
                        <option value="iuran" {{ request('act_category') === 'iuran' ? 'selected' : '' }}>Pembayaran Iuran</option>
                        <option value="pengaduan" {{ request('act_category') === 'pengaduan' ? 'selected' : '' }}>Pengaduan &amp; Fasilitas</option>
                        <option value="pengumuman" {{ request('act_category') === 'pengumuman' ? 'selected' : '' }}>Pusat Pengumuman</option>
                        <option value="profil" {{ request('act_category') === 'profil' ? 'selected' : '' }}>Pembaruan Profil</option>
                    </select>
                </div>
                <div class="col-md-5 col-8">
                    <input type="date" name="act_date" value="{{ request('act_date') }}" class="form-control form-control-sm" style="border-radius: 8px;">
                </div>
                <div class="col-md-2 col-4 d-flex gap-1">
                    <button type="submit" class="btn btn-primary btn-sm fw-bold w-100" style="border-radius: 8px; background: var(--nx-forest); border-color: var(--nx-forest);">
                        Filter
                    </button>
                    @if(request()->anyFilled(['act_category', 'act_date']))
                    <a href="{{ route('profile.edit', ['tab' => 'activity']) }}" class="btn btn-outline-secondary btn-sm" style="border-radius: 8px;" title="Reset Filter">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                    @endif
                </div>
            </form>

            <!-- Timeline Aktivitas Personal -->
            <div class="ps-3 border-start ms-2 mb-3" style="border-color: var(--nx-border) !important; border-width: 2px !important;">
                @forelse($activities as $act)
                    @php
                        $eb = $act->event_badge;
                    @endphp
                    <div class="position-relative mb-3.5 ps-3">
                        <div class="position-absolute rounded-circle"
                             style="left: -23px; top: 4px; width: 14px; height: 14px; background: #fff; border: 3px solid {{ $eb['color'] }};">
                        </div>

                        <div class="card p-3 border-0 shadow-xs" style="border-radius: 14px; background: #fafafa; border: 1px solid var(--nx-border) !important;">
                            <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mb-1">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="badge rounded-pill px-2.5 py-0.5 border"
                                          style="background: {{ $eb['bg'] }}; color: {{ $eb['color'] }}; font-size: 0.68rem; font-weight: 700;">
                                        <i class="{{ $eb['icon'] }} me-1"></i> {{ $eb['label'] }}
                                    </span>
                                    <span class="fw-bold" style="color: var(--nx-forest); font-size: 0.8rem;">
                                        {{ $act->module }}
                                    </span>
                                </div>
                                <span class="text-muted small" style="font-size: 0.74rem;">
                                    <i class="far fa-clock me-1"></i> {{ $act->created_at->format('d/m/Y H:i') }} WIB ({{ $act->created_at->diffForHumans() }})
                                </span>
                            </div>

                            <p class="mb-1 text-dark small" style="line-height: 1.45; font-size: 0.82rem;">
                                {{ $act->description }}
                            </p>

                            <div class="text-muted small mt-1" style="font-size: 0.7rem;">
                                <i class="fas fa-network-wired me-1"></i> {{ $act->ip_address ?: '-' }} · {{ $act->device_label }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <p class="text-muted small mb-0">Belum ada catatan aktivitas pada akun Anda.</p>
                    </div>
                @endforelse
            </div>

            @if($activities->hasPages())
            <div class="pt-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2" style="border-color: var(--nx-border) !important;">
                <small class="text-muted">Menampilkan {{ $activities->firstItem() }} - {{ $activities->lastItem() }} dari {{ $activities->total() }} catatan</small>
                {{ $activities->links() }}
            </div>
            @endif
        </div>

        <!-- ==================== TAB 6: PENGATURAN LAINNYA & PRIVASI ==================== -->
        @elseif($activeTab === 'preferences')
        <div class="space-y-4">
            <!-- Form Tema & Privasi -->
            <div class="card p-4 border-0 shadow-sm mb-4" style="border-radius: 18px; border: 1px solid var(--nx-border) !important;">
                <div class="d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom" style="border-color: var(--nx-border) !important;">
                    <div class="d-flex align-items-center gap-2.5">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: #e0e7ff; color: #4338ca;">
                            <i class="fas fa-sliders"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0" style="color: var(--nx-dark); font-size: 1.05rem;">6. Preferensi Tampilan, Privasi &amp; Bantuan</h5>
                            <small class="text-muted" style="font-size: 0.78rem;">Sesuaikan pengalaman visual antarmuka dan kendali visibilitas akun Anda.</small>
                        </div>
                    </div>
                </div>

                <form action="{{ route('profile.update_preferences') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Preferensi Tema -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold" style="color: var(--nx-dark);">Mode Tampilan Tema</label>
                        <div class="row g-2">
                            <div class="col-4">
                                <label class="card p-3 text-center cursor-pointer text-decoration-none h-100" style="border-radius: 12px; border: 1px solid {{ old('theme_preference', $user->theme_preference) === 'light' ? 'var(--nx-forest)' : 'var(--nx-border)' }}; background: #fff; cursor: pointer;">
                                    <i class="fas fa-sun text-warning fs-4 mb-1"></i>
                                    <span class="small fw-bold d-block text-dark">Mode Terang</span>
                                    <input type="radio" name="theme_preference" value="light" {{ old('theme_preference', $user->theme_preference) === 'light' ? 'checked' : '' }} class="form-check-input mx-auto mt-1">
                                </label>
                            </div>
                            <div class="col-4">
                                <label class="card p-3 text-center cursor-pointer text-decoration-none h-100" style="border-radius: 12px; border: 1px solid {{ old('theme_preference', $user->theme_preference) === 'dark' ? 'var(--nx-forest)' : 'var(--nx-border)' }}; background: #263449; color: #fff; cursor: pointer;">
                                    <i class="fas fa-moon text-info fs-4 mb-1"></i>
                                    <span class="small fw-bold d-block text-white">Mode Gelap</span>
                                    <input type="radio" name="theme_preference" value="dark" {{ old('theme_preference', $user->theme_preference) === 'dark' ? 'checked' : '' }} class="form-check-input mx-auto mt-1">
                                </label>
                            </div>
                            <div class="col-4">
                                <label class="card p-3 text-center cursor-pointer text-decoration-none h-100" style="border-radius: 12px; border: 1px solid {{ old('theme_preference', $user->theme_preference) === 'system' ? 'var(--nx-forest)' : 'var(--nx-border)' }}; background: #f8fafc; cursor: pointer;">
                                    <i class="fas fa-desktop text-secondary fs-4 mb-1"></i>
                                    <span class="small fw-bold d-block text-dark">Ikuti Sistem</span>
                                    <input type="radio" name="theme_preference" value="system" {{ old('theme_preference', $user->theme_preference) === 'system' ? 'checked' : '' }} class="form-check-input mx-auto mt-1">
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Preferensi Privasi -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold" style="color: var(--nx-dark);">Kendali Privasi Kontak</label>
                        <div class="p-3 rounded-3 bg-light border mb-2">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <strong class="d-block small text-dark">Samarkan Nomor WhatsApp di Direktori UMKM/Warga</strong>
                                    <span class="text-muted" style="font-size: 0.72rem;">Nomor telepon hanya dapat dilihat langsung oleh pengurus resmi RT/RW.</span>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch" name="hide_phone" value="1" {{ ($privacyPrefs['hide_phone'] ?? false) ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>

                        <div class="p-3 rounded-3 bg-light border">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <strong class="d-block small text-dark">Profil Terlihat di Lingkup RT</strong>
                                    <span class="text-muted" style="font-size: 0.72rem;">Nama Anda muncul pada daftar warga terdaftar RT setempat.</span>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch" name="profile_visible" value="1" {{ ($privacyPrefs['profile_visible'] ?? true) ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end pt-3 border-top" style="border-color: var(--nx-border) !important;">
                        <button type="submit" class="btn btn-primary fw-bold px-4 py-2.5" style="border-radius: 12px; background: var(--nx-forest); border-color: var(--nx-forest);">
                            <i class="fas fa-save me-1.5"></i> Simpan Preferensi
                        </button>
                    </div>
                </form>
            </div>

            <!-- Pusat Bantuan & Informasi Versi -->
            <div class="card p-4 border-0 shadow-sm mb-4" style="border-radius: 18px; border: 1px solid var(--nx-border) !important;">
                <h6 class="fw-bold mb-3 text-dark" style="font-size: 0.95rem;">
                    <i class="fas fa-circle-question me-2" style="color: var(--nx-forest);"></i>Pusat Bantuan &amp; Informasi Sistem
                </h6>
                <div class="row g-3">
                    <div class="col-md-6 col-12">
                        <div class="p-3 rounded-3 border bg-light h-100">
                            <strong class="d-block small text-dark mb-1"><i class="fas fa-headset text-success me-1"></i> Layanan Bantuan Warga RW 018</strong>
                            <p class="text-muted small mb-2" style="font-size: 0.76rem;">Butuh bantuan administratif atau kendala aplikasi? Hubungi narahubung Sekretariat RW 018.</p>
                            <a href="https://wa.me/6281234567890?text=Halo%20Admin%20RW%20018,%20saya%20membutuhkan%20bantuan%20terkait%20akun%20NexaNest." target="_blank" class="btn btn-sm btn-success fw-bold px-3" style="border-radius: 8px;">
                                <i class="fab fa-whatsapp me-1"></i> Kontak WhatsApp Pengurus
                            </a>
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        <div class="p-3 rounded-3 border bg-light h-100">
                            <strong class="d-block small text-dark mb-1"><i class="fas fa-code-branch text-primary me-1"></i> Versi Perangkat Lunak</strong>
                            <p class="text-muted small mb-1" style="font-size: 0.76rem;">Platform: <strong>NexaNest Smart &amp; Green Community</strong></p>
                            <span class="badge rounded-pill px-2.5 py-1" style="background: var(--nx-mint); color: var(--nx-forest); font-weight: 700; font-size: 0.72rem;">
                                Edisi Terpadu v2.4 LTS (Active)
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Deaktivasi Akun & Logout -->
            <div class="card p-4 border-0 shadow-sm" style="border-radius: 18px; border: 1px solid #fecaca !important; background: #fff5f5;">
                <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                    <div>
                        <h6 class="fw-bold text-danger mb-1" style="font-size: 0.95rem;">
                            <i class="fas fa-triangle-exclamation me-1.5"></i> Penonaktifan Akun Pengguna
                        </h6>
                        <p class="text-muted small mb-0" style="font-size: 0.76rem; line-height: 1.45;">
                            Pengajuan penonaktifan akun akan diteruskan ke Pengurus RW 018. Sesuai regulasi kependudukan lingkungan, catatan transaksi iuran dan arsip surat tetap disimpan demi akuntabilitas.
                        </p>
                        @if($user->deactivation_requested_at)
                        <div class="mt-2 text-danger small fw-bold">
                            <i class="fas fa-clock me-1"></i> Permohonan Anda telah diajukan pada {{ $user->deactivation_requested_at->format('d/m/Y') }} (Status: Menunggu Evaluasi Pengurus RW).
                        </div>
                        @endif
                    </div>
                    <div class="shrink-0 d-flex gap-2">
                        @if(!$user->deactivation_requested_at)
                        <button type="button" class="btn btn-outline-danger btn-sm fw-bold px-3 py-2" data-bs-toggle="modal" data-bs-target="#modalDeaktivasi" style="border-radius: 10px;">
                            Ajukan Penonaktifan
                        </button>
                        @endif
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm fw-bold px-3 py-2" style="border-radius: 10px;">
                                <i class="fas fa-right-from-bracket me-1"></i> Logout Akun
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- ==================== MODAL DEAKTIVASI AKUN ==================== -->
<div class="modal fade" id="modalDeaktivasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-bottom px-4 py-3" style="border-color: var(--nx-border) !important;">
                <h6 class="modal-title fw-bold text-danger mb-0">
                    <i class="fas fa-triangle-exclamation me-1.5"></i> Permohonan Penonaktifan Akun
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('profile.request_deactivation') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p class="small text-muted mb-3" style="font-size: 0.8rem; line-height: 1.45;">
                        Pengurus RW 018 akan memverifikasi permohonan penonaktifan akses akun Anda. Riwayat kependudukan, mutasi, dan pembayaran iuran akan tetap diarsipkan secara aman.
                    </p>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Alasan Penonaktifan Akun <span class="text-danger">*</span></label>
                        <textarea name="reason" rows="3" required placeholder="Contoh: Pindah domisili permanen ke luar kota..."
                                  class="form-control" style="border-radius: 10px; font-size: 0.85rem;"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Konfirmasi Kata Sandi Akun <span class="text-danger">*</span></label>
                        <input type="password" name="auth_password" required placeholder="Masukkan kata sandi Anda"
                               class="form-control" style="border-radius: 10px; font-size: 0.85rem;">
                    </div>
                </div>
                <div class="modal-footer border-top px-4 py-2.5 d-flex justify-content-between" style="border-color: var(--nx-border) !important;">
                    <button type="button" class="btn btn-light fw-bold px-3 py-2" data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
                    <button type="submit" class="btn btn-danger fw-bold px-4 py-2" style="border-radius: 10px;">Kirim Pengajuan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Live preview uploaded avatar photo
    const inputPhoto = document.getElementById('inputPhotoForm');
    const previewForm = document.getElementById('avatarPreviewForm');
    const previewSidebar = document.getElementById('avatarPreviewSidebar');
    if (inputPhoto) {
        inputPhoto.addEventListener('change', function (e) {
            const file = e.target.files?.[0];
            if (file) {
                const url = URL.createObjectURL(file);
                if (previewForm) previewForm.src = url;
                if (previewSidebar) previewSidebar.src = url;
            }
        });
    }

    // Toggle Masking for NIK
    function toggleMaskNik() {
        const input = document.getElementById('nikInput');
        const icon = document.getElementById('nikToggleIcon');
        if (!input) return;

        const isMasked = input.value === input.dataset.masked;
        if (isMasked) {
            input.value = input.dataset.full;
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.value = input.dataset.masked;
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
@endpush
@endsection
