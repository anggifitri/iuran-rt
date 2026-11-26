<?php

namespace App\Http\Controllers;

use App\Models\PlatformSetting;
use App\Models\User;
use App\Models\Pembayaran;
use App\Services\AuditLogger;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

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
        if ($request->has('email')) {
            $request->merge(['email' => strtolower(trim((string) $request->email))]);
        }

        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
        ]);

        $status = Password::broker()->sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'Kami telah mengirimkan tautan pemulihan kata sandi ke email Anda. Silakan periksa kotak masuk atau spam.');
        }

        $errorMessage = match($status) {
            Password::RESET_THROTTLED => 'Permintaan tautan pemulihan terlalu sering. Harap tunggu 1–2 menit sebelum mencoba kembali.',
            Password::INVALID_USER => 'Alamat email tidak terdaftar dalam sistem NexaNest.',
            default => 'Gagal mengirimkan tautan pemulihan kata sandi. Silakan coba kembali beberapa saat lagi.'
        };

        return back()->withErrors(['email' => $errorMessage])->withInput();
    }

    public function showResetPassword(Request $request, $token = null)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        if ($request->has('email')) {
            $request->merge(['email' => strtolower(trim((string) $request->email))]);
        }

        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'token.required' => 'Token pemulihan kata sandi tidak valid atau telah kedaluwarsa.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi minimal terdiri dari 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                AuditLogger::logPasswordChange($user);
                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', 'Kata sandi Anda berhasil diperbarui! Silakan masuk dengan kata sandi baru Anda.');
        }

        $errorMessage = match($status) {
            Password::INVALID_USER => 'Alamat email tidak ditemukan dalam sistem NexaNest.',
            Password::INVALID_TOKEN => 'Tautan pemulihan kata sandi tidak valid atau telah kedaluwarsa.',
            default => 'Gagal memperbarui kata sandi. Silakan coba kembali.'
        };

        return back()->withErrors(['email' => $errorMessage])->withInput($request->only('email'));
    }

    public function showVerificationNotice(Request $request)
    {
        if ($request->user() && $request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        return view('auth.verify-email');
    }

    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Tautan verifikasi email tidak valid atau telah kedaluwarsa.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard')->with('info', 'Alamat email Anda sudah terverifikasi sebelumnya.');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));

            AuditLogger::log(
                'EMAIL_VERIFIED',
                'Autentikasi & Keamanan',
                $user->id,
                "Warga {$user->name} ({$user->email}) berhasil menyelesaikan verifikasi email.",
                null,
                ['email' => $user->email, 'verified_at' => now()->toDateTimeString()],
                $user->rt_number,
                'success',
                'VERIFIKASI'
            );
        }

        return redirect()->route('dashboard')->with('success', 'Selamat! Alamat email Anda berhasil diverifikasi. Akun NexaNest Anda kini aktif sepenuhnya.');
    }

    public function resendVerificationEmail(Request $request)
    {
        $user = $request->user();

        if ($user && $user->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        if ($user) {
            $user->sendEmailVerificationNotification();
        }

        return back()->with('status', 'verification-link-sent')
                     ->with('success', 'Tautan verifikasi email yang baru telah dikirimkan ke alamat email Anda.');
    }

    public function login(Request $request)
    {
        if ($request->has('email')) {
            $request->merge(['email' => strtolower(trim((string) $request->email))]);
        }

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $allowRememberMe = PlatformSetting::get('allow_remember_me', '1') === '1';
        $remember = $request->boolean('remember') && $allowRememberMe;

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if (isset($user->status) && $user->status === 'suspended') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors(['email' => 'Akun Anda sedang ditangguhkan. Silakan hubungi pengelola platform.']);
            }

            $now = now()->timestamp;
            $tokenLifetime = (int) PlatformSetting::get('api_token_lifetime_minutes', 15);

            $request->session()->regenerate();
            session([
                'login_time' => $now,
                'last_activity_time' => $now,
                'remember_me' => $remember,
                'access_token' => Str::random(64),
                'access_token_expires_at' => now()->addMinutes($tokenLifetime)->timestamp,
                'refresh_token' => Str::random(64),
            ]);

            $user->update([
                'last_active_at' => now(),
                'last_login_at' => now(),
            ]);

            $rememberNote = $remember ? ' (Ingat Saya: Aktif)' : ' (Sesi Standar)';
            AuditLogger::logLogin($user, true, 'Autentikasi kredensial berhasil' . $rememberNote);

            // Cek verifikasi email untuk pendaftar baru warga
            if ($user->role === 'warga' && !$user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice')->with('warning', 'Alamat email Anda belum diverifikasi. Silakan periksa email Anda atau kirim ulang tautan verifikasi.');
            }

            if ($user->isSuperAdmin() && !$user->isRoleSwitched()) {
                return redirect()->route('super_admin.dashboard')->with('success', 'Selamat datang kembali, ' . $user->name . '! Platform Control Center siap digunakan.');
            }

            return redirect()->intended(route('dashboard'))->with('success', 'Selamat datang kembali, ' . $user->name . '!');
        }

        // Log failed login attempt
        $targetUser = User::where('email', $request->email)->first();
        AuditLogger::logLogin($targetUser, false, "Email sasaran: {$request->email}");

        return back()->withErrors(['email' => 'Email atau kata sandi tidak cocok. Silakan periksa kembali data Anda.'])->withInput();
    }

    public function showRegister()
    {
        $kepalaKeluargas = \App\Models\Warga::where(function($query) {
            $query->where('is_kk', true)
                  ->orWhere('is_kk', 1)
                  ->orWhere('is_kk', '1');
        })->orderBy('nama', 'asc')->get();

        // Ambil daftar blok per RT 006 - 010 untuk mapping dinamis
        $rtList = ['006', '007', '008', '009', '010'];
        $blocksPerRt = [];
        foreach ($rtList as $rt) {
            $rawBloks = \App\Models\Warga::where('rt_number', $rt)
                ->whereNotNull('blok_rumah')
                ->pluck('blok_rumah');

            $detected = [];
            foreach ($rawBloks as $rb) {
                if (preg_match('/Blok\s+([A-Za-z0-9]+)/i', $rb, $m)) {
                    $letter = strtoupper($m[1]);
                    $detected[$letter] = true;
                }
            }
            $blockList = array_keys($detected);
            sort($blockList);
            if (empty($blockList)) {
                $blockList = ['A', 'B', 'C', 'D', 'E', 'F'];
            }
            $blocksPerRt[$rt] = $blockList;
        }

        return view('auth.register', compact('kepalaKeluargas', 'blocksPerRt'));
    }

    public function register(Request $request)
    {
        if ($request->has('rt_number') && $request->rt_number != '') {
            $request->merge(['rt_number' => str_pad($request->rt_number, 3, '0', STR_PAD_LEFT)]);
        }
        // Nomor RW terkunci ke 018
        $request->merge(['rw_number' => '018']);

        // Format blok_rumah dari input blok dan nomor_rumah jika dikirim terpisah
        if ($request->filled('blok') && $request->filled('nomor_rumah')) {
            $blokFormatted = 'Blok ' . strtoupper(trim($request->blok)) . ' No. ' . trim($request->nomor_rumah);
            $request->merge(['blok_rumah' => $blokFormatted]);
        }

        $request->merge(['is_kk' => $request->is_kk == '1']);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users',
                'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/i',
            ],
            'password' => 'required|string|min:6|confirmed',
            'blok_rumah' => 'required|string|max:50',
            'nomor_hp' => 'required|string|max:20',
            'no_kk' => 'required|digits:16',
            'nik' => 'required|digits:16|unique:users,nik|unique:wargas,nik',
            'gender' => 'required|in:Laki-laki,Perempuan,L,P',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'tanggal_lahir' => 'required|date',
            'rt_number' => 'required|string|size:3|in:006,007,008,009,010',
            'rw_number' => 'required|string|size:3|in:018',
            'is_kk' => 'required|boolean',
            'kk_id' => 'nullable|exists:wargas,id',
            'alamat' => 'required|string',
        ], [
            'nik.required' => 'Nomor Induk Kependudukan (NIK) wajib diisi.',
            'nik.digits' => 'NIK harus tepat 16 digit angka.',
            'nik.unique' => 'NIK ini sudah terdaftar di sistem kependudukan NexaNest.',
            'no_kk.required' => 'Nomor Kartu Keluarga (No. KK) wajib diisi.',
            'no_kk.digits' => 'No. KK harus tepat 16 digit angka.',
            'blok_rumah.required' => 'Blok dan Nomor Rumah wajib dilengkapi.',
            'nomor_hp.required' => 'Nomor HP / WhatsApp wajib diisi.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'rt_number.in' => 'RT yang dipilih harus dalam lingkup RW 018 (RT 006 - RT 010).',
            'rw_number.in' => 'Nomor RW harus RW 018.',
            'email.regex' => 'Alamat email wajib menggunakan domain @gmail.com (contoh: nama@gmail.com).',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email ini sudah terdaftar. Silakan gunakan email lain atau masuk ke akun Anda.',
            'alamat.required' => 'Alamat domisili lengkap wajib diisi.',
        ]);

        $profilePhotoPath = null;
        if ($request->hasFile('profile_photo')) {
            $profilePhotoPath = $request->file('profile_photo')->store('warga_photos', 'public');
        }

        $phone = $request->nomor_hp ?: $request->phone;
        $genderStandard = in_array($request->gender, ['Laki-laki', 'L']) ? 'L' : 'P';

        // 1. Create login account (User) — pendaftar baru mandiri wajib verifikasi email (email_verified_at = null)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'warga',
            'phone' => $phone,
            'address' => $request->alamat,
            'rt_number' => $request->rt_number,
            'no_kk' => $request->no_kk,
            'nik' => $request->nik,
            'profile_photo' => $profilePhotoPath,
            'email_verified_at' => null,
        ]);

        // 2. Create profile record (Warga)
        \App\Models\Warga::create([
            'nama' => $request->name,
            'user_id' => $user->id,
            'blok_rumah' => $request->blok_rumah,
            'nomor_hp' => $phone,
            'gender' => $genderStandard,
            'no_kk' => $request->no_kk,
            'nik' => $request->nik,
            'profile_photo' => $profilePhotoPath,
            'tanggal_lahir' => $request->tanggal_lahir,
            'rt_number' => $request->rt_number,
            'rw_number' => $request->rw_number,
            'is_kk' => $request->is_kk,
            'kk_id' => $request->is_kk ? null : $request->kk_id,
            'alamat' => $request->alamat,
        ]);

        // 3. Picu pengiriman notifikasi email verifikasi akun baru
        event(new Registered($user));

        // 4. Autentikasi sesi pendaftar
        Auth::login($user, true);

        $now = now()->timestamp;
        $tokenLifetime = (int) PlatformSetting::get('api_token_lifetime_minutes', 15);
        $request->session()->regenerate();
        session([
            'login_time' => $now,
            'last_activity_time' => $now,
            'remember_me' => true,
            'access_token' => Str::random(64),
            'access_token_expires_at' => now()->addMinutes($tokenLifetime)->timestamp,
            'refresh_token' => Str::random(64),
        ]);

        return redirect()->route('verification.notice')->with('success', 'Pendaftaran berhasil! Tautan verifikasi email telah dikirimkan ke ' . $user->email . '. Silakan periksa kotak masuk atau folder spam email Anda.');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            AuditLogger::logLogout($user);
            // Invalidate persistent remember token so it cannot be re-used
            $user->setRememberToken(null);
            $user->save();
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Anda telah keluar dari akun. Sampai jumpa kembali!');
    }
}
