@extends('layouts.app')

@section('title',$title)

@section('content')
<div class="container-fluid">
    <x-card title="Detail Penilaian Tahfidz" icon="fas fa-info-circle">
        <dl class="row">
            <dt class="col-sm-3">Siswa</dt>
            <dd class="col-sm-9">{{ $penilaianTahfidz->siswa->nama ?? '-' }}</dd>

            <dt class="col-sm-3">Kelas</dt>
            <dd class="col-sm-9">{{ $penilaianTahfidz->kelas->nama_kelas ?? '-' }}</dd>

            <dt class="col-sm-3">Tanggal</dt>
            <dd class="col-sm-9">{{ optional($penilaianTahfidz->tanggal)->format('d M Y') }}</dd>

            <dt class="col-sm-3">Surat</dt>
            <dd class="col-sm-9">{{ $penilaianTahfidz->surat_awal }} - {{ $penilaianTahfidz->surat_akhir }}</dd>

            <dt class="col-sm-3">Ayat</dt>
            <dd class="col-sm-9">{{ $penilaianTahfidz->ayat_awal }} - {{ $penilaianTahfidz->ayat_akhir }}</dd>

            <dt class="col-sm-3">Guru</dt>
            <dd class="col-sm-9">{{ $penilaianTahfidz->guru->nama ?? '-' }}</dd>

            <dt class="col-sm-3">Nilai</dt>
            <dd class="col-sm-9">{{ $penilaianTahfidz->nilai }}</dd>
        </dl>

        <div class="mt-3">
            <a href="{{ route('penilaiandanpresensi.penilaiantahfidz.edit', $penilaianTahfidz->id) }}" class="btn btn-info">
                <i class="fas fa-edit mr-1"></i> Edit
            </a>
            <a href="{{ route('penilaiandanpresensi.penilaiantahfidz.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </x-card>
</div>
@endsection