<?php

namespace App\Http\Controllers;

use App\Models\Umkm;
use Illuminate\Http\Request;

class UmkmController extends Controller
{
    public function index()
    {
        $usahas = Umkm::orderBy('nama', 'asc')->get();
        return view('umkm.index', compact('usahas'));
    }

    public function create()
    {
        return view('umkm.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'pemilik' => 'nullable|string|max:255',
            'kategori' => 'nullable|string|max:255',
            'telepon' => 'nullable|string|max:50',
            'alamat' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string|max:2000',
        ]);

        Umkm::create($validated);

        return redirect()->route('umkm.index')->with('success', 'Data UMKM berhasil disimpan.');
    }
}
