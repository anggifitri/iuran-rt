<?php

namespace App\Http\Controllers;

use App\Models\Umkm;
use Illuminate\Http\Request;

class UmkmController extends Controller
{
    public function index(Request $request)
    {
        $query = Umkm::query();

        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where(function ($builder) use ($search) {
                $builder->where('nama', 'like', "%{$search}%")
                        ->orWhere('pemilik', 'like', "%{$search}%")
                        ->orWhere('kategori', 'like', "%{$search}%")
                        ->orWhere('alamat', 'like', "%{$search}%")
                        ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $usahas = $query->orderBy('nama', 'asc')->get();
        $categories = Umkm::select('kategori')
            ->whereNotNull('kategori')
            ->where('kategori', '<>', '')
            ->orderBy('kategori')
            ->pluck('kategori');

        return view('umkm.index', compact('usahas', 'categories'));
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
