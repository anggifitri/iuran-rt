@php
    $rw018 = \App\Models\WilayahRw::where('rw_number', '018')->first();
    $adminRwUser = \App\Models\User::where('role', 'admin')->where('rt_number', '000')->first();
    $adminRtUsers = \App\Models\User::where(function($q) {
        $q->where('role', 'admin_rt')->orWhere('role', 'admin');
    })->where('rt_number', '!=', '000')->orderBy('rt_number')->get();

    $activeRts = ['006', '007', '008', '009', '010'];
    $adminRtNames = [
        '006' => 'Fathir Hartono',
        '007' => 'Gavin Gunawan',
        '008' => 'Rizky Pramudya',
        '009' => 'Rian Sucipto',
        '010' => 'Bagas Kusumah',
    ];

    $wargasByRt = [];
    foreach ($activeRts as $rtKey) {
        $wList = \App\Models\Warga::where('rt_number', $rtKey)
            ->with('user')
            ->orderBy('is_kk', 'desc')
            ->orderBy('nama', 'asc')
            ->get()
            ->map(function ($w) {
                $email = $w->user ? $w->user->email : null;
                if (!$email) {
                    $u = \App\Models\User::where('warga_id', $w->id)->orWhere('name', $w->nama)->first();
                    $email = $u ? $u->email : 'tanpa akun login';
                }
                $blok = $w->blok_rumah ?: ($w->alamat ?: 'Blok -');

                return [
                    'warga_id' => $w->id,
                    'user_id' => $w->user_id ?? ($w->user ? $w->user->id : null),
                    'nama' => $w->nama,
                    'blok' => $blok,
                    'email' => $email,
                    'label' => "{$w->nama} — {$blok} ({$email})",
                    'is_kk' => (bool)$w->is_kk,
                ];
            });
        $wargasByRt[$rtKey] = $wList;
    }
@endphp

<!-- Modal Role Switcher -->
<div class="modal fade" id="modalGlobalRoleSwitcher" tabindex="-1" aria-labelledby="modalGlobalRoleSwitcherLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 22px; overflow: hidden;">
            <!-- Header with Champagne Gold & Forest Green gradient -->
            <div class="modal-header text-white px-4 py-3" style="background: linear-gradient(135deg, #183C2C 0%, #285943 55%, #6B4E12 100%); border-bottom: 2px solid #D4AF37;">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: rgba(255, 215, 0, 0.2); border: 1.5px solid #FFD54F;">
                        <i class="fas fa-crown text-warning fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold m-0 fs-6 text-white" id="modalGlobalRoleSwitcherLabel">
                            NexaNest Role Switcher · Simulasi Peran
                        </h5>
                        <p class="m-0 text-white-50" style="font-size: 0.74rem;">
                            Beralih konteks peran operasional di RW 018 tanpa mengubah role akun di database
                        </p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('super_admin.role_switch.switch') }}" method="POST" id="roleSwitcherForm" onsubmit="return validateRoleSwitcherForm(event);">
                @csrf
                <input type="hidden" name="target_rw" value="018">

                <div class="modal-body p-4 sa-modal-body" style="background: #FAFCFA;">
                    <!-- Operational Scope Badge & Security Notice -->
                    <div class="p-3 mb-4 rounded-3 border d-flex align-items-center justify-content-between flex-wrap gap-2 sa-op-badge" style="background: #F0FDF4; border-color: #BBF7D0 !important;">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge" style="background: #285943; color: #FFF; font-size: 0.74rem;">
                                <i class="fas fa-map-pin me-1"></i> Wilayah Operasional
                            </span>
                            <strong class="text-dark small sa-wilayah-text">
                                {{ $rw018 ? (str_starts_with($rw018->nama_wilayah, 'RW') ? $rw018->nama_wilayah : 'RW 018 ' . $rw018->nama_wilayah) : 'RW 018 Kelurahan Mekarjaya' }}
                            </strong>
                        </div>
                        <span class="badge bg-warning-subtle text-dark border border-warning" style="font-size: 0.72rem;">
                            <i class="fas fa-shield-alt text-warning-emphasis me-1"></i> Aktor Audit: Super Admin
                        </span>
                    </div>

                    <!-- 1. PILIH PERAN -->
                    <label class="form-label fw-bold text-dark mb-2" style="font-size: 0.86rem;">
                        <span class="badge bg-success-subtle text-success me-1">1</span> Pilih Peran yang Ingin Disimulasikan:
                    </label>
                    <div class="row g-3 mb-4">
                        <!-- Card Admin RW -->
                        <div class="col-md-4">
                            <label class="card h-100 p-3 border-2 cursor-pointer role-card text-center" style="border-radius: 16px; cursor: pointer;">
                                <input type="radio" name="target_role" value="admin_rw" class="form-check-input visually-hidden" checked onchange="updateRoleSwitcherFields()">
                                <div class="role-icon-box role-icon-admin-rw mx-auto mb-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: #E8F5E9; color: #2E7D32;">
                                    <i class="fas fa-building-columns fs-4"></i>
                                </div>
                                <h6 class="fw-bold mb-1 text-dark fs-7">Admin RW 018</h6>
                                <p class="text-muted m-0" style="font-size: 0.72rem; line-height: 1.35;">
                                    Monitoring 5 RT (RT 006–010), persetujuan surat RW, verifikasi kependudukan, kas lingkungan &amp; tata kelola RW.
                                </p>
                            </label>
                        </div>

                        <!-- Card Admin RT -->
                        <div class="col-md-4">
                            <label class="card h-100 p-3 border-2 cursor-pointer role-card text-center" style="border-radius: 16px; cursor: pointer;">
                                <input type="radio" name="target_role" value="admin_rt" class="form-check-input visually-hidden" onchange="updateRoleSwitcherFields()">
                                <div class="role-icon-box role-icon-admin-rt mx-auto mb-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: #E3F2FD; color: #1565C0;">
                                    <i class="fas fa-users-gear fs-4"></i>
                                </div>
                                <h6 class="fw-bold mb-1 text-dark fs-7">Admin RT (006 - 010)</h6>
                                <p class="text-muted m-0" style="font-size: 0.72rem; line-height: 1.35;">
                                    Kelola warga RT spesifik, validasi iuran bulanan, surat pengantar RT, dan jadwal ronda lingkungan.
                                </p>
                            </label>
                        </div>

                        <!-- Card Warga -->
                        <div class="col-md-4">
                            <label class="card h-100 p-3 border-2 cursor-pointer role-card text-center" style="border-radius: 16px; cursor: pointer;">
                                <input type="radio" name="target_role" value="warga" class="form-check-input visually-hidden" onchange="updateRoleSwitcherFields()">
                                <div class="role-icon-box role-icon-warga mx-auto mb-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: #F3E5F5; color: #7B1FA2;">
                                    <i class="fas fa-user-check fs-4"></i>
                                </div>
                                <h6 class="fw-bold mb-1 text-dark fs-7">Warga RW 018</h6>
                                <p class="text-muted m-0" style="font-size: 0.72rem; line-height: 1.35;">
                                    Eksplorasi layanan warga: pembayaran iuran, permohonan surat digital, pengaduan, dan KMS posyandu.
                                </p>
                            </label>
                        </div>
                    </div>

                    <!-- 2. PILIHAN RT / AKUN SASARAN (CASCADING FILTER) -->
                    <div id="fieldRtSelectionWrapper" class="p-3 mb-4 rounded-3 border bg-white sa-card-inner" style="display: none;">
                        <label class="form-label fw-bold text-dark mb-2" style="font-size: 0.86rem;">
                            <span class="badge bg-success-subtle text-success me-1">2</span> Tentukan Wilayah RT &amp; Akun Warga Sasaran:
                        </label>
                        <div class="row g-3">
                            <!-- Dropdown Kiri: Pilih RT -->
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted" id="labelTargetRt">
                                    Pilih RT di RW 018 <span class="text-danger" id="reqMarkRt">*</span>
                                </label>
                                <select name="target_rt" class="form-select" id="roleSwitchTargetRt" style="border-radius: 10px;" onchange="onRoleSwitchRtChanged()">
                                    <!-- Populated dynamically based on role -->
                                </select>
                                <div class="small text-muted mt-1" style="font-size: 0.72rem;">
                                    Wilayah operasional aktif: <strong>RT 006 s/d RT 010</strong>
                                </div>
                            </div>

                            <!-- Dropdown Kanan: Pilih Akun Warga Dinamis -->
                            <div class="col-md-6" id="fieldWargaUserWrapper" style="display: none;">
                                <label class="form-label small fw-semibold text-muted">
                                    Pilih Akun Warga <span class="text-danger">*</span>
                                </label>
                                <select name="target_warga_id" class="form-select" id="roleSwitchTargetWargaId" style="border-radius: 10px;" disabled onchange="checkRoleSwitcherFormValidity()">
                                    <option value="">-- Pilih RT terlebih dahulu untuk melihat daftar warga --</option>
                                </select>
                                <div id="wargaCountFeedback" class="small mt-1 text-muted" style="font-size: 0.74rem;">
                                    Pilih RT untuk memuat daftar seluruh warga terdaftar.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. MODE EKSEKUSI -->
                    <label class="form-label fw-bold text-dark mb-2" style="font-size: 0.86rem;">
                        <span class="badge bg-success-subtle text-success me-1">3</span> Mode Eksekusi Simulasi:
                    </label>
                    <div class="row g-3">
                        <!-- Mode Preview -->
                        <div class="col-md-6">
                            <label class="card p-3 border-2 mode-card cursor-pointer h-100" style="border-radius: 14px; cursor: pointer;">
                                <div class="d-flex align-items-start gap-2">
                                    <input type="radio" name="mode" value="preview" class="form-check-input mt-1" checked>
                                    <div>
                                        <div class="fw-bold text-dark fs-7 d-flex align-items-center gap-1">
                                            <i class="fas fa-eye text-info"></i> Mode Preview (Hanya Lihat)
                                        </div>
                                        <p class="text-muted m-0 mt-1" style="font-size: 0.74rem; line-height: 1.35;">
                                            Aman untuk menguji tampilan dashboard dan navigasi. Seluruh aksi penambahan, perubahan, dan penghapusan data diproteksi backend.
                                        </p>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <!-- Mode Operasional -->
                        <div class="col-md-6">
                            <label class="card p-3 border-2 mode-card cursor-pointer h-100" style="border-radius: 14px; cursor: pointer;">
                                <div class="d-flex align-items-start gap-2">
                                    <input type="radio" name="mode" value="operational" class="form-check-input mt-1">
                                    <div>
                                        <div class="fw-bold text-dark fs-7 d-flex align-items-center gap-1">
                                            <i class="fas fa-bolt text-success"></i> Mode Operasional
                                        </div>
                                        <p class="text-muted m-0 mt-1" style="font-size: 0.74rem; line-height: 1.35;">
                                            Super Admin dapat melakukan tindakan nyata (menyetujui permohonan surat, pencatatan kas, mutasi) dengan konfirmasi keamanan.
                                        </p>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer px-4 py-3 bg-white d-flex align-items-center justify-content-between sa-modal-footer">
                    <button type="button" class="btn btn-light px-3 py-2 text-secondary fw-semibold" data-bs-dismiss="modal" style="border-radius: 10px; font-size: 0.84rem;">
                        Batal
                    </button>
                    <button type="submit" id="btnStartRoleSwitch" class="btn btn-success px-4 py-2 fw-bold d-inline-flex align-items-center gap-2 shadow-sm" style="background: linear-gradient(135deg, #285943 0%, #1E4633 100%); border-radius: 10px; font-size: 0.86rem;">
                        <i class="fas fa-play"></i>
                        <span>Mulai Simulasi Peran</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .role-card {
        border-color: #E2E8F0;
        background: #FFFFFF;
        transition: all 0.2s ease;
    }
    .role-card:hover {
        border-color: #A8C9B5;
        background: #F7FAF8;
        transform: translateY(-2px);
    }
    .role-card:has(input:checked) {
        border-color: #285943 !important;
        background: #F0F7F2 !important;
        box-shadow: 0 4px 14px rgba(40, 89, 67, 0.12);
    }
    .mode-card {
        border-color: #E2E8F0;
        background: #FFFFFF;
        transition: all 0.2s ease;
    }
    .mode-card:hover {
        border-color: #CBD5E1;
    }
    .mode-card:has(input:checked) {
        border-color: #285943 !important;
        background: #F8FAF9 !important;
        box-shadow: 0 4px 12px rgba(40, 89, 67, 0.08);
    }

    /* Dark Mode Polishing */
    html.dark .sa-modal-body {
        background: #0B1120 !important;
    }
    html.dark .sa-modal-footer {
        background: #0D1426 !important;
        border-color: #1E293B !important;
    }
    html.dark .sa-card-inner {
        background: #0E1626 !important;
        border-color: #1E2D4A !important;
    }
    html.dark .sa-op-badge {
        background: rgba(16, 185, 129, 0.10) !important;
        border-color: rgba(16, 185, 129, 0.25) !important;
    }
    html.dark .sa-wilayah-text {
        color: #F8FAFC !important;
    }
    html.dark .role-card,
    html.dark .mode-card {
        border-color: #1E2D4A !important;
        background: #131B2E !important;
        color: #F1F5F9 !important;
    }
    html.dark .role-card:hover,
    html.dark .mode-card:hover {
        border-color: #38BDF8 !important;
        background: #17223B !important;
    }
    html.dark .role-card:has(input:checked),
    html.dark .mode-card:has(input:checked) {
        border-color: #38BDF8 !important;
        background: rgba(56, 189, 248, 0.15) !important;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.4) !important;
    }

    /* Role Icon Circle Dark Mode Fix */
    html.dark .role-icon-box.role-icon-admin-rw {
        background: rgba(34, 197, 94, 0.16) !important;
        color: #4ADE80 !important;
    }
    html.dark .role-icon-box.role-icon-admin-rt {
        background: rgba(59, 130, 246, 0.16) !important;
        color: #60A5FA !important;
    }
    html.dark .role-icon-box.role-icon-warga {
        background: rgba(168, 85, 247, 0.16) !important;
        color: #C084FC !important;
    }
</style>

<script>
    // Embedded Data Warga per RT (RW 018)
    window.__nexanestWargasByRt = @json($wargasByRt);
    window.__nexanestAdminRtNames = @json($adminRtNames);

    function updateRoleSwitcherFields() {
        const selectedRole = document.querySelector('input[name="target_role"]:checked')?.value;
        const rtSection = document.getElementById('fieldRtSelectionWrapper');
        const wargaSection = document.getElementById('fieldWargaUserWrapper');
        const rtSelect = document.getElementById('roleSwitchTargetRt');
        const wargaSelect = document.getElementById('roleSwitchTargetWargaId');
        const feedback = document.getElementById('wargaCountFeedback');

        if (!selectedRole || !rtSelect) return;

        if (selectedRole === 'admin_rw') {
            if (rtSection) rtSection.style.display = 'none';
            if (wargaSection) wargaSection.style.display = 'none';
            rtSelect.removeAttribute('required');
            if (wargaSelect) wargaSelect.removeAttribute('required');
        } else if (selectedRole === 'admin_rt') {
            if (rtSection) rtSection.style.display = 'block';
            if (wargaSection) wargaSection.style.display = 'none';
            if (wargaSelect) wargaSelect.removeAttribute('required');

            // Populate RT dropdown for Admin RT (default RT 006 selected)
            rtSelect.setAttribute('required', 'required');
            rtSelect.innerHTML = '';
            const rts = ['006', '007', '008', '009', '010'];
            rts.forEach(rt => {
                const adminName = window.__nexanestAdminRtNames[rt] || 'Ketua RT';
                const opt = document.createElement('option');
                opt.value = rt;
                opt.textContent = `RT ${rt} (${adminName})`;
                if (rt === '006') opt.selected = true;
                rtSelect.appendChild(opt);
            });
        } else {
            // Role: Warga RW 018
            if (rtSection) rtSection.style.display = 'block';
            if (wargaSection) wargaSection.style.display = 'block';

            // Step 1: Wajib Pilih RT Terlebih Dahulu (Default Kosong)
            rtSelect.setAttribute('required', 'required');
            rtSelect.innerHTML = '';
            
            const defaultOpt = document.createElement('option');
            defaultOpt.value = '';
            defaultOpt.textContent = '-- Pilih RT Terlebih Dahulu --';
            defaultOpt.selected = true;
            rtSelect.appendChild(defaultOpt);

            const rts = ['006', '007', '008', '009', '010'];
            rts.forEach(rt => {
                const opt = document.createElement('option');
                opt.value = rt;
                opt.textContent = `RT ${rt}`;
                rtSelect.appendChild(opt);
            });

            // Step 2: Kunci dropdown warga sebelum RT dipilih
            if (wargaSelect) {
                wargaSelect.setAttribute('required', 'required');
                wargaSelect.setAttribute('disabled', 'disabled');
                wargaSelect.innerHTML = '<option value="">-- Pilih RT terlebih dahulu untuk melihat daftar warga --</option>';
            }
            if (feedback) {
                feedback.className = 'small mt-1 text-muted';
                feedback.innerHTML = '<i class="fas fa-info-circle me-1"></i>Pilih RT di sebelah kiri untuk menampilkan seluruh warga.';
            }
        }

        checkRoleSwitcherFormValidity();
    }

    function onRoleSwitchRtChanged() {
        const selectedRole = document.querySelector('input[name="target_role"]:checked')?.value;
        const rtSelect = document.getElementById('roleSwitchTargetRt');
        const wargaSelect = document.getElementById('roleSwitchTargetWargaId');
        const feedback = document.getElementById('wargaCountFeedback');

        if (selectedRole !== 'warga' || !rtSelect || !wargaSelect) {
            checkRoleSwitcherFormValidity();
            return;
        }

        const selectedRt = rtSelect.value;

        if (!selectedRt) {
            // RT belum dipilih
            wargaSelect.setAttribute('disabled', 'disabled');
            wargaSelect.innerHTML = '<option value="">-- Pilih RT terlebih dahulu untuk melihat daftar warga --</option>';
            if (feedback) {
                feedback.className = 'small mt-1 text-muted';
                feedback.innerHTML = '<i class="fas fa-info-circle me-1"></i>Pilih RT di sebelah kiri untuk menampilkan seluruh warga.';
            }
            checkRoleSwitcherFormValidity();
            return;
        }

        // Ambil data warga dari embedded cache atau fetch
        const wargaList = (window.__nexanestWargasByRt && window.__nexanestWargasByRt[selectedRt]) || [];

        wargaSelect.removeAttribute('disabled');
        wargaSelect.innerHTML = '';

        if (wargaList.length > 0) {
            const promptOpt = document.createElement('option');
            promptOpt.value = '';
            promptOpt.textContent = `-- Pilih Salah Satu Warga di RT ${selectedRt} (${wargaList.length} Warga) --`;
            promptOpt.selected = true;
            wargaSelect.appendChild(promptOpt);

            wargaList.forEach(w => {
                const opt = document.createElement('option');
                opt.value = w.warga_id;
                opt.textContent = w.label;
                wargaSelect.appendChild(opt);
            });

            if (feedback) {
                feedback.className = 'small mt-1 text-success fw-semibold';
                feedback.innerHTML = `<i class="fas fa-check-circle me-1"></i>Ditemukan <strong>${wargaList.length}</strong> akun warga terdaftar di RT ${selectedRt}.`;
            }
        } else {
            const emptyOpt = document.createElement('option');
            emptyOpt.value = '';
            emptyOpt.textContent = `-- Belum ada akun warga di RT ${selectedRt} --`;
            wargaSelect.appendChild(emptyOpt);

            if (feedback) {
                feedback.className = 'small mt-1 text-warning fw-semibold';
                feedback.innerHTML = `<i class="fas fa-triangle-exclamation me-1"></i>Belum ditemukan data warga terdaftar di RT ${selectedRt}.`;
            }
        }

        checkRoleSwitcherFormValidity();
    }

    function checkRoleSwitcherFormValidity() {
        const selectedRole = document.querySelector('input[name="target_role"]:checked')?.value;
        const submitBtn = document.getElementById('btnStartRoleSwitch');
        const rtSelect = document.getElementById('roleSwitchTargetRt');
        const wargaSelect = document.getElementById('roleSwitchTargetWargaId');

        if (!submitBtn) return;

        if (selectedRole === 'warga') {
            const hasRt = rtSelect && rtSelect.value !== '';
            const hasWarga = wargaSelect && wargaSelect.value !== '';

            if (!hasRt || !hasWarga) {
                submitBtn.setAttribute('disabled', 'disabled');
                submitBtn.title = 'Silakan pilih RT dan salah satu Warga untuk memulai simulasi.';
            } else {
                submitBtn.removeAttribute('disabled');
                submitBtn.title = 'Mulai Simulasi Peran Warga';
            }
        } else if (selectedRole === 'admin_rt') {
            const hasRt = rtSelect && rtSelect.value !== '';
            if (!hasRt) {
                submitBtn.setAttribute('disabled', 'disabled');
            } else {
                submitBtn.removeAttribute('disabled');
                submitBtn.title = 'Mulai Simulasi Peran Admin RT';
            }
        } else {
            // Admin RW
            submitBtn.removeAttribute('disabled');
            submitBtn.title = 'Mulai Simulasi Peran Admin RW';
        }
    }

    function validateRoleSwitcherForm(e) {
        const selectedRole = document.querySelector('input[name="target_role"]:checked')?.value;
        const rtSelect = document.getElementById('roleSwitchTargetRt');
        const wargaSelect = document.getElementById('roleSwitchTargetWargaId');

        if (selectedRole === 'warga') {
            if (!rtSelect || !rtSelect.value) {
                alert('Wajib memilih RT di RW 018 terlebih dahulu.');
                if (rtSelect) rtSelect.focus();
                e.preventDefault();
                return false;
            }
            if (!wargaSelect || !wargaSelect.value) {
                alert('Wajib memilih salah satu warga di RT terpilih untuk simulasi peran Warga.');
                if (wargaSelect) wargaSelect.focus();
                e.preventDefault();
                return false;
            }
        } else if (selectedRole === 'admin_rt') {
            if (!rtSelect || !rtSelect.value) {
                alert('Wajib memilih RT untuk simulasi peran Admin RT.');
                if (rtSelect) rtSelect.focus();
                e.preventDefault();
                return false;
            }
        }
        return true;
    }

    document.addEventListener('DOMContentLoaded', updateRoleSwitcherFields);
</script>
