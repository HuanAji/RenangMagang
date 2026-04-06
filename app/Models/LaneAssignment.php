<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaneAssignment extends Model
{
    protected $fillable = [
        'heat_id',
        'lane_number',
        'athlete_id',
        'registration_id',
        'result_time',
    ];

    public function heat()
    {
        return $this->belongsTo(Heat::class);
    }

    public function athlete()
    {
        return $this->belongsTo(Athlete::class);
    }

    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }
}
