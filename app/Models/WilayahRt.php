<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WilayahRt extends Model
{
    use HasFactory;

    protected $table = 'wilayah_rt';

    protected $fillable = [
        'wilayah_rw_id',
        'rw_number',
        'rt_number',
        'nama_ketua_rt',
        'phone_ketua_rt',
        'admin_user_id',
        'status',
        'catatan',
    ];

    public function wilayahRw()
    {
        return $this->belongsTo(WilayahRw::class, 'wilayah_rw_id');
    }

    public function adminUser()
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    public function wargas()
    {
        return $this->hasMany(Warga::class, 'rt_number', 'rt_number')
                    ->where('rw_number', $this->rw_number);
    }

    public function getFormattedRtAttribute(): string
    {
        return 'RT ' . str_pad($this->rt_number, 3, '0', STR_PAD_LEFT);
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'active' => ['label' => 'Aktif', 'class' => 'bg-success-subtle text-success border border-success-subtle'],
            'inactive' => ['label' => 'Nonaktif', 'class' => 'bg-secondary-subtle text-secondary border border-secondary-subtle'],
            default => ['label' => ucfirst($this->status), 'class' => 'bg-light text-dark'],
        };
    }
}
