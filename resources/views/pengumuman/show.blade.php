@extends('layouts.app')

@section('title', $pengumuman->title . ' — NexaNest')

@section('content')
@php
    $pb = $pengumuman->prioritas_badge;
    $kb = $pengumuman->kategori_badge;
    $sb = $pengumuman->status_badge;
@endphp

<!-- Header Card -->
<div class="nx-page-header mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
            <a href="{{ route('pengumuman.index') }}" class="badge rounded-pill text-decoration-none px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
                <i class="fas fa-bullhorn me-1"></i> Pusat Pengumuman
            </a>
            <span class="text-muted small">/</span>
            <span class="badge bg-light text-secondary rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                {{ $pengumuman->kategori_label }}
            </span>
        </div>
        <h1 class="nx-page-title mb-1">{{ $pengumuman->title }}</h1>
        <div class="nx-page-subtitle d-flex align-items-center flex-wrap gap-2" style="font-size: 0.8rem;">
            <span><i class="far fa-clock me-1 text-muted"></i> Terbit: {{ $pengumuman->published_at ? $pengumuman->published_at->translatedFormat('l, d F Y - H:i') : $pengumuman->created_at->translatedFormat('l, d F Y - H:i') }} WIB</span>
            <span>•</span>
            <span><i class="fas fa-users me-1 text-muted"></i> Target: <strong>{{ $pengumuman->target_teks }}</strong></span>
            <span>•</span>
            <span><i class="fas fa-user-pen me-1 text-muted"></i> Oleh: <strong>{{ $pengumuman->creator ? $pengumuman->creator->name : 'Pengurus RW 018' }}</strong></span>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('pengumuman.index') }}" class="btn btn-outline-secondary fw-bold" style="border-radius: 12px;">
            <i class="fas fa-arrow-left me-1.5"></i> Kembali
        </a>

        @if(!$isWarga && ($isAdminRw || Auth::id() === $pengumuman->created_by))
        <a href="{{ route('pengumuman.edit', $pengumuman->id) }}" class="btn btn-warning text-dark fw-bold" style="border-radius: 12px;">
            <i class="fas fa-pen-to-square me-1.5"></i> Edit Warta
        </a>

        <form action="{{ route('pengumuman.archive', $pengumuman->id) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-light fw-bold text-secondary" style="border-radius: 12px; border: 1px solid var(--nx-border);">
                <i class="fas {{ $pengumuman->is_archived ? 'fa-box-open' : 'fa-box-archive' }} me-1.5"></i>
                {{ $pengumuman->is_archived ? 'Pulihkan' : 'Arsipkan' }}
            </button>
        </form>

        <form action="{{ route('pengumuman.destroy', $pengumuman->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus warta pengumuman ini secara permanen?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger fw-bold" style="border-radius: 12px;">
                <i class="far fa-trash-can me-1"></i> Hapus
            </button>
        </form>
        @endif
    </div>
</div>

<!-- Feedback Alerts -->
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

<!-- Emergency / High Priority Banner -->
@if($pengumuman->prioritas === 'darurat')
<div class="alert alert-danger d-flex align-items-start gap-3 p-3 mb-4 shadow-sm" style="border-radius: 16px; border: 2px solid #f87171; background: #fef2f2;">
    <div class="rounded-circle d-flex align-items-center justify-content-center shrink-0" style="width: 40px; height: 40px; background: #fee2e2; color: #dc2626; font-size: 1.25rem;">
        <i class="fas fa-triangle-exclamation"></i>
    </div>
    <div>
        <h6 class="fw-bold text-danger text-uppercase mb-1" style="letter-spacing: 0.05em; font-size: 0.85rem;">
            Peringatan Darurat / Maklumat Mendesak RW 018
        </h6>
        <p class="mb-0 small text-danger" style="font-size: 0.8rem; line-height: 1.4;">
            Pengumuman ini memiliki tingkat urgensi tertinggi yang membutuhkan perhatian dan tindak lanjut segera dari seluruh warga target.
        </p>
    </div>
</div>
@elseif($pengumuman->prioritas === 'penting')
<div class="alert alert-warning d-flex align-items-start gap-3 p-3 mb-4 shadow-sm" style="border-radius: 16px; border: 2px solid #fde047; background: #fefce8;">
    <div class="rounded-circle d-flex align-items-center justify-content-center shrink-0" style="width: 40px; height: 40px; background: #fef9c3; color: #ca8a04; font-size: 1.25rem;">
        <i class="fas fa-bell"></i>
    </div>
    <div>
        <h6 class="fw-bold text-warning-emphasis text-uppercase mb-1" style="letter-spacing: 0.05em; font-size: 0.85rem;">
            Pengumuman Penting — Wajib Diperhatikan
        </h6>
        <p class="mb-0 small text-warning-emphasis" style="font-size: 0.8rem; line-height: 1.4;">
            Warta ini memuat instruksi, batas waktu administrasi, atau maklumat penting lingkungan yang memerlukan kerja sama warga.
        </p>
    </div>
</div>
@endif

<!-- Main Announcement Card -->
<div class="card p-4 p-lg-5 border-0 shadow-sm mb-4" style="border-radius: 18px; border: 1px solid var(--nx-border) !important;">
    <!-- Top Badges Bar -->
    <div class="d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom flex-wrap gap-2" style="border-color: var(--nx-border) !important;">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <!-- Kategori Badge -->
            <span class="badge rounded-pill px-3 py-1.5"
                  style="background: {{ $kb['bg'] }}; color: {{ $kb['color'] }}; font-size: 0.75rem; font-weight: 700;">
                <i class="fas {{ $kb['icon'] }} me-1"></i>
                <span>{{ $pengumuman->kategori_label }}</span>
            </span>

            <!-- Prioritas Badge -->
            <span class="badge rounded-pill px-3 py-1.5"
                  style="background: {{ $pb['bg'] }}; color: {{ $pb['color'] }}; border: 1px solid {{ $pb['border'] }}; font-size: 0.75rem; font-weight: 700;">
                <i class="fas {{ $pb['icon'] }} me-1"></i>
                <span>{{ $pb['tag'] }}</span>
            </span>

            @if($pengumuman->is_pinned)
            <span class="badge rounded-pill px-3 py-1.5" style="background: var(--nx-mint); color: var(--nx-forest); font-size: 0.75rem; font-weight: 700;">
                <i class="fas fa-thumbtack me-1"></i> Disematkan
            </span>
            @endif

            <span class="badge rounded-pill px-3 py-1.5"
                  style="background: {{ $sb['bg'] }}; color: {{ $sb['color'] }}; font-size: 0.75rem; font-weight: 700;">
                <i class="fas {{ $sb['icon'] }} me-1"></i> {{ $sb['label'] }}
            </span>
        </div>

        <!-- Target RT Badge -->
        <div>
            <span class="badge bg-light text-secondary rounded-pill px-3 py-1.5 border" style="font-size: 0.75rem;">
                <i class="fas fa-location-dot me-1" style="color: var(--nx-forest);"></i> {{ $pengumuman->target_teks }}
            </span>
        </div>
    </div>

    <!-- Body Content -->
    <div class="text-dark mb-4" style="font-size: 0.95rem; line-height: 1.8; white-space: pre-line;">
{{ $pengumuman->content }}
    </div>

    <!-- Integrated Calendar Kegiatan Box -->
    @if($pengumuman->is_kegiatan)
    <div class="card p-3.5 mb-4 border-0 shadow-sm" style="border-radius: 16px; background: #f0fdfa; border: 1px solid #99f6e4 !important;">
        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
            <div class="d-flex align-items-start gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center shrink-0 mt-1" style="width: 44px; height: 44px; background: #ccfbf1; color: #0f766e; font-size: 1.25rem;">
                    <i class="far fa-calendar-check"></i>
                </div>
                <div>
                    <span class="badge rounded-pill px-2.5 py-0.5 mb-1" style="background: #ccfbf1; color: #0f766e; font-size: 0.68rem; font-weight: 700;">
                        Agenda Kalender Terpadu RW 018
                    </span>
                    <h6 class="fw-bold mb-1" style="color: var(--nx-dark);">{{ $pengumuman->title }}</h6>
                    <div class="text-muted small" style="font-size: 0.8rem; line-height: 1.4;">
                        <div><i class="far fa-calendar me-1.5 text-teal"></i> Tanggal: <strong>{{ $pengumuman->kegiatan_tanggal ? $pengumuman->kegiatan_tanggal->translatedFormat('l, d F Y') : '-' }}</strong></div>
                        <div><i class="far fa-clock me-1.5 text-teal"></i> Waktu: <strong>{{ $pengumuman->kegiatan_waktu_mulai ?: '08:00' }} {{ $pengumuman->kegiatan_waktu_selesai ? ' - ' . $pengumuman->kegiatan_waktu_selesai : '' }} WIB</strong></div>
                        <div><i class="fas fa-location-dot me-1.5 text-teal"></i> Lokasi: <strong>{{ $pengumuman->kegiatan_lokasi ?: 'Lingkungan RW 018' }}</strong></div>
                    </div>
                </div>
            </div>
            <div>
                <a href="{{ route('kalender.index') }}" class="btn btn-sm btn-primary fw-bold px-3 py-2 shrink-0" style="border-radius: 10px; background: var(--nx-forest); border-color: var(--nx-forest);">
                    <i class="fas fa-calendar-days me-1.5"></i> Buka Kalender Kegiatan
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Attachment Card (Lampiran Berkas) -->
    @if($pengumuman->lampiran_file)
    <div class="card p-3 mb-4 border-0 shadow-sm" style="border-radius: 16px; background: #f8fafc; border: 1px solid var(--nx-border) !important;">
        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center shrink-0" style="width: 42px; height: 42px; background: #ffe4e6; color: #e11d48; font-size: 1.25rem;">
                    <i class="fas fa-file-pdf"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0" style="color: var(--nx-dark); font-size: 0.88rem;">Dokumen Lampiran Resmi</h6>
                    <span class="text-muted small" style="font-size: 0.76rem;">{{ basename($pengumuman->lampiran_file) }}</span>
                </div>
            </div>
            <div>
                <a href="{{ asset('storage/' . $pengumuman->lampiran_file) }}" target="_blank" download class="btn btn-sm btn-light fw-bold px-3 py-2 text-dark" style="border-radius: 10px; border: 1px solid var(--nx-border);">
                    <i class="fas fa-download me-1.5 text-success"></i> Unduh Berkas Lampiran
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Action / Share Bar -->
    <div class="pt-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2" style="border-color: var(--nx-border) !important;">
        <div class="d-flex align-items-center gap-2">
            <button type="button" onclick="copyAnnouncementLink()" class="btn btn-sm btn-light fw-bold px-3 py-2" style="border-radius: 10px; border: 1px solid var(--nx-border);">
                <i class="far fa-copy me-1.5"></i> Salin Tautan
            </button>
            <a href="https://wa.me/?text={{ urlencode('*' . $pengumuman->title . "*\n\n" . Str::limit(strip_tags($pengumuman->content), 200) . "\n\nBaca selengkapnya di NexaNest: " . url()->current()) }}"
               target="_blank" class="btn btn-sm btn-success fw-bold px-3 py-2 text-white" style="border-radius: 10px; background: #25D366; border-color: #25D366;">
                <i class="fab fa-whatsapp me-1.5"></i> Bagikan ke WhatsApp
            </a>
        </div>

        <div class="text-muted small" style="font-size: 0.78rem;">
            <i class="fas fa-circle-check text-success me-1"></i> Telah Anda baca secara resmi
        </div>
    </div>
</div>

<!-- ==================== CITIZEN CONFIRMATION & RSVP SECTION ==================== -->
@if($pengumuman->requires_rsvp)
<div class="card p-4 border-0 shadow-sm mb-4" style="border-radius: 18px; border: 1px solid var(--nx-border) !important;">
    <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom flex-wrap gap-2" style="border-color: var(--nx-border) !important;">
        <div>
            <h6 class="fw-bold mb-0 d-flex align-items-center gap-2" style="color: var(--nx-dark);">
                <i class="fas fa-clipboard-question" style="color: var(--nx-forest);"></i>
                <span>Konfirmasi &amp; Tanggapan Warga (RSVP)</span>
            </h6>
            <div class="text-muted small" style="font-size: 0.78rem;">
                Pengurus membutuhkan konfirmasi keikutsertaan atau tanda pemahaman Anda atas warta ini.
            </div>
        </div>

        @if($myRsvp)
        <span class="badge rounded-pill px-3 py-1.5"
              style="background: {{ $myRsvp->response_badge['bg'] }}; color: {{ $myRsvp->response_badge['color'] }}; font-size: 0.75rem; font-weight: 700;">
            <i class="fas {{ $myRsvp->response_badge['icon'] }} me-1"></i>
            <span>Status Anda: {{ $myRsvp->response_label }}</span>
        </span>
        @endif
    </div>

    <form action="{{ route('pengumuman.rsvp', $pengumuman->id) }}" method="POST">
        @csrf

        <!-- Pilihan Respons Sesuai RSVP Type -->
        <div class="mb-3">
            <label class="form-label small fw-bold d-block" style="color: var(--nx-dark);">Pilih Tanggapan Anda:</label>

            @if($pengumuman->rsvp_type === 'hadir_tidak')
            <div class="row g-2" style="max-width: 500px;">
                <div class="col-6">
                    <label class="card p-3 cursor-pointer text-decoration-none d-flex flex-row align-items-center justify-content-between" style="border-radius: 12px; border: 1px solid #a7f3d0; background: #ecfdf5; cursor: pointer;">
                        <span class="small fw-bold d-flex align-items-center gap-1.5" style="color: var(--nx-forest);">
                            <i class="fas fa-circle-check"></i> Hadir / Siap Datang
                        </span>
                        <input type="radio" name="response" value="hadir" {{ ($myRsvp && $myRsvp->response === 'hadir') || !$myRsvp ? 'checked' : '' }} class="form-check-input">
                    </label>
                </div>
                <div class="col-6">
                    <label class="card p-3 cursor-pointer text-decoration-none d-flex flex-row align-items-center justify-content-between" style="border-radius: 12px; border: 1px solid #fecaca; background: #fef2f2; cursor: pointer;">
                        <span class="small fw-bold text-danger d-flex align-items-center gap-1.5">
                            <i class="fas fa-circle-xmark"></i> Tidak Dapat Hadir
                        </span>
                        <input type="radio" name="response" value="tidak_hadir" {{ $myRsvp && $myRsvp->response === 'tidak_hadir' ? 'checked' : '' }} class="form-check-input">
                    </label>
                </div>
            </div>

            @elseif($pengumuman->rsvp_type === 'partisipasi')
            <div class="row g-2" style="max-width: 500px;">
                <div class="col-6">
                    <label class="card p-3 cursor-pointer text-decoration-none d-flex flex-row align-items-center justify-content-between" style="border-radius: 12px; border: 1px solid #a7f3d0; background: #ecfdf5; cursor: pointer;">
                        <span class="small fw-bold d-flex align-items-center gap-1.5" style="color: var(--nx-forest);">
                            <i class="fas fa-handshake-angle"></i> Ikut Berpartisipasi
                        </span>
                        <input type="radio" name="response" value="ikut" {{ ($myRsvp && $myRsvp->response === 'ikut') || !$myRsvp ? 'checked' : '' }} class="form-check-input">
                    </label>
                </div>
                <div class="col-6">
                    <label class="card p-3 cursor-pointer text-decoration-none d-flex flex-row align-items-center justify-content-between" style="border-radius: 12px; border: 1px solid #fecaca; background: #fef2f2; cursor: pointer;">
                        <span class="small fw-bold text-danger d-flex align-items-center gap-1.5">
                            <i class="fas fa-user-xmark"></i> Tidak Ikut / Berhalangan
                        </span>
                        <input type="radio" name="response" value="tidak_ikut" {{ $myRsvp && $myRsvp->response === 'tidak_ikut' ? 'checked' : '' }} class="form-check-input">
                    </label>
                </div>
            </div>

            @else
            <!-- konfirmasi_baca -->
            <div style="max-width: 500px;">
                <label class="card p-3 cursor-pointer text-decoration-none d-flex flex-row align-items-center justify-content-between" style="border-radius: 12px; border: 1px solid #a7f3d0; background: #ecfdf5; cursor: pointer;">
                    <span class="small fw-bold d-flex align-items-center gap-1.5" style="color: var(--nx-forest);">
                        <i class="fas fa-check-double"></i> Konfirmasi Telah Membaca &amp; Memahami Warta
                    </span>
                    <input type="radio" name="response" value="konfirmasi" checked class="form-check-input">
                </label>
            </div>
            @endif
        </div>

        <!-- Catatan Opsional -->
        <div class="mb-3" style="max-width: 500px;">
            <label class="form-label small fw-bold" style="color: var(--nx-dark);">Catatan Tambahan (Opsional):</label>
            <input type="text" name="catatan" value="{{ $myRsvp ? $myRsvp->catatan : '' }}" placeholder="Contoh: Bersedia hadir membawa peralatan kebersihan / Ada izin dinas..."
                   class="form-control" style="border-radius: 12px; border: 1px solid var(--nx-border); font-size: 0.85rem;">
        </div>

        <div>
            <button type="submit" class="btn btn-primary fw-bold px-4 py-2" style="border-radius: 12px; background: var(--nx-forest); border-color: var(--nx-forest);">
                <i class="fas fa-paper-plane me-1.5"></i> {{ $myRsvp ? 'Perbarui Konfirmasi Saya' : 'Kirim Konfirmasi Tanggapan' }}
            </button>
        </div>
    </form>
</div>
@endif

<!-- ==================== OFFICER READ TRACKING & RSVP RECAP PANEL ==================== -->
@if(!$isWarga && $statsKeterbacaan)
<div class="card p-4 border-0 shadow-sm" style="border-radius: 18px; border: 1px solid var(--nx-border) !important;">
    <div class="d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom flex-wrap gap-2" style="border-color: var(--nx-border) !important;">
        <div>
            <h6 class="fw-bold mb-0 d-flex align-items-center gap-2" style="color: var(--nx-dark);">
                <i class="fas fa-chart-pie" style="color: var(--nx-forest);"></i>
                <span>Panel Pengurus: Statistik Keterbacaan &amp; Respons Warga</span>
            </h6>
            <div class="text-muted small" style="font-size: 0.78rem;">
                Monitoring kepatuhan keterbacaan surat edaran dan rekapitulasi konfirmasi warga target.
            </div>
        </div>
        <span class="badge rounded-pill px-3 py-1.5" style="background: var(--nx-mint); color: var(--nx-forest); font-weight: 700;">
            Khusus Pengurus RT/RW
        </span>
    </div>

    <!-- 4 Metric Mini Cards -->
    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-6">
            <div class="card p-3 border-0 bg-light" style="border-radius: 14px; border: 1px solid var(--nx-border) !important;">
                <span class="text-muted small">Target Warga (KK)</span>
                <h4 class="fw-bold mb-0 mt-1" style="color: var(--nx-dark);">{{ $statsKeterbacaan['total_target'] }}</h4>
                <span class="text-muted" style="font-size: 0.72rem;">Penerima warta</span>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="card p-3 border-0" style="border-radius: 14px; background: #ecfdf5; border: 1px solid #a7f3d0 !important;">
                <span class="text-success small fw-bold">Sudah Membaca</span>
                <h4 class="fw-bold mb-0 mt-1 text-success">{{ $statsKeterbacaan['total_read'] }}</h4>
                <span class="text-success" style="font-size: 0.72rem; font-weight: 700;">{{ $statsKeterbacaan['percentage'] }}% keterbacaan</span>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="card p-3 border-0" style="border-radius: 14px; background: #fef2f2; border: 1px solid #fecaca !important;">
                <span class="text-danger small fw-bold">Belum Membaca</span>
                <h4 class="fw-bold mb-0 mt-1 text-danger">{{ $statsKeterbacaan['total_unread'] }}</h4>
                <span class="text-danger" style="font-size: 0.72rem;">Perlu tindak lanjut</span>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="card p-3 border-0" style="border-radius: 14px; background: #eef2ff; border: 1px solid #c7d2fe !important;">
                <span class="text-primary small fw-bold">Respons RSVP</span>
                <h4 class="fw-bold mb-0 mt-1 text-primary">{{ $statsRsvp ? $statsRsvp['total'] : 0 }}</h4>
                <span class="text-primary" style="font-size: 0.72rem; font-weight: 700;">
                    @if($statsRsvp && $statsRsvp['total'] > 0)
                        {{ $statsRsvp['hadir'] }} Hadir / {{ $statsRsvp['tidak_hadir'] }} Berhalangan
                    @else
                        Belum ada respons
                    @endif
                </span>
            </div>
        </div>
    </div>

    <!-- Progress Bar Keterbacaan Global -->
    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between small mb-1.5 fw-bold">
            <span style="color: var(--nx-dark);">Progres Keterbacaan Seluruh Sasaran:</span>
            <span style="color: var(--nx-forest);">{{ $statsKeterbacaan['percentage'] }}% Selesai Terbaca</span>
        </div>
        <div class="progress" style="height: 10px; border-radius: 999px; background: #e2e8f0;">
            <div class="progress-bar" role="progressbar" style="width: {{ $statsKeterbacaan['percentage'] }}%; background: var(--nx-forest); border-radius: 999px;" aria-valuenow="{{ $statsKeterbacaan['percentage'] }}" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>

    <!-- Breakdown Keterbacaan per Wilayah RT -->
    <div class="mb-4">
        <h6 class="fw-bold text-uppercase mb-2" style="font-size: 0.78rem; color: var(--nx-dark); letter-spacing: 0.05em;">
            Rekapitulasi Keterbacaan Berdasarkan RT (RT 006 s.d. RT 010)
        </h6>
        <div class="row g-2">
            @foreach($statsKeterbacaan['rt_breakdown'] as $rt => $data)
            <div class="col">
                <div class="card p-2 text-center border-0 bg-light" style="border-radius: 12px; border: 1px solid var(--nx-border) !important;">
                    <span class="small fw-bold d-block" style="color: var(--nx-dark); font-size: 0.78rem;">RT {{ $rt }}</span>
                    <span class="fw-bold d-block my-0.5" style="color: var(--nx-forest); font-size: 1.1rem;">{{ $data['pct'] }}%</span>
                    <span class="text-muted d-block" style="font-size: 0.68rem;">{{ $data['read'] }} / {{ $data['total'] }} KK</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Daftar Penerima & Catatan RSVP -->
    <div>
        <h6 class="fw-bold text-uppercase mb-2" style="font-size: 0.78rem; color: var(--nx-dark); letter-spacing: 0.05em;">
            Daftar Sampel Warga &amp; Status Respons Konfirmasi
        </h6>
        <div class="table-responsive border rounded-3" style="border-color: var(--nx-border) !important;">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.82rem;">
                <thead class="table-light">
                    <tr>
                        <th class="py-2.5 px-3">Nama Warga / Akun</th>
                        <th class="py-2.5 px-3">Wilayah</th>
                        <th class="py-2.5 px-3">Status Keterbacaan</th>
                        <th class="py-2.5 px-3">Respons Konfirmasi (RSVP)</th>
                        <th class="py-2.5 px-3">Catatan Warga</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $readsUserMap = $pengumuman->reads->keyBy('user_id');
                        $rsvpsUserMap = $pengumuman->rsvps->keyBy('user_id');
                        $wargaSample = \App\Models\User::where('role', 'warga')
                            ->when($pengumuman->target_rt && $pengumuman->target_rt !== 'all', function ($q) use ($pengumuman) {
                                $q->where('rt_number', $pengumuman->target_rt);
                            })
                            ->take(15)
                            ->get();
                    @endphp

                    @foreach($wargaSample as $wu)
                    @php
                        $readRec = $readsUserMap->get($wu->id);
                        $rsvpRec = $rsvpsUserMap->get($wu->id);
                    @endphp
                    <tr>
                        <td class="py-2 px-3 fw-bold" style="color: var(--nx-dark);">
                            {{ $wu->name }}
                            <div class="text-muted small fw-normal" style="font-size: 0.72rem;">{{ $wu->email }}</div>
                        </td>
                        <td class="py-2 px-3">
                            <span class="badge bg-light text-secondary rounded-pill px-2 py-1" style="font-size: 0.72rem;">
                                RT {{ $wu->rt_number ?: '006' }}
                            </span>
                        </td>
                        <td class="py-2 px-3">
                            @if($readRec)
                            <span class="text-success fw-bold d-flex align-items-center gap-1">
                                <i class="fas fa-check-double"></i>
                                <span>Dibaca ({{ $readRec->read_at->format('d/m, H:i') }})</span>
                            </span>
                            @else
                            <span class="text-danger fw-semibold d-flex align-items-center gap-1">
                                <i class="far fa-clock"></i>
                                <span>Belum Dibaca</span>
                            </span>
                            @endif
                        </td>
                        <td class="py-2 px-3">
                            @if($rsvpRec)
                            <span class="badge rounded-pill px-2.5 py-1"
                                  style="background: {{ $rsvpRec->response_badge['bg'] }}; color: {{ $rsvpRec->response_badge['color'] }}; font-size: 0.72rem; font-weight: 700;">
                                {{ $rsvpRec->response_label }}
                            </span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="py-2 px-3 text-muted" style="font-size: 0.78rem;">
                            {{ $rsvpRec && $rsvpRec->catatan ? $rsvpRec->catatan : '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="text-muted small mt-2" style="font-size: 0.72rem; font-style: italic;">
            * Menampilkan hingga 15 sampel warga target. Data keterbacaan individual dilindungi hak akses pengurus dan tidak ditampilkan kepada sesama warga.
        </div>
    </div>
</div>
@endif

<script>
    function copyAnnouncementLink() {
        const dummy = document.createElement('input');
        document.body.appendChild(dummy);
        dummy.value = window.location.href;
        dummy.select();
        document.execCommand('copy');
        document.body.removeChild(dummy);
        alert('Tautan warta pengumuman resmi berhasil disalin ke papan klip!');
    }
</script>
@endsection
