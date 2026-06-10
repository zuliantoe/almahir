@extends('layouts.app')

@section('title', 'Portal Wali Murid')

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary border-0 shadow-lg" style="border-radius: 20px; background: linear-gradient(135deg, #4361ee 0%, #3f37c9 100%);">
                <div class="card-body p-4 text-white">
                    <div class="row align-items-center">
                        <div class="col">
                            <h2 class="font-weight-bold mb-1">Assalamu'alaikum, Bapak/Ibu {{ $wali->nama }}</h2>
                            <p class="mb-0 opacity-8">Selamat datang di portal orang tua. Pantau perkembangan pendidikan putra-putri Anda di sini.</p>
                        </div>
                        <div class="col-auto d-none d-md-block">
                            <i class="fas fa-user-shield fa-4x opacity-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Children Section --}}
    <h4 class="font-weight-bold text-dark mb-4"><i class="fas fa-child mr-2 text-primary"></i> Data Putra-Putri Anda</h4>

    <div class="row">
        @forelse($siswas as $siswa)
            <div class="col-md-6 col-lg-4">
                <div class="card dashboard-card h-100 border-0 shadow-sm animate__animated animate__fadeInUp" style="border-radius: 18px; overflow: hidden;">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <div class="d-flex align-items-center">
                            <div class="avatar-wrapper mr-3">
                                <img src="{{ $siswa->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($siswa->nama) . '&background=random' }}"
                                     class="rounded-circle shadow-sm border"
                                     style="width: 60px; height: 60px; object-fit: cover;">
                            </div>
                            <div>
                                <h5 class="font-weight-bold mb-0 text-dark">{{ $siswa->nama }}</h5>
                                <span class="badge badge-light text-muted border px-2 py-1" style="font-size: 0.7rem;">NIS: {{ $siswa->nis }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="info-grid mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Kelas</span>
                                <span class="font-weight-bold text-primary small">{{ $siswa->kelas->nama_kelas ?? 'Belum Ditentukan' }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Status</span>
                                <span class="badge badge-success small px-2 py-1 rounded-pill">{{ strtoupper($siswa->status) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Tahun Masuk</span>
                                <span class="text-dark small font-weight-bold">{{ $siswa->tahun_masuk?? '-' }}</span>
                            </div>
                        </div>

                             
                    </div>

                    <div class="card-footer bg-light border-0 text-center py-3">
                        <a href="{{ route('walimurid.portal.siswa-detail', $siswa->id) }}" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm">
                            Lihat Detail Lengkap <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card p-5 text-center shadow-sm border-0" style="border-radius: 20px;">
                    <div class="mb-3">
                        <i class="fas fa-user-slash fa-4x text-muted opacity-3"></i>
                    </div>
                    <h5 class="text-muted">Belum ada data siswa yang dihubungkan dengan akun Anda.</h5>
                    <p class="text-muted small">Silakan hubungi bagian administrasi sekolah.</p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Info Section --}}
    <div class="row mt-5">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm p-4 bg-white" style="border-radius: 18px; border-left: 6px solid #ffc107 !important;">
                <div class="d-flex align-items-center">
                    <div class="mr-3 bg-warning text-white p-3 rounded-circle shadow-sm">
                        <i class="fas fa-info-circle fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="font-weight-bold mb-1">Informasi Penting</h6>
                        <p class="text-muted small mb-0">Portal ini adalah satu-satunya kanal resmi untuk melihat nilai dan kehadiran putra-putri Anda secara real-time. Jika terdapat ketidaksesuaian data, silakan hubungi wali kelas masing-masing.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .shadow-xs { box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    .opacity-2 { opacity: 0.2; }
    .opacity-8 { opacity: 0.8; }
</style>
@endsection
