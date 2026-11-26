<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UmkmUlasan extends Model
{
    use HasFactory;

    protected $table = 'umkm_ulasans';

    protected $fillable = [
        'umkm_id',
        'user_id',
        'nama_pembeli',
        'rating',
        'komentar',
        'is_verified_purchase',
        'balasan_penjual',
        'balasan_at',
        'is_reported',
        'report_reason',
        'is_hidden',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_verified_purchase' => 'boolean',
        'balasan_at' => 'datetime',
        'is_reported' => 'boolean',
        'is_hidden' => 'boolean',
    ];

    public function umkm()
    {
        return $this->belongsTo(Umkm::class, 'umkm_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getRatingStarsAttribute()
    {
        $starsHtml = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $this->rating) {
                $starsHtml .= '<i class="fas fa-star text-warning"></i>';
            } else {
                $starsHtml .= '<i class="far fa-star text-muted" style="opacity: 0.4;"></i>';
            }
        }
        return $starsHtml;
    }
}
