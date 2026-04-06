<?php

namespace App\Http\Controllers;

use App\Models\HasilLomba;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function index()
    {
        $results = HasilLomba::orderBy('timestamp', 'desc')->paginate(15);
        return view('results.index', compact('results'));
    }

    public function getData()
    {
        $results = HasilLomba::orderBy('timestamp', 'desc')->get();

        // Get all active heats' lane assignments for player-to-athlete mapping
        $activeHeats = \App\Models\Heat::where('status', 'active')
            ->orWhere('status', 'completed')
            ->with('laneAssignments.athlete', 'event')
            ->get();

        // Build a mapping: lane_number => [athlete_name, event_name]
        // Use the most recent active/completed heat's assignments
        $laneMap = [];
        foreach ($activeHeats as $heat) {
            foreach ($heat->laneAssignments as $la) {
                $key = strval($la->lane_number);
                $laneMap[$key] = [
                    'athlete_name' => $la->athlete->nama ?? null,
                    'event_name' => $heat->event->nama_event ?? null,
                ];
            }
        }

        // Attach athlete info to each result
        $results = $results->map(function ($item) use ($laneMap) {
            $playerKey = trim($item->player);
            $item->athlete_name = $laneMap[$playerKey]['athlete_name'] ?? null;
            $item->event_name = $laneMap[$playerKey]['event_name'] ?? null;
            return $item;
        });

        return response()->json($results);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'player' => 'required|string',
            'waktu_format' => 'required|string',
            'waktu_menit' => 'integer|min:0',
            'waktu_detik' => 'integer|min:0',
            'waktu_ms' => 'integer|min:0',
        ]);

        try {
            HasilLomba::create($validated);
            return response()->json(['message' => 'Data hasil lomba berhasil disimpan'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getTable()
    {
        $results = HasilLomba::orderBy('timestamp', 'desc')->get();
        $html = '<tr><td colspan="7" style="text-align: left; background-color: #f9f9f9; padding: 4px 10px; font-size: 14px;">Koneksi berhasil ke database: db-renang</td></tr>';

        foreach ($results as $no => $result) {
            $format = $result->waktu_format;
            $parts = explode(':', $format);
            $menit = $detik = $ms = '-';

            if (count($parts) === 2) {
                $menit = $parts[0];
                $subparts = explode('.', $parts[1]);
                if (count($subparts) === 2) {
                    $detik = $subparts[0];
                    $ms = $subparts[1];
                }
            }

            $html .= '<tr>';
            $html .= '<td>' . ($no + 1) . '</td>';
            $html .= '<td>' . htmlspecialchars($result->player) . '</td>';
            $html .= '<td>' . $menit . '</td>';
            $html .= '<td>' . $detik . '</td>';
            $html .= '<td>' . $ms . '</td>';
            $html .= '<td>' . $format . '</td>';
            $html .= '<td>' . $result->timestamp . '</td>';
            $html .= '</tr>';
        }

        return response()->json(['html' => $html]);
    }
}
