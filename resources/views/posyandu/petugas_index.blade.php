@extends('layouts.app')

@section('title', 'Manajemen Petugas Posyandu — NexaNest')

@section('content')
<!-- ==================== HEADER HALAMAN ==================== -->
<div class="nx-page-header mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
            <span class="badge rounded-pill px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
                <i class="fas fa-heart-pulse me-1"></i> Tata Kelola &amp; RBAC Posyandu
            </span>
            <span class="badge bg-dark rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                <i class="fas fa-shield-halved me-1"></i> Admin RW Only
            </span>
        </div>
        <h1 class="nx-page-title">Manajemen Petugas Posyandu RW 018</h1>
        <p class="nx-page-subtitle">
            Pusat penugasan resmi kader posyandu &amp; tenaga kesehatan, verifikasi kualifikasi SK/STR, pembatasan wilayah kerja RT, serta pencabutan hak akses operasional secara instan.
        </p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('posyandu.index') }}" class="btn btn-outline-secondary fw-bold" style="border-radius: 12px;">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Posyandu
        </a>
        <button type="button" class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#modalCreatePetugas" style="border-radius: 12px; background: var(--nx-forest); border-color: var(--nx-forest);">
            <i class="fas fa-user-plus me-1"></i> Tugaskan Petugas Baru
        </button>
    </div>
</div>

<!-- ==================== ALERTS ==================== -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert" style="background: var(--nx-mint-soft); border-color: var(--nx-sage); color: var(--nx-forest); border-radius: 14px;">
    <i class="fas fa-check-circle me-2 fs-5"></i>
    <div>{{ session('success') }}</div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert" style="border-radius: 14px;">
    <i class="fas fa-exclamation-circle me-2 fs-5"></i>
    <div>{{ session('error') }}</div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- ==================== 4 STAT CARDS UTAMA ==================== -->
<div class="row g-3 mb-4">
    <!-- 1. Total Penugasan -->
    <div class="col-lg-3 col-md-6 col-6">
        <x-stat-card 
            title="Total Riwayat Petugas" 
            value="{{ $totalPetugas }}" 
            sub="Semua SK diterbitkan" 
            icon="fas fa-users-gear" 
            theme="forest" 
        />
    </div>

    <!-- 2. Petugas Aktif Valid -->
    <div class="col-lg-3 col-md-6 col-6">
        <x-stat-card 
            title="Petugas Aktif Valid" 
            value="{{ $totalAktif }}" 
            sub="Izin operasional aktif" 
            icon="fas fa-user-check" 
            theme="success" 
            valColor="#059669"
        />
    </div>

    <!-- 3. Kader Posyandu -->
    <div class="col-lg-3 col-md-6 col-6">
        <x-stat-card 
            title="Kader Posyandu" 
            value="{{ $totalKader }}" 
            sub="Penginput KMS/ANC RT" 
            icon="fas fa-hand-holding-medical" 
            theme="primary" 
            valColor="#1F567A"
        />
    </div>

    <!-- 4. Tenaga Kesehatan -->
    <div class="col-lg-3 col-md-6 col-6">
        <x-stat-card 
            title="Tenaga Kesehatan" 
            value="{{ $totalNakes }}" 
            sub="Bidan desa & validator medis" 
            icon="fas fa-user-doctor" 
            theme="indigo" 
            valColor="#4F46E5"
        />
    </div>
</div>

<!-- ==================== SECURITY & RBAC NOTICE ==================== -->
<div class="alert alert-info border-0 shadow-sm d-flex align-items-start mb-4 p-3.5" style="background: linear-gradient(135deg, #EBF8FF 0%, #DCEAF5 100%); border-left: 5px solid #3182CE !important; border-radius: 16px;">
    <div class="rounded-circle d-inline-flex align-items-center justify-content-center text-primary me-3 shrink-0" style="width: 36px; height: 36px; background: rgba(49, 130, 206, 0.15);">
        <i class="fas fa-shield-halved fa-lg"></i>
    </div>
    <div class="small">
        <strong class="text-primary d-block mb-1" style="font-size: 0.9rem;">Prinsip Keamanan Role-Based Access Control (RBAC) Posyandu NexaNest:</strong>
        <p class="text-secondary mb-0" style="line-height: 1.5;">
            Sistem autentikasi utama tetap murni 3 role: <strong>Admin RW</strong>, <strong>Admin RT</strong>, dan <strong>Warga</strong>. Penugasan sebagai <strong>Kader Posyandu</strong> atau <strong>Tenaga Kesehatan (Bidan/Dokter)</strong> diberikan berbasis akun yang sudah terdaftar tanpa mengubah role dasar. Akses rekam medis individual balita dan ibu hamil dijaga ketat: Kader hanya mencatat wilayah RT-nya, Nakes memvalidasi diagnosa klinis, Warga hanya melihat rekam medis keluarga sendiri, dan Admin RT dibatasi ke rekapitulasi statistik wilayah.
        </p>
    </div>
</div>

<!-- ==================== FILTER & TABEL DAFTAR PETUGAS ==================== -->
<div class="card p-0 mb-4 border-0 shadow-sm overflow-hidden" style="border-radius: 18px; border: 1px solid var(--nx-border) !important;">
    <!-- Filter Bar -->
    <div class="p-3.5 border-bottom bg-light">
        <form action="{{ route('posyandu.petugas.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0" placeholder="Cari nama petugas, jabatan, email, nomor SK/STR...">
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <select name="tipe" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="semua" {{ request('tipe') == 'semua' || !request('tipe') ? 'selected' : '' }}>Semua Tipe Petugas</option>
                    <option value="kader" {{ request('tipe') == 'kader' ? 'selected' : '' }}>Kader Posyandu</option>
                    <option value="tenaga_kesehatan" {{ request('tipe') == 'tenaga_kesehatan' ? 'selected' : '' }}>Tenaga Kesehatan (Bidan/Dokter)</option>
                </select>
            </div>
            <div class="col-md-2 col-sm-6">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="semua" {{ request('status') == 'semua' || !request('status') ? 'selected' : '' }}>Semua Status</option>
                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif / Selesai</option>
                    <option value="dicabut" {{ request('status') == 'dicabut' ? 'selected' : '' }}>Dicabut Akses</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                @if(request()->anyFilled(['search', 'tipe', 'status']))
                <a href="{{ route('posyandu.petugas.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filter">
                    <i class="fas fa-rotate-left"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.84rem;">
            <thead style="background: var(--nx-bg); color: var(--nx-forest); font-weight: 700;">
                <tr>
                    <th class="ps-4">Petugas &amp; Akun</th>
                    <th>Tipe &amp; Jabatan</th>
                    <th>Legalitas &amp; Dokumen (SK/STR)</th>
                    <th>Wilayah Tugas</th>
                    <th>Masa Berlaku</th>
                    <th class="text-center">Status Akses</th>
                    <th class="text-end pe-4">Aksi Manajemen</th>
                </tr>
            </thead>
            <tbody>
                @forelse($petugasList as $p)
                @php
                    $tb = $p->tipe_badge;
                    $sb = $p->status_badge;
                @endphp
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center gap-2.5">
                            <span class="rounded-circle d-inline-flex align-items-center justify-content-center text-white fw-bold shrink-0"
                                  style="width: 38px; height: 38px; background: var(--nx-forest); font-size: 0.8rem;">
                                {{ strtoupper(substr($p->user ? $p->user->name : 'P', 0, 2)) }}
                            </span>
                            <div>
                                <strong class="d-block text-dark">{{ $p->user ? $p->user->name : 'Akun Tidak Ditemukan' }}</strong>
                                <small class="text-muted d-block" style="font-size: 0.74rem;">{{ $p->user ? $p->user->email : '-' }}</small>
                                <span class="badge bg-light text-secondary border px-1.5 py-0.5" style="font-size: 0.65rem;">
                                    Akun: {{ $p->user ? strtoupper($p->user->role) : 'N/A' }}
                                </span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex flex-column gap-1">
                            <div>
                                <span class="badge rounded-pill px-2.5 py-1" style="background: {{ $tb['bg'] }}; color: {{ $tb['color'] }}; font-size: 0.72rem; font-weight: 700;">
                                    <i class="fas {{ $tb['icon'] }} me-1"></i>{{ $tb['label'] }}
                                </span>
                            </div>
                            <strong class="text-dark">{{ $p->jabatan }}</strong>
                            <small class="text-muted" style="font-size: 0.74rem;">{{ $p->instansi }}</small>
                        </div>
                    </td>
                    <td>
                        <div>
                            <span class="fw-bold text-dark d-block">
                                <i class="fas fa-id-card text-muted me-1"></i>{{ $p->nomor_sk_str ?: 'Tidak Ada No SK/STR' }}
                            </span>
                            <small class="text-muted text-truncate d-block" style="max-width: 200px; font-size: 0.74rem;" title="{{ $p->kualifikasi_dokumen }}">
                                {{ $p->kualifikasi_dokumen ?: 'Tanpa lampiran keterangan' }}
                            </small>
                            <div class="mt-1">
                                @if($p->status_verifikasi === 'terverifikasi')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5" style="font-size: 0.68rem;">
                                        <i class="fas fa-circle-check me-1"></i> Terverifikasi
                                    </span>
                                @elseif($p->status_verifikasi === 'ditolak')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0.5" style="font-size: 0.68rem;">
                                        <i class="fas fa-circle-xmark me-1"></i> Ditolak
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-0.5" style="font-size: 0.68rem;">
                                        <i class="fas fa-clock me-1"></i> Menunggu Verifikasi
                                    </span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge px-2.5 py-1" style="background: var(--nx-powder); color: var(--nx-forest); font-size: 0.75rem; font-weight: 700;">
                            <i class="fas fa-location-dot me-1"></i>{{ $p->wilayah_teks }}
                        </span>
                        <small class="text-muted d-block mt-0.5" style="font-size: 0.72rem;">{{ $p->posyandu_nama }}</small>
                    </td>
                    <td>
                        <div class="small" style="font-size: 0.75rem;">
                            <div><span class="text-muted">Mulai:</span> {{ $p->tanggal_mulai ? $p->tanggal_mulai->format('d/m/Y') : '-' }}</div>
                            <div><span class="text-muted">Sampai:</span> {{ $p->tanggal_selesai ? $p->tanggal_selesai->format('d/m/Y') : 'Penugasan Tetap' }}</div>
                            @if($p->tanggal_selesai && $p->tanggal_selesai->isPast() && $p->status === 'aktif')
                                <span class="badge bg-danger-subtle text-danger px-1.5 py-0.5" style="font-size: 0.65rem;">Masa Tugas Habis</span>
                            @endif
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="badge rounded-pill px-2.5 py-1" style="background: {{ $sb['bg'] }}; color: {{ $sb['color'] }}; font-size: 0.72rem; font-weight: 700;">
                            <i class="fas {{ $sb['icon'] }} me-1"></i>{{ $sb['label'] }}
                        </span>
                        @if($p->status === 'dicabut')
                            <small class="text-danger d-block mt-0.5 text-truncate" style="max-width: 130px; font-size: 0.7rem;" title="{{ $p->revocation_reason }}">
                                {{ $p->revocation_reason }}
                            </small>
                        @endif
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-inline-flex align-items-center gap-1">
                            <!-- Tombol Verifikasi Dokumen -->
                            <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2" title="Verifikasi Kualifikasi SK/STR"
                                    onclick="openVerifyModal({{ $p->id }}, '{{ addslashes($p->user ? $p->user->name : '') }}', '{{ $p->status_verifikasi }}', '{{ addslashes($p->catatan_verifikasi ?? '') }}')">
                                <i class="fas fa-certificate"></i>
                            </button>

                            <!-- Tombol Edit -->
                            <button type="button" class="btn btn-sm btn-outline-warning py-1 px-2 text-dark" title="Edit Data Penugasan"
                                    onclick="openEditModal({{ json_encode($p) }})">
                                <i class="fas fa-pen-to-square"></i>
                            </button>

                            <!-- Tombol Cabut Hak Akses Langsung -->
                            @if($p->status === 'aktif')
                            <button type="button" class="btn btn-sm btn-outline-danger py-1 px-2" title="Cabut Hak Akses Langsung"
                                    onclick="openRevokeModal({{ $p->id }}, '{{ addslashes($p->user ? $p->user->name : '') }}', '{{ addslashes($p->jabatan) }}')">
                                <i class="fas fa-user-slash"></i>
                            </button>
                            @endif

                            <!-- Tombol Hapus -->
                            <form action="{{ route('posyandu.petugas.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus arsip penugasan petugas ini secara permanen?');" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-secondary py-1 px-2 text-danger" title="Hapus Riwayat">
                                    <i class="far fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="fas fa-user-xmark fs-1 text-muted mb-2 d-block opacity-50"></i>
                        <strong class="d-block text-dark">Belum ada data penugasan petugas posyandu.</strong>
                        <small>Silakan klik tombol "Tugaskan Petugas Baru" di kanan atas untuk menerbitkan SK.</small>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- ==================== RIWAYAT AUDIT LOG PENUGASAN ==================== -->
<div class="card p-4 mb-4 border-0 shadow-sm" style="border-radius: 18px; border: 1px solid var(--nx-border) !important;">
    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom flex-wrap gap-2">
        <div>
            <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.05rem;">
                <i class="fas fa-clock-rotate-left text-success me-2"></i>Log Audit &amp; Riwayat Perubahan Penugasan Posyandu
            </h5>
            <small class="text-muted">Catatan jejak kepatuhan dan histori penugasan/pencabutan hak akses oleh Admin RW.</small>
        </div>
        <span class="badge rounded-pill px-3 py-1.5" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-weight: 700;">
            {{ $assignmentLogs->count() }} Aktivitas Tercatat
        </span>
    </div>

    <div class="d-flex flex-column gap-2.5">
        @forelse($assignmentLogs as $log)
        <div class="p-3 rounded-3 bg-light border d-flex align-items-start gap-3 small">
            <span class="rounded-circle d-inline-flex align-items-center justify-content-center shrink-0 mt-0.5
                @if($log->action === 'assign') bg-success text-white
                @elseif($log->action === 'revoke') bg-danger text-white
                @elseif($log->action === 'verify') bg-primary text-white
                @else bg-warning text-dark @endif" style="width: 30px; height: 30px; font-size: 0.75rem;">
                @if($log->action === 'assign') <i class="fas fa-user-plus"></i>
                @elseif($log->action === 'revoke') <i class="fas fa-user-slash"></i>
                @elseif($log->action === 'verify') <i class="fas fa-certificate"></i>
                @else <i class="fas fa-pen-nib"></i> @endif
            </span>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-center mb-0.5">
                    <strong class="text-dark">{{ $log->user ? $log->user->name : 'Sistem' }}</strong>
                    <small class="text-muted">{{ $log->created_at ? $log->created_at->format('d M Y, H:i') : '-' }}</small>
                </div>
                <p class="text-muted mb-0" style="line-height: 1.4;">{{ $log->description }}</p>
            </div>
        </div>
        @empty
        <div class="text-center py-4 text-muted small">
            Belum ada aktivitas audit penugasan posyandu.
        </div>
        @endforelse
    </div>
</div>

<!-- ==================== MODAL TUGASKAN PETUGAS BARU ==================== -->
<div class="modal fade" id="modalCreatePetugas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 18px;">
            <div class="modal-header border-bottom-0 pb-0">
                <div>
                    <h5 class="fw-bold modal-title" style="color: var(--nx-forest);">
                        <i class="fas fa-user-plus me-1"></i> Tugaskan Petugas Posyandu Baru
                    </h5>
                    <p class="text-muted small mb-0">Admin RW memberikan penugasan resmi operasional modul Posyandu.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('posyandu.petugas.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Pilih Akun Pengguna Terdaftar <span class="text-danger">*</span></label>
                        <select name="user_id" required class="form-select">
                            <option value="">-- Pilih Pengguna Warga / Admin RT --</option>
                            @foreach($eligibleUsers as $eu)
                                <option value="{{ $eu->id }}">
                                    {{ $eu->name }} ({{ $eu->email }}) — Role: {{ strtoupper($eu->role) }} [RT {{ $eu->rt_number ?: '-' }}]
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted" style="font-size: 0.72rem;">Akun terpilih akan diberi hak wewenang posyandu tanpa mengubah role dasarnya.</small>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tipe Penugasan <span class="text-danger">*</span></label>
                            <select name="tipe_petugas" required class="form-select">
                                <option value="kader">Kader Posyandu (Input KMS &amp; Pengukuran)</option>
                                <option value="tenaga_kesehatan">Tenaga Kesehatan (Bidan / Dokter / Validator Medis)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Jabatan / Peran Tugas <span class="text-danger">*</span></label>
                            <input type="text" name="jabatan" required placeholder="Contoh: Kader Balita RT 007 / Bidan Desa" class="form-control">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nomor SK / STR Kualifikasi</label>
                            <input type="text" name="nomor_sk_str" placeholder="Contoh: STR.BD.2024.08819 / SK-RW-018/2026" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Instansi / Unit Kerja</label>
                            <input type="text" name="instansi" value="Posyandu Melati Mandiri RW 018" class="form-control">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Wilayah RT Penugasan <span class="text-danger">*</span></label>
                            <select name="wilayah_rt" required class="form-select">
                                <option value="000">Seluruh Wilayah RW 018 (Pleno)</option>
                                @foreach($rtList as $rt)
                                    <option value="{{ $rt }}">Khusus RT {{ $rt }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Pos / Posyandu</label>
                            <input type="text" name="posyandu_nama" value="Posyandu Melati RW 018" class="form-control">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tanggal Mulai Berlaku <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_mulai" value="{{ date('Y-m-d') }}" required class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tanggal Selesai (Opsional)</label>
                            <input type="date" name="tanggal_selesai" class="form-control">
                            <small class="text-muted" style="font-size: 0.7rem;">Kosongkan jika penugasan tetap tanpa batas waktu.</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Keterangan / Dokumen Kualifikasi</label>
                        <textarea name="kualifikasi_dokumen" rows="2" placeholder="Sertifikat pelatihan antropometri Kemenkes, ijazah kebidanan, dll..." class="form-control"></textarea>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-bold">Catatan Penugasan Khusus Admin RW</label>
                        <textarea name="catatan_penugasan" rows="2" placeholder="Instruksi tugas khusus, pengawasan wilayah rawan stunting..." class="form-control"></textarea>
                    </div>
                </div>

                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold" style="background: var(--nx-forest); border-color: var(--nx-forest);">
                        <i class="fas fa-check me-1"></i> Terbitkan Penugasan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==================== MODAL EDIT PENUGASAN ==================== -->
<div class="modal fade" id="modalEditPetugas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 18px;">
            <div class="modal-header border-bottom-0 pb-0">
                <div>
                    <h5 class="fw-bold modal-title text-warning">
                        <i class="fas fa-pen-to-square me-1"></i> Edit Data Penugasan Petugas
                    </h5>
                    <p class="text-muted small mb-0" id="editPetugasSub">Perbarui data penugasan operasional.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formEditPetugas" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Jabatan / Peran Tugas <span class="text-danger">*</span></label>
                            <input type="text" id="edit_jabatan" name="jabatan" required class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Status Operasional <span class="text-danger">*</span></label>
                            <select id="edit_status" name="status" required class="form-select">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif / Berakhir</option>
                                <option value="dicabut">Dicabut</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nomor SK / STR</label>
                            <input type="text" id="edit_nomor_sk_str" name="nomor_sk_str" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Instansi Unit</label>
                            <input type="text" id="edit_instansi" name="instansi" class="form-control">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Wilayah RT Tugas <span class="text-danger">*</span></label>
                            <select id="edit_wilayah_rt" name="wilayah_rt" required class="form-select">
                                <option value="000">Seluruh Wilayah RW 018 (Pleno)</option>
                                @foreach($rtList as $rt)
                                    <option value="{{ $rt }}">Khusus RT {{ $rt }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Pos Posyandu</label>
                            <input type="text" id="edit_posyandu_nama" name="posyandu_nama" class="form-control">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" id="edit_tanggal_mulai" name="tanggal_mulai" required class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tanggal Selesai</label>
                            <input type="date" id="edit_tanggal_selesai" name="tanggal_selesai" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Keterangan / Dokumen Kualifikasi</label>
                        <textarea id="edit_kualifikasi_dokumen" name="kualifikasi_dokumen" rows="2" class="form-control"></textarea>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-bold">Catatan Penugasan Khusus Admin RW</label>
                        <textarea id="edit_catatan_penugasan" name="catatan_penugasan" rows="2" class="form-control"></textarea>
                    </div>
                </div>

                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning fw-bold text-dark">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==================== MODAL VERIFIKASI KUALIFIKASI ==================== -->
<div class="modal fade" id="modalVerifyPetugas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 18px;">
            <div class="modal-header border-bottom-0 pb-0">
                <div>
                    <h5 class="fw-bold modal-title text-primary">
                        <i class="fas fa-certificate me-1"></i> Verifikasi Kualifikasi Petugas
                    </h5>
                    <p class="text-muted small mb-0" id="verifyUserName">Validasi keabsahan SK / STR petugas.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formVerifyPetugas" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Keputusan Verifikasi <span class="text-danger">*</span></label>
                        <select id="verify_status" name="status_verifikasi" required class="form-select">
                            <option value="terverifikasi">Kualifikasi Valid &amp; Terverifikasi (Diterima)</option>
                            <option value="ditolak">Kualifikasi Tidak Memenuhi Syarat (Ditolak)</option>
                        </select>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-bold">Catatan Hasil Verifikasi Admin RW <span class="text-danger">*</span></label>
                        <textarea id="verify_catatan" name="catatan_verifikasi" rows="3" required placeholder="Tuliskan nomor keabsahan SK/STR atau alasan jika ditolak..." class="form-control"></textarea>
                    </div>
                </div>

                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold">
                        <i class="fas fa-check me-1"></i> Simpan Status Verifikasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==================== MODAL PENCABUTAN HAK AKSES LANGSUNG ==================== -->
<div class="modal fade" id="modalRevokePetugas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 18px;">
            <div class="modal-header border-bottom-0 pb-0">
                <div>
                    <h5 class="fw-bold modal-title text-danger">
                        <i class="fas fa-triangle-exclamation me-1"></i> Pencabutan Hak Akses Langsung
                    </h5>
                    <p class="text-muted small mb-0" id="revokeUserName">Tindakan pencegahan keamanan &amp; integritas data.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="alert alert-danger border-0 p-3 mb-3 small" style="border-radius: 12px; background: var(--nx-soft-red); color: var(--nx-danger);">
                    <strong>Perhatian Penting:</strong>
                    Pencabutan hak akses akan <strong>langsung memutus</strong> wewenang petugas untuk menginput atau memverifikasi rekam medis posyandu secara instan. Tindakan ini akan dicatat ke dalam log audit sistem.
                </div>

                <form id="formRevokePetugas" method="POST">
                    @csrf
                    <div class="mb-0">
                        <label class="form-label small fw-bold">Alasan Pencabutan Hak Akses <span class="text-danger">*</span></label>
                        <textarea name="revocation_reason" rows="3" required placeholder="Contoh: Masa bakti pengurus selesai, mutasi tugas ke luar lingkungan RW 018..." class="form-control"></textarea>
                    </div>
                    <div class="modal-footer border-top-0 px-0 pb-0 mt-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger fw-bold">
                            <i class="fas fa-user-slash me-1"></i> Cabut Hak Akses Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openEditModal(petugas) {
        document.getElementById('formEditPetugas').action = `/posyandu/petugas/${petugas.id}`;
        document.getElementById('editPetugasSub').textContent = `Petugas: ${petugas.user ? petugas.user.name : ''} (${petugas.tipe_petugas === 'tenaga_kesehatan' ? 'Tenaga Kesehatan' : 'Kader'})`;
        document.getElementById('edit_jabatan').value = petugas.jabatan || '';
        document.getElementById('edit_status').value = petugas.status || 'aktif';
        document.getElementById('edit_nomor_sk_str').value = petugas.nomor_sk_str || '';
        document.getElementById('edit_instansi').value = petugas.instansi || '';
        document.getElementById('edit_wilayah_rt').value = petugas.wilayah_rt || '000';
        document.getElementById('edit_posyandu_nama').value = petugas.posyandu_nama || '';
        document.getElementById('edit_tanggal_mulai').value = petugas.tanggal_mulai ? petugas.tanggal_mulai.split('T')[0] : '';
        document.getElementById('edit_tanggal_selesai').value = petugas.tanggal_selesai ? petugas.tanggal_selesai.split('T')[0] : '';
        document.getElementById('edit_kualifikasi_dokumen').value = petugas.kualifikasi_dokumen || '';
        document.getElementById('edit_catatan_penugasan').value = petugas.catatan_penugasan || '';
        
        new bootstrap.Modal(document.getElementById('modalEditPetugas')).show();
    }

    function openVerifyModal(id, userName, status, catatan) {
        document.getElementById('formVerifyPetugas').action = `/posyandu/petugas/${id}/verify`;
        document.getElementById('verifyUserName').textContent = `Verifikasi berkas petugas: ${userName}`;
        document.getElementById('verify_status').value = (status === 'ditolak' ? 'ditolak' : 'terverifikasi');
        document.getElementById('verify_catatan').value = catatan || '';
        
        new bootstrap.Modal(document.getElementById('modalVerifyPetugas')).show();
    }

    function openRevokeModal(id, userName, jabatan) {
        document.getElementById('formRevokePetugas').action = `/posyandu/petugas/${id}/revoke`;
        document.getElementById('revokeUserName').textContent = `Petugas: ${userName} (${jabatan})`;
        
        new bootstrap.Modal(document.getElementById('modalRevokePetugas')).show();
    }
</script>
@endpush
@endsection
