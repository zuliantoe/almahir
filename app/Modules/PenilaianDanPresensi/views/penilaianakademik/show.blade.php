@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <x-card title="Detail Penilaian Akademik" icon="fas fa-eye">
        <dl class="row">
            <dt class="col-sm-3">Siswa</dt>
            <dd class="col-sm-9">{{ $penilaianAkademik->siswa->nama ?? '-' }}</dd>

            <dt class="col-sm-3">Guru</dt>
            <dd class="col-sm-9">{{ $penilaianAkademik->guru->nama ?? '-' }}</dd>

            <dt class="col-sm-3">Mata Pelajaran</dt>
            <dd class="col-sm-9">{{ $penilaianAkademik->mataPelajaran->nama ?? '-' }}</dd>

            <dt class="col-sm-3">Tahun Ajaran</dt>
            <dd class="col-sm-9">{{ $penilaianAkademik->tahunAjaran->tahunajaran ?? '-' }}</dd>

            <dt class="col-sm-3">Nilai</dt>
            <dd class="col-sm-9">{{ $penilaianAkademik->nilai }}</dd>
        </dl>

        <div class="mt-4">
            <a href="{{ route('penilaiandanpresensi.penilaianakademik.edit', $penilaianAkademik->id) }}" class="btn btn-info">
                <i class="fas fa-edit mr-1"></i> Edit
            </a>
            <a href="{{ route('penilaiandanpresensi.penilaianakademik.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </x-card>
</div>
@endsection
