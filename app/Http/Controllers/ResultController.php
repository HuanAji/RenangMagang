<?php

namespace App\Http\Controllers;

use App\Models\HasilLomba;
use App\Models\Heat;
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

    public function clearAll()
    {
        HasilLomba::truncate();
        return response()->json(['message' => '✅ Semua data hasil lomba berhasil dihapus!']);
    }

    /**
     * Cari athlete_id berdasarkan nomor jalur (player) dari heat yang sedang aktif.
     * IoT mengirim player = nomor jalur (1-8), sistem cocokkan ke lane_assignments.
     */
    private function resolveAthleteFromLane(int $laneNumber): array
    {
        // Cari heat yang aktif
        $activeHeat = Heat::with('laneAssignments.athlete')
            ->where('status', 'active')
            ->first();

        if (!$activeHeat) {
            return ['athlete_id' => null, 'athlete_name' => null];
        }

        // Cari lane assignment yang sesuai dengan nomor jalur
        $lane = $activeHeat->laneAssignments->firstWhere('lane_number', $laneNumber);

        if (!$lane || !$lane->athlete) {
            return ['athlete_id' => null, 'athlete_name' => null];
        }

        return [
            'athlete_id'   => $lane->athlete->id,
            'athlete_name' => $lane->athlete->nama,
        ];
    }

    public function insertMentah(Request $request)
    {
        // === MODE JSON ===
        $json = [];
        if ($request->isJson() || stripos($request->header('Content-Type') ?? '', 'application/json') !== false) {
            $json = $request->json()->all();
        } else {
            // Coba parsing mentah (berguna jika di postman lupa setting header application/json)
            $decoded = json_decode($request->getContent(), true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $json = $decoded;
            }
        }

        if (!empty($json)) {

            foreach ($json as $playerKey => $playerData) {
                if (isset($playerData['waktu_ms'], $playerData['waktu_detik'], $playerData['waktu_menit'], $playerData['waktu_format'])) {
                    // Ambil nomor jalur dari key "player1", "player2", atau angka langsung
                    preg_match('/player(\d+)/', $playerKey, $matches);
                    $laneNumber = (int)($matches[1] ?? $playerKey);

                    // Auto-mapping: cari atlet berdasarkan jalur di heat aktif
                    $athleteInfo = $this->resolveAthleteFromLane($laneNumber);

                    HasilLomba::create([
                        'player'       => $laneNumber,
                        'athlete_id'   => $athleteInfo['athlete_id'],
                        'athlete_name' => $athleteInfo['athlete_name'],
                        'waktu_ms'     => $playerData['waktu_ms'],
                        'waktu_detik'  => $playerData['waktu_detik'],
                        'waktu_menit'  => $playerData['waktu_menit'],
                        'waktu_format' => $playerData['waktu_format'],
                    ]);
                }
            }

            return response("✅ Data JSON berhasil disimpan.", 200)->header('Content-Type', 'text/plain');
        }

        // === MODE FORM-DATA (per player/jalur) ===
        $player       = $request->input('player', '');
        $waktu_format = $request->input('waktu_format', '');

        if ($player === '' || $waktu_format === '') {
            return response("❌ Data tidak lengkap.", 200)->header('Content-Type', 'text/plain');
        }

        $laneNumber  = (int)$player;
        $athleteInfo = $this->resolveAthleteFromLane($laneNumber);

        try {
            HasilLomba::create([
                'player'       => $laneNumber,
                'athlete_id'   => $athleteInfo['athlete_id'],
                'athlete_name' => $athleteInfo['athlete_name'],
                'waktu_ms'     => $request->input('waktu_ms', 0),
                'waktu_detik'  => $request->input('waktu_detik', 0),
                'waktu_menit'  => $request->input('waktu_menit', 0),
                'waktu_format' => $waktu_format,
            ]);
            return response("✅ Data berhasil disimpan.", 200)->header('Content-Type', 'text/plain');
        } catch (\Exception $e) {
            return response("❌ Gagal eksekusi query: " . $e->getMessage(), 200)->header('Content-Type', 'text/plain');
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

            // Ambil nama atlet: prioritaskan dari kolom athlete_name (cache), lalu dari relasi
            $namaAtlet = $result->athlete_name ?? ($result->athlete->nama ?? null);
            $playerDisplay = 'Jalur ' . $result->player . ($namaAtlet ? ' — ' . $namaAtlet : '');

            $html .= '<tr>';
            $html .= '<td>' . ($no + 1) . '</td>';
            $html .= '<td>' . htmlspecialchars($playerDisplay) . '</td>';
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
