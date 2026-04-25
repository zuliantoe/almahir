@extends('layouts.app')

@section('title', (isset($jadwalPelajaran) ? 'Edit' : 'Tambah') . ' Jadwal Pelajaran')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-gray-800">{{ isset($jadwalPelajaran) ? 'Edit' : 'Tambah' }} Jadwal Pelajaran</h1>
            <x-btn :href="route('akademik.jadwal-pelajaran.index')" class="btn-secondary" icon="fas fa-arrow-left">
                Kembali
            </x-btn>
        </div>
    </div>

    <x-card :title="(isset($jadwalPelajaran) ? 'Form Edit Data' : 'Form Tambah Data')" type="primary" outline>
        <form action="{{ isset($jadwalPelajaran) ? route('akademik.jadwal-pelajaran.update', $jadwalPelajaran->id) : route('akademik.jadwal-pelajaran.store') }}" 
              method="POST">
            @csrf
            @if(isset($jadwalPelajaran))
                @method('PUT')
            @endif

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Rombongan Belajar (Rombel) <span class="text-danger">*</span></label>
                        <select name="rombel_id" class="form-control @error('rombel_id') is-invalid @enderror" required>
                            <option value="">Pilih Rombel</option>
                            @foreach($rombels as $rombel)
                                <option value="{{ $rombel->id }}" {{ (old('rombel_id', $jadwalPelajaran->rombel_id ?? '') == $rombel->id) ? 'selected' : '' }}>
                                    {{ $rombel->nama_rombel }} ({{ $rombel->kelas->nama_kelas }})
                                </option>
                            @endforeach
                        </select>
                        @error('rombel_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Mata Pelajaran <span class="text-danger">*</span></label>
                        <select name="mapel_id" class="form-control @error('mapel_id') is-invalid @enderror" required>
                            <option value="">Pilih Mata Pelajaran</option>
                            @foreach($mapels as $mapel)
                                <option value="{{ $mapel->id }}" {{ (old('mapel_id', $jadwalPelajaran->mapel_id ?? '') == $mapel->id) ? 'selected' : '' }}>
                                    [{{ $mapel->kode }}] {{ $mapel->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('mapel_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Guru Pengajar <span class="text-danger">*</span></label>
                        <select name="guru_id" class="form-control @error('guru_id') is-invalid @enderror" required>
                            <option value="">Pilih Guru</option>
                            @foreach($gurus as $guru)
                                <option value="{{ $guru->id }}" {{ (old('guru_id', $jadwalPelajaran->guru_id ?? '') == $guru->id) ? 'selected' : '' }}>
                                    {{ $guru->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('guru_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Hari <span class="text-danger">*</span></label>
                        <select name="hari" class="form-control @error('hari') is-invalid @enderror" required>
                            <option value="">Pilih Hari</option>
                            @foreach($hariList as $hari)
                                <option value="{{ $hari }}" {{ (old('hari', $jadwalPelajaran->hari ?? '') == $hari) ? 'selected' : '' }}>{{ $hari }}</option>
                            @endforeach
                        </select>
                        @error('hari') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <x-input label="Jam Ke-" name="jamke" type="number" 
                             :value="$jadwalPelajaran->jamke ?? ''" 
                             placeholder="Contoh: 1" required />
                </div>

                <div class="col-md-4">
                    <x-input label="Jam Mulai" name="jamawal" type="time" 
                             :value="isset($jadwalPelajaran) ? substr($jadwalPelajaran->jamawal, 0, 5) : ''" 
                             required />
                </div>

                <div class="col-md-4">
                    <x-input label="Jam Selesai" name="jamakhir" type="time" 
                             :value="isset($jadwalPelajaran) ? substr($jadwalPelajaran->jamakhir, 0, 5) : ''" 
                             required />
                </div>
            </div>

            <hr>

            <div class="d-flex justify-content-end">
                <x-btn type="reset" class="btn-light mr-2">Reset</x-btn>
                <x-btn type="submit" icon="fas fa-save">
                    {{ isset($jadwalPelajaran) ? 'Perbarui' : 'Simpan' }} Data
                </x-btn>
            </div>
        </form>
    </x-card>
</div>
@endsection
