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
    </table>

    <div class="table-pull-up">
        <table class="main-table">
            <thead>
            <tr style="border: none !important;">
                <th colspan="4" class="page-spacer-header" style="border: none !important; background: transparent !important;"></th>
            </tr>
            <tr>
                <th width="15%">Tanggal</th>
                <th width="45%">Keterangan (Pemasukan/Pengeluaran)</th>
                <th width="20%">Kredit<br>(Pemasukan)</th>
                <th width="20%">Debit<br>(Pengeluaran)</th>
            </tr>
        </thead>
        <tfoot>
            <tr style="border: none !important;">
                <td colspan="4" class="page-spacer-footer" style="border: none !important; background: transparent !important;"></td>
            </tr>
        </tfoot>
        <tbody>
            @if($groupedTransactions->isEmpty())
                <tr>
                    <td colspan="4" class="text-center" style="padding: 20px;">Tidak ada data transaksi pada periode ini.</td>
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
                        <td colspan="3">{{ $dayName }} - {{ $transactions->count() }} transaksi</td>
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
                        <td class="text-right text-success font-weight-bold">
                            {{ $trx['jenis'] == 'Pemasukan' ? 'Rp' . number_format($trx['jumlah'], 0, ',', '.') : '-' }}
                        </td>
                        <td class="text-right text-danger font-weight-bold">
                            {{ $trx['jenis'] == 'Pengeluaran' ? 'Rp' . number_format($trx['jumlah'], 0, ',', '.') : '-' }}
                        </td>
                    </tr>
                    @endforeach
                    
                    <tr class="row-total-hari">
                        <td colspan="2" class="text-right font-weight-bold">Total:</td>
                        <td class="text-right text-success font-weight-bold">Rp{{ number_format($dailyKredit, 0, ',', '.') }}</td>
                        <td class="text-right text-danger font-weight-bold">
                            {{ $dailyDebit > 0 ? 'Rp' . number_format($dailyDebit, 0, ',', '.') : 'Rp0' }}
                        </td>
                    </tr>
                @endforeach
                
                <!-- Spacer -->
                <tr style="border: none; background-color: transparent;">
                    <td colspan="4" style="border: none; height: 30px;"></td>
                </tr>

                <tr class="row-grand-total">
                    <td colspan="2" class="text-right text-uppercase">TOTAL {{ $currentMonth != 'all' ? 'BULAN INI' : 'TAHUN INI' }}:</td>
                    <td class="text-right text-success">Rp{{ number_format($grandTotalKredit, 0, ',', '.') }}</td>
                    <td class="text-right text-danger">Rp{{ number_format($grandTotalDebit, 0, ',', '.') }}</td>
                </tr>
                <tr class="row-saldo">
                    <td colspan="2" class="text-right">SALDO AKHIR {{ $currentMonth != 'all' ? 'BULAN' : 'TAHUN' }}:</td>
                    <td colspan="2" class="text-center {{ ($grandTotalKredit - $grandTotalDebit) < 0 ? 'text-danger' : 'text-success' }}">
                        Rp{{ number_format($grandTotalKredit - $grandTotalDebit, 0, ',', '.') }}
                    </td>
                </tr>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var element = document.getElementById('laporan-content');
        
        // Show loading overlay
        var overlay = document.createElement('div');
        overlay.style.position = 'fixed';
        overlay.style.top = '0';
        overlay.style.left = '0';
        overlay.style.width = '100%';
        overlay.style.height = '100%';
        overlay.style.backgroundColor = 'white';
        overlay.style.zIndex = '99999';
        overlay.style.display = 'flex';
        overlay.style.flexDirection = 'column';
        overlay.style.justifyContent = 'center';
        overlay.style.alignItems = 'center';
        overlay.innerHTML = '<div style="border: 4px solid #f3f3f3; border-top: 4px solid #4e73df; border-radius: 50%; width: 40px; height: 40px; animation: spin 2s linear infinite;"></div><h2 style="font-family: sans-serif; color: #4e73df; margin-top: 15px;">Membuka Preview PDF...</h2><style>@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }</style>';
        document.body.appendChild(overlay);

        var opt = {
            margin:       [10, 10, 10, 10],
            filename:     'Laporan_Transaksi_{{ $currentYear }}_{{ $currentMonth }}.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true, logging: false },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' },
            pagebreak:    { mode: ['avoid-all', 'css', 'legacy'] }
        };

        // Custom filename: Laporan Keuangan [Bulan] [Tahun]
        opt.filename = 'Laporan Keuangan {{ $periode }}.pdf';

        // Generate and Save PDF (Trigger Download)
        setTimeout(function() {
            html2pdf().set(opt).from(element).save().then(function() {
                // Success - Show message or close tab
                overlay.innerHTML = '<h2 style="font-family: sans-serif; color: #28a745;">Download Selesai</h2><p style="font-family: sans-serif; color: #6c757d;">File Anda sedang didownload.</p>';
                
                // Close tab after 2 seconds
                setTimeout(function() {
                    window.close();
                }, 2000);
            }).catch(function(err) {
                console.error('PDF Error:', err);
                overlay.innerHTML = '<h2 style="font-family: sans-serif; color: #dc3545;">Gagal Mendownload PDF</h2><p style="font-family: sans-serif; color: #6c757d;">Terjadi kesalahan saat memproses laporan.</p>';
            });
        }, 500);
    });
</script>
@else
<script>
    window.onload = function() {
        window.print();
    }
</script>
@endif

</body>
</html>
