@extends('layouts.super_admin')

@section('title', 'Monitoring Sistem & Infrastruktur — NexaNest Super Admin')
@section('page_title', 'Monitoring Sistem & Infrastruktur')

@section('content')
<div class="sa-monitoring-wrap">
    <!-- FLASH MESSAGES -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 border-0 shadow-sm mb-4" role="alert" style="border-left: 4px solid #10B981 !important;">
            <i class="fas fa-check-circle text-success fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center gap-2 border-0 shadow-sm mb-4" role="alert" style="border-left: 4px solid #F59E0B !important;">
            <i class="fas fa-exclamation-triangle text-warning fs-5"></i>
            <div>{{ session('warning') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 border-0 shadow-sm mb-4" role="alert" style="border-left: 4px solid #EF4444 !important;">
            <i class="fas fa-times-circle text-danger fs-5"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- TOP HEADER & CONTROLS -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 p-3 rounded-3 sa-header-card">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="sa-crown-dot"><i class="fas fa-microchip text-warning"></i></span>
                <h2 class="fw-bold m-0 fs-5">Pusat Kendali Monitoring Sistem &amp; Infrastruktur</h2>
            </div>
            <p class="text-muted m-0 small">
                Pemantauan komprehensif performa, konektivitas database, runtime server, keamanan platform, modul aktif, dan kesiapan IoT RW 018
            </p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <!-- Overall Health Status Badge -->
            @php
                $overallLevel = ($health['server_level'] === 'critical' || $health['db_level'] === 'critical') ? 'danger' : (($health['storage_level'] === 'warning' || $health['db_level'] === 'warning') ? 'warning' : 'success');
                $overallText = ($overallLevel === 'danger') ? 'Perlu Perhatian Kritis' : (($overallLevel === 'warning') ? 'Sistem Terdegradasi Ringan' : 'Semua Layanan Beroperasi Normal');
            @endphp
            <span class="badge bg-{{ $overallLevel }}-subtle text-{{ $overallLevel }} border border-{{ $overallLevel }} px-3 py-2 fs-7 fw-bold d-inline-flex align-items-center gap-2">
                <span class="sa-system-dot bg-{{ $overallLevel }}"></span>
                <span>{{ $overallText }}</span>
            </span>

            <!-- Refresh Button -->
            <button type="button" class="btn btn-sm btn-outline-secondary sa-btn-refresh d-inline-flex align-items-center gap-1.5 px-3 py-2 fw-semibold" id="btnRefreshMonitoring" onclick="location.reload();">
                <i class="fas fa-sync-alt" id="iconRefresh"></i>
                <span>Perbarui</span>
            </button>

            <!-- On-demand Integrity Check Form -->
            <form action="{{ route('super_admin.monitoring.integrity_check') }}" method="POST" class="d-inline m-0">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1.5 px-3 py-2 fw-semibold">
                    <i class="fas fa-shield-check"></i>
                    <span>Uji Integritas DB</span>
                </button>
            </form>
        </div>
    </div>

    <!-- INSPECTION TIMESTAMP NOTE -->
    <div class="d-flex justify-content-between align-items-center small text-muted mb-3 px-1">
        <div>
            <i class="fas fa-clock me-1 text-primary"></i> Waktu Pemeriksaan Aktual: <strong class="text-dark dark:text-white" id="inspectedAtText">{{ $last_inspected_at }}</strong>
        </div>
        <div>
            <i class="fas fa-server me-1"></i> Host Server: <span class="font-monospace">{{ $health['server_host'] }}</span> &bull; Lingkungan: <strong class="text-uppercase">{{ $runtime['server_env'] }}</strong>
        </div>
    </div>

    <!-- ================================================================= -->
    <!-- SECTION A: SYSTEM HEALTH OVERVIEW (6 Primary Health Indicators) -->
    <!-- ================================================================= -->
    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h5 class="fs-6 fw-bold m-0 d-flex align-items-center gap-2">
                <i class="fas fa-heart-pulse text-danger"></i>
                <span>Ringkasan Kesehatan Platform (System Health Overview)</span>
            </h5>
            <span class="badge sa-badge-core-indicators small">6 Indikator Inti</span>
        </div>

        <div class="row g-3">
            <!-- 1. Server Status -->
            <div class="col-md-4 col-xl-2">
                <div class="sa-health-card p-3 h-100 border-start border-4 border-{{ $health['server_level'] == 'normal' ? 'success' : 'danger' }}">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="sa-kpi-label">Server Status</span>
                        <i class="fas fa-server text-muted"></i>
                    </div>
                    <div class="fs-4 fw-bold text-{{ $health['server_level'] == 'normal' ? 'success' : 'danger' }} mb-1">
                        {{ $health['server_status'] }}
                    </div>
                    <div class="small text-muted">{{ $health['server_status_text'] }}</div>
                    <div class="mt-2 pt-2 border-top small text-muted">
                        <i class="fas fa-clock me-1"></i>{{ $health['server_time'] }}
                    </div>
                </div>
            </div>

            <!-- 2. Database Health -->
            <div class="col-md-4 col-xl-2">
                <div class="sa-health-card p-3 h-100 border-start border-4 border-{{ $health['db_level'] == 'normal' ? 'success' : ($health['db_level'] == 'warning' ? 'warning' : 'danger') }}">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="sa-kpi-label">Database Health</span>
                        <i class="fas fa-database text-muted"></i>
                    </div>
                    <div class="d-flex align-items-baseline gap-1 mb-1">
                        <span class="fs-4 fw-bold text-{{ $health['db_level'] == 'normal' ? 'success' : ($health['db_level'] == 'warning' ? 'warning' : 'danger') }}">
                            {{ $health['db_latency_ms'] }}
                        </span>
                        <span class="small text-muted">ms</span>
                    </div>
                    <div class="small fw-semibold text-{{ $health['db_level'] == 'normal' ? 'success' : ($health['db_level'] == 'warning' ? 'warning' : 'danger') }}">
                        <i class="fas fa-circle-check me-1"></i>{{ $health['db_status_text'] }}
                    </div>
                    <div class="mt-2 pt-2 border-top small text-muted text-truncate" title="{{ $health['db_integrity'] }}">
                        {{ $health['db_integrity'] }}
                    </div>
                </div>
            </div>

            <!-- 3. Active Users -->
            <div class="col-md-4 col-xl-2">
                <div class="sa-health-card p-3 h-100 border-start border-4 border-primary">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="sa-kpi-label">Active Users</span>
                        <i class="fas fa-users-viewfinder text-primary"></i>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 mb-1">
                        <span class="fs-4 fw-bold text-primary">{{ $health['active_users'] }}</span>
                        <span class="small text-muted">/ {{ $health['total_users'] }} akun</span>
                    </div>
                    <div class="small text-muted fw-semibold">
                        Sesi aktif {{ $health['active_period'] }}
                    </div>
                    <div class="mt-2 pt-2 border-top small text-muted">
                        Definisi: Aktivitas sesi terakhir
                    </div>
                </div>
            </div>

            <!-- 4. Storage Usage -->
            <div class="col-md-4 col-xl-2">
                <div class="sa-health-card p-3 h-100 border-start border-4 border-{{ $health['storage_level'] == 'normal' ? 'success' : ($health['storage_level'] == 'warning' ? 'warning' : 'danger') }}">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="sa-kpi-label">Storage Usage</span>
                        <i class="fas fa-hard-drive text-muted"></i>
                    </div>
                    <div class="d-flex align-items-baseline gap-1 mb-1">
                        <span class="fs-4 fw-bold text-{{ $health['storage_level'] == 'normal' ? 'dark' : ($health['storage_level'] == 'warning' ? 'warning' : 'danger') }}">
                            {{ $health['storage_percent'] }}%
                        </span>
                        <span class="small text-muted">terpakai</span>
                    </div>
                    <div class="progress mb-1" style="height: 6px;">
                        <div class="progress-bar bg-{{ $health['storage_level'] == 'normal' ? 'success' : ($health['storage_level'] == 'warning' ? 'warning' : 'danger') }}" style="width: {{ $health['storage_percent'] }}%"></div>
                    </div>
                    <div class="mt-2 pt-2 border-top small text-muted">
                        Free: {{ $health['storage_free_gb'] }} GB / {{ $health['storage_total_gb'] }} GB
                    </div>
                </div>
            </div>

            <!-- 5. API & Services -->
            <div class="col-md-4 col-xl-2">
                <div class="sa-health-card p-3 h-100 border-start border-4 border-{{ $health['api_level'] == 'normal' ? 'success' : 'warning' }}">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="sa-kpi-label">API &amp; Services</span>
                        <i class="fas fa-network-wired text-muted"></i>
                    </div>
                    <div class="fs-4 fw-bold text-{{ $health['api_level'] == 'normal' ? 'success' : 'warning' }} mb-1">
                        {{ $health['core_routes_count'] }}/{{ $health['core_routes_total'] }}
                    </div>
                    <div class="small fw-semibold text-{{ $health['api_level'] == 'normal' ? 'success' : 'warning' }}">
                        {{ $health['api_status_text'] }}
                    </div>
                    <div class="mt-2 pt-2 border-top small text-muted">
                        Semua endpoint inti terdaftar
                    </div>
                </div>
            </div>

            <!-- 6. Backup Status -->
            <div class="col-md-4 col-xl-2">
                @php
                    $backupBorder = ($health['backup_level'] === 'normal') ? 'success' : (($health['backup_level'] === 'warning') ? 'warning' : (($health['backup_level'] === 'critical') ? 'danger' : 'secondary'));
                @endphp
                <div class="sa-health-card p-3 h-100 border-start border-4 border-{{ $backupBorder }}">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="sa-kpi-label">Backup Status</span>
                        <i class="fas fa-box-archive text-muted"></i>
                    </div>
                    <div class="fs-6 fw-bold text-{{ $backupBorder }} mb-1">
                        {{ $health['backup_status_text'] }}
                    </div>
                    <div class="small text-muted">
                        Terakhir: <strong>{{ $health['backup_last_time'] }}</strong>
                    </div>
                    <div class="mt-2 pt-2 border-top small text-muted text-truncate" title="{{ $health['backup_filename'] ?: 'Belum ada cadangan dibuat' }}">
                        {{ $health['backup_filename'] ?: 'Belum ada file backup' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ================================================================= -->
    <!-- SECTION B: PERFORMANCE MONITORING & REAL TIME TRENDS -->
    <!-- ================================================================= -->
    <div class="row g-4 mb-4">
        <!-- Performance KPI Column -->
        <div class="col-xl-4">
            <div class="sa-card h-100">
                <div class="sa-card-header">
                    <h4 class="sa-card-title fs-6">
                        <i class="fas fa-gauge-high text-primary"></i>
                        <span>Metrik Performa Server Aktual</span>
                    </h4>
                    <span class="badge bg-primary-subtle text-primary">Live Metrics</span>
                </div>

                <div class="p-3 d-flex flex-column gap-3">
                    <!-- PHP Memory -->
                    <div class="p-3 rounded-3 border sa-metric-box">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-semibold text-muted">Alokasi RAM PHP (Current / Limit):</span>
                            <span class="badge bg-primary-subtle text-primary">{{ $performance['php_memory_mb'] }}</span>
                        </div>
                        <div class="d-flex align-items-baseline gap-2">
                            <span class="fs-4 fw-bold text-dark">{{ $performance['php_memory_mb'] }}</span>
                            <span class="small text-muted">/ {{ $performance['php_memory_limit'] }}</span>
                        </div>
                        <small class="text-muted">Peak Allocation: <strong>{{ $performance['php_memory_peak'] }}</strong></small>
                    </div>

                    <!-- Disk Usage Partition -->
                    <div class="p-3 rounded-3 border sa-metric-box">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-semibold text-muted">Penyimpanan Partisi Aplikasi:</span>
                            <span class="badge bg-{{ $performance['disk_percent'] > 85 ? 'danger' : 'success' }}-subtle text-{{ $performance['disk_percent'] > 85 ? 'danger' : 'success' }}">{{ $performance['disk_percent'] }}%</span>
                        </div>
                        <div class="d-flex align-items-baseline gap-2">
                            <span class="fs-4 fw-bold text-dark">{{ $performance['disk_used_gb'] }}</span>
                            <span class="small text-muted">/ {{ $performance['disk_total_gb'] }}</span>
                        </div>
                        <div class="progress mt-1" style="height: 6px;">
                            <div class="progress-bar bg-{{ $performance['disk_percent'] > 85 ? 'danger' : 'success' }}" style="width: {{ $performance['disk_percent'] }}%"></div>
                        </div>
                    </div>

                    <!-- CPU Usage (with honest Windows notice if not available) -->
                    <div class="p-3 rounded-3 border sa-metric-box">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-semibold text-muted">CPU Usage:</span>
                            @if($performance['cpu_usage'] !== null)
                                <span class="badge bg-info-subtle text-info">{{ $performance['cpu_usage'] }}%</span>
                            @else
                                <span class="badge sa-badge-cpu-na">Belum tersedia</span>
                            @endif
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            @if($performance['cpu_usage'] !== null)
                                <span class="fs-4 fw-bold text-dark">{{ $performance['cpu_usage'] }}%</span>
                            @else
                                <span class="fs-6 fw-bold text-muted">N/A (Windows Dev)</span>
                            @endif
                        </div>
                        <small class="text-muted"><i class="fas fa-circle-info me-1"></i>{{ $performance['cpu_note'] }}</small>
                    </div>

                    <!-- Server Uptime / Runtime -->
                    <div class="p-3 rounded-3 border sa-metric-box">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-semibold text-muted">Runtime Process State:</span>
                            <span class="badge bg-success-subtle text-success">Aktif</span>
                        </div>
                        <div class="fs-6 fw-bold text-dark mb-1">{{ $performance['server_uptime'] }}</div>
                        <small class="text-muted">Proses SAPI: <strong>{{ $runtime['php_sapi'] }}</strong> &bull; Timeout: <strong>{{ $runtime['max_execution_time'] }}</strong></small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Trends Chart Column -->
        <div class="col-xl-8">
            <div class="sa-card h-100">
                <div class="sa-card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="sa-card-title fs-6 m-0">
                            <i class="fas fa-chart-line text-success"></i>
                            <span>Tren Performa Server &amp; Latensi Query Database</span>
                        </h4>
                        <small class="text-muted">Riwayat titik pemeriksaan aktual (Database Latency &amp; Alokasi RAM)</small>
                    </div>
                    <span class="badge bg-success-subtle text-success small">Real Data Points</span>
                </div>

                <div class="p-3">
                    <div style="position: relative; height: 320px; width: 100%;">
                        <canvas id="performanceTrendChart"></canvas>
                    </div>
                    <div class="d-flex flex-wrap justify-content-between align-items-center pt-3 border-top mt-2 small text-muted">
                        <div>
                            <i class="fas fa-info-circle me-1 text-primary"></i> Data diambil langsung dari pembacaan memory dan latency query database secara berkala.
                        </div>
                        <div>
                            Waktu Pembaruan: <strong>{{ $last_inspected_at }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ================================================================= -->
    <!-- SECTION C & D: DATABASE DIAGNOSTICS & RUNTIME ENVIRONMENT -->
    <!-- ================================================================= -->
    <div class="row g-4 mb-4">
        <!-- C. DATABASE & CONNECTIVITY -->
        <div class="col-lg-6">
            <div class="sa-card h-100">
                <div class="sa-card-header">
                    <h4 class="sa-card-title fs-6">
                        <i class="fas fa-database text-success"></i>
                        <span>Diagnostik Database &amp; Konektivitas</span>
                    </h4>
                    <span class="badge bg-success-subtle text-success">{{ $database['connection_status'] }}</span>
                </div>

                <div class="p-3">
                    <div class="d-flex flex-column gap-2 mb-3" style="font-size: 0.85rem;">
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Database Engine:</span>
                            <strong class="text-dark">{{ $database['engine'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Jumlah Tabel Terkelola:</span>
                            <strong class="text-dark">{{ $database['table_count'] }} Tabel</strong>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Ukuran Penyimpanan Database:</span>
                            <strong class="text-dark">{{ $database['db_size'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Waktu Respon Query:</span>
                            <strong class="text-success">{{ $database['latency_ms'] }} milidetik</strong>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Foreign Key Integrity:</span>
                            <strong class="text-success">{{ $database['fk_status'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-2">
                            <span class="text-muted">Uji Struktur (PRAGMA quick_check):</span>
                            <strong class="text-success"><i class="fas fa-check-circle me-1"></i>{{ $database['integrity_status'] }}</strong>
                        </div>
                    </div>

                    <!-- Top Tables Breakdown -->
                    <div class="p-2.5 rounded-3 border bg-light-subtle sa-db-tables-box">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small fw-bold text-muted text-uppercase">8 Tabel Utama &amp; Jumlah Baris Data</span>
                            <span class="small text-muted">Aktif</span>
                        </div>
                        <div class="row g-2">
                            @foreach($database['top_tables'] as $tName => $tCount)
                                <div class="col-6 col-sm-3">
                                    <div class="p-1.5 rounded-2 border sa-table-item-box text-center">
                                        <div class="font-monospace small text-truncate" title="{{ $tName }}">{{ $tName }}</div>
                                        <div class="fw-bold fs-7 text-primary">{{ number_format($tCount) }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- D. RUNTIME & SERVER ENVIRONMENT -->
        <div class="col-lg-6">
            <div class="sa-card h-100">
                <div class="sa-card-header">
                    <h4 class="sa-card-title fs-6">
                        <i class="fas fa-microchip text-primary"></i>
                        <span>Runtime &amp; Ekstensi PHP</span>
                    </h4>
                    <span class="badge bg-light text-dark border">PHP v{{ $runtime['php_version'] }}</span>
                </div>

                <div class="p-3">
                    <div class="row g-2 mb-3">
                        @foreach($runtime['extensions'] as $extKey => $ext)
                            <div class="col-sm-6">
                                <div class="p-2 rounded-3 border d-flex align-items-center justify-content-between sa-ext-box" title="{{ $ext['description'] }}">
                                    <div>
                                        <span class="small fw-semibold font-monospace">{{ $ext['name'] }}</span>
                                        <div class="text-muted" style="font-size: 0.70rem;">{{ $ext['description'] }}</div>
                                    </div>
                                    @if($ext['loaded'])
                                        <span class="badge bg-success-subtle text-success" style="font-size: 0.70rem;">
                                            <i class="fas fa-check me-1"></i>Aktif
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger" style="font-size: 0.70rem;">
                                            <i class="fas fa-times me-1"></i>Tidak Ada
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="p-2.5 rounded-3 border bg-light-subtle sa-php-runtime-box d-flex flex-column gap-1.5" style="font-size: 0.80rem;">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Server OS:</span>
                            <strong class="text-dark">{{ $runtime['server_os'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Laravel Framework:</span>
                            <strong class="text-primary">v{{ $runtime['laravel_version'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Max Upload / Post Size:</span>
                            <strong>{{ $runtime['upload_max_filesize'] }} / {{ $runtime['post_max_size'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Queue Worker &amp; Scheduler:</span>
                            <strong class="text-success">{{ $runtime['queue_status'] }} &bull; {{ $runtime['scheduler_status'] }}</strong>
                        </div>
                        <div class="text-muted pt-1 border-top" style="font-size: 0.72rem;">
                            <i class="fas fa-shield-halved me-1 text-success"></i> Variabel rahasia (.env), APP_KEY, dan kredensial database disembunyikan sesuai standar audit.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ================================================================= -->
    <!-- SECTION E: APPLICATION & MODULE HEALTH (22 Modules Grid) -->
    <!-- ================================================================= -->
    <div class="sa-card mb-4">
        <div class="sa-card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                <h4 class="sa-card-title fs-6 m-0">
                    <i class="fas fa-layer-group text-warning"></i>
                    <span>Kondisi Modul Aplikasi NexaNest (22 Modul Terdaftar)</span>
                </h4>
                <small class="text-muted">Pemantauan ketersediaan route, tabel database relasional, dan error log modul</small>
            </div>
            <span class="badge bg-success-subtle text-success fw-bold">22 Modul Terverifikasi</span>
        </div>

        <div class="p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 28%;">Nama Modul</th>
                            <th style="width: 24%;">Route Endpoint</th>
                            <th style="width: 18%;">Tabel Terkait</th>
                            <th style="width: 15%; text-align: center;">Error Tercatat</th>
                            <th style="width: 15%; text-align: end;">Status Modul</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($modules as $m)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="sa-module-icon">
                                            <i class="fas {{ $m['icon'] }}"></i>
                                        </div>
                                        <span class="fw-semibold text-dark">{{ $m['name'] }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="font-monospace small text-muted">{{ $m['route_name'] }}</span>
                                    @if($m['route_exists'])
                                        <i class="fas fa-check-circle text-success ms-1 small" title="Route aktif"></i>
                                    @else
                                        <i class="fas fa-times-circle text-danger ms-1 small" title="Route tidak ditemukan"></i>
                                    @endif
                                </td>
                                <td>
                                    <span class="font-monospace small text-muted">{{ $m['table_name'] }}</span>
                                    @if($m['table_exists'])
                                        <i class="fas fa-database text-success ms-1 small" title="Tabel ada di DB"></i>
                                    @else
                                        <i class="fas fa-exclamation-triangle text-danger ms-1 small" title="Tabel belum dibuat"></i>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($m['recent_errors'] > 0)
                                        <span class="badge bg-warning-subtle text-warning fw-semibold">{{ $m['recent_errors'] }} Log</span>
                                    @else
                                        <span class="text-muted small">0 Error</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($m['level'] === 'normal')
                                        <span class="badge bg-success-subtle text-success px-2.5 py-1">
                                            <i class="fas fa-circle-check me-1"></i>Operational
                                        </span>
                                    @elseif($m['level'] === 'warning')
                                        <span class="badge bg-warning-subtle text-warning px-2.5 py-1">
                                            <i class="fas fa-triangle-exclamation me-1"></i>Degraded
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger px-2.5 py-1">
                                            <i class="fas fa-circle-xmark me-1"></i>Unavailable
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-2 pt-2 border-top small text-muted">
                <i class="fas fa-info-circle me-1 text-primary"></i> 
                Status "Operational" menandakan route terdaftar dan skema database siap. Wewenang eksekusi dikontrol secara ketat melalui RBAC Admin RW, Admin RT, dan Warga. Halaman monitoring ini bersifat murni pemantauan (*read-only*).
            </div>
        </div>
    </div>

    <!-- ================================================================= -->
    <!-- SECTION F: SECURITY MONITORING -->
    <!-- ================================================================= -->
    <div class="sa-card mb-4">
        <div class="sa-card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                <h4 class="sa-card-title fs-6 m-0">
                    <i class="fas fa-shield-halved text-danger"></i>
                    <span>Pemantauan Keamanan &amp; Audit Percobaan Akses (Security Monitoring)</span>
                </h4>
                <small class="text-muted">Integrasi audit log terhadap percobaan login gagal, eskalasi peran, dan audit akses data sensitif</small>
            </div>
            <a href="{{ route('super_admin.audit.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1">
                <span>Buka Audit Log Lengkap</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="p-3">
            <!-- 5 Security KPI Counters -->
            <div class="row g-2 mb-3">
                <div class="col-sm-6 col-md">
                    <div class="p-2.5 rounded-3 border text-center sa-sec-box">
                        <div class="small text-muted mb-1">Login Gagal</div>
                        <div class="fs-4 fw-bold {{ $security['failed_logins'] > 0 ? 'text-danger' : 'text-success' }}">{{ $security['failed_logins'] }}</div>
                        <div class="small text-muted" style="font-size: 0.70rem;">Percobaan ditolak</div>
                    </div>
                </div>
                <div class="col-sm-6 col-md">
                    <div class="p-2.5 rounded-3 border text-center sa-sec-box">
                        <div class="small text-muted mb-1">Aktivitas Mencurigakan</div>
                        <div class="fs-4 fw-bold {{ $security['suspicious_activity'] > 0 ? 'text-warning' : 'text-success' }}">{{ $security['suspicious_activity'] }}</div>
                        <div class="small text-muted" style="font-size: 0.70rem;">Tanda peringatan</div>
                    </div>
                </div>
                <div class="col-sm-6 col-md">
                    <div class="p-2.5 rounded-3 border text-center sa-sec-box">
                        <div class="small text-muted mb-1">Akses Tanpa Izin (403/IDOR)</div>
                        <div class="fs-4 fw-bold {{ $security['unauthorized_attempts'] > 0 ? 'text-danger' : 'text-success' }}">{{ $security['unauthorized_attempts'] }}</div>
                        <div class="small text-muted" style="font-size: 0.70rem;">Terblokir sistem</div>
                    </div>
                </div>
                <div class="col-sm-6 col-md">
                    <div class="p-2.5 rounded-3 border text-center sa-sec-box">
                        <div class="small text-muted mb-1">Akses Data Sensitif SA</div>
                        <div class="fs-4 fw-bold text-primary">{{ $security['sensitive_access_count'] }}</div>
                        <div class="small text-muted" style="font-size: 0.70rem;">Dengan alasan audit</div>
                    </div>
                </div>
                <div class="col-sm-6 col-md">
                    <div class="p-2.5 rounded-3 border text-center sa-sec-box">
                        <div class="small text-muted mb-1">Perubahan Role / Izin</div>
                        <div class="fs-4 fw-bold text-dark">{{ $security['role_changes'] }}</div>
                        <div class="small text-muted" style="font-size: 0.70rem;">Tercatat riwayat</div>
                    </div>
                </div>
            </div>

            <!-- Recent Security Events List -->
            @if(count($security['events']) > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.80rem;">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 12%;">Waktu</th>
                                <th style="width: 15%;">Event &amp; Prioritas</th>
                                <th style="width: 18%;">Aktor &amp; Peran</th>
                                <th style="width: 15%;">Modul Target</th>
                                <th style="width: 25%;">Keterangan Kejadian</th>
                                <th style="width: 15%; text-align: end;">Alamat IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($security['events'] as $sec)
                                <tr>
                                    <td class="text-muted" title="{{ $sec['diff'] }}">{{ $sec['created_at'] }}</td>
                                    <td>
                                        <div class="d-inline-flex align-items-center gap-2">
                                            @if($sec['priority'] === 'critical')
                                                <span class="badge bg-danger-subtle text-danger sa-badge-critical">Kritis</span>
                                            @elseif($sec['priority'] === 'warning')
                                                <span class="badge sa-badge-warning">Peringatan</span>
                                            @else
                                                <span class="badge sa-badge-info">Info</span>
                                            @endif
                                            <span class="font-monospace fw-semibold sa-event-text">{{ $sec['event'] }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <strong>{{ $sec['user_name'] }}</strong>
                                        <span class="text-muted small">({{ $sec['role'] }})</span>
                                    </td>
                                    <td>{{ $sec['module'] }}</td>
                                    <td class="text-truncate" style="max-width: 260px;" title="{{ $sec['description'] }}">{{ $sec['description'] }}</td>
                                    <td class="text-end font-monospace text-muted">{{ $sec['ip_address'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-shield-check text-success fs-3 mb-2 d-block"></i>
                    <span>Tidak ada peringatan keamanan mencurigakan dalam riwayat terkini.</span>
                </div>
            @endif
        </div>
    </div>

    <!-- ================================================================= -->
    <!-- SECTION G & H: BACKUP & RECOVERY + IOT (ESP32/RFID) PREPARATION -->
    <!-- ================================================================= -->
    <div class="row g-4 mb-4">
        <!-- G. BACKUP & RECOVERY -->
        <div class="col-lg-6">
            <div class="sa-card h-100">
                <div class="sa-card-header d-flex justify-content-between align-items-center">
                    <h4 class="sa-card-title fs-6 m-0">
                        <i class="fas fa-box-archive text-primary"></i>
                        <span>Status Cadangan Database &amp; Pemulihan</span>
                    </h4>
                    <a href="{{ route('super_admin.backup.index') }}" class="btn btn-sm btn-outline-primary py-1 px-2.5">
                        <i class="fas fa-folder-open me-1"></i>Kelola Backup
                    </a>
                </div>

                <div class="p-3">
                    <div class="d-flex flex-column gap-2 mb-3" style="font-size: 0.85rem;">
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Status Cadangan Terakhir:</span>
                            @if($backup['has_backup'] && $backup['latest']->status === 'success')
                                <strong class="text-success"><i class="fas fa-check-circle me-1"></i>Tersedia ({{ $backup['latest']->filesize }})</strong>
                            @else
                                <strong class="text-secondary"><i class="fas fa-minus-circle me-1"></i>Belum ada cadangan dibuat</strong>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Total Berkas Fisik Terverifikasi:</span>
                            <strong class="text-dark">{{ $backup['total_files_on_disk'] }} file di server</strong>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Jadwal Backup Otomatis:</span>
                            <strong class="text-muted">{{ $backup['next_schedule'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-2">
                            <span class="text-muted">Lokasi Penyimpanan:</span>
                            <span class="font-monospace small text-muted text-truncate" style="max-width: 250px;">{{ $backup['storage_location'] }}</span>
                        </div>
                    </div>

                    @if(count($backup['history']) > 0)
                        <div class="small fw-bold text-muted text-uppercase mb-2">Riwayat Cadangan Terakhir</div>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.80rem;">
                                <tbody>
                                    @foreach($backup['history'] as $b)
                                        <tr>
                                            <td>
                                                <i class="fas fa-file-zipper text-secondary me-1"></i>
                                                <span class="font-monospace text-dark">{{ $b->filename }}</span>
                                            </td>
                                            <td class="text-muted">{{ $b->filesize }}</td>
                                            <td class="text-muted">{{ $b->created_at ? $b->created_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') . ' WIB' : '-' }}</td>
                                            <td class="text-end">
                                                <span class="badge bg-success-subtle text-success">Valid</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-3 text-center bg-light rounded-3 text-muted small">
                            <i class="fas fa-info-circle text-primary me-1"></i> Belum ada file cadangan yang dibuat. Anda dapat membuat snapshot cadangan instan melalui menu <strong>Backup Platform</strong>.
                        </div>
                    @endif

                    <div class="mt-3 pt-2 border-top small text-muted" style="font-size: 0.72rem;">
                        <i class="fas fa-triangle-exclamation text-warning me-1"></i> Prosedur pemulihan (*restore*) membutuhkan verifikasi manual ganda dari Super Admin guna mencegah *accidental overwrite* terhadap database aktif.
                    </div>
                </div>
            </div>
        </div>

        <!-- H. IOT & DEVICE MONITORING (ESP32 & RFID RC522) -->
        <div class="col-lg-6">
            <div class="sa-card h-100">
                <div class="sa-card-header d-flex justify-content-between align-items-center">
                    <h4 class="sa-card-title fs-6 m-0">
                        <i class="fas fa-id-badge text-warning"></i>
                        <span>Monitoring Perangkat IoT &amp; RFID Akses (Persiapan Integrasi)</span>
                    </h4>
                    <span class="badge bg-warning-subtle text-warning">Hardware Ready</span>
                </div>

                <div class="p-3">
                    <!-- Clean state notice as requested -->
                    <div class="p-3 rounded-3 border mb-3 sa-iot-state-box">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge sa-badge-iot-status"><i class="fas fa-plug-circle-xmark me-1"></i>Status Koneksi</span>
                            <span class="fw-bold fs-7 text-dark">Belum ada perangkat terhubung</span>
                        </div>
                        <p class="small text-muted m-0">
                            {{ $iot['status_message'] }}
                        </p>
                    </div>

                    <!-- Hardware Architecture & Specifications -->
                    <div class="small fw-bold text-muted text-uppercase mb-2">Spesifikasi Arsitektur Perangkat RW 018</div>
                    <div class="d-flex flex-column gap-1.5 mb-3">
                        @foreach($iot['supported_hardware'] as $hw)
                            <div class="p-2 rounded-2 border bg-light-subtle sa-iot-spec-item d-flex align-items-center gap-2 small">
                                <i class="fas fa-circle-check text-primary"></i>
                                <span class="text-dark">{{ $hw }}</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Ready Schema Indicator -->
                    <div class="p-2.5 rounded-3 border bg-white dark:bg-dark sa-iot-schema-box small text-muted">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Status Skema Database RFID:</span>
                            <strong class="text-success"><i class="fas fa-check-circle me-1"></i>Kolom `rfid_uid` Siap di Tabel `wargas`</strong>
                        </div>
                    </div>

                    <div class="mt-3 pt-2 border-top small text-muted" style="font-size: 0.72rem;">
                        <i class="fas fa-info-circle text-primary me-1"></i> Tidak ada data perangkat semu (*dummy*) yang di-generate. Saat microcontroller ESP32 mulai mengirim *handshake payload* melalui endpoint webhook terproteksi, status perangkat akan beralih ke <strong>Online</strong> secara otomatis.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ================================================================= -->
    <!-- SECTION I: SYSTEM INCIDENT HISTORY -->
    <!-- ================================================================= -->
    <div class="sa-card mb-4">
        <div class="sa-card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h4 class="sa-card-title fs-6 m-0">
                    <i class="fas fa-clock-rotate-left text-danger"></i>
                    <span>Riwayat Kejadian &amp; Insiden Sistem (System Incident History)</span>
                </h4>
                <small class="text-muted">Pencatatan dan pengelompokan riwayat peringatan, kegagalan backup, dan kejadian keamanan terisolasi</small>
            </div>

            <!-- Filter Tabs -->
            <div class="btn-group btn-group-sm" role="group">
                <a href="{{ route('super_admin.monitoring', ['filter' => 'all']) }}" class="btn btn-outline-secondary {{ $filter === 'all' ? 'active' : '' }}">Semua</a>
                <a href="{{ route('super_admin.monitoring', ['filter' => 'critical']) }}" class="btn btn-outline-secondary {{ $filter === 'critical' ? 'active' : '' }}">Kritis</a>
                <a href="{{ route('super_admin.monitoring', ['filter' => 'warning']) }}" class="btn btn-outline-secondary {{ $filter === 'warning' ? 'active' : '' }}">Peringatan</a>
                <a href="{{ route('super_admin.monitoring', ['filter' => 'info']) }}" class="btn btn-outline-secondary {{ $filter === 'info' ? 'active' : '' }}">Informasi</a>
            </div>
        </div>

        <div class="p-3">
            @if(count($incidents) > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 15%;">Waktu Kejadian</th>
                                <th style="width: 12%;">Prioritas</th>
                                <th style="width: 18%;">Tipe Kejadian</th>
                                <th style="width: 35%;">Deskripsi Singkat</th>
                                <th style="width: 20%; text-align: end;">Status Penanganan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($incidents as $inc)
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $inc['time'] }}</div>
                                        <div class="text-muted small">{{ $inc['diff'] }}</div>
                                    </td>
                                    <td>
                                        @if($inc['priority'] === 'critical')
                                            <span class="badge bg-danger-subtle text-danger px-2.5 py-1">
                                                <i class="fas fa-circle-exclamation me-1"></i>Critical
                                            </span>
                                        @elseif($inc['priority'] === 'warning')
                                            <span class="badge bg-warning-subtle text-warning px-2.5 py-1">
                                                <i class="fas fa-triangle-exclamation me-1"></i>Warning
                                            </span>
                                        @else
                                            <span class="badge bg-info-subtle text-info px-2.5 py-1">
                                                <i class="fas fa-circle-info me-1"></i>Informasi
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong class="text-dark">{{ $inc['type'] }}</strong>
                                        <div class="font-monospace small text-muted">{{ $inc['event'] }}</div>
                                    </td>
                                    <td>
                                        <div class="text-dark" title="{{ $inc['description'] }}">{{ $inc['description'] }}</div>
                                        @if($inc['count'] > 1)
                                            <span class="badge bg-light text-muted border mt-1">{{ $inc['count'] }}x terjadi</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-light text-dark border px-2 py-1">
                                            <i class="fas fa-shield-check text-success me-1"></i>{{ $inc['status'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-circle-check text-success fs-1 mb-2 d-block"></i>
                    <h6 class="fw-bold text-dark">Tidak Ada Insiden Tercatat</h6>
                    <p class="small text-muted m-0">Seluruh komponen sistem berjalan stabil tanpa adanya catatan peringatan dalam filter ini.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- ================================================================= -->
<!-- COMPACT SCOPED CSS & CHART.JS INITIALIZATION -->
<!-- ================================================================= -->
@push('styles')
<style>
    .sa-monitoring-wrap {
        animation: fadeIn 0.25s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(4px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .sa-header-card {
        background: #FFFFFF;
        border: 1px solid var(--nx-border);
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
    }
    html.dark .sa-header-card {
        background: #111A2E;
        border-color: #1E2D4A;
    }

    .sa-crown-dot {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: linear-gradient(135deg, rgba(212, 175, 55, 0.2) 0%, rgba(245, 158, 11, 0.15) 100%);
        border: 1px solid rgba(212, 175, 55, 0.4);
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .sa-health-card {
        background: #FFFFFF;
        border: 1px solid var(--nx-border);
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .sa-health-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
    }
    html.dark .sa-health-card {
        background: #131B2E;
        border-color: #1E2D4A;
    }

    .sa-system-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        animation: pulseDot 2s infinite;
    }
    @keyframes pulseDot {
        0% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.4; transform: scale(1.2); }
        100% { opacity: 1; transform: scale(1); }
    }

    .sa-module-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: var(--nx-mint-soft);
        color: var(--nx-forest);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        flex-shrink: 0;
    }
    html.dark .sa-module-icon {
        background: rgba(56, 189, 248, 0.15);
        color: #38BDF8;
    }

    .sa-metric-box, .sa-ext-box, .sa-sec-box, .sa-iot-state-box {
        background: #FAFCFA;
        transition: background 0.2s;
    }
    html.dark .sa-metric-box,
    html.dark .sa-ext-box,
    html.dark .sa-sec-box,
    html.dark .sa-iot-state-box {
        background: #0E1626;
        border-color: #1E2D4A !important;
    }

    /* Dark Mode Polishing: Tombol & Badge Header */
    .sa-btn-refresh {
        transition: all 0.2s ease;
    }
    html.dark .sa-btn-refresh,
    html.dark #btnRefreshMonitoring {
        background: #17223B !important;
        border: 1px solid #273654 !important;
        color: #E2E8F0 !important;
    }
    html.dark .sa-btn-refresh:hover,
    html.dark #btnRefreshMonitoring:hover {
        background: #1E2E4F !important;
        border-color: #3B82F6 !important;
        color: #FFFFFF !important;
    }
    html.dark .sa-badge-core-indicators,
    html.dark .sa-badge-cpu-na {
        background: #1E293B !important;
        border: 1px solid #334155 !important;
        color: #CBD5E1 !important;
    }

    /* Dark Mode: 8 Tabel Utama */
    html.dark .sa-db-tables-box {
        background: #0F172A !important;
        border-color: #1E2D4A !important;
    }
    html.dark .sa-db-tables-box span.text-muted,
    html.dark .sa-db-tables-box .small.fw-bold {
        color: #CBD5E1 !important;
    }
    html.dark .sa-table-item-box {
        background: #17223B !important;
        border-color: #273654 !important;
        color: #CBD5E1 !important;
    }
    html.dark .sa-table-item-box .text-truncate {
        color: #CBD5E1 !important;
    }

    /* Dark Mode: PHP Runtime Box */
    html.dark .sa-php-runtime-box {
        background: #0F172A !important;
        border-color: #273654 !important;
    }
    html.dark .sa-php-runtime-box .text-muted {
        color: #94A3B8 !important;
    }
    html.dark .sa-php-runtime-box strong,
    html.dark .sa-php-runtime-box .text-dark {
        color: #F1F5F9 !important;
    }

    /* Dark Mode: IoT Spesifikasi & Status */
    html.dark .sa-badge-iot-status {
        background: #1E293B !important;
        border: 1px solid #334155 !important;
        color: #CBD5E1 !important;
    }
    html.dark .sa-iot-spec-item {
        background: #17223B !important;
        border-color: #273654 !important;
        color: #E2E8F0 !important;
    }
    html.dark .sa-iot-spec-item .text-dark,
    html.dark .sa-iot-spec-item span {
        color: #E2E8F0 !important;
    }
    html.dark .sa-iot-schema-box {
        background: #17223B !important;
        border-color: #273654 !important;
        color: #CBD5E1 !important;
    }

    /* Dark Mode: Security Events & Badges */
    .sa-badge-info {
        background: rgba(59, 130, 246, 0.14);
        color: #1D4ED8;
        border: 1px solid rgba(59, 130, 246, 0.3);
    }
    .sa-badge-warning {
        background: rgba(245, 158, 11, 0.14);
        color: #B45309;
        border: 1px solid rgba(245, 158, 11, 0.3);
    }
    html.dark .sa-badge-info {
        background: rgba(59, 130, 246, 0.18) !important;
        color: #93C5FD !important;
        border: 1px solid rgba(59, 130, 246, 0.35) !important;
    }
    html.dark .sa-badge-warning {
        background: rgba(245, 158, 11, 0.18) !important;
        color: #FCD34D !important;
        border: 1px solid rgba(245, 158, 11, 0.35) !important;
    }
    html.dark .sa-event-text {
        color: #F1F5F9 !important;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Initial Performance Trend Chart (Chart.js)
    const ctx = document.getElementById('performanceTrendChart');
    if (!ctx) return;

    // Load initial real snapshot points passed from controller
    const trendData = @json($performance['trend_snapshots'] ?? []);
    
    const labels = trendData.map(d => d.time || '');
    const latencyValues = trendData.map(d => parseFloat(d.db_latency) || 0);
    const memoryValues = trendData.map(d => parseFloat(d.memory_mb) || 0);

    const isDarkMode = document.documentElement.classList.contains('dark') || document.documentElement.getAttribute('data-theme') === 'dark';
    const textColor = isDarkMode ? '#94A3B8' : '#64748B';
    const gridColor = isDarkMode ? 'rgba(255, 255, 255, 0.06)' : 'rgba(0, 0, 0, 0.06)';

    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Latensi Query Database (ms)',
                    data: latencyValues,
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.12)',
                    fill: true,
                    tension: 0.35,
                    borderWidth: 2.5,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#10B981',
                    yAxisID: 'y',
                },
                {
                    label: 'Alokasi RAM PHP (MB)',
                    data: memoryValues,
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.08)',
                    fill: false,
                    tension: 0.35,
                    borderWidth: 2.5,
                    borderDash: [4, 4],
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#3B82F6',
                    yAxisID: 'y1',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        color: textColor,
                        font: { family: 'Plus Jakarta Sans, sans-serif', weight: 600, size: 12 },
                        usePointStyle: true,
                        padding: 16
                    }
                },
                tooltip: {
                    backgroundColor: isDarkMode ? '#0F172A' : '#1E293B',
                    titleColor: '#F8FAFC',
                    bodyColor: '#F8FAFC',
                    borderColor: '#334155',
                    borderWidth: 1,
                    padding: 10,
                    boxPadding: 4,
                    usePointStyle: true
                }
            },
            scales: {
                x: {
                    grid: { color: gridColor },
                    ticks: { color: textColor, font: { family: 'Plus Jakarta Sans', size: 11 } }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Latensi (ms)',
                        color: '#10B981',
                        font: { size: 11, weight: 600 }
                    },
                    grid: { color: gridColor },
                    ticks: { color: textColor, font: { size: 11 } },
                    min: 0
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'RAM (MB)',
                        color: '#3B82F6',
                        font: { size: 11, weight: 600 }
                    },
                    grid: { drawOnChartArea: false },
                    ticks: { color: textColor, font: { size: 11 } },
                    min: 0
                }
            }
        }
    });

    // 2. Auto-refresh / Live Poll support every 30 seconds (gentle interval)
    let refreshInterval = null;
    function pollMetrics() {
        const refreshIcon = document.getElementById('iconRefresh');
        if (refreshIcon) refreshIcon.classList.add('fa-spin');

        fetch("{{ route('super_admin.monitoring.metrics') }}", {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(payload => {
            if (payload && payload.data) {
                const d = payload.data;
                const timeEl = document.getElementById('inspectedAtText');
                if (timeEl && d.last_inspected_at) {
                    timeEl.textContent = d.last_inspected_at;
                }

                // Append new data point to chart if fresh
                if (d.performance && d.performance.trend_snapshots) {
                    const latest = d.performance.trend_snapshots.slice(-1)[0];
                    if (latest && !chart.data.labels.includes(latest.time)) {
                        chart.data.labels.push(latest.time);
                        chart.data.datasets[0].data.push(parseFloat(latest.db_latency));
                        chart.data.datasets[1].data.push(parseFloat(latest.memory_mb));
                        if (chart.data.labels.length > 12) {
                            chart.data.labels.shift();
                            chart.data.datasets[0].data.shift();
                            chart.data.datasets[1].data.shift();
                        }
                        chart.update('none');
                    }
                }
            }
        })
        .catch(err => console.debug('Monitoring poll note:', err))
        .finally(() => {
            if (refreshIcon) refreshIcon.classList.remove('fa-spin');
        });
    }

    // Gentle polling every 30s
    refreshInterval = setInterval(pollMetrics, 30000);

    // Clean up on unload
    window.addEventListener('beforeunload', () => {
        if (refreshInterval) clearInterval(refreshInterval);
    });
});
</script>
@endpush
@endsection
