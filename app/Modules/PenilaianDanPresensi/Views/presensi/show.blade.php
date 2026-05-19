@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <x-card title="Detail Presensi" icon="fas fa-eye">
        <dl class="row">
            <dt class="col-sm-3">Siswa</dt>
            <dd class="col-sm-9">{{ $presensi->siswa->nama ?? '-' }}</dd>

            <dt class="col-sm-3">Guru</dt>
            <dd class="col-sm-9">{{ $presensi->guru->nama ?? '-' }}</dd>

            <dt class="col-sm-3">Mata Pelajaran</dt>
            <dd class="col-sm-9">{{ $mapel->nama ?? $mapel->name ?? '-' }}</dd>

            <dt class="col-sm-3">Jadwal Pelajaran</dt>
            <dd class="col-sm-9">@if($jadwal){{ $jadwal->hari }} {{ \Carbon\Carbon::parse($jadwal->jamawal)->format('H:i') }}-{{ \Carbon\Carbon::parse($jadwal->jamakhir)->format('H:i') }}@else - @endif</dd>

            <dt class="col-sm-3">Jam</dt>
            <dd class="col-sm-9">{{ \Carbon\Carbon::parse($presensi->jam)->format('H:i') }}</dd>

            <dt class="col-sm-3">Status</dt>
            <dd class="col-sm-9">
                <span class="badge badge-{{ $presensi->status == 'Hadir' ? 'success' : ($presensi->status == 'Izin' ? 'warning' : ($presensi->status == 'Sakit' ? 'info' : 'danger')) }}">
                    {{ $presensi->status }}
                </span>
            </dd>

            <dt class="col-sm-3">Kategori</dt>
            <dd class="col-sm-9">{{ $presensi->kategori }}</dd>
        </dl>

        <div class="mt-4">
            <a href="{{ route('penilaiandanpresensi.presensi.edit', $presensi->id) }}" class="btn btn-info">
                <i class="fas fa-edit mr-1"></i> Edit
            </a>
            <a href="{{ route('penilaiandanpresensi.presensi.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </x-card>
</div>
@endsection
