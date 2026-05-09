<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
        }
        body {
            font-family: 'Inter', sans-serif;
            color: #1e293b;
            margin: 0;
            padding: 0;
            background-color: #f1f5f9;
        }
        .no-print-zone {
            padding: 15px;
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: center;
            gap: 10px;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
        }
        .btn-primary { background: #4361ee; color: white; }
        .btn-secondary { background: #64748b; color: white; }
        
        #print-area {
            width: 210mm;
            min-height: 297mm;
            margin: 10mm auto;
            background: white;
            padding: 10mm;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
            color: #0f172a;
            font-weight: 800;
        }
        .header p {
            margin: 2px 0 0;
            color: #64748b;
            font-size: 11px;
        }

        .info-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 11px;
            color: #475569;
            background: #f8fafc;
            padding: 8px 12px;
            border-radius: 6px;
        }

        .day-group {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .day-title {
            background: #1e293b;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 12px;
            margin-bottom: 10px;
            display: inline-block;
            text-transform: uppercase;
        }

        /* 2-Column Grid for Locations */
        .column-container {
            display: flex;
            gap: 15px;
            width: 100%;
        }
        .column {
            flex: 1;
        }

        .location-card {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-bottom: 15px;
            overflow: hidden;
            page-break-inside: avoid;
        }
        .location-header {
            background: #f1f5f9;
            padding: 6px 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11px;
            font-weight: 700;
            display: flex;
            justify-content: space-between;
            color: #4361ee;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        table th, table td {
            border: 1px solid #e2e8f0;
            padding: 6px 8px;
            text-align: left;
        }
        table th {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 9px;
        }
        .text-bold { font-weight: 700; color: #0f172a; }

        .signature-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-around;
            text-align: center;
            page-break-inside: avoid;
        }
        .sig-box {
            width: 40%;
        }
        .sig-title { font-size: 11px; margin-bottom: 40px; }
        .sig-name { font-size: 11px; font-weight: 700; text-decoration: underline; }

        @media print {
            body { background: white; }
            .no-print-zone { display: none; }
            #print-area { margin: 0; box-shadow: none; width: 100%; padding: 8mm; }
        }
    </style>
</head>
<body>

    <div class="no-print-zone">
        <button onclick="window.history.back()" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </button>
        <button onclick="downloadPDF()" class="btn btn-primary">
            <i class="fas fa-download"></i> Download Laporan
        </button>
    </div>

    <div id="print-area">
        <div class="header">
            <h1>JADWAL PIKET SANTRI MA'HAD AL-MAHIR</h1>
            <p>Unit Manajemen Asrama - Laporan Operasional Harian</p>
        </div>

        <div class="info-bar">
            <span><strong>PERIODE:</strong> {{ $request->tanggal_mulai ? \Carbon\Carbon::parse($request->tanggal_mulai)->translatedFormat('d M Y') : 'SEMUA' }} - {{ $request->tanggal_selesai ? \Carbon\Carbon::parse($request->tanggal_selesai)->translatedFormat('d M Y') : 'SEMUA' }}</span>
            <span><strong>DICETAK:</strong> {{ now()->translatedFormat('d/m/Y H:i') }}</span>
        </div>

        @php
            $groupedByDate = $jadwal->groupBy(function($item) {
                return $item->tanggal->format('Y-m-d');
            });
        @endphp

        @forelse($groupedByDate as $date => $dayItems)
            <div class="day-group">
                <div class="day-title">
                    <i class="fas fa-calendar-alt mr-1"></i> {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                </div>

                @php
                    // SMART BALANCING LOGIC FOR PDF
                    $groupedByLocation = $dayItems->groupBy('lokasi_piket');
                    $leftColumn = collect();
                    $rightColumn = collect();
                    $leftTotal = 0;
                    $rightTotal = 0;

                    $sortedLocations = $groupedByLocation->sortByDesc(function($items) {
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

                <div class="column-container">
                    {{-- Left Column --}}
                    <div class="column">
                        @foreach($leftColumn as $location => $items)
                            <div class="location-card">
                                <div class="location-header">
                                    <span><i class="fas fa-map-marker-alt mr-1"></i> {{ $location ?: 'UMUM' }}</span>
                                    <span>{{ $items->count() }} SANTRI</span>
                                </div>
                                <table>
                                    <thead>
                                        <tr>
                                            <th width="50">SHIFT</th>
                                            <th>NAMA SANTRI</th>
                                            <th width="80">PARAF</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $item)
                                            <tr>
                                                <td style="text-transform: capitalize;">{{ $item->shift }}</td>
                                                <td class="text-bold">{{ $item->siswa->nama ?? '-' }}</td>
                                                <td style="height: 25px;"></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                    </div>

                    {{-- Right Column --}}
                    <div class="column">
                        @foreach($rightColumn as $location => $items)
                            <div class="location-card">
                                <div class="location-header">
                                    <span><i class="fas fa-map-marker-alt mr-1"></i> {{ $location ?: 'UMUM' }}</span>
                                    <span>{{ $items->count() }} SANTRI</span>
                                </div>
                                <table>
                                    <thead>
                                        <tr>
                                            <th width="50">SHIFT</th>
                                            <th>NAMA SANTRI</th>
                                            <th width="80">PARAF</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $item)
                                            <tr>
                                                <td style="text-transform: capitalize;">{{ $item->shift }}</td>
                                                <td class="text-bold">{{ $item->siswa->nama ?? '-' }}</td>
                                                <td style="height: 25px;"></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            <div style="text-align: center; padding: 40px; color: #94a3b8; border: 1px dashed #e2e8f0; border-radius: 8px; font-size: 12px;">
                <p>Tidak ada data jadwal piket.</p>
            </div>
        @endforelse

        <div class="signature-section">
            <div class="sig-box">
                <div class="sig-title">Mengetahui,<br><strong>Musyrif Asrama</strong></div>
                <div class="sig-name">{{ $request->nama_musyrif ?? '................................' }}</div>
            </div>
            <div class="sig-box">
                <div class="sig-title">Menyetujui,<br><strong>Kepala Sekolah</strong></div>
                <div class="sig-name">{{ $request->nama_kepsek ?? '................................' }}</div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function downloadPDF() {
            const element = document.getElementById('print-area');
            const opt = {
                margin:       [0, 0, 0, 0],
                filename:     'Jadwal_Piket_Compact_{{ now()->format('Ymd') }}.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2.5, useCORS: true },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            const btn = document.querySelector('.btn-primary');
            const originalContent = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            btn.disabled = true;

            html2pdf().set(opt).from(element).save().then(() => {
                btn.innerHTML = originalContent;
                btn.disabled = false;
            });
        }
    </script>
</body>
</html>
