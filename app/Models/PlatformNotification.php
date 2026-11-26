<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatformNotification extends Model
{
    use HasFactory;

    protected $table = 'platform_notifications';

    protected $fillable = [
        'title',
        'message',
        'type',
        'target_scope',
        'target_rw',
        'target_role',
        'is_banner',
        'starts_at',
        'expires_at',
        'created_by',
    ];

    protected $casts = [
        'is_banner' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
