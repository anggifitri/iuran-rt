<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeminjamanFasilitas extends Model
{
    use HasFactory;

    protected $table = 'peminjaman_fasilitas';

    protected $fillable = [
        'nomor_pengajuan',
        'fasilitas_id',
        'user_id',
        'nik_peminjam',
        'nama_peminjam',
        'rt_number',
        'nomor_kontak',
        'tujuan',
        'jumlah_peserta',
        'waktu_mulai',
        'waktu_selesai',
        'status',
        'catatan_pengelola',
        'alasan_penolakan',
        'dokumen_pendukung',
        'biaya_sewa',
        'uang_jaminan',
        'status_pembayaran',
        'disetujui_oleh',
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'jumlah_peserta' => 'integer',
        'biaya_sewa' => 'float',
        'uang_jaminan' => 'float',
    ];

    public function fasilitas()
    {
        return $this->belongsTo(FasilitasUmum::class, 'fasilitas_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function pemeriksaan()
    {
        return $this->hasOne(PemeriksaanFasilitas::class, 'peminjaman_id');
    }

    public function histories()
    {
        return $this->hasMany(PeminjamanHistory::class, 'peminjaman_id')->orderBy('created_at', 'asc');
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'menunggu' => '<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1 fw-bold"><i class="fas fa-clock me-1"></i>Menunggu Persetujuan</span>',
            'disetujui' => '<span class="badge bg-info-subtle text-info border border-info-subtle px-2.5 py-1 fw-bold"><i class="fas fa-calendar-check me-1"></i>Disetujui</span>',
            'ditolak' => '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 fw-bold"><i class="fas fa-circle-xmark me-1"></i>Ditolak</span>',
            'digunakan' => '<span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 fw-bold"><i class="fas fa-door-open me-1"></i>Sedang Digunakan</span>',
            'menunggu_pengembalian' => '<span class="badge bg-warning-subtle text-dark border border-warning px-2.5 py-1 fw-bold"><i class="fas fa-clipboard-check me-1"></i>Menunggu Pemeriksaan</span>',
            'selesai' => '<span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 fw-bold"><i class="fas fa-circle-check me-1"></i>Selesai</span>',
            'dibatalkan' => '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2.5 py-1 fw-bold"><i class="fas fa-ban me-1"></i>Dibatalkan</span>',
            default => '<span class="badge bg-light text-dark border px-2.5 py-1 fw-bold">' . ucfirst($this->status) . '</span>',
        };
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'menunggu' => 'Menunggu Persetujuan',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
            'digunakan' => 'Sedang Digunakan',
            'menunggu_pengembalian' => 'Menunggu Pemeriksaan Pengembalian',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }
}
