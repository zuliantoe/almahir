@extends('layouts.app')

@section('title', 'Dashboard Penilaian Tahfidz')

@section('content')
<div class="container-fluid">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="m-0"><i class="fas fa-quran text-success mr-2"></i>Dashboard Penilaian Tahfidz</h3>
            <small class="text-muted">Monitoring hafalan Al-Quran siswa</small>
        </div>
        <a href="{{ route('penilaiandanpresensi.penilaiantahfidz.create') }}" class="btn btn-success">
            <i class="fas fa-plus mr-1"></i> Tambah Penilaian
        </a>
    </div>

    {{-- Statistics Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-quran"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Hafalan</span>
                    <span class="info-box-number">{{ $stats['total'] ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-book-open"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Surat Berbeda</span>
                    <span class="info-box-number">{{ $stats['surat_count'] ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Siswa Hafal</span>
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
        {{-- Left Column: Recent Hafalan --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header border-0">
                    <h3 class="card-title">
                        <i class="fas fa-list-ul mr-2"></i> Hafalan Terbaru
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('penilaiandanpresensi.penilaiantahfidz.index') }}" class="btn btn-sm btn-success">
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
                                    <th>Surat</th>
                                    <th>Ayat</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th style="width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentHafalan as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->siswa->nama ?? '-' }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $item->surat ?? '-' }}</span>
                                    </td>
                                    <td>{{ $item->ayat ?? '-' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $item->status == 'Lancar' ? 'success' : 'warning' }}">
                                            {{ $item->status ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <small>{{ $item->created_at?->locale('id')->translatedFormat('d M Y') }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('penilaiandanpresensi.penilaiantahfidz.edit', $item->id) }}" class="btn btn-xs btn-info" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-3">Belum ada data hafalan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Top Hafizan --}}
        <div class="col-lg-4">
            {{-- Top Students --}}
            <div class="card">
                <div class="card-header border-0">
                    <h3 class="card-title">
                        <i class="fas fa-crown mr-2 text-warning"></i> Top Hafizan
                    </h3>
                </div>
                <div class="card-body p-0">
                    <ul class="nav flex-column">
                        @forelse($topHafizan as $index => $item)
                        <li class="nav-item border-bottom">
                            <a href="#" class="nav-link">
                                <span class="badge badge-primary float-right">
                                    {{ $item->total ?? 0 }} Surat
                                </span>
                                <i class="fas fa-medal mr-2 {{ $index == 0 ? 'text-warning' : ($index == 1 ? 'text-secondary' : 'text-danger') }}"></i>
                                {{ $item->siswa_nama ?? '-' }}
                            </a>
                        </li>
                        @empty
                        <li class="nav-item text-center text-muted p-3">
                            Belum ada data
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
