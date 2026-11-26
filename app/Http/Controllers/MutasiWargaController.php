<?php

namespace App\Http\Controllers;

use App\Models\MutasiWarga;
use App\Models\Warga;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MutasiWargaController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            abort(403, 'Akses ditolak.');
        }

        $query = MutasiWarga::with(['warga', 'creator'])->orderBy('tanggal_mutasi', 'desc');

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }
        if ($user->isAdminRt()) {
            $query->where(function ($q) use ($user) {
                $q->where('rt_asal', $user->rt_number)
                  ->orWhere('rt_tujuan', $user->rt_number);
            });
        } elseif ($request->filled('rt_number')) {
            $rt = $request->rt_number;
            $query->where(function ($q) use ($rt) {
                $q->where('rt_asal', $rt)
                  ->orWhere('rt_tujuan', $rt);
            });
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama_warga', 'like', "%{$s}%")
                  ->orWhere('nik', 'like', "%{$s}%")
                  ->orWhere('alasan', 'like', "%{$s}%");
            });
        }

        $mutasis = $query->paginate(15)->withQueryString();

        // Metrics
        $metricQuery = MutasiWarga::query();
        if ($user->isAdminRt()) {
            $metricQuery->where(function ($q) use ($user) {
                $q->where('rt_asal', $user->rt_number)
                  ->orWhere('rt_tujuan', $user->rt_number);
            });
        }
        $totalMasuk = (clone $metricQuery)->where('tipe', 'masuk')->count();
        $totalKeluar = (clone $metricQuery)->where('tipe', 'keluar')->count();
        $totalMutasi = (clone $metricQuery)->count();

        return view('mutasi.index', compact('mutasis', 'totalMasuk', 'totalKeluar', 'totalMutasi'));
    }

    public function create()
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            abort(403, 'Akses ditolak.');
        }

        $wargaQuery = Warga::where('status_warga', '!=', 'pindah')->orderBy('nama');
        if ($user->isAdminRt()) {
            $wargaQuery->where('rt_number', $user->rt_number);
        }
        $wargas = $wargaQuery->get();

        return view('mutasi.create', compact('wargas'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            abort(403, 'Akses ditolak.');
        }

        if ($user->isAdminRt() && $request->rt_asal !== $user->rt_number && $request->rt_tujuan !== $user->rt_number) {
            abort(403, 'Akses ditolak: Pendaftaran mutasi wajib melibatkan wilayah RT Anda.');
        }

        $request->validate([
            'tipe' => 'required|in:masuk,keluar',
            'warga_id' => 'nullable|exists:wargas,id',
            'nama_warga' => 'required|string|max:255',
            'nik' => 'nullable|string|max:25',
            'tanggal_mutasi' => 'required|date',
            'alasan' => 'required|string',
            'rt_asal' => 'nullable|string',
            'rt_tujuan' => 'nullable|string',
            'alamat_asal' => 'nullable|string',
            'alamat_tujuan' => 'nullable|string',
            'dokumen_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        $dokumenPath = null;
        if ($request->hasFile('dokumen_pendukung')) {
            $dokumenPath = $request->file('dokumen_pendukung')->store('dokumen_mutasi', 'public');
        }

        $mutasi = MutasiWarga::create([
            'warga_id' => $request->warga_id,
            'nama_warga' => $request->nama_warga,
            'nik' => $request->nik,
            'tipe' => $request->tipe,
            'rt_asal' => $request->rt_asal,
            'rt_tujuan' => $request->rt_tujuan,
            'alamat_asal' => $request->alamat_asal,
            'alamat_tujuan' => $request->alamat_tujuan,
            'tanggal_mutasi' => $request->tanggal_mutasi,
            'alasan' => $request->alasan,
            'dokumen_pendukung' => $dokumenPath,
            'status' => 'selesai',
            'catatan' => $request->catatan,
            'created_by' => $user->id,
        ]);

        // If mutasi keluar, update citizen status to 'pindah' without deleting record
        if ($request->tipe === 'keluar' && $request->warga_id) {
            $warga = Warga::find($request->warga_id);
            if ($warga) {
                $warga->status_warga = 'pindah';
                $warga->save();
            }
        } elseif ($request->tipe === 'masuk' && $request->warga_id) {
            $warga = Warga::find($request->warga_id);
            if ($warga) {
                $warga->status_warga = 'aktif';
                if ($request->rt_tujuan) {
                    $warga->rt_number = $request->rt_tujuan;
                }
                $warga->save();
            }
        }

        AuditLogger::log(
            'mutate',
            'Mutasi Warga',
            $mutasi->id,
            "Pencatatan mutasi {$request->tipe} atas nama '{$request->nama_warga}' (NIK: " . ($request->nik ?: '-') . ") berhasil disimpan oleh {$user->name}.",
            null,
            $mutasi->toArray(),
            $request->rt_tujuan ?: $request->rt_asal
        );

        return redirect()->route('mutasi.index')->with('success', 'Data mutasi kependudukan berhasil dicatat!');
    }

    public function show($id)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            abort(403, 'Akses ditolak.');
        }

        $mutasi = MutasiWarga::with(['warga', 'creator'])->findOrFail($id);

        if ($user->isAdminRt() && $mutasi->rt_asal !== $user->rt_number && $mutasi->rt_tujuan !== $user->rt_number) {
            abort(403, 'Akses ditolak: Data mutasi tidak berkaitan dengan wilayah RT Anda.');
        }

        return view('mutasi.show', compact('mutasi'));
    }
}
