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

    {{-- Info Box --}}
    <div class="row">
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-primary elevation-1">
                    <i class="fas fa-user-graduate"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Pendaftar</span>
                    <span class="info-box-number">
                        {{ $data->count() }}
                    </span>
                </div>
            </div>
        </div>
    </div>


    {{-- Table Card --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Pendaftar</h3>
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

                    @forelse ($data as $item)
                        <tr>
                            <td>{{ $item->nama_lengkap }}</td>
                            <td>{{ $item->tempat_lahir }}</td>
                            <td>{{ $item->no_hp }}</td>
                            <td>
                                @if ($item->status == 'pending')
                                    <span class="badge badge-warning">Pending</span>
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

                                <a href="/pendaftaran/admin/pendaftaran/{{ $item->id }}/jadwal"
                                    class="btn btn-sm btn-success">
                                    <i class="fas fa-calendar-plus"></i> Set Jadwal
                                </a>

                            </td>
                        </tr>
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
