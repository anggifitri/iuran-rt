@extends('layouts.super_admin')

@section('title', 'Manajemen Modul RW 018 — NexaNest Super Admin')
@section('page_title', 'Manajemen Modul Platform')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
    <div>
        <h2 class="fw-bold text-dark m-0 fs-5">Katalog &amp; Kontrol 22 Modul Fitur RW 018</h2>
        <p class="text-muted m-0 small">Aktifkan atau nonaktifkan modul fitur NexaNest untuk operasional RW 018 secara aman</p>
    </div>

    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 fw-bold" style="font-size: 0.78rem;">
            <i class="fas fa-cube me-1"></i> 22 Modul Terdaftar
        </span>
    </div>
</div>

<!-- INFORMATIONAL BANNER -->
<div class="alert alert-light border border-info-subtle d-flex align-items-center justify-content-between p-3 mb-4 rounded-3" style="background: #F0F9FF;">
    <div class="d-flex align-items-center gap-2.5">
        <i class="fas fa-shield-alt text-info fs-4"></i>
        <div class="small text-muted">
            <strong class="text-dark">Proteksi Integritas Data Modul:</strong>
            Menonaktifkan modul hanya akan menyembunyikan akses operasional pada antarmuka dan memblokir endpoint rute di backend. Seluruh riwayat transaksi, data kependudukan, arsip surat, dan kas warga tetap tersimpan utuh dan tidak pernah dihapus.
        </div>
    </div>
    <span class="badge bg-info-subtle text-info border border-info d-none d-md-inline-block">Data Safe</span>
</div>

<!-- 22 MODULES GRID -->
<div class="row g-3">
    @foreach($modules as $m)
        <div class="col-md-6 col-xl-6">
            <div class="sa-card h-100 d-flex flex-column justify-content-between p-3.5">
                <div>
                    <div class="d-flex align-items-start justify-content-between gap-3 mb-2.5">
                        <div class="d-flex align-items-center gap-3">
                            <div class="sa-kpi-icon" style="background: {{ $m->is_enabled_globally ? '#E8F5E9' : '#FEE2E2' }}; color: {{ $m->is_enabled_globally ? '#2E7D32' : '#DC2626' }}; width: 44px; height: 44px; font-size: 1.15rem;">
                                <i class="fas {{ $m->icon ?: 'fa-cubes' }}"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark m-0 fs-6">{{ $m->module_name }}</h5>
                                <span class="badge bg-light text-muted font-monospace border" style="font-size: 0.68rem;">{{ $m->module_key }}</span>
                            </div>
                        </div>

                        <!-- Toggle Button -->
                        <form action="{{ route('super_admin.modules.toggle_global', $m->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm {{ $m->is_enabled_globally ? 'btn-success' : 'btn-outline-danger' }} d-inline-flex align-items-center gap-1.5 px-3 py-1 fw-bold shadow-sm" style="border-radius: 20px; font-size: 0.76rem;" title="Klik untuk mengubah status aktif modul">
                                <i class="fas {{ $m->is_enabled_globally ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                <span>{{ $m->is_enabled_globally ? 'Aktif di RW 018' : 'Dinonaktifkan' }}</span>
                            </button>
                        </form>
                    </div>

                    <p class="text-muted small mb-0" style="font-size: 0.82rem; line-height: 1.45;">
                        {{ $m->description }}
                    </p>
                </div>

                <div class="pt-2.5 mt-3 border-top d-flex align-items-center justify-content-between" style="font-size: 0.74rem;">
                    <span class="text-muted">
                        Cakupan Otorisasi: <strong>RW 018 (RT 006 - RT 010)</strong>
                    </span>
                    <span class="badge {{ $m->is_enabled_globally ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} px-2 py-0.5">
                        {{ $m->is_enabled_globally ? 'Online' : 'Offline' }}
                    </span>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
