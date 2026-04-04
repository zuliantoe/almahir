@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <x-card title="Edit Penilaian Akademik" icon="fas fa-edit">
        <form action="{{ route('penilaiandanpresensi.penilaianakademik.update', $penilaianAkademik->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="id_siswa">Siswa</label>
                <select name="id_siswa" id="id_siswa" class="form-control" required>
                    <option value="">Pilih Siswa</option>
                    @foreach($siswas as $siswa)
                        <option value="{{ $siswa->id }}" {{ old('id_siswa', $penilaianAkademik->id_siswa) == $siswa->id ? 'selected' : '' }}>{{ $siswa->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="id_guru">Guru</label>
                <select name="id_guru" id="id_guru" class="form-control" required>
                    <option value="">Pilih Guru</option>
                    @foreach($gurus as $guru)
                        <option value="{{ $guru->id }}" {{ old('id_guru', $penilaianAkademik->id_guru) == $guru->id ? 'selected' : '' }}>{{ $guru->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="id_mapel">Mata Pelajaran</label>
                <select name="id_mapel" id="id_mapel" class="form-control" required>
                    <option value="">Pilih Mata Pelajaran</option>
                    @foreach($mapels as $mapel)
                        <option value="{{ $mapel->id }}" {{ old('id_mapel', $penilaianAkademik->id_mapel) == $mapel->id ? 'selected' : '' }}>{{ $mapel->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="id_tahun_ajaran">Tahun Ajaran</label>
                <select name="id_tahun_ajaran" id="id_tahun_ajaran" class="form-control" required>
                    <option value="">Pilih Tahun Ajaran</option>
                    @foreach($tahunAjarans as $tahunAjaran)
                        <option value="{{ $tahunAjaran->id }}" {{ old('id_tahun_ajaran', $penilaianAkademik->id_tahun_ajaran) == $tahunAjaran->id ? 'selected' : '' }}>{{ $tahunAjaran->tahunajaran }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="nilai">Nilai</label>
                <input type="number" name="nilai" id="nilai" class="form-control" min="0" max="100" value="{{ old('nilai', $penilaianAkademik->nilai) }}" required>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Simpan
                </button>
                <a href="{{ route('penilaiandanpresensi.penilaianakademik.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </form>
    </x-card>
</div>
@endsection
