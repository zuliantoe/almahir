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

@section('content')
<div class="container-fluid">
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
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nomor Pengajuan</th>
                                <th>Nama Aset</th>
                                <th>Estimasi Harga</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Pengaju</th>
                                <th width="220">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengajuan as $item)
                            <tr>
                                <td>{{ $loop->iteration + ($pengajuan->currentPage() - 1) * $pengajuan->perPage() }}</td>
                                <td>{{ $item->nomor_pengajuan ?? '-' }}</td>
                                <td>
                                    {{ $item->nama_aset }}
                                    @if($item->alasan_pengajuan_ulang)
                                        <span class="badge badge-info ml-1">Ajuan Ulang</span>
                                    @endif
                                </td>
                                <td>Rp {{ number_format($item->estimasi_harga, 0, ',', '.') }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d/m/Y') }}</td>
                                <td>{{ $item->pengaju->name ?? '-' }}</td>
                                <td>
                                    {{-- Tombol Lihat --}}
                                    <button type="button" class="btn btn-xs btn-info btn-lihat" data-id="{{ $item->id }}" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    {{-- Tombol Approve --}}
                                    <button type="button" class="btn btn-xs btn-success btn-approve" 
                                            data-id="{{ $item->id }}"
                                            data-nama="{{ $item->nama_aset }}"
                                            title="Setujui">
                                        <i class="fas fa-check"></i> Setujui
                                    </button>

                                    {{-- Tombol Reject --}}
                                    <button type="button" class="btn btn-xs btn-danger btn-reject" 
                                            data-id="{{ $item->id }}"
                                            data-nama="{{ $item->nama_aset }}"
                                            title="Tolak">
                                        <i class="fas fa-times"></i> Tolak
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    <i class="fas fa-inbox"></i> Tidak ada pengajuan yang menunggu persetujuan
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

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
<div class="modal fade" id="modalApprove" tabindex="-1" role="dialog" aria-labelledby="modalApproveLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formApprove" action="" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalApproveLabel">Setujui Pengajuan Aset</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menyetujui pengajuan <strong id="approve_nama"></strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Ya, Setujui</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL REJECT --}}
<div class="modal fade" id="modalReject" tabindex="-1" role="dialog" aria-labelledby="modalRejectLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formReject" action="" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalRejectLabel">Tolak Pengajuan Aset</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Anda akan menolak pengajuan <strong id="reject_nama"></strong>. Berikan alasan penolakan:</p>
                    <div class="form-group">
                        <label for="catatan_tolak">Catatan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="catatan_tolak" name="catatan_tolak" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL DETAIL PENGAJUAN (LIHAT) --}}
<div class="modal fade" id="modalDetailPengajuan" tabindex="-1" role="dialog" aria-labelledby="modalDetailPengajuanLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetailPengajuanLabel">Detail Pengajuan Aset</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th width="40%">Nomor Pengajuan</th>
                                <td><span id="detail_nomor_pengajuan">-</span></td>
                            </tr>
                            <tr>
                                <th>Nama Aset</th>
                                <td><span id="detail_nama_aset">-</span></td>
                            </tr>
                            <tr>
                                <th>Deskripsi</th>
                                <td><span id="detail_deskripsi">-</span></td>
                            </tr>
                            <tr>
                                <th>Estimasi Harga</th>
                                <td><span id="detail_estimasi_harga">-</span></td>
                            </tr>
                            <tr>
                                <th>Tanggal Pengajuan</th>
                                <td><span id="detail_tanggal">-</span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th width="40%">Status</th>
                                <td><span id="detail_status">-</span></td>
                            </tr>
                            <tr>
                                <th>Pengaju</th>
                                <td><span id="detail_pengaju">-</span></td>
                            </tr>
                            <tr>
                                <th id="label_approved_by">Disetujui Oleh</th>
                                <td><span id="detail_approved_by">-</span></td>
                            </tr>
                            <tr>
                                <th id="label_approved_at">Tanggal Persetujuan</th>
                                <td><span id="detail_approved_at">-</span></td>
                            </tr>
                            <tr>
                                <th>Catatan Tolak</th>
                                <td><span id="detail_catatan_tolak">-</span></td>
                            </tr>
                            <tr>
                                <th>Alasan Pengajuan Ulang</th>
                                <td><span id="detail_alasan_ulang">-</span></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Data Pengadaan Terkait</h6>
                        <div id="detail_pengadaan">
                            <p class="text-muted">Tidak ada data pengadaan</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Tombol Lihat - fetch data via AJAX dan tampilkan di modal
        $('.btn-lihat').on('click', function() {
            var id = $(this).data('id');
            var url = '{{ route("manajemenasetdanasrama.pengajuan.show", ":id") }}';
            url = url.replace(':id', id);

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Isi field modal
                    $('#detail_nomor_pengajuan').text(data.nomor_pengajuan || '-');
                    $('#detail_nama_aset').text(data.nama_aset || '-');
                    $('#detail_deskripsi').text(data.deskripsi_pengajuan || '-');
                    $('#detail_estimasi_harga').text(data.estimasi_harga ? 'Rp ' + new Intl.NumberFormat('id-ID').format(data.estimasi_harga) : '-');
                    $('#detail_tanggal').text(data.tanggal_pengajuan ? new Date(data.tanggal_pengajuan).toLocaleDateString('id-ID') : '-');

                    // Status dengan badge
                    var statusText = '';
                    var statusClass = '';
                    switch (data.status) {
                        case 'diajukan':
                            statusText = 'Diajukan';
                            statusClass = 'badge-warning';
                            break;
                        case 'disetujui':
                            statusText = 'Disetujui';
                            statusClass = 'badge-success';
                            break;
                        case 'ditolak':
                            statusText = 'Ditolak';
                            statusClass = 'badge-danger';
                            break;
                        case 'proses_pengadaan':
                            statusText = 'Proses Pengadaan';
                            statusClass = 'badge-info';
                            break;
                    }
                    $('#detail_status').html('<span class="badge ' + statusClass + '">' + statusText + '</span>');

                    // Ubah label berdasarkan status
                    if (data.status === 'ditolak') {
                        $('#label_approved_by').text('Ditolak Oleh');
                        $('#label_approved_at').text('Tanggal Penolakan');
                    } else {
                        $('#label_approved_by').text('Disetujui Oleh');
                        $('#label_approved_at').text('Tanggal Persetujuan');
                    }
                    
                    $('#detail_pengaju').text(data.pengaju ? data.pengaju.name : '-');
                    $('#detail_approved_by').text(data.approver ? data.approver.name : '-');
                    $('#detail_approved_at').text(data.approved_at ? new Date(data.approved_at).toLocaleString('id-ID') : '-');
                    $('#detail_catatan_tolak').text(data.catatan_tolak || '-');
                    $('#detail_alasan_ulang').text(data.alasan_pengajuan_ulang || '-');

                    // Data pengadaan
                    if (data.pengadaan && data.pengadaan.length > 0) {
                        var html = '<table class="table table-sm table-bordered"><thead><tr><th>No. PO</th><th>Vendor</th><th>Tanggal Pesan</th><th>Status</th></tr></thead><tbody>';
                        $.each(data.pengadaan, function(i, item) {
                            html += '<tr>' +
                                '<td>' + (item.nomor_po || '-') + '</td>' +
                                '<td>' + (item.vendor || '-') + '</td>' +
                                '<td>' + (item.tanggal_pesan ? new Date(item.tanggal_pesan).toLocaleDateString('id-ID') : '-') + '</td>' +
                                '<td>' + (item.status || '-') + '</td>' +
                                '</tr>';
                        });
                        html += '</tbody></table>';
                        $('#detail_pengadaan').html(html);
                    } else {
                        $('#detail_pengadaan').html('<p class="text-muted">Tidak ada data pengadaan</p>');
                    }

                    // Tampilkan modal
                    $('#modalDetailPengajuan').modal('show');
                },
                error: function(xhr) {
                    alert('Gagal mengambil data. Silakan coba lagi.');
                }
            });
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