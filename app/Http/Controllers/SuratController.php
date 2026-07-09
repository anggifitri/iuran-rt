<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SuratController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('surat.index', compact('user')); 
    }

    public function create()
    {
        $user = auth()->user();
        return view('surat.create', compact('user'));
    }

    public function store(Request $request)
    {
        // Implementasi penyimpanan permintaan surat nanti
        return redirect()->route('surat.index')->with('success', 'Permintaan surat dikirim.');
    }
}
