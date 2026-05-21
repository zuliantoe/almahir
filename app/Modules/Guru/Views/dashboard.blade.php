@extends('layouts.app')

@section('title', 'Dashboard Guru')

@section('content')
<div class="container-fluid">

    {{-- Hero Banner --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 20px; background: linear-gradient(135deg, #4361ee 0%, #3f37c9 100%); color: white;">
                <div class="card-body p-5">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="display-5 font-weight-bold mb-2">
                                مرحباً، {{ $guru->nama }}!
                            </h1>
                            <p class="lead mb-4" style="opacity:.75;">Semangat mendidik generasi penerus bangsa hari ini.</p>
                            <div class="d-flex flex-wrap" style="gap: 10px;">
                                <a href="{{ route('akademik.jadwal-pelajaran.index') }}" class="btn btn-white btn-lg px-4 shadow-sm font-weight-bold" style="border-radius: 50px; background: white; color: #4361ee;">
                                    <i class="fas fa-calendar-alt mr-2"></i> Jadwal Mengajar
                                </a>
                                <a href="{{ route('penilaiandanpresensi.index') }}" class="btn btn-outline-light btn-lg px-4" style="border-radius: 50px;">
                                    <i class="fas fa-clipboard-check mr-2"></i> Penilaian & Presensi
                                </a>
                            </div>
                        </div>
                        <div class="col-md-4 text-center d-none d-md-block">
                            <i class="fas fa-chalkboard-teacher fa-10x" style="opacity: 0.2;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-body p-4 text-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 70px; height: 70px; background: rgba(67,97,238,0.1);">
                        <i class="fas fa-calendar-week text-primary fa-2x"></i>
                    </div>
                    <h5 class="font-weight-bold mb-1">Total Sesi/Minggu</h5>
                    <h2 class="text-primary font-weight-bold mb-0">{{ $rawJadwal->count() }}</h2>
                    <small class="text-muted">{{ $activeTahunAjaran?->tahunajaran ?? 'TA Belum Aktif' }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-body p-4 text-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 70px; height: 70px; background: rgba(40,167,69,0.1);">
                        <i class="fas fa-calendar-day text-success fa-2x"></i>
                    </div>
                    <h5 class="font-weight-bold mb-1">Mengajar Hari Ini</h5>
                    <h2 class="text-success font-weight-bold mb-0">{{ $jadwalHariIni->count() }}</h2>
                    <small class="text-muted">{{ $todayName }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-body p-4 text-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 70px; height: 70px; background: rgba(23,162,184,0.1);">
                        <i class="fas fa-users text-info fa-2x"></i>
                    </div>
                    <h5 class="font-weight-bold mb-1">Rombel Diajar</h5>
                    <h2 class="text-info font-weight-bold mb-0">{{ $rawJadwal->pluck('rombel_id')->unique()->count() }}</h2>
                    <small class="text-muted">Rombel</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Jadwal Hari Ini --}}
        <div class="col-md-5 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 font-weight-bold text-dark">
                        <i class="fas fa-clock text-success mr-2"></i> Jadwal Hari Ini
                    </h6>
                    <span class="badge badge-success px-3">{{ $todayName }}</span>
                </div>
                <div class="card-body p-0">
                    @if($jadwalHariIni->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-coffee fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Tidak ada jadwal mengajar hari ini.</p>
                            <small class="text-muted">Selamat beristirahat!</small>
                        </div>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($jadwalHariIni as $j)
                                <li class="list-group-item px-4 py-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <span class="badge badge-primary badge-pill mr-2">Jam {{ $j->jamke }}</span>
                                            <span class="font-weight-bold text-dark">{{ optional($j->mataPelajaran)->nama ?? '-' }}</span>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-users mr-1"></i>{{ optional($j->rombel)->nama_rombel ?? '-' }}
                                                &nbsp;|&nbsp;
                                                <i class="far fa-clock mr-1"></i>{{ substr($j->jamawal, 0, 5) }} – {{ substr($j->jamakhir, 0, 5) }}
                                            </small>
                                        </div>
                                        <span class="badge badge-light border">{{ optional($j->rombel->kelas ?? null)->nama_kelas ?? '' }}</span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        {{-- Timetable Mingguan --}}
        <div class="col-md-7 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 font-weight-bold text-dark">
                        <i class="fas fa-table text-primary mr-2"></i> Jadwal Mengajar Mingguan
                    </h6>
                    <a href="{{ route('akademik.jadwal-pelajaran.index') }}" class="btn btn-sm btn-outline-primary" style="border-radius: 50px;">
                        <i class="fas fa-expand-alt mr-1"></i> Lihat Penuh
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($rawJadwal->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Belum ada jadwal mengajar.</p>
                            <small class="text-muted">Hubungi admin untuk pengaturan jadwal.</small>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-0" id="timetable-dashboard-guru">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center" style="width: 60px;">Jam</th>
                                        @foreach($hariList as $hari)
                                            @php $isToday = ($hari === $todayName); @endphp
                                            <th class="text-center {{ $isToday ? 'bg-primary text-white' : '' }}" style="min-width: 100px;">
                                                {{ $hari }}
                                                @if($isToday)<br><small style="font-size:.65rem;">(Hari Ini)</small>@endif
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($usedJamKes as $jamke)
                                        @php $sampleJam = $rawJadwal->where('jamke', $jamke)->first(); @endphp
                                        <tr>
                                            <td class="text-center align-middle bg-light py-2">
                                                <strong>{{ $jamke }}</strong>
                                                @if($sampleJam)
                                                    <br><small class="text-muted" style="font-size:.65rem;">{{ substr($sampleJam->jamawal,0,5) }}</small>
                                                @endif
                                            </td>
                                            @foreach($hariList as $hari)
                                                @php $j = $timetable[$hari][$jamke] ?? null; @endphp
                                                <td class="align-middle p-1">
                                                    @if($j)
                                                        <div class="p-1 border-left border-primary bg-white rounded" style="font-size:.75rem;">
                                                            <div class="font-weight-bold text-primary text-truncate">{{ optional($j->mataPelajaran)->nama ?? '-' }}</div>
                                                            <div class="text-muted text-truncate" style="font-size:.68rem;"><i class="fas fa-users"></i> {{ optional($j->rombel)->nama_rombel ?? '-' }}</div>
                                                        </div>
                                                    @else
                                                        <div class="text-center text-muted"><i class="fas fa-minus" style="font-size:.7rem;"></i></div>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

<style>
    #timetable-dashboard-guru th, #timetable-dashboard-guru td { font-size: 0.78rem; }
</style>
@endsection
