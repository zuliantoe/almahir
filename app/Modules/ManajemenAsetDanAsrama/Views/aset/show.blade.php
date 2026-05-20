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
            <li class="breadcrumb-item"><a href="{{ route('manajemenasetdanasrama.aset.index') }}">Master Aset</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<style>
    .btn-action-xs {
        padding: 0.1rem 0.5rem !important;
        font-size: 0.75rem !important;
        line-height: 1.5 !important;
        border-radius: 4px !important;
    }
</style>

<div class="row">
    {{-- Info Utama Aset --}}
    <div class="col-md-6">
        <x-card title="Informasi Aset" icon="fas fa-boxes">
            <table class="table table-sm table-borderless">
                <tr>
                    <th width="40%">Kode Aset</th>
                    <td><code>{{ $aset->kode_aset }}</code></td>
                </tr>
                <tr>
                    <th>Nama Aset</th>
                    <td><strong>{{ $aset->nama_aset }}</strong></td>
                </tr>
                <tr>
                    <th>Harga</th>
                    <td class="text-success font-weight-bold">{{ $aset->harga_formatted }}</td>
                </tr>
                <tr>
                    <th>Status Kondisi</th>
                    <td>{!! $aset->status_badge !!}</td>
                </tr>
                <tr>
                    <th>Kondisi Fisik</th>
                    <td>{{ $aset->kondisi ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Deskripsi</th>
                    <td><small class="text-muted">{{ $aset->deskripsi_aset ?? '-' }}</small></td>
                </tr>
                <tr>
                    <th>Tanggal Pengadaan</th>
                    <td>{{ $aset->tanggal_pengadaan ? $aset->tanggal_pengadaan->format('d/m/Y') : '-' }}</td>
                </tr>
            </table>
            <x-slot name="footer">
                <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
                    <div>
                        @php
                            $backUrl = url()->previous() !== url()->current() ? url()->previous() : route('manajemenasetdanasrama.aset.index');
                        @endphp
                        <a href="{{ $backUrl }}" class="btn btn-sm btn-secondary shadow-sm">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                    </div>
                    <div class="d-flex flex-wrap" style="gap: 5px;">
                        <a href="{{ route('manajemenasetdanasrama.kerusakan.create') }}?aset_id={{ $aset->id }}" class="btn btn-sm btn-outline-danger shadow-sm">
                            <i class="fas fa-exclamation-triangle mr-1"></i> Lapor Rusak
                        </a>
                        <a href="{{ route('manajemenasetdanasrama.pemeliharaan.create') }}?aset_id={{ $aset->id }}" class="btn btn-sm btn-outline-info shadow-sm">
                            <i class="fas fa-wrench mr-1"></i> Pemeliharaan
                        </a>
                        <a href="{{ route('manajemenasetdanasrama.aset.print-label') }}?id={{ $aset->id }}" target="_blank" class="btn btn-sm btn-primary shadow-sm">
                            <i class="fas fa-print mr-1"></i> Cetak Label
                        </a>
                        <a href="{{ route('manajemenasetdanasrama.aset.edit', $aset->id) }}" class="btn btn-sm btn-warning text-white shadow-sm">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </a>
                        <button type="button" class="btn btn-sm btn-danger shadow-sm" data-toggle="modal" data-target="#modalHapus" data-id="{{ $aset->id }}" data-nama="{{ $aset->nama_aset }}" data-url="{{ route('manajemenasetdanasrama.aset.destroy', $aset->id) }}">
                            <i class="fas fa-trash mr-1"></i> Hapus
                        </button>
                    </div>
                </div>
            </x-slot>
        </x-card>
    </div>

    {{-- Info Pengadaan --}}
    <div class="col-md-6">
        <x-card title="Data Pengadaan" icon="fas fa-truck">
            @if($aset->pengadaan)
            <table class="table table-sm table-borderless">
                <tr>
                    <th width="40%">Nomor PO</th>
                    <td><code>{{ $aset->pengadaan->nomor_po }}</code></td>
                </tr>
                <tr>
                    <th>Vendor</th>
                    <td>{{ $aset->pengadaan->vendor }}</td>
                </tr>
                <tr>
                    <th>Biaya Riil</th>
                    <td>Rp {{ number_format($aset->pengadaan->biaya_riil, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Tanggal Pesan</th>
                    <td>{{ $aset->pengadaan->tanggal_pesan ? $aset->pengadaan->tanggal_pesan->format('d/m/Y') : '-' }}</td>
                </tr>
                <tr>
                    <th>Tanggal Datang</th>
                    <td>{{ $aset->pengadaan->tanggal_datang ? $aset->pengadaan->tanggal_datang->format('d/m/Y') : '-' }}</td>
                </tr>
                <tr>
                    <th>Status PO</th>
                    <td>
                        @if($aset->pengadaan->status == 'dipesan')
                            <span class="badge badge-warning">Dipesan</span>
                        @elseif($aset->pengadaan->status == 'datang')
                            <span class="badge badge-success">Datang</span>
                        @elseif($aset->pengadaan->status == 'batal')
                            <span class="badge badge-danger">Batal</span>
                        @endif
                    </td>
                </tr>
                @if($aset->pengadaan->catatan_pengadaan)
                <tr>
                    <th>Catatan Pengadaan</th>
                    <td><small>{{ $aset->pengadaan->catatan_pengadaan }}</small></td>
                </tr>
                @endif
                @if($aset->pengadaan->pengajuan)
                <tr>
                    <td colspan="2" class="bg-light font-weight-bold">Informasi Pengajuan Awal</td>
                </tr>
                @if($aset->pengadaan->pengajuan->deskripsi_pengajuan)
                <tr>
                    <th>Deskripsi Pengajuan</th>
                    <td><small>{{ $aset->pengadaan->pengajuan->deskripsi_pengajuan }}</small></td>
                </tr>
                @endif
                @if($aset->pengadaan->pengajuan->catatan_tolak)
                <tr>
                    <th>Catatan Penolakan</th>
                    <td><small class="text-danger">{{ $aset->pengadaan->pengajuan->catatan_tolak }}</small></td>
                </tr>
                @endif
                @if($aset->pengadaan->pengajuan->alasan_pengajuan_ulang)
                <tr>
                    <th>Alasan Ajukan Ulang</th>
                    <td><small class="text-warning">{{ $aset->pengadaan->pengajuan->alasan_pengajuan_ulang }}</small></td>
                </tr>
                @endif
                @endif
            </table>
            @else
            <div class="text-center py-4">
                <i class="fas fa-info-circle fa-2x text-muted mb-2"></i>
                <p class="text-muted small">Aset ini ditambahkan secara manual tanpa melalui proses pengadaan sistem.</p>
            </div>
            @endif
        </x-card>
    </div>
</div>

{{-- Riwayat Kerusakan & Pemeliharaan --}}
<div class="row mt-3">
    <div class="col-md-12 mb-4">
        <x-card title="Riwayat Kerusakan" icon="fas fa-exclamation-triangle" class="card-outline card-danger">
            @include('manajemenasetdanasrama::partials.table-kerusakan', [
                'items' => $aset->kerusakan,
                'showAset' => false,
                'actionWidth' => '80',
                'hideDelete' => true,
                'fullText' => true
            ])
        </x-card>
    </div>

    <div class="col-md-12">
        <x-card title="Riwayat Pemeliharaan" icon="fas fa-wrench" class="card-outline card-primary">
            @include('manajemenasetdanasrama::partials.table-pemeliharaan', [
                'items' => $aset->pemeliharaan,
                'showAset' => false,
                'actionWidth' => '80',
                'hideDelete' => true,
                'fullText' => true
            ])
        </x-card>
    </div>
</div>

@include('manajemenasetdanasrama::partials.modal-delete', ['id' => 'modalHapus', 'title' => 'Hapus Laporan'])

{{-- MODAL SELESAI PEMELIHARAAN --}}
<div class="modal fade" id="modalSelesai" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <form id="formSelesai" method="POST">
                @csrf
                <div class="modal-header bg-success text-white border-0 py-3">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-check-circle mr-2"></i> Penyelesaian Pemeliharaan</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <p class="mb-4">Konfirmasi bahwa pemeliharaan untuk aset <strong id="selesai_nama"></strong> telah selesai dilakukan.</p>
                    <div class="form-group mb-3">
                        <label class="small font-weight-bold text-muted text-uppercase">Tanggal Selesai <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="tanggal_selesai" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-muted text-uppercase">Catatan Penyelesaian</label>
                        <textarea class="form-control" name="catatan_selesai" rows="3" placeholder="Contoh: Sudah diganti sparepart, kondisi normal..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 px-4 justify-content-between">
                    <button type="button" class="btn btn-link text-muted font-weight-bold" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 shadow-sm font-weight-bold" style="border-radius: 8px;">Simpan & Selesaikan</button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Modal Hapus
        $('#modalHapus').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var nama = button.data('nama');
            var targetUrl = button.data('url');

            $(this).find('#formDelete').attr('action', targetUrl);
            $(this).find('#delete_nama').text(nama);
        });

        // Modal Selesai
        $('#modalSelesai').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var nama = button.data('nama');
            
            $('#selesai_nama').text(nama);
            
            var url = '{{ route("manajemenasetdanasrama.pemeliharaan.selesai", ":id") }}';
            url = url.replace(':id', id);
            $('#formSelesai').attr('action', url);
        });
    });
</script>
@endpush
