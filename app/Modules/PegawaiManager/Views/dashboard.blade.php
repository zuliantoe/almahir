@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    {{-- Stats Row --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalSdm }}</h3>
                    <p>Total Pegawai</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('pegawaimanager.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $totalGuru }}</h3>
                    <p>Total Guru</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <a href="{{ route('pegawaimanager.index') }}?type=guru" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $hadirHariIni }}</h3>
                    <p>Hadir Hari Ini</p>
                </div>
                <div class="icon">
                    <i class="fas fa-fingerprint"></i>
                </div>
                <a href="{{ route('absensi.manage.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $izinHariIni }}</h3>
                    <p>Izin/Sakit Hari Ini</p>
                </div>
                <div class="icon">
                    <i class="fas fa-envelope-open-text"></i>
                </div>
                <a href="{{ route('perizinan.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-6">
            <x-card title="Pintasan Aksi" icon="fas fa-rocket" type="primary" :outline="true">
                <div class="list-group list-group-flush">
                    <a href="{{ route('pegawaimanager.create') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="fas fa-user-plus text-primary mr-3 fa-lg"></i>
                        <div>
                            <h6 class="mb-0 font-weight-bold">Tambah Pegawai Baru</h6>
                            <small class="text-muted">Masukkan data guru atau staf baru ke dalam sistem.</small>
                        </div>
                    </a>
                    <a href="{{ route('pegawaimanager.types.index') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="fas fa-tags text-success mr-3 fa-lg"></i>
                        <div>
                            <h6 class="mb-0 font-weight-bold">Manajemen Tipe Pegawai</h6>
                            <small class="text-muted">Kelola jenis-jenis jabatan atau tipe kepegawaian.</small>
                        </div>
                    </a>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
