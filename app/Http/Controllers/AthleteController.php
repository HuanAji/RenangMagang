<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;

class AthleteController extends Controller
{
    public function index()
    {
        $athletes = Athlete::with(['registrations.event'])->orderBy('created_at', 'desc')->get();
        $events = Event::all();
        return view('athletes.index', compact('athletes', 'events'));
    }

    public function show(Athlete $athlete)
    {
        return response()->json($athlete->load('registrations.event'));
    }

    public function edit(Athlete $athlete)
    {
        return view('participant.athletes.edit', compact('athlete'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'required|in:L,P',
            'asal_club_sekolah' => 'required|string',
            'event_id' => 'nullable|array',
            'event_id.*' => 'integer|exists:events,id',
        ]);

        try {
            $athlete = Athlete::create([
                'nama' => $validated['nama'],
                'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'asal_club_sekolah' => $validated['asal_club_sekolah'],
            ]);

            if ($request->has('event_id') && is_array($validated['event_id'])) {
                foreach ($validated['event_id'] as $eventId) {
                    $kategori = $request->input('kategori_' . $eventId, '');
                    $seedTime = $request->input('seed_' . $eventId, '');

                    Registration::create([
                        'athlete_id' => $athlete->id,
                        'event_id' => $eventId,
                        'kategori_umur' => $kategori,
                        'seed_time' => $seedTime,
                    ]);
                }
            }

            return response()->json(['message' => '✅ Data atlet berhasil ditambahkan!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => '❌ Gagal menambahkan atlet: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(Athlete $athlete)
    {
        try {
            $athlete->registrations()->delete();
            $athlete->delete();
            return response()->json(['message' => '✅ Atlet berhasil dihapus!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => '❌ Gagal menghapus atlet: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, Athlete $athlete)
    {
        $validated = $request->validate([
            'nama'             => 'required|string',
            'jenis_kelamin'    => 'required|in:L,P',
            'asal_club_sekolah' => 'nullable|string',
        ]);

        try {
            $athlete->update($validated);
            return response()->json(['message' => '✅ Data atlet berhasil diperbarui!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => '❌ Gagal memperbarui atlet: ' . $e->getMessage()], 500);
        }
    }

    public function getTable()
    {
        $athletes = Athlete::with('registrations.event')->orderBy('created_at', 'desc')->get();
        $html = '';

        foreach ($athletes as $no => $athlete) {
            $events = $athlete->registrations->map(function($reg) {
                return $reg->event->nama_event . ' (' . $reg->kategori_umur . ' / ' . $reg->seed_time . ')';
            })->implode('; ');

            $html .= '<tr>';
            $html .= '<td style="text-align: center;">' . ($no + 1) . '</td>';
            $html .= '<td>' . htmlspecialchars($athlete->nama) . '</td>';
            $html .= '<td style="text-align: center;">' . ($athlete->tanggal_lahir ?? '-') . '</td>';
            $html .= '<td style="text-align: center;">' . ($athlete->jenis_kelamin ?? '-') . '</td>';
            $html .= '<td>' . htmlspecialchars($athlete->asal_club_sekolah ?? '-') . '</td>';
            $html .= '<td>' . $events . '</td>';
            $html .= '<td style="text-align: center;"><span class="material-icons" style="font-size: 16px; vertical-align: text-bottom; margin-right: 4px;">check_circle</span>' . $athlete->created_at . '</td>';
            $html .= '</tr>';
        }

        return response()->json(['html' => $html]);
    }

    public function getByEvent(Request $request)
    {
        $eventId = $request->get('event_id');
        $gender = $request->get('jenis_kelamin');

        if (!$eventId || !$gender) {
            return response()->json(['athletes' => [], 'event_name' => '']);
        }

        $event = Event::find($eventId);
        $athletesQuery = Athlete::whereHas('registrations', function ($q) use ($eventId) {
            $q->where('event_id', $eventId);
        })->where('jenis_kelamin', $gender)->orderBy('created_at', 'asc')->get();

        $formattedAthletes = $athletesQuery->map(function ($athlete) {
            return [
                'id' => $athlete->id,
                'nama' => $athlete->nama,
                'club' => $athlete->asal_club_sekolah ?? '-',
            ];
        });

        return response()->json([
            'athletes' => $formattedAthletes,
            'event_name' => $event->nama_event ?? ''
        ]);
    }
}
