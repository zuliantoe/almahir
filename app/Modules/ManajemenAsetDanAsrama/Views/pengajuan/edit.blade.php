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
            <li class="breadcrumb-item"><a href="{{ route('manajemenasetdanasrama.pengajuan.index') }}">Pengajuan</a></li>
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
            <x-card title="Edit Pengajuan Aset: {{ $pengajuan->nama_aset }}" icon="fas fa-edit">
                <form action="{{ route('manajemenasetdanasrama.pengajuan.update', $pengajuan->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="nomor_pengajuan">Nomor Pengajuan</label>
                        <input type="text" class="form-control" value="{{ $pengajuan->nomor_pengajuan }}" disabled>
                    </div>

                    <div class="form-group">
                        <label for="nama_aset">Nama Aset <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_aset') is-invalid @enderror" id="nama_aset" name="nama_aset" value="{{ old('nama_aset', $pengajuan->nama_aset) }}" required>
                        @error('nama_aset')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="deskripsi_pengajuan">Deskripsi Pengajuan <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('deskripsi_pengajuan') is-invalid @enderror" id="deskripsi_pengajuan" name="deskripsi_pengajuan" rows="4" required>{{ old('deskripsi_pengajuan', $pengajuan->deskripsi_pengajuan) }}</textarea>
                        @error('deskripsi_pengajuan')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="estimasi_harga">Estimasi Harga (Rp) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('estimasi_harga') is-invalid @enderror" id="estimasi_harga" name="estimasi_harga" value="{{ old('estimasi_harga', $pengajuan->estimasi_harga) }}" min="0" required>
                        @error('estimasi_harga')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save mr-1"></i> Update
                        </button>
                        <a href="{{ route('manajemenasetdanasrama.pengajuan.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</div>
@endsection
