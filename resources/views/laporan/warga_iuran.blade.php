@extends('layouts.app')

@section('title', 'Iuran Warga Saya — NexaNest')

@section('content')
@php
    $user = Auth::user();
@endphp

<!-- HEADER HALAMAN -->
<div class="nx-page-header">
    <div>
        <span class="badge rounded-pill mb-2 px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
            <i class="fas fa-file-invoice-dollar me-1"></i> Kartu Iuran Digital Keluarga
        </span>
        <h1 class="nx-page-title">Iuran Warga Saya</h1>
        <p class="nx-page-subtitle">Informasi status kewajiban iuran lingkungan, tagihan berjalan, dan riwayat pembayaran keluarga Anda di RT {{ $warga->rt_number ?? ($user->rt_number ?? '000') }}.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('pembayaran.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-chart-line me-1"></i> Transparansi Kas Lingkungan
        </a>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalBayarWarga">
            <i class="fas fa-upload me-1"></i> Konfirmasi Pembayaran Saya
        </button>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert" style="background: var(--nx-mint-soft); border-color: var(--nx-sage); color: var(--nx-forest);">
        <i class="fas fa-check-circle me-2 fs-5"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
        <i class="fas fa-exclamation-circle me-2 fs-5"></i>
        <div>{{ session('error') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- 1. STATUS RINGKASAN TAGIHAN -->
<div class="row g-3 mb-4">
    <!-- Status Bulan Ini -->
    <div class="col-lg-4 col-md-6">
        <div class="card p-3.5 h-100 {{ $statusTagihan === 'Lunas' ? 'border-success' : 'border-warning' }}" style="background: {{ $statusTagihan === 'Lunas' ? 'var(--nx-mint-soft)' : '#FFFBEB' }};">
            <span class="text-muted fw-bold text-uppercase" style="font-size: 0.7rem;">Status Iuran {{ \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F Y') }}</span>
            <div class="d-flex align-items-center justify-content-between my-2">
                @if($statusTagihan === 'Lunas')
                    <h3 class="fw-bold mb-0 text-success">
                        <i class="fas fa-check-circle me-1"></i> LUNAS
                    </h3>
                @elseif($statusTagihan === 'Menunggu Konfirmasi')
                    <h3 class="fw-bold mb-0 text-warning">
                        <i class="fas fa-clock me-1"></i> MENUNGGU
                    </h3>
                @else
                    <h3 class="fw-bold mb-0 text-danger">
                        <i class="fas fa-hourglass-half me-1"></i> BELUM BAYAR
                    </h3>
                @endif
                <span class="badge rounded-circle p-2 {{ $statusTagihan === 'Lunas' ? 'bg-success' : 'bg-warning' }} text-white">
                    <i class="fas {{ $statusTagihan === 'Lunas' ? 'fa-shield-check' : 'fa-bell' }} fa-lg"></i>
                </span>
            </div>
            <small class="text-muted">
                {{ $statusTagihan === 'Lunas' ? 'Terima kasih atas partisipasi aktif Anda.' : 'Silakan lakukan transfer atau serahkan tunai ke pengurus RT.' }}
            </small>
        </div>
    </div>

    <!-- Total Tunggakan -->
    <div class="col-lg-4 col-md-6">
        <div class="card p-3.5 h-100" style="border-top: 3px solid {{ $totalTunggakan > 0 ? 'var(--nx-danger)' : 'var(--nx-forest)' }};">
            <span class="text-muted fw-bold text-uppercase" style="font-size: 0.7rem;">Akumulasi Tunggakan (6 Bln Terakhir)</span>
            <h3 class="fw-bold my-2" style="color: {{ $totalTunggakan > 0 ? 'var(--nx-danger)' : 'var(--nx-forest)' }};">
                Rp {{ number_format($totalTunggakan, 0, ',', '.') }}
            </h3>
            <small class="text-muted">{{ $totalTunggakan > 0 ? 'Terdapat tagihan yang belum terselesaikan' : 'Semua kewajiban iuran Anda berstatus lunas' }}</small>
        </div>
    </div>

    <!-- Tarif Bulanan -->
    <div class="col-lg-4 col-md-12">
        <div class="card p-3.5 h-100">
            <span class="text-muted fw-bold text-uppercase" style="font-size: 0.7rem;">Tarif Iuran Wajib Lingkungan</span>
            <h3 class="fw-bold my-2" style="color: var(--nx-forest);">
                Rp {{ number_format($standardNominal, 0, ',', '.') }}
                <small class="text-muted fw-normal" style="font-size: 0.85rem;">/ KK / Bulan</small>
            </h3>
            <small class="text-muted">Mencakup kebersihan lingkungan, keamanan 24 jam, dan pemeliharaan fasum.</small>
        </div>
    </div>
</div>

<!-- 2. PETUNJUK PEMBAYARAN & REKENING RESMI RT -->
<div class="card p-4 mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #F7FAF8 0%, #EDF4EF 100%); border: 1px solid var(--nx-sage) !important;">
    <div class="row align-items-center g-3">
        <div class="col-lg-8">
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge rounded-pill px-3 py-1" style="background: var(--nx-mint); color: var(--nx-forest); font-weight: 700;">
                    <i class="fas fa-wallet me-1"></i> Rekening Resmi RT {{ $warga->rt_number ?? ($user->rt_number ?? '000') }}
                </span>
                <span class="badge bg-light text-dark border">BCA · Bank Central Asia</span>
            </div>
            <h4 class="fw-bold mb-1" style="color: var(--nx-forest);">Nomor Rekening: <span class="font-monospace">8820-1928-4491</span></h4>
            <p class="text-muted small mb-0">Atas Nama: <strong>Bendahara Kas RT {{ $warga->rt_number ?? ($user->rt_number ?? '000') }} (RW 018)</strong>. Cantumkan No. Rumah &amp; Bulan saat transfer.</p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <button type="button" class="btn btn-primary px-4 py-2.5" data-bs-toggle="modal" data-bs-target="#modalBayarWarga">
                <i class="fas fa-upload me-1"></i> Upload Bukti Transfer
            </button>
        </div>
    </div>
</div>

<!-- 3. TABEL HISTORI 6 BULAN TERAKHIR -->
<div class="card overflow-hidden mb-4">
    <div class="card-header p-3.5 bg-white border-bottom d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-0" style="font-size: 1rem;">
                <i class="fas fa-calendar-check text-primary me-2"></i>Status Tagihan 6 Bulan Terakhir
            </h5>
            <small class="text-muted">Riwayat penagihan dan status pembayaran keluarga Anda</small>
        </div>
        <span class="badge rounded-pill px-3 py-1" style="background: var(--nx-mint-soft); color: var(--nx-forest);">
            Rekap 6 Periode
        </span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4" style="width: 25%;">Periode Tagihan</th>
                    <th style="width: 20%;">Nominal Kewajiban</th>
                    <th style="width: 20%;">Status Pembayaran</th>
                    <th style="width: 20%;">Tanggal Pembayaran</th>
                    <th class="pe-4 text-end" style="width: 15%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tagihanBulanan as $tb)
                    <tr>
                        <td class="ps-4">
                            <strong style="color: var(--nx-text);">{{ $tb->periode }}</strong>
                        </td>
                        <td>
                            <span class="fw-semibold">Rp {{ number_format($tb->nominal, 0, ',', '.') }}</span>
                        </td>
                        <td>
                            @if($tb->status === 'Lunas')
                                <span class="badge rounded-pill px-2.5 py-1" style="background: var(--nx-mint); color: var(--nx-forest); font-size: 0.75rem;">
                                    <i class="fas fa-check-circle me-1"></i>Lunas
                                </span>
                            @elseif($tb->status === 'Menunggu Konfirmasi')
                                <span class="badge rounded-pill px-2.5 py-1 bg-warning text-dark" style="font-size: 0.75rem;">
                                    <i class="fas fa-clock me-1"></i>Menunggu Verifikasi
                                </span>
                            @else
                                <span class="badge rounded-pill px-2.5 py-1" style="background: var(--nx-soft-red); color: var(--nx-danger); font-size: 0.75rem;">
                                    <i class="fas fa-times-circle me-1"></i>Belum Bayar
                                </span>
                            @endif
                        </td>
                        <td class="small text-muted">
                            {{ $tb->tanggal_bayar ? \Carbon\Carbon::parse($tb->tanggal_bayar)->translatedFormat('d M Y') : '-' }}
                        </td>
                        <td class="pe-4 text-end">
                            @if($tb->status === 'Lunas' && $tb->pembayaran_id)
                                <a href="{{ route('iuran.kuitansi', $tb->pembayaran_id) }}" target="_blank" class="btn btn-sm btn-outline-success px-2.5 py-1">
                                    <i class="fas fa-receipt me-1"></i> Kuitansi
                                </a>
                            @elseif($tb->status !== 'Lunas')
                                <button type="button" class="btn btn-sm btn-outline-primary px-2.5 py-1" onclick="openBayarPeriode({{ $tb->bulan }}, {{ $tb->tahun }}, {{ $tb->nominal }})">
                                    <i class="fas fa-upload me-1"></i> Bayar
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- 4. RIWAYAT PEMBAYARAN TERCATAT -->
<div class="card overflow-hidden">
    <div class="card-header p-3.5 bg-white border-bottom">
        <h5 class="fw-bold mb-0" style="font-size: 1rem;">
            <i class="fas fa-history text-primary me-2"></i>Histori Pembayaran Terverifikasi di Kas Lingkungan
        </h5>
        <small class="text-muted">Catatan transaksi pembayaran keluarga yang telah disahkan dan masuk kas</small>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4">No. Transaksi</th>
                    <th>Tanggal</th>
                    <th>Uraian / Keterangan</th>
                    <th>Metode</th>
                    <th>Nominal</th>
                    <th class="pe-4 text-end">Kuitansi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayatPembayaran as $rp)
                    <tr>
                        <td class="ps-4 font-monospace small fw-bold text-muted">
                            {{ $rp->nomor_transaksi_formatted }}
                        </td>
                        <td class="small fw-semibold text-muted">
                            {{ $rp->tanggal ? $rp->tanggal->translatedFormat('d M Y') : '-' }}
                        </td>
                        <td>
                            <strong class="d-block" style="color: var(--nx-text); font-size: 0.88rem;">{{ $rp->keterangan }}</strong>
                            <small class="text-muted">{{ $rp->kategori }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $rp->metode_pembayaran ?: 'Tunai' }}</span>
                        </td>
                        <td>
                            <strong class="text-success">Rp {{ number_format($rp->jumlah, 0, ',', '.') }}</strong>
                        </td>
                        <td class="pe-4 text-end">
                            <a href="{{ route('iuran.kuitansi', $rp->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary px-2.5 py-1">
                                <i class="fas fa-print me-1"></i> Cetak
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted small">
                            Belum ada riwayat transaksi pembayaran terverifikasi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL BAYAR / UPLOAD BUKTI OLEH WARGA -->
<div class="modal fade" id="modalBayarWarga" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('iuran.bayar') }}" enctype="multipart/form-data" class="modal-content border-0 shadow">
            @csrf
            <div class="modal-header border-bottom py-3">
                <h6 class="modal-title fw-bold">
                    <i class="fas fa-receipt text-primary me-2"></i>Konfirmasi Pembayaran Iuran
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="p-3 mb-3 rounded-2 bg-light border small">
                    <div class="fw-bold mb-1">Kepala Keluarga: {{ $warga->nama ?? $user->name }}</div>
                    <div class="text-muted">Wilayah: RT {{ $warga->rt_number ?? ($user->rt_number ?? '000') }} · Blok {{ $warga->blok_rumah ?? '-' }}</div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Periode Bulan <span class="text-danger">*</span></label>
                        <select name="bulan" id="wargaModalBulan" class="form-select form-select-sm" required>
                            @foreach(['1'=>'Januari','2'=>'Februari','3'=>'Maret','4'=>'April','5'=>'Mei','6'=>'Juni','7'=>'Juli','8'=>'Agustus','9'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $k => $n)
                                <option value="{{ $k }}" {{ (int)$bulan === (int)$k ? 'selected' : '' }}>{{ $n }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Tahun <span class="text-danger">*</span></label>
                        <input type="number" name="tahun" id="wargaModalTahun" class="form-control form-control-sm" value="{{ $tahun }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Nominal Pembayaran (Rp) <span class="text-danger">*</span></label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="amount_paid" id="wargaModalAmount" class="form-control" value="100000" required min="1000">
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Metode Bayar <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select form-select-sm" required>
                            <option value="Transfer Bank">Transfer Bank</option>
                            <option value="QRIS">QRIS</option>
                            <option value="Tunai">Tunai / Cash</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Tanggal Bayar <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Upload Bukti Transfer / Resi <span class="text-danger">*</span></label>
                    <input type="file" name="proof_image" class="form-control form-control-sm" accept="image/*,.pdf" required>
                    <div class="form-text small text-muted">Format: Foto JPG, PNG, atau file PDF bukti transfer bank / QRIS</div>
                </div>

                <div class="mb-2">
                    <label class="form-label small fw-bold">Catatan Tambahan (Opsional)</label>
                    <input type="text" name="notes" class="form-control form-control-sm" placeholder="Contoh: Transfer via m-BCA a.n Budi">
                </div>
            </div>
            <div class="modal-footer border-top py-2.5">
                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-sm btn-primary px-3">
                    <i class="fas fa-paper-plane me-1"></i> Kirim Konfirmasi Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openBayarPeriode(bulan, tahun, nominal) {
    document.getElementById('wargaModalBulan').value = bulan;
    document.getElementById('wargaModalTahun').value = tahun;
    document.getElementById('wargaModalAmount').value = nominal;
    new bootstrap.Modal(document.getElementById('modalBayarWarga')).show();
}
</script>
@endpush
