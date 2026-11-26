<div class="umkm-modal-content">
    <div class="p-4 mb-4 rounded-4 position-relative" style="background: linear-gradient(135deg, #DCEBDD 0%, #E8F3EE 55%, #DCEAF5 100%); border: 1px solid var(--nx-border);">
        <button type="button" class="btn btn-light rounded-circle position-absolute top-square shadow-sm"
                data-bs-dismiss="modal"
                style="top: 16px; right: 16px; width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-times"></i>
        </button>

        <div class="row align-items-center g-3">
            <div class="col-lg-7">
                <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                    <span class="badge rounded-pill px-3 py-1" style="background: var(--nx-forest); color: #fff;">
                        {{ $umkm->kategori }} • RT {{ $umkm->rt_number ?? '00' }}
                    </span>
                    {!! $umkm->status_badge !!}
                </div>
                <h4 class="fw-bold mb-2" style="color: var(--nx-forest);">{{ $umkm->nama }}</h4>
                <p class="text-muted small mb-2">{{ $umkm->deskripsi ?? 'Deskripsi usaha belum tersedia.' }}</p>
                <div class="small fw-semibold mb-2" style="color: var(--nx-text);">
                    <i class="fas fa-user-check text-success me-1"></i> Pemilik: {{ $umkm->pemilik ?? 'Warga RW 018' }} •
                    <i class="fas fa-location-dot text-success ms-1 me-1"></i> {{ $umkm->alamat ?? '-' }}
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="d-inline-flex align-items-center gap-1 text-warning">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star" style="font-size: 0.8rem; color: {{ $i <= round($umkm->rating_avg) ? '#F59E0B' : '#E2E8F0' }};"></i>
                        @endfor
                    </div>
                    <span class="fw-bold small" style="color: var(--nx-forest);">{{ number_format($umkm->rating_avg, 1) }}</span>
                    <span class="text-muted small">({{ $umkm->rating_count }} ulasan)</span>
                </div>
            </div>
            <div class="col-lg-5">
                @php
                    $catKey = strtolower(trim($umkm->kategori ?? 'default'));
                    $rawCover = $categoryImages[$catKey] ?? ($categoryImages['default'] ?? 'images/umkm/kuliner.jpg');
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
                    @forelse($umkm->produks as $produk)
                        <div class="col-12">
                            <div class="p-3 rounded-4 d-flex align-items-center justify-content-between gap-3"
                                 style="background: var(--nx-bg); border: 1px solid var(--nx-border);">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $produk->foto_url }}" alt="{{ $produk->nama_produk }}" class="rounded-3" style="width: 60px; height: 60px; object-fit: cover;">
                                    <div>
                                        <h6 class="fw-bold mb-1" style="color: var(--nx-text); font-size: 0.95rem;">{{ $produk->nama_produk }}</h6>
                                        <p class="text-muted small mb-0">{{ Str::limit($produk->deskripsi, 80) }}</p>
                                    </div>
                                </div>
                                <div class="text-end flex-shrink-0">
                                    <div class="fw-bold mb-1" style="color: var(--nx-forest);">{{ $produk->harga_formatted }}</div>
                                    @php
                                        $orderMsg = urlencode("Halo {$umkm->pemilik}, saya ingin memesan {$produk->nama_produk} ({$produk->harga_formatted}) dari lapak {$umkm->nama} di NexaNest.");
                                        $phoneClean = preg_replace('/[^0-9]/', '', $umkm->telepon ?? '081234567890');
                                        if (str_starts_with($phoneClean, '0')) {
                                            $phoneClean = '62' . substr($phoneClean, 1);
                                        }
                                        $orderWaUrl = "https://wa.me/{$phoneClean}?text={$orderMsg}";
                                    @endphp
                                    <a href="{{ $orderWaUrl }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fab fa-whatsapp me-1"></i> Pesan
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-4 text-muted small">
                            <i class="fas fa-box-open fa-2x mb-2 d-block opacity-50"></i>
                            Belum ada daftar produk spesifik. Silakan hubungi penjual via WhatsApp.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card p-4 h-100">
                <h6 class="fw-bold mb-3" style="color: var(--nx-forest);"><i class="fas fa-store me-2"></i>Informasi Usaha</h6>
                <p class="small mb-2"><strong>Telepon / WA:</strong> {{ $umkm->telepon ?? '-' }}</p>
                <p class="small mb-2"><strong>Alamat:</strong> {{ $umkm->alamat ?? '-' }}</p>
                <p class="small mb-2"><strong>Kategori:</strong> {{ $umkm->kategori }}</p>
                <p class="small mb-3"><strong>Jam Operasional:</strong> {{ $umkm->jam_operasional ?? '08.00 - 21.00 WIB' }}</p>

                <div class="pt-3 border-top mt-auto">
                    <a href="{{ route('umkm.show', $umkm->id) }}" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-arrow-up-right-from-square me-1"></i> Buka Halaman Profil Lengkap
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
