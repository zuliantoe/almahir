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
                            <x-input label="Nama Kelas" name="namakelas" 
                                     :value="old('namakelas')" 
                                     placeholder="Contoh: X IPA 1" required />
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jenjang <span class="text-danger">*</span></label>
                                <select name="jenjang" class="form-control @error('jenjang') is-invalid @enderror" required>
                                    <option value="">-- Pilih Jenjang --</option>
                                    <option value="X" {{ old('jenjang') == 'X' ? 'selected' : '' }}>Kelas X</option>
                                    <option value="XI" {{ old('jenjang') == 'XI' ? 'selected' : '' }}>Kelas XI</option>
                                    <option value="XII" {{ old('jenjang') == 'XII' ? 'selected' : '' }}>Kelas XII</option>
                                </select>
                                @error('jenjang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Wali Kelas</label>
                                <select name="guru_id" class="form-control @error('guru_id') is-invalid @enderror">
                                    <option value="">-- Pilih Guru Wali Kelas --</option>
                                    @foreach($guru ?? [] as $g)
                                        <option value="{{ $g->id }}" {{ old('guru_id') == $g->id ? 'selected' : '' }}>
                                            {{ $g->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('guru_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
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
