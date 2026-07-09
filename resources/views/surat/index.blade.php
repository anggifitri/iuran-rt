@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h4 class="fw-bold">Penerbitan Surat</h4>
    <p class="text-muted">Ajukan permintaan surat administratif RT di sini.</p>

    <a href="{{ route('surat.create') }}" class="btn btn-primary mb-3">Buat Permintaan Surat</a>

    <div class="card p-3">
        <p class="mb-0 text-muted">Belum ada permintaan surat yang ditampilkan (placeholder).</p>
    </div>
</div>
@endsection
