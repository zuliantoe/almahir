@extends('layouts.app')

@section('title', 'Edit Kelas')

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
        <div class="col-md-6">
            <x-card title="Edit Data Kelas" icon="fas fa-edit" type="warning" outline>
                <form action="{{ route('akademik.kelas.update', $kelas->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <x-input label="Kode Kelas" name="kode_kelas" :value="old('kode_kelas', $kelas->kode_kelas)" />
                        </div>
                        <div class="col-md-6">
                            <x-input label="Nama Kelas" name="nama_kelas" :value="old('nama_kelas', $kelas->nama_kelas)" required />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tingkat <span class="text-danger">*</span></label>
                                <select name="tingkat_id" class="form-control @error('tingkat_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Tingkat --</option>
                                    @foreach($tingkat ?? [] as $t)
                                        <option value="{{ $t->id }}" {{ old('tingkat_id', $kelas->tingkat_id) == $t->id ? 'selected' : '' }}>
                                            {{ $t->nama_tingkat }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tingkat_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>


                    <hr>

                    <div class="d-flex justify-content-end">
                        <x-btn :href="route('akademik.kelas.index')" class="btn-secondary mr-2" icon="fas fa-times">
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
