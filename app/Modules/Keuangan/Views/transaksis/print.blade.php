<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Tabungan Keuangan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #4e73df;
            font-size: 20px;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .header h2 {
            color: #6c757d;
            font-size: 14px;
            margin: 0;
            font-weight: normal;
        }
        .info-table {
            width: 100%;
            margin-bottom: 15px;
            font-size: 12px;
        }
        .info-table td {
            padding: 2px 0;
        }
        /* .table-pull-up moved to @media print to avoid overlap on screen */
        
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .main-table th, .main-table td {
            border: 1px solid #dee2e6;
            padding: 8px;
            vertical-align: middle;
        }
        .main-table th {
            background-color: #f8f9fa;
            text-align: center;
            font-weight: bold;
        }
        .row-date {
            background-color: #dbe4f9; /* Light blue from image */
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }
        .text-muted { color: #6c757d; }
        .badge {
            display: inline-block;
            padding: 3px 6px;
            font-size: 10px;
            font-weight: bold;
            color: white;
            border-radius: 4px;
            text-align: center;
        }
        .bg-success { background-color: #28a745; }
        .bg-danger { background-color: #dc3545; }
        
        .keterangan-cell {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .desc-text {
            font-size: 10px;
            color: #6c757d;
            display: block;
            margin-top: 2px;
            white-space: pre-line;
        }
        
        .row-total-hari {
            background-color: #f8f9fa;
        }
        .row-grand-total {
            background-color: #e9ecef;
            font-weight: bold;
        }
        .row-saldo {
            background-color: #fff3cd; /* Yellowish */
            font-weight: bold;
        }
        
        .signature-area {
            width: 100%;
            margin-top: 50px;
            page-break-inside: avoid;
        }
        .signature-box {
            width: 50%;
            float: left;
            text-align: center;
        }
        .signature-space {
            height: 80px;
        }

        @media print {
            @page {
                margin: 0;
            }
            body {
                padding: 0 2cm; /* Margin Kiri & Kanan */
                margin: 0;
            }
            .header {
                padding-top: 2cm; /* Margin atas khusus Halaman 1 */
                margin-bottom: 20px;
            }
            .no-print {
                display: none;
            }
            /* Menjamin background color ter-print */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            /* Spacer untuk margin berulang di setiap halaman */
            .page-spacer-header {
                height: 2.5cm;
                border: none !important;
            }
            .page-spacer-footer {
                height: 2.5cm;
                border: none !important;
            }
            .table-pull-up {
                margin-top: -2.5cm; /* Menarik div ke atas sejauh tinggi spacer */
            }
        }
    </style>
</head>
@php
    $isPdf = request()->get('export') === 'pdf' || request()->query('export') === 'pdf';
@endphp
<body>

<div id="laporan-content">

    @php
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $periode = $currentMonth != 'all' ? $months[$currentMonth] . ' ' . $currentYear : 'Tahun ' . $currentYear;
    @endphp

    <div class="header">
        <h1>Buku Tabungan Keuangan</h1>
        <h2>Laporan {{ $currentMonth != 'all' ? 'Bulan ' . $months[$currentMonth] : 'Tahun' }} {{ $currentYear }}</h2>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%"><b>Periode</b></td>
            <td width="35%">: {{ $periode }}</td>
            <td width="15%"><b>Tanggal Cetak</b></td>
            <td width="35%">: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td width="15%"><b>Jenis Transaksi</b></td>
            <td width="85%" colspan="3">: {{ $currentType == 'all' ? 'Semua Transaksi' : $currentType }}</td>
        </tr>
    </table>

    <div class="table-pull-up">
        <table class="main-table">
            <thead>
            <tr style="border: none !important;">
                <th colspan="{{ $currentType == 'all' ? '4' : '3' }}" class="page-spacer-header" style="border: none !important; background: transparent !important;"></th>
            </tr>
            <tr>
                <th width="15%">Tanggal</th>
                <th width="45%">Keterangan (Pemasukan/Pengeluaran)</th>
                @if($currentType == 'all' || $currentType == 'Pemasukan')
                <th width="20%">Kredit<br>(Pemasukan)</th>
                @endif
                @if($currentType == 'all' || $currentType == 'Pengeluaran')
                <th width="20%">Debit<br>(Pengeluaran)</th>
                @endif
            </tr>
        </thead>
        <tfoot>
            <tr style="border: none !important;">
                <td colspan="{{ $currentType == 'all' ? '4' : '3' }}" class="page-spacer-footer" style="border: none !important; background: transparent !important;"></td>
            </tr>
        </tfoot>
        <tbody>
            @if($groupedTransactions->isEmpty())
                <tr>
                    <td colspan="{{ $currentType == 'all' ? '4' : '3' }}" class="text-center" style="padding: 20px;">Tidak ada data transaksi pada periode ini.</td>
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
                    
                    <tr class="row-date">
                        <td class="text-center">{{ $formattedDate }}</td>
                        <td colspan="{{ $currentType == 'all' ? '3' : '2' }}">{{ $dayName }} - {{ $transactions->count() }} transaksi</td>
                    </tr>

                    @foreach($transactions as $trx)
                    <tr>
                        <td class="text-center">{{ $trx['waktu'] != '-' ? $trx['waktu'] . ' WIB' : '-' }}</td>
                        <td>
                            <div style="float: left;">
                                <span class="{{ $trx['jenis'] == 'Pemasukan' ? 'text-success' : 'text-danger' }}">
                                    {{ $trx['jenis'] }}
                                </span>
                                <span class="desc-text">{{ $trx['deskripsi'] ?: '-' }}</span>
                            </div>
                            <div style="float: right;">
                                <span class="badge {{ $trx['jenis'] == 'Pemasukan' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $trx['keterangan'] }}
                                </span>
                            </div>
                            <div style="clear: both;"></div>
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
                    
                    <tr class="row-total-hari">
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

                <tr class="row-grand-total">
                    <td colspan="2" class="text-right text-uppercase">TOTAL {{ $currentMonth != 'all' ? 'BULAN INI' : 'TAHUN INI' }}:</td>
                    @if($currentType == 'all' || $currentType == 'Pemasukan')
                    <td class="text-right text-success">Rp{{ number_format($grandTotalKredit, 0, ',', '.') }}</td>
                    @endif
                    @if($currentType == 'all' || $currentType == 'Pengeluaran')
                    <td class="text-right text-danger">Rp{{ number_format($grandTotalDebit, 0, ',', '.') }}</td>
                    @endif
                </tr>
                @if($currentType == 'all')
                <tr class="row-saldo">
                    <td colspan="2" class="text-right">SALDO AKHIR {{ $currentMonth != 'all' ? 'BULAN' : 'TAHUN' }}:</td>
                    <td colspan="2" class="text-center {{ ($grandTotalKredit - $grandTotalDebit) < 0 ? 'text-danger' : 'text-success' }}">
                        Rp{{ number_format($grandTotalKredit - $grandTotalDebit, 0, ',', '.') }}
                    </td>
                </tr>
                @endif
            @endif
        </tbody>
    </table>
    </div>

    <div class="signature-area">
        <div class="signature-box">
            Mengetahui,
            <div class="signature-space"></div>
            (..................................)
        </div>
        <div class="signature-box">
            Dibuat Oleh,
            <div class="signature-space"></div>
            (..................................)
        </div>
        <div style="clear: both;"></div>
    </div>
</div> <!-- End of laporan-content -->

@if($isPdf)
<script>
    window.onload = function() {
        // Ubah title dokumen sementara untuk nama file default saat Save as PDF
        document.title = 'Laporan_Keuangan_{{ str_replace(' ', '_', $periode) }}';
        
        setTimeout(function() {
            alert("PENTING:\n\nPada jendela cetak (Print) yang muncul, pastikan Anda mengubah opsi 'Tujuan' (Destination/Printer) menjadi 'Simpan sebagai PDF' (Save as PDF) lalu klik Simpan.");
            window.print();
        }, 500);
    }
</script>
@else
<script>
    window.onload = function() {
        document.title = 'Laporan_Keuangan_{{ str_replace(' ', '_', $periode) }}';
        window.print();
    }
</script>
@endif

</body>
</html>
