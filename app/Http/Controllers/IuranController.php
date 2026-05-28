<?php

namespace App\Http\Controllers;

use App\Models\Iuran;
use Illuminate\Http\Request;

class IuranController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin,bendahara')->except(['index']);
    }

    public function index()
    {
        $iurans = Iuran::orderBy('due_date', 'asc')->paginate(10);
        return view('iuran.index', compact('iurans'));
    }

    public function create()
    {
        return view('iuran.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:1000',
            'due_date' => 'required|date',
            'type' => 'required|in:wajib,sukarela,kebersihan,keamanan'
        ]);

        Iuran::create($request->all());
        return redirect()->route('iuran.index')->with('success', 'Iuran berhasil ditambahkan!');
    }

    public function edit(Iuran $iuran)
    {
        return view('iuran.edit', compact('iuran'));
    }

    public function update(Request $request, Iuran $iuran)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:1000',
            'due_date' => 'required|date',
            'type' => 'required|in:wajib,sukarela,kebersihan,keamanan'
        ]);

        $iuran->update($request->all());
        return redirect()->route('iuran.index')->with('success', 'Iuran berhasil diupdate!');
    }

    public function destroy(Iuran $iuran)
    {
        $iuran->delete();
        return redirect()->route('iuran.index')->with('success', 'Iuran berhasil dihapus!');
    }
}
