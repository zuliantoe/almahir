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
            <li class="breadcrumb-item active">Pengadaan Aset</li>
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
            <div class="small-box bg-purple shadow-sm">
                <div class="inner">
                    <h3>{{ number_format($stats['menunggu'] ?? 0) }}</h3>
                    <p>Menunggu Proses PO</p>
                </div>
                <div class="icon"><i class="fas fa-file-contract"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning shadow-sm">
                <div class="inner">
                    <h3>{{ number_format($stats['dipesan'] ?? 0) }}</h3>
                    <p>Sedang Dipesan</p>
                </div>
                <div class="icon"><i class="fas fa-shipping-fast"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success shadow-sm">
                <div class="inner">
                    <h3>{{ number_format($stats['datang'] ?? 0) }}</h3>
                    <p>Barang Diterima</p>
                </div>
                <div class="icon"><i class="fas fa-hand-holding-box"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info shadow-sm">
                <div class="inner">
                    <h3 style="font-size: 1.6rem;">Rp {{ number_format($stats['total_biaya'] ?? 0, 0, ',', '.') }}</h3>
                    <p>Total Nilai Pengadaan</p>
                </div>
                <div class="icon"><i class="fas fa-wallet"></i></div>
            </div>
        </div>
    </div>

    {{-- Quick Navigation --}}
    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-between align-items-center">
            <div>
                <a href="{{ route('manajemenasetdanasrama.persetujuan.index') }}" class="btn btn-outline-secondary shadow-sm mr-2">
                    <i class="fas fa-arrow-left"></i> Kembali ke Persetujuan
                </a>
                <button type="button" class="btn btn-primary shadow-sm mr-2" data-toggle="modal" data-target="#modalBulkStore">
                    <i class="fas fa-file-invoice mr-1"></i> Proses Masal
                </button>
                <button type="button" class="btn btn-success shadow-sm" data-toggle="modal" data-target="#modalBulkConfirm">
                    <i class="fas fa-check-double mr-1"></i> Konfirmasi Masal
                </button>
            </div>
            <a href="{{ route('manajemenasetdanasrama.aset.index') }}" class="btn btn-outline-primary shadow-sm">
                Lanjut ke Master Aset <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>

{{-- MODAL PROSES MASAL (CREATE PO) --}}
<div class="modal fade" id="modalBulkStore" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <form action="{{ route('manajemenasetdanasrama.pengadaan.bulk-store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white border-0 py-3">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-file-invoice mr-2"></i> Proses Pengadaan Masal (Buat PO)
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-light border shadow-sm mb-4" style="border-left: 5px solid #007bff; background: #f0f7ff;">
                        <div class="d-flex align-items-center">
                            <div class="mr-3">
                                <i class="fas fa-info-circle text-primary fa-2x"></i>
                            </div>
                            <div>
                                <h6 class="font-weight-bold mb-1">Mode Proses PO Masal</h6>
                                <p class="small text-muted mb-0">Form ini untuk memproses pengajuan yang sudah <b>Disetujui</b> menjadi status <b>Dipesan</b>.</p>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="small font-weight-bold text-muted text-uppercase">Inisial Kode / Nama Barang <span class="text-danger">*</span></label>
                        <div class="input-group shadow-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-right-0"><i class="fas fa-search"></i></span>
                            </div>
                            <input type="text" class="form-control border-left-0" name="prefix" placeholder="Contoh: Kursi atau KRS" required>
                        </div>
                        <small class="text-muted">Mencari semua pengajuan disetujui yang berawalan inisial ini.</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="small font-weight-bold text-muted text-uppercase">Vendor / Supplier <span class="text-danger">*</span></label>
                                <input type="text" class="form-control shadow-sm" name="vendor" placeholder="Contoh: Toko Abadi" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="small font-weight-bold text-muted text-uppercase">Biaya Riil (Per Item)</label>
                                <div class="input-group shadow-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0"><i class="fas fa-coins"></i></span>
                                    </div>
                                    <input type="number" class="form-control border-left-0" name="biaya_riil" placeholder="Sesuai Harga Pengajuan">
                                </div>
                                <small class="text-info"><i class="fas fa-info-circle mr-1"></i> Biarkan kosong jika harga tidak berubah.</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="small font-weight-bold text-muted text-uppercase">Tanggal Pesan <span class="text-danger">*</span></label>
                                <input type="date" class="form-control shadow-sm" name="tanggal_pesan" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="small font-weight-bold text-muted text-uppercase">Estimasi Datang <span class="text-danger">*</span></label>
                                <input type="date" class="form-control shadow-sm" name="estimasi_datang" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-muted text-uppercase">Catatan Pengadaan</label>
                        <textarea class="form-control shadow-sm" name="catatan_pengadaan" rows="2" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 px-4 justify-content-between">
                    <button type="button" class="btn btn-link text-muted font-weight-bold" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm font-weight-bold" style="border-radius: 8px;">
                        <i class="fas fa-save mr-2"></i> Simpan Pengadaan Masal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL KONFIRMASI MASAL (RECEIVE) --}}
<div class="modal fade" id="modalBulkConfirm" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <form action="{{ route('manajemenasetdanasrama.pengadaan.bulk-confirm') }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white border-0 py-3">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-shipping-fast mr-2"></i> Konfirmasi Kedatangan Masal
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-light border shadow-sm mb-4" style="border-left: 5px solid #28a745; background: #f8fff9;">
                        <div class="d-flex align-items-center">
                            <div class="mr-3">
                                <i class="fas fa-info-circle text-success fa-2x"></i>
                            </div>
                            <div>
                                <h6 class="font-weight-bold mb-1">Mode Konfirmasi Masal</h6>
                                <p class="small text-muted mb-0">Masukkan inisial kode (PO/Aset) atau nama. Semua pesanan status <b>Dipesan</b> yang cocok akan dikonfirmasi datang.</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="small font-weight-bold text-muted text-uppercase">Inisial Kode / Nama Barang <span class="text-danger">*</span></label>
                                <div class="input-group shadow-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-right-0"><i class="fas fa-search"></i></span>
                                    </div>
                                    <input type="text" class="form-control border-left-0" name="prefix" placeholder="Contoh: Kursi atau KRS" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="small font-weight-bold text-muted text-uppercase">Tanggal Datang <span class="text-danger">*</span></label>
                                <div class="input-group shadow-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-right-0"><i class="fas fa-calendar-alt"></i></span>
                                    </div>
                                    <input type="date" class="form-control border-left-0" name="tanggal_datang" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted text-uppercase">Kondisi saat Diterima</label>
                                <textarea class="form-control shadow-sm" name="kondisi" rows="3" placeholder="Contoh: Baik / Segel Utuh"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted text-uppercase">Deskripsi / Catatan Tambahan</label>
                                <textarea class="form-control shadow-sm" name="deskripsi_aset" rows="3" placeholder="Contoh: Warna Hitam, Merk LG"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 px-4 justify-content-between">
                    <button type="button" class="btn btn-link text-muted font-weight-bold" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 shadow-sm font-weight-bold" style="border-radius: 8px;">
                        <i class="fas fa-save mr-2"></i> Konfirmasi Semua & Masuk Master Aset
                    </button>
                </div>
            </form>
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
    {{-- Pengajuan Menunggu Diproses --}}
    @if($menungguProses->count() > 0)
    <div class="row mb-3">
        <div class="col-md-12">
            <x-card title="Pengajuan Disetujui — Menunggu Diproses" icon="fas fa-clock">
                <div class="alert alert-warning mb-3">
                    <i class="fas fa-info-circle"></i> Pengajuan berikut sudah <strong>disetujui</strong> dan siap diproses menjadi pengadaan. Klik tombol <strong>"Proses Pengadaan"</strong> untuk melanjutkan.
                </div>
                @include('manajemenasetdanasrama::partials.table-pengajuan', [
                    'items' => $menungguProses,
                    'mode' => 'procurement',
                    'showStatus' => false,
                    'actionWidth' => '180',
                    'striped' => false
                ])
                <x-slot name="footer">
                    <small class="text-muted">{{ $menungguProses->count() }} pengajuan menunggu diproses</small>
                </x-slot>
            </x-card>
        </div>
    </div>
    @endif

    {{-- Daftar Pengadaan --}}
    <div class="row">
        <div class="col-md-12">
            <x-card title="Daftar Pengadaan Aset" icon="fas fa-truck">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nomor PO</th>
                                <th>Nama Aset (Pengajuan)</th>
                                <th>Kode Aset</th>
                                <th>Vendor</th>
                                <th>Biaya Riil</th>
                                <th>Tgl Pesan</th>
                                <th>Estimasi Datang</th>
                                <th>Tgl Datang</th>
                                <th>Catatan</th>
                                <th>Status</th>
                                <th width="180">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengadaan as $item)
                            <tr>
                                <td>{{ $loop->iteration + ($pengadaan->currentPage() - 1) * $pengadaan->perPage() }}</td>
                                <td><code>{{ $item->nomor_po }}</code></td>
                                <td>{{ $item->pengajuan->nama_aset ?? '-' }}</td>
                                <td>
                                    @if($item->aset)
                                        <span class="badge badge-info">{{ $item->aset->kode_aset }}</span>
                                    @else
                                        <span class="text-muted small"><i>Belum Datang</i></span>
                                    @endif
                                </td>
                                <td>{{ $item->vendor }}</td>
                                <td>Rp {{ number_format($item->biaya_riil, 0, ',', '.') }}</td>
                                <td>{{ $item->tanggal_pesan ? $item->tanggal_pesan->format('d/m/Y') : '-' }}</td>
                                <td>{{ $item->estimasi_datang ? $item->estimasi_datang->format('d/m/Y') : '-' }}</td>
                                <td>
                                    @if($item->tanggal_datang)
                                        <span class="text-success font-weight-bold">{{ $item->tanggal_datang->format('d/m/Y') }}</span>
                                    @else
                                        <span class="text-muted">Belum datang</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit($item->catatan_pengadaan ?? '-', 30) }}</td>
                                <td>
                                    @if($item->status == 'dipesan')
                                        <span class="badge badge-warning">Dipesan</span>
                                    @elseif($item->status == 'datang')
                                        <span class="badge badge-success">Barang Datang</span>
                                    @elseif($item->status == 'batal')
                                        <span class="badge badge-danger">Batal</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->status == 'dipesan')
                                    <button type="button" class="btn btn-action-xs btn-success"
                                            data-toggle="modal"
                                            data-target="#modalSelesai"
                                            data-id="{{ $item->id }}"
                                            data-nama="{{ $item->pengajuan->nama_aset ?? '' }}"
                                            data-biaya="{{ number_format($item->biaya_riil, 0, ',', '.') }}"
                                            data-vendor="{{ $item->vendor }}"
                                            title="Konfirmasi Barang Datang">
                                        <i class="fas fa-check mr-1"></i> Konfirmasi Datang
                                    </button>
                                    @elseif($item->status == 'datang')
                                    <a href="{{ route('manajemenasetdanasrama.aset.index') }}" class="btn btn-action-xs btn-outline-primary" title="Lihat di Master Aset">
                                        <i class="fas fa-boxes mr-1"></i> Lihat Aset
                                    </a>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted">
                                    <i class="fas fa-inbox"></i> Belum ada data pengadaan
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($pengadaan->hasPages())
                <div class="d-flex justify-content-end mt-3">
                    {{ $pengadaan->links() }}
                </div>
                @endif

                <x-slot name="footer">
                    <small class="text-muted">Total data: {{ $pengadaan->total() }}</small>
                </x-slot>
            </x-card>
        </div>
    </div>
</div>

{{-- MODAL SELESAI PENGADAAN --}}
<div class="modal fade" id="modalSelesai" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <form id="formSelesai" method="POST">
                @csrf
                <div class="modal-header bg-success text-white border-0 py-3">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-check-circle mr-2"></i> Konfirmasi Kedatangan Barang
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-light border shadow-sm mb-4" style="border-left: 5px solid #28a745; background: #f8fff9;">
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted text-uppercase font-weight-bold d-block">Nama Aset</small>
                                <strong id="selesai_nama_aset" class="text-dark"></strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted text-uppercase font-weight-bold d-block">Vendor</small>
                                <span id="selesai_vendor" class="text-dark"></span>
                            </div>
                            <div class="col-md-3 text-right">
                                <small class="text-muted text-uppercase font-weight-bold d-block">Biaya Riil</small>
                                <span id="selesai_biaya" class="font-weight-bold text-success"></span>
                            </div>
                        </div>
                    </div>
                    <h6 class="font-weight-bold text-primary mb-3"><i class="fas fa-edit mr-2"></i> Detail Registrasi Aset</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="small font-weight-bold text-muted text-uppercase">Tanggal Datang <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-right-0"><i class="fas fa-calendar-alt"></i></span>
                                    </div>
                                    <input type="date" class="form-control border-left-0" id="tanggal_datang" name="tanggal_datang" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="small font-weight-bold text-muted text-uppercase">Nama Aset (Final) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control bg-light font-weight-bold" id="selesai_nama_input" name="nama_aset" required readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="small font-weight-bold text-muted text-uppercase">Kondisi saat Diterima</label>
                                <textarea class="form-control" name="kondisi" rows="2" placeholder="Contoh: Baik / Segel Utuh"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="small font-weight-bold text-muted text-uppercase">Deskripsi Aset</label>
                                <textarea class="form-control" name="deskripsi_aset" rows="2" placeholder="Contoh: Warna Hitam, Merk LG"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 px-4 justify-content-between">
                    <button type="button" class="btn btn-link text-muted font-weight-bold" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 shadow-sm font-weight-bold" style="border-radius: 8px;">
                        <i class="fas fa-save mr-2"></i> Daftarkan ke Master Aset
                    </button>
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

        $('#modalSelesai').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var nama = button.data('nama');
            var biaya = button.data('biaya');
            var vendor = button.data('vendor');

            var modal = $(this);
            modal.find('#selesai_nama_aset').text(nama);
            modal.find('#selesai_nama_input').val(nama);
            modal.find('#selesai_vendor').text(vendor || '-');
            modal.find('#selesai_biaya').text('Rp ' + (biaya || '0'));

            var url = '{{ route("manajemenasetdanasrama.pengadaan.selesai", ":id") }}';
            url = url.replace(':id', id);
            modal.find('#formSelesai').attr('action', url);

            // SUGGEST ASSET CODE
            $.get('{{ route("manajemenasetdanasrama.aset.suggest-code") }}', { nama: nama }, function(res) {
                modal.find('input[name="kode_aset"]').val(res.code);
            });
        });

        // Sync Tanggal Pesan -> Estimasi Datang (Bulk Modal)
        $('#modalBulkStore input[name="tanggal_pesan"]').on('change', function() {
            var tanggalPesan = $(this).val();
            var estimasiDatangInput = $('#modalBulkStore input[name="estimasi_datang"]');
            
            // Set minimal tanggal datang
            estimasiDatangInput.attr('min', tanggalPesan);
            
            // Auto fill jika masih kosong atau jika tanggal datang sebelumnya lebih kecil
            if (!estimasiDatangInput.val() || estimasiDatangInput.val() < tanggalPesan) {
                estimasiDatangInput.val(tanggalPesan);
            }
        });
    });
</script>
@endpush
