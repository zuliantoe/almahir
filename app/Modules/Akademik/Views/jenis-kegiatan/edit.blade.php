@extends('layouts.app')

@section('title', 'Edit Jenis Kegiatan')

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
            <x-card title="Edit Jenis Kegiatan" icon="fas fa-edit" type="warning" outline>
                <form action="{{ route('akademik.jenis-kegiatan.update', $jenisKegiatan->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <x-input label="Nama Jenis Kegiatan" name="jeniskegiatan" 
                             :value="old('jeniskegiatan', $jenisKegiatan->jeniskegiatan)" 
                             placeholder="Contoh: Ujian Tengah Semester" 
                             required maxlength="100" 
                             hint="Maksimal 100 karakter dan harus unik." />

                    <div class="form-group text-dark">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" rows="3" class="form-control @error('deskripsi') is-invalid @enderror" placeholder="Tambahkan deskripsi kegiatan (opsional)">{{ old('deskripsi', $jenisKegiatan->deskripsi) }}</textarea>
                        @error('deskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label class="d-block">Status Kegiatan Belajar Mengajar (KBM)</label>
                        <div class="custom-control custom-switch">
                            <input type="hidden" name="is_kbm" value="0">
                            <input type="checkbox" class="custom-control-input" id="is_kbm" name="is_kbm" value="1" {{ old('is_kbm', $jenisKegiatan->is_kbm) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_kbm">Aktifkan KBM (Jika aktif, jadwal pelajaran tetap berjalan)</label>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-end">
                        <x-btn :href="route('akademik.jenis-kegiatan.index')" class="btn-secondary mr-2" icon="fas fa-times">
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
