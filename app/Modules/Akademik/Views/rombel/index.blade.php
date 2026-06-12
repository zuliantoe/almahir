@extends('layouts.app')

@section('title', 'Manajemen Rombel')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <div class="mb-3 mb-md-0">
                <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Daftar Rombel</h1>
                <p class="text-muted mb-0">Kelola Rombongan Belajar, Siswa, dan Wali Kelas</p>
            </div>
            @if(Auth::check() && !Auth::user()->hasRole('GURU') && !Auth::user()->hasRole('SISWA'))
            <x-btn :href="route('akademik.rombel.create')" icon="fas fa-plus" class="btn-primary shadow-sm">
                Tambah Rombel Baru
            </x-btn>
            @endif
        </div>
    </div>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" dismissible />
    @endif
    @if(session('error'))
        <x-alert type="danger" :message="session('error')" dismissible />
    @endif

    <div class="card border-0 shadow-sm mb-4 bg-light">
        <div class="card-body p-3">
            <form action="{{ route('akademik.rombel.index') }}" method="GET" class="row align-items-end">
                <div class="col-md-4 mb-2 mb-md-0">
                    <label class="small font-weight-bold text-muted mb-1">Cari Rombel</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                        </div>
                        <input type="text" name="search" class="form-control border-left-0" placeholder="Nama Rombel / Kelas..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3 mb-2 mb-md-0">
                    <label class="small font-weight-bold text-muted mb-1">Tahun Ajaran</label>
                    <select name="tahunajaran_id" class="form-control select2">
                        <option value="">Semua Tahun</option>
                        @foreach($tahun_ajaran as $ta)
                            <option value="{{ $ta->id }}" {{ request('tahunajaran_id') == $ta->id ? 'selected' : '' }}>
                                {{ $ta->tahunajaran }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-2 mb-md-0">
                    <label class="small font-weight-bold text-muted mb-1">Tingkat</label>
                    <select name="tingkat_id" class="form-control select2">
                        <option value="">Semua Tingkat</option>
                        @foreach($tingkat as $t)
                            <option value="{{ $t->id }}" {{ request('tingkat_id') == $t->id ? 'selected' : '' }}>
                                {{ $t->nama_tingkat }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex" style="gap: 6px;">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    @if(request()->anyFilled(['search', 'tahunajaran_id', 'tingkat_id']))
                        <a href="{{ route('akademik.rombel.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                            <i class="fas fa-undo"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <x-card title="Rombongan Belajar" icon="fas fa-users-class" type="primary" outline shadow>
        <div class="table-responsive">
            <table class="table table-hover align-items-center mb-0">
                <thead class="bg-light">
                    <tr>
                        <th width="50" class="text-center border-0">No</th>
                        <th class="border-0">Informasi Rombel</th>
                        <th class="border-0">Tingkat & Kelas</th>
                        <th class="border-0">Tahun Ajaran</th>
                        <th class="border-0">Wali Kelas</th>
                        <th class="text-center border-0">Siswa</th>
                        <th width="120" class="text-center border-0">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rombel as $r)
                        <tr>
                            <td class="text-center">{{ ($rombel->currentPage()-1) * $rombel->perPage() + $loop->iteration }}</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="font-weight-bold text-dark" style="font-size: 1.1rem;">{{ $r->nama_rombel }}</span>
                                    <small class="text-muted"><i class="fas fa-id-badge mr-1"></i> ID: #{{ $r->id }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <div class="badge badge-soft-primary p-2 px-3 mb-1">
                                        <i class="fas fa-door-open mr-1"></i> {{ $r->kelas->nama_kelas ?? '-' }}
                                    </div>
                                    <small class="text-muted ml-1"><i class="fas fa-layer-group mr-1"></i> {{ $r->tingkat->nama_tingkat ?? '-' }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="font-weight-bold">{{ $r->tahunAjaran->tahunajaran ?? '-' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm mr-3 bg-soft-info rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                        <i class="fas fa-user-tie text-info"></i>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="font-weight-bold">{{ $r->walikelas->nama ?? 'Belum ditentukan' }}</span>
                                        <small class="text-muted">Wali Kelas</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-pill badge-primary px-3 py-2" style="font-size: 0.85rem;">
                                    {{ $r->riwayatSiswa->where('status', 'aktif')->count() }} / {{ $r->riwayatSiswa->count() }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center align-items-center" style="gap: 6px;">
                                    <a href="{{ route('akademik.rombel.show', $r->id) }}" class="btn btn-sm btn-info shadow-sm" title="Lihat Detail" style="margin: 0;">
                                        <i class="fas fa-search-plus"></i>
                                    </a>
                                    @if(Auth::check() && !Auth::user()->hasRole('GURU') && !Auth::user()->hasRole('SISWA'))
                                    <a href="{{ route('akademik.rombel.edit', $r->id) }}" class="btn btn-sm btn-warning shadow-sm text-white" title="Edit Data" style="margin: 0;">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <form action="{{ route('akademik.rombel.destroy', $r->id) }}" method="POST" class="d-inline" style="margin: 0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger shadow-sm btn-delete" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-users-slash fa-4x text-light mb-3"></i>
                                    <h5 class="text-muted">Data Rombel Belum Tersedia</h5>
                                    <p class="text-muted small">Silakan tambah rombel baru untuk memulai pendataan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4 d-flex justify-content-center">
            {{ $rombel->withQueryString()->links() }}
        </div>
    </x-card>

<style>
    .bg-soft-info { background-color: rgba(23, 162, 184, 0.1); }
    .badge-soft-primary { background-color: rgba(0, 123, 255, 0.1); color: #007bff; border: 1px solid rgba(0, 123, 255, 0.2); }
    .table th { text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; }
    .table td { vertical-align: middle; }
    .avatar { flex-shrink: 0; }
</style>
</div>
@endsection
