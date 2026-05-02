@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">

    <div class="row">
        {{-- Summary Cards --}}
        <div class="col-md-4">
            <div class="glass-card hover-elevate bg-primary text-white mb-4 p-4" style="border-radius: 15px;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50 small font-weight-bold text-uppercase">Total Kehadiran</div>
                        <div class="display-4 font-weight-bold">{{ $stats['hadir'] }} <span class="h5">Hari</span></div>
                    </div>
                    <div class="bg-white rounded-circle p-3 shadow-sm text-primary">
                        <i class="fas fa-calendar-check fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card hover-elevate bg-warning text-white mb-4 p-4" style="border-radius: 15px;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50 small font-weight-bold text-uppercase">Total Terlambat</div>
                        <div class="display-4 font-weight-bold">{{ $stats['terlambat'] }} <span class="h5">Kali</span></div>
                    </div>
                    <div class="bg-white rounded-circle p-3 shadow-sm text-warning">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card hover-elevate bg-info text-white mb-4 p-4" style="border-radius: 15px;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50 small font-weight-bold text-uppercase">Izin / Sakit</div>
                        <div class="display-4 font-weight-bold">{{ $stats['izin'] }} <span class="h5">Hari</span></div>
                    </div>
                    <div class="bg-white rounded-circle p-3 shadow-sm text-info">
                        <i class="fas fa-envelope-open-text fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
        <div class="card-header gradient-primary border-0 p-4 d-flex justify-content-between align-items-center">
            <h3 class="card-title text-white font-weight-bold mb-0">
                <i class="fas fa-history mr-2"></i> Riwayat Absensi Saya
            </h3>
            <a href="{{ route('absensi.create') }}" class="btn btn-light text-primary btn-sm rounded-pill px-4 shadow-sm btn-animate font-weight-bold">
                <i class="fas fa-fingerprint mr-1"></i> Absen Sekarang
            </a>
        </div>
        
        <div class="card-body p-0">
            <table class="table table-premium table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="text-center border-0 text-muted" style="width: 50px;">No</th>
                        <th class="border-0 text-muted">Hari & Tanggal</th>
                        <th class="text-center border-0 text-muted">Jam Masuk</th>
                        <th class="text-center border-0 text-muted">Jam Pulang</th>
                        <th class="text-center border-0 text-muted">Status</th>
                        <th class="text-center border-0 text-muted">Durasi Kerja</th>
                        <th class="text-center border-0 text-muted">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($absensi as $index => $item)
                    <tr>
                        <td class="text-center">{{ $absensi->firstItem() + $index }}</td>
                        <td>
                            <div class="font-weight-bold text-dark">{{ $item->tanggal->translatedFormat('l, d F Y') }}</div>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-light border px-2 py-1">
                                <i class="far fa-clock mr-1 text-primary"></i> 
                                {{ $item->jam_masuk ? \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') : '--:--' }}
                            </span>
                            @if($item->lat_masuk)
                                <a href="https://www.google.com/maps?q={{ $item->lat_masuk }},{{ $item->long_masuk }}" target="_blank" class="ml-1" title="Lihat Lokasi Masuk">
                                    <i class="fas fa-map-marker-alt text-danger"></i>
                                </a>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge badge-light border px-2 py-1">
                                <i class="far fa-clock mr-1 text-secondary"></i> 
                                {{ $item->jam_pulang ? \Carbon\Carbon::parse($item->jam_pulang)->format('H:i') : '--:--' }}
                            </span>
                            @if($item->lat_pulang)
                                <a href="https://www.google.com/maps?q={{ $item->lat_pulang }},{{ $item->long_pulang }}" target="_blank" class="ml-1" title="Lihat Lokasi Pulang">
                                    <i class="fas fa-map-marker-alt text-danger"></i>
                                </a>
                            @endif
                        </td>
                        <td class="text-center">
                            @php
                                $statusClass = match($item->status) {
                                    'TEPAT WAKTU', 'HADIR' => 'success',
                                    'TERLAMBAT' => 'warning',
                                    'SAKIT', 'IZIN', 'CUTI', 'DINAS LUAR' => 'info',
                                    default => 'danger'
                                };
                            @endphp
                            <span class="badge badge-{{ $statusClass }} px-3 py-1 shadow-sm" style="min-width: 90px; font-weight: 700;">
                                {{ $item->status }}
                            </span>
                            @if($item->status == 'TERLAMBAT')
                                <div class="mt-1">
                                    <span class="badge badge-outline-danger text-xs border-danger" style="border: 1px solid red; color: red; padding: 1px 5px; border-radius: 4px;">
                                        <i class="fas fa-clock mr-1"></i> {{ $item->late_minutes }} Menit
                                    </span>
                                </div>
                            @endif
                        </td>
                        <td class="text-center">
                            <small class="font-italic text-muted">{{ $item->work_duration }}</small>
                        </td>
                        <td class="text-center">
                            <small>{{ $item->keterangan ?: '-' }}</small>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-calendar-times fa-3x mb-3 opacity-20"></i>
                            <p>Belum ada riwayat absensi.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-top bg-light">
            {{ $absensi->links() }}
        </div>
    </div>
</div>
@endsection
