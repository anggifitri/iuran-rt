<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalRonda extends Model
{
    use HasFactory;

    protected $table = 'jadwal_rondas';

    protected $fillable = [
        'rt_number',
        'tanggal',
        'shift',
        'lokasi_pos',
        'petugas',
        'koordinator',
        'kontak_koordinator',
        'status',
        'catatan',
        'laporan_patroli',
        'dokumentasi_patroli',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'terjadwal' => '<span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-0.5 fw-bold"><i class="fas fa-calendar me-1"></i>Terjadwal</span>',
            'berlangsung' => '<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-0.5 fw-bold"><i class="fas fa-person-running me-1"></i>Berlangsung</span>',
            'selesai' => '<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5 fw-bold"><i class="fas fa-check-circle me-1"></i>Selesai</span>',
            'tidak_terlaksana' => '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0.5 fw-bold"><i class="fas fa-circle-xmark me-1"></i>Tidak Terlaksana</span>',
            default => '<span class="badge bg-light text-dark border px-2 py-0.5 fw-bold">' . ucfirst($this->status) . '</span>',
        };
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'terjadwal' => 'Terjadwal',
            'berlangsung' => 'Sedang Berlangsung',
            'selesai' => 'Selesai Terlaksana',
            'tidak_terlaksana' => 'Tidak Terlaksana',
            default => ucfirst($this->status),
        };
    }
}
