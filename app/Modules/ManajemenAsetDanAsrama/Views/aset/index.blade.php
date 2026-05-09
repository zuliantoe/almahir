@extends('layouts.app')

@section('title', $title)

@push('css')
@include('manajemenasetdanasrama::partials.styles-dashboard')
@endpush

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
    {{-- Quick Information --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info shadow-sm">
                <div class="inner">
                    <h3>{{ number_format($stats['total'] ?? 0) }}</h3>
                    <p>Total Master Aset</p>
                </div>
                <div class="icon"><i class="fas fa-boxes"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success shadow-sm">
                <div class="inner">
                    <h3>{{ number_format($stats['baik'] ?? 0) }}</h3>
                    <p>Kondisi Baik</p>
                </div>
                <div class="icon"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger shadow-sm">
                <div class="inner">
                    <h3>{{ number_format($stats['rusak'] ?? 0) }}</h3>
                    <p>Kondisi Rusak</p>
                </div>
                <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning shadow-sm">
                <div class="inner">
                    <h3>{{ number_format($stats['dalam_perbaikan'] ?? 0) }}</h3>
                    <p>Dalam Perbaikan</p>
                </div>
                <div class="icon"><i class="fas fa-wrench"></i></div>
            </div>
        </div>
    </div>

    {{-- Quick Navigation & Actions --}}
    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-between align-items-center">
            <div>
                <a href="{{ route('manajemenasetdanasrama.pengadaan.index') }}" class="btn btn-outline-secondary shadow-sm mr-2">
                    <i class="fas fa-arrow-left"></i> Kembali ke Pengadaan
                </a>
            </div>
            <div class="d-flex">
                <a href="{{ route('manajemenasetdanasrama.aset.scan') }}" class="btn btn-dark shadow-sm mr-2">
                    <i class="fas fa-qrcode mr-1"></i> Scan QR
                </a>
                <button type="button" class="btn btn-danger shadow-sm mr-2" data-toggle="modal" data-target="#modalBulkDelete">
                    <i class="fas fa-trash-alt mr-1"></i> Hapus Massal
                </button>
                <a href="{{ route('manajemenasetdanasrama.aset.create') }}" class="btn btn-primary shadow-sm">
                    <i class="fas fa-plus-circle mr-1"></i> Tambah Aset Langsung
                </a>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <x-alert type="success" :message="session('success')" dismissible />
    @endif
    @if(session('error'))
        <x-alert type="danger" :message="session('error')" dismissible />
    @endif

    <div class="row">
        <div class="col-md-12">
            <x-card title="Daftar Master Aset" icon="fas fa-boxes">
                @include('manajemenasetdanasrama::partials.table-aset', [
                    'items' => $aset,
                    'showExtendedActions' => true,
                    'actionWidth' => '160'
                ])

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

{{-- MODAL HAPUS MASSAL --}}
<div class="modal fade" id="modalBulkDelete" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <form action="{{ route('manajemenasetdanasrama.aset.bulk-destroy') }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white border-0 py-3">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-trash-alt mr-2"></i> Hapus Aset Massal</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-exclamation-triangle text-danger fa-lg"></i>
                        </div>
                        <h6 class="font-weight-bold">Konfirmasi Penghapusan Massal</h6>
                        <p class="text-muted small">Anda dapat menghapus aset berdasarkan kode lengkap atau inisial depan kode.</p>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="small font-weight-bold text-muted text-uppercase">Pola Kode Aset (Inisial)</label>
                        <input type="text" class="form-control shadow-sm" name="pattern" placeholder="Contoh: MEB atau MJ" required style="text-transform: uppercase;">
                        <small class="text-danger mt-1 d-block" style="font-size: 0.75rem;">
                            <i class="fas fa-info-circle mr-1"></i> <b>MEB</b> akan menghapus SEMUA aset yang kodenya berawalan MEB.
                        </small>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 px-4 justify-content-between">
                    <button type="button" class="btn btn-link text-muted font-weight-bold" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger px-4 shadow-sm font-weight-bold" style="border-radius: 8px;">
                        Hapus Sekarang <i class="fas fa-trash-alt ml-1"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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
                    <p class="text-muted small mb-4">Gunakan fitur ini untuk menambah aset yang sama (spesifikasi identik) dalam jumlah banyak.</p>
                    
                    <div class="form-group text-left">
                        <label class="small font-weight-bold text-muted text-uppercase">Jumlah Duplikasi (Unit)</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white"><i class="fas fa-layer-group"></i></span>
                            </div>
                            <input type="number" class="form-control" name="jumlah" value="1" min="1" max="50" required>
                        </div>
                        <small class="form-text text-muted mt-1">Maksimal 50 unit per sekali proses.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 px-4 justify-content-between">
                    <button type="button" class="btn btn-link text-muted font-weight-bold" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-secondary px-4 shadow-sm font-weight-bold" style="border-radius: 8px;">
                        Mulai Duplikasi <i class="fas fa-arrow-right ml-1"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Modal Duplikat
        $('.btn-duplicate').on('click', function() {
            var id = $(this).data('id');
            var nama = $(this).data('nama');
            
            $('#duplikat_nama').text(nama);
            
            var url = '{{ route("manajemenasetdanasrama.aset.duplicate", ":id") }}';
            url = url.replace(':id', id);
            $('#formDuplikat').attr('action', url);
        });

        // Modal Hapus
        $('#modalHapus').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var url = button.data('url');
            var nama = button.data('nama');
            
            $(this).find('#formDelete').attr('action', url);
            $(this).find('#delete_nama').text(nama);
        });
    });
</script>
@endpush
