@extends('layouts.app')

@section('title', 'Edit Mata Pelajaran')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <x-card title="Edit Mata Pelajaran" icon="fas fa-edit" type="warning" outline>
                <form method="POST" action="{{ route('akademik.mata-pelajaran.update', $mataPelajaran) }}">
                    @csrf
                    @method('PUT')
                    
                    <x-input label="Kode Mata Pelajaran" name="kode" 
                             :value="old('kode', $mataPelajaran->kode)" required />

                    <x-input label="Nama Mata Pelajaran" name="nama" 
                             :value="old('nama', $mataPelajaran->nama)" required />

                    <div class="form-group mb-4">
                        <label>Kategori <span class="text-danger">*</span></label>
                        <select name="kategori_id" class="form-control @error('kategori_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategoriList as $kategori)
                            <option value="{{ $kategori->id }}" {{ old('kategori_id', $mataPelajaran->kategori_id) == $kategori->id ? 'selected' : '' }}>
                                {{ $kategori->kategori }}
                            </option>
                            @endforeach
                        </select>
                        @error('kategori_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="bisa_double" value="1" class="custom-control-input" id="bisa_double" {{ old('bisa_double', $mataPelajaran->bisa_double) ? 'checked' : '' }}>
                            <label class="custom-control-label font-weight-bold text-dark" for="bisa_double">Bisa Double / Kelas Gabungan</label>
                        </div>
                        <small class="form-text text-muted">
                            Aktifkan jika mata pelajaran ini dapat diajarkan oleh guru yang sama di kelas/rombel yang berbeda pada hari dan jam pelajaran yang sama secara bersamaan (misal: Tahfidz/Halaqah/Upacara).
                        </small>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-end">
                        <x-btn :href="route('akademik.mata-pelajaran.index')" class="btn-secondary mr-2" icon="fas fa-times">
                            Batal
                        </x-btn>
                        <x-btn type="submit" class="btn-warning text-white" icon="fas fa-save">
                            Simpan Perubahan
                        </x-btn>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</div>
@endsection
