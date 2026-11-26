<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanRw extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_rw';

    protected $fillable = [
        'nomor_pengajuan',
        'rw_number',
        'nama_wilayah',
        'nama_pemohon',
        'email_pemohon',
        'phone_pemohon',
        'nik_pemohon',
        'kelurahan',
        'kecamatan',
        'kota',
        'provinsi',
        'alamat_sekretariat',
        'dokumen_sk',
        'status',
        'catatan_revisi',
        'alasan_penolakan',
        'catatan_admin',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'menunggu' => ['label' => 'Menunggu Review', 'class' => 'bg-warning-subtle text-warning border border-warning-subtle', 'icon' => 'fa-clock'],
            'revisi' => ['label' => 'Perlu Revisi', 'class' => 'bg-info-subtle text-info border border-info-subtle', 'icon' => 'fa-pen-to-square'],
            'disetujui' => ['label' => 'Disetujui', 'class' => 'bg-success-subtle text-success border border-success-subtle', 'icon' => 'fa-check-circle'],
            'ditolak' => ['label' => 'Ditolak', 'class' => 'bg-danger-subtle text-danger border border-danger-subtle', 'icon' => 'fa-xmark-circle'],
            default => ['label' => ucfirst($this->status), 'class' => 'bg-light text-dark', 'icon' => 'fa-circle-question'],
        };
    }
}
