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
            <dd class="col-sm-9">
                {{ $izinSakit->jenis }}
                <span class="badge badge-secondary ml-2">{{ $izinSakit->tipe_izin }}</span>
            </dd>

            @if($izinSakit->tipe_izin === 'Per Matpel' && $izinSakit->mataPelajaran)
            <dt class="col-sm-3">Mata Pelajaran</dt>
            <dd class="col-sm-9">{{ $izinSakit->mataPelajaran->nama }}</dd>
            @endif

            <dt class="col-sm-3">Tanggal Mulai</dt>
            <dd class="col-sm-9">{{ optional($izinSakit->tgl_mulai)->format('d M Y') }}</dd>

            <dt class="col-sm-3">Tanggal Selesai</dt>
            <dd class="col-sm-9">{{ optional($izinSakit->tgl_selesai)->format('d M Y') }}</dd>

            @if($izinSakit->keterangan)
            <dt class="col-sm-3">Keterangan / Alasan</dt>
            <dd class="col-sm-9">{{ $izinSakit->keterangan }}</dd>
            @endif

            @if($izinSakit->bukti_foto)
            <dt class="col-sm-3">Bukti (Foto/Surat)</dt>
            <dd class="col-sm-9">
                <a href="{{ asset('storage/' . $izinSakit->bukti_foto) }}" target="_blank" class="btn btn-sm btn-info">
                    <i class="fas fa-image mr-1"></i> Lihat Bukti
                </a>
            </dd>
            @endif
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
