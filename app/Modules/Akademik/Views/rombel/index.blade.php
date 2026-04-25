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

    <x-card title="Rombongan Belajar" icon="fas fa-users-class" type="primary" outline>
        <div class="table-responsive">
            <table class="table table-hover align-items-center">
                <thead class="thead-light">
                    <tr>
                        <th width="50" class="text-center">No</th>
                        <th>Nama Rombel</th>
                        <th>Kelas</th>
                        <th>Tahun Ajaran</th>
                        <th>Wali Kelas</th>
                        <th class="text-center">Jumlah Siswa</th>
                        <th width="150" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rombel as $r)
                        <tr>
                            <td class="text-center">{{ ($rombel->currentPage()-1) * $rombel->perPage() + $loop->iteration }}</td>
                            <td>
                                <span class="font-weight-bold text-primary">{{ $r->nama_rombel }}</span>
                            </td>
                            <td>{{ $r->kelas->nama_kelas ?? '-' }}</td>
                            <td>
                                <span class="badge badge-info">{{ $r->tahunAjaran->tahunajaran ?? '-' }} ({{ $r->tahunAjaran->semester ?? '-' }})</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm mr-2 bg-light rounded-circle text-center" style="width: 30px; height: 30px; line-height: 30px;">
                                        <i class="fas fa-user-tie text-secondary"></i>
                                    </div>
                                    <span>{{ $r->walikelas->nama ?? 'Belum ditentukan' }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-pill badge-secondary px-3">{{ $r->riwayatSiswa->count() }} Siswa</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <x-btn :href="route('akademik.rombel.show', $r->id)" size="sm" class="btn-info" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </x-btn>
                                    @if(Auth::check() && !Auth::user()->hasRole('GURU') && !Auth::user()->hasRole('SISWA'))
                                    <x-btn :href="route('akademik.rombel.edit', $r->id)" size="sm" class="btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </x-btn>
                                    <form action="{{ route('akademik.rombel.destroy', $r->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <x-btn type="submit" size="sm" class="btn-danger btn-delete" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </x-btn>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-users-class fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Belum ada data rombel.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $rombel->links() }}
        </div>
    </x-card>
</div>
@endsection
