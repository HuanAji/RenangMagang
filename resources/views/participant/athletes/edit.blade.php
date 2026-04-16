@extends('layouts.participant')
@section('title', 'Edit Atlet')

@section('breadcrumb')
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom w-100">
        <h5 class="fw-bold mb-0" style="color: #003399;">Edit Data Atlet</h5>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent mb-0 px-0 py-1">
            <li class="breadcrumb-item"><a href="{{ route('participant.athletes') }}" style="text-decoration:none;color:#666;">Atlet Saya</a></li>
            <li class="breadcrumb-item active text-muted">Edit — {{ $athlete->nama }}</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="card shadow-sm border-0" style="max-width: 620px;">
    <div class="card-header bg-white border-0 pt-4 pb-0 d-flex align-items-center gap-2">
        <div style="width:40px;height:40px;border-radius:50%;background:#e8f0fe;display:flex;align-items:center;justify-content:center;">
            <span class="material-icons" style="color:#003399;font-size:1.3rem;">edit</span>
        </div>
        <h6 class="fw-bold mb-0" style="color:#003399;">Edit Data Atlet</h6>
    </div>

    <div class="card-body pt-4">

        {{-- Tampilkan error validasi --}}
        @if($errors->any())
            <div class="alert alert-danger mb-4">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('athletes.update', $athlete->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size:0.85rem;color:#555;">Nama Atlet</label>
                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                    value="{{ old('nama', $athlete->nama) }}" required>
                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size:0.85rem;color:#555;">Umur</label>
                <input type="number" name="umur" class="form-control @error('umur') is-invalid @enderror"
                    value="{{ old('umur', $athlete->umur) }}">
                @error('umur')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size:0.85rem;color:#555;">Jenis Kelamin</label>
                <div class="row g-0 border rounded overflow-hidden">
                    <div class="col-6">
                        <input type="radio" class="btn-check" name="jenis_kelamin" id="edit_laki" value="L"
                            {{ old('jenis_kelamin', $athlete->jenis_kelamin) === 'L' ? 'checked' : '' }} required>
                        <label class="btn w-100 rounded-0 border-0 m-0 py-2 d-flex justify-content-center align-items-center" for="edit_laki" style="font-size:0.9rem;">
                            <span class="material-icons me-2" style="font-size:1.1rem;">male</span> Laki-laki
                        </label>
                    </div>
                    <div class="col-6" style="border-left:1px solid #e0e6ed;">
                        <input type="radio" class="btn-check" name="jenis_kelamin" id="edit_perempuan" value="P"
                            {{ old('jenis_kelamin', $athlete->jenis_kelamin) === 'P' ? 'checked' : '' }}>
                        <label class="btn w-100 rounded-0 border-0 m-0 py-2 d-flex justify-content-center align-items-center" for="edit_perempuan" style="font-size:0.9rem;">
                            <span class="material-icons me-2" style="font-size:1.1rem;">female</span> Perempuan
                        </label>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size:0.85rem;color:#555;">Asal Klub/Sekolah</label>
                <input type="text" name="asal_club_sekolah" class="form-control @error('asal_club_sekolah') is-invalid @enderror"
                    value="{{ old('asal_club_sekolah', $athlete->asal_club_sekolah) }}">
                @error('asal_club_sekolah')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size:0.85rem;color:#555;">Provinsi</label>
                <input type="text" name="provinsi" class="form-control"
                    value="{{ old('provinsi', $athlete->provinsi) }}">
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold" style="font-size:0.85rem;color:#555;">Kabupaten / Kota</label>
                <input type="text" name="kabupaten_kota" class="form-control"
                    value="{{ old('kabupaten_kota', $athlete->kabupaten_kota) }}">
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('participant.athletes') }}" class="btn btn-light px-4">Batal</a>
                <button type="submit" class="btn text-white px-4" style="background-color:#003399;font-weight:500;">
                    <span class="material-icons me-1" style="font-size:1rem;vertical-align:middle;">save</span>Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-check:checked + .btn {
        background-color: #003399;
        color: white;
        border-color: #003399;
    }
</style>
@endpush
