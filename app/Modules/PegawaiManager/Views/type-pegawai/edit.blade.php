@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <x-card title="Edit Tipe Pegawai" icon="fas fa-edit">

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
            <form action="{{ route('pegawaimanager.types.update', $type->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Info tipe saat ini --}}
                <div class="form-group row mb-4">
                    <label class="col-sm-3 col-form-label text-muted">
                        <i class="fas fa-info-circle mr-1"></i> ID Tipe
                    </label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control bg-light" value="{{ $type->id }}" readonly disabled>
                        <small class="text-muted">ID ini tidak bisa diubah.</small>
                    </div>
                </div>

                <hr class="mb-4">

                <div class="form-group row mb-4">
                    <label for="nama_type" class="col-sm-3 col-form-label">
                        <i class="fas fa-tag mr-1 text-muted"></i> Nama Tipe Pegawai <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-9">
                        <input type="text"
                               name="nama_type"
                               id="nama_type"
                               class="form-control @error('nama_type') is-invalid @enderror"
                               value="{{ old('nama_type', $type->nama_type) }}"
                               placeholder="Contoh: Guru Tetap, Staf TU, Cleaning Service"
                               required>
                        @error('nama_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted"><i class="fas fa-info-circle mr-1"></i>Perubahan nama tipe akan berdampak pada semua pegawai dengan tipe ini.</small>
                    </div>
                </div>

                <div class="form-group row mt-4">
                    <div class="col-sm-9 offset-sm-3">
                        <button type="submit" class="btn btn-warning px-4 py-2 shadow-sm rounded-pill btn-animate font-weight-bold" style="color: #fff !important; text-shadow: 0 1px 2px rgba(0,0,0,0.2);">
                            <i class="fas fa-save mr-1"></i> Perbarui Tipe Pegawai
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
