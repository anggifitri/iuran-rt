<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankSampahEdukasi extends Model
{
    use HasFactory;

    protected $table = 'bank_sampah_edukasis';

    protected $fillable = [
        'judul',
        'kategori_materi',
        'deskripsi_singkat',
        'isi_materi',
        'icon',
        'gambar_ilustrasi',
        'status',
        'created_by',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
