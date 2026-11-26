<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\PengajuanRw;
use App\Models\User;
use App\Models\Warga;
use App\Models\WilayahRt;
use App\Models\WilayahRw;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class WilayahRwController extends Controller
{
    /**
     * Display listing and management center for Wilayah RW 018.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status');

        $rw = WilayahRw::where('rw_number', '018')->first();
        if (!$rw) {
            $rw = WilayahRw::create([
                'rw_number' => '018',
                'tenant_id' => 'TENANT-RW018-MEKARJAYA',
                'nama_wilayah' => 'RW 018 Kelurahan Mekarjaya',
                'kelurahan' => 'Kelurahan Mekarjaya',
                'kecamatan' => 'Kecamatan Sukmajaya',
                'kota' => 'Kota Depok',
                'provinsi' => 'Jawa Barat',
                'kode_pos' => '16411',
                'alamat_sekretariat' => 'Balai Warga RW 018, Jl. Boulevard Utama No. 01, Mekarjaya, Depok',
                'ketua_rw_nama' => 'Dirga Pradipta',
                'ketua_rw_phone' => '081234567890',
                'email_resmi' => 'sekretariat@rw018.nexanest.id',
                'admin_user_id' => 2,
                'status' => 'active',
                'total_rt' => 5,
                'catatan' => 'Wilayah operasional utama NexaNest: 5 RT terhubung, smart community & sustainable neighborhood.',
            ]);
        }

        // Active RTs count
        $activeRtCount = WilayahRt::where('rw_number', '018')->where('status', 'active')->count();
        if ($rw->total_rt !== $activeRtCount && $activeRtCount > 0) {
            $rw->total_rt = $activeRtCount;
            $rw->save();
        }

        $totalRt = $activeRtCount;
        $totalRtAll = WilayahRt::where('rw_number', '018')->count();
        $totalWarga = Warga::where('rw_number', '018')->count();

        // Active operational RT list for RW 018
        $operationalRts = WilayahRt::where('rw_number', '018')
            ->where('status', 'active')
            ->pluck('rt_number')
            ->toArray();

        // Count only active Admin RTs connected to active operational RTs (RT 006-010)
        $totalAdminRt = User::where('rw_number', '018')
            ->whereIn('rt_number', $operationalRts)
            ->where(function ($q) {
                $q->where('role', 'admin')->orWhere('role', 'admin_rt');
            })
            ->where('status', 'active')
            ->count();

        // Update default catatan if it contains outdated "8 RT"
        if (empty($rw->catatan) || str_contains($rw->catatan, '8 RT')) {
            $rw->catatan = "Wilayah operasional utama NexaNest: {$totalRt} RT terhubung (RT 006–010), smart community & sustainable neighborhood.";
            $rw->save();
        }

        // Warga distribution per RT
        $wargaCounts = Warga::where('rw_number', '018')
            ->selectRaw('rt_number, count(*) as count')
            ->groupBy('rt_number')
            ->pluck('count', 'rt_number')
            ->toArray();

        // RT List with search filter if applied
        $rtQuery = WilayahRt::where('rw_number', '018')->with('adminUser');
        if ($search) {
            $rtQuery->where(function ($q) use ($search) {
                $q->where('rt_number', 'like', "%{$search}%")
                  ->orWhere('nama_ketua_rt', 'like', "%{$search}%")
                  ->orWhere('phone_ketua_rt', 'like', "%{$search}%")
                  ->orWhere('catatan', 'like', "%{$search}%");
            });
        }
        if ($status && in_array($status, ['active', 'inactive'])) {
            $rtQuery->where('status', $status);
        }
        $rts = $rtQuery->orderBy('rt_number')->get();

        foreach ($rts as $rt) {
            $rt->warga_count = $wargaCounts[$rt->rt_number] ?? 0;
        }

        // Admin RW and Candidates
        $adminRw = User::find($rw->admin_user_id) ?? User::where('rw_number', '018')->where('rt_number', '000')->first();
        $candidateAdmins = User::where(function ($q) {
            $q->where('role', 'admin')->orWhere('role', 'admin_rw')->orWhere('role', 'super_admin');
        })->get();
        $candidateUsers = User::where('status', 'active')->orderBy('name')->get();

        // Recent Audit logs specifically for Region & Territory Management
        $auditLogs = AuditLog::where(function ($q) {
            $q->where('module', 'Manajemen Wilayah')
              ->orWhere('action_type', 'like', '%WILAYAH%')
              ->orWhere('action_type', 'like', '%PENGAJUAN_RW%')
              ->orWhereIn('action_type', ['ASSIGN_ADMIN_RW', 'ASSIGN_ADMIN_RT']);
        })
        ->whereNotIn('event', ['login', 'logout', 'role_switch', 'role_switch_exit', 'role_switch_mode_toggle', 'session_expired'])
        ->whereNotIn('action_type', ['LOGIN', 'LOGOUT', 'ROLE_SWITCH'])
        ->latest()
        ->limit(8)
        ->get();

        // Fallback for view compatibility
        $rws = collect([$rw]);
        $totalRw = 1;
        $activeRw = $rw->status === 'active' ? 1 : 0;
        $suspendedRw = $rw->status === 'suspended' ? 1 : 0;

        $tenantModules = self::getTenantModules();

        return view('super_admin.wilayah.index', compact(
            'rw',
            'rws',
            'rts',
            'totalRw',
            'activeRw',
            'suspendedRw',
            'totalRt',
            'totalRtAll',
            'totalWarga',
            'totalAdminRt',
            'adminRw',
            'candidateAdmins',
            'candidateUsers',
            'auditLogs',
            'tenantModules',
            'search',
            'status'
        ));
    }

    /**
     * Directly create a new Wilayah RW.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rw_number' => 'required|string|max:10|unique:wilayah_rw,rw_number',
            'nama_wilayah' => 'required|string|max:255',
            'kelurahan' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kota' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
            'kode_pos' => 'nullable|string|max:10',
            'alamat_sekretariat' => 'nullable|string',
            'ketua_rw_nama' => 'nullable|string|max:255',
            'ketua_rw_phone' => 'nullable|string|max:25',
            'total_rt' => 'required|integer|min:1|max:50',
            'admin_user_id' => 'nullable|exists:users,id',
            'create_new_admin' => 'nullable|boolean',
            'new_admin_name' => 'nullable|string|max:255',
            'new_admin_email' => 'nullable|email|unique:users,email',
            'new_admin_password' => 'nullable|string|min:6',
        ]);

        $adminUserId = $validated['admin_user_id'] ?? null;

        // Optionally create new Admin RW user
        if ($request->boolean('create_new_admin') && !empty($validated['new_admin_email'])) {
            $newAdmin = User::create([
                'name' => $validated['new_admin_name'] ?: 'Admin RW ' . $validated['rw_number'],
                'email' => $validated['new_admin_email'],
                'password' => Hash::make($validated['new_admin_password'] ?: 'password'),
                'role' => 'admin',
                'status' => 'active',
                'rt_number' => '000',
                'rw_number' => $validated['rw_number'],
                'phone' => $validated['ketua_rw_phone'] ?? null,
                'email_verified_at' => now(),
            ]);
            $adminUserId = $newAdmin->id;
        }

        $rw = WilayahRw::create([
            'rw_number' => $validated['rw_number'],
            'nama_wilayah' => $validated['nama_wilayah'],
            'kelurahan' => $validated['kelurahan'],
            'kecamatan' => $validated['kecamatan'],
            'kota' => $validated['kota'],
            'provinsi' => $validated['provinsi'],
            'kode_pos' => $validated['kode_pos'] ?? null,
            'alamat_sekretariat' => $validated['alamat_sekretariat'] ?? null,
            'ketua_rw_nama' => $validated['ketua_rw_nama'] ?? null,
            'ketua_rw_phone' => $validated['ketua_rw_phone'] ?? null,
            'total_rt' => $validated['total_rt'],
            'admin_user_id' => $adminUserId,
            'status' => 'active',
        ]);

        AuditLogger::log(
            'CREATE_WILAYAH_RW',
            'Super Admin Platform',
            $rw->id,
            "Super Admin (" . Auth::user()->name . ") mendaftarkan wilayah baru RW {$rw->rw_number} ({$rw->nama_wilayah})",
            null,
            $rw->toArray(),
            '000',
            'success',
            'CREATE'
        );

        return back()->with('success', "Wilayah RW {$rw->rw_number} berhasil didaftarkan ke platform NexaNest.");
    }

    /**
     * Update an existing Wilayah RW profile.
     */
    public function update(Request $request, $id)
    {
        $rw = WilayahRw::findOrFail($id);

        $validated = $request->validate([
            'nama_wilayah' => 'required|string|max:255',
            'kelurahan' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kota' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
            'kode_pos' => 'nullable|string|max:10',
            'alamat_sekretariat' => 'nullable|string',
            'ketua_rw_nama' => 'nullable|string|max:255',
            'ketua_rw_phone' => 'nullable|string|max:25',
            'email_resmi' => 'nullable|email|max:255',
            'catatan' => 'nullable|string',
            'status' => 'nullable|in:active,suspended,inactive,pending',
        ]);

        $oldData = $rw->toArray();
        $rw->update($validated);

        AuditLogger::log(
            'UPDATE_WILAYAH_RW',
            'Manajemen Wilayah',
            $rw->id,
            "Super Admin (" . Auth::user()->name . ") memperbarui profil identitas wilayah RW {$rw->rw_number}",
            $oldData,
            $rw->toArray(),
            '000',
            'success',
            'UPDATE'
        );

        return back()->with('success', "Profil identitas wilayah RW {$rw->rw_number} berhasil diperbarui.");
    }

    /**
     * Store a new RT in RW 018.
     */
    public function storeRt(Request $request)
    {
        $validated = $request->validate([
            'rt_number' => ['required', 'string', 'regex:/^[0-9]{3}$/', 'unique:wilayah_rt,rt_number,NULL,id,rw_number,018'],
            'nama_ketua_rt' => 'nullable|string|max:255',
            'phone_ketua_rt' => 'nullable|string|max:25',
            'admin_user_id' => 'nullable|exists:users,id',
            'catatan' => 'nullable|string|max:500',
        ], [
            'rt_number.regex' => 'Nomor RT harus berupa format 3 digit angka (contoh: 011).',
            'rt_number.unique' => 'Nomor RT ini sudah terdaftar di RW 018.',
        ]);

        $rw = WilayahRw::where('rw_number', '018')->first();
        $rwId = $rw ? $rw->id : null;

        $rt = WilayahRt::create([
            'wilayah_rw_id' => $rwId,
            'rw_number' => '018',
            'rt_number' => $validated['rt_number'],
            'nama_ketua_rt' => $validated['nama_ketua_rt'] ?? null,
            'phone_ketua_rt' => $validated['phone_ketua_rt'] ?? null,
            'admin_user_id' => $validated['admin_user_id'] ?? null,
            'status' => 'active',
            'catatan' => $validated['catatan'] ?? null,
        ]);

        // If admin assigned, ensure role and rt_number
        if (!empty($validated['admin_user_id'])) {
            $user = User::find($validated['admin_user_id']);
            if ($user) {
                $user->role = 'admin';
                $user->rt_number = $validated['rt_number'];
                $user->rw_number = '018';
                $user->save();
            }
        }

        // Update total_rt
        if ($rw) {
            $rw->total_rt = WilayahRt::where('rw_number', '018')->where('status', 'active')->count();
            $rw->save();
        }

        AuditLogger::log(
            'CREATE_WILAYAH_RT',
            'Manajemen Wilayah',
            $rt->id,
            "Super Admin (" . Auth::user()->name . ") menambahkan RT {$rt->rt_number} baru pada RW 018 (Ketua: " . ($rt->nama_ketua_rt ?: 'Belum ditentukan') . ")",
            null,
            $rt->toArray(),
            $rt->rt_number,
            'success',
            'CREATE'
        );

        return back()->with('success', "RT {$rt->rt_number} berhasil ditambahkan ke wilayah RW 018.");
    }

    /**
     * Update an existing RT in RW 018.
     */
    public function updateRt(Request $request, $id)
    {
        $rt = WilayahRt::findOrFail($id);

        $validated = $request->validate([
            'nama_ketua_rt' => 'nullable|string|max:255',
            'phone_ketua_rt' => 'nullable|string|max:25',
            'admin_user_id' => 'nullable|exists:users,id',
            'catatan' => 'nullable|string|max:500',
        ]);

        $oldData = $rt->toArray();
        $rt->update($validated);

        if (!empty($validated['admin_user_id']) && $validated['admin_user_id'] != $oldData['admin_user_id']) {
            $user = User::find($validated['admin_user_id']);
            if ($user) {
                $user->role = 'admin';
                $user->rt_number = $rt->rt_number;
                $user->rw_number = '018';
                $user->save();
            }
        }

        AuditLogger::log(
            'UPDATE_WILAYAH_RT',
            'Manajemen Wilayah',
            $rt->id,
            "Super Admin (" . Auth::user()->name . ") memperbarui data RT {$rt->rt_number} di RW 018",
            $oldData,
            $rt->toArray(),
            $rt->rt_number,
            'success',
            'UPDATE'
        );

        return back()->with('success', "Data RT {$rt->rt_number} berhasil diperbarui.");
    }

    /**
     * Toggle status of an RT in RW 018 (Non-destructive).
     */
    public function toggleRtStatus(Request $request, $id)
    {
        $rt = WilayahRt::findOrFail($id);

        $newStatus = $rt->status === 'active' ? 'inactive' : 'active';
        $oldStatus = $rt->status;
        $rt->status = $newStatus;
        $rt->save();

        // Update total_rt in RW
        $rw = WilayahRw::where('rw_number', '018')->first();
        if ($rw) {
            $rw->total_rt = WilayahRt::where('rw_number', '018')->where('status', 'active')->count();
            $rw->save();
        }

        $wargaCount = Warga::where('rw_number', '018')->where('rt_number', $rt->rt_number)->count();

        AuditLogger::log(
            'TOGGLE_STATUS_WILAYAH_RT',
            'Manajemen Wilayah',
            $rt->id,
            "Super Admin (" . Auth::user()->name . ") mengubah status RT {$rt->rt_number} menjadi {$newStatus} (Non-destructive: {$wargaCount} data warga & kas tetap aman)",
            ['old_status' => $oldStatus],
            ['new_status' => $newStatus],
            $rt->rt_number,
            'success',
            'STATUS_CHANGE'
        );

        $statusMsg = $newStatus === 'active' 
            ? "RT {$rt->rt_number} berhasil diaktifkan kembali." 
            : "RT {$rt->rt_number} berhasil dinonaktifkan. Seluruh data kependudukan ({$wargaCount} warga), kas, dan pengaduan tetap tersimpan aman (Non-destructive).";

        return back()->with('success', $statusMsg);
    }

    /**
     * Assign / reassign Administrator RW or RT.
     */
    public function assignAdmin(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:rw,rt',
            'rt_id' => 'required_if:type,rt|nullable|exists:wilayah_rt,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($validated['user_id']);

        if ($validated['type'] === 'rw') {
            $rw = WilayahRw::where('rw_number', '018')->firstOrFail();
            $oldAdminId = $rw->admin_user_id;
            $oldAdmin = $oldAdminId ? User::find($oldAdminId) : null;

            $rw->admin_user_id = $user->id;
            $rw->ketua_rw_nama = $user->name;
            if (!empty($user->phone)) {
                $rw->ketua_rw_phone = $user->phone;
            }
            $rw->save();

            // Set user role to admin and rt_number to 000
            $user->role = 'admin';
            $user->rt_number = '000';
            $user->rw_number = '018';
            $user->save();

            AuditLogger::log(
                'ASSIGN_ADMIN_RW',
                'Manajemen Wilayah',
                $rw->id,
                "Super Admin (" . Auth::user()->name . ") menugaskan {$user->name} sebagai Administrator RW 018 (Sebelumnya: " . ($oldAdmin ? $oldAdmin->name : 'Kosong') . ")",
                ['old_admin_user_id' => $oldAdminId],
                ['new_admin_user_id' => $user->id],
                '000',
                'success',
                'ASSIGNMENT'
            );

            return back()->with('success', "Penugasan Administrator RW 018 berhasil dialihkan kepada {$user->name}. Riwayat administratif terdahulu tetap terjaga utuh.");
        } else {
            $rt = WilayahRt::findOrFail($validated['rt_id']);
            $oldAdminId = $rt->admin_user_id;
            $oldAdmin = $oldAdminId ? User::find($oldAdminId) : null;

            $rt->admin_user_id = $user->id;
            $rt->nama_ketua_rt = $user->name;
            if (!empty($user->phone)) {
                $rt->phone_ketua_rt = $user->phone;
            }
            $rt->save();

            $user->role = 'admin';
            $user->rt_number = $rt->rt_number;
            $user->rw_number = '018';
            $user->save();

            AuditLogger::log(
                'ASSIGN_ADMIN_RT',
                'Manajemen Wilayah',
                $rt->id,
                "Super Admin (" . Auth::user()->name . ") menugaskan {$user->name} sebagai Administrator RT {$rt->rt_number} (Sebelumnya: " . ($oldAdmin ? $oldAdmin->name : 'Kosong') . ")",
                ['old_admin_user_id' => $oldAdminId],
                ['new_admin_user_id' => $user->id],
                $rt->rt_number,
                'success',
                'ASSIGNMENT'
            );

            return back()->with('success', "Penugasan Administrator RT {$rt->rt_number} berhasil dialihkan kepada {$user->name}. Riwayat administratif terdahulu tetap terjaga utuh.");
        }
    }

    /**
     * Toggle status of Wilayah RW (active, suspended, inactive).
     */
    public function toggleStatus(Request $request, $id)
    {
        $rw = WilayahRw::findOrFail($id);
        $newStatus = $request->input('status', 'active');

        if (!in_array($newStatus, ['active', 'suspended', 'inactive'])) {
            return back()->with('error', 'Status tidak valid.');
        }

        $oldStatus = $rw->status;
        $rw->status = $newStatus;
        $rw->save();

        AuditLogger::log(
            'TOGGLE_STATUS_WILAYAH_RW',
            'Super Admin Platform',
            $rw->id,
            "Super Admin (" . Auth::user()->name . ") mengubah status RW {$rw->rw_number} dari {$oldStatus} menjadi {$newStatus}",
            ['old_status' => $oldStatus],
            ['new_status' => $newStatus],
            '000',
            'success',
            'STATUS_CHANGE'
        );

        $statusHuman = match($newStatus) {
            'active' => 'diaktifkan kembali',
            'suspended' => 'ditangguhkan sementara',
            'inactive' => 'dinonaktifkan',
            default => $newStatus
        };

        return back()->with('success', "Status operasional RW {$rw->rw_number} berhasil {$statusHuman}.");
    }

    /**
     * Delete a Wilayah RW (with strict safety check).
     */
    public function destroy($id)
    {
        $rw = WilayahRw::findOrFail($id);

        if ($rw->rw_number === '018') {
            return back()->with('error', 'RW 018 adalah wilayah utama perintis platform dan tidak dapat dihapus.');
        }

        $userCount = User::where('rw_number', $rw->rw_number)->count();
        if ($userCount > 0) {
            return back()->with('error', "Gagal menghapus: Masih terdapat {$userCount} akun pengguna yang terdaftar di RW {$rw->rw_number}. Pindahkan atau nonaktifkan akun terlebih dahulu.");
        }

        $rwData = $rw->toArray();
        $rw->delete();

        AuditLogger::log(
            'DELETE_WILAYAH_RW',
            'Super Admin Platform',
            $id,
            "Super Admin (" . Auth::user()->name . ") menghapus data wilayah RW {$rw->rw_number}",
            $rwData,
            null,
            '000',
            'danger',
            'DELETE'
        );

        return back()->with('success', "Wilayah RW {$rw->rw_number} berhasil dihapus dari platform.");
    }

    /**
     * Standard list of 22 functional modules for tenant RW 018.
     */
    public static function getTenantModules(): array
    {
        return [
            ['name' => 'Dashboard & Ringkasan Komunitas', 'icon' => 'fa-gauge-high', 'category' => 'Core'],
            ['name' => 'Manajemen Iuran & Kas RT', 'icon' => 'fa-wallet', 'category' => 'Keuangan'],
            ['name' => 'Pembayaran & Konfirmasi Iuran', 'icon' => 'fa-receipt', 'category' => 'Keuangan'],
            ['name' => 'Verifikasi Warga Baru', 'icon' => 'fa-user-check', 'category' => 'Kependudukan'],
            ['name' => 'Mutasi Warga (Pindah)', 'icon' => 'fa-people-arrows', 'category' => 'Kependudukan'],
            ['name' => 'Administrasi Surat Pengantar RT/RW', 'icon' => 'fa-file-signature', 'category' => 'Pelayanan'],
            ['name' => 'Pengaduan & Aspirasi Warga', 'icon' => 'fa-bullhorn', 'category' => 'Pelayanan'],
            ['name' => 'Manajemen Fasilitas & Peminjaman', 'icon' => 'fa-building', 'category' => 'Fasilitas'],
            ['name' => 'Jadwal Ronda & Keamanan Lingkungan', 'icon' => 'fa-shield-halved', 'category' => 'Keamanan'],
            ['name' => 'Integrasi CCTV & Kamera Lingkungan', 'icon' => 'fa-video', 'category' => 'Keamanan'],
            ['name' => 'Bank Sampah & Tabungan Sampah', 'icon' => 'fa-recycle', 'category' => 'Lingkungan'],
            ['name' => 'Posyandu Balita & KMS Digital', 'icon' => 'fa-baby', 'category' => 'Kesehatan'],
            ['name' => 'Posyandu Ibu Hamil (ANC)', 'icon' => 'fa-person-pregnant', 'category' => 'Kesehatan'],
            ['name' => 'Direktori UMKM Warga', 'icon' => 'fa-store', 'category' => 'Ekonomi'],
            ['name' => 'Kalender Agenda & Notulensi Rapat', 'icon' => 'fa-calendar-days', 'category' => 'Agenda'],
            ['name' => 'Pusat Pengumuman & Broadcast Darurat', 'icon' => 'fa-tower-broadcast', 'category' => 'Komunikasi'],
            ['name' => 'Manajemen Aset & Inventaris RW', 'icon' => 'fa-boxes-stacked', 'category' => 'Aset'],
            ['name' => 'Log Aktivitas & Audit Forensik', 'icon' => 'fa-clock-rotate-left', 'category' => 'Keamanan'],
            ['name' => 'Kartu Identitas Warga (RFID & Barcode)', 'icon' => 'fa-id-card', 'category' => 'Kependudukan'],
            ['name' => 'Sistem Notifikasi WhatsApp/Email', 'icon' => 'fa-bell', 'category' => 'Komunikasi'],
            ['name' => 'Manajemen Wilayah & Administrasi RT', 'icon' => 'fa-map-location-dot', 'category' => 'Pemerintahan'],
            ['name' => 'Platform Control Center (Super Admin)', 'icon' => 'fa-sliders', 'category' => 'Super Admin'],
        ];
    }

    // =========================================================================
    // PENGAJUAN RW BARU (SELF-SERVICE REGISTRATION & APPROVAL WORKFLOW)
    // =========================================================================

    public function pengajuanIndex(Request $request)
    {
        $status = $request->query('status');
        $search = $request->query('search');

        $query = PengajuanRw::with('processor');

        if ($status && in_array($status, ['menunggu', 'revisi', 'disetujui', 'ditolak'])) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('rw_number', 'like', "%{$search}%")
                  ->orWhere('nomor_pengajuan', 'like', "%{$search}%")
                  ->orWhere('nama_wilayah', 'like', "%{$search}%")
                  ->orWhere('nama_pemohon', 'like', "%{$search}%")
                  ->orWhere('email_pemohon', 'like', "%{$search}%");
            });
        }

        $pengajuans = $query->latest()->paginate(15)->withQueryString();

        $countMenunggu = PengajuanRw::where('status', 'menunggu')->count();
        $countRevisi = PengajuanRw::where('status', 'revisi')->count();
        $countDisetujui = PengajuanRw::where('status', 'disetujui')->count();
        $countDitolak = PengajuanRw::where('status', 'ditolak')->count();

        return view('super_admin.wilayah.pengajuan', compact(
            'pengajuans',
            'countMenunggu',
            'countRevisi',
            'countDisetujui',
            'countDitolak',
            'status',
            'search'
        ));
    }

    public function approvePengajuan(Request $request, $id)
    {
        $pengajuan = PengajuanRw::findOrFail($id);

        if ($pengajuan->status === 'disetujui') {
            return back()->with('info', 'Pengajuan ini sudah disetujui sebelumnya.');
        }

        // Check if RW number already exists
        $existingRw = WilayahRw::where('rw_number', $pengajuan->rw_number)->first();
        if ($existingRw) {
            return back()->with('error', "RW {$pengajuan->rw_number} sudah aktif di platform. Harap gunakan nomor RW lain.");
        }

        // 1. Create or link user account for the applicant
        $applicantUser = User::where('email', $pengajuan->email_pemohon)->first();
        if (!$applicantUser) {
            $randomPassword = Str::random(10);
            $applicantUser = User::create([
                'name' => $pengajuan->nama_pemohon,
                'email' => $pengajuan->email_pemohon,
                'phone' => $pengajuan->phone_pemohon,
                'nik' => $pengajuan->nik_pemohon,
                'password' => Hash::make($randomPassword),
                'role' => 'admin',
                'status' => 'active',
                'rt_number' => '000',
                'rw_number' => $pengajuan->rw_number,
                'address' => $pengajuan->alamat_sekretariat ?: ($pengajuan->kelurahan . ', ' . $pengajuan->kecamatan),
                'email_verified_at' => now(),
            ]);
            $tempPasswordMsg = " Akun Admin RW telah dibuat dengan kata sandi default: {$randomPassword}";
        } else {
            $applicantUser->update([
                'role' => 'admin',
                'rt_number' => '000',
                'rw_number' => $pengajuan->rw_number,
                'status' => 'active',
            ]);
            $tempPasswordMsg = " Akun pengguna ({$applicantUser->email}) telah ditingkatkan menjadi Admin RW.";
        }

        // 2. Create Wilayah RW record
        $wilayah = WilayahRw::create([
            'rw_number' => $pengajuan->rw_number,
            'nama_wilayah' => $pengajuan->nama_wilayah,
            'kelurahan' => $pengajuan->kelurahan,
            'kecamatan' => $pengajuan->kecamatan,
            'kota' => $pengajuan->kota,
            'provinsi' => $pengajuan->provinsi,
            'alamat_sekretariat' => $pengajuan->alamat_sekretariat,
            'ketua_rw_nama' => $pengajuan->nama_pemohon,
            'ketua_rw_phone' => $pengajuan->phone_pemohon,
            'admin_user_id' => $applicantUser->id,
            'status' => 'active',
            'total_rt' => 6,
            'catatan' => "Disetujui dari permohonan #{$pengajuan->nomor_pengajuan} oleh Super Admin",
        ]);

        // 3. Update pengajuan status
        $pengajuan->update([
            'status' => 'disetujui',
            'catatan_admin' => $request->input('catatan_admin', 'Pengajuan disetujui, wilayah dan akun admin resmi diaktifkan.'),
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);

        AuditLogger::log(
            'APPROVE_PENGAJUAN_RW',
            'Super Admin Platform',
            $pengajuan->id,
            "Super Admin (" . Auth::user()->name . ") menyetujui pengajuan RW {$pengajuan->rw_number} (#{$pengajuan->nomor_pengajuan}) dan mengaktifkan wilayah",
            null,
            $wilayah->toArray(),
            '000',
            'success',
            'APPROVAL'
        );

        return back()->with('success', "Pengajuan RW {$pengajuan->rw_number} berhasil disetujui.{$tempPasswordMsg}");
    }

    public function requestRevision(Request $request, $id)
    {
        $pengajuan = PengajuanRw::findOrFail($id);

        $validated = $request->validate([
            'catatan_revisi' => 'required|string|min:5',
        ]);

        $pengajuan->update([
            'status' => 'revisi',
            'catatan_revisi' => $validated['catatan_revisi'],
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);

        AuditLogger::log(
            'REVISION_PENGAJUAN_RW',
            'Super Admin Platform',
            $pengajuan->id,
            "Super Admin (" . Auth::user()->name . ") meminta revisi pengajuan RW {$pengajuan->rw_number}: {$validated['catatan_revisi']}",
            null,
            ['status' => 'revisi', 'catatan_revisi' => $validated['catatan_revisi']],
            '000',
            'warning',
            'REVISION'
        );

        return back()->with('success', "Catatan revisi berhasil disimpan dan dikirimkan ke pemohon RW {$pengajuan->rw_number}.");
    }

    public function rejectPengajuan(Request $request, $id)
    {
        $pengajuan = PengajuanRw::findOrFail($id);

        $validated = $request->validate([
            'alasan_penolakan' => 'required|string|min:5',
        ]);

        $pengajuan->update([
            'status' => 'ditolak',
            'alasan_penolakan' => $validated['alasan_penolakan'],
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);

        AuditLogger::log(
            'REJECT_PENGAJUAN_RW',
            'Super Admin Platform',
            $pengajuan->id,
            "Super Admin (" . Auth::user()->name . ") menolak pengajuan RW {$pengajuan->rw_number}: {$validated['alasan_penolakan']}",
            null,
            ['status' => 'ditolak', 'alasan_penolakan' => $validated['alasan_penolakan']],
            '000',
            'danger',
            'REJECTION'
        );

        return back()->with('success', "Pengajuan RW {$pengajuan->rw_number} telah ditolak.");
    }
}
