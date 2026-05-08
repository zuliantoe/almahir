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
<style>
    .dashboard-header {
        background: linear-gradient(135deg, #1e3799 0%, #0984e3 100%);
        color: white;
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        position: relative;
        overflow: hidden;
    }
    .dashboard-header::after {
        content: '\f1ad';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        position: absolute;
        right: -20px;
        bottom: -20px;
        font-size: 150px;
        opacity: 0.1;
    }
    .stat-card {
        border: none;
        border-radius: 15px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
    }
    .stat-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 1.5rem;
        margin-bottom: 15px;
    }
    .quick-menu-card {
        background: white;
        border: 1px solid #edf2f7;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #2d3436;
    }
    .quick-menu-card:hover {
        background: #f8faff;
        border-color: #0984e3;
        color: #0984e3;
        transform: scale(1.03);
    }
    .quick-menu-card i {
        font-size: 2rem;
        margin-bottom: 12px;
    }
    .quick-menu-card span {
        font-weight: 600;
        font-size: 0.9rem;
    }
    .table-clean thead th {
        background: #f8f9fa;
        border-top: none;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .badge-soft {
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 500;
    }
</style>

<div class="container-fluid">
    {{-- Welcome Section --}}
    <div class="dashboard-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="font-weight-bold mb-1">Assalamu'alaikum, Admin!</h2>
                <p class="mb-0 opacity-75">Selamat datang di Pusat Manajemen Aset & Asrama Santri Al-Mahir.</p>
            </div>
            <div class="col-md-4 text-right d-none d-md-block">
                <div class="h1 mb-0"><i class="fas fa-university"></i></div>
            </div>
        </div>
    </div>

    {{-- Stats Grid Row 1 --}}
    <div class="row">
        {{-- Total Aset --}}
        <div class="col-md-3 col-6 mb-4">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body">
                    <div class="stat-icon text-info" style="background: #e3f2fd;">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <h6 class="text-muted mb-1 small uppercase font-weight-bold">Total Aset</h6>
                    <h3 class="font-weight-bold mb-0 text-dark">{{ $totalAset }}</h3>
                </div>
            </div>
        </div>
        {{-- Total Kamar --}}
        <div class="col-md-3 col-6 mb-4">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body">
                    <div class="stat-icon text-primary" style="background: #eef2ff;">
                        <i class="fas fa-door-open"></i>
                    </div>
                    <h6 class="text-muted mb-1 small uppercase font-weight-bold">Total Kamar</h6>
                    <h3 class="font-weight-bold mb-0 text-dark">{{ $totalKamar }}</h3>
                </div>
            </div>
        </div>
        {{-- Total Penghuni --}}
        <div class="col-md-3 col-6 mb-4">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body">
                    <div class="stat-icon text-success" style="background: #e8f5e9;">
                        <i class="fas fa-users"></i>
                    </div>
                    <h6 class="text-muted mb-1 small uppercase font-weight-bold">Total Penghuni</h6>
                    <h3 class="font-weight-bold mb-0 text-dark">{{ $totalPenghuni }}</h3>
                </div>
            </div>
        </div>
        {{-- Sisa Kapasitas --}}
        <div class="col-md-3 col-6 mb-4">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body">
                    <div class="stat-icon text-warning" style="background: #fff8e1;">
                        <i class="fas fa-bed"></i>
                    </div>
                    <h6 class="text-muted mb-1 small uppercase font-weight-bold">Sisa Kasur</h6>
                    <h3 class="font-weight-bold mb-0 text-dark">{{ $sisaKapasitas }} <small class="text-muted" style="font-size: 0.8rem;">/ {{ $totalKapasitas }}</small></h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Grid Row 2 --}}
    <div class="row">
        {{-- Pengajuan --}}
        <div class="col-md-3 col-6 mb-4">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body">
                    <div class="stat-icon text-purple" style="background: #f3e5f5;">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <h6 class="text-muted mb-1 small uppercase font-weight-bold">Pengajuan Aset</h6>
                    <h3 class="font-weight-bold mb-0 text-dark">{{ $totalPengajuan }}</h3>
                </div>
            </div>
        </div>
        {{-- Kerusakan --}}
        <div class="col-md-3 col-6 mb-4">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body">
                    <div class="stat-icon text-danger" style="background: #ffebee;">
                        <i class="fas fa-tools"></i>
                    </div>
                    <h6 class="text-muted mb-1 small uppercase font-weight-bold">Aset Rusak</h6>
                    <h3 class="font-weight-bold mb-0 text-dark">{{ $totalKerusakan }}</h3>
                </div>
            </div>
        </div>
        {{-- Pemeliharaan --}}
        <div class="col-md-3 col-6 mb-4">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body">
                    <div class="stat-icon text-info" style="background: #e1f5fe;">
                        <i class="fas fa-wrench"></i>
                    </div>
                    <h6 class="text-muted mb-1 small uppercase font-weight-bold">Dalam Perbaikan</h6>
                    <h3 class="font-weight-bold mb-0 text-dark">{{ $totalPemeliharaan }}</h3>
                </div>
            </div>
        </div>
        {{-- Kamar Penuh --}}
        <div class="col-md-3 col-6 mb-4">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body">
                    <div class="stat-icon text-success" style="background: #e8f5e9;">
                        <i class="fas fa-door-closed"></i>
                    </div>
                    <h6 class="text-muted mb-1 small uppercase font-weight-bold">Kamar Penuh</h6>
                    <h3 class="font-weight-bold mb-0 text-dark">{{ $kamarPenuh }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Navigation Menu --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="card-title font-weight-bold"><i class="fas fa-th-large mr-2 text-primary"></i> Menu Navigasi Cepat</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row no-gutters" style="margin: -5px;">
                        {{-- Aset Menu --}}
                        <div class="col-md-3 col-6 p-1">
                            <a href="{{ route('manajemenasetdanasrama.aset.index') }}" class="text-decoration-none">
                                <div class="quick-menu-card">
                                    <i class="fas fa-cubes text-info"></i>
                                    <span>Master Aset</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-6 p-1">
                            <a href="{{ route('manajemenasetdanasrama.kamar.index') }}" class="text-decoration-none">
                                <div class="quick-menu-card">
                                    <i class="fas fa-bed text-primary"></i>
                                    <span>Kamar Asrama</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-6 p-1">
                            <a href="{{ route('manajemenasetdanasrama.penghuni.index') }}" class="text-decoration-none">
                                <div class="quick-menu-card">
                                    <i class="fas fa-user-friends text-success"></i>
                                    <span>Daftar Penghuni</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-6 p-1">
                            <a href="{{ route('manajemenasetdanasrama.jadwal-piket.index') }}" class="text-decoration-none">
                                <div class="quick-menu-card">
                                    <i class="fas fa-clipboard-list text-warning"></i>
                                    <span>Jadwal Piket</span>
                                </div>
                            </a>
                        </div>
                        {{-- Row 2 --}}
                        <div class="col-md-3 col-6 p-1">
                            <a href="{{ route('manajemenasetdanasrama.pengajuan.index') }}" class="text-decoration-none">
                                <div class="quick-menu-card">
                                    <i class="fas fa-file-invoice text-purple" style="color: #6c5ce7;"></i>
                                    <span>Pengajuan</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-6 p-1">
                            <a href="{{ route('manajemenasetdanasrama.kerusakan.index') }}" class="text-decoration-none">
                                <div class="quick-menu-card">
                                    <i class="fas fa-tools text-danger"></i>
                                    <span>Perbaikan</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-6 p-1">
                            <a href="{{ route('manajemenasetdanasrama.trash.index') }}" class="text-decoration-none">
                                <div class="quick-menu-card">
                                    <i class="fas fa-trash-alt text-secondary"></i>
                                    <span>Tong Sampah</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-6 p-1">
                            <a href="{{ route('manajemenasetdanasrama.persetujuan.index') }}" class="text-decoration-none">
                                <div class="quick-menu-card">
                                    <i class="fas fa-check-circle text-success"></i>
                                    <span>Approval</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Side: Piket Today --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="card-title font-weight-bold mb-0"><i class="fas fa-broom mr-2 text-warning"></i> Piket Hari Ini</h5>
                    <a href="{{ route('manajemenasetdanasrama.jadwal-piket.index') }}" class="btn btn-xs btn-link text-primary">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-clean mb-0">
                            <thead>
                                <tr>
                                    <th class="pl-4">Nama Santri</th>
                                    <th>Kamar</th>
                                    <th class="pr-4 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($jadwalPiketHariIni as $item)
                                <tr>
                                    <td class="pl-4 py-3">
                                        <div class="font-weight-bold small">{{ $item->siswa->nama ?? '-' }}</div>
                                    </td>
                                    <td class="py-3">
                                        <span class="badge bg-light border text-muted small">{{ $item->kamar->nama_kamar ?? '-' }}</span>
                                    </td>
                                    <td class="pr-4 py-3 text-center">
                                        @if($item->status == 'belum')
                                            <i class="fas fa-clock text-warning" title="Belum Selesai"></i>
                                        @else
                                            <i class="fas fa-check-circle text-success" title="Selesai"></i>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5">
                                        <div class="text-muted small">
                                            <i class="fas fa-mug-hot d-block h1 mb-2 opacity-25"></i>
                                            Tidak ada jadwal piket hari ini
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection