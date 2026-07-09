@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h4 class="fw-bold">Info Posyandu</h4>
    <p class="text-muted">Jadwal posyandu dan informasi terkait.</p>

    @if($jadwal->isEmpty())
        <div class="card p-4 text-center text-muted">
            Belum ada jadwal posyandu.
        </div>
    @else
        <div class="row g-3">
            @foreach($jadwal as $item)
                <div class="col-md-6">
                    <div class="card p-3">
                        <h5>{{ $item->nama }}</h5>
                        <p class="mb-1 text-muted">{{ $item->tanggal->format('d M Y') }} &middot; {{ $item->lokasi }}</p>
                        <p class="mb-0">{{ $item->keterangan }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
