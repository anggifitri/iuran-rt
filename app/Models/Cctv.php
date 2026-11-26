<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cctv extends Model
{
    use HasFactory;

    protected $table = 'cctvs';

    protected $fillable = [
        'kode_kamera',
        'nama_kamera',
        'rt_number',
        'lokasi',
        'area_pantau',
        'status',
        'catatan_gangguan',
        'tanggal_pemasangan',
        'ip_address',
        'stream_url',
        'tipe_kamera',
        'kapasitas_penyimpanan',
        'maps_url',
        'last_online_at',
    ];

    protected $casts = [
        'tanggal_pemasangan' => 'date',
        'last_online_at' => 'datetime',
    ];

    public function recordings()
    {
        return $this->hasMany(CctvRecording::class, 'cctv_id')->orderBy('waktu_mulai', 'desc');
    }

    public function laporanKeamanans()
    {
        return $this->hasMany(LaporanKeamanan::class, 'cctv_id');
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'online' => '<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5 fw-bold"><i class="fas fa-circle-dot me-1 text-success"></i>Online</span>',
            'offline' => '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-0.5 fw-bold"><i class="fas fa-power-off me-1"></i>Offline</span>',
            'gangguan' => '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0.5 fw-bold"><i class="fas fa-triangle-exclamation me-1"></i>Gangguan</span>',
            default => '<span class="badge bg-light text-dark border px-2 py-0.5 fw-bold">' . ucfirst($this->status) . '</span>',
        };
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'online' => 'Online',
            'offline' => 'Offline',
            'gangguan' => 'Mengalami Gangguan',
            default => ucfirst($this->status),
        };
    }
}
