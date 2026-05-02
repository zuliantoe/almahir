@extends('layouts.app')

@section('title', 'Dashboard Guru Akademik')

@section('content')
<div class="container-fluid">
    {{-- Welcome Card --}}
    <div class="row mb-4">
        <div class="col-12">
            <x-card type="primary" outline>
                <div class="row align-items-center">
                    <div class="col-auto">
                        <img src="{{ Auth::user()->avatar_url ?: 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&color=fff&background=007bff' }}" 
                             class="img-circle elevation-1 border" 
                             style="width: 60px; height: 60px; object-fit: cover;">
                    </div>
                    <div class="col">
                        <h4 class="mb-1 font-weight-bold">Selamat Datang, Ust/Ustz {{ Auth::user()->name }}!</h4>
                        <p class="text-muted mb-0">
                            Status: <span class="badge badge-info">Guru / Pengajar</span>
                            &nbsp;&bull;&nbsp;
                            Silakan pantau jadwal mengajar dan kegiatan akademik hari ini.
                        </p>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    {{-- Jadwal Hari Ini & Agenda --}}
    <div class="row">
        <div class="col-lg-8 mb-4">
            <x-card title="Jadwal Mengajar Hari Ini" icon="fas fa-calendar-day" type="primary" outline>
                @if($jadwalHariIni->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-mug-hot fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Tidak ada jadwal mengajar hari ini.</h5>
                        <p class="text-xs text-muted">Selamat beristirahat atau mengerjakan administrasi lainnya.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center" width="60">Jam</th>
                                    <th>Mata Pelajaran</th>
                                    <th class="text-center">Kelas</th>
                                    <th class="text-center">Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jadwalHariIni as $j)
                                <tr>
                                    <td class="text-center">
                                        <span class="badge badge-primary rounded-circle" style="width: 25px; height: 25px; line-height: 20px;">{{ $j->jamke }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $j->mataPelajaran->nama ?? '-' }}</strong>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-info">{{ $j->rombel->nama_rombel ?? '-' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <small class="text-muted"><i class="far fa-clock mr-1"></i> {{ substr($j->jamawal, 0, 5) }} - {{ substr($j->jamakhir, 0, 5) }}</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-card>
        </div>

        <div class="col-lg-4 mb-4">
            <x-card title="Agenda Sekolah" icon="fas fa-bullhorn" type="info" outline>
                @if($kalender->isEmpty())
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-check fa-2x text-muted mb-2"></i>
                        <p class="text-muted small">Tidak ada agenda dalam waktu dekat.</p>
                    </div>
                @else
                    <div class="list-group list-group-flush">
                        @foreach($kalender as $k)
                        <div class="list-group-item px-0">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1 font-weight-bold">{{ $k->nama_kegiatan }}</h6>
                                <small class="text-info">{{ \Carbon\Carbon::parse($k->tanggal_awal)->translatedFormat('d M') }}</small>
                            </div>
                            <p class="mb-1 text-muted small">{{ Str::limit($k->keterangan, 60) }}</p>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('akademik.kalender-akademik.index', ['view' => 'calendar']) }}" class="btn btn-sm btn-block btn-outline-info">
                            Lihat Kalender Lengkap
                        </a>
                    </div>
                @endif
            </x-card>
        </div>
    </div>

    {{-- Jadwal Mingguan --}}
    <div class="row">
        <div class="col-12">
            <x-card title="Ringkasan Jadwal Mingguan" icon="fas fa-calendar-week" type="secondary" outline>
                @if($jadwalMingguan->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Jadwal mingguan Anda belum tersedia.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center">Hari</th>
                                    <th class="text-center">Jam</th>
                                    <th class="text-center">Waktu</th>
                                    <th>Mata Pelajaran</th>
                                    <th class="text-center">Kelas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jadwalMingguan as $j)
                                <tr>
                                    <td class="text-center font-weight-bold">{{ ucfirst($j->hari) }}</td>
                                    <td class="text-center">{{ $j->jamke }}</td>
                                    <td class="text-center small">{{ substr($j->jamawal, 0, 5) }} - {{ substr($j->jamakhir, 0, 5) }}</td>
                                    <td>{{ $j->mataPelajaran->nama ?? '-' }}</td>
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
