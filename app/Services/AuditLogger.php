<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    /**
     * Record an audit event into audit_logs table
     */
    public static function log(
        string $event,
        string $module,
        $recordId = null,
        ?string $description = null,
        $oldValues = null,
        $newValues = null,
        ?string $rtNumber = null,
        string $status = 'success',
        ?string $actionType = null,
        bool $isSuspicious = false
    ): ?AuditLog {
        try {
            $user = Auth::user();
            $userId = $user ? $user->id : null;
            $userName = $user ? $user->name : 'Sistem / Tamu';
            $role = $user ? $user->role : 'guest';
            $rt = $rtNumber ?? ($user ? $user->rt_number : null);

            // Audit Actor Integrity: If Super Admin is simulating another role, actor remains Super Admin
            if ($user && method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin() && method_exists($user, 'isRoleSwitched') && $user->isRoleSwitched()) {
                $ctx = $user->getRoleSwitchContext();
                $simRole = strtoupper($ctx['target_role'] ?? 'OPERATIONAL');
                $simMode = strtoupper($ctx['mode'] ?? 'OPERATIONAL');
                $simRw = $ctx['target_rw'] ?? '018';
                $simRt = $ctx['target_rt'] ?? '006';
                
                $userName = $user->name . " [Super Admin]";
                $role = 'super_admin';
                $description = ($description ? $description . " " : "") . "[Aktor: Super Admin | Konteks: {$simRole} RW {$simRw}/RT {$simRt} ({$simMode})]";
            }

            // Sanitize sensitive fields from old / new values if arrays
            $sanitize = function ($data) {
                if (!is_array($data)) return $data;
                $sensitive = ['password', 'remember_token', 'token', 'secret', 'api_key', 'pin'];
                foreach ($sensitive as $k) {
                    if (isset($data[$k])) {
                        $data[$k] = '********';
                    }
                }
                return $data;
            };

            // Derive action_type if not provided
            if (!$actionType) {
                $actionType = strtoupper($event);
            }

            // Derive device info
            $ua = Request::userAgent() ?: '';
            $deviceInfo = self::parseUserAgent($ua);

            return AuditLog::create([
                'user_id' => $userId,
                'user_name' => $userName,
                'role' => $role,
                'event' => strtolower($event),
                'status' => $status,
                'action_type' => $actionType,
                'module' => $module,
                'record_id' => $recordId ? (string)$recordId : null,
                'rt_number' => $rt,
                'description' => $description,
                'old_values' => $oldValues ? json_encode($sanitize($oldValues), JSON_UNESCAPED_UNICODE) : null,
                'new_values' => $newValues ? json_encode($sanitize($newValues), JSON_UNESCAPED_UNICODE) : null,
                'ip_address' => Request::ip(),
                'user_agent' => $ua,
                'device_info' => $deviceInfo,
                'is_suspicious' => $isSuspicious,
            ]);
        } catch (\Throwable $e) {
            // Fail silently to prevent crashing critical operational tasks if log fails
            return null;
        }
    }

    /**
     * Log authentication login (success or failure)
     */
    public static function logLogin(?User $user, bool $success = true, ?string $reason = null): ?AuditLog
    {
        $userName = $user ? $user->name : 'Pengguna Tidak Dikenal';
        $role = $user ? $user->role : 'guest';
        $rt = $user ? $user->rt_number : null;
        $status = $success ? 'success' : 'failed';
        $isSuspicious = !$success;

        $desc = $success 
            ? "Autentikasi berhasil: '{$userName}' ({$role}) berhasil masuk ke dalam sistem NexaNest."
            : "Percobaan login gagal: Kredensial tidak valid untuk percobaan login ({$reason}).";

        return self::log(
            event: $success ? 'login' : 'login_failed',
            module: 'Autentikasi & Keamanan',
            recordId: $user ? $user->id : null,
            description: $desc,
            rtNumber: $rt,
            status: $status,
            actionType: 'LOGIN',
            isSuspicious: $isSuspicious
        );
    }

    /**
     * Log authentication logout
     */
    public static function logLogout(?User $user): ?AuditLog
    {
        $userName = $user ? $user->name : 'Pengguna';
        return self::log(
            event: 'logout',
            module: 'Autentikasi & Keamanan',
            recordId: $user ? $user->id : null,
            description: "Pengguna '{$userName}' telah keluar (logout) dari sesi sistem.",
            rtNumber: $user ? $user->rt_number : null,
            status: 'success',
            actionType: 'LOGOUT'
        );
    }

    /**
     * Log password reset or credential changes
     */
    public static function logPasswordChange(User $user): ?AuditLog
    {
        return self::log(
            event: 'update',
            module: 'Autentikasi & Keamanan',
            recordId: $user->id,
            description: "Kata sandi akun pengguna '{$user->name}' ({$user->email}) telah berhasil diperbarui.",
            rtNumber: $user->rt_number,
            status: 'success',
            actionType: 'UPDATE'
        );
    }

    /**
     * Log security anomaly or unauthorized access attempt (403)
     */
    public static function logUnauthorized(string $targetModule, ?string $targetAction = null): ?AuditLog
    {
        $user = Auth::user();
        $userName = $user ? $user->name : 'Anonim / Guest';
        $role = $user ? $user->role : 'guest';
        $desc = "Peringatan Keamanan: Upaya akses tidak diizinkan oleh '{$userName}' (Role: {$role}) pada modul '{$targetModule}' " . ($targetAction ? "[Aksi: {$targetAction}]" : "");

        return self::log(
            event: 'unauthorized',
            module: 'Autentikasi & Keamanan',
            recordId: $user ? $user->id : null,
            description: $desc,
            rtNumber: $user ? $user->rt_number : null,
            status: 'suspicious',
            actionType: 'SECURITY_ALERT',
            isSuspicious: true
        );
    }

    /**
     * Parse User-Agent into clean device label
     */
    public static function parseUserAgent(?string $ua): string
    {
        if (empty($ua)) return 'Perangkat Browser';

        // OS
        $os = 'OS Lain';
        if (stripos($ua, 'Windows NT 10') !== false) $os = 'Windows 10/11';
        elseif (stripos($ua, 'Windows') !== false) $os = 'Windows';
        elseif (stripos($ua, 'iPhone') !== false) $os = 'iPhone (iOS)';
        elseif (stripos($ua, 'iPad') !== false) $os = 'iPad';
        elseif (stripos($ua, 'Android') !== false) $os = 'Android';
        elseif (stripos($ua, 'Macintosh') !== false) $os = 'macOS';
        elseif (stripos($ua, 'Linux') !== false) $os = 'Linux';

        // Browser
        $browser = 'Browser';
        if (stripos($ua, 'Edg') !== false) $browser = 'Edge';
        elseif (stripos($ua, 'Chrome') !== false && stripos($ua, 'Edg') === false) $browser = 'Chrome';
        elseif (stripos($ua, 'Safari') !== false && stripos($ua, 'Chrome') === false) $browser = 'Safari';
        elseif (stripos($ua, 'Firefox') !== false) $browser = 'Firefox';
        elseif (stripos($ua, 'Opera') !== false || stripos($ua, 'OPR') !== false) $browser = 'Opera';

        return "{$browser} pada {$os}";
    }
}
