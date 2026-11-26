@extends('layouts.app')

@section('title', 'Edit Pengumuman: ' . $pengumuman->title . ' — NexaNest')

@section('content')
<!-- Header Card -->
<div class="nx-page-header mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
            <a href="{{ route('pengumuman.index') }}" class="badge rounded-pill text-decoration-none px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
                <i class="fas fa-bullhorn me-1"></i> Pusat Pengumuman
            </a>
            <span class="text-muted small">/</span>
            <a href="{{ route('pengumuman.show', $pengumuman->id) }}" class="badge bg-light text-secondary rounded-pill px-2.5 py-1 text-decoration-none" style="font-size: 0.72rem;">
                Warta #{{ $pengumuman->id }}
            </a>
            <span class="text-muted small">/</span>
            <span class="badge bg-light text-secondary rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                Edit Warta
            </span>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
            <h1 class="nx-page-title mb-0">Edit Pengumuman Resmi</h1>
            <span class="badge rounded-pill px-2.5 py-1 {{ $pengumuman->status === 'published' ? 'bg-success' : 'bg-warning text-dark' }}" style="font-size: 0.72rem;">
                {{ ucfirst($pengumuman->status) }}
            </span>
        </div>
        <p class="nx-page-subtitle mb-0">
            Perbarui rincian warta, sasaran wilayah warga, jadwal kalender kegiatan, atau pengaturan respons RSVP.
        </p>
    </div>
    <div>
        <a href="{{ route('pengumuman.show', $pengumuman->id) }}" class="btn btn-outline-secondary fw-bold" style="border-radius: 12px;">
            <i class="fas fa-arrow-left me-1.5"></i> Batal / Lihat Warta
        </a>
    </div>
</div>

<!-- Feedback Alerts -->
@if(isset($errors) && $errors->any())
<div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 14px;">
    <div class="d-flex align-items-center mb-1">
        <i class="fas fa-exclamation-circle me-2 fs-5"></i>
        <strong class="small">Mohon periksa kembali formulir Anda:</strong>
    </div>
    <ul class="mb-0 ps-3 small">
        @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Main Form Grid -->
<form action="{{ route('pengumuman.update', $pengumuman->id) }}" method="POST" enctype="multipart/form-data" id="formPengumuman">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <!-- Left Column: Main Announcement Content (col-lg-8) -->
        <div class="col-lg-8">
            <!-- 1. Card Detail Konten -->
            <div class="card p-4 border-0 shadow-sm mb-4" style="border-radius: 18px; border: 1px solid var(--nx-border) !important;">
                <h6 class="fw-bold text-uppercase pb-2 mb-3 border-bottom d-flex align-items-center gap-2" style="font-size: 0.82rem; color: var(--nx-dark); letter-spacing: 0.05em; border-color: var(--nx-border) !important;">
                    <i class="fas fa-pen-nib" style="color: var(--nx-forest);"></i>
                    <span>1. Isi &amp; Identitas Pengumuman</span>
                </h6>

                <!-- Judul Pengumuman -->
                <div class="mb-3">
                    <label class="form-label small fw-bold" style="color: var(--nx-dark);">
                        Judul Pengumuman Resmi <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="title" id="inputTitle" value="{{ old('title', $pengumuman->title) }}" required
                           class="form-control" style="border-radius: 12px; border: 1px solid var(--nx-border); font-size: 0.9rem; font-weight: 600;">
                </div>

                <!-- Kategori & Target RT -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6 col-12">
                        <label class="form-label small fw-bold" style="color: var(--nx-dark);">
                            Kategori Informasi <span class="text-danger">*</span>
                        </label>
                        <select name="kategori" id="inputKategori" required class="form-select" style="border-radius: 12px; border: 1px solid var(--nx-border); font-size: 0.85rem;">
                            <option value="umum" {{ old('kategori', $pengumuman->kategori) == 'umum' ? 'selected' : '' }}>Umum: Informasi Lingkungan &amp; Warga</option>
                            <option value="kegiatan" {{ old('kategori', $pengumuman->kategori) == 'kegiatan' ? 'selected' : '' }}>Kegiatan: Kerja Bakti, Rapat, Musyawarah</option>
                            <option value="keuangan" {{ old('kategori', $pengumuman->kategori) == 'keuangan' ? 'selected' : '' }}>Keuangan: Laporan Kas, Iuran, Transparansi</option>
                            <option value="keamanan" {{ old('kategori', $pengumuman->kategori) == 'keamanan' ? 'selected' : '' }}>Keamanan: Siskamling, Ronda, Ketertiban</option>
                            <option value="kesehatan" {{ old('kategori', $pengumuman->kategori) == 'kesehatan' ? 'selected' : '' }}>Kesehatan: Posyandu Balita &amp; Ibu Hamil</option>
                            <option value="sosial" {{ old('kategori', $pengumuman->kategori) == 'sosial' ? 'selected' : '' }}>Sosial: Donasi, Bansos, Santunan Warga</option>
                            <option value="darurat" {{ old('kategori', $pengumuman->kategori) == 'darurat' ? 'selected' : '' }}>Darurat: Kondisi Bencana &amp; Situasi Mendesak</option>
                        </select>
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label small fw-bold" style="color: var(--nx-dark);">
                            Sasaran Target Penerima <span class="text-danger">*</span>
                        </label>
                        @if(Auth::user()->isAdminRw())
                        <select name="target_rt" required class="form-select" style="border-radius: 12px; border: 1px solid var(--nx-border); font-size: 0.85rem;">
                            <option value="all" {{ old('target_rt', $pengumuman->target_rt) == 'all' ? 'selected' : '' }}>Seluruh Warga RW 018 (Pleno 5 RT)</option>
                            @foreach($rtList as $rt)
                                <option value="{{ $rt }}" {{ old('target_rt', $pengumuman->target_rt) == $rt ? 'selected' : '' }}>Khusus Warga RT {{ $rt }}</option>
                            @endforeach
                        </select>
                        @else
                        <div>
                            <input type="hidden" name="target_rt" value="{{ Auth::user()->rt_number }}">
                            <input type="text" readonly disabled value="Khusus Wilayah RT {{ Auth::user()->rt_number }} (Terkunci Sesuai Wilayah)"
                                   class="form-control bg-light text-muted fw-bold" style="border-radius: 12px; border: 1px solid var(--nx-border); font-size: 0.85rem;">
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Tingkat Prioritas (4 Visual Cards) -->
                <div class="mb-3">
                    <label class="form-label small fw-bold d-block" style="color: var(--nx-dark);">
                        Tingkat Prioritas Warta <span class="text-danger">*</span>
                    </label>
                    <div class="row g-2">
                        <!-- 1. Darurat -->
                        <div class="col-6 col-sm-3">
                            <label class="card h-100 p-2.5 cursor-pointer text-decoration-none d-flex flex-column justify-content-between" style="border-radius: 12px; border: 1px solid #fecaca; background: #fef2f2; cursor: pointer;">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="small fw-bold text-danger d-flex align-items-center gap-1">
                                        <i class="fas fa-triangle-exclamation"></i> Darurat
                                    </span>
                                    <input type="radio" name="prioritas" value="darurat" {{ old('prioritas', $pengumuman->prioritas) == 'darurat' ? 'checked' : '' }} class="form-check-input">
                                </div>
                                <span class="text-muted" style="font-size: 0.68rem; line-height: 1.2;">Perhatian segera &amp; genting</span>
                            </label>
                        </div>

                        <!-- 2. Penting -->
                        <div class="col-6 col-sm-3">
                            <label class="card h-100 p-2.5 cursor-pointer text-decoration-none d-flex flex-column justify-content-between" style="border-radius: 12px; border: 1px solid #fef08a; background: #fefce8; cursor: pointer;">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="small fw-bold text-warning-emphasis d-flex align-items-center gap-1">
                                        <i class="fas fa-bell"></i> Penting
                                    </span>
                                    <input type="radio" name="prioritas" value="penting" {{ old('prioritas', $pengumuman->prioritas) == 'penting' ? 'checked' : '' }} class="form-check-input">
                                </div>
                                <span class="text-muted" style="font-size: 0.68rem; line-height: 1.2;">Butuh atensi / tindakan warga</span>
                            </label>
                        </div>

                        <!-- 3. Kegiatan -->
                        <div class="col-6 col-sm-3">
                            <label class="card h-100 p-2.5 cursor-pointer text-decoration-none d-flex flex-column justify-content-between" style="border-radius: 12px; border: 1px solid var(--nx-sage); background: var(--nx-mint-soft); cursor: pointer;">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="small fw-bold d-flex align-items-center gap-1" style="color: var(--nx-forest);">
                                        <i class="fas fa-calendar-days"></i> Kegiatan
                                    </span>
                                    <input type="radio" name="prioritas" value="kegiatan" {{ old('prioritas', $pengumuman->prioritas) == 'kegiatan' ? 'checked' : '' }} class="form-check-input">
                                </div>
                                <span class="text-muted" style="font-size: 0.68rem; line-height: 1.2;">Agenda kerja bakti &amp; rapat</span>
                            </label>
                        </div>

                        <!-- 4. Informasi -->
                        <div class="col-6 col-sm-3">
                            <label class="card h-100 p-2.5 cursor-pointer text-decoration-none d-flex flex-column justify-content-between" style="border-radius: 12px; border: 1px solid #bfdbfe; background: #eff6ff; cursor: pointer;">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="small fw-bold text-primary d-flex align-items-center gap-1">
                                        <i class="fas fa-circle-info"></i> Informasi
                                    </span>
                                    <input type="radio" name="prioritas" value="informasi" {{ old('prioritas', $pengumuman->prioritas ?: 'informasi') == 'informasi' ? 'checked' : '' }} class="form-check-input">
                                </div>
                                <span class="text-muted" style="font-size: 0.68rem; line-height: 1.2;">Pemberitahuan umum warga</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Isi Lengkap Pengumuman -->
                <div class="mb-3">
                    <label class="form-label small fw-bold" style="color: var(--nx-dark);">
                        Isi Maklumat / Deskripsi Lengkap <span class="text-danger">*</span>
                    </label>
                    <textarea name="content" id="inputContent" rows="8" required
                              class="form-control" style="border-radius: 12px; border: 1px solid var(--nx-border); font-size: 0.85rem; line-height: 1.6;">{{ old('content', $pengumuman->content) }}</textarea>
                </div>

                <!-- Lampiran File -->
                <div>
                    <label class="form-label small fw-bold" style="color: var(--nx-dark);">
                        Lampiran Berkas / Surat Edaran Resmi / Poster
                    </label>
                    @if($pengumuman->lampiran_file)
                    <div class="alert alert-light border d-flex align-items-center justify-content-between p-2 mb-2" style="border-radius: 12px; font-size: 0.82rem;">
                        <span class="d-flex align-items-center gap-2">
                            <i class="fas fa-file-pdf text-danger"></i>
                            <span>Lampiran aktif: <strong>{{ basename($pengumuman->lampiran_file) }}</strong></span>
                        </span>
                        <a href="{{ asset('storage/' . $pengumuman->lampiran_file) }}" target="_blank" class="btn btn-sm btn-link p-0 fw-bold text-decoration-none" style="color: var(--nx-forest);">
                            Lihat Berkas
                        </a>
                    </div>
                    @endif
                    <input type="file" name="lampiran_file" id="lampiran_file" accept=".pdf,.jpg,.jpeg,.png,.webp" class="form-control form-control-sm" style="border-radius: 10px;">
                </div>
            </div>

            <!-- 2. Integrasi Kalender Kegiatan Lingkungan -->
            <div class="card p-4 border-0 shadow-sm" style="border-radius: 18px; border: 1px solid var(--nx-border) !important;">
                <div class="d-flex align-items-center justify-content-between pb-2 mb-3 border-bottom" style="border-color: var(--nx-border) !important;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-calendar-check" style="color: var(--nx-forest);"></i>
                        <h6 class="fw-bold text-uppercase mb-0" style="font-size: 0.82rem; color: var(--nx-dark); letter-spacing: 0.05em;">
                            2. Integrasi Kalender Kegiatan Lingkungan
                        </h6>
                    </div>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" role="switch" name="is_kegiatan" id="toggleKegiatan" value="1" {{ old('is_kegiatan', $pengumuman->is_kegiatan) ? 'checked' : '' }} onchange="toggleKegiatanFields()">
                        <label class="form-check-label small fw-bold text-muted" for="toggleKegiatan">Sinkronkan ke Kalender</label>
                    </div>
                </div>

                <div id="boxKegiatan" style="{{ old('is_kegiatan', $pengumuman->is_kegiatan) ? '' : 'display: none;' }}">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4 col-12">
                            <label class="form-label small fw-bold">Tanggal Kegiatan</label>
                            <input type="date" name="kegiatan_tanggal" value="{{ old('kegiatan_tanggal', $pengumuman->kegiatan_tanggal ? $pengumuman->kegiatan_tanggal->format('Y-m-d') : '') }}"
                                   class="form-control" style="border-radius: 12px; border: 1px solid var(--nx-border); font-size: 0.85rem;">
                        </div>
                        <div class="col-md-4 col-6">
                            <label class="form-label small fw-bold">Waktu Mulai</label>
                            <input type="time" name="kegiatan_waktu_mulai" value="{{ old('kegiatan_waktu_mulai', $pengumuman->kegiatan_waktu_mulai ?: '08:00') }}"
                                   class="form-control" style="border-radius: 12px; border: 1px solid var(--nx-border); font-size: 0.85rem;">
                        </div>
                        <div class="col-md-4 col-6">
                            <label class="form-label small fw-bold">Waktu Selesai</label>
                            <input type="time" name="kegiatan_waktu_selesai" value="{{ old('kegiatan_waktu_selesai', $pengumuman->kegiatan_waktu_selesai ?: '11:30') }}"
                                   class="form-control" style="border-radius: 12px; border: 1px solid var(--nx-border); font-size: 0.85rem;">
                        </div>
                    </div>

                    <div>
                        <label class="form-label small fw-bold">Lokasi Pelaksanaan</label>
                        <input type="text" name="kegiatan_lokasi" value="{{ old('kegiatan_lokasi', $pengumuman->kegiatan_lokasi ?: 'Balai Warga RW 018') }}"
                               class="form-control" style="border-radius: 12px; border: 1px solid var(--nx-border); font-size: 0.85rem;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Settings & Buttons (col-lg-4) -->
        <div class="col-lg-4">
            <!-- 3. Konfirmasi Warga (RSVP) -->
            <div class="card p-4 border-0 shadow-sm mb-4" style="border-radius: 18px; border: 1px solid var(--nx-border) !important;">
                <h6 class="fw-bold text-uppercase pb-2 mb-3 border-bottom d-flex align-items-center gap-2" style="font-size: 0.82rem; color: var(--nx-dark); letter-spacing: 0.05em; border-color: var(--nx-border) !important;">
                    <i class="fas fa-users-viewfinder" style="color: var(--nx-forest);"></i>
                    <span>3. Konfirmasi Respon Warga</span>
                </h6>

                <div class="mb-3">
                    <div class="form-check form-switch mb-1">
                        <input class="form-check-input" type="checkbox" role="switch" name="requires_rsvp" id="toggleRsvp" value="1" {{ old('requires_rsvp', $pengumuman->requires_rsvp) ? 'checked' : '' }} onchange="toggleRsvpFields()">
                        <label class="form-check-label small fw-bold text-dark" for="toggleRsvp">Wajibkan Konfirmasi Warga</label>
                    </div>
                    <div class="text-muted" style="font-size: 0.72rem;">Warga diwajibkan memberikan tanggapan / konfirmasi resmi.</div>
                </div>

                <div id="boxRsvp" class="pt-2 border-top" style="{{ old('requires_rsvp', $pengumuman->requires_rsvp) ? '' : 'display: none;' }} border-color: var(--nx-border) !important;">
                    <label class="form-label small fw-bold">Tipe Konfirmasi Respons</label>
                    <select name="rsvp_type" class="form-select" style="border-radius: 12px; border: 1px solid var(--nx-border); font-size: 0.85rem;">
                        <option value="partisipasi" {{ old('rsvp_type', $pengumuman->rsvp_type) == 'partisipasi' ? 'selected' : '' }}>Kerja Bakti: Ikut Berpartisipasi / Berhalangan</option>
                        <option value="hadir_tidak" {{ old('rsvp_type', $pengumuman->rsvp_type) == 'hadir_tidak' ? 'selected' : '' }}>Rapat / Musyawarah: Hadir / Tidak Hadir</option>
                        <option value="konfirmasi_baca" {{ old('rsvp_type', $pengumuman->rsvp_type) == 'konfirmasi_baca' ? 'selected' : '' }}>Pendataan / Maklumat: Sudah Membaca &amp; Mengerti</option>
                    </select>
                </div>
            </div>

            <!-- 4. Penjadwalan & Masa Berlaku -->
            <div class="card p-4 border-0 shadow-sm mb-4" style="border-radius: 18px; border: 1px solid var(--nx-border) !important;">
                <h6 class="fw-bold text-uppercase pb-2 mb-3 border-bottom d-flex align-items-center gap-2" style="font-size: 0.82rem; color: var(--nx-dark); letter-spacing: 0.05em; border-color: var(--nx-border) !important;">
                    <i class="fas fa-sliders" style="color: var(--nx-forest);"></i>
                    <span>4. Penjadwalan &amp; Visibilitas</span>
                </h6>

                <!-- Pinned Checkbox -->
                <div class="card p-3 mb-3 border-0" style="border-radius: 12px; background: #fefce8; border: 1px solid #fef08a !important;">
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" name="is_pinned" id="is_pinned" value="1" {{ old('is_pinned', $pengumuman->is_pinned) ? 'checked' : '' }}>
                        <label class="form-check-label small fw-bold text-warning-emphasis d-flex align-items-center gap-1" for="is_pinned">
                            <i class="fas fa-thumbtack text-warning"></i> Sematkan Warta (Pinned)
                        </label>
                        <span class="text-muted d-block mt-1" style="font-size: 0.72rem; line-height: 1.3;">
                            Tampil di urutan teratas daftar pengumuman.
                        </span>
                    </div>
                </div>

                <!-- Tanggal Publikasi -->
                <div class="mb-3">
                    <label class="form-label small fw-bold">Waktu Publikasi</label>
                    <input type="datetime-local" name="published_at" value="{{ old('published_at', $pengumuman->published_at ? $pengumuman->published_at->format('Y-m-d\TH:i') : '') }}"
                           class="form-control" style="border-radius: 12px; border: 1px solid var(--nx-border); font-size: 0.85rem;">
                </div>

                <!-- Tanggal Berakhir -->
                <div>
                    <label class="form-label small fw-bold">Tanggal Berakhir / Kedaluwarsa</label>
                    <input type="date" name="tanggal_berakhir" value="{{ old('tanggal_berakhir', $pengumuman->tanggal_berakhir ? $pengumuman->tanggal_berakhir->format('Y-m-d') : '') }}"
                           class="form-control" style="border-radius: 12px; border: 1px solid var(--nx-border); font-size: 0.85rem;">
                </div>
            </div>

            <!-- 5. Tombol Aksi Simpan -->
            <div class="card p-4 border-0 shadow-sm" style="border-radius: 18px; border: 1px solid var(--nx-border) !important;">
                <h6 class="fw-bold text-uppercase pb-2 mb-3 border-bottom" style="font-size: 0.82rem; color: var(--nx-dark); letter-spacing: 0.05em; border-color: var(--nx-border) !important;">
                    Tindakan Perubahan
                </h6>

                <input type="hidden" name="action_type" id="inputActionType" value="{{ $pengumuman->status === 'draft' ? 'draft' : 'publish_now' }}">

                <div class="d-grid gap-2">
                    <button type="button" onclick="submitForm('publish_now')" class="btn btn-primary fw-bold py-2.5" style="border-radius: 12px; background: var(--nx-forest); border-color: var(--nx-forest);">
                        <i class="fas fa-check me-1.5"></i> Simpan &amp; Publikasikan
                    </button>

                    @if($pengumuman->status === 'draft')
                    <button type="button" onclick="submitForm('draft')" class="btn btn-light fw-bold py-2 text-secondary" style="border-radius: 12px; border: 1px solid var(--nx-border);">
                        <i class="fas fa-floppy-disk me-1.5"></i> Simpan Sebagai Draft
                    </button>
                    @endif

                    <a href="{{ route('pengumuman.show', $pengumuman->id) }}" class="btn btn-outline-secondary fw-bold py-2" style="border-radius: 12px;">
                        Batal Perubahan
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    function toggleKegiatanFields() {
        const checked = document.getElementById('toggleKegiatan').checked;
        const box = document.getElementById('boxKegiatan');
        box.style.display = checked ? 'block' : 'none';
    }

    function toggleRsvpFields() {
        const checked = document.getElementById('toggleRsvp').checked;
        const box = document.getElementById('boxRsvp');
        box.style.display = checked ? 'block' : 'none';
    }

    function submitForm(actionType) {
        document.getElementById('inputActionType').value = actionType;
        document.getElementById('formPengumuman').submit();
    }
</script>
@endsection
