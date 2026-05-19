@extends('layouts.app')

@section('title', 'Laporan Akademik')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Pusat Laporan Akademik</h1>
            <p class="text-muted">Kumpulan laporan dan statistik akademik untuk Tahun Ajaran Aktif ({{ $tahunAktif ? $tahunAktif->tahunajaran : 'Belum Ada' }}).</p>
        </div>
    </div>

    <!-- Ringkasan Statistik -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                @if(auth()->user()->hasRole('SISWA'))
                                    Teman Sekelas
                                @elseif(auth()->user()->hasRole('GURU'))
                                    Total Siswa Diajar
                                @else
                                    Total Siswa Aktif
                                @endif
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_siswa_aktif'] }} Orang</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                @if(auth()->user()->hasRole('SISWA'))
                                    Rombel Saya
                                @elseif(auth()->user()->hasRole('GURU'))
                                    Kelas Saya Ajar
                                @else
                                    Total Rombel
                                @endif
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_rombel'] }} Rombel</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                @if(auth()->user()->hasRole('SISWA'))
                                    Guru Pengajar
                                @elseif(auth()->user()->hasRole('GURU'))
                                    Jam Mengajar (Sesi)
                                @else
                                    Guru Aktif Mengajar
                                @endif
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ auth()->user()->hasRole('GURU') ? $stats['total_jadwal'] . ' Sesi' : $stats['total_guru_mengajar'] . ' Guru' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                @if(auth()->user()->hasRole('SISWA'))
                                    Total Sesi Pelajaran
                                @else
                                    Total Jadwal Pelajaran
                                @endif
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_jadwal'] }} Sesi</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Laporan -->
    <div class="row">
        <!-- Laporan Beban Mengajar -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow h-100 border-0 rounded-lg">
                <div class="card-body text-center p-5">
                    <div class="bg-primary-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px; background-color: #e3f2fd;">
                        <i class="fas fa-chalkboard-teacher fa-3x text-primary"></i>
                    </div>
                    <h5 class="font-weight-bold text-dark">Laporan Beban Mengajar</h5>
                    <p class="text-muted small mb-4">Melihat total Jam Pelajaran (JP) dan distribusi kelas untuk masing-masing guru pada tahun ajaran aktif.</p>
                    <a href="{{ route('akademik.beban-mengajar.index') }}" class="btn btn-outline-primary btn-block rounded-pill font-weight-bold">Lihat Laporan <i class="fas fa-arrow-right ml-1"></i></a>
                </div>
            </div>
        </div>

        <!-- Laporan Riwayat Kenaikan & Perpindahan -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow h-100 border-0 rounded-lg">
                <div class="card-body text-center p-5">
                    <div class="bg-success-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px; background-color: #e8f5e9;">
                        <i class="fas fa-history fa-3x text-success"></i>
                    </div>
                    <h5 class="font-weight-bold text-dark">Riwayat Kenaikan Kelas</h5>
                    <p class="text-muted small mb-4">Melihat data arsip mutasi siswa, riwayat kenaikan kelas, dan status kelulusan dari tahun ke tahun.</p>
                    <a href="{{ route('akademik.rombel.history') }}" class="btn btn-outline-success btn-block rounded-pill font-weight-bold">Lihat Riwayat <i class="fas fa-arrow-right ml-1"></i></a>
                </div>
            </div>
        </div>

        <!-- Laporan Data Rombel -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow h-100 border-0 rounded-lg">
                <div class="card-body text-center p-5">
                    <div class="bg-info-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px; background-color: #e0f7fa;">
                        <i class="fas fa-users fa-3x text-info"></i>
                    </div>
                    <h5 class="font-weight-bold text-dark">Data Induk Rombel</h5>
                    <p class="text-muted small mb-4">Melihat daftar seluruh rombongan belajar beserta wali kelas dan kapasitas siswa di tahun ajaran ini.</p>
                    <a href="{{ route('akademik.rombel.index') }}" class="btn btn-outline-info btn-block rounded-pill font-weight-bold">Kelola Rombel <i class="fas fa-arrow-right ml-1"></i></a>
                </div>
            </div>
        </div>
        
        <!-- Placeholder untuk laporan-laporan lain (Cetak Raport, Statistik Kehadiran) -->
        <div class="col-lg-12 mt-2">
            <div class="alert alert-secondary border-0 shadow-sm d-flex align-items-center">
                <i class="fas fa-info-circle fa-2x mr-3 text-secondary"></i>
                <div>
                    <h6 class="mb-1 font-weight-bold text-dark">Laporan Terintegrasi</h6>
                    <p class="mb-0 small text-muted">Laporan Presensi (Kehadiran), Penilaian Akademik (Raport), dan Keuangan dapat diakses melalui modul masing-masing setelah guru dan staf menyelesaikan input data.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-primary-light { background-color: #e3f2fd !important; }
    .bg-success-light { background-color: #e8f5e9 !important; }
    .bg-info-light { background-color: #e0f7fa !important; }
    .card.rounded-lg { border-radius: 15px; transition: transform 0.2s ease-in-out; }
    .card.rounded-lg:hover { transform: translateY(-5px); }
</style>
@endsection
