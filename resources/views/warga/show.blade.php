@extends('layouts.app')

@section('title', 'Profil Warga: ' . $warga->nama . ' — NexaNest')

@section('content')
<style>
    .profil-breadcrumb {
        font-size: 0.88rem;
        font-weight: 600;
        margin-bottom: 20px;
    }
    .profil-breadcrumb a {
        color: var(--nx-forest);
        text-decoration: none;
    }
    .profil-breadcrumb a:hover {
        text-decoration: underline;
    }
    .profil-header-card {
        background: linear-gradient(135deg, #FFFFFF 0%, #F5F9F6 100%);
        border: 1px solid var(--nx-border);
        border-radius: var(--nx-radius-lg, 20px);
        padding: 28px;
        margin-bottom: 24px;
        box-shadow: var(--nx-shadow);
    }
    .profil-avatar {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid var(--nx-mint);
        box-shadow: 0 8px 24px rgba(40, 89, 67, 0.12);
    }
    .nav-tabs-profil {
        border-bottom: 2px solid var(--nx-border);
        gap: 8px;
    }
    .nav-tabs-profil .nav-link {
        border: none;
        color: var(--nx-muted);
        font-weight: 600;
        font-size: 0.92rem;
        padding: 12px 20px;
        border-radius: 10px 10px 0 0;
        background: transparent;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .nav-tabs-profil .nav-link:hover {
        color: var(--nx-forest);
        background: var(--nx-mint-soft);
    }
    .nav-tabs-profil .nav-link.active {
        color: var(--nx-forest);
        background: #FFFFFF;
        border-bottom: 3px solid var(--nx-forest);
    }
    .profil-info-group {
        padding: 16px;
        border-radius: 12px;
        background: #F8FBF9;
        border: 1px solid var(--nx-border);
        height: 100%;
    }

    /* Dark Mode Support */
    html.dark .nav-tabs-profil .nav-link.active {
        color: #38BDF8 !important;
        background: #131B2E !important;
        border-bottom-color: #38BDF8 !important;
    }
    html.dark .profil-info-group {
        background: #17223B !important;
        border-color: #273654 !important;
        color: #F1F5F9 !important;
    }
    .profil-info-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--nx-muted);
        font-weight: 700;
        margin-bottom: 4px;
    }
    .profil-info-value {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--nx-text);
    }
    .timeline-item {
        position: relative;
        padding-left: 36px;
        margin-bottom: 24px;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: 11px;
        top: 28px;
        bottom: -18px;
        width: 2px;
        background: var(--nx-border);
    }
    .timeline-item:last-child::before {
        display: none;
    }
    .timeline-dot {
        position: absolute;
        left: 0;
        top: 4px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
    }
</style>

<!-- 6. BREADCRUMB (Point 6) -->
<nav class="profil-breadcrumb d-flex align-items-center gap-2" aria-label="breadcrumb">
    @if(Auth::user()->isAdminRw())
        <a href="{{ route('warga.index') }}">
            <i class="fas fa-database me-1"></i> Database Warga
        </a>
        <i class="fas fa-chevron-right text-muted small" style="font-size: 0.75rem;"></i>
        <a href="{{ route('warga.rt', $warga->rt_number) }}">
            RT {{ $warga->rt_number }}
        </a>
    @else
        <a href="{{ route('warga.index') }}">
            <i class="fas fa-users me-1"></i> Data Warga
        </a>
    @endif
    <i class="fas fa-chevron-right text-muted small" style="font-size: 0.75rem;"></i>
    <span class="text-dark">{{ $warga->nama }}</span>
</nav>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" style="background: #E8F5E9; color: #2E7D32; border-radius: 12px;" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" style="border-radius: 12px;" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- HEADER PROFIL WARGA -->
<div class="profil-header-card">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4">
        <div class="d-flex flex-column flex-sm-row align-items-center align-items-sm-start gap-4 text-center text-sm-start">
            <img src="{{ $warga->profile_photo_url }}" alt="{{ $warga->nama }}" class="profil-avatar">
            <div>
                <div class="d-flex align-items-center justify-content-center justify-content-sm-start gap-2 flex-wrap mb-1">
                    <span class="badge px-3 py-1 fs-6 fw-bold" style="background: var(--nx-forest); color: #FFFFFF; border-radius: 8px;">
                        RT {{ $warga->rt_number }} / RW {{ $warga->rw_number ?? '018' }}
                    </span>
                    <span class="badge" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-weight: 700;">
                        {{ $warga->status_hubungan }}
                    </span>
                    @if($warga->status_verifikasi === 'terverifikasi')
                        <span class="badge" style="background: #E8F5E9; color: #2E7D32; font-weight: 700;">
                            <i class="fas fa-circle-check me-1"></i> Terverifikasi
                        </span>
                    @elseif($warga->status_verifikasi === 'perbaikan')
                        <span class="badge" style="background: var(--nx-powder-soft); color: #1e4d72; font-weight: 700;">
                            <i class="fas fa-wrench me-1"></i> Perlu Perbaikan
                        </span>
                    @elseif($warga->status_verifikasi === 'ditolak')
                        <span class="badge" style="background: var(--nx-danger-soft); color: var(--nx-danger); font-weight: 700;">
                            <i class="fas fa-times-circle me-1"></i> Ditolak
                        </span>
                    @else
                        <span class="badge" style="background: var(--nx-warning-soft); color: var(--nx-warning); font-weight: 700;">
                            <i class="fas fa-hourglass-half me-1"></i> Menunggu Verifikasi
                        </span>
                    @endif
                    @if($warga->status_warga === 'pindah')
                        <span class="badge bg-secondary">Status: Pindah</span>
                    @endif
                </div>

                <h1 class="h3 fw-bold mb-1 text-dark" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                    {{ $warga->nama }}
                </h1>

                <!-- NIK & KK Masked -->
                @php
                    $maskedNik = $warga->nik ? (strlen($warga->nik) >= 10 ? substr($warga->nik, 0, 6) . '******' . substr($warga->nik, -4) : $warga->nik) : '-';
                    $maskedKk = $warga->no_kk ? (strlen($warga->no_kk) >= 10 ? substr($warga->no_kk, 0, 6) . '******' . substr($warga->no_kk, -4) : $warga->no_kk) : '-';
                @endphp
                <div class="text-muted small d-flex align-items-center gap-3 flex-wrap mt-2">
                    <span class="d-inline-flex align-items-center gap-1">
                        <i class="fas fa-id-card text-muted"></i>
                        <strong>NIK:</strong>
                        <span id="txtNikProfil" class="font-monospace fw-bold">{{ $maskedNik }}</span>
                        @if($warga->nik)
                            <button type="button" class="btn btn-sm btn-link text-muted p-0 ms-1" onclick="toggleNikVisibility()" title="Lihat/Sembunyikan NIK">
                                <i class="fas fa-eye" id="icToggleNik"></i>
                            </button>
                        @endif
                    </span>
                    <span>&bull;</span>
                    <span class="d-inline-flex align-items-center gap-1">
                        <i class="fas fa-people-roof text-muted"></i>
                        <strong>No. KK:</strong>
                        <span class="font-monospace fw-bold">{{ $maskedKk }}</span>
                    </span>
                    <span>&bull;</span>
                    <span>
                        <i class="fas fa-location-dot text-muted me-1"></i> Blok {{ $warga->blok_rumah ?: '-' }}
                    </span>
                    <span>&bull;</span>
                    <span class="d-inline-flex align-items-center gap-1">
                        <i class="fas fa-envelope text-muted"></i>
                        @if($warga->user && $warga->user->email)
                            <span class="font-monospace fw-semibold">{{ $warga->user->email }}</span>
                        @else
                            <span class="text-muted fst-italic">Belum punya akun</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Tombol Aksi Profil -->
        <div class="d-flex align-items-center gap-2 flex-wrap">
            @if(Auth::user()->isAdminRw() && $warga->status_verifikasi !== 'terverifikasi')
                <button type="button" class="btn btn-success d-inline-flex align-items-center gap-2"
                        data-bs-toggle="modal" data-bs-target="#modalAksiVerifWarga"
                        style="background: var(--nx-forest); border-color: var(--nx-forest); border-radius: 12px; font-weight: 600; padding: 10px 18px;">
                    <i class="fas fa-clipboard-check"></i>
                    <span>Verifikasi Berkas</span>
                </button>
            @endif

            <a href="{{ route('mutasi.create') }}?warga_id={{ $warga->id }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2"
               style="border-radius: 12px; font-weight: 600; padding: 10px 18px; background: #FFF; border-color: var(--nx-border);">
                <i class="fas fa-arrows-split-up-and-left text-muted"></i>
                <span>Ajukan Mutasi</span>
            </a>

            <a href="{{ route('warga.edit', $warga->id) }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2"
               style="border-radius: 12px; font-weight: 600; padding: 10px 18px; background: #FFF; border-color: var(--nx-border);">
                <i class="fas fa-pen-to-square text-muted"></i>
                <span>Edit Data</span>
            </a>
        </div>
    </div>
</div>

<!-- NAV TABS SECTION (Point 6) -->
<ul class="nav nav-tabs nav-tabs-profil mb-4" id="profilTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="tab-pribadi" data-bs-toggle="tab" data-bs-target="#pane-pribadi" type="button" role="tab" aria-selected="true">
            <i class="fas fa-user"></i> Informasi Pribadi
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-kependudukan" data-bs-toggle="tab" data-bs-target="#pane-kependudukan" type="button" role="tab" aria-selected="false">
            <i class="fas fa-city"></i> Informasi Kependudukan
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-keluarga" data-bs-toggle="tab" data-bs-target="#pane-keluarga" type="button" role="tab" aria-selected="false">
            <i class="fas fa-people-roof"></i> Informasi Keluarga ({{ count($semuaKeluarga) }})
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-dokumen" data-bs-toggle="tab" data-bs-target="#pane-dokumen" type="button" role="tab" aria-selected="false">
            <i class="fas fa-file-shield"></i> Dokumen Pendukung
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-riwayat" data-bs-toggle="tab" data-bs-target="#pane-riwayat" type="button" role="tab" aria-selected="false">
            <i class="fas fa-clock-rotate-left"></i> Riwayat Kependudukan &amp; Audit
        </button>
    </li>
</ul>

<!-- TAB CONTENTS -->
<div class="tab-content" id="profilTabsContent">

    <!-- 6.A. INFORMASI PRIBADI -->
    <div class="tab-pane fade show active" id="pane-pribadi" role="tabpanel" aria-labelledby="tab-pribadi">
        <div class="card p-4 shadow-sm" style="border-radius: 16px; border: 1px solid var(--nx-border);">
            <h5 class="fw-bold mb-3" style="color: var(--nx-forest);">
                <i class="fas fa-address-card me-2"></i>Data Identitas Pribadi
            </h5>
            <div class="row g-3">
                <div class="col-md-6 col-lg-4">
                    <div class="profil-info-group">
                        <div class="profil-info-label">Nama Lengkap Sesuai KTP</div>
                        <div class="profil-info-value">{{ $warga->nama }}</div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="profil-info-group">
                        <div class="profil-info-label">Nomor Induk Kependudukan (NIK)</div>
                        <div class="profil-info-value font-monospace">{{ $warga->nik ?: '-' }}</div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="profil-info-group">
                        <div class="profil-info-label">Nomor Kartu Keluarga (KK)</div>
                        <div class="profil-info-value font-monospace">{{ $warga->no_kk ?: '-' }}</div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="profil-info-group">
                        <div class="profil-info-label">Tempat &amp; Tanggal Lahir</div>
                        <div class="profil-info-value">
                            {{ $warga->tempat_lahir ? $warga->tempat_lahir . ', ' : '' }}
                            {{ $warga->tanggal_lahir ? $warga->tanggal_lahir->translatedFormat('d F Y') : '-' }}
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="profil-info-group">
                        <div class="profil-info-label">Usia</div>
                        <div class="profil-info-value">{{ $warga->umur_formatted }}</div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="profil-info-group">
                        <div class="profil-info-label">Jenis Kelamin</div>
                        <div class="profil-info-value">{{ $warga->gender === 'L' ? 'Laki-laki' : ($warga->gender === 'P' ? 'Perempuan' : '-') }}</div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="profil-info-group">
                        <div class="profil-info-label">Status Perkawinan</div>
                        <div class="profil-info-value">{{ $warga->status_perkawinan ?: 'Kawin' }}</div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="profil-info-group">
                        <div class="profil-info-label">Agama</div>
                        <div class="profil-info-value">{{ $warga->agama ?: 'Islam' }}</div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="profil-info-group">
                        <div class="profil-info-label">Pekerjaan</div>
                        <div class="profil-info-value">{{ $warga->pekerjaan ?: 'Karyawan Swasta' }}</div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="profil-info-group">
                        <div class="profil-info-label">Nomor Handphone / WhatsApp</div>
                        <div class="profil-info-value">{{ $warga->nomor_hp ?: '-' }}</div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="profil-info-group">
                        <div class="profil-info-label">Alamat Email Akun Warga</div>
                        <div class="profil-info-value">
                            @if($warga->user && $warga->user->email)
                                <span class="text-dark fw-bold font-monospace"><i class="fas fa-envelope text-primary me-1"></i>{{ $warga->user->email }}</span>
                            @else
                                <span class="text-muted fst-italic">Belum memiliki akun login</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="profil-info-group">
                        <div class="profil-info-label">Blok &amp; Nomor Rumah</div>
                        <div class="profil-info-value">{{ $warga->blok_rumah ?: '-' }}</div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="profil-info-group">
                        <div class="profil-info-label">RFID UID E-KTP</div>
                        <div class="profil-info-value font-monospace">
                            <code>{{ $warga->rfid_uid ?: 'Belum Direkam' }}</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 6.B. INFORMASI KEPENDUDUKAN -->
    <div class="tab-pane fade" id="pane-kependudukan" role="tabpanel" aria-labelledby="tab-kependudukan">
        <div class="card p-4 shadow-sm" style="border-radius: 16px; border: 1px solid var(--nx-border);">
            <h5 class="fw-bold mb-3" style="color: var(--nx-forest);">
                <i class="fas fa-building-flag me-2"></i>Status &amp; Domisili Kependudukan
            </h5>
            <div class="row g-3">
                <div class="col-12">
                    <div class="profil-info-group">
                        <div class="profil-info-label">Alamat Lengkap Domisili</div>
                        <div class="profil-info-value">{{ $warga->alamat ?: '-' }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="profil-info-group">
                        <div class="profil-info-label">Wilayah RT / RW</div>
                        <div class="profil-info-value">RT {{ $warga->rt_number }} / RW {{ $warga->rw_number ?? '018' }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="profil-info-group">
                        <div class="profil-info-label">Ketua RT Wilayah</div>
                        <div class="profil-info-value">{{ $ketuaUser ? $ketuaUser->name : 'Ketua RT ' . $warga->rt_number }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="profil-info-group">
                        <div class="profil-info-label">Status Kependudukan</div>
                        <div class="profil-info-value text-capitalize">{{ $warga->status_warga ?: 'aktif' }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="profil-info-group">
                        <div class="profil-info-label">Status Verifikasi Berkas</div>
                        <div class="profil-info-value text-capitalize">{{ $warga->status_verifikasi ?: 'menunggu' }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="profil-info-group">
                        <div class="profil-info-label">Tanggal Pendaftaran Sistem</div>
                        <div class="profil-info-value">{{ $warga->created_at ? $warga->created_at->translatedFormat('d F Y, H:i') : '-' }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="profil-info-group">
                        <div class="profil-info-label">Tanggal Pengesahan Verifikasi</div>
                        <div class="profil-info-value">{{ $warga->tanggal_verifikasi ? $warga->tanggal_verifikasi->translatedFormat('d F Y, H:i') : 'Belum diverifikasi' }}</div>
                    </div>
                </div>
                @if(!empty($warga->catatan_verifikasi))
                    <div class="col-12">
                        <div class="p-3 rounded-3" style="background: var(--nx-warning-soft); border: 1px solid #F6D89E;">
                            <div class="small fw-bold text-uppercase text-warning mb-1">Catatan Verifikasi Terakhir</div>
                            <div class="text-dark">{{ $warga->catatan_verifikasi }}</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- 6.C. INFORMASI KELUARGA & KK -->
    <div class="tab-pane fade" id="pane-keluarga" role="tabpanel" aria-labelledby="tab-keluarga">
        <div class="card p-4 shadow-sm" style="border-radius: 16px; border: 1px solid var(--nx-border);">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3 pb-3 border-bottom">
                <div>
                    <h5 class="fw-bold mb-1" style="color: var(--nx-forest);">
                        <i class="fas fa-house-chimney-user me-2"></i>Susunan Anggota Kartu Keluarga
                    </h5>
                    <p class="text-muted small mb-0">
                        Nomor KK: <strong class="font-monospace text-dark">{{ $warga->no_kk ?: 'Belum Terisi' }}</strong>
                        &bull; Kepala Keluarga: <strong>{{ $kepalaKeluarga ? $kepalaKeluarga->nama : '-' }}</strong>
                    </p>
                </div>
                <span class="badge" style="background: var(--nx-mint); color: var(--nx-forest); font-weight: 700; font-size: 0.85rem;">
                    {{ count($semuaKeluarga) }} Anggota Terdata
                </span>
            </div>

            <div class="table-responsive rounded-3" style="border: 1px solid var(--nx-border);">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                    <thead style="background: var(--nx-mint-soft); color: var(--nx-forest); font-weight: 700;">
                        <tr>
                            <th class="ps-3 py-3">Nama Anggota</th>
                            <th>Hubungan</th>
                            <th>NIK</th>
                            <th>Gender</th>
                            <th>Usia</th>
                            <th>Status Verifikasi</th>
                            <th class="pe-3 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($semuaKeluarga as $fam)
                            @php
                                $mFamNik = $fam->nik ? (strlen($fam->nik) >= 10 ? substr($fam->nik, 0, 6) . '******' . substr($fam->nik, -4) : $fam->nik) : '-';
                                $isCurrent = $fam->id === $warga->id;
                            @endphp
                            <tr class="{{ $isCurrent ? 'table-success-subtle' : '' }}">
                                <td class="ps-3 py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $fam->profile_photo_url }}" class="rounded-circle" style="width: 34px; height: 34px; object-fit: cover;">
                                        <div>
                                            <div class="fw-bold text-dark">
                                                {{ $fam->nama }}
                                                @if($isCurrent)
                                                    <span class="badge bg-success ms-1" style="font-size: 0.65rem;">Sedang Dilihat</span>
                                                @endif
                                            </div>
                                            <small class="text-muted">{{ $fam->blok_rumah ?: '-' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($fam->is_kk)
                                        <span class="badge" style="background: var(--nx-forest); color: #fff;">Kepala Keluarga</span>
                                    @else
                                        <span class="badge bg-light text-muted border">{{ $fam->status_hubungan }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="font-monospace text-muted" style="font-size: 0.8rem;">{{ $mFamNik }}</span>
                                </td>
                                <td>{{ $fam->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                <td>{{ $fam->umur_formatted }}</td>
                                <td>
                                    @if($fam->status_verifikasi === 'terverifikasi')
                                        <span class="badge" style="background: #E8F5E9; color: #2E7D32;">Terverifikasi</span>
                                    @else
                                        <span class="badge" style="background: var(--nx-warning-soft); color: var(--nx-warning);">{{ ucfirst($fam->status_verifikasi) }}</span>
                                    @endif
                                </td>
                                <td class="pe-3 text-end">
                                    @if(!$isCurrent)
                                        <a href="{{ route('warga.show', $fam->id) }}" class="btn btn-sm btn-outline-success" style="border-radius: 8px; font-size: 0.78rem;">
                                            <i class="fas fa-id-card me-1"></i> Buka Profil
                                        </a>
                                    @else
                                        <span class="text-muted small">&bull;</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    Belum ada anggota keluarga lain yang terhubung dengan nomor KK ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 6.D. DOKUMEN PENDUKUNG -->
    <div class="tab-pane fade" id="pane-dokumen" role="tabpanel" aria-labelledby="tab-dokumen">
        <div class="card p-4 shadow-sm" style="border-radius: 16px; border: 1px solid var(--nx-border);">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0" style="color: var(--nx-forest);">
                    <i class="fas fa-folder-closed me-2"></i>Berkas Dokumen Pendukung
                </h5>
                <span class="badge" style="background: var(--nx-mint-soft); color: var(--nx-forest);">
                    <i class="fas fa-lock me-1"></i> Akses Terlindungi Petugas
                </span>
            </div>

            <div class="row g-3">
                <!-- Berkas KTP -->
                <div class="col-md-6">
                    <div class="p-3 rounded-3" style="background: #FFFFFF; border: 1px solid var(--nx-border);">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-bold text-dark"><i class="fas fa-id-badge text-success me-2"></i>Foto / Kartu Tanda Penduduk (KTP)</span>
                            <span class="badge bg-light text-muted border">Identitas</span>
                        </div>
                        @if($warga->profile_photo)
                            <div class="text-center p-3 bg-light rounded-3 mb-2">
                                <img src="{{ $warga->profile_photo_url }}" alt="Dokumen KTP" class="img-fluid rounded" style="max-height: 160px; object-fit: contain;">
                            </div>
                            <small class="text-muted d-block mb-2">Foto resmi identitas terdaftar.</small>
                        @else
                            <div class="text-center p-4 bg-light rounded-3 mb-2 text-muted small">
                                <i class="fas fa-file-circle-xmark fa-2x mb-2 d-block opacity-50"></i>
                                Belum ada berkas scan KTP yang diunggah
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Berkas Tambahan / Dokumen Verifikasi -->
                <div class="col-md-6">
                    <div class="p-3 rounded-3" style="background: #FFFFFF; border: 1px solid var(--nx-border);">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-bold text-dark"><i class="fas fa-file-invoice text-primary me-2"></i>Dokumen Pengesahan KK / Mutasi</span>
                            <span class="badge bg-light text-muted border">Lampiran</span>
                        </div>
                        @if(!empty($warga->dokumen_pendukung))
                            <div class="text-center p-4 bg-light rounded-3 mb-2 text-muted small">
                                <i class="fas fa-file-pdf fa-2x text-danger mb-2 d-block"></i>
                                Dokumen digital tersedia: <code>{{ basename($warga->dokumen_pendukung) }}</code>
                            </div>
                            <a href="{{ asset('storage/' . $warga->dokumen_pendukung) }}" class="btn btn-sm btn-outline-primary w-100" target="_blank">
                                <i class="fas fa-arrow-up-right-from-square me-1"></i> Buka Dokumen Pendukung
                            </a>
                        @else
                            <div class="text-center p-4 bg-light rounded-3 mb-2 text-muted small">
                                <i class="fas fa-file-dashed-line fa-2x mb-2 d-block opacity-50"></i>
                                Tidak ada dokumen tambahan lampiran
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 6.E. RIWAYAT KEPENDUDUKAN & AUDIT (Point 6.E & 7) -->
    <div class="tab-pane fade" id="pane-riwayat" role="tabpanel" aria-labelledby="tab-riwayat">
        <div class="card p-4 shadow-sm" style="border-radius: 16px; border: 1px solid var(--nx-border);">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    <h5 class="fw-bold mb-1" style="color: var(--nx-forest);">
                        <i class="fas fa-timeline me-2"></i>Kronologi &amp; Riwayat Kependudukan
                    </h5>
                    <p class="text-muted small mb-0">
                        Jejak digital pendaftaran, verifikasi, mutasi wilayah, dan perubahan data kependudukan.
                    </p>
                </div>
                <span class="badge" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-weight: 700;">
                    Terintegrasi Audit Log &amp; Mutasi
                </span>
            </div>

            <!-- TIMELINE ITEMS -->
            <div class="px-2">
                <!-- 1. Riwayat Verifikasi Terkini -->
                @if($warga->tanggal_verifikasi)
                    <div class="timeline-item">
                        <div class="timeline-dot" style="background: #E8F5E9; color: #2E7D32;">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <div class="p-3 rounded-3" style="background: #F9FBF9; border: 1px solid var(--nx-border);">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong class="text-dark">Keputusan Verifikasi Resmi</strong>
                                <small class="text-muted">{{ $warga->tanggal_verifikasi->translatedFormat('d F Y, H:i') }}</small>
                            </div>
                            <p class="text-muted small mb-1">
                                Status disahkan menjadi <strong class="text-uppercase text-success">{{ $warga->status_verifikasi }}</strong>
                                oleh {{ $warga->verifikator ? $warga->verifikator->name : 'Admin RW' }}.
                            </p>
                            @if($warga->catatan_verifikasi)
                                <div class="p-2 rounded bg-white border small text-dark mt-2">
                                    <strong>Catatan:</strong> {{ $warga->catatan_verifikasi }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- 2. Riwayat Mutasi Warga -->
                @forelse($riwayatMutasi as $mut)
                    <div class="timeline-item">
                        <div class="timeline-dot" style="background: var(--nx-powder); color: #2b567a;">
                            <i class="fas fa-arrows-split-up-and-left"></i>
                        </div>
                        <div class="p-3 rounded-3" style="background: var(--nx-powder-soft); border: 1px solid var(--nx-pastel-blue);">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong style="color: #2b567a;">
                                    Mutasi Kependudukan: {{ strtoupper($mut->tipe) }}
                                </strong>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($mut->tanggal_mutasi)->translatedFormat('d F Y') }}</small>
                            </div>
                            <div class="small text-dark mb-1">
                                <span><strong>Dari:</strong> RT {{ $mut->rt_asal ?: '-' }}</span> &rarr;
                                <span><strong>Ke:</strong> RT {{ $mut->rt_tujuan ?: '-' }}</span>
                            </div>
                            <p class="text-muted small mb-0">
                                <strong>Alasan:</strong> {{ $mut->alasan ?: '-' }}
                            </p>
                            @if($mut->creator)
                                <small class="text-muted d-block mt-1" style="font-size: 0.74rem;">
                                    Dicatat oleh: {{ $mut->creator->name }}
                                </small>
                            @endif
                        </div>
                    </div>
                @empty
                @endforelse

                <!-- 3. Riwayat Audit Log -->
                @forelse($auditLogs as $log)
                    <div class="timeline-item">
                        <div class="timeline-dot" style="background: #F3F4F6; color: #4B5563;">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="p-3 rounded-3" style="background: #FFFFFF; border: 1px solid var(--nx-border);">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong class="text-dark small text-uppercase">Aktivitas: {{ $log->event }} ({{ $log->module }})</strong>
                                <small class="text-muted" style="font-size: 0.75rem;">{{ $log->created_at->translatedFormat('d M Y, H:i') }}</small>
                            </div>
                            <p class="small text-muted mb-0">{{ $log->description }}</p>
                            <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">
                                Pelaku: <strong>{{ $log->user_name }}</strong> ({{ $log->role }}) &bull; IP: {{ $log->ip_address }}
                            </small>
                        </div>
                    </div>
                @empty
                @endforelse

                <!-- 4. Pendaftaran Awal -->
                <div class="timeline-item">
                    <div class="timeline-dot" style="background: var(--nx-mint); color: var(--nx-forest);">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="p-3 rounded-3" style="background: var(--nx-mint-soft); border: 1px solid var(--nx-sage);">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong style="color: var(--nx-forest);">Pendaftaran Awal Data Warga</strong>
                            <small class="text-muted">{{ $warga->created_at ? $warga->created_at->translatedFormat('d F Y, H:i') : '-' }}</small>
                        </div>
                        <p class="text-muted small mb-0">
                            Warga terdaftar pertama kali di sistem kependudukan lingkungan RT {{ $warga->rt_number }} / RW 018.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL VERIFIKASI DARI PROFIL (Khusus Admin RW) -->
@if(Auth::user()->isAdminRw())
<div class="modal fade" id="modalAksiVerifWarga" tabindex="-1" aria-labelledby="modalAksiVerifLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none;">
            <div class="modal-header" style="background: var(--nx-forest); color: #fff;">
                <h5 class="modal-title fw-bold" id="modalAksiVerifLabel">
                    <i class="fas fa-clipboard-check me-2"></i>Verifikasi Warga: {{ $warga->nama }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('warga.verify_rw', $warga->id) }}" id="formVerifProfil">
                @csrf
                <input type="hidden" name="status_verifikasi" id="statusVerifProfil" value="terverifikasi">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Catatan Keputusan RW</label>
                        <textarea name="catatan_verifikasi" id="catatanVerifProfil" class="form-control" rows="3"
                                  placeholder="Wajib diisi jika status Minta Perbaikan atau Tolak..."></textarea>
                    </div>
                    <div class="alert alert-light border small text-muted">
                        <i class="fas fa-info-circle me-1 text-primary"></i> Keputusan verifikasi akan langsung dicatat ke audit log kependudukan resmi RW 018.
                    </div>
                </div>
                <div class="modal-footer border-top d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-danger" onclick="submitProfilVerif('ditolak')">
                        <i class="fas fa-times-circle me-1"></i> Tolak
                    </button>
                    <button type="button" class="btn btn-outline-warning" style="color: #92400E; border-color: #F59E0B;" onclick="submitProfilVerif('perbaikan')">
                        <i class="fas fa-wrench me-1"></i> Minta Perbaikan
                    </button>
                    <button type="button" class="btn btn-success" style="background: var(--nx-forest);" onclick="submitProfilVerif('terverifikasi')">
                        <i class="fas fa-check-circle me-1"></i> Setujui
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
    let isNikVisible = false;
    const realNik = @json($warga->nik ?: '-');
    const maskedNik = @json($maskedNik);

    function toggleNikVisibility() {
        const el = document.getElementById('txtNikProfil');
        const ic = document.getElementById('icToggleNik');
        if (!el || !realNik) return;

        isNikVisible = !isNikVisible;
        if (isNikVisible) {
            el.textContent = realNik;
            ic.className = 'fas fa-eye-slash';
        } else {
            el.textContent = maskedNik;
            ic.className = 'fas fa-eye';
        }
    }

    function submitProfilVerif(status) {
        const catatan = document.getElementById('catatanVerifProfil').value.trim();
        if ((status === 'perbaikan' || status === 'ditolak') && catatan === '') {
            alert('Catatan verifikasi wajib diisi untuk tindakan Perlu Perbaikan atau Penolakan.');
            document.getElementById('catatanVerifProfil').focus();
            return;
        }

        const confirmText = status === 'terverifikasi'
            ? 'Setujui dan sahkan data warga ini?'
            : (status === 'perbaikan'
                ? 'Kembalikan pengajuan ke warga/RT untuk diperbaiki?'
                : 'Yakin ingin menolak pengajuan ini?');

        if (confirm(confirmText)) {
            document.getElementById('statusVerifProfil').value = status;
            document.getElementById('formVerifProfil').submit();
        }
    }
</script>
@endpush
@endsection
