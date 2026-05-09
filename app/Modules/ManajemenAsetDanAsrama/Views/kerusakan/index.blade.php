@extends('layouts.app')

@section('title', $title)

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0">{{ $title }}</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('manajemenasetdanasrama.index') }}">Manajemen Aset & Asrama</a></li>
            <li class="breadcrumb-item active">Kerusakan Aset</li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    {{-- Quick Navigation --}}
    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-between">
            <a href="{{ route('manajemenasetdanasrama.aset.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-arrow-left"></i> Kembali ke Master Aset
            </a>
            <a href="{{ route('manajemenasetdanasrama.pemeliharaan.index') }}" class="btn btn-outline-primary shadow-sm">
                Lanjut ke Pemeliharaan <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" dismissible />
    @endif
    @if(session('error'))
        <x-alert type="danger" :message="session('error')" dismissible />
    @endif

    <div class="row">
        <div class="col-md-12">
            <x-card title="Daftar Kerusakan Aset" icon="fas fa-exclamation-triangle">
                <x-slot name="tools">
                    <a href="{{ route('manajemenasetdanasrama.kerusakan.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus mr-1"></i> Lapor Kerusakan
                    </a>
                </x-slot>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>Nama Aset</th>
                                <th width="130">Tanggal</th>
                                <th width="100">Tingkat</th>
                                <th width="140">Status</th>
                                <th>Deskripsi</th>
                                <th width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kerusakan as $item)
                            <tr>
                                <td>{{ $loop->iteration + ($kerusakan->currentPage() - 1) * $kerusakan->perPage() }}</td>
                                <td>
                                    <strong>{{ $item->aset->nama_aset ?? '-' }}</strong>
                                    <br><small class="text-muted">{{ $item->aset->kode_aset ?? '' }}</small>
                                </td>
                                <td>{{ $item->tanggal_kerusakan ? \Carbon\Carbon::parse($item->tanggal_kerusakan)->format('d/m/Y') : '-' }}</td>
                                <td>
                                    @if($item->tingkat_kerusakan == 'ringan')
                                        <span class="badge badge-info">Ringan</span>
                                    @elseif($item->tingkat_kerusakan == 'sedang')
                                        <span class="badge badge-warning">Sedang</span>
                                    @elseif($item->tingkat_kerusakan == 'berat')
                                        <span class="badge badge-danger">Berat</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->status_penanganan == 'belum_ditangani')
                                        <span class="badge badge-danger">Belum Ditangani</span>
                                    @elseif($item->status_penanganan == 'sedang_ditangani')
                                        <span class="badge badge-warning">Sedang Ditangani</span>
                                    @elseif($item->status_penanganan == 'selesai')
                                        <span class="badge badge-success">Selesai</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit($item->deskripsi_kerusakan ?? '-', 40) }}</td>
                                <td>
                                    <form action="{{ route('manajemenasetdanasrama.kerusakan.proses-pemeliharaan', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-xs btn-success" title="Proses Pemeliharaan">
                                            <i class="fas fa-wrench"></i> Proses
                                        </button>
                                    </form>
                                    <a href="{{ route('manajemenasetdanasrama.kerusakan.edit', $item->id) }}" class="btn btn-xs btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-xs btn-danger"
                                            data-toggle="modal"
                                            data-target="#modalHapus"
                                            data-id="{{ $item->id }}"
                                            data-nama="{{ $item->aset->nama_aset ?? '' }}"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    <i class="fas fa-inbox"></i> Belum ada data kerusakan
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($kerusakan->hasPages())
                <div class="d-flex justify-content-end mt-3">
                    {{ $kerusakan->links() }}
                </div>
                @endif

                <x-slot name="footer">
                    <small class="text-muted">Total data: {{ $kerusakan->total() }}</small>
                </x-slot>
            </x-card>
        </div>
    </div>
</div>

@include('manajemenasetdanasrama::partials.modal-delete', ['id' => 'modalHapus', 'title' => 'Hapus Laporan Kerusakan'])
@endsection
