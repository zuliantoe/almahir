@extends('layouts.app')

@section('title', 'Dashboard Santri - Akademik')

@section('content')
<div class="container-fluid">
    {{-- Content Header --}}
    <div class="row mb-3">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Dashboard Santri</h1>
        </div>
        <div class="col-sm-6 text-right">
            <span class="badge badge-info px-3 py-2">
                <i class="fas fa-clock mr-1"></i> {{ $today }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
            </span>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-book-open"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Mata Pelajaran</span>
                    <span class="info-box-number">{{ $jadwalHariIni->count() }} <small>Hari Ini</small></span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Kelas</span>
                    <span class="info-box-number text-truncate" title="{{ $rombelSiswa->rombel->nama_rombel ?? '-' }}">
                        {{ $rombelSiswa->rombel->nama_rombel ?? '-' }}
                    </span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-calendar-alt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Semester</span>
                    <span class="info-box-number">{{ $tahunAjaranAktif->semester ?? '-' }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-graduation-cap"></i></span>
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
            {{-- Welcome --}}
            <div class="card card-outline card-info shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <img src="{{ Auth::user()->avatar_url ?: 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&color=fff&background=17a2b8' }}" 
                             class="img-circle border elevation-1 mr-3" 
                             style="width: 50px; height: 50px;">
                        <div>
                            <h5 class="mb-0 font-weight-bold text-info">Ahlan wa Sahlan, {{ Auth::user()->name }}!</h5>
                            <p class="text-muted small mb-0">Semangat belajarnya hari ini ya, semoga ilmunya berkah dan bermanfaat.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ongoing Events --}}
            @if($eventHariIni->isNotEmpty())
                @foreach($eventHariIni as $event)
                <div class="callout callout-{{ $event->jenisKegiatan->is_kbm ? 'info' : 'warning' }} shadow-sm">
                    <h5><i class="fas {{ $event->jenisKegiatan->is_kbm ? 'fa-info-circle' : 'fa-calendar-day' }} mr-2"></i> {{ $event->nama_kegiatan }}</h5>
                    <p>{{ $event->deskripsi ?: 'Kegiatan madrasah sedang berlangsung hari ini.' }}</p>
                    @if(!$event->jenisKegiatan->is_kbm)
                        <span class="badge badge-danger">KBM LIBUR</span>
                    @endif
                </div>
                @endforeach
            @endif

            {{-- Schedule Table --}}
            <div class="card shadow-sm">
                <div class="card-header border-0">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-book-reader mr-1"></i> Jadwal Belajar Hari Ini</h3>
                </div>
                <div class="card-body p-0">
                    @php $isLibur = $eventHariIni->where('jenisKegiatan.is_kbm', false)->isNotEmpty(); @endphp
                    @if(!$rombelSiswa)
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-exclamation-circle fa-3x mb-3 opacity-25"></i>
                            <p>Data rombongan belajar (kelas) belum ditemukan.</p>
                        </div>
                    @elseif($jadwalHariIni->isEmpty())
                        <div class="text-center py-5 text-success">
                            <i class="fas fa-smile-beam fa-3x mb-3 opacity-50"></i>
                            <p class="mb-0">Alhamdulillah, tidak ada jadwal pelajaran hari ini.</p>
                        </div>
                    @elseif($isLibur)
                        <div class="text-center py-5 text-warning">
                            <i class="fas fa-umbrella-beach fa-3x mb-3"></i>
                            <h5>KBM Diliburkan</h5>
                            <p class="text-muted">Ikuti kegiatan sekolah yang berlangsung hari ini.</p>
                        </div>
                    @else
                        <table class="table table-striped table-valign-middle">
                            <thead>
                                <tr>
                                    <th class="text-center" width="80">SESI</th>
                                    <th>MATA PELAJARAN</th>
                                    <th>PENGAJAR</th>
                                    <th class="text-center">WAKTU</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jadwalHariIni as $j)
                                <tr>
                                    <td class="text-center font-weight-bold text-info">{{ $j->jamke }}</td>
                                    <td>
                                        <div class="font-weight-bold">{{ $j->mataPelajaran->nama ?? '-' }}</div>
                                    </td>
                                    <td>
                                        <small class="font-weight-bold text-muted text-uppercase">Ust. {{ explode(' ', $j->guru->nama)[0] }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-light border">
                                            {{ substr($j->jamawal, 0, 5) }} - {{ substr($j->jamakhir, 0, 5) }}
                                        </span>
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
                <div class="card-header bg-primary">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-calendar-alt mr-2"></i> Agenda Madrasah</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="nav nav-pills flex-column">
                        @forelse($upcomingEvents as $event)
                        <li class="nav-item p-3 border-bottom">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <span class="badge badge-primary px-2 py-1">
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
                            <div class="mt-2 font-weight-bold small">{{ $event->nama_kegiatan }}</div>
                        </li>
                        @empty
                        <li class="nav-item p-4 text-center text-muted">
                            <small>Belum ada agenda madrasah terdekat.</small>
                        </li>
                        @endforelse
                    </ul>
                </div>
                <div class="card-footer text-center py-2">
                    <a href="{{ route('akademik.kalender-akademik.index') }}" class="small text-primary font-weight-bold">
                        Lihat Selengkapnya <i class="fas fa-chevron-right ml-1"></i>
                    </a>
                </div>
            </div>

            {{-- Hikmah --}}
            <div class="card bg-gradient-warning shadow-sm border-0">
                <div class="card-body text-dark">
                    <div class="text-center">
                        <i class="fas fa-lightbulb fa-2x mb-3 opacity-50"></i>
                        <p class="font-italic font-weight-bold">"Menuntut ilmu adalah kewajiban bagi setiap muslim."</p>
                        <small class="font-weight-bold text-uppercase">— HR. Ibnu Majah</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Weekly Recap --}}
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-secondary shadow-sm">
                <div class="card-header border-0">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-calendar-week mr-1 text-secondary"></i> Rangkuman Mingguan</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        @php $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']; @endphp
                        @foreach($hariList as $hari)
                        <div class="col-md-4 col-lg-2 mb-3">
                            <div class="card h-100 shadow-none border {{ $today == $hari ? 'border-info' : '' }}">
                                <div class="card-header py-2 text-center {{ $today == $hari ? 'bg-info' : 'bg-light' }}">
                                    <span class="font-weight-bold small text-uppercase">{{ $hari }}</span>
                                </div>
                                <div class="card-body p-2">
                                    @php $jadwalHari = $jadwalMingguan->where('hari', $hari); @endphp
                                    @forelse($jadwalHari->sortBy('jamke') as $j)
                                    <div class="border-bottom pb-1 mb-2">
                                        <div class="small font-weight-bold text-truncate">{{ $j->mataPelajaran->nama }}</div>
                                        <div class="d-flex justify-content-between small text-muted">
                                            <span>#{{ $j->jamke }}</span>
                                            <span>Ust. {{ explode(' ', $j->guru->nama)[0] }}</span>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="text-center py-2 text-muted opacity-25">
                                        <i class="fas fa-circle-notch small"></i>
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
