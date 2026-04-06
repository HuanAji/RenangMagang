@extends('layouts.participant')
@section('title', 'Kompetisi Diikuti')

@section('breadcrumb')
    <div class="pt-3 pb-2 mb-3 border-bottom w-100">
        <h5 class="fw-bold text-primary mb-0" style="color: #003399 !important;">Kompetisi Diikuti</h5>
    </div>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb bg-transparent mb-0 px-0 py-1 text-muted">
        <li class="breadcrumb-item">Kompetisi</li>
        <li class="breadcrumb-item">Diikuti</li>
      </ol>
    </nav>
@endsection

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-0 pt-4 pb-0">
        <h5 class="fw-bold text-primary mb-0" style="color: #003399 !important;">Daftar Pendaftaran Saya</h5>
    </div>
    <div class="card-body mt-3">
        <div class="table-responsive">
            <table class="table table-bordered table-striped text-center align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">No</th>
                        <th>Nama Atlet</th>
                        <th>Kompetisi</th>
                        <th>Nomor Event (Lomba)</th>
                        <th>Tanggal Daftar</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.95rem;">
                    @forelse($registrations as $index => $reg)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $reg->athlete->nama ?? '-' }}</td>
                            <td>Tri Grantha Akswara Swimming Championship 2026</td>
                            <td>{{ $reg->event ? str_pad($reg->event->id, 3, '0', STR_PAD_LEFT) . ' - ' . $reg->event->nama_event : '-' }}</td>
                            <td>{{ $reg->created_at ? $reg->created_at->format('d M Y H:i') : '-' }}</td>
                            <td class="text-center"><span class="badge bg-success px-3 py-2 rounded-pill" style="font-weight: 500;">Terdaftar</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-muted p-5 text-center">Belum ada atlet yang didaftarkan ke kompetisi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
