@extends('layouts.app')

@section('content')
<div class="container-fluid">
    @php
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $days = [
            'Senin' => 'Senin', 'Selasa' => 'Selasa', 'Rabu' => 'Rabu', 
            'Kamis' => 'Kamis', 'Jumat' => 'Jumat', 'Sabtu' => 'Sabtu', 'Minggu' => 'Ahad'
        ];

        $currentYear = request('year', date('Y'));
        $currentMonth = request('month', date('n'));
        $currentDay = (auth()->user()->hasRole('SISWA') || auth()->user()->hasRole('WALI_MURID')) ? 'all' : request('day', 'all');
        $currentStatus = 'all';
        
        // For Admin/Pengurus
        if (auth()->user()->hasRole('SISWA') || auth()->user()->hasRole('WALI_MURID')) {
            $currentSantri = 'all';
        } else {
            $currentSantri = request('santri_id', 'all');
        }
        
        $searchKeyword = (auth()->user()->hasRole('SISWA') || auth()->user()->hasRole('WALI_MURID')) ? '' : request('search', '');
        
        // Filter logic
        $filteredData = $uangsakus->filter(function($item) use ($currentYear, $currentMonth, $currentDay, $currentSantri, $searchKeyword) {
            $date = \Carbon\Carbon::parse($item->tanggal);
            if ($date->format('Y') != $currentYear) return false;
            if ($currentMonth != 'all' && $date->format('n') != $currentMonth) return false;
            if ($currentDay != 'all' && $date->locale('id')->translatedFormat('l') != $currentDay) return false;
            
            if ($currentSantri != 'all' && $item->siswa_id != $currentSantri) return false;

            if (!empty($searchKeyword)) {
                $keyword = strtolower($searchKeyword);
                $siswaMatch = $item->siswa ? str_contains(strtolower($item->siswa->nama), $keyword) : false;
                $descMatch = $item->deskripsi ? str_contains(strtolower($item->deskripsi), $keyword) : false;
                if (!$siswaMatch && !$descMatch) return false;
            }
            return true;
        });

        // Filter logic for monthly stats (ignores status filter, applies year/month/day/santri/search filters)
        $monthlyData = $uangsakus->filter(function($item) use ($currentYear, $currentMonth, $currentDay, $currentSantri, $searchKeyword) {
            $date = \Carbon\Carbon::parse($item->tanggal);
            if ($date->format('Y') != $currentYear) return false;
            if ($currentMonth != 'all' && $date->format('n') != $currentMonth) return false;
            if ($currentDay != 'all' && $date->locale('id')->translatedFormat('l') != $currentDay) return false;
            if ($currentSantri != 'all' && $item->siswa_id != $currentSantri) return false;

            if (!empty($searchKeyword)) {
                $keyword = strtolower($searchKeyword);
                $siswaMatch = $item->siswa ? str_contains(strtolower($item->siswa->nama), $keyword) : false;
                $descMatch = $item->deskripsi ? str_contains(strtolower($item->deskripsi), $keyword) : false;
                if (!$siswaMatch && !$descMatch) return false;
            }
            return true;
        });

        // Filter logic for overall stats (ignores date/month/year/day/status filters)
        $allTimeData = $uangsakus->filter(function($item) use ($currentSantri, $searchKeyword) {
            if ($currentSantri != 'all' && $item->siswa_id != $currentSantri) return false;
            if (!empty($searchKeyword)) {
                $keyword = strtolower($searchKeyword);
                $siswaMatch = $item->siswa ? str_contains(strtolower($item->siswa->nama), $keyword) : false;
                $descMatch = $item->deskripsi ? str_contains(strtolower($item->deskripsi), $keyword) : false;
                if (!$siswaMatch && !$descMatch) return false;
            }
            return true;
        });

        // New Stats Logic based on Status
        $totalBelumDiberikan = $allTimeData->where('status', '!=', 'Sudah Diterima Santri')->sum('jumlah');
        if (auth()->user()->hasRole('SISWA') || auth()->user()->hasRole('WALI_MURID')) {
            $totalSudahDiberikan = $allTimeData->where('status', 'Sudah Diterima Santri')->sum('jumlah');
        } else {
            $totalSudahDiberikan = $monthlyData->where('status', 'Sudah Diterima Santri')->sum('jumlah');
        }

        // Group by santri for more accurate per-person status
        $groupedBySantri = $monthlyData->groupBy('siswa_id');
        
        $santriBelumDiberikan = $groupedBySantri->filter(function($items) {
            // Santri Belum Menerima: Jika punya minimal satu data yang belum diterima
            return $items->where('status', '!=', 'Sudah Diterima Santri')->count() > 0;
        })->count();

        $santriSudahDiberikan = $groupedBySantri->filter(function($items) {
            // Santri Sudah Menerima: Jika SEMUA datanya sudah diterima
            return $items->where('status', '!=', 'Sudah Diterima Santri')->count() === 0;
        })->count();

        // Kanban board logic
        // === SORTING ===
        // Kanban board logic: Sort by date first, then by the most recent update time
        $sortedData = $filteredData->sort(function($a, $b) {
            // Primary sort: Date (tanggal) descending
            if ($a->tanggal != $b->tanggal) {
                return $a->tanggal > $b->tanggal ? -1 : 1;
            }
            // Secondary sort: Updated at descending
            return $a->updated_at > $b->updated_at ? -1 : 1;
        });
        $groupedData = $sortedData->groupBy(function($item) { 
            return \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d'); 
        });

        $userAgent = request()->userAgent();
        $isMobileOrTablet = preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $userAgent);
        
        $perPage = $isMobileOrTablet ? 4 : 6;
        
        $page = request()->get('page', 1);
        $paginatedGroups = new \Illuminate\Pagination\LengthAwarePaginator(
            $groupedData->slice(($page - 1) * $perPage, $perPage)->all(),
            $groupedData->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $currentPageItemsCount = collect($paginatedGroups->items())->flatten(1)->count();

        $dynamicStatTitle = 'Total Uang Saku Sudah Diterima Santri';
        if (!auth()->user()->hasRole('SISWA') && !auth()->user()->hasRole('WALI_MURID')) {
            if ($currentDay != 'all') {
                $dynamicStatTitle .= ' Hari ' . ($days[$currentDay] ?? $currentDay);
            }
            if ($currentMonth != 'all') {
                $dynamicStatTitle .= ' Bulan ' . ($months[$currentMonth] ?? $currentMonth);
            }
            $dynamicStatTitle .= ' Tahun ' . $currentYear;
        }

        $dynamicBoardTitle = 'Board Uang Saku';
        if ($currentDay != 'all') {
            $dynamicBoardTitle .= ' Hari ' . ($days[$currentDay] ?? $currentDay);
        }
        if ($currentMonth != 'all') {
            $dynamicBoardTitle .= ' Bulan ' . ($months[$currentMonth] ?? $currentMonth);
        } else {
            $dynamicBoardTitle .= ' Semua Bulan';
        }
        $dynamicBoardTitle .= ' Tahun ' . $currentYear;
    @endphp

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            @if(auth()->user()->hasRole('SISWA'))
                Catatan Uang Saku Saya
            @elseif(auth()->user()->hasRole('WALI_MURID'))
                Catatan Uang Saku Anak Saya
            @else
                Manajemen Uang Saku Santri
            @endif
        </h1>
        <div class="text-muted">
            {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, j F Y') }}
        </div>
    </div>

    @if(auth()->user()->hasRole('WALI_MURID') && isset($anakSiswas) && $anakSiswas->count() > 1)
    <div class="row mb-4">
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card shadow-sm border-left-primary">
                <div class="card-body py-2">
                    <form method="GET" action="{{ route('keuangan.uangsakus.index') }}" class="mb-0">
                        <input type="hidden" name="year" value="{{ $currentYear }}">
                        <input type="hidden" name="month" value="{{ $currentMonth }}">
                        <label class="form-label small text-muted font-weight-bold mb-1">Pilih Anak</label>
                        <select name="santri_id" class="form-select custom-select shadow-sm" onchange="this.form.submit()">
                            @foreach($anakSiswas as $anak)
                                <option value="{{ $anak->id }}" {{ (isset($selectedAnakId) && $selectedAnakId == $anak->id) ? 'selected' : '' }}>
                                    {{ $anak->nama }} {{ isset($anak->kelas->tingkat) ? '(' . $anak->kelas->tingkat->nama_tingkat . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Advanced Filters -->
    @if(!auth()->user()->hasRole('SISWA') && !auth()->user()->hasRole('WALI_MURID'))
    <!-- Total Uang Saku Belum Diterima Santri (Khusus Admin/Super Admin, di atas filter) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body" style="font-family: system-ui, -apple-system, sans-serif;">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Uang Saku Belum Diterima Santri
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp{{ number_format($totalBelumDiberikan, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hand-holding-usd fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Filter Riwayat Uang Saku</h6>
                </div>
                <div class="card-body">
                    <form id="filterForm" method="GET" action="{{ route('keuangan.uangsakus.index') }}">
                        <div class="row g-3 align-items-end">
                            <div class="col-6 {{ (auth()->user()->hasRole('SISWA') || auth()->user()->hasRole('WALI_MURID')) ? 'col-md-6 col-lg-6' : 'col-md-4 col-lg-2' }}">
                                <label class="form-label small text-muted font-weight-bold mb-1">Tahun</label>
                                <select name="year" class="form-select custom-select shadow-sm" onchange="this.form.submit()">
                                    @for($i = date('Y'); $i >= date('Y')-5; $i--)
                                        <option value="{{ $i }}" {{ $currentYear == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-6 {{ (auth()->user()->hasRole('SISWA') || auth()->user()->hasRole('WALI_MURID')) ? 'col-md-6 col-lg-6' : 'col-md-4 col-lg-2' }}">
                                <label class="form-label small text-muted font-weight-bold mb-1">Bulan</label>
                                <select name="month" class="form-select custom-select shadow-sm" onchange="this.form.submit()">
                                    <option value="all" {{ $currentMonth == 'all' ? 'selected' : '' }}>Semua Bulan</option>
                                    @foreach($months as $key => $monthName)
                                        <option value="{{ $key }}" {{ $currentMonth == $key ? 'selected' : '' }}>
                                            {{ $monthName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            @if(!auth()->user()->hasRole('SISWA') && !auth()->user()->hasRole('WALI_MURID'))
                            <div class="col-6 col-md-4 col-lg-2">
                                <label class="form-label small text-muted font-weight-bold mb-1">Hari</label>
                                <select name="day" class="form-select custom-select shadow-sm" onchange="this.form.submit()">
                                    <option value="all" {{ $currentDay == 'all' ? 'selected' : '' }}>Semua Hari</option>
                                    @foreach($days as $dayVal => $dayLabel)
                                        <option value="{{ $dayVal }}" {{ $currentDay == $dayVal ? 'selected' : '' }}>
                                            {{ $dayLabel }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            @if(!auth()->user()->hasRole('SISWA') && !auth()->user()->hasRole('WALI_MURID'))
                            <div class="col-12 col-md-12 col-lg-6">
                                <label class="form-label small text-muted font-weight-bold mb-1">Santri</label>
                                <select name="santri_id" class="form-select custom-select shadow-sm select2" onchange="this.form.submit()">
                                    <option value="all" {{ $currentSantri == 'all' ? 'selected' : '' }}>Semua Santri</option>
                                    @foreach($siswas as $siswa)
                                        @php
                                            $tingkat = isset($siswa->kelas->tingkat) ? "(" . $siswa->kelas->tingkat->nama_tingkat . ")" : "";
                                        @endphp
                                        <option value="{{ $siswa->id }}" {{ $currentSantri == $siswa->id ? 'selected' : '' }}>
                                            {{ $siswa->nama }} {{ $tingkat }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-12 col-lg-12 mt-3">
                                <label class="form-label small text-muted font-weight-bold mb-1">Cari Transaksi</label>
                                <div class="input-group shadow-sm">
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Cari santri atau keterangan..." value="{{ $searchKeyword }}">
                                    <button type="submit" class="btn btn-primary px-3">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    @if($searchKeyword || $currentMonth != 'all' || $currentDay != 'all' || (!auth()->user()->hasRole('SISWA') && $currentSantri != 'all'))
                                    <a href="{{ route('keuangan.uangsakus.index') }}" class="btn btn-outline-danger px-3" title="Reset Filter">
                                        <i class="fas fa-times"></i>
                                    </a>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row">
        <!-- Total Uang Saku Belum Diberikan (Siswa / Wali) -->
        @if(auth()->user()->hasRole('SISWA') || auth()->user()->hasRole('WALI_MURID'))
        <div class="col-xl-6 col-lg-6 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body" style="font-family: system-ui, -apple-system, sans-serif;">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Uang Saku Belum Diterima Santri
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp{{ number_format($totalBelumDiberikan, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hand-holding-usd fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Total Uang Saku Sudah Diberikan -->
        <div class="{{ (auth()->user()->hasRole('SISWA') || auth()->user()->hasRole('WALI_MURID')) ? 'col-xl-6 col-lg-6 col-md-6' : 'col-12' }} mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body" style="font-family: system-ui, -apple-system, sans-serif;">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                @if(auth()->user()->hasRole('SISWA') || auth()->user()->hasRole('WALI_MURID'))
                                    Total Uang Saku Sudah Diterima Santri
                                @else
                                    {{ $dynamicStatTitle }}
                                @endif
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp{{ number_format($totalSudahDiberikan, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-double fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Santri Belum Diberikan -->
        @if(!auth()->user()->hasRole('SISWA') && !auth()->user()->hasRole('WALI_MURID'))
        <div class="col-12 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body" style="font-family: system-ui, -apple-system, sans-serif;">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Santri Belum Menerima Uang Saku
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $santriBelumDiberikan }} Santri
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-clock fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Santri Sudah Diberikan -->
        <div class="col-12 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body" style="font-family: system-ui, -apple-system, sans-serif;">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Santri Sudah Menerima Uang Saku
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $santriSudahDiberikan }} Santri
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="kanban-filter-buttons">
                                <p class="filter-btn active">
                                    <i class="fas fa-list-ul me-2"></i>Semua Riwayat
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            @if(!auth()->user()->hasRole('SISWA') && !auth()->user()->hasRole('WALI_MURID'))
                                <div class="add-transaction-buttons">
                                    <a href="{{ route('keuangan.uangsakus.create') }}" class="btn btn-primary btn-add">
                                        <i class="fas fa-plus mr-2 me-2"></i>
                                        <span class="btn-text">&nbsp;Tambah Uang Saku</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kanban Board -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white border-bottom-0">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-6 mb-2 mb-md-0">
                            <h6 class="m-0 font-weight-bold text-primary">
                                {{ $dynamicBoardTitle }}
                            </h6>
                            @if(auth()->user()->hasRole('SISWA') || auth()->user()->hasRole('WALI_MURID'))
                                <div class="mt-2">
                                    <form method="GET" action="{{ route('keuangan.uangsakus.index') }}" class="d-flex align-items-center flex-wrap">
                                        @if(auth()->user()->hasRole('WALI_MURID') && isset($selectedAnakId))
                                            <input type="hidden" name="santri_id" value="{{ $selectedAnakId }}">
                                        @endif
                                        <div class="mr-2 mb-2" style="min-width: 130px;">
                                            <select name="month" class="form-select custom-select shadow-sm" onchange="this.form.submit()" style="border-radius: 8px; font-size: 0.85rem; height: calc(1.5em + 0.5rem + 2px); padding: 0.25rem 0.5rem;">
                                                <option value="all" {{ $currentMonth == 'all' ? 'selected' : '' }}>Semua Bulan</option>
                                                @foreach($months as $key => $monthName)
                                                    <option value="{{ $key }}" {{ $currentMonth == $key ? 'selected' : '' }}>
                                                        {{ $monthName }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mr-2 mb-2" style="min-width: 90px;">
                                            <select name="year" class="form-select custom-select shadow-sm" onchange="this.form.submit()" style="border-radius: 8px; font-size: 0.85rem; height: calc(1.5em + 0.5rem + 2px); padding: 0.25rem 0.5rem;">
                                                @for($i = date('Y'); $i >= date('Y')-5; $i--)
                                                    <option value="{{ $i }}" {{ $currentYear == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        </div>
                        <div class="col-12 col-md-6 text-center text-md-end">
                            <div class="text-muted small">
                                <span>{{ $currentPageItemsCount }}</span> uang saku di halaman {{ $paginatedGroups->currentPage() }}
                                @if($searchKeyword)
                                    untuk "{{ $searchKeyword }}"
                                @endif
                                @if($currentMonth != 'all')
                                    - {{ $months[$currentMonth] }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    @if($groupedData->count() > 0)
                    <div class="kanban-container mt-2">
                        @foreach($paginatedGroups as $date => $dailyItems)
                            <div class="kanban-column">
                                <div class="kanban-column-header">
                                    <div class="column-title">
                                        <h6 class="column-day mb-0">{{ \Carbon\Carbon::parse($date)->locale('id')->translatedFormat('l') }}</h6>
                                        <small class="column-date text-muted">{{ \Carbon\Carbon::parse($date)->locale('id')->translatedFormat('j M Y') }}</small>
                                    </div>
                                    <div class="column-stats mt-2">
                                         <div class="d-flex justify-content-between w-100 px-1">
                                            <div class="text-xs">
                                                <span class="text-muted">Total:</span>
                                                <span class="font-weight-bold text-primary">Rp{{ number_format($dailyItems->sum('jumlah'), 0, ',', '.') }}</span>
                                            </div>
                                            <div class="text-xs">
                                                <span class="font-weight-bold text-muted">{{ $dailyItems->count() }}</span>
                                                <span class="text-muted">Data</span>
                                            </div>
                                         </div>
                                    </div>
                                </div>

                                <div class="kanban-column-body">
                                    @foreach($dailyItems as $item)
                                        <div class="income-card-modern border-primary">
                                            <div class="income-card-header">
                                                <div class="card-source-badge bg-primary">
                                                    <span>{{ $item->siswa->nama ?? 'Unknown' }}</span>
                                                </div>
                                                <div class="card-amount text-primary">
                                                    Rp{{ number_format($item->jumlah, 0, ',', '.') }}
                                                </div>
                                            </div>

                                            <div class="income-card-body">
                                                <div class="card-description-wrapper">
                                                    <p class="card-description">{{ $item->deskripsi ?? 'Tanpa keterangan' }}</p>
                                                </div>
                                                <div class="mt-2">
                                                    @if(!auth()->user()->hasRole('SISWA') && !auth()->user()->hasRole('WALI_MURID'))
                                                        <div class="btn-group btn-group-sm w-100" role="group" style="border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                                            <form action="{{ route('keuangan.uangsakus.updateStatus', $item->id) }}" method="POST" class="flex-fill m-0">
                                                                @csrf @method('PATCH')
                                                                <input type="hidden" name="status" value="Belum Diterima Santri">
                                                                <button type="submit" class="btn {{ $item->status == 'Belum Diterima Santri' ? 'btn-primary' : 'btn-light' }} w-100 rounded-0 py-2 border-end" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                                                    <i class="fas {{ $item->status == 'Belum Diterima Santri' ? 'fa-check-circle' : 'fa-circle' }} me-1"></i> Belum Diterima
                                                                </button>
                                                            </form>
                                                            <form action="{{ route('keuangan.uangsakus.updateStatus', $item->id) }}" method="POST" class="flex-fill m-0">
                                                                @csrf @method('PATCH')
                                                                <input type="hidden" name="status" value="Sudah Diterima Santri">
                                                                <button type="submit" class="btn {{ $item->status == 'Sudah Diterima Santri' ? 'btn-primary' : 'btn-light' }} w-100 rounded-0 py-2" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                                                    <i class="fas {{ $item->status == 'Sudah Diterima Santri' ? 'fa-check-circle' : 'fa-circle' }} me-1"></i> Sudah Diterima
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @else
                                                        <div class="text-center">
                                                            <span class="badge badge-pill {{ $item->status == 'Sudah Diterima Santri' ? 'badge-success' : 'badge-warning' }} w-100 py-2" style="font-size: 0.75rem;">
                                                                {{ $item->status }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="income-card-footer d-flex flex-column align-items-stretch">
                                                <div class="card-actions justify-content-end mb-2">
                                                    <a href="{{ route('keuangan.uangsakus.show', $item->id) }}" class="btn-action bg-primary" title="Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if(!auth()->user()->hasRole('SISWA') && !auth()->user()->hasRole('WALI_MURID'))
                                                        @if(auth()->check() && auth()->user()->isSuperAdmin())
                                                            <a href="{{ route('keuangan.uangsakus.edit', $item->id) }}" class="btn-action btn-edit" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <form action="{{ route('keuangan.uangsakus.destroy', $item->id) }}" method="POST" class="d-inline delete-form">
                                                                @csrf @method('DELETE')
                                                                <button type="button" class="btn-action btn-delete delete-btn" data-source="{{ $item->siswa->nama }}">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        @else
                                                            <button type="button" class="btn-action btn-edit" title="Edit" onclick="Swal.fire('Akses Ditolak', 'Hanya super admin yang memiliki wewenang untuk mengubah atau menghapus data uang saku.', 'error'); return false;">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button type="button" class="btn-action btn-delete" title="Hapus" onclick="Swal.fire('Akses Ditolak', 'Hanya super admin yang memiliki wewenang untuk mengubah atau menghapus data uang saku.', 'error'); return false;">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        @endif
                                                    @endif
                                                </div>
                                                <div class="card-time text-start">
                                                    <i class="fas fa-clock text-muted me-1" style="font-size: 0.8rem;"></i>
                                                    <small class="text-muted" style="font-size: 0.75rem;">{{ $item->updated_at->setTimezone('Asia/Jakarta')->format('H.i') }} WIB, {{ $item->updated_at->locale('id')->translatedFormat('d F Y') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @if($paginatedGroups->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $paginatedGroups->links('pagination::bootstrap-4') }}
                    </div>
                    @endif
                    
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">Tidak ada riwayat transaksi pada periode ini</h5>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />
<style>
    :root {
        --primary: #4e73df;
        --success: #28a745;
        --info: #36b9cc;
        --warning: #f6c23e;
        --danger: #e74a3b;
        --text: #5a5c69;
        --text-muted: #858796;
        --card-bg: #fff;
        --shadow-soft: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        --glass-blur: 10px;
    }

    /* Statistics Cards - SAMA DENGAN REFERENSI */
    .select2-container--bootstrap4 .select2-selection {
        background-color: #f8f9fc !important;
        border: none !important;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
        border-radius: 10px !important;
        height: calc(1.5em + 0.75rem + 2px) !important;
    }
    .card {
        background: var(--card-bg);
        backdrop-filter: var(--glass-blur);
        -webkit-backdrop-filter: var(--glass-blur);
        border-radius: 1rem;
        border: 1px solid rgba(255,255,255,0.12);
        box-shadow: var(--shadow-soft);
        transition: 0.35s ease;
        padding: 0.5rem !important;
    }

    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 26px rgba(0,0,0,0.12);
    }

    .border-left-success { border-left: 0.35rem solid var(--success) !important; }
    .border-left-info { border-left: 0.35rem solid var(--info) !important; }
    .border-left-primary { border-left: 0.35rem solid var(--primary) !important; }
    .border-left-warning { border-left: 0.35rem solid var(--warning) !important; }
    .border-left-danger { border-left: 0.35rem solid var(--danger) !important; }

    .text-gray-800 { color: var(--text) !important; }

    .text-xs {
        font-size: 0.8rem !important;
        font-family: 'Nunito', 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
    }

    .column-stats .text-xs {
        font-size: 0.75rem !important;
    }
    
    .h5 {
        font-size: 1.25rem !important;
        font-family: 'Nunito', 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
        font-weight: 700 !important;
    }
    
    .font-weight-bold { font-weight: 700 !important; }
    .text-uppercase { text-transform: uppercase; letter-spacing: 0.5px; }

    .text-success { color: var(--success) !important; }
    .text-info { color: var(--info) !important; }
    .text-primary { color: var(--primary) !important; }
    .text-warning { color: var(--warning) !important; }
    .text-danger { color: var(--danger) !important; }

    /* RESPONSIVE FONT SIZES - EXACTLY SAME AS PEMASUKAN */
    @media (max-width: 576px) {
        .col-xl-6, .col-lg-6, .col-md-6 {
            flex: 0 0 100%;
            max-width: 100%;
            margin-bottom: 1rem;
        }
        .card-body { padding: 1rem !important; }
        .card-body .row.no-gutters.align-items-center { display: flex; flex-direction: row; align-items: center; margin: 0 -5px; }
        .card-body .col.mr-2 { flex: 1; margin-right: 0.75rem; padding: 0 5px; }
        .card-body .col-auto { flex-shrink: 0; padding: 0 5px; }
        .text-xs { font-size: 0.7rem !important; }
        .h5 { font-size: 1.1rem !important; }
        .fa-2x { font-size: 1.5rem !important; }
    }

    @media (min-width: 577px) and (max-width: 768px) {
        .col-xl-6, .col-lg-6, .col-md-6 { flex: 0 0 50%; max-width: 50%; }
        .card-body { padding: 1rem !important; }
        .text-xs { font-size: 0.72rem !important; }
        .h5 { font-size: 1.15rem !important; }
        .fa-2x { font-size: 1.6rem !important; }
    }

    @media (min-width: 769px) and (max-width: 992px) {
        .col-xl-6, .col-lg-6, .col-md-6 { flex: 0 0 50%; max-width: 50%; }
        .card-body { padding: 1.25rem !important; }
        .text-xs { font-size: 0.75rem !important; }
        .h5 { font-size: 1.2rem !important; }
        .fa-2x { font-size: 1.7rem !important; }
    }

    @media (min-width: 993px) and (max-width: 1200px) {
        .col-xl-6, .col-lg-6 { flex: 0 0 50%; max-width: 50%; }
        .card-body { padding: 1.25rem !important; }
        .text-xs { font-size: 0.75rem !important; }
        .h5 { font-size: 1.25rem !important; }
        .fa-2x { font-size: 1.8rem !important; }
    }

    @media (min-width: 1201px) {
        .col-xl-6 { flex: 0 0 50%; max-width: 50%; }
        .card-body { padding: 1.5rem !important; }
        .text-xs { font-size: 0.8rem !important; }
        .h5 { font-size: 1.35rem !important; }
        .fa-2x { font-size: 2rem !important; }
    }

    /* Kanban & Modern Card Style */
    .kanban-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .kanban-column {
        background: #f8f9fc;
        border-radius: 12px;
        border: 1px solid rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        max-height: 700px;
        padding: 15px;
    }

    .kanban-column-header {
        padding: 0 5px 15px 5px;
        border-bottom: 2px solid #e3e6f0;
        margin-bottom: 15px;
    }

    .column-day { margin: 0; color: var(--text); font-weight: 700; font-size: 1rem; }
    .column-date { color: var(--text-muted); font-size: 0.85rem; }

    .kanban-column-body {
        flex: 1;
        overflow-y: auto;
        padding: 5px;
        min-height: 100px;
    }

    .kanban-column-body::-webkit-scrollbar {
        width: 8px;
    }

    .kanban-column-body::-webkit-scrollbar-track {
        background: rgba(0,0,0,0.05);
        border-radius: 4px;
    }

    .kanban-column-body::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 4px;
        transition: background 0.3s ease;
    }

    .kanban-column-body::-webkit-scrollbar-thumb:hover {
        background: #aaa;
    }

    .kanban-column-body::-webkit-scrollbar-thumb:active {
        background: #888;
    }

    .income-card-modern {
        background: white;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 15px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        border-left: 5px solid #ddd;
        transition: all 0.3s ease;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    }

    .income-card-modern:hover { transform: translateY(-3px); box-shadow: 0 8px 15px rgba(0,0,0,0.1); }

    .income-card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
    .card-source-badge { padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; color: white; font-weight: 700; }

    .card-amount { font-weight: 700; font-size: 0.95rem; }

    .card-description-wrapper { background: rgba(78, 115, 223, 0.05); color: #2e59d9; padding: 10px; border-radius: 8px; font-size: 0.85rem; margin-top: 10px; }

    .income-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 12px;
        border-top: 1px solid #eee;
        margin-top: 12px;
    }

    .card-actions { display: flex; gap: 6px; }
    .btn-action {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        transition: all 0.2s ease;
        border: none;
    }
    .btn-edit { background: var(--info); }
    .btn-delete { background: var(--danger); }
    .btn-action:hover { opacity: 0.9; transform: scale(1.1); color: white; text-decoration: none; }

    .kanban-filter-buttons {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        align-items: center;
    }

    .filter-btn {
        background-color: #ecf0f1;
        border: none;
        padding: 10px 16px;
        border-radius: 8px;
        transition: all 0.3s ease;
        font-weight: 600;
        color: #2c3e50;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
        font-size: 0.9rem;
    }

    .filter-btn.active {
        background-color: var(--primary);
        color: white;
        box-shadow: 0 4px 8px rgba(78, 115, 223, 0.3);
    }

    .add-transaction-buttons {
        display: flex;
        gap: 10px;
        align-items: center;
        justify-content: flex-end;
        flex-wrap: wrap;
    }

    .btn-add {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        min-width: fit-content;
        max-width: 220px;
    }
    
    .btn-add:hover {
        background-color: #2e59d9;
        border-color: #2653d4;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        color: white;
    }

    @media (max-width: 768px) {
        .kanban-container {
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .kanban-filter-buttons {
            justify-content: center;
            margin-bottom: 1rem;
        }

        .col-md-6.text-end {
            text-align: center !important;
            margin-top: 1rem;
        }

        .add-transaction-buttons {
            justify-content: center;
        }
        
        .btn-add {
            width: 100%;
            max-width: 200px;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof jQuery !== 'undefined') {
            $('.select2').select2({
                theme: 'bootstrap4',
                placeholder: "Semua Santri",
                allowClear: true
            });
        }
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const form = this.closest('.delete-form');
                const source = this.getAttribute('data-source');
                Swal.fire({
                    title: 'Hapus Transaksi?',
                    text: "Menghapus transaksi dari " + source + " akan mempengaruhi saldo santri!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e74a3b',
                    cancelButtonColor: '#858796',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });
    });
</script>
@endpush
