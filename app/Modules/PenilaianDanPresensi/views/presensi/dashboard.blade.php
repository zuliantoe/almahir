@extends('layouts.app')

@section('title', 'Dashboard Presensi')

@section('content')
<div class="container-fluid">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="m-0"><i class="fas fa-calendar-check text-info mr-2"></i>Dashboard Presensi</h3>
            <small class="text-muted">Monitoring kehadiran siswa</small>
        </div>
        <a href="{{ route('penilaiandanpresensi.presensi.create') }}" class="btn btn-info">
            <i class="fas fa-plus mr-1"></i> Tambah Presensi
        </a>
    </div>

    {{-- Today's Statistics --}}
    <div class="row mb-4">
        <div class="col-md-2.4">
            <div class="info-box bg-success">
                <span class="info-box-icon"><i class="fas fa-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Hadir Hari Ini</span>
                    <span class="info-box-number">{{ $todayHadir ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-2.4">
            <div class="info-box bg-warning">
                <span class="info-box-icon"><i class="fas fa-hand-paper"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Izin Hari Ini</span>
                    <span class="info-box-number">{{ $todayIzin ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-2.4">
            <div class="info-box bg-info">
                <span class="info-box-icon"><i class="fas fa-heartbeat"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Sakit Hari Ini</span>
                    <span class="info-box-number">{{ $todaySakit ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-2.4">
            <div class="info-box bg-danger">
                <span class="info-box-icon"><i class="fas fa-times"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Alpha Hari Ini</span>
                    <span class="info-box-number">{{ $todayAlpha ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-2.4">
            <div class="info-box bg-primary">
                <span class="info-box-icon"><i class="fas fa-list"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Presensi</span>
                    <span class="info-box-number">{{ $presensiCount ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Content Row --}}
    <div class="row">
        {{-- Left Column: Recent Presensi --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header border-0">
                    <h3 class="card-title">
                        <i class="fas fa-list-ul mr-2"></i> Presensi Terbaru
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('penilaiandanpresensi.presensi.index') }}" class="btn btn-sm btn-info">
                            <i class="fas fa-eye mr-1"></i> Lihat Semua
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Siswa</th>
                                    <th>Guru</th>
                                    <th>Jam</th>
                                    <th>Status</th>
                                    <th>Kategori</th>
                                    <th>Tanggal</th>
                                    <th style="width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPresensi as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->siswa->nama ?? '-' }}</strong>
                                    </td>
                                    <td>{{ $item->guru->nama ?? '-' }}</td>
                                    <td>
                                        <small><i class="fas fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($item->jam)->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        @if($item->status == 'Hadir')
                                            <span class="badge badge-success"><i class="fas fa-check mr-1"></i>{{ $item->status }}</span>
                                        @elseif($item->status == 'Izin')
                                            <span class="badge badge-warning"><i class="fas fa-hand-paper mr-1"></i>{{ $item->status }}</span>
                                        @elseif($item->status == 'Sakit')
                                            <span class="badge badge-info"><i class="fas fa-heartbeat mr-1"></i>{{ $item->status }}</span>
                                        @else
                                            <span class="badge badge-danger"><i class="fas fa-times mr-1"></i>{{ $item->status }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary">{{ $item->kategori }}</span>
                                    </td>
                                    <td>
                                        <small>{{ $item->created_at?->locale('id')->translatedFormat('d M') }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('penilaiandanpresensi.presensi.edit', $item->id) }}" class="btn btn-xs btn-info" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-3">Belum ada data presensi</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Statistics --}}
        <div class="col-lg-4">
            {{-- Attendance Summary This Week --}}
            <div class="card">
                <div class="card-header border-0">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie mr-2"></i> Ringkasan Minggu Ini
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="p-3">
                        @php
                            $weekTotal = ($weekHadir ?? 0) + ($weekIzin ?? 0) + ($weekSakit ?? 0) + ($weekAlpha ?? 0);
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span><i class="fas fa-check text-success mr-2"></i>Hadir</span>
                                <span>{{ $weekHadir ?? 0 }}</span>
                            </div>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $weekTotal > 0 ? (($weekHadir ?? 0)/$weekTotal)*100 : 0 }}%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span><i class="fas fa-hand-paper text-warning mr-2"></i>Izin</span>
                                <span>{{ $weekIzin ?? 0 }}</span>
                            </div>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $weekTotal > 0 ? (($weekIzin ?? 0)/$weekTotal)*100 : 0 }}%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span><i class="fas fa-heartbeat text-info mr-2"></i>Sakit</span>
                                <span>{{ $weekSakit ?? 0 }}</span>
                            </div>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-info" role="progressbar" style="width: {{ $weekTotal > 0 ? (($weekSakit ?? 0)/$weekTotal)*100 : 0 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between mb-1">
                                <span><i class="fas fa-times text-danger mr-2"></i>Alpha</span>
                                <span>{{ $weekAlpha ?? 0 }}</span>
                            </div>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $weekTotal > 0 ? (($weekAlpha ?? 0)/$weekTotal)*100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
