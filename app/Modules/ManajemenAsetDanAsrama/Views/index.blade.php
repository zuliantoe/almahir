@extends('layouts.app')

@section('title', $title)

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0">{{ $title }}</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item active">Manajemen Aset & Asrama</li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    {{-- Alert Messages --}}
    @if(session('success'))
        <x-alert type="success" :message="session('success')" dismissible />
    @endif
    @if(session('error'))
        <x-alert type="danger" :message="session('error')" dismissible />
    @endif

    {{-- Info Boxes Row 1: Aset --}}
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-boxes"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Aset</span>
                    <span class="info-box-number">{{ $totalAset }}</span>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-file-alt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Pengajuan</span>
                    <span class="info-box-number">{{ $totalPengajuan }}</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-truck"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Pengadaan</span>
                    <span class="info-box-number">{{ $totalPengadaan }}</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-tools"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Kerusakan</span>
                    <span class="info-box-number">{{ $totalKerusakan }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Info Boxes Row 2: Asrama --}}
    <div class="row">
        <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-door-open"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Kamar</span>
                    <span class="info-box-number">{{ $totalKamar }}</span>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-secondary elevation-1"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Penghuni</span>
                    <span class="info-box-number">{{ $totalPenghuni }}</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-purple elevation-1"><i class="fas fa-calendar-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Jadwal Piket Hari Ini</span>
                    <span class="info-box-number">{{ $jadwalPiketHariIni->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="row">
        <div class="col-md-12">
            <x-card title="Aksi Cepat" icon="fas fa-bolt">
                <div class="row">
                    <div class="col-md-2 col-6 mb-2">
                        <a href="{{ route('manajemenasetdanasrama.pengajuan.index') }}" class="btn btn-outline-warning btn-block">
                            <i class="fas fa-file-alt"></i> Pengajuan
                        </a>
                    </div>
                    <div class="col-md-2 col-6 mb-2">
                        <a href="{{ route('manajemenasetdanasrama.persetujuan.index') }}" class="btn btn-outline-info btn-block">
                            <i class="fas fa-check-double"></i> Persetujuan
                        </a>
                    </div>
                    <div class="col-md-2 col-6 mb-2">
                        <a href="{{ route('manajemenasetdanasrama.pengadaan.index') }}" class="btn btn-outline-success btn-block">
                            <i class="fas fa-truck"></i> Pengadaan
                        </a>
                    </div>
                    <div class="col-md-2 col-6 mb-2">
                        <a href="{{ route('manajemenasetdanasrama.aset.index') }}" class="btn btn-outline-primary btn-block">
                            <i class="fas fa-boxes"></i> Master Aset
                        </a>
                    </div>
                    <div class="col-md-2 col-6 mb-2">
                        <a href="{{ route('manajemenasetdanasrama.kamar.index') }}" class="btn btn-outline-secondary btn-block">
                            <i class="fas fa-door-open"></i> Kamar
                        </a>
                    </div>
                    <div class="col-md-2 col-6 mb-2">
                        <a href="{{ route('manajemenasetdanasrama.jadwal-piket.index') }}" class="btn btn-outline-purple btn-block">
                            <i class="fas fa-calendar-alt"></i> Jadwal Piket
                        </a>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-2 col-6 mb-2">
                        <a href="{{ route('manajemenasetdanasrama.penghuni.index') }}" class="btn btn-outline-dark btn-block">
                            <i class="fas fa-users"></i> Penghuni
                        </a>
                    </div>
                    <div class="col-md-2 col-6 mb-2">
                        <a href="{{ route('manajemenasetdanasrama.kerusakan.index') }}" class="btn btn-outline-danger btn-block">
                            <i class="fas fa-exclamation-triangle"></i> Kerusakan
                        </a>
                    </div>
                    <div class="col-md-2 col-6 mb-2">
                        <a href="{{ route('manajemenasetdanasrama.pemeliharaan.index') }}" class="btn btn-outline-info btn-block">
                            <i class="fas fa-wrench"></i> Pemeliharaan
                        </a>
                    </div>
                    <div class="col-md-2 col-6 mb-2">
                        <a href="{{ route('manajemenasetdanasrama.trash.index') }}" class="btn btn-outline-secondary btn-block">
                            <i class="fas fa-trash-restore"></i> Trash
                        </a>
                    </div>
                </div>
            </x-card>
        </div>
    </div>



    {{-- Jadwal Piket Hari Ini --}}
    <div class="row mt-3">
        <div class="col-md-12">
            <x-card title="Jadwal Piket Hari Ini" icon="fas fa-calendar-check">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Tempat</th>
                                <th>Siswa</th>
                                <th>Pekan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jadwalPiketHariIni as $item)
                            <tr>
                                <td>{{ $item->tempat ?? '-' }}</td>
                                <td>{{ $item->siswa->nama ?? '-' }}</td>
                                <td>Pekan {{ $item->pekan }}</td>
                                <td>
                                    @if($item->status == 'belum')
                                        <span class="badge badge-warning">Belum</span>
                                    @else
                                        <span class="badge badge-success">Sudah</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    <i class="fas fa-inbox"></i> Tidak ada jadwal piket hari ini
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <x-slot name="footer">
                    <a href="{{ route('manajemenasetdanasrama.jadwal-piket.index') }}" class="btn btn-sm btn-primary">
                        Kelola Jadwal <i class="fas fa-arrow-right"></i>
                    </a>
                </x-slot>
            </x-card>
        </div>
    </div>
</div>
@endsection