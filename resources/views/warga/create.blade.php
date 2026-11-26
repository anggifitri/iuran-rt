@extends('layouts.app')

@section('title', 'Tambah Data Warga — NexaNest')

@section('content')
<div class="nx-page-header">
    <div>
        <span class="badge rounded-pill mb-2 px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
            <i class="fas fa-user-plus me-1"></i> Formulir Kependudukan
        </span>
        <h1 class="nx-page-title">Tambah Data Warga Baru</h1>
        <p class="nx-page-subtitle">Input data Kepala Keluarga atau anggota keluarga baru ke dalam database RT/RW.</p>
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
    <form action="{{ route('warga.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-3">
            <div class="col-12">
                <h6 class="fw-bold mb-0" style="color: var(--nx-forest);"><i class="fas fa-id-card me-2"></i>1. Identitas Pribadi &amp; Kependudukan</h6>
                <hr class="mt-2 mb-1">
            </div>

            <div class="col-md-6">
                <label class="form-label">Nama Lengkap (Sesuai KTP) <span class="text-danger">*</span></label>
                <input type="text" name="nama" value="{{ old('nama') }}" class="form-control" required placeholder="Contoh: Budi Santoso">
            </div>
            <div class="col-md-3">
                <label class="form-label">NIK (16 Digit) <span class="text-danger">*</span></label>
                <input type="text" name="nik" value="{{ old('nik') }}" class="form-control" required placeholder="327501...">
            </div>
            <div class="col-md-3">
                <label class="form-label">Nomor KK (16 Digit) <span class="text-danger">*</span></label>
                <input type="text" name="no_kk" value="{{ old('no_kk') }}" class="form-control" required placeholder="327501...">
            </div>

            <div class="col-md-4">
                <label class="form-label">Nomor HP / WhatsApp <span class="text-danger">*</span></label>
                <input type="text" name="nomor_hp" value="{{ old('nomor_hp') }}" class="form-control" required placeholder="0812...">
            </div>
            <div class="col-md-4">
                <label class="form-label">Alamat Email Akun (@gmail.com)</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="contoh: nama@gmail.com">
                <small class="text-muted d-block mt-1" style="font-size: 0.72rem;"><i class="fas fa-info-circle me-1"></i> Opsional untuk akun login warga. Wajib domain @gmail.com.</small>
            </div>
            <div class="col-md-4">
                <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                <select name="gender" class="form-select" required>
                    <option value="">Pilih Gender...</option>
                    <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" class="form-control" required>
            </div>

            <div class="col-md-12">
                <label class="form-label">Foto Profil Warga (Opsional)</label>
                <input type="file" name="profile_photo" class="form-control" accept="image/*">
            </div>

            <div class="col-12 mt-4">
                <h6 class="fw-bold mb-0" style="color: var(--nx-forest);"><i class="fas fa-people-roof me-2"></i>2. Status Keluarga &amp; Domisili RT/RW</h6>
                <hr class="mt-2 mb-1">
            </div>

            <div class="col-md-6">
                <label class="form-label">Status dalam Keluarga <span class="text-danger">*</span></label>
                <select name="is_kk" id="statusKeluarga" class="form-select" required>
                    <option value="">Pilih Status...</option>
                    <option value="1" {{ old('is_kk') === '1' ? 'selected' : '' }}>Kepala Keluarga (KK)</option>
                    <option value="0" {{ old('is_kk') === '0' ? 'selected' : '' }}>Anggota Keluarga (Istri/Anak)</option>
                </select>
            </div>
            <div class="col-md-6" id="pilihKKWrapper" style="display: {{ old('is_kk') === '0' ? 'block' : 'none' }};">
                <label class="form-label">Kaitkan ke Kepala Keluarga</label>
                <select name="kk_id" id="pilihKK" class="form-select">
                    <option value="">-- Pilih Nama Kepala Keluarganya --</option>
                    @foreach($kepalaKeluargas as $kk)
                        <option value="{{ $kk->id }}" {{ old('kk_id') == $kk->id ? 'selected' : '' }}>{{ $kk->nama }} (RT {{ $kk->rt_number }})</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">RT <span class="text-danger">*</span></label>
                <select name="rt_number" class="form-select" required>
                    <option value="">Pilih RT...</option>
                    @foreach(['001','002','003','004','005','006','007','008','009','010'] as $rtOpt)
                        <option value="{{ $rtOpt }}" {{ old('rt_number') == $rtOpt ? 'selected' : '' }}>RT {{ $rtOpt }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">RW <span class="text-danger">*</span></label>
                <input type="number" name="rw_number" value="{{ old('rw_number', '18') }}" class="form-control" required placeholder="Cth: 18">
            </div>
            <div class="col-md-6">
                <label class="form-label">Blok Rumah <span class="text-danger">*</span></label>
                <input type="text" name="blok_rumah" value="{{ old('blok_rumah') }}" class="form-control" required placeholder="Cth: Blok A No. 12">
            </div>

            <div class="col-md-6">
                <label class="form-label">Provinsi (API Wilayah)</label>
                <select id="provinsiAPI" class="form-select" required>
                    <option value="">Memuat data...</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Kabupaten/Kota (API Wilayah)</label>
                <select id="kotaAPI" class="form-select" required disabled>
                    <option value="">Pilih Provinsi dulu...</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Kecamatan (API Wilayah)</label>
                <select id="kecamatanAPI" class="form-select" required disabled>
                    <option value="">Pilih Kota dulu...</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Kelurahan/Desa (API Wilayah)</label>
                <select id="kelurahanAPI" class="form-select" required disabled>
                    <option value="">Pilih Kecamatan dulu...</option>
                </select>
            </div>

            <input type="hidden" name="alamat" id="alamatLengkap" value="{{ old('alamat', 'Lingkungan RW 018') }}">

            <div class="col-12 mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                <a href="{{ route('warga.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fas fa-save me-1"></i> Simpan Data Warga
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusKeluarga = document.getElementById('statusKeluarga');
    const pilihKKWrapper = document.getElementById('pilihKKWrapper');
    const pilihKK = document.getElementById('pilihKK');

    statusKeluarga.addEventListener('change', function() {
        if (this.value === '0') {
            pilihKKWrapper.style.display = 'block';
            pilihKK.setAttribute('required', 'required');
        } else {
            pilihKKWrapper.style.display = 'none';
            pilihKK.removeAttribute('required');
            pilihKK.value = '';
        }
    });

    const provAPI = document.getElementById('provinsiAPI');
    const kotaAPI = document.getElementById('kotaAPI');
    const kecAPI = document.getElementById('kecamatanAPI');
    const kelAPI = document.getElementById('kelurahanAPI');
    const formWarga = document.querySelector('form');

    fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json')
        .then(response => response.json())
        .then(provinces => {
            provAPI.innerHTML = '<option value="">Pilih Provinsi...</option>';
            provinces.forEach(p => provAPI.innerHTML += `<option value="${p.id}" data-nama="${p.name}">${p.name}</option>`);
        }).catch(() => {});

    provAPI.addEventListener('change', function() {
        kotaAPI.disabled = !this.value; kecAPI.disabled = true; kelAPI.disabled = true;
        if(this.value) {
            fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${this.value}.json`)
                .then(r => r.json())
                .then(kotas => {
                    kotaAPI.innerHTML = '<option value="">Pilih Kota...</option>';
                    kotas.forEach(k => kotaAPI.innerHTML += `<option value="${k.id}" data-nama="${k.name}">${k.name}</option>`);
                });
        }
    });

    kotaAPI.addEventListener('change', function() {
        kecAPI.disabled = !this.value; kelAPI.disabled = true;
        if(this.value) {
            fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${this.value}.json`)
                .then(r => r.json())
                .then(kecamatans => {
                    kecAPI.innerHTML = '<option value="">Pilih Kecamatan...</option>';
                    kecamatans.forEach(k => kecAPI.innerHTML += `<option value="${k.id}" data-nama="${k.name}">${k.name}</option>`);
                });
        }
    });

    kecAPI.addEventListener('change', function() {
        kelAPI.disabled = !this.value;
        if(this.value) {
            fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${this.value}.json`)
                .then(r => r.json())
                .then(kelurahans => {
                    kelAPI.innerHTML = '<option value="">Pilih Kelurahan...</option>';
                    kelurahans.forEach(k => kelAPI.innerHTML += `<option value="${k.id}" data-nama="${k.name}">${k.name}</option>`);
                });
        }
    });

    formWarga.addEventListener('submit', function() {
        const p = provAPI.options[provAPI.selectedIndex]?.getAttribute('data-nama');
        const k = kotaAPI.options[kotaAPI.selectedIndex]?.getAttribute('data-nama');
        const kc = kecAPI.options[kecAPI.selectedIndex]?.getAttribute('data-nama');
        const kl = kelAPI.options[kelAPI.selectedIndex]?.getAttribute('data-nama');
        if(p && k && kc && kl) {
            document.getElementById('alamatLengkap').value = `Kel/Desa. ${kl}, Kec. ${kc}, ${k}, Provinsi ${p}`;
        }
    });
});
</script>
@endpush
