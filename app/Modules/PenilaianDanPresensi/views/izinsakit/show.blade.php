@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <x-card title="Detail Izin/Sakit" icon="fas fa-info-circle">
        <dl class="row">
            <dt class="col-sm-3">Siswa</dt>
            <dd class="col-sm-9">{{ $izinSakit->siswa->nama ?? '-' }}</dd>

            <dt class="col-sm-3">Kelas</dt>
            <dd class="col-sm-9">{{ $izinSakit->kelas->nama_kelas ?? '-' }}</dd>

            <dt class="col-sm-3">Jenis</dt>
            <dd class="col-sm-9">{{ $izinSakit->jenis }}</dd>

            <dt class="col-sm-3">Tanggal Mulai</dt>
            <dd class="col-sm-9">{{ optional($izinSakit->tgl_mulai)->format('d M Y') }}</dd>

            <dt class="col-sm-3">Tanggal Selesai</dt>
            <dd class="col-sm-9">{{ optional($izinSakit->tgl_selesai)->format('d M Y') }}</dd>
        </dl>

        <div class="mt-4">
            <a href="{{ route('penilaiandanpresensi.izinsakit.edit', $izinSakit->id) }}" class="btn btn-info">
                <i class="fas fa-edit mr-1"></i> Edit
            </a>
            <a href="{{ route('penilaiandanpresensi.izinsakit.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </x-card>
</div>
@endsection
