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
            <li class="breadcrumb-item active">Pemeliharaan</li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    {{-- Quick Information --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning shadow-sm">
                <div class="inner">
                    <h3 style="font-size: 1.8rem;">{{ number_format($stats['total_proses'] ?? 0) }}</h3>
                    <p>Dalam Perbaikan</p>
                </div>
                <div class="icon"><i class="fas fa-tools"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success shadow-sm">
                <div class="inner">
                    <h3 style="font-size: 1.8rem;">{{ number_format($stats['total_selesai'] ?? 0) }}</h3>
                    <p>Selesai Diperbaiki</p>
                </div>
                <div class="icon"><i class="fas fa-check-double"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info shadow-sm">
                <div class="inner">
                    <h3 style="font-size: 1.8rem;">Rp {{ number_format($stats['biaya_proses'] ?? 0, 0, ',', '.') }}</h3>
                    <p>Estimasi Biaya Aktif</p>
                </div>
                <div class="icon"><i class="fas fa-file-invoice-dollar"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-secondary shadow-sm">
                <div class="inner">
                    <h3 style="font-size: 1.8rem;">Rp {{ number_format($stats['biaya_selesai'] ?? 0, 0, ',', '.') }}</h3>
                    <p>Total Biaya Historis</p>
                </div>
                <div class="icon"><i class="fas fa-history"></i></div>
            </div>
        </div>
    </div>

    {{-- Quick Navigation --}}
    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-between">
            <a href="{{ route('manajemenasetdanasrama.kerusakan.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-arrow-left"></i> Kembali ke Kerusakan
            </a>
            <a href="{{ route('manajemenasetdanasrama.aset.index') }}" class="btn btn-outline-success shadow-sm">
                Ke Master Aset <i class="fas fa-check"></i>
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
            <x-card title="Daftar Pemeliharaan Aset (Sedang Proses)" icon="fas fa-wrench">
                <x-slot name="tools">
                    <a href="{{ route('manajemenasetdanasrama.pemeliharaan.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus mr-1"></i> Tambah Pemeliharaan
                    </a>
                </x-slot>

                <div class="table-responsive">
                    <table class="table table-hover align-middle table-striped mb-0" style="width: 100%; min-width: 1000px;">
                        <thead class="bg-light text-muted small text-uppercase" style="letter-spacing: 0.5px;">
                            <tr>
                                <th style="width: 4%;" class="text-center py-3 border-top-0">No</th>
                                <th style="width: 20%;" class="py-3 border-top-0">Aset</th>
                                <th style="width: 12%;" class="py-3 border-top-0 text-center">Tgl Mulai</th>
                                <th style="width: 25%;" class="py-3 border-top-0">Deskripsi & Catatan</th>
                                <th style="width: 12%;" class="py-3 border-top-0 text-right">Biaya</th>
                                <th style="width: 12%;" class="py-3 border-top-0 text-center">Status</th>
                                <th style="width: 15%;" class="py-3 border-top-0 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pemeliharaan as $item)
                            <tr>
                                <td class="text-center">{{ $loop->iteration + ($pemeliharaan->currentPage() - 1) * $pemeliharaan->perPage() }}</td>
                                <td>
                                    <strong>{{ $item->aset->nama_aset ?? '-' }}</strong>
                                    <br><small class="text-muted"><code>{{ $item->aset->kode_aset ?? '' }}</code></small>
                                </td>
                                <td class="text-center small" style="white-space: nowrap;">
                                    <span class="badge badge-light border">
                                        <i class="far fa-calendar-alt mr-1"></i> {{ $item->tanggal_pemeliharaan ? $item->tanggal_pemeliharaan->format('d/m/Y') : ($item->tanggal_mulai_pemeliharaan ? $item->tanggal_mulai_pemeliharaan->format('d/m/Y') : '-') }}
                                    </span>
                                </td>
                                <td>
                                    {{ Str::limit($item->deskripsi_pemeliharaan, 50) }}
                                    @if($item->catatan)
                                        <br><small class="text-muted"><i class="fas fa-comment-alt mr-1"></i> {{ Str::limit($item->catatan, 30) }}</small>
                                    @endif
                                </td>
                                <td class="text-right font-weight-bold text-success" style="white-space: nowrap;">Rp {{ number_format($item->biaya ?? $item->biaya_pemeliharaan, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <span class="badge badge-warning">
                                        <i class="fas fa-cog fa-spin mr-1"></i> Proses
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center" style="gap: 4px;">
                                        @if($item->aset_id)
                                        <a href="{{ route('manajemenasetdanasrama.aset.show', $item->aset_id) }}" class="btn btn-action-xs btn-info" title="Detail Aset">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endif
                                        <button type="button" class="btn btn-action-xs btn-success"
                                                data-toggle="modal"
                                                data-target="#modalSelesai"
                                                data-id="{{ $item->id }}"
                                                data-nama="{{ $item->aset->nama_aset ?? '' }}"
                                                data-deskripsi="{{ $item->deskripsi_pemeliharaan }}"
                                                data-biaya="{{ number_format($item->biaya ?? $item->biaya_pemeliharaan, 0, ',', '.') }}"
                                                title="Selesai Diperbaiki">
                                            <i class="fas fa-check-double"></i>
                                        </button>
                                        <a href="{{ route('manajemenasetdanasrama.pemeliharaan.edit', $item->id) }}" class="btn btn-action-xs btn-warning" title="Edit">
                                            <i class="fas fa-edit text-white"></i>
                                        </a>
                                        <button type="button" class="btn btn-action-xs btn-danger"
                                                data-toggle="modal"
                                                data-target="#modalHapus"
                                                data-id="{{ $item->id }}"
                                                data-nama="{{ $item->aset->nama_aset ?? '' }}"
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i> Tidak ada pemeliharaan yang sedang berjalan
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($pemeliharaan->hasPages())
                <div class="d-flex justify-content-end mt-3">
                    {{ $pemeliharaan->links() }}
                </div>
                @endif

                <x-slot name="footer">
                    <small class="text-muted">Total data: {{ $pemeliharaan->total() }}</small>
                </x-slot>
            </x-card>
        </div>
    </div>
</div>

{{-- MODAL SELESAI DIPERBAIKI --}}
<div class="modal fade" id="modalSelesai" tabindex="-1" role="dialog" aria-labelledby="modalSelesaiLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formSelesai" action="" method="POST">
                @csrf
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white" id="modalSelesaiLabel">
                        <i class="fas fa-check-circle mr-1"></i> Selesai Diperbaiki
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong><i class="fas fa-info-circle"></i> Ringkasan Pemeliharaan:</strong><br>
                        Aset: <strong id="selesai_nama_aset"></strong><br>
                        Deskripsi: <span id="selesai_deskripsi"></span><br>
                        Biaya: <span id="selesai_biaya"></span>
                    </div>

                    <p>Perbaikan aset ini sudah selesai. Isi detail penyelesaian:</p>

                    <div class="form-group">
                        <label for="tanggal_selesai">Tanggal Selesai <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="catatan_selesai">Catatan Hasil Perbaikan</label>
                        <textarea class="form-control" id="catatan_selesai" name="catatan_selesai" rows="3" placeholder="Contoh: Sudah diganti spare part, kondisi normal kembali..."></textarea>
                    </div>

                    <div class="alert alert-warning mt-3 mb-0">
                        <small><i class="fas fa-exclamation-triangle"></i> Setelah dikonfirmasi, data akan hilang dari daftar pemeliharaan dan status aset diubah menjadi <strong>"Sudah Diperbaiki"</strong>.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check mr-1"></i> Konfirmasi Selesai
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL HAPUS --}}
<div class="modal fade" id="modalHapus" tabindex="-1" role="dialog" aria-labelledby="modalHapusLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formHapus" action="" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white" id="modalHapusLabel">Hapus Pemeliharaan</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus data pemeliharaan untuk aset <strong id="hapus_nama"></strong>?</p>
                    <div class="form-group">
                        <label for="alasan_hapus">Alasan Penghapusan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="alasan_hapus" name="alasan_hapus" rows="3" placeholder="Masukkan alasan penghapusan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Modal Selesai
        $('#modalSelesai').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var nama = button.data('nama');
            var deskripsi = button.data('deskripsi');
            var biaya = button.data('biaya');

            var modal = $(this);
            modal.find('#selesai_nama_aset').text(nama);
            modal.find('#selesai_deskripsi').text(deskripsi);
            modal.find('#selesai_biaya').text('Rp ' + biaya);

            var url = '{{ route("manajemenasetdanasrama.pemeliharaan.selesai", ":id") }}';
            url = url.replace(':id', id);
            modal.find('#formSelesai').attr('action', url);
        });

        // Modal Hapus
        $('#modalHapus').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var nama = button.data('nama');

            var modal = $(this);
            modal.find('#hapus_nama').text(nama);
            modal.find('#alasan_hapus').val('');

            var url = '{{ route("manajemenasetdanasrama.pemeliharaan.destroy", ":id") }}';
            url = url.replace(':id', id);
            modal.find('#formHapus').attr('action', url);
        });
    });
</script>
@endpush
