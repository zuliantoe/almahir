<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - {{ $kamar->nama_kamar }}</title>
    <style>
        @page {
            size: A4;
            margin: 2cm;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11pt;
            color: #333;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18pt;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 10pt;
            color: #666;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 2px 0;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 8px 10px;
            text-align: left;
        }
        .data-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9pt;
        }
        .text-center { text-align: center !important; }
        .signature-container {
            margin-top: 50px;
            width: 100%;
        }
        .signature-box {
            float: left;
            width: 45%;
            text-align: center;
        }
        .signature-box.right {
            float: right;
        }
        .signature-space {
            height: 80px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            font-size: 8pt;
            text-align: right;
            color: #999;
        }
        .badge {
            padding: 2px 5px;
            border: 1px solid #ccc;
            border-radius: 3px;
            font-size: 8pt;
        }
        .clear { clear: both; }
    </style>
</head>
<body id="print-area">
    <div class="header">
        <h1>LAPORAN DAFTAR PENGHUNI KAMAR</h1>
        <p>PONDOK PESANTREN AL-MAHIR - UNIT ASRAMA PUTRA/PUTRI</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="120">Nama Kamar</td>
            <td width="10">:</td>
            <td><strong>{{ $kamar->nama_kamar }}</strong></td>
            <td width="120">Tanggal Cetak</td>
            <td width="10">:</td>
            <td>{{ date('d F Y') }}</td>
        </tr>
        <tr>
            <td>Kapasitas</td>
            <td>:</td>
            <td>{{ $kamar->kapasitas }} Siswa</td>
            <td>Total Terisi</td>
            <td>:</td>
            <td>{{ $penghuniAktif->count() }} Siswa</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="30" class="text-center">No</th>
                <th width="100">NIS</th>
                <th>Nama Lengkap Santri</th>
                <th width="120">Jabatan</th>
                <th width="100">Tgl Masuk</th>
            </tr>
        </thead>
        <tbody>
            @forelse($penghuniAktif as $item)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $item->siswa->nis ?? '-' }}</td>
                <td><strong>{{ $item->siswa->nama ?? '-' }}</strong></td>
                <td>{{ $item->jabatan ?? 'Anggota' }}</td>
                <td class="text-center">{{ $item->tanggal_masuk ? $item->tanggal_masuk->format('d/m/Y') : '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Tidak ada penghuni aktif</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="signature-container">
        <div class="signature-box">
            <p>Mengetahui,</p>
            <p><strong>Musyrif Kamar</strong></p>
            <div class="signature-space"></div>
            <p>( <u>{{ $musyrif ?? '................................' }}</u> )</p>
        </div>
        <div class="signature-box right">
            <p>Malang, {{ date('d F Y') }}</p>
            <p><strong>Kepala Sekolah / Mudir</strong></p>
            <div class="signature-space"></div>
            <p>( <u>{{ $kepsek ?? '................................' }}</u> )</p>
        </div>
        <div class="clear"></div>
    </div>

    <div class="footer">
        Dicetak melalui Sistem Manajemen Al-Mahir pada {{ date('d/m/Y H:i') }}
    </div>

    {{-- Script for PDF Download --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        window.onload = function() {
            var element = document.getElementById('print-area');
            var opt = {
                margin:       [10, 10, 10, 10],
                filename:     'Laporan-Penghuni-{{ Str::slug($kamar->nama_kamar) }}.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2 },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            // New Promise-based usage:
            html2pdf().set(opt).from(element).save().then(function() {
                // Tutup tab otomatis setelah download selesai (opsional)
                // window.close(); 
            });
        };
    </script>
</body>
</html>
