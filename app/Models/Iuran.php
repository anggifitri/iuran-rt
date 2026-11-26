<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Iuran extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'amount',
        'periode',
        'target_wilayah',
        'target_pembayar',
        'tanggal_mulai',
        'due_date',
        'type',
        'is_active',
    ];

    protected $casts = [
        'due_date' => 'date',
        'tanggal_mulai' => 'date',
        'is_active' => 'boolean',
        'amount' => 'decimal:2',
    ];

    public function pembayarans()
    {
        return $this->hasMany(PembayaranIuran::class, 'iuran_id');
    }
}
