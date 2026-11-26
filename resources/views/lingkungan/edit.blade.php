@extends('layouts.app')

@section('title', 'Edit & Revisi Laporan Lingkungan — NexaNest')

@section('content')
<div class="nx-page-header">
    <div>
        <div class="d-flex align-items-center gap-2 mb-2">
            <a href="{{ route('lingkungan.show', $laporan->id) }}" class="text-muted text-decoration-none small">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Detail Laporan
            </a>
            <span class="text-muted">/</span>
            <span class="badge rounded-pill px-2.5 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
                {{ $laporan->status === 'perlu_revisi' ? 'Revisi Laporan' : 'Edit Draft' }}
            </span>
        </div>
        <h1 class="nx-page-title">
            {{ $laporan->status === 'perlu_revisi' ? 'Revisi Laporan Lingkungan RT ' . $laporan->rt_number : 'Edit Draft Laporan RT ' . $laporan->rt_number }}
        </h1>
        <p class="nx-page-subtitle">
            Periode {{ $laporan->nama_bulan }} {{ $laporan->periode_tahun }} · Versi Saat Ini: v{{ $laporan->versi }}
        </p>
    </div>
</div>

@if($laporan->status === 'perlu_revisi')
    <div class="card p-3.5 mb-4 shadow-xs" style="background: #FFF5F5; border-left: 4px solid #DC3545; border-radius: 12px;">
        <div class="d-flex gap-3">
            <span class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px; background: #FCE8E8; color: #DC3545;">
                <i class="fas fa-comment-dots fa-lg"></i>
            </span>
            <div>
                <div class="d-flex align-items-center gap-2">
                    <h6 class="fw-bold mb-0 text-danger">Catatan Evaluasi / Permintaan Revisi dari Ketua RW:</h6>
                    <span class="badge bg-danger rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">Revisi Dibutuhkan</span>
                </div>
                <p class="text-dark small mb-1 mt-1 font-italic" style="font-size: 0.82rem; line-height: 1.4;">
                    "{{ $laporan->catatan_evaluasi_rw ?: $laporan->catatan_rw }}"
                </p>
                <small class="text-muted" style="font-size: 0.72rem;">
                    Ditinjau oleh: <strong>{{ $laporan->pemeriksa ? $laporan->pemeriksa->name : 'Ketua RW 018' }}</strong> pada {{ $laporan->tanggal_pemeriksaan ? $laporan->tanggal_pemeriksaan->translatedFormat('d F Y, H:i') : '-' }}
                </small>
            </div>
        </div>
    </div>
@endif

<form action="{{ route('lingkungan.update', $laporan->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row g-4">
        <!-- FORM UTAMA -->
        <div class="col-lg-8">
            <!-- CATATAN KHUSUS REVISI OLEH RT (JIKA PERLU REVISI) -->
            @if($laporan->status === 'perlu_revisi')
                <div class="card p-3.5 mb-4 shadow-xs border-danger" style="border-radius: 12px; background: #FFFFFF;">
                    <label class="form-label small fw-bold text-danger mb-1">
                        <i class="fas fa-pen-nib me-1"></i>Penjelasan Perbaikan dari Admin RT (Wajib Diisi untuk Resubmit):
                    </label>
                    <textarea name="catatan_revisi_rt" rows="2" class="form-control form-control-sm" placeholder="Jelaskan ringkasan apa saja yang telah diperbaiki / ditambahkan pada revisi kali ini..." required>{{ $laporan->catatan_revisi_rt }}</textarea>
                    <small class="text-muted" style="font-size: 0.72rem;">Catatan ini akan dikirimkan kepada Ketua RW bersama versi revisi v{{ $laporan->versi + 1 }}.</small>
                </div>
            @endif

            <!-- 1. INFORMASI LAPORAN -->
            <div class="card p-4 mb-4 shadow-xs" style="border-radius: 12px;">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <span class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: var(--nx-mint); color: var(--nx-forest); font-weight: 700;">1</span>
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 1rem;">Informasi &amp; Identitas Laporan</h5>
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Wilayah RT</label>
                        <input type="text" class="form-control bg-light" value="RT {{ $laporan->rt_number }}" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Bulan Periode</label>
                        <input type="text" class="form-control bg-light" value="{{ $laporan->nama_bulan }}" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Tahun</label>
                        <input type="text" class="form-control bg-light" value="{{ $laporan->periode_tahun }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Pengirim Laporan</label>
                        <input type="text" class="form-control bg-light" value="{{ $laporan->user ? $laporan->user->name : Auth::user()->name }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Versi Laporan</label>
                        <input type="text" class="form-control bg-light" value="Versi {{ $laporan->versi }} {{ $laporan->status === 'perlu_revisi' ? '-> Menjadi v' . ($laporan->versi + 1) : '' }}" readonly>
                    </div>
                </div>
            </div>

            <!-- 2. KONDISI KEBERSIHAN LINGKUNGAN -->
            <div class="card p-4 mb-4 shadow-xs" style="border-radius: 12px;">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <span class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: var(--nx-mint); color: var(--nx-forest); font-weight: 700;">2</span>
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 1rem;">Kondisi Kebersihan Lingkungan</h5>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Kondisi Umum Kebersihan Wilayah <span class="text-danger">*</span></label>
                    <select name="kondisi_kebersihan" class="form-select" required>
                        <option value="Sangat Bersih & Tertata" {{ $laporan->kondisi_kebersihan === 'Sangat Bersih & Tertata' ? 'selected' : '' }}>Sangat Bersih &amp; Tertata</option>
                        <option value="Cukup Bersih & Terawat" {{ $laporan->kondisi_kebersihan === 'Cukup Bersih & Terawat' ? 'selected' : '' }}>Cukup Bersih &amp; Terawat</option>
                        <option value="Kurang Bersih / Perlu Penanganan" {{ $laporan->kondisi_kebersihan === 'Kurang Bersih / Perlu Penanganan' ? 'selected' : '' }}>Kurang Bersih / Perlu Penanganan</option>
                    </select>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Area yang Sudah Terjaga Kebersihannya</label>
                        <textarea name="area_terjaga_kebersihan" rows="2" class="form-control">{{ $laporan->area_terjaga_kebersihan }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Area yang Masih Membutuhkan Perhatian</label>
                        <textarea name="area_perlu_perhatian_kebersihan" rows="2" class="form-control">{{ $laporan->area_perlu_perhatian_kebersihan }}</textarea>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Permasalahan Sampah atau Kebersihan</label>
                        <textarea name="masalah_sampah" rows="2" class="form-control">{{ $laporan->masalah_sampah }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Keterangan Tambahan Kebersihan</label>
                        <textarea name="keterangan_kebersihan" rows="2" class="form-control">{{ $laporan->keterangan_kebersihan }}</textarea>
                    </div>
                </div>
            </div>

            <!-- 3. KONDISI DRAINASE DAN SALURAN AIR -->
            <div class="card p-4 mb-4 shadow-xs" style="border-radius: 12px;">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <span class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: var(--nx-powder); color: #1e4b6d; font-weight: 700;">3</span>
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 1rem;">Kondisi Drainase &amp; Saluran Air</h5>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Kondisi Fisik Saluran Drainase <span class="text-danger">*</span></label>
                        <select name="kondisi_drainase" class="form-select" required>
                            <option value="Lancar Tanpa Hambatan" {{ $laporan->kondisi_drainase === 'Lancar Tanpa Hambatan' ? 'selected' : '' }}>Lancar Tanpa Hambatan</option>
                            <option value="Sedimen Cukup Tebal" {{ $laporan->kondisi_drainase === 'Sedimen Cukup Tebal' ? 'selected' : '' }}>Sedimen Cukup Tebal</option>
                            <option value="Tersumbat / Berpotensi Genangan" {{ $laporan->kondisi_drainase === 'Tersumbat / Berpotensi Genangan' ? 'selected' : '' }}>Tersumbat / Berpotensi Genangan</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Kondisi Aliran Air</label>
                        <select name="kondisi_aliran_air" class="form-select">
                            <option value="Lancar Mengalir" {{ $laporan->kondisi_aliran_air === 'Lancar Mengalir' ? 'selected' : '' }}>Lancar Mengalir</option>
                            <option value="Lambat / Genangan Sedang" {{ $laporan->kondisi_aliran_air === 'Lambat / Genangan Sedang' ? 'selected' : '' }}>Lambat / Genangan Sedang</option>
                            <option value="Tersumbat Total" {{ $laporan->kondisi_aliran_air === 'Tersumbat Total' ? 'selected' : '' }}>Tersumbat Total</option>
                        </select>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Area / Titik Rawan Genangan</label>
                        <textarea name="titik_rawan_genangan" rows="2" class="form-control">{{ $laporan->titik_rawan_genangan }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Saluran yang Membutuhkan Pengerukan</label>
                        <textarea name="saluran_butuh_pembersihan" rows="2" class="form-control">{{ $laporan->saluran_butuh_pembersihan }}</textarea>
                    </div>
                </div>
            </div>

            <!-- 4. PENGHIJAUAN DAN RUANG TERBUKA -->
            <div class="card p-4 mb-4 shadow-xs" style="border-radius: 12px;">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <span class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #FAF8EA; color: #8C6A05; font-weight: 700;">4</span>
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 1rem;">Penghijauan &amp; Ruang Terbuka</h5>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Kondisi Tanaman &amp; Penghijauan</label>
                        <select name="kondisi_penghijauan" class="form-select">
                            <option value="Subur & Asri Rindang" {{ $laporan->kondisi_penghijauan === 'Subur & Asri Rindang' ? 'selected' : '' }}>Subur &amp; Asri Rindang</option>
                            <option value="Cukup Terawat" {{ $laporan->kondisi_penghijauan === 'Cukup Terawat' ? 'selected' : '' }}>Cukup Terawat</option>
                            <option value="Kering / Butuh Peremajaan" {{ $laporan->kondisi_penghijauan === 'Kering / Butuh Peremajaan' ? 'selected' : '' }}>Kering / Butuh Peremajaan</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Ketersediaan &amp; Kondisi Ruang Terbuka</label>
                        <input type="text" name="ketersediaan_ruang_terbuka" class="form-control" value="{{ $laporan->ketersediaan_ruang_terbuka }}">
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Kegiatan Perawatan Tanaman</label>
                        <textarea name="kegiatan_perawatan_tanaman" rows="2" class="form-control">{{ $laporan->kegiatan_perawatan_tanaman }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Kendala Penghijauan</label>
                        <textarea name="kendala_penghijauan" rows="2" class="form-control">{{ $laporan->kendala_penghijauan }}</textarea>
                    </div>
                </div>
            </div>

            <!-- 5. PROGRAM DAN KEGIATAN LINGKUNGAN -->
            <div class="card p-4 mb-4 shadow-xs" style="border-radius: 12px;">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <span class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: var(--nx-mint-soft); color: var(--nx-forest); font-weight: 700;">5</span>
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 1rem;">Program &amp; Kegiatan Gotong Royong</h5>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Kegiatan Kerja Bakti yang Terlaksana</label>
                    <textarea name="kegiatan_kerja_bakti" rows="2" class="form-control">{{ $laporan->kegiatan_kerja_bakti }}</textarea>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Tanggal Pelaksanaan</label>
                        <input type="date" name="tanggal_pelaksanaan_kegiatan" class="form-control" value="{{ $laporan->tanggal_pelaksanaan_kegiatan ? $laporan->tanggal_pelaksanaan_kegiatan->format('Y-m-d') : date('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Jumlah Peserta Warga</label>
                        <input type="number" name="jumlah_peserta_kegiatan" class="form-control" value="{{ $laporan->jumlah_peserta_kegiatan }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Tautkan Agenda Rapat</label>
                        <select name="agenda_id" class="form-select">
                            <option value="">-- Pilih Jika Terkait --</option>
                            @foreach($agendaTerkait as $ag)
                                <option value="{{ $ag->id }}" {{ $laporan->agenda_id == $ag->id ? 'selected' : '' }}>{{ $ag->judul }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Program Kebersihan &amp; Pilah Sampah</label>
                        <textarea name="program_kebersihan" rows="2" class="form-control">{{ $laporan->program_kebersihan }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Program Lingkungan Lainnya</label>
                        <textarea name="program_lingkungan_lainnya" rows="2" class="form-control">{{ $laporan->program_lingkungan_lainnya }}</textarea>
                    </div>
                </div>
            </div>

            <!-- 6. KENDALA DAN RENCANA PERBAIKAN -->
            <div class="card p-4 mb-4 shadow-xs" style="border-radius: 12px;">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <span class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #FFF5F5; color: #DC3545; font-weight: 700;">6</span>
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 1rem;">Kendala &amp; Rencana Tindak Lanjut</h5>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Kendala Lingkungan yang Ditemukan</label>
                        <textarea name="kendala_lingkungan" rows="2" class="form-control">{{ $laporan->kendala_lingkungan }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Lokasi yang Butuh Perhatian Khusus</label>
                        <textarea name="lokasi_perhatian_khusus" rows="2" class="form-control">{{ $laporan->lokasi_perhatian_khusus }}</textarea>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Rencana Tindak Lanjut Pengurus RT</label>
                        <textarea name="rencana_tindak_lanjut" rows="2" class="form-control">{{ $laporan->rencana_tindak_lanjut }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Bantuan / Koordinasi yang Dibutuhkan dari RW</label>
                        <textarea name="bantuan_rw_dibutuhkan" rows="2" class="form-control">{{ $laporan->bantuan_rw_dibutuhkan }}</textarea>
                    </div>
                </div>
            </div>

            <!-- 7. DOKUMENTASI FOTO -->
            <div class="card p-4 mb-4 shadow-xs" style="border-radius: 12px;">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <span class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #F0F4F8; color: #1e4b6d; font-weight: 700;">7</span>
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 1rem;">Dokumentasi Foto Lapangan</h5>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Foto Utama Lingkungan</label>
                        @if($laporan->foto_dokumentasi)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $laporan->foto_dokumentasi) }}" class="img-thumbnail" style="max-height: 80px;" alt="Foto Utama">
                            </div>
                        @endif
                        <input type="file" name="foto_dokumentasi" class="form-control" accept="image/*">
                        <small class="text-muted" style="font-size: 0.7rem;">Pilih untuk mengganti foto.</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Foto Sebelum Kegiatan</label>
                        @if($laporan->foto_sebelum)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $laporan->foto_sebelum) }}" class="img-thumbnail" style="max-height: 80px;" alt="Foto Sebelum">
                            </div>
                        @endif
                        <input type="file" name="foto_sebelum" class="form-control" accept="image/*">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Foto Sesudah Kegiatan</label>
                        @if($laporan->foto_sesudah)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $laporan->foto_sesudah) }}" class="img-thumbnail" style="max-height: 80px;" alt="Foto Sesudah">
                            </div>
                        @endif
                        <input type="file" name="foto_sesudah" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold">Keterangan / Deskripsi Foto</label>
                    <input type="text" name="keterangan_foto" class="form-control" value="{{ $laporan->keterangan_foto }}">
                </div>
            </div>

            <!-- TOMBOL AKSI -->
            <div class="d-flex justify-content-between align-items-center p-3 rounded-3 bg-white border shadow-xs">
                <a href="{{ route('lingkungan.show', $laporan->id) }}" class="btn btn-outline-secondary">Batal</a>
                <div class="d-flex gap-2">
                    @if($laporan->status === 'draft')
                        <button type="submit" name="action_type" value="draft" class="btn btn-outline-secondary px-3 fw-bold">
                            <i class="fas fa-floppy-disk me-1"></i> Simpan Perubahan Draft
                        </button>
                    @endif
                    <button type="submit" name="action_type" value="submit" class="btn btn-primary px-4 fw-bold shadow-sm" style="background: var(--nx-forest); border-color: var(--nx-forest);">
                        <i class="fas fa-paper-plane me-1"></i> {{ $laporan->status === 'perlu_revisi' ? 'Kirimkan Revisi ke RW' : 'Serahkan Laporan ke RW' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- SIDEBAR PENDUKUNG -->
        <div class="col-lg-4">
            <!-- RIWAYAT REVISI & CATATAN RW -->
            <div class="card p-3.5 mb-3 shadow-xs" style="border-radius: 12px;">
                <h6 class="fw-bold mb-2 text-dark" style="font-size: 0.9rem;">
                    <i class="fas fa-clock-rotate-left text-success me-1.5"></i>Riwayat Status Laporan
                </h6>
                <div class="small">
                    @foreach($laporan->histories as $h)
                        <div class="mb-2 pb-2 border-bottom">
                            <div class="d-flex justify-content-between align-items-center mb-0.5">
                                <strong class="text-dark" style="font-size: 0.78rem;">{{ ucfirst(str_replace('_', ' ', $h->aksi)) }}</strong>
                                <small class="text-muted" style="font-size: 0.68rem;">{{ $h->created_at->format('d/m/Y H:i') }}</small>
                            </div>
                            <small class="text-muted d-block" style="font-size: 0.72rem;">Oleh: {{ $h->user ? $h->user->name : '-' }} (v{{ $h->versi }})</small>
                            @if($h->catatan)
                                <p class="mb-0 text-muted mt-1 p-1.5 bg-light rounded" style="font-size: 0.7rem;">"{{ $h->catatan }}"</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- REFERENSI BANK SAMPAH -->
            <div class="card p-3.5 mb-3 shadow-xs" style="background: linear-gradient(135deg, #F5FBF6 0%, #FFFFFF 100%); border-radius: 12px;">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: var(--nx-mint); color: var(--nx-forest);">
                        <i class="fas fa-recycle"></i>
                    </span>
                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.9rem;">Bank Sampah RT {{ $laporan->rt_number }}</h6>
                </div>
                <div class="p-2.5 rounded-2 bg-white border text-center mb-2">
                    <span class="text-muted small d-block" style="font-size: 0.7rem;">Total Sampah Terpilah:</span>
                    <h4 class="fw-bold mb-0" style="color: var(--nx-forest);">{{ number_format($bankSampahKg, 1) }} <span class="fs-6 text-muted fw-normal">Kg</span></h4>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
