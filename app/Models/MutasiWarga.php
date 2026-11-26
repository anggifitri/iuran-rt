<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MutasiWarga extends Model
{
    use HasFactory;

    protected $table = 'mutasi_wargas';

    protected $fillable = [
        'warga_id',
        'nama_warga',
        'nik',
        'tipe', // masuk, keluar
        'rt_asal',
        'rt_tujuan',
        'alamat_asal',
        'alamat_tujuan',
        'tanggal_mutasi',
        'alasan',
        'dokumen_pendukung',
        'status',
        'catatan',
        'created_by',
    ];

    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
