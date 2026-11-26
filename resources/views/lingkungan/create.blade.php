@extends('layouts.app')

@section('title', 'Buat Laporan Lingkungan Bulanan — NexaNest')

@section('content')
<div class="nx-page-header">
    <div>
        <div class="d-flex align-items-center gap-2 mb-2">
            <a href="{{ route('lingkungan.index') }}" class="text-muted text-decoration-none small">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Pusat Laporan
            </a>
            <span class="text-muted">/</span>
            <span class="badge rounded-pill px-2.5 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
                Formulir Laporan Bulanan
            </span>
        </div>
        <h1 class="nx-page-title">Buat Laporan Kondisi Lingkungan RT {{ $rtNumber }}</h1>
        <p class="nx-page-subtitle">
            Isi laporan berkala kondisi kebersihan, saluran air, program gotong royong, dan kendala lingkungan untuk diserahkan ke Admin RW.
        </p>
    </div>
</div>

<form action="{{ route('lingkungan.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-4">
        <!-- FORM UTAMA (7 BAGIAN SESUAI STANDAR) -->
        <div class="col-lg-8">
            <!-- 1. INFORMASI LAPORAN -->
            <div class="card p-4 mb-4 shadow-xs" style="border-radius: 12px;">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <span class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: var(--nx-mint); color: var(--nx-forest); font-weight: 700;">1</span>
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 1rem;">Informasi &amp; Identitas Laporan</h5>
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Wilayah RT</label>
                        @if(Auth::user()->isAdminRt())
                            <input type="text" class="form-control bg-light" value="RT {{ Auth::user()->rt_number }}" readonly>
                            <input type="hidden" name="rt_number" value="{{ Auth::user()->rt_number }}">
                        @else
                            <select name="rt_number" class="form-select" required>
                                @foreach($allRts as $rtOpt)
                                    <option value="{{ $rtOpt }}" {{ $rtNumber == $rtOpt ? 'selected' : '' }}>RT {{ $rtOpt }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Bulan Periode</label>
                        <select name="periode_bulan" class="form-select" required>
                            @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $k => $nama)
                                <option value="{{ $k }}" {{ $periodeBulan == $k ? 'selected' : '' }}>{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Tahun</label>
                        <input type="text" name="periode_tahun" class="form-control" value="{{ $periodeTahun }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Nama Admin / Pengurus RT Pengirim</label>
                        <input type="text" class="form-control bg-light" value="{{ Auth::user()->name }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Tanggal Input</label>
                        <input type="text" class="form-control bg-light" value="{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}" readonly>
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
                        <option value="Sangat Bersih & Tertata">Sangat Bersih &amp; Tertata (Semua gang terpelihara, bebas sampah liar)</option>
                        <option value="Cukup Bersih & Terawat" selected>Cukup Bersih &amp; Terawat (Terpelihara baik, pembersihan rutin berjalan)</option>
                        <option value="Kurang Bersih / Perlu Penanganan">Kurang Bersih / Perlu Penanganan (Terdapat titik tumpukan sampah / kotor)</option>
                    </select>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Area yang Sudah Terjaga Kebersihannya</label>
                        <textarea name="area_terjaga_kebersihan" rows="2" class="form-control" placeholder="Contoh: Jalan utama RT, area depan pos ronda, dan taman koridor..."></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Area yang Masih Membutuhkan Perhatian</label>
                        <textarea name="area_perlu_perhatian_kebersihan" rows="2" class="form-control" placeholder="Contoh: Sudut perbatasan tanah kosong di gang 4..."></textarea>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Permasalahan Sampah atau Kebersihan</label>
                        <textarea name="masalah_sampah" rows="2" class="form-control" placeholder="Contoh: Jadwal pengangkutan DLH terkadang terlambat saat akhir pekan..."></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Keterangan Tambahan Kebersihan</label>
                        <textarea name="keterangan_kebersihan" rows="2" class="form-control" placeholder="Catatan khusus kebersihan RT..."></textarea>
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
                            <option value="Lancar Tanpa Hambatan" selected>Lancar Tanpa Hambatan (Struktur selokan baik &amp; bersih)</option>
                            <option value="Sedimen Cukup Tebal">Sedimen Cukup Tebal (Ada endapan lumpur, perlu dikeruk)</option>
                            <option value="Tersumbat / Berpotensi Genangan">Tersumbat / Berpotensi Genangan (Rawan meluap saat hujan)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Kondisi Aliran Air</label>
                        <select name="kondisi_aliran_air" class="form-select">
                            <option value="Lancar Mengalir" selected>Lancar Mengalir</option>
                            <option value="Lambat / Genangan Sedang">Lambat / Genangan Sedang</option>
                            <option value="Tersumbat Total">Tersumbat Total</option>
                        </select>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Area / Titik Rawan Genangan</label>
                        <textarea name="titik_rawan_genangan" rows="2" class="form-control" placeholder="Contoh: Depan rumah Blok B12 saat hujan deras > 1 jam..."></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Saluran yang Membutuhkan Pengerukan</label>
                        <textarea name="saluran_butuh_pembersihan" rows="2" class="form-control" placeholder="Contoh: Gorong-gorong cabang timur sepanjang 15 meter..."></textarea>
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
                            <option value="Subur & Asri Rindang" selected>Subur &amp; Asri Rindang</option>
                            <option value="Cukup Terawat">Cukup Terawat</option>
                            <option value="Kering / Butuh Peremajaan">Kering / Butuh Peremajaan</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Ketersediaan &amp; Kondisi Ruang Terbuka</label>
                        <input type="text" name="ketersediaan_ruang_terbuka" class="form-control" placeholder="Contoh: Taman terpadu RT terawat baik">
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Kegiatan Perawatan Tanaman</label>
                        <textarea name="kegiatan_perawatan_tanaman" rows="2" class="form-control" placeholder="Contoh: Penyiraman rutin pagi-sore oleh kader PKK dan warga..."></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Kendala Penghijauan</label>
                        <textarea name="kendala_penghijauan" rows="2" class="form-control" placeholder="Contoh: Dahan pohon menyentuh kabel listrik PLN..."></textarea>
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
                    <textarea name="kegiatan_kerja_bakti" rows="2" class="form-control" placeholder="Contoh: Kerja bakti pembersihan selokan serentak, pemangkasan rumput lapangan..."></textarea>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Tanggal Pelaksanaan</label>
                        <input type="date" name="tanggal_pelaksanaan_kegiatan" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Jumlah Peserta Warga (Orang)</label>
                        <input type="number" name="jumlah_peserta_kegiatan" class="form-control" placeholder="Contoh: 35">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Tautkan Agenda Rapat/Kegiatan</label>
                        <select name="agenda_id" class="form-select">
                            <option value="">-- Pilih Jika Terkait Agenda --</option>
                            @foreach($agendaTerkait as $ag)
                                <option value="{{ $ag->id }}">{{ $ag->judul }} ({{ \Carbon\Carbon::parse($ag->tanggal_waktu)->format('d M') }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Program Kebersihan &amp; Pilah Sampah</label>
                        <textarea name="program_kebersihan" rows="2" class="form-control" placeholder="Contoh: Gerakan pilah sampah plastik dan kardus untuk disetor ke Bank Sampah RW..."></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Program Lingkungan Lainnya</label>
                        <textarea name="program_lingkungan_lainnya" rows="2" class="form-control" placeholder="Contoh: Pemantauan jentik nyamuk rutin oleh ibu-ibu Dasawisma..."></textarea>
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
                        <textarea name="kendala_lingkungan" rows="2" class="form-control" placeholder="Contoh: Kurangnya karung sampah saat pengerukan sedimen lumpur..."></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Lokasi yang Butuh Perhatian Khusus</label>
                        <textarea name="lokasi_perhatian_khusus" rows="2" class="form-control" placeholder="Contoh: Gorong-gorong batas barat dekat jembatan masuk..."></textarea>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Rencana Tindak Lanjut Pengurus RT</label>
                        <textarea name="rencana_tindak_lanjut" rows="2" class="form-control" placeholder="Contoh: Mengagendakan kerja bakti lanjutan pada minggu ke-3..."></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Bantuan / Koordinasi yang Dibutuhkan dari RW</label>
                        <textarea name="bantuan_rw_dibutuhkan" rows="2" class="form-control" placeholder="Contoh: Bantuan armada truk pengangkut sampah atau koordinasi dinas terkait..."></textarea>
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
                        <input type="file" name="foto_dokumentasi" class="form-control" accept="image/*">
                        <small class="text-muted" style="font-size: 0.7rem;">Foto kondisi umum lingkungan.</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Foto Sebelum Kegiatan (Opsional)</label>
                        <input type="file" name="foto_sebelum" class="form-control" accept="image/*">
                        <small class="text-muted" style="font-size: 0.7rem;">Kondisi sebelum pembersihan.</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Foto Sesudah Kegiatan (Opsional)</label>
                        <input type="file" name="foto_sesudah" class="form-control" accept="image/*">
                        <small class="text-muted" style="font-size: 0.7rem;">Kondisi setelah pembersihan.</small>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold">Keterangan / Deskripsi Foto</label>
                    <input type="text" name="keterangan_foto" class="form-control" placeholder="Contoh: Dokumentasi pengerukan selokan dan pemangkasan dahan di RT...">
                </div>
            </div>

            <!-- TOMBOL AKSI SUBMIT / DRAFT -->
            <div class="d-flex justify-content-between align-items-center p-3 rounded-3 bg-white border shadow-xs">
                <a href="{{ route('lingkungan.index') }}" class="btn btn-outline-secondary">Batal</a>
                <div class="d-flex gap-2">
                    <button type="submit" name="action_type" value="draft" class="btn btn-outline-secondary px-3 fw-bold">
                        <i class="fas fa-floppy-disk me-1"></i> Simpan Sebagai Draft
                    </button>
                    <button type="submit" name="action_type" value="submit" class="btn btn-primary px-4 fw-bold shadow-sm" style="background: var(--nx-forest); border-color: var(--nx-forest);">
                        <i class="fas fa-paper-plane me-1"></i> Serahkan Laporan ke RW
                    </button>
                </div>
            </div>
        </div>

        <!-- SIDEBAR INFO & INTEGRASI PENDUKUNG -->
        <div class="col-lg-4">
            <!-- WIDGET DATA BANK SAMPAH RT BULAN INI -->
            <div class="card p-3.5 mb-3 shadow-xs" style="background: linear-gradient(135deg, #F5FBF6 0%, #FFFFFF 100%); border-radius: 12px;">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: var(--nx-mint); color: var(--nx-forest);">
                        <i class="fas fa-recycle"></i>
                    </span>
                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.9rem;">Referensi Bank Sampah RT {{ $rtNumber }}</h6>
                </div>
                <p class="text-muted small mb-2" style="font-size: 0.74rem;">
                    Data setoran sampah terpilah warga RT {{ $rtNumber }} yang tercatat di sistem pada bulan ini:
                </p>
                <div class="p-2.5 rounded-2 bg-white border text-center mb-2">
                    <span class="text-muted small d-block" style="font-size: 0.7rem;">Total Sampah Terpilah:</span>
                    <h4 class="fw-bold mb-0" style="color: var(--nx-forest);">{{ number_format($bankSampahKg, 1) }} <span class="fs-6 text-muted fw-normal">Kg</span></h4>
                </div>
                <small class="text-muted d-block" style="font-size: 0.72rem;">
                    <i class="fas fa-info-circle me-1 text-success"></i>Data ini terhubung otomatis dari modul Bank Sampah RW 018.
                </small>
            </div>

            <!-- WIDGET PENGADUAN LINGKUNGAN RT -->
            <div class="card p-3.5 mb-3 shadow-xs" style="border-radius: 12px;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.9rem;">
                        <i class="fas fa-triangle-exclamation text-warning me-1.5"></i>Pengaduan Warga RT {{ $rtNumber }}
                    </h6>
                    <span class="badge bg-light text-dark border">{{ count($pengaduanTerkait) }} Aduan</span>
                </div>
                <p class="text-muted small mb-2" style="font-size: 0.74rem;">
                    Pengaduan kategori lingkungan/kebersihan/drainase yang dilaporkan oleh warga di RT ini:
                </p>
                <div class="list-group list-group-flush small" style="max-height: 250px; overflow-y: auto;">
                    @forelse($pengaduanTerkait as $aduan)
                        <div class="list-group-item px-0 py-2 border-bottom">
                            <strong class="d-block text-dark text-truncate" style="font-size: 0.78rem;">{{ $aduan->title }}</strong>
                            <small class="text-muted d-block text-truncate" style="font-size: 0.7rem;">{{ $aduan->content }}</small>
                            <span class="badge bg-secondary-subtle text-secondary rounded-pill mt-1" style="font-size: 0.65rem;">
                                {{ ucfirst($aduan->status) }}
                            </span>
                        </div>
                    @empty
                        <small class="text-muted d-block py-2">Tidak ada pengaduan lingkungan aktif di wilayah RT ini.</small>
                    @endforelse
                </div>
            </div>

            <!-- KETENTUAN ALUR LAPORAN -->
            <div class="card p-3.5 shadow-xs" style="background: #F8FAF8; border-radius: 12px;">
                <h6 class="fw-bold mb-2 text-dark" style="font-size: 0.88rem;">
                    <i class="fas fa-circle-info text-success me-1.5"></i>Alur Pemeriksaan Laporan
                </h6>
                <ol class="small text-muted ps-3 mb-0" style="font-size: 0.74rem; line-height: 1.5;">
                    <li><strong>Draft:</strong> Disimpan sementara untuk dilengkapi kembali nanti.</li>
                    <li><strong>Menunggu Pemeriksaan:</strong> Terkirim ke Ketua RW. Laporan akan terkunci selama proses peninjauan.</li>
                    <li><strong>Diterima:</strong> Laporan disetujui RW dan diarsipkan permanen.</li>
                    <li><strong>Perlu Revisi:</strong> Ketua RW dapat meminta kelengkapan/foto tambahan yang dapat diperbaiki kembali oleh RT.</li>
                </ol>
            </div>
        </div>
    </div>
</form>
@endsection
