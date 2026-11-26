<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatformModuleConfig extends Model
{
    use HasFactory;

    protected $table = 'platform_module_configs';

    protected $fillable = [
        'module_key',
        'module_name',
        'icon',
        'description',
        'is_enabled_globally',
        'disabled_rw_numbers',
    ];

    protected $casts = [
        'is_enabled_globally' => 'boolean',
        'disabled_rw_numbers' => 'array',
    ];

    public function isEnabledForRw(?string $rwNumber = null): bool
    {
        if (!$this->is_enabled_globally) {
            return false;
        }
        if ($rwNumber && is_array($this->disabled_rw_numbers)) {
            return !in_array($rwNumber, $this->disabled_rw_numbers, true);
        }
        return true;
    }

    public function getCategoryAttribute(): string
    {
        return match ($this->module_key) {
            'dashboard', 'profil_saya' => 'Core & Pengguna',
            'warga', 'mutasi' => 'Kependudukan',
            'transaksi_kas', 'laporan_bulanan', 'iuran_warga' => 'Keuangan',
            'surat', 'pengaduan' => 'Pelayanan',
            'monitoring', 'laporan_rt' => 'Pemerintahan',
            'manajemen_aset', 'fasilitas' => 'Fasilitas & Aset',
            'ronda' => 'Keamanan Lingkungan',
            'laporan_lingkungan', 'bank_sampah' => 'Lingkungan Hidup',
            'agenda', 'kalender' => 'Agenda & Rapat',
            'umkm' => 'Ekonomi Warga',
            'posyandu' => 'Kesehatan Masyarakat',
            'pengumuman' => 'Komunikasi & Siaran',
            'audit_aktivitas' => 'Keamanan & Audit',
            default => 'Umum',
        };
    }

    public function getDependenciesAttribute(): array
    {
        return match ($this->module_key) {
            'transaksi_kas' => ['iuran_warga'],
            'laporan_bulanan' => ['transaksi_kas'],
            'iuran_warga' => ['warga'],
            'surat' => ['warga'],
            'pengaduan' => ['warga'],
            'monitoring' => ['warga'],
            'mutasi' => ['warga'],
            'laporan_rt' => ['warga'],
            'bank_sampah' => ['warga'],
            'ronda' => ['warga'],
            'fasilitas' => ['warga'],
            'kalender' => ['agenda'],
            'umkm' => ['warga'],
            'posyandu' => ['warga'],
            default => [],
        };
    }

    public function getHealthStatusAttribute(): array
    {
        return [
            'status' => 'healthy',
            'label' => 'Optimal (Tersedia)',
            'class' => 'bg-success-subtle text-success border border-success-subtle',
        ];
    }
}
