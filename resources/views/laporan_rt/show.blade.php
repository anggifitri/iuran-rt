@extends('layouts.app')

@section('title', 'Pemeriksaan Laporan RT ' . $laporan->rt_number . ' — NexaNest')

@section('content')
<div class="nx-page-header">
    <div>
        <a href="{{ route('laporan_rt.index') }}" class="btn btn-sm btn-outline-secondary mb-2" style="border-radius: 8px;">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Laporan
        </a>
        <h1 class="nx-page-title">Laporan Bulanan: RT {{ $laporan->rt_number }}</h1>
        <p class="nx-page-subtitle">
            Periode: <strong>{{ \Carbon\Carbon::createFromDate($laporan->tahun, $laporan->bulan, 1)->translatedFormat('F Y') }}</strong> · Versi Dokumen: #{{ $laporan->version }}
        </p>
    </div>
    <div class="d-flex align-items-center gap-2">
        @if($laporan->status == 'diterima')
            <span class="badge rounded-pill px-3 py-2" style="background: var(--nx-mint); color: var(--nx-forest); font-weight: 700; font-size: 0.88rem;">
                <i class="fas fa-check-double me-1"></i>STATUS: DITERIMA &amp; DISAHKAN
            </span>
        @elseif($laporan->status == 'menunggu')
            <span class="badge rounded-pill px-3 py-2" style="background: var(--nx-warning-soft); color: var(--nx-warning); font-weight: 700; font-size: 0.88rem;">
                <i class="fas fa-hourglass-half me-1"></i>STATUS: MENUNGGU PEMERIKSAAN RW
            </span>
        @elseif($laporan->status == 'perlu_revisi')
            <span class="badge rounded-pill px-3 py-2" style="background: var(--nx-danger-soft); color: var(--nx-danger); font-weight: 700; font-size: 0.88rem;">
                <i class="fas fa-triangle-exclamation me-1"></i>STATUS: PERLU REVISI
            </span>
        @else
            <span class="badge rounded-pill px-3 py-2" style="background: #E9ECEF; color: #495057; font-weight: 700; font-size: 0.88rem;">
                <i class="fas fa-pencil me-1"></i>STATUS: DRAFT LAPORAN
            </span>
        @endif
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <!-- 1. SNAPSHOT DATA OPERASIONAL -->
        <div class="card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0" style="color: var(--nx-forest);">
                    <i class="fas fa-database me-2"></i>Snapshot Rekapitulasi Operasional RT
                </h5>
                <span class="badge rounded-pill px-2.5 py-1" style="background: #f0f0f0; color: #555; font-size: 0.72rem;">
                    Terekam: {{ $laporan->snapshot_data['generated_at'] ?? $laporan->created_at->format('d/m/Y H:i') }}
                </span>
            </div>

            @php $snap = $laporan->snapshot_data ?? []; @endphp

            <div class="row g-3 mb-4">
                <div class="col-md-4 col-6">
                    <div class="p-3 rounded-3 bg-light border">
                        <small class="text-muted d-block">Kependudukan</small>
                        <strong class="fs-5 text-dark">{{ $snap['demografi']['total_warga'] ?? 0 }} Jiwa</strong>
                        <small class="text-muted d-block">{{ $snap['demografi']['total_kk'] ?? 0 }} KK ({{ $snap['demografi']['laki_laki'] ?? 0 }}L / {{ $snap['demografi']['perempuan'] ?? 0 }}P)</small>
                    </div>
                </div>
                <div class="col-md-4 col-6">
                    <div class="p-3 rounded-3 bg-light border">
                        <small class="text-muted d-block">Pemasukan Kas</small>
                        <strong class="fs-5 text-success">+Rp {{ number_format($snap['keuangan']['pemasukan_bulan_ini'] ?? 0, 0, ',', '.') }}</strong>
                        <small class="text-muted d-block">Keluar: Rp {{ number_format($snap['keuangan']['pengeluaran_bulan_ini'] ?? 0, 0, ',', '.') }}</small>
                    </div>
                </div>
                <div class="col-md-4 col-6">
                    <div class="p-3 rounded-3 bg-light border">
                        <small class="text-muted d-block">Kepatuhan Iuran</small>
                        <strong class="fs-5 text-primary">{{ $snap['iuran']['persentase_kepatuhan'] ?? 0 }}% Lunas</strong>
                        <small class="text-muted d-block">{{ $snap['iuran']['kk_lunas'] ?? 0 }}/{{ $snap['iuran']['kk_wajib_iuran'] ?? 0 }} Kepala Keluarga</small>
                    </div>
                </div>
                <div class="col-md-4 col-6">
                    <div class="p-3 rounded-3 bg-light border">
                        <small class="text-muted d-block">Surat Diterbitkan</small>
                        <strong class="fs-5 text-dark">{{ $snap['pelayanan']['surat_diterbitkan'] ?? 0 }} Surat</strong>
                        <small class="text-muted d-block">Layanan Mandiri Warga</small>
                    </div>
                </div>
                <div class="col-md-4 col-6">
                    <div class="p-3 rounded-3 bg-light border">
                        <small class="text-muted d-block">Pengaduan Selesai</small>
                        <strong class="fs-5 text-dark">{{ $snap['pelayanan']['pengaduan_selesai'] ?? 0 }}/{{ $snap['pelayanan']['pengaduan_masuk'] ?? 0 }}</strong>
                        <small class="text-muted d-block">Laporan Lingkungan &amp; Sosial</small>
                    </div>
                </div>
                <div class="col-md-4 col-6">
                    <div class="p-3 rounded-3 bg-light border">
                        <small class="text-muted d-block">Bank Sampah RT</small>
                        <strong class="fs-5 text-dark">{{ number_format($snap['lingkungan']['sampah_terkumpul_kg'] ?? 0, 1) }} Kg</strong>
                        <small class="text-muted d-block">Pilah Daur Ulang Warga</small>
                    </div>
                </div>
            </div>

            <!-- EVALUASI & KENDALA -->
            <div class="mb-4">
                <h6 class="fw-bold text-dark small text-uppercase mb-2"><i class="fas fa-clipboard-check text-success me-1"></i>Evaluasi Kegiatan RT</h6>
                <div class="p-3 rounded-3 bg-light border text-dark" style="font-size: 0.9rem; line-height: 1.55;">
                    {{ $laporan->evaluasi_kegiatan ?: 'Tidak ada evaluasi khusus yang dicantumkan.' }}
                </div>
            </div>

            <div class="mb-4">
                <h6 class="fw-bold text-dark small text-uppercase mb-2"><i class="fas fa-triangle-exclamation text-warning me-1"></i>Kendala &amp; Hambatan Wilayah</h6>
                <div class="p-3 rounded-3 bg-light border text-dark" style="font-size: 0.9rem; line-height: 1.55;">
                    {{ $laporan->kendala ?: 'Tidak ada kendala operasional yang dilaporkan.' }}
                </div>
            </div>

            @if($laporan->catatan_tambahan)
            <div class="mb-0">
                <h6 class="fw-bold text-dark small text-uppercase mb-2"><i class="fas fa-note-sticky text-primary me-1"></i>Catatan Tambahan &amp; Permohonan</h6>
                <div class="p-3 rounded-3 bg-light border text-dark" style="font-size: 0.9rem;">
                    {{ $laporan->catatan_tambahan }}
                </div>
            </div>
            @endif
        </div>

        <!-- IF RT IS EDITING / RESUBMITTING -->
        @if((Auth::user()->isAdminRt() && $laporan->rt_number == Auth::user()->rt_number && in_array($laporan->status, ['draft', 'perlu_revisi'])) || (Auth::user()->isAdminRw() && $laporan->status == 'draft'))
        <div class="card p-4 mb-4" style="border: 2px solid var(--nx-sage) !important;">
            <h5 class="fw-bold mb-3" style="color: var(--nx-forest);">
                <i class="fas fa-pen me-2"></i>Perbarui / Kirim Ulang Laporan RT
            </h5>
            <form action="{{ route('laporan_rt.update', $laporan->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label small fw-bold">Perbarui Evaluasi Kegiatan</label>
                    <textarea name="evaluasi_kegiatan" rows="3" class="form-control" required>{{ $laporan->evaluasi_kegiatan }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Perbarui Kendala Wilayah</label>
                    <textarea name="kendala" rows="2" class="form-control">{{ $laporan->kendala }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Perbarui Catatan Tambahan</label>
                    <textarea name="catatan_tambahan" rows="2" class="form-control">{{ $laporan->catatan_tambahan }}</textarea>
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" name="action_type" value="draft" class="btn btn-outline-secondary">Simpan Draft</button>
                    <button type="submit" name="action_type" value="submit" class="btn btn-primary" style="font-weight: 700;">
                        <i class="fas fa-paper-plane me-1"></i> Kirim ke Admin RW
                    </button>
                </div>
            </form>
        </div>
        @endif
    </div>

    <!-- RIGHT COLUMN: PEMERIKSAAN RW & RIWAYAT REVISI -->
    <div class="col-lg-4">
        <!-- RW REVIEW PANEL -->
        @if(Auth::user()->isAdminRw())
        <div class="card p-4 mb-4" style="border: 2px solid var(--nx-mint) !important;">
            <h5 class="fw-bold mb-3" style="color: var(--nx-forest);">
                <i class="fas fa-stamp me-2"></i>Pemeriksaan Admin RW
            </h5>
            <form action="{{ route('laporan_rt.review', $laporan->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-bold">Keputusan Pemeriksaan</label>
                    <select name="status" class="form-select" required>
                        <option value="diterima" {{ $laporan->status == 'diterima' ? 'selected' : '' }}>✅ Terima &amp; Sahkan Laporan</option>
                        <option value="perlu_revisi" {{ $laporan->status == 'perlu_revisi' ? 'selected' : '' }}>⚠️ Minta Revisi / Perbaikan</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Catatan RW / Alasan Revisi <span class="text-danger">*</span></label>
                    <textarea name="catatan_pemeriksaan_rw" rows="4" class="form-control" required placeholder="Tuliskan arahan, tindak lanjut atau catatan yang perlu diperbaiki oleh RT...">{{ $laporan->catatan_pemeriksaan_rw }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2.5" style="border-radius: 12px; font-weight: 700;">
                    <i class="fas fa-check-to-slot me-1"></i> Simpan Hasil Pemeriksaan
                </button>
            </form>
        </div>
        @else
        <!-- IF RT, DISPLAY RW REVIEW RESULT -->
        <div class="card p-4 mb-4">
            <h5 class="fw-bold mb-3" style="color: var(--nx-forest);">
                <i class="fas fa-stamp me-2"></i>Hasil Pemeriksaan Admin RW
            </h5>
            <div class="p-3 rounded-3 mb-2" style="background: {{ $laporan->status == 'diterima' ? 'var(--nx-mint-soft)' : ($laporan->status == 'perlu_revisi' ? 'var(--nx-danger-soft)' : '#f8f9fa') }};">
                <strong class="d-block small mb-1">Catatan Pemeriksa:</strong>
                <p class="mb-0 small text-dark">{{ $laporan->catatan_pemeriksaan_rw ?: 'Laporan belum diperiksa oleh Admin RW.' }}</p>
            </div>
            @if($laporan->pemeriksa)
                <small class="text-muted d-block">
                    Diperiksa oleh: <strong>{{ $laporan->pemeriksa->name }}</strong> · {{ $laporan->waktu_pemeriksaan ? $laporan->waktu_pemeriksaan->format('d/m/Y H:i') . ' WIB' : '-' }}
                </small>
            @endif
        </div>
        @endif

        <!-- RIWAYAT REVISI & AUDIT TIMELINE -->
        <div class="card p-4">
            <h5 class="fw-bold mb-3" style="color: var(--nx-forest); font-size: 0.96rem;">
                <i class="fas fa-timeline me-2"></i>Riwayat Versi &amp; Tindak Lanjut
            </h5>

            @php $history = $laporan->riwayat_revisi ?: []; @endphp

            @if(!empty($history))
                <ul class="list-unstyled mb-0" style="border-left: 2px solid var(--nx-border); padding-left: 16px; margin-left: 8px;">
                    @foreach(array_reverse($history) as $h)
                        <li class="mb-3 position-relative">
                            <span class="position-absolute" style="left: -22px; top: 2px; width: 10px; height: 10px; border-radius: 50%; background: var(--nx-forest);"></span>
                            <strong class="d-block small text-dark">{{ $h['action'] ?? ($h['updated_by'] ?? 'Pembaruan') }}</strong>
                            <span class="text-muted d-block" style="font-size: 0.72rem;">{{ $h['reviewed_at'] ?? ($h['updated_at'] ?? '-') }}</span>
                            @if(!empty($h['notes']))
                                <small class="text-muted d-block mt-1 bg-light p-1.5 rounded"><em>"{{ $h['notes'] }}"</em></small>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted small mb-0">Belum ada riwayat perbaikan/revisi pada berkas ini.</p>
            @endif
        </div>
    </div>
</div>
@endsection
