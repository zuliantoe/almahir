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
            <li class="breadcrumb-item"><a href="{{ route('manajemenasetdanasrama.pengadaan.index') }}">Pengadaan</a></li>
            <li class="breadcrumb-item active">Proses Pengadaan</li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    @if(session('error'))
        <x-alert type="danger" :message="session('error')" dismissible />
    @endif

    <div class="row">
        {{-- Info Pengajuan --}}
        <div class="col-md-4">
            <x-card title="Info Pengajuan" icon="fas fa-file-alt">
                <table class="table table-sm table-borderless">
                    <tr>
                        <th>Nomor Pengajuan</th>
                        <td><code>{{ $pengajuan->nomor_pengajuan }}</code></td>
                    </tr>
                    <tr>
                        <th>Nama Aset</th>
                        <td>{{ $pengajuan->nama_aset }}</td>
                    </tr>
                    <tr>
                        <th>Deskripsi</th>
                        <td>{{ $pengajuan->deskripsi_pengajuan }}</td>
                    </tr>
                    <tr>
                        <th>Estimasi Harga</th>
                        <td>Rp {{ number_format($pengajuan->estimasi_harga, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Pengaju</th>
                        <td>{{ $pengajuan->pengaju->name ?? '-' }}</td>
                    </tr>
                </table>
            </x-card>
        </div>

        {{-- Form Pengadaan --}}
        <div class="col-md-8">
            <x-card title="Form Pengadaan" icon="fas fa-truck">
                <form action="{{ route('manajemenasetdanasrama.pengadaan.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="pengajuan_id" value="{{ $pengajuan->id }}">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="vendor">Vendor / Supplier <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('vendor') is-invalid @enderror" id="vendor" name="vendor" value="{{ old('vendor') }}" required>
                                @error('vendor')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="biaya_riil">Biaya Riil (Rp) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('biaya_riil') is-invalid @enderror" id="biaya_riil" name="biaya_riil" value="{{ old('biaya_riil', $pengajuan->estimasi_harga) }}" min="0" required>
                                @error('biaya_riil')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tanggal_pesan">Tanggal Pesan <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('tanggal_pesan') is-invalid @enderror" id="tanggal_pesan" name="tanggal_pesan" value="{{ old('tanggal_pesan', date('Y-m-d')) }}" required>
                                @error('tanggal_pesan')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="estimasi_datang">Estimasi Datang <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('estimasi_datang') is-invalid @enderror" id="estimasi_datang" name="estimasi_datang" value="{{ old('estimasi_datang') }}" required>
                                @error('estimasi_datang')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="catatan_pengadaan">Catatan Pengadaan</label>
                        <textarea class="form-control @error('catatan_pengadaan') is-invalid @enderror" id="catatan_pengadaan" name="catatan_pengadaan" rows="3">{{ old('catatan_pengadaan') }}</textarea>
                        @error('catatan_pengadaan')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save mr-1"></i> Simpan Pengadaan
                        </button>
                        <a href="{{ route('manajemenasetdanasrama.pengadaan.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Sync Tanggal Pesan -> Estimasi Datang
        $('#tanggal_pesan').on('change', function() {
            var tanggalPesan = $(this).val();
            var estimasiDatangInput = $('#estimasi_datang');
            
            // Set minimal tanggal datang
            estimasiDatangInput.attr('min', tanggalPesan);
            
            // Auto fill jika masih kosong atau jika tanggal datang sebelumnya lebih kecil
            if (!estimasiDatangInput.val() || estimasiDatangInput.val() < tanggalPesan) {
                estimasiDatangInput.val(tanggalPesan);
            }
        }).trigger('change'); // Trigger saat pertama kali buka halaman
    });
</script>
@endpush
