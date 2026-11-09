@extends('layouts.app')

@section('title', 'Pendaftaran Warga — NexaNest Smart & Green Community')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    :root {
        --nx-forest: #285943;
        --nx-forest-dark: #1E4633;
        --nx-sage: #A8C9B5;
        --nx-pastel-blue: #A9C9E8;
        --nx-mint: #DCEBDD;
        --nx-mint-soft: #EEF6EF;
        --nx-powder: #DCEAF5;
        --nx-powder-soft: #F0F6FC;
        --nx-bg: #F7F9F7;
        --nx-white: #FFFFFF;
        --nx-text: #263449;
        --nx-muted: #718096;
        --nx-border: #E5EBE6;
        --nx-soft-red: #FCE8E9;
    }

    body {
        font-family: 'Plus Jakarta Sans', 'Inter', sans-serif !important;
        background: radial-gradient(circle at 10% 10%, #E6F2E7 0%, #F7F9F7 50%, #E7F1FA 100%) !important;
        color: var(--nx-text) !important;
        min-height: 100vh;
    }

    .theme-toggle-btn,
    nav.navbar {
        display: none !important;
    }

    .nx-reg-wrap {
        max-width: 980px;
        margin: 0 auto;
        padding: 32px 16px 56px;
    }

    .nx-reg-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 24px;
    }

    .nx-brand-badge {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
    }

    .nx-brand-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        background: var(--nx-white);
        border: 1px solid var(--nx-border);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 20px rgba(40, 89, 67, 0.08);
    }

    .nx-brand-name {
        font-weight: 800;
        font-size: 1.25rem;
        color: var(--nx-forest);
        margin: 0;
        line-height: 1.15;
    }

    .nx-brand-tagline {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--nx-muted);
        margin: 0;
    }

    .nx-reg-hero {
        background: linear-gradient(135deg, #DCEBDD 0%, #E8F3EE 55%, #DCEAF5 100%);
        border: 1px solid var(--nx-border);
        border-radius: 24px;
        padding: 28px 32px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        box-shadow: 0 12px 32px rgba(40, 89, 67, 0.06);
    }

    .nx-reg-hero-title {
        font-size: 1.55rem;
        font-weight: 800;
        color: var(--nx-forest);
        margin-bottom: 6px;
    }

    .nx-reg-hero-desc {
        font-size: 0.9rem;
        color: #485E52;
        margin: 0;
        max-width: 600px;
    }

    .nx-reg-card {
        background: var(--nx-white);
        border: 1px solid var(--nx-border);
        border-radius: 24px;
        padding: 34px;
        box-shadow: 0 16px 44px rgba(40, 89, 67, 0.06);
    }

    .nx-section-box {
        background: var(--nx-bg);
        border: 1px solid var(--nx-border);
        border-radius: 18px;
        padding: 22px 24px;
        margin-bottom: 22px;
    }

    .nx-section-head {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 18px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--nx-border);
    }

    .nx-section-num {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: var(--nx-mint);
        color: var(--nx-forest);
        font-weight: 800;
        font-size: 0.88rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .nx-section-num.blue {
        background: var(--nx-powder);
        color: #2B5F8E;
    }

    .nx-section-title {
        font-size: 1rem;
        font-weight: 800;
        color: var(--nx-text);
        margin: 0;
    }

    .nx-section-sub {
        font-size: 0.78rem;
        color: var(--nx-muted);
        margin: 0;
    }

    .nx-label {
        display: block;
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--nx-text);
        margin-bottom: 6px;
    }

    .nx-control,
    .nx-select {
        width: 100%;
        padding: 10px 14px;
        background: var(--nx-white) !important;
        border: 1.5px solid var(--nx-border) !important;
        border-radius: 12px !important;
        font-size: 0.88rem;
        color: var(--nx-text) !important;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .nx-control:focus,
    .nx-select:focus {
        outline: none;
        border-color: var(--nx-forest) !important;
        box-shadow: 0 0 0 3px rgba(40, 89, 67, 0.12) !important;
    }

    .nx-submit-btn {
        background: var(--nx-forest);
        color: var(--nx-white);
        border: none;
        border-radius: 14px;
        padding: 13px 28px;
        font-size: 0.94rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        box-shadow: 0 10px 24px rgba(40, 89, 67, 0.2);
        transition: all 0.2s ease;
    }

    .nx-submit-btn:hover {
        background: var(--nx-forest-dark);
        color: var(--nx-white);
        transform: translateY(-1px);
    }

    .nx-outline-btn {
        background: var(--nx-white);
        color: var(--nx-forest);
        border: 1.5px solid var(--nx-border);
        border-radius: 999px;
        padding: 8px 18px;
        font-size: 0.82rem;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
    }

    .nx-outline-btn:hover {
        background: var(--nx-mint-soft);
        border-color: var(--nx-sage);
        color: var(--nx-forest-dark);
    }

    @media (max-width: 767.98px) {
        .nx-reg-card {
            padding: 20px 16px;
        }
        .nx-reg-hero {
            flex-direction: column;
            align-items: flex-start;
            padding: 22px 20px;
        }
    }
</style>
@endpush

@section('content')
<div class="nx-reg-wrap">

    <!-- TOP HEADER -->
    <div class="nx-reg-topbar">
        <a href="{{ url('/') }}" class="nx-brand-badge">
            <span class="nx-brand-icon">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none">
                    <path d="M3 10.5L12 3L21 10.5V20C21 20.5523 20.5523 21 20 21H4C3.44772 21 3 20.5523 3 20V10.5Z" stroke="#285943" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M9 21V14C9 12.8954 9.89543 12 11 12H13C14.1046 12 15 12.8954 15 14V21" stroke="#285943" stroke-width="2"/>
                    <path d="M13.5 8.5C15.5 7 18 8 18 10.5C16 11.5 13.5 10.5 13.5 8.5Z" fill="#285943"/>
                </svg>
            </span>
            <div>
                <h1 class="nx-brand-name">NexaNest</h1>
                <p class="nx-brand-tagline">Smart &amp; Green Community</p>
            </div>
        </a>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ url('/') }}" class="nx-outline-btn">
                <i class="fas fa-home"></i> Beranda
            </a>
            <a href="{{ route('login') }}" class="nx-outline-btn">
                <i class="fas fa-sign-in-alt"></i> Masuk Akun
            </a>
        </div>
    </div>

    <!-- HERO BANNER -->
    <div class="nx-reg-hero">
        <div>
            <span class="badge rounded-pill mb-2 px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
                <i class="fas fa-user-plus me-1"></i> Registrasi Warga Terintegrasi
            </span>
            <h2 class="nx-reg-hero-title">Daftar Akun Warga &amp; Data Kependudukan</h2>
            <p class="nx-reg-hero-desc">
                Lengkapi informasi akun dan profil kependudukan keluarga Anda agar terhubung langsung dengan layanan surat digital, transparansi kas, dan informasi lingkungan RT/RW.
            </p>
        </div>
        <div class="d-none d-md-block">
            <svg width="110" height="84" viewBox="0 0 110 84" fill="none">
                <rect width="110" height="84" rx="16" fill="#FFFFFF" fill-opacity="0.7"/>
                <circle cx="38" cy="36" r="12" fill="#285943"/>
                <path d="M20 66C20 56 28 50 38 50C48 50 56 56 56 66" stroke="#285943" stroke-width="4" stroke-linecap="round"/>
                <circle cx="72" cy="38" r="10" fill="#6AA6D8"/>
                <path d="M58 66C58 58 64 53 72 53C80 53 86 58 86 66" stroke="#6AA6D8" stroke-width="4" stroke-linecap="round"/>
            </svg>
        </div>
    </div>

    <div class="nx-reg-card">
        @if ($errors->any())
            <div class="p-3 rounded-4 mb-4" style="background: var(--nx-soft-red); border: 1px solid #F5C2C5; color: #9E2A2F;">
                <div class="fw-bold small mb-1"><i class="fas fa-exclamation-triangle me-1"></i> Mohon periksa kembali isian berikut:</div>
                <ul class="mb-0 ps-3 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- SECTION 1: INFORMASI AKUN -->
            <div class="nx-section-box">
                <div class="nx-section-head">
                    <span class="nx-section-num">01</span>
                    <div>
                        <h3 class="nx-section-title">Informasi Akun Login</h3>
                        <p class="nx-section-sub">Digunakan untuk masuk ke aplikasi NexaNest</p>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="nx-label">Nama Lengkap (Sesuai KTP) <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" class="nx-control" placeholder="Contoh: Budi Santoso" required>
                    </div>
                    <div class="col-md-6">
                        <label class="nx-label">Alamat Email Aktif <span class="text-danger">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" class="nx-control" placeholder="budi@email.com" required>
                    </div>
                    <div class="col-md-6">
                        <label class="nx-label">Kata Sandi <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="nx-control" placeholder="Minimal 6 karakter" required>
                    </div>
                    <div class="col-md-6">
                        <label class="nx-label">Konfirmasi Kata Sandi <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="nx-control" placeholder="Ulangi kata sandi" required>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: IDENTITAS KEPENDUDUKAN -->
            <div class="nx-section-box">
                <div class="nx-section-head">
                    <span class="nx-section-num blue">02</span>
                    <div>
                        <h3 class="nx-section-title">Identitas Kependudukan (KTP &amp; KK)</h3>
                        <p class="nx-section-sub">Data resmi untuk keperluan administrasi surat pengantar RT/RW</p>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="nx-label">Nomor Kartu Keluarga (No. KK - 16 Digit) <span class="text-danger">*</span></label>
                        <input type="text" name="no_kk" value="{{ old('no_kk') }}" class="nx-control" maxlength="16" pattern="\d{16}" title="Harus 16 digit angka" placeholder="3201xxxxxxxxxxxx" required>
                    </div>
                    <div class="col-md-6">
                        <label class="nx-label">Nomor Induk Kependudukan (NIK - 16 Digit) <span class="text-danger">*</span></label>
                        <input type="text" name="nik" value="{{ old('nik') }}" class="nx-control" maxlength="16" pattern="\d{16}" title="Harus 16 digit angka" placeholder="3201xxxxxxxxxxxx" required>
                    </div>
                    <div class="col-md-4">
                        <label class="nx-label">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select name="gender" class="nx-select" required>
                            <option value="">-- Pilih Gender --</option>
                            <option value="Laki-laki" {{ old('gender') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('gender') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="nx-label">Tanggal Lahir <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" class="nx-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="nx-label">Nomor HP / WhatsApp <span class="text-danger">*</span></label>
                        <input type="text" name="nomor_hp" value="{{ old('nomor_hp') }}" class="nx-control" placeholder="0812xxxxxxxx" required>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: STATUS KELUARGA & HUNIAN RT/RW -->
            <div class="nx-section-box">
                <div class="nx-section-head">
                    <span class="nx-section-num">03</span>
                    <div>
                        <h3 class="nx-section-title">Lingkungan RT/RW &amp; Status Keluarga</h3>
                        <p class="nx-section-sub">Penempatan domisili blok rumah serta relasi Kepala Keluarga</p>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="nx-label">Blok / Nomor Rumah <span class="text-danger">*</span></label>
                        <input type="text" name="blok_rumah" value="{{ old('blok_rumah') }}" class="nx-control" placeholder="Contoh: A1 No. 10" required>
                    </div>
                    <div class="col-md-4">
                        <label class="nx-label">Pilih RT <span class="text-danger">*</span></label>
                        <select name="rt_number" class="nx-select" required>
                            <option value="">-- Pilih RT --</option>
                            @for($i=1; $i<=5; $i++)
                                @php $rtVal = str_pad($i, 3, '0', STR_PAD_LEFT); @endphp
                                <option value="{{ $rtVal }}" {{ old('rt_number') == $rtVal ? 'selected' : '' }}>RT {{ $rtVal }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="nx-label">Nomor RW <span class="text-danger">*</span></label>
                        <input type="text" name="rw_number" value="{{ old('rw_number', '018') }}" class="nx-control" placeholder="Contoh: 018" maxlength="5" required>
                    </div>

                    <div class="col-md-6">
                        <label class="nx-label">Status dalam Keluarga <span class="text-danger">*</span></label>
                        <select name="is_kk" id="statusKeluarga" class="nx-select" required>
                            <option value="1" {{ old('is_kk', '1') == '1' ? 'selected' : '' }}>Kepala Keluarga (KK)</option>
                            <option value="0" {{ old('is_kk') == '0' ? 'selected' : '' }}>Anggota Keluarga (Istri / Anak / Lainnya)</option>
                        </select>
                    </div>

                    <div class="col-md-6" id="pilihKkContainer" style="display: {{ old('is_kk') == '0' ? 'block' : 'none' }};">
                        <label class="nx-label">Pilih Kepala Keluarga Terdaftar</label>
                        <select name="kk_id" id="kkSelect" class="nx-select">
                            <option value="">-- Cari / Pilih Kepala Keluarga --</option>
                            @foreach($kepalaKeluargas as $kk)
                                <option value="{{ $kk->id }}" {{ old('kk_id') == $kk->id ? 'selected' : '' }}>
                                    {{ $kk->nama }} — Blok {{ $kk->blok_rumah }} (No. KK: {{ $kk->no_kk }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted" style="font-size: 0.74rem;">Pastikan Kepala Keluarga Anda sudah terdaftar lebih dahulu.</small>
                    </div>
                </div>
            </div>

            <!-- SECTION 4: WILAYAH & ALAMAT LENGKAP -->
            <div class="nx-section-box mb-4">
                <div class="nx-section-head">
                    <span class="nx-section-num blue">04</span>
                    <div>
                        <h3 class="nx-section-title">Domisili Wilayah &amp; Alamat Lengkap</h3>
                        <p class="nx-section-sub">Terhubung otomatis dengan referensi wilayah Indonesia</p>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="nx-label">Provinsi</label>
                        <select id="provinsi" name="provinsi" class="nx-select">
                            <option value="">Pilih Provinsi</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="nx-label">Kabupaten / Kota</label>
                        <select id="kabupaten" name="kabupaten" class="nx-select" disabled>
                            <option value="">Pilih Kota/Kab</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="nx-label">Kecamatan</label>
                        <select id="kecamatan" name="kecamatan" class="nx-select" disabled>
                            <option value="">Pilih Kecamatan</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="nx-label">Kelurahan / Desa</label>
                        <select id="kelurahan" name="kelurahan" class="nx-select" disabled>
                            <option value="">Pilih Kelurahan</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="nx-label">Detail Jalan / Alamat Lengkap <span class="text-danger">*</span></label>
                        <textarea name="alamat" class="nx-control" rows="2" placeholder="Contoh: Perumahan Griya Asri Jl. Melati Blok A1 No. 10" required>{{ old('alamat') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 pt-2">
                <div class="small text-muted">
                    Sudah memiliki akun warga?
                    <a href="{{ route('login') }}" class="fw-bold text-decoration-none" style="color: var(--nx-forest);">Masuk di sini</a>
                </div>
                <button type="submit" class="nx-submit-btn">
                    <i class="fas fa-check-circle"></i>
                    <span>Daftar &amp; Simpan Data Warga</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('statusKeluarga');
    const kkContainer = document.getElementById('pilihKkContainer');
    const kkSelect = document.getElementById('kkSelect');

    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            if (this.value === '0') {
                kkContainer.style.display = 'block';
                kkSelect.setAttribute('required', 'required');
            } else {
                kkContainer.style.display = 'none';
                kkSelect.removeAttribute('required');
                kkSelect.value = '';
            }
        });
    }

    const apiBase = 'https://www.emsifa.com/api-wilayah-indonesia/api';
    const provSelect = document.getElementById('provinsi');
    const kabSelect = document.getElementById('kabupaten');
    const kecSelect = document.getElementById('kecamatan');
    const kelSelect = document.getElementById('kelurahan');

    fetch(`${apiBase}/provinces.json`)
        .then(res => res.json())
        .then(data => {
            data.forEach(p => {
                let opt = new Option(p.name, p.name);
                opt.dataset.id = p.id;
                provSelect.add(opt);
            });
        }).catch(() => {});

    provSelect.addEventListener('change', function() {
        kabSelect.length = 1; kecSelect.length = 1; kelSelect.length = 1;
        kabSelect.disabled = true; kecSelect.disabled = true; kelSelect.disabled = true;
        const id = this.options[this.selectedIndex]?.dataset?.id;
        if (!id) return;
        fetch(`${apiBase}/regencies/${id}.json`)
            .then(res => res.json())
            .then(data => {
                data.forEach(k => {
                    let opt = new Option(k.name, k.name);
                    opt.dataset.id = k.id;
                    kabSelect.add(opt);
                });
                kabSelect.disabled = false;
            });
    });

    kabSelect.addEventListener('change', function() {
        kecSelect.length = 1; kelSelect.length = 1;
        kecSelect.disabled = true; kelSelect.disabled = true;
        const id = this.options[this.selectedIndex]?.dataset?.id;
        if (!id) return;
        fetch(`${apiBase}/districts/${id}.json`)
            .then(res => res.json())
            .then(data => {
                data.forEach(k => {
                    let opt = new Option(k.name, k.name);
                    opt.dataset.id = k.id;
                    kecSelect.add(opt);
                });
                kecSelect.disabled = false;
            });
    });

    kecSelect.addEventListener('change', function() {
        kelSelect.length = 1; kelSelect.disabled = true;
        const id = this.options[this.selectedIndex]?.dataset?.id;
        if (!id) return;
        fetch(`${apiBase}/villages/${id}.json`)
            .then(res => res.json())
            .then(data => {
                data.forEach(k => {
                    let opt = new Option(k.name, k.name);
                    opt.dataset.id = k.id;
                    kelSelect.add(opt);
                });
                kelSelect.disabled = false;
            });
    });
});
</script>
@endpush
