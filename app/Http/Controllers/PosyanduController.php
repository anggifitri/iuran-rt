<?php

namespace App\Http\Controllers;

use App\Models\Posyandu;
use Illuminate\Http\Request;

class PosyanduController extends Controller
{
    public function index()
    {
        $jadwal = Posyandu::orderBy('tanggal', 'asc')->get();
        return view('posyandu.index', compact('jadwal'));
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
