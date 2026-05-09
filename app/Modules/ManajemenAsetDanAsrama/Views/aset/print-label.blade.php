<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f4f6f9;
        }
        .label-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: flex-start;
        }
        .label-card {
            width: 320px;
            height: 160px;
            border: 2px solid #333;
            padding: 12px;
            display: flex;
            flex-direction: row;
            align-items: center;
            overflow: hidden;
            position: relative;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .qr-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100px;
            margin-right: 15px;
            border-right: 1px dashed #ccc;
            padding-right: 15px;
        }
        .qr-code {
            width: 85px;
            height: 85px;
            padding: 5px;
            background: white;
        }
        .qr-code img {
            width: 100%;
            height: 100%;
        }
        .qr-text {
            font-size: 8px;
            text-align: center;
            margin-top: 5px;
            color: #666;
            text-transform: uppercase;
            font-weight: bold;
        }
        .asset-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .header-label {
            font-size: 10px;
            font-weight: 800;
            color: #007bff;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .asset-name {
            margin: 0;
            font-size: 14px;
            font-weight: 700;
            color: #222;
            line-height: 1.2;
            height: 34px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        .asset-code {
            font-weight: 800;
            font-size: 18px;
            color: #000;
            margin: 6px 0;
            font-family: 'Courier New', monospace;
            letter-spacing: -0.5px;
        }
        .detail-row {
            display: flex;
            font-size: 10px;
            margin-top: 2px;
            color: #555;
        }
        .detail-label {
            width: 50px;
            font-weight: 600;
        }
        .footer-label {
            position: absolute;
            bottom: 8px;
            right: 12px;
            font-size: 9px;
            font-weight: bold;
            color: #999;
            text-transform: uppercase;
        }
        @media print {
            .no-print { display: none; }
            body { padding: 0; background: white; }
            .label-card { 
                box-shadow: none; 
                page-break-inside: avoid;
                margin-bottom: 10px;
                border: 1px solid #000;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 25px; background: white; padding: 15px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="mb-1 font-weight-bold">Preview Label Aset</h5>
                <p class="text-muted small mb-0">Pastikan printer sudah terhubung dan gunakan kertas stiker label jika tersedia.</p>
            </div>
            <div>
                <button onclick="window.print()" style="padding: 10px 25px; background: #28a745; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; font-size: 14px; box-shadow: 0 4px 6px rgba(40, 167, 69, 0.2);">
                    <i class="fas fa-print mr-2"></i> Cetak Sekarang
                </button>
                <button onclick="window.history.back()" style="padding: 10px 20px; background: #f8f9fa; color: #333; border: 1px solid #ddd; border-radius: 8px; cursor: pointer; margin-left: 10px; font-weight: bold; font-size: 14px;">
                    Kembali
                </button>
            </div>
        </div>
    </div>

    <div class="label-container">
        @foreach($aset as $item)
            @php
                // URL detail aset untuk discan (Real-time data)
                $detailUrl = route('manajemenasetdanasrama.aset.show', $item->id);
                $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($detailUrl);
            @endphp
            <div class="label-card">
                <div class="qr-section">
                    <div class="qr-code">
                        <img src="{{ $qrUrl }}" alt="QR Code">
                    </div>
                    <div class="qr-text">SCAN FOR DETAIL</div>
                </div>
                <div class="asset-info">
                    <div class="header-label">Aset Management</div>
                    <h2 class="asset-name">{{ $item->nama_aset }}</h2>
                    <p class="asset-code">{{ $item->kode_aset }}</p>
                    
                    <div class="detail-row">
                        <span class="detail-label">Tgl Masuk</span>
                        <span>: {{ $item->tanggal_pengadaan ? $item->tanggal_pengadaan->format('d/m/Y') : '-' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Kondisi</span>
                        <span>: {{ strtoupper($item->status_kondisi) }}</span>
                    </div>
                </div>
                <div class="footer-label">AL-MAHIR SYSTEM</div>
            </div>
        @endforeach
    </div>
</body>
</html>
