<?php

namespace App\Http\Controllers;

use App\Models\Posyandu;
use App\Models\Warga; // Wajib ditambahin buat manggil data warga
use Illuminate\Http\Request;
use Carbon\Carbon; // Wajib ditambahin buat fitur hitung umur otomatis

class PosyanduController extends Controller
{
    public function index()
    {
        // ==========================================
        // 1. KODINGAN ASLI LU (Jadwal & Rekam Medis)
        // ==========================================
        try {
            $jadwal = Posyandu::orderBy('tanggal', 'asc')->get();
        } catch (\Exception $e) {
            $jadwal = collect();
        }

        try {
            $anak = \App\Models\PosyanduAnak::orderBy('created_at', 'desc')->get();
        } catch (\Exception $e) {
            $anak = collect();
        }

        try {
            $bumils = \App\Models\PosyanduBumil::orderBy('created_at', 'desc')->get();
        } catch (\Exception $e) {
            $bumils = collect();
        }


        // ==========================================
        // 2. TAMBAHAN BARU (Target Warga Posyandu)
        // ==========================================
        try {
            // Hitung patokan umur dari hari ini
            $batasBalita = Carbon::now()->subYears(5)->format('Y-m-d');
            $minimalUmur = Carbon::now()->subYears(18)->format('Y-m-d');
            $maksimalUmur = Carbon::now()->subYears(45)->format('Y-m-d');

            // Tarik otomatis data anak (Umur 0-5 tahun)
            $targetAnak = Warga::where(function($query) {
                                    $query->where('is_kk', false)
                                          ->orWhere('is_kk', 0)
                                          ->orWhere('is_kk', '0');
                                })
                                ->where('tanggal_lahir', '>=', $batasBalita)
                                ->orderBy('nama', 'asc')
                                ->get();

            // Tarik otomatis data ibu-ibu (Perempuan, Umur 18-45 tahun)
            $targetBumil = Warga::where(function($query) {
                                    $query->where('is_kk', false)
                                          ->orWhere('is_kk', 0)
                                          ->orWhere('is_kk', '0');
                                })
                                ->where('gender', 'P')
                                ->whereBetween('tanggal_lahir', [$maksimalUmur, $minimalUmur])
                                ->orderBy('nama', 'asc')
                                ->get();

        } catch (\Exception $e) {
            // Kalau misal kolom tanggal_lahir belum kekonek, nggak akan bikin web lu error
            $targetAnak = collect();
            $targetBumil = collect();
        }

        // Semua data dioper ke halaman view
        return view('posyandu.index', compact('jadwal', 'anak', 'bumils', 'targetAnak', 'targetBumil'));
    }

    public function create()
    {
        return view('posyandu.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'lokasi' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string|max:2000',
        ]);

        Posyandu::create($validated);

        return redirect()->route('posyandu.index')->with('success', 'Jadwal posyandu berhasil disimpan.');
    }
}
