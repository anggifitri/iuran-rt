@extends('layouts.app')

@section('title', 'Data Warga — NexaNest')

@section('content')
<!-- PAGE HEADER -->
<div class="nx-page-header">
    <div>
        <span class="badge rounded-pill mb-2 px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
            <i class="fas fa-users me-1"></i> Administrasi Kependudukan
        </span>
        <h1 class="nx-page-title">Daftar Data Warga</h1>
        <p class="nx-page-subtitle">Manajemen data kependudukan, Kartu Keluarga (KK), dan perekaman E-KTP RFID warga RT/RW.</p>
    </div>

    <div class="d-flex align-items-center flex-wrap gap-2">
        <div class="btn-group shadow-sm" role="group" style="border-radius: 12px; overflow: hidden; border: 1px solid var(--nx-border);">
            <a href="{{ route('warga.index', ['view' => 'family']) }}"
               class="btn btn-sm px-3 py-2 fw-bold {{ $viewMode === 'family' ? 'btn-primary' : 'bg-white text-dark border-0' }}">
                <i class="fas fa-house-user me-1"></i> Tampilan Keluarga
            </a>
            <a href="{{ route('warga.index', ['view' => 'table']) }}"
               class="btn btn-sm px-3 py-2 fw-bold {{ $viewMode === 'table' ? 'btn-primary' : 'bg-white text-dark border-0' }}">
                <i class="fas fa-table me-1"></i> Tampilan Tabel Warga
            </a>
        </div>

        <a href="{{ route('warga.create') }}" class="btn btn-primary">
            <i class="fas fa-user-plus me-1"></i> Tambah Warga
        </a>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-danger mb-4">
        <i class="fas fa-exclamation-triangle me-1"></i> {{ $errors->first() }}
    </div>
@endif

<!-- FILTER & PENCARIAN CEPAT -->
<div class="card p-3 mb-4">
    <div class="row g-2 align-items-center">
        <div class="col-md-8">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" id="wargaSearchInput" class="form-control border-start-0"
                       placeholder="Cari nama warga, email, NIK, No. KK, blok rumah, atau UID E-KTP pada halaman ini...">
            </div>
        </div>
        <div class="col-md-4 text-md-end">
            <span class="badge rounded-pill px-3 py-2" style="background: var(--nx-mint); color: var(--nx-forest); font-weight: 700;">
                <i class="fas fa-id-card me-1"></i> Mode: {{ $viewMode === 'family' ? 'Kartu Keluarga (Accordion)' : 'Tabel Lengkap Warga' }}
            </span>
        </div>
    </div>
</div>

<!-- 1. TAMPILAN TABEL WARGA -->
@if($viewMode === 'table')
    @if($wargas->isEmpty())
        <div class="card p-5 text-center">
            <div class="mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center"
                 style="width: 64px; height: 64px; background: var(--nx-mint-soft); color: var(--nx-forest);">
                <i class="fas fa-users fa-2x"></i>
            </div>
            <h5 class="fw-bold mb-1">Belum Ada Data Warga Terdaftar</h5>
            <p class="text-muted small mb-3">Silakan tambahkan data warga atau Kepala Keluarga baru ke dalam sistem.</p>
            <div>
                <a href="{{ route('warga.create') }}" class="btn btn-primary btn-sm px-4">
                    <i class="fas fa-plus me-1"></i> Tambah Warga Pertama
                </a>
            </div>
        </div>
    @else
        <div class="card nx-table-card overflow-hidden mb-4 shadow-xs">
            <div class="table-responsive nx-table-responsive">
                <table class="table table-hover align-middle nx-table mb-0" id="wargaTable">
                    <thead>
                        <tr>
                            <th class="ps-4">Warga</th>
                            <th>NIK &amp; Status E-KTP</th>
                            <th>No. KK</th>
                            <th>Hubungan</th>
                            <th>Gender / Usia</th>
                            <th>No. HP</th>
                            <th>Domisili RT/RW</th>
                            <th class="pe-4 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($wargas as $warga)
                            <tr class="warga-filter-item">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $warga->profile_photo_url }}" alt="{{ $warga->nama }}"
                                             class="rounded-circle shadow-sm"
                                             style="width: 42px; height: 42px; object-fit: cover; border: 2px solid var(--nx-mint);">
                                        <div>
                                            <div class="fw-bold" style="color: var(--nx-text);">{{ $warga->nama }}</div>
                                            <div class="text-xs text-slate-500" style="font-size: 0.74rem;">
                                                @if($warga->user && $warga->user->email)
                                                    <span class="text-slate-500 font-monospace"><i class="fas fa-envelope text-muted me-1"></i>{{ $warga->user->email }}</span>
                                                @else
                                                    <span class="text-slate-400 fst-italic">Belum punya akun</span>
                                                @endif
                                            </div>
                                            <small class="text-muted">Blok {{ $warga->blok_rumah ?? '-' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold" style="font-family: monospace; font-size: 0.82rem;">{{ $warga->nik ?? '-' }}</div>
                                    @if($warga->rfid_uid)
                                        <span class="badge rounded-pill mt-1" style="background: var(--nx-mint); color: var(--nx-forest); font-size: 0.68rem; font-family: monospace;">
                                            <i class="fas fa-id-card me-1"></i>UID: {{ $warga->rfid_uid }}
                                        </span>
                                    @else
                                        <span class="badge rounded-pill mt-1" style="background: var(--nx-warning-soft); color: var(--nx-warning); font-size: 0.68rem;">
                                            <i class="fas fa-rss me-1"></i>Belum Rekam E-KTP
                                        </span>
                                    @endif
                                </td>
                                <td style="font-family: monospace; font-size: 0.82rem;">{{ $warga->no_kk ?? '-' }}</td>
                                <td>
                                    <span class="badge rounded-pill px-3 py-1"
                                          style="background: {{ $warga->is_kk ? 'var(--nx-forest)' : 'var(--nx-powder)' }}; color: {{ $warga->is_kk ? '#fff' : '#2B5F8E' }}; font-size: 0.72rem;">
                                        {{ $warga->status_hubungan }}
                                    </span>
                                </td>
                                <td>
                                    <div class="small fw-semibold">{{ ($warga->gender == 'L' || $warga->gender == 'Laki-laki') ? 'Laki-laki' : 'Perempuan' }}</div>
                                    <small class="text-muted">
                                        {{ $warga->tanggal_lahir ? \Carbon\Carbon::parse($warga->tanggal_lahir)->translatedFormat('d M Y') : '-' }}
                                        ({{ $warga->umur_formatted }})
                                    </small>
                                </td>
                                <td class="small">{{ $warga->nomor_hp ?? '-' }}</td>
                                <td>
                                    <span class="badge rounded-pill" style="background: var(--nx-mint-soft); color: var(--nx-forest); border: 1px solid var(--nx-sage);">
                                        RT {{ $warga->rt_number }} / RW {{ $warga->rw_number }}
                                    </span>
                                    <div class="text-muted text-truncate mt-1" style="max-width: 160px; font-size: 0.75rem;" title="{{ $warga->alamat }}">
                                        {{ $warga->alamat ?? '-' }}
                                    </div>
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="{{ route('warga.show', $warga->id) }}" class="btn btn-sm btn-outline-success" title="Lihat Detail Profil">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button"
                                            class="btn btn-sm btn-outline-primary btn-rekam-ktp"
                                            title="Rekam E-KTP (RFID RC522)"
                                            data-id="{{ $warga->id }}"
                                            data-nama="{{ $warga->nama }}"
                                            data-nik="{{ $warga->nik }}"
                                            data-rfid="{{ $warga->rfid_uid }}"
                                            data-rt="{{ $warga->rt_number }}">
                                            <i class="fas fa-id-card"></i>
                                        </button>
                                        <a href="{{ route('warga.edit', $warga->id) }}" class="btn btn-sm btn-outline-secondary" title="Edit Data">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('warga.destroy', $warga->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data warga ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

<!-- 2. TAMPILAN KELUARGA (ACCORDION) -->
@else
    <div class="accordion" id="accordionWarga">
        @if($wargas->isEmpty())
            <div class="card p-5 text-center">
                <div class="mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center"
                     style="width: 64px; height: 64px; background: var(--nx-mint-soft); color: var(--nx-forest);">
                    <i class="fas fa-house-user fa-2x"></i>
                </div>
                <h5 class="fw-bold mb-1">Belum Ada Kepala Keluarga Terdaftar</h5>
                <p class="text-muted small mb-3">Silakan klik tombol Tambah Warga untuk mendaftarkan Kepala Keluarga.</p>
            </div>
        @else
            @foreach ($wargas as $kk)
                <div class="card mb-3 overflow-hidden warga-filter-item">
                    <div class="card-header p-3 d-flex justify-content-between align-items-center flex-wrap gap-2"
                         id="heading{{ $kk->id }}"
                         data-bs-toggle="collapse"
                         data-bs-target="#collapse{{ $kk->id }}"
                         style="cursor: pointer;">

                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $kk->profile_photo_url }}" alt="{{ $kk->nama }}"
                                 class="rounded-circle shadow-sm"
                                 style="width: 52px; height: 52px; object-fit: cover; border: 2px solid var(--nx-mint);">
                            <div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <h5 class="fw-bold mb-0" style="font-size: 1.02rem; color: var(--nx-text);">{{ $kk->nama }}</h5>
                                    <span class="badge rounded-pill" style="background: var(--nx-mint); color: var(--nx-forest); font-size: 0.7rem;">
                                        Kepala Keluarga
                                    </span>
                                    @if($kk->rfid_uid)
                                        <span class="badge rounded-pill" style="background: var(--nx-powder); color: #2B5F8E; font-size: 0.68rem; font-family: monospace;">
                                            <i class="fas fa-id-card me-1"></i>{{ $kk->rfid_uid }}
                                        </span>
                                    @endif
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-location-dot text-success me-1"></i>Blok {{ $kk->blok_rumah }} • RT {{ $kk->rt_number }} / RW {{ $kk->rw_number }} • No. KK: {{ $kk->no_kk ?? '-' }}
                                    • <i class="fas fa-envelope text-muted me-1"></i>{{ $kk->user?->email ?: 'Belum punya akun' }}
                                </small>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <span class="badge rounded-pill px-3 py-2" style="background: var(--nx-mint-soft); color: var(--nx-forest); border: 1px solid var(--nx-sage);">
                                <i class="fas fa-users me-1"></i>{{ $kk->anggotaKeluarga->count() + 1 }} Jiwa
                            </span>

                            <a href="{{ route('warga.show', $kk->id) }}" class="btn btn-sm btn-outline-success" onclick="event.stopPropagation();" title="Lihat Profil">
                                <i class="fas fa-eye"></i>
                            </a>

                            <button type="button"
                                class="btn btn-sm btn-outline-primary btn-rekam-ktp"
                                onclick="event.stopPropagation();"
                                data-id="{{ $kk->id }}"
                                data-nama="{{ $kk->nama }}"
                                data-nik="{{ $kk->nik }}"
                                data-rfid="{{ $kk->rfid_uid }}"
                                data-rt="{{ $kk->rt_number }}">
                                <i class="fas fa-id-card me-1"></i>Rekam KTP
                            </button>

                            <a href="{{ route('warga.edit', $kk->id) }}" class="btn btn-sm btn-outline-secondary" onclick="event.stopPropagation();">
                                <i class="fas fa-edit"></i>
                            </a>

                            <form action="{{ route('warga.destroy', $kk->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data KK beserta anggotanya?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="event.stopPropagation();">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div id="collapse{{ $kk->id }}" class="collapse" aria-labelledby="heading{{ $kk->id }}" data-bs-parent="#accordionWarga">
                        <div class="card-body p-4" style="background: var(--nx-bg);">
                            <div class="row g-3">
                                @php
                                    $allMembers = collect([$kk])->concat($kk->anggotaKeluarga);
                                @endphp
                                @foreach ($allMembers as $anggota)
                                    <div class="col-md-6 col-12">
                                        <div class="card h-100 p-3">
                                            <div class="d-flex align-items-start gap-3">
                                                <div class="text-center" style="width: 78px; flex-shrink: 0;">
                                                    <img src="{{ $anggota->profile_photo_url }}" alt="{{ $anggota->nama }}"
                                                         class="rounded-circle shadow-sm"
                                                         style="width: 66px; height: 66px; object-fit: cover; border: 2px solid var(--nx-mint);">
                                                    <span class="badge mt-2 px-2 py-1 rounded-pill"
                                                          style="background: {{ $anggota->is_kk ? 'var(--nx-forest)' : 'var(--nx-powder)' }}; color: {{ $anggota->is_kk ? '#fff' : '#2B5F8E' }}; font-size: 0.65rem;">
                                                        {{ $anggota->status_hubungan }}
                                                    </span>
                                                </div>

                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                                        <div>
                                                            <h6 class="fw-bold mb-1" style="color: var(--nx-text);">{{ $anggota->nama }}</h6>
                                                            @if($anggota->rfid_uid)
                                                                <span class="badge rounded-pill" style="background: var(--nx-mint); color: var(--nx-forest); font-size: 0.66rem; font-family: monospace;">
                                                                    <i class="fas fa-id-card me-1"></i>E-KTP UID: {{ $anggota->rfid_uid }}
                                                                </span>
                                                            @else
                                                                <span class="badge rounded-pill" style="background: var(--nx-warning-soft); color: var(--nx-warning); font-size: 0.66rem;">
                                                                    <i class="fas fa-rss me-1"></i>Belum Rekam E-KTP
                                                                </span>
                                                            @endif
                                                        </div>

                                                        <div class="d-flex align-items-center gap-1">
                                                            <a href="{{ route('warga.show', $anggota->id) }}" class="btn btn-sm btn-outline-success py-1 px-2" style="font-size: 0.72rem;" title="Lihat Profil">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-primary py-1 px-2 btn-rekam-ktp"
                                                                style="font-size: 0.72rem;"
                                                                title="Rekam E-KTP via RFID RC522"
                                                                data-id="{{ $anggota->id }}"
                                                                data-nama="{{ $anggota->nama }}"
                                                                data-nik="{{ $anggota->nik }}"
                                                                data-rfid="{{ $anggota->rfid_uid }}"
                                                                data-rt="{{ $anggota->rt_number }}">
                                                                <i class="fas fa-id-card me-1"></i>Rekam KTP
                                                            </button>
                                                            <a href="{{ route('warga.edit', $anggota->id) }}" class="btn btn-sm btn-outline-secondary py-1 px-2" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <form action="{{ route('warga.destroy', $anggota->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus anggota keluarga ini?')">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" title="Hapus">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>

                                                    <hr class="my-2">

                                                    <div class="row g-2 small">
                                                        <div class="col-6">
                                                            <span class="text-muted d-block" style="font-size: 0.7rem;">NIK</span>
                                                            <span class="fw-semibold" style="font-family: monospace;">{{ $anggota->nik ?? '-' }}</span>
                                                        </div>
                                                        <div class="col-6">
                                                            <span class="text-muted d-block" style="font-size: 0.7rem;">No. KK</span>
                                                            <span class="fw-semibold" style="font-family: monospace;">{{ $anggota->no_kk ?? '-' }}</span>
                                                        </div>
                                                        <div class="col-6">
                                                            <span class="text-muted d-block" style="font-size: 0.7rem;">No. HP</span>
                                                            <span class="fw-semibold">{{ $anggota->nomor_hp ?? '-' }}</span>
                                                        </div>
                                                        <div class="col-6">
                                                            <span class="text-muted d-block" style="font-size: 0.7rem;">Jenis Kelamin</span>
                                                            <span class="fw-semibold">{{ ($anggota->gender == 'L' || $anggota->gender == 'Laki-laki') ? 'Laki-laki' : 'Perempuan' }}</span>
                                                        </div>
                                                        <div class="col-6">
                                                            <span class="text-muted d-block" style="font-size: 0.7rem;">Tanggal Lahir / Usia</span>
                                                            <span class="fw-semibold">{{ $anggota->tanggal_lahir ? \Carbon\Carbon::parse($anggota->tanggal_lahir)->translatedFormat('d M Y') : '-' }} ({{ $anggota->umur_formatted }})</span>
                                                        </div>
                                                        <div class="col-6">
                                                            <span class="text-muted d-block" style="font-size: 0.7rem;">Blok &amp; RT/RW</span>
                                                            <span class="fw-semibold">Blok {{ $anggota->blok_rumah ?? '-' }} • RT {{ $anggota->rt_number }}</span>
                                                        </div>
                                                        <div class="col-12 mt-1">
                                                            <span class="text-muted d-block" style="font-size: 0.7rem;">Akun Email</span>
                                                            @if($anggota->user && $anggota->user->email)
                                                                <span class="fw-semibold text-slate-600 font-monospace" style="font-size: 0.75rem;"><i class="fas fa-envelope text-muted me-1"></i>{{ $anggota->user->email }}</span>
                                                            @else
                                                                <span class="text-slate-400 fst-italic" style="font-size: 0.75rem;">Belum punya akun</span>
                                                            @endif
                                                        </div>
                                                        <div class="col-12">
                                                            <span class="text-muted d-block" style="font-size: 0.7rem;">Alamat Domisili</span>
                                                            <span class="fw-semibold">{{ $anggota->alamat ?? '-' }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endif

<!-- PAGINATION -->
<div class="mt-4 d-flex justify-content-center">
    {{ $wargas->appends(['view' => $viewMode])->links() }}
</div>

<!-- MODAL REKAM E-KTP (NODEMCU ESP32 + RFID RC522) -->
<div class="modal fade" id="rekamKtpModal" tabindex="-1" aria-labelledby="rekamKtpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width: 42px; height: 42px; background: var(--nx-mint); color: var(--nx-forest);">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="rekamKtpModalLabel" style="color: var(--nx-forest);">Rekam E-KTP Warga</h5>
                        <small class="text-muted">NodeMCU ESP32 • Sensor RFID RC522 (13.56 MHz)</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formRekamKtp" method="POST" action="">
                @csrf
                <div class="modal-body p-4">
                    <!-- Badge Status Mode Rekam E-KTP (Tahap 3 Isolasi Smart Gate) -->
                    <div class="d-flex align-items-center gap-2 p-2 px-3 rounded-3 mb-3"
                         style="background: rgba(43, 95, 142, 0.1); border: 1px solid rgba(43, 95, 142, 0.25); color: #1F517F;">
                        <i class="fas fa-shield-halved"></i>
                        <span class="small fw-semibold" style="font-size: 0.78rem;">
                            Mode Rekam E-KTP Aktif — Tap kartu tidak akan membuka gerbang
                        </span>
                    </div>

                    <!-- Target Warga Info -->
                    <div class="p-3 rounded-4 mb-3 d-flex justify-content-between align-items-center"
                         style="background: var(--nx-mint-soft); border: 1px solid var(--nx-sage);">
                        <div>
                            <small class="text-muted d-block fw-bold" style="font-size: 0.7rem;">NAMA WARGA</small>
                            <span id="modalWargaNama" class="fw-bold" style="font-size: 1rem; color: var(--nx-forest);">-</span>
                        </div>
                        <span id="modalWargaRt" class="badge rounded-pill px-3 py-2" style="background: var(--nx-forest); color: #fff;">RT -</span>
                    </div>

                    <!-- Area Status Pemindaian Real-time -->
                    <div id="rfidScannerBox" class="text-center p-4 rounded-4 mb-3 position-relative overflow-hidden"
                         style="background: var(--nx-bg); border: 2px dashed var(--nx-sage); transition: all 0.3s ease;">
                        <div id="rfidPulseIcon" class="mx-auto mb-2 rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 68px; height: 68px; background: var(--nx-powder); color: #2B5F8E;">
                            <i class="fas fa-wifi fa-2x fa-beat-fade"></i>
                        </div>
                        <h6 id="rfidStatusTitle" class="fw-bold mb-1" style="color: var(--nx-text);">Menunggu Tap E-KTP pada RFID RC522...</h6>
                        <p id="rfidStatusDesc" class="text-muted small mb-2">
                            Tempelkan fisik E-KTP warga ke modul <strong>RC522 (NodeMCU ESP32)</strong>. UID kartu akan terisi otomatis.
                        </p>
                        <div id="rfidConflictAlert" class="alert alert-warning py-1.5 px-2 small mb-0 d-none"></div>

                        <div class="mt-2 pt-2 border-top">
                            <button type="button" id="btnSimulateTap" class="btn btn-sm btn-outline-primary" style="font-size: 0.75rem;">
                                <i class="fas fa-microchip me-1"></i>Simulasikan Kiriman NodeMCU32
                            </button>
                        </div>
                    </div>

                    <!-- Input UID RFID E-KTP -->
                    <div class="mb-3">
                        <label class="form-label">UID Chip E-KTP (Hexadecimal dari RC522)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-fingerprint text-success"></i></span>
                            <input type="text" name="rfid_uid" id="inputRfidUid" class="form-control fw-bold"
                                   style="font-family: monospace; letter-spacing: 1px;"
                                   placeholder="Menunggu tap kartu / ketik UID..." required>
                            <button type="button" class="btn btn-outline-secondary" id="btnResetUid" title="Bersihkan UID">
                                <i class="fas fa-redo-alt"></i>
                            </button>
                        </div>
                        <small class="text-muted" style="font-size: 0.72rem;">
                            Endpoint penerima NodeMCU32: <code>POST/GET {{ url('/api/rfid/scan') }}?uid=XXXX</code>
                        </small>
                    </div>

                    <!-- Input / Konfirmasi NIK -->
                    <div class="mb-2">
                        <label class="form-label">NIK Warga (16 Digit)</label>
                        <input type="text" name="nik" id="inputModalNik" class="form-control"
                               style="font-family: monospace;" placeholder="Masukkan / pastikan NIK 16 digit...">
                    </div>
                </div>

                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btnSimpanKtp" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i>Simpan Rekaman E-KTP
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Quick Filter on Current Page
    const searchInput = document.getElementById('wargaSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            document.querySelectorAll('.warga-filter-item').forEach(el => {
                el.style.display = el.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    }

    // RFID RC522 Modal Logic
    const modalEl = document.getElementById('rekamKtpModal');
    if (!modalEl) return;

    const bsModal = new bootstrap.Modal(modalEl);
    const formRekam = document.getElementById('formRekamKtp');
    const namaEl = document.getElementById('modalWargaNama');
    const rtEl = document.getElementById('modalWargaRt');
    const inputUid = document.getElementById('inputRfidUid');
    const inputNik = document.getElementById('inputModalNik');
    const statusTitle = document.getElementById('rfidStatusTitle');
    const statusDesc = document.getElementById('rfidStatusDesc');
    const scannerBox = document.getElementById('rfidScannerBox');
    const pulseIcon = document.getElementById('rfidPulseIcon');
    const conflictAlert = document.getElementById('rfidConflictAlert');
    const btnSimulate = document.getElementById('btnSimulateTap');
    const btnResetUid = document.getElementById('btnResetUid');

    let pollInterval = null;
    let modeKeepAliveInterval = null;
    let modalOpenedAt = 0;
    let currentWargaId = null;

    function activateScanMode(mode) {
        fetch(`{{ route('iot.scan_mode') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ mode: mode })
        }).catch(() => {});
    }

    function startModeKeepAlive() {
        if (modeKeepAliveInterval) clearInterval(modeKeepAliveInterval);
        activateScanMode('rekam_ktp');
        modeKeepAliveInterval = setInterval(function () {
            activateScanMode('rekam_ktp');
        }, 45000);
    }

    function stopModeKeepAlive() {
        if (modeKeepAliveInterval) {
            clearInterval(modeKeepAliveInterval);
            modeKeepAliveInterval = null;
        }
        activateScanMode('smart_gate');
    }

    function setScannerWaiting() {
        scannerBox.style.borderColor = 'var(--nx-sage)';
        scannerBox.style.background = 'var(--nx-bg)';
        pulseIcon.style.background = 'var(--nx-powder)';
        pulseIcon.style.color = '#2B5F8E';
        pulseIcon.innerHTML = '<i class="fas fa-wifi fa-2x fa-beat-fade"></i>';
        statusTitle.textContent = 'Menunggu Tap E-KTP pada RFID RC522...';
        statusDesc.innerHTML = 'Tempelkan fisik E-KTP warga ke modul <strong>RC522 (NodeMCU ESP32)</strong>. UID kartu akan terisi otomatis.';
        conflictAlert.classList.add('d-none');
    }

    function setScannerDetected(scanData) {
        inputUid.value = scanData.uid;
        scannerBox.style.borderColor = '#285943';
        scannerBox.style.background = '#EEF6EF';
        pulseIcon.style.background = '#DCEBDD';
        pulseIcon.style.color = '#285943';
        pulseIcon.innerHTML = '<i class="fas fa-check-circle fa-2x"></i>';
        statusTitle.textContent = 'E-KTP Terdeteksi: ' + scanData.uid;
        statusDesc.textContent = 'Perangkat: ' + (scanData.device_id || 'ESP32-RC522');

        if (scanData.registered_warga && String(scanData.registered_warga.id) !== String(currentWargaId)) {
            conflictAlert.classList.remove('d-none');
            conflictAlert.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>Kartu ini sudah terdaftar atas nama <strong>' +
                scanData.registered_warga.nama + ' (RT ' + scanData.registered_warga.rt_number + ')</strong>.';
        } else {
            conflictAlert.classList.add('d-none');
        }
    }

    function startPolling() {
        stopPolling();
        pollInterval = setInterval(function () {
            fetch(`{{ route('warga.rfid.latest') }}?since=${modalOpenedAt}`, {
                headers: { 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(res => {
                if (res.status === 'detected' && res.data && res.data.uid) {
                    modalOpenedAt = res.data.timestamp_ms;
                    setScannerDetected(res.data);
                }
            })
            .catch(() => {});
        }, 1500);
    }

    function stopPolling() {
        if (pollInterval) {
            clearInterval(pollInterval);
            pollInterval = null;
        }
    }

    document.querySelectorAll('.btn-rekam-ktp').forEach(btn => {
        btn.addEventListener('click', function () {
            currentWargaId = this.dataset.id;
            const nama = this.dataset.nama || '-';
            const nik = this.dataset.nik || '';
            const rfid = this.dataset.rfid || '';
            const rt = this.dataset.rt || '-';

            formRekam.action = `{{ url('/warga') }}/${currentWargaId}/rfid`;
            namaEl.textContent = nama;
            rtEl.textContent = 'RT ' + rt;
            inputNik.value = nik;
            inputUid.value = rfid;
            modalOpenedAt = Date.now() - 3000;

            setScannerWaiting();
            if (rfid) {
                statusTitle.textContent = 'UID Saat Ini: ' + rfid + ' (Siap Tap Baru)';
            }

            bsModal.show();
            startModeKeepAlive();
            startPolling();
        });
    });

    modalEl.addEventListener('hidden.bs.modal', function () {
        stopPolling();
        stopModeKeepAlive();
    });

    formRekam.addEventListener('submit', function () {
        stopPolling();
        stopModeKeepAlive();
    });

    btnResetUid.addEventListener('click', function () {
        inputUid.value = '';
        modalOpenedAt = Date.now();
        setScannerWaiting();
        inputUid.focus();
    });

    btnSimulate.addEventListener('click', function () {
        const randomHex = Array.from({ length: 7 }, () =>
            Math.floor(Math.random() * 256).toString(16).padStart(2, '0').toUpperCase()
        ).join('');

        fetch(`{{ route('rfid.receive') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                uid: randomHex,
                device_id: 'NODEMCU-ESP32-RC522',
                context: 'rekam_ktp',
                method: 'simulation'
            })
        })
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success' && res.data) {
                modalOpenedAt = res.data.timestamp_ms;
                setScannerDetected(res.data);
            }
        });
    });
});
</script>
@endpush
