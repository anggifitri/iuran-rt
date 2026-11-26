<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected static function booted(): void
    {
        static::creating(function ($user) {
            // Jika atribut email_verified_at tidak diset (misal test, seeder, pembuatan oleh pengurus),
            // tandai sebagai verified agar akun internal/lama tidak terkunci.
            // Pendaftaran mandiri warga baru via /register secara eksplisit mengisi 'email_verified_at' => null.
            if (!array_key_exists('email_verified_at', $user->getAttributes())) {
                $user->email_verified_at = now();
            }
        });
    }

    protected $fillable = [
        'name', 'display_name', 'bio', 'email', 'password', 'role', 'status', 'rt_number', 'rw_number',
        'phone', 'address', 'no_kk', 'nik', 'profile_photo', 'warga_id',
        'email_verified_at', 'phone_verified_at', 'notification_preferences', 'privacy_settings', 'theme_preference',
        'deactivation_requested_at', 'deactivation_reason', 'last_active_at', 'last_login_at'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'deactivation_requested_at' => 'datetime',
        'last_active_at' => 'datetime',
        'last_login_at' => 'datetime',
        'notification_preferences' => 'array',
        'privacy_settings' => 'array',
    ];

    protected $appends = ['profile_photo_url', 'masked_nik', 'masked_phone', 'masked_email', 'role_label'];

    public function hasVerifiedEmail(): bool
    {
        // Akun pengurus (Super Admin, Admin RW, Admin RT) selalu terverifikasi secara sistem
        if (in_array(strtolower($this->role ?? ''), ['super_admin', 'superadmin', 'super-admin', 'admin_rw', 'admin_rt', 'admin', 'bendahara'], true)) {
            return true;
        }

        return ! is_null($this->email_verified_at);
    }

    /* =========================================================================
       SUPER ADMIN & ROLE SWITCHER METHODS
       ========================================================================= */

    public function isSuperAdmin(): bool
    {
        return in_array(strtolower($this->role ?? ''), ['super_admin', 'superadmin', 'super-admin'], true)
            || $this->email === 'anggifitri0911@gmail.com';
    }

    public function isRoleSwitched(): bool
    {
        if (!$this->isSuperAdmin()) {
            return false;
        }
        return (bool) session('role_switch.is_switched', false);
    }

    public function getRoleSwitchContext(): ?array
    {
        if (!$this->isRoleSwitched()) {
            return null;
        }
        return session('role_switch', []);
    }

    public function isRoleSwitchPreview(): bool
    {
        return $this->isRoleSwitched() && (session('role_switch.mode') === 'preview');
    }

    public function isRoleSwitchOperational(): bool
    {
        return $this->isRoleSwitched() && (session('role_switch.mode') === 'operational');
    }

    public function getRtNumberAttribute($value)
    {
        if ($this->isRoleSwitched()) {
            return session('role_switch.target_rt') ?? ($value ?: '006');
        }
        return $value;
    }

    public function getRwNumberAttribute($value)
    {
        if ($this->isRoleSwitched()) {
            return session('role_switch.target_rw') ?? ($value ?: '018');
        }
        return $value ?: '018';
    }

    public function getMaskedNikAttribute(): string
    {
        $nik = $this->nik;
        if (!$nik || strlen($nik) < 8) return $nik ?: 'Belum Terdaftar';
        return substr($nik, 0, 4) . str_repeat('*', strlen($nik) - 6) . substr($nik, -2);
    }

    public function getMaskedPhoneAttribute(): string
    {
        $phone = $this->phone;
        if (!$phone || strlen($phone) < 6) return $phone ?: 'Belum Terdaftar';
        return substr($phone, 0, 4) . '****' . substr($phone, -2);
    }

    public function getMaskedEmailAttribute(): string
    {
        $email = $this->email;
        if (!$email || !str_contains($email, '@')) return $email ?: 'Belum Terdaftar';
        [$user, $domain] = explode('@', $email, 2);
        $userMasked = strlen($user) <= 2 ? $user . '***' : substr($user, 0, 2) . '***' . substr($user, -1);
        return $userMasked . '@' . $domain;
    }

    public function getRoleLabelAttribute(): string
    {
        if ($this->isSuperAdmin() && !$this->isRoleSwitched()) {
            return 'Super Admin · Platform Owner';
        }
        if ($this->isRoleSwitched()) {
            $ctx = $this->getRoleSwitchContext();
            $modeText = ($ctx['mode'] ?? 'operational') === 'preview' ? ' (Preview)' : ' (Operasional)';
            $targetRw = $ctx['target_rw'] ?? '018';
            $targetRt = $ctx['target_rt'] ?? '006';

            if ($this->isAdminRw()) {
                return "Admin RW {$targetRw} · Simulasi{$modeText}";
            } elseif ($this->isAdminRt()) {
                return "Admin RT {$targetRt} RW {$targetRw} · Simulasi{$modeText}";
            }
            return "Warga RT {$targetRt} RW {$targetRw} · Simulasi{$modeText}";
        }

        if ($this->isAdminRw()) {
            return 'Admin RW ' . ($this->rw_number ?: '018') . ' · Pengurus Komunitas';
        } elseif ($this->isAdminRt()) {
            return 'Admin RT ' . ($this->rt_number ?: '006') . ' · Pengurus Wilayah';
        }
        return 'Warga RT ' . ($this->rt_number ?: '006') . ' · RW ' . ($this->rw_number ?: '018');
    }

    public function getResolvedNotificationPreferencesAttribute(): array
    {
        $defaults = [
            'pengumuman' => true,
            'iuran' => true,
            'kegiatan' => true,
            'posyandu' => true,
            'surat' => true,
            'pengaduan' => true,
            'in_app' => true,
            'email' => true,
        ];
        return array_merge($defaults, $this->notification_preferences ?: []);
    }

    public function getResolvedPrivacySettingsAttribute(): array
    {
        $defaults = [
            'hide_phone' => false,
            'profile_visible' => true,
        ];
        return array_merge($defaults, $this->privacy_settings ?: []);
    }

    public function getProfilePhotoUrlAttribute()
    {
        // Khusus Super Admin dalam simulasi Role Switcher
        if ($this->isRoleSwitched()) {
            return $this->getSimulatedProfilePhotoUrl();
        }

        if ($this->profile_photo) {
            if (filter_var($this->profile_photo, FILTER_VALIDATE_URL)) {
                return $this->profile_photo;
            }
            return asset('storage/' . $this->profile_photo);
        }

        return static::generateInitialsAvatar($this->name ?? 'User', $this->role ?? 'warga', $this->jenis_kelamin ?? null);
    }

    /**
     * Resolusi foto profil atau inisial avatar dari user sasaran saat simulasi peran aktif.
     */
    public function getSimulatedProfilePhotoUrl(): string
    {
        $ctx = $this->getRoleSwitchContext() ?: [];
        $targetRole = $ctx['target_role'] ?? 'warga';
        $targetRt = $ctx['target_rt'] ?? null;
        $targetUserId = $ctx['target_user_id'] ?? null;
        $targetWargaId = $ctx['target_warga_id'] ?? null;
        $targetName = $ctx['target_user_name'] ?? null;

        if ($targetRole === 'warga') {
            $w = null;
            if ($targetWargaId) {
                $w = Warga::find($targetWargaId);
            }
            if (!$w && $targetUserId) {
                $u = User::find($targetUserId);
                if ($u) {
                    $w = $u->resolveWarga();
                }
            }
            if (!$w) {
                $w = $this->resolveWarga();
            }

            if ($w) {
                // 1. Cek foto profil dari user account warga jika terhubung
                if (!empty($w->user_id)) {
                    $u = $w->user ?: User::find($w->user_id);
                    if ($u && !empty($u->profile_photo)) {
                        return filter_var($u->profile_photo, FILTER_VALIDATE_URL)
                            ? $u->profile_photo
                            : asset('storage/' . $u->profile_photo);
                    }
                }

                // 2. Cek foto profil langsung di record kependudukan Warga
                if (!empty($w->profile_photo)) {
                    return filter_var($w->profile_photo, FILTER_VALIDATE_URL)
                        ? $w->profile_photo
                        : asset('storage/' . $w->profile_photo);
                }

                // 3. Fallback: Inisial nama warga (AP, DP, FH, dst) dengan warna latar konsisten per role/gender
                $name = $w->nama ?: ($targetName ?: 'Warga');
                return static::generateInitialsAvatar($name, 'warga', $w->jenis_kelamin ?? null);
            }

            return static::generateInitialsAvatar($targetName ?: 'Warga', 'warga');
        } elseif ($targetRole === 'admin_rt') {
            $targetUser = null;
            if ($targetUserId) {
                $targetUser = User::find($targetUserId);
            }
            if (!$targetUser && $targetRt) {
                $targetUser = User::where(function ($q) {
                    $q->where('role', 'admin_rt')->orWhere('role', 'admin');
                })->where('rt_number', $targetRt)->first();
            }

            if ($targetUser && !empty($targetUser->profile_photo)) {
                return filter_var($targetUser->profile_photo, FILTER_VALIDATE_URL)
                    ? $targetUser->profile_photo
                    : asset('storage/' . $targetUser->profile_photo);
            }

            $name = $targetUser ? $targetUser->name : ($targetName ?: "Admin RT {$targetRt}");
            return static::generateInitialsAvatar($name, 'admin_rt');
        } elseif ($targetRole === 'admin_rw') {
            $targetUser = null;
            if ($targetUserId) {
                $targetUser = User::find($targetUserId);
            }
            if (!$targetUser) {
                $targetUser = User::where(function ($q) {
                    $q->where('role', 'admin_rw')->orWhere('role', 'admin');
                })->where('rt_number', '000')->first();
            }

            if ($targetUser && !empty($targetUser->profile_photo)) {
                return filter_var($targetUser->profile_photo, FILTER_VALIDATE_URL)
                    ? $targetUser->profile_photo
                    : asset('storage/' . $targetUser->profile_photo);
            }

            $name = $targetUser ? $targetUser->name : ($targetName ?: 'Admin RW 018');
            return static::generateInitialsAvatar($name, 'admin_rw');
        }

        return static::generateInitialsAvatar($targetName ?: 'Simulasi', $targetRole);
    }

    /**
     * Ekstraksi 2 huruf inisial dari nama (misal Adrian Pradana -> AP, Dirga Pradipta -> DP).
     */
    public static function extractInitials(string $name): string
    {
        $clean = preg_replace('/^(bpk\.?|ibu\.?|sdr\.?|sdri\.?|h\.?|hj\.?|ir\.?|dr\.?|drs\.?)\s+/i', '', trim($name));
        $words = preg_split('/\s+/', trim($clean ?: $name));
        $words = array_values(array_filter($words));

        if (empty($words)) {
            return 'NN';
        }

        if (count($words) === 1) {
            return strtoupper(substr($words[0], 0, min(2, strlen($words[0]))));
        }

        return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
    }

    /**
     * Generate SVG data URI inisial avatar yang mandiri tanpa ketergantungan jaringan eksternal.
     */
    public static function generateInitialsAvatar(string $name, string $role = 'warga', ?string $gender = null): string
    {
        $initials = static::extractInitials($name);

        $bg = match($role) {
            'admin_rw' => '#1E4633',
            'admin_rt' => '#0369A1',
            'bendahara' => '#7C3AED',
            'super_admin' => '#854D0E',
            default => ($gender && in_array(strtoupper($gender), ['P', 'PEREMPUAN', 'WANITA', 'FEMALE', 'F'])) ? '#BE185D' : '#285943',
        };

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 128 128" width="128" height="128">'
            . '<rect width="128" height="128" rx="64" fill="' . $bg . '"/>'
            . '<text x="50%" y="54%" dominant-baseline="middle" text-anchor="middle" fill="#FFFFFF" font-family="-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,sans-serif" font-weight="700" font-size="50" letter-spacing="1">' . htmlspecialchars($initials) . '</text>'
            . '</svg>';

        return 'data:image/svg+xml;utf8,' . rawurlencode($svg);
    }

    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class);
    }

    public function warga()
    {
        if (!empty($this->warga_id)) {
            return $this->belongsTo(Warga::class, 'warga_id');
        }
        return $this->hasOne(Warga::class, 'user_id', 'id');
    }

    /**
     * Resolusi profil kependudukan Warga secara fleksibel, tepat & idempoten.
     */
    public function resolveWarga(): ?Warga
    {
        // Khusus Super Admin dalam simulasi Role Switcher sebagai Warga
        if ($this->isRoleSwitched() && session('role_switch.target_role') === 'warga') {
            $targetWargaId = session('role_switch.target_warga_id');
            if (!empty($targetWargaId)) {
                $w = Warga::find($targetWargaId);
                if ($w) return $w;
            }

            $targetUserId = session('role_switch.target_user_id');
            if (!empty($targetUserId)) {
                $targetUser = User::find($targetUserId);
                if ($targetUser && $targetUser->id !== $this->id) {
                    $w = $targetUser->resolveWarga();
                    if ($w) return $w;
                }
            }

            // Jika tidak ditentukan target_user_id spesifik, otomatis ambil 1 warga sampel dari target RT
            $targetRt = session('role_switch.target_rt') ?: '006';
            $sampleWarga = Warga::where('rt_number', $targetRt)
                ->whereHas('pembayarans')
                ->first();
            if (!$sampleWarga) {
                $sampleWarga = Warga::where('rt_number', $targetRt)->whereNotNull('user_id')->first();
            }
            if (!$sampleWarga) {
                $sampleWarga = Warga::where('rt_number', $targetRt)->first();
            }
            if ($sampleWarga) {
                return $sampleWarga;
            }
        }

        if (!empty($this->warga_id)) {
            $w = Warga::find($this->warga_id);
            if ($w) return $w;
        }

        $w = Warga::where('user_id', $this->id)->first();
        if ($w) return $w;

        if (!empty($this->nik)) {
            $w = Warga::where('nik', $this->nik)->first();
            if ($w) return $w;
        }

        if (!empty($this->name)) {
            $w = Warga::where('nama', $this->name)->first();
            if ($w) return $w;

            if (!empty($this->rt_number)) {
                $w = Warga::where('nama', 'like', '%' . trim($this->name) . '%')
                    ->where('rt_number', $this->rt_number)
                    ->first();
                if ($w) return $w;
            }
        }

        return null;
    }

    /**
     * Ambil seluruh warga_id anggota keluarga dalam 1 Nomor KK yang sama.
     */
    public function familyWargaIds(): array
    {
        $warga = $this->resolveWarga();
        $noKk = $warga ? $warga->no_kk : ($this->no_kk ?? null);

        if (!empty($noKk) && !empty(trim($noKk))) {
            $ids = Warga::where('no_kk', trim($noKk))->pluck('id')->toArray();
            if (!empty($ids)) {
                return $ids;
            }
        }

        return $warga ? [$warga->id] : [];
    }

    /**
     * Deteksi jenis kelamin akun (dari tabel wargas.gender atau profil terkait).
     */
    public function getGenderAttribute(): ?string
    {
        $warga = $this->resolveWarga();
        if ($warga && !empty($warga->gender)) {
            $g = strtoupper(trim($warga->gender));
            if (in_array($g, ['P', 'L'], true)) {
                return $g;
            }
        }

        // Heuristic fallback nama jika tidak memiliki profil warga (misal super admin / pengurus)
        if (!empty($this->name)) {
            if (preg_match('/\b(anggi|fitri|siti|ayu|putri|dewi|ani|nur|linda|aisha|alya|nadia|nadine|clara|clarissa|ghiserabiella|bella|sarah|dina|maya|retno|rini|tari|lestari|sisca)\b/i', $this->name)) {
                return 'P';
            }
            if (preg_match('/\b(budi|rian|dirga|fathir|gavin|rizky|bagas|gibran|mahesa|dimas|adrian|angga|arjuna|arkan|aryo|bastian|bintang|ahmad|muhammad)\b/i', $this->name)) {
                return 'L';
            }
        }

        return null;
    }

    public function isFemale(): bool
    {
        return $this->gender === 'P';
    }

    public function isMale(): bool
    {
        return $this->gender === 'L';
    }

    public function getToastTheme(): string
    {
        if ($this->isFemale()) {
            return 'female';
        }
        if ($this->isMale()) {
            return 'male';
        }
        return 'neutral';
    }

    public function isAdmin()
    {
        if ($this->isRoleSwitched()) {
            $target = session('role_switch.target_role');
            return in_array($target, ['admin_rw', 'admin_rt', 'admin'], true);
        }
        return in_array($this->role, ['admin', 'admin_rt', 'admin_rw'], true);
    }

    public function isAdminRt()
    {
        if ($this->isRoleSwitched()) {
            return session('role_switch.target_role') === 'admin_rt';
        }
        return ($this->role === 'admin' && $this->rt_number !== '000') || $this->role === 'admin_rt';
    }

    public function isAdminRw()
    {
        if ($this->isRoleSwitched()) {
            return session('role_switch.target_role') === 'admin_rw';
        }
        return ($this->role === 'admin' && $this->rt_number === '000') || $this->role === 'admin_rw';
    }

    public function isRw()
    {
        return $this->isAdminRw();
    }

    public function isBendahara()
    {
        if ($this->isRoleSwitched()) {
            return false;
        }
        return $this->role === 'bendahara';
    }

    public function isWarga()
    {
        if ($this->isRoleSwitched()) {
            return session('role_switch.target_role') === 'warga';
        }
        return $this->role === 'warga';
    }

    /**
     * Posyandu Officer Assignments.
     */
    public function posyanduAssignments()
    {
        return $this->hasMany(\App\Models\PosyanduPetugas::class, 'user_id');
    }

    public function activePosyanduAssignment()
    {
        return $this->hasOne(\App\Models\PosyanduPetugas::class, 'user_id')
            ->where('status', 'aktif')
            ->where('status_verifikasi', 'terverifikasi')
            ->where('tanggal_mulai', '<=', now()->toDateString())
            ->where(function ($q) {
                $q->whereNull('tanggal_selesai')
                  ->orWhere('tanggal_selesai', '>=', now()->toDateString());
            });
    }

    public function isPosyanduKader(?string $rt = null): bool
    {
        $asg = $this->activePosyanduAssignment;
        if (!$asg || $asg->tipe_petugas !== 'kader') {
            return false;
        }
        if ($rt && $asg->wilayah_rt && $asg->wilayah_rt !== 'semua' && $asg->wilayah_rt !== '000') {
            return $asg->wilayah_rt === $rt;
        }
        return true;
    }

    public function isPosyanduNakes(?string $rt = null): bool
    {
        $asg = $this->activePosyanduAssignment;
        if (!$asg || $asg->tipe_petugas !== 'tenaga_kesehatan') {
            return false;
        }
        if ($rt && $asg->wilayah_rt && $asg->wilayah_rt !== 'semua' && $asg->wilayah_rt !== '000') {
            return $asg->wilayah_rt === $rt;
        }
        return true;
    }

    public function isPosyanduOfficer(?string $rt = null): bool
    {
        return $this->isAdminRw() || $this->isPosyanduKader($rt) || $this->isPosyanduNakes($rt);
    }

    public function canManagePosyanduPetugas(): bool
    {
        return $this->isAdminRw();
    }

    public function pengumumanReads()
    {
        return $this->hasMany(PengumumanRead::class, 'user_id');
    }

    public function pengumumanRsvps()
    {
        return $this->hasMany(PengumumanRsvp::class, 'user_id');
    }
}
