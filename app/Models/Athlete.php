<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Athlete extends Model
{
    protected $table = 'athletes';
    protected $fillable = [
        'nama',
        'umur',
        'tanggal_lahir',
        'jenis_kelamin',
        'id_card_path',
        'kelengkapan_dokumen',
        'asal_club_sekolah',
        'provinsi',
        'kabupaten_kota',
        'surat_keterangan_path',
        'akta_kelahiran_path',
        'ktp_path'
    ];
    public $timestamps = true;

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function trackRecords()
    {
        return $this->hasMany(TrackRecord::class);
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'registrations', 'athlete_id', 'event_id');
    }

    public function laneAssignments()
    {
        return $this->hasMany(LaneAssignment::class);
    }
}
