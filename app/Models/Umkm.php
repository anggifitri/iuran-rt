<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Umkm extends Model
{
    use HasFactory;

    protected $table = 'umkms';

    protected $fillable = [
        'user_id',
        'nama',
        'pemilik',
        'kategori',
        'telepon',
        'jam_operasional',
        'alamat',
        'deskripsi',
        'cover_image',
        'rt_number',
        'status_verifikasi', // 'terverifikasi', 'menunggu', 'ditolak'
        'verified_at',
        'verified_by',
        'rating_avg',
        'rating_count',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'rating_avg' => 'float',
        'rating_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function produks()
    {
        return $this->hasMany(UmkmProduk::class, 'umkm_id')->orderBy('created_at', 'desc');
    }

    public function ulasans()
    {
        return $this->hasMany(UmkmUlasan::class, 'umkm_id')->orderBy('created_at', 'desc');
    }

    public function visibleUlasans()
    {
        return $this->hasMany(UmkmUlasan::class, 'umkm_id')->where('is_hidden', false)->orderBy('created_at', 'desc');
    }

    public function favorits()
    {
        return $this->hasMany(UmkmFavorit::class, 'umkm_id');
    }

    /**
     * Recalculate average rating & count from visible reviews
     */
    public function refreshRating(): void
    {
        $reviews = $this->ulasans()->where('is_hidden', false)->get();
        $count = $reviews->count();
        $avg = $count > 0 ? round($reviews->avg('rating'), 2) : 0.00;

        $this->update([
            'rating_avg' => $avg,
            'rating_count' => $count,
        ]);
    }

    /**
     * Check if currently authenticated user has favorited this UMKM
     */
    public function getIsFavoritedAttribute(): bool
    {
        if (!Auth::check()) return false;
        return $this->favorits()->where('user_id', Auth::id())->exists();
    }

    /**
     * Rating stars display helper
     */
    public function getRatingStarsAttribute(): string
    {
        $score = round($this->rating_avg);
        $starsHtml = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $score) {
                $starsHtml .= '<i class="fas fa-star text-warning"></i>';
            } else {
                $starsHtml .= '<i class="far fa-star text-muted" style="opacity: 0.35;"></i>';
            }
        }
        return $starsHtml;
    }

    /**
     * Status badge helper
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status_verifikasi) {
            'terverifikasi' => '<span class="badge rounded-pill px-2.5 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700; font-size: 0.72rem;"><i class="fas fa-certificate me-1 text-success"></i>Terverifikasi RW 018</span>',
            'ditolak' => '<span class="badge rounded-pill px-2.5 py-1" style="background: var(--nx-danger-soft); color: var(--nx-danger); font-weight: 700; font-size: 0.72rem;"><i class="fas fa-times-circle me-1"></i>Verifikasi Ditolak</span>',
            default => '<span class="badge rounded-pill px-2.5 py-1" style="background: var(--nx-warning-soft); color: #B45309; font-weight: 700; font-size: 0.72rem;"><i class="fas fa-clock me-1"></i>Menunggu Verifikasi</span>',
        };
    }

    /**
     * Direct WhatsApp URL with greeting
     */
    public function getWhatsappUrlAttribute(): string
    {
        $phone = preg_replace('/[^0-9]/', '', $this->telepon ?? '');
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }
        if (empty($phone)) {
            $phone = '628123456789';
        }

        $text = "Halo {$this->pemilik}, saya warga RW 018 ingin bertanya seputar produk/layanan di {$this->nama}.";
        return 'https://wa.me/' . $phone . '?text=' . urlencode($text);
    }
}
