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
            <li class="breadcrumb-item active">Persetujuan Pengajuan</li>
        </ol>
    </div>
</div>
@endsection

@push('css')
@include('manajemenasetdanasrama::partials.styles-dashboard')
@endpush

@section('content')
<div class="container-fluid">
    {{-- Quick Information --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning shadow-sm">
                <div class="inner">
                    <h3>{{ number_format($stats['total_pending'] ?? 0) }}</h3>
                    <p>Menunggu Review</p>
                </div>
                <div class="icon"><i class="fas fa-user-check"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info shadow-sm">
                <div class="inner">
                    <h3 style="font-size: 1.6rem;">Rp {{ number_format($stats['estimasi_biaya'] ?? 0, 0, ',', '.') }}</h3>
                    <p>Estimasi Anggaran</p>
                </div>
                <div class="icon"><i class="fas fa-coins"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success shadow-sm">
                <div class="inner">
                    <h3>{{ number_format($stats['total_approved'] ?? 0) }}</h3>
                    <p>Telah Disetujui</p>
                </div>
                <div class="icon"><i class="fas fa-check-double"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-secondary shadow-sm">
                <div class="inner">
                    <h3>{{ number_format($stats['total_rejected'] ?? 0) }}</h3>
                    <p>Ditolak / Revisi</p>
                </div>
                <div class="icon"><i class="fas fa-ban"></i></div>
            </div>
        </div>
    </div>

    {{-- Quick Navigation --}}
    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-between">
            <a href="{{ route('manajemenasetdanasrama.pengajuan.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-arrow-left"></i> Kembali ke Pengajuan
            </a>
            <a href="{{ route('manajemenasetdanasrama.pengadaan.index') }}" class="btn btn-outline-primary shadow-sm">
                Lanjut ke Pengadaan <i class="fas fa-arrow-right"></i>
            </a>
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
            <x-card title="Daftar Pengajuan Menunggu Persetujuan" icon="fas fa-clock">
                @include('manajemenasetdanasrama::partials.table-pengajuan', [
                    'items' => $pengajuan,
                    'mode' => 'approver',
                    'showStatus' => false,
                    'actionWidth' => '150'
                ])

                {{-- Pagination --}}
                @if($pengajuan->hasPages())
                <div class="d-flex justify-content-end mt-3">
                    {{ $pengajuan->links() }}
                </div>
                @endif

                <x-slot name="footer">
                    <small class="text-muted">Total data: {{ $pengajuan->total() }}</small>
                </x-slot>
            </x-card>
        </div>
    </div>
</div>

{{-- MODAL APPROVE --}}
<div class="modal fade" id="modalApprove" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header bg-white border-0 pt-4 pb-0 justify-content-center">
                <div class="rounded-circle bg-success-soft d-flex align-items-center justify-content-center" style="width: 70px; height: 70px; background: #e8f5e9;">
                    <i class="fas fa-check text-success fa-2x"></i>
                </div>
            </div>
            <div class="modal-body text-center p-4">
                <h5 class="font-weight-bold">Setujui Pengajuan?</h5>
                <p class="text-muted small">Pengajuan <span id="approve_nama" class="text-dark font-weight-bold"></span> akan diteruskan ke proses pengadaan.</p>
                <form id="formApprove" method="POST">
                    @csrf
            </div>
            <div class="modal-footer border-0 bg-light p-3 justify-content-center">
                    <button type="button" class="btn btn-link text-muted font-weight-bold mr-2" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 shadow-sm" style="border-radius: 8px;">Ya, Setujui</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- MODAL REJECT --}}
<div class="modal fade" id="modalReject" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <form id="formReject" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white border-0 py-3">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-times-circle mr-2"></i> Tolak Pengajuan
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-file-excel text-danger fa-lg"></i>
                    </div>
                    <p class="mb-4">Berikan alasan penolakan untuk pengajuan:<br><strong id="reject_nama" class="text-danger"></strong></p>
                    
                    <div class="form-group text-left mb-0">
                        <label class="small font-weight-bold text-muted text-uppercase">Catatan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control shadow-sm border-danger" id="catatan_tolak" name="catatan_tolak" rows="3" placeholder="Masukkan alasan penolakan agar pengaju bisa memperbaiki..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 px-4 justify-content-center">
                    <button type="button" class="btn btn-link text-muted font-weight-bold mr-2" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger px-4 shadow-sm" style="border-radius: 8px;">Ya, Tolak Pengajuan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('manajemenasetdanasrama::partials.modal-detail-pengajuan')
@endsection

@push('scripts')
@include('manajemenasetdanasrama::partials.scripts-asset')
<script>
    $(document).ready(function() {
        // Tombol Lihat - fetch data via AJAX dan tampilkan di modal
        $('.btn-lihat').on('click', function() {
            var id = $(this).data('id');
            var url = '{{ route("manajemenasetdanasrama.pengajuan.show", ":id") }}';
            showDetailPengajuan(id, url);
        });

        // Approve button
        $('.btn-approve').on('click', function() {
            var id = $(this).data('id');
            var nama = $(this).data('nama');
            $('#approve_nama').text(nama);

            var url = '{{ route("manajemenasetdanasrama.persetujuan.approve", ":id") }}';
            url = url.replace(':id', id);
            $('#formApprove').attr('action', url);

            $('#modalApprove').modal('show');
        });

        // Reject button
        $('.btn-reject').on('click', function() {
            var id = $(this).data('id');
            var nama = $(this).data('nama');
            $('#reject_nama').text(nama);

            var url = '{{ route("manajemenasetdanasrama.persetujuan.reject", ":id") }}';
            url = url.replace(':id', id);
            $('#formReject').attr('action', url);

            // Kosongkan textarea setiap kali modal dibuka
            $('#catatan_tolak').val('');

            $('#modalReject').modal('show');
        });
    });
</script>
@endpush