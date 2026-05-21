@extends('layouts.app')

@section('title', $title)

@php
    $isSiswa = auth()->user()->hasRole('SISWA');
    $isGuru = auth()->user()->hasRole('GURU');
    $canManagePiket = !$isSiswa && !$isGuru;
    $canCheckOffPiket = !$isSiswa;
@endphp

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6"><h1 class="m-0 font-weight-bold text-dark">{{ $title }}</h1></div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right small bg-transparent p-0 m-0">
            <li class="breadcrumb-item"><a href="{{ route('manajemenasetdanasrama.index') }}">Manajemen Aset & Asrama</a></li>
            <li class="breadcrumb-item"><a href="{{ route('manajemenasetdanasrama.jadwal-piket.index') }}">Jadwal Piket</a></li>
            <li class="breadcrumb-item active">Evaluasi Piket</li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    
    {{-- TAB NAVIGATION --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="card-body p-2 bg-white">
                    <ul class="nav nav-pills nav-fill" id="evaluasiTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link py-3 {{ $activeTab === 'harian' ? 'active font-weight-bold bg-primary text-white shadow-sm' : 'text-muted bg-transparent' }}" 
                               href="{{ route('manajemenasetdanasrama.jadwal-piket.evaluasi', ['tab' => 'harian']) }}" 
                               style="border-radius: 10px; transition: all 0.3s ease;">
                                <i class="fas fa-calendar-day mr-2"></i> Evaluasi Harian
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link py-3 {{ $activeTab === 'rekap' ? 'active font-weight-bold bg-primary text-white shadow-sm' : 'text-muted bg-transparent' }}" 
                               href="{{ route('manajemenasetdanasrama.jadwal-piket.evaluasi', ['tab' => 'rekap']) }}" 
                               style="border-radius: 10px; transition: all 0.3s ease;">
                                <i class="fas fa-chart-bar mr-2"></i> Rekap Performa Santri
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- STATS SECTION DYNAMIC --}}
    @if($activeTab === 'harian')
        <div class="row">
            <div class="col-lg-4 col-6">
                <div class="small-box bg-info shadow-sm" style="border-radius: 12px; overflow: hidden;">
                    <div class="inner">
                        <h3>{{ number_format($statsHarian['total']) }}</h3>
                        <p>Total Jadwal Piket (Hari Ini)</p>
                    </div>
                    <div class="icon"><i class="fas fa-clipboard-list"></i></div>
                </div>
            </div>
            <div class="col-lg-4 col-6">
                <div class="small-box bg-success shadow-sm" style="border-radius: 12px; overflow: hidden;">
                    <div class="inner">
                        <h3>{{ $statsHarian['rate'] }}%</h3>
                        <p>Tingkat Kerajinan Harian</p>
                    </div>
                    <div class="icon"><i class="fas fa-star"></i></div>
                </div>
            </div>
            <div class="col-lg-4 col-6">
                <div class="small-box bg-warning shadow-sm" style="border-radius: 12px; overflow: hidden;">
                    <div class="inner">
                        <h3>{{ number_format($statsHarian['belum']) }}</h3>
                        <p>Belum Piket / Belum Selesai</p>
                    </div>
                    <div class="icon"><i class="fas fa-hourglass-half"></i></div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-lg-4 col-6">
                <div class="small-box bg-info shadow-sm" style="border-radius: 12px; overflow: hidden;">
                    <div class="inner">
                        <h3>{{ number_format($statsRekap['total']) }}</h3>
                        <p>Total Penugasan Piket</p>
                    </div>
                    <div class="icon"><i class="fas fa-history"></i></div>
                </div>
            </div>
            <div class="col-lg-4 col-6">
                <div class="small-box bg-success shadow-sm" style="border-radius: 12px; overflow: hidden;">
                    <div class="inner">
                        <h3>{{ $statsRekap['rate'] }}%</h3>
                        <p>Rata-rata Tingkat Kerajinan</p>
                    </div>
                    <div class="icon"><i class="fas fa-percentage"></i></div>
                </div>
            </div>
            <div class="col-lg-4 col-6">
                <div class="small-box bg-danger shadow-sm" style="border-radius: 12px; overflow: hidden;">
                    <div class="inner">
                        <h3>{{ number_format($statsRekap['belum']) }}</h3>
                        <p>Total Piket Terlewat</p>
                    </div>
                    <div class="icon"><i class="fas fa-exclamation-circle"></i></div>
                </div>
            </div>
        </div>
    @endif

    @if(session('success')) <x-alert type="success" :message="session('success')" dismissible /> @endif
    @if(session('error')) <x-alert type="danger" :message="session('error')" dismissible /> @endif
    @if(session('warning')) <x-alert type="warning" :message="session('warning')" dismissible /> @endif

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
        .bg-gray-light { background-color: #f8fafc; }
        .btn-action { width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; border-radius: 10px; border: none; transition: all 0.3s ease; font-size: 13px; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .btn-soft-success { background: #dcfce7; color: #16a34a; } .btn-soft-success:hover { background: #16a34a; color: white; transform: translateY(-3px); box-shadow: 0 5px 15px rgba(22, 163, 74, 0.3); }
        .progress-xxs { height: 6px; border-radius: 3px; background-color: #e9ecef; }
    </style>

    <div class="row">
        <div class="col-md-12">
            
            {{-- TAB CONTENT 1: EVALUASI HARIAN --}}
            @if($activeTab === 'harian')
                <x-card title="Evaluasi Jadwal Piket Harian" icon="fas fa-calendar-check" outline>
                    <x-slot name="tools">
                        <a href="{{ route('manajemenasetdanasrama.jadwal-piket.index') }}" class="btn btn-sm btn-secondary shadow-sm px-3" style="border-radius: 8px;">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Jadwal
                        </a>
                    </x-slot>

                    <div class="card-body p-4 bg-light">
                        
                        {{-- DATE NAVIGATION BAR --}}
                        @if($activeDate)
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center mb-4 bg-white p-3 shadow-sm border" style="border-radius: 12px; gap: 15px;">
                                <div>
                                    @if($paginator->currentPage() < $paginator->total())
                                        <a href="{{ $paginator->url($paginator->currentPage() + 1) }}" class="btn btn-sm btn-outline-primary shadow-sm font-weight-bold" style="border-radius: 8px; transition: all 0.2s;">
                                            <i class="fas fa-chevron-left mr-1"></i> Hari Sebelumnya
                                        </a>
                                    @else
                                        <button class="btn btn-sm btn-outline-secondary font-weight-bold" disabled style="border-radius: 8px;">
                                            <i class="fas fa-chevron-left mr-1"></i> Hari Sebelumnya
                                        </button>
                                    @endif
                                </div>
                                
                                <div class="text-center">
                                    <h5 class="mb-0 font-weight-bold text-dark">
                                        <i class="fas fa-calendar-day text-primary mr-2"></i>
                                        {{ \Carbon\Carbon::parse($activeDate)->translatedFormat('l, d F Y') }}
                                        @if(\Carbon\Carbon::parse($activeDate)->isToday())
                                            <span class="badge badge-pill badge-primary ml-2 font-weight-normal shadow-sm" style="font-size: 11px; padding: 4px 10px;">HARI INI</span>
                                        @endif
                                    </h5>
                                </div>

                                <div>
                                    @if($paginator->currentPage() > 1)
                                        <a href="{{ $paginator->url($paginator->currentPage() - 1) }}" class="btn btn-sm btn-outline-primary shadow-sm font-weight-bold" style="border-radius: 8px; transition: all 0.2s;">
                                            Hari Berikutnya <i class="fas fa-chevron-right ml-1"></i>
                                        </a>
                                    @else
                                        <button class="btn btn-sm btn-outline-secondary font-weight-bold" disabled style="border-radius: 8px;">
                                            Hari Berikutnya <i class="fas fa-chevron-right ml-1"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 font-weight-bold text-primary"><i class="fas fa-tasks mr-2 text-info"></i> Daftar Santri Piket</h6>
                                <span class="badge badge-pill bg-light-info text-info px-3 py-1 font-weight-bold" style="font-size: 11px;">{{ count($harianJadwal) }} Santri Bertugas</span>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="bg-gray-light text-muted small uppercase">
                                            <tr>
                                                <th class="pl-4 py-3 border-0" width="60">No</th>
                                                <th class="py-3 border-0">Nama Santri</th>
                                                <th class="py-3 border-0">Kamar</th>
                                                <th class="py-3 border-0" width="100">Shift</th>
                                                <th class="py-3 border-0">Lokasi Piket</th>
                                                <th class="py-3 border-0 text-center" width="160">Status Kerajinan</th>
                                                @if($canCheckOffPiket)
                                                    <th class="py-3 border-0 text-center pr-4" width="100">Aksi</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($harianJadwal as $index => $item)
                                                <tr>
                                                    <td class="align-middle pl-4 py-3 font-weight-bold text-muted">{{ $index + 1 }}</td>
                                                    <td class="align-middle py-3">
                                                        <div class="font-weight-bold text-dark" style="font-size: 14px;">{{ $item->siswa->nama ?? '-' }}</div>
                                                        <div class="text-muted small">NIS: {{ $item->siswa->nis ?? '-' }}</div>
                                                    </td>
                                                    <td class="align-middle py-3 font-weight-bold text-muted">
                                                        {{ $item->kamar->nama_kamar ?? '-' }}
                                                    </td>
                                                    <td class="align-middle py-3">
                                                        <span class="badge badge-pill badge-secondary text-uppercase px-2 py-1" style="font-size: 10px;">{{ $item->shift }}</span>
                                                    </td>
                                                    <td class="align-middle py-3">
                                                        <div class="text-dark font-weight-bold"><i class="fas fa-map-marker-alt text-info mr-1"></i> {{ $item->lokasi_piket ?: 'Umum' }}</div>
                                                    </td>
                                                    <td class="align-middle py-3 text-center">
                                                        @if($item->status == 'sudah' || $item->status == 'selesai')
                                                            <span class="badge badge-pill badge-success px-3 py-1 font-weight-bold" style="border-radius: 8px;">
                                                                <i class="fas fa-check-circle mr-1"></i> Rajin (Selesai)
                                                            </span>
                                                        @else
                                                            <span class="badge badge-pill badge-warning px-3 py-1 font-weight-bold" style="border-radius: 8px;">
                                                                <i class="fas fa-clock mr-1"></i> Belum Selesai
                                                            </span>
                                                        @endif
                                                    </td>
                                                    @if($canCheckOffPiket)
                                                        <td class="text-center align-middle pr-4 py-3">
                                                            @if($item->status == 'belum')
                                                                <form action="{{ route('manajemenasetdanasrama.jadwal-piket.selesai', $item->id) }}" method="POST" class="m-0 d-inline-block">
                                                                    @csrf
                                                                    <button type="submit" class="btn-action btn-soft-success" title="Tandai Selesai Piket">
                                                                        <i class="fas fa-check"></i>
                                                                    </button>
                                                                </form>
                                                            @else
                                                                <span class="text-success small font-weight-bold"><i class="fas fa-check-double mr-1"></i> Selesai</span>
                                                            @endif
                                                        </td>
                                                    @endif
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center py-5 text-muted">
                                                        <i class="fas fa-calendar-times fa-3x mb-3 text-muted" style="opacity: 0.5;"></i>
                                                        <p class="mb-0 font-weight-bold">Tidak ada jadwal piket yang tercatat pada hari ini.</p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- PAGE DATE PAGINATOR (BOTTOM) --}}
                        @if($paginator->hasPages())
                            <div class="mt-4 d-flex flex-column flex-md-row justify-content-between align-items-center bg-white p-3 border shadow-sm" style="border-radius: 12px; gap: 15px;">
                                <div class="small text-muted font-weight-bold">Menampilkan Tanggal Ke-{{ $paginator->currentPage() }} dari {{ $paginator->total() }} Tanggal Berjadwal</div>
                                <div class="pagination-container">{{ $paginator->links() }}</div>
                            </div>
                        @endif

                    </div>
                </x-card>

            {{-- TAB CONTENT 2: REKAP PERFORMA KERAJINAN --}}
            @else
                <x-card title="Rekap Performa Kerajinan Santri" icon="fas fa-chart-line" outline>
                    <x-slot name="tools">
                        <a href="{{ route('manajemenasetdanasrama.jadwal-piket.index') }}" class="btn btn-sm btn-secondary shadow-sm px-3" style="border-radius: 8px;">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Jadwal
                        </a>
                    </x-slot>

                    {{-- FILTER BAR SIMETRIS --}}
                    <div class="card-body border-bottom bg-light p-4 filter-bar-synced">
                        <form action="{{ route('manajemenasetdanasrama.jadwal-piket.evaluasi') }}" method="GET">
                            <input type="hidden" name="tab" value="rekap">
                            <div class="row align-items-end">
                                {{-- CARI SANTRI --}}
                                <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                                    <label class="small font-weight-bold text-muted uppercase mb-1">Cari Santri</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text text-muted"><i class="fas fa-search"></i></span></div>
                                        <input type="text" class="form-control" name="q" placeholder="Ketik nama..." value="{{ request('q') }}">
                                    </div>
                                </div>

                                {{-- STATUS KERAJINAN --}}
                                <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                                    <label class="small font-weight-bold text-muted uppercase mb-1">Tingkat Kerajinan</label>
                                    <select class="form-control select2" name="kerajinan" data-placeholder="Semua Kerajinan" onchange="this.form.submit()">
                                        <option value="">Semua Tingkat Kerajinan</option>
                                        <option value="high" {{ request('kerajinan') == 'high' ? 'selected' : '' }}>Sangat Rajin (>= 80%)</option>
                                        <option value="medium" {{ request('kerajinan') == 'medium' ? 'selected' : '' }}>Cukup Rajin (50% - 79%)</option>
                                        <option value="low" {{ request('kerajinan') == 'low' ? 'selected' : '' }}>Kurang Rajin (&lt; 50%)</option>
                                        <option value="none" {{ request('kerajinan') == 'none' ? 'selected' : '' }}>Belum Pernah Piket</option>
                                    </select>
                                </div>

                                {{-- TANGGAL MULAI --}}
                                <div class="col-lg-2 col-md-6 mb-3 mb-lg-0">
                                    <label class="small font-weight-bold text-muted uppercase mb-1">Mulai Tanggal</label>
                                    <input type="date" class="form-control" name="tanggal_mulai" value="{{ $tanggalMulai }}">
                                </div>

                                {{-- TANGGAL SELESAI --}}
                                <div class="col-lg-2 col-md-6 mb-3 mb-lg-0">
                                    <label class="small font-weight-bold text-muted uppercase mb-1">Sampai Tanggal</label>
                                    <input type="date" class="form-control" name="tanggal_selesai" value="{{ $tanggalSelesai }}">
                                </div>

                                {{-- TOMBOL AKSI --}}
                                <div class="col-lg-2 col-md-12">
                                    <div class="d-flex" style="gap: 8px;">
                                        <button type="submit" class="btn btn-primary flex-grow-1 font-weight-bold">
                                            Filter
                                        </button>
                                        @if(request()->filled('q') || request()->filled('kerajinan') || request()->filled('tanggal_mulai') || request()->filled('tanggal_selesai'))
                                            <a href="{{ route('manajemenasetdanasrama.jadwal-piket.evaluasi', ['tab' => 'rekap']) }}" class="btn btn-outline-secondary px-3 d-inline-flex align-items-center justify-content-center" title="Reset Filter">
                                                <i class="fas fa-sync-alt"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="card-body p-4 bg-light">
                        <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0 font-weight-bold text-primary"><i class="fas fa-user-shield mr-2"></i> Rekap Performa Kerajinan Santri</h5>
                                <span class="badge badge-pill bg-light-info text-info px-3 py-1 font-weight-bold" style="font-size: 11px;">{{ $complianceData->total() }} Santri Aktif</span>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="bg-gray-light text-muted small uppercase">
                                            <tr>
                                                <th class="pl-4 py-3 border-0" width="60">No</th>
                                                <th class="py-3 border-0">Nama Santri</th>
                                                <th class="py-3 border-0 text-center" width="110">Total Tugas</th>
                                                <th class="py-3 border-0 text-center text-success" width="110">Selesai (Rajin)</th>
                                                <th class="py-3 border-0 text-center text-danger" width="110">Terlewat (Belum)</th>
                                                <th class="py-3 border-0 pr-4" width="220">Kerajinan (%)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($complianceData as $index => $siswa)
                                                @php
                                                    $total = $siswa->total_piket;
                                                    $selesai = $siswa->total_selesai;
                                                    $belum = $siswa->total_belum;
                                                    $pct = $total > 0 ? round(($selesai / $total) * 100, 1) : null;
                                                    
                                                    // Badge & Progress Bar color mapping
                                                    if ($pct === null) {
                                                        $color = 'secondary';
                                                        $badgeText = 'Belum Ada Tugas';
                                                    } elseif ($pct >= 80) {
                                                        $color = 'success';
                                                        $badgeText = 'Sangat Rajin';
                                                    } elseif ($pct >= 50) {
                                                        $color = 'warning';
                                                        $badgeText = 'Cukup Rajin';
                                                    } else {
                                                        $color = 'danger';
                                                        $badgeText = 'Kurang Rajin';
                                                    }
                                                @endphp
                                                <tr>
                                                    <td class="align-middle pl-4 py-3 font-weight-bold text-muted">{{ $complianceData->firstItem() + $index }}</td>
                                                    <td class="align-middle py-3">
                                                        <div class="font-weight-bold text-dark" style="font-size: 14px;">{{ $siswa->nama }}</div>
                                                        <div class="text-muted small">NIS: {{ $siswa->nis }} | {{ $siswa->kamarPenghuni->first()->kamar->nama_kamar ?? 'Belum ada Kamar' }}</div>
                                                    </td>
                                                    <td class="align-middle py-3 text-center font-weight-bold">{{ $total }}</td>
                                                    <td class="align-middle py-3 text-center font-weight-bold text-success">{{ $selesai }}</td>
                                                    <td class="align-middle py-3 text-center font-weight-bold text-danger">{{ $belum }}</td>
                                                    <td class="align-middle py-3 pr-4">
                                                        @if($pct !== null)
                                                            <div class="d-flex align-items-center">
                                                                <div class="flex-grow-1 mr-2">
                                                                    <div class="progress progress-xxs mb-0">
                                                                        <div class="progress-bar bg-{{ $color }}" role="progressbar" style="width: {{ $pct }}%" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                                    </div>
                                                                    <span class="text-muted small font-weight-bold" style="font-size: 10px;">{{ $badgeText }}</span>
                                                                </div>
                                                                <span class="badge badge-pill badge-{{ $color }} px-2 font-weight-bold" style="font-size: 11px; min-width: 55px; text-align: center;">{{ $pct }}%</span>
                                                            </div>
                                                        @else
                                                            <span class="badge badge-pill badge-secondary px-2 font-weight-bold" style="font-size: 11px;">{{ $badgeText }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-5 text-muted">
                                                        <i class="fas fa-user-slash fa-2x mb-3 text-muted" style="opacity: 0.5;"></i>
                                                        <p class="mb-0 font-weight-bold">Tidak ada data santri ditemukan</p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                @if($complianceData->hasPages())
                                    <div class="p-3 border-top d-flex justify-content-between align-items-center bg-white" style="gap: 15px;">
                                        <div class="small text-muted font-weight-bold">Menampilkan {{ $complianceData->firstItem() }}-{{ $complianceData->lastItem() }} dari {{ $complianceData->total() }} santri</div>
                                        <div class="pagination-container">{{ $complianceData->appends(request()->query())->links() }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </x-card>
            @endif

        </div>
    </div>
</div>
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
