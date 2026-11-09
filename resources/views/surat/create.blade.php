@extends('layouts.app')

@section('title', 'Ajukan Surat Keterangan — NexaNest')

@section('content')
<div class="nx-page-header">
    <div>
        <span class="badge rounded-pill mb-2 px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
            <i class="fas fa-file-signature me-1"></i> Formulir E-Surat
        </span>
        <h1 class="nx-page-title">Ajukan Surat Keterangan Digital</h1>
        <p class="nx-page-subtitle">Permohonan otomatis diteruskan ke Pengurus RT dan RW untuk penandatanganan elektronik (TTE).</p>
    </div>
    <a href="{{ route('surat.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Surat
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card p-4">
            <form method="POST" action="{{ route('surat.store') }}">
                @csrf

                @if($user->isAdmin())
                    <div class="mb-4 p-3 rounded-4" style="background: var(--nx-mint-soft); border: 1px solid var(--nx-sage);">
                        <label class="form-label fw-bold" style="color: var(--nx-forest);">
                            <i class="fas fa-user-friends me-1"></i> Pilih Warga Pemohon <span class="text-danger">*</span>
                        </label>
                        <select name="target_user_id" class="form-select" required>
                            <option value="">-- Pilih Warga Terdaftar --</option>
                            @foreach($wargas as $w)
                                @php
                                    $wUser = \App\Models\User::where('nik', $w->nik)->first();
                                @endphp
                                @if($wUser)
                                    <option value="{{ $wUser->id }}" {{ old('target_user_id') == $wUser->id ? 'selected' : '' }}>
                                        {{ $w->nama }} (NIK: {{ $w->nik }} · RT {{ $w->rt_number }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        <small class="text-muted d-block mt-1">Hanya menampilkan warga yang telah memiliki akun aktif di sistem.</small>
                    </div>
                @else
                    <div class="p-3 mb-4 rounded-4" style="background: var(--nx-mint-soft); border: 1px solid var(--nx-sage);">
                        <h6 class="fw-bold mb-2" style="color: var(--nx-forest);">
                            <i class="fas fa-id-card me-1"></i> Data Pemohon Otomatis Terdeteksi
                        </h6>
                        @if($warga)
                            <div class="row g-2 small">
                                <div class="col-md-6">
                                    <span class="text-muted d-block">Nama Lengkap</span>
                                    <strong style="color: var(--nx-text);">{{ $warga->nama }}</strong>
                                </div>
                                <div class="col-md-3">
                                    <span class="text-muted d-block">NIK</span>
                                    <strong style="font-family: monospace;">{{ $warga->nik ?? '-' }}</strong>
                                </div>
                                <div class="col-md-3">
                                    <span class="text-muted d-block">No. KK</span>
                                    <strong style="font-family: monospace;">{{ $warga->no_kk ?? '-' }}</strong>
                                </div>
                                <div class="col-md-3">
                                    <span class="text-muted d-block">Jenis Kelamin</span>
                                    <strong>{{ ($warga->gender == 'L' || $warga->gender == 'Laki-laki') ? 'Laki-laki' : 'Perempuan' }}</strong>
                                </div>
                                <div class="col-md-3">
                                    <span class="text-muted d-block">Wilayah RT/RW</span>
                                    <strong>RT {{ $warga->rt_number }} / RW {{ $warga->rw_number }}</strong>
                                </div>
                                <div class="col-md-3">
                                    <span class="text-muted d-block">Blok Rumah</span>
                                    <strong>{{ $warga->blok_rumah }}</strong>
                                </div>
                                <div class="col-md-3">
                                    <span class="text-muted d-block">No. HP</span>
                                    <strong>{{ $warga->nomor_hp ?? '-' }}</strong>
                                </div>
                                <div class="col-12">
                                    <span class="text-muted d-block">Alamat Domisili</span>
                                    <strong>{{ $warga->alamat }}</strong>
                                </div>
                            </div>
                        @else
                            <div class="small" style="color: var(--nx-warning);">
                                <i class="fas fa-exclamation-triangle me-1"></i> Profil kependudukan Anda belum terhubung di database warga. Mohon hubungi Pengurus RT untuk sinkronisasi NIK akun Anda.
                            </div>
                        @endif
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label">1. Pilih Jenis Surat Keterangan <span class="text-danger">*</span></label>
                    <select name="jenis_surat" class="form-select" required>
                        <option value="">-- Pilih Surat Keterangan --</option>
                        <option value="Surat Keterangan Domisili" {{ old('jenis_surat') == 'Surat Keterangan Domisili' ? 'selected' : '' }}>Surat Keterangan Domisili</option>
                        <option value="Surat Keterangan Usaha (SKU)" {{ old('jenis_surat') == 'Surat Keterangan Usaha (SKU)' ? 'selected' : '' }}>Surat Keterangan Usaha (SKU)</option>
                        <option value="Surat Keterangan Tidak Mampu (SKTM)" {{ old('jenis_surat') == 'Surat Keterangan Tidak Mampu (SKTM)' ? 'selected' : '' }}>Surat Keterangan Tidak Mampu (SKTM)</option>
                        <option value="Surat Pengantar Kelakuan Baik (SKCK)" {{ old('jenis_surat') == 'Surat Pengantar Kelakuan Baik (SKCK)' ? 'selected' : '' }}>Surat Pengantar Kelakuan Baik (SKCK)</option>
                        <option value="Surat Pengantar Pernikahan" {{ old('jenis_surat') == 'Surat Pengantar Pernikahan' ? 'selected' : '' }}>Surat Pengantar Pernikahan</option>
                    </select>
                    @error('jenis_surat')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="form-label">2. Detail Keperluan / Tujuan Surat <span class="text-danger">*</span></label>
                    <textarea name="keperluan" class="form-control" rows="4"
                              placeholder="Jelaskan secara jelas tujuan pembuatan surat ini, contoh: 'Digunakan untuk persyaratan administrasi beasiswa pendidikan', dll."
                              required>{{ old('keperluan') }}</textarea>
                    @error('keperluan')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                    <a href="{{ route('surat.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                    <button type="submit" class="btn btn-primary px-4" {{ (!$user->isAdmin() && !$warga) ? 'disabled' : '' }}>
                        <i class="fas fa-paper-plane me-1"></i> {{ $user->isAdmin() ? 'Terbitkan Surat Sekarang' : 'Kirim Pengajuan Surat' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
