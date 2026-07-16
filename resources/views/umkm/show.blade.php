@if(request('embed'))
    <div class="card border-0 shadow-sm position-relative overflow-hidden rounded-4">
        @php
            $imgSrc = app(\App\Http\Controllers\UmkmController::class)->resolveCoverImage($usaha->cover_image ?? null, $usaha->kategori ?? null);
        @endphp
        <div style="height: 240px; background: url('{{ $imgSrc }}') center/cover no-repeat;">
            <button type="button" class="btn btn-light rounded-circle position-absolute top-square" style="top: 15px; right: 15px; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; z-index: 10;">
                <i class="fas fa-times text-dark"></i>
            </button>
        </div>
        <div class="card-body p-4">
            <span class="badge bg-primary mb-2">{{ $usaha->kategori ?? 'Umum' }}</span>
            <h3 class="fw-bold text-dark mb-3">{{ $usaha->nama }}</h3>
            <p class="text-muted mb-4">{{ $usaha->deskripsi ?? 'Deskripsi belum tersedia.' }}</p>
            <hr>
            <div class="row g-3 text-secondary">
                <div class="col-md-6">
                    <p class="mb-2"><i class="fas fa-user me-2 text-primary"></i><strong>Pemilik:</strong> {{ $usaha->pemilik ?? '-' }}</p>
                    <p class="mb-0"><i class="fas fa-phone me-2 text-success"></i><strong>Telepon:</strong> {{ $usaha->telepon ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-0"><i class="fas fa-map-marker-alt me-2 text-danger"></i><strong>Alamat:</strong> {{ $usaha->alamat ?? '-' }}</p>
                </div>
            </div>
            <div class="mt-4 text-end">
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $usaha->telepon) }}" target="_blank" class="btn btn-success rounded-pill px-4">
                    <i class="fab fa-whatsapp me-2"></i>Hubungi Warga
                </a>
            </div>
        </div>
    </div>
@else
    @extends('layouts.app')
    @section('content')
    <div class="container py-4">
        <div class="mb-3">
            <a href="{{ route('umkm.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Direktori
            </a>
        </div>
        <div class="card border-0 shadow-sm overflow-hidden rounded-4">
            @php
                $imgSrc = app(\App\Http\Controllers\UmkmController::class)->resolveCoverImage($usaha->cover_image ?? null, $usaha->kategori ?? null);
            @endphp
            <div style="height: 350px; background: url('{{ $imgSrc }}') center/cover no-repeat;"></div>
            <div class="card-body p-4">
                <span class="badge bg-primary mb-2">{{ $usaha->kategori ?? 'Umum' }}</span>
                <h2 class="fw-bold text-dark mb-3">{{ $usaha->nama }}</h2>
                <p class="text-muted fs-5 mb-4">{{ $usaha->deskripsi ?? 'Deskripsi belum tersedia.' }}</p>
                <hr>
                <div class="row g-3 text-secondary fs-6">
                    <div class="col-md-6">
                        <p class="mb-2"><i class="fas fa-user me-2 text-primary"></i><strong>Pemilik:</strong> {{ $usaha->pemilik ?? '-' }}</p>
                        <p class="mb-0"><i class="fas fa-phone me-2 text-success"></i><strong>Telepon:</strong> {{ $usaha->telepon ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-0"><i class="fas fa-map-marker-alt me-2 text-danger"></i><strong>Alamat:</strong> {{ $usaha->alamat ?? '-' }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $usaha->telepon) }}" target="_blank" class="btn btn-success rounded-pill px-4 btn-lg">
                        <i class="fab fa-whatsapp me-2"></i>Hubungi Via WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endsection
@endif
