@extends('layouts.app')

@section('title', 'Set Jadwal Tes')

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>Set Jadwal Tes</h1>
    </div>
</div>
@endsection

@section('content')

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            {{ $pendaftaran->nama_lengkap }}
        </h3>
    </div>

    <div class="card-body">

        {{-- FORM TAMBAH JADWAL --}}
        <form method="POST" action="{{ url('pendaftaran/admin/jadwal/store', $pendaftaran->id) }}">
            @csrf

            <div class="form-group">
                <label>Nama Tes</label>
                <input type="text" name="nama_tes" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Tanggal</label>
                <input type="date" name="tanggal" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Jam</label>
                <input type="time" name="jam" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Metode</label>
                <select name="metode" class="form-control">
                    <option value="offline">Offline</option>
                    <option value="online">Online</option>
                </select>
            </div>

            <div class="form-group">
                <label>Lokasi (jika offline)</label>
                <input type="text" name="lokasi" class="form-control">
            </div>

            <div class="form-group">
                <label>Link (jika online)</label>
                <input type="url" name="link" class="form-control">
            </div>

            <button type="submit" class="btn btn-success">
                Simpan Jadwal
            </button>
        </form>

        <hr>

        {{-- DAFTAR JADWAL --}}
        <h5>Daftar Jadwal Tes</h5>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama Tes</th>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Metode</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendaftaran->seleksis as $seleksi)
                <tr>
                    <td>{{ $seleksi->nama_tes }}</td>
                    <td>{{ $seleksi->tanggal }}</td>
                    <td>{{ $seleksi->jam }}</td>
                    <td>{{ ucfirst($seleksi->metode) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">Belum ada jadwal</td>
                </tr>
                @endforelse
            </tbody>
        </table>

    </div>

    <div class="card-footer text-right">
        <a href="/pendaftaran/admin/pendaftaran'" class="btn btn-secondary">
            Kembali
        </a>
    </div>

</div>

@endsection