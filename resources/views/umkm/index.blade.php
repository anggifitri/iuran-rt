@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card p-4 mb-4 border-0 shadow-sm umkm-hero" style="background: linear-gradient(135deg, #7c3aed, #22d3ee); color: #fff; overflow: hidden;">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <span class="badge bg-white text-primary rounded-pill mb-3">UMKM Unggulan RT {{ Auth::user()->rt_number ?? '00' }}</span>
                <h2 class="fw-bold">Direktori UMKM Warga</h2>
                <p class="mb-4" style="opacity: 0.9; max-width: 520px;">Cari dan belanja produk lokal buatan warga RT dengan mudah, cepat, dan transparan.</p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('umkm.create') }}" class="btn btn-light btn-lg text-primary">Tambah UMKM</a>
                    <a href="#daftar-umkm" class="btn btn-outline-light btn-lg">Lihat Direktori</a>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-block text-end">
                <div class="umkm-hero-image rounded-4 overflow-hidden shadow-lg" style="min-height: 280px; background: url('https://images.unsplash.com/photo-1517430816045-df4b7de11d1d?auto=format&fit=crop&w=900&q=80') center/cover no-repeat;"></div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4" id="daftar-umkm">
        <div class="col-sm-6 col-md-3">
            <div class="card p-3 umkm-stat-card">
                <h6 class="text-muted mb-2">Usaha Tercatat</h6>
                <h4 class="mb-0">{{ $totalUsaha }}</h4>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card p-3 umkm-stat-card">
                <h6 class="text-muted mb-2">Usaha Unggulan</h6>
                <h4 class="mb-0">{{ $featuredUsaha }}</h4>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card p-3 umkm-stat-card">
                <h6 class="text-muted mb-2">Kategori</h6>
                <h4 class="mb-0">{{ $totalKategori }}</h4>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card p-3 umkm-stat-card">
                <h6 class="text-muted mb-2">Verifikasi</h6>
                <h4 class="mb-0">Semua</h4>
            </div>
        </div>
    </div>

    @if($featuredItems->isNotEmpty())
        <div class="card p-4 mb-4 shadow-sm border-0 umkm-featured-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold mb-1">UMKM Unggulan</h5>
                    <p class="text-muted mb-0">Pilihan usaha teratas dari direktori UMKM saat ini.</p>
                </div>
                <span class="badge bg-primary rounded-pill">{{ $featuredItems->count() }} Terbaru</span>
            </div>

            <div id="umkmFeaturedCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    @foreach($featuredItems as $index => $item)
                        @php
                            $rawCat = strtolower(trim($item->kategori ?? 'default'));
                            if (strpos($rawCat, 'jasa') !== false) $fKey = 'jasa';
                            elseif (strpos($rawCat, 'kerajinan') !== false) $fKey = 'kerajinan';
                            elseif (strpos($rawCat, 'makanan') !== false || strpos($rawCat, 'kuliner') !== false) $fKey = 'makanan & minuman';
                            elseif (strpos($rawCat, 'perdagangan') !== false) $fKey = 'perdagangan';
                            else $fKey = $rawCat;

                            $fUrl = $categoryImages[$fKey] ?? $categoryImages['default'];
                            $fSrc = preg_match('/^https?:\/\//', $fUrl) ? $fUrl : asset($fUrl);
                        @endphp
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <div class="row g-3 align-items-center">
                                <div class="col-lg-5">
                                    <div class="rounded-4 overflow-hidden" style="height: 280px;">
                                        <img src="{{ $fSrc }}" alt="{{ $item->kategori ?? 'UMKM' }}" class="img-fluid w-100 h-100" style="object-fit: cover; min-height: 280px;">
                                    </div>
                                </div>
                                <div class="col-lg-7">
                                    <span class="badge bg-warning text-dark mb-3">{{ $item->kategori ?? 'Umum' }}</span>
                                    <h4 class="fw-bold">{{ $item->nama }}</h4>
                                    <p class="text-muted">{{ \Illuminate\Support\Str::limit($item->deskripsi ?? 'Deskripsi belum tersedia.', 140) }}</p>
                                    <p class="mb-1"><strong>Pemilik:</strong> {{ $item->pemilik ?? 'Tidak tercantum' }}</p>
                                    <p class="mb-1"><strong>Telepon:</strong> {{ $item->telepon ?? '-' }}</p>
                                    <p class="mb-0"><strong>Alamat:</strong> {{ $item->alamat ?? '-' }}</p>
                                        <div class="mt-4">
                                            <a href="#" data-url="{{ route('umkm.show', $item) }}?embed=1" class="btn btn-purple open-umkm">Kunjungi</a>
                                        </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-between mt-4">
                    <button class="carousel-control-prev btn btn-light rounded-circle shadow-sm p-2" type="button" data-bs-target="#umkmFeaturedCarousel" data-bs-slide="prev" style="width: 48px; height: 48px;">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Sebelumnya</span>
                    </button>
                    <button class="carousel-control-next btn btn-light rounded-circle shadow-sm p-2" type="button" data-bs-target="#umkmFeaturedCarousel" data-bs-slide="next" style="width: 48px; height: 48px;">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Berikutnya</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <div class="modal fade" id="umkmModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="background: transparent; border: none; box-shadow: none;">
          <div class="modal-body p-0">
            <div id="umkmModalContent"></div>
          </div>
        </div>
      </div>
    </div>

    @push('styles')
    <style>
      .modal-backdrop.show { background-color: rgba(0,0,0,0.35); }
      #umkmModal .modal-content { background: transparent; }
      #umkmModal .modal-body { padding: 0 20px 40px 20px; }
      #umkmModal .umkm-modal-content { background: rgba(255,255,255,0.96); border-radius: 14px; padding: 18px; }
      @media (max-width: 576px) { #umkmModal .modal-dialog { margin: 12px; } }
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

    <div class="card p-3 mb-4 shadow-sm umkm-search-card border-0">
        <div class="row g-3 align-items-center">
            <div class="col-lg-5">
                <h5 class="fw-bold mb-1">Direktori UMKM Warga</h5>
                <p class="text-muted mb-0">Cari dan belanja produk lokal buatan warga RT {{ Auth::user()->rt_number ?? '-' }}.</p>
            </div>
            <div class="col-lg-7">
                <form method="GET" action="{{ route('umkm.index') }}" class="row g-2">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search"></i></span>
                            <input type="text" name="q" value="{{ request('q') }}" class="form-control border-start-0" placeholder="Cari usaha atau produk...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select name="kategori" class="form-select">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ request('kategori') === $category ? 'selected' : '' }}>{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Cari</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if($usahas->isEmpty())
        <div class="card p-4 text-center text-muted">
            <h5 class="mb-2">Tidak ada usaha terdaftar.</h5>
            <p class="mb-0">Silakan tambah data UMKM atau hapus filter pencarian.</p>
        </div>
    @else
        <div class="row g-4">
            @foreach($usahas as $usaha)
                @php
                    $rawCat = strtolower(trim($usaha->kategori ?? 'default'));
                    if (strpos($rawCat, 'jasa') !== false) $imgKey = 'jasa';
                    elseif (strpos($rawCat, 'kerajinan') !== false) $imgKey = 'kerajinan';
                    elseif (strpos($rawCat, 'makanan') !== false || strpos($rawCat, 'kuliner') !== false) $imgKey = 'makanan & minuman';
                    elseif (strpos($rawCat, 'perdagangan') !== false) $imgKey = 'perdagangan';
                    else $imgKey = $rawCat;

                    $imgUrl = $categoryImages[$imgKey] ?? $categoryImages['default'];
                    $imgSrc = preg_match('/^https?:\/\//', $imgUrl) ? $imgUrl : asset($imgUrl);
                @endphp
                <div class="col-md-6 col-xl-4">
                    <div class="card umkm-card h-100 shadow-sm border-0">
                        <img src="{{ $imgSrc }}" class="card-img-top" alt="{{ $usaha->kategori ?? 'UMKM' }}" loading="lazy">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge bg-primary">{{ $usaha->kategori ?? 'Umum' }}</span>
                                <span class="badge bg-secondary">RT {{ Auth::user()->rt_number ?? '-' }}</span>
                            </div>
                            <h5 class="card-title">{{ $usaha->nama }}</h5>
                            <p class="text-secondary mb-3">{{ \Illuminate\Support\Str::limit($usaha->deskripsi ?? 'Deskripsi belum tersedia.', 110) }}</p>
                            <div class="text-secondary mb-4">
                                <p class="mb-2"><i class="fas fa-map-marker-alt me-2"></i>{{ $usaha->alamat ?? '-' }}</p>
                                <p class="mb-0"><i class="fas fa-phone me-2"></i>{{ $usaha->telepon ?? '-' }}</p>
                            </div>
                            <a href="#" data-url="{{ route('umkm.show', $usaha) }}?embed=1" class="btn btn-purple w-100 open-umkm">Kunjungi</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .umkm-hero {
        min-height: 320px;
        border-radius: 24px;
    }

    .umkm-hero-image {
        min-height: 280px;
    }

    .umkm-stat-card {
        border-radius: 18px;
        background: #fff;
        color: #0f172a;
        border: 1px solid rgba(226, 232, 240, 0.8);
    }

    .umkm-featured-card {
        border-radius: 24px;
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.8);
    }

    .umkm-search-card {
        border-radius: 22px;
        background: #ffffff;
        color: #0f172a;
        border: 1px solid rgba(226,232,240,0.9);
    }

    .umkm-card {
        border-radius: 24px;
        background: #ffffff;
    }

    .umkm-card .card-img-top {
        height: 220px;
        object-fit: cover;
        border-top-left-radius: 24px;
        border-top-right-radius: 24px;
    }

    .umkm-card .card-body {
        padding: 1.5rem;
    }

    .umkm-card h5 {
        color: #0f172a;
    }

    .umkm-card p {
        color: #6b7280;
    }

    .umkm-search-card .form-control,
    .umkm-search-card .form-select {
        border-radius: 999px;
        border: 1px solid rgba(226,232,240,0.9);
    }

    .umkm-search-card .input-group-text {
        background: transparent;
        border: none;
    }

    .btn-purple {
        background: linear-gradient(135deg, #7c3aed, #22d3ee);
        border: none;
        color: #fff;
        box-shadow: 0 10px 25px rgba(124, 58, 237, 0.18);
    }

    .btn-purple:hover {
        background: linear-gradient(135deg, #6d28d9, #0ea5e9);
    }

    .carousel-item {
        min-height: 320px;
    }

    .carousel-control-prev-icon,
    .carousel-control-next-icon {
        filter: invert(1);
    }
</style>
@endpush
