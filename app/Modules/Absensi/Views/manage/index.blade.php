@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">

    {{-- Stats Cards --}}
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm border-0">
                <span class="info-box-icon bg-info elevation-1 text-white"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-uppercase text-muted small font-weight-bold">Total Pegawai</span>
                    <span class="info-box-number h4 font-weight-bolder mb-0">{{ $stats['total'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm border-0">
                <span class="info-box-icon bg-success elevation-1 text-white"><i class="fas fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-uppercase text-muted small font-weight-bold">Hadir</span>
                    <span class="info-box-number h4 font-weight-bolder mb-0">{{ $stats['hadir'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm border-0">
                <span class="info-box-icon bg-warning elevation-1 text-white"><i class="fas fa-envelope-open-text"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-uppercase text-muted small font-weight-bold">Izin / Sakit</span>
                    <span class="info-box-number h4 font-weight-bolder mb-0">{{ $stats['izin'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm border-0">
                <span class="info-box-icon bg-danger elevation-1 text-white"><i class="fas fa-user-times"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-uppercase text-muted small font-weight-bold">Tanpa Keterangan</span>
                    <span class="info-box-number h4 font-weight-bolder mb-0">{{ $stats['alpa'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <x-card title="Daftar Kehadiran: {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}" icon="fas fa-calendar-alt">
        
        {{-- Filters --}}
        <div class="p-3 mb-4 bg-light rounded border shadow-xs">
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
                        <button type="submit" class="btn btn-primary btn-block shadow-sm">
                            <i class="fas fa-search mr-1"></i> Cari & Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="table-responsive">
            <table class="table table-hover table-striped border">
                <thead class="thead-dark text-center">
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

        <div class="mt-3">
            {{ $rekap->links() }}
        </div>
    </x-card>
</div>

<style>
    .table-danger-light { background-color: rgba(220, 53, 69, 0.05); }
</style>
@endsection
