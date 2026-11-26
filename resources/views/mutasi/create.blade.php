@extends('layouts.app')

@section('title', 'Catat Mutasi Warga — NexaNest')

@section('content')
<div class="nx-page-header">
    <div>
        <a href="{{ route('mutasi.index') }}" class="btn btn-sm btn-outline-secondary mb-2" style="border-radius: 8px;">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Riwayat Mutasi
        </a>
        <h1 class="nx-page-title">Pencatatan Mutasi Kependudukan</h1>
        <p class="nx-page-subtitle">
            Catat perpindahan warga yang pindah masuk ke lingkungan RW 018 atau pindah keluar wilayah secara terstruktur.
        </p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card p-4">
            <form action="{{ route('mutasi.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label class="form-label small fw-bold">Jenis Mutasi <span class="text-danger">*</span></label>
                    <div class="d-flex gap-3">
                        <div class="form-check card p-3 flex-fill border cursor-pointer" id="cardMasuk">
                            <input class="form-check-input" type="radio" name="tipe" id="tipeMasuk" value="masuk" checked onchange="toggleTipe()">
                            <label class="form-check-label fw-bold d-block text-dark ms-2" for="tipeMasuk">
                                <i class="fas fa-arrow-right-to-bracket text-success me-1"></i> Pindah Masuk (Warga Baru)
                                <small class="d-block text-muted fw-normal" style="font-size: 0.72rem;">Warga baru datang dan menetap di lingkungan RT/RW 018.</small>
                            </label>
                        </div>
                        <div class="form-check card p-3 flex-fill border cursor-pointer" id="cardKeluar">
                            <input class="form-check-input" type="radio" name="tipe" id="tipeKeluar" value="keluar" onchange="toggleTipe()">
                            <label class="form-check-label fw-bold d-block text-dark ms-2" for="tipeKeluar">
                                <i class="fas fa-arrow-right-from-bracket text-danger me-1"></i> Pindah Keluar
                                <small class="d-block text-muted fw-normal" style="font-size: 0.72rem;">Warga lama pindah domisili keluar dari lingkungan RW 018.</small>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- SELECT WARGA (IF MUTASI KELUAR) -->
                <div class="mb-3 d-none" id="wrapSelectWarga">
                    <label class="form-label small fw-bold">Pilih Warga Yang Pindah Keluar <span class="text-danger">*</span></label>
                    <select name="warga_id" id="wargaSelect" class="form-select" onchange="fillWargaData()">
                        <option value="">-- Pilih Nama Warga --</option>
                        @foreach($wargas as $w)
                            <option value="{{ $w->id }}" data-nama="{{ $w->nama }}" data-nik="{{ $w->nik }}" data-rt="{{ $w->rt_number }}" data-alamat="{{ $w->alamat }}">
                                {{ $w->nama }} (RT {{ $w->rt_number }} - Blok {{ $w->blok_rumah ?: '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Nama Lengkap Warga <span class="text-danger">*</span></label>
                        <input type="text" name="nama_warga" id="namaWargaInput" class="form-control" required placeholder="Contoh: Budi Santoso">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">NIK (Nomor Induk Kependudukan)</label>
                        <input type="text" name="nik" id="nikInput" class="form-control" placeholder="16 digit NIK...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Tanggal Resmi Mutasi <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_mutasi" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-6" id="wrapRtTujuan">
                        <label class="form-label small fw-bold">Wilayah RT Tujuan di RW 018 <span class="text-danger">*</span></label>
                        <select name="rt_tujuan" class="form-select">
                            @foreach(['006', '007', '008', '009', '010'] as $rt)
                                <option value="{{ $rt }}">RT {{ $rt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 d-none" id="wrapRtAsal">
                        <label class="form-label small fw-bold">RT Asal (RW 018)</label>
                        <input type="text" name="rt_asal" id="rtAsalInput" class="form-control" readonly>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6" id="wrapAlamatAsal">
                        <label class="form-label small fw-bold">Alamat Asal Sebelumnya</label>
                        <textarea name="alamat_asal" rows="2" class="form-control" placeholder="Kota/Kabupaten, Kecamatan, atau kelurahan asal..."></textarea>
                    </div>
                    <div class="col-md-6" id="wrapAlamatTujuan">
                        <label class="form-label small fw-bold">Alamat Baru di Lingkungan RW 018</label>
                        <textarea name="alamat_tujuan" rows="2" class="form-control" placeholder="Blok rumah, nomor jalan RT tujuan..."></textarea>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Alasan Kepindahan / Mutasi <span class="text-danger">*</span></label>
                    <textarea name="alasan" rows="2" class="form-control" required placeholder="Contoh: Pembelian rumah baru, mutasi dinas pekerjaan, urusan keluarga..."></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold">Upload Surat Keterangan Pindah / SKPWNI / Dokumen Pendukung (Opsional)</label>
                    <input type="file" name="dokumen_pendukung" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                    <small class="text-muted" style="font-size: 0.72rem;">Format: PDF, JPG, PNG (Maks. 4MB)</small>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('mutasi.index') }}" class="btn btn-secondary px-4">Batal</a>
                    <button type="submit" class="btn btn-primary px-4" style="border-radius: 12px; font-weight: 700;">
                        <i class="fas fa-floppy-disk me-1"></i> Simpan Catatan Mutasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleTipe() {
    let isKeluar = document.getElementById('tipeKeluar').checked;
    let wrapSelectWarga = document.getElementById('wrapSelectWarga');
    let wrapRtTujuan = document.getElementById('wrapRtTujuan');
    let wrapRtAsal = document.getElementById('wrapRtAsal');
    let wrapAlamatAsal = document.getElementById('wrapAlamatAsal');
    let wrapAlamatTujuan = document.getElementById('wrapAlamatTujuan');
    
    if (isKeluar) {
        wrapSelectWarga.classList.remove('d-none');
        wrapRtAsal.classList.remove('d-none');
        wrapRtTujuan.classList.add('d-none');
        document.getElementById('cardKeluar').classList.add('bg-light');
        document.getElementById('cardMasuk').classList.remove('bg-light');
    } else {
        wrapSelectWarga.classList.add('d-none');
        wrapRtAsal.classList.add('d-none');
        wrapRtTujuan.classList.remove('d-none');
        document.getElementById('cardMasuk').classList.add('bg-light');
        document.getElementById('cardKeluar').classList.remove('bg-light');
    }
}

function fillWargaData() {
    let select = document.getElementById('wargaSelect');
    let opt = select.options[select.selectedIndex];
    if (opt.value) {
        document.getElementById('namaWargaInput').value = opt.getAttribute('data-nama') || '';
        document.getElementById('nikInput').value = opt.getAttribute('data-nik') || '';
        document.getElementById('rtAsalInput').value = opt.getAttribute('data-rt') || '';
    }
}
</script>
@endsection
