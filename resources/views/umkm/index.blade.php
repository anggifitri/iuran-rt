@extends('layouts.app')

@section('title', 'Direktori UMKM Warga — NexaNest')

@section('content')
<!-- HERO BANNER UMKM -->
<div class="nx-page-header">
    <div>
        <span class="badge rounded-pill mb-2 px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
            <i class="fas fa-store me-1"></i> Ekonomi Kreatif Lingkungan RW 018
        </span>
        <h1 class="nx-page-title">Direktori UMKM Warga</h1>
        <p class="nx-page-subtitle">Temukan dan dukung produk lokal, kuliner, kerajinan, serta jasa karya tetangga di lingkungan kita.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="#daftar-umkm" class="btn btn-outline-primary">
            <i class="fas fa-compass me-1"></i> Jelajahi Katalog
        </a>
        <a href="{{ route('umkm.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Daftarkan UMKM
        </a>
    </div>
</div>

<!-- STATS CARDS -->
<div class="row g-3 mb-4" id="daftar-umkm">
    <div class="col-sm-6 col-md-3">
        <div class="card p-3 h-100" style="background: var(--nx-mint-soft) !important; border-color: var(--nx-sage) !important;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <small class="text-muted fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem;">Usaha Tercatat</small>
                    <h3 class="mb-0 fw-bold" style="color: var(--nx-forest);">{{ $totalUsaha }}</h3>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width: 44px; height: 44px; background: var(--nx-mint); color: var(--nx-forest);">
                    <i class="fas fa-store"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-3">
        <div class="card p-3 h-100" style="background: var(--nx-powder-soft) !important; border-color: var(--nx-pastel-blue) !important;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <small class="text-muted fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem;">Usaha Unggulan</small>
                    <h3 class="mb-0 fw-bold" style="color: #2B5F8E;">{{ $featuredUsaha }}</h3>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width: 44px; height: 44px; background: var(--nx-powder); color: #2B5F8E;">
                    <i class="fas fa-award"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-3">
        <div class="card p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <small class="text-muted fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem;">Kategori Usaha</small>
                    <h3 class="mb-0 fw-bold" style="color: var(--nx-text);">{{ $totalKategori }}</h3>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width: 44px; height: 44px; background: var(--nx-mint-soft); color: var(--nx-forest);">
                    <i class="fas fa-tags"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-3">
        <div class="card p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <small class="text-muted fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem;">Status Mitra Warga</small>
                    <h3 class="mb-0 fw-bold" style="color: var(--nx-forest);">Terverifikasi</h3>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width: 44px; height: 44px; background: var(--nx-mint); color: var(--nx-forest);">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FEATURED CAROUSEL -->
@if($featuredItems->isNotEmpty())
    <div class="card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="fw-bold mb-0" style="font-size: 1.05rem;">
                    <i class="fas fa-star text-warning me-2"></i>Rekomendasi UMKM Unggulan Warga
                </h5>
                <small class="text-muted">Pilihan usaha warga teratas di lingkungan RW 018</small>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-secondary rounded-circle" type="button" data-bs-target="#umkmFeaturedCarousel" data-bs-slide="prev" style="width: 36px; height: 36px;">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="btn btn-sm btn-outline-secondary rounded-circle" type="button" data-bs-target="#umkmFeaturedCarousel" data-bs-slide="next" style="width: 36px; height: 36px;">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>

        <div id="umkmFeaturedCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                @foreach ($featuredItems as $index => $item)
                    @php
                        $fSrc = app(\App\Http\Controllers\UmkmController::class)->resolveCoverImage($item->cover_image ?? null, $item->kategori ?? null);
                    @endphp
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                        <div class="row g-4 align-items-center">
                            <div class="col-lg-5">
                                <div class="rounded-4 overflow-hidden shadow-sm" style="height: 230px;">
                                    <img src="{{ $fSrc }}" alt="{{ $item->nama }}" class="img-fluid w-100 h-100" style="object-fit: cover;">
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="d-flex gap-2 align-items-center mb-2">
                                    <span class="badge rounded-pill px-3 py-1" style="background: var(--nx-mint); color: var(--nx-forest);">{{ $item->kategori ?? 'Umum' }}</span>
                                    <span class="badge rounded-pill px-3 py-1" style="background: var(--nx-powder); color: #2B5F8E;">RT {{ $item->rt_number ?? '-' }}</span>
                                </div>
                                <h4 class="fw-bold mb-2" style="color: var(--nx-text);">{{ $item->nama }}</h4>
                                <p class="text-muted mb-3 small" style="line-height: 1.5;">
                                    {{ \Illuminate\Support\Str::limit($item->deskripsi ?? 'Deskripsi belum tersedia.', 150) }}
                                </p>

                                <div class="row g-2 small mb-3">
                                    <div class="col-sm-6">
                                        <span class="text-muted"><i class="fas fa-user-tie text-success me-1"></i>Pemilik:</span>
                                        <strong>{{ $item->pemilik ?? 'Warga RW 018' }}</strong>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="text-muted"><i class="fas fa-phone text-success me-1"></i>Kontak:</span>
                                        <strong>{{ $item->telepon ?? '-' }}</strong>
                                    </div>
                                    <div class="col-12">
                                        <span class="text-muted"><i class="fas fa-location-dot text-success me-1"></i>Alamat:</span>
                                        <strong>{{ $item->alamat ?? '-' }}</strong>
                                    </div>
                                </div>

                                <a href="#" data-url="{{ route('umkm.show', $item) }}?embed=1" class="btn btn-primary btn-sm px-4 open-umkm">
                                    <i class="fas fa-store me-1"></i> Kunjungi Usaha
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

<!-- SEARCH & CATEGORY FILTER -->
<div class="card p-3 mb-3">
    <form method="GET" id="searchForm" action="{{ route('umkm.index') }}" class="row g-2 align-items-center">
        @if(request('kategori'))
            <input type="hidden" name="kategori" value="{{ request('kategori') }}">
        @endif
        <div class="col-md-9">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control border-start-0"
                       placeholder="Cari usaha, warung, kuliner, produk, atau jasa warga...">
            </div>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100 py-2">
                <i class="fas fa-search me-1"></i> Cari Usaha
            </button>
        </div>
    </form>
</div>

<!-- HORIZONTAL CATEGORY PILLS -->
<div class="d-flex flex-wrap gap-2 mb-4">
    <a href="{{ route('umkm.index', array_filter(['q' => request('q')])) }}"
       class="btn btn-sm rounded-pill px-3 py-2 {{ empty(request('kategori')) ? 'btn-primary' : 'btn-outline-secondary' }}">
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
        <a href="{{ route('umkm.index', array_filter(['kategori' => $category, 'q' => request('q')])) }}"
           class="btn btn-sm rounded-pill px-3 py-2 {{ request('kategori') === $category ? 'btn-primary' : 'btn-outline-secondary' }}">
            <i class="fas {{ $catIcon }} me-1"></i> {{ $category }}
        </a>
    @endforeach
</div>

<!-- GRID DIRECTORY LIST -->
@if($usahas->isEmpty())
    <div class="card p-5 text-center">
        <div class="mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center"
             style="width: 60px; height: 60px; background: var(--nx-mint-soft); color: var(--nx-forest);">
            <i class="fas fa-store-slash fa-lg"></i>
        </div>
        <h5 class="fw-bold mb-1">Tidak Ada UMKM Ditemukan</h5>
        <p class="text-muted small mb-0">Tidak ada data UMKM yang cocok dengan kata kunci atau filter kategori tersebut.</p>
    </div>
@else
    <div class="row g-4">
        @foreach ($usahas as $usaha)
            @php
                $imgSrc = app(\App\Http\Controllers\UmkmController::class)->resolveCoverImage($usaha->cover_image ?? null, $usaha->kategori ?? null);
            @endphp
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 overflow-hidden card-hover">
                    <div class="position-relative overflow-hidden" style="height: 195px;">
                        <img src="{{ $imgSrc }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $usaha->nama }}" loading="lazy">
                        <span class="badge rounded-pill position-absolute top-0 start-0 m-3 px-3 py-1.5 shadow-sm"
                              style="background: var(--nx-forest); color: #fff; font-size: 0.72rem;">
                            {{ $usaha->kategori ?? 'Umum' }}
                        </span>
                        <span class="badge rounded-pill position-absolute top-0 end-0 m-3 px-3 py-1.5 shadow-sm"
                              style="background: rgba(255,255,255,0.92); color: var(--nx-text); font-size: 0.72rem;">
                            RT {{ $usaha->rt_number ?? '-' }}
                        </span>
                    </div>

                    <div class="card-body d-flex flex-column p-4">
                        <h5 class="fw-bold mb-1" style="color: var(--nx-text); font-size: 1.05rem;">{{ $usaha->nama }}</h5>
                        <p class="text-muted small mb-3 flex-grow-1" style="line-height: 1.5;">
                            {{ \Illuminate\Support\Str::limit($usaha->deskripsi ?? 'Deskripsi belum tersedia.', 100) }}
                        </p>

                        <div class="small text-muted mb-3">
                            <div class="text-truncate mb-1"><i class="fas fa-location-dot text-success me-2"></i>{{ $usaha->alamat ?? '-' }}</div>
                            <div><i class="fas fa-phone text-success me-2"></i>{{ $usaha->telepon ?? '-' }}</div>
                        </div>

                        <a href="#" data-url="{{ route('umkm.show', $usaha) }}?embed=1" class="btn btn-primary w-100 open-umkm">
                            <i class="fas fa-bag-shopping me-1"></i> Kunjungi Toko
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

<!-- EMBED DETAILS MODAL -->
<div class="modal fade" id="umkmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="background: transparent !important; border: none !important; box-shadow: none !important;">
            <div class="modal-body p-0">
                <div id="umkmModalContent"></div>
            </div>
        </div>
    </div>
</div>
@endsection

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
                    container.innerHTML = '<div class="card p-4 shadow-lg">'+html+'</div>';
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
