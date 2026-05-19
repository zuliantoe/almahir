@extends('layouts.app')

@section('title', 'Jadwal Mengajar Saya')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-gray-800">Jadwal Mengajar Mingguan</h1>
            <div class="btn-group">
                <x-btn :href="route('akademik.jadwal-pelajaran.index', ['tampil' => 'all'])" class="btn-outline-primary" icon="fas fa-list">
                    Lihat Semua Jadwal
                </x-btn>
            </div>
        </div>
    </div>

    {{-- Filter Tahun Ajaran --}}
    <x-card class="mb-4 shadow-sm border-left-primary">
        <form action="{{ route('akademik.jadwal-pelajaran.index') }}" method="GET" class="row align-items-center">
            <div class="col-md-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-calendar-check fa-2x text-primary mr-3"></i>
                    <div>
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tahun Ajaran Aktif</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $activeTahunAjaran ? $activeTahunAjaran->tahunajaran : 'Belum Ditentukan' }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <select name="tahun_ajaran_id" class="form-control" onchange="this.form.submit()">
                    @foreach($tahunAjarans as $ta)
                        <option value="{{ $ta->id }}" {{ ($activeTahunAjaran && $activeTahunAjaran->id == $ta->id) ? 'selected' : '' }}>
                            Tahun Ajaran: {{ $ta->tahunajaran }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <x-btn type="submit" class="btn-primary w-100">Ganti Tahun</x-btn>
            </div>
        </form>
    </x-card>

    @if($rawJadwal->isEmpty())
        <x-card type="info" outline>
            <div class="text-center py-5">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <h5>Belum Ada Jadwal Mengajar</h5>
                <p class="text-muted">Hubungi admin untuk pengaturan jadwal Anda.</p>
            </div>
        </x-card>
    @else

        {{-- Timetable Mingguan --}}
        <x-card title="Tabel Jadwal Mingguan" icon="fas fa-table" type="primary" outline>
            <x-slot name="tools">
                <span class="badge badge-primary px-3">{{ $rawJadwal->count() }} Sesi</span>
            </x-slot>

            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="timetable-guru">
                    <thead>
                        <tr class="bg-light">
                            <th class="text-center" style="width:100px;">Jam</th>
                            @foreach($hariList as $hari)
                                @php
                                    $hariNames = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
                                    $hariLabel = $hariNames[$hari] ?? $hari;
                                    $isToday = \Carbon\Carbon::now()->dayOfWeekIso == $hari;
                                @endphp
                                <th class="text-center {{ $isToday ? 'bg-primary text-white' : '' }}">
                                    {{ $hariLabel }}
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
                                            <div class="p-2 border-left border-primary bg-white shadow-sm rounded">
                                                <div class="font-weight-bold text-primary small">{{ $j->mataPelajaran->nama ?? 'Mapel' }}</div>
                                                <div class="text-muted" style="font-size: .75rem;">
                                                    <i class="fas fa-users mr-1"></i>{{ optional($j->rombel)->nama_rombel ?? '-' }}
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
        </x-card>

        {{-- Ringkasan per Hari --}}
        <h5 class="mt-4 mb-3 text-gray-800"><i class="fas fa-list-ul mr-2"></i>Ringkasan Per Hari</h5>
        <div class="row">
            @foreach($hariList as $hari)
                @php
                    $hariNames = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
                    $hariLabel = $hariNames[$hari] ?? $hari;
                    $jadwalHari = $rawJadwal->where('hari', $hari);
                    $isToday = \Carbon\Carbon::now()->dayOfWeekIso == $hari;
                @endphp
                @if($jadwalHari->isNotEmpty())
                <div class="col-md-4 mb-3">
                    <x-card :title="$hariLabel" :type="$isToday ? 'primary' : 'secondary'" outline>
                        @if($isToday)
                            <x-slot name="tools">
                                <span class="badge badge-primary">Hari Ini</span>
                            </x-slot>
                        @endif
                        
                        <ul class="list-group list-group-flush">
                            @foreach($jadwalHari->sortBy('jamke') as $j)
                                <li class="list-group-item p-2 d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge badge-light mr-2">{{ $j->jamke }}</span>
                                        <span class="small font-weight-bold">{{ optional($j->mataPelajaran)->nama }}</span>
                                    </div>
                                    <span class="badge badge-pill badge-light">{{ optional($j->rombel)->nama_rombel }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </x-card>
                </div>
                @endif
            @endforeach
        </div>

    @endif
</div>
@endsection
