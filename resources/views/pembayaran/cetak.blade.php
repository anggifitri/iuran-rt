<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan NexaNest</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.4;
            padding: 20px;
            background-color: #fff;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px double #333;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 14px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .data-table th, .data-table td {
            border: 1px solid #666;
            padding: 8px 10px;
            text-align: left;
            font-size: 13px;
        }
        .data-table th {
            background-color: #f2f2f2;
            text-transform: uppercase;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .text-success {
            color: #198754;
            font-weight: bold;
        }
        .text-danger {
            color: #dc3545;
            font-weight: bold;
        }
        .summary-box {
            float: right;
            width: 40%;
            border: 1px solid #666;
            padding: 10px;
            background-color: #fafafa;
            margin-bottom: 30px;
        }
        .summary-box table {
            width: 100%;
            font-size: 14px;
        }
        .summary-box td {
            padding: 4px 0;
        }
        .clear {
            clear: both;
        }
        .ttd-section {
            margin-top: 50px;
            width: 100%;
        }
        .ttd-box {
            float: right;
            width: 250px;
            text-align: center;
        }
        .ttd-space {
            height: 80px;
        }

        /* CSS khusus saat dicetak */
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 15px; background: #0d6efd; color: white; border: none; border-radius: 5px; cursor: pointer;">
            Print / Simpan PDF
        </button>
    </div>

    <div class="header">
        <h2>Laporan Keuangan NexaNest</h2>
        <p>Rekapitulasi Transaksi Pemasukan dan Pengeluaran Kas Warga</p>
    </div>

    <table class="info-table">
        <tr>
            <td style="width: 15%;">Tanggal Cetak</td>
            <td>: {{ date('d F Y') }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 5%;">No</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 25%;">Keterangan / Warga</th>
                <th style="width: 20%;">Kategori</th>
                <th class="text-center" style="width: 15%;">Tipe</th>
                <th class="text-right" style="width: 20%;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalMasuk = 0;
                $totalKeluar = 0;
            @endphp
            @forelse($pembayarans as $index => $p)
                @php
                    if($p->tipe == 'masuk') {
                        $totalMasuk += $p->jumlah;
                    } else {
                        $totalKeluar += $p->jumlah;
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ date('d M Y', strtotime($p->tanggal)) }}</td>
                    <td>
                        {{ $p->warga ? $p->warga->nama : 'Umum / Non-Warga' }}
                        @if($p->keterangan)
                            <br><small style="color: #666; font-style: italic;">({{ $p->keterangan }})</small>
                        @endif
                    </td>
                    <td>{{ $p->kategori }}</td>
                    <td class="text-center">
                        <span class="{{ $p->tipe == 'masuk' ? 'text-success' : 'text-danger' }}">
                            {{ $p->tipe == 'masuk' ? 'Masuk' : 'Keluar' }}
                        </span>
                    </td>
                    <td class="text-right">
                        {{ $p->tipe == 'masuk' ? '+' : '-' }} Rp {{ number_format($p->jumlah, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Belum ada data transaksi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary-box">
        <table>
            <tr>
                <td>Total Pemasukan</td>
                <td class="text-right text-success">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Pengeluaran</td>
                <td class="text-right text-danger">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</td>
            </tr>
            <tr style="border-top: 1px solid #333; font-weight: bold;">
                <td style="padding-top: 8px;">Sisa Saldo Kas</td>
                <td class="padding-top: 8px; text-right" style="color: #0d6efd;">
                    Rp {{ number_format($totalMasuk - $totalKeluar, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>

    <div class="clear"></div>

    <div class="ttd-section">
        <div class="ttd-box">
            <p>Bekasi, {{ date('d F Y') }}</p>
            <p>Mengetahui,</p>
            <p><strong>Ketua RT</strong></p>
            <div class="ttd-space"></div>
            <p>_______________________</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>

</body>
</html>
