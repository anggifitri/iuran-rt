@extends('layouts.app')

@section('title', 'Detail Aset ' . $aset->nomor_aset . ' — NexaNest')

@section('content')
@php
    $user = Auth::user();
    $isWarga = $user->isWarga();
    $badge = $aset->kondisi_badge;
@endphp

<!-- HEADER DETAIL ASET -->
<div class="nx-page-header mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-2">
            <a href="{{ route('pengaduan.index', ['tab' => 'aset']) }}" class="btn btn-sm btn-outline-secondary py-1 px-2.5">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Database Aset
            </a>
            <span class="badge rounded-pill px-2.5 py-1 font-monospace" style="background: var(--nx-bg); color: var(--nx-forest); border: 1px solid var(--nx-border); font-size: 0.8rem;">
                <i class="fas fa-qrcode me-1"></i>{{ $aset->nomor_aset }}
            </span>
            <span class="badge rounded-pill px-2.5 py-1" style="background: {{ $badge['bg'] }}; color: {{ $badge['text'] }}; font-size: 0.8rem;">
                {{ $badge['label'] }}
            </span>
        </div>
        <h1 class="nx-page-title">{{ $aset->nama }}</h1>
        <p class="nx-page-subtitle">{{ $aset->kategori }} • Wilayah RT {{ $aset->rt_number }} / RW {{ $aset->rw_number }}</p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        @if($aset->google_maps_url)
            <a href="{{ $aset->google_maps_url }}" target="_blank" class="btn btn-success">
                <i class="fas fa-map-location-dot me-1"></i> Buka di Google Maps
            </a>
        @endif
        <a href="{{ route('pengaduan.create', ['aset_id' => $aset->id, 'aset_code' => $aset->nomor_aset]) }}" class="btn btn-outline-warning text-dark">
            <i class="fas fa-triangle-exclamation me-1"></i> Laporkan Kerusakan
        </a>
        @if(!$isWarga)
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCatatPemeriksaan">
                <i class="fas fa-screwdriver-wrench me-1"></i> Catat Pemeriksaan / Perawatan
            </button>
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
    <!-- KOLOM KIRI: IDENTITAS ASET, SPESIFIKASI, & LOKASI MAPS -->
    <div class="col-lg-8">
        <div class="card p-4 border-0 shadow-sm mb-4">
            <h5 class="fw-bold mb-3" style="font-size: 1.05rem; color: var(--nx-text);">
                <i class="fas fa-info-circle text-primary me-2"></i>Informasi &amp; Identitas Sarana
            </h5>

            <div class="row g-3">
                <div class="col-sm-6">
                    <span class="text-muted small d-block">Nomor ID Barcode Aset</span>
                    <strong class="font-monospace fs-6" style="color: var(--nx-forest);">{{ $aset->nomor_aset }}</strong>
                </div>

                <div class="col-sm-6">
                    <span class="text-muted small d-block">Kategori Sarana</span>
                    <strong>{{ $aset->kategori }}</strong>
                </div>

                <div class="col-sm-6">
                    <span class="text-muted small d-block">Wilayah Cakupan</span>
                    <span class="badge rounded-pill px-2.5 py-1" style="background: var(--nx-powder); color: #1F517F;">
                        RT {{ $aset->rt_number }} / RW {{ $aset->rw_number }}
                    </span>
                </div>

                <div class="col-sm-6">
                    <span class="text-muted small d-block">Status Pemeliharaan</span>
                    <span class="badge bg-light text-dark border px-2.5 py-1">
                        {{ ucfirst(str_replace('_', ' ', $aset->status_pemeliharaan)) }}
                    </span>
                </div>

                <div class="col-12">
                    <span class="text-muted small d-block">Alamat &amp; Titik Lokasi Fisik</span>
                    <div class="p-3 rounded-3 mt-1" style="background: var(--nx-bg); border: 1px solid var(--nx-border);">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <strong class="d-block text-dark"><i class="fas fa-map-pin text-danger me-1"></i>{{ $aset->lokasi }}</strong>
                                @if($aset->latitude && $aset->longitude)
                                    <small class="text-muted font-monospace">Koordinat: {{ $aset->latitude }}, {{ $aset->longitude }}</small>
                                @else
                                    <small class="text-muted">Titik koordinat GPS belum ditentukan</small>
                                @endif
                            </div>
                            @if($aset->google_maps_url)
                                <a href="{{ $aset->google_maps_url }}" target="_blank" class="btn btn-sm btn-success px-3 py-1.5">
                                    <i class="fas fa-map-location-dot me-1"></i> Buka Google Maps
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <span class="text-muted small d-block">Tanggal Perolehan / Instalasi</span>
                    <strong>{{ $aset->tanggal_perolehan ? $aset->tanggal_perolehan->translatedFormat('d F Y') : '-' }}</strong>
                </div>

                <div class="col-sm-6">
                    <span class="text-muted small d-block">Pemeriksaan Fisik Terakhir</span>
                    <strong>{{ $aset->terakhir_diperiksa ? $aset->terakhir_diperiksa->translatedFormat('d F Y · H:i') : 'Belum pernah' }}</strong>
                </div>

                @if($aset->spesifikasi)
                    <div class="col-12">
                        <span class="text-muted small d-block">Spesifikasi Teknis &amp; Catatan Khusus</span>
                        <p class="mb-0 small text-muted p-3 rounded-3 mt-1" style="background: var(--nx-bg); border: 1px solid var(--nx-border); line-height: 1.6;">
                            {{ $aset->spesifikasi }}
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- TAB NAVIGASI: RIWAYAT PEMERIKSAAN, PENGADUAN, DAN DOKUMENTASI -->
        <div class="card p-3 border-0 shadow-sm mb-4">
            <ul class="nav nav-pills gap-2" id="asetDetailSubTab" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active py-2 px-3 fw-bold small" id="pemeriksaan-tab" data-bs-toggle="tab" data-bs-target="#tab-pemeriksaan" type="button">
                        <i class="fas fa-screwdriver-wrench me-1"></i> Riwayat Pemeriksaan &amp; Perawatan ({{ $aset->pemeriksaans->count() }})
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link py-2 px-3 fw-bold small" id="pengaduan-tab" data-bs-toggle="tab" data-bs-target="#tab-pengaduan" type="button">
                        <i class="fas fa-clipboard-list me-1"></i> Pengaduan Terkait ({{ $aset->pengaduans->count() }})
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link py-2 px-3 fw-bold small" id="dokumen-tab" data-bs-toggle="tab" data-bs-target="#tab-dokumen" type="button">
                        <i class="fas fa-images me-1"></i> Dokumentasi Foto ({{ $aset->dokumentasis->count() }})
                    </button>
                </li>
            </ul>
        </div>

        <div class="tab-content">
            <!-- TAB 1: RIWAYAT PEMERIKSAAN & PERAWATAN -->
            <div class="tab-pane fade show active" id="tab-pemeriksaan">
                <div class="card p-4 border-0 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0" style="font-size: 0.95rem;">
                            <i class="fas fa-clipboard-check text-primary me-2"></i>Log Pemeriksaan &amp; Servis Teknis
                        </h6>
                        @if(!$isWarga)
                            <button type="button" class="btn btn-xs btn-primary py-1 px-2.5" data-bs-toggle="modal" data-bs-target="#modalCatatPemeriksaan">
                                <i class="fas fa-plus me-1"></i> Catat Log Baru
                            </button>
                        @endif
                    </div>

                    @if($aset->pemeriksaans->isEmpty())
                        <div class="text-center py-4 text-muted small">
                            <i class="fas fa-shield-halved fa-2x mb-2 d-block text-secondary opacity-50"></i>
                            Belum ada catatan riwayat pemeriksaan khusus untuk sarana ini.
                        </div>
                    @else
                        <div class="timeline position-relative">
                            @foreach($aset->pemeriksaans as $pem)
                                <div class="p-3 rounded-3 mb-3" style="background: var(--nx-bg); border: 1px solid var(--nx-border);">
                                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-1.5">
                                        <div>
                                            <strong class="d-block" style="color: var(--nx-text);">
                                                {{ ucfirst(str_replace('_', ' ', $pem->jenis_kegiatan)) }}
                                            </strong>
                                            <small class="text-muted">
                                                <i class="far fa-calendar-alt me-1"></i>{{ $pem->tanggal ? $pem->tanggal->translatedFormat('d F Y · H:i') : '-' }} WIB
                                                • Petugas: <strong>{{ $pem->petugas_nama ?? ($pem->user->name ?? 'Tim Teknis') }}</strong>
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge rounded-pill px-2.5 py-1 {{ $pem->kondisi_setelah === 'baik' ? 'bg-success' : 'bg-warning text-dark' }}">
                                                Hasil: {{ ucfirst($pem->kondisi_setelah) }}
                                            </span>
                                            @if($pem->biaya > 0)
                                                <small class="d-block text-muted fw-bold mt-0.5">Biaya: Rp {{ number_format($pem->biaya, 0, ',', '.') }}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <p class="mb-2 text-muted small" style="line-height: 1.5;">{{ $pem->catatan }}</p>

                                    <!-- Foto sebelum / sesudah jika ada -->
                                    @if($pem->foto_sebelum || $pem->foto_setelah)
                                        <div class="d-flex gap-2 mt-2">
                                            @if($pem->foto_sebelum)
                                                <div class="rounded overflow-hidden border" style="width: 70px; height: 70px;">
                                                    <img src="{{ asset('storage/' . $pem->foto_sebelum) }}" alt="Sebelum" class="w-100 h-100 object-fit-cover" title="Foto Sebelum">
                                                </div>
                                            @endif
                                            @if($pem->foto_setelah)
                                                <div class="rounded overflow-hidden border" style="width: 70px; height: 70px;">
                                                    <img src="{{ asset('storage/' . $pem->foto_setelah) }}" alt="Setelah" class="w-100 h-100 object-fit-cover" title="Foto Setelah">
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- TAB 2: PENGADUAN TERKAIT ASET INI -->
            <div class="tab-pane fade" id="tab-pengaduan">
                <div class="card p-4 border-0 shadow-sm">
                    <h6 class="fw-bold mb-3" style="font-size: 0.95rem;">
                        <i class="fas fa-list-check text-primary me-2"></i>Daftar Laporan Warga Terkait Aset Ini
                    </h6>

                    @if($aset->pengaduans->isEmpty())
                        <div class="text-center py-4 text-muted small">
                            <i class="fas fa-check-circle text-success fa-2x mb-2 d-block"></i>
                            Tidak ada keluhan atau pengaduan kerusakan aktif terkait sarana ini.
                        </div>
                    @else
                        <div class="d-flex flex-column gap-2.5">
                            @foreach($aset->pengaduans as $pg)
                                @php $sBadge = $pg->status_badge; @endphp
                                <div class="p-3 rounded-3 border d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="badge rounded-pill font-monospace" style="background: var(--nx-bg); color: var(--nx-forest); border: 1px solid var(--nx-border); font-size: 0.72rem;">
                                                {{ $pg->nomor_pengaduan ?: 'PGD-'.$pg->id }}
                                            </span>
                                            <span class="badge rounded-pill px-2.5 py-0.5" style="background: {{ $sBadge['bg'] }}; color: {{ $sBadge['color'] }}; font-size: 0.72rem;">
                                                {{ $pg->status_label }}
                                            </span>
                                        </div>
                                        <a href="{{ route('pengaduan.show', $pg->id) }}" class="fw-bold text-decoration-none d-block small" style="color: var(--nx-text);">
                                            {{ $pg->title }}
                                        </a>
                                        <small class="text-muted">
                                            Oleh: {{ $pg->user->name ?? 'Warga' }} • {{ $pg->created_at ? $pg->created_at->translatedFormat('d M Y') : '-' }}
                                        </small>
                                    </div>
                                    <a href="{{ route('pengaduan.show', $pg->id) }}" class="btn btn-sm btn-outline-primary px-3 py-1">
                                        Lihat Laporan <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- TAB 3: DOKUMENTASI FOTO SEBELUM & SESUDAH -->
            <div class="tab-pane fade" id="tab-dokumen">
                <div class="card p-4 border-0 shadow-sm">
                    <h6 class="fw-bold mb-3" style="font-size: 0.95rem;">
                        <i class="fas fa-images text-primary me-2"></i>Dokumentasi Visual Lapangan
                    </h6>

                    @if($aset->dokumentasis->isEmpty())
                        <div class="text-center py-4 text-muted small">
                            Belum ada foto dokumentasi diunggah untuk aset ini.
                        </div>
                    @else
                        <div class="row g-3">
                            @foreach($aset->dokumentasis as $dok)
                                <div class="col-md-4 col-sm-6">
                                    <div class="card h-100 p-2 border rounded-3">
                                        <div class="position-relative overflow-hidden rounded-2 mb-2" style="height: 140px; background: #E2E8F0;">
                                            @if(file_exists(public_path('storage/' . $dok->foto)))
                                                <img src="{{ asset('storage/' . $dok->foto) }}" alt="Foto" class="w-100 h-100 object-fit-cover">
                                            @else
                                                <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-muted" style="background: var(--nx-bg);">
                                                    <i class="fas fa-camera fa-2x mb-1 text-secondary opacity-50"></i>
                                                    <span style="font-size: 0.7rem;">Foto Tersimpan</span>
                                                </div>
                                            @endif
                                            <span class="badge position-absolute top-0 start-0 m-1.5 px-2 py-0.5" style="background: rgba(0,0,0,0.65); font-size: 0.65rem;">
                                                {{ ucfirst(str_replace('_', ' ', $dok->tipe_dokumentasi)) }}
                                            </span>
                                        </div>
                                        <small class="text-muted d-block text-truncate" title="{{ $dok->keterangan }}">{{ $dok->keterangan ?: 'Dokumentasi lapangan' }}</small>
                                        <small class="text-muted mt-auto" style="font-size: 0.68rem;">{{ $dok->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- KOLOM KANAN: KARTU QR CODE RESMI & GOOGLE MAPS INTEGRATION -->
    <div class="col-lg-4">
        <!-- KARTU QR CODE FISIK ASET -->
        <div class="card p-4 border-0 shadow-sm mb-4 text-center" style="background: #FFFFFF;">
            <span class="badge rounded-pill mb-2 px-3 py-1 mx-auto" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-weight: 700;">
                <i class="fas fa-qrcode me-1"></i> QR Code Identifikasi Resmi
            </span>
            <h6 class="fw-bold mb-1" style="color: var(--nx-text);">Barcode Identitas Sarana</h6>
            <p class="text-muted small mb-3">Ditempelkan pada plat fisik tiang/perangkat untuk pemindaian lapangan cepat</p>

            <!-- Render QR Code Visual -->
            <div class="p-3 rounded-4 mx-auto mb-3 border d-inline-block" style="background: #FFFFFF; box-shadow: var(--nx-shadow);">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($aset->nomor_aset) }}"
                     alt="QR Code {{ $aset->nomor_aset }}"
                     class="img-fluid rounded"
                     style="width: 170px; height: 170px;"
                     onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'170\' height=\'170\' viewBox=\'0 0 170 170\'><rect width=\'170\' height=\'170\' fill=\'%23F1F5F9\'/><text x=\'50%25\' y=\'50%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' font-family=\'monospace\' font-size=\'14\' fill=\'%23285943\'>{{ $aset->nomor_aset }}</text></svg>'">
                <div class="mt-2 font-monospace fw-bold small text-muted">{{ $aset->nomor_aset }}</div>
            </div>

            <div class="d-flex justify-content-center gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary px-3" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Cetak Label QR
                </button>
            </div>
        </div>

        <!-- INTEGRASI GOOGLE MAPS CARD -->
        <div class="card p-4 border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #F0FDF4 0%, #DCFCE7 100%); border: 1px solid var(--nx-sage) !important;">
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="rounded-circle d-flex align-items-center justify-content-center"
                      style="width: 36px; height: 36px; background: var(--nx-forest); color: #FFFFFF;">
                    <i class="fas fa-map-location-dot"></i>
                </span>
                <h6 class="fw-bold mb-0" style="color: var(--nx-forest);">Lokasi Google Maps</h6>
            </div>
            <p class="small text-muted mb-3" style="line-height: 1.5;">
                Petugas dan warga dapat langsung melihat rute navigasi dan petunjuk arah ke titik sarana fisik ini.
            </p>

            @if($aset->latitude && $aset->longitude)
                <div class="p-2.5 rounded-3 bg-white border mb-3 small font-monospace">
                    <strong>Lat:</strong> {{ $aset->latitude }}<br>
                    <strong>Lng:</strong> {{ $aset->longitude }}
                </div>
                <a href="{{ $aset->google_maps_url }}" target="_blank" class="btn btn-success w-100 py-2 fw-bold">
                    <i class="fas fa-directions me-1"></i> Buka Petunjuk Arah di Maps
                </a>
            @else
                <div class="p-2.5 rounded-3 bg-white border mb-3 small text-muted">
                    <i class="fas fa-info-circle me-1 text-warning"></i> Titik koordinat numerik GPS belum ditentukan. Tautan Maps menggunakan deskripsi alamat.
                </div>
                @if($aset->google_maps_url)
                    <a href="{{ $aset->google_maps_url }}" target="_blank" class="btn btn-outline-success w-100 py-2 fw-bold">
                        <i class="fas fa-map-marker-alt me-1"></i> Cari Alamat di Maps
                    </a>
                @endif
            @endif
        </div>
    </div>
</div>

<!-- ==================== MODAL CATAT PEMERIKSAAN / PERAWATAN (ADMIN) ==================== -->
@if(!$isWarga)
<div class="modal fade" id="modalCatatPemeriksaan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('aset.pemeriksaan.store', $aset->id) }}" enctype="multipart/form-data" class="modal-content border-0 shadow">
            @csrf
            <div class="modal-header border-bottom py-3">
                <h6 class="modal-title fw-bold">
                    <i class="fas fa-screwdriver-wrench text-primary me-2"></i>Catat Hasil Pemeriksaan / Perawatan
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Jenis Kegiatan <span class="text-danger">*</span></label>
                    <select name="jenis_kegiatan" class="form-select form-select-sm" required>
                        <option value="pemeriksaan_rutin">Pemeriksaan Rutin / Patroli Fisik</option>
                        <option value="inspeksi_pengaduan">Inspeksi Tindak Lanjut Pengaduan Warga</option>
                        <option value="perawatan_berkala">Perawatan Berkala / Servis</option>
                        <option value="perbaikan_kerusakan">Perbaikan Kerusakan / Penggantian Part</option>
                    </select>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Tanggal &amp; Waktu <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="tanggal" class="form-control form-control-sm" value="{{ date('Y-m-d\TH:i') }}" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Kondisi Setelah Inspeksi <span class="text-danger">*</span></label>
                        <select name="kondisi_setelah" class="form-select form-select-sm" required>
                            <option value="baik">Kondisi Baik (Normal)</option>
                            <option value="rusak_ringan">Rusak Ringan</option>
                            <option value="rusak_berat">Rusak Berat</option>
                            <option value="dalam_perbaikan">Masih Dalam Perbaikan</option>
                        </select>
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Nama Petugas / Teknisi</label>
                        <input type="text" name="petugas_nama" class="form-control form-control-sm" value="{{ $user->name }}" placeholder="Contoh: Budi Santoso">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Estimasi Biaya (Rp)</label>
                        <input type="number" name="biaya" class="form-control form-control-sm" placeholder="0" min="0">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Catatan Hasil Pemeriksaan <span class="text-danger">*</span></label>
                    <textarea name="catatan" class="form-control form-control-sm" rows="3" placeholder="Tuliskan komponen yang diperiksa, tindakan yang diambil, atau penggantian suku cadang..." required></textarea>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Foto Sebelum Servis</label>
                        <input type="file" name="foto_sebelum" class="form-control form-control-sm" accept="image/*">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Foto Setelah Servis</label>
                        <input type="file" name="foto_setelah" class="form-control form-control-sm" accept="image/*">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top py-2.5">
                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-sm btn-primary px-3">
                    <i class="fas fa-save me-1"></i> Simpan Catatan Inspeksi
                </button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
