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
        // Ambil target anak (umur 0-5 tahun) dari warga
        $batasBalita = Carbon::now()->subYears(5)->format('Y-m-d');
        $targetAnak = Warga::where(function($query) {
            $query->where('is_kk', false)
                  ->orWhere('is_kk', 0)
                  ->orWhere('is_kk', '0');
        })
        ->where('tanggal_lahir', '>=', $batasBalita)
        ->orderBy('nama', 'asc')
        ->get();

        // Ambil target ibu hamil (wanita, umur 18-45 tahun)
        $minimalUmur = Carbon::now()->subYears(18)->format('Y-m-d');
        $maksimalUmur = Carbon::now()->subYears(45)->format('Y-m-d');
        $targetBumil = Warga::where(function($query) {
            $query->where('is_kk', false)
                  ->orWhere('is_kk', 0)
                  ->orWhere('is_kk', '0');
        })
        ->where('gender', 'P')
        ->whereBetween('tanggal_lahir', [$maksimalUmur, $minimalUmur])
        ->orderBy('nama', 'asc')
        ->get();

        return view('posyandu.create', compact('targetAnak', 'targetBumil'));
    }

    public function store(Request $request)
    {
        $type = $request->input('type');

        if ($type === 'anak') {
            $validated = $request->validate([
                'nama_anak' => 'required|string|max:255',
                'umur_bulan' => 'required|integer|min:0',
                'berat_badan' => 'required|numeric|min:0',
                'tinggi_badan' => 'required|numeric|min:0',
                'status_tumbuh' => 'required|string|max:100',
                'solusi' => 'nullable|string|max:2000',
                'imunisasi_checked' => 'nullable|array',
            ]);

            \App\Models\PosyanduAnak::create([
                'nama_anak' => $validated['nama_anak'],
                'umur_bulan' => $validated['umur_bulan'],
                'berat_badan' => $validated['berat_badan'],
                'tinggi_badan' => $validated['tinggi_badan'],
                'status_tumbuh' => $validated['status_tumbuh'],
                'solusi' => $validated['solusi'],
                'imunisasi_checked' => $validated['imunisasi_checked'] ?? [],
            ]);

            return redirect()->route('posyandu.index')->with('success', 'Rekam medis anak berhasil disimpan.');
        } elseif ($type === 'bumil') {
            $validated = $request->validate([
                'nama_ibu' => 'required|string|max:255',
                'usia_kehamilan_minggu' => 'required|integer|min:0',
                'berat_badan' => 'required|numeric|min:0',
                'tekanan_darah' => 'required|string|max:50',
                'lila' => 'required|numeric|min:0',
                'status_kesehatan' => 'required|string|max:100',
                'solusi' => 'nullable|string|max:2000',
            ]);

            \App\Models\PosyanduBumil::create($validated);

            return redirect()->route('posyandu.index')->with('success', 'Rekam medis ibu hamil berhasil disimpan.');
        } else {
            // Default: Jadwal Kegiatan Posyandu
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
}
