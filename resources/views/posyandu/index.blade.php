@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark" style="color: var(--text-main) !important;">NexaNest · Posyandu Digital</h2>
            <p class="text-muted mb-0">Pantau secara rutin imunisasi balita, tumbuh kembang anak, serta kesehatan ibu hamil di RT 006-010 RW 018.</p>
        </div>
    </div>

    <!-- 1. JADWAL KEGIATAN & PEMERIKSAAN POSYANDU -->
    <div class="card p-4 shadow-sm border-0 mb-4" style="border-radius: 16px; background-color: var(--bg-card); border: 1px solid var(--border-color);">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0 text-primary"><i class="fas fa-calendar-alt me-2"></i>Kalender & Jadwal Kegiatan Posyandu</h5>
            <a href="{{ route('posyandu.create') }}" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus me-1"></i>Tambah Jadwal</a>
        </div>
        
        @if($jadwal->isEmpty())
            <p class="text-muted mb-0">Belum ada jadwal posyandu mendatang.</p>
        @else
            <div class="row g-3">
                @foreach($jadwal as $ev)
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 border border-light" style="border-radius: 12px; background: rgba(59,130,246,0.03);">
                            <div class="card-body p-3">
                                <span class="badge bg-primary mb-2" style="font-size: 0.65rem;">
                                    <i class="fas fa-clock me-1"></i>{{ \Carbon\Carbon::parse($ev->tanggal)->translatedFormat('d F Y') }}
                                </span>
                                <h6 class="fw-bold text-dark mb-1" style="color: var(--text-main) !important;">{{ $ev->nama }}</h6>
                                <small class="d-block text-muted mb-2"><i class="fas fa-map-marker-alt me-1"></i>{{ $ev->lokasi ?? '-' }}</small>
                                <p class="mb-0 text-secondary" style="font-size: 0.8rem; line-height: 1.3;">{{ $ev->keterangan }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="row g-4 mb-4">
        <!-- 2. REKAM ANAK (BALITA) -->
        <div class="col-lg-6">
            <div class="card p-4 shadow-sm border-0 h-100" style="border-radius: 16px; background-color: var(--bg-card); border: 1px solid var(--border-color);">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0 text-success"><i class="fas fa-baby me-2"></i>Rekam Medis & Tumbuh Kembang Anak</h5>
                    <span class="badge bg-success rounded-pill">{{ $anak->count() }} Terdaftar</span>
                </div>
                
                @if($anak->isEmpty())
                    <p class="text-muted mb-0">Belum ada data pemeriksaan anak.</p>
                @else
                    <div class="accordion" id="accordionAnak">
                        @foreach($anak as $item)
                            @php
                                $statusClass = 'bg-success';
                                if ($item->status_tumbuh === 'Kurang Gizi') $statusClass = 'bg-warning';
                                if ($item->status_tumbuh === 'Stunting') $statusClass = 'bg-danger';
                                if ($item->status_tumbuh === 'Kelebihan Berat Badan') $statusClass = 'bg-purple';
                            @endphp
                            <div class="card mb-2 border border-light" style="border-radius: 10px; overflow: hidden;">
                                <div class="card-header bg-white p-3 d-flex justify-content-between align-items-center cursor-pointer" 
                                     data-bs-toggle="collapse" 
                                     data-bs-target="#collapseAnak{{ $item->id }}" 
                                     style="cursor: pointer; border-bottom: none;">
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark" style="color: var(--text-main) !important;">{{ $item->nama_anak }}</h6>
                                        <small class="text-muted">{{ $item->umur_bulan }} Bulan | {{ $item->berat_badan }} kg / {{ $item->tinggi_badan }} cm</small>
                                    </div>
                                    <span class="badge {{ $statusClass }} rounded-pill" style="font-size: 0.7rem;">{{ $item->status_tumbuh }}</span>
                                </div>

                                <div id="collapseAnak{{ $item->id }}" class="collapse" data-bs-parent="#accordionAnak">
                                    <div class="card-body bg-light border-top p-3" style="font-size: 0.85rem;">
                                        <!-- Solusi Tumbuh Kembang -->
                                        <div class="p-3 rounded bg-white border border-light mb-3 shadow-sm">
                                            <small class="fw-bold text-success d-block mb-1"><i class="fas fa-lightbulb me-1"></i>Rekomendasi & Solusi Gizi:</small>
                                            <p class="mb-0 text-secondary" style="line-height: 1.4;">{{ $item->solusi ?? 'Belum ada catatan solusi.' }}</p>
                                        </div>

                                        <!-- Tracker Imunisasi -->
                                        <div class="p-3 rounded bg-white border border-light shadow-sm">
                                            <small class="fw-bold text-secondary d-block mb-2"><i class="fas fa-syringe me-1"></i>Status Vaksinasi / Imunisasi Anak:</small>
                                            <div class="d-flex flex-wrap gap-2">
                                                @php
                                                    $allVaccines = ['BCG', 'Polio 1', 'Polio 2', 'Polio 3', 'DPT-HB-Hib 1', 'DPT-HB-Hib 2', 'DPT-HB-Hib 3', 'Campak-Rubela'];
                                                    $checkedVaccines = is_array($item->imunisasi_checked) ? $item->imunisasi_checked : [];
                                                @endphp
                                                @foreach($allVaccines as $vax)
                                                    @php $hasVax = in_array($vax, $checkedVaccines); @endphp
                                                    <span class="badge {{ $hasVax ? 'bg-success text-white' : 'bg-secondary-subtle text-muted' }} rounded-pill border" style="font-size: 0.7rem; padding: 4px 10px;">
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
            <div class="card p-4 shadow-sm border-0 h-100" style="border-radius: 16px; background-color: var(--bg-card); border: 1px solid var(--border-color);">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0 text-danger"><i class="fas fa-heartbeat me-2"></i>Rekam Medis & Kesehatan Ibu Hamil</h5>
                    <span class="badge bg-danger rounded-pill">{{ $bumils->count() }} Terdaftar</span>
                </div>
                
                @if($bumils->isEmpty())
                    <p class="text-muted mb-0">Belum ada data pemeriksaan ibu hamil.</p>
                @else
                    <div class="accordion" id="accordionBumil">
                        @foreach($bumils as $item)
                            @php
                                $statusBumilClass = 'bg-success';
                                if ($item->status_kesehatan === 'Kekurangan Energi Kronis (KEK)') $statusBumilClass = 'bg-warning';
                                if ($item->status_kesehatan === 'Hipertensi Gestasional') $statusBumilClass = 'bg-danger';
                                if ($item->status_kesehatan === 'Anemia Ringan') $statusBumilClass = 'bg-purple';
                            @endphp
                            <div class="card mb-2 border border-light" style="border-radius: 10px; overflow: hidden;">
                                <div class="card-header bg-white p-3 d-flex justify-content-between align-items-center cursor-pointer" 
                                     data-bs-toggle="collapse" 
                                     data-bs-target="#collapseBumil{{ $item->id }}" 
                                     style="cursor: pointer; border-bottom: none;">
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark" style="color: var(--text-main) !important;">{{ $item->nama_ibu }}</h6>
                                        <small class="text-muted">Kehamilan: {{ $item->usia_kehamilan_minggu }} Minggu | LiLA: {{ $item->lila }} cm</small>
                                    </div>
                                    <span class="badge {{ $statusBumilClass }} rounded-pill" style="font-size: 0.7rem;">{{ $item->status_kesehatan }}</span>
                                </div>

                                <div id="collapseBumil{{ $item->id }}" class="collapse" data-bs-parent="#accordionBumil">
                                    <div class="card-body bg-light border-top p-3" style="font-size: 0.85rem;">
                                        <!-- Vital Stats -->
                                        <div class="row g-2 text-center mb-3">
                                            <div class="col-4">
                                                <div class="p-2 bg-white rounded border shadow-sm">
                                                    <span class="d-block text-muted" style="font-size: 0.7rem;">Berat Badan</span>
                                                    <span class="fw-bold text-dark">{{ $item->berat_badan }} kg</span>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="p-2 bg-white rounded border shadow-sm">
                                                    <span class="d-block text-muted" style="font-size: 0.7rem;">Tensi Darah</span>
                                                    <span class="fw-bold text-dark">{{ $item->tekanan_darah }}</span>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="p-2 bg-white rounded border shadow-sm">
                                                    <span class="d-block text-muted" style="font-size: 0.7rem;">LiLA (Lengan)</span>
                                                    <span class="fw-bold text-dark">{{ $item->lila }} cm</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Solusi Bidan/Dokter -->
                                        <div class="p-3 rounded bg-white border border-light mb-3 shadow-sm">
                                            <small class="fw-bold text-danger d-block mb-1"><i class="fas fa-user-md me-1"></i>Catatan & Tindakan Medis Ibu:</small>
                                            <p class="mb-0 text-secondary" style="line-height: 1.4;">{{ $item->solusi ?? 'Belum ada catatan solusi.' }}</p>
                                        </div>

                                        <!-- Perkembangan Janin Trimester -->
                                        <div class="p-3 rounded bg-white border border-light shadow-sm border-start border-4 border-info">
                                            <small class="fw-bold text-info d-block mb-1"><i class="fas fa-baby-carriage me-1"></i>Tahap Perkembangan Bayi (Fetus):</small>
                                            <p class="mb-0 text-secondary" style="line-height: 1.4;">
                                                @if($item->usia_kehamilan_minggu <= 12)
                                                    <strong>Trimester I (Awal):</strong> Ukuran janin berkisar dari biji poppy hingga jeruk nipis. Detak jantung janin mulai terbentuk sejak minggu ke-6. Pembentukan awal sistem saraf, tangan, dan organ vital sedang berlangsung intensif.
                                                @elseif($item->usia_kehamilan_minggu <= 26)
                                                    <strong>Trimester II (Menengah):</strong> Ukuran janin seperti kelapa kupas. Bayi sudah dapat mendengar denyut jantung ibu dan suara luar, serta mulai merespon rangsangan cahaya. Tendangan dan pergerakan tangan janin mulai aktif terasa oleh ibu.
                                                @else
                                                    <strong>Trimester III (Akhir):</strong> Ukuran janin seperti semangka. Seluruh organ vital (terutama paru-paru) mematangkan fungsinya. Posisi kepala mulai berputar ke arah bawah pintu panggul bersiap untuk menyongsong proses persalinan harian.
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
    <div class="card p-4 shadow-sm border-0" style="border-radius: 16px; background-color: var(--bg-card); border: 1px solid var(--border-color);">
        <h5 class="fw-bold mb-3 text-dark" style="color: var(--text-main) !important;"><i class="fas fa-chart-line me-2"></i>Kurva Kartu Menuju Sehat (KMS) Digital</h5>
        <canvas id="kmsChart" height="120"></canvas>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('kmsChart');
if (ctx) {
    const data = @json($anak->map(function($item){ return ['nama' => $item->nama_anak, 'berat' => (float) $item->berat_badan, 'tinggi' => (float) $item->tinggi_badan]; }));
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.map(item => item.nama),
            datasets: [
                {
                    label: 'Berat Badan (kg)',
                    data: data.map(item => item.berat),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16,185,129,0.15)',
                    tension: 0.3,
                    borderWidth: 2
                },
                {
                    label: 'Tinggi Badan (cm)',
                    data: data.map(item => item.tinggi),
                    borderColor: '#ec4899',
                    backgroundColor: 'rgba(236,72,153,0.15)',
                    tension: 0.3,
                    borderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { 
                legend: { 
                    labels: { 
                        color: getComputedStyle(document.documentElement).getPropertyValue('--text-main').trim() || '#333'
                    } 
                } 
            },
            scales: { 
                x: { 
                    ticks: { 
                        color: getComputedStyle(document.documentElement).getPropertyValue('--text-muted').trim() || '#666'
                    },
                    grid: {
                        color: getComputedStyle(document.documentElement).getPropertyValue('--border-color').trim() || 'rgba(0,0,0,0.05)'
                    }
                }, 
                y: { 
                    ticks: { 
                        color: getComputedStyle(document.documentElement).getPropertyValue('--text-muted').trim() || '#666'
                    },
                    grid: {
                        color: getComputedStyle(document.documentElement).getPropertyValue('--border-color').trim() || 'rgba(0,0,0,0.05)'
                    }
                } 
            }
        }
    });
}
</script>
@endpush
