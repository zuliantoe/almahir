@extends('layouts.app')

@section('title', 'Data Kelas')

@section('content')
<div class="container-fluid">
    {{-- Success/Error Messages --}}
    @if(session('success'))
        <x-alert type="success" :message="session('success')" dismissible />
    @endif

    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-gray-800">Manajemen Kelas</h1>
            @if(Auth::check() && !Auth::user()->hasRole('GURU') && !Auth::user()->hasRole('SISWA'))
            <x-btn :href="route('akademik.kelas.create')" icon="fas fa-plus">
                Tambah Kelas Baru
            </x-btn>
            @endif
        </div>
    </div>

    {{-- Daftar Kelas --}}
    <x-card title="Daftar Kelas Aktif" icon="fas fa-school" type="primary" outline>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th>Kode</th>
                        <th>Nama Kelas</th>
                        <th class="text-center">Tingkat</th>
                        @if(Auth::check() && !Auth::user()->hasRole('GURU') && !Auth::user()->hasRole('SISWA'))
                        <th width="150px" class="text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kelas as $k)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td><code>{{ $k->kode_kelas ?? '-' }}</code></td>
                            <td><strong>{{ $k->nama_kelas }}</strong></td>
                            <td class="text-center">
                                <span class="badge badge-info">{{ $k->tingkat->nama_tingkat ?? '-' }}</span>
                            </td>
                            @if(Auth::check() && !Auth::user()->hasRole('GURU') && !Auth::user()->hasRole('SISWA'))
                            <td class="text-center">
                                <div class="btn-group">
                                    <x-btn :href="route('akademik.kelas.show', $k->id)" size="sm" class="btn-info" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </x-btn>
                                    <x-btn :href="route('akademik.kelas.edit', $k->id)" size="sm" class="btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </x-btn>
                                    <form action="{{ route('akademik.kelas.destroy', $k->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <x-btn type="submit" size="sm" class="btn-danger btn-delete" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </x-btn>
                                    </form>
                                </div>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-school fa-2x mb-3"></i><br>
                                Belum ada data kelas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</div>
@endsection
