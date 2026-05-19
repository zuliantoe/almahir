@extends('layouts.app')

@section('title', (isset($kalenderAkademik) ? 'Edit' : 'Tambah') . ' Kegiatan Akademik')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-gray-800">{{ isset($kalenderAkademik) ? 'Edit' : 'Tambah' }} Kegiatan Akademik</h1>
            <x-btn :href="route('akademik.kalender-akademik.index')" class="btn-secondary" icon="fas fa-arrow-left">
                Kembali
            </x-btn>
        </div>
    </div>

    <x-card :title="(isset($kalenderAkademik) ? 'Form Edit Kegiatan' : 'Form Tambah Kegiatan')" type="primary" outline>
        <form action="{{ isset($kalenderAkademik) ? route('akademik.kalender-akademik.update', $kalenderAkademik->id) : route('akademik.kalender-akademik.store') }}" 
              method="POST">
            @csrf
            @if(isset($kalenderAkademik))
                @method('PUT')
            @endif

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tahun Ajaran <span class="text-danger">*</span></label>
                        <select name="tahunajaran_id" class="form-control @error('tahunajaran_id') is-invalid @enderror" required>
                            <option value="">Pilih Tahun Ajaran</option>
                            @foreach($tahunAjarans as $ta)
                                <option value="{{ $ta->id }}" {{ (old('tahunajaran_id', $kalenderAkademik->tahunajaran_id ?? '') == $ta->id) ? 'selected' : '' }}>
                                    {{ $ta->tahunajaran }}
                                </option>
                            @endforeach
                        </select>
                        @error('tahunajaran_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Semester <span class="text-danger">*</span></label>
                        <select name="semester" class="form-control @error('semester') is-invalid @enderror" required>
                            <option value="">Pilih Semester</option>
                            <option value="Ganjil" {{ old('semester', $kalenderAkademik->semester ?? '') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="Genap" {{ old('semester', $kalenderAkademik->semester ?? '') == 'Genap' ? 'selected' : '' }}>Genap</option>
                        </select>
                        @error('semester') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Jenis Kegiatan <span class="text-danger">*</span></label>
                        <select name="kegiatan_id" class="form-control @error('kegiatan_id') is-invalid @enderror" required>
                            <option value="">Pilih Jenis Kegiatan</option>
                            @foreach($jenisKegiatans as $jk)
                                <option value="{{ $jk->id }}" {{ (old('kegiatan_id', $kalenderAkademik->kegiatan_id ?? '') == $jk->id) ? 'selected' : '' }}>
                                    {{ $jk->jeniskegiatan }}
                                </option>
                            @endforeach
                        </select>
                        @error('kegiatan_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="col-12">
                    <x-input label="Nama Kegiatan" name="nama_kegiatan" 
                             :value="$kalenderAkademik->nama_kegiatan ?? ''" 
                             placeholder="Contoh: UTS Semester Ganjil" required />
                </div>

                <div class="col-md-6">
                    <x-input label="Tanggal Mulai" name="tanggal_awal" type="date" 
                             :value="$kalenderAkademik->tanggal_awal ?? ''" required />
                </div>

                <div class="col-md-6">
                    <x-input label="Tanggal Selesai" name="tanggal_akhir" type="date" 
                             :value="$kalenderAkademik->tanggal_akhir ?? ''" required />
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" 
                                  rows="3" placeholder="Keterangan tambahan kegiatan...">{{ old('deskripsi', $kalenderAkademik->deskripsi ?? '') }}</textarea>
                        @error('deskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <hr>

            <div class="d-flex justify-content-end">
                <x-btn type="reset" class="btn-light mr-2">Reset</x-btn>
                <x-btn type="submit" icon="fas fa-save">
                    {{ isset($kalenderAkademik) ? 'Perbarui' : 'Simpan' }} Kegiatan
                </x-btn>
            </div>
        </form>
    </x-card>
</div>
@endsection
