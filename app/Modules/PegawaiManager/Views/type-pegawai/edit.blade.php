@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <x-card title="Edit Tipe Pegawai" icon="fas fa-edit">
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('pegawaimanager.types.update', $type->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Nama Tipe Pegawai</label>
                <input type="text" name="nama_type" class="form-control" value="{{ old('nama_type', $type->nama_type) }}" 
                    placeholder="Contoh: Guru Tetap, Staf TU, Cleaning Service" required>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Perbarui
                </button>
                <a href="{{ route('pegawaimanager.types.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Batal
                </a>
            </div>
        </form>
    </x-card>
</div>
@endsection
