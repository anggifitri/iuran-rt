@php
    $useSuperAdminLayout = auth()->check() && auth()->user()->isSuperAdmin() && !auth()->user()->isRoleSwitched();
@endphp
@extends($useSuperAdminLayout ? 'layouts.super_admin' : 'layouts.app')

@section('title', 'Smart Gate RW 018 — NexaNest')
@section('page_title', 'Smart Gate & Portal Otomatis')
@section('page_subtitle', 'Monitoring status palang gerbang real-time dan akses darurat geofencing RW 018')

@section('content')
<div class="container-fluid py-2 px-0">

    <!-- Header Status Gerbang -->
    <div class="card p-4 rounded-4 border mb-4 shadow-sm" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0 align-self-start" 
                     style="width: 48px; height: 48px; min-width: 48px; min-height: 48px; background: linear-gradient(135deg, #1E4633 0%, #285943 100%); box-shadow: 0 4px 12px rgba(40,89,67,0.25);">
                    <i class="fas fa-torii-gate fa-xl"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <h4 class="fw-bold m-0" style="color: var(--nx-text);">{{ $setting->gate_name }}</h4>
                        <span class="badge {{ $setting->is_active ? 'bg-success' : 'bg-danger' }} px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">
                            <i class="fas {{ $setting->is_active ? 'fa-signal' : 'fa-power-off' }} me-1"></i>
                            {{ $setting->is_active ? 'ONLINE' : 'OFFLINE' }}
                        </span>
                    </div>
                    <p class="text-muted small m-0 mt-1">
                        Gerbang otomatis akses warga RW 018 · Terhubung ke mikrokontroler ESP32 &amp; RFID RC522.
                    </p>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <div class="px-3 py-1.5 rounded-3 border d-flex align-items-center gap-2" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                    <span class="pulse-indicator" id="livePollingPulse"></span>
                    <small class="text-muted font-monospace" style="font-size: 0.75rem;">LIVE MONITORING</small>
                </div>

                @if(auth()->user()->isSuperAdmin() || auth()->user()->isAdminRw())
                    <a href="{{ route('iot.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1.5" style="border-radius: 8px;">
                        <i class="fas fa-sliders"></i>
                        <span>Pengaturan API &amp; IoT</span>
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- MAIN GRID: 2 KOLOM (KIRI: ANIMASI PALANG & STATUS, KANAN: BUKA DARURAT GPS & KONTROL) -->
    <div class="row g-4 mb-4">
        
        <!-- KOLOM 1: VISUALISASI ANIMASI PALANG GERBANG (SVG/CSS) -->
        <div class="col-xl-7 col-lg-7">
            <div class="card p-4 rounded-4 border h-100 shadow-sm" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
                
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2" style="border-color: var(--nx-border) !important;">
                    <span class="small fw-bold text-muted text-uppercase letter-spacing-1">
                        <i class="fas fa-video me-1 text-primary"></i> Visualisasi Palang Gerbang Interaktif
                    </span>
                    <span id="gateStateBadge" class="badge bg-secondary-subtle text-secondary border px-2.5 py-1 font-monospace">
                        STANDBY (TERTUTUP)
                    </span>
                </div>

                <!-- STAGE VISUALISASI GERBANG -->
                <div class="gate-stage-container rounded-4 position-relative p-4 mb-3 d-flex flex-column align-items-center justify-content-center overflow-hidden" 
                     id="gateStage">

                    <!-- Background Road & Sky Elements -->
                    <div class="gate-sky position-absolute top-0 start-0 w-100 h-100"></div>
                    <div class="gate-road position-absolute bottom-0 start-0 w-100">
                        <div class="road-markings"></div>
                    </div>

                    <!-- SVG & CSS GATE STRUCTURE -->
                    <div class="gate-assembly position-relative" style="width: 100%; max-width: 480px; height: 260px;">
                        
                        <!-- Housing Cabinet (Tiang Gerbang / Pedestal) -->
                        <div class="gate-cabinet position-absolute" id="gateCabinet">
                            <!-- LED Traffic Light Indicator -->
                            <div class="led-light-panel d-flex flex-column align-items-center gap-1 p-1">
                                <div class="led-dot led-red" id="ledRed"></div>
                                <div class="led-dot led-green" id="ledGreen"></div>
                            </div>
                            
                            <!-- Display Layar Digital Mini -->
                            <div class="cabinet-screen font-monospace text-center py-1 mt-1" id="cabinetScreen">
                                STANDBY
                            </div>

                            <!-- RFID Tap Reader Sensor Area -->
                            <div class="rfid-tap-zone text-center mt-2">
                                <i class="fas fa-wave-square text-white-50" style="font-size: 0.9rem;"></i>
                                <span class="d-block" style="font-size: 0.55rem; color: #CBD5E1;">TAP E-KTP</span>
                            </div>
                        </div>

                        <!-- Palang Gerbang (Boom Barrier Arm) -->
                        <div class="gate-barrier-pivot" id="gateBarrierPivot">
                            <div class="gate-barrier-arm" id="gateBarrierArm">
                                <div class="arm-tip-light"></div>
                            </div>
                        </div>

                        <!-- Mobil / Kendaraan Ilustrasi -->
                        <div class="car-illustration position-absolute" id="carIllustration">
                            <i class="fas fa-car-side fa-3x" style="color: var(--nx-forest); opacity: 0.85;"></i>
                        </div>
                    </div>
                </div>

                <!-- BANNER STATUS RESPON EVENT AKTIF -->
                <div id="gateEventAlert" class="alert alert-secondary d-flex flex-wrap align-items-center gap-2 gap-sm-3 p-3 rounded-3 mb-0 transition-all">
                    <div class="alert-icon-box rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" id="alertIconBox"
                         style="width: 42px; height: 42px; background: rgba(100, 116, 139, 0.15); color: #64748B;">
                        <i class="fas fa-shield-halved fa-lg"></i>
                    </div>
                    <div class="flex-grow-1" style="min-width: 0; word-break: break-word;">
                        <strong class="d-block mb-0.5" id="alertTitle" style="color: var(--nx-text);">Menunggu Aktivitas Tap Kartu atau GPS...</strong>
                        <p class="small text-muted mb-0" id="alertDesc" style="white-space: normal; line-height: 1.4;">
                            Tempelkan E-KTP pada RFID RC522 gerbang atau gunakan tombol buka darurat GPS warga.
                        </p>
                    </div>
                    <div id="countdownBox" class="text-end d-none flex-shrink-0 ms-auto">
                        <span class="badge bg-success-subtle text-success font-monospace px-2.5 py-1.5" id="countdownTimer">
                            Menutup: 6s
                        </span>
                    </div>
                </div>

            </div>
        </div>

        <!-- KOLOM 2: BUKA PORTAL DARURAT VIA GPS & INFO LOKASI -->
        <div class="col-xl-5 col-lg-5">
            <div class="card p-4 rounded-4 border h-100 shadow-sm d-flex flex-column justify-content-between" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
                
                <div>
                    <div class="border-bottom pb-3 mb-3 d-flex align-items-center gap-2" style="border-color: var(--nx-border) !important;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" 
                             style="width: 34px; height: 34px; background: rgba(239, 68, 68, 0.12); color: #EF4444;">
                            <i class="fas fa-location-crosshairs"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold m-0 fs-6" style="color: var(--nx-text);">Buka Portal Darurat (GPS)</h5>
                            <small class="text-muted">Akses darurat warga berbasis koordinat Geofencing</small>
                        </div>
                    </div>

                    <div class="p-3 rounded-3 border mb-3" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small">Titik Gerbang Terdaftar:</span>
                            <span class="badge bg-light text-dark border font-monospace" style="font-size: 0.72rem;">
                                {{ $setting->gate_latitude && $setting->gate_longitude ? number_format($setting->gate_latitude, 5) . ', ' . number_format($setting->gate_longitude, 5) : 'Belum Diatur' }}
                            </span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small">Radius Izin Geofence:</span>
                            <span class="badge bg-primary-subtle text-primary fw-bold font-monospace">
                                {{ $setting->geofence_radius }} Meter
                            </span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-muted small">Pengguna Aktif:</span>
                            <strong class="small" style="color: var(--nx-text);">
                                {{ $user->name }} ({{ $user->role_label }})
                            </strong>
                        </div>
                    </div>

                    <p class="small text-muted mb-3" style="font-size: 0.78rem; line-height: 1.45;">
                        <i class="fas fa-circle-info text-primary me-1"></i>
                        Tekan tombol di bawah saat Anda berada di depan gerbang untuk membuka palang secara otomatis tanpa fisik kartu. Browser akan memverifikasi jarak Anda ke gerbang menggunakan GPS perangkat.
                    </p>

                    <!-- Feedback Box Hasil Pengujian GPS -->
                    <div id="gpsFeedbackAlert" class="alert d-none p-3 rounded-3 mb-3 small"></div>
                </div>

                <!-- Tombol Buka Darurat GPS -->
                <div class="pt-3 border-top" style="border-color: var(--nx-border) !important;">
                    <button type="button" class="btn btn-danger w-100 py-3 fw-bold d-flex align-items-center justify-content-center gap-2 shadow" 
                            id="btnEmergencyGpsOpen" style="border-radius: 12px; font-size: 0.95rem; letter-spacing: 0.5px;">
                        <i class="fas fa-location-arrow"></i>
                        <span id="btnEmergencyGpsText">Buka Portal Darurat (GPS)</span>
                    </button>
                    <small class="text-muted d-block text-center mt-2" style="font-size: 0.7rem;">
                        Memerlukan izin akses lokasi browser &amp; akurasi GPS aktif.
                    </small>
                </div>

            </div>
        </div>

    </div>

    <!-- TABEL 10 AKTIVITAS PORTAL TERBARU -->
    <div class="card p-4 rounded-4 border shadow-sm" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
        <div class="border-bottom pb-3 mb-3 d-flex flex-wrap align-items-center justify-content-between gap-3" style="border-color: var(--nx-border) !important;">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center" 
                     style="width: 32px; height: 32px; background: rgba(40, 89, 67, 0.12); color: var(--nx-forest);">
                    <i class="fas fa-clock-rotate-left"></i>
                </div>
                <div>
                    <h5 class="fw-bold m-0 fs-6" style="color: var(--nx-text);">10 Aktivitas Portal Terbaru</h5>
                    <small class="text-muted">Pencatatan real-time pembukaan palang via E-KTP RFID &amp; Buka Darurat GPS</small>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-muted border font-monospace" style="font-size: 0.72rem;">
                    Auto-update 2s
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle m-0" style="color: var(--nx-text); font-size: 0.8rem;">
                <thead style="background: var(--nx-bg); border-bottom: 1px solid var(--nx-border);">
                    <tr>
                        <th class="py-2.5 px-3 small fw-bold" style="color: var(--nx-muted);">WAKTU</th>
                        <th class="py-2.5 px-3 small fw-bold" style="color: var(--nx-muted);">METODE</th>
                        <th class="py-2.5 px-3 small fw-bold" style="color: var(--nx-muted);">UID / IDENTIFIKATOR</th>
                        <th class="py-2.5 px-3 small fw-bold" style="color: var(--nx-muted);">NAMA WARGA</th>
                        <th class="py-2.5 px-3 small fw-bold" style="color: var(--nx-muted);">STATUS</th>
                        <th class="py-2.5 px-3 small fw-bold text-end" style="color: var(--nx-muted);">KETERANGAN</th>
                    </tr>
                </thead>
                <tbody id="recentActivitiesTbody">
                    @forelse($recentLogs as $log)
                        <tr style="border-bottom: 1px solid var(--nx-border);">
                            <td class="py-2.5 px-3">
                                <span class="font-monospace fw-bold" style="color: var(--nx-text);">
                                    {{ $log->created_at ? $log->created_at->timezone('Asia/Jakarta')->translatedFormat('H:i:s') : '-' }} WIB
                                </span>
                                <small class="text-muted d-block" style="font-size: 0.68rem;">
                                    {{ $log->created_at ? $log->created_at->diffForHumans() : '' }}
                                </small>
                            </td>

                            <td class="py-2.5 px-3">
                                @if($log->method === 'gps_emergency')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle font-monospace" style="font-size: 0.68rem;">
                                        <i class="fas fa-location-crosshairs me-1"></i>Darurat GPS
                                    </span>
                                @elseif($log->method === 'simulation')
                                    <span class="badge bg-info-subtle text-info border border-info-subtle font-monospace" style="font-size: 0.68rem;">
                                        <i class="fas fa-microchip me-1"></i>Simulasi
                                    </span>
                                @else
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace" style="font-size: 0.68rem;">
                                        <i class="fas fa-id-card me-1"></i>RFID RC522
                                    </span>
                                @endif
                            </td>

                            <td class="py-2.5 px-3 font-monospace">
                                <span class="badge px-2 py-1" style="background: var(--nx-bg); color: var(--nx-text); border: 1px solid var(--nx-border); letter-spacing: 0.5px;">
                                    {{ $log->uid }}
                                </span>
                            </td>

                            <td class="py-2.5 px-3">
                                @if($log->warga)
                                    <strong style="color: var(--nx-text);">{{ $log->warga->nama }}</strong>
                                    <small class="text-muted d-block" style="font-size: 0.68rem;">RT {{ $log->warga->rt_number ?? '-' }} · {{ $log->warga->blok_rumah ?? '-' }}</small>
                                @else
                                    <span class="text-muted fst-italic">Belum Terdaftar</span>
                                @endif
                            </td>

                            <td class="py-2.5 px-3">
                                <span class="badge {{ $log->status_badge_class }} font-monospace" style="font-size: 0.68rem;">
                                    {{ $log->status_label }}
                                </span>
                            </td>

                            <td class="py-2.5 px-3 text-end text-muted" style="font-size: 0.72rem;">
                                {{ $log->message ?: '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-torii-gate fa-2x mb-2 d-block opacity-25"></i>
                                <span>Belum ada catatan aktivitas portal.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
/* CSS STYLING & ANIMATION: SMART GATE STAGE */
.gate-stage-container {
    height: 310px;
    background: linear-gradient(180deg, #E2E8F0 0%, #CBD5E1 65%, #94A3B8 100%);
    border: 2px solid var(--nx-border);
    box-shadow: inset 0 2px 8px rgba(0,0,0,0.06);
}

html.dark .gate-stage-container {
    background: linear-gradient(180deg, #0B1120 0%, #111827 65%, #1E293B 100%) !important;
    border-color: #1E293B !important;
}

.pulse-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background-color: #10B981;
    display: inline-block;
    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    animation: pulseRing 1.8s infinite;
}

@keyframes pulseRing {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}

/* Road Styling */
.gate-road {
    height: 75px;
    background: #334155;
    border-top: 4px solid #F59E0B;
}

html.dark .gate-road {
    background: #0F172A;
    border-top: 4px solid #D97706;
}

.road-markings {
    position: absolute;
    top: 50%;
    left: 0;
    width: 100%;
    height: 4px;
    background: repeating-linear-gradient(90deg, #F8FAFC 0, #F8FAFC 24px, transparent 24px, transparent 48px);
    opacity: 0.5;
}

/* Cabinet Housing */
.gate-cabinet {
    left: 80px;
    bottom: 30px;
    width: 64px;
    height: 135px;
    background: linear-gradient(180deg, #F97316 0%, #EA580C 100%);
    border-radius: 8px 8px 3px 3px;
    border: 3px solid #C2410C;
    box-shadow: 0 10px 25px rgba(0,0,0,0.3);
    z-index: 10;
}

.led-light-panel {
    background: #1E293B;
    border-radius: 4px;
    margin: 4px;
    border: 1px solid #334155;
}

.led-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #475569;
    transition: all 0.3s ease;
}

.led-red.active {
    background: #EF4444 !important;
    box-shadow: 0 0 10px #EF4444, 0 0 20px rgba(239, 68, 68, 0.8);
}

.led-green.active {
    background: #10B981 !important;
    box-shadow: 0 0 10px #10B981, 0 0 20px rgba(16, 185, 129, 0.8);
}

.cabinet-screen {
    background: #020617;
    color: #38BDF8;
    font-size: 0.6rem;
    font-weight: bold;
    border: 1px solid #1E293B;
    margin: 0 4px;
    border-radius: 3px;
    letter-spacing: 0.5px;
}

/* Boom Barrier Arm Pivot & Animation */
.gate-barrier-pivot {
    position: absolute;
    left: 105px;
    bottom: 110px;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: #334155;
    border: 3px solid #64748B;
    z-index: 15;
    box-shadow: 0 4px 10px rgba(0,0,0,0.3);
}

.gate-barrier-arm {
    position: absolute;
    left: 17px;
    top: 11px;
    width: 250px;
    height: 12px;
    border-radius: 0 6px 6px 0;
    background: repeating-linear-gradient(45deg, #DC2626, #DC2626 15px, #FFFFFF 15px, #FFFFFF 30px);
    border: 1px solid rgba(0,0,0,0.25);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    transform-origin: 0px 6px;
    transform: rotate(0deg); /* 0deg = Tertutup */
    transition: transform 0.85s cubic-bezier(0.2, 0.8, 0.2, 1);
}

/* State: Palang Terbuka */
.gate-barrier-arm.open {
    transform: rotate(-85deg); /* -85deg = Terbuka */
}

/* State: Lampu Kedip Merah saat ditolak */
@keyframes blinkRed {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.2; }
}

.led-red.blink {
    animation: blinkRed 0.4s infinite;
}

/* Mobil */
.car-illustration {
    right: 25px;
    bottom: 35px;
    z-index: 5;
    transition: transform 1.2s ease-in-out;
}

.car-illustration.drive-through {
    transform: translateX(-160px);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Referensi Elemen DOM
    const gateArm = document.getElementById('gateBarrierArm');
    const ledRed = document.getElementById('ledRed');
    const ledGreen = document.getElementById('ledGreen');
    const cabinetScreen = document.getElementById('cabinetScreen');
    const gateStateBadge = document.getElementById('gateStateBadge');
    const alertBox = document.getElementById('gateEventAlert');
    const alertIcon = document.getElementById('alertIconBox');
    const alertTitle = document.getElementById('alertTitle');
    const alertDesc = document.getElementById('alertDesc');
    const countdownBox = document.getElementById('countdownBox');
    const countdownTimer = document.getElementById('countdownTimer');
    const carIllustration = document.getElementById('carIllustration');
    const tbodyLogs = document.getElementById('recentActivitiesTbody');

    // Tombol Buka Darurat GPS
    const btnEmergencyGps = document.getElementById('btnEmergencyGpsOpen');
    const btnEmergencyText = document.getElementById('btnEmergencyGpsText');
    const gpsFeedback = document.getElementById('gpsFeedbackAlert');

    let currentGateState = 'standby';
    let lastHandledLogId = null;
    let autoCloseTimer = null;
    let countdownInterval = null;

    // Fungsi Render Tampilan Palang (Standby / Terbuka / Ditolak)
    function applyGateState(state, noticeData = null) {
        if (state === 'opened') {
            currentGateState = 'opened';
            gateArm.classList.add('open');
            ledRed.classList.remove('active', 'blink');
            ledGreen.classList.add('active');
            cabinetScreen.textContent = 'OPEN';
            cabinetScreen.style.color = '#10B981';
            gateStateBadge.className = 'badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 font-monospace';
            gateStateBadge.textContent = 'TERBUKA (AKSES DITERIMA)';

            if (carIllustration) {
                carIllustration.classList.add('drive-through');
            }

            if (noticeData) {
                alertBox.className = 'alert alert-success d-flex align-items-center gap-3 p-3 rounded-3 mb-0 shadow-sm';
                alertIcon.style.background = 'rgba(16, 185, 129, 0.2)';
                alertIcon.style.color = '#10B981';
                alertIcon.innerHTML = '<i class="fas fa-circle-check fa-lg"></i>';
                alertTitle.textContent = noticeData.title || 'Palang Terbuka';
                alertDesc.textContent = (noticeData.message || '') + ' · ' + (noticeData.detail || '');
                countdownBox.classList.remove('d-none');
                
                startCountdown(noticeData.seconds_remaining || 6);
            }
        } 
        else if (state === 'rejected') {
            currentGateState = 'rejected';
            gateArm.classList.remove('open');
            ledGreen.classList.remove('active');
            ledRed.classList.add('active', 'blink');
            cabinetScreen.textContent = 'DENIED';
            cabinetScreen.style.color = '#EF4444';
            gateStateBadge.className = 'badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 font-monospace';
            gateStateBadge.textContent = 'DITOLAK (TERTUTUP)';

            if (carIllustration) {
                carIllustration.classList.remove('drive-through');
            }

            if (noticeData) {
                alertBox.className = 'alert alert-danger d-flex align-items-center gap-3 p-3 rounded-3 mb-0 shadow-sm';
                alertIcon.style.background = 'rgba(239, 68, 68, 0.2)';
                alertIcon.style.color = '#EF4444';
                alertIcon.innerHTML = '<i class="fas fa-ban fa-lg"></i>';
                alertTitle.textContent = noticeData.title || 'Akses Ditolak';
                alertDesc.textContent = (noticeData.message || '') + ' · ' + (noticeData.detail || '');
                countdownBox.classList.add('d-none');
            }

            // Setelah 5 detik kembali ke standby
            clearTimeout(autoCloseTimer);
            autoCloseTimer = setTimeout(() => {
                applyGateState('standby');
            }, 5000);
        } 
        else {
            // STANDBY / TERTUTUP
            currentGateState = 'standby';
            gateArm.classList.remove('open');
            ledRed.classList.add('active');
            ledRed.classList.remove('blink');
            ledGreen.classList.remove('active');
            cabinetScreen.textContent = 'STANDBY';
            cabinetScreen.style.color = '#38BDF8';
            gateStateBadge.className = 'badge bg-secondary-subtle text-secondary border px-2.5 py-1 font-monospace';
            gateStateBadge.textContent = 'STANDBY (TERTUTUP)';

            if (carIllustration) {
                carIllustration.classList.remove('drive-through');
            }

            alertBox.className = 'alert alert-secondary d-flex align-items-center gap-3 p-3 rounded-3 mb-0';
            alertIcon.style.background = 'rgba(100, 116, 139, 0.15)';
            alertIcon.style.color = '#64748B';
            alertIcon.innerHTML = '<i class="fas fa-shield-halved fa-lg"></i>';
            alertTitle.textContent = 'Menunggu Aktivitas Tap Kartu atau GPS...';
            alertDesc.textContent = 'Tempelkan E-KTP pada RFID RC522 gerbang atau gunakan tombol buka darurat GPS warga.';
            countdownBox.classList.add('d-none');
            clearInterval(countdownInterval);
        }
    }

    function startCountdown(seconds) {
        clearInterval(countdownInterval);
        let remain = Math.max(1, Math.min(seconds, 6));
        countdownTimer.textContent = `Menutup: ${remain}s`;

        countdownInterval = setInterval(() => {
            remain--;
            if (remain <= 0) {
                clearInterval(countdownInterval);
                applyGateState('standby');
            } else {
                countdownTimer.textContent = `Menutup: ${remain}s`;
            }
        }, 1000);
    }

    // Default standby saat pertama dibuka
    applyGateState('standby');

    // 1. Polling Real-time Status Gerbang (/smart-gate/live-status) setiap 1.8 detik
    function pollLiveStatus() {
        fetch(`{{ route('smart_gate.live_status') }}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                if (res.gate_state === 'opened') {
                    applyGateState('opened', res.gate_notice);
                } else if (res.gate_state === 'rejected') {
                    applyGateState('rejected', res.gate_notice);
                } else if (currentGateState !== 'standby' && res.gate_state === 'standby') {
                    applyGateState('standby');
                }

                if (Array.isArray(res.recent_logs)) {
                    renderRecentLogs(res.recent_logs);
                }
            }
        })
        .catch(() => {});
    }

    function renderRecentLogs(logs) {
        if (!tbodyLogs) return;
        if (logs.length === 0) {
            tbodyLogs.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="fas fa-torii-gate fa-2x mb-2 d-block opacity-25"></i>
                        <span>Belum ada catatan aktivitas portal.</span>
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        logs.forEach(log => {
            let methodBadge = '';
            if (log.method === 'gps_emergency') {
                methodBadge = '<span class="badge bg-danger-subtle text-danger border border-danger-subtle font-monospace" style="font-size: 0.68rem;"><i class="fas fa-location-crosshairs me-1"></i>Darurat GPS</span>';
            } else if (log.method === 'simulation') {
                methodBadge = '<span class="badge bg-info-subtle text-info border border-info-subtle font-monospace" style="font-size: 0.68rem;"><i class="fas fa-microchip me-1"></i>Simulasi</span>';
            } else {
                methodBadge = '<span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace" style="font-size: 0.68rem;"><i class="fas fa-id-card me-1"></i>RFID RC522</span>';
            }

            html += `
                <tr style="border-bottom: 1px solid var(--nx-border);">
                    <td class="py-2.5 px-3">
                        <span class="font-monospace fw-bold" style="color: var(--nx-text);">${log.created_at}</span>
                        <small class="text-muted d-block" style="font-size: 0.68rem;">${log.diff_for_humans}</small>
                    </td>
                    <td class="py-2.5 px-3">${methodBadge}</td>
                    <td class="py-2.5 px-3 font-monospace">
                        <span class="badge px-2 py-1" style="background: var(--nx-bg); color: var(--nx-text); border: 1px solid var(--nx-border); letter-spacing: 0.5px;">
                            ${log.uid}
                        </span>
                    </td>
                    <td class="py-2.5 px-3">
                        ${log.warga_nama !== 'Belum Terdaftar' 
                            ? `<strong style="color: var(--nx-text);">${log.warga_nama}</strong><small class="text-muted d-block" style="font-size: 0.68rem;">${log.warga_rt || '-'}</small>`
                            : `<span class="text-muted fst-italic">Belum Terdaftar</span>`
                        }
                    </td>
                    <td class="py-2.5 px-3">
                        <span class="badge ${log.status_badge} font-monospace" style="font-size: 0.68rem;">
                            ${log.status_label}
                        </span>
                    </td>
                    <td class="py-2.5 px-3 text-end text-muted" style="font-size: 0.72rem;">
                        ${log.message || '-'}
                    </td>
                </tr>
            `;
        });
        tbodyLogs.innerHTML = html;
    }

    // Jalankan polling setiap 1.8 detik
    const liveInterval = setInterval(pollLiveStatus, 1800);

    // 2. Fitur Buka Portal Darurat via GPS Geofencing (Haversine)
    if (btnEmergencyGps) {
        btnEmergencyGps.addEventListener('click', function () {
            if (!navigator.geolocation) {
                showGpsFeedback('danger', 'Perangkat atau browser Anda tidak mendukung fitur lokasi (Geolocation).');
                return;
            }

            btnEmergencyGps.disabled = true;
            btnEmergencyText.textContent = 'Mendeteksi Posisi GPS...';

            navigator.geolocation.getCurrentPosition(
                function (position) {
                    btnEmergencyText.textContent = 'Memvalidasi Jarak ke Gerbang...';

                    fetch(`{{ route('smart_gate.emergency_open') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude
                        })
                    })
                    .then(r => r.json().then(data => ({ status: r.status, body: data })))
                    .then(({ status, body }) => {
                        if (status === 200 && body.success) {
                            showGpsFeedback('success', `<i class="fas fa-circle-check me-1"></i><strong>${body.message}</strong> (Jarak: ${body.distance_meters} m)`);
                            applyGateState('opened', {
                                title: 'Palang Terbuka — Darurat GPS Disetujui',
                                message: `Selamat Datang, ${body.warga_nama || 'Warga'}`,
                                detail: `Jarak: ${body.distance_meters} m (Batas: ${body.radius} m)`,
                                seconds_remaining: 6
                            });
                        } else {
                            showGpsFeedback('danger', `<i class="fas fa-triangle-exclamation me-1"></i>${body.message || 'Gagal memproses buka darurat.'}`);
                            applyGateState('rejected', {
                                title: 'Akses Darurat GPS Ditolak',
                                message: body.message || 'Di Luar Radius Gerbang',
                                detail: `Jarak: ${body.distance_meters || '?'} m (Batas: ${body.radius || '?'} m)`
                            });
                        }
                    })
                    .catch(err => {
                        showGpsFeedback('danger', 'Terjadi kesalahan jaringan saat memproses buka gerbang.');
                    })
                    .finally(() => {
                        btnEmergencyGps.disabled = false;
                        btnEmergencyText.textContent = 'Buka Portal Darurat (GPS)';
                    });
                },
                function (err) {
                    btnEmergencyGps.disabled = false;
                    btnEmergencyText.textContent = 'Buka Portal Darurat (GPS)';
                    let msg = 'Gagal mendeteksi lokasi GPS.';
                    if (err.code === 1) msg = 'Izin akses lokasi ditolak. Silakan izinkan browser mengakses lokasi perangkat Anda.';
                    else if (err.code === 2) msg = 'Posisi GPS tidak dapat ditemukan. Pastikan GPS perangkat menyala.';
                    else if (err.code === 3) msg = 'Waktu permintaan lokasi habis (timeout). Coba lagi.';
                    showGpsFeedback('danger', `<i class="fas fa-exclamation-circle me-1"></i>${msg}`);
                },
                { enableHighAccuracy: true, timeout: 12000, maximumAge: 0 }
            );
        });
    }

    function showGpsFeedback(type, htmlContent) {
        gpsFeedback.className = `alert alert-${type} p-3 rounded-3 mb-3 small d-block shadow-sm`;
        gpsFeedback.innerHTML = htmlContent;
    }
});
</script>
@endpush
