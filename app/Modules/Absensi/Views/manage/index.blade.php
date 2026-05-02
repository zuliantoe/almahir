@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">

    {{-- Stats Cards --}}
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3 mb-4">
            <div class="glass-card hover-elevate p-3 border-0 h-100" style="border-left: 5px solid #17a2b8 !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-uppercase text-muted small font-weight-bold">Total Pegawai</span>
                        <div class="h3 font-weight-bolder mb-0 text-dark">{{ $stats['total'] }}</div>
                    </div>
                    <div class="bg-info-light rounded-circle p-3 text-info" style="background: rgba(23, 162, 184, 0.1);">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3 mb-4">
            <div class="glass-card hover-elevate p-3 border-0 h-100" style="border-left: 5px solid #28a745 !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-uppercase text-muted small font-weight-bold">Hadir Fisik</span>
                        <div class="h3 font-weight-bolder mb-0 text-success">{{ $stats['hadir'] }}</div>
                    </div>
                    <div class="bg-success-light rounded-circle p-3 text-success" style="background: rgba(40, 167, 69, 0.1);">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3 mb-4">
            <div class="glass-card hover-elevate p-3 border-0 h-100" style="border-left: 5px solid #ffc107 !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-uppercase text-muted small font-weight-bold">Izin / Sakit</span>
                        <div class="h3 font-weight-bolder mb-0 text-warning">{{ $stats['izin'] }}</div>
                    </div>
                    <div class="bg-warning-light rounded-circle p-3 text-warning" style="background: rgba(255, 193, 7, 0.1);">
                        <i class="fas fa-envelope-open-text fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3 mb-4">
            <div class="glass-card hover-elevate p-3 border-0 h-100" style="border-left: 5px solid #dc3545 !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-uppercase text-muted small font-weight-bold">Tanpa Keterangan</span>
                        <div class="h3 font-weight-bolder mb-0 text-danger">{{ $stats['alpa'] }}</div>
                    </div>
                    <div class="bg-danger-light rounded-circle p-3 text-danger" style="background: rgba(220, 53, 69, 0.1);">
                        <i class="fas fa-user-times fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
        <div class="card-header gradient-primary border-0 p-4">
            <h3 class="card-title text-white font-weight-bold mb-0 mt-1">
                <i class="fas fa-calendar-alt mr-2"></i> Daftar Kehadiran: {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}
            </h3>
            <div class="card-tools">
                <a href="{{ route('absensi.manage.export', ['date' => $selectedDate, 'search' => request('search')]) }}" class="btn btn-light text-success btn-sm rounded-pill px-4 shadow-sm btn-animate font-weight-bold">
                    <i class="fas fa-file-excel mr-1"></i> Export Laporan (CSV)
                </a>
            </div>
        </div>
        
        <div class="card-body p-4 bg-light">
        
        {{-- Filters --}}
        <div class="glass-card p-3 mb-4">
            <form action="{{ route('absensi.manage.index') }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <div class="form-group mb-0">
                            <label class="small font-weight-bold text-muted">Tanggal Pantauan</label>
                            <input type="date" name="date" class="form-control" value="{{ $selectedDate }}" onchange="this.form.submit()">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group mb-0">
                            <label class="small font-weight-bold text-muted">Cari Nama Pegawai</label>
                            <input type="text" name="search" class="form-control" placeholder="Ketik nama untuk mencari..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary btn-animate btn-block rounded-pill shadow-sm">
                            <i class="fas fa-search mr-1"></i> Cari & Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="table-responsive bg-white rounded shadow-sm border-0">
            <table class="table table-premium table-hover mb-0">
                <thead class="bg-light text-center">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th class="text-left">Pegawai</th>
                        <th>Jam Masuk</th>
                        <th>Jam Pulang</th>
                        <th>Status</th>
                        <th>Keterangan / Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekap as $index => $item)
                    <tr class="@if($item->status == 'ALPA') table-danger-light @endif">
                        <td class="text-center align-middle">{{ $rekap->firstItem() + $index }}</td>
                        <td class="align-middle">
                            <div class="d-flex align-items-center">
                                <img src="{{ $item->pegawai->user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($item->pegawai->nama) }}" 
                                     class="img-circle elevation-1 mr-2 border" style="width: 40px; height: 40px; object-fit: cover; background: #fff;">
                                <div>
                                    <div class="font-weight-bold text-dark">{{ $item->pegawai->nama }}</div>
                                    <small class="text-muted">{{ $item->pegawai->typePegawai->nama_type ?? 'Pegawai' }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="text-center align-middle font-weight-bold">
                            {{ $item->jam_masuk }}
                        </td>
                        <td class="text-center align-middle font-weight-bold">
                            {{ $item->jam_pulang }}
                        </td>
                        <td class="text-center align-middle">
                            <span class="badge badge-{{ $item->color }} px-3 py-2 shadow-sm" style="min-width: 100px; font-size: 0.85rem;">
                                {{ $item->status }}
                            </span>
                        </td>
                        <td class="text-center align-middle">
                            @if($item->izin_id)
                                <a href="{{ route('perizinan.show', $item->izin_id) }}" class="btn btn-xs btn-outline-info shadow-sm">
                                    <i class="fas fa-envelope-open-text mr-1"></i> Lihat Izin
                                </a>
                            @elseif($item->status == 'ALPA')
                                <span class="text-danger small font-weight-bold">Belum ada keterangan</span>
                            @elseif($item->status == 'LIBUR')
                                <span class="text-muted small italic">Hari Libur</span>
                            @else
                                <span class="text-success small font-weight-bold">Hadir Fisik</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted bg-white">
                            <i class="fas fa-user-clock fa-3x mb-3 opacity-2"></i>
                            <p>Data tidak ditemukan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $rekap->links() }}
        </div>
        
        </div>
    </div>
</div>

<style>
    .table-danger-light { background-color: rgba(220, 53, 69, 0.05); }
</style>
@endsection
