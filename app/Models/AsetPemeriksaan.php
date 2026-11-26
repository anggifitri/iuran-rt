<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsetPemeriksaan extends Model
{
    use HasFactory;

    protected $table = 'aset_pemeriksaans';

    protected $fillable = [
        'aset_id',
        'user_id',
        'pengaduan_id',
        'jenis_kegiatan',
        'tanggal',
        'kondisi_sebelum',
        'kondisi_setelah',
        'catatan',
        'foto_sebelum',
        'foto_setelah',
        'biaya',
        'petugas_nama',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'biaya' => 'decimal:2',
    ];

    public function aset()
    {
        return $this->belongsTo(Aset::class, 'aset_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pengaduan()
    {
        return $this->belongsTo(Pengaduan::class, 'pengaduan_id');
    }
}
