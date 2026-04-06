<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\Athlete;
use App\Services\HeatGeneratorService;

class RegistrationController extends Controller
{
    public function index()
    {
        $registrations = Registration::with('athlete', 'event')->get();
        return view('registrations.index', compact('registrations'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'athlete_id' => 'required|exists:athletes,id',
            'event_id' => 'required|array',
            'event_id.*' => 'exists:events,id',
        ]);

        try {
            $athlete = Athlete::findOrFail($validated['athlete_id']);
            $heatService = new HeatGeneratorService();
            $affectedEvents = [];

            foreach ($validated['event_id'] as $eventId) {
                // Check if already registered
                $exists = Registration::where('athlete_id', $validated['athlete_id'])
                                      ->where('event_id', $eventId)
                                      ->exists();
                if (!$exists) {
                    Registration::create([
                        'athlete_id' => $validated['athlete_id'],
                        'event_id' => $eventId,
                        'kategori_umur' => '',
                        'seed_time' => '',
                    ]);
                    $affectedEvents[] = $eventId;
                }
            }

            // Auto-generate heats for each affected event
            foreach ($affectedEvents as $eventId) {
                if ($athlete->jenis_kelamin) {
                    $heatService->generateForEvent($eventId, $athlete->jenis_kelamin);
                }
            }

            return response()->json(['message' => '✅ Pendaftaran berhasil!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => '❌ Gagal mendaftar: ' . $e->getMessage()], 500);
        }
    }
}

