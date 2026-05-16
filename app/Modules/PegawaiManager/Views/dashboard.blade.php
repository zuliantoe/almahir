@extends('layouts.app')

@section('title', $title)

@push('styles')
<style>
    .stat-card {
        border-radius: 16px;
        border: none;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        overflow: hidden;
    }
    .stat-card:hover { transform: translateY(-4px); box-shadow: 0 12px 30px rgba(0,0,0,0.12) !important; }
    .stat-card .inner-icon {
        width: 60px; height: 60px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem; opacity: 0.9;
    }
    .stat-card h3 { font-size: 2.2rem; font-weight: 800; margin: 0; }
    .chart-card { border-radius: 16px; border: none; }
    .audit-item { border-left: 3px solid #e9ecef; padding-left: 12px; margin-bottom: 12px; transition: border-color .2s; }
    .audit-item:hover { border-left-color: #007bff; }
    .audit-item .time-badge { font-size: 0.72rem; color: #adb5bd; }
    .section-title { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: #6c757d; margin-bottom: 14px; }
    .badge-seleksi { font-size: 0.78rem; font-weight: 600; border-radius: 20px; padding: 5px 12px; }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- ============================================================ --}}
    {{-- ROW 1: STATS CARDS --}}
    {{-- ============================================================ --}}
    <div class="row mb-4">
        <div class="col-lg-2 col-md-4 col-6 mb-3">
            <div class="card stat-card shadow-sm h-100" style="background: linear-gradient(135deg,#4361ee,#3a0ca3);">
                <div class="card-body text-white d-flex flex-column justify-content-between p-3">
                    <div class="inner-icon bg-white bg-opacity-25 mb-2" style="background:rgba(255,255,255,0.2)">
                        <i class="fas fa-users text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-white">{{ $totalSdm }}</h3>
                        <small class="text-white-50 font-weight-bold">Total Pegawai</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6 mb-3">
            <div class="card stat-card shadow-sm h-100" style="background: linear-gradient(135deg,#06d6a0,#0096c7);">
                <div class="card-body text-white d-flex flex-column justify-content-between p-3">
                    <div class="inner-icon mb-2" style="background:rgba(255,255,255,0.2)">
                        <i class="fas fa-chalkboard-teacher text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-white">{{ $totalGuru }}</h3>
                        <small class="text-white-50 font-weight-bold">Total Guru</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6 mb-3">
            <div class="card stat-card shadow-sm h-100" style="background: linear-gradient(135deg,#f72585,#b5179e);">
                <div class="card-body text-white d-flex flex-column justify-content-between p-3">
                    <div class="inner-icon mb-2" style="background:rgba(255,255,255,0.2)">
                        <i class="fas fa-user-clock text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-white">{{ $totalCalon }}</h3>
                        <small class="text-white-50 font-weight-bold">Calon Pegawai</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6 mb-3">
            <div class="card stat-card shadow-sm h-100" style="background: linear-gradient(135deg,#ffd60a,#fb8500);">
                <div class="card-body d-flex flex-column justify-content-between p-3">
                    <div class="inner-icon mb-2" style="background:rgba(255,255,255,0.2)">
                        <i class="fas fa-fingerprint text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-white">{{ $hadirHariIni }}</h3>
                        <small class="text-white font-weight-bold" style="opacity:.8">Hadir Hari Ini</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6 mb-3">
            <div class="card stat-card shadow-sm h-100" style="background: linear-gradient(135deg,#ef233c,#8d0801);">
                <div class="card-body text-white d-flex flex-column justify-content-between p-3">
                    <div class="inner-icon mb-2" style="background:rgba(255,255,255,0.2)">
                        <i class="fas fa-envelope-open-text text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-white">{{ $izinHariIni }}</h3>
                        <small class="text-white-50 font-weight-bold">Izin Hari Ini</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6 mb-3">
            <div class="card stat-card shadow-sm h-100" style="background: linear-gradient(135deg,#f77f00,#d62828);">
                <div class="card-body text-white d-flex flex-column justify-content-between p-3">
                    <div class="inner-icon mb-2" style="background:rgba(255,255,255,0.2)">
                        <i class="fas fa-exclamation-triangle text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-white">{{ $terlambatHariIni }}</h3>
                        <small class="text-white-50 font-weight-bold">Terlambat Hari Ini</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- ROW 2: CHART KIRI (Tren Rekrutmen) + KANAN (Pie Komposisi) --}}
    {{-- ============================================================ --}}
    <div class="row mb-4">
        {{-- BAR CHART: Tren Rekrutmen --}}
        <div class="col-lg-8 mb-3">
            <div class="card chart-card shadow-sm h-100">
                <div class="card-body p-4">
                    <p class="section-title"><i class="fas fa-chart-bar mr-1"></i> Tren Rekrutmen 6 Bulan Terakhir</p>
                    <div id="chartRekrutmen" style="min-height:260px;"></div>
                </div>
            </div>
        </div>
        {{-- PIE CHART: Komposisi Tipe + Gender --}}
        <div class="col-lg-4 mb-3">
            <div class="card chart-card shadow-sm h-100">
                <div class="card-body p-4">
                    <p class="section-title"><i class="fas fa-chart-pie mr-1"></i> Komposisi SDM</p>
                    <div id="chartKomposisiTipe" style="min-height:180px;"></div>
                    <hr class="my-2">
                    <div id="chartGender" style="min-height:130px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- ROW 3: LINE CHART Kehadiran + Status Seleksi + Pintasan --}}
    {{-- ============================================================ --}}
    <div class="row mb-4">
        {{-- LINE CHART: Kehadiran --}}
        <div class="col-lg-8 mb-3">
            <div class="card chart-card shadow-sm h-100">
                <div class="card-body p-4">
                    <p class="section-title"><i class="fas fa-chart-line mr-1"></i> Tren Kehadiran 6 Bulan Terakhir</p>
                    <div id="chartKehadiran" style="min-height:260px;"></div>
                </div>
            </div>
        </div>
        {{-- STATUS SELEKSI + PINTASAN --}}
        <div class="col-lg-4 mb-3">
            <div class="card chart-card shadow-sm mb-3">
                <div class="card-body p-4">
                    <p class="section-title"><i class="fas fa-funnel-dollar mr-1"></i> Pipeline Rekrutmen</p>
                    @php
                        $stages = [
                            'baru'       => ['label' => 'Lamaran Baru', 'color' => '#4361ee', 'icon' => 'fa-file-alt'],
                            'berkas'     => ['label' => 'Seleksi Berkas', 'color' => '#06d6a0', 'icon' => 'fa-folder-open'],
                            'wawancara'  => ['label' => 'Wawancara', 'color' => '#ffd60a', 'icon' => 'fa-comments'],
                            'diterima'   => ['label' => 'Diterima', 'color' => '#2dc653', 'icon' => 'fa-check-circle'],
                            'ditolak'    => ['label' => 'Ditolak', 'color' => '#ef233c', 'icon' => 'fa-times-circle'],
                        ];
                    @endphp
                    @foreach($stages as $key => $stage)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center">
                            <i class="fas {{ $stage['icon'] }} mr-2" style="color:{{ $stage['color'] }};width:16px;"></i>
                            <small class="font-weight-bold">{{ $stage['label'] }}</small>
                        </div>
                        <span class="badge badge-seleksi text-white" style="background:{{ $stage['color'] }}">
                            {{ $statusSeleksi[$key] ?? 0 }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="card chart-card shadow-sm">
                <div class="card-body p-4">
                    <p class="section-title"><i class="fas fa-rocket mr-1"></i> Pintasan Aksi</p>
                    <a href="{{ route('pegawaimanager.create') }}" class="btn btn-primary btn-block btn-sm mb-2 rounded-pill font-weight-bold shadow-sm">
                        <i class="fas fa-user-plus mr-1"></i> Tambah Pegawai
                    </a>
                    <a href="{{ route('pegawaimanager.calon-pegawai.index') }}" class="btn btn-warning btn-block btn-sm mb-2 rounded-pill font-weight-bold shadow-sm text-dark">
                        <i class="fas fa-user-clock mr-1"></i> Data Calon Pegawai
                    </a>
                    <a href="{{ route('pegawaimanager.export') }}" class="btn btn-success btn-block btn-sm rounded-pill font-weight-bold shadow-sm">
                        <i class="fas fa-file-excel mr-1"></i> Export Data
                    </a>
                    @if(auth()->user()->hasRole('SUPER_ADMIN'))
                    <hr class="my-2">
                    <a href="{{ route('system.backup') }}"
                       class="btn btn-dark btn-block btn-sm rounded-pill font-weight-bold shadow-sm"
                       onclick="return confirm('Download backup database sekarang?\nFile .sql akan terunduh otomatis.')">
                        <i class="fas fa-database mr-1"></i> Backup Database (.sql)
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- ROW 4: AUDIT TRAIL --}}
    {{-- ============================================================ --}}
    <div class="row">
        <div class="col-12">
            <div class="card chart-card shadow-sm">
                <div class="card-body p-4">
                    <p class="section-title"><i class="fas fa-history mr-1"></i> Jejak Rekam Aktivitas (Audit Trail) — 15 Aktivitas Terakhir</p>
                    @if($activityLogs->isEmpty())
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-history fa-3x mb-3 text-gray-300"></i>
                            <p>Belum ada aktivitas yang tercatat. Log akan muncul setelah data Pegawai diubah.</p>
                        </div>
                    @else
                    <div class="row">
                        @foreach($activityLogs as $log)
                        <div class="col-md-6">
                            <div class="audit-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        @php
                                            $eventColor = match($log->event ?? '') {
                                                'created' => 'success',
                                                'updated' => 'primary',
                                                'deleted' => 'danger',
                                                default => 'secondary'
                                            };
                                            $eventIcon = match($log->event ?? '') {
                                                'created' => 'fa-plus-circle',
                                                'updated' => 'fa-edit',
                                                'deleted' => 'fa-trash-alt',
                                                default => 'fa-dot-circle'
                                            };
                                        @endphp
                                        <span class="badge badge-{{ $eventColor }} badge-seleksi mr-1">
                                            <i class="fas {{ $eventIcon }} mr-1"></i>{{ strtoupper($log->event ?? 'log') }}
                                        </span>
                                        <span class="small text-dark">{!! $log->description !!}</span>
                                    </div>
                                </div>
                                <div class="time-badge mt-1">
                                    <i class="fas fa-user mr-1"></i>{{ $log->causer->name ?? 'Sistem' }}
                                    &nbsp;•&nbsp;
                                    <i class="fas fa-clock mr-1"></i>{{ $log->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
{{-- ApexCharts CDN --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts@latest"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ---- DATA dari Laravel ----
    const trenRekrutmen  = @json($trenRekrutmen);
    const trenKehadiran  = @json($trenKehadiran);
    const komposisiTipe  = @json($komposisiTipe);
    const komposisiGender = @json($komposisiGender);

    // ============================================================
    // CHART 1: Tren Rekrutmen (Bar)
    // ============================================================
    new ApexCharts(document.querySelector('#chartRekrutmen'), {
        chart: { type: 'bar', height: 260, toolbar: { show: false }, fontFamily: 'inherit' },
        series: [
            { name: 'Pegawai Masuk', data: trenRekrutmen.map(d => d.masuk) },
            { name: 'Lamaran Baru', data: trenRekrutmen.map(d => d.calon) },
        ],
        xaxis: { categories: trenRekrutmen.map(d => d.bulan) },
        colors: ['#4361ee', '#f72585'],
        plotOptions: { bar: { borderRadius: 6, columnWidth: '50%' } },
        dataLabels: { enabled: false },
        legend: { position: 'top' },
        grid: { borderColor: '#f1f3fa' },
    }).render();

    // ============================================================
    // CHART 2: Komposisi Tipe Pegawai (Donut)
    // ============================================================
    if (komposisiTipe.length > 0) {
        new ApexCharts(document.querySelector('#chartKomposisiTipe'), {
            chart: { type: 'donut', height: 180, fontFamily: 'inherit' },
            series: komposisiTipe.map(d => d.total),
            labels: komposisiTipe.map(d => d.label),
            colors: ['#4361ee','#06d6a0','#f72585','#ffd60a','#ef233c'],
            legend: { position: 'right', fontSize: '11px' },
            dataLabels: { enabled: true },
            plotOptions: { pie: { donut: { size: '55%' } } },
        }).render();
    } else {
        document.querySelector('#chartKomposisiTipe').innerHTML = '<p class="text-muted text-center small pt-3">Belum ada data tipe.</p>';
    }

    // ============================================================
    // CHART 3: Komposisi Gender (Bar Horizontal)
    // ============================================================
    if (komposisiGender.length > 0) {
        new ApexCharts(document.querySelector('#chartGender'), {
            chart: { type: 'bar', height: 130, toolbar: { show: false }, fontFamily: 'inherit' },
            series: [{ name: 'Jumlah', data: komposisiGender.map(d => d.total) }],
            xaxis: { categories: komposisiGender.map(d => d.label) },
            colors: ['#0096c7', '#f72585'],
            plotOptions: { bar: { horizontal: true, borderRadius: 4, barHeight: '60%' } },
            dataLabels: { enabled: true },
            grid: { show: false },
        }).render();
    } else {
        document.querySelector('#chartGender').innerHTML = '<p class="text-muted text-center small pt-2">Data gender belum diisi.</p>';
    }

    // ============================================================
    // CHART 4: Tren Kehadiran (Line)
    // ============================================================
    new ApexCharts(document.querySelector('#chartKehadiran'), {
        chart: { type: 'area', height: 260, toolbar: { show: false }, fontFamily: 'inherit' },
        series: [
            { name: 'Hadir', data: trenKehadiran.map(d => d.hadir) },
            { name: 'Terlambat', data: trenKehadiran.map(d => d.terlambat) },
        ],
        xaxis: { categories: trenKehadiran.map(d => d.bulan) },
        colors: ['#06d6a0', '#f77f00'],
        stroke: { curve: 'smooth', width: 2 },
        fill: { type: 'gradient', gradient: { opacityFrom: 0.4, opacityTo: 0.05 } },
        dataLabels: { enabled: false },
        legend: { position: 'top' },
        grid: { borderColor: '#f1f3fa' },
    }).render();

});
</script>
@endpush
