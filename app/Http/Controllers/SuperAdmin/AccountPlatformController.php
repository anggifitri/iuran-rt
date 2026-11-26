<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SuperAdminSensitiveAccessLog;
use App\Models\User;
use App\Models\Warga;
use App\Models\WilayahRt;
use App\Models\WilayahRw;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AccountPlatformController extends Controller
{
    /**
     * Display accounts and access management center for Super Admin.
     * PRINCIPLE: Super Admin strictly manages administrative accounts (Admin RW and Admin RT).
     * Citizen (Warga) accounts are monitored purely in aggregate.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $role = $request->query('role');
        $rt = $request->query('rt');
        $status = $request->query('status');
        $sort = $request->query('sort', 'created_at');
        $direction = $request->query('direction', 'desc');

        $availableRts = ['006', '007', '008', '009', '010'];

        // 1. Query ONLY Active Pengurus Accounts of RW 018 (Admin RW on RT 000 & Admin RT on RT 006-010)
        $query = User::where('rw_number', '018')
            ->where(function ($q) use ($availableRts) {
                // Admin RW
                $q->where(function ($rwQ) {
                    $rwQ->where(function ($r) {
                        $r->where('role', 'admin_rw')->orWhere('role', 'admin');
                    })->where('rt_number', '000');
                })
                // Admin RT
                ->orWhere(function ($rtQ) use ($availableRts) {
                    $rtQ->where(function ($r) {
                        $r->where('role', 'admin_rt')->orWhere('role', 'admin');
                    })->whereIn('rt_number', $availableRts);
                });
            });

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($role) {
            if ($role === 'admin_rw') {
                $query->where('rt_number', '000');
            } elseif ($role === 'admin_rt') {
                $query->whereIn('rt_number', $availableRts);
            }
        }

        if ($rt !== null && $rt !== '') {
            $query->where('rt_number', $rt);
        }

        if ($status && in_array($status, ['active', 'suspended', 'inactive'])) {
            $query->where('status', $status);
        }

        // Allowed sort columns
        if (in_array($sort, ['name', 'email', 'created_at', 'last_active_at', 'status', 'rt_number'])) {
            $query->orderBy($sort, $direction === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $pengurusList = $query->paginate(15)->withQueryString();

        // 2. Aggregate Statistics - Strictly in sync with 6 active Pengurus (Admin RW: 1, Admin RT: 5)
        $pengurusBase = User::where('rw_number', '018')
            ->where(function ($q) use ($availableRts) {
                $q->where(function ($rwQ) {
                    $rwQ->where(function ($r) {
                        $r->where('role', 'admin_rw')->orWhere('role', 'admin');
                    })->where('rt_number', '000');
                })->orWhere(function ($rtQ) use ($availableRts) {
                    $rtQ->where(function ($r) {
                        $r->where('role', 'admin_rt')->orWhere('role', 'admin');
                    })->whereIn('rt_number', $availableRts);
                });
            });

        $totalPengurus = (clone $pengurusBase)->count();
        $totalAdminRw = (clone $pengurusBase)->where('rt_number', '000')->count();
        $totalAdminRt = (clone $pengurusBase)->whereIn('rt_number', $availableRts)->count();
        $pengurusAktif = (clone $pengurusBase)->where('status', 'active')->count();
        $pengurusNonaktif = (clone $pengurusBase)->whereIn('status', ['suspended', 'inactive'])->count();

        // Citizen (Warga) Aggregates ONLY (Zero citizen identity exposed)
        $totalWarga = Warga::where('rw_number', '018')->count();
        $totalAkunWarga = User::where('role', 'warga')->count();
        $wargaAktif = User::where('role', 'warga')->where('status', 'active')->count();
        $wargaBelumVerifikasi = Warga::where('rw_number', '018')->where('status_verifikasi', '!=', 'terverifikasi')->count();
        
        $wargaDistribution = Warga::where('rw_number', '018')
            ->selectRaw('rt_number, count(*) as count')
            ->groupBy('rt_number')
            ->pluck('count', 'rt_number')
            ->toArray();

        // 3. Governance Hierarchy Data for RW 018
        $rw = WilayahRw::where('rw_number', '018')->first();
        $adminRwUser = $rw && $rw->admin_user_id 
            ? User::find($rw->admin_user_id) 
            : User::where('rw_number', '018')->where('rt_number', '000')->first();

        $rts = WilayahRt::where('rw_number', '018')->with('adminUser')->orderBy('rt_number')->get();

        // 4. Audit Trail for Account & Access Events (Filtered strictly, no login/logout/role_switch leakage)
        $auditLogs = AuditLog::where(function ($q) {
            $q->where('action_type', 'like', '%PENGURUS%')
              ->orWhere('module', 'Manajemen Akun')
              ->orWhereIn('action_type', [
                  'CREATE_PENGURUS_ACCOUNT',
                  'UPDATE_PENGURUS_ACCOUNT',
                  'TOGGLE_PENGURUS_STATUS',
                  'RESET_PENGURUS_PASSWORD',
                  'REVOKE_PENGURUS_SESSIONS',
                  'REASSIGN_PENGURUS',
                  'ASSIGN_ADMIN_RW',
                  'ASSIGN_ADMIN_RT',
                  'account_created',
                  'account_updated',
                  'account_deactivated',
                  'account_activated',
                  'password_reset',
                  'role_assigned',
                  'permission_updated'
              ]);
        })
        ->whereNotIn('event', ['login', 'logout', 'role_switch', 'role_switch_exit', 'role_switch_mode_toggle', 'session_expired'])
        ->whereNotIn('action_type', ['LOGIN', 'LOGOUT', 'ROLE_SWITCH'])
        ->latest()
        ->limit(8)
        ->get();

        // Alias for backwards view compatibility
        $users = $pengurusList;
        $totalUsers = $totalPengurus;
        $totalActive = $pengurusAktif;
        $totalSuspended = $pengurusNonaktif;

        return view('super_admin.accounts.index', compact(
            'pengurusList',
            'users',
            'totalPengurus',
            'totalAdminRw',
            'totalAdminRt',
            'pengurusAktif',
            'pengurusNonaktif',
            'totalWarga',
            'totalAkunWarga',
            'wargaAktif',
            'wargaBelumVerifikasi',
            'wargaDistribution',
            'rw',
            'adminRwUser',
            'rts',
            'availableRts',
            'auditLogs',
            'totalUsers',
            'totalActive',
            'totalSuspended',
            'search',
            'role',
            'rt',
            'status',
            'sort',
            'direction'
        ));
    }

    /**
     * Store a newly created administrative (pengurus) account.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin_rw,admin_rt',
            'rt_number' => 'required_if:role,admin_rt|nullable|in:006,007,008,009,010',
            'phone' => 'nullable|string|max:25',
            'password' => 'required|string|min:8',
        ], [
            'email.unique' => 'Email ini sudah terdaftar pada akun lain di platform.',
            'password.min' => 'Kata sandi minimal harus 8 karakter.',
            'role.in' => 'Peran yang diizinkan hanya Admin RW dan Admin RT.',
            'rt_number.required_if' => 'Penugasan nomor RT wajib dipilih untuk Admin RT.',
            'rt_number.in' => 'Nomor RT harus merupakan RT operasional aktif RW 018 (RT 006 s/d RT 010).',
        ]);

        $actualRole = 'admin';
        $rtNumber = $validated['role'] === 'admin_rw' ? '000' : ($validated['rt_number'] ?: '006');

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $actualRole,
            'status' => 'active',
            'rw_number' => '018',
            'rt_number' => $rtNumber,
            'phone' => $validated['phone'] ?? null,
            'email_verified_at' => now(),
        ]);

        // Sync with WilayahRt or WilayahRw if assigned
        if ($validated['role'] === 'admin_rt') {
            WilayahRt::where('rw_number', '018')
                ->where('rt_number', $rtNumber)
                ->update(['admin_user_id' => $user->id]);
        } elseif ($validated['role'] === 'admin_rw') {
            WilayahRw::where('rw_number', '018')
                ->update(['admin_user_id' => $user->id]);
        }

        AuditLogger::log(
            'CREATE_PENGURUS_ACCOUNT',
            'Manajemen Akun',
            $user->id,
            "Super Admin (" . Auth::user()->name . ") membuat akun pengurus baru: {$user->name} ({$user->email}) sebagai " . $user->role_label,
            null,
            $user->only(['name', 'email', 'role', 'rw_number', 'rt_number', 'status']),
            $user->rt_number,
            'success',
            'CREATE'
        );

        return back()->with('success', "Akun pengurus {$user->name} ({$user->role_label}) berhasil dibuat.");
    }

    /**
     * Show details of a specific pengurus account (JSON/modal endpoint).
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'warga' || $user->isSuperAdmin()) {
            abort(403, 'Akses ditolak: Detail akun non-pengurus tidak dapat diakses melalui modul ini.');
        }

        $userLogs = AuditLog::where(function ($q) use ($user) {
            $q->where('record_id', $user->id)
              ->orWhere('user_id', $user->id);
        })->latest()->limit(5)->get();

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?: '-',
                'role' => $user->role,
                'role_label' => $user->role_label,
                'rw_number' => $user->rw_number ?: '018',
                'rt_number' => $user->rt_number ?: '000',
                'status' => $user->status,
                'profile_photo_url' => $user->profile_photo_url,
                'created_at' => $user->created_at ? $user->created_at->format('d M Y, H:i') : '-',
                'last_active_at' => $user->last_active_at ? $user->last_active_at->diffForHumans() : 'Belum tercatat',
            ],
            'recent_logs' => $userLogs,
        ]);
    }

    /**
     * Update an administrative user details.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Guardrail: Tolak HTTP 403 jika target adalah warga atau Super Admin
        if ($user->role === 'warga' || $user->isSuperAdmin()) {
            abort(403, 'Akses ditolak: Modul ini dibatasi khusus untuk pengelolaan akun pengurus aktif (Admin RW & Admin RT).');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$id}",
            'role' => 'required|in:admin_rw,admin_rt',
            'rt_number' => 'required_if:role,admin_rt|nullable|in:000,006,007,008,009,010',
            'phone' => 'nullable|string|max:25',
            'status' => 'required|in:active,suspended,inactive',
        ]);

        $oldValues = $user->only(['name', 'email', 'role', 'rw_number', 'rt_number', 'status']);

        // Normalize operational admin roles
        $targetRole = $validated['role'];
        if ($targetRole === 'admin_rw') {
            $user->role = 'admin';
            $user->rt_number = '000';
        } elseif ($targetRole === 'admin_rt') {
            $user->role = 'admin';
            $user->rt_number = $validated['rt_number'] ?: '006';
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];
        $user->status = $validated['status'];
        $user->save();

        // Sync with WilayahRt or WilayahRw if RT changed
        if ($targetRole === 'admin_rt') {
            WilayahRt::where('rw_number', '018')
                ->where('rt_number', $user->rt_number)
                ->update(['admin_user_id' => $user->id]);
        } elseif ($targetRole === 'admin_rw') {
            WilayahRw::where('rw_number', '018')
                ->update(['admin_user_id' => $user->id]);
        }

        AuditLogger::log(
            'UPDATE_PENGURUS_ACCOUNT',
            'Manajemen Akun',
            $user->id,
            "Super Admin (" . Auth::user()->name . ") memperbarui data akun pengurus {$user->name} ({$user->email})",
            $oldValues,
            $user->only(['name', 'email', 'role', 'rw_number', 'rt_number', 'status']),
            $user->rt_number,
            'success',
            'UPDATE'
        );

        return back()->with('success', "Akun pengurus {$user->name} berhasil diperbarui.");
    }

    /**
     * Toggle account status (Active / Suspended / Inactive).
     * Non-destructive: historical administrative records are preserved 100%.
     */
    public function toggleStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menangguhkan akun Anda sendiri.');
        }

        // Guardrail: Tolak HTTP 403 jika target adalah warga atau Super Admin
        if ($user->role === 'warga' || $user->isSuperAdmin()) {
            abort(403, 'Akses ditolak: Pengelolaan status akun warga dan Super Admin dilarang melalui modul ini.');
        }

        $newStatus = $user->status === 'active' ? 'suspended' : 'active';
        $oldStatus = $user->status;
        $user->status = $newStatus;
        $user->save();

        AuditLogger::log(
            'TOGGLE_PENGURUS_STATUS',
            'Manajemen Akun',
            $user->id,
            "Super Admin (" . Auth::user()->name . ") mengubah status akun pengurus {$user->name} ({$user->email}) dari {$oldStatus} menjadi {$newStatus}",
            ['old_status' => $oldStatus],
            ['new_status' => $newStatus],
            $user->rt_number,
            'success',
            'STATUS_CHANGE'
        );

        $actionText = $newStatus === 'active' ? 'diaktifkan kembali' : 'ditangguhkan (nonaktif)';
        return back()->with('success', "Akun pengurus {$user->name} berhasil {$actionText}. Riwayat kerja administratif tetap terjaga.");
    }

    /**
     * Reset password for an administrative pengurus account securely.
     */
    public function resetPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Guardrail: Tolak HTTP 403 jika target adalah warga atau Super Admin
        if ($user->role === 'warga' || $user->isSuperAdmin()) {
            abort(403, 'Akses ditolak: Reset kata sandi akun warga dan Super Admin dilarang melalui modul ini.');
        }

        $validated = $request->validate([
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'new_password.min' => 'Kata sandi baru minimal harus 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $user->password = Hash::make($validated['new_password']);
        $user->save();

        AuditLogger::log(
            'RESET_PENGURUS_PASSWORD',
            'Manajemen Akun',
            $user->id,
            "Super Admin (" . Auth::user()->name . ") mereset kata sandi akun pengurus {$user->name} ({$user->email}) secara aman",
            null,
            null,
            $user->rt_number,
            'success',
            'PASSWORD_RESET'
        );

        return back()->with('success', "Kata sandi akun pengurus {$user->name} berhasil diperbarui.");
    }

    /**
     * Revoke active user sessions (Force Logout).
     */
    public function revokeSession(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat mencabut sesi Anda sendiri melalui menu ini.');
        }

        // Guardrail: Tolak HTTP 403 jika target adalah warga atau Super Admin
        if ($user->role === 'warga' || $user->isSuperAdmin()) {
            abort(403, 'Akses ditolak: Pencabutan sesi akun warga dan Super Admin dilarang melalui modul ini.');
        }

        DB::table('sessions')->where('user_id', $user->id)->delete();

        AuditLogger::log(
            'REVOKE_PENGURUS_SESSIONS',
            'Manajemen Akun',
            $user->id,
            "Super Admin (" . Auth::user()->name . ") mencabut seluruh sesi login aktif untuk akun {$user->name} ({$user->email})",
            null,
            null,
            $user->rt_number,
            'success',
            'SESSION_REVOKED'
        );

        return back()->with('success', "Seluruh sesi aktif untuk akun {$user->name} berhasil dicabut. Pengguna wajib login ulang.");
    }

    /**
     * Reassign an administrative user to a different role or RT.
     */
    public function reassign(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'target_role' => 'required|in:admin_rw,admin_rt',
            'target_rt' => 'required_if:target_role,admin_rt|nullable|in:006,007,008,009,010',
        ]);

        $user = User::findOrFail($validated['user_id']);

        if ($user->role === 'warga' || $user->isSuperAdmin()) {
            abort(403, 'Akses ditolak: Penugasan role pengurus hanya dapat dilakukan untuk akun pengurus yang sah.');
        }

        $oldRole = $user->role_label;
        $oldRt = $user->rt_number;

        if ($validated['target_role'] === 'admin_rw') {
            $user->role = 'admin';
            $user->rt_number = '000';
            $user->rw_number = '018';
            $user->save();

            WilayahRw::where('rw_number', '018')->update(['admin_user_id' => $user->id]);
        } else {
            $user->role = 'admin';
            $user->rt_number = $validated['target_rt'] ?: '006';
            $user->rw_number = '018';
            $user->save();

            WilayahRt::where('rw_number', '018')
                ->where('rt_number', $user->rt_number)
                ->update(['admin_user_id' => $user->id]);
        }

        AuditLogger::log(
            'REASSIGN_PENGURUS',
            'Manajemen Akun',
            $user->id,
            "Super Admin (" . Auth::user()->name . ") memindahkan penugasan {$user->name} dari {$oldRole} menjadi " . $user->role_label,
            ['old_rt' => $oldRt, 'old_role' => $oldRole],
            ['new_rt' => $user->rt_number, 'new_role' => $user->role_label],
            $user->rt_number,
            'success',
            'REASSIGNMENT'
        );

        return back()->with('success', "Penugasan {$user->name} berhasil dialihkan ke {$user->role_label}. Seluruh riwayat administrasi sebelumnya tetap tersimpan utuh.");
    }

    /**
     * Unlock sensitive data (NIK, Phone, Email) with Super Admin password re-authentication.
     */
    public function unlockSensitive(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'super_admin_password' => 'required|string',
            'access_reason' => 'required|string|min:5|max:500',
        ]);

        $superAdmin = Auth::user();

        if (!Hash::check($validated['super_admin_password'], $superAdmin->password)) {
            AuditLogger::log(
                'SENSITIVE_DATA_ACCESS_FAILED',
                'Super Admin Platform',
                $validated['user_id'],
                "Percobaan pembukaan data sensitif gagal: Kata sandi Super Admin salah",
                null,
                ['reason' => $validated['access_reason']],
                null,
                'failed',
                'SECURITY_ALERT',
                true
            );

            return response()->json([
                'success' => false,
                'message' => 'Kata sandi Super Admin salah. Verifikasi dibatalkan.',
            ], 422);
        }

        $targetUser = User::findOrFail($validated['user_id']);

        if ($targetUser->role === 'warga' || $targetUser->isSuperAdmin()) {
            abort(403, 'Akses ditolak: Pembukaan data sensitif melalui modul ini dibatasi hanya untuk akun pengurus.');
        }

        SuperAdminSensitiveAccessLog::create([
            'user_id' => $superAdmin->id,
            'target_user_id' => $targetUser->id,
            'target_type' => 'user_profile_sensitive',
            'target_identifier' => "ID: {$targetUser->id} | {$targetUser->email}",
            'reason' => $validated['access_reason'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        AuditLogger::log(
            'SENSITIVE_DATA_UNLOCKED',
            'Super Admin Platform',
            $targetUser->id,
            "Super Admin (" . $superAdmin->name . ") membuka sensor data sensitif akun {$targetUser->name} ({$targetUser->email}). Alasan: {$validated['access_reason']}",
            null,
            ['reason' => $validated['access_reason']],
            $targetUser->rt_number,
            'warning',
            'SENSITIVE_ACCESS'
        );

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $targetUser->id,
                'name' => $targetUser->name,
                'nik' => $targetUser->nik ?: 'Tidak terdaftar',
                'phone' => $targetUser->phone ?: 'Tidak terdaftar',
                'email' => $targetUser->email,
                'no_kk' => $targetUser->no_kk ?: 'Tidak terdaftar',
                'address' => $targetUser->address ?: 'Tidak terdaftar',
            ],
            'message' => 'Otorisasi berhasil. Data lengkap dibuka.',
        ]);
    }

    /**
     * Display sensitive data access logs history.
     */
    public function sensitiveLogs(Request $request)
    {
        $logs = SuperAdminSensitiveAccessLog::with(['user', 'targetUser'])
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('super_admin.accounts.sensitive_logs', compact('logs'));
    }
}
