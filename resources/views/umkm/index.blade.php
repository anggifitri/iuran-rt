@extends('layouts.app')

@section('title', 'Direktori UMKM Warga — NexaNest')

@section('content')
<!-- HEADER DIREKTORI UMKM SESUAI STANDAR NEXANEST -->
<div class="nx-page-header">
    <div>
        <span class="badge rounded-pill mb-2 px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
            <i class="fas fa-store me-1.5"></i> Ekonomi Kreatif &amp; Produk Lokal RW 018
        </span>
        <h1 class="nx-page-title">Direktori UMKM Warga</h1>
        <p class="nx-page-subtitle">
            Pusat promosi dan direktori usaha warga lingkungan RW 018. Temukan kuliner, kerajinan tangan, dan jasa karya tetangga terdekat dengan ulasan pembeli terpercaya.
        </p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        @if(Auth::check())
            <a href="{{ route('umkm.index', ['favorit' => 1]) }}" class="btn btn-sm {{ request('favorit') ? 'btn-danger text-white' : 'btn-outline-danger' }} px-3 py-2 fw-bold" style="border-radius: 10px; height: 38px; display: inline-flex; align-items: center;">
                <i class="fas fa-heart me-1.5"></i>Favorit Saya
            </a>
        @endif
        <a href="{{ route('umkm.create') }}" class="btn btn-sm btn-primary px-3.5 py-2 fw-bold shadow-xs" style="border-radius: 10px; height: 38px; display: inline-flex; align-items: center;">
            <i class="fas fa-plus me-1.5"></i>Daftarkan Usaha Baru
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-xs" role="alert" style="border-radius: 12px; background: var(--nx-mint-soft); color: var(--nx-forest);">
        <i class="fas fa-circle-check me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-xs" role="alert" style="border-radius: 12px; background: var(--nx-danger-soft); color: var(--nx-danger);">
        <i class="fas fa-circle-exclamation me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- METRIC OVERVIEW CARDS (DATABASE-DRIVEN STANDAR NEXANEST) -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <x-stat-card 
            title="Total UMKM Warga" 
            value="{{ $totalUsaha }}" 
            sub="Usaha aktif terdaftar" 
            icon="fas fa-store" 
            theme="forest" 
        />
    </div>
    <div class="col-md-3 col-sm-6">
        <x-stat-card 
            title="Usaha Terverifikasi" 
            value="{{ $usahaTerverifikasi }}" 
            sub="Tervalidasi pengurus RT/RW" 
            icon="fas fa-certificate" 
            theme="primary" 
            valColor="#1F567A" 
        />
    </div>
    <div class="col-md-3 col-sm-6">
        <x-stat-card 
            title="Katalog Produk & Jasa" 
            value="{{ $totalProduk }}" 
            sub="Item siap dipesan" 
            icon="fas fa-box-open" 
            theme="warning" 
            valColor="#D97706" 
        />
    </div>
    <div class="col-md-3 col-sm-6">
        <x-stat-card 
            title="Ulasan Pembeli" 
            value="{{ $totalUlasan }}" 
            sub="Testimoni terverifikasi" 
            icon="fas fa-comments" 
            theme="forest" 
            valColor="#059669" 
        />
    </div>
</div>

<!-- FILTER & PENCARIAN TERPADU (.nx-filter-card) -->
<div class="card nx-filter-card mb-4 shadow-xs" style="border-radius: 14px; border: 1px solid var(--nx-border);">
    <div class="p-3">
        <form method="GET" action="{{ route('umkm.index') }}" class="row g-2 align-items-center" id="filterUmkmForm">
            <!-- Search Keyword -->
            <div class="col-lg-4 col-md-6 col-12">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 10px 0 0 10px;">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" name="q" class="form-control form-control-sm border-start-0" placeholder="Cari nama usaha, produk, pemilik..." value="{{ request('q') }}" style="border-radius: 0 10px 10px 0; height: 38px;">
                </div>
            </div>

            <!-- Kategori Dropdown -->
            <div class="col-lg-2 col-md-3 col-6">
                <select name="kategori" class="form-select form-select-sm" style="border-radius: 10px; height: 38px;">
                    <option value="">-- Semua Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('kategori') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Wilayah RT -->
            <div class="col-lg-2 col-md-3 col-6">
                <select name="rt_number" class="form-select form-select-sm" style="border-radius: 10px; height: 38px;">
                    <option value="">-- Semua Wilayah RT --</option>
                    @foreach($rtList as $rt)
                        <option value="{{ $rt }}" {{ request('rt_number') == $rt ? 'selected' : '' }}>RT {{ $rt }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Verifikasi -->
            <div class="col-lg-2 col-md-4 col-6">
                <select name="status_verifikasi" class="form-select form-select-sm" style="border-radius: 10px; height: 38px;">
                    <option value="">-- Status Verifikasi --</option>
                    <option value="terverifikasi" {{ request('status_verifikasi') == 'terverifikasi' ? 'selected' : '' }}>✅ Terverifikasi</option>
                    <option value="menunggu" {{ request('status_verifikasi') == 'menunggu' ? 'selected' : '' }}>⏳ Menunggu Verifikasi</option>
                    <option value="ditolak" {{ request('status_verifikasi') == 'ditolak' ? 'selected' : '' }}>❌ Ditolak</option>
                </select>
            </div>

            <!-- Sort By & Action Buttons -->
            <div class="col-lg-2 col-md-4 col-6 d-flex gap-1.5">
                <select name="sort" class="form-select form-select-sm flex-fill" style="border-radius: 10px; height: 38px;">
                    <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                    <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Rating Tertinggi</option>
                    <option value="nama" {{ request('sort') == 'nama' ? 'selected' : '' }}>Nama A-Z</option>
                </select>
                <button type="submit" class="btn btn-sm btn-primary px-3" style="border-radius: 10px; height: 38px; font-weight: 600;" title="Terapkan Filter">
                    <i class="fas fa-filter"></i>
                </button>
                @if(request()->anyFilled(['q', 'kategori', 'rt_number', 'status_verifikasi', 'sort', 'favorit']))
                    <a href="{{ route('umkm.index') }}" class="btn btn-sm btn-outline-secondary px-2.5" style="border-radius: 10px; height: 38px; display: inline-flex; align-items: center;" title="Reset Filter">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>

        <!-- Quick Horizontal Category Pills -->
        <div class="d-flex flex-wrap gap-1.5 mt-3 pt-2.5 border-top" style="border-color: var(--nx-border) !important;">
            <a href="{{ route('umkm.index', array_filter(['q' => request('q'), 'rt_number' => request('rt_number'), 'status_verifikasi' => request('status_verifikasi')])) }}"
               class="btn btn-sm {{ empty(request('kategori')) ? 'btn-primary' : 'btn-outline-secondary' }}" style="border-radius: 20px; font-size: 0.75rem; font-weight: 600;">
                Semua Usaha
            </a>
            @foreach($categories as $category)
                @php
                    $catIcon = 'fa-store';
                    $lowerCat = strtolower($category);
                    if (str_contains($lowerCat, 'makan') || str_contains($lowerCat, 'kuliner')) $catIcon = 'fa-utensils';
                    elseif (str_contains($lowerCat, 'jasa')) $catIcon = 'fa-screwdriver-wrench';
                    elseif (str_contains($lowerCat, 'kerajinan')) $catIcon = 'fa-palette';
                    elseif (str_contains($lowerCat, 'dagang')) $catIcon = 'fa-bag-shopping';
                @endphp
                <a href="{{ route('umkm.index', array_filter(['kategori' => $category, 'q' => request('q'), 'rt_number' => request('rt_number'), 'status_verifikasi' => request('status_verifikasi')])) }}"
                   class="btn btn-sm {{ request('kategori') === $category ? 'btn-primary' : 'btn-outline-secondary' }}" style="border-radius: 20px; font-size: 0.75rem; font-weight: 600;">
                    <i class="fas {{ $catIcon }} me-1"></i>{{ $category }}
                </a>
            @endforeach
        </div>
    </div>
</div>

<!-- REKOMENDASI UNGGULAN WARGA CAROUSEL (IF AVAILABLE) -->
@if($featuredItems->isNotEmpty() && !request()->anyFilled(['q', 'kategori', 'rt_number', 'status_verifikasi', 'favorit']))
    <div class="card p-3.5 mb-4 shadow-xs" style="border-radius: 14px; border: 1px solid var(--nx-border); background: linear-gradient(135deg, #F7F9F7 0%, #FFFFFF 100%);">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.05rem;">
                    <i class="fas fa-award text-warning me-2"></i>Rekomendasi UMKM Unggulan Warga RW 018
                </h5>
                <small class="text-muted">Usaha warga dengan rating ulasan pembeli tertinggi dan berstatus terverifikasi resmi.</small>
            </div>
            <div class="d-flex gap-1.5">
                <button class="btn btn-sm btn-outline-secondary rounded-circle" type="button" data-bs-target="#umkmFeaturedCarousel" data-bs-slide="prev" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                    <i class="fas fa-chevron-left" style="font-size: 0.75rem;"></i>
                </button>
                <button class="btn btn-sm btn-outline-secondary rounded-circle" type="button" data-bs-target="#umkmFeaturedCarousel" data-bs-slide="next" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                    <i class="fas fa-chevron-right" style="font-size: 0.75rem;"></i>
                </button>
            </div>
        </div>

        <div id="umkmFeaturedCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                @foreach($featuredItems as $index => $item)
                    @php
                        $fCover = app(\App\Http\Controllers\UmkmController::class)->resolveCoverImage($item->cover_image, $item->kategori);
                    @endphp
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                        <div class="row g-3 align-items-center">
                            <div class="col-lg-4 col-md-5">
                                <div class="rounded-3 overflow-hidden shadow-xs" style="height: 180px;">
                                    <img src="{{ $fCover }}" alt="{{ $item->nama }}" class="img-fluid w-100 h-100" style="object-fit: cover;">
                                </div>
                            </div>
                            <div class="col-lg-8 col-md-7">
                                <div class="d-flex flex-wrap align-items-center gap-1.5 mb-1.5">
                                    <span class="badge rounded-pill px-2.5 py-1" style="background: var(--nx-mint); color: var(--nx-forest); font-weight: 700; font-size: 0.7rem;">
                                        {{ $item->kategori }}
                                    </span>
                                    <span class="badge rounded-pill px-2.5 py-1" style="background: var(--nx-powder); color: #1E4B6D; font-weight: 700; font-size: 0.7rem;">
                                        RT {{ $item->rt_number }}
                                    </span>
                                    {!! $item->status_badge !!}
                                </div>
                                <h4 class="fw-bold text-dark mb-1" style="font-size: 1.2rem;">{{ $item->nama }}</h4>
                                
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <div class="text-warning small">
                                        {!! $item->rating_stars !!}
                                    </div>
                                    <strong class="text-dark small">{{ number_format($item->rating_avg, 1) }}</strong>
                                    <span class="text-muted small">({{ $item->rating_count }} Ulasan Terverifikasi)</span>
                                </div>

                                <p class="text-muted small mb-2.5" style="line-height: 1.5; font-size: 0.82rem;">
                                    {{ \Illuminate\Support\Str::limit($item->deskripsi, 140) }}
                                </p>

                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <a href="{{ route('umkm.show', $item->id) }}" class="btn btn-sm btn-primary px-3 py-1.5 fw-bold" style="border-radius: 8px;">
                                        <i class="fas fa-store me-1.5"></i>Kunjungi Toko
                                    </a>
                                    <a href="{{ $item->whatsapp_url }}" target="_blank" class="btn btn-sm btn-outline-success px-3 py-1.5 fw-bold" style="border-radius: 8px;">
                                        <i class="fab fa-whatsapp me-1.5"></i>Hubungi Penjual
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

<!-- DAFTAR KATALOG GRID UMKM (.nx-grid-card) -->
<div class="row g-3">
    @forelse($usahas as $usaha)
        @php
            $imgCover = app(\App\Http\Controllers\UmkmController::class)->resolveCoverImage($usaha->cover_image, $usaha->kategori);
            $prodCount = $usaha->produks->count();
            $reviewCount = $usaha->visibleUlasans->count();
        @endphp
        <div class="col-lg-4 col-md-6 col-12">
            <div class="card nx-grid-card shadow-xs h-100" style="border-radius: 14px; border: 1px solid var(--nx-border); background: #ffffff; overflow: hidden;">
                <!-- Cover Image & Overlays -->
                <div class="position-relative overflow-hidden" style="height: 190px; background: #E5EBE6;">
                    <img src="{{ $imgCover }}" class="w-100 h-100" style="object-fit: cover; transition: transform 0.3s ease;" alt="{{ $usaha->nama }}" loading="lazy">
                    
                    <!-- Category Badge (Top Left) -->
                    <span class="badge rounded-pill position-absolute top-0 start-0 m-2.5 px-2.5 py-1 shadow-sm"
                          style="background: var(--nx-forest); color: #fff; font-size: 0.72rem; font-weight: 700;">
                        {{ $usaha->kategori }}
                    </span>

                    <!-- RT Badge & Favorite Button (Top Right) -->
                    <div class="position-absolute top-0 end-0 m-2.5 d-flex gap-1.5 align-items-center">
                        <span class="badge rounded-pill px-2.5 py-1 shadow-sm"
                              style="background: rgba(255,255,255,0.92); color: var(--nx-dark); font-size: 0.72rem; font-weight: 700;">
                            RT {{ $usaha->rt_number }}
                        </span>
                        @if(Auth::check())
                            <form action="{{ route('umkm.toggle_favorit', $usaha->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm rounded-circle shadow-sm" style="width: 28px; height: 28px; padding: 0; background: rgba(255,255,255,0.92); border: none;" title="{{ $usaha->is_favorited ? 'Hapus dari Favorit' : 'Tambah ke Favorit' }}">
                                    <i class="fas fa-heart {{ $usaha->is_favorited ? 'text-danger' : 'text-muted' }}" style="font-size: 0.75rem;"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Body Content -->
                <div class="nx-grid-card-body p-3.5 d-flex flex-column">
                    <!-- Rating & Product Count Line -->
                    <div class="d-flex justify-content-between align-items-center mb-1.5" style="font-size: 0.78rem;">
                        <div class="d-flex align-items-center gap-1">
                            <span class="text-warning"><i class="fas fa-star"></i></span>
                            <strong class="text-dark">{{ number_format($usaha->rating_avg, 1) }}</strong>
                            <span class="text-muted">({{ $reviewCount }})</span>
                        </div>
                        <span class="text-muted">
                            <i class="fas fa-box text-secondary me-1"></i>{{ $prodCount }} Produk
                        </span>
                    </div>

                    <!-- Store Name -->
                    <h5 class="fw-bold text-dark mb-1" style="font-size: 1.05rem; line-height: 1.35;">
                        <a href="{{ route('umkm.show', $usaha->id) }}" class="text-decoration-none text-dark hover-forest">
                            {{ $usaha->nama }}
                        </a>
                    </h5>

                    <!-- Owner & Verification Status -->
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small text-muted text-truncate" style="font-size: 0.78rem;">
                            <i class="fas fa-user-tie text-success me-1"></i>{{ $usaha->pemilik }}
                        </span>
                        <div>
                            {!! $usaha->status_badge !!}
                        </div>
                    </div>

                    <!-- Description -->
                    <p class="small text-muted mb-3 flex-grow-1" style="line-height: 1.45; font-size: 0.8rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 38px;">
                        {{ $usaha->deskripsi ?: 'Produk dan layanan berkualitas dari warga lingkungan RW 018.' }}
                    </p>

                    <!-- Contact & Address Snippet -->
                    <div class="small text-muted mb-3 pt-2 border-top" style="font-size: 0.76rem; border-color: var(--nx-border) !important;">
                        <div class="text-truncate mb-0.5">
                            <i class="fas fa-location-dot text-danger me-1.5" style="width: 14px;"></i>{{ $usaha->alamat }}
                        </div>
                        <div class="text-truncate">
                            <i class="fas fa-clock text-primary me-1.5" style="width: 14px;"></i>{{ $usaha->jam_operasional ?: '08:00 - 20:00 WIB' }}
                        </div>
                    </div>
                </div>

                <!-- Footer Action Buttons -->
                <div class="nx-grid-card-footer px-3.5 py-2.5 bg-light d-flex justify-content-between align-items-center" style="border-top: 1px solid var(--nx-border); border-radius: 0 0 14px 14px;">
                    <a href="{{ $usaha->whatsapp_url }}" target="_blank" class="btn btn-sm btn-outline-success px-2.5 py-1" style="border-radius: 8px; font-weight: 600; font-size: 0.78rem;">
                        <i class="fab fa-whatsapp me-1"></i>Chat WA
                    </a>
                    <a href="{{ route('umkm.show', $usaha->id) }}" class="btn btn-sm btn-primary px-3 py-1" style="border-radius: 8px; font-weight: 600; font-size: 0.78rem;">
                        Lihat Detail &rarr;
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card p-5 text-center text-muted" style="border-radius: 14px; border: 1px dashed var(--nx-border); background: #ffffff;">
                <div class="mb-3">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 72px; height: 72px; background: var(--nx-mint-soft); color: var(--nx-forest);">
                        <i class="fas fa-store-slash fa-2x"></i>
                    </span>
                </div>
                <h5 class="fw-bold mb-1 text-dark">Tidak Ada UMKM Ditemukan</h5>
                <p class="small text-muted mb-3" style="max-width: 480px; margin: 0 auto;">
                    Tidak ditemukan usaha warga yang cocok dengan kata kunci, kategori, wilayah RT, atau filter status yang dipilih.
                </p>
                <div>
                    <a href="{{ route('umkm.index') }}" class="btn btn-outline-secondary btn-sm px-3 py-1.5 me-2" style="border-radius: 8px; font-weight: 600;">
                        <i class="fas fa-rotate-left me-1"></i>Reset Filter
                    </a>
                    <a href="{{ route('umkm.create') }}" class="btn btn-primary btn-sm px-3 py-1.5" style="border-radius: 8px; font-weight: 700;">
                        <i class="fas fa-plus me-1"></i>Daftarkan Usaha
                    </a>
                </div>
            </div>
        </div>
    @endforelse
</div>

<!-- PAGINASI HALAMAN -->
@if($usahas->hasPages())
    <div class="mt-4 d-flex justify-content-center">
        {{ $usahas->links() }}
    </div>
@endif

@endsection
