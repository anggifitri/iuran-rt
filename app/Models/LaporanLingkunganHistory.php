<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanLingkunganHistory extends Model
{
    use HasFactory;

    protected $table = 'laporan_lingkungan_histories';

    protected $fillable = [
        'laporan_id',
        'user_id',
        'aksi',
        'status_sebelum',
        'status_sesudah',
        'catatan',
        'versi',
        'snapshot_data',
    ];

    protected $casts = [
        'snapshot_data' => 'array',
    ];

    public function laporan()
    {
        return $this->belongsTo(LaporanLingkungan::class, 'laporan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
