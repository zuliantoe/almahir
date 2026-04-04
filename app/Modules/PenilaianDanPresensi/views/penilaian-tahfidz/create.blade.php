@extends('layouts.app')

@section('title',$title)

@section('content')
<div class="container-fluid">
    <x-card title="Tambah Penilaian Tahfidz" icon="fas fa-plus-circle">
        <form action="{{ route('penilaiandanpresensi.penilaiantahfidz.store') }}" method="POST">
            @csrf

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="id_siswa">Siswa <span class="text-danger">*</span></label>
                    <select name="id_siswa" id="id_siswa" class="form-control" required>
                        <option value="">-- Pilih Siswa --</option>
                        @foreach($siswas as $siswa)
                        <option value="{{ $siswa->id }}" {{ old('id_siswa')==$siswa->id ? 'selected' : '' }}>{{ $siswa->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-4">
                    <label for="id_kelas">Kelas <span class="text-danger">*</span></label>
                    <select name="id_kelas" id="id_kelas" class="form-control" required>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelas as $k)
                        <option value="{{ $k->id }}" {{ old('id_kelas')==$k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-4">
                    <label for="id_guru">Guru <span class="text-danger">*</span></label>
                    <select name="id_guru" id="id_guru" class="form-control" required>
                        <option value="">-- Pilih Guru --</option>
                        @foreach($gurus as $guru)
                        <option value="{{ $guru->id }}" {{ old('id_guru')==$guru->id ? 'selected' : '' }}>{{ $guru->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ old('tanggal') }}" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="surat_awal">Surat Awal</label>
                    <input type="text" name="surat_awal" id="surat_awal" class="form-control" value="{{ old('surat_awal') }}" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="surat_akhir">Surat Akhir</label>
                    <input type="text" name="surat_akhir" id="surat_akhir" class="form-control" value="{{ old('surat_akhir') }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="ayat_awal">Ayat Awal</label>
                    <input type="number" name="ayat_awal" id="ayat_awal" class="form-control" min="1" value="{{ old('ayat_awal') }}" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="ayat_akhir">Ayat Akhir</label>
                    <input type="number" name="ayat_akhir" id="ayat_akhir" class="form-control" min="1" value="{{ old('ayat_akhir') }}" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="nilai">Nilai</label>
                    <input type="number" name="nilai" id="nilai" class="form-control" min="0" max="100" value="{{ old('nilai') }}" required>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Simpan
                </button>
                <a href="{{ route('penilaiandanpresensi.penilaiantahfidz.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </form>
    </x-card>
</div>
@endsection