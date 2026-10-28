# 🔐 AUTENTIKASI

> Login, registrasi warga mandiri, reset password, dan logout.

## 1. Ringkasan
Modul autentikasi berbasis **session Laravel** (`Auth::attempt` + remember me). Warga baru dapat mendaftar sendiri; proses registrasi **membuat dua record sekaligus**: akun login (`users`) dan profil kependudukan (`wargas`). Akun admin dibuat via seeder/tinker (tidak ada registrasi admin dari UI).

## 2. Aktor & Hak Akses
| Aktor | Aksi |
|---|---|
| Guest | Lihat cover, login, registrasi, reset password |
| User terautentikasi | Logout |

## 3. File Terkait
| Jenis | Path |
|---|---|
| Controller | `app/Http/Controllers/AuthController.php` |
| Model | `app/Models/User.php`, `app/Models/warga.php` |
| View | `resources/views/cover.blade.php`, `auth/login.blade.php`, `auth/register.blade.php`, `auth/forgot-password.blade.php` |
| Route | `routes/web.php` baris 10–22 |

## 4. Route
| Method | URI | Name | Method Controller |
|---|---|---|---|
| GET | `/` | — | view `cover` |
| GET | `/login` | `login` | `showLogin` |
| POST | `/login` | — | `login` |
| GET | `/register` | `register` | `showRegister` |
| POST | `/register` | — | `register` |
| GET | `/forgot-password` | `password.request` | `showForgotPassword` |
| POST | `/forgot-password` | `password.email` | `sendResetLinkEmail` |
| POST | `/logout` | `logout` | `logout` |

## 5. Aturan Bisnis
1. **Login** memakai email + password, selalu `remember = true`, session di-regenerate setelah sukses, redirect ke `intended` atau `/dashboard`.
2. **Registrasi**:
   - `rt_number` & `rw_number` di-padding 3 digit (`6` → `006`).
   - RT wajib salah satu `006–010`.
   - Role otomatis `warga`.
   - Jika `is_kk = 0`, user wajib memilih Kepala Keluarga (`kk_id`) dari daftar warga `is_kk = true`.
   - Foto profil (opsional) disimpan di `storage/app/public/warga_photos`, path yang sama dipakai di `users` dan `wargas`.
   - Setelah sukses → **auto login** → dashboard.
3. **Reset password** (kondisi saat ini): user memasukkan email + password baru + konfirmasi; jika email ada, password langsung diganti lalu auto login.
4. **Logout**: invalidate session + regenerate CSRF token → redirect `/`.

## 6. Use Case Diagram
```mermaid
flowchart LR
    G(["👤 Guest"])
    U(["👤 User Terautentikasi"])
    UC1(("Login"))
    UC2(("Registrasi Warga"))
    UC3(("Reset Password"))
    UC4(("Logout"))
    UC5(("Pilih Kepala Keluarga"))
    UC6(("Upload Foto Profil"))
    G --> UC1
    G --> UC2
    G --> UC3
    U --> UC4
    UC2 -. include .-> UC5
    UC2 -. extend .-> UC6
```

## 7. Flowchart

### 7.1 Login
```mermaid
flowchart TD
    A["Buka /login"] --> B["Isi email dan password"]
    B --> C{"Validasi format?"}
    C -- Gagal --> B
    C -- OK --> D{"Auth::attempt berhasil?"}
    D -- Tidak --> E["Kembali + error: Email atau password salah"] --> B
    D -- Ya --> F["Regenerate session"]
    F --> G["Redirect /dashboard + flash Selamat datang kembali"]
```

### 7.2 Registrasi
```mermaid
flowchart TD
    A["Buka /register"] --> B["Sistem memuat daftar Kepala Keluarga"]
    B --> C["Isi form: nama, email, password, blok, RT, RW, tgl lahir, alamat, status KK"]
    C --> D{"is_kk = 1?"}
    D -- Ya --> E["kk_id = null"]
    D -- Tidak --> F["Pilih Kepala Keluarga, kk_id = pilihan"]
    E --> G["Padding RT/RW 3 digit"]
    F --> G
    G --> H{"Validasi lolos?"}
    H -- Tidak --> C
    H -- Ya --> I{"Ada foto?"}
    I -- Ya --> J["Simpan ke warga_photos"]
    I -- Tidak --> K["profile_photo = null"]
    J --> L["Create User role=warga"]
    K --> L
    L --> M["Create Warga"]
    M --> N["Auth::login"] --> O["Redirect /dashboard"]
```

## 8. Sequence Diagram — Registrasi
```mermaid
sequenceDiagram
    actor W as Calon Warga
    participant V as register.blade.php
    participant AC as AuthController
    participant U as User Model
    participant WG as Warga Model
    participant FS as Storage public
    participant DB as SQLite

    W->>V: GET /register
    V->>AC: showRegister()
    AC->>WG: where is_kk = true orderBy nama
    WG->>DB: SELECT
    DB-->>AC: daftar KK
    AC-->>W: form registrasi
    W->>AC: POST /register (form + foto)
    AC->>AC: str_pad rt/rw, cast is_kk, validate
    alt foto diunggah
        AC->>FS: store warga_photos
        FS-->>AC: path
    end
    AC->>U: create role warga
    U->>DB: INSERT users
    AC->>WG: create profil
    WG->>DB: INSERT wargas
    AC->>AC: Auth::login(user, remember)
    AC-->>W: 302 /dashboard + flash sukses
```

## 9. Class Diagram
```mermaid
classDiagram
    class AuthController {
        +showLogin() View
        +login(Request) RedirectResponse
        +showRegister() View
        +register(Request) RedirectResponse
        +showForgotPassword() View
        +sendResetLinkEmail(Request) RedirectResponse
        +logout(Request) RedirectResponse
    }
    class User {
        +name
        +email
        +role
        +rt_number
        +nik
        +no_kk
        +profile_photo
        +isAdmin() bool
        +isAdminRt() bool
        +isAdminRw() bool
        +isWarga() bool
        +warga() HasOne
    }
    class Warga {
        +nama
        +nik
        +is_kk
        +kk_id
        +rt_number
        +kepalaKeluarga() BelongsTo
        +anggotaKeluarga() HasMany
    }
    AuthController ..> User : creates
    AuthController ..> Warga : creates
    User "1" --> "0..1" Warga : nik
```

## 10. State Diagram — Sesi Pengguna
```mermaid
stateDiagram-v2
    [*] --> Guest
    Guest --> Authenticated : login sukses
    Guest --> Authenticated : registrasi sukses
    Guest --> Authenticated : reset password
    Authenticated --> Guest : logout
    Authenticated --> Guest : session expired
```

## 11. Validasi
| Field (register) | Rule |
|---|---|
| name | required, string, max 255 |
| email | required, email, unique:users |
| password | required, min 6, confirmed |
| blok_rumah | required, max 50 |
| tanggal_lahir | required, date |
| rt_number | required, size 3, in 006–010 |
| rw_number | required, size 3 |
| is_kk | required, boolean |
| kk_id | nullable, exists:wargas,id |
| address | required |
| gender | nullable, in L,P |
| profile_photo | nullable, image jpg/jpeg/png, max 2 MB |

## 12. Known Issues & Pengembangan Lanjutan
| # | Isu | Rekomendasi |
|---|---|---|
| 🔴 1 | Reset password **tanpa token/email verifikasi** — account takeover | Gunakan `Password::sendResetLink` + tabel `password_reset_tokens`, atau OTP WhatsApp |
| 🟠 2 | Tidak ada rate-limit login | Tambah `throttle:5,1` pada `POST /login` |
| 🟡 3 | Route guest tidak memakai middleware `guest` (user login bisa buka /login) | Bungkus dengan `Route::middleware('guest')` |
| 🟡 4 | Registrasi tidak mencegah duplikasi NIK | Tambah `unique:wargas,nik` |
| 🟢 5 | `APP_NAME` masih `Laravel` | Ubah ke `NexaNest` |
