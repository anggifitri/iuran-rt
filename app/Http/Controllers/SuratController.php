<?php

namespace App\Http\Controllers;

use App\Models\Surat;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuratController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = Surat::query()->latest();

        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        $surats = $query->paginate(10);

        return view('surat.index', compact('user', 'surats'));
    }

    public function create()
    {
        return view('surat.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis' => 'required|string|max:255',
            'keterangan' => 'nullable|string|max:2000',
        ]);

        $surat = Surat::create([
            'user_id' => Auth::id(),
            'jenis' => $validated['jenis'],
            'keterangan' => $validated['keterangan'] ?? null,
            'status' => 'pending',
        ]);

        AuditLogger::log(
            event: 'create',
            module: 'Penerbitan Surat',
            recordId: $surat->id,
            description: "Warga '" . Auth::user()->name . "' mengajukan permohonan surat jenis '{$surat->jenis}'.",
            rtNumber: Auth::user()->rt_number,
            status: 'success',
            actionType: 'CREATE'
        );

        return redirect()->route('surat.index')->with('success', 'Permintaan surat berhasil dikirim.');
    }
}
