<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PosyanduBumil extends Model
{
    use HasFactory;

    protected $table = 'posyandu_bumils';

    protected $fillable = [
        'warga_id',
        'nama_ibu',
        'nik',
        'nama_suami',
        'tanggal_lahir',
        'hpht',
        'hpl',
        'usia_kehamilan_minggu',
        'hamil_ke',
        'golongan_darah',
        'no_telepon',
        'rt_number',
        'alamat',
        'berat_badan',
        'tekanan_darah',
        'lila',
        'status_kesehatan',
        'status_kehamilan',
        'solusi',
        'perlu_tindak_lanjut',
        'alasan_tindak_lanjut',
    ];

    protected $casts = [
        'tanggal_lahir'        => 'date',
        'hpht'                 => 'date',
        'hpl'                  => 'date',
        'usia_kehamilan_minggu'=> 'integer',
        'hamil_ke'             => 'integer',
        'berat_badan'          => 'float',
        'lila'                 => 'float',
        'perlu_tindak_lanjut'  => 'boolean',
    ];

    public function warga()
    {
        return $this->belongsTo(Warga::class, 'warga_id');
    }

    public function pemeriksaans()
    {
        return $this->hasMany(PosyanduBumilPemeriksaan::class, 'posyandu_bumil_id')->orderBy('tanggal_pemeriksaan', 'asc');
    }

    public function latestPemeriksaan()
    {
        return $this->hasOne(PosyanduBumilPemeriksaan::class, 'posyandu_bumil_id')->latestOfMany('tanggal_pemeriksaan');
    }

    /**
     * Trimester label.
     */
    public function getTrimesterAttribute(): string
    {
        $w = $this->usia_kehamilan_minggu ?? 1;
        if ($w <= 13) {
            return 'Trimester I';
        }
        if ($w <= 27) {
            return 'Trimester II';
        }
        return 'Trimester III';
    }

    /**
     * Estimated Delivery Date countdown formatted.
     */
    public function getHplCountdownAttribute(): string
    {
        if (!$this->hpl) {
            return '-';
        }
        $target = Carbon::parse($this->hpl)->startOfDay();
        $today = now()->startOfDay();
        $diff = (int) round($today->diffInDays($target, false));
        if ($diff < 0) {
            return 'Lewat HPL (' . abs($diff) . ' Hari Lalu)';
        }
        if ($diff === 0) {
            return 'Hari Ini (HPL)';
        }
        return $diff . ' Hari Lagi';
    }

    /**
     * Status badge.
     */
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

    /**
     * Sync latest examination back to main record.
     */
    public function syncLatestExamination(): void
    {
        $latest = $this->pemeriksaans()->orderBy('tanggal_pemeriksaan', 'desc')->first();
        if ($latest) {
            $this->usia_kehamilan_minggu = $latest->usia_kehamilan_minggu;
            $this->berat_badan = $latest->berat_badan;
            $this->tekanan_darah = $latest->tekanan_darah;
            $this->lila = $latest->lila;
            $this->status_kesehatan = $latest->status_kesehatan;
            if ($latest->catatan_petugas) {
                $this->solusi = $latest->catatan_petugas;
            }
            $this->perlu_tindak_lanjut = (
                str_contains(strtolower($latest->status_kesehatan), 'kek') ||
                str_contains(strtolower($latest->status_kesehatan), 'hipertensi') ||
                str_contains(strtolower($latest->status_kesehatan), 'anemia')
            );
            if ($this->perlu_tindak_lanjut) {
                $this->alasan_tindak_lanjut = $latest->status_kesehatan;
            } else {
                $this->alasan_tindak_lanjut = null;
            }
            $this->save();
        }
    }
}
