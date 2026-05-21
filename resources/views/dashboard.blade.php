@extends('layouts.app')

@section('title', 'Beranda Utama')

@section('content')
@php
    $hariIni = \Carbon\Carbon::now()->locale('id')->translatedFormat('l');
    $tanggalIni = \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y');
@endphp
<div class="container-fluid">
    {{-- 🌟 Personalized Welcome Card (SIAKAD Standard) --}}
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm animate__animated animate__fadeIn" style="border-radius: 16px; background: linear-gradient(135deg, #1e3a8a 0%, #3f37c9 60%, #4361ee 100%); color: #fff; overflow: hidden; position: relative;">
                <div style="position: absolute; top: -30px; right: -30px; width: 200px; height: 200px; background: rgba(255,255,255,0.04); border-radius: 50%;"></div>
                <div style="position: absolute; bottom: -50px; right: 80px; width: 150px; height: 150px; background: rgba(255,255,255,0.03); border-radius: 50%;"></div>
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-auto d-none d-md-block">
                            <div style="width: 80px; height: 80px; border-radius: 50%; border: 3px solid rgba(255,255,255,0.3); overflow: hidden; background: rgba(255,255,255,0.1);">
                                <img src="{{ Auth::user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=fff&color=4361ee&size=80' }}"
                                     style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        </div>
                        <div class="col">
                            <p class="mb-1 small" style="color: rgba(255,255,255,0.7); letter-spacing: 1px; text-transform: uppercase; font-size: 0.72rem;">Ahlan Wa Sahlan</p>
                            <h3 class="font-weight-bold mb-1" style="color: #fff;">{{ Auth::user()->name }}</h3>
                            <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
                                <span class="badge" style="background: rgba(255, 255, 255, 0.18); color: #fff; border: 1px solid rgba(255, 255, 255, 0.3); padding: 4px 10px; border-radius: 20px; font-size: 0.72rem;">
                                    <i class="fas fa-user-shield mr-1"></i> {{ Auth::user()->primary_role ?? 'User' }}
                                </span>
                                <span class="badge d-none d-lg-inline-block" style="background: rgba(255, 255, 255, 0.1); color: rgba(255, 255, 255, 0.85); padding: 4px 10px; border-radius: 20px; font-size: 0.72rem;">
                                    <i class="fas fa-laptop-code mr-1"></i> SIAKAD ALMAHIR
                                </span>
                                <span class="badge" style="background: rgba(40,167,69,0.25); color: #34d399; border: 1px solid rgba(40,167,69,0.4); padding: 4px 10px; border-radius: 20px; font-size: 0.72rem;">
                                    <i class="fas fa-check-circle mr-1"></i> Online
                                </span>
                            </div>
                        </div>
                        <div class="col-auto text-right d-none d-sm-block">
                            <div class="small mb-1" style="color: rgba(255,255,255,0.5); font-size: 0.72rem; text-transform: uppercase; letter-spacing: 1px;">Tahun Ajaran {{ $tahunAjaranAktif }}</div>
                            <div class="font-weight-bold" style="color: rgba(255,255,255,0.9); font-size: 0.9rem;">{{ $hariIni }}, {{ $tanggalIni }}</div>
                            <div id="dashboard-clock" class="mt-1" style="color: #4cc9f0; font-size: 1.3rem; font-weight: 700; letter-spacing: 2px; font-family: 'Courier New', monospace;">00:00:00 WIB</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 📊 Dynamic Statistics Row --}}
    <div class="row">
        {{-- Total Guru --}}
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm border-0 bg-white" style="border-radius: 10px;">
                <span class="info-box-icon elevation-1 text-white" 
                      style="background: linear-gradient(135deg, #28a745 0%, #11998e 100%); border-radius: 8px;">
                    <i class="fas fa-chalkboard-teacher"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text text-uppercase text-muted small font-weight-bold">Guru & Pengajar</span>
                    <span class="info-box-number h4 font-weight-bolder mb-0 text-dark">{{ $totalGuru }}</span>
                </div>
            </div>
        </div>
        
        {{-- Total Staf --}}
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3 shadow-sm border-0 bg-white" style="border-radius: 10px;">
                <span class="info-box-icon elevation-1 text-white"
                      style="background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%); border-radius: 8px;">
                    <i class="fas fa-user-shield"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text text-uppercase text-muted small font-weight-bold">Staf Administrasi</span>
                    <span class="info-box-number h4 font-weight-bolder mb-0 text-dark">{{ $totalStaff }}</span>
                </div>
            </div>
        </div>

        {{-- Total Siswa --}}
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3 shadow-sm border-0 bg-white" style="border-radius: 10px;">
                <span class="info-box-icon elevation-1 text-white"
                      style="background: linear-gradient(135deg, #17a2b8 0%, #007bff 100%); border-radius: 8px;">
                    <i class="fas fa-user-graduate"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text text-uppercase text-muted small font-weight-bold">Total Siswa</span>
                    <span class="info-box-number h4 font-weight-bolder mb-0 text-dark">{{ $totalSiswa }}</span>
                </div>
            </div>
        </div>

        {{-- Tahun Ajaran --}}
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3 shadow-sm border-0 bg-white" style="border-radius: 10px;">
                <span class="info-box-icon elevation-1 text-white"
                      style="background: linear-gradient(135deg, #6610f2 0%, #6f42c1 100%); border-radius: 8px;">
                    <i class="fas fa-calendar-alt"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text text-uppercase text-muted small font-weight-bold">Tahun Ajaran</span>
                    <span class="info-box-number h6 font-weight-bold mb-0 text-dark" style="font-size: 0.95rem;">{{ $tahunAjaranAktif }}</span>
                </div>
            </div>
        </div>
    </div>
    
    {{-- 🧩 Unified Module Grid --}}
    <div class="row mt-4 mb-2">
        <div class="col-12">
            <div class="d-flex align-items-center mb-3">
                <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-th-large mr-2 text-primary"></i> Akses Portal Modul</h5>
                <div class="ml-3 flex-grow-1 border-top opacity-2"></div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Modul Akademik --}}
        @if(Auth::user()->hasRole(['SUPER_ADMIN', 'STAFF']))
        <div class="col-12 col-md-6 col-lg-3 mb-4">
            <div class="card h-100 border-0 shadow-sm dashboard-card" style="border-radius: 16px; overflow: hidden;">
                <div class="p-3 d-flex align-items-center text-white" style="background: linear-gradient(135deg, #17a2b8 0%, #007bff 100%);">
                    <i class="fas fa-graduation-cap fa-2x mr-3"></i>
                    <div>
                        <h5 class="font-weight-bold mb-0">Akademik</h5>
                        <small class="opacity-75">Kurikulum & Rombel</small>
                    </div>
                </div>
                <div class="card-body d-flex flex-column justify-content-between p-3">
                    <p class="text-muted text-sm mb-3">Kelola jadwal pelajaran, kurikulum, kelas, mata pelajaran, tingkat kelas, dan rombongan belajar.</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-light text-primary font-weight-bold">{{ $totalRombel }} Rombel</span>
                        <a href="{{ route('akademik.index') }}" class="btn btn-outline-primary btn-sm px-3 font-weight-bold">Masuk Modul</a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Modul Kepegawaian --}}
        @if(Auth::user()->hasRole(['SUPER_ADMIN', 'STAFF']))
        <div class="col-12 col-md-6 col-lg-3 mb-4">
            <div class="card h-100 border-0 shadow-sm dashboard-card" style="border-radius: 16px; overflow: hidden;">
                <div class="p-3 d-flex align-items-center text-white" style="background: linear-gradient(135deg, #28a745 0%, #11998e 100%);">
                    <i class="fas fa-chalkboard-teacher fa-2x mr-3"></i>
                    <div>
                        <h5 class="font-weight-bold mb-0">Kepegawaian</h5>
                        <small class="opacity-75">Data SDM & Pegawai</small>
                    </div>
                </div>
                <div class="card-body d-flex flex-column justify-content-between p-3">
                    <p class="text-muted text-sm mb-3">Manajemen data profil seluruh guru/staf, kenaikan jabatan, gaji berkala, serta status dinas.</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-light text-success font-weight-bold">{{ $totalSdm }} Pegawai</span>
                        <a href="{{ route('pegawaimanager.dashboard') }}" class="btn btn-outline-success btn-sm px-3 font-weight-bold">Masuk Modul</a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Modul Kehadiran --}}
        @if(Auth::user()->hasRole(['SUPER_ADMIN', 'STAFF']))
        <div class="col-12 col-md-6 col-lg-3 mb-4">
            <div class="card h-100 border-0 shadow-sm dashboard-card" style="border-radius: 16px; overflow: hidden;">
                <div class="p-3 d-flex align-items-center text-white" style="background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%);">
                    <i class="fas fa-fingerprint fa-2x mr-3"></i>
                    <div>
                        <h5 class="font-weight-bold mb-0">Kehadiran</h5>
                        <small class="opacity-75">Presensi & Izin Cuti</small>
                    </div>
                </div>
                <div class="card-body d-flex flex-column justify-content-between p-3">
                    <p class="text-muted text-sm mb-3">Pencatatan presensi harian sidik jari, permohonan izin cuti, serta monitoring sakit pegawai.</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-light text-warning font-weight-bold">{{ $totalCalonPegawai }} Calon Pegawai</span>
                        <a href="{{ route('absensi.index') }}" class="btn btn-outline-warning btn-sm px-3 font-weight-bold">Masuk Modul</a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Modul Penilaian & Presensi --}}
        @if(Auth::user()->hasRole(['SUPER_ADMIN', 'GURU']))
        <div class="col-12 col-md-6 col-lg-3 mb-4">
            <div class="card h-100 border-0 shadow-sm dashboard-card" style="border-radius: 16px; overflow: hidden;">
                <div class="p-3 d-flex align-items-center text-white" style="background: linear-gradient(135deg, #6f42c1 0%, #a020f0 100%);">
                    <i class="fas fa-user-check fa-2x mr-3"></i>
                    <div>
                        <h5 class="font-weight-bold mb-0">Penilaian & Presensi</h5>
                        <small class="opacity-75">Presensi Siswa & Nilai</small>
                    </div>
                </div>
                <div class="card-body d-flex flex-column justify-content-between p-3">
                    <p class="text-muted text-sm mb-3">Kelola presensi harian siswa/santri, penilaian akademik, penilaian tahfidz quran, dan cetak raport.</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-light text-purple font-weight-bold" style="color: #6f42c1;">Raport Digital</span>
                        <a href="{{ route('penilaiandanpresensi.index') }}" class="btn btn-outline-purple btn-sm px-3 font-weight-bold" style="border-color: #6f42c1; color: #6f42c1;">Masuk Modul</a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Modul Keuangan --}}
        @if(Auth::user()->hasRole(['SUPER_ADMIN', 'KEUANGAN']))
        <div class="col-12 col-md-6 col-lg-3 mb-4">
            <div class="card h-100 border-0 shadow-sm dashboard-card" style="border-radius: 16px; overflow: hidden;">
                <div class="p-3 d-flex align-items-center text-white" style="background: linear-gradient(135deg, #20c997 0%, #00ffff 100%);">
                    <i class="fas fa-money-bill-wave fa-2x mr-3"></i>
                    <div>
                        <h5 class="font-weight-bold mb-0">Keuangan</h5>
                        <small class="opacity-75">SPP & Transaksi</small>
                    </div>
                </div>
                <div class="card-body d-flex flex-column justify-content-between p-3">
                    <p class="text-muted text-sm mb-3">Pengelolaan pembayaran SPP, uang saku santri, pencatatan transaksi masuk/keluar, dan laporan kas.</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-light text-teal font-weight-bold">Kas & SPP</span>
                        <a href="{{ route('keuangan.index') }}" class="btn btn-outline-teal btn-sm px-3 font-weight-bold">Masuk Modul</a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Modul Aset & Asrama --}}
        @if(Auth::user()->hasRole(['SUPER_ADMIN', 'STAF_TU']))
        <div class="col-12 col-md-6 col-lg-3 mb-4">
            <div class="card h-100 border-0 shadow-sm dashboard-card" style="border-radius: 16px; overflow: hidden;">
                <div class="p-3 d-flex align-items-center text-white" style="background: linear-gradient(135deg, #fd7e14 0%, #ff007f 100%);">
                    <i class="fas fa-building fa-2x mr-3"></i>
                    <div>
                        <h5 class="font-weight-bold mb-0">Aset & Asrama</h5>
                        <small class="opacity-75">Kamar & Aset Madrasah</small>
                    </div>
                </div>
                <div class="card-body d-flex flex-column justify-content-between p-3">
                    <p class="text-muted text-sm mb-3">Monitoring aset fisik, pengajuan inventaris baru, serta pembagian kamar dan jadwal piket asrama.</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-light text-orange font-weight-bold">{{ $totalKamar }} Kamar</span>
                        <a href="{{ route('manajemenasetdanasrama.index') }}" class="btn btn-outline-orange btn-sm px-3 font-weight-bold" style="color: #fd7e14; border-color: #fd7e14;">Masuk Modul</a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Modul Pengaturan --}}
        @if(Auth::user()->hasRole('SUPER_ADMIN'))
        <div class="col-12 col-md-6 col-lg-3 mb-4">
            <div class="card h-100 border-0 shadow-sm dashboard-card" style="border-radius: 16px; overflow: hidden;">
                <div class="p-3 d-flex align-items-center text-white" style="background: linear-gradient(135deg, #6c757d 0%, #343a40 100%);">
                    <i class="fas fa-users-cog fa-2x mr-3"></i>
                    <div>
                        <h5 class="font-weight-bold mb-0">Pengaturan</h5>
                        <small class="opacity-75">Konfigurasi & User</small>
                    </div>
                </div>
                <div class="card-body d-flex flex-column justify-content-between p-3">
                    <p class="text-muted text-sm mb-3">Kelola kredensial pengguna, pembagian peran (roles), izin akses modul, dan log audit keamanan.</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-light text-secondary font-weight-bold">System Config</span>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm px-3 font-weight-bold">Masuk Modul</a>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- 📊 Detailed Module Summaries & Fast Insights --}}
    <div class="row mt-4">
        {{-- Akademik & Kepegawaian Summaries --}}
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-header border-0 bg-transparent pt-4 px-4">
                    <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-chart-line mr-2 text-info"></i> Ringkasan Cepat Modul Utama</h5>
                </div>
                <div class="card-body px-4 pb-4 pt-2">
                    <div class="row">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="p-3 rounded bg-light border-0" style="border-radius: 12px !important;">
                                <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-graduation-cap text-info mr-2"></i> Sistem Akademik</h6>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted text-sm">Total Kelas Terdaftar</span>
                                    <span class="font-weight-bold text-dark text-sm">{{ $totalKelas }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted text-sm">Rombongan Belajar (Rombel)</span>
                                    <span class="font-weight-bold text-dark text-sm">{{ $totalRombel }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted text-sm">Mata Pelajaran (Mapel)</span>
                                    <span class="font-weight-bold text-dark text-sm">{{ $totalMapel }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded bg-light border-0" style="border-radius: 12px !important;">
                                <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-building text-orange mr-2" style="color: #fd7e14;"></i> Manajemen Aset & Asrama</h6>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted text-sm">Kamar Asrama Terdaftar</span>
                                    <span class="font-weight-bold text-dark text-sm">{{ $totalKamar }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted text-sm">Inventaris Aset Sekolah</span>
                                    <span class="font-weight-bold text-dark text-sm">{{ $totalAset }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted text-sm">Laporan Kerusakan Aktif</span>
                                    <span class="font-weight-bold text-danger text-sm">
                                        {{ $totalKerusakan }} 
                                        @if($totalKerusakan > 0)
                                            <i class="fas fa-exclamation-triangle ml-1 animate__animated animate__flash animate__infinite"></i>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="p-3 rounded bg-light border-0 d-flex align-items-center justify-content-between" style="border-radius: 12px !important;">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user-plus text-success mr-3 fa-lg"></i>
                                    <div>
                                        <span class="font-weight-bold text-dark text-sm d-block">Pendaftaran Pegawai Baru</span>
                                        <small class="text-muted text-xs">Total data lamaran masuk di modul kepegawaian</small>
                                    </div>
                                </div>
                                <span class="badge badge-success font-weight-bold px-3 py-2" style="font-size: 0.85rem;">{{ $totalCalonPegawai }} Calon Pegawai</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 📘 Quick Guides / Action Cards --}}
        <div class="col-lg-4 mb-4">
            <x-card title="Panduan Sistem Almahir" icon="fas fa-book-open" type="secondary" :outline="true" class="h-100 border-0 shadow-sm">
                <div class="text-center py-2">
                    <h5 class="text-primary font-weight-bold mb-3 small-text" style="letter-spacing: 1px;">SELAMAT DATANG DI SIAKAD ALMAHIR</h5>
                    <p class="text-muted text-sm" style="line-height: 1.6;">
                        Gunakan menu di samping atau pintasan modul di atas untuk mulai mengelola data akademik sekolah Anda.
                        Pastikan data utama (Guru & Siswa) sudah tervalidasi sebelum menginput nilai atau absensi.
                    </p>
                </div>
                <hr class="my-3">
                <div class="list-group list-group-flush">
                    <a href="{{ route('pegawaimanager.create') }}" class="list-group-item list-group-item-action border-0 py-2 mb-2 bg-light-soft hover-translate shadow-xs" style="border-radius: 10px;">
                        <div class="d-flex align-items-center">
                            <div class="mr-3 bg-primary text-white p-2 rounded-circle shadow-sm" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-user-plus" style="font-size: 0.85rem;"></i>
                            </div>
                            <div>
                                <span class="d-block font-weight-bold text-dark text-xs" style="font-size: 0.8rem;">Tambah Pegawai</span>
                                <small class="text-muted text-xs">Guru atau Staf baru</small>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('pegawaimanager.index') }}" class="list-group-item list-group-item-action border-0 py-2 mb-2 bg-light-soft hover-translate shadow-xs" style="border-radius: 10px;">
                        <div class="d-flex align-items-center">
                            <div class="mr-3 bg-success text-white p-2 rounded-circle shadow-sm" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-users-cog" style="font-size: 0.85rem;"></i>
                            </div>
                            <div>
                                <span class="d-block font-weight-bold text-dark text-xs" style="font-size: 0.8rem;">Manajemen SDM</span>
                                <small class="text-muted text-xs">Kelola data seluruh pegawai</small>
                            </div>
                        </div>
                    </a>
                </div>
            </x-card>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');
    
    body { font-family: 'Outfit', sans-serif !important; }
    .bg-light-soft { background-color: #f8f9fc; transition: all 0.2s ease; }
    .hover-translate:hover { transform: translateY(-3px); background-color: #fff; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1) !important; z-index: 10; }
    .opacity-2 { opacity: 0.2; }
    .text-xs { font-size: 0.72rem; }
    .btn-outline-purple {
        border-color: #6f42c1;
        color: #6f42c1;
    }
    .btn-outline-purple:hover {
        background-color: #6f42c1;
        color: white;
    }
    .btn-outline-orange {
        border-color: #fd7e14;
        color: #fd7e14;
    }
    .btn-outline-orange:hover {
        background-color: #fd7e14;
        color: white;
    }
    .btn-outline-teal {
        border-color: #20c997;
        color: #20c997;
    }
    .btn-outline-teal:hover {
        background-color: #20c997;
        color: white;
    }
</style>
@push('scripts')
<script>
    function updateDashboardClock() {
        const now = new Date();
        const display = now.toLocaleTimeString('id-ID', { hour12: false });
        const clockElement = document.getElementById('dashboard-clock');
        if(clockElement) clockElement.textContent = display + ' WIB';
    }
    setInterval(updateDashboardClock, 1000);
    updateDashboardClock();
</script>
@endpush
@endsection
