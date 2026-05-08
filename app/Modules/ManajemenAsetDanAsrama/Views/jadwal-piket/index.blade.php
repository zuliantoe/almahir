@extends('layouts.app')

@section('title', $title)

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0 font-weight-bold text-dark">{{ $title }}</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right small bg-transparent p-0 m-0">
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

    <div class="row">
        <div class="col-md-12">
            <x-card title="Manajemen Jadwal Piket" icon="fas fa-calendar-alt" outline>
                <x-slot name="tools">
                    <div class="d-flex flex-wrap" style="gap: 8px;">
                        <button type="button" class="btn btn-sm btn-info shadow-sm px-3" style="border-radius: 8px;" data-toggle="modal" data-target="#modalAutoGenerate">
                            <i class="fas fa-robot mr-1"></i> Auto-Generate
                        </button>
                        <button type="button" class="btn btn-sm btn-secondary shadow-sm px-3" style="border-radius: 8px;" onclick="triggerPrintAll()">
                            <i class="fas fa-print mr-1"></i> Cetak
                        </button>
                        <button type="button" class="btn btn-sm btn-danger shadow-sm px-3" style="border-radius: 8px;" onclick="if(confirm('Hapus semua jadwal piket? Data tidak bisa dikembalikan.')) document.getElementById('form-reset-piket').submit();">
                            <i class="fas fa-trash-alt mr-1"></i> Reset
                        </button>
                        <form id="form-reset-piket" action="{{ route('manajemenasetdanasrama.jadwal-piket.reset') }}" method="POST" style="display: none;">@csrf</form>
                        <a href="{{ route('manajemenasetdanasrama.jadwal-piket.create') }}" class="btn btn-sm btn-primary shadow-sm px-3" style="border-radius: 8px;">
                            <i class="fas fa-plus mr-1"></i> Tambah
                        </a>
                    </div>
                </x-slot>

                <div class="card-body border-bottom bg-light p-4">
                    <form action="{{ route('manajemenasetdanasrama.jadwal-piket.index') }}" method="GET">
                        <div class="row align-items-end">
                            <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                                <label class="small font-weight-bold text-muted uppercase mb-1">Cari Santri</label>
                                <div class="input-group shadow-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-right-0 text-muted"><i class="fas fa-search"></i></span>
                                    </div>
                                    <input type="text" class="form-control border-left-0" name="q" placeholder="Nama..." value="{{ request('q') }}">
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6 mb-3 mb-lg-0">
                                <label class="small font-weight-bold text-muted uppercase mb-1">Lokasi</label>
                                <select class="form-control select2 shadow-sm" name="lokasi_piket" onchange="this.form.submit()">
                                    <option value="">Semua Lokasi</option>
                                    @foreach($locations as $loc)
                                        <option value="{{ $loc }}" {{ request('lokasi_piket') == $loc ? 'selected' : '' }}>{{ $loc }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-5 col-md-8 mb-3 mb-lg-0">
                                <label class="small font-weight-bold text-muted uppercase mb-1">Rentang Tanggal</label>
                                <div class="row no-gutters">
                                    <div class="col-5">
                                        <input type="date" class="form-control shadow-sm" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}">
                                    </div>
                                    <div class="col-2 text-center align-self-center text-muted">-</div>
                                    <div class="col-5">
                                        <input type="date" class="form-control shadow-sm" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-4">
                                <div class="d-flex" style="gap: 5px;">
                                    <button type="submit" class="btn btn-primary btn-block shadow-sm">Filter</button>
                                    <a href="{{ route('manajemenasetdanasrama.jadwal-piket.index') }}" class="btn btn-outline-secondary shadow-sm" title="Reset Filter">
                                        <i class="fas fa-sync-alt"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body bg-light p-2 p-md-4">
                    @php
                        $groupedByDate = $jadwal->getCollection()->groupBy(function($item) {
                            return \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d');
                        });
                    @endphp

                    @forelse($groupedByDate as $date => $dayItems)
                        <div class="piket-day-group mb-5">
                            <div class="bg-dark px-4 py-3 border-left border-warning shadow-sm mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center" style="border-width: 5px !important; border-radius: 0 12px 12px 0; gap: 15px;">
                                <h5 class="mb-0 font-weight-bold text-white">
                                    <i class="fas fa-calendar-check mr-2 text-warning"></i> {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                                </h5>
                                <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
                                    <span class="badge badge-warning font-weight-bold px-3 py-2 shadow-sm">{{ $dayItems->count() }} Santri</span>
                                    <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-4" onclick="triggerPrintDay('{{ $date }}')">
                                        <i class="fas fa-print mr-2"></i> Cetak Hari Ini
                                    </button>
                                </div>
                            </div>
                            
                            @php
                                $groupedByLocation = $dayItems->groupBy('lokasi_piket');
                                $leftColumn = collect();
                                $rightColumn = collect();
                                $leftTotal = 0;
                                $rightTotal = 0;
                                $sortedLocations = $groupedByLocation->sortByDesc(function($items) { return $items->count(); });

                                foreach($sortedLocations as $location => $items) {
                                    if ($leftTotal <= $rightTotal) { $leftColumn->put($location, $items); $leftTotal += $items->count(); } 
                                    else { $rightColumn->put($location, $items); $rightTotal += $items->count(); }
                                }
                            @endphp

                            <div class="row">
                                <div class="col-lg-6 mb-3 mb-lg-0">
                                    @foreach($leftColumn as $location => $items)
                                        <div class="card shadow-sm border-0 mb-4 animate__animated animate__fadeInLeft" style="border-radius: 15px; overflow: hidden;">
                                            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                                                <h6 class="mb-0 font-weight-bold text-primary"><i class="fas fa-map-marker-alt mr-2 text-info"></i> {{ $location ?: 'Umum / Lainnya' }}</h6>
                                                <span class="badge badge-pill bg-light-info text-info px-2 py-1" style="font-size: 10px;">{{ $items->count() }} Santri</span>
                                            </div>
                                            <div class="card-body p-0">
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-hover mb-0">
                                                        <thead class="bg-gray-light text-muted small uppercase"><tr><th width="75" class="pl-3 py-3 border-0">Shift</th><th class="py-3 border-0">Nama Santri</th><th width="110" class="text-center pr-3 py-3 border-0">Aksi</th></tr></thead>
                                                        <tbody>
                                                            @foreach($items as $item)
                                                            <tr>
                                                                <td class="align-middle pl-3 py-2 small font-weight-bold">{{ ucfirst($item->shift) }}</td>
                                                                <td class="align-middle py-2 font-weight-bold text-dark" style="font-size: 13px;">{{ $item->siswa->nama ?? '-' }}</td>
                                                                <td class="text-center align-middle pr-3 py-2">
                                                                    <div class="d-flex justify-content-center" style="gap: 5px;">
                                                                        @if($item->status == 'belum')<form action="{{ route('manajemenasetdanasrama.jadwal-piket.selesai', $item->id) }}" method="POST" class="m-0">@csrf<button type="submit" class="btn-action btn-soft-success"><i class="fas fa-check"></i></button></form>@endif
                                                                        <a href="{{ route('manajemenasetdanasrama.jadwal-piket.edit', $item->id) }}" class="btn-action btn-soft-warning"><i class="fas fa-pencil-alt"></i></a>
                                                                        <button type="button" class="btn-action btn-soft-danger" data-toggle="modal" data-target="#modalHapus" data-id="{{ $item->id }}" data-nama="{{ $item->siswa->nama ?? '' }}"><i class="fas fa-trash"></i></button>
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
                                <div class="col-lg-6">
                                    @foreach($rightColumn as $location => $items)
                                        <div class="card shadow-sm border-0 mb-4 animate__animated animate__fadeInRight" style="border-radius: 15px; overflow: hidden;">
                                            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                                                <h6 class="mb-0 font-weight-bold text-primary"><i class="fas fa-map-marker-alt mr-2 text-info"></i> {{ $location ?: 'Umum / Lainnya' }}</h6>
                                                <span class="badge badge-pill bg-light-info text-info px-2 py-1" style="font-size: 10px;">{{ $items->count() }} Santri</span>
                                            </div>
                                            <div class="card-body p-0">
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-hover mb-0">
                                                        <thead class="bg-gray-light text-muted small uppercase"><tr><th width="75" class="pl-3 py-3 border-0">Shift</th><th class="py-3 border-0">Nama Santri</th><th width="110" class="text-center pr-3 py-3 border-0">Aksi</th></tr></thead>
                                                        <tbody>
                                                            @foreach($items as $item)
                                                            <tr>
                                                                <td class="align-middle pl-3 py-2 small font-weight-bold">{{ ucfirst($item->shift) }}</td>
                                                                <td class="align-middle py-2 font-weight-bold text-dark" style="font-size: 13px;">{{ $item->siswa->nama ?? '-' }}</td>
                                                                <td class="text-center align-middle pr-3 py-2">
                                                                    <div class="d-flex justify-content-center" style="gap: 5px;">
                                                                        @if($item->status == 'belum')<form action="{{ route('manajemenasetdanasrama.jadwal-piket.selesai', $item->id) }}" method="POST" class="m-0">@csrf<button type="submit" class="btn-action btn-soft-success"><i class="fas fa-check"></i></button></form>@endif
                                                                        <a href="{{ route('manajemenasetdanasrama.jadwal-piket.edit', $item->id) }}" class="btn-action btn-soft-warning"><i class="fas fa-pencil-alt"></i></a>
                                                                        <button type="button" class="btn-action btn-soft-danger" data-toggle="modal" data-target="#modalHapus" data-id="{{ $item->id }}" data-nama="{{ $item->siswa->nama ?? '' }}"><i class="fas fa-trash"></i></button>
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
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted bg-white border shadow-sm" style="border-radius: 20px;">
                            <img src="https://illustrations.popsy.co/gray/search.svg" alt="Empty" style="width: 150px; opacity: 0.5;">
                            <h5 class="mt-4 font-weight-bold">Data tidak ditemukan!</h5>
                            <a href="{{ route('manajemenasetdanasrama.jadwal-piket.index') }}" class="btn btn-primary px-4 shadow-sm" style="border-radius: 50px;"><i class="fas fa-undo mr-1"></i> Reset Pencarian</a>
                        </div>
                    @endforelse
                </div>
            </x-card>
        </div>
    </div>
</div>

{{-- MODAL PRINT SIGNATURE & OPTION --}}
<div class="modal fade" id="modalPrintPiket" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <form action="{{ route('manajemenasetdanasrama.jadwal-piket.print') }}" method="GET" target="_blank">
                <input type="hidden" name="q" value="{{ request('q') }}">
                <input type="hidden" name="lokasi_piket" value="{{ request('lokasi_piket') }}">
                
                <div class="modal-header bg-secondary text-white border-0 py-3">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-print mr-2"></i> Cetak Jadwal Piket</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body p-4">
                    {{-- PILIHAN MODE CETAK --}}
                    <div class="form-group mb-4">
                        <label class="small font-weight-bold text-muted uppercase">Pilih Mode Cetak</label>
                        <div class="d-flex" style="gap: 15px;">
                            <div class="custom-control custom-radio">
                                <input type="radio" id="modeAll" name="print_mode" class="custom-control-input" value="all" checked>
                                <label class="custom-control-label font-weight-bold" for="modeAll">Sesuai Filter</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="modeSingle" name="print_mode" class="custom-control-input" value="single">
                                <label class="custom-control-label font-weight-bold" for="modeSingle">1 Hari Saja</label>
                            </div>
                        </div>
                    </div>

                    {{-- INPUT TANGGAL (Muncul jika pilih 1 Hari Saja) --}}
                    <div id="print_date_container" class="form-group mb-4 d-none animate__animated animate__fadeIn">
                        <label class="small font-weight-bold text-primary uppercase">Pilih Tanggal <span class="text-danger">*</span></label>
                        <input type="date" id="print_single_date" name="single_date" class="form-control" value="{{ date('Y-m-d') }}">
                        {{-- Hidden inputs for controller compatibility --}}
                        <input type="hidden" id="print_tanggal_mulai" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}">
                        <input type="hidden" id="print_tanggal_selesai" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}">
                    </div>

                    <hr>

                    <div class="form-group mb-3 mt-4">
                        <label class="small font-weight-bold text-muted uppercase">Nama Musyrif <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_musyrif" placeholder="Nama Musyrif..." required>
                    </div>
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-muted uppercase">Nama Kepala Sekolah <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_kepsek" placeholder="Nama Kepsek..." required>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 px-4">
                    <button type="button" class="btn btn-link text-muted font-weight-bold mr-auto" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm" style="border-radius: 10px;">
                        <i class="fas fa-file-pdf mr-2"></i> Generate PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL HAPUS & AUTO GENERATE (SAME AS BEFORE) --}}
<div class="modal fade" id="modalHapus" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <form id="formHapus" action="" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header bg-danger text-white border-0 py-3">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-trash mr-2"></i> Hapus Jadwal</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="mb-3"><i class="fas fa-exclamation-triangle fa-3x text-danger opacity-25"></i></div>
                    <p class="mb-0 text-muted">Apakah Anda yakin ingin menghapus jadwal:</p>
                    <h5 class="font-weight-bold text-dark mt-2" id="hapus_nama"></h5>
                </div>
                <div class="modal-footer bg-light border-0 py-3 px-4">
                    <button type="button" class="btn btn-link text-muted font-weight-bold mr-auto" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger px-4 shadow-sm" style="border-radius: 10px;">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAutoGenerate" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <form id="formSmartGenerate" action="{{ route('manajemenasetdanasrama.jadwal-piket.auto-generate') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white border-0 py-3">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-robot mr-2"></i> Smart Picket Generator</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div id="step-1" class="picket-step">
                    <div class="modal-body p-4 text-dark">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="small font-weight-bold text-muted uppercase">Dari Tanggal</label>
                                <input type="date" class="form-control" name="tanggal_mulai" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="small font-weight-bold text-muted uppercase">Sampai Tanggal</label>
                                <input type="date" class="form-control" name="tanggal_selesai" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="small font-weight-bold text-muted uppercase">Pilih Shift</label>
                                <select class="form-control shadow-sm" name="shift" required><option value="pagi">Pagi</option><option value="sore">Sore</option><option value="malam">Malam</option></select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="small font-weight-bold text-primary uppercase">Jumlah Lokasi</label>
                                <input type="number" id="input_jumlah_lokasi" class="form-control shadow-sm" placeholder="Contoh: 3" min="1" max="10" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 py-3 px-4">
                        <button type="button" class="btn btn-link text-muted font-weight-bold mr-2" data-dismiss="modal">Batal</button>
                        <button type="button" id="btnNextStep" class="btn btn-primary px-4 shadow-sm font-weight-bold" style="border-radius: 8px;">Lanjut <i class="fas fa-arrow-right ml-1"></i></button>
                    </div>
                </div>
                <div id="step-2" class="picket-step d-none">
                    <div class="modal-body p-4 text-dark">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="font-weight-bold text-primary mb-0"><i class="fas fa-cog mr-1"></i> Detail Lokasi</h6>
                            <div class="bg-dark text-white px-3 py-1 rounded-pill small">Sisa Santri: <span id="label_sisa_santri" class="font-weight-bold text-warning">{{ $totalSantri }}</span></div>
                        </div>
                        <div id="location_inputs_container" style="max-height: 300px; overflow-y: auto; padding-right: 5px;"></div>
                    </div>
                    <div class="modal-footer bg-light border-0 py-3 px-4">
                        <button type="button" id="btnPrevStep" class="btn btn-outline-secondary px-3 mr-auto" style="border-radius: 8px;"><i class="fas fa-arrow-left mr-1"></i> Kembali</button>
                        <button type="submit" id="btnGenerateNow" class="btn btn-success px-4 font-weight-bold shadow-sm" style="border-radius: 8px;" disabled>Generate <i class="fas fa-rocket ml-1"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .bg-gray-light { background-color: #f8fafc; }
    .bg-light-info { background-color: #e0f2fe; }
    .btn-action { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 8px; border: none; transition: all 0.2s ease; font-size: 14px; cursor: pointer; }
    .btn-soft-success { background: #dcfce7; color: #16a34a; } .btn-soft-success:hover { background: #16a34a; color: white; transform: scale(1.1); }
    .btn-soft-warning { background: #fef9c3; color: #ca8a04; } .btn-soft-warning:hover { background: #ca8a04; color: white; transform: scale(1.1); }
    .btn-soft-danger { background: #fee2e2; color: #dc2626; } .btn-soft-danger:hover { background: #dc2626; color: white; transform: scale(1.1); }
    .location-row { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; margin-bottom: 12px; border-left: 5px solid #4361ee; }
</style>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const totalSantri = {{ $totalSantri }};

        $('#modalHapus').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var modal = $(this);
            modal.find('#hapus_nama').text(button.data('nama'));
            var url = '{{ route("manajemenasetdanasrama.jadwal-piket.destroy", ":id") }}'.replace(':id', button.data('id'));
            modal.find('#formHapus').attr('action', url);
        });

        // Toggle Mode Cetak
        $('input[name="print_mode"]').on('change', function() {
            if ($(this).val() === 'single') {
                $('#print_date_container').removeClass('d-none');
            } else {
                $('#print_date_container').addClass('d-none');
            }
        });

        // Update Hidden Dates when Single Date Changes
        $('#print_single_date').on('change', function() {
            if ($('#modeSingle').is(':checked')) {
                $('#print_tanggal_mulai').val($(this).val());
                $('#print_tanggal_selesai').val($(this).val());
            }
        });

        // Wizard Logic
        $('#btnNextStep').on('click', function() {
            const jml = $('#input_jumlah_lokasi').val();
            if(!jml || jml < 1) { alert('Isi jumlah lokasi!'); return; }
            let html = '';
            for(let i=1; i<=jml; i++) {
                html += `<div class="location-row shadow-sm"><div class="row align-items-center"><div class="col-7"><label class="small font-weight-bold text-muted uppercase">LOKASI ${i}</label><input type="text" name="lokasi[]" class="form-control" placeholder="Nama..." required></div><div class="col-5"><label class="small font-weight-bold text-muted uppercase">KUOTA</label><input type="number" name="jumlah_santri[]" class="form-control input-piket-quota" value="0" min="1" required></div></div></div>`;
            }
            $('#location_inputs_container').html(html);
            $('#step-1').addClass('d-none'); $('#step-2').removeClass('d-none');
            updateRemainingCount();
        });

        $('#btnPrevStep').on('click', function() { $('#step-2').addClass('d-none'); $('#step-1').removeClass('d-none'); });

        $(document).on('input', '.input-piket-quota', function() { updateRemainingCount(); });

        function updateRemainingCount() {
            let totalInput = 0;
            $('.input-piket-quota').each(function() { totalInput += parseInt($(this).val()) || 0; });
            const sisa = totalSantri - totalInput;
            const label = $('#label_sisa_santri');
            label.text(sisa);
            if (sisa < 0) { label.removeClass('text-warning text-success').addClass('text-danger'); $('#btnGenerateNow').attr('disabled', true); } 
            else if (sisa === 0) { label.removeClass('text-warning text-danger').addClass('text-success'); $('#btnGenerateNow').attr('disabled', false); } 
            else { label.removeClass('text-success text-danger').addClass('text-warning'); $('#btnGenerateNow').attr('disabled', true); }
        }
    });

    function triggerPrintAll() {
        $('#modeAll').prop('checked', true);
        $('#print_date_container').addClass('d-none');
        // Reset to global filters
        $('#print_tanggal_mulai').val('{{ request('tanggal_mulai') }}');
        $('#print_tanggal_selesai').val('{{ request('tanggal_selesai') }}');
        $('#modalPrintPiket').modal('show');
    }

    function triggerPrintDay(date) {
        $('#modeSingle').prop('checked', true);
        $('#print_date_container').removeClass('d-none');
        $('#print_single_date').val(date);
        $('#print_tanggal_mulai').val(date);
        $('#print_tanggal_selesai').val(date);
        $('#modalPrintPiket').modal('show');
    }
</script>
@endpush
