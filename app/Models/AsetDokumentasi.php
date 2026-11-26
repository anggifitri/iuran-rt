<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsetDokumentasi extends Model
{
    use HasFactory;

    protected $table = 'aset_dokumentasis';

    protected $fillable = [
        'aset_id',
        'pengaduan_id',
        'user_id',
        'foto',
        'tipe_dokumentasi',
        'keterangan',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function aset()
    {
        return $this->belongsTo(Aset::class, 'aset_id');
    }

    public function pengaduan()
    {
        return $this->belongsTo(Pengaduan::class, 'pengaduan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
