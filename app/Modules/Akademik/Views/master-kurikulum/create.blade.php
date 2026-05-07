@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold text-primary">
                        <i class="fas fa-plus mr-1"></i> {{ $title }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('akademik.master-kurikulum.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                    </div>
                </div>
                <form action="{{ route('akademik.master-kurikulum.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="nama_kurikulum">Nama Kurikulum <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="nama_kurikulum" 
                                   id="nama_kurikulum" 
                                   class="form-control @error('nama_kurikulum') is-invalid @enderror" 
                                   placeholder="Contoh: Kurikulum Merdeka, Kurikulum 2013" 
                                   value="{{ old('nama_kurikulum') }}" 
                                   required>
                            @error('nama_kurikulum')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Masukkan nama resmi kurikulum yang digunakan.</small>
                        </div>

                        <div class="form-group mt-4">
                            <label class="d-block">Status Kurikulum <span class="text-danger">*</span></label>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="status1" name="status" class="custom-control-input" value="1" {{ old('status', '1') == '1' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="status1">Aktif</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="status0" name="status" class="custom-control-input" value="0" {{ old('status') == '0' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="status0">Tidak Aktif</label>
                            </div>
                            @error('status')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer bg-light text-right">
                        <button type="reset" class="btn btn-secondary shadow-sm mr-2">
                            <i class="fas fa-undo mr-1"></i> Reset
                        </button>
                        <button type="submit" class="btn btn-primary shadow-sm">
                            <i class="fas fa-save mr-1"></i> Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
