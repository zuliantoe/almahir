@extends('layouts.app')

@section('title', $title)

@push('styles')
<style>
    .profile-card {
        border-radius: 24px;
        background: linear-gradient(135deg, #1e293b, #0f172a);
        color: white;
        position: relative;
        overflow: hidden;
        border: none;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }
    .profile-card::before {
        content: ''; position: absolute;
        top: -50%; left: -50%; width: 200%; height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0) 70%);
        transform: rotate(30deg); z-index: 0; pointer-events: none;
    }
    .profile-avatar {
        width: 150px; height: 150px;
        border-radius: 50%;
        border: 5px solid rgba(255,255,255,0.15);
        box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        object-fit: cover;
        z-index: 1; position: relative;
        transition: transform 0.3s ease;
    }
    .profile-avatar:hover { transform: scale(1.05); }
    
    .info-card {
        border-radius: 20px; border: 1px solid #e2e8f0; background: white;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03); transition: transform 0.3s ease;
        overflow: hidden;
    }
    .info-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(0,0,0,0.08); }
    
    .stat-item { 
        padding: 1.5rem 1rem; text-align: center; border-radius: 16px; background: #f8fafc;
        border: 1px solid #f1f5f9; transition: all 0.3s ease;
    }
    .stat-item:hover { background: white; box-shadow: 0 8px 20px rgba(0,0,0,0.05); transform: translateY(-3px); }
    
    .badge-role { background: rgba(255,255,255,0.15); backdrop-filter: blur(5px); border: 1px solid rgba(255,255,255,0.3); color: white; padding: 8px 20px; border-radius: 30px; font-weight: 700; letter-spacing: 1px; font-size: 0.85rem;}
    
    .btn-gradient-primary { background: linear-gradient(135deg, #4361ee, #4cc9f0); color: white; border: none; transition: all 0.3s ease; }
    .btn-gradient-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(67, 97, 238, 0.4); color: white; }
    
    .info-list-item { padding: 14px 0; border-bottom: 1px dashed rgba(255,255,255,0.15); display: flex; justify-content: space-between; align-items: center; z-index: 1; position: relative;}
    .info-list-item:last-child { border-bottom: none; }
    
    .icon-box { width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-right: 15px; }
    .icon-box-primary { background: #e0f2fe; color: #0284c7; }
    .icon-box-success { background: #dcfce7; color: #15803d; }
    .icon-box-info { background: #cffafe; color: #0891b2; }
    .icon-box-warning { background: #fef9c3; color: #a16207; }
    .icon-box-danger { background: #fee2e2; color: #b91c1c; }
    
    .card-title-modern { font-weight: 800; color: #1e293b; font-size: 1.25rem; display: flex; align-items: center;}
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    
    {{-- Header / Tombol Kembali --}}
    <div class="mb-4">
        <a href="{{ route('pegawaimanager.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm font-weight-bold" style="border: 1px solid #e2e8f0;">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Pegawai
        </a>
    </div>

    <div class="row">
        {{-- Kolom Kiri: Ringkasan Profil --}}
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card profile-card">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <img class="profile-avatar"
                             src="{{ $pegawai->user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($pegawai->nama).'&size=150' }}"
                             alt="Foto Profil">
                    </div>

                    <h3 class="text-center font-weight-bold mb-2 text-white" style="font-family: 'Outfit', sans-serif;">{{ $pegawai->nama }}</h3>
                    <p class="text-center mb-4">
                        <span class="badge-role">
                            <i class="fas fa-shield-alt mr-1"></i> {{ $pegawai->user->primary_role ?? 'Pegawai' }}
                        </span>
                    </p>

                    <div class="mb-4">
                        <div class="info-list-item">
                            <b style="color: rgba(255,255,255,0.7); font-weight: 500;">Kategori / Tipe</b> 
                            <span class="badge" style="background: rgba(255,255,255,0.9); color: #0f172a; padding: 6px 12px; border-radius: 8px;">{{ $pegawai->typePegawai->nama_type ?? '-' }}</span>
                        </div>
                        <div class="info-list-item">
                            <b style="color: rgba(255,255,255,0.7); font-weight: 500;">Status Akun</b> 
                            @php $status = $pegawai->user->account_status ?? 'inactive'; @endphp
                            @if($status === 'active')
                                <span class="badge" style="background: #2dc653; color: white; padding: 6px 12px; border-radius: 8px;"><i class="fas fa-check-circle mr-1"></i> Aktif</span>
                            @else
                                <span class="badge" style="background: #ef233c; color: white; padding: 6px 12px; border-radius: 8px;"><i class="fas fa-times-circle mr-1"></i> Nonaktif</span>
                            @endif
                        </div>
                        <div class="info-list-item">
                            <b style="color: rgba(255,255,255,0.7); font-weight: 500;">Bergabung Sejak</b> 
                            <span style="font-weight: 600;">{{ $pegawai->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center mt-2">
                        <a href="{{ route('pegawaimanager.edit', $pegawai->id) }}" class="btn btn-white btn-block rounded-pill shadow-sm font-weight-bold py-2" style="background: white; color: #1e293b; z-index: 1;">
                            <i class="fas fa-user-edit mr-2"></i> Edit Profil Pegawai
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Detail Informasi --}}
        <div class="col-xl-8 col-lg-7">
            
            {{-- Statistik Absensi --}}
            <div class="card info-card mb-4">
                <div class="card-header bg-white p-4 border-0 pb-0">
                    <h5 class="card-title-modern"><i class="fas fa-chart-pie text-success mr-3"></i> Statistik Kehadiran (Bulan Ini)</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="stat-item border-bottom-0" style="border-top: 4px solid #10b981;">
                                <h2 class="font-weight-bold mb-1" style="color: #10b981; font-family: 'Outfit', sans-serif; font-size: 2.5rem;">{{ $absensiStats['hadir'] ?? 0 }}</h2>
                                <span class="text-muted small text-uppercase font-weight-bold letter-spacing-1"><i class="fas fa-check-circle mr-1 text-success"></i> Total Hadir</span>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="stat-item border-bottom-0" style="border-top: 4px solid #f59e0b;">
                                <h2 class="font-weight-bold mb-1" style="color: #f59e0b; font-family: 'Outfit', sans-serif; font-size: 2.5rem;">{{ $absensiStats['terlambat'] ?? 0 }}</h2>
                                <span class="text-muted small text-uppercase font-weight-bold letter-spacing-1"><i class="fas fa-clock mr-1 text-warning"></i> Terlambat</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-item border-bottom-0" style="border-top: 4px solid #0ea5e9;">
                                <h2 class="font-weight-bold mb-1" style="color: #0ea5e9; font-family: 'Outfit', sans-serif; font-size: 2.5rem;">{{ $absensiStats['izin'] ?? 0 }}</h2>
                                <span class="text-muted small text-uppercase font-weight-bold letter-spacing-1"><i class="fas fa-file-invoice mr-1 text-info"></i> Izin / Sakit</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Statistik Izin & Cuti Tahunan --}}
            <div class="card info-card mb-4">
                <div class="card-header bg-white p-4 border-0 pb-0 d-flex flex-wrap justify-content-between align-items-center">
                    <h5 class="card-title-modern mb-2 mb-sm-0"><i class="fas fa-calendar-alt text-primary mr-3"></i> Rekap Izin & Cuti <span class="text-primary ml-1">({{ now()->year }})</span></h5>
                    <a href="{{ route('perizinan.index', ['pegawai_id' => $pegawai->id]) }}" class="btn btn-sm btn-light rounded-pill px-4 shadow-sm font-weight-bold" style="border: 1px solid #e2e8f0;">
                        <i class="fas fa-external-link-alt mr-1"></i> Buka Manajemen Perizinan
                    </a>
                </div>
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="rounded shadow-sm d-flex flex-column justify-content-center p-4 text-center h-100" style="background: linear-gradient(135deg, #4361ee, #4cc9f0); color: white;">
                                <div class="small text-uppercase font-weight-bold mb-2" style="opacity: 0.8; letter-spacing: 1px;">Sisa Jatah Cuti</div>
                                <h2 class="font-weight-bold mb-0" style="font-family: 'Outfit', sans-serif; font-size: 3rem;">{{ $izinStats['sisa_cuti'] }} <span class="h5 font-weight-normal" style="opacity: 0.8;">Hari</span></h2>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="row g-3">
                                <div class="col-6 col-sm-3 mb-3 mb-sm-0">
                                    <div class="stat-item p-3 h-100 d-flex flex-column justify-content-center">
                                        <div class="h3 font-weight-bold text-dark mb-0">{{ $izinStats['total'] }} <small class="text-muted font-weight-normal text-xs">Hr</small></div>
                                        <div class="text-xs text-muted font-weight-bold text-uppercase mt-1">Total Izin</div>
                                    </div>
                                </div>
                                <div class="col-6 col-sm-3 mb-3 mb-sm-0">
                                    <div class="stat-item p-3 h-100 d-flex flex-column justify-content-center" style="background: #f0fdf4;">
                                        <div class="h3 font-weight-bold text-success mb-0">{{ $izinStats['disetujui'] }} <small class="text-muted font-weight-normal text-xs">Hr</small></div>
                                        <div class="text-xs text-success font-weight-bold text-uppercase mt-1">Disetujui</div>
                                    </div>
                                </div>
                                <div class="col-6 col-sm-3">
                                    <div class="stat-item p-3 h-100 d-flex flex-column justify-content-center" style="background: #fffbeb;">
                                        <div class="h3 font-weight-bold text-warning mb-0">{{ $izinStats['menunggu'] }} <small class="text-muted font-weight-normal text-xs">Hr</small></div>
                                        <div class="text-xs text-warning font-weight-bold text-uppercase mt-1">Menunggu</div>
                                    </div>
                                </div>
                                <div class="col-6 col-sm-3">
                                    <div class="stat-item p-3 h-100 d-flex flex-column justify-content-center" style="background: #fef2f2;">
                                        <div class="h3 font-weight-bold text-danger mb-0">{{ $izinStats['ditolak'] }} <small class="text-muted font-weight-normal text-xs">Hr</small></div>
                                        <div class="text-xs text-danger font-weight-bold text-uppercase mt-1">Ditolak</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Detail Informasi Personal --}}
            <div class="card info-card">
                <div class="card-header bg-white p-4 border-0 pb-0">
                    <h5 class="card-title-modern"><i class="fas fa-address-card text-warning mr-3"></i> Detail Informasi Pegawai</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="d-flex align-items-center p-3 rounded" style="background: #f8fafc; border: 1px solid #f1f5f9;">
                                <div class="icon-box icon-box-primary">
                                    <i class="fas fa-envelope fa-fw"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted small text-uppercase font-weight-bold mb-1 letter-spacing-1">Email Pegawai</h6>
                                    <span class="h6 mb-0 font-weight-bold text-dark">{{ $pegawai->user->email ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="d-flex align-items-center p-3 rounded" style="background: #f8fafc; border: 1px solid #f1f5f9;">
                                <div class="icon-box icon-box-success">
                                    <i class="fas fa-phone-alt fa-fw"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted small text-uppercase font-weight-bold mb-1 letter-spacing-1">Nomor Telepon/HP</h6>
                                    <span class="h6 mb-0 font-weight-bold text-dark">{{ $pegawai->user->phone ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4 mb-md-0">
                            <div class="d-flex align-items-center p-3 rounded" style="background: #f8fafc; border: 1px solid #f1f5f9;">
                                <div class="icon-box icon-box-info">
                                    <i class="fas fa-calendar-check fa-fw"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted small text-uppercase font-weight-bold mb-1 letter-spacing-1">Mulai Tugas (TMT)</h6>
                                    <span class="h6 mb-0 font-weight-bold text-dark">{{ $pegawai->tanggal_masuk ? $pegawai->tanggal_masuk->format('d F Y') : '-' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4 mb-md-0">
                            <div class="d-flex align-items-center p-3 rounded" style="background: #f8fafc; border: 1px solid #f1f5f9;">
                                <div class="icon-box icon-box-warning">
                                    <i class="fas fa-hourglass-half fa-fw"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted small text-uppercase font-weight-bold mb-1 letter-spacing-1">Masa Kerja</h6>
                                    @if($pegawai->tanggal_masuk)
                                        @php $mk = \Carbon\Carbon::parse($pegawai->tanggal_masuk)->diff(now()); @endphp
                                        <span class="h6 mb-0 font-weight-bold text-dark">
                                            {{ $mk->y > 0 ? $mk->y.' Tahun ' : '' }}{{ $mk->m > 0 ? $mk->m.' Bulan ' : '' }}{{ $mk->d }} Hari
                                        </span>
                                    @else
                                        <span class="h6 mb-0 text-muted">Belum diatur</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mt-4">
                            <div class="d-flex p-3 rounded" style="background: #fef2f2; border: 1px solid #fecaca;">
                                <div class="icon-box icon-box-danger flex-shrink-0">
                                    <i class="fas fa-map-marker-alt fa-fw"></i>
                                </div>
                                <div>
                                    <h6 class="text-danger small text-uppercase font-weight-bold mb-1 letter-spacing-1">Alamat Domisili Lengkap</h6>
                                    <span class="h6 mb-0 font-weight-bold text-dark text-wrap" style="line-height: 1.6;">
                                        {{ $pegawai->alamat ?? 'Alamat belum diatur pada sistem.' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection
