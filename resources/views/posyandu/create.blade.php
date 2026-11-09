@extends('layouts.app')

@section('title', 'Tambah Data Posyandu — NexaNest')

@section('content')
<div class="nx-page-header">
    <div>
        <span class="badge rounded-pill mb-2 px-3 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;">
            <i class="fas fa-heart-circle-plus me-1"></i> Pencatatan Layanan Posyandu
        </span>
        <h1 class="nx-page-title">Tambah Data Posyandu</h1>
        <p class="nx-page-subtitle">Input jadwal kegiatan baru, rekam tumbuh kembang balita, atau pemeriksaan ibu hamil.</p>
    </div>
    <a href="{{ route('posyandu.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Kembali ke Info Posyandu
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card p-4">
            <form method="POST" action="{{ route('posyandu.store') }}" id="posyanduForm">
                @csrf

                <!-- 1. PILIH TIPE DATA -->
                <div class="p-3 rounded-4 mb-4" style="background: var(--nx-mint-soft); border: 1px solid var(--nx-sage);">
                    <label class="form-label fw-bold" style="color: var(--nx-forest);">
                        <i class="fas fa-layer-group me-1"></i> Pilih Kategori Data Posyandu
                    </label>
                    <select name="type" id="dataTypeSelect" class="form-select" required>
                        <option value="jadwal">1. Jadwal Kegiatan / Pemeriksaan Posyandu</option>
                        <option value="anak">2. Rekam Medis Tumbuh Kembang Anak (Balita)</option>
                        <option value="bumil">3. Rekam Medis Kesehatan Ibu Hamil</option>
                    </select>
                </div>

                <!-- SECTION 1: JADWAL KEGIATAN -->
                <div id="sectionJadwal" class="form-section">
                    <div class="mb-3">
                        <label class="form-label">Nama Kegiatan Posyandu <span class="text-danger">*</span></label>
                        <input type="text" name="nama" id="inputNamaJadwal" class="form-control"
                               placeholder="Contoh: Posyandu Balita Melati - Pemeriksaan Tumbuh Kembang" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Pelaksanaan <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" id="inputTanggalJadwal" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lokasi Kegiatan</label>
                            <input type="text" name="lokasi" id="inputLokasiJadwal" class="form-control" placeholder="Contoh: Balai RW 018">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan Tambahan / Layanan</label>
                        <textarea name="keterangan" id="inputKeteranganJadwal" class="form-control" rows="4"
                                  placeholder="Tuliskan rincian kegiatan posyandu dan layanan yang diberikan..."></textarea>
                    </div>
                </div>

                <!-- SECTION 2: REKAM MEDIS ANAK (BALITA) -->
                <div id="sectionAnak" class="form-section" style="display: none;">
                    <div class="mb-3">
                        <label class="form-label">Pilih Anak (Balita) <span class="text-danger">*</span></label>
                        <select id="selectAnak" class="form-select mb-2">
                            <option value="">-- Pilih Anak Terdaftar --</option>
                            @foreach($targetAnak as $ch)
                                <option value="{{ $ch->id }}" data-nama="{{ $ch->nama }}" data-tgllahir="{{ $ch->tanggal_lahir }}">
                                    {{ $ch->nama }} (Lahir: {{ \Carbon\Carbon::parse($ch->tanggal_lahir)->translatedFormat('d M Y') }} | Blok: {{ $ch->blok_rumah }})
                                </option>
                            @endforeach
                            <option value="manual">-- Input Nama Secara Manual --</option>
                        </select>
                        <div id="manualAnakDiv" style="display: none;" class="mt-2">
                            <input type="text" id="inputManualNamaAnak" class="form-control" placeholder="Tulis Nama Lengkap Anak...">
                        </div>
                        <input type="hidden" name="nama_anak" id="actualNamaAnak">
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Umur Anak (Bulan) <span class="text-danger">*</span></label>
                            <input type="number" name="umur_bulan" id="inputUmurBulan" class="form-control" min="0" placeholder="Otomatis / manual">
                            <small class="text-muted" style="font-size: 0.74rem;">Terhitung otomatis jika memilih anak terdaftar.</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Berat Badan (kg) <span class="text-danger">*</span></label>
                            <input type="number" name="berat_badan" step="0.01" class="form-control" placeholder="Contoh: 12.5">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tinggi Badan (cm) <span class="text-danger">*</span></label>
                            <input type="number" name="tinggi_badan" step="0.01" class="form-control" placeholder="Contoh: 85.3">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status Tumbuh Kembang <span class="text-danger">*</span></label>
                        <select name="status_tumbuh" id="selectStatusTumbuh" class="form-select">
                            <option value="Normal">Normal (Kurva Hijau KMS)</option>
                            <option value="Kurang Gizi">Kurang Gizi</option>
                            <option value="Stunting">Stunting (Sangat Pendek)</option>
                            <option value="Kelebihan Berat Badan">Kelebihan Berat Badan (Overweight)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Rekomendasi &amp; Solusi Gizi</label>
                        <textarea name="solusi" id="inputSolusiAnak" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label d-block">Riwayat Imunisasi Diberikan</label>
                        <div class="row g-2 p-3 rounded-4" style="background: var(--nx-bg); border: 1px solid var(--nx-border);">
                            @php
                                $vaccines = ['BCG', 'Polio 1', 'Polio 2', 'Polio 3', 'DPT-HB-Hib 1', 'DPT-HB-Hib 2', 'DPT-HB-Hib 3', 'Campak-Rubela'];
                            @endphp
                            @foreach($vaccines as $vax)
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="imunisasi_checked[]" value="{{ $vax }}" id="vax_{{ Str::slug($vax) }}">
                                        <label class="form-check-label fw-semibold small" for="vax_{{ Str::slug($vax) }}" style="cursor: pointer;">
                                            {{ $vax }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- SECTION 3: REKAM MEDIS IBU HAMIL (BUMIL) -->
                <div id="sectionBumil" class="form-section" style="display: none;">
                    <div class="mb-3">
                        <label class="form-label">Pilih Ibu Hamil <span class="text-danger">*</span></label>
                        <select id="selectBumil" class="form-select mb-2">
                            <option value="">-- Pilih Ibu Terdaftar --</option>
                            @foreach($targetBumil as $mom)
                                <option value="{{ $mom->id }}" data-nama="{{ $mom->nama }}">
                                    {{ $mom->nama }} (Umur: {{ $mom->umur }} Thn | Blok: {{ $mom->blok_rumah }})
                                </option>
                            @endforeach
                            <option value="manual">-- Input Nama Secara Manual --</option>
                        </select>
                        <div id="manualBumilDiv" style="display: none;" class="mt-2">
                            <input type="text" id="inputManualNamaBumil" class="form-control" placeholder="Tulis Nama Lengkap Ibu Hamil...">
                        </div>
                        <input type="hidden" name="nama_ibu" id="actualNamaIbu">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Usia Kehamilan (Minggu) <span class="text-danger">*</span></label>
                            <input type="number" name="usia_kehamilan_minggu" class="form-control" min="0" max="42" placeholder="Contoh: 14">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Berat Badan Ibu (kg) <span class="text-danger">*</span></label>
                            <input type="number" name="berat_badan" step="0.01" class="form-control" placeholder="Contoh: 62.8">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tekanan Darah (mmHg) <span class="text-danger">*</span></label>
                            <input type="text" name="tekanan_darah" class="form-control" placeholder="Contoh: 120/80">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lingkar Lengan Atas / LiLA (cm) <span class="text-danger">*</span></label>
                            <input type="number" name="lila" step="0.01" class="form-control" placeholder="Contoh: 24.5">
                            <small class="text-muted" style="font-size: 0.74rem;">Status KEK terindikasi jika LiLA &lt; 23.5 cm.</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status Kesehatan Ibu <span class="text-danger">*</span></label>
                        <select name="status_kesehatan" id="selectStatusKesehatan" class="form-select">
                            <option value="Sehat">Sehat Walafiat</option>
                            <option value="Kekurangan Energi Kronis (KEK)">Kekurangan Energi Kronis (KEK)</option>
                            <option value="Hipertensi Gestasional">Hipertensi Gestasional</option>
                            <option value="Anemia Ringan">Anemia Ringan</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Saran &amp; Tindakan Medis</label>
                        <textarea name="solusi" id="inputSolusiBumil" class="form-control" rows="3"></textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 pt-3 border-top mt-4">
                    <a href="{{ route('posyandu.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> Simpan Rekam Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('dataTypeSelect').addEventListener('change', function() {
        const type = this.value;
        document.querySelectorAll('.form-section').forEach(el => el.style.display = 'none');

        toggleRequiredFields('sectionJadwal', false);
        toggleRequiredFields('sectionAnak', false);
        toggleRequiredFields('sectionBumil', false);

        if (type === 'jadwal') {
            document.getElementById('sectionJadwal').style.display = 'block';
            toggleRequiredFields('sectionJadwal', true);
        } else if (type === 'anak') {
            document.getElementById('sectionAnak').style.display = 'block';
            toggleRequiredFields('sectionAnak', true);
        } else if (type === 'bumil') {
            document.getElementById('sectionBumil').style.display = 'block';
            toggleRequiredFields('sectionBumil', true);
        }
    });

    function toggleRequiredFields(sectionId, isRequired) {
        const section = document.getElementById(sectionId);
        section.querySelectorAll('input, select, textarea').forEach(el => {
            if (el.id !== 'inputManualNamaAnak' && el.id !== 'inputManualNamaBumil' && el.type !== 'hidden' && el.type !== 'checkbox') {
                if (isRequired) {
                    el.setAttribute('required', 'required');
                } else {
                    el.removeAttribute('required');
                }
            }
        });
    }

    toggleRequiredFields('sectionAnak', false);
    toggleRequiredFields('sectionBumil', false);

    const selectAnak = document.getElementById('selectAnak');
    const manualAnakDiv = document.getElementById('manualAnakDiv');
    const inputManualNamaAnak = document.getElementById('inputManualNamaAnak');
    const actualNamaAnak = document.getElementById('actualNamaAnak');
    const inputUmurBulan = document.getElementById('inputUmurBulan');

    selectAnak.addEventListener('change', function() {
        const selectedVal = this.value;
        if (selectedVal === 'manual') {
            manualAnakDiv.style.display = 'block';
            inputManualNamaAnak.setAttribute('required', 'required');
            inputManualNamaAnak.value = '';
            actualNamaAnak.value = '';
            inputUmurBulan.value = '';
            inputUmurBulan.removeAttribute('readonly');
        } else if (selectedVal === '') {
            manualAnakDiv.style.display = 'none';
            inputManualNamaAnak.removeAttribute('required');
            actualNamaAnak.value = '';
            inputUmurBulan.value = '';
            inputUmurBulan.removeAttribute('readonly');
        } else {
            manualAnakDiv.style.display = 'none';
            inputManualNamaAnak.removeAttribute('required');
            const option = this.options[this.selectedIndex];
            const nama = option.getAttribute('data-nama');
            const tglLahir = option.getAttribute('data-tgllahir');
            actualNamaAnak.value = nama;

            if (tglLahir) {
                const birthDate = new Date(tglLahir);
                const today = new Date();
                const diffMonths = (today.getFullYear() - birthDate.getFullYear()) * 12 + today.getMonth() - birthDate.getMonth();
                inputUmurBulan.value = Math.max(0, diffMonths);
                inputUmurBulan.setAttribute('readonly', 'readonly');
            } else {
                inputUmurBulan.value = '';
                inputUmurBulan.removeAttribute('readonly');
            }
        }
    });

    inputManualNamaAnak.addEventListener('input', function() {
        actualNamaAnak.value = this.value;
    });

    const selectStatusTumbuh = document.getElementById('selectStatusTumbuh');
    const inputSolusiAnak = document.getElementById('inputSolusiAnak');
    const templatesSolusiAnak = {
        'Normal': 'Pertahankan pola pemberian makanan bergizi 3 kali sehari dan ASI/susu. Pantau terus tinggi dan berat badan anak di Posyandu setiap bulan untuk menjaga grafiknya tetap di zona hijau KMS.',
        'Kurang Gizi': 'Berat badan anak berada di bawah batas normal KMS. Berikan Pemberian Makanan Tambahan (PMT) kaya protein hewani (telur, ikan, daging), tingkatkan frekuensi makan menjadi 3x makanan utama dan 2x selingan padat gizi, serta segera konsultasi ke Puskesmas.',
        'Stunting': 'Tinggi badan anak tidak sesuai dengan usianya (pendek/stunted). Diperlukan intervensi gizi spesifik: berikan asupan protein hewani setiap hari, konsultasikan dengan dokter anak di Puskesmas untuk pemberian suplemen Zinc/Zat Besi.',
        'Kelebihan Berat Badan': 'Berat badan anak melebihi kurva pertumbuhan atas. Kurangi porsi makanan instan, batasi susu formula/minuman manis dengan gula tambahan, dorong anak untuk bermain fisik aktif, dan konsultasikan dengan ahli gizi.'
    };
    inputSolusiAnak.value = templatesSolusiAnak['Normal'];
    selectStatusTumbuh.addEventListener('change', function() {
        inputSolusiAnak.value = templatesSolusiAnak[this.value] || '';
    });

    const selectBumil = document.getElementById('selectBumil');
    const manualBumilDiv = document.getElementById('manualBumilDiv');
    const inputManualNamaBumil = document.getElementById('inputManualNamaBumil');
    const actualNamaIbu = document.getElementById('actualNamaIbu');

    selectBumil.addEventListener('change', function() {
        const selectedVal = this.value;
        if (selectedVal === 'manual') {
            manualBumilDiv.style.display = 'block';
            inputManualNamaBumil.setAttribute('required', 'required');
            inputManualNamaBumil.value = '';
            actualNamaIbu.value = '';
        } else if (selectedVal === '') {
            manualBumilDiv.style.display = 'none';
            inputManualNamaBumil.removeAttribute('required');
            actualNamaIbu.value = '';
        } else {
            manualBumilDiv.style.display = 'none';
            inputManualNamaBumil.removeAttribute('required');
            const option = this.options[this.selectedIndex];
            actualNamaIbu.value = option.getAttribute('data-nama');
        }
    });

    inputManualNamaBumil.addEventListener('input', function() {
        actualNamaIbu.value = this.value;
    });

    const selectStatusKesehatan = document.getElementById('selectStatusKesehatan');
    const inputSolusiBumil = document.getElementById('inputSolusiBumil');
    const templatesSolusiBumil = {
        'Sehat': 'Kondisi kesehatan ibu dan perkembangan janin terpantau sangat baik dan normal. Lanjutkan konsumsi makanan bergizi seimbang, minum 1 Tablet Tambah Darah (TTD) setiap hari, minum air putih cukup, dan ikuti kelas senam hamil.',
        'Kekurangan Energi Kronis (KEK)': 'Lingkar lengan atas (LiLA) kurang dari 23.5 cm menunjukkan risiko KEK. Ibu hamil wajib mengonsumsi PMT pemulihan ibu hamil (biskuit/susu), menambah asupan kalori protein harian, dan rutin minum TTD.',
        'Hipertensi Gestasional': 'Tekanan darah ibu hamil tinggi. Batasi konsumsi garam, perbanyak istirahat posisi miring kiri, kelola stres, dan lakukan kontrol rutin ke bidan/dokter kandungan.',
        'Anemia Ringan': 'Kadar hemoglobin ibu rendah. Disarankan meminum Tablet Tambah Darah (TTD) 2 kali sehari bersama sumber Vitamin C, perbanyak makan sayur hijau dan protein hewani, serta hindari minum teh setelah makan.'
    };
    inputSolusiBumil.value = templatesSolusiBumil['Sehat'];
    selectStatusKesehatan.addEventListener('change', function() {
        inputSolusiBumil.value = templatesSolusiBumil[this.value] || '';
    });
</script>
@endsection
