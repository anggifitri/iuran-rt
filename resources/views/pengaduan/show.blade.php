@extends('layouts.app')

@section('title', 'Laporan ' . ($pengaduan->nomor_pengaduan ?: 'PGD-'.$pengaduan->id) . ' — NexaNest')

@section('content')
@php
    $user = Auth::user();
    $isWarga = $user->isWarga();
    $isAdminRw = $user->isAdminRw();
    $isAdminRt = $user->isAdminRt();
    $sBadge = $pengaduan->status_badge;
    $pBadge = $pengaduan->priority_badge;
    $overdue = $pengaduan->is_overdue;
@endphp

<!-- HEADER DETAIL PENGADUAN -->
<div class="nx-page-header mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
            <a href="{{ route('pengaduan.index', ['tab' => 'pengaduan']) }}" class="btn btn-sm btn-outline-secondary py-1 px-2.5">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Pengaduan
            </a>
            <span class="badge rounded-pill font-monospace px-2.5 py-1" style="background: var(--nx-bg); color: var(--nx-forest); border: 1px solid var(--nx-border); font-size: 0.82rem;">
                <i class="fas fa-hashtag me-1"></i>{{ $pengaduan->nomor_pengaduan ?: 'PGD-' . $pengaduan->id }}
            </span>
            <span class="badge rounded-pill px-3 py-1 fw-bold" style="background: {{ $sBadge['bg'] }}; color: {{ $sBadge['color'] }}; font-size: 0.82rem;">
                <i class="fas {{ $sBadge['icon'] }} me-1"></i> {{ strtoupper($pengaduan->status_label) }}
            </span>
            <span class="badge rounded-pill px-3 py-1" style="background: {{ $pBadge['bg'] }}; color: {{ $pBadge['color'] }}; font-size: 0.82rem; font-weight: 700;">
                <i class="fas fa-flag me-1"></i>Prioritas: {{ $pBadge['label'] }}
            </span>
            @if($overdue)
                <span class="badge rounded-pill px-2.5 py-1 bg-danger text-white">
                    <i class="fas fa-triangle-exclamation me-1"></i> Terlambat / Overdue
                </span>
            @endif
        </div>
        <h1 class="nx-page-title">{{ $pengaduan->title }}</h1>
        <p class="nx-page-subtitle">Kategori: {{ $pengaduan->category }} • Dilaporkan pada {{ $pengaduan->created_at ? $pengaduan->created_at->translatedFormat('d F Y · H:i') : '-' }} WIB</p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        @if($pengaduan->google_maps_url)
            <a href="{{ $pengaduan->google_maps_url }}" target="_blank" class="btn btn-success">
                <i class="fas fa-map-location-dot me-1"></i> Buka Titik di Google Maps
            </a>
        @endif
        @if(!$isWarga && $pengaduan->status !== 'selesai' && $pengaduan->status !== 'ditolak')
            <a href="#cardTindakLanjutAdmin" class="btn btn-primary">
                <i class="fas fa-pen-to-square me-1"></i> Tindak Lanjuti Laporan
            </a>
        @endif
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert" style="background: var(--nx-mint-soft); border-color: var(--nx-sage); color: var(--nx-forest);">
        <i class="fas fa-check-circle me-2 fs-5"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row g-4 mb-4">
    <!-- KOLOM KIRI: RINCIAN PENGADUAN, LOKASI & ASET, TIMELINE STATUS -->
    <div class="col-lg-8">
        <!-- 1. KARTU ISI PENGADUAN LENGKAP -->
        <div class="card p-4 border-0 shadow-sm mb-4">
            <h5 class="fw-bold mb-3" style="font-size: 1.05rem; color: var(--nx-text);">
                <i class="fas fa-file-lines text-primary me-2"></i>Uraian Laporan Masalah
            </h5>
            <p class="text-muted" style="line-height: 1.8; font-size: 0.95rem; white-space: pre-line;">{{ $pengaduan->content }}</p>

            <!-- Foto Lampiran Warga jika ada -->
            @if($pengaduan->foto_lampiran)
                <div class="mt-3 pt-3 border-top">
                    <span class="small fw-bold text-muted d-block mb-2"><i class="fas fa-image me-1"></i>Foto Bukti Lampiran Warga:</span>
                    <div class="rounded-3 overflow-hidden border d-inline-block" style="max-width: 320px;">
                        <a href="{{ asset('storage/' . $pengaduan->foto_lampiran) }}" target="_blank">
                            <img src="{{ asset('storage/' . $pengaduan->foto_lampiran) }}" alt="Lampiran" class="img-fluid">
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- 2. KARTU ASET LINGKUNGAN TERHUBUNG & LOKASI GOOGLE MAPS -->
        <div class="card p-4 border-0 shadow-sm mb-4">
            <h5 class="fw-bold mb-3" style="font-size: 1.05rem; color: var(--nx-text);">
                <i class="fas fa-map-location-dot text-success me-2"></i>Lokasi Kejadian &amp; Aset Fisik Terkait
            </h5>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="p-3 rounded-3 h-100" style="background: var(--nx-bg); border: 1px solid var(--nx-border);">
                        <span class="text-muted small fw-bold text-uppercase d-block mb-1">Aset Lingkungan</span>
                        @if($pengaduan->aset)
                            <strong class="d-block text-dark">{{ $pengaduan->aset->nama }}</strong>
                            <span class="badge rounded-pill font-monospace px-2 py-0.5 mt-1" style="background: var(--nx-mint); color: var(--nx-forest); border: 1px solid var(--nx-border);">
                                <i class="fas fa-qrcode me-1"></i>{{ $pengaduan->aset->nomor_aset }}
                            </span>
                            <div class="mt-2">
                                <a href="{{ route('aset.show', $pengaduan->aset->id) }}" class="btn btn-xs btn-outline-primary py-1 px-2.5">
                                    <i class="fas fa-eye me-1"></i> Lihat Data Aset &amp; Riwayat
                                </a>
                            </div>
                        @elseif($pengaduan->barcode_code)
                            <strong class="d-block text-dark">Barcode QR: {{ $pengaduan->barcode_code }}</strong>
                            <small class="text-muted">Aset terdaftar via kode QR fisik</small>
                        @else
                            <span class="text-muted small">Laporan fasilitas umum bebas (tidak terikat aset spesifik).</span>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="p-3 rounded-3 h-100" style="background: var(--nx-bg); border: 1px solid var(--nx-border);">
                        <span class="text-muted small fw-bold text-uppercase d-block mb-1">Titik Lokasi &amp; Wilayah</span>
                        <strong class="d-block text-dark mb-1"><i class="fas fa-map-pin text-danger me-1"></i>{{ $pengaduan->location_details }}</strong>
                        <span class="badge rounded-pill px-2 py-0.5 mb-2" style="background: var(--nx-powder); color: #1F517F;">
                            Wilayah RT {{ $pengaduan->rt_number }} / RW 018
                        </span>

                        @if($pengaduan->google_maps_url)
                            <div>
                                <a href="{{ $pengaduan->google_maps_url }}" target="_blank" class="btn btn-xs btn-success py-1 px-2.5 fw-bold">
                                    <i class="fas fa-directions me-1"></i> Buka di Google Maps
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. CATATAN TINDAK LANJUT & FEEDBACK WARGA -->
        <div class="card p-4 border-0 shadow-sm mb-4">
            <h5 class="fw-bold mb-3" style="font-size: 1.05rem; color: var(--nx-text);">
                <i class="fas fa-comments text-primary me-2"></i>Catatan Perkembangan &amp; Tindak Lanjut
            </h5>

            <!-- Bagian yang dapat dilihat Warga -->
            <div class="p-3 rounded-3 mb-3" style="background: var(--nx-mint-soft); border-left: 4px solid var(--nx-forest);">
                <div class="d-flex align-items-center gap-1.5 mb-1 text-success fw-bold small">
                    <i class="fas fa-bullhorn"></i> Catatan Tindak Lanjut untuk Warga (Transparan)
                </div>
                <p class="mb-0 text-dark small" style="line-height: 1.6;">
                    {{ $pengaduan->feedback_warga ?: 'Laporan telah dicatat ke sistem. Petugas sedang menjadwalkan pemeriksaan lapangan.' }}
                </p>
            </div>

            <!-- Bagian Catatan Internal Khusus Admin RW & RT (Warga TIDAK BISA MELIHAT) -->
            @if(!$isWarga)
                <div class="p-3 rounded-3" style="background: #FFFBEB; border-left: 4px solid #F59E0B;">
                    <div class="d-flex align-items-center gap-1.5 mb-1 text-warning fw-bold small">
                        <i class="fas fa-lock"></i> Catatan Internal Pengurus RW / RT (Hanya Terlihat oleh Admin)
                    </div>
                    <p class="mb-0 text-dark small" style="line-height: 1.6;">
                        {{ $pengaduan->internal_notes ?: 'Belum ada catatan internal teknis dari pengurus.' }}
                    </p>
                </div>
            @endif
        </div>

        <!-- 4. TIMELINE RIWAYAT PERUBAHAN STATUS -->
        <div class="card p-4 border-0 shadow-sm mb-4">
            <h5 class="fw-bold mb-3" style="font-size: 1.05rem; color: var(--nx-text);">
                <i class="fas fa-timeline text-primary me-2"></i>Alur &amp; Riwayat Perubahan Status
            </h5>

            @if($pengaduan->histories->isEmpty())
                <div class="text-center py-3 text-muted small">Belum ada riwayat aktivitas tercatat.</div>
            @else
                <div class="position-relative ps-3">
                    @foreach($pengaduan->histories as $h)
                        <div class="position-relative pb-3 mb-2 border-start ps-3" style="border-color: var(--nx-sage) !important;">
                            <span class="position-absolute rounded-circle"
                                  style="width: 10px; height: 10px; background: var(--nx-forest); left: -5px; top: 5px;"></span>
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-1">
                                <span class="badge rounded-pill px-2 py-0.5 text-uppercase fw-bold" style="background: var(--nx-mint); color: var(--nx-forest); font-size: 0.72rem;">
                                    {{ $h->status_ke }}
                                </span>
                                <small class="text-muted">{{ $h->created_at ? $h->created_at->translatedFormat('d M Y · H:i') : '-' }} WIB</small>
                            </div>
                            <p class="mb-0 small text-dark mt-1" style="line-height: 1.5;">{{ $h->catatan }}</p>
                            <small class="text-muted" style="font-size: 0.7rem;">Oleh: <strong>{{ $h->user->name ?? 'Sistem' }}</strong></small>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- KOLOM KANAN: IDENTITAS PELAPOR & FORM TINDAK LANJUT ADMIN -->
    <div class="col-lg-4">
        <!-- 1. IDENTITAS WARGA PELAPOR -->
        <div class="card p-4 border-0 shadow-sm mb-4">
            <h6 class="fw-bold mb-3" style="font-size: 0.95rem; color: var(--nx-text);">
                <i class="fas fa-user-circle text-primary me-2"></i>Identitas Warga Pelapor
            </h6>

            <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                     style="width: 48px; height: 48px; background: var(--nx-mint); color: var(--nx-forest); font-size: 1.1rem;">
                    {{ strtoupper(substr($pengaduan->user->name ?? 'W', 0, 2)) }}
                </div>
                <div>
                    <h6 class="fw-bold mb-0" style="color: var(--nx-text);">{{ $pengaduan->user->name ?? 'Warga' }}</h6>
                    <span class="badge rounded-pill px-2 py-0.5" style="background: var(--nx-powder); color: #1F517F; font-size: 0.72rem;">
                        Warga RT {{ $pengaduan->rt_number ?? ($pengaduan->user->rt_number ?? '000') }}
                    </span>
                    <small class="text-muted d-block mt-0.5" style="font-size: 0.72rem;">Email: {{ $pengaduan->user->email ?? '-' }}</small>
                </div>
            </div>

            <div class="small text-muted">
                <div class="d-flex justify-content-between mb-1.5">
                    <span>Waktu Pelaporan:</span>
                    <strong class="text-dark">{{ $pengaduan->created_at ? $pengaduan->created_at->translatedFormat('d M Y H:i') : '-' }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-1.5">
                    <span>Diverifikasi Oleh:</span>
                    <strong class="text-dark">{{ $pengaduan->verifier->name ?? '-' }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Petugas Bertugas:</span>
                    <strong class="text-primary">{{ $pengaduan->petugas_nama ?: '-' }}</strong>
                </div>
            </div>
        </div>

        <!-- 2. FORM VERIFIKASI & TINDAK LANJUT ADMIN RW / ADMIN RT -->
        @if(!$isWarga)
            <div class="card p-4 border-0 shadow-sm mb-4" id="cardTindakLanjutAdmin" style="background: linear-gradient(180deg, #FFFFFF 0%, var(--nx-bg) 100%); border: 1.5px solid var(--nx-forest) !important;">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <span class="rounded-circle d-flex align-items-center justify-content-center"
                          style="width: 36px; height: 36px; background: var(--nx-mint); color: var(--nx-forest);">
                        <i class="fas fa-user-gear"></i>
                    </span>
                    <div>
                        <h6 class="fw-bold mb-0" style="color: var(--nx-forest);">Panel Tindak Lanjut Pengurus</h6>
                        <small class="text-muted">Kelola status, prioritas, &amp; teknisi penangan</small>
                    </div>
                </div>

                <form method="POST" action="{{ route('pengaduan.update_status', $pengaduan->id) }}">
                    @csrf
                    @method('PUT')

                    <!-- Perubahan Status -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Perbarui Status Penanganan <span class="text-danger">*</span></label>
                        <select name="status" class="form-select form-select-sm" required>
                            <option value="menunggu" {{ $pengaduan->status === 'menunggu' ? 'selected' : '' }}>1. Menunggu Verifikasi</option>
                            <option value="diverifikasi" {{ $pengaduan->status === 'diverifikasi' ? 'selected' : '' }}>2. Diverifikasi (Disetujui)</option>
                            <option value="ditugaskan" {{ $pengaduan->status === 'ditugaskan' ? 'selected' : '' }}>3. Ditugaskan ke Petugas</option>
                            <option value="proses" {{ in_array($pengaduan->status, ['proses', 'sedang_diproses']) ? 'selected' : '' }}>4. Sedang Diproses Lapangan</option>
                            <option value="selesai" {{ $pengaduan->status === 'selesai' ? 'selected' : '' }}>5. Selesai Ditangani</option>
                            <option value="ditolak" {{ $pengaduan->status === 'ditolak' ? 'selected' : '' }}>6. Ditolak / Tidak Valid</option>
                        </select>
                    </div>

                    <!-- Prioritas Laporan -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Tentukan Tingkat Prioritas <span class="text-danger">*</span></label>
                        <select name="priority" class="form-select form-select-sm" required>
                            <option value="rendah" {{ $pengaduan->priority === 'rendah' ? 'selected' : '' }}>Rendah (Non-Mendesak)</option>
                            <option value="sedang" {{ $pengaduan->priority === 'sedang' ? 'selected' : '' }}>Sedang (Standar)</option>
                            <option value="tinggi" {{ $pengaduan->priority === 'tinggi' ? 'selected' : '' }}>Tinggi (Segera)</option>
                            <option value="darurat" {{ $pengaduan->priority === 'darurat' ? 'selected' : '' }}>Darurat / Emergency (Segera 24 Jam)</option>
                        </select>
                    </div>

                    <!-- Nama Petugas yang Ditugaskan -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Petugas / Tim Teknis Penanggung Jawab</label>
                        <input type="text" name="petugas_nama" class="form-control form-control-sm"
                               value="{{ $pengaduan->petugas_nama }}" placeholder="Contoh: Budi Santoso (Teknisi Listrik RW)">
                    </div>

                    <!-- Jika terhubung aset: Opsi update kondisi aset -->
                    @if($pengaduan->aset)
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Pembaruan Kondisi Aset Fisik</label>
                            <select name="kondisi_aset_setelah" class="form-select form-select-sm">
                                <option value="baik" {{ $pengaduan->aset->kondisi === 'baik' ? 'selected' : '' }}>Kondisi Baik (Normal)</option>
                                <option value="rusak_ringan" {{ $pengaduan->aset->kondisi === 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                <option value="rusak_berat" {{ $pengaduan->aset->kondisi === 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                                <option value="dalam_perbaikan" {{ $pengaduan->aset->kondisi === 'dalam_perbaikan' ? 'selected' : '' }}>Dalam Perbaikan</option>
                            </select>
                        </div>
                    @endif

                    <!-- Catatan Tindak Lanjut untuk Warga (Public) -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Catatan Tindak Lanjut (Dapat Dilihat Warga)</label>
                        <textarea name="feedback_warga" class="form-control form-control-sm" rows="2.5" placeholder="Pesan tanggapan resmi yang akan tampil di aplikasi warga...">{{ $pengaduan->feedback_warga }}</textarea>
                    </div>

                    <!-- Catatan Internal Admin (Private) -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-danger"><i class="fas fa-lock me-1"></i>Catatan Internal Pengurus (Rahasia)</label>
                        <textarea name="internal_notes" class="form-control form-control-sm" rows="2.5" placeholder="Catatan teknis, biaya part, atau koordinasi antar-pengurus yang tidak boleh dibaca warga...">{{ $pengaduan->internal_notes }}</textarea>
                        <small class="text-muted" style="font-size: 0.7rem;">Hanya dapat dibaca oleh Admin RW &amp; RT.</small>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                        <i class="fas fa-save me-1"></i> Perbarui Status &amp; Catatan
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
