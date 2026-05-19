@extends('layouts.app')

@section('title', $title ?? 'Data Siswa')

@section('content')
<div class="container-fluid">
    <div class="row page-titles mb-4">
        <div class="col-md-5 align-self-center">
            <h3 class="text-primary font-weight-bold"><i class="fas fa-users mr-2"></i> {{ $title ?? 'Data Siswa' }}</h3>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <a href="{{ route('siswa.create') }}" class="btn btn-primary shadow-sm px-4" style="border-radius: 50px;">
                <i class="fas fa-plus-circle mr-1"></i> Tambah Santri Baru
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 border-0">NIS & Foto</th>
                            <th class="border-0">Nama Lengkap</th>
                            <th class="border-0">Kelas</th>
                            <th class="border-0">Status</th>
                            <th class="border-0 text-center px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswas as $siswa)
                        <tr>
                            <td class="px-4 align-middle">
                                <div class="d-flex align-items-center">
                                    <div class="mr-3">
                                        @if($siswa->foto)
                                            <img src="{{ asset('storage/' . $siswa->foto) }}" class="rounded-circle" width="45" height="45" style="object-fit: cover; border: 2px solid #0d6efd;">
                                        @else
                                            <div class="rounded-circle bg-primary-light d-flex align-items-center justify-content-center text-primary font-weight-bold" style="width: 45px; height: 45px; border: 2px solid #0d6efd;">
                                                {{ substr($siswa->nama, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-weight-bold text-dark">{{ $siswa->nis }}</div>
                                        <small class="text-muted">ID: {{ substr($siswa->id, 0, 8) }}...</small>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <div class="font-weight-bold text-dark">{{ $siswa->nama }}</div>
                                <small class="text-muted"><i class="far fa-envelope mr-1"></i> {{ $siswa->email }}</small>
                            </td>
                            <td class="align-middle">
                                <span class="badge badge-info px-3 py-1" style="border-radius: 6px;">{{ $siswa->kelas->nama_kelas ?? ($siswa->kelas->nama_rombel ?? '-') }}</span>
                            </td>
                            <td class="align-middle">
                                @php
                                    $statusClass = [
                                        'aktif' => 'success',
                                        'lulus' => 'primary',
                                        'keluar' => 'danger',
                                        'cuti' => 'warning'
                                    ][$siswa->status] ?? 'secondary';
                                @endphp
                                <span class="badge badge-{{ $statusClass }} px-3 py-1" style="border-radius: 6px; text-transform: capitalize;">{{ $siswa->status ?? 'Aktif' }}</span>
                            </td>
                            <td class="align-middle text-center px-4">
                                <a href="{{ route('siswa.show', $siswa->id) }}" class="btn btn-outline-primary btn-sm px-3 shadow-sm" style="border-radius: 50px;">
                                    <i class="fas fa-eye mr-1"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="mb-3 opacity-25">
                                    <i class="fas fa-user-friends fa-4x text-primary"></i>
                                </div>
                                <h5 class="text-muted font-weight-bold">Belum ada data santri.</h5>
                                <p class="text-muted small">Silakan tambah santri pertama Anda untuk memulai.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
