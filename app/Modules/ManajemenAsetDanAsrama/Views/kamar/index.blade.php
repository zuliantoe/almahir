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
            <li class="breadcrumb-item active">Data Kamar</li>
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

    <div class="row">
        <div class="col-md-12">
            <x-card title="Daftar Kamar Asrama" icon="fas fa-door-open">
                <x-slot name="tools">
                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalTambahKamar">
                        <i class="fas fa-plus mr-1"></i> Tambah Kamar
                    </button>
                </x-slot>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th width="150">Nama Kamar</th>
                                <th width="100">Kapasitas</th>
                                <th width="100">Terisi</th>
                                <th width="100">Sisa</th>
                                <th>Deskripsi</th>
                                <th width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kamar as $item)
                            <tr>
                                <td>{{ $loop->iteration + ($kamar->currentPage() - 1) * $kamar->perPage() }}</td>
                                <td><strong>{{ $item->nama_kamar }}</strong></td>
                                <td>{{ $item->kapasitas }} orang</td>
                                <td>{{ $item->terisi }} orang</td>
                                <td>{{ $item->sisa }} orang</td>
                                <td>{!! $item->status_kapasitas_badge !!}</td>
                                <td>{{ Str::limit($item->deskripsi ?? '-', 50) }}</td>
                                <td>
                                    <a href="{{ route('manajemenasetdanasrama.kamar.show', $item->id) }}" class="btn btn-xs btn-info" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-xs btn-warning"
                                            data-toggle="modal"
                                            data-target="#modalEditKamar"
                                            data-id="{{ $item->id }}"
                                            data-nama="{{ $item->nama_kamar }}"
                                            data-kapasitas="{{ $item->kapasitas }}"
                                            data-deskripsi="{{ $item->deskripsi }}"
                                            title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-xs btn-danger"
                                            data-toggle="modal"
                                            data-target="#modalHapusKamar"
                                            data-id="{{ $item->id }}"
                                            data-nama="{{ $item->nama_kamar }}"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    <i class="fas fa-inbox"></i> Belum ada data kamar
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($kamar->hasPages())
                <div class="d-flex justify-content-end mt-3">
                    {{ $kamar->links() }}
                </div>
                @endif

                <x-slot name="footer">
                    <small class="text-muted">Total data: {{ $kamar->total() }}</small>
                </x-slot>
            </x-card>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH KAMAR --}}
<div class="modal fade" id="modalTambahKamar" tabindex="-1" role="dialog" aria-labelledby="modalTambahKamarLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('manajemenasetdanasrama.kamar.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahKamarLabel">Tambah Kamar</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nama_kamar">Nama Kamar <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_kamar') is-invalid @enderror" id="nama_kamar" name="nama_kamar" value="{{ old('nama_kamar') }}" placeholder="Contoh: Kamar A1" required>
                        @error('nama_kamar')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="kapasitas">Kapasitas <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('kapasitas') is-invalid @enderror" id="kapasitas" name="kapasitas" value="{{ old('kapasitas', 4) }}" min="1" required>
                        @error('kapasitas')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="deskripsi">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL EDIT KAMAR --}}
<div class="modal fade" id="modalEditKamar" tabindex="-1" role="dialog" aria-labelledby="modalEditKamarLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formEditKamar" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="modalEditKamarLabel">Edit Kamar</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_nama_kamar">Nama Kamar <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_nama_kamar" name="nama_kamar" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_kapasitas">Kapasitas <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="edit_kapasitas" name="kapasitas" min="1" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_deskripsi">Deskripsi</label>
                        <textarea class="form-control" id="edit_deskripsi" name="deskripsi" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL HAPUS KAMAR --}}
<div class="modal fade" id="modalHapusKamar" tabindex="-1" role="dialog" aria-labelledby="modalHapusKamarLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formHapusKamar" action="" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white" id="modalHapusKamarLabel">Hapus Kamar</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus kamar <strong id="hapus_nama_kamar"></strong>?</p>
                    <p class="text-danger"><small><i class="fas fa-info-circle"></i> Kamar yang masih memiliki penghuni tidak dapat dihapus.</small></p>
                    <div class="form-group">
                        <label for="alasan_hapus_kamar">Alasan Penghapusan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="alasan_hapus_kamar" name="alasan_hapus" rows="3" placeholder="Masukkan alasan penghapusan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Edit modal
        $('#modalEditKamar').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var modal = $(this);
            modal.find('#edit_nama_kamar').val(button.data('nama'));
            modal.find('#edit_kapasitas').val(button.data('kapasitas'));
            modal.find('#edit_deskripsi').val(button.data('deskripsi'));
            var url = '{{ route("manajemenasetdanasrama.kamar.update", ":id") }}';
            url = url.replace(':id', button.data('id'));
            modal.find('#formEditKamar').attr('action', url);
        });

        // Hapus modal
        $('#modalHapusKamar').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var modal = $(this);
            modal.find('#hapus_nama_kamar').text(button.data('nama'));
            modal.find('#alasan_hapus_kamar').val('');
            var url = '{{ route("manajemenasetdanasrama.kamar.destroy", ":id") }}';
            url = url.replace(':id', button.data('id'));
            modal.find('#formHapusKamar').attr('action', url);
        });
    });
</script>
@endpush
