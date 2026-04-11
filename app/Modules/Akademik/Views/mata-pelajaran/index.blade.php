@extends('layouts.app')

@section('title', 'Mata Pelajaran')

@include('akademik::components.style')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2 class="font-weight-bold text-dark mb-0">Manajemen Mata Pelajaran</h2>
            <a href="{{ route('akademik.mata-pelajaran.create') }}" class="btn btn-primary btn-modern">
                <i class="fas fa-plus mr-1"></i> Tambah Pelajaran Baru
            </a>
        </div>
    </div>

    {{-- Search & Filter --}}
    <div class="card card-modern mb-4">
        <div class="card-body">
            <form method="GET">
                <div class="row align-items-end">
                    <div class="col-md-4 mb-3">
                        <label class="font-weight-bold">Cari Kode/Nama</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                            </div>
                            <input type="text" name="search"
                                value="{{ request('search') }}"
                                class="form-control form-control-modern" placeholder="Ketik kata kunci...">
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="font-weight-bold">Kategori</label>
                        <select name="kategori" class="form-control form-control-modern">
                            <option value="">Semua Kategori</option>
                            @foreach($kategoriList as $kategori)
                            <option value="{{ $kategori->id }}"
                                {{ request('kategori') == $kategori->id ? 'selected' : '' }}>
                                {{ $kategori->kategori }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <button type="submit" class="btn btn-primary btn-modern mr-2">
                            <i class="fas fa-filter"></i> Terapkan
                        </button>
                        @if(request()->has('search') || request()->has('kategori'))
                        <a href="{{ route('akademik.mata-pelajaran.index') }}" class="btn btn-secondary btn-modern">
                            <i class="fas fa-sync"></i> Reset
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card card-modern">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover table-modern text-nowrap">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th>Kode</th>
                        <th>Nama Mata Pelajaran</th>
                        <th>Kategori</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mataPelajaran as $item)
                    <tr>
                        <td class="text-center">
                            {{ $loop->iteration + ($mataPelajaran->currentPage() - 1) * $mataPelajaran->perPage() }}
                        </td>
                        <td><span class="badge badge-light badge-modern">{{ $item->kode }}</span></td>
                        <td class="font-weight-bold text-dark">{{ $item->nama }}</td>
                        <td>
                            @if(isset($item->kategori->kategori))
                                <span class="badge badge-info badge-modern">{{ $item->kategori->kategori }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('akademik.mata-pelajaran.show', $item) }}" class="btn btn-info btn-sm btn-modern" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('akademik.mata-pelajaran.edit', $item) }}" class="btn btn-warning btn-sm btn-modern text-white" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('akademik.mata-pelajaran.destroy', $item) }}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')" class="btn btn-danger btn-sm btn-modern" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted font-weight-bold">Tidak ada data mata pelajaran.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($mataPelajaran->hasPages())
        <div class="card-footer bg-white pt-3 pb-0">
            {{ $mataPelajaran->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
