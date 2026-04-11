@extends('layouts.app')

@section('title', 'Beranda Utama')

@section('content')
<div class="container-fluid">
    {{-- 🌟 Personalized Welcome Card (SIAKAD Standard) --}}
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow-sm border-0" style="border-radius: 12px; border-left: 5px solid #007bff; background: #fff;">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-auto d-none d-md-block">
                            <img src="{{ Auth::user()->avatar_url }}" 
                                 class="img-circle elevation-1 border" 
                                 style="width: 75px; height: 75px; object-fit: cover; background: #f4f6f9; padding: 3px;">
                        </div>
                        <div class="col">
                            <h4 class="font-weight-bold text-dark mb-1">
                                Selamat Datang, {{ Auth::user()->name }}!
                            </h4>
                            <p class="text-muted mb-0">
                                Saat ini Anda login sebagai <span class="badge badge-light border px-2 py-1 text-primary">{{ Auth::user()->primary_role ?? 'User' }}</span>.
                                <span class="d-none d-lg-inline ml-1 border-left pl-2">Selamat bekerja di sistem <strong>SIAKAD ALMAHIRA</strong>.</span>
                            </p>
                        </div>
                        <div class="col-auto text-right text-muted d-none d-sm-block">
                            <div class="small font-weight-bold">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</div>
                            <div class="small" id="dashboard-clock">00:00:00</div>
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
                    <span class="info-box-number h4 font-weight-bolder mb-0 text-dark">2024 / 2025</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        {{-- 📘 Overview Section --}}
        <div class="col-lg-8">
            <x-card title="Panduan Sistem Almahira" icon="fas fa-book-open" type="secondary" :outline="true">
                <div class="text-center py-4">
                    <h5 class="text-primary font-weight-bold mb-3 small-text">SELAMAT DATANG DI SIAKAD ALMAHIRA</h5>
                    <p class="text-muted px-lg-5" style="line-height: 1.8; font-size: 0.95rem;">
                        Gunakan menu di samping atau pintasan di sebelah kanan untuk mulai mengelola data akademik sekolah Anda.
                        Pastikan data utama (Guru & Siswa) sudah tervalidasi sebelum menginput nilai atau absensi.
                    </p>
                </div>
                <hr class="my-4">
                <div class="row text-center mb-3">
                    <div class="col-md-4">
                        <div class="p-2">
                            <i class="fas fa-lock text-success mb-2" style="font-size: 1.5rem;"></i>
                            <h6 class="font-weight-bold text-dark small">Keamanan Data</h6>
                            <p class="text-xs text-muted mb-0">Proteksi data akademik terenkripsi aman.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2 border-left border-right">
                            <i class="fas fa-bolt text-warning mb-2" style="font-size: 1.5rem;"></i>
                            <h6 class="font-weight-bold text-dark small">Akses Cepat</h6>
                            <p class="text-xs text-muted mb-0">Navigasi efisien untuk kerja Admin.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2">
                            <i class="fas fa-chart-line text-info mb-2" style="font-size: 1.5rem;"></i>
                            <h6 class="font-weight-bold text-dark small">Laporan Akurat</h6>
                            <p class="text-xs text-muted mb-0">Statistik real-time pendukung keputusan.</p>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>

        {{-- 🚀 Quick Actions Section --}}
        <div class="col-lg-4">
            <x-card title="Aksi Pintar" icon="fas fa-rocket" type="primary" :outline="true" class="shadow-sm">
                <div class="list-group list-group-flush mt-2">
                    <a href="{{ route('pegawaimanager.create') }}" class="list-group-item list-group-item-action border-0 py-3 mb-2 bg-light-soft hover-translate shadow-sm" style="border-radius: 10px;">
                        <div class="d-flex align-items-center">
                            <div class="mr-3 bg-primary text-white p-2 rounded-circle shadow-sm" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div>
                                <span class="d-block font-weight-bold text-dark" style="font-size: 0.9rem;">Tambah Pegawai</span>
                                <small class="text-muted">Guru atau Staf baru</small>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('pegawaimanager.index') }}" class="list-group-item list-group-item-action border-0 py-3 mb-2 bg-light-soft hover-translate shadow-sm" style="border-radius: 10px;">
                        <div class="d-flex align-items-center">
                            <div class="mr-3 bg-success text-white p-2 rounded-circle shadow-sm" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-users-cog"></i>
                            </div>
                            <div>
                                <span class="d-block font-weight-bold text-dark" style="font-size: 0.9rem;">Manajemen SDM</span>
                                <small class="text-muted">Kelola data seluruh pegawai</small>
                            </div>
                        </div>
                    </a>

                    <div class="p-3 mt-2 bg-white rounded border border-warning shadow-xs" style="border-style: dashed !important; border-width: 2px !important;">
                        <div class="d-flex">
                            <i class="fas fa-info-circle text-warning mr-2 mt-1"></i>
                            <span class="text-xs text-muted">Modul tambahan akan muncul secara otomatis di sini setelah diinstal.</span>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');
    
    body { font-family: 'Inter', sans-serif !important; }
    .bg-light-soft { background-color: #f8f9fc; transition: all 0.2s ease; }
    .hover-translate:hover { transform: translateY(-3px); background-color: #fff; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1) !important; z-index: 10; }
    .opacity-2 { opacity: 0.2; }
    .text-xs { font-size: 0.75rem; }
    .small-text { letter-spacing: 1px; }
</style>
@push('scripts')
<script>
    function updateDashboardClock() {
        const now = new Date();
        const display = now.toLocaleTimeString('id-ID', { hour12: false });
        const clockElement = document.getElementById('dashboard-clock');
        if(clockElement) clockElement.textContent = display;
    }
    setInterval(updateDashboardClock, 1000);
    updateDashboardClock();
</script>
@endpush
@endsection
