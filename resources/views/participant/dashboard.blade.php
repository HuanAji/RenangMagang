@extends('layouts.participant')
@section('title', 'Dashboard')

@section('breadcrumb')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
    </div>
@endsection

@section('content')
<div class="row">
    <!-- Kompetisi Aktif -->
    <div class="col-md-3 mb-4">
        <div class="card h-100 border-0 shadow-sm rounded-0">
            <div class="card-body d-flex p-0">
                <div class="text-white p-4 d-flex align-items-center justify-content-center" style="width: 80px; background-color: #003399 !important;">
                    <span class="material-icons" style="font-size: 2.5rem;">pool</span>
                </div>
                <div class="p-3 bg-white w-100">
                    <h6 class="text-muted mb-1">Kompetisi Aktif</h6>
                    <h3 class="mb-0">{{ $kompetisiAktif }}</h3>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Cabang Lomba -->
    <div class="col-md-3 mb-4">
        <div class="card h-100 border-0 shadow-sm rounded-0">
            <div class="card-body d-flex p-0">
                <div class="text-white p-4 d-flex align-items-center justify-content-center" style="width: 80px; background-color: #ff4d4f !important;">
                    <span class="material-icons" style="font-size: 2.5rem;">emoji_events</span>
                </div>
                <div class="p-3 bg-white w-100">
                    <h6 class="text-muted mb-1">Cabang Lomba Didaftarkan</h6>
                    <h3 class="mb-0">{{ $cabangDidaftarkan }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Tagihan -->
    <div class="col-md-3 mb-4">
        <div class="card h-100 border-0 shadow-sm rounded-0">
            <div class="card-body d-flex p-0">
                <div class="text-white p-4 d-flex align-items-center justify-content-center" style="width: 80px; background-color: #fca311 !important;">
                    <h3 class="mb-0 fw-bold">Rp</h3>
                </div>
                <div class="p-3 bg-white w-100">
                    <h6 class="text-muted mb-1">Tagihan Tervalidasi</h6>
                    <h3 class="mb-0">{{ $tagihanTervalidasi }} / <span class="text-muted fs-5">{{ $totalTagihan }}</span></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Atlet Terdaftar -->
    <div class="col-md-3 mb-4">
        <div class="card h-100 border-0 shadow-sm rounded-0">
            <div class="card-body d-flex p-0">
                <div class="text-white p-4 d-flex align-items-center justify-content-center" style="width: 80px; background-color: #52c41a !important;">
                    <span class="material-icons" style="font-size: 2.5rem;">groups</span>
                </div>
                <div class="p-3 bg-white w-100">
                    <h6 class="text-muted mb-1">Atlet Terdaftar</h6>
                    <h3 class="mb-0">{{ $atletTerdaftar }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Daftar Peserta Table -->
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold text-primary mb-0" style="color: #003399 !important;">Daftar Peserta</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <div class="d-flex align-items-center">
                        Tampilkan 
                        <select class="form-select mx-2 form-select-sm" style="width: auto;">
                            <option>10</option>
                            <option>25</option>
                            <option>50</option>
                        </select>
                        entri
                    </div>
                    <div>
                        <label>Cari: <input type="search" class="form-control form-control-sm d-inline-block w-auto"></label>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped text-center">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Kompetisi</th>
                                <th>Nomor Lomba</th>
                                <th>Status Peserta</th>
                                <th>Status Kompetisi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($peserta as $index => $p)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $p->athlete->nama ?? '-' }}</td>
                                <td>SwimPool Competition</td>
                                <td>{{ $p->event->nama_event ?? '-' }}</td>
                                <td><span class="badge bg-success">Terdaftar</span></td>
                                <td><span class="badge bg-primary">Aktif</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center bg-light">Tidak ada data yang tersedia pada tabel ini</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <small>Menampilkan 1 sampai {{ count($peserta) }} dari {{ count($peserta) }} entri</small>
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item disabled"><a class="page-link" href="#">Sebelumnya</a></li>
                        <li class="page-item disabled"><a class="page-link" href="#">Selanjutnya</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Aktivitas Terbaru -->
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold text-primary mb-0" style="color: #003399 !important;">Aktivitas Terbaru</h5>
            </div>
            <div class="card-body">
                <!-- Data goes here -->
                <p class="text-muted text-center py-4">Belum ada aktivitas.</p>
            </div>
        </div>
    </div>
</div>
@endsection
