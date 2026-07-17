@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4" style="background-color: var(--bg-card); border: 1px solid var(--border-color) !important;">
                <div class="card-header bg-transparent p-4 border-bottom" style="border-color: var(--border-color) !important;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-file-signature fa-lg"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0 text-dark" style="color: var(--text-main) !important;">Ajukan Surat Keterangan Digital</h4>
                            <p class="text-muted small mb-0">Permohonan otomatis terkirim ke RT dan RW untuk penandatanganan elektronik (TTE).</p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <!-- PREVIEW PROFILE / SELECT WARGA (FOR ADMIN) -->
                    @if($user->isAdmin())
                        <div class="mb-4 p-3 rounded-3 border bg-light" style="border-color: var(--border-color) !important;">
                            <h6 class="fw-bold text-secondary mb-3"><i class="fas fa-user-friends text-primary me-1"></i> Pilih Warga Pemohon:</h6>
                            <select name="target_user_id" class="form-select form-select-lg" required style="border-radius: 10px;">
                                <option value="">-- Pilih Warga --</option>
                                @foreach($wargas as $w)
                                    @php
                                        // Cari apakah warga tersebut memiliki user account login
                                        $wUser = \App\Models\User::where('nik', $w->nik)->first();
                                    @endphp
                                    @if($wUser)
                                        <option value="{{ $wUser->id }}" {{ old('target_user_id') == $wUser->id ? 'selected' : '' }}>
                                            {{ $w->nama }} (NIK: {{ $w->nik }} · RT {{ $w->rt_number }})
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            <span class="text-muted small mt-2 d-block"><i class="fas fa-info-circle me-1"></i> Hanya menampilkan warga yang telah memiliki akun login di sistem.</span>
                        </div>
                    @else
                        <!-- PREVIEW PROFILE (AUTO-DETECTION NOTICE FOR CITIZENS) -->
                        <div class="p-3 mb-4 rounded-3 border bg-light" style="border-color: var(--border-color) !important;">
                            <h6 class="fw-bold text-secondary mb-2"><i class="fas fa-info-circle text-primary me-1"></i> Data Pemohon Otomatis Terdeteksi:</h6>
                            @if($warga)
                                <div class="row g-3 small text-secondary">
                                    <div class="col-md-6">
                                        <strong>Nama Lengkap:</strong> <span class="text-dark fw-semibold d-block">{{ $warga->nama }}</span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>NIK:</strong> <span class="text-dark fw-semibold d-block">{{ $warga->nik ?? '-' }}</span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>No. KK:</strong> <span class="text-dark fw-semibold d-block">{{ $warga->no_kk ?? '-' }}</span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Gender:</strong> <span class="text-dark fw-semibold d-block">{{ $warga->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>RT/RW:</strong> <span class="text-dark fw-semibold d-block">RT {{ $warga->rt_number }} / RW {{ $warga->rw_number }}</span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Blok Rumah:</strong> <span class="text-dark fw-semibold d-block">{{ $warga->blok_rumah }}</span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>No. HP:</strong> <span class="text-dark fw-semibold d-block">{{ $warga->nomor_hp ?? '-' }}</span>
                                    </div>
                                    <div class="col-md-12">
                                        <strong>Alamat Lengkap:</strong> <span class="text-dark d-block">{{ $warga->alamat }}</span>
                                    </div>
                                </div>
                            @else
                                <div class="text-warning small">
                                    <i class="fas fa-exclamation-triangle me-1"></i> Profil kependudukan Anda belum terdaftar di database warga. Mohon hubungi Admin RT untuk sinkronisasi NIK akun Anda.
                                </div>
                            @endif
                        </div>
                    @endif

                    <form method="POST" action="{{ route('surat.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary">1. Pilih Jenis Surat</label>
                            <select name="jenis_surat" class="form-select form-select-lg" required style="border-radius: 10px;">
                                <option value="">-- Pilih Surat Keterangan --</option>
                                <option value="Surat Keterangan Domisili">Surat Keterangan Domisili</option>
                                <option value="Surat Keterangan Usaha (SKU)">Surat Keterangan Usaha (SKU)</option>
                                <option value="Surat Keterangan Tidak Mampu (SKTM)">Surat Keterangan Tidak Mampu (SKTM)</option>
                                <option value="Surat Pengantar Kelakuan Baik (SKCK)">Surat Pengantar Kelakuan Baik (SKCK)</option>
                                <option value="Surat Pengantar Pernikahan">Surat Pengantar Pernikahan</option>
                            </select>
                            @error('jenis_surat')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary">2. Detail Keperluan / Tujuan Surat</label>
                            <textarea name="keperluan" class="form-control" rows="4" placeholder="Jelaskan secara jelas tujuan pembuatan surat ini, contoh: 'Digunakan untuk syarat pendaftaran beasiswa anak', 'Sebagai syarat pengajuan pinjaman modal usaha di bank', dll." required style="border-radius: 10px;"></textarea>
                            @error('keperluan')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <a href="{{ route('surat.index') }}" class="btn btn-outline-secondary px-4 py-2.5" style="border-radius: 10px;"><i class="fas fa-arrow-left me-1"></i>Kembali</a>
                            <button type="submit" class="btn btn-primary btn-lg px-4 py-2.5 shadow-sm" style="border-radius: 10px; font-weight: 600;" {{ (!$user->isAdmin() && !$warga) ? 'disabled' : '' }}>
                                <i class="fas fa-paper-plane me-1"></i>{{ $user->isAdmin() ? 'Terbitkan Surat Secara Instan' : 'Kirim Pengajuan Surat' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
