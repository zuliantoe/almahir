@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    {{-- Premium Header Section --}}
    <div class="row mb-4">
        <div class="col-12">
            @if(isset($isSiswa))
                {{-- Siswa Header: Blue Gradient Style --}}
                <div class="card border-0 shadow-lg overflow-hidden animate__animated animate__fadeIn" style="border-radius: 24px; background: linear-gradient(135deg, #4361ee 0%, #3f37c9 100%); color: white;">
                    <div class="card-body p-5">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h1 class="display-4 font-weight-bold mb-3">Assalamualaikum, أهلاً وسهلاً!</h1>
                                <p class="lead mb-4 opacity-75">أهلاً وسهلاً، <strong>{{ auth()->user()->name }}</strong>. Teruslah belajar dan berproses menjadi pribadi yang bermanfaat bagi ummat.</p>
                                <div class="d-flex flex-wrap">
                                    <a href="{{ route('penilaiandanpresensi.presensi.siswa.index') }}" class="btn btn-outline-light btn-lg px-4 mr-3 mb-2" style="border-radius: 50px;">
                                        <i class="fas fa-fingerprint mr-2"></i> Absensi Saya
                                    </a>
                                    <a href="{{ route('penilaiandanpresensi.penilaianakademik.index') }}" class="btn btn-outline-light btn-lg px-4 mb-2" style="border-radius: 50px;">
                                        <i class="fas fa-graduation-cap mr-2"></i> Lihat Nilai
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-4 text-center d-none d-md-block">
                                <i class="fas fa-user-graduate fa-10x opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                {{-- Guru/Admin Header: Clean Professional Style --}}
                <div class="card border-0 shadow-sm overflow-hidden animate__animated animate__fadeIn" style="border-radius: 24px; background: white; border-left: 10px solid #4361ee !important;">
                    <div class="card-body p-5">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h1 class="display-4 font-weight-bold text-primary mb-3">Assalamualaikum, أهلاً وسهلاً!</h1>
                                <p class="lead text-muted mb-4">أهلاً وسهلاً، <strong>{{ auth()->user()->name }}</strong>. Mari kita mulai hari ini dengan semangat dan ikhlas untuk mendidik generasi rabbani.</p>
                                <div class="d-flex flex-wrap">
                                    <a href="{{ route('penilaiandanpresensi.presensi.index') }}" class="btn btn-primary btn-lg px-4 mr-3 mb-2 shadow-sm" style="border-radius: 50px;">
                                        <i class="fas fa-list-check mr-2"></i> Rekap Presensi
                                    </a>
                                    <a href="{{ route('penilaiandanpresensi.penilaianakademik.create') }}" class="btn btn-outline-primary btn-lg px-4 mb-2" style="border-radius: 50px;">
                                        <i class="fas fa-edit mr-2"></i> Input Nilai
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-4 text-center d-none d-md-block">
                                <i class="fas fa-chalkboard-teacher fa-10x text-primary opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Stats Grid for Siswa --}}
    @if(isset($isSiswa))
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center p-4" style="border-radius: 20px;">
                <div class="icon-circle bg-primary-light text-primary mb-3 mx-auto">
                    <i class="fas fa-star fa-lg"></i>
                </div>
                <h3 class="font-weight-bold mb-0 text-dark">{{ $penilaianAkademik->avg('nilai') ? number_format($penilaianAkademik->avg('nilai'), 1) : '-' }}</h3>
                <p class="text-muted small mb-0">Rata-rata Nilai</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center p-4" style="border-radius: 20px;">
                <div class="icon-circle bg-success-light text-success mb-3 mx-auto">
                    <i class="fas fa-calendar-check fa-lg"></i>
                </div>
                @php 
                    $totalP = array_sum($statsPresensi);
                    $percent = $totalP > 0 ? round(($statsPresensi['hadir'] / $totalP) * 100) : 0;
                @endphp
                <h3 class="font-weight-bold mb-0 text-dark">{{ $percent }}%</h3>
                <p class="text-muted small mb-0">Tingkat Kehadiran</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center p-4" style="border-radius: 20px;">
                <div class="icon-circle bg-info-light text-info mb-3 mx-auto">
                    <i class="fas fa-award fa-lg"></i>
                </div>
                <h3 class="font-weight-bold mb-0 text-dark">-</h3>
                <p class="text-muted small mb-0">Peringkat Kelas</p>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        {{-- Presensi Today --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="fas fa-chart-pie mr-2 text-primary"></i> {{ isset($isSiswa) ? 'Statistik Kehadiran Saya' : 'Statistik Presensi Hari Ini' }}</h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="d-flex flex-column gap-3">
                                @php
                                    $totalPresensi = array_sum($statsPresensi);
                                    $divisor = max(1, $totalPresensi);
                                @endphp
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted small font-weight-bold"><i class="fas fa-check-circle text-success mr-2"></i> Hadir</span>
                                    <span class="badge badge-success px-2">{{ $statsPresensi['hadir'] }}</span>
                                </div>
                                <div class="progress mb-3" style="height: 8px; border-radius: 4px;">
                                    <div class="progress-bar bg-success" style="width: {{ ($statsPresensi['hadir'] / $divisor) * 100 }}%"></div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted small font-weight-bold"><i class="fas fa-info-circle text-info mr-2"></i> Izin</span>
                                    <span class="badge badge-info px-2">{{ $statsPresensi['izin'] }}</span>
                                </div>
                                <div class="progress mb-3" style="height: 8px; border-radius: 4px;">
                                    <div class="progress-bar bg-info" style="width: {{ ($statsPresensi['izin'] / $divisor) * 100 }}%"></div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted small font-weight-bold"><i class="fas fa-hand-holding-medical text-warning mr-2"></i> Sakit</span>
                                    <span class="badge badge-warning px-2">{{ $statsPresensi['sakit'] }}</span>
                                </div>
                                <div class="progress mb-3" style="height: 8px; border-radius: 4px;">
                                    <div class="progress-bar bg-warning text-white" style="width: {{ ($statsPresensi['sakit'] / $divisor) * 100 }}%"></div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted small font-weight-bold"><i class="fas fa-times-circle text-danger mr-2"></i> Alpha</span>
                                    <span class="badge badge-danger px-2">{{ $statsPresensi['alpha'] }}</span>
                                </div>
                                <div class="progress" style="height: 8px; border-radius: 4px;">
                                    <div class="progress-bar bg-danger" style="width: {{ ($statsPresensi['alpha'] / $divisor) * 100 }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-center">
                            <div class="p-4 bg-light rounded-lg border-dashed">
                                <h1 class="font-weight-bold text-dark display-4">{{ $totalPresensi }}</h1>
                                <p class="text-muted mb-0 font-weight-bold">Total Entri Presensi</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Penilaian Terbaru --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="fas fa-star mr-2 text-warning"></i> Penilaian Terbaru</h5>
                    <ul class="nav nav-pills nav-pills-custom" id="pills-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active py-1 px-3 small" id="pills-akademik-tab" data-toggle="pill" href="#pills-akademik" role="tab">Akademik</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link py-1 px-3 small" id="pills-tahfidz-tab" data-toggle="pill" href="#pills-tahfidz" role="tab">Tahfidz</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-0">
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-akademik" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr class="text-muted small">
                                            <th class="border-0 px-4">Santri</th>
                                            <th class="border-0">Mapel</th>
                                            <th class="border-0 text-center">Nilai</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($penilaianAkademik as $pen)
                                        <tr>
                                            <td class="px-4 font-weight-bold text-dark small">{{ $pen->siswa->nama ?? '-' }}</td>
                                            <td class="small">{{ $pen->mataPelajaran->nama ?? '-' }}</td>
                                            <td class="text-center">
                                                <span class="badge badge-{{ $pen->nilai >= ($pen->kkm ?? 75) ? 'success' : 'danger' }} shadow-sm">
                                                    {{ $pen->nilai }}
                                                </span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="3" class="text-center py-4 small text-muted">Belum ada data penilaian.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-tahfidz" role="tabpanel">
                             <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr class="text-muted small">
                                            <th class="border-0 px-4">Santri</th>
                                            <th class="border-0">Surat</th>
                                            <th class="border-0 text-center">Hasil</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($penilaianTahfidz as $tah)
                                        <tr>
                                            <td class="px-4 font-weight-bold text-dark small">{{ $tah->siswa->nama ?? '-' }}</td>
                                            <td class="small">{{ $tah->surat_awal }} - {{ $tah->surat_akhir }}</td>
                                            <td class="text-center">
                                                <span class="badge badge-{{ $tah->status_capaian == 'Lolos' ? 'success' : 'danger' }} shadow-sm">
                                                    {{ $tah->status_capaian }}
                                                </span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="3" class="text-center py-4 small text-muted">Belum ada data tahfidz.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pending Requests --}}
        <div class="col-md-4">
            @if(!isset($isSiswa))
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="fas fa-clock mr-2 text-danger"></i> Menunggu Konfirmasi</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($pendingIzin ?? [] as $izin)
                        <div class="list-group-item border-0 px-4 py-3 bg-hover-light">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="font-weight-bold text-dark small">{{ $izin->siswa->nama ?? '-' }}</span>
                                <span class="badge badge-light-danger text-danger px-2 py-1 small" style="font-size: 0.65rem;">{{ $izin->jenis }}</span>
                            </div>
                            <p class="text-muted small mb-2 mb-0 truncate-2">{{ $izin->keterangan }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted"><i class="far fa-calendar mr-1"></i> {{ $izin->tgl_mulai->format('d M Y') }}</small>
                                <a href="{{ route('penilaiandanpresensi.izinsakit.index') }}" class="btn btn-sm btn-outline-danger px-3" style="border-radius: 10px; font-size: 0.7rem;">Konfirmasi</a>
                            </div>
                        </div>
                        @empty
                        <div class="p-5 text-center">
                            <i class="fas fa-check-circle fa-2x text-success opacity-20 mb-2"></i>
                            <p class="text-muted small mb-0">Semua pengajuan telah dikonfirmasi.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
                @if(isset($pendingIzin) && $pendingIzin->count() > 0)
                <div class="card-footer bg-white border-0 text-center pb-4">
                    <a href="{{ route('penilaiandanpresensi.izinsakit.index') }}" class="btn btn-light btn-sm font-weight-bold text-primary px-4" style="border-radius: 10px;">Lihat Semua</a>
                </div>
                @endif
            </div>
            @endif

            {{-- Quick Links --}}
            <div class="card border-0 shadow-sm" style="border-radius: 20px; background: #f8fafc;">
                <div class="card-body p-4">
                    <h6 class="font-weight-bold text-dark mb-3">Tautan Cepat</h6>
                    <div class="d-grid gap-2">
                        @if(isset($isSiswa))
                        <a href="{{ route('penilaiandanpresensi.presensi.siswa.index') }}" class="btn btn-white btn-block text-left shadow-sm mb-2 border-0" style="border-radius: 12px;">
                            <i class="fas fa-fingerprint text-primary mr-3"></i> Absensi Saya
                        </a>
                        <a href="{{ route('penilaiandanpresensi.penilaianakademik.index') }}" class="btn btn-white btn-block text-left shadow-sm mb-2 border-0" style="border-radius: 12px;">
                            <i class="fas fa-graduation-cap text-success mr-3"></i> Lihat Nilai
                        </a>
                        @else
                        <a href="{{ route('penilaiandanpresensi.presensi.index') }}" class="btn btn-white btn-block text-left shadow-sm mb-2 border-0" style="border-radius: 12px;">
                            <i class="fas fa-qrcode text-primary mr-3"></i> Rekap Presensi
                        </a>
                        <a href="{{ route('penilaiandanpresensi.penilaianakademik.create') }}" class="btn btn-white btn-block text-left shadow-sm mb-2 border-0" style="border-radius: 12px;">
                            <i class="fas fa-edit text-success mr-3"></i> Input Nilai
                        </a>
                        @endif
                        <a href="{{ route('penilaiandanpresensi.index') }}" class="btn btn-white btn-block text-left shadow-sm border-0" style="border-radius: 12px;">
                            <i class="fas fa-chart-line text-muted mr-3"></i> Statistik Penilaian
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .icon-circle {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
    }
    .bg-primary-light { background-color: rgba(99, 102, 241, 0.1); }
    .bg-success-light { background-color: rgba(34, 197, 94, 0.1); }
    .bg-warning-light { background-color: rgba(234, 179, 8, 0.1); }
    .bg-danger-light { background-color: rgba(239, 68, 68, 0.1); }
    .bg-light-danger { background-color: rgba(239, 68, 68, 0.05); }
    .bg-info-light { background-color: rgba(6, 182, 212, 0.1); }
    
    .opacity-10 { opacity: 0.1; }
    .opacity-20 { opacity: 0.2; }
    .opacity-25 { opacity: 0.25; }

    .btn-white:hover { 
        background: #f8fafc !important; 
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
    }

    .nav-pills-custom .nav-link {
        border-radius: 8px;
        color: #64748b;
        background: #f1f5f9;
        margin-left: 5px;
    }
    .nav-pills-custom .nav-link.active {
        background: #6366f1;
        color: white;
        box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.2);
    }
    .bg-hover-light:hover {
        background-color: #f8fafc;
    }
    .truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .border-dashed {
        border: 2px dashed #e2e8f0;
    }
    .rounded-lg { border-radius: 1rem; }
    .text-indigo { color: #4338ca; }
</style>
@endsection
