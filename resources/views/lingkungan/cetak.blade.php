<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Lingkungan RT {{ $laporan->rt_number }} - {{ $laporan->nama_bulan }} {{ $laporan->periode_tahun }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background: #fff;
            font-size: 11pt;
            line-height: 1.4;
        }
        .header-kop {
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }
        .table-data th {
            background-color: #f2f2f2 !important;
            -webkit-print-color-adjust: exact;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
                margin: 0;
            }
        }
    </style>
</head>
<body class="p-4">
    <div class="no-print mb-3 text-end">
        <button onclick="window.print()" class="btn btn-primary btn-sm">
            Cetak / Simpan PDF
        </button>
        <button onclick="window.close()" class="btn btn-secondary btn-sm">
            Tutup
        </button>
    </div>

    <!-- KOP SURAT RW -->
    <div class="text-center header-kop">
        <h5 class="mb-0 fw-bold">RUKUN WARGA 018</h5>
        <h4 class="mb-0 fw-bold">KELURAHAN SUKAMAJU — KECAMATAN CILODONG</h4>
        <small class="text-muted">Sekretariat RW 018, Balai Warga, Jl. Melati Raya No. 01 · Kota Depok</small>
    </div>

    <div class="text-center mb-4">
        <h5 class="fw-bold mb-1 text-uppercase text-decoration-underline">LAPORAN EVALUASI &amp; KONDISI LINGKUNGAN BULANAN</h5>
        <p class="mb-0">Nomor: {{ $laporan->id }}/EVAL-LING/RT{{ $laporan->rt_number }}/{{ $laporan->periode_bulan }}/{{ $laporan->periode_tahun }}</p>
    </div>

    <!-- DATA IDENTITAS -->
    <table class="table table-sm table-bordered mb-3">
        <tr>
            <th style="width: 25%;">Wilayah RT</th>
            <td style="width: 25%;">RT {{ $laporan->rt_number }} / RW 018</td>
            <th style="width: 25%;">Periode Pelaporan</th>
            <td style="width: 25%;">{{ $laporan->nama_bulan }} {{ $laporan->periode_tahun }}</td>
        </tr>
        <tr>
            <th>Pengurus / Admin RT</th>
            <td>{{ $laporan->user ? $laporan->user->name : '-' }}</td>
            <th>Status Pengesahan RW</th>
            <td><strong>{{ strtoupper($laporan->status_label) }}</strong> (v{{ $laporan->versi }})</td>
        </tr>
        <tr>
            <th>Tanggal Penyerahan</th>
            <td>{{ $laporan->tanggal_pengiriman ? $laporan->tanggal_pengiriman->translatedFormat('d F Y') : '-' }}</td>
            <th>Tanggal Pemeriksaan RW</th>
            <td>{{ $laporan->tanggal_pemeriksaan ? $laporan->tanggal_pemeriksaan->translatedFormat('d F Y') : '-' }}</td>
        </tr>
    </table>

    <!-- SEKTOR 1: KEBERSIHAN -->
    <h6 class="fw-bold mb-1">1. SEKTOR KEBERSIHAN LINGKUNGAN</h6>
    <table class="table table-sm table-bordered mb-3">
        <tr>
            <th style="width: 35%;">Status Umum Kebersihan</th>
            <td><strong>{{ $laporan->kondisi_kebersihan }}</strong></td>
        </tr>
        <tr>
            <th>Area Terpelihara Baik</th>
            <td>{{ $laporan->area_terjaga_kebersihan ?: '-' }}</td>
        </tr>
        <tr>
            <th>Area Butuh Penanganan</th>
            <td>{{ $laporan->area_perlu_perhatian_kebersihan ?: '-' }}</td>
        </tr>
        <tr>
            <th>Pengangkutan &amp; Masalah Sampah</th>
            <td>{{ $laporan->masalah_sampah ?: '-' }}</td>
        </tr>
    </table>

    <!-- SEKTOR 2: DRAINASE -->
    <h6 class="fw-bold mb-1">2. SEKTOR SALURAN DRAINASE &amp; ALIRAN AIR</h6>
    <table class="table table-sm table-bordered mb-3">
        <tr>
            <th style="width: 35%;">Kondisi Fisik Saluran</th>
            <td><strong>{{ $laporan->kondisi_drainase }}</strong></td>
        </tr>
        <tr>
            <th>Kelancaran Aliran Air</th>
            <td>{{ $laporan->kondisi_aliran_air ?: 'Lancar Mengalir' }}</td>
        </tr>
        <tr>
            <th>Titik Rawan Genangan</th>
            <td>{{ $laporan->titik_rawan_genangan ?: 'Nihil' }}</td>
        </tr>
        <tr>
            <th>Kebutuhan Pengerukan Sedimen</th>
            <td>{{ $laporan->saluran_butuh_pembersihan ?: '-' }}</td>
        </tr>
    </table>

    <!-- SEKTOR 3: KEGIATAN & PENGHIJAUAN -->
    <h6 class="fw-bold mb-1">3. KEGIATAN GOTONG ROYONG &amp; PENGHIJAUAN</h6>
    <table class="table table-sm table-bordered mb-3">
        <tr>
            <th style="width: 35%;">Kerja Bakti Terlaksana</th>
            <td>{{ $laporan->kegiatan_kerja_bakti ?: '-' }}</td>
        </tr>
        <tr>
            <th>Tanggal Pelaksanaan &amp; Peserta</th>
            <td>
                {{ $laporan->tanggal_pelaksanaan_kegiatan ? $laporan->tanggal_pelaksanaan_kegiatan->translatedFormat('d F Y') : '-' }}
                ({{ $laporan->jumlah_peserta_kegiatan }} Warga Berpartisipasi)
            </td>
        </tr>
        <tr>
            <th>Kondisi Penghijauan &amp; RTH</th>
            <td>{{ $laporan->kondisi_penghijauan ?: '-' }} · {{ $laporan->ketersediaan_ruang_terbuka ?: '-' }}</td>
        </tr>
    </table>

    <!-- SEKTOR 4: EVALUASI RW -->
    <h6 class="fw-bold mb-1">4. CATATAN EVALUASI &amp; PERSETUJUAN KETUA RW</h6>
    <div class="border p-2.5 mb-4" style="background: #fcfcfc;">
        <p class="mb-0 font-italic">
            "{{ $laporan->catatan_evaluasi_rw ?: $laporan->catatan_rw ?: 'Laporan telah diperiksa dan disetujui sesuai dengan kondisi lapangan yang berlaku.' }}"
        </p>
    </div>

    <!-- TANDA TANGAN PENGESAHAN -->
    <div class="row text-center mt-5">
        <div class="col-6">
            <p class="mb-5">Pengurus RT {{ $laporan->rt_number }},</p>
            <strong class="text-decoration-underline">{{ $laporan->user ? $laporan->user->name : 'Ketua RT ' . $laporan->rt_number }}</strong>
            <p class="mb-0 small text-muted">Admin / Pengurus Lingkungan</p>
        </div>
        <div class="col-6">
            <p class="mb-5">Ketua RW 018 Sukamaju,</p>
            <strong class="text-decoration-underline">{{ $laporan->pemeriksa ? $laporan->pemeriksa->name : 'Dirga Pradipta' }}</strong>
            <p class="mb-0 small text-muted">Ketua Rukun Warga 018</p>
        </div>
    </div>
</body>
</html>
