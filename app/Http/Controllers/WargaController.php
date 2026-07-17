<?php

namespace App\Http\Controllers;

use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WargaController extends Controller
{
    public function index(Request $request)
    {
        $viewMode = $request->input('view', 'family');

        if ($viewMode === 'table') {
            $wargas = Warga::orderBy('nama', 'asc')->paginate(20);
        } else {
            $wargas = Warga::where(function($query) {
                $query->where('is_kk', true)
                      ->orWhere('is_kk', 1)
                      ->orWhere('is_kk', '1');
            })->with('anggotaKeluarga')->orderBy('nama', 'asc')->paginate(10);
        }

        return view('warga.index', compact('wargas', 'viewMode'));
    }

    public function create()
    {
        // TAMBAHAN: Disamakan query-nya agar dropdown Kepala Keluarga tidak kosong di SQLite
        $kepalaKeluargas = Warga::where(function($query) {
            $query->where('is_kk', true)
                  ->orWhere('is_kk', 1)
                  ->orWhere('is_kk', '1');
        })->orderBy('nama', 'asc')->get();

        return view('warga.create', compact('kepalaKeluargas'));
    }

    public function store(Request $request)
    {
        if ($request->has('rt_number') && $request->rt_number != '') {
            $request->merge(['rt_number' => str_pad($request->rt_number, 3, '0', STR_PAD_LEFT)]);
        }
        if ($request->has('rw_number') && $request->rw_number != '') {
            $request->merge(['rw_number' => str_pad($request->rw_number, 3, '0', STR_PAD_LEFT)]);
        }

        $request->merge(['is_kk' => $request->is_kk == '1']);

        $request->validate([
            'nama' => 'required|string|max:255',
            'blok_rumah' => 'required|string|max:50',
            'nomor_hp' => 'nullable|string|max:20',
            'no_kk' => 'nullable|string|max:25',
            'nik' => 'nullable|string|max:25',
            'gender' => 'nullable|in:L,P',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'tanggal_lahir' => 'required|date',
            'rt_number' => 'required|string|size:3|in:006,007,008,009,010',
            'rw_number' => 'required|string|size:3',
            'is_kk' => 'required|boolean',
            'kk_id' => 'nullable|exists:wargas,id',
            'alamat' => 'required|string',
        ]);

        $data = $request->only([
            'nama', 'blok_rumah', 'nomor_hp', 'no_kk', 'nik', 'gender',
            'tanggal_lahir', 'rt_number', 'rw_number', 'is_kk', 'alamat'
        ]);

        $data['kk_id'] = $request->is_kk ? null : $request->kk_id;

        if ($request->hasFile('profile_photo')) {
            $data['profile_photo'] = $request->file('profile_photo')->store('warga_photos', 'public');
        }

        Warga::create($data);

        return redirect()->route('warga.index')->with('success', 'Data warga berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        return redirect()->route('warga.index');
    }

    public function edit(string $id)
    {
        $warga = Warga::findOrFail($id);

        // TAMBAHAN: Disamakan query-nya agar dropdown tidak kosong
        $kepalaKeluargas = Warga::where(function($query) {
            $query->where('is_kk', true)
                  ->orWhere('is_kk', 1)
                  ->orWhere('is_kk', '1');
        })->where('id', '!=', $id)->orderBy('nama', 'asc')->get();

        return view('warga.edit', compact('warga', 'kepalaKeluargas'));
    }

    public function update(Request $request, string $id)
    {
        if ($request->has('rt_number') && $request->rt_number != '') {
            $request->merge(['rt_number' => str_pad($request->rt_number, 3, '0', STR_PAD_LEFT)]);
        }
        if ($request->has('rw_number') && $request->rw_number != '') {
            $request->merge(['rw_number' => str_pad($request->rw_number, 3, '0', STR_PAD_LEFT)]);
        }

        $request->merge(['is_kk' => $request->is_kk == '1']);

        $request->validate([
            'nama' => 'required|string|max:255',
            'blok_rumah' => 'required|string|max:50',
            'nomor_hp' => 'nullable|string|max:20',
            'no_kk' => 'nullable|string|max:25',
            'nik' => 'nullable|string|max:25',
            'gender' => 'nullable|in:L,P',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'tanggal_lahir' => 'required|date',
            'rt_number' => 'required|string|size:3|in:006,007,008,009,010',
            'rw_number' => 'required|string|size:3',
            'is_kk' => 'required|boolean',
            'kk_id' => 'nullable|exists:wargas,id',
            'alamat' => 'required|string',
        ]);

        $warga = Warga::findOrFail($id);

        $data = $request->only([
            'nama', 'blok_rumah', 'nomor_hp', 'no_kk', 'nik', 'gender',
            'tanggal_lahir', 'rt_number', 'rw_number', 'is_kk', 'alamat'
        ]);

        $data['kk_id'] = $request->is_kk ? null : $request->kk_id;

        if ($request->hasFile('profile_photo')) {
            $photo = $request->file('profile_photo');
            $path = $photo->store('warga_photos', 'public');

            // TAMBAHAN: Cek agar tidak menghapus URL foto dari seeder
            if ($warga->profile_photo && !filter_var($warga->profile_photo, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($warga->profile_photo)) {
                Storage::disk('public')->delete($warga->profile_photo);
            }
            $data['profile_photo'] = $path;
        }

        $warga->update($data);

        return redirect()->route('warga.index')->with('success', 'Data warga berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $warga = Warga::findOrFail($id);

        // TAMBAHAN: Cek agar tidak menghapus URL foto dari seeder
        if ($warga->profile_photo && !filter_var($warga->profile_photo, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($warga->profile_photo)) {
            Storage::disk('public')->delete($warga->profile_photo);
        }
        $warga->delete();

        return redirect()->route('warga.index')->with('success', 'Data warga berhasil dihapus.');
    }
}
