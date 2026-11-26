@extends('layouts.app')

@section('title', 'Monitoring Wilayah — NexaNest')

@section('content')
<style>
    .mon-filter { display:flex; gap:.5rem; flex-wrap:wrap; }
    .mon-filter .form-select {
        min-width: 140px; height: 40px; padding: .4rem 2.2rem .4rem .9rem;
        border-radius: 10px; border: 1px solid var(--nx-border, #E5EFE7);
        background-color: #fff; font-weight: 600; font-size: .85rem; color: #263449;
        background-position: right .8rem center;
    }
    .mon-filter .form-select[name="tahun"] { min-width: 100px; }
    .mon-reminder { background:#FFFBEF; border:1px solid #F6D89E; border-radius:16px; padding:1.1rem 1.25rem; margin-bottom:1.5rem; }
    .mon-reminder-head { display:flex; align-items:center; gap:.6rem; margin-bottom:.85rem; }
    .mon-reminder-head .ic { width:34px; height:34px; border-radius:10px; background:#FCE8BF; color:#B7791F; display:flex; align-items:center; justify-content:center; }
    .mon-reminder-head h6 { margin:0; font-weight:700; font-size:.95rem; color:#263449; }
    .mon-reminder-head .count { margin-left:auto; background:#FCE8BF; color:#8a5a12; font-weight:700; font-size:.72rem; padding:.25rem .65rem; border-radius:999px; }
    .mon-reminder-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(320px, 1fr)); gap:.6rem; }
    .mon-reminder-item { display:flex; align-items:center; gap:.7rem; background:#fff; border:1px solid #F3E3BC; border-left:4px solid #E0A93B; border-radius:10px; padding:.65rem .85rem; font-size:.82rem; color:#263449; line-height:1.35; transition:transform .15s, box-shadow .15s; }
    .mon-reminder-item:hover { transform:translateY(-1px); box-shadow:0 4px 12px rgba(224,169,59,.15); }
    .mon-reminder-item.danger { border-left-color:#D9534F; }
    .mon-reminder-item.info { border-left-color:#4A7FB0; }
    .mon-reminder-item i { flex-shrink:0; width:18px; text-align:center; }
    .mon-rt-cell { display:flex; align-items:center; gap:.7rem; }
    @media (max-width: 768px) { .nx-page-header { flex-direction:column; align-items:flex-start !important; gap:1rem; } }

    /* Dark Mode Support */
    html.dark .mon-filter .form-select {
        background-color: #0F172A !important;
        border-color: #273654 !important;
        color: #F1F5F9 !important;
    }
    html.dark .mon-reminder {
        background: #17223B !important;
        border-color: #273654 !important;
    }
    html.dark .mon-reminder-head h6 {
        color: #F1F5F9 !important;
    }
    html.dark .mon-reminder-item {
        background: #131B2E !important;
        border-color: #1E2D4A !important;
        border-left-color: #F59E0B !important;
        color: #F1F5F9 !important;
    }
    html.dark .mon-reminder-item:hover {
        background: #1E2E4F !important;
        color: #FFFFFF !important;
    }
</style>
<div class="nx-page-header">
    <div>
        <span class="badge rounded-pill mb-2 px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
            <i class="fas fa-tower-observation me-1"></i> Pengawasan &amp; Tata Kelola Wilayah
        </span>
        <h1 class="nx-page-title">Pusat Monitoring Wilayah (RW 018)</h1>
        <p class="nx-page-subtitle">
            Pusat pemantauan real-time perkembangan administrasi, keuangan, kepatuhan iuran, pelayanan, dan lingkungan di seluruh RT 006 sampai RT 010.
        </p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <form method="GET" action="{{ route('monitoring.index') }}" class="mon-filter">
            <select name="bulan" class="form-select form-select-sm" onchange="this.form.submit()">
                @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $k => $n)
                    <option value="{{ $k }}" {{ $bulan == $k ? 'selected' : '' }}>{{ $n }}</option>
                @endforeach
            </select>
            <select name="tahun" class="form-select form-select-sm" onchange="this.form.submit()">
                @foreach(range(date('Y'), date('Y') - 4) as $y)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </form>
    </div>
</div>

<!-- REMINDERS / PENGINGAT SISTEM -->
@if(!empty($reminders))
<div class="mon-reminder">
    <div class="mon-reminder-head">
        <span class="ic"><i class="fas fa-bell"></i></span>
        <h6>Pengingat &amp; Perhatian Khusus Admin RW</h6>
        <span class="count">{{ count($reminders) }} item</span>
    </div>
    <div class="mon-reminder-grid">
        @foreach($reminders as $rem)
            <div class="mon-reminder-item {{ $rem['type'] }}">
                <i class="fas {{ $rem['icon'] }} text-{{ $rem['type'] }}"></i>
                <span>{{ $rem['text'] }}</span>
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- METRIC OVERVIEW CARDS -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card p-3 h-100 d-flex flex-row align-items-center justify-content-between" style="background: var(--nx-mint-soft) !important; border-color: var(--nx-sage) !important;">
            <div>
                <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem;">Total Kependudukan RW</span>
                <h3 class="fw-bold mb-0" style="color: var(--nx-forest);">{{ number_format($totalWargaRW) }} <span class="fs-6 text-muted fw-normal">Warga</span></h3>
                <small class="text-muted" style="font-size: 0.72rem;">{{ number_format($totalKkRW) }} Kepala Keluarga (5 RT)</small>
            </div>
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: var(--nx-mint); color: var(--nx-forest);">
                <i class="fas fa-users-rays fa-lg"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card p-3 h-100 d-flex flex-row align-items-center justify-content-between" style="background: var(--nx-powder-soft) !important; border-color: var(--nx-pastel-blue) !important;">
            <div>
                <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem;">Status Laporan RT</span>
                <h3 class="fw-bold mb-0" style="color: #2b567a;">{{ $countLaporanDiterima }}/5 <span class="fs-6 text-muted fw-normal">Diterima</span></h3>
                <small class="text-muted" style="font-size: 0.72rem;">{{ $countLaporanMenunggu }} Menunggu · {{ $countLaporanRevisi }} Revisi</small>
            </div>
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: var(--nx-powder); color: #2b567a;">
                <i class="fas fa-file-circle-check fa-lg"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card p-3 h-100 d-flex flex-row align-items-center justify-content-between" style="background: var(--nx-warning-soft) !important; border-color: #F6D89E !important;">
            <div>
                <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem;">Pengaduan Warga Aktif</span>
                <h3 class="fw-bold mb-0" style="color: var(--nx-warning);">{{ $totalPengaduanAktifRW }} <span class="fs-6 text-muted fw-normal">Laporan</span></h3>
                <small class="text-muted" style="font-size: 0.72rem;">Dalam proses tindak lanjut RT/RW</small>
            </div>
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: #FCE8BF; color: var(--nx-warning);">
                <i class="fas fa-comments fa-lg"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card p-3 h-100 d-flex flex-row align-items-center justify-content-between" style="background: #FAF8EA !important; border-color: #E2D9A7 !important;">
            <div>
                <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem;">Aktivitas Bank Sampah</span>
                <h3 class="fw-bold mb-0" style="color: #6C5B17;">{{ number_format($totalSampahTerkumpulRW, 1) }} <span class="fs-6 text-muted fw-normal">Kg</span></h3>
                <small class="text-muted" style="font-size: 0.72rem;">Total pilah daur ulang warga</small>
            </div>
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: #F6EDC2; color: #6C5B17;">
                <i class="fas fa-recycle fa-lg"></i>
            </div>
        </div>
    </div>
</div>

<!-- DAFTAR MONITORING SETIAP RT -->
<div class="card overflow-hidden mb-4">
    <div class="card-header p-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0" style="font-size: 1rem; color: var(--nx-forest);">
            <i class="fas fa-map-location-dot me-2"></i>Status Operasional &amp; Monitoring Seluruh RT (Periode: {{ \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F Y') }})
        </h5>
        <span class="badge rounded-pill px-3 py-1" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-weight: 700;">
            5 Wilayah RT
        </span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4" style="width: 14%;">Wilayah RT</th>
                    <th style="width: 20%;">Ketua RT</th>
                    <th style="width: 16%;">Kependudukan</th>
                    <th style="width: 16%;">Kas RT</th>
                    <th style="width: 16%;">Kepatuhan Iuran</th>
                    <th style="width: 18%;">Status Laporan Bulanan</th>
                    <th class="pe-4 text-end" style="width: 15%;">Detail Monitoring</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rtData as $rt => $data)
                    <tr>
                        <td class="ps-4">
                            <div class="mon-rt-cell">
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                     style="width: 40px; height: 40px; background: var(--nx-mint); color: var(--nx-forest); font-size: 0.88rem;">
                                    {{ $rt }}
                                </div>
                                <div>
                                    <strong class="d-block text-dark">RT {{ $rt }}</strong>
                                    <small class="text-muted" style="font-size: 0.72rem;">RW 018</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <strong class="d-block text-dark" style="font-size: 0.88rem;">{{ $data['ketua_nama'] }}</strong>
                            <small class="text-muted" style="font-size: 0.72rem;"><i class="fas fa-phone-alt me-1"></i>{{ $data['ketua_phone'] }}</small>
                        </td>
                        <td>
                            <strong class="d-block text-dark">{{ $data['warga_count'] }} <span class="fw-normal text-muted small">Jiwa</span></strong>
                            <small class="text-muted" style="font-size: 0.72rem;">{{ $data['kk_count'] }} KK ({{ $data['male_count'] }}L / {{ $data['female_count'] }}P)</small>
                        </td>
                        <td>
                            <strong class="d-block {{ $data['saldo_kas'] >= 0 ? 'text-success' : 'text-danger' }}">
                                Rp {{ number_format($data['saldo_kas'], 0, ',', '.') }}
                            </strong>
                            <small class="text-muted" style="font-size: 0.72rem;">Kas Operasional RT</small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height: 7px; border-radius: 4px;">
                                    <div class="progress-bar" role="progressbar" style="width: {{ $data['iuran_compliance'] }}%; background: var(--nx-forest);"></div>
                                </div>
                                <span class="fw-bold small" style="font-size: 0.78rem;">{{ $data['iuran_compliance'] }}%</span>
                            </div>
                            <small class="text-muted" style="font-size: 0.72rem;">Bulan {{ \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('M Y') }}</small>
                        </td>
                        <td>
                            @if($data['status_laporan'] == 'diterima')
                                <span class="badge rounded-pill px-3 py-1" style="background: var(--nx-mint); color: var(--nx-forest); font-weight: 700;">
                                    <i class="fas fa-check-double me-1"></i>Diterima RW
                                </span>
                            @elseif($data['status_laporan'] == 'menunggu')
                                <span class="badge rounded-pill px-3 py-1" style="background: var(--nx-warning-soft); color: var(--nx-warning); font-weight: 700;">
                                    <i class="fas fa-hourglass-half me-1"></i>Menunggu Review
                                </span>
                            @elseif($data['status_laporan'] == 'perlu_revisi')
                                <span class="badge rounded-pill px-3 py-1" style="background: var(--nx-danger-soft); color: var(--nx-danger); font-weight: 700;">
                                    <i class="fas fa-triangle-exclamation me-1"></i>Perlu Revisi
                                </span>
                            @else
                                <span class="badge rounded-pill px-3 py-1" style="background: #E9ECEF; color: #6C757D; font-weight: 700;">
                                    <i class="fas fa-circle-minus me-1"></i>Belum Diserahkan
                                </span>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            <a href="{{ route('monitoring.rt_detail', $rt) }}" class="btn btn-sm btn-outline-primary" style="border-radius: 8px; font-weight: 600;">
                                <i class="fas fa-chart-pie me-1"></i> Periksa Detail
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- PERBANDINGAN ANTAR-RT (CHARTS) -->
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card p-4 h-100">
            <h6 class="fw-bold mb-3" style="color: var(--nx-forest);">
                <i class="fas fa-chart-column me-2"></i>Perbandingan Jumlah Warga &amp; KK Antar-RT
            </h6>
            <div style="height: 250px;">
                <canvas id="chartWargaRt"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card p-4 h-100">
            <h6 class="fw-bold mb-3" style="color: var(--nx-forest);">
                <i class="fas fa-chart-line me-2"></i>Perbandingan Saldo Kas &amp; Setoran Sampah Antar-RT
            </h6>
            <div style="height: 250px;">
                <canvas id="chartKasRt"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let rtLabels = @json(array_keys($rtData)).map(rt => 'RT ' + rt);
    let wargaData = @json(array_column($rtData, 'warga_count'));
    let kkData = @json(array_column($rtData, 'kk_count'));
    let kasData = @json(array_column($rtData, 'saldo_kas')).map(val => val / 1000000); // in jutaan

    // Chart 1: Warga & KK
    new Chart(document.getElementById('chartWargaRt').getContext('2d'), {
        type: 'bar',
        data: {
            labels: rtLabels,
            datasets: [
                {
                    label: 'Total Warga (Jiwa)',
                    data: wargaData,
                    backgroundColor: '#285943',
                    borderRadius: 6,
                },
                {
                    label: 'Kepala Keluarga (KK)',
                    data: kkData,
                    backgroundColor: '#A8C9B5',
                    borderRadius: 6,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'top' } }
        }
    });

    // Chart 2: Kas
    new Chart(document.getElementById('chartKasRt').getContext('2d'), {
        type: 'line',
        data: {
            labels: rtLabels,
            datasets: [
                {
                    label: 'Saldo Kas RT (Juta Rp)',
                    data: kasData,
                    borderColor: '#2b567a',
                    backgroundColor: 'rgba(169, 201, 232, 0.35)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 5,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'top' } }
        }
    });
});
</script>
@endsection
