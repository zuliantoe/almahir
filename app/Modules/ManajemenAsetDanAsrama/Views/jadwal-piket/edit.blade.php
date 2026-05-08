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
            <li class="breadcrumb-item"><a href="{{ route('manajemenasetdanasrama.jadwal-piket.index') }}">Jadwal Piket</a></li>
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
            <x-card title="Edit Jadwal Piket" icon="fas fa-edit">
                <form action="{{ route('manajemenasetdanasrama.jadwal-piket.update', $jadwal->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kamar_id">Pilih Kamar <span class="text-danger">*</span></label>
                                <select class="form-control @error('kamar_id') is-invalid @enderror" id="kamar_id" name="kamar_id" required>
                                    @foreach($kamar as $k)
                                        <option value="{{ $k->id }}" {{ old('kamar_id', $jadwal->kamar_id) == $k->id ? 'selected' : '' }}>{{ $k->nama_kamar }}</option>
                                    @endforeach
                                </select>
                                @error('kamar_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tanggal">Tanggal Piket <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('tanggal') is-invalid @enderror" id="tanggal" name="tanggal" value="{{ old('tanggal', $jadwal->tanggal ? $jadwal->tanggal->format('Y-m-d') : '') }}" required>
                                @error('tanggal')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="siswa_id">Siswa <span class="text-danger">*</span></label>
                        <select class="form-control @error('siswa_id') is-invalid @enderror" id="siswa_id" name="siswa_id" required>
                            @foreach($siswa as $s)
                                <option value="{{ $s->id }}" {{ old('siswa_id', $jadwal->siswa_id) == $s->id ? 'selected' : '' }}>{{ $s->nama }}</option>
                            @endforeach
                        </select>
                        @error('siswa_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save mr-1"></i> Update
                        </button>
                        <a href="{{ route('manajemenasetdanasrama.jadwal-piket.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</div>
@endsection
