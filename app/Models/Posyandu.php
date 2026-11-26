<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posyandu extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'tanggal',
        'lokasi',
        'keterangan',
        'status',
        'kategori',
        'jam_mulai',
        'jam_selesai',
        'penanggung_jawab',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function getStatusBadgeAttribute(): array
    {
        return match (strtolower($this->status ?? 'terjadwal')) {
            'selesai'    => ['bg' => 'var(--nx-mint-soft)', 'color' => 'var(--nx-forest)', 'icon' => 'fa-circle-check', 'label' => 'Selesai'],
            'dibatalkan' => ['bg' => 'var(--nx-soft-red)',  'color' => 'var(--nx-danger)', 'icon' => 'fa-circle-xmark', 'label' => 'Dibatalkan'],
            default      => ['bg' => 'var(--nx-powder)',    'color' => '#1F517F',          'icon' => 'fa-clock',        'label' => 'Terjadwal'],
        };
    }
}
