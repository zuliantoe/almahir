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
            <li class="breadcrumb-item active">Pengajuan Aset</li>
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
                    <p>Total Pengajuan</p>
                </div>
                <div class="icon"><i class="fas fa-file-invoice"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning shadow-sm">
                <div class="inner">
                    <h3>{{ number_format($stats['diajukan'] ?? 0) }}</h3>
                    <p>Menunggu Persetujuan</p>
                </div>
                <div class="icon"><i class="fas fa-clock"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success shadow-sm">
                <div class="inner">
                    <h3>{{ number_format($stats['disetujui'] ?? 0) }}</h3>
                    <p>Telah Disetujui</p>
                </div>
                <div class="icon"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger shadow-sm">
                <div class="inner">
                    <h3>{{ number_format($stats['ditolak'] ?? 0) }}</h3>
                    <p>Ditolak / Perbaikan</p>
                </div>
                <div class="icon"><i class="fas fa-times-circle"></i></div>
            </div>
        </div>
    </div>

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
                    <button type="button" class="btn btn-sm btn-danger mr-1" data-toggle="modal" data-target="#modalBulkDelete">
                        <i class="fas fa-trash-alt mr-1"></i> Hapus Massal
                    </button>
                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalTambahPengajuan">
                        <i class="fas fa-plus mr-1"></i> Tambah Pengajuan
                    </button>
                </x-slot>

                @include('manajemenasetdanasrama::partials.table-pengajuan', [
                    'items' => $pengajuan,
                    'mode' => 'user',
                    'showStatus' => true,
                    'actionWidth' => '180'
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

@include('manajemenasetdanasrama::partials.modal-delete', ['id' => 'modalHapus', 'title' => 'Hapus Pengajuan Aset'])

{{-- MODAL HAPUS MASSAL --}}
<div class="modal fade" id="modalBulkDelete" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <form action="{{ route('manajemenasetdanasrama.pengajuan.bulk-destroy') }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white border-0 py-3">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-trash-alt mr-2"></i> Hapus Pengajuan Massal</h5>
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
                        <p class="text-muted small">Hapus pengajuan berdasarkan kode aset atau inisial yang diajukan.</p>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="small font-weight-bold text-muted text-uppercase">Pola Kode Aset (Inisial)</label>
                        <input type="text" class="form-control shadow-sm" name="pattern" placeholder="Contoh: MEB atau MJ" required style="text-transform: uppercase;">
                        <small class="text-danger mt-1 d-block" style="font-size: 0.75rem;">
                            <i class="fas fa-info-circle mr-1"></i> <b>PERHATIAN:</b> Semua pengajuan dengan inisial ini akan dihapus permanen.
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

{{-- MODAL DUPLIKAT PENGAJUAN --}}
<div class="modal fade" id="modalDuplikatPengajuan" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <form id="formDuplikatPengajuan" method="POST">
                @csrf
                <div class="modal-header bg-secondary text-white border-0 py-3">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-copy mr-2"></i> Duplikat Pengajuan</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4 text-center">
                    <h6 class="font-weight-bold mb-1" id="duplikat_pengajuan_nama"></h6>
                    <p class="text-muted small mb-4">Ingin menduplikat pengajuan ini berapa kali?</p>
                    <div class="form-group text-left">
                        <label class="small font-weight-bold text-muted">JUMLAH DUPLIKASI</label>
                        <input type="number" class="form-control" name="jumlah" value="1" min="1" max="100" required>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 justify-content-center">
                    <button type="button" class="btn btn-link text-muted font-weight-bold mr-2" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-secondary px-4 shadow-sm" style="border-radius: 8px;">Proses Duplikat</button>
                </div>
            </form>
        </div>
    </div>
</div>

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
                    <div class="row">
                        <div class="col-md-9">
                            <div class="form-group mb-3">
                                <label class="small font-weight-bold text-muted text-uppercase">Nama Aset <span class="text-danger">*</span></label>
                                <div class="input-group shadow-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-right-0"><i class="fas fa-box text-primary"></i></span>
                                    </div>
                                    <input type="text" class="form-control border-left-0" name="nama_aset" placeholder="Contoh: Meja Belajar" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label class="small font-weight-bold text-muted text-uppercase">Jumlah <span class="text-danger">*</span></label>
                                <input type="number" class="form-control shadow-sm" name="jumlah" value="1" min="1" max="100" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="small font-weight-bold text-muted text-uppercase">Deskripsi Alasan <span class="text-danger">*</span></label>
                        <textarea class="form-control shadow-sm" name="deskripsi_pengajuan" rows="2" placeholder="Mengapa aset ini dibutuhkan?" required></textarea>
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


@include('manajemenasetdanasrama::partials.modal-detail-pengajuan')

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
@include('manajemenasetdanasrama::partials.scripts-asset')
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
            showDetailPengajuan(id, url);
        });

        // Modal Duplikat
        $('.btn-duplicate-pengajuan').on('click', function() {
            var id = $(this).data('id');
            var nama = $(this).data('nama');
            $('#duplikat_pengajuan_nama').text(nama);
            
            var url = '{{ route("manajemenasetdanasrama.pengajuan.duplicate", ":id") }}';
            url = url.replace(':id', id);
            $('#formDuplikatPengajuan').attr('action', url);
            $('#modalDuplikatPengajuan').modal('show');
        });

        // Modal Hapus
        $('#modalHapus').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var url = button.data('url');
            var nama = button.data('nama');
            
            $(this).find('#formDelete').attr('action', url);
            $(this).find('#hapus_nama_generic').text(nama);
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
        // Prevent double submission
        $('form').on('submit', function() {
            var btn = $(this).find('button[type="submit"]');
            btn.attr('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...');
        });
    });
</script>
@endpush