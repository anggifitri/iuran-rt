@extends('layouts.app')

@section('title', 'Detail Laporan Lingkungan RT ' . $laporan->rt_number . ' — NexaNest')

@section('content')
<div class="nx-page-header">
    <div>
        <div class="d-flex align-items-center gap-2 mb-2">
            <a href="{{ route('lingkungan.index') }}" class="text-muted text-decoration-none small">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Pusat Laporan
            </a>
            <span class="text-muted">/</span>
            <span class="badge rounded-pill px-2.5 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
                Detail Laporan Lingkungan
            </span>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <h1 class="nx-page-title mb-0">Laporan Lingkungan RT {{ $laporan->rt_number }}</h1>
            <span class="badge {{ $laporan->status === 'diterima' ? 'bg-success' : ($laporan->status === 'perlu_revisi' ? 'bg-danger' : ($laporan->status === 'draft' ? 'bg-warning text-dark' : 'bg-info text-dark')) }} rounded-pill px-3 py-1 fs-6">
                {{ $laporan->status_label }}
            </span>
            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1" style="font-size: 0.75rem;">
                Versi {{ $laporan->versi }}
            </span>
            @if($laporan->is_terlambat)
                <span class="badge bg-danger rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">
                    <i class="fas fa-clock me-1"></i> Terlambat
                </span>
            @endif
        </div>
        <p class="nx-page-subtitle mt-1 mb-0">
            Periode: <strong>{{ $laporan->nama_bulan }} {{ $laporan->periode_tahun }}</strong> · Pengirim: {{ $laporan->user ? $laporan->user->name : '-' }}
            @if($laporan->tanggal_pengiriman)
                · Dikirim pada: {{ $laporan->tanggal_pengiriman->translatedFormat('d F Y, H:i') }} WIB
            @endif
        </p>
    </div>

    <!-- ACTION BUTTONS -->
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('lingkungan.cetak', $laporan->id) }}" target="_blank" class="btn btn-outline-secondary fw-bold" style="border-radius: 12px;">
            <i class="fas fa-print me-1"></i> Cetak Dokumen
        </a>

        @if($laporan->canBeEditedBy(Auth::user()))
            <a href="{{ route('lingkungan.edit', $laporan->id) }}" class="btn btn-warning fw-bold text-dark" style="border-radius: 12px;">
                <i class="fas fa-pen-to-square me-1"></i> {{ $laporan->status === 'perlu_revisi' ? 'Kirimkan Revisi Laporan' : 'Edit Laporan' }}
            </a>
        @endif

        @if(Auth::user()->isAdminRw())
            <button type="button" class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#modalEvaluasiRw" style="border-radius: 12px; background: var(--nx-forest); border-color: var(--nx-forest);">
                <i class="fas fa-clipboard-check me-1"></i> Evaluasi Laporan (RW)
            </button>
        @endif
    </div>
</div>

<!-- ALERT EVALUASI RW JIKA SUDAH ADA -->
@if($laporan->catatan_evaluasi_rw || $laporan->catatan_rw)
    <div class="card p-3.5 mb-4 shadow-xs" style="background: {{ $laporan->status === 'diterima' ? '#F0F9F3' : '#FFF5F5' }}; border-left: 4px solid {{ $laporan->status === 'diterima' ? '#285943' : '#DC3545' }}; border-radius: 12px;">
        <div class="d-flex gap-3">
            <span class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px; background: {{ $laporan->status === 'diterima' ? '#E1F3E7' : '#FCE8E8' }}; color: {{ $laporan->status === 'diterima' ? '#285943' : '#DC3545' }};">
                <i class="fas {{ $laporan->status === 'diterima' ? 'fa-circle-check' : 'fa-comment-dots' }} fa-lg"></i>
            </span>
            <div class="flex-grow-1">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <h6 class="fw-bold mb-0" style="color: {{ $laporan->status === 'diterima' ? '#285943' : '#DC3545' }};">
                        Catatan Evaluasi Resmi Ketua RW 018:
                    </h6>
                    <small class="text-muted" style="font-size: 0.72rem;">
                        {{ $laporan->tanggal_pemeriksaan ? $laporan->tanggal_pemeriksaan->translatedFormat('d F Y, H:i') : '-' }} WIB
                    </small>
                </div>
                <p class="text-dark small mb-1" style="font-size: 0.82rem; line-height: 1.45;">
                    "{{ $laporan->catatan_evaluasi_rw ?: $laporan->catatan_rw }}"
                </p>
                <small class="text-muted" style="font-size: 0.72rem;">
                    Pemeriksa: <strong>{{ $laporan->pemeriksa ? $laporan->pemeriksa->name : 'Ketua RW 018' }}</strong>
                </small>
            </div>
        </div>
    </div>
@endif

<div class="row g-4">
    <!-- KONTEN RINCIAN LAPORAN -->
    <div class="col-lg-8">
        <!-- 1. KONDISI KEBERSIHAN -->
        <div class="card p-4 mb-4 shadow-xs" style="border-radius: 12px;">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: var(--nx-mint-soft); color: var(--nx-forest);">
                        <i class="fas fa-broom"></i>
                    </span>
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 1rem;">1. Kondisi Kebersihan Lingkungan</h5>
                </div>
                <span class="badge rounded-pill px-3 py-1 fw-bold" style="background: var(--nx-mint); color: var(--nx-forest); font-size: 0.75rem;">
                    {{ $laporan->kondisi_kebersihan }}
                </span>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem;">Area yang Terjaga Kebersihannya:</span>
                    <p class="small text-dark mb-0 bg-light p-2.5 rounded-2">
                        {{ $laporan->area_terjaga_kebersihan ?: 'Seluruh area lingkungan terpelihara bersih.' }}
                    </p>
                </div>
                <div class="col-md-6">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem;">Area yang Butuh Perhatian:</span>
                    <p class="small text-dark mb-0 bg-light p-2.5 rounded-2">
                        {{ $laporan->area_perlu_perhatian_kebersihan ?: 'Tidak ada area kritis yang membutuhkan perhatian khusus.' }}
                    </p>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem;">Permasalahan Sampah:</span>
                    <p class="small text-dark mb-0">
                        {{ $laporan->masalah_sampah ?: 'Pengangkutan sampah berjalan normal sesuai jadwal 3x seminggu.' }}
                    </p>
                </div>
                <div class="col-md-6">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem;">Keterangan Tambahan Kebersihan:</span>
                    <p class="small text-dark mb-0">
                        {{ $laporan->keterangan_kebersihan ?: '-' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- 2. KONDISI DRAINASE & SALURAN AIR -->
        <div class="card p-4 mb-4 shadow-xs" style="border-radius: 12px;">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: var(--nx-powder-soft); color: #1e4b6d;">
                        <i class="fas fa-water"></i>
                    </span>
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 1rem;">2. Kondisi Drainase &amp; Saluran Air</h5>
                </div>
                <span class="badge rounded-pill px-3 py-1 fw-bold" style="background: var(--nx-powder); color: #1e4b6d; font-size: 0.75rem;">
                    {{ $laporan->kondisi_drainase }}
                </span>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem;">Kondisi Aliran Air:</span>
                    <p class="small text-dark mb-0 bg-light p-2.5 rounded-2">
                        <strong>{{ $laporan->kondisi_aliran_air ?: 'Lancar Mengalir' }}</strong>
                    </p>
                </div>
                <div class="col-md-6">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem;">Titik Rawan Genangan:</span>
                    <p class="small text-dark mb-0 bg-light p-2.5 rounded-2">
                        {{ $laporan->titik_rawan_genangan ?: 'Nihil titik genangan air.' }}
                    </p>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem;">Saluran Butuh Pengerukan:</span>
                    <p class="small text-dark mb-0">
                        {{ $laporan->saluran_butuh_pembersihan ?: 'Tidak ada saluran yang membutuhkan pengerukan mendesak.' }}
                    </p>
                </div>
                <div class="col-md-6">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem;">Keterangan Tambahan Drainase:</span>
                    <p class="small text-dark mb-0">
                        {{ $laporan->keterangan_drainase ?: '-' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- 3. PENGHIJAUAN & RUANG TERBUKA -->
        <div class="card p-4 mb-4 shadow-xs" style="border-radius: 12px;">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #FAF8EA; color: #8C6A05;">
                        <i class="fas fa-tree"></i>
                    </span>
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 1rem;">3. Penghijauan &amp; Ruang Terbuka</h5>
                </div>
                <span class="badge rounded-pill px-3 py-1 fw-bold" style="background: #FAF8EA; color: #8C6A05; font-size: 0.75rem;">
                    {{ $laporan->kondisi_penghijauan ?: 'Subur & Asri' }}
                </span>
            </div>

            <div class="row g-3 mb-2">
                <div class="col-md-6">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem;">Ruang Terbuka &amp; Taman:</span>
                    <p class="small text-dark mb-0">
                        {{ $laporan->ketersediaan_ruang_terbuka ?: 'Taman lingkungan terawat baik.' }}
                    </p>
                </div>
                <div class="col-md-6">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem;">Kegiatan Perawatan Tanaman:</span>
                    <p class="small text-dark mb-0">
                        {{ $laporan->kegiatan_perawatan_tanaman ?: 'Penyiraman dan pemupukan kompos rutin.' }}
                    </p>
                </div>
            </div>

            @if($laporan->kendala_penghijauan)
                <div class="mt-2 pt-2 border-top">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem;">Kendala Penghijauan:</span>
                    <p class="small text-danger mb-0">
                        <i class="fas fa-circle-exclamation me-1"></i>{{ $laporan->kendala_penghijauan }}
                    </p>
                </div>
            @endif
        </div>

        <!-- 4. PROGRAM & KEGIATAN LINGKUNGAN -->
        <div class="card p-4 mb-4 shadow-xs" style="border-radius: 12px;">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: var(--nx-mint-soft); color: var(--nx-forest);">
                        <i class="fas fa-hand-holding-hand"></i>
                    </span>
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 1rem;">4. Program &amp; Kegiatan Gotong Royong</h5>
                </div>
                @if($laporan->jumlah_peserta_kegiatan > 0)
                    <span class="badge rounded-pill px-3 py-1 bg-light text-dark border fw-bold" style="font-size: 0.75rem;">
                        <i class="fas fa-users me-1 text-success"></i>{{ $laporan->jumlah_peserta_kegiatan }} Warga Berpartisipasi
                    </span>
                @endif
            </div>

            <div class="mb-3">
                <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem;">Kerja Bakti Terlaksana:</span>
                <p class="small text-dark mb-0 bg-light p-2.5 rounded-2">
                    {{ $laporan->kegiatan_kerja_bakti ?: 'Pembersihan rutin gotong royong berkala.' }}
                </p>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem;">Program Kebersihan &amp; Pilah Sampah:</span>
                    <p class="small text-dark mb-0">
                        {{ $laporan->program_kebersihan ?: 'Penyetoran ke Bank Sampah RW.' }}
                    </p>
                </div>
                <div class="col-md-6">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem;">Program Lingkungan Lainnya:</span>
                    <p class="small text-dark mb-0">
                        {{ $laporan->program_lingkungan_lainnya ?: '-' }}
                    </p>
                </div>
            </div>

            @if($agendaTerkait)
                <div class="mt-3 pt-2.5 border-top">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem;">Agenda Terkait yang Ditautkan:</span>
                    <div class="d-flex align-items-center gap-2 p-2 rounded-2 bg-light">
                        <i class="fas fa-calendar-check text-success"></i>
                        <span class="small text-dark fw-bold">{{ $agendaTerkait->judul }}</span>
                        <small class="text-muted">({{ \Carbon\Carbon::parse($agendaTerkait->tanggal_waktu)->translatedFormat('d M Y') }})</small>
                    </div>
                </div>
            @endif
        </div>

        <!-- 5. KENDALA & RENCANA PERBAIKAN -->
        <div class="card p-4 mb-4 shadow-xs" style="border-radius: 12px;">
            <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                <span class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #FFF5F5; color: #DC3545;">
                    <i class="fas fa-triangle-exclamation"></i>
                </span>
                <h5 class="fw-bold mb-0 text-dark" style="font-size: 1rem;">5. Kendala &amp; Tindak Lanjut</h5>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem;">Kendala yang Ditemukan:</span>
                    <p class="small text-danger mb-0 bg-light p-2.5 rounded-2">
                        {{ $laporan->kendala_lingkungan ?: 'Tidak ada kendala berarti di lapangan.' }}
                    </p>
                </div>
                <div class="col-md-6">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem;">Lokasi Perhatian Khusus:</span>
                    <p class="small text-dark mb-0 bg-light p-2.5 rounded-2">
                        {{ $laporan->lokasi_perhatian_khusus ?: 'Tidak ada lokasi kritis.' }}
                    </p>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem;">Rencana Tindak Lanjut RT:</span>
                    <p class="small text-dark mb-0">
                        {{ $laporan->rencana_tindak_lanjut ?: 'Monitoring pemeliharaan rutin terus dilanjutkan.' }}
                    </p>
                </div>
                <div class="col-md-6">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem;">Bantuan Koordinasi dari RW:</span>
                    <p class="small text-dark mb-0">
                        {{ $laporan->bantuan_rw_dibutuhkan ?: '-' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- 6. DOKUMENTASI FOTO -->
        <div class="card p-4 shadow-xs" style="border-radius: 12px;">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #F0F4F8; color: #1e4b6d;">
                        <i class="fas fa-camera"></i>
                    </span>
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 1rem;">6. Dokumentasi Foto Lapangan</h5>
                </div>
                @if($laporan->keterangan_foto)
                    <small class="text-muted" style="font-size: 0.74rem;">{{ $laporan->keterangan_foto }}</small>
                @endif
            </div>

            <div class="row g-3">
                @if($laporan->foto_dokumentasi)
                    <div class="col-md-4">
                        <div class="border rounded-3 p-2 bg-light text-center h-100">
                            <span class="badge bg-secondary mb-1.5" style="font-size: 0.68rem;">Foto Utama Lingkungan</span>
                            <a href="{{ asset('storage/' . $laporan->foto_dokumentasi) }}" target="_blank">
                                <img src="{{ asset('storage/' . $laporan->foto_dokumentasi) }}" class="img-fluid rounded-2 shadow-xs" style="max-height: 180px; object-fit: cover;" alt="Foto Utama">
                            </a>
                        </div>
                    </div>
                @endif

                @if($laporan->foto_sebelum)
                    <div class="col-md-4">
                        <div class="border rounded-3 p-2 bg-light text-center h-100">
                            <span class="badge bg-warning text-dark mb-1.5" style="font-size: 0.68rem;">Foto Sebelum Kegiatan</span>
                            <a href="{{ asset('storage/' . $laporan->foto_sebelum) }}" target="_blank">
                                <img src="{{ asset('storage/' . $laporan->foto_sebelum) }}" class="img-fluid rounded-2 shadow-xs" style="max-height: 180px; object-fit: cover;" alt="Sebelum">
                            </a>
                        </div>
                    </div>
                @endif

                @if($laporan->foto_sesudah)
                    <div class="col-md-4">
                        <div class="border rounded-3 p-2 bg-light text-center h-100">
                            <span class="badge bg-success mb-1.5" style="font-size: 0.68rem;">Foto Sesudah Kegiatan</span>
                            <a href="{{ asset('storage/' . $laporan->foto_sesudah) }}" target="_blank">
                                <img src="{{ asset('storage/' . $laporan->foto_sesudah) }}" class="img-fluid rounded-2 shadow-xs" style="max-height: 180px; object-fit: cover;" alt="Sesudah">
                            </a>
                        </div>
                    </div>
                @endif

                @if(!$laporan->foto_dokumentasi && !$laporan->foto_sebelum && !$laporan->foto_sesudah)
                    <div class="col-12 text-center py-4 text-muted small">
                        <i class="fas fa-image fa-2x mb-1 d-block opacity-50"></i>
                        Tidak ada lampiran foto dokumentasi pada laporan ini.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- SIDEBAR: AUDIT LOG, BANK SAMPAH, PENGADUAN -->
    <div class="col-lg-4">
        <!-- AUDIT LOG & RIWAYAT VERSI EVALUASI -->
        <div class="card p-3.5 mb-4 shadow-xs" style="border-radius: 12px;">
            <h6 class="fw-bold mb-3 text-dark" style="font-size: 0.92rem;">
                <i class="fas fa-clock-rotate-left text-success me-2"></i>Riwayat &amp; Log Evaluasi Laporan
            </h6>
            <div class="timeline-activity">
                @forelse($laporan->histories as $h)
                    <div class="d-flex gap-2.5 mb-3 pb-2 border-bottom">
                        <span class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-0.5" 
                              style="width: 26px; height: 26px; background: {{ $h->aksi === 'diterima' ? '#EBF8F2' : ($h->aksi === 'revisi_diminta' ? '#FFF5F5' : '#F0F4F8') }}; color: {{ $h->aksi === 'diterima' ? '#285943' : ($h->aksi === 'revisi_diminta' ? '#DC3545' : '#1e4b6d') }}; font-size: 0.72rem;">
                            <i class="fas {{ $h->aksi === 'diterima' ? 'fa-check' : ($h->aksi === 'revisi_diminta' ? 'fa-rotate-left' : 'fa-paper-plane') }}"></i>
                        </span>
                        <div class="flex-grow-1 small" style="font-size: 0.76rem;">
                            <div class="d-flex justify-content-between align-items-center mb-0.5">
                                <strong class="text-dark">{{ $h->user ? $h->user->name : 'Sistem' }}</strong>
                                <small class="text-muted" style="font-size: 0.68rem;">{{ $h->created_at->format('d/m/Y H:i') }}</small>
                            </div>
                            <span class="badge bg-light text-dark border px-2 py-0.2 mb-1" style="font-size: 0.65rem;">
                                {{ ucfirst(str_replace('_', ' ', $h->aksi)) }} (v{{ $h->versi }})
                            </span>
                            @if($h->catatan)
                                <p class="mb-0 text-muted p-1.5 rounded-2 bg-light" style="font-size: 0.7rem; line-height: 1.35;">
                                    "{{ $h->catatan }}"
                                </p>
                            @endif
                        </div>
                    </div>
                @empty
                    <small class="text-muted">Belum ada riwayat aktivitas.</small>
                @endforelse
            </div>
        </div>

        <!-- INTEGRASI BANK SAMPAH RT -->
        <div class="card p-3.5 mb-4 shadow-xs" style="background: linear-gradient(135deg, #F5FBF6 0%, #FFFFFF 100%); border-radius: 12px;">
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: var(--nx-mint); color: var(--nx-forest);">
                    <i class="fas fa-recycle"></i>
                </span>
                <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.9rem;">Setoran Bank Sampah RT {{ $laporan->rt_number }}</h6>
            </div>
            <p class="text-muted small mb-2" style="font-size: 0.74rem;">
                Total tabungan sampah terpilah warga RT {{ $laporan->rt_number }} pada periode {{ $laporan->nama_bulan }} {{ $laporan->periode_tahun }}:
            </p>
            <div class="row g-2 text-center mb-2">
                <div class="col-6">
                    <div class="p-2 rounded-2 bg-white border">
                        <span class="text-muted small d-block" style="font-size: 0.68rem;">Volume:</span>
                        <strong class="text-dark">{{ number_format($bankSampahSummary ? $bankSampahSummary->total_kg : 0, 1) }} Kg</strong>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-2 rounded-2 bg-white border">
                        <span class="text-muted small d-block" style="font-size: 0.68rem;">Nilai Rupiah:</span>
                        <strong class="text-success">Rp {{ number_format($bankSampahSummary ? $bankSampahSummary->total_rp : 0, 0, ',', '.') }}</strong>
                    </div>
                </div>
            </div>
            <a href="{{ route('bank_sampah.index', ['rt_number' => $laporan->rt_number]) }}" class="btn btn-sm btn-outline-success w-100 fw-bold" style="font-size: 0.72rem; border-radius: 8px;">
                Buka Data Bank Sampah <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>

        <!-- INTEGRASI PENGADUAN WARGA -->
        <div class="card p-3.5 shadow-xs" style="border-radius: 12px;">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.9rem;">
                    <i class="fas fa-triangle-exclamation text-warning me-1.5"></i>Pengaduan Terkait RT {{ $laporan->rt_number }}
                </h6>
                <span class="badge bg-light text-dark border">{{ count($pengaduanTerkait) }} Aduan</span>
            </div>
            <div class="list-group list-group-flush small" style="max-height: 250px; overflow-y: auto;">
                @forelse($pengaduanTerkait as $aduan)
                    <div class="list-group-item px-0 py-2 border-bottom">
                        <a href="{{ route('pengaduan.show', $aduan->id) }}" class="text-dark fw-bold text-decoration-none d-block text-truncate" style="font-size: 0.78rem;">
                            {{ $aduan->title }}
                        </a>
                        <small class="text-muted d-block text-truncate" style="font-size: 0.7rem;">{{ $aduan->content }}</small>
                        <span class="badge bg-secondary-subtle text-secondary rounded-pill mt-1" style="font-size: 0.65rem;">
                            {{ ucfirst($aduan->status) }}
                        </span>
                    </div>
                @empty
                    <small class="text-muted d-block py-2">Tidak ada pengaduan kebersihan/drainase di RT ini.</small>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- MODAL EVALUASI RW -->
@if(Auth::user()->isAdminRw())
<div class="modal fade" id="modalEvaluasiRw" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('lingkungan.evaluasi', $laporan->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="font-size: 1rem; color: var(--nx-forest);">
                        <i class="fas fa-clipboard-check me-2"></i>Evaluasi Laporan Lingkungan RT {{ $laporan->rt_number }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="p-2.5 rounded-2 bg-light mb-3 small">
                        <strong>Periode:</strong> {{ $laporan->nama_bulan }} {{ $laporan->periode_tahun }}<br>
                        <strong>Pengirim:</strong> {{ $laporan->user ? $laporan->user->name : '-' }}<br>
                        <strong>Versi Saat Ini:</strong> v{{ $laporan->versi }}
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Keputusan Evaluasi Ketua RW <span class="text-danger">*</span></label>
                        <select name="keputusan" class="form-select" required>
                            <option value="diterima" {{ $laporan->status === 'diterima' ? 'selected' : '' }}>Terima Laporan (Disetujui Resmi)</option>
                            <option value="perlu_revisi" {{ $laporan->status === 'perlu_revisi' ? 'selected' : '' }}>Minta Revisi (Kembalikan ke Admin RT)</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold">Catatan Evaluasi / Alasan Keputusan <span class="text-danger">*</span></label>
                        <textarea name="catatan_evaluasi_rw" rows="4" class="form-control" placeholder="Tuliskan catatan apresiasi jika disetujui, atau instruksi perbaikan yang jelas jika meminta revisi..." required>{{ $laporan->catatan_evaluasi_rw ?: $laporan->catatan_rw }}</textarea>
                        <small class="text-muted" style="font-size: 0.72rem;">Catatan ini hanya dapat dilihat oleh Admin RW dan Pengurus RT terkait.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-3 fw-bold">Simpan Evaluasi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
