<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanLingkunganPeriode extends Model
{
    use HasFactory;

    protected $table = 'laporan_lingkungan_periodes';

    protected $fillable = [
        'periode_bulan',
        'periode_tahun',
        'tanggal_mulai',
        'batas_waktu',
        'status',
        'catatan_instruksi_rw',
        'created_by',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'batas_waktu' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
