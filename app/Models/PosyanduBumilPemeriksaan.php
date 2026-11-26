<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosyanduBumilPemeriksaan extends Model
{
    use HasFactory;

    protected $table = 'posyandu_bumil_pemeriksaans';

    protected $fillable = [
        'posyandu_bumil_id',
        'posyandu_id',
        'tanggal_pemeriksaan',
        'usia_kehamilan_minggu',
        'berat_badan',
        'tekanan_darah',
        'tinggi_fundus',
        'lila',
        'detak_jantung_janin',
        'keluhan',
        'pemberian_ttd',
        'status_kesehatan',
        'catatan_petugas',
        'jadwal_berikutnya',
        'petugas_nama',
        'is_verified_nakes',
        'verified_by_nakes_id',
        'verified_nakes_at',
        'catatan_nakes',
        'rekomendasi_rujukan',
        'created_by',
    ];

    protected $casts = [
        'tanggal_pemeriksaan' => 'date',
        'jadwal_berikutnya'   => 'date',
        'berat_badan'         => 'float',
        'lila'                => 'float',
        'tinggi_fundus'       => 'float',
        'pemberian_ttd'       => 'integer',
        'usia_kehamilan_minggu' => 'integer',
        'is_verified_nakes'   => 'boolean',
        'verified_nakes_at'   => 'datetime',
    ];

    public function bumil()
    {
        return $this->belongsTo(PosyanduBumil::class, 'posyandu_bumil_id');
    }

    public function posyandu()
    {
        return $this->belongsTo(Posyandu::class, 'posyandu_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function verifiedNakes()
    {
        return $this->belongsTo(User::class, 'verified_by_nakes_id');
    }

    public function getStatusBadgeAttribute(): array
    {
        $status = strtolower($this->status_kesehatan ?? 'normal');
        if (str_contains($status, 'kek') || str_contains($status, 'hipertensi') || str_contains($status, 'bahaya')) {
            return ['bg' => 'var(--nx-soft-red)', 'color' => 'var(--nx-danger)', 'icon' => 'fa-triangle-exclamation', 'label' => $this->status_kesehatan];
        }
        if (str_contains($status, 'anemia') || str_contains($status, 'kurang')) {
            return ['bg' => 'var(--nx-warning-soft)', 'color' => '#B45309', 'icon' => 'fa-circle-exclamation', 'label' => $this->status_kesehatan];
        }
        return ['bg' => 'var(--nx-mint-soft)', 'color' => 'var(--nx-forest)', 'icon' => 'fa-circle-check', 'label' => 'Kondisi Baik / Normal'];
    }
}
