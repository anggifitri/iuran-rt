@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h4 class="fw-bold">Tambah UMKM</h4>
    <form method="POST" action="{{ route('umkm.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Nama Usaha</label>
            <input type="text" name="nama" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Pemilik</label>
            <input type="text" name="pemilik" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Kategori</label>
            <input type="text" name="kategori" class="form-control">
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
