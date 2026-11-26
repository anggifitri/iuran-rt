<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosyanduAnakImunisasi extends Model
{
    use HasFactory;

    protected $table = 'posyandu_anak_imunisasis';

    protected $fillable = [
        'posyandu_anak_id',
        'vaksin_nama',
        'tanggal_pemberian',
        'status', // Sudah Diberikan, Terjadwal, Terlewat
        'jadwal_berikutnya',
        'batch_nomor',
        'petugas_nama',
        'catatan_petugas',
    ];

    protected $casts = [
        'tanggal_pemberian' => 'date',
        'jadwal_berikutnya' => 'date',
    ];

    public function anak()
    {
        return $this->belongsTo(PosyanduAnak::class, 'posyandu_anak_id');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'Sudah Diberikan' => ['bg' => 'var(--nx-mint-soft)', 'color' => 'var(--nx-forest)', 'icon' => 'fa-circle-check'],
            'Terlewat'        => ['bg' => 'var(--nx-soft-red)',  'color' => 'var(--nx-danger)', 'icon' => 'fa-circle-xmark'],
            default           => ['bg' => 'var(--nx-powder)',    'color' => '#1F517F',          'icon' => 'fa-clock'],
        };
    }
}
