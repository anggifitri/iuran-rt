<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Bulanan Kas RT</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.5; padding: 20px; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 3px double #000; padding-bottom: 10px; }
        .header h2 { margin: 0; text-transform: uppercase; font-size: 20px; }
        .header p { margin: 5px 0 0 0; color: #555; font-size: 14px; }
        .meta-info { width: 100%; margin-bottom: 15px; font-size: 13px; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .data-table th, .data-table td { border: 1px solid #444; padding: 7px 10px; font-size: 12px; }
        .data-table th { background-color: #f5f5f5; text-transform: uppercase; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-success { color: #198754; font-weight: bold; }
        .text-danger { color: #dc3545; font-weight: bold; }

        .summary-wrapper { width: 100%; margin-top: 20px; }
        .summary-table { float: right; width: 45%; border-collapse: collapse; margin-bottom: 20px; }
        .summary-table td { padding: 5px; font-size: 13px; border-bottom: 1px solid #ddd; }
        .summary-table tr.total { font-weight: bold; background-color: #f9f9f9; border-top: 1px solid #000; }
        .clear { clear: both; }

        .ttd-section { margin-top: 40px; width: 100%; }
        .ttd-box { float: right; width: 200px; text-align: center; font-size: 13px; }
        .ttd-space { height: 70px; }

        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 15px; background: #198754; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">
            <i class="fas fa-print"></i> Simpan Sebagai PDF
        </button>
    </div>

    <div class="header">
        <h2>Laporan Keuangan Kas Bulanan RT</h2>
        <p>Rekapitulasi Spesifik Bulanan Periode:
            <strong>
            @php
                $namaBulanIndo = [
                    '01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April', '05'=>'Mei', '06'=>'Juni',
                    '07'=>'Juli', '08'=>'Agustus', '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'
                ];
                echo $namaBulanIndo[$bulan] . ' ' . $tahun;
            @endphp
            </strong>
        </p>
    </div>

    <table class="meta-info">
        <tr>
            <td style="width: 15%;">Tanggal Cetak</td>
            <td>: {{ date('d F Y - H:i') }} WIB</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 5%;">No</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 35%;">Keterangan / Warga</th>
                <th style="width: 20%;">Kategori</th>
                <th class="text-center" style="width: 10%;">Tipe</th>
                <th class="text-right" style="width: 15%;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr style="background-color: #fafafa; font-style: italic;">
                <td class="text-center">-</td>
                <td>01-{{ $bulan }}-{{ $tahun }}</td>
                <td><strong>SALDO AWAL (Sisa Kas Bulan Sebelumnya)</strong></td>
                <td>-</td>
                <td class="text-center">Sisa</td>
                <td class="text-right" style="color: #0d6efd; font-weight: bold;">
                    Rp {{ number_format($saldoAwal, 0, ',', '.') }}
                </td>
            </tr>

            @forelse($pembayarans as $index => $p)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ date('d M Y', strtotime($p->tanggal)) }}</td>
                    <td>
                        {{ $p->warga ? $p->warga->nama : 'Umum / Non-Warga' }}
                        @if($p->keterangan)
                            <small style="color: #555;"><br>({{ $p->keterangan }})</small>
                        @endif
                    </td>
                    <td>{{ $p->kategori }}</td>
                    <td class="text-center">
                        <span class="{{ $p->tipe == 'masuk' ? 'text-success' : 'text-danger' }}">
                            {{ $p->tipe == 'masuk' ? 'Masuk' : 'Keluar' }}
                        </span>
                    </td>
                    <td class="text-right {{ $p->tipe == 'masuk' ? 'text-success' : 'text-danger' }}">
                        {{ $p->tipe == 'masuk' ? '+' : '-' }} Rp {{ number_format($p->jumlah, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 15px;">Tidak ada pergerakan transaksi pada bulan ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary-wrapper">
        <table class="summary-table">
            <tr>
                <td>Saldo Awal Bulan</td>
                <td class="text-right">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>(+) Total Pemasukan Bulan Ini</td>
                <td class="text-right text-success">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>(-) Total Pengeluaran Bulan Ini</td>
                <td class="text-right text-danger">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</td>
            </tr>
            <tr class="total">
                <td>Saldo Akhir (Sisa Kas Saat Ini)</td>
                <td class="text-right" style="color: #0d6efd;">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td>
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
