@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <x-card title="Tambah Tipe Pegawai" icon="fas fa-plus">

        <x-slot name="tools">
            <a href="{{ route('pegawaimanager.types.index') }}" class="btn btn-secondary btn-sm rounded-pill px-3 shadow-sm btn-animate">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </x-slot>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible shadow-sm mb-4">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="fas fa-exclamation-circle mr-2"></i>
                <ul class="mb-0 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="p-4 glass-card mb-4">
            <form action="{{ route('pegawaimanager.types.store') }}" method="POST">
                @csrf

                <div class="form-group row mb-4">
                    <label for="nama_type" class="col-sm-3 col-form-label">
                        <i class="fas fa-tag mr-1 text-muted"></i> Nama Tipe Pegawai <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-9">
                        <input type="text"
                               name="nama_type"
                               id="nama_type"
                               class="form-control @error('nama_type') is-invalid @enderror"
                               value="{{ old('nama_type') }}"
                               placeholder="Contoh: Guru Tetap, Staf TU, Cleaning Service"
                               required>
                        @error('nama_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted"><i class="fas fa-info-circle mr-1"></i>Masukkan nama kategori atau tipe status pegawai.</small>
                    </div>
                </div>

                <div class="form-group row mt-4">
                    <div class="col-sm-9 offset-sm-3">
                        <button type="submit" class="btn btn-primary px-4 py-2 shadow-sm rounded-pill btn-animate gradient-primary border-0">
                            <i class="fas fa-save mr-1"></i> Simpan Tipe Pegawai
                        </button>
                        <a href="{{ route('pegawaimanager.types.index') }}" class="btn btn-secondary px-4 py-2 shadow-sm rounded-pill ml-2 btn-animate">
                            <i class="fas fa-times mr-1"></i> Batal
                        </a>
                    </div>
                </div>
            </form>
        </div>

    </x-card>
</div>
@endsection
