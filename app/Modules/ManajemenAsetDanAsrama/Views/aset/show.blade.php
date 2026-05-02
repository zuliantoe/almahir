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
<div class="container-fluid">
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
                        <td>{{ $aset->nama_aset }}</td>
                    </tr>
                    <tr>
                        <th>Harga</th>
                        <td>Rp {{ number_format($aset->harga, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Status Kondisi</th>
                        <td>
                            @if($aset->status_kondisi == 'baik')
                                <span class="badge badge-success">Baik</span>
                            @elseif($aset->status_kondisi == 'rusak')
                                <span class="badge badge-danger">Rusak</span>
                            @elseif($aset->status_kondisi == 'dalam_perbaikan')
                                <span class="badge badge-warning">Dalam Perbaikan</span>
                            @elseif($aset->status_kondisi == 'sudah_diperbaiki')
                                <span class="badge badge-info">Sudah Diperbaiki</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Kondisi</th>
                        <td>{{ $aset->kondisi ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Deskripsi</th>
                        <td>{{ $aset->deskripsi_aset ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Pengajuan</th>
                        <td>{{ $aset->tanggal_pengajuan ? $aset->tanggal_pengajuan->format('d/m/Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Pengadaan</th>
                        <td>{{ $aset->tanggal_pengadaan ? $aset->tanggal_pengadaan->format('d/m/Y') : '-' }}</td>
                    </tr>
                </table>
                <x-slot name="footer">
                    <a href="{{ route('manajemenasetdanasrama.aset.edit', $aset->id) }}" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit mr-1"></i> Edit Aset
                    </a>
                    <a href="{{ route('manajemenasetdanasrama.aset.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
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
                        <th>Status</th>
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
                    <tr>
                        <th>Catatan</th>
                        <td>{{ $aset->pengadaan->catatan_pengadaan ?? '-' }}</td>
                    </tr>
                </table>
                @else
                <p class="text-muted"><i class="fas fa-info-circle"></i> Tidak ada data pengadaan terkait</p>
                @endif
            </x-card>
        </div>
    </div>

    {{-- Riwayat Kerusakan & Pemeliharaan --}}
    <div class="row mt-3">
        {{-- Riwayat Kerusakan --}}
        <div class="col-md-12 mb-4">
            <x-card title="Riwayat Kerusakan" icon="fas fa-exclamation-triangle">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th width="120">Tanggal</th>
                                <th>Deskripsi Kerusakan</th>
                                <th width="150">Tingkat Kerusakan</th>
                                <th width="150">Status Penanganan</th>
                                <th>Catatan Tambahan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($aset->kerusakan as $kerusakan)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $kerusakan->tanggal_kerusakan ? \Carbon\Carbon::parse($kerusakan->tanggal_kerusakan)->format('d/m/Y') : ($kerusakan->tanggal_rusak ? \Carbon\Carbon::parse($kerusakan->tanggal_rusak)->format('d/m/Y') : '-') }}</td>
                                <td>{{ $kerusakan->deskripsi_kerusakan ?? '-' }}</td>
                                <td>
                                    @if($kerusakan->tingkat_kerusakan == 'ringan')
                                        <span class="badge badge-info">Ringan</span>
                                    @elseif($kerusakan->tingkat_kerusakan == 'sedang')
                                        <span class="badge badge-warning">Sedang</span>
                                    @elseif($kerusakan->tingkat_kerusakan == 'berat')
                                        <span class="badge badge-danger">Berat</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($kerusakan->status_penanganan == 'belum_ditangani')
                                        <span class="badge badge-danger">Belum Ditangani</span>
                                    @elseif($kerusakan->status_penanganan == 'sedang_ditangani')
                                        <span class="badge badge-warning">Sedang Diperbaiki</span>
                                    @elseif($kerusakan->status_penanganan == 'selesai')
                                        <span class="badge badge-success">Selesai</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $kerusakan->catatan ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Tidak ada riwayat kerusakan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>

        {{-- Riwayat Pemeliharaan --}}
        <div class="col-md-12">
            <x-card title="Riwayat Pemeliharaan" icon="fas fa-wrench">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th width="120">Tanggal Mulai</th>
                                <th width="120">Tanggal Selesai</th>
                                <th>Deskripsi & Catatan Awal</th>
                                <th>Catatan Penyelesaian</th>
                                <th width="150">Biaya</th>
                                <th width="120">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($aset->pemeliharaan as $pelihara)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $pelihara->tanggal_pemeliharaan ? \Carbon\Carbon::parse($pelihara->tanggal_pemeliharaan)->format('d/m/Y') : ($pelihara->tanggal_mulai_pemeliharaan ? \Carbon\Carbon::parse($pelihara->tanggal_mulai_pemeliharaan)->format('d/m/Y') : '-') }}</td>
                                <td>{{ $pelihara->tanggal_selesai_pemeliharaan ? \Carbon\Carbon::parse($pelihara->tanggal_selesai_pemeliharaan)->format('d/m/Y') : '-' }}</td>
                                <td>
                                    <strong>Deskripsi:</strong> {{ $pelihara->deskripsi_pemeliharaan ?? '-' }}<br>
                                    @if($pelihara->catatan)
                                    <small class="text-muted"><strong>Catatan:</strong> {{ $pelihara->catatan }}</small>
                                    @endif
                                </td>
                                <td>{{ $pelihara->catatan_selesai ?? '-' }}</td>
                                <td>Rp {{ number_format($pelihara->biaya ?? $pelihara->biaya_pemeliharaan ?? 0, 0, ',', '.') }}</td>
                                <td>
                                    @if($pelihara->status == 'proses' || is_null($pelihara->status))
                                        <span class="badge badge-warning">Proses</span>
                                    @elseif($pelihara->status == 'selesai')
                                        <span class="badge badge-success">Selesai</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">Tidak ada riwayat pemeliharaan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
