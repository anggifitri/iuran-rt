<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RfidLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'uid',
        'warga_id',
        'status',
        'context',
        'method',
        'device_ip',
        'message',
        'raw_payload',
    ];

    protected $casts = [
        'raw_payload' => 'array',
    ];

    public function warga(): BelongsTo
    {
        return $this->belongsTo(\App\Models\warga::class, 'warga_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'registered' => 'Terdaftar',
            'unregistered' => 'Belum Terdaftar',
            'rejected_token' => 'Token Ditolak',
            'rejected_geofence' => 'Radius GPS Ditolak',
            'api_disabled' => 'API Nonaktif',
            default => ucfirst((string) $this->status),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'registered' => 'bg-success-subtle text-success border border-success-subtle',
            'unregistered' => 'bg-warning-subtle text-warning border border-warning-subtle',
            'rejected_token' => 'bg-danger-subtle text-danger border border-danger-subtle',
            'rejected_geofence' => 'bg-danger-subtle text-danger border border-danger-subtle',
            'api_disabled' => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
            default => 'bg-light text-dark border',
        };
    }

    public function getContextLabelAttribute(): string
    {
        return match($this->context) {
            'rekam_ktp' => 'Rekam E-KTP',
            'iuran' => 'Setoran Iuran',
            'smart_gate' => 'Smart Gate',
            'general' => 'Smart Gate',
            default => strtoupper((string) $this->context),
        };
    }

    public function getContextBadgeClassAttribute(): string
    {
        return match($this->context) {
            'rekam_ktp' => 'bg-info-subtle text-info border border-info-subtle',
            'iuran' => 'bg-success-subtle text-success border border-success-subtle',
            'smart_gate', 'general' => 'bg-primary-subtle text-primary border border-primary-subtle',
            default => 'bg-light text-dark border',
        };
    }
}
