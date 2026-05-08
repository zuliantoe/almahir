@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4 no-print">
        <div class="col-12 d-flex justify-content-between align-items-center bg-white p-3 shadow-sm rounded-lg">
            <div>
                <a href="{{ route('penilaiandanpresensi.penilaianakademik.raport.index') }}" class="btn btn-outline-secondary px-4">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>
            <div class="text-center d-none d-md-block">
                <h5 class="mb-0 font-weight-bold text-primary">Pratinjau Raport Digital</h5>
                <p class="text-muted small mb-0">Pastikan data sudah benar sebelum mencetak</p>
            </div>
            <div>
                <button onclick="window.print()" class="btn btn-primary px-5 shadow-sm font-weight-bold btn-lg">
                    <i class="fas fa-print mr-2"></i> CETAK RAPORT
                </button>
            </div>
        </div>
    </div>

    {{-- Official Raport Sheet --}}
    <div class="raport-sheet shadow-lg mx-auto bg-white mb-5">
        {{-- Header / Kop Surat --}}
        <div class="raport-header mb-2">
            <table style="width: 100%; border: none !important;">
                <tr>
                    <td style="width: 15%; text-align: left; vertical-align: middle; border: none !important; padding: 0 !important;">
                        <img src="{{ asset('logo.png') }}" alt="Logo" style="width: 95px; height: auto;">
                    </td>
                    <td style="width: 85%; text-align: center; vertical-align: middle; border: none !important; padding: 0 !important; padding-right: 95px;">
                        <h4 style="margin: 0; font-family: 'Inter', sans-serif; font-weight: 700; font-size: 1.15rem; text-transform: uppercase; letter-spacing: 0.5px;">YAYASAN ALMAHIR ATTARBAWIYYAH SURAKARTA</h4>
                        <h1 style="margin: 2px 0; font-family: 'Inter', sans-serif; font-weight: 900; font-size: 1.85rem; color: #2C5E9E; line-height: 1.1;">PONDOK PESANTREN QUR’AN DAN IT AL MAHIR</h1>
                        <p style="margin: 0; font-family: 'Inter', sans-serif; font-weight: 700; font-size: 0.95rem; text-transform: uppercase;">NSP : 510033130037</p>
                        <p style="margin: 2px 0 0 0; font-family: 'Crimson Pro', serif; font-size: 0.88rem; line-height: 1.3;">
                            Alamat : Jl. Adi Sumarmo RT 001/RW 007 Gawanan, Colomadu, Karanganyar, Jawa Tengah 57175 <br>
                            No Telp : (0271) 7686636
                        </p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="raport-divider mb-4"></div>

        <div class="text-center mb-4">
            <h3 class="raport-title mb-1">LAPORAN HASIL BELAJAR (RAPORT)</h3>
            <p class="raport-subtitle font-weight-bold">
                Semester: {{ $activeTA->semester ?? '-' }} | Tahun Pelajaran: {{ $activeTA->tahunajaran ?? '-' }}
            </p>
        </div>

        {{-- Biodata Section --}}
        <div class="biodata-grid mb-4">
            <div class="row">
                <div class="col-7">
                    <table class="info-table w-100">
                        <tr>
                            <td width="160">Nama Peserta Didik</td>
                            <td width="10">:</td>
                            <td class="font-weight-bold text-uppercase">{{ $siswa->nama }}</td>
                        </tr>
                        <tr>
                            <td>Nomor Induk / NISN</td>
                            <td>:</td>
                            <td>{{ $siswa->nis ?? '-' }} / {{ $siswa->nisn ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Kelas</td>
                            <td>:</td>
                            <td>{{ $siswa->kelas->nama_kelas ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-5 pl-5">
                    <table class="info-table w-100">
                        <tr>
                            <td width="140">Semester</td>
                            <td width="10">:</td>
                            <td>{{ $activeTA->semester ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Tahun Pelajaran</td>
                            <td>:</td>
                            <td>{{ $activeTA->tahunajaran ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- Main Grades Table --}}
        <div class="grades-section mb-5">
            <table class="table table-bordered raport-table text-center mb-0">
                <thead>
                    <tr style="height: 40px;">
                        <th width="40">No</th>
                        <th class="text-left" width="220">Mata Pelajaran</th>
                        <th width="60">KKM</th>
                        <th width="60">Nilai</th>
                        <th width="80">Rerata Kelas</th>
                        <th class="text-left">Deskripsi Kemajuan Belajar</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        $globalNo = 1; 
                        $categoryLetters = ['A', 'B', 'C', 'D', 'E'];
                        $catIdx = 0;
                    @endphp
                    @forelse($rekapGrouped as $catName => $items)
                    <tr class="category-row">
                        <td colspan="6" class="text-left font-weight-bold py-2 px-3">
                            {{ $categoryLetters[$catIdx++] }}. Mata Pelajaran {{ $catName }}
                        </td>
                    </tr>
                        @foreach($items as $item)
                        <tr>
                            <td class="align-middle">{{ $globalNo++ }}.</td>
                            <td class="text-left align-middle py-2">{{ $item['nama'] }}</td>
                            <td class="align-middle">{{ $item['kkm'] }}</td>
                            <td class="align-middle font-weight-bold">{{ round($item['final']) }}</td>
                            <td class="align-middle">{{ round($item['rerata_kelas']) }}</td>
                            <td class="text-left small align-middle p-2 deskripsi-cell">
                                @if($item['final'] >= 85)
                                    Terlampaui
                                @elseif($item['final'] >= 75)
                                    Tercapai
                                @else
                                    Belum Tercapai
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    @empty
                    <tr>
                        <td colspan="6" class="py-4 text-muted font-italic">Belum ada data nilai yang diinput.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="row">
            {{-- Attendance --}}
            <div class="col-6 pr-4">
                <h5 class="section-title mb-3">B. KETIDAKHADIRAN</h5>
                <table class="table table-bordered attendance-table">
                    <tr>
                        <td width="200" class="py-2 px-3 bg-light">Sakit</td>
                        <td class="text-center py-2 px-3">{{ $attendance['Sakit'] }} Hari</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3 bg-light">Izin</td>
                        <td class="text-center py-2 px-3">{{ $attendance['Izin'] }} Hari</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3 bg-light">Tanpa Keterangan</td>
                        <td class="text-center py-2 px-3">{{ $attendance['Alpha'] }} Hari</td>
                    </tr>
                </table>
            </div>

            {{-- Tahfidz Al-Qur'an --}}
            <div class="col-6">
                <h5 class="section-title mb-3">C. CAPAIAN TAHFIDZ</h5>
                <table class="table table-bordered extracurricular-table text-center">
                    <thead class="bg-light">
                        <tr>
                            <th width="40">No</th>
                            <th>Materi / Surat</th>
                            <th width="100">Capaian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tahfidz->take(3) as $t)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="text-left">{{ $t->surat_awal }} - {{ $t->surat_akhir }}</td>
                            <td>{{ $t->nilai }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-muted small font-italic py-1">— Data tahfidz nihil —</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- Teacher's Note --}}
        <div class="notes-section mt-4 mb-5">
            <h5 class="section-title mb-3">D. SARAN / NASIHAT WALI KELAS</h5>
            <div class="note-box p-3">
                <p class="mb-0 text-muted" style="min-height: 40px;">
                    @if(isset($catatan) && $catatan->catatan)
                        {{ $catatan->catatan }}
                    @else
                        Terus pertahankan semangat belajarnya dan tingkatkan kedisiplinan dalam beribadah.
                    @endif
                </p>
            </div>
        </div>

        {{-- Signatures --}}
        <div class="signature-section mt-5">
            <div class="row">
                <div class="col-4 text-center">
                    <p class="mb-5">Orang Tua / Wali Santri,</p>
                    <div class="signature-space"></div>
                    <p class="font-weight-bold mb-0">( ........................................ )</p>
                </div>
                <div class="col-4"></div>
                <div class="col-4 text-center">
                    <p class="mb-0">Surakarta, {{ date('d F Y') }}</p>
                    <p class="mb-5">Wali Kelas,</p>
                    <div class="signature-space"></div>
                    <p class="font-weight-bold mb-0 text-uppercase" style="text-decoration: underline;">{{ auth()->user()->name }}</p>
                    <p class="small text-muted">NIP. ........................................</p>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-12 text-center">
                    <p class="mb-5">Mengetahui,<br>Kepala Pesantren Al-Mahir</p>
                    <div class="signature-space"></div>
                    <p class="font-weight-bold mb-0 text-uppercase" style="text-decoration: underline;">Ustadz H. Ahmad Muzakki, M.A.</p>
                    <p class="small text-muted">NIY. 2025.001.002</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Crimson+Pro:ital,wght@0,400;0,700;1,400&family=Inter:wght@400;700;900&display=swap');
    
    .raport-sheet {
        font-family: 'Inter', sans-serif;
        color: #000;
        background-color: #fff;
        padding: 20mm 15mm;
        width: 210mm;
        min-height: 297mm;
        border: 1px solid #ddd;
        position: relative;
    }

    .raport-header {
        border-bottom: 4px double #000;
        padding-bottom: 5px;
        margin-bottom: 12px;
    }

    .raport-divider {
        border-bottom: 2px solid #000;
        margin-top: -10px;
    }

    .raport-title {
        font-weight: 800;
        text-decoration: underline;
        letter-spacing: 0.5px;
        font-size: 1.2rem;
    }

    .info-table td {
        padding: 2px 0;
        font-size: 0.9rem;
    }

    .section-title {
        font-weight: 700;
        display: inline-block;
        margin-bottom: 5px;
        font-size: 0.95rem;
    }

    /* Table Styling matches the image exactly */
    .raport-table {
        border: 2px solid #000 !important;
        font-size: 0.9rem;
        border-collapse: collapse !important;
        width: 100%;
    }

    .raport-table th, .raport-table td {
        border: 1px solid #000 !important;
        padding: 6px 8px !important;
    }

    .raport-table thead th {
        background-color: #ffffff !important;
        font-weight: 700;
        color: #000;
    }

    .category-row {
        background-color: #ffffff !important;
        font-weight: 700;
    }

    .attendance-table, .extracurricular-table {
        border: 1.5px solid #000 !important;
        font-size: 0.85rem;
    }

    .attendance-table td, .extracurricular-table th, .extracurricular-table td {
        border: 1px solid #000 !important;
        padding: 4px 8px !important;
    }

    .note-box {
        border: 1.5px solid #000;
        padding: 10px;
        min-height: 50px;
    }

    .signature-space {
        height: 50px;
    }

    @media print {
        @page {
            size: A4;
            margin: 0;
        }
        body {
            margin: 0;
            background: #fff;
            -webkit-print-color-adjust: exact;
        }
        .no-print { display: none !important; }
        .container-fluid { padding: 0 !important; width: 100% !important; }
        .raport-sheet {
            border: none !important;
            box-shadow: none !important;
            padding: 10mm 15mm !important;
            width: 100% !important;
            margin: 0 !important;
        }
    }
</style>
@endsection
