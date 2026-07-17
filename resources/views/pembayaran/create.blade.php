@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4" style="background-color: var(--bg-card); border: 1px solid var(--border-color) !important;">
                <div class="card-header bg-transparent p-4 border-bottom" style="border-color: var(--border-color) !important;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-plus-circle fa-lg"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0 text-dark" style="color: var(--text-main) !important;">Catat Transaksi Kas Baru</h4>
                            <p class="text-muted small mb-0">Catat pemasukan iuran warga atau pengeluaran operasional fasilitas secara terintegrasi.</p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('pembayaran.store') }}" method="POST">
                        @csrf

                        <div class="row mb-3 g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary">Tipe Transaksi</label>
                                <select name="tipe" class="form-select" required style="border-radius: 10px;">
                                    <option value="masuk">Pemasukan (Iuran / Donasi)</option>
                                    <option value="keluar">Pengeluaran (Beban Operasional)</option>
                                </select>
                                @error('tipe')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary">Tanggal Transaksi</label>
                                <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required style="border-radius: 10px;">
                                @error('tanggal')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary">Pilih Warga Pembayar (Opsional)</label>
                            <select name="warga_id" class="form-select" style="border-radius: 10px;">
                                <option value="">-- Kas Umum / Non-Warga --</option>
                                @foreach($warga as $w)
                                    <option value="{{ $w->id }}">{{ $w->nama }} (Blok {{ $w->blok_rumah }} · RT {{ $w->rt_number }})</option>
                                @endforeach
                            </select>
                            <div class="form-text text-muted small"><i class="fas fa-info-circle me-1"></i> Kosongkan pilihan jika transaksi ini bersifat pengeluaran rutin/umum kemasyarakatan.</div>
                            @error('warga_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="row mb-3 g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary">Kategori Transaksi</label>
                                <select name="kategori" class="form-select" required style="border-radius: 10px;">
                                    <option value="">-- Pilih Kategori --</option>
                                    <optgroup label="Pemasukan (Masuk)">
                                        <option value="Iuran Bulanan">Iuran Bulanan</option>
                                        <option value="Iuran Sukarela">Iuran Sukarela</option>
                                        <option value="Donasi Warga">Donasi Warga</option>
                                    </optgroup>
                                    <optgroup label="Pengeluaran (Beban)">
                                        <option value="Kebersihan">Kebersihan (Sampah)</option>
                                        <option value="Keamanan">Keamanan (Security/Siskamling)</option>
                                        <option value="Listrik & Air">Listrik & Air Fasum</option>
                                        <option value="Perbaikan/Maintenance">Perbaikan Fasilitas / Alat RT</option>
                                        <option value="Lain-lain">Lain-lain</option>
                                    </optgroup>
                                </select>
                                @error('kategori')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary">Jumlah (Nominal Rp)</label>
                                <input type="number" name="jumlah" class="form-control" required placeholder="Masukkan angka tanpa titik, cth: 100000" min="0" style="border-radius: 10px;">
                                @error('jumlah')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-secondary">Keterangan Tambahan</label>
                            <textarea name="keterangan" class="form-control" rows="3" placeholder="Tuliskan catatan opsional mengenai transaksi ini..." style="border-radius: 10px;"></textarea>
                            @error('keterangan')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <a href="{{ route('pembayaran.index') }}" class="btn btn-outline-secondary px-4 py-2.5" style="border-radius: 10px;"><i class="fas fa-times me-1"></i>Batal</a>
                            <button type="submit" class="btn btn-primary px-4 py-2.5 shadow-sm" style="border-radius: 10px; font-weight: 600;">
                                <i class="fas fa-save me-1"></i>Simpan Transaksi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
