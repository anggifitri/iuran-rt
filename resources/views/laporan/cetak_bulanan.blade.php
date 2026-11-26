<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan Bulanan — NexaNest</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; color: #1e293b; line-height: 1.5; padding: 24px; font-size: 13px; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 3px double #285943; padding-bottom: 12px; }
        .header h2 { margin: 0; text-transform: uppercase; font-size: 20px; color: #285943; font-weight: 800; }
        .header p { margin: 5px 0 0 0; color: #64748b; font-size: 13px; }
        .meta-info { width: 100%; margin-bottom: 15px; font-size: 12px; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .data-table th, .data-table td { border: 1px solid #cbd5e1; padding: 7px 10px; font-size: 11px; }
        .data-table th { background-color: #f1f5f9; text-transform: uppercase; color: #334155; font-weight: 700; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-success { color: #16a34a; font-weight: 700; }
        .text-danger { color: #dc2626; font-weight: 700; }

        .summary-wrapper { width: 100%; margin-top: 20px; }
        .summary-table { float: right; width: 48%; border-collapse: collapse; margin-bottom: 20px; }
        .summary-table td { padding: 6px 10px; font-size: 12px; border-bottom: 1px solid #e2e8f0; }
        .summary-table tr.total { font-weight: bold; background-color: #f8fafc; border-top: 2px solid #285943; }
        .clear { clear: both; }

        .ttd-section { margin-top: 40px; width: 100%; }
        .ttd-box { float: right; width: 220px; text-align: center; font-size: 12px; }
        .ttd-space { height: 70px; }

        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 18px; background: #285943; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">
            Cetak Dokumen / Simpan PDF
        </button>
    </div>

    <div class="header">
        <h2>Laporan Pertanggungjawaban Keuangan Kas Bulanan</h2>
        <p>Rukun Warga 018 · Kelurahan Cipinang Melayu · Periode:
            <strong>
            @php
                $namaBulanIndo = [
                    '1'=>'Januari', '2'=>'Februari', '3'=>'Maret', '4'=>'April', '5'=>'Mei', '6'=>'Juni',
                    '7'=>'Juli', '8'=>'Agustus', '9'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember',
                    '01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April', '05'=>'Mei', '06'=>'Juni',
                    '07'=>'Juli', '08'=>'Agustus', '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'
                ];
                echo ($namaBulanIndo[$bulan] ?? $bulan) . ' ' . $tahun;
            @endphp
            </strong>
        </p>
    </div>

    <table class="meta-info">
        <tr>
            <td style="width: 15%;">Wilayah Laporan</td>
            <td>: {{ request('rt_number') && request('rt_number') !== 'all' ? 'RT ' . request('rt_number') : 'Seluruh Wilayah RW 018' }}</td>
            <td style="width: 15%; text-align: right;">Tanggal Cetak:</td>
            <td style="width: 25%;">{{ date('d F Y - H:i') }} WIB</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 4%;">No</th>
                <th style="width: 11%;">Tanggal</th>
                <th style="width: 14%;">No. Transaksi</th>
                <th style="width: 28%;">Uraian / Warga / Sumber</th>
                <th style="width: 15%;">Kategori</th>
                <th class="text-center" style="width: 8%;">Tipe</th>
                <th class="text-right" style="width: 20%;">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr style="background-color: #f8fafc; font-style: italic;">
                <td class="text-center">-</td>
                <td>01-{{ sprintf('%02d', $bulan) }}-{{ $tahun }}</td>
                <td class="text-muted font-monospace">-</td>
                <td><strong>SALDO AWAL (Sisa Kas Bulan Sebelumnya)</strong></td>
                <td>Saldo Kumulatif</td>
                <td class="text-center text-muted">Awal</td>
                <td class="text-right" style="color: #285943; font-weight: bold;">
                    Rp {{ number_format($saldoAwal, 0, ',', '.') }}
                </td>
            </tr>

            @forelse($pembayarans as $index => $p)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $p->tanggal ? $p->tanggal->format('d/m/Y') : '-' }}</td>
                    <td style="font-family: monospace;">{{ $p->nomor_transaksi_formatted }}</td>
                    <td>
                        {{ $p->warga ? $p->warga->nama : ($p->sumber_tujuan ?: 'Kas Umum') }}
                        @if($p->keterangan)
                            <small style="color: #64748b; display: block;">{{ $p->keterangan }}</small>
                        @endif
                    </td>
                    <td>{{ $p->kategori }}</td>
                    <td class="text-center">
                        <span class="{{ $p->tipe === 'masuk' ? 'text-success' : 'text-danger' }}">
                            {{ $p->tipe === 'masuk' ? 'Masuk' : 'Keluar' }}
                        </span>
                    </td>
                    <td class="text-right {{ $p->tipe === 'masuk' ? 'text-success' : 'text-danger' }}">
                        {{ $p->tipe === 'masuk' ? '+' : '-' }} Rp {{ number_format($p->jumlah, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 15px;">Tidak ada pergerakan transaksi pada periode bulan ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary-wrapper">
        <table class="summary-table">
            <tr>
                <td>Saldo Awal Kas</td>
                <td class="text-right">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>(+) Total Pemasukan Bulan Ini</td>
                <td class="text-right text-success">+ Rp {{ number_format($totalMasuk, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>(-) Total Pengeluaran Bulan Ini</td>
                <td class="text-right text-danger">- Rp {{ number_format($totalKeluar, 0, ',', '.') }}</td>
            </tr>
            <tr class="total">
                <td>Saldo Akhir Kas Berjalan</td>
                <td class="text-right" style="color: #285943; font-size: 14px;">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="clear"></div>

    <div class="ttd-section">
        <div class="ttd-box">
            <p>Jakarta Timur, {{ date('d F Y') }}</p>
            <p>Mengetahui dan Mengesahkan,</p>
            <p><strong>Pengurus Lingkungan RW 018</strong></p>
            <div class="ttd-space"></div>
            <p><strong>( Bendahara / Ketua )</strong></p>
        </div>
    </div>

</body>
</html>
