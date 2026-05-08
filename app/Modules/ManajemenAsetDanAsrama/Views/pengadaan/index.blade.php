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

@section('content')
<div class="container-fluid">
    {{-- Quick Navigation --}}
    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-between">
            <a href="{{ route('manajemenasetdanasrama.persetujuan.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-arrow-left"></i> Kembali ke Persetujuan
            </a>
            <a href="{{ route('manajemenasetdanasrama.aset.index') }}" class="btn btn-outline-primary shadow-sm">
                Lanjut ke Master Aset <i class="fas fa-arrow-right"></i>
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
    {{-- Pengajuan Menunggu Diproses --}}
    @if($menungguProses->count() > 0)
    <div class="row mb-3">
        <div class="col-md-12">
            <x-card title="Pengajuan Disetujui — Menunggu Diproses" icon="fas fa-clock">
                <div class="alert alert-warning mb-3">
                    <i class="fas fa-info-circle"></i> Pengajuan berikut sudah <strong>disetujui</strong> dan siap diproses menjadi pengadaan. Klik tombol <strong>"Proses Pengadaan"</strong> untuk melanjutkan.
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Nomor Pengajuan</th>
                                <th>Nama Aset</th>
                                <th>Estimasi Harga</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Pengaju</th>
                                <th width="180">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($menungguProses as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><code>{{ $item->nomor_pengajuan }}</code></td>
                                <td><strong>{{ $item->nama_aset }}</strong></td>
                                <td>Rp {{ number_format($item->estimasi_harga, 0, ',', '.') }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d/m/Y') }}</td>
                                <td>{{ $item->pengaju->name ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('manajemenasetdanasrama.pengadaan.proses', $item->id) }}" 
                                       class="btn btn-sm btn-success">
                                        <i class="fas fa-truck mr-1"></i> Proses Pengadaan
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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
                                    <button type="button" class="btn btn-xs btn-success"
                                            data-toggle="modal"
                                            data-target="#modalSelesai"
                                            data-id="{{ $item->id }}"
                                            data-nama="{{ $item->pengajuan->nama_aset ?? '' }}"
                                            data-biaya="{{ number_format($item->biaya_riil, 0, ',', '.') }}"
                                            data-vendor="{{ $item->vendor }}"
                                            title="Konfirmasi Barang Datang">
                                        <i class="fas fa-check"></i> Konfirmasi Datang
                                    </button>
                                    @elseif($item->status == 'datang')
                                    <a href="{{ route('manajemenasetdanasrama.aset.index') }}" class="btn btn-xs btn-outline-primary" title="Lihat di Master Aset">
                                        <i class="fas fa-boxes"></i> Lihat Aset
                                    </a>
                                    @else
                                        <span class="text-muted">-</span>
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
<div class="modal fade" id="modalSelesai" tabindex="-1" role="dialog" aria-labelledby="modalSelesaiLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="formSelesai" action="" method="POST">
                @csrf
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white" id="modalSelesaiLabel">Selesaikan Pengadaan & Tambahkan Aset</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong><i class="fas fa-info-circle"></i> Ringkasan Pengadaan:</strong><br>
                        Aset: <strong id="selesai_nama_aset"></strong><br>
                        Vendor: <span id="selesai_vendor"></span><br>
                        Biaya: <span id="selesai_biaya"></span>
                    </div>
                    <p>Konfirmasi bahwa barang sudah datang dan daftarkan ke Master Aset:</p>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tanggal_datang">Tanggal Barang Datang <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="tanggal_datang" name="tanggal_datang" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kode_aset">Kode Aset <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="kode_aset" name="kode_aset" placeholder="Contoh: AST-001" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="nama_aset">Nama Aset <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="selesai_nama_input" name="nama_aset" required readonly>
                    </div>
                    <div class="form-group">
                        <label for="kamar_id">Pilih Kamar (Lokasi Penempatan) <span class="text-danger">*</span></label>
                        <select class="form-control" id="kamar_id" name="kamar_id" required>
                            <option value="">-- Pilih Kamar --</option>
                            @foreach($kamar as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kamar }} (Kapasitas: {{ $k->kapasitas }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="kondisi">Kondisi Saat Diterima</label>
                        <textarea class="form-control" id="kondisi" name="kondisi" rows="2" placeholder="Contoh: Baik, sesuai spesifikasi"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="deskripsi_aset">Deskripsi Aset</label>
                        <textarea class="form-control" id="deskripsi_aset" name="deskripsi_aset" rows="2" placeholder="Detail tambahan mengenai aset"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-check mr-1"></i> Konfirmasi & Simpan ke Master Aset</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
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
        });
    });
</script>
@endpush
