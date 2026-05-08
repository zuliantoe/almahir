@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pengeluaran</h1>
        <div class="text-muted">
            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        @php
            $months = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
                7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];

            $days = [];
            for ($i = 1; $i <= 31; $i++) {
                $days[$i] = $i;
            }

            $currentYear = request('year', date('Y'));
            $currentMonth = request('month', 'all');
            $currentDay = request('day', 'all');
            $searchKeyword = request('search', '');
            
            // Ambil semua tahun dari seluruh data (sebelum difilter) untuk dropdown
            $allYears = $pengeluarans->pluck('tanggal')->map(function($date) {
                return date('Y', strtotime($date));
            })->unique()->sort()->values();
            if($allYears->isEmpty()) {
                $allYears = collect([date('Y')]);
            }
            
            // === LOGIKA FILTERING (Tumpang tindih dihindari dengan mengecek kondisi sekaligus) ===
            $filteredIncomes = $pengeluarans->filter(function($item) use ($currentYear, $currentMonth, $currentDay, $searchKeyword) {
                $date = \Carbon\Carbon::parse($item->tanggal);
                
                // 1. Filter Tahun (selalu aktif)
                if ($date->format('Y') != $currentYear) return false;
                
                // 2. Filter Bulan
                if ($currentMonth != 'all' && $date->format('n') != $currentMonth) return false;
                
                // 3. Filter Hari
                if ($currentDay != 'all' && $date->format('j') != $currentDay) return false;
                
                // 4. Filter Pencarian (Tujuan atau Deskripsi)
                if (!empty($searchKeyword)) {
                    $keyword = strtolower($searchKeyword);
                    $tujuanMatch = $item->tujuan ? str_contains(strtolower($item->tujuan->nama), $keyword) : false;
                    $descMatch = $item->deskripsi ? str_contains(strtolower($item->deskripsi), $keyword) : false;
                    if (!$tujuanMatch && !$descMatch) return false;
                }
                
                // Jika lolos semua filter, maka data dipertahankan
                return true;
            });

            // === KALKULASI KARTU STATISTIK (Berdasarkan data yang TERFILTER) ===
            $totalIncome = $filteredIncomes->sum('jumlah');
            $totalTransactions = $filteredIncomes->count();
            $avgTransaction = $totalTransactions > 0 ? $totalIncome / $totalTransactions : 0;
            
            $mostFrequentSource = $filteredIncomes->groupBy('tujuan_id')->sortByDesc(function($group) {
                return $group->count();
            })->first();
            $sourceName = $mostFrequentSource ? $mostFrequentSource->first()->tujuan->nama : '-';

            // === SORTING (Default: Terbaru) ===
            $sortedIncomes = $filteredIncomes->sortByDesc('tanggal');

            // === GROUPING UNTUK BOARD KANBAN ===
            $groupedIncomes = $sortedIncomes->groupBy(function($item) { 
                return \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d'); 
            });

            // === PAGINATION KANBAN BOARD ===
            $userAgent = request()->userAgent();
            $isMobileOrTablet = preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $userAgent);
            
            $perPage = $isMobileOrTablet ? 4 : 6;
            
            $page = request()->get('page', 1);
            $paginatedGroups = new \Illuminate\Pagination\LengthAwarePaginator(
                $groupedIncomes->slice(($page - 1) * $perPage, $perPage)->all(),
                $groupedIncomes->count(),
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );

            $currentPageItems = $sortedIncomes->count();
            $totalDays = $groupedIncomes->count();
        @endphp

        <!-- Total Pengeluaran -->
        <div class="col-xl-6 col-lg-6 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body" style="font-family: system-ui, -apple-system, sans-serif;">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Total Pengeluaran
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp{{ number_format($totalIncome, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jumlah Transaksi -->
        <div class="col-xl-6 col-lg-6 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body" style="font-family: system-ui, -apple-system, sans-serif;">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Jumlah Transaksi
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $totalTransactions }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-receipt fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rata-rata per Transaksi -->
        <div class="col-xl-6 col-lg-6 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body" style="font-family: system-ui, -apple-system, sans-serif;">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Rata-rata per Transaksi
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp{{ number_format($avgTransaction, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tujuan Terbanyak -->
        <div class="col-xl-6 col-lg-6 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body" style="font-family: system-ui, -apple-system, sans-serif;">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Tujuan Terbanyak
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ Str::limit($sourceName, 12) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Filter Pengeluaran</h6>
                </div>
                <div class="card-body">
                    <form id="filterForm" method="GET" action="{{ route('keuangan.pengeluarans.index') }}">
                        <div class="row g-3 align-items-end">
                            <div class="col-6 col-md-3 col-lg-2">
                                <label class="form-label small text-muted font-weight-bold mb-1">Tahun</label>
                                <select name="year" class="form-select custom-select shadow-sm" onchange="this.form.submit()">
                                    @foreach($allYears as $yearItem)
                                        <option value="{{ $yearItem }}" {{ $currentYear == $yearItem ? 'selected' : '' }}>
                                            {{ $yearItem }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-6 col-md-3 col-lg-2">
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

                            @if($currentMonth != 'all')
                            <div class="col-12 col-md-3 col-lg-2">
                                <label class="form-label small text-muted font-weight-bold mb-1">Hari</label>
                                <select name="day" class="form-select custom-select shadow-sm" onchange="this.form.submit()">
                                    <option value="all" {{ $currentDay == 'all' ? 'selected' : '' }}>Semua Hari</option>
                                    @foreach($days as $dayNum)
                                        <option value="{{ $dayNum }}" {{ $currentDay == $dayNum ? 'selected' : '' }}>
                                            {{ $dayNum }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-12 col-lg-6">
                            @else
                            <input type="hidden" name="day" value="all">
                            <div class="col-12 col-md-6 col-lg-8">
                            @endif
                                <label class="form-label small text-muted font-weight-bold mb-1">Cari Pengeluaran</label>
                                <div class="input-group shadow-sm">
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Cari berdasarkan tujuan atau deskripsi..." value="{{ $searchKeyword }}">
                                    <button type="submit" class="btn btn-primary px-3">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    @if($searchKeyword || $currentMonth != 'all' || $currentDay != 'all')
                                    <a href="{{ route('keuangan.pengeluarans.index') }}" class="btn btn-outline-danger px-3" title="Reset Filter">
                                        <i class="fas fa-times"></i>
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="kanban-filter-buttons">
                                <p class="filter-btn active">
                                    <i class="fas fa-money-bill-wave me-2"></i>Semua Pengeluaran
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="add-transaction-buttons">
                                <a href="{{ route('keuangan.pengeluarans.create') }}" class="btn btn-danger btn-add">
                                    <i class="fas fa-plus mr-2 me-2"></i>
                                    <span class="btn-text">&nbsp;Tambah Pengeluaran</span>
                                </a>
                            </div>
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
                <div class="card-header py-3">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-4 mb-2 mb-md-0">
                            <h6 class="m-0 font-weight-bold text-primary">Board Pengeluaran</h6>
                        </div>
                        <div class="col-12 col-md-8 text-md-end">
                            <div class="text-muted small">
                                <span id="incomeCount">{{ $currentPageItems }}</span> pengeluaran di {{ $groupedIncomes->count() }} hari
                                @if($searchKeyword)
                                    untuk "{{ $searchKeyword }}"
                                @endif
                                @if($currentMonth != 'all')
                                    - {{ $months[$currentMonth] }}
                                @endif
                                @if($currentDay != 'all')
                                    - Hari {{ $currentDay }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($groupedIncomes->count() > 0)
                    <div class="kanban-container">
                        @foreach($paginatedGroups as $date => $dailyIncomes)
                            @php
                                $dayName = \Carbon\Carbon::parse($date)->translatedFormat('l');
                                $formattedDate = \Carbon\Carbon::parse($date)->format('d M Y');
                                $dailyTotal = $dailyIncomes->sum('jumlah');
                            @endphp
                            
                            <div class="kanban-column">
                                <div class="kanban-column-header">
                                    <div class="column-title">
                                        <h6 class="column-day">{{ $dayName }}</h6>
                                        <small class="column-date text-muted">{{ $formattedDate }}</small>
                                    </div>
                                    <div class="column-stats">
                                        <div class="stat-income">
                                            <i class="fas fa-money-bill-wave text-danger me-1"></i>
                                            <span class="stat-amount">Rp{{ number_format($dailyTotal, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="kanban-column-body">
                                    @foreach($dailyIncomes as $income)
                                        @php
                                            $sourceName = $income->tujuan->nama ?? '-';
                                            $incomeId = $income->id;
                                            $incomeAmount = $income->jumlah;
                                            $incomeTime = optional($income->created_at)->setTimezone('Asia/Jakarta')->format('H.i') ?? '-';
                                            $incomeDescription = $income->deskripsi;
                                        @endphp
                                        
                                        <div class="income-card-modern" 
                                             data-source="{{ strtolower($sourceName) }}"
                                             data-description="{{ strtolower($incomeDescription ?? '') }}">
                                            <div class="income-card-header">
                                                <div class="card-source-badge">
                                                    <span class="badge-text">{{ $sourceName }}</span>
                                                </div>
                                                <div class="card-amount text-danger">
                                                    <span class="amount-text">
                                                        + Rp{{ number_format($incomeAmount, 0, ',', '.') }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="income-card-body">
                                                <div class="card-description-wrapper">
                                                    @if($incomeDescription)
                                                    <p class="card-description">{{ $incomeDescription }}</p>
                                                    @else
                                                    <p class="card-description text-muted">Tidak ada deskripsi</p>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="income-card-footer">
                                                <div class="card-time">
                                                    <i class="fas fa-clock text-muted me-1"></i>
                                                    <small class="time-text">
                                                        @if($incomeTime != '-' && $incomeTime != '00.00')
                                                            {{ $incomeTime }} WIB
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </small>
                                                </div>
                                                <div class="card-actions">
                                                    <a href="{{ route('keuangan.pengeluarans.show', $incomeId) }}" 
                                                       class="btn-action btn-view" 
                                                       title="Detail Pengeluaran">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('keuangan.pengeluarans.edit', $incomeId) }}" 
                                                       class="btn-action btn-edit" 
                                                       title="Edit Pengeluaran">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('keuangan.pengeluarans.destroy', $incomeId) }}" 
                                                          method="POST" 
                                                          class="d-inline delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" 
                                                                class="btn-action btn-delete delete-btn" 
                                                                title="Hapus Pengeluaran"
                                                                data-source="{{ $sourceName }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="kanban-column-footer">
                                    <div class="column-total text-danger">
                                        <span class="total-text">Total: +Rp{{ number_format($dailyTotal, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="column-count">
                                        <span class="count-text">{{ $dailyIncomes->count() }} pengeluaran</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @if($paginatedGroups->hasPages())
                    <div class="d-flex justify-content-center mt-4 mb-2">
                        {{ $paginatedGroups->links('pagination::bootstrap-4') }}
                    </div>
                    @endif
                    
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-money-bill-wave fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">Tidak ada pengeluaran</h5>
                        <p class="text-muted">Tidak ditemukan pengeluaran yang sesuai dengan filter yang dipilih.</p>
                        <a href="{{ route('keuangan.pengeluarans.index') }}" class="btn btn-primary">
                            <i class="fas fa-refresh me-2"></i>Reset Filter
                        </a>
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
<style>
    :root {
        --primary: #4e73df;
        --success: #e74a3b;
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

    .border-left-danger {
        border-left: 0.35rem solid var(--danger) !important;
    }
    .border-left-info {
        border-left: 0.35rem solid var(--info) !important;
    }
    .border-left-primary {
        border-left: 0.35rem solid var(--primary) !important;
    }
    .border-left-warning {
        border-left: 0.35rem solid var(--warning) !important;
    }

    .text-gray-800 {
        color: var(--text) !important;
    }

    /* Font & Text Styles - SAMA DENGAN REFERENSI */
    .text-xs {
        font-size: 0.7rem !important;
        font-family: 'Nunito', 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
    }
    
    .h5 {
        font-size: 1.25rem !important;
        font-family: 'Nunito', 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
        font-weight: 700 !important;
    }
    
    .font-weight-bold {
        font-weight: 700 !important;
    }
    
    .text-uppercase {
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Icon Colors - SAMA DENGAN WARNA BORDER KIRI CARD */
    .text-danger {
        color: var(--danger) !important;
    }
    
    .text-info {
        color: var(--info) !important;
    }
    
    .text-primary {
        color: var(--primary) !important;
    }
    
    .text-warning {
        color: var(--warning) !important;
    }

    /* RESPONSIVE FONT SIZES - SAMA DENGAN REFERENSI */
    @media (max-width: 576px) {
        .col-xl-6, .col-lg-6, .col-md-6 {
            flex: 0 0 100%;
            max-width: 100%;
            margin-bottom: 1rem;
        }
        
        .card-body {
            padding: 1rem !important;
        }
        
        .card-body .row.no-gutters.align-items-center {
            display: flex;
            flex-direction: row;
            align-items: center;
            margin: 0 -5px;
        }
        
        .card-body .col.mr-2 {
            flex: 1;
            margin-right: 0.75rem;
            padding: 0 5px;
        }
        
        .card-body .col-auto {
            flex-shrink: 0;
            padding: 0 5px;
        }
        
        .text-xs {
            font-size: 0.7rem !important;
        }
        
        .h5 {
            font-size: 1.1rem !important;
        }
        
        .fa-2x {
            font-size: 1.5rem !important;
        }
    }

    @media (min-width: 577px) and (max-width: 768px) {
        .col-xl-6, .col-lg-6 {
            flex: 0 0 50%;
            max-width: 50%;
        }
        
        .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
        }
        
        .card-body {
            padding: 1rem !important;
        }
        
        .text-xs {
            font-size: 0.72rem !important;
        }
        
        .h5 {
            font-size: 1.15rem !important;
        }
        
        .fa-2x {
            font-size: 1.6rem !important;
        }
    }

    @media (min-width: 769px) and (max-width: 992px) {
        .col-xl-6, .col-lg-6 {
            flex: 0 0 50%;
            max-width: 50%;
        }
        
        .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
        }
        
        .card-body {
            padding: 1.25rem !important;
        }
        
        .text-xs {
            font-size: 0.75rem !important;
        }
        
        .h5 {
            font-size: 1.2rem !important;
        }
        
        .fa-2x {
            font-size: 1.7rem !important;
        }
    }

    @media (min-width: 993px) and (max-width: 1200px) {
        .col-xl-6 {
            flex: 0 0 50%;
            max-width: 50%;
        }
        
        .col-lg-6 {
            flex: 0 0 50%;
            max-width: 50%;
        }
        
        .card-body {
            padding: 1.25rem !important;
        }
        
        .text-xs {
            font-size: 0.75rem !important;
        }
        
        .h5 {
            font-size: 1.25rem !important;
        }
        
        .fa-2x {
            font-size: 1.8rem !important;
        }
    }

    @media (min-width: 1201px) {
        .col-xl-6 {
            flex: 0 0 50%;
            max-width: 50%;
        }
        
        .card-body {
            padding: 1.5rem !important;
        }
        
        .text-xs {
            font-size: 0.8rem !important;
        }
        
        .h5 {
            font-size: 1.35rem !important;
        }
        
        .fa-2x {
            font-size: 2rem !important;
        }
    }

    @media (max-width: 400px) {
        .col-xl-6, .col-lg-6, .col-md-6 {
            flex: 0 0 100%;
            max-width: 100%;
        }
        
        .card-body {
            padding: 0.875rem !important;
        }
        
        .text-xs {
            font-size: 0.65rem !important;
        }
        
        .h5 {
            font-size: 1rem !important;
        }
        
        .fa-2x {
            font-size: 1.3rem !important;
        }
    }

    /* Kanban Container */
    .kanban-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .kanban-column {
        background: var(--card-bg);
        border-radius: 12px;
        border: 1px solid rgba(255,255,255,0.1);
        backdrop-filter: var(--glass-blur);
        box-shadow: var(--shadow-soft);
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        max-height: 600px;
    }

    .kanban-column:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    .kanban-column-header {
        padding: 12px 15px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        background: rgba(0,0,0,0.02);
        flex-shrink: 0;
    }

    .column-title h6 {
        margin: 0;
        color: var(--text);
        font-weight: 600;
        font-size: 0.95rem;
    }

    .column-title small {
        color: var(--text-muted);
        font-size: 0.8rem;
    }

    .column-stats {
        display: flex;
        justify-content: space-between;
        margin-top: 8px;
        font-size: 0.75rem;
        flex-wrap: wrap;
        gap: 5px;
    }

    .stat-income {
        display: flex;
        align-items: center;
        gap: 4px;
        flex: 1;
        min-width: 0;
    }

    .stat-amount {
        font-size: 0.7rem;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .kanban-column-body {
        flex: 1;
        padding: 10px;
        overflow-y: auto;
        max-height: 450px;
        min-height: 100px;
    }

    .kanban-column-body::-webkit-scrollbar {
        width: 4px;
    }

    .kanban-column-body::-webkit-scrollbar-track {
        background: rgba(0,0,0,0.1);
        border-radius: 2px;
    }

    .kanban-column-body::-webkit-scrollbar-thumb {
        background: var(--primary);
        border-radius: 2px;
    }

    .income-card-modern {
        background: white;
        border-radius: 12px;
        padding: 12px 15px;
        margin-bottom: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border-left: 5px solid #e74a3b;
        transition: all 0.3s ease;
        cursor: pointer;
        min-height: 140px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        word-wrap: break-word;
        overflow-wrap: break-word;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    }

    .income-card-modern:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(231, 74, 59, 0.2);
        border-left-color: #c0392b;
    }

    .income-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 10px;
        gap: 8px;
        flex-wrap: wrap;
        flex-shrink: 0;
    }

    .card-source-badge {
        padding: 6px 10px;
        border-radius: 8px;
        font-size: 0.7rem;
        font-weight: 600;
        white-space: nowrap;
        flex-shrink: 0;
        background: linear-gradient(135deg, #e74a3b, #c0392b);
        color: white;
    }

    .card-amount {
        font-weight: 700;
        font-size: 0.85rem;
        text-align: right;
        flex-shrink: 0;
    }

    .income-card-body {
        margin-bottom: 10px;
        flex: 1;
        min-height: 50px;
        overflow: hidden;
    }

    .card-description-wrapper {
        background: rgba(231, 74, 59, 0.05);
        padding: 8px 10px;
        border-radius: 6px;
        border-left: 3px solid rgba(231, 74, 59, 0.3);
    }

    .card-description {
        margin: 0;
        color: var(--text);
        font-size: 0.78rem;
        line-height: 1.4;
        word-break: break-word;
    }

    .income-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 10px;
        border-top: 1px solid rgba(0,0,0,0.1);
        gap: 8px;
        flex-wrap: wrap;
        flex-shrink: 0;
    }

    .card-time {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 0.7rem;
        color: var(--text-muted);
        flex: 1;
        min-width: 0;
    }

    .card-actions {
        display: flex;
        gap: 4px;
        flex-shrink: 0;
    }

    .btn-action {
        width: 26px;
        height: 26px;
        border: none;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        flex-shrink: 0;
    }

    .btn-view {
        background: var(--primary);
        color: white;
    }

    .btn-edit {
        background: var(--info);
        color: white;
    }

    .btn-delete {
        background: var(--danger);
        color: white;
    }

    .btn-action:hover {
        transform: scale(1.1);
    }

    .kanban-column-footer {
        padding: 10px 12px;
        border-top: 1px solid rgba(255,255,255,0.1);
        background: rgba(0,0,0,0.02);
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.75rem;
        flex-shrink: 0;
        gap: 8px;
        flex-wrap: wrap;
    }

    .column-total {
        font-weight: 600;
        flex: 1;
        min-width: 0;
    }

    .column-count {
        color: var(--text-muted);
        flex-shrink: 0;
    }

    .dropdown .btn-outline-primary {
        border-color: var(--primary);
        color: var(--primary);
        font-size: 0.8rem;
        padding: 6px 12px;
    }

    .dropdown-menu {
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border: 1px solid rgba(0,0,0,0.1);
    }

    .dropdown-item.active {
        background-color: var(--primary);
        color: white;
    }

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
        background-color: var(--danger);
        color: white;
        box-shadow: 0 4px 8px rgba(231, 74, 59, 0.3);
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

    @media (max-width: 640px) {
        .income-card-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .card-amount {
            text-align: left;
        }

        .income-card-footer {
            flex-direction: column;
            align-items: flex-start;
        }

        .card-actions {
            align-self: flex-end;
        }
    }

    .kanban-column-body:empty::after {
        content: "Tidak ada pengeluaran";
        display: block;
        text-align: center;
        color: var(--text-muted);
        padding: 20px;
        font-style: italic;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('.delete-form');
                const source = this.getAttribute('data-source');
                Swal.fire({
                    title: 'Hapus pengeluaran ini?',
                    text: 'Anda akan menghapus pengeluaran dari "' + source + '". Tindakan ini tidak dapat dibatalkan!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            timer: 3000,
            showConfirmButton: false
        });
    @endif
</script>
@endpush