<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Warga;
use App\Models\WilayahRw;
use App\Services\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleSwitcherController extends Controller
{
    /**
     * Switch role context into Admin RW, Admin RT, or Warga.
     */
    public function switch(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isSuperAdmin()) {
            abort(403, 'Hanya Super Admin yang diizinkan menggunakan Role Switcher.');
        }

        $validated = $request->validate([
            'target_role' => 'required|in:admin_rw,admin_rt,warga',
            'target_rw' => 'required|string',
            'target_rt' => 'nullable|string',
            'mode' => 'required|in:operational,preview',
            'target_user_id' => 'nullable|exists:users,id',
            'target_warga_id' => 'nullable|exists:wargas,id',
        ]);

        $targetRole = $validated['target_role'];
        $targetRw = $validated['target_rw'];
        $mode = $validated['mode'];
        $targetRt = $validated['target_rt'] ?? null;
        $targetUserId = $validated['target_user_id'] ?? null;
        $targetWargaId = $validated['target_warga_id'] ?? null;
        $targetUserName = null;

        // Validasi dan Resolusi Khusus Peran Warga
        if ($targetRole === 'warga') {
            if (empty($targetRt) || !in_array($targetRt, ['006', '007', '008', '009', '010'])) {
                return back()->with('error', 'Wajib memilih RT terlebih dahulu (RT 006 – RT 010) sebelum memulai simulasi Warga.')->withInput();
            }

            if (empty($targetWargaId) && empty($targetUserId)) {
                return back()->with('error', 'Wajib memilih salah satu warga dari RT terpilih untuk simulasi peran Warga.')->withInput();
            }

            $selectedWarga = null;
            if (!empty($targetWargaId)) {
                $selectedWarga = Warga::where('id', $targetWargaId)
                    ->where('rt_number', $targetRt)
                    ->first();
            } elseif (!empty($targetUserId)) {
                $tu = User::find($targetUserId);
                if ($tu) {
                    $selectedWarga = $tu->resolveWarga();
                }
            }

            if (!$selectedWarga || $selectedWarga->rt_number !== $targetRt) {
                return back()->with('error', "Akun/data warga yang dipilih tidak valid atau tidak terdaftar di RT {$targetRt}.")->withInput();
            }

            $targetWargaId = $selectedWarga->id;
            $targetUserId = $selectedWarga->user_id;
            $targetUserName = $selectedWarga->nama;
        } elseif ($targetRole === 'admin_rt') {
            if (empty($targetRt) || !in_array($targetRt, ['006', '007', '008', '009', '010'])) {
                return back()->with('error', 'Wajib memilih salah satu RT operasional (RT 006 – RT 010) untuk simulasi Admin RT.')->withInput();
            }

            $adminRtUser = User::where(function ($q) {
                $q->where('role', 'admin_rt')->orWhere('role', 'admin');
            })->where('rt_number', $targetRt)->first();

            if ($adminRtUser) {
                $targetUserId = $adminRtUser->id;
                $targetUserName = $adminRtUser->name;
            } else {
                $targetUserName = "Admin RT {$targetRt}";
            }
        } else {
            // admin_rw
            $targetRt = '000';
            $adminRwUser = User::where(function ($q) {
                $q->where('role', 'admin_rw')->orWhere('role', 'admin');
            })->where('rt_number', '000')->first();

            if ($adminRwUser) {
                $targetUserId = $adminRwUser->id;
                $targetUserName = $adminRwUser->name;
            } else {
                $targetUserName = "Admin RW {$targetRw}";
            }
        }

        session([
            'role_switch' => [
                'is_switched' => true,
                'target_role' => $targetRole,
                'target_rw' => $targetRw,
                'target_rt' => $targetRt,
                'mode' => $mode,
                'target_user_id' => $targetUserId,
                'target_warga_id' => $targetWargaId,
                'target_user_name' => $targetUserName,
                'switched_at' => now()->toDateTimeString(),
                'switched_by' => $user->id,
            ]
        ]);

        // Audit Trail
        AuditLogger::log(
            'ROLE_SWITCH',
            'Super Admin Platform',
            $targetUserId ?: $targetWargaId,
            "Super Admin ({$user->name}) beralih konteks peran ke " . strtoupper($targetRole) . " (RW {$targetRw}, RT {$targetRt}) " . ($targetUserName ? "[Subjek: {$targetUserName}] " : "") . "dalam Mode " . strtoupper($mode),
            null,
            session('role_switch'),
            $targetRt,
            'success',
            'ROLE_SWITCH'
        );

        $roleHuman = match($targetRole) {
            'admin_rw' => "Admin RW {$targetRw}",
            'admin_rt' => "Admin RT {$targetRt} (RW {$targetRw})",
            'warga' => "Warga: {$targetUserName} (RT {$targetRt} RW {$targetRw})",
            default => $targetRole
        };

        $modeHuman = $mode === 'preview' ? 'Mode Preview (Hanya Lihat)' : 'Mode Operasional';

        return redirect()->route('dashboard')->with('success', "Peran disimulasikan sebagai {$roleHuman} dalam {$modeHuman}.");
    }

    /**
     * Endpoint API untuk mengambil daftar warga berdasarkan RT secara dinamis (Cascading).
     */
    public function getWargaByRt(Request $request, string $rt): JsonResponse
    {
        $user = Auth::user();
        if (!$user || !$user->isSuperAdmin()) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $rtNumber = str_pad(ltrim($rt, '0'), 3, '0', STR_PAD_LEFT);

        $wargas = Warga::where('rt_number', $rtNumber)
            ->with('user')
            ->orderBy('is_kk', 'desc')
            ->orderBy('nama', 'asc')
            ->get()
            ->map(function ($w) {
                $email = $w->user ? $w->user->email : null;
                if (!$email) {
                    $u = User::where('warga_id', $w->id)->orWhere('name', $w->nama)->first();
                    $email = $u ? $u->email : 'tanpa akun login';
                }
                $blok = $w->blok_rumah ?: ($w->alamat ?: 'Blok -');

                return [
                    'warga_id' => $w->id,
                    'user_id' => $w->user_id ?? ($w->user ? $w->user->id : null),
                    'nama' => $w->nama,
                    'blok' => $blok,
                    'email' => $email,
                    'label' => "{$w->nama} — {$blok} ({$email})",
                    'is_kk' => (bool)$w->is_kk,
                ];
            });

        return response()->json([
            'status' => 'success',
            'rt' => $rtNumber,
            'count' => $wargas->count(),
            'wargas' => $wargas,
        ]);
    }

    /**
     * Toggle mode between Preview and Operational quickly.
     */
    public function toggleMode(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isSuperAdmin() || !session('role_switch.is_switched')) {
            return back()->with('error', 'Role Switcher sedang tidak aktif.');
        }

        $currentMode = session('role_switch.mode', 'operational');
        $newMode = $currentMode === 'preview' ? 'operational' : 'preview';

        session(['role_switch.mode' => $newMode]);

        AuditLogger::log(
            'ROLE_SWITCH_MODE_TOGGLE',
            'Super Admin Platform',
            null,
            "Super Admin mengubah mode simulasi menjadi " . strtoupper($newMode),
            ['old_mode' => $currentMode],
            ['new_mode' => $newMode],
            session('role_switch.target_rt'),
            'success',
            'ROLE_SWITCH'
        );

        $msg = $newMode === 'preview'
            ? 'Beralih ke Mode Preview (Hanya Lihat). Modifikasi data diproteksi.'
            : 'Beralih ke Mode Operasional. Anda kini dapat melakukan tindakan administratif langsung.';

        return back()->with('success', $msg);
    }

    /**
     * Exit role switcher and safely return to Super Admin Platform Control Center.
     */
    public function exit(Request $request)
    {
        $user = Auth::user();
        if ($user && $user->isSuperAdmin()) {
            $prevContext = session('role_switch');
            session()->forget('role_switch');

            AuditLogger::log(
                'ROLE_SWITCH_EXIT',
                'Super Admin Platform',
                null,
                "Super Admin ({$user->name}) keluar dari simulasi peran dan kembali ke status Super Admin HQ",
                $prevContext,
                null,
                null,
                'success',
                'ROLE_SWITCH'
            );

            return redirect()->route('super_admin.dashboard')->with('success', 'Kembali ke Platform Control Center sebagai Super Admin HQ.');
        }

        return redirect()->route('dashboard');
    }
}
