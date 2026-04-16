<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AthleteController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\HeatController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.show');

// Athletes
Route::get('/athletes', [AthleteController::class, 'index'])->name('athletes.index');
Route::post('/athletes', [AthleteController::class, 'store'])->name('athletes.store');
Route::get('/athletes/{athlete}', [AthleteController::class, 'show'])->name('athletes.show');
Route::put('/athletes/{athlete}', [AthleteController::class, 'update'])->name('athletes.update');
Route::delete('/athletes/{athlete}', [AthleteController::class, 'destroy'])->name('athletes.destroy');
Route::get('/athletes/{athlete}/edit', [AthleteController::class, 'edit'])->name('athletes.edit');

// Registrations

Route::get('/registrations', [RegistrationController::class, 'index'])->name('registrations.index');
Route::post('/registrations', [RegistrationController::class, 'store'])->name('registrations.store');

// Results/Hasil Lomba
Route::get('/results', [ResultController::class, 'index'])->name('results.index');
Route::get('/results/data', [ResultController::class, 'getData'])->name('results.data');
Route::post('/results', [ResultController::class, 'store'])->name('results.store');
Route::post('/results/clear-all', [ResultController::class, 'clearAll'])->name('results.clear_all');
Route::any('/insert.php', [ResultController::class, 'insertMentah'])->name('insert.php');

// Heats
Route::get('/heats', [HeatController::class, 'index'])->name('heats.index');
Route::post('/heats/{heat}/activate', [HeatController::class, 'setActive'])->name('heats.activate');
Route::post('/heats/{heat}/complete', [HeatController::class, 'complete'])->name('heats.complete');
Route::post('/heats/regenerate', [HeatController::class, 'regenerate'])->name('heats.regenerate');

// Operator / Wasit Dashboard
Route::get('/operator', [HeatController::class, 'operatorDashboard'])->name('operator.dashboard');

// API Routes for AJAX loading
Route::get('/api/results-table', [ResultController::class, 'getTable']);
Route::get('/api/athletes-table', [AthleteController::class, 'getTable']);
Route::get('/api/athletes/by-event', [AthleteController::class, 'getByEvent'])->name('api.athletes.by_event');
Route::get('/api/dashboard-stats', [DashboardController::class, 'getStats']);
Route::get('/api/dashboard-chart-data', [DashboardController::class, 'getChartData']);
Route::get('/api/heats', [HeatController::class, 'getHeatsApi'])->name('api.heats');
Route::get('/api/active-heat', [HeatController::class, 'getActiveHeatApi'])->name('api.active_heat');

// Participant Auth routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Participant Dashboard routes
Route::prefix('participant')->name('participant.')->group(function () {
    Route::get('/dashboard', function () {
        $kompetisiAktif = 1; // Contoh 1 kompetisi aktif
        $cabangDidaftarkan = \App\Models\Registration::count();
        $tagihanTervalidasi = 0;
        $totalTagihan = 0;
        $atletTerdaftar = \App\Models\Athlete::count();
        $peserta = \App\Models\Registration::with(['athlete', 'event'])->orderBy('created_at', 'desc')->take(10)->get();

        return view('participant.dashboard', compact(
            'kompetisiAktif', 'cabangDidaftarkan', 'tagihanTervalidasi', 'totalTagihan', 'atletTerdaftar', 'peserta'
        ));
    })->name('dashboard');
    
    Route::get('/athletes', function (\Illuminate\Http\Request $request) {
        $query = \App\Models\Athlete::with('registrations.event')->orderBy('created_at', 'desc');
        
        if ($search = $request->get('search')) {
            $query->where('nama', 'LIKE', '%' . $search . '%')
                  ->orWhere('asal_club_sekolah', 'LIKE', '%' . $search . '%');
        }
        
        $perPage = $request->get('per_page', 10);
        $athletes = $query->paginate($perPage)->withQueryString();
        $events = \App\Models\Event::all();
        
        return view('participant.athletes.index', compact('athletes', 'events'));
    })->name('athletes');
    
    Route::get('/competitions/explore', function () {
        $event = \App\Models\Event::first();
        $events = \App\Models\Event::all();
        $athletes = \App\Models\Athlete::all();
        $terdaftar = \App\Models\Registration::distinct('athlete_id')->count('athlete_id');
        $kuota = 9999;
        return view('participant.competitions.explore', compact('event', 'events', 'athletes', 'terdaftar', 'kuota'));
    })->name('competitions.explore');

    Route::get('/competitions/diikuti', function () {
        $registrations = \App\Models\Registration::with('athlete', 'event')->orderBy('created_at', 'desc')->get();
        return view('participant.competitions.diikuti', compact('registrations'));
    })->name('competitions.diikuti');

    Route::get('/competitions/heats', [HeatController::class, 'index'])->name('competitions.heats');
});

