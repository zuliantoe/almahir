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
            <li class="breadcrumb-item"><a href="{{ route('manajemenasetdanasrama.kamar.index') }}">Data Kamar</a></li>
            <li class="breadcrumb-item active">Detail Kamar</li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        {{-- Detail Kamar --}}
        <div class="col-md-4">
            <x-card title="Informasi Kamar" icon="fas fa-info-circle" class="card-primary card-outline">
                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Nama Kamar</b> <span class="float-right">{{ $kamar->nama_kamar }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Kapasitas Total</b> <span class="float-right">{{ $kamar->kapasitas }} orang</span>
                    </li>
                    <li class="list-group-item">
                        <b>Terisi</b> <span class="float-right">{{ $kamar->terisi }} orang</span>
                    </li>
                    <li class="list-group-item">
                        <b>Sisa Slot</b> <span class="float-right">{{ $kamar->sisa }} orang</span>
                    </li>
                    <li class="list-group-item">
                        <b>Status</b> <span class="float-right">{!! $kamar->status_kapasitas_badge !!}</span>
                    </li>
                </ul>

                <strong><i class="fas fa-file-alt mr-1"></i> Deskripsi</strong>
                <p class="text-muted">
                    {{ $kamar->deskripsi ?: 'Tidak ada deskripsi tambahan.' }}
                </p>

                <div class="mt-4">
                    <a href="{{ route('manajemenasetdanasrama.kamar.index') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                    </a>
                </div>
            </x-card>
        </div>

        {{-- Data Penghuni --}}
        <div class="col-md-8">
            {{-- Penghuni Aktif --}}
            <x-card title="Penghuni Saat Ini" icon="fas fa-users" class="card-success card-outline">
                <x-slot name="tools">
                    <a href="{{ route('manajemenasetdanasrama.penghuni.create', ['kamar_id' => $kamar->id]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-user-plus"></i> Tambah Penghuni
                    </a>
                </x-slot>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>Nama Siswa</th>
                                <th>Tanggal Masuk</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($penghuniAktif as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $item->siswa->nama ?? '-' }}</strong></td>
                                <td>{{ $item->tanggal_masuk ? $item->tanggal_masuk->format('d/m/Y') : '-' }}</td>
                                <td>{{ Str::limit($item->keterangan ?? '-', 50) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    <i class="fas fa-inbox"></i> Belum ada penghuni aktif
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>

            {{-- Riwayat Penghuni --}}
            <x-card title="Riwayat Penghuni Sebelumnya" icon="fas fa-history" class="card-secondary card-outline collapsed-card">
                <x-slot name="tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-plus"></i>
                    </button>
                </x-slot>

                <div class="table-responsive" style="display: none;">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>Nama Siswa</th>
                                <th>Masuk</th>
                                <th>Keluar</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($riwayatPenghuni as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->siswa->nama ?? '-' }}</td>
                                <td>{{ $item->tanggal_masuk ? $item->tanggal_masuk->format('d/m/Y') : '-' }}</td>
                                <td>{{ $item->tanggal_keluar ? $item->tanggal_keluar->format('d/m/Y') : '-' }}</td>
                                <td>{{ Str::limit($item->keterangan ?? '-', 30) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    <i class="fas fa-inbox"></i> Belum ada riwayat penghuni
                                </td>
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
