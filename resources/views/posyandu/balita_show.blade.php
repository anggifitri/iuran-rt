@extends('layouts.app')

@section('title', 'KMS Digital & Profil Balita: ' . $anak->nama_anak . ' — NexaNest')

@section('content')
@php
    $gender = strtoupper($anak->jenis_kelamin ?? 'L');
    $genderIcon = $gender === 'P' ? 'fa-venus text-danger' : 'fa-mars text-primary';
    $statusBadge = $anak->status_badge;
@endphp

<!-- ==================== TOMBOL KEMBALI & HEADER ==================== -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ route('posyandu.index') }}" class="btn btn-outline-secondary btn-sm px-3 py-1.5" style="border-radius: 10px;">
        <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard Posyandu
    </a>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary btn-sm px-3 py-1.5" onclick="window.print()" style="border-radius: 10px;">
            <i class="fas fa-print me-1"></i> Cetak Rekam KMS
        </button>
        @if($isOfficer)
            <button type="button" class="btn btn-primary btn-sm px-3 py-1.5" data-bs-toggle="modal" data-bs-target="#modalCatatPemeriksaan" style="border-radius: 10px;">
                <i class="fas fa-plus-circle me-1"></i> Catat Pemeriksaan Baru
            </button>
        @endif
    </div>
</div>

<!-- ==================== PROFILE HERO BALITA ==================== -->
<div class="card p-4 mb-4 border-0 shadow-sm position-relative overflow-hidden"
     style="background: linear-gradient(135deg, #FFFFFF 0%, #F4F9F5 60%, #E8F3EE 100%); border-radius: 20px; border: 1px solid var(--nx-border) !important;">
    <div class="row align-items-center g-3">
        <div class="col-lg-8">
            <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                <span class="badge rounded-pill px-3 py-1" style="background: var(--nx-forest); color: #fff; font-size: 0.78rem;">
                    <i class="fas fa-baby me-1"></i> Balita Posyandu RW 018
                </span>
                <span class="badge rounded-pill px-2.5 py-1" style="background: var(--nx-bg); color: var(--nx-forest); border: 1px solid var(--nx-border); font-size: 0.75rem;">
                    RT {{ $anak->rt_number ?? '007' }}
                </span>
                <span class="badge rounded-pill px-2.5 py-1" style="background: {{ $statusBadge['bg'] }}; color: {{ $statusBadge['color'] }}; font-size: 0.75rem; font-weight: 700;">
                    <i class="fas {{ $statusBadge['icon'] }} me-1"></i>{{ $statusBadge['label'] }}
                </span>
            </div>

            <h2 class="fw-bold mb-1" style="color: var(--nx-forest); font-size: 1.65rem;">
                {{ $anak->nama_anak }}
                <small class="fs-6 fw-normal text-muted ms-1"><i class="fas {{ $genderIcon }}"></i> ({{ $anak->jenis_kelamin_teks }})</small>
            </h2>

            <div class="d-flex flex-wrap gap-3 text-muted small mb-3">
                <span><i class="far fa-calendar-alt text-success me-1"></i>Lahir: <strong>{{ $anak->tanggal_lahir ? $anak->tanggal_lahir->translatedFormat('d F Y') : '-' }}</strong> ({{ $anak->usia_teks }})</span>
                <span><i class="fas fa-id-card text-success me-1"></i>NIK: {{ $anak->nik ?? '-' }}</span>
                <span><i class="fas fa-droplet text-danger me-1"></i>Golongan Darah: <strong>{{ $anak->golongan_darah ?? '-' }}</strong></span>
            </div>

            <div class="p-3 rounded-3 bg-white border small text-muted d-flex flex-wrap gap-4">
                <div><i class="fas fa-user-nurse text-success me-1"></i>Ibu Kandung: <strong class="text-dark">{{ $anak->nama_ibu ?? 'Ibu Warga' }}</strong></div>
                <div><i class="fas fa-user-shield text-success me-1"></i>Ayah: <strong class="text-dark">{{ $anak->nama_ayah ?? 'Bpk. Warga' }}</strong></div>
                <div><i class="fas fa-location-dot text-success me-1"></i>Alamat: <strong class="text-dark">{{ $anak->alamat ?? 'Lingkungan RW 018' }}</strong></div>
                <div><i class="fas fa-phone text-success me-1"></i>Kontak: <strong class="text-dark">{{ $anak->no_telepon ?? '-' }}</strong></div>
            </div>
        </div>

        <div class="col-lg-4 text-lg-end">
            <div class="p-3 rounded-4 bg-white border text-center shadow-sm d-inline-block w-100" style="max-width: 320px;">
                <span class="d-block text-muted small mb-1">Hasil Pengukuran Terkini</span>
                <div class="row g-2 text-center my-2">
                    <div class="col-4">
                        <span class="d-block text-muted" style="font-size: 0.7rem;">Berat</span>
                        <h4 class="fw-bold mb-0" style="color: var(--nx-forest);">{{ $anak->berat_badan }}<small class="fs-6 text-muted">kg</small></h4>
                    </div>
                    <div class="col-4">
                        <span class="d-block text-muted" style="font-size: 0.7rem;">Tinggi</span>
                        <h4 class="fw-bold mb-0 text-primary">{{ $anak->tinggi_badan }}<small class="fs-6 text-muted">cm</small></h4>
                    </div>
                    <div class="col-4">
                        <span class="d-block text-muted" style="font-size: 0.7rem;">Usia</span>
                        <h4 class="fw-bold mb-0 text-dark">{{ $anak->umur_bulan }}<small class="fs-6 text-muted">bln</small></h4>
                    </div>
                </div>
                <div class="pt-2 border-top small text-muted">
                    Lahir: {{ $anak->berat_lahir ?? '-' }} kg / {{ $anak->tinggi_lahir ?? '-' }} cm
                </div>
            </div>

            @if($isOfficer)
            <div class="mt-2 text-end">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalEditBalita">
                    <i class="fas fa-pencil me-1"></i> Edit Data Identitas
                </button>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- ==================== BANNER EVALUASI ANTROPOMETRI WHO ==================== -->
@if($latestPem)
<div class="card p-3.5 mb-4 border-0 shadow-sm" style="background: #FFFFFF; border-radius: 16px; border: 1px solid var(--nx-border) !important;">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <h6 class="fw-bold mb-0" style="color: var(--nx-forest); font-size: 0.95rem;">
            <i class="fas fa-stethoscope me-1"></i> Status Pertumbuhan Antropometri Menurut Standar Baku WHO / Kemenkes RI
        </h6>
        <span class="badge rounded-pill px-3 py-1 font-monospace" style="background: var(--nx-bg); color: var(--nx-forest); border: 1px solid var(--nx-border); font-size: 0.72rem;">
            Pemeriksaan: {{ $latestPem->tanggal_pemeriksaan->translatedFormat('d F Y') }}
        </span>
    </div>

    <div class="row g-3">
        <!-- 1. BB/U -->
        <div class="col-md-3 col-sm-6">
            <div class="p-3 rounded-3 h-100" style="background: var(--nx-bg); border-left: 4px solid var(--nx-forest);">
                <small class="text-muted d-block fw-bold" style="font-size: 0.7rem;">1. BB/U (BERAT / UMUR)</small>
                <strong class="d-block text-dark mb-1" style="font-size: 0.92rem;">{{ $latestPem->status_bb_u }}</strong>
                <p class="mb-0 text-muted" style="font-size: 0.75rem;">Indikator mendeteksi berat badan sangat kurang / kurang / normal.</p>
            </div>
        </div>

        <!-- 2. TB/U -->
        <div class="col-md-3 col-sm-6">
            <div class="p-3 rounded-3 h-100" style="background: var(--nx-bg); border-left: 4px solid {{ str_contains(strtolower($latestPem->status_tb_u), 'pendek') ? 'var(--nx-danger)' : '#10B981' }};">
                <small class="text-muted d-block fw-bold" style="font-size: 0.7rem;">2. TB/U (TINGGI / UMUR)</small>
                <strong class="d-block {{ str_contains(strtolower($latestPem->status_tb_u), 'pendek') ? 'text-danger' : 'text-dark' }} mb-1" style="font-size: 0.92rem;">
                    {{ $latestPem->status_tb_u }}
                </strong>
                <p class="mb-0 text-muted" style="font-size: 0.75rem;">Indikator utama deteksi stunting / perawakan tubuh kronis.</p>
            </div>
        </div>

        <!-- 3. BB/TB -->
        <div class="col-md-3 col-sm-6">
            <div class="p-3 rounded-3 h-100" style="background: var(--nx-bg); border-left: 4px solid #3B82F6;">
                <small class="text-muted d-block fw-bold" style="font-size: 0.7rem;">3. BB/TB (BERAT / TINGGI)</small>
                <strong class="d-block text-dark mb-1" style="font-size: 0.92rem;">{{ $latestPem->status_bb_tb }}</strong>
                <p class="mb-0 text-muted" style="font-size: 0.75rem;">Indikator wasting (kekurusan akut atau gizi lebih/obesitas).</p>
            </div>
        </div>

        <!-- 4. IMT/U -->
        <div class="col-md-3 col-sm-6">
            <div class="p-3 rounded-3 h-100" style="background: var(--nx-bg); border-left: 4px solid #6366F1;">
                <small class="text-muted d-block fw-bold" style="font-size: 0.7rem;">4. IMT/U (INDEKS MASSA TUBUH)</small>
                <strong class="d-block text-dark mb-1" style="font-size: 0.92rem;">{{ $latestPem->status_imt_u }}</strong>
                <p class="mb-0 text-muted" style="font-size: 0.75rem;">Skor proporsi massa tubuh terhadap kuadrat tinggi badan.</p>
            </div>
        </div>
    </div>

    @if($anak->solusi || $latestPem->catatan_petugas)
    <div class="mt-3 p-3 rounded-3 bg-light border">
        <small class="fw-bold d-block mb-1" style="color: var(--nx-forest);">
            <i class="fas fa-lightbulb me-1"></i> Catatan Tindak Lanjut &amp; Rekomendasi Gizi Petugas Kesehatan:
        </small>
        <p class="mb-0 small text-muted">{{ $latestPem->catatan_petugas ?: $anak->solusi }}</p>
    </div>
    @endif
</div>
@endif

<!-- ==================== 5 TAB UTAMA REKAM MEDIS ==================== -->
<div class="card p-2 mb-4 border-0 shadow-sm" style="background: var(--nx-bg); border-radius: 14px;">
    <ul class="nav nav-pills nav-fill gap-1" id="kmsTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link py-2.5 px-3 fw-bold d-flex align-items-center justify-content-center gap-2 active"
                    id="tab-btn-kms" data-bs-toggle="pill" data-bs-target="#tab-kms" type="button" role="tab"
                    style="border-radius: 10px; font-size: 0.88rem;">
                <i class="fas fa-chart-area"></i>
                <span>1. Kurva KMS Digital (WHO)</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link py-2.5 px-3 fw-bold d-flex align-items-center justify-content-center gap-2"
                    id="tab-btn-pemeriksaan" data-bs-toggle="pill" data-bs-target="#tab-pemeriksaan" type="button" role="tab"
                    style="border-radius: 10px; font-size: 0.88rem;">
                <i class="fas fa-clipboard-check"></i>
                <span>2. Riwayat Pemeriksaan ({{ $pemeriksaansAsc->count() }})</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link py-2.5 px-3 fw-bold d-flex align-items-center justify-content-center gap-2"
                    id="tab-btn-imunisasi" data-bs-toggle="pill" data-bs-target="#tab-imunisasi" type="button" role="tab"
                    style="border-radius: 10px; font-size: 0.88rem;">
                <i class="fas fa-syringe"></i>
                <span>3. Riwayat &amp; Jadwal Imunisasi</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link py-2.5 px-3 fw-bold d-flex align-items-center justify-content-center gap-2"
                    id="tab-btn-identitas" data-bs-toggle="pill" data-bs-target="#tab-identitas" type="button" role="tab"
                    style="border-radius: 10px; font-size: 0.88rem;">
                <i class="fas fa-address-card"></i>
                <span>4. Identitas &amp; Keluarga</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link py-2.5 px-3 fw-bold d-flex align-items-center justify-content-center gap-2"
                    id="tab-btn-audit" data-bs-toggle="pill" data-bs-target="#tab-audit" type="button" role="tab"
                    style="border-radius: 10px; font-size: 0.88rem;">
                <i class="fas fa-shield-halved"></i>
                <span>5. Catatan &amp; Audit Log</span>
            </button>
        </li>
    </ul>
</div>

<div class="tab-content" id="kmsTabContent">
    <!-- ========================================================================= -->
    <!-- TAB 1: KURVA KMS DIGITAL (WHO)                                            -->
    <!-- ========================================================================= -->
    <div class="tab-pane fade show active" id="tab-kms" role="tabpanel">
        <div class="card p-4 mb-4 border-0 shadow-sm" style="border-radius: 18px;">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div>
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.05rem;">
                        <i class="fas fa-chart-line text-success me-2"></i>Kartu Menuju Sehat (KMS) Digital Interaktif
                    </h5>
                    <small class="text-muted">Kurva acuan standar pertumbuhan WHO (0 - 60 Bulan) khusus anak {{ $anak->jenis_kelamin_teks }}</small>
                </div>

                <!-- Parameter Switcher Pills -->
                <div class="btn-group btn-group-sm" role="group" id="kmsChartSwitcher">
                    <button type="button" class="btn btn-outline-primary active" onclick="switchKmsChart('bb_u', this)">
                        BB / Umur (kg)
                    </button>
                    <button type="button" class="btn btn-outline-primary" onclick="switchKmsChart('tb_u', this)">
                        TB / Umur (cm)
                    </button>
                    <button type="button" class="btn btn-outline-primary" onclick="switchKmsChart('imt_u', this)">
                        IMT / Umur
                    </button>
                </div>
            </div>

            <!-- Legend Info -->
            <div class="p-2.5 rounded-3 mb-3 d-flex flex-wrap align-items-center justify-content-between gap-2"
                 style="background: var(--nx-bg); font-size: 0.75rem;">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <span><i class="fas fa-circle text-success me-1"></i><strong>Titik Balita:</strong> {{ $anak->nama_anak }}</span>
                    <span><i class="fas fa-minus text-success me-1"></i><strong>Median (0 SD):</strong> Garis Ideal</span>
                    <span><i class="fas fa-minus text-warning me-1"></i><strong>-2 SD / +2 SD:</strong> Batas Normal</span>
                    <span><i class="fas fa-minus text-danger me-1"></i><strong>-3 SD / +3 SD:</strong> Batas Waspada</span>
                </div>
                <span class="text-muted"><i class="fas fa-circle-info me-1"></i>Arahkan kursor / sentuh titik grafik untuk melihat rincian</span>
            </div>

            <div style="position: relative; height: 380px; width: 100%;">
                <canvas id="digitalKmsCanvas"></canvas>
            </div>
        </div>

        <!-- Tabel Ringkasan Pengukuran Antropometri di Bawah Grafik -->
        <div class="card p-4 border-0 shadow-sm" style="border-radius: 18px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0" style="color: var(--nx-forest);">
                    <i class="fas fa-table-list me-2"></i>Tabel Riwayat Pengukuran Antropometri &amp; Interpretasi
                </h6>
                <small class="text-muted">Total {{ $pemeriksaansAsc->count() }} titik pengukuran kronologis</small>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.82rem;">
                    <thead style="background: var(--nx-bg); color: var(--nx-forest);">
                        <tr>
                            <th>Tanggal</th>
                            <th>Usia (Bulan)</th>
                            <th>Berat (kg)</th>
                            <th>Tinggi (cm)</th>
                            <th>LK (cm)</th>
                            <th>LiLA (cm)</th>
                            <th>IMT</th>
                            <th>Status BB/U</th>
                            <th>Status TB/U (Stunting)</th>
                            <th>Status BB/TB</th>
                            <th>Catatan Petugas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pemeriksaansDesc as $p)
                            <tr>
                                <td class="fw-bold text-dark">{{ $p->tanggal_pemeriksaan ? \Carbon\Carbon::parse($p->tanggal_pemeriksaan)->format('d/m/Y') : '-' }}</td>
                                <td>{{ $p->umur_bulan }} Bln</td>
                                <td class="fw-bold text-success">{{ $p->berat_badan }} kg</td>
                                <td class="fw-bold text-primary">{{ $p->tinggi_badan }} cm</td>
                                <td>{{ $p->lingkar_kepala ? $p->lingkar_kepala . ' cm' : '-' }}</td>
                                <td>{{ $p->lingkar_lengan ? $p->lingkar_lengan . ' cm' : '-' }}</td>
                                <td>{{ $p->imt ?: round($p->berat_badan / (($p->tinggi_badan/100)*($p->tinggi_badan/100)), 1) }}</td>
                                <td>
                                    <span class="badge rounded-pill bg-light text-dark border">{{ $p->status_bb_u }}</span>
                                </td>
                                <td>
                                    <span class="badge rounded-pill {{ str_contains(strtolower($p->status_tb_u), 'pendek') ? 'bg-danger text-white' : 'bg-success text-white' }}">
                                        {{ $p->status_tb_u }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge rounded-pill bg-light text-dark border">{{ $p->status_bb_tb }}</span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ Str::limit($p->catatan_petugas ?? '-', 35) }}</small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-4 text-muted">Belum ada riwayat pemeriksaan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 2: RIWAYAT PEMERIKSAAN LENGKAP                                        -->
    <!-- ========================================================================= -->
    <div class="tab-pane fade" id="tab-pemeriksaan" role="tabpanel">
        <div class="card p-4 border-0 shadow-sm" style="border-radius: 18px;">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div>
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.05rem;">
                        <i class="fas fa-clipboard-check text-success me-2"></i>Buku Log Pemeriksaan Berkala Balita
                    </h5>
                    <small class="text-muted">Pencatatan penimbangan bulanan, asupan vitamin A, obat cacing, dan ASI eksklusif</small>
                </div>
                @if($isOfficer)
                    <button type="button" class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalCatatPemeriksaan">
                        <i class="fas fa-plus me-1"></i> Catat Pemeriksaan Baru
                    </button>
                @endif
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.84rem;">
                    <thead style="background: var(--nx-bg); color: var(--nx-forest);">
                        <tr>
                            <th>No</th>
                            <th>Tanggal Periksa</th>
                            <th>Usia</th>
                            <th>BB / TB</th>
                            <th>LK / LiLA</th>
                            <th>Status Gizi</th>
                            <th>ASI / Vit A / Cacing</th>
                            <th>Catatan Medis &amp; Solusi</th>
                            <th>Pemeriksa</th>
                            @if($isOfficer)
                                <th class="text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pemeriksaansDesc as $idx => $item)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td class="fw-bold">{{ $item->tanggal_pemeriksaan ? \Carbon\Carbon::parse($item->tanggal_pemeriksaan)->format('d M Y') : '-' }}</td>
                                <td>{{ $item->umur_bulan }} Bulan</td>
                                <td>
                                    <strong style="color: var(--nx-forest);">{{ $item->berat_badan }} kg</strong> /
                                    <span class="text-primary">{{ $item->tinggi_badan }} cm</span>
                                </td>
                                <td>{{ $item->lingkar_kepala ?? '-' }} cm / {{ $item->lingkar_lengan ?? '-' }} cm</td>
                                <td>
                                    @php $b = $item->status_badge; @endphp
                                    <span class="badge rounded-pill px-2.5 py-1" style="background: {{ $b['bg'] }}; color: {{ $b['color'] }}; font-size: 0.72rem;">
                                        {{ $b['label'] }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1" style="font-size: 0.72rem;">
                                        <span>ASI: <strong>{{ $item->asi_eksklusif ?? '-' }}</strong></span>
                                        <span>Vit A: {!! $item->vitamin_a ? '<i class="fas fa-check text-success"></i> Diberikan' : '-' !!}</span>
                                        <span>Obat Cacing: {!! $item->obat_cacing ? '<i class="fas fa-check text-success"></i> Diberikan' : '-' !!}</span>
                                    </div>
                                </td>
                                <td>
                                    <p class="mb-1 text-muted" style="max-width: 250px; font-size: 0.8rem; line-height: 1.4;">
                                        {{ $item->catatan_petugas ?? 'Pemeriksaan rutin berjalan lancar.' }}
                                    </p>
                                    @if($item->is_verified_nakes)
                                        <div class="p-2 rounded-2" style="background: #ECFDF5; border: 1px solid #A7F3D0; font-size: 0.72rem;">
                                            <span class="text-success fw-bold d-flex align-items-center gap-1">
                                                <i class="fas fa-certificate text-emerald-500"></i> Validasi Medis Tenaga Kesehatan
                                            </span>
                                            <span class="text-dark d-block">Status: <strong>{{ $item->status_tumbuh }}</strong></span>
                                            <span class="text-secondary d-block">{{ $item->catatan_nakes }}</span>
                                            @if($item->rekomendasi_rujukan)
                                                <span class="text-danger fw-semibold d-block mt-0.5"><i class="fas fa-hospital me-1"></i>Rujukan: {{ $item->rekomendasi_rujukan }}</span>
                                            @endif
                                            <small class="text-muted d-block mt-0.5">Oleh: {{ $item->verifiedNakes ? $item->verifiedNakes->name : 'Nakes' }} ({{ $item->verified_nakes_at ? \Carbon\Carbon::parse($item->verified_nakes_at)->format('d/m/Y') : '-' }})</small>
                                        </div>
                                    @else
                                        <span class="badge bg-light text-muted border" style="font-size: 0.68rem;">
                                            <i class="fas fa-clock text-warning me-1"></i> Menunggu Verifikasi Nakes
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted d-block">{{ $item->petugas_nama ?? 'Bidan Wilayah' }}</small>
                                </td>
                                @if($isOfficer)
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center gap-1">
                                            @if($isNakes || $isAdminRw)
                                                <button type="button" class="btn btn-sm btn-outline-success py-1 px-2" title="Validasi & Diagnosa Medis Nakes"
                                                        onclick="openNakesModalBalita({{ $item->id }}, '{{ addslashes($item->status_tumbuh ?? '') }}', '{{ addslashes($item->catatan_nakes ?? '') }}', '{{ addslashes($item->rekomendasi_rujukan ?? '') }}')">
                                                    <i class="fas fa-user-md"></i>
                                                </button>
                                            @endif

                                            @if(!$item->is_verified_nakes || $isNakes || $isAdminRw)
                                                <form action="{{ route('posyandu.balita.pemeriksaan.destroy', [$anak->id, $item->id]) }}" method="POST" class="d-inline"
                                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pemeriksaan tanggal {{ $item->tanggal_pemeriksaan ? \Carbon\Carbon::parse($item->tanggal_pemeriksaan)->format('d/m/Y') : '-' }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="badge bg-light text-muted border py-1 px-1.5" title="Data rekam medis yang telah diverifikasi Nakes tidak dapat diubah/dihapus oleh Kader">
                                                    <i class="fas fa-lock text-muted"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isOfficer ? 10 : 9 }}" class="text-center py-4 text-muted">Belum ada riwayat pemeriksaan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 3: RIWAYAT & JADWAL IMUNISASI                                         -->
    <!-- ========================================================================= -->
    <div class="tab-pane fade" id="tab-imunisasi" role="tabpanel">
        <div class="card p-4 border-0 shadow-sm" style="border-radius: 18px;">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div>
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.05rem;">
                        <i class="fas fa-syringe text-success me-2"></i>Kartu Vaksinasi &amp; Imunisasi Anak
                    </h5>
                    <small class="text-muted">Pemantauan vaksinasi dasar lengkap sesuai target Program Imunisasi Nasional (PIN)</small>
                </div>
                @if($isOfficer)
                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCatatImunisasi">
                        <i class="fas fa-plus me-1"></i> Tambah Jadwal Vaksin
                    </button>
                @endif
            </div>

            <div class="row g-3">
                @forelse($anak->imunisasis as $vax)
                    @php
                        $isGiven = $vax->status === 'Sudah Diberikan';
                        $isMissed = $vax->status === 'Terlewat';
                        $cardBg = $isGiven ? 'linear-gradient(135deg, #FFFFFF 0%, #F0FDF4 100%)' : ($isMissed ? '#FEF2F2' : '#FFFFFF');
                        $borderCol = $isGiven ? '#86EFAC' : ($isMissed ? '#FCA5A5' : 'var(--nx-border)');
                    @endphp
                    <div class="col-lg-4 col-md-6">
                        <div class="p-3.5 rounded-4 h-100 border d-flex flex-column justify-content-between shadow-sm"
                             style="background: {{ $cardBg }}; border-color: {{ $borderCol }} !important;">
                            <div>
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.95rem;">
                                        {{ $vax->vaksin_nama }}
                                    </h6>
                                    @php $vb = $vax->status_badge; @endphp
                                    <span class="badge rounded-pill px-2.5 py-1" style="background: {{ $vb['bg'] }}; color: {{ $vb['color'] }}; font-size: 0.72rem; font-weight: 700;">
                                        <i class="fas {{ $vb['icon'] }} me-1"></i>{{ $vax->status }}
                                    </span>
                                </div>

                                <div class="small text-muted mb-2">
                                    @if($isGiven)
                                        <div><i class="far fa-calendar-check text-success me-1"></i>Diberikan: <strong>{{ $vax->tanggal_pemberian ? $vax->tanggal_pemberian->format('d F Y') : '-' }}</strong></div>
                                        @if($vax->batch_nomor)
                                            <div><i class="fas fa-barcode text-muted me-1"></i>Batch: {{ $vax->batch_nomor }}</div>
                                        @endif
                                        <div><i class="fas fa-user-doctor text-success me-1"></i>Oleh: {{ $vax->petugas_nama ?? 'Bidan Desa' }}</div>
                                    @else
                                        <div><i class="far fa-clock text-primary me-1"></i>Target Jadwal: <strong>{{ $vax->jadwal_berikutnya ? $vax->jadwal_berikutnya->format('d F Y') : 'Sesuai Usia' }}</strong></div>
                                    @endif
                                </div>

                                @if($vax->catatan_petugas)
                                    <p class="mb-0 text-muted small" style="font-size: 0.78rem; line-height: 1.4;">
                                        {{ $vax->catatan_petugas }}
                                    </p>
                                @endif
                            </div>

                            @if($isOfficer)
                            <div class="pt-2 border-top mt-2 text-end">
                                <button type="button" class="btn btn-sm btn-outline-secondary py-0.5 px-2"
                                        data-bs-toggle="modal" data-bs-target="#modalUpdateImunisasi{{ $vax->id }}"
                                        style="font-size: 0.75rem;">
                                    <i class="fas fa-pencil me-1"></i> Update Status
                                </button>
                            </div>

                            <!-- Modal Update Status Imunisasi -->
                            <div class="modal fade" id="modalUpdateImunisasi{{ $vax->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow" style="border-radius: 18px;">
                                        <div class="modal-header border-bottom-0 pb-0">
                                            <h5 class="fw-bold modal-title" style="color: var(--nx-forest);">
                                                <i class="fas fa-syringe me-1"></i> Update Status Vaksinasi
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('posyandu.balita.imunisasi.update', [$anak->id, $vax->id]) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Vaksin</label>
                                                    <input type="text" class="form-control" value="{{ $vax->vaksin_nama }}" readonly>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Status Pemberian</label>
                                                    <select name="status" class="form-select" required>
                                                        <option value="Sudah Diberikan" {{ $vax->status === 'Sudah Diberikan' ? 'selected' : '' }}>Sudah Diberikan</option>
                                                        <option value="Terjadwal" {{ $vax->status === 'Terjadwal' ? 'selected' : '' }}>Terjadwal</option>
                                                        <option value="Terlewat" {{ $vax->status === 'Terlewat' ? 'selected' : '' }}>Terlewat</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Tanggal Pemberian</label>
                                                    <input type="date" name="tanggal_pemberian" class="form-control" value="{{ $vax->tanggal_pemberian ? $vax->tanggal_pemberian->format('Y-m-d') : now()->format('Y-m-d') }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Nomor Batch Vaksin</label>
                                                    <input type="text" name="batch_nomor" class="form-control" value="{{ $vax->batch_nomor }}" placeholder="Contoh: BIO-2026-9812">
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label small fw-bold">Catatan / Reaksi Pasca Imunisasi (KIPI)</label>
                                                    <textarea name="catatan_petugas" rows="2" class="form-control">{{ $vax->catatan_petugas }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-top-0">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Simpan Status Vaksin</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-4 text-muted">
                        Belum ada jadwal imunisasi yang dicatat untuk balita ini.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 4: IDENTITAS & KELUARGA                                               -->
    <!-- ========================================================================= -->
    <div class="tab-pane fade" id="tab-identitas" role="tabpanel">
        <div class="card p-4 border-0 shadow-sm" style="border-radius: 18px;">
            <h5 class="fw-bold mb-3 text-dark" style="font-size: 1.05rem;">
                <i class="fas fa-address-card text-success me-2"></i>Data Kependudukan &amp; Riwayat Kelahiran
            </h5>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="p-3.5 rounded-4 bg-light border">
                        <h6 class="fw-bold mb-3 text-dark" style="font-size: 0.95rem;">Identitas Balita</h6>
                        <table class="table table-sm table-borderless small mb-0">
                            <tr>
                                <td class="text-muted" style="width: 140px;">Nama Lengkap</td>
                                <td class="fw-bold text-dark">{{ $anak->nama_anak }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">NIK Anak</td>
                                <td>{{ $anak->nik ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Jenis Kelamin</td>
                                <td>{{ $anak->jenis_kelamin_teks }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tanggal Lahir</td>
                                <td>{{ $anak->tanggal_lahir ? $anak->tanggal_lahir->format('d F Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Golongan Darah</td>
                                <td><span class="badge bg-secondary-subtle text-dark">{{ $anak->golongan_darah ?? '-' }}</span></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Berat Lahir</td>
                                <td>{{ $anak->berat_lahir ? $anak->berat_lahir . ' kg' : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tinggi Lahir</td>
                                <td>{{ $anak->tinggi_lahir ? $anak->tinggi_lahir . ' cm' : '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="p-3.5 rounded-4 bg-light border">
                        <h6 class="fw-bold mb-3 text-dark" style="font-size: 0.95rem;">Keluarga &amp; Tempat Tinggal</h6>
                        <table class="table table-sm table-borderless small mb-0">
                            <tr>
                                <td class="text-muted" style="width: 140px;">Nama Ibu</td>
                                <td class="fw-bold text-dark">{{ $anak->nama_ibu ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Nama Ayah</td>
                                <td class="fw-bold text-dark">{{ $anak->nama_ayah ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Nomor KK</td>
                                <td>{{ $anak->warga ? ($anak->warga->no_kk ?? '-') : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Wilayah RT / RW</td>
                                <td>RT {{ $anak->rt_number ?? '007' }} / RW 018</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Alamat Domisili</td>
                                <td>{{ $anak->alamat ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Nomor Telepon/WA</td>
                                <td>{{ $anak->no_telepon ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Catatan Alergi / Khusus</td>
                                <td>{{ $anak->catatan_khusus ?? 'Tidak ada riwayat alergi khusus' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 5: CATATAN & AUDIT LOG                                                -->
    <!-- ========================================================================= -->
    <div class="tab-pane fade" id="tab-audit" role="tabpanel">
        <div class="card p-4 border-0 shadow-sm" style="border-radius: 18px;">
            <h5 class="fw-bold mb-3 text-dark" style="font-size: 1.05rem;">
                <i class="fas fa-shield-halved text-success me-2"></i>Catatan Petugas &amp; Riwayat Audit Log Kesehatan
            </h5>
            <p class="text-muted small mb-4">Setiap penambahan atau perubahan catatan antropometri dan imunisasi tersimpan transparan dalam audit trail demi akuntabilitas data kesehatan masyarakat.</p>

            @if($auditLogs->isEmpty())
                <div class="p-4 text-center text-muted small rounded-3 bg-light border">
                    Belum ada riwayat aktivitas perubahan data untuk balita ini.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.82rem;">
                        <thead class="text-muted border-bottom">
                            <tr>
                                <th>Waktu</th>
                                <th>Petugas / User</th>
                                <th>Aksi</th>
                                <th>Keterangan Aktivitas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($auditLogs as $log)
                                <tr>
                                    <td class="text-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <strong>{{ $log->user_name }}</strong>
                                        <span class="badge bg-light text-muted border ms-1" style="font-size: 0.68rem;">{{ strtoupper($log->role ?? 'USER') }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-dark">{{ strtoupper($log->event) }}</span>
                                    </td>
                                    <td class="text-muted">{{ $log->description }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- ==================== MODAL CATAT PEMERIKSAAN BARU ==================== -->
@if($isOfficer)
<div class="modal fade" id="modalCatatPemeriksaan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 18px;">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="fw-bold modal-title" style="color: var(--nx-forest);">
                    <i class="fas fa-stethoscope me-1"></i> Catat Hasil Pemeriksaan Balita: {{ $anak->nama_anak }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('posyandu.balita.pemeriksaan.store', $anak->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tanggal Pemeriksaan</label>
                            <input type="date" name="tanggal_pemeriksaan" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Usia Anak Saat Pemeriksaan (Bulan)</label>
                            @php
                                $calcMonths = $anak->tanggal_lahir ? round(\Carbon\Carbon::parse($anak->tanggal_lahir)->diffInMonths(now()), 1) : $anak->umur_bulan;
                            @endphp
                            <input type="number" step="0.1" name="umur_bulan" class="form-control" value="{{ $calcMonths }}" required>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Berat Badan (kg) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="berat_badan" class="form-control" placeholder="Misal: 9.25" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Panjang / Tinggi Badan (cm) <span class="text-danger">*</span></label>
                            <input type="number" step="0.1" name="tinggi_badan" class="form-control" placeholder="Misal: 76.5" required>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Lingkar Kepala (cm)</label>
                            <input type="number" step="0.1" name="lingkar_kepala" class="form-control" placeholder="Misal: 44.5">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Lingkar Lengan Atas / LiLA (cm)</label>
                            <input type="number" step="0.1" name="lingkar_lengan" class="form-control" placeholder="Misal: 14.0">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Status ASI Eksklusif</label>
                            <select name="asi_eksklusif" class="form-select">
                                <option value="Ya">Ya (ASI Saja)</option>
                                <option value="Tidak">Tidak</option>
                                <option value="Selesai">Selesai (&gt; 6 Bulan)</option>
                            </select>
                        </div>
                        <div class="col-md-4 pt-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="vitamin_a" value="1" id="checkVitA">
                                <label class="form-check-label small fw-bold" for="checkVitA">
                                    Diberikan Kapsul Vitamin A
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4 pt-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="obat_cacing" value="1" id="checkCacing">
                                <label class="form-check-label small fw-bold" for="checkCacing">
                                    Diberikan Obat Cacing
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold">Catatan &amp; Konseling Gizi Petugas</label>
                        <textarea name="catatan_petugas" rows="3" class="form-control" placeholder="Nafsu makan baik, anjuran konsumsi protein hewani telur/ikan, jadwalkan penimbangan bulan depan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Hasil &amp; Perbarui KMS</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==================== MODAL CATAT IMUNISASI BARU ==================== -->
<div class="modal fade" id="modalCatatImunisasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 18px;">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="fw-bold modal-title" style="color: var(--nx-forest);">
                    <i class="fas fa-syringe me-1"></i> Catat Imunisasi Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('posyandu.balita.imunisasi.store', $anak->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Vaksin / Imunisasi</label>
                        <input type="text" name="vaksin_nama" class="form-control" placeholder="Contoh: PCV 1 / Rotavirus 1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Status Pemberian</label>
                        <select name="status" class="form-select" required>
                            <option value="Sudah Diberikan">Sudah Diberikan Hari Ini</option>
                            <option value="Terjadwal">Terjadwal (Mendatang)</option>
                            <option value="Terlewat">Terlewat</option>
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tanggal Pemberian</label>
                            <input type="date" name="tanggal_pemberian" class="form-control" value="{{ now()->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nomor Batch Vaksin</label>
                            <input type="text" name="batch_nomor" class="form-control" placeholder="BIO-2026-XXXX">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Jadwal Vaksin Lanjutan Berikutnya</label>
                        <input type="date" name="jadwal_berikutnya" class="form-control">
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold">Catatan Petugas</label>
                        <textarea name="catatan_petugas" rows="2" class="form-control" placeholder="Disuntikkan pada paha kanan, tidak ada demam pasca suntik..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Pencatatan Vaksin</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==================== MODAL EDIT DATA BALITA ==================== -->
<div class="modal fade" id="modalEditBalita" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 18px;">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="fw-bold modal-title" style="color: var(--nx-forest);">
                    <i class="fas fa-pencil me-1"></i> Edit Data Identitas Balita
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('posyandu.balita.update', $anak->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label small fw-bold">Nama Lengkap Balita</label>
                            <input type="text" name="nama_anak" class="form-control" value="{{ $anak->nama_anak }}" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-bold">NIK Anak</label>
                            <input type="text" name="nik" class="form-control" value="{{ $anak->nik }}">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select" required>
                                <option value="L" {{ $anak->jenis_kelamin === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ $anak->jenis_kelamin === 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control" value="{{ $anak->tanggal_lahir ? $anak->tanggal_lahir->format('Y-m-d') : '' }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Golongan Darah</label>
                            <select name="golongan_darah" class="form-select">
                                <option value="" {{ !$anak->golongan_darah ? 'selected' : '' }}>Belum Diketahui</option>
                                <option value="A" {{ $anak->golongan_darah === 'A' ? 'selected' : '' }}>A</option>
                                <option value="B" {{ $anak->golongan_darah === 'B' ? 'selected' : '' }}>B</option>
                                <option value="AB" {{ $anak->golongan_darah === 'AB' ? 'selected' : '' }}>AB</option>
                                <option value="O" {{ $anak->golongan_darah === 'O' ? 'selected' : '' }}>O</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Ibu Kandung</label>
                            <input type="text" name="nama_ibu" class="form-control" value="{{ $anak->nama_ibu }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Ayah</label>
                            <input type="text" name="nama_ayah" class="form-control" value="{{ $anak->nama_ayah }}">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Wilayah RT</label>
                            <select name="rt_number" class="form-select" required>
                                @foreach(['006', '007', '008', '009', '010'] as $rt)
                                    <option value="{{ $rt }}" {{ $anak->rt_number === $rt ? 'selected' : '' }}>RT {{ $rt }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Alamat Rumah</label>
                            <input type="text" name="alamat" class="form-control" value="{{ $anak->alamat }}">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Berat Lahir (kg)</label>
                            <input type="number" step="0.01" name="berat_lahir" class="form-control" value="{{ $anak->berat_lahir }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Tinggi Lahir (cm)</label>
                            <input type="number" step="0.1" name="tinggi_lahir" class="form-control" value="{{ $anak->tinggi_lahir }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">No. Telepon / WA</label>
                            <input type="text" name="no_telepon" class="form-control" value="{{ $anak->no_telepon }}">
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold">Catatan Khusus / Riwayat Alergi</label>
                        <textarea name="catatan_khusus" rows="2" class="form-control">{{ $anak->catatan_khusus }}</textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan Identitas</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@if($isNakes || $isAdminRw)
<div class="modal fade" id="modalVerifyNakesBalita" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 18px;">
            <div class="modal-header border-bottom-0 pb-0">
                <div>
                    <h5 class="fw-bold modal-title text-success">
                        <i class="fas fa-user-md me-1"></i> Validasi Medis Tenaga Kesehatan
                    </h5>
                    <p class="text-muted small mb-0">Penetapan status klinis antropometri dan rekomendasi rujukan Puskesmas.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formVerifyNakesBalita" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Diagnosa / Status Tumbuh Kembang *</label>
                        <select id="nakes_status_tumbuh" name="status_tumbuh" class="form-select" required>
                            <option value="Sesuai Usia / Baik">Sesuai Usia / Baik (Normal)</option>
                            <option value="Berisiko Gizi Kurang">Berisiko Gizi Kurang</option>
                            <option value="Gizi Kurang / Wasting">Gizi Kurang / Wasting</option>
                            <option value="Gizi Buruk">Gizi Buruk (Butuh Penanganan Segera)</option>
                            <option value="Gizi Lebih / Obesitas">Gizi Lebih / Obesitas</option>
                            <option value="Indikasi Stunting (Perlu Terapi PMT)">Indikasi Stunting (Perlu Terapi PMT)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Catatan Klinis &amp; Terapi Nakes *</label>
                        <textarea id="nakes_catatan" name="catatan_nakes" rows="3" class="form-control" required
                                  placeholder="Evaluasi asupan gizi, terapi pemberian PMT lokal, stimulasi motorik balita..."></textarea>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold">Rekomendasi Rujukan Puskesmas / RS (Opsional)</label>
                        <input type="text" id="nakes_rujukan" name="rekomendasi_rujukan" class="form-control"
                               placeholder="Contoh: Poli Gizi Puskesmas Pembantu Cisaranten Kulon">
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i> Simpan Validasi Medis
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function openNakesModalBalita(pemeriksaanId, status, catatan, rujukan) {
    const form = document.getElementById('formVerifyNakesBalita');
    form.action = `/posyandu/balita/{{ $anak->id }}/pemeriksaan/${pemeriksaanId}/verify-nakes`;
    if (status) document.getElementById('nakes_status_tumbuh').value = status;
    document.getElementById('nakes_catatan').value = catatan || '';
    document.getElementById('nakes_rujukan').value = rujukan || '';
    const modal = new bootstrap.Modal(document.getElementById('modalVerifyNakesBalita'));
    modal.show();
}
let kmsChartInstance = null;

// Datasets prepared from backend
const whoBbU = @json($whoBbU);
const whoTbU = @json($whoTbU);
const whoImtU = @json($whoImtU);

const ptsBbU = @json($chartPointsBbU);
const ptsTbU = @json($chartPointsTbU);
const ptsImtU = @json($chartPointsImtU);

function renderKmsChart(type) {
    const ctx = document.getElementById('digitalKmsCanvas');
    if (!ctx) return;

    if (kmsChartInstance) {
        kmsChartInstance.destroy();
    }

    let whoData = whoBbU;
    let childPts = ptsBbU;
    let yLabel = 'Berat Badan (kg)';

    if (type === 'tb_u') {
        whoData = whoTbU;
        childPts = ptsTbU;
        yLabel = 'Tinggi Badan (cm)';
    } else if (type === 'imt_u') {
        whoData = whoImtU;
        childPts = ptsImtU;
        yLabel = 'Indeks Massa Tubuh (IMT)';
    }

    kmsChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            datasets: [
                // Child's Actual Measurements
                {
                    label: 'Hasil Pengukuran Balita',
                    data: childPts,
                    borderColor: '#285943',
                    backgroundColor: '#285943',
                    pointBackgroundColor: '#FFFFFF',
                    pointBorderColor: '#285943',
                    pointBorderWidth: 3,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    borderWidth: 3.5,
                    tension: 0.2,
                    order: 1
                },
                // Median Curve (Ideal 0 SD)
                {
                    label: 'Median WHO (0 SD)',
                    data: whoData.median,
                    borderColor: '#10B981',
                    borderWidth: 2,
                    pointRadius: 0,
                    tension: 0.35,
                    borderDash: [],
                    fill: false,
                    order: 2
                },
                // +2 SD (Batas Normal Atas)
                {
                    label: '+2 SD (Batas Normal Atas)',
                    data: whoData.plus2,
                    borderColor: '#F59E0B',
                    borderWidth: 1.5,
                    borderDash: [5, 5],
                    pointRadius: 0,
                    tension: 0.35,
                    fill: false,
                    order: 3
                },
                // -2 SD (Batas Normal Bawah)
                {
                    label: '-2 SD (Batas Normal Bawah)',
                    data: whoData.minus2,
                    borderColor: '#F59E0B',
                    borderWidth: 1.5,
                    borderDash: [5, 5],
                    pointRadius: 0,
                    tension: 0.35,
                    fill: false,
                    order: 4
                },
                // +3 SD (Batas Risiko Tinggi)
                {
                    label: '+3 SD (Batas Waspada Atas)',
                    data: whoData.plus3,
                    borderColor: '#EF4444',
                    borderWidth: 1.5,
                    borderDash: [3, 3],
                    pointRadius: 0,
                    tension: 0.35,
                    fill: false,
                    order: 5
                },
                // -3 SD (Garis Merah / Bahaya Bawah)
                {
                    label: '-3 SD (Garis Merah Bahaya Bawah)',
                    data: whoData.minus3,
                    borderColor: '#EF4444',
                    borderWidth: 1.5,
                    borderDash: [3, 3],
                    pointRadius: 0,
                    tension: 0.35,
                    fill: false,
                    order: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'nearest',
                intersect: false
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        boxWidth: 14,
                        font: { size: 11, family: 'Plus Jakarta Sans, sans-serif' },
                        color: '#4A5568'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(38, 52, 73, 0.95)',
                    padding: 12,
                    cornerRadius: 10,
                    titleFont: { size: 13, weight: 'bold' },
                    bodyFont: { size: 12 },
                    callbacks: {
                        title: function(context) {
                            return 'Usia: ' + context[0].parsed.x + ' Bulan';
                        },
                        label: function(context) {
                            const ds = context.dataset;
                            if (ds.label === 'Hasil Pengukuran Balita') {
                                const raw = context.raw;
                                return [
                                    'Pengukuran: ' + raw.y + ' ' + (type === 'tb_u' ? 'cm' : (type === 'imt_u' ? '' : 'kg')),
                                    'Tanggal: ' + (raw.date || '-'),
                                    'Status: ' + (raw.status || '-')
                                ];
                            }
                            return ds.label + ': ' + context.parsed.y;
                        }
                    }
                }
            },
            scales: {
                x: {
                    type: 'linear',
                    min: 0,
                    max: 60,
                    title: {
                        display: true,
                        text: 'Usia Anak (Bulan)',
                        color: '#718096',
                        font: { weight: 'bold', size: 12 }
                    },
                    grid: { color: '#F0F4F1' },
                    ticks: {
                        stepSize: 6,
                        color: '#718096'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: yLabel,
                        color: '#718096',
                        font: { weight: 'bold', size: 12 }
                    },
                    grid: { color: '#F0F4F1' },
                    ticks: { color: '#718096' }
                }
            }
        }
    });
}

function switchKmsChart(type, btn) {
    document.querySelectorAll('#kmsChartSwitcher .btn').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');
    renderKmsChart(type);
}

document.addEventListener('DOMContentLoaded', function () {
    renderKmsChart('bb_u');
});
</script>
@endpush
