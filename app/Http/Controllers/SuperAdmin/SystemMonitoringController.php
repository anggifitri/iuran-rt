<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\SystemMonitoringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SystemMonitoringController extends Controller
{
    protected SystemMonitoringService $monitoringService;

    public function __construct(SystemMonitoringService $monitoringService)
    {
        $this->monitoringService = $monitoringService;
    }

    /**
     * Display the main Super Admin Monitoring Center
     */
    public function index(Request $request)
    {
        $incidentFilter = $request->query('filter', 'all');
        $data = $this->monitoringService->getFullMonitoringData($incidentFilter);

        return view('super_admin.monitoring.index', $data);
    }

    /**
     * API endpoint for live auto-refresh without page reload
     */
    public function apiMetrics(Request $request): JsonResponse
    {
        $incidentFilter = $request->query('filter', 'all');
        $data = $this->monitoringService->getFullMonitoringData($incidentFilter);

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Explicit on-demand database integrity check
     */
    public function integrityCheck(Request $request)
    {
        try {
            $driver = DB::getDriverName();
            if ($driver === 'sqlite') {
                $check = DB::select("PRAGMA quick_check;");
                $firstRow = !empty($check) ? (array) $check[0] : [];
                $result = reset($firstRow);

                if ($result === 'ok') {
                    return back()->with('success', 'Diagnostik Integritas Database SQLite Berhasil: Struktur file database 100% konsisten (PRAGMA quick_check: ok).');
                } else {
                    return back()->with('warning', 'Pemeriksaan Integritas Database: ' . $result);
                }
            } else {
                DB::select("SELECT 1");
                return back()->with('success', 'Koneksi database ' . strtoupper($driver) . ' aktif dan responsif.');
            }
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal melakukan uji integritas database: ' . $e->getMessage());
        }
    }
}
