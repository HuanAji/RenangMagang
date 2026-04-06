<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    protected $table = 'registrations';
    protected $fillable = [
        'athlete_id',
        'event_id',
        'kategori_umur',
        'seed_time'
    ];
    public $timestamps = true;

    public function athlete()
    {
        return $this->belongsTo(Athlete::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
