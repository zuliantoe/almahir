@extends('layouts.app')

@section('title', 'Tambah Jenis Kegiatan')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow-md p-6">

    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800">
            Tambah Jenis Kegiatan
        </h2>
        <p class="text-sm text-gray-500">
            Tambahkan jenis kegiatan baru untuk kalender akademik
        </p>
    </div>

    {{-- Error Alert --}}
    @if ($errors->any())
        <div class="mb-5 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('akademik.jenis-kegiatan.store') }}" method="POST">
        @csrf

        {{-- Nama Jenis Kegiatan --}}
        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Nama Jenis Kegiatan <span class="text-red-500">*</span>
            </label>

            <input type="text"
                   name="jeniskegiatan"
                   value="{{ old('jeniskegiatan') }}"
                   maxlength="100"
                   required
                   class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-400 focus:outline-none
                          @error('jeniskegiatan') border-red-500 @enderror"
                   placeholder="Contoh: Ujian Tengah Semester">

            @error('jeniskegiatan')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror

            <p class="text-xs text-gray-500 mt-1">
                Maksimal 100 karakter dan harus unik.
            </p>
        </div>

        {{-- Deskripsi --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Deskripsi
            </label>

            <textarea name="deskripsi"
                      rows="4"
                      maxlength="500"
                      class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-400 focus:outline-none
                             @error('deskripsi') border-red-500 @enderror"
                      placeholder="Tambahkan deskripsi kegiatan (opsional)">{{ old('deskripsi') }}</textarea>

            @error('deskripsi')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror

            <p class="text-xs text-gray-500 mt-1">
                Maksimal 500 karakter.
            </p>
        </div>

        {{-- Button --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('akademik.jenis-kegiatan.index') }}"
               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                Batal
            </a>

            <button type="submit"
                    class="px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                Simpan
            </button>
        </div>

    </form>
</div>
@endsection
