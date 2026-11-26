<?php

namespace App\Http\Controllers;

use App\Models\IotSetting;
use App\Models\RfidLog;
use App\Models\Warga;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SmartGateController extends Controller
{
    /**
     * Halaman Monitoring Smart Gate (Visualisasi Animasi Palang Real-time).
     */
    public function index(Request $request)
    {
        $setting = IotSetting::current();
        $recentLogs = RfidLog::with('warga')->latest()->take(10)->get();

        $user = Auth::user();
        $warga = $user->warga ?? Warga::where('nik', $user->nik)->first() ?? Warga::where('nama', $user->name)->first();

        return view('smart_gate.index', compact('setting', 'recentLogs', 'user', 'warga'));
    }

    /**
     * Endpoint Polling Status Real-time (dipanggil setiap 1.5 - 2 detik).
     */
    public function liveStatus(Request $request)
    {
        $setting = IotSetting::current();

        // PENTING — Isolasi Smart Gate: HANYA log dengan context = 'smart_gate' (atau 'general')
        // atau method = 'gps_emergency' yang dapat memicu animasi palang terbuka/ditolak!
        $latestGateLog = RfidLog::with('warga')
            ->where(function ($q) {
                $q->whereIn('context', ['smart_gate', 'general'])
                  ->orWhere('method', 'gps_emergency');
            })
            ->latest()
            ->first();

        $secondsAgo = $latestGateLog ? (int) $latestGateLog->created_at->diffInSeconds(now()) : 9999;

        $gateState = 'standby'; // 'standby' (tertutup), 'opened', 'rejected'
        $gateNotice = null;

        // Cek apakah ada aktivitas gerbang dalam 10 detik terakhir
        if ($latestGateLog && $secondsAgo <= 10) {
            if ($latestGateLog->status === 'registered') {
                $gateState = 'opened';
                $methodText = match($latestGateLog->method) {
                    'gps_emergency' => 'Darurat GPS',
                    'simulation' => 'Simulasi Sistem',
                    default => 'Kartu RFID',
                };
                $wargaNama = $latestGateLog->warga?->nama ?? 'Warga Terdaftar';
                $wargaRt = $latestGateLog->warga?->rt_number ? ' (RT ' . $latestGateLog->warga->rt_number . ')' : '';
                $gateNotice = [
                    'type' => 'success',
                    'title' => 'Palang Terbuka — Akses Diterima',
                    'message' => "Selamat Datang, {$wargaNama}{$wargaRt}",
                    'detail' => "Metode: {$methodText} · UID: {$latestGateLog->uid}",
                    'seconds_remaining' => max(0, 6 - $secondsAgo),
                ];
            } elseif (in_array($latestGateLog->status, ['unregistered', 'rejected_token', 'rejected_geofence', 'api_disabled'], true)) {
                $gateState = 'rejected';
                $rejectReason = match($latestGateLog->status) {
                    'unregistered' => 'Kartu E-KTP Tidak Terdaftar di Database Warga',
                    'rejected_geofence' => 'Di Luar Batas Jarak Gerbang (GPS Ditolak)',
                    'rejected_token' => 'Token API Perangkat Tidak Sah',
                    'api_disabled' => 'Sistem IoT Sedang Dinonaktifkan',
                    default => 'Akses Ditolak',
                };
                $gateNotice = [
                    'type' => 'danger',
                    'title' => 'Akses Ditolak — Palang Tetap Tertutup',
                    'message' => $rejectReason,
                    'detail' => $latestGateLog->message ?: "UID: {$latestGateLog->uid}",
                    'seconds_remaining' => max(0, 6 - $secondsAgo),
                ];
            }
        }

        $recentLogs = RfidLog::with('warga')->latest()->take(10)->get()->map(function ($log) {
            return [
                'id' => $log->id,
                'uid' => $log->uid,
                'status' => $log->status,
                'status_label' => $log->status_label,
                'status_badge' => $log->status_badge_class,
                'context' => $log->context,
                'method' => $log->method,
                'warga_nama' => $log->warga?->nama ?? 'Belum Terdaftar',
                'warga_rt' => $log->warga?->rt_number ? 'RT ' . $log->warga->rt_number : null,
                'message' => $log->message,
                'created_at' => $log->created_at ? $log->created_at->timezone('Asia/Jakarta')->translatedFormat('H:i:s') . ' WIB' : '-',
                'diff_for_humans' => $log->created_at ? $log->created_at->diffForHumans() : '',
            ];
        });

        return response()->json([
            'success' => true,
            'gate_name' => $setting->gate_name,
            'is_active' => (bool) $setting->is_active,
            'gate_state' => $gateState,
            'gate_notice' => $gateNotice,
            'last_log_id' => $latestGateLog?->id,
            'recent_logs' => $recentLogs,
        ]);
    }

    /**
     * Buka Portal Darurat via GPS Geofencing (Rumus Haversine).
     */
    public function emergencyOpen(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ], [
            'latitude.required' => 'Koordinat latitude perangkat wajib disertakan.',
            'longitude.required' => 'Koordinat longitude perangkat wajib disertakan.',
        ]);

        $setting = IotSetting::current();

        if (!$setting->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Sistem Smart Gate sedang dinonaktifkan oleh pengurus lingkungan.',
            ], 503);
        }

        if (is_null($setting->gate_latitude) || is_null($setting->gate_longitude)) {
            return response()->json([
                'success' => false,
                'message' => 'Titik koordinat Smart Gate belum diatur oleh Pengurus RW. Silakan atur di menu Pengaturan API & IoT.',
            ], 422);
        }

        $userLat = (float) $request->latitude;
        $userLng = (float) $request->longitude;
        $gateLat = (float) $setting->gate_latitude;
        $gateLng = (float) $setting->gate_longitude;

        // Rumus Haversine untuk kalkulasi jarak akurat dalam satuan meter
        $earthRadius = 6371000; // Radius bumi dalam meter
        $latFrom = deg2rad($userLat);
        $lonFrom = deg2rad($userLng);
        $latTo = deg2rad($gateLat);
        $lonTo = deg2rad($gateLng);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        $distance = $angle * $earthRadius; // Jarak dalam meter

        $radius = (int) ($setting->geofence_radius ?: 20);

        $user = Auth::user();
        $warga = $user->warga 
            ?? Warga::where('nik', $user->nik)->first() 
            ?? Warga::where('nama', $user->name)->first();

        // 1. Jika dalam radius geofencing (<= radius meter) -> AKSES DITERIMA
        if ($distance <= $radius) {
            $formattedDist = number_format($distance, 1);
            $logMessage = "Buka darurat GPS disetujui (jarak {$formattedDist} meter dari {$setting->gate_name})";

            RfidLog::create([
                'uid' => 'GPS-EMERGENCY',
                'warga_id' => $warga?->id,
                'status' => 'registered',
                'context' => 'smart_gate',
                'method' => 'gps_emergency',
                'device_ip' => $request->ip(),
                'message' => $logMessage,
                'raw_payload' => [
                    'latitude' => $userLat,
                    'longitude' => $userLng,
                    'distance_meters' => round($distance, 2),
                    'radius_limit' => $radius,
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                ],
            ]);

            AuditLogger::log(
                'SMART_GATE_EMERGENCY_OPEN',
                'Smart Gate & Keamanan',
                $user->id,
                "Akses buka portal darurat GPS berhasil oleh {$user->name} (Jarak: {$formattedDist} m).",
                null,
                ['distance' => $distance, 'radius' => $radius],
                $user->rt_number ?? '000',
                'success',
                'SMART_GATE'
            );

            return response()->json([
                'success' => true,
                'message' => "Buka portal darurat GPS disetujui! Palang gerbang dibuka (Jarak Anda: {$formattedDist} m dari gerbang).",
                'distance_meters' => round($distance, 1),
                'radius' => $radius,
                'warga_nama' => $user->name,
            ], 200);
        }

        // 2. Jika di luar radius geofencing (> radius meter) -> AKSES DITOLAK
        $formattedDist = number_format($distance, 1);
        $logMessage = "Di luar radius gerbang (jarak {$formattedDist} m, batas {$radius} m)";

        RfidLog::create([
            'uid' => 'GPS-EMERGENCY',
            'warga_id' => $warga?->id,
            'status' => 'rejected_geofence',
            'context' => 'smart_gate',
            'method' => 'gps_emergency',
            'device_ip' => $request->ip(),
            'message' => $logMessage,
            'raw_payload' => [
                'latitude' => $userLat,
                'longitude' => $userLng,
                'distance_meters' => round($distance, 2),
                'radius_limit' => $radius,
                'user_id' => $user->id,
                'user_name' => $user->name,
            ],
        ]);

        return response()->json([
            'success' => false,
            'message' => "Gagal membuka gerbang: Posisi Anda berada {$formattedDist} meter dari gerbang (batas radius maksimal {$radius} meter).",
            'distance_meters' => round($distance, 1),
            'radius' => $radius,
        ], 403);
    }
}
