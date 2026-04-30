@extends('layouts.participant')
@section('title', 'Heat & Jalur Perlombaan')

@push('styles')
    <script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/heats.css') }}">
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
            <div class="panel-label">Panel Kendali Heat</div>
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
            <select id="select-ku" class="panel-select">
                <option value="">-- Semua Kelompok Umur --</option>
                <option value="KU I">KU I (10–12 Tahun)</option>
                <option value="KU II">KU II (13–14 Tahun)</option>
                <option value="KU III">KU III (15–17 Tahun)</option>
                <option value="KU IV">KU IV (18–24 Tahun)</option>
                <option value="Senior">Senior / Open (25+ Tahun)</option>
                <option value="Umum">Umum (di bawah 10 tahun)</option>
            </select>
            <button class="btn-load-heats" id="btn-load-heats">
                <span class="material-icons" style="font-size:1.1rem;">search</span> Tampilkan Heat
            </button>
            <button class="btn-load-heats mt-2" id="btn-generate-heats" style="background:var(--accent-orange); color:white;">
                <span class="material-icons" style="font-size:1.1rem;">auto_awesome</span> Update Heat
            </button>

            <hr style="border-color: #e2e8f0; margin: 14px 0;">
            <div class="panel-label">Aksi Massal</div>
            <button class="btn-gen-all" id="btn-generate-all">
                <span class="material-icons">rocket_launch</span> Generate Semua Event
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
            <div style="display:flex; justify-content:space-between; align-items:center; padding-right:18px;">
                <h5>🏊 Peserta di Kolam (Heat Aktif)</h5>
                <button class="btn-action-sm btn-action-green" id="btn-export-excel" title="Export ke Excel">
                    <span class="material-icons">table_view</span> Export Excel
                </button>
            </div>
            <table class="lane-table">
                <thead>
                    <tr>
                        <th style="width:40px; text-align:center;">Jalur</th>
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
                <h5>⏱️ Hasil Waktu Lomba (Real-time)</h5>
                <div style="display:flex; gap:8px; align-items:center;">
                    <button class="btn-action-sm btn-action-blue" id="btn-reset-db" title="Race Ulang">
                        <span class="material-icons">refresh</span> Race Ulang
                    </button>
                    <button class="btn-action-sm btn-action-red" id="btn-end-session" title="Akhiri sesi dan selesaikan Heat">
                        <span class="material-icons">flag</span> Akhiri Sesi
                    </button>
                </div>
            </div>
            <div class="iot-scroll">
                <table class="iot-table">
                    <thead>
                            <th style="width:6%;">No</th>
                            <th style="width:6%;">Player</th>
                            <th style="width:12%; text-align:center;">Waktu (menit)</th>
                            <th style="width:12%; text-align:center;">Waktu (detik)</th>
                            <th style="width:12%; text-align:center;">Waktu (ms)</th>
                            <th style="width:20%; text-align:center;">Waktu Format</th>
                            <th style="width:26%; text-align:center;">Waktu Input</th>
                        </tr>
                    </thead>
                    <tbody id="iot-tbody">
                        <tr><td colspan="7" class="empty-state" style="padding:30px;">
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

<!-- ===== MODAL KONFIRMASI AKHIRI SESI ===== -->
<div class="modal fade" id="modal-end-session" tabindex="-1" aria-labelledby="modalEndSessionLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:40px;height:40px;border-radius:50%;background:#e0f2fe;display:flex;align-items:center;justify-content:center;">
                        <span class="material-icons" style="color:#0284c7;font-size:1.3rem;">flag</span>
                    </div>
                    <h6 class="modal-title fw-bold mb-0" id="modalEndSessionLabel">Akhiri Sesi Ini?</h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <p class="mb-2" style="font-size:0.95rem;">Apakah Anda yakin ingin menyelesaikan Heat yang sedang aktif?</p>
                <div class="alert alert-info py-2 px-3 m-0 d-flex gap-2" style="font-size:0.82rem;border-radius:8px; line-height: 1.4;">
                    <span class="material-icons" style="font-size:1.2rem; flex-shrink:0;">info</span>
                    <div>Tabel dan Daftar Peserta akan dikosongkan. Hasil akan tetap tersimpan.</div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn text-white px-4" id="btn-konfirmasi-end-session" style="background-color:#0284c7;">
                    Selesaikan Heat
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

<!-- ===== MODAL KONFIRMASI TANDAI SELESAI ===== -->
<div class="modal fade" id="modal-tandai-selesai" tabindex="-1" aria-labelledby="modalTandaiSelesaiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:40px;height:40px;border-radius:50%;background:#e0f2fe;display:flex;align-items:center;justify-content:center;">
                        <span class="material-icons" style="color:#0284c7;font-size:1.3rem;">check_circle</span>
                    </div>
                    <h6 class="modal-title fw-bold mb-0" id="modalTandaiSelesaiLabel">Tandai Heat Selesai?</h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <p class="mb-2" style="font-size:0.95rem;">Apakah Anda yakin ingin menandai Heat ini sebagai selesai?</p>
                <div class="alert alert-info py-2 px-3 m-0 d-flex gap-2" style="font-size:0.82rem;border-radius:8px; line-height: 1.4;">
                    <span class="material-icons" style="font-size:1.2rem; flex-shrink:0;">info</span>
                    <div>Data waktu dari alat akan disimpan secara permanen untuk Heat ini dan tabel akan dikosongkan.</div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn text-white px-4" id="btn-konfirmasi-tandai-selesai" style="background-color:#0284c7;">
                    Ya, Selesai
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

<!-- ===== MODAL KONFIRMASI GENERATE SEMUA ===== -->
<div class="modal fade" id="modal-generate-all" tabindex="-1" aria-labelledby="modalGenerateAllLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg, #1e293b, #334155);display:flex;align-items:center;justify-content:center;">
                        <span class="material-icons" style="color:#f59e0b;font-size:1.3rem;">rocket_launch</span>
                    </div>
                    <h6 class="modal-title fw-bold mb-0" id="modalGenerateAllLabel">Generate Semua Event</h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <p class="mb-2" style="font-size:0.95rem;">Anda akan men-generate ulang <strong>SEMUA</strong> Heat untuk seluruh event dan gender sekaligus.</p>
                <div class="alert alert-warning py-2 px-3 m-0 d-flex gap-2" style="font-size:0.82rem;border-radius:8px; line-height: 1.4;">
                    <span class="material-icons" style="font-size:1.2rem; flex-shrink:0;">warning</span>
                    <div>Proses ini akan <strong>menghapus semua Heat lama</strong> dan membuat Heat baru berdasarkan daftar peserta terbaru. Pastikan data atlet sudah lengkap!</div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal" style="border-radius:8px; font-weight:600;">Batal</button>
                <button type="button" class="btn text-white px-4" id="btn-konfirmasi-generate-all" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%); border-radius:8px; font-weight:600;">
                    <span class="material-icons me-1" style="font-size:1rem; vertical-align:middle; color:#f59e0b;">rocket_launch</span> Ya, Generate Semua
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ===== FULLSCREEN OVERLAY PROGRESS ===== -->
<div class="gen-all-overlay" id="gen-all-overlay">
    <div class="gen-all-card" id="gen-all-card">
        <!-- Processing State -->
        <div id="gen-all-processing">
            <div class="gen-all-icon">
                <span class="material-icons">auto_awesome</span>
            </div>
            <div class="gen-all-title">Sedang Men-generate Heat...</div>
            <div class="gen-all-subtitle">Mohon tunggu, semua event sedang diproses secara otomatis.</div>
            <div class="gen-all-progress-wrap">
                <div class="gen-all-progress-bar" id="gen-all-progress-bar"></div>
            </div>
            <div class="gen-all-status" id="gen-all-status">Mempersiapkan data...</div>
        </div>
        <!-- Done State (hidden by default) -->
        <div id="gen-all-done" style="display:none;">
            <div class="gen-all-done-icon">
                <span class="material-icons">check_circle</span>
            </div>
            <div class="gen-all-title" style="color:#16a34a;">Generate Selesai! 🎉</div>
            <div class="gen-all-subtitle" id="gen-all-result-text">Semua heat berhasil dibuat.</div>
            <div id="gen-all-details" style="text-align:left; background:#f8fafc; border-radius:12px; padding:14px 16px; margin-bottom:20px; max-height:160px; overflow-y:auto;"></div>
            <button class="btn btn-success px-5 py-2" id="btn-gen-all-close" style="border-radius:10px; font-weight:700; font-size:0.95rem;">Tutup</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/heats.js') }}"></script>
@endpush
