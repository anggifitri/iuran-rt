@extends('layouts.super_admin')

@section('title', 'Log Akses Data Sensitif — NexaNest Super Admin')
@section('page_title', 'Log Akses Data Sensitif')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
    <div>
        <h2 class="fw-bold text-dark m-0 fs-5">Audit Trail Pembukaan Sensor Data Sensitif</h2>
        <p class="text-muted m-0 small">Pencatatan forensik append-only setiap pembukaan NIK, No. KK, No HP, dan identitas warga</p>
    </div>

    <a href="{{ route('super_admin.accounts.index') }}" class="btn btn-outline-secondary px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2" style="border-radius: 12px; font-size: 0.86rem;">
        <i class="fas fa-arrow-left"></i>
        <span>Kembali ke Manajemen Akun</span>
    </a>
</div>

<!-- SENSITIVE LOGS TABLE -->
<div class="sa-card p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="sa-table">
            <thead>
                <tr>
                    <th>Waktu Akses</th>
                    <th>Super Admin (Aktor)</th>
                    <th>Akun Sasaran</th>
                    <th>Jenis Data</th>
                    <th>Alasan Pembukaan</th>
                    <th>Alamat IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td class="small text-muted text-nowrap">
                            {{ $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : '-' }}
                        </td>
                        <td>
                            <strong class="text-dark small d-block">{{ $log->user ? $log->user->name : 'Super Admin' }}</strong>
                            <small class="text-muted">{{ $log->user ? $log->user->email : '-' }}</small>
                        </td>
                        <td>
                            @if($log->targetUser)
                                <div class="fw-semibold text-dark small">{{ $log->targetUser->name }}</div>
                                <small class="text-muted">{{ $log->targetUser->email }} &bull; RW {{ $log->targetUser->rw_number }}</small>
                            @else
                                <span class="small text-muted">{{ $log->target_identifier ?: '-' }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-warning-subtle text-dark border border-warning px-2 py-0.5" style="font-size: 0.72rem;">
                                <i class="fas fa-id-card me-1"></i> Data Identitas (NIK/KK/HP)
                            </span>
                        </td>
                        <td>
                            <div class="small fw-semibold text-dark">{{ $log->reason }}</div>
                        </td>
                        <td class="small text-muted font-monospace">
                            {{ $log->ip_address ?: '127.0.0.1' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-shield-check fa-2x mb-2 text-success d-block"></i>
                            Belum ada catatan pembukaan data sensitif.
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
