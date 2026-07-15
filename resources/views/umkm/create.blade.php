@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h4 class="fw-bold">Tambah UMKM</h4>
    <form method="POST" action="{{ route('umkm.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Nama Usaha</label>
            <input id="namaUsahaInput" type="text" name="nama" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Pemilik</label>
            <input type="text" name="pemilik" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Kategori</label>
            <select id="kategoriSelect" name="kategori" class="form-select" required disabled>
                <option value="">Isi nama usaha terlebih dahulu untuk memuat kategori...</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Telepon</label>
            <input type="text" name="telepon" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Alamat</label>
            <input type="text" name="alamat" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="4"></textarea>
        </div>
        <button class="btn btn-primary">Simpan</button>
    </form>
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

            categorySelect.innerHTML = '<option value="">Pilih kategori</option>';
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
</script>
@endpush
