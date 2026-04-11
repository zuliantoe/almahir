@extends('layouts.app')

@section('title', 'Dashboard Akademik')

@push('styles')
<style>
    .small-box {
        border-radius: 0.75rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: transform 0.2s ease-in-out;
    }
    .small-box:hover {
        transform: translateY(-5px);
    }
    .small-box .icon {
        color: rgba(255, 255, 255, 0.8) !important;
        top: 15px;
        right: 15px;
        font-size: 60px;
    }
    .small-box h3 {
        font-size: 2.2rem;
        font-weight: 700;
        margin-bottom: 5px;
    }
    .card {
        border-radius: 0.75rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: none;
        margin-bottom: 1.5rem;
    }
    .card-header {
        background-color: transparent !important;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 1.25rem 1.5rem;
    }
    .card-title {
        font-weight: 600;
        font-size: 1.1rem;
        color: #2c3e50;
    }
    .quick-action-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        padding: 1.5rem 1rem;
        border-radius: 0.5rem;
        transition: all 0.3s;
        text-align: center;
        height: 100%;
        color: white !important;
        text-decoration: none !important;
    }
    .quick-action-btn i {
        font-size: 2rem;
        margin-bottom: 0.75rem;
    }
    .quick-action-btn span {
        font-weight: 500;
        letter-spacing: 0.5px;
    }
    .quick-action-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    .bg-gradient-blue { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
    .bg-gradient-green { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
    .bg-gradient-purple { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .bg-gradient-orange { background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); }
    
    .timeline>div>.timeline-item {
        box-shadow: none;
        border: 1px solid rgba(0,0,0,0.05);
        border-radius: 0.5rem;
    }
    .timeline>div>i {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
</style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        {{-- ====== Header Section ====== --}}
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="font-weight-bold text-dark mb-0">Overview Akademik</h2>
                <p class="text-muted">Ringkasan data dan aktivitas terbaru hari ini.</p>
            </div>
        </div>

        {{-- ====== Statistik Row ====== --}}
        <div class="row">
            {{-- Total Siswa --}}
            <div class="col-lg-3 col-6">
                <div class="small-box bg-gradient-blue text-white">
                    <div class="inner">
                        <h3>{{ number_format($totalSiswa ?? 0) }}</h3>
                        <p class="mb-0 text-white-50">Total Siswa Aktif</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                </div>
            </div>

            {{-- Total Guru --}}
            <div class="col-lg-3 col-6">
                <div class="small-box bg-gradient-green text-white">
                    <div class="inner">
                        <h3>{{ number_format($totalGuru ?? 0) }}</h3>
                        <p class="mb-0 text-white-50">Total Tenaga Pengajar</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                </div>
            </div>

            {{-- Total Kelas --}}
            <div class="col-lg-3 col-6">
                <div class="small-box bg-gradient-purple text-white">
                    <div class="inner">
                        <h3>{{ number_format($totalKelas ?? 0) }}</h3>
                        <p class="mb-0 text-white-50">Rombongan Belajar</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-school"></i>
                    </div>
                </div>
            </div>

            {{-- Mata Pelajaran --}}
            <div class="col-lg-3 col-6">
                <div class="small-box bg-gradient-orange text-white">
                    <div class="inner">
                        <h3>{{ number_format($totalMapel ?? 0) }}</h3>
                        <p class="mb-0 text-white-50">Total Mata Pelajaran</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- ====== Main Content ====== --}}
        <div class="row mt-3">
            
            {{-- Left Column: Chart & Quick Actions --}}
            <div class="col-md-8">
                
                {{-- Chart --}}
                <div class="card card-outline card-primary">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-chart-bar mr-2 text-primary"></i>
                            Komposisi Data Akademik
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="position-relative mb-4" style="height: 300px;">
                            <canvas id="statistikChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="card mt-4">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-location-arrow mr-2 text-info"></i>
                            Akses Cepat
                        </h3>
                    </div>
                    <div class="card-body">
                        
                        <div class="mb-3 border-bottom pb-2">
                            <span class="font-weight-bold text-muted text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px;"><i class="fas fa-folder-open mr-1"></i> Data Master (Daftar Data)</span>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3 col-6 mb-3">
                                <a href="{{ route('akademik.tahun-ajaran.index') }}" class="quick-action-btn bg-primary py-3">
                                    <i class="fas fa-calendar-alt mb-2" style="font-size: 1.5rem;"></i>
                                    <span style="font-size: 0.9rem;">Tahun Ajaran</span>
                                </a>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <a href="{{ route('akademik.kelas.index') }}" class="quick-action-btn bg-success py-3">
                                    <i class="fas fa-door-open mb-2" style="font-size: 1.5rem;"></i>
                                    <span style="font-size: 0.9rem;">Kelas</span>
                                </a>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <a href="{{ route('akademik.mata-pelajaran.index') }}" class="quick-action-btn bg-danger py-3">
                                    <i class="fas fa-book mb-2" style="font-size: 1.5rem;"></i>
                                    <span style="font-size: 0.9rem;">Mata Pelajaran</span>
                                </a>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <a href="{{ route('akademik.jenis-kegiatan.index') }}" class="quick-action-btn bg-info py-3">
                                    <i class="fas fa-clipboard-list mb-2" style="font-size: 1.5rem;"></i>
                                    <span style="font-size: 0.9rem;">Jenis Kegiatan</span>
                                </a>
                            </div>
                        </div>

                        <div class="mb-3 border-bottom pb-2 mt-2">
                            <span class="font-weight-bold text-muted text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px;"><i class="fas fa-plus-circle mr-1"></i> Tambah Data Baru (Create)</span>
                        </div>
                        <div class="row">
                            <div class="col-md-3 col-6 mb-3">
                                <a href="{{ route('akademik.tahun-ajaran.create') }}" class="quick-action-btn bg-gradient-blue py-3">
                                    <i class="fas fa-calendar-plus mb-2" style="font-size: 1.5rem;"></i>
                                    <span style="font-size: 0.85rem;">+ Thn Ajaran</span>
                                </a>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <a href="{{ route('akademik.kelas.create') }}" class="quick-action-btn bg-gradient-green py-3">
                                    <i class="fas fa-plus mb-2" style="font-size: 1.5rem;"></i>
                                    <span style="font-size: 0.85rem;">+ Kelas Baru</span>
                                </a>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <a href="{{ route('akademik.mata-pelajaran.create') }}" class="quick-action-btn bg-gradient-orange py-3">
                                    <i class="fas fa-book-medical mb-2" style="font-size: 1.5rem;"></i>
                                    <span style="font-size: 0.85rem;">+ Pelajaran</span>
                                </a>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <a href="{{ route('akademik.jenis-kegiatan.create') }}" class="quick-action-btn bg-gradient-purple py-3">
                                    <i class="fas fa-file-medical mb-2" style="font-size: 1.5rem;"></i>
                                    <span style="font-size: 0.85rem;">+ Kegiatan</span>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            {{-- Right Column: Recent Activities --}}
            <div class="col-md-4">
                <div class="card card-outline card-info h-100">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-history mr-2 text-info"></i>
                            Baru Saja Ditambahkan
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <ul class="nav nav-pills p-3" id="custom-tabs-two-tab" role="tablist">
                            <li class="nav-item flex-fill text-center">
                                <a class="nav-link active" id="tabs-siswa-tab" data-toggle="pill" href="#tabs-siswa" role="tab" aria-controls="tabs-siswa" aria-selected="true">Siswa Baru</a>
                            </li>
                            <li class="nav-item flex-fill text-center ml-2">
                                <a class="nav-link" id="tabs-guru-tab" data-toggle="pill" href="#tabs-guru" role="tab" aria-controls="tabs-guru" aria-selected="false">Guru Baru</a>
                            </li>
                        </ul>
                        
                        <div class="tab-content" id="custom-tabs-two-tabContent">
                            {{-- Tab Siswa --}}
                            <div class="tab-pane fade show active p-3" id="tabs-siswa" role="tabpanel" aria-labelledby="tabs-siswa-tab">
                                <div class="timeline timeline-inverse">
                                    @forelse($siswaTerbaru ?? [] as $siswa)
                                        <div>
                                            <i class="fas fa-user-graduate bg-primary"></i>
                                            <div class="timeline-item">
                                                <span class="time"><i class="far fa-clock"></i> {{ $siswa->created_at->diffForHumans() }}</span>
                                                <h3 class="timeline-header border-0">
                                                    <strong>{{ $siswa->nama }}</strong> <br>
                                                    <span class="text-xs text-muted">NIS: {{ $siswa->nis }}</span>
                                                </h3>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center p-3 text-muted">Belum ada data siswa</div>
                                    @endforelse
                                    <div><i class="far fa-clock bg-gray"></i></div>
                                </div>
                            </div>
                            
                            {{-- Tab Guru --}}
                            <div class="tab-pane fade p-3" id="tabs-guru" role="tabpanel" aria-labelledby="tabs-guru-tab">
                                <div class="timeline timeline-inverse">
                                    @forelse($guruTerbaru ?? [] as $guru)
                                        <div>
                                            <i class="fas fa-chalkboard-teacher bg-success"></i>
                                            <div class="timeline-item">
                                                <span class="time"><i class="far fa-clock"></i> {{ $guru->created_at->diffForHumans() }}</span>
                                                <h3 class="timeline-header border-0">
                                                    <strong>{{ $guru->nama }}</strong> <br>
                                                    <span class="text-xs text-muted">NIP: {{ $guru->nip ?? '-' }}</span>
                                                </h3>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center p-3 text-muted">Belum ada data guru</div>
                                    @endforelse
                                    <div><i class="far fa-clock bg-gray"></i></div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('statistikChart').getContext('2d');
            
            // Gradient setups for Chart
            let gradientBlue = ctx.createLinearGradient(0, 0, 0, 400);
            gradientBlue.addColorStop(0, '#4facfe');
            gradientBlue.addColorStop(1, '#00f2fe');

            let gradientGreen = ctx.createLinearGradient(0, 0, 0, 400);
            gradientGreen.addColorStop(0, '#43e97b');
            gradientGreen.addColorStop(1, '#38f9d7');
            
            let gradientPurple = ctx.createLinearGradient(0, 0, 0, 400);
            gradientPurple.addColorStop(0, '#667eea');
            gradientPurple.addColorStop(1, '#764ba2');
            
            let gradientOrange = ctx.createLinearGradient(0, 0, 0, 400);
            gradientOrange.addColorStop(0, '#f6d365');
            gradientOrange.addColorStop(1, '#fda085');

            new Chart(ctx, {
                type: 'bar', // Change to 'doughnut' if you prefer circular chart
                data: {
                    labels: ['Total Siswa', 'Total Guru', 'Total Kelas', 'Mata Pelajaran'],
                    datasets: [{
                        label: 'Statistik Akademik',
                        data: [
                            {{ $totalSiswa ?? 0 }}, 
                            {{ $totalGuru ?? 0 }}, 
                            {{ $totalKelas ?? 0 }}, 
                            {{ $totalMapel ?? 0 }}
                        ],
                        backgroundColor: [
                            gradientBlue,
                            gradientGreen,
                            gradientPurple,
                            gradientOrange
                        ],
                        borderRadius: 8,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)',
                                drawBorder: false
                            }
                        },
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
