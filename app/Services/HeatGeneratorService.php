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
            ->with('athlete.trackRecords')
            ->get();

        if ($registrations->isEmpty()) {
            return;
        }

        // Split athletes into two groups:
        // Group A: have track record for this event's nomor_lomba → sorted fastest first
        // Group B: no track record → alphabetical order
        $withRecord = [];
        $withoutRecord = [];

        foreach ($registrations as $reg) {
            $athlete = $reg->athlete;
            // Find best track record matching this event name
            $bestRecord = $this->getBestTrackRecord($athlete, $event->nama_event);

            if ($bestRecord !== null) {
                $withRecord[] = [
                    'registration' => $reg,
                    'athlete' => $athlete,
                    'time_ms' => $bestRecord,
                ];
            } else {
                $withoutRecord[] = [
                    'registration' => $reg,
                    'athlete' => $athlete,
                    'time_ms' => PHP_INT_MAX,
                ];
            }
        }

        // Sort Group A by time (fastest first)
        usort($withRecord, fn($a, $b) => $a['time_ms'] <=> $b['time_ms']);

        // Sort Group B alphabetically
        usort($withoutRecord, fn($a, $b) => strcmp($a['athlete']->nama, $b['athlete']->nama));

        // Merge: athletes with records first, then without
        $sortedAthletes = array_merge($withRecord, $withoutRecord);

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

    /**
     * Parse a durasi_renang string ("00:14.048") to total milliseconds.
     * Returns null if unparseable.
     */
    private function parseTimeToMs(string $timeStr): ?int
    {
        $timeStr = trim($timeStr);
        // Format: "MM:SS.mmm" or "MM:SS" or "SS.mmm"
        if (preg_match('/^(\d+):(\d+)\.(\d+)$/', $timeStr, $m)) {
            $menit = intval($m[1]);
            $detik = intval($m[2]);
            $ms = intval(str_pad($m[3], 3, '0', STR_PAD_RIGHT)); // ensure 3 digits
            return ($menit * 60000) + ($detik * 1000) + $ms;
        }
        if (preg_match('/^(\d+):(\d+)$/', $timeStr, $m)) {
            $menit = intval($m[1]);
            $detik = intval($m[2]);
            return ($menit * 60000) + ($detik * 1000);
        }
        if (preg_match('/^(\d+)\.(\d+)$/', $timeStr, $m)) {
            $detik = intval($m[1]);
            $ms = intval(str_pad($m[2], 3, '0', STR_PAD_RIGHT));
            return ($detik * 1000) + $ms;
        }
        return null;
    }

    /**
     * Get the best (fastest) track record time in ms for an athlete
     * matching a specific event name (nomor_lomba).
     * Returns null if no matching record found.
     */
    private function getBestTrackRecord(Athlete $athlete, string $eventName): ?int
    {
        $records = $athlete->trackRecords
            ->where('nomor_lomba', $eventName);

        if ($records->isEmpty()) {
            return null;
        }

        $bestMs = null;
        foreach ($records as $record) {
            $ms = $this->parseTimeToMs($record->durasi_renang);
            if ($ms !== null && ($bestMs === null || $ms < $bestMs)) {
                $bestMs = $ms;
            }
        }

        return $bestMs;
    }
}
