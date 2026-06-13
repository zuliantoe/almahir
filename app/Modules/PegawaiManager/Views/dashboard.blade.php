@extends('layouts.app')

@section('title', $title)

@push('styles')
<style>
    .glass-stat-card {
        border-radius: 20px;
        border: none;
        position: relative;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        z-index: 1;
    }
    .glass-stat-card:hover { 
        transform: translateY(-8px); 
    }
    .glass-stat-card::after {
        content: ''; position: absolute;
        top: -50%; left: -50%; width: 200%; height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
        transform: rotate(30deg); z-index: -1; pointer-events: none;
    }
    .glass-stat-card .inner-icon {
        width: 50px; height: 50px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        background: rgba(255,255,255,0.2); backdrop-filter: blur(5px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.1); transition: transform 0.3s;
    }
    .glass-stat-card:hover .inner-icon { transform: scale(1.1) rotate(5deg); }
    .glass-stat-card h3 { font-size: 2.2rem; font-weight: 800; margin: 0; font-family: 'Outfit', sans-serif; line-height: 1; margin-bottom: 5px; }

    /* Premium Gradients */
    .grad-users { background: linear-gradient(135deg, #4361ee, #4cc9f0); box-shadow: 0 10px 20px rgba(67,97,238,0.25); }
    .grad-guru { background: linear-gradient(135deg, #06d6a0, #118ab2); box-shadow: 0 10px 20px rgba(6,214,160,0.25); }
    .grad-calon { background: linear-gradient(135deg, #f72585, #b5179e); box-shadow: 0 10px 20px rgba(247,37,133,0.25); }
    .grad-hadir { background: linear-gradient(135deg, #ff9f1c, #ffbf69); box-shadow: 0 10px 20px rgba(255,159,28,0.25); }
    .grad-izin { background: linear-gradient(135deg, #ef233c, #d90429); box-shadow: 0 10px 20px rgba(239,35,60,0.25); }
    .grad-terlambat { background: linear-gradient(135deg, #e07a5f, #f4a261); box-shadow: 0 10px 20px rgba(224,122,95,0.25); }

    .chart-card { border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
    .chart-card .card-body { padding: 1.5rem !important; }
    .section-title { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1.2px; color: #64748b; margin-bottom: 20px; }
    
    .audit-item { border-left: 3px solid #e2e8f0; padding-left: 15px; margin-bottom: 15px; transition: all .2s ease; border-radius: 0 8px 8px 0; }
    .audit-item:hover { border-left-color: #4361ee; background: #f8fafc; padding: 10px 15px; margin-left: -10px; margin-right: -10px; }
    .audit-item .time-badge { font-size: 0.75rem; color: #94a3b8; margin-top: 5px; }
    .badge-seleksi { font-size: 0.75rem; font-weight: 700; border-radius: 12px; padding: 6px 12px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    
    .btn-gradient-primary { background: linear-gradient(135deg, #4361ee, #4cc9f0); color: white; border: none; }
    .btn-gradient-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(67, 97, 238, 0.4); color: white; }
    .btn-gradient-warning { background: linear-gradient(135deg, #ffd60a, #ff9f1c); color: #333; border: none; }
    .btn-gradient-warning:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(255, 159, 28, 0.4); color: #000; }
    .btn-gradient-success { background: linear-gradient(135deg, #06d6a0, #2dc653); color: white; border: none; }
    .btn-gradient-success:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(6, 214, 160, 0.4); color: white; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">

    {{-- ============================================================ --}}
    {{-- ROW 1: STATS CARDS --}}
    {{-- ============================================================ --}}
    <div class="row mb-4">
        <div class="col-lg-2 col-md-4 col-6 mb-3">
            <div class="card glass-stat-card grad-users h-100">
                <div class="card-body text-white d-flex flex-column justify-content-between p-3">
                    <div class="inner-icon mb-3">
                        <i class="fas fa-users text-white fa-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-white">{{ $totalSdm }}</h3>
                        <small class="text-white-50 font-weight-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.65rem;">Total Pegawai</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6 mb-3">
            <div class="card glass-stat-card grad-guru h-100">
                <div class="card-body text-white d-flex flex-column justify-content-between p-3">
                    <div class="inner-icon mb-3">
                        <i class="fas fa-chalkboard-teacher text-white fa-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-white">{{ $totalGuru }}</h3>
                        <small class="text-white-50 font-weight-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.65rem;">Total Guru</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6 mb-3">
            <div class="card glass-stat-card grad-calon h-100">
                <div class="card-body text-white d-flex flex-column justify-content-between p-3">
                    <div class="inner-icon mb-3">
                        <i class="fas fa-user-clock text-white fa-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-white">{{ $totalCalon }}</h3>
                        <small class="text-white-50 font-weight-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.65rem;">Calon Pegawai</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6 mb-3">
            <div class="card glass-stat-card grad-hadir h-100">
                <div class="card-body text-white d-flex flex-column justify-content-between p-3">
                    <div class="inner-icon mb-3">
                        <i class="fas fa-fingerprint text-white fa-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-white">{{ $hadirHariIni }}</h3>
                        <small class="text-white font-weight-bold text-uppercase" style="opacity: 0.8; letter-spacing: 0.5px; font-size: 0.65rem;">Hadir Hari Ini</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6 mb-3">
            <div class="card glass-stat-card grad-izin h-100">
                <div class="card-body text-white d-flex flex-column justify-content-between p-3">
                    <div class="inner-icon mb-3">
                        <i class="fas fa-envelope-open-text text-white fa-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-white">{{ $izinHariIni }}</h3>
                        <small class="text-white-50 font-weight-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.65rem;">Izin / Sakit</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6 mb-3">
            <div class="card glass-stat-card grad-terlambat h-100">
                <div class="card-body text-white d-flex flex-column justify-content-between p-3">
                    <div class="inner-icon mb-3">
                        <i class="fas fa-exclamation-triangle text-white fa-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-white">{{ $terlambatHariIni }}</h3>
                        <small class="text-white-50 font-weight-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.65rem;">Terlambat</small>
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
                <div class="card-body">
                    <p class="section-title"><i class="fas fa-chart-bar text-primary mr-2"></i> Tren Rekrutmen 6 Bulan Terakhir</p>
                    <div id="chartRekrutmen" style="min-height:260px;"></div>
                </div>
            </div>
        </div>
        {{-- PIE CHART: Komposisi Tipe + Gender --}}
        <div class="col-lg-4 mb-3">
            <div class="card chart-card shadow-sm h-100">
                <div class="card-body">
                    <p class="section-title"><i class="fas fa-chart-pie text-success mr-2"></i> Komposisi SDM</p>
                    <div id="chartKomposisiTipe" style="min-height:180px;"></div>
                    <hr class="my-3 border-light">
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
                <div class="card-body">
                    <p class="section-title"><i class="fas fa-chart-line text-info mr-2"></i> Tren Kehadiran 6 Bulan Terakhir</p>
                    <div id="chartKehadiran" style="min-height:260px;"></div>
                </div>
            </div>
        </div>
        {{-- STATUS SELEKSI + PINTASAN --}}
        <div class="col-lg-4 mb-3">
            <div class="card chart-card shadow-sm mb-4">
                <div class="card-body">
                    <p class="section-title"><i class="fas fa-funnel-dollar text-warning mr-2"></i> Pipeline Rekrutmen</p>
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
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 32px; height: 32px; background: {{ $stage['color'] }}20; color: {{ $stage['color'] }};">
                                <i class="fas {{ $stage['icon'] }}"></i>
                            </div>
                            <span class="font-weight-bold text-dark">{{ $stage['label'] }}</span>
                        </div>
                        <span class="badge badge-seleksi text-white" style="background: {{ $stage['color'] }}; min-width: 30px;">
                            {{ $statusSeleksi[$key] ?? 0 }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="card chart-card shadow-sm">
                <div class="card-body">
                    <p class="section-title"><i class="fas fa-rocket text-danger mr-2"></i> Pintasan Aksi</p>
                    <a href="{{ route('pegawaimanager.create') }}" class="btn btn-gradient-primary btn-block mb-3 rounded-pill font-weight-bold shadow-sm p-2">
                        <i class="fas fa-user-plus mr-2"></i> Tambah Pegawai Baru
                    </a>
                    <a href="{{ route('pegawaimanager.calon-pegawai.index') }}" class="btn btn-gradient-warning btn-block mb-3 rounded-pill font-weight-bold shadow-sm p-2">
                        <i class="fas fa-user-clock mr-2"></i> Manajemen Pelamar
                    </a>
                    <a href="{{ route('pegawaimanager.export') }}" class="btn btn-gradient-success btn-block rounded-pill font-weight-bold shadow-sm p-2">
                        <i class="fas fa-file-excel mr-2"></i> Export Data CSV
                    </a>
                    @if(auth()->user()->hasRole('SUPER_ADMIN'))
                    <hr class="my-3 border-light">
                    <a href="{{ route('system.backup') }}"
                       class="btn btn-dark btn-block rounded-pill font-weight-bold shadow-sm p-2"
                       onclick="return confirm('Download backup database sekarang?\nFile .sql akan terunduh otomatis.')">
                        <i class="fas fa-database mr-2"></i> Backup Database (.sql)
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
            <div class="card chart-card shadow-sm mb-4">
                <div class="card-body">
                    <p class="section-title"><i class="fas fa-history text-secondary mr-2"></i> Jejak Rekam Aktivitas (Audit Trail) — 15 Aktivitas Terakhir</p>
                    @if($activityLogs->isEmpty())
                        <div class="text-center text-muted py-5 bg-light rounded" style="border: 1px dashed #cbd5e1;">
                            <i class="fas fa-history fa-3x mb-3 text-gray-400"></i>
                            <h6 class="font-weight-bold">Belum Ada Aktivitas</h6>
                            <p class="small mb-0">Log aktivitas sistem akan muncul setelah data Pegawai dimanipulasi.</p>
                        </div>
                    @else
                    <div class="row">
                        @foreach($activityLogs as $log)
                        <div class="col-md-6 col-lg-4">
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
                                        <span class="badge badge-soft-{{ $eventColor }} badge-seleksi mr-1 mb-1" style="background: var(--{{ $eventColor }}); color: white;">
                                            <i class="fas {{ $eventIcon }} mr-1"></i>{{ strtoupper($log->event ?? 'log') }}
                                        </span>
                                        <div class="small text-dark font-weight-bold mt-1" style="line-height: 1.4;">{!! $log->description !!}</div>
                                    </div>
                                </div>
                                <div class="time-badge d-flex align-items-center">
                                    <i class="fas fa-user-circle mr-1 text-primary"></i> <span class="font-weight-bold text-dark mr-2">{{ $log->causer->name ?? 'Sistem' }}</span>
                                    <i class="far fa-clock mr-1"></i> {{ $log->created_at->diffForHumans() }}
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
        chart: { type: 'bar', height: 260, toolbar: { show: false }, fontFamily: 'Outfit, sans-serif' },
        series: [
            { name: 'Pegawai Masuk', data: trenRekrutmen.map(d => d.masuk) },
            { name: 'Lamaran Baru', data: trenRekrutmen.map(d => d.calon) },
        ],
        xaxis: { categories: trenRekrutmen.map(d => d.bulan), labels: { style: { colors: '#64748b' } } },
        colors: ['#4361ee', '#f72585'],
        plotOptions: { bar: { borderRadius: 6, columnWidth: '45%' } },
        dataLabels: { enabled: false },
        legend: { position: 'top', fontWeight: 600, labels: { colors: '#334155' } },
        grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
    }).render();

    // ============================================================
    // CHART 2: Komposisi Tipe Pegawai (Donut)
    // ============================================================
    if (komposisiTipe.length > 0) {
        new ApexCharts(document.querySelector('#chartKomposisiTipe'), {
            chart: { type: 'donut', height: 200, fontFamily: 'Outfit, sans-serif' },
            series: komposisiTipe.map(d => d.total),
            labels: komposisiTipe.map(d => d.label),
            colors: ['#4361ee','#06d6a0','#f72585','#ffd60a','#ef233c'],
            legend: { position: 'bottom', fontSize: '12px', fontWeight: 600, labels: { colors: '#334155' } },
            dataLabels: { enabled: false },
            plotOptions: { pie: { donut: { size: '65%', labels: { show: true, name: { show: true }, value: { show: true, fontSize: '20px', fontWeight: 700 } } } } },
        }).render();
    } else {
        document.querySelector('#chartKomposisiTipe').innerHTML = '<div class="text-center p-4 bg-light rounded"><i class="fas fa-chart-pie text-muted fa-2x mb-2"></i><p class="text-muted small mb-0">Belum ada data</p></div>';
    }

    // ============================================================
    // CHART 3: Komposisi Gender (Bar Horizontal)
    // ============================================================
    if (komposisiGender.length > 0) {
        new ApexCharts(document.querySelector('#chartGender'), {
            chart: { type: 'bar', height: 130, toolbar: { show: false }, fontFamily: 'Outfit, sans-serif' },
            series: [{ name: 'Jumlah', data: komposisiGender.map(d => d.total) }],
            xaxis: { categories: komposisiGender.map(d => d.label), labels: { style: { colors: '#64748b' } } },
            colors: ['#0096c7', '#f72585'],
            plotOptions: { bar: { horizontal: true, borderRadius: 6, barHeight: '50%' } },
            dataLabels: { enabled: true, style: { fontSize: '12px', fontWeight: 700 } },
            grid: { show: false },
        }).render();
    } else {
        document.querySelector('#chartGender').innerHTML = '<div class="text-center p-3 bg-light rounded"><p class="text-muted small mb-0">Data gender belum diisi.</p></div>';
    }

    // ============================================================
    // CHART 4: Tren Kehadiran (Line)
    // ============================================================
    new ApexCharts(document.querySelector('#chartKehadiran'), {
        chart: { type: 'area', height: 260, toolbar: { show: false }, fontFamily: 'Outfit, sans-serif' },
        series: [
            { name: 'Hadir', data: trenKehadiran.map(d => d.hadir) },
            { name: 'Terlambat', data: trenKehadiran.map(d => d.terlambat) },
        ],
        xaxis: { categories: trenKehadiran.map(d => d.bulan), labels: { style: { colors: '#64748b' } } },
        colors: ['#06d6a0', '#ff9f1c'],
        stroke: { curve: 'smooth', width: 3 },
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 90, 100] } },
        dataLabels: { enabled: false },
        legend: { position: 'top', fontWeight: 600, labels: { colors: '#334155' } },
        grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
    }).render();

});
</script>
@endpush
