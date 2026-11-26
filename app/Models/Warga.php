<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Warga extends Model
{
    use HasFactory;

    // Explicitly define the table name as 'warga'
    protected $table = 'wargas';

    protected $fillable = [
        'user_id',
        'nama',
        'blok_rumah',
        'nomor_hp',
        'gender',
        'no_kk',
        'nik',
        'rfid_uid',
        'profile_photo',
        'tanggal_lahir',
        'tempat_lahir',
        'agama',
        'status_perkawinan',
        'pekerjaan',
        'rt_number',
        'rw_number',
        'is_kk',
        'kk_id',
        'alamat',
        'status_verifikasi',
        'jenis_pengajuan',
        'data_sebelumnya',
        'alasan_pengajuan',
        'pengaju_nama',
        'catatan_verifikasi',
        'dokumen_pendukung',
        'tanggal_verifikasi',
        'verifikator_id',
        'status_warga',
    ];

    protected $casts = [
        'data_sebelumnya' => 'array',
        'tanggal_lahir' => 'date',
        'tanggal_verifikasi' => 'datetime',
        'is_kk' => 'boolean',
    ];

    protected $appends = ['profile_photo_url', 'status_hubungan', 'umur_formatted'];

    public function getStatusHubunganAttribute()
    {
        if ($this->is_kk) {
            return 'Kepala Keluarga';
        }
        if ($this->gender === 'P' && $this->umur >= 18) {
            return 'Istri';
        }
        return 'Anak';
    }

    public function getUmurFormattedAttribute()
    {
        if (!$this->tanggal_lahir) return '-';
        $birthdate = Carbon::parse($this->tanggal_lahir);
        $now = Carbon::now();
        
        $years = (int)$birthdate->diffInYears($now);
        $months = (int)($birthdate->diffInMonths($now) % 12);
        
        if ($years == 0) {
            return $months . ' Bln';
        }
        
        if ($months == 0) {
            return $years . ' Thn';
        }
        
        return $years . ' Thn ' . $months . ' Bln';
    }

    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo) {
            if (filter_var($this->profile_photo, FILTER_VALIDATE_URL)) {
                return $this->profile_photo;
            }
            return asset('storage/' . $this->profile_photo);
        }

        return User::generateInitialsAvatar($this->nama ?? 'Warga', 'warga', $this->jenis_kelamin ?? null);
    }

    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class, 'warga_id');
    }

    public function anggotaKeluarga()
    {
        return $this->hasMany(Warga::class, 'kk_id', 'id');
    }

    public function kepalaKeluarga()
    {
        return $this->belongsTo(Warga::class, 'kk_id', 'id');
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'verifikator_id');
    }

    public function mutasis()
    {
        return $this->hasMany(MutasiWarga::class, 'warga_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getSemuaAnggotaKeluargaAttribute()
    {
        if (!empty($this->no_kk)) {
            return Warga::where('no_kk', $this->no_kk)->orderBy('is_kk', 'desc')->orderBy('tanggal_lahir', 'asc')->get();
        }
        if ($this->is_kk) {
            return Warga::where('id', $this->id)->orWhere('kk_id', $this->id)->orderBy('is_kk', 'desc')->get();
        }
        if ($this->kk_id) {
            return Warga::where('id', $this->kk_id)->orWhere('kk_id', $this->kk_id)->orderBy('is_kk', 'desc')->get();
        }
        return collect([$this]);
    }

    public function getUmurAttribute()
    {
        if (!$this->tanggal_lahir) return 0;
        return Carbon::parse($this->tanggal_lahir)->age;
    }
}
