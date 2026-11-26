<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranIuran extends Model
{
    use HasFactory;

    protected $table = 'pembayarans';

    protected $fillable = [
        'user_id',
        'warga_id',
        'rt_number',
        'iuran_id',
        'amount_paid',
        'nominal_tagihan',
        'payment_date',
        'bulan',
        'tahun',
        'status', // pending, confirmed, rejected, cancelled
        'payment_method',
        'proof_image',
        'notes',
        'confirmed_by',
        'transaction_id',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount_paid' => 'decimal:2',
        'nominal_tagihan' => 'decimal:2',
    ];

    public function iuran()
    {
        return $this->belongsTo(Iuran::class, 'iuran_id');
    }

    public function warga()
    {
        return $this->belongsTo(Warga::class, 'warga_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function transaction()
    {
        return $this->belongsTo(Pembayaran::class, 'transaction_id');
    }
}
