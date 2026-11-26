@extends('layouts.app')

@section('title', 'Detail Verifikasi: ' . $warga->nama . ' — NexaNest')

@section('content')
<div class="nx-page-header">
    <div>
        <a href="{{ route('verifikasi.index') }}" class="btn btn-sm btn-outline-secondary mb-2" style="border-radius: 8px;">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Verifikasi
        </a>
        <h1 class="nx-page-title">Pemeriksaan Dokumen: {{ $warga->nama }}</h1>
        <p class="nx-page-subtitle">
            Detail data warga, rekam kartu keluarga, dan formulir pengesahan resmi tingkat RW / RT.
        </p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge rounded-pill px-3 py-2"
              style="font-size: 0.85rem; font-weight: 700;
              @if($warga->status_verifikasi == 'terverifikasi') background: var(--nx-mint); color: var(--nx-forest);
              @elseif($warga->status_verifikasi == 'ditolak') background: var(--nx-danger-soft); color: var(--nx-danger);
              @elseif($warga->status_verifikasi == 'perbaikan') background: var(--nx-powder); color: #1e4d72;
              @else background: var(--nx-warning-soft); color: var(--nx-warning); @endif">
            Status: {{ strtoupper($warga->status_verifikasi ?? 'MENUNGGU') }}
        </span>
    </div>
</div>

<div class="row g-4">
    <!-- LEFT COLUMN: IDENTITAS & FOTO -->
    <div class="col-lg-4">
        <div class="card p-4 text-center">
            <img src="{{ $warga->profile_photo_url }}" alt="{{ $warga->nama }}" class="rounded-circle mx-auto mb-3"
                 style="width: 120px; height: 120px; object-fit: cover; border: 4px solid var(--nx-mint); box-shadow: var(--nx-shadow);">
            
            <h4 class="fw-bold mb-1" style="color: var(--nx-forest);">{{ $warga->nama }}</h4>
            <span class="badge rounded-pill px-3 py-1 mb-3" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-weight: 700;">
                {{ $warga->status_hubungan }}
            </span>

            <ul class="list-group list-group-flush text-start small">
                <li class="list-group-item d-flex justify-content-between px-0">
                    <span class="text-muted">Wilayah:</span>
                    <strong class="text-dark">RT {{ $warga->rt_number }} / RW {{ $warga->rw_number ?? '018' }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between px-0">
                    <span class="text-muted">Blok Rumah:</span>
                    <strong class="text-dark">{{ $warga->blok_rumah ?: '-' }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between px-0">
                    <span class="text-muted">Jenis Kelamin:</span>
                    <strong class="text-dark">{{ $warga->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between px-0">
                    <span class="text-muted">Usia:</span>
                    <strong class="text-dark">{{ $warga->umur_formatted }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between px-0">
                    <span class="text-muted">RFID E-KTP UID:</span>
                    <strong class="text-dark"><code>{{ $warga->rfid_uid ?: 'Belum Direkam' }}</code></strong>
                </li>
            </ul>
        </div>
    </div>

    <!-- RIGHT COLUMN: DETAIL FORM & KEPUTUSAN VERIFIKASI -->
    <div class="col-lg-8">
        <div class="card p-4 mb-4">
            <h5 class="fw-bold mb-3" style="color: var(--nx-forest);">
                <i class="fas fa-id-card me-2"></i>Informasi Kependudukan Lengkap
            </h5>
            
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="text-muted small d-block">Nomor Induk Kependudukan (NIK)</label>
                    @php
                        $maskedNik = $warga->nik ? (strlen($warga->nik) >= 12 ? substr($warga->nik, 0, 6) . '******' . substr($warga->nik, -4) : $warga->nik) : '-';
                    @endphp
                    <div class="d-flex align-items-center gap-2">
                        <span class="fs-6 fw-bold text-dark font-monospace" id="nikDisplay">{{ $maskedNik }}</span>
                        @if($warga->nik)
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 0.72rem;" onclick="toggleNik()">
                                <i class="fas fa-eye" id="nikIcon"></i>
                            </button>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="text-muted small d-block">Nomor Kartu Keluarga (No. KK)</label>
                    <span class="fs-6 fw-bold text-dark font-monospace">{{ $warga->no_kk ?: '-' }}</span>
                </div>
                <div class="col-md-6">
                    <label class="text-muted small d-block">Nomor Kontak / WhatsApp</label>
                    <span class="fw-semibold text-dark">{{ $warga->nomor_hp ?: '-' }}</span>
                </div>
                <div class="col-md-6">
                    <label class="text-muted small d-block">Tanggal Lahir</label>
                    <span class="fw-semibold text-dark">{{ $warga->tanggal_lahir ? \Carbon\Carbon::parse($warga->tanggal_lahir)->translatedFormat('d F Y') : '-' }}</span>
                </div>
                <div class="col-12">
                    <label class="text-muted small d-block">Alamat Tempat Tinggal</label>
                    <p class="fw-semibold text-dark mb-0 bg-light p-3 rounded-3">{{ $warga->alamat ?: 'Belum diisi' }}</p>
                </div>
            </div>
        </div>

        <!-- FORM KEPUTUSAN VERIFIKASI -->
        <div class="card p-4" style="border: 2px solid var(--nx-mint) !important;">
            <h5 class="fw-bold mb-3" style="color: var(--nx-forest);">
                <i class="fas fa-signature me-2"></i>Keputusan Pengesahan Verifikasi Petugas
            </h5>

            <form action="{{ route('verifikasi.update_status', $warga->id) }}" method="POST">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Status Verifikasi</label>
                        <select name="status_verifikasi" class="form-select" required>
                            <option value="terverifikasi" {{ $warga->status_verifikasi == 'terverifikasi' ? 'selected' : '' }}>✅ Sahkan &amp; Terverifikasi</option>
                            <option value="perbaikan" {{ $warga->status_verifikasi == 'perbaikan' ? 'selected' : '' }}>⚠️ Minta Perbaikan Berkas</option>
                            <option value="ditolak" {{ $warga->status_verifikasi == 'ditolak' ? 'selected' : '' }}>❌ Tolak Pengajuan</option>
                            <option value="menunggu" {{ $warga->status_verifikasi == 'menunggu' ? 'selected' : '' }}>⏳ Menunggu Review</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Terakhir Diperiksa Pada</label>
                        <input type="text" class="form-control bg-light" readonly value="{{ $warga->tanggal_verifikasi ? \Carbon\Carbon::parse($warga->tanggal_verifikasi)->format('d/m/Y H:i') . ' WIB' : 'Belum pernah diperiksa' }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Catatan Petugas Pemeriksa</label>
                        <textarea name="catatan_verifikasi" rows="3" class="form-control" placeholder="Tuliskan catatan verifikasi, nomor registrasi, atau petunjuk perbaikan jika berkas dikembalikan...">{{ $warga->catatan_verifikasi }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <span class="small text-muted"><i class="fas fa-lock me-1"></i>Aksi ini akan dicatat ke dalam log audit resmi.</span>
                    <button type="submit" class="btn btn-primary" style="border-radius: 12px; font-weight: 700;">
                        <i class="fas fa-floppy-disk me-1"></i> Simpan Keputusan Verifikasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let fullNik = "{{ $warga->nik }}";
let maskedNik = "{{ $maskedNik }}";
let isRevealed = false;

function toggleNik() {
    let display = document.getElementById('nikDisplay');
    let icon = document.getElementById('nikIcon');
    if (isRevealed) {
        display.innerText = maskedNik;
        icon.className = 'fas fa-eye';
        isRevealed = false;
    } else {
        display.innerText = fullNik;
        icon.className = 'fas fa-eye-slash';
        isRevealed = true;
    }
}
</script>
@endsection
