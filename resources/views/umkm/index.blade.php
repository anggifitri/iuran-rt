@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- HERO BANNER -->
    <div class="card p-4 mb-4 border-0 shadow-sm umkm-hero text-white" style="background: linear-gradient(135deg, #7c3aed, #22d3ee); overflow: hidden; border-radius: 24px;">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <span class="badge bg-white text-primary rounded-pill mb-3 fw-bold px-3 py-2">UMKM Unggulan RT {{ Auth::user()->rt_number ?? '00' }}</span>
                <h2 class="fw-bold display-6">Direktori UMKM Warga</h2>
                <p class="mb-4 text-white-50" style="max-width: 520px; line-height: 1.5;">Temukan dan dukung produk lokal karya tetangga Anda. Belanja lebih dekat, cepat, dan transparan.</p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('umkm.create') }}" class="btn btn-light btn-lg text-primary fw-bold" style="border-radius: 12px;"><i class="fas fa-plus me-2"></i>Tambah UMKM</a>
                    <a href="#daftar-umkm" class="btn btn-outline-light btn-lg" style="border-radius: 12px;">Lihat Direktori</a>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-block text-end">
                <div class="umkm-hero-image rounded-4 overflow-hidden shadow-lg" style="min-height: 260px; background: url('https://images.unsplash.com/photo-1578916171728-46686eac8d58?auto=format&fit=crop&w=900&q=80') center/cover no-repeat;"></div>
            </div>
        </div>
    </div>

    <!-- STATS CARDS -->
    <div class="row g-3 mb-4" id="daftar-umkm">
        <div class="col-sm-6 col-md-3">
            <div class="card p-3 umkm-stat-card border shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-1" style="font-size: 0.8rem; font-weight: 500;">Usaha Tercatat</h6>
                        <h3 class="mb-0 fw-bold" style="color: var(--text-main);">{{ $totalUsaha }}</h3>
                    </div>
                    <div class="rounded-circle p-2 bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="fas fa-store" style="font-size: 1.2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card p-3 umkm-stat-card border shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-1" style="font-size: 0.8rem; font-weight: 500;">Usaha Unggulan</h6>
                        <h3 class="mb-0 fw-bold" style="color: var(--text-main);">{{ $featuredUsaha }}</h3>
                    </div>
                    <div class="rounded-circle p-2 bg-warning-subtle text-warning d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="fas fa-award" style="font-size: 1.2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card p-3 umkm-stat-card border shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-1" style="font-size: 0.8rem; font-weight: 500;">Kategori</h6>
                        <h3 class="mb-0 fw-bold" style="color: var(--text-main);">{{ $totalKategori }}</h3>
                    </div>
                    <div class="rounded-circle p-2 bg-info-subtle text-info d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="fas fa-tags" style="font-size: 1.2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card p-3 umkm-stat-card border shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-1" style="font-size: 0.8rem; font-weight: 500;">Verifikasi</h6>
                        <h3 class="mb-0 fw-bold text-success" style="color: var(--text-main);">100%</h3>
                    </div>
                    <div class="rounded-circle p-2 bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="fas fa-check-circle" style="font-size: 1.2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FEATURED CAROUSEL -->
    @if($featuredItems->isNotEmpty())
        <div class="card p-4 mb-4 shadow-sm border-0 umkm-featured-card" style="border-radius: 24px; background-color: var(--bg-card); border: 1px solid var(--border-color);">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="fw-bold mb-1" style="color: var(--text-main);"><i class="fas fa-star text-warning me-2"></i>Rekomendasi UMKM Unggulan</h5>
                    <p class="text-muted mb-0" style="font-size: 0.85rem;">Pilihan usaha teratas milik warga yang paling diminati saat ini.</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-light rounded-circle border shadow-sm" type="button" data-bs-target="#umkmFeaturedCarousel" data-bs-slide="prev" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; background-color: var(--bg-card); color: var(--text-main); border-color: var(--border-color) !important;">
                        <i class="fas fa-chevron-left" style="font-size: 0.85rem;"></i>
                    </button>
                    <button class="btn btn-sm btn-light rounded-circle border shadow-sm" type="button" data-bs-target="#umkmFeaturedCarousel" data-bs-slide="next" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; background-color: var(--bg-card); color: var(--text-main); border-color: var(--border-color) !important;">
                        <i class="fas fa-chevron-right" style="font-size: 0.85rem;"></i>
                    </button>
                </div>
            </div>

            <div id="umkmFeaturedCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    @foreach ($featuredItems as $index => $item)
                        @php
                            $rawCat = strtolower(trim($item->kategori ?? ''));

                            $imgJasa = [
                                'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=600&q=80',
                                'https://images.unsplash.com/photo-1581578731548-c64695cc6952?auto=format&fit=crop&w=600&q=80',
                                'https://images.unsplash.com/photo-1600880292203-757bb62b4baf?auto=format&fit=crop&w=600&q=80',
                                'https://images.unsplash.com/photo-1556761175-4b46a572b786?auto=format&fit=crop&w=600&q=80'
                            ];
                            $imgKerajinan = [
                                'https://images.unsplash.com/photo-1477867082705-47a1d8d462f8?auto=format&fit=crop&w=600&q=80',
                                'https://images.unsplash.com/photo-1585806871183-b78cc70f5e1f?auto=format&fit=crop&w=600&q=80',
                                'https://images.unsplash.com/photo-1606760227091-3dd870d97f1d?auto=format&fit=crop&w=600&q=80'
                            ];
                            $imgMakanan = [
                                'https://images.unsplash.com/photo-1498654896293-37aacf113fd9?auto=format&fit=crop&w=600&q=80',
                                'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&w=600&q=80',
                                'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=600&q=80',
                                'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=600&q=80'
                            ];
                            $imgDagang = [
                                'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=600&q=80',
                                'https://images.unsplash.com/photo-1534452203293-494d7ddbf7e0?auto=format&fit=crop&w=600&q=80',
                                'https://images.unsplash.com/photo-1472851294608-062f824d29cc?auto=format&fit=crop&w=600&q=80'
                            ];
                            $imgDefault = [
                                'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=600&q=80',
                                'https://images.unsplash.com/photo-1556761175-4b46a572b786?auto=format&fit=crop&w=600&q=80'
                            ];

                            $idx = $loop->iteration;

                            if (strpos($rawCat, 'jasa') !== false) $fallback = $imgJasa[$idx % count($imgJasa)];
                            elseif (strpos($rawCat, 'kerajinan') !== false) $fallback = $imgKerajinan[$idx % count($imgKerajinan)];
                            elseif (strpos($rawCat, 'makan') !== false || strpos($rawCat, 'kuliner') !== false) $fallback = $imgMakanan[$idx % count($imgMakanan)];
                            elseif (strpos($rawCat, 'dagang') !== false || strpos($rawCat, 'perdagangan') !== false) $fallback = $imgDagang[$idx % count($imgDagang)];
                            else $fallback = $imgDefault[$idx % count($imgDefault)];

                            $dbImage = trim($item->cover_image ?? '');
                            $fSrc = $fallback;

                            if (!empty($dbImage)) {
                                if (filter_var($dbImage, FILTER_VALIDATE_URL)) {
                                    $fSrc = $dbImage;
                                } elseif (file_exists(public_path('storage/' . $dbImage))) {
                                    $fSrc = asset('storage/' . $dbImage);
                                }
                            }
                        @endphp
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <div class="row g-4 align-items-center">
                                <div class="col-lg-5">
                                    <div class="rounded-4 overflow-hidden shadow-sm" style="height: 250px;">
                                        <img src="{{ $fSrc }}"
                                             onerror="this.onerror=null; this.src='{{ $fallback }}';"
                                             alt="{{ $item->kategori ?? 'UMKM' }}"
                                             class="img-fluid w-100 h-100"
                                             style="object-fit: cover;">
                                    </div>
                                </div>
                                <div class="col-lg-7">
                                    <div class="d-flex gap-2 align-items-center mb-2">
                                        <span class="badge bg-warning text-dark fw-semibold px-2 py-1.5">{{ $item->kategori ?? 'Umum' }}</span>
                                        <span class="badge bg-secondary fw-semibold px-2 py-1.5">RT {{ $item->rt_number ?? '-' }}</span>
                                    </div>
                                    <h4 class="fw-bold mb-2" style="color: var(--text-main);">{{ $item->nama }}</h4>
                                    <p class="text-muted mb-3" style="font-size: 0.9rem; line-height: 1.4;">{{ \Illuminate\Support\Str::limit($item->deskripsi ?? 'Deskripsi belum tersedia.', 140) }}</p>
                                    
                                    <div class="row g-2 text-secondary" style="font-size: 0.85rem;">
                                        <div class="col-sm-6">
                                            <strong><i class="fas fa-user-tie me-1"></i>Pemilik:</strong> <span style="color: var(--text-main);">{{ $item->pemilik ?? 'Tidak tercantum' }}</span>
                                        </div>
                                        <div class="col-sm-6">
                                            <strong><i class="fas fa-phone me-1"></i>Hubungi:</strong> <span style="color: var(--text-main);">{{ $item->telepon ?? '-' }}</span>
                                        </div>
                                        <div class="col-12">
                                            <strong><i class="fas fa-map-marker-alt me-1"></i>Alamat:</strong> <span style="color: var(--text-main);">{{ $item->alamat ?? '-' }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <a href="#" data-url="{{ route('umkm.show', $item) }}?embed=1" class="btn btn-purple px-4 open-umkm">Kunjungi Usaha</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- SEARCH & CATEGORY FILTER TABS -->
    <div class="card p-3 mb-4 shadow-sm umkm-search-card border-0" style="border-radius: 20px; background-color: var(--bg-card); border: 1px solid var(--border-color) !important;">
        <form method="GET" id="searchForm" action="{{ route('umkm.index') }}" class="row g-2">
            <!-- Hidden input for kategori to maintain filter when searching text -->
            @if(request('kategori'))
                <input type="hidden" name="kategori" value="{{ request('kategori') }}">
            @endif
            <div class="col-md-9 position-relative">
                <i class="fas fa-search position-absolute text-muted" style="left: 16px; top: 50%; transform: translateY(-50%); z-index: 10;"></i>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control ps-5 form-control-lg" placeholder="Cari usaha, warung, produk, atau jasa warga..." style="border-radius: 12px; height: 50px;">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-lg w-100" style="border-radius: 12px; height: 50px;"><i class="fas fa-search me-2"></i>Cari</button>
            </div>
        </form>
    </div>

    <!-- HORIZONTAL CATEGORY MENU (TAB FILTER) -->
    <div class="d-flex flex-wrap gap-2 justify-content-start mb-4 px-1" style="overflow-x: auto; white-space: nowrap;">
        <a href="{{ route('umkm.index', array_filter(['q' => request('q')])) }}" class="btn {{ empty(request('kategori')) ? 'btn-primary shadow-sm' : 'btn-outline-secondary border' }} rounded-pill px-4 py-2" style="font-size: 0.85rem; font-weight: 600; border-radius: 50px;">
            <i class="fas fa-border-all me-1"></i> Semua Usaha
        </a>
        @foreach ($categories as $category)
            @php
                $catIcon = 'fa-store';
                $lowerCat = strtolower($category);
                if (strpos($lowerCat, 'makan') !== false || strpos($lowerCat, 'kuliner') !== false) $catIcon = 'fa-utensils';
                elseif (strpos($lowerCat, 'jasa') !== false) $catIcon = 'fa-concierge-bell';
                elseif (strpos($lowerCat, 'kerajinan') !== false) $catIcon = 'fa-palette';
                elseif (strpos($lowerCat, 'dagang') !== false) $catIcon = 'fa-shopping-bag';
            @endphp
            <a href="{{ route('umkm.index', array_filter(['kategori' => $category, 'q' => request('q')])) }}" class="btn {{ request('kategori') === $category ? 'btn-primary shadow-sm' : 'btn-outline-secondary border' }} rounded-pill px-4 py-2" style="font-size: 0.85rem; font-weight: 600; border-radius: 50px;">
                <i class="fas {{ $catIcon }} me-1"></i> {{ $category }}
            </a>
        @endforeach
    </div>

    <!-- GRID DIRECTORY LIST -->
    @if($usahas->isEmpty())
        <div class="card p-5 text-center text-muted border-0 shadow-sm rounded-4" style="background-color: var(--bg-card); border: 1px solid var(--border-color);">
            <i class="fas fa-store-slash fa-3x mb-3 text-secondary" style="opacity: 0.6;"></i>
            <h5 class="mb-2 fw-semibold">Tidak Ada Hasil Ditemukan</h5>
            <p class="mb-0">Tidak ada data UMKM yang cocok dengan pencarian Anda. Silakan bersihkan kata kunci atau filter.</p>
        </div>
    @else
        <div class="row g-4">
            @foreach ($usahas as $usaha)
                @php
                    $rawCat = strtolower(trim($usaha->kategori ?? ''));

                    $imgJasa = [
                        'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=600&q=80',
                        'https://images.unsplash.com/photo-1581578731548-c64695cc6952?auto=format&fit=crop&w=600&q=80',
                        'https://images.unsplash.com/photo-1600880292203-757bb62b4baf?auto=format&fit=crop&w=600&q=80',
                        'https://images.unsplash.com/photo-1556761175-4b46a572b786?auto=format&fit=crop&w=600&q=80'
                    ];
                    $imgKerajinan = [
                        'https://images.unsplash.com/photo-1477867082705-47a1d8d462f8?auto=format&fit=crop&w=600&q=80',
                        'https://images.unsplash.com/photo-1585806871183-b78cc70f5e1f?auto=format&fit=crop&w=600&q=80',
                        'https://images.unsplash.com/photo-1606760227091-3dd870d97f1d?auto=format&fit=crop&w=600&q=80'
                    ];
                    $imgMakanan = [
                        'https://images.unsplash.com/photo-1498654896293-37aacf113fd9?auto=format&fit=crop&w=600&q=80',
                        'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&w=600&q=80',
                        'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=600&q=80',
                        'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=600&q=80'
                    ];
                    $imgDagang = [
                        'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=600&q=80',
                        'https://images.unsplash.com/photo-1534452203293-494d7ddbf7e0?auto=format&fit=crop&w=600&q=80',
                        'https://images.unsplash.com/photo-1472851294608-062f824d29cc?auto=format&fit=crop&w=600&q=80'
                    ];
                    $imgDefault = [
                        'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=600&q=80',
                        'https://images.unsplash.com/photo-1556761175-4b46a572b786?auto=format&fit=crop&w=600&q=80'
                    ];

                    $idx = $loop->iteration;

                    if (strpos($rawCat, 'jasa') !== false) $fallback = $imgJasa[$idx % count($imgJasa)];
                    elseif (strpos($rawCat, 'kerajinan') !== false) $fallback = $imgKerajinan[$idx % count($imgKerajinan)];
                    elseif (strpos($rawCat, 'makan') !== false || strpos($rawCat, 'kuliner') !== false) $fallback = $imgMakanan[$idx % count($imgMakanan)];
                    elseif (strpos($rawCat, 'dagang') !== false || strpos($rawCat, 'perdagangan') !== false) $fallback = $imgDagang[$idx % count($imgDagang)];
                    else $fallback = $imgDefault[$idx % count($imgDefault)];

                    $dbImage = trim($usaha->cover_image ?? '');
                    $imgSrc = $fallback;

                    if (!empty($dbImage)) {
                        if (filter_var($dbImage, FILTER_VALIDATE_URL)) {
                            $imgSrc = $dbImage;
                        } elseif (file_exists(public_path('storage/' . $dbImage))) {
                            $imgSrc = asset('storage/' . $dbImage);
                        }
                    }
                @endphp
                <div class="col-md-6 col-xl-4">
                    <div class="card umkm-card h-100 shadow-sm border-0" style="border-radius: 20px; overflow: hidden; background-color: var(--bg-card); border: 1px solid var(--border-color) !important;">
                        
                        <!-- Badges Absolute di Atas Gambar -->
                        <div class="position-relative overflow-hidden" style="height: 200px;">
                            <img src="{{ $imgSrc }}"
                                 onerror="this.onerror=null; this.src='{{ $fallback }}';"
                                 class="card-img-top w-100 h-100"
                                 style="object-fit: cover;"
                                 alt="{{ $usaha->kategori ?? 'UMKM' }}"
                                 loading="lazy">
                            
                            <span class="badge bg-primary position-absolute top-0 start-0 m-3 shadow" style="z-index: 10; font-size: 0.75rem; padding: 6px 12px; border-radius: 8px;">
                                {{ $usaha->kategori ?? 'Umum' }}
                            </span>
                            <span class="badge bg-dark bg-opacity-50 position-absolute top-0 end-0 m-3 shadow" style="z-index: 10; font-size: 0.75rem; padding: 6px 12px; border-radius: 8px; backdrop-filter: blur(4px);">
                                RT {{ $usaha->rt_number ?? '-' }}
                            </span>
                        </div>

                        <div class="card-body d-flex flex-column p-4">
                            <h5 class="card-title fw-bold mb-2" style="color: var(--text-main);">{{ $usaha->nama }}</h5>
                            <p class="text-secondary mb-3 flex-grow-1" style="font-size: 0.85rem; line-height: 1.4;">{{ \Illuminate\Support\Str::limit($usaha->deskripsi ?? 'Deskripsi belum tersedia.', 100) }}</p>
                            
                            <div class="text-secondary mb-3" style="font-size: 0.8rem;">
                                <p class="mb-1 text-truncate"><i class="fas fa-map-marker-alt text-primary me-2"></i>{{ $usaha->alamat ?? '-' }}</p>
                                <p class="mb-0"><i class="fas fa-phone text-success me-2"></i>{{ $usaha->telepon ?? '-' }}</p>
                            </div>
                            
                            <a href="#" data-url="{{ route('umkm.show', $usaha) }}?embed=1" class="btn btn-purple w-100 open-umkm" style="border-radius: 12px;">Kunjungi Toko</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- EMBED DETAILS MODAL -->
    <div class="modal fade" id="umkmModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="background: transparent; border: none; box-shadow: none;">
          <div class="modal-body p-0">
            <div id="umkmModalContent"></div>
          </div>
        </div>
      </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .umkm-hero { min-height: 300px; border-radius: 24px; }
    .umkm-hero-image { min-height: 240px; }
    
    .umkm-stat-card { 
        border-radius: 18px; 
        background: var(--bg-card) !important; 
        border: 1px solid var(--border-color) !important; 
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .umkm-stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 22px rgba(0,0,0,0.06) !important;
    }
    
    .umkm-featured-card { 
        border-radius: 24px; 
        background: var(--bg-card) !important; 
        border: 1px solid var(--border-color) !important; 
    }
    
    .umkm-search-card { 
        border-radius: 20px; 
        background: var(--bg-card) !important; 
        border: 1px solid var(--border-color) !important; 
    }
    .umkm-search-card .form-control {
        background-color: var(--bg-body) !important;
        border: 1px solid var(--border-color) !important;
        color: var(--text-main) !important;
    }
    
    .umkm-card { 
        border-radius: 20px; 
        background: var(--bg-card) !important; 
        border: 1px solid var(--border-color) !important; 
        transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1), box-shadow 0.3s ease;
    }
    .umkm-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 36px rgba(0,0,0,0.1) !important;
    }
    
    .umkm-card .card-img-top { 
        transition: transform 0.4s ease;
    }
    .umkm-card:hover .card-img-top {
        transform: scale(1.05);
    }
    
    .btn-purple { 
        background: linear-gradient(135deg, #7c3aed, #22d3ee); 
        border: none; 
        color: #fff; 
        box-shadow: 0 10px 25px rgba(124, 58, 237, 0.15); 
        transition: all 0.3s ease;
        font-weight: 600;
    }
    .btn-purple:hover { 
        background: linear-gradient(135deg, #6d28d9, #0ea5e9); 
        transform: translateY(-2px);
        box-shadow: 0 12px 30px rgba(124, 58, 237, 0.25); 
        color: #fff;
    }
    
    .carousel-item { min-height: 250px; }
    
    /* Category scroll list styling */
    ::-webkit-scrollbar {
        height: 6px;
    }
    ::-webkit-scrollbar-track {
        background: transparent;
    }
    ::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.1);
        border-radius: 10px;
    }
    
    .modal-backdrop.show { background-color: rgba(0,0,0,0.45); }
    #umkmModal .modal-content { background: transparent; }
    #umkmModal .modal-body { padding: 0 20px 40px 20px; }
    #umkmModal .umkm-modal-content { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 20px; color: var(--text-main); }
    
    @media (max-width: 576px) { 
        #umkmModal .modal-dialog { margin: 12px; } 
        .umkm-hero { min-height: auto; }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.open-umkm').forEach(function(el){
        el.addEventListener('click', function(e){
            e.preventDefault();
            var url = el.getAttribute('data-url');
            if (!url) return;
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function(r){ return r.text(); })
                .then(function(html){
                    var container = document.getElementById('umkmModalContent');
                    container.innerHTML = '<div class="umkm-modal-content">'+html+'<\/div>';
                    var modal = new bootstrap.Modal(document.getElementById('umkmModal'));
                    modal.show();
                    var top = container.querySelector('.top-square');
                    if (top) top.addEventListener('click', function(){ modal.hide(); });
                }).catch(function(err){ console.error(err); });
        });
    });
});
</script>
@endpush
