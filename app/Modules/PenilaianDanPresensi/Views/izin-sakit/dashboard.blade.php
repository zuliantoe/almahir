@extends('layouts.app')

@section('title', 'Dashboard Izin & Sakit')

@section('content')
<div class="container-fluid">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="m-0"><i class="fas fa-user-md text-warning mr-2"></i>Dashboard Izin & Sakit</h3>
            <small class="text-muted">Monitoring pengajuan izin dan sakit siswa</small>
        </div>
        <a href="{{ route('penilaiandanpresensi.izinsakit.create') }}" class="btn btn-warning">
            <i class="fas fa-plus mr-1"></i> Tambah Pengajuan
        </a>
    </div>

    {{-- Statistics Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Pending</span>
                    <span class="info-box-number">{{ $stats['pending'] ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Disetujui</span>
                    <span class="info-box-number">{{ $stats['approved'] ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-danger"><i class="fas fa-times-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Ditolak</span>
                    <span class="info-box-number">{{ $stats['rejected'] ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-primary"><i class="fas fa-list"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total</span>
                    <span class="info-box-number">{{ $stats['total'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Content Row --}}
    <div class="row">
        {{-- Left Column: Pending Requests --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-warning border-0">
                    <h3 class="card-title">
                        <i class="fas fa-hourglass-half mr-2"></i> Pengajuan Menunggu Persetujuan
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('penilaiandanpresensi.izinsakit.index') }}" class="btn btn-sm btn-light">
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
                                    <th>Jenis</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Berakhir</th>
                                    <th>Keterangan</th>
                                    <th style="width: 150px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingRequests as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->siswa->nama ?? '-' }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $item->jenis == 'Izin' ? 'info' : 'danger' }}">
                                            {{ $item->jenis ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <small>{{ $item->tanggal_mulai?->locale('id')->translatedFormat('d M Y') }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $item->tanggal_berakhir?->locale('id')->translatedFormat('d M Y') }}</small>
                                    </td>
                                    <td>
                                        <small>{{ Str::limit($item->keterangan ?? '-', 30) }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('penilaiandanpresensi.izinsakit.edit', $item->id) }}" class="btn btn-xs btn-info" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('penilaiandanpresensi.izinsakit.show', $item->id) }}" class="btn btn-xs btn-primary" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-3">Tidak ada pengajuan yang menunggu</td>
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
            {{-- Type Distribution --}}
            <div class="card">
                <div class="card-header border-0">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar mr-2"></i> Distribusi Jenis
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="p-3">
                        @php
                            $totalIzin = $distribution['izin'] ?? 0;
                            $totalSakit = $distribution['sakit'] ?? 0;
                            $grandTotal = $totalIzin + $totalSakit;
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span><i class="fas fa-hand-paper text-info mr-2"></i>Izin</span>
                                <span>{{ $totalIzin }}</span>
                            </div>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-info" role="progressbar" style="width: {{ $grandTotal > 0 ? ($totalIzin/$grandTotal)*100 : 0 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between mb-1">
                                <span><i class="fas fa-heartbeat text-danger mr-2"></i>Sakit</span>
                                <span>{{ $totalSakit }}</span>
                            </div>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $grandTotal > 0 ? ($totalSakit/$grandTotal)*100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Status Distribution --}}
            <div class="card mt-3">
                <div class="card-header border-0">
                    <h3 class="card-title">
                        <i class="fas fa-check-double mr-2"></i> Status Pengajuan
                    </h3>
                </div>
                <div class="card-body p-0">
                    <ul class="nav flex-column">
                        <li class="nav-item border-bottom">
                            <a href="#" class="nav-link">
                                <span class="badge badge-warning float-right">{{ $stats['pending'] ?? 0 }}</span>
                                <i class="fas fa-hourglass-half mr-2 text-warning"></i>Pending
                            </a>
                        </li>
                        <li class="nav-item border-bottom">
                            <a href="#" class="nav-link">
                                <span class="badge badge-success float-right">{{ $stats['approved'] ?? 0 }}</span>
                                <i class="fas fa-check-circle mr-2 text-success"></i>Disetujui
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <span class="badge badge-danger float-right">{{ $stats['rejected'] ?? 0 }}</span>
                                <i class="fas fa-times-circle mr-2 text-danger"></i>Ditolak
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
