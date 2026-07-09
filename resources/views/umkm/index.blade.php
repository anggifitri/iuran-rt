@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h4 class="fw-bold">Direktori UMKM</h4>
    <p class="text-muted">Daftar usaha warga di lingkungan RT.</p>

    @if($usahas->isEmpty())
        <div class="card p-4 text-center text-muted">
            Belum ada usaha UMKM.
        </div>
    @else
        <div class="row g-3">
            @foreach($usahas as $usaha)
                <div class="col-md-6">
                    <div class="card p-3">
                        <h5>{{ $usaha->nama }}</h5>
                        <p class="mb-1 text-muted">{{ $usaha->kategori }} &middot; {{ $usaha->telepon }}</p>
                        <p class="mb-0">{{ $usaha->alamat }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
