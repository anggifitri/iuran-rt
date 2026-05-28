@extends('layouts.app')

@section('title', 'Manajemen Kas & Pembayaran')

@section('content')
<div class="card shadow border-0 bg-dark text-white" style="border-radius: 15px;">
    <div class="card-header bg-transparent border-bottom border-secondary d-flex justify-content-between align-items-center p-3">
        <h5 class="mb-0 text-white"><i class="fas fa-history me-2 text-primary"></i>Riwayat Transaksi Kas</h5>

        <div class="d-flex gap-2">
            <a href="{{ route('laporan.cetak') }}" class="btn btn-outline-light btn-sm">
                <i class="fas fa-file-pdf me-1"></i> Cetak PDF
            </a>
            <a href="{{ route('pembayaran.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Tambah Transaksi
            </a>
        </div>
    </div>

    <div class="card-body p-0"> <div class="table-responsive">
            <table class="table table-dark table-hover mb-0 align-middle">
                <thead class="text-white-50">
                    <tr class="border-bottom border-secondary">
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3">Keterangan / Warga</th>
                        <th class="py-3">Kategori</th>
                        <th class="py-3">Tipe</th>
                        <th class="py-3">Jumlah</th>
                        <th class="py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pembayarans as $p)
                    <tr class="border-bottom border-secondary">
                        <td class="align-middle px-4">{{ \Carbon\Carbon::parse($p->tanggal)->format('d M Y') }}</td>
                        <td class="align-middle">
                            <div class="fw-bold text-white">{{ $p->warga->nama ?? 'Umum / Non-Warga' }}</div>
                            <small class="text-white-50">{{ $p->keterangan ?? '-' }}</small>
                        </td>
                        <td class="align-middle">
                            <span class="badge bg-secondary">{{ $p->kategori }}</span>
                        </td>
                        <td class="align-middle">
                            @if($p->tipe == 'masuk')
                                <span class="badge bg-success"><i class="fas fa-arrow-down me-1"></i>Masuk</span>
                            @else
                                <span class="badge bg-danger"><i class="fas fa-arrow-up me-1"></i>Keluar</span>
                            @endif
                        </td>
                        <td class="align-middle fw-bold {{ $p->tipe == 'masuk' ? 'text-success' : 'text-danger' }}">
                            {{ $p->tipe == 'masuk' ? '+' : '-' }} Rp {{ number_format($p->jumlah, 0, ',', '.') }}
                        </td>
                        <td class="text-center align-middle">
                            <form action="{{ route('pembayaran.destroy', $p->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus transaksi ini?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-white-50">Belum ada data transaksi kas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3">
            {{ $pembayarans->links() }}
        </div>
    </div>
</div>
@endsection
