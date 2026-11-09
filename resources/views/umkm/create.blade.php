@extends('layouts.app')

@section('title', 'Daftarkan UMKM Warga — NexaNest')

@section('content')
<div class="nx-page-header">
    <div>
        <span class="badge rounded-pill mb-2 px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
            <i class="fas fa-shop me-1"></i> Pendaftaran Mitra UMKM
        </span>
        <h1 class="nx-page-title">Tambah Usaha UMKM Warga</h1>
        <p class="nx-page-subtitle">Promosikan produk kuliner, kerajinan, atau layanan jasa Anda kepada seluruh warga RW 018.</p>
    </div>
    <a href="{{ route('umkm.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Kembali ke Direktori
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card p-4">
            <form method="POST" action="{{ route('umkm.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Nama Usaha / Toko <span class="text-danger">*</span></label>
                    <input id="namaUsahaInput" type="text" name="nama" value="{{ old('nama') }}" class="form-control"
                           placeholder="Contoh: Dapur Sehat Bu Rina" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Pemilik Usaha</label>
                        <input type="text" name="pemilik" value="{{ old('pemilik', Auth::user()->name ?? '') }}" class="form-control"
                               placeholder="Contoh: Budi Santoso">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Wilayah RT <span class="text-danger">*</span></label>
                        <select name="rt_number" class="form-select" required>
                            @foreach(['001','002','003','004','005','006','007','008','009','010'] as $rtOpt)
                                <option value="{{ $rtOpt }}" {{ (Auth::user()->rt_number ?? '') === $rtOpt ? 'selected' : '' }}>RT {{ $rtOpt }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kategori Usaha <span class="text-danger">*</span></label>
                    <select id="kategoriSelect" name="kategori" class="form-select" required disabled>
                        <option value="">Isi nama usaha terlebih dahulu untuk memuat kategori...</option>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nomor Telepon / WhatsApp Order</label>
                        <input type="text" name="telepon" value="{{ old('telepon') }}" class="form-control" placeholder="Contoh: 081234567890">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Alamat / Blok Lokasi Usaha</label>
                        <input type="text" name="alamat" value="{{ old('alamat') }}" class="form-control" placeholder="Contoh: Jl. Sakura Blok A No. 15">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi Produk / Layanan Unggulan</label>
                    <textarea name="deskripsi" class="form-control" rows="4"
                              placeholder="Jelaskan produk unggulan, jam buka, atau layanan pesan antar yang Anda sediakan...">{{ old('deskripsi') }}</textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label">Foto Cover Toko / Produk (Opsional)</label>
                    <input type="file" name="cover_image" id="coverImageInput" class="form-control" accept="image/*">
                    <small class="text-muted d-block mt-1">
                        Format JPG, PNG, atau WEBP (Maks. 2MB). Jika dikosongkan, sistem otomatis memasangkan ilustrasi sesuai kategori usaha.
                    </small>

                    <div id="imagePreviewContainer" class="mt-3 text-center" style="display: none;">
                        <span class="d-block text-muted small mb-2"><i class="fas fa-image me-1"></i>Pratinjau Foto Cover:</span>
                        <div class="d-inline-block rounded-4 overflow-hidden shadow-sm border" style="max-width: 100%; max-height: 240px;">
                            <img id="imagePreview" src="#" alt="Preview Cover" style="max-height: 240px; width: auto; object-fit: cover;">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                    <a href="{{ route('umkm.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> Simpan &amp; Daftarkan Usaha
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const nameInput = document.getElementById('namaUsahaInput');
    const categorySelect = document.getElementById('kategoriSelect');
    let categoryLoaded = false;

    async function loadUmkmCategories() {
        if (categoryLoaded) return;
        try {
            const response = await fetch('{{ route('umkm.categories') }}');
            const categories = await response.json();

            categorySelect.innerHTML = '<option value="">Pilih Kategori Usaha</option>';
            categories.forEach(category => {
                const option = document.createElement('option');
                option.value = category;
                option.textContent = category;
                categorySelect.appendChild(option);
            });

            categorySelect.disabled = false;
            categoryLoaded = true;
        } catch (error) {
            categorySelect.innerHTML = '<option value="Kuliner & Makanan">Kuliner & Makanan</option><option value="Perdagangan">Perdagangan</option><option value="Jasa">Jasa</option><option value="Kerajinan">Kerajinan</option>';
            categorySelect.disabled = false;
        }
    }

    function handleNameInput() {
        if (nameInput.value.trim().length > 0) {
            loadUmkmCategories();
        } else {
            categorySelect.disabled = true;
            categorySelect.innerHTML = '<option value="">Isi nama usaha terlebih dahulu untuk memuat kategori...</option>';
            categoryLoaded = false;
        }
    }

    nameInput.addEventListener('input', handleNameInput);
    nameInput.addEventListener('focus', handleNameInput);

    document.addEventListener('DOMContentLoaded', function () {
        if (nameInput.value.trim().length > 0) {
            loadUmkmCategories();
        }
    });

    const coverImageInput = document.getElementById('coverImageInput');
    const imagePreviewContainer = document.getElementById('imagePreviewContainer');
    const imagePreview = document.getElementById('imagePreview');

    coverImageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreviewContainer.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            imagePreviewContainer.style.display = 'none';
        }
    });
</script>
@endpush
