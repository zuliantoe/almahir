<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            margin: 0;
            padding: 10px;
            background: #fff;
        }
        .label-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .label-card {
            width: 280px;
            height: 140px;
            border: 1px solid #000;
            padding: 10px;
            display: flex;
            flex-direction: row;
            align-items: center;
            overflow: hidden;
            position: relative;
            background: white;
        }
        .qr-code {
            width: 90px;
            height: 90px;
            margin-right: 15px;
        }
        .qr-code img {
            width: 100%;
            height: 100%;
        }
        .asset-info {
            flex: 1;
        }
        .asset-info h2 {
            margin: 0;
            font-size: 14px;
            text-transform: uppercase;
        }
        .asset-info p {
            margin: 2px 0;
            font-size: 11px;
        }
        .asset-code {
            font-weight: bold;
            font-size: 16px !important;
            margin-top: 5px !important;
            letter-spacing: 1px;
        }
        .footer-label {
            position: absolute;
            bottom: 5px;
            right: 10px;
            font-size: 8px;
            color: #666;
        }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
            .label-card { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer;">
            Cetak Label
        </button>
        <button onclick="window.history.back()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
            Kembali
        </button>
    </div>

    <div class="label-container">
        @foreach($aset as $item)
            @php
                $detailUrl = route('manajemenasetdanasrama.aset.show', $item->id);
                $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($detailUrl);
            @endphp
            <div class="label-card">
                <div class="qr-code">
                    <img src="{{ $qrUrl }}" alt="QR Code">
                </div>
                <div class="asset-info">
                    <h2>{{ $item->nama_aset }}</h2>
                    <p class="asset-code">{{ $item->kode_aset }}</p>
                    <p><strong>Lokasi:</strong> {{ $item->kamar ? $item->kamar->nama_kamar : 'N/A' }}</p>
                    <p><strong>Kondisi:</strong> {{ ucfirst($item->status_kondisi) }}</p>
                </div>
                <div class="footer-label">AL-MAHIR ASSET MGMT</div>
            </div>
        @endforeach
    </div>
</body>
</html>
