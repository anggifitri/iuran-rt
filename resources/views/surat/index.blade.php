@extends('layouts.app')

@section('title', 'Penerbitan Surat Digital — NexaNest')

@section('content')
<div class="nx-page-header">
    <div>
        <span class="badge rounded-pill mb-2 px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
            <i class="fas fa-file-signature me-1"></i> Layanan Administrasi E-Surat
        </span>
        <h1 class="nx-page-title">Penerbitan Surat Digital (TTE)</h1>
        <p class="nx-page-subtitle">Ajukan, verifikasi berjenjang (RT &amp; RW), dan unduh surat keterangan resmi bertanda tangan elektronik.</p>
    </div>
    <a href="{{ route('surat.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> {{ $user->isAdmin() ? 'Buat Surat Baru' : 'Ajukan Surat Baru' }}
    </a>
</div>

@if($user->isAdminRw() || $user->isAdminRt())
    <div class="card p-3 mb-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('surat.index', ['filter' => 'pending']) }}"
                   class="btn btn-sm rounded-pill px-3 py-2 {{ (isset($filter) && $filter === 'pending') ? 'btn-primary' : 'btn-outline-primary' }}">
                    <i class="fas fa-hourglass-half me-1"></i> Menunggu Disetujui {{ $user->isAdminRw() ? '(RW)' : '(RT)' }}
                </a>

                @if($user->isAdminRt())
                    <a href="{{ route('surat.index', ['filter' => 'pending_rw']) }}"
                       class="btn btn-sm rounded-pill px-3 py-2 {{ (isset($filter) && $filter === 'pending_rw') ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="fas fa-paper-plane me-1"></i> Sedang Diproses RW
                    </a>
                @endif

                <a href="{{ route('surat.index', ['filter' => 'selesai']) }}"
                   class="btn btn-sm rounded-pill px-3 py-2 {{ (isset($filter) && $filter === 'selesai') ? 'btn-primary' : 'btn-outline-primary' }}">
                    <i class="fas fa-check-circle me-1"></i> Sudah Selesai (TTE)
                </a>

                <a href="{{ route('surat.index', ['filter' => 'all']) }}"
                   class="btn btn-sm rounded-pill px-3 py-2 {{ (!isset($filter) || $filter === 'all') ? 'btn-primary' : 'btn-outline-primary' }}">
                    <i class="fas fa-layer-group me-1"></i> Semua Surat
                </a>
            </div>

            <span class="badge rounded-pill px-3 py-2" style="background: var(--nx-mint-soft); color: var(--nx-forest);">
                <i class="fas fa-qrcode me-1"></i> Tanda Tangan Elektronik Aktif
            </span>
        </div>
    </div>
@endif

<div class="card nx-table-card overflow-hidden shadow-xs border-0 mb-4">
    <div class="card-header bg-white p-3.5 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="fw-bold mb-0 text-dark" style="font-size: 0.96rem;">
                <i class="fas fa-file-signature text-success me-2"></i>Daftar Pengajuan Surat Digital Warga
            </h5>
            <small class="text-muted" style="font-size: 0.75rem;">Layanan persetujuan berjenjang RT/RW dengan Tanda Tangan Elektronik (TTE)</small>
        </div>
        <span class="badge rounded-pill px-3 py-1.5" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-weight: 700; font-size: 0.74rem;">
            {{ $surats->total() }} Pengajuan
        </span>
    </div>
    <div class="table-responsive nx-table-responsive">
        <table class="table table-hover align-middle nx-table mb-0">
            <thead>
                <tr>
                    <th class="ps-4" style="width: 24%;">Pemohon</th>
                    <th style="width: 18%;">Jenis Surat</th>
                    <th style="width: 20%;">Keperluan</th>
                    <th style="width: 10%;">Domisili</th>
                    <th style="width: 12%;">Tanggal</th>
                    <th style="width: 10%;">Status Alur</th>
                    <th class="pe-4 text-end" style="width: 14%;">Dokumen / Aksi</th>
                </tr>
            </thead>
            <tbody>
                @if($surats->isEmpty())
                    <tr>
                        <td colspan="7" class="p-0 border-0">
                            <x-empty-state
                                icon="fas fa-file-signature"
                                title="Belum Ada Pengajuan Surat"
                                message="Permohonan surat keterangan yang Anda buat atau perlu disetujui akan muncul di sini."
                                actionUrl="{{ route('surat.create') }}"
                                actionLabel="{{ $user->isAdmin() ? 'Buat Surat Baru' : 'Ajukan Surat Baru' }}"
                                actionIcon="fas fa-plus"
                            />
                        </td>
                    </tr>
                @else
                    @foreach($surats as $surat)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $surat->user->warga->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($surat->user->name).'&background=285943&color=fff&rounded=true' }}"
                                         class="rounded-circle shadow-sm"
                                         style="width: 38px; height: 38px; object-fit: cover; border: 2px solid var(--nx-mint);">
                                    <div>
                                        <span class="fw-bold d-block" style="color: var(--nx-text);">{{ $surat->user->name }}</span>
                                        <span class="text-muted small" style="font-size: 0.74rem; font-family: monospace;">NIK: {{ $surat->user->nik ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold" style="color: var(--nx-forest);">{{ $surat->jenis_surat }}</span>
                            </td>
                            <td>
                                <span class="d-inline-block text-truncate small" style="max-width: 210px;" title="{{ $surat->keperluan }}">
                                    {{ $surat->keperluan }}
                                </span>
                            </td>
                            <td>
                                <span class="badge rounded-pill px-2.5 py-0.5" style="background: var(--nx-bg); color: var(--nx-text); border: 1px solid var(--nx-border); font-size: 0.74rem;">
                                    RT {{ $surat->rt_number ?? '-' }} / RW 018
                                </span>
                            </td>
                            <td class="small fw-semibold text-muted">{{ $surat->created_at->translatedFormat('d M Y H:i') }}</td>
                            <td>
                                @if($surat->status === 'pending_rt' || $surat->status === 'pending')
                                    <x-status-badge status="menunggu" label="Menunggu RT" icon="fas fa-clock" />
                                @elseif($surat->status === 'pending_rw')
                                    <x-status-badge status="diproses" label="Menunggu RW" icon="fas fa-paper-plane" />
                                @elseif($surat->status === 'selesai')
                                    <x-status-badge status="selesai" label="Selesai (TTE)" icon="fas fa-check-circle" />
                                @else
                                    <x-status-badge status="ditolak" label="Ditolak" icon="fas fa-times-circle" />
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                @if($user->isWarga())
                                    @if($surat->status === 'selesai')
                                        <a href="{{ route('surat.pdf', $surat->id) }}" target="_blank" class="btn btn-sm btn-primary px-3" style="border-radius: 8px; font-size: 0.78rem;">
                                            <i class="fas fa-file-pdf me-1"></i>Unduh PDF
                                        </a>
                                    @else
                                        <span class="text-muted small"><i class="fas fa-hourglass-half me-1"></i>Dalam Proses</span>
                                    @endif
                                @endif

                                @if($user->isAdminRt())
                                    @if($surat->status === 'pending_rt' || $surat->status === 'pending')
                                        <form action="{{ route('surat.approve_rt', $surat->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-primary px-3" style="border-radius: 8px; font-size: 0.78rem;">
                                                <i class="fas fa-check me-1"></i>Setujui (RT)
                                            </button>
                                        </form>
                                    @elseif($surat->status === 'pending_rw')
                                        <span class="text-muted small"><i class="fas fa-paper-plane me-1"></i>Diteruskan RW</span>
                                    @elseif($surat->status === 'selesai')
                                        <a href="{{ route('surat.pdf', $surat->id) }}" target="_blank" class="btn btn-sm btn-outline-primary px-3" style="border-radius: 8px; font-size: 0.78rem;">
                                            <i class="fas fa-file-pdf me-1"></i>Unduh PDF
                                        </a>
                                    @endif
                                @endif

                                @if($user->isAdminRw())
                                    @if($surat->status === 'pending_rt' || $surat->status === 'pending')
                                        <span class="text-muted small"><i class="fas fa-hourglass-half me-1"></i>Menunggu RT</span>
                                    @elseif($surat->status === 'pending_rw')
                                        <form action="{{ route('surat.approve_rw', $surat->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-primary px-3" style="border-radius: 8px; font-size: 0.78rem;">
                                                <i class="fas fa-signature me-1"></i>TTE &amp; Sahkan
                                            </button>
                                        </form>
                                    @elseif($surat->status === 'selesai')
                                        <a href="{{ route('surat.pdf', $surat->id) }}" target="_blank" class="btn btn-sm btn-outline-primary px-3" style="border-radius: 8px; font-size: 0.78rem;">
                                            <i class="fas fa-file-pdf me-1"></i>Unduh PDF
                                        </a>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
    @if($surats->hasPages())
        <div class="card-footer bg-white p-3.5 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="small text-muted">Menampilkan {{ $surats->firstItem() }} - {{ $surats->lastItem() }} dari {{ $surats->total() }} surat</span>
            {{ $surats->links() }}
        </div>
    @endif
</div>
@endsection
