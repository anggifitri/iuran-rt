@extends('layouts.app')

@section('title', 'Profil Admin RT')

@section('content')
<style>
    .profile-headline {
        background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
        border: 1px solid rgba(148, 163, 184, 0.18);
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.18);
    }

    .profile-summary {
        background: linear-gradient(180deg, #0f172a 0%, #111827 100%);
        border: 1px solid rgba(96, 165, 250, 0.18);
        color: #f8fafc;
    }

    .profile-summary .table th {
        color: #94a3b8;
        font-weight: 600;
        width: 34%;
        border: none;
        background: transparent;
    }

    .profile-summary .table td {
        color: #e2e8f0;
        border: none;
        background: transparent;
    }

    .profile-summary .table-responsive {
        background: rgba(15, 23, 42, 0.9);
        border: 1px solid rgba(148, 163, 184, 0.12);
        border-radius: 1rem;
        padding: 1rem;
    }

    .profile-summary .table-responsive .table {
        margin-bottom: 0;
    }

    .profile-form {
        background: rgba(15, 23, 42, 0.95);
        border: 1px solid rgba(148, 163, 184, 0.18);
    }

    .profile-form .form-control,
    .profile-form .form-select,
    .profile-form textarea {
        background: #111827;
        border-color: rgba(148, 163, 184, 0.24);
        color: #e2e8f0;
        box-shadow: none;
    }

    .profile-form .form-control:focus,
    .profile-form .form-select:focus,
    .profile-form textarea:focus {
        background: #111827;
        border-color: #3b82f6;
        color: #f8fafc;
        box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.12);
    }

    .profile-form .form-control[type="file"] {
        background: #0f172a;
        border-color: rgba(148, 163, 184, 0.24);
        color: #e2e8f0;
    }

    .profile-form .form-label,
    .profile-form .form-text {
        color: #cbd5e1;
    }

    .profile-card-title {
        color: #f8fafc;
    }

    .profile-badge {
        background: rgba(59, 130, 246, 0.14);
        color: #60a5fa;
        border-radius: 9999px;
        padding: 0.3rem 0.75rem;
        font-size: 0.8rem;
    }
</style>

<div class="row justify-content-center">
    <div class="col-xl-10">
        <div class="d-flex justify-content-between align-items-center mb-4 profile-headline p-4 rounded-4">
            <div>
                <p class="small mb-1 text-secondary">Profil Admin RT</p>
                <h4 class="fw-bold mb-1 profile-card-title">{{ $user->name }}</h4>
                <p class="mb-0 text-white-50">RT {{ $user->rt_number ?? '-' }} • {{ ucfirst($user->role) }}</p>
            </div>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-light">Kembali ke Dashboard</a>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card profile-summary p-4 rounded-4 shadow-sm">
                    <div class="text-center">
                        <img src="{{ $user->profile_photo_url }}" alt="Foto Profil" class="rounded-circle mb-3" width="150" height="150" style="object-fit: cover; border: 4px solid rgba(59, 130, 246, 0.25);">
                        <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                        <div class="profile-badge mb-2">{{ ucfirst($user->role) }} RT {{ $user->rt_number ?? '-' }}</div>
                        <p class="small text-slate-400 mb-4">Akun dibuat pada {{ $user->created_at->format('d M Y') }}</p>
                    </div>

                    <div>
                        <h6 class="fw-semibold mb-3 text-white">Informasi Lengkap</h6>
                        <div class="table-responsive">
                            <table class="table table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <th>Email</th>
                                        <td>{{ $user->email }}</td>
                                    </tr>
                                    <tr>
                                        <th>No. KK</th>
                                        <td>{{ $user->no_kk ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>NIK</th>
                                        <td>{{ $user->nik ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>No. Telepon</th>
                                        <td>{{ $user->phone ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Alamat</th>
                                        <td>{{ $user->address ?? '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card profile-form p-4 rounded-4 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="fw-bold mb-1 text-white">Ubah Profil</h5>
                            <p class="small mb-0 text-white-50">Perbarui informasi akun dan foto profil admin RT.</p>
                        </div>
                        <span class="badge bg-primary bg-opacity-15 text-primary">Edit Data</span>
                    </div>

                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror bg-slate-900 border-slate-700 text-white" id="name" name="name" value="{{ old('name', $user->name) }}">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror bg-slate-900 border-slate-700 text-white" id="email" name="email" value="{{ old('email', $user->email) }}">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label">No. Telepon</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror bg-slate-900 border-slate-700 text-white" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="rt_number" class="form-label">No. RT</label>
                                <input type="text" class="form-control @error('rt_number') is-invalid @enderror bg-slate-900 border-slate-700 text-white" id="rt_number" name="rt_number" value="{{ old('rt_number', $user->rt_number) }}">
                                @error('rt_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="no_kk" class="form-label">No. KK</label>
                                <input type="text" class="form-control @error('no_kk') is-invalid @enderror bg-slate-900 border-slate-700 text-white" id="no_kk" name="no_kk" value="{{ old('no_kk', $user->no_kk) }}">
                                @error('no_kk')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="nik" class="form-label">NIK</label>
                                <input type="text" class="form-control @error('nik') is-invalid @enderror bg-slate-900 border-slate-700 text-white" id="nik" name="nik" value="{{ old('nik', $user->nik) }}">
                                @error('nik')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label for="address" class="form-label">Alamat</label>
                                <textarea class="form-control @error('address') is-invalid @enderror bg-slate-900 border-slate-700 text-white" id="address" name="address" rows="3">{{ old('address', $user->address) }}</textarea>
                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label for="profile_photo" class="form-label">Foto Profil</label>
                                <input type="file" class="form-control @error('profile_photo') is-invalid @enderror bg-slate-900 border-slate-700 text-white" id="profile_photo" name="profile_photo" accept="image/png, image/jpeg">
                                @error('profile_photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-text">Unggah foto JPG/PNG, maksimal 2MB. Biarkan kosong jika tidak ingin mengganti.</div>
                            </div>

                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
