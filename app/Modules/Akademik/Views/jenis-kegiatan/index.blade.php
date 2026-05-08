@extends('layouts.app')

@section('title', 'Jenis Kegiatan')

@section('content')
<div class="container-fluid">
    {{-- Success/Error Messages --}}
    @if(session('success'))
        <x-alert type="success" :message="session('success')" dismissible />
    @endif

    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-gray-800">Manajemen Jenis Kegiatan</h1>
            @if(Auth::check() && !Auth::user()->hasRole('GURU') && !Auth::user()->hasRole('SISWA'))
            <x-btn :href="route('akademik.jenis-kegiatan.create')" icon="fas fa-plus">
                Tambah Kegiatan
            </x-btn>
            @endif
        </div>
    </div>

    {{-- Search & Filter --}}
    <x-card title="Filter Data" icon="fas fa-filter" outline collapsible>
        <form action="{{ route('akademik.jenis-kegiatan.index') }}" method="GET">
            <div class="row align-items-end">
                <div class="col-md-5 mb-3">
                    <x-input label="Pencarian Kata Kunci" name="search" :value="request('search')" placeholder="Ketik nama kegiatan..." prepend="<i class='fas fa-search'></i>" />
                </div>
                <div class="col-md-4 mb-3">
                    <div class="btn-group w-100">
                        <x-btn type="submit" class="btn-info flex-fill" icon="fas fa-search">
                            Cari Data
                        </x-btn>
                        @if(request('search'))
                            <x-btn :href="route('akademik.jenis-kegiatan.index')" class="btn-secondary" icon="fas fa-sync" title="Reset" />
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </x-card>

    {{-- Tabel Data --}}
    <x-card title="Daftar Klasifikasi Kegiatan" type="primary" outline>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="200px">Jenis Kegiatan</th>
                        <th>Deskripsi Singkat</th>
                        <th width="120px" class="text-center">Status KBM</th>
                        <th width="200px" class="text-center">Total Penggunaan</th>

                        @if(Auth::check() && !Auth::user()->hasRole('GURU') && !Auth::user()->hasRole('SISWA'))
                        <th width="150px" class="text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($jenisKegiatan as $item)
                    <tr>
                        <td class="text-center">
                            {{ $loop->iteration + ($jenisKegiatan->currentPage() - 1) * $jenisKegiatan->perPage() }}
                        </td>
                        <td><span class="badge badge-light border">{{ $item->jeniskegiatan }}</span></td>
                        <td class="text-muted text-wrap">
                            {{ Str::limit($item->deskripsi ?? '-', 100) }}
                        </td>
                        <td class="text-center">
                            @if($item->is_kbm)
                                <span class="badge badge-success"><i class="fas fa-check-circle"></i> KBM Aktif</span>
                            @else
                                <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Non-KBM</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if(($item->kalender_akademik_count ?? $item->kalenderAkademik->count()) > 0)
                                <span class="badge badge-success px-3 py-2">
                                    {{ $item->kalender_akademik_count ?? $item->kalenderAkademik->count() }} Kegiatan
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        @if(Auth::check() && !Auth::user()->hasRole('GURU') && !Auth::user()->hasRole('SISWA'))
                        <td class="text-center">
                            <div class="btn-group">
                                <x-btn :href="route('akademik.jenis-kegiatan.show', $item->id)" size="sm" class="btn-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </x-btn>
                                <x-btn :href="route('akademik.jenis-kegiatan.edit', $item->id)" size="sm" class="btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </x-btn>
                                <form action="{{ route('akademik.jenis-kegiatan.destroy', $item->id) }}" method="POST" class="d-inline">
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
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fas fa-folder-open fa-2x mb-3"></i><br>
                            Tidak ada master jenis kegiatan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($jenisKegiatan->hasPages())
        <x-slot name="footer">
            {{ $jenisKegiatan->withQueryString()->links() }}
        </x-slot>
        @endif
    </x-card>
</div>
@endsection

