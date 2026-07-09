@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h4 class="fw-bold">Ajukan Penerbitan Surat</h4>
    <form method="POST" action="{{ route('surat.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Jenis Surat</label>
            <input type="text" name="jenis" class="form-control" placeholder="Contoh: Surat Keterangan" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Keterangan</label>
            <textarea name="keterangan" class="form-control" rows="4"></textarea>
        </div>
        <button class="btn btn-primary">Kirim Permintaan</button>
    </form>
</div>
@endsection
