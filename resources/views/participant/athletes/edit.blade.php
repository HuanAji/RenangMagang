@extends('layouts.participant')
@section('title', 'Edit Atlet')

@push('styles')
<style>
    .edit-container {
        max-width: 780px;
        margin: 0 auto;
    }

    /* Header Card */
    .edit-header {
        background: linear-gradient(135deg, #003399 0%, #0055cc 100%);
        border-radius: 16px;
        padding: 28px 30px;
        color: white;
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(0, 51, 153, 0.2);
    }
    .edit-avatar {
        width: 64px; height: 64px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.6rem; font-weight: 800;
        flex-shrink: 0;
        border: 3px solid rgba(255,255,255,0.3);
    }
    .edit-header-info h4 {
        margin: 0; font-weight: 800; font-size: 1.3rem;
    }
    .edit-header-info p {
        margin: 4px 0 0; font-size: 0.85rem; opacity: 0.8;
    }

    /* Form Card */
    .edit-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        overflow: hidden;
    }
    .edit-card-header {
        padding: 20px 28px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .edit-card-header .icon-wrap {
        width: 36px; height: 36px;
        border-radius: 10px;
        background: linear-gradient(135deg, #e8eef8, #dbeafe);
        display: flex; align-items: center; justify-content: center;
    }
    .edit-card-header .icon-wrap .material-icons {
        font-size: 1.2rem; color: #003399;
    }
    .edit-card-header h6 {
        margin: 0; font-weight: 700; color: #1e293b; font-size: 0.95rem;
    }
    .edit-card-body {
        padding: 28px;
    }

    /* Form styling */
    .form-section-title {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 1px solid #f1f5f9;
    }
    .form-label-custom {
        font-size: 0.82rem;
        font-weight: 600;
        color: #475569;
        margin-bottom: 6px;
    }
    .form-control-custom {
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 0.9rem;
        transition: all 0.2s ease;
        background: #f8fafc;
    }
    .form-control-custom:focus {
        border-color: #003399;
        box-shadow: 0 0 0 3px rgba(0, 51, 153, 0.1);
        background: white;
    }

    /* Gender toggle */
    .gender-toggle {
        display: flex;
        gap: 10px;
    }
    .gender-option {
        flex: 1;
        position: relative;
    }
    .gender-option input { display: none; }
    .gender-option label {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 11px 14px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.88rem;
        color: #64748b;
        background: #f8fafc;
        transition: all 0.2s ease;
    }
    .gender-option label:hover {
        border-color: #94a3b8;
        background: #f1f5f9;
    }
    .gender-option input:checked + label {
        border-color: #003399;
        background: linear-gradient(135deg, #e8eef8, #dbeafe);
        color: #003399;
    }
    .gender-option label .material-icons {
        font-size: 1.2rem;
    }

    /* Buttons */
    .btn-cancel {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 24px;
        font-weight: 600;
        font-size: 0.88rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }
    .btn-cancel:hover {
        background: #e2e8f0;
        color: #1e293b;
    }
    .btn-save {
        background: linear-gradient(135deg, #003399 0%, #0055cc 100%);
        color: white;
        border: none;
        border-radius: 10px;
        padding: 10px 28px;
        font-weight: 600;
        font-size: 0.88rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.25s ease;
    }
    .btn-save:hover {
        background: linear-gradient(135deg, #002277 0%, #003399 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 51, 153, 0.25);
    }
    .btn-save .material-icons, .btn-cancel .material-icons {
        font-size: 1.1rem;
    }

    /* Alert styles */
    .alert-modern {
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 0.85rem;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        border: none;
    }
    .alert-modern .material-icons {
        font-size: 1.2rem;
        flex-shrink: 0;
        margin-top: 1px;
    }

    /* Form row responsive grid */
    .form-row-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    /* Event checkbox styles */
    .event-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 10px;
    }
    .event-checkbox-card {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 14px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        background: #f8fafc;
    }
    .event-checkbox-card:hover { background: #f1f5f9; border-color: #cbd5e1; }
    .event-checkbox-card.selected {
        border-color: #003399;
        background: #f0f5ff;
    }
    .event-checkbox-card input[type="checkbox"] {
        margin-top: 3px;
        width: 16px;
        height: 16px;
        accent-color: #003399;
    }
    .event-checkbox-details {
        display: flex; flex-direction: column;
    }
    .event-checkbox-details strong { font-size: 0.88rem; color: #1e293b; line-height: 1.2; margin-bottom: 2px; }
    .event-checkbox-details span { font-size: 0.72rem; color: #64748b; }

    @media (max-width: 576px) {
        .form-row-2 { grid-template-columns: 1fr; }
        .edit-header { flex-direction: column; text-align: center; }
        .event-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('breadcrumb')
    <div class="pt-3 pb-2 mb-1">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size: 0.85rem;">
                <li class="breadcrumb-item">
                    <a href="{{ route('participant.athletes') }}" style="text-decoration:none; color:#003399; font-weight:600;">
                        <span class="material-icons" style="font-size:0.95rem; vertical-align:middle;">groups</span> Daftar Atlet
                    </a>
                </li>
                <li class="breadcrumb-item active text-muted">Edit — {{ $athlete->nama }}</li>
            </ol>
        </nav>
    </div>
@endsection

@section('content')
<div class="edit-container">

    <!-- Header Card -->
    <div class="edit-header">
        <div class="edit-avatar">
            {{ strtoupper(substr($athlete->nama, 0, 1)) }}
        </div>
        <div class="edit-header-info">
            <h4>{{ $athlete->nama }}</h4>
            <p>
                <span class="material-icons" style="font-size:0.9rem; vertical-align:middle;">badge</span>
                ID #{{ str_pad($athlete->id, 4, '0', STR_PAD_LEFT) }} · 
                {{ $athlete->jenis_kelamin === 'L' ? 'Putra' : 'Putri' }} · 
                {{ $athlete->asal_club_sekolah ?? 'Belum ada klub' }}
            </p>
        </div>
    </div>

    {{-- Error Alert --}}
    @if($errors->any())
        <div class="alert-modern bg-danger bg-opacity-10 text-danger mb-3">
            <span class="material-icons">error</span>
            <div>
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Success Alert --}}
    @if(session('success'))
        <div class="alert-modern bg-success bg-opacity-10 text-success mb-3">
            <span class="material-icons">check_circle</span>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <!-- Form Card -->
    <div class="edit-card">
        <div class="edit-card-header">
            <div class="icon-wrap">
                <span class="material-icons">edit_note</span>
            </div>
            <h6>Edit Informasi Atlet</h6>
        </div>

        <div class="edit-card-body">
            <form method="POST" action="{{ route('athletes.update', $athlete->id) }}">
                @csrf
                @method('PUT')

                <!-- Section: Data Pribadi -->
                <div class="form-section-title">Data Pribadi</div>

                <div class="mb-3">
                    <label class="form-label-custom">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control form-control-custom @error('nama') is-invalid @enderror"
                        value="{{ old('nama', $athlete->nama) }}" placeholder="Masukkan nama lengkap" required>
                    @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-row-2 mb-3">
                    <div>
                        <label class="form-label-custom">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control form-control-custom"
                            value="{{ old('tanggal_lahir', $athlete->tanggal_lahir) }}">
                    </div>
                    <div>
                        <label class="form-label-custom">Jenis Kelamin <span class="text-danger">*</span></label>
                        <div class="gender-toggle">
                            <div class="gender-option">
                                <input type="radio" name="jenis_kelamin" id="edit_laki" value="L"
                                    {{ old('jenis_kelamin', $athlete->jenis_kelamin) === 'L' ? 'checked' : '' }} required>
                                <label for="edit_laki">
                                    <span class="material-icons">male</span> Putra
                                </label>
                            </div>
                            <div class="gender-option">
                                <input type="radio" name="jenis_kelamin" id="edit_perempuan" value="P"
                                    {{ old('jenis_kelamin', $athlete->jenis_kelamin) === 'P' ? 'checked' : '' }}>
                                <label for="edit_perempuan">
                                    <span class="material-icons">female</span> Putri
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section: Asal & Organisasi -->
                <div class="form-section-title" style="margin-top: 28px;">Asal & Organisasi</div>

                <div class="mb-4">
                    <label class="form-label-custom">Asal Klub / Sekolah</label>
                    <input type="text" name="asal_club_sekolah" class="form-control form-control-custom @error('asal_club_sekolah') is-invalid @enderror"
                        value="{{ old('asal_club_sekolah', $athlete->asal_club_sekolah) }}" placeholder="Contoh: SMA N 1 Surabaya">
                    @error('asal_club_sekolah')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <!-- Section: Event Lomba -->
                <div class="form-section-title" style="margin-top: 28px;">Event Perlombaan</div>
                <div class="mb-4">
                    <label class="form-label-custom d-block mb-3">Pilih Event yang Diikuti</label>
                    <div class="event-grid">
                        @foreach($events as $event)
                            @php
                                $isChecked = in_array($event->id, old('event_id', $registeredEventIds ?? []));
                            @endphp
                            <label class="event-checkbox-card {{ $isChecked ? 'selected' : '' }}" id="card_event_{{ $event->id }}">
                                <input type="checkbox" name="event_id[]" value="{{ $event->id }}" 
                                    onchange="document.getElementById('card_event_{{ $event->id }}').classList.toggle('selected', this.checked)"
                                    {{ $isChecked ? 'checked' : '' }}>
                                <div class="event-checkbox-details">
                                    <strong>{{ $event->nama_event }}</strong>
                                    <span>Event #{{ str_pad($event->id, 3, '0', STR_PAD_LEFT) }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex gap-3 justify-content-end pt-3" style="border-top: 1px solid #f1f5f9;">
                    <a href="{{ route('participant.athletes') }}" class="btn-cancel">
                        <span class="material-icons">arrow_back</span> Kembali
                    </a>
                    <button type="submit" class="btn-save">
                        <span class="material-icons">save</span> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
