@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card p-4 shadow-sm border-0" style="border-radius: 16px; background-color: var(--bg-card); border: 1px solid var(--border-color);">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold mb-0 text-dark" style="color: var(--text-main) !important;"><i class="fas fa-plus-circle me-2 text-primary"></i>Tambah Data Posyandu</h4>
                    <a href="{{ route('posyandu.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Kembali</a>
                </div>

                <form method="POST" action="{{ route('posyandu.store') }}" id="posyanduForm">
                    @csrf

                    <!-- 1. PILIH TIPE DATA -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary">Kategori Tipe Data</label>
                        <select name="type" id="dataTypeSelect" class="form-select form-select-lg" required style="border-radius: 10px;">
                            <option value="jadwal">Jadwal Kegiatan / Pemeriksaan Posyandu</option>
                            <option value="anak">Rekam Medis Tumbuh Kembang Anak (Balita)</option>
                            <option value="bumil">Rekam Medis Kesehatan Ibu Hamil</option>
                        </select>
                    </div>

                    <hr class="my-4" style="border-color: var(--border-color); opacity: 0.5;">

                    <!-- ========================================== -->
                    <!-- SECTION 1: JADWAL KEGIATAN                 -->
                    <!-- ========================================== -->
                    <div id="sectionJadwal" class="form-section">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary">Nama Kegiatan Posyandu</label>
                            <input type="text" name="nama" id="inputNamaJadwal" class="form-control" placeholder="Contoh: Posyandu Balita Melati - Pemeriksaan Tumbuh Kembang" required style="border-radius: 10px;">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-secondary">Tanggal Pelaksanaan</label>
                                <input type="date" name="tanggal" id="inputTanggalJadwal" class="form-control" required style="border-radius: 10px;">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-secondary">Lokasi Kegiatan</label>
                                <input type="text" name="lokasi" id="inputLokasiJadwal" class="form-control" placeholder="Contoh: Balai RW 018" style="border-radius: 10px;">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary">Keterangan Tambahan / Layanan</label>
                            <textarea name="keterangan" id="inputKeteranganJadwal" class="form-control" rows="4" placeholder="Tuliskan rincian kegiatan posyandu dan layanan yang diberikan..." style="border-radius: 10px;"></textarea>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- SECTION 2: REKAM MEDIS ANAK (BALITA)       -->
                    <!-- ========================================== -->
                    <div id="sectionAnak" class="form-section" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary">Pilih Anak (Balita)</label>
                            <select id="selectAnak" class="form-select mb-2" style="border-radius: 10px;">
                                <option value="">-- Pilih Anak Terdaftar --</option>
                                @foreach($targetAnak as $ch)
                                    <option value="{{ $ch->id }}" data-nama="{{ $ch->nama }}" data-tgllahir="{{ $ch->tanggal_lahir }}">
                                        {{ $ch->nama }} (Lahir: {{ \Carbon\Carbon::parse($ch->tanggal_lahir)->translatedFormat('d M Y') }} | Blok: {{ $ch->blok_rumah }})
                                    </option>
                                @endforeach
                                <option value="manual">-- Input Nama Secara Manual --</option>
                            </select>
                            <div id="manualAnakDiv" style="display: none;" class="mt-2">
                                <input type="text" id="inputManualNamaAnak" class="form-control" placeholder="Tulis Nama Lengkap Anak..." style="border-radius: 10px;">
                            </div>
                            <!-- Input name actual to be submitted -->
                            <input type="hidden" name="nama_anak" id="actualNamaAnak">
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold text-secondary">Umur Anak (Bulan)</label>
                                <input type="number" name="umur_bulan" id="inputUmurBulan" class="form-control" min="0" placeholder="Pilih anak atau isi manual..." required style="border-radius: 10px;">
                                <small class="text-muted" style="font-size: 0.75rem;">Terhitung otomatis jika memilih anak terdaftar.</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold text-secondary">Berat Badan (kg)</label>
                                <input type="number" name="berat_badan" step="0.01" class="form-control" placeholder="Contoh: 12.5" required style="border-radius: 10px;">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold text-secondary">Tinggi Badan (cm)</label>
                                <input type="number" name="tinggi_badan" step="0.01" class="form-control" placeholder="Contoh: 85.3" required style="border-radius: 10px;">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary">Status Tumbuh Kembang</label>
                            <select name="status_tumbuh" id="selectStatusTumbuh" class="form-select" required style="border-radius: 10px;">
                                <option value="Normal">Normal (Kurva Hijau KMS)</option>
                                <option value="Kurang Gizi">Kurang Gizi</option>
                                <option value="Stunting">Stunting (Sangat Pendek)</option>
                                <option value="Kelebihan Berat Badan">Kelebihan Berat Badan (Overweight)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary">Rekomendasi & Solusi Gizi</label>
                            <textarea name="solusi" id="inputSolusiAnak" class="form-control" rows="3" placeholder="Pilih status tumbuh kembang untuk memuat saran otomatis, atau tulis saran custom..." style="border-radius: 10px;"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary d-block">Riwayat Imunisasi Diberikan</label>
                            <div class="row g-2 px-2 py-3 rounded bg-light border">
                                @php
                                    $vaccines = ['BCG', 'Polio 1', 'Polio 2', 'Polio 3', 'DPT-HB-Hib 1', 'DPT-HB-Hib 2', 'DPT-HB-Hib 3', 'Campak-Rubela'];
                                @endphp
                                @foreach($vaccines as $vax)
                                    <div class="col-sm-6 col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="imunisasi_checked[]" value="{{ $vax }}" id="vax_{{ Str::slug($vax) }}">
                                            <label class="form-check-label text-dark fw-medium" for="vax_{{ Str::slug($vax) }}" style="font-size: 0.8rem; cursor: pointer;">
                                                {{ $vax }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- SECTION 3: REKAM MEDIS IBU HAMIL (BUMIL)   -->
                    <!-- ========================================== -->
                    <div id="sectionBumil" class="form-section" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary">Pilih Ibu Hamil</label>
                            <select id="selectBumil" class="form-select mb-2" style="border-radius: 10px;">
                                <option value="">-- Pilih Ibu Terdaftar --</option>
                                @foreach($targetBumil as $mom)
                                    <option value="{{ $mom->id }}" data-nama="{{ $mom->nama }}">
                                        {{ $mom->nama }} (Umur: {{ $mom->umur }} Thn | Blok: {{ $mom->blok_rumah }})
                                    </option>
                                @endforeach
                                <option value="manual">-- Input Nama Secara Manual --</option>
                            </select>
                            <div id="manualBumilDiv" style="display: none;" class="mt-2">
                                <input type="text" id="inputManualNamaBumil" class="form-control" placeholder="Tulis Nama Lengkap Ibu Hamil..." style="border-radius: 10px;">
                            </div>
                            <!-- Input name actual to be submitted -->
                            <input type="hidden" name="nama_ibu" id="actualNamaIbu">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-secondary">Usia Kehamilan (Minggu)</label>
                                <input type="number" name="usia_kehamilan_minggu" class="form-control" min="0" max="42" placeholder="Contoh: 14" required style="border-radius: 10px;">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-secondary">Berat Badan Ibu (kg)</label>
                                <input type="number" name="berat_badan" step="0.01" class="form-control" placeholder="Contoh: 62.8" required style="border-radius: 10px;">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-secondary">Tekanan Darah (mmHg)</label>
                                <input type="text" name="tekanan_darah" class="form-control" placeholder="Contoh: 120/80" required style="border-radius: 10px;">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-secondary">Lingkar Lengan Atas / LiLA (cm)</label>
                                <input type="number" name="lila" step="0.01" class="form-control" placeholder="Contoh: 24.5" required style="border-radius: 10px;">
                                <small class="text-muted" style="font-size: 0.75rem;">Status KEK terindikasi jika LiLA < 23.5 cm.</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary">Status Kesehatan Ibu</label>
                            <select name="status_kesehatan" id="selectStatusKesehatan" class="form-select" required style="border-radius: 10px;">
                                <option value="Sehat">Sehat Walafiat</option>
                                <option value="Kekurangan Energi Kronis (KEK)">Kekurangan Energi Kronis (KEK)</option>
                                <option value="Hipertensi Gestasional">Hipertensi Gestasional</option>
                                <option value="Anemia Ringan">Anemia Ringan</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary">Saran & Tindakan Medis</label>
                            <textarea name="solusi" id="inputSolusiBumil" class="form-control" rows="3" placeholder="Pilih status kesehatan untuk memuat saran otomatis, atau tulis tindakan custom..." style="border-radius: 10px;"></textarea>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm" style="border-radius: 12px;"><i class="fas fa-save me-1"></i>Simpan Rekam Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // 1. TAMPILKAN SUB-FORM YANG SESUAI DENGAN TIPE DATA YANG DIPILIH
    document.getElementById('dataTypeSelect').addEventListener('change', function() {
        const type = this.value;
        
        // Sembunyikan semua section
        document.querySelectorAll('.form-section').forEach(el => el.style.display = 'none');
        
        // Reset validasi required untuk field-field section yang tidak aktif
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
            // Abaikan input manual name div dan hidden inputs
            if (el.id !== 'inputManualNamaAnak' && el.id !== 'inputManualNamaBumil' && el.type !== 'hidden' && el.type !== 'checkbox') {
                if (isRequired) {
                    el.setAttribute('required', 'required');
                } else {
                    el.removeAttribute('required');
                }
            }
        });
    }

    // Pemicu awal saat load halaman
    toggleRequiredFields('sectionAnak', false);
    toggleRequiredFields('sectionBumil', false);

    // ==========================================
    // JS UNTUK REKAM ANAK (BALITA)              
    // ==========================================
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
            
            // Dapatkan opsi yang dipilih
            const option = this.options[this.selectedIndex];
            const nama = option.getAttribute('data-nama');
            const tglLahir = option.getAttribute('data-tgllahir');
            
            actualNamaAnak.value = nama;
            
            // Hitung umur anak dalam bulan otomatis
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

    // Template solusi otomatis gizi anak
    const selectStatusTumbuh = document.getElementById('selectStatusTumbuh');
    const inputSolusiAnak = document.getElementById('inputSolusiAnak');
    const templatesSolusiAnak = {
        'Normal': 'Pertahankan pola pemberian makanan bergizi 3 kali sehari dan ASI/susu. Pantau terus tinggi dan berat badan anak di Posyandu setiap bulan untuk menjaga grafiknya tetap di zona hijau KMS.',
        'Kurang Gizi': 'Berat badan anak berada di bawah batas normal KMS. Berikan Pemberian Makanan Tambahan (PMT) kaya protein hewani (telur, ikan, daging), tingkatkan frekuensi makan menjadi 3x makanan utama dan 2x selingan padat gizi, serta segera konsultasi ke Puskesmas.',
        'Stunting': 'Tinggi badan anak tidak sesuai dengan usianya (pendek/stunted). Diperlukan intervensi gizi spesifik: berikan asupan protein hewani setiap hari, konsultasikan dengan dokter anak di Puskesmas untuk pemberian suplemen Zinc/Zat Besi.',
        'Kelebihan Berat Badan': 'Berat badan anak melebihi kurva pertumbuhan atas. Kurangi porsi makanan instan, batasi susu formula/minuman manis dengan gula tambahan, dorong anak untuk bermain fisik aktif (merangkak, berjalan, berlari), dan konsultasikan dengan ahli gizi.'
    };
    
    // Set default value
    inputSolusiAnak.value = templatesSolusiAnak['Normal'];
    
    selectStatusTumbuh.addEventListener('change', function() {
        inputSolusiAnak.value = templatesSolusiAnak[this.value] || '';
    });

    // ==========================================
    // JS UNTUK REKAM IBU HAMIL (BUMIL)          
    // ==========================================
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
            const nama = option.getAttribute('data-nama');
            actualNamaIbu.value = nama;
        }
    });

    inputManualNamaBumil.addEventListener('input', function() {
        actualNamaIbu.value = this.value;
    });

    // Template solusi otomatis kesehatan bumil
    const selectStatusKesehatan = document.getElementById('selectStatusKesehatan');
    const inputSolusiBumil = document.getElementById('inputSolusiBumil');
    const templatesSolusiBumil = {
        'Sehat': 'Kondisi kesehatan ibu dan perkembangan janin terpantau sangat baik dan normal. Lanjutkan konsumsi makanan bergizi seimbang (karbohidrat, protein, buah, sayur), minum 1 Tablet Tambah Darah (TTD) setiap hari, minum air putih 2.5 - 3 liter per hari, dan ikuti kelas senam hamil secara rutin.',
        'Kekurangan Energi Kronis (KEK)': 'Lingkar lengan atas (LILA) kurang dari 23.5 cm menunjukkan risiko KEK. Ibu hamil wajib mengonsumsi PMT pemulihan ibu hamil (biskuit/susu), menambah asupan kalori protein harian, rutin minum Tablet Tambah Darah (TTD), serta meminimalkan aktivitas fisik yang melelahkan.',
        'Hipertensi Gestasional': 'Tekanan darah ibu hamil tinggi (di atas 140/90 mmHg). Batasi ketat konsumsi garam dan makanan asin/instan, perbanyak istirahat dengan posisi tidur miring kiri untuk melancarkan aliran darah ke janin, kelola stres dengan baik, dan lakukan kontrol mingguan ke bidan/dokter kandungan.',
        'Anemia Ringan': 'Kadar hemoglobin ibu rendah. Disarankan untuk meminum Tablet Tambah Darah (TTD) 2 kali sehari (minum menggunakan air jeruk/sumber Vitamin C untuk memaksimalkan penyerapan zat besi), perbanyak makan sayur hijau (bayam, daun katuk), hati ayam, dan daging merah, serta hindari minum teh setelah makan.'
    };
    
    // Set default value
    inputSolusiBumil.value = templatesSolusiBumil['Sehat'];
    
    selectStatusKesehatan.addEventListener('change', function() {
        inputSolusiBumil.value = templatesSolusiBumil[this.value] || '';
    });
</script>
@endsection
