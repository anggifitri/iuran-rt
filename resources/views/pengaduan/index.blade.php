@extends('layouts.app')

@section('title', 'Pengaduan Warga & Lingkungan — NexaNest')

@section('content')
<div class="nx-page-header">
    <div>
        <span class="badge rounded-pill mb-2 px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
            <i class="fas fa-comment-dots me-1"></i> Layanan Aspirasi &amp; Fasilitas Lingkungan
        </span>
        <h1 class="nx-page-title">Pengaduan &amp; Laporan Lingkungan</h1>
        <p class="nx-page-subtitle">Portal pelaporan keluhan warga dan kerusakan fasilitas lingkungan terintegrasi QR Code Aset.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('pengaduan.create', ['scan' => 1]) }}" class="btn btn-outline-primary">
            <i class="fas fa-qrcode me-1"></i> Scan QR Fasilitas
        </a>
        <a href="{{ route('pengaduan.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Buat Laporan Baru
        </a>
    </div>
</div>

<!-- QR ASSET SCANNER BANNER -->
<div class="card p-4 mb-4" style="background: linear-gradient(135deg, #285943 0%, #3A755B 100%) !important; border: none !important;">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-4 bg-white d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width: 54px; height: 54px; color: var(--nx-forest);">
                <i class="fas fa-qrcode fa-xl"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-1" style="color: #FFFFFF !important; font-size: 1.05rem;">Pindai QR Code pada Fasilitas &amp; Aset Lingkungan</h5>
                <p class="mb-0 small" style="color: rgba(255,255,255,0.86) !important; max-width: 600px;">
                    Setiap tiang penerangan jalan, hydrant pemadam, kamera CCTV, dan bak sampah komunal memiliki kode QR unik untuk mendeteksi titik koordinat perbaikan secara otomatis.
                </p>
            </div>
        </div>
        <a href="{{ route('pengaduan.create', ['scan' => 1]) }}" class="btn bg-white fw-bold px-4 py-2.5 rounded-3" style="color: var(--nx-forest) !important;">
            <i class="fas fa-camera me-1"></i> Simulasikan Scan Barcode
        </a>
    </div>
</div>

@if($pengaduans->isEmpty())
    <div class="card p-5 text-center">
        <div class="mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center"
             style="width: 64px; height: 64px; background: var(--nx-mint-soft); color: var(--nx-forest);">
            <i class="fas fa-shield-heart fa-2x"></i>
        </div>
        <h5 class="fw-bold mb-1">Belum Ada Laporan Pengaduan</h5>
        <p class="text-muted small mb-0">Lingkungan terpantau aman dan kondusif. Silakan buat laporan apabila menemukan kendala sarana publik.</p>
    </div>
@else
    <div class="row g-3">
        @foreach($pengaduans as $pengaduan)
            <div class="col-12">
                <div class="card p-4 card-hover">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                        <div>
                            <h5 class="fw-bold mb-1" style="color: var(--nx-text); font-size: 1.05rem;">{{ $pengaduan->title }}</h5>
                            <small class="text-muted">
                                <i class="fas fa-user-circle text-success me-1"></i>Dilaporkan oleh: <strong>{{ $pengaduan->user->name ?? 'Warga' }}</strong>
                                • <i class="far fa-calendar-alt ms-2 me-1"></i>{{ $pengaduan->created_at->translatedFormat('d F Y H:i') }}
                            </small>
                        </div>

                        @if($pengaduan->status === 'selesai')
                            <span class="badge rounded-pill px-3 py-1.5" style="background: var(--nx-mint); color: var(--nx-forest);">
                                <i class="fas fa-check-circle me-1"></i> SELESAI
                            </span>
                        @elseif($pengaduan->status === 'proses')
                            <span class="badge rounded-pill px-3 py-1.5" style="background: var(--nx-powder); color: #2B5F8E;">
                                <i class="fas fa-spinner fa-spin me-1"></i> DIPROSES
                            </span>
                        @else
                            <span class="badge rounded-pill px-3 py-1.5" style="background: var(--nx-warning-soft); color: var(--nx-warning);">
                                <i class="fas fa-clock me-1"></i> {{ strtoupper($pengaduan->status) }}
                            </span>
                        @endif
                    </div>

                    <p class="text-muted mb-3" style="line-height: 1.6; font-size: 0.92rem;">{{ $pengaduan->content }}</p>

                    @if($pengaduan->barcode_code)
                        <div class="p-3 rounded-4" style="background: var(--nx-bg); border: 1px solid var(--nx-border);">
                            <div class="row g-3 align-items-center">
                                <div class="col-md-8">
                                    <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                                        <span class="badge rounded-pill px-3 py-1" style="background: var(--nx-mint); color: var(--nx-forest);">
                                            <i class="fas fa-qrcode me-1"></i>QR Aset: {{ $pengaduan->barcode_code }}
                                        </span>
                                        <span class="badge rounded-pill px-3 py-1" style="background: var(--nx-powder); color: #2B5F8E;">
                                            <i class="fas fa-tags me-1"></i>{{ $pengaduan->category ?? 'Utilitas Lingkungan' }}
                                        </span>
                                        <span class="badge rounded-pill px-3 py-1" style="background: var(--nx-white); color: var(--nx-text); border: 1px solid var(--nx-border);">
                                            <i class="fas fa-map-pin text-danger me-1"></i>RT {{ $pengaduan->rt_number ?? '-' }}
                                        </span>
                                    </div>
                                    <p class="mb-0 small text-muted">
                                        <i class="fas fa-location-dot text-success me-1"></i><strong>Lokasi Aset:</strong> {{ $pengaduan->location_details }}
                                    </p>
                                </div>
                                @if($pengaduan->latitude && $pengaduan->longitude)
                                    <div class="col-md-4 text-md-end">
                                        <a href="https://www.google.com/maps/search/?api=1&query={{ $pengaduan->latitude }},{{ $pengaduan->longitude }}"
                                           target="_blank" class="btn btn-sm btn-outline-primary px-3 py-2">
                                            <i class="fas fa-map-location-dot me-1"></i> Buka Titik Maps
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $pengaduans->links() }}
    </div>
@endif
@endsection
