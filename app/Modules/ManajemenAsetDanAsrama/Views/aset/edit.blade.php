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
            <li class="breadcrumb-item active">Edit Aset</li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header bg-warning text-dark py-3">
                    <h5 class="card-title mb-0 font-weight-bold">
                        <i class="fas fa-edit mr-2"></i> Perbarui Data Aset
                    </h5>
                </div>
                <form action="{{ route('manajemenasetdanasrama.aset.update', $aset->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold text-muted text-uppercase">Kode Aset</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light"><i class="fas fa-lock text-muted"></i></span>
                                        </div>
                                        <input type="text" class="form-control bg-light" value="{{ $aset->kode_aset }}" readonly disabled>
                                    </div>
                                    <small class="text-muted italic">Kode aset bersifat permanen.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold text-muted text-uppercase">Nama Aset <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nama_aset') is-invalid @enderror" name="nama_aset" value="{{ old('nama_aset', $aset->nama_aset) }}" required>
                                    @error('nama_aset') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold text-muted text-uppercase">Status Kondisi <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status_kondisi') is-invalid @enderror" name="status_kondisi" required>
                                        <option value="baik" {{ old('status_kondisi', $aset->status_kondisi) == 'baik' ? 'selected' : '' }}>Baik</option>
                                        <option value="rusak" {{ old('status_kondisi', $aset->status_kondisi) == 'rusak' ? 'selected' : '' }}>Rusak</option>
                                        <option value="dalam_perbaikan" {{ old('status_kondisi', $aset->status_kondisi) == 'dalam_perbaikan' ? 'selected' : '' }}>Dalam Perbaikan</option>
                                        <option value="sudah_diperbaiki" {{ old('status_kondisi', $aset->status_kondisi) == 'sudah_diperbaiki' ? 'selected' : '' }}>Sudah Diperbaiki</option>
                                    </select>
                                    @error('status_kondisi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="small font-weight-bold text-muted text-uppercase">Kondisi Fisik (Detail)</label>
                            <textarea class="form-control @error('kondisi') is-invalid @enderror" name="kondisi" rows="2" placeholder="Contoh: Lecet sedikit, baterai bocor, dsb...">{{ old('kondisi', $aset->kondisi) }}</textarea>
                            @error('kondisi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label class="small font-weight-bold text-muted text-uppercase">Deskripsi / Catatan Tambahan</label>
                            <textarea class="form-control @error('deskripsi_aset') is-invalid @enderror" name="deskripsi_aset" rows="3">{{ old('deskripsi_aset', $aset->deskripsi_aset) }}</textarea>
                            @error('deskripsi_aset') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="card-footer bg-light p-4 d-flex justify-content-between">
                        <a href="{{ route('manajemenasetdanasrama.aset.index') }}" class="btn btn-link text-muted font-weight-bold text-decoration-none">
                            <i class="fas fa-times mr-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-warning px-5 shadow-sm font-weight-bold" style="border-radius: 10px;">
                            <i class="fas fa-save mr-2"></i> Perbarui Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
