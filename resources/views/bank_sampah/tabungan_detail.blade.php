@extends('layouts.app')

@section('title', 'Buku Tabungan Nasabah Bank Sampah — NexaNest')

@section('content')
<div class="nx-page-header">
    <div>
        <a href="{{ route('bank_sampah.index', ['tab' => 'setoran']) }}" class="text-decoration-none small text-muted d-inline-flex align-items-center mb-2">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Data Setoran
        </a>
        <h1 class="nx-page-title">
            <i class="fas fa-book-bookmark text-success me-2"></i>Buku Rekening Tabungan Sampah
        </h1>
        <p class="nx-page-subtitle">
            Buku tabungan digital nasabah pemilahan sampah: {{ $warga->nama }} · RT {{ $warga->rt_number }}/RW 018
        </p>
    </div>
    <div class="d-flex gap-2">
        @if(Auth::user()->isAdminRw() || Auth::user()->isAdminRt())
            <button type="button" class="btn btn-outline-secondary fw-bold" data-bs-toggle="modal" data-bs-target="#modalPenarikanWarga" style="border-radius: 10px;">
                <i class="fas fa-hand-holding-dollar me-1"></i> Cairkan Saldo
            </button>
            <button type="button" class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#modalCatatSetoranWarga" style="border-radius: 10px; background: var(--nx-forest); border-color: var(--nx-forest);">
                <i class="fas fa-plus me-1"></i> Catat Setoran
            </button>
        @endif
    </div>
</div>

<!-- INFO PROFIL NASABAH & RINGKASAN SALDO -->
<div class="row g-3 mb-4">
    <div class="col-lg-4 col-md-12">
        <div class="card p-4 h-100 shadow-xs border-0" style="border-radius: 14px; background: linear-gradient(145deg, #ffffff, #f7faf8); border: 1px solid #dcebdd !important;">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-xs" style="width: 52px; height: 52px; background: var(--nx-forest); font-size: 1.25rem;">
                    {{ strtoupper(substr($warga->nama, 0, 2)) }}
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">{{ $warga->nama }}</h5>
                    <span class="badge bg-light text-dark border px-2 py-0.5" style="font-size: 0.72rem;">
                        <i class="fas fa-id-card me-1 text-muted"></i> NIK: {{ $warga->nik ?: '-' }}
                    </span>
                </div>
            </div>
            <div class="small border-top pt-2.5" style="font-size: 0.8rem; line-height: 1.8;">
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Wilayah RT:</span>
                    <strong class="text-dark">RT {{ $warga->rt_number }} / RW 018</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Alamat / Blok:</span>
                    <strong class="text-dark">{{ $warga->blok_rumah ?: 'Blok -' }} No. {{ $warga->no_rumah ?: '-' }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Status Kependudukan:</span>
                    <span class="badge bg-success-subtle text-success px-2 py-0.5 fw-bold">{{ ucfirst($warga->status_warga ?: 'tetap') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- KARTU SALDO AKTIF -->
    <div class="col-lg-4 col-md-6">
        <div class="card p-4 h-100 shadow-xs text-white border-0" style="border-radius: 14px; background: linear-gradient(135deg, #285943 0%, #1e4533 100%);">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-uppercase small fw-bold" style="letter-spacing: 0.05em; opacity: 0.85;">Saldo Tabungan Aktif</span>
                <i class="fas fa-wallet fa-lg" style="opacity: 0.75;"></i>
            </div>
            <h2 class="fw-bold mb-2" style="font-size: 1.85rem;">Rp {{ number_format($saldo, 0, ',', '.') }}</h2>
            <div class="small" style="opacity: 0.85; font-size: 0.75rem;">
                <i class="fas fa-shield-halved me-1"></i> Dapat dicairkan tunai atau dialihkan untuk pembayaran iuran warga.
            </div>
        </div>
    </div>

    <!-- KARTU AKUMULASI TIMBANGAN -->
    <div class="col-lg-4 col-md-6">
        <div class="card p-4 h-100 shadow-xs border-0" style="border-radius: 14px; background: #ffffff; border: 1px solid #e2e8f0 !important;">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-muted small fw-bold text-uppercase">Statistik Sampah Terkumpul</span>
                <i class="fas fa-weight-hanging text-success fa-lg"></i>
            </div>
            <h2 class="fw-bold text-dark mb-1" style="font-size: 1.85rem;">
                {{ number_format($totalKg, 1, ',', '.') }} <span class="fs-6 fw-normal text-muted">Kg</span>
            </h2>
            <div class="small text-muted d-flex justify-content-between pt-2 border-top mt-2" style="font-size: 0.76rem;">
                <span>Total Setoran Bruto: <strong>Rp {{ number_format($totalSetoran, 0, ',', '.') }}</strong></span>
                <span>Telah Dicairkan: <strong class="text-danger">Rp {{ number_format($totalPenarikan, 0, ',', '.') }}</strong></span>
            </div>
        </div>
    </div>
</div>

<!-- TABS RIWAYAT: SETORAN VS PENARIKAN -->
<div class="card shadow-xs overflow-hidden border-0" style="border-radius: 14px; border: 1px solid #e2e8f0 !important;">
    <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center">
        <ul class="nav nav-tabs card-header-tabs" id="tabBukuWarga" role="tablist">
            <li class="nav-item">
                <button class="nav-link active fw-bold text-dark" id="tab-setoran-link" data-bs-toggle="tab" data-bs-target="#tab-setoran" type="button" role="tab">
                    <i class="fas fa-arrow-down text-success me-1.5"></i> Riwayat Setoran Sampah ({{ $setorans->count() }})
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-bold text-dark" id="tab-penarikan-link" data-bs-toggle="tab" data-bs-target="#tab-penarikan" type="button" role="tab">
                    <i class="fas fa-arrow-up text-danger me-1.5"></i> Riwayat Pencairan Tabungan ({{ $penarikans->count() }})
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body p-0">
        <div class="tab-content">
            <!-- TABEL SETORAN -->
            <div class="tab-pane fade show active" id="tab-setoran" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.84rem;">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">No. Transaksi</th>
                                <th>Tanggal</th>
                                <th>Kategori</th>
                                <th>Berat</th>
                                <th>Harga Satuan</th>
                                <th>Nilai Setor</th>
                                <th>Status</th>
                                <th>Petugas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($setorans as $s)
                                <tr>
                                    <td class="ps-3 fw-bold text-dark">{{ $s->nomor_transaksi }}</td>
                                    <td>{{ \Carbon\Carbon::parse($s->tanggal_setor)->translatedFormat('d M Y') }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark border px-2 py-0.5">
                                            {{ ucfirst($s->kategori) }}
                                        </span>
                                    </td>
                                    <td class="fw-bold">{{ number_format($s->berat_kg, 1, ',', '.') }} {{ $s->satuan ?: 'Kg' }}</td>
                                    <td>Rp {{ number_format($s->harga_per_satuan, 0, ',', '.') }}</td>
                                    <td class="fw-bold {{ $s->status === 'diterima' ? 'text-success' : 'text-muted' }}">
                                        Rp {{ number_format($s->nilai_rupiah, 0, ',', '.') }}
                                    </td>
                                    <td>
                                        @if($s->status === 'diterima')
                                            <span class="badge bg-success-subtle text-success px-2 py-0.5 fw-bold">Diterima</span>
                                        @elseif($s->status === 'menunggu')
                                            <span class="badge bg-warning-subtle text-warning px-2 py-0.5 fw-bold">Menunggu</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger px-2 py-0.5 fw-bold">Ditolak</span>
                                        @endif
                                    </td>
                                    <td><small class="text-muted">{{ $s->petugas ?: '-' }}</small></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">Belum ada riwayat penyetoran sampah untuk nasabah ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TABEL PENARIKAN -->
            <div class="tab-pane fade" id="tab-penarikan" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.84rem;">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">No. Penarikan</th>
                                <th>Tanggal Pencairan</th>
                                <th>Jumlah Dicairkan</th>
                                <th>Metode</th>
                                <th>Status</th>
                                <th>Petugas</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($penarikans as $p)
                                <tr>
                                    <td class="ps-3 fw-bold text-dark">{{ $p->nomor_penarikan }}</td>
                                    <td>{{ \Carbon\Carbon::parse($p->tanggal_penarikan)->translatedFormat('d M Y') }}</td>
                                    <td class="fw-bold text-danger">Rp {{ number_format($p->jumlah_penarikan, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark border px-2 py-0.5">
                                            {{ ucfirst(str_replace('_', ' ', $p->metode_penarikan)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success-subtle text-success px-2 py-0.5 fw-bold">Selesai</span>
                                    </td>
                                    <td><small class="text-muted">{{ $p->petugas ?: '-' }}</small></td>
                                    <td><small class="text-muted">{{ $p->catatan ?: '-' }}</small></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Belum pernah melakukan pencairan saldo tabungan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PENARIKAN CEPAT DARI HALAMAN BUKU TABUNGAN -->
<div class="modal fade" id="modalPenarikanWarga" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('bank_sampah.store_penarikan') }}" method="POST">
                @csrf
                <input type="hidden" name="warga_id" value="{{ $warga->id }}">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="font-size: 1rem; color: var(--nx-forest);">
                        <i class="fas fa-hand-holding-dollar me-2"></i>Pencairan Saldo: {{ $warga->nama }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="p-2.5 rounded-2 bg-light mb-3 small d-flex justify-content-between align-items-center">
                        <span class="text-muted">Saldo Tersedia:</span>
                        <strong class="text-success fs-6">Rp {{ number_format($saldo, 0, ',', '.') }}</strong>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nominal Penarikan (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="jumlah_penarikan" class="form-control" max="{{ $saldo }}" min="1000" placeholder="Maks: {{ $saldo }}" required>
                        <small class="text-muted" style="font-size: 0.72rem;">Jumlah penarikan tidak boleh melebihi saldo aktif saat ini.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Metode Pencairan <span class="text-danger">*</span></label>
                        <select name="metode_penarikan" class="form-select" required>
                            <option value="tunai">Uang Tunai</option>
                            <option value="transfer">Transfer Bank / E-Wallet</option>
                            <option value="potong_iuran">Alokasi Pembayaran Iuran Warga</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Tanggal Pencairan <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_penarikan" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold">Catatan (Opsional)</label>
                        <input type="text" name="catatan" class="form-control" placeholder="Contoh: Penarikan untuk belanja RT...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold" {{ $saldo <= 0 ? 'disabled' : '' }}>
                        Proses Pencairan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
