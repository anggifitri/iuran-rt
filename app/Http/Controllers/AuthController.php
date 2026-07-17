<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan.'])->withInput();
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('login')->with('success', 'Password berhasil diperbarui. Silakan login dengan password baru.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials, true)) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'))->with('success', 'Selamat datang kembali!');
        }

        return back()->withErrors(['email' => 'Email atau password salah.']);
    }

    public function showRegister()
    {
        $kepalaKeluargas = \App\Models\Warga::where(function($query) {
            $query->where('is_kk', true)
                  ->orWhere('is_kk', 1)
                  ->orWhere('is_kk', '1');
        })->orderBy('nama', 'asc')->get();

        return view('auth.register', compact('kepalaKeluargas'));
    }

    public function register(Request $request)
    {
        if ($request->has('rt_number') && $request->rt_number != '') {
            $request->merge(['rt_number' => str_pad($request->rt_number, 3, '0', STR_PAD_LEFT)]);
        }
        if ($request->has('rw_number') && $request->rw_number != '') {
            $request->merge(['rw_number' => str_pad($request->rw_number, 3, '0', STR_PAD_LEFT)]);
        }

        $request->merge(['is_kk' => $request->is_kk == '1']);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'blok_rumah' => 'required|string|max:50',
            'phone' => 'nullable|string|max:20',
            'no_kk' => 'nullable|string|max:25',
            'nik' => 'nullable|string|max:25',
            'gender' => 'nullable|in:L,P',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'tanggal_lahir' => 'required|date',
            'rt_number' => 'required|string|size:3|in:006,007,008,009,010',
            'rw_number' => 'required|string|size:3',
            'is_kk' => 'required|boolean',
            'kk_id' => 'nullable|exists:wargas,id',
            'address' => 'required|string',
        ]);

        $profilePhotoPath = null;
        if ($request->hasFile('profile_photo')) {
            $profilePhotoPath = $request->file('profile_photo')->store('warga_photos', 'public');
        }

        // 1. Create login account (User)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'warga',
            'phone' => $request->phone,
            'address' => $request->address,
            'rt_number' => $request->rt_number,
            'no_kk' => $request->no_kk,
            'nik' => $request->nik,
            'profile_photo' => $profilePhotoPath,
        ]);

        // 2. Create profile record (Warga)
        \App\Models\Warga::create([
            'nama' => $request->name,
            'blok_rumah' => $request->blok_rumah,
            'nomor_hp' => $request->phone,
            'gender' => $request->gender,
            'no_kk' => $request->no_kk,
            'nik' => $request->nik,
            'profile_photo' => $profilePhotoPath,
            'tanggal_lahir' => $request->tanggal_lahir,
            'rt_number' => $request->rt_number,
            'rw_number' => $request->rw_number,
            'is_kk' => $request->is_kk,
            'kk_id' => $request->is_kk ? null : $request->kk_id,
            'alamat' => $request->address,
        ]);

        Auth::login($user, true);
        return redirect()->route('dashboard')->with('success', 'Pendaftaran berhasil!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Anda telah logout.');
    }
}
