<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    use HasFactory;

    protected $table = 'pengumumans';

    protected $fillable = [
        'title',
        'content',
        'kategori',
        'prioritas',
        'target_rt',
        'created_by',
        'is_pinned',
        'requires_rsvp',
        'rsvp_type',
        'lampiran_file',
        'status',
        'is_archived',
        'archived_at',
        'published_at',
        'tanggal_berakhir',
        'is_kegiatan',
        'kegiatan_tanggal',
        'kegiatan_waktu_mulai',
        'kegiatan_waktu_selesai',
        'kegiatan_lokasi',
        'agenda_rapat_id',
    ];

    protected $casts = [
        'published_at'      => 'datetime',
        'tanggal_berakhir'  => 'date',
        'is_pinned'         => 'boolean',
        'requires_rsvp'     => 'boolean',
        'is_archived'       => 'boolean',
        'archived_at'       => 'datetime',
        'is_kegiatan'       => 'boolean',
        'kegiatan_tanggal'  => 'date',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reads()
    {
        return $this->hasMany(PengumumanRead::class, 'pengumuman_id');
    }

    public function rsvps()
    {
        return $this->hasMany(PengumumanRsvp::class, 'pengumuman_id');
    }

    public function agendaRapat()
    {
        return $this->belongsTo(AgendaRapat::class, 'agenda_rapat_id');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_archived', false)
            ->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('tanggal_berakhir')->orWhere('tanggal_berakhir', '>=', now()->toDateString());
            });
    }

    public function scopePinned(Builder $query): Builder
    {
        return $query->where('is_pinned', true);
    }

    public function scopeDaruratPenting(Builder $query): Builder
    {
        return $query->whereIn('prioritas', ['darurat', 'penting']);
    }

    public function scopeForUser(Builder $query, ?User $user): Builder
    {
        if (!$user) {
            return $query->active()->where('target_rt', 'all');
        }

        if ($user->isAdminRw()) {
            return $query; // Admin RW can see all records
        }

        if ($user->isAdminRt()) {
            $rt = $user->rt_number;
            return $query->where(function ($q) use ($rt, $user) {
                $q->where('target_rt', 'all')
                  ->orWhere('target_rt', $rt)
                  ->orWhere('created_by', $user->id);
            });
        }

        // Warga: only published, not archived, matching target RT or 'all'
        $rt = $user->rt_number;
        return $query->active()
            ->where(function ($q) use ($rt) {
                $q->where('target_rt', 'all')->orWhere('target_rt', $rt);
            });
    }

    // ==========================================
    // ACCESSORS & BADGES
    // ==========================================

    public function getKategoriLabelAttribute(): string
    {
        return match($this->kategori) {
            'kegiatan' => 'Kegiatan Lingkungan',
            'keuangan' => 'Keuangan & Kas',
            'keamanan' => 'Keamanan & Ronda',
            'kesehatan' => 'Kesehatan & Posyandu',
            'sosial' => 'Sosial & Warga',
            'darurat', 'informasi_darurat' => 'Darurat & Mendesak',
            default => 'Informasi Umum',
        };
    }

    public function getKategoriBadgeAttribute(): array
    {
        return match($this->kategori) {
            'kegiatan' => [
                'bg'    => '#DCEBDD',
                'color' => '#285943',
                'icon'  => 'fa-calendar-check',
            ],
            'keuangan' => [
                'bg'    => '#FEF3C7',
                'color' => '#92400E',
                'icon'  => 'fa-wallet',
            ],
            'keamanan' => [
                'bg'    => '#DCEAF5',
                'color' => '#1E4B6D',
                'icon'  => 'fa-shield-halved',
            ],
            'kesehatan' => [
                'bg'    => '#E0F2FE',
                'color' => '#0369A1',
                'icon'  => 'fa-heart-pulse',
            ],
            'sosial' => [
                'bg'    => '#F3E8FF',
                'color' => '#6B21A8',
                'icon'  => 'fa-people-roof',
            ],
            'darurat', 'informasi_darurat' => [
                'bg'    => '#FCE8E9',
                'color' => '#C53030',
                'icon'  => 'fa-triangle-exclamation',
            ],
            default => [
                'bg'    => '#EDF2F7',
                'color' => '#2D3748',
                'icon'  => 'fa-bullhorn',
            ],
        };
    }

    public function getPrioritasLabelAttribute(): string
    {
        return match($this->prioritas) {
            'darurat' => 'Darurat',
            'penting' => 'Penting',
            'kegiatan' => 'Kegiatan',
            default => 'Informasi',
        };
    }

    public function getPrioritasBadgeAttribute(): array
    {
        return match($this->prioritas) {
            'darurat' => [
                'bg'     => '#FCE8E9',
                'border' => '#FEB2B2',
                'color'  => '#9B2C2C',
                'icon'   => 'fa-fire-flame-curved',
                'tag'    => 'Prioritas Tertinggi',
            ],
            'penting' => [
                'bg'     => '#FEF3C7',
                'border' => '#FDE68A',
                'color'  => '#B45309',
                'icon'   => 'fa-bell',
                'tag'    => 'Penting',
            ],
            'kegiatan' => [
                'bg'     => '#DCEBDD',
                'border' => '#A8C9B5',
                'color'  => '#285943',
                'icon'   => 'fa-calendar-days',
                'tag'    => 'Agenda Warga',
            ],
            default => [
                'bg'     => '#EBF8FF',
                'border' => '#BEE3F8',
                'color'  => '#2B6CB0',
                'icon'   => 'fa-circle-info',
                'tag'    => 'Info Umum',
            ],
        };
    }

    public function getTargetTeksAttribute(): string
    {
        if ($this->target_rt === 'all' || empty($this->target_rt)) {
            return 'Seluruh Warga RW 018';
        }
        return 'Khusus Warga RT ' . str_pad($this->target_rt, 3, '0', STR_PAD_LEFT);
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->is_archived) {
            return 'Diarsipkan';
        }
        if ($this->status === 'draft') {
            return 'Draft';
        }
        if ($this->published_at && $this->published_at->isFuture()) {
            return 'Terjadwal';
        }
        if ($this->tanggal_berakhir && $this->tanggal_berakhir->isPast()) {
            return 'Kedaluwarsa';
        }
        return 'Aktif & Dipublikasikan';
    }

    public function getStatusBadgeAttribute(): array
    {
        if ($this->is_archived) {
            return ['bg' => '#EDF2F7', 'color' => '#718096', 'icon' => 'fa-box-archive', 'label' => 'Diarsipkan'];
        }
        if ($this->status === 'draft') {
            return ['bg' => '#FEF3C7', 'color' => '#B45309', 'icon' => 'fa-file-lines', 'label' => 'Draft'];
        }
        if ($this->published_at && $this->published_at->isFuture()) {
            return ['bg' => '#EBF8FF', 'color' => '#2B6CB0', 'icon' => 'fa-clock', 'label' => 'Terjadwal'];
        }
        if ($this->tanggal_berakhir && $this->tanggal_berakhir->isPast()) {
            return ['bg' => '#FEE2E2', 'color' => '#991B1B', 'icon' => 'fa-hourglass-end', 'label' => 'Kedaluwarsa'];
        }
        return ['bg' => '#DCEBDD', 'color' => '#285943', 'icon' => 'fa-circle-check', 'label' => 'Aktif'];
    }

    /**
     * Check if a specific user has read this announcement.
     */
    public function isReadByUser(?int $userId): bool
    {
        if (!$userId) return false;
        return $this->reads()->where('user_id', $userId)->exists();
    }

    /**
     * Calculate comprehensive read tracking stats for officers.
     */
    public function getStatsKeterbacaanAttribute(): array
    {
        // Target users query
        $userQuery = User::where('role', 'warga');
        if ($this->target_rt && $this->target_rt !== 'all') {
            $userQuery->where('rt_number', $this->target_rt);
        }

        $targetUsers = $userQuery->get();
        $totalTarget = $targetUsers->count();

        $readUserIds = $this->reads()->pluck('user_id')->toArray();
        $readCount = 0;
        $unreadCount = 0;

        $rtBreakdown = [];
        $rtList = ['006', '007', '008', '009', '010'];
        foreach ($rtList as $rt) {
            $rtBreakdown[$rt] = [
                'total'  => 0,
                'read'   => 0,
                'unread' => 0,
                'pct'    => 0,
            ];
        }

        foreach ($targetUsers as $u) {
            $uRt = str_pad($u->rt_number ?? '006', 3, '0', STR_PAD_LEFT);
            if (!isset($rtBreakdown[$uRt])) {
                $rtBreakdown[$uRt] = ['total' => 0, 'read' => 0, 'unread' => 0, 'pct' => 0];
            }
            $rtBreakdown[$uRt]['total']++;

            if (in_array($u->id, $readUserIds)) {
                $readCount++;
                $rtBreakdown[$uRt]['read']++;
            } else {
                $unreadCount++;
                $rtBreakdown[$uRt]['unread']++;
            }
        }

        foreach ($rtBreakdown as $rt => $data) {
            $rtBreakdown[$rt]['pct'] = $data['total'] > 0 ? round(($data['read'] / $data['total']) * 100, 1) : 0;
        }

        $percentage = $totalTarget > 0 ? round(($readCount / $totalTarget) * 100, 1) : 0;

        return [
            'total_target' => $totalTarget,
            'total_read'   => $readCount,
            'total_unread' => $unreadCount,
            'percentage'   => $percentage,
            'rt_breakdown' => $rtBreakdown,
        ];
    }

    /**
     * Calculate RSVP response summary.
     */
    public function getStatsRsvpAttribute(): array
    {
        $all = $this->rsvps;
        $total = $all->count();
        $hadir = $all->whereIn('response', ['hadir', 'ikut'])->count();
        $tidakHadir = $all->whereIn('response', ['tidak_hadir', 'tidak_ikut'])->count();
        $konfirmasi = $all->where('response', 'konfirmasi')->count();

        return [
            'total'       => $total,
            'hadir'       => $hadir,
            'tidak_hadir' => $tidakHadir,
            'konfirmasi'  => $konfirmasi,
        ];
    }
}
