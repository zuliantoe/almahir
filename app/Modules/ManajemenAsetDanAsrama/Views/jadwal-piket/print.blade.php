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
            padding: 20px;
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
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }
        .btn-primary { background: #4361ee; color: white; }
        .btn-secondary { background: #64748b; color: white; }
        
        #print-area {
            width: 210mm; 
            min-height: 297mm;
            margin: 20px auto;
            background: white;
            padding: 15mm;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #1e293b;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            text-transform: uppercase;
            color: #0f172a;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            margin-bottom: 20px;
            font-size: 12px;
            color: #475569;
        }

        .day-group {
            margin-bottom: 40px;
            page-break-inside: avoid;
        }
        .day-title {
            background: #f8fafc;
            border: 2px solid #1e293b;
            color: #1e293b;
            padding: 8px 15px;
            border-radius: 6px;
            font-weight: 800;
            font-size: 14px;
            margin-bottom: 15px;
            display: inline-block;
        }

        .location-section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .location-title {
            font-size: 13px;
            font-weight: 700;
            color: #4361ee;
            margin-bottom: 8px;
            padding-left: 5px;
            border-left: 4px solid #4361ee;
            display: flex;
            justify-content: space-between;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-bottom: 10px;
        }
        table th, table td {
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
            text-align: left;
        }
        table th {
            background-color: #f1f5f9;
            font-weight: 700;
            color: #475569;
        }

        .signature-section {
            margin-top: 50px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            text-align: center;
            page-break-inside: avoid;
        }
        .sig-box {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .sig-space {
            height: 70px;
        }
        .sig-name {
            font-weight: 700;
            text-decoration: underline;
        }

        @media print {
            body { background: white; }
            .no-print-zone { display: none; }
            #print-area { margin: 0; box-shadow: none; width: 100%; padding: 10mm; }
        }
    </style>
</head>
<body>

    <div class="no-print-zone">
        <button onclick="window.history.back()" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </button>
        <button onclick="downloadPDF()" class="btn btn-primary">
            <i class="fas fa-download"></i> Download PDF
        </button>
    </div>

    <div id="print-area">
        <div class="header">
            <h1>Jadwal Piket Santri</h1>
            <p style="margin: 0; font-size: 12px; color: #64748b;">Ma'had Al-Mahir - Unit Manajemen Asrama</p>
        </div>

        <div class="info-grid">
            <div>
                <strong>Periode:</strong> 
                {{ $request->tanggal_mulai ? \Carbon\Carbon::parse($request->tanggal_mulai)->translatedFormat('d M Y') : 'Semua' }} 
                - 
                {{ $request->tanggal_selesai ? \Carbon\Carbon::parse($request->tanggal_selesai)->translatedFormat('d M Y') : 'Semua' }}
            </div>
            <div style="text-align: right;">
                <strong>Dicetak:</strong> {{ now()->translatedFormat('d/m/Y H:i') }}
            </div>
        </div>

        @php
            $groupedByDate = $jadwal->groupBy(function($item) {
                return $item->tanggal->format('Y-m-d');
            });
        @endphp

        @forelse($groupedByDate as $date => $dayItems)
            <div class="day-group">
                <div class="day-title text-uppercase">
                    <i class="fas fa-calendar-day mr-2"></i> {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                </div>

                @php
                    $groupedByLocation = $dayItems->groupBy('lokasi_piket');
                @endphp

                @foreach($groupedByLocation as $location => $items)
                    <div class="location-section">
                        <div class="location-title">
                            <span><i class="fas fa-map-marker-alt mr-1"></i> LOKASI: {{ $location ?: 'UMUM' }}</span>
                            <span style="color: #64748b; font-size: 11px;">{{ $items->count() }} Santri</span>
                        </div>
                        <table>
                            <thead>
                                <tr>
                                    <th width="80">Waktu</th>
                                    <th>Nama Santri</th>
                                    <th width="120">NIS</th>
                                    <th width="150">Keterangan / Paraf</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                    <tr>
                                        <td style="text-transform: capitalize; font-weight: 600;">{{ $item->shift }}</td>
                                        <td style="font-weight: 700;">{{ $item->siswa->nama ?? '-' }}</td>
                                        <td>{{ $item->siswa->nis ?? '-' }}</td>
                                        <td style="height: 35px;"></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>
        @empty
            <div style="text-align: center; padding: 50px; color: #94a3b8; border: 1px dashed #e2e8f0;">
                <p>Tidak ada data jadwal piket.</p>
            </div>
        @endforelse

        <div class="signature-section">
            <div class="sig-box">
                <p style="font-size: 12px; margin-bottom: 5px;">Mengetahui,</p>
                <p style="font-size: 12px; font-weight: 700; margin-top: 0;">Musyrif Asrama</p>
                <div class="sig-space"></div>
                <p class="sig-name" style="font-size: 13px;">{{ $request->nama_musyrif ?? '................................' }}</p>
            </div>
            <div class="sig-box">
                <p style="font-size: 12px; margin-bottom: 5px;">Menyetujui,</p>
                <p style="font-size: 12px; font-weight: 700; margin-top: 0;">Kepala Sekolah</p>
                <div class="sig-space"></div>
                <p class="sig-name" style="font-size: 13px;">{{ $request->nama_kepsek ?? '................................' }}</p>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function downloadPDF() {
            const element = document.getElementById('print-area');
            const opt = {
                margin:       [10, 0, 10, 0],
                filename:     'Jadwal_Piket_{{ now()->format('Ymd') }}.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            const btn = document.querySelector('.btn-primary');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
            btn.disabled = true;

            html2pdf().set(opt).from(element).save().then(() => {
                btn.innerHTML = '<i class="fas fa-download"></i> Download PDF';
                btn.disabled = false;
            });
        }
    </script>
</body>
</html>
