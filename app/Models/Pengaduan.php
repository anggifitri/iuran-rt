<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengaduan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_pengaduan',
        'user_id',
        'title',
        'content',
        'status',
        'priority',
        'barcode_code',
        'aset_id',
        'category',
        'rt_number',
        'location_details',
        'latitude',
        'longitude',
        'foto_lampiran',
        'petugas_nama',
        'petugas_user_id',
        'internal_notes',
        'feedback_warga',
        'verified_at',
        'verified_by',
        'deadline_at',
        'completed_at',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'verified_at' => 'datetime',
        'deadline_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function aset()
    {
        return $this->belongsTo(Aset::class, 'aset_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_user_id');
    }

    public function histories()
    {
        return $this->hasMany(PengaduanHistory::class, 'pengaduan_id')->latest();
    }

    public function pemeriksaans()
    {
        return $this->hasMany(AsetPemeriksaan::class, 'pengaduan_id')->latest('tanggal');
    }

    public function dokumentasis()
    {
        return $this->hasMany(AsetDokumentasi::class, 'pengaduan_id')->latest();
    }

    public function getIsOverdueAttribute()
    {
        if (in_array(strtolower($this->status), ['selesai', 'ditolak'])) {
            return false;
        }
        if ($this->deadline_at) {
            return now()->greaterThan($this->deadline_at);
        }
        // Jika belum ada deadline khusus, pengaduan dianggap terlambat jika belum selesai > 3 hari
        return $this->created_at && now()->diffInDays($this->created_at) >= 3;
    }

    public function getStatusLabelAttribute()
    {
        return match(strtolower($this->status)) {
            'pending', 'menunggu' => 'Menunggu',
            'diverifikasi' => 'Diverifikasi',
            'ditugaskan' => 'Ditugaskan',
            'proses', 'sedang_diproses' => 'Sedang Diproses',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
            default => ucfirst($this->status),
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match(strtolower($this->status)) {
            'pending', 'menunggu' => ['bg' => '#FEF3C7', 'color' => '#92400E', 'icon' => 'fa-clock'],
            'diverifikasi' => ['bg' => 'var(--nx-powder)', 'color' => '#1F517F', 'icon' => 'fa-check'],
            'ditugaskan' => ['bg' => '#E0E7FF', 'color' => '#3730A3', 'icon' => 'fa-user-gear'],
            'proses', 'sedang_diproses' => ['bg' => '#DBEAFE', 'color' => '#1D4ED8', 'icon' => 'fa-spinner fa-spin'],
            'selesai' => ['bg' => 'var(--nx-mint)', 'color' => 'var(--nx-forest)', 'icon' => 'fa-check-circle'],
            'ditolak' => ['bg' => '#FEE2E2', 'color' => '#991B1B', 'icon' => 'fa-ban'],
            default => ['bg' => '#F1F5F9', 'color' => '#475569', 'icon' => 'fa-info-circle'],
        };
    }

    public function getPriorityBadgeAttribute()
    {
        return match(strtolower($this->priority ?? 'sedang')) {
            'darurat' => ['label' => 'Darurat / Emergency', 'bg' => '#FEE2E2', 'color' => '#B91C1C'],
            'tinggi' => ['label' => 'Tinggi', 'bg' => '#FFEDD5', 'color' => '#C2410C'],
            'sedang' => ['label' => 'Sedang', 'bg' => '#FEF3C7', 'color' => '#B45309'],
            'rendah' => ['label' => 'Rendah', 'bg' => '#F3F4F6', 'color' => '#4B5563'],
            default => ['label' => ucfirst($this->priority ?? 'sedang'), 'bg' => '#F1F5F9', 'color' => '#475569'],
        };
    }

    public function getGoogleMapsUrlAttribute()
    {
        if ($this->latitude && $this->longitude) {
            return "https://www.google.com/maps/search/?api=1&query={$this->latitude},{$this->longitude}";
        }
        if ($this->aset && $this->aset->latitude && $this->aset->longitude) {
            return $this->aset->google_maps_url;
        }
        if (!empty($this->location_details)) {
            $query = urlencode($this->location_details . ", RW 018");
            return "https://www.google.com/maps/search/?api=1&query={$query}";
        }
        return null;
    }
}
