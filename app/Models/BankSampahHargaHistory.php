<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankSampahHargaHistory extends Model
{
    use HasFactory;

    protected $table = 'bank_sampah_harga_histories';

    protected $fillable = [
        'kategori_id',
        'harga_lama',
        'harga_baru',
        'user_id',
        'catatan',
    ];

    protected $casts = [
        'harga_lama' => 'float',
        'harga_baru' => 'float',
    ];

    public function kategori()
    {
        return $this->belongsTo(BankSampahKategori::class, 'kategori_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
