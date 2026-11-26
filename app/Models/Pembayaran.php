<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    // Explicitly define the table name as 'transactions' based on legacy project usage
    protected $table = 'transactions';

    protected $fillable = [
        'nomor_transaksi',
        'warga_id',
        'rt_number',
        'tipe',
        'jumlah',
        'kategori',
        'keterangan',
        'sumber_tujuan',
        'metode_pembayaran',
        'nomor_referensi',
        'bukti_transaksi',
        'status',
        'pembayaran_id',
        'created_by',
        'tanggal',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'integer',
    ];

    protected $appends = ['nomor_transaksi_formatted'];

    public function getNomorTransaksiFormattedAttribute()
    {
        if (!empty($this->nomor_transaksi)) {
            return $this->nomor_transaksi;
        }
        $year = $this->tanggal ? date('Y', strtotime($this->tanggal)) : '2026';
        return sprintf('TRX-%s-%05d', $year, $this->id);
    }

    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function pembayaranIuran()
    {
        return $this->belongsTo(PembayaranIuran::class, 'pembayaran_id');
    }
}
