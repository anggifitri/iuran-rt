<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PosyanduController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        // In future, load jadwal dari model
        $jadwal = [];
        return view('posyandu.index', compact('user', 'jadwal'));
    }
}
