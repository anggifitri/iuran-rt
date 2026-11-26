<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanLingkungan extends Model
{
    use HasFactory;

    protected $table = 'laporan_lingkungans';

    protected $fillable = [
        'rt_number',
        'user_id',
        'tanggal_pengiriman',
        'periode_bulan',
        'periode_tahun',

        // Kebersihan
        'kondisi_kebersihan',
        'area_terjaga_kebersihan',
        'area_perlu_perhatian_kebersihan',
        'masalah_sampah',
        'keterangan_kebersihan',

        // Drainase
        'kondisi_drainase',
        'kondisi_aliran_air',
        'titik_rawan_genangan',
        'saluran_butuh_pembersihan',
        'keterangan_drainase',

        // Penghijauan
        'program_penghijauan',
        'kondisi_penghijauan',
        'ketersediaan_ruang_terbuka',
        'kegiatan_perawatan_tanaman',
        'kendala_penghijauan',

        // Program & Kegiatan
        'kegiatan_kerja_bakti',
        'program_kebersihan',
        'program_lingkungan_lainnya',
        'tanggal_pelaksanaan_kegiatan',
        'jumlah_peserta_kegiatan',
        'agenda_id',

        // Kendala & Rencana
        'kendala_lingkungan',
        'lokasi_perhatian_khusus',
        'rencana_tindak_lanjut',
        'bantuan_rw_dibutuhkan',

        // Dokumentasi
        'foto_dokumentasi',
        'foto_sebelum',
        'foto_sesudah',
        'keterangan_foto',
        'dokumentasi_tambahan',

        // Pemeriksaan & Status
        'status', // draft, menunggu_pemeriksaan, diterima, perlu_revisi
        'catatan_rw',
        'pemeriksa_id',
        'tanggal_pemeriksaan',
        'catatan_evaluasi_rw',
        'catatan_revisi_rt',
        'versi',
        'is_terlambat',
    ];

    protected $casts = [
        'tanggal_pengiriman' => 'datetime',
        'tanggal_pemeriksaan' => 'datetime',
        'tanggal_pelaksanaan_kegiatan' => 'date',
        'dokumentasi_tambahan' => 'array',
        'is_terlambat' => 'boolean',
        'versi' => 'integer',
        'jumlah_peserta_kegiatan' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pemeriksa()
    {
        return $this->belongsTo(User::class, 'pemeriksa_id');
    }

    public function agenda()
    {
        return $this->belongsTo(AgendaRapat::class, 'agenda_id');
    }

    public function histories()
    {
        return $this->hasMany(LaporanLingkunganHistory::class, 'laporan_id')->orderBy('created_at', 'desc');
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isMenunggu(): bool
    {
        return in_array($this->status, ['menunggu_pemeriksaan', 'menunggu', 'tercatat']);
    }

    public function isDiterima(): bool
    {
        return $this->status === 'diterima';
    }

    public function isPerluRevisi(): bool
    {
        return $this->status === 'perlu_revisi';
    }

    public function canBeEditedBy(User $user): bool
    {
        if ($user->isAdminRw()) {
            return true;
        }

        if ($user->isAdminRt() && $user->rt_number === $this->rt_number) {
            return in_array($this->status, ['draft', 'perlu_revisi']);
        }

        return false;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Draft Disimpan',
            'menunggu_pemeriksaan', 'menunggu', 'tercatat' => 'Menunggu Pemeriksaan',
            'diterima' => 'Diterima RW',
            'perlu_revisi' => 'Perlu Revisi',
            default => ucfirst($this->status),
        };
    }

    public function getNamaBulanAttribute(): string
    {
        $bulanMap = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        return $bulanMap[str_pad($this->periode_bulan, 2, '0', STR_PAD_LEFT)] ?? $this->periode_bulan;
    }
}
