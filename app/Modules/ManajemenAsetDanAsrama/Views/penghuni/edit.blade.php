@extends('layouts.app')

@section('title', $title)

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0">{{ $title }}</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('manajemenasetdanasrama.index') }}">Manajemen Aset & Asrama</a></li>
            <li class="breadcrumb-item"><a href="{{ route('manajemenasetdanasrama.penghuni.index') }}">Penghuni</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    @if(session('error'))
        <x-alert type="danger" :message="session('error')" dismissible />
    @endif

    <div class="row justify-content-center">
        <div class="col-md-8">
            <x-card title="Edit Penghuni Kamar" icon="fas fa-edit">
                <form action="{{ route('manajemenasetdanasrama.penghuni.update', $penghuni->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kamar_id">Kamar <span class="text-danger">*</span></label>
                                <select class="form-control select2 @error('kamar_id') is-invalid @enderror" id="kamar_id" name="kamar_id" required>
                                    <option value="">-- Pilih Kamar --</option>
                                    @foreach($kamar as $k)
                                        <option value="{{ $k->id }}" {{ old('kamar_id', $penghuni->kamar_id) == $k->id ? 'selected' : '' }}>
                                            {{ $k->nama_kamar }} (Kapasitas: {{ $k->kapasitas }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('kamar_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="siswa_id">Siswa <span class="text-danger">*</span></label>
                                <select class="form-control select2 @error('siswa_id') is-invalid @enderror" id="siswa_id" name="siswa_id" required>
                                    @foreach($siswa as $s)
                                        <option value="{{ $s->id }}" {{ old('siswa_id', $penghuni->siswa_id) == $s->id ? 'selected' : '' }}>
                                            {{ $s->nama }} (NIS: {{ $s->nis }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('siswa_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="jabatan">Jabatan <span class="text-danger">*</span></label>
                                <select class="form-control @error('jabatan') is-invalid @enderror" id="jabatan" name="jabatan" required>
                                    <option value="Anggota" {{ old('jabatan', $penghuni->jabatan) == 'Anggota' ? 'selected' : '' }}>Anggota</option>
                                    @if(!$hasKetua)
                                        <option value="Ketua Kamar" {{ old('jabatan', $penghuni->jabatan) == 'Ketua Kamar' ? 'selected' : '' }}>Ketua Kamar</option>
                                    @endif
                                    <option value="Wakil Ketua Kamar" {{ old('jabatan', $penghuni->jabatan) == 'Wakil Ketua Kamar' ? 'selected' : '' }}>Wakil Ketua</option>
                                </select>
                                @error('jabatan')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tanggal_masuk">Tanggal Masuk <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('tanggal_masuk') is-invalid @enderror" id="tanggal_masuk" name="tanggal_masuk" value="{{ old('tanggal_masuk', $penghuni->tanggal_masuk ? $penghuni->tanggal_masuk->format('Y-m-d') : '') }}" required>
                                @error('tanggal_masuk')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tanggal_keluar">Tanggal Keluar</label>
                                <input type="date" class="form-control @error('tanggal_keluar') is-invalid @enderror" id="tanggal_keluar" name="tanggal_keluar" value="{{ old('tanggal_keluar', $penghuni->tanggal_keluar ? $penghuni->tanggal_keluar->format('Y-m-d') : '') }}">
                                @error('tanggal_keluar')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" rows="3">{{ old('keterangan', $penghuni->keterangan) }}</textarea>
                        @error('keterangan')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save mr-1"></i> Update
                        </button>
                        <a href="{{ route('manajemenasetdanasrama.penghuni.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
<style>
    .select2-container--bootstrap4 .select2-selection--single {
        height: calc(2.25rem + 12px) !important;
    }
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
        line-height: calc(2.25rem + 10px) !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: "-- Pilih --",
            allowClear: true
        });
    });
</script>
@endpush
