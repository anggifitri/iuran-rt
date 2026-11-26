@extends('layouts.app')

@section('title', $umkm->nama . ' — Direktori UMKM NexaNest')

@section('content')
<!-- BREADCRUMB & BACK BUTTON -->
<div class="mb-3">
    <a href="{{ route('umkm.index') }}" class="btn btn-sm btn-outline-secondary px-3 py-1.5" style="border-radius: 8px; font-weight: 600; font-size: 0.82rem;">
        <i class="fas fa-arrow-left me-1.5"></i>Kembali ke Direktori UMKM
    </a>
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

<!-- HERO STORE PROFILE CARD -->
@php
    $coverSrc = app(\App\Http\Controllers\UmkmController::class)->resolveCoverImage($umkm->cover_image, $umkm->kategori);
@endphp
<div class="card overflow-hidden mb-4 shadow-xs" style="border-radius: 16px; border: 1px solid var(--nx-border); background: #ffffff;">
    <!-- Banner Cover -->
    <div class="position-relative" style="height: 240px; background: url('{{ $coverSrc }}') center/cover no-repeat;">
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(to bottom, rgba(0,0,0,0.2) 0%, rgba(38,52,73,0.75) 100%);"></div>
        
        <!-- Top Controls: RT, Verification, Favorite, Share -->
        <div class="position-absolute top-0 end-0 m-3 d-flex gap-2 align-items-center">
            @if(Auth::check())
                <form action="{{ route('umkm.toggle_favorit', $umkm->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm rounded-pill px-3 py-1.5 shadow-sm fw-bold" style="background: rgba(255,255,255,0.95); border: none; font-size: 0.78rem;">
                        <i class="fas fa-heart {{ $umkm->is_favorited ? 'text-danger' : 'text-muted' }} me-1"></i>
                        {{ $umkm->is_favorited ? 'Disimpan di Favorit' : 'Simpan Favorit' }}
                    </button>
                </form>
            @endif

            <button type="button" class="btn btn-sm rounded-pill px-3 py-1.5 shadow-sm fw-bold" style="background: rgba(255,255,255,0.95); border: none; font-size: 0.78rem;" onclick="copyShareUrl()">
                <i class="fas fa-share-nodes text-primary me-1"></i>Bagikan
            </button>
        </div>

        <!-- Store Info Overlay (Bottom) -->
        <div class="position-absolute bottom-0 start-0 p-4 text-white w-100">
            <div class="d-flex flex-wrap align-items-center gap-2 mb-1.5">
                <span class="badge rounded-pill px-2.5 py-1" style="background: var(--nx-mint); color: var(--nx-forest); font-weight: 700; font-size: 0.72rem;">
                    {{ $umkm->kategori }}
                </span>
                <span class="badge rounded-pill px-2.5 py-1" style="background: rgba(255,255,255,0.25); color: #fff; font-weight: 700; font-size: 0.72rem; backdrop-filter: blur(4px);">
                    RT {{ $umkm->rt_number }} RW 018
                </span>
                {!! $umkm->status_badge !!}
            </div>
            <h1 class="fw-bold mb-1 text-white" style="font-size: 1.7rem; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">{{ $umkm->nama }}</h1>
            <p class="mb-0 text-white-50 small">
                <i class="fas fa-user-tie me-1 text-white"></i>Pemilik: <strong>{{ $umkm->pemilik }}</strong> · 
                <i class="fas fa-location-dot ms-2 me-1 text-white"></i>{{ $umkm->alamat }}
            </p>
        </div>
    </div>

    <!-- Quick Store Info Bar & Action CTAs -->
    <div class="p-3.5 bg-white d-flex flex-wrap justify-content-between align-items-center gap-3 border-top" style="border-color: var(--nx-border) !important;">
        <!-- Left: Rating & Jam Operasional -->
        <div class="d-flex flex-wrap align-items-center gap-4 small">
            <div class="d-flex align-items-center gap-2">
                <span class="text-warning fs-5">{!! $umkm->rating_stars !!}</span>
                <div>
                    <strong class="text-dark fs-6">{{ number_format($umkm->rating_avg, 1) }}</strong>
                    <span class="text-muted">/ 5.0 ({{ $totalReviewCount }} Ulasan)</span>
                </div>
            </div>
            <div class="border-start ps-3 d-none d-md-block text-muted">
                <span class="d-block" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 600;">Jam Operasional:</span>
                <strong class="text-dark">{{ $umkm->jam_operasional ?: '08:00 - 20:00 WIB' }}</strong>
            </div>
            <div class="border-start ps-3 d-none d-md-block text-muted">
                <span class="d-block" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 600;">Nomor Kontak:</span>
                <strong class="text-dark">{{ $umkm->telepon }}</strong>
            </div>
        </div>

        <!-- Right: Primary CTA Buttons -->
        <div class="d-flex flex-wrap align-items-center gap-2">
            @if($isAdmin)
                <!-- Verifikasi Dropdown for Admin -->
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle px-3 py-2 fw-bold" type="button" data-bs-toggle="dropdown" style="border-radius: 10px;">
                        <i class="fas fa-shield-check me-1"></i>Verifikasi Usaha
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius: 10px; font-size: 0.85rem;">
                        <li><h6 class="dropdown-header small fw-bold text-uppercase">Status Verifikasi RT/RW</h6></li>
                        <li>
                            <form action="{{ route('umkm.toggle_verifikasi', $umkm->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status_verifikasi" value="terverifikasi">
                                <button type="submit" class="dropdown-item py-2 text-success {{ $umkm->status_verifikasi === 'terverifikasi' ? 'active' : '' }}">
                                    <i class="fas fa-check-circle me-2"></i>Terverifikasi Resmi
                                </button>
                            </form>
                        </li>
                        <li>
                            <form action="{{ route('umkm.toggle_verifikasi', $umkm->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status_verifikasi" value="menunggu">
                                <button type="submit" class="dropdown-item py-2 text-warning {{ $umkm->status_verifikasi === 'menunggu' ? 'active' : '' }}">
                                    <i class="fas fa-clock me-2"></i>Menunggu Verifikasi
                                </button>
                            </form>
                        </li>
                        <li>
                            <form action="{{ route('umkm.toggle_verifikasi', $umkm->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status_verifikasi" value="ditolak">
                                <button type="submit" class="dropdown-item py-2 text-danger {{ $umkm->status_verifikasi === 'ditolak' ? 'active' : '' }}">
                                    <i class="fas fa-times-circle me-2"></i>Tolak Verifikasi
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endif

            @if($isOwner || $isAdmin)
                <button type="button" class="btn btn-sm btn-outline-primary px-3 py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#modalEditUmkm" style="border-radius: 10px;">
                    <i class="fas fa-pen-to-square me-1"></i>Edit Profil
                </button>
            @endif

            <a href="{{ $whatsappUrl }}" target="_blank" class="btn btn-sm btn-success px-4 py-2 fw-bold shadow-xs" style="border-radius: 10px; background: #25D366; border-color: #25D366;">
                <i class="fab fa-whatsapp fa-lg me-1.5"></i>Hubungi Penjual
            </a>
        </div>
    </div>
</div>

<!-- NAV TABS UTAMA: PRODUK, ULASAN, DAN KELOLA USAHA -->
<ul class="nav nav-pills mb-4 gap-2" id="umkmTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active px-3.5 py-2 fw-bold" id="produk-tab" data-bs-toggle="pill" data-bs-target="#tab-produk" type="button" role="tab" style="border-radius: 10px; font-size: 0.88rem;">
            <i class="fas fa-box-open me-1.5"></i>1. Katalog Produk &amp; Layanan
            <span class="badge rounded-pill bg-white text-dark ms-1.5 px-2" style="font-size: 0.72rem;">{{ $umkm->produks->count() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link px-3.5 py-2 fw-bold" id="ulasan-tab" data-bs-toggle="pill" data-bs-target="#tab-ulasan" type="button" role="tab" style="border-radius: 10px; font-size: 0.88rem;">
            <i class="fas fa-comments me-1.5"></i>2. Rating &amp; Ulasan Pembeli
            <span class="badge rounded-pill bg-white text-dark ms-1.5 px-2" style="font-size: 0.72rem;">{{ $totalReviewCount }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link px-3.5 py-2 fw-bold" id="profil-tab" data-bs-toggle="pill" data-bs-target="#tab-profil" type="button" role="tab" style="border-radius: 10px; font-size: 0.88rem;">
            <i class="fas fa-circle-info me-1.5"></i>3. Profil &amp; Galeri Usaha
        </button>
    </li>
    @if($isOwner || $isAdmin)
    <li class="nav-item ms-auto" role="presentation">
        <button class="nav-link px-3.5 py-2 fw-bold btn-outline-primary" id="kelola-tab" data-bs-toggle="pill" data-bs-target="#tab-kelola" type="button" role="tab" style="border-radius: 10px; font-size: 0.88rem; background: var(--nx-powder); color: #1E4B6D;">
            <i class="fas fa-sliders me-1.5"></i>Dashboard Pengelola
        </button>
    </li>
    @endif
</ul>

<div class="tab-content" id="umkmTabContent">
    <!-- ==================== TAB 1: KATALOG PRODUK & LAYANAN ==================== -->
    <div class="tab-pane fade show active" id="tab-produk" role="tabpanel">
        <div class="card p-4 shadow-xs" style="border-radius: 14px; border: 1px solid var(--nx-border); background: #ffffff;">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                <div>
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.1rem;">
                        <i class="fas fa-utensils text-success me-2"></i>Daftar Menu, Produk &amp; Layanan
                    </h5>
                    <small class="text-muted">Pilih produk dan pesan langsung secara mudah melalui WhatsApp resmi penjual.</small>
                </div>
                @if($isOwner || $isAdmin)
                <div>
                    <button type="button" class="btn btn-sm btn-primary px-3 py-1.5 fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahProduk" style="border-radius: 8px;">
                        <i class="fas fa-plus me-1.5"></i>Tambah Produk Baru
                    </button>
                </div>
                @endif
            </div>

            <div class="row g-3">
                @forelse($umkm->produks as $produk)
                    @php
                        $pesanText = "Halo {$umkm->pemilik}, saya ingin memesan '{$produk->nama_produk}' ({$produk->harga_formatted}) dari katalog NexaNest. Apakah masih tersedia?";
                        $orderWaUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $umkm->whatsapp_url ? $umkm->telepon : '628123456789') . "?text=" . urlencode($pesanText);
                    @endphp
                    <div class="col-lg-6 col-12">
                        <div class="card p-3 shadow-xs h-100 border transition-card" style="border-radius: 12px; border-color: var(--nx-border) !important; background: {{ $produk->is_tersedia ? '#ffffff' : '#F9FAFB' }};">
                            <div class="d-flex align-items-center gap-3">
                                <!-- Foto Produk -->
                                <div class="position-relative flex-shrink-0 rounded-3 overflow-hidden" style="width: 88px; height: 88px; background: #E5EBE6;">
                                    <img src="{{ $produk->foto_url }}" alt="{{ $produk->nama_produk }}" class="w-100 h-100" style="object-fit: cover;">
                                    @if(!$produk->is_tersedia)
                                        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(0,0,0,0.55);">
                                            <span class="badge bg-danger text-white px-2 py-0.5" style="font-size: 0.65rem;">Habis</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Info Produk -->
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">{{ $produk->nama_produk }}</h6>
                                        <span class="fw-bold" style="color: var(--nx-forest); font-size: 0.95rem;">
                                            {{ $produk->harga_formatted }}
                                        </span>
                                    </div>
                                    <p class="text-muted small mb-2" style="font-size: 0.78rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        {{ $produk->deskripsi ?: 'Produk berkualitas karya warga lingkungan.' }}
                                    </p>

                                    <!-- Actions -->
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge rounded-pill px-2 py-0.5" style="background: {{ $produk->is_tersedia ? 'var(--nx-mint-soft)' : '#F3F4F6' }}; color: {{ $produk->is_tersedia ? 'var(--nx-forest)' : '#6B7280' }}; font-size: 0.68rem; font-weight: 700;">
                                            {{ $produk->is_tersedia ? 'Tersedia' : 'Stok Habis' }}
                                        </span>

                                        <div class="d-flex gap-1.5 align-items-center">
                                            @if($isOwner || $isAdmin)
                                                <button type="button" class="btn btn-sm btn-outline-secondary px-2 py-0.5" style="border-radius: 6px; font-size: 0.72rem;" data-bs-toggle="modal" data-bs-target="#modalEditProduk{{ $produk->id }}" title="Edit Produk">
                                                    <i class="fas fa-pen"></i>
                                                </button>
                                                <form action="{{ route('umkm.produk.destroy', [$umkm->id, $produk->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus produk ini dari katalog?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-0.5" style="border-radius: 6px; font-size: 0.72rem;" title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            @if($produk->is_tersedia)
                                                <a href="{{ $orderWaUrl }}" target="_blank" class="btn btn-sm btn-outline-success px-2.5 py-0.5 fw-bold" style="border-radius: 6px; font-size: 0.75rem;">
                                                    <i class="fab fa-whatsapp me-1"></i>Pesan
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Edit Produk -->
                        @if($isOwner || $isAdmin)
                        <div class="modal fade" id="modalEditProduk{{ $produk->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.12);">
                                    <form action="{{ route('umkm.produk.update', [$umkm->id, $produk->id]) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header px-4 pt-4 pb-2 border-0">
                                            <h5 class="modal-title fw-bold text-dark" style="font-size: 1.1rem;">Edit Produk</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body px-4 py-3">
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Nama Produk <span class="text-danger">*</span></label>
                                                <input type="text" name="nama_produk" class="form-control" required value="{{ $produk->nama_produk }}" style="border-radius: 10px; height: 40px;">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Harga Produk (Rp) <span class="text-danger">*</span></label>
                                                <input type="number" name="harga" class="form-control" required value="{{ (int)$produk->harga }}" style="border-radius: 10px; height: 40px;">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Deskripsi Produk</label>
                                                <textarea name="deskripsi" rows="2" class="form-control" style="border-radius: 10px; font-size: 0.85rem;">{{ $produk->deskripsi }}</textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Ganti Foto Produk (Opsional)</label>
                                                <input type="file" name="foto" class="form-control" accept="image/*" style="border-radius: 10px;">
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="is_tersedia" value="1" id="switchStok{{ $produk->id }}" {{ $produk->is_tersedia ? 'checked' : '' }}>
                                                <label class="form-check-label small fw-bold" for="switchStok{{ $produk->id }}">Status Produk Tersedia</label>
                                            </div>
                                        </div>
                                        <div class="modal-footer px-4 pb-4 pt-2 border-0">
                                            <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
                                            <button type="submit" class="btn btn-primary px-4 fw-bold" style="border-radius: 10px;">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5 text-muted">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2" style="width: 60px; height: 60px; background: var(--nx-mint-soft); color: var(--nx-forest);">
                                <i class="fas fa-box-open fa-lg"></i>
                            </span>
                            <h6 class="fw-bold mb-1 text-dark">Katalog Produk Belum Ditambahkan</h6>
                            <p class="small text-muted mb-3">Pemilik usaha belum mengunggah daftar produk atau layanan ke dalam sistem.</p>
                            @if($isOwner || $isAdmin)
                                <button type="button" class="btn btn-sm btn-primary px-3 py-1.5 fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahProduk" style="border-radius: 8px;">
                                    <i class="fas fa-plus me-1.5"></i>Tambah Produk Pertama
                                </button>
                            @endif
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- ==================== TAB 2: RATING & ULASAN PEMBELI ==================== -->
    <div class="tab-pane fade" id="tab-ulasan" role="tabpanel">
        <div class="row g-4">
            <!-- Left: Ringkasan Rating & Form Tulis Ulasan -->
            <div class="col-lg-4 col-12">
                <!-- Card Rangkuman Skor Rating -->
                <div class="card p-4 mb-4 shadow-xs" style="border-radius: 14px; border: 1px solid var(--nx-border); background: #ffffff;">
                    <h6 class="fw-bold text-dark mb-3" style="font-size: 0.95rem;">
                        <i class="fas fa-star text-warning me-1.5"></i>Rangkuman Ulasan Pembeli
                    </h6>

                    <div class="text-center py-2 mb-3">
                        <h2 class="fw-bold mb-1" style="color: var(--nx-forest); font-size: 2.5rem;">
                            {{ number_format($umkm->rating_avg, 1) }}
                        </h2>
                        <div class="text-warning fs-5 mb-1">
                            {!! $umkm->rating_stars !!}
                        </div>
                        <small class="text-muted d-block">Berdasarkan <strong>{{ $totalReviewCount }}</strong> ulasan pembeli terverifikasi</small>
                    </div>

                    <!-- Breakdown Bintang 5 sampai 1 -->
                    <div class="d-flex flex-column gap-1.5" style="font-size: 0.78rem;">
                        @for($s = 5; $s >= 1; $s--)
                            @php
                                $cnt = $breakdown[$s] ?? 0;
                                $pct = $totalReviewCount > 0 ? round(($cnt / $totalReviewCount) * 100) : 0;
                            @endphp
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-dark fw-bold" style="width: 45px;">{{ $s }} Bintang</span>
                                <div class="progress flex-grow-1" style="height: 6px; border-radius: 10px; background: #E5EBE6;">
                                    <div class="progress-bar" style="width: {{ $pct }}%; background: {{ $s >= 4 ? '#285943' : ($s == 3 ? '#D97706' : '#DC2626') }};"></div>
                                </div>
                                <span class="text-muted" style="width: 28px; text-align: right;">{{ $cnt }}</span>
                            </div>
                        @endfor
                    </div>
                </div>

                <!-- Form Tulis / Edit Ulasan (Khusus Pembeli/Warga) -->
                @if(Auth::check() && !$isOwner)
                    <div class="card p-4 shadow-xs" style="border-radius: 14px; border: 1px solid var(--nx-mint); background: #ffffff;">
                        <h6 class="fw-bold text-dark mb-2" style="font-size: 0.95rem;">
                            <i class="fas fa-pen-nib text-primary me-1.5"></i>
                            {{ $myReview ? 'Perbarui Ulasan Anda' : 'Tulis Ulasan & Pengalaman' }}
                        </h6>
                        <p class="small text-muted mb-3" style="font-size: 0.78rem;">Bagikan ulasan jujur setelah berbelanja untuk meningkatkan kepercayaan sesama warga.</p>

                        <form action="{{ $myReview ? route('umkm.ulasan.update', [$umkm->id, $myReview->id]) : route('umkm.ulasan.store', $umkm->id) }}" method="POST">
                            @csrf
                            @if($myReview)
                                @method('PUT')
                            @endif

                            <!-- Pilih Bintang -->
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark">Beri Rating Usaha <span class="text-danger">*</span></label>
                                <select name="rating" class="form-select form-select-sm fw-bold" required style="border-radius: 8px; height: 38px;">
                                    <option value="5" {{ ($myReview ? $myReview->rating : 5) == 5 ? 'selected' : '' }}>⭐⭐⭐⭐⭐ 5 Bintang (Sangat Puas)</option>
                                    <option value="4" {{ ($myReview ? $myReview->rating : 0) == 4 ? 'selected' : '' }}>⭐⭐⭐⭐ 4 Bintang (Puas / Bagus)</option>
                                    <option value="3" {{ ($myReview ? $myReview->rating : 0) == 3 ? 'selected' : '' }}>⭐⭐⭐ 3 Bintang (Cukup)</option>
                                    <option value="2" {{ ($myReview ? $myReview->rating : 0) == 2 ? 'selected' : '' }}>⭐⭐ 2 Bintang (Kurang Puas)</option>
                                    <option value="1" {{ ($myReview ? $myReview->rating : 0) == 1 ? 'selected' : '' }}>⭐ 1 Bintang (Kecewa)</option>
                                </select>
                            </div>

                            <!-- Komentar Ulasan -->
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark">Pengalaman Belanja / Komentar <span class="text-danger">*</span></label>
                                <textarea name="komentar" rows="3" class="form-control" required placeholder="Ceritakan kualitas rasa, produk, keramahan penjual, atau kecepatan pengiriman..." style="border-radius: 8px; font-size: 0.82rem; line-height: 1.45;">{{ $myReview ? $myReview->komentar : '' }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary btn-sm w-100 py-2 fw-bold" style="border-radius: 8px;">
                                <i class="fas fa-paper-plane me-1"></i>{{ $myReview ? 'Simpan Perubahan Ulasan' : 'Kirim Ulasan Pembeli' }}
                            </button>
                        </form>
                    </div>
                @elseif(!Auth::check())
                    <div class="card p-3.5 text-center text-muted" style="border-radius: 14px; border: 1px dashed var(--nx-border); background: #ffffff;">
                        <i class="fas fa-lock fa-2x mb-2 text-muted" style="opacity: 0.35;"></i>
                        <p class="small mb-2">Silakan masuk ke akun warga Anda untuk memberikan rating dan ulasan pada usaha ini.</p>
                        <a href="{{ route('login') }}" class="btn btn-sm btn-primary px-3 fw-bold" style="border-radius: 8px;">Masuk Akun</a>
                    </div>
                @endif
            </div>

            <!-- Right: Daftar Ulasan & Komentar Pembeli -->
            <div class="col-lg-8 col-12">
                <div class="card p-4 shadow-xs" style="border-radius: 14px; border: 1px solid var(--nx-border); background: #ffffff;">
                    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom" style="border-color: var(--nx-border) !important;">
                        <div>
                            <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.05rem;">
                                <i class="fas fa-comments text-success me-2"></i>Ulasan dari Warga Pembeli
                            </h5>
                            <small class="text-muted">Testimoni nyata dari sesama warga RW 018.</small>
                        </div>
                        <span class="badge rounded-pill px-2.5 py-1" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-weight: 700; font-size: 0.72rem;">
                            {{ $totalReviewCount }} Ulasan Terbit
                        </span>
                    </div>

                    <div class="d-flex flex-column gap-3.5">
                        @forelse($umkm->visibleUlasans as $ulasan)
                            <div class="p-3.5 rounded-3 border bg-white" style="border-color: var(--nx-border) !important;">
                                <!-- Reviewer Header: Name, Verified Badge, Rating, Date -->
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-xs"
                                             style="width: 36px; height: 36px; background: var(--nx-forest); font-size: 0.85rem;">
                                            {{ strtoupper(substr($ulasan->nama_pembeli, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark d-flex align-items-center gap-1.5" style="font-size: 0.9rem;">
                                                {{ $ulasan->nama_pembeli }}
                                                @if($ulasan->is_verified_purchase)
                                                    <span class="badge rounded-pill px-2 py-0.5" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-size: 0.65rem; font-weight: 700;">
                                                        <i class="fas fa-check-circle me-1 text-success"></i>Pembelian Terverifikasi
                                                    </span>
                                                @endif
                                            </div>
                                            <small class="text-muted" style="font-size: 0.72rem;">
                                                {{ $ulasan->created_at->translatedFormat('d F Y, H:i') }} WIB
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Stars & Report Dropdown -->
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="text-warning small">
                                            {!! $ulasan->rating_stars !!}
                                        </div>

                                        @if(Auth::check())
                                            <div class="dropdown">
                                                <button class="btn btn-sm text-muted p-0" type="button" data-bs-toggle="dropdown" style="border: none;">
                                                    <i class="fas fa-ellipsis-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius: 8px; font-size: 0.78rem;">
                                                    @if(Auth::id() === $ulasan->user_id)
                                                        <li>
                                                            <a href="#tab-ulasan" class="dropdown-item py-1.5 text-primary">
                                                                <i class="fas fa-pen me-1.5"></i>Edit Ulasan Saya
                                                            </a>
                                                        </li>
                                                    @endif
                                                    @if($isOwner)
                                                        <li>
                                                            <button type="button" class="dropdown-item py-1.5 text-success" data-bs-toggle="modal" data-bs-target="#modalBalasUlasan{{ $ulasan->id }}">
                                                                <i class="fas fa-reply me-1.5"></i>{{ $ulasan->balasan_penjual ? 'Ubah Balasan' : 'Balas Ulasan' }}
                                                            </button>
                                                        </li>
                                                    @endif
                                                    <li>
                                                        <button type="button" class="dropdown-item py-1.5 text-danger" data-bs-toggle="modal" data-bs-target="#modalLaporUlasan{{ $ulasan->id }}">
                                                            <i class="fas fa-flag me-1.5"></i>Laporkan Ulasan
                                                        </button>
                                                    </li>
                                                    @if($isAdminRw)
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form action="{{ route('umkm.ulasan.moderate', [$umkm->id, $ulasan->id]) }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="action" value="hide">
                                                                <button type="submit" class="dropdown-item py-1.5 text-secondary">
                                                                    <i class="fas fa-eye-slash me-1.5"></i>Sembunyikan Ulasan
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Review Comment Body -->
                                <p class="text-dark small mb-2" style="line-height: 1.55; font-size: 0.85rem;">
                                    {{ $ulasan->komentar }}
                                </p>

                                <!-- Seller Response / Balasan Pemilik UMKM -->
                                @if($ulasan->balasan_penjual)
                                    <div class="p-3 rounded-3 mt-2.5" style="background: #F7F9F7; border-left: 3px solid var(--nx-forest);">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <strong class="text-dark small" style="font-size: 0.78rem;">
                                                <i class="fas fa-store text-success me-1"></i>Tanggapan Penjual ({{ $umkm->nama }})
                                            </strong>
                                            <small class="text-muted" style="font-size: 0.68rem;">
                                                {{ $ulasan->balasan_at ? $ulasan->balasan_at->translatedFormat('d M Y') : '' }}
                                            </small>
                                        </div>
                                        <p class="mb-0 text-muted small" style="line-height: 1.45; font-size: 0.8rem;">
                                            {{ $ulasan->balasan_penjual }}
                                        </p>
                                    </div>
                                @elseif($isOwner)
                                    <div class="mt-2 text-end">
                                        <button type="button" class="btn btn-sm btn-outline-success px-2.5 py-1 fw-bold" style="border-radius: 6px; font-size: 0.72rem;" data-bs-toggle="modal" data-bs-target="#modalBalasUlasan{{ $ulasan->id }}">
                                            <i class="fas fa-reply me-1"></i>Balas Ulasan Ini
                                        </button>
                                    </div>
                                @endif
                            </div>

                            <!-- Modal Balas Ulasan -->
                            @if($isOwner || $isAdmin)
                            <div class="modal fade" id="modalBalasUlasan{{ $ulasan->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content" style="border-radius: 16px; border: none;">
                                        <form action="{{ route('umkm.ulasan.reply', [$umkm->id, $ulasan->id]) }}" method="POST">
                                            @csrf
                                            <div class="modal-header px-4 pt-4 pb-2 border-0">
                                                <h5 class="modal-title fw-bold text-dark" style="font-size: 1.1rem;">
                                                    <i class="fas fa-reply text-success me-2"></i>Tanggapan Penjual
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body px-4 py-3">
                                                <div class="p-2.5 rounded-3 bg-light border small text-dark mb-3">
                                                    <div class="text-muted" style="font-size: 0.72rem;">Ulasan dari {{ $ulasan->nama_pembeli }}:</div>
                                                    <em>"{{ $ulasan->komentar }}"</em>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label small fw-bold">Tulis Balasan Ramah <span class="text-danger">*</span></label>
                                                    <textarea name="balasan_penjual" rows="3" class="form-control" required placeholder="Contoh: Terima kasih banyak atas dukungannya! Kami tunggu pesanan berikutnya..." style="border-radius: 10px; font-size: 0.85rem;">{{ $ulasan->balasan_penjual }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer px-4 pb-4 pt-2 border-0">
                                                <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
                                                <button type="submit" class="btn btn-primary px-4 fw-bold" style="border-radius: 10px;">Kirim Balasan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Modal Laporkan Ulasan -->
                            <div class="modal fade" id="modalLaporUlasan{{ $ulasan->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content" style="border-radius: 16px; border: none;">
                                        <form action="{{ route('umkm.ulasan.report', [$umkm->id, $ulasan->id]) }}" method="POST">
                                            @csrf
                                            <div class="modal-header px-4 pt-4 pb-2 border-0">
                                                <h5 class="modal-title fw-bold text-danger" style="font-size: 1.1rem;">
                                                    <i class="fas fa-flag me-2"></i>Laporkan Ulasan Tidak Sesuai
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body px-4 py-3">
                                                <p class="small text-muted mb-3">Laporan akan diteruskan kepada Pengurus RW untuk ditinjau dan dimoderasi secara adil.</p>
                                                <div class="mb-2">
                                                    <label class="form-label small fw-bold">Alasan Pelaporan <span class="text-danger">*</span></label>
                                                    <select name="report_reason" class="form-select mb-2" required style="border-radius: 10px; height: 40px;">
                                                        <option value="Kata-kata kasar atau tidak pantas">Kata-kata kasar atau tidak pantas</option>
                                                        <option value="Informasi palsu / fitnah / spam">Informasi palsu / fitnah / spam</option>
                                                        <option value="Mengandung promosi produk lain">Mengandung promosi produk lain</option>
                                                        <option value="Lainnya">Lainnya</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer px-4 pb-4 pt-2 border-0">
                                                <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
                                                <button type="submit" class="btn btn-danger px-4 fw-bold" style="border-radius: 10px;">Kirim Laporan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-comment-dots fa-3x mb-2 text-muted" style="opacity: 0.35;"></i>
                                <h6 class="fw-bold mb-1 text-dark">Belum Ada Ulasan Pembeli</h6>
                                <p class="small text-muted mb-0">Jadilah warga pertama yang memberikan ulasan dan rating untuk usaha ini!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== TAB 3: PROFIL & GALERI USAHA ==================== -->
    <div class="tab-pane fade" id="tab-profil" role="tabpanel">
        <div class="row g-4">
            <div class="col-lg-8 col-12">
                <div class="card p-4 mb-4 shadow-xs" style="border-radius: 14px; border: 1px solid var(--nx-border); background: #ffffff;">
                    <h5 class="fw-bold text-dark mb-3" style="font-size: 1.05rem;">
                        <i class="fas fa-circle-info text-success me-2"></i>Tentang Usaha &amp; Layanan
                    </h5>
                    <p class="text-dark small mb-4" style="line-height: 1.7; font-size: 0.88rem; white-space: pre-line;">
                        {{ $umkm->deskripsi ?: 'Usaha UMKM warga terdaftar resmi dalam lingkungan RW 018.' }}
                    </p>

                    <!-- Galeri Foto Produk -->
                    <h6 class="fw-bold text-dark mb-3 pt-3 border-top" style="font-size: 0.95rem; border-color: var(--nx-border) !important;">
                        <i class="fas fa-images text-primary me-2"></i>Galeri Foto Produk &amp; Suasana
                    </h6>

                    <div class="row g-2">
                        @forelse($umkm->produks as $p)
                            <div class="col-md-4 col-6">
                                <div class="rounded-3 overflow-hidden shadow-xs position-relative" style="height: 130px; background: #E5EBE6;">
                                    <img src="{{ $p->foto_url }}" alt="{{ $p->nama_produk }}" class="w-100 h-100" style="object-fit: cover;">
                                    <span class="position-absolute bottom-0 start-0 w-100 p-1.5 text-white small text-truncate" style="background: rgba(0,0,0,0.6); font-size: 0.7rem;">
                                        {{ $p->nama_produk }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="p-3 rounded-3 text-muted text-center bg-light small">
                                    Belum ada foto produk di galeri.
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right Sidebar: Kontak & Lokasi -->
            <div class="col-lg-4 col-12">
                <div class="card p-3.5 mb-3.5 shadow-xs" style="border-radius: 14px; border: 1px solid var(--nx-border); background: #ffffff;">
                    <h6 class="fw-bold mb-3 text-dark" style="font-size: 0.95rem;">
                        <i class="fas fa-address-card text-primary me-1.5"></i>Informasi Kontak Resmi
                    </h6>
                    <ul class="list-unstyled mb-0 small" style="line-height: 1.8;">
                        <li class="pb-2 border-bottom">
                            <span class="text-muted d-block" style="font-size: 0.72rem;">Nama Pemilik:</span>
                            <strong class="text-dark">{{ $umkm->pemilik }}</strong>
                        </li>
                        <li class="py-2 border-bottom">
                            <span class="text-muted d-block" style="font-size: 0.72rem;">Wilayah Domisili:</span>
                            <span class="text-dark fw-bold">RT {{ $umkm->rt_number }} / RW 018</span>
                        </li>
                        <li class="py-2 border-bottom">
                            <span class="text-muted d-block" style="font-size: 0.72rem;">Alamat Lengkap:</span>
                            <span class="text-dark">{{ $umkm->alamat }}</span>
                        </li>
                        <li class="py-2 border-bottom">
                            <span class="text-muted d-block" style="font-size: 0.72rem;">Jam Operasional:</span>
                            <span class="text-dark">{{ $umkm->jam_operasional ?: '08:00 - 20:00 WIB' }}</span>
                        </li>
                        <li class="pt-2">
                            <span class="text-muted d-block" style="font-size: 0.72rem;">Nomor WhatsApp:</span>
                            <span class="text-dark fw-bold">{{ $umkm->telepon }}</span>
                        </li>
                    </ul>
                </div>

                <div class="card p-3.5 shadow-xs" style="border-radius: 14px; border: 1px solid var(--nx-border); background: #ffffff;">
                    <h6 class="fw-bold mb-2 text-dark" style="font-size: 0.95rem;">
                        <i class="fas fa-handshake-angle text-success me-1.5"></i>Dukung Tetangga Kita
                    </h6>
                    <p class="small text-muted mb-3" style="font-size: 0.78rem; line-height: 1.5;">
                        Dengan berbelanja di UMKM warga, Anda ikut menggerakkan roda ekonomi lingkungan dan mempererat kerukunan antarwarga RW 018.
                    </p>
                    <a href="{{ $whatsappUrl }}" target="_blank" class="btn btn-sm btn-primary w-100 py-2 fw-bold" style="border-radius: 8px;">
                        <i class="fab fa-whatsapp me-1.5"></i>Order Langsung via WA
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== TAB 4: DASHBOARD PENGELOLA (OWNER / ADMIN) ==================== -->
    @if($isOwner || $isAdmin)
    <div class="tab-pane fade" id="tab-kelola" role="tabpanel">
        <div class="row g-4">
            <!-- Left: Pengelolaan Usaha & Ringkasan Performa -->
            <div class="col-lg-8 col-12">
                <div class="card p-4 mb-4 shadow-xs" style="border-radius: 14px; border: 1px solid var(--nx-border); background: #ffffff;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.05rem;">
                            <i class="fas fa-chart-line text-primary me-2"></i>Ringkasan Performa Usaha
                        </h5>
                        <button type="button" class="btn btn-sm btn-outline-primary px-3 py-1 fw-bold" data-bs-toggle="modal" data-bs-target="#modalEditUmkm" style="border-radius: 8px;">
                            <i class="fas fa-pen-to-square me-1"></i>Edit Informasi Usaha
                        </button>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4 col-6">
                            <div class="p-3 rounded-3" style="background: var(--nx-mint-soft); border: 1px solid var(--nx-mint);">
                                <small class="text-muted d-block" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700;">Skor Rating Rata-rata</small>
                                <h3 class="fw-bold text-dark mb-0 mt-1">{{ number_format($umkm->rating_avg, 1) }} <small class="text-warning fs-6">★</small></h3>
                            </div>
                        </div>
                        <div class="col-md-4 col-6">
                            <div class="p-3 rounded-3" style="background: var(--nx-powder); border: 1px solid var(--nx-border);">
                                <small class="text-muted d-block" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700;">Ulasan Pembeli</small>
                                <h3 class="fw-bold text-dark mb-0 mt-1">{{ $totalReviewCount }}</h3>
                            </div>
                        </div>
                        <div class="col-md-4 col-12">
                            <div class="p-3 rounded-3" style="background: #FFFBEB; border: 1px solid #FDE68A;">
                                <small class="text-muted d-block" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700;">Total Produk Terdaftar</small>
                                <h3 class="fw-bold text-dark mb-0 mt-1">{{ $umkm->produks->count() }}</h3>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Manajemen Produk -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.95rem;">Kelola Daftar Produk Anda</h6>
                        <button type="button" class="btn btn-sm btn-primary px-2.5 py-1 fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahProduk" style="border-radius: 8px;">
                            <i class="fas fa-plus me-1"></i>Tambah Produk
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                            <thead class="bg-light text-muted" style="font-size: 0.75rem; text-transform: uppercase;">
                                <tr>
                                    <th class="ps-3 py-2.5">Produk</th>
                                    <th class="py-2.5">Harga</th>
                                    <th class="py-2.5">Ketersediaan</th>
                                    <th class="text-end pe-3 py-2.5">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($umkm->produks as $p)
                                    <tr>
                                        <td class="ps-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="{{ $p->foto_url }}" class="rounded-2" style="width: 36px; height: 36px; object-fit: cover;">
                                                <strong class="text-dark">{{ $p->nama_produk }}</strong>
                                            </div>
                                        </td>
                                        <td>{{ $p->harga_formatted }}</td>
                                        <td>
                                            <span class="badge rounded-pill px-2 py-0.5" style="background: {{ $p->is_tersedia ? 'var(--nx-mint-soft)' : '#FEE2E2' }}; color: {{ $p->is_tersedia ? 'var(--nx-forest)' : '#DC2626' }}; font-size: 0.7rem; font-weight: 700;">
                                                {{ $p->is_tersedia ? 'Tersedia' : 'Habis' }}
                                            </span>
                                        </td>
                                        <td class="text-end pe-3">
                                            <button type="button" class="btn btn-sm btn-outline-secondary px-2 py-0.5" data-bs-toggle="modal" data-bs-target="#modalEditProduk{{ $p->id }}" style="border-radius: 6px;">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                            <form action="{{ route('umkm.produk.destroy', [$umkm->id, $p->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus produk?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-0.5" style="border-radius: 6px;">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3 text-muted small">Belum ada produk.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Moderasi Ulasan Yang Dilaporkan (Khusus Admin RW) -->
                @if($isAdminRw && $reportedReviews->count() > 0)
                    <div class="card p-4 shadow-xs border-danger mb-4" style="border-radius: 14px; background: #FFF5F5;">
                        <h6 class="fw-bold text-danger mb-3" style="font-size: 0.95rem;">
                            <i class="fas fa-triangle-exclamation me-2"></i>Moderasi Ulasan Pembeli Yang Dilaporkan ({{ $reportedReviews->count() }})
                        </h6>

                        @foreach($reportedReviews as $rep)
                            <div class="p-3 rounded-3 bg-white border mb-2.5 small">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong>{{ $rep->nama_pembeli }} (Rating: {{ $rep->rating }}★)</strong>
                                    <span class="badge bg-danger text-white">Dilaporkan</span>
                                </div>
                                <div class="text-muted mb-1">Alasan Laporan: <em>"{{ $rep->report_reason }}"</em></div>
                                <div class="p-2 rounded bg-light mb-2">"{{ $rep->komentar }}"</div>
                                <div class="d-flex gap-2 justify-content-end">
                                    <form action="{{ route('umkm.ulasan.moderate', [$umkm->id, $rep->id]) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="action" value="dismiss">
                                        <button type="submit" class="btn btn-sm btn-outline-secondary px-2.5 py-1" style="border-radius: 6px;">Tolak Laporan</button>
                                    </form>
                                    <form action="{{ route('umkm.ulasan.moderate', [$umkm->id, $rep->id]) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="action" value="hide">
                                        <button type="submit" class="btn btn-sm btn-warning px-2.5 py-1 text-dark fw-bold" style="border-radius: 6px;">Sembunyikan Ulasan</button>
                                    </form>
                                    <form action="{{ route('umkm.ulasan.moderate', [$umkm->id, $rep->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus permanen ulasan ini?');">
                                        @csrf
                                        <input type="hidden" name="action" value="delete">
                                        <button type="submit" class="btn btn-sm btn-danger px-2.5 py-1 fw-bold" style="border-radius: 6px;">Hapus Ulasan</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Right: Panduan Sukses Usaha Warga -->
            <div class="col-lg-4 col-12">
                <div class="card p-3.5 shadow-xs" style="border-radius: 14px; border: 1px solid var(--nx-border); background: #ffffff;">
                    <h6 class="fw-bold mb-3 text-dark" style="font-size: 0.95rem;">
                        <i class="fas fa-lightbulb text-warning me-1.5"></i>Tips Meningkatkan Usaha
                    </h6>
                    <ul class="list-unstyled small mb-0" style="line-height: 1.7;">
                        <li class="mb-2 pb-2 border-bottom">
                            <strong>1. Balas Ulasan Pembeli</strong>
                            <p class="text-muted mb-0 small">Membalas ulasan pembeli menunjukkan komitmen layanan dan meningkatkan loyalitas pelanggan.</p>
                        </li>
                        <li class="mb-2 pb-2 border-bottom">
                            <strong>2. Lengkapi Foto &amp; Harga</strong>
                            <p class="text-muted mb-0 small">Produk dengan foto jelas dan harga transparan lebih disukai warga sekitar.</p>
                        </li>
                        <li>
                            <strong>3. Fast Respon WhatsApp</strong>
                            <p class="text-muted mb-0 small">Balas chat warga dengan ramah dan tepat waktu untuk meningkatkan konversi pesanan.</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- ========================================== -->
<!-- MODAL: TAMBAH PRODUK BARU -->
<!-- ========================================== -->
@if($isOwner || $isAdmin)
<div class="modal fade" id="modalTambahProduk" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.12);">
            <form action="{{ route('umkm.produk.store', $umkm->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header px-4 pt-4 pb-2 border-0">
                    <h5 class="modal-title fw-bold text-dark" style="font-size: 1.15rem;">
                        <i class="fas fa-plus text-primary me-2"></i>Tambah Produk / Layanan Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Produk / Menu / Jasa <span class="text-danger">*</span></label>
                        <input type="text" name="nama_produk" class="form-control" required placeholder="Contoh: Paket Nasi Timbel Ayam Goreng" style="border-radius: 10px; height: 40px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Harga Produk (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="harga" class="form-control" required placeholder="Contoh: 25000" style="border-radius: 10px; height: 40px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Deskripsi Ringkas Produk</label>
                        <textarea name="deskripsi" rows="2" class="form-control" placeholder="Tuliskan keunggulan rasa, bahan, porsi, atau spesifikasi..." style="border-radius: 10px; font-size: 0.85rem;"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Foto Produk (Opsional)</label>
                        <input type="file" name="foto" class="form-control" accept="image/*" style="border-radius: 10px;">
                        <small class="text-muted" style="font-size: 0.72rem;">Format: JPG, PNG, WebP. Maksimal 2MB.</small>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_tersedia" value="1" id="switchStokBaru" checked>
                        <label class="form-check-label small fw-bold" for="switchStokBaru">Status Produk Tersedia untuk Dipesan</label>
                    </div>
                </div>
                <div class="modal-footer px-4 pb-4 pt-2 border-0">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold" style="border-radius: 10px;">Tambahkan Produk</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: EDIT PROFIL UMKM -->
<div class="modal fade" id="modalEditUmkm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.12);">
            <form action="{{ route('umkm.update', $umkm->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header px-4 pt-4 pb-2 border-0">
                    <h5 class="modal-title fw-bold text-dark" style="font-size: 1.15rem;">
                        <i class="fas fa-pen-to-square text-primary me-2"></i>Edit Informasi Profil Usaha
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Usaha / Toko <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" required value="{{ $umkm->nama }}" style="border-radius: 10px; height: 40px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Pemilik Usaha <span class="text-danger">*</span></label>
                            <input type="text" name="pemilik" class="form-control" required value="{{ $umkm->pemilik }}" style="border-radius: 10px; height: 40px;">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Kategori Usaha <span class="text-danger">*</span></label>
                            <select name="kategori" class="form-select" required style="border-radius: 10px; height: 40px;">
                                @foreach(app(\App\Http\Controllers\UmkmController::class)->getUmkmCategories() as $c)
                                    <option value="{{ $c }}" {{ $umkm->kategori === $c ? 'selected' : '' }}>{{ $c }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Wilayah RT <span class="text-danger">*</span></label>
                            <select name="rt_number" class="form-select" required style="border-radius: 10px; height: 40px;">
                                @foreach(['006', '007', '008', '009', '010'] as $rt)
                                    <option value="{{ $rt }}" {{ $umkm->rt_number === $rt ? 'selected' : '' }}>RT {{ $rt }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Nomor WhatsApp / Telp <span class="text-danger">*</span></label>
                            <input type="text" name="telepon" class="form-control" required value="{{ $umkm->telepon }}" placeholder="08123456789" style="border-radius: 10px; height: 40px;">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Jam Operasional</label>
                            <input type="text" name="jam_operasional" class="form-control" value="{{ $umkm->jam_operasional }}" placeholder="08:00 - 21:00 WIB" style="border-radius: 10px; height: 40px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Alamat Lengkap Usaha <span class="text-danger">*</span></label>
                            <input type="text" name="alamat" class="form-control" required value="{{ $umkm->alamat }}" style="border-radius: 10px; height: 40px;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Deskripsi Usaha</label>
                        <textarea name="deskripsi" rows="3" class="form-control" style="border-radius: 10px; font-size: 0.85rem;">{{ $umkm->deskripsi }}</textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Ganti Foto Banner / Cover Usaha (Opsional)</label>
                        <input type="file" name="cover_image" class="form-control" accept="image/*" style="border-radius: 10px;">
                    </div>
                </div>
                <div class="modal-footer px-4 pb-4 pt-2 border-0">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold" style="border-radius: 10px;">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
function copyShareUrl() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        alert('Tautan profil UMKM berhasil disalin ke clipboard! Anda dapat membagikannya kepada warga lain.');
    }).catch(function() {
        prompt('Salin link UMKM berikut:', window.location.href);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Auto switch tab based on hash (e.g. #tab-ulasan or #tab-produk)
    if (window.location.hash) {
        const hash = window.location.hash;
        if (hash === '#tab-ulasan') {
            const btn = document.getElementById('ulasan-tab');
            if (btn) bootstrap.Tab.getOrCreateInstance(btn).show();
        } else if (hash === '#tab-produk') {
            const btn = document.getElementById('produk-tab');
            if (btn) bootstrap.Tab.getOrCreateInstance(btn).show();
        } else if (hash === '#tab-profil') {
            const btn = document.getElementById('profil-tab');
            if (btn) bootstrap.Tab.getOrCreateInstance(btn).show();
        } else if (hash === '#tab-kelola') {
            const btn = document.getElementById('kelola-tab');
            if (btn) bootstrap.Tab.getOrCreateInstance(btn).show();
        }
    }
});
</script>
@endsection
