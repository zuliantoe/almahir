@extends('layouts.app')

@section('title', 'Tambah Kategori Pelajaran')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tambah Kategori Pelajaran</h3>
                    <div class="card-tools">
                        <a href="{{ route('akademik.kategori-pelajaran.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('akademik.kategori-pelajaran.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="kategori">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="kategori"
                                   id="kategori"
                                   class="form-control @error('kategori') is-invalid @enderror"
                                   value="{{ old('kategori') }}"
                                   maxlength="100"
                                   required>
                            @error('kategori')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group text-right">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                            <a href="{{ route('akademik.kategori-pelajaran.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
