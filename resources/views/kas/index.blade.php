@extends('layouts.app')

@section('title', 'Kas RT')

@section('content')
<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card">
            <div class="card-header bg-white text-center">
                <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Laporan Kas RT</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <h6 class="text-muted">Total Kas Saat Ini</h6>
                    <h2 class="text-success">Rp {{ number_format($kas->balance ?? 0, 0, ',', '.') }}</h2>
                </div>

                <hr>

                <div class="row text-center">
                    <div class="col-6">
                        <h6 class="text-muted">Total Pemasukan</h6>
                        <h5 class="text-primary">Rp {{ number_format($kas->total_income ?? 0, 0, ',', '.') }}</h5>
                    </div>
                    <div class="col-6">
                        <h6 class="text-muted">Total Pengeluaran</h6>
                        <h5 class="text-danger">Rp {{ number_format($kas->total_expense ?? 0, 0, ',', '.') }}</h5>
                    </div>
                </div>

                <hr>

                <div class="mt-3">
                    <h6>Statistik</h6>
                    <p>Total Warga: <strong>{{ $totalWarga ?? 0 }}</strong> orang</p>
                    <p>Total Pembayaran Terkonfirmasi: <strong>{{ $totalSudahBayar ?? 0 }}</strong> transaksi</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
