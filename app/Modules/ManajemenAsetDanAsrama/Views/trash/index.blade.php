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
            <li class="breadcrumb-item active">Trash</li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    {{-- Alert Messages --}}
    @if(session('success'))
        <x-alert type="success" :message="session('success')" dismissible />
    @endif
    @if(session('error'))
        <x-alert type="danger" :message="session('error')" dismissible />
    @endif

    {{-- Aset Terhapus --}}
    <div class="row">
        <div class="col-md-12">
            <x-card title="Aset Terhapus" icon="fas fa-boxes">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th width="120">Kode Aset</th>
                                <th>Nama Aset</th>
                                <th>Alasan Hapus</th>
                                <th width="150">Dihapus Oleh</th>
                                <th width="150">Tanggal Hapus</th>
                                <th width="150">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($asetTrash as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><code>{{ $item->kode_aset }}</code></td>
                                <td>{{ $item->nama_aset }}</td>
                                <td>{{ Str::limit($item->alasan_hapus ?? '-', 50) }}</td>
                                <td>{{ $item->deletedBy->name ?? '-' }}</td>
                                <td>{{ $item->deleted_at ? $item->deleted_at->format('d/m/Y H:i') : '-' }}</td>
                                <td>
                                    <form action="{{ route('manajemenasetdanasrama.trash.restore', ['type' => 'aset', 'id' => $item->id]) }}" method="POST" style="display:inline">
                                        @csrf
                                        <button type="submit" class="btn btn-xs btn-success" title="Pulihkan">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-xs btn-danger"
                                            data-toggle="modal"
                                            data-target="#modalForceDelete"
                                            data-type="aset"
                                            data-id="{{ $item->id }}"
                                            data-nama="{{ $item->nama_aset }}"
                                            title="Hapus Permanen">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    <i class="fas fa-inbox"></i> Tidak ada aset yang terhapus
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>

    {{-- Pengajuan Terhapus --}}
    <div class="row">
        <div class="col-md-12">
            <x-card title="Pengajuan Terhapus" icon="fas fa-file-alt">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th width="140">Nomor Pengajuan</th>
                                <th>Nama Aset</th>
                                <th>Alasan Hapus</th>
                                <th width="150">Dihapus Oleh</th>
                                <th width="150">Tanggal Hapus</th>
                                <th width="150">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengajuanTrash as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->nomor_pengajuan ?? '-' }}</td>
                                <td>{{ $item->nama_aset }}</td>
                                <td>{{ Str::limit($item->alasan_hapus ?? '-', 50) }}</td>
                                <td>{{ $item->deletedBy->name ?? '-' }}</td>
                                <td>{{ $item->deleted_at ? $item->deleted_at->format('d/m/Y H:i') : '-' }}</td>
                                <td>
                                    <form action="{{ route('manajemenasetdanasrama.trash.restore', ['type' => 'pengajuan', 'id' => $item->id]) }}" method="POST" style="display:inline">
                                        @csrf
                                        <button type="submit" class="btn btn-xs btn-success" title="Pulihkan">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-xs btn-danger"
                                            data-toggle="modal"
                                            data-target="#modalForceDelete"
                                            data-type="pengajuan"
                                            data-id="{{ $item->id }}"
                                            data-nama="{{ $item->nama_aset }}"
                                            title="Hapus Permanen">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    <i class="fas fa-inbox"></i> Tidak ada pengajuan yang terhapus
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>
</div>

{{-- MODAL FORCE DELETE --}}
<div class="modal fade" id="modalForceDelete" tabindex="-1" role="dialog" aria-labelledby="modalForceDeleteLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formForceDelete" action="" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white" id="modalForceDeleteLabel">Hapus Permanen</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                    </div>
                    <p class="text-center">Apakah Anda yakin ingin <strong>MENGHAPUS PERMANEN</strong> data <strong id="force_delete_nama"></strong>?</p>
                    <p class="text-center text-danger"><small><i class="fas fa-info-circle"></i> Data yang dihapus permanen tidak dapat dikembalikan!</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Hapus Permanen</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#modalForceDelete').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var type = button.data('type');
            var id = button.data('id');
            var nama = button.data('nama');

            var modal = $(this);
            modal.find('#force_delete_nama').text(nama);

            var url = '{{ route("manajemenasetdanasrama.trash.force-delete", ["type" => ":type", "id" => ":id"]) }}';
            url = url.replace(':type', type).replace(':id', id);
            modal.find('#formForceDelete').attr('action', url);
        });
    });
</script>
@endpush
