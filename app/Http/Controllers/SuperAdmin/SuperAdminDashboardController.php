<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\PlatformModuleConfig;
use App\Models\User;
use App\Models\Warga;
use App\Models\WilayahRw;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuperAdminDashboardController extends Controller
{
    public function index()
    {
        // 1. Wilayah Operasional Utama: RW 018 (5 RT: RT 006 s/d RT 010)
        $rw018 = WilayahRw::where('rw_number', '018')->first();
        if (!$rw018) {
            $rw018 = WilayahRw::create([
                'rw_number' => '018',
                'nama_wilayah' => 'RW 018 Kelurahan Mekarjaya',
                'kelurahan' => 'Kelurahan Mekarjaya',
                'kecamatan' => 'Kecamatan Sukmajaya',
                'kota' => 'Kota Depok',
                'provinsi' => 'Jawa Barat',
                'total_rt' => 5,
                'status' => 'active',
                'ketua_rw_nama' => 'Dirga Pradipta',
                'ketua_rw_phone' => '081234567890',
            ]);
        } elseif ($rw018->total_rt != 5) {
            $rw018->update(['total_rt' => 5]);
        }

        $activeRtNumbers = ['006', '007', '008', '009', '010'];

        // 2. Core Operational Metrics (RW 018)
        $totalUsers = User::where('role', '!=', 'super_admin')->count();
        $totalAdminRw = User::where(function ($q) {
            $q->where('role', 'admin_rw')->orWhere(function ($sq) {
                $sq->where('role', 'admin')->where('rt_number', '000');
            });
        })->count();

        $totalAdminRt = User::where(function ($q) {
            $q->where('role', 'admin_rt')->orWhere('role', 'admin');
        })->whereIn('rt_number', $activeRtNumbers)->count();

        $totalWargaAccounts = User::where('role', 'warga')->count();
        $totalWargaData = Warga::whereIn('rt_number', $activeRtNumbers)->count();
        $totalKk = Warga::whereIn('rt_number', $activeRtNumbers)->where('is_kk', true)->count();

        // 3. Kas Operasional RW 018 (Saldo Kas Gabungan RW 018: Total Pemasukan - Total Pengeluaran)
        $totalKasGlobal = 0;
        $totalPemasukan = 0;
        $totalPengeluaran = 0;
        try {
            $pembayaranQuery = Pembayaran::where('status', '!=', 'dibatalkan')
                ->where('status', '!=', 'pending');
            $totalPemasukan = (clone $pembayaranQuery)->where('tipe', 'masuk')->sum('jumlah');
            $totalPengeluaran = (clone $pembayaranQuery)->where('tipe', 'keluar')->sum('jumlah');
            $totalKasGlobal = $totalPemasukan - $totalPengeluaran; // Saldo bersih gabungan RW 018 (Rp 66.910.000)
        } catch (\Throwable $e) {
            $totalKasGlobal = 0;
            $totalPemasukan = 0;
            $totalPengeluaran = 0;
        }

        // 4. Modul Platform (22 Modul)
        $totalModules = PlatformModuleConfig::count();
        $activeModules = PlatformModuleConfig::where('is_enabled_globally', true)->count();

        // 5. RT Distribution in RW 018 (Wilayah Operasional Aktif: RT 006 - RT 010)
        $rtList = [];
        foreach ($activeRtNumbers as $rtNum) {
            $adminRt = User::where(function ($q) {
                $q->where('role', 'admin_rt')->orWhere('role', 'admin');
            })->where('rt_number', $rtNum)->first();

            $wargaCount = Warga::where('rt_number', $rtNum)->count();
            $kkCount = Warga::where('rt_number', $rtNum)->where('is_kk', true)->count();

            $rtList[] = [
                'rt_number' => $rtNum,
                'admin_name' => $adminRt ? $adminRt->name : 'Belum Ditugaskan',
                'admin_email' => $adminRt ? $adminRt->email : null,
                'admin_user_id' => $adminRt ? $adminRt->id : null,
                'warga_count' => $wargaCount,
                'kk_count' => $kkCount,
            ];
        }

        // 6. Recent Audit Logs (RW 018 & Super Admin Actions)
        $recentAuditLogs = AuditLog::latest()->limit(8)->get();

        // 7. System Health Metrics
        $dbDriver = DB::getDriverName();
        $dbSize = '0 MB';
        if ($dbDriver === 'sqlite') {
            $dbPath = database_path('database.sqlite');
            if (file_exists($dbPath)) {
                $dbSize = round(filesize($dbPath) / 1024 / 1024, 2) . ' MB';
            }
        }

        $systemHealth = [
            'db_driver' => strtoupper($dbDriver),
            'db_size' => $dbSize,
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_os' => PHP_OS,
            'status' => 'Optimal (Single-RW Active)',
        ];

        return view('super_admin.dashboard', compact(
            'rw018',
            'totalUsers',
            'totalAdminRw',
            'totalAdminRt',
            'totalWargaAccounts',
            'totalWargaData',
            'totalKk',
            'totalKasGlobal',
            'totalPemasukan',
            'totalPengeluaran',
            'totalModules',
            'activeModules',
            'rtList',
            'recentAuditLogs',
            'systemHealth'
        ));
    }
}
