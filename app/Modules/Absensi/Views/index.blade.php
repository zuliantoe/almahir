@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">

    <div class="row">
        {{-- Summary Cards --}}
        <div class="col-md-4">
            <x-card class="bg-primary text-white mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50 small">Total Kehadiran</div>
                        <div class="text-lg font-weight-bold">{{ $stats['hadir'] }} Hari</div>
                    </div>
                    <i class="fas fa-calendar-check fa-2x opacity-50"></i>
                </div>
            </x-card>
        </div>
        <div class="col-md-4">
            <x-card class="bg-warning text-white mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50 small">Total Terlambat</div>
                        <div class="text-lg font-weight-bold">{{ $stats['terlambat'] }} Kali</div>
                    </div>
                    <i class="fas fa-clock fa-2x opacity-50"></i>
                </div>
            </x-card>
        </div>
        <div class="col-md-4">
            <x-card class="bg-info text-white mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50 small">Izin / Sakit</div>
                        <div class="text-lg font-weight-bold">{{ $stats['izin'] }} Hari</div>
                    </div>
                    <i class="fas fa-envelope-open-text fa-2x opacity-50"></i>
                </div>
            </x-card>
        </div>
    </div>

    <x-card title="Riwayat Absensi Saya" icon="fas fa-history">
        <x-slot name="tools">
            <a href="{{ route('absensi.create') }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                <i class="fas fa-fingerprint mr-1"></i> Absen Sekarang
            </a>
        </x-slot>

        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th class="text-center" style="width: 50px;">No</th>
                        <th>Hari & Tanggal</th>
                        <th class="text-center">Jam Masuk</th>
                        <th class="text-center">Jam Pulang</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Durasi Kerja</th>
                        <th class="text-center">Keterangan</th>
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

        <div class="mt-3">
            {{ $absensi->links() }}
        </div>
    </x-card>
</div>
@endsection
