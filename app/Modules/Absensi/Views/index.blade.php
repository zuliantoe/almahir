@extends('layouts.app')

@section('title', $title)

@section('content')
<style>
    :root {
        --hadir-start: #4361ee;
        --hadir-end: #4cc9f0;
        --terlambat-start: #f72585;
        --terlambat-end: #ffb703;
        --izin-start: #06d6a0;
        --izin-end: #118ab2;
    }
    
    .glass-panel-card {
        border-radius: 20px;
        background: #ffffff;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.8);
    }

    .summary-card {
        border-radius: 24px;
        border: none;
        position: relative;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        z-index: 1;
    }
    
    .summary-card:hover {
        transform: translateY(-8px);
    }
    
    .summary-card::after {
        content: '';
        position: absolute;
        top: -50%; left: -50%; width: 200%; height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
        transform: rotate(30deg);
        z-index: -1;
        pointer-events: none;
    }

    .summary-card .icon-wrapper {
        width: 64px; 
        height: 64px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .summary-card:hover .icon-wrapper {
        transform: scale(1.1) rotate(5deg);
    }

    .gradient-hadir {
        background: linear-gradient(135deg, var(--hadir-start), var(--hadir-end));
        box-shadow: 0 15px 30px rgba(67, 97, 238, 0.25);
    }
    
    .gradient-terlambat {
        background: linear-gradient(135deg, var(--terlambat-start), var(--terlambat-end));
        box-shadow: 0 15px 30px rgba(247, 37, 133, 0.25);
    }
    
    .gradient-izin {
        background: linear-gradient(135deg, var(--izin-start), var(--izin-end));
        box-shadow: 0 15px 30px rgba(6, 214, 160, 0.25);
    }

    .table-premium th {
        background: #f8fafc;
        color: #64748b;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-bottom: 2px solid #e2e8f0 !important;
        padding: 1.2rem 1rem;
        font-weight: 700;
    }
    
    .table-premium td {
        padding: 1.2rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        transition: background-color 0.2s ease;
        color: #334155;
    }
    
    .table-premium tbody tr:hover td {
        background-color: #f8fafc;
    }

    .btn-maps-pin {
        width: 32px;
        height: 32px;
        border-radius: 10px !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background-color: #fee2e2;
        color: #ef4444;
        border: none;
        transition: all 0.2s ease;
        text-decoration: none !important;
    }
    .btn-maps-pin:hover {
        background-color: #ef4444;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3);
    }

    .badge-soft-success { background-color: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .badge-soft-warning { background-color: #fef9c3; color: #a16207; border: 1px solid #fef08a; }
    .badge-soft-danger { background-color: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
    .badge-soft-info { background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }

    .badge-time {
        background: #f1f5f9;
        color: #475569;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        font-size: 0.85rem;
        border: 1px solid #e2e8f0;
    }

    .card-header-modern {
        background: #1e293b;
        border-radius: 20px 20px 0 0 !important;
        position: relative;
        overflow: hidden;
    }
    
    .card-header-modern::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; height: 4px;
        background: linear-gradient(90deg, var(--hadir-start), var(--hadir-end), var(--izin-start));
    }
    
    .btn-absen {
        background: linear-gradient(135deg, var(--hadir-start), var(--hadir-end));
        color: white;
        border: none;
        border-radius: 12px;
        padding: 10px 24px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
    }
    .btn-absen:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(67, 97, 238, 0.4);
        color: white;
    }
    
    .empty-state-icon {
        width: 80px;
        height: 80px;
        background: #f1f5f9;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        color: #94a3b8;
    }
</style>

<div class="container-fluid py-4">

    {{-- Summary Cards --}}
    <div class="row mb-4">
        <div class="col-md-4 mb-4 mb-md-0">
            <div class="summary-card gradient-hadir text-white p-4 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50 small font-weight-bold text-uppercase mb-1" style="letter-spacing: 1px;">Total Kehadiran</div>
                        <div class="display-4 font-weight-bold" style="font-family: 'Outfit', sans-serif; line-height: 1.1;">
                            {{ $stats['hadir'] }} <span class="h5 font-weight-normal text-white-50 ml-1">Hari</span>
                        </div>
                    </div>
                    <div class="icon-wrapper">
                        <i class="fas fa-calendar-check fa-2x text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4 mb-md-0">
            <div class="summary-card gradient-terlambat text-white p-4 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50 small font-weight-bold text-uppercase mb-1" style="letter-spacing: 1px;">Total Terlambat</div>
                        <div class="display-4 font-weight-bold" style="font-family: 'Outfit', sans-serif; line-height: 1.1;">
                            {{ $stats['terlambat'] }} <span class="h5 font-weight-normal text-white-50 ml-1">Kali</span>
                        </div>
                    </div>
                    <div class="icon-wrapper">
                        <i class="fas fa-clock fa-2x text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-card gradient-izin text-white p-4 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50 small font-weight-bold text-uppercase mb-1" style="letter-spacing: 1px;">Izin / Sakit</div>
                        <div class="display-4 font-weight-bold" style="font-family: 'Outfit', sans-serif; line-height: 1.1;">
                            {{ $stats['izin'] }} <span class="h5 font-weight-normal text-white-50 ml-1">Hari</span>
                        </div>
                    </div>
                    <div class="icon-wrapper">
                        <i class="fas fa-envelope-open-text fa-2x text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main History Card --}}
    <div class="card border-0 glass-panel-card">
        <div class="card-header border-0 p-4 d-flex flex-wrap justify-content-between align-items-center card-header-modern">
            <h5 class="card-title text-white font-weight-bold mb-0">
                <i class="fas fa-clipboard-list text-info mr-2"></i> Riwayat Absensi Saya
            </h5>
            <a href="{{ route('absensi.create') }}" class="btn-absen mt-3 mt-sm-0">
                <i class="fas fa-fingerprint mr-2"></i> Absen Sekarang
            </a>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-premium mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 60px;">No</th>
                            <th>Hari & Tanggal</th>
                            <th class="text-center">Jam Masuk</th>
                            <th class="text-center">Jam Pulang</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Durasi Kerja</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($absensi as $index => $item)
                        <tr>
                            <td class="text-center align-middle text-muted font-weight-bold">{{ $absensi->firstItem() + $index }}</td>
                            <td class="align-middle">
                                <div class="font-weight-bold text-dark mb-1">{{ $item->tanggal->translatedFormat('l, d F Y') }}</div>
                                <div class="text-muted small"><i class="far fa-calendar-alt mr-1"></i> Jadwal Reguler</div>
                            </td>
                            <td class="text-center align-middle">
                                <div class="d-flex align-items-center justify-content-center">
                                    <span class="badge-time">
                                        <i class="far fa-clock mr-1 text-primary"></i> 
                                        {{ $item->jam_masuk ? \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') : '--:--' }}
                                    </span>
                                    @if($item->lat_masuk)
                                        <a href="https://www.google.com/maps?q={{ $item->lat_masuk }},{{ $item->long_masuk }}" target="_blank" class="btn-maps-pin ml-2" data-toggle="tooltip" title="Lihat Peta Lokasi Masuk">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center align-middle">
                                <div class="d-flex align-items-center justify-content-center">
                                    <span class="badge-time">
                                        <i class="far fa-clock mr-1 text-secondary"></i> 
                                        {{ $item->jam_pulang ? \Carbon\Carbon::parse($item->jam_pulang)->format('H:i') : '--:--' }}
                                    </span>
                                    @if($item->lat_pulang)
                                        <a href="https://www.google.com/maps?q={{ $item->lat_pulang }},{{ $item->long_pulang }}" target="_blank" class="btn-maps-pin ml-2" data-toggle="tooltip" title="Lihat Peta Lokasi Pulang">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center align-middle">
                                @php
                                    $statusClass = match($item->status) {
                                        'TEPAT WAKTU', 'HADIR' => 'badge-soft-success',
                                        'TERLAMBAT' => 'badge-soft-warning',
                                        'SAKIT', 'IZIN', 'CUTI', 'DINAS LUAR' => 'badge-soft-info',
                                        default => 'badge-soft-danger'
                                    };
                                    
                                    $statusText = match($item->status) {
                                        'TEPAT WAKTU' => 'TEPAT WAKTU',
                                        'TERLAMBAT' => 'TERLAMBAT',
                                        'SAKIT' => 'SAKIT',
                                        'IZIN' => 'IZIN',
                                        default => $item->status
                                    };
                                @endphp
                                <div class="d-flex flex-column align-items-center">
                                    <span class="badge {{ $statusClass }} px-3 py-2 rounded-pill" style="font-weight: 700; font-size: 0.75rem; letter-spacing: 0.5px;">
                                        {{ $statusText }}
                                    </span>
                                    @if($item->status == 'TERLAMBAT')
                                        <div class="mt-2">
                                            <span class="badge badge-soft-danger px-2 py-1 rounded" style="font-size: 0.7rem;">
                                                <i class="fas fa-exclamation-circle mr-1"></i> Telat {{ abs((int)$item->late_minutes) }} Mnt
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center align-middle">
                                <span class="badge badge-light border text-dark font-weight-bold px-3 py-2 rounded">{{ $item->work_duration ?: '-' }}</span>
                            </td>
                            <td class="align-middle">
                                <span class="text-muted small {{ !$item->keterangan ? 'font-italic' : '' }}">{{ $item->keterangan ?: '-' }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="py-5">
                                    <div class="empty-state-icon mb-3">
                                        <i class="fas fa-clipboard-check fa-2x"></i>
                                    </div>
                                    <h5 class="font-weight-bold text-dark mb-2">Belum Ada Riwayat Kehadiran</h5>
                                    <p class="text-muted mb-0">Anda belum mencatatkan kehadiran pada periode ini.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($absensi->hasPages())
        <div class="card-footer bg-white p-4 border-top d-flex justify-content-center">
            {{ $absensi->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endpush
@endsection
