<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'rt_number', 'phone', 'address', 'no_kk', 'nik', 'profile_photo'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $appends = ['profile_photo_url'];

    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo) {
            if (filter_var($this->profile_photo, FILTER_VALIDATE_URL)) {
                return $this->profile_photo;
            }
            return asset('storage/' . $this->profile_photo);
        }

        $name = urlencode($this->name ?? 'User');
        return "https://ui-avatars.com/api/?name={$name}&background=2563eb&color=ffffff&rounded=true&size=128";
    }

    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class);
    }

    public function warga()
    {
        return $this->hasOne(Warga::class, 'nik', 'nik');
    }

    public function isAdmin()
    {
        return in_array($this->role, ['admin', 'admin_rt', 'admin_rw'], true);
    }

    public function isAdminRt()
    {
        return $this->role === 'admin_rt';
    }

    public function isAdminRw()
    {
        return $this->role === 'admin_rw';
    }

    public function isBendahara()
    {
        return $this->role === 'bendahara';
    }

    public function isWarga()
    {
        return $this->role === 'warga';
    }
}
