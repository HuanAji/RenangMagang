<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilLomba extends Model
{
    protected $table = 'hasil_lomba';
    protected $fillable = [
        'player',
        'waktu_ms',
        'waktu_detik',
        'waktu_menit',
        'waktu_format',
        'timestamp'
    ];
    public $timestamps = false;
    protected $attributes = [
        'waktu_ms' => 0,
        'waktu_detik' => 0,
        'waktu_menit' => 0,
    ];

    protected $casts = [
        'timestamp' => 'datetime',
    ];
}
