@extends('layouts.app')

@section('title', 'Detail Rombel')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Detail Rombel</h1>
                <p class="text-muted">Informasi lengkap rombongan belajar</p>
            </div>
            <x-btn :href="route('akademik.rombel.index')" icon="fas fa-arrow-left" class="btn-secondary shadow-sm">
                Kembali
            </x-btn>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <x-card title="Informasi Utama" icon="fas fa-info-circle" type="primary" outline>
                <div class="table-responsive">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <th class="text-muted small text-uppercase" width="40%">Nama Rombel</th>
                            <td class="font-weight-bold">: {{ $rombel->nama_rombel }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted small text-uppercase">Kelas</th>
                            <td>: {{ $rombel->kelas->nama_kelas ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted small text-uppercase">Tahun Ajaran</th>
                            <td>: {{ $rombel->tahunAjaran->tahunajaran ?? '-' }} ({{ $rombel->tahunAjaran->semester ?? '-' }})</td>
                        </tr>
                        <tr>
                            <th class="text-muted small text-uppercase">Wali Kelas</th>
                            <td>: {{ $rombel->walikelas->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted small text-uppercase">Jumlah Siswa</th>
                            <td>: {{ $rombel->riwayatSiswa->count() }} Siswa</td>
                        </tr>
                    </table>
                </div>
                @if($rombel->keterangan)
                    <hr>
                    <label class="text-muted small text-uppercase font-weight-bold">Keterangan:</label>
                    <p class="mb-0 small">{{ $rombel->keterangan }}</p>
                @endif
                
                @if(Auth::check() && !Auth::user()->hasRole('GURU') && !Auth::user()->hasRole('SISWA'))
                <div class="mt-4">
                    <x-btn :href="route('akademik.rombel.edit', $rombel->id)" class="btn-warning btn-block text-white" icon="fas fa-edit">
                        Edit Rombel
                    </x-btn>
                </div>
                @endif
            </x-card>
        </div>

        <div class="col-lg-8">
            <x-card title="Daftar Siswa" icon="fas fa-user-graduate" type="info" outline>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th width="50" class="text-center">No</th>
                                <th>NIS</th>
                                <th>Nama Lengkap</th>
                                <th class="text-center">L/P</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rombel->riwayatSiswa as $rs)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td><code>{{ $rs->siswa->nis }}</code></td>
                                    <td class="font-weight-bold">{{ $rs->siswa->nama }}</td>
                                    <td class="text-center text-muted">{{ $rs->siswa->jenis_kelamin }}</td>
                                    <td>{{ $rs->siswa->email ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted small italic">Rombel ini belum memiliki siswa.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
