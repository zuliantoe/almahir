@extends('layouts.app')

@section('title', 'Dashboard Akademik')

@section('content')
<div class="container-fluid">
    {{-- Header Section --}}
    <div class="row mb-3">
        <div class="col-12">
            <h1 class="h3 mb-0 text-gray-800">Overview Akademik</h1>
            <p class="text-muted">Ringkasan data dan aktivitas terbaru hari ini.</p>
        </div>
    </div>

    {{-- Statistik Row --}}
    <div class="row">
        {{-- Total Siswa --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info shadow-sm">
                <div class="inner">
                    <h3>{{ number_format($totalSiswa ?? 0) }}</h3>
                    <p>Total Siswa Aktif</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <a href="#" class="small-box-footer">Selengkapnya <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        {{-- Total Guru --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success shadow-sm">
                <div class="inner">
                    <h3>{{ number_format($totalGuru ?? 0) }}</h3>
                    <p>Total Tenaga Pengajar</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <a href="#" class="small-box-footer">Selengkapnya <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        {{-- Total Kelas --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-purple shadow-sm">
                <div class="inner">
                    <h3>{{ number_format($totalKelas ?? 0) }}</h3>
                    <p>Rombongan Belajar</p>
                </div>
                <div class="icon">
                    <i class="fas fa-school"></i>
                </div>
                <a href="{{ route('akademik.kelas.index') }}" class="small-box-footer">Selengkapnya <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        {{-- Mata Pelajaran --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning shadow-sm">
                <div class="inner">
                    <h3>{{ number_format($totalMapel ?? 0) }}</h3>
                    <p>Total Mata Pelajaran</p>
                </div>
                <div class="icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <a href="{{ route('akademik.mata-pelajaran.index') }}" class="small-box-footer">Selengkapnya <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="row mt-3">
        {{-- Left Column: Chart & Quick Actions --}}
        <div class="col-md-8">
            <x-card title="Komposisi Data Akademik" icon="fas fa-chart-bar" type="primary" outline>
                <div class="position-relative mb-4" style="height: 300px;">
                    <canvas id="statistikChart"></canvas>
                </div>
            </x-card>

            <x-card title="Akses Cepat" icon="fas fa-location-arrow" outline>
                <div class="mb-3 border-bottom pb-2">
                    <span class="font-weight-bold text-muted text-uppercase small"><i class="fas fa-folder-open mr-1"></i> Data Master (Daftar)</span>
                </div>
                <div class="row mb-4 text-center">
                    <div class="col-md-3 col-6 mb-2">
                        <x-btn :href="route('akademik.tahun-ajaran.index')" class="btn-primary w-100 py-3 shadow-sm bg-primary border-0" style="display: flex; flex-direction: column; align-items: center;">
                            <i class="fas fa-calendar-alt mb-2 fa-lg"></i>
                            <span class="small font-weight-bold">Tahun Ajaran</span>
                        </x-btn>
                    </div>
                    <div class="col-md-3 col-6 mb-2">
                        <x-btn :href="route('akademik.kelas.index')" class="btn-success w-100 py-3 shadow-sm bg-success border-0" style="display: flex; flex-direction: column; align-items: center;">
                            <i class="fas fa-door-open mb-2 fa-lg"></i>
                            <span class="small font-weight-bold">Kelas</span>
                        </x-btn>
                    </div>
                    <div class="col-md-3 col-6 mb-2">
                        <x-btn :href="route('akademik.mata-pelajaran.index')" class="btn-danger w-100 py-3 shadow-sm bg-danger border-0" style="display: flex; flex-direction: column; align-items: center;">
                            <i class="fas fa-book mb-2 fa-lg"></i>
                            <span class="small font-weight-bold">Pelajaran</span>
                        </x-btn>
                    </div>
                    <div class="col-md-3 col-6 mb-2">
                        <x-btn :href="route('akademik.jenis-kegiatan.index')" class="btn-info w-100 py-3 shadow-sm bg-info border-0" style="display: flex; flex-direction: column; align-items: center;">
                            <i class="fas fa-clipboard-list mb-2 fa-lg"></i>
                            <span class="small font-weight-bold">Kegiatan</span>
                        </x-btn>
                    </div>
                </div>

                <div class="mb-3 border-bottom pb-2">
                    <span class="font-weight-bold text-muted text-uppercase small"><i class="fas fa-plus-circle mr-1"></i> Tambah Data Baru</span>
                </div>
                <div class="row">
                    <div class="col-md-4 col-12 mb-2">
                        <x-btn :href="route('akademik.jadwal-pelajaran.create')" class="btn-outline-primary w-100" icon="fas fa-clock mr-1">Jadwal Baru</x-btn>
                    </div>
                    <div class="col-md-4 col-12 mb-2">
                        <x-btn :href="route('akademik.kalender-akademik.create')" class="btn-outline-success w-100" icon="fas fa-calendar-plus mr-1">Agenda Baru</x-btn>
                    </div>
                    <div class="col-md-4 col-12 mb-2">
                        <x-btn :href="route('akademik.kurikulum.create')" class="btn-outline-warning w-100" icon="fas fa-book-reader mr-1">Kurikulum</x-btn>
                    </div>
                </div>
            </x-card>
        </div>

        {{-- Right Column: Recent Activities --}}
        <div class="col-md-4">
            <x-card title="Baru Saja Bergabung" icon="fas fa-history" type="info" outline>
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs mb-3" id="recent-tab" role="tablist">
                        <li class="nav-item flex-grow-1 text-center">
                            <a class="nav-link active" id="siswa-tab" data-toggle="pill" href="#tab-siswa" role="tab" aria-selected="true">Siswa</a>
                        </li>
                        <li class="nav-item flex-grow-1 text-center">
                            <a class="nav-link" id="guru-tab" data-toggle="pill" href="#tab-guru" role="tab" aria-selected="false">Guru</a>
                        </li>
                    </ul>
                    <div class="tab-content border p-2 rounded">
                        <div class="tab-pane fade show active" id="tab-siswa" role="tabpanel">
                            <div class="timeline timeline-inverse">
                                @forelse($siswaTerbaru ?? [] as $siswa)
                                    <div>
                                        <i class="fas fa-user-graduate bg-primary shadow-sm"></i>
                                        <div class="timeline-item border-0 bg-light mb-2 rounded shadow-sm">
                                            <span class="time small text-muted"><i class="far fa-clock"></i> {{ $siswa->created_at->diffForHumans() }}</span>
                                            <h3 class="timeline-header border-0 pb-0" style="font-size: 0.95rem;"><strong>{{ $siswa->nama }}</strong></h3>
                                            <div class="timeline-body pt-0 small text-muted">NIS: {{ $siswa->nis }}</div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center p-3 text-muted small">Belum ada data siswa</div>
                                @endforelse
                                <div><i class="far fa-clock bg-gray"></i></div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab-guru" role="tabpanel">
                            <div class="timeline timeline-inverse">
                                @forelse($guruTerbaru ?? [] as $guru)
                                    <div>
                                        <i class="fas fa-chalkboard-teacher bg-success shadow-sm"></i>
                                        <div class="timeline-item border-0 bg-light mb-2 rounded shadow-sm">
                                            <span class="time small text-muted"><i class="far fa-clock"></i> {{ $guru->created_at->diffForHumans() }}</span>
                                            <h3 class="timeline-header border-0 pb-0" style="font-size: 0.95rem;"><strong>{{ $guru->nama }}</strong></h3>
                                            <div class="timeline-body pt-0 small text-muted">NIP: {{ $guru->nip ?? '-' }}</div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center p-3 text-muted small">Belum ada data guru</div>
                                @endforelse
                                <div><i class="far fa-clock bg-gray"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('statistikChart').getContext('2d');
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Siswa', 'Guru', 'Kelas', 'Mapel'],
                datasets: [{
                    label: 'Statistik Akademik',
                    data: [
                        {{ $totalSiswa ?? 0 }}, 
                        {{ $totalGuru ?? 0 }}, 
                        {{ $totalKelas ?? 0 }}, 
                        {{ $totalMapel ?? 0 }}
                    ],
                    backgroundColor: [
                        '#17a2b8',
                        '#28a745',
                        '#6f42c1',
                        '#ffc107'
                    ],
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0, 0, 0, 0.05)' } },
                    x: { grid: { display: false } }
                }
            }
        });
    });
</script>
@endpush
