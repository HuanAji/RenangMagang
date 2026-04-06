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
        <h5 class="fw-bold text-primary mb-0" style="color: #003399 !important;">List Atlet Dari Samator Swimming Club</h5>
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
            <button id="btn-add-peserta" class="btn rounded-pill px-4 me-2 d-inline-flex align-items-center text-white" style="background-color: #52c41a; border: none;">
                <span class="material-icons me-1" style="font-size: 1.1rem;">person_add</span> Tambah
            </button>
            <button class="btn rounded-pill px-4 d-inline-flex align-items-center" style="border: 1px solid #52c41a; color: #52c41a; background: white;">
                <span class="material-icons me-1" style="font-size: 1.1rem;">bolt</span> Tambah Banyak
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="d-flex justify-content-between mb-3 mt-3">
            <div class="d-flex align-items-center" style="font-size: 0.9rem; color: #666;">
                Tampilkan 
                <select class="form-select mx-2 form-select-sm" style="width: auto;">
                    <option>10</option>
                    <option>25</option>
                    <option>50</option>
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
                        <th style="color: #666; font-size: 0.9rem; font-weight: 600;">Nama</th>
                        <th style="color: #666; font-size: 0.9rem; font-weight: 600;">Umur</th>
                        <th style="color: #666; font-size: 0.9rem; font-weight: 600;">Jenis Kelamin</th>
                        <th style="color: #666; font-size: 0.9rem; font-weight: 600;">Kelengkapan Dokumen</th>
                        <th style="color: #666; font-size: 0.9rem; font-weight: 600;">Track Record</th>
                        <th style="color: #666; font-size: 0.9rem; font-weight: 600;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbody-peserta" style="font-size: 0.9rem; color: #444;">
                    @forelse($athletes as $index => $athlete)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $athlete->nama }}</td>
                            <td>{{ $athlete->umur ?? '-' }}</td>
                            <td>{{ $athlete->jenis_kelamin === 'L' ? 'Laki-laki' : ($athlete->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}</td>
                            <td>
                                @if($athlete->kelengkapan_dokumen === 'Belum Lengkap')
                                    <span class="badge rounded-pill px-3 py-2" style="background-color: #ff4d4f; font-weight: 500;">Belum Lengkap</span>
                                @else
                                    <span class="badge rounded-pill px-3 py-2" style="background-color: #52c41a; font-weight: 500;">Lengkap</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge rounded-pill px-3 py-2 text-dark" style="background-color: #ffc107; font-weight: 500;">{{ collect($athlete->trackRecords)->count() }} Kompetisi</span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('athletes.documents', $athlete->id) }}" class="btn btn-sm text-white p-1" style="background-color: #003399;" title="Dokumen">
                                        <span class="material-icons" style="font-size: 1.1rem;">description</span>
                                    </a>
                                    <a href="{{ route('athletes.track_records', $athlete->id) }}" class="btn btn-sm text-white p-1" style="background-color: #33a5ff;" title="Track Record">
                                        <span class="material-icons" style="font-size: 1.1rem;">account_tree</span>
                                    </a>
                                    <button class="btn btn-sm text-white p-1" style="background-color: #ff4d4f;" title="Hapus">
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
            <small class="text-muted">Menampilkan {{ count($athletes) > 0 ? 1 : 0 }} sampai {{ count($athletes) }} dari {{ count($athletes) }} entri</small>
            <ul class="pagination pagination-sm mb-0">
                <li class="page-item border disabled"><a class="page-link border-0 text-muted" href="#">Sebelumnya</a></li>
                <li class="page-item border active"><a class="page-link border-0 text-white" style="background-color: #003399;" href="#">1</a></li>
                <li class="page-item border disabled"><a class="page-link border-0 text-muted" href="#">Selanjutnya</a></li>
            </ul>
        </div>
    </div>
</div>

<!-- Modal Tambah Peserta -->
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
                <label class="form-label" style="font-size: 0.85rem; color: #555;">Umur</label>
                <input type="number" name="umur" class="form-control" placeholder="Umur" required style="border-color: #e0e6ed; font-size: 0.9rem;">
            </div>
            
            <div class="mb-3">
                <label class="form-label" style="font-size: 0.85rem; color: #555;">Asal Instansi Pendidikan</label>
                <div class="d-flex gap-2">
                    <select name="asal_club_sekolah" class="form-select text-muted" required style="border-color: #e0e6ed; font-size: 0.9rem;">
                        <option value="" disabled selected>Pilih Asal Sekolah</option>
                        <option value="Samator Swimming Club">Samator Swimming Club</option>
                        <option value="Universitas Gadjah Mada">Universitas Gadjah Mada</option>
                        <option value="Universitas Negeri Yogyakarta">Universitas Negeri Yogyakarta</option>
                        <option value="Lainnya">Lainnya...</option>
                    </select>
                    <button type="button" class="btn text-white px-3" style="background-color: #52c41a;">
                        <span class="material-icons" style="font-size: 1.1rem; vertical-align: middle;">add</span>
                    </button>
                </div>
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
            
            <div class="mb-4 mt-2">
                <small class="text-danger" style="font-size: 0.8rem;">*Pastikan ulang semua data yang diinputkan telah benar!</small>
            </div>
            
            <div class="mb-3">
                <label class="form-label" style="font-size: 0.85rem; color: #555;">Provinsi Asal Atlet</label>
                <select name="provinsi" class="form-select text-muted" required style="border-color: #e0e6ed; font-size: 0.9rem;">
                    <option value="" disabled selected>Pilih Asal Provinsi</option>
                    <option value="DIY">DI Yogyakarta</option>
                    <option value="Jawa Tengah">Jawa Tengah</option>
                    <option value="Jawa Timur">Jawa Timur</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="form-label" style="font-size: 0.85rem; color: #555;">Kabupaten/Kota Asal Atlet</label>
                <select name="kabupaten_kota" class="form-select text-muted" required style="border-color: #e0e6ed; font-size: 0.9rem; background-color: #f8f9fa;">
                    <option value="" disabled selected>Pilih provinsi terlebih dahulu</option>
                    <option value="Yogyakarta">Kota Yogyakarta</option>
                    <option value="Sleman">Sleman</option>
                    <option value="Bantul">Bantul</option>
                </select>
            </div>
            
            <div class="text-end mt-2">
                <button type="submit" class="btn px-4" style="background-color: #003399; color: white; font-weight: 500;">Kirim</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const btnAdd = document.getElementById('btn-add-peserta');
    const modal = document.getElementById('modal-peserta');
    const cancelBtn = document.getElementById('btn-cancel-peserta');
    const form = document.getElementById('form-peserta');

    btnAdd.addEventListener('click', () => {
        modal.style.display = 'flex';
    });

    cancelBtn.addEventListener('click', () => {
        modal.style.display = 'none';
        form.reset();
    });

    form.addEventListener('submit', (e) => {
        e.preventDefault();

        const formData = new FormData(form);
        
        fetch('{{ route("athletes.store") }}', {
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
                alert('✅ ' + data.message);
                modal.style.display = 'none';
                form.reset();
                setTimeout(() => location.reload(), 500);
            } else if (data.error) {
                alert('❌ Error: ' + data.error);
            } else if (data.errors) {
                let errorMsg = '❌ Validasi error:\n';
                for (let field in data.errors) {
                    errorMsg += '- ' + field + ': ' + data.errors[field][0] + '\n';
                }
                alert(errorMsg);
            } else {
                alert('❌ Error: Gagal menyimpan peserta');
            }
        })
        .catch(err => {
            alert('❌ Error: ' + err.message);
        });
    });
</script>
@endpush
@endsection
