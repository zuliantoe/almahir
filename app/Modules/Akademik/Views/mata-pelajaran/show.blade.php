@extends('layouts.app')

@section('title', 'Detail Mata Pelajaran')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h3 class="text-xl font-semibold mb-6">Detail Mata Pelajaran</h3>

    <p><strong>Kode:</strong> {{ $mataPelajaran->kode }}</p>
    <p><strong>Nama:</strong> {{ $mataPelajaran->nama }}</p>
    <p><strong>Kategori:</strong> {{ $mataPelajaran->kategori->kategori ?? '-' }}</p>

    <div class="mt-6 flex gap-2">
        <a href="{{ route('akademik.mata-pelajaran.index') }}" class="btn-secondary">
            Kembali
        </a>
        <a href="{{ route('akademik.mata-pelajaran.edit', $mataPelajaran) }}"
            class="btn-primary">
            Edit
        </a>
    </div>
</div>
@endsection
