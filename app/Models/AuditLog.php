<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'role',
        'event',
        'status',
        'action_type',
        'module',
        'record_id',
        'rt_number',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'device_info',
        'is_suspicious',
    ];

    protected $casts = [
        'is_suspicious' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Unique human-readable formatted ID, e.g. #AUD-00028
     */
    public function getFormattedIdAttribute(): string
    {
        return '#AUD-' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Badge visual configuration based on event type
     */
    public function getEventBadgeAttribute(): array
    {
        $ev = strtolower(trim($this->event ?? ''));
        return match($ev) {
            'create' => [
                'bg' => 'rgba(40, 89, 67, 0.12)',
                'color' => '#285943',
                'border' => '#A8C9B5',
                'icon' => 'fas fa-plus-circle',
                'label' => 'CREATE',
            ],
            'update' => [
                'bg' => '#E0F2FE',
                'color' => '#0369A1',
                'border' => '#BAE6FD',
                'icon' => 'fas fa-pen-to-square',
                'label' => 'UPDATE',
            ],
            'delete' => [
                'bg' => '#FEE2E2',
                'color' => '#B91C1C',
                'border' => '#FECACA',
                'icon' => 'fas fa-trash-can',
                'label' => 'DELETE',
            ],
            'login' => [
                'bg' => '#ECFDF5',
                'color' => '#047857',
                'border' => '#A7F3D0',
                'icon' => 'fas fa-right-to-bracket',
                'label' => 'LOGIN',
            ],
            'logout' => [
                'bg' => '#F1F5F9',
                'color' => '#475569',
                'border' => '#E2E8F0',
                'icon' => 'fas fa-right-from-bracket',
                'label' => 'LOGOUT',
            ],
            'approve', 'verify' => [
                'bg' => '#DCEBDD',
                'color' => '#285943',
                'border' => '#A8C9B5',
                'icon' => 'fas fa-circle-check',
                'label' => strtoupper($ev),
            ],
            'reject' => [
                'bg' => '#FFF1F2',
                'color' => '#BE123C',
                'border' => '#FECDD3',
                'icon' => 'fas fa-circle-xmark',
                'label' => 'REJECT',
            ],
            'revise' => [
                'bg' => '#FEF3C7',
                'color' => '#B45309',
                'border' => '#FDE68A',
                'icon' => 'fas fa-rotate-left',
                'label' => 'REVISE',
            ],
            'mutate' => [
                'bg' => '#F3E8FF',
                'color' => '#6B21A8',
                'border' => '#E9D5FF',
                'icon' => 'fas fa-people-arrows',
                'label' => 'MUTATE',
            ],
            'export' => [
                'bg' => '#FEF9C3',
                'color' => '#854D0E',
                'border' => '#FEF08A',
                'icon' => 'fas fa-file-export',
                'label' => 'EXPORT',
            ],
            'security_alert', 'unauthorized' => [
                'bg' => '#FFE4E6',
                'color' => '#9F1239',
                'border' => '#FDA4AF',
                'icon' => 'fas fa-shield-halved',
                'label' => 'SECURITY',
            ],
            default => [
                'bg' => '#F3F4F6',
                'color' => '#374151',
                'border' => '#E5E7EB',
                'icon' => 'fas fa-bolt',
                'label' => strtoupper($this->event ?: 'ACTION'),
            ]
        };
    }

    /**
     * Badge visual configuration based on status
     */
    public function getStatusBadgeAttribute(): array
    {
        $st = strtolower(trim($this->status ?? 'success'));
        return match($st) {
            'failed' => [
                'bg' => '#FEE2E2',
                'color' => '#DC2626',
                'icon' => 'fas fa-circle-xmark',
                'label' => 'GAGAL',
            ],
            'warning' => [
                'bg' => '#FEF3C7',
                'color' => '#D97706',
                'icon' => 'fas fa-triangle-exclamation',
                'label' => 'PERINGATAN',
            ],
            'suspicious' => [
                'bg' => '#FFE4E6',
                'color' => '#BE123C',
                'icon' => 'fas fa-shield-virus',
                'label' => 'MENCURIGAKAN',
            ],
            default => [
                'bg' => '#DCEBDD',
                'color' => '#285943',
                'icon' => 'fas fa-circle-check',
                'label' => 'BERHASIL',
            ],
        };
    }

    /**
     * Parse User-Agent into clean device label
     */
    public function getDeviceLabelAttribute(): string
    {
        if (!empty($this->device_info)) {
            return $this->device_info;
        }

        $ua = $this->user_agent ?: '';
        if (empty($ua)) return 'Perangkat Tidak Diketahui';

        // Detect OS
        $os = 'Perangkat Lain';
        if (stripos($ua, 'Windows NT 10') !== false) $os = 'Windows 10/11';
        elseif (stripos($ua, 'Windows') !== false) $os = 'Windows';
        elseif (stripos($ua, 'iPhone') !== false) $os = 'iPhone (iOS)';
        elseif (stripos($ua, 'iPad') !== false) $os = 'iPad (iPadOS)';
        elseif (stripos($ua, 'Android') !== false) $os = 'Android';
        elseif (stripos($ua, 'Macintosh') !== false) $os = 'macOS';
        elseif (stripos($ua, 'Linux') !== false) $os = 'Linux';

        // Detect Browser
        $browser = 'Browser';
        if (stripos($ua, 'Edg') !== false) $browser = 'Microsoft Edge';
        elseif (stripos($ua, 'Chrome') !== false && stripos($ua, 'Edg') === false) $browser = 'Google Chrome';
        elseif (stripos($ua, 'Safari') !== false && stripos($ua, 'Chrome') === false) $browser = 'Apple Safari';
        elseif (stripos($ua, 'Firefox') !== false) $browser = 'Mozilla Firefox';
        elseif (stripos($ua, 'Opera') !== false || stripos($ua, 'OPR') !== false) $browser = 'Opera';

        return "{$browser} ({$os})";
    }

    /**
     * Calculate modified keys between old and new values for Diff comparison
     */
    public function getDiffDetailsAttribute(): array
    {
        $old = json_decode($this->old_values ?? '{}', true) ?: [];
        $new = json_decode($this->new_values ?? '{}', true) ?: [];

        $allKeys = array_unique(array_merge(array_keys($old), array_keys($new)));
        $diffs = [];

        foreach ($allKeys as $k) {
            // Ignore technical timestamps or token fields
            if (in_array($k, ['updated_at', 'remember_token', 'password'])) continue;

            $oldVal = $old[$k] ?? null;
            $newVal = $new[$k] ?? null;

            $hasChanged = ($oldVal !== $newVal);

            $diffs[] = [
                'field' => ucwords(str_replace('_', ' ', $k)),
                'key' => $k,
                'old' => is_array($oldVal) ? json_encode($oldVal, JSON_UNESCAPED_UNICODE) : (string)($oldVal ?? '-'),
                'new' => is_array($newVal) ? json_encode($newVal, JSON_UNESCAPED_UNICODE) : (string)($newVal ?? '-'),
                'changed' => $hasChanged,
            ];
        }

        return $diffs;
    }
}
