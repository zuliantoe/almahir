@extends('layouts.app')

@section('title', $title ?? 'Detail Siswa')

@section('content')
<div class="container-fluid">
    <div class="row page-titles mb-4">
        <div class="col-md-5 align-self-center">
            <h3 class="text-primary font-weight-bold"><i class="fas fa-user-graduate mr-2"></i> {{ $title ?? 'Detail Siswa' }}</h3>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <a href="{{ route('siswa.index') }}" class="btn btn-outline-secondary shadow-sm px-4" style="border-radius: 50px;">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
            <a href="{{ route('siswa.edit', $siswa->id) }}" class="btn btn-primary shadow-sm px-4 ml-2" style="border-radius: 50px;">
                <i class="fas fa-edit mr-1"></i> Edit Siswa
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Kolom Profil Kiri -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center mb-4" style="border-radius: 20px; overflow: hidden;">
                <div class="card-body p-5">
                    <div class="mb-4">
                        @if($siswa->foto)
                            <img src="{{ asset('storage/' . $siswa->foto) }}" class="rounded-circle shadow-sm" width="150" height="150" style="object-fit: cover; border: 4px solid #f8f9fa;">
                        @else
                            <div class="rounded-circle bg-primary-light d-flex align-items-center justify-content-center text-primary font-weight-bold shadow-sm mx-auto" style="width: 150px; height: 150px; font-size: 3rem; border: 4px solid #f8f9fa;">
                                {{ substr($siswa->nama, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <h4 class="font-weight-bold text-dark mb-1">{{ $siswa->nama }}</h4>
                    <p class="text-muted mb-3"><i class="fas fa-id-card mr-1"></i> NIS: {{ $siswa->nis }}</p>
                    
                    @php
                        $statusClass = [
                            'aktif' => 'success',
                            'lulus' => 'primary',
                            'keluar' => 'danger',
                            'cuti' => 'warning'
                        ][$siswa->status] ?? 'secondary';
                    @endphp
                    <span class="badge badge-{{ $statusClass }} px-4 py-2 shadow-sm" style="border-radius: 50px; font-size: 0.9rem; text-transform: capitalize;">
                        <i class="fas fa-circle mr-1" style="font-size: 0.6rem;"></i> {{ $siswa->status ?? 'Aktif' }}
                    </span>
                </div>
            </div>

            <!-- Kartu QR Digital -->
            <div class="card border-0 shadow-sm text-center mb-4" style="border-radius: 20px; background: linear-gradient(135deg, #4361ee 0%, #3f37c9 100%); color: white;">
                <div class="card-body p-4">
                    <h5 class="font-weight-bold mb-1">KARTU SANTRI DIGITAL</h5>
                    <p class="small opacity-75 mb-4">Gunakan QR ini untuk presensi</p>
                    
                    <div class="d-inline-block p-3 bg-white shadow-sm mb-4" style="border-radius: 20px;">
                        <!-- QR Code dari API Gratis goqr.me -->
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={{ $siswa->nis }}&margin=10" alt="QR Code {{ $siswa->nis }}" style="width: 100%; max-width: 200px;">
                    </div>
                    
                    <button class="btn btn-light btn-block font-weight-bold text-primary shadow-sm" style="border-radius: 50px;" onclick="window.print()">
                        <i class="fas fa-print mr-1"></i> Cetak Kartu
                    </button>
                </div>
            </div>
        </div>

        <!-- Kolom Data Kanan -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
                <div class="card-header bg-white py-4 border-0">
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="fas fa-info-circle text-primary mr-2"></i> Informasi Santri</h5>
                </div>
                <div class="card-body px-4 pb-4 pt-0">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="text-muted small font-weight-bold text-uppercase mb-1">Nama Lengkap</label>
                            <div class="font-weight-bold text-dark" style="font-size: 1.1rem;">{{ $siswa->nama }}</div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="text-muted small font-weight-bold text-uppercase mb-1">Email User</label>
                            <div class="text-dark">{{ $siswa->email }}</div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="text-muted small font-weight-bold text-uppercase mb-1">Tempat, Tanggal Lahir</label>
                            <div class="text-dark">{{ $siswa->tempat_lahir }}, {{ \Carbon\Carbon::parse($siswa->tanggal_lahir)->translatedFormat('d F Y') }}</div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="text-muted small font-weight-bold text-uppercase mb-1">Jenis Kelamin</label>
                            <div class="text-dark">
                                @if($siswa->jenis_kelamin == 'L')
                                     Laki-laki
                                @elseif($siswa->jenis_kelamin == 'P')
                                   Perempuan
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="text-muted small font-weight-bold text-uppercase mb-1">Nomor Telepon</label>
                            <div class="text-dark">{{ $siswa->telepon ?: '-' }}</div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="text-muted small font-weight-bold text-uppercase mb-1">Kelas Saat Ini</label>
                            <div class="text-dark">
                                <span class="badge badge-info px-3 py-1" style="border-radius: 6px;">
                                    {{ $siswa->kelas->nama_kelas ?? ($siswa->kelas->nama_rombel ?? 'Belum memiliki kelas') }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-12 mb-4">
                            <label class="text-muted small font-weight-bold text-uppercase mb-1">Tahun Masuk</label>
                            <div class="text-dark">{{ $siswa->tahun_masuk }}</div>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label class="text-muted small font-weight-bold text-uppercase mb-1">Alamat Lengkap</label>
                            <div class="text-dark p-3 bg-light" style="border-radius: 12px; border: 1px dashed #ced4da;">
                                {{ $siswa->alamat ?: 'Alamat belum diisi.' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-primary-light { background-color: rgba(13, 110, 25, 0.05); }
    .opacity-75 { opacity: 0.75; }
</style>
@endsection
