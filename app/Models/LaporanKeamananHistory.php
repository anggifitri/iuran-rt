<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanKeamananHistory extends Model
{
    use HasFactory;

    protected $table = 'laporan_keamanan_histories';

    protected $fillable = [
        'laporan_keamanan_id',
        'user_id',
        'status_sebelumnya',
        'status_baru',
        'keterangan',
        'petugas',
    ];

    public function laporan()
    {
        return $this->belongsTo(LaporanKeamanan::class, 'laporan_keamanan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
