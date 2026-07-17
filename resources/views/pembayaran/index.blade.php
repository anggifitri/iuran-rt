@extends('layouts.app')

@section('title', 'Riwayat Transaksi Kas')

@section('content')
<div class="container py-4">
    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark" style="color: var(--text-main) !important;">Riwayat Transaksi Kas</h2>
            <p class="text-muted mb-0">Kelola dan monitor pencatatan iuran masuk serta pengeluaran kas lingkungan.</p>
        </div>
        <a href="{{ route('pembayaran.create') }}" class="btn btn-primary btn-lg shadow-sm px-4 py-2.5" style="border-radius: 12px; font-weight: 600;">
            <i class="fas fa-plus me-1"></i>Tambah Transaksi
        </a>
    </div>

    <!-- GRAFIK TREN KAS -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4" style="background-color: var(--bg-card); border: 1px solid var(--border-color) !important;">
        <h5 class="fw-bold text-dark mb-3" style="color: var(--text-main) !important;"><i class="fas fa-chart-bar text-primary me-2"></i>Tren Kas Bulanan (6 Bulan Terakhir)</h5>
        <div style="height: 250px; position: relative;">
            <canvas id="kasChart"></canvas>
        </div>
    </div>

    <!-- RIWAYAT TRANSAKSI BERDASARKAN BULAN -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background-color: var(--bg-card); border: 1px solid var(--border-color) !important;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="color: var(--text-main);">
                <thead style="background-color: rgba(59,130,246,0.03); color: var(--text-main); border-bottom: 2px solid var(--border-color);">
                    <tr>
                        <th class="ps-4" style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; opacity: 0.8; width: 15%;">Tanggal</th>
                        <th style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; opacity: 0.8; width: 35%;">Keterangan / Warga</th>
                        <th style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; opacity: 0.8; width: 15%;">Kategori</th>
                        <th style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; opacity: 0.8; width: 10%;">Tipe</th>
                        <th style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; opacity: 0.8; width: 15%;">Jumlah</th>
                        <th class="pe-4 text-end" style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; opacity: 0.8; width: 10%;">Aksi</th>
                    </tr>
                </thead>
                <tbody style="border-top: none;">
                    @php
                        // Mengelompokkan transaksi kas yang diambil ke dalam grup Bulan Tahun
                        $groupedPembayarans = $pembayarans->groupBy(function($item) {
                            return \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y');
                        });
                    @endphp

                    @forelse($groupedPembayarans as $bulan => $items)
                        <!-- Subheader Bulan -->
                        <tr style="background-color: rgba(59,130,246,0.02); font-weight: bold; border-bottom: 1.5px solid var(--border-color);">
                            <td colspan="6" class="py-3 ps-4 text-dark" style="color: var(--text-main) !important;">
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    <i class="fas fa-calendar-alt text-primary me-1"></i>
                                    <span>Bulan {{ $bulan }}</span>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle ms-2" style="font-size: 0.75rem;">
                                        Total Masuk: +Rp {{ number_format($items->where('tipe', 'masuk')->sum('jumlah'), 0, ',', '.') }}
                                    </span>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle ms-1" style="font-size: 0.75rem;">
                                        Total Keluar: -Rp {{ number_format($items->where('tipe', 'keluar')->sum('jumlah'), 0, ',', '.') }}
                                    </span>
                                </div>
                            </td>
                        </tr>

                        @foreach($items as $p)
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td class="ps-4" style="font-size: 0.9rem;">
                                    {{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d M Y') }}
                                </td>
                                <td>
                                    <span class="fw-bold d-block text-dark" style="color: var(--text-main) !important; font-size: 0.95rem;">
                                        {{ $p->warga->nama ?? 'Umum / Non-Warga' }}
                                    </span>
                                    <small class="text-muted d-block" style="font-size: 0.8rem;">{{ $p->keterangan ?? '-' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary border" style="font-size: 0.75rem;">
                                        {{ $p->kategori }}
                                    </span>
                                </td>
                                <td>
                                    @if($p->tipe == 'masuk')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.75rem;">
                                            <i class="fas fa-arrow-down me-1"></i>Masuk
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size: 0.75rem;">
                                            <i class="fas fa-arrow-up me-1"></i>Keluar
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <strong class="{{ $p->tipe == 'masuk' ? 'text-success' : 'text-danger' }}" style="font-size: 0.95rem;">
                                        {{ $p->tipe == 'masuk' ? '+' : '-' }} Rp {{ number_format($p->jumlah, 0, ',', '.') }}
                                    </strong>
                                </td>
                                <td class="pe-4 text-end">
                                    <form action="{{ route('pembayaran.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pencatatan transaksi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 6px;" title="Hapus"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-history fa-3x mb-3 text-secondary" style="opacity: 0.5;"></i>
                                <h5 class="fw-semibold mb-1">Belum Ada Transaksi Kas</h5>
                                <p class="small mb-0">Klik tombol Tambah Transaksi di atas untuk melakukan pencatatan kas baru.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- PAGINATION -->
        <div class="p-3 d-flex justify-content-center border-top" style="border-color: var(--border-color) !important;">
            {{ $pembayarans->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('kasChart').getContext('2d');
    
    // Konfigurasi Chart.js
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartData->pluck('bulan_tahun')) !!},
            datasets: [
                {
                    label: 'Pemasukan (Masuk)',
                    data: {!! json_encode($chartData->pluck('masuk')) !!},
                    backgroundColor: 'rgba(34, 197, 94, 0.75)',
                    borderColor: '#22c55e',
                    borderWidth: 1.5,
                    borderRadius: 6
                },
                {
                    label: 'Pengeluaran (Keluar)',
                    data: {!! json_encode($chartData->pluck('keluar')) !!},
                    backgroundColor: 'rgba(239, 68, 68, 0.75)',
                    borderColor: '#ef4444',
                    borderWidth: 1.5,
                    borderRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        color: '#64748b',
                        font: { family: 'Plus Jakarta Sans', weight: 'bold' }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#64748b' }
                },
                y: {
                    grid: { color: 'rgba(100, 116, 139, 0.1)' },
                    ticks: { color: '#64748b' }
                }
            }
        }
    });
});
</script>
@endpush
