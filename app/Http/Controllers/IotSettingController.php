<?php

namespace App\Http\Controllers;

use App\Models\IotSetting;
use App\Models\RfidLog;
use App\Models\Warga;
use App\Models\PembayaranIuran;
use App\Services\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class IotSettingController extends Controller
{
    /**
     * Pastikan hanya Super Admin dan Admin RW yang dapat mengakses pengaturan utama.
     */
    protected function authorizeAccess(): void
    {
        $user = Auth::user();
        if (!$user || (!$user->isAdminRw() && !$user->isSuperAdmin())) {
            abort(403, 'Akses terbatas untuk Super Admin dan Admin RW.');
        }
    }

    /**
     * Pastikan Super Admin, Admin RW, atau Admin RT yang dapat mengakses (Operasional RFID Multi-Fungsi).
     */
    protected function authorizeAdminStaffAccess(): void
    {
        $user = Auth::user();
        if (!$user || (!$user->isAdminRw() && !$user->isSuperAdmin() && !$user->isAdminRt() && $user->role !== 'admin')) {
            abort(403, 'Akses terbatas untuk pengurus (Super Admin, Admin RW, Admin RT).');
        }
    }

    /**
     * Dashboard Pengaturan API & Log RFID Terpusat.
     */
    public function index(Request $request)
    {
        $this->authorizeAccess();

        $setting = IotSetting::current();

        $statusFilter = $request->query('status');
        $search = $request->query('q');

        $logsQuery = RfidLog::with('warga')->latest();

        if ($statusFilter && in_array($statusFilter, ['registered', 'unregistered', 'rejected_token', 'rejected_geofence', 'api_disabled'], true)) {
            $logsQuery->where('status', $statusFilter);
        }

        if ($search) {
            $logsQuery->where(function ($q) use ($search) {
                $q->where('uid', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhere('device_ip', 'like', "%{$search}%")
                  ->orWhereHas('warga', function ($wq) use ($search) {
                      $wq->where('nama', 'like', "%{$search}%")
                         ->orWhere('nik', 'like', "%{$search}%");
                  });
            });
        }

        $logs = $logsQuery->paginate(15)->withQueryString();

        // Metrik Ringkasan Hari Ini & Keseluruhan
        $todayStart = Carbon::today('Asia/Jakarta');
        $stats = [
            'total_today' => RfidLog::where('created_at', '>=', $todayStart)->count(),
            'registered_today' => RfidLog::where('created_at', '>=', $todayStart)->where('status', 'registered')->count(),
            'unregistered_today' => RfidLog::where('created_at', '>=', $todayStart)->where('status', 'unregistered')->count(),
            'rejected_today' => RfidLog::where('created_at', '>=', $todayStart)->whereIn('status', ['rejected_token', 'api_disabled'])->count(),
            'total_all' => RfidLog::count(),
        ];

        return view('iot.index', compact('setting', 'logs', 'stats', 'statusFilter', 'search'));
    }

    /**
     * Perbarui Pengaturan API & Smart Gate.
     */
    public function update(Request $request)
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'is_active' => 'required|boolean',
            'gate_name' => 'required|string|max:100',
            'api_token' => 'required|string|min:16|max:64',
            'gate_latitude' => 'nullable|numeric|between:-90,90',
            'gate_longitude' => 'nullable|numeric|between:-180,180',
            'geofence_radius' => 'required|integer|min:5|max:500',
        ], [
            'gate_name.required' => 'Nama gerbang wajib diisi.',
            'api_token.required' => 'Token API wajib diisi.',
            'api_token.min' => 'Token API minimal 16 karakter.',
            'geofence_radius.min' => 'Radius geofencing minimal 5 meter.',
            'geofence_radius.max' => 'Radius geofencing maksimal 500 meter.',
        ]);

        $setting = IotSetting::current();
        $setting->update($validated);

        AuditLogger::log(
            'UPDATE_IOT_SETTINGS',
            'IoT & Smart Gate',
            $setting->id,
            "Pengurus memperbarui konfigurasi IoT Smart Gate & API Token.",
            null,
            $setting->toArray(),
            '000',
            'success',
            'PENGATURAN'
        );

        return back()->with('success', 'Pengaturan API IoT & Lokasi Smart Gate berhasil disimpan.');
    }

    /**
     * Generate Token API Acak Baru (32 Karakter).
     */
    public function generateToken(Request $request)
    {
        $this->authorizeAccess();

        $newToken = Str::random(32);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'token' => $newToken,
            ]);
        }

        $setting = IotSetting::current();
        $setting->update(['api_token' => $newToken]);

        return back()->with('success', 'Token API baru berhasil digenerate.');
    }

    /**
     * Mengatur Mode Pemindaian RFID Aktif (Smart Gate, Rekam KTP, atau Setoran Iuran).
     * Endpoint: POST /iot/scan-mode
     */
    public function setScanMode(Request $request)
    {
        $this->authorizeAdminStaffAccess();

        $validated = $request->validate([
            'mode' => 'required|string|in:smart_gate,rekam_ktp,iuran',
        ], [
            'mode.required' => 'Parameter mode pemindaian wajib diisi.',
            'mode.in' => 'Mode pemindaian harus bernilai: smart_gate, rekam_ktp, atau iuran.',
        ]);

        $mode = $validated['mode'];

        if ($mode === 'smart_gate') {
            Cache::forget('rfid_active_mode');
            $activeMode = 'smart_gate';
            $expiresIn = 0;
        } else {
            // Kunci mode aktif di Cache selama 90 detik
            Cache::put('rfid_active_mode', $mode, now()->addSeconds(90));
            $activeMode = $mode;
            $expiresIn = 90;
        }

        return response()->json([
            'success' => true,
            'mode' => $activeMode,
            'active_mode' => $activeMode,
            'expires_in' => $expiresIn,
            'message' => "Mode RFID aktif berhasil dialihkan ke '{$activeMode}'.",
        ]);
    }

    /**
     * Endpoint Polling Ringan untuk Scan RFID Terbaru Berdasarkan Konteks (Iuran / Rekam KTP).
     * Endpoint: GET /iot/latest-scan?context=iuran&since=...
     */
    public function getLatestScan(Request $request)
    {
        $this->authorizeAdminStaffAccess();
        $user = Auth::user();

        $context = $request->query('context', 'iuran');
        $since = (float) $request->query('since', 0);

        // 1. Ambil dari Cache context khusus jika tersedia
        $scan = Cache::get('rfid_last_scan_' . $context);

        // 2. Jika tidak ada di cache konteks, cari log terbaru dengan konteks tersebut dalam 15 detik terakhir
        if (!$scan) {
            $threshold = now()->subSeconds(15);
            $log = RfidLog::with('warga')
                ->where('context', $context)
                ->where('created_at', '>=', $threshold)
                ->latest()
                ->first();

            if ($log) {
                $scan = [
                    'uid' => $log->uid,
                    'device_id' => 'ESP32-RC522',
                    'context' => $log->context,
                    'status' => $log->status,
                    'registered' => $log->status === 'registered' && $log->warga_id,
                    'scanned_at' => $log->created_at->toIso8601String(),
                    'timestamp_ms' => (float) ($log->created_at->timestamp * 1000),
                    'registered_warga' => $log->warga ? [
                        'id' => $log->warga->id,
                        'nama' => $log->warga->nama,
                        'rt_number' => $log->warga->rt_number,
                        'blok_rumah' => $log->warga->blok_rumah,
                        'nik' => $log->warga->nik,
                        'no_kk' => $log->warga->no_kk,
                    ] : null,
                ];
            }
        }

        if (!$scan) {
            return response()->json([
                'success' => true,
                'status' => 'waiting',
                'data' => null,
                'active_mode' => Cache::get('rfid_active_mode', 'smart_gate'),
            ]);
        }

        $timestampMs = (float) ($scan['timestamp_ms'] ?? 0);
        if ($since > 0 && $timestampMs <= $since) {
            return response()->json([
                'success' => true,
                'status' => 'waiting',
                'data' => null,
                'active_mode' => Cache::get('rfid_active_mode', 'smart_gate'),
            ]);
        }

        // 3. Perkaya data profil warga & status iuran untuk konteks iuran
        $wargaData = null;
        $iuranSummary = null;

        $wargaId = $scan['registered_warga']['id'] ?? null;
        $warga = $wargaId ? Warga::find($wargaId) : Warga::where('rfid_uid', $scan['uid'])->first();

        if ($warga) {
            $isSameRt = !$user->isAdminRt() || $warga->rt_number === $user->rt_number;
            $kkWarga = $warga->is_kk ? $warga : Warga::where('no_kk', $warga->no_kk)->where('is_kk', true)->first();

            $curMonth = (int) date('m');
            $curYear = (int) date('Y');

            $curPayment = PembayaranIuran::where(function ($q) use ($warga, $kkWarga) {
                    $q->where('warga_id', $warga->id);
                    if ($kkWarga) {
                        $q->orWhere('warga_id', $kkWarga->id);
                    }
                })
                ->where('bulan', $curMonth)
                ->where('tahun', $curYear)
                ->orderByDesc('id')
                ->first();

            $lastConfirmed = PembayaranIuran::where(function ($q) use ($warga, $kkWarga) {
                    $q->where('warga_id', $warga->id);
                    if ($kkWarga) {
                        $q->orWhere('warga_id', $kkWarga->id);
                    }
                })
                ->where('status', 'confirmed')
                ->orderByDesc('payment_date')
                ->orderByDesc('id')
                ->first();

            $statusIuranBulanIni = 'Belum Bayar';
            $badgeStatusClass = 'bg-danger-subtle text-danger border border-danger-subtle';
            if ($curPayment) {
                if ($curPayment->status === 'confirmed') {
                    $statusIuranBulanIni = 'Lunas';
                    $badgeStatusClass = 'bg-success-subtle text-success border border-success-subtle';
                } elseif ($curPayment->status === 'pending') {
                    $statusIuranBulanIni = 'Menunggu Konfirmasi';
                    $badgeStatusClass = 'bg-warning-subtle text-warning border border-warning-subtle';
                }
            }

            $wargaData = [
                'id' => $warga->id,
                'nama' => $warga->nama,
                'nik' => $warga->nik,
                'rt_number' => $warga->rt_number,
                'rw_number' => '018',
                'blok_rumah' => $warga->blok_rumah ?? '-',
                'no_kk' => $warga->no_kk ?? '-',
                'is_kk' => (bool) $warga->is_kk,
                'hubungan_keluarga' => $warga->is_kk ? 'Kepala Keluarga' : ($warga->hubungan_keluarga ?? 'Anggota Keluarga'),
                'is_same_rt' => $isSameRt,
                'kk_id' => $kkWarga?->id ?? $warga->id,
                'kk_nama' => $kkWarga?->nama ?? $warga->nama,
            ];

            $iuranSummary = [
                'periode_bulan' => \Carbon\Carbon::createFromDate($curYear, $curMonth, 1)->translatedFormat('F Y'),
                'status_bulan_ini' => $statusIuranBulanIni,
                'status_badge' => $badgeStatusClass,
                'nominal_standar' => 100000,
                'nominal_standar_formatted' => 'Rp 100.000',
                'terakhir_bayar' => $lastConfirmed && $lastConfirmed->payment_date 
                    ? $lastConfirmed->payment_date->translatedFormat('d F Y') 
                    : 'Belum pernah membayar',
                'terakhir_nominal' => $lastConfirmed 
                    ? 'Rp ' . number_format($lastConfirmed->amount_paid, 0, ',', '.') 
                    : '-',
            ];
        }

        return response()->json([
            'success' => true,
            'status' => 'detected',
            'active_mode' => Cache::get('rfid_active_mode', 'smart_gate'),
            'data' => [
                'uid' => $scan['uid'],
                'context' => $scan['context'] ?? $context,
                'registered' => (bool) ($warga ? true : ($scan['registered'] ?? false)),
                'timestamp_ms' => $timestampMs,
                'warga' => $wargaData,
                'iuran_summary' => $iuranSummary,
            ],
        ]);
    }

    /**
     * Endpoint Polling Log RFID JSON (untuk Auto-Refresh).
     */
    public function logs(Request $request)
    {
        $this->authorizeAccess();

        $statusFilter = $request->query('status');
        $query = RfidLog::with('warga')->latest();

        if ($statusFilter && in_array($statusFilter, ['registered', 'unregistered', 'rejected_token', 'rejected_geofence', 'api_disabled'], true)) {
            $query->where('status', $statusFilter);
        }

        $logs = $query->take(20)->get()->map(function ($log) {
            return [
                'id' => $log->id,
                'uid' => $log->uid,
                'status' => $log->status,
                'status_label' => $log->status_label,
                'status_badge' => $log->status_badge_class,
                'context' => $log->context,
                'context_label' => $log->context_label,
                'context_badge' => $log->context_badge_class,
                'method' => $log->method,
                'warga_nama' => $log->warga?->nama ?? 'Belum Terdaftar',
                'warga_rt' => $log->warga?->rt_number ? 'RT ' . $log->warga->rt_number : null,
                'message' => $log->message,
                'device_ip' => $log->device_ip ?? '-',
                'created_at' => $log->created_at ? $log->created_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i:s') : '-',
                'diff_for_humans' => $log->created_at ? $log->created_at->diffForHumans() : '',
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $logs,
        ]);
    }

    /**
     * Bersihkan riwayat log lama jika diperlukan.
     */
    public function clearLogs(Request $request)
    {
        $this->authorizeAccess();

        RfidLog::truncate();

        AuditLogger::log(
            'CLEAR_RFID_LOGS',
            'IoT & Smart Gate',
            null,
            "Pengurus membersihkan seluruh riwayat log RFID.",
            null,
            null,
            '000',
            'warning',
            'PENGATURAN'
        );

        return back()->with('success', 'Riwayat log RFID berhasil dibersihkan.');
    }
}
