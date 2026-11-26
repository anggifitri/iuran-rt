<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class RoleSwitchContextMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->isSuperAdmin() && $user->isRoleSwitched()) {
                $switchData = session('role_switch', []);
                $isReadOnlyPreview = ($switchData['mode'] ?? 'operational') === 'preview';

                // Share role switch banner data to all Blade views
                View::share('isRoleSwitched', true);
                View::share('roleSwitchData', $switchData);
                View::share('isRoleSwitchPreview', $isReadOnlyPreview);

                // If in Preview Mode, guard against state mutation requests (POST, PUT, PATCH, DELETE)
                $method = strtoupper($request->method());
                if ($isReadOnlyPreview && in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
                    // Allow exit switcher or role switcher switch actions, and logout
                    $allowedRoutes = [
                        'super_admin.role_switch.exit',
                        'super_admin.role_switch.switch',
                        'logout',
                    ];

                    $currentRouteName = $request->route() ? $request->route()->getName() : '';
                    if (!in_array($currentRouteName, $allowedRoutes, true)) {
                        $msg = 'Aksi diblokir: Anda sedang berada dalam Mode Preview (Hanya Lihat). Beralihlah ke Mode Operasional untuk melakukan penambahan, perubahan, atau penghapusan data.';
                        if ($request->ajax() || $request->wantsJson()) {
                            return response()->json(['error' => $msg], 403);
                        }
                        return back()->with('error', $msg);
                    }
                }
            } else {
                View::share('isRoleSwitched', false);
                View::share('roleSwitchData', null);
                View::share('isRoleSwitchPreview', false);
            }
        } else {
            View::share('isRoleSwitched', false);
            View::share('roleSwitchData', null);
            View::share('isRoleSwitchPreview', false);
        }

        return $next($request);
    }
}
