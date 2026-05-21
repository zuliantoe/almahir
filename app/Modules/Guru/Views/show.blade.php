@extends('layouts.app')

@section('title', $title ?? 'Detail Guru')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="row page-titles mb-4">
        <div class="col-md-5 align-self-center">
            <h3 class="text-primary font-weight-bold">
                <i class="fas fa-chalkboard-teacher mr-2"></i> {{ $title ?? 'Detail Guru' }}
            </h3>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <a href="{{ route('guru.index') }}" class="btn btn-outline-secondary shadow-sm px-4" style="border-radius: 50px;">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
            <a href="{{ route('guru.edit', $guru->id) }}" class="btn btn-primary shadow-sm px-4 ml-2" style="border-radius: 50px;">
                <i class="fas fa-edit mr-1"></i> Edit
            </a>
        </div>
    </div>

    <div class="row">
        {{-- Kolom Profil Kiri --}}
        <div class="col-md-4">
            {{-- Kartu Profil --}}
            <div class="card border-0 shadow-sm text-center mb-4" style="border-radius: 20px; overflow: hidden;">
                <div class="card-body p-5">
                    <div class="mb-4">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white font-weight-bold shadow-sm mx-auto"
                             style="width: 110px; height: 110px; font-size: 2.8rem;">
                            {{ strtoupper(substr($guru->nama, 0, 1)) }}
                        </div>
                    </div>
                    <h4 class="font-weight-bold text-dark mb-1">{{ $guru->nama }}</h4>
                    <p class="text-muted mb-1"><i class="fas fa-id-badge mr-1"></i> NIP: {{ $guru->nip ?? '-' }}</p>
                    @if($guru->user && $guru->user->email)
                        <p class="text-muted mb-3"><i class="fas fa-envelope mr-1"></i> {{ $guru->user->email }}</p>
                    @endif
                    @php
                        $statusClass = match($guru->status ?? '') {
                            'aktif'   => 'success',
                            'nonaktif'=> 'warning',
                            'pensiun' => 'secondary',
                            default   => 'info',
                        };
                    @endphp
                    <span class="badge badge-{{ $statusClass }} px-4 py-2 shadow-sm" style="border-radius: 50px; font-size: 0.9rem; text-transform: capitalize;">
                        <i class="fas fa-circle mr-1" style="font-size: 0.6rem;"></i> {{ $guru->status ?? '-' }}
                    </span>
                </div>
            </div>

            {{-- Info Tambahan --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 font-weight-bold text-dark"><i class="fas fa-info-circle text-primary mr-2"></i> Informasi Guru</h6>
                </div>
                <div class="card-body pt-0">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted" style="width: 45%;">Jenis Kelamin</td>
                            <td>
                                @if($guru->jenis_kelamin == 'L') <i class="fas fa-mars text-primary mr-1"></i> Laki-laki
                                @elseif($guru->jenis_kelamin == 'P') <i class="fas fa-venus text-danger mr-1"></i> Perempuan
                                @else - @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tempat Lahir</td>
                            <td>{{ $guru->tempat_lahir ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Lahir</td>
                            <td>{{ $guru->tanggal_lahir ? \Carbon\Carbon::parse($guru->tanggal_lahir)->translatedFormat('d F Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Alamat</td>
                            <td>{{ $guru->alamat ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Ringkasan Jadwal --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px; background: linear-gradient(135deg, #4361ee 0%, #3f37c9 100%); color: white;">
                <div class="card-body p-4 text-center">
                    <i class="fas fa-calendar-alt fa-2x mb-3" style="opacity:.7;"></i>
                    <h5 class="font-weight-bold mb-1">Total Sesi Mengajar</h5>
                    <h2 class="font-weight-bold mb-0" style="font-size: 3rem;">{{ $rawJadwal->count() }}</h2>
                    <small style="opacity:.75;">
                        sesi / minggu — {{ $activeTahunAjaran ? $activeTahunAjaran->tahunajaran : 'TA Belum Aktif' }}
                    </small>
                    <hr style="border-color: rgba(255,255,255,0.3);">
                    <a href="{{ route('akademik.jadwal-pelajaran.index', ['guru_id' => $guru->id]) }}"
                       class="btn btn-light btn-block font-weight-bold text-primary shadow-sm" style="border-radius: 50px;">
                        <i class="fas fa-external-link-alt mr-1"></i> Lihat Semua Jadwal
                    </a>
                </div>
            </div>
        </div>

        {{-- Kolom Jadwal Kanan --}}
        <div class="col-md-8">

            {{-- Filter Tahun Ajaran --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                <div class="card-body py-3">
                    <form action="{{ route('guru.show', $guru->id) }}" method="GET" class="row align-items-center">
                        <div class="col-md-2 text-muted font-weight-bold">
                            <i class="fas fa-filter mr-1 text-primary"></i> Tahun Ajaran
                        </div>
                        <div class="col-md-7">
                            <select name="tahun_ajaran_id" class="form-control" onchange="this.form.submit()">
                                @foreach($tahunAjarans as $ta)
                                    <option value="{{ $ta->id }}" {{ ($activeTahunAjaran && $activeTahunAjaran->id == $ta->id) ? 'selected' : '' }}>
                                        {{ $ta->tahunajaran }} {{ $ta->status ? '(Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-sync-alt mr-1"></i> Tampilkan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if($rawJadwal->isEmpty())
                <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">Belum Ada Jadwal Mengajar</h5>
                        <p class="text-muted mb-0">Guru ini belum memiliki jadwal mengajar untuk tahun ajaran yang dipilih.</p>
                        <a href="{{ route('akademik.jadwal-pelajaran.create') }}" class="btn btn-primary mt-3" style="border-radius: 50px;">
                            <i class="fas fa-plus mr-1"></i> Tambah Jadwal
                        </a>
                    </div>
                </div>
            @else

                {{-- Timetable Mingguan --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 font-weight-bold text-dark">
                            <i class="fas fa-table text-primary mr-2"></i> Jadwal Mengajar Mingguan
                        </h6>
                        <span class="badge badge-primary px-3">{{ $rawJadwal->count() }} Sesi</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0" id="timetable-guru-show">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center" style="width: 90px;">Jam ke-</th>
                                        @foreach($hariList as $hari)
                                            @php $isToday = ($hari === $todayName); @endphp
                                            <th class="text-center {{ $isToday ? 'bg-primary text-white' : '' }}">
                                                {{ $hari }}
                                                @if($isToday)<br><small>(Hari Ini)</small>@endif
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($usedJamKes as $jamke)
                                        @php $sampleJam = $rawJadwal->where('jamke', $jamke)->first(); @endphp
                                        <tr>
                                            <td class="text-center align-middle bg-light">
                                                <strong>{{ $jamke }}</strong>
                                                @if($sampleJam)
                                                    <br><small class="text-muted">{{ substr($sampleJam->jamawal, 0, 5) }}-{{ substr($sampleJam->jamakhir, 0, 5) }}</small>
                                                @endif
                                            </td>
                                            @foreach($hariList as $hari)
                                                @php $j = $timetable[$hari][$jamke] ?? null; @endphp
                                                <td class="align-middle p-1" style="min-width: 120px;">
                                                    @if($j)
                                                        <div class="p-2 border-left border-primary bg-white rounded shadow-sm">
                                                            <div class="font-weight-bold text-primary" style="font-size: .8rem;">
                                                                {{ optional($j->mataPelajaran)->nama ?? '-' }}
                                                            </div>
                                                            <div class="text-muted" style="font-size: .72rem;">
                                                                <i class="fas fa-users mr-1"></i>{{ optional($j->rombel)->nama_rombel ?? '-' }}
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="text-center text-muted"><i class="fas fa-minus small"></i></div>
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
                <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-list-ul text-primary mr-2"></i> Ringkasan Per Hari</h6>
                <div class="row">
                    @foreach($hariList as $hari)
                        @php
                            $jadwalHari = $rawJadwal->where('hari', $hari);
                            $isToday    = ($hari === $todayName);
                        @endphp
                        @if($jadwalHari->isNotEmpty())
                            <div class="col-md-6 mb-3">
                                <div class="card border-0 shadow-sm h-100 {{ $isToday ? 'border-left border-primary' : '' }}" style="border-radius: 14px;">
                                    <div class="card-header bg-white border-0 py-2 d-flex justify-content-between align-items-center">
                                        <span class="font-weight-bold {{ $isToday ? 'text-primary' : 'text-dark' }}">
                                            {{ $hari }}
                                        </span>
                                        @if($isToday)
                                            <span class="badge badge-primary">Hari Ini</span>
                                        @else
                                            <span class="badge badge-light">{{ $jadwalHari->count() }} sesi</span>
                                        @endif
                                    </div>
                                    <div class="card-body p-0">
                                        <ul class="list-group list-group-flush">
                                            @foreach($jadwalHari->sortBy('jamke') as $j)
                                                <li class="list-group-item px-3 py-2 d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <span class="badge badge-primary badge-pill mr-2">{{ $j->jamke }}</span>
                                                        <span class="small font-weight-bold">{{ optional($j->mataPelajaran)->nama ?? '-' }}</span>
                                                    </div>
                                                    <span class="badge badge-light border">{{ optional($j->rombel)->nama_rombel ?? '-' }}</span>
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
    </div>
</div>

<style>
    #timetable-guru-show th, #timetable-guru-show td { font-size: 0.82rem; }
    @media (max-width: 768px) {
        #timetable-guru-show th, #timetable-guru-show td { font-size: 0.72rem; padding: 0.3rem; }
    }
</style>
@endsection
