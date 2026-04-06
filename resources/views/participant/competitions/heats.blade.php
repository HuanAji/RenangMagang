@extends('layouts.participant')
@section('title', 'Heat & Jalur Lomba')

@push('styles')
<style>
    .heat-card {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }
    .heat-header {
        padding: 12px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .heat-header.pending { background: #f8f9fa; border-bottom: 2px solid #dee2e6; }
    .heat-header.active { background: #d4edda; border-bottom: 2px solid #28a745; }
    .heat-header.completed { background: #e2e3e5; border-bottom: 2px solid #6c757d; }
    .lane-row {
        display: flex;
        align-items: center;
        padding: 10px 20px;
        border-bottom: 1px solid #f1f3f5;
        font-size: 0.92rem;
    }
    .lane-row:last-child { border-bottom: none; }
    .lane-number {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #003399, #0055cc);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1rem;
        margin-right: 16px;
        flex-shrink: 0;
    }
    .lane-athlete {
        flex: 1;
    }
    .lane-record {
        color: #6c757d;
        font-size: 0.85rem;
        min-width: 120px;
        text-align: right;
    }
    .filter-bar {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
</style>
@endpush

@section('breadcrumb')
    <div class="pt-3 pb-2 mb-3 border-bottom w-100">
        <h5 class="fw-bold text-primary mb-0" style="color: #003399 !important;">Heat & Penugasan Jalur</h5>
    </div>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb bg-transparent mb-0 px-0 py-1 text-muted">
        <li class="breadcrumb-item">Kompetisi</li>
        <li class="breadcrumb-item">Heat & Jalur</li>
      </ol>
    </nav>
@endsection

@section('content')
{{-- Filter Bar --}}
<div class="filter-bar">
    <form method="GET" action="{{ route('participant.competitions.heats') }}" class="row g-3 align-items-end">
        <div class="col-md-5">
            <label class="form-label fw-bold" style="font-size: 0.85rem; color: #555;">Nomor Event / Lomba</label>
            <select name="event_id" class="form-select" required style="border-color: #e0e6ed;">
                <option value="" disabled {{ !$selectedEventId ? 'selected' : '' }}>-- Pilih Nomor Event --</option>
                @foreach($events as $evt)
                    <option value="{{ $evt->id }}" {{ $selectedEventId == $evt->id ? 'selected' : '' }}>
                        {{ str_pad($evt->id, 3, '0', STR_PAD_LEFT) }} - {{ $evt->nama_event }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold" style="font-size: 0.85rem; color: #555;">Jenis Kelamin</label>
            <select name="jenis_kelamin" class="form-select" required style="border-color: #e0e6ed;">
                <option value="" disabled {{ !$selectedGender ? 'selected' : '' }}>-- Pilih --</option>
                <option value="L" {{ $selectedGender == 'L' ? 'selected' : '' }}>Putra</option>
                <option value="P" {{ $selectedGender == 'P' ? 'selected' : '' }}>Putri</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn w-100" style="background-color: #003399; color: white; font-weight: 500;">
                <span class="material-icons me-1" style="font-size: 1rem; vertical-align: middle;">search</span>Tampilkan
            </button>
        </div>
    </form>
</div>

{{-- Heat Cards --}}
@if($selectedEventId && $selectedGender)
    @if($heats->isEmpty())
        <div class="text-center bg-white rounded-3 shadow-sm p-5">
            <span class="material-icons text-muted" style="font-size: 4rem;">pool</span>
            <p class="text-muted mt-3 mb-0" style="font-size: 1rem;">Belum ada heat yang ter-generate untuk event & kategori ini.<br>
            Pastikan ada peserta yang sudah mendaftar.</p>
        </div>
    @else
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold text-secondary mb-0">
                {{ $heats->first()->event->nama_event ?? '' }} — {{ $selectedGender == 'L' ? 'Putra' : 'Putri' }}
            </h6>
            <span class="badge rounded-pill" style="background-color: #003399; font-size: 0.85rem; padding: 0.4rem 1rem;">
                {{ $heats->count() }} Heat · {{ $heats->sum(fn($h) => $h->laneAssignments->count()) }} Atlet
            </span>
        </div>

        @foreach($heats as $heat)
            <div class="heat-card">
                <div class="heat-header {{ $heat->status }}">
                    <div>
                        <strong style="font-size: 1rem;">Heat {{ $heat->heat_number }}</strong>
                        @if($heat->status === 'pending')
                            <span class="badge bg-secondary ms-2">Menunggu</span>
                        @elseif($heat->status === 'active')
                            <span class="badge bg-success ms-2">🔴 AKTIF</span>
                        @else
                            <span class="badge bg-dark ms-2">Selesai</span>
                        @endif
                    </div>
                </div>
                <div class="heat-body bg-white">
                    @foreach($heat->laneAssignments as $lane)
                        <div class="lane-row">
                            <div class="lane-number">{{ $lane->lane_number }}</div>
                            <div class="lane-athlete">
                                <strong>{{ $lane->athlete->nama ?? '-' }}</strong>
                                <small class="d-block text-muted">{{ $lane->athlete->asal_club_sekolah ?? '' }}</small>
                            </div>
                            <div class="lane-record">
                                @if($lane->result_time)
                                    <span class="fw-bold text-success">{{ $lane->result_time }}</span>
                                @else
                                    @php
                                        $bestTr = $lane->athlete->trackRecords
                                            ->where('nomor_lomba', $heat->event->nama_event)
                                            ->sortBy('durasi_renang')
                                            ->first();
                                    @endphp
                                    @if($bestTr)
                                        <span title="Track Record">🏅 {{ $bestTr->durasi_renang }}</span>
                                    @else
                                        <span class="text-muted">No Record</span>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif
@else
    <div class="text-center bg-white rounded-3 shadow-sm p-5">
        <span class="material-icons text-muted" style="font-size: 4rem;">filter_list</span>
        <p class="text-muted mt-3 mb-0">Silakan pilih <strong>Nomor Event</strong> dan <strong>Jenis Kelamin</strong> untuk melihat pembagian heat.</p>
    </div>
@endif

@endsection
