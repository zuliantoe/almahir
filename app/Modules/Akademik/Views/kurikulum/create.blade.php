@extends('layouts.app')

@section('title', (isset($kurikulum) ? 'Edit' : 'Tambah') . ' Kurikulum')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-gray-800">{{ isset($kurikulum) ? 'Edit' : 'Tambah' }} Kurikulum</h1>
            <x-btn :href="route('akademik.kurikulum.index')" class="btn-secondary" icon="fas fa-arrow-left">
                Kembali
            </x-btn>
        </div>
    </div>

    <x-card :title="(isset($kurikulum) ? 'Form Edit Kurikulum' : 'Form Tambah Kurikulum')" type="primary" outline>
        <form action="{{ isset($kurikulum) ? route('akademik.kurikulum.update', $kurikulum->id) : route('akademik.kurikulum.store') }}" 
              method="POST">
            @csrf
            @if(isset($kurikulum))
                @method('PUT')
            @endif

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="font-weight-bold">Master Kurikulum <span class="text-danger">*</span></label>
                    <select name="master_kurikulum_id" class="form-control @error('master_kurikulum_id') is-invalid @enderror" required>
                        <option value="">Pilih Master Kurikulum</option>
                        @foreach($masterKurikulums as $mk)
                            <option value="{{ $mk->id }}" {{ (old('master_kurikulum_id', $kurikulum->master_kurikulum_id ?? '') == $mk->id) ? 'selected' : '' }}>
                                {{ $mk->nama_kurikulum }}
                            </option>
                        @endforeach
                    </select>
                    @error('master_kurikulum_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="font-weight-bold">Tingkat <span class="text-danger">*</span></label>
                    <select name="tingkat_id" class="form-control @error('tingkat_id') is-invalid @enderror" required>
                        <option value="">Pilih Tingkat</option>
                        @foreach($tingkats as $tingkat)
                            <option value="{{ $tingkat->id }}" {{ (old('tingkat_id', $kurikulum->tingkat_id ?? '') == $tingkat->id) ? 'selected' : '' }}>
                                {{ $tingkat->nama_tingkat }}
                            </option>
                        @endforeach
                    </select>
                    @error('tingkat_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="font-weight-bold">Tahun Ajaran <span class="text-danger">*</span></label>
                    <select name="tahunajaran_id" class="form-control @error('tahunajaran_id') is-invalid @enderror" required>
                        <option value="">Pilih Tahun Ajaran</option>
                        @foreach($tahunAjarans as $ta)
                            <option value="{{ $ta->id }}" {{ (old('tahunajaran_id', $kurikulum->tahunajaran_id ?? '') == $ta->id) ? 'selected' : '' }}>
                                {{ $ta->tahunajaran }} ({{ $ta->semester }})
                            </option>
                        @endforeach
                    </select>
                    @error('tahunajaran_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="font-weight-bold">Kelas <span class="text-danger">*</span></label>
                    <select name="kelas_id" class="form-control @error('kelas_id') is-invalid @enderror" required>
                        <option value="">Pilih Kelas</option>
                        @foreach($kelases as $kelas)
                            <option value="{{ $kelas->id }}" {{ (old('kelas_id', $kurikulum->kelas_id ?? '') == $kelas->id) ? 'selected' : '' }}>
                                {{ $kelas->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                    @error('kelas_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="font-weight-bold">Mata Pelajaran <span class="text-danger">*</span></label>
                    <select name="mapel_id" class="form-control @error('mapel_id') is-invalid @enderror" required>
                        <option value="">Pilih Mata Pelajaran</option>
                        @foreach($mapels as $mapel)
                            <option value="{{ $mapel->id }}" {{ (old('mapel_id', $kurikulum->mapel_id ?? '') == $mapel->id) ? 'selected' : '' }}>
                                [{{ $mapel->kode }}] {{ $mapel->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('mapel_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3 mb-3">
                    <x-input label="Total Jam / Minggu" name="totaljam" type="number" 
                             :value="old('totaljam', $kurikulum->totaljam ?? '')" 
                             placeholder="Contoh: 4" required />
                </div>

                <div class="col-md-3 mb-3">
                    <x-input label="KKM" name="kkm" type="number" 
                             :value="old('kkm', $kurikulum->kkm ?? '')" 
                             placeholder="Contoh: 75" required />
                </div>
            </div>

            <hr>

            <div class="d-flex justify-content-end">
                <x-btn type="reset" class="btn-light mr-2">Reset</x-btn>
                <x-btn type="submit" icon="fas fa-save">
                    {{ isset($kurikulum) ? 'Perbarui' : 'Simpan' }} Kurikulum
                </x-btn>
            </div>
        </form>
    </x-card>
</div>
@endsection
