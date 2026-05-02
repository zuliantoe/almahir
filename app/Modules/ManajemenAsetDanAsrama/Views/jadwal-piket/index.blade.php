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
            <x-card title="Daftar Jadwal Piket" icon="fas fa-calendar-alt">
                <x-slot name="tools">
                    <button type="button" class="btn btn-sm btn-info mr-2" data-toggle="modal" data-target="#modalAutoGenerate">
                        <i class="fas fa-robot mr-1"></i> Auto-Generate
                    </button>
                    <a href="{{ route('manajemenasetdanasrama.jadwal-piket.print', request()->all()) }}" target="_blank" class="btn btn-sm btn-secondary mr-2">
                        <i class="fas fa-print mr-1"></i> Cetak Jadwal
                    </a>
                    <a href="{{ route('manajemenasetdanasrama.jadwal-piket.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus mr-1"></i> Tambah Jadwal
                    </a>
                </x-slot>

                <div class="card-body border-bottom">
                    <form action="{{ route('manajemenasetdanasrama.jadwal-piket.index') }}" method="GET">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="kamar_id">Filter Kamar</label>
                                    <select class="form-control" id="kamar_id" name="kamar_id">
                                        <option value="">Semua Kamar</option>
                                        @foreach($kamar as $k)
                                            <option value="{{ $k->id }}" {{ request('kamar_id') == $k->id ? 'selected' : '' }}>
                                                {{ $k->nama_kamar }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tanggal_mulai">Dari Tanggal</label>
                                    <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tanggal_selesai">Sampai Tanggal</label>
                                    <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}">
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <div class="form-group w-100">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                    @if(request()->hasAny(['kamar_id', 'tanggal_mulai', 'tanggal_selesai']))
                                        <a href="{{ route('manajemenasetdanasrama.jadwal-piket.index') }}" class="btn btn-default btn-block mt-2">
                                            Reset
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>Kamar</th>
                                <th>Tanggal</th>
                                <th>Nama Santri</th>
                                <th>Status</th>
                                <th width="150">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jadwal as $item)
                            <tr>
                                <td>{{ $loop->iteration + ($jadwal->currentPage() - 1) * $jadwal->perPage() }}</td>
                                <td>{{ $item->kamar->nama_kamar ?? '-' }}</td>
                                <td>
                                    <strong>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('l, d F Y') }}</strong>
                                </td>
                                <td>{{ $item->siswa->nama ?? '-' }}</td>
                                <td>
                                    @if($item->status == 'sudah')
                                        <span class="badge badge-success"><i class="fas fa-check-circle"></i> Selesai</span>
                                    @else
                                        <span class="badge badge-warning"><i class="fas fa-clock"></i> Belum</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->status == 'belum')
                                    <form action="{{ route('manajemenasetdanasrama.jadwal-piket.selesai', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-xs btn-success" title="Tandai Selesai" onclick="return confirm('Tandai piket ini selesai?')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    @endif
                                    <a href="{{ route('manajemenasetdanasrama.jadwal-piket.edit', $item->id) }}" class="btn btn-xs btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    <i class="fas fa-inbox"></i> Belum ada data jadwal piket
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($jadwal->hasPages())
                <div class="d-flex justify-content-end mt-3">
                    {{ $jadwal->links() }}
                </div>
                @endif

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

{{-- MODAL AUTO GENERATE --}}
<div class="modal fade" id="modalAutoGenerate" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('manajemenasetdanasrama.jadwal-piket.auto-generate') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white"><i class="fas fa-robot mr-1"></i> Auto-Generate Jadwal Piket</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Sistem akan membuat jadwal piket secara adil (Round Robin) berdasarkan jumlah santri di kamar.</p>
                    <div class="form-group">
                        <label>Pilih Kamar <span class="text-danger">*</span></label>
                        <select class="form-control" name="kamar_id" required>
                            <option value="">-- Pilih Kamar --</option>
                            @foreach($kamar as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kamar }} ({{ $k->penghuni()->whereNull('tanggal_keluar')->count() }} Santri)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Dari Tanggal <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="tanggal_mulai" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Sampai Tanggal <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="tanggal_selesai" value="{{ date('Y-m-d', strtotime('+7 days')) }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Orang per Hari <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="person_per_day" value="1" min="1" required>
                        <small class="text-muted">Berapa orang yang piket dalam satu hari untuk kamar tersebut.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-cogs mr-1"></i> Generate</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#modalHapus').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var modal = $(this);
            modal.find('#hapus_nama').text(button.data('nama'));
            modal.find('#alasan_hapus').val('');
            var url = '{{ route("manajemenasetdanasrama.jadwal-piket.destroy", ":id") }}'.replace(':id', button.data('id'));
            modal.find('#formHapus').attr('action', url);
        });
    });
</script>
@endpush
