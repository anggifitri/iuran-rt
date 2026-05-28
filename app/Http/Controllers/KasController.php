<?php

namespace App\Http\Controllers;

use App\Models\KasRT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KasController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin,bendahara');
    }

    public function index()
    {
        $kas = KasRT::first();
        $totalWarga = \App\Models\User::where('role', 'warga')->count();

        // Menggunakan DB table karena model Pembayaran saat ini diarahkan ke 'transactions'
        // sedangkan kolom status ada di tabel 'pembayarans'.
        $totalSudahBayar = DB::table('pembayarans')->where('status', 'confirmed')->count();

        return view('kas.index', compact('kas', 'totalWarga', 'totalSudahBayar'));
    }
}
