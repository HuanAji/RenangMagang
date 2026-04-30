@extends('layouts.participant')
@section('title', 'Edit Atlet')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/edit-athlete.css') }}">
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
