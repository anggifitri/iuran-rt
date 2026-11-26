@extends('layouts.app')

@section('title', 'Audit & Aktivitas — NexaNest')

@section('content')
<!-- ==================== HEADER HALAMAN ==================== -->
<div class="nx-page-header mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
            <span class="badge rounded-pill px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
                <i class="fas fa-shield-halved me-1"></i> Pengawasan, Integritas &amp; Transparansi Sistem
            </span>
            @if($isAdminRw)
                <span class="badge rounded-pill px-2.5 py-1 bg-dark" style="font-size: 0.72rem;">
                    <i class="fas fa-crown me-1"></i> Admin RW (Akses Penuh)
                </span>
            @elseif($isAdminRt)
                <span class="badge rounded-pill px-2.5 py-1" style="background: #1E4B6D; color: #fff; font-size: 0.72rem;">
                    <i class="fas fa-user-shield me-1"></i> Admin RT {{ Auth::user()->rt_number }}
                </span>
            @else
                <span class="badge rounded-pill px-2.5 py-1" style="background: var(--nx-mint); color: var(--nx-forest); font-size: 0.72rem;">
                    <i class="fas fa-user-check me-1"></i> Riwayat Akun Warga
                </span>
            @endif
        </div>
        <h1 class="nx-page-title mb-1">
            {{ $isWarga ? 'Riwayat Aktivitas Akun Saya' : 'Audit & Aktivitas Sistem RW 018' }}
        </h1>
        <p class="nx-page-subtitle mb-0">
            {{ $isWarga 
                ? 'Catatan kronologis seluruh interaksi akun, pengajuan surat, transaksi iuran, permohonan fasilitas, dan riwayat login Anda secara transparan.'
                : 'Pusat pemantauan seluruh rekaman aktivitas operasional, perubahan data kependudukan, pengawasan keuangan kas, dan integritas keamanan sistem lingkungan.' 
            }}
        </p>
    </div>

    <!-- ACTION BUTTONS -->
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('audit.index', ['tab' => $activeTab]) }}" class="btn btn-outline-secondary fw-bold" style="border-radius: 12px;" title="Perbarui Data">
            <i class="fas fa-arrows-rotate me-1"></i> Muat Ulang
        </a>

        @if(!$isWarga)
        <button type="button" class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#modalEksporAudit" style="border-radius: 12px; background: var(--nx-forest); border-color: var(--nx-forest);">
            <i class="fas fa-file-export me-1.5"></i> Ekspor Laporan
        </button>
        @endif
    </div>
</div>

<!-- ==================== 4 METRIC STAT CARDS ==================== -->
<div class="row g-3 mb-4">
    <!-- 1. Total Aktivitas -->
    <div class="col-lg-3 col-md-6 col-6">
        <x-stat-card 
            title="Total Aktivitas" 
            value="{{ number_format($totalLogs) }}" 
            sub="{{ $isWarga ? 'Aktivitas akun Anda' : 'Seluruh rekaman terdata' }}" 
            icon="fas fa-list-check" 
            theme="forest" 
        />
    </div>

    <!-- 2. Perubahan Data -->
    <div class="col-lg-3 col-md-6 col-6">
        <x-stat-card 
            title="Perubahan Data" 
            value="{{ number_format($totalChanges) }}" 
            sub="Create / Update / Mutasi" 
            icon="fas fa-code-compare" 
            theme="primary" 
            valColor="#0284c7"
        />
    </div>

    <!-- 3. Pengguna Aktif Terlibat -->
    <div class="col-lg-3 col-md-6 col-6">
        <x-stat-card 
            title="{{ $isWarga ? 'Status Kredensial' : 'Pengguna Aktif' }}" 
            value="{{ $isWarga ? 'Terverifikasi' : number_format($activeUsersCount) }}" 
            sub="{{ $isWarga ? 'Akun warga aktif' : 'Warga & pengurus terekam' }}" 
            icon="fas fa-users" 
            theme="warning" 
            valColor="#d97706"
        />
    </div>

    <!-- 4. Perlu Ditinjau / Anomali -->
    <div class="col-lg-3 col-md-6 col-6">
        <x-stat-card 
            title="{{ $isWarga ? 'Aktivitas Keamanan' : 'Perlu Peninjauan' }}" 
            value="{{ number_format($needsReviewCount) }}" 
            sub="{{ $needsReviewCount > 0 ? 'Anomali / Akses ditolak' : 'Sistem aman & normal' }}" 
            icon="fas fa-triangle-exclamation" 
            theme="{{ $needsReviewCount > 0 ? 'danger' : 'success' }}" 
            valColor="{{ $needsReviewCount > 0 ? '#dc2626' : '#059669' }}"
        />
    </div>
</div>

<!-- ==================== TAB NAVIGATION BAR ==================== -->
<div class="card p-2 border-0 shadow-sm mb-4" style="border-radius: 16px; border: 1px solid var(--nx-border) !important;">
    <ul class="nav nav-pills gap-1 flex-wrap">
        <li class="nav-item">
            <a href="{{ route('audit.index', array_merge(request()->except('tab', 'page'), ['tab' => 'logs'])) }}"
               class="nav-link py-2 px-3 fw-bold {{ $activeTab === 'logs' ? 'active' : 'text-secondary' }}"
               style="{{ $activeTab === 'logs' ? 'background: var(--nx-forest); border-radius: 12px;' : 'border-radius: 12px;' }}">
                <i class="fas fa-table-list me-1.5"></i> Log Audit Terpadu
                <span class="badge ms-1.5 {{ $activeTab === 'logs' ? 'bg-light text-dark' : 'bg-secondary bg-opacity-10 text-secondary' }}">{{ $totalLogs }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('audit.index', array_merge(request()->except('tab', 'page'), ['tab' => 'timeline'])) }}"
               class="nav-link py-2 px-3 fw-bold {{ $activeTab === 'timeline' ? 'active' : 'text-secondary' }}"
               style="{{ $activeTab === 'timeline' ? 'background: var(--nx-forest); border-radius: 12px;' : 'border-radius: 12px;' }}">
                <i class="fas fa-timeline me-1.5"></i> Timeline Kronologis
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('audit.index', array_merge(request()->except('tab', 'page'), ['tab' => 'security'])) }}"
               class="nav-link py-2 px-3 fw-bold {{ $activeTab === 'security' ? 'active' : 'text-secondary' }}"
               style="{{ $activeTab === 'security' ? 'background: #1E4B6D; border-radius: 12px;' : 'border-radius: 12px;' }}">
                <i class="fas fa-shield-halved me-1.5"></i> Keamanan &amp; Autentikasi
                @if($failedLoginCount + $unauthorizedCount > 0)
                <span class="badge ms-1.5 bg-danger text-white">{{ $failedLoginCount + $unauthorizedCount }}</span>
                @endif
            </a>
        </li>
        @if(!$isWarga)
        <li class="nav-item">
            <a href="{{ route('audit.index', array_merge(request()->except('tab', 'page'), ['tab' => 'analytics'])) }}"
               class="nav-link py-2 px-3 fw-bold {{ $activeTab === 'analytics' ? 'active' : 'text-secondary' }}"
               style="{{ $activeTab === 'analytics' ? 'background: #0f766e; border-radius: 12px;' : 'border-radius: 12px;' }}">
                <i class="fas fa-chart-pie me-1.5"></i> Statistik &amp; Analitik
            </a>
        </li>
        @endif
    </ul>
</div>

<!-- ==================== TAB 1: LOG AUDIT TERPADU (TABEL & FILTER) ==================== -->
@if($activeTab === 'logs')
    <!-- FILTER CARD -->
    <div class="card p-3.5 border-0 shadow-sm mb-4" style="border-radius: 18px; border: 1px solid var(--nx-border) !important;">
        <form action="{{ route('audit.index') }}" method="GET" class="row g-2.5 align-items-end">
            <input type="hidden" name="tab" value="logs">

            <!-- Pencarian Teks -->
            <div class="col-lg-3 col-md-6 col-12">
                <label class="form-label small fw-bold text-muted mb-1">Pencarian Kata Kunci</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0" style="border-radius: 12px 0 0 12px; border-color: var(--nx-border);">
                        <i class="fas fa-magnifying-glass text-muted small"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, ID objek, IP, deskripsi..."
                           class="form-control border-start-0 ps-0" style="border-radius: 0 12px 12px 0; border-color: var(--nx-border); font-size: 0.85rem;">
                </div>
            </div>

            <!-- Modul -->
            <div class="col-lg-2 col-md-6 col-6">
                <label class="form-label small fw-bold text-muted mb-1">Modul Sistem</label>
                <select name="module" class="form-select" style="border-radius: 12px; border-color: var(--nx-border); font-size: 0.85rem;">
                    <option value="semua">Semua Modul</option>
                    @foreach($moduleList as $mod)
                        <option value="{{ $mod }}" {{ request('module') == $mod ? 'selected' : '' }}>{{ $mod }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Event -->
            <div class="col-lg-2 col-md-4 col-6">
                <label class="form-label small fw-bold text-muted mb-1">Jenis Tindakan</label>
                <select name="event" class="form-select" style="border-radius: 12px; border-color: var(--nx-border); font-size: 0.85rem;">
                    <option value="semua">Semua Tindakan</option>
                    <option value="create" {{ request('event') == 'create' ? 'selected' : '' }}>CREATE (Penambahan)</option>
                    <option value="update" {{ request('event') == 'update' ? 'selected' : '' }}>UPDATE (Perubahan)</option>
                    <option value="delete" {{ request('event') == 'delete' ? 'selected' : '' }}>DELETE (Penghapusan)</option>
                    <option value="login" {{ request('event') == 'login' ? 'selected' : '' }}>LOGIN</option>
                    <option value="logout" {{ request('event') == 'logout' ? 'selected' : '' }}>LOGOUT</option>
                    <option value="approve" {{ request('event') == 'approve' ? 'selected' : '' }}>APPROVE (Persetujuan)</option>
                    <option value="reject" {{ request('event') == 'reject' ? 'selected' : '' }}>REJECT (Penolakan)</option>
                    <option value="mutate" {{ request('event') == 'mutate' ? 'selected' : '' }}>MUTATE (Mutasi Warga)</option>
                    <option value="export" {{ request('event') == 'export' ? 'selected' : '' }}>EXPORT (Ekspor Data)</option>
                </select>
            </div>

            <!-- Status -->
            <div class="col-lg-2 col-md-4 col-6">
                <label class="form-label small fw-bold text-muted mb-1">Status Hasil</label>
                <select name="status" class="form-select" style="border-radius: 12px; border-color: var(--nx-border); font-size: 0.85rem;">
                    <option value="semua">Semua Status</option>
                    <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>BERHASIL</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>GAGAL</option>
                    <option value="warning" {{ request('status') == 'warning' ? 'selected' : '' }}>PERINGATAN</option>
                    <option value="suspicious" {{ request('status') == 'suspicious' ? 'selected' : '' }}>MENCURIGAKAN</option>
                </select>
            </div>

            <!-- Target RT (Khusus Admin RW) -->
            @if($isAdminRw)
            <div class="col-lg-1 col-md-4 col-6">
                <label class="form-label small fw-bold text-muted mb-1">Wilayah RT</label>
                <select name="rt_number" class="form-select" style="border-radius: 12px; border-color: var(--nx-border); font-size: 0.85rem;">
                    <option value="semua">Semua</option>
                    <option value="000" {{ request('rt_number') == '000' ? 'selected' : '' }}>RW</option>
                    @foreach($rtList as $rt)
                        <option value="{{ $rt }}" {{ request('rt_number') == $rt ? 'selected' : '' }}>RT {{ $rt }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <!-- Tombol Aksi -->
            <div class="{{ $isAdminRw ? 'col-lg-2' : 'col-lg-3' }} col-12 d-flex align-items-center gap-2">
                <button type="submit" class="btn btn-primary fw-bold w-100" style="border-radius: 12px; background: var(--nx-forest); border-color: var(--nx-forest); font-size: 0.85rem;">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                @if(request()->anyFilled(['search', 'module', 'event', 'status', 'rt_number', 'date_from', 'date_to']))
                <a href="{{ route('audit.index', ['tab' => 'logs']) }}" class="btn btn-outline-secondary px-2.5" style="border-radius: 12px;" title="Reset Filter">
                    <i class="fas fa-rotate-left"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- TABEL LOG AUDIT -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 18px; border: 1px solid var(--nx-border) !important; overflow: hidden;">
        <div class="card-header bg-white p-3.5 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2" style="border-color: var(--nx-border) !important;">
            <div>
                <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.94rem;">
                    <i class="fas fa-list-check me-2" style="color: var(--nx-forest);"></i>Daftar Rekaman Audit Operasional
                </h6>
                <div class="text-muted small" style="font-size: 0.76rem;">Catatan bersifat immutable (tidak dapat dihapus atau diubah demi integritas data).</div>
            </div>
            <span class="badge rounded-pill px-3 py-1.5" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-weight: 700; font-size: 0.74rem;">
                Menampilkan {{ $logs->total() }} Catatan
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.82rem;">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 py-3" style="width: 11%;">ID Log</th>
                        <th class="py-3" style="width: 15%;">Waktu (WIB) &amp; IP</th>
                        <th class="py-3" style="width: 18%;">Pengguna</th>
                        <th class="py-3" style="width: 15%;">Modul &amp; Tindakan</th>
                        <th class="py-3" style="width: 10%;">Status</th>
                        <th class="py-3" style="width: 21%;">Ringkasan Aktivitas</th>
                        <th class="pe-3 py-3 text-end" style="width: 10%;">Perubahan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        @php
                            $eb = $log->event_badge;
                            $sb = $log->status_badge;
                        @endphp
                        <tr>
                            <!-- ID Log -->
                            <td class="ps-3">
                                <span class="badge bg-light text-dark font-monospace border px-2 py-1" style="font-size: 0.75rem; border-radius: 8px;">
                                    {{ $log->formatted_id }}
                                </span>
                            </td>

                            <!-- Waktu & IP -->
                            <td>
                                <strong class="d-block" style="color: var(--nx-dark); font-size: 0.82rem;">{{ $log->created_at->format('d/m/Y H:i:s') }}</strong>
                                <small class="text-muted" style="font-size: 0.72rem;">
                                    <i class="fas fa-network-wired me-1 text-muted"></i>{{ $log->ip_address ?: '-' }}
                                </small>
                            </td>

                            <!-- Pengguna -->
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold shrink-0"
                                         style="width: 32px; height: 32px; font-size: 0.75rem; background: var(--nx-mint); color: var(--nx-forest);">
                                        {{ strtoupper(substr($log->user_name ?: 'U', 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold" style="color: var(--nx-dark); line-height: 1.2;">{{ $log->user_name ?: 'Sistem' }}</div>
                                        <span class="badge rounded-pill px-2 py-0.5 mt-0.5" style="font-size: 0.65rem; background: #e2e8f0; color: #475569;">
                                            {{ strtoupper($log->role ?: 'GUEST') }}
                                            @if($log->rt_number && $log->rt_number !== '000') · RT {{ $log->rt_number }} @endif
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Modul & Tindakan -->
                            <td>
                                <span class="d-block fw-bold" style="color: var(--nx-forest); font-size: 0.82rem;">{{ $log->module }}</span>
                                <span class="badge rounded-pill px-2 py-0.5 mt-0.5 border"
                                      style="background: {{ $eb['bg'] }}; color: {{ $eb['color'] }}; border-color: {{ $eb['border'] }} !important; font-size: 0.68rem; font-weight: 700;">
                                    <i class="{{ $eb['icon'] }} me-1"></i> {{ $eb['label'] }}
                                </span>
                            </td>

                            <!-- Status Tindakan -->
                            <td>
                                <span class="badge rounded-pill px-2.5 py-1"
                                      style="background: {{ $sb['bg'] }}; color: {{ $sb['color'] }}; font-size: 0.7rem; font-weight: 700;">
                                    <i class="{{ $sb['icon'] }} me-1"></i> {{ $sb['label'] }}
                                </span>
                            </td>

                            <!-- Deskripsi Ringkasan -->
                            <td>
                                <p class="mb-0 text-dark small" style="line-height: 1.45; font-size: 0.8rem;">{{ $log->description }}</p>
                                @if($log->record_id)
                                    <span class="text-muted" style="font-size: 0.72rem;">Objek: <code>#{{ $log->record_id }}</code></span>
                                @endif
                            </td>

                            <!-- Detail Diff / Perubahan -->
                            <td class="pe-3 text-end">
                                @if($log->old_values || $log->new_values)
                                    <button type="button" class="btn btn-sm btn-outline-secondary px-2.5 py-1"
                                            style="border-radius: 8px; font-size: 0.75rem; font-weight: 600;"
                                            data-bs-toggle="modal" data-bs-target="#modalDiff{{ $log->id }}">
                                        <i class="fas fa-code-compare me-1 text-primary"></i> Before/After
                                    </button>

                                    <!-- MODAL DETAIL PERUBAHAN DATA (BEFORE & AFTER) -->
                                    <div class="modal fade" id="modalDiff{{ $log->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                            <div class="modal-content text-start border-0 shadow-lg" style="border-radius: 20px;">
                                                <div class="modal-header border-bottom px-4 py-3" style="border-color: var(--nx-border) !important;">
                                                    <div>
                                                        <span class="badge rounded-pill px-2.5 py-0.5 mb-1" style="background: var(--nx-mint); color: var(--nx-forest); font-weight: 700; font-size: 0.7rem;">
                                                            {{ $log->formatted_id }} · {{ $log->module }}
                                                        </span>
                                                        <h5 class="modal-title fw-bold" style="font-size: 1.05rem; color: var(--nx-dark);">
                                                            Detail Perubahan Data (Before &amp; After)
                                                        </h5>
                                                    </div>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <!-- Metadata Singkat Perubahan -->
                                                    <div class="card p-3 mb-3 border-0 bg-light" style="border-radius: 12px; font-size: 0.8rem;">
                                                        <div class="row g-2">
                                                            <div class="col-sm-6">
                                                                <span class="text-muted">Pelaku Perubahan:</span>
                                                                <strong class="d-block" style="color: var(--nx-dark);">{{ $log->user_name }} ({{ strtoupper($log->role) }})</strong>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <span class="text-muted">Waktu Perubahan:</span>
                                                                <strong class="d-block" style="color: var(--nx-dark);">{{ $log->created_at->translatedFormat('d F Y, H:i:s') }} WIB</strong>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Tabel Perbandingan Diff -->
                                                    <div class="table-responsive border rounded-3" style="border-color: var(--nx-border) !important;">
                                                        <table class="table table-sm align-middle mb-0" style="font-size: 0.8rem;">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th class="py-2 px-3" style="width: 25%;">Kolom / Bidang</th>
                                                                    <th class="py-2 px-3" style="width: 37.5%; color: #dc2626;"><i class="fas fa-history me-1"></i> Sebelum (Before)</th>
                                                                    <th class="py-2 px-3" style="width: 37.5%; color: var(--nx-forest);"><i class="fas fa-check-circle me-1"></i> Sesudah (After)</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse($log->diff_details as $diff)
                                                                <tr style="{{ $diff['changed'] ? 'background: #fafaf9;' : '' }}">
                                                                    <td class="py-2 px-3 fw-bold" style="color: var(--nx-dark);">
                                                                        {{ $diff['field'] }}
                                                                    </td>
                                                                    <td class="py-2 px-3 font-monospace" style="background: #fef2f2; color: #991b1b; word-break: break-all;">
                                                                        {{ $diff['old'] }}
                                                                    </td>
                                                                    <td class="py-2 px-3 font-monospace" style="background: #ecfdf5; color: #065f46; word-break: break-all;">
                                                                        {{ $diff['new'] }}
                                                                    </td>
                                                                </tr>
                                                                @empty
                                                                <tr>
                                                                    <td colspan="3" class="text-center py-3 text-muted">Tidak ada rincian pasangan nilai.</td>
                                                                </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top px-4 py-2.5" style="border-color: var(--nx-border) !important;">
                                                    <button type="button" class="btn btn-secondary btn-sm fw-bold px-3 py-1.5" data-bs-dismiss="modal" style="border-radius: 10px;">
                                                        Tutup
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                                     style="width: 56px; height: 56px; background: var(--nx-mint); color: var(--nx-forest); font-size: 1.5rem;">
                                    <i class="fas fa-clipboard-check"></i>
                                </div>
                                <h6 class="fw-bold mb-1" style="color: var(--nx-dark);">Belum Ada Riwayat Aktivitas</h6>
                                <p class="text-muted small mb-0">Tidak ditemukan rekaman aktivitas yang sesuai dengan parameter filter Anda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="card-footer bg-white p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2" style="border-color: var(--nx-border) !important;">
            <small class="text-muted">Menampilkan {{ $logs->firstItem() }} - {{ $logs->lastItem() }} dari {{ $logs->total() }} catatan</small>
            {{ $logs->links() }}
        </div>
        @endif
    </div>

<!-- ==================== TAB 2: TIMELINE KRONOLOGIS ==================== -->
@elseif($activeTab === 'timeline')
    <div class="card p-4 border-0 shadow-sm mb-4" style="border-radius: 18px; border: 1px solid var(--nx-border) !important;">
        <div class="d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom" style="border-color: var(--nx-border) !important;">
            <div>
                <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.94rem;">
                    <i class="fas fa-timeline me-2" style="color: var(--nx-forest);"></i>Timeline Aktivitas Kronologis
                </h6>
                <div class="text-muted small" style="font-size: 0.76rem;">Rangkaian peristiwa sistem yang dikelompokkan secara terstruktur berdasarkan tanggal.</div>
            </div>
            <span class="badge rounded-pill px-3 py-1.5" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-weight: 700; font-size: 0.74rem;">
                {{ count($timelineLogs) }} Kelompok Hari
            </span>
        </div>

        <div class="timeline-container px-2">
            @forelse($timelineLogs as $dateLabel => $groupLogs)
                <!-- Header Tanggal -->
                <div class="d-flex align-items-center gap-2 mb-3 mt-3">
                    <span class="badge rounded-pill px-3 py-1.5 shadow-xs" style="background: var(--nx-forest); color: #fff; font-size: 0.78rem; font-weight: 700;">
                        <i class="far fa-calendar-days me-1.5"></i> {{ $dateLabel }}
                    </span>
                    <hr class="flex-grow-1 my-0 text-muted" style="opacity: 0.2;">
                </div>

                <!-- Daftar Event pada Tanggal Ini -->
                <div class="ps-3 border-start ms-3 mb-4" style="border-color: var(--nx-border) !important; border-width: 2px !important;">
                    @foreach($groupLogs as $item)
                        @php
                            $eb = $item->event_badge;
                        @endphp
                        <div class="position-relative mb-3.5 ps-3">
                            <!-- Bullet Marker -->
                            <div class="position-absolute rounded-circle"
                                 style="left: -23px; top: 3px; width: 14px; height: 14px; background: #fff; border: 3px solid {{ $eb['color'] }};">
                            </div>

                            <div class="card p-3 border-0 shadow-xs" style="border-radius: 14px; background: #fafafa; border: 1px solid var(--nx-border) !important;">
                                <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mb-1.5">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="badge rounded-pill px-2.5 py-0.5 border"
                                              style="background: {{ $eb['bg'] }}; color: {{ $eb['color'] }}; font-size: 0.68rem; font-weight: 700;">
                                            <i class="{{ $eb['icon'] }} me-1"></i> {{ $eb['label'] }}
                                        </span>
                                        <span class="fw-bold" style="color: var(--nx-forest); font-size: 0.8rem;">
                                            {{ $item->module }}
                                        </span>
                                        @if($item->rt_number && $item->rt_number !== '000')
                                        <span class="badge bg-light text-secondary rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">
                                            RT {{ $item->rt_number }}
                                        </span>
                                        @endif
                                    </div>
                                    <span class="text-muted small" style="font-size: 0.74rem;">
                                        <i class="far fa-clock me-1"></i> {{ $item->created_at->format('H:i:s') }} WIB ({{ $item->created_at->diffForHumans() }})
                                    </span>
                                </div>

                                <p class="mb-1 text-dark small" style="line-height: 1.45; font-size: 0.82rem;">
                                    {{ $item->description }}
                                </p>

                                <div class="d-flex align-items-center justify-content-between text-muted small mt-2 pt-2 border-top" style="font-size: 0.72rem; border-color: #eee !important;">
                                    <span>
                                        <i class="fas fa-user-circle me-1"></i> Oleh: <strong>{{ $item->user_name ?: 'Sistem' }}</strong> ({{ strtoupper($item->role) }})
                                    </span>
                                    <span>
                                        <i class="fas fa-network-wired me-1"></i> {{ $item->ip_address ?: '-' }} · {{ $item->device_label }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @empty
                <div class="text-center py-5">
                    <p class="text-muted small mb-0">Belum ada riwayat aktivitas pada timeline ini.</p>
                </div>
            @endforelse
        </div>
    </div>

<!-- ==================== TAB 3: KEAMANAN & AUTENTIKASI ==================== -->
@elseif($activeTab === 'security')
    <!-- WARNING BOX IF SUSPICIOUS ATTEMPTS EXIST -->
    @if($failedLoginCount + $unauthorizedCount > 0)
    <div class="alert alert-danger d-flex align-items-start gap-3 p-3.5 mb-4 shadow-sm" style="border-radius: 16px; border: 2px solid #f87171; background: #fef2f2;">
        <div class="rounded-circle d-flex align-items-center justify-content-center shrink-0" style="width: 44px; height: 44px; background: #fee2e2; color: #dc2626; font-size: 1.3rem;">
            <i class="fas fa-shield-virus"></i>
        </div>
        <div>
            <h6 class="fw-bold text-danger text-uppercase mb-1" style="letter-spacing: 0.05em; font-size: 0.85rem;">
                Peringatan Keamanan Sistem (Deteksi Aktivitas Tidak Wajar)
            </h6>
            <p class="mb-0 small text-danger" style="font-size: 0.82rem; line-height: 1.45;">
                Ditemukan total <strong>{{ $failedLoginCount }} percobaan login gagal</strong> dan <strong>{{ $unauthorizedCount }} percobaan akses tidak diizinkan</strong>. Seluruh alamat IP dan identitas perangkat telah direkam otomatis oleh firewall sistem NexaNest untuk keperluan audit keamanan.
            </p>
        </div>
    </div>
    @endif

    <!-- 2 MINI STAT CARDS KEAMANAN -->
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-12">
            <div class="card p-3 border-0 shadow-sm" style="border-radius: 16px; border: 1px solid #fecaca !important; background: #fff5f5;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-danger small fw-bold">Percobaan Login Gagal</span>
                        <h3 class="fw-bold text-danger mb-0 mt-1">{{ number_format($failedLoginCount) }}</h3>
                        <span class="text-muted" style="font-size: 0.72rem;">Kredensial atau password tidak cocok</span>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #fee2e2; color: #dc2626; font-size: 1.3rem;">
                        <i class="fas fa-user-xmark"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="card p-3 border-0 shadow-sm" style="border-radius: 16px; border: 1px solid #fed7aa !important; background: #fffaf0;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-warning-emphasis small fw-bold">Percobaan Akses Tidak Diizinkan (403)</span>
                        <h3 class="fw-bold text-warning-emphasis mb-0 mt-1">{{ number_format($unauthorizedCount) }}</h3>
                        <span class="text-muted" style="font-size: 0.72rem;">Upaya menerobos hak otorisasi role</span>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #ffedd5; color: #ea580c; font-size: 1.3rem;">
                        <i class="fas fa-ban"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TABEL LOG KEAMANAN & SESI AUTENTIKASI -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 18px; border: 1px solid var(--nx-border) !important; overflow: hidden;">
        <div class="card-header bg-white p-3.5 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2" style="border-color: var(--nx-border) !important;">
            <div>
                <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.94rem;">
                    <i class="fas fa-fingerprint me-2" style="color: #1E4B6D;"></i>Log Autentikasi, Sesi Pengguna &amp; Keamanan Jaringan
                </h6>
                <div class="text-muted small" style="font-size: 0.76rem;">Pencatatan login, logout, reset kata sandi, dan deteksi anomali perangkat.</div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.82rem;">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 py-3" style="width: 15%;">Waktu (WIB)</th>
                        <th class="py-3" style="width: 20%;">Pengguna / Target Akun</th>
                        <th class="py-3" style="width: 14%;">Jenis Sesi</th>
                        <th class="py-3" style="width: 16%;">Alamat IP &amp; Wilayah</th>
                        <th class="py-3" style="width: 20%;">Perangkat / Browser</th>
                        <th class="pe-3 py-3 text-end" style="width: 15%;">Status Keamanan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($securityLogs as $sLog)
                        @php
                            $sb = $sLog->status_badge;
                        @endphp
                        <tr>
                            <td class="ps-3">
                                <strong class="d-block" style="color: var(--nx-dark); font-size: 0.82rem;">{{ $sLog->created_at->format('d/m/Y H:i:s') }}</strong>
                                <small class="text-muted" style="font-size: 0.72rem;">{{ $sLog->created_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                <strong>{{ $sLog->user_name ?: 'Anonim' }}</strong>
                                <div class="text-muted small" style="font-size: 0.72rem;">
                                    Role: {{ strtoupper($sLog->role ?: 'GUEST') }}
                                </div>
                            </td>
                            <td>
                                <span class="badge rounded-pill px-2.5 py-1"
                                      style="background: {{ $sLog->event === 'login' ? '#ECFDF5' : ($sLog->event === 'logout' ? '#F1F5F9' : '#FEE2E2') }}; color: {{ $sLog->event === 'login' ? '#047857' : ($sLog->event === 'logout' ? '#475569' : '#DC2626') }}; font-weight: 700; font-size: 0.7rem;">
                                    {{ strtoupper($sLog->event) }}
                                </span>
                            </td>
                            <td>
                                <code>{{ $sLog->ip_address ?: '-' }}</code>
                                @if($sLog->rt_number && $sLog->rt_number !== '000')
                                <span class="badge bg-light text-secondary rounded-pill px-2 py-0.5 ms-1" style="font-size: 0.65rem;">
                                    RT {{ $sLog->rt_number }}
                                </span>
                                @endif
                            </td>
                            <td>
                                <span class="text-muted" style="font-size: 0.78rem;">
                                    <i class="fas fa-desktop me-1 text-muted"></i> {{ $sLog->device_label }}
                                </span>
                            </td>
                            <td class="pe-3 text-end">
                                <span class="badge rounded-pill px-2.5 py-1"
                                      style="background: {{ $sb['bg'] }}; color: {{ $sb['color'] }}; font-size: 0.7rem; font-weight: 700;">
                                    <i class="{{ $sb['icon'] }} me-1"></i> {{ $sb['label'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                Tidak ada rekaman log keamanan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($securityLogs->hasPages())
        <div class="card-footer bg-white p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2" style="border-color: var(--nx-border) !important;">
            <small class="text-muted">Menampilkan {{ $securityLogs->firstItem() }} - {{ $securityLogs->lastItem() }} dari {{ $securityLogs->total() }} catatan</small>
            {{ $securityLogs->links() }}
        </div>
        @endif
    </div>

<!-- ==================== TAB 4: STATISTIK & ANALITIK SISTEM ==================== -->
@elseif($activeTab === 'analytics' && !$isWarga)
    <div class="row g-4 mb-4">
        <!-- Grafik Tren Waktu (14 Hari Terakhir) -->
        <div class="col-lg-8 col-12">
            <div class="card p-4 border-0 shadow-sm h-100" style="border-radius: 18px; border: 1px solid var(--nx-border) !important;">
                <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom" style="border-color: var(--nx-border) !important;">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.94rem;">
                            <i class="fas fa-chart-line me-2" style="color: var(--nx-forest);"></i>Tren Frekuensi Aktivitas Sistem (14 Hari Terakhir)
                        </h6>
                        <small class="text-muted" style="font-size: 0.74rem;">Pantau lonjakan aktivitas, interaksi pengguna, dan pembaruan data harian.</small>
                    </div>
                    <span class="badge rounded-pill px-3 py-1" style="background: var(--nx-mint); color: var(--nx-forest); font-weight: 700; font-size: 0.72rem;">
                        Real-time Data
                    </span>
                </div>
                <div style="height: 280px; position: relative;">
                    <canvas id="chartAuditTrend"></canvas>
                </div>
            </div>
        </div>

        <!-- Distribusi Role Pengguna -->
        <div class="col-lg-4 col-12">
            <div class="card p-4 border-0 shadow-sm h-100" style="border-radius: 18px; border: 1px solid var(--nx-border) !important;">
                <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom" style="border-color: var(--nx-border) !important;">
                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.94rem;">
                        <i class="fas fa-users-gear me-2" style="color: #1E4B6D;"></i>Distribusi Aktor Role
                    </h6>
                </div>
                <div style="height: 220px; position: relative;">
                    <canvas id="chartAuditRole"></canvas>
                </div>
                <div class="mt-3 pt-3 border-top" style="border-color: var(--nx-border) !important;">
                    <div class="d-flex align-items-center justify-content-between small mb-1">
                        <span class="text-muted"><i class="fas fa-circle me-1" style="color: #285943;"></i> Administrator RW:</span>
                        <strong class="text-dark">{{ $roleStats['admin'] ?? ($roleStats['admin_rw'] ?? 0) }}</strong>
                    </div>
                    <div class="d-flex align-items-center justify-content-between small mb-1">
                        <span class="text-muted"><i class="fas fa-circle me-1" style="color: #1E4B6D;"></i> Administrator RT:</span>
                        <strong class="text-dark">{{ $roleStats['admin_rt'] ?? 0 }}</strong>
                    </div>
                    <div class="d-flex align-items-center justify-content-between small">
                        <span class="text-muted"><i class="fas fa-circle me-1" style="color: #A8C9B5;"></i> Warga Lingkungan:</span>
                        <strong class="text-dark">{{ $roleStats['warga'] ?? 0 }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Distribusi Berdasarkan Modul -->
        <div class="col-lg-6 col-12">
            <div class="card p-4 border-0 shadow-sm h-100" style="border-radius: 18px; border: 1px solid var(--nx-border) !important;">
                <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom" style="border-color: var(--nx-border) !important;">
                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.94rem;">
                        <i class="fas fa-cubes me-2" style="color: var(--nx-forest);"></i>Aktivitas Terbanyak Berdasarkan Modul
                    </h6>
                    <small class="text-muted" style="font-size: 0.72rem;">Top 6 Modul</small>
                </div>
                <div style="height: 250px; position: relative;">
                    <canvas id="chartAuditModule"></canvas>
                </div>
            </div>
        </div>

        <!-- Distribusi Berdasarkan Status Tindakan -->
        <div class="col-lg-6 col-12">
            <div class="card p-4 border-0 shadow-sm h-100" style="border-radius: 18px; border: 1px solid var(--nx-border) !important;">
                <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom" style="border-color: var(--nx-border) !important;">
                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.94rem;">
                        <i class="fas fa-shield-halved me-2" style="color: #059669;"></i>Integritas Status Eksekusi Tindakan
                    </h6>
                </div>
                <div class="row g-3 align-items-center">
                    <div class="col-sm-6">
                        <div style="height: 200px; position: relative;">
                            <canvas id="chartAuditStatus"></canvas>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="space-y-2">
                            <div class="p-2.5 rounded-3 mb-2" style="background: #ecfdf5; border: 1px solid #a7f3d0;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small fw-bold text-success"><i class="fas fa-check-circle me-1"></i> Berhasil (Normal)</span>
                                    <span class="fw-bold text-success">{{ $statusStats['success'] ?? 0 }}</span>
                                </div>
                            </div>
                            <div class="p-2.5 rounded-3 mb-2" style="background: #fefce8; border: 1px solid #fef08a;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small fw-bold text-warning-emphasis"><i class="fas fa-triangle-exclamation me-1"></i> Peringatan</span>
                                    <span class="fw-bold text-warning-emphasis">{{ $statusStats['warning'] ?? 0 }}</span>
                                </div>
                            </div>
                            <div class="p-2.5 rounded-3 mb-2" style="background: #fef2f2; border: 1px solid #fecaca;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small fw-bold text-danger"><i class="fas fa-circle-xmark me-1"></i> Gagal</span>
                                    <span class="fw-bold text-danger">{{ $statusStats['failed'] ?? 0 }}</span>
                                </div>
                            </div>
                            <div class="p-2.5 rounded-3" style="background: #fff1f2; border: 1px solid #fecdd3;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small fw-bold" style="color: #be123c;"><i class="fas fa-shield-virus me-1"></i> Mencurigakan</span>
                                    <span class="fw-bold" style="color: #be123c;">{{ $statusStats['suspicious'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- ==================== MODAL EKSPOR LAPORAN AUDIT ==================== -->
@if(!$isWarga)
<div class="modal fade" id="modalEksporAudit" tabindex="-1" aria-labelledby="modalEksporAuditLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-bottom px-4 py-3" style="border-color: var(--nx-border) !important;">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: var(--nx-mint); color: var(--nx-forest);">
                        <i class="fas fa-file-export"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold mb-0" style="color: var(--nx-dark);">Ekspor Laporan Audit &amp; Aktivitas</h6>
                        <small class="text-muted" style="font-size: 0.75rem;">Pilih parameter filter dan format berkas ekspor</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="formEksporAudit" method="GET" action="{{ route('audit.export_pdf') }}" target="_blank">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Format Ekspor Dokumen</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="card p-2.5 cursor-pointer text-decoration-none d-flex flex-row align-items-center justify-content-between" style="border-radius: 12px; border: 1px solid var(--nx-sage); background: var(--nx-mint-soft); cursor: pointer;">
                                    <span class="small fw-bold d-flex align-items-center gap-1.5" style="color: var(--nx-forest);">
                                        <i class="fas fa-file-pdf text-danger"></i> Dokumen PDF
                                    </span>
                                    <input type="radio" name="export_format" value="pdf" checked class="form-check-input" onchange="updateExportAction(this.value)">
                                </label>
                            </div>
                            <div class="col-6">
                                <label class="card p-2.5 cursor-pointer text-decoration-none d-flex flex-row align-items-center justify-content-between" style="border-radius: 12px; border: 1px solid #bfdbfe; background: #eff6ff; cursor: pointer;">
                                    <span class="small fw-bold text-primary d-flex align-items-center gap-1.5">
                                        <i class="fas fa-file-excel text-success"></i> Berkas Excel / CSV
                                    </span>
                                    <input type="radio" name="export_format" value="excel" class="form-check-input" onchange="updateExportAction(this.value)">
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-2.5 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Dari Tanggal</label>
                            <input type="date" name="date_from" value="{{ request('date_from', date('Y-m-01')) }}" class="form-control" style="border-radius: 10px; font-size: 0.85rem;">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Sampai Tanggal</label>
                            <input type="date" name="date_to" value="{{ request('date_to', date('Y-m-d')) }}" class="form-control" style="border-radius: 10px; font-size: 0.85rem;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Filter Modul</label>
                        <select name="module" class="form-select" style="border-radius: 10px; font-size: 0.85rem;">
                            <option value="">Semua Modul</option>
                            @foreach($moduleList as $mod)
                                <option value="{{ $mod }}" {{ request('module') == $mod ? 'selected' : '' }}>{{ $mod }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if($isAdminRw)
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Cakupan Wilayah</label>
                        <select name="rt_number" class="form-select" style="border-radius: 10px; font-size: 0.85rem;">
                            <option value="">Seluruh Wilayah RW 018 (Agregat)</option>
                            @foreach($rtList as $rt)
                                <option value="{{ $rt }}" {{ request('rt_number') == $rt ? 'selected' : '' }}>Khusus RT {{ $rt }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>

                <div class="modal-footer border-top px-4 py-2.5 d-flex justify-content-between" style="border-color: var(--nx-border) !important;">
                    <button type="button" class="btn btn-light fw-bold px-3 py-2" data-bs-dismiss="modal" style="border-radius: 10px;">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary fw-bold px-4 py-2" style="border-radius: 10px; background: var(--nx-forest); border-color: var(--nx-forest);">
                        <i class="fas fa-download me-1.5"></i> Ekspor Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function updateExportAction(format) {
        const form = document.getElementById('formEksporAudit');
        if (format === 'excel') {
            form.action = "{{ route('audit.export_excel') }}";
            form.removeAttribute('target');
        } else {
            form.action = "{{ route('audit.export_pdf') }}";
            form.setAttribute('target', '_blank');
        }
    }
</script>
@endif

<!-- ==================== CHART.JS SCRIPT (HANYA AKTIF PADA TAB ANALYTICS) ==================== -->
@if($activeTab === 'analytics' && !$isWarga)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // 1. Chart Tren Aktivitas 14 Hari
        const ctxTrend = document.getElementById('chartAuditTrend').getContext('2d');
        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartDates) !!},
                datasets: [{
                    label: 'Jumlah Aktivitas Harian',
                    data: {!! json_encode($chartActivityCounts) !!},
                    borderColor: '#285943',
                    backgroundColor: 'rgba(40, 89, 67, 0.08)',
                    fill: true,
                    tension: 0.35,
                    borderWidth: 2.5,
                    pointBackgroundColor: '#285943',
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#263449',
                        padding: 10,
                        titleFont: { size: 12, weight: 'bold' }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0, font: { size: 11 } },
                        grid: { color: 'rgba(229, 235, 230, 0.6)' }
                    },
                    x: {
                        ticks: { font: { size: 10.5 } },
                        grid: { display: false }
                    }
                }
            }
        });

        // 2. Chart Distribusi Role
        const ctxRole = document.getElementById('chartAuditRole').getContext('2d');
        new Chart(ctxRole, {
            type: 'doughnut',
            data: {
                labels: ['Admin RW', 'Admin RT', 'Warga'],
                datasets: [{
                    data: [
                        {{ $roleStats['admin'] ?? ($roleStats['admin_rw'] ?? 0) }},
                        {{ $roleStats['admin_rt'] ?? 0 }},
                        {{ $roleStats['warga'] ?? 0 }}
                    ],
                    backgroundColor: ['#285943', '#1E4B6D', '#A8C9B5'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } }
                },
                cutout: '70%'
            }
        });

        // 3. Chart Distribusi Modul
        const ctxModule = document.getElementById('chartAuditModule').getContext('2d');
        new Chart(ctxModule, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartModuleLabels) !!},
                datasets: [{
                    label: 'Aktivitas Tercatat',
                    data: {!! json_encode($chartModuleCounts) !!},
                    backgroundColor: '#1E4B6D',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } },
                    x: { ticks: { font: { size: 10 } } }
                }
            }
        });

        // 4. Chart Status Integritas
        const ctxStatus = document.getElementById('chartAuditStatus').getContext('2d');
        new Chart(ctxStatus, {
            type: 'pie',
            data: {
                labels: ['Berhasil', 'Peringatan', 'Gagal', 'Mencurigakan'],
                datasets: [{
                    data: [
                        {{ $statusStats['success'] ?? 0 }},
                        {{ $statusStats['warning'] ?? 0 }},
                        {{ $statusStats['failed'] ?? 0 }},
                        {{ $statusStats['suspicious'] ?? 0 }}
                    ],
                    backgroundColor: ['#059669', '#d97706', '#dc2626', '#be123c'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });
    });
</script>
@endif

@endsection
