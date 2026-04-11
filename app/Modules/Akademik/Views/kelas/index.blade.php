@extends('layouts.app')

@section('title', 'Data Kelas')

@include('akademik::components.style')

@section('content')
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2 class="font-weight-bold text-dark mb-0">Manajemen Kelas</h2>
            <a href="{{ route('akademik.kelas.create') }}" class="btn btn-primary btn-modern">
                <i class="fas fa-plus mr-1"></i> Tambah Kelas Baru
            </a>
        </div>
    </div>

    {{-- Card --}}
    <div class="card card-modern">
        <div class="card-header bg-gradient-purple">
            <h3 class="card-title text-white">
                <i class="fas fa-school mr-2"></i>
                Daftar Kelas Aktif
            </h3>
        </div>

        <div class="card-body p-0 table-responsive">
            <table class="table table-hover table-modern text-nowrap">
                <thead class="text-center">
                    <tr>
                        <th width="5%">No</th>
                        <th class="text-left">Nama Kelas</th>
                        <th>Total Jadwal</th>
                        <th>Total Kurikulum</th>
                        <th width="20%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kelas as $k)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="font-weight-bold text-dark">
                                <span class="badge badge-light badge-modern mr-2">{{ $k->nama }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-info badge-modern">{{ $k->jadwal_pelajaran_count ?? 0 }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-primary badge-modern">{{ $k->kurikulum_count ?? 0 }}</span>
                            </td>
                            <td class="text-center">

                                <a href="{{ route('akademik.kelas.show', $k->id) }}"
                                   class="btn btn-info btn-sm btn-modern" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <a href="{{ route('akademik.kelas.edit', $k->id) }}"
                                   class="btn btn-warning btn-sm btn-modern text-white" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('akademik.kelas.destroy', $k->id) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Yakin hapus data jadwal kelas ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm btn-modern" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="fas fa-school fa-3x text-muted mb-3"></i>
                                <p class="text-muted font-weight-bold">Belum ada data kelas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>
@endsection
