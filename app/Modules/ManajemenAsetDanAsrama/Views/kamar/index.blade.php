@extends('layouts.app')

@section('title', $title)

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0" style="font-weight: 700;">{{ $title }}</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('manajemenasetdanasrama.index') }}">Asrama</a></li>
            <li class="breadcrumb-item active">Kamar</li>
        </ol>
    </div>
</div>

<style>
    .table-compact td {
        padding: 0.6rem 0.75rem !important;
        font-size: 0.9rem;
    }
    .table-compact thead th {
        padding: 0.75rem !important;
        background-color: #f4f6f9;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }
    .btn-xs-custom {
        padding: 1px 6px !important;
        font-size: 0.7rem !important;
        border-radius: 4px !important;
        margin: 0 2px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 24px;
        width: 24px;
    }
</style>
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
                    <table class="table table-hover table-compact mb-0">
                        <thead>
                            <tr>
                                <th width="50" class="text-center">No</th>
                                <th width="150">Nama Kamar</th>
                                <th width="120">Kapasitas</th>
                                <th width="150">Status Hunian</th>
                                <th>Deskripsi</th>
                                <th width="150" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kamar as $item)
                            <tr>
                                <td class="text-center text-muted">{{ $loop->iteration + ($kamar->currentPage() - 1) * $kamar->perPage() }}</td>
                                <td>
                                    <strong>{{ $item->nama_kamar }}</strong>
                                    <div class="mt-1">
                                        @forelse($item->penghuni->take(3) as $p)
                                            <span class="badge badge-light border text-dark mb-1" style="font-weight: 400; font-size: 0.7rem;">
                                                <i class="fas fa-user-circle mr-1 text-primary"></i> {{ Str::words($p->siswa->nama ?? 'N/A', 1, '') }}
                                            </span>
                                        @empty
                                            <small class="text-muted italic">Kosong</small>
                                        @endforelse
                                        @if($item->penghuni->count() > 3)
                                            <span class="badge badge-light border text-muted mb-1" style="font-size: 0.7rem;">+{{ $item->penghuni->count() - 3 }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="progress progress-xs" style="height: 6px;">
                                        @php $percent = $item->kapasitas > 0 ? ($item->terisi / $item->kapasitas) * 100 : 0; @endphp
                                        <div class="progress-bar {{ $percent >= 100 ? 'bg-danger' : ($percent >= 75 ? 'bg-warning' : 'bg-success') }}" 
                                             role="progressbar" style="width: {{ $percent }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ $item->terisi }} / {{ $item->kapasitas }} Orang</small>
                                </td>
                                <td>{!! $item->status_kapasitas_badge !!}</td>
                                <td><small class="text-muted">{{ Str::limit($item->deskripsi ?? '-', 60) }}</small></td>
                                <td class="text-center">
                                    @if($item->sisa > 0)
                                    <a href="{{ route('manajemenasetdanasrama.penghuni.assign-multiple', $item->id) }}" 
                                       class="btn btn-xs-custom btn-success" title="Tambah Penghuni">
                                        <i class="fas fa-user-plus"></i>
                                    </a>
                                    @endif
                                    <a href="{{ route('manajemenasetdanasrama.kamar.show', $item->id) }}" class="btn btn-xs-custom btn-info" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-xs-custom btn-warning"
                                            data-toggle="modal"
                                            data-target="#modalEditKamar"
                                            data-id="{{ $item->id }}"
                                            data-nama="{{ $item->nama_kamar }}"
                                            data-kapasitas="{{ $item->kapasitas }}"
                                            data-deskripsi="{{ $item->deskripsi }}"
                                            title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-xs-custom btn-danger"
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
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="fas fa-inbox fa-2x mb-2"></i><br>Belum ada data kamar
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
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h5 class="modal-title font-weight-bold" id="modalTambahKamarLabel">
                    <i class="fas fa-plus-circle mr-2"></i> Tambah Kamar Baru
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('manajemenasetdanasrama.kamar.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="form-group mb-4">
                        <label class="small font-weight-bold text-uppercase text-muted mb-1">Nama Kamar <span class="text-danger">*</span></label>
                        <div class="input-group shadow-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-right-0"><i class="fas fa-door-open text-primary"></i></span>
                            </div>
                            <input type="text" name="nama_kamar" class="form-control border-left-0" placeholder="Contoh: Abu Bakar 01" required>
                        </div>
                    </div>
                    <div class="form-group mb-4">
                        <label class="small font-weight-bold text-uppercase text-muted mb-1">Kapasitas (Orang) <span class="text-danger">*</span></label>
                        <div class="input-group shadow-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-right-0"><i class="fas fa-users text-primary"></i></span>
                            </div>
                            <input type="number" name="kapasitas" class="form-control border-left-0" value="4" min="1" required>
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-uppercase text-muted mb-1">Deskripsi / Catatan</label>
                        <textarea name="deskripsi" class="form-control" rows="3" placeholder="Opsional..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 px-4">
                    <button type="button" class="btn btn-link text-muted font-weight-bold" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm" style="border-radius: 8px;">
                        Selanjutnya <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL EDIT KAMAR --}}
<div class="modal fade" id="modalEditKamar" tabindex="-1" role="dialog" aria-labelledby="modalEditKamarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header bg-warning text-white border-0 py-3">
                <h5 class="modal-title font-weight-bold" id="modalEditKamarLabel">
                    <i class="fas fa-edit mr-2"></i> Edit Data Kamar
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEditKamar" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="form-group mb-4">
                        <label class="small font-weight-bold text-uppercase text-muted mb-1">Nama Kamar <span class="text-danger">*</span></label>
                        <div class="input-group shadow-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-right-0"><i class="fas fa-door-open text-warning"></i></span>
                            </div>
                            <input type="text" name="nama_kamar" id="edit_nama_kamar" class="form-control border-left-0" required>
                        </div>
                    </div>
                    <div class="form-group mb-4">
                        <label class="small font-weight-bold text-uppercase text-muted mb-1">Kapasitas (Orang) <span class="text-danger">*</span></label>
                        <div class="input-group shadow-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-right-0"><i class="fas fa-users text-warning"></i></span>
                            </div>
                            <input type="number" name="kapasitas" id="edit_kapasitas" class="form-control border-left-0" min="1" required>
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-uppercase text-muted mb-1">Deskripsi / Catatan</label>
                        <textarea name="deskripsi" id="edit_deskripsi" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 px-4">
                    <button type="button" class="btn btn-link text-muted font-weight-bold" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning px-4 shadow-sm text-white" style="border-radius: 8px;">
                        <i class="fas fa-save mr-2"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL HAPUS KAMAR --}}
<div class="modal fade" id="modalHapusKamar" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header bg-white border-0 pt-4 pb-0 justify-content-center">
                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                    <i class="fas fa-door-closed text-danger fa-2x"></i>
                </div>
            </div>
            <div class="modal-body text-center p-4">
                <h5 class="font-weight-bold">Hapus Kamar?</h5>
                <p class="text-muted small">Kamar <span id="hapus_nama_kamar" class="text-dark font-weight-bold"></span> akan dihapus permanen.</p>
                <form id="formHapusKamar" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="form-group text-left">
                        <label class="small font-weight-bold text-muted text-uppercase">Alasan Penghapusan <span class="text-danger">*</span></label>
                        <textarea class="form-control bg-light border-0" id="alasan_hapus_kamar" name="alasan_hapus" rows="2" placeholder="Masukkan alasan..." required></textarea>
                    </div>
                    <p class="text-danger mb-0" style="font-size: 0.7rem;"><i class="fas fa-info-circle mr-1"></i> Kamar dengan penghuni aktif tidak bisa dihapus.</p>
            </div>
            <div class="modal-footer border-0 bg-light p-3 justify-content-center">
                    <button type="button" class="btn btn-link text-muted font-weight-bold mr-2" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger px-4 shadow-sm" style="border-radius: 8px;">Ya, Hapus</button>
                </form>
            </div>
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
