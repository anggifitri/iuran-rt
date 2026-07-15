@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Bikin container lebih kecil dan ke tengah biar mirip tampilan modal/card -->
    <div class="col-lg-8 col-xl-7 mx-auto">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-5" style="background-color: #fff;">

            <!-- 1. Header Area (Ungu) -->
            <div class="p-3" style="background-color: #8b5cf6; color: #fff;">
                <div class="d-flex align-items-center">
                    <img src="{{ preg_match('/^https?:\/\//', $categoryImages[strtolower(trim($umkm->kategori ?? 'default'))] ?? $categoryImages['default']) ? ($categoryImages[strtolower(trim($umkm->kategori ?? 'default'))] ?? $categoryImages['default']) : asset($categoryImages[strtolower(trim($umkm->kategori ?? 'default'))] ?? $categoryImages['default']) }}"
                         alt="Logo UMKM"
                         class="rounded-3 me-3"
                         style="width: 55px; height: 55px; object-fit: cover; border: 2px solid rgba(255,255,255,0.5);">
                    <div>
                        <h5 class="mb-0 fw-bold">{{ $umkm->nama }}</h5>
                        <small style="opacity: 0.85;">{{ $umkm->kategori }} • RT {{ Auth::user()->rt_number ?? '00' }}</small>
                    </div>
                </div>
            </div>

            <!-- 2. Banner Image Area (Full Width) -->
            @php
                $bannerImage = preg_match('/^https?:\/\//', $categoryImages[strtolower(trim($umkm->kategori ?? 'default'))] ?? $categoryImages['default']) ? $categoryImages[strtolower(trim($umkm->kategori ?? 'default'))] ?? $categoryImages['default'] : asset($categoryImages[strtolower(trim($umkm->kategori ?? 'default'))] ?? $categoryImages['default']);
            @endphp
            <img src="{{ $bannerImage }}" class="w-100" style="height: 220px; object-fit: cover;" alt="Banner Utama">

            <!-- 3. Detail Info Area (Putih) -->
            <div class="card-body p-4">
                <p class="mb-4" style="color: #4b5563; font-size: 0.95rem;">
                    {{ $umkm->deskripsi ?? 'Deskripsi usaha belum tersedia.' }}
                </p>

                <!-- Alamat & Pemilik (Kiri - Kanan) -->
                <div class="row mb-4" style="color: #6b7280; font-size: 0.9rem;">
                    <div class="col-sm-6 mb-2 mb-sm-0 d-flex align-items-start">
                        <i class="fas fa-map-marker-alt me-2 mt-1" style="color: #ef4444;"></i>
                        <span>{{ $umkm->alamat ?? '-' }}</span>
                    </div>
                    <div class="col-sm-6 d-flex align-items-start">
                        <i class="far fa-user me-2 mt-1" style="color: #6366f1;"></i>
                        <span>Pemilik: <strong style="color: #1f2937;">{{ strtoupper($umkm->pemilik ?? 'Tidak tercantum') }}</strong></span>
                    </div>
                </div>

                <!-- Tombol WA Utama -->
                <a href="{{ $whatsappUrl }}" target="_blank" class="btn btn-success d-inline-flex align-items-center gap-2 mb-4 px-3 py-2 rounded-3 fw-medium" style="background-color: #22c55e; border: none;">
                    <i class="fab fa-whatsapp fs-5"></i>
                    Chat Langsung WhatsApp
                </a>

                <!-- 4. Katalog Menu Area -->
                <h6 class="fw-bold mb-3 d-flex align-items-center gap-2" style="color: #1f2937;">
                    <i class="fas fa-book text-primary"></i>
                    Katalog Produk / Menu
                </h6>

                <div class="d-flex flex-column gap-3">
                    @foreach($menuItems as $item)
                        <div class="card border rounded-4 shadow-sm p-3" style="border-color: #f3f4f6 !important;">
                            <div class="d-flex align-items-center">
                                <!-- Foto Makanan -->
                                <div class="me-3">
                                    <div style="width: 70px; height: 70px; border-radius: 12px; background: url('{{ asset($item['image']) }}') center/cover no-repeat; border: 1px solid #e5e7eb;"></div>
                                </div>

                                <!-- Info Makanan -->
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-1" style="color: #1f2937;">{{ $item['nama'] }}</h6>
                                    <small class="d-block mb-1" style="color: #9ca3af;">{{ $item['deskripsi'] }}</small>
                                    <div class="fw-bold" style="color: #6366f1;">{{ $item['harga'] }}</div>
                                </div>

                                <!-- Tombol Order -->
                                <div class="ms-2">
                                    <a href="{{ $whatsappUrl }}" target="_blank" class="btn btn-success btn-sm d-inline-flex align-items-center gap-1 px-3 rounded-pill" style="background-color: #22c55e; border: none; font-weight: 500;">
                                        <i class="fab fa-whatsapp"></i> Order
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
