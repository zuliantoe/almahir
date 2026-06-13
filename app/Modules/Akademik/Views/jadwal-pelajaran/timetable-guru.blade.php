@extends('layouts.app')

@section('title', 'Jadwal Mengajar Saya')

@section('content')
@php
    $getColor = function($mapelName) {
        $colors = [
            ['bg' => 'rgba(67, 97, 238, 0.05)',  'border' => '#4361ee', 'text' => '#2b47d6'],
            ['bg' => 'rgba(16, 185, 129, 0.05)', 'border' => '#10b981', 'text' => '#0c8f63'],
            ['bg' => 'rgba(139, 92, 246, 0.05)', 'border' => '#8b5cf6', 'text' => '#6d28d9'],
            ['bg' => 'rgba(245, 158, 11, 0.05)',  'border' => '#f59e0b', 'text' => '#b45309'],
            ['bg' => 'rgba(239, 68, 68, 0.05)',  'border' => '#ef4444', 'text' => '#b91c1c'],
            ['bg' => 'rgba(20, 184, 166, 0.05)', 'border' => '#14b8a6', 'text' => '#0f766e'],
            ['bg' => 'rgba(244, 63, 94, 0.05)',  'border' => '#f43f5e', 'text' => '#be123c'],
            ['bg' => 'rgba(6, 182, 212, 0.05)',  'border' => '#06b6d4', 'text' => '#0369a1'],
        ];
        $hash = crc32($mapelName);
        $index = abs($hash) % count($colors);
        return $colors[$index];
    };
@endphp

<div class="container-fluid">

    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-12 d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <div class="mb-3 mb-md-0">
                <h1 class="h3 mb-1 text-gray-800 font-weight-bold">Jadwal Mengajar Mingguan</h1>
                <p class="text-muted mb-0"><i class="fas fa-user-tie mr-1 text-primary"></i> Guru: <strong>{{ optional($guru)->nama }}</strong></p>
            </div>
            <div class="d-flex flex-wrap" style="gap: 8px;">
                <x-btn :href="route('akademik.jadwal-pelajaran.index', ['tampil' => 'all'])" class="btn-outline-primary rounded-pill px-4 shadow-sm" icon="fas fa-list">
                    Lihat Semua Jadwal
                </x-btn>
            </div>
        </div>
    </div>

    {{-- Filter Tahun Ajaran --}}
    <div class="card mb-4 shadow-sm border-0 rounded-xl" style="background: linear-gradient(to right, #ffffff, #f8fafc);">
        <div class="card-body p-3">
            <form action="{{ route('akademik.jadwal-pelajaran.index') }}" method="GET" class="row align-items-center">
                <div class="col-lg-4 col-md-5 mb-3 mb-md-0">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary-soft p-3 rounded-circle mr-3">
                            <i class="fas fa-calendar-check fa-lg text-primary"></i>
                        </div>
                        <div>
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-0">Tahun Ajaran Aktif</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $activeTahunAjaran ? $activeTahunAjaran->tahunajaran : 'Belum Ditentukan' }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 col-md-4 mb-3 mb-md-0">
                    <select name="tahun_ajaran_id" class="form-control select-premium" onchange="this.form.submit()">
                        @foreach($tahunAjarans as $ta)
                            <option value="{{ $ta->id }}" {{ ($activeTahunAjaran && $activeTahunAjaran->id == $ta->id) ? 'selected' : '' }}>
                                Tahun Ajaran: {{ $ta->tahunajaran }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 col-md-3">
                    <x-btn type="submit" class="btn-primary w-100 rounded-pill px-4 shadow-sm">
                        <i class="fas fa-sync mr-1"></i> Ganti Tahun
                    </x-btn>
                </div>
            </form>
        </div>
    </div>

    @if($rawJadwal->isEmpty())
        <x-card type="info" outline class="border-0 shadow-sm rounded-xl">
            <div class="text-center py-5">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <h5 class="font-weight-bold">Belum Ada Jadwal Mengajar</h5>
                <p class="text-muted">Hubungi admin untuk pengaturan jadwal Anda.</p>
            </div>
        </x-card>
    @else

        {{-- Timetable Mingguan --}}
        <div class="card shadow-lg border-0 rounded-xl mb-4 overflow-hidden">
            <div class="card-header bg-primary py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-table mr-2"></i>Tabel Jadwal Mingguan</h6>
                <span class="badge badge-light px-3 py-2 text-primary font-weight-bold rounded-pill shadow-sm">{{ $rawJadwal->count() }} Sesi Mengajar</span>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table-timetable" id="timetable-guru">
                        <thead>
                            <tr>
                                <th style="width:110px;">Jam ke</th>
                                @foreach($hariList as $hari)
                                    @php
                                        $hariNames = ['Senin'=>'Senin','Selasa'=>'Selasa','Rabu'=>'Rabu','Kamis'=>'Kamis','Jumat'=>'Jumat','Sabtu'=>'Sabtu','Minggu'=>'Minggu'];
                                        $hariLabel = is_string($hari) ? $hari : ($hariNames[$hari] ?? $hari);
                                        $todayLabel = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'][\Carbon\Carbon::now()->dayOfWeekIso - 1] ?? '';
                                        $isToday = ($hariLabel === $todayLabel);
                                    @endphp
                                    <th class="{{ $isToday ? 'today-header' : '' }}">
                                        {{ $hariLabel }}
                                        @if($isToday)
                                            <span class="d-block small mt-1 font-weight-normal text-white-50">(Hari Ini)</span>
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
                                    <td class="align-middle">
                                        <div class="time-col">
                                            <span class="time-number">{{ $jamke }}</span>
                                            @if($sampleJam)
                                                <span class="time-range">{{ substr($sampleJam->jamawal, 0, 5) }} - {{ substr($sampleJam->jamakhir, 0, 5) }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    @foreach($hariList as $hari)
                                        @php
                                            $j = $timetable[$hari][$jamke] ?? null;
                                            $hariNames = ['Senin'=>'Senin','Selasa'=>'Selasa','Rabu'=>'Rabu','Kamis'=>'Kamis','Jumat'=>'Jumat','Sabtu'=>'Sabtu','Minggu'=>'Minggu'];
                                            $hariLabel = is_string($hari) ? $hari : ($hariNames[$hari] ?? $hari);
                                            $todayLabel = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'][\Carbon\Carbon::now()->dayOfWeekIso - 1] ?? '';
                                            $isToday = ($hariLabel === $todayLabel);
                                        @endphp
                                        <td class="schedule-cell {{ $isToday ? 'today-column' : '' }}">
                                            @if($j)
                                                @php
                                                    $color = $getColor($j->mataPelajaran?->nama ?? 'Mapel');
                                                @endphp
                                                <div class="schedule-card" style="--accent-color: {{ $color['border'] }}; --text-color: {{ $color['text'] }}; background-color: {{ $color['bg'] }}; border: 1px solid {{ $color['border'] }}2b;">
                                                    <div class="schedule-subject">{{ $j->mataPelajaran?->nama ?? 'Mapel' }}</div>
                                                    <div>
                                                        <div class="schedule-info">
                                                            <i class="fas fa-users"></i>
                                                            <span>{{ optional($j->rombel)->nama_rombel ?? '-' }}</span>
                                                        </div>
                                                        <div class="schedule-info">
                                                            <i class="far fa-clock"></i>
                                                            <span>{{ substr($j->jamawal,0,5) }}-{{ substr($j->jamakhir,0,5) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="empty-card">
                                                    <span>—</span>
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

        {{-- Ringkasan per Hari --}}
        <div class="d-flex align-items-center mt-5 mb-4">
            <div class="bg-primary-soft p-2 rounded-circle mr-3">
                <i class="fas fa-calendar-day text-primary fa-lg"></i>
            </div>
            <h4 class="h4 mb-0 text-gray-800 font-weight-bold">Ringkasan Sesi Mengajar Harian</h4>
        </div>
        
        <div class="row">
            @foreach($hariList as $hari)
                @php
                    $hariNames = [1=>'Senin','Senin'=>'Senin',2=>'Selasa','Selasa'=>'Selasa',3=>'Rabu','Rabu'=>'Rabu',4=>'Kamis','Kamis'=>'Kamis',5=>'Jumat','Jumat'=>'Jumat',6=>'Sabtu','Sabtu'=>'Sabtu',7=>'Minggu','Minggu'=>'Minggu'];
                    $hariLabel = $hariNames[$hari] ?? $hari;
                    $todayLabel = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'][\Carbon\Carbon::now()->dayOfWeekIso - 1] ?? '';
                    $jadwalHari = $rawJadwal->where('hari', $hari);
                    $isToday = ($hariLabel === $todayLabel);
                @endphp
                @if($jadwalHari->isNotEmpty())
                <div class="col-lg-4 col-md-6 mb-4 guru-day-card">
                    <div class="card timeline-card h-100 {{ $isToday ? 'border-primary shadow' : 'shadow-sm border-0' }}">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center {{ $isToday ? 'bg-primary text-white' : 'bg-light text-dark border-0' }}">
                            <h6 class="m-0 font-weight-bold">
                                <i class="fas fa-calendar-day mr-2"></i>{{ $hariLabel }}
                            </h6>
                            @if($isToday)
                                <span class="badge badge-light px-3 py-1 text-primary rounded-pill shadow-sm">Hari Ini</span>
                            @endif
                        </div>
                        <div class="card-body p-4">
                            <ul class="timeline-list">
                                @foreach($jadwalHari->sortBy('jamke') as $j)
                                    @php
                                        $color = $getColor($j->mataPelajaran?->nama ?? 'Mapel');
                                    @endphp
                                    <li class="timeline-item {{ $isToday ? 'active' : '' }}">
                                        <div class="timeline-badge" style="background: {{ $color['border'] }}; box-shadow: 0 0 0 2px {{ $color['border'] }}33;"></div>
                                        <div class="timeline-time">Jam Ke-{{ $j->jamke }} ({{ substr($j->jamawal, 0, 5) }} - {{ substr($j->jamakhir, 0, 5) }})</div>
                                        <div class="timeline-content" style="border-left-color: {{ $color['border'] }}; background: {{ $color['bg'] }}">
                                            <div class="timeline-title" style="color: {{ $color['text'] }}">{{ optional($j->mataPelajaran)->nama }}</div>
                                            <div class="timeline-subtitle">
                                                <i class="fas fa-users mr-1 opacity-7"></i> Rombel: {{ optional($j->rombel)->nama_rombel }}
                                            </div>
                                        </div>
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

<style>
    .rounded-xl { border-radius: 1rem !important; }
    
    /* Premium Styling for Timetable */
    .table-timetable {
        border-collapse: separate;
        border-spacing: 8px;
        width: 100%;
        background-color: transparent;
    }
    .table-timetable th {
        font-weight: 700;
        font-size: 0.82rem;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #64748b;
        background: #f8fafc;
        border: none;
        padding: 14px 10px;
        border-radius: 10px;
        text-align: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .table-timetable th.today-header {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: #ffffff !important;
        box-shadow: 0 4px 10px rgba(78, 115, 223, 0.3);
    }
    
    .time-col {
        background: #f1f5f9;
        border-radius: 10px;
        padding: 10px 8px;
        text-align: center;
        width: 110px;
        border: 1px solid #e2e8f0;
    }
    .time-number {
        font-size: 1rem;
        font-weight: 800;
        color: #1e293b;
        display: block;
        line-height: 1.2;
    }
    .time-range {
        font-size: 0.7rem;
        font-weight: 600;
        color: #64748b;
        display: block;
        margin-top: 4px;
    }
    
    /* Cell content style */
    .schedule-cell {
        min-width: 150px;
        vertical-align: middle;
        padding: 0 !important;
        border: none !important;
    }
    
    .schedule-card {
        padding: 12px 14px;
        border-radius: 12px;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.02);
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        min-height: 85px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
    }
    .schedule-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background-color: var(--accent-color);
        border-radius: 12px 0 0 12px;
    }
    .schedule-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 18px rgba(0, 0, 0, 0.06);
    }
    .schedule-subject {
        font-size: 0.82rem;
        font-weight: 700;
        line-height: 1.3;
        margin-bottom: 6px;
        color: var(--text-color);
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .schedule-info {
        font-size: 0.72rem;
        font-weight: 500;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .schedule-info i {
        color: var(--accent-color);
        width: 12px;
        text-align: center;
        opacity: 0.85;
    }
    
    /* Empty slot */
    .empty-card {
        border: 1px dashed #cbd5e1;
        background-color: rgba(248, 250, 252, 0.4);
        border-radius: 12px;
        min-height: 85px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #cbd5e1;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .today-column {
        background-color: rgba(78, 115, 223, 0.02);
        border-radius: 10px;
    }

    /* Soft BG alerts/badges */
    .bg-primary-soft { background-color: rgba(78, 115, 223, 0.08); }
    .bg-info-soft { background-color: rgba(54, 185, 204, 0.08); }
    .select-premium {
        height: 42px !important;
        border-radius: 8px !important;
        border: 1px solid #e1e5ef !important;
        background-color: #fff !important;
        font-weight: 500 !important;
        font-size: 0.9rem !important;
    }
    
    /* Timeline styles for list */
    .timeline-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.02);
        transition: all 0.25s ease;
        overflow: hidden;
    }
    .timeline-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 24px rgba(0,0,0,0.05) !important;
    }
    .timeline-list {
        position: relative;
        padding-left: 20px;
        margin: 5px 0;
    }
    .timeline-list::before {
        content: '';
        position: absolute;
        left: 4px;
        top: 0;
        width: 2px;
        height: 100%;
        background: #e2e8f0;
    }
    .timeline-item {
        position: relative;
        padding-bottom: 18px;
        list-style: none;
    }
    .timeline-item:last-child {
        padding-bottom: 0;
    }
    .timeline-badge {
        position: absolute;
        left: -21px;
        top: 5px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #cbd5e1;
        border: 2px solid #fff;
    }
    .timeline-time {
        font-size: 0.7rem;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 4px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .timeline-content {
        margin-left: 6px;
        background: #f8fafc;
        padding: 10px 14px;
        border-radius: 10px;
        border-left: 3px solid #cbd5e1;
    }
    .timeline-title {
        font-size: 0.85rem;
        font-weight: 700;
        color: #1e293b;
    }
    .timeline-subtitle {
        font-size: 0.72rem;
        color: #64748b;
        margin-top: 3px;
        font-weight: 500;
    }

    @media (max-width: 768px) {
        .table-timetable th, .table-timetable td {
            padding: 4px !important;
        }
        .schedule-cell {
            min-width: 120px;
        }
        .schedule-card {
            padding: 8px 10px;
            min-height: 75px;
        }
        .schedule-subject {
            font-size: 0.75rem;
            margin-bottom: 4px;
        }
        .schedule-info {
            font-size: 0.65rem;
        }
        .empty-card {
            min-height: 75px;
        }
    }
</style>
@endsection

