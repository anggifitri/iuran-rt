@extends('layouts.app')

@section('title', 'Profil Pengguna')

@section('content')
@php
    $roleTitle = ucfirst($user->role);
    if ($user->role === 'admin') {
        $roleTitle = $user->rt_number === '000' ? 'Admin RW 018' : 'Admin RT ' . $user->rt_number;
    } elseif ($user->role === 'bendahara') {
        $roleTitle = 'Bendahara RT ' . $user->rt_number;
    } else {
        $roleTitle = ucfirst($user->role) . ' RT ' . $user->rt_number;
    }
@endphp

<div class="row justify-content-center">
    <div class="col-xl-10">
        <div class="d-flex justify-content-between align-items-center mb-4 card p-4 border-0 shadow-sm rounded-4" style="background: linear-gradient(135deg, var(--bg-card) 0%, rgba(59, 130, 246, 0.05) 100%); border: 1px solid var(--border-color) !important;">
            <div>
                <p class="small mb-1 text-muted">Profil Pengguna</p>
                <h4 class="fw-bold mb-1" style="color: var(--text-main);">{{ $user->name }}</h4>
                <p class="mb-0" style="color: var(--text-muted);">{{ $roleTitle }}</p>
            </div>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-primary px-4 rounded-pill">Kembali ke Dashboard</a>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card p-4 rounded-4 shadow-sm border-0 h-100" style="background-color: var(--bg-card); border: 1px solid var(--border-color) !important;">
                    <div class="text-center mb-4">
                        <img src="{{ $user->profile_photo_url }}" alt="Foto Profil" class="rounded-circle mb-3 shadow-sm" width="150" height="150" style="object-fit: cover; border: 4px solid var(--primary);">
                        <h5 class="fw-bold mb-1" style="color: var(--text-main);">{{ $user->name }}</h5>
                        <div class="badge bg-primary bg-opacity-10 text-primary rounded-pill mb-2 px-3 py-2">{{ $roleTitle }}</div>
                        <p class="small mb-0" style="color: var(--text-muted);">Akun dibuat pada {{ $user->created_at->format('d M Y') }}</p>
                    </div>

                    <div>
                        <h6 class="fw-semibold mb-3" style="color: var(--text-main);">Informasi Lengkap</h6>
                        <div class="table-responsive rounded-3 p-3 shadow-sm" style="background-color: rgba(59, 130, 246, 0.05); border: 1px solid var(--border-color);">
                            <table class="table table-borderless mb-0" style="background-color: transparent;">
                                <tbody>
                                    <tr>
                                        <th style="color: var(--text-muted); font-weight: 600; width: 35%; background: transparent;">Email</th>
                                        <td style="color: var(--text-main); background: transparent;">{{ $user->email }}</td>
                                    </tr>
                                    <tr>
                                        <th style="color: var(--text-muted); font-weight: 600; background: transparent;">No. KK</th>
                                        <td style="color: var(--text-main); background: transparent;">{{ $user->no_kk ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th style="color: var(--text-muted); font-weight: 600; background: transparent;">NIK</th>
                                        <td style="color: var(--text-main); background: transparent;">{{ $user->nik ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th style="color: var(--text-muted); font-weight: 600; background: transparent;">No. Telepon</th>
                                        <td style="color: var(--text-main); background: transparent;">{{ $user->phone ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th style="color: var(--text-muted); font-weight: 600; background: transparent;">Alamat</th>
                                        <td style="color: var(--text-main); background: transparent;">{{ $user->address ?? '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card p-4 rounded-4 shadow-sm border-0 h-100" style="background-color: var(--bg-card); border: 1px solid var(--border-color) !important;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="fw-bold mb-1" style="color: var(--text-main);">Ubah Profil</h5>
                            <p class="small mb-0" style="color: var(--text-muted);">Perbarui informasi akun dan foto profil Anda.</p>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2"><i class="fas fa-edit me-1"></i> Edit Data</span>
                    </div>

                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label" style="color: var(--text-muted);">Nama Lengkap</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label" style="color: var(--text-muted);">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label" style="color: var(--text-muted);">No. Telepon</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="rt_number" class="form-label" style="color: var(--text-muted);">No. RT</label>
                                <input type="text" class="form-control @error('rt_number') is-invalid @enderror" id="rt_number" name="rt_number" value="{{ old('rt_number', $user->rt_number) }}">
                                @error('rt_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="no_kk" class="form-label" style="color: var(--text-muted);">No. KK</label>
                                <input type="text" class="form-control @error('no_kk') is-invalid @enderror" id="no_kk" name="no_kk" value="{{ old('no_kk', $user->no_kk) }}">
                                @error('no_kk')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="nik" class="form-label" style="color: var(--text-muted);">NIK</label>
                                <input type="text" class="form-control @error('nik') is-invalid @enderror" id="nik" name="nik" value="{{ old('nik', $user->nik) }}">
                                @error('nik')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label for="address" class="form-label" style="color: var(--text-muted);">Alamat</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', $user->address) }}</textarea>
                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label for="profile_photo" class="form-label" style="color: var(--text-muted);">Foto Profil</label>
                                <input type="file" class="form-control @error('profile_photo') is-invalid @enderror" id="profile_photo" name="profile_photo" accept="image/png, image/jpeg">
                                @error('profile_photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-text" style="color: var(--text-muted);">Unggah foto JPG/PNG, maksimal 2MB. Biarkan kosong jika tidak ingin mengganti.</div>
                            </div>

                            <div class="col-12 text-end mt-4">
                                <button type="submit" class="btn btn-primary px-4 rounded-pill fw-medium shadow-sm"><i class="fas fa-save me-2"></i> Simpan Perubahan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
