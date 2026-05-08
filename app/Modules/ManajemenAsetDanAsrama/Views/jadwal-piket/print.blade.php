<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #444;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            text-transform: uppercase;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0 0;
            font-style: italic;
        }
        .info {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table th, table td {
            border: 1px solid #999;
            padding: 10px;
            text-align: left;
        }
        table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .status-badge {
            font-size: 10px;
            padding: 2px 5px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }
        .footer {
            margin-top: 50px;
            text-align: right;
            font-size: 12px;
        }
        .signature {
            margin-top: 60px;
            display: inline-block;
            width: 200px;
            border-top: 1px solid #000;
            text-align: center;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
            Cetak Sekarang
        </button>
        <button onclick="window.history.back()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
            Kembali
        </button>
    </div>

    <div class="header">
        <h1>Jadwal Piket Santri</h1>
        <p>Ma'had Al-Mahir - Manajemen Asrama</p>
    </div>

    <div class="info">
        <div>
            <strong>Periode:</strong> 
            {{ $request->tanggal_mulai ? \Carbon\Carbon::parse($request->tanggal_mulai)->format('d M Y') : 'Awal' }} 
            s/d 
            {{ $request->tanggal_selesai ? \Carbon\Carbon::parse($request->tanggal_selesai)->format('d M Y') : 'Akhir' }}
        </div>
        <div style="text-align: right;">
            <strong>Dicetak pada:</strong> {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    @php
        $groupedJadwal = $jadwal->groupBy(function($item) {
            return $item->tanggal->format('Y-m-d');
        });
    @endphp

    @forelse($groupedJadwal as $date => $items)
        <div style="page-break-inside: avoid; margin-bottom: 40px;">
            <div style="background: #f0f0f0; padding: 8px 15px; border: 1px solid #999; border-bottom: none; font-weight: bold; font-size: 16px;">
                <i class="fas fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
            </div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 80px;">Waktu</th>
                        <th style="width: 150px;">Lokasi</th>
                        <th>Nama Santri</th>
                        <th style="width: 100px;">NIS</th>
                        <th style="width: 150px;">Tanda Tangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                        <tr>
                            <td style="text-align: center; text-transform: capitalize;">{{ $item->shift }}</td>
                            <td style="font-weight: bold; color: #007bff;">{{ $item->lokasi_piket ?? '-' }}</td>
                            <td>
                                <strong>{{ $item->siswa->nama }}</strong>
                            </td>
                            <td>{{ $item->siswa->nis }}</td>
                            <td style="height: 45px;"></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @empty
        <div style="text-align: center; padding: 50px; border: 1px dashed #ccc;">
            Tidak ada jadwal dalam periode ini.
        </div>
    @endforelse

    <div class="footer">
        <div class="signature">
            Musyrif Asrama
        </div>
    </div>

    <script>
        // window.print();
    </script>
</body>
</html>
