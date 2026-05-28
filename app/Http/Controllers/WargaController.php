<?php

namespace App\Http\Controllers;

use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WargaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $wargas = Warga::orderBy('nama', 'asc')->paginate(10);
        return view('warga.index', compact('wargas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('warga.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'blok_rumah' => 'required|string|max:50',
            'nomor_hp' => 'nullable|string|max:20',
            'no_kk' => 'nullable|string|max:25',
            'nik' => 'nullable|string|max:25',
            'gender' => 'nullable|in:L,P',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only(['nama', 'blok_rumah', 'nomor_hp', 'no_kk', 'nik', 'gender']);
        if ($request->hasFile('profile_photo')) {
            $data['profile_photo'] = $request->file('profile_photo')->store('warga_photos', 'public');
        }

        Warga::create($data);

        return redirect()->route('warga.index')->with('success', 'Data warga berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return redirect()->route('warga.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $warga = Warga::findOrFail($id);
        return view('warga.edit', compact('warga'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'blok_rumah' => 'required|string|max:50',
            'nomor_hp' => 'nullable|string|max:20',
            'no_kk' => 'nullable|string|max:25',
            'nik' => 'nullable|string|max:25',
            'gender' => 'nullable|in:L,P',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $warga = Warga::findOrFail($id);
        $data = $request->only(['nama', 'blok_rumah', 'nomor_hp', 'no_kk', 'nik', 'gender']);
        if ($request->hasFile('profile_photo')) {
            $photo = $request->file('profile_photo');
            $path = $photo->store('warga_photos', 'public');
            if ($warga->profile_photo && Storage::disk('public')->exists($warga->profile_photo)) {
                Storage::disk('public')->delete($warga->profile_photo);
            }
            $data['profile_photo'] = $path;
        }
        $warga->update($data);

        return redirect()->route('warga.index')->with('success', 'Data warga berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $warga = Warga::findOrFail($id);
        $warga->delete();

        return redirect()->route('warga.index')->with('success', 'Data warga berhasil dihapus.');
    }
}
