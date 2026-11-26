<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\PlatformBackup;
use App\Models\PlatformModuleConfig;
use App\Models\PlatformNotification;
use App\Models\PlatformSetting;
use App\Models\WilayahRw;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class SettingsPlatformController extends Controller
{
    /**
     * Unified Platform Settings Hub with 5 internal tabs:
     * 1. general (Konfigurasi Umum)
     * 2. modules (Manajemen Modul)
     * 3. notifications (Notifikasi Platform)
     * 4. backup (Backup & Pemulihan)
     * 5. security (Keamanan Konfigurasi)
     */
    public function index(Request $request)
    {
        $activeTab = $request->query('tab', 'general');
        $validTabs = ['general', 'modules', 'notifications', 'backup', 'security'];
        if (!in_array($activeTab, $validTabs)) {
            $activeTab = 'general';
        }

        // Settings key-value dictionary
        $settings = PlatformSetting::pluck('value', 'key')->toArray();

        // 1. General & System Metadata
        $appVersion = config('app.version', 'v2.4.0-stable');
        $environment = app()->environment();
        $dbDriver = DB::getDriverName();
        $phpVersion = PHP_VERSION;
        $laravelVersion = app()->version();
        $rw018 = WilayahRw::where('rw_number', '018')->first();

        // 2. Modules (all 22 active platform modules)
        $modules = PlatformModuleConfig::all();
        $moduleStats = [
            'total' => $modules->count(),
            'active_global' => $modules->where('is_enabled_globally', true)->count(),
            'inactive_global' => $modules->where('is_enabled_globally', false)->count(),
            'disabled_rw018' => $modules->filter(function ($m) {
                return in_array('018', $m->disabled_rw_numbers ?? []);
            })->count(),
        ];

        // 3. Notifications (with pagination)
        $notificationQuery = PlatformNotification::with('creator')->latest();
        if ($request->filled('notif_type')) {
            $notificationQuery->where('type', $request->notif_type);
        }
        $notifications = $notificationQuery->paginate(10, ['*'], 'notif_page');

        // 4. Backups
        $backupDir = storage_path('app/backups');
        $backupFilesCount = file_exists($backupDir) ? count(File::files($backupDir)) : 0;
        $totalBackupBytes = 0;
        if (file_exists($backupDir)) {
            foreach (File::files($backupDir) as $file) {
                $totalBackupBytes += $file->getSize();
            }
        }
        $totalBackupFormatted = round($totalBackupBytes / 1024 / 1024, 2) . ' MB';
        $latestBackup = PlatformBackup::latest()->first();
        $backups = PlatformBackup::with('creator')->latest()->paginate(10, ['*'], 'backup_page');

        // 5. Security & Configuration Audit Logs (Strictly configuration/platform events, no role switch leakage)
        $auditLogs = AuditLog::with('user')
            ->where(function ($query) {
                $query->whereIn('action_type', [
                    'CONFIG_CHANGE', 'SETTINGS_UPDATE', 'BROADCAST', 
                    'BACKUP', 'BACKUP_RESTORE', 'MAINTENANCE_TOGGLE', 'SECURITY_CHANGE',
                    'UPDATE_GENERAL_SETTINGS', 'TOGGLE_MODULE_GLOBAL', 'TOGGLE_MODULE_RW',
                    'CREATE_PLATFORM_NOTIFICATION', 'UPDATE_PLATFORM_NOTIFICATION', 'DELETE_PLATFORM_NOTIFICATION',
                    'CREATE_BACKUP', 'RESTORE_BACKUP', 'UPDATE_BACKUP_SCHEDULE', 'UPDATE_SECURITY_SETTINGS'
                ])
                ->orWhere('event', 'like', '%SETTING%')
                ->orWhere('event', 'like', '%MODULE%')
                ->orWhere('event', 'like', '%BACKUP%')
                ->orWhere('event', 'like', '%MAINTENANCE%')
                ->orWhere('module', 'Pengaturan Platform')
                ->orWhere('module', 'Manajemen Modul')
                ->orWhere('module', 'Notifikasi Platform')
                ->orWhere('module', 'Backup Platform');
            })
            ->whereNotIn('event', ['login', 'logout', 'role_switch', 'role_switch_exit', 'role_switch_mode_toggle', 'session_expired'])
            ->whereNotIn('action_type', ['LOGIN', 'LOGOUT', 'ROLE_SWITCH'])
            ->latest()
            ->paginate(10, ['*'], 'audit_page');

        return view('super_admin.settings.index', compact(
            'activeTab',
            'settings',
            'appVersion',
            'environment',
            'dbDriver',
            'phpVersion',
            'laravelVersion',
            'rw018',
            'modules',
            'moduleStats',
            'notifications',
            'backups',
            'backupFilesCount',
            'totalBackupFormatted',
            'latestBackup',
            'auditLogs'
        ));
    }

    /**
     * Update Part 1: Konfigurasi Umum & Maintenance Mode
     */
    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'platform_name' => 'required|string|max:255',
            'platform_tagline' => 'nullable|string|max:255',
            'platform_description' => 'nullable|string|max:500',
            'super_admin_contact' => 'required|email|max:255',
            'super_admin_phone' => 'nullable|string|max:30',
            'app_timezone' => 'required|string|max:100',
            'date_format' => 'required|string|max:50',
            'time_format' => 'required|string|max:50',
            'default_rw' => 'required|string|max:10',
            'maintenance_mode' => 'nullable|boolean',
            'maintenance_rw_018' => 'nullable|boolean',
            'maintenance_reason' => 'nullable|string|max:255',
            'maintenance_estimated_end' => 'nullable|string|max:100',
            'maintenance_message' => 'nullable|string|max:500',
        ]);

        $validated['maintenance_mode'] = $request->boolean('maintenance_mode') ? '1' : '0';
        $validated['maintenance_rw_018'] = $request->boolean('maintenance_rw_018') ? '1' : '0';

        foreach ($validated as $key => $value) {
            PlatformSetting::set($key, (string) ($value ?? ''));
        }

        AuditLogger::log(
            'UPDATE_GENERAL_SETTINGS',
            'Super Admin Platform',
            null,
            "Super Admin (" . Auth::user()->name . ") memperbarui Konfigurasi Umum Platform",
            null,
            $validated,
            null,
            'success',
            'SETTINGS_UPDATE'
        );

        return redirect()->route('super_admin.settings.index', ['tab' => 'general'])
            ->with('success', 'Konfigurasi Umum Platform berhasil diperbarui.');
    }

    /**
     * Quick toggle for Maintenance Mode (Global or RW 018)
     */
    public function toggleMaintenance(Request $request)
    {
        $scope = $request->input('scope', 'global');
        $settingKey = ($scope === 'rw_018') ? 'maintenance_rw_018' : 'maintenance_mode';
        $current = PlatformSetting::get($settingKey, '0');
        $new = ($current === '1') ? '0' : '1';

        PlatformSetting::set($settingKey, $new);

        $label = ($scope === 'rw_018') ? 'Pemeliharaan Khusus RW 018' : 'Mode Pemeliharaan Global';
        $statusText = ($new === '1') ? 'DIAKTIFKAN' : 'DINONAKTIFKAN';

        AuditLogger::log(
            'MAINTENANCE_MODE_TOGGLED',
            'Super Admin Platform',
            null,
            "Super Admin (" . Auth::user()->name . ") mengubah {$label} menjadi {$statusText}",
            ['previous' => $current],
            ['new' => $new, 'scope' => $scope],
            null,
            'warning',
            'MAINTENANCE_TOGGLE'
        );

        return redirect()->route('super_admin.settings.index', ['tab' => 'general'])
            ->with('success', "{$label} berhasil {$statusText}.");
    }

    /**
     * Update Part 2: Toggle Module Globally (Non-Destructive)
     */
    public function toggleModuleGlobal(Request $request, $id)
    {
        $module = PlatformModuleConfig::findOrFail($id);
        $oldState = $module->is_enabled_globally;
        $module->is_enabled_globally = !$oldState;
        $module->save();

        AuditLogger::log(
            'TOGGLE_MODULE_GLOBAL',
            'Super Admin Platform',
            $module->id,
            "Super Admin (" . Auth::user()->name . ") mengubah status global modul {$module->module_name} menjadi " . ($module->is_enabled_globally ? 'AKTIF' : 'NONAKTIF'),
            ['old_state' => $oldState],
            ['new_state' => $module->is_enabled_globally],
            null,
            'success',
            'CONFIG_CHANGE'
        );

        $stateText = $module->is_enabled_globally ? 'diaktifkan secara global' : 'dinonaktifkan secara global';
        return redirect()->route('super_admin.settings.index', ['tab' => 'modules'])
            ->with('success', "Modul {$module->module_name} berhasil {$stateText}. Tidak ada data historis yang dihapus.");
    }

    /**
     * Update Part 2: Toggle Module for RW 018 (Non-Destructive)
     */
    public function toggleModuleRw(Request $request, $id)
    {
        $module = PlatformModuleConfig::findOrFail($id);
        $rwNumber = '018'; // Focused strictly on operational RW 018
        $disabled = is_array($module->disabled_rw_numbers) ? $module->disabled_rw_numbers : [];

        if (in_array($rwNumber, $disabled)) {
            // Re-enable for RW 018
            $disabled = array_values(array_diff($disabled, [$rwNumber]));
            $actionText = 'diaktifkan kembali untuk RW 018';
        } else {
            // Disable for RW 018
            $disabled[] = $rwNumber;
            $actionText = 'dinonaktifkan khusus untuk RW 018';
        }

        $module->disabled_rw_numbers = $disabled;
        $module->save();

        AuditLogger::log(
            'UPDATE_MODULE_RW_ACCESS',
            'Super Admin Platform',
            $module->id,
            "Super Admin (" . Auth::user()->name . ") {$actionText} pada modul {$module->module_name}",
            null,
            ['disabled_rw_numbers' => $module->disabled_rw_numbers],
            null,
            'success',
            'CONFIG_CHANGE'
        );

        return redirect()->route('super_admin.settings.index', ['tab' => 'modules'])
            ->with('success', "Modul {$module->module_name} berhasil {$actionText}.");
    }

    /**
     * Part 3: Store Platform Broadcast Notification
     */
    public function storeNotification(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,warning,critical,maintenance',
            'target_scope' => 'required|in:all,rw,role',
            'target_rw' => 'nullable|string',
            'target_role' => 'nullable|string',
            'is_banner' => 'nullable|boolean',
            'expires_at' => 'nullable|date',
        ]);

        $notif = PlatformNotification::create([
            'title' => $validated['title'],
            'message' => $validated['message'],
            'type' => $validated['type'],
            'target_scope' => $validated['target_scope'],
            'target_rw' => $validated['target_rw'] ?? null,
            'target_role' => $validated['target_role'] ?? null,
            'is_banner' => $request->boolean('is_banner'),
            'starts_at' => now(),
            'expires_at' => $request->filled('expires_at') ? $request->expires_at : now()->addDays(14),
            'created_by' => Auth::id(),
        ]);

        AuditLogger::log(
            'BROADCAST_PLATFORM_NOTIFICATION',
            'Super Admin Platform',
            $notif->id,
            "Super Admin (" . Auth::user()->name . ") mempublikasikan siaran platform: {$notif->title} [{$notif->type}]",
            null,
            $notif->toArray(),
            null,
            'success',
            'BROADCAST'
        );

        return redirect()->route('super_admin.settings.index', ['tab' => 'notifications'])
            ->with('success', 'Siaran notifikasi platform berhasil dipublikasikan.');
    }

    /**
     * Part 3: Update Platform Broadcast Notification
     */
    public function updateNotification(Request $request, $id)
    {
        $notif = PlatformNotification::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,warning,critical,maintenance',
            'target_scope' => 'required|in:all,rw,role',
            'target_rw' => 'nullable|string',
            'target_role' => 'nullable|string',
            'is_banner' => 'nullable|boolean',
            'expires_at' => 'nullable|date',
        ]);

        $oldData = $notif->toArray();
        $notif->update([
            'title' => $validated['title'],
            'message' => $validated['message'],
            'type' => $validated['type'],
            'target_scope' => $validated['target_scope'],
            'target_rw' => $validated['target_rw'] ?? null,
            'target_role' => $validated['target_role'] ?? null,
            'is_banner' => $request->boolean('is_banner'),
            'expires_at' => $request->filled('expires_at') ? $request->expires_at : $notif->expires_at,
        ]);

        AuditLogger::log(
            'UPDATE_PLATFORM_NOTIFICATION',
            'Super Admin Platform',
            $notif->id,
            "Super Admin (" . Auth::user()->name . ") memperbarui siaran platform: {$notif->title}",
            $oldData,
            $notif->toArray(),
            null,
            'success',
            'BROADCAST'
        );

        return redirect()->route('super_admin.settings.index', ['tab' => 'notifications'])
            ->with('success', 'Notifikasi platform berhasil diperbarui.');
    }

    /**
     * Part 3: Delete Platform Broadcast Notification
     */
    public function destroyNotification($id)
    {
        $notif = PlatformNotification::findOrFail($id);
        $title = $notif->title;
        $notif->delete();

        AuditLogger::log(
            'DELETE_PLATFORM_NOTIFICATION',
            'Super Admin Platform',
            $id,
            "Super Admin (" . Auth::user()->name . ") menghapus siaran platform: {$title}",
            null,
            null,
            null,
            'warning',
            'BROADCAST'
        );

        return redirect()->route('super_admin.settings.index', ['tab' => 'notifications'])
            ->with('success', "Notifikasi '{$title}' berhasil dihapus.");
    }

    /**
     * Part 4: Manual Backup Creation
     */
    public function createBackup(Request $request)
    {
        $backupDir = storage_path('app/backups');
        if (!file_exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $timestamp = date('Y-m-d_His');
        $filename = "nexanest_backup_manual_{$timestamp}.sqlite";
        $destPath = $backupDir . '/' . $filename;
        $sourcePath = database_path('database.sqlite');

        if (!file_exists($sourcePath)) {
            return redirect()->route('super_admin.settings.index', ['tab' => 'backup'])
                ->with('error', 'Gagal membuat cadangan: file database SQLite tidak ditemukan.');
        }

        try {
            File::copy($sourcePath, $destPath);
            $bytes = filesize($destPath);
            $filesize = round($bytes / 1024 / 1024, 2) . ' MB';
            $sha256 = hash_file('sha256', $destPath);

            $backup = PlatformBackup::create([
                'filename' => $filename,
                'filesize' => $filesize,
                'type' => 'database',
                'status' => 'success',
                'notes' => "Cadangan manual Super Admin HQ [SHA256: {$sha256}]",
                'created_by' => Auth::id(),
            ]);

            AuditLogger::log(
                'DATABASE_BACKUP_CREATED',
                'Super Admin Platform',
                $backup->id,
                "Super Admin (" . Auth::user()->name . ") membuat cadangan database: {$filename} ({$filesize})",
                null,
                ['filename' => $filename, 'size' => $filesize, 'sha256' => $sha256],
                null,
                'success',
                'BACKUP'
            );

            return redirect()->route('super_admin.settings.index', ['tab' => 'backup'])
                ->with('success', "Cadangan database berhasil dibuat: {$filename} ({$filesize}). SHA-256 terverifikasi.");
        } catch (\Throwable $e) {
            return redirect()->route('super_admin.settings.index', ['tab' => 'backup'])
                ->with('error', 'Gagal membuat cadangan: ' . $e->getMessage());
        }
    }

    /**
     * Part 4: Update Scheduled Backup Settings
     */
    public function updateBackupSchedule(Request $request)
    {
        $validated = $request->validate([
            'backup_schedule_frequency' => 'required|in:daily,weekly,disabled',
            'backup_schedule_time' => 'required|string|max:10',
            'backup_retention_days' => 'required|integer|min:1|max:365',
        ]);

        foreach ($validated as $key => $val) {
            PlatformSetting::set($key, (string) $val);
        }

        AuditLogger::log(
            'UPDATE_BACKUP_SCHEDULE',
            'Super Admin Platform',
            null,
            "Super Admin (" . Auth::user()->name . ") memperbarui jadwal cadangan otomatis [{$validated['backup_schedule_frequency']} jam {$validated['backup_schedule_time']}]",
            null,
            $validated,
            null,
            'success',
            'CONFIG_CHANGE'
        );

        return redirect()->route('super_admin.settings.index', ['tab' => 'backup'])
            ->with('success', 'Jadwal cadangan otomatis platform berhasil diperbarui.');
    }

    /**
     * Part 4: Restore Backup with Safety Snapshot & Re-authentication
     */
    public function restoreBackup(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string',
            'confirm_restore' => 'required|in:PULIHKAN',
        ], [
            'password.required' => 'Kata sandi Super Admin wajib diisi untuk verifikasi keamanan.',
            'confirm_restore.in' => 'Konfirmasi wajib diketik secara tepat: PULIHKAN',
        ]);

        // Security re-auth check
        if (!Hash::check($request->password, Auth::user()->password)) {
            AuditLogger::log(
                'RESTORE_FAILED_PASSWORD',
                'Super Admin Platform',
                $id,
                "Percobaan pemulihan database GAGAL: verifikasi kata sandi salah oleh " . Auth::user()->name,
                null,
                null,
                null,
                'failed',
                'SECURITY_CHANGE',
                true
            );

            return redirect()->route('super_admin.settings.index', ['tab' => 'backup'])
                ->with('error', 'Kata sandi Super Admin tidak sesuai. Pemulihan dibatalkan demi keamanan.');
        }

        $backup = PlatformBackup::findOrFail($id);
        $backupDir = storage_path('app/backups');
        $restoreFilePath = $backupDir . '/' . $backup->filename;

        if (!file_exists($restoreFilePath)) {
            return redirect()->route('super_admin.settings.index', ['tab' => 'backup'])
                ->with('error', 'File cadangan fisik tidak ditemukan pada server penyimpanan.');
        }

        $currentDbPath = database_path('database.sqlite');

        try {
            // STEP 1: SAFETY PROTOCOL - Create an emergency safety pre-restore backup first!
            $safetyTimestamp = date('Y-m-d_His');
            $safetyFilename = "nexanest_pre_restore_safety_{$safetyTimestamp}.sqlite";
            $safetyDest = $backupDir . '/' . $safetyFilename;

            if (file_exists($currentDbPath)) {
                File::copy($currentDbPath, $safetyDest);
                $safetyBytes = filesize($safetyDest);
                $safetySize = round($safetyBytes / 1024 / 1024, 2) . ' MB';
                $safetySha = hash_file('sha256', $safetyDest);

                PlatformBackup::create([
                    'filename' => $safetyFilename,
                    'filesize' => $safetySize,
                    'type' => 'safety_snapshot',
                    'status' => 'success',
                    'notes' => "Snapshot darurat otomatis sebelum pemulihan cadangan #{$backup->id} [SHA256: {$safetySha}]",
                    'created_by' => Auth::id(),
                ]);
            }

            // STEP 2: Overwrite SQLite database with selected backup file
            File::copy($restoreFilePath, $currentDbPath);

            AuditLogger::log(
                'DATABASE_RESTORED',
                'Super Admin Platform',
                $backup->id,
                "Super Admin (" . Auth::user()->name . ") berhasil memulihkan database dari file cadangan: {$backup->filename}",
                ['target_backup' => $backup->filename],
                ['safety_snapshot' => $safetyFilename ?? null],
                null,
                'warning',
                'BACKUP_RESTORE'
            );

            return redirect()->route('super_admin.settings.index', ['tab' => 'backup'])
                ->with('success', "Database platform berhasil dipulihkan dari {$backup->filename}. Snapshot pengaman pra-pemulihan otomatis telah disimpan ({$safetyFilename}).");
        } catch (\Throwable $e) {
            return redirect()->route('super_admin.settings.index', ['tab' => 'backup'])
                ->with('error', 'Gagal memulihkan database: ' . $e->getMessage());
        }
    }

    /**
     * Part 4: Download Backup
     */
    public function downloadBackup($id)
    {
        $backup = PlatformBackup::findOrFail($id);
        $filePath = storage_path('app/backups/' . $backup->filename);

        if (!file_exists($filePath)) {
            return redirect()->route('super_admin.settings.index', ['tab' => 'backup'])
                ->with('error', 'File cadangan fisik tidak ditemukan pada direktori penyimpanan.');
        }

        AuditLogger::log(
            'DATABASE_BACKUP_DOWNLOADED',
            'Super Admin Platform',
            $backup->id,
            "Super Admin (" . Auth::user()->name . ") mengunduh arsip cadangan: {$backup->filename}",
            null,
            null,
            null,
            'warning',
            'BACKUP_DOWNLOAD'
        );

        return response()->download($filePath);
    }

    /**
     * Part 5: Update Keamanan Konfigurasi
     */
    public function updateSecurity(Request $request)
    {
        $validated = $request->validate([
            'session_idle_timeout_minutes' => 'nullable|integer|min:5|max:1440',
            'session_absolute_timeout_minutes' => 'nullable|integer|min:15|max:1440',
            'api_token_lifetime_minutes' => 'nullable|integer|min:5|max:120',
            'allow_remember_me' => 'nullable|boolean',
            'sensitive_data_auth_timeout_minutes' => 'required|integer|min:1|max:120',
            'max_login_attempts' => 'required|integer|min:3|max:20',
            'lockout_duration_minutes' => 'required|integer|min:1|max:60',
            'allow_self_rw_registration' => 'nullable|boolean',
            'max_rt_per_rw' => 'required|integer|min:1|max:100',
        ]);

        $validated['allow_self_rw_registration'] = $request->boolean('allow_self_rw_registration') ? '1' : '0';
        if ($request->has('allow_remember_me')) {
            $validated['allow_remember_me'] = $request->boolean('allow_remember_me') ? '1' : '0';
        }

        foreach ($validated as $key => $val) {
            if ($val !== null) {
                PlatformSetting::set($key, (string) $val);
            }
        }

        AuditLogger::log(
            'UPDATE_SECURITY_SETTINGS',
            'Super Admin Platform',
            null,
            "Super Admin (" . Auth::user()->name . ") memperbarui Kebijakan & Keamanan Konfigurasi Platform (Session, Token, dan Parameter Keamanan)",
            null,
            $validated,
            null,
            'warning',
            'SECURITY_CHANGE'
        );

        return redirect()->route('super_admin.settings.index', ['tab' => 'security'])
            ->with('success', 'Konfigurasi parameter sesi dan keamanan platform berhasil diperbarui.');
    }
}
