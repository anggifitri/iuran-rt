@extends('layouts.super_admin')

@section('title', 'Backup & Pemulihan — NexaNest Super Admin')
@section('page_title', 'Backup & Pemulihan Database')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
    <div>
        <h2 class="fw-bold text-dark m-0 fs-5">Pencadangan &amp; Pemulihan Data Platform</h2>
        <p class="text-muted m-0 small">Simpan salinan database secara berkala untuk menjamin kontinuitas operasional seluruh RW</p>
    </div>

    <form action="{{ route('super_admin.backup.create') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-success px-3 py-2 fw-bold d-inline-flex align-items-center gap-2 shadow-sm" style="background: linear-gradient(135deg, #285943 0%, #1E4633 100%); border-radius: 12px; font-size: 0.86rem;">
            <i class="fas fa-database"></i>
            <span>Buat Cadangan Instan Sekarang</span>
        </button>
    </form>
</div>

<!-- BACKUP CARDS SUMMARY -->
<div class="row g-3 mb-4">
    <div class="col-sm-6">
        <div class="sa-kpi-card p-3">
            <span class="sa-kpi-label">Total Arsip Cadangan</span>
            <div class="d-flex align-items-baseline gap-2">
                <span class="fs-4 fw-bold text-dark">{{ $totalBackupFiles }}</span>
                <span class="small text-muted">File Tersimpan di Server</span>
            </div>
            <small class="text-success fw-semibold"><i class="fas fa-shield-alt me-1"></i>Penyimpanan Aman Terenkripsi</small>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="sa-kpi-card p-3">
            <span class="sa-kpi-label">Format Database</span>
            <div class="d-flex align-items-baseline gap-2">
                <span class="fs-4 fw-bold text-primary">SQLite 3</span>
                <span class="small text-muted">Self-Contained DB</span>
            </div>
            <small class="text-muted">Siap diunduh untuk pemulihan cepat (Disaster Recovery)</small>
        </div>
    </div>
</div>

<!-- BACKUP LIST TABLE -->
<div class="sa-card p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="sa-table">
            <thead>
                <tr>
                    <th>Nama File Cadangan</th>
                    <th>Ukuran File</th>
                    <th>Tipe Cadangan</th>
                    <th>Dibuat Oleh</th>
                    <th>Waktu Pembuatan</th>
                    <th class="text-end">Aksi Unduh</th>
                </tr>
            </thead>
            <tbody>
                @forelse($backups as $b)
                    <tr>
                        <td>
                            <strong class="text-dark small font-monospace d-block">
                                <i class="fas fa-file-zipper text-warning me-1"></i> {{ $b->filename }}
                            </strong>
                            <small class="text-muted">{{ $b->notes ?: 'Cadangan otomatis' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border font-monospace">{{ $b->filesize }}</span>
                        </td>
                        <td>
                            <span class="badge bg-success-subtle text-success border border-success" style="font-size: 0.70rem;">
                                {{ strtoupper($b->type) }}
                            </span>
                        </td>
                        <td>
                            <small class="text-dark fw-semibold">{{ $b->creator ? $b->creator->name : 'Super Admin' }}</small>
                        </td>
                        <td class="small text-muted text-nowrap">
                            {{ $b->created_at ? $b->created_at->format('d/m/Y H:i:s') : '-' }}
                        </td>
                        <td class="text-end">
                            <a href="{{ route('super_admin.backup.download', $b->id) }}" class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1 py-1 px-3 fw-semibold" style="font-size: 0.76rem; border-radius: 8px;">
                                <i class="fas fa-download"></i>
                                <span>Unduh</span>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Belum ada file cadangan yang dibuat. Klik tombol di atas untuk membuat cadangan baru.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($backups->hasPages())
        <div class="p-3 border-top d-flex justify-content-center">
            {{ $backups->links() }}
        </div>
    @endif
</div>
@endsection
