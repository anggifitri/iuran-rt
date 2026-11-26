<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\PlatformNotification;
use App\Models\WilayahRw;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationPlatformController extends Controller
{
    public function index()
    {
        return redirect()->route('super_admin.settings.index', ['tab' => 'notifications']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,warning,critical,maintenance',
            'target_scope' => 'required|in:all,rw,role',
            'target_rw' => 'nullable|string',
            'target_role' => 'nullable|string',
            'is_banner' => 'nullable|boolean',
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
            'expires_at' => now()->addDays(14),
            'created_by' => Auth::id(),
        ]);

        AuditLogger::log(
            'BROADCAST_PLATFORM_NOTIFICATION',
            'Super Admin Platform',
            $notif->id,
            "Super Admin (" . Auth::user()->name . ") menyiarkan notifikasi platform: {$notif->title}",
            null,
            $notif->toArray(),
            null,
            'success',
            'BROADCAST'
        );

        return back()->with('success', 'Siaran pengumuman sistem berhasil dipublikasikan.');
    }

    public function destroy($id)
    {
        $notif = PlatformNotification::findOrFail($id);
        $notif->delete();

        return back()->with('success', 'Notifikasi berhasil dihapus.');
    }
}
