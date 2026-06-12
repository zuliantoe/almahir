@extends('layouts.app')

@section('title', 'Mata Pelajaran')

@section('content')
<div class="container-fluid">
    {{-- Success/Error Messages --}}
    @if(session('success'))
        <x-alert type="success" :message="session('success')" dismissible />
    @endif

    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-gray-800">Manajemen Mata Pelajaran</h1>
            @if(Auth::check() && !Auth::user()->hasRole('GURU') && !Auth::user()->hasRole('SISWA'))
            <x-btn :href="route('akademik.mata-pelajaran.create')" icon="fas fa-plus">
                Tambah Pelajaran Baru
            </x-btn>
            @endif
        </div>
    </div>

    {{-- Search & Filter --}}
    <x-card title="Filter Data" icon="fas fa-filter" outline collapsible>
        <form method="GET">
            <div class="row align-items-end">
                <div class="col-md-5 mb-3">
                    <x-input label="Cari Kode/Nama" name="search" :value="request('search')" placeholder="Ketik kata kunci..." prepend="<i class='fas fa-search'></i>" />
                </div>

                <div class="col-md-4 mb-3">
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="kategori" class="form-control">
                            <option value="">Semua Kategori</option>
                            @foreach($kategoriList as $kategori)
                            <option value="{{ $kategori->id }}" {{ request('kategori') == $kategori->id ? 'selected' : '' }}>
                                {{ $kategori->kategori }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="btn-group w-100">
                        <x-btn type="submit" class="btn-info flex-fill" icon="fas fa-search">
                            Filter
                        </x-btn>
                        @if(request()->has('search') || request()->has('kategori'))
                            <x-btn :href="route('akademik.mata-pelajaran.index')" class="btn-secondary" icon="fas fa-sync" title="Reset" />
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </x-card>

    {{-- Tabel --}}
    <x-card title="Daftar Mata Pelajaran" type="primary" outline>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th>Kode</th>
                        <th>Nama Mata Pelajaran</th>
                        <th>Kategori</th>
                        <th>Tipe Kelas</th>
                        @if(Auth::check() && !Auth::user()->hasRole('GURU') && !Auth::user()->hasRole('SISWA'))
                        <th width="150px" class="text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($mataPelajaran as $item)
                    <tr>
                        <td class="text-center">
                            {{ $loop->iteration + ($mataPelajaran->currentPage() - 1) * $mataPelajaran->perPage() }}
                        </td>
                        <td><span class="badge badge-light border">{{ $item->kode }}</span></td>
                        <td><strong>{{ $item->nama }}</strong></td>
                        <td>
                            @if(isset($item->kategori->kategori))
                                <span class="badge badge-info">{{ $item->kategori->kategori }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($item->bisa_double)
                                <span class="badge badge-success" style="background: rgba(40, 167, 69, 0.15); color: #28a745; border: 1px solid rgba(40,167,69,0.3);"><i class="fas fa-check-double mr-1"></i> Kelas Gabungan (Double)</span>
                            @else
                                <span class="badge badge-secondary" style="background: rgba(108, 117, 125, 0.15); color: #6c757d; border: 1px solid rgba(108,117,125,0.3);">Kelas Reguler</span>
                            @endif
                        </td>
                        @if(Auth::check() && !Auth::user()->hasRole('GURU') && !Auth::user()->hasRole('SISWA'))
                        <td class="text-center">
                            <div class="d-flex justify-content-center align-items-center" style="gap: 6px;">
                                <x-btn :href="route('akademik.mata-pelajaran.show', $item)" size="sm" class="btn-info" title="Detail" style="margin: 0;">
                                    <i class="fas fa-eye"></i>
                                </x-btn>
                                <x-btn :href="route('akademik.mata-pelajaran.edit', $item)" size="sm" class="btn-warning" title="Edit" style="margin: 0;">
                                    <i class="fas fa-edit"></i>
                                </x-btn>
                                <form action="{{ route('akademik.mata-pelajaran.destroy', $item->id) }}" method="POST" class="d-inline" style="margin: 0;">
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
                            Tidak ada data mata pelajaran.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($mataPelajaran->hasPages())
        <x-slot name="footer">
            {{ $mataPelajaran->withQueryString()->links() }}
        </x-slot>
        @endif
    </x-card>
</div>
@endsection

