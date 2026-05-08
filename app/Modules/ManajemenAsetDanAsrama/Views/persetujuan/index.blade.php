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

{{-- MODAL DETAIL PENGAJUAN (LIHAT) --}}
<div class="modal fade" id="modalDetailPengajuan" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header bg-info text-white border-0 py-3">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-file-invoice mr-2"></i> Detail Pengajuan Aset
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-md-6 border-right">
                        <h6 class="font-weight-bold text-info mb-3 text-uppercase small" style="letter-spacing: 1px;">Informasi Aset</h6>
                        <div class="mb-3">
                            <label class="small text-muted mb-0 d-block text-uppercase">Nomor Pengajuan</label>
                            <code id="detail_nomor_pengajuan" class="font-weight-bold" style="font-size: 1rem;">-</code>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted mb-0 d-block text-uppercase">Nama Aset</label>
                            <span id="detail_nama_aset" class="font-weight-bold text-dark h6">-</span>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted mb-0 d-block text-uppercase">Estimasi Harga</label>
                            <span id="detail_estimasi_harga" class="font-weight-bold text-success h6">-</span>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted mb-0 d-block text-uppercase">Deskripsi Alasan</label>
                            <p id="detail_deskripsi" class="text-muted small mb-0"></p>
                        </div>
                    </div>
                    <div class="col-md-6 pl-md-4">
                        <h6 class="font-weight-bold text-info mb-3 text-uppercase small" style="letter-spacing: 1px;">Status & Verifikasi</h6>
                        <div class="mb-3">
                            <label class="small text-muted mb-0 d-block text-uppercase">Status Saat Ini</label>
                            <div id="detail_status" class="mt-1"></div>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="small text-muted mb-0 d-block text-uppercase">Diajukan Oleh</label>
                                <span id="detail_pengaju" class="font-weight-bold small text-dark">-</span>
                            </div>
                            <div class="col-6 mb-3 text-right">
                                <label class="small text-muted mb-0 d-block text-uppercase">Tgl Pengajuan</label>
                                <span id="detail_tanggal" class="font-weight-bold small text-dark">-</span>
                            </div>
                        </div>
                        <div class="bg-light p-3 rounded shadow-sm border">
                            <div class="mb-2">
                                <label id="label_approved_by" class="small text-muted mb-0 d-block text-uppercase">Verifikator</label>
                                <span id="detail_approved_by" class="font-weight-bold small text-dark">-</span>
                            </div>
                            <div>
                                <label id="label_approved_at" class="small text-muted mb-0 d-block text-uppercase">Tgl Verifikasi</label>
                                <span id="detail_approved_at" class="font-weight-bold small text-dark">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="section_catatan_tolak" class="mt-4 p-3 bg-danger-soft rounded border border-danger" style="display:none; background: #fff5f5;">
                    <label class="small text-danger font-weight-bold mb-1 text-uppercase"><i class="fas fa-exclamation-circle mr-1"></i> Catatan Penolakan</label>
                    <p id="detail_catatan_tolak" class="mb-0 text-dark small italic"></p>
                </div>

                <div class="mt-4 pt-3 border-top">
                    <h6 class="font-weight-bold text-muted mb-3 text-uppercase small"><i class="fas fa-link mr-1"></i> Riwayat Pengadaan Terkait</h6>
                    <div id="detail_pengadaan" class="table-responsive">
                        {{-- Data via AJAX --}}
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light py-3 px-4">
                <button type="button" class="btn btn-secondary px-4 shadow-sm font-weight-bold" style="border-radius: 8px;" data-dismiss="modal">Tutup</button>
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

                    $('#detail_status').html('<span class="badge ' + statusClass + '">' + statusText + '</span>');
                    
                    $('#detail_pengaju').text(data.pengaju ? data.pengaju.name : '-');
                    $('#detail_approved_by').text(data.approver ? data.approver.name : '-');
                    $('#detail_approved_at').text(data.approved_at ? new Date(data.approved_at).toLocaleString('id-ID') : '-');

                    // Data pengadaan
                    if (data.pengadaan && data.pengadaan.length > 0) {
                        var html = '<table class="table table-sm table-bordered"><thead><tr><th>No. PO</th><th>Vendor</th><th>Tgl Pesan</th><th>Status</th></tr></thead><tbody>';
                        $.each(data.pengadaan, function(i, item) {
                            html += '<tr>' +
                                '<td>' + (item.nomor_po || '-') + '</td>' +
                                '<td>' + (item.vendor || '-') + '</td>' +
                                '<td>' + (item.tanggal_pesan ? new Date(item.tanggal_pesan).toLocaleDateString('id-ID') : '-') + '</td>' +
                                '<td><span class="badge badge-light border">' + (item.status || '-') + '</span></td>' +
                                '</tr>';
                        });
                        html += '</tbody></table>';
                        $('#detail_pengadaan').html(html);
                    } else {
                        $('#detail_pengadaan').html('<p class="text-muted small italic">Tidak ada riwayat pengadaan</p>');
                    }

                    // Tampilkan catatan tolak jika statusnya ditolak
                    if (data.status === 'ditolak' && data.catatan_tolak) {
                        $('#section_catatan_tolak').fadeIn();
                        $('#detail_catatan_tolak').text(data.catatan_tolak);
                        $('#label_approved_by').text('Ditolak Oleh');
                        $('#label_approved_at').text('Tanggal Penolakan');
                    } else {
                        $('#section_catatan_tolak').hide();
                        $('#label_approved_by').text('Verifikator');
                        $('#label_approved_at').text('Tgl Verifikasi');
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