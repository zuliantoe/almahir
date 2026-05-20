@extends('layouts.app')

@section('title', $title)

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0" style="font-weight: 700;">{{ $title }}</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Penghuni</li>
        </ol>
    </div>
</div>

<style>
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
    .badge-soft {
        font-weight: 500;
        padding: 4px 8px;
        border-radius: 6px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <x-card title="Daftar Penghuni Kamar" icon="fas fa-users">

                <div class="card-body border-bottom bg-light py-3 px-4 mb-4" style="border-radius: 8px;">
                    <form action="{{ route('siswa.asrama.penghuni.index') }}" method="GET" class="row align-items-center">
                        {{-- DROPDOWN FILTER KAMAR --}}
                        <div class="col-md-4 mb-2 mb-md-0">
                            <label class="small font-weight-bold text-muted uppercase mb-1">Filter Kamar</label>
                            <select name="kamar_id" class="form-control select2" data-placeholder="-- Semua Kamar --">
                                <option value="">-- Semua Kamar --</option>
                                @foreach($kamar as $k)
                                    <option value="{{ $k->id }}" {{ request('kamar_id') == $k->id ? 'selected' : '' }}>
                                        {{ $k->nama_kamar }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- TEXT INPUT CARI SANTRI --}}
                        <div class="col-md-5 mb-2 mb-md-0">
                            <label class="small font-weight-bold text-muted uppercase mb-1">Pencarian Santri</label>
                            <input type="text" name="search" class="form-control" placeholder="Cari nama atau NIS santri..." value="{{ request('search') }}">
                        </div>

                        {{-- TOMBOL FILTER & RESET --}}
                        <div class="col-md-3 mt-md-4 d-flex" style="gap: 8px;">
                            <button type="submit" class="btn btn-primary flex-grow-1 shadow-sm font-weight-bold" style="border-radius: 8px;">
                                Filter
                            </button>
                            @if(request()->filled('kamar_id') || request()->filled('search'))
                                <a href="{{ route('siswa.asrama.penghuni.index') }}" class="btn btn-outline-secondary shadow-sm" title="Reset Filter" style="border-radius: 8px;">
                                    <i class="fas fa-sync-alt"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-compact mb-0">
                        <thead>
                            <tr>
                                <th width="50" class="text-center">No</th>
                                <th>Nama Lengkap & NIS</th>
                                <th width="150">Kamar</th>
                                <th width="150">Masa Tinggal</th>
                                <th>Keterangan</th>
                                <th width="100" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($penghuni as $item)
                            <tr>
                                <td class="text-center text-muted">{{ $loop->iteration + ($penghuni->currentPage() - 1) * $penghuni->perPage() }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="mr-2">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($item->siswa->nama ?? 'S') }}&background=random&size=32" class="img-circle" alt="User">
                                        </div>
                                        <div>
                                            <div style="font-weight: 600; color: #2d3436;">{{ $item->siswa->nama ?? '-' }}</div>
                                            <small class="text-muted">{{ $item->siswa->nis ?? 'NIS belum ada' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($item->tanggal_keluar && \Carbon\Carbon::parse($item->tanggal_keluar)->lte(now()))
                                        <span class="badge badge-secondary badge-soft">
                                            <i class="fas fa-door-closed mr-1"></i> Belum dapat kamar
                                        </span>
                                    @else
                                        <span class="badge badge-info badge-soft">
                                            <i class="fas fa-door-open mr-1"></i> {{ $item->kamar->nama_kamar ?? '-' }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div style="font-size: 0.85rem;">
                                        <i class="far fa-calendar-check text-success mr-1"></i> {{ $item->tanggal_masuk ? \Carbon\Carbon::parse($item->tanggal_masuk)->format('d M Y') : '-' }}
                                        <br>
                                        <i class="far fa-calendar-times text-muted mr-1"></i> <span class="text-muted">{{ $item->tanggal_keluar ? \Carbon\Carbon::parse($item->tanggal_keluar)->format('d M Y') : 'Sekarang' }}</span>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $ket = $item->keterangan ?? '-';
                                        $isHistory = str_contains($ket, 'Pindahan') || str_contains($ket, 'Tukar');
                                    @endphp
                                    <span class="{{ $isHistory ? 'text-primary font-weight-bold' : 'text-muted' }} italic" style="font-size: 0.85rem;">
                                        @if($isHistory) <i class="fas fa-exchange-alt mr-1 small"></i> @endif
                                        {{ Str::limit($ket, 60) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-xs-custom btn-info btn-detail-penghuni" 
                                            data-nama="{{ $item->siswa->nama ?? '-' }}"
                                            data-nis="{{ $item->siswa->nis ?? '-' }}"
                                            data-email="{{ $item->siswa->email ?? '-' }}"
                                            data-ttl="{{ ($item->siswa->tempat_lahir ?? '-') . ', ' . ($item->siswa->tanggal_lahir ? \Carbon\Carbon::parse($item->siswa->tanggal_lahir)->format('d M Y') : '-') }}"
                                            data-jk="{{ $item->siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}"
                                            data-alamat="{{ $item->siswa->alamat ?? '-' }}"
                                            data-telepon="{{ $item->siswa->telepon ?? '-' }}"
                                            data-tahun_masuk="{{ $item->siswa->tahun_masuk ?? '-' }}"
                                            data-kamar="{{ $item->kamar->nama_kamar ?? '-' }}"
                                            data-jabatan="{{ $item->jabatan ?? 'Anggota' }}"
                                            data-masuk="{{ $item->tanggal_masuk ? \Carbon\Carbon::parse($item->tanggal_masuk)->format('d M Y') : '-' }}"
                                            data-keterangan="{{ $item->keterangan ?? '-' }}"
                                            data-avatar="https://ui-avatars.com/api/?name={{ urlencode($item->siswa->nama ?? 'S') }}&background=random&size=128"
                                            title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-2x mb-2"></i><br>Tidak ditemukan data penghuni
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($penghuni->hasPages())
                <div class="d-flex justify-content-end mt-3">
                    {{ $penghuni->links() }}
                </div>
                @endif

                <x-slot name="footer">
                    <small class="text-muted">Total data: {{ $penghuni->total() }}</small>
                </x-slot>
            </x-card>
        </div>
    </div>
</div>

{{-- MODAL DETAIL PENGHUNI --}}
<div class="modal fade" id="modalDetailPenghuni" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header bg-info text-white border-0 py-3">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-user-circle mr-2"></i> Detail Profil & Data Akademik Santri</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="row no-gutters">
                    {{-- Sisi Kiri: Foto & Status Utama --}}
                    <div class="col-md-3 bg-light d-flex flex-column align-items-center p-4 border-right">
                        <div class="position-relative mb-3">
                            <img id="detail_avatar" src="" class="img-circle elevation-2" style="width: 160px; height: 160px; object-fit: cover; border: 5px solid #fff;">
                        </div>
                        <h5 id="detail_nama" class="font-weight-bold mb-1 text-center text-primary"></h5>
                        <span id="detail_nis" class="badge badge-secondary px-3 py-2 mb-3"></span>
                        <div id="detail_badge_jabatan"></div>
                        
                        <div class="mt-4 w-100">
                            <div class="info-box bg-white shadow-none border mb-2">
                                <span class="info-box-icon bg-info"><i class="fas fa-door-open"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text small">Kamar</span>
                                    <span id="detail_kamar_side" class="info-box-number small"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Sisi Kanan: Detail Informasi --}}
                    <div class="col-md-9 p-4">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-info font-weight-bold mb-3 border-bottom pb-2"><i class="fas fa-user mr-2"></i> BIODATA DIRI</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="120" class="text-muted">Tempat, Tgl Lahir</th>
                                        <td width="10">:</td>
                                        <td id="detail_ttl"></td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Jenis Kelamin</th>
                                        <td>:</td>
                                        <td id="detail_jk"></td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Email</th>
                                        <td>:</td>
                                        <td id="detail_email"></td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Telepon/WA</th>
                                        <td>:</td>
                                        <td id="detail_telepon"></td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Alamat</th>
                                        <td>:</td>
                                        <td id="detail_alamat" class="small"></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-info font-weight-bold mb-3 border-bottom pb-2"><i class="fas fa-graduation-cap mr-2"></i> DATA AKADEMIK & ASRAMA</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="120" class="text-muted">Tahun Masuk</th>
                                        <td width="10">:</td>
                                        <td id="detail_tahun_masuk"></td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Status Kamar</th>
                                        <td>:</td>
                                        <td id="detail_jabatan_text"></td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Tgl Masuk Kamar</th>
                                        <td>:</td>
                                        <td id="detail_masuk"></td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Keterangan</th>
                                        <td>:</td>
                                        <td id="detail_keterangan" class="italic small"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary shadow-sm" data-dismiss="modal">Tutup Profil</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').each(function() {
            $(this).select2({
                theme: 'bootstrap4',
                placeholder: $(this).data('placeholder') || "-- Pilih --",
                allowClear: true
            });
        });

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
