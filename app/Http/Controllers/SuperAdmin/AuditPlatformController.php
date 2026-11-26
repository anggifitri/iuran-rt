<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditPlatformController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $module = $request->query('module');
        $role = $request->query('role');
        $status = $request->query('status');

        $query = AuditLog::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                  ->orWhere('event', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        if ($module) {
            $query->where('module', $module);
        }

        if ($role) {
            $query->where('role', $role);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $logs = $query->latest()->paginate(25)->withQueryString();

        $modules = AuditLog::select('module')->distinct()->whereNotNull('module')->pluck('module');

        $totalLogs = AuditLog::count();
        $totalSuccess = AuditLog::where('status', 'success')->count();
        $totalAlerts = AuditLog::where(function ($q) {
            $q->where('status', '!=', 'success')->orWhere('is_suspicious', true);
        })->count();

        return view('super_admin.audit.index', compact(
            'logs',
            'modules',
            'totalLogs',
            'totalSuccess',
            'totalAlerts',
            'search',
            'module',
            'role',
            'status'
        ));
    }

    public function exportCsv()
    {
        $logs = AuditLog::latest()->limit(500)->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="audit_platform_' . date('Ymd_His') . '.csv"',
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Waktu', 'Aktor', 'Peran', 'Modul', 'Event', 'Keterangan', 'IP Address', 'Status']);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->created_at ? $log->created_at->toDateTimeString() : '',
                    $log->user_name,
                    $log->role,
                    $log->module,
                    $log->event,
                    $log->description,
                    $log->ip_address,
                    $log->status,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
