<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use App\Services\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SessionTokenController extends Controller
{
    /**
     * Memperbarui access token menggunakan refresh token yang valid.
     * Aturan: Refresh token TIDAK BOLEH memperpanjang sesi melewati absolute timeout (1 jam).
     * Rotasi refresh token diterapkan untuk mencegah replay attacks.
     */
    public function refreshToken(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'error' => 'unauthenticated',
                'message' => 'Autentikasi tidak ditemukan. Silakan login kembali.'
            ], 401);
        }

        // 1. Cek status akun (Condition D)
        if (isset($user->status) && $user->status === 'suspended') {
            Auth::logout();
            $request->session()->invalidate();

            return response()->json([
                'status' => 'error',
                'error' => 'account_suspended',
                'message' => 'Akun Anda sedang ditangguhkan. Silakan hubungi pengelola platform.'
            ], 403);
        }

        $now = now()->timestamp;
        $absoluteTimeoutMinutes = (int) PlatformSetting::get('session_absolute_timeout_minutes', 60);
        $tokenLifetimeMinutes = (int) PlatformSetting::get('api_token_lifetime_minutes', 15);
        $idleTimeoutMinutes = (int) PlatformSetting::get('session_idle_timeout_minutes', 30);

        $loginTime = session('login_time', $now);
        $lastActivityTime = session('last_activity_time', $now);

        // 2. Cek apakah sesi sudah melewati absolute timeout
        $elapsedSinceLogin = $now - $loginTime;
        if ($elapsedSinceLogin > ($absoluteTimeoutMinutes * 60)) {
            Auth::logout();
            $request->session()->invalidate();

            return response()->json([
                'status' => 'error',
                'error' => 'session_absolute_timeout',
                'message' => "Batas maksimal durasi sesi ({$absoluteTimeoutMinutes} menit) telah tercapai. Refresh token tidak diizinkan memperpanjang sesi melewati batas ini. Silakan login kembali."
            ], 401);
        }

        // 3. Cek apakah sesi sudah melewati idle timeout
        $elapsedSinceActivity = $now - $lastActivityTime;
        if ($elapsedSinceActivity > ($idleTimeoutMinutes * 60)) {
            Auth::logout();
            $request->session()->invalidate();

            return response()->json([
                'status' => 'error',
                'error' => 'session_idle_timeout',
                'message' => "Sesi Anda telah berakhir karena tidak ada aktivitas selama {$idleTimeoutMinutes} menit. Silakan login kembali."
            ], 401);
        }

        // 4. Validasi refresh token yang dikirimkan
        $providedRefreshToken = $request->input('refresh_token') ?: $request->header('X-Refresh-Token');
        $storedRefreshToken = session('refresh_token');

        if (!$storedRefreshToken || ($providedRefreshToken && !hash_equals((string)$storedRefreshToken, (string)$providedRefreshToken))) {
            return response()->json([
                'status' => 'error',
                'error' => 'invalid_refresh_token',
                'message' => 'Refresh token tidak valid atau telah dirotasi sebelumnya.'
            ], 401);
        }

        // 5. Rotasi Refresh Token & Penerbitan Access Token Baru (Security Best Practice)
        $newAccessToken = Str::random(64);
        $newRefreshToken = Str::random(64);
        $tokenExpiresAt = now()->addMinutes($tokenLifetimeMinutes)->timestamp;

        session([
            'access_token' => $newAccessToken,
            'access_token_expires_at' => $tokenExpiresAt,
            'refresh_token' => $newRefreshToken,
            'last_activity_time' => $now,
        ]);

        $remainingAbsoluteSeconds = max(0, ($absoluteTimeoutMinutes * 60) - ($now - $loginTime));

        return response()->json([
            'status' => 'success',
            'message' => 'Access token berhasil diperbarui melalui rotasi refresh token.',
            'access_token' => $newAccessToken,
            'token_type' => 'Bearer',
            'expires_in' => $tokenLifetimeMinutes * 60,
            'refresh_token' => $newRefreshToken,
            'session_remaining_seconds' => $remainingAbsoluteSeconds,
        ]);
    }

    /**
     * Memeriksa status sesi aktif, sisa idle timeout, dan absolute timeout.
     */
    public function sessionStatus(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'authenticated' => false,
                'message' => 'Sesi tidak terautentikasi.'
            ], 401);
        }

        $now = now()->timestamp;
        $idleTimeoutMinutes = (int) PlatformSetting::get('session_idle_timeout_minutes', 30);
        $absoluteTimeoutMinutes = (int) PlatformSetting::get('session_absolute_timeout_minutes', 60);

        $loginTime = session('login_time', $now);
        $lastActivityTime = session('last_activity_time', $now);
        $tokenExpiresAt = session('access_token_expires_at', $now);

        $remainingIdle = max(0, ($idleTimeoutMinutes * 60) - ($now - $lastActivityTime));
        $remainingAbsolute = max(0, ($absoluteTimeoutMinutes * 60) - ($now - $loginTime));
        $remainingToken = max(0, $tokenExpiresAt - $now);

        return response()->json([
            'status' => 'success',
            'authenticated' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'is_role_switched' => method_exists($user, 'isRoleSwitched') ? $user->isRoleSwitched() : false,
            ],
            'session' => [
                'login_time' => $loginTime,
                'last_activity_time' => $lastActivityTime,
                'idle_timeout_minutes' => $idleTimeoutMinutes,
                'idle_remaining_seconds' => $remainingIdle,
                'absolute_timeout_minutes' => $absoluteTimeoutMinutes,
                'absolute_remaining_seconds' => $remainingAbsolute,
                'is_remember_me' => (bool) session('remember_me', false),
            ],
            'token' => [
                'is_expired' => ($remainingToken <= 0),
                'expires_in_seconds' => $remainingToken,
            ]
        ]);
    }

    /**
     * Logout dan pencabutan sesi serta refresh token API.
     */
    public function logout(Request $request): JsonResponse
    {
        $user = Auth::user();

        if ($user) {
            AuditLogger::logLogout($user);
            $user->setRememberToken(null);
            $user->save();
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'status' => 'success',
            'message' => 'Sesi dan seluruh token autentikasi berhasil dicabut.'
        ]);
    }
}
