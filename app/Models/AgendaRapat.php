<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgendaRapat extends Model
{
    use HasFactory;

    protected $table = 'agenda_rapats';

    protected $fillable = [
        'judul',
        'jenis_rapat', // 'rw', 'koordinasi_rt', 'musyawarah_warga'
        'deskripsi',
        'tanggal_waktu',
        'waktu_selesai',
        'lokasi',
        'penyelenggara',
        'target_peserta',
        'rt_number',
        'agenda_pembahasan',
        'notulen',
        'hasil_keputusan',
        'tindak_lanjut',
        'pic_tindak_lanjut',
        'tenggat_tindak_lanjut',
        'lampiran_dokumen',
        'lampiran_notulen',
        'status', // 'akan_datang', 'berlangsung', 'selesai', 'dibatalkan'
        'is_published_notulen',
        'published_at',
        'created_by',
    ];

    protected $casts = [
        'tanggal_waktu' => 'datetime',
        'tenggat_tindak_lanjut' => 'date',
        'published_at' => 'datetime',
        'is_published_notulen' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function kehadirans()
    {
        return $this->hasMany(AgendaKehadiran::class, 'agenda_id');
    }

    public function notulenHistories()
    {
        return $this->hasMany(AgendaNotulenHistory::class, 'agenda_id')->orderBy('created_at', 'desc');
    }

    // Accessors
    public function getJenisLabelAttribute()
    {
        return match ($this->jenis_rapat) {
            'rw' => 'Rapat Internal RW',
            'koordinasi_rt' => 'Koordinasi RT',
            'musyawarah_warga' => 'Musyawarah Warga',
            default => 'Agenda RW',
        };
    }

    public function getJenisBadgeAttribute()
    {
        return match ($this->jenis_rapat) {
            'rw' => '<span class="badge rounded-pill px-2.5 py-1" style="background: rgba(40,89,67,0.12); color: var(--nx-forest); font-weight: 700;"><i class="fas fa-building me-1"></i>Internal RW</span>',
            'koordinasi_rt' => '<span class="badge rounded-pill px-2.5 py-1" style="background: var(--nx-powder); color: #1e4b6d; font-weight: 700;"><i class="fas fa-users-gear me-1"></i>Koordinasi RT</span>',
            'musyawarah_warga' => '<span class="badge rounded-pill px-2.5 py-1" style="background: var(--nx-mint-soft); color: var(--nx-forest); font-weight: 700;"><i class="fas fa-people-roof me-1"></i>Musyawarah Warga</span>',
            default => '<span class="badge rounded-pill px-2.5 py-1 bg-light text-dark font-weight-bold">Umum</span>',
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'selesai' => '<span class="badge rounded-pill px-2.5 py-1" style="background: var(--nx-mint); color: var(--nx-forest); font-weight: 700;"><i class="fas fa-circle-check me-1"></i>Selesai</span>',
            'berlangsung' => '<span class="badge rounded-pill px-2.5 py-1" style="background: var(--nx-powder); color: #1e4b6d; font-weight: 700;"><i class="fas fa-satellite-dish me-1"></i>Sedang Berlangsung</span>',
            'dibatalkan' => '<span class="badge rounded-pill px-2.5 py-1" style="background: var(--nx-danger-soft); color: var(--nx-danger); font-weight: 700;"><i class="fas fa-ban me-1"></i>Dibatalkan</span>',
            default => '<span class="badge rounded-pill px-2.5 py-1" style="background: var(--nx-warning-soft); color: var(--nx-warning); font-weight: 700;"><i class="fas fa-clock me-1"></i>Akan Datang</span>',
        };
    }

    public function getWaktuFormattedAttribute()
    {
        if (!$this->tanggal_waktu) return '-';
        $start = Carbon::parse($this->tanggal_waktu);
        $dateStr = $start->translatedFormat('l, d F Y');
        $timeStart = $start->format('H:i');
        
        if ($this->waktu_selesai) {
            $timeEnd = Carbon::parse($this->waktu_selesai)->format('H:i');
            return "{$dateStr} · {$timeStart} - {$timeEnd} WIB";
        }

        return "{$dateStr} · {$timeStart} WIB";
    }

    public function getKehadiranSummaryAttribute()
    {
        $hadir = $this->kehadirans->where('status', 'hadir')->count();
        $izin = $this->kehadirans->where('status', 'izin')->count();
        $tidakHadir = $this->kehadirans->where('status', 'tidak_hadir')->count();
        $total = $this->kehadirans->count();

        return (object)[
            'hadir' => $hadir,
            'izin' => $izin,
            'tidak_hadir' => $tidakHadir,
            'total' => $total,
            'persentase' => $total > 0 ? round(($hadir / $total) * 100, 1) : 0,
        ];
    }
}
