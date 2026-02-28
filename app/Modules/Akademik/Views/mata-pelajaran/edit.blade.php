@extends('layouts.app')

@section('title', 'Edit Mata Pelajaran')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h3 class="text-xl font-semibold mb-6">Edit Mata Pelajaran</h3>

    <form method="POST"
        action="{{ route('akademik.mata-pelajaran.update', $mataPelajaran) }}">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="form-label">Kode</label>
            <input type="text" name="kode"
                value="{{ old('kode', $mataPelajaran->kode) }}"
                class="form-input w-full">
        </div>

        <div class="mb-4">
            <label class="form-label">Nama</label>
            <input type="text" name="nama"
                value="{{ old('nama', $mataPelajaran->nama) }}"
                class="form-input w-full">
        </div>

        <div class="mb-4">
            <label class="form-label">Kategori</label>
            <select name="kategori_id" class="form-input w-full">
                @foreach($kategoriList as $kategori)
                <option value="{{ $kategori->id }}"
                    {{ old('kategori_id', $mataPelajaran->kategori_id) == $kategori->id ? 'selected' : '' }}>
                    {{ $kategori->kategori }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="flex gap-2">
            <button class="btn-primary">Update</button>
            <a href="{{ route('akademik.mata-pelajaran.index') }}" class="btn-secondary">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
