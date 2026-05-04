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
            {{-- Alert Kalender Akademik Hari Ini --}}
            @if($eventHariIni->isNotEmpty())
                @foreach($eventHariIni as $event)
                <div class="alert alert-{{ $event->jenisKegiatan->is_kbm ? 'info' : 'warning' }} shadow-sm border-0 mb-3" role="alert">
                    <div class="d-flex align-items-center">
                        <div class="mr-3">
                            <i class="fas {{ $event->jenisKegiatan->is_kbm ? 'fa-info-circle' : 'fa-calendar-day' }} fa-2x opacity-50"></i>
                        </div>
                        <div>
                            <h5 class="alert-heading mb-1 font-weight-bold">
                                {{ $event->nama_kegiatan }} 
                                @if(!$event->jenisKegiatan->is_kbm) <span class="badge badge-danger ml-2">KBM LIBUR</span> @endif
                            </h5>
                            <p class="mb-0">{{ $event->deskripsi ?: 'Kegiatan sekolah hari ini.' }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            @endif

            <x-card title="Jadwal Belajar Hari Ini" icon="fas fa-book-reader" type="info" outline>
                @if($jadwalHariIni->isEmpty())
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <span class="fa-stack fa-2x">
                                <i class="fas fa-circle fa-stack-2x text-light"></i>
                                <i class="fas fa-mug-hot fa-stack-1x text-muted"></i>
                            </span>
                        </div>
                        <h5 class="text-muted font-weight-bold">Alhamdulillah, tidak ada jadwal pelajaran hari ini.</h5>
                        <p class="text-muted small">Ayo manfaatkan waktu untuk murojaah, istirahat, atau hobi bermanfaat lainnya.</p>
                    </div>
                @else
                    {{-- Status KBM --}}
                    @php
                        $isLibur = $eventHariIni->where('jenisKegiatan.is_kbm', false)->isNotEmpty();
                    @endphp

                    @if($isLibur)
                        <div class="text-center py-5 bg-light rounded border mb-0">
                            <i class="fas fa-umbrella-beach fa-4x text-warning mb-3"></i>
                            <h5 class="font-weight-bold text-dark">KBM Diliburkan Sementara</h5>
                            <p class="text-muted px-4">Jadwal pelajaran hari ini tidak berlaku karena adanya kegiatan/agenda sekolah di atas.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-muted small">
                                    <tr>
                                        <th class="text-center border-0" width="80">SESI</th>
                                        <th class="border-0">MATA PELAJARAN</th>
                                        <th class="border-0">PENGAJAR</th>
                                        <th class="text-center border-0">WAKTU</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($jadwalHariIni as $j)
                                    <tr class="transition-all hover-shadow">
                                        <td class="text-center align-middle">
                                            <div class="h5 mb-0 font-weight-bold text-info">{{ $j->jamke }}</div>
                                            <small class="text-uppercase text-muted" style="font-size: 0.65rem;">Jam Ke</small>
                                        </td>
                                        <td class="align-middle">
                                            <div class="font-weight-bold text-dark h6 mb-0">{{ $j->mataPelajaran->nama ?? '-' }}</div>
                                            <div class="text-muted small">ID: {{ $j->mataPelajaran->kode ?? '-' }}</div>
                                        </td>
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light rounded-circle p-2 mr-2 text-info" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-user-tie fa-sm"></i>
                                                </div>
                                                <span class="small font-weight-bold">{{ $j->guru->nama ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <div class="badge badge-light border px-3 py-2 text-info">
                                                <i class="far fa-clock mr-1"></i> {{ substr($j->jamawal, 0, 5) }} - {{ substr($j->jamakhir, 0, 5) }}
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
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
                        <table class="table table-hover align-middle border">
                            <thead class="bg-light text-muted small">
                                <tr>
                                    <th class="text-center border-0" width="120">HARI</th>
                                    <th class="text-center border-0" width="80">SESI</th>
                                    <th class="text-center border-0" width="150">WAKTU</th>
                                    <th class="border-0">MATA PELAJARAN</th>
                                    <th class="border-0">PENGAJAR</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $currentDay = null; @endphp
                                @foreach($jadwalMingguan as $j)
                                    @if($currentDay !== $j->hari)
                                        <tr class="bg-light-info">
                                            <td colspan="5" class="py-2 px-3">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-calendar-day text-info mr-2"></i>
                                                    <span class="font-weight-bold text-info text-uppercase" style="letter-spacing: 1px;">{{ $j->hari }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                        @php $currentDay = $j->hari; @endphp
                                    @endif
                                    <tr class="border-bottom">
                                        <td class="text-center text-muted small">—</td>
                                        <td class="text-center font-weight-bold text-secondary">{{ $j->jamke }}</td>
                                        <td class="text-center">
                                            <span class="badge badge-light border text-muted px-2 py-1">
                                                <i class="far fa-clock mr-1"></i> {{ substr($j->jamawal, 0, 5) }} - {{ substr($j->jamakhir, 0, 5) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="font-weight-bold text-dark">{{ $j->mataPelajaran->nama ?? '-' }}</div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light rounded-circle p-1 mr-2 text-secondary" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-user-tie fa-xs"></i>
                                                </div>
                                                <span class="small">{{ $j->guru->nama ?? '-' }}</span>
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
    </div>
</div>
@endsection
