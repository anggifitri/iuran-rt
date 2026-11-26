<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanRt extends Model
{
    use HasFactory;

    protected $table = 'laporan_rts';

    protected $fillable = [
        'rt_number',
        'bulan',
        'tahun',
        'pengirim_id',
        'status', // draft, menunggu, diterima, perlu_revisi
        'snapshot_data',
        'evaluasi_kegiatan',
        'kendala',
        'catatan_tambahan',
        'catatan_pemeriksaan_rw',
        'pemeriksa_id',
        'waktu_pemeriksaan',
        'riwayat_revisi',
        'version',
    ];

    protected $casts = [
        'snapshot_data' => 'array',
        'riwayat_revisi' => 'array',
        'waktu_pemeriksaan' => 'datetime',
    ];

    public function pengirim()
    {
        return $this->belongsTo(User::class, 'pengirim_id');
    }

    public function pemeriksa()
    {
        return $this->belongsTo(User::class, 'pemeriksa_id');
    }
}
