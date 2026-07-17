@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm rounded-4" style="background-color: var(--bg-card); border: 1px solid var(--border-color) !important;">
                <div class="card-header bg-transparent p-4 border-bottom" style="border-color: var(--border-color) !important;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-user-plus fa-lg"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0 text-dark" style="color: var(--text-main) !important;">Pendaftaran Akun Warga Baru</h4>
                            <p class="text-muted small mb-0">Lengkapi formulir di bawah ini untuk membuat akun dan profil warga terintegrasi.</p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- 1. DATA KREDENSIAL AKUN -->
                        <h5 class="fw-bold text-primary mb-3"><i class="fas fa-lock me-1"></i> 1. Kredensial Login Akun</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-secondary">Alamat Email</label>
                                <input type="email" name="email" value="{{ old('email') }}" class="form-control" required placeholder="Cth: warga@email.com" style="border-radius: 10px;">
                                @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-secondary">Password Baru</label>
                                <input type="password" name="password" class="form-control" required placeholder="Minimal 6 karakter" style="border-radius: 10px;">
                                @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-secondary">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" class="form-control" required placeholder="Ulangi password" style="border-radius: 10px;">
                            </div>
                        </div>

                        <hr class="my-4" style="border-color: var(--border-color);">

                        <!-- 2. DATA DIRI WARGA -->
                        <h5 class="fw-bold text-primary mb-3"><i class="fas fa-id-card me-1"></i> 2. Data Identitas Warga</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary">Nama Lengkap</label>
                                <input type="text" name="name" value="{{ old('name') }}" class="form-control" required placeholder="Contoh: Budi Santoso" style="border-radius: 10px;">
                                @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-secondary">NIK (16 Digit)</label>
                                <input type="text" name="nik" value="{{ old('nik') }}" class="form-control" placeholder="327501..." style="border-radius: 10px;">
                                @error('nik')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-secondary">Nomor KK (16 Digit)</label>
                                <input type="text" name="no_kk" value="{{ old('no_kk') }}" class="form-control" placeholder="327501..." style="border-radius: 10px;">
                                @error('no_kk')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-secondary">Nomor Telepon / HP</label>
                                <input type="text" name="phone" value="{{ old('phone') }}" class="form-control" placeholder="0812..." style="border-radius: 10px;">
                                @error('phone')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-secondary">Jenis Kelamin</label>
                                <select name="gender" class="form-select" required style="border-radius: 10px;">
                                    <option value="">Pilih Gender...</option>
                                    <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('gender')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-secondary">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" class="form-control" required style="border-radius: 10px;">
                                @error('tanggal_lahir')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-semibold text-secondary">Foto Profil (Opsional)</label>
                                <input type="file" name="profile_photo" class="form-control" accept="image/*" style="border-radius: 10px;">
                                @error('profile_photo')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <hr class="my-4" style="border-color: var(--border-color);">

                        <!-- 3. HUBUNGAN KELUARGA -->
                        <h5 class="fw-bold text-primary mb-3"><i class="fas fa-sitemap me-1"></i> 3. Struktur Hubungan Keluarga</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary">Status dalam Keluarga</label>
                                <select name="is_kk" id="statusKeluarga" class="form-select" required style="border-radius: 10px;">
                                    <option value="">Pilih Status...</option>
                                    <option value="1" {{ old('is_kk') == '1' ? 'selected' : '' }}>Kepala Keluarga (KK)</option>
                                    <option value="0" {{ old('is_kk') == '0' ? 'selected' : '' }}>Anggota Keluarga (Istri/Anak)</option>
                                </select>
                                @error('is_kk')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6" id="pilihKKWrapper" style="display: {{ old('is_kk') == '0' ? 'block' : 'none' }};">
                                <label class="form-label fw-semibold text-primary">Kaitkan ke Kepala Keluarga</label>
                                <select name="kk_id" id="pilihKK" class="form-select" style="border-radius: 10px;">
                                    <option value="">-- Pilih Nama Kepala Keluarganya --</option>
                                    @foreach($kepalaKeluargas as $kk)
                                        <option value="{{ $kk->id }}" {{ old('kk_id') == $kk->id ? 'selected' : '' }}>{{ $kk->nama }} (RT {{ $kk->rt_number }})</option>
                                    @endforeach
                                </select>
                                @error('kk_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <hr class="my-4" style="border-color: var(--border-color);">

                        <!-- 4. LOKASI RUMAH & WILAYAH -->
                        <h5 class="fw-bold text-primary mb-3"><i class="fas fa-home me-1"></i> 4. Lokasi Tempat Tinggal</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-secondary">RT</label>
                                <select name="rt_number" class="form-select" required style="border-radius: 10px;">
                                    <option value="">Pilih RT...</option>
                                    @foreach(['006','007','008','009','010'] as $rtOpt)
                                        <option value="{{ $rtOpt }}" {{ old('rt_number') == $rtOpt ? 'selected' : '' }}>RT {{ $rtOpt }}</option>
                                    @endforeach
                                </select>
                                @error('rt_number')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-secondary">RW</label>
                                <input type="number" name="rw_number" value="{{ old('rw_number', 18) }}" class="form-control" required placeholder="Cth: 18" style="border-radius: 10px;">
                                @error('rw_number')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary">Blok Rumah / Nomor Rumah</label>
                                <input type="text" name="blok_rumah" value="{{ old('blok_rumah') }}" class="form-control" required placeholder="Cth: Blok A No. 12" style="border-radius: 10px;">
                                @error('blok_rumah')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary">Provinsi (API)</label>
                                <select id="provinsiAPI" class="form-select" required style="border-radius: 10px;">
                                    <option value="">Memuat data...</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary">Kabupaten/Kota (API)</label>
                                <select id="kotaAPI" class="form-select" required disabled style="border-radius: 10px;">
                                    <option value="">Pilih Provinsi dulu...</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary">Kecamatan (API)</label>
                                <select id="kecamatanAPI" class="form-select" required disabled style="border-radius: 10px;">
                                    <option value="">Pilih Kota dulu...</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary">Kelurahan/Desa (API)</label>
                                <select id="kelurahanAPI" class="form-select" required disabled style="border-radius: 10px;">
                                    <option value="">Pilih Kecamatan dulu...</option>
                                </select>
                            </div>
                        </div>

                        <!-- Hidden input for constructed full address -->
                        <input type="hidden" name="address" id="alamatLengkap" value="{{ old('address') }}">
                        @error('address')<div class="text-danger small mb-3">{{ $message }}</div>@enderror

                        <div class="col-12 mt-4 text-center">
                            <button type="submit" class="btn btn-primary btn-lg w-100 py-3 shadow-sm btn-animated" style="border-radius: 12px; font-weight: 700;">
                                <i class="fas fa-user-plus me-1"></i> SELESAIKAN PENDAFTARAN WARGA
                            </button>
                            <p class="text-muted mt-3 mb-0">Sudah memiliki akun? <a href="{{ route('login') }}" class="text-primary fw-bold">Masuk di sini</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tampil/Sembunyi Pilihan KK
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

    // API Emsifa (Full sampai Kelurahan)
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
        });

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
        const p = provAPI.options[provAPI.selectedIndex].getAttribute('data-nama');
        const k = kotaAPI.options[kotaAPI.selectedIndex].getAttribute('data-nama');
        const kc = kecAPI.options[kecAPI.selectedIndex].getAttribute('data-nama');
        const kl = kelAPI.options[kelAPI.selectedIndex].getAttribute('data-nama');
        if(p && k && kc && kl) {
            document.getElementById('alamatLengkap').value = `Kel/Desa. ${kl}, Kec. ${kc}, ${k}, Provinsi ${p}`;
        }
    });
});
</script>
@endpush
