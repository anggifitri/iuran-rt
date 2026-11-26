@extends('layouts.app')

@section('title', 'Detail Agenda: ' . $agenda->judul . ' — NexaNest')

@section('content')
<!-- BREADCRUMB & HEADER -->
<div class="nx-page-header">
    <div>
        <a href="{{ route('agenda.index') }}" class="btn btn-sm btn-outline-secondary mb-2.5 px-3 py-1.5" style="border-radius: 8px; font-size: 0.82rem; font-weight: 600;">
            <i class="fas fa-arrow-left me-1.5"></i>Kembali ke Daftar Agenda
        </a>
        <div class="d-flex align-items-center gap-2 mb-1">
            {!! $agenda->jenis_badge !!}
            @if($agenda->rt_number)
                <span class="badge rounded-pill px-2.5 py-1" style="background: #EBF3EE; color: var(--nx-forest); font-size: 0.72rem; font-weight: 700;">
                    RT {{ $agenda->rt_number }}
                </span>
            @endif
            @if($agenda->is_published_notulen)
                <span class="badge rounded-pill px-2.5 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-size: 0.72rem; font-weight: 700;">
                    <i class="fas fa-file-circle-check me-1"></i>Notulensi Dipublikasikan
                </span>
            @endif
        </div>
        <h1 class="nx-page-title mb-1" style="font-size: 1.45rem;">{{ $agenda->judul }}</h1>
        <p class="nx-page-subtitle mb-0">
            Penyelenggara: <strong>{{ $agenda->penyelenggara }}</strong> · Peserta: {{ $agenda->target_peserta }}
        </p>
    </div>

    <!-- ACTION BUTTONS (FOR ADMIN) -->
    <div class="d-flex align-items-center gap-2">
        <div>
            {!! $agenda->status_badge !!}
        </div>

        @if(!Auth::user()->isWarga())
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle px-3 py-2" type="button" data-bs-toggle="dropdown" style="border-radius: 10px; font-weight: 600;">
                <i class="fas fa-ellipsis-vertical me-1"></i>Kelola Status
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius: 10px; border: 1px solid var(--nx-border); font-size: 0.85rem;">
                <li><h6 class="dropdown-header small fw-bold text-uppercase">Ubah Status Rapat</h6></li>
                <li>
                    <form action="{{ route('agenda.update_status', $agenda->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="akan_datang">
                        <button type="submit" class="dropdown-item py-2 {{ $agenda->status == 'akan_datang' ? 'active' : '' }}">
                            <i class="fas fa-clock text-warning me-2" style="width: 16px;"></i>Akan Datang
                        </button>
                    </form>
                </li>
                <li>
                    <form action="{{ route('agenda.update_status', $agenda->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="berlangsung">
                        <button type="submit" class="dropdown-item py-2 {{ $agenda->status == 'berlangsung' ? 'active' : '' }}">
                            <i class="fas fa-satellite-dish text-info me-2" style="width: 16px;"></i>Sedang Berlangsung
                        </button>
                    </form>
                </li>
                <li>
                    <form action="{{ route('agenda.update_status', $agenda->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="selesai">
                        <button type="submit" class="dropdown-item py-2 {{ $agenda->status == 'selesai' ? 'active' : '' }}">
                            <i class="fas fa-circle-check text-success me-2" style="width: 16px;"></i>Telah Selesai
                        </button>
                    </form>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <button type="button" class="dropdown-item py-2 text-danger" data-bs-toggle="modal" data-bs-target="#modalBatalkanAgenda">
                        <i class="fas fa-ban me-2" style="width: 16px;"></i>Batalkan Agenda
                    </button>
                </li>
            </ul>
        </div>

        <button type="button" class="btn btn-sm btn-outline-primary px-3 py-2" data-bs-toggle="modal" data-bs-target="#modalEditAgenda" style="border-radius: 10px; font-weight: 600;">
            <i class="fas fa-pen-to-square me-1"></i>Edit
        </button>

        @if(Auth::user()->isRw() || (Auth::user()->isAdminRT() && $agenda->created_by == Auth::id()))
        <button type="button" class="btn btn-sm btn-outline-danger px-2.5 py-2" data-bs-toggle="modal" data-bs-target="#modalHapusAgenda" style="border-radius: 10px;" title="Hapus Agenda">
            <i class="fas fa-trash"></i>
        </button>
        @endif
        @endif
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-xs" role="alert" style="border-radius: 12px; background: var(--nx-mint-soft); color: var(--nx-forest);">
        <i class="fas fa-circle-check me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show mb-4 border-0 shadow-xs" role="alert" style="border-radius: 12px; background: #FEF3C7; color: #92400E;">
        <i class="fas fa-triangle-exclamation me-2"></i>{{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-xs" role="alert" style="border-radius: 12px; background: var(--nx-danger-soft); color: var(--nx-danger);">
        <i class="fas fa-circle-exclamation me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- NAV TABS UTAMA: DETAIL, KEHADIRAN, NOTULENSI -->
<ul class="nav nav-pills mb-4 gap-2" id="agendaTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active px-3.5 py-2 fw-bold" id="detail-tab" data-bs-toggle="pill" data-bs-target="#tab-detail" type="button" role="tab" style="border-radius: 10px; font-size: 0.88rem;">
            <i class="fas fa-circle-info me-1.5"></i>1. Informasi &amp; Rangkaian Acara
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link px-3.5 py-2 fw-bold" id="kehadiran-tab" data-bs-toggle="pill" data-bs-target="#tab-kehadiran" type="button" role="tab" style="border-radius: 10px; font-size: 0.88rem;">
            <i class="fas fa-users-viewfinder me-1.5"></i>2. Daftar Hadir Peserta
            <span class="badge rounded-pill bg-white text-dark ms-1.5 px-2" style="font-size: 0.72rem;">{{ $agenda->kehadirans->count() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link px-3.5 py-2 fw-bold position-relative" id="notulensi-tab" data-bs-toggle="pill" data-bs-target="#tab-notulensi" type="button" role="tab" style="border-radius: 10px; font-size: 0.88rem;">
            <i class="fas fa-file-lines me-1.5"></i>3. Notulensi &amp; Hasil Rapat
            @if($agenda->is_published_notulen)
                <span class="badge rounded-pill bg-success ms-1.5 px-2" style="font-size: 0.72rem;">Terbit</span>
            @elseif($agenda->hasil_keputusan || $agenda->notulen)
                <span class="badge rounded-pill bg-warning text-dark ms-1.5 px-2" style="font-size: 0.72rem;">Draft</span>
            @endif
        </button>
    </li>
</ul>

<div class="tab-content" id="agendaTabContent">
    <!-- ==================== TAB 1: INFORMASI DETAIL ==================== -->
    <div class="tab-pane fade show active" id="tab-detail" role="tabpanel">
        <div class="row g-4">
            <!-- Left: Ringkasan & Pokok Bahasan -->
            <div class="col-lg-8">
                <!-- Card Informasi Utama -->
                <div class="card p-4 mb-4 shadow-xs" style="border-radius: 14px; border: 1px solid var(--nx-border);">
                    <h5 class="fw-bold mb-3 text-dark" style="font-size: 1.05rem;">
                        <i class="fas fa-calendar-check text-success me-2"></i>Jadwal &amp; Tempat Pertemuan
                    </h5>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 rounded-3" style="background: #F7F9F7; border: 1px solid var(--nx-border);">
                                <small class="text-muted d-block mb-1" style="font-size: 0.75rem; text-transform: uppercase; font-weight: 600;">Waktu Pelaksanaan</small>
                                <strong class="text-dark d-block" style="font-size: 0.95rem;">
                                    {{ \Carbon\Carbon::parse($agenda->tanggal_waktu)->translatedFormat('l, d F Y') }}
                                </strong>
                                <span class="badge rounded-pill px-2.5 py-1 mt-1" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-weight: 700; font-size: 0.75rem;">
                                    <i class="fas fa-clock me-1"></i>{{ \Carbon\Carbon::parse($agenda->tanggal_waktu)->format('H:i') }} - {{ $agenda->waktu_selesai ? \Carbon\Carbon::parse($agenda->waktu_selesai)->format('H:i') : 'Selesai' }} WIB
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3" style="background: #F7F9F7; border: 1px solid var(--nx-border);">
                                <small class="text-muted d-block mb-1" style="font-size: 0.75rem; text-transform: uppercase; font-weight: 600;">Lokasi / Media Pertemuan</small>
                                <strong class="text-dark d-block" style="font-size: 0.95rem;">{{ $agenda->lokasi }}</strong>
                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                                    <i class="fas fa-map-pin me-1 text-danger"></i>Kawasan Lingkungan RW 018
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Gambaran / Deskripsi -->
                    @if($agenda->deskripsi)
                    <div class="mb-4">
                        <h6 class="fw-bold text-dark small text-uppercase mb-2" style="font-size: 0.78rem; letter-spacing: 0.5px;">
                            <i class="fas fa-align-left text-primary me-1.5"></i>Gambaran Umum &amp; Latar Belakang
                        </h6>
                        <div class="p-3 rounded-3 bg-white border text-dark" style="font-size: 0.88rem; line-height: 1.6; border-color: var(--nx-border) !important;">
                            {{ $agenda->deskripsi }}
                        </div>
                    </div>
                    @endif

                    <!-- Pokok Bahasan / Agenda -->
                    <div>
                        <h6 class="fw-bold text-dark small text-uppercase mb-2" style="font-size: 0.78rem; letter-spacing: 0.5px;">
                            <i class="fas fa-list-check text-success me-1.5"></i>Poin Pembahasan / Rangkaian Acara
                        </h6>
                        <div class="p-3.5 rounded-3 text-dark" style="background: #F7F9F7; border: 1px solid var(--nx-border); line-height: 1.65; font-size: 0.88rem; white-space: pre-line;">{{ $agenda->agenda_pembahasan }}</div>
                    </div>
                </div>
            </div>

            <!-- Right: Meta Organisasi & Lampiran -->
            <div class="col-lg-4">
                <!-- Card Sasaran & Penyelenggara -->
                <div class="card p-3.5 mb-3.5 shadow-xs" style="border-radius: 14px; border: 1px solid var(--nx-border);">
                    <h6 class="fw-bold mb-3 text-dark" style="font-size: 0.95rem;">
                        <i class="fas fa-id-card-clip text-primary me-1.5"></i>Penyelenggara &amp; Undangan
                    </h6>

                    <ul class="list-unstyled mb-0" style="font-size: 0.82rem; line-height: 1.8;">
                        <li class="mb-2 pb-2 border-bottom">
                            <span class="text-muted d-block small">Penyelenggara Resmi:</span>
                            <strong class="text-dark">{{ $agenda->penyelenggara }}</strong>
                        </li>
                        <li class="mb-2 pb-2 border-bottom">
                            <span class="text-muted d-block small">Jenis Agenda:</span>
                            <span class="fw-semibold text-dark">{{ $agenda->jenis_label }}</span>
                        </li>
                        <li class="mb-2 pb-2 border-bottom">
                            <span class="text-muted d-block small">Sasaran Peserta:</span>
                            <span class="text-dark">{{ $agenda->target_peserta }}</span>
                        </li>
                        <li class="mb-2 pb-2 border-bottom">
                            <span class="text-muted d-block small">Cakupan Wilayah:</span>
                            <span class="text-dark">{{ $agenda->rt_number ? 'RT ' . $agenda->rt_number : 'Seluruh Wilayah RW 018' }}</span>
                        </li>
                        <li>
                            <span class="text-muted d-block small">Didaftarkan Oleh:</span>
                            <span class="text-dark">{{ $agenda->creator ? $agenda->creator->name : 'Sistem' }} ({{ $agenda->created_at->translatedFormat('d M Y, H:i') }} WIB)</span>
                        </li>
                    </ul>
                </div>

                <!-- Card Lampiran TOR / Berkas Undangan -->
                <div class="card p-3.5 shadow-xs" style="border-radius: 14px; border: 1px solid var(--nx-border);">
                    <h6 class="fw-bold mb-3 text-dark" style="font-size: 0.95rem;">
                        <i class="fas fa-paperclip text-danger me-1.5"></i>Berkas Undangan / TOR
                    </h6>

                    @if($agenda->lampiran_dokumen)
                        <div class="text-center p-3 rounded-3 mb-2" style="background: #F7F9F7; border: 1px dashed var(--nx-border);">
                            <i class="fas fa-file-pdf text-danger fa-2x mb-2"></i>
                            <div class="small fw-bold text-dark text-truncate mb-2">{{ basename($agenda->lampiran_dokumen) }}</div>
                            <a href="{{ asset('storage/' . $agenda->lampiran_dokumen) }}" target="_blank" class="btn btn-sm btn-primary w-100" style="border-radius: 8px; font-weight: 600; font-size: 0.8rem;">
                                <i class="fas fa-download me-1.5"></i>Unduh / Lihat Berkas
                            </a>
                        </div>
                    @else
                        <div class="text-center p-3 text-muted" style="font-size: 0.8rem;">
                            <i class="fas fa-file-lines fa-2x mb-2 opacity-50"></i>
                            <p class="mb-0">Tidak ada lampiran dokumen digital untuk agenda ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== TAB 2: DAFTAR HADIR PESERTA ==================== -->
    <div class="tab-pane fade" id="tab-kehadiran" role="tabpanel">
        <!-- 3 Stat Counters Kehadiran -->
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-6">
                <div class="card p-3 shadow-xs h-100" style="border-radius: 12px; border-left: 4px solid var(--nx-forest); background: #ffffff;">
                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Peserta Hadir</small>
                    <div class="d-flex align-items-baseline gap-2 mt-1">
                        <h3 class="fw-bold text-dark mb-0">{{ $kehadiranSummary->hadir }}</h3>
                        <span class="small text-success fw-bold">({{ $kehadiranSummary->persentase }}%)</span>
                    </div>
                    <small class="text-muted" style="font-size: 0.72rem;">Tercatat hadir musyawarah</small>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card p-3 shadow-xs h-100" style="border-radius: 12px; border-left: 4px solid #1F567A; background: #ffffff;">
                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Peserta Izin</small>
                    <div class="d-flex align-items-baseline gap-2 mt-1">
                        <h3 class="fw-bold text-dark mb-0" style="color: #1F567A;">{{ $kehadiranSummary->izin }}</h3>
                    </div>
                    <small class="text-muted" style="font-size: 0.72rem;">Konfirmasi izin berhalangan</small>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card p-3 shadow-xs h-100" style="border-radius: 12px; border-left: 4px solid var(--nx-danger); background: #ffffff;">
                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Tidak Hadir</small>
                    <div class="d-flex align-items-baseline gap-2 mt-1">
                        <h3 class="fw-bold text-dark mb-0" style="color: var(--nx-danger);">{{ $kehadiranSummary->tidak_hadir }}</h3>
                    </div>
                    <small class="text-muted" style="font-size: 0.72rem;">Belum/tanpa keterangan</small>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card p-3 shadow-xs h-100" style="border-radius: 12px; border-left: 4px solid #D97706; background: #ffffff;">
                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Total Undangan</small>
                    <div class="d-flex align-items-baseline gap-2 mt-1">
                        <h3 class="fw-bold text-dark mb-0" style="color: #D97706;">{{ $kehadiranSummary->total }}</h3>
                    </div>
                    <small class="text-muted" style="font-size: 0.72rem;">Daftar peserta terdaftar</small>
                </div>
            </div>
        </div>

        <div class="card shadow-xs" style="border-radius: 14px; border: 1px solid var(--nx-border);">
            <div class="p-3.5 d-flex justify-content-between align-items-center border-bottom" style="border-color: var(--nx-border) !important;">
                <div>
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 1rem;">
                        <i class="fas fa-clipboard-user text-primary me-2"></i>Daftar Presensi Kehadiran
                    </h5>
                    <small class="text-muted">Kelola status kehadiran peserta rapat secara cepat dan praktis.</small>
                </div>
                @if(!Auth::user()->isWarga())
                <div>
                    <button type="button" class="btn btn-sm btn-primary px-3 py-1.5" data-bs-toggle="modal" data-bs-target="#modalTambahPeserta" style="border-radius: 8px; font-weight: 600;">
                        <i class="fas fa-user-plus me-1.5"></i>Tambah Peserta
                    </button>
                </div>
                @endif
            </div>

            <!-- TABEL DAFTAR HADIR (STANDAR NEXANEST .nx-table) -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                    <thead class="bg-light text-muted" style="font-size: 0.75rem; text-transform: uppercase;">
                        <tr>
                            <th class="ps-3.5 py-3" style="width: 50px;">#</th>
                            <th class="py-3">Nama Peserta</th>
                            <th class="py-3">Jabatan / Wilayah</th>
                            <th class="py-3">Status Kehadiran</th>
                            <th class="py-3">Waktu Presensi / Keterangan</th>
                            @if(!Auth::user()->isWarga())
                            <th class="text-end pe-3.5 py-3" style="width: 140px;">Aksi Cepat</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($agenda->kehadirans as $idx => $k)
                            <tr>
                                <td class="ps-3.5 text-muted">{{ $idx + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $k->nama_peserta }}</div>
                                    @if($k->user)
                                        <small class="text-muted" style="font-size: 0.72rem;"><i class="fas fa-circle-check text-success me-1"></i>Akun Terverifikasi</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-dark">{{ $k->jabatan ?: '-' }}</span>
                                    @if($k->rt_number)
                                        <span class="badge rounded-pill bg-light text-dark ms-1" style="font-size: 0.7rem;">RT {{ $k->rt_number }}</span>
                                    @endif
                                </td>
                                <td>
                                    {!! $k->status_badge !!}
                                </td>
                                <td>
                                    @if($k->status == 'hadir')
                                        <span class="text-dark small"><i class="fas fa-clock text-success me-1"></i>{{ $k->waktu_kehadiran ? $k->waktu_kehadiran->translatedFormat('H:i') . ' WIB' : 'Tercatat Hadir' }}</span>
                                    @else
                                        <span class="text-muted small">{{ $k->keterangan ?: '-' }}</span>
                                    @endif
                                </td>
                                @if(!Auth::user()->isWarga())
                                <td class="text-end pe-3.5">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <!-- Quick toggle to Hadir -->
                                        <form action="{{ route('agenda.toggle_kehadiran', [$agenda->id, $k->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="status" value="hadir">
                                            <button type="submit" class="btn btn-sm {{ $k->status == 'hadir' ? 'btn-success' : 'btn-outline-success' }}" title="Tandai Hadir" style="border-radius: 6px 0 0 6px; padding: 2px 7px;">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <!-- Quick toggle to Izin -->
                                        <form action="{{ route('agenda.toggle_kehadiran', [$agenda->id, $k->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="status" value="izin">
                                            <button type="submit" class="btn btn-sm {{ $k->status == 'izin' ? 'btn-primary' : 'btn-outline-primary' }}" title="Tandai Izin" style="border-radius: 0; padding: 2px 7px;">
                                                <i class="fas fa-info"></i>
                                            </button>
                                        </form>
                                        <!-- Quick toggle to Tidak Hadir -->
                                        <form action="{{ route('agenda.toggle_kehadiran', [$agenda->id, $k->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="status" value="tidak_hadir">
                                            <button type="submit" class="btn btn-sm {{ $k->status == 'tidak_hadir' ? 'btn-danger' : 'btn-outline-danger' }}" title="Tandai Tidak Hadir" style="border-radius: 0; padding: 2px 7px;">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                        <!-- Delete attendee -->
                                        <form action="{{ route('agenda.destroy_kehadiran', [$agenda->id, $k->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus peserta ini dari daftar hadir?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-secondary" title="Hapus dari Daftar" style="border-radius: 0 6px 6px 0; padding: 2px 7px;">
                                                <i class="fas fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ !Auth::user()->isWarga() ? '6' : '5' }}" class="text-center py-4 text-muted">
                                    <i class="fas fa-users-slash fa-2x mb-2 text-muted" style="opacity: 0.4;"></i>
                                    <p class="mb-0 small">Belum ada data kehadiran peserta yang tercatat.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ==================== TAB 3: NOTULENSI & HASIL RAPAT ==================== -->
    <div class="tab-pane fade" id="tab-notulensi" role="tabpanel">
        <div class="row g-4">
            <!-- Left: Isi Notulensi & Keputusan -->
            <div class="col-lg-8">
                <!-- Status Banner Notulensi -->
                <div class="card p-3 mb-4 shadow-xs" style="border-radius: 12px; border: 1px solid var(--nx-border); background: {{ $agenda->is_published_notulen ? 'var(--nx-mint-soft)' : '#FEF3C7' }};">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            @if($agenda->is_published_notulen)
                                <i class="fas fa-circle-check text-success fa-lg"></i>
                                <div>
                                    <strong class="text-dark d-block" style="font-size: 0.9rem;">Notulensi &amp; Hasil Rapat Telah Dipublikasikan Resmi</strong>
                                    <small class="text-muted">Dipublikasikan pada: {{ $agenda->published_at ? $agenda->published_at->translatedFormat('d F Y, H:i') . ' WIB' : '-' }}</small>
                                </div>
                            @else
                                <i class="fas fa-file-pen text-warning fa-lg"></i>
                                <div>
                                    <strong class="text-dark d-block" style="font-size: 0.9rem;">Draft Notulensi (Belum Dipublikasikan)</strong>
                                    <small class="text-muted">Hasil rapat saat ini hanya dapat ditinjau oleh Admin/Pengurus RW.</small>
                                </div>
                            @endif
                        </div>
                        @if(!Auth::user()->isWarga())
                        <div>
                            <button type="button" class="btn btn-sm btn-primary px-3 py-1.5" data-bs-toggle="modal" data-bs-target="#modalKelolaNotulen" style="border-radius: 8px; font-weight: 700;">
                                <i class="fas fa-pen-to-square me-1.5"></i>{{ $agenda->hasil_keputusan || $agenda->notulen ? 'Edit Notulensi' : 'Isi Notulensi Rapat' }}
                            </button>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- 1. Hasil Keputusan Musyawarah -->
                <div class="card p-4 mb-4 shadow-xs" style="border-radius: 14px; border: 1px solid var(--nx-mint); background: #ffffff;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 32px; height: 32px; background: var(--nx-mint-soft); color: var(--nx-forest);">
                            <i class="fas fa-gavel"></i>
                        </span>
                        <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.05rem;">Hasil Keputusan &amp; Kebijakan Musyawarah</h5>
                    </div>

                    @if($agenda->hasil_keputusan)
                        <div class="p-3.5 rounded-3 text-dark" style="background: var(--nx-mint-soft); border: 1px solid var(--nx-mint); font-size: 0.9rem; line-height: 1.65; white-space: pre-line;">{{ $agenda->hasil_keputusan }}</div>
                    @else
                        <div class="p-4 rounded-3 text-center text-muted" style="background: #F7F9F7; border: 1px dashed var(--nx-border);">
                            <i class="fas fa-gavel fa-2x mb-2 text-muted" style="opacity: 0.3;"></i>
                            <p class="mb-0 small">Belum ada poin keputusan atau kebijakan musyawarah yang dicatat.</p>
                        </div>
                    @endif
                </div>

                <!-- 2. Catatan Notulensi Lengkap -->
                <div class="card p-4 mb-4 shadow-xs" style="border-radius: 14px; border: 1px solid var(--nx-border); background: #ffffff;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 32px; height: 32px; background: var(--nx-powder); color: #1e4b6d;">
                            <i class="fas fa-book-open"></i>
                        </span>
                        <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.05rem;">Ringkasan Jalannya Diskusi &amp; Notulensi Lengkap</h5>
                    </div>

                    @if($agenda->notulen)
                        <div class="p-3.5 rounded-3 text-dark" style="background: #F7F9F7; border: 1px solid var(--nx-border); font-size: 0.88rem; line-height: 1.65; white-space: pre-line;">{{ $agenda->notulen }}</div>
                    @else
                        <div class="p-4 rounded-3 text-center text-muted" style="background: #F7F9F7; border: 1px dashed var(--nx-border);">
                            <i class="fas fa-book-open fa-2x mb-2 text-muted" style="opacity: 0.3;"></i>
                            <p class="mb-0 small">Belum ada ringkasan pembahasan jalannya rapat yang dicatat.</p>
                        </div>
                    @endif
                </div>

                <!-- 3. Rencana Tindak Lanjut & PIC -->
                <div class="card p-4 shadow-xs" style="border-radius: 14px; border: 1px solid var(--nx-border); background: #ffffff;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 32px; height: 32px; background: #FEF3C7; color: #92400E;">
                            <i class="fas fa-list-check"></i>
                        </span>
                        <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.05rem;">Rencana Tindak Lanjut (Action Items)</h5>
                    </div>

                    @if($agenda->tindak_lanjut)
                        <div class="p-3.5 rounded-3 mb-3 text-dark" style="background: #FFFBEB; border: 1px solid #FDE68A; font-size: 0.88rem; line-height: 1.65; white-space: pre-line;">{{ $agenda->tindak_lanjut }}</div>
                        
                        <div class="row g-2">
                            <div class="col-md-6">
                                <div class="p-2.5 rounded-3 bg-light border small text-dark">
                                    <span class="text-muted d-block" style="font-size: 0.72rem;">Penanggung Jawab (PIC):</span>
                                    <strong>{{ $agenda->pic_tindak_lanjut ?: '-' }}</strong>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-2.5 rounded-3 bg-light border small text-dark">
                                    <span class="text-muted d-block" style="font-size: 0.72rem;">Tenggat Waktu Realisasi:</span>
                                    <strong>{{ $agenda->tenggat_tindak_lanjut ? $agenda->tenggat_tindak_lanjut->translatedFormat('d F Y') : '-' }}</strong>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="p-3 text-center text-muted" style="background: #F7F9F7; border: 1px dashed var(--nx-border); border-radius: 10px;">
                            <p class="mb-0 small">Belum ada butir tindak lanjut yang ditentukan.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right: Lampiran Hasil & Riwayat Perubahan -->
            <div class="col-lg-4">
                <!-- Lampiran Foto / Berkas Notulensi -->
                <div class="card p-3.5 mb-3.5 shadow-xs" style="border-radius: 14px; border: 1px solid var(--nx-border);">
                    <h6 class="fw-bold mb-3 text-dark" style="font-size: 0.95rem;">
                        <i class="fas fa-images text-primary me-1.5"></i>Dokumentasi &amp; Berkas Hasil
                    </h6>

                    @if($agenda->lampiran_notulen)
                        <div class="text-center p-3 rounded-3" style="background: #F7F9F7; border: 1px dashed var(--nx-border);">
                            @php
                                $ext = pathinfo($agenda->lampiran_notulen, PATHINFO_EXTENSION);
                            @endphp
                            @if(in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'webp']))
                                <img src="{{ asset('storage/' . $agenda->lampiran_notulen) }}" alt="Dokumentasi Rapat" class="img-fluid rounded-3 mb-2 shadow-xs" style="max-height: 160px; object-fit: cover;">
                            @else
                                <i class="fas fa-file-pdf text-danger fa-2x mb-2"></i>
                            @endif
                            <div class="small fw-bold text-dark text-truncate mb-2">{{ basename($agenda->lampiran_notulen) }}</div>
                            <a href="{{ asset('storage/' . $agenda->lampiran_notulen) }}" target="_blank" class="btn btn-sm btn-outline-primary w-100" style="border-radius: 8px; font-size: 0.8rem; font-weight: 600;">
                                <i class="fas fa-download me-1.5"></i>Buka Berkas Hasil
                            </a>
                        </div>
                    @else
                        <div class="text-center p-3 text-muted" style="font-size: 0.8rem;">
                            <i class="fas fa-image fa-2x mb-2 opacity-50"></i>
                            <p class="mb-0">Belum ada foto dokumentasi atau berkas notulensi yang diunggah.</p>
                        </div>
                    @endif
                </div>

                <!-- Riwayat Perubahan Notulensi (Change Tracking) -->
                <div class="card p-3.5 shadow-xs" style="border-radius: 14px; border: 1px solid var(--nx-border);">
                    <h6 class="fw-bold mb-3 text-dark" style="font-size: 0.95rem;">
                        <i class="fas fa-clock-rotate-left text-secondary me-1.5"></i>Riwayat Perubahan Notulensi
                    </h6>

                    @if($agenda->notulenHistories->count() > 0)
                        <div class="timeline-simple" style="font-size: 0.8rem;">
                            @foreach($agenda->notulenHistories as $h)
                                <div class="mb-3 pb-2 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="badge rounded-pill px-2 py-0.5" style="background: {{ $h->action_type == 'publish' ? 'var(--nx-mint)' : '#FEF3C7' }}; color: {{ $h->action_type == 'publish' ? 'var(--nx-forest)' : '#92400E' }}; font-size: 0.68rem; font-weight: 700;">
                                            {{ strtoupper($h->action_type) }}
                                        </span>
                                        <span class="text-muted" style="font-size: 0.72rem;">{{ $h->created_at->translatedFormat('d M, H:i') }}</span>
                                    </div>
                                    <div class="text-dark small">{{ $h->catatan_perubahan }}</div>
                                    <small class="text-muted" style="font-size: 0.7rem;">Oleh: {{ $h->user ? $h->user->name : 'Sistem' }}</small>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted small mb-0">Belum ada catatan riwayat perubahan notulensi.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==================== MODALS PENGELOLAAN ==================== -->
@if(!Auth::user()->isWarga())
<!-- 1. Modal Tambah Peserta -->
<div class="modal fade" id="modalTambahPeserta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.12);">
            <form action="{{ route('agenda.store_kehadiran', $agenda->id) }}" method="POST">
                @csrf
                <div class="modal-header px-4 pt-4 pb-2 border-0">
                    <h5 class="modal-title fw-bold text-dark" style="font-size: 1.1rem;">
                        <i class="fas fa-user-plus text-primary me-2"></i>Tambah Peserta ke Daftar Hadir
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Pilih Dari Pengguna Terdaftar (Opsional)</label>
                        <select id="selectUserPeserta" name="user_id" class="form-select" style="border-radius: 10px; height: 40px;">
                            <option value="">-- Pilih atau Masukkan Manual di Bawah --</option>
                            @foreach($allUsers as $u)
                                <option value="{{ $u->id }}" data-name="{{ $u->name }}" data-rt="{{ $u->rt_number }}" data-jabatan="{{ $u->isRw() ? 'Ketua RW' : ($u->isAdminRT() ? 'Ketua RT ' . $u->rt_number : ($u->isBendahara() ? 'Bendahara RW' : 'Warga RT ' . $u->rt_number)) }}">
                                    {{ $u->name }} ({{ strtoupper($u->role) }} - RT {{ $u->rt_number }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Lengkap Peserta <span class="text-danger">*</span></label>
                        <input type="text" name="nama_peserta" id="inputNamaPeserta" class="form-control" required placeholder="Contoh: Bpk. Bambang Sudarsono" style="border-radius: 10px; height: 40px;">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-8">
                            <label class="form-label small fw-bold">Jabatan / Keterangan</label>
                            <input type="text" name="jabatan" id="inputJabatanPeserta" class="form-control" placeholder="Contoh: Tokoh Masyarakat / Warga" style="border-radius: 10px; height: 40px;">
                        </div>
                        <div class="col-4">
                            <label class="form-label small fw-bold">Wilayah RT</label>
                            <input type="text" name="rt_number" id="inputRtPeserta" class="form-control" placeholder="007" style="border-radius: 10px; height: 40px;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Status Kehadiran Awal <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required style="border-radius: 10px; height: 40px;">
                            <option value="hadir">✅ Hadir</option>
                            <option value="izin">ℹ️ Izin</option>
                            <option value="tidak_hadir">❌ Tidak Hadir</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Catatan / Keterangan Tambahan (Opsional)</label>
                        <input type="text" name="keterangan" class="form-control" placeholder="Contoh: Hadir mewakili Ketua RT" style="border-radius: 10px; height: 40px;">
                    </div>
                </div>
                <div class="modal-footer px-4 pb-4 pt-2 border-0">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
                    <button type="submit" class="btn btn-primary px-4" style="border-radius: 10px; font-weight: 700;">Simpan Peserta</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 2. Modal Kelola Notulensi (Draft & Publikasi) -->
<div class="modal fade" id="modalKelolaNotulen" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.12);">
            <form action="{{ route('agenda.update_notulen', $agenda->id) }}" method="POST" enctype="multipart/form-data" id="formKelolaNotulen">
                @csrf
                <input type="hidden" name="action_type" id="inputActionType" value="publish">

                <div class="modal-header px-4 pt-4 pb-2 border-0">
                    <div>
                        <h5 class="modal-title fw-bold text-dark" style="font-size: 1.15rem;">
                            <i class="fas fa-file-pen text-success me-2"></i>Pengisian Notulensi &amp; Hasil Musyawarah
                        </h5>
                        <p class="small text-muted mb-0">Catat hasil musyawarah, tetapkan tindak lanjut, dan pilih apakah disimpan sebagai draft atau dipublikasikan resmi.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body px-4 py-3">
                    <!-- Status Pertemuan Saat Ini -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Status Rapat</label>
                        <select name="status_rapat" class="form-select" style="border-radius: 10px; height: 40px;">
                            <option value="selesai" {{ $agenda->status == 'selesai' ? 'selected' : '' }}>✅ Rapat Telah Selesai</option>
                            <option value="berlangsung" {{ $agenda->status == 'berlangsung' ? 'selected' : '' }}>📡 Sedang Berlangsung</option>
                            <option value="akan_datang" {{ $agenda->status == 'akan_datang' ? 'selected' : '' }}>⏳ Masih Akan Datang</option>
                        </select>
                    </div>

                    <!-- Poin Keputusan -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Poin Keputusan Musyawarah Yang Disepakati</label>
                        <textarea name="hasil_keputusan" rows="4" class="form-control" placeholder="Tuliskan keputusan musyawarah yang bersifat mengikat...&#10;1. Disepakati iuran kebersihan RT diseragamkan Rp 25.000&#10;2. Kerja bakti serentak tanggal 25 November 2026" style="border-radius: 10px; line-height: 1.5; font-size: 0.85rem;">{{ $agenda->hasil_keputusan }}</textarea>
                    </div>

                    <!-- Notulensi Lengkap -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Notulensi Lengkap Jalannya Diskusi</label>
                        <textarea name="notulen" rows="5" class="form-control" placeholder="Rangkuman dinamika diskusi, tanya jawab, dan masukan peserta selama rapat berlangsung..." style="border-radius: 10px; line-height: 1.5; font-size: 0.85rem;">{{ $agenda->notulen }}</textarea>
                    </div>

                    <!-- Tindak Lanjut & PIC -->
                    <div class="p-3 rounded-3 mb-3" style="background: #FFFBEB; border: 1px solid #FDE68A;">
                        <h6 class="fw-bold text-dark small mb-2"><i class="fas fa-list-check text-warning me-1.5"></i>Rencana Tindak Lanjut (Action Items)</h6>
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-muted">Deskripsi Tindak Lanjut</label>
                            <textarea name="tindak_lanjut" rows="2" class="form-control" placeholder="Contoh: Pengadaan 5 unit tong sampah pilah dan pembentukan satgas lingkungan..." style="border-radius: 8px; font-size: 0.82rem;">{{ $agenda->tindak_lanjut }}</textarea>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Penanggung Jawab (PIC)</label>
                                <input type="text" name="pic_tindak_lanjut" class="form-control" value="{{ $agenda->pic_tindak_lanjut }}" placeholder="Contoh: Gavin Gunawan (Ketua RT 007)" style="border-radius: 8px; height: 38px; font-size: 0.82rem;">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Tenggat Waktu Realisasi</label>
                                <input type="date" name="tenggat_tindak_lanjut" class="form-control" value="{{ $agenda->tenggat_tindak_lanjut ? $agenda->tenggat_tindak_lanjut->format('Y-m-d') : '' }}" style="border-radius: 8px; height: 38px; font-size: 0.82rem;">
                            </div>
                        </div>
                    </div>

                    <!-- Lampiran Notulensi / Foto -->
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-dark">Lampiran Foto Dokumentasi / Dokumen Hasil (Opsional)</label>
                        <input type="file" name="lampiran_notulen" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.docx" style="border-radius: 10px;">
                        <small class="text-muted" style="font-size: 0.72rem;">Unggah foto pelaksanaan musyawarah atau file PDF berita acara. Maksimal 5MB.</small>
                    </div>
                </div>

                <div class="modal-footer px-4 pb-4 pt-2 border-0 d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
                    <div class="d-flex gap-2">
                        <button type="submit" onclick="document.getElementById('inputActionType').value='draft';" class="btn btn-outline-warning px-3" style="border-radius: 10px; font-weight: 700;">
                            <i class="fas fa-floppy-disk me-1.5"></i>Simpan Draft
                        </button>
                        <button type="submit" onclick="document.getElementById('inputActionType').value='publish';" class="btn btn-primary px-3.5" style="border-radius: 10px; font-weight: 700;">
                            <i class="fas fa-bullhorn me-1.5"></i>Publikasikan Hasil Rapat
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 3. Modal Edit Agenda -->
<div class="modal fade" id="modalEditAgenda" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.12);">
            <form action="{{ route('agenda.update', $agenda->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header px-4 pt-4 pb-2 border-0">
                    <h5 class="modal-title fw-bold text-dark" style="font-size: 1.15rem;">
                        <i class="fas fa-pen-to-square text-primary me-2"></i>Edit Informasi Agenda Rapat
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Judul Agenda <span class="text-danger">*</span></label>
                        <input type="text" name="judul" class="form-control" required value="{{ $agenda->judul }}" style="border-radius: 10px; height: 40px;">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Jenis Rapat</label>
                            <select name="jenis_rapat" class="form-select" required style="border-radius: 10px; height: 40px;">
                                @if(!Auth::user()->isAdminRT())
                                <option value="rw" {{ $agenda->jenis_rapat == 'rw' ? 'selected' : '' }}>Rapat Internal RW</option>
                                @endif
                                <option value="koordinasi_rt" {{ $agenda->jenis_rapat == 'koordinasi_rt' ? 'selected' : '' }}>Koordinasi RT</option>
                                <option value="musyawarah_warga" {{ $agenda->jenis_rapat == 'musyawarah_warga' ? 'selected' : '' }}>Musyawarah Warga</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Wilayah RT</label>
                            <input type="text" name="rt_number" class="form-control" value="{{ $agenda->rt_number }}" placeholder="Kosongkan jika tingkat RW" style="border-radius: 10px; height: 40px;">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Tanggal Pertemuan</label>
                            <input type="date" name="tanggal" class="form-control" required value="{{ $agenda->tanggal_waktu ? $agenda->tanggal_waktu->format('Y-m-d') : '' }}" style="border-radius: 10px; height: 40px;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Waktu Mulai</label>
                            <input type="time" name="waktu_mulai" class="form-control" required value="{{ $agenda->tanggal_waktu ? $agenda->tanggal_waktu->format('H:i') : '19:30' }}" style="border-radius: 10px; height: 40px;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Waktu Selesai</label>
                            <input type="time" name="waktu_selesai" class="form-control" required value="{{ $agenda->waktu_selesai ? \Carbon\Carbon::parse($agenda->waktu_selesai)->format('H:i') : '21:30' }}" style="border-radius: 10px; height: 40px;">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Lokasi Pertemuan</label>
                            <input type="text" name="lokasi" class="form-control" required value="{{ $agenda->lokasi }}" style="border-radius: 10px; height: 40px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Penyelenggara</label>
                            <input type="text" name="penyelenggara" class="form-control" required value="{{ $agenda->penyelenggara }}" style="border-radius: 10px; height: 40px;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Sasaran Undangan Peserta</label>
                        <input type="text" name="target_peserta" class="form-control" required value="{{ $agenda->target_peserta }}" style="border-radius: 10px; height: 40px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Poin Pembahasan / Agenda</label>
                        <textarea name="agenda_pembahasan" rows="3" class="form-control" required style="border-radius: 10px; font-size: 0.85rem;">{{ $agenda->agenda_pembahasan }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Deskripsi Ringkas</label>
                        <textarea name="deskripsi" rows="2" class="form-control" style="border-radius: 10px; font-size: 0.85rem;">{{ $agenda->deskripsi }}</textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Ganti Berkas Undangan / TOR (Opsional)</label>
                        <input type="file" name="lampiran_dokumen" class="form-control" accept=".pdf,.doc,.docx,.jpg,.png" style="border-radius: 10px;">
                    </div>
                </div>
                <div class="modal-footer px-4 pb-4 pt-2 border-0">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
                    <button type="submit" class="btn btn-primary px-4" style="border-radius: 10px; font-weight: 700;">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 4. Modal Batalkan Agenda -->
<div class="modal fade" id="modalBatalkanAgenda" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.12);">
            <form action="{{ route('agenda.update_status', $agenda->id) }}" method="POST">
                @csrf
                <input type="hidden" name="status" value="dibatalkan">
                <div class="modal-header px-4 pt-4 pb-2 border-0">
                    <h5 class="modal-title fw-bold text-danger" style="font-size: 1.1rem;">
                        <i class="fas fa-ban me-2"></i>Batalkan Agenda Pertemuan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <p class="small text-muted mb-3">Agenda akan ditandai sebagai <strong>DIBATALKAN</strong> pada Kalender Kegiatan dan notifikasi pembatalan akan dikirimkan kepada peserta.</p>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Alasan Pembatalan (Opsional)</label>
                        <textarea name="keterangan_batal" rows="3" class="form-control" placeholder="Contoh: Ditunda dikarenakan ada agenda darurat perbaikan saluran air RW..." style="border-radius: 10px; font-size: 0.85rem;"></textarea>
                    </div>
                </div>
                <div class="modal-footer px-4 pb-4 pt-2 border-0">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
                    <button type="submit" class="btn btn-danger px-4" style="border-radius: 10px; font-weight: 700;">Konfirmasi Pembatalan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 5. Modal Hapus Agenda -->
@if(Auth::user()->isRw() || (Auth::user()->isAdminRT() && $agenda->created_by == Auth::id()))
<div class="modal fade" id="modalHapusAgenda" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.12);">
            <form action="{{ route('agenda.destroy', $agenda->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header px-4 pt-4 pb-2 border-0">
                    <h5 class="modal-title fw-bold text-danger" style="font-size: 1.1rem;">
                        <i class="fas fa-trash-can me-2"></i>Hapus Agenda Rapat
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <p class="small text-dark mb-0">Apakah Anda yakin ingin menghapus agenda <strong>"{{ $agenda->judul }}"</strong> secara permanen beserta data absensi dan notulensinya? Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer px-4 pb-4 pt-2 border-0">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
                    <button type="submit" class="btn btn-danger px-4" style="border-radius: 10px; font-weight: 700;">Hapus Permanen</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fill participant details when user is selected from dropdown
    const selectUser = document.getElementById('selectUserPeserta');
    const inputNama = document.getElementById('inputNamaPeserta');
    const inputJabatan = document.getElementById('inputJabatanPeserta');
    const inputRt = document.getElementById('inputRtPeserta');

    if (selectUser) {
        selectUser.addEventListener('change', function() {
            const opt = selectUser.selectedOptions[0];
            if (opt && opt.value) {
                inputNama.value = opt.getAttribute('data-name') || '';
                inputJabatan.value = opt.getAttribute('data-jabatan') || '';
                inputRt.value = opt.getAttribute('data-rt') || '';
            }
        });
    }

    // Auto open tab based on URL hash (e.g. #tab-kehadiran or #tab-notulensi)
    if (window.location.hash) {
        const hash = window.location.hash;
        if (hash === '#tab-kehadiran') {
            const tabBtn = document.getElementById('kehadiran-tab');
            if (tabBtn) {
                bootstrap.Tab.getOrCreateInstance(tabBtn).show();
            }
        } else if (hash === '#tab-notulensi') {
            const tabBtn = document.getElementById('notulensi-tab');
            if (tabBtn) {
                bootstrap.Tab.getOrCreateInstance(tabBtn).show();
            }
        }
    }
});
</script>
@endif
@endsection
