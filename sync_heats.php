<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Registration;
use App\Services\HeatGeneratorService;
use App\Models\Heat;

echo "Memulai sinkronisasi / pembuatan ulang Heat untuk peserta lama...\n";

// Get all unique event_id + jenis_kelamin combinations
$combos = Registration::select('event_id', 'athletes.jenis_kelamin')
    ->join('athletes', 'registrations.athlete_id', '=', 'athletes.id')
    ->whereNotNull('athletes.jenis_kelamin')
    ->groupBy('event_id', 'athletes.jenis_kelamin')
    ->get();

$service = new HeatGeneratorService();

$count = 0;
foreach ($combos as $combo) {
    if ($combo->jenis_kelamin) {
        $service->generateForEvent($combo->event_id, $combo->jenis_kelamin);
        echo "✅ Generated: Event {$combo->event_id} - Gender {$combo->jenis_kelamin}\n";
        $count++;
    }
}

echo "\nSelesai! Berhasil meng-generate ulang heat untuk {$count} kombinasi kategori.\n";
echo "Total Heat saat ini: " . Heat::count() . "\n";
