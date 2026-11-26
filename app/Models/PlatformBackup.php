<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatformBackup extends Model
{
    use HasFactory;

    protected $table = 'platform_backups';

    protected $fillable = [
        'filename',
        'filesize',
        'type',
        'status',
        'notes',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getChecksumAttribute(): ?string
    {
        if (preg_match('/SHA256:\s*([a-f0-9]{64})/i', $this->notes ?? '', $matches)) {
            return $matches[1];
        }
        return null;
    }
}
