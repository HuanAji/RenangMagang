@extends('layouts.participant')
@section('title', 'Heat & Jalur Perlombaan')

@push('styles')
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

        /* ===== LAYOUT ===== */
        .op-layout {
            display: grid;
            grid-template-columns: 330px 1fr;
            gap: 20px;
            height: calc(100vh - 165px); /* Membutuhkan height statis agar sisi dalam bisa overlow */
            align-items: start;
        }

        /* ===== LEFT PANEL ===== */
        .op-panel {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            overflow-y: auto;
            padding: 18px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
            height: 100%;
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
        .op-main { 
            overflow-y: auto; 
            background: transparent; 
            height: 100%;
            padding-right: 5px;
        }

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
    </style>
@endpush

@section('breadcrumb')
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom w-100">
        <h5 class="fw-bold mb-0" style="color: #003399;">
            <span class="material-icons me-2" style="vertical-align: middle;">pool</span>Heat & Jalur Perlombaan
        </h5>
        <div style="font-size: 0.82rem; color: #666; font-weight: 500;">
            <span class="material-icons" style="font-size:0.9rem; vertical-align:middle; color:#003399;">schedule</span>
            <span id="clock" class="ms-1"></span>
        </div>
    </div>
@endsection

@section('content')
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
            <button class="btn-load-heats mt-2" id="btn-generate-heats" style="background:var(--accent-orange); color:white;">
                <span class="material-icons" style="font-size:1.1rem;">auto_awesome</span> Generate/Update Heat
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
                <span class="live-tag">
                    <span class="live-dot" style="width:7px;height:7px;background:white;border-radius:50%;animation:pulse 1.5s infinite;"></span> LIVE
                </span>
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
                        <span class="material-icons" style="font-size:0.95rem;">delete_sweep</span> Reset Semua Waktu
                    </button>
                    <button class="btn-clear-iot" id="btn-export-excel" title="Export ke Excel" style="background:var(--accent-green-light); color:var(--accent-green); border-color:rgba(22,163,74,0.2);">
                        <span class="material-icons" style="font-size:0.95rem;">table_view</span> Export Excel
                    </button>
                    <button class="btn-clear-iot" id="btn-clear-iot" title="Bersihkan tampilan layar saja">
                        <span class="material-icons" style="font-size:0.95rem;">cleaning_services</span> Bersihkan Layar
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

<!-- Modal Konfirmasi Generate Heat -->
<div class="modal fade" id="modal-generate-heat" tabindex="-1" aria-labelledby="modalGenerateHeatLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:40px;height:40px;border-radius:50%;background:#fff7ed;display:flex;align-items:center;justify-content:center;">
                        <span class="material-icons" style="color:var(--accent-orange);font-size:1.3rem;">auto_awesome</span>
                    </div>
                    <h6 class="modal-title fw-bold mb-0" id="modalGenerateHeatLabel">Konfirmasi Generate Heat</h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <p class="mb-2" style="font-size:0.95rem;">Apakah Anda yakin ingin melakukan generate ulang Heat?</p>
                <div class="alert alert-warning py-2 px-3 m-0 d-flex gap-2" style="font-size:0.82rem;border-radius:8px; line-height: 1.4;">
                    <span class="material-icons" style="font-size:1.2rem; flex-shrink:0;">warning</span>
                    <div>Semua status dan jadwal untuk event kategori ini akan direset ulang dan diperbarui berdasarkan daftar peserta terakhir!</div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn text-white px-4" id="btn-konfirmasi-generate" style="background-color:var(--accent-orange);">
                    Generate Sekarang
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ===== MODAL KONFIRMASI RESET SESI ===== -->
<div class="modal fade" id="modal-reset-sesi" tabindex="-1" aria-labelledby="modalResetSesiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:40px;height:40px;border-radius:50%;background:#fff1f0;display:flex;align-items:center;justify-content:center;">
                        <span class="material-icons" style="color:#dc2626;font-size:1.3rem;">restart_alt</span>
                    </div>
                    <h6 class="modal-title fw-bold mb-0" id="modalResetSesiLabel">Reset Waktu Sesi Terakhir</h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-2">
                <p class="mb-2" style="font-size:0.95rem;">Hapus data waktu dari <strong>batch/heat terakhir</strong> saja?</p>
                <div class="alert alert-warning py-2 px-3 d-flex gap-2 m-0" style="font-size:0.82rem;border-radius:8px;">
                    <span class="material-icons" style="font-size:1.2rem;flex-shrink:0;">info</span>
                    <div>Data heat-heat <strong>sebelumnya tetap aman</strong>. Gunakan fitur ini jika ada tanding ulang akibat kecurangan atau gagal start.</div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn text-white px-4" id="btn-konfirmasi-reset-sesi" style="background-color:#dc2626;">
                    <span class="material-icons me-1" style="font-size:1rem;vertical-align:middle;">delete_sweep</span> Reset Sesi Ini
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ===== MODAL KONFIRMASI EXPORT EXCEL ===== -->
<div class="modal fade" id="modal-export-excel" tabindex="-1" aria-labelledby="modalExportExcelLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:40px;height:40px;border-radius:50%;background:#dcfce7;display:flex;align-items:center;justify-content:center;">
                        <span class="material-icons" style="color:#16a34a;font-size:1.3rem;">table_view</span>
                    </div>
                    <h6 class="modal-title fw-bold mb-0" id="modalExportExcelLabel">Konfirmasi Export Excel</h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <p class="mb-2" style="font-size:0.95rem;">Anda akan mendownload hasil waktu renang yang ditarik dari alat IoT.</p>
                <div class="p-3 mb-1" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px;">
                    <div style="font-size:0.8rem; color:#64748b; margin-bottom:4px;">Heat yang akan diexport:</div>
                    <div id="export-modal-filename" style="font-weight:600; color:#0f172a; word-break:break-word;">(Memuat data...)</div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn text-white px-4" id="btn-konfirmasi-export" style="background-color:#16a34a;">
                    <span class="material-icons me-1" style="font-size:1rem;vertical-align:middle;">download</span> Download Excel
                </button>
            </div>
        </div>
    </div>
</div>
<!-- ===== TOAST NOTIFIKASI ===== -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100;">
    <div id="app-toast" class="toast align-items-center border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2" id="toast-body">
                <span class="material-icons" id="toast-icon" style="font-size:1.2rem;"></span>
                <span id="toast-msg"></span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;

    // ===== TOAST HELPER =====
    function showToast(msg, type = 'success') {
        const toastEl  = document.getElementById('app-toast');
        const toastMsg = document.getElementById('toast-msg');
        const toastIcon = document.getElementById('toast-icon');

        toastEl.className = 'toast align-items-center border-0 shadow text-white';
        if (type === 'success') {
            toastEl.classList.add('bg-success');
            toastIcon.textContent = 'check_circle';
        } else if (type === 'error') {
            toastEl.classList.add('bg-danger');
            toastIcon.textContent = 'error';
        } else {
            // warning
            toastEl.className = 'toast align-items-center border-0 shadow bg-warning text-dark';
            toastIcon.textContent = 'warning';
        }

        toastMsg.textContent = msg;
        const bsToast = bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 3500 });
        bsToast.show();
    }

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

    // ===== LOAD & GENERATE HEATS =====
    document.getElementById('btn-load-heats').addEventListener('click', loadHeats);

    document.getElementById('btn-generate-heats').addEventListener('click', () => {
        const eventId = document.getElementById('select-event').value;
        const gender = document.getElementById('select-gender').value;
        if (!eventId || !gender) { showToast('Pilih event dan jenis kelamin sebelum membuat Heat!', 'warning'); return; }
        
        const bsModal = new bootstrap.Modal(document.getElementById('modal-generate-heat'));
        bsModal.show();
    });

    document.getElementById('btn-konfirmasi-generate').addEventListener('click', () => {
        const eventId = document.getElementById('select-event').value;
        const gender = document.getElementById('select-gender').value;
        
        const modalEl = document.getElementById('modal-generate-heat');
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if(modalInstance) modalInstance.hide();

        fetch('/heats/regenerate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ event_id: eventId, jenis_kelamin: gender })
        })
        .then(r => r.json())
        .then(data => {
            showToast(data.message || 'Heat berhasil di-generate!', 'success');
            loadHeats();
        })
        .catch(err => showToast('Gagal generate heat: ' + err.message, 'error'));
    });

    function loadHeats() {
        const eventId = document.getElementById('select-event').value;
        const gender = document.getElementById('select-gender').value;
        if (!eventId || !gender) { showToast('Pilih event dan jenis kelamin terlebih dahulu!', 'warning'); return; }
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

            // Semua heat bisa dipilih/diaktifkan bebas
            let actionHtml = '';
            if (heat.status === 'active') {
                actionHtml = `<button class="btn-heat btn-complete" onclick="completeHeat(${heat.id})">
                    <span class="material-icons">stop</span> Tandai Selesai
                </button>`;
            } else {
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
    window.activateHeat = function(id) {
        fetch(`/heats/${id}/activate`, {
            method:'POST', headers:{'X-CSRF-TOKEN': CSRF, 'Accept':'application/json'}
        }).then(r => r.json()).then(() => { loadHeats(); loadActiveHeat(); });
    }
    window.completeHeat = function(id) {
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

    // ===== RESET SESI TERAKHIR (hanya hapus data batch/heat terakhir) =====
    document.getElementById('btn-reset-db').addEventListener('click', () => {
        const bsModal = new bootstrap.Modal(document.getElementById('modal-reset-sesi'));
        bsModal.show();
    });

    document.getElementById('btn-konfirmasi-reset-sesi').addEventListener('click', () => {
        bootstrap.Modal.getInstance(document.getElementById('modal-reset-sesi'))?.hide();

        fetch('/results/clear-last-session', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(d => {
            showToast(d.message || '✅ Data sesi terakhir berhasil dihapus!', 'success');
            loadIoTResults();
        })
        .catch(() => showToast('❌ Gagal menghapus data!', 'error'));
    });

    // ===== EXPORT EXCEL =====
    document.getElementById('btn-export-excel').addEventListener('click', () => {
        const rows = document.querySelectorAll('#iot-tbody tr');
        if (rows.length === 0 || (rows.length === 1 && rows[0].querySelector('.empty-state'))) {
            showToast('Tidak ada data untuk di-export!', 'warning'); return;
        }

        // Tampilkan dulu teks di modal apa yang akan terdownload
        const activeTitle  = document.getElementById('ahb-title').textContent.trim();
        const genderSelect = document.getElementById('select-gender');
        const genderText   = genderSelect ? (genderSelect.value === 'L' ? 'Putra' : genderSelect.value === 'P' ? 'Putri' : 'Semua') : 'Semua';
        const eventSelect  = document.getElementById('select-event');
        const eventText    = eventSelect ? (eventSelect.options[eventSelect.selectedIndex]?.text || 'Event') : 'Event';
        const eventClean   = eventText.replace(/^\d+\s*[\-—]\s*/, '').trim();
        const heatMatch    = activeTitle.match(/Heat\s*(\d+)/i);
        const heatLabel    = heatMatch ? `Heat ${heatMatch[1]}` : (activeTitle || 'Heat');
        
        const fileTitle = activeTitle && activeTitle !== 'Belum ada Heat aktif' ? activeTitle : `${eventClean} — ${genderText} — ${heatLabel}`;
        
        document.getElementById('export-modal-filename').textContent = fileTitle;
        
        const exportModal = new bootstrap.Modal(document.getElementById('modal-export-excel'));
        exportModal.show();
    });

    // Proses download setelah konfirmasi
    document.getElementById('btn-konfirmasi-export').addEventListener('click', () => {
        bootstrap.Modal.getInstance(document.getElementById('modal-export-excel'))?.hide();
        
        fetch('/results/data')
            .then(r => r.json())
            .then(data => {
                if (!data || data.length === 0) { showToast('Tidak ada data yang tersedia!', 'warning'); return; }

                const activeTitle  = document.getElementById('ahb-title').textContent.trim();
                const genderSelect = document.getElementById('select-gender');
                const genderText   = genderSelect ? (genderSelect.value === 'L' ? 'Putra' : genderSelect.value === 'P' ? 'Putri' : 'Semua') : 'Semua';
                const eventSelect  = document.getElementById('select-event');
                const eventText    = eventSelect ? (eventSelect.options[eventSelect.selectedIndex]?.text || 'Event') : 'Event';
                const eventClean   = eventText.replace(/^\d+\s*[\-—]\s*/, '').trim();
                const heatMatch    = activeTitle.match(/Heat\s*(\d+)/i);
                const heatLabel    = heatMatch ? `Heat ${heatMatch[1]}` : (activeTitle || 'Heat');
                
                const fileTitle = activeTitle && activeTitle !== 'Belum ada Heat aktif' ? activeTitle : `${eventClean} — ${genderText} — ${heatLabel}`;

                const sortedData   = [...data].sort((a, b) => (a.waktu_ms || 0) - (b.waktu_ms || 0));
                const now     = new Date();
                const tanggal = now.toLocaleDateString('id-ID', { weekday:'long', year:'numeric', month:'long', day:'numeric' });

                const sheetRows = [
                    [`Hasil Waktu Lomba Renang — ${fileTitle}`],
                    [`${heatLabel}`],
                    [`Tanggal: ${tanggal}`],
                    [],
                    ['Rank', 'No. Jalur', 'Nama Atlet', 'Waktu (Menit)', 'Waktu (Detik)', 'Waktu (MS)', 'Waktu Format']
                ];

                sortedData.forEach((item, idx) => {
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
                        menit, detik, ms, fmt
                    ]);
                });

                const ws = XLSX.utils.aoa_to_sheet(sheetRows);
                ws['!cols'] = [{wch:5},{wch:8},{wch:25},{wch:13},{wch:13},{wch:10},{wch:15}];
                ws['!merges'] = [
                    {s:{r:0,c:0},e:{r:0,c:6}},
                    {s:{r:1,c:0},e:{r:1,c:6}},
                    {s:{r:2,c:0},e:{r:2,c:6}}
                ];

                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, 'Hasil Lomba');

                const filename = `${fileTitle}.xlsx`;
                XLSX.writeFile(wb, filename);
                showToast(`File "${filename}" berhasil didownload!`, 'success');
            })
        .catch(() => showToast('❌ Gagal mengambil data untuk export!', 'error'));
    });

    // ===== INIT =====
    window.addEventListener('load', () => { loadActiveHeat(); loadIoTResults(); });
    setInterval(() => { loadActiveHeat(); loadIoTResults(); }, 2000);
</script>
@endpush
