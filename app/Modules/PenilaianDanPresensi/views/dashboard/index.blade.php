@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    {{-- Alert Messages --}}
    @if(session('success'))
        <x-alert type="success" :message="session('success')" dismissible />
    @endif
    @if(session('error'))
        <x-alert type="danger" :message="session('error')" dismissible />
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="m-0">Dashboard Penilaian dan Presensi</h4>
        <small class="text-muted">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</small>
    </div>

    {{-- Navigation Menu --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-md-3 border-right">
                            <a href="{{ route('penilaiandanpresensi.penilaianakademik.index') }}" class="d-block p-3 text-center text-decoration-none hover-shadow transition">
                                <i class="fas fa-book fa-2x mb-2" style="color: #007bff;"></i>
                                <p class="mb-0"><strong>Penilaian Akademik</strong></p>
                                <small class="text-muted">{{ $penilaianAkademikCount }} data</small>
                            </a>
                        </div>
                        <div class="col-md-3 border-right">
                            <a href="{{ route('penilaiandanpresensi.penilaiantahfidz.index') }}" class="d-block p-3 text-center text-decoration-none hover-shadow transition">
                                <i class="fas fa-quran fa-2x mb-2" style="color: #28a745;"></i>
                                <p class="mb-0"><strong>Penilaian Tahfidz</strong></p>
                                <small class="text-muted">{{ $penilaianTahfidzCount }} data</small>
                            </a>
                        </div>
                        <div class="col-md-3 border-right">
                            <a href="{{ route('penilaiandanpresensi.presensi.index') }}" class="d-block p-3 text-center text-decoration-none hover-shadow transition">
                                <i class="fas fa-calendar-check fa-2x mb-2" style="color: #17a2b8;"></i>
                                <p class="mb-0"><strong>Presensi</strong></p>
                                <small class="text-muted">{{ $presensiCount }} data</small>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('penilaiandanpresensi.izinsakit.index') }}" class="d-block p-3 text-center text-decoration-none hover-shadow transition">
                                <i class="fas fa-user-md fa-2x mb-2" style="color: #ffc107;"></i>
                                <p class="mb-0"><strong>Izin & Sakit</strong></p>
                                <small class="text-muted">{{ $izinSakitCount }} data</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Row --}}
    <div class="row">
        {{-- Left Column: Penilaian Akademik & Tahfidz --}}
        <div class="col-lg-6">
            {{-- Penilaian Akademik --}}
            <x-card title="Penilaian Akademik" icon="fas fa-book" class="mb-4">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1"><small class="text-muted">Total Penilaian</small></p>
                        <h4 class="font-weight-bold">{{ $penilaianAkademikCount }}</h4>
                    </div>
                    <div class="col-md-6 text-right">
                        <p class="mb-1"><small class="text-muted">Terbaru</small></p>
                        <p class="mb-0" style="font-size: 0.9rem;">{{ $recentPenilaianAkademik->first()?->created_at?->locale('id')->translatedFormat('d M Y') ?? '-' }}</p>
                    </div>
                </div>
                <hr>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width: 60%">Siswa</th>
                                <th style="width: 20%">Nilai</th>
                                <th style="width: 20%">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPenilaianAkademik as $item)
                            <tr>
                                <td>{{ $item->siswa->nama ?? '-' }}</td>
                                <td><strong>{{ $item->nilai ?? '-' }}</strong></td>
                                <td><small class="text-muted">{{ $item->created_at?->locale('id')->translatedFormat('d M') }}</small></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">Tidak ada data</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <x-slot name="footer">
                    <a href="{{ route('penilaiandanpresensi.penilaianakademik.index') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-arrow-right mr-1"></i> Lihat Semua
                    </a>
                </x-slot>
            </x-card>

            {{-- Penilaian Tahfidz --}}
            <x-card title="Penilaian Tahfidz" icon="fas fa-quran" class="mb-4">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1"><small class="text-muted">Total Penilaian</small></p>
                        <h4 class="font-weight-bold">{{ $penilaianTahfidzCount }}</h4>
                    </div>
                    <div class="col-md-6 text-right">
                        <p class="mb-1"><small class="text-muted">Terbaru</small></p>
                        <p class="mb-0" style="font-size: 0.9rem;">{{ $recentPenilaianTahfidz->first()?->created_at?->locale('id')->translatedFormat('d M Y') ?? '-' }}</p>
                    </div>
                </div>
                <hr>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width: 50%">Siswa</th>
                                <th style="width: 30%">Surat</th>
                                <th style="width: 20%">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPenilaianTahfidz as $item)
                            <tr>
                                <td>{{ $item->siswa->nama ?? '-' }}</td>
                                <td><span class="badge badge-info">{{ $item->surat ?? '-' }}</span></td>
                                <td><small class="text-muted">{{ $item->created_at?->locale('id')->translatedFormat('d M') }}</small></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">Tidak ada data</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <x-slot name="footer">
                    <a href="{{ route('penilaiandanpresensi.penilaiantahfidz.index') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-arrow-right mr-1"></i> Lihat Semua
                    </a>
                </x-slot>
            </x-card>
        </div>

        {{-- Right Column: Presensi & Izin Sakit --}}
        <div class="col-lg-6">
            {{-- Today's Presensi Summary --}}
            <x-card title="Presensi Hari Ini" icon="fas fa-clock" class="mb-4">
                <div class="row text-center">
                    <div class="col-6 col-md-6 mb-3">
                        <div class="p-3 rounded" style="background-color: #d4edda;">
                            <h5 class="font-weight-bold text-success mb-1">{{ $todayHadir }}</h5>
                            <small class="text-muted">Hadir</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-6 mb-3">
                        <div class="p-3 rounded" style="background-color: #fff3cd;">
                            <h5 class="font-weight-bold text-warning mb-1">{{ $todayIzin }}</h5>
                            <small class="text-muted">Izin</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-6 mb-3">
                        <div class="p-3 rounded" style="background-color: #d1ecf1;">
                            <h5 class="font-weight-bold text-info mb-1">{{ $todaySakit }}</h5>
                            <small class="text-muted">Sakit</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-6 mb-3">
                        <div class="p-3 rounded" style="background-color: #f8d7da;">
                            <h5 class="font-weight-bold text-danger mb-1">{{ $todayAlpha }}</h5>
                            <small class="text-muted">Alpha</small>
                        </div>
                    </div>
                </div>
                <x-slot name="footer">
                    <a href="{{ route('penilaiandanpresensi.presensi.index') }}" class="btn btn-sm btn-info">
                        <i class="fas fa-arrow-right mr-1"></i> Kelola Presensi
                    </a>
                </x-slot>
            </x-card>

            {{-- Presensi Terbaru --}}
            <x-card title="Presensi Terbaru" icon="fas fa-calendar-check" class="mb-4">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1"><small class="text-muted">Total Presensi</small></p>
                        <h4 class="font-weight-bold">{{ $presensiCount }}</h4>
                    </div>
                    <div class="col-md-6 text-right">
                        <p class="mb-1"><small class="text-muted">Terbaru</small></p>
                        <p class="mb-0" style="font-size: 0.9rem;">{{ $recentPresensi->first()?->created_at?->locale('id')->translatedFormat('d M Y') ?? '-' }}</p>
                    </div>
                </div>
                <hr>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width: 50%">Siswa</th>
                                <th style="width: 30%">Status</th>
                                <th style="width: 20%">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPresensi as $item)
                            <tr>
                                <td>{{ $item->siswa->nama ?? '-' }}</td>
                                <td>
                                    <span class="badge badge-{{ $item->status == 'Hadir' ? 'success' : ($item->status == 'Izin' ? 'warning' : ($item->status == 'Sakit' ? 'info' : 'danger')) }}">
                                        {{ $item->status }}
                                    </span>
                                </td>
                                <td><small class="text-muted">{{ $item->created_at?->locale('id')->translatedFormat('d M') }}</small></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">Tidak ada data</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <x-slot name="footer">
                    <a href="{{ route('penilaiandanpresensi.presensi.index') }}" class="btn btn-sm btn-info">
                        <i class="fas fa-arrow-right mr-1"></i> Lihat Semua
                    </a>
                </x-slot>
            </x-card>

            {{-- Izin & Sakit --}}
            <x-card title="Izin & Sakit" icon="fas fa-user-md" class="mb-4">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1"><small class="text-muted">Total Pengajuan</small></p>
                        <h4 class="font-weight-bold">{{ $izinSakitCount }}</h4>
                    </div>
                    <div class="col-md-6 text-right">
                        <p class="mb-1"><small class="text-muted">Pending</small></p>
                        <h4 class="font-weight-bold text-warning">{{ $pendingIzinSakit }}</h4>
                    </div>
                </div>
                <hr>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width: 50%">Siswa</th>
                                <th style="width: 30%">Jenis</th>
                                <th style="width: 20%">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentIzinSakit as $item)
                            <tr>
                                <td>{{ $item->siswa->nama ?? '-' }}</td>
                                <td>
                                    <span class="badge badge-{{ $item->jenis == 'Izin' ? 'warning' : 'danger' }}">
                                        {{ $item->jenis ?? '-' }}
                                    </span>
                                </td>
                                <td><small class="text-muted">{{ $item->created_at?->locale('id')->translatedFormat('d M') }}</small></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">Tidak ada data</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <x-slot name="footer">
                    <a href="{{ route('penilaiandanpresensi.izinsakit.index') }}" class="btn btn-sm btn-warning">
                        <i class="fas fa-arrow-right mr-1"></i> Lihat Semua
                    </a>
                </x-slot>
            </x-card>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .hover-shadow {
        transition: all 0.3s ease;
    }
    .hover-shadow:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        background-color: #f8f9fa;
    }
    .border-right {
        border-right: 1px solid #e3e6f0;
    }
    @media (max-width: 768px) {
        .border-right {
            border-right: none;
            border-bottom: 1px solid #e3e6f0;
        }
        .border-right:last-child {
            border-bottom: none;
        }
    }
</style>
@endpush