<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankSampahPenarikan extends Model
{
    use HasFactory;

    protected $table = 'bank_sampah_penarikans';

    protected $fillable = [
        'nomor_penarikan',
        'warga_id',
        'user_id',
        'rt_number',
        'tanggal_penarikan',
        'jumlah_penarikan',
        'metode_penarikan',
        'petugas',
        'catatan',
        'status',
    ];

    protected $casts = [
        'tanggal_penarikan' => 'date',
        'jumlah_penarikan' => 'float',
    ];

    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
