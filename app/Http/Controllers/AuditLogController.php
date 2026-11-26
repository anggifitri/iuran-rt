<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AuditLogController extends Controller
{
    /**
     * Main Audit & Activity Index with 4 Integrated Tabs
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->isWarga()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk pengurus lingkungan.');
        }

        $isAdminRw = $user->isAdminRw();
        $isAdminRt = $user->isAdminRt();
        $isWarga   = $user->isWarga();

        $activeTab = $request->get('tab', 'logs');
        if (!in_array($activeTab, ['logs', 'timeline', 'security', 'analytics'])) {
            $activeTab = 'logs';
        }

        // Base Query with Role-Based Scoping (RBAC)
        $baseQuery = AuditLog::with('user');

        if ($isAdminRw) {
            // Admin RW can inspect everything across RW 018
        } elseif ($isAdminRt) {
            // Admin RT sees logs related to their RT or their own actions
            $rt = $user->rt_number;
            $baseQuery->where(function ($q) use ($rt, $user) {
                $q->where('rt_number', $rt)
                  ->orWhere('user_id', $user->id);
            });
        } else {
            // Warga only sees their own account activities
            $baseQuery->where('user_id', $user->id);
        }

        // Apply Common Filters
        $filteredQuery = clone $baseQuery;

        if ($request->filled('search')) {
            $s = trim($request->search);
            $filteredQuery->where(function ($q) use ($s) {
                $q->where('description', 'like', "%{$s}%")
                  ->orWhere('user_name', 'like', "%{$s}%")
                  ->orWhere('record_id', 'like', "%{$s}%")
                  ->orWhere('ip_address', 'like', "%{$s}%")
                  ->orWhere('module', 'like', "%{$s}%");
            });
        }

        if ($request->filled('module') && $request->module !== 'semua') {
            $filteredQuery->where('module', $request->module);
        }

        if ($request->filled('event') && $request->event !== 'semua') {
            $filteredQuery->where('event', $request->event);
        }

        if ($request->filled('status') && $request->status !== 'semua') {
            $filteredQuery->where('status', $request->status);
        }

        if ($request->filled('role') && $request->role !== 'semua' && !$isWarga) {
            $filteredQuery->where('role', $request->role);
        }

        if ($request->filled('rt_number') && $request->rt_number !== 'semua' && $isAdminRw) {
            $filteredQuery->where('rt_number', $request->rt_number);
        }

        if ($request->filled('date_from')) {
            $filteredQuery->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $filteredQuery->whereDate('created_at', '<=', $request->date_to);
        }

        // 1. Metric Cards Statistics (Real-time from Database)
        $totalLogs = (clone $baseQuery)->count();
        $totalChanges = (clone $baseQuery)->whereIn('event', ['create', 'update', 'delete', 'mutate'])->count();
        $activeUsersCount = (clone $baseQuery)->whereNotNull('user_id')->distinct('user_id')->count('user_id');
        $needsReviewCount = (clone $baseQuery)->where(function ($q) {
            $q->where('is_suspicious', true)
              ->orWhereIn('status', ['warning', 'failed', 'suspicious'])
              ->orWhereIn('event', ['delete', 'reject', 'unauthorized', 'login_failed']);
        })->count();

        // 2. Data for Tab 1: Logs Table
        $logs = (clone $filteredQuery)
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        // 3. Data for Tab 2: Chronological Timeline
        $timelineLogs = (clone $filteredQuery)
            ->latest('created_at')
            ->take(60)
            ->get()
            ->groupBy(function ($item) {
                $created = $item->created_at;
                if ($created->isToday()) return 'Hari Ini';
                if ($created->isYesterday()) return 'Kemarin';
                return $created->translatedFormat('l, d F Y');
            });

        // 4. Data for Tab 3: Security & Authentication Logs
        $securityQuery = (clone $baseQuery)->where(function ($q) {
            $q->whereIn('event', ['login', 'login_failed', 'logout', 'unauthorized', 'security_alert'])
              ->orWhere('module', 'Autentikasi & Keamanan')
              ->orWhere('is_suspicious', true)
              ->orWhere('status', 'suspicious');
        });

        if ($request->filled('search')) {
            $s = trim($request->search);
            $securityQuery->where(function ($q) use ($s) {
                $q->where('description', 'like', "%{$s}%")
                  ->orWhere('user_name', 'like', "%{$s}%")
                  ->orWhere('ip_address', 'like', "%{$s}%");
            });
        }

        $securityLogs = (clone $securityQuery)->latest('created_at')->paginate(15, ['*'], 'sec_page')->withQueryString();
        $failedLoginCount = (clone $securityQuery)->where('event', 'login_failed')->count();
        $unauthorizedCount = (clone $securityQuery)->where('event', 'unauthorized')->count();

        // 5. Data for Tab 4: System Analytics & Visual Charts (Chart.js)
        // A. Trend Aktivitas 14 Hari Terakhir
        $chartDates = [];
        $chartActivityCounts = [];
        for ($i = 13; $i >= 0; $i--) {
            $dt = Carbon::today()->subDays($i);
            $dateStr = $dt->format('Y-m-d');
            $labelStr = $dt->translatedFormat('d M');
            $chartDates[] = $labelStr;

            $countOnDay = (clone $baseQuery)->whereDate('created_at', $dateStr)->count();
            $chartActivityCounts[] = $countOnDay;
        }

        // B. Distribusi Aktivitas Berdasarkan Modul (Top 6 + Lainnya)
        $moduleStats = (clone $baseQuery)
            ->select('module', DB::raw('count(*) as total'))
            ->groupBy('module')
            ->orderByDesc('total')
            ->take(6)
            ->get();

        $chartModuleLabels = $moduleStats->pluck('module')->toArray();
        $chartModuleCounts = $moduleStats->pluck('total')->toArray();

        // C. Distribusi Aktivitas Berdasarkan Role
        $roleStats = (clone $baseQuery)
            ->select('role', DB::raw('count(*) as total'))
            ->groupBy('role')
            ->pluck('total', 'role')
            ->toArray();

        // D. Distribusi Status (Berhasil, Gagal, Peringatan, Mencurigakan)
        $statusStats = (clone $baseQuery)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Distinct lists for dropdowns
        $moduleList = AuditLog::select('module')->distinct()->pluck('module')->filter()->values();
        $eventList = AuditLog::select('event')->distinct()->pluck('event')->filter()->values();
        $rtList = ['006', '007', '008', '009', '010'];

        return view('audit.index', compact(
            'logs',
            'timelineLogs',
            'securityLogs',
            'activeTab',
            'totalLogs',
            'totalChanges',
            'activeUsersCount',
            'needsReviewCount',
            'failedLoginCount',
            'unauthorizedCount',
            'chartDates',
            'chartActivityCounts',
            'chartModuleLabels',
            'chartModuleCounts',
            'roleStats',
            'statusStats',
            'moduleList',
            'eventList',
            'rtList',
            'isAdminRw',
            'isAdminRt',
            'isWarga'
        ));
    }

    /**
     * Export Audit Logs into Printable Official PDF Document
     */
    public function exportPdf(Request $request)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            abort(403, 'Akses terbatas untuk pengurus lingkungan.');
        }

        $query = $this->buildFilteredQuery($request, $user);
        $logs = $query->latest('created_at')->take(300)->get();

        $printedAt = Carbon::now();
        $filterSummary = [
            'modul'     => $request->get('module') ?: 'Semua Modul',
            'event'     => $request->get('event') ?: 'Semua Event',
            'role'      => $request->get('role') ?: 'Semua Role',
            'rt'        => $request->get('rt_number') ?: ($user->isAdminRt() ? 'RT ' . $user->rt_number : 'Seluruh RW 018'),
            'date_from' => $request->get('date_from') ?: 'Awal',
            'date_to'   => $request->get('date_to') ?: 'Sekarang',
        ];

        // Log this export activity
        AuditLogger::log(
            event: 'export',
            module: 'Audit & Aktivitas',
            description: "Pengurus '{$user->name}' mengekspor dokumen audit cetak PDF (" . count($logs) . " rekaman).",
            status: 'success',
            actionType: 'EXPORT'
        );

        $ketuaRw = User::where('role', 'admin_rw')->orWhere('email', 'adminrw@rt.com')->orWhere('email', 'adminrw@nexanest.com')->first();
        $ketuaRwName = $ketuaRw ? $ketuaRw->name : 'Dirga Pradipta';

        return view('audit.export_pdf', compact('logs', 'printedAt', 'filterSummary', 'user', 'ketuaRwName'));
    }

    /**
     * Export Audit Logs into Microsoft Excel / CSV Stream
     */
    public function exportExcel(Request $request)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            abort(403, 'Akses terbatas untuk pengurus lingkungan.');
        }

        $query = $this->buildFilteredQuery($request, $user);
        $logs = $query->latest('created_at')->get();

        $filename = 'Audit_Aktivitas_RW018_' . date('Ymd_His') . '.csv';

        // Log this export activity
        AuditLogger::log(
            event: 'export',
            module: 'Audit & Aktivitas',
            description: "Pengurus '{$user->name}' mengekspor berkas spreadsheet audit CSV/Excel (" . count($logs) . " rekaman).",
            status: 'success',
            actionType: 'EXPORT'
        );

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($logs) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM so Excel opens it with proper encoding
            fputs($handle, "\xEF\xBB\xBF");

            // CSV Column Header
            fputcsv($handle, [
                'ID Log',
                'Waktu Kejadian (WIB)',
                'Nama Pengguna',
                'Role Pengguna',
                'Wilayah RT',
                'Modul',
                'Jenis Tindakan',
                'Status',
                'ID Objek',
                'Deskripsi Aktivitas',
                'Alamat IP',
                'Perangkat / Browser'
            ]);

            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->formatted_id,
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->user_name ?: 'Sistem',
                    strtoupper($log->role ?: 'GUEST'),
                    $log->rt_number ?: 'RW',
                    $log->module,
                    strtoupper($log->event),
                    strtoupper($log->status ?: 'SUCCESS'),
                    $log->record_id ?: '-',
                    $log->description,
                    $log->ip_address ?: '-',
                    $log->device_label ?: '-'
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Return JSON Diff Details for a specific Log
     */
    public function diffDetail($id)
    {
        $user = Auth::user();
        $log = AuditLog::findOrFail($id);

        // RBAC check
        if ($user->isWarga() && $log->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak untuk melihat detail perubahan data pengguna lain.');
        }

        if ($user->isAdminRt() && $log->rt_number && $log->rt_number !== $user->rt_number && $log->user_id !== $user->id) {
            abort(403, 'Akses terbatas untuk wilayah RT Anda.');
        }

        return response()->json([
            'success' => true,
            'id' => $log->formatted_id,
            'user_name' => $log->user_name,
            'module' => $log->module,
            'event' => strtoupper($log->event),
            'status' => strtoupper($log->status),
            'created_at' => $log->created_at->translatedFormat('d F Y, H:i:s') . ' WIB',
            'diffs' => $log->diff_details,
        ]);
    }

    /**
     * Helper to build scoped and filtered query
     */
    protected function buildFilteredQuery(Request $request, $user)
    {
        $query = AuditLog::with('user');

        if ($user->isAdminRw()) {
            if ($request->filled('rt_number') && $request->rt_number !== 'semua') {
                $query->where('rt_number', $request->rt_number);
            }
        } elseif ($user->isAdminRt()) {
            $rt = $user->rt_number;
            $query->where(function ($q) use ($rt, $user) {
                $q->where('rt_number', $rt)->orWhere('user_id', $user->id);
            });
        } else {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('description', 'like', "%{$s}%")
                  ->orWhere('user_name', 'like', "%{$s}%")
                  ->orWhere('module', 'like', "%{$s}%");
            });
        }

        if ($request->filled('module') && $request->module !== 'semua') {
            $query->where('module', $request->module);
        }

        if ($request->filled('event') && $request->event !== 'semua') {
            $query->where('event', $request->event);
        }

        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }

        if ($request->filled('role') && $request->role !== 'semua') {
            $query->where('role', $request->role);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return $query;
    }
}
