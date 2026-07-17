<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat_Resmi_{{ str_replace(' ', '_', $surat->jenis_surat) }}_{{ $surat->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f3f4f6;
            font-family: "Times New Roman", Times, serif;
            color: #000;
        }
        
        .paper {
            background-color: #fff;
            width: 210mm;
            min-height: 297mm;
            padding: 25mm 20mm 20mm 20mm;
            margin: 20px auto;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            position: relative;
            box-sizing: border-box;
        }

        .kop-surat {
            border-bottom: 4px double #000;
            padding-bottom: 12px;
            margin-bottom: 25px;
            text-align: center;
        }

        .kop-title-large {
            font-size: 1.4rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
            line-height: 1.2;
        }

        .kop-title-medium {
            font-size: 1.2rem;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .kop-sub {
            font-size: 0.85rem;
            font-style: italic;
            margin-bottom: 0;
            opacity: 0.85;
        }

        .surat-title-box {
            text-align: center;
            margin-bottom: 25px;
        }

        .surat-title {
            font-size: 1.15rem;
            font-weight: 700;
            text-decoration: underline;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .surat-number {
            font-size: 0.95rem;
        }

        .content-section {
            line-height: 1.6;
            font-size: 1rem;
            text-align: justify;
        }

        .data-table {
            width: 100%;
            margin: 20px 0;
        }

        .data-table td {
            padding: 4px 8px;
            vertical-align: top;
        }

        .data-table td.label-col {
            width: 32%;
        }

        .data-table td.colon-col {
            width: 3%;
            text-align: center;
        }

        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }

        .signature-box {
            width: 40%;
            text-align: center;
        }

        .tte-box {
            border: 1px dashed #22c55e;
            background-color: rgba(34,197,94,0.03);
            border-radius: 8px;
            padding: 10px;
            margin: 15px auto;
            max-width: 220px;
            font-size: 0.75rem;
            color: #15803d;
            font-family: monospace;
            text-align: center;
        }

        .tte-box img {
            width: 60px;
            height: 60px;
            margin-bottom: 6px;
        }

        .tte-hash-code {
            word-break: break-all;
            font-size: 0.6rem;
            color: #86efac;
            background: #14532d;
            padding: 4px;
            border-radius: 4px;
            margin-top: 4px;
            display: block;
        }

        /* Print Controls Panel */
        .print-control-panel {
            max-width: 210mm;
            margin: 15px auto 0 auto;
            border-radius: 12px;
        }

        @media print {
            body {
                background-color: #fff;
            }
            .paper {
                margin: 0;
                box-shadow: none;
                width: 100%;
                min-height: auto;
                padding: 15mm 15mm 15mm 15mm;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- PRINT CONTROL BUTTONS -->
    <div class="print-control-panel no-print p-3 bg-dark text-white d-flex align-items-center justify-content-between shadow-sm">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-file-pdf fa-lg text-danger"></i>
            <div>
                <strong class="d-block" style="font-size: 0.9rem;">Dokumen Surat Sah (TTE)</strong>
                <span class="text-white-50 small" style="font-size: 0.75rem;">Siap dicetak atau disimpan sebagai file PDF.</span>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.close()" class="btn btn-sm btn-outline-light"><i class="fas fa-times me-1"></i>Tutup</button>
            <button onclick="window.print()" class="btn btn-sm btn-success px-3"><i class="fas fa-print me-1"></i>Cetak Surat / Simpan PDF</button>
        </div>
    </div>

    <!-- MAIN PAPER SHEET -->
    <div class="paper">
        
        <!-- KOP SURAT (LETTERHEAD) -->
        <div class="kop-surat">
            <div class="d-flex align-items-center justify-content-center gap-3">
                <!-- Mock logo garuda / daerah -->
                <div style="width: 70px; height: 70px; flex-shrink: 0;" class="text-black">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="2.5" class="w-100 h-100">
                        <path d="M50 10 C30 25, 20 45, 20 60 C20 78, 35 90, 50 90 C65 90, 80 78, 80 60 C80 45, 70 25, 50 10 Z"/>
                        <path d="M50 10 V90" stroke-width="1.2" stroke-dasharray="2 2" opacity="0.5"/>
                        <path d="M32 50 H68" stroke-width="1" opacity="0.3"/>
                        <circle cx="50" cy="50" r="10" fill="currentColor" fill-opacity="0.1"/>
                    </svg>
                </div>
                <div>
                    <div class="kop-title-large">Pemerintah Kota Bandung</div>
                    <div class="kop-title-medium">Rukun Tetangga {{ $surat->rt_number ?? '006' }} / Rukun Warga 018</div>
                    <div class="kop-title-medium">Kelurahan Bandung Indah, Kecamatan Bandung Raya</div>
                    <p class="kop-sub">Sekretariat: Blok {{ $warga->blok_rumah ?? 'A' }} Jl. Sakura Indah RT {{ $surat->rt_number ?? '006' }} RW 018, Kota Bandung</p>
                </div>
            </div>
        </div>

        <!-- TITLE & NOMOR SURAT -->
        <div class="surat-title-box">
            <h5 class="surat-title">{{ $surat->jenis_surat }}</h5>
            <div class="surat-number">Nomor: {{ date('Y', strtotime($surat->created_at)) }}/RT-{{ $surat->rt_number }}/RW-018/{{ str_pad($surat->id, 4, '0', STR_PAD_LEFT) }}</div>
        </div>

        <!-- CONTENT -->
        <div class="content-section">
            <p>Yang bertanda tangan di bawah ini Pengurus Rukun Tetangga {{ $surat->rt_number }} / Rukun Warga 018 Kelurahan Bandung Indah Kecamatan Bandung Raya menerangkan dengan sebenarnya bahwa:</p>
            
            <table class="data-table">
                <tr>
                    <td class="label-col">Nama Lengkap</td>
                    <td class="colon-col">:</td>
                    <td><strong>{{ $warga->nama ?? $user->name }}</strong></td>
                </tr>
                <tr>
                    <td class="label-col">NIK (No. KTP)</td>
                    <td class="colon-col">:</td>
                    <td>{{ $warga->nik ?? $user->nik ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-col">Nomor Kartu Keluarga</td>
                    <td class="colon-col">:</td>
                    <td>{{ $warga->no_kk ?? $user->no_kk ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-col">Jenis Kelamin</td>
                    <td class="colon-col">:</td>
                    <td>{{ $warga ? ($warga->gender == 'L' ? 'Laki-laki' : 'Perempuan') : '-' }}</td>
                </tr>
                <tr>
                    <td class="label-col">Tempat/Tanggal Lahir</td>
                    <td class="colon-col">:</td>
                    <td>{{ $warga && $warga->tanggal_lahir ? \Carbon\Carbon::parse($warga->tanggal_lahir)->translatedFormat('d F Y') : '-' }}</td>
                </tr>
                <tr>
                    <td class="label-col">No. Telepon/HP</td>
                    <td class="colon-col">:</td>
                    <td>{{ $warga->nomor_hp ?? $user->phone ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-col">Alamat Lengkap</td>
                    <td class="colon-col">:</td>
                    <td>{{ $warga->alamat ?? $user->address ?? '-' }}</td>
                </tr>
            </table>

            <p>Bahwa nama yang bersangkutan di atas adalah benar-benar warga yang bertempat tinggal di wilayah kami Rukun Tetangga {{ $surat->rt_number }} / Rukun Warga 018 Kelurahan Bandung Indah.</p>
            
            <p>Surat Keterangan Pengantar ini dikeluarkan secara resmi atas permohonan bersangkutan untuk keperluan/tujuan:</p>
            <div class="p-3 my-3 bg-light border-start border-4 border-primary rounded-2 fs-6 fw-bold text-dark">
                &ldquo; {{ $surat->keperluan }} &rdquo;
            </div>

            <p>Demikian surat keterangan pengantar ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya, serta bagi instansi terkait harap menjadi maklum adanya.</p>
        </div>

        <!-- DATE & SIGNATURES -->
        <div class="text-end mt-4 mb-2 small text-muted" style="font-style: italic;">
            Diterbitkan di Kota Bandung pada tanggal: {{ $surat->updated_at->translatedFormat('d F Y') }}
        </div>

        <div class="signature-section">
            <!-- RT SIGNATURE -->
            <div class="signature-box">
                <span class="d-block mb-1">Mengetahui,</span>
                <strong class="d-block">Ketua RT {{ $surat->rt_number }}</strong>
                
                <!-- Mock RT TTE Sign -->
                <div class="tte-box">
                    <!-- Simple Mock QR Code SVG -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="6" class="mx-auto d-block mb-1 text-success">
                        <rect x="10" y="10" width="30" height="30" fill="currentColor"/>
                        <rect x="60" y="10" width="30" height="30" fill="currentColor"/>
                        <rect x="10" y="60" width="30" height="30" fill="currentColor"/>
                        <rect x="60" y="60" width="15" height="15" fill="currentColor"/>
                        <rect x="80" y="80" width="10" height="10" fill="currentColor"/>
                        <circle cx="50" cy="50" r="10" fill="currentColor"/>
                    </svg>
                    <span class="d-block fw-bold" style="font-size: 0.65rem;">TTE RT {{ $surat->rt_number }} VALID</span>
                    <span class="text-muted d-block" style="font-size: 0.55rem;">ID: RT-{{ $surat->rt_number }}-{{ $surat->id }}</span>
                </div>
                
                <span class="d-block text-decoration-underline fw-bold">Ketua RT {{ $surat->rt_number }}</span>
                <span class="small text-muted d-block">Pemerintah Kota Bandung</span>
            </div>

            <!-- RW SIGNATURE -->
            <div class="signature-box">
                <span class="d-block mb-1">Menyetujui & Mengesahkan,</span>
                <strong class="d-block">Ketua RW 018</strong>
                
                <!-- Real TTE Sign for RW -->
                <div class="tte-box">
                    <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="6" class="mx-auto d-block mb-1 text-primary">
                        <rect x="10" y="10" width="30" height="30"/>
                        <rect x="60" y="10" width="30" height="30"/>
                        <rect x="10" y="60" width="30" height="30"/>
                        <path d="M45 45 H55 V55 H45 Z" fill="currentColor"/>
                        <rect x="60" y="60" width="30" height="30" fill="currentColor"/>
                    </svg>
                    <span class="d-block fw-bold text-primary" style="font-size: 0.65rem;">TTE RW 018 SAH</span>
                    <span class="text-muted d-block" style="font-size: 0.55rem;">Tanda Tangan Elektronik</span>
                    <span class="tte-hash-code text-white">{{ substr($surat->tte_hash, 0, 16) }}...</span>
                </div>
                
                <span class="d-block text-decoration-underline fw-bold">Ketua RW 018</span>
                <span class="small text-muted d-block">Kel. Bandung Indah</span>
            </div>
        </div>

        <!-- FOOTER VERIFICATION INFO -->
        <div class="mt-5 pt-3 text-center text-muted" style="border-top: 1px dashed #ccc; font-size: 0.7rem; font-family: sans-serif;">
            <i class="fas fa-shield-alt text-success me-1"></i> Surat ini sah secara hukum dan diterbitkan melalui Sistem Digital Kemasyarakatan NexaNest RW 018.<br>
            Keaslian tanda tangan elektronik dapat diverifikasi secara hukum melalui hash SHA-256: <strong class="text-dark">{{ $surat->tte_hash }}</strong>
        </div>
    </div>

    <!-- AUTO PRINT SCRIPT FOR CONVENIENCE -->
    <script>
        window.addEventListener('load', function() {
            // Auto open print dialog when loaded (helpful if they want it straight to PDF)
            setTimeout(() => {
                window.print();
            }, 600);
        });
    </script>
</body>
</html>
