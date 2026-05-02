@extends('layouts.app')

@section('title', $title ?? 'Data Siswa')

@section('content-header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">{{ $title ?? 'Data Siswa' }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active">Siswa</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    {{-- Page Header with Action Button --}}
    <x-card title="Daftar Siswa" type="primary">
        <x-slot name="tools">
            <x-btn class="btn-success btn-sm" icon="fas fa-plus" href="{{ route('siswa.create') }}">
                Tambah Siswa
            </x-btn>
        </x-slot>

        {{-- Alert for Success Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        {{-- Data Table --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="bg-primary text-white">
                    <tr>
                        <th style="width: 50px">#</th>
                        <th>NIS</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>Kelas</th>
                        <th>Status</th>
                        <th style="width: 150px">Aksi</th>
                    </tr>
                </thead>
                    @forelse($siswa as $index => $s)
                        <tr>
                            <td>{{ $siswa->firstItem() + $index }}</td>
                            <td>{{ $s->nis }}</td>
                            <td>{{ $s->nama }}</td>
                            <td>{{ $s->email }}</td>
                            <td>{{ $s->kelas_id ?? '-' }}</td>
                            <td>
                                <span class="badge badge-{{ $s->status === 'aktif' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($s->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('siswa.edit', $s->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('siswa.destroy', $s->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                Belum ada data siswa. 
                                <a href="{{ route('siswa.create') }}">Tambah siswa pertama</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-slot name="footer">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted">
                    Menampilkan {{ $siswa->firstItem() ?? 0 }} - {{ $siswa->lastItem() ?? 0 }} dari {{ $siswa->total() }} data
                </span>
                {{ $siswa->links() }}
            </div>
        </x-slot>
    </x-card>
@endsection
