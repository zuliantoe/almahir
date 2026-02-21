@extends('layouts.app')

@section('title', 'Detail Jenis Kegiatan')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-xl shadow-md p-6">

    {{-- Header --}}
    <div class="flex justify-between items-start mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">
                Detail Jenis Kegiatan
            </h2>
            <p class="text-sm text-gray-500">
                Informasi lengkap jenis kegiatan kalender akademik
            </p>
        </div>

        <span class="px-3 py-1 text-sm bg-indigo-100 text-indigo-700 rounded-full">
            {{ $jenisKegiatan->kalenderAkademik->count() }} Kegiatan
        </span>
    </div>

    {{-- Detail Card --}}
    <div class="space-y-5">

        {{-- Nama --}}
        <div>
            <p class="text-sm text-gray-500">Nama Jenis Kegiatan</p>
            <p class="text-lg font-medium text-gray-800">
                {{ $jenisKegiatan->jeniskegiatan }}
            </p>
        </div>

        {{-- Deskripsi --}}
        <div>
            <p class="text-sm text-gray-500">Deskripsi</p>
            <p class="text-gray-700">
                {{ $jenisKegiatan->deskripsi ?? '-' }}
            </p>
        </div>

        {{-- Statistik --}}
        <div class="bg-gray-50 rounded-lg p-4 border">
            <p class="text-sm text-gray-500">Jumlah Kegiatan Terkait</p>
            <p class="text-2xl font-semibold text-indigo-600">
                {{ $jenisKegiatan->kalenderAkademik->count() }}
            </p>
        </div>

    </div>

    {{-- Action Buttons --}}
    <div class="mt-8 flex justify-end gap-3">

        <a href="{{ route('akademik.jenis-kegiatan.index') }}"
           class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            Kembali
        </a>

        <a href="{{ route('akademik.jenis-kegiatan.edit', $jenisKegiatan->id) }}"
           class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
            Edit
        </a>

        <form action="{{ route('akademik.jenis-kegiatan.destroy', $jenisKegiatan->id) }}"
              method="POST"
              onsubmit="return confirm('Yakin ingin menghapus jenis kegiatan ini?')">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                Hapus
            </button>
        </form>

    </div>

</div>
@endsection
