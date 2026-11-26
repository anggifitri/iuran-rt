@extends('layouts.app')

@section('title', 'Notifikasi & Pemberitahuan — NexaNest')

@section('content')
@php
    $user = Auth::user();
@endphp

<div class="nx-page-header">
    <div>
        <span class="badge rounded-pill mb-2 px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
            <i class="fas fa-bell me-1"></i> Pusat Pemberitahuan
        </span>
        <h1 class="nx-page-title">Notifikasi Saya</h1>
        <p class="nx-page-subtitle">Daftar pengumuman tagihan iuran, status pembayaran, dan informasi layanan lingkungan.</p>
    </div>
    <div class="d-flex gap-2">
        @if($unreadCount > 0)
            <form action="{{ route('notifikasi.read_all') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-primary">
                    <i class="fas fa-check-double me-1"></i> Tandai Semua Sudah Dibaca
                </button>
            </form>
        @endif
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert" style="background: var(--nx-mint-soft); border-color: var(--nx-sage); color: var(--nx-forest);">
        <i class="fas fa-check-circle me-2 fs-5"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- FILTER TABS -->
<div class="card p-3 mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="btn-group btn-group-sm" role="group">
            <a href="{{ route('notifikasi.index') }}" class="btn {{ !request('status') ? 'btn-primary' : 'btn-outline-secondary' }}">
                Semua Notifikasi ({{ $notifications->total() }})
            </a>
            <a href="{{ route('notifikasi.index', ['status' => 'unread']) }}" class="btn {{ request('status') === 'unread' ? 'btn-primary' : 'btn-outline-secondary' }}">
                Belum Dibaca ({{ $unreadCount }})
            </a>
        </div>
        <small class="text-muted">Menampilkan riwayat notifikasi akun {{ $user->name }}</small>
    </div>
</div>

<!-- LIST NOTIFIKASI -->
<div class="card overflow-hidden">
    <div class="list-group list-group-flush">
        @forelse($notifications as $n)
            @php
                $bgClass = !$n->is_read ? 'bg-light' : '';
                $iconClass = 'fa-bell text-secondary';
                $badgeType = 'bg-secondary';
                if ($n->tipe === 'tagihan_baru') {
                    $iconClass = 'fa-file-invoice-dollar text-primary';
                    $badgeType = 'bg-primary';
                } elseif ($n->tipe === 'pembayaran_diterima') {
                    $iconClass = 'fa-check-circle text-success';
                    $badgeType = 'bg-success';
                } elseif ($n->tipe === 'pembayaran_ditolak') {
                    $iconClass = 'fa-times-circle text-danger';
                    $badgeType = 'bg-danger';
                } elseif ($n->tipe === 'pembayaran_pending') {
                    $iconClass = 'fa-clock text-warning';
                    $badgeType = 'bg-warning text-dark';
                } elseif ($n->tipe === 'jatuh_tempo' || $n->tipe === 'terlambat') {
                    $iconClass = 'fa-exclamation-triangle text-danger';
                    $badgeType = 'bg-danger';
                }
            @endphp
            <div class="list-group-item p-3.5 {{ $bgClass }} border-bottom">
                <div class="d-flex align-items-start justify-content-between gap-3">
                    <div class="d-flex align-items-start gap-3">
                        <span class="rounded-circle d-flex align-items-center justify-content-center p-2.5" style="background: var(--nx-bg); width: 42px; height: 42px; flex-shrink: 0;">
                            <i class="fas {{ $iconClass }} fa-lg"></i>
                        </span>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h6 class="fw-bold mb-0" style="color: var(--nx-text);">{{ $n->judul }}</h6>
                                <span class="badge rounded-pill px-2.5 py-0.5 {{ $badgeType }}" style="font-size: 0.68rem;">
                                    {{ ucwords(str_replace('_', ' ', $n->tipe)) }}
                                </span>
                                @if(!$n->is_read)
                                    <span class="badge rounded-pill bg-danger px-2 py-0.5" style="font-size: 0.65rem;">Baru</span>
                                @endif
                            </div>
                            <p class="text-muted small mb-1.5" style="line-height: 1.45;">{{ $n->pesan }}</p>
                            <div class="d-flex align-items-center gap-3 text-muted" style="font-size: 0.72rem;">
                                <span><i class="far fa-clock me-1"></i>{{ $n->created_at->diffForHumans() }} ({{ $n->created_at->translatedFormat('d M Y, H:i') }} WIB)</span>
                                @if($n->nominal)
                                    <span><i class="fas fa-tag me-1"></i>Rp {{ number_format($n->nominal, 0, ',', '.') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2" style="flex-shrink: 0;">
                        @if($n->link_url)
                            <form action="{{ route('notifikasi.read', $n->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-primary px-3">
                                    <i class="fas fa-arrow-right me-1"></i> Buka
                                </button>
                            </form>
                        @elseif(!$n->is_read)
                            <form action="{{ route('notifikasi.read', $n->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-light border px-2.5" title="Tandai sudah dibaca">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="p-5 text-center text-muted">
                <i class="fas fa-bell-slash fa-2x mb-2 text-muted"></i>
                <h6 class="fw-bold mb-1">Tidak Ada Notifikasi</h6>
                <p class="small mb-0">Semua pemberitahuan dan tagihan iuran Anda akan tercatat di sini.</p>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div class="p-3 border-top d-flex justify-content-center">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
