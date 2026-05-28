<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Iuran extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'amount',
        'due_date',
        'type',
        'is_active',
    ];

    protected $casts = [
        'due_date' => 'date',
        'is_active' => 'boolean',
    ];
}
