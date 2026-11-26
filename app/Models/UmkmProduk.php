<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UmkmProduk extends Model
{
    use HasFactory;

    protected $table = 'umkm_produks';

    protected $fillable = [
        'umkm_id',
        'nama_produk',
        'deskripsi',
        'harga',
        'foto',
        'is_tersedia',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'is_tersedia' => 'boolean',
    ];

    public function umkm()
    {
        return $this->belongsTo(Umkm::class, 'umkm_id');
    }

    public function getHargaFormattedAttribute()
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }

    public function getFotoUrlAttribute()
    {
        if (!empty($this->foto)) {
            if (preg_match('/^https?:\/\//', $this->foto)) {
                return $this->foto;
            }
            return asset('storage/' . $this->foto);
        }
        return 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=400&q=80';
    }
}
