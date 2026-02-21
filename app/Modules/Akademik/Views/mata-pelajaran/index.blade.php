@extends('layouts.app')

@section('title', 'Mata Pelajaran')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-semibold">Daftar Mata Pelajaran</h3>
        @can('isAdmin')
        <a href="{{ route('akademik.mata-pelajaran.create') }}" class="btn-primary">
            Tambah Mata Pelajaran
        </a>
        @endcan
    </div>

    {{-- Search & Filter --}}
    <div class="mb-6 card">
        <form method="GET">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="form-label">Cari Kode/Nama</label>
                    <input type="text" name="search"
                        value="{{ request('search') }}"
                        class="form-input">
                </div>

                <div>
                    <label class="form-label">Kategori</label>
                    <select name="kategori" class="form-input">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoriList as $kategori)
                        <option value="{{ $kategori->id }}"
                            {{ request('kategori') == $kategori->id ? 'selected' : '' }}>
                            {{ $kategori->kategori }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button class="btn-primary">Terapkan</button>
                    @if(request()->has('search') || request()->has('kategori'))
                    <a href="{{ route('akademik.mata-pelajaran.index') }}" class="btn-secondary">
                        Reset
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">Kode</th>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Kategori</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mataPelajaran as $item)
                <tr class="border-t hover:bg-gray-50">
                    <td class="px-4 py-3">
                        {{ $loop->iteration + ($mataPelajaran->currentPage() - 1) * $mataPelajaran->perPage() }}
                    </td>
                    <td class="px-4 py-3">{{ $item->kode }}</td>
                    <td class="px-4 py-3">{{ $item->nama }}</td>
                    <td class="px-4 py-3">
                        {{ $item->kategori->kategori ?? '-' }}
                    </td>
                    <td class="px-4 py-3 flex gap-2">
                        <a href="{{ route('akademik.mata-pelajaran.show', $item) }}" class="text-blue-500">Detail</a>
                        <a href="{{ route('akademik.mata-pelajaran.edit', $item) }}" class="text-green-500">Edit</a>
                        <form method="POST" action="{{ route('akademik.mata-pelajaran.destroy', $item) }}">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Yakin?')" class="text-red-500">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4">
                        Tidak ada data.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $mataPelajaran->withQueryString()->links() }}
    </div>
</div>
@endsection
