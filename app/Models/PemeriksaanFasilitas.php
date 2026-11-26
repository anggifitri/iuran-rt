<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemeriksaanFasilitas extends Model
{
    use HasFactory;

    protected $table = 'pemeriksaan_fasilitas';

    protected $fillable = [
        'peminjaman_id',
        'fasilitas_id',
        'petugas_nama',
        'tanggal_cek_awal',
        'kondisi_sebelum',
        'catatan_sebelum',
        'foto_sebelum',
        'perlengkapan_keluar',
        'tanggal_cek_akhir',
        'kondisi_sesudah',
        'catatan_sesudah',
        'foto_sesudah',
        'perlengkapan_kembali',
        'ada_kerusakan',
        'tindak_lanjut_kerusakan',
    ];

    protected $casts = [
        'tanggal_cek_awal' => 'datetime',
        'tanggal_cek_akhir' => 'datetime',
        'ada_kerusakan' => 'boolean',
    ];

    public function peminjaman()
    {
        return $this->belongsTo(PeminjamanFasilitas::class, 'peminjaman_id');
    }

    public function fasilitas()
    {
        return $this->belongsTo(FasilitasUmum::class, 'fasilitas_id');
    }
}
