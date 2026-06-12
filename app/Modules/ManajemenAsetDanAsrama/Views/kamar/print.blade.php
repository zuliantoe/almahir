<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - {{ $kamar->nama_kamar }}</title>
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
        .btn {
            padding: 10px 24px; border-radius: 8px; font-weight: 700; cursor: pointer; border: none;
            font-size: 14px; display: inline-flex; align-items: center; gap: 10px; transition: 0.2s;
            text-decoration: none;
        }
        .btn-back { background: #64748b; color: white; }
        .btn-print { background: #10b981; color: white; }
        .btn-download { background: #3b82f6; color: white; }
        .btn:hover { opacity: 0.9; transform: translateY(-1px); }

        /* Tampilan layar */
        .paper {
            background: white; width: 210mm; min-height: 297mm; margin: 30px auto;
            padding: 20mm; box-shadow: 0 0 20px rgba(0,0,0,0.15);
        }

        .header { text-align: center; margin-bottom: 25px; border-bottom: 3px double #334155; padding-bottom: 15px; }
        .header h1 { margin: 0; font-size: 20px; font-weight: 800; text-transform: uppercase; }
        .header p { margin: 5px 0 0; font-size: 12px; color: #64748b; }

        .info-grid {
            display: flex; justify-content: space-between; margin-bottom: 25px; font-size: 12px;
            background: #f8fafc; padding: 15px; border: 1px solid #e2e8f0; border-radius: 8px;
        }
        .info-item { display: flex; gap: 10px; }
        .info-label { font-weight: 600; color: #64748b; }

        table { width: 100%; border-collapse: collapse; font-size: 12px; margin-bottom: 30px; table-layout: fixed; word-wrap: break-word; }
        table th, table td { border: 1px solid #e2e8f0; padding: 8px 10px; vertical-align: middle; }
        table th { background: #f1f5f9; font-weight: 800; text-transform: uppercase; font-size: 10px; text-align: left; }
        .text-center { text-align: center; }

        .sig-container { margin-top: 40px; display: flex; justify-content: space-between; text-align: center; break-inside: avoid; }
        .sig-box { width: 40%; }
        .sig-space { height: 60px; }
        .sig-name { font-weight: 800; text-decoration: underline; }

        /* Sembunyikan print-wrapper di layar */
        #print-wrapper { display: none; }
        #screen-view { display: block; }

        @media print {
            body { background: white; margin: 0; padding: 0; }
            .toolbar { display: none; }
            #screen-view { display: none !important; }

            /* print-wrapper: HTML table dengan thead/tfoot kosong = margin atas-bawah tiap halaman */
            #print-wrapper {
                display: table;
                width: 100%;
                border-collapse: collapse;
                border: none;
            }
            #print-wrapper > thead > tr > td,
            #print-wrapper > tfoot > tr > td {
                height: 20px; padding: 0; border: none;
            }
            #print-wrapper > tbody > tr > td {
                padding: 0; border: none;
            }
            .paper {
                display: block; margin: 0 !important;
                padding: 0 20mm !important;
                width: auto !important; max-width: none !important;
                box-shadow: none !important;
            }
            .sig-container { break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <a href="javascript:history.back()" class="btn btn-back"><i class="fas fa-arrow-left"></i> Kembali</a>
        <button onclick="window.print()" class="btn btn-print"><i class="fas fa-print"></i> Cetak Sekarang</button>
        <button id="download-btn" onclick="downloadPDF()" class="btn btn-download"><i class="fas fa-file-pdf"></i> Download PDF</button>
    </div>

    {{-- LAYAR: tampilan preview biasa --}}
    <div id="screen-view" class="paper">
        <div class="header">
            <h1>LAPORAN DAFTAR PENGHUNI KAMAR</h1>
            <p>PONDOK PESANTREN AL-MAHIR - UNIT ASRAMA PUTRA/PUTRI</p>
        </div>
        <div class="info-grid">
            <div class="info-item"><span class="info-label">Kamar:</span> <strong>{{ $kamar->nama_kamar }}</strong></div>
            <div class="info-item"><span class="info-label">Kapasitas:</span> {{ $kamar->kapasitas }} (Terisi: {{ $penghuniAktif->count() }})</div>
            <div class="info-item"><span class="info-label">Dicetak:</span> {{ date('d/m/Y') }}</div>
        </div>
        <table>
            <thead>
                <tr>
                    <th width="40" class="text-center">NO</th>
                    <th width="100">NIS</th>
                    <th>NAMA LENGKAP SANTRI</th>
                    <th width="120">JABATAN</th>
                    <th width="100" class="text-center">TGL MASUK</th>
                </tr>
            </thead>
            <tbody>
                @foreach($penghuniAktif as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $item->siswa->nis ?? '-' }}</td>
                    <td><strong>{{ $item->siswa->nama ?? '-' }}</strong></td>
                    <td>{{ $item->jabatan ?? 'Anggota' }}</td>
                    <td class="text-center">{{ $item->tanggal_masuk ? $item->tanggal_masuk->format('d/m/Y') : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="sig-container">
            <div class="sig-box">
                <p>Mengetahui,<br><strong>Musyrif Kamar</strong></p>
                <div class="sig-space"></div>
                <p class="sig-name">( {{ $musyrif ?? '................................' }} )</p>
            </div>
            <div class="sig-box">
                <p>Karanganyar, {{ date('d F Y') }}<br><strong>Kepala Sekolah</strong></p>
                <div class="sig-space"></div>
                <p class="sig-name">( {{ $kepsek ?? '................................' }} )</p>
            </div>
        </div>
    </div>

    {{-- PRINT: HTML table dengan thead/tfoot kosong = margin 20px atas-bawah tiap halaman --}}
    <table id="print-wrapper" style="width:100%;border:none;border-collapse:collapse;">
        <thead><tr><td style="height:20px;padding:0;border:none;font-size:0;"> </td></tr></thead>
        <tfoot><tr><td style="height:20px;padding:0;border:none;font-size:0;"> </td></tr></tfoot>
        <tbody><tr><td style="padding:0;border:none;">
            <div class="paper" id="print-area">
                <div class="header">
                    <h1>LAPORAN DAFTAR PENGHUNI KAMAR</h1>
                    <p>PONDOK PESANTREN AL-MAHIR - UNIT ASRAMA PUTRA/PUTRI</p>
                </div>
                <div class="info-grid">
                    <div class="info-item"><span class="info-label">Kamar:</span> <strong>{{ $kamar->nama_kamar }}</strong></div>
                    <div class="info-item"><span class="info-label">Kapasitas:</span> {{ $kamar->kapasitas }} (Terisi: {{ $penghuniAktif->count() }})</div>
                    <div class="info-item"><span class="info-label">Dicetak:</span> {{ date('d/m/Y') }}</div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th width="40" class="text-center">NO</th>
                            <th width="100">NIS</th>
                            <th>NAMA LENGKAP SANTRI</th>
                            <th width="120">JABATAN</th>
                            <th width="100" class="text-center">TGL MASUK</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($penghuniAktif as $item)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $item->siswa->nis ?? '-' }}</td>
                            <td><strong>{{ $item->siswa->nama ?? '-' }}</strong></td>
                            <td>{{ $item->jabatan ?? 'Anggota' }}</td>
                            <td class="text-center">{{ $item->tanggal_masuk ? $item->tanggal_masuk->format('d/m/Y') : '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="sig-container">
                    <div class="sig-box">
                        <p>Mengetahui,<br><strong>Musyrif Kamar</strong></p>
                        <div class="sig-space"></div>
                        <p class="sig-name">( {{ $musyrif ?? '................................' }} )</p>
                    </div>
                    <div class="sig-box">
                        <p>Karanganyar, {{ date('d F Y') }}<br><strong>Kepala Sekolah</strong></p>
                        <div class="sig-space"></div>
                        <p class="sig-name">( {{ $kepsek ?? '................................' }} )</p>
                    </div>
                </div>
            </div>
        </td></tr></tbody>
    </table>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function downloadPDF() {
            const btn        = document.getElementById('download-btn');
            const wrapper    = document.getElementById('print-wrapper');
            const screenView = document.getElementById('screen-view');
            const element    = document.getElementById('print-area');
            const origText   = btn.innerHTML;

            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyiapkan...';
            btn.disabled  = true;

            const restore = () => {
                screenView.style.removeProperty('display');
                wrapper.style.removeProperty('display');
                element.style.cssText = '';
                btn.innerHTML = origText;
                btn.disabled  = false;
            };

            // Tampilkan print-wrapper untuk di-capture
            screenView.style.setProperty('display', 'none', 'important');
            wrapper.style.setProperty('display', 'block', 'important');
            element.style.cssText = 'padding:0 20mm!important;margin:0!important;box-shadow:none!important;width:210mm!important;display:block!important;';

            setTimeout(() => {
                const opt = {
                    margin:      [5.3, 0, 5.3, 0], // 5.3mm ≈ 20px atas & bawah
                    filename:    'Penghuni-{{ Str::slug($kamar->nama_kamar) }}-{{ date("Ymd") }}.pdf',
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
