<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosyanduBumil extends Model
{
    use HasFactory;

    protected $table = 'posyandu_bumils';

    protected $fillable = [
        'nama_ibu',
        'usia_kehamilan_minggu',
        'berat_badan',
        'tekanan_darah',
        'lila',
        'status_kesehatan',
        'solusi',
    ];
}
