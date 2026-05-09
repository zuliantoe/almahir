@extends('layouts.app')

@section('title', $title)

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0">{{ $title }}</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('manajemenasetdanasrama.index') }}">Manajemen Aset & Asrama</a></li>
            <li class="breadcrumb-item"><a href="{{ route('manajemenasetdanasrama.kamar.index') }}">Data Kamar</a></li>
            <li class="breadcrumb-item active">Detail Kamar</li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    {{-- Header Khusus Cetak (Hanya Muncul saat Print) --}}
    <div class="d-none d-print-block text-center mb-4">
        <h4>DAFTAR PENGHUNI ASRAMA</h4>
        <h2 style="font-weight: 700; text-transform: uppercase;">KAMAR: {{ $kamar->nama_kamar }}</h2>
        <hr style="border: 2px solid #000;">
    </div>

    {{-- Info Box Layout (Modern Dashboard) --}}
    <div class="row no-print">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-door-open"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Nama Kamar</span>
                    <span class="info-box-number">{{ $kamar->nama_kamar }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Kapasitas Total</span>
                    <span class="info-box-number">{{ $kamar->kapasitas }} <small>Slot</small></span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-user-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Terisi</span>
                    <span class="info-box-number">{{ $kamar->terisi }} <small>Siswa</small></span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-user-plus"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Sisa Slot</span>
                    <span class="info-box-number">{{ $kamar->sisa }} <small>Slot</small></span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            {{-- Data Penghuni --}}
            <x-card title="Daftar Penghuni Aktif" icon="fas fa-users" class="card-outline card-success">
                <x-slot name="tools">
                    <div class="no-print">
                        <button type="button" data-toggle="modal" data-target="#modalCetakKamar" class="btn btn-sm btn-info mr-1 shadow-sm">
                            <i class="fas fa-print mr-1"></i> Cetak Laporan
                        </button>
                        <a href="{{ route('manajemenasetdanasrama.penghuni.assign-multiple', $kamar->id) }}" class="btn btn-sm btn-primary mr-1 shadow-sm">
                            <i class="fas fa-user-plus mr-1"></i> Tambah Penghuni
                        </a>
                        <a href="{{ route('manajemenasetdanasrama.kamar.index') }}" class="btn btn-sm btn-secondary shadow-sm">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                    </div>
                </x-slot>

                <div class="table-responsive">
                    <table class="table table-hover table-compact mb-0">
                        <thead>
                            <tr>
                                <th width="50" class="text-center">No</th>
                                <th>Nama Lengkap & NIS</th>
                                <th width="150">Jabatan</th>
                                <th width="150">Tanggal Masuk</th>
                                <th>Keterangan</th>
                                <th width="100" class="text-center no-print">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($penghuniAktif as $item)
                            <tr>
                                <td class="text-center text-muted">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="mr-2">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($item->siswa->nama ?? 'S') }}&background=random&size=28" class="img-circle shadow-sm" alt="User">
                                        </div>
                                        <div>
                                            <div style="font-weight: 600; color: #2d3436;">{{ $item->siswa->nama ?? '-' }}</div>
                                            <small class="text-muted">{{ $item->siswa->nis ?? '-' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $badgeColor = match($item->jabatan) {
                                            'Ketua Kamar' => 'badge-danger',
                                            'Wakil Ketua Kamar' => 'badge-warning',
                                            default => 'badge-info'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeColor }}" style="font-weight: 500; padding: 5px 10px; border-radius: 6px;">{{ $item->jabatan ?? 'Anggota' }}</span>
                                </td>
                                <td>{{ $item->tanggal_masuk ? $item->tanggal_masuk->format('d M Y') : '-' }}</td>
                                <td>
                                    @php
                                        $ket = $item->keterangan ?? '-';
                                        $isHistory = str_contains($ket, 'Pindahan') || str_contains($ket, 'Tukar');
                                    @endphp
                                    <small class="{{ $isHistory ? 'text-primary font-weight-bold' : 'text-muted' }} italic text-truncate" style="max-width: 200px; display: inline-block;">
                                        @if($isHistory) <i class="fas fa-exchange-alt mr-1 small"></i> @endif
                                        {{ $ket }}
                                    </small>
                                </td>
                                <td class="text-center no-print">
                                    <div class="d-flex justify-content-center" style="gap: 5px;">
                                        <button type="button" class="btn btn-xs-custom btn-info btn-detail-penghuni" 
                                                data-id="{{ $item->id }}"
                                                data-nama="{{ $item->siswa->nama ?? '-' }}"
                                                data-nis="{{ $item->siswa->nis ?? '-' }}"
                                                data-email="{{ $item->siswa->email ?? '-' }}"
                                                data-ttl="{{ ($item->siswa->tempat_lahir ?? '-') . ', ' . ($item->siswa->tanggal_lahir ? $item->siswa->tanggal_lahir->format('d M Y') : '-') }}"
                                                data-jk="{{ $item->siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}"
                                                data-alamat="{{ $item->siswa->alamat ?? '-' }}"
                                                data-telepon="{{ $item->siswa->telepon ?? '-' }}"
                                                data-tahun_masuk="{{ $item->siswa->tahun_masuk ?? '-' }}"
                                                data-kamar="{{ $kamar->nama_kamar }}"
                                                data-jabatan="{{ $item->jabatan ?? 'Anggota' }}"
                                                data-masuk="{{ $item->tanggal_masuk ? $item->tanggal_masuk->format('d M Y') : '-' }}"
                                                data-keterangan="{{ $item->keterangan ?? '-' }}"
                                                data-avatar="https://ui-avatars.com/api/?name={{ urlencode($item->siswa->nama ?? 'S') }}&background=random&size=128"
                                                title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="{{ route('manajemenasetdanasrama.penghuni.edit', $item->id) }}" class="btn btn-xs-custom btn-warning" title="Edit">
                                            <i class="fas fa-edit text-white"></i>
                                        </a>
                                        <form action="{{ route('manajemenasetdanasrama.penghuni.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin mengeluarkan santri ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs-custom btn-danger" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <img src="https://illustrations.popsy.co/gray/empty-inbox.svg" alt="Empty" style="width: 100px; opacity: 0.5;">
                                    <p class="mt-2 text-muted">Belum ada penghuni aktif di kamar ini.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>

            {{-- Riwayat --}}
            <x-card title="Riwayat Penghuni Sebelumnya" icon="fas fa-history" class="card-outline card-secondary collapsed-card no-print mt-3">
                <x-slot name="tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-plus"></i>
                    </button>
                </x-slot>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th width="50" class="text-center">No</th>
                                <th>Nama Siswa</th>
                                <th>Masuk</th>
                                <th>Keluar</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($riwayatPenghuni as $item)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td><strong>{{ $item->siswa->nama ?? '-' }}</strong></td>
                                <td>{{ $item->tanggal_masuk ? $item->tanggal_masuk->format('d/m/Y') : '-' }}</td>
                                <td>{{ $item->tanggal_keluar ? $item->tanggal_keluar->format('d/m/Y') : '-' }}</td>
                                <td><small class="text-muted">{{ $item->keterangan }}</small></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-3 text-muted small italic">Tidak ada riwayat penghuni sebelumnya.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>
</div>

{{-- MODAL DETAIL PENGHUNI --}}
<div class="modal fade" id="modalDetailPenghuni" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-info text-white border-0 py-3">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-id-card mr-2"></i> Profil Lengkap Santri</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="row no-gutters">
                    {{-- Sisi Kiri: Foto & Status Utama --}}
                    <div class="col-md-4 bg-light d-flex flex-column align-items-center p-5 border-right">
                        <div class="position-relative mb-4">
                            <div class="rounded-circle shadow-sm p-1 bg-white">
                                <img id="detail_avatar" src="" class="rounded-circle" style="width: 180px; height: 180px; object-fit: cover;">
                            </div>
                            <div id="detail_badge_jabatan" class="position-absolute" style="bottom: 0; width: 100%; text-align: center;"></div>
                        </div>
                        <h4 id="detail_nama" class="font-weight-bold mb-1 text-center text-primary"></h4>
                        <p id="detail_nis" class="text-muted mb-4"></p>
                        
                        <div class="card bg-white border-0 shadow-sm w-100 mb-2" style="border-radius: 12px;">
                            <div class="card-body p-3 d-flex align-items-center">
                                <div class="bg-info-soft p-2 rounded-circle mr-3">
                                    <i class="fas fa-door-open text-info"></i>
                                </div>
                                <div>
                                    <small class="text-muted text-uppercase d-block font-weight-bold" style="font-size: 0.65rem;">Kamar Saat Ini</small>
                                    <span id="detail_kamar_side" class="font-weight-bold text-dark"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Sisi Kanan: Detail Informasi --}}
                    <div class="col-md-8 p-5">
                        <div class="row mb-5">
                            <div class="col-12 mb-4">
                                <h6 class="text-uppercase font-weight-bold text-info" style="letter-spacing: 1px;">
                                    <i class="fas fa-user-circle mr-2"></i> Data Personal
                                </h6>
                                <hr class="mt-2 mb-4">
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted font-weight-bold mb-1">TEMPAT, TGL LAHIR</label>
                                <p id="detail_ttl" class="font-weight-bold text-dark"></p>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted font-weight-bold mb-1">JENIS KELAMIN</label>
                                <p id="detail_jk" class="font-weight-bold text-dark"></p>
                            </div>
                            <div class="col-md-6 mt-3">
                                <label class="small text-muted font-weight-bold mb-1">EMAIL AKADEMIK</label>
                                <p id="detail_email" class="font-weight-bold text-dark"></p>
                            </div>
                            <div class="col-md-6 mt-3">
                                <label class="small text-muted font-weight-bold mb-1">NOMOR TELEPON/WA</label>
                                <p id="detail_telepon" class="font-weight-bold text-dark text-success"></p>
                            </div>
                            <div class="col-12 mt-3">
                                <label class="small text-muted font-weight-bold mb-1">ALAMAT DOMISILI</label>
                                <p id="detail_alamat" class="text-dark mb-0"></p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-4">
                                <h6 class="text-uppercase font-weight-bold text-info" style="letter-spacing: 1px;">
                                    <i class="fas fa-university mr-2"></i> Status Keasramaan
                                </h6>
                                <hr class="mt-2 mb-4">
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted font-weight-bold mb-1">TAHUN MASUK</label>
                                <p id="detail_tahun_masuk" class="font-weight-bold text-dark"></p>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted font-weight-bold mb-1">TANGGAL MASUK KAMAR</label>
                                <p id="detail_masuk" class="font-weight-bold text-dark text-info"></p>
                            </div>
                            <div class="col-12 mt-3">
                                <label class="small text-muted font-weight-bold mb-1">CATATAN/KETERANGAN</label>
                                <div class="bg-light p-3 rounded" style="border-left: 4px solid #dee2e6;">
                                    <p id="detail_keterangan" class="mb-0 italic text-muted"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 py-3 px-5">
                <button type="button" class="btn btn-secondary px-4 shadow-sm font-weight-bold" style="border-radius: 8px;" data-dismiss="modal">Tutup Profil</button>
            </div>
        </div>
    </div>
</div>
{{-- MODAL CETAK KAMAR --}}
<div class="modal fade" id="modalCetakKamar" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header bg-info text-white border-0 py-3">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-print mr-2"></i> Pengaturan Cetak Kamar</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formCetakKamar" action="{{ route('manajemenasetdanasrama.kamar.print', $kamar->id) }}" method="GET" target="_blank">
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 shadow-none mb-4" style="background: #e8f4f8; color: #1a4b63;">
                        <i class="fas fa-info-circle mr-2"></i> Masukkan nama penandatangan untuk dicantumkan di bagian bawah laporan.
                    </div>
                    <div class="form-group mb-3">
                        <label class="small font-weight-bold text-muted text-uppercase">Nama Musyrif Kamar</label>
                        <div class="input-group shadow-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-right-0"><i class="fas fa-user-tie text-info"></i></span>
                            </div>
                            <input type="text" name="musyrif" class="form-control border-left-0" placeholder="Contoh: Ustadz Ahmad" required>
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-muted text-uppercase">Nama Kepala Sekolah / Mudir</label>
                        <div class="input-group shadow-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-right-0"><i class="fas fa-user-graduate text-info"></i></span>
                            </div>
                            <input type="text" name="kepsek" class="form-control border-left-0" placeholder="Contoh: Dr. Sulaiman, M.Pd" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 px-4">
                    <button type="button" class="btn btn-link text-muted font-weight-bold" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info px-4 shadow-sm" style="border-radius: 8px;">
                        <i class="fas fa-file-pdf mr-1"></i> Download Laporan PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-xs-custom {
        padding: 1px 6px !important;
        font-size: 0.7rem !important;
        border-radius: 4px !important;
        margin: 0 2px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 24px;
        width: 24px;
    }
    .table-compact td {
        padding: 0.6rem 0.75rem !important;
        font-size: 0.9rem;
    }
    .table-compact thead th {
        padding: 0.75rem !important;
        background-color: #f4f6f9;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }
    @media print {
        .no-print, .main-footer, .main-header, .breadcrumb, .btn, .card-tools {
            display: none !important;
        }
        .content-wrapper {
            background-color: white !important;
            margin-left: 0 !important;
            padding: 0 !important;
            border: none !important;
        }
        .col-md-8 {
            flex: 0 0 100% !important;
            max-width: 100% !important;
        }
        .card {
            box-shadow: none !important;
            border: none !important;
        }
        .card-header {
            display: none !important;
        }
        .card-outline {
            border-top: 3px solid #000 !important;
        }
        body {
            background-color: white !important;
        }
        .badge {
            border: 1px solid #000 !important;
            color: black !important;
            background-color: transparent !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        $('.btn-detail-penghuni').on('click', function() {
            var data = $(this).data();
            var modal = $('#modalDetailPenghuni');
            
            modal.find('#detail_avatar').attr('src', data.avatar);
            modal.find('#detail_nama').text(data.nama);
            modal.find('#detail_nis').text("NIS: " + data.nis);
            modal.find('#detail_kamar_side').text(data.kamar);
            modal.find('#detail_masuk').text(data.masuk);
            modal.find('#detail_keterangan').text(data.keterangan);
            modal.find('#detail_ttl').text(data.ttl);
            modal.find('#detail_jk').text(data.jk);
            modal.find('#detail_email').text(data.email);
            modal.find('#detail_telepon').text(data.telepon);
            modal.find('#detail_alamat').text(data.alamat);
            modal.find('#detail_tahun_masuk').text(data.tahun_masuk);
            modal.find('#detail_jabatan_text').text(data.jabatan);
            
            // Badge Jabatan
            var badgeClass = 'badge-info';
            if (data.jabatan == 'Ketua Kamar') badgeClass = 'badge-danger';
            else if (data.jabatan == 'Wakil Ketua Kamar') badgeClass = 'badge-warning';
            
            modal.find('#detail_badge_jabatan').html('<span class="badge ' + badgeClass + ' px-3 py-2" style="font-size: 0.9rem; border-radius: 6px;">' + data.jabatan + '</span>');
            
            modal.modal('show');
        });
    });
</script>
@endpush
