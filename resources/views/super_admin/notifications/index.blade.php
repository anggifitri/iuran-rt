@extends('layouts.super_admin')

@section('title', 'Broadcast & Notifikasi Platform — NexaNest Super Admin')
@section('page_title', 'Broadcast & Notifikasi Platform')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
    <div>
        <h2 class="fw-bold text-dark m-0 fs-5">Pusat Siaran &amp; Pengumuman Lintas Sistem</h2>
        <p class="text-muted m-0 small">Kirim pengumuman pembaruan, pemeliharaan sistem, atau imbauan darurat ke seluruh RW</p>
    </div>

    <button type="button" class="btn btn-success px-3 py-2 fw-bold d-inline-flex align-items-center gap-2 shadow-sm" style="background: linear-gradient(135deg, #285943 0%, #1E4633 100%); border-radius: 12px; font-size: 0.86rem;" data-bs-toggle="modal" data-bs-target="#modalCreateBroadcast">
        <i class="fas fa-bullhorn"></i>
        <span>Kirim Siaran Baru</span>
    </button>
</div>

<!-- NOTIFICATION LISTING TABLE -->
<div class="sa-card p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="sa-table">
            <thead>
                <tr>
                    <th>Judul Siaran</th>
                    <th>Tipe Notifikasi</th>
                    <th>Target Jangkauan</th>
                    <th>Status Banner</th>
                    <th>Tanggal Terbit</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($notifications as $n)
                    @php
                        $typeBadge = match($n->type) {
                            'info' => 'bg-info-subtle text-info border border-info-subtle',
                            'warning' => 'bg-warning-subtle text-warning border border-warning-subtle',
                            'critical' => 'bg-danger-subtle text-danger border border-danger-subtle',
                            'maintenance' => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
                            default => 'bg-light text-dark'
                        };
                    @endphp
                    <tr>
                        <td>
                            <strong class="text-dark small d-block">{{ $n->title }}</strong>
                            <small class="text-muted d-block text-truncate" style="max-width: 360px;">{{ $n->message }}</small>
                        </td>
                        <td>
                            <span class="badge {{ $typeBadge }} px-2 py-0.5" style="font-size: 0.72rem;">
                                {{ strtoupper($n->type) }}
                            </span>
                        </td>
                        <td>
                            <span class="small fw-semibold text-dark">
                                @if($n->target_scope === 'all')
                                    Semua Wilayah RW
                                @elseif($n->target_scope === 'rw')
                                    Khusus RW {{ $n->target_rw }}
                                @else
                                    Peran {{ strtoupper($n->target_role) }}
                                @endif
                            </span>
                        </td>
                        <td>
                            @if($n->is_banner)
                                <span class="badge bg-warning-subtle text-dark border border-warning" style="font-size: 0.70rem;">
                                    <i class="fas fa-flag me-1"></i> Banner Atas
                                </span>
                            @else
                                <span class="text-muted small">Notifikasi Biasa</span>
                            @endif
                        </td>
                        <td class="small text-muted text-nowrap">
                            {{ $n->created_at ? $n->created_at->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td class="text-end">
                            <form action="{{ route('super_admin.notifications.destroy', $n->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light border text-danger py-1 px-2" style="font-size: 0.75rem; border-radius: 8px;" onclick="return confirm('Hapus siaran ini?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Belum ada pengumuman siaran yang diterbitkan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($notifications->hasPages())
        <div class="p-3 border-top d-flex justify-content-center">
            {{ $notifications->links() }}
        </div>
    @endif
</div>

<!-- MODAL CREATE BROADCAST -->
<div class="modal fade" id="modalCreateBroadcast" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header bg-success text-white px-4 py-3" style="background: linear-gradient(135deg, #1E4633 0%, #285943 100%);">
                <h5 class="modal-title fs-6 fw-bold text-white">
                    <i class="fas fa-bullhorn me-2 text-warning"></i> Buat Siaran Sistem Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('super_admin.notifications.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 text-start">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Judul Pengumuman</label>
                        <input type="text" name="title" class="form-control" placeholder="Contoh: Pemeliharaan Server Terjadwal" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Isi Pesan Siaran</label>
                        <textarea name="message" class="form-control" rows="3" placeholder="Tuliskan pesan yang akan dibaca pengguna..." required></textarea>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tingkat Urgensi</label>
                            <select name="type" class="form-select">
                                <option value="info">Informasi (Biasa)</option>
                                <option value="warning">Peringatan (Warning)</option>
                                <option value="critical">Kritis (Penting)</option>
                                <option value="maintenance">Pemeliharaan (Maintenance)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Target Penerima</label>
                            <select name="target_scope" class="form-select" onchange="document.getElementById('targetRwSelectWrap').style.display = (this.value === 'rw') ? 'block' : 'none'">
                                <option value="all">Semua Pengguna Platform</option>
                                <option value="rw">Khusus Wilayah RW Tertentu</option>
                                <option value="role">Khusus Admin RT & RW</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3" id="targetRwSelectWrap" style="display: none;">
                        <label class="form-label small fw-bold">Pilih RW Sasaran</label>
                        <select name="target_rw" class="form-select">
                            @foreach($rws as $rw)
                                <option value="{{ $rw->rw_number }}">RW {{ $rw->rw_number }} — {{ $rw->nama_wilayah }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="is_banner" value="1" id="checkIsBanner" checked>
                        <label class="form-check-label small fw-semibold" for="checkIsBanner">
                            Tampilkan sebagai Banner Notifikasi Mengambang di atas layar
                        </label>
                    </div>
                </div>
                <div class="modal-footer px-4 py-3 bg-light">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold">Siarkan Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
