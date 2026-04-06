<?php

namespace App\Http\Controllers;

use App\Models\HasilLomba;
use App\Models\Athlete;
use App\Models\Registration;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = $this->getStats();
        return view('dashboard.index', $stats);
    }

    public function getStats()
    {
        $hasilLomba = HasilLomba::all();
        
        $totalPeserta = $hasilLomba->count();
        $totalAthletes = Athlete::count();

        if ($hasilLomba->count() > 0) {
            // Parse waktu_format to get total seconds for comparisons
            $times = $hasilLomba->map(function($item) {
                $format = $item->waktu_format; // "00:14.048" format
                $parts = explode(':', $format);
                if (count($parts) == 2) {
                    $menit = intval($parts[0]);
                    $subparts = explode('.', $parts[1]);
                    $detik = intval($subparts[0]);
                    $ms = isset($subparts[1]) ? intval($subparts[1]) : 0;
                    return ($menit * 60000) + ($detik * 1000) + $ms;
                }
                return 0;
            });

            $fastestTime = $times->min();
            $slowestTime = $times->max();
            $avgTime = $times->avg();

            // Find athletes with fastest/slowest times
            $fastestAthlete = $hasilLomba->first(function($item) use ($fastestTime) {
                $format = $item->waktu_format;
                $parts = explode(':', $format);
                if (count($parts) == 2) {
                    $menit = intval($parts[0]);
                    $subparts = explode('.', $parts[1]);
                    $detik = intval($subparts[0]);
                    $ms = isset($subparts[1]) ? intval($subparts[1]) : 0;
                    return (($menit * 60000) + ($detik * 1000) + $ms) == $fastestTime;
                }
            });

            $formatFastestTime = $fastestAthlete->waktu_format ?? '-';
            $formatAvgTime = $this->msToFormat(intval($avgTime));
        } else {
            $totalPeserta = 0;
            $formatFastestTime = '-';
            $formatAvgTime = '-';
            $slowestTime = 0;
        }

        return [
            'totalPeserta' => $totalPeserta,
            'totalAthletes' => $totalAthletes,
            'fastestTime' => $formatFastestTime,
            'averageTime' => $formatAvgTime,
            'slowestTime' => isset($slowestTime) && $slowestTime > 0 ? $this->msToFormat(intval($slowestTime)) : '-',
        ];
    }

    public function getChartData()
    {
        $hasilLomba = HasilLomba::orderBy('timestamp', 'asc')->get();
        
        $labels = [];
        $data = [];
        
        foreach ($hasilLomba as $item) {
            $labels[] = $item->player;
            $format = $item->waktu_format;
            $parts = explode(':', $format);
            if (count($parts) == 2) {
                $menit = intval($parts[0]);
                $subparts = explode('.', $parts[1]);
                $detik = intval($subparts[0]);
                $data[] = ($menit * 60) + $detik;
            }
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }

    private function msToFormat($ms)
    {
        $seconds = intval($ms / 1000);
        $menit = intval($seconds / 60);
        $detik = $seconds % 60;
        $milidetik = $ms % 1000;

        return sprintf("%02d:%02d.%03d", $menit, $detik, $milidetik);
    }
}
