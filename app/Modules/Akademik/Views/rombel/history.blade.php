@extends('layouts.app')

@section('title', 'Riwayat Perpindahan Rombel')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Riwayat & Perpindahan Rombel</h1>
                <p class="text-muted">Pantau rekam jejak rombel dari tahun ke tahun</p>
            </div>
            <x-btn :href="route('akademik.rombel.index')" icon="fas fa-list" class="btn-primary shadow-sm">
                Manajemen Rombel
            </x-btn>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card border-0 shadow-sm mb-4 bg-light">
        <div class="card-body p-3">
            <form action="{{ route('akademik.rombel.history') }}" method="GET" class="row align-items-end">
                <div class="col-md-4 mb-2 mb-md-0">
                    <label class="small font-weight-bold text-muted mb-1">Filter Tahun Ajaran</label>
                    <select name="tahunajaran_id" class="form-control">
                        <option value="">Semua Tahun Ajaran</option>
                        @foreach($tahun_ajaran as $ta)
                            <option value="{{ $ta->id }}" {{ request('tahunajaran_id') == $ta->id ? 'selected' : '' }}>
                                {{ $ta->tahunajaran }} - {{ $ta->semester }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-2 mb-md-0">
                    <label class="small font-weight-bold text-muted mb-1">Filter Status</label>
                    <select name="status" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="naik" {{ request('status') == 'naik' ? 'selected' : '' }}>Sudah Naik Kelas</option>
                        <option value="lulus" {{ request('status') == 'lulus' ? 'selected' : '' }}>Sudah Lulus</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="fas fa-filter mr-1"></i> Terapkan Filter
                    </button>
                    @if(request()->anyFilled(['tahunajaran_id', 'status']))
                        <a href="{{ route('akademik.rombel.history') }}" class="btn btn-outline-secondary ml-2" title="Reset">
                            <i class="fas fa-undo"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <x-card title="Data Perpindahan Rombel" icon="fas fa-history" type="dark" outline shadow>
        <div class="table-responsive">
            <table class="table table-hover align-items-center mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0">Nama Rombel</th>
                        <th class="border-0">Tahun Ajaran</th>
                        <th class="border-0">Tingkat</th>
                        <th class="border-0">Kelas</th>
                        <th class="text-center border-0">Total Siswa</th>
                        <th class="text-center border-0">Status Record</th>
                        <th class="text-center border-0">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($history as $h)
                        <tr>
                            <td>
                                <div class="font-weight-bold text-dark">{{ $h->rombel->nama_rombel ?? 'Rombel Terhapus' }}</div>
                                <small class="text-muted">ID: #{{ $h->rombel_id }}</small>
                            </td>
                            <td>
                                <div class="badge badge-soft-info p-2">
                                    {{ $h->tahunAjaran->tahunajaran ?? '-' }} ({{ $h->tahunAjaran->semester ?? '-' }})
                                </div>
                            </td>
                            <td>
                                <span class="text-dark">{{ $h->kelas->tingkat->nama_tingkat ?? '-' }}</span>
                            </td>
                            <td>
                                <div class="font-weight-bold">{{ $h->kelas->nama_kelas ?? '-' }}</div>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-pill badge-primary px-3">{{ $h->total_siswa }} Siswa</span>
                            </td>
                            <td class="text-center">
                                @if($h->status == 'aktif')
                                    <span class="badge badge-success px-3 py-2"><i class="fas fa-check-circle mr-1"></i> Aktif</span>
                                @elseif($h->status == 'naik')
                                    <span class="badge badge-info px-3 py-2"><i class="fas fa-arrow-up mr-1"></i> Riwayat (Naik)</span>
                                @elseif($h->status == 'lulus')
                                    <span class="badge badge-dark px-3 py-2"><i class="fas fa-graduation-cap mr-1"></i> Riwayat (Lulus)</span>
                                @else
                                    <span class="badge badge-secondary px-3 py-2">{{ $h->status }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('akademik.rombel.show', $h->rombel_id) }}?tahunajaran_id={{ $h->tahunajaran_id }}" class="btn btn-sm btn-outline-primary shadow-sm">
                                    <i class="fas fa-eye mr-1"></i> Detail Riwayat
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-history fa-3x text-light mb-3"></i>
                                <h5 class="text-muted">Belum ada riwayat perpindahan rombel</h5>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4 d-flex justify-content-center">
            {{ $history->links() }}
        </div>
    </x-card>
</div>

<style>
    .badge-soft-info { background-color: rgba(23, 162, 184, 0.1); color: #17a2b8; border: 1px solid rgba(23, 162, 184, 0.2); }
    .table td { vertical-align: middle; }
    .table th { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: #6c757d; }
</style>
@endsection
