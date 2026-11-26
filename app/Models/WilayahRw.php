<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WilayahRw extends Model
{
    use HasFactory;

    protected $table = 'wilayah_rw';

    protected $fillable = [
        'rw_number',
        'tenant_id',
        'nama_wilayah',
        'kelurahan',
        'kecamatan',
        'kota',
        'provinsi',
        'kode_pos',
        'alamat_sekretariat',
        'ketua_rw_nama',
        'ketua_rw_phone',
        'email_resmi',
        'admin_user_id',
        'status',
        'total_rt',
        'catatan',
    ];

    public function rts()
    {
        return $this->hasMany(WilayahRt::class, 'wilayah_rw_id');
    }

    public function adminUser()
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'rw_number', 'rw_number');
    }

    public function wargas()
    {
        return $this->hasMany(Warga::class, 'rw_number', 'rw_number');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'active' => ['label' => 'Aktif', 'class' => 'bg-success-subtle text-success border border-success-subtle'],
            'suspended' => ['label' => 'Ditangguhkan', 'class' => 'bg-danger-subtle text-danger border border-danger-subtle'],
            'pending' => ['label' => 'Menunggu Verifikasi', 'class' => 'bg-warning-subtle text-warning border border-warning-subtle'],
            'inactive' => ['label' => 'Nonaktif', 'class' => 'bg-secondary-subtle text-secondary border border-secondary-subtle'],
            default => ['label' => ucfirst($this->status), 'class' => 'bg-light text-dark'],
        };
    }
}
