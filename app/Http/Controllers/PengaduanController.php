<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengaduanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = Pengaduan::query()->latest();

        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        $pengaduans = $query->paginate(10);

        return view('pengaduan.index', compact('user', 'pengaduans'));
    }

    public function create()
    {
        return view('pengaduan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:2000',
            'barcode_code' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'rt_number' => 'nullable|string|max:10',
            'location_details' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        Pengaduan::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'content' => $validated['content'],
            'status' => 'pending',
            'barcode_code' => $validated['barcode_code'] ?? null,
            'category' => $validated['category'] ?? null,
            'rt_number' => $validated['rt_number'] ?? null,
            'location_details' => $validated['location_details'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
        ]);

        return redirect()->route('pengaduan.index')->with('success', 'Pengaduan berhasil dikirim.');
    }
}
