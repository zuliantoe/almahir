@extends('layouts.app')

@section('title', 'Detail Tahun Ajaran')

@section('content')
<div class="container-fluid">
    <x-card title="Detail Tahun Ajaran" type="info">

        {{-- Header Info --}}
        <div class="row">
            <div class="col-md-6">
                <div class="info-box bg-light">
                    <div class="info-box-content">
                        <span class="info-box-text">Tahun Ajaran</span>
                        <span class="info-box-number">{{ $tahunAjaran->tahun_ajaran }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-box bg-light">
                    <div class="info-box-content">
                        <span class="info-box-text">Status</span>
                        <span class="info-box-number">
                            @if($tahunAjaran->status)
                                <span class="badge badge-success">AKTIF</span>
                            @else
                                <span class="badge badge-secondary">TIDAK AKTIF</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detail Table --}}
        <div class="table-responsive">
            <table class="table table-bordered">
                <tr>
                    <th width="200">ID</th>
                    <td>{{ $tahunAjaran->id }}</td>
                </tr>
                <tr>
                    <th>Tahun Ajaran</th>
                    <td>{{ $tahunAjaran->tahun_ajaran }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        @if($tahunAjaran->status)
                            <span class="badge badge-success">Aktif</span>
                            <small class="text-muted">(Tahun ajaran sedang berjalan)</small>
                        @else
                            <span class="badge badge-secondary">Tidak Aktif</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Dibuat Pada</th>
                    <td>{{ $tahunAjaran->created_at->format('d F Y H:i:s') }}</td>
                </tr>
                <tr>
                    <th>Diperbarui Pada</th>
                    <td>{{ $tahunAjaran->updated_at->format('d F Y H:i:s') }}</td>
                </tr>
            </table>
        </div>

        {{-- Related Data Stats --}}
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $tahunAjaran->kalenderAkademik()->count() }}</h3>
                        <p>Kalender Akademik</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $tahunAjaran->jadwalPelajaran()->count() }}</h3>
                        <p>Jadwal Pelajaran</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $tahunAjaran->kurikulum()->count() }}</h3>
                        <p>Kurikulum</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-book"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $tahunAjaran->rombel()->count() }}</h3>
                        <p>Rombel</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer Buttons --}}
        <x-slot name="footer">
            <div class="d-flex justify-content-between">
                <a href="{{ route('akademik.tahun-ajaran.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <div>
                    <a href="{{ route('akademik.tahun-ajaran.edit', $tahunAjaran->id) }}"
                       class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                </div>
            </div>
        </x-slot>

    </x-card>
</div>
@endsection
