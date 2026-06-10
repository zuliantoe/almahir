@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    {{-- Back Button & Header --}}
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('walimurid.portal.siswa-detail', $siswa->id) }}" class="btn btn-light rounded-circle shadow-sm mr-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h3 class="font-weight-bold mb-0">Jadwal Pelajaran: {{ $siswa->nama }}</h3>
            <p class="text-muted mb-0">Jadwal pelajaran mingguan aktif kelas: <strong>{{ $rombelSiswa->rombel->nama_rombel ?? 'Belum Ditentukan' }}</strong></p>
        </div>
    </div>

    @if(!$rombelSiswa)
        <div class="card border-0 shadow-sm" style="border-radius: 20px;">
            <div class="card-body text-center py-5">
                <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                <h5>Siswa Belum Terdaftar di Kelas/Rombel</h5>
                <p class="text-muted mb-0">Putra/putri Anda belum dimasukkan ke kelas manapun pada tahun ajaran ini.</p>
            </div>
        </div>
    @elseif($rawJadwal->isEmpty())
        <div class="card border-0 shadow-sm" style="border-radius: 20px;">
            <div class="card-body text-center py-5">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <h5>Jadwal Belum Diatur</h5>
                <p class="text-muted mb-0">Jadwal pelajaran untuk kelas {{ $rombelSiswa->rombel->nama_rombel }} belum diatur oleh admin.</p>
            </div>
        </div>
    @else
        {{-- Timetable Mingguan --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
            <div class="card-header bg-white p-4 border-light d-flex justify-content-between align-items-center">
                <h5 class="font-weight-bold mb-0 text-primary"><i class="fas fa-table mr-2"></i>Tabel Jadwal Mingguan</h5>
                <span class="badge badge-primary px-3 py-2 rounded-pill">{{ $rawJadwal->count() }} Sesi Pelajaran</span>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="timetable-siswa">
                        <thead>
                            <tr class="bg-light">
                                <th class="text-center" style="width:100px;">Jam ke-</th>
                                @foreach($hariList as $hari)
                                    @php
                                        $todayLabel = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'][\Carbon\Carbon::now()->dayOfWeekIso - 1] ?? '';
                                        $isToday = ($hari === $todayLabel);
                                    @endphp
                                    <th class="text-center {{ $isToday ? 'bg-primary text-white' : '' }}">
                                        {{ $hari }}
                                        @if($isToday)
                                            <br><small>(Hari Ini)</small>
                                        @endif
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($usedJamKes as $jamke)
                                @php
                                    $sampleJam = $rawJadwal->where('jamke', $jamke)->first();
                                @endphp
                                <tr>
                                    <td class="text-center align-middle bg-light">
                                        <strong>{{ $jamke }}</strong>
                                        @if($sampleJam)
                                            <br><small class="text-muted">{{ substr($sampleJam->jamawal, 0, 5) }} - {{ substr($sampleJam->jamakhir, 0, 5) }}</small>
                                        @endif
                                    </td>
                                    @foreach($hariList as $hari)
                                        @php
                                            $j = $timetable[$hari][$jamke] ?? null;
                                        @endphp
                                        <td class="align-middle p-2" style="min-width:130px;">
                                            @if($j)
                                                <div class="p-2 border-left border-info bg-white shadow-sm rounded">
                                                    <div class="font-weight-bold text-info small">{{ $j->mataPelajaran?->nama ?? 'Mapel' }}</div>
                                                    <div class="text-muted" style="font-size: .75rem;">
                                                        <i class="fas fa-user-tie mr-1"></i>{{ optional($j->guru)->nama ?? '-' }}
                                                    </div>
                                                    <div class="text-muted" style="font-size: .7rem;">
                                                        <i class="far fa-clock mr-1"></i>{{ substr($j->jamawal,0,5) }}-{{ substr($j->jamakhir,0,5) }}
                                                    </div>
                                                </div>
                                            @else
                                                <div class="text-center text-muted">
                                                    <i class="fas fa-minus small"></i>
                                                </div>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Ringkasan Per Hari --}}
        <h5 class="mb-3 text-dark font-weight-bold"><i class="fas fa-list-ul mr-2 text-primary"></i>Ringkasan Harian</h5>
        <div class="row">
            @foreach($hariList as $hari)
                @php
                    $todayLabel = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'][\Carbon\Carbon::now()->dayOfWeekIso - 1] ?? '';
                    $jadwalHari = $rawJadwal->where('hari', $hari);
                    $isToday = ($hari === $todayLabel);
                @endphp
                @if($jadwalHari->isNotEmpty())
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 18px; border-top: 4px solid {{ $isToday ? '#4361ee' : '#e2e8f0' }} !important;">
                        <div class="card-header bg-white border-0 pt-3 px-3 pb-0 d-flex justify-content-between align-items-center">
                            <h6 class="font-weight-bold mb-0 text-dark">{{ $hari }}</h6>
                            @if($isToday)
                                <span class="badge badge-primary rounded-pill px-2 py-1" style="font-size: 0.65rem;">Hari Ini</span>
                            @endif
                        </div>
                        <div class="card-body p-3">
                            <ul class="list-group list-group-flush">
                                @foreach($jadwalHari->sortBy('jamke') as $j)
                                    <li class="list-group-item p-2 px-0 d-flex justify-content-between align-items-center border-light">
                                        <div>
                                            <span class="badge badge-light mr-2 border">{{ $j->jamke }}</span>
                                            <span class="small font-weight-bold text-dark">{{ optional($j->mataPelajaran)->nama }}</span>
                                        </div>
                                        <span class="small text-muted">{{ optional($j->guru)->nama ?? '-' }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    @endif
</div>
@endsection
