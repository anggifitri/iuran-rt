<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warga extends Model
{
    use HasFactory;

    // Explicitly define the table name as 'warga'
    protected $table = 'wargas';

    protected $fillable = [
        'nama',
        'blok_rumah',
        'nomor_hp',
        'gender',
        'no_kk',
        'nik',
        'profile_photo',
    ];

    protected $appends = ['profile_photo_url'];

    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo) {
            return asset('storage/' . $this->profile_photo);
        }

        $name = urlencode($this->nama ?? 'Warga');
        return "https://ui-avatars.com/api/?name={$name}&background=2563eb&color=ffffff&rounded=true&size=128";
    }

    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class, 'warga_id');
    }
}
