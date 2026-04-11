@extends('layouts.app')

@section('title', 'Edit Mata Pelajaran')

@include('akademik::components.style')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-modern">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-edit text-warning mr-2"></i> Edit Mata Pelajaran</h3>
                </div>
                
                <form method="POST" action="{{ route('akademik.mata-pelajaran.update', $mataPelajaran) }}">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        
                        <div class="form-group mb-4">
                            <label class="font-weight-bold">Kode Mata Pelajaran</label>
                            <input type="text" name="kode" value="{{ old('kode', $mataPelajaran->kode) }}" class="form-control form-control-modern @error('kode') is-invalid @enderror" required>
                            @error('kode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold">Nama Mata Pelajaran</label>
                            <input type="text" name="nama" value="{{ old('nama', $mataPelajaran->nama) }}" class="form-control form-control-modern @error('nama') is-invalid @enderror" required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold">Kategori</label>
                            <select name="kategori_id" class="form-control form-control-modern @error('kategori_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($kategoriList as $kategori)
                                <option value="{{ $kategori->id }}" {{ old('kategori_id', $mataPelajaran->kategori_id) == $kategori->id ? 'selected' : '' }}>
                                    {{ $kategori->kategori }}
                                </option>
                                @endforeach
                            </select>
                            @error('kategori_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                    
                    <div class="card-footer bg-white text-right">
                        <a href="{{ route('akademik.mata-pelajaran.index') }}" class="btn btn-secondary btn-modern mr-2">
                            <i class="fas fa-times"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-warning btn-modern text-white">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
