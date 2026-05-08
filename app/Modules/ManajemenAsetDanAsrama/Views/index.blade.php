@extends('layouts.app')

@section('title', $title)

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0 text-dark font-weight-bold">{{ $title }}</h1>
    </div>
    <div class="col-sm-6 text-right">
        <span class="badge badge-light border px-3 py-2">
            <i class="fas fa-calendar-alt mr-1"></i> {{ now()->translatedFormat('l, d F Y') }}
        </span>
    </div>
</div>
@endsection

@section('content')
<style>
    .stat-card {
        border: none;
        border-radius: 12px;
        transition: all 0.3s ease;
        overflow: hidden;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
    }
    .stat-icon {
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        font-size: 1.1rem;
    }
    .bg-gray-light {
        background-color: #f8f9fa;
    }
    .masonry-wrapper {
        column-count: 2;
        column-gap: 20px;
    }
    .masonry-item {
        display: inline-block;
        width: 100%;
        margin-bottom: 20px;
        break-inside: avoid;
    }
    @media (max-width: 768px) {
        .masonry-wrapper {
            column-count: 1;
        }
    }
</style>

<div class="container-fluid">
    {{-- Quick Information - Row 1: ASET --}}
    <div class="row mb-2">
        {{-- Total Aset --}}
        <div class="col-md-3 col-6 mb-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="stat-icon text-primary mr-3" style="background: #e7f1ff;">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0 small font-weight-bold text-uppercase">Total Aset</h6>
                        <h4 class="font-weight-bold mb-0 text-dark">{{ $totalAset }}</h4>
                    </div>
                </div>
            </div>
        </div>
        {{-- Pengajuan --}}
        <div class="col-md-3 col-6 mb-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="stat-icon mr-3" style="background: #f3e5f5; color: #6c5ce7;">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0 small font-weight-bold text-uppercase">Pengajuan</h6>
                        <h4 class="font-weight-bold mb-0 text-dark">{{ $totalPengajuan }}</h4>
                    </div>
                </div>
            </div>
        </div>
        {{-- Kerusakan --}}
        <div class="col-md-3 col-6 mb-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="stat-icon text-danger mr-3" style="background: #ffebee;">
                        <i class="fas fa-tools"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0 small font-weight-bold text-uppercase">Aset Rusak</h6>
                        <h4 class="font-weight-bold mb-0 text-dark">{{ $totalKerusakan }}</h4>
                    </div>
                </div>
            </div>
        </div>
        {{-- Pemeliharaan --}}
        <div class="col-md-3 col-6 mb-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="stat-icon text-secondary mr-3" style="background: #f5f5f5;">
                        <i class="fas fa-wrench"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0 small font-weight-bold text-uppercase">Perbaikan</h6>
                        <h4 class="font-weight-bold mb-0 text-dark">{{ $totalPemeliharaan }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Information - Row 2: ASRAMA --}}
    <div class="row mb-2">
        {{-- Total Kamar --}}
        <div class="col-md-3 col-6 mb-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="stat-icon text-info mr-3" style="background: #e0f7fa;">
                        <i class="fas fa-door-open"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0 small font-weight-bold text-uppercase">Total Kamar</h6>
                        <h4 class="font-weight-bold mb-0 text-dark">{{ $totalKamar ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
        {{-- Total Penghuni --}}
        <div class="col-md-3 col-6 mb-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="stat-icon text-success mr-3" style="background: #e8f5e9;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0 small font-weight-bold text-uppercase">Penghuni</h6>
                        <h4 class="font-weight-bold mb-0 text-dark">{{ $totalPenghuni }}</h4>
                    </div>
                </div>
            </div>
        </div>
        {{-- Sisa Kapasitas --}}
        <div class="col-md-3 col-6 mb-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="stat-icon text-warning mr-3" style="background: #fff8e1;">
                        <i class="fas fa-bed"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0 small font-weight-bold text-uppercase">Sisa Kasur</h6>
                        <h4 class="font-weight-bold mb-0 text-dark">{{ $sisaKapasitas }}</h4>
                    </div>
                </div>
            </div>
        </div>
        {{-- Kamar Penuh --}}
        <div class="col-md-3 col-6 mb-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="stat-icon mr-3" style="background: #efebe9; color: #795548;">
                        <i class="fas fa-door-closed"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0 small font-weight-bold text-uppercase">Kamar Penuh</h6>
                        <h4 class="font-weight-bold mb-0 text-dark">{{ $kamarPenuh }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    {{-- Main Picket Schedule Grid --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header bg-dark py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 font-weight-bold text-white">
                        <i class="fas fa-calendar-check mr-2 text-warning"></i> JADWAL PIKET HARI INI
                    </h5>
                    <div class="d-flex align-items-center">
                        <span class="badge badge-warning mr-3 px-3 py-2 font-weight-bold">
                            {{ now()->translatedFormat('l, d F Y') }}
                        </span>
                        <a href="{{ route('manajemenasetdanasrama.jadwal-piket.index') }}" class="btn btn-sm btn-outline-light rounded-pill px-3">
                            Kelola Semua <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body bg-light p-4">
                    @if($jadwalToday->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-mug-hot fa-3x mb-3 opacity-25"></i>
                            <h5 class="font-weight-bold">Alhamdulillah!</h5>
                            <p>Tidak ada jadwal piket yang terdaftar untuk hari ini.</p>
                            <a href="{{ route('manajemenasetdanasrama.jadwal-piket.index') }}" class="btn btn-primary btn-sm rounded-pill px-4">
                                <i class="fas fa-plus mr-1"></i> Buat Jadwal Sekarang
                            </a>
                        </div>
                    @else
                        <div class="masonry-wrapper">
                        @foreach($jadwalToday as $location => $items)
                            <div class="masonry-item">
                                <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                                    <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center border-bottom">
                                        <h6 class="mb-0 font-weight-bold text-primary">
                                            <i class="fas fa-map-marker-alt mr-2"></i> {{ $location ?: 'Umum' }}
                                        </h6>
                                        <span class="badge badge-pill badge-light border text-muted" style="font-size: 11px;">
                                            {{ $items->count() }} Santri
                                        </span>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-sm table-hover mb-0">
                                            <thead>
                                                <tr class="bg-gray-light text-muted small uppercase">
                                                    <th width="80" class="pl-3 py-2 border-0">Shift</th>
                                                    <th class="py-2 border-0">Nama Santri</th>
                                                    <th width="100" class="text-center pr-3 py-2 border-0">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($items as $item)
                                                <tr>
                                                    <td class="align-middle pl-3 py-2">
                                                        <span class="badge badge-outline-secondary border text-capitalize px-2 py-1" style="font-size: 10px; font-weight: 500;">
                                                            {{ $item->shift }}
                                                        </span>
                                                    </td>
                                                    <td class="align-middle py-2 font-weight-bold text-dark" style="font-size: 13px;">
                                                        {{ $item->siswa->nama ?? '-' }}
                                                    </td>
                                                    <td class="text-center align-middle pr-3 py-2">
                                                        @if($item->status == 'sudah')
                                                            <span class="badge badge-success px-2 py-1" style="font-size: 9px;"><i class="fas fa-check mr-1"></i>SELESAI</span>
                                                        @else
                                                            <span class="badge badge-warning px-2 py-1" style="font-size: 9px;"><i class="fas fa-clock mr-1"></i>BELUM</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection