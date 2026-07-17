@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark" style="color: var(--text-main) !important;">Penerbitan Surat Digital</h2>
            <p class="text-muted mb-0">Ajukan, setujui, dan unduh surat keterangan resmi bertanda tangan elektronik (TTE).</p>
        </div>
        <a href="{{ route('surat.create') }}" class="btn btn-primary btn-lg shadow-sm px-4 py-2.5" style="border-radius: 12px; font-weight: 600;">
            <i class="fas fa-plus me-1"></i>{{ $user->isAdmin() ? 'Buat Surat Baru' : 'Ajukan Surat Baru' }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background-color: var(--bg-card); border: 1px solid var(--border-color) !important;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="color: var(--text-main);">
                <thead style="background-color: rgba(59,130,246,0.03); color: var(--text-main); border-bottom: 2px solid var(--border-color);">
                    <tr>
                        <th class="ps-4" style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; opacity: 0.8;">Pemohon</th>
                        <th style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; opacity: 0.8;">Jenis Surat</th>
                        <th style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; opacity: 0.8;">Keperluan</th>
                        <th style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; opacity: 0.8;">RT/RW</th>
                        <th style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; opacity: 0.8;">Tanggal Diajukan</th>
                        <th style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; opacity: 0.8;">Status Alur</th>
                        <th class="pe-4 text-end" style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; opacity: 0.8;">Dokumen / Aksi</th>
                    </tr>
                </thead>
                <tbody style="border-top: none;">
                    @if($surats->isEmpty())
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-file-invoice fa-3x mb-3 text-secondary" style="opacity: 0.5;"></i>
                                <h5 class="fw-semibold mb-1">Belum Ada Pengajuan Surat</h5>
                                <p class="small mb-0">Permohonan surat keterangan yang Anda buat atau harus disetujui akan muncul di sini.</p>
                            </td>
                        </tr>
                    @else
                        @foreach($surats as $surat)
                            @php
                                $statusBadge = '';
                                if ($surat->status === 'pending_rt') {
                                    $statusBadge = '<span class="badge bg-warning-subtle text-warning border border-warning px-2.5 py-1.5 rounded-pill"><i class="fas fa-clock fa-spin me-1"></i> Menunggu RT</span>';
                                } elseif ($surat->status === 'pending_rw') {
                                    $statusBadge = '<span class="badge bg-info-subtle text-info border border-info px-2.5 py-1.5 rounded-pill"><i class="fas fa-spinner fa-spin me-1"></i> Menunggu RW</span>';
                                } elseif ($surat->status === 'selesai') {
                                    $statusBadge = '<span class="badge bg-success-subtle text-success border border-success px-2.5 py-1.5 rounded-pill"><i class="fas fa-check-circle me-1"></i> Selesai (TTE)</span>';
                                } else {
                                    $statusBadge = '<span class="badge bg-danger-subtle text-danger border border-danger px-2.5 py-1.5 rounded-pill">Ditolak</span>';
                                }
                            @endphp
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $surat->user->warga->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($surat->user->name).'&background=3b82f6&color=fff&rounded=true' }}" class="rounded-circle shadow-sm" style="width: 35px; height: 35px; object-fit: cover;">
                                        <div>
                                            <span class="fw-bold d-block" style="color: var(--text-main); font-size: 0.9rem;">{{ $surat->user->name }}</span>
                                            <span class="text-muted small" style="font-size: 0.75rem;">NIK: {{ $surat->user->nik ?? '-' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark" style="color: var(--text-main) !important; font-size: 0.9rem;">{{ $surat->jenis_surat }}</span>
                                </td>
                                <td>
                                    <span class="d-inline-block text-truncate small" style="max-width: 200px;" title="{{ $surat->keperluan }}">
                                        {{ $surat->keperluan }}
                                    </span>
                                </td>
                                <td style="font-size: 0.85rem;">RT {{ $surat->rt_number ?? '-' }} / RW 018</td>
                                <td style="font-size: 0.85rem;">{{ $surat->created_at->translatedFormat('d M Y H:i') }}</td>
                                <td>{!! $statusBadge !!}</td>
                                <td class="pe-4 text-end">
                                    <!-- WARGA ACTION -->
                                    @if($user->isWarga())
                                        @if($surat->status === 'selesai')
                                            <a href="{{ route('surat.pdf', $surat->id) }}" target="_blank" class="btn btn-sm btn-success px-3 py-1.5" style="border-radius: 8px; font-weight: 500;">
                                                <i class="fas fa-file-pdf me-1"></i>Unduh PDF
                                            </a>
                                        @else
                                            <span class="text-muted small"><i class="fas fa-hourglass-half me-1"></i>Dalam Proses</span>
                                        @endif
                                    @endif

                                    <!-- ADMIN RT ACTION -->
                                    @if($user->isAdmin() && $user->rt_number !== '000')
                                        @if($surat->status === 'pending_rt')
                                            <form action="{{ route('surat.approve_rt', $surat->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-primary px-3 py-1.5" style="border-radius: 8px; font-weight: 500;">
                                                    <i class="fas fa-check me-1"></i>Setujui (RT)
                                                </button>
                                            </form>
                                        @elseif($surat->status === 'pending_rw')
                                            <span class="text-muted small"><i class="fas fa-plane-departure me-1"></i>Diteruskan ke RW</span>
                                        @elseif($surat->status === 'selesai')
                                            <a href="{{ route('surat.pdf', $surat->id) }}" target="_blank" class="btn btn-sm btn-outline-success px-3 py-1.5" style="border-radius: 8px; font-weight: 500;">
                                                <i class="fas fa-file-pdf me-1"></i>Unduh PDF
                                            </a>
                                        @endif
                                    @endif

                                    <!-- ADMIN RW ACTION -->
                                    @if($user->isAdmin() && $user->rt_number === '000')
                                        @if($surat->status === 'pending_rt')
                                            <span class="text-muted small"><i class="fas fa-hourglass-half me-1"></i>Menunggu RT</span>
                                        @elseif($surat->status === 'pending_rw')
                                            <form action="{{ route('surat.approve_rw', $surat->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success px-3 py-1.5" style="border-radius: 8px; font-weight: 600;">
                                                    <i class="fas fa-signature me-1"></i>TTE & Terbitkan (RW)
                                                </button>
                                            </form>
                                        @elseif($surat->status === 'selesai')
                                            <a href="{{ route('surat.pdf', $surat->id) }}" target="_blank" class="btn btn-sm btn-outline-success px-3 py-1.5" style="border-radius: 8px; font-weight: 500;">
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
    </div>

    <!-- PAGINATION -->
    <div class="mt-4 d-flex justify-content-center">
        {{ $surats->links() }}
    </div>
</div>
@endsection
