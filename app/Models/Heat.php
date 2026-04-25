<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Heat extends Model
{
    protected $fillable = [
        'event_id',
        'heat_number',
        'jenis_kelamin',
        'kelompok_umur',
        'status',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function laneAssignments()
    {
        return $this->hasMany(LaneAssignment::class)->orderBy('lane_number');
    }
}
