@extends('layouts.app')

@section('title', 'Profil Kesehatan Ibu Hamil: ' . $bumil->nama_ibu . ' — NexaNest')

@section('content')
@php
    $statusBadge = $bumil->status_badge;
@endphp

<!-- ==================== TOMBOL KEMBALI & HEADER ==================== -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ route('posyandu.index') }}" class="btn btn-outline-secondary btn-sm px-3 py-1.5" style="border-radius: 10px;">
        <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard Posyandu
    </a>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary btn-sm px-3 py-1.5" onclick="window.print()" style="border-radius: 10px;">
            <i class="fas fa-print me-1"></i> Cetak Rekam Medis
        </button>
        @if($isOfficer)
            <button type="button" class="btn btn-primary btn-sm px-3 py-1.5" data-bs-toggle="modal" data-bs-target="#modalCatatAnc" style="border-radius: 10px;">
                <i class="fas fa-plus-circle me-1"></i> Catat Pemeriksaan ANC Baru
            </button>
        @endif
    </div>
</div>

<!-- ==================== PROFILE HERO IBU HAMIL ==================== -->
<div class="card p-4 mb-4 border-0 shadow-sm position-relative overflow-hidden"
     style="background: linear-gradient(135deg, #FFFFFF 0%, #F5F9FD 60%, #E6EFF8 100%); border-radius: 20px; border: 1px solid var(--nx-border) !important;">
    <div class="row align-items-center g-3">
        <div class="col-lg-8">
            <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                <span class="badge rounded-pill px-3 py-1" style="background: #2563EB; color: #fff; font-size: 0.78rem;">
                    <i class="fas fa-person-pregnant me-1"></i> Ibu Hamil Terdaftar RW 018
                </span>
                <span class="badge rounded-pill px-2.5 py-1" style="background: var(--nx-powder); color: #1F517F; border: 1px solid #B0D4F1; font-size: 0.75rem;">
                    RT {{ $bumil->rt_number ?? '008' }} • {{ $bumil->trimester }}
                </span>
                <span class="badge rounded-pill px-2.5 py-1" style="background: {{ $statusBadge['bg'] }}; color: {{ $statusBadge['color'] }}; font-size: 0.75rem; font-weight: 700;">
                    <i class="fas {{ $statusBadge['icon'] }} me-1"></i>{{ $statusBadge['label'] }}
                </span>
            </div>

            <h2 class="fw-bold mb-1" style="color: #1E3A8A; font-size: 1.65rem;">
                {{ $bumil->nama_ibu }}
            </h2>

            <div class="d-flex flex-wrap gap-3 text-muted small mb-3">
                <span><i class="fas fa-heart text-danger me-1"></i>Suami Pendamping: <strong class="text-dark">{{ $bumil->nama_suami ?? '-' }}</strong></span>
                <span><i class="fas fa-id-card text-primary me-1"></i>NIK: {{ $bumil->nik ?? '-' }}</span>
                <span><i class="fas fa-droplet text-danger me-1"></i>Gol. Darah: <strong>{{ $bumil->golongan_darah ?? '-' }}</strong></span>
                <span><i class="fas fa-baby me-1 text-primary"></i>Kehamilan Ke-<strong>{{ $bumil->hamil_ke }}</strong> (G{{ $bumil->hamil_ke }})</span>
            </div>

            <div class="p-3 rounded-3 bg-white border small text-muted d-flex flex-wrap gap-4">
                <div><i class="far fa-calendar-check text-primary me-1"></i>HPHT: <strong>{{ $bumil->hpht ? $bumil->hpht->format('d M Y') : '-' }}</strong></div>
                <div><i class="fas fa-bell text-warning me-1"></i>Taksiran Persalinan (HPL): <strong class="text-dark">{{ $bumil->hpl ? $bumil->hpl->format('d F Y') : '-' }}</strong> ({{ $bumil->hpl_countdown }})</div>
                <div><i class="fas fa-location-dot text-success me-1"></i>Alamat: <strong class="text-dark">{{ $bumil->alamat ?? 'Lingkungan RW 018' }}</strong></div>
                <div><i class="fas fa-phone text-success me-1"></i>No. HP: <strong class="text-dark">{{ $bumil->no_telepon ?? '-' }}</strong></div>
            </div>
        </div>

        <div class="col-lg-4 text-lg-end">
            <div class="p-3 rounded-4 bg-white border text-center shadow-sm d-inline-block w-100" style="max-width: 320px;">
                <span class="d-block text-muted small mb-1">Status Fisik Terkini</span>
                <div class="row g-2 text-center my-2">
                    <div class="col-4">
                        <span class="d-block text-muted" style="font-size: 0.7rem;">Usia Kandungan</span>
                        <h4 class="fw-bold mb-0 text-primary">{{ $bumil->usia_kehamilan_minggu }}<small class="fs-6 text-muted">mgg</small></h4>
                    </div>
                    <div class="col-4">
                        <span class="d-block text-muted" style="font-size: 0.7rem;">Tensi Darah</span>
                        <h4 class="fw-bold mb-0 text-dark" style="font-size: 1.1rem;">{{ $bumil->tekanan_darah }}</h4>
                    </div>
                    <div class="col-4">
                        <span class="d-block text-muted" style="font-size: 0.7rem;">LiLA (Lengan)</span>
                        <h4 class="fw-bold mb-0" style="color: {{ $bumil->lila < 23.5 ? 'var(--nx-danger)' : 'var(--nx-forest)' }};">
                            {{ $bumil->lila }}<small class="fs-6 text-muted">cm</small>
                        </h4>
                    </div>
                </div>
                <div class="pt-2 border-top small text-muted">
                    Berat Badan: <strong>{{ $bumil->berat_badan }} kg</strong> • Target TTD: <strong>{{ $totalTtd }} / 90 butir</strong>
                </div>
            </div>

            @if($isOfficer)
            <div class="mt-2 text-end">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalEditBumil">
                    <i class="fas fa-pencil me-1"></i> Edit Data Ibu Hamil
                </button>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- ==================== BANNER INDIKATOR KESEHATAN IBU HAMIL ==================== -->
<div class="row g-3 mb-4">
    <!-- 1. Skrining LiLA (KEK) -->
    <div class="col-md-4">
        <div class="card p-3.5 h-100 border shadow-sm" style="border-radius: 16px; background: {{ $bumil->lila < 23.5 ? '#FFF5F5' : '#F0F9F3' }}; border-color: {{ $bumil->lila < 23.5 ? 'rgba(239, 68, 68, 0.25)' : 'rgba(40, 89, 67, 0.25)' }} !important;">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <small class="fw-bold text-muted" style="font-size: 0.72rem;">SKRINING GIZI (LiLA)</small>
                <i class="fas {{ $bumil->lila < 23.5 ? 'fa-triangle-exclamation text-danger' : 'fa-circle-check text-success' }}"></i>
            </div>
            <h5 class="fw-bold mb-1 {{ $bumil->lila < 23.5 ? 'text-danger' : 'text-dark' }}">
                {{ $bumil->lila }} cm
            </h5>
            <small class="text-muted">
                {{ $bumil->lila < 23.5 ? 'Beresiko KEK (Kurang Energi Kronis) karena < 23.5 cm. Diperlukan konsumsi PMT biskuit & susu hamil.' : 'Status gizi lingkar lengan normal (>= 23.5 cm). Bebas dari risiko KEK.' }}
            </small>
        </div>
    </div>

    <!-- 2. Tekanan Darah (Preeklamsia) -->
    <div class="col-md-4">
        <div class="card p-3.5 h-100 border shadow-sm" style="border-radius: 16px; background: #EBF4FA; border-color: rgba(59, 130, 246, 0.25) !important;">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <small class="fw-bold text-muted" style="font-size: 0.72rem;">TEKANAN DARAH</small>
                <i class="fas fa-heart-pulse text-primary"></i>
            </div>
            <h5 class="fw-bold mb-1 text-dark">
                {{ $bumil->tekanan_darah }} mmHg
            </h5>
            <small class="text-muted">
                Skrining berkala hipertensi gestasional dan deteksi dini gejala preeklamsia trimester akhir.
            </small>
        </div>
    </div>

    <!-- 3. Tablet Tambah Darah (TTD) -->
    <div class="col-md-4">
        <div class="card p-3.5 h-100 border shadow-sm" style="border-radius: 16px; background: #EEF2FF; border-color: rgba(99, 102, 241, 0.25) !important;">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <small class="fw-bold text-muted" style="font-size: 0.72rem;">TABLET TAMBAH DARAH (TTD)</small>
                <i class="fas fa-pills text-indigo"></i>
            </div>
            <h5 class="fw-bold mb-1 text-dark">
                {{ $totalTtd }} / 90 Tablet
            </h5>
            <div class="progress my-1" style="height: 6px;">
                @php $ttdPct = min(100, round(($totalTtd / 90) * 100)); @endphp
                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $ttdPct }}%;"></div>
            </div>
            <small class="text-muted">
                Target wajib 90 tablet selama kehamilan guna mencegah anemia maternal dan berat badan lahir rendah.
            </small>
        </div>
    </div>
</div>

<!-- ==================== TAB NAVIGASI BUMIL ==================== -->
<div class="card p-2 mb-4 border-0 shadow-sm" style="background: var(--nx-bg); border-radius: 14px;">
    <ul class="nav nav-pills nav-fill gap-1" id="bumilTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link py-2.5 px-3 fw-bold d-flex align-items-center justify-content-center gap-2 active"
                    id="tab-btn-timeline" data-bs-toggle="pill" data-bs-target="#tab-timeline" type="button" role="tab"
                    style="border-radius: 10px; font-size: 0.88rem;">
                <i class="fas fa-timeline"></i>
                <span>1. Timeline Riwayat Pemeriksaan ANC ({{ $ancTimeline->count() }})</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link py-2.5 px-3 fw-bold d-flex align-items-center justify-content-center gap-2"
                    id="tab-btn-tabel" data-bs-toggle="pill" data-bs-target="#tab-tabel" type="button" role="tab"
                    style="border-radius: 10px; font-size: 0.88rem;">
                <i class="fas fa-table-list"></i>
                <span>2. Tabel Rekam Medis Detail</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link py-2.5 px-3 fw-bold d-flex align-items-center justify-content-center gap-2"
                    id="tab-btn-audit" data-bs-toggle="pill" data-bs-target="#tab-audit" type="button" role="tab"
                    style="border-radius: 10px; font-size: 0.88rem;">
                <i class="fas fa-shield-halved"></i>
                <span>3. Catatan &amp; Audit Log</span>
            </button>
        </li>
    </ul>
</div>

<div class="tab-content" id="bumilTabContent">
    <!-- ========================================================================= -->
    <!-- TAB 1: TIMELINE RIWAYAT ANC                                               -->
    <!-- ========================================================================= -->
    <div class="tab-pane fade show active" id="tab-timeline" role="tabpanel">
        <div class="card p-4 border-0 shadow-sm" style="border-radius: 18px;">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                <div>
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.05rem;">
                        <i class="fas fa-timeline text-primary me-2"></i>Timeline Kunjungan Antenatal Care (ANC)
                    </h5>
                    <small class="text-muted">Pemantauan bertahap perkembangan janin dan kesehatan ibu dari Trimester I sampai III</small>
                </div>
                @if($isOfficer)
                    <button type="button" class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalCatatAnc">
                        <i class="fas fa-plus me-1"></i> Catat ANC Baru
                    </button>
                @endif
            </div>

            @if($ancTimeline->isEmpty())
                <x-empty-state 
                    title="Belum Ada Rekam ANC" 
                    subtitle="Belum ada catatan riwayat pemeriksaan kehamilan berkala untuk ibu ini." 
                    icon="fas fa-stethoscope" 
                />
            @else
                <!-- Timeline Component -->
                <div class="position-relative ps-4" style="border-left: 2px solid var(--nx-border); margin-left: 12px;">
                    @foreach($ancTimeline as $idx => $anc)
                        @php
                            $ab = $anc->status_badge;
                        @endphp
                        <div class="mb-4 position-relative">
                            <!-- Timeline Dot -->
                            <span class="position-absolute rounded-circle shadow-sm d-flex align-items-center justify-content-center text-white"
                                  style="left: -32px; top: 0; width: 24px; height: 24px; background: #2563EB; font-size: 0.7rem;">
                                {{ $ancTimeline->count() - $idx }}
                            </span>

                            <div class="card p-3.5 border-0 shadow-sm" style="border-radius: 16px; background: #FFFFFF; border: 1px solid var(--nx-border) !important;">
                                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                                    <div>
                                        <span class="badge rounded-pill px-2.5 py-1 mb-1" style="background: var(--nx-powder); color: #1F517F; font-size: 0.72rem;">
                                            Kunjungan ANC • Usia Kehamilan {{ $anc->usia_kehamilan_minggu }} Minggu
                                        </span>
                                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.98rem;">
                                            {{ $anc->tanggal_pemeriksaan->translatedFormat('l, d F Y') }}
                                        </h6>
                                    </div>
                                    <span class="badge rounded-pill px-2.5 py-1" style="background: {{ $ab['bg'] }}; color: {{ $ab['color'] }}; font-size: 0.72rem; font-weight: 700;">
                                        <i class="fas {{ $ab['icon'] }} me-1"></i>{{ $ab['label'] }}
                                    </span>
                                </div>

                                <div class="row g-2 text-center p-2.5 rounded-3 mb-2" style="background: var(--nx-bg); font-size: 0.8rem;">
                                    <div class="col-md-2 col-4">
                                        <span class="d-block text-muted" style="font-size: 0.7rem;">Berat Ibu</span>
                                        <strong class="text-dark">{{ $anc->berat_badan }} kg</strong>
                                    </div>
                                    <div class="col-md-2 col-4">
                                        <span class="d-block text-muted" style="font-size: 0.7rem;">Tensi Darah</span>
                                        <strong class="text-primary">{{ $anc->tekanan_darah }}</strong>
                                    </div>
                                    <div class="col-md-2 col-4">
                                        <span class="d-block text-muted" style="font-size: 0.7rem;">LiLA</span>
                                        <strong style="color: {{ $anc->lila < 23.5 ? 'var(--nx-danger)' : 'var(--nx-forest)' }};">{{ $anc->lila }} cm</strong>
                                    </div>
                                    <div class="col-md-2 col-4">
                                        <span class="d-block text-muted" style="font-size: 0.7rem;">Tinggi Fundus</span>
                                        <strong class="text-dark">{{ $anc->tinggi_fundus ? $anc->tinggi_fundus . ' cm' : '-' }}</strong>
                                    </div>
                                    <div class="col-md-2 col-4">
                                        <span class="d-block text-muted" style="font-size: 0.7rem;">Detak Jantung (DJJ)</span>
                                        <strong class="text-success">{{ $anc->detak_jantung_janin ?? '-' }}</strong>
                                    </div>
                                    <div class="col-md-2 col-4">
                                        <span class="d-block text-muted" style="font-size: 0.7rem;">Diberi TTD</span>
                                        <strong class="text-dark">{{ $anc->pemberian_ttd }} Butir</strong>
                                    </div>
                                </div>

                                @if($anc->keluhan)
                                    <div class="small text-muted mb-1">
                                        <strong class="text-dark"><i class="fas fa-comment-dots text-warning me-1"></i>Keluhan Ibu:</strong> {{ $anc->keluhan }}
                                    </div>
                                @endif

                                @if($anc->catatan_petugas)
                                    <div class="small text-muted mb-2">
                                        <strong class="text-dark"><i class="fas fa-notes-medical text-success me-1"></i>Saran Petugas &amp; Tindakan:</strong> {{ $anc->catatan_petugas }}
                                    </div>
                                @endif

                                @if($anc->is_verified_nakes)
                                    <div class="p-2.5 rounded-2 mb-2" style="background: #ECFDF5; border: 1px solid #A7F3D0; font-size: 0.75rem;">
                                        <span class="text-success fw-bold d-flex align-items-center gap-1">
                                            <i class="fas fa-certificate text-emerald-500"></i> Validasi Medis Tenaga Kesehatan / Bidan
                                        </span>
                                        <span class="text-dark d-block">Status Klinis: <strong>{{ $anc->status_kesehatan }}</strong></span>
                                        <span class="text-secondary d-block">{{ $anc->catatan_nakes }}</span>
                                        @if($anc->rekomendasi_rujukan)
                                            <span class="text-danger fw-semibold d-block mt-0.5"><i class="fas fa-hospital me-1"></i>Rujukan: {{ $anc->rekomendasi_rujukan }}</span>
                                        @endif
                                        <small class="text-muted d-block mt-0.5">Oleh: {{ $anc->verifiedNakes ? $anc->verifiedNakes->name : 'Nakes' }} ({{ $anc->verified_nakes_at ? $anc->verified_nakes_at->format('d/m/Y') : '-' }})</small>
                                    </div>
                                @else
                                    <div class="mb-2">
                                        <span class="badge bg-light text-muted border" style="font-size: 0.68rem;">
                                            <i class="fas fa-clock text-warning me-1"></i> Menunggu Verifikasi Medis Nakes
                                        </span>
                                    </div>
                                @endif

                                <div class="pt-2 border-top d-flex justify-content-between align-items-center small text-muted" style="font-size: 0.75rem;">
                                    <div>
                                        <i class="fas fa-user-doctor me-1 text-success"></i>Pemeriksa: {{ $anc->petugas_nama ?? 'Bidan Desa RW 018' }} •
                                        <i class="far fa-calendar-alt ms-1 me-1 text-primary"></i>Kunjungan Berikutnya: <strong>{{ $anc->jadwal_berikutnya ? $anc->jadwal_berikutnya->format('d M Y') : '-' }}</strong>
                                    </div>
                                    @if($isOfficer)
                                        <div class="d-flex align-items-center gap-1">
                                            @if($isNakes || $isAdminRw)
                                                <button type="button" class="btn btn-sm btn-outline-success py-0.5 px-2" style="font-size: 0.72rem;"
                                                        onclick="openNakesModalBumil({{ $anc->id }}, '{{ addslashes($anc->status_kesehatan ?? '') }}', '{{ addslashes($anc->catatan_nakes ?? '') }}', '{{ addslashes($anc->rekomendasi_rujukan ?? '') }}')">
                                                    <i class="fas fa-user-md me-1"></i> Validasi Medis
                                                </button>
                                            @endif

                                            @if(!$anc->is_verified_nakes || $isNakes || $isAdminRw)
                                                <form action="{{ route('posyandu.bumil.pemeriksaan.destroy', [$bumil->id, $anc->id]) }}" method="POST" class="d-inline"
                                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan pemeriksaan ANC ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger py-0.5 px-2" style="font-size: 0.72rem;">
                                                        <i class="fas fa-trash me-1"></i> Hapus
                                                    </button>
                                                </form>
                                            @else
                                                <span class="badge bg-light text-muted border py-1 px-1.5" title="Data ANC yang telah diverifikasi Nakes tidak dapat diubah/dihapus oleh Kader">
                                                    <i class="fas fa-lock text-muted"></i>
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 2: TABEL REKAM MEDIS DETAIL                                          -->
    <!-- ========================================================================= -->
    <div class="tab-pane fade" id="tab-tabel" role="tabpanel">
        <div class="card p-4 border-0 shadow-sm" style="border-radius: 18px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.05rem;">
                    <i class="fas fa-table-list text-primary me-2"></i>Buku Register Pemeriksaan Kehamilan
                </h5>
                <small class="text-muted">Total {{ $ancTimeline->count() }} kali pemeriksaan tercatat</small>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.84rem;">
                    <thead style="background: var(--nx-bg); color: var(--nx-forest);">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Usia Kehamilan</th>
                            <th>BB</th>
                            <th>Tensi (TD)</th>
                            <th>LiLA</th>
                            <th>TFU</th>
                            <th>DJJ</th>
                            <th>TTD</th>
                            <th>Status Medis</th>
                            <th>Jadwal Selanjutnya</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ancTimeline as $i => $row)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td class="fw-bold">{{ $row->tanggal_pemeriksaan->format('d/m/Y') }}</td>
                                <td>{{ $row->usia_kehamilan_minggu }} Minggu</td>
                                <td>{{ $row->berat_badan }} kg</td>
                                <td>{{ $row->tekanan_darah }}</td>
                                <td>{{ $row->lila }} cm</td>
                                <td>{{ $row->tinggi_fundus ? $row->tinggi_fundus . ' cm' : '-' }}</td>
                                <td>{{ $row->detak_jantung_janin ?? '-' }}</td>
                                <td>+{{ $row->pemberian_ttd }} Butir</td>
                                <td>
                                    @php $rb = $row->status_badge; @endphp
                                    <span class="badge rounded-pill px-2.5 py-1" style="background: {{ $rb['bg'] }}; color: {{ $rb['color'] }}; font-size: 0.72rem;">
                                        {{ $rb['label'] }}
                                    </span>
                                </td>
                                <td>{{ $row->jadwal_berikutnya ? $row->jadwal_berikutnya->format('d/m/Y') : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-4 text-muted">Belum ada data pemeriksaan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 3: CATATAN & AUDIT LOG                                                -->
    <!-- ========================================================================= -->
    <div class="tab-pane fade" id="tab-audit" role="tabpanel">
        <div class="card p-4 border-0 shadow-sm" style="border-radius: 18px;">
            <h5 class="fw-bold mb-3 text-dark" style="font-size: 1.05rem;">
                <i class="fas fa-shield-halved text-primary me-2"></i>Catatan Petugas &amp; Riwayat Audit Log
            </h5>

            @if($auditLogs->isEmpty())
                <div class="p-4 text-center text-muted small rounded-3 bg-light border">
                    Belum ada riwayat aktivitas perubahan data untuk ibu hamil ini.
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

<!-- ==================== MODAL CATAT PEMERIKSAAN ANC BARU ==================== -->
@if($isOfficer)
<div class="modal fade" id="modalCatatAnc" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 18px;">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="fw-bold modal-title" style="color: #1E3A8A;">
                    <i class="fas fa-stethoscope me-1"></i> Catat Pemeriksaan ANC Baru: {{ $bumil->nama_ibu }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('posyandu.bumil.pemeriksaan.store', $bumil->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tanggal Pemeriksaan</label>
                            <input type="date" name="tanggal_pemeriksaan" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Usia Kehamilan (Minggu)</label>
                            <input type="number" name="usia_kehamilan_minggu" class="form-control" value="{{ $bumil->usia_kehamilan_minggu }}" required>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Berat Badan Ibu (kg)</label>
                            <input type="number" step="0.1" name="berat_badan" class="form-control" placeholder="Misal: 62.5" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Tekanan Darah (Tensi)</label>
                            <input type="text" name="tekanan_darah" class="form-control" placeholder="110/75" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Lingkar Lengan LiLA (cm)</label>
                            <input type="number" step="0.1" name="lila" class="form-control" placeholder="25.0" required>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Tinggi Fundus Uteri (cm)</label>
                            <input type="number" step="0.1" name="tinggi_fundus" class="form-control" placeholder="Misal: 24.0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Detak Jantung Janin (DJJ)</label>
                            <input type="text" name="detak_jantung_janin" class="form-control" placeholder="144 bpm (Doppler)">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Jumlah Pemberian TTD (Tablet)</label>
                            <input type="number" name="pemberian_ttd" class="form-control" value="30">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Keluhan Ibu Hamil</label>
                        <input type="text" name="keluhan" class="form-control" placeholder="Pusing ringan, pegal pinggang, mual berkurang...">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Jadwal Kunjungan Berikutnya</label>
                            <input type="date" name="jadwal_berikutnya" class="form-control" value="{{ now()->addWeeks(4)->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Saran Medis &amp; Catatan Petugas</label>
                            <textarea name="catatan_petugas" rows="2" class="form-control" placeholder="Kondisi janin baik, anjurkan tidur miring kiri, lanjutkan suplemen kalsium dan zat besi..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Pemeriksaan ANC</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==================== MODAL EDIT DATA IBU HAMIL ==================== -->
<div class="modal fade" id="modalEditBumil" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 18px;">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="fw-bold modal-title" style="color: #1E3A8A;">
                    <i class="fas fa-pencil me-1"></i> Edit Data Ibu Hamil: {{ $bumil->nama_ibu }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('posyandu.bumil.update', $bumil->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label small fw-bold">Nama Lengkap Ibu Hamil</label>
                            <input type="text" name="nama_ibu" class="form-control" value="{{ $bumil->nama_ibu }}" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-bold">NIK KTP</label>
                            <input type="text" name="nik" class="form-control" value="{{ $bumil->nik }}">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Suami</label>
                            <input type="text" name="nama_suami" class="form-control" value="{{ $bumil->nama_suami }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nomor Telepon / WhatsApp</label>
                            <input type="text" name="no_telepon" class="form-control" value="{{ $bumil->no_telepon }}">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Wilayah RT</label>
                            <select name="rt_number" class="form-select" required>
                                @foreach(['006', '007', '008', '009', '010'] as $rt)
                                    <option value="{{ $rt }}" {{ $bumil->rt_number === $rt ? 'selected' : '' }}>RT {{ $rt }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Alamat Rumah</label>
                            <input type="text" name="alamat" class="form-control" value="{{ $bumil->alamat }}">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">HPHT</label>
                            <input type="date" name="hpht" class="form-control" value="{{ $bumil->hpht ? $bumil->hpht->format('Y-m-d') : '' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">HPL (Hari Perkiraan Lahir)</label>
                            <input type="date" name="hpl" class="form-control" value="{{ $bumil->hpl ? $bumil->hpl->format('Y-m-d') : '' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Kehamilan Ke- (Gravida)</label>
                            <input type="number" name="hamil_ke" class="form-control" value="{{ $bumil->hamil_ke }}">
                        </div>
                    </div>
                    <div class="row g-3 mb-0">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Golongan Darah</label>
                            <select name="golongan_darah" class="form-select">
                                <option value="" {{ !$bumil->golongan_darah ? 'selected' : '' }}>Belum Diketahui</option>
                                <option value="A" {{ $bumil->golongan_darah === 'A' ? 'selected' : '' }}>A</option>
                                <option value="B" {{ $bumil->golongan_darah === 'B' ? 'selected' : '' }}>B</option>
                                <option value="AB" {{ $bumil->golongan_darah === 'AB' ? 'selected' : '' }}>AB</option>
                                <option value="O" {{ $bumil->golongan_darah === 'O' ? 'selected' : '' }}>O</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Status Kehamilan</label>
                            <select name="status_kehamilan" class="form-select" required>
                                <option value="Aktif Hamil" {{ $bumil->status_kehamilan === 'Aktif Hamil' ? 'selected' : '' }}>Aktif Hamil</option>
                                <option value="Sudah Melahirkan" {{ $bumil->status_kehamilan === 'Sudah Melahirkan' ? 'selected' : '' }}>Sudah Melahirkan</option>
                                <option value="Gugur" {{ $bumil->status_kehamilan === 'Gugur' ? 'selected' : '' }}>Gugur / Abortus</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@if($isNakes || $isAdminRw)
<div class="modal fade" id="modalVerifyNakesBumil" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 18px;">
            <div class="modal-header border-bottom-0 pb-0">
                <div>
                    <h5 class="fw-bold modal-title text-success">
                        <i class="fas fa-user-md me-1"></i> Validasi Medis Nakes / Bidan Desa
                    </h5>
                    <p class="text-muted small mb-0">Penetapan diagnosa klinis ANC ibu hamil dan rujukan medis.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formVerifyNakesBumil" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Diagnosa Klinis ANC *</label>
                        <select id="nakes_status_kesehatan" name="status_kesehatan" class="form-select" required>
                            <option value="Sehat / Normal">Sehat / Normal (Tanpa Komplikasi)</option>
                            <option value="Perlu Perhatian Khusus">Perlu Perhatian Khusus (Pantau Rutin)</option>
                            <option value="Anemia Sedang">Anemia Sedang (Hb 8-10.9 g/dL)</option>
                            <option value="Anemia Berat">Anemia Berat (Hb < 8 g/dL)</option>
                            <option value="Hipertensi Gestasional">Hipertensi Gestasional</option>
                            <option value="Risiko Tinggi / KEK">Risiko Tinggi / KEK (Kurang Energi Kronis)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Catatan Klinis &amp; Terapi Nakes *</label>
                        <textarea id="nakes_catatan_bumil" name="catatan_nakes" rows="3" class="form-control" required
                                  placeholder="Evaluasi asupan asam folat, kalsium, pantau gerakan janin dan tekanan darah..."></textarea>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold">Rekomendasi Rujukan Puskesmas / RS (Opsional)</label>
                        <input type="text" id="nakes_rujukan_bumil" name="rekomendasi_rujukan" class="form-control"
                               placeholder="Contoh: Poli KIA Puskesmas Pembantu untuk USG Doppler">
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
<script>
function openNakesModalBumil(pemeriksaanId, status, catatan, rujukan) {
    const form = document.getElementById('formVerifyNakesBumil');
    form.action = `/posyandu/bumil/{{ $bumil->id }}/pemeriksaan/${pemeriksaanId}/verify-nakes`;
    if (status) document.getElementById('nakes_status_kesehatan').value = status;
    document.getElementById('nakes_catatan_bumil').value = catatan || '';
    document.getElementById('nakes_rujukan_bumil').value = rujukan || '';
    const modal = new bootstrap.Modal(document.getElementById('modalVerifyNakesBumil'));
    modal.show();
}
</script>
@endpush
