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
            <li class="breadcrumb-item active">Jadwal Piket</li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <x-alert type="success" :message="session('success')" dismissible />
    @endif
    @if(session('error'))
        <x-alert type="danger" :message="session('error')" dismissible />
    @endif
    @if(session('warning'))
        <x-alert type="warning" :message="session('warning')" dismissible />
    @endif

    <div class="row">
        <div class="col-md-12">
            <x-card title="Manajemen Jadwal Piket" icon="fas fa-calendar-alt">
                <x-slot name="tools">
                    <button type="button" class="btn btn-sm btn-info mr-1" data-toggle="modal" data-target="#modalAutoGenerate">
                        <i class="fas fa-robot mr-1"></i> Auto-Generate
                    </button>
                    <a href="{{ route('manajemenasetdanasrama.jadwal-piket.print', request()->all()) }}" target="_blank" class="btn btn-sm btn-secondary mr-1">
                        <i class="fas fa-print mr-1"></i> Cetak
                    </a>
                    <button type="button" class="btn btn-sm btn-danger mr-1" onclick="if(confirm('Hapus semua jadwal piket? Data tidak bisa dikembalikan.')) document.getElementById('form-reset-piket').submit();">
                        <i class="fas fa-trash-alt mr-1"></i> Reset Semua
                    </button>
                    <form id="form-reset-piket" action="{{ route('manajemenasetdanasrama.jadwal-piket.reset') }}" method="POST" style="display: none;">@csrf</form>
                    <a href="{{ route('manajemenasetdanasrama.jadwal-piket.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus mr-1"></i> Tambah
                    </a>
                </x-slot>

                <div class="card-body border-bottom bg-light">
                    <form action="{{ route('manajemenasetdanasrama.jadwal-piket.index') }}" method="GET">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group mb-md-0">
                                    <label class="small font-weight-bold">Cari Nama Santri</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control" name="q" placeholder="Ketik nama..." value="{{ request('q') }}">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-md-0">
                                    <label class="small font-weight-bold">Filter Lokasi</label>
                                    <select class="form-control form-control-sm select2" name="lokasi_piket" onchange="this.form.submit()">
                                        <option value="">Semua Lokasi</option>
                                        @foreach($locations as $loc)
                                            <option value="{{ $loc }}" {{ request('lokasi_piket') == $loc ? 'selected' : '' }}>{{ $loc }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-0">
                                    <label class="small font-weight-bold">Rentang Tanggal</label>
                                    <div class="d-flex align-items-center">
                                        <input type="date" class="form-control form-control-sm" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}">
                                        <span class="mx-2">-</span>
                                        <input type="date" class="form-control form-control-sm" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <a href="{{ route('manajemenasetdanasrama.jadwal-piket.index') }}" class="btn btn-sm btn-default btn-block">
                                    <i class="fas fa-undo"></i> Reset Filter
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

<style>
    .masonry-wrapper {
        column-count: 2;
        column-gap: 20px;
    }
    .masonry-item {
        display: inline-block;
        width: 100%;
        margin-bottom: 20px;
        break-inside: avoid;
    }
    @media (max-width: 768px) {
        .masonry-wrapper {
            column-count: 1;
        }
    }
</style>

                <div class="card-body bg-light p-4">
                    @php
                        $groupedByDate = $jadwal->getCollection()->groupBy(function($item) {
                            return \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d');
                        });
                    @endphp

                    @forelse($groupedByDate as $date => $dayItems)
                        <div class="piket-day-group mb-5">
                            {{-- Day Header --}}
                            <div class="bg-dark px-3 py-2 border-left border-warning shadow-sm mb-3 d-flex justify-content-between align-items-center" style="border-width: 5px !important; border-radius: 0 4px 4px 0;">
                                <h5 class="mb-0 font-weight-bold text-white">
                                    <i class="fas fa-calendar-check mr-2 text-warning"></i> {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                                </h5>
                                <div class="d-flex align-items-center">
                                    <span class="badge badge-warning font-weight-bold px-3 py-1 mr-2">{{ $dayItems->count() }} Santri</span>
                                    <a href="{{ route('manajemenasetdanasrama.jadwal-piket.print', ['tanggal_mulai' => $date, 'tanggal_selesai' => $date]) }}" target="_blank" class="btn btn-xs btn-outline-light">
                                        <i class="fas fa-print"></i> Cetak Hari Ini
                                    </a>
                                </div>
                            </div>
                            
                            {{-- Locations Grid (Masonry) --}}
                            <div class="masonry-wrapper">
                                @php
                                    $groupedByLocation = $dayItems->groupBy('lokasi_piket');
                                @endphp

                                @foreach($groupedByLocation as $location => $items)
                                    <div class="masonry-item">
                                        <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                                            <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center border-bottom">
                                                <h6 class="mb-0 font-weight-bold text-primary">
                                                    <i class="fas fa-map-marker-alt mr-2 text-info"></i> {{ $location ?: 'Umum / Lainnya' }}
                                                </h6>
                                                <span class="badge badge-pill badge-light border text-muted" style="font-size: 11px;">
                                                    {{ $items->count() }} Orang
                                                </span>
                                            </div>
                                            <div class="card-body p-0">
                                                <table class="table table-sm table-hover mb-0">
                                                    <thead class="bg-gray-light text-muted small uppercase">
                                                        <tr>
                                                            <th width="80" class="pl-3 py-2 border-0">Shift</th>
                                                            <th class="py-2 border-0">Nama Santri</th>
                                                            <th width="100" class="text-center pr-3 py-2 border-0">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($items as $item)
                                                        <tr>
                                                            <td class="align-middle pl-3 py-2">
                                                                <span class="badge badge-outline-secondary border text-capitalize px-2 py-1" style="font-size: 10px; font-weight: 500;">
                                                                    {{ $item->shift }}
                                                                </span>
                                                            </td>
                                                            <td class="align-middle py-2 font-weight-bold text-dark" style="font-size: 13px;">
                                                                {{ $item->siswa->nama ?? '-' }}
                                                                @if($item->status == 'sudah')
                                                                    <span class="ml-1 text-success"><i class="fas fa-check-circle" style="font-size: 10px;"></i></span>
                                                                @endif
                                                            </td>
                                                            <td class="text-center align-middle pr-3 py-2">
                                                                <div class="btn-group">
                                                                    @if($item->status == 'belum')
                                                                    <form action="{{ route('manajemenasetdanasrama.jadwal-piket.selesai', $item->id) }}" method="POST" class="d-inline">
                                                                        @csrf
                                                                        <button type="submit" class="btn btn-xs-custom btn-success" title="Tandai Selesai">
                                                                            <i class="fas fa-check"></i>
                                                                        </button>
                                                                    </form>
                                                                    @endif
                                                                    <a href="{{ route('manajemenasetdanasrama.jadwal-piket.edit', $item->id) }}" class="btn btn-xs-custom btn-warning" title="Edit">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                    <button type="button" class="btn btn-xs-custom btn-danger" data-toggle="modal" data-target="#modalHapus" data-id="{{ $item->id }}" data-nama="{{ $item->siswa->nama ?? '' }}">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted bg-white shadow-sm" style="border-radius: 15px;">
                            <i class="fas fa-search fa-3x mb-3 opacity-25"></i>
                            <h5 class="font-weight-bold">Tidak ditemukan!</h5>
                            <p>Data jadwal piket tidak ditemukan untuk filter ini.</p>
                            <a href="{{ route('manajemenasetdanasrama.jadwal-piket.index') }}" class="btn btn-primary btn-sm rounded-pill px-4">
                                Lihat Semua Jadwal
                            </a>
                        </div>
                    @endforelse

                    @if($jadwal->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-4 bg-white p-3 shadow-sm" style="border-radius: 12px;">
                            <div class="small text-muted">
                                Menampilkan <b>{{ $jadwal->firstItem() }}</b> - <b>{{ $jadwal->lastItem() }}</b> dari <b>{{ $jadwal->total() }}</b> jadwal.
                            </div>
                            <div>
                                {{ $jadwal->links() }}
                            </div>
                        </div>
                    @endif
                </div>

                <x-slot name="footer">
                    <small class="text-muted">Total data: {{ $jadwal->total() }}</small>
                </x-slot>
            </x-card>
        </div>
    </div>
</div>

{{-- MODAL HAPUS --}}
<div class="modal fade" id="modalHapus" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formHapus" action="" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white">Hapus Jadwal Piket</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus jadwal piket <strong id="hapus_nama"></strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL AUTO GENERATE WIZARD --}}
<div class="modal fade" id="modalAutoGenerate" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <form id="formSmartGenerate" action="{{ route('manajemenasetdanasrama.jadwal-piket.auto-generate') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white border-0 py-3">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-robot mr-2"></i> Smart Picket Generator
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                {{-- STEP 1: Dasar & Jumlah Lokasi --}}
                <div id="step-1" class="picket-step">
                    <div class="modal-body p-4">
                        <div class="alert alert-info border-0 shadow-sm mb-4" style="border-radius: 10px; background: #e7f3ff; color: #004085;">
                            <i class="fas fa-info-circle mr-2"></i> <strong>Langkah 1:</strong> Tentukan rentang waktu dan berapa banyak lokasi yang akan dipiketi.
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="small font-weight-bold text-uppercase text-muted mb-1">Dari Tanggal <span class="text-danger">*</span></label>
                                    <div class="input-group shadow-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-calendar-alt text-primary"></i></span>
                                        </div>
                                        <input type="date" class="form-control border-left-0" name="tanggal_mulai" value="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="small font-weight-bold text-uppercase text-muted mb-1">Sampai Tanggal <span class="text-danger">*</span></label>
                                    <div class="input-group shadow-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-calendar-check text-primary"></i></span>
                                        </div>
                                        <input type="date" class="form-control border-left-0" name="tanggal_selesai" value="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-4">
                                    <label class="small font-weight-bold text-uppercase text-muted mb-1">Pilih Waktu (Shift) <span class="text-danger">*</span></label>
                                    <select class="form-control shadow-sm" name="shift" required>
                                        <option value="pagi">Pagi</option>
                                        <option value="sore">Sore</option>
                                        <option value="malam">Malam</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mt-2">
                            <label class="small font-weight-bold text-uppercase text-primary mb-1">Berapa Banyak Tempat Piket? <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg shadow-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white"><i class="fas fa-map-marker-alt text-danger"></i></span>
                                </div>
                                <input type="number" id="input_jumlah_lokasi" class="form-control font-weight-bold" placeholder="Contoh: 3" min="1" max="10" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 py-3 px-4">
                        <button type="button" class="btn btn-link text-muted font-weight-bold mr-2" data-dismiss="modal">Batal</button>
                        <button type="button" id="btnNextStep" class="btn btn-primary px-4 shadow-sm font-weight-bold" style="border-radius: 8px;">
                            Lanjut Atur Lokasi <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>

                {{-- STEP 2: Detail Lokasi & Kuota --}}
                <div id="step-2" class="picket-step d-none">
                    <div class="modal-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="font-weight-bold text-primary mb-0"><i class="fas fa-list-ul mr-2"></i> Pengaturan Lokasi & Kuota</h6>
                            <div class="bg-dark text-white px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.85rem;">
                                <i class="fas fa-users mr-1"></i> Sisa Santri: <span id="label_sisa_santri" class="font-weight-bold text-warning">{{ $totalSantri }}</span> / {{ $totalSantri }}
                            </div>
                        </div>
                        
                        <div id="location_inputs_container" style="max-height: 350px; overflow-y: auto; padding-right: 10px;">
                            {{-- Input Dinamis Muncul Disini --}}
                        </div>

                        <div class="alert alert-warning border-0 shadow-sm mt-3 mb-0" id="alert_over_limit" style="display: none; border-radius: 10px;">
                            <i class="fas fa-exclamation-triangle mr-2"></i> <strong>Peringatan!</strong> Jumlah santri melebihi kapasitas total yang tersedia.
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 py-3 px-4">
                        <button type="button" id="btnPrevStep" class="btn btn-outline-secondary px-4 mr-auto" style="border-radius: 8px;"><i class="fas fa-arrow-left mr-2"></i> Kembali</button>
                        <button type="submit" id="btnGenerateNow" class="btn btn-success px-4 font-weight-bold shadow-sm" style="border-radius: 8px;" disabled><i class="fas fa-rocket mr-2"></i> Generate Sekarang</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .picket-step {
        transition: all 0.3s ease-in-out;
    }
    .location-row {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 15px;
        border-left: 5px solid #007bff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    #location_inputs_container::-webkit-scrollbar {
        width: 5px;
    }
    #location_inputs_container::-webkit-scrollbar-thumb {
        background: #ddd;
        border-radius: 10px;
    }
    .btn-xs-custom {
        padding: 0.15rem 0.35rem;
        font-size: 0.75rem;
        line-height: 1.2;
        border-radius: 0.2rem;
    }
    .bg-gray-light {
        background-color: #f4f6f9;
    }
</style>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const totalSantri = {{ $totalSantri }};
        
        // Modal Hapus logic
        $('#modalHapus').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var modal = $(this);
            modal.find('#hapus_nama').text(button.data('nama'));
            var url = '{{ route("manajemenasetdanasrama.jadwal-piket.destroy", ":id") }}'.replace(':id', button.data('id'));
            modal.find('#formHapus').attr('action', url);
        });

        // Wizard Logic: Next Step
        $('#btnNextStep').on('click', function() {
            const jml = $('#input_jumlah_lokasi').val();
            if(!jml || jml < 1) {
                alert('Silakan isi jumlah lokasi piket terlebih dahulu.');
                return;
            }

            // Generate Inputs di Step 2
            let html = '';
            for(let i=1; i<=jml; i++) {
                html += `
                    <div class="location-row shadow-sm">
                        <div class="row">
                            <div class="col-md-7">
                                <div class="form-group mb-md-0">
                                    <label class="small font-weight-bold text-muted">NAMA LOKASI ${i}</label>
                                    <input type="text" name="lokasi[]" class="form-control" placeholder="Misal: Masjid / Halaman" required>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group mb-0">
                                    <label class="small font-weight-bold text-muted">JUMLAH SANTRI</label>
                                    <input type="number" name="jumlah_santri[]" class="form-control input-piket-quota" value="0" min="1" required>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
            $('#location_inputs_container').html(html);
            
            // Switch Step
            $('#step-1').addClass('d-none');
            $('#step-2').removeClass('d-none');
            updateRemainingCount();
        });

        // Wizard Logic: Prev Step
        $('#btnPrevStep').on('click', function() {
            $('#step-2').addClass('d-none');
            $('#step-1').removeClass('d-none');
        });

        // Live Counter Logic
        $(document).on('input', '.input-piket-quota', function() {
            updateRemainingCount();
        });

        function updateRemainingCount() {
            let totalInput = 0;
            $('.input-piket-quota').each(function() {
                totalInput += parseInt($(this).val()) || 0;
            });

            const sisa = totalSantri - totalInput;
            const label = $('#label_sisa_santri');
            
            label.text(sisa);

            if (sisa < 0) {
                label.removeClass('text-warning text-success').addClass('text-danger');
                $('#alert_over_limit').fadeIn();
                $('#btnGenerateNow').attr('disabled', true);
            } else if (sisa === 0) {
                label.removeClass('text-warning text-danger').addClass('text-success');
                $('#alert_over_limit').fadeOut();
                $('#btnGenerateNow').attr('disabled', false);
            } else {
                label.removeClass('text-success text-danger').addClass('text-warning');
                $('#alert_over_limit').fadeOut();
                $('#btnGenerateNow').attr('disabled', true); // Masih ada sisa, jangan biarkan generate (sesuai konsep semua harus kebagian)
            }
        }
    });
</script>
@endpush
