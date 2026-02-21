@extends('layouts.app')

@section('title', 'Dashboard Akademik')

@section('content')
    <div class="container-fluid">

        {{-- ====== Statistik Row ====== --}}
        <div class="row">

            {{-- Total Siswa --}}
            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>1,248</h3>
                        <p>Total Siswa</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                </div>
            </div>

            {{-- Total Guru --}}
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>86</h3>
                        <p>Total Guru</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                </div>
            </div>

            {{-- Total Kelas --}}
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>32</h3>
                        <p>Total Kelas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-school"></i>
                    </div>
                </div>
            </div>

            {{-- Mata Pelajaran --}}
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>18</h3>
                        <p>Mata Pelajaran</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-book"></i>
                    </div>
                </div>
            </div>

        </div>

        {{-- ====== Grafik & Aktivitas ====== --}}
        <div class="row">

            {{-- Grafik Statistik --}}
            <div class="col-md-8">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-bolt mr-1"></i>
                            Button Cepat
                        </h3>
                    </div>
                    <div class="card-body">

                        <a href="tahun-ajaran" class="btn btn-primary m-1">
                            Tahun Ajaran
                        </a>

                        <a href="kelas" class="btn btn-success m-1">
                            Kelas
                        </a>

                         <a href="jenis-kegiatan" class="btn btn-success m-1">
                            Jenis Kegiatan
                        </a>

                        <a href="mapel" class="btn btn-danger m-1">
                            Mata Pelajaran
                        </a>

                    </div>
                </div>
            </div>

            {{-- Aktivitas Terbaru --}}
            <div class="col-md-4">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-bell mr-1"></i>
                            Aktivitas Terbaru
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <i class="fas fa-user-plus text-success mr-2"></i>
                                5 Siswa baru ditambahkan
                            </li>
                            <li class="list-group-item">
                                <i class="fas fa-edit text-warning mr-2"></i>
                                Data guru diperbarui
                            </li>
                            <li class="list-group-item">
                                <i class="fas fa-calendar-check text-primary mr-2"></i>
                                Jadwal semester dibuat
                            </li>
                            <li class="list-group-item">
                                <i class="fas fa-file-alt text-danger mr-2"></i>
                                Nilai raport diinput
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('statistikChart').getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Siswa', 'Guru', 'Kelas', 'Mapel'],
                datasets: [{
                    label: 'Data Akademik',
                    data: [1248, 86, 32, 18],
                    backgroundColor: [
                        '#007bff',
                        '#28a745',
                        '#ffc107',
                        '#dc3545'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    </script>
@endsection
