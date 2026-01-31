@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <x-card title="Tambah Wali Murid" icon="fas fa-user-plus">
        <form action="{{ route('walimurid.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <x-input name="nama" label="Nama Lengkap" placeholder="Nama wali" :value="old('nama')" required />
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Hubungan <span class="text-danger">*</span></label>
                        <select name="hubungan" class="form-control @error('hubungan') is-invalid @enderror" required>
                            <option value="ayah" {{ old('hubungan') == 'ayah' ? 'selected' : '' }}>Ayah</option>
                            <option value="ibu" {{ old('hubungan') == 'ibu' ? 'selected' : '' }}>Ibu</option>
                            <option value="wali" {{ old('hubungan') == 'wali' ? 'selected' : '' }}>Wali</option>
                        </select>
                        @error('hubungan')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <x-input type="email" name="email" label="Email" placeholder="email@example.com" :value="old('email')" />
                </div>
                <div class="col-md-6">
                    <x-input name="telepon" label="Telepon" placeholder="08xxxxxxxxxx" :value="old('telepon')" />
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <x-input name="pekerjaan" label="Pekerjaan" placeholder="Wiraswasta, PNS, dll" :value="old('pekerjaan')" />
                </div>
            </div>

            <div class="form-group">
                <label>Alamat</label>
                <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat lengkap">{{ old('alamat') }}</textarea>
            </div>

            <hr>
            <div class="d-flex justify-content-between">
                <a href="{{ route('walimurid.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
                <x-btn type="submit" variant="primary" icon="fas fa-save">Simpan</x-btn>
            </div>
        </form>
    </x-card>
</div>
@endsection
