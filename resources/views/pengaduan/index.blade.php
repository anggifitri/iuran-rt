@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark" style="color: var(--text-main) !important;">Pengaduan & Laporan Warga</h2>
            <p class="text-muted mb-0">Portal pelaporan kerusakan fasilitas lingkungan terintegrasi QR Barcode Asset.</p>
        </div>
        <a href="{{ route('pengaduan.create') }}" class="btn btn-primary btn-lg shadow-sm" style="border-radius: 12px;"><i class="fas fa-plus me-1"></i>Buat Laporan</a>
    </div>

    <!-- SCANNER SIMULATOR BANNER -->
    <div class="card p-3 mb-4 border-0 shadow-sm text-white" style="background: linear-gradient(135deg, #10b981, #059669); border-radius: 16px;">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle p-2 bg-white text-success d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="fas fa-qrcode" style="font-size: 1.5rem;"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1">Pindai QR Code di Lokasi Fasilitas / Aset</h6>
                    <p class="mb-0 text-white-50 small" style="max-width: 520px; line-height: 1.4;">Setiap tiang listrik, hydrant pemadam, tiang wifi/CCTV, dan bak sampah memiliki kode QR unik untuk mempermudah deteksi lokasi perbaikan teknisi.</p>
                </div>
            </div>
            <a href="{{ route('pengaduan.create') }}?scan=1" class="btn btn-light text-success fw-bold px-4 py-2.5 shadow-sm" style="border-radius: 10px;">Simulasikan Scan Barcode</a>
        </div>
    </div>

    @if($pengaduans->isEmpty())
        <div class="card p-5 text-center text-muted border-0 shadow-sm rounded-4" style="background-color: var(--bg-card); border: 1px solid var(--border-color);">
            <i class="fas fa-clipboard-list fa-3x mb-3 text-secondary" style="opacity: 0.6;"></i>
            <h5 class="mb-2 fw-semibold">Belum Ada Pengaduan</h5>
            <p class="mb-0">Lingkungan terpantau aman dan kondusif. Silakan laporkan jika Anda menemui kendala pada sarana publik.</p>
        </div>
    @else
        <div class="row g-3">
            @foreach($pengaduans as $pengaduan)
                @php
                    $statusClass = 'bg-warning-subtle text-warning border-warning';
                    if ($pengaduan->status === 'proses') $statusClass = 'bg-info-subtle text-info border-info';
                    if ($pengaduan->status === 'selesai') $statusClass = 'bg-success-subtle text-success border-success';
                @endphp
                <div class="col-12">
                    <div class="card p-4 shadow-sm border-0 complaint-card" style="border-radius: 16px; background-color: var(--bg-card); border: 1px solid var(--border-color);">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                            <div>
                                <h5 class="fw-bold mb-1 text-dark" style="color: var(--text-main) !important;">{{ $pengaduan->title }}</h5>
                                <small class="text-muted">
                                    <i class="fas fa-user-circle me-1"></i>Dilaporkan oleh: <strong>{{ $pengaduan->user->name ?? 'Warga' }}</strong> 
                                    • <i class="fas fa-calendar-alt ms-2 me-1"></i>{{ $pengaduan->created_at->translatedFormat('d F Y H:i') }}
                                </small>
                            </div>
                            <span class="badge rounded-pill border px-3 py-1.5 {{ $statusClass }}" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                <i class="fas {{ $pengaduan->status === 'selesai' ? 'fa-check-circle' : ($pengaduan->status === 'proses' ? 'fa-spinner fa-spin' : 'fa-clock') }} me-1"></i>
                                {{ strtoupper($pengaduan->status) }}
                            </span>
                        </div>

                        <p class="text-secondary mb-3" style="line-height: 1.5; font-size: 0.95rem;">{{ $pengaduan->content }}</p>

                        @if($pengaduan->barcode_code)
                            <div class="p-3 rounded border" style="background: rgba(59,130,246,0.02); border-color: var(--border-color) !important;">
                                <div class="row g-3 align-items-center">
                                    <div class="col-md-8">
                                        <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                                            <span class="badge bg-dark-subtle text-dark border px-2 py-1.5" style="font-size: 0.75rem; border-radius: 8px;">
                                                <i class="fas fa-qrcode me-1 text-primary"></i>QR Aset: {{ $pengaduan->barcode_code }}
                                            </span>
                                            <span class="badge bg-primary-subtle text-primary border px-2 py-1.5" style="font-size: 0.75rem; border-radius: 8px;">
                                                <i class="fas fa-tags me-1"></i>Kategori: {{ $pengaduan->category ?? 'Utilitas' }}
                                            </span>
                                            <span class="badge bg-secondary-subtle text-secondary border px-2 py-1.5" style="font-size: 0.75rem; border-radius: 8px;">
                                                <i class="fas fa-map-marker-alt me-1 text-danger"></i>Lingkup: RT {{ $pengaduan->rt_number ?? '-' }}
                                            </span>
                                        </div>
                                        <p class="mb-0 text-secondary small" style="line-height: 1.4;"><i class="fas fa-map-marked-alt text-danger me-1"></i><strong>Detail Lokasi:</strong> {{ $pengaduan->location_details }}</p>
                                    </div>
                                    @if($pengaduan->latitude && $pengaduan->longitude)
                                        <div class="col-md-4 text-md-end">
                                            <a href="https://www.google.com/maps/search/?api=1&query={{ $pengaduan->latitude }},{{ $pengaduan->longitude }}" target="_blank" class="btn btn-sm btn-outline-primary px-3 py-2" style="border-radius: 8px; font-weight: 500;">
                                                <i class="fas fa-location-arrow me-1"></i>Rute Maps Teknisi
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
</div>
@endsection

@push('styles')
<style>
    .complaint-card {
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .complaint-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.06) !important;
    }
</style>
@endpush
