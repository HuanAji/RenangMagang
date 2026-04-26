@extends('layouts.participant')
@section('title', 'Dashboard')

@section('breadcrumb')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
    </div>
@endsection

@section('content')
<style>
    .stat-card {
        position: relative;
        border-radius: 16px;
        padding: 24px 22px 18px;
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.06);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        min-height: 145px;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 28px rgba(0,0,0,0.1);
    }
    .stat-card .stat-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        margin-bottom: 10px;
        opacity: 0.7;
    }
    .stat-card .stat-value {
        font-size: 2.2rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 4px;
    }
    .stat-card .stat-sub {
        font-size: 0.78rem;
        opacity: 0.6;
    }
    .stat-card .stat-icon {
        position: absolute;
        top: 18px;
        right: 18px;
        font-size: 1.6rem;
        opacity: 0.35;
    }
    .stat-card .stat-sparkline {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 40px;
        opacity: 0.25;
    }
</style>

<div class="row g-3 mb-4">
    <!-- Total Klub/Sekolah -->
    <div class="col-md-3 col-sm-6">
        <div class="stat-card shadow-sm" style="background: linear-gradient(135deg, #ffffff 0%, #e8f0fe 100%); color: #1a3a6b;">
            <span class="material-icons stat-icon">domain</span>
            <div class="stat-label">Total Klub/Sekolah</div>
            <div class="stat-value">{{ $totalKlub }}</div>
            <div class="stat-sub">Tim yang berpartisipasi</div>
            <svg class="stat-sparkline" viewBox="0 0 200 40" preserveAspectRatio="none">
                <polyline fill="none" stroke="#003399" stroke-width="2" points="0,35 20,28 40,30 60,18 80,22 100,12 120,15 140,8 160,14 180,6 200,10"/>
                <linearGradient id="grad1" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#003399" stop-opacity="0.3"/>
                    <stop offset="100%" stop-color="#003399" stop-opacity="0"/>
                </linearGradient>
                <polyline fill="url(#grad1)" stroke="none" points="0,40 0,35 20,28 40,30 60,18 80,22 100,12 120,15 140,8 160,14 180,6 200,10 200,40"/>
            </svg>
        </div>
    </div>
    
    <!-- Atlet Terdaftar -->
    <div class="col-md-3 col-sm-6">
        <div class="stat-card shadow-sm" style="background: linear-gradient(135deg, #ffffff 0%, #ecfdf5 100%); color: #0a4a2e;">
            <span class="material-icons stat-icon">groups</span>
            <div class="stat-label">Atlet Terdaftar</div>
            <div class="stat-value">{{ $atletTerdaftar }}</div>
            <div class="stat-sub">Total peserta terdaftar</div>
            <svg class="stat-sparkline" viewBox="0 0 200 40" preserveAspectRatio="none">
                <polyline fill="none" stroke="#16a34a" stroke-width="2" points="0,35 25,30 50,25 75,28 100,18 125,22 150,14 175,10 200,6"/>
                <linearGradient id="grad4" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#16a34a" stop-opacity="0.3"/>
                    <stop offset="100%" stop-color="#16a34a" stop-opacity="0"/>
                </linearGradient>
                <polyline fill="url(#grad4)" stroke="none" points="0,40 0,35 25,30 50,25 75,28 100,18 125,22 150,14 175,10 200,6 200,40"/>
            </svg>
        </div>
    </div>

    <!-- Pendaftaran Lomba -->
    <div class="col-md-3 col-sm-6">
        <div class="stat-card shadow-sm" style="background: linear-gradient(135deg, #ffffff 0%, #fef3f2 100%); color: #6b1a1a;">
            <span class="material-icons stat-icon">assignment</span>
            <div class="stat-label">Pendaftaran Lomba</div>
            <div class="stat-value">{{ $totalPendaftaran }}</div>
            <div class="stat-sub">Total entri cabang lomba</div>
            <svg class="stat-sparkline" viewBox="0 0 200 40" preserveAspectRatio="none">
                <polyline fill="none" stroke="#dc2626" stroke-width="2" points="0,30 25,22 50,28 75,15 100,20 125,10 150,18 175,8 200,12"/>
                <linearGradient id="grad2" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#dc2626" stop-opacity="0.3"/>
                    <stop offset="100%" stop-color="#dc2626" stop-opacity="0"/>
                </linearGradient>
                <polyline fill="url(#grad2)" stroke="none" points="0,40 0,30 25,22 50,28 75,15 100,20 125,10 150,18 175,8 200,12 200,40"/>
            </svg>
        </div>
    </div>

    <!-- Total Heat -->
    <div class="col-md-3 col-sm-6">
        <div class="stat-card shadow-sm" style="background: linear-gradient(135deg, #ffffff 0%, #fef9ec 100%); color: #6b4a0a;">
            <span class="material-icons stat-icon">pool</span>
            <div class="stat-label">Total Heat Lomba</div>
            <div class="stat-value">{{ $totalHeat }}</div>
            <div class="stat-sub">Jadwal yang digenerate</div>
            <svg class="stat-sparkline" viewBox="0 0 200 40" preserveAspectRatio="none">
                <polyline fill="none" stroke="#f59e0b" stroke-width="2" points="0,32 30,25 60,30 90,18 120,22 150,12 180,16 200,8"/>
                <linearGradient id="grad3" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#f59e0b" stop-opacity="0.3"/>
                    <stop offset="100%" stop-color="#f59e0b" stop-opacity="0"/>
                </linearGradient>
                <polyline fill="url(#grad3)" stroke="none" points="0,40 0,32 30,25 60,30 90,18 120,22 150,12 180,16 200,8 200,40"/>
            </svg>
        </div>
    </div>
</div>

<style>
    /* Custom Scrollbar for panels */
    .custom-scroll::-webkit-scrollbar { width: 6px; }
    .custom-scroll::-webkit-scrollbar-track { background: transparent; }
    .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>

<div class="row g-3">
    <!-- Daftar Peserta Table -->
    <div class="col-md-8">
        <div style="background: linear-gradient(135deg, #ffffff 0%, #f0f4ff 100%); border-radius:16px; border:1px solid rgba(0,0,0,0.06); overflow:hidden;" class="shadow-sm">
            <div style="padding:22px 24px 0;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <div style="font-size:0.68rem; font-weight:700; text-transform:uppercase; letter-spacing:1.2px; color:#003399; opacity:0.6; margin-bottom:4px;">Daftar Peserta</div>
                        <div style="font-size:1.15rem; font-weight:800; color:#1a3a6b;">Peserta <em style="font-weight:400; font-style:italic;">terdaftar.</em></div>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <div style="position:relative;">
                            <span class="material-icons" style="position:absolute; left:10px; top:50%; transform:translateY(-50%); font-size:1rem; color:#94a3b8;">search</span>
                            <input type="search" class="form-control form-control-sm" placeholder="Cari peserta..." style="padding-left:32px; border-radius:10px; border:1px solid #e2e8f0; font-size:0.8rem; background:#f8fafc;">
                        </div>
                    </div>
                </div>
            </div>
            <div style="padding:0 24px 20px;">
                <div class="table-responsive custom-scroll" style="max-height: 420px; overflow-y: auto; border-radius:12px; border:1px solid #e8edf5;">
                    <table class="table mb-0" style="font-size:0.85rem;">
                        <thead style="position: sticky; top: 0; z-index: 10;">
                            <tr>
                                <th style="padding:12px 14px; font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#64748b; border:none; text-align:center; width:45px; background:#eef2f8; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">No</th>
                                <th style="padding:12px 14px; font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#64748b; border:none; background:#eef2f8; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">Nama</th>
                                <th style="padding:12px 14px; font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#64748b; border:none; background:#eef2f8; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">Nomor Lomba</th>
                                <th style="padding:12px 14px; font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#64748b; border:none; text-align:center; background:#eef2f8; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($peserta as $index => $p)
                            <tr style="border-bottom:1px solid #f1f5f9; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                                <td style="padding:12px 14px; text-align:center; color:#94a3b8; font-weight:600; border:none;">{{ $index + 1 }}</td>
                                <td style="padding:12px 14px; border:none;">
                                    <div style="font-weight:600; color:#1e293b;">{{ $p->athlete->nama ?? '-' }}</div>
                                    <div style="font-size:0.72rem; color:#94a3b8;">{{ $p->athlete->asal_club_sekolah ?? 'SwimPool Competition' }}</div>
                                </td>
                                <td style="padding:12px 14px; color:#475569; border:none;">{{ $p->event->nama_event ?? '-' }}</td>
                                <td style="padding:12px 14px; text-align:center; border:none;">
                                    <span style="display:inline-block; padding:3px 10px; border-radius:20px; font-size:0.7rem; font-weight:600; background:#dcfce7; color:#16a34a;">Terdaftar</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" style="padding:40px; text-align:center; border:none;">
                                    <span class="material-icons" style="font-size:2rem; color:#cbd5e1; display:block; margin-bottom:8px;">person_off</span>
                                    <span style="color:#94a3b8; font-size:0.85rem;">Belum ada peserta terdaftar</span>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3" style="font-size:0.78rem; color:#94a3b8;">
                    <small>Menampilkan {{ count($peserta) }} peserta</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Heat -->
    <div class="col-md-4">
        <div style="background: linear-gradient(135deg, #ffffff 0%, #f5f3ff 100%); border-radius:16px; border:1px solid rgba(0,0,0,0.06); overflow:hidden; height:100%;" class="shadow-sm">
            <div style="padding:22px 24px 0;">
                <div style="font-size:0.68rem; font-weight:700; text-transform:uppercase; letter-spacing:1.2px; color:#7c3aed; opacity:0.6; margin-bottom:4px;">Distribusi Heat</div>
                <div style="font-size:1.15rem; font-weight:800; color:#3b1a6b;">Daftar <em style="font-weight:400; font-style:italic;">Heat Lomba.</em></div>
            </div>
            <div class="custom-scroll" style="padding:20px 24px; max-height:400px; overflow-y:auto;">
                @if(isset($heatSummary) && $heatSummary->count() > 0)
                    @foreach($heatSummary as $eventId => $heats)
                        @php $eventName = $heats->first()->event->nama_event ?? 'Event ' . $eventId; @endphp
                        <div style="padding:12px 0; border-bottom:1px solid #f1f0f9;">
                            <div style="display:flex; align-items:flex-start; gap:10px;">
                                <div style="width:28px; height:28px; border-radius:8px; background:linear-gradient(135deg,#e0e7ff,#c7d2fe); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                    <span class="material-icons" style="font-size:1rem; color:#4f46e5;">water</span>
                                </div>
                                <div style="flex:1;">
                                    <div style="font-size:0.85rem; font-weight:700; color:#1e293b; margin-bottom:6px; line-height:1.3;">{{ $eventName }}</div>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($heats as $h)
                                            <span style="font-size:0.68rem; font-weight:600; background:rgba(79, 70, 229, 0.1); border:1px solid rgba(79, 70, 229, 0.2); color:#4338ca; padding:3px 8px; border-radius:12px;">
                                                {{ $h->kelompok_umur ?: 'Umum' }} <span style="opacity:0.5; margin:0 2px;">•</span> {{ $h->total_heats }} Heat
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div style="text-align:center; padding:40px 0;">
                        <span class="material-icons" style="font-size:2.5rem; color:#cbd5e1; display:block; margin-bottom:12px;">pool</span>
                        <span style="color:#94a3b8; font-size:0.85rem; display:block;">Belum ada Heat yang di-generate.</span>
                        <a href="{{ route('participant.competitions.heats') }}" class="btn btn-sm mt-3" style="background:#4f46e5; color:white; border-radius:8px; font-size:0.75rem; font-weight:600;">Generate Heat Sekarang</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
