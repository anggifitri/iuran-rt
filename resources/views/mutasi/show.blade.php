@extends('layouts.app')

@section('title', 'Detail Mutasi: ' . $mutasi->nama_warga . ' — NexaNest')

@section('content')
<div class="nx-page-header">
    <div>
        <a href="{{ route('mutasi.index') }}" class="btn btn-sm btn-outline-secondary mb-2" style="border-radius: 8px;">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Mutasi
        </a>
        <h1 class="nx-page-title">Arsip Mutasi: {{ $mutasi->nama_warga }}</h1>
        <p class="nx-page-subtitle">
            Dokumentasi lengkap perpindahan kependudukan dan surat keterangan pendukung.
        </p>
    </div>
    <div class="d-flex align-items-center gap-2">
        @if($mutasi->tipe == 'masuk')
            <span class="badge rounded-pill px-3 py-2" style="background: var(--nx-mint); color: var(--nx-forest); font-weight: 700;">
                <i class="fas fa-arrow-right-to-bracket me-1"></i>Pindah Masuk (Warga Baru)
            </span>
        @else
            <span class="badge rounded-pill px-3 py-2" style="background: var(--nx-danger-soft); color: var(--nx-danger); font-weight: 700;">
                <i class="fas fa-arrow-right-from-bracket me-1"></i>Pindah Keluar Wilayah
            </span>
        @endif
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card p-4 mb-4">
            <h5 class="fw-bold mb-3" style="color: var(--nx-forest);">
                <i class="fas fa-file-invoice me-2"></i>Rincian Mutasi Kependudukan
            </h5>

            <table class="table table-bordered align-middle">
                <tbody>
                    <tr>
                        <th style="width: 30%; background: #fbfbfb;">Nama Warga</th>
                        <td class="fw-bold text-dark">{{ $mutasi->nama_warga }}</td>
                    </tr>
                    <tr>
                        <th style="background: #fbfbfb;">Nomor Kependudukan (NIK)</th>
                        <td><code>{{ $mutasi->nik ?: 'Tidak tercantum' }}</code></td>
                    </tr>
                    <tr>
                        <th style="background: #fbfbfb;">Tanggal Efektif Mutasi</th>
                        <td>{{ \Carbon\Carbon::parse($mutasi->tanggal_mutasi)->translatedFormat('d F Y') }}</td>
                    </tr>
                    <tr>
                        <th style="background: #fbfbfb;">Wilayah Asal</th>
                        <td>
                            @if($mutasi->rt_asal) <strong>RT {{ $mutasi->rt_asal }}</strong>, @endif
                            {{ $mutasi->alamat_asal ?: 'Luar Wilayah RW 018' }}
                        </td>
                    </tr>
                    <tr>
                        <th style="background: #fbfbfb;">Wilayah Tujuan</th>
                        <td>
                            @if($mutasi->rt_tujuan) <strong class="text-success">RT {{ $mutasi->rt_tujuan }} (RW 018)</strong>, @endif
                            {{ $mutasi->alamat_tujuan ?: 'Luar Wilayah' }}
                        </td>
                    </tr>
                    <tr>
                        <th style="background: #fbfbfb;">Alasan Kepindahan</th>
                        <td>{{ $mutasi->alasan }}</td>
                    </tr>
                    <tr>
                        <th style="background: #fbfbfb;">Petugas Pencatat</th>
                        <td>
                            {{ $mutasi->creator ? $mutasi->creator->name : 'Sistem' }}
                            <small class="text-muted d-block">{{ $mutasi->created_at->format('d/m/Y H:i') }} WIB</small>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card p-4">
            <h5 class="fw-bold mb-3" style="color: var(--nx-forest);">
                <i class="fas fa-paperclip me-2"></i>Berkas Pendukung
            </h5>

            @if($mutasi->dokumen_pendukung)
                <div class="text-center p-3 bg-light rounded-3 mb-3">
                    <i class="fas fa-file-pdf text-danger fa-3x mb-2"></i>
                    <strong class="d-block small text-dark mb-2">Dokumen Surat Pindah</strong>
                    <a href="{{ asset('storage/' . $mutasi->dokumen_pendukung) }}" target="_blank" class="btn btn-sm btn-outline-primary" style="border-radius: 8px;">
                        <i class="fas fa-download me-1"></i> Buka / Unduh Berkas
                    </a>
                </div>
            @else
                <div class="text-center py-4 bg-light rounded-3 text-muted">
                    <i class="fas fa-file-excel fa-2x mb-2" style="opacity: 0.4;"></i>
                    <p class="small mb-0">Tidak ada lampiran dokumen digital yang diunggah.</p>
                </div>
            @endif

            <hr>
            <div class="small text-muted">
                <i class="fas fa-info-circle me-1"></i> Catatan sistem: Data kependudukan warga tetap diarsipkan secara historis dan tidak dihapus dari basis data demi kelengkapan sensus RW.
            </div>
        </div>
    </div>
</div>
@endsection
