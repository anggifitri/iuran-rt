<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankSampah extends Model
{
    use HasFactory;

    protected $table = 'bank_sampahs';

    protected $fillable = [
        'nomor_transaksi',
        'rt_number',
        'warga_id',
        'user_id',
        'nama_penyetor',
        'tanggal_setor',
        'kategori',
        'berat_kg',
        'satuan',
        'harga_per_satuan',
        'nilai_rupiah',
        'status', // menunggu, diterima, ditolak
        'alasan_penolakan',
        'foto_dokumentasi',
        'catatan',
        'petugas',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'tanggal_setor' => 'date',
        'berat_kg' => 'float',
        'harga_per_satuan' => 'float',
        'nilai_rupiah' => 'float',
        'verified_at' => 'datetime',
    ];

    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function kategoriDetail()
    {
        return $this->belongsTo(BankSampahKategori::class, 'kategori', 'kode');
    }

    public function isDiterima(): bool
    {
        return $this->status === 'diterima';
    }

    public function isMenunggu(): bool
    {
        return in_array($this->status, ['menunggu', 'pending']);
    }

    public function isDitolak(): bool
    {
        return $this->status === 'ditolak';
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'diterima' => '<span class="badge bg-success rounded-pill px-2.5 py-1">Diterima</span>',
            'menunggu', 'pending' => '<span class="badge bg-warning text-dark rounded-pill px-2.5 py-1">Menunggu Konfirmasi</span>',
            'ditolak' => '<span class="badge bg-danger rounded-pill px-2.5 py-1">Ditolak</span>',
            default => '<span class="badge bg-secondary rounded-pill px-2.5 py-1">' . ucfirst($this->status) . '</span>',
        };
    }
}
