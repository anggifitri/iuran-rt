@extends('layouts.app')

@section('title', 'Laporan Bulanan Terpadu — NexaNest')

@section('content')
@php
    $user = Auth::user();
    $isAdminRw = $user->isAdminRw();
    $isAdminRt = $user->isAdminRt();
@endphp

<!-- HEADER HALAMAN -->
<div class="nx-page-header">
    <div>
        <span class="badge rounded-pill mb-2 px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
            <i class="fas fa-chart-pie me-1"></i> Rekapitulasi Terpadu {{ $isAdminRt ? 'RT ' . $user->rt_number : 'RW 018' }}
        </span>
        <h1 class="nx-page-title">Laporan Bulanan Terpadu</h1>
        <p class="nx-page-subtitle">Pusat rekapitulasi keuangan kas, kepatuhan iuran warga, status laporan resmi RT, dan aktivitas operasional lingkungan.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('laporan.export_bulanan', ['bulan' => $bulan, 'tahun' => $tahun, 'rt_number' => $rtFilter]) }}" class="btn btn-outline-secondary">
            <i class="fas fa-file-excel me-1"></i> Ekspor CSV
        </a>
        <a href="{{ route('laporan.cetak_bulanan', ['bulan' => $bulan, 'tahun' => $tahun, 'rt_number' => $rtFilter]) }}" target="_blank" class="btn btn-primary">
            <i class="fas fa-file-pdf me-1"></i> Cetak / Ekspor PDF
        </a>
    </div>
</div>

<!-- FILTER PANEL -->
<div class="card nx-filter-card mb-4">
    <form action="{{ route('laporan.index') }}" method="GET" class="row g-3 align-items-end">
        <div class="col-lg-3 col-md-4">
            <label class="form-label small fw-bold text-muted mb-1">Bulan Laporan</label>
            <select name="bulan" class="form-select">
                @foreach([
                    '1'=>'Januari', '2'=>'Februari', '3'=>'Maret', '4'=>'April',
                    '5'=>'Mei', '6'=>'Juni', '7'=>'Juli', '8'=>'Agustus',
                    '9'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'
                ] as $key => $namaBulan)
                    <option value="{{ $key }}" {{ (int)$bulan === (int)$key ? 'selected' : '' }}>{{ $namaBulan }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-lg-2 col-md-3">
            <label class="form-label small fw-bold text-muted mb-1">Tahun Laporan</label>
            <select name="tahun" class="form-select">
                @foreach($daftarTahun as $t)
                    <option value="{{ $t }}" {{ (int)$tahun === (int)$t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>

        @if($isAdminRw)
            <div class="col-lg-4 col-md-5">
                <label class="form-label small fw-bold text-muted mb-1">Cakupan Wilayah</label>
                <select name="rt_number" class="form-select">
                    <option value="all">Seluruh Wilayah RW 018 (Agregat)</option>
                    @foreach($rtList as $rt)
                        <option value="{{ $rt }}" {{ $rtFilter === $rt ? 'selected' : '' }}>Khusus RT {{ $rt }}</option>
                    @endforeach
                </select>
            </div>
        @else
            <div class="col-lg-4 col-md-5">
                <label class="form-label small fw-bold text-muted mb-1">Wilayah RT</label>
                <input type="text" class="form-control bg-light" value="RT {{ $user->rt_number }}" readonly>
            </div>
        @endif

        <div class="col-lg-3 col-md-12">
            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-filter me-1.5"></i> Tampilkan Laporan
            </button>
        </div>
    </form>
</div>

<!-- 1. RINGKASAN KEUANGAN BULANAN AKTUAL (STANDAR NEXANEST) -->
<div class="row g-3 mb-4">
    <div class="col-xl-2 col-md-4 col-sm-6">
        <x-stat-card 
            class="nx-stat-card-kpi"
            title="Saldo Awal" 
            value="Rp {{ number_format($saldoAwal, 0, ',', '.') }}" 
            sub="Sebelum awal bulan" 
            icon="fas fa-wallet" 
            theme="primary" 
            valColor="#2B5F8E" 
        />
    </div>

    <div class="col-xl-2 col-md-4 col-sm-6">
        <x-stat-card 
            class="nx-stat-card-kpi"
            title="Pemasukan Kas" 
            value="+Rp {{ number_format($totalMasuk, 0, ',', '.') }}" 
            sub="Realitas kas masuk" 
            icon="fas fa-arrow-down" 
            theme="forest" 
            valColor="var(--nx-forest)" 
        />
    </div>

    <div class="col-xl-2 col-md-4 col-sm-6">
        <x-stat-card 
            class="nx-stat-card-kpi"
            title="Pengeluaran Kas" 
            value="-Rp {{ number_format($totalKeluar, 0, ',', '.') }}" 
            sub="Beban operasional" 
            icon="fas fa-arrow-up" 
            theme="danger" 
            valColor="var(--nx-danger)" 
        />
    </div>

    <div class="col-xl-2 col-md-4 col-sm-6">
        <x-stat-card 
            class="nx-stat-card-kpi"
            title="Saldo Akhir Kas" 
            value="Rp {{ number_format($saldoAkhir, 0, ',', '.') }}" 
            sub="Sisa kas berjalan" 
            icon="fas fa-vault" 
            theme="forest" 
            valColor="var(--nx-forest)" 
        />
    </div>

    <div class="col-xl-2 col-md-4 col-sm-6">
        <x-stat-card 
            class="nx-stat-card-kpi"
            title="Iuran Terkumpul" 
            value="Rp {{ number_format($totalIuranBulanIni, 0, ',', '.') }}" 
            sub="Dari iuran KK" 
            icon="fas fa-circle-check" 
            theme="success" 
            valColor="#059669" 
        />
    </div>

    <div class="col-xl-2 col-md-4 col-sm-6">
        <x-stat-card 
            class="nx-stat-card-kpi"
            title="Tunggakan Iuran" 
            value="Rp {{ number_format($tunggakanIuran, 0, ',', '.') }}" 
            sub="Belum tertagih" 
            icon="fas fa-hourglass-half" 
            theme="warning" 
            valColor="#D97706" 
        />
    </div>
</div>

<!-- 2. STATUS LAPORAN RESMI RT (SECTION 6.A) -->
<div class="card p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div class="d-flex align-items-center gap-2">
            <span class="rounded-3 d-inline-flex align-items-center justify-content-center"
                  style="width: 36px; height: 36px; background: var(--nx-powder); color: #1F517F;">
                <i class="fas fa-clipboard-check"></i>
            </span>
            <div>
                <h5 class="fw-bold mb-0" style="font-size: 1rem;">Status Laporan Pertanggungjawaban RT</h5>
                <small class="text-muted">Progres pengiriman dan verifikasi berkas laporan resmi RT periode ini</small>
            </div>
        </div>
        <a href="{{ route('laporan_rt.index') }}" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-search-plus me-1"></i> Buka Pemeriksaan Laporan RT
        </a>
    </div>

    <div class="row g-2.5 text-center">
        <div class="col">
            <div class="p-3 rounded-3 bg-light border h-100 d-flex flex-column justify-content-center">
                <span class="text-muted small fw-bold d-block mb-1" style="font-size: 0.72rem;">Total Wilayah</span>
                <strong class="fs-4 text-dark">{{ $totalRt }} RT</strong>
            </div>
        </div>
        <div class="col">
            <div class="p-3 rounded-3 border h-100 d-flex flex-column justify-content-center" style="background: #E8F5E9; border-color: #C8E6C9 !important;">
                <span class="small fw-bold d-block mb-1 text-success" style="font-size: 0.72rem;">Laporan Disetujui</span>
                <strong class="fs-4 text-success">{{ $laporanDiterima }} RT</strong>
            </div>
        </div>
        <div class="col">
            <div class="p-3 rounded-3 border h-100 d-flex flex-column justify-content-center" style="background: #FEF3DF; border-color: #FDE68A !important;">
                <span class="small fw-bold d-block mb-1 text-warning" style="font-size: 0.72rem;">Menunggu Diperiksa</span>
                <strong class="fs-4 text-warning">{{ $laporanMenunggu }} RT</strong>
            </div>
        </div>
        <div class="col">
            <div class="p-3 rounded-3 border h-100 d-flex flex-column justify-content-center" style="background: #FCE8E9; border-color: #FECACA !important;">
                <span class="small fw-bold d-block mb-1 text-danger" style="font-size: 0.72rem;">Perlu Revisi</span>
                <strong class="fs-4 text-danger">{{ $laporanRevisi }} RT</strong>
            </div>
        </div>
        <div class="col">
            <div class="p-3 rounded-3 bg-light border h-100 d-flex flex-column justify-content-center">
                <span class="text-muted small fw-bold d-block mb-1" style="font-size: 0.72rem;">Belum Mengirimkan</span>
                <strong class="fs-4 text-muted">{{ $laporanBelumKirim }} RT</strong>
            </div>
        </div>
    </div>
</div>

<!-- 3. TABEL KOMPARASI KEUANGAN ANTAR RT (SECTION 6.D) -->
@if($isAdminRw)
    <div class="card overflow-hidden mb-4">
        <div class="card-header p-3.5 bg-white border-bottom d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-0" style="font-size: 1rem;">
                    <i class="fas fa-scale-balanced text-primary me-2"></i>Komparasi Finansial &amp; Kepatuhan Antar RT
                </h5>
                <small class="text-muted">Perbandingan kinerja realisasi kas dan kepatuhan warga di seluruh RT</small>
            </div>
            <span class="badge rounded-pill px-3 py-1" style="background: var(--nx-mint-soft); color: var(--nx-forest);">
                Wilayah RW 018
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Wilayah RT</th>
                        <th>Pemasukan Kas</th>
                        <th>Pengeluaran Kas</th>
                        <th>Saldo Kas RT</th>
                        <th>Kepala Keluarga</th>
                        <th>Kepatuhan Iuran</th>
                        <th>Status Laporan RT</th>
                        <th class="pe-4 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekapPerRt as $rrt)
                        <tr>
                            <td class="ps-4">
                                <span class="badge rounded-pill px-2.5 py-1 fw-bold" style="background: var(--nx-powder); color: #1F517F;">
                                    RT {{ $rrt['rt_number'] }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-semibold text-success">+Rp {{ number_format($rrt['pemasukan'], 0, ',', '.') }}</span>
                            </td>
                            <td>
                                <span class="fw-semibold text-danger">-Rp {{ number_format($rrt['pengeluaran'], 0, ',', '.') }}</span>
                            </td>
                            <td>
                                <strong style="color: var(--nx-text);">Rp {{ number_format($rrt['saldo'], 0, ',', '.') }}</strong>
                            </td>
                            <td>
                                <span>{{ $rrt['kk_lunas'] }} / {{ $rrt['total_kk'] }} KK</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar" role="progressbar" style="width: {{ $rrt['persen_kepatuhan'] }}%; background-color: var(--nx-forest);" aria-valuenow="{{ $rrt['persen_kepatuhan'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <span class="small fw-bold">{{ $rrt['persen_kepatuhan'] }}%</span>
                                </div>
                            </td>
                            <td>
                                @if($rrt['status_laporan'] === 'diterima')
                                    <span class="badge rounded-pill px-2.5 py-0.5 bg-success">Diterima</span>
                                @elseif($rrt['status_laporan'] === 'menunggu')
                                    <span class="badge rounded-pill px-2.5 py-0.5 bg-warning text-dark">Menunggu</span>
                                @elseif($rrt['status_laporan'] === 'perlu_revisi')
                                    <span class="badge rounded-pill px-2.5 py-0.5 bg-danger">Revisi</span>
                                @else
                                    <span class="badge rounded-pill px-2.5 py-0.5 bg-secondary">Belum Kirim</span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <a href="{{ route('laporan.iuran', ['rt_number' => $rrt['rt_number'], 'bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-xs btn-outline-secondary py-1 px-2" style="font-size: 0.75rem;">
                                    <i class="fas fa-list me-1"></i>Iuran RT
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

<!-- 4. REKAP AKTIVITAS WILAYAH TERPADU (SECTION 6.C) -->
<div class="card p-4 mb-4">
    <h5 class="fw-bold mb-1" style="font-size: 1rem;">
        <i class="fas fa-bolt text-warning me-2"></i>Aktivitas Operasional &amp; Pelayanan Wilayah
    </h5>
    <p class="text-muted small mb-3">Statistik layanan persuratan, aduan masyarakat, bank sampah, dan keamanan selama bulan ini</p>

    <div class="row g-3">
        <div class="col-md col-sm-6">
            <div class="p-3 rounded-3 text-center" style="background: var(--nx-bg); border: 1px solid var(--nx-border);">
                <i class="fas fa-envelope-open-text text-primary fs-4 mb-2"></i>
                <h4 class="fw-bold mb-0" style="color: var(--nx-text);">{{ $aktivitasSurat }}</h4>
                <small class="text-muted" style="font-size: 0.75rem;">Surat Pengajuan</small>
            </div>
        </div>
        <div class="col-md col-sm-6">
            <div class="p-3 rounded-3 text-center" style="background: var(--nx-bg); border: 1px solid var(--nx-border);">
                <i class="fas fa-bullhorn text-warning fs-4 mb-2"></i>
                <h4 class="fw-bold mb-0" style="color: var(--nx-text);">{{ $aktivitasPengaduan }}</h4>
                <small class="text-muted" style="font-size: 0.75rem;">Pengaduan Warga</small>
            </div>
        </div>
        <div class="col-md col-sm-6">
            <div class="p-3 rounded-3 text-center" style="background: var(--nx-bg); border: 1px solid var(--nx-border);">
                <i class="fas fa-recycle text-success fs-4 mb-2"></i>
                <h4 class="fw-bold mb-0" style="color: var(--nx-text);">{{ $aktivitasBankSampah }}</h4>
                <small class="text-muted" style="font-size: 0.75rem;">Transaksi Sampah</small>
            </div>
        </div>
        <div class="col-md col-sm-6">
            <div class="p-3 rounded-3 text-center" style="background: var(--nx-bg); border: 1px solid var(--nx-border);">
                <i class="fas fa-shield-halved text-info fs-4 mb-2"></i>
                <h4 class="fw-bold mb-0" style="color: var(--nx-text);">{{ $aktivitasRonda }}</h4>
                <small class="text-muted" style="font-size: 0.75rem;">Jadwal Ronda</small>
            </div>
        </div>
        <div class="col-md col-sm-6">
            <div class="p-3 rounded-3 text-center" style="background: var(--nx-bg); border: 1px solid var(--nx-border);">
                <i class="fas fa-people-arrows text-danger fs-4 mb-2"></i>
                <h4 class="fw-bold mb-0" style="color: var(--nx-text);">{{ $aktivitasMutasi }}</h4>
                <small class="text-muted" style="font-size: 0.75rem;">Mutasi Kependudukan</small>
            </div>
        </div>
    </div>
</div>

<!-- 5. BUKU TRANSAKSI KAS BULAN BERJALAN -->
<div class="card overflow-hidden">
    <div class="card-header p-3.5 bg-white border-bottom d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-0" style="font-size: 1rem;">
                <i class="fas fa-list text-primary me-2"></i>Rincian Jurnal Kas Periode Ini
            </h5>
            <small class="text-muted">Daftar transaksi kas pemasukan dan pengeluaran yang terverifikasi</small>
        </div>
        <span class="badge rounded-pill px-3 py-1" style="background: var(--nx-mint-soft); color: var(--nx-forest);">
            {{ count($pembayarans) }} Catatan
        </span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4" style="width: 15%;">Tanggal</th>
                    <th style="width: 15%;">No. Transaksi</th>
                    <th style="width: 32%;">Uraian Transaksi</th>
                    <th style="width: 15%;">Kategori</th>
                    <th style="width: 10%;">Tipe</th>
                    <th class="pe-4 text-end" style="width: 13%;">Nominal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pembayarans as $p)
                    <tr>
                        <td class="ps-4 small fw-semibold text-muted">
                            {{ $p->tanggal ? $p->tanggal->translatedFormat('d M Y') : '-' }}
                        </td>
                        <td class="font-monospace small fw-bold text-muted">
                            {{ $p->nomor_transaksi_formatted }}
                        </td>
                        <td>
                            <strong class="d-block" style="color: var(--nx-text); font-size: 0.88rem;">
                                {{ $p->warga ? $p->warga->nama : ($p->sumber_tujuan ?: 'Kas Operasional') }}
                            </strong>
                            <small class="text-muted">{{ $p->keterangan ?: '-' }}</small>
                        </td>
                        <td>
                            <span class="badge rounded-pill px-2.5 py-1" style="background: var(--nx-bg); color: var(--nx-text); border: 1px solid var(--nx-border); font-size: 0.72rem;">
                                {{ $p->kategori }}
                            </span>
                        </td>
                        <td>
                            @if($p->tipe === 'masuk')
                                <span class="badge rounded-pill px-2.5 py-0.5" style="background: var(--nx-mint); color: var(--nx-forest); font-size: 0.72rem;">Masuk</span>
                            @else
                                <span class="badge rounded-pill px-2.5 py-0.5" style="background: var(--nx-soft-red); color: var(--nx-danger); font-size: 0.72rem;">Keluar</span>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            @if($p->tipe === 'masuk')
                                <strong style="color: var(--nx-forest); font-size: 0.92rem;">
                                    + Rp {{ number_format($p->jumlah, 0, ',', '.') }}
                                </strong>
                            @else
                                <strong style="color: var(--nx-danger); font-size: 0.92rem;">
                                    - Rp {{ number_format($p->jumlah, 0, ',', '.') }}
                                </strong>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted small">
                            Belum ada catatan transaksi kas pada periode bulan terpilih.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
