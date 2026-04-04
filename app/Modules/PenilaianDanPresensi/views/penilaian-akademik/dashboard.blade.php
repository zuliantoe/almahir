@extends('layouts.app')

@section('title', 'Dashboard Penilaian Akademik')

@section('content')
<div class="container-fluid">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="m-0"><i class="fas fa-book text-primary mr-2"></i>Dashboard Penilaian Akademik</h3>
            <small class="text-muted">Monitoring dan manajemen penilaian akademik siswa</small>
        </div>
        <a href="{{ route('penilaiandanpresensi.penilaianakademik.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i> Tambah Penilaian
        </a>
    </div>

    {{-- Statistics Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-book"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Penilaian</span>
                    <span class="info-box-number">{{ $stats['total'] ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-star"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Rata-Rata Nilai</span>
                    <span class="info-box-number">{{ number_format($stats['average'] ?? 0, 2) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Siswa Dinilai</span>
                    <span class="info-box-number">{{ $stats['students'] ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-danger"><i class="fas fa-calendar-alt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Bulan Ini</span>
                    <span class="info-box-number">{{ $stats['this_month'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Content Row --}}
    <div class="row">
        {{-- Left Column: Recent Penilaian --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header border-0">
                    <h3 class="card-title">
                        <i class="fas fa-list-ul mr-2"></i> Penilaian Terbaru
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('penilaiandanpresensi.penilaianakademik.index') }}" class="btn btn-sm btn-info">
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
                                    <th>Nilai</th>
                                    <th>Tanggal</th>
                                    <th style="width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentNilai as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->siswa->nama ?? '-' }}</strong>
                                    </td>
                                    <td>{{ $item->guru->nama ?? '-' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $item->nilai >= 75 ? 'success' : ($item->nilai >= 60 ? 'warning' : 'danger') }}">
                                            {{ $item->nilai }}
                                        </span>
                                    </td>
                                    <td>
                                        <small>{{ $item->created_at?->locale('id')->translatedFormat('d M Y') }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('penilaiandanpresensi.penilaianakademik.edit', $item->id) }}" class="btn btn-xs btn-info" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">Belum ada data penilaian</td>
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
            {{-- Distribution by Grade --}}
            <div class="card">
                <div class="card-header border-0">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar mr-2"></i> Distribusi Nilai
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="p-3">
                        @php
                            $excellent = $distribution['excellent'] ?? 0;
                            $good = $distribution['good'] ?? 0;
                            $fair = $distribution['fair'] ?? 0;
                            $poor = $distribution['poor'] ?? 0;
                            $total = $excellent + $good + $fair + $poor;
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>A (90-100)</span>
                                <span>{{ $excellent }}</span>
                            </div>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $total > 0 ? ($excellent/$total)*100 : 0 }}%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>B (75-89)</span>
                                <span>{{ $good }}</span>
                            </div>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-info" role="progressbar" style="width: {{ $total > 0 ? ($good/$total)*100 : 0 }}%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>C (60-74)</span>
                                <span>{{ $fair }}</span>
                            </div>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $total > 0 ? ($fair/$total)*100 : 0 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>D (&lt;60)</span>
                                <span>{{ $poor }}</span>
                            </div>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $total > 0 ? ($poor/$total)*100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
