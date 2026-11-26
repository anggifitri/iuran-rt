<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aset extends Model
{
    use HasFactory;

    protected $table = 'asets';

    protected $fillable = [
        'nomor_aset',
        'nama',
        'kategori',
        'rt_number',
        'rw_number',
        'lokasi',
        'latitude',
        'longitude',
        'kondisi',
        'status_pemeliharaan',
        'tanggal_perolehan',
        'terakhir_diperiksa',
        'foto',
        'spesifikasi',
    ];

    protected $casts = [
        'tanggal_perolehan' => 'date',
        'terakhir_diperiksa' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function pengaduans()
    {
        return $this->hasMany(Pengaduan::class, 'aset_id')->latest();
    }

    public function pemeriksaans()
    {
        return $this->hasMany(AsetPemeriksaan::class, 'aset_id')->latest('tanggal');
    }

    public function dokumentasis()
    {
        return $this->hasMany(AsetDokumentasi::class, 'aset_id')->latest();
    }

    public function getGoogleMapsUrlAttribute()
    {
        if ($this->latitude && $this->longitude) {
            return "https://www.google.com/maps/search/?api=1&query={$this->latitude},{$this->longitude}";
        }
        if (!empty($this->lokasi)) {
            $query = urlencode($this->lokasi . ", RW 018");
            return "https://www.google.com/maps/search/?api=1&query={$query}";
        }
        return null;
    }

    public function getKondisiBadgeAttribute()
    {
        return match($this->kondisi) {
            'baik' => ['label' => 'Kondisi Baik', 'class' => 'bg-success', 'bg' => 'var(--nx-mint)', 'text' => 'var(--nx-forest)'],
            'rusak_ringan' => ['label' => 'Rusak Ringan', 'class' => 'bg-warning text-dark', 'bg' => '#FEF3C7', 'text' => '#92400E'],
            'rusak_berat' => ['label' => 'Rusak Berat', 'class' => 'bg-danger', 'bg' => '#FEE2E2', 'text' => '#991B1B'],
            'dalam_perbaikan' => ['label' => 'Dalam Perbaikan', 'class' => 'bg-info text-white', 'bg' => '#E0F2FE', 'text' => '#0369A1'],
            default => ['label' => ucfirst($this->kondisi), 'class' => 'bg-secondary', 'bg' => '#F1F5F9', 'text' => '#475569'],
        };
    }
}
