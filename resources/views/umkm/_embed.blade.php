<div class="umkm-modal-content">
    <div class="p-4 mb-4 rounded-4 position-relative" style="background: linear-gradient(135deg, #DCEBDD 0%, #E8F3EE 55%, #DCEAF5 100%); border: 1px solid var(--nx-border);">
        <button type="button" class="btn btn-light rounded-circle position-absolute top-square shadow-sm"
                style="top: 16px; right: 16px; width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-times"></i>
        </button>

        <div class="row align-items-center g-3">
            <div class="col-lg-7">
                <span class="badge rounded-pill px-3 py-1 mb-2" style="background: var(--nx-forest); color: #fff;">
                    {{ $umkm->kategori }} • RT {{ $umkm->rt_number ?? (Auth::user()->rt_number ?? '00') }}
                </span>
                <h4 class="fw-bold mb-2" style="color: var(--nx-forest);">{{ $umkm->nama }}</h4>
                <p class="text-muted small mb-2">{{ $umkm->deskripsi ?? 'Deskripsi usaha belum tersedia.' }}</p>
                <div class="small fw-semibold" style="color: var(--nx-text);">
                    <i class="fas fa-user-check text-success me-1"></i> Pemilik: {{ $umkm->pemilik ?? 'Warga RW 018' }} •
                    <i class="fas fa-location-dot text-success ms-1 me-1"></i> {{ $umkm->alamat ?? '-' }}
                </div>
            </div>
            <div class="col-lg-5">
                @php
                    $catKey = strtolower(trim($umkm->kategori ?? 'default'));
                    $rawCover = $categoryImages[$catKey] ?? $categoryImages['default'];
                    $coverUrl = preg_match('/^https?:\/\//', $rawCover) ? $rawCover : asset($rawCover);
                @endphp
                <div class="rounded-4 overflow-hidden shadow-sm" style="min-height: 160px; background: url('{{ $coverUrl }}') center/cover no-repeat;"></div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card p-4 mb-0">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div>
                        <h5 class="fw-bold mb-0" style="font-size: 1rem;">Katalog Produk &amp; Layanan</h5>
                        <small class="text-muted">Pilih produk dan pesan langsung melalui WhatsApp</small>
                    </div>
                    <a href="{{ $whatsappUrl }}" target="_blank" class="btn btn-primary btn-sm px-3">
                        <i class="fab fa-whatsapp me-1"></i> Chat Penjual
                    </a>
                </div>
                <div class="row g-3">
                    @foreach($menuItems as $item)
                        <div class="col-12">
                            <div class="p-3 rounded-4 d-flex align-items-center justify-content-between gap-3"
                                 style="background: var(--nx-bg); border: 1px solid var(--nx-border);">
                                <div class="d-flex align-items-center gap-3">
                                    @php
                                        if (preg_match('/^https?:\/\//', $item['image'])) {
                                            $imgSrc = $item['image'];
                                        } elseif (Illuminate\Support\Str::startsWith($item['image'], 'images/')) {
                                            $imgSrc = asset($item['image']);
                                        } else {
                                            $imgSrc = asset('images/menu/'.$item['image']);
                                        }
                                    @endphp
                                    <img src="{{ $imgSrc }}" alt="{{ $item['nama'] }}" class="rounded-3" style="width: 64px; height: 64px; object-fit: cover;">
                                    <div>
                                        <h6 class="fw-bold mb-1" style="color: var(--nx-text);">{{ $item['nama'] }}</h6>
                                        <p class="text-muted small mb-0">{{ $item['deskripsi'] }}</p>
                                    </div>
                                </div>
                                <div class="text-end flex-shrink-0">
                                    <div class="fw-bold mb-1" style="color: var(--nx-forest);">{{ $item['harga'] }}</div>
                                    <a href="{{ $whatsappUrl }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fab fa-whatsapp me-1"></i> Pesan
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card p-4 h-100">
                <h6 class="fw-bold mb-3" style="color: var(--nx-forest);"><i class="fas fa-address-book me-2"></i>Detail Kontak Toko</h6>
                <p class="small mb-2"><strong>Telepon / WA:</strong> {{ $umkm->telepon ?? '-' }}</p>
                <p class="small mb-2"><strong>Alamat:</strong> {{ $umkm->alamat ?? '-' }}</p>
                <p class="small mb-0"><strong>Kategori:</strong> {{ $umkm->kategori }}</p>
            </div>
        </div>
    </div>
</div>
