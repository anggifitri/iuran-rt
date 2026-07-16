@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h4 class="fw-bold">Input Data Posyandu</h4>
    <form method="POST" action="{{ route('posyandu.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Tipe Data</label>
            <select name="type" class="form-select" required>
                <option value="anak">Anak</option>
                <option value="bumil">Ibu Hamil</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="nama" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Tanggal</label>
            <input type="date" name="tanggal" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Keterangan</label>
            <textarea name="keterangan" class="form-control" rows="4"></textarea>
        </div>
        <button class="btn btn-primary">Simpan</button>
    </form>
</div>
@endsection
