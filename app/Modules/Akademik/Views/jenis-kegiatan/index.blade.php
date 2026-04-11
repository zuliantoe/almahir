@extends('layouts.app')

@section('title', 'Jenis Kegiatan')

@include('akademik::components.style')

@section('content')
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h2 class="font-weight-bold text-dark mb-0">Manajemen Jenis Kegiatan</h2>
                <p class="text-muted mb-0">Daftar klasifikasi kegiatan pada kalender akademik</p>
            </div>
            <a href="{{ route('akademik.jenis-kegiatan.create') }}" class="btn btn-primary btn-modern">
                <i class="fas fa-plus mr-1"></i> Tambah Kegiatan
            </a>
        </div>
    </div>

    {{-- Search & Filter --}}
    <div class="card card-modern mb-4">
        <div class="card-body">
            <form action="{{ route('akademik.jenis-kegiatan.index') }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-5 mb-3">
                        <label class="font-weight-bold">Pencarian Kata Kunci</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                            </div>
                            <input type="text"
                                   name="search"
                                   placeholder="Ketik nama kegiatan..."
                                   value="{{ request('search') }}"
                                   class="form-control form-control-modern">
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <button class="btn btn-primary btn-modern mr-2" type="submit">
                            <i class="fas fa-search"></i> Cari Data
                        </button>
                        @if(request('search'))
                        <a href="{{ route('akademik.jenis-kegiatan.index') }}" class="btn btn-secondary btn-modern">
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
                        <th class="w-25">Jenis Kegiatan</th>
                        <th>Deskripsi Singkat</th>
                        <th width="15%" class="text-center">Dipakai Di Kalender</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jenisKegiatan as $item)
                    <tr>
                        <td class="text-center">
                            {{ $loop->iteration + ($jenisKegiatan->currentPage() - 1) * $jenisKegiatan->perPage() }}
                        </td>
                        <td class="font-weight-bold text-dark">
                            <span class="badge badge-light badge-modern" style="font-size: 0.9rem;">{{ $item->jeniskegiatan }}</span>
                        </td>
                        <td class="text-muted text-wrap">
                            {{ Str::limit($item->deskripsi ?? '-', 60) }}
                        </td>
                        <td class="text-center">
                            @if(($item->kalender_akademik_count ?? $item->kalenderAkademik->count()) > 0)
                                <span class="badge badge-success badge-modern px-3 py-2">
                                    {{ $item->kalender_akademik_count ?? $item->kalenderAkademik->count() }} Kegiatan
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('akademik.jenis-kegiatan.show', $item->id) }}"
                               class="btn btn-info btn-sm btn-modern" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('akademik.jenis-kegiatan.edit', $item->id) }}"
                               class="btn btn-warning btn-sm btn-modern text-white" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('akademik.jenis-kegiatan.destroy', $item->id) }}"
                                  method="POST"
                                  class="d-inline"
                                  onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm btn-modern" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                            <p class="font-weight-bold text-muted">Tidak ada master jenis kegiatan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($jenisKegiatan->hasPages())
        <div class="card-footer bg-white pt-3 pb-0">
            {{ $jenisKegiatan->withQueryString()->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
