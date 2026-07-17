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
        
        if ($user->rt_number === '000') {
            // Admin RW: sees all requests
            $surats = SuratPengajuan::with('user.warga')->latest()->paginate(15);
        } elseif ($user->isAdmin()) {
            // Admin RT: sees requests in their RT
            $surats = SuratPengajuan::with('user.warga')->where('rt_number', $user->rt_number)->latest()->paginate(15);
        } else {
            // Warga: sees their own requests
            $surats = SuratPengajuan::with('user.warga')->where('user_id', $user->id)->latest()->paginate(15);
        }

        return view('surat.index', compact('user', 'surats'));
    }

    public function create()
    {
        $user = Auth::user();
        $warga = $user->warga;
        
        $wargas = collect();
        if ($user->isAdmin()) {
            // Fetch citizens who have registered user accounts for target selection
            $wargas = \App\Models\Warga::orderBy('nama', 'asc')->get();
        }

        return view('surat.create', compact('user', 'warga', 'wargas'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            $validated = $request->validate([
                'target_user_id' => 'required|exists:users,id',
                'jenis_surat' => 'required|string|max:255',
                'keperluan' => 'required|string|max:2000',
            ]);

            $targetUser = \App\Models\User::findOrFail($validated['target_user_id']);
            $rtNumber = $targetUser->rt_number ?? '006';

            if ($user->rt_number === '000') {
                // Admin RW creates it: starts finished with TTE
                $tteHash = hash('sha256', Str::random(10) . '|' . $validated['jenis_surat'] . '|' . now()->toDateTimeString());
                $surat = SuratPengajuan::create([
                    'user_id' => $targetUser->id,
                    'rt_number' => $rtNumber,
                    'jenis_surat' => $validated['jenis_surat'],
                    'keperluan' => $validated['keperluan'],
                    'status' => 'selesai',
                    'tte_hash' => $tteHash,
                ]);
                $surat->update([
                    'pdf_path' => route('surat.pdf', $surat->id)
                ]);
                $msg = 'Surat berhasil dibuat langsung oleh Admin RW dan siap diunduh!';
            } else {
                // Admin RT creates it: starts pending_rw (needs RW sign)
                SuratPengajuan::create([
                    'user_id' => $targetUser->id,
                    'rt_number' => $rtNumber,
                    'jenis_surat' => $validated['jenis_surat'],
                    'keperluan' => $validated['keperluan'],
                    'status' => 'pending_rw',
                ]);
                $msg = 'Surat berhasil dibuat oleh Admin RT dan diteruskan ke RW untuk TTE.';
            }
        } else {
            $validated = $request->validate([
                'jenis_surat' => 'required|string|max:255',
                'keperluan' => 'required|string|max:2000',
            ]);

            SuratPengajuan::create([
                'user_id' => $user->id,
                'rt_number' => $user->rt_number,
                'jenis_surat' => $validated['jenis_surat'],
                'keperluan' => $validated['keperluan'],
                'status' => 'pending_rt',
            ]);
            $msg = 'Permohonan surat berhasil dikirim ke RT Anda.';
        }

        return redirect()->route('surat.index')->with('success', $msg);
    }

    public function approveRt(SuratPengajuan $surat)
    {
        $user = Auth::user();
        if (!$user->isAdmin() || $user->rt_number === '000') {
            abort(403, 'Hanya Admin RT yang dapat menyetujui permohonan ini.');
        }

        $surat->update([
            'status' => 'pending_rw',
        ]);

        return redirect()->route('surat.index')->with('success', 'Surat berhasil disetujui di tingkat RT dan diteruskan ke RW.');
    }

    public function approveRw(SuratPengajuan $surat)
    {
        $user = Auth::user();
        if (!$user->isAdmin() || $user->rt_number !== '000') {
            abort(403, 'Hanya Admin RW yang dapat menyetujui permohonan ini.');
        }

        // Generate TTE Hash (SHA-256)
        $tteHash = hash('sha256', $surat->id . '|' . $surat->jenis_surat . '|' . now()->toDateTimeString());

        $surat->update([
            'status' => 'selesai',
            'tte_hash' => $tteHash,
            'pdf_path' => route('surat.pdf', $surat->id)
        ]);

        return redirect()->route('surat.index')->with('success', 'Surat berhasil ditandatangani secara elektronik (TTE) dan diterbitkan!');
    }

    public function downloadPdf(SuratPengajuan $surat)
    {
        if ($surat->status !== 'selesai') {
            abort(403, 'Surat belum selesai diproses.');
        }

        $user = $surat->user;
        $warga = $user->warga;

        return view('surat.pdf', compact('surat', 'user', 'warga'));
    }
}
