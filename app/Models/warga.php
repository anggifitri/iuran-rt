<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        'tanggal_lahir',
        'rt_number',
        'rw_number',
        'is_kk',
        'kk_id',
        'alamat',
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

        $name = urlencode($this->nama ?? 'Warga');
        return "https://ui-avatars.com/api/?name={$name}&background=2563eb&color=ffffff&rounded=true&size=128";
    }

    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class, 'warga_id');
    }
    public function anggotaKeluarga()
    {
        return $this->hasMany(Warga::class, 'kk_id', 'id');
    }

    public function kepalaKeluarga()
    {
        return $this->belongsTo(Warga::class, 'kk_id', 'id');
    }

    public function getUmurAttribute()
    {
        if (!$this->tanggal_lahir) return 0;
        return Carbon::parse($this->tanggal_lahir)->age;
    }
}
