@php
    $logoPath = public_path('logo.png');
    $logoBase64 = '';
    if (file_exists($logoPath)) {
        $logoData = file_get_contents($logoPath);
        $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
    }

    // Normalisasi nomor HP untuk tombol WhatsApp
    $normalizePhone = function($phone) {
        $clean = preg_replace('/[^0-9]/', '', $phone);
        if (strpos($clean, '0') === 0) {
            $clean = '62' . substr($clean, 1);
        }
        return $clean;
    };
    $waAyah = $pendaftaran->no_hp_ayah ? $normalizePhone($pendaftaran->no_hp_ayah) : '';
    $waIbu = $pendaftaran->no_hp_ibu ? $normalizePhone($pendaftaran->no_hp_ibu) : '';
@endphp
@extends('layouts.app')

@section('title', 'Detail Pendaftaran')

@section('content-header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Detail Pendaftaran</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item">Admin</li>
                <li class="breadcrumb-item">
                    <a href="/pendaftaran/admin/pendaftaran">Pendaftaran</a>
                </li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>
        </div>
    </div>
@endsection


@section('content')

@push('styles')
<style>
    /* Matikan efek hover kartu di halaman detail agar tidak jitter saat klik tombol */
    .card:hover {
        transform: none !important;
    }

    /* Animasi modal muncul dari bawah ke atas yang lebih stabil */
    .modal-bottom-up.fade .modal-dialog {
        transform: translateY(20px);
        opacity: 0;
        transition: all 0.3s ease-out;
    }
    .modal-bottom-up.show .modal-dialog {
        transform: translateY(0);
        opacity: 1;
    }

    /* Print only area */
    @media print {
        body {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body * {
            visibility: hidden;
        }

        #print-jadwal,
        #print-jadwal * {
            visibility: visible;
        }

        #print-jadwal {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            margin: 0;
            padding: 20px; /* Sedikit padding agar tidak mepet ujung kertas */
        }

        table {
            page-break-inside: auto;
        }
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        .text-muted {
            color: #6c757d !important;
        }
    }
</style>
@endpush


    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                Data Lengkap Siswa
            </h3>
        </div>

        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            {{-- DATA SISWA --}}
            <h5 class="mb-3"><strong>Data Siswa</strong></h5>
            <table class="table table-bordered mb-4">
                <tr>
                    <th width="30%">NISN</th>
                    <td>{{ $pendaftaran->nisn }}</td>
                </tr>
                <tr>
                    <th>Nama Lengkap</th>
                    <td>{{ $pendaftaran->nama_lengkap }}</td>
                </tr>
                <tr>
                    <th>Tempat Lahir</th>
                    <td>{{ $pendaftaran->tempat_lahir }}</td>
                </tr>
                <tr>
                    <th>Tanggal Lahir</th>
                    <td>{{ $pendaftaran->tanggal_lahir }}</td>
                </tr>
                <tr>
                    <th>Jenis Kelamin</th>
                    <td>
                        {{ $pendaftaran->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                    </td>
                </tr>
                <tr>
                    <th>Berat Badan</th>
                    <td>{{ $pendaftaran->berat_badan }} kg</td>
                </tr>
                <tr>
                    <th>Tinggi Badan</th>
                    <td>{{ $pendaftaran->tinggi_badan }} cm</td>
                </tr>
                <tr>
                    <th>Riwayat Sakit</th>
                    <td>{{ $pendaftaran->riwayat_sakit }}</td>
                </tr>
            </table>


            {{-- DATA ALAMAT --}}
            <h5 class="mb-3"><strong>Data Alamat</strong></h5>
            <table class="table table-bordered mb-4">
                <tr>
                    <th width="30%">Kelurahan</th>
                    <td>{{ $pendaftaran->kelurahan }}</td>
                </tr>
                <tr>
                    <th>Kecamatan</th>
                    <td>{{ $pendaftaran->kecamatan }}</td>
                </tr>
                <tr>
                    <th>Kota</th>
                    <td>{{ $pendaftaran->kota }}</td>
                </tr>
                <tr>
                    <th>Provinsi</th>
                    <td>{{ $pendaftaran->provinsi }}</td>
                </tr>
                <tr>
                    <th>Alamat Lengkap</th>
                    <td>{{ $pendaftaran->alamat }}</td>
                </tr>
            </table>


            {{-- DATA ORANG TUA --}}
            <h5 class="mb-3"><strong>Data Orang Tua</strong></h5>
            <div class="row mb-4">
                <div class="col-md-6">
                    <p class="mb-1 text-muted"><strong>Data Ayah</strong></p>
                    <table class="table table-sm table-bordered">
                        <tr>
                            <th width="40%">Nama Ayah</th>
                            <td>{{ $pendaftaran->nama_ayah }}</td>
                        </tr>
                        <tr>
                            <th>Pekerjaan</th>
                            <td>{{ $pendaftaran->pekerjaan_ayah }}</td>
                        </tr>
                        <tr>
                            <th>No HP</th>
                            <td>
                                @if($pendaftaran->no_hp_ayah)
                                    <a href="https://wa.me/{{ $waAyah }}" target="_blank" class="text-success font-weight-bold">
                                        <i class="fab fa-whatsapp"></i> {{ $pendaftaran->no_hp_ayah }}
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Alamat</th>
                            <td>{{ $pendaftaran->alamat_ayah ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <p class="mb-1 text-muted"><strong>Data Ibu</strong></p>
                    <table class="table table-sm table-bordered">
                        <tr>
                            <th width="40%">Nama Ibu</th>
                            <td>{{ $pendaftaran->nama_ibu ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Pekerjaan</th>
                            <td>{{ $pendaftaran->pekerjaan_ibu ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>No HP</th>
                            <td>
                                @if($pendaftaran->no_hp_ibu)
                                    <a href="https://wa.me/{{ $waIbu }}" target="_blank" class="text-success font-weight-bold">
                                        <i class="fab fa-whatsapp"></i> {{ $pendaftaran->no_hp_ibu }}
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Alamat</th>
                            <td>{{ $pendaftaran->alamat_ibu ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-12 mt-2">
                    <table class="table table-sm table-bordered">
                        <tr>
                            <th width="20%">Email (Wali)</th>
                            <td>{{ $pendaftaran->email }}</td>
                        </tr>
                    </table>
                </div>
            </div>


            {{-- STATUS & ADMIN --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="m-0"><strong>Status Pendaftaran</strong></h5>
                <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#modalCatatan">
                    <i class="fas fa-edit"></i> Edit Catatan
                </button>
            </div>
            <table class="table table-bordered">
                <tr>
                    <th width="30%">Status</th>
                    <td>
                        @if ($pendaftaran->status == 'pending')
                            <span class="badge badge-warning">Ditunda</span>
                        @elseif($pendaftaran->status == 'diproses')
                            <span class="badge badge-info">Diproses</span>
                        @elseif($pendaftaran->status == 'diterima')
                            <span class="badge badge-success">Diterima</span>
                        @else
                            <span class="badge badge-danger">Ditolak</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Tanggal Daftar</th>
                    <td>{{ date('d-m-Y H:i', strtotime($pendaftaran->tanggal_daftar)) }} </td>
                </tr>
                <tr>
                    <th>Tanggal Diterima</th>
                    <td>{{ $pendaftaran->tanggal_diterima ? date('d-m-Y H:i', strtotime($pendaftaran->tanggal_diterima)) : '-' }}</td>
                </tr>
                <tr>
                    <th>Catatan</th>
                    <td>{{ $pendaftaran->catatan ?? '-' }}</td>
                </tr>
            </table>

            <div class="mt-3">
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalTerima" {{ $pendaftaran->status == 'diterima' ? 'disabled' : '' }}>
                    <i class="fas fa-check"></i> Terima
                </button>

                <button type="button" class="btn btn-danger ml-2" data-toggle="modal" data-target="#modalTolak" {{ $pendaftaran->status == 'ditolak' ? 'disabled' : '' }}>
                    <i class="fas fa-times"></i> Tolak
                </button>
            </div>

            <!-- Modal Terima -->
            <div class="modal modal-bottom-up fade" id="modalTerima" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form action="/pendaftaran/admin/pendaftaran/{{ $pendaftaran->id }}/status" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="diterima">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title">Konfirmasi Terima</h5>
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body text-center py-4">
                                <i class="fas fa-check-circle text-success mb-3" style="font-size: 4rem;"></i>
                                <h5 class="mb-0">Apakah Anda yakin ingin menerima pendaftar ini?</h5>
                                <p class="text-muted mt-2">Status pendaftaran akan diubah menjadi "Diterima".</p>
                            </div>
                            <div class="modal-footer justify-content-center">
                                <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-success px-4">Ya, Terima</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Tolak -->
            <div class="modal modal-bottom-up fade" id="modalTolak" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form action="/pendaftaran/admin/pendaftaran/{{ $pendaftaran->id }}/status" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="ditolak">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title">Konfirmasi Tolak</h5>
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body text-center py-4">
                                <i class="fas fa-times-circle text-danger mb-3" style="font-size: 4rem;"></i>
                                <h5 class="mb-0">Apakah Anda yakin ingin menolak pendaftar ini?</h5>
                                <p class="text-muted mt-2">Status pendaftaran akan diubah menjadi "Ditolak".</p>
                            </div>
                            <div class="modal-footer justify-content-center">
                                <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-danger px-4">Ya, Tolak</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Catatan -->
            <div class="modal modal-bottom-up fade" id="modalCatatan" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form action="/pendaftaran/admin/pendaftaran/{{ $pendaftaran->id }}/catatan" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-header bg-info text-white">
                                <h5 class="modal-title">Edit Catatan Pendaftaran</h5>
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body text-left">
                                <div class="form-group">
                                    <label>Catatan Tambahan</label>
                                    <textarea name="catatan" rows="4" class="form-control" placeholder="Tulis catatan admin di sini...">{{ $pendaftaran->catatan }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-info">Simpan Catatan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- JADWAL TES --}}
            <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
                <h5 class="m-0"><strong>Jadwal Tes</strong></h5>
                <div class="d-flex align-items-center">
                    {{-- Fitur Download Jadwal disembunyikan
                    @if ($pendaftaran->seleksis->count() > 0)
                        <button type="button" id="btn-download-jadwal" class="btn btn-sm btn-outline-primary mr-2" onclick="downloadJadwal()">
                            <i class="fas fa-download mr-1"></i> Download Jadwal
                        </button>
                    @endif
                    --}}

                    <button type="button" class="btn btn-sm btn-info mr-2" data-toggle="modal" data-target="#modalPilihTemplate">
                        <i class="fas fa-list-ol"></i> Pilih Template
                    </button>
                    <a href="/pendaftaran/admin/pendaftaran/{{ $pendaftaran->id }}/jadwal" class="btn btn-sm btn-success">
                        <i class="fas fa-calendar-plus"></i> Set Jadwal Manual
                    </a>
                </div>
            </div>

            <!-- Modal Pilih Template -->
            <div class="modal modal-bottom-up fade" id="modalPilihTemplate" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form action="/pendaftaran/admin/pendaftaran/{{ $pendaftaran->id }}/apply-template" method="POST">
                            @csrf
                            <div class="modal-header bg-info text-white">
                                <h5 class="modal-title">Pilih Template Tes Seleksi</h5>
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body text-left">
                                <div class="form-group">
                                    <label>Pilih Template</label>
                                    <select name="template_id" class="form-control" required>
                                        <option value="">-- Pilih Template --</option>
                                        @foreach(\Modules\Pendaftaran\Models\TemplateSeleksi::latest()->get() as $tmpl)
                                            <option value="{{ $tmpl->id }}">{{ $tmpl->nama_template }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Template akan mendaftarkan beberapa tes sekaligus ke pendaftar.</small>
                                </div>
                                <div class="form-group">
                                    <label>Tanggal Tes <i>(Default untuk semua baris tes)</i></label>
                                    <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Jam <i>(Default, bisa diedit nanti)</i></label>
                                    <input type="time" name="jam" class="form-control" value="08:00" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-info">Terapkan Template</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- AREA PRINT --}}
            <div id="print-jadwal">
                
                {{-- KOP SURAT (Hanya tampil saat di-print) --}}
                <div class="d-none d-print-block">
                    <div class="print-header mb-2">
                        <table style="width: 100%; border: none !important; margin-bottom: 0;">
                            <tr>
                                <td style="width: 15%; text-align: left; vertical-align: middle; border: none !important; padding: 0 !important;">
                                    <img src="{{ asset('logo.png') }}" alt="Logo" style="width: 95px; height: auto;">
                                </td>
                                <td style="width: 85%; text-align: center; vertical-align: middle; border: none !important; padding: 0 !important; padding-right: 95px;">
                                    <h4 style="margin: 0; font-family: 'Arial', sans-serif; font-weight: 700; font-size: 1.15rem; text-transform: uppercase; letter-spacing: 0.5px;">YAYASAN ALMAHIR ATTARBAWIYYAH SURAKARTA</h4>
                                    <h1 style="margin: 2px 0; font-family: 'Arial', sans-serif; font-weight: 900; font-size: 1.85rem; color: #2C5E9E; line-height: 1.1;">PONDOK PESANTREN QUR’AN DAN IT AL MAHIR</h1>
                                    <p style="margin: 0; font-family: 'Arial', sans-serif; font-weight: 700; font-size: 0.95rem; text-transform: uppercase;">NSP : 510033130037</p>
                                    <p style="margin: 2px 0 0 0; font-family: 'Times New Roman', serif; font-size: 0.88rem; line-height: 1.3;">
                                        Alamat : Jl. Adi Sumarmo RT 001/RW 007 Gawanan, Colomadu, Karanganyar, Jawa Tengah 57175 <br>
                                        No Telp : (0271) 7686636
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div style="border-bottom: 4px double #000; padding-bottom: 5px; margin-bottom: 12px;"></div>
                    <div style="border-bottom: 2px solid #000; margin-top: -10px; margin-bottom: 24px;"></div>
                </div>

                <div class="text-center mb-4 d-none d-print-block">
                    <h4 style="font-weight: bold; text-decoration: underline;">JADWAL TES SELEKSI</h4>
                </div>
                <table class="table table-bordered mb-4 d-none d-print-block">
                    <tbody>
                        <tr>
                            <th width="30%">Nama Siswa</th>
                            <td>{{ $pendaftaran->nama_lengkap }}</td>
                        </tr>
                    </tbody>
                </table>

                @if ($pendaftaran->seleksis->count() > 0)
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nama Tes</th>
                                <th>Tanggal</th>
                                <th>Jam</th>
                                <th>Pengampu</th>
                                <th>Metode</th>
                                <th>Lokasi / Link</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pendaftaran->seleksis as $jadwal)
                                <tr>
                                    <td>{{ $jadwal->nama_tes }}</td>
                                    <td>{{ $jadwal->tanggal }}</td>
                                    <td>{{ $jadwal->jam }}</td>
                                    <td>{{ $jadwal->guru ? $jadwal->guru->nama : $jadwal->pengampu }}</td>
                                    <td>{{ $jadwal->metode }}</td>
                                    <td>
                                        @if(strtolower($jadwal->metode) == 'offline')
                                            {{ $jadwal->lokasi ?? '-' }}
                                        @elseif(strtolower($jadwal->metode) == 'online')
                                            {{ $jadwal->link ?? '-' }}
                                        @else
                                            {{ $jadwal->lokasi ?? '-' }}
                                            @if ($jadwal->link)
                                                <br>
                                                <span>{{ $jadwal->link }}</span>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted">Belum ada jadwal tes</p>
                @endif
            </div>

            {{-- ACTION BUTTONS FOR PRINT & WHATSAPP --}}
            @if ($pendaftaran->seleksis->count() > 0)
                <div class="mt-4 border-top pt-3">
                    <button type="button" id="btn-download-jadwal" class="btn btn-primary" onclick="downloadJadwal()">
                        <i class="fas fa-file-pdf mr-1"></i> Cetak Jadwal (PDF)
                    </button>
                    
                    @php
                        $primaryWa = $waAyah ?: $waIbu;
                    @endphp
                    @if($primaryWa)
                        <button type="button" class="btn btn-success ml-2" onclick="sendToWhatsApp('{{ $primaryWa }}')">
                            <i class="fab fa-whatsapp mr-1"></i> Cetak & Kirim ke WA
                        </button>
                    @endif
                </div>
            @endif
        </div>


        <div class="card-footer text-right">
            <a href="/pendaftaran/admin/pendaftaran" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

    </div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    function downloadJadwal() {
        const btn = document.getElementById('btn-download-jadwal');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Proses...';
        btn.disabled = true;

        // Ambil logo base64 dari PHP
        const logoBase64 = "{{ $logoBase64 }}";

        // Susun HTML mandiri dengan CSS inline khusus untuk cetak PDF
        const htmlContent = `
            <html>
            <head>
                <style>
                    body {
                        font-family: 'Arial', sans-serif;
                        padding: 20px;
                        background-color: #ffffff;
                        color: #333333;
                    }
                    .print-header {
                        margin-bottom: 5px;
                    }
                    .double-line {
                        border-bottom: 4px double #000000;
                        padding-bottom: 5px;
                        margin-bottom: 8px;
                    }
                    .single-line {
                        border-bottom: 1.5px solid #000000;
                        margin-top: -6px;
                        margin-bottom: 20px;
                    }
                    .text-center {
                        text-align: center;
                    }
                    .mb-4 {
                        margin-bottom: 1.5rem;
                    }
                    .kop-table {
                        width: 100%;
                        border: none !important;
                        margin-bottom: 0;
                    }
                    .kop-table td {
                        border: none !important;
                        padding: 0 !important;
                    }
                    .table-pdf {
                        width: 100%;
                        border-collapse: collapse;
                        margin-bottom: 20px;
                    }
                    .table-pdf th, .table-pdf td {
                        border: 1px solid #666666;
                        padding: 8px 10px;
                        text-align: left;
                        vertical-align: middle;
                        font-size: 13px;
                    }
                    .table-pdf th {
                        background-color: #f2f2f2;
                        font-weight: bold;
                        width: 25%;
                    }
                    .table-jadwal {
                        width: 100%;
                        border-collapse: collapse;
                    }
                    .table-jadwal th, .table-jadwal td {
                        border: 1px solid #666666;
                        padding: 8px 10px;
                        text-align: left;
                        vertical-align: middle;
                        font-size: 12px;
                    }
                    .table-jadwal th {
                        background-color: #f2f2f2;
                        font-weight: bold;
                    }
                </style>
            </head>
            <body>
                <div class="print-header">
                    <table class="kop-table">
                        <tr>
                            <td style="width: 15%; text-align: left; vertical-align: middle;">
                                <img src="${logoBase64}" alt="Logo" style="width: 90px; height: auto;">
                            </td>
                            <td style="width: 85%; text-align: center; vertical-align: middle; padding-right: 90px;">
                                <h4 style="margin: 0; font-family: 'Arial', sans-serif; font-weight: 700; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">YAYASAN ALMAHIR ATTARBAWIYYAH SURAKARTA</h4>
                                <h1 style="margin: 2px 0; font-family: 'Arial', sans-serif; font-weight: 900; font-size: 20px; color: #2C5E9E; line-height: 1.1;">PONDOK PESANTREN QUR’AN DAN IT AL MAHIR</h1>
                                <p style="margin: 0; font-family: 'Arial', sans-serif; font-weight: 700; font-size: 11px; text-transform: uppercase;">NSP : 510033130037</p>
                                <p style="margin: 2px 0 0 0; font-family: 'Times New Roman', serif; font-size: 10.5px; line-height: 1.3;">
                                    Alamat : Jl. Adi Sumarmo RT 001/RW 007 Gawanan, Colomadu, Karanganyar, Jawa Tengah 57175 <br>
                                    No Telp : (0271) 7686636
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="double-line"></div>
                <div class="single-line"></div>

                <div class="text-center mb-4">
                    <h4 style="font-weight: bold; text-decoration: underline; font-size: 16px;">JADWAL TES SELEKSI</h4>
                </div>

                <table class="table-pdf mb-4">
                    <tbody>
                        <tr>
                            <th>Nama Siswa</th>
                            <td>{{ $pendaftaran->nama_lengkap }}</td>
                        </tr>
                    </tbody>
                </table>

                <table class="table-jadwal">
                    <thead>
                        <tr>
                            <th>Nama Tes</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Pengampu</th>
                            <th>Metode</th>
                            <th>Lokasi / Link</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pendaftaran->seleksis as $jadwal)
                            <tr>
                                <td>{{ $jadwal->nama_tes }}</td>
                                <td>{{ $jadwal->tanggal }}</td>
                                <td>{{ $jadwal->jam }}</td>
                                <td>{{ $jadwal->guru ? $jadwal->guru->nama : $jadwal->pengampu }}</td>
                                <td>{{ ucfirst($jadwal->metode) }}</td>
                                <td>
                                    @if(strtolower($jadwal->metode) == 'offline')
                                        {{ $jadwal->lokasi ?? '-' }}
                                    @elseif(strtolower($jadwal->metode) == 'online')
                                        {{ $jadwal->link ?? '-' }}
                                    @else
                                        {{ $jadwal->lokasi ?? '-' }}
                                        @if ($jadwal->link)
                                            <br>
                                            <span>{{ $jadwal->link }}</span>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </body>
            </html>
        `;

        // Konfigurasi PDF
        var opt = {
            margin:       10,
            filename:     'Jadwal_Tes_{{ str_replace(" ", "_", $pendaftaran->nama_lengkap) }}.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        // Render langsung dari HTML string
        html2pdf().set(opt).from(htmlContent).save().then(() => {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        }).catch(err => {
            console.error(err);
            btn.innerHTML = originalHtml;
            btn.disabled = false;
            alert('Gagal mengunduh jadwal tes.');
        });
    }

    function sendToWhatsApp(phone) {
        // Trigger the PDF download first
        downloadJadwal();

        // Prefilled message template
        const namaSiswa = "{{ $pendaftaran->nama_lengkap }}";
        const message = `Assalamualaikum wr. wb.,\n\nBerikut kami kirimkan jadwal tes seleksi PPDB Pondok Pesantren Qur'an & IT Al-Mahir untuk calon santri atas nama *${namaSiswa}*.\n\nSilakan lampirkan dokumen PDF jadwal tes yang baru saja terunduh.\n\nSyukran, Jazakumullahu Khairan.`;
        const encodedMessage = encodeURIComponent(message);
        const waUrl = `https://wa.me/${phone}?text=${encodedMessage}`;
        
        // Open WhatsApp Web/App in a new window after 1 second
        setTimeout(() => {
            window.open(waUrl, '_blank');
        }, 1000);
    }
</script>
@endpush

@endsection
