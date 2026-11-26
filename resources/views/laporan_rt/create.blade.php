@extends('layouts.app')

@section('title', 'Penyusunan Laporan Bulanan RT ' . $rtNumber . ' — NexaNest')

@section('content')
<div class="nx-page-header">
    <div>
        <a href="{{ route('laporan_rt.index') }}" class="btn btn-sm btn-outline-secondary mb-2" style="border-radius: 8px;">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Laporan
        </a>
        <h1 class="nx-page-title">Penyusunan Laporan Resmi: RT {{ $rtNumber }}</h1>
        <p class="nx-page-subtitle">
            Sistem telah menghitung secara otomatis data kependudukan, keuangan, kepatuhan iuran, dan pelayanan. Anda hanya perlu melengkapi evaluasi &amp; kendala.
        </p>
    </div>
    <div>
        <span class="badge rounded-pill px-3 py-2" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-weight: 700; font-size: 0.88rem;">
            Periode: {{ \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F Y') }}
        </span>
    </div>
</div>

<form action="{{ route('laporan_rt.store') }}" method="POST">
    @csrf
    <input type="hidden" name="rt_number" value="{{ $rtNumber }}">
    <input type="hidden" name="bulan" value="{{ $bulan }}">
    <input type="hidden" name="tahun" value="{{ $tahun }}">

    <!-- LIVE DATA PREVIEW (SNAPSHOT) -->
    <div class="card p-4 mb-4">
        <h5 class="fw-bold mb-3" style="color: var(--nx-forest);">
            <i class="fas fa-chart-pie me-2"></i>Rekapitulasi Operasional Otomatis (Database NexaNest)
        </h5>

        <div class="row g-3">
            <div class="col-md-3 col-6">
                <div class="p-3 rounded-3 bg-light border">
                    <span class="text-muted small d-block">Total Warga &amp; KK</span>
                    <strong class="fs-5 text-dark">{{ $metrics['demografi']['total_warga'] }} Jiwa</strong>
                    <small class="text-muted d-block">{{ $metrics['demografi']['total_kk'] }} KK ({{ $metrics['demografi']['laki_laki'] }}L / {{ $metrics['demografi']['perempuan'] }}P)</small>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-3 rounded-3 bg-light border">
                    <span class="text-muted small d-block">Pemasukan &amp; Pengeluaran</span>
                    <strong class="fs-5 text-success">+Rp {{ number_format($metrics['keuangan']['pemasukan_bulan_ini'], 0, ',', '.') }}</strong>
                    <small class="text-danger d-block">-Rp {{ number_format($metrics['keuangan']['pengeluaran_bulan_ini'], 0, ',', '.') }}</small>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-3 rounded-3 bg-light border">
                    <span class="text-muted small d-block">Kepatuhan Iuran Bulanan</span>
                    <strong class="fs-5 text-primary">{{ $metrics['iuran']['persentase_kepatuhan'] }}% Lunas</strong>
                    <small class="text-muted d-block">{{ $metrics['iuran']['kk_lunas'] }} dari {{ $metrics['iuran']['kk_wajib_iuran'] }} KK</small>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-3 rounded-3 bg-light border">
                    <span class="text-muted small d-block">Pelayanan &amp; Bank Sampah</span>
                    <strong class="fs-5 text-dark">{{ $metrics['pelayanan']['surat_diterbitkan'] }} Surat</strong>
                    <small class="text-muted d-block">{{ $metrics['lingkungan']['sampah_terkumpul_kg'] }} Kg Sampah Terpilah</small>
                </div>
            </div>
        </div>
    </div>

    <!-- FORM EVALUASI & KENDALA -->
    <div class="card p-4 mb-4">
        <h5 class="fw-bold mb-3" style="color: var(--nx-forest);">
            <i class="fas fa-pen-to-square me-2"></i>Evaluasi Kegiatan, Kendala &amp; Tindak Lanjut
        </h5>

        <div class="mb-3">
            <label class="form-label small fw-bold">Evaluasi Kegiatan &amp; Program Kerja RT Bulan Ini <span class="text-danger">*</span></label>
            <textarea name="evaluasi_kegiatan" rows="4" class="form-control" required placeholder="Jelaskan ringkasan kegiatan RT yang terlaksana (misal: kerja bakti lingkungan, rapat pengurus, posyandu rutin, kondisi keamanan)..."></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label small fw-bold">Kendala, Hambatan, atau Permasalahan di Lingkungan RT</label>
            <textarea name="kendala" rows="3" class="form-control" placeholder="Tuliskan jika terdapat kendala fasilitas, iuran warga yang menunggak, saluran drainase mampet, atau persoalan sosial lain..."></textarea>
        </div>

        <div class="mb-4">
            <label class="form-label small fw-bold">Catatan Tambahan &amp; Permohonan Bantuan ke Pengurus RW</label>
            <textarea name="catatan_tambahan" rows="2" class="form-control" placeholder="Usulan atau aspirasi yang membutuhkan koordinasi lanjutan tingkat RW..."></textarea>
        </div>

        <div class="p-3 rounded-3 bg-light border d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <strong class="d-block text-dark small"><i class="fas fa-info-circle text-primary me-1"></i>Ketentuan Pengiriman:</strong>
                <span class="text-muted small">Laporan yang diserahkan akan otomatis menyimpan snapshot data agar tidak terpengaruh pergeseran transaksi di masa mendatang.</span>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" name="action_type" value="draft" class="btn btn-outline-secondary px-3" style="border-radius: 12px; font-weight: 600;">
                    <i class="fas fa-floppy-disk me-1"></i> Simpan Sebagai Draft
                </button>
                <button type="submit" name="action_type" value="submit" class="btn btn-primary px-4" style="border-radius: 12px; font-weight: 700;">
                    <i class="fas fa-paper-plane me-1"></i> Kirim Laporan ke Admin RW
                </button>
            </div>
        </div>
    </div>
</form>
@endsection
