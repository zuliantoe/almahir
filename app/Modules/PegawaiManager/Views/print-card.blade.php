<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            margin: 0;
            padding: 40px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            color: #1f2937;
            box-sizing: border-box;
        }

        .no-print-area {
            margin-bottom: 30px;
            text-align: center;
            max-width: 420px;
            width: 100%;
        }

        .btn-print {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            border: none;
            padding: 14px 35px;
            font-size: 1rem;
            font-weight: 700;
            border-radius: 50px;
            cursor: pointer;
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.25);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(37, 99, 235, 0.35);
            background: linear-gradient(135deg, #1d4ed8, #1e40af);
        }

        .btn-print:active {
            transform: translateY(0);
        }

        /* Tips Panel on Web */
        .print-tips {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            padding: 18px 20px;
            border-radius: 20px;
            margin-top: 20px;
            font-size: 0.85rem;
            color: #4b5563;
            text-align: left;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.03);
            line-height: 1.5;
        }

        .print-tips h6 {
            margin: 0 0 10px 0;
            font-size: 0.9rem;
            font-weight: 800;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .print-tips h6 i {
            color: #eab308;
        }

        .print-tips ul {
            margin: 0;
            padding-left: 20px;
        }

        .print-tips li {
            margin-bottom: 6px;
        }

        .print-tips li strong {
            color: #111827;
        }

        /* Card Container (Standard ID Card aspect ratio CR80: 1:1.58, scaled up) */
        .id-card {
            width: 350px;
            height: 550px;
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            position: relative;
            border: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            box-sizing: border-box;
            background-color: #ffffff;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Top Header Area */
        .card-header {
            width: 100%;
            height: 155px;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            color: white;
            padding-top: 25px;
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .card-header::after {
            content: '';
            position: absolute;
            bottom: -20px;
            left: 0;
            right: 0;
            height: 40px;
            background: #ffffff;
            border-radius: 50% 50% 0 0;
            z-index: 1;
        }

        .logo-placeholder {
            font-size: 1.8rem;
            font-weight: 800;
            letter-spacing: 2px;
            margin-bottom: 5px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 8px;
            z-index: 2;
        }

        .logo-placeholder i {
            color: #f59e0b; /* Gold Cap */
        }

        .sub-logo {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 3px;
            font-weight: 700;
            opacity: 0.9;
            z-index: 2;
        }

        /* User Photo Container */
        .avatar-container {
            position: absolute;
            top: 100px;
            width: 110px;
            height: 110px;
            z-index: 5;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-avatar {
            width: 102px;
            height: 102px;
            border-radius: 50%;
            border: 4px solid #ffffff;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
            object-fit: cover;
            background: #ffffff;
        }

        /* Card Content Area */
        .card-content {
            margin-top: 70px;
            padding: 0 25px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            box-sizing: border-box;
        }

        .info-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        .employee-name {
            font-size: 1.55rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .employee-title {
            font-size: 0.8rem;
            font-weight: 700;
            color: #2563eb;
            background: rgba(37, 99, 235, 0.08);
            padding: 5px 16px;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
            display: inline-block;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* QR Code wrapper */
        .qr-section {
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            padding: 10px;
            border-radius: 20px;
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .qr-image {
            width: 125px;
            height: 125px;
            display: block;
        }

        /* Card Footer */
        .card-footer {
            width: 100%;
            background: #f8fafc;
            padding: 15px 0;
            border-top: 1px solid #e2e8f0;
            font-size: 0.72rem;
            color: #64748b;
            font-weight: 600;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            border-bottom-left-radius: 24px;
            border-bottom-right-radius: 24px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .card-footer i {
            color: #10b981; /* Green shield */
        }

        /* PRINT STYLE RULES */
        @media print {
            @page {
                size: auto;
                margin: 0;
            }

            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                height: 100vh !important;
                min-height: 100vh !important;
            }

            .no-print-area {
                display: none !important;
            }

            .id-card {
                box-shadow: none !important;
                border: 1.5px dashed #000000 !important; /* Clear black dashed border for precise cutting */
                margin: 0 !important;
                page-break-inside: avoid;
                /* Force background color & images rendering */
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .card-header {
                background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%) !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .employee-title {
                background: rgba(37, 99, 235, 0.08) !important;
                color: #2563eb !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .qr-section {
                background: #f8fafc !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .card-footer {
                background: #f8fafc !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-area">
        <button class="btn-print" onclick="window.print()">
            <i class="fas fa-print"></i> Cetak Kartu QR
        </button>
        
        <div class="print-tips">
            <h6><i class="fas fa-exclamation-triangle" style="color: #dc2626;"></i> PENTING - Agar Warna Kartu Muncul:</h6>
            <ul style="padding-left: 20px; margin-bottom: 12px; color: #dc2626; font-weight: 600;">
                <li>Pilih opsi "Warna / Color" pada pilihan warna printer Anda (jangan pilih Monochrome/Hitam-Putih/Grayscale).</li>
                <li style="color: #4b5563; font-weight: normal;">Centang opsi "Grafik latar belakang" (Background graphics) pada menu setelan cetak browser.</li>
            </ul>
            
            <h6><i class="fas fa-cut"></i> Cara Memotong Kartu:</h6>
            <ul style="padding-left: 20px;">
                <li>Garis putus-putus hitam di sekeliling kartu setelah dicetak berfungsi sebagai batas potong (crop guide).</li>
                <li>Atur Margin pencetakan ke "Tanpa Margin (None)" atau "Minimum" agar ukuran kartu presisi.</li>
                <li>Gunakan kertas tebal (seperti Art Paper 260gsm) atau kertas PVC ID Card.</li>
            </ul>
        </div>
    </div>

    <div class="id-card">
        <!-- Top Wave Header -->
        <div class="card-header">
            <div class="logo-placeholder">
                <i class="fas fa-graduation-cap"></i> ALMAHIRA
            </div>
            <div class="sub-logo">KARTU ABSENSI PEGAWAI</div>
        </div>

        <!-- Photo Container -->
        <div class="avatar-container">
            <img class="user-avatar" src="{{ $pegawai->user?->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($pegawai->nama).'&background=0D8ABC&color=fff&size=150' }}" alt="Foto Pegawai">
        </div>

        <!-- Content -->
        <div class="card-content">
            <div class="info-section">
                <div class="employee-name">{{ $pegawai->nama }}</div>
                <div class="employee-title">{{ $pegawai->typePegawai?->nama_type ?? 'Pegawai' }}</div>
            </div>

            <!-- QR Code -->
            <div class="qr-section">
                <img class="qr-image" src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $pegawai->qr_token }}&margin=5" alt="QR Token Absensi">
            </div>
        </div>

        <!-- Footer -->
        <div class="card-footer">
            <i class="fas fa-shield-alt"></i> ALMAHIRA DIGITAL SYSTEM &copy; {{ date('Y') }}
        </div>
    </div>

</body>
</html>
