<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan masuk terlebih dahulu.');
        }

        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Halaman ini khusus untuk Super Admin (Platform Owner).');
        }

        // Check if account status is suspended
        if (isset($user->status) && $user->status === 'suspended') {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Akun Super Admin Anda sedang ditangguhkan. Hubungi tim teknis platform.');
        }

        return $next($request);
    }
}
