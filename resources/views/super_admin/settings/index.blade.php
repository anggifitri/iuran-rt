@extends('layouts.super_admin')

@section('title', 'Pengaturan & Konfigurasi Platform — NexaNest Super Admin')
@section('page_title', 'Pengaturan Platform')

@section('content')
<!-- TOP HEADER & STATUS BAR -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge font-monospace px-2.5 py-1" style="background: rgba(40, 89, 67, 0.12); color: var(--nx-forest); border: 1px solid rgba(40, 89, 67, 0.25); font-size: 0.72rem; border-radius: 6px;">
                <i class="fas fa-sliders me-1"></i> CONTROL CENTER HQ
            </span>
            <span class="badge font-monospace px-2.5 py-1" style="background: rgba(14, 165, 233, 0.12); color: #0284C7; border: 1px solid rgba(14, 165, 233, 0.25); font-size: 0.72rem; border-radius: 6px;">
                {{ $appVersion }}
            </span>
            <span class="badge font-monospace px-2.5 py-1 {{ $environment === 'production' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }}" style="font-size: 0.72rem; border-radius: 6px;">
                <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i> {{ strtoupper($environment) }}
            </span>
        </div>
        <h2 class="fw-bold m-0 fs-5" style="color: var(--nx-text);">Pengaturan &amp; Konfigurasi Platform</h2>
        <p class="m-0 small" style="color: var(--nx-muted);">Tata kelola konfigurasi teknis, manajemen modul non-destruktif, siaran sistem, backup &amp; disaster recovery, dan kebijakan keamanan.</p>
    </div>

    <div class="d-flex align-items-center gap-2">
        @if(($settings['maintenance_mode'] ?? '0') === '1')
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 fw-bold d-inline-flex align-items-center gap-2" style="border-radius: 8px;">
                <i class="fas fa-triangle-exclamation"></i>
                <span>Mode Pemeliharaan Global Aktif</span>
            </span>
        @elseif(($settings['maintenance_rw_018'] ?? '0') === '1')
            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 fw-bold d-inline-flex align-items-center gap-2" style="border-radius: 8px;">
                <i class="fas fa-wrench"></i>
                <span>Pemeliharaan RW 018 Aktif</span>
            </span>
        @else
            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fw-bold d-inline-flex align-items-center gap-2" style="border-radius: 8px;">
                <i class="fas fa-circle-check"></i>
                <span>Platform Beroperasi Normal</span>
            </span>
        @endif

        <form action="{{ route('super_admin.settings.backup.create') }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-success fw-semibold d-inline-flex align-items-center gap-2 px-3 py-2 shadow-sm" style="border-radius: 8px;" title="Buat cadangan database SQLite instan">
                <i class="fas fa-database"></i>
                <span class="d-none d-md-inline">Backup Instan</span>
            </button>
        </form>
    </div>
</div>

<!-- 5 INTERNAL TABS NAVIGATION -->
<div class="mb-4">
    <ul class="nav nav-pills p-1.5 rounded-3 border d-flex flex-wrap gap-1" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
        <li class="nav-item flex-grow-1 flex-md-grow-0">
            <a class="nav-link text-center px-3 py-2 fw-bold small d-flex align-items-center justify-content-center gap-2 {{ $activeTab === 'general' ? 'active' : '' }}" 
               href="{{ route('super_admin.settings.index', ['tab' => 'general']) }}"
               style="{{ $activeTab === 'general' ? 'background: var(--nx-forest); color: #FFFFFF; border-radius: 8px;' : 'color: var(--nx-muted); border-radius: 8px;' }}">
                <i class="fas fa-sliders"></i>
                <span>1. Konfigurasi Umum</span>
            </a>
        </li>
        <li class="nav-item flex-grow-1 flex-md-grow-0">
            <a class="nav-link text-center px-3 py-2 fw-bold small d-flex align-items-center justify-content-center gap-2 {{ $activeTab === 'modules' ? 'active' : '' }}" 
               href="{{ route('super_admin.settings.index', ['tab' => 'modules']) }}"
               style="{{ $activeTab === 'modules' ? 'background: var(--nx-forest); color: #FFFFFF; border-radius: 8px;' : 'color: var(--nx-muted); border-radius: 8px;' }}">
                <i class="fas fa-cubes-stacked"></i>
                <span>2. Manajemen Modul</span>
                <span class="badge rounded-pill {{ $activeTab === 'modules' ? 'bg-white text-dark' : 'bg-light text-muted border' }}" style="font-size: 0.68rem;">22</span>
            </a>
        </li>
        <li class="nav-item flex-grow-1 flex-md-grow-0">
            <a class="nav-link text-center px-3 py-2 fw-bold small d-flex align-items-center justify-content-center gap-2 {{ $activeTab === 'notifications' ? 'active' : '' }}" 
               href="{{ route('super_admin.settings.index', ['tab' => 'notifications']) }}"
               style="{{ $activeTab === 'notifications' ? 'background: var(--nx-forest); color: #FFFFFF; border-radius: 8px;' : 'color: var(--nx-muted); border-radius: 8px;' }}">
                <i class="fas fa-bullhorn"></i>
                <span>3. Notifikasi Platform</span>
                <span class="badge rounded-pill {{ $activeTab === 'notifications' ? 'bg-white text-dark' : 'bg-light text-muted border' }}" style="font-size: 0.68rem;">Siaran</span>
            </a>
        </li>
        <li class="nav-item flex-grow-1 flex-md-grow-0">
            <a class="nav-link text-center px-3 py-2 fw-bold small d-flex align-items-center justify-content-center gap-2 {{ $activeTab === 'backup' ? 'active' : '' }}" 
               href="{{ route('super_admin.settings.index', ['tab' => 'backup']) }}"
               style="{{ $activeTab === 'backup' ? 'background: var(--nx-forest); color: #FFFFFF; border-radius: 8px;' : 'color: var(--nx-muted); border-radius: 8px;' }}">
                <i class="fas fa-database"></i>
                <span>4. Backup &amp; Pemulihan</span>
                <span class="badge rounded-pill {{ $activeTab === 'backup' ? 'bg-white text-dark' : 'bg-light text-muted border' }}" style="font-size: 0.68rem;">DR</span>
            </a>
        </li>
        <li class="nav-item flex-grow-1 flex-md-grow-0">
            <a class="nav-link text-center px-3 py-2 fw-bold small d-flex align-items-center justify-content-center gap-2 {{ $activeTab === 'security' ? 'active' : '' }}" 
               href="{{ route('super_admin.settings.index', ['tab' => 'security']) }}"
               style="{{ $activeTab === 'security' ? 'background: var(--nx-forest); color: #FFFFFF; border-radius: 8px;' : 'color: var(--nx-muted); border-radius: 8px;' }}">
                <i class="fas fa-shield-halved"></i>
                <span>5. Keamanan &amp; Audit</span>
            </a>
        </li>
    </ul>
</div>

<!-- ========================================================================= -->
<!-- TAB 1: KONFIGURASI UMUM -->
<!-- ========================================================================= -->
@if($activeTab === 'general')
<div class="row g-4">
    <!-- Form Utama Konfigurasi Umum -->
    <div class="col-lg-8">
        <div class="sa-card p-4 rounded-3 border mb-4" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
            <form action="{{ route('super_admin.settings.general.update') }}" method="POST">
                @csrf
                
                <!-- BAGIAN 1: IDENTITAS & BRANDING -->
                <div class="border-bottom pb-3 mb-4" style="border-color: var(--nx-border) !important;">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(40, 89, 67, 0.12); color: var(--nx-forest);">
                            <i class="fas fa-feather"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold m-0 fs-6" style="color: var(--nx-text);">Identitas &amp; Informasi Platform</h5>
                            <small style="color: var(--nx-muted);">Nama platform, tagline, dan kontak resmi tingkat sistem</small>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold" style="color: var(--nx-text);">Nama Platform</label>
                        <input type="text" name="platform_name" class="form-control" 
                               value="{{ $settings['platform_name'] ?? 'NexaNest Smart Community Platform' }}" required
                               style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold" style="color: var(--nx-text);">Tagline Platform</label>
                        <input type="text" name="platform_tagline" class="form-control" 
                               value="{{ $settings['platform_tagline'] ?? 'Smart & Sustainable Multi-RW Neighborhood Control Center' }}"
                               style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold" style="color: var(--nx-text);">Deskripsi Singkat Platform</label>
                        <textarea name="platform_description" rows="2" class="form-control" 
                                  style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">{{ $settings['platform_description'] ?? 'Platform tata kelola digital rukun warga terpadu yang memadukan transparansi keuangan, administrasi kependudukan, pemantauan lingkungan, dan pemberdayaan komunitas.' }}</textarea>
                    </div>
                </div>

                <!-- BAGIAN 2: WAKTU & FORMAT TANGGAL -->
                <div class="border-bottom pb-3 mb-4 pt-2" style="border-color: var(--nx-border) !important;">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(14, 165, 233, 0.12); color: #0284C7;">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold m-0 fs-6" style="color: var(--nx-text);">Zona Waktu &amp; Format Tanggal</h5>
                            <small style="color: var(--nx-muted);">Standarisasi waktu dan penanggalan pada log audit, kuitansi, dan transaksi</small>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold" style="color: var(--nx-text);">Zona Waktu (Timezone)</label>
                        <select name="app_timezone" class="form-select" style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                            <option value="Asia/Jakarta" {{ ($settings['app_timezone'] ?? 'Asia/Jakarta') === 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta (WIB, UTC+7)</option>
                            <option value="Asia/Makassar" {{ ($settings['app_timezone'] ?? '') === 'Asia/Makassar' ? 'selected' : '' }}>Asia/Makassar (WITA, UTC+8)</option>
                            <option value="Asia/Jayapura" {{ ($settings['app_timezone'] ?? '') === 'Asia/Jayapura' ? 'selected' : '' }}>Asia/Jayapura (WIT, UTC+9)</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold" style="color: var(--nx-text);">Format Tanggal</label>
                        <select name="date_format" class="form-select" style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                            <option value="d M Y" {{ ($settings['date_format'] ?? 'd M Y') === 'd M Y' ? 'selected' : '' }}>d M Y (Contoh: 25 Nov 2026)</option>
                            <option value="Y-m-d" {{ ($settings['date_format'] ?? '') === 'Y-m-d' ? 'selected' : '' }}>Y-m-d (Contoh: 2026-11-25)</option>
                            <option value="d/m/Y" {{ ($settings['date_format'] ?? '') === 'd/m/Y' ? 'selected' : '' }}>d/m/Y (Contoh: 25/11/2026)</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold" style="color: var(--nx-text);">Format Jam</label>
                        <select name="time_format" class="form-select" style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                            <option value="H:i" {{ ($settings['time_format'] ?? 'H:i') === 'H:i' ? 'selected' : '' }}>24 Jam (Contoh: 14:30)</option>
                            <option value="h:i A" {{ ($settings['time_format'] ?? '') === 'h:i A' ? 'selected' : '' }}>12 Jam (Contoh: 02:30 PM)</option>
                        </select>
                    </div>
                </div>

                <!-- BAGIAN 3: KONTAK DUKUNGAN RESMI -->
                <div class="border-bottom pb-3 mb-4 pt-2" style="border-color: var(--nx-border) !important;">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(16, 185, 129, 0.12); color: #10B981;">
                            <i class="fas fa-headset"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold m-0 fs-6" style="color: var(--nx-text);">Kontak Resmi Dukungan Platform</h5>
                            <small style="color: var(--nx-muted);">Tautan bantuan teknis bagi seluruh Admin RW dan Admin RT</small>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold" style="color: var(--nx-text);">Email Dukungan Super Admin</label>
                        <input type="email" name="super_admin_contact" class="form-control" 
                               value="{{ $settings['super_admin_contact'] ?? 'anggifitri0911@gmail.com' }}" required
                               style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold" style="color: var(--nx-text);">Nomor Telepon / WhatsApp Helpdesk</label>
                        <input type="text" name="super_admin_phone" class="form-control" 
                               value="{{ $settings['super_admin_phone'] ?? '+62 812-3456-7890' }}"
                               style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold" style="color: var(--nx-text);">Wilayah RW Perintis Utama</label>
                        <input type="text" name="default_rw" class="form-control" 
                               value="{{ $settings['default_rw'] ?? '018' }}" readonly
                               style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border); cursor: not-allowed;">
                        <small class="text-muted" style="font-size: 0.72rem;">RW 018 saat ini merupakan satu-satunya wilayah operasional aktif platform.</small>
                    </div>
                </div>

                <!-- BAGIAN 4: PENGATURAN MODE PEMELIHARAAN (MAINTENANCE MODE) -->
                <div class="border-bottom pb-3 mb-4 pt-2" style="border-color: var(--nx-border) !important;">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(239, 68, 68, 0.12); color: #EF4444;">
                            <i class="fas fa-triangle-exclamation"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold m-0 fs-6" style="color: var(--nx-text);">Mode Pemeliharaan (Maintenance Mode)</h5>
                            <small style="color: var(--nx-muted);">Pengaturan pengalihan operasional sistem saat perbaikan atau migrasi platform</small>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border h-100" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" name="maintenance_mode" value="1" id="switchMaintGlobal" {{ ($settings['maintenance_mode'] ?? '0') === '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold small text-danger" for="switchMaintGlobal">
                                    Aktifkan Mode Pemeliharaan Global
                                </label>
                            </div>
                            <small class="d-block mt-2" style="color: var(--nx-muted); font-size: 0.76rem; line-height: 1.4;">
                                Menutup akses operasional seluruh pengguna (Admin RW, Admin RT, Warga). <strong>Super Admin HQ tetap memiliki akses penuh (whitelist bypass).</strong>
                            </small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border h-100" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" name="maintenance_rw_018" value="1" id="switchMaintRw018" {{ ($settings['maintenance_rw_018'] ?? '0') === '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold small text-warning" for="switchMaintRw018">
                                    Pemeliharaan Parsial Khusus RW 018
                                </label>
                            </div>
                            <small class="d-block mt-2" style="color: var(--nx-muted); font-size: 0.76rem; line-height: 1.4;">
                                Hanya membatasi operasional di lingkungan RW 018 tanpa mematikan platform tingkat pusat.
                            </small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold" style="color: var(--nx-text);">Alasan Pemeliharaan</label>
                        <input type="text" name="maintenance_reason" class="form-control" 
                               value="{{ $settings['maintenance_reason'] ?? 'Optimalisasi performa & pembaruan sistem database' }}"
                               placeholder="Contoh: Migrasi server & indeks database"
                               style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold" style="color: var(--nx-text);">Estimasi Selesai</label>
                        <input type="text" name="maintenance_estimated_end" class="form-control" 
                               value="{{ $settings['maintenance_estimated_end'] ?? '1-2 Jam' }}"
                               placeholder="Contoh: 2 jam / 25 Nov 2026, 16:00 WIB"
                               style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-bold" style="color: var(--nx-text);">Pesan Pengumuman Tampil ke Pengguna</label>
                        <textarea name="maintenance_message" rows="2" class="form-control" 
                                  style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">{{ $settings['maintenance_message'] ?? 'Platform NexaNest sedang dalam pemeliharaan berkala untuk peningkatan keandalan layanan. Seluruh data Anda aman dan sistem akan kembali aktif segera.' }}</textarea>
                    </div>
                </div>

                <!-- SUBMIT BUTTON -->
                <div class="d-flex justify-content-end pt-3 border-top" style="border-color: var(--nx-border) !important;">
                    <button type="submit" class="btn btn-success px-4 py-2 fw-bold d-inline-flex align-items-center gap-2 shadow-sm" style="border-radius: 8px;">
                        <i class="fas fa-check-circle"></i>
                        <span>Simpan Konfigurasi Umum</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Panel Samping: System Runtime & Logo Info -->
    <div class="col-lg-4">
        <!-- Card 1: Logo & Branding Preview -->
        <div class="sa-card p-4 rounded-3 border mb-4 text-center" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
            <div class="p-3 rounded-3 mb-3 d-inline-block border" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                <x-nexanest-logo size="lg" />
            </div>
            <h5 class="fw-bold m-0 fs-6" style="color: var(--nx-text);">{{ $settings['platform_name'] ?? 'NexaNest Smart Community' }}</h5>
            <p class="small m-0 text-muted mt-1">{{ $settings['platform_tagline'] ?? 'Smart & Sustainable Multi-RW Neighborhood' }}</p>
            <div class="mt-3 pt-3 border-top d-flex align-items-center justify-content-between text-start" style="border-color: var(--nx-border) !important; font-size: 0.76rem;">
                <span style="color: var(--nx-muted);">Wilayah Perintis:</span>
                <strong style="color: var(--nx-text);">RW 018 Kelurahan {{ $rw018->kelurahan ?? 'Mekarjaya' }}</strong>
            </div>
            <div class="pt-2 d-flex align-items-center justify-content-between text-start" style="font-size: 0.76rem;">
                <span style="color: var(--nx-muted);">Dukungan Super Admin:</span>
                <strong class="font-monospace" style="color: var(--nx-forest);">{{ $settings['super_admin_contact'] ?? 'anggifitri0911@gmail.com' }}</strong>
            </div>
        </div>

        <!-- Card 2: System Metadata Dinamis -->
        <div class="sa-card p-4 rounded-3 border" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
            <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2" style="border-color: var(--nx-border) !important;">
                <h6 class="fw-bold m-0 fs-6" style="color: var(--nx-text);">
                    <i class="fas fa-microchip text-primary me-1"></i> Spesifikasi Runtime
                </h6>
                <span class="badge bg-primary-subtle text-primary font-monospace" style="font-size: 0.70rem;">READY</span>
            </div>

            <div class="d-flex flex-column gap-2" style="font-size: 0.8rem;">
                <div class="d-flex align-items-center justify-content-between">
                    <span style="color: var(--nx-muted);">Versi Aplikasi:</span>
                    <strong class="font-monospace" style="color: var(--nx-text);">{{ $appVersion }}</strong>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <span style="color: var(--nx-muted);">Versi Laravel:</span>
                    <strong class="font-monospace" style="color: var(--nx-text);">v{{ $laravelVersion }}</strong>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <span style="color: var(--nx-muted);">Versi PHP:</span>
                    <strong class="font-monospace" style="color: var(--nx-text);">{{ $phpVersion }}</strong>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <span style="color: var(--nx-muted);">Database Driver:</span>
                    <span class="badge bg-secondary-subtle text-secondary font-monospace sa-badge-db-driver">{{ strtoupper($dbDriver) }} (SQLite 3)</span>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <span style="color: var(--nx-muted);">Environment:</span>
                    <span class="badge {{ $environment === 'production' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} font-monospace">{{ strtoupper($environment) }}</span>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <span style="color: var(--nx-muted);">Timezone Aktif:</span>
                    <strong class="font-monospace" style="color: var(--nx-text);">{{ $settings['app_timezone'] ?? 'Asia/Jakarta' }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- ========================================================================= -->
<!-- TAB 2: MANAJEMEN MODUL -->
<!-- ========================================================================= -->
@if($activeTab === 'modules')
<!-- KPI STATS MODUL -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="sa-kpi-card p-3 rounded-3 border h-100" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
            <span class="small fw-semibold" style="color: var(--nx-muted);">Total Modul Terdaftar</span>
            <div class="d-flex align-items-baseline gap-2 mt-1">
                <h3 class="fs-4 fw-bold m-0" style="color: var(--nx-text);">{{ $moduleStats['total'] }}</h3>
                <span class="small" style="color: var(--nx-muted);">Modul Sistem</span>
            </div>
            <small class="text-success mt-1 d-block"><i class="fas fa-check-double me-1"></i>Arsitektur Multi-RW Terintegrasi</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="sa-kpi-card p-3 rounded-3 border h-100" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
            <span class="small fw-semibold" style="color: var(--nx-muted);">Aktif Secara Global</span>
            <div class="d-flex align-items-baseline gap-2 mt-1">
                <h3 class="fs-4 fw-bold m-0 text-success">{{ $moduleStats['active_global'] }}</h3>
                <span class="small" style="color: var(--nx-muted);">dari {{ $moduleStats['total'] }} Modul</span>
            </div>
            <small class="text-muted mt-1 d-block">Dapat diakses oleh seluruh wilayah aktif</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="sa-kpi-card p-3 rounded-3 border h-100" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
            <span class="small fw-semibold" style="color: var(--nx-muted);">Dinonaktifkan Global</span>
            <div class="d-flex align-items-baseline gap-2 mt-1">
                <h3 class="fs-4 fw-bold m-0 text-danger">{{ $moduleStats['inactive_global'] }}</h3>
                <span class="small" style="color: var(--nx-muted);">Modul Nonaktif</span>
            </div>
            <small class="text-muted mt-1 d-block">Akses diblokir di tingkat platform</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="sa-kpi-card p-3 rounded-3 border h-100" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
            <span class="small fw-semibold" style="color: var(--nx-muted);">Pengecualian RW 018</span>
            <div class="d-flex align-items-baseline gap-2 mt-1">
                <h3 class="fs-4 fw-bold m-0 text-warning">{{ $moduleStats['disabled_rw018'] }}</h3>
                <span class="small" style="color: var(--nx-muted);">Modul Dibatasi</span>
            </div>
            <small class="text-warning mt-1 d-block"><i class="fas fa-shield me-1"></i>Kebijakan Spesifik RW 018</small>
        </div>
    </div>
</div>

<!-- INFORMASI PRINSIP NON-DESTRUKTIF -->
<div class="p-3 rounded-3 border mb-4 d-flex align-items-center justify-content-between gap-3 flex-wrap" style="background: rgba(14, 165, 233, 0.08); border-color: rgba(14, 165, 233, 0.25) !important;">
    <div class="d-flex align-items-center gap-3">
        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px; background: rgba(14, 165, 233, 0.2); color: #0284C7;">
            <i class="fas fa-shield-halved fs-5"></i>
        </div>
        <div>
            <strong class="d-block text-primary small">Prinsip Proteksi Non-Destruktif Data Modul:</strong>
            <p class="m-0 text-muted" style="font-size: 0.78rem; line-height: 1.4;">
                Menonaktifkan modul HANYA menutup hak akses antarmuka dan membatasi rute operasional. <strong>Seluruh struktur tabel database, mutasi transaksi kas, riwayat surat permohonan, setoran iuran, dan data warga tetap aman 100% dan tidak pernah dihapus.</strong>
            </p>
        </div>
    </div>
    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1.5 font-monospace" style="font-size: 0.74rem;">
        100% Zero Data Loss
    </span>
</div>

<!-- FILTER KATEGORI MODUL -->
<div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
    <span class="small fw-semibold text-muted me-1">Filter Kategori:</span>
    <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-1 active module-filter-btn" data-category="all" style="border-radius: 20px; font-size: 0.76rem;">
        Semua (22)
    </button>
    <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-1 module-filter-btn" data-category="Administrasi Wilayah" style="border-radius: 20px; font-size: 0.76rem;">
        Administrasi Wilayah
    </button>
    <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-1 module-filter-btn" data-category="Keuangan & Iuran" style="border-radius: 20px; font-size: 0.76rem;">
        Keuangan &amp; Iuran
    </button>
    <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-1 module-filter-btn" data-category="Layanan Warga" style="border-radius: 20px; font-size: 0.76rem;">
        Layanan Warga
    </button>
    <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-1 module-filter-btn" data-category="Komunitas & Lingkungan" style="border-radius: 20px; font-size: 0.76rem;">
        Komunitas &amp; Lingkungan
    </button>
    <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-1 module-filter-btn" data-category="Sistem & Pengawasan" style="border-radius: 20px; font-size: 0.76rem;">
        Sistem &amp; Pengawasan
    </button>
</div>

<!-- 22 MODUL GRID -->
<div class="row g-3" id="modulesGrid">
    @foreach($modules as $m)
        @php
            $isRw018Disabled = in_array('018', $m->disabled_rw_numbers ?? []);
            $badgeCategory = match($m->category) {
                'Administrasi Wilayah' => 'background: rgba(14, 165, 233, 0.12); color: #0284C7; border: 1px solid rgba(14, 165, 233, 0.25);',
                'Keuangan & Iuran' => 'background: rgba(16, 185, 129, 0.12); color: #10B981; border: 1px solid rgba(16, 185, 129, 0.25);',
                'Layanan Warga' => 'background: rgba(245, 158, 11, 0.12); color: #D97706; border: 1px solid rgba(245, 158, 11, 0.25);',
                'Komunitas & Lingkungan' => 'background: rgba(99, 102, 241, 0.12); color: #6366F1; border: 1px solid rgba(99, 102, 241, 0.25);',
                default => 'background: rgba(100, 116, 139, 0.12); color: #64748B; border: 1px solid rgba(100, 116, 139, 0.25);'
            };
        @endphp
        <div class="col-md-6 col-xl-6 module-card-item" data-category="{{ $m->category }}">
            <div class="sa-card h-100 d-flex flex-column justify-content-between p-3.5 rounded-3 border" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
                <div>
                    <!-- Header Card -->
                    <div class="d-flex align-items-start justify-content-between gap-3 mb-2.5">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" 
                                 style="width: 44px; height: 44px; font-size: 1.15rem; background: {{ $m->is_enabled_globally ? 'rgba(40, 89, 67, 0.15)' : 'rgba(239, 68, 68, 0.15)' }}; color: {{ $m->is_enabled_globally ? 'var(--nx-forest)' : '#EF4444' }};">
                                <i class="fas {{ $m->icon ?: 'fa-cubes' }}"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold m-0 fs-6" style="color: var(--nx-text);">{{ $m->module_name }}</h5>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <span class="badge font-monospace" style="font-size: 0.68rem; background: var(--nx-bg); color: var(--nx-muted); border: 1px solid var(--nx-border);">
                                        {{ $m->module_key }}
                                    </span>
                                    <span class="badge" style="{{ $badgeCategory }} font-size: 0.68rem;">
                                        {{ $m->category }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Status Global Pill -->
                        <span class="badge {{ $m->is_enabled_globally ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} px-2 py-1" style="font-size: 0.72rem;">
                            {{ $m->is_enabled_globally ? 'Aktif Global' : 'Nonaktif Global' }}
                        </span>
                    </div>

                    <!-- Description -->
                    <p class="small mb-3 mt-2" style="color: var(--nx-muted); font-size: 0.82rem; line-height: 1.45;">
                        {{ str_replace('RT 001 - RT 008', 'RT 006 – RT 010', $m->description) }}
                    </p>

                    <!-- Dependencies & Health -->
                    <div class="d-flex flex-wrap align-items-center gap-1.5 mb-2" style="font-size: 0.72rem;">
                        <span class="text-muted"><i class="fas fa-link me-1"></i>Relasi:</span>
                        @foreach($m->dependencies as $dep)
                            <span class="badge bg-light text-dark border font-monospace" style="font-size: 0.66rem;">{{ $dep }}</span>
                        @endforeach
                        <span class="badge bg-success-subtle text-success ms-auto font-monospace" style="font-size: 0.66rem;">
                            <i class="fas fa-database me-1"></i>DB Ready
                        </span>
                    </div>
                </div>

                <!-- Footer Toggles (Global & RW 018) -->
                <div class="pt-2.5 mt-3 border-top d-flex flex-wrap align-items-center justify-content-between gap-2" style="border-color: var(--nx-border) !important; font-size: 0.74rem;">
                    <!-- Toggle Global -->
                    <form action="{{ route('super_admin.settings.modules.toggle_global', $m->id) }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-sm {{ $m->is_enabled_globally ? 'btn-outline-danger' : 'btn-success' }} px-2.5 py-1 d-inline-flex align-items-center gap-1.5" style="border-radius: 6px; font-size: 0.74rem;" title="Ubah status ketersediaan di seluruh platform">
                            <i class="fas {{ $m->is_enabled_globally ? 'fa-ban' : 'fa-check' }}"></i>
                            <span>{{ $m->is_enabled_globally ? 'Nonaktifkan Global' : 'Aktifkan Global' }}</span>
                        </button>
                    </form>

                    <!-- Toggle Override RW 018 -->
                    <form action="{{ route('super_admin.settings.modules.toggle_rw', $m->id) }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-sm {{ $isRw018Disabled ? 'btn-warning' : 'btn-outline-secondary' }} px-2.5 py-1 d-inline-flex align-items-center gap-1.5" style="border-radius: 6px; font-size: 0.74rem;" title="Atur ketersediaan khusus wilayah operasional RW 018">
                            <i class="fas {{ $isRw018Disabled ? 'fa-lock-open' : 'fa-lock' }}"></i>
                            <span>{{ $isRw018Disabled ? 'Aktifkan di RW 018' : 'Batasi di RW 018' }}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endif

<!-- ========================================================================= -->
<!-- TAB 3: NOTIFIKASI PLATFORM -->
<!-- ========================================================================= -->
@if($activeTab === 'notifications')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
    <div>
        <h3 class="fw-bold m-0 fs-6" style="color: var(--nx-text);">Pusat Siaran &amp; Pengumuman Sistem Tingkat Platform</h3>
        <p class="m-0 small" style="color: var(--nx-muted);">Kirim pemberitahuan teknis, jadwal pemeliharaan, gangguan layanan, atau kebijakan ke seluruh jajaran pengurus (Terpisah dari pengumuman warga RW).</p>
    </div>

    <button type="button" class="btn btn-success px-3 py-2 fw-bold d-inline-flex align-items-center gap-2 shadow-sm" style="border-radius: 8px; font-size: 0.86rem;" data-bs-toggle="modal" data-bs-target="#modalCreatePlatformNotification">
        <i class="fas fa-bullhorn"></i>
        <span>Buat Siaran Baru</span>
    </button>
</div>

<!-- FILTER TIPE SIARAN -->
<div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
    <span class="small fw-semibold text-muted me-1">Filter Prioritas:</span>
    <a href="{{ route('super_admin.settings.index', ['tab' => 'notifications']) }}" 
       class="btn btn-sm {{ !request('notif_type') ? 'btn-secondary' : 'btn-outline-secondary' }} px-3 py-1" style="border-radius: 20px; font-size: 0.76rem;">
        Semua Siaran
    </a>
    <a href="{{ route('super_admin.settings.index', ['tab' => 'notifications', 'notif_type' => 'info']) }}" 
       class="btn btn-sm {{ request('notif_type') === 'info' ? 'btn-info text-white' : 'btn-outline-info' }} px-3 py-1" style="border-radius: 20px; font-size: 0.76rem;">
        Info
    </a>
    <a href="{{ route('super_admin.settings.index', ['tab' => 'notifications', 'notif_type' => 'warning']) }}" 
       class="btn btn-sm {{ request('notif_type') === 'warning' ? 'btn-warning text-dark' : 'btn-outline-warning' }} px-3 py-1" style="border-radius: 20px; font-size: 0.76rem;">
        Penting (Warning)
    </a>
    <a href="{{ route('super_admin.settings.index', ['tab' => 'notifications', 'notif_type' => 'critical']) }}" 
       class="btn btn-sm {{ request('notif_type') === 'critical' ? 'btn-danger' : 'btn-outline-danger' }} px-3 py-1" style="border-radius: 20px; font-size: 0.76rem;">
        Darurat (Critical)
    </a>
    <a href="{{ route('super_admin.settings.index', ['tab' => 'notifications', 'notif_type' => 'maintenance']) }}" 
       class="btn btn-sm {{ request('notif_type') === 'maintenance' ? 'btn-dark' : 'btn-outline-dark' }} px-3 py-1 notif-filter-maintenance" style="border-radius: 20px; font-size: 0.76rem;">
        Pemeliharaan
    </a>
</div>

<!-- TABEL DAFTAR SIARAN NOTIFIKASI -->
<div class="sa-card p-0 overflow-hidden rounded-3 border mb-4" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
    <div class="table-responsive">
        <table class="table align-middle m-0" style="color: var(--nx-text);">
            <thead style="background: var(--nx-bg); border-bottom: 1px solid var(--nx-border);">
                <tr>
                    <th class="py-3 px-3 small fw-bold" style="color: var(--nx-muted);">JUDUL &amp; PESAN SIARAN</th>
                    <th class="py-3 px-3 small fw-bold" style="color: var(--nx-muted);">PRIORITAS</th>
                    <th class="py-3 px-3 small fw-bold" style="color: var(--nx-muted);">TARGET AUDIENS</th>
                    <th class="py-3 px-3 small fw-bold" style="color: var(--nx-muted);">STATUS BANNER</th>
                    <th class="py-3 px-3 small fw-bold" style="color: var(--nx-muted);">PERIODE TAYANG</th>
                    <th class="py-3 px-3 small fw-bold text-end" style="color: var(--nx-muted);">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($notifications as $n)
                    @php
                        $typeBadge = match($n->type) {
                            'info' => 'bg-info-subtle text-info border border-info-subtle sa-badge-notif-info',
                            'warning' => 'bg-warning-subtle text-warning border border-warning-subtle',
                            'critical' => 'bg-danger-subtle text-danger border border-danger-subtle',
                            'maintenance' => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
                            default => 'bg-light text-dark'
                        };
                    @endphp
                    <tr style="border-bottom: 1px solid var(--nx-border);">
                        <td class="px-3 py-3">
                            <strong class="d-block small" style="color: var(--nx-text);">{{ $n->title }}</strong>
                            <small class="text-muted d-block text-truncate" style="max-width: 380px;">{{ $n->message }}</small>
                        </td>
                        <td class="px-3 py-3">
                            <span class="badge {{ $typeBadge }} px-2.5 py-1 font-monospace" style="font-size: 0.70rem;">
                                {{ strtoupper($n->type) }}
                            </span>
                        </td>
                        <td class="px-3 py-3">
                            <span class="small fw-semibold" style="color: var(--nx-text);">
                                @if($n->target_scope === 'all')
                                    <i class="fas fa-users me-1 text-primary"></i> Seluruh Pengurus
                                @elseif($n->target_scope === 'rw')
                                    <i class="fas fa-landmark me-1 text-success"></i> Khusus RW {{ $n->target_rw ?: '018' }}
                                @else
                                    <i class="fas fa-user-shield me-1 text-warning"></i> Role: {{ strtoupper($n->target_role) }}
                                @endif
                            </span>
                        </td>
                        <td class="px-3 py-3">
                            @if($n->is_banner)
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle" style="font-size: 0.70rem;">
                                    <i class="fas fa-flag me-1"></i> Banner Melayang
                                </span>
                            @else
                                <span class="badge bg-light text-muted border" style="font-size: 0.70rem;">Notifikasi Kotak</span>
                            @endif
                        </td>
                        <td class="px-3 py-3">
                            <small class="d-block font-monospace" style="color: var(--nx-text); font-size: 0.74rem;">
                                {{ $n->starts_at ? \Carbon\Carbon::parse($n->starts_at)->format('d M Y') : 'Sekarang' }}
                                s/d
                                {{ $n->expires_at ? \Carbon\Carbon::parse($n->expires_at)->format('d M Y') : 'Permanen' }}
                            </small>
                            <small class="text-muted" style="font-size: 0.70rem;">Oleh: {{ $n->creator ? $n->creator->name : 'Super Admin' }}</small>
                        </td>
                        <td class="px-3 py-3 text-end">
                            <div class="d-flex align-items-center justify-content-end gap-1.5">
                                <button type="button" class="btn btn-sm btn-outline-primary px-2 py-1" style="border-radius: 6px; font-size: 0.74rem;" 
                                        data-bs-toggle="modal" data-bs-target="#modalEditPlatformNotification_{{ $n->id }}" title="Edit Siaran">
                                    <i class="fas fa-pen-to-square"></i>
                                </button>
                                <form action="{{ route('super_admin.settings.notifications.destroy', $n->id) }}" method="POST" class="m-0" onsubmit="return confirm('Apakah Anda yakin ingin menghapus siaran notifikasi ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1" style="border-radius: 6px; font-size: 0.74rem;" title="Hapus Siaran">
                                        <i class="fas fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <!-- MODAL EDIT NOTIFIKASI -->
                    <div class="modal fade" id="modalEditPlatformNotification_{{ $n->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
                                <div class="modal-header border-bottom" style="border-color: var(--nx-border) !important;">
                                    <h5 class="modal-title fw-bold fs-6" style="color: var(--nx-text);">
                                        <i class="fas fa-pen text-primary me-1"></i> Edit Siaran Platform
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('super_admin.settings.notifications.update', $n->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold" style="color: var(--nx-text);">Judul Siaran</label>
                                            <input type="text" name="title" class="form-control" value="{{ $n->title }}" required
                                                   style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold" style="color: var(--nx-text);">Isi Pesan / Pengumuman</label>
                                            <textarea name="message" rows="3" class="form-control" required
                                                      style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">{{ $n->message }}</textarea>
                                        </div>
                                        <div class="row g-2 mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold" style="color: var(--nx-text);">Tingkat Prioritas</label>
                                                <select name="type" class="form-select" style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                                                    <option value="info" {{ $n->type === 'info' ? 'selected' : '' }}>Info Standar</option>
                                                    <option value="warning" {{ $n->type === 'warning' ? 'selected' : '' }}>Penting (Warning)</option>
                                                    <option value="critical" {{ $n->type === 'critical' ? 'selected' : '' }}>Darurat (Critical)</option>
                                                    <option value="maintenance" {{ $n->type === 'maintenance' ? 'selected' : '' }}>Pemeliharaan Sistem</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold" style="color: var(--nx-text);">Target Jangkauan</label>
                                                <select name="target_scope" class="form-select" style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                                                    <option value="all" {{ $n->target_scope === 'all' ? 'selected' : '' }}>Seluruh Pengurus Platform</option>
                                                    <option value="rw" {{ $n->target_scope === 'rw' ? 'selected' : '' }}>Khusus Wilayah RW</option>
                                                    <option value="role" {{ $n->target_scope === 'role' ? 'selected' : '' }}>Khusus Peran Tertentu</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold" style="color: var(--nx-text);">Tanggal Berakhir Tayang</label>
                                            <input type="date" name="expires_at" class="form-control" 
                                                   value="{{ $n->expires_at ? \Carbon\Carbon::parse($n->expires_at)->format('Y-m-d') : '' }}"
                                                   style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="is_banner" value="1" id="switchBannerEdit_{{ $n->id }}" {{ $n->is_banner ? 'checked' : '' }}>
                                            <label class="form-check-label small fw-bold" for="switchBannerEdit_{{ $n->id }}" style="color: var(--nx-text);">
                                                Tampilkan Sebagai Banner Prioritas di Bagian Atas Aplikasi
                                            </label>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top" style="border-color: var(--nx-border) !important;">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary btn-sm px-3 fw-bold">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-bullhorn fa-2x mb-2 d-block opacity-25"></i>
                            <span>Belum ada siaran pengumuman sistem platform yang aktif.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($notifications->hasPages())
        <div class="p-3 border-top" style="border-color: var(--nx-border) !important;">
            {{ $notifications->appends(['tab' => 'notifications'])->links() }}
        </div>
    @endif
</div>

<!-- MODAL CREATE NOTIFIKASI -->
<div class="modal fade" id="modalCreatePlatformNotification" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
            <div class="modal-header border-bottom" style="border-color: var(--nx-border) !important;">
                <h5 class="modal-title fw-bold fs-6" style="color: var(--nx-text);">
                    <i class="fas fa-bullhorn text-success me-1"></i> Buat Siaran Notifikasi Platform
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('super_admin.settings.notifications.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold" style="color: var(--nx-text);">Judul Siaran</label>
                        <input type="text" name="title" class="form-control" placeholder="Contoh: Jadwal Pemeliharaan Server Database 26 Nov" required
                               style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold" style="color: var(--nx-text);">Isi Pesan / Pengumuman</label>
                        <textarea name="message" rows="3" class="form-control" placeholder="Tuliskan instruksi teknis atau pengumuman resmi bagi pengurus..." required
                                  style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);"></textarea>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold" style="color: var(--nx-text);">Tingkat Prioritas</label>
                            <select name="type" class="form-select" style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                                <option value="info">Info Standar</option>
                                <option value="warning">Penting (Warning)</option>
                                <option value="critical">Darurat (Critical)</option>
                                <option value="maintenance">Pemeliharaan Sistem</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold" style="color: var(--nx-text);">Target Jangkauan</label>
                            <select name="target_scope" class="form-select" style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                                <option value="all">Seluruh Pengurus Platform</option>
                                <option value="rw">Khusus Wilayah RW 018</option>
                                <option value="role">Khusus Admin RT</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold" style="color: var(--nx-text);">Masa Tayang Berakhir</label>
                        <input type="date" name="expires_at" class="form-control" 
                               value="{{ now()->addDays(7)->format('Y-m-d') }}"
                               style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_banner" value="1" id="switchBannerCreate" checked>
                        <label class="form-check-label small fw-bold" for="switchBannerCreate" style="color: var(--nx-text);">
                            Tampilkan Sebagai Banner Melayang di Header Pengurus
                        </label>
                    </div>
                </div>
                <div class="modal-footer border-top" style="border-color: var(--nx-border) !important;">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm px-3 fw-bold">Publikasikan Siaran</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- ========================================================================= -->
<!-- TAB 4: BACKUP & PEMULIHAN (DISASTER RECOVERY) -->
<!-- ========================================================================= -->
@if($activeTab === 'backup')
<!-- KPI STATS CADANGAN -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="sa-kpi-card p-3 rounded-3 border h-100" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
            <span class="small fw-semibold" style="color: var(--nx-muted);">Total Berkas Cadangan</span>
            <div class="d-flex align-items-baseline gap-2 mt-1">
                <h3 class="fs-4 fw-bold m-0" style="color: var(--nx-text);">{{ $backupFilesCount }}</h3>
                <span class="small" style="color: var(--nx-muted);">File Tersimpan</span>
            </div>
            <small class="text-success mt-1 d-block"><i class="fas fa-shield-alt me-1"></i>Penyimpanan Terisolasi Aman</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="sa-kpi-card p-3 rounded-3 border h-100" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
            <span class="small fw-semibold" style="color: var(--nx-muted);">Total Ukuran Storage</span>
            <div class="d-flex align-items-baseline gap-2 mt-1">
                <h3 class="fs-4 fw-bold m-0 font-monospace text-primary">{{ $totalBackupFormatted }}</h3>
            </div>
            <small class="text-muted mt-1 d-block">Direktori: <code style="font-size: 0.68rem;">storage/app/backups</code></small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="sa-kpi-card p-3 rounded-3 border h-100" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
            <span class="small fw-semibold" style="color: var(--nx-muted);">Cadangan Terakhir</span>
            <div class="d-flex align-items-baseline gap-2 mt-1">
                <h3 class="fs-6 fw-bold m-0 font-monospace" style="color: var(--nx-text);">
                    {{ $latestBackup ? $latestBackup->created_at->diffForHumans() : 'Belum Ada' }}
                </h3>
            </div>
            <small class="text-muted mt-1 d-block">{{ $latestBackup ? $latestBackup->filesize : '-' }}</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="sa-kpi-card p-3 rounded-3 border h-100" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
            <span class="small fw-semibold" style="color: var(--nx-muted);">Mesin Database</span>
            <div class="d-flex align-items-baseline gap-2 mt-1">
                <h3 class="fs-4 fw-bold m-0" style="color: var(--nx-forest);">SQLite 3</h3>
            </div>
            <small class="text-success mt-1 d-block"><i class="fas fa-circle-check me-1"></i>Self-Contained &amp; Hot-Copyable</small>
        </div>
    </div>
</div>

<!-- BAR AKSI CADANGAN & JADWAL OTOMATIS -->
<div class="p-3 rounded-3 border mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
    <div class="d-flex align-items-center gap-3">
        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px; background: rgba(40, 89, 67, 0.12); color: var(--nx-forest);">
            <i class="fas fa-database fs-5"></i>
        </div>
        <div>
            <h5 class="fw-bold m-0 fs-6" style="color: var(--nx-text);">Pencadangan Instan &amp; Pemulihan Bencana (Disaster Recovery)</h5>
            <small style="color: var(--nx-muted);">Jadwal Otomatis: <strong>{{ strtoupper($settings['backup_schedule_frequency'] ?? 'DAILY') }}</strong> jam <strong>{{ $settings['backup_schedule_time'] ?? '02:00' }} WIB</strong> (Retensi: {{ $settings['backup_retention_days'] ?? '30' }} Hari)</small>
        </div>
    </div>

    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2" style="border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#modalScheduleBackup">
            <i class="fas fa-calendar-gear"></i>
            <span>Konfigurasi Jadwal</span>
        </button>

        <form action="{{ route('super_admin.settings.backup.create') }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="btn btn-sm btn-success px-3 py-2 fw-bold d-inline-flex align-items-center gap-2 shadow-sm" style="border-radius: 8px;">
                <i class="fas fa-plus-circle"></i>
                <span>Buat Cadangan Manual Sekarang</span>
            </button>
        </form>
    </div>
</div>

<!-- TABEL BERKAS CADANGAN -->
<div class="sa-card p-0 overflow-hidden rounded-3 border mb-4" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
    <div class="table-responsive">
        <table class="table align-middle m-0" style="color: var(--nx-text);">
            <thead style="background: var(--nx-bg); border-bottom: 1px solid var(--nx-border);">
                <tr>
                    <th class="py-3 px-3 small fw-bold" style="color: var(--nx-muted);">NAMA BERKAS CADANGAN</th>
                    <th class="py-3 px-3 small fw-bold" style="color: var(--nx-muted);">UKURAN</th>
                    <th class="py-3 px-3 small fw-bold" style="color: var(--nx-muted);">TIPE &amp; INTEGRITAS (SHA-256)</th>
                    <th class="py-3 px-3 small fw-bold" style="color: var(--nx-muted);">PEMBUAT</th>
                    <th class="py-3 px-3 small fw-bold" style="color: var(--nx-muted);">TANGGAL BUAT</th>
                    <th class="py-3 px-3 small fw-bold text-end" style="color: var(--nx-muted);">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($backups as $b)
                    @php
                        $isSafetySnapshot = ($b->type === 'safety_snapshot');
                    @endphp
                    <tr style="border-bottom: 1px solid var(--nx-border);">
                        <td class="px-3 py-3">
                            <strong class="d-block small font-monospace" style="color: var(--nx-text);">
                                <i class="fas {{ $isSafetySnapshot ? 'fa-shield-halved text-warning' : 'fa-file-zipper text-success' }} me-1"></i>
                                {{ $b->filename }}
                            </strong>
                            <small class="text-muted d-block">{{ $b->notes ?: 'Cadangan sistem' }}</small>
                        </td>
                        <td class="px-3 py-3">
                            <span class="badge bg-light text-dark border font-monospace" style="font-size: 0.72rem;">{{ $b->filesize }}</span>
                        </td>
                        <td class="px-3 py-3">
                            <div class="d-flex flex-column gap-1">
                                <span class="badge {{ $isSafetySnapshot ? 'bg-warning-subtle text-warning' : 'bg-success-subtle text-success' }} align-self-start font-monospace" style="font-size: 0.68rem;">
                                    {{ strtoupper($b->type) }}
                                </span>
                                @if($b->checksum)
                                    <span class="badge font-monospace text-truncate" style="max-width: 180px; font-size: 0.64rem; background: var(--nx-bg); color: var(--nx-muted); border: 1px solid var(--nx-border);" title="SHA-256: {{ $b->checksum }}">
                                        SHA: {{ substr($b->checksum, 0, 16) }}...
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-3 py-3">
                            <small class="fw-semibold" style="color: var(--nx-text);">
                                {{ $b->creator ? $b->creator->name : 'System Scheduler' }}
                            </small>
                        </td>
                        <td class="px-3 py-3 font-monospace small" style="color: var(--nx-muted); font-size: 0.76rem;">
                            {{ $b->created_at ? $b->created_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') . ' WIB' : '-' }}
                        </td>
                        <td class="px-3 py-3 text-end">
                            <div class="d-flex align-items-center justify-content-end gap-1.5">
                                <!-- Tombol Unduh -->
                                <a href="{{ route('super_admin.settings.backup.download', $b->id) }}" class="btn btn-sm btn-outline-primary px-2.5 py-1" style="border-radius: 6px; font-size: 0.74rem;" title="Unduh arsip cadangan ke komputer lokal">
                                    <i class="fas fa-download"></i>
                                    <span class="d-none d-md-inline ms-1">Unduh</span>
                                </a>

                                <!-- Tombol Pulihkan (Buka Modal) -->
                                <button type="button" class="btn btn-sm btn-outline-danger px-2.5 py-1" style="border-radius: 6px; font-size: 0.74rem;" 
                                        data-bs-toggle="modal" data-bs-target="#modalRestoreBackup_{{ $b->id }}" title="Pulihkan database dari arsip ini">
                                    <i class="fas fa-rotate-left"></i>
                                    <span class="d-none d-md-inline ms-1">Pulihkan</span>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- MODAL KONFIRMASI RESTORE (HIGH SECURITY PROTECTED) -->
                    <div class="modal fade" id="modalRestoreBackup_{{ $b->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border border-danger-subtle" style="background: var(--nx-card-bg); border-color: rgba(239, 68, 68, 0.4) !important;">
                                <div class="modal-header border-bottom border-danger-subtle bg-danger-subtle">
                                    <h5 class="modal-title fw-bold fs-6 text-danger d-flex align-items-center gap-2">
                                        <i class="fas fa-triangle-exclamation"></i>
                                        <span>Konfirmasi Pemulihan Database</span>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('super_admin.settings.backup.restore', $b->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="p-3 rounded-3 border border-danger-subtle bg-danger-subtle mb-3">
                                            <p class="m-0 small text-danger fw-semibold" style="line-height: 1.45;">
                                                <i class="fas fa-radiation me-1"></i> <strong>PERINGATAN RISIKO TINGGI:</strong><br>
                                                Pemulihan akan menimpa seluruh data sistem saat ini dengan kondisi saat file cadangan <code>{{ $b->filename }}</code> dibuat ({{ $b->created_at->format('d M Y, H:i') }}).
                                            </p>
                                        </div>

                                        <div class="p-2.5 rounded-3 border mb-3" style="background: var(--nx-bg); border-color: var(--nx-border) !important; font-size: 0.76rem;">
                                            <i class="fas fa-shield-halved text-success me-1"></i>
                                            <strong>Prosedur Keselamatan Otomatis:</strong> Sistem secara otomatis akan membuat snapshot pengaman darurat (pre-restore snapshot) dari database aktif sebelum pemulihan dijalankan.
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label small fw-bold text-danger">Ketik "PULIHKAN" untuk konfirmasi eksekusi:</label>
                                            <input type="text" name="confirm_restore" class="form-control font-monospace" placeholder="PULIHKAN" required
                                                   style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label small fw-bold" style="color: var(--nx-text);">Kata Sandi Super Admin Saat Ini:</label>
                                            <input type="password" name="password" class="form-control" placeholder="Masukkan kata sandi login Anda..." required
                                                   style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                                            <small class="text-muted" style="font-size: 0.72rem;">Wajib memasukkan kata sandi Super Admin untuk otentikasi tindakan berisiko tinggi.</small>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top" style="border-color: var(--nx-border) !important;">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batalkan</button>
                                        <button type="submit" class="btn btn-danger btn-sm px-4 fw-bold">Verifikasi &amp; Pulihkan Sekarang</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-database fa-2x mb-2 d-block opacity-25"></i>
                            <span>Belum ada arsip cadangan database yang tersimpan.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($backups->hasPages())
        <div class="p-3 border-top" style="border-color: var(--nx-border) !important;">
            {{ $backups->appends(['tab' => 'backup'])->links() }}
        </div>
    @endif
</div>

<!-- MODAL KONFIGURASI JADWAL CADANGAN OTOMATIS -->
<div class="modal fade" id="modalScheduleBackup" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
            <div class="modal-header border-bottom" style="border-color: var(--nx-border) !important;">
                <h5 class="modal-title fw-bold fs-6" style="color: var(--nx-text);">
                    <i class="fas fa-calendar-clock text-primary me-1"></i> Pengaturan Jadwal Cadangan Otomatis
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('super_admin.settings.backup.schedule') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold" style="color: var(--nx-text);">Frekuensi Pencadangan</label>
                        <select name="backup_schedule_frequency" class="form-select" style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                            <option value="daily" {{ ($settings['backup_schedule_frequency'] ?? 'daily') === 'daily' ? 'selected' : '' }}>Harian (Setiap Hari)</option>
                            <option value="weekly" {{ ($settings['backup_schedule_frequency'] ?? '') === 'weekly' ? 'selected' : '' }}>Mingguan (Setiap Minggu)</option>
                            <option value="disabled" {{ ($settings['backup_schedule_frequency'] ?? '') === 'disabled' ? 'selected' : '' }}>Nonaktifkan Jadwal Otomatis</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold" style="color: var(--nx-text);">Waktu Eksekusi (Jam WIB)</label>
                        <input type="text" name="backup_schedule_time" class="form-control font-monospace" 
                               value="{{ $settings['backup_schedule_time'] ?? '02:00' }}" placeholder="02:00" required
                               style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                        <small class="text-muted" style="font-size: 0.72rem;">Dijalankan via cron scheduler Laravel (<code>php artisan schedule:run</code>).</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold" style="color: var(--nx-text);">Masa Retensi Arsip (Hari)</label>
                        <input type="number" name="backup_retention_days" class="form-control font-monospace" 
                               value="{{ $settings['backup_retention_days'] ?? '30' }}" min="1" max="365" required
                               style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                        <small class="text-muted" style="font-size: 0.72rem;">File cadangan yang lebih tua dari retensi ini akan dihapus secara otomatis.</small>
                    </div>
                </div>
                <div class="modal-footer border-top" style="border-color: var(--nx-border) !important;">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-3 fw-bold">Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- ========================================================================= -->
<!-- TAB 5: KEAMANAN KONFIGURASI & AUDIT TRAIL -->
<!-- ========================================================================= -->
@if($activeTab === 'security')
<div class="row g-4 mb-4">
    <!-- FORM PARAMETER KEAMANAN -->
    <div class="col-lg-6">
        <div class="sa-card p-4 rounded-3 border h-100" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
            <div class="border-bottom pb-3 mb-4" style="border-color: var(--nx-border) !important;">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(239, 68, 68, 0.12); color: #EF4444;">
                        <i class="fas fa-shield-halved"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold m-0 fs-6" style="color: var(--nx-text);">Kebijakan Keamanan &amp; Sesi Platform</h5>
                        <small style="color: var(--nx-muted);">Ambang batas login, lockout, batas wilayah, dan proteksi sesi sensitif</small>
                    </div>
                </div>
            </div>

            <form action="{{ route('super_admin.settings.security.update') }}" method="POST">
                @csrf

                <!-- Parameter Manajemen Sesi & Token -->
                <div class="p-3 rounded-3 border mb-3" style="background: rgba(40, 89, 67, 0.05); border-color: rgba(40, 89, 67, 0.2) !important;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="fas fa-stopwatch text-success"></i>
                        <strong class="small" style="color: var(--nx-text);">Kebijakan Durasi Sesi &amp; Token (Session Management)</strong>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold" style="color: var(--nx-text); font-size: 0.76rem;">Idle Timeout / Inaktivitas (Menit)</label>
                            <input type="number" name="session_idle_timeout_minutes" class="form-control form-control-sm font-monospace" 
                                   value="{{ $settings['session_idle_timeout_minutes'] ?? '30' }}" min="5" max="1440" required
                                   style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                            <small class="text-muted" style="font-size: 0.70rem;">Logout otomatis jika pengguna tidak aktif (Default: 30 menit).</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold" style="color: var(--nx-text); font-size: 0.76rem;">Absolute Timeout / Batas Sesi (Menit)</label>
                            <input type="number" name="session_absolute_timeout_minutes" class="form-control form-control-sm font-monospace" 
                                   value="{{ $settings['session_absolute_timeout_minutes'] ?? '60' }}" min="15" max="1440" required
                                   style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                            <small class="text-muted" style="font-size: 0.70rem;">Batas mutlak durasi sesi sejak login awal (Default: 60 menit).</small>
                        </div>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold" style="color: var(--nx-text); font-size: 0.76rem;">Masa Berlaku Access Token (Menit)</label>
                            <input type="number" name="api_token_lifetime_minutes" class="form-control form-control-sm font-monospace" 
                                   value="{{ $settings['api_token_lifetime_minutes'] ?? '15' }}" min="5" max="120" required
                                   style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                            <small class="text-muted" style="font-size: 0.70rem;">Kedaluwarsa Access Token REST API (Default: 15 menit).</small>
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="form-check form-switch pt-3">
                                <input class="form-check-input" type="checkbox" name="allow_remember_me" value="1" id="switchRememberMe" {{ ($settings['allow_remember_me'] ?? '1') === '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold small" for="switchRememberMe" style="color: var(--nx-text); font-size: 0.76rem;">
                                    Izinkan Fitur "Ingat Saya" (Warga)
                                </label>
                                <small class="text-muted d-block" style="font-size: 0.68rem;">Tetap dibatasi idle timeout 30 menit dan batas mutlak 60 menit.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold" style="color: var(--nx-text);">Batas Waktu Sesi Buka Sensor NIK (Menit)</label>
                    <input type="number" name="sensitive_data_auth_timeout_minutes" class="form-control" 
                           value="{{ $settings['sensitive_data_auth_timeout_minutes'] ?? '15' }}" min="1" max="120" required
                           style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                    <small class="text-muted" style="font-size: 0.72rem;">Sesi akses data sensor NIK/kontak warga akan otomatis terkunci kembali setelah durasi ini.</small>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold" style="color: var(--nx-text);">Maksimal Login Gagal</label>
                        <input type="number" name="max_login_attempts" class="form-control" 
                               value="{{ $settings['max_login_attempts'] ?? '5' }}" min="3" max="20" required
                               style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                        <small class="text-muted" style="font-size: 0.72rem;">Ambang batas sebelum akun ditangguhkan sementara.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold" style="color: var(--nx-text);">Durasi Lockout (Menit)</label>
                        <input type="number" name="lockout_duration_minutes" class="form-control" 
                               value="{{ $settings['lockout_duration_minutes'] ?? '15' }}" min="1" max="60" required
                               style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                        <small class="text-muted" style="font-size: 0.72rem;">Waktu tunggu bagi user setelah melewati ambang batas.</small>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold" style="color: var(--nx-text);">Maksimal Rukun Tetangga (RT) per RW</label>
                    <input type="number" name="max_rt_per_rw" class="form-control" 
                           value="{{ $settings['max_rt_per_rw'] ?? '25' }}" min="1" max="100" required
                           style="background: var(--nx-bg); color: var(--nx-text); border-color: var(--nx-border);">
                    <small class="text-muted" style="font-size: 0.72rem;">Batas struktur administrasi per wilayah RW (Default: 25 RT).</small>
                </div>

                <div class="p-3 rounded-3 border mb-4" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                    <div class="form-check form-switch m-0">
                        <input class="form-check-input" type="checkbox" name="allow_self_rw_registration" value="1" id="switchSelfRw" {{ ($settings['allow_self_rw_registration'] ?? '1') === '1' ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold small" for="switchSelfRw" style="color: var(--nx-text);">
                            Izinkan Pendaftaran / Pengajuan Mandiri RW Baru
                        </label>
                    </div>
                    <small class="text-muted d-block mt-1" style="font-size: 0.74rem;">
                        Jika dinonaktifkan, pembuatan wilayah RW baru hanya dapat dilakukan langsung oleh Super Admin HQ.
                    </small>
                </div>

                <div class="d-flex justify-content-end pt-3 border-top" style="border-color: var(--nx-border) !important;">
                    <button type="submit" class="btn btn-primary px-4 py-2 fw-bold d-inline-flex align-items-center gap-2 shadow-sm" style="border-radius: 8px;">
                        <i class="fas fa-shield"></i>
                        <span>Simpan Kebijakan Keamanan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- PANDUAN KEAMANAN & PRINSIP TATA KELOLA -->
    <div class="col-lg-6">
        <div class="sa-card p-4 rounded-3 border h-100" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
            <div class="border-bottom pb-3 mb-3" style="border-color: var(--nx-border) !important;">
                <h5 class="fw-bold m-0 fs-6" style="color: var(--nx-text);">
                    <i class="fas fa-lock text-success me-1"></i> Panduan Tata Kelola &amp; Akses Super Admin
                </h5>
                <small style="color: var(--nx-muted);">Kewenangan dan batasan Super Admin platform</small>
            </div>

            <div class="d-flex flex-column gap-3" style="font-size: 0.82rem; line-height: 1.5;">
                <div class="p-3 rounded-3 border" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                    <strong class="d-block text-success mb-1"><i class="fas fa-circle-check me-1"></i> Super Admin HQ Memiliki Kewenangan:</strong>
                    <ul class="m-0 ps-3" style="color: var(--nx-muted);">
                        <li>Mengatur konfigurasi global dan arsitektur platform.</li>
                        <li>Mengaktifkan dan membatasi modul secara aman (non-destruktif).</li>
                        <li>Menyiarkan pengumuman sistem, kebijakan, dan jadwal pemeliharaan.</li>
                        <li>Membuat cadangan (backup) dan pemulihan darurat (restore).</li>
                        <li>Memantau audit trail seluruh aktivitas konfigurasi platform.</li>
                    </ul>
                </div>

                <div class="p-3 rounded-3 border" style="background: var(--nx-bg); border-color: var(--nx-border) !important;">
                    <strong class="d-block text-danger mb-1"><i class="fas fa-circle-xmark me-1"></i> Super Admin HQ DILARANG:</strong>
                    <ul class="m-0 ps-3" style="color: var(--nx-muted);">
                        <li>Mengubah atau mengambil alih transaksi kas operasional RW 018.</li>
                        <li>Memanipulasi nominal iuran warga dan data kependudukan pribadi.</li>
                        <li>Menerbitkan surat pengantar RT/RW secara sepihak.</li>
                    </ul>
                </div>

                <div class="d-flex align-items-center justify-content-between pt-2">
                    <span class="text-muted small">Kunjungi Log Buka Sensor NIK:</span>
                    <a href="{{ route('super_admin.sensitive_logs') }}" class="btn btn-sm btn-outline-secondary px-3 py-1" style="border-radius: 6px; font-size: 0.74rem;">
                        <i class="fas fa-user-shield me-1"></i> Log NIK
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- AUDIT TRAIL LOG KONFIGURASI PLATFORM -->
<div class="sa-card p-0 overflow-hidden rounded-3 border mb-4" style="background: var(--nx-card-bg); border-color: var(--nx-border) !important;">
    <div class="p-3 border-bottom d-flex align-items-center justify-content-between" style="border-color: var(--nx-border) !important;">
        <h5 class="fw-bold m-0 fs-6" style="color: var(--nx-text);">
            <i class="fas fa-clock-rotate-left text-primary me-1"></i> Log Audit Perubahan Konfigurasi Platform
        </h5>
        <a href="{{ route('super_admin.audit.index') }}" class="btn btn-sm btn-link text-decoration-none p-0 fw-bold" style="color: var(--nx-forest); font-size: 0.78rem;">
            Lihat Semua Log Audit Platform &rarr;
        </a>
    </div>

    <div class="table-responsive">
        <table class="table align-middle m-0" style="color: var(--nx-text);">
            <thead style="background: var(--nx-bg); border-bottom: 1px solid var(--nx-border);">
                <tr>
                    <th class="py-2.5 px-3 small fw-bold" style="color: var(--nx-muted);">ID LOG</th>
                    <th class="py-2.5 px-3 small fw-bold" style="color: var(--nx-muted);">WAKTU</th>
                    <th class="py-2.5 px-3 small fw-bold" style="color: var(--nx-muted);">AKTOR</th>
                    <th class="py-2.5 px-3 small fw-bold" style="color: var(--nx-muted);">EVENT / AKSI</th>
                    <th class="py-2.5 px-3 small fw-bold" style="color: var(--nx-muted);">DESKRIPSI PERUBAHAN</th>
                    <th class="py-2.5 px-3 small fw-bold text-end" style="color: var(--nx-muted);">IP ADDRESS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($auditLogs as $log)
                    <tr style="border-bottom: 1px solid var(--nx-border); font-size: 0.8rem;">
                        <td class="px-3 py-2.5 font-monospace fw-bold text-primary" style="font-size: 0.74rem;">
                            {{ $log->formatted_id }}
                        </td>
                        <td class="px-3 py-2.5 font-monospace text-muted" style="font-size: 0.74rem;">
                            {{ $log->created_at ? $log->created_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i:s') . ' WIB' : '-' }}
                        </td>
                        <td class="px-3 py-2.5">
                            <strong style="color: var(--nx-text);">{{ $log->user_name }}</strong>
                            <span class="badge bg-light text-muted border font-monospace ms-1" style="font-size: 0.64rem;">{{ strtoupper($log->role) }}</span>
                        </td>
                        <td class="px-3 py-2.5 font-monospace">
                            <span class="badge bg-secondary-subtle text-secondary sa-audit-event-badge" style="font-size: 0.68rem;">{{ $log->event ?: ($log->action_type ?: $log->action) }}</span>
                        </td>
                        <td class="px-3 py-2.5 text-muted" style="font-size: 0.78rem;">
                            {{ $log->description }}
                        </td>
                        <td class="px-3 py-2.5 text-end font-monospace text-muted" style="font-size: 0.72rem;">
                            {{ $log->ip_address ?: '127.0.0.1' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted small">
                            <i class="fas fa-history fa-2x mb-2 d-block opacity-50"></i>
                            Belum ada riwayat perubahan konfigurasi platform yang tercatat.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($auditLogs->hasPages())
        <div class="p-3 border-top" style="border-color: var(--nx-border) !important;">
            {{ $auditLogs->appends(['tab' => 'security'])->links() }}
        </div>
    @endif
</div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Filter Modul Berdasarkan Kategori di Tab 2
    const filterButtons = document.querySelectorAll('.module-filter-btn');
    const moduleCards = document.querySelectorAll('.module-card-item');

    filterButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            filterButtons.forEach(b => {
                b.classList.remove('active', 'btn-secondary');
                b.classList.add('btn-outline-secondary');
            });
            this.classList.add('active', 'btn-secondary');
            this.classList.remove('btn-outline-secondary');

            const category = this.getAttribute('data-category');

            moduleCards.forEach(card => {
                if (category === 'all' || card.getAttribute('data-category') === category) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});
</script>
@endpush
@endsection
