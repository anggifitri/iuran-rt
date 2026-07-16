<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosyanduAnak extends Model
{
    use HasFactory;

    protected $table = 'posyandu_anaks';

    protected $fillable = [
        'nama_anak',
        'umur_bulan',
        'berat_badan',
        'tinggi_badan',
        'status_tumbuh',
        'solusi',
        'imunisasi_checked',
    ];

    protected $casts = [
        'imunisasi_checked' => 'array',
    ];
}
