@php
    $useSuperAdminLayout = auth()->check() && auth()->user()->isSuperAdmin() && !auth()->user()->isRoleSwitched();
@endphp
@extends($useSuperAdminLayout ? 'layouts.super_admin' : 'layouts.app')

@section('title', 'Pengaturan API & Smart Gate IoT — NexaNest')
@section('page_title', 'Pengaturan API & Smart Gate')
@section('page_subtitle', 'Pusat integrasi mikrokontroler ESP32, pembaca RFID RC522, dan gerbang otomatis RW 018')

@section('content')
<div class="container-fluid py-2 px-0">

    <!-- Flash Notification Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4 rounded-3 shadow-sm" role="alert">
            <i class="fas fa-circle-check fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4 rounded-3 shadow-sm" role="alert">
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="fas fa-triangle-exclamation fs-5"></i>
                <strong>Terdapat beberapa kesalahan pengisian:</strong>
            </div>
            <ul class="mb-0 ps-3 small">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Header Banner & Ringkasan Metrik -->
    <div class="card p-4 rounded-4 border mb-4 shadow-sm" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0 align-self-start" 
                     style="width: 48px; height: 48px; min-width: 48px; min-height: 48px; background: linear-gradient(135deg, #1E4633 0%, #285943 100%); box-shadow: 0 4px 12px rgba(40,89,67,0.25);">
                    <i class="fas fa-microchip fa-xl"></i>
                </div>
                <div style="min-width: 0;">
                    <h4 class="fw-bold m-0" style="color: var(--nx-text);">Pusat Kendali IoT &amp; Smart Gate</h4>
                    <p class="text-muted small m-0">Pengaturan token autentikasi ESP32 + RFID RC522, koordinat geofencing, dan log tap kartu.</p>
                </div>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <span class="badge {{ $setting->is_active ? 'bg-success' : 'bg-danger' }} px-3 py-2 rounded-pill fs-7 fw-semibold shadow-sm text-wrap text-start">
                    <i class="fas {{ $setting->is_active ? 'fa-signal' : 'fa-power-off' }} me-1"></i>
                    API IoT: {{ $setting->is_active ? 'AKTIF' : 'NONAKTIF' }}
                </span>
                <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill fs-7 fw-semibold border text-wrap text-start">
                    <i class="fas fa-shield-halved me-1"></i> Mode Keamanan: Terproteksi Token
                </span>
            </div>
        </div>

        <!-- 4 KPI Metrics -->
        <div class="row g-3">
            <div class="col-6 col-md-3">
                <div class="p-3 rounded-3 border h-100" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                    <span class="text-muted small d-block">Tap Hari Ini</span>
                    <h3 class="fw-bold m-0 mt-1" style="color: var(--nx-text);">{{ number_format($stats['total_today']) }}</h3>
                    <small class="text-muted" style="font-size: 0.72rem;">Total {{ number_format($stats['total_all']) }} sepanjang masa</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 rounded-3 border h-100" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                    <span class="text-muted small d-block">Kartu Warga Terdaftar</span>
                    <h3 class="fw-bold text-success m-0 mt-1">{{ number_format($stats['registered_today']) }}</h3>
                    <small class="text-success" style="font-size: 0.72rem;"><i class="fas fa-check-circle me-1"></i>Identitas dikenali</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 rounded-3 border h-100" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                    <span class="text-muted small d-block">Kartu Belum Terdaftar</span>
                    <h3 class="fw-bold text-warning m-0 mt-1" style="color: #D97706 !important;">{{ number_format($stats['unregistered_today']) }}</h3>
                    <small class="text-muted" style="font-size: 0.72rem;"><i class="fas fa-question-circle me-1"></i>Siap direkam ke warga</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 rounded-3 border h-100" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                    <span class="text-muted small d-block">Ditolak / Invalid Token</span>
                    <h3 class="fw-bold text-danger m-0 mt-1">{{ number_format($stats['rejected_today']) }}</h3>
                    <small class="text-danger" style="font-size: 0.72rem;"><i class="fas fa-ban me-1"></i>Akses tidak sah dicegah</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Konten Utama: 2 Kolom (Kiri: Panel Form Pengaturan, Kanan: Tabel Log RFID) -->
    <div class="row g-4">
        
        <!-- KOLOM KIRI: PANEL KONFIGURASI API & SMART GATE -->
        <div class="col-xl-5 col-lg-6">
            <div class="card p-4 rounded-4 border h-100 shadow-sm" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
                <div class="border-bottom pb-3 mb-4 d-flex align-items-center justify-content-between" style="border-color: var(--nx-border) !important;">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" 
                             style="width: 34px; height: 34px; background: rgba(40, 89, 67, 0.12); color: var(--nx-forest);">
                            <i class="fas fa-sliders"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold m-0 fs-6" style="color: var(--nx-text);">Konfigurasi API &amp; Gerbang</h5>
                            <small class="text-muted">Parameter keamanan dan geofencing Smart Gate</small>
                        </div>
                    </div>
                </div>

                <form action="{{ route('iot.settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- SAKLAR AKTIF/NONAKTIF -->
                    <div class="p-3 rounded-3 border mb-4 d-flex align-items-center justify-content-between" 
                         style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                        <div>
                            <strong class="d-block" style="color: var(--nx-text); font-size: 0.9rem;">Saklar Penerimaan API IoT</strong>
                            <small class="text-muted" style="font-size: 0.75rem;">
                                Saat nonaktif, seluruh kiriman scan ESP32 akan ditolak otomatis (HTTP 503).
                            </small>
                        </div>
                        <div class="form-check form-switch m-0 fs-4">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" role="switch" name="is_active" value="1" 
                                   id="toggleIsActive" {{ $setting->is_active ? 'checked' : '' }} style="cursor: pointer;">
                        </div>
                    </div>

                    <!-- API TOKEN -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold" style="color: var(--nx-text);">
                            <i class="fas fa-key me-1 text-warning"></i> API Secret Token (ESP32 Autentikasi)
                        </label>
                        <div class="input-group">
                            <input type="text" name="api_token" id="inputApiToken" class="form-control font-monospace fw-bold" 
                                   value="{{ old('api_token', $setting->api_token) }}" required minlength="16" maxlength="64"
                                   style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border); letter-spacing: 1px;">
                            <button type="button" class="btn btn-outline-secondary" id="btnCopyToken" title="Salin Token">
                                <i class="fas fa-copy"></i>
                            </button>
                            <button type="button" class="btn btn-outline-primary" id="btnGenerateToken" title="Generate Token Baru">
                                <i class="fas fa-arrows-rotate"></i>
                            </button>
                        </div>
                        <small class="text-muted" style="font-size: 0.72rem;">
                            Token ini wajib disematkan pada firmware ESP32 (lewat body JSON, query, atau header).
                        </small>
                    </div>

                    <!-- PANDUAN INTEGRASI ENDPOINT & JSON PAYLOAD -->
                    <div class="p-3 rounded-3 border mb-4" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                        <span class="d-block small fw-bold mb-2" style="color: var(--nx-text);">
                            <i class="fas fa-network-wired me-1 text-primary"></i> Endpoint Target ESP32
                        </span>
                        <div class="input-group input-group-sm mb-2">
                            <span class="input-group-text font-monospace small bg-light text-muted">POST/GET</span>
                            <input type="text" class="form-control font-monospace small" readonly value="{{ url('/api/rfid/scan') }}" id="endpointUrl"
                                   style="background: var(--nx-card-bg); color: var(--nx-text); border-color: var(--nx-border);">
                            <button class="btn btn-outline-secondary" type="button" id="btnCopyEndpoint">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>

                        <span class="d-block small fw-bold mb-1" style="color: var(--nx-text); font-size: 0.75rem;">
                            Contoh JSON Body Request:
                        </span>
                        <pre class="m-0 p-2.5 rounded-2 font-monospace" 
                             style="background: #0F172A; color: #38BDF8; font-size: 0.72rem; line-height: 1.45; overflow-x: auto;"><code>{
  "uid": "A1B2C3D4",
  "token": "{{ substr($setting->api_token, 0, 10) }}...",
  "device_id": "ESP32-GATE-01",
  "context": "smart_gate"
}</code></pre>
                    </div>

                    <!-- PENGATURAN LOKASI GERBANG & GEOFENCING -->
                    <div class="border-top pt-3 mb-3" style="border-color: var(--nx-border) !important;">
                        <h6 class="fw-bold mb-2" style="color: var(--nx-text); font-size: 0.85rem;">
                            <i class="fas fa-location-dot me-1 text-danger"></i> Lokasi Smart Gate &amp; Geofencing (Tahap 2 Ready)
                        </h6>
                        <p class="text-muted small mb-3" style="font-size: 0.75rem;">
                            Koordinat gerbang fisik RW 018 untuk validasi jarak darurat dan pembukaan gerbang via GPS warga.
                        </p>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold" style="color: var(--nx-text);">Nama / Identitas Gerbang</label>
                            <input type="text" name="gate_name" class="form-control form-control-sm" value="{{ old('gate_name', $setting->gate_name) }}" required
                                   style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                        </div>

                        @php
                            $rawRadius = old('geofence_radius', $setting->geofence_radius ?? 15);
                            // Clamp radius antara min: 5 meter dan max: 20 meter, default 15 meter
                            $currentRadius = max(5, min(20, (int) ($rawRadius ?: 15)));
                        @endphp
                        <div class="row g-2 mb-2">
                            <div class="col-12 col-sm-6">
                                <label class="form-label small fw-semibold" style="color: var(--nx-text);">Latitude Gerbang</label>
                                <input type="number" step="any" name="gate_latitude" id="inputLatitude" class="form-control form-control-sm font-monospace"
                                       value="{{ old('gate_latitude', $setting->gate_latitude) }}" placeholder="-6.388889"
                                       style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label small fw-semibold" style="color: var(--nx-text);">Longitude Gerbang</label>
                                <input type="number" step="any" name="gate_longitude" id="inputLongitude" class="form-control form-control-sm font-monospace"
                                       value="{{ old('gate_longitude', $setting->gate_longitude) }}" placeholder="106.845556"
                                       style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mb-3">
                            <button type="button" class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1.5" id="btnCurrentLocation" style="font-size: 0.75rem;">
                                <i class="fas fa-location-crosshairs"></i>
                                <span>Gunakan Lokasi Saya Saat Ini</span>
                            </button>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="form-label small fw-semibold m-0" style="color: var(--nx-text);">Radius Geofence (Meter)</label>
                                <span class="badge bg-primary-subtle text-primary fw-bold font-monospace" id="radiusValueBadge">
                                    {{ $currentRadius }} m
                                </span>
                            </div>
                            <input type="range" class="form-range" name="geofence_radius" id="rangeGeofence" min="5" max="20" step="1" 
                                   value="{{ $currentRadius }}">
                            <div class="d-flex justify-content-between small text-muted font-monospace mt-1" style="font-size: 0.7rem;">
                                <span>5 m (Min)</span>
                                <span>15 m (Default)</span>
                                <span>20 m (Maks)</span>
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">
                                Radius geofencing aman gerbang otomatis: minimal 5 meter s/d maksimal 20 meter (rekomendasi: 15 meter).
                            </small>
                        </div>
                    </div>

                    <!-- TOMBOL SIMPAN -->
                    <div class="d-flex justify-content-end pt-3 border-top" style="border-color: var(--nx-border) !important;">
                        <button type="submit" class="btn btn-primary px-4 py-2 fw-bold d-inline-flex align-items-center gap-2 shadow-sm" style="border-radius: 8px;">
                            <i class="fas fa-floppy-disk"></i>
                            <span>Simpan Konfigurasi IoT</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- KOLOM KANAN: TABEL LOG SCAN RFID TERPUSAT -->
        <div class="col-xl-7 col-lg-6">
            <div class="card p-4 rounded-4 border h-100 shadow-sm" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
                
                <!-- Table Header with Filters & Controls -->
                <div class="border-bottom pb-3 mb-3 d-flex flex-wrap align-items-center justify-content-between gap-3" style="border-color: var(--nx-border) !important;">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" 
                             style="width: 34px; height: 34px; background: rgba(59, 130, 246, 0.12); color: #2563EB;">
                            <i class="fas fa-list-check"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold m-0 fs-6" style="color: var(--nx-text);">Log Riwayat Scan RFID Terpusat</h5>
                            <small class="text-muted">Pencatatan real-time tap kartu E-KTP dari seluruh perangkat</small>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1.5" id="btnManualRefresh" title="Segarkan Data">
                            <i class="fas fa-rotate"></i>
                            <span class="d-none d-sm-inline">Refresh</span>
                        </button>
                        
                        <div class="form-check form-switch m-0 d-inline-flex align-items-center gap-1" title="Perbarui log otomatis tiap 10 detik">
                            <input class="form-check-input mt-0" type="checkbox" id="toggleAutoRefresh" style="cursor: pointer;">
                            <label class="form-check-label text-muted small" for="toggleAutoRefresh" style="font-size: 0.72rem;">Auto</label>
                        </div>

                        @if(auth()->user()->isSuperAdmin())
                        <form action="{{ route('iot.logs.clear') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengosongkan seluruh riwayat log RFID?');" class="m-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger px-2" title="Bersihkan Log">
                                <i class="fas fa-trash-can"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>

                <!-- Status Filter Pills & Search -->
                <form action="{{ route('iot.index') }}" method="GET" class="row g-2 mb-3">
                    <div class="col-sm-7">
                        <div class="d-flex flex-wrap gap-1">
                            <a href="{{ route('iot.index') }}" 
                               class="btn btn-sm {{ empty($statusFilter) ? 'btn-primary' : 'btn-outline-secondary' }} px-2.5 py-1" style="font-size: 0.75rem; border-radius: 6px;">
                                Semua
                            </a>
                            <a href="{{ route('iot.index', ['status' => 'registered', 'q' => $search]) }}" 
                               class="btn btn-sm {{ $statusFilter === 'registered' ? 'btn-success text-white' : 'btn-outline-success' }} px-2.5 py-1" style="font-size: 0.75rem; border-radius: 6px;">
                                Terdaftar
                            </a>
                            <a href="{{ route('iot.index', ['status' => 'unregistered', 'q' => $search]) }}" 
                               class="btn btn-sm {{ $statusFilter === 'unregistered' ? 'btn-warning text-dark' : 'btn-outline-warning' }} px-2.5 py-1" style="font-size: 0.75rem; border-radius: 6px;">
                                Belum Terdaftar
                            </a>
                            <a href="{{ route('iot.index', ['status' => 'rejected_token', 'q' => $search]) }}" 
                               class="btn btn-sm {{ $statusFilter === 'rejected_token' ? 'btn-danger text-white' : 'btn-outline-danger' }} px-2.5 py-1" style="font-size: 0.75rem; border-radius: 6px;">
                                Ditolak Token
                            </a>
                            <a href="{{ route('iot.index', ['status' => 'rejected_geofence', 'q' => $search]) }}" 
                               class="btn btn-sm {{ $statusFilter === 'rejected_geofence' ? 'btn-danger text-white' : 'btn-outline-danger' }} px-2.5 py-1" style="font-size: 0.75rem; border-radius: 6px;">
                                Radius Ditolak
                            </a>
                        </div>
                    </div>

                    <div class="col-sm-5">
                        <div class="input-group input-group-sm">
                            <input type="text" name="q" class="form-control" placeholder="Cari UID, Warga, IP..." value="{{ $search }}"
                                   style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                            @if($search || $statusFilter)
                                <a href="{{ route('iot.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                                    <i class="fas fa-xmark"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                <!-- TABEL DATA -->
                <div class="table-responsive flex-grow-1" style="min-height: 380px;">
                    <table class="table align-middle m-0" style="color: var(--nx-text); font-size: 0.78rem;">
                        <thead style="background: var(--nx-bg); border-bottom: 1px solid var(--nx-border);">
                            <tr>
                                <th class="py-2.5 px-3 small fw-bold" style="color: var(--nx-muted);">WAKTU</th>
                                <th class="py-2.5 px-3 small fw-bold" style="color: var(--nx-muted);">UID KARTU</th>
                                <th class="py-2.5 px-3 small fw-bold" style="color: var(--nx-muted);">IDENTITAS WARGA</th>
                                <th class="py-2.5 px-3 small fw-bold" style="color: var(--nx-muted);">KONTEKS</th>
                                <th class="py-2.5 px-3 small fw-bold" style="color: var(--nx-muted);">STATUS</th>
                                <th class="py-2.5 px-3 small fw-bold text-end" style="color: var(--nx-muted);">IP / PERANGKAT</th>
                            </tr>
                        </thead>
                        <tbody id="rfidLogsTbody">
                            @forelse($logs as $log)
                                <tr style="border-bottom: 1px solid var(--nx-border);">
                                    <td class="py-2.5 px-3">
                                        <div class="font-monospace fw-bold" style="color: var(--nx-text);">
                                            {{ $log->created_at ? $log->created_at->timezone('Asia/Jakarta')->translatedFormat('H:i:s') : '-' }} WIB
                                        </div>
                                        <small class="text-muted d-block" style="font-size: 0.68rem;">
                                            {{ $log->created_at ? $log->created_at->diffForHumans() : '' }}
                                        </small>
                                    </td>

                                    <td class="py-2.5 px-3 font-monospace">
                                        <span class="badge font-monospace px-2 py-1" 
                                              style="background: var(--nx-bg); color: var(--nx-text); border: 1px solid var(--nx-border); letter-spacing: 1px;">
                                            <i class="fas fa-fingerprint me-1 text-primary"></i>{{ $log->uid }}
                                        </span>
                                    </td>

                                    <td class="py-2.5 px-3">
                                        @if($log->warga)
                                            <div class="fw-bold" style="color: var(--nx-text);">{{ $log->warga->nama }}</div>
                                            <small class="text-muted" style="font-size: 0.68rem;">RT {{ $log->warga->rt_number ?? '-' }} · {{ $log->warga->blok_rumah ?? '-' }}</small>
                                        @else
                                            <span class="text-muted fst-italic">Belum Terhubung</span>
                                        @endif
                                    </td>

                                    <td class="py-2.5 px-3">
                                        <span class="badge {{ $log->context_badge_class }} font-monospace" style="font-size: 0.68rem;">
                                            @if($log->context === 'smart_gate' || $log->context === 'general')
                                                <i class="fas fa-door-open me-1"></i>
                                            @elseif($log->context === 'rekam_ktp')
                                                <i class="fas fa-id-card me-1"></i>
                                            @elseif($log->context === 'iuran')
                                                <i class="fas fa-wallet me-1"></i>
                                            @endif
                                            {{ $log->context_label }}
                                        </span>
                                        <small class="text-muted d-block" style="font-size: 0.66rem;">
                                            {{ $log->method }}
                                        </small>
                                    </td>

                                    <td class="py-2.5 px-3">
                                        <span class="badge {{ $log->status_badge_class }} font-monospace" style="font-size: 0.68rem;">
                                            {{ $log->status_label }}
                                        </span>
                                        @if($log->message)
                                            <small class="text-muted d-block text-truncate" style="max-width: 140px; font-size: 0.66rem;" title="{{ $log->message }}">
                                                {{ $log->message }}
                                            </small>
                                        @endif
                                    </td>

                                    <td class="py-2.5 px-3 text-end font-monospace">
                                        <span class="text-muted" style="font-size: 0.72rem;">{{ $log->device_ip ?: '127.0.0.1' }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fas fa-microchip fa-2x mb-2 d-block opacity-25"></i>
                                        <span>Belum ada riwayat tap kartu RFID yang tercatat.</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($logs->hasPages())
                    <div class="pt-3 border-top d-flex justify-content-between align-items-center" style="border-color: var(--nx-border) !important;">
                        <small class="text-muted">Menampilkan {{ $logs->firstItem() }} - {{ $logs->lastItem() }} dari {{ $logs->total() }} log</small>
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Helper Copy with Fallback for HTTP / non-secure contexts
    function copyTextFallback(text, btnElement, successMessage = 'Tersalin!') {
        function showFeedback() {
            const originalHtml = btnElement.innerHTML;
            btnElement.innerHTML = `<i class="fas fa-check text-success me-1"></i><span class="small fw-bold text-success">${successMessage}</span>`;
            setTimeout(() => { btnElement.innerHTML = originalHtml; }, 1800);
        }

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(showFeedback).catch(() => fallback(text));
        } else {
            fallback(text);
        }

        function fallback(t) {
            const textArea = document.createElement("textarea");
            textArea.value = t;
            textArea.style.position = "fixed";
            textArea.style.left = "-999999px";
            textArea.style.top = "-999999px";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                document.execCommand('copy');
                showFeedback();
            } catch (err) {
                alert('Gagal menyalin teks. Silakan salin secara manual.');
            }
            document.body.removeChild(textArea);
        }
    }

    // 1. Copy Token API
    const btnCopyToken = document.getElementById('btnCopyToken');
    const inputToken = document.getElementById('inputApiToken');
    if (btnCopyToken && inputToken) {
        btnCopyToken.addEventListener('click', function () {
            copyTextFallback(inputToken.value, btnCopyToken, 'Tersalin!');
        });
    }

    // 2. Copy Endpoint URL
    const btnCopyEndpoint = document.getElementById('btnCopyEndpoint');
    const endpointInput = document.getElementById('endpointUrl');
    if (btnCopyEndpoint && endpointInput) {
        btnCopyEndpoint.addEventListener('click', function () {
            copyTextFallback(endpointInput.value, btnCopyEndpoint, 'Tersalin!');
        });
    }

    // 3. Generate New Token via AJAX
    const btnGenToken = document.getElementById('btnGenerateToken');
    if (btnGenToken && inputToken) {
        btnGenToken.addEventListener('click', function () {
            if (!confirm('Hasilkan token baru? Token lama pada perangkat ESP32 harus diperbarui agar tetap dapat terhubung.')) {
                return;
            }
            btnGenToken.disabled = true;
            fetch(`{{ route('iot.token.generate') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(r => r.json())
            .then(res => {
                if (res.success && res.token) {
                    inputToken.value = res.token;
                    alert('Token baru berhasil dibuat: ' + res.token + '\nJangan lupa tekan tombol "Simpan Konfigurasi IoT"!');
                }
            })
            .catch(() => alert('Gagal menghasilkan token baru.'))
            .finally(() => { btnGenToken.disabled = false; });
        });
    }

    // 4. Geolocation: Gunakan Lokasi Saya Saat Ini
    const btnCurrentLocation = document.getElementById('btnCurrentLocation');
    const inputLat = document.getElementById('inputLatitude');
    const inputLng = document.getElementById('inputLongitude');
    if (btnCurrentLocation && inputLat && inputLng) {
        btnCurrentLocation.addEventListener('click', function () {
            if (!navigator.geolocation) {
                alert('Browser Anda tidak mendukung fitur Geolocation.');
                return;
            }

            btnCurrentLocation.disabled = true;
            btnCurrentLocation.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mendeteksi Lokasi GPS...';

            navigator.geolocation.getCurrentPosition(
                function (position) {
                    inputLat.value = position.coords.latitude.toFixed(7);
                    inputLng.value = position.coords.longitude.toFixed(7);
                    btnCurrentLocation.disabled = false;
                    btnCurrentLocation.innerHTML = '<i class="fas fa-check text-success"></i> Koordinat Terisi';
                    setTimeout(() => {
                        btnCurrentLocation.innerHTML = '<i class="fas fa-location-crosshairs"></i> <span>Gunakan Lokasi Saya Saat Ini</span>';
                    }, 2000);
                },
                function (err) {
                    btnCurrentLocation.disabled = false;
                    btnCurrentLocation.innerHTML = '<i class="fas fa-location-crosshairs"></i> <span>Gunakan Lokasi Saya Saat Ini</span>';
                    alert('Gagal mendeteksi lokasi: ' + err.message + '\nPastikan izin GPS / akses lokasi browser telah diizinkan.');
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        });
    }

    // 5. Radius Slider Indicator
    const rangeGeofence = document.getElementById('rangeGeofence');
    const radiusBadge = document.getElementById('radiusValueBadge');
    if (rangeGeofence && radiusBadge) {
        rangeGeofence.addEventListener('input', function () {
            radiusBadge.textContent = this.value + ' m';
        });
    }

    // 6. Manual & Auto Refresh Polling Log
    const btnManualRefresh = document.getElementById('btnManualRefresh');
    const toggleAutoRefresh = document.getElementById('toggleAutoRefresh');
    let autoRefreshTimer = null;

    function refreshLogs() {
        if (btnManualRefresh) {
            btnManualRefresh.innerHTML = '<i class="fas fa-rotate fa-spin"></i>';
        }

        const currentStatus = `{{ $statusFilter }}`;
        fetch(`{{ route('iot.logs') }}?status=${encodeURIComponent(currentStatus)}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(res => {
            if (res.success && Array.isArray(res.data)) {
                renderLogsTbody(res.data);
            }
        })
        .catch(() => {})
        .finally(() => {
            if (btnManualRefresh) {
                btnManualRefresh.innerHTML = '<i class="fas fa-rotate"></i> <span class="d-none d-sm-inline">Refresh</span>';
            }
        });
    }

    function renderLogsTbody(items) {
        const tbody = document.getElementById('rfidLogsTbody');
        if (!tbody) return;

        if (items.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="fas fa-microchip fa-2x mb-2 d-block opacity-25"></i>
                        <span>Belum ada riwayat tap kartu RFID yang tercatat.</span>
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        items.forEach(log => {
            html += `
                <tr style="border-bottom: 1px solid var(--nx-border);">
                    <td class="py-2.5 px-3">
                        <div class="font-monospace fw-bold" style="color: var(--nx-text);">
                            ${log.created_at} WIB
                        </div>
                        <small class="text-muted d-block" style="font-size: 0.68rem;">
                            ${log.diff_for_humans}
                        </small>
                    </td>

                    <td class="py-2.5 px-3 font-monospace">
                        <span class="badge font-monospace px-2 py-1" 
                              style="background: var(--nx-bg); color: var(--nx-text); border: 1px solid var(--nx-border); letter-spacing: 1px;">
                            <i class="fas fa-fingerprint me-1 text-primary"></i>${log.uid}
                        </span>
                    </td>

                    <td class="py-2.5 px-3">
                        ${log.warga_nama !== 'Belum Terdaftar' 
                            ? `<div class="fw-bold" style="color: var(--nx-text);">${log.warga_nama}</div>
                               <small class="text-muted" style="font-size: 0.68rem;">${log.warga_rt || '-'}</small>`
                            : `<span class="text-muted fst-italic">Belum Terhubung</span>`
                        }
                    </td>

                    <td class="py-2.5 px-3">
                        <span class="badge ${log.context_badge || 'bg-light text-dark border'} font-monospace" style="font-size: 0.68rem;">
                            ${(log.context === 'smart_gate' || log.context === 'general') ? '<i class="fas fa-door-open me-1"></i>' : (log.context === 'rekam_ktp' ? '<i class="fas fa-id-card me-1"></i>' : (log.context === 'iuran' ? '<i class="fas fa-wallet me-1"></i>' : ''))}
                            ${log.context_label || (log.context || 'GENERAL').toUpperCase()}
                        </span>
                        <small class="text-muted d-block" style="font-size: 0.66rem;">
                            ${log.method || 'rfid'}
                        </small>
                    </td>

                    <td class="py-2.5 px-3">
                        <span class="badge ${log.status_badge} font-monospace" style="font-size: 0.68rem;">
                            ${log.status_label}
                        </span>
                        ${log.message ? `<small class="text-muted d-block text-truncate" style="max-width: 140px; font-size: 0.66rem;" title="${log.message}">${log.message}</small>` : ''}
                    </td>

                    <td class="py-2.5 px-3 text-end font-monospace">
                        <span class="text-muted" style="font-size: 0.72rem;">${log.device_ip || '127.0.0.1'}</span>
                    </td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    }

    if (btnManualRefresh) {
        btnManualRefresh.addEventListener('click', refreshLogs);
    }

    if (toggleAutoRefresh) {
        toggleAutoRefresh.addEventListener('change', function () {
            if (this.checked) {
                refreshLogs();
                autoRefreshTimer = setInterval(refreshLogs, 10000);
            } else {
                clearInterval(autoRefreshTimer);
                autoRefreshTimer = null;
            }
        });
    }
});
</script>
@endpush
