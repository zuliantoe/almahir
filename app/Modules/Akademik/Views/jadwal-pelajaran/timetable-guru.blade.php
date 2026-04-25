@extends('layouts.app')

@section('title', 'Jadwal Mengajar Saya')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 16px; background: linear-gradient(135deg, #1e3c72 0%, #2a5298 60%, #3a6fd8 100%);">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-auto d-none d-md-block">
                            <div class="rounded-circle d-flex align-items-center justify-content-center shadow" style="width:60px;height:60px;background:rgba(255,255,255,0.15);">
                                <i class="fas fa-chalkboard-teacher fa-2x text-white"></i>
                            </div>
                        </div>
                        <div class="col text-white">
                            <h4 class="font-weight-bold mb-1">Jadwal Mengajar Mingguan</h4>
                            <p class="mb-0 opacity-75" style="opacity:.75;">
                                {{ $guru->nama ?? Auth::user()->name }}
                                &nbsp;&bull;&nbsp;
                                {{ $rawJadwal->count() }} sesi · {{ $rawJadwal->pluck('rombel.nama_rombel')->unique()->count() }} kelas
                            </p>
                        </div>
                        <div class="col-auto">
                            <div class="d-flex gap-2">
                                <a href="{{ route('akademik.jadwal-pelajaran.index', ['tampil' => 'all']) }}" class="btn btn-sm btn-light rounded-pill px-3 shadow-sm mr-2">
                                    <i class="fas fa-list mr-1"></i> Lihat Semua Jadwal
                                </a>
                                <span class="badge px-3 py-2 rounded-pill shadow-sm" style="background:rgba(255,255,255,0.2);color:#fff;font-size:.85rem;">
                                    <i class="far fa-calendar-alt mr-1"></i>
                                    {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($rawJadwal->isEmpty())
        {{-- Empty State --}}
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm text-center py-5" style="border-radius:16px;">
                    <div class="card-body">
                        <div class="d-inline-block rounded-circle p-4 mb-3" style="background:#f0f4ff;">
                            <i class="fas fa-calendar-times fa-3x" style="color:#6c8ebf;"></i>
                        </div>
                        <h5 class="font-weight-bold text-muted">Belum Ada Jadwal Mengajar</h5>
                        <p class="text-muted small">Hubungi admin untuk pengaturan jadwal Anda.</p>
                    </div>
                </div>
            </div>
        </div>
    @else

        {{-- Timetable Mingguan --}}
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius:16px;overflow:hidden;">
                    <div class="card-header border-0 py-3 d-flex align-items-center" style="background:linear-gradient(90deg,#1e3c72,#2a5298);">
                        <i class="fas fa-table mr-2 text-white"></i>
                        <span class="font-weight-bold text-white">Tabel Jadwal Mingguan</span>
                        <span class="ml-2 badge badge-light text-primary rounded-pill px-3">{{ $rawJadwal->count() }} Sesi</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0" id="timetable-guru" style="min-width:700px;">
                                <thead>
                                    <tr style="background:linear-gradient(90deg,#e8edf8,#f0f4ff);">
                                        <th class="text-center align-middle py-3" style="width:80px;font-size:.8rem;letter-spacing:.05em;color:#6c757d;text-transform:uppercase;background:#f8f9ff;border-right:2px solid #dee2f0;">Jam</th>
                                        @foreach($hariList as $hari)
                                            @php
                                                $isToday = \Carbon\Carbon::now()->locale('id')->translatedFormat('l') === $hari;
                                                $hasClass = isset($timetable[$hari]);
                                            @endphp
                                            <th class="text-center align-middle py-3 {{ $isToday ? 'today-col' : '' }}"
                                                style="font-size:.85rem;font-weight:700;{{ $isToday ? 'background:linear-gradient(180deg,#e8f0fe,#d2e3fc);color:#1a56db;' : 'color:#495057;' }}">
                                                {{ $hari }}
                                                @if($isToday)
                                                    <br><span class="badge badge-primary rounded-pill" style="font-size:.65rem;font-weight:600;">Hari Ini</span>
                                                @endif
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($usedJamKes as $jamke)
                                        @php
                                            // Cari waktu dari data yang ada
                                            $sampleJam = $rawJadwal->where('jamke', $jamke)->first();
                                        @endphp
                                        <tr class="timetable-row">
                                            <td class="text-center align-middle py-3" style="background:#f8f9ff;border-right:2px solid #dee2f0;">
                                                <div class="font-weight-bold" style="font-size:1.1rem;color:#1e3c72;">{{ $jamke }}</div>
                                                @if($sampleJam)
                                                    <div class="text-muted" style="font-size:.7rem;">
                                                        {{ substr($sampleJam->jamawal, 0, 5) }}<br>{{ substr($sampleJam->jamakhir, 0, 5) }}
                                                    </div>
                                                @endif
                                            </td>
                                            @foreach($hariList as $hari)
                                                @php
                                                    $j = $timetable[$hari][$jamke] ?? null;
                                                    $isToday = \Carbon\Carbon::now()->locale('id')->translatedFormat('l') === $hari;
                                                @endphp
                                                <td class="align-middle p-2 {{ $isToday ? 'today-col' : '' }}"
                                                    style="{{ $isToday ? 'background:rgba(26,86,219,.04);' : '' }}min-width:130px;">
                                                    @if($j)
                                                        @php
                                                            $colors = [
                                                                'Matematika' => ['bg'=>'#e3f2fd','border'=>'#1976d2','text'=>'#0d47a1'],
                                                                'Bahasa Inggris' => ['bg'=>'#e8f5e9','border'=>'#388e3c','text'=>'#1b5e20'],
                                                                'Fisika' => ['bg'=>'#fff3e0','border'=>'#f57c00','text'=>'#e65100'],
                                                                'Pendidikan Agama' => ['bg'=>'#f3e5f5','border'=>'#7b1fa2','text'=>'#4a148c'],
                                                                'Bahasa Arab' => ['bg'=>'#e8eaf6','border'=>'#3949ab','text'=>'#1a237e'],
                                                                'Tahfidz Al-Quran' => ['bg'=>'#fce4ec','border'=>'#c2185b','text'=>'#880e4f'],
                                                            ];
                                                            $mapelNama = $j->mataPelajaran->nama ?? 'Mapel';
                                                            $c = $colors[$mapelNama] ?? ['bg'=>'#f5f5f5','border'=>'#78909c','text'=>'#37474f'];
                                                        @endphp
                                                        <div class="jadwal-cell rounded shadow-xs p-2"
                                                             style="background:{{ $c['bg'] }};border-left:4px solid {{ $c['border'] }};cursor:default;" 
                                                             title="{{ $mapelNama }} | {{ optional($j->rombel)->nama_rombel }}">
                                                            <div class="font-weight-bold" style="font-size:.82rem;color:{{ $c['text'] }};line-height:1.3;">
                                                                {{ $mapelNama }}
                                                            </div>
                                                            <div class="mt-1 d-flex align-items-center">
                                                                <span class="badge rounded-pill" style="font-size:.68rem;background:rgba(0,0,0,.08);color:{{ $c['text'] }};font-weight:600;">
                                                                    <i class="fas fa-users mr-1"></i>{{ optional($j->rombel)->nama_rombel ?? '-' }}
                                                                </span>
                                                            </div>
                                                            <div class="text-muted mt-1" style="font-size:.68rem;">
                                                                <i class="far fa-clock mr-1"></i>{{ substr($j->jamawal,0,5) }}–{{ substr($j->jamakhir,0,5) }}
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="text-center" style="color:#dee2e6;">
                                                            <i class="fas fa-minus" style="font-size:.7rem;"></i>
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
            </div>
        </div>

        {{-- Ringkasan per Hari --}}
        <div class="row mt-4">
            <div class="col-12 mb-2">
                <h6 class="font-weight-bold text-muted text-uppercase" style="letter-spacing:.08em;font-size:.78rem;">
                    <i class="fas fa-chart-bar mr-1"></i> Ringkasan Per Hari
                </h6>
            </div>
            @foreach($hariList as $hari)
                @php
                    $jadwalHari = $rawJadwal->where('hari', $hari);
                    $isToday = \Carbon\Carbon::now()->locale('id')->translatedFormat('l') === $hari;
                @endphp
                @if($jadwalHari->isNotEmpty())
                <div class="col-sm-6 col-lg-4 mb-3">
                    <div class="card border-0 shadow-sm h-100" style="border-radius:12px;{{ $isToday ? 'border-left:4px solid #1a56db !important;' : '' }}">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="mr-2 rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width:32px;height:32px;background:{{ $isToday ? 'linear-gradient(135deg,#1e3c72,#2a5298)' : '#f0f4ff' }};">
                                    <i class="fas fa-calendar-day" style="font-size:.7rem;color:{{ $isToday ? '#fff' : '#1e3c72' }};"></i>
                                </div>
                                <span class="font-weight-bold" style="color:{{ $isToday ? '#1a56db' : '#495057' }};">{{ $hari }}</span>
                                @if($isToday)
                                    <span class="badge badge-primary rounded-pill ml-2" style="font-size:.65rem;">Hari Ini</span>
                                @endif
                                <span class="ml-auto badge badge-light text-muted">{{ $jadwalHari->count() }} sesi</span>
                            </div>
                            @foreach($jadwalHari->sortBy('jamke') as $j)
                                <div class="d-flex align-items-center py-1" style="border-bottom:1px dashed #f0f0f0;">
                                    <span class="badge badge-light text-primary mr-2" style="min-width:24px;">{{ $j->jamke }}</span>
                                    <span class="small font-weight-bold text-dark" style="flex:1;">{{ optional($j->mataPelajaran)->nama }}</span>
                                    <span class="badge rounded-pill text-muted" style="background:#f5f5f5;font-size:.67rem;">{{ optional($j->rombel)->nama_rombel }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>

    @endif
</div>

@push('styles')
<style>
    .timetable-row:hover td { background: rgba(26,86,219,.03) !important; }
    .today-col { background: rgba(26,86,219,.03); }
    .jadwal-cell { transition: transform .15s, box-shadow .15s; }
    .jadwal-cell:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,.12) !important; }
    #timetable-guru td, #timetable-guru th { vertical-align: middle; }
</style>
@endpush
@endsection
