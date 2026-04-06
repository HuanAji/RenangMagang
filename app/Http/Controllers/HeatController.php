<?php

namespace App\Http\Controllers;

use App\Models\Heat;
use App\Models\Event;
use App\Models\LaneAssignment;
use App\Models\HasilLomba;
use App\Services\HeatGeneratorService;
use Illuminate\Http\Request;

class HeatController extends Controller
{
    public function index(Request $request)
    {
        $events = Event::all();
        $selectedEventId = $request->get('event_id');
        $selectedGender = $request->get('jenis_kelamin');

        $heats = collect();

        if ($selectedEventId && $selectedGender) {
            $heats = Heat::with('laneAssignments.athlete', 'event')
                ->where('event_id', $selectedEventId)
                ->where('jenis_kelamin', $selectedGender)
                ->orderBy('heat_number')
                ->get();
        }

        return view('participant.competitions.heats', compact('events', 'heats', 'selectedEventId', 'selectedGender'));
    }

    /**
     * Operator / Wasit Dashboard page.
     */
    public function operatorDashboard()
    {
        $events = Event::all();
        return view('dashboard.operator', compact('events'));
    }

    /**
     * API: Get all heats for a given event + gender, with lane assignments.
     */
    public function getHeatsApi(Request $request)
    {
        $eventId = $request->get('event_id');
        $gender = $request->get('jenis_kelamin');

        if (!$eventId || !$gender) {
            return response()->json(['heats' => [], 'event_name' => '']);
        }

        $event = Event::find($eventId);
        $heats = Heat::with('laneAssignments.athlete.trackRecords')
            ->where('event_id', $eventId)
            ->where('jenis_kelamin', $gender)
            ->orderBy('heat_number')
            ->get()
            ->map(function ($heat) use ($event) {
                return [
                    'id' => $heat->id,
                    'heat_number' => $heat->heat_number,
                    'status' => $heat->status,
                    'lanes' => $heat->laneAssignments->map(function ($la) use ($event) {
                        $bestTr = $la->athlete->trackRecords
                            ->where('nomor_lomba', $event->nama_event)
                            ->sortBy('durasi_renang')
                            ->first();
                        return [
                            'lane_number' => $la->lane_number,
                            'athlete_name' => $la->athlete->nama ?? '-',
                            'club' => $la->athlete->asal_club_sekolah ?? '-',
                            'track_record' => $bestTr->durasi_renang ?? null,
                            'result_time' => $la->result_time,
                        ];
                    }),
                ];
            });

        return response()->json([
            'heats' => $heats,
            'event_name' => $event->nama_event ?? '',
        ]);
    }

    /**
     * API: Get the currently active heat's lane info (for real-time IoT display).
     */
    public function getActiveHeatApi()
    {
        $heat = Heat::with('laneAssignments.athlete', 'event')
            ->where('status', 'active')
            ->first();

        if (!$heat) {
            return response()->json(['active' => false, 'lanes' => [], 'info' => '']);
        }

        $lanes = $heat->laneAssignments->map(function ($la) {
            return [
                'lane_number' => $la->lane_number,
                'athlete_name' => $la->athlete->nama ?? '-',
                'club' => $la->athlete->asal_club_sekolah ?? '-',
                'result_time' => $la->result_time,
            ];
        });

        $genderLabel = $heat->jenis_kelamin === 'L' ? 'Putra' : 'Putri';

        return response()->json([
            'active' => true,
            'heat_id' => $heat->id,
            'heat_number' => $heat->heat_number,
            'event_name' => $heat->event->nama_event ?? '-',
            'gender' => $genderLabel,
            'info' => ($heat->event->nama_event ?? '') . ' — ' . $genderLabel . ' — Heat ' . $heat->heat_number,
            'lanes' => $lanes,
        ]);
    }

    /**
     * Set a heat as active (for IoT matching).
     */
    public function setActive(Heat $heat)
    {
        // Deactivate all other heats for same event + gender
        Heat::where('event_id', $heat->event_id)
            ->where('jenis_kelamin', $heat->jenis_kelamin)
            ->where('status', 'active')
            ->update(['status' => 'pending']);

        $heat->update(['status' => 'active']);

        return response()->json(['message' => '✅ Heat ' . $heat->heat_number . ' sekarang aktif!']);
    }

    /**
     * Mark heat as completed.
     */
    public function complete(Heat $heat)
    {
        $heat->update(['status' => 'completed']);
        return response()->json(['message' => '✅ Heat ' . $heat->heat_number . ' selesai!']);
    }

    /**
     * Manually regenerate heats for an event + gender.
     */
    public function regenerate(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'jenis_kelamin' => 'required|in:L,P',
        ]);

        $service = new HeatGeneratorService();
        $service->generateForEvent($validated['event_id'], $validated['jenis_kelamin']);

        return response()->json(['message' => '✅ Heat berhasil di-generate ulang!']);
    }
}
