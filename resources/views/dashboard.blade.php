@extends('layouts.app')

@section('title', 'Dashboard')

@section('content-header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Dashboard</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item active">Home</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    {{-- Info boxes --}}
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-user-graduate"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Siswa</span>
                    <span class="info-box-number">0</span>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-chalkboard-teacher"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Guru</span>
                    <span class="info-box-number">0</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-door-open"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Kelas</span>
                    <span class="info-box-number">0</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-calendar-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Tahun Ajaran</span>
                    <span class="info-box-number">2024/2025</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Welcome Card --}}
        <div class="col-md-8">
            <x-card title="Selamat Datang di SIAKAD" type="primary">
                <p>
                    <strong>SIAKAD</strong> (Sistem Informasi Akademik) adalah aplikasi yang dirancang untuk
                    mengelola data akademik sekolah secara efisien dan terintegrasi.
                </p>
                <h5>Fitur Utama:</h5>
                <ul>
                    <li><strong>Manajemen Data Siswa</strong> - Kelola data siswa dengan mudah</li>
                    <li><strong>Manajemen Data Guru</strong> - Kelola data guru dan staff</li>
                    <li><strong>Penilaian</strong> - Input dan rekap nilai siswa</li>
                    <li><strong>Absensi</strong> - Monitoring kehadiran siswa</li>
                    <li><strong>Keuangan</strong> - Manajemen pembayaran SPP dan biaya lainnya</li>
                </ul>
                
                <x-slot name="footer">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i>
                        Hubungi administrator jika Anda memerlukan bantuan.
                    </small>
                </x-slot>
            </x-card>
        </div>

        {{-- Quick Actions --}}
        <div class="col-md-4">
            <x-card title="Aksi Cepat" type="info" :outline="true">
                <div class="list-group list-group-flush">
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-user-plus mr-2 text-primary"></i>
                        Tambah Siswa Baru
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-clipboard-list mr-2 text-success"></i>
                        Input Nilai
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-user-check mr-2 text-warning"></i>
                        Rekap Absensi
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-money-bill-wave mr-2 text-info"></i>
                        Pembayaran SPP
                    </a>
                </div>
            </x-card>

            @if(config('app.debug'))
            <x-card title="Developer Tools" type="secondary" :outline="true">
                <x-btn class="btn-info btn-block" icon="fas fa-palette" href="{{ url('/dev/ui-guide') }}">
                    UI Style Guide
                </x-btn>
            </x-card>
            @endif
        </div>
    </div>
@endsection
