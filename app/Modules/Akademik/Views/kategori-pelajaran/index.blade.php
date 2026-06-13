@extends('layouts.app')

@section('title', 'Kategori Pelajaran')

@section('content')
<div class="container-fluid">
    {{-- Success/Error Messages --}}
    @if(session('success'))
        <x-alert type="success" :message="session('success')" dismissible />
    @endif
    @if(session('error'))
        <x-alert type="danger" :message="session('error')" dismissible />
    @endif

    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-gray-800">Kategori Mata Pelajaran</h1>
            @if(!Auth::user()->hasRole(['GURU', 'SISWA']))
            <x-btn :href="route('akademik.kategori-pelajaran.create')" icon="fas fa-plus">
                Tambah Kategori
            </x-btn>
            @endif
        </div>
    </div>

    <x-card title="Daftar Kategori" icon="fas fa-tags" type="primary" outline>
        <div class="table-responsive">
            {{-- Search Bar --}}
            <div class="mb-3">
                <form action="{{ route('akademik.kategori-pelajaran.index') }}" method="GET">
                    <div class="row align-items-end">
                        <div class="col-md-5">
                            <x-input label="Cari Kategori" name="search" :value="request('search')" placeholder="Cari nama kategori..." prepend="<i class='fas fa-search'></i>" />
                        </div>
                        <div class="col-md-3">
                             <div class="btn-group w-100">
                                <x-btn type="submit" class="btn-info" icon="fas fa-search">Cari</x-btn>
                                @if(request('search'))
                                    <x-btn :href="route('akademik.kategori-pelajaran.index')" class="btn-secondary" icon="fas fa-times">Reset</x-btn>
                                @endif
                             </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th>Nama Kategori</th>
                        <th class="text-center">Jumlah Mata Pelajaran</th>
                        @if(!Auth::user()->hasRole(['GURU', 'SISWA']))
                        <th width="150px" class="text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($kategoriPelajaran as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration + ($kategoriPelajaran->currentPage() - 1) * $kategoriPelajaran->perPage() }}</td>
                        <td><strong>{{ $item->kategori }}</strong></td>
                        <td class="text-center">
                            @if(($item->mata_pelajaran_count ?? $item->mataPelajaran->count()) > 0)
                                <span class="badge badge-info px-3 py-2">
                                    {{ $item->mata_pelajaran_count ?? $item->mataPelajaran->count() }} Mapel
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        @if(!Auth::user()->hasRole(['GURU', 'SISWA']))
                        <td class="text-center">
                            <div class="d-flex justify-content-center align-items-center" style="gap: 6px;">
                                <x-btn :href="route('akademik.kategori-pelajaran.show', $item->id)" size="sm" class="btn-info" title="Detail" style="margin: 0;">
                                    <i class="fas fa-eye"></i>
                                </x-btn>
                                <x-btn :href="route('akademik.kategori-pelajaran.edit', $item->id)" size="sm" class="btn-warning" title="Edit" style="margin: 0;">
                                    <i class="fas fa-edit"></i>
                                </x-btn>
                                <form action="{{ route('akademik.kategori-pelajaran.destroy', $item->id) }}" method="POST" class="d-inline" style="margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <x-btn type="submit" size="sm" class="btn-danger btn-delete" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </x-btn>
                                </form>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ Auth::user()->hasRole(['GURU', 'SISWA']) ? 3 : 4 }}" class="text-center py-5 text-muted">
                            <i class="fas fa-tags fa-2x mb-3"></i><br>
                            Tidak ada data kategori pelajaran.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($kategoriPelajaran->hasPages())
        <x-slot name="footer">
            {{ $kategoriPelajaran->links() }}
        </x-slot>
        @endif
    </x-card>
</div>
@endsection

