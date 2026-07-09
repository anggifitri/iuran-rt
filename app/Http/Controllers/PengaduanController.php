<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PengaduanController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('pengaduan.index', compact('user'));
    }

    public function create()
    {
        $user = auth()->user();
        return view('pengaduan.create', compact('user'));
    }

    public function store(Request $request)
    {
        // Placeholder - simpan pengaduan
        return redirect()->route('pengaduan.index')->with('success', 'Pengaduan telah dikirim.');
    }
}
