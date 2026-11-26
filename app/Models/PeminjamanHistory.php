<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeminjamanHistory extends Model
{
    use HasFactory;

    protected $table = 'peminjaman_histories';

    protected $fillable = [
        'peminjaman_id',
        'user_id',
        'status_sebelumnya',
        'status_baru',
        'keterangan',
        'petugas',
    ];

    public function peminjaman()
    {
        return $this->belongsTo(PeminjamanFasilitas::class, 'peminjaman_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
