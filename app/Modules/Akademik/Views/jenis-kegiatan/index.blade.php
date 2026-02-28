@extends('layouts.app')

@section('title', 'Jenis Kegiatan')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-xl font-semibold">Daftar Jenis Kegiatan</h3>
            <p class="text-sm text-gray-500">Manajemen jenis kegiatan kalender akademik</p>
        </div>
        <a href="{{ route('akademik.jenis-kegiatan.create') }}" class="btn-primary">
            + Tambah Jenis Kegiatan
        </a>
    </div>

    {{-- Search --}}
    <div class="mb-6 bg-gray-50 p-4 rounded-lg">
        <form action="{{ route('akademik.jenis-kegiatan.index') }}" method="GET">
            <div class="flex items-center gap-3">
                <input type="text"
                       name="search"
                       placeholder="Cari jenis kegiatan..."
                       value="{{ request('search') }}"
                       class="form-input w-full">

                <button type="submit" class="btn-primary">
                    Cari
                </button>

                @if(request('search'))
                    <a href="{{ route('akademik.jenis-kegiatan.index') }}" class="btn-secondary">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-gray-100 text-gray-700 text-sm uppercase">
                <tr>
                    <th class="px-4 py-3 text-left w-16">No</th>
                    <th class="px-4 py-3 text-left">Jenis Kegiatan</th>
                    <th class="px-4 py-3 text-left">Deskripsi</th>
                    <th class="px-4 py-3 text-left w-40">Jumlah Kegiatan</th>
                    <th class="px-4 py-3 text-left w-40">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm">
                @forelse($jenisKegiatan as $item)
                <tr class="border-t hover:bg-gray-50 transition">
                    <td class="px-4 py-3">
                        {{ $loop->iteration + ($jenisKegiatan->currentPage() - 1) * $jenisKegiatan->perPage() }}
                    </td>

                    <td class="px-4 py-3 font-medium">
                        {{ $item->jeniskegiatan }}
                    </td>

                    <td class="px-4 py-3 text-gray-500">
                        {{ $item->deskripsi ?? '-' }}
                    </td>

                    <td class="px-4 py-3">
                        <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs">
                            {{ $item->kalender_akademik_count ?? $item->kalenderAkademik->count() }}
                            kegiatan
                        </span>
                    </td>

                    <td class="px-4 py-3">
                        <div class="flex gap-3 items-center">

                            {{-- Show --}}
                            <a href="{{ route('akademik.jenis-kegiatan.show', $item->id) }}"
                               class="text-blue-500 hover:text-blue-700">
                                👁
                            </a>

                            {{-- Edit --}}
                            <a href="{{ route('akademik.jenis-kegiatan.edit', $item->id) }}"
                               class="text-green-500 hover:text-green-700">
                                ✏
                            </a>

                            {{-- Delete --}}
                            <form action="{{ route('akademik.jenis-kegiatan.destroy', $item->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-500 hover:text-red-700">
                                    🗑
                                </button>
                            </form>

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-gray-400">
                        Tidak ada data jenis kegiatan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $jenisKegiatan->withQueryString()->links() }}
    </div>

</div>
@endsection
