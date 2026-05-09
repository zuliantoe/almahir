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
<div class="modal fade" id="modalTambahPengajuan" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <form action="{{ route('manajemenasetdanasrama.pengajuan.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white border-0 py-3">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-plus-circle mr-2"></i> Buat Pengajuan Aset
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="small font-weight-bold text-muted text-uppercase">Nama Aset <span class="text-danger">*</span></label>
                        <div class="input-group shadow-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-right-0"><i class="fas fa-box text-primary"></i></span>
                            </div>
                            <input type="text" class="form-control border-left-0 @error('nama_aset') is-invalid @enderror" name="nama_aset" value="{{ old('nama_aset') }}" placeholder="Contoh: Laptop Admin" required>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="small font-weight-bold text-muted text-uppercase">Deskripsi Alasan <span class="text-danger">*</span></label>
                        <textarea class="form-control shadow-sm @error('deskripsi_pengajuan') is-invalid @enderror" name="deskripsi_pengajuan" rows="3" placeholder="Jelaskan mengapa aset ini dibutuhkan..." required>{{ old('deskripsi_pengajuan') }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="small font-weight-bold text-muted text-uppercase">Estimasi Harga (Rp) <span class="text-danger">*</span></label>
                                <div class="input-group shadow-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-right-0 font-weight-bold text-muted">Rp</span>
                                    </div>
                                    <input type="number" class="form-control border-left-0 @error('estimasi_harga') is-invalid @enderror" name="estimasi_harga" value="{{ old('estimasi_harga') }}" min="0" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="small font-weight-bold text-muted text-uppercase">Tanggal Pengajuan <span class="text-danger">*</span></label>
                                <input type="date" class="form-control shadow-sm @error('tanggal_pengajuan') is-invalid @enderror" name="tanggal_pengajuan" value="{{ old('tanggal_pengajuan', date('Y-m-d')) }}" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 px-4">
                    <button type="button" class="btn btn-link text-muted font-weight-bold" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm" style="border-radius: 8px;">Kirim Pengajuan <i class="fas fa-paper-plane ml-2"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL EDIT PENGAJUAN --}}
<div class="modal fade" id="modalEditPengajuan" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <form id="formEditPengajuan" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-warning text-white border-0 py-3">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-edit mr-2"></i> Perbarui Pengajuan
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="small font-weight-bold text-muted text-uppercase">Nama Aset <span class="text-danger">*</span></label>
                        <div class="input-group shadow-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-right-0"><i class="fas fa-box text-warning"></i></span>
                            </div>
                            <input type="text" class="form-control border-left-0" id="edit_nama_aset" name="nama_aset" required>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="small font-weight-bold text-muted text-uppercase">Deskripsi Alasan <span class="text-danger">*</span></label>
                        <textarea class="form-control shadow-sm" id="edit_deskripsi_pengajuan" name="deskripsi_pengajuan" rows="3" required></textarea>
                    </div>
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-muted text-uppercase">Estimasi Harga (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group shadow-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-right-0 font-weight-bold text-muted">Rp</span>
                            </div>
                            <input type="number" class="form-control border-left-0" id="edit_estimasi_harga" name="estimasi_harga" min="0" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 px-4">
                    <button type="button" class="btn btn-link text-muted font-weight-bold" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning px-4 shadow-sm text-white" style="border-radius: 8px;"><i class="fas fa-save mr-2"></i> Simpan Perubahan</button>
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

{{-- MODAL AJUKAN ULANG --}}
<div class="modal fade" id="modalAjukanUlang" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <form id="formAjukanUlang" method="POST">
                @csrf
                <div class="modal-header bg-secondary text-white border-0 py-3">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-redo mr-2"></i> Ajukan Kembali Pengajuan
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-secondary border-0 mb-4" style="background: #f0f2f5;">
                        <i class="fas fa-info-circle mr-2"></i> Anda akan mengajukan kembali: <strong id="ajukan_nama" class="text-dark"></strong>
                    </div>
                    <div class="form-group mb-3">
                        <label class="small font-weight-bold text-muted text-uppercase">Deskripsi Pengajuan (Update) <span class="text-danger">*</span></label>
                        <textarea class="form-control shadow-sm" id="ajukan_deskripsi" name="deskripsi_pengajuan" rows="3" required></textarea>
                    </div>
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-muted text-uppercase">Alasan Pengajuan Ulang <span class="text-danger">*</span></label>
                        <textarea class="form-control shadow-sm border-warning" id="alasan_pengajuan_ulang" name="alasan_pengajuan_ulang" rows="3" placeholder="Jelaskan alasan mengapa ini diajukan kembali setelah ditolak..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 px-4">
                    <button type="button" class="btn btn-link text-muted font-weight-bold" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm" style="border-radius: 8px;"><i class="fas fa-paper-plane mr-2"></i> Kirim Ulang</button>
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