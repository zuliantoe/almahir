@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
        <a href="{{ route('keuangan.sumbers.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 mr-2 d-none d-sm-inline-block"></i> Kembali
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 mb-4 rounded-xl overflow-hidden">
                <div class="card-header py-3 bg-white border-bottom border-light">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-plus-circle mr-2"></i> Tambah Data Baru</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('keuangan.sumbers.store') }}" method="POST">
                        @csrf
                        <div class="form-group mb-4">
                            <label for="nama" class="small font-weight-bold text-muted mb-2">Nama Sumber Pemasukan</label>
                            <input type="text" 
                                   class="form-control form-control-user bg-light border-0 shadow-sm rounded-lg @error('nama') is-invalid @enderror" 
                                   id="nama" 
                                   name="nama" 
                                   placeholder="Contoh: Donasi Alumni, Dana BOS, dll" 
                                   value="{{ old('nama') }}" 
                                   required 
                                   autofocus>
                            @error('nama')
                                <div class="invalid-feedback ml-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="reset" class="btn btn-light mr-3 px-4">Reset</button>
                            <button type="submit" class="btn btn-primary px-5 shadow-sm">
                                <i class="fas fa-save mr-2"></i> Simpan Sumber
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .rounded-xl { border-radius: 12px; }
    .rounded-lg { border-radius: 10px; }
    .bg-light { background-color: #f8f9fc !important; }
    .form-control:focus {
        background-color: #ffffff !important;
        border: 1px solid #4e73df !important;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.1) !important;
    }
</style>
@endsection
