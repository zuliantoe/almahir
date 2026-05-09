@extends('layouts.app')

@section('title', $title)

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6"><h1 class="m-0 font-weight-bold text-dark">{{ $title }}</h1></div>
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
    {{-- STATS SECTION --}}
    <div class="row">
        <div class="col-lg-3 col-6"><div class="small-box bg-info shadow-sm"><div class="inner"><h3>{{ number_format($stats['total'] ?? 0) }}</h3><p>Total Jadwal</p></div><div class="icon"><i class="fas fa-calendar-alt"></i></div></div></div>
        <div class="col-lg-3 col-6"><div class="small-box bg-primary shadow-sm"><div class="inner"><h3>{{ number_format($stats['hari_ini'] ?? 0) }}</h3><p>Jadwal Hari Ini</p></div><div class="icon"><i class="fas fa-calendar-day"></i></div></div></div>
        <div class="col-lg-3 col-6"><div class="small-box bg-success shadow-sm"><div class="inner"><h3>{{ number_format($stats['selesai'] ?? 0) }}</h3><p>Tugas Selesai</p></div><div class="icon"><i class="fas fa-check-circle"></i></div></div></div>
        <div class="col-lg-3 col-6"><div class="small-box bg-warning shadow-sm"><div class="inner"><h3>{{ number_format($stats['belum'] ?? 0) }}</h3><p>Menunggu</p></div><div class="icon"><i class="fas fa-hourglass-half"></i></div></div></div>
    </div>

    @if(session('success')) <x-alert type="success" :message="session('success')" dismissible /> @endif
    @if(session('error')) <x-alert type="danger" :message="session('error')" dismissible /> @endif
    @if(session('warning')) <x-alert type="warning" :message="session('warning')" dismissible /> @endif

    <div class="row">
        <div class="col-md-12">
            <x-card title="Manajemen Jadwal Piket" icon="fas fa-calendar-check" outline>
                <x-slot name="tools">
                    <div class="d-flex flex-wrap" style="gap: 8px;">
                        <button type="button" class="btn btn-sm btn-info shadow-sm px-3" style="border-radius: 8px;" data-toggle="modal" data-target="#modalAutoGenerate"><i class="fas fa-robot mr-1"></i> Auto-Generate</button>
                        <button type="button" class="btn btn-sm btn-secondary shadow-sm px-3" style="border-radius: 8px;" onclick="triggerPrintAll()"><i class="fas fa-print mr-1"></i> Cetak</button>
                        <button type="button" class="btn btn-sm btn-danger shadow-sm px-3" style="border-radius: 8px;" onclick="if(confirm('Hapus seluruh riwayat jadwal piket? Data kamar dan santri tetap aman.')) document.getElementById('form-reset-piket').submit();"><i class="fas fa-eraser mr-1"></i> Kosongkan Jadwal</button>
                        <form id="form-reset-piket" action="{{ route('manajemenasetdanasrama.jadwal-piket.reset') }}" method="POST" style="display: none;">@csrf</form>
                        <button type="button" class="btn btn-sm btn-primary shadow-sm px-3" style="border-radius: 8px;" data-toggle="modal" data-target="#modalManualAdd"><i class="fas fa-plus mr-1"></i> Tambah</button>
                    </div>
                </x-slot>

                {{-- FILTER PANEL --}}
                <div class="card-body border-bottom bg-light p-4">
                    <form action="{{ route('manajemenasetdanasrama.jadwal-piket.index') }}" method="GET">
                        <div class="row align-items-end">
                            <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                                <label class="small font-weight-bold text-muted uppercase mb-1">Cari Santri</label>
                                <div class="input-group shadow-sm">
                                    <div class="input-group-prepend"><span class="input-group-text bg-white border-right-0 text-muted"><i class="fas fa-search"></i></span></div>
                                    <input type="text" class="form-control border-left-0" name="q" placeholder="Nama..." value="{{ request('q') }}">
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6 mb-3 mb-lg-0">
                                <label class="small font-weight-bold text-muted uppercase mb-1">Lokasi</label>
                                <select class="form-control select2 shadow-sm" name="lokasi_piket" onchange="this.form.submit()">
                                    <option value="">Semua Lokasi</option>
                                    @foreach($locations as $loc) <option value="{{ $loc }}" {{ request('lokasi_piket') == $loc ? 'selected' : '' }}>{{ $loc }}</option> @endforeach
                                </select>
                            </div>
                            <div class="col-lg-5 col-md-8 mb-3 mb-lg-0">
                                <label class="small font-weight-bold text-muted uppercase mb-1">Rentang Tanggal</label>
                                <div class="row no-gutters">
                                    <div class="col-5"><input type="date" class="form-control shadow-sm" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}"></div>
                                    <div class="col-2 text-center align-self-center text-muted">-</div>
                                    <div class="col-5"><input type="date" class="form-control shadow-sm" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}"></div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-4">
                                <button type="submit" class="btn btn-primary btn-block shadow-sm">Filter</button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- DATA SECTION --}}
                <div class="card-body bg-light p-2 p-md-4">
                    @if($activeDate)
                        <div class="piket-day-group mb-4">
                            <div class="bg-dark px-4 py-3 border-left border-warning shadow-sm mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center" style="border-width: 5px !important; border-radius: 0 12px 12px 0; gap: 15px;">
                                <h5 class="mb-0 font-weight-bold text-white">
                                    <i class="fas fa-calendar-check mr-2 text-warning"></i> {{ \Carbon\Carbon::parse($activeDate)->translatedFormat('l, d F Y') }}
                                    @if(\Carbon\Carbon::parse($activeDate)->isToday())
                                        <span class="badge badge-pill badge-primary ml-2 font-weight-normal shadow-sm" style="font-size: 11px;">HARI INI</span>
                                    @endif
                                </h5>
                                <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-4 shadow-sm" onclick="triggerDeleteDay('{{ $activeDate }}', '{{ \Carbon\Carbon::parse($activeDate)->translatedFormat('l, d F Y') }}')">
                                        <i class="fas fa-trash-alt mr-2"></i> Hapus Jadwal Hari Ini
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-4 shadow-sm" onclick="triggerPrintDay('{{ $activeDate }}')">
                                        <i class="fas fa-print mr-2"></i> Cetak Hari Ini
                                    </button>
                                </div>
                            </div>
                            
                            @php
                                $groupedByLocation = $jadwal->groupBy('lokasi_piket');
                                $leftColumn = collect(); $rightColumn = collect(); $leftTotal = 0; $rightTotal = 0;
                                $sortedLocations = $groupedByLocation->sortByDesc(function($items) { return $items->count(); });
                                foreach($sortedLocations as $location => $items) {
                                    if ($leftTotal <= $rightTotal) { $leftColumn->put($location, $items); $leftTotal += $items->count(); } 
                                    else { $rightColumn->put($location, $items); $rightTotal += $items->count(); }
                                }
                            @endphp

                            <div class="row">
                                <div class="col-lg-6 mb-3 mb-lg-0">
                                    @foreach($leftColumn as $location => $items)
                                        <div class="card shadow-sm border-0 mb-4 animate__animated animate__fadeInUp" style="border-radius: 15px; overflow: hidden;">
                                            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                                                <h6 class="mb-0 font-weight-bold text-primary"><i class="fas fa-map-marker-alt mr-2 text-info"></i> {{ $location ?: 'Umum' }}</h6>
                                                <span class="badge badge-pill bg-light-info text-info px-2 py-1" style="font-size: 10px;">{{ $items->count() }} Santri</span>
                                            </div>
                                            <div class="card-body p-0">
                                                <div class="table-responsive"><table class="table table-sm table-hover mb-0"><thead class="bg-gray-light text-muted small uppercase"><tr><th width="75" class="pl-3 py-3 border-0">Shift</th><th class="py-3 border-0">Nama Santri</th><th width="115" class="text-center pr-3 py-3 border-0">Aksi</th></tr></thead>
                                                    <tbody>
                                                        @foreach($items as $item)
                                                        <tr>
                                                            <td class="align-middle pl-3 py-2 small font-weight-bold">{{ ucfirst($item->shift) }}</td>
                                                            <td class="align-middle py-2 font-weight-bold text-dark" style="font-size: 13px;">{{ $item->siswa->nama ?? '-' }}</td>
                                                            <td class="text-center align-middle pr-3 py-2">
                                                                <div class="d-flex justify-content-center" style="gap: 8px;">
                                                                    @if($item->status == 'belum')
                                                                    <form action="{{ route('manajemenasetdanasrama.jadwal-piket.selesai', $item->id) }}" method="POST" class="m-0">
                                                                        @csrf
                                                                        <button type="submit" class="btn-action btn-soft-success" title="Tandai Selesai"><i class="fas fa-check"></i></button>
                                                                    </form>
                                                                    @endif
                                                                    <a href="{{ route('manajemenasetdanasrama.jadwal-piket.edit', $item->id) }}" class="btn-action btn-soft-warning" title="Edit Jadwal"><i class="fas fa-pencil-alt"></i></a>
                                                                    <button type="button" class="btn-action btn-soft-danger" data-toggle="modal" data-target="#modalHapus" data-id="{{ $item->id }}" data-nama="{{ $item->siswa->nama ?? '' }}" title="Hapus"><i class="fas fa-trash"></i></button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="col-lg-6">
                                    @foreach($rightColumn as $location => $items)
                                        <div class="card shadow-sm border-0 mb-4 animate__animated animate__fadeInUp" style="border-radius: 15px; overflow: hidden;">
                                            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                                                <h6 class="mb-0 font-weight-bold text-primary"><i class="fas fa-map-marker-alt mr-2 text-info"></i> {{ $location ?: 'Umum' }}</h6>
                                                <span class="badge badge-pill bg-light-info text-info px-2 py-1" style="font-size: 10px;">{{ $items->count() }} Santri</span>
                                            </div>
                                            <div class="card-body p-0">
                                                <div class="table-responsive"><table class="table table-sm table-hover mb-0"><thead class="bg-gray-light text-muted small uppercase"><tr><th width="75" class="pl-3 py-3 border-0">Shift</th><th class="py-3 border-0">Nama Santri</th><th width="115" class="text-center pr-3 py-3 border-0">Aksi</th></tr></thead>
                                                    <tbody>
                                                        @foreach($items as $item)
                                                        <tr>
                                                            <td class="align-middle pl-3 py-2 small font-weight-bold">{{ ucfirst($item->shift) }}</td>
                                                            <td class="align-middle py-2 font-weight-bold text-dark" style="font-size: 13px;">{{ $item->siswa->nama ?? '-' }}</td>
                                                            <td class="text-center align-middle pr-3 py-2">
                                                                <div class="d-flex justify-content-center" style="gap: 8px;">
                                                                    @if($item->status == 'belum')
                                                                    <form action="{{ route('manajemenasetdanasrama.jadwal-piket.selesai', $item->id) }}" method="POST" class="m-0">
                                                                        @csrf
                                                                        <button type="submit" class="btn-action btn-soft-success" title="Tandai Selesai"><i class="fas fa-check"></i></button>
                                                                    </form>
                                                                    @endif
                                                                    <a href="{{ route('manajemenasetdanasrama.jadwal-piket.edit', $item->id) }}" class="btn-action btn-soft-warning" title="Edit Jadwal"><i class="fas fa-pencil-alt"></i></a>
                                                                    <button type="button" class="btn-action btn-soft-danger" data-toggle="modal" data-target="#modalHapus" data-id="{{ $item->id }}" data-nama="{{ $item->siswa->nama ?? '' }}" title="Hapus"><i class="fas fa-trash"></i></button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted bg-white border shadow-sm" style="border-radius: 20px;">
                            <img src="https://illustrations.popsy.co/gray/search.svg" alt="Empty" style="width: 150px; opacity: 0.5;">
                            <h5 class="mt-4 font-weight-bold text-dark">Tidak ada jadwal ditemukan!</h5>
                        </div>
                    @endif

                    {{-- DATE PAGINATION --}}
                    @if($paginator->hasPages())
                        <div class="mt-4 d-flex flex-column flex-md-row justify-content-between align-items-center bg-white p-3 border shadow-sm" style="border-radius: 15px; gap: 15px;">
                            <div class="small text-muted font-weight-bold">Menampilkan Tanggal Ke-{{ $paginator->currentPage() }} dari {{ $paginator->total() }} Tanggal</div>
                            <div class="pagination-container">{{ $paginator->links() }}</div>
                        </div>
                    @endif
                </div>
            </x-card>
        </div>
    </div>
</div>

{{-- MODAL HAPUS PER HARI --}}
<div class="modal fade" id="modalDeleteDay" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <form id="formDeleteDay" action="" method="POST">@csrf @method('DELETE')
                <div class="modal-header bg-danger text-white border-0 py-3"><h5 class="modal-title font-weight-bold"><i class="fas fa-calendar-times mr-2"></i> Hapus Jadwal Harian</h5><button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button></div>
                <div class="modal-body p-4 text-center text-dark"><div class="mb-3"><i class="fas fa-exclamation-triangle fa-3x text-danger opacity-25"></i></div><p class="mb-0 text-muted">Hapus <strong>SELURUH JADWAL</strong> pada tanggal:</p><h5 class="font-weight-bold text-dark mt-2" id="delete_day_label"></h5></div>
                <div class="modal-footer bg-light border-0 py-3 px-4"><button type="button" class="btn btn-link text-muted font-weight-bold mr-auto" data-dismiss="modal">Batal</button><button type="submit" class="btn btn-danger px-4 shadow-sm" style="border-radius: 10px;">Ya, Hapus Semua</button></div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL HAPUS ITEM --}}
<div class="modal fade" id="modalHapus" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <form id="formHapus" action="" method="POST">@csrf @method('DELETE')
                <div class="modal-header bg-danger text-white border-0 py-3"><h5 class="modal-title font-weight-bold"><i class="fas fa-trash mr-2"></i> Hapus Jadwal</h5><button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button></div>
                <div class="modal-body p-4 text-center text-dark"><div class="mb-3"><i class="fas fa-exclamation-triangle fa-3x text-danger opacity-25"></i></div><p class="mb-0 text-muted">Apakah Anda yakin ingin menghapus jadwal:</p><h5 class="font-weight-bold text-dark mt-2" id="hapus_nama"></h5></div>
                <div class="modal-footer bg-light border-0 py-3 px-4"><button type="button" class="btn btn-link text-muted font-weight-bold mr-auto" data-dismiss="modal">Batal</button><button type="submit" class="btn btn-danger px-4 shadow-sm" style="border-radius: 10px;">Ya, Hapus</button></div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL MANUAL BULK ADD --}}
<div class="modal fade" id="modalManualAdd" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <form id="formManualBulk" action="{{ route('manajemenasetdanasrama.jadwal-piket.bulk-store') }}" method="POST">@csrf
                <div class="modal-header bg-primary text-white border-0 py-3"><h5 class="modal-title font-weight-bold"><i class="fas fa-user-edit mr-2"></i> Tambah Jadwal Manual</h5><button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button></div>
                <div id="manual-step-1" class="manual-piket-step">
                    <div class="modal-body p-4 text-dark"><div class="row"><div class="col-md-6 mb-3"><label class="small font-weight-bold text-muted uppercase">Dari Tanggal</label><input type="date" class="form-control" name="tanggal_mulai" value="{{ date('Y-m-d') }}" required></div><div class="col-md-6 mb-3"><label class="small font-weight-bold text-muted uppercase">Sampai Tanggal</label><input type="date" class="form-control" name="tanggal_selesai" value="{{ date('Y-m-d') }}" required></div><div class="col-md-6 mb-3"><label class="small font-weight-bold text-muted uppercase">Shift</label><select class="form-control" name="shift" required><option value="pagi">Pagi</option><option value="sore">Sore</option><option value="malam">Malam</option></select></div><div class="col-md-6 mb-3"><label class="small font-weight-bold text-primary uppercase">Jumlah Lokasi</label><input type="number" id="manual_jml_lokasi" class="form-control" placeholder="Contoh: 3" min="1" max="10" required></div></div></div>
                    <div class="modal-footer bg-light border-0 py-3 px-4"><button type="button" class="btn btn-link text-muted font-weight-bold mr-2" data-dismiss="modal">Batal</button><button type="button" id="btnManualNext1" class="btn btn-primary px-4 shadow-sm font-weight-bold" style="border-radius: 8px;">Lanjut <i class="fas fa-arrow-right ml-1"></i></button></div>
                </div>
                <div id="manual-step-2" class="manual-piket-step d-none">
                    <div class="modal-body p-4 text-dark"><h6 class="font-weight-bold text-primary mb-4"><i class="fas fa-cog mr-1"></i> Atur Lokasi & Kuota Santri</h6><div id="manual_location_container" style="max-height: 350px; overflow-y: auto;"></div></div>
                    <div class="modal-footer bg-light border-0 py-3 px-4"><button type="button" id="btnManualPrev2" class="btn btn-outline-secondary px-3 mr-auto" style="border-radius: 8px;"><i class="fas fa-arrow-left mr-1"></i> Kembali</button><button type="button" id="btnManualNext2" class="btn btn-primary px-4 font-weight-bold shadow-sm" style="border-radius: 8px;">Lanjut Pilih Santri <i class="fas fa-arrow-right ml-1"></i></button></div>
                </div>
                <div id="manual-step-3" class="manual-piket-step d-none">
                    <div class="modal-body p-4 text-dark"><div class="d-flex justify-content-between align-items-center mb-4"><h6 class="font-weight-bold text-primary mb-0"><i class="fas fa-users mr-1"></i> Pilih Santri Per Lokasi</h6><span class="badge badge-pill badge-warning small px-3">Satu santri hanya bisa dipilih sekali</span></div><div id="manual_siswa_assignment_container" style="max-height: 400px; overflow-y: auto; padding-right: 5px;"></div></div>
                    <div class="modal-footer bg-light border-0 py-3 px-4"><button type="button" id="btnManualPrev3" class="btn btn-outline-secondary px-3 mr-auto" style="border-radius: 8px;"><i class="fas fa-arrow-left mr-1"></i> Kembali</button><button type="submit" id="btnSaveManual" class="btn btn-success px-5 font-weight-bold shadow-sm" style="border-radius: 8px;">Simpan Jadwal <i class="fas fa-check-circle ml-1"></i></button></div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL PRINT --}}
<div class="modal fade" id="modalPrintPiket" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <form action="{{ route('manajemenasetdanasrama.jadwal-piket.print') }}" method="GET" target="_blank">
                <input type="hidden" name="q" value="{{ request('q') }}">
                <input type="hidden" name="lokasi_piket" value="{{ request('lokasi_piket') }}">
                <div class="modal-header bg-secondary text-white border-0 py-3"><h5 class="modal-title font-weight-bold"><i class="fas fa-print mr-2"></i> Cetak Jadwal Piket</h5><button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button></div>
                <div class="modal-body p-4 text-dark">
                    <div class="form-group mb-4" id="print_mode_selector">
                        <label class="small font-weight-bold text-muted uppercase">Pilih Mode Cetak</label><div class="d-flex" style="gap: 15px;"><div class="custom-control custom-radio"><input type="radio" id="modeAll" name="print_mode" class="custom-control-input" value="all" checked><label class="custom-control-label font-weight-bold" for="modeAll">Sesuai Filter</label></div><div class="custom-control custom-radio"><input type="radio" id="modeSingle" name="print_mode" class="custom-control-input" value="single"><label class="custom-control-label font-weight-bold" for="modeSingle">1 Hari Saja</label></div></div></div><div id="print_date_container" class="form-group mb-4 d-none"><label class="small font-weight-bold text-primary uppercase">Pilih Tanggal</label><input type="date" id="print_single_date" name="single_date" class="form-control" value="{{ date('Y-m-d') }}"><input type="hidden" id="print_tanggal_mulai" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}"><input type="hidden" id="print_tanggal_selesai" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}"></div><hr><div class="form-group mb-3 mt-4"><label class="small font-weight-bold text-muted uppercase">Nama Musyrif <span class="text-danger">*</span></label><input type="text" class="form-control" name="nama_musyrif" placeholder="Nama Musyrif..." required></div><div class="form-group mb-0"><label class="small font-weight-bold text-muted uppercase">Nama Kepala Sekolah <span class="text-danger">*</span></label><input type="text" class="form-control" name="nama_kepsek" placeholder="Nama Kepsek..." required></div></div>
                <div class="modal-footer bg-light border-0 py-3 px-4"><button type="button" class="btn btn-link text-muted font-weight-bold mr-auto" data-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary px-4 shadow-sm" style="border-radius: 10px;"><i class="fas fa-file-pdf mr-2"></i> Generate PDF</button></div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL AUTO GENERATE --}}
<div class="modal fade" id="modalAutoGenerate" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <form id="formSmartGenerate" action="{{ route('manajemenasetdanasrama.jadwal-piket.auto-generate') }}" method="POST">@csrf
                <div class="modal-header bg-primary text-white border-0 py-3"><h5 class="modal-title font-weight-bold"><i class="fas fa-robot mr-2"></i> Smart Picket Generator</h5><button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button></div>
                <div id="step-1" class="picket-step"><div class="modal-body p-4 text-dark"><div class="row"><div class="col-md-6 mb-3"><label class="small font-weight-bold text-muted uppercase">Dari Tanggal</label><input type="date" class="form-control" name="tanggal_mulai" value="{{ date('Y-m-d') }}" required></div><div class="col-md-6 mb-3"><label class="small font-weight-bold text-muted uppercase">Sampai Tanggal</label><input type="date" class="form-control" name="tanggal_selesai" value="{{ date('Y-m-d') }}" required></div><div class="col-md-6 mb-3"><label class="small font-weight-bold text-muted uppercase">Pilih Shift</label><select class="form-control" name="shift" required><option value="pagi">Pagi</option><option value="sore">Sore</option><option value="malam">Malam</option></select></div><div class="col-md-6 mb-3"><label class="small font-weight-bold text-primary uppercase">Jumlah Lokasi</label><input type="number" id="input_jumlah_lokasi" class="form-control" placeholder="Contoh: 3" min="1" max="10" required></div></div></div><div class="modal-footer bg-light border-0 py-3 px-4"><button type="button" class="btn btn-link text-muted font-weight-bold mr-2" data-dismiss="modal">Batal</button><button type="button" id="btnNextStep" class="btn btn-primary px-4 shadow-sm font-weight-bold" style="border-radius: 8px;">Lanjut <i class="fas fa-arrow-right ml-1"></i></button></div></div>
                <div id="step-2" class="picket-step d-none"><div class="modal-body p-4 text-dark"><div class="d-flex justify-content-between align-items-center mb-4"><h6 class="font-weight-bold text-primary mb-0"><i class="fas fa-cog mr-1"></i> Detail Lokasi</h6><div class="bg-dark text-white px-3 py-1 rounded-pill small">Total Santri: <span id="label_sisa_santri" class="font-weight-bold text-warning">{{ $totalSantri }}</span></div></div><div id="location_inputs_container" style="max-height: 300px; overflow-y: auto; padding-right: 5px;"></div></div><div class="modal-footer bg-light border-0 py-3 px-4"><button type="button" id="btnPrevStep" class="btn btn-outline-secondary px-3 mr-auto" style="border-radius: 8px;"><i class="fas fa-arrow-left mr-1"></i> Kembali</button><button type="submit" id="btnGenerateNow" class="btn btn-success px-4 font-weight-bold shadow-sm" style="border-radius: 8px;" disabled>Generate <i class="fas fa-rocket ml-1"></i></button></div></div>
            </form>
        </div>
    </div>
</div>

<style>
    .bg-gray-light { background-color: #f8fafc; } .bg-light-info { background-color: #e0f2fe; }
    .small-box .icon { top: 10px; right: 15px; font-size: 50px; opacity: 0.3; }
    .btn-action { width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; border-radius: 10px; border: none; transition: all 0.3s ease; font-size: 13px; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    .btn-soft-success { background: #dcfce7; color: #16a34a; } .btn-soft-success:hover { background: #16a34a; color: white; transform: translateY(-3px); box-shadow: 0 5px 15px rgba(22, 163, 74, 0.3); }
    .btn-soft-warning { background: #fef9c3; color: #ca8a04; } .btn-soft-warning:hover { background: #ca8a04; color: white; transform: translateY(-3px); box-shadow: 0 5px 15px rgba(202, 138, 4, 0.3); }
    .btn-soft-danger { background: #fee2e2; color: #dc2626; } .btn-soft-danger:hover { background: #dc2626; color: white; transform: translateY(-3px); box-shadow: 0 5px 15px rgba(220, 38, 38, 0.3); }
    .manual-loc-row { background: #f8fafc; border-left: 4px solid #4361ee; padding: 15px; border-radius: 12px; margin-bottom: 12px; }
    .assignment-card { background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px; margin-bottom: 15px; }
</style>

@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        const totalSantriCount = {{ $totalSantri }};

        // Modal Hapus Item
        $('#modalHapus').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); var modal = $(this);
            modal.find('#hapus_nama').text(button.data('nama'));
            var url = '{{ route("manajemenasetdanasrama.jadwal-piket.destroy", ":id") }}'.replace(':id', button.data('id'));
            modal.find('#formHapus').attr('action', url);
        });

        // Trigger Delete Day
        window.triggerDeleteDay = function(date, label) {
            $('#delete_day_label').text(label);
            var url = '{{ route("manajemenasetdanasrama.jadwal-piket.destroy-day", ":date") }}'.replace(':date', date);
            $('#formDeleteDay').attr('action', url); $('#modalDeleteDay').modal('show');
        };

        // Print Logic
        $('input[name="print_mode"]').on('change', function() {
            if ($(this).val() === 'single') { $('#print_date_container').removeClass('d-none'); } 
            else { $('#print_date_container').addClass('d-none'); }
        });

        window.triggerPrintDay = function(date) { 
            $('#print_mode_selector').addClass('d-none'); // Sembunyikan pilihan mode
            $('#print_date_container').addClass('d-none'); // Sembunyikan pilihan tanggal
            $('#modeSingle').prop('checked', true); 
            $('#print_single_date').val(date); 
            $('#print_tanggal_mulai').val(date); 
            $('#print_tanggal_selesai').val(date); 
            $('#modalPrintPiket').modal('show'); 
        }

        window.triggerPrintAll = function() { 
            $('#print_mode_selector').removeClass('d-none'); // Munculkan pilihan mode
            $('#modeAll').prop('checked', true); 
            $('#print_date_container').addClass('d-none'); 
            $('#print_tanggal_mulai').val('{{ request('tanggal_mulai') }}'); 
            $('#print_tanggal_selesai').val('{{ request('tanggal_selesai') }}'); 
            $('#modalPrintPiket').modal('show'); 
        }

        // Manual Bulk Add Logic
        $('#btnManualNext1').on('click', function() {
            const jml = $('#manual_jml_lokasi').val(); if(!jml || jml < 1) { alert('Isi jumlah lokasi!'); return; }
            let html = '';
            for(let i=1; i<=jml; i++) {
                html += `<div class="manual-loc-row"><div class="row align-items-center"><div class="col-7"><label class="small font-weight-bold text-muted uppercase">LOKASI ${i}</label><input type="text" name="lokasi[]" class="form-control manual-loc-name" placeholder="Masjid, Asrama, dll..." required></div><div class="col-5"><label class="small font-weight-bold text-muted uppercase">KUOTA SANTRI</label><input type="number" name="jumlah_santri[]" class="form-control manual-loc-quota" value="1" min="1" required></div></div></div>`;
            }
            $('#manual_location_container').html(html); $('#manual-step-1').addClass('d-none'); $('#manual-step-2').removeClass('d-none');
        });

        $('#btnManualPrev2').on('click', function() { $('#manual-step-2').addClass('d-none'); $('#manual-step-1').removeClass('d-none'); });

        $('#btnManualNext2').on('click', function() {
            let html = ''; let valid = true;
            $('.manual-loc-name').each(function(index) {
                const name = $(this).val(); const quota = parseInt($('.manual-loc-quota').eq(index).val()) || 0; if(!name) valid = false;
                html += `<div class="assignment-card"><div class="bg-light px-2 py-1 mb-2 font-weight-bold text-primary small border-bottom" style="border-radius: 5px 5px 0 0;"><i class="fas fa-map-pin mr-1"></i> ${name} (${quota} Slot)</div><div class="row">`;
                for(let j=0; j<quota; j++) {
                    html += `<div class="col-md-6 mb-2"><label class="small text-muted mb-1">Slot ${j+1}</label><input type="hidden" name="lokasi_mapping[]" value="${index}"><select name="siswa_id[]" class="form-control select2-siswa" required><option value="">- Cari Santri -</option>@foreach($allSiswa as $s)<option value="{{ $s->id }}">{{ $s->nama }} ({{ $s->nis }})</option>@endforeach</select></div>`;
                }
                html += `</div></div>`;
            });
            if(!valid) { alert('Semua nama lokasi harus diisi!'); return; }
            $('#manual_siswa_assignment_container').html(html); $('.select2-siswa').select2({ theme: 'bootstrap4', width: '100%', placeholder: '- Cari Santri -' });
            $('#manual-step-2').addClass('d-none'); $('#manual-step-3').removeClass('d-none');
        });

        $('#btnManualPrev3').on('click', function() { $('#manual-step-3').addClass('d-none'); $('#manual-step-2').removeClass('d-none'); });

        $(document).on('change', '.select2-siswa', function() {
            const selectedVal = $(this).val(); const currentSelect = $(this); if (!selectedVal) return;
            let duplicateFound = false;
            $('.select2-siswa').not(currentSelect).each(function() { if ($(this).val() === selectedVal) { duplicateFound = true; return false; } });
            if (duplicateFound) { Swal.fire({ icon: 'warning', title: 'Santri Duplikat!', text: 'Santri ini sudah dipilih di slot lain.', confirmButtonColor: '#4361ee' }); $(this).val(null).trigger('change'); }
        });

        // Auto Generate Logic
        $('#btnNextStep').on('click', function() {
            const jml = $('#input_jumlah_lokasi').val(); if(!jml || jml < 1) { alert('Isi jumlah lokasi!'); return; }
            let html = '';
            for(let i=1; i<=jml; i++) {
                html += `<div class="manual-loc-row"><div class="row align-items-center"><div class="col-7"><label class="small font-weight-bold text-muted uppercase">LOKASI ${i}</label><input type="text" name="lokasi[]" class="form-control" placeholder="Nama..." required></div><div class="col-5"><label class="small font-weight-bold text-muted uppercase">KUOTA</label><input type="number" name="jumlah_santri[]" class="form-control input-piket-quota" value="1" min="1" required></div></div></div>`;
            }
            $('#location_inputs_container').html(html); $('#step-1').addClass('d-none'); $('#step-2').removeClass('d-none'); updateRemainingCount();
        });

        $('#btnPrevStep').on('click', function() { $('#step-2').addClass('d-none'); $('#step-1').removeClass('d-none'); });

        $(document).on('input', '.input-piket-quota', function() { updateRemainingCount(); });

        function updateRemainingCount() {
            let totalInput = 0; $('.input-piket-quota').each(function() { totalInput += parseInt($(this).val()) || 0; });
            const sisa = totalSantriCount - totalInput; const label = $('#label_sisa_santri'); label.text(sisa);
            if (sisa < 0) { label.removeClass('text-warning text-success').addClass('text-danger'); $('#btnGenerateNow').attr('disabled', true); } 
            else if (sisa === 0) { label.removeClass('text-warning text-danger').addClass('text-success'); $('#btnGenerateNow').attr('disabled', false); } 
            else { label.removeClass('text-success text-danger').addClass('text-warning'); $('#btnGenerateNow').attr('disabled', true); }
        }
    });
</script>
@endpush
