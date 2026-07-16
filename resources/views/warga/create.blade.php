@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white p-4 border-bottom">
            <h4 class="fw-bold mb-0">Tambah Data Warga</h4>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('warga.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" required placeholder="Contoh: Budi Santoso">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">NIK</label>
                        <input type="text" name="nik" class="form-control" placeholder="327501...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Nomor KK</label>
                        <input type="text" name="no_kk" class="form-control" placeholder="327501...">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Nomor HP</label>
                        <input type="text" name="nomor_hp" class="form-control" placeholder="0812...">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Jenis Kelamin</label>
                        <select name="gender" class="form-select" required>
                            <option value="">Pilih Gender...</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Foto Profil (Opsional)</label>
                        <input type="file" name="profile_photo" class="form-control" accept="image/*">
                    </div>

                    <hr class="my-4 text-muted">

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Status dalam Keluarga</label>
                        <select name="is_kk" id="statusKeluarga" class="form-select" required>
                            <option value="">Pilih Status...</option>
                            <option value="1">Kepala Keluarga (KK)</option>
                            <option value="0">Anggota Keluarga (Istri/Anak)</option>
                        </select>
                    </div>
                    <div class="col-md-6" id="pilihKKWrapper" style="display: none;">
                        <label class="form-label fw-semibold text-primary">Kaitkan ke Kepala Keluarga</label>
                        <select name="kk_id" id="pilihKK" class="form-select">
                            <option value="">-- Pilih Nama Kepala Keluarganya --</option>
                            @foreach($kepalaKeluargas as $kk)
                                <option value="{{ $kk->id }}">{{ $kk->nama }} (RT {{ $kk->rt_number }})</option>
                            @endforeach
                        </select>
                    </div>

                    <hr class="my-4 text-muted">

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">RT</label>
                        <select name="rt_number" class="form-select" required>
                            <option value="">Pilih RT...</option>
                            <option value="006">RT 006</option>
                            <option value="007">RT 007</option>
                            <option value="008">RT 008</option>
                            <option value="009">RT 009</option>
                            <option value="010">RT 010</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">RW</label>
                        <input type="number" name="rw_number" class="form-control" required placeholder="Cth: 18">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Blok Rumah (Sesuai Data Lama)</label>
                        <input type="text" name="blok_rumah" class="form-control" required placeholder="Cth: Blok A No. 12">
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Provinsi (API)</label>
                        <select id="provinsiAPI" class="form-select" required>
                            <option value="">Memuat data...</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Kabupaten/Kota (API)</label>
                        <select id="kotaAPI" class="form-select" required disabled>
                            <option value="">Pilih Provinsi dulu...</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Kecamatan (API)</label>
                        <select id="kecamatanAPI" class="form-select" required disabled>
                            <option value="">Pilih Kota dulu...</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Kelurahan/Desa (API)</label>
                        <select id="kelurahanAPI" class="form-select" required disabled>
                            <option value="">Pilih Kecamatan dulu...</option>
                        </select>
                    </div>

                    <input type="hidden" name="alamat" id="alamatLengkap">

                    <div class="col-12 mt-4 text-end">
                        <a href="{{ route('warga.index') }}" class="btn btn-light px-4 me-2">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">Simpan Data Warga</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

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
@endsection