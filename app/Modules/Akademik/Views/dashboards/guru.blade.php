@extends('layouts.app')

@section('title', 'Dashboard Guru Akademik')

@section('content')
<div class="container-fluid">
    {{-- Premium Welcome Card --}}
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow-lg border-0 overflow-hidden" style="border-radius: 16px; background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                <div class="card-body p-4 position-relative">
                    <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%; blur: 20px;"></div>
                    <div class="row align-items-center position-relative z-index-1">
                        <div class="col-auto d-none d-md-block">
                            <img src="{{ Auth::user()->avatar_url ?: 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&color=fff&background=28a745' }}" 
                                 class="img-circle elevation-2 border-white" 
                                 style="width: 80px; height: 80px; object-fit: cover; border: 3px solid rgba(255,255,255,0.5);">
                        </div>
                        <div class="col text-white">
                            <h3 class="font-weight-bold mb-1" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">
                                Selamat Datang, Ust/Ustz {{ Auth::user()->name }}!
                            </h3>
                            <p class="text-white-50 mb-0" style="font-size: 1.1rem;">
                                Saat ini Anda login sebagai <span class="badge badge-light border-0 px-3 py-2 text-primary shadow-sm rounded-pill">Guru / Pengajar</span>
                                <span class="d-none d-lg-inline ml-2 pl-2" style="border-left: 1px solid rgba(255,255,255,0.3);">Silakan pantau jadwal mengajar dan kegiatan akademik hari ini.</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Jadwal Hari Ini & Kalender Row --}}
    <div class="row">
        <div class="col-lg-8">
            <x-card title="Jadwal Mengajar Hari Ini" icon="fas fa-calendar-day" type="white" class="shadow-sm border-0" style="border-radius: 12px;">
                <x-slot name="header">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <h3 class="card-title font-weight-bold text-dark"><i class="fas fa-chalkboard-teacher mr-2 text-primary"></i> Jadwal Mengajar ({{ ucfirst($today) }})</h3>
                        <span class="badge badge-primary px-3 py-2 rounded-pill shadow-xs">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span>
                    </div>
                </x-slot>
                @if($jadwalHariIni->isEmpty())
                    <div class="text-center py-5">
                        <div class="bg-light d-inline-block rounded-circle p-4 mb-3">
                            <i class="fas fa-mug-hot fa-3x text-muted opacity-50"></i>
                        </div>
                        <h5 class="text-muted font-weight-light">Tenang Ustadz/Ustadzah, tidak ada jadwal mengajar hari ini.</h5>
                        <p class="text-xs text-muted">Selamat beristirahat atau mengerjakan administrasi lainnya.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-borderless table-hover">
                            <thead>
                                <tr class="text-muted small text-uppercase" style="letter-spacing: 1px;">
                                    <th class="text-center border-bottom pb-3">Jam</th>
                                    <th class="border-bottom pb-3">Mata Pelajaran</th>
                                    <th class="text-center border-bottom pb-3">Kelas</th>
                                    <th class="text-center border-bottom pb-3">Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jadwalHariIni as $j)
                                <tr class="align-middle">
                                    <td class="text-center py-3">
                                        <div class="bg-primary-soft text-primary font-weight-bold rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; background: rgba(0,123,255,0.1);">
                                            {{ $j->jamke }}
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <span class="d-block font-weight-bold text-dark">{{ $j->mataPelajaran->nama ?? '-' }}</span>
                                        <small class="text-muted"><i class="fas fa-book-reader mr-1"></i> Akademik</small>
                                    </td>
                                    <td class="text-center py-3">
                                        <span class="badge badge-outline-info border-info text-info px-3 py-2 rounded-pill" style="border: 1px solid;">
                                            {{ $j->rombel->nama_rombel ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="text-center py-3">
                                        <div class="text-muted small font-weight-bold">
                                            <i class="far fa-clock mr-1"></i> {{ substr($j->jamawal, 0, 5) }} - {{ substr($j->jamakhir, 0, 5) }}
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-card>
        </div>

        <div class="col-lg-4">
            <x-card title="Agenda Sekolah" icon="fas fa-bullhorn" type="white" class="shadow-sm border-0 h-100" style="border-radius: 12px;">
                @if($kalender->isEmpty())
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-check fa-2x text-muted mb-2"></i>
                        <p class="text-muted small">Tidak ada agenda dalam waktu dekat.</p>
                    </div>
                @else
                    <div class="timeline timeline-inverse">
                        @foreach($kalender as $k)
                        <div class="mb-3 border-left pl-3 ml-2" style="border-left-width: 3px !important; border-color: #ffc107 !important;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge badge-light text-warning font-weight-bold" style="font-size: 0.75rem;">
                                    {{ \Carbon\Carbon::parse($k->tanggal_awal)->translatedFormat('d M') }}
                                </span>
                            </div>
                            <h6 class="font-weight-bold text-dark mb-1" style="font-size: 0.9rem;">{{ $k->nama_kegiatan }}</h6>
                            <p class="text-muted text-xs mb-0">{{ Str::limit($k->keterangan, 60) }}</p>
                        </div>
                        @endforeach
                    </div>
                @endif
            </x-card>
        </div>
    </div>

    {{-- Jadwal Mingguan --}}
    <div class="row mt-4">
        <div class="col-12">
            <x-card title="Jadwal Mengajar Seluruh Pekan" icon="fas fa-calendar-week" type="white" class="shadow-sm border-0" style="border-radius: 12px;">
                @if($jadwalMingguan->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Jadwal mingguan Anda belum tersedia.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th class="text-center" style="border-top-left-radius: 10px;">Hari</th>
                                    <th class="text-center">Jam</th>
                                    <th class="text-center">Waktu</th>
                                    <th>Mata Pelajaran</th>
                                    <th class="text-center" style="border-top-right-radius: 10px;">Kelas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jadwalMingguan as $j)
                                <tr>
                                    <td class="text-center font-weight-bold text-primary">{{ ucfirst($j->hari) }}</td>
                                    <td class="text-center">{{ $j->jamke }}</td>
                                    <td class="text-center small">{{ substr($j->jamawal, 0, 5) }} - {{ substr($j->jamakhir, 0, 5) }}</td>
                                    <td class="font-weight-bold">{{ $j->mataPelajaran->nama ?? '-' }}</td>
                                    <td class="text-center"><span class="badge badge-info">{{ $j->rombel->nama_rombel ?? '-' }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-card>
        </div>
    </div>
</div>
@endsection
