<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'events';
    protected $fillable = ['nama_event'];
    public $timestamps = true;

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function heats()
    {
        return $this->hasMany(Heat::class);
    }
}
