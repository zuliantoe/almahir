@extends('layouts.app')

@section('title', 'Dashboard Siswa Akademik')

@section('content')
<div class="container-fluid">
    {{-- Premium Welcome Card --}}
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow-lg border-0 overflow-hidden" style="border-radius: 16px; background: linear-gradient(135deg, #0093E9 0%, #80D0C7 100%);">
                <div class="card-body p-4 position-relative">
                    <div style="position: absolute; bottom: -40px; left: -40px; width: 180px; height: 180px; background: rgba(255,255,255,0.15); border-radius: 50%; blur: 25px;"></div>
                    <div class="row align-items-center position-relative z-index-1">
                        <div class="col-auto d-none d-md-block">
                            <img src="{{ Auth::user()->avatar_url ?: 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&color=fff&background=17a2b8' }}" 
                                 class="img-circle elevation-2 border-white shadow-sm" 
                                 style="width: 80px; height: 80px; object-fit: cover; border: 3px solid rgba(255,255,255,0.6);">
                        </div>
                        <div class="col text-white">
                            <h3 class="font-weight-bold mb-1" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.1);">
                                Ahlan wa Sahlan, {{ Auth::user()->name }}!
                            </h3>
                            <p class="text-white mb-0" style="font-size: 1.1rem; opacity: 0.9;">
                                Saat ini Anda login sebagai <span class="badge badge-light border-0 px-3 py-2 text-info shadow-sm rounded-pill">Santri / Siswa</span> 
                                @if($rombelSiswa && $rombelSiswa->rombel)
                                    di kelas <span class="font-weight-bold" style="text-decoration: underline;">{{ $rombelSiswa->rombel->nama_rombel }}</span>
                                @endif
                                <span class="d-none d-lg-inline ml-2 pl-2" style="border-left: 1px solid rgba(255,255,255,0.3);">Semangat belajarnya hari ini ya!</span>
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
            <x-card title="Jadwal Belajar Hari Ini" icon="fas fa-book-open" type="white" class="shadow-sm border-0" style="border-radius: 12px;">
                <x-slot name="header">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <h3 class="card-title font-weight-bold text-dark"><i class="fas fa-user-graduate mr-2 text-info"></i> Jadwal Pelajaran ({{ ucfirst($today) }})</h3>
                        <span class="badge badge-info px-3 py-2 rounded-pill shadow-xs">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span>
                    </div>
                </x-slot>
                @if($jadwalHariIni->isEmpty())
                    <div class="text-center py-5">
                        <div class="bg-light d-inline-block rounded-circle p-4 mb-3">
                            <i class="fas fa-umbrella-beach fa-3x text-muted opacity-50"></i>
                        </div>
                        <h5 class="text-muted font-weight-light">Alhamdulillah, tidak ada jadwal pelajaran hari ini.</h5>
                        <p class="text-xs text-muted">Ayo manfaatkan waktu untuk murojaah atau istirahat.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-borderless table-hover">
                            <thead>
                                <tr class="text-muted small text-uppercase" style="letter-spacing: 1px;">
                                    <th class="text-center border-bottom pb-3">Jam</th>
                                    <th class="border-bottom pb-3">Mata Pelajaran</th>
                                    <th class="border-bottom pb-3">Guru / Pengajar</th>
                                    <th class="text-center border-bottom pb-3">Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jadwalHariIni as $j)
                                <tr class="align-middle">
                                    <td class="text-center py-3">
                                        <div class="text-info font-weight-bold rounded-circle mx-auto d-flex align-items-center justify-content-center shadow-xs" style="width: 35px; height: 35px; background: rgba(23,162,184,0.1); border: 1px solid rgba(23,162,184,0.2);">
                                            {{ $j->jamke }}
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <span class="d-block font-weight-bold text-dark">{{ $j->mataPelajaran->nama ?? '-' }}</span>
                                        <small class="text-muted text-xs"><i class="fas fa-tag mr-1"></i> Akademik</small>
                                    </td>
                                    <td class="py-3">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user-tie text-muted mr-2"></i>
                                            <span class="text-dark">{{ $j->guru->nama ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center py-3">
                                        <div class="text-muted small font-weight-bold">
                                            <i class="far fa-clock mr-1 text-info"></i> {{ substr($j->jamawal, 0, 5) }} - {{ substr($j->jamakhir, 0, 5) }}
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
            <x-card title="Info Madrasah" icon="fas fa-bullhorn" type="white" class="shadow-sm border-0 h-100" style="border-radius: 12px;">
                @if($kalender->isEmpty())
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-check fa-2x text-muted mb-2"></i>
                        <p class="text-muted small">Tidak ada agenda sekolah dalam waktu dekat.</p>
                    </div>
                @else
                    <div class="timeline mt-3">
                        @foreach($kalender as $k)
                        <div class="mb-3 border-left pl-3 ml-2" style="border-left-width: 3px !important; border-color: #17a2b8 !important;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge badge-light text-info font-weight-bold" style="font-size: 0.75rem;">
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
            <x-card title="Jadwal Pelajaran Seluruh Pekan" icon="fas fa-calendar-week" type="white" class="shadow-sm border-0" style="border-radius: 12px;">
                @if($jadwalMingguan->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Jadwal mingguan kelas Anda belum tersedia.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="bg-info text-white">
                                <tr>
                                    <th class="text-center" style="border-top-left-radius: 10px;">Hari</th>
                                    <th class="text-center">Jam</th>
                                    <th class="text-center">Waktu</th>
                                    <th>Mata Pelajaran</th>
                                    <th class="text-center" style="border-top-right-radius: 10px;">Guru / Pengajar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jadwalMingguan as $j)
                                <tr>
                                    <td class="text-center font-weight-bold text-info border-right" style="background: rgba(23,162,184,0.02);">{{ ucfirst($j->hari) }}</td>
                                    <td class="text-center">{{ $j->jamke }}</td>
                                    <td class="text-center small">{{ substr($j->jamawal, 0, 5) }} - {{ substr($j->jamakhir, 0, 5) }}</td>
                                    <td class="font-weight-bold">{{ $j->mataPelajaran->nama ?? '-' }}</td>
                                    <td class="text-center py-2">
                                        <span class="badge badge-light border text-dark px-3 mt-1">{{ $j->guru->nama ?? '-' }}</span>
                                    </td>
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
