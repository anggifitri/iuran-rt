<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Pembayaran;
use App\Models\Surat;
use App\Models\User;
use App\Models\Warga;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class ProfileController extends Controller
{
    /**
     * Resolusi akun pengguna dan data kependudukan aktif (mendukung Role Switcher).
     * Mengembalikan array: [User $displayUser, ?Warga $warga, bool $isRoleSwitched, bool $isPreviewMode]
     */
    protected function resolveCurrentProfileContext(User $authUser): array
    {
        $isRoleSwitched = $authUser->isRoleSwitched();
        $isPreviewMode = $authUser->isRoleSwitchPreview();

        if (!$isRoleSwitched) {
            $warga = $authUser->warga
                ?? Warga::where('nik', $authUser->nik)->first()
                ?? Warga::where('nama', $authUser->name)->first();
            return [$authUser, $warga, false, false];
        }

        $ctx = $authUser->getRoleSwitchContext() ?? [];
        $targetRole = $ctx['target_role'] ?? 'warga';
        $targetRt = $ctx['target_rt'] ?? null;
        $targetUserId = $ctx['target_user_id'] ?? null;
        $targetWargaId = $ctx['target_warga_id'] ?? null;

        $simulatedUser = null;
        $warga = null;

        // 1. Coba dari target_user_id
        if (!empty($targetUserId)) {
            $simulatedUser = User::find($targetUserId);
        }

        // 2. Coba dari target_warga_id
        if (!empty($targetWargaId)) {
            $warga = Warga::find($targetWargaId);
            if ($warga && $warga->user_id && !$simulatedUser) {
                $simulatedUser = User::find($warga->user_id);
            }
        }

        // 3. Fallback pencarian sesuai role jika belum ditemukan
        if (!$simulatedUser) {
            if ($targetRole === 'admin_rt') {
                $simulatedUser = User::where(function ($q) {
                    $q->where('role', 'admin_rt')->orWhere('role', 'admin');
                })->where('rt_number', $targetRt)->first();
            } elseif ($targetRole === 'admin_rw') {
                $simulatedUser = User::where(function ($q) {
                    $q->where('role', 'admin_rw')->orWhere('role', 'admin');
                })->where('rt_number', '000')->first();
            } elseif ($targetRole === 'warga') {
                if (!$warga) {
                    $warga = $authUser->resolveWarga();
                }
                if ($warga && $warga->user_id) {
                    $simulatedUser = User::find($warga->user_id);
                }
            }
        }

        // 4. Jika tetap tidak ada user account (misal warga tanpa akun login), buat instance User virtual
        if (!$simulatedUser && $warga) {
            $simulatedUser = new User([
                'name' => $warga->nama,
                'email' => $warga->email ?? (strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $warga->nama)) . '@warga.nexanest.test'),
                'role' => 'warga',
                'rt_number' => $warga->rt_number,
                'rw_number' => $warga->rw_number ?? '018',
                'nik' => $warga->nik,
                'no_kk' => $warga->no_kk,
                'phone' => $warga->nomor_hp ?? $warga->phone ?? null,
                'address' => $warga->alamat,
                'profile_photo' => $warga->profile_photo,
            ]);
            $simulatedUser->id = $warga->id;
            $simulatedUser->exists = false;
        }

        if (!$simulatedUser) {
            $simulatedUser = $authUser;
        }

        if (!$warga) {
            $warga = $simulatedUser->warga
                ?? Warga::where('nik', $simulatedUser->nik)->first()
                ?? Warga::where('nama', $simulatedUser->name)->first();
        }

        return [$simulatedUser, $warga, true, $isPreviewMode];
    }

    /**
     * Show User Profile Page with 6 Integrated Tabs
     */
    public function edit(Request $request)
    {
        $authUser = Auth::user();
        if (!$authUser) {
            return redirect()->route('login');
        }

        [$user, $warga, $isRoleSwitched, $isPreviewMode] = $this->resolveCurrentProfileContext($authUser);

        $activeTab = $request->get('tab', 'personal');
        if (!in_array($activeTab, ['personal', 'account', 'security', 'notifications', 'activity', 'preferences'])) {
            $activeTab = 'personal';
        }

        // 2. Fetch User Activities (Scoped ke akun yang sedang dilihat)
        $targetUserId = $user->id;
        $actQuery = AuditLog::query();
        if ($targetUserId) {
            $actQuery->where(function ($q) use ($targetUserId, $warga) {
                $q->where('user_id', $targetUserId);
                if ($warga) {
                    $q->orWhere('record_id', $warga->id);
                }
            });
        } elseif ($warga) {
            $actQuery->where('record_id', $warga->id);
        }

        if ($request->filled('act_category') && $request->act_category !== 'semua') {
            $cat = $request->act_category;
            if ($cat === 'login') {
                $actQuery->whereIn('event', ['login', 'logout', 'login_failed']);
            } elseif ($cat === 'surat') {
                $actQuery->where('module', 'Penerbitan Surat');
            } elseif ($cat === 'iuran') {
                $actQuery->where('module', 'Iuran Warga');
            } elseif ($cat === 'pengaduan') {
                $actQuery->where('module', 'Manajemen Aset & Pengaduan');
            } elseif ($cat === 'pengumuman') {
                $actQuery->where('module', 'Pusat Pengumuman');
            } elseif ($cat === 'profil') {
                $actQuery->where('module', 'Profil Saya');
            }
        }

        if ($request->filled('act_date')) {
            $actQuery->whereDate('created_at', $request->act_date);
        }

        $activities = (clone $actQuery)->latest('created_at')->paginate(12, ['*'], 'act_page')->withQueryString();

        // 3. User Login Sessions (Last 8 login/logout events)
        $loginHistory = $targetUserId ? AuditLog::where('user_id', $targetUserId)
            ->whereIn('event', ['login', 'logout', 'login_failed'])
            ->latest('created_at')
            ->take(8)
            ->get() : collect();

        // 4. Current Device & Network Metadata
        $currentIp = $request->ip();
        $currentUserAgent = $request->userAgent() ?: '';
        $currentDevice = AuditLogger::parseUserAgent($currentUserAgent);

        // 5. Account Summary Statistics
        $totalActivities = (clone $actQuery)->count();
        $totalSurat = Surat::where(function ($q) use ($targetUserId, $warga) {
            if ($targetUserId) $q->where('user_id', $targetUserId);
            if ($warga) $q->orWhere('warga_id', $warga->id);
        })->count();
        $totalPembayaran = Pembayaran::where(function ($q) use ($targetUserId, $warga) {
            if ($targetUserId) $q->where('user_id', $targetUserId);
            if ($warga) $q->orWhere('warga_id', $warga->id);
        })->count();

        // 6. Resolved Preferences
        $notifPrefs = $user->resolved_notification_preferences;
        $privacyPrefs = $user->resolved_privacy_settings;

        return view('profile', compact(
            'user',
            'warga',
            'activeTab',
            'activities',
            'loginHistory',
            'currentIp',
            'currentDevice',
            'totalActivities',
            'totalSurat',
            'totalPembayaran',
            'notifPrefs',
            'privacyPrefs',
            'isRoleSwitched',
            'isPreviewMode',
            'authUser'
        ));
    }

    /**
     * Update Personal & Demographic Information
     */
    public function updatePersonal(Request $request)
    {
        $authUser = Auth::user();
        if ($authUser->isRoleSwitchPreview()) {
            return redirect()->back()->with('error', 'Aksi ditolak: Anda sedang dalam Mode Preview (Hanya Lihat). Form profil tidak dapat diubah.');
        }

        [$user, $warga] = $this->resolveCurrentProfileContext($authUser);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'nullable|in:L,P',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'agama' => 'nullable|string|max:50',
            'status_perkawinan' => 'nullable|string|max:50',
            'pekerjaan' => 'nullable|string|max:100',
            'address' => 'required|string|max:500',
            'blok_rumah' => 'nullable|string|max:50',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        $oldValues = [
            'name' => $user->name,
            'address' => $user->address,
        ];

        // Handle Photo Upload
        if ($request->hasFile('profile_photo')) {
            $photo = $request->file('profile_photo');
            $path = $photo->store('profile_photos', 'public');

            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $user->profile_photo = $path;
        }

        $user->name = $validated['name'];
        $user->address = $validated['address'];
        if ($user->exists) {
            $user->save();
        }

        // Sync or Create Warga record
        if (!$warga) {
            $warga = $user->warga ?? Warga::where('nik', $user->nik)->first() ?? Warga::where('nama', $user->name)->first();
        }
        if ($warga) {
            $warga->update([
                'nama' => $validated['name'],
                'gender' => $validated['gender'] ?? $warga->gender,
                'tempat_lahir' => $validated['tempat_lahir'] ?? $warga->tempat_lahir,
                'tanggal_lahir' => $validated['tanggal_lahir'] ?? $warga->tanggal_lahir,
                'agama' => $validated['agama'] ?? $warga->agama,
                'status_perkawinan' => $validated['status_perkawinan'] ?? $warga->status_perkawinan,
                'pekerjaan' => $validated['pekerjaan'] ?? $warga->pekerjaan,
                'alamat' => $validated['address'],
                'blok_rumah' => $validated['blok_rumah'] ?? $warga->blok_rumah,
                'profile_photo' => $user->profile_photo ?? $warga->profile_photo,
            ]);
        }

        // Audit Log
        AuditLogger::log(
            event: 'update',
            module: 'Profil Saya',
            recordId: $user->id,
            description: "Pengguna '{$user->name}' memperbarui informasi pribadi dan biodata profil.",
            oldValues: $oldValues,
            newValues: ['name' => $user->name, 'address' => $user->address],
            rtNumber: $user->rt_number,
            status: 'success',
            actionType: 'UPDATE'
        );

        return redirect()->route('profile.edit', ['tab' => 'personal'])
            ->with('success', 'Informasi pribadi dan data kependudukan Anda berhasil diperbarui.');
    }

    /**
     * Update Account Credentials (Email & Phone with Current Password Verification)
     */
    public function updateAccount(Request $request)
    {
        $authUser = Auth::user();
        if ($authUser->isRoleSwitchPreview()) {
            return redirect()->back()->with('error', 'Aksi ditolak: Anda sedang dalam Mode Preview (Hanya Lihat). Informasi akun tidak dapat diubah.');
        }

        [$user, $warga] = $this->resolveCurrentProfileContext($authUser);

        $validated = $request->validate([
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/i',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => ['required', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
            'current_password' => 'required|string',
        ], [
            'email.regex' => 'Alamat email wajib menggunakan domain @gmail.com (contoh: nama@gmail.com).',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email ini sudah digunakan oleh akun lain.',
        ]);

        if (!Hash::check($validated['current_password'], $authUser->password) && !Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Kata sandi saat ini yang Anda masukkan tidak sesuai.'])->withInput();
        }

        $emailChanged = ($user->email !== $validated['email']);
        $phoneChanged = ($user->phone !== $validated['phone']);

        $oldValues = [
            'email' => $user->email,
            'phone' => $user->phone,
        ];

        $user->email = $validated['email'];
        $user->phone = $validated['phone'];

        if ($emailChanged) {
            $user->email_verified_at = now();
        }
        if ($phoneChanged) {
            $user->phone_verified_at = now();
        }

        if ($user->exists) {
            $user->save();
        }

        // Update phone on linked warga
        if ($warga) {
            $warga->update(['nomor_hp' => $user->phone]);
        }

        // Audit Log
        AuditLogger::log(
            event: 'update',
            module: 'Profil Saya',
            recordId: $user->id,
            description: "Kredensial akun (Email/Nomor Telepon) diperbarui oleh '{$user->name}'.",
            oldValues: $oldValues,
            newValues: ['email' => $user->email, 'phone' => $user->phone],
            rtNumber: $user->rt_number,
            status: 'success',
            actionType: 'UPDATE'
        );

        return redirect()->route('profile.edit', ['tab' => 'account'])
            ->with('success', 'Informasi akun (Email dan Nomor Telepon) berhasil diperbarui.');
    }

    /**
     * Update Password with Verification
     */
    public function updatePassword(Request $request)
    {
        $authUser = Auth::user();
        if ($authUser->isRoleSwitchPreview()) {
            return redirect()->back()->with('error', 'Aksi ditolak: Anda sedang dalam Mode Preview (Hanya Lihat). Kata sandi akun tidak dapat diubah.');
        }

        [$user] = $this->resolveCurrentProfileContext($authUser);

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], $authUser->password) && !Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Kata sandi lama yang Anda masukkan tidak sesuai.']);
        }

        $user->password = Hash::make($validated['password']);
        if ($user->exists) {
            $user->save();
        }

        AuditLogger::logPasswordChange($user);

        return redirect()->route('profile.edit', ['tab' => 'security'])
            ->with('success', 'Kata sandi akun Anda berhasil diperbarui dengan aman.');
    }

    /**
     * Update Notification Preferences
     */
    public function updateNotifications(Request $request)
    {
        $authUser = Auth::user();
        if ($authUser->isRoleSwitchPreview()) {
            return redirect()->back()->with('error', 'Aksi ditolak: Anda sedang dalam Mode Preview (Hanya Lihat). Preferensi notifikasi tidak dapat diubah.');
        }

        [$user] = $this->resolveCurrentProfileContext($authUser);

        $prefs = [
            'pengumuman' => $request->has('notif_pengumuman'),
            'iuran'      => $request->has('notif_iuran'),
            'kegiatan'   => $request->has('notif_kegiatan'),
            'posyandu'   => $request->has('notif_posyandu'),
            'surat'      => $request->has('notif_surat'),
            'pengaduan'  => $request->has('notif_pengaduan'),
            'in_app'     => $request->has('channel_in_app'),
            'email'      => $request->has('channel_email'),
        ];

        $user->notification_preferences = $prefs;
        if ($user->exists) {
            $user->save();
        }

        AuditLogger::log(
            event: 'update',
            module: 'Profil Saya',
            recordId: $user->id,
            description: "Preferensi notifikasi akun diperbarui oleh '{$user->name}'.",
            rtNumber: $user->rt_number,
            status: 'success',
            actionType: 'UPDATE'
        );

        return redirect()->route('profile.edit', ['tab' => 'notifications'])
            ->with('success', 'Preferensi notifikasi Anda berhasil disimpan.');
    }

    /**
     * Update Theme & Privacy Preferences
     */
    public function updatePreferences(Request $request)
    {
        $authUser = Auth::user();
        if ($authUser->isRoleSwitchPreview()) {
            return redirect()->back()->with('error', 'Aksi ditolak: Anda sedang dalam Mode Preview (Hanya Lihat). Pengaturan preferensi tidak dapat diubah.');
        }

        [$user] = $this->resolveCurrentProfileContext($authUser);

        $validated = $request->validate([
            'theme_preference' => 'required|in:light,dark,system',
        ]);

        $user->theme_preference = $validated['theme_preference'];
        $user->privacy_settings = [
            'hide_phone' => $request->has('hide_phone'),
            'profile_visible' => $request->has('profile_visible'),
        ];
        if ($user->exists) {
            $user->save();
        }

        AuditLogger::log(
            event: 'update',
            module: 'Profil Saya',
            recordId: $user->id,
            description: "Preferensi tema & privasi diperbarui oleh '{$user->name}'.",
            rtNumber: $user->rt_number,
            status: 'success',
            actionType: 'UPDATE'
        );

        return redirect()->route('profile.edit', ['tab' => 'preferences'])
            ->with('success', 'Pengaturan tema dan preferensi privasi berhasil disimpan.');
    }

    /**
     * Verify Phone Number instantly
     */
    public function verifyPhone(Request $request)
    {
        $authUser = Auth::user();
        if ($authUser->isRoleSwitchPreview()) {
            return redirect()->back()->with('error', 'Aksi ditolak: Anda sedang dalam Mode Preview (Hanya Lihat).');
        }

        [$user] = $this->resolveCurrentProfileContext($authUser);
        $user->phone_verified_at = now();
        if ($user->exists) {
            $user->save();
        }

        AuditLogger::log(
            event: 'verify',
            module: 'Profil Saya',
            recordId: $user->id,
            description: "Verifikasi nomor kontak WhatsApp ({$user->phone}) berhasil diselesaikan oleh '{$user->name}'.",
            rtNumber: $user->rt_number,
            status: 'success',
            actionType: 'VERIFY'
        );

        return redirect()->route('profile.edit', ['tab' => 'account'])
            ->with('success', 'Nomor telepon Anda telah berhasil terverifikasi.');
    }

    /**
     * Logout other sessions / devices
     */
    public function logoutOtherDevices(Request $request)
    {
        $authUser = Auth::user();
        if ($authUser->isRoleSwitchPreview()) {
            return redirect()->back()->with('error', 'Aksi ditolak: Anda sedang dalam Mode Preview (Hanya Lihat).');
        }

        $request->validate([
            'confirm_password' => 'required|string',
        ]);

        if (!Hash::check($request->confirm_password, $authUser->password)) {
            return back()->withErrors(['confirm_password' => 'Kata sandi konfirmasi tidak valid.']);
        }

        Auth::logoutOtherDevices($request->confirm_password);

        AuditLogger::log(
            event: 'security_alert',
            module: 'Autentikasi & Keamanan',
            recordId: $authUser->id,
            description: "Pengguna '{$authUser->name}' mengakhiri seluruh sesi login dari perangkat lain.",
            rtNumber: $authUser->rt_number,
            status: 'success',
            actionType: 'LOGOUT'
        );

        return redirect()->route('profile.edit', ['tab' => 'security'])
            ->with('success', 'Seluruh sesi akun di perangkat lain berhasil diputuskan.');
    }

    /**
     * Request Account Deactivation (Submission to Admin RW)
     */
    public function requestDeactivation(Request $request)
    {
        $authUser = Auth::user();
        if ($authUser->isRoleSwitchPreview()) {
            return redirect()->back()->with('error', 'Aksi ditolak: Anda sedang dalam Mode Preview (Hanya Lihat).');
        }

        $request->validate([
            'reason' => 'required|string|max:1000',
            'auth_password' => 'required|string',
        ]);

        [$user] = $this->resolveCurrentProfileContext($authUser);
        if (!Hash::check($request->auth_password, $authUser->password) && !Hash::check($request->auth_password, $user->password)) {
            return back()->withErrors(['auth_password' => 'Kata sandi konfirmasi tidak sesuai.']);
        }

        $user->deactivation_requested_at = now();
        $user->deactivation_reason = $request->reason;
        if ($user->exists) {
            $user->save();
        }

        AuditLogger::log(
            event: 'update',
            module: 'Profil Saya',
            recordId: $user->id,
            description: "Pengajuan penonaktifan akun diserahkan oleh '{$user->name}' (Alasan: {$request->reason}). Data kependudukan & transaksi tetap diarsipkan sesuai regulasi RW 018.",
            rtNumber: $user->rt_number,
            status: 'warning',
            actionType: 'UPDATE'
        );

        return redirect()->route('profile.edit', ['tab' => 'preferences'])
            ->with('success', 'Permohonan penonaktifan akun telah diteruskan kepada Pengurus RW 018 untuk ditindaklanjuti secara administratif.');
    }

    /**
     * General update method (Maintained for backward compatibility)
     */
    public function update(Request $request)
    {
        return $this->updatePersonal($request);
    }
}
