@extends('layouts.app')

@section('title', 'Tambah Transaksi Kas')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 15px;">
            <div class="card-header bg-transparent border-bottom border-secondary p-3">
                <h5 class="mb-0 text-white"><i class="fas fa-plus-circle me-2 text-primary"></i>Catat Transaksi Baru</h5>
            </div>

            <div class="card-body p-4">
                <form action="{{ route('pembayaran.store') }}" method="POST">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-white-50 small">Tipe Transaksi</label>
                            <select name="tipe" class="form-select bg-dark text-white border-secondary" required>
                                <option value="masuk">Pemasukan (Iuran/Donasi)</option>
                                <option value="keluar">Pengeluaran (Beban Kas)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white-50 small">Tanggal</label>
                            <input type="date" name="tanggal" class="form-select bg-dark text-white border-secondary" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-white-50 small">Pilih Warga (Opsional)</label>
                        <select name="warga_id" class="form-select bg-dark text-white border-secondary">
                            <option value="">-- Umum / Non-Warga --</option>
                            @foreach($warga as $w)
                                <option value="{{ $w->id }}">{{ $w->nama }} - {{ $w->blok_rumah }}</option>
                            @endforeach
                        </select>
                        <div class="form-text text-muted small">Kosongkan jika ini pengeluaran rutin/umum.</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-white-50 small">Kategori</label>
                            <select name="kategori" class="form-select bg-dark text-white border-secondary" required>
                                <option value="">-- Pilih Kategori --</option>
                                <optgroup label="Pemasukan (Iuran)">
                                    <option value="Iuran Bulanan">Iuran Bulanan</option>
                                    <option value="Iuran Sukarela">Iuran Sukarela</option>
                                    <option value="Donasi Warga">Donasi Warga</option>
                                </optgroup>
                                <optgroup label="Pengeluaran (Beban)">
                                    <option value="Kebersihan">Kebersihan (Tukang Sampah)</option>
                                    <option value="Keamanan">Keamanan (Scurity/Siskamling)</option>
                                    <option value="Listrik & Air">Listrik & Air Fasum</option>
                                    <option value="Perbaikan/Maintenance">Perbaikan / Alat RT</option>
                                    <option value="Lain-lain">Lain-lain</option>
                                </optgroup>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white-50 small">Jumlah (Nominal)</label>
                            <select name="jumlah" class="form-select bg-dark text-white border-secondary" required>
                                <option value="">-- Pilih Nominal --</option>
                                <option value="20000">Rp 20.000</option>
                                <option value="50000">Rp 50.000</option>
                                <option value="100000">Rp 100.000</option>
                                <option value="500000">Rp 500.000</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-white-50 small">Keterangan Tambahan</label>
                        <textarea name="keterangan" class="form-control bg-dark text-white border-secondary" rows="3" placeholder="Tambahkan catatan jika perlu..."></textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('pembayaran.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-2"></i>Simpan Transaksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
