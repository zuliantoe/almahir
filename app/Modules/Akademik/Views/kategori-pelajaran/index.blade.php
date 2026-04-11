@extends('layouts.app')

@section('title', 'Kategori Pelajaran')

@include('akademik::components.style')

@section('content')
<div class="container-fluid py-4">

    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2 class="font-weight-bold text-dark mb-0">Kategori Mata Pelajaran</h2>
            <a href="{{ route('akademik.kategori-pelajaran.create') }}" class="btn btn-primary btn-modern">
                <i class="fas fa-plus mr-1"></i> Tambah Kategori
            </a>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" style="border-radius: 0.5rem;" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" style="border-radius: 0.5rem;" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    <div class="card card-modern">
        <div class="card-header bg-gradient-orange">
            <h3 class="card-title text-white">
                <i class="fas fa-tags mr-2"></i>
                Daftar Kategori
            </h3>
        </div>
        <div class="card-body p-0 table-responsive">
            
            {{-- Search Bar --}}
            <div class="p-3 bg-light border-bottom">
                <form action="{{ route('akademik.kategori-pelajaran.index') }}" method="GET">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-5">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                                </div>
                                <input type="text"
                                       name="search"
                                       class="form-control form-control-modern"
                                       placeholder="Cari nama kategori..."
                                       value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button class="btn btn-primary btn-modern" type="submit" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                        Cari
                                    </button>
                                </div>
                            </div>
                        </div>
                        @if(request('search'))
                        <div class="col-auto">
                            <a href="{{ route('akademik.kategori-pelajaran.index') }}" class="btn btn-secondary btn-modern">
                                <i class="fas fa-times"></i> Reset
                            </a>
                        </div>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <table class="table table-hover table-modern text-nowrap">
                <thead class="text-center">
                    <tr>
                        <th width="5%">No</th>
                        <th class="text-left">Nama Kategori</th>
                        <th>Jumlah Mata Pelajaran</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kategoriPelajaran as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration + ($kategoriPelajaran->currentPage() - 1) * $kategoriPelajaran->perPage() }}</td>
                        <td class="font-weight-bold text-dark">{{ $item->kategori }}</td>
                        <td class="text-center">
                            @if(($item->mata_pelajaran_count ?? $item->mataPelajaran->count()) > 0)
                                <span class="badge badge-info badge-modern px-3 py-2">
                                    {{ $item->mata_pelajaran_count ?? $item->mataPelajaran->count() }} Mapel
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('akademik.kategori-pelajaran.show', $item->id) }}"
                               class="btn btn-sm btn-info btn-modern" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('akademik.kategori-pelajaran.edit', $item->id) }}"
                               class="btn btn-sm btn-warning btn-modern text-white" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('akademik.kategori-pelajaran.destroy', $item->id) }}"
                                  method="POST"
                                  class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="btn btn-sm btn-danger btn-modern"
                                        title="Hapus"
                                        onclick="return confirm('Yakin ingin menghapus kategori ini?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                            <p class="font-weight-bold text-muted">Tidak ada data kategori pelajaran.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($kategoriPelajaran->hasPages())
        <div class="card-footer bg-white pt-3 border-0">
            {{ $kategoriPelajaran->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
