@extends('layouts.app')

@section('title', 'Detail Kelas')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="row mb-3">
        <div class="col-sm-6">
            <h1 class="m-0">Detail Kelas</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('akademik.kelas.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    {{-- Card --}}
    <div class="card card-info card-outline">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-info-circle mr-1"></i>
                Informasi Kelas
            </h3>
        </div>

        <div class="card-body">

            <div class="row">
                <div class="col-md-6">
                    <strong>Nama Kelas</strong>
                    <p class="text-muted">{{ $kelas->nama }}</p>
                </div>

                <div class="col-md-3">
                    <strong>Total Jadwal</strong>
                    <p class="text-muted">{{ $kelas->jadwal_pelajaran_count ?? 0 }}</p>
                </div>

                <div class="col-md-3">
                    <strong>Total Kurikulum</strong>
                    <p class="text-muted">{{ $kelas->kurikulum_count ?? 0 }}</p>
                </div>
            </div>

        </div>

        <div class="card-footer text-right">
            <a href="{{ route('akademik.kelas.edit', $kelas->id) }}"
               class="btn btn-warning">
                <i class="fas fa-edit mr-1"></i> Edit
            </a>
        </div>
    </div>

</div>
@endsection
