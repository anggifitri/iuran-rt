<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class IotSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'is_active',
        'api_token',
        'gate_name',
        'gate_latitude',
        'gate_longitude',
        'geofence_radius',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'geofence_radius' => 'integer',
        'gate_latitude' => 'float',
        'gate_longitude' => 'float',
    ];

    /**
     * Ambil atau inisialisasi baris pengaturan tunggal untuk platform.
     */
    public static function current(): self
    {
        $setting = static::first();
        if (!$setting) {
            $setting = static::create([
                'is_active' => true,
                'api_token' => Str::random(32),
                'gate_name' => 'Gerbang Utama RW',
                'gate_latitude' => -6.388889,
                'gate_longitude' => 106.845556,
                'geofence_radius' => 20,
            ]);
        }
        return $setting;
    }
}
