@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="row min-vh-100 align-items-center">
    <div class="col-md-8 col-lg-6 mx-auto">
        <div class="card shadow">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <i class="fas fa-user-plus fa-3x text-primary"></i>
                    <h3 class="mt-3">Daftar Kas RT</h3>
                    <p class="text-muted">Bergabung sebagai warga</p>
                </div>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-group">
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email') }}" required
                                       placeholder="Masukkan Email">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       id="password" name="password" required
                                       placeholder="*******">
                                <button class="input-group-text" type="button" onclick="toggleRegisterPass('password','eyeReg1','eyeOffReg1')" style="background: var(--bg-card); border-color: var(--border-color); color: var(--text-muted); cursor:pointer;" tabindex="-1">
                                    <svg id="eyeReg1" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                                        <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                                        <line x1="1" y1="1" x2="23" y2="23"/>
                                    </svg>
                                    <svg id="eyeOffReg1" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </button>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required placeholder="*******">
                                <button class="input-group-text" type="button" onclick="toggleRegisterPass('password_confirmation','eyeReg2','eyeOffReg2')" style="background: var(--bg-card); border-color: var(--border-color); color: var(--text-muted); cursor:pointer;" tabindex="-1">
                                    <svg id="eyeReg2" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                                        <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                                        <line x1="1" y1="1" x2="23" y2="23"/>
                                    </svg>
                                    <svg id="eyeOffReg2" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">No. Telepon</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                   id="phone" name="phone" value="{{ old('phone') }}" required>
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="rt_number" class="form-label">No. RT</label>
                            <input type="text" class="form-control @error('rt_number') is-invalid @enderror"
                                   id="rt_number" name="rt_number" value="{{ old('rt_number') }}" required>
                            @error('rt_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Alamat</label>
                        <textarea class="form-control @error('address') is-invalid @enderror"
                                  id="address" name="address" rows="2" required>{{ old('address') }}</textarea>
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-animated">
                        <i class="fas fa-user-plus me-2"></i>Daftar
                    </button>
                </form>

                <hr class="my-4">

                <div class="text-center">
                    <p class="mb-0">Sudah punya akun?
                        <a href="{{ route('login') }}" class="text-decoration-none">Login disini</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle register password visibility only when clicking the eye button
    function toggleRegisterPass(inputId, eyeId, eyeOffId) {
        const input = document.getElementById(inputId);
        const eyeIcon = document.getElementById(eyeId);
        const eyeOffIcon = document.getElementById(eyeOffId);
        // mark this input as toggled by eye so focus won't force-hide immediately
        window['toggledByEye_' + inputId] = true;
        if (input.type === 'password') {
            input.type = 'text';
            if (eyeIcon) eyeIcon.style.display = 'none';
            if (eyeOffIcon) eyeOffIcon.style.display = 'block';
        } else {
            input.type = 'password';
            if (eyeIcon) eyeIcon.style.display = 'block';
            if (eyeOffIcon) eyeOffIcon.style.display = 'none';
        }
        setTimeout(() => { window['toggledByEye_' + inputId] = false; }, 350);
    }

    // Attach focus handlers so clicking the input itself won't reveal the password
    (function() {
        ['password','password_confirmation'].forEach(function(id) {
            const el = document.getElementById(id);
            if (!el) return;
            el.addEventListener('focus', function() {
                if (!window['toggledByEye_' + id]) {
                    this.type = 'password';
                    const eye = document.getElementById(id === 'password' ? 'eyeReg1' : 'eyeReg2');
                    const eyeOff = document.getElementById(id === 'password' ? 'eyeOffReg1' : 'eyeOffReg2');
                    if (eye) eye.style.display = 'block';
                    if (eyeOff) eyeOff.style.display = 'none';
                }
            });
        });
    })();
</script>

@endsection
