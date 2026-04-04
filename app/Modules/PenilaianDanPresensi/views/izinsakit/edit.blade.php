@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <x-card title="Edit Izin/Sakit" icon="fas fa-edit">
        <form action="{{ route('penilaiandanpresensi.izinsakit.update', $izinSakit->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="id_siswa">Siswa <span class="text-danger">*</span></label>
                <select name="id_siswa" id="id_siswa" class="form-control" required>
                    <option value="">-- Pilih Siswa --</option>
                    @foreach($siswas as $siswa)
                        <option value="{{ $siswa->id }}" {{ old('id_siswa', $izinSakit->id_siswa) == $siswa->id ? 'selected' : '' }}>{{ $siswa->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="id_kelas">Kelas <span class="text-danger">*</span></label>
                <select name="id_kelas" id="id_kelas" class="form-control" required>
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id }}" {{ old('id_kelas', $izinSakit->id_kelas) == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="jenis">Jenis <span class="text-danger">*</span></label>
                <select name="jenis" id="jenis" class="form-control" required>
                    <option value="">-- Pilih Jenis --</option>
                    <option value="Izin" {{ old('jenis', $izinSakit->jenis)=='Izin' ? 'selected' : '' }}>Izin</option>
                    <option value="Sakit" {{ old('jenis', $izinSakit->jenis)=='Sakit' ? 'selected' : '' }}>Sakit</option>
                </select>
            </div>

            <div class="form-group">
                <label for="tgl_mulai">Tanggal Mulai <span class="text-danger">*</span></label>
                <input type="date" name="tgl_mulai" id="tgl_mulai" class="form-control" value="{{ old('tgl_mulai', $izinSakit->tgl_mulai?->format('Y-m-d')) }}" required>
            </div>

            <div class="form-group">
                <label for="tgl_selesai">Tanggal Selesai <span class="text-danger">*</span></label>
                <input type="date" name="tgl_selesai" id="tgl_selesai" class="form-control" value="{{ old('tgl_selesai', $izinSakit->tgl_selesai?->format('Y-m-d')) }}" required>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Simpan
                </button>
                <a href="{{ route('penilaiandanpresensi.izinsakit.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </form>
    </x-card>
</div>
@endsection
