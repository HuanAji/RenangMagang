<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Waktu - Renang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4" style="background: rgba(0,0,0,0.2) !important;">
        <div class="container-lg">
            <a class="navbar-brand" href="{{ route('dashboard') }}">Renang Time Keeper</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white-50" href="{{ route('results.index') }}">Hasil</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-lg">
        <h1 class="text-white mb-4">Hasil Waktu Timekeeper</h1>
        <div class="card" style="box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Player</th>
                            <th>Waktu (menit)</th>
                            <th>Waktu (detik)</th>
                            <th>Waktu (ms)</th>
                            <th>Waktu Format</th>
                            <th>Waktu Input</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($results as $index => $result)
                        @php
                            $format = $result->waktu_format;
                            $parts = explode(':', $format);
                            $menit = $detik = $ms = '-';
                            if (count($parts) === 2) {
                                $menit = $parts[0];
                                $subparts = explode('.', $parts[1]);
                                $detik = $subparts[0];
                                $ms = $subparts[1] ?? '-';
                            }
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $result->player }}</td>
                            <td>{{ $menit }}</td>
                            <td>{{ $detik }}</td>
                            <td>{{ $ms }}</td>
                            <td><strong>{{ $format }}</strong></td>
                            <td>{{ $result->timestamp }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">Tidak ada data hasil</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($results->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $results->links() }}
        </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
