@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">

    {{-- Alerts handled globally via SweetAlert2 --}}

    <x-card title="Daftar Tipe Pegawai" icon="fas fa-tags">

        <x-slot name="tools">
            <a href="{{ route('pegawaimanager.types.create') }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm btn-animate gradient-primary border-0">
                <i class="fas fa-plus mr-1"></i> Tambah Tipe
            </a>
            <a href="{{ route('pegawaimanager.index') }}" class="btn btn-secondary btn-sm rounded-pill px-3 shadow-sm ml-2 btn-animate">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </x-slot>

        <div class="table-responsive mt-2">
            <table class="table table-hover table-premium">
                <thead>
                    <tr>
                        <th style="width: 50px;" class="text-center">No</th>
                        <th>Kategori / Nama Tipe</th>
                        <th class="text-center">Jumlah Pegawai</th>
                        <th class="text-center">Tanggal Dibuat</th>
                        <th class="text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($types as $index => $item)
                    <tr>
                        <td class="text-center">{{ $types->firstItem() + $index }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center mr-3 shadow-sm"
                                     style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); flex-shrink: 0;">
                                    <i class="fas fa-tag text-white" style="font-size: 0.9rem;"></i>
                                </div>
                                <div>
                                    <div class="font-weight-bold text-dark mb-0">{{ $item->nama_type }}</div>
                                    <small class="text-muted"><i class="fas fa-users mr-1"></i>{{ $item->pegawai_count }} Pegawai terdaftar</small>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-info-soft px-3 py-1 text-primary" style="font-weight: 600; border-radius: 8px; background-color: #e3f2fd;">
                                <i class="fas fa-users mr-1"></i>{{ $item->pegawai_count }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="text-muted small">
                                <i class="fas fa-calendar-alt mr-1"></i>{{ $item->created_at->format('d/m/Y H:i') }}
                            </span>
                        </td>
                        <td class="text-center py-3">
                            <div class="d-flex justify-content-center">
                                <a href="{{ route('pegawaimanager.types.edit', $item->id) }}"
                                   class="btn btn-outline-info btn-sm mx-1 shadow-sm px-2 btn-animate rounded-circle"
                                   style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;"
                                   title="Edit Tipe">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('pegawaimanager.types.destroy', $item->id) }}"
                                      method="POST"
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                            class="btn btn-outline-danger btn-sm mx-1 shadow-sm px-2 btn-delete btn-animate rounded-circle"
                                            style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;"
                                            title="Hapus Tipe">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5 bg-white">
                            <div class="mb-3">
                                <i class="fas fa-tags fa-4x text-gray-300"></i>
                            </div>
                            <h5 class="font-weight-bold mb-1">Belum Ada Tipe Pegawai</h5>
                            <p class="small">Silakan tambahkan tipe pegawai pertama Anda untuk memulai.</p>
                            <a href="{{ route('pegawaimanager.types.create') }}" class="btn btn-primary btn-sm mt-2 rounded-pill px-4">
                                <i class="fas fa-plus mr-1"></i> Tambah Tipe
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Links --}}
        @if($types->hasPages())
            <div class="card-footer bg-light border-top d-flex justify-content-between align-items-center py-3">
                <div class="text-muted small font-italic">
                    Menampilkan <strong>{{ $types->firstItem() }}</strong> sampai <strong>{{ $types->lastItem() }}</strong>
                    dari <strong>{{ $types->total() }}</strong> total tipe.
                </div>
                <div class="pagination-sm">
                    {{ $types->links() }}
                </div>
            </div>
        @endif

    </x-card>
</div>
@endsection
