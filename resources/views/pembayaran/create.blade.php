@extends('layouts.app')

@section('title', 'Catat Transaksi Kas — NexaNest')

@section('content')
<div class="nx-page-header">
    <div>
        <span class="badge rounded-pill mb-2 px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
            <i class="fas fa-plus-circle me-1"></i> Pencatatan Kas RT/RW
        </span>
        <h1 class="nx-page-title">Catat Transaksi Kas Baru</h1>
        <p class="nx-page-subtitle">Catat pemasukan iuran warga atau pengeluaran operasional fasilitas lingkungan secara transparan.</p>
    </div>
    <a href="{{ route('pembayaran.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Kembali ke Riwayat Kas
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card p-4">
            <form action="{{ route('pembayaran.store') }}" method="POST">
                @csrf

                <div class="row mb-3 g-3">
                    <div class="col-md-6">
                        <label class="form-label">Tipe Transaksi <span class="text-danger">*</span></label>
                        <select name="tipe" class="form-select" required>
                            <option value="masuk" {{ old('tipe') == 'masuk' ? 'selected' : '' }}>Pemasukan (Iuran / Donasi)</option>
                            <option value="keluar" {{ old('tipe') == 'keluar' ? 'selected' : '' }}>Pengeluaran (Beban Operasional)</option>
                        </select>
                        @error('tipe')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Transaksi <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                        @error('tanggal')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Pilih Warga Pembayar (Opsional untuk Kas Umum)</label>
                    <select name="warga_id" class="form-select">
                        <option value="">-- Kas Umum / Non-Warga --</option>
                        @foreach($warga as $w)
                            <option value="{{ $w->id }}" {{ old('warga_id') == $w->id ? 'selected' : '' }}>
                                {{ $w->nama }} (Blok {{ $w->blok_rumah }} · RT {{ $w->rt_number }})
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text text-muted small">
                        <i class="fas fa-info-circle me-1"></i> Kosongkan pilihan apabila transaksi ini bersifat pengeluaran rutin/umum lingkungan.
                    </div>
                    @error('warga_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="row mb-3 g-3">
                    <div class="col-md-6">
                        <label class="form-label">Kategori Transaksi <span class="text-danger">*</span></label>
                        <select name="kategori" class="form-select" required>
                            <option value="">-- Pilih Kategori --</option>
                            <optgroup label="Pemasukan (Masuk)">
                                <option value="Iuran Bulanan" {{ old('kategori') == 'Iuran Bulanan' ? 'selected' : '' }}>Iuran Bulanan</option>
                                <option value="Iuran Sukarela" {{ old('kategori') == 'Iuran Sukarela' ? 'selected' : '' }}>Iuran Sukarela</option>
                                <option value="Donasi Warga" {{ old('kategori') == 'Donasi Warga' ? 'selected' : '' }}>Donasi Warga</option>
                            </optgroup>
                            <optgroup label="Pengeluaran (Beban)">
                                <option value="Kebersihan" {{ old('kategori') == 'Kebersihan' ? 'selected' : '' }}>Kebersihan (Sampah)</option>
                                <option value="Keamanan" {{ old('kategori') == 'Keamanan' ? 'selected' : '' }}>Keamanan (Security/Siskamling)</option>
                                <option value="Listrik & Air" {{ old('kategori') == 'Listrik & Air' ? 'selected' : '' }}>Listrik &amp; Air Fasum</option>
                                <option value="Perbaikan/Maintenance" {{ old('kategori') == 'Perbaikan/Maintenance' ? 'selected' : '' }}>Perbaikan Fasilitas / Alat RT</option>
                                <option value="Lain-lain" {{ old('kategori') == 'Lain-lain' ? 'selected' : '' }}>Lain-lain</option>
                            </optgroup>
                        </select>
                        @error('kategori')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jumlah Nominal (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="jumlah" value="{{ old('jumlah') }}" class="form-control" required placeholder="Contoh: 100000" min="0">
                        @error('jumlah')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Keterangan / Catatan Tambahan</label>
                    <textarea name="keterangan" class="form-control" rows="3" placeholder="Tuliskan catatan rincian transaksi ini...">{{ old('keterangan') }}</textarea>
                    @error('keterangan')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                    <a href="{{ route('pembayaran.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> Simpan Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
