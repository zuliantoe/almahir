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
            <li class="breadcrumb-item"><a href="{{ route('manajemenasetdanasrama.aset.index') }}">Master Aset</a></li>
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
            <x-card title="Edit Aset: {{ $aset->nama_aset }}" icon="fas fa-edit">
                <form action="{{ route('manajemenasetdanasrama.aset.update', $aset->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="kode_aset">Kode Aset</label>
                        <input type="text" class="form-control" value="{{ $aset->kode_aset }}" disabled>
                        <small class="text-muted">Kode aset tidak dapat diubah</small>
                    </div>

                    <div class="form-group">
                        <label for="nama_aset">Nama Aset <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_aset') is-invalid @enderror" id="nama_aset" name="nama_aset" value="{{ old('nama_aset', $aset->nama_aset) }}" required>
                        @error('nama_aset')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="kamar_id">Lokasi Penempatan (Kamar) <span class="text-danger">*</span></label>
                        <select class="form-control @error('kamar_id') is-invalid @enderror" id="kamar_id" name="kamar_id" required>
                            <option value="">-- Pilih Kamar --</option>
                            @foreach($kamar as $k)
                            <option value="{{ $k->id }}" {{ old('kamar_id', $aset->kamar_id) == $k->id ? 'selected' : '' }}>{{ $k->nama_kamar }}</option>
                            @endforeach
                        </select>
                        @error('kamar_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="status_kondisi">Status Kondisi <span class="text-danger">*</span></label>
                        <select class="form-control @error('status_kondisi') is-invalid @enderror" id="status_kondisi" name="status_kondisi" required>
                            <option value="baik" {{ old('status_kondisi', $aset->status_kondisi) == 'baik' ? 'selected' : '' }}>Baik</option>
                            <option value="rusak" {{ old('status_kondisi', $aset->status_kondisi) == 'rusak' ? 'selected' : '' }}>Rusak</option>
                            <option value="dalam_perbaikan" {{ old('status_kondisi', $aset->status_kondisi) == 'dalam_perbaikan' ? 'selected' : '' }}>Dalam Perbaikan</option>
                            <option value="sudah_diperbaiki" {{ old('status_kondisi', $aset->status_kondisi) == 'sudah_diperbaiki' ? 'selected' : '' }}>Sudah Diperbaiki</option>
                        </select>
                        @error('status_kondisi')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="kondisi">Kondisi (Detail)</label>
                        <textarea class="form-control @error('kondisi') is-invalid @enderror" id="kondisi" name="kondisi" rows="3">{{ old('kondisi', $aset->kondisi) }}</textarea>
                        @error('kondisi')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="deskripsi_aset">Deskripsi Aset</label>
                        <textarea class="form-control @error('deskripsi_aset') is-invalid @enderror" id="deskripsi_aset" name="deskripsi_aset" rows="3">{{ old('deskripsi_aset', $aset->deskripsi_aset) }}</textarea>
                        @error('deskripsi_aset')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save mr-1"></i> Update
                        </button>
                        <a href="{{ route('manajemenasetdanasrama.aset.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</div>
@endsection
