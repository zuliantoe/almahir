@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <x-card title="Edit Wali Murid" icon="fas fa-user-edit">
        <form action="{{ route('walimurid.update', $waliMurid->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <x-input name="nama" label="Nama Lengkap" :value="old('nama', $waliMurid->nama)" required />
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Hubungan <span class="text-danger">*</span></label>
                        <select name="hubungan" class="form-control" required>
                            <option value="ayah" {{ old('hubungan', $waliMurid->hubungan) == 'ayah' ? 'selected' : '' }}>Ayah</option>
                            <option value="ibu" {{ old('hubungan', $waliMurid->hubungan) == 'ibu' ? 'selected' : '' }}>Ibu</option>
                            <option value="wali" {{ old('hubungan', $waliMurid->hubungan) == 'wali' ? 'selected' : '' }}>Wali</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <x-input type="email" name="email" label="Email" :value="old('email', $waliMurid->email)" />
                </div>
                <div class="col-md-6">
                    <x-input name="telepon" label="Telepon" :value="old('telepon', $waliMurid->telepon)" />
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <x-input name="pekerjaan" label="Pekerjaan" :value="old('pekerjaan', $waliMurid->pekerjaan)" />
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Pilih Siswa (Anak) <span class="text-danger">*</span></label>
                        <select name="siswa_ids[]" class="form-control select2 @error('siswa_ids') is-invalid @enderror" multiple="multiple" data-placeholder="Pilih satu atau lebih siswa" required>
                            @php
                                $selectedIds = old('siswa_ids', $waliMurid->siswa->pluck('id')->toArray());
                            @endphp
                            @foreach($siswas as $siswa)
                                <option value="{{ $siswa->id }}" {{ in_array($siswa->id, $selectedIds) ? 'selected' : '' }}>
                                    {{ $siswa->nama }} ({{ $siswa->nis }})
                                </option>
                            @endforeach
                        </select>
                        @error('siswa_ids')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Alamat</label>
                <textarea name="alamat" class="form-control" rows="2">{{ old('alamat', $waliMurid->alamat) }}</textarea>
            </div>

            @if($waliMurid->user)
            <div class="alert alert-info">
                <i class="fas fa-info-circle mr-1"></i>
                Wali ini memiliki akun login: <strong>{{ $waliMurid->user->email }}</strong>
            </div>
            @endif

            <hr>
            <div class="d-flex justify-content-between">
                <a href="{{ route('walimurid.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
                <x-btn type="submit" variant="primary" icon="fas fa-save">Update</x-btn>
            </div>
        </form>
    </x-card>
</div>
@endsection
