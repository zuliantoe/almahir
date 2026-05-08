@extends('layouts.app')

@section('title', 'Dashboard Guru - Akademik')

@section('content')
<div class="container-fluid">
    {{-- Content Header --}}
    <div class="row mb-3">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Dashboard Guru</h1>
        </div>
        <div class="col-sm-6 text-right">
            <span class="badge badge-primary px-3 py-2">
                <i class="fas fa-calendar-alt mr-1"></i> {{ $today }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
            </span>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-chalkboard-teacher"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Sesi Mengajar</span>
                    <span class="info-box-number">{{ $jadwalHariIni->count() }} <small>Hari Ini</small></span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-calendar-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Agenda Berjalan</span>
                    <span class="info-box-number">{{ $eventHariIni->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-graduation-cap"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Semester</span>
                    <span class="info-box-number">{{ $tahunAjaranAktif->semester ?? '-' }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Tahun Ajaran</span>
                    <span class="info-box-number">{{ $tahunAjaranAktif->tahunajaran ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="row">
        <div class="col-md-8">
            {{-- Welcome & Active Events --}}
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-0">
                        <img src="{{ Auth::user()->avatar_url ?: 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&color=fff&background=007bff' }}" 
                             class="img-circle border elevation-1 mr-3" 
                             style="width: 50px; height: 50px;">
                        <div>
                            <h5 class="mb-0 font-weight-bold text-primary">Ahlan wa Sahlan, Ust/Ustz {{ Auth::user()->name }}!</h5>
                            <p class="text-muted small mb-0">Mari kita mulai hari ini dengan semangat mengabdi untuk ummat.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ongoing Events --}}
            @if($eventHariIni->isNotEmpty())
                @foreach($eventHariIni as $event)
                <div class="callout callout-{{ $event->jenisKegiatan->is_kbm ? 'info' : 'warning' }} shadow-sm">
                    <h5><i class="fas {{ $event->jenisKegiatan->is_kbm ? 'fa-info-circle' : 'fa-exclamation-triangle' }} mr-2"></i> {{ $event->nama_kegiatan }}</h5>
                    <p>{{ $event->deskripsi ?: 'Agenda akademik sedang berlangsung hari ini.' }}</p>
                    @if(!$event->jenisKegiatan->is_kbm)
                        <span class="badge badge-danger">KBM LIBUR</span>
                    @endif
                </div>
                @endforeach
            @endif

            {{-- Schedule Table --}}
            <div class="card shadow-sm">
                <div class="card-header border-0">
                    <h3 class="card-title"><i class="fas fa-clock mr-1"></i> Jadwal Mengajar Hari Ini</h3>
                </div>
                <div class="card-body p-0">
                    @php $isLibur = $eventHariIni->where('jenisKegiatan.is_kbm', false)->isNotEmpty(); @endphp
                    @if($jadwalHariIni->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-mug-hot fa-3x mb-3 opacity-25"></i>
                            <p class="mb-0">Tidak ada jadwal mengajar hari ini.</p>
                        </div>
                    @elseif($isLibur)
                        <div class="text-center py-5 text-warning">
                            <i class="fas fa-user-slash fa-3x mb-3"></i>
                            <h5>KBM Sedang Diliburkan</h5>
                            <p class="text-muted">Jadwal hari ini ditangguhkan karena agenda sekolah.</p>
                        </div>
                    @else
                        <table class="table table-striped table-valign-middle">
                            <thead>
                                <tr>
                                    <th class="text-center" width="80">SESI</th>
                                    <th>MATA PELAJARAN</th>
                                    <th class="text-center">KELAS</th>
                                    <th class="text-center">WAKTU</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jadwalHariIni as $j)
                                <tr>
                                    <td class="text-center font-weight-bold text-primary">{{ $j->jamke }}</td>
                                    <td>
                                        <div class="font-weight-bold">{{ $j->mataPelajaran->nama ?? '-' }}</div>
                                        <small class="text-muted">ID: {{ $j->mataPelajaran->kode ?? '-' }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-info">{{ $j->rombel->nama_rombel ?? '-' }}</span>
                                    </td>
                                    <td class="text-center text-muted small">
                                        <i class="far fa-clock mr-1"></i> {{ substr($j->jamawal, 0, 5) }} - {{ substr($j->jamakhir, 0, 5) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            {{-- Upcoming Agenda --}}
            <div class="card shadow-sm">
                <div class="card-header bg-info">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-bullhorn mr-2"></i> Agenda Mendatang</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="nav nav-pills flex-column">
                        @forelse($upcomingEvents as $event)
                        <li class="nav-item p-3 border-bottom">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <span class="badge badge-info px-2 py-1">
                                    {{ \Carbon\Carbon::parse($event->tanggal_awal)->translatedFormat('d M') }}
                                </span>
                                <small class="text-muted">
                                    @php
                                        $targetDate = \Carbon\Carbon::parse($event->tanggal_awal)->startOfDay();
                                        $todayDate = \Carbon\Carbon::now()->startOfDay();
                                        $diff = $todayDate->diffInDays($targetDate, false);
                                    @endphp
                                    @if($diff == 0)
                                        Hari ini
                                    @elseif($diff == 1)
                                        Besok
                                    @elseif($diff > 1)
                                        {{ $diff }} hari lagi
                                    @else
                                        Sudah lewat
                                    @endif
                                </small>
                            </div>
                            <div class="mt-2 font-weight-bold">{{ $event->nama_kegiatan }}</div>
                            <p class="small text-muted mb-0">{{ Str::limit($event->deskripsi, 60) }}</p>
                        </li>
                        @empty
                        <li class="nav-item p-4 text-center text-muted">
                            <small>Tidak ada agenda mendatang terdekat.</small>
                        </li>
                        @endforelse
                    </ul>
                </div>
                <div class="card-footer text-center py-2">
                    <a href="{{ route('akademik.kalender-akademik.index') }}" class="small-box-footer text-info font-weight-bold">
                        Lihat Seluruh Kalender <i class="fas fa-arrow-circle-right ml-1"></i>
                    </a>
                </div>
            </div>

            {{-- Quote of the day --}}
            <div class="card bg-gradient-primary shadow-sm border-0">
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-quote-left fa-2x mb-3 opacity-50"></i>
                        <p class="font-italic">"Barang siapa menempuh suatu jalan untuk mencari ilmu, maka Allah akan memudahkan baginya jalan menuju surga."</p>
                        <small class="font-weight-bold text-uppercase">— HR. Muslim</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Weekly Summary --}}
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-secondary shadow-sm">
                <div class="card-header border-0">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-calendar-week mr-1 text-secondary"></i> Ringkasan Mingguan</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        @php $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']; @endphp
                        @foreach($hariList as $hari)
                        <div class="col-md-4 col-lg-2 mb-3">
                            <div class="card h-100 shadow-none border {{ $today == $hari ? 'border-primary' : '' }}">
                                <div class="card-header py-2 text-center {{ $today == $hari ? 'bg-primary' : 'bg-light' }}">
                                    <span class="font-weight-bold small text-uppercase">{{ $hari }}</span>
                                </div>
                                <div class="card-body p-2">
                                    @php $jadwalHari = $jadwalMingguan->where('hari', $hari); @endphp
                                    @forelse($jadwalHari->sortBy('jamke') as $j)
                                    <div class="border-bottom pb-1 mb-2">
                                        <div class="small font-weight-bold text-truncate">{{ $j->mataPelajaran->nama }}</div>
                                        <div class="d-flex justify-content-between small text-muted">
                                            <span>#{{ $j->jamke }}</span>
                                            <span>{{ $j->rombel->nama_rombel }}</span>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="text-center py-2 text-muted opacity-25">
                                        <i class="fas fa-minus small"></i>
                                    </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
