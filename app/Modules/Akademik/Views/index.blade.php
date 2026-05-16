@extends('layouts.app')

@section('title', 'Dashboard Akademik')

@section('content')
<div class="container-fluid">
    @if(isset($ongoingEvents) && count($ongoingEvents))
    <div class="row">
        <div class="col-12">
            @foreach($ongoingEvents as $event)
            <div class="alert alert-{{ $event->jenisKegiatan->warna ? 'info' : 'primary' }} shadow-sm border-left-info" style="border-left: 5px solid {{ $event->jenisKegiatan->warna ?? '#4e73df' }} !important;">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <i class="fas fa-calendar-check fa-2x opacity-50"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 font-weight-bold">AGENDA BERJALAN: {{ $event->nama_kegiatan }}</h6>
                        <small>{{ $event->tanggal_awal->translatedFormat('d M') }} - {{ $event->tanggal_akhir->translatedFormat('d M Y') }} | {{ $event->deskripsi }}</small>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Header Section --}}
    <div class="row mb-3">
        <div class="col-12">
            <h1 class="h3 mb-0 text-gray-800">Overview Akademik</h1>
            <p class="text-muted">Ringkasan data dan aktivitas terbaru hari ini.</p>
        </div>
    </div>

    @if(auth()->user()->hasRole('SUPER_ADMIN') || auth()->user()->hasRole('STAFF'))
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
    @else
    {{-- Welcome Card for Guru/Siswa --}}
    <div class="row">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white shadow-lg border-0 rounded-xl mb-4">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="mr-4">
                        <i class="fas fa-user-circle fa-5x opacity-7"></i>
                    </div>
                    <div>
                        <h2 class="font-weight-bold mb-1">Selamat Datang, {{ auth()->user()->name }}!</h2>
                        <p class="mb-0 opacity-8">Anda login sebagai <strong>{{ auth()->user()->hasRole('GURU') ? 'Guru / Pengajar' : 'Santri / Siswa' }}</strong>.</p>
                        <hr class="border-light opacity-3 my-2">
                        <div class="d-flex">
                            <x-btn :href="route('akademik.jadwal-pelajaran.index')" class="btn-light btn-sm mr-2 font-weight-bold text-primary px-3 rounded-pill">
                                <i class="fas fa-clock mr-1"></i> Lihat Jadwal Saya
                            </x-btn>
                            <x-btn :href="route('akademik.kalender-akademik.index')" class="btn-outline-light btn-sm font-weight-bold px-3 rounded-pill">
                                <i class="fas fa-calendar-alt mr-1"></i> Agenda Sekolah
                            </x-btn>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Main Content --}}
    <div class="row mt-3">
        {{-- Left Column: Chart & Quick Actions --}}
        <div class="col-md-8">
            @if(auth()->user()->hasRole('SUPER_ADMIN') || auth()->user()->hasRole('STAFF'))
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
            @else
            <x-card :title="'Jadwal ' . (auth()->user()->hasRole('GURU') ? 'Mengajar' : 'Belajar') . ' Hari Ini'" icon="fas fa-clock" type="primary" outline>
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-2">
                    {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                </div>
                
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Jam</th>
                                <th>Mapel</th>
                                <th>{{ auth()->user()->hasRole('GURU') ? 'Rombel' : 'Guru' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jadwalHariIni ?? [] as $jadwal)
                            <tr>
                                <td class="small font-weight-bold">{{ substr($jadwal->jamawal, 0, 5) }}</td>
                                <td class="small">{{ $jadwal->mataPelajaran->nama }}</td>
                                <td class="small text-muted">
                                    {{ auth()->user()->hasRole('GURU') ? $jadwal->rombel->nama_rombel : $jadwal->guru->nama }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted small">
                                    <i class="fas fa-coffee mb-2 d-block fa-2x"></i>
                                    Tidak ada jadwal hari ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(count($jadwalHariIni ?? []) > 0)
                <div class="mt-3 text-center">
                    <x-btn :href="route('akademik.jadwal-pelajaran.index')" class="btn-sm btn-light border w-100">
                        Lihat Jadwal Lengkap
                    </x-btn>
                </div>
                @endif
            </x-card>
            @endif
        </div>

        {{-- Right Column: Notifications & Recent Activities --}}
        <div class="col-md-4">
            {{-- Upcoming Events Notification --}}
            <x-card title="Agenda Akademik Terdekat" icon="fas fa-bell" type="warning" outline>
                <div class="list-group list-group-flush mb-2">
                    @forelse($upcomingEvents ?? [] as $event)
                        <div class="list-group-item px-0 py-2 border-bottom">
                            <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                <h6 class="mb-0 font-weight-bold text-truncate" style="max-width: 70%;" title="{{ $event->nama_kegiatan }}">
                                    @if($event->jenisKegiatan?->is_kbm == 0)
                                        <i class="fas fa-exclamation-circle text-danger mr-1" title="Libur / Non KBM"></i>
                                    @else
                                        <i class="fas fa-calendar-day text-success mr-1"></i>
                                    @endif
                                    {{ $event->nama_kegiatan }}
                                </h6>
                                <small class="text-muted font-weight-bold">
                                    {{ \Carbon\Carbon::parse($event->tanggal_awal)->diffForHumans() }}
                                </small>
                            </div>
                            <small class="text-muted d-block">
                                <i class="far fa-clock mr-1"></i> 
                                {{ \Carbon\Carbon::parse($event->tanggal_awal)->translatedFormat('d M Y') }}
                                @if($event->tanggal_awal != $event->tanggal_akhir)
                                 - {{ \Carbon\Carbon::parse($event->tanggal_akhir)->translatedFormat('d M Y') }}
                                @endif
                            </small>
                        </div>
                    @empty
                        <div class="text-center p-3 text-muted">
                            <i class="fas fa-calendar-check fa-2x mb-2 text-light"></i>
                            <p class="mb-0 small">Tidak ada agenda dalam 30 hari ke depan.</p>
                        </div>
                    @endforelse
                </div>
                <a href="{{ route('akademik.kalender-akademik.index') }}" class="btn btn-block btn-sm btn-outline-warning mt-2">Lihat Kalender Penuh</a>
            </x-card>

            @if(auth()->user()->hasRole('SUPER_ADMIN') || auth()->user()->hasRole('STAFF'))
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
            @endif
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
