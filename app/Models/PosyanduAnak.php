<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PosyanduAnak extends Model
{
    use HasFactory;

    protected $table = 'posyandu_anaks';

    protected $fillable = [
        'warga_id',
        'nama_anak',
        'nik',
        'jenis_kelamin',
        'tanggal_lahir',
        'nama_ibu',
        'nama_ayah',
        'no_telepon',
        'rt_number',
        'alamat',
        'berat_lahir',
        'tinggi_lahir',
        'lingkar_kepala_lahir',
        'golongan_darah',
        'umur_bulan',
        'berat_badan',
        'tinggi_badan',
        'status_tumbuh',
        'solusi',
        'imunisasi_checked',
        'catatan_khusus',
        'perlu_tindak_lanjut',
        'alasan_tindak_lanjut',
    ];

    protected $casts = [
        'tanggal_lahir'        => 'date',
        'umur_bulan'           => 'float',
        'berat_badan'          => 'float',
        'tinggi_badan'         => 'float',
        'berat_lahir'          => 'float',
        'tinggi_lahir'         => 'float',
        'lingkar_kepala_lahir' => 'float',
        'imunisasi_checked'    => 'array',
        'perlu_tindak_lanjut'  => 'boolean',
    ];

    public function warga()
    {
        return $this->belongsTo(Warga::class, 'warga_id');
    }

    public function pemeriksaans()
    {
        return $this->hasMany(PosyanduAnakPemeriksaan::class, 'posyandu_anak_id')->orderBy('tanggal_pemeriksaan', 'asc');
    }

    public function latestPemeriksaan()
    {
        return $this->hasOne(PosyanduAnakPemeriksaan::class, 'posyandu_anak_id')->latestOfMany('tanggal_pemeriksaan');
    }

    public function imunisasis()
    {
        return $this->hasMany(PosyanduAnakImunisasi::class, 'posyandu_anak_id')->orderBy('id', 'asc');
    }

    /**
     * Exact age calculation formatted in years and months.
     */
    public function getUsiaTeksAttribute(): string
    {
        if ($this->tanggal_lahir) {
            $diff = Carbon::parse($this->tanggal_lahir)->diff(now());
            if ($diff->y > 0) {
                return "{$diff->y} Thn {$diff->m} Bln";
            }
            return "{$diff->m} Bulan " . ($diff->d > 0 ? "{$diff->d} Hari" : "");
        }
        $m = round($this->umur_bulan ?? 0);
        $years = floor($m / 12);
        $remMonths = $m % 12;
        if ($years > 0) {
            return "{$years} Thn {$remMonths} Bln";
        }
        return "{$m} Bulan";
    }

    /**
     * Gender label.
     */
    public function getJenisKelaminTeksAttribute(): string
    {
        return strtoupper($this->jenis_kelamin ?? 'L') === 'P' ? 'Perempuan' : 'Laki-laki';
    }

    /**
     * Status badge.
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

    /**
     * Sync latest examination back to main record.
     */
    public function syncLatestExamination(): void
    {
        $latest = $this->pemeriksaans()->orderBy('tanggal_pemeriksaan', 'desc')->first();
        if ($latest) {
            $this->umur_bulan = $latest->umur_bulan;
            $this->berat_badan = $latest->berat_badan;
            $this->tinggi_badan = $latest->tinggi_badan;
            $this->status_tumbuh = $latest->status_tumbuh;
            if ($latest->catatan_petugas) {
                $this->solusi = $latest->catatan_petugas;
            }
            $this->perlu_tindak_lanjut = (
                str_contains(strtolower($latest->status_tumbuh), 'kurang') ||
                str_contains(strtolower($latest->status_tumbuh), 'stunt') ||
                str_contains(strtolower($latest->status_tumbuh), 'buruk')
            );
            if ($this->perlu_tindak_lanjut) {
                $this->alasan_tindak_lanjut = $latest->status_tumbuh;
            } else {
                $this->alasan_tindak_lanjut = null;
            }
            $this->save();
        }
    }
}
