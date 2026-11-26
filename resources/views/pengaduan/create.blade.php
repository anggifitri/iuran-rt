@extends('layouts.app')

@section('title', 'Buat Pengaduan & Laporan Lingkungan — NexaNest')

@section('content')
@php
    $user = Auth::user();
@endphp

<div class="nx-page-header mb-4">
    <div>
        <span class="badge rounded-pill mb-2 px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
            <i class="fas fa-boxes-stacked me-1"></i> Pelaporan Warga &amp; Aset Lingkungan
        </span>
        <h1 class="nx-page-title">Buat Pengaduan / Laporan Sarana</h1>
        <p class="nx-page-subtitle">Laporkan kendala fasilitas umum, kebersihan, atau kerusakan sarana fisik lingkungan secara cepat dan terintegrasi GPS &amp; QR Code.</p>
    </div>
    <a href="{{ route('pengaduan.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Kembali ke Manajemen Aset &amp; Pengaduan
    </a>
</div>

<div class="row justify-content-center mb-4">
    <div class="col-lg-9">
        <div class="card p-4 border-0 shadow-sm">
            <!-- BANNER SCANNER QR ASET LINGKUNGAN -->
            <div class="p-4 mb-4 rounded-4 text-center" style="background: var(--nx-mint-soft); border: 1.5px dashed var(--nx-sage);">
                <div class="mx-auto mb-2 rounded-circle d-flex align-items-center justify-content-center"
                     style="width: 52px; height: 52px; background: #FFFFFF; color: var(--nx-forest); box-shadow: var(--nx-shadow);">
                    <i class="fas fa-qrcode fa-lg"></i>
                </div>
                <h6 class="fw-bold mb-1" style="color: var(--nx-forest);">Lapor Cepat Terhubung QR Code Aset</h6>
                <p class="text-muted small mb-3" style="max-width: 560px; margin: auto;">
                    Pindai kode QR fisik yang tertera pada tiang lampu jalan, hydrant, CCTV, atau bak sampah untuk mendeteksi ID sarana dan koordinat lokasi secara otomatis.
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-primary px-4 py-2" data-bs-toggle="modal" data-bs-target="#scanModal">
                        <i class="fas fa-camera me-1"></i> Buka Pemindai QR Code
                    </button>
                    <a href="{{ route('pengaduan.index', ['tab' => 'aset']) }}" class="btn btn-outline-secondary px-3 py-2">
                        <i class="fas fa-list me-1"></i> Pilih dari Database Aset
                    </a>
                </div>
            </div>

            <!-- CONTAINER DATA ASET TERDETEKSI DARI SCAN ATAU PREFILLED -->
            <div id="scanMetadataContainer" class="p-3.5 mb-4 rounded-4" style="{{ $prefilledAset ? '' : 'display: none;' }} background: var(--nx-powder-soft); border: 1.5px solid var(--nx-pastel-blue);">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold mb-0" style="color: #1F517F;">
                        <i class="fas fa-check-circle me-1 text-success"></i>Aset Lingkungan Terhubung ke Laporan:
                    </h6>
                    <button type="button" class="btn btn-xs btn-outline-secondary" id="btnClearAset">
                        <i class="fas fa-times me-1"></i> Lepas Aset
                    </button>
                </div>
                <div class="row g-2 small">
                    <div class="col-sm-6">
                        <span class="text-muted">ID Barcode Aset:</span>
                        <strong id="metaBarcode" class="d-block font-monospace text-primary">{{ $prefilledAset ? $prefilledAset->nomor_aset : '' }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted">Nama Sarana:</span>
                        <strong id="metaNamaAset" class="d-block text-dark">{{ $prefilledAset ? $prefilledAset->nama : '' }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted">Kategori Aset:</span>
                        <strong id="metaCategory" class="d-block">{{ $prefilledAset ? $prefilledAset->kategori : '' }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted">Wilayah Cakupan:</span>
                        <strong id="metaRt" class="d-block">{{ $prefilledAset ? 'RT ' . $prefilledAset->rt_number : '' }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted">Titik Koordinat GPS:</span>
                        <strong id="metaCoords" class="d-block font-monospace">{{ $prefilledAset && $prefilledAset->latitude ? $prefilledAset->latitude . ', ' . $prefilledAset->longitude : '-' }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted">Detail Lokasi Fisik:</span>
                        <strong id="metaLocation" class="d-block">{{ $prefilledAset ? $prefilledAset->lokasi : '' }}</strong>
                    </div>
                </div>
            </div>

            <!-- FORMULIR INPUT PENGADUAN -->
            <form method="POST" action="{{ route('pengaduan.store') }}" enctype="multipart/form-data">
                @csrf

                <!-- Hidden inputs for scanner values -->
                <input type="hidden" name="aset_id" id="inputAsetId" value="{{ $prefilledAset ? $prefilledAset->id : '' }}">
                <input type="hidden" name="barcode_code" id="inputBarcode" value="{{ $prefilledAset ? $prefilledAset->nomor_aset : '' }}">
                <input type="hidden" name="latitude" id="inputLat" value="{{ $prefilledAset ? $prefilledAset->latitude : '' }}">
                <input type="hidden" name="longitude" id="inputLng" value="{{ $prefilledAset ? $prefilledAset->longitude : '' }}">

                <!-- Judul Laporan -->
                <div class="mb-3">
                    <label class="form-label small fw-bold">Judul Pengaduan / Keluhan <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="inputTitle" class="form-control"
                           placeholder="Contoh: Lampu Penerangan Jalan Padam di RT 006 Depan Blok A" required>
                </div>

                <div class="row g-3 mb-3">
                    <!-- Kategori Pengaduan -->
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Kategori Sarana / Keluhan <span class="text-danger">*</span></label>
                        <select name="category" id="inputCategory" class="form-select" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="Tiang Listrik (Kelistrikan)">Tiang Listrik (Kelistrikan)</option>
                            <option value="CCTV (Keamanan)">CCTV (Keamanan)</option>
                            <option value="Hydrant Damkar (Utilitas Air)">Hydrant Damkar (Utilitas Air)</option>
                            <option value="Saluran Air (Drainase)">Saluran Air (Drainase)</option>
                            <option value="Bak Sampah (Sanitasi)">Bak Sampah (Sanitasi)</option>
                            <option value="Fasilitas Umum">Fasilitas Umum</option>
                            <option value="Infrastruktur (Jalan)">Infrastruktur (Jalan)</option>
                            <option value="Ketertiban Lingkungan">Ketertiban Lingkungan</option>
                        </select>
                    </div>

                    <!-- Tingkat Prioritas -->
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Tingkat Urgensi / Prioritas <span class="text-danger">*</span></label>
                        <select name="priority" id="inputPriority" class="form-select" required>
                            <option value="sedang" selected>Sedang (Penanganan Standar 2-3 Hari)</option>
                            <option value="rendah">Rendah (Non-Mendesak)</option>
                            <option value="tinggi">Tinggi (Mengganggu Aktivitas Warga)</option>
                            <option value="darurat">Darurat / Emergency (Segera 24 Jam: Banjir/Korsleting/Bocor)</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <!-- Wilayah RT -->
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Wilayah RT Terkait <span class="text-danger">*</span></label>
                        <select name="rt_number" id="inputRt" class="form-select" required>
                            @foreach(['006', '007', '008', '009', '010'] as $rt)
                                <option value="{{ $rt }}" {{ ($prefilledAset && $prefilledAset->rt_number === $rt) || ($user->rt_number === $rt) ? 'selected' : '' }}>RT {{ $rt }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Detail Lokasi Kejadian -->
                    <div class="col-md-8">
                        <label class="form-label small fw-bold">Patokan Alamat / Lokasi Kejadian <span class="text-danger">*</span></label>
                        <input type="text" name="location_details" id="inputLocation" class="form-control"
                               placeholder="Contoh: Jl. Sakura Depan Blok A No. 12 RT 006"
                               value="{{ $prefilledAset ? $prefilledAset->lokasi : '' }}" required>
                    </div>
                </div>

                <!-- Kronologi / Deskripsi Laporan -->
                <div class="mb-3">
                    <label class="form-label small fw-bold">Kronologi / Rincian Kerusakan <span class="text-danger">*</span></label>
                    <textarea name="content" id="inputContent" class="form-control" rows="4"
                              placeholder="Jelaskan secara rinci kondisi masalah, perkiraan waktu kejadian, dan dampak bagi lingkungan sekitar..."
                              required></textarea>
                </div>

                <!-- Unggah Foto Lampiran -->
                <div class="mb-4">
                    <label class="form-label small fw-bold">Unggah Foto Bukti Kejadian (Opsional)</label>
                    <input type="file" name="foto_lampiran" id="inputLampiranFoto" class="form-control" accept="image/*" capture="environment">
                    <small class="text-muted" style="font-size: 0.75rem;">Mendukung kamera HP langsung atau unggah dari perangkat (Maks. 5MB).</small>

                    <div id="fotoPreviewBox" class="mt-2 text-center" style="display: none;">
                        <img id="previewImg" src="" alt="Preview Bukti" class="img-fluid rounded-3 border" style="max-height: 180px;">
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                    <a href="{{ route('pengaduan.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                    <button type="submit" class="btn btn-primary px-4 py-2 fw-bold">
                        <i class="fas fa-paper-plane me-1"></i> Kirim Laporan Pengaduan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==================== MODAL SCANNER QR CODE & PILIH ASET ==================== -->
<div class="modal fade" id="scanModal" tabindex="-1" aria-labelledby="scanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold" id="scanModalLabel" style="color: var(--nx-forest);">
                    <i class="fas fa-qrcode me-2"></i>Pindai QR Code atau Pilih Sarana Lingkungan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Real-time HTML5 Camera Viewfinder -->
                <div class="p-3 rounded-4 mb-3" style="background: #1B3B2B; color: #FFFFFF;">
                    <div id="modalQrReader" style="width: 100%; border-radius: 12px; overflow: hidden;"></div>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <button type="button" id="btnStartModalCamera" class="btn btn-sm btn-success">
                            <i class="fas fa-camera me-1"></i> Aktifkan Kamera HP
                        </button>
                        <button type="button" id="btnStopModalCamera" class="btn btn-sm btn-outline-light" style="display: none;">
                            <i class="fas fa-stop me-1"></i> Matikan Kamera
                        </button>
                        <span id="modalScanStatus" class="small text-muted">Kamera siap diaktifkan</span>
                    </div>
                </div>

                <!-- Manual Input / Quick Match -->
                <div class="p-3 rounded-3 mb-3 border bg-light">
                    <label class="form-label small fw-bold text-muted mb-1">Ketik ID Barcode Aset:</label>
                    <div class="input-group input-group-sm">
                        <input type="text" id="modalInputCode" class="form-control font-monospace" placeholder="Contoh: TL-018-006-012">
                        <button type="button" id="btnModalCheckCode" class="btn btn-primary px-3">
                            <i class="fas fa-search me-1"></i> Pilih Aset
                        </button>
                    </div>
                </div>

                <h6 class="fw-bold mb-2 small text-uppercase text-muted"><i class="fas fa-list me-1"></i>Atau Pilih Langsung dari Sarana Tersedia di RW 018:</h6>
                <div class="list-group rounded-3 overflow-hidden border" style="max-height: 240px; overflow-y: auto;">
                    @foreach($allAsets as $as)
                        <button type="button" class="list-group-item list-group-item-action p-2.5 d-flex align-items-center justify-content-between btn-select-modal-aset"
                            data-id="{{ $as->id }}"
                            data-barcode="{{ $as->nomor_aset }}"
                            data-nama="{{ $as->nama }}"
                            data-category="{{ $as->kategori }}"
                            data-rt="{{ $as->rt_number }}"
                            data-location="{{ $as->lokasi }}"
                            data-lat="{{ $as->latitude }}"
                            data-lng="{{ $as->longitude }}">
                            <div>
                                <strong class="d-block small text-dark">[{{ $as->nomor_aset }}] {{ $as->nama }}</strong>
                                <span class="text-muted" style="font-size: 0.72rem;">RT {{ $as->rt_number }} • {{ $as->lokasi }}</span>
                            </div>
                            <span class="badge rounded-pill px-2 py-1 {{ $as->kondisi === 'baik' ? 'bg-success' : 'bg-warning text-dark' }}" style="font-size: 0.68rem;">
                                {{ ucfirst($as->kondisi) }}
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Preview Lampiran Foto
    const inputLampiran = document.getElementById('inputLampiranFoto');
    if (inputLampiran) {
        inputLampiran.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImg').src = e.target.result;
                    document.getElementById('fotoPreviewBox').style.display = 'block';
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    // 2. Pasang Data Aset ke Form
    function applyAsetData(id, barcode, nama, category, rt, location, lat, lng) {
        document.getElementById('inputAsetId').value = id || '';
        document.getElementById('inputBarcode').value = barcode || '';
        document.getElementById('inputLat').value = lat || '';
        document.getElementById('inputLng').value = lng || '';
        document.getElementById('inputLocation').value = location || '';

        // Tampilkan Container Info Aset
        document.getElementById('metaBarcode').textContent = barcode;
        document.getElementById('metaNamaAset').textContent = nama;
        document.getElementById('metaCategory').textContent = category;
        document.getElementById('metaRt').textContent = 'RT ' + rt;
        document.getElementById('metaCoords').textContent = (lat && lng) ? (lat + ', ' + lng) : '-';
        document.getElementById('metaLocation').textContent = location;
        document.getElementById('scanMetadataContainer').style.display = 'block';

        // Auto set dropdown RT
        const rtSelect = document.getElementById('inputRt');
        if (rtSelect && rt) {
            rtSelect.value = rt;
        }

        // Auto pilih kategori yang sesuai
        const catSelect = document.getElementById('inputCategory');
        if (catSelect && category) {
            for (let i = 0; i < catSelect.options.length; i++) {
                if (category.toLowerCase().includes('listrik') && catSelect.options[i].value.includes('Kelistrikan')) {
                    catSelect.selectedIndex = i; break;
                } else if (category.toLowerCase().includes('cctv') && catSelect.options[i].value.includes('CCTV')) {
                    catSelect.selectedIndex = i; break;
                } else if (category.toLowerCase().includes('hydrant') && catSelect.options[i].value.includes('Hydrant')) {
                    catSelect.selectedIndex = i; break;
                } else if (category.toLowerCase().includes('sampah') && catSelect.options[i].value.includes('Sampah')) {
                    catSelect.selectedIndex = i; break;
                } else if (category.toLowerCase().includes('drainase') && catSelect.options[i].value.includes('Drainase')) {
                    catSelect.selectedIndex = i; break;
                }
            }
        }

        // Tutup Modal
        const modalEl = document.getElementById('scanModal');
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance) {
            modalInstance.hide();
        }
    }

    // Klik tombol pilih aset di list modal
    document.querySelectorAll('.btn-select-modal-aset').forEach(btn => {
        btn.addEventListener('click', function() {
            applyAsetData(
                this.getAttribute('data-id'),
                this.getAttribute('data-barcode'),
                this.getAttribute('data-nama'),
                this.getAttribute('data-category'),
                this.getAttribute('data-rt'),
                this.getAttribute('data-location'),
                this.getAttribute('data-lat'),
                this.getAttribute('data-lng')
            );
        });
    });

    // Lepas Aset
    const btnClearAset = document.getElementById('btnClearAset');
    if (btnClearAset) {
        btnClearAset.addEventListener('click', function() {
            document.getElementById('inputAsetId').value = '';
            document.getElementById('inputBarcode').value = '';
            document.getElementById('inputLat').value = '';
            document.getElementById('inputLng').value = '';
            document.getElementById('scanMetadataContainer').style.display = 'none';
        });
    }

    // Tombol Cek Manual di Modal
    const btnModalCheck = document.getElementById('btnModalCheckCode');
    if (btnModalCheck) {
        btnModalCheck.addEventListener('click', function() {
            const code = document.getElementById('modalInputCode').value.trim();
            if (!code) return;
            fetch(`/aset/find-by-code/${encodeURIComponent(code)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.aset) {
                        const as = data.aset;
                        applyAsetData(as.id, as.nomor_aset, as.nama, as.kategori, as.rt_number, as.lokasi, as.latitude, as.longitude);
                    } else {
                        alert(data.message || 'Aset tidak ditemukan.');
                    }
                })
                .catch(e => alert('Aset tidak ditemukan.'));
        });
    }

    // Real Camera Scanner di Modal
    let modalQrScanner = null;
    const btnStartCamera = document.getElementById('btnStartModalCamera');
    const btnStopCamera = document.getElementById('btnStopModalCamera');
    const statusText = document.getElementById('modalScanStatus');

    if (btnStartCamera && typeof Html5Qrcode !== 'undefined') {
        btnStartCamera.addEventListener('click', function() {
            modalQrScanner = new Html5Qrcode("modalQrReader");
            statusText.textContent = "Mengakses kamera...";

            modalQrScanner.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: { width: 220, height: 220 } },
                (decodedText) => {
                    statusText.textContent = "Terdeteksi: " + decodedText;
                    fetch(`/aset/find-by-code/${encodeURIComponent(decodedText)}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.success && data.aset) {
                                const as = data.aset;
                                applyAsetData(as.id, as.nomor_aset, as.nama, as.kategori, as.rt_number, as.lokasi, as.latitude, as.longitude);
                            }
                        });
                    modalQrScanner.stop();
                    btnStartCamera.style.display = 'inline-block';
                    btnStopCamera.style.display = 'none';
                },
                () => {}
            ).then(() => {
                btnStartCamera.style.display = 'none';
                btnStopCamera.style.display = 'inline-block';
                statusText.textContent = "Arahkan kamera ke QR Code Aset";
            }).catch(err => {
                statusText.textContent = "Kamera tidak dapat diakses.";
            });
        });

        if (btnStopCamera) {
            btnStopCamera.addEventListener('click', function() {
                if (modalQrScanner) {
                    modalQrScanner.stop().then(() => {
                        btnStartCamera.style.display = 'inline-block';
                        btnStopCamera.style.display = 'none';
                        statusText.textContent = "Kamera dihentikan";
                    });
                }
            });
        }
    }
});
</script>
@endpush
