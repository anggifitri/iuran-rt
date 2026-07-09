<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UmkmController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        // In future, load usaha dari model
        $usaha = [];
        return view('umkm.index', compact('user', 'usaha'));
    }
}
