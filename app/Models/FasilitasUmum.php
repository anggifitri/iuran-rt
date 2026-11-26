<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FasilitasUmum extends Model
{
    use HasFactory;

    protected $table = 'fasilitas_umums';

    protected $fillable = [
        'nama',
        'kategori',
        'deskripsi',
        'lokasi',
        'kapasitas',
        'status_operasional',
        'kondisi_fisik',
        'daftar_perlengkapan',
        'jam_operasional',
        'aturan_penggunaan',
        'biaya_sewa',
        'uang_jaminan',
        'penanggung_jawab',
        'kontak_pj',
        'foto',
        'syarat_ketentuan',
    ];

    protected $casts = [
        'kapasitas' => 'integer',
        'biaya_sewa' => 'float',
        'uang_jaminan' => 'float',
    ];

    public function peminjamans()
    {
        return $this->hasMany(PeminjamanFasilitas::class, 'fasilitas_id')->orderBy('waktu_mulai', 'desc');
    }

    public function pemeriksaans()
    {
        return $this->hasMany(PemeriksaanFasilitas::class, 'fasilitas_id');
    }

    public function getStatusOperasionalBadgeAttribute()
    {
        return match ($this->status_operasional) {
            'tersedia' => '<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5 fw-bold"><i class="fas fa-circle-check me-1"></i>Tersedia</span>',
            'digunakan' => '<span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-0.5 fw-bold"><i class="fas fa-door-open me-1"></i>Sedang Digunakan</span>',
            'pemeliharaan' => '<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-0.5 fw-bold"><i class="fas fa-wrench me-1"></i>Dalam Pemeliharaan</span>',
            default => '<span class="badge bg-light text-dark border px-2 py-0.5 fw-bold">' . ucfirst($this->status_operasional) . '</span>',
        };
    }

    public function getKondisiFisikBadgeAttribute()
    {
        return match ($this->kondisi_fisik) {
            'Baik' => '<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5 fw-bold"><i class="fas fa-thumbs-up me-1"></i>Kondisi Baik</span>',
            'Perlu Perawatan' => '<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-0.5 fw-bold"><i class="fas fa-triangle-exclamation me-1"></i>Perlu Perawatan</span>',
            'Tidak Tersedia' => '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0.5 fw-bold"><i class="fas fa-ban me-1"></i>Tidak Tersedia</span>',
            default => '<span class="badge bg-light text-dark border px-2 py-0.5 fw-bold">' . ucfirst($this->kondisi_fisik) . '</span>',
        };
    }
}
