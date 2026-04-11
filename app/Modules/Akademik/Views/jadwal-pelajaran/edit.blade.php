@extends('layouts.app')

@section('title', 'Edit Jadwal Pelajaran')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <x-card title="Edit Jadwal Pelajaran" icon="fas fa-edit" type="warning" outline>
                <form action="{{ route('akademik.jadwal-pelajaran.update', $jadwalPelajaran->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Rombongan Belajar (Rombel)</label>
                            <select name="rombel_id" class="form-control @error('rombel_id') is-invalid @enderror">
                                <option value="">Pilih Rombel</option>
                                @foreach($rombels as $rombel)
                                    <option value="{{ $rombel->id }}" {{ (old('rombel_id', $jadwalPelajaran->rombel_id) == $rombel->id) ? 'selected' : '' }}>
                                        {{ $rombel->nama_rombel }} ({{ $rombel->kelas->nama_kelas }})
                                    </option>
                                @endforeach
                            </select>
                            @error('rombel_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Mata Pelajaran</label>
                            <select name="mapel_id" class="form-control @error('mapel_id') is-invalid @enderror">
                                <option value="">Pilih Mata Pelajaran</option>
                                @foreach($mapels as $mapel)
                                    <option value="{{ $mapel->id }}" {{ (old('mapel_id', $jadwalPelajaran->mapel_id) == $mapel->id) ? 'selected' : '' }}>
                                        [{{ $mapel->kode }}] {{ $mapel->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('mapel_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Guru Pengajar</label>
                            <select name="guru_id" class="form-control @error('guru_id') is-invalid @enderror">
                                <option value="">Pilih Guru</option>
                                @foreach($gurus as $guru)
                                    <option value="{{ $guru->id }}" {{ (old('guru_id', $jadwalPelajaran->guru_id) == $guru->id) ? 'selected' : '' }}>
                                        {{ $guru->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('guru_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Hari</label>
                            <select name="hari" class="form-control @error('hari') is-invalid @enderror">
                                <option value="">Pilih Hari</option>
                                @foreach($hariList as $hari)
                                    <option value="{{ $hari }}" {{ (old('hari', $jadwalPelajaran->hari) == $hari) ? 'selected' : '' }}>{{ $hari }}</option>
                                @endforeach
                            </select>
                            @error('hari') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <x-input label="Jam Ke-" type="number" name="jamke" 
                                     :value="old('jamke', $jadwalPelajaran->jamke)" 
                                     placeholder="Contoh: 1" />
                        </div>

                        <div class="col-md-4 mb-3">
                            <x-input label="Jam Mulai" type="time" name="jamawal" 
                                     :value="old('jamawal', substr($jadwalPelajaran->jamawal, 0, 5))" />
                        </div>

                        <div class="col-md-4 mb-3">
                            <x-input label="Jam Selesai" type="time" name="jamakhir" 
                                     :value="old('jamakhir', substr($jadwalPelajaran->jamakhir, 0, 5))" />
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-end">
                        <x-btn :href="route('akademik.jadwal-pelajaran.index')" class="btn-secondary mr-2" icon="fas fa-times">
                            Batal
                        </x-btn>
                        <x-btn type="submit" class="btn-warning text-white" icon="fas fa-save">
                            Perbarui Data
                        </x-btn>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</div>
@endsection
