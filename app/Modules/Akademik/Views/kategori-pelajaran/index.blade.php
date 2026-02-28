@extends('layouts.app')

@section('title', 'Kategori Pelajaran')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Kategori Pelajaran</h3>
                    <div class="card-tools">
                        <a href="{{ route('akademik.kategori-pelajaran.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Kategori
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Search --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form action="{{ route('akademik.kategori-pelajaran.index') }}" method="GET" class="form-inline">
                                <div class="input-group">
                                    <input type="text"
                                           name="search"
                                           class="form-control"
                                           placeholder="Cari kategori..."
                                           value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        @if(request('search'))
                                            <a href="{{ route('akademik.kategori-pelajaran.index') }}" class="btn btn-secondary">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Flash Messages --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    @endif

                    {{-- Table --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th width="50">No</th>
                                    <th>Kategori</th>
                                    <th width="200">Jumlah Mata Pelajaran</th>
                                    <th width="200">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kategoriPelajaran as $item)
                                <tr>
                                    <td>{{ $loop->iteration + ($kategoriPelajaran->currentPage() - 1) * $kategoriPelajaran->perPage() }}</td>
                                    <td>{{ $item->kategori }}</td>
                                    <td>
                                        <span class="badge badge-info">
                                            {{ $item->mata_pelajaran_count ?? $item->mataPelajaran->count() }} Mata Pelajaran
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('akademik.kategori-pelajaran.show', $item->id) }}"
                                               class="btn btn-sm btn-info"
                                               title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('akademik.kategori-pelajaran.edit', $item->id) }}"
                                               class="btn btn-sm btn-warning"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('akademik.kategori-pelajaran.destroy', $item->id) }}"
                                                  method="POST"
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-danger"
                                                        title="Hapus"
                                                        onclick="return confirm('Yakin ingin menghapus kategori ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        Tidak ada data kategori pelajaran.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-3">
                        {{ $kategoriPelajaran->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
