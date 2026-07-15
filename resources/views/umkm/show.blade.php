@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="umkm-modal mx-auto">
    <div class="card p-0 mb-4 border-0 shadow-sm umkm-hero" style="background: linear-gradient(135deg, #7c3aed, #22d3ee); color: #fff; overflow: hidden; position: relative;">
        <div class="top-square"></div>
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="d-flex align-items-center mb-2">
                    <img src="{{ preg_match('/^https?:\/\//', $categoryImages[strtolower(trim($umkm->kategori ?? 'default'))] ?? $categoryImages['default']) ? ($categoryImages[strtolower(trim($umkm->kategori ?? 'default'))] ?? $categoryImages['default']) : asset($categoryImages[strtolower(trim($umkm->kategori ?? 'default'))] ?? $categoryImages['default']) }}" alt="" class="avatar me-3">
                    <div>
                        <h4 class="mb-0 fw-bold">{{ $umkm->nama }}</h4>
                        <small class="opacity-85">{{ $umkm->kategori }} • RT {{ Auth::user()->rt_number ?? '00' }}</small>
                    </div>
                </div>
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
                    <a href="{{ $whatsappUrl }}" target="_blank" class="btn whatsapp-btn d-inline-flex align-items-center gap-2 px-4 py-2">
                        <i class="fab fa-whatsapp"></i>
                        Chat Langsung WhatsApp
                    </a>
                </div>
                <div class="row g-3">
                    @foreach($menuItems as $item)
                        <div class="col-12">
                            <div class="product-card card border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="d-flex align-items-center p-3">
                                    <div class="me-3">
                                        <div class="product-image" style="background: url('{{ asset($item['image']) }}') center/cover no-repeat;"></div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold mb-1">{{ $item['nama'] }}</h6>
                                        <p class="text-muted mb-2">{{ $item['deskripsi'] }}</p>
                                    </div>
                                    <div class="text-end">
                                        <div class="mb-2 fw-bold price-text">{{ $item['harga'] }}</div>
                                        <a href="{{ $whatsappUrl }}" target="_blank" class="btn order-btn d-inline-flex align-items-center gap-2">
                                            <i class="fab fa-whatsapp"></i>
                                            Order
                                        </a>
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
</div>
</div>
@endsection

@push('styles')
<style>
    .umkm-modal {
        max-width: 980px;
    }

    .umkm-hero {
        min-height: 140px;
        border-radius: 16px 16px 0 0;
        padding: 22px 28px;
    }

    .top-square {
        position: absolute;
        right: 18px;
        top: 16px;
        width: 34px;
        height: 34px;
        background: #fff;
        border-radius: 6px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }

    .avatar {
        width: 56px;
        height: 56px;
        object-fit: cover;
        border-radius: 12px;
        border: 2px solid rgba(255,255,255,0.2);
        box-shadow: 0 6px 18px rgba(124,58,237,0.12);
    }

    .whatsapp-btn {
        background: linear-gradient(90deg, #34d058, #22c55e);
        color: #fff;
        border: none;
        box-shadow: 0 8px 24px rgba(34,197,94,0.18);
        border-radius: 10px;
    }

    .whatsapp-btn i { font-size: 18px; }

    .product-card { border-radius: 14px; }

    .product-image {
        width: 84px;
        height: 84px;
        border-radius: 12px;
        background-color: #fff;
        background-size: cover;
        background-position: center;
        box-shadow: 0 6px 18px rgba(15,23,42,0.04);
    }

    .price-text {
        color: #6d28d9;
        font-size: 1rem;
    }

    .order-btn {
        background: #22c55e;
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 8px 12px;
    }

    .order-btn i { font-size: 14px; }
</style>
@endpush
