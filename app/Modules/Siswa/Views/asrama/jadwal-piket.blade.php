@extends('layouts.app')

@section('title', $title)

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6"><h1 class="m-0 font-weight-bold text-dark">{{ $title }}</h1></div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right small bg-transparent p-0 m-0">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Jadwal Piket</li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <x-card title="Jadwal Piket Asrama" icon="fas fa-calendar-check" outline>
                
                {{-- FILTER PANEL SIMETRIS --}}
                <style>
                    .filter-bar-synced .form-control,
                    .filter-bar-synced .input-group-text,
                    .filter-bar-synced .btn,
                    .filter-bar-synced .select2-container--bootstrap4 .select2-selection--single {
                        height: 38px !important;
                        border-radius: 6px !important;
                        border: 1px solid #ced4da !important;
                        box-shadow: none !important;
                        font-size: 0.9rem !important;
                    }
                    .filter-bar-synced .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
                        line-height: 36px !important;
                        padding-top: 0 !important;
                        padding-bottom: 0 !important;
                        padding-left: 12px !important;
                    }
                    .filter-bar-synced .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
                        height: 36px !important;
                        top: 1px !important;
                    }
                    .filter-bar-synced .input-group .input-group-text {
                        border-top-right-radius: 0 !important;
                        border-bottom-right-radius: 0 !important;
                        border-right: 0 !important;
                        background-color: #ffffff !important;
                    }
                    .filter-bar-synced .input-group .form-control {
                        border-top-left-radius: 0 !important;
                        border-bottom-left-radius: 0 !important;
                        border-left: 0 !important;
                    }
                    .filter-bar-synced input[type="date"] {
                        padding: 0 12px !important;
                        line-height: 36px !important;
                    }
                    .filter-bar-synced .btn {
                        border: none !important;
                        font-weight: 600 !important;
                    }
                </style>
                <div class="card-body border-bottom bg-light p-4 filter-bar-synced">
                    <form action="{{ route('siswa.asrama.jadwal-piket.index') }}" method="GET">
                        <div class="row align-items-end">
                            {{-- CARI SANTRI --}}
                            <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                                <label class="small font-weight-bold text-muted uppercase mb-1">Cari Santri</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text text-muted"><i class="fas fa-search"></i></span></div>
                                    <input type="text" class="form-control" name="q" placeholder="Ketik nama..." value="{{ request('q') }}">
                                </div>
                            </div>

                            {{-- LOKASI PIKET --}}
                            <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                                <label class="small font-weight-bold text-muted uppercase mb-1">Lokasi Piket</label>
                                <select class="form-control select2" name="lokasi_piket" data-placeholder="Semua Lokasi" onchange="this.form.submit()">
                                    <option value="">Semua Lokasi</option>
                                    @foreach($lokasiList as $loc) 
                                        <option value="{{ $loc }}" {{ request('lokasi_piket') == $loc ? 'selected' : '' }}>{{ $loc }}</option> 
                                    @endforeach
                                </select>
                            </div>

                            {{-- TANGGAL MULAI --}}
                            <div class="col-lg-2 col-md-6 mb-3 mb-lg-0">
                                <label class="small font-weight-bold text-muted uppercase mb-1">Mulai Tanggal</label>
                                <input type="date" class="form-control" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}">
                            </div>

                            {{-- TANGGAL SELESAI --}}
                            <div class="col-lg-2 col-md-6 mb-3 mb-lg-0">
                                <label class="small font-weight-bold text-muted uppercase mb-1">Sampai Tanggal</label>
                                <input type="date" class="form-control" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}">
                            </div>

                            {{-- TOMBOL AKSI SIMETRIS --}}
                            <div class="col-lg-2 col-md-12">
                                <div class="d-flex" style="gap: 8px;">
                                    <button type="submit" class="btn btn-primary flex-grow-1 font-weight-bold">
                                        Filter
                                    </button>
                                    @if(request()->filled('q') || request()->filled('lokasi_piket') || request()->filled('tanggal_mulai') || request()->filled('tanggal_selesai'))
                                        <a href="{{ route('siswa.asrama.jadwal-piket.index') }}" class="btn btn-outline-secondary px-3 d-inline-flex align-items-center justify-content-center" title="Reset Filter">
                                            <i class="fas fa-sync-alt"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- DATA SECTION --}}
                <div class="card-body bg-light p-2 p-md-4">
                    @if($activeDate)
                        <div class="piket-day-group mb-4">
                            <div class="bg-dark px-4 py-3 border-left border-warning shadow-sm mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center" style="border-width: 5px !important; border-radius: 0 12px 12px 0; gap: 15px;">
                                <h5 class="mb-0 font-weight-bold text-white">
                                    <i class="fas fa-calendar-check mr-2 text-warning"></i> {{ \Carbon\Carbon::parse($activeDate)->translatedFormat('l, d F Y') }}
                                    @if(\Carbon\Carbon::parse($activeDate)->isToday())
                                        <span class="badge badge-pill badge-primary ml-2 font-weight-normal shadow-sm" style="font-size: 11px;">HARI INI</span>
                                    @endif
                                </h5>
                            </div>
                            
                            @php
                                $groupedByLocation = $jadwalData->groupBy('lokasi_piket');
                                $leftColumn = collect(); $rightColumn = collect(); $leftTotal = 0; $rightTotal = 0;
                                $sortedLocations = $groupedByLocation->sortByDesc(function($items) { return $items->count(); });
                                foreach($sortedLocations as $location => $items) {
                                    if ($leftTotal <= $rightTotal) { $leftColumn->put($location, $items); $leftTotal += $items->count(); } 
                                    else { $rightColumn->put($location, $items); $rightTotal += $items->count(); }
                                }
                            @endphp

                            <div class="row">
                                <div class="col-lg-6 mb-3 mb-lg-0">
                                    @foreach($leftColumn as $location => $items)
                                        <div class="card shadow-sm border-0 mb-4 animate__animated animate__fadeInUp" style="border-radius: 15px; overflow: hidden;">
                                            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                                                <h6 class="mb-0 font-weight-bold text-primary"><i class="fas fa-map-marker-alt mr-2 text-info"></i> {{ $location ?: 'Umum' }}</h6>
                                                <span class="badge badge-pill bg-light-info text-info px-2 py-1" style="font-size: 10px;">{{ $items->count() }} Santri</span>
                                            </div>
                                            <div class="card-body p-0">
                                                <div class="table-responsive"><table class="table table-sm table-hover mb-0"><thead class="bg-gray-light text-muted small uppercase"><tr><th width="75" class="pl-3 py-3 border-0">Shift</th><th class="py-3 border-0">Nama Santri</th><th width="115" class="text-center pr-3 py-3 border-0">Status</th></tr></thead>
                                                    <tbody>
                                                        @php $mySiswaId = auth()->user()->ref?->id; @endphp
                                                        @foreach($items as $item)
                                                        @php $isMyPiket = $item->siswa_id == $mySiswaId; @endphp
                                                        <tr style="{{ $isMyPiket ? 'background-color: rgba(255, 193, 7, 0.15); border-left: 4px solid #ffc107;' : '' }}">
                                                            <td class="align-middle pl-3 py-2 small font-weight-bold">{{ ucfirst($item->shift) }}</td>
                                                            <td class="align-middle py-2 font-weight-bold {{ $isMyPiket ? 'text-primary' : 'text-dark' }}" style="font-size: 13px;">
                                                                {{ $item->siswa->nama ?? '-' }}
                                                                @if($isMyPiket) <span class="badge badge-warning ml-1" style="font-size: 0.6rem;">Jadwal Saya</span> @endif
                                                            </td>
                                                            <td class="text-center align-middle pr-3 py-2">
                                                                <span class="badge badge-pill {{ $item->status == 'selesai' ? 'badge-success' : 'badge-warning' }} px-3 py-1">{{ ucfirst($item->status) }}</span>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="col-lg-6">
                                    @foreach($rightColumn as $location => $items)
                                        <div class="card shadow-sm border-0 mb-4 animate__animated animate__fadeInUp" style="border-radius: 15px; overflow: hidden;">
                                            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                                                <h6 class="mb-0 font-weight-bold text-primary"><i class="fas fa-map-marker-alt mr-2 text-info"></i> {{ $location ?: 'Umum' }}</h6>
                                                <span class="badge badge-pill bg-light-info text-info px-2 py-1" style="font-size: 10px;">{{ $items->count() }} Santri</span>
                                            </div>
                                            <div class="card-body p-0">
                                                <div class="table-responsive"><table class="table table-sm table-hover mb-0"><thead class="bg-gray-light text-muted small uppercase"><tr><th width="75" class="pl-3 py-3 border-0">Shift</th><th class="py-3 border-0">Nama Santri</th><th width="115" class="text-center pr-3 py-3 border-0">Status</th></tr></thead>
                                                    <tbody>
                                                        @php $mySiswaId = auth()->user()->ref?->id; @endphp
                                                        @foreach($items as $item)
                                                        @php $isMyPiket = $item->siswa_id == $mySiswaId; @endphp
                                                        <tr style="{{ $isMyPiket ? 'background-color: rgba(255, 193, 7, 0.15); border-left: 4px solid #ffc107;' : '' }}">
                                                            <td class="align-middle pl-3 py-2 small font-weight-bold">{{ ucfirst($item->shift) }}</td>
                                                            <td class="align-middle py-2 font-weight-bold {{ $isMyPiket ? 'text-primary' : 'text-dark' }}" style="font-size: 13px;">
                                                                {{ $item->siswa->nama ?? '-' }}
                                                                @if($isMyPiket) <span class="badge badge-warning ml-1" style="font-size: 0.6rem;">Jadwal Saya</span> @endif
                                                            </td>
                                                            <td class="text-center align-middle pr-3 py-2">
                                                                <span class="badge badge-pill {{ $item->status == 'selesai' ? 'badge-success' : 'badge-warning' }} px-3 py-1">{{ ucfirst($item->status) }}</span>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted bg-white border shadow-sm" style="border-radius: 20px;">
                            <h5 class="font-weight-bold text-dark mb-0">Tidak ada jadwal ditemukan!</h5>
                        </div>
                    @endif

                    {{-- DATE PAGINATION --}}
                    @if($paginatedDates->hasPages())
                        <div class="mt-4 d-flex flex-column flex-md-row justify-content-between align-items-center bg-white p-3 border shadow-sm" style="border-radius: 15px; gap: 15px;">
                            <div class="small text-muted font-weight-bold">Menampilkan Tanggal Ke-{{ $paginatedDates->currentPage() }} dari {{ $paginatedDates->total() }} Tanggal</div>
                            <div class="pagination-container">{{ $paginatedDates->links() }}</div>
                        </div>
                    @endif
                </div>
            </x-card>
        </div>
    </div>
</div>

<style>
    .bg-gray-light { background-color: #f8fafc; } .bg-light-info { background-color: #e0f2fe; }
</style>

@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        if ($('.select2').length) {
            $('.select2').select2({ theme: 'bootstrap4', width: '100%' });
        }
    });
</script>
@endpush
