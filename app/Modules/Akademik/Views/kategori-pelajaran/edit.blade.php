@extends('layouts.app')

@section('title', 'Edit Kategori Pelajaran')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <x-card title="Edit Kategori Pelajaran" icon="fas fa-edit" type="warning" outline>
                <form action="{{ route('akademik.kategori-pelajaran.update', $kategoriPelajaran->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <x-input label="Nama Kategori" name="kategori" 
                             :value="old('kategori', $kategoriPelajaran->kategori)" 
                             placeholder="Contoh: Muatan Lokal" required maxlength="100" />

                    <hr>

                    <div class="d-flex justify-content-end">
                        <x-btn :href="route('akademik.kategori-pelajaran.index')" class="btn-secondary mr-2" icon="fas fa-times">
                            Batal
                        </x-btn>
                        <x-btn type="submit" class="btn-warning text-white" icon="fas fa-save">
                            Perbarui Kategori
                        </x-btn>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</div>
@endsection
