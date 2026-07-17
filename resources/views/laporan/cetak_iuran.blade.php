<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Iuran Warga</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.5; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 3px double #000; padding-bottom: 10px; }
        .header h2 { margin: 0; text-transform: uppercase; font-size: 20px; }
        .header p { margin: 5px 0 0 0; color: #555; font-size: 14px; }
        .meta { margin-bottom: 15px; font-size: 13px; }
        .badge { display: inline-block; padding: 5px 10px; border-radius: 999px; font-size: 12px; color: #fff; }
        .badge-success { background: #198754; }
        .badge-danger { background: #dc3545; }
        .badge-warning { background: #ffc107; color: #212529; }
        .badge-secondary { background: #6c757d; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .data-table th, .data-table td { border: 1px solid #444; padding: 8px 10px; font-size: 12px; }
        .data-table th { background-color: #f5f5f5; text-transform: uppercase; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .no-print { margin-bottom: 20px; text-align: right; }
        @media print { .no-print { display: none; } body { padding: 0; } }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" style="padding: 10px 18px; background: #198754; color: white; border: none; border-radius: 5px; cursor: pointer;">
            <i class="fas fa-print"></i> Simpan Sebagai PDF
        </button>
    </div>

    <div class="header">
        <h2>Laporan Iuran Warga</h2>
        <p>Periode: {{ \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->format('F Y') }}</p>
    </div>

    <div class="meta">
        <div><strong>Tanggal Cetak:</strong> {{ date('d F Y - H:i') }} WIB</div>
        <div><strong>Filter Warga:</strong> {{ $wargaFilter ? ($rows[0]['warga']->nama ?? 'Tidak ditemukan') : 'Semua Warga' }}</div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 5%;">No</th>
                <th style="width: 25%;">Nama Warga</th>
                <th style="width: 15%;">Status</th>
                <th style="width: 15%;">Tanggal Bayar</th>
                <th style="width: 25%;">Riwayat 3 Bulan</th>
                <th style="width: 15%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $row['warga']->nama }}</td>
                    <td>{{ $row['status'] }}</td>
                    <td>{{ $row['tanggal_bayar'] ? \Carbon\Carbon::parse($row['tanggal_bayar'])->format('d-m-Y') : '-' }}</td>
                    <td>
                        @foreach($row['history'] as $h)
                            @if($h['paid'])
                                <span class="badge badge-success">{{ $h['month'] }}</span>
                            @else
                                <span class="badge badge-secondary">{{ $h['month'] }}</span>
                            @endif
                        @endforeach
                    </td>
                    <td>
                        @if($row['keterangan'] == 'Rajin')
                            <span class="badge badge-success">Rajin</span>
                        @elseif($row['keterangan'] == 'Tidak Pernah Bayar')
                            <span class="badge badge-danger">Tidak Pernah Bayar</span>
                        @else
                            <span class="badge badge-warning">Kurang Bayar</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Belum ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
