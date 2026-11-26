<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SuperAdminProfileController extends Controller
{
    /**
     * Tampilkan Halaman Profil Super Admin Terpadu (6 Bagian)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $activeTab = $request->get('tab', 'personal');
        $validTabs = ['personal', 'account', 'security', 'notifications', 'activity', 'preferences'];
        if (!in_array($activeTab, $validTabs, true)) {
            $activeTab = 'personal';
        }

        // 1. Sesi Login Aktif (dari tabel sessions)
        $currentSessionId = session()->getId();
        $sessions = DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderByDesc('last_activity')
            ->get()
            ->map(function ($s) use ($currentSessionId) {
                return (object) [
                    'id' => $s->id,
                    'ip_address' => $s->ip_address,
                    'user_agent' => $s->user_agent,
                    'device_name' => AuditLogger::parseUserAgent($s->user_agent),
                    'last_activity' => Carbon::createFromTimestamp($s->last_activity)->timezone('Asia/Jakarta'),
                    'is_current' => ($s->id === $currentSessionId),
                ];
            });

        // 2. Riwayat Keamanan Terkini (dari audit_logs)
        $securityLogs = AuditLog::where('user_id', $user->id)
            ->where(function ($q) {
                $q->whereIn('event', ['login', 'logout', 'login_failed', 'unauthorized', 'password_change', 'security_alert', 'verify'])
                  ->orWhereIn('action_type', ['SECURITY_ALERT', 'LOGOUT', 'SECURITY_CHANGE', 'VERIFY']);
            })
            ->latest('created_at')
            ->take(8)
            ->get();

        // 3. Aktivitas Saya (Audit Logs Super Admin dengan Filter)
        $actQuery = AuditLog::where('user_id', $user->id);

        if ($request->filled('act_module') && $request->act_module !== 'semua') {
            $actQuery->where('module', $request->act_module);
        }
        if ($request->filled('act_type') && $request->act_type !== 'semua') {
            $actQuery->where('action_type', $request->act_type);
        }
        if ($request->filled('act_date')) {
            $actQuery->whereDate('created_at', $request->act_date);
        }

        $activities = (clone $actQuery)->latest('created_at')->paginate(10, ['*'], 'act_page')->withQueryString();

        // Opsi filter dinamis dari riwayat audit pengguna
        $modulesList = AuditLog::where('user_id', $user->id)->distinct()->pluck('module')->filter()->values();
        $actionTypesList = AuditLog::where('user_id', $user->id)->distinct()->pluck('action_type')->filter()->values();

        // 4. Preferensi Notifikasi Terkonfigurasi
        $defaultNotifPrefs = [
            'notif_login_activity' => true,
            'notif_account_change' => true,
            'notif_admin_access_change' => true,
            'notif_security_alert' => true, // Kebijakan sistem: wajib aktif
            'notif_system_outage' => true,
            'notif_backup_failure' => true,
            'notif_platform_maintenance' => true,
            'notif_platform_update' => true,
            'channel_in_app' => true,
            'channel_email' => true,
        ];
        $notifPrefs = array_merge($defaultNotifPrefs, $user->notification_preferences ?: []);

        // 5. Informasi Akun & Waktu Login Terakhir
        $lastLoginLog = AuditLog::where('user_id', $user->id)
            ->where('event', 'login')
            ->latest('created_at')
            ->first();
        $lastLoginFormatted = $user->last_login_at 
            ? $user->last_login_at->timezone('Asia/Jakarta')->format('d M Y, H:i') . ' WIB'
            : ($lastLoginLog ? $lastLoginLog->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') . ' WIB' : 'Sesi Aktif Sekarang');

        return view('super_admin.profile.index', compact(
            'user',
            'activeTab',
            'sessions',
            'securityLogs',
            'activities',
            'modulesList',
            'actionTypesList',
            'notifPrefs',
            'lastLoginFormatted'
        ));
    }

    /**
     * Bagian A: Perbarui Informasi Pribadi & Foto Profil
     */
    public function updatePersonal(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'display_name' => 'nullable|string|max:100',
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
            'bio' => 'nullable|string|max:500',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'phone.unique' => 'Nomor telepon ini sudah digunakan oleh akun lain.',
            'profile_photo.max' => 'Ukuran foto profil maksimal 3 MB.',
            'profile_photo.image' => 'Berkas harus berupa gambar (JPG, PNG, atau WebP).',
        ]);

        $oldValues = [
            'name' => $user->name,
            'display_name' => $user->display_name,
            'phone' => $user->phone,
            'bio' => $user->bio,
        ];

        // Handle Photo Upload
        if ($request->hasFile('profile_photo')) {
            $photo = $request->file('profile_photo');
            $path = $photo->store('profile_photos', 'public');

            // Hapus foto lama jika tersimpan di disk public
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $user->profile_photo = $path;
        }

        $user->name = $validated['name'];
        $user->display_name = $validated['display_name'] ?? $validated['name'];
        $user->phone = $validated['phone'];
        $user->bio = $validated['bio'] ?? null;
        $user->save();

        AuditLogger::log(
            event: 'update',
            module: 'Profil Super Admin',
            recordId: $user->id,
            description: "Super Admin '{$user->name}' memperbarui informasi pribadi dan biodata profil HQ.",
            oldValues: $oldValues,
            newValues: [
                'name' => $user->name,
                'display_name' => $user->display_name,
                'phone' => $user->phone,
                'bio' => $user->bio,
            ],
            status: 'success',
            actionType: 'UPDATE'
        );

        return redirect()->route('super_admin.profile.index', ['tab' => 'personal'])
            ->with('success', 'Informasi pribadi Super Admin berhasil disimpan.');
    }

    /**
     * Bagian B: Perbarui Informasi Kredensial Akun (Email dengan Proteksi Password)
     */
    public function updateAccount(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'current_password' => 'required|string',
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Alamat email ini sudah terdaftar pada akun lain.',
            'current_password.required' => 'Kata sandi saat ini wajib diisi untuk konfirmasi keamanan.',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            AuditLogger::log(
                event: 'security_alert',
                module: 'Profil Super Admin',
                recordId: $user->id,
                description: "Percobaan perubahan email Super Admin DITOLAK: kata sandi verifikasi tidak sesuai.",
                status: 'suspicious',
                actionType: 'SECURITY_ALERT',
                isSuspicious: true
            );

            return back()->withErrors(['current_password' => 'Kata sandi saat ini yang Anda masukkan tidak sesuai.'])->withInput();
        }

        $oldEmail = $user->email;
        $user->email = $validated['email'];
        $user->email_verified_at = now(); // Diverifikasi dengan re-autentikasi password
        $user->save();

        AuditLogger::log(
            event: 'update',
            module: 'Profil Super Admin',
            recordId: $user->id,
            description: "Super Admin '{$user->name}' mengubah email login akun dari '{$oldEmail}' menjadi '{$user->email}'.",
            oldValues: ['email' => $oldEmail],
            newValues: ['email' => $user->email],
            status: 'success',
            actionType: 'SECURITY_CHANGE'
        );

        return redirect()->route('super_admin.profile.index', ['tab' => 'account'])
            ->with('success', 'Alamat email akun Super Admin berhasil diperbarui dan diverifikasi.');
    }

    /**
     * Bagian C.1: Ubah Kata Sandi Akun
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'Kata sandi lama wajib diisi.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            AuditLogger::log(
                event: 'security_alert',
                module: 'Profil Super Admin',
                recordId: $user->id,
                description: "Percobaan ubah kata sandi Super Admin GAGAL: kata sandi lama salah.",
                status: 'suspicious',
                actionType: 'SECURITY_ALERT',
                isSuspicious: true
            );

            return back()->withErrors(['current_password' => 'Kata sandi lama yang Anda masukkan tidak sesuai.']);
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        AuditLogger::logPasswordChange($user);

        return redirect()->route('super_admin.profile.index', ['tab' => 'security'])
            ->with('success', 'Kata sandi akun Super Admin berhasil diperbarui dengan aman.');
    }

    /**
     * Bagian C.4: Cabut / Logout Sesi Perangkat Tertentu
     */
    public function revokeSession(Request $request, $id)
    {
        $user = Auth::user();

        if ($id === $request->session()->getId() || $id === session()->getId()) {
            return redirect()->route('super_admin.profile.index', ['tab' => 'security'])
                ->with('error', 'Anda tidak dapat menghentikan sesi yang sedang Anda gunakan saat ini dari tombol ini.');
        }

        $deleted = DB::table('sessions')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->delete();

        if ($deleted) {
            AuditLogger::log(
                event: 'logout',
                module: 'Profil Super Admin',
                recordId: $user->id,
                description: "Super Admin '{$user->name}' mencabut sesi perangkat aktif ID: {$id}.",
                status: 'success',
                actionType: 'LOGOUT'
            );

            return redirect()->route('super_admin.profile.index', ['tab' => 'security'])
                ->with('success', 'Sesi perangkat berhasil dihentikan.');
        }

        return redirect()->route('super_admin.profile.index', ['tab' => 'security'])
            ->with('error', 'Sesi perangkat tidak ditemukan atau sudah tidak aktif.');
    }

    /**
     * Bagian C.4: Logout Seluruh Perangkat Lain (Logout Other Devices)
     */
    public function revokeOtherSessions(Request $request)
    {
        $request->validate([
            'confirm_password' => 'required|string',
        ], [
            'confirm_password.required' => 'Kata sandi konfirmasi wajib diisi.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->confirm_password, $user->password)) {
            return back()->withErrors(['confirm_password' => 'Kata sandi konfirmasi tidak sesuai.'])->withInput();
        }

        // 1. Laravel Auth Logout Other Devices
        Auth::logoutOtherDevices($request->confirm_password);

        // 2. Bersihkan tabel database sessions kecuali sesi saat ini
        $currentSessionId = session()->getId();
        DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', $currentSessionId)
            ->delete();

        AuditLogger::log(
            event: 'security_alert',
            module: 'Profil Super Admin',
            recordId: $user->id,
            description: "Super Admin '{$user->name}' mengakhiri seluruh sesi login dari perangkat lain.",
            status: 'success',
            actionType: 'LOGOUT'
        );

        return redirect()->route('super_admin.profile.index', ['tab' => 'security'])
            ->with('success', 'Seluruh sesi login Super Admin di perangkat lain berhasil diputuskan.');
    }

    /**
     * Bagian D: Simpan Preferensi Notifikasi Pribadi Super Admin
     */
    public function updateNotifications(Request $request)
    {
        $user = Auth::user();

        $prefs = [
            'notif_login_activity' => $request->boolean('notif_login_activity'),
            'notif_account_change' => $request->boolean('notif_account_change'),
            'notif_admin_access_change' => $request->boolean('notif_admin_access_change'),
            'notif_security_alert' => true, // Kebijakan keamanan platform: selalu aktif
            'notif_system_outage' => $request->boolean('notif_system_outage'),
            'notif_backup_failure' => $request->boolean('notif_backup_failure'),
            'notif_platform_maintenance' => $request->boolean('notif_platform_maintenance'),
            'notif_platform_update' => $request->boolean('notif_platform_update'),
            'channel_in_app' => $request->boolean('channel_in_app'),
            'channel_email' => $request->boolean('channel_email'),
        ];

        $user->notification_preferences = $prefs;
        $user->save();

        AuditLogger::log(
            event: 'update',
            module: 'Profil Super Admin',
            recordId: $user->id,
            description: "Super Admin '{$user->name}' memperbarui preferensi notifikasi sistem HQ.",
            status: 'success',
            actionType: 'UPDATE'
        );

        return redirect()->route('super_admin.profile.index', ['tab' => 'notifications'])
            ->with('success', 'Preferensi notifikasi Super Admin berhasil disimpan.');
    }

    /**
     * Bagian F: Simpan Preferensi Tampilan (Day / Night Mode)
     */
    public function updatePreferences(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'theme_preference' => 'required|in:light,dark,system',
        ]);

        $user->theme_preference = $validated['theme_preference'];
        $user->save();

        AuditLogger::log(
            event: 'update',
            module: 'Profil Super Admin',
            recordId: $user->id,
            description: "Super Admin '{$user->name}' mengatur preferensi tema tampilan ke '{$validated['theme_preference']}'.",
            status: 'success',
            actionType: 'UPDATE'
        );

        return redirect()->route('super_admin.profile.index', ['tab' => 'preferences'])
            ->with('success', 'Preferensi tema tampilan berhasil disimpan.');
    }
}
