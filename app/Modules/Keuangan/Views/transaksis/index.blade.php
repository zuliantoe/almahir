@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Laporan Transaksi</h1>
        <div class="text-muted">
            {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        @php
            $months = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
                7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
        @endphp

        <!-- Total Saldo -->
        <div class="col-xl-6 col-lg-6 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Saldo Tahun {{ $currentYear }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @if($totalSaldoYear < 0)
                                    -Rp{{ number_format(abs($totalSaldoYear), 0, ',', '.') }}
                                @else
                                    Rp{{ number_format($totalSaldoYear, 0, ',', '.') }}
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Pemasukan Filter -->
        <div class="col-xl-6 col-lg-6 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Pemasukan ({{ $currentMonth != 'all' ? $months[$currentMonth] : 'Tahun Ini' }})
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp{{ number_format($totalPemasukanFilter, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-down fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Pengeluaran Filter -->
        <div class="col-xl-6 col-lg-6 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Pengeluaran ({{ $currentMonth != 'all' ? $months[$currentMonth] : 'Tahun Ini' }})
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp{{ number_format($totalPengeluaranFilter, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-up fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jumlah Transaksi -->
        <div class="col-xl-6 col-lg-6 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Transaksi
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $totalTransaksiFilter }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-receipt fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow border-0">
                <div class="card-body p-3 p-lg-4">
                    <div class="row align-items-center g-3">
                        <!-- Left: Filter Form -->
                        <div class="col-12 col-lg-7 mb-4 mb-lg-0">
                            <form id="filterForm" method="GET" action="{{ route('keuangan.transaksis.index') }}">
                                <div class="row g-2 align-items-end">
                                    <div class="col-12 col-md-4">
                                        <label class="form-label small text-muted font-weight-bold mb-1 d-block">Tahun</label>
                                        <select name="year" class="form-select custom-select shadow-sm w-100" onchange="this.form.submit()">
                                            @foreach($allYears as $yearItem)
                                                <option value="{{ $yearItem }}" {{ $currentYear == $yearItem ? 'selected' : '' }}>
                                                    {{ $yearItem }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <label class="form-label small text-muted font-weight-bold mb-1 d-block">Bulan</label>
                                        <select name="month" class="form-select custom-select shadow-sm w-100" onchange="this.form.submit()">
                                            <option value="all" {{ $currentMonth == 'all' ? 'selected' : '' }}>Semua Bulan</option>
                                            @foreach($months as $key => $monthName)
                                                <option value="{{ $key }}" {{ $currentMonth == $key ? 'selected' : '' }}>
                                                    {{ $monthName }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <label class="form-label small text-muted font-weight-bold mb-1 d-block">Jenis Transaksi</label>
                                        <select name="type" class="form-select custom-select shadow-sm w-100" onchange="this.form.submit()">
                                            <option value="all" {{ $currentType == 'all' ? 'selected' : '' }}>Semua Transaksi</option>
                                            <option value="Pemasukan" {{ $currentType == 'Pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                                            <option value="Pengeluaran" {{ $currentType == 'Pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Right: Action Buttons Grouped -->
                        <div class="col-12 col-lg-5">
                            <div class="d-flex flex-column gap-3 h-100 justify-content-center">
                                <!-- Row 1: Print & Export -->
                                <div class="row g-2 mb-3">
                                    @php
                                        $printUrl = route('keuangan.transaksis.print', ['year' => $currentYear, 'month' => $currentMonth, 'type' => $currentType]);
                                        $pdfUrl = route('keuangan.transaksis.print', ['year' => $currentYear, 'month' => $currentMonth, 'type' => $currentType, 'export' => 'pdf']);
                                    @endphp
                                    <div class="col-6">
                                        <a href="{{ $printUrl }}" target="_blank" class="btn btn-primary w-100 shadow-sm py-2" title="Print Laporan">
                                            <i class="fas fa-print me-1"></i> Print
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <a href="javascript:void(0)" onclick="confirmExport('{{ $pdfUrl }}')" class="btn btn-danger w-100 shadow-sm py-2" title="Export PDF">
                                            <i class="fas fa-file-pdf me-1"></i> Export PDF
                                        </a>
                                    </div>
                                </div>
                                <!-- Row 2: Add Income & Expense -->
                                <div class="row g-2">
                                    <div class="col-6">
                                        <a href="{{ route('keuangan.pemasukans.create') }}" class="btn btn-success w-100 shadow-sm py-2" title="Tambah Pemasukan">
                                            Tambah Pemasukan
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <a href="{{ route('keuangan.pengeluarans.create') }}" class="btn btn-danger w-100 shadow-sm py-2" style="background-color: var(--danger); border-color: var(--danger);" title="Tambah Pengeluaran">
                                            Tambah Pengeluaran
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Laporan -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Preview Laporan Transaksi</h6>
                    <div class="small text-muted mt-1">
                        Jenis Transaksi : {{ $currentType == 'all' ? 'Semua Transaksi' : $currentType }}
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead class="bg-light">
                                <tr class="text-center">
                                    <th width="15%">Tanggal</th>
                                    <th>Keterangan (Pemasukan/Pengeluaran)</th>
                                    @if($currentType == 'all' || $currentType == 'Pemasukan')
                                        <th width="20%">Kredit (Pemasukan)</th>
                                    @endif
                                    @if($currentType == 'all' || $currentType == 'Pengeluaran')
                                        <th width="20%">Debit (Pengeluaran)</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @if($groupedTransactions->isEmpty())
                                <tr>
                                    <td colspan="{{ $currentType == 'all' ? '4' : '3' }}" class="text-center py-4 text-muted">Tidak ada data transaksi pada periode ini.</td>
                                </tr>
                                @else
                                    @php
                                        $grandTotalKredit = 0;
                                        $grandTotalDebit = 0;
                                    @endphp

                                    @foreach($groupedTransactions as $date => $transactions)
                                        @php
                                            $carbonDate = \Carbon\Carbon::parse($date);
                                            $dayName = $carbonDate->locale('id')->translatedFormat('l');
                                            $formattedDate = $carbonDate->format('d/m/Y');
                                            $dailyKredit = $transactions->where('jenis', 'Pemasukan')->sum('jumlah');
                                            $dailyDebit = $transactions->where('jenis', 'Pengeluaran')->sum('jumlah');
                                            
                                            $grandTotalKredit += $dailyKredit;
                                            $grandTotalDebit += $dailyDebit;
                                        @endphp
                                        
                                        <!-- Header Hari -->
                                        <tr style="background-color: #dbe4f9;">
                                            <td class="font-weight-bold text-center">{{ $formattedDate }}</td>
                                            <td class="font-weight-bold" colspan="{{ $currentType == 'all' ? '3' : '2' }}">{{ $dayName }} - {{ $transactions->count() }} transaksi</td>
                                        </tr>

                                        <!-- Detail Transaksi -->
                                        @foreach($transactions as $trx)
                                        <tr>
                                            <td class="text-center text-muted">
                                                {{ $trx['waktu'] != '-' ? $trx['waktu'] . ' WIB' : '-' }}
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <div class="{{ $trx['jenis'] == 'Pemasukan' ? 'text-success' : 'text-danger' }}">
                                                            {{ $trx['jenis'] }}
                                                        </div>
                                                        <small class="text-muted" style="white-space: pre-line;">{{ $trx['deskripsi'] ?: '-' }}</small>
                                                    </div>
                                                    <span class="badge {{ $trx['jenis'] == 'Pemasukan' ? 'bg-success' : 'bg-danger' }} text-white px-2 py-1" style="border-radius: 4px;">
                                                        {{ $trx['keterangan'] }}
                                                    </span>
                                                </div>
                                            </td>
                                            @if($currentType == 'all' || $currentType == 'Pemasukan')
                                            <td class="text-right text-success font-weight-bold">
                                                {{ $trx['jenis'] == 'Pemasukan' ? 'Rp' . number_format($trx['jumlah'], 0, ',', '.') : '-' }}
                                            </td>
                                            @endif
                                            @if($currentType == 'all' || $currentType == 'Pengeluaran')
                                            <td class="text-right text-danger font-weight-bold">
                                                {{ $trx['jenis'] == 'Pengeluaran' ? 'Rp' . number_format($trx['jumlah'], 0, ',', '.') : '-' }}
                                            </td>
                                            @endif
                                        </tr>
                                        @endforeach
                                        
                                        <!-- Footer Hari -->
                                        <tr class="bg-light">
                                            <td colspan="2" class="text-right font-weight-bold">Total:</td>
                                            @if($currentType == 'all' || $currentType == 'Pemasukan')
                                            <td class="text-right text-success font-weight-bold">Rp{{ number_format($dailyKredit, 0, ',', '.') }}</td>
                                            @endif
                                            @if($currentType == 'all' || $currentType == 'Pengeluaran')
                                            <td class="text-right text-danger font-weight-bold">
                                                {{ $dailyDebit > 0 ? 'Rp' . number_format($dailyDebit, 0, ',', '.') : 'Rp0' }}
                                            </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                    
                                    <!-- Spacer -->
                                    <tr style="border: none; background-color: transparent;">
                                        <td colspan="{{ $currentType == 'all' ? '4' : '3' }}" style="border: none; height: 30px;"></td>
                                    </tr>

                                    <!-- Grand Total -->
                                    <tr style="background-color: #e9ecef;">
                                        <td colspan="2" class="text-right font-weight-bold text-uppercase">TOTAL {{ $currentMonth != 'all' ? 'BULAN INI' : 'TAHUN INI' }}:</td>
                                        @if($currentType == 'all' || $currentType == 'Pemasukan')
                                        <td class="text-right text-success font-weight-bold">Rp{{ number_format($grandTotalKredit, 0, ',', '.') }}</td>
                                        @endif
                                        @if($currentType == 'all' || $currentType == 'Pengeluaran')
                                        <td class="text-right text-danger font-weight-bold">Rp{{ number_format($grandTotalDebit, 0, ',', '.') }}</td>
                                        @endif
                                    </tr>
                                    @if($currentType == 'all')
                                    <tr style="background-color: #fff3cd;">
                                        <td colspan="2" class="text-right font-weight-bold">SALDO AKHIR {{ $currentMonth != 'all' ? 'BULAN' : 'TAHUN' }}:</td>
                                        <td colspan="2" class="text-center font-weight-bold {{ ($grandTotalKredit - $grandTotalDebit) < 0 ? 'text-danger' : 'text-success' }}">
                                            Rp{{ number_format($grandTotalKredit - $grandTotalDebit, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    @endif
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
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

    .border-left-success {
        border-left: 0.35rem solid var(--success) !important;
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
    .border-left-danger {
        border-left: 0.35rem solid var(--danger) !important;
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

    /* Icon Colors */
    .text-success {
        color: var(--success) !important;
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
    
    .text-danger {
        color: var(--danger) !important;
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

    .table-bordered th, .table-bordered td {
        vertical-align: middle;
    }
</style>
@endpush
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmExport(url) {
        Swal.fire({
            title: 'Export ke PDF',
            text: "Sistem akan membuka jendela cetak. Pastikan Anda memilih 'Simpan sebagai PDF' (Save as PDF) pada opsi Tujuan (Destination).",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Lanjutkan',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.open(url, '_blank');
            }
        });
    }
</script>
@endpush
