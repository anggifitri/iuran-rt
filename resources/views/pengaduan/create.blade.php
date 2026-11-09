@extends('layouts.app')

@section('title', 'Buat Pengaduan & Laporan Lingkungan — NexaNest')

@section('content')
<div class="nx-page-header">
    <div>
        <span class="badge rounded-pill mb-2 px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
            <i class="fas fa-leaf me-1"></i> Pelaporan Warga &amp; Aset Lingkungan
        </span>
        <h1 class="nx-page-title">Buat Pengaduan / Laporan Baru</h1>
        <p class="nx-page-subtitle">Laporkan kendala fasilitas umum, kebersihan, atau keamanan lingkungan secara cepat.</p>
    </div>
    <a href="{{ route('pengaduan.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Laporan
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card p-4">
            <!-- SIMULASI QR SCANNER BOX -->
            <div class="p-4 mb-4 rounded-4 text-center" style="background: var(--nx-mint-soft); border: 1.5px dashed var(--nx-sage);">
                <div class="mx-auto mb-2 rounded-circle d-flex align-items-center justify-content-center"
                     style="width: 52px; height: 52px; background: var(--nx-white); color: var(--nx-forest); box-shadow: var(--nx-shadow);">
                    <i class="fas fa-qrcode fa-lg"></i>
                </div>
                <h6 class="fw-bold mb-1" style="color: var(--nx-forest);">Lapor Cepat via QR Code Aset Lingkungan</h6>
                <p class="text-muted small mb-3">
                    Pindai kode QR yang tertera pada tiang lampu jalan, hydrant, CCTV, atau bak sampah untuk mengisi titik lokasi otomatis.
                </p>
                <button type="button" class="btn btn-primary px-4" data-bs-toggle="modal" data-bs-target="#scanModal">
                    <i class="fas fa-camera me-1"></i> Buka Kamera Pemindai QR (Simulasi)
                </button>
            </div>

            <form method="POST" action="{{ route('pengaduan.store') }}">
                @csrf

                <!-- SCAN METADATA INFO -->
                <div id="scanMetadataContainer" class="p-3 mb-4 rounded-4" style="display: none; background: var(--nx-powder-soft); border: 1px solid var(--nx-pastel-blue);">
                    <h6 class="fw-bold mb-2" style="color: #2B5F8E;"><i class="fas fa-check-circle me-1"></i>Data Aset Lingkungan Berhasil Dipindai:</h6>
                    <div class="row g-2 small">
                        <div class="col-sm-6">
                            <span class="text-muted">Kode QR Aset:</span> <strong id="metaBarcode" class="d-block"></strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted">Kategori Aset:</span> <strong id="metaCategory" class="d-block"></strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted">Wilayah RT:</span> <strong id="metaRt" class="d-block"></strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted">Koordinat GPS:</span> <strong id="metaCoords" class="d-block"></strong>
                        </div>
                        <div class="col-12">
                            <span class="text-muted">Detail Lokasi:</span> <strong id="metaLocation" class="d-block"></strong>
                        </div>
                    </div>
                </div>

                <!-- Hidden inputs for scanner values -->
                <input type="hidden" name="barcode_code" id="inputBarcode">
                <input type="hidden" name="category" id="inputCategory">
                <input type="hidden" name="rt_number" id="inputRt">
                <input type="hidden" name="location_details" id="inputLocation">
                <input type="hidden" name="latitude" id="inputLat">
                <input type="hidden" name="longitude" id="inputLng">

                <div class="mb-3">
                    <label class="form-label">Judul Laporan / Keluhan <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="inputTitle" class="form-control"
                           placeholder="Contoh: Lampu Penerangan Jalan Padam di RT 006" required>
                </div>

                <div class="mb-4">
                    <label class="form-label">Rincian Laporan / Kronologi <span class="text-danger">*</span></label>
                    <textarea name="content" id="inputContent" class="form-control" rows="5"
                              placeholder="Jelaskan secara rinci kondisi kerusakan fasilitas atau masalah lingkungan agar memudahkan petugas menindaklanjuti..."
                              required></textarea>
                </div>

                <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                    <a href="{{ route('pengaduan.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-paper-plane me-1"></i> Kirim Laporan Pengaduan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL SIMULATOR SCANNER -->
<div class="modal fade" id="scanModal" tabindex="-1" aria-labelledby="scanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="scanModalLabel" style="color: var(--nx-forest);">
                    <i class="fas fa-qrcode me-2"></i>Pemindai QR Aset Lingkungan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Viewfinder Simulator -->
                <div class="position-relative overflow-hidden mb-4 rounded-4 d-flex align-items-center justify-content-center"
                     style="height: 170px; background: #1B3B2B;">
                    <div class="position-absolute w-100 h-100" style="border: 3px dashed #A8C9B5; pointer-events: none; border-radius: 16px; inset: 0;"></div>
                    <div class="w-100 position-absolute" style="height: 2px; background: #DCEBDD; top: 0; animation: scanLine 2s infinite linear; left: 0;"></div>
                    <i class="fas fa-qrcode text-white fa-4x opacity-50"></i>
                </div>

                <h6 class="fw-bold mb-3 small text-uppercase text-muted"><i class="fas fa-list me-1"></i>Pilih Aset Lingkungan Terdekat (Simulasi):</h6>
                <div class="list-group rounded-4 overflow-hidden border">
                    <button type="button" class="list-group-item list-group-item-action p-3 d-flex align-items-center gap-3 border-0 border-bottom scan-option"
                        data-barcode="TL-018-006-012"
                        data-category="Tiang Listrik (Kelistrikan)"
                        data-rt="006"
                        data-location="Tiang Listrik TL-018-006-012, Jl. Sakura Depan Blok A No. 12, RT 006 RW 018"
                        data-lat="-6.914744"
                        data-lng="107.609810"
                        data-title="Korsleting & Lampu Jalan Padam di Tiang TL-018-006-012"
                        data-content="Lampu penerangan jalan umum pada tiang listrik nomor TL-018-006-012 mati total sejak semalam. Sempat terdengar suara letupan kecil di bagian atas kotak sekring tiang.">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width: 40px; height: 40px; background: var(--nx-warning-soft); color: var(--nx-warning);"><i class="fas fa-bolt"></i></div>
                        <div class="text-start">
                            <strong class="d-block" style="font-size: 0.9rem; color: var(--nx-text);">Tiang Listrik TL-018-006-012</strong>
                            <span class="text-muted small">RT 006 · Jl. Sakura Depan Blok A</span>
                        </div>
                    </button>

                    <button type="button" class="list-group-item list-group-item-action p-3 d-flex align-items-center gap-3 border-0 border-bottom scan-option"
                        data-barcode="HYD-018-007-003"
                        data-category="Hydrant Damkar (Utilitas Air)"
                        data-rt="007"
                        data-location="Hydrant Pemadam HYD-018-007-003, Samping Pos Ronda Cemara, RT 007 RW 018"
                        data-lat="-6.915230"
                        data-lng="107.610245"
                        data-title="Kebocoran Air Bersih di Pilar Hydrant HYD-018-007-003"
                        data-content="Ada rembesan air bersih yang cukup deras keluar dari katup bawah pilar hydrant nomor HYD-018-007-003. Air menggenangi jalan masuk gang Cemara.">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width: 40px; height: 40px; background: var(--nx-powder); color: #2B5F8E;"><i class="fas fa-tint"></i></div>
                        <div class="text-start">
                            <strong class="d-block" style="font-size: 0.9rem; color: var(--nx-text);">Pilar Hydrant HYD-018-007-003</strong>
                            <span class="text-muted small">RT 007 · Samping Pos Ronda Cemara</span>
                        </div>
                    </button>

                    <button type="button" class="list-group-item list-group-item-action p-3 d-flex align-items-center gap-3 border-0 border-bottom scan-option"
                        data-barcode="CCTV-018-008-005"
                        data-category="Tiang WiFi/CCTV (Keamanan)"
                        data-rt="008"
                        data-location="Tiang CCTV CCTV-018-008-005, Pertigaan Balai Pertemuan, RT 008 RW 018"
                        data-lat="-6.914390"
                        data-lng="107.611102"
                        data-title="Kamera CCTV Keamanan Mati di Tiang CCTV-018-008-005"
                        data-content="Tampilan feed CCTV keamanan lingkungan pada tiang nomor CCTV-018-008-005 terputus dan tidak mengirimkan gambar sejak tadi siang. Mohon dicek koneksi kabel LAN atau powernya.">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width: 40px; height: 40px; background: var(--nx-powder-soft); color: #2B5F8E;"><i class="fas fa-video"></i></div>
                        <div class="text-start">
                            <strong class="d-block" style="font-size: 0.9rem; color: var(--nx-text);">Tiang CCTV CCTV-018-008-005</strong>
                            <span class="text-muted small">RT 008 · Pertigaan Balai Pertemuan</span>
                        </div>
                    </button>

                    <button type="button" class="list-group-item list-group-item-action p-3 d-flex align-items-center gap-3 border-0 border-bottom scan-option"
                        data-barcode="BSK-018-009-001"
                        data-category="Bak Sampah (Sanitasi)"
                        data-rt="009"
                        data-location="Bak Sampah Fiber BSK-018-009-001, Depan Taman Bermain Anak, RT 009 RW 018"
                        data-lat="-6.913880"
                        data-lng="107.610540"
                        data-title="Tutup Bak Sampah Komunal BSK-018-009-001 Pecah"
                        data-content="Engsel pintu penutup bak sampah komunal fiber nomor BSK-018-009-001 patah akibat benturan. Sampah menjadi berserakan karena tidak bisa tertutup rapat.">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width: 40px; height: 40px; background: var(--nx-mint); color: var(--nx-forest);"><i class="fas fa-recycle"></i></div>
                        <div class="text-start">
                            <strong class="d-block" style="font-size: 0.9rem; color: var(--nx-text);">Bak Sampah BSK-018-009-001</strong>
                            <span class="text-muted small">RT 009 · Depan Taman Bermain Anak</span>
                        </div>
                    </button>

                    <button type="button" class="list-group-item list-group-item-action p-3 d-flex align-items-center gap-3 border-0 scan-option"
                        data-barcode="PRT-018-010-002"
                        data-category="Portal Gate (Akses RW)"
                        data-rt="010"
                        data-location="Pintu Gerbang Utama PRT-018-010-002, Jl. Kenanga Masuk RT 010 RW 018"
                        data-lat="-6.916120"
                        data-lng="107.612003"
                        data-title="Portal Gerbang Otomatis PRT-018-010-002 Macet"
                        data-content="Motor penggerak portal otomatis pada gerbang masuk PRT-018-010-002 tidak merespon saat kartu RFID warga ditempelkan. Terpaksa gerbang dibuka secara manual.">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width: 40px; height: 40px; background: var(--nx-soft-red); color: var(--nx-danger);"><i class="fas fa-door-open"></i></div>
                        <div class="text-start">
                            <strong class="d-block" style="font-size: 0.9rem; color: var(--nx-text);">Gerbang Portal PRT-018-010-002</strong>
                            <span class="text-muted small">RT 010 · Jl. Kenanga Gerbang Utama</span>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @keyframes scanLine {
        0% { top: 0; }
        50% { top: 100%; }
        100% { top: 0; }
    }
    .scan-option:hover {
        background-color: var(--nx-mint-soft) !important;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('scan') === '1') {
        const scanModal = new bootstrap.Modal(document.getElementById('scanModal'));
        scanModal.show();
    }

    document.querySelectorAll('.scan-option').forEach(btn => {
        btn.addEventListener('click', function() {
            const barcode = this.getAttribute('data-barcode');
            const category = this.getAttribute('data-category');
            const rt = this.getAttribute('data-rt');
            const location = this.getAttribute('data-location');
            const lat = this.getAttribute('data-lat');
            const lng = this.getAttribute('data-lng');
            const title = this.getAttribute('data-title');
            const content = this.getAttribute('data-content');

            document.getElementById('inputBarcode').value = barcode;
            document.getElementById('inputCategory').value = category;
            document.getElementById('inputRt').value = rt;
            document.getElementById('inputLocation').value = location;
            document.getElementById('inputLat').value = lat;
            document.getElementById('inputLng').value = lng;
            document.getElementById('inputTitle').value = title;
            document.getElementById('inputContent').value = content;

            document.getElementById('metaBarcode').textContent = barcode;
            document.getElementById('metaCategory').textContent = category;
            document.getElementById('metaRt').textContent = 'RT ' + rt;
            document.getElementById('metaCoords').textContent = lat + ', ' + lng;
            document.getElementById('metaLocation').textContent = location;
            document.getElementById('scanMetadataContainer').style.display = 'block';

            try {
                const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioCtx.createOscillator();
                const gainNode = audioCtx.createGain();
                oscillator.connect(gainNode);
                gainNode.connect(audioCtx.destination);
                oscillator.type = 'sine';
                oscillator.frequency.value = 900;
                gainNode.gain.setValueAtTime(0.08, audioCtx.currentTime);
                oscillator.start();
                setTimeout(() => oscillator.stop(), 120);
            } catch(e) {}

            bootstrap.Modal.getInstance(document.getElementById('scanModal')).hide();
        });
    });
});
</script>
@endpush
