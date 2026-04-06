@extends('layouts.participant')
@section('title', 'Eksplor Kompetisi')

@section('breadcrumb')
    <div class="pt-3 pb-2 mb-3 border-bottom w-100">
        <h5 class="fw-bold text-primary mb-0" style="color: #003399 !important;">Cari Kompetisi</h5>
    </div>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb bg-transparent mb-0 px-0 py-1 text-muted">
        <li class="breadcrumb-item">Kompetisi</li>
        <li class="breadcrumb-item">Eksplor</li>
      </ol>
    </nav>
@endsection

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-0 pt-4 pb-0">
        <h5 class="fw-bold text-primary mb-0" style="color: #003399 !important;">Daftar Kompetisi</h5>
    </div>
    <div class="card-body">
        <div class="d-flex justify-content-between mb-3 mt-3">
            <div class="d-flex align-items-center">
                Tampilkan 
                <select class="form-select mx-2 form-select-sm" style="width: auto;">
                    <option>10</option>
                </select>
                entri
            </div>
            <div>
                <label>Cari: <input type="search" class="form-control form-control-sm d-inline-block w-auto"></label>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">No</th>
                        <th>Nama</th>
                        <th>Lokasi</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Peserta Terdaftar</th>
                        <th class="text-center">Kuota</th>
                        <th class="text-center">Mulai</th>
                        <th class="text-center border-end-0">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">1</td>
                        <td>Tri Grantha Akswara Swimming Championship 2026</td>
                        <td>Kolam Renang Tirta Krida, Yogyakarta</td>
                        <td class="text-center">
                            @if($terdaftar >= $kuota)
                                <span class="badge bg-danger px-3 py-2 rounded-pill">Kuota Penuh</span>
                            @else
                                <span class="badge bg-primary px-3 py-2 rounded-pill">Buka Pendaftaran</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $terdaftar }}</td>
                        <td class="text-center">{{ $kuota }}</td>
                        <td class="text-center">2026-08-30<br>06:30:00</td>
                        <td class="text-center border-end-0">
                            @if($terdaftar >= $kuota)
                                <button class="btn btn-secondary btn-sm d-inline-flex align-items-center" disabled>
                                    <span class="material-icons me-1" style="font-size: 1rem;">person_off</span> Penuh
                                </button>
                            @else
                                <button id="btn-daftar" class="btn btn-success btn-sm d-inline-flex align-items-center" style="background-color: #52c41a; border-color: #52c41a;">
                                    <span class="material-icons me-1" style="font-size: 1rem;">person_add</span> Daftar
                                </button>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-3">
            <small class="text-muted">Menampilkan 1 sampai 1 dari 1 entri</small>
            <ul class="pagination pagination-sm mb-0">
                <li class="page-item border disabled"><a class="page-link border-0 text-muted" href="#">Sebelumnya</a></li>
                <li class="page-item border disabled"><a class="page-link border-0 text-muted" href="#">Selanjutnya</a></li>
            </ul>
        </div>
    </div>
</div>

<!-- Modal Daftar Atlet ke Kompetisi -->
<div id="modal-daftar" class="custom-modal-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;z-index:1050;">
    <div class="bg-white p-4 rounded shadow" style="width:100%;max-width:550px;max-height:90vh;overflow-y:auto; position: relative;">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h5 class="fw-bold mb-0 text-secondary" style="font-size: 1.1rem; color: #555;">Pendaftaran Atlet</h5>
            <button type="button" class="btn-close" style="font-size: 0.8rem;" aria-label="Close" id="btn-cancel-top"></button>
        </div>
        
        <form id="form-daftar" method="POST" action="{{ route('registrations.store') }}">
            @csrf
            
            <div class="mb-3">
                <label class="form-label" style="font-size: 0.85rem; color: #888;">Kompetisi</label>
                <p class="mb-0" style="font-size: 0.95rem; color: #444; font-weight: 500;">Tri Grantha Akswara Swimming Championship 2026</p>
            </div>
            
            <div class="mb-4">
                <label class="form-label" style="font-size: 0.85rem; color: #888;">Atlet</label>
                <select name="athlete_id" class="form-select text-muted" required style="border-color: #e6e9ef; font-size: 0.9rem;">
                    <option value="" disabled selected>Pilih Atlet</option>
                    @foreach($athletes as $athlete)
                        <option value="{{ $athlete->id }}">{{ $athlete->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold" style="font-size: 0.9rem; color: #555;">Nomor Event</label>
                <div class="d-flex flex-column gap-2 mt-2" style="max-height: 250px; overflow-y: auto;">
                    @foreach($events as $evt)
                        <div class="form-check" style="padding-left: 1.5rem;">
                            <input class="form-check-input border-secondary" type="checkbox" name="event_id[]" value="{{ $evt->id }}" id="event_{{ $evt->id }}" style="cursor: pointer;">
                            <label class="form-check-label" for="event_{{ $evt->id }}" style="font-size: 0.9rem; color: #555; cursor: pointer;">
                                {{ str_pad($evt->id, 3, '0', STR_PAD_LEFT) }} - {{ $evt->nama_event }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <div class="d-flex gap-2 justify-content-end mt-4 pt-3 border-top">
                <button type="button" id="btn-cancel-daftar" class="btn" style="background-color: #e2e8f0; color: #64748b; font-weight: 500; padding: 0.4rem 1.25rem;">Batal</button>
                <button type="submit" class="btn" style="background-color: #003399; color: white; font-weight: 500; padding: 0.4rem 1.25rem;">Daftarkan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const btnDaftar = document.getElementById('btn-daftar');
    const modalDaftar = document.getElementById('modal-daftar');
    const cancelBtnDaftar = document.getElementById('btn-cancel-daftar');
    const cancelBtnTop = document.getElementById('btn-cancel-top');
    const formDaftar = document.getElementById('form-daftar');

    btnDaftar.addEventListener('click', () => {
        modalDaftar.style.display = 'flex';
    });

    const closeModal = () => {
        modalDaftar.style.display = 'none';
        formDaftar.reset();
    };

    cancelBtnDaftar.addEventListener('click', closeModal);
    cancelBtnTop.addEventListener('click', closeModal);

    formDaftar.addEventListener('submit', (e) => {
        e.preventDefault();

        // Check if at least one checkbox is selected
        const checkboxes = document.querySelectorAll('input[name="event_id[]"]:checked');
        if (checkboxes.length === 0) {
            alert('Silakan pilih minimal satu Nomor Event.');
            return;
        }

        const formData = new FormData(formDaftar);
        
        fetch('{{ route("registrations.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(r => r.json().then(data => ({status: r.status, data})))
        .then(({status, data}) => {
            if (status === 200 && data.message) {
                alert(data.message);
                closeModal();
                location.reload();
            } else if (data.error) {
                alert(data.error);
            } else if (data.errors) {
                let errorMsg = '❌ Validasi error:\n';
                for (let field in data.errors) {
                    errorMsg += '- ' + field + ': ' + data.errors[field][0] + '\n';
                }
                alert(errorMsg);
            } else {
                alert('❌ Error: Gagal mendaftarkan peserta');
            }
        })
        .catch(err => {
            alert('❌ Error: ' + err.message);
        });
    });
</script>
@endpush
@endsection
