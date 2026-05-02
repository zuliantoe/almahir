@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <div class="row page-titles mb-4">
        <div class="col-md-5 align-self-center">
            <h3 class="text-warning font-weight-bold"><i class="fas fa-eye mr-2"></i> {{ $title }}</h3>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <a href="{{ route('penilaiandanpresensi.penilaiantahfidz.index') }}" class="btn btn-outline-warning btn-sm shadow-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header bg-warning py-3 d-flex justify-content-between align-items-center">
                    <h5 class="text-dark mb-0 font-weight-bold"><i class="fas fa-quran mr-2"></i> Rincian Setoran Hafalan</h5>
                    <span class="badge badge-dark px-3 py-2" style="border-radius: 10px;">
                        <i class="fas fa-calendar-alt mr-1 text-warning"></i> {{ $penilaianTahfidz->tanggal ? $penilaianTahfidz->tanggal->format('d M Y') : '-' }}
                    </span>
                </div>
                <div class="card-body p-0" style="background-color: #fffdf7;">
                    <div class="p-4 text-center border-bottom bg-white">
                        <div class="display-4 text-warning mb-2"><i class="fas fa-user-circle"></i></div>
                        <h4 class="font-weight-bold mb-1">{{ $penilaianTahfidz->siswa->nama ?? '-' }}</h4>
                        <span class="badge badge-info px-3 py-2" style="border-radius: 20px;">Kelas: {{ $penilaianTahfidz->kelas->nama_kelas ?? '-' }}</span>
                    </div>

                    <div class="row p-4 text-center">
                        <div class="col-md-5">
                            <small class="text-muted text-uppercase font-weight-bold">Dari (Awal)</small>
                            <h4 class="text-dark mt-2">{{ $penilaianTahfidz->surat_awal }}</h4>
                            <div class="badge badge-light p-2 font-weight-bold">Ayat: {{ $penilaianTahfidz->ayat_awal }}</div>
                        </div>
                        <div class="col-md-2 align-self-center py-3">
                            <i class="fas fa-long-arrow-alt-right fa-2x text-warning opacity-50"></i>
                        </div>
                        <div class="col-md-5">
                            <small class="text-muted text-uppercase font-weight-bold">Sampai (Akhir)</small>
                            <h4 class="text-dark mt-2">{{ $penilaianTahfidz->surat_akhir }}</h4>
                            <div class="badge badge-light p-2 font-weight-bold">Ayat: {{ $penilaianTahfidz->ayat_akhir }}</div>
                        </div>
                    </div>

                    <hr class="m-0">

                    <div class="row p-4 align-items-center bg-white">
                        <div class="col-6 text-center border-right">
                            <small class="text-muted text-uppercase font-weight-bold d-block mb-2">Nilai</small>
                            <h1 class="text-primary font-weight-bold mb-0 display-3">{{ $penilaianTahfidz->nilai }}</h1>
                        </div>
                        <div class="col-6 text-center">
                            <small class="text-muted text-uppercase font-weight-bold d-block mb-2">Status Capaian</small>
                            @if($penilaianTahfidz->status_capaian == 'Lolos')
                                <div class="text-success h3 mb-0">
                                    <i class="fas fa-check-circle fa-2x d-block mb-2"></i>
                                    <span class="font-weight-bold">LOLOS</span>
                                </div>
                            @else
                                <div class="text-danger h3 mb-0">
                                    <i class="fas fa-times-circle fa-2x d-block mb-2"></i>
                                    <span class="font-weight-bold">TIDAK LOLOS</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="p-4 bg-light border-top text-center">
                        <small class="text-muted d-block mb-1">Guru Pengampu:</small>
                        <h6 class="font-weight-bold text-dark mb-0"><i class="fas fa-user-tie mr-1 text-warning"></i> {{ $penilaianTahfidz->guru->nama ?? '-' }}</h6>
                    </div>
                </div>
                <div class="card-footer bg-white py-3 text-right border-top">
                    <a href="{{ route('penilaiandanpresensi.penilaiantahfidz.edit', $penilaianTahfidz->id) }}" class="btn btn-warning px-4 font-weight-bold shadow-sm">
                        <i class="fas fa-edit mr-1"></i> Edit Data Ini
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection