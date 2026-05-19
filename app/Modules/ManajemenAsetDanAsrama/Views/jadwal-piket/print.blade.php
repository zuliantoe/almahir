<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* CSS SAKTI - STANDAR INDUSTRI */
        * { box-sizing: border-box; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        
        @page {
            size: A4;
            margin: 0; /* WAJIB 0 agar URL/judul browser tidak muncul */
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            margin: 0;
            padding: 0;
            color: #1e293b;
        }

        /* TOOLBAR */
        .toolbar {
            background: #ffffff;
            padding: 12px 20px;
            display: flex;
            justify-content: center;
            gap: 12px;
            position: sticky;
            top: 0;
            z-index: 9999;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .btn {
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: 0.2s;
            text-decoration: none;
        }
        .btn-back { background: #64748b; color: white; }
        .btn-print { background: #10b981; color: white; }
        .btn-download { background: #3b82f6; color: white; }
        .btn:hover { opacity: 0.9; transform: translateY(-1px); }

        /* KERTAS PREVIEW */
        .paper {
            background: white;
            width: 210mm;
            min-height: 297mm;
            margin: 30px auto;
            padding: 20mm; /* MARGIN AMAN DI DALAM KERTAS */
            box-shadow: 0 0 20px rgba(0,0,0,0.15);
            position: relative;
            display: flex;
            flex-direction: column;
        }

        /* HEADER */
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px double #334155;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
            text-transform: uppercase;
            color: #0f172a;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 13px;
            color: #64748b;
        }

        .meta-info {
            display: flex;
            justify-content: space-between;
            background: #f8fafc;
            padding: 12px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            margin-bottom: 25px;
            font-size: 12px;
        }

        /* CONTENT */
        .day-group {
            margin-bottom: 35px;
            break-inside: avoid;
            page-break-inside: avoid;
        }
        .day-label {
            background: #0f172a;
            color: white;
            padding: 8px 16px;
            font-weight: 800;
            font-size: 14px;
            display: inline-block;
            border-radius: 4px;
            margin-bottom: 15px;
            box-shadow: 3px 3px 0 #3b82f6;
        }

        .grid {
            display: flex;
            gap: 15px; /* Sedikit kurangi gap agar muat */
        }
        .col { 
            flex: 1; 
            min-width: 0; /* Cegah flex item melar melewati layar */
            max-width: 50%; /* Paksa persis setengah-setengah */
        }

        .card {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            margin-bottom: 20px;
            break-inside: avoid;
            page-break-inside: avoid;
            overflow: hidden;
        }
        .card-header {
            background: #f8fafc;
            padding: 10px 15px;
            border-bottom: 1px solid #cbd5e1;
            font-weight: 800;
            font-size: 12px;
            color: #2563eb;
            display: flex;
            justify-content: space-between;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            font-size: 11px; /* Sedikit dikecilkan agar aman */
            table-layout: fixed; /* KUNCI UTAMA: Paksa tabel tidak melar */
            word-wrap: break-word;
        }
        table th, table td {
            border: 1px solid #e2e8f0;
            padding: 8px 10px; /* Kurangi padding sedikit */
            vertical-align: middle;
        }
        table th {
            background: #f1f5f9;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 10px;
            text-align: left;
        }
        .text-bold { font-weight: 700; color: #0f172a; }

        /* SIGNATURE */
        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-around;
            text-align: center;
            break-inside: avoid;
        }
        .sig-box { width: 40%; }
        .sig-title { font-size: 13px; margin-bottom: 60px; font-weight: 600; }
        .sig-name { font-size: 13px; font-weight: 800; text-decoration: underline; }

        /* Sembunyikan print-wrapper di layar, tampilkan saat print */
        #print-wrapper { display: none; }
        /* Sembunyikan screen-view saat print, tampilkan di layar */
        #screen-view { display: block; }

        @media print {
            body { background: white; margin: 0; padding: 0; }
            .toolbar { display: none; }
            #screen-view { display: none !important; }
            #print-wrapper {
                display: table;
                width: 100%;
                border-collapse: collapse;
                border: none;
            }
            #print-wrapper > thead > tr > td,
            #print-wrapper > tfoot > tr > td {
                height: 20px;
                padding: 0;
                border: none;
            }
            #print-wrapper > tbody > tr > td {
                padding: 0;
                border: none;
            }
            .paper {
                display: block;
                margin: 0 !important;
                padding: 0 20mm !important;
                width: auto !important;
                max-width: none !important;
                box-shadow: none !important;
            }
            .card, .signature-section { break-inside: avoid; }
        }
    </style>
</head>
<body>

    <div class="toolbar">
        <a href="javascript:history.back()" class="btn btn-back">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <button onclick="window.print()" class="btn btn-print">
            <i class="fas fa-print"></i> Cetak Sekarang (Rapih)
        </button>
        <button id="download-btn" onclick="downloadPDF()" class="btn btn-download">
            <i class="fas fa-download"></i> Download PDF
        </button>
    </div>

    {{-- LAYAR: div paper biasa --}}
    <div id="screen-view" class="paper">
        <div class="header">
            <h1>JADWAL PIKET SANTRI MA'HAD AL-MAHIR</h1>
            <p>Unit Manajemen Asrama - Laporan Operasional Harian</p>
        </div>

        <div class="meta-info">
            <span><strong>PERIODE:</strong> {{ $request->tanggal_mulai ? \Carbon\Carbon::parse($request->tanggal_mulai)->translatedFormat('d M Y') : 'SEMUA' }} - {{ $request->tanggal_selesai ? \Carbon\Carbon::parse($request->tanggal_selesai)->translatedFormat('d M Y') : 'SEMUA' }}</span>
            <span><strong>WAKTU CETAK:</strong> {{ now()->translatedFormat('d/m/Y H:i') }}</span>
        </div>

        @php
            $groupedByDate = $jadwal->groupBy(fn($item) => $item->tanggal->format('Y-m-d'));
        @endphp

        @forelse($groupedByDate as $date => $dayItems)
            <div class="day-group">
                <div class="day-label">
                    <i class="fas fa-calendar-alt mr-2"></i> {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                </div>

                @php
                    $groupedByLocation = $dayItems->groupBy('lokasi_piket');
                    $leftCol = collect(); $rightCol = collect();
                    $leftCount = 0; $rightCount = 0;
                    $sorted = $groupedByLocation->sortByDesc(fn($items) => $items->count());

                    foreach($sorted as $loc => $items) {
                        if ($leftCount <= $rightCount) { $leftCol->put($loc, $items); $leftCount += $items->count(); }
                        else { $rightCol->put($loc, $items); $rightCount += $items->count(); }
                    }
                @endphp

                <div class="grid">
                    <div class="col">
                        @foreach($leftCol as $loc => $items)
                            <div class="card">
                                <div class="card-header">
                                    <span><i class="fas fa-map-marker-alt mr-1"></i> {{ $loc ?: 'UMUM' }}</span>
                                    <span>{{ $items->count() }} SANTRI</span>
                                </div>
                                <table>
                                    <thead>
                                        <tr><th width="60">SHIFT</th><th>NAMA SANTRI</th><th width="80">PARAF</th></tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $item)
                                            <tr>
                                                <td style="text-transform: capitalize;">{{ $item->shift }}</td>
                                                <td class="text-bold">{{ $item->siswa->nama ?? '-' }}</td>
                                                <td style="height: 30px;"></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                    </div>
                    <div class="col">
                        @foreach($rightCol as $loc => $items)
                            <div class="card">
                                <div class="card-header">
                                    <span><i class="fas fa-map-marker-alt mr-1"></i> {{ $loc ?: 'UMUM' }}</span>
                                    <span>{{ $items->count() }} SANTRI</span>
                                </div>
                                <table>
                                    <thead>
                                        <tr><th width="60">SHIFT</th><th>NAMA SANTRI</th><th width="80">PARAF</th></tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $item)
                                            <tr>
                                                <td style="text-transform: capitalize;">{{ $item->shift }}</td>
                                                <td class="text-bold">{{ $item->siswa->nama ?? '-' }}</td>
                                                <td style="height: 30px;"></td>
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
            <div style="text-align: center; padding: 50px; color: #94a3b8; border: 2px dashed #e2e8f0; border-radius: 12px;">
                <p>Data tidak ditemukan.</p>
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
        </div>{{-- end signature-section --}}
    </div>{{-- end #screen-view --}}

    {{-- PRINT: HTML table asli - thead & tfoot kosong diulang otomatis oleh browser di atas/bawah SETIAP halaman --}}
    <table id="print-wrapper" style="width:100%;border:none;border-collapse:collapse;">
        <thead><tr><td style="height:20px;padding:0;border:none;line-height:20px;font-size:0;"> </td></tr></thead>
        <tfoot><tr><td style="height:20px;padding:0;border:none;line-height:20px;font-size:0;"> </td></tr></tfoot>
        <tbody><tr><td style="padding:0;border:none;">
            <div class="paper" id="print-area">
                <div class="header">
                    <h1>JADWAL PIKET SANTRI MA'HAD AL-MAHIR</h1>
                    <p>Unit Manajemen Asrama - Laporan Operasional Harian</p>
                </div>
                <div class="meta-info">
                    <span><strong>PERIODE:</strong> {{ $request->tanggal_mulai ? \Carbon\Carbon::parse($request->tanggal_mulai)->translatedFormat('d M Y') : 'SEMUA' }} - {{ $request->tanggal_selesai ? \Carbon\Carbon::parse($request->tanggal_selesai)->translatedFormat('d M Y') : 'SEMUA' }}</span>
                    <span><strong>WAKTU CETAK:</strong> {{ now()->translatedFormat('d/m/Y H:i') }}</span>
                </div>
                @php $gbd2 = $jadwal->groupBy(fn($item) => $item->tanggal->format('Y-m-d')); @endphp
                @forelse($gbd2 as $dt2 => $di2)
                    <div class="day-group">
                        <div class="day-label"><i class="fas fa-calendar-alt mr-2"></i> {{ \Carbon\Carbon::parse($dt2)->translatedFormat('l, d F Y') }}</div>
                        @php
                            $gbl2=$di2->groupBy('lokasi_piket');
                            $lc2=collect();$rc2=collect();$lct2=0;$rct2=0;
                            foreach($gbl2->sortByDesc(fn($i)=>$i->count()) as $l2=>$it2){
                                if($lct2<=$rct2){$lc2->put($l2,$it2);$lct2+=$it2->count();}else{$rc2->put($l2,$it2);$rct2+=$it2->count();}
                            }
                        @endphp
                        <div class="grid">
                            <div class="col">
                                @foreach($lc2 as $l2 => $it2)
                                <div class="card"><div class="card-header"><span><i class="fas fa-map-marker-alt mr-1"></i> {{ $l2 ?: 'UMUM' }}</span><span>{{ $it2->count() }} SANTRI</span></div>
                                <table><thead><tr><th width="60">SHIFT</th><th>NAMA SANTRI</th><th width="80">PARAF</th></tr></thead><tbody>
                                @foreach($it2 as $p2)<tr><td style="text-transform:capitalize">{{ $p2->shift }}</td><td class="text-bold">{{ $p2->siswa->nama ?? '-' }}</td><td style="height:30px"></td></tr>@endforeach
                                </tbody></table></div>
                                @endforeach
                            </div>
                            <div class="col">
                                @foreach($rc2 as $l2 => $it2)
                                <div class="card"><div class="card-header"><span><i class="fas fa-map-marker-alt mr-1"></i> {{ $l2 ?: 'UMUM' }}</span><span>{{ $it2->count() }} SANTRI</span></div>
                                <table><thead><tr><th width="60">SHIFT</th><th>NAMA SANTRI</th><th width="80">PARAF</th></tr></thead><tbody>
                                @foreach($it2 as $p2)<tr><td style="text-transform:capitalize">{{ $p2->shift }}</td><td class="text-bold">{{ $p2->siswa->nama ?? '-' }}</td><td style="height:30px"></td></tr>@endforeach
                                </tbody></table></div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="text-align:center;padding:50px;color:#94a3b8;"><p>Data tidak ditemukan.</p></div>
                @endforelse
                <div class="signature-section">
                    <div class="sig-box"><div class="sig-title">Mengetahui,<br><strong>Musyrif Asrama</strong></div><div class="sig-name">{{ $request->nama_musyrif ?? '................................' }}</div></div>
                    <div class="sig-box"><div class="sig-title">Menyetujui,<br><strong>Kepala Sekolah</strong></div><div class="sig-name">{{ $request->nama_kepsek ?? '................................' }}</div></div>
                </div>
            </div>
        </td></tr></tbody>
    </table>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        /**
         * Sisipkan spacer sebelum .day-group yang akan kepotong
         * Kalkulasi berdasarkan tinggi slice canvas yang dipakai html2pdf.js
         */
        function insertSmartBreaks(container) {
            container.querySelectorAll('.smart-spacer').forEach(el => el.remove());

            const w = container.offsetWidth;
            if (!w || w < 50) return;

            // html2pdf slices canvas di interval ini (tanpa margin, margin ada di PDF)
            const pxPerMm = w / 210;
            const sliceH  = (297 - 5.3 - 5.3) * pxPerMm; // 286.4mm per halaman

            Array.from(container.querySelectorAll('.day-group')).forEach(group => {
                container.offsetHeight; // paksa reflow tiap iterasi

                const containerTop = container.getBoundingClientRect().top;
                const groupTop     = group.getBoundingClientRect().top - containerTop;
                const groupH       = group.offsetHeight;

                const pageIndex = Math.floor(groupTop / sliceH);
                const pageEnd   = (pageIndex + 1) * sliceH;
                const remaining = pageEnd - groupTop;

                if (groupH > remaining && remaining > 0 && remaining < sliceH) {
                    const spacer       = document.createElement('div');
                    spacer.className   = 'smart-spacer';
                    spacer.style.cssText = `height:${Math.ceil(remaining)}px;display:block;flex-shrink:0;`;
                    group.parentNode.insertBefore(spacer, group);
                }
            });
        }

        function downloadPDF() {
            const btn = document.getElementById('download-btn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyiapkan...';
            btn.disabled = true;

            const wrapper    = document.getElementById('print-wrapper');
            const screenView = document.getElementById('screen-view');
            const element    = document.getElementById('print-area');

            const restore = () => {
                element.querySelectorAll('.smart-spacer').forEach(el => el.remove());
                screenView.style.removeProperty('display');
                wrapper.style.removeProperty('display');
                element.style.cssText = '';
                btn.innerHTML = originalText;
                btn.disabled  = false;
            };

            // Tampilkan print-wrapper
            screenView.style.setProperty('display', 'none', 'important');
            wrapper.style.setProperty('display', 'block', 'important');
            element.style.cssText = 'padding:0 20mm!important;margin:0!important;box-shadow:none!important;width:210mm!important;display:block!important;';

            // Tunggu browser repaint dulu (150ms) sebelum hitung posisi
            setTimeout(() => {
                insertSmartBreaks(element);

                const opt = {
                    margin:      [5.3, 0, 5.3, 0],
                    filename:    'Jadwal_Piket_{{ date("Ymd") }}.pdf',
                    image:       { type: 'jpeg', quality: 1 },
                    html2canvas: { scale: 2, useCORS: true, letterRendering: true, scrollY: 0, scrollX: 0 },
                    jsPDF:       { unit: 'mm', format: 'a4', orientation: 'portrait' },
                    pagebreak:   { mode: ['css', 'legacy'] }
                };

                html2pdf().set(opt).from(element).save().then(restore).catch(restore);
            }, 150);
        }
    </script>
</body>
</html>
