<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Wasit / Operator IoT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>
    <style>
        :root {
            --bg-body: #f4f6f9;
            --bg-card: #ffffff;
            --bg-panel: #f8fafc;
            --accent-blue: #003399;
            --accent-blue-light: #e8eef8;
            --accent-green: #16a34a;
            --accent-green-light: #dcfce7;
            --accent-red: #dc2626;
            --accent-red-light: #fee2e2;
            --accent-orange: #ea580c;
            --accent-orange-light: #fff7ed;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --border-color: #e2e8f0;
        }

        * { box-sizing: border-box; }
        body {
            background: var(--bg-body);
            color: var(--text-primary);
            font-family: 'Inter', sans-serif;
            margin: 0;
            min-height: 100vh;
        }

        /* ===== HEADER ===== */
        .op-header {
            background: var(--accent-blue);
            padding: 14px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,51,153,0.15);
        }
        .op-header h1 {
            font-size: 1.15rem;
            font-weight: 700;
            margin: 0;
            color: white;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .op-header .live-dot {
            width: 10px; height: 10px;
            background: #00e676;
            border-radius: 50%;
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.3); }
        }

        /* ===== LAYOUT ===== */
        .op-layout {
            display: grid;
            grid-template-columns: 330px 1fr;
            gap: 0;
            height: calc(100vh - 54px);
        }

        /* ===== LEFT PANEL ===== */
        .op-panel {
            background: var(--bg-card);
            border-right: 1px solid var(--border-color);
            overflow-y: auto;
            padding: 18px;
        }
        .panel-section { margin-bottom: 22px; }
        .panel-label {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: var(--text-secondary);
            margin-bottom: 10px;
            font-weight: 700;
        }
        .panel-select {
            width: 100%;
            background: var(--bg-body);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 9px 12px;
            font-size: 0.88rem;
            margin-bottom: 10px;
        }
        .panel-select:focus { border-color: var(--accent-blue); outline: none; box-shadow: 0 0 0 3px rgba(0,51,153,0.1); }

        .btn-load-heats {
            width: 100%;
            background: var(--accent-blue);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px;
            font-weight: 600;
            font-size: 0.88rem;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 6px;
            transition: background 0.2s;
        }
        .btn-load-heats:hover { background: #002277; }

        /* Heat cards */
        .heat-item {
            background: var(--bg-body);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 12px 14px;
            margin-bottom: 10px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .heat-item:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
        .heat-item.active-heat {
            border-color: var(--accent-green);
            background: var(--accent-green-light);
        }
        .heat-item.completed-heat {
            opacity: 0.55;
        }
        .heat-item-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 6px;
        }
        .heat-item-header strong { font-size: 0.92rem; color: var(--text-primary); }
        .heat-badge {
            font-size: 0.68rem; padding: 3px 8px; border-radius: 20px;
            font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;
        }
        .heat-badge.pending { background: #f1f5f9; color: var(--text-secondary); }
        .heat-badge.active { background: var(--accent-green-light); color: var(--accent-green); }
        .heat-badge.completed { background: #f1f5f9; color: var(--text-muted); }
        .heat-athletes { font-size: 0.78rem; color: var(--text-secondary); margin-bottom: 8px; line-height: 1.5; }

        .heat-actions { display: flex; gap: 6px; }
        .btn-heat {
            flex: 1; padding: 7px 0; border: none; border-radius: 6px;
            font-weight: 600; font-size: 0.78rem; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 4px;
            transition: 0.2s;
        }
        .btn-heat .material-icons { font-size: 1rem; }
        .btn-activate { background: var(--accent-green-light); color: var(--accent-green); }
        .btn-activate:hover { background: #bbf7d0; }
        .btn-complete { background: var(--accent-red-light); color: var(--accent-red); }
        .btn-complete:hover { background: #fecaca; }

        /* ===== RIGHT MAIN ===== */
        .op-main { overflow-y: auto; padding: 20px 24px; background: var(--bg-body); }

        /* Banner */
        .active-heat-banner {
            background: var(--accent-blue);
            color: white;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 18px;
            display: flex; justify-content: space-between; align-items: center;
            box-shadow: 0 2px 8px rgba(0,51,153,0.15);
        }
        .active-heat-banner.no-active { background: #94a3b8; }
        .ahb-left h3 { margin: 0; font-size: 1.1rem; font-weight: 700; }
        .ahb-left p { margin: 4px 0 0; font-size: 0.85rem; color: rgba(255,255,255,0.8); }
        .ahb-right .live-tag {
            background: var(--accent-red);
            color: white;
            font-size: 0.72rem; font-weight: 700; padding: 4px 12px; border-radius: 20px;
            display: flex; align-items: center; gap: 5px;
            text-transform: uppercase; letter-spacing: 0.8px;
        }

        /* Stopwatch */
        .stopwatch-wrap {
            background: var(--bg-card);
            border: 2px solid var(--border-color);
            border-radius: 14px;
            margin-bottom: 18px;
            padding: 24px;
            text-align: center;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        }
        .stopwatch-wrap.sw-running {
            border-color: var(--accent-green);
            box-shadow: 0 0 16px rgba(22,163,74,0.12);
        }
        .stopwatch-wrap.sw-stopped { border-color: var(--accent-orange); }
        .sw-label {
            font-size: 0.72rem; text-transform: uppercase; letter-spacing: 1.5px;
            color: var(--text-secondary); margin-bottom: 4px; font-weight: 600;
        }
        .sw-display {
            font-family: 'Inter', monospace;
            font-size: 4.2rem; font-weight: 800; letter-spacing: 3px;
            color: var(--text-primary); line-height: 1; margin: 10px 0 18px;
            transition: color 0.3s;
        }
        .sw-running .sw-display { color: var(--accent-green); }
        .sw-stopped .sw-display { color: var(--accent-orange); }
        .sw-ms { font-size: 2.6rem; opacity: 0.5; }
        .sw-buttons { display: flex; gap: 12px; justify-content: center; }
        .btn-sw {
            padding: 12px 36px; border: none; border-radius: 10px;
            font-weight: 700; font-size: 0.95rem; cursor: pointer;
            display: flex; align-items: center; gap: 6px;
            transition: 0.2s; letter-spacing: 0.5px;
        }
        .btn-sw .material-icons { font-size: 1.2rem; }
        .btn-sw-start { background: var(--accent-green); color: white; }
        .btn-sw-start:hover { background: #15803d; }
        .btn-sw-stop { background: var(--accent-red); color: white; }
        .btn-sw-stop:hover { background: #b91c1c; }
        .btn-sw-reset { background: #f1f5f9; color: var(--text-secondary); border: 1px solid var(--border-color); }
        .btn-sw-reset:hover { background: #e2e8f0; color: var(--text-primary); }

        /* Lane table */
        .lane-table-wrap {
            background: var(--bg-card);
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 18px;
            border: 1px solid var(--border-color);
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        }
        .lane-table-wrap h5 {
            padding: 14px 18px 8px; margin: 0;
            font-size: 0.9rem; font-weight: 700; color: var(--text-primary);
        }
        .lane-table { width: 100%; border-collapse: collapse; }
        .lane-table th {
            background: var(--bg-body);
            font-size: 0.72rem; text-transform: uppercase; letter-spacing: 1px;
            color: var(--text-secondary); padding: 10px 14px; text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        .lane-table td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--border-color);
            font-size: 0.88rem;
        }
        .lane-table tr:last-child td { border-bottom: none; }
        .lane-num {
            width: 36px; height: 36px; border-radius: 50%;
            background: var(--accent-blue); color: white;
            display: inline-flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.9rem;
        }
        .result-time { color: var(--accent-green); font-weight: 700; font-size: 1rem; }
        .no-time { color: var(--text-muted); font-style: italic; }

        /* IoT table */
        .iot-table-wrap {
            background: var(--bg-card);
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--border-color);
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        }
        .iot-table-header {
            padding: 14px 18px;
            display: flex; justify-content: space-between; align-items: center;
            border-bottom: 1px solid var(--border-color);
        }
        .iot-table-header h5 { margin: 0; font-size: 0.92rem; font-weight: 700; }
        .btn-clear-iot {
            background: var(--accent-red-light);
            color: var(--accent-red);
            border: 1px solid rgba(220,38,38,0.2);
            border-radius: 6px; padding: 5px 14px;
            font-size: 0.78rem; font-weight: 600; cursor: pointer;
            display: flex; align-items: center; gap: 4px; transition: 0.2s;
        }
        .btn-clear-iot:hover { background: #fecaca; }
        .btn-clear-iot .material-icons { font-size: 0.95rem; }
        .iot-scroll { max-height: 360px; overflow-y: auto; }
        .iot-table { width: 100%; border-collapse: collapse; }
        .iot-table th {
            background: var(--bg-body);
            font-size: 0.72rem; text-transform: uppercase; letter-spacing: 1px;
            color: var(--text-secondary); padding: 10px 14px; text-align: center;
            border-bottom: 1px solid var(--border-color);
            position: sticky; top: 0; z-index: 1;
        }
        .iot-table td {
            padding: 9px 14px; border-bottom: 1px solid var(--border-color);
            font-size: 0.85rem; text-align: center;
        }
        .iot-table td:nth-child(3) { text-align: left; }
        .iot-table tr:hover { background: #f8fafc; }

        .empty-state {
            text-align: center; padding: 40px 20px; color: var(--text-muted);
        }
        .empty-state .material-icons { font-size: 3rem; margin-bottom: 10px; display: block; color: var(--text-muted); }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
    </style>
</head>
<body>

<!-- HEADER -->
<header class="op-header">
    <h1>
        <span class="material-icons">sports</span>
        Dashboard Wasit / Operator IoT
        <span class="live-dot"></span>
    </h1>
    <div style="font-size: 0.82rem; color: rgba(255,255,255,0.8);">
        <span class="material-icons" style="font-size:0.9rem; vertical-align:middle;">schedule</span>
        <span id="clock"></span>
    </div>
</header>

<!-- LAYOUT -->
<div class="op-layout">

    <!-- LEFT PANEL -->
    <aside class="op-panel">
        <div class="panel-section">
            <div class="panel-label">🎛️ Panel Kendali Heat</div>
            <select id="select-event" class="panel-select">
                <option value="" disabled selected>-- Pilih Nomor Event --</option>
                @foreach($events as $evt)
                    <option value="{{ $evt->id }}">{{ str_pad($evt->id, 3, '0', STR_PAD_LEFT) }} — {{ $evt->nama_event }}</option>
                @endforeach
            </select>
            <select id="select-gender" class="panel-select">
                <option value="" disabled selected>-- Jenis Kelamin --</option>
                <option value="L">Putra</option>
                <option value="P">Putri</option>
            </select>
            <button class="btn-load-heats" id="btn-load-heats">
                <span class="material-icons" style="font-size:1.1rem;">search</span> Tampilkan Heat
            </button>
        </div>
        <div class="panel-section">
            <div class="panel-label">📋 Daftar Heat</div>
            <div id="heat-list">
                <div class="empty-state" style="padding: 20px;">
                    <span class="material-icons" style="font-size: 2rem;">format_list_numbered</span>
                    <p style="margin: 6px 0 0; font-size: 0.82rem;">Pilih event & gender di atas</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- RIGHT MAIN -->
    <main class="op-main">

        <!-- Banner -->
        <div id="active-heat-banner" class="active-heat-banner no-active">
            <div class="ahb-left">
                <h3 id="ahb-title">Belum ada Heat aktif</h3>
                <p id="ahb-subtitle">Aktifkan salah satu Heat dari panel kiri untuk memulai perlombaan</p>
            </div>
            <div class="ahb-right" id="ahb-live" style="display:none;">
                <span class="live-tag"><span class="live-dot" style="width:7px;height:7px;background:white;"></span> LIVE</span>
            </div>
        </div>

        <!-- STOPWATCH -->
        <div class="stopwatch-wrap" id="sw-wrap">
            <div class="sw-label">⏱️ Stopwatch Perlombaan</div>
            <div class="sw-display" id="sw-display">00:00<span class="sw-ms">.000</span></div>
            <div class="sw-buttons">
                <button class="btn-sw btn-sw-start" id="btn-sw-start">
                    <span class="material-icons">play_arrow</span> MULAI
                </button>
                <button class="btn-sw btn-sw-stop" id="btn-sw-stop" style="display:none;">
                    <span class="material-icons">stop</span> STOP
                </button>
                <button class="btn-sw btn-sw-reset" id="btn-sw-reset" style="display:none;">
                    <span class="material-icons">replay</span> RESET
                </button>
            </div>
        </div>

        <!-- Lane Table -->
        <div class="lane-table-wrap">
            <h5>🏊 Peserta di Kolam (Heat Aktif)</h5>
            <table class="lane-table">
                <thead>
                    <tr>
                        <th style="width:60px; text-align:center;">Jalur</th>
                        <th>Nama Atlet</th>
                        <th>Klub / Sekolah</th>
                        <th style="text-align:center;">Hasil Waktu</th>
                    </tr>
                </thead>
                <tbody id="lane-tbody">
                    <tr><td colspan="4" class="empty-state" style="padding:30px;">
                        <span class="material-icons">pool</span>Aktifkan Heat untuk melihat peserta
                    </td></tr>
                </tbody>
            </table>
        </div>

        <!-- IoT Results -->
        <div class="iot-table-wrap">
            <div class="iot-table-header">
                <h5>⏱️ Hasil Waktu IoT (Real-time)</h5>
                <div style="display:flex; gap:8px; align-items:center;">
                    <button class="btn-clear-iot" id="btn-reset-db" title="Hapus semua data dari database">
                        <span class="material-icons">delete_sweep</span> Reset Semua Waktu
                    </button>
                    <button class="btn-clear-iot" id="btn-export-excel" title="Export ke Excel" style="background:var(--accent-green-light); color:var(--accent-green); border-color:rgba(22,163,74,0.2);">
                        <span class="material-icons">table_view</span> Export Excel
                    </button>
                    <button class="btn-clear-iot" id="btn-clear-iot" title="Bersihkan tampilan layar saja">
                        <span class="material-icons">cleaning_services</span> Bersihkan Layar
                    </button>
                </div>
            </div>
            <div class="iot-scroll">
                <table class="iot-table">
                    <thead>
                        <tr>
                            <th style="width:4%;">No</th>
                            <th style="width:6%;">Jalur</th>
                            <th style="width:20%; text-align:left;">Nama Atlet</th>
                            <th style="width:8%;">Menit</th>
                            <th style="width:8%;">Detik</th>
                            <th style="width:8%;">MS</th>
                            <th style="width:14%;">Waktu Format</th>
                        </tr>
                    </thead>
                    <tbody id="iot-tbody">
                        <tr><td colspan="8" class="empty-state" style="padding:30px;">
                            <span class="material-icons">timer</span> Menunggu data dari alat IoT...
                        </td></tr>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>

<script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;

    // ===== CLOCK =====
    function updateClock() {
        const now = new Date();
        document.getElementById('clock').textContent = now.toLocaleString('id-ID', {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
            hour: '2-digit', minute: '2-digit', second: '2-digit'
        });
    }
    setInterval(updateClock, 1000);
    updateClock();

    // ===== LOAD HEATS =====
    document.getElementById('btn-load-heats').addEventListener('click', loadHeats);

    function loadHeats() {
        const eventId = document.getElementById('select-event').value;
        const gender = document.getElementById('select-gender').value;
        if (!eventId || !gender) { alert('Pilih event dan jenis kelamin!'); return; }
        fetch(`/api/heats?event_id=${eventId}&jenis_kelamin=${gender}`)
            .then(r => r.json())
            .then(data => renderHeatList(data.heats, data.event_name));
    }

    function renderHeatList(heats, eventName) {
        const container = document.getElementById('heat-list');
        if (!heats || heats.length === 0) {
            container.innerHTML = `<div class="empty-state" style="padding:20px;">
                <span class="material-icons" style="font-size:2rem;">event_busy</span>
                <p style="margin:6px 0 0;font-size:0.82rem;">Belum ada heat untuk kombinasi ini</p>
            </div>`;
            return;
        }
        let html = '';
        heats.forEach(heat => {
            const totalPeserta = heat.lanes.length;
            const isFull = totalPeserta >= 8;
            const statusClass = heat.status === 'active' ? 'active-heat' : heat.status === 'completed' ? 'completed-heat' : '';
            const badgeClass = heat.status;
            const badgeText = heat.status === 'active' ? '🔴 AKTIF' : heat.status === 'completed' ? '✅ SELESAI' : '⏳ MENUNGGU';

            // Ringkasan peserta: jumlah + info penuh/tidak
            const pesertaInfo = isFull
                ? `<span style="font-weight:600; color:var(--accent-green);">${totalPeserta} peserta</span> <span style="color:var(--text-muted); font-size:0.75rem;">(jalur 1–${totalPeserta})</span>`
                : `<span style="font-weight:600; color:var(--accent-orange);">${totalPeserta} peserta</span> <span style="color:var(--text-muted); font-size:0.75rem;">(tidak penuh)</span>`;

            // Semua heat bisa dipilih/diaktifkan bebas (tidak harus urutan)
            let actionHtml = '';
            if (heat.status === 'active') {
                actionHtml = `<button class="btn-heat btn-complete" onclick="completeHeat(${heat.id})">
                    <span class="material-icons">stop</span> Tandai Selesai
                </button>`;
            } else {
                // pending ATAU completed — tetap bisa diaktifkan ulang
                actionHtml = `<button class="btn-heat btn-activate" onclick="activateHeat(${heat.id})">
                    <span class="material-icons">play_arrow</span> ${heat.status === 'completed' ? 'Aktifkan Ulang' : 'Aktifkan'}
                </button>`;
            }

            html += `
                <div class="heat-item ${statusClass}">
                    <div class="heat-item-header">
                        <strong>Heat ${heat.heat_number}</strong>
                        <span class="heat-badge ${badgeClass}">${badgeText}</span>
                    </div>
                    <div class="heat-athletes" style="margin-bottom:8px;">${pesertaInfo}</div>
                    <div class="heat-actions">${actionHtml}</div>
                </div>`;
        });
        container.innerHTML = html;
    }

    // ===== ACTIVATE / COMPLETE =====
    function activateHeat(id) {
        fetch(`/heats/${id}/activate`, {
            method:'POST', headers:{'X-CSRF-TOKEN': CSRF, 'Accept':'application/json'}
        }).then(r => r.json()).then(() => { loadHeats(); loadActiveHeat(); });
    }
    function completeHeat(id) {
        if (!confirm('Tandai Heat ini selesai?')) return;
        fetch(`/heats/${id}/complete`, {
            method:'POST', headers:{'X-CSRF-TOKEN': CSRF, 'Accept':'application/json'}
        }).then(r => r.json()).then(() => { loadHeats(); loadActiveHeat(); });
    }

    // ===== ACTIVE HEAT =====
    function loadActiveHeat() {
        fetch('/api/active-heat').then(r => r.json()).then(data => {
            const banner = document.getElementById('active-heat-banner');
            const title = document.getElementById('ahb-title');
            const sub = document.getElementById('ahb-subtitle');
            const live = document.getElementById('ahb-live');
            const tbody = document.getElementById('lane-tbody');
            if (data.active) {
                banner.classList.remove('no-active');
                title.textContent = data.info;
                sub.textContent = `${data.lanes.length} Atlet siap berlomba`;
                live.style.display = 'block';
                let laneHtml = '';
                data.lanes.forEach(l => {
                    const timeHtml = l.result_time
                        ? `<span class="result-time">${l.result_time}</span>`
                        : `<span class="no-time">menunggu...</span>`;
                    laneHtml += `<tr>
                        <td style="text-align:center;"><span class="lane-num">${l.lane_number}</span></td>
                        <td><strong>${l.athlete_name}</strong></td>
                        <td style="color:var(--text-secondary);">${l.club}</td>
                        <td style="text-align:center;">${timeHtml}</td>
                    </tr>`;
                });
                tbody.innerHTML = laneHtml;
            } else {
                banner.classList.add('no-active');
                title.textContent = 'Belum ada Heat aktif';
                sub.textContent = 'Aktifkan salah satu Heat dari panel kiri untuk memulai perlombaan';
                live.style.display = 'none';
                tbody.innerHTML = `<tr><td colspan="4" class="empty-state" style="padding:30px;">
                    <span class="material-icons">pool</span> Aktifkan Heat untuk melihat peserta
                </td></tr>`;
            }
        });
    }

    // ===== IoT RESULTS =====
    let iotHidden = false;
    function loadIoTResults() {
        if (iotHidden) return;
        fetch('/results/data').then(r => r.json()).then(data => {
            const tbody = document.getElementById('iot-tbody');
            if (!data || data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="8" class="empty-state" style="padding:30px;">
                    <span class="material-icons">timer</span> Menunggu data dari alat IoT...
                </td></tr>`;
                return;
            }
            let html = '';
            data.forEach((item, idx) => {
                const format = item.waktu_format;
                const parts = format.split(':');
                let menit = '-', detik = '-', ms = '-';
                if (parts.length === 2) {
                    menit = parts[0];
                    const sub = parts[1].split('.');
                    detik = sub[0]; ms = sub[1] || '-';
                }
                const name = item.athlete_name || '<span style="color:var(--text-muted);">-</span>';
                html += `<tr>
                    <td>${idx + 1}</td>
                    <td><strong>${item.player}</strong></td>
                    <td style="text-align:left;">${name}</td>
                    <td>${menit}</td><td>${detik}</td><td>${ms}</td>
                    <td><strong style="color:var(--accent-green);">${format}</strong></td>
                    <td style="color:var(--text-secondary);font-size:0.8rem;">${item.timestamp}</td>
                </tr>`;
            });
            tbody.innerHTML = html;
        });
    }

    // ===== STOPWATCH =====
    let swStartTime = 0, swElapsed = 0, swRunning = false, swInterval = null;
    const swDisplay = document.getElementById('sw-display');
    const swWrap = document.getElementById('sw-wrap');
    const btnStart = document.getElementById('btn-sw-start');
    const btnStop = document.getElementById('btn-sw-stop');
    const btnReset = document.getElementById('btn-sw-reset');

    function formatSW(ms) {
        const totalSec = Math.floor(ms / 1000);
        const m = String(Math.floor(totalSec / 60)).padStart(2, '0');
        const s = String(totalSec % 60).padStart(2, '0');
        const millis = String(ms % 1000).padStart(3, '0');
        return `${m}:${s}<span class="sw-ms">.${millis}</span>`;
    }
    function updateSW() { swElapsed = Date.now() - swStartTime; swDisplay.innerHTML = formatSW(swElapsed); }

    btnStart.addEventListener('click', () => {
        swStartTime = Date.now() - swElapsed;
        swRunning = true;
        swInterval = setInterval(updateSW, 37);
        btnStart.style.display = 'none'; btnStop.style.display = 'flex'; btnReset.style.display = 'none';
        swWrap.classList.add('sw-running'); swWrap.classList.remove('sw-stopped');
    });
    btnStop.addEventListener('click', () => {
        clearInterval(swInterval); swRunning = false;
        btnStop.style.display = 'none'; btnStart.style.display = 'flex';
        btnStart.innerHTML = '<span class="material-icons">play_arrow</span> LANJUT';
        btnReset.style.display = 'flex';
        swWrap.classList.remove('sw-running'); swWrap.classList.add('sw-stopped');
    });
    btnReset.addEventListener('click', () => {
        clearInterval(swInterval); swRunning = false; swElapsed = 0;
        swDisplay.innerHTML = formatSW(0);
        btnStart.innerHTML = '<span class="material-icons">play_arrow</span> MULAI';
        btnStart.style.display = 'flex'; btnStop.style.display = 'none'; btnReset.style.display = 'none';
        swWrap.classList.remove('sw-running', 'sw-stopped');
    });

    // ===== CLEAR IOT (layar saja) =====
    document.getElementById('btn-clear-iot').addEventListener('click', () => {
        iotHidden = true;
        document.getElementById('iot-tbody').innerHTML = `<tr><td colspan="8" class="empty-state" style="padding:30px;">
            <span class="material-icons">timer</span> Layar dibersihkan. Menunggu data baru...
        </td></tr>`;
        setTimeout(() => { iotHidden = false; }, 3000);
    });

    // ===== RESET SEMUA WAKTU (hapus dari database) =====
    document.getElementById('btn-reset-db').addEventListener('click', () => {
        if (!confirm('⚠️ Hapus SEMUA data waktu dari database secara permanen?\n\nTindakan ini tidak bisa dibatalkan!')) return;
        fetch('/results/clear-all', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(d => {
            alert(d.message || '✅ Semua data berhasil dihapus!');
            loadIoTResults();
        })
        .catch(() => alert('❌ Gagal menghapus data!'));
    });

    // ===== EXPORT EXCEL =====
    document.getElementById('btn-export-excel').addEventListener('click', () => {
        const rows = document.querySelectorAll('#iot-tbody tr');
        if (rows.length === 0 || (rows.length === 1 && rows[0].querySelector('.empty-state'))) {
            alert('Tidak ada data untuk di-export!'); return;
        }

        // Ambil data aktif dari endpoint /results/data
        fetch('/results/data')
            .then(r => r.json())
            .then(data => {
                if (!data || data.length === 0) { alert('Tidak ada data!'); return; }

                const now = new Date();
                const tanggal = now.toLocaleDateString('id-ID', { weekday:'long', year:'numeric', month:'long', day:'numeric' });
                const activeTitle = document.getElementById('ahb-title').textContent;

                const sheetRows = [
                    ['Hasil Waktu Lomba Renang - IoT'],
                    [`Heat Aktif: ${activeTitle}`],
                    [`Tanggal: ${tanggal}`],
                    [],
                    ['No', 'Jalur', 'Nama Atlet', 'Waktu (Menit)', 'Waktu (Detik)', 'Waktu (MS)', 'Waktu Format']
                ];

                data.forEach((item, idx) => {
                    const fmt = item.waktu_format || '-';
                    const parts = fmt.split(':');
                    let menit = '-', detik = '-', ms = '-';
                    if (parts.length === 2) {
                        menit = parts[0];
                        const sub = parts[1].split('.');
                        detik = sub[0]; ms = sub[1] || '-';
                    }
                    sheetRows.push([
                        idx + 1,
                        item.player,
                        item.athlete_name || '-',
                        menit, detik, ms, fmt,
                        item.timestamp
                    ]);
                });

                const ws = XLSX.utils.aoa_to_sheet(sheetRows);
                ws['!cols'] = [{wch:4},{wch:6},{wch:25},{wch:13},{wch:13},{wch:10},{wch:15},{wch:22}];
                ws['!merges'] = [
                    {s:{r:0,c:0},e:{r:0,c:7}},
                    {s:{r:1,c:0},e:{r:1,c:7}},
                    {s:{r:2,c:0},e:{r:2,c:7}}
                ];

                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, 'Hasil IoT');
                const filename = `Hasil_Waktu_IoT_${now.toISOString().slice(0,10)}.xlsx`;
                XLSX.writeFile(wb, filename);
            })
        .catch(() => alert('❌ Gagal mengambil data untuk export!'));
    });

    // ===== INIT =====
    window.addEventListener('load', () => { loadActiveHeat(); loadIoTResults(); });
    setInterval(() => { loadActiveHeat(); loadIoTResults(); }, 2000);
</script>
</body>
</html>
