@extends('layouts.app')

@section('title', 'Dashboard Siswa Akademik')

@section('content')
<div class="container-fluid">
    {{-- Welcome Card --}}
    <div class="row mb-4">
        <div class="col-12">
            <x-card type="info" outline>
                <div class="row align-items-center">
                    <div class="col-auto">
                        <img src="{{ Auth::user()->avatar_url ?: 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&color=fff&background=17a2b8' }}" 
                             class="img-circle elevation-1 border" 
                             style="width: 60px; height: 60px; object-fit: cover;">
                    </div>
                    <div class="col">
                        <h4 class="mb-1 font-weight-bold">Ahlan wa Sahlan, {{ Auth::user()->name }}!</h4>
                        <p class="text-muted mb-0">
                            Status: <span class="badge badge-info">Santri / Siswa</span>
                            @if($rombelSiswa && $rombelSiswa->rombel)
                                &nbsp;&bull;&nbsp;
                                Kelas: <strong>{{ $rombelSiswa->rombel->nama_rombel }}</strong>
                            @endif
                            &nbsp;&bull;&nbsp;
                            Semangat belajarnya hari ini ya!
                        </p>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    {{-- Jadwal Hari Ini & Agenda --}}
    <div class="row">
        <div class="col-lg-8 mb-4">
            <x-card title="Jadwal Belajar Hari Ini" icon="fas fa-book-open" type="info" outline>
                @if($jadwalHariIni->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-umbrella-beach fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Alhamdulillah, tidak ada jadwal pelajaran hari ini.</h5>
                        <p class="text-xs text-muted">Ayo manfaatkan waktu untuk murojaah atau istirahat.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center" width="60">Jam</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Guru / Pengajar</th>
                                    <th class="text-center">Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jadwalHariIni as $j)
                                <tr>
                                    <td class="text-center">
                                        <span class="badge badge-info rounded-circle" style="width: 25px; height: 25px; line-height: 20px;">{{ $j->jamke }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $j->mataPelajaran->nama ?? '-' }}</strong>
                                    </td>
                                    <td>
                                        <small><i class="fas fa-user-tie text-muted mr-1"></i> {{ $j->guru->nama ?? '-' }}</small>
                                    </td>
                                    <td class="text-center">
                                        <small class="text-muted font-weight-bold">
                                            <i class="far fa-clock mr-1 text-info"></i> {{ substr($j->jamawal, 0, 5) }} - {{ substr($j->jamakhir, 0, 5) }}
                                        </small>
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
            <x-card title="Info Madrasah" icon="fas fa-bullhorn" type="primary" outline>
                @if($kalender->isEmpty())
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-check fa-2x text-muted mb-2"></i>
                        <p class="text-muted small">Tidak ada agenda sekolah dalam waktu dekat.</p>
                    </div>
                @else
                    <div class="list-group list-group-flush">
                        @foreach($kalender as $k)
                        <div class="list-group-item px-0">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1 font-weight-bold small text-info">{{ $k->nama_kegiatan }}</h6>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($k->tanggal_awal)->translatedFormat('d M') }}</small>
                            </div>
                            <p class="mb-1 text-muted small">{{ Str::limit($k->keterangan, 60) }}</p>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('akademik.kalender-akademik.index', ['view' => 'calendar']) }}" class="btn btn-sm btn-block btn-outline-primary">
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
                        <p class="text-muted">Jadwal mingguan kelas Anda belum tersedia.</p>
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
                                    <th class="text-center">Guru / Pengajar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jadwalMingguan as $j)
                                <tr>
                                    <td class="text-center font-weight-bold">{{ ucfirst($j->hari) }}</td>
                                    <td class="text-center">{{ $j->jamke }}</td>
                                    <td class="text-center small">{{ substr($j->jamawal, 0, 5) }} - {{ substr($j->jamakhir, 0, 5) }}</td>
                                    <td class="font-weight-bold">{{ $j->mataPelajaran->nama ?? '-' }}</td>
                                    <td class="text-center">
                                        <span class="small">{{ $j->guru->nama ?? '-' }}</span>
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
