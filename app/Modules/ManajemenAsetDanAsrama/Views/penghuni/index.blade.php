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
            <li class="breadcrumb-item active">Penghuni Kamar</li>
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
            <x-card title="Daftar Penghuni Kamar" icon="fas fa-users">
                <x-slot name="tools">
                    <a href="{{ route('manajemenasetdanasrama.penghuni.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus mr-1"></i> Tambah Penghuni
                    </a>
                </x-slot>

                <div class="card-body border-bottom">
                    <form action="{{ route('manajemenasetdanasrama.penghuni.index') }}" method="GET" class="row">
                        <div class="col-md-4 mb-2">
                            <select name="kamar_id" class="form-control form-control-sm">
                                <option value="">-- Semua Kamar --</option>
                                @foreach($kamar as $k)
                                    <option value="{{ $k->id }}" {{ request('kamar_id') == $k->id ? 'selected' : '' }}>
                                        {{ $k->nama_kamar }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <button type="submit" class="btn btn-sm btn-primary btn-block"><i class="fas fa-search"></i> Filter</button>
                        </div>
                        @if(request()->has('kamar_id'))
                        <div class="col-md-2 mb-2">
                            <a href="{{ route('manajemenasetdanasrama.penghuni.index') }}" class="btn btn-sm btn-secondary btn-block">Reset</a>
                        </div>
                        @endif
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>Nama Siswa</th>
                                <th width="130">Kamar</th>
                                <th width="130">Tgl Masuk</th>
                                <th width="130">Tgl Keluar</th>
                                <th>Keterangan</th>
                                <th width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($penghuni as $item)
                            <tr>
                                <td>{{ $loop->iteration + ($penghuni->currentPage() - 1) * $penghuni->perPage() }}</td>
                                <td><strong>{{ $item->siswa->nama ?? '-' }}</strong></td>
                                <td><span class="badge badge-primary">{{ $item->kamar->nama_kamar ?? '-' }}</span></td>
                                <td>{{ $item->tanggal_masuk ? \Carbon\Carbon::parse($item->tanggal_masuk)->format('d/m/Y') : '-' }}</td>
                                <td>{{ $item->tanggal_keluar ? \Carbon\Carbon::parse($item->tanggal_keluar)->format('d/m/Y') : '-' }}</td>
                                <td>{{ Str::limit($item->keterangan ?? '-', 40) }}</td>
                                <td>
                                    <a href="{{ route('manajemenasetdanasrama.penghuni.edit', $item->id) }}" class="btn btn-xs btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-xs btn-danger"
                                            data-toggle="modal"
                                            data-target="#modalHapus"
                                            data-id="{{ $item->id }}"
                                            data-nama="{{ $item->siswa->nama ?? '' }}"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    <i class="fas fa-inbox"></i> Belum ada data penghuni
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($penghuni->hasPages())
                <div class="d-flex justify-content-end mt-3">
                    {{ $penghuni->links() }}
                </div>
                @endif

                <x-slot name="footer">
                    <small class="text-muted">Total data: {{ $penghuni->total() }}</small>
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
                    <h5 class="modal-title text-white">Hapus Penghuni</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus penghuni <strong id="hapus_nama"></strong> dari kamar?</p>
                    <div class="form-group">
                        <label for="alasan_hapus">Alasan Penghapusan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="alasan_hapus" name="alasan_hapus" rows="3" placeholder="Masukkan alasan penghapusan..." required></textarea>
                    </div>
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
            modal.find('#alasan_hapus').val('');
            var url = '{{ route("manajemenasetdanasrama.penghuni.destroy", ":id") }}'.replace(':id', button.data('id'));
            modal.find('#formHapus').attr('action', url);
        });
    });
</script>
@endpush

