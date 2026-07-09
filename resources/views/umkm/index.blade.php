@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h4 class="fw-bold">Direktori UMKM</h4>
            <p class="text-muted mb-1">Temukan usaha warga RT dengan kategori, kontak, dan alamat lengkap.</p>
            <small class="text-muted">{{ $usahas->count() }} usaha ditemukan.</small>
        </div>
        <a href="{{ route('umkm.create') }}" class="btn btn-primary">Tambah UMKM</a>
    </div>

    <form method="GET" action="{{ route('umkm.index') }}" class="row g-2 align-items-end mb-4">
        <div class="col-md-6">
            <label class="form-label">Cari usaha</label>
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Nama, pemilik, kategori, alamat...">
        </div>
        <div class="col-md-4">
            <label class="form-label">Filter kategori</label>
            <select name="kategori" class="form-select">
                <option value="">Semua kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}" {{ request('kategori') === $category ? 'selected' : '' }}>{{ $category }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-secondary w-100">Cari</button>
        </div>
    </form>

    @if($usahas->isEmpty())
        <div class="card p-4 text-center text-muted">
            <h5 class="mb-2">Tidak ada hasil.</h5>
            <p class="mb-0">Coba kata kunci lain atau tambahkan usaha UMKM baru.</p>
        </div>
    @else
        <div class="row g-4">
            @foreach($usahas as $usaha)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 p-3 shadow-sm border-0" style="background: rgba(255,255,255,0.04);">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h5 class="mb-1">{{ $usaha->nama }}</h5>
                                <small class="text-muted">{{ $usaha->pemilik ? 'Pemilik: ' . $usaha->pemilik : 'Pemilik tidak tersedia' }}</small>
                            </div>
                            <span class="badge rounded-pill bg-primary text-white">{{ $usaha->kategori ?? 'Umum' }}</span>
                        </div>

                        <p class="mb-2 text-white-50">{{ \Illuminate\Support\Str::limit($usaha->deskripsi ?? 'Belum ada deskripsi usaha.', 120) }}</p>

                        <div class="mb-3">
                            <p class="mb-1"><i class="fas fa-map-marker-alt me-2"></i>{{ $usaha->alamat ?? 'Alamat belum diisi' }}</p>
                            <p class="mb-0"><i class="fas fa-phone me-2"></i>{{ $usaha->telepon ?? '-' }}</p>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mt-auto pt-2 border-top border-secondary pt-3">
                            <span class="badge bg-secondary">RT {{ Auth::user()->rt_number ?? '-' }}</span>
                            <span class="badge bg-secondary">UMKM</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
