<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankSampahKategori extends Model
{
    use HasFactory;

    protected $table = 'bank_sampah_kategoris';

    protected $fillable = [
        'kode',
        'nama_kategori',
        'deskripsi',
        'satuan',
        'harga_min',
        'harga_max',
        'harga_acuan',
        'tips_pemilahan',
        'icon',
        'accent_color',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'harga_min' => 'float',
        'harga_max' => 'float',
        'harga_acuan' => 'float',
        'is_active' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function priceHistories()
    {
        return $this->hasMany(BankSampahHargaHistory::class, 'kategori_id')->orderBy('created_at', 'desc');
    }

    public function setorans()
    {
        return $this->hasMany(BankSampah::class, 'kategori', 'kode');
    }

    public function getRangeLabelAttribute(): string
    {
        return 'Rp ' . number_format($this->harga_min, 0, ',', '.') . ' – Rp ' . number_format($this->harga_max, 0, ',', '.') . ' / ' . $this->satuan;
    }
}
