@extends('layouts.app')

@section('title', 'Catat Transaksi Kas Baru — NexaNest')

@section('content')
@php
    $user = Auth::user();
    $isAdminRw = $user->isAdminRw();
    $isAdminRt = $user->isAdminRt();
@endphp

<div class="nx-page-header">
    <div>
        <span class="badge rounded-pill mb-2 px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
            <i class="fas fa-plus-circle me-1"></i> Form Keuangan Lingkungan
        </span>
        <h1 class="nx-page-title">Catat Transaksi Kas Baru</h1>
        <p class="nx-page-subtitle">Catat pemasukan kas atau pengeluaran operasional lingkungan {{ $isAdminRt ? 'RT ' . $user->rt_number : 'RW 018' }} secara akurat.</p>
    </div>
    <a href="{{ route('pembayaran.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Kembali ke Riwayat Transaksi
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-9 col-xl-8">
        <div class="card p-4 border-0 shadow-sm">
            <form action="{{ route('pembayaran.store') }}" method="POST" enctype="multipart/form-data" id="formCreateTx">
                @csrf

                <!-- 1. TIPE TRANSAKSI (TOGGLE BUTTONS) -->
                <div class="mb-4">
                    <label class="form-label fw-bold small text-muted text-uppercase mb-2">Tipe Arus Kas <span class="text-danger">*</span></label>
                    <div class="row g-2">
                        <div class="col-6">
                            <input type="radio" class="btn-check" name="tipe" id="tipe_masuk" value="masuk" {{ old('tipe', 'masuk') === 'masuk' ? 'checked' : '' }} onchange="switchTipe('masuk')">
                            <label class="btn btn-outline-success w-100 py-3 text-center d-flex flex-column align-items-center gap-1 rounded-3" for="tipe_masuk">
                                <i class="fas fa-arrow-down fa-lg"></i>
                                <span class="fw-bold">Pemasukan Kas (Masuk)</span>
                                <small class="text-muted" style="font-size: 0.72rem;">Iuran warga, donasi, bantuan, bank sampah</small>
                            </label>
                        </div>
                        <div class="col-6">
                            <input type="radio" class="btn-check" name="tipe" id="tipe_keluar" value="keluar" {{ old('tipe') === 'keluar' ? 'checked' : '' }} onchange="switchTipe('keluar')">
                            <label class="btn btn-outline-danger w-100 py-3 text-center d-flex flex-column align-items-center gap-1 rounded-3" for="tipe_keluar">
                                <i class="fas fa-arrow-up fa-lg"></i>
                                <span class="fw-bold">Pengeluaran Operasional (Keluar)</span>
                                <small class="text-muted" style="font-size: 0.72rem;">Kebersihan, keamanan, fasum, konsumsi</small>
                            </label>
                        </div>
                    </div>
                    @error('tipe')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <!-- 2. TANGGAL & JUMLAH NOMINAL -->
                <div class="row mb-3 g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Tanggal Transaksi <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                        @error('tanggal')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Jumlah Nominal (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold">Rp</span>
                            <input type="number" name="jumlah" id="inputJumlah" class="form-control" value="{{ old('jumlah') }}" required placeholder="Contoh: 150000" min="1" step="1">
                        </div>
                        <div class="form-text small text-muted" id="nominalTerbilang">Format angka murni tanpa titik</div>
                        @error('jumlah')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                <!-- 3. KATEGORI & WILAYAH -->
                <div class="row mb-3 g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Kategori Transaksi <span class="text-danger">*</span></label>
                        <select name="kategori" id="selectKategori" class="form-select" required>
                            <option value="">-- Pilih Kategori --</option>
                        </select>
                        @error('kategori')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Alokasi Wilayah RT/RW</label>
                        @if($isAdminRt)
                            <input type="text" class="form-control bg-light" value="RT {{ $user->rt_number }}" readonly>
                            <input type="hidden" name="rt_number" value="{{ $user->rt_number }}">
                        @else
                            <select name="rt_number" class="form-select">
                                <option value="000" {{ old('rt_number') === '000' ? 'selected' : '' }}>RW 018 (Kas Induk RW)</option>
                                <option value="006" {{ old('rt_number') === '006' ? 'selected' : '' }}>RT 006</option>
                                <option value="007" {{ old('rt_number') === '007' ? 'selected' : '' }}>RT 007</option>
                                <option value="008" {{ old('rt_number') === '008' ? 'selected' : '' }}>RT 008</option>
                                <option value="009" {{ old('rt_number') === '009' ? 'selected' : '' }}>RT 009</option>
                                <option value="010" {{ old('rt_number') === '010' ? 'selected' : '' }}>RT 010</option>
                            </select>
                        @endif
                        @error('rt_number')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                <!-- 4. WARGA TERKAIT (OPSIONAL) -->
                <div class="mb-3" id="groupWarga">
                    <label class="form-label fw-bold small">Warga Pembayar / Penerima (Opsional)</label>
                    <select name="warga_id" class="form-select">
                        <option value="">-- Kas Umum / Non-Warga / Pihak Ketiga --</option>
                        @foreach($warga as $w)
                            <option value="{{ $w->id }}" {{ old('warga_id') == $w->id ? 'selected' : '' }}>
                                {{ $w->nama }} (Blok {{ $w->blok_rumah ?? '-' }} · RT {{ $w->rt_number }})
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text small text-muted">
                        Pilih nama warga jika transaksi berkaitan langsung dengan iuran atau donasi perseorangan.
                    </div>
                    @error('warga_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <!-- 5. SUMBER / TUJUAN DANA & METODE PEMBAYARAN -->
                <div class="row mb-3 g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Sumber / Tujuan Dana</label>
                        <input type="text" name="sumber_tujuan" class="form-control" value="{{ old('sumber_tujuan') }}" placeholder="Contoh: Petugas Kebersihan / Kas Warga Blok A">
                        @error('sumber_tujuan')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Metode Pembayaran</label>
                        <select name="metode_pembayaran" class="form-select">
                            <option value="Tunai" {{ old('metode_pembayaran') === 'Tunai' ? 'selected' : '' }}>Tunai / Cash</option>
                            <option value="Transfer Bank" {{ old('metode_pembayaran') === 'Transfer Bank' ? 'selected' : '' }}>Transfer Bank</option>
                            <option value="QRIS" {{ old('metode_pembayaran') === 'QRIS' ? 'selected' : '' }}>QRIS Lingkungan</option>
                        </select>
                        @error('metode_pembayaran')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                <!-- 6. NOMOR REFERENSI & BUKTI TRANSAKSI -->
                <div class="row mb-3 g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Nomor Referensi (Opsional)</label>
                        <input type="text" name="nomor_referensi" class="form-control" value="{{ old('nomor_referensi') }}" placeholder="Contoh: INV-2026-001 / No. Struk">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Upload Bukti / Struk Transaksi</label>
                        <input type="file" name="bukti_transaksi" class="form-control" accept="image/*,.pdf">
                        <div class="form-text small text-muted">Format: JPG, PNG, atau PDF (maks. 4MB)</div>
                        @error('bukti_transaksi')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                <!-- 7. KETERANGAN / URAIAN -->
                <div class="mb-4">
                    <label class="form-label fw-bold small">Keterangan / Uraian Rincian <span class="text-danger">*</span></label>
                    <textarea name="keterangan" class="form-control" rows="3" required placeholder="Tuliskan keterangan lengkap transaksi kas ini...">{{ old('keterangan') }}</textarea>
                    @error('keterangan')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                    <a href="{{ route('pembayaran.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                    <button type="submit" class="btn btn-primary px-4" id="btnSubmit">
                        <i class="fas fa-check-circle me-1"></i> Simpan Transaksi Kas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const kategoriMasuk = {!! json_encode($kategoriMasuk) !!};
const kategoriKeluar = {!! json_encode($kategoriKeluar) !!};
const oldKategori = "{{ old('kategori', '') }}";

function switchTipe(tipe) {
    const sel = document.getElementById('selectKategori');
    sel.innerHTML = '<option value="">-- Pilih Kategori --</option>';

    const list = tipe === 'masuk' ? kategoriMasuk : kategoriKeluar;
    list.forEach(item => {
        const opt = document.createElement('option');
        opt.value = item;
        opt.textContent = item;
        if (item === oldKategori) opt.selected = true;
        sel.appendChild(opt);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const checkedTipe = document.querySelector('input[name="tipe"]:checked')?.value || 'masuk';
    switchTipe(checkedTipe);
});
</script>
@endpush
