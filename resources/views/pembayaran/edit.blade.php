@extends('layouts.app')

@section('title', 'Edit Transaksi Kas — NexaNest')

@section('content')
@php
    $user = Auth::user();
    $isAdminRw = $user->isAdminRw();
    $isAdminRt = $user->isAdminRt();
@endphp

<div class="nx-page-header">
    <div>
        <span class="badge rounded-pill mb-2 px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
            <i class="fas fa-edit me-1"></i> Perbarui Transaksi
        </span>
        <h1 class="nx-page-title">Edit Transaksi {{ $pembayaran->nomor_transaksi_formatted }}</h1>
        <p class="nx-page-subtitle">Perbarui data transaksi kas lingkungan. Seluruh perubahan tercatat di riwayat audit sistem.</p>
    </div>
    <a href="{{ route('pembayaran.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Kembali ke Riwayat Transaksi
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-9 col-xl-8">
        <div class="card p-4 border-0 shadow-sm">
            <form action="{{ route('pembayaran.update', $pembayaran->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- INFO NOMOR TRANSAKSI -->
                <div class="p-3 mb-4 rounded-3 d-flex justify-content-between align-items-center" style="background: var(--nx-bg); border: 1px solid var(--nx-border);">
                    <div>
                        <span class="text-muted small d-block">Nomor Identifikasi Transaksi:</span>
                        <span class="font-monospace fw-bold fs-5" style="color: var(--nx-forest);">{{ $pembayaran->nomor_transaksi_formatted }}</span>
                    </div>
                    <span class="badge rounded-pill px-3 py-1.5 {{ $pembayaran->tipe === 'masuk' ? 'bg-success' : 'bg-danger' }}">
                        <i class="fas {{ $pembayaran->tipe === 'masuk' ? 'fa-arrow-down' : 'fa-arrow-up' }} me-1"></i>
                        {{ $pembayaran->tipe === 'masuk' ? 'Pemasukan' : 'Pengeluaran' }}
                    </span>
                </div>

                <!-- 1. TANGGAL & JUMLAH NOMINAL -->
                <div class="row mb-3 g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Tanggal Transaksi <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', $pembayaran->tanggal ? $pembayaran->tanggal->format('Y-m-d') : '') }}" required>
                        @error('tanggal')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Jumlah Nominal (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold">Rp</span>
                            <input type="number" name="jumlah" class="form-control" value="{{ old('jumlah', $pembayaran->jumlah) }}" required min="1" step="1">
                        </div>
                        @error('jumlah')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                <!-- 2. KATEGORI & WILAYAH -->
                <div class="row mb-3 g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Kategori Transaksi <span class="text-danger">*</span></label>
                        <select name="kategori" class="form-select" required>
                            @php
                                $kategoriList = $pembayaran->tipe === 'masuk' ? $kategoriMasuk : $kategoriKeluar;
                            @endphp
                            @foreach($kategoriList as $kItem)
                                <option value="{{ $kItem }}" {{ old('kategori', $pembayaran->kategori) === $kItem ? 'selected' : '' }}>{{ $kItem }}</option>
                            @endforeach
                        </select>
                        @error('kategori')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Alokasi Wilayah RT/RW</label>
                        @if($isAdminRt)
                            <input type="text" class="form-control bg-light" value="RT {{ $pembayaran->rt_number }}" readonly>
                        @else
                            <select name="rt_number" class="form-select">
                                <option value="000" {{ old('rt_number', $pembayaran->rt_number) === '000' ? 'selected' : '' }}>RW 018 (Kas Induk RW)</option>
                                <option value="006" {{ old('rt_number', $pembayaran->rt_number) === '006' ? 'selected' : '' }}>RT 006</option>
                                <option value="007" {{ old('rt_number', $pembayaran->rt_number) === '007' ? 'selected' : '' }}>RT 007</option>
                                <option value="008" {{ old('rt_number', $pembayaran->rt_number) === '008' ? 'selected' : '' }}>RT 008</option>
                                <option value="009" {{ old('rt_number', $pembayaran->rt_number) === '009' ? 'selected' : '' }}>RT 009</option>
                                <option value="010" {{ old('rt_number', $pembayaran->rt_number) === '010' ? 'selected' : '' }}>RT 010</option>
                            </select>
                        @endif
                    </div>
                </div>

                <!-- 3. WARGA TERKAIT -->
                <div class="mb-3">
                    <label class="form-label fw-bold small">Warga Pembayar / Terkait (Opsional)</label>
                    <select name="warga_id" class="form-select">
                        <option value="">-- Kas Umum / Non-Warga / Pihak Ketiga --</option>
                        @foreach($warga as $w)
                            <option value="{{ $w->id }}" {{ old('warga_id', $pembayaran->warga_id) == $w->id ? 'selected' : '' }}>
                                {{ $w->nama }} (Blok {{ $w->blok_rumah ?? '-' }} · RT {{ $w->rt_number }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 4. SUMBER / TUJUAN & METODE -->
                <div class="row mb-3 g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Sumber / Tujuan Dana</label>
                        <input type="text" name="sumber_tujuan" class="form-control" value="{{ old('sumber_tujuan', $pembayaran->sumber_tujuan) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Metode Pembayaran</label>
                        <select name="metode_pembayaran" class="form-select">
                            <option value="Tunai" {{ old('metode_pembayaran', $pembayaran->metode_pembayaran) === 'Tunai' ? 'selected' : '' }}>Tunai / Cash</option>
                            <option value="Transfer Bank" {{ old('metode_pembayaran', $pembayaran->metode_pembayaran) === 'Transfer Bank' ? 'selected' : '' }}>Transfer Bank</option>
                            <option value="QRIS" {{ old('metode_pembayaran', $pembayaran->metode_pembayaran) === 'QRIS' ? 'selected' : '' }}>QRIS Lingkungan</option>
                        </select>
                    </div>
                </div>

                <!-- 5. NOMOR REFERENSI & BUKTI TRANSAKSI -->
                <div class="row mb-3 g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Nomor Referensi (Opsional)</label>
                        <input type="text" name="nomor_referensi" class="form-control" value="{{ old('nomor_referensi', $pembayaran->nomor_referensi) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Ganti Bukti / Struk Transaksi</label>
                        <input type="file" name="bukti_transaksi" class="form-control" accept="image/*,.pdf">
                        @if($pembayaran->bukti_transaksi)
                            <div class="form-text small">
                                <a href="{{ asset('storage/' . $pembayaran->bukti_transaksi) }}" target="_blank" class="text-primary">
                                    <i class="fas fa-file-image me-1"></i>Lihat bukti transaksi saat ini
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- 6. KETERANGAN -->
                <div class="mb-4">
                    <label class="form-label fw-bold small">Keterangan / Uraian Rincian <span class="text-danger">*</span></label>
                    <textarea name="keterangan" class="form-control" rows="3" required>{{ old('keterangan', $pembayaran->keterangan) }}</textarea>
                    @error('keterangan')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                    <a href="{{ route('pembayaran.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
