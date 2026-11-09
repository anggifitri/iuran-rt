<?php

namespace App\Http\Controllers;

use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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
            'rfid_uid' => 'nullable|string|max:64|unique:wargas,rfid_uid',
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
            'nama', 'blok_rumah', 'nomor_hp', 'no_kk', 'nik', 'rfid_uid', 'gender',
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
            'rfid_uid' => ['nullable', 'string', 'max:64', Rule::unique('wargas', 'rfid_uid')->ignore($warga->id)],
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
            'nama', 'blok_rumah', 'nomor_hp', 'no_kk', 'nik', 'rfid_uid', 'gender',
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

    /**
     * Endpoint untuk menerima kiriman data UID E-KTP dari NodeMCU ESP32 + RFID RC522.
     * Mendukung POST maupun GET: /api/rfid/scan?uid=A1B2C3D4
     */
    public function receiveRfidScan(Request $request)
    {
        $rawUid = $request->input('uid') ?? $request->input('rfid_uid') ?? $request->input('card_uid');

        if (!$rawUid) {
            return response()->json([
                'status' => 'error',
                'message' => 'Parameter uid wajib dikirim oleh NodeMCU ESP32.',
            ], 422);
        }

        // Normalisasi UID (huruf besar, hanya karakter hex/alphanumeric tanpa spasi/titik dua)
        $uid = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', trim($rawUid)));

        $existingWarga = Warga::where('rfid_uid', $uid)->first();

        $scanPayload = [
            'uid' => $uid,
            'device_id' => $request->input('device_id', 'ESP32-RC522'),
            'scanned_at' => now()->toIso8601String(),
            'timestamp_ms' => round(microtime(true) * 1000),
            'registered_warga' => $existingWarga ? [
                'id' => $existingWarga->id,
                'nama' => $existingWarga->nama,
                'rt_number' => $existingWarga->rt_number,
            ] : null,
        ];

        // Simpan di cache selama 5 menit agar modal admin dapat mengambil hasil tap terbaru
        Cache::put('rfid_last_scan', $scanPayload, now()->addMinutes(5));

        return response()->json([
            'status' => 'success',
            'message' => 'UID E-KTP berhasil diterima dari RC522.',
            'data' => $scanPayload,
        ]);
    }

    /**
     * Endpoint polling untuk modal Rekam E-KTP di halaman Admin Data Warga.
     */
    public function getLatestRfidScan(Request $request)
    {
        if (Auth::user()->role === 'warga') {
            return response()->json(['status' => 'forbidden'], 403);
        }

        $scan = Cache::get('rfid_last_scan');
        $since = (float) $request->query('since', 0);

        if (!$scan || ($since > 0 && $scan['timestamp_ms'] <= $since)) {
            return response()->json([
                'status' => 'waiting',
                'data' => null,
            ]);
        }

        return response()->json([
            'status' => 'detected',
            'data' => $scan,
        ]);
    }

    /**
     * Menyimpan / mengaitkan UID E-KTP hasil scan RC522 ke data Warga tertentu.
     */
    public function storeRfidKtp(Request $request, string $id)
    {
        $user = Auth::user();
        if ($user->role === 'warga') {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }

        $warga = Warga::findOrFail($id);

        if ($user->isAdminRt() && $warga->rt_number !== $user->rt_number) {
            return redirect()->route('warga.index')->with('error', 'Akses ditolak: Warga berada di luar RT Anda.');
        }

        $rawUid = $request->input('rfid_uid', '');
        $normalizedUid = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', trim($rawUid)));
        $request->merge(['rfid_uid' => $normalizedUid]);

        $request->validate([
            'rfid_uid' => [
                'required',
                'string',
                'min:4',
                'max:64',
                Rule::unique('wargas', 'rfid_uid')->ignore($warga->id),
            ],
            'nik' => 'nullable|string|max:25',
        ], [
            'rfid_uid.required' => 'UID E-KTP belum terbaca. Silakan tempelkan E-KTP pada reader RFID RC522.',
            'rfid_uid.unique' => 'Kartu E-KTP (UID) ini sudah terdaftar pada warga lain.',
        ]);

        $updateData = ['rfid_uid' => $normalizedUid];
        if ($request->filled('nik')) {
            $updateData['nik'] = $request->nik;
        }

        $warga->update($updateData);

        // Bersihkan cache scan terakhir setelah berhasil direkam
        Cache::forget('rfid_last_scan');

        return redirect()->back()->with('success', "E-KTP (UID: {$normalizedUid}) berhasil direkam untuk {$warga->nama}.");
    }
}
