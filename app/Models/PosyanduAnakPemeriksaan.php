<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosyanduAnakPemeriksaan extends Model
{
    use HasFactory;

    protected $table = 'posyandu_anak_pemeriksaans';

    protected $fillable = [
        'posyandu_anak_id',
        'posyandu_id',
        'tanggal_pemeriksaan',
        'umur_bulan',
        'berat_badan',
        'tinggi_badan',
        'lingkar_kepala',
        'lingkar_lengan',
        'imt',
        'status_bb_u',
        'status_tb_u',
        'status_bb_tb',
        'status_imt_u',
        'status_tumbuh',
        'asi_eksklusif',
        'vitamin_a',
        'obat_cacing',
        'catatan_petugas',
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
        'umur_bulan'          => 'float',
        'berat_badan'         => 'float',
        'tinggi_badan'        => 'float',
        'lingkar_kepala'      => 'float',
        'lingkar_lengan'      => 'float',
        'imt'                 => 'float',
        'vitamin_a'           => 'boolean',
        'obat_cacing'         => 'boolean',
        'is_verified_nakes'   => 'boolean',
        'verified_nakes_at'   => 'datetime',
    ];

    public function anak()
    {
        return $this->belongsTo(PosyanduAnak::class, 'posyandu_anak_id');
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

    /**
     * Badge status visual helper.
     */
    public function getStatusBadgeAttribute(): array
    {
        $status = strtolower($this->status_tumbuh ?? 'normal');
        if (str_contains($status, 'buruk') || str_contains($status, 'stunt') || str_contains($status, 'sangat pendek')) {
            return ['bg' => 'var(--nx-soft-red)', 'color' => 'var(--nx-danger)', 'icon' => 'fa-triangle-exclamation', 'label' => $this->status_tumbuh];
        }
        if (str_contains($status, 'kurang') || str_contains($status, 'pendek')) {
            return ['bg' => 'var(--nx-warning-soft)', 'color' => '#B45309', 'icon' => 'fa-circle-exclamation', 'label' => $this->status_tumbuh];
        }
        if (str_contains($status, 'lebih') || str_contains($status, 'obes')) {
            return ['bg' => 'var(--nx-powder)', 'color' => '#1F517F', 'icon' => 'fa-info-circle', 'label' => $this->status_tumbuh];
        }
        return ['bg' => 'var(--nx-mint-soft)', 'color' => 'var(--nx-forest)', 'icon' => 'fa-circle-check', 'label' => 'Normal / Baik'];
    }
}
