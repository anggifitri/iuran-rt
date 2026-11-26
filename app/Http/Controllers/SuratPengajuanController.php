<?php

namespace App\Http\Controllers;

use App\Models\SuratPengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SuratPengajuanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $filter = $request->get('filter', 'all');
        
        $query = SuratPengajuan::with('user.warga')->latest();

        if ($user->isAdminRw()) {
            if ($filter === 'pending') {
                $query->where('status', 'pending_rw');
            } elseif ($filter === 'selesai') {
                $query->where('status', 'selesai');
            }
        } elseif ($user->isAdminRt()) {
            $query->where('rt_number', $user->rt_number);
            if ($filter === 'pending') {
                $query->where('status', 'pending_rt');
            } elseif ($filter === 'pending_rw') {
                $query->where('status', 'pending_rw');
            } elseif ($filter === 'selesai') {
                $query->where('status', 'selesai');
            }
        } else {
            $query->where('user_id', $user->id);
        }

        $surats = $query->paginate(15);

        return view('surat.index', compact('user', 'surats', 'filter'));
    }

    public function create()
    {
        $user = Auth::user();
        $warga = $user->warga;
        
        $wargas = collect();
        if ($user->isAdmin()) {
            // Fetch citizens who have registered user accounts for target selection
            $wargaQuery = \App\Models\Warga::orderBy('nama', 'asc');
            if ($user->isAdminRt()) {
                $wargaQuery->where('rt_number', $user->rt_number);
            }
            $wargas = $wargaQuery->get();
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

            if ($user->isAdminRt() && $targetUser->rt_number !== $user->rt_number) {
                abort(403, 'Akses ditolak: Anda hanya dapat membuat surat atas nama warga di wilayah RT ' . $user->rt_number . '.');
            }

            if ($user->isAdminRw()) {
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
        if (!$user->isAdminRt()) {
            abort(403, 'Hanya Admin RT yang dapat menyetujui permohonan surat tingkat RT.');
        }

        if ($surat->rt_number !== $user->rt_number) {
            abort(403, 'Akses ditolak: Surat ini milik warga RT ' . $surat->rt_number . ', bukan wilayah wewenang RT Anda.');
        }

        if ($surat->status !== 'pending_rt') {
            return redirect()->route('surat.index')->with('error', 'Status surat tidak dalam antrean persetujuan RT.');
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

        $currentUser = Auth::user();
        if ($currentUser->isWarga() && $surat->user_id !== $currentUser->id) {
            abort(403, 'Akses ditolak: Anda hanya berhak mengunduh berkas surat milik Anda sendiri.');
        }

        if ($currentUser->isAdminRt() && $surat->rt_number !== $currentUser->rt_number) {
            abort(403, 'Akses ditolak: Surat ini milik warga di luar wilayah RT Anda.');
        }

        $user = $surat->user;
        $warga = $user ? $user->warga : null;

        return view('surat.pdf', compact('surat', 'user', 'warga'));
    }
}
