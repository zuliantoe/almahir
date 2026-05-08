@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    
    {{-- Header / Tombol Kembali --}}
    <div class="mb-4">
        <a href="{{ route('pegawaimanager.index') }}" class="btn btn-secondary btn-sm rounded-pill px-3 shadow-sm btn-animate">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="row">
        {{-- Kolom Kiri: Ringkasan Profil --}}
        <div class="col-md-4 mb-4">
            <div class="card gradient-primary border-0 shadow-lg" style="border-radius: 15px;">
                <div class="card-body box-profile p-4">
                    <div class="text-center mb-4">
                        <img class="profile-user-img img-fluid img-circle elevation-2"
                             src="{{ $pegawai->user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($pegawai->nama).'&size=128' }}"
                             alt="User profile picture"
                             style="width: 130px; height: 130px; object-fit: cover; border: 4px solid rgba(255,255,255,0.5);">
                    </div>

                    <h3 class="profile-username text-center font-weight-bold mb-1 text-white">{{ $pegawai->nama }}</h3>
                    <p class="text-center mb-3">
                        <span class="badge bg-white text-primary px-3 py-2 rounded-pill shadow-sm" style="font-weight: 600;">
                            <i class="fas fa-shield-alt mr-1"></i> {{ $pegawai->user->primary_role ?? 'Pegawai' }}
                        </span>
                    </p>

                    <ul class="list-group list-group-unbordered mb-4" style="background: transparent;">
                        <li class="list-group-item d-flex justify-content-between align-items-center" style="background: transparent; border-color: rgba(255,255,255,0.2); color: white;">
                            <b>Tipe Pegawai</b> <span class="badge badge-light px-2 py-1 text-dark">{{ $pegawai->typePegawai->nama_type ?? '-' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center" style="background: transparent; border-color: rgba(255,255,255,0.2); color: white;">
                            <b>Status Akun</b> 
                            @php $status = $pegawai->user->account_status ?? 'inactive'; @endphp
                            @if($status === 'active')
                                <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Aktif</span>
                            @else
                                <span class="badge badge-danger px-2 py-1"><i class="fas fa-times-circle mr-1"></i> Nonaktif</span>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center" style="background: transparent; border-color: rgba(255,255,255,0.2); color: white;">
                            <b>Terdaftar Sejak</b> <span>{{ $pegawai->created_at->format('d/m/Y') }}</span>
                        </li>
                    </ul>

                    <div class="d-flex justify-content-center mt-4">
                        <a href="{{ route('pegawaimanager.edit', $pegawai->id) }}" class="btn btn-light btn-block btn-animate text-primary font-weight-bold rounded-pill shadow-sm">
                            <i class="fas fa-edit mr-1"></i> Edit Profil
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Detail Informasi --}}
        <div class="col-md-8">
            
            {{-- Statistik Absensi --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header bg-white p-3 border-bottom">
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="fas fa-chart-pie text-success mr-2"></i> Statistik Kehadiran (Bulan Ini)</h5>
                </div>
                <div class="card-body p-4 bg-light">
                    <div class="row text-center">
                        <div class="col-md-4 mb-3">
                            <div class="p-3 bg-white rounded border-bottom border-success shadow-sm hover-elevate h-100">
                                <h2 class="font-weight-bold text-success mb-1">{{ $absensiStats['hadir'] ?? 0 }}</h2>
                                <span class="text-muted small text-uppercase font-weight-bold"><i class="fas fa-check-circle mr-1"></i> Total Hadir</span>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="p-3 bg-white rounded border-bottom border-warning shadow-sm hover-elevate h-100">
                                <h2 class="font-weight-bold text-warning mb-1">{{ $absensiStats['terlambat'] ?? 0 }}</h2>
                                <span class="text-muted small text-uppercase font-weight-bold"><i class="fas fa-clock mr-1"></i> Terlambat</span>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="p-3 bg-white rounded border-bottom border-info shadow-sm hover-elevate h-100">
                                <h2 class="font-weight-bold text-info mb-1">{{ $absensiStats['izin'] ?? 0 }}</h2>
                                <span class="text-muted small text-uppercase font-weight-bold"><i class="fas fa-file-invoice mr-1"></i> Total Perizinan</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Statistik Izin & Cuti Tahunan --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="fas fa-file-alt text-primary mr-2"></i> Rekap Izin & Cuti <span class="text-primary">({{ now()->year }})</span></h5>
                    <a href="{{ route('perizinan.index', ['pegawai_id' => $pegawai->id]) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 btn-animate">
                        <i class="fas fa-external-link-alt mr-1"></i> Lihat Detail
                    </a>
                </div>
                <div class="card-body p-4 bg-light">
                    <div class="row text-center mb-3">
                        <div class="col-md-5">
                            <div class="p-3 gradient-primary text-white rounded shadow-sm hover-elevate h-100 d-flex flex-column justify-content-center">
                                <div class="small text-uppercase font-weight-bold opacity-8 mb-1">Sisa Jatah Cuti/Izin</div>
                                <h2 class="font-weight-bold mb-0">{{ $izinStats['sisa_cuti'] }} <span class="h6 mb-0">Hari</span></h2>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="row g-2">
                                <div class="col-3">
                                    <div class="p-2 bg-white rounded shadow-sm border-top border-primary h-100">
                                        <div class="h5 font-weight-bold text-primary mb-0">{{ $izinStats['total'] }} <small>Hr</small></div>
                                        <div class="text-xs text-muted">Total</div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="p-2 bg-white rounded shadow-sm border-top border-success h-100">
                                        <div class="h5 font-weight-bold text-success mb-0">{{ $izinStats['disetujui'] }} <small>Hr</small></div>
                                        <div class="text-xs text-muted">Setuju</div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="p-2 bg-white rounded shadow-sm border-top border-warning h-100">
                                        <div class="h5 font-weight-bold text-warning mb-0">{{ $izinStats['menunggu'] }} <small>Hr</small></div>
                                        <div class="text-xs text-muted">Tunggu</div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="p-2 bg-white rounded shadow-sm border-top border-danger h-100">
                                        <div class="h5 font-weight-bold text-danger mb-0">{{ $izinStats['ditolak'] }} <small>Hr</small></div>
                                        <div class="text-xs text-muted">Tolak</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header bg-white p-3 border-bottom">
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="fas fa-info-circle text-primary mr-2"></i> Detail Informasi Pegawai</h5>
                </div>
                <div class="card-body p-4 bg-light">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="p-3 bg-white rounded border-left border-primary shadow-sm h-100 hover-elevate">
                                <h6 class="text-muted small text-uppercase font-weight-bold mb-2">Kontak Email</h6>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary-light rounded-circle p-2 mr-3 text-primary" style="background: #e3f2fd;">
                                        <i class="fas fa-envelope fa-fw"></i>
                                    </div>
                                    <span class="h6 mb-0">{{ $pegawai->email ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="p-3 bg-white rounded border-left border-success shadow-sm h-100 hover-elevate">
                                <h6 class="text-muted small text-uppercase font-weight-bold mb-2">Nomor Telepon/HP</h6>
                                <div class="d-flex align-items-center">
                                    <div class="bg-success-light rounded-circle p-2 mr-3 text-success" style="background: #d1e7dd;">
                                        <i class="fas fa-phone-alt fa-fw"></i>
                                    </div>
                                    <span class="h6 mb-0">{{ $pegawai->no_hp ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="p-3 bg-white rounded border-left border-info shadow-sm h-100 hover-elevate">
                                <h6 class="text-muted small text-uppercase font-weight-bold mb-2">Tanggal Mulai Tugas (TMT)</h6>
                                <div class="d-flex align-items-center">
                                    <div class="bg-info-light rounded-circle p-2 mr-3 text-info" style="background: #cff4fc;">
                                        <i class="fas fa-calendar-check fa-fw"></i>
                                    </div>
                                    <span class="h6 mb-0">{{ $pegawai->tanggal_masuk ? $pegawai->tanggal_masuk->format('d F Y') : '-' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="p-3 bg-white rounded border-left border-warning shadow-sm h-100 hover-elevate">
                                <h6 class="text-muted small text-uppercase font-weight-bold mb-2">Masa Kerja</h6>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle p-2 mr-3 text-warning" style="background: #fff3cd;">
                                        <i class="fas fa-hourglass-half fa-fw"></i>
                                    </div>
                                    @if($pegawai->tanggal_masuk)
                                        @php $mk = \Carbon\Carbon::parse($pegawai->tanggal_masuk)->diff(now()); @endphp
                                        <div>
                                            <span class="h6 mb-0 font-weight-bold text-dark">
                                                {{ $mk->y > 0 ? $mk->y.' Tahun ' : '' }}{{ $mk->m > 0 ? $mk->m.' Bulan ' : '' }}{{ $mk->d }} Hari
                                            </span>
                                            <small class="d-block text-muted">sejak {{ $pegawai->tanggal_masuk->format('d M Y') }}</small>
                                        </div>
                                    @else
                                        <span class="h6 mb-0 text-muted">Belum diatur</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mb-4">
                            <div class="p-3 bg-white rounded border-left border-danger shadow-sm h-100 hover-elevate">
                                <h6 class="text-muted small text-uppercase font-weight-bold mb-2">Alamat Domisili</h6>
                                <div class="d-flex">
                                    <div class="bg-danger-light rounded-circle p-2 mr-3 text-danger" style="background: #f8d7da;">
                                        <i class="fas fa-map-marker-alt fa-fw"></i>
                                    </div>
                                    <span class="h6 mb-0 mt-1 text-wrap" style="line-height: 1.6;">
                                        {{ $pegawai->alamat ?? 'Alamat belum diatur.' }}
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
