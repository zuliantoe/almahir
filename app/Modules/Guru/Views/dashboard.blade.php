@extends('layouts.app')

@section('title', 'Dashboard Guru')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card glass-card hover-elevate overflow-hidden border-0 shadow-sm">
                <div class="card-body p-5">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="display-4 font-weight-bold text-primary mb-3">Selamat Datang, Ustadz/Ustadzah!</h1>
                            <p class="lead text-muted mb-4">Senang melihat Anda kembali. Mari kita mulai hari ini dengan semangat untuk mendidik generasi rabbani.</p>
                            <div class="d-flex flex-wrap">
                                <a href="{{ route('penilaiandanpresensi.presensi.index') }}" class="btn btn-primary btn-lg px-4 mr-3 mb-2 shadow-sm" style="border-radius: 50px;">
                                    <i class="fas fa-user-check mr-2"></i> Input Presensi
                                </a>
                                <a href="{{ route('penilaiandanpresensi.penilaianakademik.index') }}" class="btn btn-outline-primary btn-lg px-4 mb-2" style="border-radius: 50px;">
                                    <i class="fas fa-file-invoice mr-2"></i> Input Nilai
                                </a>
                            </div>
                        </div>
                        <div class="col-md-4 text-center d-none d-md-block">
                            <i class="fas fa-chalkboard-teacher fa-10x text-primary opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card glass-card hover-elevate border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-primary-light p-3 mr-3">
                            <i class="fas fa-users text-primary fa-lg"></i>
                        </div>
                        <h5 class="mb-0 font-weight-bold">Total Santri</h5>
                    </div>
                    <h2 class="font-weight-bold mb-1">{{ $stats['total_siswa'] }}</h2>
                    <p class="text-muted small mb-0">Terdaftar di kelas Anda</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card glass-card hover-elevate border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-success-light p-3 mr-3">
                            <i class="fas fa-calendar-check text-success fa-lg"></i>
                        </div>
                        <h5 class="mb-0 font-weight-bold">Presensi Hari Ini</h5>
                    </div>
                    <h2 class="font-weight-bold mb-1">{{ $stats['presensi_today'] }}</h2>
                    <p class="text-muted small mb-0">Sudah melakukan absensi</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card glass-card hover-elevate border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-info-light p-3 mr-3">
                            <i class="fas fa-star text-info fa-lg"></i>
                        </div>
                        <h5 class="mb-0 font-weight-bold">Rata-rata Nilai</h5>
                    </div>
                    <h2 class="font-weight-bold mb-1">{{ number_format($stats['avg_nilai'], 1) }}</h2>
                    <p class="text-muted small mb-0">Pencapaian akademik santri</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-primary-light { background-color: rgba(67, 97, 238, 0.1); }
    .bg-success-light { background-color: rgba(40, 167, 69, 0.1); }
    .bg-info-light { background-color: rgba(23, 162, 184, 0.1); }
    .opacity-25 { opacity: 0.15; }
</style>
@endsection
