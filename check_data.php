<?php
// Check registration data
$regs = \App\Models\Registration::where('event_id', 1)->with('athlete')->get();
echo "== Registrations for Event 1 (Gaya Bebas 50m) ==" . PHP_EOL;
foreach ($regs as $r) {
    $jk = $r->athlete->jenis_kelamin ?? 'NULL';
    echo "  Athlete #{$r->athlete->id} | {$r->athlete->nama} | JK: {$jk}" . PHP_EOL;
}
echo PHP_EOL;
echo "Total Registrations: " . \App\Models\Registration::count() . PHP_EOL;
echo "Total Heats: " . \App\Models\Heat::count() . PHP_EOL;
echo "Total Lane Assignments: " . \App\Models\LaneAssignment::count() . PHP_EOL;
