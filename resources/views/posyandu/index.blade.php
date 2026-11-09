@extends('layouts.app')

@section('title', 'Posyandu Digital — NexaNest')

@section('content')
<div class="nx-page-header">
    <div>
        <span class="badge rounded-pill mb-2 px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
            <i class="fas fa-heart-pulse me-1"></i> Layanan Kesehatan Lingkungan
        </span>
        <h1 class="nx-page-title">Informasi &amp; Rekam Posyandu Digital</h1>
        <p class="nx-page-subtitle">Pantau jadwal posyandu, imunisasi balita, kurva KMS, serta kesehatan ibu hamil di lingkungan RW 018.</p>
    </div>
    <a href="{{ route('posyandu.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Tambah Jadwal / Rekam Medis
    </a>
</div>

<!-- 1. JADWAL KEGIATAN & PEMERIKSAAN POSYANDU -->
<div class="card p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div class="d-flex align-items-center gap-2">
            <span class="rounded-3 d-inline-flex align-items-center justify-content-center"
                  style="width: 36px; height: 36px; background: var(--nx-mint); color: var(--nx-forest);">
                <i class="far fa-calendar-check"></i>
            </span>
            <div>
                <h5 class="fw-bold mb-0" style="font-size: 1rem;">Kalender &amp; Jadwal Kegiatan Posyandu</h5>
                <small class="text-muted">Jadwal pemeriksaan rutin balita, lansia, dan ibu hamil</small>
            </div>
        </div>
        <a href="{{ route('posyandu.create') }}" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-plus me-1"></i> Jadwal Baru
        </a>
    </div>

    @if($jadwal->isEmpty())
        <div class="p-4 rounded-4 text-center" style="background: var(--nx-bg); border: 1px dashed var(--nx-border);">
            <p class="text-muted small mb-0">Belum ada jadwal kegiatan Posyandu mendatang yang tercatat.</p>
        </div>
    @else
        <div class="row g-3">
            @foreach($jadwal as $ev)
                <div class="col-md-6 col-lg-3">
                    <div class="p-3 rounded-4 h-100 d-flex flex-column justify-content-between"
                         style="background: var(--nx-mint-soft); border: 1px solid var(--nx-sage);">
                        <div>
                            <span class="badge rounded-pill mb-2 px-3 py-1" style="background: var(--nx-forest); color: #fff; font-size: 0.7rem;">
                                <i class="far fa-clock me-1"></i>{{ \Carbon\Carbon::parse($ev->tanggal)->translatedFormat('d F Y') }}
                            </span>
                            <h6 class="fw-bold mb-1" style="color: var(--nx-text);">{{ $ev->nama }}</h6>
                            <small class="d-block text-muted mb-2">
                                <i class="fas fa-location-dot text-success me-1"></i>{{ $ev->lokasi ?? 'Balai Warga RW 018' }}
                            </small>
                        </div>
                        <p class="mb-0 text-muted small" style="font-size: 0.8rem; line-height: 1.4;">{{ $ev->keterangan }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<div class="row g-4 mb-4">
    <!-- 2. REKAM ANAK (BALITA) -->
    <div class="col-lg-6">
        <div class="card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="rounded-3 d-inline-flex align-items-center justify-content-center"
                          style="width: 36px; height: 36px; background: var(--nx-mint); color: var(--nx-forest);">
                        <i class="fas fa-baby"></i>
                    </span>
                    <h5 class="fw-bold mb-0" style="font-size: 1rem;">Tumbuh Kembang Anak (Balita)</h5>
                </div>
                <span class="badge rounded-pill px-3 py-1.5" style="background: var(--nx-mint); color: var(--nx-forest);">
                    {{ $anak->count() }} Terdaftar
                </span>
            </div>

            @if($anak->isEmpty())
                <div class="p-4 rounded-4 text-center" style="background: var(--nx-bg);">
                    <p class="text-muted small mb-0">Belum ada data pemeriksaan tumbuh kembang balita.</p>
                </div>
            @else
                <div class="accordion" id="accordionAnak">
                    @foreach($anak as $item)
                        @php
                            $badgeBg = 'var(--nx-mint)';
                            $badgeColor = 'var(--nx-forest)';
                            if ($item->status_tumbuh === 'Kurang Gizi') {
                                $badgeBg = 'var(--nx-warning-soft)';
                                $badgeColor = 'var(--nx-warning)';
                            } elseif ($item->status_tumbuh === 'Stunting') {
                                $badgeBg = 'var(--nx-soft-red)';
                                $badgeColor = 'var(--nx-danger)';
                            } elseif ($item->status_tumbuh === 'Kelebihan Berat Badan') {
                                $badgeBg = 'var(--nx-powder)';
                                $badgeColor = '#2B5F8E';
                            }
                        @endphp
                        <div class="card mb-2 overflow-hidden" style="border-radius: 14px !important;">
                            <div class="card-header p-3 d-flex justify-content-between align-items-center"
                                 data-bs-toggle="collapse"
                                 data-bs-target="#collapseAnak{{ $item->id }}"
                                 style="cursor: pointer;">
                                <div>
                                    <h6 class="fw-bold mb-0" style="color: var(--nx-text); font-size: 0.92rem;">{{ $item->nama_anak }}</h6>
                                    <small class="text-muted">Usia {{ $item->umur_bulan }} Bulan • {{ $item->berat_badan }} kg / {{ $item->tinggi_badan }} cm</small>
                                </div>
                                <span class="badge rounded-pill px-3 py-1" style="background: {{ $badgeBg }}; color: {{ $badgeColor }}; font-size: 0.72rem;">
                                    {{ $item->status_tumbuh }}
                                </span>
                            </div>

                            <div id="collapseAnak{{ $item->id }}" class="collapse" data-bs-parent="#accordionAnak">
                                <div class="card-body p-3" style="background: var(--nx-bg); font-size: 0.84rem;">
                                    <div class="p-3 rounded-3 bg-white border mb-2">
                                        <small class="fw-bold d-block mb-1" style="color: var(--nx-forest);">
                                            <i class="fas fa-lightbulb me-1"></i>Rekomendasi &amp; Solusi Gizi:
                                        </small>
                                        <p class="mb-0 text-muted">{{ $item->solusi ?? 'Belum ada catatan solusi.' }}</p>
                                    </div>

                                    <div class="p-3 rounded-3 bg-white border">
                                        <small class="fw-bold text-muted d-block mb-2"><i class="fas fa-syringe me-1"></i>Status Vaksinasi / Imunisasi:</small>
                                        <div class="d-flex flex-wrap gap-1.5">
                                            @php
                                                $allVaccines = ['BCG', 'Polio 1', 'Polio 2', 'Polio 3', 'DPT-HB-Hib 1', 'DPT-HB-Hib 2', 'DPT-HB-Hib 3', 'Campak-Rubela'];
                                                $checkedVaccines = is_array($item->imunisasi_checked) ? $item->imunisasi_checked : [];
                                            @endphp
                                            @foreach($allVaccines as $vax)
                                                @php $hasVax = in_array($vax, $checkedVaccines); @endphp
                                                <span class="badge rounded-pill px-2.5 py-1"
                                                      style="background: {{ $hasVax ? 'var(--nx-mint)' : 'var(--nx-bg)' }}; color: {{ $hasVax ? 'var(--nx-forest)' : 'var(--nx-muted)' }}; border: 1px solid var(--nx-border); font-size: 0.7rem;">
                                                    <i class="fas {{ $hasVax ? 'fa-check-circle me-1' : 'fa-clock me-1' }}"></i>{{ $vax }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- 3. REKAM IBU HAMIL (BUMIL) -->
    <div class="col-lg-6">
        <div class="card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="rounded-3 d-inline-flex align-items-center justify-content-center"
                          style="width: 36px; height: 36px; background: var(--nx-powder); color: #2B5F8E;">
                        <i class="fas fa-person-pregnant"></i>
                    </span>
                    <h5 class="fw-bold mb-0" style="font-size: 1rem;">Kesehatan Ibu Hamil</h5>
                </div>
                <span class="badge rounded-pill px-3 py-1.5" style="background: var(--nx-powder); color: #2B5F8E;">
                    {{ $bumils->count() }} Terdaftar
                </span>
            </div>

            @if($bumils->isEmpty())
                <div class="p-4 rounded-4 text-center" style="background: var(--nx-bg);">
                    <p class="text-muted small mb-0">Belum ada data pemeriksaan kesehatan ibu hamil.</p>
                </div>
            @else
                <div class="accordion" id="accordionBumil">
                    @foreach($bumils as $item)
                        @php
                            $bBg = 'var(--nx-mint)';
                            $bColor = 'var(--nx-forest)';
                            if ($item->status_kesehatan === 'Kekurangan Energi Kronis (KEK)') {
                                $bBg = 'var(--nx-warning-soft)';
                                $bColor = 'var(--nx-warning)';
                            } elseif ($item->status_kesehatan === 'Hipertensi Gestasional') {
                                $bBg = 'var(--nx-soft-red)';
                                $bColor = 'var(--nx-danger)';
                            } elseif ($item->status_kesehatan === 'Anemia Ringan') {
                                $bBg = 'var(--nx-powder)';
                                $bColor = '#2B5F8E';
                            }
                        @endphp
                        <div class="card mb-2 overflow-hidden" style="border-radius: 14px !important;">
                            <div class="card-header p-3 d-flex justify-content-between align-items-center"
                                 data-bs-toggle="collapse"
                                 data-bs-target="#collapseBumil{{ $item->id }}"
                                 style="cursor: pointer;">
                                <div>
                                    <h6 class="fw-bold mb-0" style="color: var(--nx-text); font-size: 0.92rem;">{{ $item->nama_ibu }}</h6>
                                    <small class="text-muted">Usia Kehamilan {{ $item->usia_kehamilan_minggu }} Minggu • LiLA {{ $item->lila }} cm</small>
                                </div>
                                <span class="badge rounded-pill px-3 py-1" style="background: {{ $bBg }}; color: {{ $bColor }}; font-size: 0.72rem;">
                                    {{ $item->status_kesehatan }}
                                </span>
                            </div>

                            <div id="collapseBumil{{ $item->id }}" class="collapse" data-bs-parent="#accordionBumil">
                                <div class="card-body p-3" style="background: var(--nx-bg); font-size: 0.84rem;">
                                    <div class="row g-2 text-center mb-2">
                                        <div class="col-4">
                                            <div class="p-2 bg-white rounded-3 border">
                                                <span class="d-block text-muted" style="font-size: 0.7rem;">Berat Badan</span>
                                                <span class="fw-bold">{{ $item->berat_badan }} kg</span>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="p-2 bg-white rounded-3 border">
                                                <span class="d-block text-muted" style="font-size: 0.7rem;">Tensi Darah</span>
                                                <span class="fw-bold">{{ $item->tekanan_darah }}</span>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="p-2 bg-white rounded-3 border">
                                                <span class="d-block text-muted" style="font-size: 0.7rem;">LiLA (Lengan)</span>
                                                <span class="fw-bold">{{ $item->lila }} cm</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-3 rounded-3 bg-white border mb-2">
                                        <small class="fw-bold d-block mb-1" style="color: var(--nx-forest);">
                                            <i class="fas fa-user-doctor me-1"></i>Catatan &amp; Tindakan Medis:
                                        </small>
                                        <p class="mb-0 text-muted">{{ $item->solusi ?? 'Belum ada catatan solusi.' }}</p>
                                    </div>

                                    <div class="p-3 rounded-3 bg-white border" style="border-left: 4px solid #6AA6D8 !important;">
                                        <small class="fw-bold d-block mb-1" style="color: #2B5F8E;">
                                            <i class="fas fa-baby-carriage me-1"></i>Tahap Perkembangan Janin:
                                        </small>
                                        <p class="mb-0 text-muted">
                                            @if($item->usia_kehamilan_minggu <= 12)
                                                <strong>Trimester I (Awal):</strong> Pembentukan awal sistem saraf dan organ vital janin berlangsung intensif.
                                            @elseif($item->usia_kehamilan_minggu <= 26)
                                                <strong>Trimester II (Menengah):</strong> Janin mulai aktif bergerak dan merespons suara dari luar.
                                            @else
                                                <strong>Trimester III (Akhir):</strong> Pematangan fungsi paru-paru dan persiapan posisi jalan lahir.
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<!-- KMS GRAPH -->
<div class="card p-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <h5 class="fw-bold mb-0" style="font-size: 1rem;">
            <i class="fas fa-chart-area text-success me-2"></i>Kurva Kartu Menuju Sehat (KMS) Digital Balita
        </h5>
        <div class="d-flex gap-3 small fw-semibold">
            <span><i class="fas fa-circle me-1" style="color: #285943;"></i> Berat Badan (kg)</span>
            <span><i class="fas fa-circle me-1" style="color: #5B9BD5;"></i> Tinggi Badan (cm)</span>
        </div>
    </div>
    <canvas id="kmsChart" height="105"></canvas>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('kmsChart');
    if (!ctx) return;

    const data = @json($anak->map(function($item){ return ['nama' => $item->nama_anak, 'berat' => (float) $item->berat_badan, 'tinggi' => (float) $item->tinggi_badan]; }));
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.map(item => item.nama),
            datasets: [
                {
                    label: 'Berat Badan (kg)',
                    data: data.map(item => item.berat),
                    borderColor: '#285943',
                    backgroundColor: 'rgba(40, 89, 67, 0.12)',
                    tension: 0.35,
                    borderWidth: 2.5,
                    fill: true
                },
                {
                    label: 'Tinggi Badan (cm)',
                    data: data.map(item => item.tinggi),
                    borderColor: '#5B9BD5',
                    backgroundColor: 'rgba(91, 155, 213, 0.12)',
                    tension: 0.35,
                    borderWidth: 2.5,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    ticks: { color: '#718096' },
                    grid: { display: false }
                },
                y: {
                    ticks: { color: '#718096' },
                    grid: { color: '#EDF3EE' }
                }
            }
        }
    });
});
</script>
@endpush
