@extends('layouts.participant')
@section('title', 'Daftar Atlet')

@push('styles')
<style>
    /* Styling for the custom radio buttons in the form */
    .radio-group-custom .btn-check:checked + .btn {
        background-color: #003399;
        color: white;
        border-color: #003399;
    }
    .radio-group-custom .btn {
        border-color: #e0e6ed;
        color: #666;
    }
    .radio-group-custom .btn:hover {
        background-color: #f8f9fa;
    }
    .radio-group-custom .btn-check:checked + .btn:hover {
        background-color: #002266;
    }
</style>
@endpush

@section('breadcrumb')
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom w-100">
        <h5 class="fw-bold text-primary mb-0" style="color: #003399 !important;">Daftar Atlet Peserta Renang </h5>
        <span class="badge rounded-pill" style="background-color: #003399; font-size: 0.9rem; padding: 0.5rem 1rem;">Total : {{ count($athletes) }} Atlet</span>
    </div>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb bg-transparent mb-0 px-0 py-1">
        <li class="breadcrumb-item"><a href="{{ route('participant.athletes') }}" style="text-decoration: none; color: #666;">Atlet Saya</a></li>
      </ol>
    </nav>
@endsection

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold text-primary mb-0" style="color: #003399 !important;">Daftar Atlet</h5>
        <div>
            <button id="btn-add-peserta" class="btn btn-success">
                <span class="material-icons" style="vertical-align: middle; margin-right: 5px;">person_add</span> Tambah Peserta
            </button>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('participant.athletes') }}" class="d-flex justify-content-between mb-3 mt-3 w-100">
            <div class="d-flex align-items-center" style="font-size: 0.9rem; color: #666;">
                Tampilkan 
                <select name="per_page" class="form-select mx-2 form-select-sm" style="width: auto;" onchange="this.form.submit()">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                </select>
                entri
            </div>
            <div>
                <label style="font-size: 0.9rem; color: #666;" class="d-flex align-items-center gap-2">Cari: 
                    <input type="search" name="search" value="{{ request('search') }}" class="form-control form-control-sm d-inline-block w-auto" placeholder="Ketik nama atau klub...">
                </label>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="color: #666; font-size: 0.9rem; font-weight: 600;">No</th>
                        <th style="text-align: center; color: #666; font-size: 0.9rem; font-weight: 600;">Nama</th>
                        <th class="text-center" style="color: #666; font-size: 0.9rem; font-weight: 600;">Kelompok Umur</th>
                        <th class="text-center" style="color: #666; font-size: 0.9rem; font-weight: 600;">Jenis Kelamin</th>
                        <th style="text-align: center; color: #666; font-size: 0.9rem; font-weight: 600;">Asal Klub/Sekolah</th>
                        <th style="text-align: center; color: #666; font-size: 0.9rem; font-weight: 600;">Event</th>
                        <th class="text-center" style="color: #666; font-size: 0.9rem; font-weight: 600; width: 110px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbody-peserta" style="font-size: 0.9rem; color: #444;">
                    @forelse($athletes as $index => $athlete)
                        <tr>
                            <td class="text-center">{{ ($athletes->currentPage() - 1) * $athletes->perPage() + $loop->iteration }}</td>
                            <td>{{ $athlete->nama }}</td>
                            <td class="text-center">
    @php
        $umur = $athlete->umur ?? ($athlete->tanggal_lahir ? \Carbon\Carbon::parse($athlete->tanggal_lahir)->age : null);
        if ($umur === null) {
            $kuLabel = '-';
        } elseif ($umur < 10)      { $kuLabel = null; }
        elseif ($umur <= 12)       { $kuLabel = 'KU I (10–12 Tahun)'; }
        elseif ($umur <= 14)       { $kuLabel = 'KU II (13–14 Tahun)'; }
        elseif ($umur <= 17)       { $kuLabel = 'KU III (15–17 Tahun)'; }
        elseif ($umur <= 24)       { $kuLabel = 'KU IV (18–24 Tahun)'; }
        else                       { $kuLabel = 'Senior / Open (25+ Tahun)'; }
    @endphp

    @if($umur === null)
        <span style="color:#aaa;">-</span>
    @elseif($kuLabel === null)
        <div class="fw-bold">{{ $umur }} Tahun</div>
        <div style="font-size:0.78rem; color:#aaa;">Belum masuk KU</div>
    @else
        <div class="fw-bold">{{ $umur }} Tahun</div>
        <div style="font-size:0.78rem; color:#003399; font-weight: 500;">{{ $kuLabel }}</div>
    @endif
</td>
                            <td class="text-center">{{ $athlete->jenis_kelamin === 'L' ? 'Laki-laki' : ($athlete->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}</td>
                            <td>{{ $athlete->asal_club_sekolah ?? '-' }}</td>
                            <td>
                                @foreach($athlete->registrations as $reg)
                                    <span class="badge" style="background-color:#13c2c2;">{{ $reg->event->nama_event }}</span>
                                @endforeach
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('athletes.edit', $athlete->id) }}" class="btn btn-sm text-white p-1" style="background-color: #1890ff;" title="Edit">
                                        <span class="material-icons" style="font-size: 1.1rem;">edit</span>
                                    </a>
                                    <button class="btn btn-sm text-white p-1 btn-delete-athlete" 
                                        data-id="{{ $athlete->id }}" 
                                        data-nama="{{ $athlete->nama }}"
                                        style="background-color: #ff4d4f;" title="Hapus">
                                        <span class="material-icons" style="font-size: 1.1rem;">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center bg-light text-muted py-3">Tidak ada data yang tersedia pada tabel ini</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-3">
            <small class="text-muted">Menampilkan {{ $athletes->firstItem() ?? 0 }} sampai {{ $athletes->lastItem() ?? 0 }} dari {{ $athletes->total() }} entri</small>
            <div class="m-0 pagination-sm">
                {{ $athletes->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

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
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0 text-secondary">Tambah Atlet</h5>
            <button type="button" class="btn-close" style="font-size: 0.8rem;" aria-label="Close" id="btn-cancel-peserta"></button>
        </div>

        <form id="form-peserta" method="POST" action="{{ route('athletes.store') }}">
            @csrf

            <div class="mb-2">
                <label class="form-label mb-1" style="font-size: 0.85rem; color: #555;">Nama Atlet</label>
                <input type="text" name="nama" class="form-control" placeholder="Nama Atlet" required style="border-color: #b3d4ff; font-size: 0.9rem;">
            </div>

            <div class="mb-2">
                <label class="form-label mb-1" style="font-size: 0.85rem; color: #555;">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" id="input-tanggal-lahir" class="form-control" required style="border-color: #e0e6ed; font-size: 0.9rem;">
                <div id="preview-ku" style="display:none; margin-top: 4px;">
                    <div id="label-ku-full" style="font-size: 0.85rem; color: #003399; font-weight: 500;"></div>
                </div>
                <div id="preview-ku-warning" style="display:none; margin-top: 4px;">
                    <span style="font-size: 0.8rem; color: #ff4d4f;">
                        <span class="material-icons" style="font-size: 0.9rem; vertical-align: middle;">warning</span>
                        Atlet di bawah 10 tahun tidak masuk kategori KU manapun.
                    </span>
                </div>
            </div>

            <div class="mb-2">
                <label class="form-label mb-1" style="font-size: 0.85rem; color: #555;">Asal Klub / Sekolah</label>
                <div class="d-flex gap-2">
                    <select id="select-asal-klub" name="asal_club_sekolah" class="form-select text-muted" required style="border-color: #e0e6ed; font-size: 0.9rem;">
                        <option value="" disabled selected>Pilih Asal Sekolah / Klub</option>
                        <option value="SMA Negeri 2 Jakarta">SMA Negeri 2 Jakarta</option>
                        <option value="Samator Swimming Club">Samator Swimming Club</option>
                        <option value="Universitas Gadjah Mada">Universitas Gadjah Mada</option>
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
                        <input type="radio" class="btn-check" name="jenis_kelamin" id="hk_laki" value="L" autocomplete="off" required>
                        <label class="btn w-100 rounded-0 border-0 m-0 py-2 d-flex justify-content-center align-items-center" for="hk_laki" style="font-size: 0.9rem;">
                            <span class="material-icons me-2" style="font-size: 1.1rem;">male</span> Laki-laki
                        </label>
                    </div>
                    <div class="col-6" style="border-left: 1px solid #e0e6ed;">
                        <input type="radio" class="btn-check" name="jenis_kelamin" id="hk_perempuan" value="P" autocomplete="off" required>
                        <label class="btn w-100 rounded-0 border-0 m-0 py-2 d-flex justify-content-center align-items-center" for="hk_perempuan" style="font-size: 0.9rem;">
                            <span class="material-icons me-2" style="font-size: 1.1rem;">female</span> Perempuan
                        </label>
                    </div>
                </div>
            </div>

            <div class="mb-3 mt-1">
                <small class="text-danger" style="font-size: 0.8rem;">*Pastikan ulang semua data yang diinputkan telah benar!</small>
            </div>

            <div class="mb-2">
                <label class="form-label mb-1" style="font-size: 0.85rem; color: #555;">Pilih Event</label>
                <div style="max-height: 180px; overflow-y: auto; border: 1px solid #ddd; padding: 8px; border-radius: 4px;">
                    @foreach($events as $event)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="event_id[]" value="{{ $event->id }}" id="event_{{ $event->id }}">
                        <label class="form-check-label" for="event_{{ $event->id }}" style="font-size: 0.9rem;">
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

<!-- ===== MODAL KONFIRMASI HAPUS ===== -->
<div class="modal fade" id="modal-konfirmasi-hapus" tabindex="-1" aria-labelledby="modalHapusLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:40px;height:40px;border-radius:50%;background:#fff1f0;display:flex;align-items:center;justify-content:center;">
                        <span class="material-icons" style="color:#ff4d4f;font-size:1.3rem;">delete_outline</span>
                    </div>
                    <h6 class="modal-title fw-bold mb-0" id="modalHapusLabel">Konfirmasi Hapus Atlet</h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <p class="mb-1" style="font-size:0.95rem;">Anda yakin ingin menghapus atlet:</p>
                <p class="fw-bold mb-3" id="hapus-nama-atlet" style="color:#003399;"></p>
                <div class="alert alert-warning py-2 px-3 d-flex align-items-center gap-2" style="font-size:0.82rem;border-radius:8px;">
                    <span class="material-icons" style="font-size:1rem;">info</span>
                    Data registrasi &amp; track record atlet ini juga akan ikut terhapus!
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn text-white px-4" id="btn-konfirmasi-hapus" style="background-color:#ff4d4f;">
                    <span class="material-icons me-1" style="font-size:1rem;vertical-align:middle;">delete</span> Hapus
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // ===== TOAST HELPER =====
    function showToast(msg, type = 'success') {
        const toastEl  = document.getElementById('app-toast');
        const toastMsg = document.getElementById('toast-msg');
        const toastIcon = document.getElementById('toast-icon');

        // Warna & ikon
        toastEl.className = 'toast align-items-center border-0 shadow text-white';
        if (type === 'success') {
            toastEl.classList.add('bg-success');
            toastIcon.textContent = 'check_circle';
        } else if (type === 'error') {
            toastEl.classList.add('bg-danger');
            toastIcon.textContent = 'error';
        } else {
            toastEl.classList.add('bg-warning', 'text-dark');
            toastIcon.textContent = 'warning';
        }

        toastMsg.textContent = msg;
        const bsToast = bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 3500 });
        bsToast.show();
    }

    // ===== MODAL TAMBAH PESERTA =====
    const btnAdd    = document.getElementById('btn-add-peserta');
    const modal     = document.getElementById('modal-peserta');
    const cancelBtn = document.getElementById('btn-cancel-peserta');
    const form      = document.getElementById('form-peserta');

    btnAdd.addEventListener('click', () => { modal.style.display = 'flex'; });
    cancelBtn.addEventListener('click', () => { modal.style.display = 'none'; form.reset(); resetTambahKlub(); resetPreviewKU(); });
    modal.addEventListener('click', (e) => { if (e.target === modal) { modal.style.display = 'none'; form.reset(); resetTambahKlub(); resetPreviewKU(); } });

    // ===== TOMBOL TAMBAH KLUB =====
    const btnTambahKlub = document.getElementById('btn-tambah-klub');
    const wrapInputKlub = document.getElementById('wrap-input-klub');
    const inputKlubBaru = document.getElementById('input-klub-baru');
    const btnKonfirmKlub = document.getElementById('btn-konfirm-klub');
    const selectKlub = document.getElementById('select-asal-klub');
    const iconTambah = document.getElementById('icon-tambah-klub');
    const msgKlub = document.getElementById('msg-klub');
    let klubPanelOpen = false;

    function resetTambahKlub() {
        wrapInputKlub.style.setProperty('display', 'none', 'important');
        iconTambah.textContent = 'add';
        btnTambahKlub.style.backgroundColor = '#52c41a';
        klubPanelOpen = false;
        inputKlubBaru.value = '';
        msgKlub.style.display = 'none';
        selectKlub.disabled = false;
    }

    btnTambahKlub.addEventListener('click', () => {
        klubPanelOpen = !klubPanelOpen;
        if (klubPanelOpen) {
            wrapInputKlub.style.setProperty('display', 'flex', 'important');
            inputKlubBaru.focus();
            iconTambah.textContent = 'close';
            btnTambahKlub.style.backgroundColor = '#ff4d4f';
            selectKlub.disabled = true;
        } else {
            resetTambahKlub();
        }
    });

    btnKonfirmKlub.addEventListener('click', () => {
        const val = inputKlubBaru.value.trim();
        if (!val) { alert('Isi nama klub/sekolah!'); return; }
        const ex = Array.from(selectKlub.options).find(o => o.value.toLowerCase() === val.toLowerCase());
        if (!ex) {
            const opt = new Option(val, val);
            selectKlub.add(opt);
        }
        selectKlub.value = val;
        
        msgKlub.textContent = 'Klub baru ditambahkan ke daftar!';
        msgKlub.style.display = 'block';
        wrapInputKlub.style.setProperty('display', 'none', 'important');
        iconTambah.textContent = 'add';
        btnTambahKlub.style.backgroundColor = '#52c41a';
        klubPanelOpen = false;
        inputKlubBaru.value = '';
        selectKlub.disabled = false;
        setTimeout(() => msgKlub.style.display = 'none', 3000);
    });

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(form);

        fetch('{{ route("athletes.store") }}', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json().then(data => ({ status: r.status, data })))
        .then(({ status, data }) => {
            if (status === 200 && data.message) {
                modal.style.display = 'none';
                form.reset();
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 1200);
            } else if (data.error) {
                showToast(data.error, 'error');
            } else if (data.errors) {
                const first = Object.values(data.errors)[0][0];
                showToast('Validasi: ' + first, 'error');
            } else {
                showToast('Gagal menyimpan peserta', 'error');
            }
        })
        .catch(err => showToast('Error: ' + err.message, 'error'));
    });

    // ===== DELETE ATLET =====
    let deleteTargetId   = null;
    let deleteTargetRow  = null;

    // Klik tombol hapus → tampilkan modal konfirmasi Bootstrap
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-delete-athlete');
        if (!btn) return;

        deleteTargetId  = btn.dataset.id;
        deleteTargetRow = btn.closest('tr');

        document.getElementById('hapus-nama-atlet').textContent = btn.dataset.nama;
        const bsModal = new bootstrap.Modal(document.getElementById('modal-konfirmasi-hapus'));
        bsModal.show();
    });

    // Klik tombol "Hapus" di dalam modal konfirmasi
    document.getElementById('btn-konfirmasi-hapus').addEventListener('click', function() {
        if (!deleteTargetId) return;

        // Tutup modal konfirmasi
        bootstrap.Modal.getInstance(document.getElementById('modal-konfirmasi-hapus')).hide();

        fetch(`/athletes/${deleteTargetId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.message) {
                showToast(data.message, 'success');
                // Reload halaman setelah toast muncul sebentar
                setTimeout(() => location.reload(), 1200);
            } else {
                showToast(data.error || 'Gagal menghapus atlet', 'error');
            }
            deleteTargetId  = null;
            deleteTargetRow = null;
        })
        .catch(err => showToast('Error: ' + err.message, 'error'));
    });

    // ===== KELOMPOK UMUR OTOMATIS (PRSI) =====
    function hitungKU(tanggalLahir) {
        const lahir = new Date(tanggalLahir);
        const today = new Date();
        let umur = today.getFullYear() - lahir.getFullYear();
        const belumUltah =
            today.getMonth() < lahir.getMonth() ||
            (today.getMonth() === lahir.getMonth() && today.getDate() < lahir.getDate());
        if (belumUltah) umur--;

        if (umur < 10)  return { ku: null, umur };
        if (umur <= 12) return { ku: 'KU I (10–12 Tahun)', umur };
        if (umur <= 14) return { ku: 'KU II (13–14 Tahun)', umur };
        if (umur <= 17) return { ku: 'KU III (15–17 Tahun)', umur };
        if (umur <= 24) return { ku: 'KU IV (18–24 Tahun)', umur };
        return { ku: 'Senior / Open (25+ Tahun)', umur };
    }

    document.getElementById('input-tanggal-lahir').addEventListener('change', function () {
        const val = this.value;
        const previewKU      = document.getElementById('preview-ku');
        const previewWarning = document.getElementById('preview-ku-warning');
        const labelKU        = document.getElementById('label-ku');
        const labelUmur      = document.getElementById('label-umur');

        if (!val) {
            previewKU.style.display      = 'none';
            previewWarning.style.display = 'none';
            return;
        }

        const { ku, umur } = hitungKU(val);

        if (ku === null) {
            previewKU.style.display      = 'none';
            previewWarning.style.display = 'block';
        } else {
            const labelFull = document.getElementById('label-ku-full');
            previewKU.style.display      = 'block';
            previewWarning.style.display = 'none';
            labelFull.textContent = ku + ' (' + umur + ' tahun)';
        }
    });

    function resetPreviewKU() {
        document.getElementById('preview-ku').style.display       = 'none';
        document.getElementById('preview-ku-warning').style.display = 'none';
        document.getElementById('input-tanggal-lahir').value      = '';
    }
</script>
@endpush
@endsection