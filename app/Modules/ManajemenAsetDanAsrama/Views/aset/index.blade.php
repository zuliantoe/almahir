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
                                <td>{{ $item->harga_formatted }}</td>
                                <td>{!! $item->status_badge !!}</td>
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
                                                    data-url="{{ route('manajemenasetdanasrama.aset.destroy', $item->id) }}"
                                                    title="Hapus">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                            <a href="{{ route('manajemenasetdanasrama.aset.print-label') }}?id={{ $item->id }}" target="_blank" class="btn btn-xs btn-default" title="Cetak Label">
                                                <i class="fas fa-print"></i> Label
                                            </a>
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

@include('manajemenasetdanasrama::partials.modal-delete', ['id' => 'modalHapus', 'title' => 'Hapus Master Aset'])

{{-- MODAL DUPLIKAT --}}
<div class="modal fade" id="modalDuplikat" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <form id="formDuplikat" method="POST">
                @csrf
                <div class="modal-header bg-secondary text-white border-0 py-3">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-copy mr-2"></i> Duplikat Master Aset</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-boxes text-secondary fa-lg"></i>
                    </div>
                    <h6 class="font-weight-bold mb-1" id="duplikat_nama"></h6>
                    <p class="text-muted small">Kode Induk: <span id="duplikat_kode_text" class="badge badge-light border"></span></p>
                    
                    <div class="form-group text-left mt-4">
                        <label class="small font-weight-bold text-muted text-uppercase">Jumlah Salinan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-right-0"><i class="fas fa-layer-group"></i></span>
                            </div>
                            <input type="number" class="form-control border-left-0" id="jumlah_duplikat" name="jumlah_duplikat" min="1" max="50" value="1" required>
                        </div>
                        <small class="text-muted d-block mt-2">Sistem akan meng-generate aset baru dengan urutan kode otomatis.</small>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light p-3 justify-content-center">
                    <button type="button" class="btn btn-link text-muted font-weight-bold mr-2" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-secondary px-4 shadow-sm" style="border-radius: 8px;"><i class="fas fa-copy mr-1"></i> Proses Duplikat</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {

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
