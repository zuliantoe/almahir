@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    {{-- Alert Messages --}}
    @if(session('success'))
        <x-alert type="success" :message="session('success')" dismissible />
    @endif
    @if(session('error'))
        <x-alert type="danger" :message="session('error')" dismissible />
    @endif

    <x-card title="Daftar Presensi" icon="fas fa-calendar-check">
        <x-slot name="tools">
            <a href="{{ route('penilaiandanpresensi.presensi.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Tambah Presensi
            </a>
        </x-slot>

        {{-- Stats Summary --}}
        <div class="row mb-4">
            <div class="col-md-2 col-sm-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $presensis->where('status', 'Hadir')->count() }}</h3>
                        <p>Hadir</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $presensis->where('status', 'Izin')->count() }}</h3>
                        <p>Izin</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-hand-paper"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $presensis->where('status', 'Sakit')->count() }}</h3>
                        <p>Sakit</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $presensis->where('status', 'Alpha')->count() }}</h3>
                        <p>Alpha</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-times"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{ $presensis->total() }}</h3>
                        <p>Total</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-list"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="card card-outline card-info mb-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-filter mr-2"></i> Filter Data
                </h3>
            </div>
            <div class="card-body">
                <form method="GET" class="form-inline">
                    <div class="form-group mr-2">
                        <label for="status_filter" class="mr-2">Status:</label>
                        <select name="status" id="status_filter" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="Hadir">Hadir</option>
                            <option value="Izin">Izin</option>
                            <option value="Sakit">Sakit</option>
                            <option value="Alpha">Alpha</option>
                        </select>
                    </div>
                    <div class="form-group mr-2">
                        <label for="kategori_filter" class="mr-2">Kategori:</label>
                        <select name="kategori" id="kategori_filter" class="form-control">
                            <option value="">Semua Kategori</option>
                            <option value="Sekolah">Sekolah</option>
                            <option value="Pengajian">Pengajian</option>
                            <option value="Ekstrakurikuler">Ekstrakurikuler</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-sm btn-info">
                        <i class="fas fa-search mr-1"></i> Cari
                    </button>
                </form>
            </div>
        </div>

        {{-- Table Section --}}
        <div class="table-responsive">
            <table class="table table-hover table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th style="width: 5%">No</th>
                        <th style="width: 15%">Siswa</th>
                        <th style="width: 12%">Guru</th>
                        <th style="width: 12%">Mata Pelajaran</th>
                        <th style="width: 8%">Jam</th>
                        <th style="width: 10%">Status</th>
                        <th style="width: 10%">Kategori</th>
                        <th style="width: 12%">Tanggal</th>
                        <th class="text-center" style="width: 16%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($presensis as $index => $item)
                    <tr>
                        <td>{{ ($presensis->currentPage() - 1) * $presensis->perPage() + $index + 1 }}</td>
                        <td>
                            <strong>{{ $item->siswa->nama ?? '-' }}</strong>
                            @if($item->scan_id)
                                <br>
                                <small class="text-muted">ID: {{ $item->scan_id }}</small>
                            @endif
                        </td>
                        <td>{{ $item->guru->nama ?? '-' }}</td>
                        <td>{{ $mapels->get($item->id_mapel)->nama ?? $mapels->get($item->id_mapel)->name ?? $item->id_mapel ?? '-' }}</td>
                        <td>
                            <i class="fas fa-clock mr-1 text-info"></i>
                            {{ \Carbon\Carbon::parse($item->jam)->format('H:i') }}
                        </td>
                        <td>
                            @if($item->status == 'Hadir')
                                <span class="badge badge-success">
                                    <i class="fas fa-check mr-1"></i> {{ $item->status }}
                                </span>
                            @elseif($item->status == 'Izin')
                                <span class="badge badge-warning">
                                    <i class="fas fa-hand-paper mr-1"></i> {{ $item->status }}
                                </span>
                            @elseif($item->status == 'Sakit')
                                <span class="badge badge-info">
                                    <i class="fas fa-heartbeat mr-1"></i> {{ $item->status }}
                                </span>
                            @else
                                <span class="badge badge-danger">
                                    <i class="fas fa-times mr-1"></i> {{ $item->status }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-secondary">{{ $item->kategori }}</span>
                        </td>
                        <td>
                            <small>{{ $item->created_at->locale('id')->translatedFormat('d M Y H:i') }}</small>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('penilaiandanpresensi.presensi.show', $item->id) }}"
                                   class="btn btn-success" title="Lihat Detail" data-toggle="tooltip">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('penilaiandanpresensi.presensi.edit', $item->id) }}"
                                   class="btn btn-info" title="Edit" data-toggle="tooltip">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-danger" 
                                        title="Hapus" 
                                        data-toggle="tooltip"
                                        onclick="hapusData('{{ route('penilaiandanpresensi.presensi.destroy', $item->id) }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-5">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            <strong>Belum ada data presensi.</strong><br>
                            <a href="{{ route('penilaiandanpresensi.presensi.create') }}" class="btn btn-primary btn-sm mt-2">
                                <i class="fas fa-plus mr-1"></i> Tambah Presensi Pertama
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Footer with Pagination --}}
        <x-slot name="footer">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted">
                    Menampilkan <strong>{{ $presensis->count() }}</strong> dari <strong>{{ $presensis->total() }}</strong> data
                </span>
                <nav>
                    {{ $presensis->links() }}
                </nav>
            </div>
        </x-slot>
    </x-card>
</div>

{{-- Delete Confirmation Modal --}}
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<script>
function hapusData(url) {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        document.getElementById('deleteForm').action = url;
        document.getElementById('deleteForm').submit();
    }
}
</script>
@endpush
