<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Kepatuhan Iuran Warga — NexaNest</title>
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
        .badge { display: inline-block; padding: 3px 8px; border-radius: 999px; font-size: 10px; font-weight: bold; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-danger { background: #fee2e2; color: #991b1b; }

        .summary-wrapper { width: 100%; margin-top: 20px; }
        .summary-table { float: right; width: 45%; border-collapse: collapse; margin-bottom: 20px; }
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
        <h2>Rekapitulasi Kepatuhan Pembayaran Iuran Warga</h2>
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
            <td style="width: 15%;">Wilayah Filter</td>
            <td>: {{ !empty($rtFilter) && $rtFilter !== 'all' ? 'RT ' . $rtFilter : 'Seluruh Wilayah RW 018' }}</td>
            <td style="width: 15%; text-align: right;">Tanggal Cetak:</td>
            <td style="width: 25%;">{{ date('d F Y - H:i') }} WIB</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 4%;">No</th>
                <th style="width: 25%;">Nama Kepala Keluarga</th>
                <th style="width: 15%;">Wilayah &amp; Blok</th>
                <th style="width: 15%;">Status Pembayaran</th>
                <th style="width: 15%;">Tanggal Bayar</th>
                <th style="width: 12%;">Metode</th>
                <th class="text-right" style="width: 14%;">Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $index => $row)
                @php
                    $isLunas = $row['status'] === 'Lunas';
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $row['warga']->nama }}</strong></td>
                    <td>RT {{ $row['warga']->rt_number }} · Blok {{ $row['warga']->blok_rumah ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $isLunas ? 'badge-success' : 'badge-danger' }}">
                            {{ $row['status'] }}
                        </span>
                    </td>
                    <td>{{ $row['tanggal_bayar'] ? \Carbon\Carbon::parse($row['tanggal_bayar'])->format('d/m/Y') : '-' }}</td>
                    <td>{{ $row['metode'] }}</td>
                    <td class="text-right {{ $isLunas ? 'text-success' : 'text-danger' }}">
                        Rp {{ number_format($row['nominal'], 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 15px;">Belum ada data warga terdaftar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary-wrapper">
        <table class="summary-table">
            <tr>
                <td>Total Iuran Terkumpul</td>
                <td class="text-right text-success fw-bold" style="color: #166534;">Rp {{ number_format($totalTerkumpul, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Tunggakan</td>
                <td class="text-right text-danger fw-bold" style="color: #991b1b;">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</td>
            </tr>
            <tr class="total">
                <td>Total Potensi Tagihan</td>
                <td class="text-right" style="color: #285943; font-size: 13px;">Rp {{ number_format($totalTerkumpul + $totalTunggakan, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="clear"></div>

    <div class="ttd-section">
        <div class="ttd-box">
            <p>Jakarta Timur, {{ date('d F Y') }}</p>
            <p>Pengurus Kas Lingkungan,</p>
            <div class="ttd-space"></div>
            <p><strong>( ______________________ )</strong></p>
        </div>
    </div>

</body>
</html>
