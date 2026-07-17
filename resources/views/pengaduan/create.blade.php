@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card p-4 shadow-sm border-0" style="border-radius: 16px; background-color: var(--bg-card); border: 1px solid var(--border-color);">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold mb-0 text-dark" style="color: var(--text-main) !important;">
                        <i class="fas fa-bullhorn text-primary me-2"></i>Buat Pengaduan / Laporan
                    </h4>
                    <a href="{{ route('pengaduan.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 8px;"><i class="fas fa-arrow-left me-1"></i>Kembali</a>
                </div>

                <!-- SIMULASI QR SCAN BUTTON -->
                <div class="p-3 mb-4 rounded-3 text-center border shadow-sm" style="background: rgba(16,185,129,0.03); border-color: rgba(16,185,129,0.2) !important;">
                    <i class="fas fa-qrcode text-success fa-2x mb-2"></i>
                    <h6 class="fw-bold text-success mb-1">Lapor Cepat via QR Code Aset Lingkungan</h6>
                    <p class="text-muted small mb-3">Pindai kode QR/Barcode yang tertera pada tiang listrik, hydrant, wifi, atau bak sampah untuk mendeteksi lokasi otomatis.</p>
                    <button type="button" class="btn btn-success px-4 py-2" data-bs-toggle="modal" data-bs-target="#scanModal" style="border-radius: 10px; font-weight: 500;">
                        <i class="fas fa-camera me-1"></i>Buka Kamera Pemindai (Simulasi)
                    </button>
                </div>

                <form method="POST" action="{{ route('pengaduan.store') }}">
                    @csrf

                    <!-- SCAN METADATA INFO (COLLAPSIBLE/DYNAMIC) -->
                    <div id="scanMetadataContainer" class="p-3 mb-4 rounded bg-light border border-info-subtle border-start border-4" style="display: none;">
                        <h6 class="fw-bold text-info mb-2"><i class="fas fa-info-circle me-1"></i>Data Aset Berhasil Dipindai:</h6>
                        <div class="row g-2 small text-secondary">
                            <div class="col-6">
                                <strong>Kode QR Aset:</strong> <span id="metaBarcode" class="text-dark fw-semibold"></span>
                            </div>
                            <div class="col-6">
                                <strong>Kategori Aset:</strong> <span id="metaCategory" class="text-dark fw-semibold"></span>
                            </div>
                            <div class="col-6">
                                <strong>Nomor RT:</strong> <span id="metaRt" class="text-dark fw-semibold"></span>
                            </div>
                            <div class="col-6">
                                <strong>Koordinat:</strong> <span id="metaCoords" class="text-dark fw-semibold"></span>
                            </div>
                            <div class="col-12 mt-1">
                                <strong>Detail Lokasi:</strong> <span id="metaLocation" class="text-dark"></span>
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
                        <label class="form-label fw-semibold text-secondary">Judul Laporan</label>
                        <input type="text" name="title" id="inputTitle" class="form-control form-control-lg" placeholder="Contoh: Korsleting Kabel Tiang Listrik RT 006" required style="border-radius: 10px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Isi Laporan / Keluhan</label>
                        <textarea name="content" id="inputContent" class="form-control" rows="5" placeholder="Jelaskan secara rinci kendala atau kerusakan fasilitas publik yang terjadi agar mempermudah teknisi..." required style="border-radius: 10px;"></textarea>
                    </div>

                    <button class="btn btn-primary btn-lg w-100 shadow-sm" style="border-radius: 12px; font-weight: 600;"><i class="fas fa-paper-plane me-1"></i>Kirim Laporan Pengaduan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MODAL SIMULATOR SCANNER -->
<div class="modal fade" id="scanModal" tabindex="-1" aria-labelledby="scanModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 20px;">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="scanModalLabel"><i class="fas fa-barcode me-2"></i>Kamera Pemindai Aset</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <!-- Viewfinder Simulator -->
        <div class="position-relative overflow-hidden mb-4 bg-dark rounded-4 d-flex align-items-center justify-content-center" style="height: 180px;">
            <div class="position-absolute w-100 h-100 bg-opacity-25" style="border: 4px dashed #10b981; pointer-events: none; border-radius: 16px; inset: 0;"></div>
            <!-- Laser scanning line -->
            <div class="w-100 bg-success opacity-75 position-absolute" style="height: 2px; top: 0; animation: scanLine 2s infinite linear; left: 0;"></div>
            <i class="fas fa-qrcode text-white fa-4x opacity-50"></i>
        </div>

        <h6 class="fw-bold mb-3"><i class="fas fa-list me-1"></i>Pilih Aset Terdekat (Simulasi Pindai Barcode):</h6>
        <div class="list-group">
            <button type="button" class="list-group-item list-group-item-action p-3 d-flex align-items-center gap-3 border-0 border-bottom scan-option" 
                data-barcode="TL-018-006-012"
                data-category="Tiang Listrik (Kelistrikan)"
                data-rt="006"
                data-location="Tiang Listrik TL-018-006-012, Jl. Sakura Depan Blok A No. 12, RT 006 RW 018"
                data-lat="-6.914744"
                data-lng="107.609810"
                data-title="Korsleting & Lampu Jalan Padam di Tiang TL-018-006-012"
                data-content="Lampu penerangan jalan umum pada tiang listrik nomor TL-018-006-012 mati total sejak semalam. Sempat terdengar suara letupan kecil di bagian atas kotak sekring tiang.">
                <div class="rounded-circle p-2.5 bg-warning-subtle text-warning d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="fas fa-bolt"></i></div>
                <div class="text-start">
                    <strong class="d-block text-dark" style="font-size: 0.9rem;">Tiang Listrik TL-018-006-012</strong>
                    <span class="text-muted small" style="font-size: 0.75rem;">RT 006 · Jl. Sakura Depan Blok A</span>
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
                <div class="rounded-circle p-2.5 bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="fas fa-tint"></i></div>
                <div class="text-start">
                    <strong class="d-block text-dark" style="font-size: 0.9rem;">Pilar Hydrant HYD-018-007-003</strong>
                    <span class="text-muted small" style="font-size: 0.75rem;">RT 007 · Samping Pos Ronda Cemara</span>
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
                <div class="rounded-circle p-2.5 bg-info-subtle text-info d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="fas fa-video"></i></div>
                <div class="text-start">
                    <strong class="d-block text-dark" style="font-size: 0.9rem;">Tiang CCTV CCTV-018-008-005</strong>
                    <span class="text-muted small" style="font-size: 0.75rem;">RT 008 · Pertigaan Balai Pertemuan</span>
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
                <div class="rounded-circle p-2.5 bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="fas fa-trash-alt"></i></div>
                <div class="text-start">
                    <strong class="d-block text-dark" style="font-size: 0.9rem;">Bak Sampah BSK-018-009-001</strong>
                    <span class="text-muted small" style="font-size: 0.75rem;">RT 009 · Depan Taman Bermain Anak</span>
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
                <div class="rounded-circle p-2.5 bg-danger-subtle text-danger d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="fas fa-door-open"></i></div>
                <div class="text-start">
                    <strong class="d-block text-dark" style="font-size: 0.9rem;">Gerbang Portal PRT-018-010-002</strong>
                    <span class="text-muted small" style="font-size: 0.75rem;">RT 010 · Jl. Kenanga Gerbang Utama</span>
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
        background-color: rgba(16, 185, 129, 0.06) !important;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tampilkan otomatis modal scan jika parameter URL scan=1
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('scan') === '1') {
            const scanModal = new bootstrap.Modal(document.getElementById('scanModal'));
            scanModal.show();
        }

        // Logic pemilihan aset scan
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

                // Isi input fields
                document.getElementById('inputBarcode').value = barcode;
                document.getElementById('inputCategory').value = category;
                document.getElementById('inputRt').value = rt;
                document.getElementById('inputLocation').value = location;
                document.getElementById('inputLat').value = lat;
                document.getElementById('inputLng').value = lng;
                document.getElementById('inputTitle').value = title;
                document.getElementById('inputContent').value = content;

                // Tampilkan metadata box
                document.getElementById('metaBarcode').textContent = barcode;
                document.getElementById('metaCategory').textContent = category;
                document.getElementById('metaRt').textContent = 'RT ' + rt;
                document.getElementById('metaCoords').textContent = lat + ', ' + lng;
                document.getElementById('metaLocation').textContent = location;
                document.getElementById('scanMetadataContainer').style.display = 'block';

                // Bunyi bip audio (HTML5 Web Audio API)
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

                // Tutup modal
                bootstrap.Modal.getInstance(document.getElementById('scanModal')).hide();
            });
        });
    });
</script>
@endpush
