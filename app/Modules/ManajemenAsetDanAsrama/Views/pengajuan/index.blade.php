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
            <li class="breadcrumb-item active">Pengajuan Aset</li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    {{-- Quick Navigation --}}
    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-between">
            <a href="{{ route('manajemenasetdanasrama.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
            </a>
            <a href="{{ route('manajemenasetdanasrama.persetujuan.index') }}" class="btn btn-outline-primary shadow-sm">
                Lanjut ke Persetujuan <i class="fas fa-arrow-right"></i>
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
            <x-card title="Daftar Pengajuan Aset" icon="fas fa-file-alt">
                <x-slot name="tools">
                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalTambahPengajuan">
                        <i class="fas fa-plus mr-1"></i> Tambah Pengajuan
                    </button>
                </x-slot>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th width="140">Nomor Pengajuan</th>
                                <th>Nama Aset</th>
                                <th width="140">Estimasi Harga</th>
                                <th width="130">Tanggal Pengajuan</th>
                                <th width="150">Status</th>
                                <th width="150">Pengaju</th>
                                <th width="150">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengajuan as $item)
                            <tr>
                                <td>{{ $loop->iteration + ($pengajuan->currentPage() - 1) * $pengajuan->perPage() }}</td>
                                <td>{{ $item->nomor_pengajuan ?? '-' }}</td>
                                <td><strong>{{ $item->nama_aset }}</strong></td>
                                <td>{{ $item->estimasi_harga_formatted }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d/m/Y') }}</td>
                                <td>{!! $item->status_badge !!}</td>
                                <td>{{ $item->pengaju->name ?? '-' }}</td>
                                <td>
                                    {{-- Tombol Lihat --}}
                                    <button type="button" class="btn btn-xs btn-info btn-lihat" data-id="{{ $item->id }}" title="Lihat">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    {{-- Tombol Edit --}}
                                    <button type="button" class="btn btn-xs btn-warning" 
                                            data-toggle="modal" 
                                            data-target="#modalEditPengajuan"
                                            data-id="{{ $item->id }}"
                                            data-nama_aset="{{ $item->nama_aset }}"
                                            data-deskripsi="{{ $item->deskripsi_pengajuan }}"
                                            data-estimasi_harga="{{ $item->estimasi_harga }}"
                                            title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    {{-- Tombol Hapus --}}
                                    <button type="button" class="btn btn-xs btn-danger" 
                                            data-toggle="modal" 
                                            data-target="#modalHapus"
                                            data-id="{{ $item->id }}"
                                            data-nama="{{ $item->nama_aset }}"
                                            data-url="{{ route('manajemenasetdanasrama.pengajuan.destroy', $item->id) }}"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                    {{-- Tombol Ajukan Kembali (khusus ditolak) --}}
                                    @if($item->status == 'ditolak')
                                    <button type="button" class="btn btn-xs btn-secondary btn-ajukan-ulang mt-1"
                                            data-toggle="modal" 
                                            data-target="#modalAjukanUlang"
                                            data-id="{{ $item->id }}"
                                            data-nama="{{ $item->nama_aset }}"
                                            data-deskripsi="{{ $item->deskripsi_pengajuan }}"
                                            title="Ajukan Kembali">
                                        <i class="fas fa-redo"></i> Ajukan Kembali
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    <i class="fas fa-inbox"></i> Belum ada data pengajuan
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

@include('manajemenasetdanasrama::partials.modal-delete', ['id' => 'modalHapus', 'title' => 'Hapus Pengajuan Aset'])

{{-- MODAL TAMBAH PENGAJUAN --}}
<div class="modal fade" id="modalTambahPengajuan" tabindex="-1" role="dialog" aria-labelledby="modalTambahPengajuanLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('manajemenasetdanasrama.pengajuan.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white" id="modalTambahPengajuanLabel">Tambah Pengajuan Aset</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nama_aset">Nama Aset <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_aset') is-invalid @enderror" id="nama_aset" name="nama_aset" value="{{ old('nama_aset') }}" required>
                        @error('nama_aset')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="deskripsi_pengajuan">Deskripsi Pengajuan <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('deskripsi_pengajuan') is-invalid @enderror" id="deskripsi_pengajuan" name="deskripsi_pengajuan" rows="3" required>{{ old('deskripsi_pengajuan') }}</textarea>
                        @error('deskripsi_pengajuan')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="estimasi_harga">Estimasi Harga (Rp) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('estimasi_harga') is-invalid @enderror" id="estimasi_harga" name="estimasi_harga" value="{{ old('estimasi_harga') }}" min="0" required>
                        @error('estimasi_harga')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="tanggal_pengajuan">Tanggal Pengajuan <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('tanggal_pengajuan') is-invalid @enderror" id="tanggal_pengajuan" name="tanggal_pengajuan" value="{{ old('tanggal_pengajuan', date('Y-m-d')) }}" required>
                        @error('tanggal_pengajuan')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
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

{{-- MODAL EDIT PENGAJUAN --}}
<div class="modal fade" id="modalEditPengajuan" tabindex="-1" role="dialog" aria-labelledby="modalEditPengajuanLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formEditPengajuan" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="modalEditPengajuanLabel">Edit Pengajuan Aset</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_nama_aset">Nama Aset <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_nama_aset" name="nama_aset" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_deskripsi_pengajuan">Deskripsi Pengajuan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="edit_deskripsi_pengajuan" name="deskripsi_pengajuan" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit_estimasi_harga">Estimasi Harga (Rp) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="edit_estimasi_harga" name="estimasi_harga" min="0" required>
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


{{-- MODAL DETAIL PENGAJUAN (LIHAT) --}}
<div class="modal fade" id="modalDetailPengajuan" tabindex="-1" role="dialog" aria-labelledby="modalDetailPengajuanLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white" id="modalDetailPengajuanLabel">Detail Pengajuan Aset</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
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

{{-- MODAL AJUKAN ULANG --}}
<div class="modal fade" id="modalAjukanUlang" tabindex="-1" role="dialog" aria-labelledby="modalAjukanUlangLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formAjukanUlang" action="" method="POST">
                @csrf
                <div class="modal-header bg-secondary">
                    <h5 class="modal-title text-white" id="modalAjukanUlangLabel">Ajukan Kembali Pengajuan</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Ajukan kembali pengajuan <strong id="ajukan_nama"></strong> dengan perubahan berikut:</p>
                    <div class="form-group">
                        <label for="ajukan_deskripsi">Deskripsi Pengajuan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="ajukan_deskripsi" name="deskripsi_pengajuan" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="alasan_pengajuan_ulang">Alasan Pengajuan Ulang <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="alasan_pengajuan_ulang" name="alasan_pengajuan_ulang" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Edit modal - isi data dari tombol
        $('#modalEditPengajuan').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var nama = button.data('nama_aset');
            var deskripsi = button.data('deskripsi');
            var estimasi = button.data('estimasi_harga');

            var modal = $(this);
            modal.find('#edit_nama_aset').val(nama);
            modal.find('#edit_deskripsi_pengajuan').val(deskripsi);
            modal.find('#edit_estimasi_harga').val(estimasi);

            var url = '{{ route("manajemenasetdanasrama.pengajuan.update", ":id") }}';
            url = url.replace(':id', id);
            modal.find('#formEditPengajuan').attr('action', url);
        });


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

        // Ajukan ulang modal
        $('.btn-ajukan-ulang').on('click', function() {
            var id = $(this).data('id');
            var nama = $(this).data('nama');
            var deskripsi = $(this).data('deskripsi');

            $('#ajukan_nama').text(nama);
            $('#ajukan_deskripsi').val(deskripsi);
            $('#alasan_pengajuan_ulang').val(''); // kosongkan field alasan

            var url = '{{ route("manajemenasetdanasrama.pengajuan.ajukan-ulang", ":id") }}';
            url = url.replace(':id', id);
            $('#formAjukanUlang').attr('action', url);
        });
    });
</script>
@endpush