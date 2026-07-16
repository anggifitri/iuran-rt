<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratPengajuan extends Model
{
    use HasFactory;

    protected $table = 'surat_pengajuans';

    protected $fillable = [
        'user_id',
        'rt_number',
        'jenis_surat',
        'keperluan',
        'status',
        'tte_hash',
        'pdf_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
