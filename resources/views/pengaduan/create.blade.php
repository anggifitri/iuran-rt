@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h4 class="fw-bold">Buat Pengaduan</h4>
    <form method="POST" action="{{ route('pengaduan.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Judul</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Isi Pengaduan</label>
            <textarea name="content" class="form-control" rows="5" required></textarea>
        </div>
        <button class="btn btn-primary">Kirim</button>
    </form>
</div>
@endsection
