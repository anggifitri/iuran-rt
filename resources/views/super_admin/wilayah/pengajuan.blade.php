@extends('layouts.super_admin')

@section('title', 'Pengajuan RW Baru — NexaNest Super Admin')
@section('page_title', 'Pengajuan RW Baru')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
    <div>
        <h2 class="fw-bold text-dark m-0 fs-5">Tinjauan Permohonan Integrasi RW Baru</h2>
        <p class="text-muted m-0 small">Verifikasi dokumen SK dan persetujuan registrasi mandiri dari calon pengurus RW</p>
    </div>
</div>

<!-- STATUS FILTER TABS -->
<div class="sa-card mb-4 p-2">
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('super_admin.pengajuan.index') }}" class="btn btn-sm {{ empty($status) ? 'btn-success fw-bold text-white' : 'btn-light' }}" style="border-radius: 10px;">
            Semua Pengajuan
        </a>
        <a href="{{ route('super_admin.pengajuan.index', ['status' => 'menunggu']) }}" class="btn btn-sm {{ $status === 'menunggu' ? 'btn-warning fw-bold text-dark' : 'btn-light' }}" style="border-radius: 10px;">
            <i class="fas fa-clock me-1"></i> Menunggu Review ({{ $countMenunggu }})
        </a>
        <a href="{{ route('super_admin.pengajuan.index', ['status' => 'revisi']) }}" class="btn btn-sm {{ $status === 'revisi' ? 'btn-info fw-bold text-white' : 'btn-light' }}" style="border-radius: 10px;">
            <i class="fas fa-pen me-1"></i> Perlu Revisi ({{ $countRevisi }})
        </a>
        <a href="{{ route('super_admin.pengajuan.index', ['status' => 'disetujui']) }}" class="btn btn-sm {{ $status === 'disetujui' ? 'btn-success fw-bold text-white' : 'btn-light' }}" style="border-radius: 10px;">
            <i class="fas fa-check-circle me-1"></i> Disetujui ({{ $countDisetujui }})
        </a>
        <a href="{{ route('super_admin.pengajuan.index', ['status' => 'ditolak']) }}" class="btn btn-sm {{ $status === 'ditolak' ? 'btn-danger fw-bold text-white' : 'btn-light' }}" style="border-radius: 10px;">
            <i class="fas fa-times-circle me-1"></i> Ditolak ({{ $countDitolak }})
        </a>
    </div>
</div>

<!-- PENGAJUAN LISTING TABLE -->
<div class="sa-card p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="sa-table">
            <thead>
                <tr>
                    <th>No. Pengajuan</th>
                    <th>Wilayah Sasaran</th>
                    <th>Identitas Pemohon</th>
                    <th>Dokumen SK</th>
                    <th>Tanggal Masuk</th>
                    <th>Status</th>
                    <th class="text-end">Tindakan Super Admin</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengajuans as $p)
                    @php
                        $badge = $p->status_badge;
                    @endphp
                    <tr>
                        <td>
                            <strong class="text-dark small d-block">{{ $p->nomor_pengajuan }}</strong>
                            <small class="text-muted">ID: #{{ $p->id }}</small>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">RW {{ $p->rw_number }}</div>
                            <div class="small text-muted">{{ $p->nama_wilayah }}</div>
                            <small class="text-muted">{{ $p->kelurahan }}, {{ $p->kota }}</small>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark small">{{ $p->nama_pemohon }}</div>
                            <small class="text-muted d-block"><i class="fas fa-envelope me-1"></i>{{ $p->email_pemohon }}</small>
                            <small class="text-muted d-block"><i class="fas fa-phone me-1"></i>{{ $p->phone_pemohon }}</small>
                        </td>
                        <td>
                            @if($p->dokumen_sk)
                                <span class="badge bg-light text-dark border px-2 py-1">
                                    <i class="fas fa-file-pdf text-danger me-1"></i>{{ $p->dokumen_sk }}
                                </span>
                            @else
                                <span class="text-muted small fst-italic">Tidak dilampirkan</span>
                            @endif
                        </td>
                        <td class="small text-muted text-nowrap">
                            {{ $p->created_at ? $p->created_at->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td>
                            <span class="badge {{ $badge['class'] }} px-2 py-1" style="font-size: 0.74rem;">
                                <i class="fas {{ $badge['icon'] }} me-1"></i>{{ $badge['label'] }}
                            </span>
                            @if($p->status === 'revisi' && $p->catatan_revisi)
                                <div class="mt-1 small text-info fst-italic" style="font-size: 0.72rem; max-width: 200px;">
                                    Catatan: {{ Str::limit($p->catatan_revisi, 50) }}
                                </div>
                            @elseif($p->status === 'ditolak' && $p->alasan_penolakan)
                                <div class="mt-1 small text-danger fst-italic" style="font-size: 0.72rem; max-width: 200px;">
                                    Alasan: {{ Str::limit($p->alasan_penolakan, 50) }}
                                </div>
                            @endif
                        </td>
                        <td class="text-end">
                            @if($p->status === 'menunggu' || $p->status === 'revisi')
                                <div class="d-inline-flex align-items-center gap-1">
                                    <!-- Tombol Setujui -->
                                    <button type="button" class="btn btn-sm btn-success d-inline-flex align-items-center gap-1 py-1 px-2 fw-semibold" style="font-size: 0.76rem; border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#modalApprove{{ $p->id }}" title="Setujui dan Aktifkan RW">
                                        <i class="fas fa-check"></i>
                                        <span>Setujui</span>
                                    </button>

                                    <!-- Tombol Minta Revisi -->
                                    <button type="button" class="btn btn-sm btn-outline-info d-inline-flex align-items-center gap-1 py-1 px-2 fw-semibold" style="font-size: 0.76rem; border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#modalRevisi{{ $p->id }}" title="Minta Revisi Dokumen">
                                        <i class="fas fa-pen-to-square"></i>
                                        <span>Revisi</span>
                                    </button>

                                    <!-- Tombol Tolak -->
                                    <button type="button" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1 py-1 px-2 fw-semibold" style="font-size: 0.76rem; border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#modalTolak{{ $p->id }}" title="Tolak Permohonan">
                                        <i class="fas fa-xmark"></i>
                                        <span>Tolak</span>
                                    </button>
                                </div>
                            @else
                                <span class="badge bg-light text-muted border">Sudah Diproses</span>
                            @endif
                        </td>
                    </tr>

                    <!-- MODAL APPROVE -->
                    <div class="modal fade" id="modalApprove{{ $p->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow" style="border-radius: 18px;">
                                <div class="modal-header bg-success text-white px-4 py-3">
                                    <h5 class="modal-title fs-6 fw-bold text-white">
                                        <i class="fas fa-check-circle me-1"></i> Konfirmasi Persetujuan RW {{ $p->rw_number }}
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('super_admin.pengajuan.approve', $p->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-body p-4 text-start">
                                        <p class="small text-muted mb-3">
                                            Menyetujui pengajuan ini akan <strong>secara otomatis membuat wilayah RW {{ $p->rw_number }}</strong> dan mengaktifkan akun Admin RW untuk <strong>{{ $p->nama_pemohon }}</strong> ({{ $p->email_pemohon }}).
                                        </p>
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Catatan Otorisasi (Opsional)</label>
                                            <textarea name="catatan_admin" class="form-control" rows="2" placeholder="Catatan persetujuan resmi...">Pengajuan disetujui, SK dan kelengkapan telah diverifikasi.</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer px-4 py-3 border-top" style="background: var(--nx-bg);">
                                        <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-success px-4 fw-bold">Ya, Setujui &amp; Aktifkan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- MODAL REVISI -->
                    <div class="modal fade" id="modalRevisi{{ $p->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow" style="border-radius: 18px;">
                                <div class="modal-header bg-info text-white px-4 py-3">
                                    <h5 class="modal-title fs-6 fw-bold text-white">
                                        <i class="fas fa-pen-to-square me-1"></i> Minta Revisi Pengajuan RW {{ $p->rw_number }}
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('super_admin.pengajuan.revision', $p->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-body p-4 text-start">
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold text-dark">Rincian Dokumen / Data yang Perlu Diperbaiki <span class="text-danger">*</span></label>
                                            <textarea name="catatan_revisi" class="form-control" rows="4" placeholder="Sebutkan secara spesifik apa yang harus diperbaiki pemohon..." required></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer px-4 py-3 border-top" style="background: var(--nx-bg);">
                                        <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-info px-4 fw-bold text-white">Kirim Catatan Revisi</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- MODAL TOLAK -->
                    <div class="modal fade" id="modalTolak{{ $p->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow" style="border-radius: 18px;">
                                <div class="modal-header bg-danger text-white px-4 py-3">
                                    <h5 class="modal-title fs-6 fw-bold text-white">
                                        <i class="fas fa-times-circle me-1"></i> Tolak Pengajuan RW {{ $p->rw_number }}
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('super_admin.pengajuan.reject', $p->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-body p-4 text-start">
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold text-dark">Alasan Penolakan Resmi <span class="text-danger">*</span></label>
                                            <textarea name="alasan_penolakan" class="form-control" rows="4" placeholder="Tuliskan alasan penolakan secara jelas..." required></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer px-4 py-3 border-top" style="background: var(--nx-bg);">
                                        <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-danger px-4 fw-bold">Tolak Pengajuan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-folder-open fa-2x mb-2 d-block"></i>
                            Tidak ada pengajuan RW yang sesuai dengan kriteria filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pengajuans->hasPages())
        <div class="p-3 border-top d-flex justify-content-center">
            {{ $pengajuans->links() }}
        </div>
    @endif
</div>
@endsection
