<?php

namespace App\Services;

use App\Models\Athlete;
use App\Models\Event;
use App\Models\Heat;
use App\Models\LaneAssignment;
use App\Models\Registration;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HeatGeneratorService
{
    const LANES_PER_HEAT = 8;

    /**
     * Hitung Kelompok Umur (KU) berdasarkan umur atlet.
     * Menggunakan sistem PRSI.
     */
    private function hitungKU(?int $umur): string
    {
        if ($umur === null || $umur < 10) return 'Umum';
        if ($umur <= 12) return 'KU I';
        if ($umur <= 14) return 'KU II';
        if ($umur <= 17) return 'KU III';
        if ($umur <= 24) return 'KU IV';
        return 'Senior';
    }

    /**
     * Regenerate all heats for a specific event + jenis_kelamin.
     * Peserta dikelompokkan berdasarkan Kelompok Umur (KU) terlebih dahulu,
     * baru kemudian dibagi per jalur (maks 8 per heat).
     */
    public function generateForEvent(int $eventId, string $jenisKelamin): void
    {
        Event::findOrFail($eventId);

        // Ambil semua registrasi untuk event + gender ini
        $registrations = Registration::where('event_id', $eventId)
            ->whereHas('athlete', function ($q) use ($jenisKelamin) {
                $q->where('jenis_kelamin', $jenisKelamin);
            })
            ->with('athlete')
            ->get();

        if ($registrations->isEmpty()) {
            return;
        }

        // Hapus semua heat lama untuk event + gender ini (akan cascade ke lane_assignments)
        Heat::where('event_id', $eventId)
            ->where('jenis_kelamin', $jenisKelamin)
            ->delete();

        // Kelompokkan peserta berdasarkan KU
        $groups = [];
        foreach ($registrations as $reg) {
            $athlete = $reg->athlete;

            // Hitung umur dari kolom 'umur' atau dari 'tanggal_lahir'
            $umur = $athlete->umur
                ?? ($athlete->tanggal_lahir
                    ? Carbon::parse($athlete->tanggal_lahir)->age
                    : null);

            $ku = $this->hitungKU($umur);
            $groups[$ku][] = [
                'registration' => $reg,
                'athlete'      => $athlete,
            ];
        }

        // Urutkan grup sesuai hierarki KU PRSI
        $kuOrder = ['KU I', 'KU II', 'KU III', 'KU IV', 'Senior', 'Umum'];
        uksort($groups, fn($a, $b) => array_search($a, $kuOrder) - array_search($b, $kuOrder));

        DB::transaction(function () use ($groups, $eventId, $jenisKelamin) {
            foreach ($groups as $ku => $athletes) {
                // Urutkan alfabet dalam tiap KU
                usort($athletes, fn($a, $b) => strcmp($a['athlete']->nama, $b['athlete']->nama));

                // Bagi per 8 jalur
                $chunks = array_chunk($athletes, self::LANES_PER_HEAT);

                // Standar kompetisi: chunk tidak penuh dipindah ke DEPAN (Heat 1)
                if (count($chunks) > 1) {
                    $lastChunk = end($chunks);
                    if (count($lastChunk) < self::LANES_PER_HEAT) {
                        array_pop($chunks);
                        array_unshift($chunks, $lastChunk);
                    }
                }

                foreach ($chunks as $heatIndex => $athleteGroup) {
                    $heat = Heat::create([
                        'event_id'      => $eventId,
                        'heat_number'   => $heatIndex + 1,
                        'jenis_kelamin' => $jenisKelamin,
                        'kelompok_umur' => $ku,
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
            }
        });
    }

    /**
     * Regenerate heats for ALL events + genders that this athlete is registered in.
     * Dipanggil otomatis saat registrasi baru dibuat.
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
