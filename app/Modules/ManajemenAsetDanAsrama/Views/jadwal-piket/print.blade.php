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
            <strong>Kamar:</strong> {{ $kamar ? $kamar->nama_kamar : 'Semua Kamar' }}<br>
            <strong>Kapasitas:</strong> {{ $kamar ? $kamar->kapasitas : '-' }} Santri
        </div>
        <div style="text-align: right;">
            <strong>Periode:</strong> 
            {{ $request->tanggal_mulai ? \Carbon\Carbon::parse($request->tanggal_mulai)->format('d M Y') : 'Awal' }} 
            s/d 
            {{ $request->tanggal_selesai ? \Carbon\Carbon::parse($request->tanggal_selesai)->format('d M Y') : 'Akhir' }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 50px;">No</th>
                <th style="width: 60px;">Foto</th>
                <th>Hari / Tanggal</th>
                <th>Nama Santri</th>
                <th>NIS</th>
                <th>Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jadwal as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="text-align: center; vertical-align: middle;">
                        @if($item->siswa->foto)
                            <img src="{{ asset('storage/' . $item->siswa->foto) }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px; border: 1px solid #ddd;">
                        @else
                            <div style="width: 50px; height: 50px; background: #eee; border-radius: 5px; display: flex; align-items: center; justify-content: center; font-size: 8px; color: #999;">No Photo</div>
                        @endif
                    </td>
                    <td>
                        {{ $item->tanggal->isoFormat('dddd') }}<br>
                        <small>{{ $item->tanggal->format('d/m/Y') }}</small>
                    </td>
                    <td>
                        <strong>{{ $item->siswa->nama }}</strong>
                    </td>
                    <td>{{ $item->siswa->nis }}</td>
                    <td style="height: 40px;"></td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Tidak ada jadwal dalam periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i') }}<br>
        <div class="signature">
            Musyrif Asrama
        </div>
    </div>

    <script>
        // Auto trigger print when loaded if needed
        // window.print();
    </script>
</body>
</html>
