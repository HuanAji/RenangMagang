@extends('layouts.participant')
@section('title', 'Daftar Dokumen')

@section('breadcrumb')
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom w-100">
        <h5 class="fw-bold text-primary mb-0" style="color: #003399 !important;">List Dokumen Milik {{ $athlete->nama }} Dari {{ $athlete->asal_club_sekolah }}</h5>
    </div>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb bg-transparent mb-0 px-0 py-1">
        <li class="breadcrumb-item"><a href="{{ route('participant.athletes') }}" style="text-decoration: none; color: #666;">Atlet Saya</a></li>
        <li class="breadcrumb-item active" aria-current="page">Dokumen</li>
      </ol>
    </nav>
@endsection

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold text-primary mb-0" style="color: #003399 !important;">Daftar Dokumen</h5>
        <div>
            <button id="btn-add-dokumen" class="btn rounded-pill px-4 text-white d-inline-flex align-items-center" style="background-color: #52c41a; border: none;">
                <span class="material-icons me-1" style="font-size: 1.1rem;">add</span> Tambah
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
        
        @php
            $docs = [];
            if ($athlete->surat_keterangan_path) $docs[] = ['jenis' => 'Surat Keterangan Sekolah/PT/Club', 'path' => $athlete->surat_keterangan_path];
            if ($athlete->akta_kelahiran_path) $docs[] = ['jenis' => 'Akta Kelahiran/KTP', 'path' => $athlete->akta_kelahiran_path];
        @endphp
        
        <div class="table-responsive">
            <table class="table table-bordered table-striped text-center align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="color: #666; font-size: 0.9rem; font-weight: 600;">#</th>
                        <th style="color: #666; font-size: 0.9rem; font-weight: 600;">Jenis Dokumen</th>
                        <th style="color: #666; font-size: 0.9rem; font-weight: 600;">Aksi</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.9rem; color: #444;">
                    @forelse($docs as $index => $doc)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $doc['jenis'] }}</td>
                            <td>
                                <a href="{{ Storage::url($doc['path']) }}" target="_blank" class="btn btn-sm text-white p-1" style="background-color: #33a5ff;" title="Lihat Dokumen">
                                    <span class="material-icons" style="font-size: 1.1rem;">visibility</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center bg-light text-muted py-3">Tidak ada data yang tersedia pada tabel ini</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-3">
            <small class="text-muted">Menampilkan {{ count($docs) > 0 ? 1 : 0 }} sampai {{ count($docs) }} dari {{ count($docs) }} entri</small>
            <ul class="pagination pagination-sm mb-0">
                <li class="page-item border disabled"><a class="page-link border-0 text-muted" href="#">Sebelumnya</a></li>
                <li class="page-item border active"><a class="page-link border-0 text-white" style="background-color: #003399;" href="#">1</a></li>
                <li class="page-item border disabled"><a class="page-link border-0 text-muted" href="#">Selanjutnya</a></li>
            </ul>
        </div>
    </div>
</div>

<!-- Modal Tambah Dokumen -->
<div id="modal-dokumen" class="custom-modal-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;z-index:1050;">
    <div class="bg-white p-4 rounded shadow" style="width:100%;max-width:550px;max-height:90vh;overflow-y:auto; position: relative;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0 text-secondary">Tambah Dokumen</h5>
            <button type="button" class="btn-close" style="font-size: 0.8rem;" aria-label="Close" onclick="closeDocModal()"></button>
        </div>
        
        <div class="alert alert-info border-0 rounded-3 mb-4" style="background-color: #e6f7ff; color: #0050b3; font-size: 0.85rem;">
            Silakan unggah dokumen yang diperlukan. Keduanya diperlukan agar status dokumen menjadi <strong>Lengkap</strong>.
        </div>
        
        <form id="form-dokumen">
            @csrf
            
            <div class="mb-3 position-relative">
                <label class="form-label fw-bold" style="font-size: 0.85rem; color: #555;">1. Surat Keterangan Sekolah/PT/Club</label>
                <input class="form-control" type="file" name="surat_keterangan" accept=".pdf,.png,.jpg,.jpeg" style="border-color: #e0e6ed; font-size: 0.9rem;">
            </div>
            
            <div class="mb-4 position-relative">
                <label class="form-label fw-bold" style="font-size: 0.85rem; color: #555;">2. Akta Kelahiran/KTP</label>
                <input class="form-control" type="file" name="akta_kelahiran" accept=".pdf,.png,.jpg,.jpeg" style="border-color: #e0e6ed; font-size: 0.9rem;">
            </div>
            
            <div class="text-end">
                <button type="submit" class="btn px-4" style="background-color: #003399; color: white; font-weight: 500;">Kirim</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const btnAddDoc = document.getElementById('btn-add-dokumen');
    const docModal = document.getElementById('modal-dokumen');
    const formDokumen = document.getElementById('form-dokumen');

    btnAddDoc.addEventListener('click', () => {
        docModal.style.display = 'flex';
    });
    
    function closeDocModal() {
        docModal.style.display = 'none';
        formDokumen.reset();
    }

    formDokumen.addEventListener('submit', (e) => {
        e.preventDefault();
        
        const formData = new FormData(formDokumen);
        
        fetch('{{ route("athletes.upload_document", $athlete->id) }}', {
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
                closeDocModal();
                setTimeout(() => location.reload(), 500);
            } else if (data.errors) {
                let errorMsg = '❌ Validasi error:\n';
                for (let field in data.errors) {
                    errorMsg += '- ' + field + ': ' + data.errors[field][0] + '\n';
                }
                alert(errorMsg);
            } else {
                alert('❌ Error: ' + (data.message || 'Gagal menyimpan dokumen'));
            }
        })
        .catch(err => {
            alert('❌ Error: ' + err.message);
        });
    });
</script>
@endpush
@endsection
