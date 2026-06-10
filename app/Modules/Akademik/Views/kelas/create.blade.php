@extends('layouts.app')

@section('title', 'Tambah Kelas')

@section('content')
<div class="container-fluid">
    {{-- Error Messages --}}
    @if ($errors->any())
        <x-alert type="danger" dismissible>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-8">
            <x-card title="Tambah Kelas" icon="fas fa-plus-circle" type="primary" outline>
                <form action="{{ route('akademik.kelas.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <x-input label="Kode Kelas" name="kode_kelas" 
                                     :value="old('kode_kelas')" 
                                     placeholder="Contoh: KLS-X-IPA1" />
                        </div>
                        <div class="col-md-6">
                            <x-input label="Nama Kelas" name="nama_kelas" 
                                     :value="old('nama_kelas')" 
                                     placeholder="Contoh: X IPA 1" required />
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tingkat <span class="text-danger">*</span></label>
                                <select name="tingkat_id" class="form-control @error('tingkat_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Tingkat --</option>
                                    @foreach($tingkat ?? [] as $t)
                                        <option value="{{ $t->id }}" {{ old('tingkat_id') == $t->id ? 'selected' : '' }}>
                                            {{ $t->nama_tingkat }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tingkat_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <x-input label="Kapasitas Kelas" name="kapasitas" type="number" 
                                     :value="old('kapasitas', 30)" 
                                     placeholder="Contoh: 30" required min="1" max="100" />
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-end">
                        <x-btn :href="route('akademik.kelas.index')" class="btn-secondary mr-2" icon="fas fa-times">
                            Batal
                        </x-btn>
                        <x-btn type="submit" icon="fas fa-save">
                            Simpan Data
                        </x-btn>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</div>
@endsection
