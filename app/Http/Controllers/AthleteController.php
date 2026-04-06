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
        $athletes = Athlete::with(['registrations.event', 'trackRecords'])->orderBy('created_at', 'desc')->get();
        $events = Event::all();
        return view('athletes.index', compact('athletes', 'events'));
    }

    public function show(Athlete $athlete)
    {
        return response()->json($athlete->load('registrations.event'));
    }

    public function documents(Athlete $athlete)
    {
        return view('participant.athletes.documents', compact('athlete'));
    }

    public function trackRecords(Athlete $athlete)
    {
        $athlete->load('trackRecords');
        $events = Event::all(); // To populate "Nomor Lomba" dropdown if needed
        return view('participant.athletes.track_records', compact('athlete', 'events'));
    }

    public function storeTrackRecord(Request $request, Athlete $athlete)
    {
        $validated = $request->validate([
            'nama_kompetisi' => 'required|string',
            'nomor_lomba' => 'required|string',
            'durasi_renang' => 'required|string',
        ]);

        \App\Models\TrackRecord::create([
            'athlete_id' => $athlete->id,
            'nama_kompetisi' => $validated['nama_kompetisi'],
            'nomor_lomba' => $validated['nomor_lomba'],
            'durasi_renang' => $validated['durasi_renang'],
        ]);

        return response()->json(['message' => '✅ Track record berhasil ditambahkan!']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string',
            'umur' => 'nullable|numeric',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'required|in:L,P',
            'asal_club_sekolah' => 'required|string',
            'provinsi' => 'nullable|string',
            'kabupaten_kota' => 'nullable|string',
            'event_id' => 'nullable|array',
            'event_id.*' => 'integer|exists:events,id',
        ]);

        try {
            $athlete = Athlete::create([
                'nama' => $validated['nama'],
                'umur' => $validated['umur'] ?? null,
                'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'asal_club_sekolah' => $validated['asal_club_sekolah'],
                'provinsi' => $validated['provinsi'] ?? null,
                'kabupaten_kota' => $validated['kabupaten_kota'] ?? null,
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

    public function uploadDocument(Request $request, Athlete $athlete)
    {
        $request->validate([
            'surat_keterangan' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'akta_kelahiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        if ($request->hasFile('surat_keterangan')) {
            $athlete->surat_keterangan_path = $request->file('surat_keterangan')->store('documents', 'public');
        }

        if ($request->hasFile('akta_kelahiran')) {
            $athlete->akta_kelahiran_path = $request->file('akta_kelahiran')->store('documents', 'public');
        }

        // Logic check kelengkapan
        if (!empty($athlete->surat_keterangan_path) && !empty($athlete->akta_kelahiran_path)) {
            $athlete->kelengkapan_dokumen = 'Lengkap';
        }

        $athlete->save();

        return response()->json(['message' => '✅ Dokumen berhasil diunggah!']);
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
