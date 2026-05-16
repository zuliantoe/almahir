@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <x-card title="Edit Data Guru" icon="fas fa-user-edit">
        <form action="{{ route('guru.update', $guru->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <x-input name="nip" label="NIP" placeholder="Nomor Induk Pegawai" :value="old('nip', $guru->nip)" />
                </div>
                <div class="col-md-6">
                    <x-input name="nama" label="Nama Lengkap" placeholder="Nama guru" :value="old('nama', $guru->nama)" required />
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <x-input type="email" name="email" label="Email" placeholder="email@sekolah.sch.id" :value="old('email', $guru->email)" />
                </div>
                <div class="col-md-6">
                    <x-input name="telepon" label="Telepon" placeholder="08xxxxxxxxxx" :value="old('telepon', $guru->telepon)" />
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <x-input name="tempat_lahir" label="Tempat Lahir" placeholder="Kota" :value="old('tempat_lahir', $guru->tempat_lahir)" />
                </div>
                <div class="col-md-4">
                    <x-input type="date" name="tanggal_lahir" label="Tanggal Lahir" :value="old('tanggal_lahir', $guru->tanggal_lahir?->format('Y-m-d'))" />
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-control @error('jenis_kelamin') is-invalid @enderror">
                            <option value="">-- Pilih --</option>
                            <option value="L" {{ old('jenis_kelamin', $guru->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin', $guru->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <x-input name="jabatan" label="Jabatan" placeholder="Guru Tetap, Kepala Sekolah, dll" :value="old('jabatan', $guru->jabatan)" />
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="2" placeholder="Alamat lengkap">{{ old('alamat', $guru->alamat) }}</textarea>
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
                            <option value="aktif" {{ old('status', $guru->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ old('status', $guru->status) == 'nonaktif' ? 'selected' : '' }}>Non-Aktif</option>
                            <option value="pensiun" {{ old('status', $guru->status) == 'pensiun' ? 'selected' : '' }}>Pensiun</option>
                        </select>
                        @error('status')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Linked User Info --}}
            @if($guru->user)
            <div class="alert alert-info">
                <i class="fas fa-info-circle mr-1"></i>
                Guru ini memiliki akun login: <strong>{{ $guru->user->email }}</strong>
            </div>
            @endif

            <hr>
            <div class="d-flex justify-content-between">
                <a href="{{ route('guru.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
                <x-btn type="submit" variant="primary" icon="fas fa-save">Update</x-btn>
            </div>
        </form>
    </x-card>
</div>
@endsection
