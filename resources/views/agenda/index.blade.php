@extends('layouts.app')

@section('title', 'Agenda & Rapat RW — NexaNest')

@section('content')
<!-- HEADER HALAMAN SESUAI STANDAR NEXANEST -->
<div class="nx-page-header">
    <div>
        <span class="badge rounded-pill mb-2 px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
            <i class="fas fa-handshake me-1"></i> Koordinasi &amp; Musyawarah Lingkungan
        </span>
        <h1 class="nx-page-title">{{ Auth::user()->isWarga() ? 'Agenda & Musyawarah Warga' : 'Agenda & Rapat Pengurus RW' }}</h1>
        <p class="nx-page-subtitle">
            {{ Auth::user()->isWarga() ? 'Pusat informasi agenda musyawarah warga, jadwal pertemuan lingkungan, dan dokumentasi hasil mufakat RW 018.' : 'Pusat koordinasi agenda rapat RW/RT, pencatatan kehadiran, notulensi, dan dokumentasi keputusan rapat.' }}
        </p>
    </div>
    @if(!Auth::user()->isWarga())
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-primary px-3.5 py-2.5" data-bs-toggle="modal" data-bs-target="#modalTambahAgenda" style="border-radius: 12px; font-weight: 700; box-shadow: 0 4px 12px rgba(40,89,67,0.25);">
            <i class="fas fa-plus me-1.5"></i> Buat Agenda Rapat
        </button>
    </div>
    @endif
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

<!-- METRIC OVERVIEW CARDS (DATABASE-DRIVEN STANDAR NEXANEST) -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <x-stat-card 
            title="Agenda Mendatang" 
            value="{{ $totalMendatang }}" 
            sub="Musyawarah terjadwal" 
            icon="fas fa-calendar-check" 
            theme="forest" 
        />
    </div>
    <div class="col-md-4">
        <x-stat-card 
            title="Rapat Terlaksana" 
            value="{{ $totalSelesai }}" 
            sub="Lengkap notulensi & hasil" 
            icon="fas fa-clipboard-check" 
            theme="primary" 
            valColor="#1F567A" 
        />
    </div>
    <div class="col-md-4">
        <x-stat-card 
            title="Total Agenda" 
            value="{{ $totalAgenda }}" 
            sub="Arsip koordinasi RW 018" 
            icon="fas fa-comments" 
            theme="warning" 
            valColor="#D97706" 
        />
    </div>
</div>

<!-- FILTER & PENCARIAN TERPADU (STANDAR .nx-filter-card) -->
<div class="card nx-filter-card mb-4 shadow-xs" style="border-radius: 14px; border: 1px solid var(--nx-border);">
    <div class="p-3">
        <form method="GET" action="{{ route('agenda.index') }}" class="row g-2 align-items-center">
            <!-- Search Keyword -->
            <div class="{{ Auth::user()->isWarga() ? 'col-lg-4 col-md-6' : 'col-lg-3 col-md-6' }}">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 10px 0 0 10px;">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control form-control-sm border-start-0" placeholder="Cari judul, lokasi, topik..." value="{{ request('search') }}" style="border-radius: 0 10px 10px 0; height: 38px;">
                </div>
            </div>

            <!-- Periode Filter -->
            <div class="{{ Auth::user()->isWarga() ? 'col-lg-3 col-md-3 col-6' : 'col-lg-2 col-md-3 col-6' }}">
                <select name="periode" class="form-select form-select-sm" style="border-radius: 10px; height: 38px;">
                    <option value="">-- Semua Periode --</option>
                    <option value="bulan_ini" {{ request('periode') == 'bulan_ini' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="bulan_depan" {{ request('periode') == 'bulan_depan' ? 'selected' : '' }}>Bulan Depan</option>
                    <option value="riwayat" {{ request('periode') == 'riwayat' ? 'selected' : '' }}>Riwayat Selesai</option>
                </select>
            </div>

            <!-- Jenis Rapat Filter -->
            <div class="{{ Auth::user()->isWarga() ? 'col-lg-3 col-md-3 col-6' : 'col-lg-2 col-md-3 col-6' }}">
                <select name="jenis_rapat" class="form-select form-select-sm" style="border-radius: 10px; height: 38px;">
                    <option value="">-- Semua Jenis Rapat --</option>
                    <option value="rw" {{ request('jenis_rapat') == 'rw' ? 'selected' : '' }}>Internal RW</option>
                    <option value="koordinasi_rt" {{ request('jenis_rapat') == 'koordinasi_rt' ? 'selected' : '' }}>Koordinasi RT</option>
                    <option value="musyawarah_warga" {{ request('jenis_rapat') == 'musyawarah_warga' ? 'selected' : '' }}>Musyawarah Warga</option>
                </select>
            </div>

            @if(!Auth::user()->isWarga())
            <!-- Wilayah RT Filter -->
            <div class="col-lg-2 col-md-4 col-6">
                <select name="rt_number" class="form-select form-select-sm" style="border-radius: 10px; height: 38px;">
                    <option value="">-- Seluruh Wilayah --</option>
                    <option value="rw" {{ request('rt_number') == 'rw' ? 'selected' : '' }}>Tingkat RW (Agregat)</option>
                    @foreach($rtList as $rt)
                        <option value="{{ $rt }}" {{ request('rt_number') == $rt ? 'selected' : '' }}>RT {{ $rt }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <!-- Status Filter -->
            <div class="col-lg-2 col-md-4 col-6">
                <select name="status" class="form-select form-select-sm" style="border-radius: 10px; height: 38px;">
                    <option value="">-- Semua Status --</option>
                    <option value="akan_datang" {{ request('status') == 'akan_datang' ? 'selected' : '' }}>⏳ Akan Datang</option>
                    <option value="berlangsung" {{ request('status') == 'berlangsung' ? 'selected' : '' }}>📡 Sedang Berlangsung</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>✅ Selesai</option>
                    <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>❌ Dibatalkan</option>
                </select>
            </div>

            <!-- Actions -->
            <div class="col-lg-1 col-md-4 col-12 d-flex gap-1 justify-content-end">
                <button type="submit" class="btn btn-sm btn-primary flex-fill" style="border-radius: 10px; height: 38px; font-weight: 600;" title="Terapkan Filter">
                    <i class="fas fa-filter me-1"></i>Filter
                </button>
                @if(request()->anyFilled(['search', 'periode', 'jenis_rapat', 'rt_number', 'status']))
                    <a href="{{ route('agenda.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 10px; height: 38px; display: flex; align-items: center; justify-content: center;" title="Reset Filter">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- DAFTAR KARTU AGENDA RAPAT (STANDAR .nx-grid-card) -->
<div class="row g-3">
    @forelse($agendas as $ag)
        <div class="col-md-6 col-lg-4">
            <div class="card nx-grid-card shadow-xs h-100" style="border-radius: 14px; border: 1px solid var(--nx-border); background: #ffffff;">
                <div class="nx-grid-card-body p-3.5">
                    <!-- Top Badges -->
                    <div class="d-flex justify-content-between align-items-center mb-2.5">
                        <div>
                            {!! $ag->jenis_badge !!}
                            @if($ag->rt_number)
                                <span class="badge rounded-pill px-2 py-0.5 ms-1" style="background: #EBF3EE; color: var(--nx-forest); font-size: 0.7rem; font-weight: 700;">
                                    RT {{ $ag->rt_number }}
                                </span>
                            @endif
                        </div>
                        <div>
                            {!! $ag->status_badge !!}
                        </div>
                    </div>

                    <!-- Meeting Title -->
                    <h5 class="fw-bold mb-2 text-dark" style="font-size: 1rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 44px;">
                        <a href="{{ route('agenda.show', $ag->id) }}" class="text-decoration-none text-dark hover-forest">
                            {{ $ag->judul }}
                        </a>
                    </h5>

                    <!-- Meeting Meta -->
                    <div class="mb-3 text-muted" style="font-size: 0.8rem; line-height: 1.6;">
                        <div class="d-flex align-items-center mb-1">
                            <i class="fas fa-calendar-day me-2 text-success" style="width: 14px;"></i>
                            <span class="text-dark fw-semibold">{{ $ag->waktu_formatted }}</span>
                        </div>
                        <div class="d-flex align-items-center mb-1">
                            <i class="fas fa-location-dot me-2 text-danger" style="width: 14px;"></i>
                            <span class="text-truncate">{{ $ag->lokasi }}</span>
                        </div>
                        <div class="d-flex align-items-center mb-1">
                            <i class="fas fa-user-tie me-2 text-primary" style="width: 14px;"></i>
                            <span class="text-truncate">Penyelenggara: <strong>{{ $ag->penyelenggara }}</strong></span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-users me-2 text-secondary" style="width: 14px;"></i>
                            <span class="text-truncate">Peserta: {{ $ag->target_peserta }}</span>
                        </div>
                    </div>

                    <!-- Description Preview -->
                    @if($ag->deskripsi)
                        <p class="small text-muted mb-0" style="font-size: 0.78rem; line-height: 1.45; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 34px;">
                            {{ $ag->deskripsi }}
                        </p>
                    @else
                        <p class="small text-muted mb-0 fst-italic" style="font-size: 0.78rem; min-height: 34px;">
                            Tidak ada deskripsi tambahan.
                        </p>
                    @endif
                </div>

                <!-- Card Footer (Auto Aligned) -->
                <div class="nx-grid-card-footer px-3.5 py-2.5 bg-light d-flex justify-content-between align-items-center" style="border-top: 1px solid var(--nx-border); border-radius: 0 0 14px 14px;">
                    <div>
                        @if($ag->is_published_notulen)
                            <span class="badge rounded-pill px-2.5 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-size: 0.7rem; font-weight: 700;">
                                <i class="fas fa-file-circle-check me-1"></i>Notulensi Terbit
                            </span>
                        @elseif($ag->hasil_keputusan || $ag->notulen)
                            <span class="badge rounded-pill px-2.5 py-1" style="background: var(--nx-warning-soft); color: var(--nx-warning); font-size: 0.7rem; font-weight: 700;">
                                <i class="fas fa-file-pen me-1"></i>Draft Notulensi
                            </span>
                        @else
                            <span class="text-muted" style="font-size: 0.72rem;">
                                <i class="fas fa-clock me-1"></i>Belum ada notulen
                            </span>
                        @endif
                    </div>
                    <div>
                        <a href="{{ route('agenda.show', $ag->id) }}" class="btn btn-sm btn-outline-primary px-2.5 py-1" style="border-radius: 8px; font-weight: 600; font-size: 0.78rem;">
                            Buka Detail &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card p-5 text-center text-muted" style="border-radius: 14px; border: 1px dashed var(--nx-border); background: #ffffff;">
                <div class="mb-3">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 72px; height: 72px; background: var(--nx-mint-soft); color: var(--nx-forest);">
                        <i class="fas fa-calendar-xmark fa-2x"></i>
                    </span>
                </div>
                <h5 class="fw-bold mb-1 text-dark">Belum Ada Agenda Rapat Terdaftar</h5>
                <p class="small text-muted mb-3" style="max-width: 480px; margin: 0 auto;">
                    Tidak ditemukan agenda musyawarah yang sesuai dengan filter atau kata kunci yang dipilih.
                </p>
                @if(!Auth::user()->isWarga())
                    <div>
                        <button type="button" class="btn btn-primary btn-sm px-3 py-2" data-bs-toggle="modal" data-bs-target="#modalTambahAgenda" style="border-radius: 10px; font-weight: 700;">
                            <i class="fas fa-plus me-1.5"></i> Buat Agenda Rapat Baru
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @endforelse
</div>

<!-- PAGINASI HALAMAN -->
@if($agendas->hasPages())
    <div class="mt-4 d-flex justify-content-center">
        {{ $agendas->links() }}
    </div>
@endif

<!-- MODAL TAMBAH AGENDA RAPAT (STANDAR FORMULIR TERSTRUKTUR & VALIDASI BENTROK) -->
@if(!Auth::user()->isWarga())
<div class="modal fade" id="modalTambahAgenda" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.12);">
            <form action="{{ route('agenda.store') }}" method="POST" enctype="multipart/form-data" id="formBuatAgenda">
                @csrf
                <div class="modal-header px-4 pt-4 pb-3 border-0">
                    <div>
                        <span class="badge rounded-pill mb-1.5 px-2.5 py-0.5" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700; font-size: 0.72rem;">
                            <i class="fas fa-calendar-plus me-1"></i>Musyawarah &amp; Koordinasi
                        </span>
                        <h5 class="modal-title fw-bold text-dark" style="font-size: 1.15rem;">
                            Rencanakan Agenda / Rapat Baru
                        </h5>
                        <p class="small text-muted mb-0">Agenda langsung dipublikasikan dan tersinkronisasi otomatis dengan Kalender Kegiatan.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body px-4 py-3">
                    <!-- Judul Rapat -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Judul Agenda / Rapat <span class="text-danger">*</span></label>
                        <input type="text" name="judul" class="form-control" required placeholder="Contoh: Rapat Koordinasi Penataan Lingkungan & Kesiapan Musim Hujan RW 018" style="border-radius: 10px; height: 42px;">
                    </div>

                    <!-- Jenis Rapat & Wilayah RT -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Jenis Rapat <span class="text-danger">*</span></label>
                            <select name="jenis_rapat" class="form-select" required style="border-radius: 10px; height: 42px;" id="inputJenisRapat">
                                @if(!Auth::user()->isAdminRT())
                                <option value="rw" {{ Auth::user()->isRw() ? 'selected' : '' }}>Rapat Internal RW</option>
                                @endif
                                <option value="koordinasi_rt" {{ Auth::user()->isAdminRT() ? 'selected' : '' }}>Koordinasi RT</option>
                                <option value="musyawarah_warga">Musyawarah Warga (Terbuka)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Wilayah RT Terkait</label>
                            <select name="rt_number" class="form-select" style="border-radius: 10px; height: 42px;">
                                <option value="">Tingkat RW (Meliputi Semua RT)</option>
                                @foreach($rtList as $rt)
                                    <option value="{{ $rt }}" {{ Auth::user()->rt_number == $rt ? 'selected' : '' }}>RT {{ $rt }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted" style="font-size: 0.72rem;">Kosongkan jika agenda merupakan rapat pleno tingkat RW.</small>
                        </div>
                    </div>

                    <!-- Tanggal, Waktu Mulai & Waktu Selesai -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Tanggal Pertemuan <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" id="agendaTanggal" class="form-control" required value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" style="border-radius: 10px; height: 42px;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Waktu Mulai <span class="text-danger">*</span></label>
                            <input type="time" name="waktu_mulai" id="agendaWaktuMulai" class="form-control" required value="19:30" style="border-radius: 10px; height: 42px;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Waktu Selesai <span class="text-danger">*</span></label>
                            <input type="time" name="waktu_selesai" id="agendaWaktuSelesai" class="form-control" required value="21:30" style="border-radius: 10px; height: 42px;">
                        </div>
                    </div>

                    <!-- Warning Collision Box (Dynamic) -->
                    <div id="collisionAlert" class="alert alert-warning p-2.5 mb-3 d-none border-0" style="border-radius: 10px; background: #FEF3C7; color: #92400E; font-size: 0.8rem;">
                        <i class="fas fa-triangle-exclamation me-1.5"></i><span id="collisionText"></span>
                    </div>

                    <!-- Lokasi & Penyelenggara -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Lokasi Pertemuan <span class="text-danger">*</span></label>
                            <input type="text" name="lokasi" id="agendaLokasi" class="form-control" required placeholder="Contoh: Balai Warga RW 018 / Ruang Posyandu RT 007" style="border-radius: 10px; height: 42px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Penyelenggara <span class="text-danger">*</span></label>
                            <input type="text" name="penyelenggara" class="form-control" required value="{{ Auth::user()->isRw() ? 'Pengurus RW 018' : 'Pengurus RT ' . Auth::user()->rt_number }}" style="border-radius: 10px; height: 42px;">
                        </div>
                    </div>

                    <!-- Sasaran / Target Peserta -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Sasaran / Undangan Peserta <span class="text-danger">*</span></label>
                        <select name="target_peserta" class="form-select" required style="border-radius: 10px; height: 42px;">
                            <option value="Seluruh Ketua RT 006 - 010 & Pengurus RW">Seluruh Ketua RT 006 - 010 &amp; Pengurus RW</option>
                            <option value="Pengurus RT & Petugas Ronda Lingkungan">Pengurus RT &amp; Petugas Ronda Lingkungan</option>
                            <option value="Warga RW 018 (Terbuka)">Warga RW 018 (Terbuka)</option>
                            <option value="Kader Posyandu & Tim Penggerak PKK">Kader Posyandu &amp; Tim Penggerak PKK</option>
                            <option value="Karang Taruna & Remaja Masjid">Karang Taruna &amp; Remaja Masjid</option>
                            <option value="Perwakilan Warga RT Tertentu">Perwakilan Warga RT Tertentu</option>
                        </select>
                        <small class="text-muted" style="font-size: 0.72rem;">Sistem otomatis menambahkan daftar hadir awal berdasarkan target peserta yang dipilih.</small>
                    </div>

                    <!-- Poin Pembahasan / Agenda -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Poin Pembahasan / Rangkaian Agenda <span class="text-danger">*</span></label>
                        <textarea name="agenda_pembahasan" rows="3" class="form-control" required placeholder="Tuliskan poin pembahasan utama...&#10;1. Evaluasi laporan kebersihan saluran&#10;2. Penentuan jadwal kerja bakti serentak&#10;3. Pengadaan sarana PJU" style="border-radius: 10px; line-height: 1.5; font-size: 0.85rem;"></textarea>
                    </div>

                    <!-- Deskripsi Tambahan -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Deskripsi Tambahan (Opsional)</label>
                        <textarea name="deskripsi" rows="2" class="form-control" placeholder="Tuliskan gambaran umum, latar belakang, atau hal yang perlu dipersiapkan peserta..." style="border-radius: 10px; font-size: 0.85rem;"></textarea>
                    </div>

                    <!-- Lampiran Undangan / TOR -->
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-dark">Lampiran Berkas Undangan / Dokumen TOR (Opsional)</label>
                        <input type="file" name="lampiran_dokumen" class="form-control" accept=".pdf,.doc,.docx,.jpg,.png" style="border-radius: 10px;">
                        <small class="text-muted" style="font-size: 0.72rem;">Format: PDF, Word DOCX, atau Gambar JPG/PNG. Maksimal 5MB.</small>
                    </div>
                </div>

                <div class="modal-footer px-4 pb-4 pt-2 border-0">
                    <button type="button" class="btn btn-secondary px-3 py-2" data-bs-dismiss="modal" style="border-radius: 10px; font-weight: 600;">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 py-2" style="border-radius: 10px; font-weight: 700;">
                        <i class="fas fa-calendar-check me-1.5"></i>Simpan &amp; Publikasikan Agenda
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tanggalInput = document.getElementById('agendaTanggal');
    const lokasiInput = document.getElementById('agendaLokasi');
    const waktuMulaiInput = document.getElementById('agendaWaktuMulai');
    const waktuSelesaiInput = document.getElementById('agendaWaktuSelesai');
    const collisionAlert = document.getElementById('collisionAlert');
    const collisionText = document.getElementById('collisionText');
    const form = document.getElementById('formBuatAgenda');

    function checkCollision() {
        const tanggal = tanggalInput.value;
        const lokasi = lokasiInput.value.trim();
        if (!tanggal || lokasi.length < 3) {
            collisionAlert.classList.add('d-none');
            return;
        }

        fetch(`{{ route('agenda.check_collision') }}?tanggal=${encodeURIComponent(tanggal)}&lokasi=${encodeURIComponent(lokasi)}`)
            .then(res => res.json())
            .then(data => {
                if (data.collision) {
                    collisionText.textContent = data.message;
                    collisionAlert.classList.remove('d-none');
                } else {
                    collisionAlert.classList.add('d-none');
                }
            })
            .catch(() => {
                collisionAlert.classList.add('d-none');
            });
    }

    if (tanggalInput && lokasiInput) {
        tanggalInput.addEventListener('change', checkCollision);
        lokasiInput.addEventListener('input', debounce(checkCollision, 500));
    }

    if (form) {
        form.addEventListener('submit', function(e) {
            const start = waktuMulaiInput.value;
            const end = waktuSelesaiInput.value;
            if (start && end && end <= start) {
                e.preventDefault();
                alert('Peringatan: Waktu selesai harus lebih akhir dari waktu mulai pertemuan.');
                waktuSelesaiInput.focus();
            }
        });
    }

    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }
});
</script>
@endif
@endsection
