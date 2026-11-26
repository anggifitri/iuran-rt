<?php

namespace App\Http\Middleware;

use App\Models\PlatformSetting;
use App\Services\AuditLogger;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnforceSessionTimeoutMiddleware
{
    /**
     * Handle an incoming request for Session Idle and Absolute Timeout.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // 1. Kondisi D: Pemeriksaan Akun Ditangguhkan (Suspended)
        if (isset($user->status) && $user->status === 'suspended') {
            AuditLogger::log(
                event: 'account_locked',
                module: 'Autentikasi & Keamanan',
                recordId: $user->id,
                description: "Sesi aktif pengguna '{$user->name}' dibatalkan otomatis karena akun berstatus ditangguhkan.",
                status: 'warning',
                actionType: 'LOGOUT'
            );

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'error' => 'account_suspended',
                    'message' => 'Akun Anda sedang ditangguhkan. Silakan hubungi pengelola platform.'
                ], 403);
            }

            return redirect()->route('login')
                ->with('error', 'Akun Anda sedang ditangguhkan. Silakan hubungi pengelola platform.')
                ->withErrors([
                    'email' => 'Akun Anda sedang ditangguhkan. Silakan hubungi pengelola platform.'
                ]);
        }

        $now = now()->timestamp;

        // 2. Ambil parameter durasi dari Pengaturan Platform (dengan fallback default aman)
        $idleTimeoutMinutes = (int) PlatformSetting::get('session_idle_timeout_minutes', 30);
        $absoluteTimeoutMinutes = (int) PlatformSetting::get('session_absolute_timeout_minutes', 60);

        $loginTime = session('login_time');
        $lastActivityTime = session('last_activity_time');

        // Inisialisasi awal jika sesi belum memiliki timestamp (misal sesi baru atau persistent remember-me)
        if (!$loginTime) {
            session(['login_time' => $now]);
            $loginTime = $now;
        }

        if (!$lastActivityTime) {
            session(['last_activity_time' => $now]);
            $lastActivityTime = $now;
        }

        // 3. Batas Maksimal Sesi (Absolute Timeout - Maks 1 Jam)
        // Aturan: Batas ini TIDAK BOLEH diperpanjang oleh aktivitas maupun Role Switcher Super Admin!
        $elapsedSinceLogin = $now - $loginTime;
        if ($elapsedSinceLogin > ($absoluteTimeoutMinutes * 60)) {
            AuditLogger::log(
                event: 'session_expired',
                module: 'Autentikasi & Keamanan',
                recordId: $user->id,
                description: "Sesi login pengguna '{$user->name}' berakhir otomatis karena mencapai batas maksimal absolute timeout ({$absoluteTimeoutMinutes} menit).",
                status: 'warning',
                actionType: 'LOGOUT'
            );

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'error' => 'session_absolute_timeout',
                    'message' => "Batas maksimal sesi login ({$absoluteTimeoutMinutes} menit) telah tercapai demi keamanan. Silakan login kembali."
                ], 401);
            }

            return redirect()->route('login')->with(
                'error',
                "Batas maksimal sesi login ({$absoluteTimeoutMinutes} menit) telah tercapai demi keamanan. Silakan login kembali."
            );
        }

        // 4. Batas Waktu Tidak Aktif (Idle Timeout - Maks 30 Menit)
        // Aturan: Jangan menghitung aktivitas otomatis seperti background polling/metrics sebagai aktivitas pengguna!
        $isBackgroundPolling = $request->hasHeader('X-Background-Request')
            || $request->query('background') === '1'
            || $request->is('super-admin/monitoring/api/metrics')
            || $request->is('api/monitoring/*')
            || $request->is('api/rfid/*')
            || $request->is('api/auth/session/status');

        $elapsedSinceLastActivity = $now - $lastActivityTime;
        if ($elapsedSinceLastActivity > ($idleTimeoutMinutes * 60)) {
            AuditLogger::log(
                event: 'session_idle_timeout',
                module: 'Autentikasi & Keamanan',
                recordId: $user->id,
                description: "Sesi login pengguna '{$user->name}' berakhir otomatis karena tidak aktif selama {$idleTimeoutMinutes} menit.",
                status: 'warning',
                actionType: 'LOGOUT'
            );

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'error' => 'session_idle_timeout',
                    'message' => "Sesi Anda telah berakhir karena tidak ada aktivitas selama {$idleTimeoutMinutes} menit. Silakan login kembali."
                ], 401);
            }

            return redirect()->route('login')->with(
                'error',
                "Sesi Anda telah berakhir karena tidak ada aktivitas selama {$idleTimeoutMinutes} menit. Silakan login kembali."
            );
        }

        // Jika bukan request polling otomatis, perbarui waktu aktivitas terakhir
        if (!$isBackgroundPolling) {
            session(['last_activity_time' => $now]);
        }

        return $next($request);
    }
}
