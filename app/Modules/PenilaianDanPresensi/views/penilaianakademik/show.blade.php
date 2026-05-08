@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <div class="row page-titles mb-4">
        <div class="col-md-5 align-self-center">
            <h3 class="text-primary"><i class="fas fa-eye mr-2"></i> {{ $title }}</h3>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <a href="{{ route('penilaiandanpresensi.penilaianakademik.index') }}" class="btn btn-outline-primary btn-sm shadow-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header bg-primary py-3 d-flex justify-content-between align-items-center">
                    <h5 class="text-white mb-0"><i class="fas fa-info-circle mr-2"></i> Rincian Nilai Akademik</h5>
                    <span class="badge badge-light text-primary px-3 py-2" style="border-radius: 10px;">
                        <i class="fas fa-calendar-alt mr-1"></i> {{ $penilaianAkademik->tahunAjaran->tahunajaran ?? '-' }}
                    </span>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <tbody>
                            <tr>
                                <th class="bg-light pl-4" style="width: 30%; border-top: none;"><i class="fas fa-user-graduate mr-2 text-primary"></i> Santri</th>
                                <td class="pl-4 font-weight-bold" style="border-top: none;">{{ $penilaianAkademik->siswa->nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light pl-4"><i class="fas fa-school mr-2 text-primary"></i> Kelas</th>
                                <td class="pl-4"><span class="badge badge-info shadow-sm">{{ $penilaianAkademik->siswa->kelas->nama_kelas ?? '-' }}</span></td>
                            </tr>
                            <tr>
                                <th class="bg-light pl-4"><i class="fas fa-user-tie mr-2 text-primary"></i> Guru Pengampu</th>
                                <td class="pl-4">{{ $penilaianAkademik->guru->nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light pl-4"><i class="fas fa-book mr-2 text-primary"></i> Mata Pelajaran</th>
                                <td class="pl-4 font-weight-bold">{{ $penilaianAkademik->mataPelajaran->nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light pl-4"><i class="fas fa-bullseye mr-2 text-primary"></i> KKM</th>
                                <td class="pl-4 text-danger font-weight-bold">{{ $penilaianAkademik->kkm ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light pl-4" style="height: 80px;"><i class="fas fa-star mr-2 text-primary"></i> Nilai Akhir</th>
                                <td class="pl-4">
                                    <h2 class="text-primary mb-0 font-weight-bold">{{ $penilaianAkademik->nilai }}</h2>
                                    @php
                                        $status = $penilaianAkademik->nilai >= $penilaianAkademik->kkm ? 'Tuntas' : 'Belum Tuntas';
                                        $badgeClass = $penilaianAkademik->nilai >= $penilaianAkademik->kkm ? 'success' : 'danger';
                                    @endphp
                                    <span class="badge badge-{{ $badgeClass }}">{{ $status }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white py-3 text-right border-top">
                    <a href="{{ route('penilaiandanpresensi.penilaianakademik.edit', $penilaianAkademik->id) }}" class="btn btn-info px-4 shadow-sm">
                        <i class="fas fa-edit mr-1"></i> Edit Nilai Ini
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
