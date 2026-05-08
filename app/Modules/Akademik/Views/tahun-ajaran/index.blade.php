@extends('layouts.app')

@section('title', 'Data Tahun Ajaran')

@section('content')
<div class="container-fluid">
    {{-- Success/Error Messages --}}
    @if(session('success'))
        <x-alert type="success" :message="session('success')" dismissible />
    @endif

    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-gray-800">Manajemen Tahun Ajaran</h1>
            @if(Auth::check() && !Auth::user()->hasRole('GURU') && !Auth::user()->hasRole('SISWA'))
            <x-btn :href="route('akademik.tahun-ajaran.create')" icon="fas fa-plus">
                Tambah Tahun Ajaran
            </x-btn>
            @endif
        </div>
    </div>

    {{-- Form Filter --}}
    <x-card title="Filter Data" icon="fas fa-filter" outline collapsible>
        <form action="{{ route('akademik.tahun-ajaran.index') }}" method="GET">
            <div class="row align-items-end">
                <div class="col-md-5 mb-3">
                    <x-input label="Cari Tahun Ajaran" name="search" :value="request('search')" placeholder="Masukkan tahun ajaran..." prepend="<i class='fas fa-search'></i>" />
                </div>

                <div class="col-md-4 mb-3">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <x-btn type="submit" class="btn-info w-100" icon="fas fa-filter">
                        Filter
                    </x-btn>
                </div>
            </div>
        </form>
    </x-card>

    {{-- Tabel Data --}}
    <x-card title="Daftar Periode Akademik" type="primary" outline>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th>Tahun Ajaran</th>
                        <th>Status</th>
                        <th>Dibuat</th>
                        @if(Auth::check() && !Auth::user()->hasRole('GURU') && !Auth::user()->hasRole('SISWA'))
                        <th width="150px" class="text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($tahunAjaran as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration + ($tahunAjaran->currentPage() - 1) * $tahunAjaran->perPage() }}</td>
                        <td><span class="badge badge-light border">{{ $item->tahunajaran }}</span></td>
                        <td>
                            @if($item->status)
                                <span class="badge badge-success">
                                    <i class="fas fa-check-circle"></i> Aktif
                                </span>
                            @else
                                <span class="badge badge-secondary">
                                    <i class="fas fa-times-circle"></i> Tidak Aktif
                                </span>
                            @endif
                        </td>
                        <td class="text-muted"><i class="far fa-clock mr-1"></i> {{ $item->created_at->format('d/m/Y H:i') }}</td>
                        @if(Auth::check() && !Auth::user()->hasRole('GURU') && !Auth::user()->hasRole('SISWA'))
                        <td class="text-center">
                            <div class="btn-group">
                                <x-btn :href="route('akademik.tahun-ajaran.show', $item->id)" size="sm" class="btn-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </x-btn>
                                <x-btn :href="route('akademik.tahun-ajaran.edit', $item->id)" size="sm" class="btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </x-btn>
                                <form action="{{ route('akademik.tahun-ajaran.destroy', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <x-btn type="submit" size="sm" class="btn-danger btn-delete" title="Hapus" 
                                           :disabled="$item->status">
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
                            <i class="fas fa-calendar-times fa-2x mb-3"></i><br>
                            Tidak ada data tahun ajaran
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($tahunAjaran->hasPages())
        <x-slot name="footer">
            {{ $tahunAjaran->withQueryString()->links() }}
        </x-slot>
        @endif
    </x-card>
</div>
@endsection

