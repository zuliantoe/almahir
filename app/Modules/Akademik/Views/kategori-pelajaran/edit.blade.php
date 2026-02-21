@extends('layouts.app')

@section('title', 'Edit Kategori Pelajaran')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Kategori Pelajaran</h3>
                    <div class="card-tools">
                        <a href="{{ route('akademik.kategori-pelajaran.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('akademik.kategori-pelajaran.update', $kategoriPelajaran->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="kategori">Nama Kategori</label>
                            <input type="text"
                                   name="kategori"
                                   id="kategori"
                                   class="form-control @error('kategori') is-invalid @enderror"
                                   value="{{ old('kategori', $kategoriPelajaran->kategori) }}"
                                   maxlength="100"
                                   required>
                            @error('kategori')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group text-right">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update
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
