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
            <li class="breadcrumb-item active">Jadwal Piket</li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <x-alert type="success" :message="session('success')" dismissible />
    @endif
    @if(session('error'))
        <x-alert type="danger" :message="session('error')" dismissible />
    @endif

    <div class="row">
        <div class="col-md-12">
            <x-card title="Daftar Jadwal Piket" icon="fas fa-calendar-alt">
                <x-slot name="tools">
                    <a href="{{ route('manajemenasetdanasrama.jadwal-piket.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus mr-1"></i> Tambah Jadwal
                    </a>
                </x-slot>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th width="120">Bulan</th>
                                <th width="90">Pekan</th>
                                <th width="100">Hari</th>
                                <th>Tempat</th>
                                <th>Siswa</th>
                                <th width="90">Status</th>
                                <th width="130">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                            @endphp
                            @forelse($jadwal as $item)
                            <tr>
                                <td>{{ $loop->iteration + ($jadwal->currentPage() - 1) * $jadwal->perPage() }}</td>
                                <td>{{ $namaBulan[$item->bulan] ?? $item->bulan }}</td>
                                <td>Pekan {{ $item->pekan }}</td>
                                <td>{{ $item->hari }}</td>
                                <td>{{ Str::limit($item->tempat, 30) }}</td>
                                <td><strong>{{ $item->siswa->nama ?? '-' }}</strong></td>
                                <td>
                                    @if($item->status == 'belum')
                                        <span class="badge badge-warning">Belum</span>
                                    @elseif($item->status == 'sudah')
                                        <span class="badge badge-success">Sudah</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->status == 'belum')
                                    <form action="{{ route('manajemenasetdanasrama.jadwal-piket.selesai', $item->id) }}" method="POST" style="display:inline">
                                        @csrf
                                        <button type="submit" class="btn btn-xs btn-success" title="Tandai Selesai">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    @endif
                                    <a href="{{ route('manajemenasetdanasrama.jadwal-piket.edit', $item->id) }}" class="btn btn-xs btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-xs btn-danger"
                                            data-toggle="modal"
                                            data-target="#modalHapus"
                                            data-id="{{ $item->id }}"
                                            data-nama="{{ $item->hari }} - {{ $item->siswa->nama ?? '' }}"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    <i class="fas fa-inbox"></i> Belum ada data jadwal piket
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($jadwal->hasPages())
                <div class="d-flex justify-content-end mt-3">
                    {{ $jadwal->links() }}
                </div>
                @endif

                <x-slot name="footer">
                    <small class="text-muted">Total data: {{ $jadwal->total() }}</small>
                </x-slot>
            </x-card>
        </div>
    </div>
</div>

{{-- MODAL HAPUS --}}
<div class="modal fade" id="modalHapus" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formHapus" action="" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white">Hapus Jadwal Piket</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus jadwal piket <strong id="hapus_nama"></strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#modalHapus').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var modal = $(this);
            modal.find('#hapus_nama').text(button.data('nama'));
            var url = '{{ route("manajemenasetdanasrama.jadwal-piket.destroy", ":id") }}'.replace(':id', button.data('id'));
            modal.find('#formHapus').attr('action', url);
        });
    });
</script>
@endpush
