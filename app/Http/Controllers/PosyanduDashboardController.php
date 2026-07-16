<?php

namespace App\Http\Controllers;

use App\Models\PosyanduAnak;
use App\Models\PosyanduBumil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PosyanduDashboardController extends Controller
{
    public function index()
    {
        $anak = PosyanduAnak::latest()->get();
        $bumils = PosyanduBumil::latest()->get();

        return view('posyandu.index', compact('anak', 'bumils'));
    }

    public function storeAnak(Request $request)
    {
        $validated = $request->validate([
            'nama_anak' => 'required|string|max:255',
            'umur_bulan' => 'required|integer',
            'berat_badan' => 'required|numeric',
            'tinggi_badan' => 'required|numeric',
            'status_tumbuh' => 'required|string',
            'solusi' => 'nullable|string',
            'imunisasi_checked' => 'nullable|array',
        ]);

        PosyanduAnak::create($validated);

        return redirect()->route('posyandu.index')->with('success', 'Rekam medis anak berhasil disimpan.');
    }

    public function storeBumil(Request $request)
    {
        $validated = $request->validate([
            'nama_ibu' => 'required|string|max:255',
            'usia_kehamilan_minggu' => 'required|integer',
            'berat_badan' => 'required|numeric',
            'tekanan_darah' => 'required|string',
            'lila' => 'required|numeric',
            'status_kesehatan' => 'required|string',
            'solusi' => 'nullable|string',
        ]);

        PosyanduBumil::create($validated);

        return redirect()->route('posyandu.index')->with('success', 'Rekam medis ibu hamil berhasil disimpan.');
    }
}
