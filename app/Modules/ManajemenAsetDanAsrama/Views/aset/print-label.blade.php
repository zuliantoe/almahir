<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * { box-sizing: border-box; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        @page { size: A4; margin: 0; } /* margin: 0 agar URL browser tidak muncul */
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; margin: 0; padding: 0; color: #1e293b; }

        .toolbar {
            background: white; padding: 12px 20px; display: flex; justify-content: center; gap: 12px;
            position: sticky; top: 0; z-index: 9999; border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        .btn { padding: 10px 24px; border-radius: 8px; font-weight: 700; cursor: pointer; border: none; font-size: 14px; display: inline-flex; align-items: center; gap: 10px; transition: 0.2s; text-decoration: none; }
        .btn-back { background: #64748b; color: white; }
        .btn-print { background: #10b981; color: white; }
        .btn-download { background: #3b82f6; color: white; }
        .btn:hover { opacity: 0.9; transform: translateY(-1px); }

        /* ===== LABEL CARD STYLE (shared) ===== */
        .label-row { display: flex; gap: 8mm; margin-bottom: 8mm; break-inside: avoid; page-break-inside: avoid; }
        .label-card {
            width: 80mm; height: 40mm; border: 1.5px solid #333; border-radius: 6px;
            padding: 8px; display: flex; gap: 8px; background: white; flex-shrink: 0;
        }
        .qr-side { width: 28mm; display: flex; flex-direction: column; align-items: center; justify-content: center; border-right: 1px dashed #ccc; padding-right: 8px; flex-shrink: 0; }
        .qr-side img { width: 100%; height: auto; }
        .qr-side span { font-size: 7px; font-weight: 800; margin-top: 4px; color: #666; }
        .info-side { flex: 1; display: flex; flex-direction: column; justify-content: center; overflow: hidden; }
        .info-header { font-size: 7px; font-weight: 900; color: #dc3545; text-transform: uppercase; margin-bottom: 2px; }
        .info-name { font-size: 11px; font-weight: 800; margin: 0 0 2px; line-height: 1.2; max-height: 26px; overflow: hidden; }
        .info-code { font-size: 13px; font-weight: 900; margin: 3px 0; font-family: monospace; }
        .info-detail { font-size: 8px; color: #666; }

        /* ===== LAYAR ===== */
        #screen-view {
            background: white; width: 210mm; min-height: 297mm; margin: 30px auto;
            padding: 15mm 20mm; box-shadow: 0 0 20px rgba(0,0,0,0.15);
        }

        /* ===== PRINT: thead/tfoot kosong = 20px margin tiap halaman ===== */
        #print-wrapper { display: none; }
        #pdf-content   { display: none; }

        @media print {
            body { background: white; margin: 0; padding: 0; }
            .toolbar { display: none; }
            #screen-view { display: none !important; }
            #pdf-content { display: none !important; }

            /* thead & tfoot kosong diulang otomatis browser = 20px atas/bawah tiap halaman */
            #print-wrapper {
                display: table; width: 100%;
                border-collapse: collapse; border: none;
            }
            #print-wrapper > thead > tr > td,
            #print-wrapper > tfoot > tr > td { height: 20px; padding: 0; border: none; font-size: 0; }
            #print-wrapper > tbody > tr > td { padding: 0; border: none; }

            .print-area {
                display: block; margin: 0 !important; padding: 0 20mm !important;
                width: auto !important; box-shadow: none !important;
            }
            .label-row { break-inside: avoid; page-break-inside: avoid; }
            .label-card { break-inside: avoid; page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <a href="javascript:history.back()" class="btn btn-back"><i class="fas fa-arrow-left"></i> Kembali</a>
        <button onclick="window.print()" class="btn btn-print"><i class="fas fa-print"></i> Cetak Sekarang</button>
        <button id="download-btn" onclick="downloadPDF()" class="btn btn-download"><i class="fas fa-file-pdf"></i> Download PDF</button>
    </div>

    {{-- ===== LAYAR: preview label ===== --}}
    <div id="screen-view">
        @php $rows = $aset->chunk(2); @endphp
        @foreach($rows as $row)
        <div class="label-row">
            @foreach($row as $item)
            <div class="label-card">
                <div class="qr-side">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($item->kode_aset) }}" alt="QR" crossorigin="anonymous">
                    <span>SCAN DETAIL</span>
                </div>
                <div class="info-side">
                    <div class="info-header">PROPERTY OF AL-MAHIR</div>
                    <h2 class="info-name">{{ $item->nama_aset }}</h2>
                    <div class="info-code">{{ $item->kode_aset }}</div>
                    <div class="info-detail">Kondisi: {{ strtoupper($item->status_kondisi) }}</div>
                </div>
            </div>
            @endforeach
            @if($row->count() < 2)<div style="width:80mm;flex-shrink:0;"></div>@endif
        </div>
        @endforeach
    </div>

    {{-- ===== PRINT: thead/tfoot = margin 20px atas-bawah tiap halaman ===== --}}
    <table id="print-wrapper" style="width:100%;border:none;border-collapse:collapse;">
        <thead><tr><td style="height:20px;padding:0;border:none;font-size:0;"> </td></tr></thead>
        <tfoot><tr><td style="height:20px;padding:0;border:none;font-size:0;"> </td></tr></tfoot>
        <tbody><tr><td style="padding:0;border:none;">
            <div class="print-area">
                @foreach($rows as $row)
                <div class="label-row">
                    @foreach($row as $item)
                    <div class="label-card">
                        <div class="qr-side">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($item->kode_aset) }}" alt="QR" crossorigin="anonymous">
                            <span>SCAN DETAIL</span>
                        </div>
                        <div class="info-side">
                            <div class="info-header">PROPERTY OF AL-MAHIR</div>
                            <h2 class="info-name">{{ $item->nama_aset }}</h2>
                            <div class="info-code">{{ $item->kode_aset }}</div>
                            <div class="info-detail">Kondisi: {{ strtoupper($item->status_kondisi) }}</div>
                        </div>
                    </div>
                    @endforeach
                    @if($row->count() < 2)<div style="width:80mm;flex-shrink:0;"></div>@endif
                </div>
                @endforeach
            </div>
        </td></tr></tbody>
    </table>

    {{-- ===== PDF DOWNLOAD: identik dengan print, margin 20px atas-bawah ===== --}}
    <div id="pdf-content" style="background:white;width:210mm;padding:0 20mm;">
        @foreach($rows as $row)
        <div class="label-row">
            @foreach($row as $item)
            <div class="label-card">
                <div class="qr-side">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($item->kode_aset) }}" alt="QR" crossorigin="anonymous">
                    <span>SCAN DETAIL</span>
                </div>
                <div class="info-side">
                    <div class="info-header">PROPERTY OF AL-MAHIR</div>
                    <h2 class="info-name">{{ $item->nama_aset }}</h2>
                    <div class="info-code">{{ $item->kode_aset }}</div>
                    <div class="info-detail">Kondisi: {{ strtoupper($item->status_kondisi) }}</div>
                </div>
            </div>
            @endforeach
            @if($row->count() < 2)<div style="width:80mm;flex-shrink:0;"></div>@endif
        </div>
        @endforeach
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function downloadPDF() {
            const btn      = document.getElementById('download-btn');
            const element  = document.getElementById('pdf-content');
            const origText = btn.innerHTML;

            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memuat gambar...';
            btn.disabled  = true;

            // Tampilkan element untuk di-capture
            element.style.display = 'block';

            // Tunggu semua QR image selesai load
            const images   = Array.from(element.querySelectorAll('img'));
            const promises = images.map(img => {
                if (img.complete && img.naturalWidth > 0) return Promise.resolve();
                return new Promise(resolve => { img.onload = resolve; img.onerror = resolve; });
            });

            Promise.all(promises).then(() => {
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating PDF...';

                setTimeout(() => {
                    const opt = {
                        margin:      [5.3, 0, 5.3, 0], // 5.3mm ≈ 20px atas & bawah (sama dengan cetak)
                        filename:    'Label_Aset_AlMahir_{{ date("Ymd_His") }}.pdf',
                        image:       { type: 'jpeg', quality: 0.98 },
                        html2canvas: { scale: 2, useCORS: true, allowTaint: false, letterRendering: true, scrollY: 0, scrollX: 0 },
                        jsPDF:       { unit: 'mm', format: 'a4', orientation: 'portrait' },
                        pagebreak:   { mode: ['css', 'legacy'], avoid: ['.label-row', '.label-card'] }
                    };

                    html2pdf().set(opt).from(element).save().then(() => {
                        element.style.display = 'none';
                        btn.innerHTML = origText;
                        btn.disabled  = false;
                    }).catch(() => {
                        element.style.display = 'none';
                        btn.innerHTML = origText;
                        btn.disabled  = false;
                    });
                }, 100);
            });
        }
    </script>
</body>
</html>
