@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card p-4 mb-4 border-0 shadow-sm umkm-hero" style="background: linear-gradient(135deg, #7c3aed, #22d3ee); color: #fff; overflow: hidden;">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <span class="badge bg-white text-primary rounded-pill mb-3">{{ $umkm->kategori }} • RT {{ Auth::user()->rt_number ?? '00' }}</span>
                <h2 class="fw-bold mb-2">{{ $umkm->nama }}</h2>
                <p class="mb-1">{{ $umkm->deskripsi ?? 'Deskripsi usaha belum tersedia.' }}</p>
                <p class="mb-0 opacity-75">Pemilik: {{ $umkm->pemilik ?? 'Tidak tercantum' }} • {{ $umkm->alamat ?? '-' }}</p>
            </div>
            <div class="col-lg-5 mt-4 mt-lg-0">
                <div class="rounded-4 overflow-hidden shadow-lg" style="min-height: 280px; background: url('{{ preg_match('/^https?:\/\//', $categoryImages[strtolower(trim($umkm->kategori ?? 'default'))] ?? $categoryImages['default']) ? $categoryImages[strtolower(trim($umkm->kategori ?? 'default'))] ?? $categoryImages['default'] : asset($categoryImages[strtolower(trim($umkm->kategori ?? 'default'))] ?? $categoryImages['default']) }}') center/cover no-repeat;"></div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card p-4 shadow-sm border-0 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold mb-1">Katalog Produk / Menu</h5>
                        <p class="text-muted mb-0">Pilih produk atau layanan favorit, lalu chat penjual langsung via WhatsApp.</p>
                    </div>
                    <a href="{{ $whatsappUrl }}" target="_blank" class="btn btn-success d-inline-flex align-items-center gap-2 px-4 py-2">
                        <i class="fab fa-whatsapp"></i>
                        Order via WhatsApp
                    </a>
                </div>
                <div class="row g-3">
                    @foreach($menuItems as $item)
                        <div class="col-md-12">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="row g-0 align-items-center">
                                    <div class="col-auto">
                                        <div class="product-image" style="width: 120px; height: 120px; background: url('{{ asset($item['image']) }}') center/cover no-repeat;"></div>
                                    </div>
                                    <div class="col">
                                        <div class="card-body py-3">
                                            <h6 class="fw-bold mb-1">{{ $item['nama'] }}</h6>
                                            <p class="text-muted mb-2">{{ $item['deskripsi'] }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fw-bold text-primary">{{ $item['harga'] }}</span>
                                                <a href="{{ $whatsappUrl }}" target="_blank" class="btn btn-success btn-sm d-flex align-items-center gap-2">
                                                    <i class="fab fa-whatsapp"></i>
                                                    Order
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card p-4 shadow-sm border-0">
                <h5 class="fw-bold mb-3">Detail Kontak</h5>
                <p class="mb-2"><strong>Telepon:</strong> {{ $umkm->telepon ?? '-' }}</p>
                <p class="mb-2"><strong>Alamat:</strong> {{ $umkm->alamat ?? '-' }}</p>
                <p class="mb-0"><strong>Kategori:</strong> {{ $umkm->kategori }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .product-image {
        border-radius: 24px;
        background-color: #f8fafc;
    }

    .umkm-hero {
        min-height: 320px;
        border-radius: 24px;
    }

    .btn-success {
        background-color: #22c55e;
        border-color: #22c55e;
    }

    .btn-success:hover {
        background-color: #16a34a;
        border-color: #16a34a;
    }
</style>
@endpush
