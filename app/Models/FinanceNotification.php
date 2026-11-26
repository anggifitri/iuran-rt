<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceNotification extends Model
{
    use HasFactory;

    protected $table = 'finance_notifications';

    protected $fillable = [
        'user_id',
        'warga_id',
        'tipe',
        'judul',
        'pesan',
        'nominal',
        'periode',
        'due_date',
        'link_url',
        'is_read',
        'read_at',
        'data',
    ];

    protected $casts = [
        'nominal' => 'float',
        'due_date' => 'date',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }

    // Accessor aliases for flexible access in views
    public function getTypeAttribute()
    {
        return $this->tipe;
    }

    public function getTitleAttribute()
    {
        return $this->judul;
    }

    public function getMessageAttribute()
    {
        return $this->pesan;
    }

    /**
     * Tandai notifikasi sebagai telah dibaca
     */
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
        return $this;
    }

    /**
     * Helper to create a notification for a user or citizen
     */
    public static function send($userId, $tipe, $judul, $pesan, array $options = [])
    {
        return self::create([
            'user_id' => $userId,
            'warga_id' => $options['warga_id'] ?? null,
            'tipe' => $tipe,
            'judul' => $judul,
            'pesan' => $pesan,
            'nominal' => $options['nominal'] ?? null,
            'periode' => $options['periode'] ?? null,
            'due_date' => $options['due_date'] ?? null,
            'link_url' => $options['link_url'] ?? route('laporan.iuran'),
            'is_read' => false,
            'data' => $options['data'] ?? null,
        ]);
    }
}
