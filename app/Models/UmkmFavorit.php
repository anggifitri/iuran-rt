<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UmkmFavorit extends Model
{
    use HasFactory;

    protected $table = 'umkm_favorits';

    protected $fillable = [
        'umkm_id',
        'user_id',
    ];

    public function umkm()
    {
        return $this->belongsTo(Umkm::class, 'umkm_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
