@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">

    {{-- Alerts handled globally via SweetAlert2 --}}

    <x-card title="Daftar Tipe Pegawai" icon="fas fa-tags">

        <x-slot name="tools">
            <a href="{{ route('pegawaimanager.types.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Tambah Tipe
            </a>
            <a href="{{ route('pegawaimanager.index') }}" class="btn btn-secondary btn-sm ml-2">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Pegawai
            </a>
        </x-slot>

        <div class="table-responsive">
            <table class="table table-hover table-striped">
            <div class="form-group">
                <label>Nama Kategori Pegawai <span class="text-danger">*</span></label>
                <input type="text" name="nama_type" class="form-control" value="{{ old('nama_type') }}" 
                    placeholder="Contoh: Guru Tetap, Staf Administrasi, dll." required>
                <small class="form-text text-muted">Masukkan nama kategori atau klasifikasi posisi pegawai di sekolah.</small>
            </div>
        <thead class="thead-dark">
            <tr>
                <th style="width: 50px;" class="text-center">No</th>
                <th>Kategori / Nama Tipe</th>
                <th class="text-center">Tanggal Dibuat</th>
                <th class="text-center" style="width: 150px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($types as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="font-weight-bold text-dark">{{ $item->nama_type }}</td>
                <td class="text-center text-muted small">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('pegawaimanager.types.edit', $item->id) }}" 
                                   class="btn btn-info" 
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('pegawaimanager.types.destroy', $item->id) }}" 
                                      method="POST" 
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger btn-delete" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            Belum ada data tipe pegawai.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</div>
@endsection
