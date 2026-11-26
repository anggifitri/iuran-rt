<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Resmi Bank Sampah — NexaNest</title>
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
        <h2>Laporan Rekapitulasi Program Bank Sampah Digital</h2>
        <p>Rukun Warga 018 · Kelurahan Cipinang Melayu · Periode:
            <strong>
            @php
                $namaBulanIndo = [
                    '1'=>'Januari', '2'=>'Februari', '3'=>'Maret', '4'=>'April', '5'=>'Mei', '6'=>'Juni',
                    '7'=>'Juli', '8'=>'Agustus', '9'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember',
                    '01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April', '05'=>'Mei', '06'=>'Juni',
                    '07'=>'Juli', '08'=>'Agustus', '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'
                ];
                $blnLabel = $bulan ? ($namaBulanIndo[$bulan] ?? $bulan) . ' ' : 'Tahun ';
                echo $blnLabel . $tahun;
            @endphp
            </strong>
        </p>
    </div>

    <table class="meta-info">
        <tr>
            <td style="width: 15%;">Wilayah Laporan</td>
            <td>: {{ $rtFilter ? 'RT ' . $rtFilter : 'Seluruh Wilayah RW 018' }}</td>
            <td style="width: 15%; text-align: right;">Tanggal Cetak:</td>
            <td style="width: 25%;">{{ date('d F Y - H:i') }} WIB</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th>No. Transaksi</th>
                <th>Tanggal Setor</th>
                <th>Nama Nasabah</th>
                <th class="text-center">RT</th>
                <th>Kategori</th>
                <th class="text-right">Berat / Volume</th>
                <th class="text-right">Harga Satuan</th>
                <th class="text-right">Nilai Rupiah</th>
                <th>Petugas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $idx => $r)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td><strong>{{ $r->nomor_transaksi }}</strong></td>
                    <td>{{ \Carbon\Carbon::parse($r->tanggal_setor)->translatedFormat('d/m/Y') }}</td>
                    <td>{{ $r->nama_penyetor }}</td>
                    <td class="text-center">RT {{ $r->rt_number }}</td>
                    <td>{{ ucfirst($r->kategori) }}</td>
                    <td class="text-right">{{ number_format($r->berat_kg, 1, ',', '.') }} {{ $r->satuan ?: 'Kg' }}</td>
                    <td class="text-right">Rp {{ number_format($r->harga_per_satuan, 0, ',', '.') }}</td>
                    <td class="text-right text-success">Rp {{ number_format($r->nilai_rupiah, 0, ',', '.') }}</td>
                    <td>{{ $r->petugas ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding: 20px; color: #64748b;">
                        Tidak ada data setoran bank sampah yang tercatat pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary-wrapper">
        <table class="summary-table">
            <tr>
                <td>Total Transaksi Penimbangan</td>
                <td class="text-right"><strong>{{ number_format($totalTx, 0, ',', '.') }} Transaksi</strong></td>
            </tr>
            <tr>
                <td>Total Volume Sampah Terkumpul</td>
                <td class="text-right"><strong>{{ number_format($totalKg, 1, ',', '.') }} Kg</strong></td>
            </tr>
            <tr class="total">
                <td>Total Nilai Ekonomi Sampah</td>
                <td class="text-right text-success">Rp {{ number_format($totalRp, 0, ',', '.') }}</td>
            </tr>
        </table>
        <div class="clear"></div>
    </div>

    <div class="ttd-section">
        <div class="ttd-box">
            Jakarta, {{ date('d F Y') }}<br>
            Pengurus Bank Sampah RW 018
            <div class="ttd-space"></div>
            <strong><u>{{ Auth::user()->name }}</u></strong><br>
            {{ Auth::user()->isAdminRw() ? 'Admin Pengurus RW' : 'Admin RT ' . Auth::user()->rt_number }}
        </div>
        <div class="clear"></div>
    </div>

</body>
</html>
