@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <x-alert type="success" :message="session('success')" dismissible />
    @endif
    @if(session('error'))
        <x-alert type="danger" :message="session('error')" dismissible />
    @endif

    <x-card title="Data Wali Murid" icon="fas fa-users">
        <x-slot name="tools">
            <a href="{{ route('walimurid.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Tambah Wali
            </a>
        </x-slot>

        <form action="{{ route('walimurid.index') }}" method="GET" class="mb-3">
            <div class="row">
                <div class="col-md-4">
                    <x-input name="search" placeholder="Cari nama/email/telepon..." :value="request('search')" />
                </div>
                <div class="col-md-2">
                    <x-btn type="submit" variant="info" icon="fas fa-search">Cari</x-btn>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th width="50">No</th>
                        <th>Nama Wali</th>
                        <th>Hubungan</th>
                        <th>Putra/Putri (Siswa)</th>
                        <th>Telepon</th>
                        <th>Pekerjaan</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($waliMurid as $index => $wali)
                    <tr>
                        <td>{{ $waliMurid->firstItem() + $index }}</td>
                        <td>
                            <strong>{{ $wali->nama }}</strong>
                            @if($wali->email)
                            <br><small class="text-muted">{{ $wali->email }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-info">{{ ucfirst($wali->hubungan) }}</span>
                        </td>
                        <td>
                            @forelse($wali->siswa as $s)
                                <span class="badge badge-light border mb-1">
                                    <i class="fas fa-user-graduate text-primary mr-1"></i> {{ $s->nama }}
                                </span><br>
                            @empty
                                <span class="text-danger small"><i class="fas fa-exclamation-triangle"></i> Belum ada anak</span>
                            @endforelse
                        </td>
                        <td>{{ $wali->telepon ?? '-' }}</td>
                        <td>{{ $wali->pekerjaan ?? '-' }}</td>
                        <td>
                            <a href="{{ route('walimurid.edit', $wali->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('walimurid.destroy', $wali->id) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Yakin ingin menghapus?')">
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
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-3x mb-2"></i>
                            <p>Belum ada data wali murid</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <div>Menampilkan {{ $waliMurid->count() }} dari {{ $waliMurid->total() }} data</div>
            {{ $waliMurid->withQueryString()->links() }}
        </div>
    </x-card>
</div>
@endsection
