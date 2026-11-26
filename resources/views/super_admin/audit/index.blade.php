@extends('layouts.super_admin')

@section('title', 'Audit & Keamanan Platform — NexaNest Super Admin')
@section('page_title', 'Audit & Keamanan Platform')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
    <div>
        <h2 class="fw-bold text-dark m-0 fs-5">Audit Trail &amp; Keamanan Seluruh Ekosistem</h2>
        <p class="text-muted m-0 small">Pemantauan append-only seluruh aktivitas pengguna, perubahan data, dan keamanan multi-RW</p>
    </div>

    <a href="{{ route('super_admin.audit.export_csv') }}" class="btn btn-outline-success px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2" style="border-radius: 12px; font-size: 0.86rem;">
        <i class="fas fa-file-csv"></i>
        <span>Export Log (CSV)</span>
    </a>
</div>

<!-- AUDIT METRIC SUMMARY -->
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="sa-kpi-card p-3">
            <span class="sa-kpi-label">Total Aktivitas Tercatat</span>
            <div class="d-flex align-items-baseline gap-2">
                <span class="fs-4 fw-bold text-dark">{{ $totalLogs }}</span>
                <span class="small text-muted">Log Sistem</span>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="sa-kpi-card p-3">
            <span class="sa-kpi-label">Aksi Berhasil (Normal)</span>
            <div class="d-flex align-items-baseline gap-2">
                <span class="fs-4 fw-bold text-success">{{ $totalSuccess }}</span>
                <span class="small text-muted">Operasi Sukses</span>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="sa-kpi-card p-3">
            <span class="sa-kpi-label">Peringatan &amp; Keamanan</span>
            <div class="d-flex align-items-baseline gap-2">
                <span class="fs-4 fw-bold text-danger">{{ $totalAlerts }}</span>
                <span class="small text-muted">Perlu Ditinjau</span>
            </div>
        </div>
    </div>
</div>

<!-- FILTER BAR -->
<div class="sa-card mb-4 p-3">
    <form method="GET" action="{{ route('super_admin.audit.index') }}" class="row g-2 align-items-center">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama, event, keterangan, IP..." value="{{ $search }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="module" class="form-select">
                <option value="">Semua Modul Platform</option>
                @foreach($modules as $m)
                    <option value="{{ $m }}" {{ $module === $m ? 'selected' : '' }}>{{ $m }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">Semua Status</option>
                <option value="success" {{ $status === 'success' ? 'selected' : '' }}>Sukses</option>
                <option value="failed" {{ $status === 'failed' ? 'selected' : '' }}>Gagal</option>
                <option value="warning" {{ $status === 'warning' ? 'selected' : '' }}>Warning</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-outline-success fw-semibold px-3 flex-grow-1" style="border-radius: 10px;">
                <i class="fas fa-filter me-1"></i> Filter
            </button>
            @if($search || $module || $status)
                <a href="{{ route('super_admin.audit.index') }}" class="btn btn-light border px-3" style="border-radius: 10px;" title="Reset Filter">
                    <i class="fas fa-rotate-left"></i>
                </a>
            @endif
        </div>
    </form>
</div>

<!-- AUDIT LOGS TABLE -->
<div class="sa-card p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="sa-table">
            <thead>
                <tr>
                    <th>Waktu Kejadian</th>
                    <th>Aktor &amp; Peran</th>
                    <th>Modul Sistem</th>
                    <th>Peristiwa (Event)</th>
                    <th>Deskripsi / Catatan</th>
                    <th>IP / Perangkat</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    @php
                        $statusBadge = match($log->status) {
                            'success' => 'bg-success-subtle text-success border border-success-subtle',
                            'warning' => 'bg-warning-subtle text-warning border border-warning-subtle',
                            'danger', 'failed' => 'bg-danger-subtle text-danger border border-danger-subtle',
                            default => 'bg-light text-dark'
                        };
                    @endphp
                    <tr>
                        <td class="small text-muted text-nowrap">
                            {{ $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : '-' }}
                        </td>
                        <td>
                            <strong class="text-dark small d-block">{{ $log->user_name ?: 'Sistem' }}</strong>
                            <span class="badge bg-light text-dark border" style="font-size: 0.68rem;">
                                {{ strtoupper($log->role ?: 'GUEST') }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.72rem;">
                                {{ $log->module ?: 'Platform' }}
                            </span>
                        </td>
                        <td>
                            <span class="fw-bold text-dark font-monospace" style="font-size: 0.78rem;">
                                {{ $log->event }}
                            </span>
                        </td>
                        <td>
                            <div class="small text-dark" style="max-width: 380px;">
                                {{ $log->description ?: '-' }}
                            </div>
                        </td>
                        <td class="small text-muted font-monospace">
                            {{ $log->ip_address ?: '127.0.0.1' }}
                        </td>
                        <td>
                            <span class="badge {{ $statusBadge }} px-2 py-0.5" style="font-size: 0.72rem;">
                                {{ strtoupper($log->status ?: 'OK') }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-list-check fa-2x mb-2 d-block"></i>
                            Tidak ada log aktivitas yang cocok dengan filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
        <div class="p-3 border-top d-flex justify-content-center">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection
