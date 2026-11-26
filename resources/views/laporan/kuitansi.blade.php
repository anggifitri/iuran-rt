<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuitansi Pembayaran Iuran #{{ $payment->id }} — NexaNest</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #1e293b;
        }
        .receipt-card {
            max-width: 680px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }
        .receipt-header {
            background: linear-gradient(135deg, #285943 0%, #1e4533 100%);
            color: #ffffff;
            padding: 24px 32px;
        }
        .stamp-lunas {
            display: inline-block;
            border: 2px solid #16a34a;
            color: #16a34a;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
            padding: 6px 16px;
            border-radius: 6px;
            transform: rotate(-8deg);
        }
        @media print {
            body { background: #ffffff; }
            .receipt-card { box-shadow: none; border: 1px solid #000000; margin: 0; max-width: 100%; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

<div class="receipt-card">
    <div class="receipt-header d-flex justify-content-between align-items-center">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span style="font-size: 1.25rem; font-weight: 800; letter-spacing: -0.5px;">NexaNest</span>
                <span class="badge bg-white text-dark px-2 py-0.5" style="font-size: 0.7rem; font-weight: 700;">RW 018</span>
            </div>
            <p class="mb-0 small" style="opacity: 0.9;">Rukun Warga 018 · Kelurahan Cipinang Melayu</p>
        </div>
        <div class="text-end">
            <span class="badge bg-white bg-opacity-25 px-3 py-1 font-monospace" style="font-size: 0.82rem;">
                No. KUITANSI-{{ sprintf('%05d', $payment->id) }}
            </span>
            <div class="small mt-1" style="opacity: 0.85;">Tanggal: {{ $payment->payment_date ? $payment->payment_date->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}</div>
        </div>
    </div>

    <div class="p-4">
        <div class="text-center my-2">
            <h4 class="fw-bold mb-1" style="color: #285943;">KUITANSI PEMBAYARAN IURAN RESMI</h4>
            <span class="text-muted small">Tanda bukti pembayaran iuran dan kontribusi warga lingkungan</span>
        </div>

        <div class="my-4 p-3 rounded-3" style="background: #f8fafc; border: 1px dashed #cbd5e1;">
            <div class="row g-2 small">
                <div class="col-4 text-muted">Telah Diterima Dari:</div>
                <div class="col-8 fw-bold fs-6">{{ $payment->warga->nama ?? ($payment->user->name ?? 'Warga RT ' . $payment->rt_number) }}</div>

                <div class="col-4 text-muted">Alamat / Wilayah:</div>
                <div class="col-8">RT {{ $payment->rt_number }} · Blok {{ $payment->warga->blok_rumah ?? '-' }} · RW 018</div>

                <div class="col-4 text-muted">Untuk Pembayaran:</div>
                <div class="col-8 fw-semibold text-primary">
                    {{ $payment->iuran->name ?? 'Iuran Lingkungan Terpadu' }} — Periode {{ sprintf('%02d/%d', $payment->bulan, $payment->tahun) }}
                </div>

                <div class="col-4 text-muted">Metode Pembayaran:</div>
                <div class="col-8">{{ $payment->payment_method ?: 'Transfer' }}</div>

                @if($payment->notes)
                    <div class="col-4 text-muted">Keterangan:</div>
                    <div class="col-8 text-muted fst-italic">{{ $payment->notes }}</div>
                @endif
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center p-3 rounded-3 mb-4" style="background: #ecfdf5; border: 1px solid #a7f3d0;">
            <div>
                <span class="text-muted small d-block">Jumlah Pembayaran Diterima:</span>
                <h3 class="fw-bold mb-0 text-success">
                    Rp {{ number_format($payment->amount_paid, 0, ',', '.') }}
                </h3>
            </div>
            <div>
                <div class="stamp-lunas">
                    <i class="fas fa-check-circle me-1"></i> LUNAS
                </div>
            </div>
        </div>

        <div class="row mt-4 pt-3 border-top text-center" style="font-size: 0.85rem;">
            <div class="col-6">
                <span class="text-muted d-block mb-4">Warga Pembayar,</span>
                <strong class="d-block mt-3">{{ $payment->warga->nama ?? ($payment->user->name ?? 'Warga') }}</strong>
                <small class="text-muted">Keluarga RT {{ $payment->rt_number }}</small>
            </div>
            <div class="col-6">
                <span class="text-muted d-block mb-4">Pengurus RT / RW 018,</span>
                <strong class="d-block mt-3">{{ $payment->verifikator->name ?? 'Pengurus Lingkungan' }}</strong>
                <small class="text-muted">Penerima Kas Resmi</small>
            </div>
        </div>
    </div>

    <div class="p-3 bg-light border-top text-center no-print d-flex justify-content-between align-items-center">
        <a href="javascript:window.close()" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Tutup
        </a>
        <button type="button" class="btn btn-sm btn-primary px-4" onclick="window.print()">
            <i class="fas fa-print me-1"></i> Cetak Kuitansi
        </button>
    </div>
</div>

</body>
</html>
