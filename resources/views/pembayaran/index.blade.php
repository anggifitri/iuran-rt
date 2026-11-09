@extends('layouts.app')

@section('title', 'Transaksi Kas — NexaNest')

@section('content')
<div class="nx-page-header">
    <div>
        <span class="badge rounded-pill mb-2 px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
            <i class="fas fa-wallet me-1"></i> Keuangan Lingkungan
        </span>
        <h1 class="nx-page-title">Riwayat Transaksi Kas</h1>
        <p class="nx-page-subtitle">Kelola dan pantau pencatatan iuran masuk serta pengeluaran operasional lingkungan RT/RW.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('laporan.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-chart-column me-1"></i> Laporan Bulanan
        </a>
        <a href="{{ route('pembayaran.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Catat Transaksi Baru
        </a>
    </div>
</div>

<!-- GRAFIK TREN KAS -->
<div class="card p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div class="d-flex align-items-center gap-2">
            <span class="rounded-3 d-inline-flex align-items-center justify-content-center"
                  style="width: 36px; height: 36px; background: var(--nx-mint); color: var(--nx-forest);">
                <i class="fas fa-chart-line"></i>
            </span>
            <div>
                <h5 class="fw-bold mb-0" style="font-size: 1rem;">Tren Arus Kas Bulanan (6 Bulan Terakhir)</h5>
                <small class="text-muted">Perbandingan realisasi pemasukan dan pengeluaran kas</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3 small fw-semibold">
            <span><i class="fas fa-circle me-1" style="color: #285943;"></i> Pemasukan</span>
            <span><i class="fas fa-circle me-1" style="color: #5B9BD5;"></i> Pengeluaran</span>
        </div>
    </div>
    <div style="height: 250px; position: relative;">
        <canvas id="kasChart"></canvas>
    </div>
</div>

<!-- TABEL RIWAYAT TRANSAKSI BERDASARKAN BULAN -->
<div class="card overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4" style="width: 15%;">Tanggal</th>
                    <th style="width: 35%;">Keterangan / Warga</th>
                    <th style="width: 15%;">Kategori</th>
                    <th style="width: 12%;">Tipe</th>
                    <th style="width: 15%;">Jumlah Nominal</th>
                    <th class="pe-4 text-end" style="width: 8%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $groupedPembayarans = $pembayarans->groupBy(function($item) {
                        return \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y');
                    });
                @endphp

                @forelse($groupedPembayarans as $bulan => $items)
                    <tr style="background: var(--nx-bg);">
                        <td colspan="6" class="py-2.5 ps-4">
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <span class="fw-bold" style="color: var(--nx-forest);">
                                    <i class="far fa-calendar-check me-1"></i> Periode {{ $bulan }}
                                </span>
                                <span class="badge rounded-pill px-3 py-1" style="background: var(--nx-mint); color: var(--nx-forest);">
                                    Masuk: +Rp {{ number_format($items->where('tipe', 'masuk')->sum('jumlah'), 0, ',', '.') }}
                                </span>
                                <span class="badge rounded-pill px-3 py-1" style="background: var(--nx-powder); color: #2B5F8E;">
                                    Keluar: -Rp {{ number_format($items->where('tipe', 'keluar')->sum('jumlah'), 0, ',', '.') }}
                                </span>
                            </div>
                        </td>
                    </tr>

                    @foreach($items as $p)
                        <tr>
                            <td class="ps-4 small fw-semibold">
                                {{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d M Y') }}
                            </td>
                            <td>
                                <span class="fw-bold d-block" style="color: var(--nx-text);">
                                    {{ $p->warga->nama ?? 'Kas Umum / Non-Warga' }}
                                </span>
                                <small class="text-muted d-block">{{ $p->keterangan ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="badge rounded-pill px-3 py-1" style="background: var(--nx-bg); color: var(--nx-text); border: 1px solid var(--nx-border);">
                                    {{ $p->kategori }}
                                </span>
                            </td>
                            <td>
                                @if($p->tipe == 'masuk')
                                    <span class="badge rounded-pill px-3 py-1" style="background: var(--nx-mint); color: var(--nx-forest);">
                                        <i class="fas fa-arrow-down me-1"></i>Masuk
                                    </span>
                                @else
                                    <span class="badge rounded-pill px-3 py-1" style="background: var(--nx-soft-red); color: var(--nx-danger);">
                                        <i class="fas fa-arrow-up me-1"></i>Keluar
                                    </span>
                                @endif
                            </td>
                            <td>
                                <strong style="color: {{ $p->tipe == 'masuk' ? 'var(--nx-forest)' : 'var(--nx-danger)' }};">
                                    {{ $p->tipe == 'masuk' ? '+' : '-' }} Rp {{ number_format($p->jumlah, 0, ',', '.') }}
                                </strong>
                            </td>
                            <td class="pe-4 text-end">
                                <form action="{{ route('pembayaran.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pencatatan transaksi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 60px; height: 60px; background: var(--nx-mint-soft); color: var(--nx-forest);">
                                <i class="fas fa-wallet fa-lg"></i>
                            </div>
                            <h6 class="fw-bold mb-1">Belum Ada Transaksi Kas</h6>
                            <p class="text-muted small mb-0">Klik tombol Catat Transaksi Baru di atas untuk memulai pencatatan kas.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 d-flex justify-content-center border-top">
        {{ $pembayarans->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('kasChart')?.getContext('2d');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartData->pluck('bulan_tahun')) !!},
            datasets: [
                {
                    label: 'Pemasukan (Masuk)',
                    data: {!! json_encode($chartData->pluck('masuk')) !!},
                    backgroundColor: '#285943',
                    borderRadius: 8,
                    maxBarThickness: 32
                },
                {
                    label: 'Pengeluaran (Keluar)',
                    data: {!! json_encode($chartData->pluck('keluar')) !!},
                    backgroundColor: '#6AA6D8',
                    borderRadius: 8,
                    maxBarThickness: 32
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#718096', font: { family: 'Plus Jakarta Sans' } }
                },
                y: {
                    grid: { color: '#EDF3EE' },
                    ticks: { color: '#718096', font: { family: 'Plus Jakarta Sans' } }
                }
            }
        }
    });
});
</script>
@endpush
