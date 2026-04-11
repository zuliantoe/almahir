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

                    <x-input label="Nama Kelas" name="nama" :value="old('nama', $kelas->nama)" required />
                    
                    <x-alert type="info">
                        <i class="fas fa-info-circle mr-1"></i> Untuk mengedit Jenjang atau Wali Kelas, masuk ke pengaturan tingkat lanjut kelas.
                    </x-alert>

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
