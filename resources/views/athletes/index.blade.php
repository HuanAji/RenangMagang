<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Peserta - Renang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #4c67e3ff 100%);
            min-height: 100vh;
        }
        .container-lg {
            margin-top: 30px;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .table-responsive {
            border-radius: 12px;
            overflow: hidden;
        }
        .table {
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }
        .table thead tr:first-child th:first-child {
            border-top-left-radius: 12px;
        }
        .table thead tr:first-child th:last-child {
            border-top-right-radius: 12px;
        }
        .table tbody tr:last-child td:first-child {
            border-bottom-left-radius: 12px;
        }
        .table tbody tr:last-child td:last-child {
            border-bottom-right-radius: 12px;
        }
    </style>
</head>
<body>

    <div class="container-lg">

        <!-- ====================================================== -->
        <!-- HASIL WAKTU TIMEKEEPER (dari Arduino/IoT) -->
        <!-- ====================================================== -->
        <div class="mb-4">
            <div class="card">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold" style="color: #333;">
                        <span class="material-icons" style="vertical-align: middle; color: #667eea;">speed</span>
                        Hasil Waktu Timekeeper
                    </h5>
                    <div class="d-flex align-items-center gap-3">
                        <span id="iot-status-badge" class="badge bg-secondary" style="font-size: 0.78rem;">
                            <span class="material-icons" style="font-size:0.85rem; vertical-align: middle;">sync</span>
                            Memuat...
                        </span>
                        <button id="btn-clear-results" class="btn btn-sm btn-outline-danger">
                            <span class="material-icons" style="font-size:0.9rem; vertical-align:middle;">delete_sweep</span> Hapus Semua
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover mb-0" id="hasil-iot-table">
                        <thead style="background: #222; color: white;">
                            <tr>
                                <th class="text-center" style="background:#222; color:white; width:5%;">No</th>
                                <th class="text-center" style="background:#222; color:white; width:10%;">Player</th>
                                <th class="text-center" style="background:#222; color:white;">Waktu (menit)</th>
                                <th class="text-center" style="background:#222; color:white;">Waktu (detik)</th>
                                <th class="text-center" style="background:#222; color:white;">Waktu (ms)</th>
                                <th class="text-center" style="background:#222; color:white;">Waktu Format</th>
                                <th class="text-center" style="background:#222; color:white;">Waktu Input</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-hasil-iot">
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <span class="material-icons d-block mb-2" style="font-size: 2rem; color: #ccc;">hourglass_empty</span>
                                    Menunggu data dari Arduino...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ====================================================== -->
        <!-- DAFTAR PESERTA TERDAFTAR -->
        <!-- ====================================================== -->
        <div class="mb-4">
            <h1 class="text-white mb-2">Daftar Peserta Terdaftar</h1>
            <button id="btn-add-peserta" class="btn btn-success">
                <span class="material-icons" style="vertical-align: middle; margin-right: 5px;">person_add</span> Tambah Peserta
            </button>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th class="text-center">Umur</th>
                            <th class="text-center">Jenis Kelamin</th>
                            <th>Asal Klub/Sekolah</th>
                            <th>Event</th>
                            <th class="text-center" style="width:110px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-peserta">
                        @forelse($athletes as $index => $athlete)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $athlete->nama }}</td>
                            <td class="text-center">{{ $athlete->umur ? $athlete->umur . ' Tahun' : ($athlete->tanggal_lahir ? \Carbon\Carbon::parse($athlete->tanggal_lahir)->age . ' Tahun' : '-') }}</td>
                            <td class="text-center">{{ $athlete->jenis_kelamin === 'L' ? 'Laki-laki' : ($athlete->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}</td>
                            <td>{{ $athlete->asal_club_sekolah ?? '-' }}</td>
                            <td>
                                @foreach($athlete->registrations as $reg)
                                    <span class="badge bg-info">{{ $reg->event->nama_event }}</span>
                                @endforeach
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <button class="btn btn-sm text-white p-1 btn-edit-athlete"
                                        data-id="{{ $athlete->id }}"
                                        data-nama="{{ $athlete->nama }}"
                                        data-umur="{{ $athlete->umur }}"
                                        data-jenis_kelamin="{{ $athlete->jenis_kelamin }}"
                                        data-asal="{{ $athlete->asal_club_sekolah }}"
                                        style="background-color:#1890ff;" title="Edit">
                                        <span class="material-icons" style="font-size:1.05rem;">edit</span>
                                    </button>
                                    <button class="btn btn-sm text-white p-1 btn-delete-athlete"
                                        data-id="{{ $athlete->id }}"
                                        data-nama="{{ $athlete->nama }}"
                                        style="background-color:#ff4d4f;" title="Hapus">
                                        <span class="material-icons" style="font-size:1.05rem;">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">Belum ada peserta terdaftar</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ===== TOAST NOTIFIKASI ===== -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1100;">
        <div id="app-toast" class="toast align-items-center border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <span class="material-icons" id="toast-icon" style="font-size:1.2rem;"></span>
                    <span id="toast-msg"></span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    <!-- ===== MODAL EDIT ATLET ===== -->
    <div class="modal fade" id="modal-edit-atlet" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0">
                    <h6 class="modal-title fw-bold">✏️ Edit Data Atlet</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit-atlet-id">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:0.85rem;">Nama Atlet</label>
                        <input type="text" id="edit-nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:0.85rem;">Umur</label>
                        <input type="number" id="edit-umur" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:0.85rem;">Jenis Kelamin</label>
                        <select id="edit-jenis_kelamin" class="form-select">
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:0.85rem;">Asal Klub/Sekolah</label>
                        <input type="text" id="edit-asal" class="form-control">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn text-white px-4" id="btn-simpan-edit" style="background-color:#1890ff;">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== MODAL KONFIRMASI HAPUS ===== -->
    <div class="modal fade" id="modal-hapus-atlet" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:38px;height:38px;border-radius:50%;background:#fff1f0;display:flex;align-items:center;justify-content:center;">
                            <span class="material-icons" style="color:#ff4d4f;font-size:1.2rem;">delete_outline</span>
                        </div>
                        <h6 class="modal-title fw-bold mb-0">Konfirmasi Hapus Atlet</h6>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p style="font-size:0.93rem;">Yakin hapus atlet: <strong id="hapus-nama-atlet" style="color:#003399;"></strong>?</p>
                    <div class="alert alert-warning py-2 px-3 d-flex align-items-center gap-2 mb-0" style="font-size:0.82rem;">
                        <span class="material-icons" style="font-size:1rem;">info</span>
                        Registrasi &amp; track record juga akan ikut terhapus!
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn text-white px-4" id="btn-konfirmasi-hapus" style="background-color:#ff4d4f;">
                        <span class="material-icons me-1" style="font-size:1rem;vertical-align:middle;">delete</span>Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ====================================================== -->
    <!-- RACE TIMING SECTION -->
    <!-- ====================================================== -->
    <div class="card mt-4" id="race-timing-section">
        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 fw-bold" style="color: #333;">
                <span class="material-icons" style="vertical-align: middle; color: #667eea;">timer</span>
                Pencatatan Waktu Lomba (1 Heat)
            </h5>
        </div>
        <div class="card-body">
            <!-- Heat Selector -->
            <div class="row g-3 mb-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-bold" style="font-size:0.85rem;">Nomor Event / Lomba</label>
                    <select id="race-event" class="form-select">
                        <option value="" disabled selected>-- Pilih Event --</option>
                        @foreach($events as $evt)
                            <option value="{{ $evt->id }}">{{ str_pad($evt->id, 3, '0', STR_PAD_LEFT) }} - {{ $evt->nama_event }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold" style="font-size:0.85rem;">Jenis Kelamin</label>
                    <select id="race-gender" class="form-select">
                        <option value="" disabled selected>-- Pilih --</option>
                        <option value="L">Putra</option>
                        <option value="P">Putri</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold" style="font-size:0.85rem;">Pilih Heat</label>
                    <select id="race-heat" class="form-select" disabled>
                        <option value="" disabled selected>-- Pilih Heat --</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn w-100" id="btn-load-race" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; font-weight: 600;">
                        <span class="material-icons" style="font-size:1rem; vertical-align:middle;">download</span> Muat
                    </button>
                </div>
            </div>

            <!-- Master Stopwatch -->
            <div id="race-sw-wrap" class="text-center py-3 mb-3 rounded-3" style="background: #f8f9fa; border: 2px solid #e9ecef;">
                <div style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1.5px; color: #888; font-weight: 600;">⏱️ Stopwatch Heat</div>
                <div id="race-sw-display" style="font-size: 3.5rem; font-weight: 800; font-family: 'Inter', monospace; color: #333; letter-spacing: 2px; margin: 6px 0 12px;">
                    00:00<span style="font-size: 2.2rem; opacity: 0.5;">.000</span>
                </div>
                <div class="d-flex gap-2 justify-content-center">
                    <button class="btn btn-success px-4 fw-bold" id="btn-race-start">
                        <span class="material-icons" style="font-size:1.1rem; vertical-align:middle;">play_arrow</span> START
                    </button>
                    <button class="btn btn-danger px-4 fw-bold" id="btn-race-finish" style="display:none;">
                        <span class="material-icons" style="font-size:1.1rem; vertical-align:middle;">flag</span> FINISH
                    </button>
                    <button class="btn btn-outline-secondary px-4 fw-bold" id="btn-race-reset" style="display:none;">
                        <span class="material-icons" style="font-size:1.1rem; vertical-align:middle;">replay</span> RESET
                    </button>
                </div>
            </div>

            <!-- Timing Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0" id="race-timing-table">
                    <thead class="table-light">
                        <tr>
                            <th style="text-align:center; width:5%;">Jalur</th>
                            <th style="width:30%;">Nama Peserta</th>
                            <th style="text-align:center; width:10%;">Waktu (menit)</th>
                            <th style="text-align:center; width:10%;">Waktu (detik)</th>
                            <th style="text-align:center; width:10%;">Waktu (ms)</th>
                            <th style="text-align:center; width:18%;">Waktu Format</th>
                            <th style="text-align:center; width:17%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="race-timing-tbody">
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <span class="material-icons d-block mb-2" style="font-size: 2.5rem; color: #ccc;">pool</span>
                                Pilih Event, Gender, dan Heat di atas, lalu klik "Muat"
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex gap-2 mt-3 justify-content-end" id="race-action-buttons" style="display:none !important;">
                <button class="btn btn-outline-danger" id="btn-race-clear">
                    <span class="material-icons" style="font-size:1rem; vertical-align:middle;">delete_sweep</span> Reset Semua Waktu
                </button>
                <button class="btn btn-success" id="btn-race-print">
                    <span class="material-icons" style="font-size:1rem; vertical-align:middle;">print</span> Export Excel
                </button>
            </div>
        </div>
    </div>

    </div><!-- end container-lg -->

    <!-- Modal Tambah Peserta -->
    <style>
        .custom-modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            align-items: center;
            justify-content: center;
            z-index: 1050;
        }
        .radio-group-custom .btn-check:checked + .btn {
            background-color: #003399;
            color: white;
            border-color: #003399;
        }
        .radio-group-custom .btn {
            border-color: #e0e6ed;
            color: #666;
        }
        .radio-group-custom .btn:hover { background-color: #f8f9fa; }
        .radio-group-custom .btn-check:checked + .btn:hover { background-color: #002266; }
    </style>

    <div id="modal-peserta" class="custom-modal-overlay">
        <div class="bg-white p-4 rounded shadow" style="width:100%;max-width:550px;max-height:90vh;overflow-y:auto; position: relative;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0 text-secondary">Tambah Atlet</h5>
                <button type="button" class="btn-close" style="font-size: 0.8rem;" aria-label="Close" id="btn-cancel-peserta"></button>
            </div>

            <form id="form-peserta" method="POST" action="{{ route('athletes.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label" style="font-size: 0.85rem; color: #555;">Nama Atlet</label>
                    <input type="text" name="nama" class="form-control" placeholder="Nama Atlet" required style="border-color: #b3d4ff; font-size: 0.9rem;">
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-size: 0.85rem; color: #555;">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-control" required style="border-color: #e0e6ed; font-size: 0.9rem;">
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-size: 0.85rem; color: #555;">Asal Klub / Sekolah</label>
                    <div class="d-flex gap-2">
                        <select id="select-asal-klub" name="asal_club_sekolah" class="form-select text-muted" required style="border-color: #e0e6ed; font-size: 0.9rem;">
                            <option value="" disabled selected>Pilih Asal Sekolah / Klub</option>
                            <option value="Samator Swimming Club">Samator Swimming Club</option>
                            <option value="Universitas Gadjah Mada">Universitas Gadjah Mada</option>
                            <option value="Universitas Negeri Yogyakarta">Universitas Negeri Yogyakarta</option>
                        </select>
                        <button type="button" id="btn-tambah-klub" class="btn text-white px-3" style="background-color: #52c41a;" title="Tambah sekolah/klub baru">
                            <span class="material-icons" id="icon-tambah-klub" style="font-size: 1.1rem; vertical-align: middle;">add</span>
                        </button>
                    </div>
                    <!-- Input tambah klub baru (tersembunyi) -->
                    <div id="wrap-input-klub" class="d-flex gap-2 mt-2" style="display:none !important;">
                        <input type="text" id="input-klub-baru" class="form-control" placeholder="Ketik nama sekolah / klub baru..." style="font-size: 0.9rem; border-color: #52c41a;">
                        <button type="button" id="btn-konfirm-klub" class="btn text-white px-3" style="background-color: #003399; white-space: nowrap; font-size: 0.85rem;">
                            <span class="material-icons" style="font-size: 1rem; vertical-align: middle;">check</span> Tambahkan
                        </button>
                    </div>
                    <small id="msg-klub" class="text-success" style="font-size:0.8rem; display:none;"></small>
                </div>

                <div class="mb-1">
                    <label class="form-label mb-2" style="font-size: 0.85rem; color: #555;">Jenis Kelamin</label>
                    <div class="row g-0 radio-group-custom border rounded overflow-hidden">
                        <div class="col-6">
                            <input type="radio" class="btn-check" name="jenis_kelamin" id="jk_laki" value="L" autocomplete="off" required>
                            <label class="btn w-100 rounded-0 border-0 m-0 py-2 d-flex justify-content-center align-items-center" for="jk_laki" style="font-size: 0.9rem;">
                                <span class="material-icons me-2" style="font-size: 1.1rem;">male</span> Laki-laki
                            </label>
                        </div>
                        <div class="col-6" style="border-left: 1px solid #e0e6ed;">
                            <input type="radio" class="btn-check" name="jenis_kelamin" id="jk_perempuan" value="P" autocomplete="off" required>
                            <label class="btn w-100 rounded-0 border-0 m-0 py-2 d-flex justify-content-center align-items-center" for="jk_perempuan" style="font-size: 0.9rem;">
                                <span class="material-icons me-2" style="font-size: 1.1rem;">female</span> Perempuan
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mb-4 mt-2">
                    <small class="text-danger" style="font-size: 0.8rem;">*Pastikan ulang semua data yang diinputkan telah benar!</small>
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-size: 0.85rem; color: #555;">Pilih Event</label>
                    <div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                        @foreach($events as $event)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="event_id[]" value="{{ $event->id }}" id="event_{{ $event->id }}">
                            <label class="form-check-label" for="event_{{ $event->id }}">
                                {{ $event->nama_event }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="text-end mt-2">
                    <button type="submit" class="btn px-4" style="background-color: #003399; color: white; font-weight: 500;">Kirim</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>
    <script>
        // ===== MODAL PESERTA =====
        const btn = document.getElementById('btn-add-peserta');
        const modal = document.getElementById('modal-peserta');
        const cancelBtn = document.getElementById('btn-cancel-peserta');
        const form = document.getElementById('form-peserta');

        btn.addEventListener('click', () => { modal.style.display = 'flex'; });
        cancelBtn.addEventListener('click', () => { modal.style.display = 'none'; form.reset(); resetTambahKlub(); });
        // Close on backdrop click
        modal.addEventListener('click', (e) => { if (e.target === modal) { modal.style.display = 'none'; form.reset(); resetTambahKlub(); } });

        // ===== TOMBOL TAMBAH KLUB =====
        const btnTambahKlub = document.getElementById('btn-tambah-klub');
        const wrapInputKlub = document.getElementById('wrap-input-klub');
        const inputKlubBaru = document.getElementById('input-klub-baru');
        const btnKonfirmKlub = document.getElementById('btn-konfirm-klub');
        const selectKlub = document.getElementById('select-asal-klub');
        const iconTambah = document.getElementById('icon-tambah-klub');
        const msgKlub = document.getElementById('msg-klub');
        let klubPanelOpen = false;

        btnTambahKlub.addEventListener('click', () => {
            klubPanelOpen = !klubPanelOpen;
            if (klubPanelOpen) {
                wrapInputKlub.style.removeProperty('display');
                wrapInputKlub.style.display = 'flex';
                iconTambah.textContent = 'close';
                btnTambahKlub.style.backgroundColor = '#ff4d4f';
                inputKlubBaru.focus();
            } else {
                resetTambahKlub();
            }
        });

        btnKonfirmKlub.addEventListener('click', () => {
            const namaKlub = inputKlubBaru.value.trim();
            if (!namaKlub) { inputKlubBaru.style.borderColor = '#ff4d4f'; inputKlubBaru.focus(); return; }

            // Cek duplikat
            const existing = Array.from(selectKlub.options).find(o => o.value.toLowerCase() === namaKlub.toLowerCase());
            if (existing) {
                selectKlub.value = existing.value;
            } else {
                const newOpt = new Option(namaKlub, namaKlub, true, true);
                selectKlub.add(newOpt);
            }

            msgKlub.textContent = '✅ "' + namaKlub + '" berhasil ditambahkan!';
            msgKlub.style.display = 'block';
            setTimeout(() => { msgKlub.style.display = 'none'; }, 2500);
            resetTambahKlub();
        });

        // Enter key di input juga trigger konfirm
        inputKlubBaru.addEventListener('keydown', (e) => { if (e.key === 'Enter') { e.preventDefault(); btnKonfirmKlub.click(); } });

        function resetTambahKlub() {
            klubPanelOpen = false;
            wrapInputKlub.style.display = 'none';
            iconTambah.textContent = 'add';
            btnTambahKlub.style.backgroundColor = '#52c41a';
            inputKlubBaru.value = '';
            inputKlubBaru.style.borderColor = '#52c41a';
        }

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const selectedEvents = form.querySelectorAll('input[name="event_id[]"]:checked');
            if (selectedEvents.length === 0) { alert('⚠️ Harap pilih minimal 1 event!'); return; }
            const formData = new FormData(form);
            fetch('{{ route("athletes.store") }}', {
                method: 'POST', body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(r => r.json().then(data => ({status: r.status, data})))
            .then(({status, data}) => {
                if (status === 200 && data.message) {
                    alert('✅ ' + data.message);
                    modal.style.display = 'none'; form.reset();
                    setTimeout(() => location.reload(), 500);
                } else if (data.error) { alert('❌ Error: ' + data.error); }
                else if (data.errors) {
                    let msg = '❌ Validasi error:\n';
                    for (let f in data.errors) msg += '- ' + f + ': ' + data.errors[f][0] + '\n';
                    alert(msg);
                } else { alert('❌ Error: Gagal menyimpan peserta'); }
            })
            .catch(err => alert('❌ Error: ' + err.message));
        });

        // ===== RACE TIMING =====
        let raceHeats = [];
        let raceLanes = []; // { lane_number, athlete_name, club, menit, detik, ms, format, finished }
        let raceStartTime = 0;
        let raceElapsed = 0;
        let raceRunning = false;
        let raceInterval = null;
        let currentEventName = '';
        let currentGenderLabel = '';
        let currentHeatNumber = '';

        const raceEventSel = document.getElementById('race-event');
        const raceGenderSel = document.getElementById('race-gender');
        const raceHeatSel = document.getElementById('race-heat');

        // Load heats when event+gender changes
        function loadRaceHeats() {
            const eid = raceEventSel.value;
            const gen = raceGenderSel.value;
            if (!eid || !gen) return;

            fetch(`/api/athletes/by-event?event_id=${eid}&jenis_kelamin=${gen}`)
                .then(r => r.json())
                .then(data => {
                    const athletes = data.athletes || [];
                    currentEventName = data.event_name || '';
                    raceHeatSel.innerHTML = '<option value="" disabled selected>-- Pilih Heat --</option>';
                    
                    raceHeats = [];
                    // Pecah atlet menjadi 8 org per heat
                    for (let i = 0; i < athletes.length; i += 8) {
                        const heatNum = Math.floor(i / 8) + 1;
                        const chunk = athletes.slice(i, i + 8);
                        raceHeats.push({
                            id: heatNum, // Gunakan heatNum sbg ID virtual
                            heat_number: heatNum,
                            lanes: chunk.map((ath, idx) => ({
                                lane_number: idx + 1,
                                athlete_name: ath.nama,
                                club: ath.club
                            }))
                        });
                    }

                    raceHeats.forEach(h => {
                        const opt = document.createElement('option');
                        opt.value = h.id;
                        opt.textContent = `Heat ${h.heat_number} (${h.lanes.length} peserta)`;
                        raceHeatSel.appendChild(opt);
                    });
                    raceHeatSel.disabled = raceHeats.length === 0;
                });
        }

        raceEventSel.addEventListener('change', loadRaceHeats);
        raceGenderSel.addEventListener('change', loadRaceHeats);

        // Load selected heat into timing table
        document.getElementById('btn-load-race').addEventListener('click', () => {
            const heatId = raceHeatSel.value;
            if (!heatId) { alert('Pilih heat terlebih dahulu!'); return; }

            const heat = raceHeats.find(h => h.id == heatId);
            if (!heat) return;

            currentHeatNumber = heat.heat_number;
            currentGenderLabel = raceGenderSel.value === 'L' ? 'Putra' : 'Putri';

            raceLanes = heat.lanes.map(l => ({
                lane_number: l.lane_number,
                athlete_name: l.athlete_name,
                club: l.club,
                menit: '-',
                detik: '-',
                ms: '-',
                format: '-',
                finished: false
            }));

            renderRaceTable();
            resetRaceStopwatch();
            document.getElementById('race-action-buttons').style.display = 'flex';
            document.getElementById('race-action-buttons').style.removeProperty('display');
        });

        function renderRaceTable() {
            const tbody = document.getElementById('race-timing-tbody');
            if (raceLanes.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted">Tidak ada peserta</td></tr>';
                return;
            }
            let html = '';
            raceLanes.forEach((lane, idx) => {
                const finishBtnHtml = lane.finished
                    ? `<span class="badge bg-success"><span class="material-icons" style="font-size:0.85rem;vertical-align:middle;">check</span> Tercatat</span>`
                    : `<button class="btn btn-sm btn-outline-danger fw-bold btn-lane-finish" data-idx="${idx}" ${!raceRunning ? 'disabled' : ''}>
                        <span class="material-icons" style="font-size:0.9rem;vertical-align:middle;">flag</span> Finish
                    </button>`;
                const formatColor = lane.finished ? 'color: #16a34a; font-weight: 700;' : '';
                html += `<tr>
                    <td style="text-align:center; font-weight:700;"><span class="badge rounded-pill" style="background:linear-gradient(135deg,#667eea,#764ba2);font-size:0.9rem;padding:6px 12px;">${lane.lane_number}</span></td>
                    <td><strong>${lane.athlete_name}</strong><br><small class="text-muted">${lane.club}</small></td>
                    <td style="text-align:center;">${lane.menit}</td>
                    <td style="text-align:center;">${lane.detik}</td>
                    <td style="text-align:center;">${lane.ms}</td>
                    <td style="text-align:center; font-size:1.05rem; ${formatColor}">${lane.format}</td>
                    <td style="text-align:center;">${finishBtnHtml}</td>
                </tr>`;
            });
            tbody.innerHTML = html;

            // Attach finish button listeners
            document.querySelectorAll('.btn-lane-finish').forEach(btn => {
                btn.addEventListener('click', () => {
                    const idx = parseInt(btn.dataset.idx);
                    finishLane(idx);
                });
            });
        }

        function finishLane(idx) {
            if (!raceRunning || raceLanes[idx].finished) return;
            const elapsed = Date.now() - raceStartTime;
            const totalSec = Math.floor(elapsed / 1000);
            const m = Math.floor(totalSec / 60);
            const s = totalSec % 60;
            const millis = elapsed % 1000;

            raceLanes[idx].menit = String(m).padStart(2, '0');
            raceLanes[idx].detik = String(s).padStart(2, '0');
            raceLanes[idx].ms = String(millis).padStart(3, '0');
            raceLanes[idx].format = `${raceLanes[idx].menit}:${raceLanes[idx].detik}.${raceLanes[idx].ms}`;
            raceLanes[idx].finished = true;
            renderRaceTable();

            // Check if all finished
            if (raceLanes.every(l => l.finished)) {
                clearInterval(raceInterval);
                raceRunning = false;
                document.getElementById('btn-race-finish').style.display = 'none';
                document.getElementById('btn-race-reset').style.display = 'inline-flex';
                const swWrap = document.getElementById('race-sw-wrap');
                swWrap.style.borderColor = '#ea580c';
                swWrap.style.background = '#fff7ed';
            }
        }

        // ===== RACE STOPWATCH =====
        function formatRaceSW(ms) {
            const totalSec = Math.floor(ms / 1000);
            const m = String(Math.floor(totalSec / 60)).padStart(2, '0');
            const s = String(totalSec % 60).padStart(2, '0');
            const millis = String(ms % 1000).padStart(3, '0');
            return `${m}:${s}<span style="font-size:2.2rem;opacity:0.5;">.${millis}</span>`;
        }

        function updateRaceSW() {
            raceElapsed = Date.now() - raceStartTime;
            document.getElementById('race-sw-display').innerHTML = formatRaceSW(raceElapsed);
        }

        document.getElementById('btn-race-start').addEventListener('click', () => {
            if (raceLanes.length === 0) { alert('Muat data heat terlebih dahulu!'); return; }
            raceStartTime = Date.now() - raceElapsed;
            raceRunning = true;
            raceInterval = setInterval(updateRaceSW, 37);

            document.getElementById('btn-race-start').style.display = 'none';
            document.getElementById('btn-race-finish').style.display = 'inline-flex';
            document.getElementById('btn-race-reset').style.display = 'none';
            const swWrap = document.getElementById('race-sw-wrap');
            swWrap.style.borderColor = '#16a34a';
            swWrap.style.background = '#f0fdf4';
            renderRaceTable(); // re-enable finish buttons
        });

        document.getElementById('btn-race-finish').addEventListener('click', () => {
            clearInterval(raceInterval);
            raceRunning = false;
            document.getElementById('btn-race-finish').style.display = 'none';
            document.getElementById('btn-race-start').style.display = 'inline-flex';
            document.getElementById('btn-race-start').innerHTML = '<span class="material-icons" style="font-size:1.1rem;vertical-align:middle;">play_arrow</span> LANJUT';
            document.getElementById('btn-race-reset').style.display = 'inline-flex';
            const swWrap = document.getElementById('race-sw-wrap');
            swWrap.style.borderColor = '#ea580c';
            swWrap.style.background = '#fff7ed';
            renderRaceTable(); // disable finish buttons
        });

        function resetRaceStopwatch() {
            clearInterval(raceInterval);
            raceRunning = false;
            raceElapsed = 0;
            document.getElementById('race-sw-display').innerHTML = formatRaceSW(0);
            document.getElementById('btn-race-start').style.display = 'inline-flex';
            document.getElementById('btn-race-start').innerHTML = '<span class="material-icons" style="font-size:1.1rem;vertical-align:middle;">play_arrow</span> START';
            document.getElementById('btn-race-finish').style.display = 'none';
            document.getElementById('btn-race-reset').style.display = 'none';
            const swWrap = document.getElementById('race-sw-wrap');
            swWrap.style.borderColor = '#e9ecef';
            swWrap.style.background = '#f8f9fa';
        }

        document.getElementById('btn-race-reset').addEventListener('click', () => {
            if (!confirm('Reset semua waktu dan stopwatch?')) return;
            raceLanes.forEach(l => { l.menit = '-'; l.detik = '-'; l.ms = '-'; l.format = '-'; l.finished = false; });
            resetRaceStopwatch();
            renderRaceTable();
        });

        // Clear all times
        document.getElementById('btn-race-clear').addEventListener('click', () => {
            if (!confirm('Reset semua waktu yang sudah tercatat?')) return;
            raceLanes.forEach(l => { l.menit = '-'; l.detik = '-'; l.ms = '-'; l.format = '-'; l.finished = false; });
            resetRaceStopwatch();
            renderRaceTable();
        });

        // ===== EXPORT EXCEL =====
        document.getElementById('btn-race-print').addEventListener('click', () => {
            if (raceLanes.length === 0) { alert('Tidak ada data untuk di-export!'); return; }

            const rows = [
                ['Hasil Lomba Renang'],
                [`Event: ${currentEventName} — ${currentGenderLabel} — Heat ${currentHeatNumber}`],
                [`Tanggal: ${new Date().toLocaleDateString('id-ID', { weekday:'long', year:'numeric', month:'long', day:'numeric' })}`],
                [],
                ['Jalur', 'Nama Peserta', 'Klub/Sekolah', 'Waktu (menit)', 'Waktu (detik)', 'Waktu (ms)', 'Waktu Format']
            ];

            raceLanes.forEach(l => {
                rows.push([l.lane_number, l.athlete_name, l.club, l.menit, l.detik, l.ms, l.format]);
            });

            const ws = XLSX.utils.aoa_to_sheet(rows);
            // Column widths
            ws['!cols'] = [
                {wch: 6}, {wch: 28}, {wch: 28}, {wch: 14}, {wch: 14}, {wch: 12}, {wch: 16}
            ];
            // Merge title row
            ws['!merges'] = [{s:{r:0,c:0},e:{r:0,c:6}}, {s:{r:1,c:0},e:{r:1,c:6}}, {s:{r:2,c:0},e:{r:2,c:6}}];

            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Hasil Lomba');
            const filename = `Hasil_${currentEventName}_${currentGenderLabel}_Heat${currentHeatNumber}.xlsx`.replace(/\s+/g, '_');
            XLSX.writeFile(wb, filename);
        });
        // ===== HASIL WAKTU TIMEKEEPER (IoT / Arduino) =====
        let lastResultCount = 0;

        function loadHasilIoT() {
            fetch('/api/results-table')
                .then(r => r.json())
                .then(data => {
                    const tbody = document.getElementById('tbody-hasil-iot');
                    const badge = document.getElementById('iot-status-badge');

                    if (!data.html || data.html.trim() === '') {
                        tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-muted">
                            <span class="material-icons d-block mb-2" style="font-size:2rem;color:#ccc;">hourglass_empty</span>
                            Belum ada data dari Arduino...
                        </td></tr>`;
                        badge.className = 'badge bg-warning text-dark';
                        badge.innerHTML = '<span class="material-icons" style="font-size:0.85rem;vertical-align:middle;">wifi_off</span> Menunggu Data';
                        return;
                    }

                    // Hitung jumlah baris dari server (cek apakah ada data baru)
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = data.html;
                    const rowCount = tempDiv.querySelectorAll('tr').length;

                    // Parse html, buang baris pertama (koneksi info), format ulang
                    const rows = Array.from(tempDiv.querySelectorAll('tr'));
                    let newHtml = '';
                    let no = 1;
                    rows.forEach(row => {
                        const cells = row.querySelectorAll('td');
                        if (cells.length < 6) return; // skip info row
                        const player = cells[1]?.innerText || '-';
                        const menit = cells[2]?.innerText || '-';
                        const detik = cells[3]?.innerText || '-';
                        const ms = cells[4]?.innerText || '-';
                        const fmt = cells[5]?.innerText || '-';
                        const waktuInput = cells[6]?.innerText || '-';
                        newHtml += `<tr>
                            <td class="text-center">${no++}</td>
                            <td class="text-center fw-bold">${player}</td>
                            <td class="text-center">${menit}</td>
                            <td class="text-center" style="color:#1d4ed8;">${detik}</td>
                            <td class="text-center">${ms}</td>
                            <td class="text-center fw-bold" style="color:#1d4ed8;">${fmt}</td>
                            <td class="text-center" style="color:#1d4ed8;">${waktuInput}</td>
                        </tr>`;
                    });

                    if (newHtml === '') {
                        tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-muted">Belum ada data dari Arduino...</td></tr>`;
                        badge.className = 'badge bg-secondary';
                        badge.innerHTML = 'Tidak ada data';
                        return;
                    }

                    tbody.innerHTML = newHtml;
                    badge.className = 'badge bg-success';
                    badge.innerHTML = `<span class="material-icons" style="font-size:0.85rem;vertical-align:middle;">wifi</span> Terhubung · ${no - 1} data`;
                    lastResultCount = rowCount;
                })
                .catch(() => {
                    const badge = document.getElementById('iot-status-badge');
                    badge.className = 'badge bg-danger';
                    badge.innerHTML = '<span class="material-icons" style="font-size:0.85rem;vertical-align:middle;">error</span> Gagal terhubung';
                });
        }

        // Load pertama saat halaman dibuka
        loadHasilIoT();
        // Auto-refresh setiap 3 detik
        setInterval(loadHasilIoT, 3000);

        // Tombol Hapus Semua hasil IoT
        document.getElementById('btn-clear-results').addEventListener('click', () => {
            if (!confirm('Hapus semua data hasil waktu dari database?')) return;
            fetch('/results/clear-all', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(d => { showToast(d.message || 'Data berhasil dihapus', 'success'); loadHasilIoT(); })
            .catch(() => showToast('Gagal menghapus data!', 'error'));
        });

        // ===== TOAST HELPER =====
        function showToast(msg, type = 'success') {
            const toastEl   = document.getElementById('app-toast');
            const toastMsg  = document.getElementById('toast-msg');
            const toastIcon = document.getElementById('toast-icon');
            toastEl.className = 'toast align-items-center border-0 shadow text-white';
            if (type === 'success') { toastEl.classList.add('bg-success'); toastIcon.textContent = 'check_circle'; }
            else if (type === 'error') { toastEl.classList.add('bg-danger'); toastIcon.textContent = 'error'; }
            else { toastEl.classList.add('bg-warning', 'text-dark'); toastIcon.textContent = 'warning'; }
            toastMsg.textContent = msg;
            bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 3500 }).show();
        }

        const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

        // ===== EDIT ATLET =====
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-edit-athlete');
            if (!btn) return;
            document.getElementById('edit-atlet-id').value       = btn.dataset.id;
            document.getElementById('edit-nama').value           = btn.dataset.nama;
            document.getElementById('edit-umur').value           = btn.dataset.umur;
            document.getElementById('edit-jenis_kelamin').value  = btn.dataset.jenis_kelamin;
            document.getElementById('edit-asal').value           = btn.dataset.asal;
            new bootstrap.Modal(document.getElementById('modal-edit-atlet')).show();
        });

        document.getElementById('btn-simpan-edit').addEventListener('click', function() {
            const id   = document.getElementById('edit-atlet-id').value;
            const body = new FormData();
            body.append('nama',              document.getElementById('edit-nama').value);
            body.append('umur',              document.getElementById('edit-umur').value);
            body.append('jenis_kelamin',     document.getElementById('edit-jenis_kelamin').value);
            body.append('asal_club_sekolah', document.getElementById('edit-asal').value);
            body.append('_method', 'PUT');

            fetch(`/athletes/${id}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: body
            })
            .then(r => r.json())
            .then(data => {
                bootstrap.Modal.getInstance(document.getElementById('modal-edit-atlet')).hide();
                if (data.message) {
                    showToast(data.message, 'success');
                    setTimeout(() => location.reload(), 1200);
                } else {
                    showToast(data.error || 'Gagal memperbarui atlet', 'error');
                }
            })
            .catch(err => showToast('Error: ' + err.message, 'error'));
        });

        // ===== DELETE ATLET =====
        let deleteTargetId = null;

        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-delete-athlete');
            if (!btn) return;
            deleteTargetId = btn.dataset.id;
            document.getElementById('hapus-nama-atlet').textContent = btn.dataset.nama;
            new bootstrap.Modal(document.getElementById('modal-hapus-atlet')).show();
        });

        document.getElementById('btn-konfirmasi-hapus').addEventListener('click', function() {
            if (!deleteTargetId) return;
            bootstrap.Modal.getInstance(document.getElementById('modal-hapus-atlet')).hide();
            fetch(`/athletes/${deleteTargetId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.message) {
                    showToast(data.message, 'success');
                    setTimeout(() => location.reload(), 1200);
                } else {
                    showToast(data.error || 'Gagal menghapus atlet', 'error');
                }
                deleteTargetId = null;
            })
            .catch(err => showToast('Error: ' + err.message, 'error'));
        });
    </script>
</body>
</html>
