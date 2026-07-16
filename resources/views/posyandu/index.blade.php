@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h4 class="fw-bold">NexaNest · Info Posyandu Digital</h4>
    <p class="text-muted">Pantau perkembangan anak dan ibu hamil secara terintegrasi dengan data yang langsung terplot.</p>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card p-4 shadow-sm border-0">
                <h5 class="fw-bold">Rekam Anak</h5>
                @if($anak->isEmpty())
                    <p class="text-muted mb-0">Belum ada data anak.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead><tr><th>Nama</th><th>Umur</th><th>Status</th></tr></thead>
                            <tbody>
                                @foreach($anak as $item)
                                    <tr>
                                        <td>{{ $item->nama_anak }}</td>
                                        <td>{{ $item->umur_bulan }} bulan</td>
                                        <td>{{ $item->status_tumbuh }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card p-4 shadow-sm border-0">
                <h5 class="fw-bold">Rekam Ibu Hamil</h5>
                @if($bumils->isEmpty())
                    <p class="text-muted mb-0">Belum ada data ibu hamil.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead><tr><th>Nama</th><th>Usia Kehamilan</th><th>Status</th></tr></thead>
                            <tbody>
                                @foreach($bumils as $item)
                                    <tr>
                                        <td>{{ $item->nama_ibu }}</td>
                                        <td>{{ $item->usia_kehamilan_minggu }} minggu</td>
                                        <td>{{ $item->status_kesehatan }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="card p-4 shadow-sm border-0">
        <h5 class="fw-bold mb-3">Grafik KMS Digital</h5>
        <canvas id="kmsChart" height="180"></canvas>
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
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.15)',
                    tension: 0.3
                },
                {
                    label: 'Tinggi Badan (cm)',
                    data: data.map(item => item.tinggi),
                    borderColor: '#ec4899',
                    backgroundColor: 'rgba(236,72,153,0.15)',
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { labels: { color: getComputedStyle(document.documentElement).getPropertyValue('--text-main').trim() } } },
            scales: { x: { ticks: { color: getComputedStyle(document.documentElement).getPropertyValue('--text-muted').trim() } }, y: { ticks: { color: getComputedStyle(document.documentElement).getPropertyValue('--text-muted').trim() } } }
        }
    });
}
</script>
@endpush
