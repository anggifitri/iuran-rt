@extends('layouts.app')

@section('title', 'Edit Data Warga — NexaNest')

@section('content')
@php
    $kepalaKeluargas = $kepalaKeluargas ?? \App\Models\Warga::where('is_kk', 1)->where('id', '!=', $warga->id)->get();
@endphp

<div class="nx-page-header">
    <div>
        <span class="badge rounded-pill mb-2 px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
            <i class="fas fa-user-pen me-1"></i> Pembaruan Data Kependudukan
        </span>
        <h1 class="nx-page-title">Edit Data Warga: {{ $warga->nama }}</h1>
        <p class="nx-page-subtitle">Perbarui informasi identitas, domisili blok rumah, dan status hubungan keluarga warga.</p>
    </div>
    <a href="{{ route('warga.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Warga
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger mb-4">
        <div class="fw-bold mb-1"><i class="fas fa-exclamation-circle me-1"></i> Mohon periksa kembali isian berikut:</div>
        <ul class="mb-0 ps-3 small">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card p-4">
    <form action="{{ route('warga.update', $warga->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <div class="col-12 d-flex align-items-center gap-3 pb-3 border-bottom">
                <img src="{{ $warga->profile_photo_url }}" alt="{{ $warga->nama }}"
                     class="rounded-circle shadow-sm"
                     style="width: 68px; height: 68px; object-fit: cover; border: 3px solid var(--nx-mint);">
                <div>
                    <h6 class="fw-bold mb-1" style="color: var(--nx-text);">{{ $warga->nama }}</h6>
                    <small class="text-muted d-block">Blok {{ $warga->blok_rumah }} • RT {{ $warga->rt_number }} / RW {{ $warga->rw_number }}</small>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="nama" value="{{ old('nama', $warga->nama) }}" required class="form-control" placeholder="Masukkan nama lengkap">
            </div>

            <div class="col-md-3">
                <label class="form-label">NIK (16 Digit) <span class="text-danger">*</span></label>
                <input type="text" name="nik" value="{{ old('nik', $warga->nik) }}" required class="form-control" placeholder="Masukkan NIK">
            </div>

            <div class="col-md-3">
                <label class="form-label">No. KK (16 Digit) <span class="text-danger">*</span></label>
                <input type="text" name="no_kk" value="{{ old('no_kk', $warga->no_kk) }}" required class="form-control" placeholder="Masukkan No. KK">
            </div>

            <div class="col-md-4">
                <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                <select name="gender" class="form-select" required>
                    <option value="">-- Pilih Gender --</option>
                    <option value="L" {{ in_array(old('gender', $warga->gender), ['L', 'Laki-laki']) ? 'selected' : '' }}>L - Laki-laki</option>
                    <option value="P" {{ in_array(old('gender', $warga->gender), ['P', 'Perempuan']) ? 'selected' : '' }}>P - Perempuan</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $warga->tanggal_lahir ? \Carbon\Carbon::parse($warga->tanggal_lahir)->format('Y-m-d') : '') }}" required class="form-control">
            </div>

            <div class="col-md-4">
                <label class="form-label">Nomor HP / WhatsApp <span class="text-danger">*</span></label>
                <input type="text" name="nomor_hp" value="{{ old('nomor_hp', $warga->nomor_hp) }}" required class="form-control" placeholder="Masukkan nomor HP">
            </div>

            <div class="col-md-4">
                <label class="form-label">Alamat Email Akun (@gmail.com)</label>
                <input type="email" name="email" value="{{ old('email', $warga->user?->email) }}" class="form-control" placeholder="contoh: nama@gmail.com">
                <small class="text-muted d-block mt-1" style="font-size: 0.72rem;"><i class="fas fa-info-circle me-1"></i> Akun login warga. Wajib domain @gmail.com.</small>
            </div>

            <div class="col-md-4">
                <label class="form-label">Blok Rumah <span class="text-danger">*</span></label>
                <input type="text" name="blok_rumah" value="{{ old('blok_rumah', $warga->blok_rumah) }}" required class="form-control" placeholder="Contoh: A1/12">
            </div>

            <div class="col-md-4">
                <label class="form-label">Nomor RT <span class="text-danger">*</span></label>
                <input type="text" name="rt_number" value="{{ old('rt_number', $warga->rt_number) }}" required class="form-control" placeholder="Contoh: 006">
            </div>

            <div class="col-md-4">
                <label class="form-label">Nomor RW <span class="text-danger">*</span></label>
                <input type="text" name="rw_number" value="{{ old('rw_number', $warga->rw_number ?? '018') }}" required class="form-control" placeholder="Contoh: 018">
            </div>

            <div class="col-md-6">
                <label class="form-label">Status dalam Keluarga <span class="text-danger">*</span></label>
                <select name="is_kk" id="statusKeluargaEdit" class="form-select" required>
                    <option value="1" {{ old('is_kk', $warga->is_kk) == 1 ? 'selected' : '' }}>Kepala Keluarga (KK)</option>
                    <option value="0" {{ old('is_kk', $warga->is_kk) == 0 ? 'selected' : '' }}>Anggota Keluarga (Istri/Anak)</option>
                </select>
            </div>

            <div class="col-md-6" id="pilihKKWrapperEdit" style="display: {{ old('is_kk', $warga->is_kk) == 0 ? 'block' : 'none' }};">
                <label class="form-label">Kaitkan ke Kepala Keluarga</label>
                <select name="kk_id" id="pilihKKEdit" class="form-select">
                    <option value="">-- Pilih Kepala Keluarga --</option>
                    @foreach($kepalaKeluargas as $kk)
                        <option value="{{ $kk->id }}" {{ old('kk_id', $warga->kk_id) == $kk->id ? 'selected' : '' }}>
                            {{ $kk->nama }} (Blok {{ $kk->blok_rumah }} • RT {{ $kk->rt_number }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-12">
                <label class="form-label">Alamat Domisili Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="alamat" value="{{ old('alamat', $warga->alamat ?? 'Lingkungan RW 018') }}" required class="form-control">
            </div>

            <div class="col-md-12">
                <label class="form-label">Ganti Foto Profil (Opsional)</label>
                <input type="file" name="profile_photo" class="form-control @error('profile_photo') is-invalid @enderror" accept="image/png, image/jpeg">
                @error('profile_photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12 mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                <a href="{{ route('warga.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fas fa-save me-1"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const statusSel = document.getElementById('statusKeluargaEdit');
    const wrapper = document.getElementById('pilihKKWrapperEdit');
    const kkSel = document.getElementById('pilihKKEdit');

    if (statusSel) {
        statusSel.addEventListener('change', function () {
            if (this.value === '0') {
                wrapper.style.display = 'block';
            } else {
                wrapper.style.display = 'none';
                if (kkSel) kkSel.value = '';
            }
        });
    }
});
</script>
@endpush
