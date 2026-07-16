<?php

namespace App\Http\Controllers;

use App\Models\SuratPengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SuratPengajuanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $surats = SuratPengajuan::query()->latest()->paginate(10);

        if ($user && $user->isWarga()) {
            $surats = SuratPengajuan::where('user_id', $user->id)->latest()->paginate(10);
        }

        return view('surat.index', compact('user', 'surats'));
    }

    public function create()
    {
        return view('surat.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_surat' => 'required|string|max:255',
            'keperluan' => 'required|string|max:2000',
        ]);

        $surat = SuratPengajuan::create([
            'user_id' => Auth::id(),
            'rt_number' => Auth::user()?->rt_number,
            'jenis_surat' => $validated['jenis_surat'],
            'keperluan' => $validated['keperluan'],
            'status' => 'pending_rt',
        ]);

        return redirect()->route('surat.index')->with('success', 'Permohonan surat berhasil dikirim ke RT Anda.');
    }

    public function updateStatus(Request $request, SuratPengajuan $surat)
    {
        $user = Auth::user();
        if (! $user || ! $user->isAdmin()) {
            abort(403);
        }

        $status = $request->input('status');
        $surat->status = $status;

        if ($status === 'approved') {
            $surat->tte_hash = hash('sha256', $surat->id . '|' . $surat->jenis_surat . '|' . now()->toDateTimeString());
            $surat->pdf_path = 'surat/' . Str::slug($surat->jenis_surat) . '-' . $surat->id . '.pdf';
        }

        $surat->save();

        return back()->with('success', 'Status surat berhasil diperbarui.');
    }
}
