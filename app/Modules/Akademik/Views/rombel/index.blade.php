@extends('layouts.app')

@section('title', 'Manajemen Rombel')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Daftar Rombel</h1>
                <p class="text-muted">Kelola Rombongan Belajar, Siswa, dan Wali Kelas</p>
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
                                <div class="badge badge-soft-primary p-2 px-3">
                                    <i class="fas fa-door-open mr-1"></i> {{ $r->kelas->nama_kelas ?? '-' }}
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="font-weight-bold">{{ $r->tahunAjaran->tahunajaran ?? '-' }}</span>
                                    <small class="text-primary font-weight-bold text-uppercase" style="letter-spacing: 1px;">
                                        {{ $r->tahunAjaran->semester ?? '-' }}
                                    </small>
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
                                <div class="btn-group">
                                    <a href="{{ route('akademik.rombel.show', $r->id) }}" class="btn btn-sm btn-info shadow-sm" title="Lihat Detail">
                                        <i class="fas fa-search-plus"></i>
                                    </a>
                                    @if(Auth::check() && !Auth::user()->hasRole('GURU') && !Auth::user()->hasRole('SISWA'))
                                    <a href="{{ route('akademik.rombel.edit', $r->id) }}" class="btn btn-sm btn-warning shadow-sm mx-1 text-white" title="Edit Data">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <form action="{{ route('akademik.rombel.destroy', $r->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Rombel ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger shadow-sm" title="Hapus">
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
            {{ $rombel->links() }}
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
