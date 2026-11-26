<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgendaNotulenHistory extends Model
{
    use HasFactory;

    protected $table = 'agenda_notulen_histories';

    protected $fillable = [
        'agenda_id',
        'user_id',
        'action_type', // 'draft', 'publish', 'update'
        'ringkasan',
        'hasil_keputusan',
        'catatan_perubahan',
    ];

    public function agenda()
    {
        return $this->belongsTo(AgendaRapat::class, 'agenda_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
