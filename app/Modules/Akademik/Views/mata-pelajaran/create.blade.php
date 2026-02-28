@extends('layouts.app')

@section('title', 'Tambah Mata Pelajaran')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6 max-w-2xl mx-auto">
    <h3 class="text-xl font-semibold mb-6">Tambah Mata Pelajaran</h3>

    <form method="POST" action="{{ route('akademik.mata-pelajaran.store') }}">
        @csrf

        <div class="mb-4">
            <label class="form-label">Kode</label>
            <input type="text" name="kode"
                value="{{ old('kode') }}"
                class="form-input w-full">
        </div>

        <div class="mb-4">
            <label class="form-label">Nama</label>
            <input type="text" name="nama"
                value="{{ old('nama') }}"
                class="form-input w-full">
        </div>

        <div class="mb-4">
            <label class="form-label">Kategori</label>
            <select name="kategori_id" class="form-input w-full">
                <option value="">Pilih</option>
                @foreach($kategoriList as $kategori)
                <option value="{{ $kategori->id }}"
                    {{ old('kategori_id') == $kategori->id ? 'selected' : '' }}>
                    {{ $kategori->kategori }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="flex gap-2">
            <button class="btn-primary">Simpan</button>
            <a href="{{ route('akademik.mata-pelajaran.index') }}" class="btn-secondary">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
