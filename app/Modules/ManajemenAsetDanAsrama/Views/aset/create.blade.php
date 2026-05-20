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
            <li class="breadcrumb-item active">Tambah Aset Langsung</li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="card-title mb-0 font-weight-bold">
                        <i class="fas fa-plus-circle mr-2"></i> Form Registrasi Aset Baru
                    </h5>
                </div>
                <form action="{{ route('manajemenasetdanasrama.aset.store') }}" method="POST">
                    @csrf
                    <div class="card-body p-4">
                        <div class="alert alert-info border-0 shadow-sm mb-4" style="background: #e7f3ff; color: #004085; border-radius: 10px;">
                            <i class="fas fa-info-circle mr-2"></i> Gunakan form ini untuk mendata aset yang sudah ada atau dibeli tanpa melalui alur pengajuan sistem.
                        </div>

                        <div class="row">
                            <div class="col-md-9">
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold text-muted text-uppercase">Nama Aset <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nama_aset') is-invalid @enderror" name="nama_aset" value="{{ old('nama_aset') }}" placeholder="Contoh: AC Panasonic 1PK" required>
                                    @error('nama_aset') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold text-muted text-uppercase">Jumlah <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('jumlah_aset') is-invalid @enderror" name="jumlah_aset" value="{{ old('jumlah_aset', 1) }}" min="1" max="500" required>
                                    @error('jumlah_aset') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold text-muted text-uppercase">Harga Perolehan (Rp) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white font-weight-bold text-success">Rp</span>
                                        </div>
                                        <input type="number" class="form-control @error('harga') is-invalid @enderror" name="harga" value="{{ old('harga') }}" placeholder="0" required>
                                        @error('harga') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold text-muted text-uppercase">Tanggal Pengadaan</label>
                                    <input type="date" class="form-control" name="tanggal_pengadaan" value="{{ old('tanggal_pengadaan', date('Y-m-d')) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold text-muted text-uppercase">Kondisi Aset</label>
                                    <input type="text" class="form-control" name="kondisi" value="{{ old('kondisi', 'Baik') }}" placeholder="Contoh: Baru / Bekas Berkualitas">
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-0">
                            <label class="small font-weight-bold text-muted text-uppercase">Deskripsi / Spesifikasi</label>
                            <textarea class="form-control" name="deskripsi_aset" rows="3" placeholder="Contoh: Warna Putih, No Seri: 12345, Garansi 1 Tahun">{{ old('deskripsi_aset') }}</textarea>
                        </div>
                    </div>
                    <div class="card-footer bg-light p-4 d-flex justify-content-between">
                        <a href="{{ route('manajemenasetdanasrama.aset.index') }}" class="btn btn-link text-muted font-weight-bold text-decoration-none">
                            <i class="fas fa-times mr-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary px-5 shadow-sm font-weight-bold" style="border-radius: 10px;">
                            <i class="fas fa-save mr-2"></i> Simpan Aset Baru
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
