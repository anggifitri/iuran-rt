@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card p-4 shadow-sm border-0" style="border-radius: 20px; background-color: var(--bg-card); border: 1px solid var(--border-color);">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold mb-0 text-dark" style="color: var(--text-main) !important;">
                        <i class="fas fa-store-alt text-primary me-2"></i>Tambah UMKM Warga
                    </h4>
                    <a href="{{ route('umkm.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 8px;">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>

                <form method="POST" action="{{ route('umkm.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Nama Usaha / Toko</label>
                        <input id="namaUsahaInput" type="text" name="nama" class="form-control form-control-lg" placeholder="Contoh: Warung Sejahtera Mandiri" required style="border-radius: 10px;">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-secondary">Nama Pemilik</label>
                            <input type="text" name="pemilik" class="form-control" placeholder="Contoh: Budi Santoso" style="border-radius: 10px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-secondary">RT (Rukun Tetangga)</label>
                            <select name="rt_number" class="form-select" required style="border-radius: 10px;">
                                <option value="006" {{ (Auth::user()->rt_number ?? '') === '006' ? 'selected' : '' }}>RT 006</option>
                                <option value="007" {{ (Auth::user()->rt_number ?? '') === '007' ? 'selected' : '' }}>RT 007</option>
                                <option value="008" {{ (Auth::user()->rt_number ?? '') === '008' ? 'selected' : '' }}>RT 008</option>
                                <option value="009" {{ (Auth::user()->rt_number ?? '') === '009' ? 'selected' : '' }}>RT 009</option>
                                <option value="010" {{ (Auth::user()->rt_number ?? '') === '010' ? 'selected' : '' }}>RT 010</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Kategori Usaha</label>
                        <select id="kategoriSelect" name="kategori" class="form-select" required disabled style="border-radius: 10px;">
                            <option value="">Isi nama usaha terlebih dahulu untuk memuat kategori...</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-secondary">No. Telepon / WhatsApp</label>
                            <input type="text" name="telepon" class="form-control" placeholder="Contoh: 081234567890" style="border-radius: 10px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-secondary">Alamat Usaha</label>
                            <input type="text" name="alamat" class="form-control" placeholder="Contoh: Jl. Sakura No. 15, RT 006 RW 018" style="border-radius: 10px;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Deskripsi Singkat Usaha</label>
                        <textarea name="deskripsi" class="form-control" rows="4" placeholder="Jelaskan produk unggulan atau layanan yang Anda sediakan..." style="border-radius: 10px;"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Foto Cover Toko / Usaha</label>
                        <input type="file" name="cover_image" id="coverImageInput" class="form-control" accept="image/*" style="border-radius: 10px;">
                        <small class="text-muted d-block mt-1">Format: JPG, JPEG, PNG, GIF. Maksimal 2MB. Jika dikosongkan, gambar cover estetik otomatis dari Unsplash akan dipasangkan sesuai kategori pilihan Anda.</small>
                        
                        <!-- Image Preview Container -->
                        <div id="imagePreviewContainer" class="mt-3 text-center" style="display: none;">
                            <span class="d-block text-secondary mb-2" style="font-size: 0.8rem;"><i class="fas fa-image me-1"></i>Pratinjau Foto Cover:</span>
                            <div class="d-inline-block rounded-3 overflow-hidden shadow-sm border" style="max-width: 100%; max-height: 250px;">
                                <img id="imagePreview" src="#" alt="Preview Cover" style="max-height: 250px; width: auto; object-fit: cover;">
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm" style="border-radius: 12px; font-weight: 600;">
                            <i class="fas fa-save me-1"></i>Simpan & Daftarkan Usaha
                        </button>
                    </div>
                </form>
            </div>
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
        if (categoryLoaded) {
            return;
        }

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
            categorySelect.innerHTML = '<option value="">Gagal memuat kategori</option>';
            categorySelect.disabled = true;
            console.error(error);
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

    // Image Upload Preview Logic
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
