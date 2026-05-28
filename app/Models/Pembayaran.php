<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    // Explicitly define the table name as 'transactions' based on controller usage
    protected $table = 'transactions';

    protected $fillable = [
        'warga_id',
        'tipe',
        'jumlah',
        'kategori',
        'keterangan',
        'tanggal',
    ];

    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }
}
