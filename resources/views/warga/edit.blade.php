@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-white fw-bold">Edit Data Warga</h2>
        <a href="{{ route('warga.index') }}" class="btn btn-secondary">
            Batal
        </a>
    </div>

    <div class="card shadow border-0 bg-dark text-white">
        <div class="card-body p-4">
            <form action="{{ route('warga.update', $warga->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label text-white">Nama Lengkap</label>
                    <input type="text" name="nama" value="{{ old('nama', $warga->nama) }}" required class="form-control bg-secondary text-white border-0" placeholder="Masukkan nama lengkap">
                </div>

                <div class="mb-3">
                    <label class="form-label text-white">Blok Rumah (Contoh: A1/12)</label>
                    <input type="text" name="blok_rumah" value="{{ old('blok_rumah', $warga->blok_rumah) }}" required class="form-control bg-secondary text-white border-0" placeholder="Contoh: A1/12">
                </div>

                <div class="mb-3">
                    <label class="form-label text-white">No. KK</label>
                    <input type="text" name="no_kk" value="{{ old('no_kk', $warga->no_kk) }}" class="form-control bg-secondary text-white border-0" placeholder="Masukkan No. KK">
                </div>

                <div class="mb-3">
                    <label class="form-label text-white">NIK</label>
                    <input type="text" name="nik" value="{{ old('nik', $warga->nik) }}" class="form-control bg-secondary text-white border-0" placeholder="Masukkan NIK">
                </div>

                <div class="mb-3">
                    <label class="form-label text-white">Gender</label>
                    <select name="gender" class="form-select bg-secondary text-white border-0">
                        <option value="">-- Pilih Gender --</option>
                        <option value="L" {{ old('gender', $warga->gender) == 'L' ? 'selected' : '' }}>L - Laki-laki</option>
                        <option value="P" {{ old('gender', $warga->gender) == 'P' ? 'selected' : '' }}>P - Perempuan</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label text-white">Foto Profil</label>
                    <input type="file" name="profile_photo" class="form-control @error('profile_photo') is-invalid @enderror bg-secondary text-white border-0" accept="image/png, image/jpeg">
                    @error('profile_photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="form-label text-white">Nomor HP</label>
                    <input type="text" name="nomor_hp" value="{{ old('nomor_hp', $warga->nomor_hp) }}" class="form-control bg-secondary text-white border-0" placeholder="Masukkan nomor HP (Boleh kosong)">
                </div>

                <button type="submit" class="btn btn-primary px-4 fw-bold">
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
