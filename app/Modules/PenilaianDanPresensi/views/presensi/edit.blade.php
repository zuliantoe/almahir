@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <x-card title="Edit Presensi" icon="fas fa-edit">
        <form action="{{ route('penilaiandanpresensi.presensi.update', $presensi->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="id_siswa">Siswa</label>
                <select name="id_siswa" id="id_siswa" class="form-control" required>
                    <option value="">Pilih Siswa</option>
                    @foreach($siswas as $siswa)
                        <option value="{{ $siswa->id }}" {{ old('id_siswa', $presensi->id_siswa) == $siswa->id ? 'selected' : '' }}>{{ $siswa->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="id_guru">Guru</label>
                <select name="id_guru" id="id_guru" class="form-control" required>
                    <option value="">Pilih Guru</option>
                    @foreach($gurus as $guru)
                        <option value="{{ $guru->id }}" {{ old('id_guru', $presensi->id_guru) == $guru->id ? 'selected' : '' }}>{{ $guru->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="id_mapel">Mata Pelajaran</label>
                <select name="id_mapel" id="id_mapel" class="form-control" required>
                    <option value="">Pilih Mata Pelajaran</option>
                    @foreach($mapels as $mapel)
                        <option value="{{ $mapel->id }}" {{ old('id_mapel', $presensi->id_mapel) == $mapel->id ? 'selected' : '' }}>{{ $mapel->nama ?? $mapel->name ?? 'Mapel '.$mapel->id }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="id_jadwal_pelajaran">Jadwal Pelajaran</label>
                <select name="id_jadwal_pelajaran" id="id_jadwal_pelajaran" class="form-control" required>
                    <option value="">Pilih Jadwal Pelajaran</option>
                    @foreach($jadwals as $jadwal)
                        <option value="{{ $jadwal->id }}" {{ old('id_jadwal_pelajaran', $presensi->id_jadwal_pelajaran) == $jadwal->id ? 'selected' : '' }}>{{ $jadwal->hari }} - {{ \Carbon\Carbon::parse($jadwal->jamawal)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jamakhir)->format('H:i') }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="jam">Jam</label>
                <input type="time" name="jam" id="jam" class="form-control" value="{{ old('jam', \Carbon\Carbon::parse($presensi->jam)->format('H:i')) }}" required>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="">Pilih Status</option>
                    <option value="Hadir" {{ old('status', $presensi->status) == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="Izin" {{ old('status', $presensi->status) == 'Izin' ? 'selected' : '' }}>Izin</option>
                    <option value="Sakit" {{ old('status', $presensi->status) == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="Alpha" {{ old('status', $presensi->status) == 'Alpha' ? 'selected' : '' }}>Alpha</option>
                </select>
            </div>

            <div class="form-group">
                <label for="kategori">Kategori</label>
                <select name="kategori" id="kategori" class="form-control" required>
                    <option value="">Pilih Kategori</option>
                    <option value="Sekolah" {{ old('kategori', $presensi->kategori) == 'Sekolah' ? 'selected' : '' }}>Sekolah</option>
                    <option value="Pengajian" {{ old('kategori', $presensi->kategori) == 'Pengajian' ? 'selected' : '' }}>Pengajian</option>
                    <option value="Ekstrakurikuler" {{ old('kategori', $presensi->kategori) == 'Ekstrakurikuler' ? 'selected' : '' }}>Ekstrakurikuler</option>
                </select>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Simpan
                </button>
                <a href="{{ route('penilaiandanpresensi.presensi.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </form>
    </x-card>
</div>
@endsection
