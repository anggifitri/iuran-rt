<?php

namespace App\Http\Controllers;

use App\Models\FinanceNotification;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    /**
     * Tampilkan halaman riwayat notifikasi untuk pengguna yang sedang login
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = FinanceNotification::where('user_id', $user->id)->orderBy('created_at', 'desc');

        if ($request->get('status') === 'unread') {
            $query->where('is_read', false);
        }

        $notifications = $query->paginate(20)->withQueryString();
        $unreadCount = FinanceNotification::where('user_id', $user->id)->where('is_read', false)->count();

        return view('notifikasi.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Tandai notifikasi spesifik sebagai telah dibaca
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        $notif = FinanceNotification::where('id', $id)->where('user_id', $user->id)->firstOrFail();

        $notif->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        if ($notif->link_url) {
            return redirect($notif->link_url);
        }

        return redirect()->back()->with('success', 'Notifikasi telah ditandai sudah dibaca.');
    }

    /**
     * Tandai seluruh notifikasi pengguna sebagai telah dibaca
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        FinanceNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai sudah dibaca.');
    }
}
