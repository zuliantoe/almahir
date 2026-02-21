@extends('layouts.app')

@section('title', 'Data Kelas')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="row mb-3">
        <div class="col-sm-6">
            <h1 class="m-0">Datar Kelas</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('akademik.kelas.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Tambah Kelas
            </a>
        </div>
    </div>

    {{-- Card --}}
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-school mr-1"></i>
                Daftar Kelas
            </h3>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="text-center">
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Kelas</th>
                        <th>Total Jadwal</th>
                        <th>Total Kurikulum</th>
                        <th width="20%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kelas as $k)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $k->nama }}</td>
                            <td class="text-center">{{ $k->jadwal_pelajaran_count ?? 0 }}</td>
                            <td class="text-center">{{ $k->kurikulum_count ?? 0 }}</td>
                            <td class="text-center">

                                <a href="{{ route('akademik.kelas.show', $k->id) }}"
                                   class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <a href="{{ route('akademik.kelas.edit', $k->id) }}"
                                   class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('akademik.kelas.destroy', $k->id) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Yakin hapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Belum ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>
@endsection
