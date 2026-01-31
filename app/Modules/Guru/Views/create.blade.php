@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <x-card title="Tambah Guru Baru" icon="fas fa-user-plus">
        <form action="{{ route('guru.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <x-input name="nip" label="NIP" placeholder="Nomor Induk Pegawai" :value="old('nip')" />
                </div>
                <div class="col-md-6">
                    <x-input name="nama" label="Nama Lengkap" placeholder="Nama guru" :value="old('nama')" required />
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <x-input type="email" name="email" label="Email" placeholder="email@sekolah.sch.id" :value="old('email')" />
                </div>
                <div class="col-md-6">
                    <x-input name="telepon" label="Telepon" placeholder="08xxxxxxxxxx" :value="old('telepon')" />
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <x-input name="tempat_lahir" label="Tempat Lahir" placeholder="Kota" :value="old('tempat_lahir')" />
                </div>
                <div class="col-md-4">
                    <x-input type="date" name="tanggal_lahir" label="Tanggal Lahir" :value="old('tanggal_lahir')" />
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-control @error('jenis_kelamin') is-invalid @enderror">
                            <option value="">-- Pilih --</option>
                            <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <x-input name="jabatan" label="Jabatan" placeholder="Guru Tetap, Kepala Sekolah, dll" :value="old('jabatan')" />
                </div>
                <div class="col-md-6">
                    <x-input name="mata_pelajaran" label="Mata Pelajaran" placeholder="Matematika, IPA, dll" :value="old('mata_pelajaran')" />
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="2" placeholder="Alamat lengkap">{{ old('alamat') }}</textarea>
                        @error('alamat')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                            <option value="aktif" {{ old('status', 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Non-Aktif</option>
                            <option value="pensiun" {{ old('status') == 'pensiun' ? 'selected' : '' }}>Pensiun</option>
                        </select>
                        @error('status')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <hr>
            <div class="d-flex justify-content-between">
                <a href="{{ route('guru.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
                <x-btn type="submit" variant="primary" icon="fas fa-save">Simpan</x-btn>
            </div>
        </form>
    </x-card>
</div>
@endsection
