@extends('layouts.app')

@section('title', 'Detail Pendaftaran')

@section('content-header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Detail Pendaftaran</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item">Admin</li>
                <li class="breadcrumb-item">
                    <a href="/pendaftaran/admin/pendaftaran">Pendaftaran</a>
                </li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>
        </div>
    </div>
@endsection


@section('content')

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                Data Lengkap Siswa
            </h3>
        </div>

        <div class="card-body">

            {{-- DATA SISWA --}}
            <h5 class="mb-3"><strong>Data Siswa</strong></h5>
            <table class="table table-bordered mb-4">
                <tr>
                    <th width="30%">NISN</th>
                    <td>{{ $pendaftaran->nisn }}</td>
                </tr>
                <tr>
                    <th>Nama Lengkap</th>
                    <td>{{ $pendaftaran->nama_lengkap }}</td>
                </tr>
                <tr>
                    <th>Tempat Lahir</th>
                    <td>{{ $pendaftaran->tempat_lahir }}</td>
                </tr>
                <tr>
                    <th>Tanggal Lahir</th>
                    <td>{{ $pendaftaran->tanggal_lahir }}</td>
                </tr>
                <tr>
                    <th>Jenis Kelamin</th>
                    <td>
                        {{ $pendaftaran->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                    </td>
                </tr>
                <tr>
                    <th>Berat Badan</th>
                    <td>{{ $pendaftaran->berat_badan }} kg</td>
                </tr>
                <tr>
                    <th>Tinggi Badan</th>
                    <td>{{ $pendaftaran->tinggi_badan }} cm</td>
                </tr>
                <tr>
                    <th>Riwayat Sakit</th>
                    <td>{{ $pendaftaran->riwayat_sakit }}</td>
                </tr>
            </table>


            {{-- DATA ALAMAT --}}
            <h5 class="mb-3"><strong>Data Alamat</strong></h5>
            <table class="table table-bordered mb-4">
                <tr>
                    <th width="30%">Kelurahan</th>
                    <td>{{ $pendaftaran->kelurahan }}</td>
                </tr>
                <tr>
                    <th>Kecamatan</th>
                    <td>{{ $pendaftaran->kecamatan }}</td>
                </tr>
                <tr>
                    <th>Kota</th>
                    <td>{{ $pendaftaran->kota }}</td>
                </tr>
                <tr>
                    <th>Provinsi</th>
                    <td>{{ $pendaftaran->provinsi }}</td>
                </tr>
                <tr>
                    <th>Alamat Lengkap</th>
                    <td>{{ $pendaftaran->alamat }}</td>
                </tr>
            </table>


            {{-- DATA ORANG TUA --}}
            <h5 class="mb-3"><strong>Data Orang Tua</strong></h5>
            <table class="table table-bordered mb-4">
                <tr>
                    <th width="30%">Nama Ayah</th>
                    <td>{{ $pendaftaran->nama_ayah }}</td>
                </tr>
                <tr>
                    <th>Pekerjaan Ayah</th>
                    <td>{{ $pendaftaran->pekerjaan_ayah }}</td>
                </tr>
                <tr>
                    <th>No HP</th>
                    <td>{{ $pendaftaran->no_hp }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $pendaftaran->email }}</td>
                </tr>
            </table>


            {{-- STATUS & ADMIN --}}
            <h5 class="mb-3"><strong>Status Pendaftaran</strong></h5>
            <table class="table table-bordered">
                <tr>
                    <th width="30%">Status</th>
                    <td>
                        @if ($pendaftaran->status == 'pending')
                            <span class="badge badge-warning">Pending</span>
                        @elseif($pendaftaran->status == 'diproses')
                            <span class="badge badge-info">Diproses</span>
                        @elseif($pendaftaran->status == 'diterima')
                            <span class="badge badge-success">Diterima</span>
                        @else
                            <span class="badge badge-danger">Ditolak</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Tanggal Daftar</th>
                    <td>{{ $pendaftaran->tanggal_daftar }}</td>
                </tr>
                <tr>
                    <th>Tanggal Diterima</th>
                    <td>{{ $pendaftaran->tanggal_diterima ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Catatan</th>
                    <td>{{ $pendaftaran->catatan ?? '-' }}</td>
                </tr>
            </table>

        </div>
        

        <div class="card-footer text-right">
            <a href="/pendaftaran/admin/pendaftaran" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

    </div>

@endsection
