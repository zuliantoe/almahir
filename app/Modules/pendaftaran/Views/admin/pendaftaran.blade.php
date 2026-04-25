@extends('layouts.app')

@section('title', 'Data Pendaftaran')

@section('content-header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Data Pendaftaran Siswa</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item">Admin</li>
                <li class="breadcrumb-item active">Pendaftaran</li>
            </ol>
        </div>
    </div>
@endsection


@section('content')

    {{-- Table Card --}}
    <div class="card">
        <div class="card-header pb-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="card-title m-0">Daftar Pendaftar</h3>
                <a href="/pendaftaran/admin/template-seleksi" class="btn btn-sm btn-primary ml-auto">
                    <i class="fas fa-list-alt"></i> Kelola Template Tes
                </a>
            </div>
            
            <form method="GET" action="/pendaftaran/admin/pendaftaran">
                <div class="form-row align-items-end">
                    <div class="col-md-3">
                        <label>Filter Status</label>
                        <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                            <option value="all" {{ $currentStatus == 'all' ? 'selected' : '' }}>Semua Status</option>
                            <option value="pending" {{ $currentStatus == 'pending' ? 'selected' : '' }}>Ditunda</option>
                            <option value="diproses" {{ $currentStatus == 'diproses' ? 'selected' : '' }}>Diproses</option>
                            <option value="diterima" {{ $currentStatus == 'diterima' ? 'selected' : '' }}>Diterima</option>
                            <option value="ditolak" {{ $currentStatus == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Tempat Lahir</th>
                        <th>No HP</th>
                        <th>Status</th>
                        <th width="180">Aksi</th>
                    </tr>
                </thead>
                <tbody>

                    @forelse ($data as $groupName => $items)
                        <tr class="bg-light">
                            <td colspan="5"><strong>{{ $groupName }}</strong></td>
                        </tr>
                        @foreach ($items as $item)
                            <tr>
                                <td>{{ $item->nama_lengkap }}</td>
                                <td>{{ $item->tempat_lahir }}</td>
                                <td>{{ $item->no_hp }}</td>
                                <td>
                                    @if ($item->status == 'pending')
                                        <span class="badge badge-warning">Ditunda</span>
                                    @elseif($item->status == 'diproses')
                                        <span class="badge badge-info">Diproses</span>
                                    @elseif($item->status == 'diterima')
                                        <span class="badge badge-success">Diterima</span>
                                    @else
                                        <span class="badge badge-danger">Ditolak</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="/pendaftaran/admin/pendaftaran/{{ $item->id }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> Lihat
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">
                                Belum ada data pendaftaran
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
    </div>

@endsection
