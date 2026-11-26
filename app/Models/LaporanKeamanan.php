<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanKeamanan extends Model
{
    use HasFactory;

    protected $table = 'laporan_keamanans';

    protected $fillable = [
        'nomor_laporan',
        'user_id',
        'nama_pelapor',
        'kontak_pelapor',
        'rt_number',
        'judul',
        'kategori',
        'waktu_kejadian',
        'lokasi',
        'cctv_id',
        'kronologi',
        'dokumentasi',
        'dokumentasi_penanganan',
        'status',
        'tindak_lanjut',
        'petugas_penanganan',
        'selesai_at',
    ];

    protected $casts = [
        'waktu_kejadian' => 'datetime',
        'selesai_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cctv()
    {
        return $this->belongsTo(Cctv::class, 'cctv_id');
    }

    public function histories()
    {
        return $this->hasMany(LaporanKeamananHistory::class, 'laporan_keamanan_id')->orderBy('created_at', 'asc');
    }

    public function recordings()
    {
        return $this->hasMany(CctvRecording::class, 'laporan_keamanan_id');
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'baru' => '<span class="badge bg-info-subtle text-info border border-info-subtle px-2.5 py-1 fw-bold"><i class="fas fa-bullhorn me-1"></i>Baru Dilaporkan</span>',
            'menunggu' => '<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1 fw-bold"><i class="fas fa-clock me-1"></i>Menunggu Penanganan</span>',
            'diproses' => '<span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 fw-bold"><i class="fas fa-spinner fa-spin me-1"></i>Sedang Ditangani</span>',
            'selesai' => '<span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 fw-bold"><i class="fas fa-circle-check me-1"></i>Selesai</span>',
            default => '<span class="badge bg-light text-dark border px-2 py-0.5 fw-bold">' . ucfirst($this->status) . '</span>',
        };
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'baru' => 'Baru Dilaporkan',
            'menunggu' => 'Menunggu Penanganan',
            'diproses' => 'Sedang Ditangani',
            'selesai' => 'Selesai',
            default => ucfirst($this->status),
        };
    }
}
