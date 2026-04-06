@extends('layouts.participant')
@section('title', 'Daftar Track Record')

@section('breadcrumb')
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom w-100">
        <h5 class="fw-bold text-primary mb-0" style="color: #003399 !important;">Track Record Kompetisi Milik <span class="text-danger">{{ $athlete->nama }}</span> Dari <span class="text-danger">{{ $athlete->asal_club_sekolah }}</span></h5>
    </div>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb bg-transparent mb-0 px-0 py-1">
        <li class="breadcrumb-item"><a href="{{ route('participant.athletes') }}" style="text-decoration: none; color: #666;">Atlet Saya</a></li>
        <li class="breadcrumb-item active" aria-current="page">Track Record</li>
      </ol>
    </nav>
@endsection

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold text-primary mb-0" style="color: #003399 !important;">Daftar Track Record</h5>
        <div>
            <button id="btn-add-track" class="btn rounded-pill px-4 text-white d-inline-flex align-items-center" style="background-color: #52c41a; border: none;">
                <span class="material-icons me-1" style="font-size: 1.1rem;">post_add</span> Tambah
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="d-flex justify-content-between mb-3 mt-3">
            <div class="d-flex align-items-center" style="font-size: 0.9rem; color: #666;">
                Tampilkan 
                <select class="form-select mx-2 form-select-sm" style="width: auto;">
                    <option>10</option>
                </select>
                entri
            </div>
            <div>
                <label style="font-size: 0.9rem; color: #666;">Cari: <input type="search" class="form-control form-control-sm d-inline-block w-auto ms-1"></label>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered table-striped text-center align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="color: #666; font-size: 0.9rem; font-weight: 600;">#</th>
                        <th style="color: #666; font-size: 0.9rem; font-weight: 600;">Nama Kompetisi</th>
                        <th style="color: #666; font-size: 0.9rem; font-weight: 600;">Nomor Lomba</th>
                        <th style="color: #666; font-size: 0.9rem; font-weight: 600;">Durasi Renang</th>
                        <th style="color: #666; font-size: 0.9rem; font-weight: 600;">Aksi</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.9rem; color: #444;">
                    @forelse($athlete->trackRecords as $index => $record)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $record->nama_kompetisi }}</td>
                            <td>{{ $record->nomor_lomba }}</td>
                            <td class="fw-bold" style="letter-spacing: 2px;">{{ $record->durasi_renang }}</td>
                            <td>
                                <button class="btn btn-sm text-white p-1" style="background-color: #ff4d4f;" title="Hapus">
                                    <span class="material-icons" style="font-size: 1.1rem;">delete</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center bg-light text-muted py-3">Tidak ada data yang tersedia pada tabel ini</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-3">
            <small class="text-muted">Menampilkan {{ count($athlete->trackRecords) > 0 ? 1 : 0 }} sampai {{ count($athlete->trackRecords) }} dari {{ count($athlete->trackRecords) }} entri</small>
            <ul class="pagination pagination-sm mb-0">
                <li class="page-item border disabled"><a class="page-link border-0 text-muted" href="#">Sebelumnya</a></li>
                <li class="page-item border active"><a class="page-link border-0 text-white" style="background-color: #003399;" href="#">1</a></li>
                <li class="page-item border disabled"><a class="page-link border-0 text-muted" href="#">Selanjutnya</a></li>
            </ul>
        </div>
    </div>
</div>

<!-- Modal Tambah Track Record -->
<div id="modal-track" class="custom-modal-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;z-index:1050;">
    <div class="bg-white p-4 rounded shadow" style="width:100%;max-width:550px;max-height:90vh;overflow-y:auto; position: relative;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0 text-secondary">Tambah Track Record</h5>
            <button type="button" class="btn-close" style="font-size: 0.8rem;" aria-label="Close" onclick="closeTrackModal()"></button>
        </div>
        
        <form id="form-track">
            @csrf
            
            <div class="mb-3">
                <label class="form-label" style="font-size: 0.85rem; color: #555;">Nama Kompetisi</label>
                <input class="form-control" type="text" name="nama_kompetisi" placeholder="GSC 2024" required style="border-color: #e0e6ed; font-size: 0.9rem;">
            </div>
            
            <div class="mb-4">
                <label class="form-label" style="font-size: 0.85rem; color: #555;">Nomor Lomba</label>
                <select class="form-select text-muted" name="nomor_lomba" required style="border-color: #e0e6ed; font-size: 0.9rem;">
                    <option value="" disabled selected>Pilih Nomor Lomba</option>
                    @foreach($events as $event)
                        <option value="{{ $event->nama_event }}">{{ $event->nama_event }}</option>
                    @endforeach
                    <!-- Fallbacks if db is empty -->
                    @if(count($events) == 0)
                        <option value="50m GAYA BEBAS Putra">50m GAYA BEBAS Putra</option>
                        <option value="100m GAYA BEBAS Putra">100m GAYA BEBAS Putra</option>
                    @endif
                </select>
            </div>

            <div class="mb-5">
                <label class="form-label" style="font-size: 0.85rem; color: #555;">Durasi Renang</label>
                <div class="d-flex align-items-center gap-2">
                    <input type="text" id="dur_min" class="form-control text-center fw-bold px-1 py-2" placeholder="00" maxlength="2" style="width: 50px; background-color: #f0f2f5; border: 1px solid #e0e6ed; font-size: 1.2rem;">
                    <span class="fw-bold fs-4">:</span>
                    <input type="text" id="dur_sec" class="form-control text-center fw-bold px-1 py-2" placeholder="00" maxlength="2" style="width: 50px; background-color: #f0f2f5; border: 1px solid #e0e6ed; font-size: 1.2rem;">
                    <span class="fw-bold fs-4">.</span>
                    <input type="text" id="dur_ms" class="form-control text-center fw-bold px-1 py-2 shadow-sm" placeholder="00" maxlength="2" style="width: 50px; background-color: white; border: 2px solid #555; border-radius: 4px; font-size: 1.2rem;">
                </div>
                <!-- Hidden input to store the combined value -->
                <input type="hidden" name="durasi_renang" id="combined_durasi" required>
            </div>
            
            <div class="text-end">
                <button type="submit" class="btn px-5 rounded-pill shadow-sm" style="background-color: #003399; color: white; font-weight: 500;">Simpan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const btnAddTrack = document.getElementById('btn-add-track');
    const trackModal = document.getElementById('modal-track');
    const formTrack = document.getElementById('form-track');

    const durMin = document.getElementById('dur_min');
    const durSec = document.getElementById('dur_sec');
    const durMs = document.getElementById('dur_ms');
    const combinedDurasi = document.getElementById('combined_durasi');

    btnAddTrack.addEventListener('click', () => {
        trackModal.style.display = 'flex';
    });
    
    function closeTrackModal() {
        trackModal.style.display = 'none';
        formTrack.reset();
    }

    // Combine inputs before submit
    function syncDurasi() {
        let m = durMin.value.padStart(2, '0') || '00';
        let s = durSec.value.padStart(2, '0') || '00';
        let ms = durMs.value.padStart(2, '0') || '00';
        combinedDurasi.value = `${m}:${s}.${ms}`;
    }

    [durMin, durSec, durMs].forEach(input => {
        input.addEventListener('input', () => {
            // Numbers only
            input.value = input.value.replace(/[^0-9]/g, '');
            syncDurasi();
            // Auto advance
            if(input.value.length === parseInt(input.getAttribute('maxlength'))) {
                if(input.id === 'dur_min') durSec.focus();
                if(input.id === 'dur_sec') durMs.focus();
            }
        });
    });

    formTrack.addEventListener('submit', (e) => {
        e.preventDefault();
        syncDurasi(); // ensure latest value
        
        const formData = new FormData(formTrack);
        
        fetch('{{ route("athletes.store_track_record", $athlete->id) }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(r => r.json().then(data => ({status: r.status, data})))
        .then(({status, data}) => {
            if (status === 200 && data.message) {
                alert(data.message);
                closeTrackModal();
                setTimeout(() => location.reload(), 500);
            } else if (data.errors) {
                let errorMsg = '❌ Validasi error:\n';
                for (let field in data.errors) {
                    errorMsg += '- ' + field + ': ' + data.errors[field][0] + '\n';
                }
                alert(errorMsg);
            } else {
                alert('❌ Error: ' + (data.message || 'Gagal menyimpan track record'));
            }
        })
        .catch(err => {
            alert('❌ Error: ' + err.message);
        });
    });
</script>
@endpush
@endsection
