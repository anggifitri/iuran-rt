<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CctvRecording extends Model
{
    use HasFactory;

    protected $table = 'cctv_recordings';

    protected $fillable = [
        'cctv_id',
        'laporan_keamanan_id',
        'judul_rekaman',
        'waktu_mulai',
        'waktu_selesai',
        'ukuran_file',
        'file_path',
        'is_locked',
        'catatan',
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'is_locked' => 'boolean',
    ];

    public function cctv()
    {
        return $this->belongsTo(Cctv::class, 'cctv_id');
    }

    public function laporanKeamanan()
    {
        return $this->belongsTo(LaporanKeamanan::class, 'laporan_keamanan_id');
    }
}
