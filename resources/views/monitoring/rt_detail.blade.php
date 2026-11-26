@extends('layouts.app')

@section('title', 'Detail Monitoring RT ' . $rt_number . ' — NexaNest')

@section('content')
<div class="nx-page-header">
    <div>
        <a href="{{ route('monitoring.index') }}" class="btn btn-sm btn-outline-secondary mb-2" style="border-radius: 8px;">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Monitoring Wilayah
        </a>
        <h1 class="nx-page-title">Pusat Monitoring Wilayah: RT {{ $rt_number }}</h1>
        <p class="nx-page-subtitle">
            Profil mendalam operasional RT {{ $rt_number }} RW 018 mencakup kependudukan, kas, kepatuhan iuran, pelayanan surat, pengaduan, dan lingkungan.
        </p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('laporan_rt.create', ['rt_number' => $rt_number, 'bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-primary" style="border-radius: 12px; font-weight: 700;">
            <i class="fas fa-file-signature me-1"></i> Input / Cek Laporan RT
        </a>
    </div>
</div>

<!-- SECTION 1: PROFIL & KETUA RT -->
<div class="card p-4 mb-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                 style="width: 54px; height: 54px; background: var(--nx-mint); color: var(--nx-forest); font-size: 1.25rem;">
                {{ $rt_number }}
            </div>
            <div>
                <h4 class="fw-bold mb-0 text-dark">Rukun Tetangga {{ $rt_number }}</h4>
                <p class="text-muted small mb-0">Rukun Warga 018 · Kelurahan Bandung Indah</p>
            </div>
        </div>
        <div class="p-2.5 px-4 rounded-3 bg-light border text-end">
            <span class="text-muted small d-block">Ketua RT:</span>
            <strong class="text-dark">{{ $ketuaRt ? $ketuaRt->name : 'Belum Ditunjuk' }}</strong>
            <small class="text-muted d-block"><i class="fas fa-phone-alt me-1"></i>{{ $ketuaRt ? $ketuaRt->phone : '-' }}</small>
        </div>
    </div>
</div>

<!-- 4 TOP CARDS -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card p-3 h-100" style="background: var(--nx-mint-soft) !important; border-color: var(--nx-sage) !important;">
            <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem;">Kependudukan</span>
            <h3 class="fw-bold mb-0" style="color: var(--nx-forest);">{{ $totalWarga }} <span class="fs-6 text-muted fw-normal">Warga</span></h3>
            <small class="text-muted" style="font-size: 0.72rem;">{{ $totalKk }} KK ({{ $maleCount }}L / {{ $femaleCount }}P)</small>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card p-3 h-100" style="background: var(--nx-powder-soft) !important; border-color: var(--nx-pastel-blue) !important;">
            <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem;">Saldo Kas RT</span>
            <h3 class="fw-bold mb-0" style="color: #2b567a;">Rp {{ number_format($saldoKas, 0, ',', '.') }}</h3>
            <small class="text-muted" style="font-size: 0.72rem;">+{{ number_format($kasMasuk) }} / -{{ number_format($kasKeluar) }}</small>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card p-3 h-100" style="background: var(--nx-warning-soft) !important; border-color: #F6D89E !important;">
            <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem;">Pelayanan Surat</span>
            <h3 class="fw-bold mb-0" style="color: var(--nx-warning);">{{ $suratSelesaiCount }}/{{ $suratPengajuanCount }}</h3>
            <small class="text-muted" style="font-size: 0.72rem;">{{ $suratPendingCount }} Surat Menunggu Approval</small>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card p-3 h-100" style="background: #FAF8EA !important; border-color: #E2D9A7 !important;">
            <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem;">Bank Sampah RT</span>
            <h3 class="fw-bold mb-0" style="color: #6C5B17;">{{ number_format($totalSampahKg, 1) }} <span class="fs-6 text-muted fw-normal">Kg</span></h3>
            <small class="text-muted" style="font-size: 0.72rem;">Pilah sampah warga RT {{ $rt_number }}</small>
        </div>
    </div>
</div>

<!-- SECTION 2 & 3: KEUANGAN TERAKHIR & PENGADUAN WARGA -->
<div class="row g-4 mb-4">
    <!-- TRANSAKSI KAS TERAKHIR -->
    <div class="col-lg-6">
        <div class="card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0" style="color: var(--nx-forest); font-size: 1rem;">
                    <i class="fas fa-wallet me-2"></i>Transaksi Kas RT {{ $rt_number }} Terakhir
                </h5>
                <a href="{{ route('pembayaran.index') }}" class="small fw-semibold text-decoration-none">Semua Kas &rarr;</a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead>
                        <tr class="small text-muted">
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th class="text-end">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransactions as $tx)
                            <tr>
                                <td><small>{{ \Carbon\Carbon::parse($tx->tanggal)->format('d M Y') }}</small></td>
                                <td>
                                    <strong class="d-block small text-dark">{{ $tx->kategori }}</strong>
                                    <small class="text-muted">{{ $tx->keterangan ?: '-' }}</small>
                                </td>
                                <td class="text-end">
                                    <strong class="{{ $tx->tipe == 'masuk' ? 'text-success' : 'text-danger' }} small">
                                        {{ $tx->tipe == 'masuk' ? '+' : '-' }} Rp {{ number_format($tx->jumlah, 0, ',', '.') }}
                                    </strong>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-3 text-muted small">Belum ada transaksi di RT ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- PENGADUAN & LINGKUNGAN -->
    <div class="col-lg-6">
        <div class="card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0" style="color: var(--nx-forest); font-size: 1rem;">
                    <i class="fas fa-bullhorn me-2"></i>Status Pengaduan &amp; Kondisi Lingkungan
                </h5>
                <a href="{{ route('pengaduan.index') }}" class="small fw-semibold text-decoration-none">Semua Pengaduan &rarr;</a>
            </div>
            
            <div class="row g-2 mb-3">
                <div class="col-4 text-center p-2 rounded-3 bg-light border">
                    <span class="text-muted small d-block">Baru</span>
                    <strong class="text-warning fs-5">{{ $pengaduanBaru }}</strong>
                </div>
                <div class="col-4 text-center p-2 rounded-3 bg-light border">
                    <span class="text-muted small d-block">Diproses</span>
                    <strong class="text-primary fs-5">{{ $pengaduanProses }}</strong>
                </div>
                <div class="col-4 text-center p-2 rounded-3 bg-light border">
                    <span class="text-muted small d-block">Selesai</span>
                    <strong class="text-success fs-5">{{ $pengaduanSelesai }}</strong>
                </div>
            </div>

            <div class="p-3 rounded-3" style="background: var(--nx-mint-soft); border: 1px solid var(--nx-sage);">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="fas fa-leaf text-success"></i>
                    <strong class="small text-dark">Laporan Kondisi Lingkungan Terakhir:</strong>
                </div>
                @if($recentLaporanLingkungan)
                    <p class="small mb-1 text-dark">
                        Kebersihan: <strong>{{ $recentLaporanLingkungan->kondisi_kebersihan }}</strong> · Drainase: <strong>{{ $recentLaporanLingkungan->kondisi_drainase }}</strong>
                    </p>
                    <small class="text-muted d-block">
                        Periode: {{ $recentLaporanLingkungan->periode_bulan }}/{{ $recentLaporanLingkungan->periode_tahun }}
                    </small>
                @else
                    <p class="small text-muted mb-0">Belum ada evaluasi berkala lingkungan yang diisi untuk RT ini.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- SECTION 4: RIWAYAT LAPORAN BULANAN RT -->
<div class="card overflow-hidden">
    <div class="card-header p-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0" style="font-size: 1rem; color: var(--nx-forest);">
            <i class="fas fa-folder-closed text-success me-2"></i>Riwayat Laporan Resmi Bulanan RT {{ $rt_number }}
        </h5>
        <span class="badge rounded-pill px-3 py-1" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-weight: 700;">
            {{ count($laporanList) }} Berkas Tersimpan
        </span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4">Periode</th>
                    <th>Status Laporan</th>
                    <th>Pengirim</th>
                    <th>Catatan Pemeriksaan RW</th>
                    <th class="pe-4 text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($laporanList as $lap)
                    <tr>
                        <td class="ps-4">
                            <strong class="d-block text-dark">{{ \Carbon\Carbon::createFromDate($lap->tahun, $lap->bulan, 1)->translatedFormat('F Y') }}</strong>
                            <small class="text-muted">Versi: #{{ $lap->version }}</small>
                        </td>
                        <td>
                            @if($lap->status == 'diterima')
                                <span class="badge rounded-pill px-3 py-1" style="background: var(--nx-mint); color: var(--nx-forest); font-weight: 700;">
                                    <i class="fas fa-check-double me-1"></i>Diterima RW
                                </span>
                            @elseif($lap->status == 'menunggu')
                                <span class="badge rounded-pill px-3 py-1" style="background: var(--nx-warning-soft); color: var(--nx-warning); font-weight: 700;">
                                    <i class="fas fa-hourglass-half me-1"></i>Menunggu Pemeriksaan
                                </span>
                            @elseif($lap->status == 'perlu_revisi')
                                <span class="badge rounded-pill px-3 py-1" style="background: var(--nx-danger-soft); color: var(--nx-danger); font-weight: 700;">
                                    <i class="fas fa-triangle-exclamation me-1"></i>Perlu Revisi
                                </span>
                            @else
                                <span class="badge rounded-pill px-3 py-1" style="background: #E9ECEF; color: #495057;">Draft</span>
                            @endif
                        </td>
                        <td>
                            <strong class="d-block small text-dark">{{ $lap->pengirim ? $lap->pengirim->name : 'Admin RT' }}</strong>
                            <small class="text-muted">{{ $lap->created_at->format('d/m/Y H:i') }} WIB</small>
                        </td>
                        <td>
                            <small class="text-dark">{{ $lap->catatan_pemeriksaan_rw ?: 'Belum ada catatan' }}</small>
                        </td>
                        <td class="pe-4 text-end">
                            <a href="{{ route('laporan_rt.show', $lap->id) }}" class="btn btn-sm btn-outline-primary" style="border-radius: 8px;">
                                <i class="fas fa-file-magnifying-glass me-1"></i> Buka Berkas
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted small">
                            Belum ada laporan bulanan yang tercatat untuk RT {{ $rt_number }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
