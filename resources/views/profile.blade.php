@extends('layouts.app')

@section('title', 'Pengaturan Profil — NexaNest')

@section('content')
<div class="nx-page-header">
    <div>
        <span class="badge rounded-pill mb-2 px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
            <i class="fas fa-user-shield me-1"></i> Identitas &amp; Akun Pengguna
        </span>
        <h1 class="nx-page-title">Pengaturan Profil Saya</h1>
        <p class="nx-page-subtitle">Kelola foto profil, nama tampilan, dan alamat email akun NexaNest Anda.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('password.request') }}" class="btn btn-outline-primary">
            <i class="fas fa-key me-2"></i>Ganti Password
        </a>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Dashboard
        </a>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger mb-4">
        <div class="fw-bold mb-1"><i class="fas fa-circle-exclamation me-1"></i> Terdapat kesalahan validasi:</div>
        <ul class="mb-0 ps-3 small">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-4">
    <!-- KOLOM KIRI: RINGKASAN KARTU PROFIL -->
    <div class="col-lg-4">
        <div class="card p-4 text-center h-100">
            <div class="position-relative d-inline-block mx-auto mb-3">
                <img id="avatarPreview"
                     src="{{ $user->profile_photo_url }}"
                     alt="Avatar {{ $user->name }}"
                     class="rounded-circle shadow-sm"
                     width="124"
                     height="124"
                     style="object-fit: cover; border: 4px solid var(--nx-mint);">
                <span class="position-absolute bottom-0 end-0 rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm"
                      style="width: 34px; height: 34px; background: var(--nx-forest); color: #fff;">
                    <i class="fas fa-camera small"></i>
                </span>
            </div>

            <h5 class="fw-bold mb-1" style="color: var(--nx-text);">{{ $user->name }}</h5>
            <p class="text-muted small mb-3">{{ $user->email }}</p>

            <div class="d-flex justify-content-center gap-2 flex-wrap mb-4">
                <span class="badge rounded-pill px-3 py-2" style="background: var(--nx-mint); color: var(--nx-forest); font-weight: 700;">
                    <i class="fas fa-id-badge me-1"></i>
                    {{ $user->isAdminRw() ? 'Admin RW 018' : ($user->isAdminRt() ? 'Admin RT ' . $user->rt_number : ucfirst($user->role)) }}
                </span>
                <span class="badge rounded-pill px-3 py-2" style="background: var(--nx-powder); color: #2B5F8E; font-weight: 700;">
                    <i class="fas fa-map-pin me-1"></i>
                    {{ $user->rt_number === '000' ? 'Lingkup RW 018' : 'Wilayah RT ' . ($user->rt_number ?? '-') }}
                </span>
            </div>

            <div class="p-3 rounded-4 text-start mt-auto" style="background: var(--nx-mint-soft); border: 1px solid var(--nx-border);">
                <div class="fw-bold small mb-1" style="color: var(--nx-forest);">
                    <i class="fas fa-shield-halved me-1"></i> Keamanan Wilayah RT/RW
                </div>
                <p class="small text-muted mb-0" style="font-size: 0.78rem; line-height: 1.5;">
                    Nomor wilayah RT dikunci otomatis sesuai hak akses akun untuk menjaga integritas data warga dan laporan kas.
                </p>
            </div>
        </div>
    </div>

    <!-- KOLOM KANAN: FORM UPDATE PROFIL -->
    <div class="col-lg-8">
        <div class="card p-4">
            <div class="d-flex align-items-center gap-2 pb-3 mb-4 border-bottom">
                <span class="rounded-3 d-inline-flex align-items-center justify-content-center"
                      style="width: 38px; height: 38px; background: var(--nx-mint); color: var(--nx-forest);">
                    <i class="fas fa-user-pen"></i>
                </span>
                <div>
                    <h5 class="fw-bold mb-0" style="font-size: 1.05rem;">Formulir Pembaruan Profil</h5>
                    <small class="text-muted">Perubahan akan langsung diterapkan pada akun Anda</small>
                </div>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-12">
                        <label for="profile_photo" class="form-label">Unggah Foto Profil Baru</label>
                        <input id="profile_photo"
                               name="profile_photo"
                               type="file"
                               accept="image/*"
                               class="form-control @error('profile_photo') is-invalid @enderror">
                        <small class="text-muted" style="font-size: 0.76rem;">Format JPG, PNG, atau WEBP (Maksimal 2MB). Kosongkan jika tidak ingin mengganti foto.</small>
                        @error('profile_photo')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input id="name"
                               name="name"
                               type="text"
                               value="{{ old('name', $user->name) }}"
                               required
                               class="form-control @error('name') is-invalid @enderror">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label">Alamat Email <span class="text-danger">*</span></label>
                        <input id="email"
                               name="email"
                               type="email"
                               value="{{ old('email', $user->email) }}"
                               required
                               class="form-control @error('email') is-invalid @enderror">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="rt_number" class="form-label">Nomor Wilayah RT (Terkunci Sistem)</label>
                        <input id="rt_number"
                               name="rt_number"
                               type="text"
                               value="{{ old('rt_number', $user->rt_number) }}"
                               readonly
                               class="form-control">
                        <small class="text-muted" style="font-size: 0.75rem;">Hubungi Admin RW apabila terdapat perubahan domisili RT.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Peran Sistem (Role)</label>
                        <input type="text"
                               value="{{ strtoupper($user->role) }}"
                               readonly
                               class="form-control">
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary px-4">Batal</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-2"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('profile_photo');
    const preview = document.getElementById('avatarPreview');
    if (fileInput && preview) {
        fileInput.addEventListener('change', function (e) {
            const file = e.target.files?.[0];
            if (file) {
                preview.src = URL.createObjectURL(file);
            }
        });
    }
});
</script>
@endpush
