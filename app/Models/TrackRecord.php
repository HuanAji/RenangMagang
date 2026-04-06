<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'athlete_id',
        'nama_kompetisi',
        'nomor_lomba',
        'durasi_renang'
    ];

    public function athlete()
    {
        return $this->belongsTo(Athlete::class);
    }
}
