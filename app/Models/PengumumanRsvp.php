<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengumumanRsvp extends Model
{
    use HasFactory;

    protected $table = 'pengumuman_rsvps';

    protected $fillable = [
        'pengumuman_id',
        'user_id',
        'warga_id',
        'response',
        'catatan',
        'responded_at',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    public function pengumuman()
    {
        return $this->belongsTo(Pengumuman::class, 'pengumuman_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function warga()
    {
        return $this->belongsTo(Warga::class, 'warga_id');
    }

    public function getResponseLabelAttribute(): string
    {
        return match($this->response) {
            'hadir'        => 'Hadir',
            'tidak_hadir'  => 'Tidak Hadir',
            'ikut'         => 'Ikut Berpartisipasi',
            'tidak_ikut'   => 'Tidak Dapat Hadir',
            'konfirmasi'   => 'Sudah Membaca & Mengerti',
            default        => ucfirst($this->response),
        };
    }

    public function getResponseBadgeAttribute(): array
    {
        return match($this->response) {
            'hadir', 'ikut', 'konfirmasi' => [
                'bg'    => '#DCEBDD',
                'color' => '#285943',
                'icon'  => 'fa-circle-check',
            ],
            'tidak_hadir', 'tidak_ikut' => [
                'bg'    => '#FCE8E9',
                'color' => '#E53E3E',
                'icon'  => 'fa-circle-xmark',
            ],
            default => [
                'bg'    => '#EDF2F7',
                'color' => '#4A5568',
                'icon'  => 'fa-circle-question',
            ],
        };
    }
}
