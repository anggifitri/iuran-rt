@extends('layouts.app')

@section('title', 'Buat Pengumuman Baru — NexaNest')

@section('content')
<!-- Header Card -->
<div class="nx-page-header mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
            <a href="{{ route('pengumuman.index') }}" class="badge rounded-pill text-decoration-none px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
                <i class="fas fa-bullhorn me-1"></i> Pusat Pengumuman
            </a>
            <span class="text-muted small">/</span>
            <span class="badge bg-light text-secondary rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                Formulir Penerbitan Warta
            </span>
        </div>
        <h1 class="nx-page-title mb-1">Penerbitan Pengumuman Resmi</h1>
        <p class="nx-page-subtitle mb-0">
            Publikasikan maklumat resmi, surat edaran, imbauan ketertiban, agenda kegiatan, atau peringatan darurat kepada warga dengan sasaran terukur.
        </p>
    </div>
    <div>
        <a href="{{ route('pengumuman.index') }}" class="btn btn-outline-secondary fw-bold" style="border-radius: 12px;">
            <i class="fas fa-arrow-left me-1.5"></i> Kembali ke Pusat Warta
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
<form action="{{ route('pengumuman.store') }}" method="POST" enctype="multipart/form-data" id="formPengumuman">
    @csrf

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
                    <input type="text" name="title" id="inputTitle" value="{{ old('title') }}" required
                           placeholder="Contoh: Kerja Bakti Akbar Bersih Lingkungan & Normalisasi Drainase RW 018"
                           class="form-control" style="border-radius: 12px; border: 1px solid var(--nx-border); font-size: 0.9rem; font-weight: 600;">
                    <div class="form-text small text-muted">Gunakan judul yang ringkas, tegas, dan mudah dipahami warga.</div>
                </div>

                <!-- Kategori & Target RT -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6 col-12">
                        <label class="form-label small fw-bold" style="color: var(--nx-dark);">
                            Kategori Informasi <span class="text-danger">*</span>
                        </label>
                        <select name="kategori" id="inputKategori" required class="form-select" style="border-radius: 12px; border: 1px solid var(--nx-border); font-size: 0.85rem;">
                            <option value="umum" {{ old('kategori') == 'umum' ? 'selected' : '' }}>Umum: Informasi Lingkungan &amp; Warga</option>
                            <option value="kegiatan" {{ old('kategori') == 'kegiatan' ? 'selected' : '' }}>Kegiatan: Kerja Bakti, Rapat, Musyawarah</option>
                            <option value="keuangan" {{ old('kategori') == 'keuangan' ? 'selected' : '' }}>Keuangan: Laporan Kas, Iuran, Transparansi</option>
                            <option value="keamanan" {{ old('kategori') == 'keamanan' ? 'selected' : '' }}>Keamanan: Siskamling, Ronda, Ketertiban</option>
                            <option value="kesehatan" {{ old('kategori') == 'kesehatan' ? 'selected' : '' }}>Kesehatan: Posyandu Balita &amp; Ibu Hamil</option>
                            <option value="sosial" {{ old('kategori') == 'sosial' ? 'selected' : '' }}>Sosial: Donasi, Bansos, Santunan Warga</option>
                            <option value="darurat" {{ old('kategori') == 'darurat' ? 'selected' : '' }}>Darurat: Kondisi Bencana &amp; Situasi Mendesak</option>
                        </select>
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label small fw-bold" style="color: var(--nx-dark);">
                            Sasaran Target Penerima <span class="text-danger">*</span>
                        </label>
                        @if(Auth::user()->isAdminRw())
                        <select name="target_rt" required class="form-select" style="border-radius: 12px; border: 1px solid var(--nx-border); font-size: 0.85rem;">
                            <option value="all" {{ old('target_rt') == 'all' ? 'selected' : '' }}>Seluruh Warga RW 018 (Pleno 5 RT)</option>
                            @foreach($rtList as $rt)
                                <option value="{{ $rt }}" {{ old('target_rt') == $rt ? 'selected' : '' }}>Khusus Warga RT {{ $rt }}</option>
                            @endforeach
                        </select>
                        @else
                        <div>
                            <input type="hidden" name="target_rt" value="{{ Auth::user()->rt_number }}">
                            <input type="text" readonly disabled value="Khusus Wilayah RT {{ Auth::user()->rt_number }} (Terkunci Sesuai Wilayah)"
                                   class="form-control bg-light text-muted fw-bold" style="border-radius: 12px; border: 1px solid var(--nx-border); font-size: 0.85rem;">
                        </div>
                        <div class="form-text small text-muted">Admin RT memiliki kewenangan penerbitan untuk wilayah RT yang dipimpin.</div>
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
                                    <input type="radio" name="prioritas" value="darurat" {{ old('prioritas') == 'darurat' ? 'checked' : '' }} class="form-check-input">
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
                                    <input type="radio" name="prioritas" value="penting" {{ old('prioritas') == 'penting' ? 'checked' : '' }} class="form-check-input">
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
                                    <input type="radio" name="prioritas" value="kegiatan" {{ old('prioritas') == 'kegiatan' ? 'checked' : '' }} class="form-check-input">
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
                                    <input type="radio" name="prioritas" value="informasi" {{ old('prioritas', 'informasi') == 'informasi' ? 'checked' : '' }} class="form-check-input">
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
                              placeholder="Tuliskan isi pengumuman, latar belakang, poin-poin instruksi, waktu pelaksanaan, serta narahubung secara lengkap dan jelas..."
                              class="form-control" style="border-radius: 12px; border: 1px solid var(--nx-border); font-size: 0.85rem; line-height: 1.6;">{{ old('content') }}</textarea>
                </div>

                <!-- Lampiran File -->
                <div>
                    <label class="form-label small fw-bold" style="color: var(--nx-dark);">
                        Lampiran Berkas / Surat Edaran Resmi / Poster (Opsional)
                    </label>
                    <div class="card p-3 text-center border-dashed" style="border-radius: 14px; border: 2px dashed var(--nx-border); background: #fafafa;">
                        <i class="fas fa-cloud-arrow-up text-secondary fs-3 mb-1"></i>
                        <input type="file" name="lampiran_file" id="lampiran_file" accept=".pdf,.jpg,.jpeg,.png,.webp" class="form-control form-control-sm mx-auto" style="max-width: 400px; border-radius: 10px;">
                        <span class="text-muted mt-2" style="font-size: 0.72rem;">Mendukung berkas PDF, JPG, PNG, atau WEBP dengan ukuran maksimal 5 MB.</span>
                    </div>
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
                        <input class="form-check-input" type="checkbox" role="switch" name="is_kegiatan" id="toggleKegiatan" value="1" {{ old('is_kegiatan') ? 'checked' : '' }} onchange="toggleKegiatanFields()">
                        <label class="form-check-label small fw-bold text-muted" for="toggleKegiatan">Sinkronkan ke Kalender</label>
                    </div>
                </div>

                <div id="boxKegiatan" style="{{ old('is_kegiatan') ? '' : 'display: none;' }}">
                    <p class="text-muted small mb-3" style="font-size: 0.82rem;">
                        Jika diaktifkan, agenda kegiatan ini akan otomatis tercatat pada <strong>Kalender Kegiatan Terpadu RW 018</strong> tanpa perlu input ulang data.
                    </p>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4 col-12">
                            <label class="form-label small fw-bold">Tanggal Kegiatan</label>
                            <input type="date" name="kegiatan_tanggal" value="{{ old('kegiatan_tanggal', date('Y-m-d')) }}"
                                   class="form-control" style="border-radius: 12px; border: 1px solid var(--nx-border); font-size: 0.85rem;">
                        </div>
                        <div class="col-md-4 col-6">
                            <label class="form-label small fw-bold">Waktu Mulai</label>
                            <input type="time" name="kegiatan_waktu_mulai" value="{{ old('kegiatan_waktu_mulai', '08:00') }}"
                                   class="form-control" style="border-radius: 12px; border: 1px solid var(--nx-border); font-size: 0.85rem;">
                        </div>
                        <div class="col-md-4 col-6">
                            <label class="form-label small fw-bold">Waktu Selesai (Opsional)</label>
                            <input type="time" name="kegiatan_waktu_selesai" value="{{ old('kegiatan_waktu_selesai', '11:30') }}"
                                   class="form-control" style="border-radius: 12px; border: 1px solid var(--nx-border); font-size: 0.85rem;">
                        </div>
                    </div>

                    <div>
                        <label class="form-label small fw-bold">Lokasi Pelaksanaan</label>
                        <input type="text" name="kegiatan_lokasi" value="{{ old('kegiatan_lokasi', 'Balai Warga RW 018') }}" placeholder="Contoh: Balai Warga RW 018 / Sepanjang Saluran RT 007"
                               class="form-control" style="border-radius: 12px; border: 1px solid var(--nx-border); font-size: 0.85rem;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Settings & Action Buttons (col-lg-4) -->
        <div class="col-lg-4">
            <!-- 3. Interaktivitas & Konfirmasi Warga (RSVP) -->
            <div class="card p-4 border-0 shadow-sm mb-4" style="border-radius: 18px; border: 1px solid var(--nx-border) !important;">
                <h6 class="fw-bold text-uppercase pb-2 mb-3 border-bottom d-flex align-items-center gap-2" style="font-size: 0.82rem; color: var(--nx-dark); letter-spacing: 0.05em; border-color: var(--nx-border) !important;">
                    <i class="fas fa-users-viewfinder" style="color: var(--nx-forest);"></i>
                    <span>3. Konfirmasi Respon Warga</span>
                </h6>

                <div class="mb-3">
                    <div class="form-check form-switch mb-1">
                        <input class="form-check-input" type="checkbox" role="switch" name="requires_rsvp" id="toggleRsvp" value="1" {{ old('requires_rsvp') ? 'checked' : '' }} onchange="toggleRsvpFields()">
                        <label class="form-check-label small fw-bold text-dark" for="toggleRsvp">Wajibkan Konfirmasi Warga</label>
                    </div>
                    <div class="text-muted" style="font-size: 0.72rem;">Mengaktifkan tombol konfirmasi kehadiran/tanggapan resmi pada halaman warga.</div>
                </div>

                <div id="boxRsvp" class="pt-2 border-top" style="{{ old('requires_rsvp') ? '' : 'display: none;' }} border-color: var(--nx-border) !important;">
                    <label class="form-label small fw-bold">Tipe Konfirmasi Respons</label>
                    <select name="rsvp_type" class="form-select" style="border-radius: 12px; border: 1px solid var(--nx-border); font-size: 0.85rem;">
                        <option value="partisipasi" {{ old('rsvp_type') == 'partisipasi' ? 'selected' : '' }}>Kerja Bakti: Ikut Berpartisipasi / Berhalangan</option>
                        <option value="hadir_tidak" {{ old('rsvp_type') == 'hadir_tidak' ? 'selected' : '' }}>Rapat / Musyawarah: Hadir / Tidak Hadir</option>
                        <option value="konfirmasi_baca" {{ old('rsvp_type') == 'konfirmasi_baca' ? 'selected' : '' }}>Pendataan / Maklumat: Sudah Membaca &amp; Mengerti</option>
                    </select>
                </div>
            </div>

            <!-- 4. Penjadwalan & Visibilitas -->
            <div class="card p-4 border-0 shadow-sm mb-4" style="border-radius: 18px; border: 1px solid var(--nx-border) !important;">
                <h6 class="fw-bold text-uppercase pb-2 mb-3 border-bottom d-flex align-items-center gap-2" style="font-size: 0.82rem; color: var(--nx-dark); letter-spacing: 0.05em; border-color: var(--nx-border) !important;">
                    <i class="fas fa-sliders" style="color: var(--nx-forest);"></i>
                    <span>4. Penjadwalan &amp; Visibilitas</span>
                </h6>

                <!-- Pinned Checkbox -->
                <div class="card p-3 mb-3 border-0" style="border-radius: 12px; background: #fefce8; border: 1px solid #fef08a !important;">
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" name="is_pinned" id="is_pinned" value="1" {{ old('is_pinned') ? 'checked' : '' }}>
                        <label class="form-check-label small fw-bold text-warning-emphasis d-flex align-items-center gap-1" for="is_pinned">
                            <i class="fas fa-thumbtack text-warning"></i> Sematkan Warta (Pinned)
                        </label>
                        <span class="text-muted d-block mt-1" style="font-size: 0.72rem; line-height: 1.3;">
                            Tampil di urutan teratas daftar pengumuman dan disorot pada dashboard warga.
                        </span>
                    </div>
                </div>

                <!-- Tanggal Publikasi -->
                <div class="mb-3">
                    <label class="form-label small fw-bold">Jadwalkan Waktu Publikasi (Opsional)</label>
                    <input type="datetime-local" name="published_at" value="{{ old('published_at') }}"
                           class="form-control" style="border-radius: 12px; border: 1px solid var(--nx-border); font-size: 0.85rem;">
                    <span class="text-muted d-block mt-1" style="font-size: 0.72rem;">Kosongkan jika ingin langsung dipublikasikan saat ini.</span>
                </div>

                <!-- Tanggal Berakhir -->
                <div>
                    <label class="form-label small fw-bold">Tanggal Berakhir / Kedaluwarsa</label>
                    <input type="date" name="tanggal_berakhir" value="{{ old('tanggal_berakhir') }}"
                           class="form-control" style="border-radius: 12px; border: 1px solid var(--nx-border); font-size: 0.85rem;">
                    <span class="text-muted d-block mt-1" style="font-size: 0.72rem;">Warta otomatis berpindah ke arsip setelah tanggal ini terlewati.</span>
                </div>
            </div>

            <!-- 5. Tombol Aksi Penerbitan -->
            <div class="card p-4 border-0 shadow-sm" style="border-radius: 18px; border: 1px solid var(--nx-border) !important;">
                <h6 class="fw-bold text-uppercase pb-2 mb-3 border-bottom" style="font-size: 0.82rem; color: var(--nx-dark); letter-spacing: 0.05em; border-color: var(--nx-border) !important;">
                    Tindakan Penerbitan
                </h6>

                <!-- Hidden input to store action type -->
                <input type="hidden" name="action_type" id="inputActionType" value="publish_now">

                <div class="d-grid gap-2">
                    <!-- Button: Publikasikan Sekarang -->
                    <button type="button" onclick="submitForm('publish_now')" class="btn btn-primary fw-bold py-2.5" style="border-radius: 12px; background: var(--nx-forest); border-color: var(--nx-forest);">
                        <i class="fas fa-bullhorn me-1.5"></i> Publikasikan Sekarang
                    </button>

                    <!-- Button: Jadwalkan Publikasi -->
                    <button type="button" onclick="submitForm('schedule')" class="btn btn-info text-white fw-bold py-2" style="border-radius: 12px; background: #0284c7; border-color: #0284c7;">
                        <i class="fas fa-clock me-1.5"></i> Jadwalkan Publikasi
                    </button>

                    <!-- Button: Simpan Draft -->
                    <button type="button" onclick="submitForm('draft')" class="btn btn-light fw-bold py-2 text-secondary" style="border-radius: 12px; border: 1px solid var(--nx-border);">
                        <i class="fas fa-file-pen me-1.5"></i> Simpan Sebagai Draft
                    </button>

                    <!-- Button: Pratinjau Modal -->
                    <button type="button" onclick="openPreviewModal()" class="btn btn-outline-secondary fw-bold py-2" style="border-radius: 12px;">
                        <i class="far fa-eye me-1.5"></i> Pratinjau Tampilan Warta
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- ==================== MODAL PRATINJAU PENGUMUMAN ==================== -->
<div class="modal fade" id="modalPreview" tabindex="-1" aria-labelledby="modalPreviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-bottom px-4 py-3" style="border-color: var(--nx-border) !important;">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge rounded-pill px-3 py-1" style="background: var(--nx-mint); color: var(--nx-forest); font-weight: 700;">Pratinjau Pengumuman</span>
                    <span class="text-muted small">Simulasi tampilan bagi warga</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <h4 id="prevTitle" class="fw-bold mb-2" style="color: var(--nx-dark);"></h4>
                <div class="d-flex align-items-center gap-2 text-muted small pb-3 mb-3 border-bottom" style="font-size: 0.78rem; border-color: var(--nx-border) !important;">
                    <span id="prevKategori" class="fw-bold" style="color: var(--nx-forest);"></span>
                    <span>•</span>
                    <span>Oleh: {{ Auth::user()->name }}</span>
                    <span>•</span>
                    <span>{{ date('d M Y') }}</span>
                </div>
                <div id="prevContent" class="text-muted small" style="white-space: pre-line; line-height: 1.6; font-size: 0.88rem;"></div>
            </div>
            <div class="modal-footer border-top px-4 py-2.5" style="border-color: var(--nx-border) !important;">
                <button type="button" class="btn btn-light fw-bold px-3 py-2" data-bs-dismiss="modal" style="border-radius: 10px;">
                    Tutup Pratinjau
                </button>
            </div>
        </div>
    </div>
</div>

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

    function openPreviewModal() {
        const title = document.getElementById('inputTitle').value || 'Judul Pengumuman';
        const katElem = document.getElementById('inputKategori');
        const kategori = katElem.options[katElem.selectedIndex].text;
        const content = document.getElementById('inputContent').value || 'Isi maklumat pengumuman akan tampil di sini...';

        document.getElementById('prevTitle').textContent = title;
        document.getElementById('prevKategori').textContent = kategori;
        document.getElementById('prevContent').textContent = content;

        const modal = new bootstrap.Modal(document.getElementById('modalPreview'));
        modal.show();
    }
</script>
@endsection
