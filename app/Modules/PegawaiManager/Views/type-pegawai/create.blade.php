@extends('layouts.app')

@section('title', $title)

@push('styles')
<style>
    .glass-panel-card {
        border-radius: 16px;
        background: #ffffff;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.8);
        overflow: hidden;
    }
    
    .form-group label {
        font-weight: 700;
        color: #334155;
        letter-spacing: 0.5px;
    }
    
    .form-control {
        border-radius: 10px;
        border: 2px solid #e2e8f0;
        padding: 0.75rem 1rem;
        color: #1e293b;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        border-color: #4cc9f0;
        box-shadow: 0 0 0 0.25rem rgba(76, 201, 240, 0.25);
    }
    
    .btn-gradient-primary { background: linear-gradient(135deg, #4361ee, #4cc9f0); color: white; border: none; transition: all 0.3s ease; padding: 10px 24px; font-weight: bold;}
    .btn-gradient-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(67, 97, 238, 0.4); color: white; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4 p-3 animate__animated animate__headShake" style="border-radius: 12px; background: #fef2f2; border-left: 5px solid #ef4444 !important; color: #b91c1c;">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle fa-2x mr-3 text-danger"></i>
                <div>
                    <h6 class="font-weight-bold mb-1">Terdapat Kesalahan</h6>
                    <ul class="mb-0 pl-3 small">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="close text-danger" data-dismiss="alert" aria-label="Close" style="opacity: 0.7; padding: 1rem;">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card glass-panel-card mb-4">
                <div class="card-header bg-white p-4 border-bottom d-flex flex-wrap justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1 font-weight-bold text-dark"><i class="fas fa-plus-circle text-primary mr-2"></i> Tambah Tipe Pegawai</h5>
                        <p class="text-muted small mb-0 mt-1">Buat kategori tipe pegawai baru.</p>
                    </div>
                </div>

                <div class="card-body p-4 p-md-5 bg-light">
                    <form action="{{ route('pegawaimanager.types.store') }}" method="POST">
                        @csrf
                        
                        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                            <div class="card-body p-4">
                                <div class="form-group mb-4">
                                    <label for="nama_type" class="text-uppercase small"><i class="fas fa-tag text-muted mr-1"></i> Nama Tipe Pegawai <span class="text-danger">*</span></label>
                                    <input type="text"
                                           name="nama_type"
                                           id="nama_type"
                                           class="form-control @error('nama_type') is-invalid @enderror"
                                           value="{{ old('nama_type') }}"
                                           placeholder="Contoh: Guru Tetap, Staf TU, Cleaning Service"
                                           required>
                                    @error('nama_type')
                                        <div class="invalid-feedback font-weight-bold">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted mt-2"><i class="fas fa-info-circle mr-1"></i>Masukkan nama kategori yang representatif untuk pegawai.</small>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('pegawaimanager.types.index') }}" class="btn btn-light rounded-pill px-4 py-2 font-weight-bold mr-2 shadow-sm" style="border: 1px solid #e2e8f0; color: #475569;">
                                <i class="fas fa-times mr-2"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-gradient-primary rounded-pill shadow-sm">
                                <i class="fas fa-save mr-2"></i> Simpan Tipe Pegawai
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
