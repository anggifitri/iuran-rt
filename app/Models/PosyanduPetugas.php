<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PosyanduPetugas extends Model
{
    use HasFactory;

    protected $table = 'posyandu_petugas';

    protected $fillable = [
        'user_id',
        'tipe_petugas', // 'kader' atau 'tenaga_kesehatan'
        'jabatan',
        'nomor_sk_str',
        'instansi',
        'posyandu_nama',
        'wilayah_rt',
        'tanggal_mulai',
        'tanggal_selesai',
        'status', // 'aktif', 'nonaktif', 'dicabut'
        'status_verifikasi', // 'terverifikasi', 'menunggu', 'ditolak'
        'kualifikasi_dokumen',
        'catatan_penugasan',
        'catatan_verifikasi',
        'assigned_by',
        'revoked_at',
        'revoked_by',
        'revocation_reason',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
        'revoked_at'      => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function revoker()
    {
        return $this->belongsTo(User::class, 'revoked_by');
    }

    /**
     * Scope for active and currently valid assignments.
     */
    public function scopeAktifValid($query)
    {
        return $query->where('status', 'aktif')
            ->where('status_verifikasi', 'terverifikasi')
            ->where('tanggal_mulai', '<=', now()->toDateString())
            ->where(function ($q) {
                $q->whereNull('tanggal_selesai')
                  ->orWhere('tanggal_selesai', '>=', now()->toDateString());
            });
    }

    public function scopeKader($query)
    {
        return $query->where('tipe_petugas', 'kader');
    }

    public function scopeNakes($query)
    {
        return $query->where('tipe_petugas', 'tenaga_kesehatan');
    }

    /**
     * Check if assignment is expired.
     */
    public function isExpired(): bool
    {
        return $this->tanggal_selesai && Carbon::parse($this->tanggal_selesai)->isPast();
    }

    /**
     * Status badge visual helper.
     */
    public function getStatusBadgeAttribute(): array
    {
        if ($this->status === 'dicabut') {
            return ['bg' => 'var(--nx-soft-red)', 'color' => 'var(--nx-danger)', 'icon' => 'fa-ban', 'label' => 'Dicabut'];
        }
        if ($this->status === 'nonaktif' || $this->isExpired()) {
            return ['bg' => 'var(--nx-bg)', 'color' => 'var(--nx-muted)', 'icon' => 'fa-clock', 'label' => 'Kedaluwarsa / Nonaktif'];
        }
        if ($this->status_verifikasi === 'menunggu') {
            return ['bg' => 'var(--nx-warning-soft)', 'color' => '#B45309', 'icon' => 'fa-hourglass-half', 'label' => 'Menunggu Verifikasi'];
        }
        if ($this->status_verifikasi === 'ditolak') {
            return ['bg' => 'var(--nx-soft-red)', 'color' => 'var(--nx-danger)', 'icon' => 'fa-times-circle', 'label' => 'Verifikasi Ditolak'];
        }
        return ['bg' => 'var(--nx-mint-soft)', 'color' => 'var(--nx-forest)', 'icon' => 'fa-certificate', 'label' => 'Aktif & Terverifikasi'];
    }

    /**
     * Tipe petugas badge.
     */
    public function getTipeBadgeAttribute(): array
    {
        if ($this->tipe_petugas === 'tenaga_kesehatan') {
            return [
                'bg'    => 'var(--nx-powder)',
                'color' => '#1F517F',
                'icon'  => 'fa-user-doctor',
                'label' => 'Tenaga Kesehatan (Medis)',
            ];
        }
        return [
            'bg'    => 'var(--nx-mint-soft)',
            'color' => 'var(--nx-forest)',
            'icon'  => 'fa-user-nurse',
            'label' => 'Kader Posyandu',
        ];
    }

    /**
     * Wilayah penugasan formatted text.
     */
    public function getWilayahTeksAttribute(): string
    {
        if (!$this->wilayah_rt || strtolower($this->wilayah_rt) === 'semua' || $this->wilayah_rt === '000') {
            return 'Seluruh Wilayah RW 018 (Semua RT)';
        }
        return 'Wilayah RT ' . $this->wilayah_rt;
    }
}
