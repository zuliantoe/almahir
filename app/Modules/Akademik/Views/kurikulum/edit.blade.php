@extends('layouts.app')

@section('title', 'Edit Kurikulum')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <x-card title="Edit Data Kurikulum" icon="fas fa-edit" type="warning" outline>
                <form action="{{ route('akademik.kurikulum.update', $kurikulum->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Master Kurikulum</label>
                            <select name="master_kurikulum_id" class="form-control @error('master_kurikulum_id') is-invalid @enderror">
                                <option value="">Pilih Master Kurikulum</option>
                                @foreach($masterKurikulums as $mk)
                                    <option value="{{ $mk->id }}" {{ (old('master_kurikulum_id', $kurikulum->master_kurikulum_id) == $mk->id) ? 'selected' : '' }}>
                                        {{ $mk->nama_kurikulum }}
                                    </option>
                                @endforeach
                            </select>
                            @error('master_kurikulum_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Tingkat</label>
                            <select name="tingkat_id" class="form-control @error('tingkat_id') is-invalid @enderror">
                                <option value="">Pilih Tingkat</option>
                                @foreach($tingkats as $tingkat)
                                    <option value="{{ $tingkat->id }}" {{ (old('tingkat_id', $kurikulum->tingkat_id) == $tingkat->id) ? 'selected' : '' }}>
                                        {{ $tingkat->nama_tingkat }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tingkat_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Tahun Ajaran</label>
                            <select name="tahunajaran_id" class="form-control @error('tahunajaran_id') is-invalid @enderror">
                                <option value="">Pilih Tahun Ajaran</option>
                                @foreach($tahunAjarans as $ta)
                                    <option value="{{ $ta->id }}" {{ (old('tahunajaran_id', $kurikulum->tahunajaran_id) == $ta->id) ? 'selected' : '' }}>
                                        {{ $ta->tahunajaran }} ({{ $ta->semester }})
                                    </option>
                                @endforeach
                            </select>
                            @error('tahunajaran_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Kelas</label>
                            <select name="kelas_id" class="form-control @error('kelas_id') is-invalid @enderror">
                                <option value="">Pilih Kelas</option>
                                @foreach($kelases as $kelas)
                                    <option value="{{ $kelas->id }}" {{ (old('kelas_id', $kurikulum->kelas_id) == $kelas->id) ? 'selected' : '' }}>
                                        {{ $kelas->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kelas_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Mata Pelajaran</label>
                            <select name="mapel_id" class="form-control @error('mapel_id') is-invalid @enderror">
                                <option value="">Pilih Mata Pelajaran</option>
                                @foreach($mapels as $mapel)
                                    <option value="{{ $mapel->id }}" {{ (old('mapel_id', $kurikulum->mapel_id) == $mapel->id) ? 'selected' : '' }}>
                                        [{{ $mapel->kode }}] {{ $mapel->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('mapel_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <x-input label="Total Jam / Minggu" name="totaljam" type="number" 
                                     :value="old('totaljam', $kurikulum->totaljam)" 
                                     placeholder="Contoh: 4" />
                        </div>

                        <div class="col-md-3 mb-3">
                            <x-input label="KKM" name="kkm" type="number" 
                                     :value="old('kkm', $kurikulum->kkm)" 
                                     placeholder="Contoh: 75" />
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-end">
                        <x-btn :href="route('akademik.kurikulum.index')" class="btn-secondary mr-2" icon="fas fa-arrow-left">
                            Batal
                        </x-btn>
                        <x-btn type="submit" class="btn-warning text-white" icon="fas fa-save">
                            Perbarui Kurikulum
                        </x-btn>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</div>
@endsection
