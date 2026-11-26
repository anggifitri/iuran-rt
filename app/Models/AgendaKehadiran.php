<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgendaKehadiran extends Model
{
    use HasFactory;

    protected $table = 'agenda_kehadirans';

    protected $fillable = [
        'agenda_id',
        'user_id',
        'nama_peserta',
        'jabatan',
        'rt_number',
        'status', // 'hadir', 'tidak_hadir', 'izin'
        'keterangan',
        'waktu_kehadiran',
    ];

    protected $casts = [
        'waktu_kehadiran' => 'datetime',
    ];

    public function agenda()
    {
        return $this->belongsTo(AgendaRapat::class, 'agenda_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'hadir' => '<span class="badge rounded-pill px-2.5 py-1" style="background: var(--nx-mint); color: var(--nx-forest); font-weight: 700;"><i class="fas fa-check-circle me-1"></i>Hadir</span>',
            'izin' => '<span class="badge rounded-pill px-2.5 py-1" style="background: var(--nx-powder); color: #1e4b6d; font-weight: 700;"><i class="fas fa-info-circle me-1"></i>Izin</span>',
            'tidak_hadir' => '<span class="badge rounded-pill px-2.5 py-1" style="background: var(--nx-danger-soft); color: var(--nx-danger); font-weight: 700;"><i class="fas fa-times-circle me-1"></i>Tidak Hadir</span>',
            default => '<span class="badge rounded-pill px-2.5 py-1 bg-secondary text-white font-weight-bold">' . ucfirst($this->status) . '</span>',
        };
    }
}
