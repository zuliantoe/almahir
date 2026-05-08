@extends('layouts.app')

@section('title', 'Tambah Kategori Pelajaran')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <x-card title="Tambah Kategori Pelajaran" icon="fas fa-plus-circle" type="primary" outline>
                <form action="{{ route('akademik.kategori-pelajaran.store') }}" method="POST">
                    @csrf

                    <x-input label="Nama Kategori" name="kategori" :value="old('kategori')" 
                             placeholder="Contoh: Muatan Lokal" required maxlength="100" />

                    <hr>

                    <div class="d-flex justify-content-end">
                        <x-btn :href="route('akademik.kategori-pelajaran.index')" class="btn-secondary mr-2" icon="fas fa-times">
                            Batal
                        </x-btn>
                        <x-btn type="submit" icon="fas fa-save">
                            Simpan Kategori
                        </x-btn>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
