<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaduanHistory extends Model
{
    use HasFactory;

    protected $table = 'pengaduan_histories';

    protected $fillable = [
        'pengaduan_id',
        'user_id',
        'status_dari',
        'status_ke',
        'catatan',
        'is_internal',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
    ];

    public function pengaduan()
    {
        return $this->belongsTo(Pengaduan::class, 'pengaduan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
