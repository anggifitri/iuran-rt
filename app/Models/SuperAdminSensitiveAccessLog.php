<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuperAdminSensitiveAccessLog extends Model
{
    use HasFactory;

    protected $table = 'super_admin_sensitive_access_logs';

    protected $fillable = [
        'user_id',
        'target_user_id',
        'target_type',
        'target_identifier',
        'reason',
        'ip_address',
        'user_agent',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }
}
