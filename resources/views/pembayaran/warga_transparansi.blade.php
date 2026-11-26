@extends('layouts.app')

@section('title', 'Transparansi Kas Lingkungan — NexaNest')

@section('content')
@php
    $user = Auth::user();
@endphp

<!-- HEADER HALAMAN -->
<div class="nx-page-header">
    <div>
        <span class="badge rounded-pill mb-2 px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
            <i class="fas fa-shield-alt me-1"></i> Transparansi Publik RT {{ $user->rt_number ?? '000' }} &amp; RW 018
        </span>
        <h1 class="nx-page-title">Transparansi Kas Lingkungan</h1>
        <p class="nx-page-subtitle">Informasi keterbukaan arus kas pemasukan dan pengeluaran operasional lingkungan demi akuntabilitas bersama.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('laporan.iuran') }}" class="btn btn-primary">
            <i class="fas fa-file-invoice-dollar me-1"></i> Iuran Warga Saya
        </a>
    </div>
</div>

<!-- INFO BANNER -->
<div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4" style="background: #EBF4FC; color: #1E4F7D; border-radius: 12px;">
    <i class="fas fa-info-circle fs-4 me-3"></i>
    <div class="small">
        <strong>Prinsip Transparansi NexaNest:</strong> Seluruh warga berhak memantau realisasi perputaran kas lingkungan RW dan RT secara terbuka dan bertanggung jawab. Data transaksi di bawah ini telah disaring demi melindungi privasi identitas personal warga.
    </div>
</div>

<!-- 1. SUMMARY CARDS DUAL LEVEL (RW 018 & RT SAYA) -->
<div class="row g-3 mb-4">
    <!-- Kas Induk RW 018 -->
    <div class="col-lg-6">
        <div class="card p-4 h-100 border-0 shadow-sm" style="background: linear-gradient(135deg, #285943 0%, #1a3d2e 100%); color: #ffffff;">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <span class="badge rounded-pill px-2.5 py-1 mb-2" style="background: rgba(255,255,255,0.2); color: #ffffff; font-size: 0.72rem;">
                        <i class="fas fa-landmark me-1"></i> Kas Utama Lingkungan RW 018
                    </span>
                    <h3 class="fw-bold mb-0">Rp {{ number_format($rwSaldo, 0, ',', '.') }}</h3>
                    <small style="opacity: 0.85;">Saldo Akhir Kas Seluruh Wilayah RW 018</small>
                </div>
                <div class="rounded-3 d-flex align-items-center justify-content-center"
                     style="width: 44px; height: 44px; background: rgba(255,255,255,0.18);">
                    <i class="fas fa-vault fa-lg"></i>
                </div>
            </div>
            <div class="row g-2 pt-3 border-top" style="border-color: rgba(255,255,255,0.2) !important;">
                <div class="col-6">
                    <small style="opacity: 0.8; font-size: 0.72rem;">TOTAL PEMASUKAN RW</small>
                    <div class="fw-bold" style="font-size: 0.95rem;">+Rp {{ number_format($rwMasuk, 0, ',', '.') }}</div>
                </div>
                <div class="col-6">
                    <small style="opacity: 0.8; font-size: 0.72rem;">TOTAL PENGELUARAN RW</small>
                    <div class="fw-bold" style="font-size: 0.95rem;">-Rp {{ number_format($rwKeluar, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kas RT Saya -->
    <div class="col-lg-6">
        <div class="card p-4 h-100 border-0 shadow-sm" style="background: linear-gradient(135deg, #1f517f 0%, #153859 100%); color: #ffffff;">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <span class="badge rounded-pill px-2.5 py-1 mb-2" style="background: rgba(255,255,255,0.2); color: #ffffff; font-size: 0.72rem;">
                        <i class="fas fa-home me-1"></i> Kas Wilayah RT {{ $user->rt_number ?? '000' }}
                    </span>
                    <h3 class="fw-bold mb-0">Rp {{ number_format($rtSaldo, 0, ',', '.') }}</h3>
                    <small style="opacity: 0.85;">Saldo Akhir Kas Operasional RT {{ $user->rt_number ?? '000' }}</small>
                </div>
                <div class="rounded-3 d-flex align-items-center justify-content-center"
                     style="width: 44px; height: 44px; background: rgba(255,255,255,0.18);">
                    <i class="fas fa-wallet fa-lg"></i>
                </div>
            </div>
            <div class="row g-2 pt-3 border-top" style="border-color: rgba(255,255,255,0.2) !important;">
                <div class="col-6">
                    <small style="opacity: 0.8; font-size: 0.72rem;">PEMASUKAN RT SAYA</small>
                    <div class="fw-bold" style="font-size: 0.95rem;">+Rp {{ number_format($rtMasuk, 0, ',', '.') }}</div>
                </div>
                <div class="col-6">
                    <small style="opacity: 0.8; font-size: 0.72rem;">PENGELUARAN RT SAYA</small>
                    <div class="fw-bold" style="font-size: 0.95rem;">-Rp {{ number_format($rtKeluar, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 2. ALOKASI PENGELUARAN PUBLIK -->
<div class="card p-4 mb-4">
    <div class="d-flex align-items-center gap-2 mb-3">
        <span class="rounded-3 d-inline-flex align-items-center justify-content-center"
              style="width: 36px; height: 36px; background: var(--nx-powder); color: #1F517F;">
            <i class="fas fa-chart-pie"></i>
        </span>
        <div>
            <h5 class="fw-bold mb-0" style="font-size: 1rem;">Alokasi Pengeluaran Fasilitas &amp; Layanan Lingkungan</h5>
            <small class="text-muted">Rincian penggunaan kas warga untuk kebersihan, keamanan, dan perbaikan sarana umum</small>
        </div>
    </div>

    <div class="row g-3">
        @php
            $grandTotalKeluar = $rwKeluar > 0 ? $rwKeluar : 1;
        @endphp
        @forelse($pengeluaranKategori as $pk)
            @php
                $persen = round(($pk->total / $grandTotalKeluar) * 100, 1);
            @endphp
            <div class="col-md-4 col-sm-6">
                <div class="p-3 rounded-3" style="background: var(--nx-bg); border: 1px solid var(--nx-border);">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-bold small text-truncate" title="{{ $pk->kategori }}">{{ $pk->kategori }}</span>
                        <span class="badge rounded-pill px-2 py-0.5" style="background: var(--nx-mint); color: var(--nx-forest); font-size: 0.7rem;">{{ $persen }}%</span>
                    </div>
                    <div class="progress mb-2" style="height: 6px;">
                        <div class="progress-bar" role="progressbar" style="width: {{ $persen }}%; background: #285943;" aria-valuenow="{{ $persen }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <span class="fw-bold" style="color: var(--nx-danger); font-size: 0.88rem;">
                        Rp {{ number_format($pk->total, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-3 text-muted small">Belum ada rincian alokasi pengeluaran.</div>
        @endforelse
    </div>
</div>

<!-- 3. TABEL ARUS KAS PUBLIK -->
<div class="card overflow-hidden">
    <div class="card-header p-3.5 bg-white border-bottom d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-0" style="font-size: 1rem;">
                <i class="fas fa-list-check text-primary me-2"></i>Jurnal Transaksi Kas Terbuka
            </h5>
            <small class="text-muted">Menampilkan catatan uang masuk &amp; operasional keluar tanpa mengekspos data privat</small>
        </div>
        <span class="badge rounded-pill px-3 py-1" style="background: var(--nx-mint-soft); color: var(--nx-forest);">
            Publik &amp; Terverifikasi
        </span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4" style="width: 15%;">No. Transaksi</th>
                    <th style="width: 14%;">Tanggal</th>
                    <th style="width: 30%;">Uraian Keperluan</th>
                    <th style="width: 16%;">Kategori</th>
                    <th style="width: 10%;">Wilayah</th>
                    <th class="pe-4 text-end" style="width: 15%;">Nominal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transaksiPublik as $tp)
                    <tr>
                        <td class="ps-4 font-monospace small fw-bold text-muted">
                            {{ $tp->nomor_transaksi_formatted }}
                        </td>
                        <td class="small fw-semibold text-muted">
                            {{ $tp->tanggal ? $tp->tanggal->translatedFormat('d M Y') : '-' }}
                        </td>
                        <td>
                            <strong class="d-block" style="color: var(--nx-text); font-size: 0.88rem;">
                                {{ $tp->keterangan ?: ($tp->sumber_tujuan ?: 'Kas Lingkungan') }}
                            </strong>
                            <small class="text-muted">{{ $tp->sumber_tujuan ?: '-' }}</small>
                        </td>
                        <td>
                            <span class="badge rounded-pill px-2.5 py-1" style="background: var(--nx-bg); color: var(--nx-text); border: 1px solid var(--nx-border); font-size: 0.72rem;">
                                {{ $tp->kategori }}
                            </span>
                        </td>
                        <td>
                            @if($tp->rt_number === '000')
                                <span class="badge rounded-pill px-2 py-0.5" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-size: 0.7rem;">RW 018</span>
                            @else
                                <span class="badge rounded-pill px-2 py-0.5" style="background: var(--nx-powder); color: #1F517F; font-size: 0.7rem;">RT {{ $tp->rt_number }}</span>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            @if($tp->tipe === 'masuk')
                                <strong style="color: var(--nx-forest); font-size: 0.92rem;">
                                    + Rp {{ number_format($tp->jumlah, 0, ',', '.') }}
                                </strong>
                            @else
                                <strong style="color: var(--nx-danger); font-size: 0.92rem;">
                                    - Rp {{ number_format($tp->jumlah, 0, ',', '.') }}
                                </strong>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <h6 class="fw-bold mb-1">Belum Ada Transaksi Kas Publik</h6>
                            <p class="text-muted small mb-0">Catatan mutasi kas lingkungan akan ditampilkan di sini.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- PAGINATION -->
    <div class="p-3 d-flex justify-content-center border-top">
        {{ $transaksiPublik->links() }}
    </div>
</div>
@endsection
