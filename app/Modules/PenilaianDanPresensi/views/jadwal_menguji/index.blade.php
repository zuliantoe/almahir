@extends('layouts.app')

@section('title', $title)
@section('breadcrumb', 'Penilaian & Presensi / ' . $title)

@section('content')
@push('styles')
<style>
    @media print {
        /* Sembunyikan tombol/aksi saat print */
        .btn, .card-header small { display: none !important; }
        .card-header .d-flex { display: none !important; }
        /* tampilkan judul dan tabel */
        body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .card { box-shadow: none !important; }
    }
    .print-header {
        display: none;
        text-align: center;
        margin-bottom: 18px;
    }
    @media print {
        .print-header { display: block; }
    }
</style>
@endpush

<div class="print-header">
    <div style="font-weight:800; font-size:18px;">Al Mahir</div>
    <div style="font-size:14px; margin-top:4px;">Jadwal Tes Seleksi</div>
    <div style="font-size:12px; margin-top:4px;">{{ date('d-m-Y') }}</div>
</div>

<div class="row">

    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 20px;">
            <div class="card-header bg-white py-4 border-0 d-flex justify-content-between align-items-center" style="border-radius: 20px 20px 0 0;">
                <div>
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="fas fa-calendar-check text-primary mr-2"></i> {{ $title }}</h5>
                    <small class="text-muted">Cetak jadwal seleksi untuk arsip Al Mahir</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-primary font-weight-bold px-3" onclick="window.print()">
                        <i class="fas fa-print mr-1"></i> Print
                    </button>
                    <a href="{{ route('penilaiandanpresensi.index') }}" class="btn btn-sm btn-light text-secondary rounded-pill font-weight-bold px-3">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </div>
            
            <div class="card-body p-0">

                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-top-0 border-bottom-0 pl-4 py-3 text-uppercase text-secondary font-weight-bold" style="font-size: 0.8rem;">No</th>
                                <th class="border-top-0 border-bottom-0 py-3 text-uppercase text-secondary font-weight-bold" style="font-size: 0.8rem;">Calon Santri</th>
                                <th class="border-top-0 border-bottom-0 py-3 text-uppercase text-secondary font-weight-bold" style="font-size: 0.8rem;">Jenis Tes</th>
                                <th class="border-top-0 border-bottom-0 py-3 text-uppercase text-secondary font-weight-bold" style="font-size: 0.8rem;">Tanggal & Waktu</th>
                                <th class="border-top-0 border-bottom-0 py-3 text-uppercase text-secondary font-weight-bold" style="font-size: 0.8rem;">Metode / Lokasi</th>
                              
                        </thead>
                        <tbody>
                            @forelse($jadwalTes as $index => $jadwal)
                            <tr>
                                <td class="pl-4 align-middle">{{ $index + 1 }}</td>
                                <td class="align-middle">
                                    <div class="font-weight-bold text-dark">{{ $jadwal->pendaftaran->nama_lengkap ?? 'Tidak Diketahui' }}</div>
                                    <small class="text-muted">No. Daftar: {{ $jadwal->pendaftaran->no_pendaftaran ?? '-' }}</small>
                                </td>
                                <td class="align-middle">
                                    <span class="badge badge-warning text-dark px-2 py-1 shadow-sm">{{ $jadwal->nama_tes }}</span>
                                </td>
                                <td class="align-middle">
                                    <div class="text-dark font-weight-bold"><i class="far fa-calendar-alt text-primary mr-1"></i> {{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('d M Y') }}</div>
                                    <small class="text-danger"><i class="far fa-clock mr-1"></i> {{ \Carbon\Carbon::parse($jadwal->jam)->format('H:i') }} WIB</small>
                                </td>
                                <td class="align-middle">
                                    @if($jadwal->metode == 'offline')
                                        <div class="text-success font-weight-bold"><i class="fas fa-map-marker-alt mr-1"></i> Offline</div>
                                        <small class="text-muted">{{ $jadwal->lokasi ?: 'Ruang Ujian' }}</small>
                                    @else
                                        <div class="text-info font-weight-bold"><i class="fas fa-video mr-1"></i> Online</div>
                                        <small><a href="{{ $jadwal->link }}" target="_blank" class="text-info text-decoration-underline"><i class="fas fa-external-link-alt mr-1"></i> Buka Link</a></small>
                                    @endif
                                </td>
                                
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-calendar-times fa-3x mb-3 text-light"></i>
                                    <p class="mb-0">Tidak ada jadwal menguji saat ini.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($jadwalTes->count() > 0)
            <div class="card-footer bg-white border-top text-muted small text-center py-3" style="border-radius: 0 0 20px 20px;">
                Menampilkan total {{ $jadwalTes->count() }} jadwal tes seleksi untuk Anda.
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
