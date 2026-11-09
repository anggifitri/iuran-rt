@php
    $usaha = $usaha ?? $umkm;
@endphp

@if(request('embed'))
    <div class="card border-0 position-relative overflow-hidden rounded-4">
        @php
            $imgSrc = app(\App\Http\Controllers\UmkmController::class)->resolveCoverImage($usaha->cover_image ?? null, $usaha->kategori ?? null);
        @endphp
        <div style="height: 240px; background: url('{{ $imgSrc }}') center/cover no-repeat;">
            <button type="button" class="btn btn-light rounded-circle position-absolute top-square shadow-sm"
                    style="top: 15px; right: 15px; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; z-index: 10;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="card-body p-4">
            <span class="badge rounded-pill px-3 py-1 mb-2" style="background: var(--nx-mint); color: var(--nx-forest);">
                {{ $usaha->kategori ?? 'Umum' }}
            </span>
            <h3 class="fw-bold mb-2" style="color: var(--nx-text);">{{ $usaha->nama }}</h3>
            <p class="text-muted mb-4">{{ $usaha->deskripsi ?? 'Deskripsi belum tersedia.' }}</p>
            <hr>
            <div class="row g-3 small">
                <div class="col-md-6">
                    <p class="mb-2"><i class="fas fa-user text-success me-2"></i><strong>Pemilik:</strong> {{ $usaha->pemilik ?? '-' }}</p>
                    <p class="mb-0"><i class="fas fa-phone text-success me-2"></i><strong>Telepon:</strong> {{ $usaha->telepon ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-0"><i class="fas fa-location-dot text-success me-2"></i><strong>Alamat:</strong> {{ $usaha->alamat ?? '-' }}</p>
                </div>
            </div>
            <div class="mt-4 text-end">
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $usaha->telepon) }}" target="_blank" class="btn btn-primary rounded-pill px-4">
                    <i class="fab fa-whatsapp me-2"></i>Hubungi Penjual via WhatsApp
                </a>
            </div>
        </div>
    </div>
@else
    @extends('layouts.app')
    @section('title', ($usaha->nama ?? 'Detail UMKM') . ' — NexaNest')
    @section('content')
    <div class="nx-page-header">
        <div>
            <span class="badge rounded-pill mb-2 px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
                <i class="fas fa-store me-1"></i> {{ $usaha->kategori ?? 'Mitra UMKM Warga' }}
            </span>
            <h1 class="nx-page-title">{{ $usaha->nama }}</h1>
            <p class="nx-page-subtitle">Detail informasi usaha dan kontak pemesanan langsung UMKM warga.</p>
        </div>
        <a href="{{ route('umkm.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Direktori
        </a>
    </div>

    <div class="card overflow-hidden">
        @php
            $imgSrc = app(\App\Http\Controllers\UmkmController::class)->resolveCoverImage($usaha->cover_image ?? null, $usaha->kategori ?? null);
        @endphp
        <div style="height: 320px; background: url('{{ $imgSrc }}') center/cover no-repeat;"></div>
        <div class="card-body p-4">
            <div class="d-flex gap-2 mb-2">
                <span class="badge rounded-pill px-3 py-1" style="background: var(--nx-mint); color: var(--nx-forest);">{{ $usaha->kategori ?? 'Umum' }}</span>
                <span class="badge rounded-pill px-3 py-1" style="background: var(--nx-powder); color: #2B5F8E;">RT {{ $usaha->rt_number ?? '-' }}</span>
            </div>
            <h3 class="fw-bold mb-3" style="color: var(--nx-text);">{{ $usaha->nama }}</h3>
            <p class="text-muted mb-4" style="line-height: 1.6;">{{ $usaha->deskripsi ?? 'Deskripsi belum tersedia.' }}</p>
            <hr>
            <div class="row g-3">
                <div class="col-md-6">
                    <p class="mb-2"><i class="fas fa-user text-success me-2"></i><strong>Pemilik:</strong> {{ $usaha->pemilik ?? '-' }}</p>
                    <p class="mb-0"><i class="fas fa-phone text-success me-2"></i><strong>Telepon:</strong> {{ $usaha->telepon ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-0"><i class="fas fa-location-dot text-success me-2"></i><strong>Alamat:</strong> {{ $usaha->alamat ?? '-' }}</p>
                </div>
            </div>
            <div class="mt-4 pt-3 border-top">
                <a href="{{ $whatsappUrl ?? ('https://wa.me/' . preg_replace('/[^0-9]/', '', $usaha->telepon)) }}" target="_blank" class="btn btn-primary px-4">
                    <i class="fab fa-whatsapp me-2"></i>Hubungi Via WhatsApp
                </a>
            </div>
        </div>
    </div>
    @endsection
@endif
