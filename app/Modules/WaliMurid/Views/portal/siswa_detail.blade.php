@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    {{-- Back Button & Header --}}
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('walimurid.portal.dashboard') }}" class="btn btn-light rounded-circle shadow-sm mr-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h3 class="font-weight-bold mb-0">Detail Profil Siswa</h3>
            <p class="text-muted mb-0">Informasi lengkap putra-putri Anda</p>
        </div>
    </div>

    <div class="row">
        {{-- Profile Card --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px; overflow: hidden;">
                <div class="card-header bg-gradient-primary p-5 text-center border-0">
                    <div class="avatar-container mb-3">
                        <img src="{{ $siswa->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($siswa->nama) . '&background=random&size=128' }}" 
                             class="rounded-circle border border-white shadow" 
                             style="width: 120px; height: 120px; object-fit: cover; border-width: 4px !important;">
                    </div>
                    <h4 class="text-white font-weight-bold mb-1">{{ $siswa->nama }}</h4>
                    <span class="badge badge-light px-3 py-2 rounded-pill shadow-sm">
                        <i class="fas fa-id-card mr-1 text-primary"></i> NIS: {{ $siswa->nis }}
                    </span>
                </div>
                <div class="card-body p-4">
                    <div class="list-group list-group-flush border-0">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 border-light">
                            <span class="text-muted"><i class="fas fa-envelope mr-2"></i> Email</span>
                            <span class="font-weight-bold">{{ $siswa->email ?? '-' }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 border-light">
                            <span class="text-muted"><i class="fas fa-phone mr-2"></i> Telepon</span>
                            <span class="font-weight-bold">{{ $siswa->telepon ?? '-' }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 border-0">
                            <span class="text-muted"><i class="fas fa-venus-mars mr-2"></i> Jenis Kelamin</span>
                            <span class="font-weight-bold">{{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Stats --}}
            <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <h6 class="font-weight-bold mb-3">Ringkasan Akademik</h6>
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="p-3 bg-light rounded" style="border-radius: 15px;">
                                <div class="text-primary h4 font-weight-bold mb-0">A</div>
                                <div class="text-muted x-small">Predikat Rata-rata</div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="p-3 bg-light rounded" style="border-radius: 15px;">
                                <div class="text-success h4 font-weight-bold mb-0">98%</div>
                                <div class="text-muted x-small">Kehadiran</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detailed Info --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
                <div class="card-header bg-white p-4 border-light">
                    <h5 class="font-weight-bold mb-0"><i class="fas fa-info-circle mr-2 text-primary"></i> Informasi Detail</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="text-muted small font-weight-bold text-uppercase">Tempat, Tanggal Lahir</label>
                            <p class="h6 font-weight-bold">{{ $siswa->tempat_lahir ?? '-' }}, {{ $siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('d F Y') : '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="text-muted small font-weight-bold text-uppercase">Tahun Masuk</label>
                            <p class="h6 font-weight-bold">{{ $siswa->tahun_masuk ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="text-muted small font-weight-bold text-uppercase">Kelas Saat Ini</label>
                            <p class="h6 font-weight-bold text-primary">{{ $siswa->kelas->nama_kelas ?? 'Belum Ditentukan' }}</p>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="text-muted small font-weight-bold text-uppercase">Status Siswa</label>
                            <div>
                                <span class="badge badge-success px-3 py-2 rounded-pill">{{ strtoupper($siswa->status) }}</span>
                            </div>
                        </div>
                        <div class="col-12 mb-4">
                            <label class="text-muted small font-weight-bold text-uppercase">Alamat Lengkap</label>
                            <p class="h6 font-weight-bold">{{ $siswa->alamat ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Placeholder for additional sections --}}
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="icon-box bg-info-light text-info mr-3 p-3 rounded-circle" style="background: rgba(23, 162, 184, 0.1);">
                                <i class="fas fa-book fa-lg"></i>
                            </div>
                            <div>
                                <h6 class="font-weight-bold mb-1">Mata Pelajaran</h6>
                                <p class="text-muted small mb-0">Lihat jadwal pelajaran aktif</p>
                            </div>
                            <i class="fas fa-chevron-right ml-auto text-muted"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="icon-box bg-warning-light text-warning mr-3 p-3 rounded-circle" style="background: rgba(255, 193, 7, 0.1);">
                                <i class="fas fa-wallet fa-lg"></i>
                            </div>
                            <div>
                                <h6 class="font-weight-bold mb-1">Pembayaran</h6>
                                <p class="text-muted small mb-0">Status SPP dan biaya lainnya</p>
                            </div>
                            <i class="fas fa-chevron-right ml-auto text-muted"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #4361ee 0%, #3f37c9 100%);
    }
    .x-small { font-size: 0.65rem; font-weight: 700; text-transform: uppercase; }
    .shadow-sm { box-shadow: 0 4px 15px rgba(0,0,0,0.05) !important; }
</style>
@endsection
