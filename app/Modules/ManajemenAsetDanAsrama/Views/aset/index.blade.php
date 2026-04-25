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
            <li class="breadcrumb-item active">Master Aset</li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    {{-- Quick Navigation --}}
    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-between">
            <a href="{{ route('manajemenasetdanasrama.pengadaan.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-arrow-left"></i> Kembali ke Pengadaan
            </a>
            <a href="{{ route('manajemenasetdanasrama.kerusakan.index') }}" class="btn btn-outline-danger shadow-sm">
                Pantau Kerusakan <i class="fas fa-arrow-right"></i>
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
            <x-card title="Daftar Master Aset" icon="fas fa-boxes">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th width="120">Kode Aset</th>
                                <th>Nama Aset</th>
                                <th width="140">Harga</th>
                                <th width="140">Status Kondisi</th>
                                <th width="130">Tgl Pengadaan</th>
                                <th width="200">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($aset as $item)
                            <tr>
                                <td>{{ $loop->iteration + ($aset->currentPage() - 1) * $aset->perPage() }}</td>
                                <td><code>{{ $item->kode_aset }}</code></td>
                                <td><strong>{{ $item->nama_aset }}</strong></td>
                                <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                <td>
                                    @if($item->status_kondisi == 'baik')
                                        <span class="badge badge-success">Baik</span>
                                    @elseif($item->status_kondisi == 'rusak')
                                        <span class="badge badge-danger">Rusak</span>
                                    @elseif($item->status_kondisi == 'dalam_perbaikan')
                                        <span class="badge badge-warning">Dalam Perbaikan</span>
                                    @elseif($item->status_kondisi == 'sudah_diperbaiki')
                                        <span class="badge badge-info">Sudah Diperbaiki</span>
                                    @endif
                                </td>
                                <td>{{ $item->tanggal_pengadaan ? \Carbon\Carbon::parse($item->tanggal_pengadaan)->format('d/m/Y') : '-' }}</td>
                                <td>
                                    <div class="row px-2">
                                        <div class="btn-group w-100 mb-1">
                                            <a href="{{ route('manajemenasetdanasrama.aset.show', $item->id) }}" class="btn btn-xs btn-info" title="Detail">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                            <a href="{{ route('manajemenasetdanasrama.aset.edit', $item->id) }}" class="btn btn-xs btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <button type="button" class="btn btn-xs btn-secondary"
                                                    data-toggle="modal"
                                                    data-target="#modalDuplikat"
                                                    data-id="{{ $item->id }}"
                                                    data-nama="{{ $item->nama_aset }}"
                                                    data-kode="{{ $item->kode_aset }}"
                                                    title="Duplikat Aset">
                                                <i class="fas fa-copy"></i> Duplikat
                                            </button>
                                            <button type="button" class="btn btn-xs btn-danger"
                                                    data-toggle="modal"
                                                    data-target="#modalHapus"
                                                    data-id="{{ $item->id }}"
                                                    data-nama="{{ $item->nama_aset }}"
                                                    title="Hapus">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </div>
                                        <div class="btn-group w-100">
                                            <a href="{{ route('manajemenasetdanasrama.kerusakan.create') }}?aset_id={{ $item->id }}" class="btn btn-xs btn-outline-danger" title="Lapor Kerusakan">
                                                <i class="fas fa-exclamation-triangle"></i> Kerusakan
                                            </a>
                                            <a href="{{ route('manajemenasetdanasrama.pemeliharaan.create') }}?aset_id={{ $item->id }}" class="btn btn-xs btn-outline-primary" title="Catat Pemeliharaan">
                                                <i class="fas fa-wrench"></i> Pemeliharaan
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    <i class="fas fa-inbox"></i> Belum ada data aset
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($aset->hasPages())
                <div class="d-flex justify-content-end mt-3">
                    {{ $aset->links() }}
                </div>
                @endif

                <x-slot name="footer">
                    <small class="text-muted">Total data: {{ $aset->total() }}</small>
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
                    <h5 class="modal-title text-white">Hapus Aset</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus aset <strong id="hapus_nama"></strong>?</p>
                    <div class="form-group">
                        <label for="alasan_hapus">Alasan Penghapusan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="alasan_hapus" name="alasan_hapus" rows="3" required></textarea>
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

{{-- MODAL DUPLIKAT --}}
<div class="modal fade" id="modalDuplikat" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formDuplikat" action="" method="POST">
                @csrf
                <div class="modal-header bg-secondary">
                    <h5 class="modal-title text-white">Duplikat Aset</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <p>Menduplikat aset <strong id="duplikat_nama"></strong> (Kode Induk: <span id="duplikat_kode_text"></span>).</p>
                    <div class="form-group">
                        <label for="jumlah_duplikat">Jumlah Duplikat <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="jumlah_duplikat" name="jumlah_duplikat" min="1" max="50" value="1" required>
                        <small class="text-muted">Aset baru akan memiliki nama yang sama dengan urutan kode aset yang bertambah (Contoh: KODE-1, KODE-2).</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-secondary"><i class="fas fa-copy"></i> Duplikat</button>
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
            var url = '{{ route("manajemenasetdanasrama.aset.destroy", ":id") }}'.replace(':id', button.data('id'));
            modal.find('#formHapus').attr('action', url);
        });

        $('#modalDuplikat').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var modal = $(this);
            modal.find('#duplikat_nama').text(button.data('nama'));
            modal.find('#duplikat_kode_text').text(button.data('kode'));
            var url = '{{ route("manajemenasetdanasrama.aset.duplicate", ":id") }}'.replace(':id', button.data('id'));
            modal.find('#formDuplikat').attr('action', url);
        });
    });
</script>
@endpush
