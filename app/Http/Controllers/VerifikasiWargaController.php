<?php

namespace App\Http\Controllers;

use App\Models\Warga;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifikasiWargaController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }

        // Untuk Admin RW, seluruh fitur verifikasi kini dipusatkan di halaman Database Warga
        if ($user->isAdminRw()) {
            $params = array_filter([
                'status' => $request->status,
                'search' => $request->search,
            ]);
            return redirect()->route('warga.index', $params);
        }

        $query = Warga::query()->orderBy('created_at', 'desc');

        if ($user->isAdminRt()) {
            $query->where('rt_number', $user->rt_number);
        } elseif ($request->filled('rt_number')) {
            $query->where('rt_number', $request->rt_number);
        }

        if ($request->filled('status')) {
            $query->where('status_verifikasi', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama', 'like', "%{$s}%")
                  ->orWhere('nik', 'like', "%{$s}%")
                  ->orWhere('no_kk', 'like', "%{$s}%");
            });
        }

        $wargas = $query->paginate(15)->withQueryString();

        // Metrics
        $metricQuery = Warga::query();
        if ($user->isAdminRt()) {
            $metricQuery->where('rt_number', $user->rt_number);
        }
        $totalMenunggu = (clone $metricQuery)->where('status_verifikasi', 'menunggu')->count();
        $totalTerverifikasi = (clone $metricQuery)->where('status_verifikasi', 'terverifikasi')->count();
        $totalDitolak = (clone $metricQuery)->where('status_verifikasi', 'ditolak')->count();
        $totalPerbaikan = (clone $metricQuery)->where('status_verifikasi', 'perbaikan')->count();

        return view('verifikasi.index', compact(
            'wargas',
            'totalMenunggu',
            'totalTerverifikasi',
            'totalDitolak',
            'totalPerbaikan'
        ));
    }

    public function show($id)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }

        $warga = Warga::findOrFail($id);
        if ($user->isAdminRt() && $warga->rt_number !== $user->rt_number) {
            return redirect()->route('verifikasi.index')->with('error', 'Akses ditolak untuk data RT lain.');
        }

        return view('verifikasi.show', compact('warga'));
    }

    public function updateStatus(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'status_verifikasi' => 'required|in:terverifikasi,ditolak,perbaikan,menunggu',
            'catatan_verifikasi' => 'nullable|string|max:1000',
        ]);

        $warga = Warga::findOrFail($id);
        if ($user->isAdminRt() && $warga->rt_number !== $user->rt_number) {
            return redirect()->route('verifikasi.index')->with('error', 'Akses ditolak.');
        }

        $oldStatus = $warga->status_verifikasi;
        $warga->status_verifikasi = $request->status_verifikasi;
        $warga->catatan_verifikasi = $request->catatan_verifikasi;
        $warga->verifikator_id = $user->id;
        $warga->tanggal_verifikasi = now();
        $warga->save();

        AuditLogger::log(
            $request->status_verifikasi === 'terverifikasi' ? 'verify' : ($request->status_verifikasi === 'ditolak' ? 'reject' : 'revise'),
            'Verifikasi Warga',
            $warga->id,
            "Verifikasi data warga '{$warga->nama}' diubah dari '{$oldStatus}' menjadi '{$request->status_verifikasi}' oleh {$user->name}. Catatan: " . ($request->catatan_verifikasi ?: '-'),
            ['status_verifikasi' => $oldStatus],
            ['status_verifikasi' => $warga->status_verifikasi, 'catatan' => $warga->catatan_verifikasi],
            $warga->rt_number
        );

        if ($user->isAdminRw()) {
            return redirect()->route('warga.index', ['status' => $request->status_verifikasi])->with('success', "Status verifikasi warga '{$warga->nama}' berhasil diperbarui menjadi " . strtoupper($request->status_verifikasi) . ".");
        }

        return redirect()->route('verifikasi.index')->with('success', "Status verifikasi warga '{$warga->nama}' berhasil diperbarui menjadi " . strtoupper($request->status_verifikasi) . ".");
    }
}
