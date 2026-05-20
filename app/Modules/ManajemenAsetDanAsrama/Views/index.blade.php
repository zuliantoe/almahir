@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    {{-- Header Section --}}
    <div class="row mb-3">
        <div class="col-12">
            <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
            <p class="text-muted small">Ringkasan pengelolaan aset dan asrama santri hari ini.</p>
        </div>
    </div>

    {{-- Row 1: Aset Quick Info (4 Kotak) --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info shadow-sm">
                <div class="inner">
                    <h3>{{ number_format($totalAset ?? 0) }}</h3>
                    <p>Total Master Aset</p>
                </div>
                <div class="icon"><i class="fas fa-boxes"></i></div>
                <a href="{{ route('manajemenasetdanasrama.aset.index') }}" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success shadow-sm">
                <div class="inner">
                    <h3>{{ number_format($asetByStatus['baik'] ?? 0) }}</h3>
                    <p>Kondisi Baik</p>
                </div>
                <div class="icon"><i class="fas fa-check-circle"></i></div>
                <a href="{{ route('manajemenasetdanasrama.aset.index') }}" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger shadow-sm">
                <div class="inner">
                    <h3>{{ number_format($asetByStatus['rusak'] ?? 0) }}</h3>
                    <p>Kondisi Rusak</p>
                </div>
                <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
                <a href="{{ route('manajemenasetdanasrama.kerusakan.index') }}" class="small-box-footer">Lihat Laporan <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning shadow-sm">
                <div class="inner">
                    <h3>{{ number_format($asetByStatus['dalam_perbaikan'] ?? 0) }}</h3>
                    <p>Dalam Perbaikan</p>
                </div>
                <div class="icon"><i class="fas fa-wrench"></i></div>
                <a href="{{ route('manajemenasetdanasrama.pemeliharaan.index') }}" class="small-box-footer">Lihat Progress <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    {{-- Row 2: Asrama Quick Info (4 Kotak) --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-purple shadow-sm">
                <div class="inner">
                    <h3>{{ number_format($totalKamar ?? 0) }}</h3>
                    <p>Total Kamar</p>
                </div>
                <div class="icon"><i class="fas fa-door-open"></i></div>
                <a href="{{ route('manajemenasetdanasrama.kamar.index') }}" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-teal shadow-sm">
                <div class="inner">
                    <h3>{{ number_format($totalPenghuni ?? 0) }}</h3>
                    <p>Penghuni</p>
                </div>
                <div class="icon"><i class="fas fa-users"></i></div>
                <a href="{{ route('manajemenasetdanasrama.penghuni.index') }}" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning shadow-sm">
                <div class="inner">
                    <h3>{{ number_format($sisaKapasitas ?? 0) }}</h3>
                    <p>Sisa Kasur</p>
                </div>
                <div class="icon"><i class="fas fa-bed"></i></div>
                <a href="{{ route('manajemenasetdanasrama.kamar.index') }}" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-orange shadow-sm">
                <div class="inner">
                    <h3>{{ number_format($kamarPenuh ?? 0) }}</h3>
                    <p>Kamar Penuh</p>
                </div>
                <div class="icon"><i class="fas fa-door-closed"></i></div>
                <a href="{{ route('manajemenasetdanasrama.kamar.index') }}" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    {{-- Row 3: Jadwal Piket (Full Width with Smart Balancing) --}}
    <div class="row mt-2">
        <div class="col-12">
            <x-card title="Jadwal Piket Hari Ini" icon="fas fa-calendar-check" outline>
                <x-slot name="tools">
                    <span class="badge badge-light border px-2 py-1 mr-2">
                        <i class="fas fa-clock mr-1 text-primary"></i> {{ now()->translatedFormat('l, d F Y') }}
                    </span>
                    <a href="{{ route('manajemenasetdanasrama.jadwal-piket.index') }}" class="btn btn-xs btn-primary shadow-sm">
                        Kelola Semua <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </x-slot>

                <div class="card-body p-0">
                    @if($jadwalToday->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-mug-hot fa-3x mb-3 opacity-25"></i>
                            <h5 class="font-weight-bold">Alhamdulillah!</h5>
                            <p class="mb-0">Tidak ada jadwal piket yang terdaftar untuk hari ini.</p>
                        </div>
                    @else
                        @php
                            // Smart Balancing Logic
                            $leftColumn = collect();
                            $rightColumn = collect();
                            $leftTotal = 0;
                            $rightTotal = 0;

                            $sortedLocations = $jadwalToday->sortByDesc(function($items) {
                                return $items->count();
                            });

                            foreach($sortedLocations as $location => $items) {
                                if ($leftTotal <= $rightTotal) {
                                    $leftColumn->put($location, $items);
                                    $leftTotal += $items->count();
                                } else {
                                    $rightColumn->put($location, $items);
                                    $rightTotal += $items->count();
                                }
                            }
                        @endphp

                        <div class="row p-3">
                            {{-- Left Side --}}
                            <div class="col-md-6">
                                @foreach($leftColumn as $location => $items)
                                    <div class="card shadow-none border mb-4">
                                        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 font-weight-bold text-dark" style="font-size: 0.9rem;">
                                                <i class="fas fa-map-marker-alt mr-2 text-primary"></i> {{ $location ?: 'Umum' }}
                                            </h6>
                                            <span class="badge badge-pill badge-white border text-muted" style="font-size: 0.65rem;">
                                                {{ $items->count() }} Santri
                                            </span>
                                        </div>
                                        <div class="card-body p-0">
                                            <table class="table table-sm table-hover mb-0">
                                                <thead class="bg-gray-light text-muted small">
                                                    <tr>
                                                        <th class="pl-3 py-2 border-0">Nama Santri</th>
                                                        <th width="80" class="py-2 border-0">Shift</th>
                                                        <th width="90" class="text-center pr-3 py-2 border-0">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($items as $item)
                                                    <tr>
                                                        <td class="align-middle pl-3 py-2 font-weight-bold text-dark" style="font-size: 0.85rem;">{{ $item->siswa->nama ?? '-' }}</td>
                                                        <td class="align-middle py-2"><span class="badge badge-light border text-capitalize" style="font-size: 0.65rem;">{{ $item->shift }}</span></td>
                                                        <td class="text-center align-middle pr-3 py-2">
                                                            @if($item->status == 'sudah')
                                                                <span class="badge badge-success px-2 py-1" style="font-size: 0.6rem;">SELESAI</span>
                                                            @else
                                                                <span class="badge badge-warning px-2 py-1" style="font-size: 0.6rem;">BELUM</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Right Side --}}
                            <div class="col-md-6">
                                @foreach($rightColumn as $location => $items)
                                    <div class="card shadow-none border mb-4">
                                        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 font-weight-bold text-dark" style="font-size: 0.9rem;">
                                                <i class="fas fa-map-marker-alt mr-2 text-primary"></i> {{ $location ?: 'Umum' }}
                                            </h6>
                                            <span class="badge badge-pill badge-white border text-muted" style="font-size: 0.65rem;">
                                                {{ $items->count() }} Santri
                                            </span>
                                        </div>
                                        <div class="card-body p-0">
                                            <table class="table table-sm table-hover mb-0">
                                                <thead class="bg-gray-light text-muted small">
                                                    <tr>
                                                        <th class="pl-3 py-2 border-0">Nama Santri</th>
                                                        <th width="80" class="py-2 border-0">Shift</th>
                                                        <th width="90" class="text-center pr-3 py-2 border-0">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($items as $item)
                                                    <tr>
                                                        <td class="align-middle pl-3 py-2 font-weight-bold text-dark" style="font-size: 0.85rem;">{{ $item->siswa->nama ?? '-' }}</td>
                                                        <td class="align-middle py-2"><span class="badge badge-light border text-capitalize" style="font-size: 0.65rem;">{{ $item->shift }}</span></td>
                                                        <td class="text-center align-middle pr-3 py-2">
                                                            @if($item->status == 'sudah')
                                                                <span class="badge badge-success px-2 py-1" style="font-size: 0.6rem;">SELESAI</span>
                                                            @else
                                                                <span class="badge badge-warning px-2 py-1" style="font-size: 0.6rem;">BELUM</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </x-card>
        </div>
    </div>
</div>

<style>
    .small-box .icon {
        top: 10px;
        right: 15px;
        font-size: 50px;
        opacity: 0.3;
    }
    .bg-purple { background-color: #6f42c1 !important; color: #fff !important; }
    .bg-teal { background-color: #20c997 !important; color: #fff !important; }
    .bg-orange { background-color: #fd7e14 !important; color: #fff !important; }
    .bg-gray-light { background-color: #f8fafc; }
</style>
@endsection