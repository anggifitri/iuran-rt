<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Audit &amp; Aktivitas — NexaNest RW 018</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; color: #1e293b; line-height: 1.5; padding: 24px; font-size: 12px; }
        .header { text-align: center; margin-bottom: 22px; border-bottom: 3px double #285943; padding-bottom: 12px; }
        .header h2 { margin: 0; text-transform: uppercase; font-size: 18px; color: #285943; font-weight: 800; letter-spacing: 0.05em; }
        .header p { margin: 4px 0 0 0; color: #64748b; font-size: 12px; }
        .meta-box { width: 100%; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; margin-bottom: 18px; background-color: #f8fafc; }
        .meta-table { width: 100%; border-collapse: collapse; font-size: 11px; }
        .meta-table td { padding: 4px 8px; vertical-align: top; }
        .meta-table td.label { font-weight: bold; color: #475569; width: 18%; }
        
        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .data-table th, .data-table td { border: 1px solid #cbd5e1; padding: 6px 8px; font-size: 10.5px; }
        .data-table th { background-color: #f1f5f9; text-transform: uppercase; color: #334155; font-weight: 700; text-align: left; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-weight: bold; font-size: 9.5px; text-transform: uppercase; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-failed { background: #fee2e2; color: #991b1b; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-info { background: #e0f2fe; color: #075985; }

        .ttd-section { margin-top: 35px; width: 100%; page-break-inside: avoid; }
        .ttd-table { width: 100%; border-collapse: collapse; }
        .ttd-box { width: 33%; text-align: center; font-size: 11px; vertical-align: top; }
        .ttd-space { height: 60px; }

        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 18px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 18px; background: #285943; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; font-size: 12px;">
            🖨️ Cetak Dokumen / Simpan PDF
        </button>
    </div>

    <!-- KOP RESMI LAPORAN RW 018 -->
    <div class="header">
        <h2>Laporan Rekapitulasi Audit &amp; Aktivitas Sistem</h2>
        <p><strong>Rukun Warga 018</strong> · Kelurahan Cipinang Melayu · Kecamatan Makasar · Jakarta Timur</p>
        <p style="font-size: 10.5px; color: #94a3b8;">NexaNest Smart &amp; Green Community · Dokumen Pengawasan Transparansi &amp; Akuntabilitas</p>
    </div>

    <!-- METADATA FILTER & WAKTU CETAK -->
    <div class="meta-box">
        <table class="meta-table">
            <tr>
                <td class="label">Waktu Cetak:</td>
                <td>{{ $printedAt->translatedFormat('l, d F Y - H:i:s') }} WIB</td>
                <td class="label">Pencetak Dokumen:</td>
                <td>{{ $user->name }} ({{ strtoupper($user->role) }})</td>
            </tr>
            <tr>
                <td class="label">Cakupan Wilayah:</td>
                <td>{{ $filterSummary['rt'] }}</td>
                <td class="label">Modul Terpilih:</td>
                <td>{{ $filterSummary['modul'] }}</td>
            </tr>
            <tr>
                <td class="label">Jenis Tindakan:</td>
                <td>{{ $filterSummary['event'] }}</td>
                <td class="label">Periode Waktu:</td>
                <td>{{ $filterSummary['date_from'] }} s.d. {{ $filterSummary['date_to'] }}</td>
            </tr>
            <tr>
                <td class="label">Total Catatan:</td>
                <td colspan="3"><strong>{{ number_format(count($logs)) }} Transaksi Aktivitas</strong> tercatat dalam database sistem</td>
            </tr>
        </table>
    </div>

    <!-- TABEL CATATAN AUDIT -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 3%;" class="text-center">No</th>
                <th style="width: 10%;">ID Log</th>
                <th style="width: 14%;">Waktu (WIB)</th>
                <th style="width: 15%;">Pengguna &amp; Wilayah</th>
                <th style="width: 14%;">Modul &amp; Aksi</th>
                <th style="width: 32%;">Deskripsi Ringkasan Aktivitas</th>
                <th style="width: 12%;">Alamat IP &amp; Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $idx => $log)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td><strong>{{ $log->formatted_id }}</strong></td>
                <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                <td>
                    <strong>{{ $log->user_name ?: 'Sistem' }}</strong><br>
                    <small style="color: #64748b;">
                        {{ strtoupper($log->role ?: 'GUEST') }}
                        @if($log->rt_number && $log->rt_number !== '000') · RT {{ $log->rt_number }} @endif
                    </small>
                </td>
                <td>
                    <span style="font-weight: 600; color: #285943;">{{ $log->module }}</span><br>
                    <span class="badge {{ in_array($log->event, ['create', 'approve', 'verify']) ? 'badge-success' : (in_array($log->event, ['delete', 'reject']) ? 'badge-failed' : 'badge-info') }}">
                        {{ strtoupper($log->event) }}
                    </span>
                </td>
                <td>
                    {{ $log->description }}
                    @if($log->record_id)
                        <br><small style="color: #94a3b8;">ID Objek: #{{ $log->record_id }}</small>
                    @endif
                </td>
                <td>
                    <code>{{ $log->ip_address ?: '-' }}</code><br>
                    <span class="badge {{ $log->status === 'failed' ? 'badge-failed' : ($log->status === 'warning' ? 'badge-warning' : 'badge-success') }}">
                        {{ strtoupper($log->status ?: 'SUCCESS') }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center" style="padding: 20px; color: #64748b;">
                    Tidak ada rekaman aktivitas yang sesuai dengan parameter filter yang ditentukan.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- TANDA TANGAN & PENGESAHAN RESMI -->
    <div class="ttd-section">
        <table class="ttd-table">
            <tr>
                <td class="ttd-box">
                    Mengetahui,<br>
                    <strong>Sekretaris RW 018</strong>
                    <div class="ttd-space"></div>
                    <strong><u>Hendra Pratama</u></strong><br>
                    <small>NIP: 018.02.2024</small>
                </td>
                <td class="ttd-box">
                    Petugas Audit &amp; IT,<br>
                    <strong>Administrator Sistem</strong>
                    <div class="ttd-space"></div>
                    <strong><u>{{ $user->name }}</u></strong><br>
                    <small>{{ strtoupper($user->role) }} RW 018</small>
                </td>
                <td class="ttd-box">
                    Jakarta, {{ $printedAt->translatedFormat('d F Y') }}<br>
                    Menyetujui,<br>
                    <strong>Ketua RW 018</strong>
                    <div class="ttd-space"></div>
                    <strong><u>{{ $ketuaRwName ?? 'Dirga Pradipta' }}</u></strong><br>
                    <small>Ketua RW 018 Masa Bakti 2024-2027</small>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
