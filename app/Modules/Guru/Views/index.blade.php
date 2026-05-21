@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    {{-- Alert Messages --}}
    @if(session('success'))
        <x-alert type="success" :message="session('success')" dismissible />
    @endif
    @if(session('error'))
        <x-alert type="danger" :message="session('error')" dismissible />
    @endif

    <x-card title="Data Guru" icon="fas fa-chalkboard-teacher">
        <x-slot name="tools">
            <a href="{{ route('guru.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Tambah Guru
            </a>
        </x-slot>

        {{-- Filter Form --}}
        <form action="{{ route('guru.index') }}" method="GET" class="mb-3">
            <div class="row">
                <div class="col-md-4">
                    <x-input name="search" placeholder="Cari nama/NIP/email..." :value="request('search')" />
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-control">
                        <option value="">-- Semua Status --</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Non-Aktif</option>
                        <option value="pensiun" {{ request('status') == 'pensiun' ? 'selected' : '' }}>Pensiun</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <x-btn type="submit" variant="info" icon="fas fa-search">Cari</x-btn>
                </div>
            </div>
        </form>

        {{-- Data Table --}}
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th width="50">No</th>
                        <th>NIP</th>
                        <th>Nama</th>
                        <th>Status</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($guru as $index => $g)
                    <tr>
                        <td>{{ $guru->firstItem() + $index }}</td>
                        <td>{{ $g->nip ?? '-' }}</td>
                        <td>
                            <strong>{{ $g->nama }}</strong>
                            @if($g->user && $g->user->email)
                            <br><small class="text-muted">{{ $g->user->email }}</small>
                            @endif
                        </td>
                        <td>
                            @if($g->status == 'aktif')
                                <span class="badge badge-success">Aktif</span>
                            @elseif($g->status == 'nonaktif')
                                <span class="badge badge-warning">Non-Aktif</span>
                            @else
                                <span class="badge badge-secondary">Pensiun</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('guru.show', $g->id) }}" class="btn btn-info btn-sm" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('guru.edit', $g->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('guru.destroy', $g->id) }}" method="POST" class="d-inline" 
                                  onsubmit="return confirm('Yakin ingin menghapus data guru ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-3x mb-2"></i>
                            <p>Belum ada data guru</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-between align-items-center">
            <div>Menampilkan {{ $guru->count() }} dari {{ $guru->total() }} data</div>
            {{ $guru->withQueryString()->links() }}
        </div>
    </x-card>
</div>
@endsection
