<?php

namespace App\Http\Controllers;

use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class WargaController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->role === 'warga') return redirect()->route('dashboard');

        $viewMode = $request->input('view', 'family');
        $query = Warga::query();

        if (Auth::user()->isAdminRt()) {
            $query->where('rt_number', Auth::user()->rt_number);
        }

        if ($viewMode === 'table') {
            $wargas = $query->orderBy('nama', 'asc')->paginate(20);
        } else {
            $wargas = $query->where(function($q) {
                $q->where('is_kk', true)
                  ->orWhere('is_kk', 1)
                  ->orWhere('is_kk', '1');
            })->with('anggotaKeluarga')->orderBy('nama', 'asc')->paginate(10);
        }

        return view('warga.index', compact('wargas', 'viewMode'));
    }

    public function create()
    {
        if (Auth::user()->role === 'warga') return redirect()->route('dashboard');

        $query = Warga::where(function($q) {
            $q->where('is_kk', true)
              ->orWhere('is_kk', 1)
              ->orWhere('is_kk', '1');
        });

        if (Auth::user()->isAdminRt()) {
            $query->where('rt_number', Auth::user()->rt_number);
        }

        $kepalaKeluargas = $query->orderBy('nama', 'asc')->get();

        return view('warga.create', compact('kepalaKeluargas'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role === 'warga') return redirect()->route('dashboard');

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

        if (Auth::user()->isAdminRt() && $request->rt_number !== Auth::user()->rt_number) {
            return redirect()->back()->withErrors(['rt_number' => 'Anda hanya bisa menambahkan warga ke RT Anda.']);
        }

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
        if (Auth::user()->role === 'warga') return redirect()->route('dashboard');

        $warga = Warga::findOrFail($id);
        if (Auth::user()->isAdminRt() && $warga->rt_number !== Auth::user()->rt_number) {
            return redirect()->route('warga.index')->with('error', 'Akses ditolak.');
        }

        $query = Warga::where(function($q) {
            $q->where('is_kk', true)
              ->orWhere('is_kk', 1)
              ->orWhere('is_kk', '1');
        })->where('id', '!=', $id);

        if (Auth::user()->isAdminRt()) {
            $query->where('rt_number', Auth::user()->rt_number);
        }

        $kepalaKeluargas = $query->orderBy('nama', 'asc')->get();

        return view('warga.edit', compact('warga', 'kepalaKeluargas'));
    }

    public function update(Request $request, string $id)
    {
        if (Auth::user()->role === 'warga') return redirect()->route('dashboard');

        $warga = Warga::findOrFail($id);
        if (Auth::user()->isAdminRt() && $warga->rt_number !== Auth::user()->rt_number) {
            return redirect()->route('warga.index')->with('error', 'Akses ditolak.');
        }

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

        if (Auth::user()->isAdminRt() && $request->rt_number !== Auth::user()->rt_number) {
            return redirect()->back()->withErrors(['rt_number' => 'Anda tidak bisa mengubah RT ke luar wewenang Anda.']);
        }

        $data = $request->only([
            'nama', 'blok_rumah', 'nomor_hp', 'no_kk', 'nik', 'gender',
            'tanggal_lahir', 'rt_number', 'rw_number', 'is_kk', 'alamat'
        ]);

        $data['kk_id'] = $request->is_kk ? null : $request->kk_id;

        if ($request->hasFile('profile_photo')) {
            if ($warga->profile_photo && Storage::disk('public')->exists($warga->profile_photo)) {
                Storage::disk('public')->delete($warga->profile_photo);
            }
            $data['profile_photo'] = $request->file('profile_photo')->store('warga_photos', 'public');
        }

        $warga->update($data);

        return redirect()->route('warga.index')->with('success', 'Data warga berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        if (Auth::user()->role === 'warga') return redirect()->route('dashboard');

        $warga = Warga::findOrFail($id);
        if (Auth::user()->isAdminRt() && $warga->rt_number !== Auth::user()->rt_number) {
            return redirect()->route('warga.index')->with('error', 'Akses ditolak.');
        }

        if ($warga->profile_photo && Storage::disk('public')->exists($warga->profile_photo)) {
            Storage::disk('public')->delete($warga->profile_photo);
        }

        $warga->delete();

        return redirect()->route('warga.index')->with('success', 'Data warga berhasil dihapus.');
    }
}
