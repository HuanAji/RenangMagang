<?php

namespace App\Services;

use App\Models\Athlete;
use App\Models\Event;
use App\Models\Heat;
use App\Models\LaneAssignment;
use App\Models\Registration;
use App\Models\TrackRecord;
use Illuminate\Support\Facades\DB;

class HeatGeneratorService
{
    const LANES_PER_HEAT = 8;

    /**
     * Regenerate all heats for a specific event + jenis_kelamin.
     * Called automatically when a new registration is created.
     */
    public function generateForEvent(int $eventId, string $jenisKelamin): void
    {
        $event = Event::findOrFail($eventId);

        // Get all registrations for this event + gender
        $registrations = Registration::where('event_id', $eventId)
            ->whereHas('athlete', function ($q) use ($jenisKelamin) {
                $q->where('jenis_kelamin', $jenisKelamin);
            })
            ->with('athlete')
            ->get();

        if ($registrations->isEmpty()) {
            return;
        }

        $sortedAthletes = [];
        foreach ($registrations as $reg) {
            $sortedAthletes[] = [
                'registration' => $reg,
                'athlete' => $reg->athlete,
            ];
        }

        // Sort alphabetically by athlete name
        usort($sortedAthletes, fn($a, $b) => strcmp($a['athlete']->nama, $b['athlete']->nama));

        // Delete existing heats for this event + gender (will cascade delete lane_assignments)
        Heat::where('event_id', $eventId)
            ->where('jenis_kelamin', $jenisKelamin)
            ->delete();

        // Chunk into heats of 8
        $chunks = array_chunk($sortedAthletes, self::LANES_PER_HEAT);

        // Standar kompetisi renang: heat yang tidak penuh (sisa) ditempatkan di AWAL
        // sehingga heat 1 = heat dengan peserta sedikit, heat terakhir = heat terkuat/penuh
        if (count($chunks) > 1) {
            $lastChunk = end($chunks);
            // Jika chunk terakhir tidak penuh (< 8), pindahkan ke depan
            if (count($lastChunk) < self::LANES_PER_HEAT) {
                array_pop($chunks);         // Hapus dari belakang
                array_unshift($chunks, $lastChunk); // Taruh di depan
            }
        }

        DB::transaction(function () use ($chunks, $eventId, $jenisKelamin) {
            foreach ($chunks as $heatIndex => $athleteGroup) {
                $heat = Heat::create([
                    'event_id'      => $eventId,
                    'heat_number'   => $heatIndex + 1,
                    'jenis_kelamin' => $jenisKelamin,
                    'status'        => 'pending',
                ]);

                foreach ($athleteGroup as $laneIndex => $entry) {
                    LaneAssignment::create([
                        'heat_id'         => $heat->id,
                        'lane_number'     => $laneIndex + 1,
                        'athlete_id'      => $entry['athlete']->id,
                        'registration_id' => $entry['registration']->id,
                    ]);
                }
            }
        });
    }

    /**
     * Regenerate heats for ALL events + genders that this athlete is registered in.
     * Called after a new registration.
     */
    public function regenerateForRegistration(Registration $registration): void
    {
        $athlete = $registration->athlete;
        if (!$athlete || !$athlete->jenis_kelamin) {
            return;
        }

        $this->generateForEvent($registration->event_id, $athlete->jenis_kelamin);
    }


}
