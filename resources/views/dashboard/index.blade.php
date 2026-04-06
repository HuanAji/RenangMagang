<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Time Keeper - Renang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15);
        }

        .stat-icon {
            font-size: 2.5rem;
            color: #667eea;
            margin-bottom: 10px;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: #333;
            margin: 10px 0;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }

        .chart-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .table-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .header {
            color: white;
            margin-bottom: 30px;
        }

        .nav-link {
            color: white !important;
            margin: 0 10px;
            transition: color 0.3s;
        }

        .nav-link:hover {
            color: #ffd700 !important;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }

        .leaderboard-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            border-bottom: 1px solid #eee;
            transition: background 0.3s;
        }

        .leaderboard-item:hover {
            background: #f5f5f5;
        }

        .rank-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: bold;
            margin-right: 12px;
        }

        .time-value {
            font-weight: bold;
            color: #667eea;
            font-size: 1.1rem;
        }

        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 300px;
        }

        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4" style="background: rgba(0,0,0,0.2) !important;">
        <div class="container-lg">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <span class="material-icons" style="vertical-align: middle;">local_pool</span> Renang Time Keeper
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <span class="material-icons" style="font-size: 18px; vertical-align: middle;">dashboard</span> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('athletes.index') }}">
                            <span class="material-icons" style="font-size: 18px; vertical-align: middle;">people</span> Peserta
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('results.index') }}">
                            <span class="material-icons" style="font-size: 18px; vertical-align: middle;">timer</span> Hasil
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-lg">
        <!-- Header -->
        <div class="header">
            <h1 class="mb-2">Dashboard Time Keeper</h1>
            <p class="lead">Monitoring real-time hasil kompetisi renang</p>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon">
                        <span class="material-icons">people</span>
                    </div>
                    <div class="stat-label">Total Peserta Finish</div>
                    <div class="stat-value" id="stat-peserta">{{ $totalPeserta ?? 0 }}</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon" style="color: #ffa500;">
                        <span class="material-icons">trending_down</span>
                    </div>
                    <div class="stat-label">Waktu Tercepat</div>
                    <div class="stat-value time-value" id="stat-fastest">{{ $fastestTime ?? '-' }}</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon" style="color: #ff6b6b;">
                        <span class="material-icons">trending_up</span>
                    </div>
                    <div class="stat-label">Rata-rata Waktu</div>
                    <div class="stat-value time-value" id="stat-average">{{ $averageTime ?? '-' }}</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon" style="color: #4ecdc4;">
                        <span class="material-icons">assignment</span>
                    </div>
                    <div class="stat-label">Total Atlet Terdaftar</div>
                    <div class="stat-value" id="stat-athletes">{{ $totalAthletes ?? 0 }}</div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <div class="col-lg-8">
                <div class="chart-container">
                    <h5 class="mb-3">Grafik Waktu Peserta</h5>
                    <canvas id="timeChart"></canvas>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="chart-container">
                    <h5 class="mb-3">🏆 Top 5 Tercepat</h5>
                    <div id="leaderboard" class="leaderboard">
                        <div class="loading">
                            <div class="loading-spinner"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results Table -->
        <div class="row">
            <div class="col-12">
                <div class="table-container">
                    <h5 class="mb-3">📊 Hasil Waktu Timekeeper (Real-time)</h5>
                    <div style="height: 400px; overflow-y: auto;">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th style="text-align: center; width: 4%;">No</th>
                                    <th style="text-align: center; width: 6%;">Jalur</th>
                                    <th style="width: 18%;">Nama Atlet</th>
                                    <th style="text-align: center; width: 8%;">Menit</th>
                                    <th style="text-align: center; width: 8%;">Detik</th>
                                    <th style="text-align: center; width: 8%;">MS</th>
                                    <th style="text-align: center; width: 13%;">Waktu Format</th>
                                    <th style="width: 20%;">Waktu Input</th>
                                </tr>
                            </thead>
                            <tbody id="resultsTableBody">
                                <tr><td colspan="8" class="text-center py-4">Loading data...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script>
        let timeChart = null;

        function loadDashboardStats() {
            fetch('{{ route("dashboard.show") }}')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('stat-peserta').textContent = data.totalPeserta || 0;
                    document.getElementById('stat-fastest').textContent = data.fastestTime || '-';
                    document.getElementById('stat-average').textContent = data.averageTime || '-';
                    document.getElementById('stat-athletes').textContent = data.totalAthletes || 0;
                });
        }

        function loadChartData() {
            fetch('{{ route("results.data") }}')
                .then(response => response.json())
                .then(data => {
                    const labels = data.map(item => item.player);
                    const times = data.map(item => {
                        const format = item.waktu_format;
                        const parts = format.split(':');
                        if (parts.length === 2) {
                            const menit = parseInt(parts[0]);
                            const subparts = parts[1].split('.');
                            const detik = parseInt(subparts[0]);
                            return (menit * 60) + detik;
                        }
                        return 0;
                    });

                    const ctx = document.getElementById('timeChart').getContext('2d');
                    if (timeChart) timeChart.destroy();
                    
                    timeChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Waktu (detik)',
                                data: times,
                                backgroundColor: 'rgba(102, 126, 234, 0.6)',
                                borderColor: 'rgba(102, 126, 234, 1)',
                                borderWidth: 1,
                                borderRadius: 8,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: {
                                    display: true,
                                    labels: { font: { size: 12 } }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: { display: true, text: 'Waktu (detik)' }
                                }
                            }
                        }
                    });

                    loadLeaderboard(data);
                });
        }

        function loadLeaderboard(data) {
            const sorted = [...data].sort((a, b) => {
                const aFormat = a.waktu_format;
                const bFormat = b.waktu_format;
                const aParts = aFormat.split(':');
                const bParts = bFormat.split(':');
                
                const aTime = parseInt(aParts[0]) * 60000 + parseInt(aParts[1].replace(/\D/g, ''));
                const bTime = parseInt(bParts[0]) * 60000 + parseInt(bParts[1].replace(/\D/g, ''));
                
                return aTime - bTime;
            }).slice(0, 5);

            let html = '';
            sorted.forEach((item, index) => {
                html += `
                    <div class="leaderboard-item">
                        <span>
                            <span class="rank-badge">${index + 1}</span>
                            <strong>${item.player}</strong>
                        </span>
                        <span class="time-value">${item.waktu_format}</span>
                    </div>
                `;
            });

            document.getElementById('leaderboard').innerHTML = html;
        }

        function loadResultsTable() {
            fetch('{{ route("results.data") }}')
                .then(response => response.json())
                .then(data => {
                    let html = '<tr><td colspan="8" style="text-align: left; background-color: #f9f9f9; padding: 4px 10px; font-size: 14px;">Koneksi berhasil ke database: db-renang</td></tr>';
                    
                    data.forEach((item, index) => {
                        const format = item.waktu_format;
                        const parts = format.split(':');
                        let menit = '-', detik = '-', ms = '-';
                        
                        if (parts.length === 2) {
                            menit = parts[0];
                            const subparts = parts[1].split('.');
                            detik = subparts[0];
                            ms = subparts[1] || '-';
                        }

                        const athleteName = item.athlete_name || '<span class="text-muted">-</span>';

                        html += `
                            <tr>
                                <td style="text-align: center;">${index + 1}</td>
                                <td style="text-align: center;"><strong>${item.player}</strong></td>
                                <td>${athleteName}</td>
                                <td style="text-align: center;">${menit}</td>
                                <td style="text-align: center;">${detik}</td>
                                <td style="text-align: center;">${ms}</td>
                                <td style="text-align: center;">${format}</td>
                                <td>${item.timestamp}</td>
                            </tr>
                        `;
                    });

                    document.getElementById('resultsTableBody').innerHTML = html;
                });
        }

        // Initial load
        window.addEventListener('load', () => {
            loadDashboardStats();
            loadChartData();
            loadResultsTable();
        });

        // Auto-refresh every 2 seconds
        setInterval(() => {
            loadDashboardStats();
            loadChartData();
            loadResultsTable();
        }, 2000);
    </script>
</body>
</html>
