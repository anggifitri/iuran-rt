@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h4 class="fw-bold">Pengaduan Warga</h4>
    <p class="text-muted">Laporkan masalah lingkungan atau administrasi ke RT.</p>
    <a href="{{ route('pengaduan.create') }}" class="btn btn-primary mb-3">Buat Pengaduan</a>

    <div class="card p-3">
        <p class="mb-0 text-muted">Belum ada pengaduan yang ditampilkan (placeholder).</p>
    </div>
</div>
@endsection
